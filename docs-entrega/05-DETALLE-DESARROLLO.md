# Detalle de Desarrollo - Sistema KDS v2.0

## Especificación Técnica de Componentes Desarrollados

---

## 1. Resumen del Proyecto

### 1.1 Información General

| Aspecto | Detalle |
|---------|---------|
| **Nombre** | KDS - Kitchen Display System |
| **Versión** | 2.0.0 |
| **Tipo** | Sistema de visualización de órdenes para cocinas |
| **Stack** | Node.js + React + PostgreSQL + Redis |
| **Arquitectura** | Monorepo con 3 aplicaciones |

### 1.2 Métricas del Código

| Componente | Archivos | Líneas de Código (aprox) |
|------------|----------|--------------------------|
| Backend | ~40 | ~5,000 |
| KDS Frontend | ~30 | ~3,500 |
| Backoffice | ~25 | ~4,000 |
| Infraestructura | ~15 | ~500 |
| **Total** | **~110** | **~13,000** |

---

## 2. Backend (Node.js/Express)

### 2.1 Estructura de Archivos

```
backend/
├── src/
│   ├── config/
│   │   ├── database.ts          # Conexión Prisma PostgreSQL
│   │   ├── redis.ts             # Cliente Redis + Pub/Sub
│   │   ├── env.ts               # Validación variables entorno
│   │   └── mxp.ts               # Conexión SQL Server MAXPOINT
│   │
│   ├── controllers/
│   │   ├── auth.controller.ts   # Login, refresh, me
│   │   ├── user.controller.ts   # CRUD usuarios
│   │   ├── screen.controller.ts # CRUD pantallas + config
│   │   ├── queue.controller.ts  # CRUD colas + canales
│   │   ├── order.controller.ts  # CRUD órdenes + stats
│   │   ├── config.controller.ts # Config general + polling
│   │   └── mirror.controller.ts # KDS remoto (mirror)
│   │
│   ├── services/
│   │   ├── auth.service.ts      # JWT, bcrypt (193 líneas)
│   │   ├── order.service.ts     # Lógica órdenes (656 líneas)
│   │   ├── screen.service.ts    # Lógica pantallas (362 líneas)
│   │   ├── queue.service.ts     # Lógica colas
│   │   ├── balancer.service.ts  # Round-Robin (411 líneas)
│   │   ├── websocket.service.ts # Socket.IO (385 líneas)
│   │   ├── polling.service.ts   # MAXPOINT polling (163 líneas)
│   │   ├── printer.service.ts   # Impresión TCP (236 líneas)
│   │   ├── centralized-printer.service.ts # Impresión HTTP (292 líneas)
│   │   ├── mxp.service.ts       # SQL Server queries (132 líneas)
│   │   ├── api-ticket.service.ts # Legacy API (335 líneas)
│   │   └── mirror-kds.service.ts # Replicación (412 líneas)
│   │
│   ├── middlewares/
│   │   ├── auth.middleware.ts   # JWT validation
│   │   └── error.middleware.ts  # Error handler global
│   │
│   ├── routes/
│   │   └── index.ts             # Definición de todas las rutas
│   │
│   ├── types/
│   │   └── index.ts             # Tipos TS + Zod schemas
│   │
│   ├── utils/
│   │   └── logger.ts            # Winston logger config
│   │
│   └── index.ts                 # Entry point Express
│
├── prisma/
│   ├── schema.prisma            # Modelo de datos completo
│   ├── seed.ts                  # Datos iniciales
│   └── migrations/              # Migraciones de BD
│
├── package.json
├── tsconfig.json
└── .env.example
```

### 2.2 Servicios Desarrollados

#### 2.2.1 AuthService (`auth.service.ts`)
**Líneas**: 193

**Funcionalidades**:
- Login con email/password
- Generación de JWT access token (15m)
- Generación de JWT refresh token (7d)
- Refresh de tokens
- Validación de tokens
- Hash de contraseñas con bcrypt

**Métodos principales**:
```typescript
async login(email: string, password: string): Promise<AuthResponse>
async refreshToken(refreshToken: string): Promise<TokenPair>
async validateToken(token: string): Promise<User>
async hashPassword(password: string): Promise<string>
async comparePassword(password: string, hash: string): Promise<boolean>
```

#### 2.2.2 OrderService (`order.service.ts`)
**Líneas**: 656

**Funcionalidades**:
- CRUD completo de órdenes
- Gestión de estados (PENDING → IN_PROGRESS → FINISHED)
- Finalización y deshacer
- Estadísticas en tiempo real
- Limpieza automática de órdenes antiguas
- Generación de órdenes de prueba

**Métodos principales**:
```typescript
async findAll(filters: OrderFilters): Promise<Order[]>
async findByScreen(screenId: string): Promise<Order[]>
async create(data: CreateOrderDto): Promise<Order>
async upsertFromMaxpoint(orders: MaxpointOrder[]): Promise<Order[]>
async finish(orderId: string): Promise<Order>
async undo(orderId: string): Promise<Order>
async cancel(orderId: string): Promise<Order>
async getStats(): Promise<OrderStats>
async getDashboardStats(): Promise<DashboardStats>
async cleanup(olderThanHours: number): Promise<number>
async generateTestOrders(screenId: string, count: number): Promise<Order[]>
```

#### 2.2.3 ScreenService (`screen.service.ts`)
**Líneas**: 362

**Funcionalidades**:
- CRUD de pantallas
- Configuración de apariencia (50+ campos)
- Configuración de preferencias
- Configuración de teclado/botonera
- Gestión de impresoras
- Heartbeat monitoring
- Activación/desactivación standby

**Métodos principales**:
```typescript
async findAll(): Promise<Screen[]>
async findById(id: string): Promise<Screen>
async findByNumber(number: number): Promise<Screen>
async create(data: CreateScreenDto): Promise<Screen>
async update(id: string, data: UpdateScreenDto): Promise<Screen>
async updateAppearance(id: string, data: AppearanceDto): Promise<Appearance>
async updatePreference(id: string, data: PreferenceDto): Promise<Preference>
async updateKeyboard(id: string, data: KeyboardDto): Promise<KeyboardConfig>
async setPrinter(id: string, data: PrinterDto): Promise<Printer>
async setStandby(id: string): Promise<Screen>
async activate(id: string): Promise<Screen>
async getFullConfig(id: string): Promise<ScreenConfig>
async recordHeartbeat(id: string): Promise<void>
async checkOfflineScreens(): Promise<Screen[]>
```

#### 2.2.4 BalancerService (`balancer.service.ts`)
**Líneas**: 411

**Funcionalidades**:
- Distribución Round-Robin de órdenes
- Caché de índices en Redis
- Filtrado por canal
- Filtrado por patrón de producto
- Fallback a pantalla única
- Reset de balance

**Métodos principales**:
```typescript
async distributeOrder(order: Order, queue: Queue): Promise<Screen | null>
async distributeOrders(orders: Order[]): Promise<Map<string, Order[]>>
async getNextScreen(queueId: string, screens: Screen[]): Promise<Screen>
async resetBalance(queueId: string): Promise<void>
async filterByChannel(order: Order, queue: Queue): Promise<boolean>
async filterByProduct(order: Order, queue: Queue): Promise<Order>
```

#### 2.2.5 WebSocketService (`websocket.service.ts`)
**Líneas**: 385

**Funcionalidades**:
- Servidor Socket.IO
- Autenticación por token
- Registro de pantallas
- Heartbeat de pantallas
- Distribución de órdenes en tiempo real
- Broadcast de configuración
- Pub/Sub con Redis para escalabilidad

**Eventos manejados**:
```typescript
// Cliente → Servidor
'screen:register'    // Registrar pantalla
'screen:heartbeat'   // Ping de vida
'order:finish'       // Finalizar orden
'order:undo'         // Deshacer

// Servidor → Cliente
'screen:orders:update'   // Órdenes actualizadas
'screen:configUpdated'   // Config modificada
'screen:statusChanged'   // Cambio de estado
'order:finished'         // Confirmación finalización
'order:undone'           // Confirmación undo
```

#### 2.2.6 PollingService (`polling.service.ts`)
**Líneas**: 163

**Funcionalidades**:
- Polling periódico a MAXPOINT SQL Server
- Intervalo configurable (default 2000ms)
- Deduplicación automática
- Auto-cleanup de procesados
- Control start/stop
- Forzar ciclo manual

**Métodos principales**:
```typescript
async start(intervalMs?: number): Promise<void>
async stop(): Promise<void>
async poll(): Promise<Order[]>
async forcePoll(): Promise<Order[]>
getStatus(): PollingStatus
```

#### 2.2.7 PrinterService (`printer.service.ts`)
**Líneas**: 236

**Funcionalidades**:
- Impresión TCP directa (puerto 9100)
- Formato ESC/POS
- Reintentos automáticos
- Test de conexión

**Métodos principales**:
```typescript
async print(printer: Printer, order: Order): Promise<boolean>
async testConnection(ip: string, port: number): Promise<boolean>
formatTicket(order: Order): Buffer
```

#### 2.2.8 MirrorKdsService (`mirror-kds.service.ts`)
**Líneas**: 412

**Funcionalidades**:
- Replicación read-only de KDS remota
- Sincronización de órdenes
- Sincronización de pantallas
- Sincronización de colas
- Reconexión automática
- Estadísticas de mirror

**Métodos principales**:
```typescript
async configure(remoteUrl: string, apiKey: string): Promise<void>
async disconnect(): Promise<void>
async testConnection(): Promise<boolean>
async syncOrders(): Promise<Order[]>
async syncScreens(): Promise<Screen[]>
getStats(): MirrorStats
```

### 2.3 Middlewares

#### AuthMiddleware (`auth.middleware.ts`)
```typescript
// Validación JWT
export const authenticate = async (req, res, next) => {
  const token = req.headers.authorization?.replace('Bearer ', '');
  const user = await authService.validateToken(token);
  req.user = user;
  next();
};

// Autorización por rol
export const authorize = (...roles: UserRole[]) => {
  return (req, res, next) => {
    if (!roles.includes(req.user.role)) {
      throw new ForbiddenError();
    }
    next();
  };
};
```

#### ErrorMiddleware (`error.middleware.ts`)
```typescript
export const errorHandler = (err, req, res, next) => {
  logger.error(err);

  if (err instanceof ValidationError) {
    return res.status(400).json({ error: err.message, details: err.details });
  }

  if (err instanceof UnauthorizedError) {
    return res.status(401).json({ error: 'No autorizado' });
  }

  return res.status(500).json({ error: 'Error interno del servidor' });
};
```

### 2.4 Validación con Zod

```typescript
// types/index.ts
export const createScreenSchema = z.object({
  name: z.string().min(1).max(100),
  queueId: z.string().cuid(),
});

export const updateAppearanceSchema = z.object({
  theme: z.enum(['DARK', 'LIGHT']).optional(),
  columnsPerScreen: z.number().min(1).max(10).optional(),
  backgroundColor: z.string().regex(/^#[0-9A-Fa-f]{6}$/).optional(),
  // ... 50+ campos más
});

export const orderFiltersSchema = z.object({
  status: z.enum(['PENDING', 'IN_PROGRESS', 'FINISHED', 'CANCELLED']).optional(),
  screenId: z.string().cuid().optional(),
  channel: z.string().optional(),
  from: z.coerce.date().optional(),
  to: z.coerce.date().optional(),
});
```

---

## 3. KDS Frontend (React)

### 3.1 Estructura de Archivos

```
kds-frontend/
├── src/
│   ├── components/
│   │   ├── Header/
│   │   │   └── Header.tsx       # Encabezado con número y cola
│   │   ├── OrderCard/
│   │   │   ├── OrderCard.tsx    # Tarjeta de orden
│   │   │   └── OrderCard.css    # Estilos dinámicos
│   │   ├── OrderGrid/
│   │   │   └── OrderGrid.tsx    # Grid paginado
│   │   ├── Footer/
│   │   │   └── Footer.tsx       # Contador de órdenes
│   │   ├── StandbyScreen/
│   │   │   └── StandbyScreen.tsx # Pantalla standby
│   │   └── TestModePanel/
│   │       └── TestModePanel.tsx # Panel de pruebas
│   │
│   ├── hooks/
│   │   ├── useWebSocket.ts      # Conexión Socket.IO
│   │   ├── useKeyboard.ts       # Manejo botonera
│   │   └── useScreenSize.ts     # Responsive detection
│   │
│   ├── store/
│   │   ├── configStore.ts       # Config y apariencia
│   │   ├── orderStore.ts        # Estado de órdenes
│   │   ├── screenStore.ts       # Estado pantalla
│   │   └── testModeStore.ts     # Modo prueba
│   │
│   ├── services/
│   │   ├── socket.ts            # Instancia Socket.IO
│   │   └── api.ts               # Cliente HTTP
│   │
│   ├── utils/
│   │   ├── buttonController.ts  # Lógica botonera
│   │   ├── timeUtils.ts         # Cálculo colores SLA
│   │   └── ticketPdfGenerator.ts # Generador PDF
│   │
│   ├── types/
│   │   └── index.ts             # Interfaces TypeScript
│   │
│   ├── App.tsx                  # Router + selector pantallas
│   ├── main.tsx                 # Entry point
│   └── index.css                # Estilos globales TailwindCSS
│
├── package.json
├── vite.config.ts
├── tailwind.config.js
└── tsconfig.json
```

### 3.2 Componentes Desarrollados

#### 3.2.1 OrderCard (`OrderCard.tsx`)

**Funcionalidades**:
- Visualización de orden individual
- Timer en tiempo real
- Colores dinámicos según SLA
- Badge de canal (LOCAL, LLEVAR, etc.)
- Lista de productos con subitems
- Modificadores y notas
- Soporte táctil (opcional)

**Props**:
```typescript
interface OrderCardProps {
  order: Order;
  position: number;
  appearance: Appearance;
  onFinish: (orderId: string) => void;
  touchEnabled: boolean;
}
```

**Estilos dinámicos**:
```typescript
// Calcula color según tiempo transcurrido
const getBackgroundColor = (createdAt: Date, cardColors: CardColor[]) => {
  const elapsed = getElapsedMinutes(createdAt);

  for (const colorConfig of cardColors.sort((a, b) => a.order - b.order)) {
    const [mins, secs] = colorConfig.minutes.split(':').map(Number);
    const threshold = mins + secs / 60;

    if (elapsed <= threshold) {
      return colorConfig.color;
    }
  }

  return cardColors[cardColors.length - 1]?.color || '#ff0000';
};
```

#### 3.2.2 OrderGrid (`OrderGrid.tsx`)

**Funcionalidades**:
- Grid responsivo de tarjetas
- Paginación automática
- Navegación con teclado
- Animaciones de entrada/salida

**Props**:
```typescript
interface OrderGridProps {
  orders: Order[];
  columns: number;
  rows: number;
  currentPage: number;
  appearance: Appearance;
  onFinish: (orderId: string) => void;
}
```

#### 3.2.3 StandbyScreen (`StandbyScreen.tsx`)

**Funcionalidades**:
- Pantalla negra completa
- Texto "STANDBY" centrado
- Desactivación con cualquier input

#### 3.2.4 TestModePanel (`TestModePanel.tsx`)

**Funcionalidades**:
- Panel flotante en esquina
- Generador de órdenes de prueba
- Selector de pantalla destino
- Limpiador de órdenes test
- Indicador de conexión

### 3.3 Hooks Desarrollados

#### 3.3.1 useWebSocket (`useWebSocket.ts`)

```typescript
export function useWebSocket(screenId: string | null) {
  const { setOrders, addOrder, removeOrder } = useOrderStore();
  const { setConfig } = useConfigStore();
  const [isConnected, setIsConnected] = useState(false);

  useEffect(() => {
    if (!screenId) return;

    // Conectar
    socket.connect();

    // Registrar pantalla
    socket.emit('screen:register', { screenId });

    // Listeners
    socket.on('connect', () => setIsConnected(true));
    socket.on('disconnect', () => setIsConnected(false));
    socket.on('screen:orders:update', setOrders);
    socket.on('screen:configUpdated', setConfig);

    // Heartbeat
    const interval = setInterval(() => {
      socket.emit('screen:heartbeat', { screenId });
    }, 10000);

    return () => {
      clearInterval(interval);
      socket.disconnect();
    };
  }, [screenId]);

  return { isConnected, socket };
}
```

#### 3.3.2 useKeyboard (`useKeyboard.ts`)

```typescript
export function useKeyboard(config: KeyboardConfig) {
  const [pressedKeys, setPressedKeys] = useState<Set<string>>(new Set());
  const [comboTimers, setComboTimers] = useState<Map<string, NodeJS.Timeout>>(new Map());

  const { orders, finishOrder } = useOrderStore();
  const { setStandby } = useScreenStore();
  const { currentPage, setPage } = useConfigStore();

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      const key = e.key.toLowerCase();

      // Debounce
      if (Date.now() - lastKeyTime < config.debounceTime) return;

      // Agregar a pressed
      setPressedKeys(prev => new Set(prev).add(key));

      // Verificar combos
      checkCombos(key);

      // Acciones simples
      handleSimpleKey(key);
    };

    const handleKeyUp = (e: KeyboardEvent) => {
      const key = e.key.toLowerCase();
      setPressedKeys(prev => {
        const next = new Set(prev);
        next.delete(key);
        return next;
      });

      // Cancelar combo si se suelta tecla
      cancelCombo(key);
    };

    window.addEventListener('keydown', handleKeyDown);
    window.addEventListener('keyup', handleKeyUp);

    return () => {
      window.removeEventListener('keydown', handleKeyDown);
      window.removeEventListener('keyup', handleKeyUp);
    };
  }, [config, orders, currentPage]);

  const handleSimpleKey = (key: string) => {
    // Finalizar órdenes por posición
    if (key === config.finishFirstOrder) finishOrderByPosition(0);
    if (key === config.finishSecondOrder) finishOrderByPosition(1);
    // ... etc

    // Navegación
    if (key === config.nextPage) setPage(currentPage + 1);
    if (key === config.previousPage) setPage(currentPage - 1);

    // Acciones especiales
    if (key === config.undo) undoLastAction();
  };

  const checkCombos = (key: string) => {
    for (const combo of config.combos) {
      if (combo.keys.every(k => pressedKeys.has(k) || k === key)) {
        // Iniciar timer para holdTime
        const timer = setTimeout(() => {
          executeComboAction(combo.action);
        }, combo.holdTime);

        setComboTimers(prev => new Map(prev).set(combo.action, timer));
      }
    }
  };

  const executeComboAction = (action: string) => {
    if (action === 'standby') {
      setStandby(true);
    }
    // ... otras acciones
  };

  return { pressedKeys };
}
```

### 3.4 Stores (Zustand)

#### 3.4.1 ConfigStore (`configStore.ts`)

```typescript
interface ConfigState {
  appearance: Appearance | null;
  preference: Preference | null;
  keyboard: KeyboardConfig | null;
  currentPage: number;
  totalPages: number;

  setConfig: (config: ScreenConfig) => void;
  setPage: (page: number) => void;
  setTotalPages: (total: number) => void;
}

export const useConfigStore = create<ConfigState>((set) => ({
  appearance: null,
  preference: null,
  keyboard: null,
  currentPage: 1,
  totalPages: 1,

  setConfig: (config) => set({
    appearance: config.appearance,
    preference: config.preference,
    keyboard: config.keyboard,
  }),

  setPage: (page) => set({ currentPage: page }),
  setTotalPages: (total) => set({ totalPages: total }),
}));
```

#### 3.4.2 OrderStore (`orderStore.ts`)

```typescript
interface OrderState {
  orders: Order[];
  finishedHistory: Order[];  // Para undo

  setOrders: (orders: Order[]) => void;
  addOrder: (order: Order) => void;
  finishOrder: (orderId: string) => void;
  undoLastFinish: () => Order | null;
  clearOrders: () => void;
}

export const useOrderStore = create<OrderState>((set, get) => ({
  orders: [],
  finishedHistory: [],

  setOrders: (orders) => set({ orders }),

  addOrder: (order) => set((state) => ({
    orders: [...state.orders, order],
  })),

  finishOrder: (orderId) => set((state) => {
    const order = state.orders.find(o => o.id === orderId);
    return {
      orders: state.orders.filter(o => o.id !== orderId),
      finishedHistory: order
        ? [order, ...state.finishedHistory].slice(0, 10)
        : state.finishedHistory,
    };
  }),

  undoLastFinish: () => {
    const { finishedHistory } = get();
    if (finishedHistory.length === 0) return null;

    const [lastFinished, ...rest] = finishedHistory;
    set((state) => ({
      orders: [lastFinished, ...state.orders],
      finishedHistory: rest,
    }));
    return lastFinished;
  },

  clearOrders: () => set({ orders: [], finishedHistory: [] }),
}));
```

### 3.5 Utilidades

#### TimeUtils (`timeUtils.ts`)

```typescript
// Formatear tiempo transcurrido
export function formatElapsedTime(createdAt: Date): string {
  const now = new Date();
  const elapsed = Math.floor((now.getTime() - createdAt.getTime()) / 1000);

  const minutes = Math.floor(elapsed / 60);
  const seconds = elapsed % 60;

  return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

// Obtener minutos transcurridos
export function getElapsedMinutes(createdAt: Date): number {
  const now = new Date();
  return (now.getTime() - createdAt.getTime()) / 60000;
}

// Obtener color según SLA
export function getSLAColor(
  createdAt: Date,
  cardColors: CardColor[]
): string {
  const elapsed = getElapsedMinutes(createdAt);
  const sortedColors = [...cardColors].sort((a, b) => a.order - b.order);

  for (const config of sortedColors) {
    const [mins, secs] = config.minutes.split(':').map(Number);
    const threshold = mins + secs / 60;

    if (elapsed <= threshold) {
      return config.color;
    }
  }

  return sortedColors[sortedColors.length - 1]?.color || '#ff0000';
}
```

---

## 4. Backoffice (React + Ant Design)

### 4.1 Estructura de Archivos

```
backoffice/
├── src/
│   ├── components/
│   │   ├── Layout.tsx           # Layout con menú lateral
│   │   └── ScreenPreview.tsx    # Preview de pantalla KDS
│   │
│   ├── pages/
│   │   ├── Login.tsx            # Página de login
│   │   ├── Dashboard.tsx        # KPIs y gráficos
│   │   ├── Screens.tsx          # CRUD pantallas
│   │   ├── Queues.tsx           # CRUD colas
│   │   ├── Orders.tsx           # Listado órdenes
│   │   ├── Appearance.tsx       # Editor visual
│   │   ├── SLA.tsx              # Config tiempos/colores
│   │   ├── Settings.tsx         # Config general
│   │   ├── Users.tsx            # CRUD usuarios
│   │   └── TestScreen.tsx       # Preview fullscreen
│   │
│   ├── services/
│   │   └── api.ts               # Cliente HTTP Axios
│   │
│   ├── store/
│   │   ├── authStore.ts         # Autenticación
│   │   └── testModeStore.ts     # Modo prueba
│   │
│   ├── utils/
│   │   └── pdfReport.ts         # Generación reportes
│   │
│   ├── App.tsx                  # Router principal
│   ├── main.tsx                 # Entry point
│   └── index.css                # Estilos globales
│
├── package.json
├── vite.config.ts
└── tsconfig.json
```

### 4.2 Páginas Desarrolladas

#### 4.2.1 Dashboard (`Dashboard.tsx`)

**Funcionalidades**:
- KPIs en tiempo real (órdenes hoy, tiempo promedio, pantallas online)
- Gráfico de órdenes por hora (Chart.js)
- Gráfico de tiempo promedio
- Gráfico de distribución por canal
- Lista de pantallas con estado
- Alertas de pantallas offline

**Componentes Ant Design utilizados**:
- Card, Row, Col (layout)
- Statistic (KPIs)
- Table (pantallas)
- Badge (estados)
- Alert (notificaciones)

#### 4.2.2 Screens (`Screens.tsx`)

**Funcionalidades**:
- Tabla de pantallas con filtros
- Modal de creación
- Modal de edición
- Configuración de apariencia (sub-modal)
- Configuración de preferencias
- Configuración de teclado
- Configuración de impresora
- Acciones: standby, activar, eliminar
- Regenerar API key

**Formularios**:
```typescript
// Crear/Editar pantalla
interface ScreenForm {
  name: string;
  queueId: string;
}

// Apariencia (simplificado, tiene 50+ campos)
interface AppearanceForm {
  theme: 'DARK' | 'LIGHT';
  columnsPerScreen: number;
  rows: number;
  backgroundColor: string;
  // ... más campos
}

// Preferencias
interface PreferenceForm {
  showClientData: boolean;
  touchEnabled: boolean;
  botoneraEnabled: boolean;
  // ... más campos
}

// Teclado
interface KeyboardForm {
  finishFirstOrder: string;
  nextPage: string;
  // ... más campos
}
```

#### 4.2.3 Queues (`Queues.tsx`)

**Funcionalidades**:
- Tabla de colas
- CRUD colas
- Gestión de canales por cola
- Gestión de filtros (suppressors)
- Configuración de distribución

#### 4.2.4 Orders (`Orders.tsx`)

**Funcionalidades**:
- Tabla de órdenes con paginación
- Filtros: estado, pantalla, canal, fecha
- Búsqueda por número
- Ver detalle de orden
- Cancelar orden
- Exportar a PDF
- Estadísticas

#### 4.2.5 Appearance (`Appearance.tsx`)

**Funcionalidades**:
- Selector de pantalla
- Editor de colores con ColorPicker
- Editor de tipografías
- Editor de layout
- Preview en vivo
- Guardar/resetear configuración

**Secciones del editor**:
1. Layout (columnas, filas, tamaños)
2. Tema (dark/light, colores base)
3. Header (tipografía, colores)
4. Timer (tipografía, colores)
5. Cliente (tipografía, colores)
6. Productos (tipografía, colores)
7. Subitems (tipografía, indentación)
8. Modificadores (tipografía)
9. Notas (tipografía)
10. Canal/Footer (tipografía)

#### 4.2.6 SLA (`SLA.tsx`)

**Funcionalidades**:
- Selector de pantalla
- Timeline visual de intervalos
- Configuración de colores por tiempo
- Agregar/eliminar intervalos
- Preview de cambios
- Aplicar a todas las pantallas

#### 4.2.7 Settings (`Settings.tsx`)

**Funcionalidades**:
- Configuración MAXPOINT
  - Host, puerto, usuario, contraseña, base de datos
  - Test de conexión
- Modos de operación
  - Ticket mode: POLLING / API
  - Print mode: LOCAL / CENTRALIZED
  - Test mode: on/off
- Control de polling
  - Iniciar/detener
  - Intervalo
  - Forzar ciclo

#### 4.2.8 Users (`Users.tsx`)

**Funcionalidades**:
- Tabla de usuarios
- Crear usuario (nombre, email, password, rol)
- Editar usuario
- Cambiar contraseña
- Activar/desactivar
- Eliminar usuario

### 4.3 Servicios

#### API Service (`api.ts`)

```typescript
import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
});

// Interceptor para agregar token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('access_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Interceptor para refresh token
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      const refreshToken = localStorage.getItem('refresh_token');
      if (refreshToken) {
        const { data } = await api.post('/auth/refresh', { refresh_token: refreshToken });
        localStorage.setItem('access_token', data.access_token);
        error.config.headers.Authorization = `Bearer ${data.access_token}`;
        return api(error.config);
      }
    }
    return Promise.reject(error);
  }
);

// Exports
export const authApi = {
  login: (email: string, password: string) =>
    api.post('/auth/login', { email, password }),
  refresh: (refreshToken: string) =>
    api.post('/auth/refresh', { refresh_token: refreshToken }),
  me: () => api.get('/auth/me'),
};

export const screensApi = {
  list: () => api.get('/screens'),
  get: (id: string) => api.get(`/screens/${id}`),
  create: (data: any) => api.post('/screens', data),
  update: (id: string, data: any) => api.put(`/screens/${id}`, data),
  delete: (id: string) => api.delete(`/screens/${id}`),
  updateAppearance: (id: string, data: any) => api.put(`/screens/${id}/appearance`, data),
  // ... más métodos
};

export const ordersApi = {
  list: (params?: any) => api.get('/orders', { params }),
  finish: (id: string) => api.post(`/orders/${id}/finish`),
  cancel: (id: string) => api.post(`/orders/${id}/cancel`),
  stats: () => api.get('/orders/stats'),
  generateTest: (screenId: string) => api.post('/orders/generate-test', { screenId }),
};

// ... más APIs
```

### 4.4 Componente ScreenPreview

```typescript
// components/ScreenPreview.tsx
interface ScreenPreviewProps {
  appearance: Appearance;
  orders?: Order[];
  scale?: number;
}

export function ScreenPreview({ appearance, orders = [], scale = 0.5 }: ScreenPreviewProps) {
  const mockOrders = orders.length > 0 ? orders : generateMockOrders();

  return (
    <div
      style={{
        transform: `scale(${scale})`,
        transformOrigin: 'top left',
        width: `${100 / scale}%`,
        height: `${100 / scale}%`,
      }}
    >
      <div
        style={{
          backgroundColor: appearance.backgroundColor,
          display: 'grid',
          gridTemplateColumns: `repeat(${appearance.columnsPerScreen}, ${appearance.columnSize})`,
          gap: '16px',
          padding: '16px',
        }}
      >
        {mockOrders.slice(0, appearance.columnsPerScreen * appearance.rows).map((order, i) => (
          <PreviewCard
            key={i}
            order={order}
            appearance={appearance}
          />
        ))}
      </div>
    </div>
  );
}
```

---

## 5. Infraestructura

### 5.1 Docker Compose de Producción

```yaml
# infra/docker-compose.yml
version: '3.8'

services:
  postgres:
    image: postgres:15-alpine
    container_name: kds-postgres
    environment:
      POSTGRES_USER: ${POSTGRES_USER}
      POSTGRES_PASSWORD: ${POSTGRES_PASSWORD}
      POSTGRES_DB: ${POSTGRES_DB}
    volumes:
      - postgres_data:/var/lib/postgresql/data
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U ${POSTGRES_USER}"]
      interval: 10s
      timeout: 5s
      retries: 5

  redis:
    image: redis:7-alpine
    container_name: kds-redis
    command: redis-server --appendonly yes --requirepass ${REDIS_PASSWORD}
    volumes:
      - redis_data:/data
    healthcheck:
      test: ["CMD", "redis-cli", "-a", "${REDIS_PASSWORD}", "ping"]
      interval: 10s

  backend:
    build:
      context: ..
      dockerfile: infra/Dockerfile.backend
    container_name: kds-backend
    depends_on:
      postgres:
        condition: service_healthy
      redis:
        condition: service_healthy
    environment:
      DATABASE_URL: postgresql://${POSTGRES_USER}:${POSTGRES_PASSWORD}@postgres:5432/${POSTGRES_DB}
      REDIS_URL: redis://:${REDIS_PASSWORD}@redis:6379
      JWT_SECRET: ${JWT_SECRET}
      # ... más variables
    ports:
      - "${BACKEND_PORT:-3000}:3000"

  kds-frontend:
    build:
      context: ..
      dockerfile: infra/Dockerfile.kds-frontend
    container_name: kds-frontend
    depends_on:
      - backend
    ports:
      - "${KDS_FRONTEND_PORT:-8080}:80"

  backoffice:
    build:
      context: ..
      dockerfile: infra/Dockerfile.backoffice
    container_name: kds-backoffice
    depends_on:
      - backend
    ports:
      - "${BACKOFFICE_PORT:-8081}:80"

volumes:
  postgres_data:
  redis_data:

networks:
  default:
    name: kds-network
```

### 5.2 Dockerfile Backend (Multi-stage)

```dockerfile
# infra/Dockerfile.backend
FROM node:20-alpine AS builder
WORKDIR /app
COPY backend/package*.json ./
RUN npm ci
COPY backend/ .
RUN npx prisma generate
RUN npm run build

FROM node:20-alpine AS runner
RUN addgroup -g 1001 -S kds && adduser -S kds -u 1001
WORKDIR /app
COPY --from=builder /app/dist ./dist
COPY --from=builder /app/node_modules ./node_modules
COPY --from=builder /app/prisma ./prisma
COPY --from=builder /app/package.json ./
USER kds
EXPOSE 3000
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s \
  CMD wget --no-verbose --tries=1 --spider http://localhost:3000/api/health || exit 1
CMD ["node", "dist/index.js"]
```

### 5.3 Dockerfile Frontend (Vite + Nginx)

```dockerfile
# infra/Dockerfile.kds-frontend
FROM node:20-alpine AS builder
WORKDIR /app
COPY kds-frontend/package*.json ./
RUN npm ci
COPY kds-frontend/ .
ARG VITE_API_URL=/api
ARG VITE_WS_URL=
ENV VITE_API_URL=$VITE_API_URL
ENV VITE_WS_URL=$VITE_WS_URL
RUN npm run build

FROM nginx:alpine AS runner
COPY --from=builder /app/dist /usr/share/nginx/html
COPY infra/nginx/kds-frontend.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
```

---

## 6. Funcionalidades Especiales

### 6.1 Modo de Prueba (Sandbox)

Permite probar el sistema sin afectar órdenes reales.

**Activación**:
1. Backoffice → Settings → Modos → Test Mode ON
2. En pantalla KDS aparece panel flotante

**Características**:
- Genera órdenes de prueba con datos aleatorios
- Las órdenes test tienen flag `isTest: true`
- Se pueden limpiar todas las órdenes test
- El polling de MAXPOINT sigue funcionando pero en modo "preview"

### 6.2 Botonera Física con Combos

Soporte para combinaciones de teclas mantenidas:

```typescript
// Configuración por defecto
const defaultCombos = [
  {
    keys: ['i', 'g'],
    holdTime: 3000,  // 3 segundos
    action: 'standby'
  }
];

// Uso: mantener I y G durante 3 segundos activa standby
```

### 6.3 Mirror KDS (Replicación)

Permite ver órdenes de una KDS remota en modo solo lectura.

**Casos de uso**:
- Supervisión centralizada
- Pantallas de gerencia
- Monitoreo multi-local

**Configuración**:
```typescript
// POST /api/mirror/configure
{
  "remoteUrl": "http://kds-remota:3000",
  "apiKey": "api_key_de_la_kds_remota"
}
```

### 6.4 Impresión Centralizada

Para locales con servidor de impresión central:

**Configuración**:
```bash
# .env
PRINT_MODE=CENTRALIZED
CENTRALIZED_PRINT_URL=http://servidor-impresion:5000
```

**Flujo**:
1. Orden se finaliza
2. Backend envía POST al servidor de impresión
3. Servidor de impresión envía a impresora física

---

## 7. Pendientes y Mejoras Futuras

### 7.1 Funcionalidades Pendientes

| Funcionalidad | Prioridad | Descripción |
|---------------|-----------|-------------|
| Reportes PDF | Alta | Exportación de estadísticas |
| Multi-idioma | Media | Soporte i18n |
| Notificaciones push | Media | Alertas en backoffice |
| Historial de auditoría UI | Baja | Visualización de logs |
| Temas personalizados | Baja | Más opciones de colores |

### 7.2 Mejoras Técnicas Sugeridas

| Área | Mejora | Beneficio |
|------|--------|-----------|
| Testing | Agregar Jest + Cypress | Cobertura de tests |
| CI/CD | Pipeline de GitHub Actions | Automatización |
| Monitoring | Agregar Prometheus + Grafana | Observabilidad |
| Cache | Implementar cache en frontend | Performance |
| PWA | Convertir KDS a PWA | Offline support |

### 7.3 Bugs Conocidos

| ID | Descripción | Severidad |
|----|-------------|-----------|
| - | Timer puede desfasarse después de varias horas | Baja |
| - | Preview de apariencia no actualiza en tiempo real algunos campos | Baja |

---

## 8. Contacto y Soporte

Para consultas técnicas sobre este desarrollo, contactar al equipo de desarrollo original.

**Repositorio**: [URL del repositorio]
**Documentación**: `/docs-entrega/`
**Colección Postman**: `KDS-API.postman_collection.json`

---

**Documento**: Detalle de Desarrollo
**Sistema**: KDS v2.0 - Kitchen Display System
**Fecha**: Diciembre 2025
**Versión del documento**: 1.0
