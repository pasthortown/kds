import { Request, Response } from 'express';
import { prisma } from '../config/database';
import { redis, REDIS_KEYS } from '../config/redis';
import { pollingService } from '../services/polling.service';
import { printerService } from '../services/printer.service';
import { centralizedPrinterService } from '../services/centralized-printer.service';
import { apiTicketService, ApiComanda } from '../services/api-ticket.service';
import { websocketService } from '../services/websocket.service';
import { AuthenticatedRequest } from '../types';
import { asyncHandler, AppError } from '../middlewares/error.middleware';

/**
 * GET /api/config/general
 * Obtener configuración general
 */
export const getGeneralConfig = asyncHandler(
  async (_req: Request, res: Response) => {
    let config = await prisma.generalConfig.findUnique({
      where: { id: 'general' },
    });

    // Crear configuración por defecto si no existe
    if (!config) {
      config = await prisma.generalConfig.create({
        data: { id: 'general' },
      });
    }

    res.json(config);
  }
);

/**
 * PUT /api/config/general
 * Actualizar configuración general
 */
export const updateGeneralConfig = asyncHandler(
  async (req: AuthenticatedRequest, res: Response) => {
    const data = req.body;

    // Eliminar campos que no deben actualizarse directamente
    delete data.id;
    delete data.updatedAt;

    const config = await prisma.generalConfig.upsert({
      where: { id: 'general' },
      create: { id: 'general', ...data },
      update: data,
    });

    // Invalidar cache
    await redis.del(REDIS_KEYS.generalConfig());

    // Reiniciar polling si cambió el intervalo
    if (data.pollingInterval) {
      pollingService.stop();
      pollingService.start();
    }

    res.json(config);
  }
);

/**
 * GET /api/config/mxp
 * DEPRECADO: La conexión a MaxPoint ahora la maneja el servicio sync (.NET)
 * Se mantiene para compatibilidad con el backoffice
 */
export const getMxpConfig = asyncHandler(
  async (_req: Request, res: Response) => {
    res.json({
      message: 'DEPRECADO: La conexión a MaxPoint ahora la maneja el servicio sync (.NET)',
      mxpHost: '',
      mxpPort: null,
      mxpUser: '',
      mxpPassword: '',
      mxpDatabase: '',
      pollingInterval: 2000,
      lastPollTime: null,
      lastOrderId: null,
    });
  }
);

/**
 * PUT /api/config/mxp
 * DEPRECADO: La conexión a MaxPoint ahora la maneja el servicio sync (.NET)
 */
export const updateMxpConfig = asyncHandler(
  async (_req: AuthenticatedRequest, res: Response) => {
    res.status(410).json({
      message: 'DEPRECADO: La conexión a MaxPoint ahora la maneja el servicio sync (.NET). Configure el archivo config.txt del servicio sync.',
    });
  }
);

/**
 * POST /api/config/mxp/test
 * DEPRECADO: La conexión a MaxPoint ahora la maneja el servicio sync (.NET)
 */
export const testMxpConnection = asyncHandler(
  async (_req: AuthenticatedRequest, res: Response) => {
    res.status(410).json({
      success: false,
      message: 'DEPRECADO: La conexión a MaxPoint ahora la maneja el servicio sync (.NET)',
    });
  }
);

/**
 * GET /api/config/polling
 * Obtener estado del servicio de polling
 */
export const getPollingStatus = asyncHandler(
  async (_req: Request, res: Response) => {
    const status = await pollingService.getStatus();
    res.json(status);
  }
);

/**
 * POST /api/config/polling/start
 * Iniciar servicio de polling
 */
export const startPolling = asyncHandler(
  async (_req: AuthenticatedRequest, res: Response) => {
    await pollingService.start();
    res.json({ message: 'Polling started' });
  }
);

/**
 * POST /api/config/polling/stop
 * Detener servicio de polling
 */
export const stopPolling = asyncHandler(
  async (_req: AuthenticatedRequest, res: Response) => {
    pollingService.stop();
    res.json({ message: 'Polling stopped' });
  }
);

/**
 * POST /api/config/polling/force
 * Forzar un ciclo de polling
 */
export const forcePoll = asyncHandler(
  async (_req: AuthenticatedRequest, res: Response) => {
    await pollingService.forcePoll();
    res.json({ message: 'Poll cycle completed' });
  }
);

/**
 * GET /api/config/health
 * Health check del sistema
 */
export const healthCheck = asyncHandler(
  async (_req: Request, res: Response) => {
    const checks = {
      database: false,
      redis: false,
      websocket: false,
    };

    // Check database
    try {
      await prisma.$queryRaw`SELECT 1`;
      checks.database = true;
    } catch {
      checks.database = false;
    }

    // Check Redis
    try {
      const pong = await redis.ping();
      checks.redis = pong === 'PONG';
    } catch {
      checks.redis = false;
    }

    // Check WebSocket
    try {
      const io = websocketService.getIO();
      checks.websocket = io !== null;
    } catch {
      checks.websocket = false;
    }

    const healthy = checks.database && checks.redis;

    res.status(healthy ? 200 : 503).json({
      status: healthy ? 'healthy' : 'unhealthy',
      checks,
      timestamp: new Date().toISOString(),
    });
  }
);

/**
 * GET /api/config/stats
 * Estadísticas generales del sistema
 */
export const getSystemStats = asyncHandler(
  async (_req: Request, res: Response) => {
    const [screensTotal, screensOnline, queuesTotal, ordersToday] =
      await Promise.all([
        prisma.screen.count(),
        prisma.screen.count({ where: { status: 'ONLINE' } }),
        prisma.queue.count({ where: { active: true } }),
        prisma.order.count({
          where: {
            createdAt: {
              gte: new Date(new Date().setHours(0, 0, 0, 0)),
            },
          },
        }),
      ]);

    res.json({
      screens: {
        total: screensTotal,
        online: screensOnline,
      },
      queues: queuesTotal,
      ordersToday,
      uptime: process.uptime(),
      memory: process.memoryUsage(),
    });
  }
);

// ============================================
// ENDPOINTS DE MODO DE CONFIGURACIÓN
// ============================================

/**
 * GET /api/config/modes
 * Obtener modos de configuración actuales (tickets e impresión)
 */
export const getConfigModes = asyncHandler(
  async (_req: Request, res: Response) => {
    const config = await prisma.generalConfig.findUnique({
      where: { id: 'general' },
      select: {
        ticketMode: true,
        printMode: true,
        centralizedPrintUrl: true,
        centralizedPrintPort: true,
        printTemplate: true,
        printTemplateType: true,
      },
    });

    // Obtener pantallas con su printerName para la configuración de impresoras
    const screens = await prisma.screen.findMany({
      select: {
        id: true,
        name: true,
        number: true,
        printerName: true,
      },
      orderBy: { number: 'asc' },
    });

    res.json({
      ticketMode: config?.ticketMode || 'POLLING',
      printMode: config?.printMode || 'LOCAL',
      centralizedPrintUrl: config?.centralizedPrintUrl || '',
      centralizedPrintPort: config?.centralizedPrintPort || 5000,
      printTemplate: config?.printTemplate || '',
      printTemplateType: config?.printTemplateType || 'orden_pedido',
      screenPrinters: screens.map(s => ({
        id: s.id,
        name: s.name,
        number: s.number,
        printerName: s.printerName || '',
      })),
    });
  }
);

/**
 * Sanitiza el XML de plantilla para evitar problemas de escape
 * Remueve doble escapes que pueden venir del JSON
 */
function sanitizePrintTemplate(template: string): string {
  if (!template) return template;

  let sanitized = template;

  // Remover doble escapes de comillas: \" -> "
  sanitized = sanitized.replace(/\\"/g, '"');

  // Remover escapes de barras invertidas dobles: \\\\ -> \\
  sanitized = sanitized.replace(/\\\\/g, '\\');

  // Remover espacios/saltos de línea innecesarios al inicio y final
  sanitized = sanitized.trim();

  return sanitized;
}

/**
 * PUT /api/config/modes
 * Actualizar modos de configuración
 */
export const updateConfigModes = asyncHandler(
  async (req: AuthenticatedRequest, res: Response) => {
    const {
      ticketMode,
      printMode,
      centralizedPrintUrl,
      centralizedPrintPort,
      printTemplate,
      printTemplateType,
      screenPrinters,
    } = req.body;

    const data: any = {};
    if (ticketMode !== undefined) data.ticketMode = ticketMode;
    if (printMode !== undefined) data.printMode = printMode;
    if (centralizedPrintUrl !== undefined) data.centralizedPrintUrl = centralizedPrintUrl;
    if (centralizedPrintPort !== undefined) data.centralizedPrintPort = centralizedPrintPort;
    // Sanitizar el XML de la plantilla antes de guardar
    if (printTemplate !== undefined) data.printTemplate = sanitizePrintTemplate(printTemplate);
    if (printTemplateType !== undefined) data.printTemplateType = printTemplateType;

    const config = await prisma.generalConfig.upsert({
      where: { id: 'general' },
      create: { id: 'general', ...data },
      update: data,
    });

    // Actualizar printerName de cada pantalla si se proporcionó
    if (screenPrinters && Array.isArray(screenPrinters)) {
      for (const sp of screenPrinters) {
        if (sp.id && sp.printerName !== undefined) {
          await prisma.screen.update({
            where: { id: sp.id },
            data: { printerName: sp.printerName || null },
          });
        }
      }
    }

    // Si se cambió el modo de tickets, reiniciar/detener polling
    if (ticketMode) {
      if (ticketMode === 'API') {
        pollingService.stop();
      } else {
        await pollingService.start();
      }
    }

    // Invalidar cache
    await redis.del(REDIS_KEYS.generalConfig());

    res.json({
      message: 'Modos de configuración actualizados',
      config: {
        ticketMode: config.ticketMode,
        printMode: config.printMode,
        centralizedPrintUrl: config.centralizedPrintUrl,
        centralizedPrintPort: config.centralizedPrintPort,
        printTemplate: config.printTemplate,
        printTemplateType: config.printTemplateType,
      },
    });
  }
);

/**
 * POST /api/config/print/test-centralized
 * Probar conexión con servicio de impresión centralizado
 */
export const testCentralizedPrint = asyncHandler(
  async (_req: AuthenticatedRequest, res: Response) => {
    const result = await centralizedPrinterService.testConnection();
    res.json(result);
  }
);

// ============================================
// ENDPOINTS DE API TICKETS (Modo API)
// ============================================

/**
 * POST /api/comandas
 * Endpoint para recibir comandas via API (compatible con sistema anterior)
 * También procesa acciones de usuario y devuelve comandas para pantalla por IP
 */
export const receiveComandas = asyncHandler(
  async (req: Request, res: Response) => {
    const { userActions } = req.body;

    // Obtener IP del cliente
    const clientIp = req.ip || req.socket.remoteAddress || '';
    const ip = clientIp.replace('::ffff:', ''); // Normalizar IPv6 a IPv4

    // Obtener comandas para esta pantalla (procesando acciones si las hay)
    const result = await apiTicketService.getCommandsForScreen(ip, userActions);

    res.type('application/json').send(result);
  }
);

/**
 * POST /api/tickets/receive
 * Endpoint para recibir una comanda individual via API
 */
export const receiveTicket = asyncHandler(
  async (req: AuthenticatedRequest, res: Response) => {
    // Verificar que el modo API esté habilitado
    const isApiMode = await apiTicketService.isApiModeEnabled();
    if (!isApiMode) {
      throw new AppError(400, 'Modo API no habilitado. Active el modo API en configuración.');
    }

    const comanda: ApiComanda = req.body;
    const result = await apiTicketService.receiveComanda(comanda);

    if (result.success) {
      res.json({ success: true, orderId: result.orderId });
    } else {
      res.status(400).json({ success: false, error: result.error });
    }
  }
);

/**
 * POST /api/tickets/receive-batch
 * Endpoint para recibir múltiples comandas via API
 */
export const receiveTicketsBatch = asyncHandler(
  async (req: AuthenticatedRequest, res: Response) => {
    // Verificar que el modo API esté habilitado
    const isApiMode = await apiTicketService.isApiModeEnabled();
    if (!isApiMode) {
      throw new AppError(400, 'Modo API no habilitado. Active el modo API en configuración.');
    }

    const comandas: ApiComanda[] = req.body.comandas || req.body;
    const result = await apiTicketService.receiveComandas(comandas);

    res.json(result);
  }
);

/**
 * GET /api/config
 * Endpoint deprecado - Las pantallas ahora se identifican por número, no por IP
 * Usar /api/screens/by-number/:number en su lugar
 */
export const getScreenConfigByIp = asyncHandler(
  async (_req: Request, res: Response) => {
    res.status(410).json({
      error: 'Endpoint deprecado',
      message: 'Las pantallas ahora se identifican por número. Usar /api/screens/by-number/:number',
      example: '/api/screens/by-number/1',
    });
  }
);
