# Impresion Directa (Modo LOCAL)

## Descripcion General

El modo de **Impresion Directa** permite que el sistema KDS envie los datos de impresion directamente a las impresoras termicas via protocolo **TCP/IP** sin intermediarios. La comunicacion se realiza utilizando el protocolo **ESC/POS**, estandar de la industria para impresoras de punto de venta.

## Arquitectura

```
+-------------+       TCP Socket       +------------------+
|   Backend   | --------------------> |  Impresora       |
|   (Node.js) |     Puerto 9100       |  Termica ESC/POS |
+-------------+                        +------------------+
```

## Servicio Principal

**Archivo:** `backend/src/services/printer.service.ts`

### Clase: `PrinterService`

El servicio maneja la conexion TCP directa y el formateo ESC/POS.

## Configuracion

### Parametros por Defecto

| Parametro | Valor | Descripcion |
|-----------|-------|-------------|
| `retries` | 3 | Intentos de impresion antes de fallar |
| `timeout` | 5000ms | Tiempo maximo de espera por conexion |
| `port` | 9100 | Puerto estandar de impresoras TCP |

### Configuracion en Base de Datos

**Tabla:** `GeneralConfig`

```
printMode: 'LOCAL'
printTcp: true
printRetries: 3
```

**Tabla:** `Printer` (por pantalla)

```
screenId: String (FK)
name: String
ip: String (ej: "192.168.1.100")
port: Int (default: 9100)
enabled: Boolean
```

## Flujo de Impresion

1. **Orden finalizada:** El usuario marca la orden como FINISHED en la pantalla
2. **WebSocket recibe evento:** `order:finish`
3. **Verificar modo:** Se consulta `printMode` en configuracion general
4. **Si es LOCAL:**
   - Se obtiene configuracion de impresora de la pantalla
   - Se formatea la orden en ESC/POS
   - Se abre socket TCP a IP:Puerto
   - Se envian los bytes
5. **Registro:** Se actualiza `printedAt` en la orden
6. **Reintentos:** Si falla, se reintenta con delay progresivo (1s, 2s, 3s)

## Metodos Principales

### `printOrder(order, printerConfig)`

Punto de entrada principal. Detecta el modo y delega.

```typescript
async printOrder(order: Order, printerConfig: PrinterConfig): Promise<boolean>
```

### `printOrderLocal(order, printerConfig)`

Ejecuta la impresion TCP directa.

```typescript
async printOrderLocal(order: Order, printerConfig: PrinterConfig): Promise<boolean>
```

### `sendToPrinter(ip, port, content)`

Abre el socket TCP y envia los datos.

```typescript
private sendToPrinter(ip: string, port: number, content: Buffer): Promise<void>
```

### `formatOrderForPrint(order)`

Convierte la orden a formato ESC/POS.

```typescript
private formatOrderForPrint(order: Order): Buffer
```

## Formato ESC/POS

El sistema genera comandos ESC/POS estandar:

### Comandos Utilizados

| Comando | Hex | Funcion |
|---------|-----|---------|
| ESC @ | `\x1B@` | Reset impresora |
| ESC a 1 | `\x1Ba\x01` | Centrar texto |
| ESC a 0 | `\x1Ba\x00` | Alinear izquierda |
| GS ! 17 | `\x1D!\x11` | Doble alto y ancho |
| GS ! 0 | `\x1D!\x00` | Tamano normal |
| GS V A 3 | `\x1DVA\x03` | Corte parcial de papel |

### Estructura del Ticket

```
[Reset impresora]
[Centrar]
[Doble tamaño]
ORDEN 001
[Tamaño normal]

[Alinear izquierda]
Canal: UBER
Cliente: Juan Perez

--------------------------------

2x Big Mac
  + Papas grandes
  * Sin cebolla

--------------------------------

Hora: 14:35

[Corte de papel]
```

## Prueba de Impresora

### Metodo `testPrinter(ip, port)`

Permite verificar conectividad antes de guardar configuracion.

```typescript
async testPrinter(ip: string, port: number): Promise<boolean>
```

Envia un comando de prueba con corte de papel.

### Endpoint REST

```
POST /api/screens/:id/printer/test
Body: { "ip": "192.168.1.100", "port": 9100 }
```

## Manejo de Errores

### Reintentos Exponenciales

Si la impresion falla:
1. Intento 1: Espera 1 segundo
2. Intento 2: Espera 2 segundos
3. Intento 3: Fallo definitivo

### Errores Comunes

| Error | Causa | Solucion |
|-------|-------|----------|
| `Connection timeout` | Impresora apagada o IP incorrecta | Verificar IP y estado |
| `Connection refused` | Puerto bloqueado o servicio no activo | Verificar puerto 9100 |
| `ECONNRESET` | Conexion interrumpida | Verificar cable de red |

## Ventajas del Modo LOCAL

- **Latencia minima:** Conexion directa sin intermediarios
- **Sin dependencias externas:** No requiere servicios adicionales
- **Simplicidad:** Arquitectura simple de mantener
- **Control total:** El sistema KDS controla el formato ESC/POS

## Desventajas del Modo LOCAL

- **Requiere acceso de red directo:** El backend debe alcanzar la IP de la impresora
- **Sin cache ni cola:** Si la impresora falla, se pierden ordenes
- **Formato fijo:** El formato ESC/POS esta hardcodeado en el servicio

## Requisitos de Red

- El servidor backend debe tener acceso TCP al puerto 9100 de cada impresora
- Las impresoras deben estar en la misma red o tener rutas configuradas
- Firewalls deben permitir conexiones salientes al puerto 9100

## Ejemplo de Uso

### Configurar Impresora para Pantalla

```http
PUT /api/screens/screen-001/printer
Content-Type: application/json

{
  "name": "Cocina Principal",
  "ip": "192.168.1.100",
  "port": 9100,
  "enabled": true
}
```

### Cambiar a Modo LOCAL

```http
PUT /api/config/modes
Content-Type: application/json

{
  "printMode": "LOCAL"
}
```
