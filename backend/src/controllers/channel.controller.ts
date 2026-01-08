import { Request, Response } from 'express';
import { z } from 'zod';
import { prisma } from '../config/database';
import { redis, REDIS_KEYS, redisPub } from '../config/redis';
import { AuthenticatedRequest } from '../types';
import { asyncHandler, AppError } from '../middlewares/error.middleware';

/**
 * Sincroniza los colores del canal a todas las pantallas (ChannelColor)
 */
async function syncChannelToAllScreens(channelName: string, backgroundColor: string, textColor: string): Promise<void> {
  // Obtener todas las appearances
  const appearances = await prisma.appearance.findMany({
    select: { id: true, screenId: true },
  });

  for (const appearance of appearances) {
    // Upsert: crear o actualizar el channelColor para esta appearance
    await prisma.channelColor.upsert({
      where: {
        appearanceId_channel: {
          appearanceId: appearance.id,
          channel: channelName,
        },
      },
      update: {
        color: backgroundColor,
        textColor: textColor,
      },
      create: {
        appearanceId: appearance.id,
        channel: channelName,
        color: backgroundColor,
        textColor: textColor,
      },
    });

    // Invalidar cache de la pantalla
    await redis.del(REDIS_KEYS.configCache(appearance.screenId));

    // Notificar al frontend via WebSocket
    await redisPub.publish(
      REDIS_KEYS.configUpdated(),
      JSON.stringify({ screenId: appearance.screenId })
    );
  }
}

/**
 * Elimina el canal de todas las pantallas (ChannelColor)
 */
async function removeChannelFromAllScreens(channelName: string): Promise<void> {
  // Obtener todas las appearances para invalidar cache
  const appearances = await prisma.appearance.findMany({
    select: { screenId: true },
  });

  // Eliminar todos los channelColors con este nombre
  await prisma.channelColor.deleteMany({
    where: { channel: channelName },
  });

  // Invalidar cache y notificar a todas las pantallas
  for (const appearance of appearances) {
    await redis.del(REDIS_KEYS.configCache(appearance.screenId));

    // Notificar al frontend via WebSocket
    await redisPub.publish(
      REDIS_KEYS.configUpdated(),
      JSON.stringify({ screenId: appearance.screenId })
    );
  }
}

// Schemas de validación
const createChannelSchema = z.object({
  name: z.string().min(1).max(50),
  displayName: z.string().max(50).optional(),
  backgroundColor: z.string().regex(/^#[0-9A-Fa-f]{6}$/).default('#4a90e2'),
  textColor: z.string().regex(/^#[0-9A-Fa-f]{6}$/).default('#ffffff'),
  icon: z.string().max(100).optional(),
  priority: z.number().int().min(0).max(100).default(0),
  active: z.boolean().default(true),
});

const updateChannelSchema = createChannelSchema.partial();

/**
 * GET /api/channels
 * Obtener todos los canales
 */
export const getAllChannels = asyncHandler(
  async (_req: Request, res: Response) => {
    const channels = await prisma.channel.findMany({
      orderBy: [
        { priority: 'desc' },
        { name: 'asc' }
      ],
    });

    res.json(channels);
  }
);

/**
 * GET /api/channels/active
 * Obtener solo canales activos
 */
export const getActiveChannels = asyncHandler(
  async (_req: Request, res: Response) => {
    const channels = await prisma.channel.findMany({
      where: { active: true },
      orderBy: [
        { priority: 'desc' },
        { name: 'asc' }
      ],
    });

    res.json(channels);
  }
);

/**
 * GET /api/channels/:id
 * Obtener un canal por ID
 */
export const getChannel = asyncHandler(
  async (req: Request, res: Response) => {
    const { id } = req.params;

    const channel = await prisma.channel.findUnique({
      where: { id },
    });

    if (!channel) {
      throw new AppError(404, 'Channel not found');
    }

    res.json(channel);
  }
);

/**
 * GET /api/channels/by-name/:name
 * Obtener un canal por nombre
 */
export const getChannelByName = asyncHandler(
  async (req: Request, res: Response) => {
    const { name } = req.params;

    const channel = await prisma.channel.findFirst({
      where: {
        name: { equals: name, mode: 'insensitive' }
      },
    });

    if (!channel) {
      // Retornar colores por defecto si no existe
      res.json({
        name,
        backgroundColor: '#4a90e2',
        textColor: '#ffffff',
        active: true,
      });
      return;
    }

    res.json(channel);
  }
);

/**
 * POST /api/channels
 * Crear un nuevo canal
 */
export const createChannel = asyncHandler(
  async (req: AuthenticatedRequest, res: Response) => {
    const data = createChannelSchema.parse(req.body);

    // Verificar si ya existe
    const existing = await prisma.channel.findUnique({
      where: { name: data.name },
    });

    if (existing) {
      throw new AppError(400, 'Channel already exists');
    }

    const channel = await prisma.channel.create({
      data,
    });

    // Sincronizar a todas las pantallas
    await syncChannelToAllScreens(channel.name, channel.backgroundColor, channel.textColor);

    res.status(201).json(channel);
  }
);

/**
 * PUT /api/channels/:id
 * Actualizar un canal
 */
export const updateChannel = asyncHandler(
  async (req: AuthenticatedRequest, res: Response) => {
    const { id } = req.params;
    const data = updateChannelSchema.parse(req.body);

    // Verificar que existe
    const existing = await prisma.channel.findUnique({
      where: { id },
    });

    if (!existing) {
      throw new AppError(404, 'Channel not found');
    }

    // Si se está cambiando el nombre, verificar que no exista otro con ese nombre
    if (data.name && data.name !== existing.name) {
      const nameExists = await prisma.channel.findUnique({
        where: { name: data.name },
      });
      if (nameExists) {
        throw new AppError(400, 'Channel name already exists');
      }
    }

    const channel = await prisma.channel.update({
      where: { id },
      data,
    });

    // Si cambió el nombre, eliminar el viejo y crear el nuevo
    if (data.name && data.name !== existing.name) {
      await removeChannelFromAllScreens(existing.name);
    }

    // Sincronizar los colores actualizados a todas las pantallas
    await syncChannelToAllScreens(channel.name, channel.backgroundColor, channel.textColor);

    res.json(channel);
  }
);

/**
 * DELETE /api/channels/:id
 * Eliminar un canal
 */
export const deleteChannel = asyncHandler(
  async (req: AuthenticatedRequest, res: Response) => {
    const { id } = req.params;

    // Verificar que existe
    const existing = await prisma.channel.findUnique({
      where: { id },
    });

    if (!existing) {
      throw new AppError(404, 'Channel not found');
    }

    await prisma.channel.delete({
      where: { id },
    });

    // Eliminar de todas las pantallas
    await removeChannelFromAllScreens(existing.name);

    res.json({ message: 'Channel deleted' });
  }
);

/**
 * POST /api/channels/seed
 * Crear canales por defecto
 */
export const seedChannels = asyncHandler(
  async (_req: AuthenticatedRequest, res: Response) => {
    // SALON = verde (#02d01d), LLEVAR = púrpura (#891cb4)
    // Orden: primero SALON, luego LLEVAR; KIOSKO > MXP > PICKUP > DOMICILIO
    const defaultChannels = [
      { name: 'KIOSKO EFECTIVO-SALON', backgroundColor: '#02d01d', textColor: '#ffffff', priority: 9 },
      { name: 'KIOSKO TARJETA-SALON', backgroundColor: '#02d01d', textColor: '#ffffff', priority: 8 },
      { name: 'MXP-SALON', backgroundColor: '#02d01d', textColor: '#ffffff', priority: 7 },
      { name: 'PICKUP-SALON', backgroundColor: '#02d01d', textColor: '#ffffff', priority: 6 },
      { name: 'KIOSKO EFECTIVO-LLEVAR', backgroundColor: '#891cb4', textColor: '#ffffff', priority: 5 },
      { name: 'KIOSKO TARJETA-LLEVAR', backgroundColor: '#891cb4', textColor: '#ffffff', priority: 4 },
      { name: 'MXP-LLEVAR', backgroundColor: '#891cb4', textColor: '#ffffff', priority: 3 },
      { name: 'PICKUP-LLEVAR', backgroundColor: '#891cb4', textColor: '#ffffff', priority: 2 },
      { name: 'DIMICILIO-DOMICILIO', backgroundColor: '#891cb4', textColor: '#ffffff', priority: 1 },
    ];

    let created = 0;
    let skipped = 0;

    for (const channel of defaultChannels) {
      try {
        const newChannel = await prisma.channel.create({
          data: channel,
        });
        // Sincronizar a todas las pantallas
        await syncChannelToAllScreens(newChannel.name, newChannel.backgroundColor, newChannel.textColor);
        created++;
      } catch {
        // Ya existe, actualizar colores en todas las pantallas
        await syncChannelToAllScreens(channel.name, channel.backgroundColor, channel.textColor);
        skipped++;
      }
    }

    res.json({
      message: `Seeded ${created} channels, ${skipped} already existed (colors synced)`,
      created,
      skipped,
    });
  }
);
