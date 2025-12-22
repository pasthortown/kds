# Estructura del Reporte de Dashboard KDS

Este documento describe la estructura completa del JSON retornado por el endpoint `/api/reports/dashboard`, utilizado para alimentar el Dashboard del Backoffice del sistema KDS.

---

## Índice

1. [Estructura General](#estructura-general)
2. [metadata](#1-metadata---metadatos-del-reporte)
3. [summary](#2-summary---resumen-ejecutivo)
4. [screenStatus](#3-screenstatus---estado-de-pantallas)
5. [highlights](#4-highlights---datos-destacados)
6. [byScreen](#5-byscreen---estadísticas-por-pantalla)
7. [byChannel](#6-bychannel---estadísticas-por-canal)
8. [hourlyStats](#7-hourlystats---distribución-horaria)
9. [screens](#8-screens---lista-de-pantallas)
10. [Ejemplo de Uso](#ejemplo-de-uso)

---

## Estructura General

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

## 1. `metadata` - Metadatos del Reporte

Información general sobre el contexto del reporte generado.

```json
{
  "metadata": {
    "id_restaurante": "K027",
    "generatedAt": "2025-12-22T03:55:51.670Z",
    "timeLimit": 5,
    "timezone": "America/Mexico_City",
    "reportDate": "domingo, 21 de diciembre de 2025"
  }
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_restaurante` | `string` | Identificador único del restaurante. Se configura mediante la variable de entorno `RESTAURANT_ID`. |
| `generatedAt` | `string` (ISO 8601) | Fecha y hora exacta de generación del reporte en formato UTC. |
| `timeLimit` | `number` | Tiempo límite en **minutos** usado para calcular si una orden está "a tiempo". Valor por defecto: 5. |
| `timezone` | `string` | Zona horaria del servidor donde se generó el reporte. |
| `reportDate` | `string` | Fecha del reporte en formato legible (localizado en español). |

### Uso en Dashboard
- `id_restaurante`: Mostrar en cabecera del dashboard para identificar el local.
- `generatedAt`: Mostrar hora de última actualización.
- `timeLimit`: Referencia para indicar el umbral de tiempo.

---

## 2. `summary` - Resumen Ejecutivo

Estadísticas consolidadas del día actual. Es la sección más importante para KPIs principales.

```json
{
  "summary": {
    "pending": 8,
    "inProgress": 0,
    "finishedToday": 153,
    "cancelledToday": 3,
    "onTime": 113,
    "outOfTime": 40,
    "avgFinishTime": 280,
    "minFinishTime": 62,
    "maxFinishTime": 889,
    "totalProcessed": 153,
    "onTimePercentage": 74,
    "outOfTimePercentage": 26
  }
}
```

| Campo | Tipo | Unidad | Descripción |
|-------|------|--------|-------------|
| `pending` | `number` | órdenes | Cantidad de órdenes actualmente pendientes (estado `PENDING`). |
| `inProgress` | `number` | órdenes | Cantidad de órdenes en progreso (estado `IN_PROGRESS`). |
| `finishedToday` | `number` | órdenes | Total de órdenes finalizadas hoy (estado `FINISHED`). |
| `cancelledToday` | `number` | órdenes | Total de órdenes canceladas hoy (estado `CANCELLED`). |
| `onTime` | `number` | órdenes | Órdenes finalizadas **dentro** del tiempo límite (`timeLimit`). |
| `outOfTime` | `number` | órdenes | Órdenes finalizadas **fuera** del tiempo límite. |
| `avgFinishTime` | `number` | segundos | Tiempo **promedio** de finalización de órdenes. |
| `minFinishTime` | `number` | segundos | Tiempo **mínimo** de finalización (orden más rápida del día). |
| `maxFinishTime` | `number` | segundos | Tiempo **máximo** de finalización (orden más lenta del día). |
| `totalProcessed` | `number` | órdenes | Total de órdenes procesadas = `onTime + outOfTime`. |
| `onTimePercentage` | `number` | % (0-100) | Porcentaje de órdenes finalizadas a tiempo. |
| `outOfTimePercentage` | `number` | % (0-100) | Porcentaje de órdenes finalizadas fuera de tiempo. |

### Fórmulas de Cálculo

```
totalProcessed = onTime + outOfTime
onTimePercentage = (onTime / totalProcessed) * 100
outOfTimePercentage = (outOfTime / totalProcessed) * 100
avgFinishTime = sum(finishTimes) / count(finishedOrders)
```

### Uso en Dashboard
- **Tarjetas principales**: `pending`, `finishedToday`, `onTimePercentage`
- **Gráfico de pastel**: `onTime` vs `outOfTime`
- **Indicadores de tiempo**: `avgFinishTime` (convertir a mm:ss), `minFinishTime`, `maxFinishTime`

---

## 3. `screenStatus` - Estado de Pantallas

Resumen del estado de conexión de todas las pantallas del sistema.

```json
{
  "screenStatus": {
    "online": 0,
    "offline": 4,
    "standby": 0,
    "total": 4
  }
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `online` | `number` | Pantallas activas y recibiendo heartbeat en tiempo real. |
| `offline` | `number` | Pantallas sin conexión (sin heartbeat en los últimos 30 segundos). |
| `standby` | `number` | Pantallas en modo espera/ahorro de energía. |
| `total` | `number` | Total de pantallas configuradas en el sistema. |

### Estados de Pantalla

| Estado | Descripción | Acción sugerida |
|--------|-------------|-----------------|
| `ONLINE` | Pantalla funcionando correctamente | Ninguna |
| `OFFLINE` | Sin comunicación con el servidor | Verificar conexión de red |
| `STANDBY` | Modo ahorro de energía activado | Normal si está fuera de horario |

### Uso en Dashboard
- **Indicador visual**: Semáforo o badges con colores (verde/rojo/amarillo).
- **Alerta**: Si `offline > 0` durante horario operativo.

---

## 4. `highlights` - Datos Destacados

Información relevante sobre rendimiento destacado del día. Útil para gamificación y análisis rápido.

```json
{
  "highlights": {
    "fastestOrder": { ... },
    "slowestOrder": { ... },
    "peakHour": { ... },
    "topChannel": { ... },
    "mostEfficientScreen": { ... }
  }
}
```

> **Nota**: Todos los campos de `highlights` pueden ser `null` si no hay datos suficientes.

---

### 4.1 `fastestOrder` - Orden Más Rápida

La orden que se finalizó en el menor tiempo durante el día.

```json
{
  "fastestOrder": {
    "id": "cmjgmf31a00kg6jvipgst75n3",
    "identifier": "1146",
    "channel": "Kiosko-Efectivo",
    "finishTime": 62,
    "items": [
      {
        "name": "Coca-Cola 500ml",
        "quantity": 2,
        "modifier": "Bebida: Sprite"
      }
    ]
  }
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | `string` | ID único de la orden en la base de datos. |
| `identifier` | `string` | Número de orden visible al cliente (ej: "1146", "A125"). |
| `channel` | `string` | Canal de venta por donde ingresó la orden. |
| `finishTime` | `number` | Tiempo de finalización en **segundos**. |
| `items` | `array` | Lista de productos de la orden. |
| `items[].name` | `string` | Nombre del producto. |
| `items[].quantity` | `number` | Cantidad del producto. |
| `items[].modifier` | `string \| null` | Modificador o nota especial (opcional). |

---

### 4.2 `slowestOrder` - Orden Más Lenta

La orden que tardó más tiempo en finalizarse. Misma estructura que `fastestOrder`.

```json
{
  "slowestOrder": {
    "id": "cmjgmf31400kd6jvimt28hv9g",
    "identifier": "1145",
    "channel": "APP",
    "finishTime": 889,
    "items": [
      {
        "name": "Papas Grandes",
        "quantity": 1,
        "modifier": "Sin cebolla"
      }
    ]
  }
}
```

### Uso en Dashboard
- Mostrar comparativa visual entre orden más rápida y más lenta.
- Identificar patrones de demora por tipo de producto o canal.

---

### 4.3 `peakHour` - Hora Pico

La hora del día con mayor volumen de órdenes procesadas.

```json
{
  "peakHour": {
    "hour": 10,
    "hourLabel": "10:00",
    "total": 15,
    "onTime": 12,
    "outOfTime": 3
  }
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `hour` | `number` | Hora del día en formato 24h (0-23). |
| `hourLabel` | `string` | Etiqueta formateada para mostrar (ej: "10:00"). |
| `total` | `number` | Total de órdenes finalizadas en esa hora. |
| `onTime` | `number` | Órdenes a tiempo en esa hora. |
| `outOfTime` | `number` | Órdenes fuera de tiempo en esa hora. |

---

### 4.4 `topChannel` - Canal Más Activo

El canal de venta con mayor volumen de órdenes del día.

```json
{
  "topChannel": {
    "channel": "PedidosYa",
    "total": 25,
    "onTime": 16,
    "outOfTime": 9,
    "avgFinishTime": 315
  }
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `channel` | `string` | Nombre del canal de venta. |
| `total` | `number` | Total de órdenes del canal. |
| `onTime` | `number` | Órdenes a tiempo. |
| `outOfTime` | `number` | Órdenes fuera de tiempo. |
| `avgFinishTime` | `number` | Tiempo promedio de finalización en segundos. |

---

### 4.5 `mostEfficientScreen` - Pantalla Más Eficiente

La pantalla con mayor porcentaje de órdenes a tiempo (mínimo 5 órdenes procesadas).

```json
{
  "mostEfficientScreen": {
    "screenId": "cmjgme1gx000pvsfooijliqq7",
    "screenName": "Pantalla3",
    "queueName": "LINEAS",
    "pending": 0,
    "finishedToday": 32,
    "onTime": 26,
    "outOfTime": 6,
    "avgFinishTime": 244,
    "efficiencyRate": 81
  }
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `screenId` | `string` | ID único de la pantalla. |
| `screenName` | `string` | Nombre descriptivo de la pantalla. |
| `queueName` | `string` | Nombre de la cola asignada. |
| `pending` | `number` | Órdenes pendientes actualmente asignadas. |
| `finishedToday` | `number` | Órdenes finalizadas hoy. |
| `onTime` | `number` | Órdenes a tiempo. |
| `outOfTime` | `number` | Órdenes fuera de tiempo. |
| `avgFinishTime` | `number` | Tiempo promedio en segundos. |
| `efficiencyRate` | `number` | Tasa de eficiencia (0-100%). |

---

## 5. `byScreen` - Estadísticas por Pantalla

Array con estadísticas detalladas de rendimiento de cada pantalla.

```json
{
  "byScreen": [
    {
      "screenId": "cmjgme1hw0010vsfoaloyq47j",
      "screenName": "Pantalla4",
      "queueName": "LINEAS",
      "pending": 3,
      "finishedToday": 38,
      "onTime": 30,
      "outOfTime": 8,
      "avgFinishTime": 252,
      "efficiencyRate": 79
    }
  ]
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `screenId` | `string` | ID único de la pantalla. |
| `screenName` | `string` | Nombre de la pantalla. |
| `queueName` | `string` | Cola a la que pertenece. |
| `pending` | `number` | Órdenes pendientes asignadas actualmente. |
| `finishedToday` | `number` | Órdenes finalizadas hoy. |
| `onTime` | `number` | Órdenes a tiempo. |
| `outOfTime` | `number` | Órdenes fuera de tiempo. |
| `avgFinishTime` | `number` | Tiempo promedio en segundos. |
| `efficiencyRate` | `number` | Porcentaje de eficiencia (0-100). |

### Fórmula de Eficiencia

```
efficiencyRate = (onTime / finishedToday) * 100
```

### Uso en Dashboard
- **Tabla comparativa**: Ranking de pantallas por eficiencia.
- **Gráfico de barras**: Comparar `onTime` vs `outOfTime` por pantalla.
- **Indicador de carga**: Mostrar `pending` para balance de trabajo.

---

## 6. `byChannel` - Estadísticas por Canal

Array con estadísticas de rendimiento por cada canal de venta.

```json
{
  "byChannel": [
    {
      "channel": "PedidosYa",
      "total": 25,
      "onTime": 16,
      "outOfTime": 9,
      "avgFinishTime": 315,
      "efficiencyRate": 64
    },
    {
      "channel": "Local",
      "total": 25,
      "onTime": 20,
      "outOfTime": 5,
      "avgFinishTime": 251,
      "efficiencyRate": 80
    }
  ]
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `channel` | `string` | Nombre del canal de venta. |
| `total` | `number` | Total de órdenes procesadas del canal. |
| `onTime` | `number` | Órdenes a tiempo. |
| `outOfTime` | `number` | Órdenes fuera de tiempo. |
| `avgFinishTime` | `number` | Tiempo promedio en segundos. |
| `efficiencyRate` | `number` | Porcentaje de eficiencia (0-100). |

### Canales Típicos

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

### Uso en Dashboard
- **Gráfico de pastel**: Distribución de órdenes por canal.
- **Tabla**: Ranking de canales por eficiencia o volumen.
- **Análisis**: Identificar canales problemáticos (baja eficiencia).

---

## 7. `hourlyStats` - Distribución Horaria

Array con distribución de órdenes por cada hora del día.

```json
{
  "hourlyStats": [
    {
      "hour": 10,
      "total": 15,
      "onTime": 12,
      "outOfTime": 3,
      "hourLabel": "10:00",
      "efficiencyRate": 80
    },
    {
      "hour": 11,
      "total": 15,
      "onTime": 10,
      "outOfTime": 5,
      "hourLabel": "11:00",
      "efficiencyRate": 67
    }
  ]
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `hour` | `number` | Hora del día en formato 24h (0-23). |
| `total` | `number` | Total de órdenes finalizadas en esa hora. |
| `onTime` | `number` | Órdenes a tiempo. |
| `outOfTime` | `number` | Órdenes fuera de tiempo. |
| `hourLabel` | `string` | Etiqueta formateada ("06:00", "11:00", etc.). |
| `efficiencyRate` | `number` | Porcentaje de eficiencia (0-100). |

### Uso en Dashboard
- **Gráfico de líneas/barras**: Mostrar volumen de órdenes por hora.
- **Mapa de calor**: Identificar horas pico visualmente.
- **Análisis**: Detectar horas con baja eficiencia para planificación de personal.

---

## 8. `screens` - Lista de Pantallas

Array con información del estado actual de cada pantalla configurada.

```json
{
  "screens": [
    {
      "id": "cmjgme1el0003vsfoq5smdm80",
      "number": 1,
      "name": "Pantalla1",
      "url": "/kds1",
      "queueName": "LINEAS",
      "status": "OFFLINE",
      "lastHeartbeat": "2025-12-22T03:54:01.529Z"
    }
  ]
}
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | `string` | ID único de la pantalla. |
| `number` | `number` | Número de pantalla (1, 2, 3...). |
| `name` | `string` | Nombre descriptivo. |
| `url` | `string` | URL de acceso a la pantalla KDS (ej: `/kds1`). |
| `queueName` | `string` | Cola asignada a la pantalla. |
| `status` | `string` | Estado actual: `ONLINE`, `OFFLINE`, `STANDBY`. |
| `lastHeartbeat` | `string \| null` | Último heartbeat recibido (ISO 8601). `null` si nunca conectó. |

### Uso en Dashboard
- **Panel de monitoreo**: Mostrar estado de cada pantalla con indicador visual.
- **Acceso rápido**: Links a cada pantalla KDS.
- **Diagnóstico**: Tiempo desde último heartbeat para detectar problemas.

---

## Ejemplo de Uso

### Petición

```bash
curl -X GET "http://localhost:3000/api/reports/dashboard?timeLimit=5" \
  -H "Authorization: Bearer <token>"
```

### Parámetros de Query

| Parámetro | Tipo | Default | Descripción |
|-----------|------|---------|-------------|
| `timeLimit` | `number` | 5 | Tiempo límite en minutos para considerar una orden "a tiempo". |

### Código de Ejemplo (TypeScript)

```typescript
interface DashboardReport {
  metadata: ReportMetadata;
  summary: ReportSummary;
  screenStatus: ScreenStatusSummary;
  highlights: ReportHighlights;
  byScreen: ScreenStats[];
  byChannel: ChannelStats[];
  hourlyStats: HourlyStats[];
  screens: ScreenInfo[];
}

// Obtener reporte
const response = await fetch('/api/reports/dashboard?timeLimit=5', {
  headers: { 'Authorization': `Bearer ${token}` }
});
const report: DashboardReport = await response.json();

// Mostrar KPIs principales
console.log(`Órdenes del día: ${report.summary.finishedToday}`);
console.log(`Eficiencia: ${report.summary.onTimePercentage}%`);
console.log(`Tiempo promedio: ${Math.floor(report.summary.avgFinishTime / 60)}:${report.summary.avgFinishTime % 60}`);

// Identificar hora pico
if (report.highlights.peakHour) {
  console.log(`Hora pico: ${report.highlights.peakHour.hourLabel} con ${report.highlights.peakHour.total} órdenes`);
}

// Pantalla más eficiente
if (report.highlights.mostEfficientScreen) {
  console.log(`Mejor pantalla: ${report.highlights.mostEfficientScreen.screenName} (${report.highlights.mostEfficientScreen.efficiencyRate}%)`);
}
```

---

## Conversiones Útiles

### Segundos a formato mm:ss

```typescript
function formatTime(seconds: number): string {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

// Ejemplo: formatTime(280) => "04:40"
```

### Determinar color por eficiencia

```typescript
function getEfficiencyColor(rate: number): string {
  if (rate >= 80) return 'green';   // Excelente
  if (rate >= 60) return 'yellow';  // Aceptable
  return 'red';                      // Crítico
}
```

---

## Notas Importantes

1. **Zona horaria**: Todos los timestamps están en UTC. El campo `metadata.timezone` indica la zona horaria del servidor para referencia.

2. **Valores nulos**: Los campos en `highlights` pueden ser `null` si no hay datos suficientes (ej: ninguna orden finalizada).

3. **Eficiencia mínima**: Para `mostEfficientScreen` se requieren al menos 5 órdenes finalizadas para evitar estadísticas sesgadas.

4. **Actualización**: Se recomienda refrescar el dashboard cada 30-60 segundos para mantener datos actualizados.

5. **Cache**: El endpoint no tiene cache interno; cada llamada consulta datos en tiempo real.
