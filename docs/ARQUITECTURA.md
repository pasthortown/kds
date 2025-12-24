# Arquitectura del Sistema KDS

Este documento describe la arquitectura tecnica del sistema Kitchen Display System (KDS).

## Indice

1. [Vision General](#vision-general)
2. [Componentes del Sistema](#componentes-del-sistema)
3. [Arquitectura de Servicios](#arquitectura-de-servicios)
4. [Modelo de Datos](#modelo-de-datos)
5. [Flujo de Ordenes](#flujo-de-ordenes)
6. [Comunicacion en Tiempo Real](#comunicacion-en-tiempo-real)
7. [Sistema de Colas](#sistema-de-colas)
8. [Autenticacion y Seguridad](#autenticacion-y-seguridad)
9. [Configuracion por Pantalla](#configuracion-por-pantalla)
10. [Integraciones](#integraciones)

---

## Vision General

El sistema KDS es una solucion para la gestion de ordenes en cocinas de restaurantes. Permite visualizar, gestionar y completar ordenes desde multiples pantallas, con soporte para diferentes colas de produccion, canales de venta y configuraciones personalizadas.

### Caracteristicas Principales

- **Multi-pantalla**: Soporte para multiples pantallas de cocina
- **Multi-cola**: Diferentes colas de produccion (LINEAS, SANDUCHE, etc.)
- **Multi-canal**: Integracion con diversos canales de venta
- **Tiempo real**: Actualizaciones instantaneas via WebSocket
- **Configurable**: Apariencia y comportamiento personalizable por pantalla
- **SLA Visual**: Colores dinamicos segun tiempo de espera

---

## Componentes del Sistema

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              SISTEMA KDS                                     │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                   │
│  │   Backoffice │    │ KDS Frontend │    │ KDS Frontend │  ...              │
│  │  (Admin UI)  │    │ (Pantalla 1) │    │ (Pantalla 2) │                   │
│  │   React/TS   │    │   React/TS   │    │   React/TS   │                   │
│  │   :8081      │    │    :8080     │    │    :8080     │                   │
│  └──────┬───────┘    └──────┬───────┘    └──────┬───────┘                   │
│         │                   │                   │                            │
│         └───────────────────┴───────────────────┘                            │
│                             │                                                │
│                     ┌───────┴───────┐                                        │
│                     │    NGINX      │  (Reverse Proxy)                       │
│                     │    :80/443    │                                        │
│                     └───────┬───────┘                                        │
│                             │                                                │
│  ┌──────────────────────────┴──────────────────────────┐                    │
│  │                     Backend API                      │                    │
│  │                  Node.js / Express                   │                    │
│  │                       :3000                          │                    │
│  │  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌────────┐  │                    │
│  │  │  REST   │  │WebSocket│  │  Auth   │  │ Mirror │  │                    │
│  │  │  API    │  │ Server  │  │  JWT    │  │ Service│  │                    │
│  │  └─────────┘  └─────────┘  └─────────┘  └────────┘  │                    │
│  └─────────────────────┬────────────────────────────────┘                    │
│                        │                                                     │
│         ┌──────────────┼──────────────┐                                     │
│         │              │              │                                     │
│  ┌──────┴──────┐ ┌─────┴─────┐ ┌─────┴─────┐                               │
│  │  PostgreSQL │ │   Redis   │ │   Sync    │                               │
│  │    :5432    │ │   :6379   │ │  Service  │                               │
│  │  (Primary   │ │  (Cache/  │ │   .NET    │                               │
│  │   Storage)  │ │  Pub-Sub) │ │   :8100   │                               │
│  └─────────────┘ └───────────┘ └─────┬─────┘                               │
│                                      │                                      │
│                               ┌──────┴──────┐                               │
│                               │  MaxPoint   │                               │
│                               │ SQL Server  │                               │
│                               │  (Externo)  │                               │
│                               └─────────────┘                               │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Arquitectura de Servicios

### Backend API (Node.js/Express)

**Ubicacion**: `/backend`

**Tecnologias**:
- Node.js 18+
- Express.js
- TypeScript
- Prisma ORM
- Socket.IO

**Responsabilidades**:
- API REST para CRUD de entidades
- Autenticacion y autorizacion JWT
- WebSocket para tiempo real
- Logica de negocio (balanceo, SLA)
- Gestion de sesiones y heartbeat

**Estructura**:
```
backend/
├── src/
│   ├── routes/          # Endpoints REST
│   ├── services/        # Logica de negocio
│   ├── middleware/      # Auth, logging, etc.
│   ├── websocket/       # Handlers de Socket.IO
│   └── utils/           # Utilidades
├── prisma/
│   ├── schema.prisma    # Modelo de datos
│   └── seed.ts          # Datos iniciales
└── package.json
```

### KDS Frontend (React)

**Ubicacion**: `/kds-frontend`

**Tecnologias**:
- React 18
- TypeScript
- Vite
- Socket.IO Client
- CSS Modules

**Responsabilidades**:
- Visualizacion de ordenes
- Interaccion tactil/teclado
- Timer y colores SLA
- Conexion WebSocket
- Modo offline

**Estructura**:
```
kds-frontend/
├── src/
│   ├── components/      # Componentes UI
│   ├── hooks/           # Custom hooks
│   ├── services/        # API client
│   ├── stores/          # Estado global
│   └── utils/           # Utilidades
└── vite.config.ts
```

### Backoffice (React)

**Ubicacion**: `/backoffice`

**Tecnologias**:
- React 18
- TypeScript
- Ant Design
- Vite

**Responsabilidades**:
- Panel de administracion
- Gestion de pantallas
- Configuracion de apariencia
- Reportes y estadisticas
- Gestion de usuarios

**Estructura**:
```
backoffice/
├── src/
│   ├── components/      # Componentes reutilizables
│   ├── pages/           # Vistas principales
│   ├── services/        # API client
│   └── contexts/        # Estado global
└── vite.config.ts
```

### Sync Service (.NET)

**Ubicacion**: `/sync`

**Tecnologias**:
- .NET 6+
- Entity Framework
- SQL Server Client

**Responsabilidades**:
- Polling a MaxPoint
- Transformacion de ordenes
- Envio al backend KDS
- Logging y reintentos

---

## Modelo de Datos

### Diagrama Entidad-Relacion

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│      User       │     │     Queue       │     │    Channel      │
├─────────────────┤     ├─────────────────┤     ├─────────────────┤
│ id              │     │ id              │     │ id              │
│ email           │     │ name            │     │ name            │
│ password        │     │ description     │     │ displayName     │
│ name            │     │ distribution    │     │ backgroundColor │
│ role            │     │ active          │     │ textColor       │
│ active          │     └────────┬────────┘     │ priority        │
└─────────────────┘              │              │ active          │
                                 │              └─────────────────┘
                    ┌────────────┼────────────┐
                    │            │            │
           ┌────────┴───┐  ┌─────┴─────┐  ┌───┴───────────┐
           │QueueChannel│  │QueueFilter│  │    Screen     │
           ├────────────┤  ├───────────┤  ├───────────────┤
           │ id         │  │ id        │  │ id            │
           │ queueId    │  │ queueId   │  │ number        │
           │ channel    │  │ pattern   │  │ name          │
           │ color      │  │ suppress  │  │ queueId       │
           │ priority   │  │ active    │  │ status        │
           │ active     │  └───────────┘  │ apiKey        │
           └────────────┘                 └───────┬───────┘
                                                  │
                    ┌─────────────────────────────┼─────────────────────────────┐
                    │                             │                             │
           ┌────────┴───────┐           ┌─────────┴─────────┐         ┌─────────┴─────────┐
           │   Appearance   │           │   Preference      │         │  KeyboardConfig   │
           ├────────────────┤           ├───────────────────┤         ├───────────────────┤
           │ id             │           │ id                │         │ id                │
           │ screenId       │           │ screenId          │         │ screenId          │
           │ backgroundColor│           │ finishOrderActive │         │ finishFirstOrder  │
           │ cardColor      │           │ showClientData    │         │ nextPage          │
           │ textColor      │           │ showIdentifier    │         │ previousPage      │
           │ header*        │           │ touchEnabled      │         │ ...               │
           │ timer*         │           │ botoneraEnabled   │         └───────────────────┘
           │ client*        │           └───────────────────┘
           │ product*       │
           │ ...            │
           └────────┬───────┘
                    │
       ┌────────────┴────────────┐
       │                         │
┌──────┴──────┐          ┌───────┴───────┐
│  CardColor  │          │ ChannelColor  │
├─────────────┤          ├───────────────┤
│ id          │          │ id            │
│ appearanceId│          │ appearanceId  │
│ color       │          │ channel       │
│ quantityColor          │ color         │
│ minutes     │          │ textColor     │
│ order       │          └───────────────┘
│ isFullBackground
└─────────────┘

┌─────────────────┐     ┌─────────────────┐
│     Order       │     │   OrderItem     │
├─────────────────┤     ├─────────────────┤
│ id              │◄────│ id              │
│ externalId      │     │ orderId         │
│ screenId        │     │ name            │
│ channel         │     │ quantity        │
│ customerName    │     │ notes           │
│ identifier      │     │ modifier        │
│ status          │     │ comments        │
│ statusPos       │     └─────────────────┘
│ createdAt       │
│ finishedAt      │
│ comments        │
└─────────────────┘
```

### Entidades Principales

| Entidad | Descripcion |
|---------|-------------|
| **User** | Usuarios del sistema (admin, operador, viewer) |
| **Queue** | Colas de produccion (LINEAS, SANDUCHE) |
| **QueueChannel** | Canales asociados a cada cola |
| **QueueFilter** | Filtros de productos por cola |
| **Channel** | Canales globales de venta |
| **Screen** | Pantallas fisicas de cocina |
| **Appearance** | Configuracion visual por pantalla |
| **CardColor** | Colores SLA por tiempo |
| **ChannelColor** | Colores por canal en cada pantalla |
| **Preference** | Preferencias de comportamiento |
| **KeyboardConfig** | Mapeo de teclas/botonera |
| **Order** | Ordenes de cocina (incluye statusPos para estado del POS) |
| **OrderItem** | Items de cada orden |
| **GeneralConfig** | Configuracion global del sistema |

---

## Flujo de Ordenes

### 1. Ingreso de Orden

```
MaxPoint        Sync Service       Backend         WebSocket        Pantallas
   │                │                 │                │                │
   │  Nueva orden   │                 │                │                │
   ├───────────────►│                 │                │                │
   │                │  POST /orders   │                │                │
   │                ├────────────────►│                │                │
   │                │                 │                │                │
   │                │                 ├─ Validar       │                │
   │                │                 ├─ Asignar cola  │                │
   │                │                 ├─ Balancear     │                │
   │                │                 │                │                │
   │                │     200 OK      │                │                │
   │                │◄────────────────┤                │                │
   │                │                 │   order:new    │                │
   │                │                 ├───────────────►│                │
   │                │                 │                │   Actualizar   │
   │                │                 │                ├───────────────►│
   │                │                 │                │                │
```

### 2. Completar Orden

```
Pantalla        WebSocket          Backend         Base de Datos
   │                │                 │                │
   │  Finalizar     │                 │                │
   ├───────────────►│                 │                │
   │                │ order:finish    │                │
   │                ├────────────────►│                │
   │                │                 │                │
   │                │                 ├─ Validar       │
   │                │                 ├─ Update status │
   │                │                 │      UPDATE    │
   │                │                 ├───────────────►│
   │                │                 │       OK       │
   │                │                 │◄───────────────┤
   │                │ order:finished  │                │
   │                │◄────────────────┤                │
   │   Actualizar   │                 │                │
   │◄───────────────┤                 │                │
   │                │ broadcast       │                │
   │                ├─────────────────┼───────────────►│ Otras pantallas
   │                │                 │                │
```

---

## Comunicacion en Tiempo Real

### Eventos WebSocket

| Evento | Direccion | Descripcion |
|--------|-----------|-------------|
| `connect` | Cliente → Servidor | Conexion inicial |
| `authenticate` | Cliente → Servidor | Autenticacion con API Key |
| `authenticated` | Servidor → Cliente | Confirmacion de autenticacion |
| `order:new` | Servidor → Cliente | Nueva orden asignada |
| `order:updated` | Servidor → Cliente | Orden modificada |
| `order:finished` | Servidor → Cliente | Orden completada |
| `order:cancelled` | Servidor → Cliente | Orden cancelada |
| `order:finish` | Cliente → Servidor | Solicitud de completar orden |
| `order:undo` | Cliente → Servidor | Deshacer ultima accion |
| `screen:heartbeat` | Cliente → Servidor | Señal de vida |
| `config:updated` | Servidor → Cliente | Configuracion actualizada |

### Rooms de Socket.IO

```
                    ┌─────────────────────────────────┐
                    │         Socket.IO Server        │
                    └─────────────────────────────────┘
                               │
          ┌────────────────────┼────────────────────┐
          │                    │                    │
     ┌────┴────┐          ┌────┴────┐          ┌────┴────┐
     │ screen:1│          │ screen:2│          │ screen:3│
     │  (Room) │          │  (Room) │          │  (Room) │
     └────┬────┘          └────┬────┘          └────┬────┘
          │                    │                    │
     ┌────┴────┐          ┌────┴────┐          ┌────┴────┐
     │Pantalla1│          │Pantalla2│          │Pantalla3│
     └─────────┘          └─────────┘          └─────────┘
```

---

## Sistema de Colas

### Tipos de Distribucion

| Tipo | Codigo | Comportamiento |
|------|--------|----------------|
| **DISTRIBUTED** | D | Ordenes se reparten entre pantallas activas |
| **SINGLE** | S | Ordenes van a una sola pantalla |

### Algoritmo de Balanceo

```javascript
// Pseudocodigo del balanceo DISTRIBUTED
function balancearOrden(orden, cola) {
  // 1. Obtener pantallas activas de la cola
  pantallas = cola.screens.filter(s => s.status === 'ONLINE')

  // 2. Contar ordenes pendientes por pantalla
  for (pantalla in pantallas) {
    pantalla.carga = contarOrdenesPendientes(pantalla)
  }

  // 3. Ordenar por carga (menor primero)
  pantallas.sort((a, b) => a.carga - b.carga)

  // 4. Asignar a la pantalla con menor carga
  return pantallas[0]
}
```

### Filtros de Cola

Los filtros permiten incluir o excluir productos de una cola:

| Campo | Descripcion |
|-------|-------------|
| `pattern` | Texto a buscar en el nombre del producto |
| `suppress` | `true` = excluir, `false` = incluir |

**Ejemplo**: Cola SANDUCHE
- Filtro: `pattern: "SANDUCHE", suppress: false` → Solo muestra productos con "SANDUCHE"
- Cola LINEAS: `pattern: "SANDUCHE", suppress: true` → Oculta productos con "SANDUCHE"

---

## Autenticacion y Seguridad

### JWT (JSON Web Tokens)

```
┌─────────────┐                    ┌─────────────┐
│   Cliente   │                    │   Backend   │
└──────┬──────┘                    └──────┬──────┘
       │                                  │
       │  POST /auth/login                │
       │  {email, password}               │
       ├─────────────────────────────────►│
       │                                  │
       │                    Validar credenciales
       │                    Generar JWT tokens
       │                                  │
       │  {accessToken, refreshToken}     │
       │◄─────────────────────────────────┤
       │                                  │
       │  GET /api/resource               │
       │  Authorization: Bearer {token}   │
       ├─────────────────────────────────►│
       │                                  │
       │                    Validar JWT
       │                    Verificar permisos
       │                                  │
       │  200 OK {data}                   │
       │◄─────────────────────────────────┤
       │                                  │
```

### Autenticacion de Pantallas

Las pantallas usan un API Key unico para autenticarse:

```javascript
// Cliente KDS Frontend
socket.emit('authenticate', { apiKey: 'screen-api-key-123' });

// Servidor valida y responde
socket.on('authenticated', (data) => {
  // { screenId, screenName, queueId, ... }
});
```

### Roles de Usuario

| Rol | Permisos |
|-----|----------|
| **ADMIN** | Acceso total al sistema |
| **OPERATOR** | Gestion de ordenes y pantallas |
| **VIEWER** | Solo lectura |

---

## Configuracion por Pantalla

Cada pantalla tiene su propia configuracion independiente:

### Apariencia (Appearance)

- Colores generales (fondo, tarjeta, texto)
- Tipografia de cada elemento
- Visibilidad de componentes
- Colores SLA
- Colores por canal

### Preferencias (Preference)

- Modo tactil / botonera
- Datos de cliente a mostrar
- Paginacion
- Auto-finalizacion de ordenes

### Teclado (KeyboardConfig)

- Mapeo de teclas para acciones
- Combos de teclas
- Tiempo de debounce

---

## Integraciones

### MaxPoint (POS)

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  MaxPoint   │     │    Sync     │     │   Backend   │
│  SQL Server │────►│   Service   │────►│    KDS      │
└─────────────┘     └─────────────┘     └─────────────┘
     Polling cada N segundos    API REST POST /orders
```

**Flujo**:
1. Sync Service hace polling a tabla de comandas en MaxPoint
2. Transforma datos al formato KDS
3. Envia ordenes al Backend via API REST
4. Backend distribuye y notifica pantallas

### API Externa

El sistema expone endpoints para integracion externa:

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/api/orders` | POST | Crear nueva orden |
| `/api/orders/:id` | GET | Obtener orden |
| `/api/orders/:id/finish` | POST | Completar orden |
| `/api/orders/stats` | GET | Estadisticas |

---

## Tecnologias Utilizadas

### Backend

| Tecnologia | Version | Proposito |
|------------|---------|-----------|
| Node.js | 18+ | Runtime |
| Express | 4.x | Framework web |
| TypeScript | 5.x | Tipado estatico |
| Prisma | 5.x | ORM |
| Socket.IO | 4.x | WebSockets |
| JWT | - | Autenticacion |
| bcrypt | - | Hash de passwords |

### Frontend

| Tecnologia | Version | Proposito |
|------------|---------|-----------|
| React | 18+ | UI Framework |
| TypeScript | 5.x | Tipado estatico |
| Vite | 5.x | Build tool |
| Socket.IO Client | 4.x | WebSockets |
| Ant Design | 5.x | UI Components (Backoffice) |

### Infraestructura

| Tecnologia | Version | Proposito |
|------------|---------|-----------|
| PostgreSQL | 15+ | Base de datos |
| Redis | 7+ | Cache y Pub/Sub |
| Docker | 20+ | Contenedores |
| Docker Compose | 2+ | Orquestacion |
| NGINX | - | Reverse proxy |

---

## Escalabilidad

### Horizontal

- Multiples instancias de Backend detras de load balancer
- Redis para compartir estado entre instancias
- Sticky sessions para WebSocket

### Vertical

- Aumentar recursos de PostgreSQL
- Aumentar memoria de Redis
- Optimizar queries con indices

### Consideraciones

- Limitar polling de Sync Service
- Implementar paginacion en APIs
- Cache de configuraciones en Redis
- Cleanup periodico de ordenes antiguas
