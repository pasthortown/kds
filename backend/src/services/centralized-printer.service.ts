import axios from 'axios';
import { prisma } from '../config/database';
import { Order, OrderItem } from '../types';
import { printerLogger } from '../utils/logger';

/**
 * Interface para el payload de impresión centralizada
 * Compatible con el servicio de impresión .NET
 */
export interface CentralizedPrintPayload {
  idImpresora: string;
  aplicaBalanceo: string;
  impresorasBalancear: string;
  idPlantilla: string;
  idMarca: string;
  data: {
    num_ficha: string;
    pickup_cfac_id: string;
    qrPedido: string;
    empresaDelivery: string;
    transaccion: string;
    fecha: string;
    cajero: string;
    observacion: string;
    numeroCuenta: string;
    fecha_ingresa: string;
    fecha_hasta: string;
  };
  registros: RegistroDetalle[];
  footer: string[];
}

interface RegistroDetalle {
  registrosDetalle: Array<{
    producto: string;
    cantidad: string;
  }>;
}

/**
 * Servicio de impresión centralizada
 * Envía las órdenes a un servicio HTTP externo para impresión
 */
export class CentralizedPrinterService {
  private retries: number = 2; // Reducido para failover más rápido
  private timeout: number = 8000; // 8 segundos

  /**
   * Verifica si el modo centralizado está habilitado
   */
  async isCentralizedModeEnabled(): Promise<boolean> {
    const config = await prisma.generalConfig.findUnique({
      where: { id: 'general' },
    });
    return config?.printMode === 'CENTRALIZED';
  }

  /**
   * Obtiene la configuración del servicio centralizado
   */
  async getCentralizedConfig(): Promise<{
    url: string;
    urlBackup: string;
    port: number;
    printTemplate: string;
  } | null> {
    const config = await prisma.generalConfig.findUnique({
      where: { id: 'general' },
    });

    if (!config?.centralizedPrintUrl) {
      return null;
    }

    return {
      url: config.centralizedPrintUrl,
      urlBackup: config.centralizedPrintUrlBackup || '',
      port: config.centralizedPrintPort || 5000,
      printTemplate: config.printTemplate || '',
    };
  }

  /**
   * Obtiene el nombre de la impresora configurada para una pantalla
   */
  async getPrinterNameForScreen(screenId: string): Promise<string> {
    const screen = await prisma.screen.findUnique({
      where: { id: screenId },
      select: { printerName: true, name: true },
    });

    // Usar printerName si está configurado, sino usar el nombre de la pantalla
    return screen?.printerName || screen?.name || 'default';
  }

  /**
   * Imprime una orden usando el servicio centralizado
   * Flujo con failover rápido:
   * 1. Intenta con URL principal (1 intento rápido)
   * 2. Si falla, intenta con URL backup inmediatamente
   * 3. Si backup también falla, reintenta con la que respondió mejor
   */
  async printOrder(order: Order, screenId: string): Promise<boolean> {
    const centralConfig = await this.getCentralizedConfig();
    if (!centralConfig) {
      printerLogger.error('Centralized print service not configured');
      return false;
    }

    if (!centralConfig.printTemplate) {
      printerLogger.error('Print template (formatoXML) not configured');
      return false;
    }

    const printerName = await this.getPrinterNameForScreen(screenId);
    if (!printerName) {
      printerLogger.warn(`No printer configured for screen ${screenId}`);
      return false;
    }

    const payload = this.formatOrderForCentralizedPrint(
      order,
      printerName,
      centralConfig.printTemplate
    );

    printerLogger.debug('Centralized print payload:', { payload });

    const primaryUrl = centralConfig.url;
    const backupUrl = centralConfig.urlBackup;

    // Si no hay backup configurado, usar solo la principal con reintentos
    if (!backupUrl) {
      printerLogger.info(`[${order.identifier}] No backup URL configured, using PRIMARY only`);
      return this.tryPrintWithUrl(primaryUrl, payload, order, printerName, 'PRIMARY');
    }

    // Estrategia de failover rápido: intentar PRIMARY primero (1 intento)
    printerLogger.info(`[${order.identifier}] Trying PRIMARY URL first...`);
    const primarySuccess = await this.trySinglePrint(primaryUrl, payload, order, printerName, 'PRIMARY');

    if (primarySuccess) {
      return true;
    }

    // PRIMARY falló - intentar BACKUP inmediatamente (1 intento)
    printerLogger.info(`[${order.identifier}] PRIMARY failed, trying BACKUP URL immediately...`);
    const backupSuccess = await this.trySinglePrint(backupUrl, payload, order, printerName, 'BACKUP');

    if (backupSuccess) {
      return true;
    }

    // Ambos fallaron en el primer intento - hacer reintentos con ambos
    printerLogger.info(`[${order.identifier}] Both URLs failed, retrying with delays...`);

    // Reintentar PRIMARY
    await this.delay(1000);
    const primaryRetry = await this.trySinglePrint(primaryUrl, payload, order, printerName, 'PRIMARY');
    if (primaryRetry) {
      return true;
    }

    // Reintentar BACKUP
    await this.delay(1000);
    const backupRetry = await this.trySinglePrint(backupUrl, payload, order, printerName, 'BACKUP');
    if (backupRetry) {
      return true;
    }

    printerLogger.error(
      `Failed to print order ${order.identifier} via centralized service (all attempts failed on both URLs)`
    );
    return false;
  }

  /**
   * Intenta imprimir una sola vez (sin reintentos)
   * Para failover rápido entre URLs
   */
  private async trySinglePrint(
    url: string,
    payload: CentralizedPrintPayload,
    order: Order,
    printerName: string,
    urlType: 'PRIMARY' | 'BACKUP'
  ): Promise<boolean> {
    try {
      await this.sendToCentralizedService(url, payload);

      // Registrar impresión exitosa
      await prisma.order.update({
        where: { id: order.id },
        data: { printedAt: new Date() },
      });

      printerLogger.info(
        `Order ${order.identifier} sent to centralized print service [${urlType}] (printer: ${printerName})`
      );
      return true;
    } catch (error) {
      printerLogger.warn(
        `Centralized print [${urlType}] failed for order ${order.identifier}`,
        { error: error instanceof Error ? error.message : error }
      );
      return false;
    }
  }

  /**
   * Intenta imprimir con una URL específica con reintentos
   * Se usa cuando solo hay URL principal (sin backup)
   */
  private async tryPrintWithUrl(
    url: string,
    payload: CentralizedPrintPayload,
    order: Order,
    printerName: string,
    urlType: 'PRIMARY' | 'BACKUP'
  ): Promise<boolean> {
    for (let attempt = 1; attempt <= this.retries; attempt++) {
      try {
        await this.sendToCentralizedService(url, payload);

        // Registrar impresión exitosa
        await prisma.order.update({
          where: { id: order.id },
          data: { printedAt: new Date() },
        });

        printerLogger.info(
          `Order ${order.identifier} sent to centralized print service [${urlType}] (printer: ${printerName})`
        );
        return true;
      } catch (error) {
        printerLogger.warn(
          `Centralized print [${urlType}] attempt ${attempt}/${this.retries} failed for order ${order.identifier}`,
          { error }
        );

        if (attempt === this.retries) {
          printerLogger.warn(
            `All ${this.retries} attempts failed for [${urlType}] URL`
          );
          return false;
        }

        // Esperar antes de reintentar (delay progresivo)
        await this.delay(1000 * attempt);
      }
    }

    return false;
  }

  /**
   * Envía la orden al servicio centralizado via HTTP POST
   */
  private async sendToCentralizedService(
    url: string,
    payload: CentralizedPrintPayload
  ): Promise<void> {
    try {
      // Log del payload que se envía
      printerLogger.info('=== CENTRALIZED PRINT REQUEST ===');
      printerLogger.info(`URL: ${url}`);
      printerLogger.info(`Payload: ${JSON.stringify(payload, null, 2)}`);

      const response = await axios.post(url, payload, {
        timeout: this.timeout,
        headers: {
          'Content-Type': 'application/json',
        },
      });

      // Log de la respuesta completa
      printerLogger.info('=== CENTRALIZED PRINT RESPONSE ===');
      printerLogger.info(`Status: ${response.status}`);
      printerLogger.info(`Data: ${JSON.stringify(response.data)}`);

      // Verificar error HTTP
      if (response.status >= 400) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }

      // Verificar error en la respuesta del servicio
      if (response.data?.error === true) {
        throw new Error(`Print service error: ${response.data.mensaje || 'Unknown error'}`);
      }
    } catch (error: unknown) {
      printerLogger.error('=== CENTRALIZED PRINT ERROR ===');
      if (axios.isAxiosError(error)) {
        printerLogger.error(`Axios Error: ${error.message}`);
        printerLogger.error(`Response Status: ${error.response?.status}`);
        printerLogger.error(`Response Data: ${JSON.stringify(error.response?.data)}`);
        throw new Error(
          `Centralized print service error: ${error.message} - ${JSON.stringify(error.response?.data) || ''}`
        );
      }
      printerLogger.error(`Unknown Error: ${error}`);
      throw error;
    }
  }

  /**
   * Formatea una fecha al formato dd/MM/yyyy HH:mm:ss
   */
  private formatDate(date: Date): string {
    const pad = (n: number) => n.toString().padStart(2, '0');
    const d = new Date(date);
    return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
  }

  /**
   * Construye los registros de productos para impresión
   * Formato: producto principal, modificadores con " - ", comentarios
   */
  private buildRegistros(items: OrderItem[]): RegistroDetalle[] {
    const registrosDetalle: Array<{ producto: string; cantidad: string }> = [];

    for (const item of items) {
      // 1. Producto principal
      registrosDetalle.push({
        producto: item.name,
        cantidad: item.quantity.toString(),
      });

      // 2. Modificadores (si existen) - separados por coma
      if (item.modifier) {
        const modifiers = item.modifier.split(',').map(m => m.trim());
        for (const mod of modifiers) {
          if (mod) {
            registrosDetalle.push({
              producto: ` - ${mod}`,
              cantidad: ' ',
            });
          }
        }
      }

      // 3. Comentarios del item (si existen)
      if (item.comments) {
        registrosDetalle.push({
          producto: `>> ${item.comments}`,
          cantidad: ' ',
        });
      }

      // NOTA: item.notes NO se imprime según especificación
    }

    // Retornar como array con un objeto que contiene registrosDetalle
    return [{ registrosDetalle }];
  }

  /**
   * Formatea una orden para el servicio centralizado
   */
  private formatOrderForCentralizedPrint(
    order: Order,
    printerName: string,
    printTemplate: string
  ): CentralizedPrintPayload {
    const now = new Date();
    const createdAt = new Date(order.createdAt);

    // Obtener últimos 2 dígitos del identifier para transaccion
    const transaccion = order.identifier.slice(-2);

    // Determinar empresaDelivery basado en el canal
    const empresaDelivery = this.getEmpresaDelivery(order.channel);

    return {
      idImpresora: printerName,
      aplicaBalanceo: '',
      impresorasBalancear: '',
      idPlantilla: printTemplate,  // XML completo de la plantilla
      idMarca: '',
      data: {
        num_ficha: order.identifier,
        pickup_cfac_id: order.identifier,
        qrPedido: order.identifier,
        empresaDelivery: empresaDelivery,
        transaccion: transaccion,
        fecha: this.formatDate(createdAt),
        cajero: '',
        observacion: [order.customerName, order.channel, order.comments].filter(Boolean).join(', '),
        numeroCuenta: '1',
        fecha_ingresa: this.formatDate(createdAt),
        fecha_hasta: this.formatDate(now),
      },
      registros: this.buildRegistros(order.items),
      footer: [],
    };
  }

  /**
   * Determina el código de empresa delivery basado en el canal
   */
  private getEmpresaDelivery(channel: string): string {
    const channelLower = (channel || '').toLowerCase();

    if (channelLower.includes('pedidosya') || channelLower.includes('pya')) {
      return 'pya';
    }
    if (channelLower.includes('uber')) {
      return 'uber';
    }
    if (channelLower.includes('rappi')) {
      return 'rappi';
    }
    if (channelLower.includes('didi')) {
      return 'didi';
    }
    if (channelLower.includes('delivery') || channelLower.includes('domicilio')) {
      return 'del';
    }
    if (channelLower.includes('llevar') || channelLower.includes('para llevar')) {
      return 'llv';
    }
    if (channelLower.includes('salon') || channelLower.includes('salón')) {
      return 'sal';
    }

    // Por defecto, usar primeras 3 letras del canal
    return channel ? channel.substring(0, 3).toLowerCase() : 'ped';
  }

  /**
   * Prueba la conexión con el servicio centralizado
   * @param useBackup - Si es true, prueba la URL de backup en lugar de la principal
   */
  async testConnection(useBackup: boolean = false): Promise<{ success: boolean; message: string }> {
    const config = await this.getCentralizedConfig();
    if (!config) {
      return { success: false, message: 'Servicio centralizado no configurado' };
    }

    const url = useBackup ? config.urlBackup : config.url;
    const urlType = useBackup ? 'BACKUP' : 'PRINCIPAL';

    if (!url) {
      return { success: false, message: `URL ${urlType} no configurada` };
    }

    try {
      // Hacer POST con payload mínimo para verificar que el servicio responde
      const response = await axios.post(url, {}, {
        timeout: 5000,
        headers: { 'Content-Type': 'application/json' },
        validateStatus: () => true, // Aceptar cualquier código de respuesta
      });

      // Si responde (aunque sea con error de validación), el servicio está activo
      if (response.status < 500) {
        return {
          success: true,
          message: `Servicio ${urlType} activo. Respuesta: ${response.data?.mensaje || response.status}`,
        };
      }

      return {
        success: false,
        message: `Error del servidor ${urlType}: ${response.status}`,
      };
    } catch (error: unknown) {
      const message = axios.isAxiosError(error)
        ? `Error de conexión ${urlType}: ${error.message}`
        : 'Error desconocido';

      return { success: false, message };
    }
  }

  /**
   * Delay helper
   */
  private delay(ms: number): Promise<void> {
    return new Promise((resolve) => setTimeout(resolve, ms));
  }
}

// Singleton
export const centralizedPrinterService = new CentralizedPrinterService();
