import { prisma } from '../config/database';
import { env } from '../config/env';
import { orderService } from './order.service';
import { logger } from '../utils/logger';

/**
 * Servicio de polling - Ahora solo maneja limpieza periódica
 * La lectura de órdenes de MaxPoint la realiza el servicio sync (.NET)
 */
export class PollingService {
  private intervalId: NodeJS.Timeout | null = null;
  private isRunning: boolean = false;

  /**
   * Verifica si el modo Polling está habilitado (vs modo API)
   */
  async isPollingModeEnabled(): Promise<boolean> {
    const config = await prisma.generalConfig.findUnique({
      where: { id: 'general' },
    });
    return config?.ticketMode !== 'API'; // Si no es API, es POLLING
  }

  /**
   * Obtiene el modo de tickets actual
   */
  async getTicketMode(): Promise<string> {
    const config = await prisma.generalConfig.findUnique({
      where: { id: 'general' },
    });
    return config?.ticketMode || 'POLLING';
  }

  /**
   * Inicia el servicio de limpieza periódica
   * NOTA: La lectura de MaxPoint ya no se realiza aquí,
   * es manejada por el servicio sync (.NET)
   */
  async start(): Promise<void> {
    if (this.isRunning) {
      logger.warn('[POLLING] Already running');
      return;
    }

    this.isRunning = true;
    logger.info(`[POLLING] Cleanup service started with interval ${env.POLLING_INTERVAL}ms`);

    // Ejecutar limpieza inmediatamente
    this.cleanup();

    // Configurar intervalo para limpieza periódica
    this.intervalId = setInterval(() => {
      this.cleanup();
    }, env.POLLING_INTERVAL * 10); // Limpieza cada 10 intervalos
  }

  /**
   * Detiene el servicio
   */
  stop(): void {
    if (this.intervalId) {
      clearInterval(this.intervalId);
      this.intervalId = null;
    }
    this.isRunning = false;
    logger.info('[POLLING] Stopped');
  }

  /**
   * Ejecuta tareas de limpieza
   */
  private async cleanup(): Promise<void> {
    try {
      // Limpiar órdenes antiguas
      await orderService.cleanupOldOrders(env.ORDER_LIFETIME_HOURS * 6);
    } catch (error) {
      logger.error('[POLLING] Cleanup error', { error });
    }
  }

  /**
   * Fuerza un ciclo de limpieza
   */
  async forcePoll(): Promise<void> {
    logger.info('[POLLING] Force cleanup triggered');
    await this.cleanup();
  }

  /**
   * Estado del servicio
   */
  async getStatus(): Promise<{ running: boolean; interval: number; ticketMode: string }> {
    const ticketMode = await this.getTicketMode();
    return {
      running: this.isRunning,
      interval: env.POLLING_INTERVAL,
      ticketMode,
    };
  }
}

// Singleton
export const pollingService = new PollingService();
