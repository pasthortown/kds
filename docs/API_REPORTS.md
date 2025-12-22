# API de Reportes del Dashboard

Este documento describe el endpoint de reportes del Dashboard del sistema KDS.

---

## Endpoint Principal

### `GET /api/reports/dashboard`

Obtiene todos los datos necesarios para generar reportes del dashboard, incluyendo estadísticas de órdenes, rendimiento por pantalla, por canal y distribución horaria.

#### Autenticación

Requiere token JWT en el header `Authorization`.

```
Authorization: Bearer <token>
```

#### Parámetros de Query

| Parámetro | Tipo | Requerido | Default | Descripción |
|-----------|------|-----------|---------|-------------|
| `timeLimit` | number | No | 5 | Tiempo límite en minutos para considerar una orden "a tiempo" |

#### Ejemplo de Petición

```bash
curl -X GET "http://localhost:3000/api/reports/dashboard?timeLimit=5" \
  -H "Authorization: Bearer <token>"
```

---

## Estructura de la Respuesta JSON

```json
{
  "metadata": { ... },
  "summary": { ... },
  "screenStatus": { ... },
  "highlights": { ... },
  "byScreen": [ ... ],
  "byChannel": [ ... ],
  "hourlyStats": [ ... ],
  "screens": [ ... ]
}
```

---

## Detalle de Cada Sección

### 1. `metadata` - Metadatos del Reporte

Información general sobre el reporte generado.

```json
{
  "metadata": {
    "id_restaurante": "K027",
    "generatedAt": "2025-12-19T22:54:03.630Z",
    "timeLimit": 5,
    "timezone": "America/Mexico_City",
    "reportDate": "viernes, 19 de diciembre de 2025"
  }
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_restaurante` | string | Identificador único del restaurante (configurado en variable de entorno `RESTAURANT_ID`) |
| `generatedAt` | string (ISO 8601) | Fecha y hora exacta de generación del reporte en formato UTC |
| `timeLimit` | number | Tiempo límite en minutos usado para calcular órdenes "a tiempo" |
| `timezone` | string | Zona horaria del servidor donde se generó el reporte |
| `reportDate` | string | Fecha del reporte en formato legible (español Ecuador) |

---

### 2. `summary` - Resumen Ejecutivo

Estadísticas generales consolidadas del día actual.

```json
{
  "summary": {
    "pending": 19,
    "inProgress": 0,
    "finishedToday": 90,
    "cancelledToday": 3,
    "onTime": 58,
    "outOfTime": 32,
    "avgFinishTime": 325,
    "minFinishTime": 61,
    "maxFinishTime": 878,
    "totalProcessed": 90,
    "onTimePercentage": 64,
    "outOfTimePercentage": 36
  }
}
```

| Campo | Tipo | Unidad | Descripción |
|-------|------|--------|-------------|
| `pending` | number | órdenes | Cantidad de órdenes pendientes (estado `PENDING`) |
| `inProgress` | number | órdenes | Cantidad de órdenes en progreso (estado `IN_PROGRESS`) |
| `finishedToday` | number | órdenes | Total de órdenes finalizadas hoy (estado `FINISHED`) |
| `cancelledToday` | number | órdenes | Total de órdenes canceladas hoy (estado `CANCELLED`) |
| `onTime` | number | órdenes | Órdenes finalizadas dentro del tiempo límite |
| `outOfTime` | number | órdenes | Órdenes finalizadas fuera del tiempo límite |
| `avgFinishTime` | number | segundos | Tiempo promedio de finalización de órdenes |
| `minFinishTime` | number | segundos | Tiempo mínimo de finalización (orden más rápida) |
| `maxFinishTime` | number | segundos | Tiempo máximo de finalización (orden más lenta) |
| `totalProcessed` | number | órdenes | Total de órdenes procesadas (`onTime + outOfTime`) |
| `onTimePercentage` | number | porcentaje | Porcentaje de órdenes a tiempo (0-100) |
| `outOfTimePercentage` | number | porcentaje | Porcentaje de órdenes fuera de tiempo (0-100) |

#### Fórmulas de Cálculo

```
totalProcessed = onTime + outOfTime
onTimePercentage = (onTime / totalProcessed) * 100
outOfTimePercentage = (outOfTime / totalProcessed) * 100
avgFinishTime = sum(finishTimes) / count(finishedOrders)
```

---

### 3. `screenStatus` - Estado de Pantallas

Resumen del estado actual de todas las pantallas del sistema.

```json
{
  "screenStatus": {
    "online": 4,
    "offline": 0,
    "standby": 0,
    "total": 4
  }
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `online` | number | Pantallas activas y recibiendo heartbeat |
| `offline` | number | Pantallas sin conexión (sin heartbeat reciente) |
| `standby` | number | Pantallas en modo espera/ahorro de energía |
| `total` | number | Total de pantallas configuradas en el sistema |

---

### 4. `highlights` - Datos Destacados

Información relevante sobre órdenes y rendimiento destacado.

```json
{
  "highlights": {
    "fastestOrder": {
      "id": "cmjdfl0gr0095ixf59995j4t9",
      "identifier": "1064",
      "channel": "Local",
      "finishTime": 61,
      "items": [
        { "name": "Nuggets x20", "quantity": 3, "modifier": "Extra crispy" },
        { "name": "Pollo Original 8pcs", "quantity": 1, "modifier": "Sin cebolla" }
      ]
    },
    "slowestOrder": {
      "id": "cmjdfl0cd003qixf57g033lwa",
      "identifier": "1027",
      "channel": "RAPPI",
      "finishTime": 878,
      "items": [
        { "name": "Twister Clasico", "quantity": 2 },
        { "name": "Pollo Original 8pcs", "quantity": 2, "modifier": "Bebida: Sprite" }
      ]
    },
    "peakHour": {
      "hour": 11,
      "hourLabel": "11:00",
      "total": 15,
      "onTime": 13,
      "outOfTime": 2
    },
    "topChannel": {
      "channel": "Drive",
      "total": 16,
      "onTime": 9,
      "outOfTime": 7,
      "avgFinishTime": 337
    },
    "mostEfficientScreen": {
      "screenId": "cmjd4sh8x0013netnt62poubw",
      "screenName": "Pantalla2",
      "queueName": "LINEAS",
      "pending": 7,
      "finishedToday": 18,
      "onTime": 13,
      "outOfTime": 5,
      "avgFinishTime": 296,
      "efficiencyRate": 72
    }
  }
}
```

#### 4.1 `fastestOrder` - Orden Más Rápida

La orden que se finalizó en menor tiempo durante el día.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | string | ID único de la orden en la base de datos |
| `identifier` | string | Número de orden visible (ej: "1064", "A125") |
| `channel` | string | Canal de venta (Local, RAPPI, Drive, etc.) |
| `finishTime` | number | Tiempo de finalización en segundos |
| `items` | array | Lista de productos de la orden |
| `items[].name` | string | Nombre del producto |
| `items[].quantity` | number | Cantidad del producto |
| `items[].modifier` | string? | Modificador o nota especial (opcional) |

#### 4.2 `slowestOrder` - Orden Más Lenta

La orden que tardó más tiempo en finalizarse. Misma estructura que `fastestOrder`.

#### 4.3 `peakHour` - Hora Pico

La hora del día con mayor cantidad de órdenes.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `hour` | number | Hora del día (0-23) |
| `hourLabel` | string | Etiqueta formateada (ej: "11:00") |
| `total` | number | Total de órdenes en esa hora |
| `onTime` | number | Órdenes a tiempo en esa hora |
| `outOfTime` | number | Órdenes fuera de tiempo en esa hora |

#### 4.4 `topChannel` - Canal Más Activo

El canal de venta con mayor volumen de órdenes.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `channel` | string | Nombre del canal |
| `total` | number | Total de órdenes del canal |
| `onTime` | number | Órdenes a tiempo |
| `outOfTime` | number | Órdenes fuera de tiempo |
| `avgFinishTime` | number | Tiempo promedio de finalización (segundos) |

#### 4.5 `mostEfficientScreen` - Pantalla Más Eficiente

La pantalla con mayor porcentaje de órdenes a tiempo (mínimo 5 órdenes).

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `screenId` | string | ID único de la pantalla |
| `screenName` | string | Nombre de la pantalla |
| `queueName` | string | Nombre de la cola asignada |
| `pending` | number | Órdenes pendientes actuales |
| `finishedToday` | number | Órdenes finalizadas hoy |
| `onTime` | number | Órdenes a tiempo |
| `outOfTime` | number | Órdenes fuera de tiempo |
| `avgFinishTime` | number | Tiempo promedio (segundos) |
| `efficiencyRate` | number | Tasa de eficiencia (0-100%) |

> **Nota:** Todos los campos de `highlights` pueden ser `null` si no hay datos disponibles.

---

### 5. `byScreen` - Estadísticas por Pantalla

Array con estadísticas detalladas de cada pantalla.

```json
{
  "byScreen": [
    {
      "screenId": "cmjd4sh8d000rnetnn2091zst",
      "screenName": "Pantalla1",
      "queueName": "LINEAS",
      "pending": 5,
      "finishedToday": 25,
      "onTime": 15,
      "outOfTime": 10,
      "avgFinishTime": 313,
      "efficiencyRate": 60
    },
    {
      "screenId": "cmjd4sh8x0013netnt62poubw",
      "screenName": "Pantalla2",
      "queueName": "LINEAS",
      "pending": 7,
      "finishedToday": 18,
      "onTime": 13,
      "outOfTime": 5,
      "avgFinishTime": 296,
      "efficiencyRate": 72
    }
  ]
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `screenId` | string | ID único de la pantalla |
| `screenName` | string | Nombre de la pantalla |
| `queueName` | string | Cola a la que pertenece |
| `pending` | number | Órdenes pendientes asignadas |
| `finishedToday` | number | Órdenes finalizadas hoy |
| `onTime` | number | Órdenes a tiempo |
| `outOfTime` | number | Órdenes fuera de tiempo |
| `avgFinishTime` | number | Tiempo promedio (segundos) |
| `efficiencyRate` | number | Porcentaje de eficiencia (0-100) |

#### Fórmula de Eficiencia

```
efficiencyRate = (onTime / finishedToday) * 100
```

---

### 6. `byChannel` - Estadísticas por Canal

Array con estadísticas de cada canal de venta.

```json
{
  "byChannel": [
    {
      "channel": "Drive",
      "total": 16,
      "onTime": 9,
      "outOfTime": 7,
      "avgFinishTime": 337,
      "efficiencyRate": 56
    },
    {
      "channel": "Kiosko-Tarjeta",
      "total": 8,
      "onTime": 6,
      "outOfTime": 2,
      "avgFinishTime": 292,
      "efficiencyRate": 75
    }
  ]
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `channel` | string | Nombre del canal de venta |
| `total` | number | Total de órdenes del canal hoy |
| `onTime` | number | Órdenes a tiempo |
| `outOfTime` | number | Órdenes fuera de tiempo |
| `avgFinishTime` | number | Tiempo promedio (segundos) |
| `efficiencyRate` | number | Porcentaje de eficiencia (0-100) |

#### Canales Comunes

| Canal | Descripción |
|-------|-------------|
| `Local` | Órdenes para consumo en el local |
| `Llevar` | Órdenes para llevar |
| `Drive` | Órdenes de autoservicio |
| `Kiosko-Efectivo` | Kiosko de autoatención (pago efectivo) |
| `Kiosko-Tarjeta` | Kiosko de autoatención (pago tarjeta) |
| `RAPPI` | Órdenes de la app Rappi |
| `PedidosYa` | Órdenes de PedidosYa |
| `APP` | Órdenes de la app propia |
| `CALLCENTER` | Órdenes por teléfono |

---

### 7. `hourlyStats` - Estadísticas por Hora

Array con distribución de órdenes por hora del día.

```json
{
  "hourlyStats": [
    {
      "hour": 6,
      "hourLabel": "06:00",
      "total": 5,
      "onTime": 4,
      "outOfTime": 1,
      "efficiencyRate": 80
    },
    {
      "hour": 11,
      "hourLabel": "11:00",
      "total": 15,
      "onTime": 13,
      "outOfTime": 2,
      "efficiencyRate": 87
    },
    {
      "hour": 12,
      "hourLabel": "12:00",
      "total": 14,
      "onTime": 9,
      "outOfTime": 5,
      "efficiencyRate": 64
    }
  ]
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `hour` | number | Hora del día (0-23) |
| `hourLabel` | string | Etiqueta formateada ("06:00", "11:00", etc.) |
| `total` | number | Total de órdenes en esa hora |
| `onTime` | number | Órdenes a tiempo |
| `outOfTime` | number | Órdenes fuera de tiempo |
| `efficiencyRate` | number | Porcentaje de eficiencia (0-100) |

> **Nota:** Solo se incluyen horas con actividad o dentro del horario laboral (6:00 - 23:00).

---

### 8. `screens` - Lista de Pantallas

Array con información del estado actual de cada pantalla.

```json
{
  "screens": [
    {
      "id": "cmjd4sh8d000rnetnn2091zst",
      "number": 1,
      "name": "Pantalla1",
      "url": "/kds1",
      "queueName": "LINEAS",
      "status": "ONLINE",
      "lastHeartbeat": "2025-12-19T22:53:45.123Z"
    },
    {
      "id": "cmjd4sh8x0013netnt62poubw",
      "number": 2,
      "name": "Pantalla2",
      "url": "/kds2",
      "queueName": "LINEAS",
      "status": "ONLINE",
      "lastHeartbeat": "2025-12-19T22:53:42.456Z"
    }
  ]
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | string | ID único de la pantalla |
| `number` | number | Número de pantalla (1, 2, 3...) |
| `name` | string | Nombre descriptivo |
| `url` | string | URL de acceso a la pantalla KDS |
| `queueName` | string | Cola asignada |
| `status` | string | Estado actual: `ONLINE`, `OFFLINE`, `STANDBY` |
| `lastHeartbeat` | string \| null | Último heartbeat recibido (ISO 8601) |

#### Estados de Pantalla

| Estado | Descripción |
|--------|-------------|
| `ONLINE` | Pantalla activa y funcionando |
| `OFFLINE` | Sin conexión (no envía heartbeat) |
| `STANDBY` | En modo espera/ahorro de energía |

---

## Ejemplo de Respuesta Completa

```json
{
  "metadata": {
    "id_restaurante": "K027",
    "generatedAt": "2025-12-19T22:54:03.630Z",
    "timeLimit": 5,
    "timezone": "America/Mexico_City",
    "reportDate": "viernes, 19 de diciembre de 2025"
  },
  "summary": {
    "pending": 19,
    "inProgress": 0,
    "finishedToday": 90,
    "cancelledToday": 3,
    "onTime": 58,
    "outOfTime": 32,
    "avgFinishTime": 325,
    "minFinishTime": 61,
    "maxFinishTime": 878,
    "totalProcessed": 90,
    "onTimePercentage": 64,
    "outOfTimePercentage": 36
  },
  "screenStatus": {
    "online": 4,
    "offline": 0,
    "standby": 0,
    "total": 4
  },
  "highlights": {
    "fastestOrder": { ... },
    "slowestOrder": { ... },
    "peakHour": { ... },
    "topChannel": { ... },
    "mostEfficientScreen": { ... }
  },
  "byScreen": [ ... ],
  "byChannel": [ ... ],
  "hourlyStats": [ ... ],
  "screens": [ ... ]
}
```

---

## Endpoint Secundario

### `GET /api/reports/daily-summary`

Obtiene un resumen simplificado de un día específico.

#### Parámetros

| Parámetro | Tipo | Requerido | Default | Descripción |
|-----------|------|-----------|---------|-------------|
| `date` | string (YYYY-MM-DD) | No | Hoy | Fecha del resumen |

#### Ejemplo

```bash
curl -X GET "http://localhost:3000/api/reports/daily-summary?date=2025-12-19" \
  -H "Authorization: Bearer <token>"
```

#### Respuesta

```json
{
  "date": "2025-12-19",
  "dateFormatted": "viernes, 19 de diciembre de 2025",
  "totals": {
    "total": 112,
    "finished": 90,
    "pending": 19,
    "cancelled": 3
  },
  "times": {
    "avgFinishTime": 325,
    "minFinishTime": 61,
    "maxFinishTime": 878
  },
  "byChannel": [
    { "channel": "Drive", "count": 16 },
    { "channel": "CALLCENTER", "count": 14 },
    { "channel": "RAPPI", "count": 13 }
  ]
}
```

---

## Códigos de Error

| Código | Descripción |
|--------|-------------|
| 401 | Token no proporcionado o inválido |
| 403 | Sin permisos para acceder al recurso |
| 500 | Error interno del servidor |

---

## Configuración

### Variable de Entorno

El `id_restaurante` se configura mediante la variable de entorno:

```env
RESTAURANT_ID=K027
```

Esta variable debe estar definida en:
- `.env` (raíz del proyecto) - para Docker
- `backend/.env` - para desarrollo local

---

## Uso en Frontend

```typescript
import { reportsApi } from '../services/api';

// Obtener reporte del dashboard
const response = await reportsApi.getDashboardReport(5);
const report = response.data;

console.log(report.metadata.id_restaurante); // "K027"
console.log(report.summary.onTimePercentage); // 64
console.log(report.highlights.peakHour?.hourLabel); // "11:00"
```
