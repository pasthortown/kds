import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function main() {
  // Obtener la pantalla 3
  const screen = await prisma.screen.findFirst({
    where: { number: 3 },
    include: { queue: true },
  });

  if (!screen) {
    console.error('Pantalla 3 no encontrada');
    process.exit(1);
  }

  console.log(`Creando órdenes para: ${screen.name} (Cola: ${screen.queue?.name})`);

  // Canales disponibles para las pruebas
  const channels = [
    'Local',
    'Kiosko-Efectivo',
    'Kiosko-Tarjeta',
    'PedidosYa',
    'RAPPI',
    'UberEats',
    'Glovo',
    'Drive',
    'APP',
    'Delivery',
  ];

  // Productos de ejemplo
  const products = [
    { name: 'COMBO FAMILIAR 8 PRESAS', items: ['8x Presas Originales', '2x Papas Grandes', '2x Ensaladas', '4x Bebidas'] },
    { name: 'MEGA CAJA', items: ['12x Presas Crispy', '3x Papas Medianas', 'Salsa BBQ'] },
    { name: 'DUO KRUNCH', items: ['2x Sandwich Krunch', '2x Papas Medianas', '2x Bebidas'] },
    { name: 'BUCKET 15 PRESAS', items: ['15x Presas Mixtas', '4x Papas Grandes'] },
    { name: 'COMBO PERSONAL', items: ['3x Presas', '1x Papa', '1x Bebida'] },
    { name: 'WRAP TWISTER', items: ['2x Wrap Twister', 'Papas Medianas'] },
    { name: 'SANDWICH ZINGER', items: ['Sandwich Zinger', 'Papas', 'Bebida Grande'] },
    { name: 'ALITAS BBQ x12', items: ['12x Alitas BBQ', 'Salsa Ranch'] },
  ];

  const customers = [
    'CONSUMIDOR FINAL',
    'Juan Pérez',
    'María García',
    'Carlos López',
    'Ana Martínez',
    'Pedro Sánchez',
    'Laura Torres',
    'Diego Ramírez',
  ];

  const comments = [
    'Cliente VIP',
    'Urgente',
    'Sin cebolla',
    'Extra salsa',
    'Pago con tarjeta',
    'Delivery express',
    null,
  ];

  // Crear 10 órdenes con diferentes canales
  for (let i = 0; i < 10; i++) {
    const channel = channels[i % channels.length];
    const product = products[i % products.length];
    const customer = customers[i % customers.length];
    const comment = comments[i % comments.length];
    const orderNumber = 1000 + i;

    const order = await prisma.order.create({
      data: {
        externalId: `TEST-CHANNEL-${Date.now()}-${i}`,
        channel: channel,
        customerName: customer,
        identifier: `${orderNumber}`,
        status: 'PENDING',
        comments: comment,
        screen: { connect: { id: screen.id } },
        items: {
          create: [
            {
              name: product.name,
              quantity: Math.floor(Math.random() * 3) + 1,
              modifier: product.items.join(', '),
              notes: i % 3 === 0 ? 'Sin mayonesa' : null,
              comments: i % 4 === 0 ? 'Revisar cantidad' : null,
            },
            ...(i % 2 === 0 ? [{
              name: 'BEBIDA GRANDE',
              quantity: 2,
              modifier: 'Pepsi, Sprite',
            }] : []),
          ],
        },
      },
      include: { items: true },
    });

    console.log(`✓ Orden ${order.identifier} creada - Canal: ${channel} - Cliente: ${customer}`);
  }

  console.log('\n¡10 órdenes de prueba creadas exitosamente!');
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
