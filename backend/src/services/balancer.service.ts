import { prisma } from '../config/database';
import { redis, REDIS_KEYS } from '../config/redis';
import { Order, Queue } from '../types';
import { balancerLogger } from '../utils/logger';
import { screenService } from './screen.service';
import { mirrorKDSService, MirrorOrder } from './mirror-kds.service';

interface BalanceResult {
  screenId: string;
  orders: Order[];
}

/**
 * Servicio de balanceo de órdenes entre pantallas
 */
export class BalancerService {
  /**
   * Distribuye órdenes entre pantallas activas de una cola
   */
  async distributeOrders(
    orders: Order[],
    queueId: string
  ): Promise<BalanceResult[]> {
    // Obtener configuración de la cola
    const queue = await prisma.queue.findUnique({
      where: { id: queueId },
      include: {
        filters: { where: { active: true } },
      },
    });

    if (!queue) {
      balancerLogger.error(`Queue not found: ${queueId}`);
      return [];
    }

    // Filtrar órdenes según filtros de la cola
    const filteredOrders = this.filterOrders(orders, queue.filters);

    if (filteredOrders.length === 0) {
      return [];
    }

    // Obtener pantallas activas
    const activeScreenIds = await screenService.getActiveScreensForQueue(queueId);

    balancerLogger.info(
      `Queue ${queue.name}: ${activeScreenIds.length} active screens`
    );

    if (activeScreenIds.length === 0) {
      balancerLogger.warn(`No active screens for queue ${queue.name}`);
      return [];
    }

    // Distribuir según estrategia
    if (queue.distribution === 'DISTRIBUTED') {
      // Si solo hay 1 pantalla activa, no tiene sentido balancear
      // Todas las órdenes van a esa única pantalla
      if (activeScreenIds.length === 1) {
        balancerLogger.info(
          `Queue ${queue.name}: Solo 1 pantalla activa, balanceo desactivado temporalmente`
        );
        return [
          {
            screenId: activeScreenIds[0],
            orders: filteredOrders,
          },
        ];
      }
      // Con 2+ pantallas activas, balancear con Round-Robin
      balancerLogger.info(
        `Queue ${queue.name}: ${activeScreenIds.length} pantallas activas, balanceo activado`
      );
      return this.distributeRoundRobin(filteredOrders, activeScreenIds, queueId);
    } else {
      // SINGLE: todas las órdenes a la primera pantalla activa
      return [
        {
          screenId: activeScreenIds[0],
          orders: filteredOrders,
        },
      ];
    }
  }

  /**
   * Distribución Round-Robin entre pantallas
   */
  private async distributeRoundRobin(
    orders: Order[],
    screenIds: string[],
    queueId: string
  ): Promise<BalanceResult[]> {
    const result = new Map<string, Order[]>();

    // Inicializar resultado
    screenIds.forEach((id) => result.set(id, []));

    // Obtener índice actual desde Redis
    const indexKey = REDIS_KEYS.balancerIndex(queueId);
    let currentIndex = parseInt((await redis.get(indexKey)) || '0');

    // Distribuir órdenes
    for (const order of orders) {
      const screenId = screenIds[currentIndex % screenIds.length];
      result.get(screenId)!.push(order);
      currentIndex++;
    }

    // Guardar índice para siguiente ciclo
    await redis.set(indexKey, currentIndex.toString());

    // Log de distribución
    const distribution = screenIds
      .map((id) => `${id.slice(-4)}=${result.get(id)!.length}`)
      .join(', ');
    balancerLogger.info(`Distributed ${orders.length} orders: ${distribution}`);

    return screenIds.map((screenId) => ({
      screenId,
      orders: result.get(screenId)!,
    }));
  }

  /**
   * Filtra órdenes y sus items según los filtros de la cola
   *
   * Lógica de filtrado:
   * - suppress: true → OCULTAR items que coinciden con el patrón (ej: LINEAS oculta S.)
   * - suppress: false → MOSTRAR SOLO items que coinciden con el patrón (ej: SANDUCHE muestra S.)
   *
   * Una orden se incluye si tiene al menos un item después del filtrado.
   * La misma orden puede aparecer en múltiples colas con diferentes items visibles.
   */
  private filterOrders(
    orders: Order[],
    filters: Array<{ pattern: string; suppress: boolean }>
  ): Order[] {
    if (filters.length === 0) {
      return orders;
    }

    const filteredOrders: Order[] = [];

    for (const order of orders) {
      // Filtrar items según los filtros de la cola
      const filteredItems = this.filterItemsByQueueFilters(order.items, filters);

      // Solo incluir la orden si tiene al menos un item después del filtrado
      if (filteredItems.length > 0) {
        filteredOrders.push({
          ...order,
          items: filteredItems,
        });
      }
    }

    return filteredOrders;
  }

  /**
   * Filtra items según los filtros de la cola
   *
   * @param items - Items de la orden
   * @param filters - Filtros de la cola
   * @returns Items filtrados
   */
  private filterItemsByQueueFilters(
    items: Order['items'],
    filters: Array<{ pattern: string; suppress: boolean }>
  ): Order['items'] {
    // Separar filtros por tipo
    const suppressFilters = filters.filter(f => f.suppress);
    const showOnlyFilters = filters.filter(f => !f.suppress);

    return items.filter((item) => {
      const itemNameLower = item.name.toLowerCase();

      // Si hay filtros de "mostrar solo" (suppress: false), el item DEBE coincidir con al menos uno
      if (showOnlyFilters.length > 0) {
        const matchesShowFilter = showOnlyFilters.some(filter =>
          itemNameLower.includes(filter.pattern.toLowerCase())
        );
        if (!matchesShowFilter) {
          return false; // No coincide con ningún filtro de "mostrar solo"
        }
      }

      // Si hay filtros de "ocultar" (suppress: true), el item NO debe coincidir con ninguno
      if (suppressFilters.length > 0) {
        const matchesSuppressFilter = suppressFilters.some(filter =>
          itemNameLower.includes(filter.pattern.toLowerCase())
        );
        if (matchesSuppressFilter) {
          return false; // Coincide con un filtro de "ocultar"
        }
      }

      return true;
    });
  }

  /**
   * Asigna una orden a una pantalla específica
   */
  async assignOrderToScreen(orderId: string, screenId: string): Promise<void> {
    await prisma.order.update({
      where: { id: orderId },
      data: { screenId },
    });

    // Guardar en Redis para acceso rápido
    await redis.sadd(REDIS_KEYS.screenOrders(screenId), orderId);

    balancerLogger.debug(`Order ${orderId} assigned to screen ${screenId}`);
  }

  /**
   * Obtiene órdenes para una pantalla, aplicando filtros de la cola
   * Si el Mirror está conectado, obtiene órdenes del KDS2 remoto
   *
   * Lógica:
   * - Para colas SINGLE: Obtiene TODAS las órdenes pendientes y filtra items
   * - Para colas DISTRIBUTED: Obtiene órdenes asignadas a la pantalla y filtra items
   *
   * Esto permite que una misma orden aparezca en múltiples colas con items filtrados.
   */
  async getOrdersForScreen(screenId: string): Promise<Order[]> {
    // Si el Mirror está conectado, obtener órdenes del KDS2 remoto
    if (mirrorKDSService.getConnectionStatus()) {
      return this.getOrdersFromMirror(screenId);
    }

    // Obtener información de la pantalla y su cola
    const screen = await prisma.screen.findUnique({
      where: { id: screenId },
      include: {
        queue: {
          include: {
            filters: { where: { active: true } },
          },
        },
      },
    });

    if (!screen || !screen.queue) {
      return [];
    }

    const { queue } = screen;
    let orders;

    // Si la cola es SINGLE, obtener TODAS las órdenes pendientes
    // Esto permite que la pantalla de SANDUCHE vea órdenes que están asignadas a otras pantallas
    if (queue.distribution === 'SINGLE') {
      orders = await prisma.order.findMany({
        where: {
          status: { in: ['PENDING', 'IN_PROGRESS'] },
        },
        include: {
          items: true,
        },
        orderBy: {
          createdAt: 'asc',
        },
      });
    } else {
      // DISTRIBUTED: obtener solo las órdenes asignadas a esta pantalla
      orders = await prisma.order.findMany({
        where: {
          screenId,
          status: { in: ['PENDING', 'IN_PROGRESS'] },
        },
        include: {
          items: true,
        },
        orderBy: {
          createdAt: 'asc',
        },
      });
    }

    // Convertir a formato Order
    const mappedOrders: Order[] = orders.map((order) => ({
      id: order.id,
      externalId: order.externalId,
      screenId: order.screenId || undefined,
      channel: order.channel,
      customerName: order.customerName || undefined,
      identifier: order.identifier,
      status: order.status as Order['status'],
      createdAt: order.createdAt,
      finishedAt: order.finishedAt || undefined,
      items: order.items.map((item) => ({
        id: item.id,
        name: item.name,
        quantity: item.quantity,
        notes: item.notes || undefined,
        modifier: item.modifier || undefined,
        comments: item.comments || undefined,
      })),
      // Campos opcionales para impresión/visualización
      comments: order.comments || undefined,
      templateHTML: order.templateHTML || undefined,
      valuesHTML: order.valuesHTML || undefined,
      statusPos: order.statusPos || undefined,
    }));

    // Aplicar filtros de la cola a nivel de items
    if (queue.filters.length > 0) {
      return this.filterOrders(mappedOrders, queue.filters);
    }

    return mappedOrders;
  }

  /**
   * Obtiene órdenes del Mirror (KDS2 remoto) para una pantalla local
   */
  private async getOrdersFromMirror(screenId: string): Promise<Order[]> {
    try {
      // Obtener info de la pantalla local para mapear con el Mirror
      const screen = await prisma.screen.findUnique({
        where: { id: screenId },
        select: { name: true, number: true },
      });

      if (!screen) return [];

      // Mapeo: KDS1 → Pantalla1, KDS2 → Pantalla2, etc.
      // O usar el número directamente
      const mirrorScreenName = `Pantalla${screen.number}`;

      // Obtener órdenes del Mirror filtrando por la pantalla remota
      const mirrorOrders = await mirrorKDSService.getOrdersOnScreen(mirrorScreenName);

      // Convertir MirrorOrder a Order
      return mirrorOrders.map((mo: MirrorOrder) => ({
        id: mo.id,
        externalId: mo.externalId,
        screenId: screenId,
        channel: mo.channel,
        channelType: mo.channelType,
        customerName: mo.customerName,
        identifier: mo.identifier,
        status: mo.status as Order['status'],
        createdAt: mo.createdAt,
        finishedAt: undefined,
        items: mo.items.map((item) => ({
          id: item.id,
          name: item.name,
          quantity: item.quantity,
          notes: item.notes,
          modifier: item.subitems?.map(s => `${s.quantity}x ${s.name}`).join(', '),
        })),
      }));
    } catch (error) {
      balancerLogger.error('Error getting orders from Mirror:', { error });
      return [];
    }
  }

  /**
   * Redistribuye órdenes cuando una pantalla se reactiva
   * Balancea equitativamente las órdenes entre todas las pantallas activas
   */
  async handleScreenReactivation(screenId: string): Promise<string[]> {
    const screen = await prisma.screen.findUnique({
      where: { id: screenId },
      select: { queueId: true, name: true },
    });

    if (!screen) return [];

    balancerLogger.info(
      `Screen ${screen.name} reactivated - redistributing orders in queue`
    );

    // Obtener todas las pantallas activas de la cola
    let activeScreenIds = await screenService.getActiveScreensForQueue(screen.queueId);

    // Asegurarse de que la pantalla que se está encendiendo esté incluida
    // (puede que aún no tenga heartbeat activo)
    if (!activeScreenIds.includes(screenId)) {
      activeScreenIds.push(screenId);
      balancerLogger.info(`Added reactivating screen ${screen.name} to active list`);
    }

    // Si solo hay una pantalla (la que se está encendiendo), igual redistribuir
    // para que las órdenes que estaban en pantallas apagadas vuelvan a aparecer
    if (activeScreenIds.length === 0) {
      activeScreenIds = [screenId];
    }

    // Obtener TODAS las órdenes pendientes de la cola (de todas las pantallas activas)
    const allPendingOrders = await prisma.order.findMany({
      where: {
        screen: { queueId: screen.queueId },
        status: { in: ['PENDING', 'IN_PROGRESS'] },
      },
      select: { id: true, screenId: true },
      orderBy: { createdAt: 'asc' },
    });

    if (allPendingOrders.length === 0) {
      balancerLogger.info(`No pending orders to redistribute`);
      return [];
    }

    balancerLogger.info(
      `Redistributing ${allPendingOrders.length} orders among ${activeScreenIds.length} active screens`
    );

    // Redistribuir equitativamente usando round-robin
    const affectedScreenIds: Set<string> = new Set();
    let index = 0;

    for (const order of allPendingOrders) {
      const targetScreenId = activeScreenIds[index % activeScreenIds.length];

      // Solo actualizar si cambia de pantalla
      if (order.screenId !== targetScreenId) {
        await prisma.order.update({
          where: { id: order.id },
          data: { screenId: targetScreenId },
        });

        // Actualizar Redis
        if (order.screenId) {
          await redis.srem(REDIS_KEYS.screenOrders(order.screenId), order.id);
        }
        await redis.sadd(REDIS_KEYS.screenOrders(targetScreenId), order.id);

        affectedScreenIds.add(targetScreenId);
        if (order.screenId) {
          affectedScreenIds.add(order.screenId);
        }
      }

      index++;
    }

    // Calcular distribución final para log
    const distribution: Record<string, number> = {};
    for (const screenId of activeScreenIds) {
      const count = await prisma.order.count({
        where: { screenId, status: { in: ['PENDING', 'IN_PROGRESS'] } },
      });
      distribution[screenId.slice(-4)] = count;
    }

    balancerLogger.info(
      `Redistribution complete. Distribution: ${JSON.stringify(distribution)}`
    );

    return Array.from(affectedScreenIds);
  }

  /**
   * Maneja cuando una pantalla entra en standby
   * Redistribuye las órdenes pendientes a pantallas activas
   */
  async handleScreenStandby(screenId: string): Promise<string[]> {
    const screen = await prisma.screen.findUnique({
      where: { id: screenId },
      select: { queueId: true, name: true },
    });

    if (!screen) return [];

    balancerLogger.info(
      `Screen ${screen.name} entered standby - redistributing orders`
    );

    // Obtener pantallas activas de la misma cola (excluyendo la que se apaga)
    const activeScreenIds = await screenService.getActiveScreensForQueue(screen.queueId);
    const remainingScreens = activeScreenIds.filter(id => id !== screenId);

    if (remainingScreens.length === 0) {
      balancerLogger.warn(
        `No active screens to redistribute orders from ${screen.name}`
      );
      return [];
    }

    // Obtener órdenes pendientes de la pantalla que se apaga
    const pendingOrders = await prisma.order.findMany({
      where: {
        screenId,
        status: { in: ['PENDING', 'IN_PROGRESS'] },
      },
      select: { id: true },
    });

    if (pendingOrders.length === 0) {
      balancerLogger.info(`No pending orders to redistribute from ${screen.name}`);
      return [];
    }

    balancerLogger.info(
      `Redistributing ${pendingOrders.length} orders from ${screen.name} to ${remainingScreens.length} active screens`
    );

    // Redistribuir órdenes usando round-robin entre pantallas activas
    const affectedScreenIds: Set<string> = new Set();
    let index = 0;

    for (const order of pendingOrders) {
      const targetScreenId = remainingScreens[index % remainingScreens.length];

      await prisma.order.update({
        where: { id: order.id },
        data: { screenId: targetScreenId },
      });

      // Actualizar Redis
      await redis.srem(REDIS_KEYS.screenOrders(screenId), order.id);
      await redis.sadd(REDIS_KEYS.screenOrders(targetScreenId), order.id);

      affectedScreenIds.add(targetScreenId);
      index++;
    }

    balancerLogger.info(
      `Redistributed ${pendingOrders.length} orders to screens: ${Array.from(affectedScreenIds).map(id => id.slice(-4)).join(', ')}`
    );

    return Array.from(affectedScreenIds);
  }

  /**
   * Obtiene estadísticas de balanceo para una cola
   */
  async getBalanceStats(
    queueId: string
  ): Promise<{
    queueName: string;
    totalOrders: number;
    activeScreens: number;
    ordersPerScreen: Record<string, number>;
  }> {
    const queue = await prisma.queue.findUnique({
      where: { id: queueId },
      include: {
        screens: {
          select: { id: true, name: true },
        },
      },
    });

    if (!queue) {
      throw new Error('Queue not found');
    }

    const activeScreenIds = await screenService.getActiveScreensForQueue(
      queueId
    );

    const ordersPerScreen: Record<string, number> = {};
    let totalOrders = 0;

    for (const screen of queue.screens) {
      const count = await prisma.order.count({
        where: {
          screenId: screen.id,
          status: { in: ['PENDING', 'IN_PROGRESS'] },
        },
      });
      ordersPerScreen[screen.name] = count;
      totalOrders += count;
    }

    return {
      queueName: queue.name,
      totalOrders,
      activeScreens: activeScreenIds.length,
      ordersPerScreen,
    };
  }

  /**
   * Reinicia el índice de balanceo para una cola
   */
  async resetBalanceIndex(queueId: string): Promise<void> {
    await redis.del(REDIS_KEYS.balancerIndex(queueId));
    balancerLogger.info(`Balance index reset for queue ${queueId}`);
  }
}

// Singleton
export const balancerService = new BalancerService();
