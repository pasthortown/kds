# Arquitectura del Sistema KDS v2.0

## Kitchen Display System - Documentación de Arquitectura

---

## 1. Visión General

El **KDS (Kitchen Display System)** es un sistema de visualización de órdenes en tiempo real para cocinas de restaurantes. Permite mostrar pedidos provenientes del sistema POS MAXPOINT en múltiples pantallas de cocina, con distribución automática, gestión de tiempos (SLA) y configuración visual completa.

### 1.1 Diagrama de Arquitectura General

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           SISTEMA KDS v2.0                                  │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│   ┌─────────────┐     ┌─────────────┐     ┌─────────────┐                  │
│   │  MAXPOINT   │     │   API REST  │     │   CLIENTES  │                  │
│   │  SQL Server │────▶│   + Socket  │◀────│   EXTERNOS  │                  │
│   │  (Polling)  │     │   Backend   │     │   (Legacy)  │                  │
│   └─────────────┘     └──────┬──────┘     └─────────────┘                  │
│                              │                                              │
│         ┌────────────────────┼────────────────────┐                        │
│         │                    │                    │                        │
│         ▼                    ▼                    ▼                        │
│   ┌───────────┐        ┌───────────┐        ┌───────────┐                  │
│   │ PostgreSQL│        │   Redis   │        │  Socket.IO │                  │
│   │    15     │        │     7     │        │  Pub/Sub   │                  │
│   │  (Data)   │        │  (Cache)  │        │ (Realtime) │                  │
│   └───────────┘        └───────────┘        └─────┬─────┘                  │
│                                                   │                        │
│         ┌─────────────────────────────────────────┼─────────────────┐      │
│         │                                         │                 │      │
│         ▼                                         ▼                 ▼      │
│   ┌───────────────┐                    ┌───────────────┐    ┌───────────┐  │
│   │   Backoffice  │                    │  KDS Frontend │    │ KDS Front │  │
│   │    (Admin)    │                    │  (Pantalla 1) │    │(Pantalla N│  │
│   │   :8081       │                    │    :8080      │    │   :8080   │  │
│   └───────────────┘                    └───────────────┘    └───────────┘  │
│                                                                             │
│   ┌─────────────────────────────────────────────────────────────────────┐  │
│   │                         IMPRESORAS                                   │  │
│   │   ┌─────────────┐              ┌─────────────────────┐              │  │
│   │   │ TCP :9100   │              │  HTTP Centralizado  │              │  │
│   │   │  (Local)    │              │    (Servidor)       │              │  │
│   │   └─────────────┘              └─────────────────────┘              │  │
│   └─────────────────────────────────────────────────────────────────────┘  │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Componentes del Sistema

### 2.1 Backend (Node.js/Express)

**Tecnologías**: Node.js 20 LTS, Express 4.18, TypeScript 5.3, Prisma 5.7

**Responsabilidades**:
- API REST para CRUD de entidades
- WebSocket (Socket.IO) para comunicación en tiempo real
- Polling a MAXPOINT SQL Server
- Distribución de órdenes (balanceo Round-Robin)
- Autenticación JWT
- Gestión de impresoras

**Estructura de Carpetas**:
```
backend/
├── src/
│   ├── config/          # Configuración (env, db, redis, mxp)
│   ├── controllers/     # Controladores REST
│   ├── middlewares/     # Auth, error handling
│   ├── routes/          # Definición de rutas
│   ├── services/        # Lógica de negocio
│   ├── types/           # Tipos TypeScript + Zod
│   ├── utils/           # Logger, helpers
│   └── index.ts         # Entry point
├── prisma/
│   ├── schema.prisma    # Modelo de datos
│   └── seed.ts          # Datos iniciales
└── package.json
```

**Servicios Principales**:

| Servicio | Archivo | Líneas | Función |
|----------|---------|--------|---------|
| Auth | auth.service.ts | 193 | JWT, login, refresh tokens |
| Order | order.service.ts | 656 | CRUD órdenes, estados |
| Screen | screen.service.ts | 362 | CRUD pantallas, heartbeat |
| Balancer | balancer.service.ts | 411 | Distribución Round-Robin |
| WebSocket | websocket.service.ts | 385 | Socket.IO, eventos tiempo real |
| Polling | polling.service.ts | 163 | Lectura MAXPOINT |
| Printer | printer.service.ts | 236 | Impresión TCP local |
| Mirror | mirror-kds.service.ts | 412 | Replicación KDS remota |

---

### 2.2 KDS Frontend (React)

**Tecnologías**: React 18, TypeScript, Vite 5, Zustand, TailwindCSS, Socket.IO Client

**Responsabilidades**:
- Visualización de órdenes en grid
- Timer en tiempo real con colores SLA
- Soporte botonera física USB
- Modo standby
- Modo prueba (sandbox)

**Estructura de Carpetas**:
```
kds-frontend/
├── src/
│   ├── components/      # OrderCard, OrderGrid, Header, Footer
│   ├── hooks/           # useWebSocket, useKeyboard
│   ├── store/           # Zustand stores
│   ├── services/        # API, Socket
│   ├── utils/           # TimeUtils, ButtonController
│   ├── types/           # TypeScript interfaces
│   ├── App.tsx          # Router
│   └── main.tsx         # Entry point
└── package.json
```

**Componentes Principales**:

| Componente | Función |
|------------|---------|
| OrderCard | Tarjeta individual de orden con timer, productos, canal |
| OrderGrid | Grid paginado de órdenes |
| Header | Número/nombre pantalla, indicador cola |
| Footer | Contador de órdenes pendientes |
| StandbyScreen | Pantalla negra en modo standby |
| TestModePanel | Panel flotante para pruebas |

---

### 2.3 Backoffice (React + Ant Design)

**Tecnologías**: React 18, TypeScript, Vite 5, Ant Design 5, Zustand, Chart.js

**Responsabilidades**:
- Dashboard con KPIs y gráficos
- CRUD de pantallas, colas, usuarios
- Editor visual de apariencia
- Configuración SLA
- Configuración MAXPOINT
- Gestión de polling

**Estructura de Carpetas**:
```
backoffice/
├── src/
│   ├── components/      # Layout, ScreenPreview
│   ├── pages/           # Dashboard, Screens, Queues, etc.
│   ├── services/        # API client
│   ├── store/           # Auth store
│   ├── utils/           # PDF reports
│   ├── App.tsx          # Router
│   └── main.tsx         # Entry point
└── package.json
```

**Páginas**:

| Página | Ruta | Función |
|--------|------|---------|
| Login | /login | Autenticación |
| Dashboard | / | KPIs, gráficos, estado pantallas |
| Screens | /screens | CRUD pantallas |
| Queues | /queues | CRUD colas y canales |
| Orders | /orders | Listado y búsqueda |
| Appearance | /appearance | Editor visual CSS |
| SLA | /sla | Configuración tiempos/colores |
| Settings | /settings | Config MAXPOINT, modos |
| Users | /users | CRUD usuarios |

---

### 2.4 Infraestructura (Docker)

**Servicios Docker**:

| Servicio | Imagen | Puerto | Volumen |
|----------|--------|--------|---------|
| PostgreSQL | postgres:15-alpine | 5432 | postgres_data |
| Redis | redis:7-alpine | 6379 | redis_data |
| Backend | Node.js custom | 3000 | - |
| KDS Frontend | Nginx + static | 8080 | - |
| Backoffice | Nginx + static | 8081 | - |

**Red**: `kds-network` (bridge)

---

## 3. Flujos de Datos

### 3.1 Flujo de Órdenes (Principal)

```
┌─────────────┐      ┌─────────────┐      ┌─────────────┐      ┌─────────────┐
│  MAXPOINT   │      │   Backend   │      │   Redis     │      │ KDS Screen  │
│  SQL Server │      │   Polling   │      │   Pub/Sub   │      │  Frontend   │
└──────┬──────┘      └──────┬──────┘      └──────┬──────┘      └──────┬──────┘
       │                    │                    │                    │
       │  1. Query órdenes  │                    │                    │
       │◀───────────────────│                    │                    │
       │                    │                    │                    │
       │  2. Retorna datos  │                    │                    │
       │───────────────────▶│                    │                    │
       │                    │                    │                    │
       │                    │ 3. Upsert en       │                    │
       │                    │    PostgreSQL      │                    │
       │                    │                    │                    │
       │                    │ 4. Distribuir      │                    │
       │                    │    (Round-Robin)   │                    │
       │                    │                    │                    │
       │                    │ 5. Publicar        │                    │
       │                    │───────────────────▶│                    │
       │                    │                    │                    │
       │                    │                    │ 6. Notificar       │
       │                    │                    │───────────────────▶│
       │                    │                    │                    │
       │                    │                    │                    │ 7. Renderizar
       │                    │                    │                    │    orden en grid
       │                    │                    │                    │
```

### 3.2 Flujo de Finalización de Orden

```
┌─────────────┐      ┌─────────────┐      ┌─────────────┐      ┌─────────────┐
│ KDS Screen  │      │   Backend   │      │ PostgreSQL  │      │  Impresora  │
│  Frontend   │      │   Service   │      │   + Redis   │      │   (TCP)     │
└──────┬──────┘      └──────┬──────┘      └──────┬──────┘      └──────┬──────┘
       │                    │                    │                    │
       │ 1. WebSocket       │                    │                    │
       │    order:finish    │                    │                    │
       │───────────────────▶│                    │                    │
       │                    │                    │                    │
       │                    │ 2. Update status   │                    │
       │                    │    FINISHED        │                    │
       │                    │───────────────────▶│                    │
       │                    │                    │                    │
       │                    │ 3. Broadcast       │                    │
       │                    │    a pantallas     │                    │
       │                    │───────────────────▶│                    │
       │                    │                    │                    │
       │                    │ 4. Imprimir        │                    │
       │                    │    (si habilitado) │                    │
       │                    │───────────────────────────────────────▶│
       │                    │                    │                    │
       │ 5. Actualizar UI   │                    │                    │
       │◀───────────────────│                    │                    │
       │                    │                    │                    │
```

### 3.3 Flujo de Configuración en Tiempo Real

```
┌─────────────┐      ┌─────────────┐      ┌─────────────┐      ┌─────────────┐
│  Backoffice │      │   Backend   │      │   Redis     │      │ KDS Screen  │
│   Admin     │      │   API       │      │   Pub/Sub   │      │  Frontend   │
└──────┬──────┘      └──────┬──────┘      └──────┬──────┘      └──────┬──────┘
       │                    │                    │                    │
       │ 1. PUT /appearance │                    │                    │
       │───────────────────▶│                    │                    │
       │                    │                    │                    │
       │                    │ 2. Guardar en DB   │                    │
       │                    │───────────────────▶│                    │
       │                    │                    │                    │
       │                    │ 3. Publicar cambio │                    │
       │                    │───────────────────▶│                    │
       │                    │                    │                    │
       │                    │                    │ 4. Notificar       │
       │                    │                    │    pantalla        │
       │                    │                    │───────────────────▶│
       │                    │                    │                    │
       │                    │                    │                    │ 5. Re-render
       │                    │                    │                    │    sin reload
       │                    │                    │                    │
```

---

## 4. Modelo de Datos

### 4.1 Diagrama Entidad-Relación

```
┌─────────────────┐
│      User       │
├─────────────────┤
│ id (PK)         │
│ email           │
│ password        │
│ name            │
│ role            │──────────────┐
│ active          │              │
└─────────────────┘              │
                                 │
┌─────────────────┐              │
│    AuditLog     │              │
├─────────────────┤              │
│ id (PK)         │              │
│ userId (FK)     │◀─────────────┘
│ action          │
│ entity          │
│ timestamp       │
└─────────────────┘

┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│      Queue      │       │     Screen      │       │      Order      │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK)         │◀──────│ queueId (FK)    │◀──────│ screenId (FK)   │
│ name            │       │ id (PK)         │       │ id (PK)         │
│ distribution    │       │ number          │       │ externalId      │
│ active          │       │ name            │       │ channel         │
└────────┬────────┘       │ status          │       │ status          │
         │                │ apiKey          │       │ customerName    │
         │                └────────┬────────┘       └────────┬────────┘
         │                         │                         │
         ▼                         │                         ▼
┌─────────────────┐                │                ┌─────────────────┐
│  QueueChannel   │                │                │   OrderItem     │
├─────────────────┤                │                ├─────────────────┤
│ id (PK)         │                │                │ id (PK)         │
│ queueId (FK)    │                │                │ orderId (FK)    │
│ channel         │                │                │ name            │
│ color           │                │                │ quantity        │
│ priority        │                │                │ notes           │
└─────────────────┘                │                │ modifier        │
                                   │                └─────────────────┘
┌─────────────────┐                │
│   QueueFilter   │                │
├─────────────────┤                ▼
│ id (PK)         │       ┌─────────────────┐
│ queueId (FK)    │       │   Appearance    │
│ pattern         │       ├─────────────────┤
│ suppress        │       │ id (PK)         │
└─────────────────┘       │ screenId (FK)   │◀─── 1:1
                          │ theme           │
                          │ colors...       │
                          │ fonts...        │
                          └────────┬────────┘
                                   │
                                   ▼
                          ┌─────────────────┐
                          │   CardColor     │
                          ├─────────────────┤
                          │ id (PK)         │
                          │ appearanceId(FK)│
                          │ color           │
                          │ minutes         │
                          │ order           │
                          └─────────────────┘

┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│   Preference    │       │ KeyboardConfig  │       │    Printer      │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK)         │       │ id (PK)         │       │ id (PK)         │
│ screenId (FK)   │◀─1:1─▶│ screenId (FK)   │◀─1:1─▶│ screenId (FK)   │
│ showClientData  │       │ finishFirstOrder│       │ name            │
│ touchEnabled    │       │ nextPage        │       │ ip              │
│ botoneraEnabled │       │ combos          │       │ port            │
└─────────────────┘       └─────────────────┘       │ enabled         │
                                                    └─────────────────┘

┌─────────────────┐
│  GeneralConfig  │ (Singleton)
├─────────────────┤
│ id = "general"  │
│ testMode        │
│ ticketMode      │
│ printMode       │
│ pollingInterval │
│ mxpHost         │
│ ...             │
└─────────────────┘
```

### 4.2 Tablas Principales

| Tabla | Registros típicos | Función |
|-------|-------------------|---------|
| User | 5-20 | Usuarios del sistema |
| Queue | 1-5 | Colas de distribución |
| Screen | 2-10 | Pantallas de cocina |
| Order | 100-1000/día | Órdenes activas |
| Appearance | 1 por pantalla | Config visual |
| GeneralConfig | 1 (singleton) | Config global |

---

## 5. Seguridad

### 5.1 Autenticación

- **JWT Access Token**: 15 minutos de vida
- **JWT Refresh Token**: 7 días de vida
- **Bcrypt**: Hash de contraseñas (12 rounds)
- **Cookies HttpOnly**: Para refresh tokens (opcional)

### 5.2 Autorización (Roles)

| Rol | Permisos |
|-----|----------|
| ADMIN | Control total del sistema |
| OPERATOR | Gestión pantallas y órdenes |
| VIEWER | Solo lectura |

### 5.3 Protección API

- **Helmet**: Headers de seguridad
- **CORS**: Origins configurables
- **Rate Limiting**: 1000 req/15 min por IP
- **Validación**: Zod schemas en todas las rutas

---

## 6. Escalabilidad

### 6.1 Horizontal

```
                    ┌─────────────┐
                    │   Nginx     │
                    │   Reverse   │
                    │   Proxy     │
                    └──────┬──────┘
                           │
           ┌───────────────┼───────────────┐
           │               │               │
           ▼               ▼               ▼
    ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
    │  Backend 1  │ │  Backend 2  │ │  Backend 3  │
    └──────┬──────┘ └──────┬──────┘ └──────┬──────┘
           │               │               │
           └───────────────┼───────────────┘
                           │
           ┌───────────────┴───────────────┐
           │                               │
           ▼                               ▼
    ┌─────────────┐                 ┌─────────────┐
    │  PostgreSQL │                 │    Redis    │
    │   Primary   │                 │   Cluster   │
    └─────────────┘                 └─────────────┘
```

### 6.2 Consideraciones

- **Redis Pub/Sub**: Sincroniza eventos entre instancias backend
- **Sticky Sessions**: No requeridas (stateless con JWT)
- **PostgreSQL**: Replica de lectura para reportes
- **Load Balancer**: Round-Robin o Least Connections

---

## 7. Integración con MAXPOINT

### 7.1 Conexión SQL Server

```typescript
// Configuración en backend/src/config/mxp.ts
{
  server: process.env.MXP_SERVER,
  database: process.env.MXP_DATABASE,
  user: process.env.MXP_USER,
  password: process.env.MXP_PASSWORD,
  options: {
    encrypt: true,
    trustServerCertificate: true
  }
}
```

### 7.2 Query de Polling

El sistema consulta la tabla de tickets de MAXPOINT cada N segundos (configurable, default 2000ms) y procesa las órdenes nuevas.

### 7.3 Modo de Operación

| Modo | Descripción |
|------|-------------|
| POLLING | Lee de MAXPOINT SQL Server |
| API | Recibe tickets por API REST (legacy) |

---

## 8. Tecnologías y Versiones

### Backend
| Tecnología | Versión | Uso |
|------------|---------|-----|
| Node.js | 20.x LTS | Runtime |
| Express | 4.18.x | Framework HTTP |
| TypeScript | 5.3.x | Lenguaje |
| Prisma | 5.7.x | ORM |
| Socket.IO | 4.7.x | WebSocket |
| jsonwebtoken | 9.0.x | JWT |
| Zod | 3.22.x | Validación |

### Frontend KDS
| Tecnología | Versión | Uso |
|------------|---------|-----|
| React | 18.2.x | UI Framework |
| Vite | 5.0.x | Build tool |
| Zustand | 4.4.x | State |
| TailwindCSS | 3.3.x | Estilos |
| Socket.IO Client | 4.7.x | WebSocket |

### Backoffice
| Tecnología | Versión | Uso |
|------------|---------|-----|
| React | 18.2.x | UI Framework |
| Ant Design | 5.12.x | Componentes UI |
| Chart.js | 4.5.x | Gráficos |
| jsPDF | 3.0.x | Reportes |

### Infraestructura
| Tecnología | Versión | Uso |
|------------|---------|-----|
| PostgreSQL | 15 Alpine | Base de datos |
| Redis | 7 Alpine | Cache/Pub-Sub |
| Docker | 20.10+ | Contenedores |
| Nginx | latest | Reverse proxy |

---

## 9. Puertos y Endpoints

### Desarrollo

| Servicio | Puerto | URL |
|----------|--------|-----|
| Backend API | 3000 | http://localhost:3000/api |
| KDS Frontend | 5173 | http://localhost:5173 |
| Backoffice | 5174 | http://localhost:5174 |
| PostgreSQL | 5432 | localhost:5432 |
| Redis | 6379 | localhost:6379 |
| Adminer | 8082 | http://localhost:8082 |
| Redis Commander | 8083 | http://localhost:8083 |

### Producción (Docker)

| Servicio | Puerto | URL |
|----------|--------|-----|
| Backend API | 3000 | http://servidor:3000/api |
| KDS Frontend | 8080 | http://servidor:8080 |
| Backoffice | 8081 | http://servidor:8081 |

---

**Documento**: Arquitectura del Sistema
**Sistema**: KDS v2.0 - Kitchen Display System
**Fecha**: Diciembre 2025
