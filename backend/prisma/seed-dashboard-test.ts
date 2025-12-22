import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

/**
 * Script para generar datos de prueba para validar el Dashboard
 * Ejecutar con: npx ts-node prisma/seed-dashboard-test.ts
 */
async function main() {
  console.log('Generando datos de prueba para Dashboard...\n');

  // Obtener pantallas existentes
  const screens = await prisma.screen.findMany({
    include: { queue: true },
  });

  if (screens.length === 0) {
    console.error('No hay pantallas configuradas. Ejecuta primero: npm run db:seed');
    process.exit(1);
  }

  console.log(`Pantallas encontradas: ${screens.length}`);
  screens.forEach((s) => console.log(`  - ${s.name} (Cola: ${s.queue.name})`));

  // Canales de venta
  const channels = [
    'Local',
    'Kiosko-Efectivo',
    'Kiosko-Tarjeta',
    'PedidosYa',
    'RAPPI',
    'APP',
    'Drive',
    'CALLCENTER',
  ];

  // Productos de ejemplo
  const products = [
    { name: 'Pollo Original 8pcs', base: 'pollo' },
    { name: 'Pollo Crispy 4pcs', base: 'pollo' },
    { name: 'Bucket 15 Presas', base: 'bucket' },
    { name: 'Bucket 21 Presas', base: 'bucket' },
    { name: 'Twister Clasico', base: 'sanduche' },
    { name: 'Sanduche Supreme', base: 'sanduche' },
    { name: 'Papas Grandes', base: 'acomp' },
    { name: 'Coca-Cola 500ml', base: 'bebida' },
    { name: 'Alitas BBQ x12', base: 'alitas' },
    { name: 'Nuggets x20', base: 'nuggets' },
  ];

  const modifiers = [
    'Sin sal',
    'Extra crispy',
    'Con salsa BBQ',
    'Sin cebolla',
    '8 en Original, 7 en Crispy',
    'Bebida: Sprite',
    null,
    null,
  ];

  // Limpiar órdenes de prueba anteriores
  console.log('\nLimpiando órdenes de prueba anteriores...');
  await prisma.orderItem.deleteMany({
    where: { order: { externalId: { startsWith: 'DASHBOARD-TEST-' } } },
  });
  await prisma.order.deleteMany({
    where: { externalId: { startsWith: 'DASHBOARD-TEST-' } },
  });

  const now = new Date();
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
  const createdOrders = [];

  // =====================================================
  // 1. ORDENES FINALIZADAS (para estadísticas del día)
  // =====================================================
  console.log('\nCreando órdenes finalizadas...');

  // Generar órdenes finalizadas distribuidas por hora
  for (let hour = 6; hour <= now.getHours(); hour++) {
    // Cantidad de órdenes por hora (más en horas pico)
    const ordersPerHour = hour >= 11 && hour <= 14 ? 15 : hour >= 18 && hour <= 21 ? 12 : 5;

    for (let i = 0; i < ordersPerHour; i++) {
      const screen = screens[Math.floor(Math.random() * screens.length)];
      const channel = channels[Math.floor(Math.random() * channels.length)];

      // Tiempo de creación dentro de la hora
      const createdAt = new Date(today);
      createdAt.setHours(hour, Math.floor(Math.random() * 60), Math.floor(Math.random() * 60));

      // Tiempo de finalización (variado para tener mezcla de "a tiempo" y "fuera de tiempo")
      // timeLimit típico es 5 minutos = 300 segundos
      const isOnTime = Math.random() < 0.7; // 70% a tiempo
      const finishTimeSeconds = isOnTime
        ? Math.floor(Math.random() * 240) + 60 // 1-4 minutos (a tiempo)
        : Math.floor(Math.random() * 600) + 300; // 5-15 minutos (fuera de tiempo)

      const finishedAt = new Date(createdAt.getTime() + finishTimeSeconds * 1000);

      // No crear órdenes en el futuro
      if (finishedAt > now) continue;

      // Generar items aleatorios
      const numItems = Math.floor(Math.random() * 5) + 1;
      const items = [];
      for (let j = 0; j < numItems; j++) {
        const product = products[Math.floor(Math.random() * products.length)];
        const modifier = modifiers[Math.floor(Math.random() * modifiers.length)];
        items.push({
          name: product.name,
          quantity: Math.floor(Math.random() * 3) + 1,
          modifier,
        });
      }

      const orderNum = 1000 + createdOrders.length;
      const order = await prisma.order.create({
        data: {
          externalId: `DASHBOARD-TEST-FINISHED-${Date.now()}-${createdOrders.length}`,
          screenId: screen.id,
          channel,
          customerName: Math.random() > 0.3 ? `Cliente ${orderNum}` : null,
          identifier: orderNum.toString(),
          status: 'FINISHED',
          createdAt,
          finishedAt,
          items: { create: items },
        },
      });

      createdOrders.push({ ...order, type: 'FINISHED' });
    }
  }

  console.log(`  Creadas ${createdOrders.filter((o) => o.type === 'FINISHED').length} órdenes finalizadas`);

  // =====================================================
  // 2. ORDENES PENDIENTES (activas ahora)
  // =====================================================
  console.log('\nCreando órdenes pendientes...');
  const pendingCount = 8;

  for (let i = 0; i < pendingCount; i++) {
    const screen = screens[Math.floor(Math.random() * screens.length)];
    const channel = channels[Math.floor(Math.random() * channels.length)];

    // Creadas hace 0-10 minutos
    const minutesAgo = Math.random() * 10;
    const createdAt = new Date(now.getTime() - minutesAgo * 60 * 1000);

    const numItems = Math.floor(Math.random() * 6) + 2;
    const items = [];
    for (let j = 0; j < numItems; j++) {
      const product = products[Math.floor(Math.random() * products.length)];
      const modifier = modifiers[Math.floor(Math.random() * modifiers.length)];
      items.push({
        name: product.name,
        quantity: Math.floor(Math.random() * 3) + 1,
        modifier,
      });
    }

    const orderNum = 2000 + i;
    const order = await prisma.order.create({
      data: {
        externalId: `DASHBOARD-TEST-PENDING-${Date.now()}-${i}`,
        screenId: screen.id,
        channel,
        customerName: `Cliente ${orderNum}`,
        identifier: orderNum.toString(),
        status: 'PENDING',
        createdAt,
        items: { create: items },
      },
    });

    createdOrders.push({ ...order, type: 'PENDING' });
  }

  console.log(`  Creadas ${pendingCount} órdenes pendientes`);

  // =====================================================
  // 3. ORDENES CANCELADAS
  // =====================================================
  console.log('\nCreando órdenes canceladas...');
  const cancelledCount = 3;

  for (let i = 0; i < cancelledCount; i++) {
    const screen = screens[Math.floor(Math.random() * screens.length)];
    const channel = channels[Math.floor(Math.random() * channels.length)];

    const hoursAgo = Math.floor(Math.random() * (now.getHours() - 6)) + 1;
    const createdAt = new Date(today);
    createdAt.setHours(now.getHours() - hoursAgo, Math.floor(Math.random() * 60));

    const numItems = Math.floor(Math.random() * 4) + 1;
    const items = [];
    for (let j = 0; j < numItems; j++) {
      const product = products[Math.floor(Math.random() * products.length)];
      items.push({
        name: product.name,
        quantity: Math.floor(Math.random() * 2) + 1,
        modifier: null,
      });
    }

    const orderNum = 3000 + i;
    const order = await prisma.order.create({
      data: {
        externalId: `DASHBOARD-TEST-CANCELLED-${Date.now()}-${i}`,
        screenId: screen.id,
        channel,
        customerName: `Cliente ${orderNum}`,
        identifier: orderNum.toString(),
        status: 'CANCELLED',
        createdAt,
        items: { create: items },
      },
    });

    createdOrders.push({ ...order, type: 'CANCELLED' });
  }

  console.log(`  Creadas ${cancelledCount} órdenes canceladas`);

  // =====================================================
  // RESUMEN
  // =====================================================
  const finishedOrders = createdOrders.filter((o) => o.type === 'FINISHED');
  const pendingOrders = createdOrders.filter((o) => o.type === 'PENDING');
  const cancelledOrders = createdOrders.filter((o) => o.type === 'CANCELLED');

  console.log('\n' + '='.repeat(60));
  console.log('RESUMEN DE DATOS DE PRUEBA GENERADOS');
  console.log('='.repeat(60));
  console.log(`Total de órdenes creadas: ${createdOrders.length}`);
  console.log(`  - Finalizadas: ${finishedOrders.length}`);
  console.log(`  - Pendientes:  ${pendingOrders.length}`);
  console.log(`  - Canceladas:  ${cancelledOrders.length}`);
  console.log('');
  console.log('Distribución por pantalla:');
  screens.forEach((screen) => {
    const count = createdOrders.filter((o) => o.screenId === screen.id).length;
    console.log(`  - ${screen.name}: ${count} órdenes`);
  });
  console.log('');
  console.log('Ahora puedes abrir el Dashboard en el Backoffice para ver las estadísticas.');
  console.log('URL: http://localhost:5174 (o el puerto configurado)');
  console.log('');
  console.log('Para limpiar estos datos, ejecuta:');
  console.log('  npx ts-node prisma/seed-dashboard-test.ts --clean');
  console.log('='.repeat(60));
}

// Verificar si se quiere limpiar
if (process.argv.includes('--clean')) {
  prisma.orderItem
    .deleteMany({
      where: { order: { externalId: { startsWith: 'DASHBOARD-TEST-' } } },
    })
    .then(() =>
      prisma.order.deleteMany({
        where: { externalId: { startsWith: 'DASHBOARD-TEST-' } },
      })
    )
    .then((result) => {
      console.log(`Eliminadas ${result.count} órdenes de prueba del Dashboard.`);
      process.exit(0);
    })
    .catch((e) => {
      console.error(e);
      process.exit(1);
    })
    .finally(() => prisma.$disconnect());
} else {
  main()
    .catch((e) => {
      console.error(e);
      process.exit(1);
    })
    .finally(() => prisma.$disconnect());
}
