# Arquitectura de Bases de Datos del KDS

Este documento describe las tres bases de datos que utiliza el sistema KDS y cómo interactúan entre sí.

## Diagrama General

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              SISTEMA KDS                                     │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐        │
│   │   PostgreSQL    │    │    MaxPoint     │    │     KDS2        │        │
│   │   (BD LOCAL)    │    │   (SQL Server)  │    │  (SQL Server)   │        │
│   │                 │    │                 │    │    "Mirror"     │        │
│   │  Puerto: 5432   │    │  Puerto: 1433   │    │  Puerto: 1433   │        │
│   └────────┬────────┘    └────────┬────────┘    └────────┬────────┘        │
│            │                      │                      │                  │
│            │ Prisma ORM           │ mssql driver         │ mssql driver    │
│            │ (lectura/escritura)  │ (solo lectura)       │ (solo lectura)  │
│            │                      │                      │                  │
│            └──────────────────────┼──────────────────────┘                  │
│                                   │                                         │
│                          ┌────────▼────────┐                                │
│                          │    Backend      │                                │
│                          │   (Node.js)     │                                │
│                          └─────────────────┘                                │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 1. PostgreSQL (Base de Datos Local)

### Propósito
Base de datos principal del sistema KDS. Almacena **TODA** la configuración y datos internos.

### Conexión
- **Driver:** Prisma ORM
- **URL:** `postgresql://kds:password@kds-postgres:5432/kds`
- **Archivo de config:** `backend/src/config/database.ts`

### Tipo de Acceso
**Lectura y Escritura**

### Tablas Principales

| Tabla | Descripción |
|-------|-------------|
| `User` | Usuarios del backoffice (admin, operadores) |
| `Queue` | Colas de órdenes (Cocina, Bebidas, etc.) |
| `QueueChannel` | Canales asociados a cada cola |
| `QueueFilter` | Filtros de productos por cola |
| `Screen` | Pantallas KDS (número, nombre, estado, API key) |
| `Appearance` | Configuración visual de cada pantalla (colores, fuentes, SLA) |
| `Preference` | Preferencias de cada pantalla (touch, identificadores) |
| `KeyboardConfig` | Mapeo de teclas para cada pantalla |
| `Printer` | Impresoras asignadas a pantallas |
| `Order` | Órdenes procesadas (cuando NO usan Mirror) |
| `OrderItem` | Items de cada orden |
| `CardColor` | Colores del SLA (verde/amarillo/rojo por tiempo) |
| `ChannelColor` | Colores personalizados por canal |
| `GeneralConfig` | Configuración global del sistema |
| `ProductCounter` | Contadores de productos |
| `AuditLog` | Registro de auditoría |
| `Heartbeat` | Latidos de conexión de pantallas |

### Schema
El schema completo se encuentra en: `backend/prisma/schema.prisma`

### Cuándo se Usa
- **SIEMPRE** para configuración (pantallas, apariencia, usuarios, colas)
- Para órdenes **SOLO** cuando el Mirror KDS NO está activo

---

## 2. MaxPoint (SQL Server Externo)

### Propósito
Sistema POS (Point of Sale) de MaxPoint. Es el sistema de caja/ventas del restaurante.

### Conexión
- **Driver:** `mssql` (tedious)
- **Host:** Configurable en backoffice (`MXP_HOST`)
- **Puerto:** 1433 (SQL Server por defecto)
- **Archivo de config:** `backend/src/config/mxp.ts`

### Tipo de Acceso
**Solo Lectura** - El KDS nunca modifica datos de MaxPoint

### Configuración
Las credenciales se configuran en:
1. Variables de entorno (`.env`)
2. Tabla `GeneralConfig` en PostgreSQL (configurable desde backoffice)

```env
MXP_HOST=192.168.1.100
MXP_PORT=1433
MXP_USER=sa
MXP_PASSWORD=password
MXP_DATABASE=MaxPoint
```

### Query de Polling
```sql
SELECT
  o.IdOrden as OrderId,
  c.Nombre as Channel,
  o.NombreCliente as CustomerName,
  o.NumeroOrden as OrderNumber,
  o.FechaCreacion as CreatedAt
FROM Ordenes o
INNER JOIN Canales c ON o.IdCanal = c.IdCanal
WHERE o.Estado = 'PENDIENTE'
  AND o.FechaCreacion >= DATEADD(hour, -@hoursBack, GETDATE())
ORDER BY o.FechaCreacion ASC
```

### Servicio
`backend/src/services/mxp.service.ts`

### Cuándo se Usa
- Cuando `ticketMode = "POLLING"` en la configuración general
- El backend hace polling cada 2 segundos buscando órdenes nuevas
- Las órdenes encontradas se copian a PostgreSQL y se distribuyen a las pantallas

### Notas
- La estructura de tablas puede variar según la versión de MaxPoint
- Si la query falla, puede necesitar adaptarse a la estructura específica de tu instalación

---

## 3. KDS2 / Mirror (SQL Server Externo)

### Propósito
Conexión a un sistema KDS existente desarrollado en .NET. Permite "espejear" las órdenes de otro KDS sin interferir con él.

### Conexión
- **Driver:** `mssql` (tedious)
- **Host:** Configurable dinámicamente desde el backoffice
- **Puerto:** 1433
- **Archivo de config:** `backend/src/services/mirror-kds.service.ts`

### Tipo de Acceso
**Solo Lectura** (modo espejo/mirror)

### Tablas que Lee

#### Tabla `Comandas`
Contiene las órdenes en formato JSON.

```sql
SELECT
  c.IdOrden,
  c.datosComanda,      -- JSON con datos de la orden
  c.fechaIngreso,
  d.Cola,
  d.Pantalla,
  d.IdEstadoDistribucion
FROM Comandas c
INNER JOIN Distribucion d ON c.IdOrden = d.idOrden
WHERE d.IdEstadoDistribucion = 'EN_PANTALLA'
ORDER BY c.fechaIngreso ASC
```

#### Tabla `Distribucion`
Indica en qué pantalla está cada orden.

| Campo | Descripción |
|-------|-------------|
| `idOrden` | ID de la orden |
| `Cola` | Nombre de la cola |
| `Pantalla` | Nombre de la pantalla (ej: "Pantalla1") |
| `IdEstadoDistribucion` | Estado: "EN_PANTALLA", "FINALIZADA", etc. |

### Estructura del JSON `datosComanda`

```typescript
interface KDS2Comanda {
  id: string;
  createdAt: string;
  orderId: string;
  channel: {
    id: number;
    name: string;
    type: string;  // SALON, LLEVAR, etc.
  };
  customer?: {
    name: string;
  };
  cashRegister?: {
    cashier: string;
    name: string;
  };
  products: Array<{
    productId?: string;
    name?: string;
    amount?: number;
    category?: string;
    content?: string[];  // Modificadores
    products?: Array<{   // Subproductos
      productId?: string;
      name?: string;
      amount?: number;
    }>;
  }>;
  otrosDatos?: {
    turno: number;
    nroCheque: string;
    llamarPor: string;
    Fecha: string;
    Direccion: string;
  };
}
```

### Manejo del Timer
El Mirror guarda el timestamp de llegada de cada orden en Redis para que el timer funcione correctamente incluso después de reiniciar el backend.

```
Redis Key: mirror:order:created:{orderId}
Value: ISO timestamp
TTL: 24 horas
```

### Cuándo se Usa
- Cuando el Mirror está configurado y conectado
- El `balancerService.getOrdersForScreen()` detecta si el Mirror está activo
- Si está activo: obtiene órdenes del KDS2
- Si no está activo: obtiene órdenes de PostgreSQL

### Notas Importantes
- El Mirror es **solo lectura** - no modifica nada en el KDS2
- Las órdenes del Mirror **NO** se guardan en PostgreSQL
- Solo se muestran en tiempo real en las pantallas

---

## Flujo de Órdenes

### Opción A: Sin Mirror (MaxPoint → PostgreSQL)

```
[MaxPoint] ──polling 2s──▶ [Backend] ──guarda──▶ [PostgreSQL]
     │                         │                      │
     │                         │ distribuye           │ lee órdenes
     │                         ▼                      ▼
     │                    [Balancer] ◀────────── [Order table]
     │                         │
     │                         │ WebSocket
     │                         ▼
     │                    [Pantallas KDS]
```

1. Backend hace polling a MaxPoint cada 2 segundos
2. Órdenes nuevas se guardan en PostgreSQL
3. El Balancer las distribuye entre pantallas activas
4. Se envían por WebSocket a las pantallas

### Opción B: Con Mirror (KDS2 → Directo)

```
[KDS2 .NET] ◀──lectura──▶ [Backend Mirror Service]
     │                         │
     │  Tablas:                │ mapea a formato interno
     │  - Comandas             │ guarda timestamp en Redis
     │  - Distribucion         ▼
     │                    [Balancer] ──WebSocket──▶ [Pantallas KDS]
```

1. Backend lee órdenes del KDS2 cuando una pantalla las solicita
2. Mapea los datos al formato interno
3. Guarda timestamp en Redis para el timer
4. Envía las órdenes por WebSocket

**Nota:** En modo Mirror, las órdenes **NO** se guardan en PostgreSQL. Solo se leen del KDS2 y se muestran en tiempo real.

---

## Resumen Comparativo

| Característica | PostgreSQL | MaxPoint | KDS2 (Mirror) |
|----------------|------------|----------|---------------|
| **Motor** | PostgreSQL | SQL Server | SQL Server |
| **Acceso** | Lectura/Escritura | Solo Lectura | Solo Lectura |
| **Propósito** | Config + Órdenes locales | POS/Ventas | KDS .NET existente |
| **Driver** | Prisma ORM | mssql (tedious) | mssql (tedious) |
| **Polling** | No aplica | Cada 2 seg | En demanda |
| **Ubicación** | Docker local | Servidor externo | Servidor externo |
| **Persistencia** | Sí | N/A (solo lee) | Solo en Redis (timestamps) |

---

## Archivos Relacionados

| Archivo | Descripción |
|---------|-------------|
| `backend/prisma/schema.prisma` | Schema de PostgreSQL |
| `backend/src/config/database.ts` | Conexión a PostgreSQL |
| `backend/src/config/mxp.ts` | Conexión a MaxPoint |
| `backend/src/services/mxp.service.ts` | Servicio de polling MaxPoint |
| `backend/src/services/mirror-kds.service.ts` | Servicio Mirror KDS2 |
| `backend/src/services/order.service.ts` | Gestión de órdenes |
| `backend/src/services/balancer.service.ts` | Distribución de órdenes |

---

## Configuración desde Backoffice

La configuración de las conexiones externas (MaxPoint y Mirror) se puede hacer desde:
- **Backoffice → Configuración → Conexión MaxPoint**
- **Backoffice → Configuración → Mirror KDS**

Los datos se guardan en la tabla `GeneralConfig` de PostgreSQL.
