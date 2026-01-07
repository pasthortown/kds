import { Request } from 'express';
import { z } from 'zod';

// ============================================
// AUTH TYPES
// ============================================

export interface JwtPayload {
  userId: string;
  email: string;
  role: 'ADMIN' | 'OPERATOR' | 'VIEWER';
}

export interface AuthenticatedRequest extends Request {
  user?: JwtPayload;
}

// ============================================
// SCREEN TYPES
// ============================================

export type ScreenStatus = 'ONLINE' | 'OFFLINE' | 'STANDBY';

export interface ScreenWithConfig {
  id: string;
  number: number;
  name: string;
  queueId: string;
  status: ScreenStatus;
  queue: {
    id: string;
    name: string;
    distribution: string;
  };
  appearance: AppearanceConfig | null;
  preference: PreferenceConfig | null;
  keyboard: KeyboardConfig | null;
  printer: PrinterConfig | null;
}

// ============================================
// APPEARANCE TYPES
// ============================================

export interface CardColorConfig {
  id: string;
  color: string;
  minutes: string;
  order: number;
  isFullBackground: boolean;
}

export interface ChannelColorConfig {
  channel: string;
  color: string;
}

export interface AppearanceConfig {
  fontSize: string;
  fontFamily: string;
  columnsPerScreen: number;
  columnSize: string;
  footerHeight: string;
  ordersDisplay: string;
  theme: string;
  screenName: string;
  screenSplit: boolean;
  showCounters: boolean;
  // Colores generales
  backgroundColor: string;
  headerColor: string;
  headerTextColor: string;
  cardColor: string;
  textColor: string;
  accentColor: string;
  // Header
  headerFontFamily: string;
  headerFontSize: string;
  headerFontWeight: string;
  headerFontStyle: string;
  headerBgColor: string;
  headerTextColorCustom: string;
  showHeader: boolean;
  headerShowChannel: boolean;
  headerShowTime: boolean;
  // Timer
  timerFontFamily: string;
  timerFontSize: string;
  timerFontWeight: string;
  timerFontStyle: string;
  timerTextColor: string;
  showTimer: boolean;
  // Cliente
  clientFontFamily: string;
  clientFontSize: string;
  clientFontWeight: string;
  clientFontStyle: string;
  clientTextColor: string;
  clientBgColor: string;
  showClient: boolean;
  // Cantidad
  quantityFontFamily: string;
  quantityFontSize: string;
  quantityFontWeight: string;
  quantityFontStyle: string;
  quantityTextColor: string;
  showQuantity: boolean;
  // Producto
  productFontFamily: string;
  productFontSize: string;
  productFontWeight: string;
  productFontStyle: string;
  productTextColor: string;
  productBgColor: string;
  productUppercase: boolean;
  // Subitem
  subitemFontFamily: string;
  subitemFontSize: string;
  subitemFontWeight: string;
  subitemFontStyle: string;
  subitemTextColor: string;
  subitemBgColor: string;
  subitemIndent: number;
  showSubitems: boolean;
  // Modificador
  modifierFontFamily: string;
  modifierFontSize: string;
  modifierFontWeight: string;
  modifierFontStyle: string;
  modifierFontColor: string;
  modifierBgColor: string;
  modifierIndent: number;
  showModifiers: boolean;
  // Notas especiales
  notesFontFamily: string;
  notesFontSize: string;
  notesFontWeight: string;
  notesFontStyle: string;
  notesTextColor: string;
  notesBgColor: string;
  notesIndent: number;
  showNotes: boolean;
  // Comentarios
  commentsFontFamily: string;
  commentsFontSize: string;
  commentsFontWeight: string;
  commentsFontStyle: string;
  commentsTextColor: string;
  commentsBgColor: string;
  commentsIndent: number;
  showComments: boolean;
  // Canal
  channelFontFamily: string;
  channelFontSize: string;
  channelFontWeight: string;
  channelFontStyle: string;
  channelTextColor: string;
  channelUppercase: boolean;
  showChannel: boolean;
  // Disposicion
  rows: number;
  maxItemsPerColumn: number;
  showOrderNumber: boolean;
  animationEnabled: boolean;
  cardColors: CardColorConfig[];
  channelColors: ChannelColorConfig[];
}

// ============================================
// PREFERENCE TYPES
// ============================================

export interface PreferenceConfig {
  finishOrderActive: boolean;
  finishOrderTime: string;
  showClientData: boolean;
  showName: boolean;
  showIdentifier: boolean;
  identifierMessage: string;
  showNumerator: boolean;
  showPagination: boolean;
  sourceBoxActive: boolean;
  sourceBoxMessage: string;
  touchEnabled: boolean;
  botoneraEnabled: boolean;
}

// ============================================
// KEYBOARD TYPES
// ============================================

export interface KeyboardConfig {
  finishFirstOrder: string;
  finishSecondOrder: string;
  finishThirdOrder: string;
  finishFourthOrder: string;
  finishFifthOrder: string;
  nextPage: string;
  previousPage: string;
  undo: string;
  resetTime: string;
  firstPage: string;
  secondPage: string;
  middlePage: string;
  penultimatePage: string;
  lastPage: string;
  confirmModal: string;
  cancelModal: string;
  power: string;
  exit: string;
  combos: ComboConfig[];
  debounceTime: number;
}

export interface ComboConfig {
  keys: string[];
  holdTime: number;
  action: string;
  enabled: boolean;
}

// ============================================
// PRINTER TYPES
// ============================================

export interface PrinterConfig {
  name: string;
  ip: string;
  port: number;
  enabled: boolean;
}

// ============================================
// ORDER TYPES
// ============================================

export interface OrderItem {
  id: string;
  name: string;
  quantity: number;
  notes?: string;
  modifier?: string;
  comments?: string;
}

export interface Order {
  id: string;
  externalId: string;
  posId?: string; // ID interno del POS (odp_id)
  screenId?: string;
  channel: string;
  channelType?: string; // SALON, LLEVAR, etc.
  customerName?: string;
  identifier: string;
  status: 'PENDING' | 'IN_PROGRESS' | 'FINISHED' | 'CANCELLED';
  createdAt: Date;
  finishedAt?: Date;
  items: OrderItem[];
  // Campos adicionales para impresión/visualización
  comments?: string;      // Comentarios adicionales de la orden
  templateHTML?: string;  // Plantilla HTML para renderizado
  valuesHTML?: string;    // Valores HTML para la plantilla
  statusPos?: string;     // Estado de la orden en el POS (ej: "TOMANDO PEDIDO", "PEDIDO TOMADO")
}

// ============================================
// QUEUE TYPES
// ============================================

export interface QueueChannel {
  channel: string;
  color: string;
  priority: number;
  active: boolean;
}

export interface QueueFilter {
  pattern: string;
  suppress: boolean;
  active: boolean;
}

export interface Queue {
  id: string;
  name: string;
  description?: string;
  distribution: 'DISTRIBUTED' | 'SINGLE';
  active: boolean;
  channels: QueueChannel[];
  filters: QueueFilter[];
}

// ============================================
// WEBSOCKET EVENTS
// ============================================

export interface WsScreenRegister {
  screenId: string;
  apiKey: string;
}

export interface WsHeartbeat {
  screenId: string;
  timestamp: number;
}

export interface WsOrdersUpdate {
  orders: Order[];
  totalInQueue: number;
  activeScreensInQueue: number;
}

export interface WsConfigUpdate {
  screenId: string;
  config: ScreenWithConfig;
}

export interface WsScreenStatus {
  screenId: string;
  status: ScreenStatus;
}

export interface WsOrderFinish {
  orderId: string;
  screenId: string;
  timestamp: number;
}

// ============================================
// ZOD SCHEMAS
// ============================================

export const loginSchema = z.object({
  email: z.string().email(),
  password: z.string().min(6),
});

export const createScreenSchema = z.object({
  name: z.string().min(1).max(50),
  queueId: z.string().cuid(),
});

export const updateScreenSchema = z.object({
  name: z.string().min(1).max(50).optional(),
  queueId: z.string().cuid().optional(),
});

export const createQueueSchema = z.object({
  name: z.string().min(1).max(50),
  description: z.string().optional(),
  distribution: z.enum(['DISTRIBUTED', 'SINGLE']).default('DISTRIBUTED'),
  channels: z.array(z.object({
    channel: z.string(),
    color: z.string().regex(/^#[0-9A-Fa-f]{6}$/),
    priority: z.number().int().min(0).default(0),
  })).optional(),
  filters: z.array(z.object({
    pattern: z.string(),
    suppress: z.boolean().default(false),
  })).optional(),
});

export const updateAppearanceSchema = z.object({
  fontSize: z.string().optional(),
  fontFamily: z.string().optional(),
  columnsPerScreen: z.number().int().min(1).max(10).optional(),
  columnSize: z.string().optional(),
  footerHeight: z.string().optional(),
  ordersDisplay: z.enum(['COLUMNS', 'ROWS']).optional(),
  theme: z.enum(['DARK', 'LIGHT']).optional(),
  screenName: z.string().optional(),
  screenSplit: z.boolean().optional(),
  showCounters: z.boolean().optional(),

  // Colores generales
  backgroundColor: z.string().optional(),
  headerColor: z.string().optional(),
  headerTextColor: z.string().optional(),
  cardColor: z.string().optional(),
  textColor: z.string().optional(),
  accentColor: z.string().optional(),

  // Tipografia header
  headerFontFamily: z.string().optional(),
  headerFontSize: z.string().optional(),
  headerFontWeight: z.string().optional(),
  headerFontStyle: z.string().optional(),
  headerBgColor: z.string().optional(),
  headerTextColorCustom: z.string().optional(),
  showHeader: z.boolean().optional(),
  headerShowChannel: z.boolean().optional(),
  headerShowTime: z.boolean().optional(),

  // Tipografia timer
  timerFontFamily: z.string().optional(),
  timerFontSize: z.string().optional(),
  timerFontWeight: z.string().optional(),
  timerFontStyle: z.string().optional(),
  timerTextColor: z.string().optional(),
  showTimer: z.boolean().optional(),

  // Tipografia cliente
  clientFontFamily: z.string().optional(),
  clientFontSize: z.string().optional(),
  clientFontWeight: z.string().optional(),
  clientFontStyle: z.string().optional(),
  clientTextColor: z.string().optional(),
  clientBgColor: z.string().optional(),
  showClient: z.boolean().optional(),

  // Tipografia cantidad
  quantityFontFamily: z.string().optional(),
  quantityFontSize: z.string().optional(),
  quantityFontWeight: z.string().optional(),
  quantityFontStyle: z.string().optional(),
  quantityTextColor: z.string().optional(),
  showQuantity: z.boolean().optional(),

  // Tipografia de productos
  productFontFamily: z.string().optional(),
  productFontSize: z.string().optional(),
  productFontWeight: z.string().optional(),
  productFontStyle: z.string().optional(),
  productTextColor: z.string().optional(),
  productBgColor: z.string().optional(),
  productUppercase: z.boolean().optional(),

  // Tipografia subitems
  subitemFontFamily: z.string().optional(),
  subitemFontSize: z.string().optional(),
  subitemFontWeight: z.string().optional(),
  subitemFontStyle: z.string().optional(),
  subitemTextColor: z.string().optional(),
  subitemBgColor: z.string().optional(),
  subitemIndent: z.number().int().min(0).max(100).optional(),
  showSubitems: z.boolean().optional(),

  // Tipografia de modificadores
  modifierFontFamily: z.string().optional(),
  modifierFontSize: z.string().optional(),
  modifierFontWeight: z.string().optional(),
  modifierFontStyle: z.string().optional(),
  modifierFontColor: z.string().optional(),
  modifierBgColor: z.string().optional(),
  modifierIndent: z.number().int().min(0).max(100).optional(),
  showModifiers: z.boolean().optional(),

  // Tipografia notas especiales
  notesFontFamily: z.string().optional(),
  notesFontSize: z.string().optional(),
  notesFontWeight: z.string().optional(),
  notesFontStyle: z.string().optional(),
  notesTextColor: z.string().optional(),
  notesBgColor: z.string().optional(),
  notesIndent: z.number().int().min(0).max(100).optional(),
  showNotes: z.boolean().optional(),

  // Tipografia comentarios
  commentsFontFamily: z.string().optional(),
  commentsFontSize: z.string().optional(),
  commentsFontWeight: z.string().optional(),
  commentsFontStyle: z.string().optional(),
  commentsTextColor: z.string().optional(),
  commentsBgColor: z.string().optional(),
  commentsIndent: z.number().int().min(0).max(100).optional(),
  showComments: z.boolean().optional(),

  // Tipografia canal/footer
  channelFontFamily: z.string().optional(),
  channelFontSize: z.string().optional(),
  channelFontWeight: z.string().optional(),
  channelFontStyle: z.string().optional(),
  channelTextColor: z.string().optional(),
  channelUppercase: z.boolean().optional(),
  showChannel: z.boolean().optional(),

  // Disposicion
  columns: z.number().int().min(1).max(10).optional(),
  rows: z.number().int().min(1).max(10).optional(),
  maxItemsPerColumn: z.number().int().min(1).max(20).optional(),

  // Opciones de visualizacion
  showOrderNumber: z.boolean().optional(),
  animationEnabled: z.boolean().optional(),

  cardColors: z.array(z.object({
    color: z.string().regex(/^#[0-9A-Fa-f]{6}$/),
    quantityColor: z.string().optional().default(''),
    minutes: z.string().regex(/^\d{2}:\d{2}$/),
    order: z.number().int().min(1).max(10),
    isFullBackground: z.boolean().default(false),
  })).optional(),
  channelColors: z.array(z.object({
    channel: z.string(),
    color: z.string().regex(/^#[0-9A-Fa-f]{6}$/),
  })).optional(),
});

export const updateKeyboardSchema = z.object({
  finishFirstOrder: z.string().max(1).optional(),
  finishSecondOrder: z.string().max(1).optional(),
  finishThirdOrder: z.string().max(1).optional(),
  finishFourthOrder: z.string().max(1).optional(),
  finishFifthOrder: z.string().max(1).optional(),
  nextPage: z.string().max(1).optional(),
  previousPage: z.string().max(1).optional(),
  undo: z.string().max(1).optional(),
  resetTime: z.string().max(1).optional(),
  firstPage: z.string().max(1).optional(),
  secondPage: z.string().max(1).optional(),
  middlePage: z.string().max(1).optional(),
  penultimatePage: z.string().max(1).optional(),
  lastPage: z.string().max(1).optional(),
  confirmModal: z.string().max(1).optional(),
  cancelModal: z.string().max(1).optional(),
  power: z.string().max(1).optional(),
  exit: z.string().max(1).optional(),
  combos: z.array(z.object({
    keys: z.array(z.string().max(1)),
    holdTime: z.number().int().min(500).max(10000),
    action: z.string(),
    enabled: z.boolean().default(true),
  })).optional(),
  debounceTime: z.number().int().min(50).max(500).optional(),
});
