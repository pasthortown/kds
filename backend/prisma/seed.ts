import { PrismaClient } from '@prisma/client';
import bcrypt from 'bcryptjs';

const prisma = new PrismaClient();

// Canales globales del sistema
const defaultChannels = [
  { name: 'Local', backgroundColor: '#7ed321', textColor: '#ffffff', priority: 10 },
  { name: 'Kiosko-Efectivo', backgroundColor: '#0299d0', textColor: '#ffffff', priority: 9 },
  { name: 'Kiosko-Tarjeta', backgroundColor: '#d0021b', textColor: '#ffffff', priority: 8 },
  { name: 'PedidosYa', backgroundColor: '#d0021b', textColor: '#ffffff', priority: 7 },
  { name: 'RAPPI', backgroundColor: '#ff5a00', textColor: '#ffffff', priority: 6 },
  { name: 'UberEats', backgroundColor: '#06c167', textColor: '#ffffff', priority: 5 },
  { name: 'Glovo', backgroundColor: '#ffc244', textColor: '#000000', priority: 4 },
  { name: 'Drive', backgroundColor: '#9b59b6', textColor: '#ffffff', priority: 3 },
  { name: 'Delivery', backgroundColor: '#e74c3c', textColor: '#ffffff', priority: 1 },
];

// Canales de cola (para cada cola)
const queueChannelsData = [
  { channel: 'Local', color: '#7ed321', priority: 10 },
  { channel: 'Kiosko-Efectivo', color: '#0299d0', priority: 9 },
  { channel: 'Kiosko-Tarjeta', color: '#d0021b', priority: 8 },
  { channel: 'PedidosYa', color: '#d0021b', priority: 7 },
  { channel: 'RAPPI', color: '#ff5a00', priority: 6 },
  { channel: 'UberEats', color: '#06c167', priority: 5 },
  { channel: 'Glovo', color: '#ffc244', priority: 4 },
  { channel: 'Drive', color: '#9b59b6', priority: 3 },
  { channel: 'Delivery', color: '#e74c3c', priority: 1 },
  { channel: 'APP', color: '#dc21d6', priority: 0 },
];

// Colores de canal por apariencia (para cada pantalla)
// Incluye canales con sufijo -SALON (verde) y -LLEVAR (morado)
const channelColorsData = [
  // Canales base
  { channel: 'Local', color: '#7ed321', textColor: '#ffffff' },
  { channel: 'Kiosko-Efectivo', color: '#0299d0', textColor: '#ffffff' },
  { channel: 'Kiosko-Tarjeta', color: '#d0021b', textColor: '#ffffff' },
  { channel: 'PedidosYa', color: '#d0021b', textColor: '#ffffff' },
  { channel: 'RAPPI', color: '#ff5a00', textColor: '#ffffff' },
  { channel: 'UberEats', color: '#06c167', textColor: '#ffffff' },
  { channel: 'Glovo', color: '#ffc244', textColor: '#000000' },
  { channel: 'Drive', color: '#9b59b6', textColor: '#ffffff' },
  { channel: 'Delivery', color: '#e74c3c', textColor: '#ffffff' },
  // Canales KIOSKO con tipo (SALON=verde, LLEVAR=morado)
  { channel: 'KIOSKO EFECTIVO-SALON', color: '#02d01d', textColor: '#ffffff' },
  { channel: 'KIOSKO EFECTIVO-LLEVAR', color: '#891cb4', textColor: '#ffffff' },
  { channel: 'KIOSKO TARJETA-SALON', color: '#02d01d', textColor: '#ffffff' },
  { channel: 'KIOSKO TARJETA-LLEVAR', color: '#891cb4', textColor: '#ffffff' },
  // Canales MXP con tipo (SALON=verde, LLEVAR=morado)
  { channel: 'MXP-SALON', color: '#02d01d', textColor: '#ffffff' },
  { channel: 'MXP-LLEVAR', color: '#891cb4', textColor: '#ffffff' },
];

// Colores SLA por tiempo (minutos)
const cardColorsData = [
  { color: '#3e961f', quantityColor: '#0c8c14', minutes: '01:00', order: 1, isFullBackground: false },
  { color: '#9b9728', quantityColor: '#7e7622', minutes: '02:00', order: 2, isFullBackground: false },
  { color: '#cf1d09', quantityColor: '#a70a0a', minutes: '03:00', order: 3, isFullBackground: true },
];

// Configuracion de apariencia base
const baseAppearance = {
  fontSize: '20px',
  fontFamily: 'Arimo-Medium',
  columnsPerScreen: 4,
  columnSize: '260px',
  footerHeight: '72px',
  ordersDisplay: 'COLUMNS',
  theme: 'DARK',
  screenName: '',
  screenSplit: true,
  showCounters: false,
  backgroundColor: '#000000',
  headerColor: '#1a1a2e',
  headerTextColor: '#ffffff',
  cardColor: '#ffffff',
  textColor: '#1a1a2e',
  accentColor: '#e94560',
  // Header
  headerFontFamily: 'monospace',
  headerFontSize: 'xlarge',
  headerFontWeight: 'bold',
  headerFontStyle: 'normal',
  headerBgColor: '',
  headerTextColorCustom: '#ffffff',
  showHeader: true,
  // Timer
  timerFontFamily: 'monospace',
  timerFontSize: 'xxlarge',
  timerFontWeight: 'bold',
  timerFontStyle: 'normal',
  timerTextColor: '#ffffff',
  showTimer: true,
  // Cliente
  clientFontFamily: 'monospace',
  clientFontSize: 'medium',
  clientFontWeight: 'normal',
  clientFontStyle: 'italic',
  clientTextColor: '#ffffff',
  clientBgColor: '',
  showClient: true,
  // Cantidad
  quantityFontFamily: 'monospace',
  quantityFontSize: 'xlarge',
  quantityFontWeight: 'bold',
  quantityFontStyle: 'normal',
  quantityTextColor: '',
  showQuantity: true,
  // Producto
  productFontFamily: 'monospace',
  productFontSize: 'xlarge',
  productFontWeight: 'bold',
  productFontStyle: 'normal',
  productTextColor: '',
  productBgColor: '',
  productUppercase: true,
  // Subitem
  subitemFontFamily: 'monospace',
  subitemFontSize: 'large',
  subitemFontWeight: 'normal',
  subitemFontStyle: 'normal',
  subitemTextColor: '#333333',
  subitemBgColor: '',
  subitemIndent: 24,
  showSubitems: true,
  // Modificador
  modifierFontFamily: 'monospace',
  modifierFontSize: 'large',
  modifierFontWeight: 'normal',
  modifierFontStyle: 'italic',
  modifierFontColor: '#666666',
  modifierBgColor: '',
  modifierIndent: 24,
  showModifiers: true,
  // Notas
  notesFontFamily: 'monospace',
  notesFontSize: 'large',
  notesFontWeight: 'normal',
  notesFontStyle: 'italic',
  notesTextColor: '#004cff',
  notesBgColor: '',
  notesIndent: 24,
  showNotes: true,
  // Comentarios
  commentsFontFamily: 'monospace',
  commentsFontSize: 'large',
  commentsFontWeight: 'semibold',
  commentsFontStyle: 'italic',
  commentsTextColor: '#4b824d',
  commentsBgColor: '',
  commentsIndent: 24,
  showComments: true,
  // Canal
  channelFontFamily: 'monospace',
  channelFontSize: 'xxlarge',
  channelFontWeight: 'bold',
  channelFontStyle: 'normal',
  channelTextColor: '#ffffff',
  channelUppercase: true,
  showChannel: true,
  // Legacy
  headerShowChannel: true,
  headerShowTime: true,
  rows: 3,
  maxItemsPerColumn: 6,
  showOrderNumber: true,
  animationEnabled: true,
};

// Configuracion de preferencias base
const basePreference = {
  finishOrderActive: false,
  finishOrderTime: '00:20',
  showClientData: true,
  showName: true,
  showIdentifier: true,
  identifierMessage: 'Orden',
  showNumerator: false,
  showPagination: true,
  sourceBoxActive: true,
  sourceBoxMessage: 'KDS',
  touchEnabled: false,
  botoneraEnabled: true,
};

// Configuracion de teclado base
const baseKeyboard = {
  finishFirstOrder: 'h',
  finishSecondOrder: '3',
  finishThirdOrder: '1',
  finishFourthOrder: 'f',
  finishFifthOrder: 'j',
  nextPage: 'i',
  previousPage: 'g',
  undo: 'c',
  resetTime: 'r',
  firstPage: 'q',
  secondPage: 'w',
  middlePage: 'e',
  penultimatePage: 'x',
  lastPage: 't',
  confirmModal: '0',
  cancelModal: 'v',
  power: 'a',
  exit: 'm',
  combos: '[]',
  debounceTime: 200,
};

// Configuración de impresión centralizada
// Usa 127.0.0.1 por defecto para evitar que se direccionen impresiones a otros locales
const centralizedPrintConfig = {
  printMode: 'CENTRALIZED',
  centralizedPrintUrl: 'http://127.0.0.1:5000/api/ImpresionTickets/Impresion',
  centralizedPrintUrlBackup: '',
  centralizedPrintPort: 5000,
  printTemplateType: 'orden_pedido',
  printTemplate: `<?xml version="1.0" encoding="utf-8"?><plantilla id="impresionOrdenPedidoLocal"><salto/><parametro alineacion="centrado" estilo="bold|fontB">datafono</parametro><salto/><etiqueta estilo="bold|fontB" tamano="2" alineacion="izquierda">    #Medio     #Pedido       #Paquete     #Bebidas </etiqueta><salto/><etiqueta estilo="default" alineacion="izquierda">_________ _________ _________ _________</etiqueta><salto/><etiqueta estilo="default" alineacion="izquierda">| </etiqueta><parametro estilo="fontB" alineacion="izquierda">medio</parametro><etiqueta estilo="default" alineacion="izquierda">    | |       | |       | |       |</etiqueta><salto/><etiqueta estilo="default">----------------------------------------</etiqueta><salto/><parametro alineacion="centrado" estilo="bold|fontB">tituloPickup</parametro><salto/><parametro alineacion="centrado" estilo="bold|fontB">tipoPickup</parametro><salto/><etiqueta estilo="bold" alineacion="izquierda">MESA: </etiqueta><parametro estilo="bold" alineacion="izquierda">mesa</parametro><salto/><etiqueta estilo="bold" alineacion="izquierda">TRANSACIÓN #: </etiqueta><parametro estilo="bold" alineacion="izquierda">transaccion</parametro><salto/><etiqueta estilo="bold" alineacion="izquierda"># Cuenta:</etiqueta><parametro estilo="bold" alineacion="izquierda">numeroCuenta</parametro><salto/><parametro alineacion="izquierda" estilo="bold" sizeToMultiply="1">observacion</parametro><salto/><etiqueta estilo="default" alineacion="izquierda">Canal: </etiqueta><parametro estilo="default" alineacion="izquierda">canal</parametro><salto/><etiqueta estilo="default" alineacion="izquierda">Cajero/a: </etiqueta><parametro estilo="default" alineacion="izquierda">cajero</parametro><salto/><etiqueta estilo="default" alineacion="izquierda">FECHA: </etiqueta><parametro estilo="default" alineacion="izquierda">fecha</parametro><salto/><salto/><etiqueta estilo="default">------------------------------------------</etiqueta><etiquetaCentrada estilo="bold" tamano="5">CANT</etiquetaCentrada><etiquetaCentrada estilo="bold" tamano="1"/><etiquetaIzquierda estilo="bold" tamano="32">DESCRIPCIÓN</etiquetaIzquierda><salto/><etiqueta estilo="default">------------------------------------------</etiqueta><salto/><registrosDetalle estilo="bold"><item alineacion="centrado" tamano="5">cantidad</item><espacio alineacion="izquierda" tamano="1"/><item alineacion="izquierda" tamano="32">producto</item></registrosDetalle><salto/><etiqueta estilo="default" alineacion="centrado">------------------------------------------</etiqueta><salto/><salto/><parametro alineacion="izquierda" estilo="default">qrDireccionTitulo</parametro><salto/><qr alineacion="centrado">qrDireccion</qr><qr alineacion="centrado">qrPedido</qr><salto/><salto/><salto/><salto/><salto/></plantilla>`,
};

async function main() {
  console.log('Seeding database...');

  // =====================================================
  // USUARIOS
  // =====================================================
  const kfcAdminPassword = await bcrypt.hash('cx-dsi2025', 10);
  await prisma.user.upsert({
    where: { email: 'admin@kfc.com.ec' },
    update: { password: kfcAdminPassword },
    create: {
      email: 'admin@kfc.com.ec',
      password: kfcAdminPassword,
      name: 'Administrador KFC',
      role: 'ADMIN',
    },
  });
  console.log('User admin@kfc.com.ec created');

  const defaultAdminPassword = process.env.ADMIN_DEFAULT_PASSWORD || 'admin123';
  const adminPassword = await bcrypt.hash(defaultAdminPassword, 10);
  const adminEmail = process.env.ADMIN_EMAIL || 'admin@kds.local';
  await prisma.user.upsert({
    where: { email: adminEmail },
    update: { password: adminPassword },
    create: {
      email: adminEmail,
      password: adminPassword,
      name: 'Administrador',
      role: 'ADMIN',
    },
  });
  console.log(`User ${adminEmail} created`);

  // =====================================================
  // CONFIGURACION GENERAL (con impresión centralizada)
  // =====================================================
  // IMPORTANTE: NO sobrescribir URLs de impresión en update para preservar configuración de producción
  await prisma.generalConfig.upsert({
    where: { id: 'general' },
    update: {
      ticketMode: 'API',  // Forzar modo API por defecto
      // NO incluir centralizedPrintUrl ni centralizedPrintUrlBackup en update
      // para preservar las URLs configuradas en producción
    },
    create: {
      id: 'general',
      testMode: false,
      ticketMode: 'API',
      pollingInterval: 2000,
      orderLifetime: 4,
      logRetentionDays: 5,
      printTcp: true,
      orderAliveTime: 300,
      printRetries: 3,
      printColumns: 24,
      printFontSize: 'small',
      showOrdersAndCounters: true,
      countProducts: false,
      // Configuración de impresión centralizada (solo en create)
      ...centralizedPrintConfig,
    },
  });
  console.log('GeneralConfig created/updated with ticketMode: API');

  // =====================================================
  // CANALES GLOBALES
  // =====================================================
  for (const ch of defaultChannels) {
    await prisma.channel.upsert({
      where: { name: ch.name },
      update: {
        backgroundColor: ch.backgroundColor,
        textColor: ch.textColor,
        priority: ch.priority,
      },
      create: {
        name: ch.name,
        backgroundColor: ch.backgroundColor,
        textColor: ch.textColor,
        priority: ch.priority,
        active: true,
      },
    });
  }
  console.log(`${defaultChannels.length} global channels created`);

  // =====================================================
  // COLA LINEAS
  // =====================================================
  const queueLineas = await prisma.queue.upsert({
    where: { name: 'LINEAS' },
    update: {},
    create: {
      name: 'LINEAS',
      description: 'Cola principal de produccion',
      distribution: 'DISTRIBUTED',
      active: true,
    },
  });

  // Filtros para LINEAS (ocultar S.)
  await prisma.queueFilter.upsert({
    where: { queueId_pattern: { queueId: queueLineas.id, pattern: 'S.' } },
    update: {},
    create: {
      queueId: queueLineas.id,
      pattern: 'S.',
      suppress: true,
      active: true,
    },
  });

  // Canales para LINEAS
  for (const ch of queueChannelsData) {
    await prisma.queueChannel.upsert({
      where: { queueId_channel: { queueId: queueLineas.id, channel: ch.channel } },
      update: { color: ch.color, priority: ch.priority },
      create: {
        queueId: queueLineas.id,
        channel: ch.channel,
        color: ch.color,
        priority: ch.priority,
        active: true,
      },
    });
  }
  console.log('Queue LINEAS created with channels and filters');

  // =====================================================
  // COLA SANDUCHE
  // =====================================================
  const queueSanduche = await prisma.queue.upsert({
    where: { name: 'SANDUCHE' },
    update: {},
    create: {
      name: 'SANDUCHE',
      description: 'Cola de sanduches',
      distribution: 'SINGLE',
      active: true,
    },
  });

  // Filtros para SANDUCHE (mostrar S.)
  await prisma.queueFilter.upsert({
    where: { queueId_pattern: { queueId: queueSanduche.id, pattern: 'S.' } },
    update: {},
    create: {
      queueId: queueSanduche.id,
      pattern: 'S.',
      suppress: false,
      active: true,
    },
  });

  // Canales para SANDUCHE
  for (const ch of queueChannelsData) {
    await prisma.queueChannel.upsert({
      where: { queueId_channel: { queueId: queueSanduche.id, channel: ch.channel } },
      update: { color: ch.color, priority: ch.priority },
      create: {
        queueId: queueSanduche.id,
        channel: ch.channel,
        color: ch.color,
        priority: ch.priority,
        active: true,
      },
    });
  }
  console.log('Queue SANDUCHE created with channels and filters');

  // =====================================================
  // PANTALLAS (con nombres de impresoras para impresión centralizada)
  // =====================================================
  const screensConfig = [
    { name: 'Pantalla1', queueId: queueLineas.id, printerName: 'linea' },
    { name: 'Pantalla2', queueId: queueLineas.id, printerName: 'linea2' },
    { name: 'Pantalla3', queueId: queueSanduche.id, printerName: 'sanduches' },
  ];

  for (const screenConf of screensConfig) {
    const screen = await prisma.screen.upsert({
      where: { name: screenConf.name },
      update: {
        queueId: screenConf.queueId,
        printerName: screenConf.printerName, // Nombre de impresora para impresión centralizada
      },
      create: {
        name: screenConf.name,
        queueId: screenConf.queueId,
        printerName: screenConf.printerName,
        status: 'OFFLINE',
      },
    });

    // Crear Appearance
    const appearance = await prisma.appearance.upsert({
      where: { screenId: screen.id },
      update: {},
      create: {
        screenId: screen.id,
        ...baseAppearance,
      },
    });

    // Crear CardColors (SLA)
    for (const cc of cardColorsData) {
      await prisma.cardColor.upsert({
        where: { appearanceId_order: { appearanceId: appearance.id, order: cc.order } },
        update: { color: cc.color, quantityColor: cc.quantityColor, minutes: cc.minutes, isFullBackground: cc.isFullBackground },
        create: {
          appearanceId: appearance.id,
          ...cc,
        },
      });
    }

    // Crear ChannelColors
    for (const chc of channelColorsData) {
      await prisma.channelColor.upsert({
        where: { appearanceId_channel: { appearanceId: appearance.id, channel: chc.channel } },
        update: { color: chc.color, textColor: chc.textColor },
        create: {
          appearanceId: appearance.id,
          ...chc,
        },
      });
    }

    // Crear Preference
    await prisma.preference.upsert({
      where: { screenId: screen.id },
      update: {},
      create: {
        screenId: screen.id,
        ...basePreference,
      },
    });

    // Crear KeyboardConfig
    await prisma.keyboardConfig.upsert({
      where: { screenId: screen.id },
      update: {},
      create: {
        screenId: screen.id,
        ...baseKeyboard,
      },
    });

    console.log(`Screen ${screenConf.name} created (printer: ${screenConf.printerName}) with appearance, preferences, and keyboard config`);
  }

  console.log('Seeding completed!');
  console.log('  - Print Mode: CENTRALIZED');
  console.log('  - Print URL: ' + centralizedPrintConfig.centralizedPrintUrl);
  console.log('  - Printers: Pantalla1->linea, Pantalla2->linea2, Pantalla3->sanduches');
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
