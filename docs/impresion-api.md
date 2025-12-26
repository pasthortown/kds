# Impresion por API (Modo CENTRALIZED)

## Descripcion General

El modo de **Impresion Centralizada** permite que el sistema KDS envie las ordenes a un **servicio HTTP externo** que se encarga de la impresion fisica. Este modo es compatible con el servicio de impresion .NET utilizado en implementaciones anteriores.

## Arquitectura

```
+-------------+     HTTP POST      +------------------+     TCP      +------------------+
|   Backend   | ----------------> |  Servicio .NET   | ----------> |  Impresora       |
|   (Node.js) |    JSON Payload   |  (Windows)       |  ESC/POS    |  Termica         |
+-------------+                    +------------------+             +------------------+
```

## Servicio Principal

**Archivo:** `backend/src/services/centralized-printer.service.ts`

### Clase: `CentralizedPrinterService`

El servicio convierte las ordenes al formato del servicio .NET y las envia via HTTP POST.

## Configuracion

### Parametros por Defecto

| Parametro | Valor | Descripcion |
|-----------|-------|-------------|
| `retries` | 3 | Intentos de envio antes de fallar |
| `timeout` | 10000ms | Tiempo maximo de espera HTTP |
| `port` | 5000 | Puerto por defecto del servicio .NET |

### Configuracion en Base de Datos

**Tabla:** `GeneralConfig`

```
printMode: 'CENTRALIZED'
centralizedPrintUrl: 'http://192.168.1.50:5000/api/ImpresionTickets/Impresion'
centralizedPrintPort: 5000
```

## Flujo de Impresion

1. **Orden finalizada:** El usuario marca la orden como FINISHED
2. **WebSocket recibe evento:** `order:finish`
3. **Verificar modo:** Se consulta `printMode` en configuracion general
4. **Si es CENTRALIZED:**
   - Se obtiene URL del servicio centralizado
   - Se convierte la orden al formato JSON compatible .NET
   - Se envia HTTP POST al servicio
5. **Servicio .NET procesa:** Recibe el JSON y envia a la impresora
6. **Registro:** Se actualiza `printedAt` en la orden

## Estructura del Payload

El servicio envia un JSON estructurado compatible con el sistema .NET anterior:

### Interface `CentralizedPrintPayload`

```typescript
interface CentralizedPrintPayload {
  comanda: {
    id: string;
    orderId: string;
    createdAt: string;
    channel: {
      id: number;
      name: string;
      type: string;
    };
    cashRegister: {
      cashier: string;
      name: string;
    };
    customer: {
      name: string;
    };
    products: CentralizedProduct[];
    otrosDatos: {
      turno: number | string;
      nroCheque: string;
      llamarPor: string;
      Fecha: string;
      Direccion: string;
    };
  };
  configuracion: {
    columnas: number;
    impresora: string;
    impresoraIP: string;
    impresoraPuerto: number;
  };
}
```

### Ejemplo de Payload

```json
{
  "comanda": {
    "id": "clx123abc",
    "orderId": "EXT-001",
    "createdAt": "2025-01-15T14:30:00.000Z",
    "channel": {
      "id": 1,
      "name": "UBER",
      "type": "DELIVERY"
    },
    "cashRegister": {
      "cashier": "",
      "name": ""
    },
    "customer": {
      "name": "Juan Perez"
    },
    "products": [
      {
        "productId": "prod-001",
        "name": "Big Mac",
        "amount": 2,
        "category": "",
        "content": ["Sin cebolla"],
        "products": [
          {
            "productId": "sub-001",
            "name": "Papas grandes",
            "amount": 1,
            "content": []
          }
        ]
      }
    ],
    "otrosDatos": {
      "turno": -1,
      "nroCheque": "001",
      "llamarPor": "Juan Perez",
      "Fecha": "15/1/2025, 14:30:00",
      "Direccion": ""
    }
  },
  "configuracion": {
    "columnas": 42,
    "impresora": "Cocina Principal",
    "impresoraIP": "192.168.1.100",
    "impresoraPuerto": 9100
  }
}
```

## Metodos Principales

### `printOrder(order, printerConfig)`

Envia la orden al servicio centralizado.

```typescript
async printOrder(order: Order, printerConfig: PrinterConfig): Promise<boolean>
```

### `sendToCentralizedService(url, payload)`

Ejecuta el HTTP POST con axios.

```typescript
private async sendToCentralizedService(
  url: string,
  payload: CentralizedPrintPayload
): Promise<void>
```

### `formatOrderForCentralizedPrint(order, printerConfig)`

Convierte la orden al formato JSON esperado por el servicio .NET.

```typescript
private formatOrderForCentralizedPrint(
  order: Order,
  printerConfig: PrinterConfig
): CentralizedPrintPayload
```

### `testConnection()`

Prueba la conectividad con el servicio centralizado.

```typescript
async testConnection(): Promise<{ success: boolean; message: string }>
```

## Manejo de Productos y Subproductos

El sistema detecta subproductos por indentacion:

```typescript
// Si el nombre empieza con espacios, es un subproducto
if (item.name.startsWith('  ')) {
  // Agregar al producto padre actual
  currentProduct.products.push({...});
} else {
  // Es un producto principal
  currentProduct = {...};
  products.push(currentProduct);
}
```

## Prueba de Conexion

### Endpoint REST

```
POST /api/config/print/test-centralized
```

### Respuesta

```json
{
  "success": true,
  "message": "Conexion exitosa. Estado: 200"
}
```

## Manejo de Errores

### Reintentos Progresivos

Si el envio falla:
1. Intento 1: Espera 1 segundo
2. Intento 2: Espera 2 segundos
3. Intento 3: Fallo definitivo, se registra en logs

### Errores Comunes

| Error | Causa | Solucion |
|-------|-------|----------|
| `ECONNREFUSED` | Servicio .NET no activo | Iniciar servicio de impresion |
| `ETIMEDOUT` | Red lenta o servicio sobrecargado | Verificar conectividad |
| `HTTP 400` | Payload mal formateado | Revisar estructura de datos |
| `HTTP 500` | Error interno del servicio .NET | Revisar logs del servicio |

## Ventajas del Modo CENTRALIZED

- **Compatibilidad:** Funciona con infraestructura .NET existente
- **Formato flexible:** El servicio .NET controla el formato de impresion
- **Centralizacion:** Un solo punto gestiona todas las impresoras
- **Escalabilidad:** El servicio .NET puede manejar colas y prioridades
- **Independencia de red:** El backend no necesita acceso directo a impresoras

## Desventajas del Modo CENTRALIZED

- **Dependencia externa:** Requiere que el servicio .NET este activo
- **Latencia adicional:** Un salto de red extra
- **Punto unico de fallo:** Si el servicio .NET falla, no se imprime nada
- **Mantenimiento doble:** Dos sistemas a mantener

## Requisitos

### Backend KDS
- Acceso HTTP al servicio .NET
- Puerto configurado (default: 5000)

### Servicio .NET
- Endpoint `/api/ImpresionTickets/Impresion` disponible
- Acceso TCP a las impresoras termicas
- Capacidad de procesar el formato JSON especificado

## Ejemplo de Uso

### Configurar Servicio Centralizado

```http
PUT /api/config/modes
Content-Type: application/json

{
  "printMode": "CENTRALIZED",
  "centralizedPrintUrl": "http://192.168.1.50:5000/api/ImpresionTickets/Impresion",
  "centralizedPrintPort": 5000
}
```

### Probar Conexion

```http
POST /api/config/print/test-centralized
```

### Respuesta Exitosa

```json
{
  "success": true,
  "message": "Conexion exitosa. Estado: 200"
}
```

## Comparacion con Modo LOCAL

| Aspecto | LOCAL | CENTRALIZED |
|---------|-------|-------------|
| Conexion | TCP directo | HTTP via servicio |
| Formato | ESC/POS en Node.js | Delegado a .NET |
| Dependencias | Solo impresoras | Servicio .NET activo |
| Latencia | Minima | Mayor (+1 salto) |
| Complejidad | Simple | Requiere servicio externo |
| Compatibilidad | Impresoras TCP | Infraestructura .NET |
