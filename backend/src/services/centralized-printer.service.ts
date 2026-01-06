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
  private retries: number = 3;
  private timeout: number = 10000; // 10 segundos

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
   * 1. Primero hace health check a ambas URLs (sin plantilla para no imprimir)
   * 2. Envía la impresión solo al servicio que responda
   * 3. Si ambos responden, usa el principal
   * 4. Si ninguno responde, intenta con el principal
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

    // Determinar qué URL usar basándose en health check
    const targetUrl = await this.determineTargetUrl(
      centralConfig.url,
      centralConfig.urlBackup,
      order.identifier
    );

    // Intentar imprimir con la URL seleccionada
    const success = await this.tryPrintWithUrl(
      targetUrl.url,
      payload,
      order,
      printerName,
      targetUrl.type
    );

    if (success) {
      return true;
    }

    // Si falló y hay otra URL disponible, intentar con la alternativa
    if (targetUrl.alternativeUrl) {
      printerLogger.info(
        `${targetUrl.type} URL failed for order ${order.identifier}, trying alternative...`
      );

      const alternativeSuccess = await this.tryPrintWithUrl(
        targetUrl.alternativeUrl,
        payload,
        order,
        printerName,
        targetUrl.type === 'PRIMARY' ? 'BACKUP' : 'PRIMARY'
      );

      if (alternativeSuccess) {
        return true;
      }
    }

    printerLogger.error(
      `Failed to print order ${order.identifier} via centralized service (all attempts failed)`
    );
    return false;
  }

  /**
   * Determina qué URL usar para imprimir basándose en health check
   * Hace health check en paralelo y usa el primero en responder
   */
  private async determineTargetUrl(
    primaryUrl: string,
    backupUrl: string,
    orderIdentifier: string
  ): Promise<{ url: string; type: 'PRIMARY' | 'BACKUP'; alternativeUrl?: string }> {
    const HEALTH_TIMEOUT = 2000; // 2 segundos para health check

    // Si no hay backup, usar principal directamente
    if (!backupUrl) {
      printerLogger.info(`[${orderIdentifier}] No backup URL, using PRIMARY`);
      return { url: primaryUrl, type: 'PRIMARY' };
    }

    printerLogger.info(`[${orderIdentifier}] Checking service availability...`);

    // Crear promesas de health check para ambas URLs
    // Enviamos payload vacío (sin plantilla) para verificar disponibilidad sin imprimir
    const healthPayload = {};

    const primaryHealth = this.healthCheck(primaryUrl, healthPayload, HEALTH_TIMEOUT)
      .then(() => ({ url: primaryUrl, type: 'PRIMARY' as const, available: true, time: Date.now() }))
      .catch(() => ({ url: primaryUrl, type: 'PRIMARY' as const, available: false, time: Date.now() }));

    const backupHealth = this.healthCheck(backupUrl, healthPayload, HEALTH_TIMEOUT)
      .then(() => ({ url: backupUrl, type: 'BACKUP' as const, available: true, time: Date.now() }))
      .catch(() => ({ url: backupUrl, type: 'BACKUP' as const, available: false, time: Date.now() }));

    // Usar Promise.race para obtener el primero en responder
    // Pero también necesitamos saber si ambos están disponibles
    const raceResult = await Promise.race([
      primaryHealth.then(r => ({ ...r, first: true })),
      backupHealth.then(r => ({ ...r, first: true }))
    ]);

    // Esperar un poco más para ver si el otro también responde
    const results = await Promise.all([primaryHealth, backupHealth]);
    const primaryResult = results.find(r => r.type === 'PRIMARY')!;
    const backupResult = results.find(r => r.type === 'BACKUP')!;

    printerLogger.info(
      `[${orderIdentifier}] Health check results - PRIMARY: ${primaryResult.available}, BACKUP: ${backupResult.available}`
    );

    // Lógica de selección:
    // 1. Si ambos disponibles, usar PRIMARY
    // 2. Si solo uno disponible, usar ese
    // 3. Si ninguno disponible, usar PRIMARY (intentar de todas formas)
    // 4. Si respuestas en destiempo, usar el primero en responder

    if (primaryResult.available && backupResult.available) {
      // Ambos disponibles - usar PRIMARY
      printerLogger.info(`[${orderIdentifier}] Both available, using PRIMARY`);
      return { url: primaryUrl, type: 'PRIMARY', alternativeUrl: backupUrl };
    }

    if (primaryResult.available && !backupResult.available) {
      // Solo PRIMARY disponible
      printerLogger.info(`[${orderIdentifier}] Only PRIMARY available`);
      return { url: primaryUrl, type: 'PRIMARY' };
    }

    if (!primaryResult.available && backupResult.available) {
      // Solo BACKUP disponible
      printerLogger.info(`[${orderIdentifier}] Only BACKUP available`);
      return { url: backupUrl, type: 'BACKUP' };
    }

    // Ninguno disponible - usar el primero en responder (aunque con error) o PRIMARY por defecto
    if (raceResult.type === 'BACKUP') {
      printerLogger.info(`[${orderIdentifier}] None available, BACKUP responded first, trying BACKUP`);
      return { url: backupUrl, type: 'BACKUP', alternativeUrl: primaryUrl };
    }

    printerLogger.info(`[${orderIdentifier}] None available, defaulting to PRIMARY`);
    return { url: primaryUrl, type: 'PRIMARY', alternativeUrl: backupUrl };
  }

  /**
   * Health check - envía payload vacío para verificar disponibilidad sin imprimir
   * Cualquier respuesta (incluso error de validación) indica que el servicio está activo
   */
  private async healthCheck(
    url: string,
    payload: object,
    timeout: number
  ): Promise<void> {
    const response = await axios.post(url, payload, {
      timeout,
      headers: { 'Content-Type': 'application/json' },
      validateStatus: () => true, // Aceptar cualquier código de respuesta
    });

    // Si el servidor responde (incluso con error 4xx), está disponible
    // Solo 5xx o timeout significa no disponible
    if (response.status >= 500) {
      throw new Error(`Server error: ${response.status}`);
    }

    // Servicio disponible (aunque responda con error de validación)
    printerLogger.debug(`Health check OK for ${url}: ${response.status}`);
  }

  /**
   * Intenta imprimir con una URL específica con reintentos
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
