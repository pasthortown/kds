import { Request, Response } from 'express';
import { prisma } from '../config/database';
import { redis, REDIS_KEYS } from '../config/redis';
import { env } from '../config/env';
import { orderService } from '../services/order.service';
import { screenService } from '../services/screen.service';
import { asyncHandler } from '../middlewares/error.middleware';
import { ScreenStatus } from '../types';

/**
 * GET /api/reports/dashboard
 * Obtiene todos los datos necesarios para el reporte del dashboard
 * Combina estadísticas de órdenes, pantallas y metadatos en una sola respuesta
 */
export const getDashboardReport = asyncHandler(
  async (req: Request, res: Response) => {
    const { timeLimit = '5' } = req.query;
    const timeLimitMinutes = parseInt(timeLimit as string);

    // Obtener datos en paralelo
    const [dashboardStats, screens] = await Promise.all([
      orderService.getDashboardStats(timeLimitMinutes),
      screenService.getAllScreensWithStatus(),
    ]);

    // Calcular métricas adicionales para el reporte
    const totalFinished = dashboardStats.summary.onTime + dashboardStats.summary.outOfTime;
    const onTimePercentage = totalFinished > 0
      ? Math.round((dashboardStats.summary.onTime / totalFinished) * 100)
      : 0;
    const outOfTimePercentage = totalFinished > 0
      ? Math.round((dashboardStats.summary.outOfTime / totalFinished) * 100)
      : 0;

    // Contar pantallas por estado
    const screensByStatus = {
      online: screens.filter(s => s.status === 'ONLINE').length,
      offline: screens.filter(s => s.status === 'OFFLINE').length,
      standby: screens.filter(s => s.status === 'STANDBY').length,
      total: screens.length,
    };

    // Hora pico (hora con más órdenes)
    const peakHour = dashboardStats.hourlyStats.reduce(
      (max, curr) => (curr.total > max.total ? curr : max),
      { hour: 0, total: 0, onTime: 0, outOfTime: 0 }
    );

    // Canal más activo
    const topChannel = dashboardStats.byChannel.reduce(
      (max, curr) => (curr.total > max.total ? curr : max),
      { channel: 'N/A', total: 0, onTime: 0, outOfTime: 0, avgFinishTime: 0 }
    );

    // Pantalla más eficiente (mayor % a tiempo con al menos 5 órdenes)
    const efficientScreen = dashboardStats.byScreen
      .filter(s => s.finishedToday >= 5)
      .reduce(
        (best, curr) => {
          const currRate = curr.finishedToday > 0 ? curr.onTime / curr.finishedToday : 0;
          const bestRate = best.finishedToday > 0 ? best.onTime / best.finishedToday : 0;
          return currRate > bestRate ? curr : best;
        },
        dashboardStats.byScreen[0] || null
      );

    const report = {
      // Metadatos del reporte
      metadata: {
        id_restaurante: env.RESTAURANT_ID,
        generatedAt: new Date().toISOString(),
        timeLimit: timeLimitMinutes,
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        reportDate: new Date().toLocaleDateString('es-EC', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric',
        }),
      },

      // Resumen ejecutivo
      summary: {
        ...dashboardStats.summary,
        totalProcessed: totalFinished,
        onTimePercentage,
        outOfTimePercentage,
      },

      // Estado de pantallas
      screenStatus: screensByStatus,

      // Órdenes destacadas
      highlights: {
        fastestOrder: dashboardStats.fastestOrder,
        slowestOrder: dashboardStats.slowestOrder,
        peakHour: peakHour.total > 0 ? {
          hour: peakHour.hour,
          hourLabel: `${peakHour.hour.toString().padStart(2, '0')}:00`,
          total: peakHour.total,
          onTime: peakHour.onTime,
          outOfTime: peakHour.outOfTime,
        } : null,
        topChannel: topChannel.total > 0 ? topChannel : null,
        mostEfficientScreen: efficientScreen ? {
          ...efficientScreen,
          efficiencyRate: efficientScreen.finishedToday > 0
            ? Math.round((efficientScreen.onTime / efficientScreen.finishedToday) * 100)
            : 0,
        } : null,
      },

      // Estadísticas por pantalla
      byScreen: dashboardStats.byScreen.map(screen => ({
        ...screen,
        efficiencyRate: screen.finishedToday > 0
          ? Math.round((screen.onTime / screen.finishedToday) * 100)
          : 0,
      })),

      // Estadísticas por canal
      byChannel: dashboardStats.byChannel.map(channel => ({
        ...channel,
        efficiencyRate: channel.total > 0
          ? Math.round((channel.onTime / channel.total) * 100)
          : 0,
      })),

      // Estadísticas por hora (solo horas con actividad o en horario laboral)
      hourlyStats: dashboardStats.hourlyStats
        .filter(h => h.total > 0 || (h.hour >= 6 && h.hour <= 23))
        .map(h => ({
          ...h,
          hourLabel: `${h.hour.toString().padStart(2, '0')}:00`,
          efficiencyRate: h.total > 0 ? Math.round((h.onTime / h.total) * 100) : 0,
        })),

      // Lista de pantallas con estado
      screens: screens.map(s => ({
        id: s.id,
        number: s.number,
        name: s.name,
        url: `/kds${s.number}`,
        queueName: s.queueName,
        status: s.status,
        lastHeartbeat: s.lastHeartbeat,
      })),
    };

    res.json(report);
  }
);

/**
 * GET /api/reports/daily-summary
 * Obtiene un resumen diario de operaciones
 */
export const getDailySummary = asyncHandler(
  async (req: Request, res: Response) => {
    const { date } = req.query;

    // Si no se especifica fecha, usar hoy
    const targetDate = date ? new Date(date as string) : new Date();
    targetDate.setHours(0, 0, 0, 0);

    const nextDay = new Date(targetDate);
    nextDay.setDate(nextDay.getDate() + 1);

    // Obtener órdenes del día
    const orders = await prisma.order.findMany({
      where: {
        createdAt: {
          gte: targetDate,
          lt: nextDay,
        },
      },
      select: {
        id: true,
        status: true,
        channel: true,
        createdAt: true,
        finishedAt: true,
        screenId: true,
        screen: {
          select: {
            name: true,
            queue: { select: { name: true } },
          },
        },
      },
    });

    // Calcular estadísticas
    const finished = orders.filter(o => o.status === 'FINISHED');
    const pending = orders.filter(o => o.status === 'PENDING');
    const cancelled = orders.filter(o => o.status === 'CANCELLED');

    // Tiempos de finalización
    const finishTimes = finished
      .filter(o => o.finishedAt)
      .map(o => o.finishedAt!.getTime() - o.createdAt.getTime());

    const summary = {
      date: targetDate.toISOString().split('T')[0],
      dateFormatted: targetDate.toLocaleDateString('es-EC', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      }),
      totals: {
        total: orders.length,
        finished: finished.length,
        pending: pending.length,
        cancelled: cancelled.length,
      },
      times: {
        avgFinishTime: finishTimes.length > 0
          ? Math.round(finishTimes.reduce((a, b) => a + b, 0) / finishTimes.length / 1000)
          : 0,
        minFinishTime: finishTimes.length > 0
          ? Math.round(Math.min(...finishTimes) / 1000)
          : 0,
        maxFinishTime: finishTimes.length > 0
          ? Math.round(Math.max(...finishTimes) / 1000)
          : 0,
      },
      byChannel: Object.entries(
        orders.reduce((acc, o) => {
          acc[o.channel] = (acc[o.channel] || 0) + 1;
          return acc;
        }, {} as Record<string, number>)
      ).map(([channel, count]) => ({ channel, count }))
        .sort((a, b) => b.count - a.count),
    };

    res.json(summary);
  }
);
