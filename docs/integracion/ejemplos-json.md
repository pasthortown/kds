# Ejemplos de JSON para Integración

Este documento contiene ejemplos prácticos de JSON extraídos de la colección de Postman del sistema KDS.

## 1. Orden Simple

La estructura más básica para crear una orden:

```json
{
  "id": "ORD-20251226-001",
  "orderId": "1001",
  "createdAt": "2025-12-26T14:30:00.000Z",
  "channel": {
    "id": 1,
    "name": "LOCAL",
    "type": "LOCAL"
  },
  "cashRegister": {
    "cashier": "CAJERO1",
    "name": "Caja 1"
  },
  "customer": {
    "name": "Juan Perez"
  },
  "products": [
    {
      "productId": "PROD001",
      "name": "COMBO FAMILIAR",
      "amount": 1,
      "category": "COMBOS"
    },
    {
      "productId": "PROD002",
      "name": "PAPAS GRANDES",
      "amount": 2,
      "category": "ACOMPAÑAMIENTOS"
    }
  ]
}
```

## 2. Orden con Subproductos y Modificadores

Orden con múltiples productos y modificadores descriptivos:

```json
{
  "id": "ORD-SUB-20251226-001",
  "orderId": "1002",
  "createdAt": "2025-12-26T14:35:00.000Z",
  "channel": {
    "id": 1,
    "name": "LOCAL",
    "type": "LOCAL"
  },
  "cashRegister": {
    "cashier": "CAJERO1",
    "name": "Caja 1"
  },
  "customer": {
    "name": "Maria Garcia"
  },
  "products": [
    {
      "productId": "COMBO001",
      "name": "MEGA COMBO",
      "amount": 1,
      "category": "COMBOS",
      "modifier": "Tamaño Grande"
    },
    {
      "productId": "SUB001",
      "name": "POLLO CRISPY 3 PIEZAS",
      "amount": 1,
      "category": "POLLO",
      "modifier": "Extra crispy"
    },
    {
      "productId": "SUB002",
      "name": "PAPAS MEDIANAS",
      "amount": 1,
      "category": "ACOMPAÑAMIENTOS",
      "modifier": "Sin sal"
    },
    {
      "productId": "SUB003",
      "name": "COCA-COLA 500ML",
      "amount": 1,
      "category": "BEBIDAS",
      "modifier": "Sin hielo"
    }
  ]
}
```

## 3. Orden con Notas Especiales (content)

Orden con instrucciones especiales para la cocina usando el campo `content`:

```json
{
  "id": "ORD-MOD-20251226-001",
  "orderId": "1003",
  "createdAt": "2025-12-26T14:40:00.000Z",
  "channel": {
    "id": 1,
    "name": "LOCAL",
    "type": "LOCAL"
  },
  "cashRegister": {
    "cashier": "CAJERO2",
    "name": "Caja 2"
  },
  "customer": {
    "name": "Carlos Rodriguez"
  },
  "products": [
    {
      "productId": "SAND001",
      "name": "SANDWICH CRISPY",
      "amount": 2,
      "category": "SANDWICHES",
      "modifier": "Pan integral, Pollo extra crispy",
      "content": [
        "*SIN MAYONESA",
        "*EXTRA QUESO",
        "*BIEN COCIDO"
      ],
      "comments": "Cliente alergico a la mostaza"
    },
    {
      "productId": "BEB001",
      "name": "GASEOSA GRANDE",
      "amount": 2,
      "category": "BEBIDAS",
      "modifier": "Coca-Cola",
      "content": [
        "*SIN HIELO"
      ]
    }
  ],
  "comments": "Orden para llevar - Cliente frecuente"
}
```

## 4. Orden Delivery con Datos Adicionales

Orden de delivery con información completa de entrega:

```json
{
  "id": "ORD-DEL-20251226-001",
  "orderId": "1004",
  "createdAt": "2025-12-26T14:45:00.000Z",
  "channel": {
    "id": 2,
    "name": "DELIVERY",
    "type": "DELIVERY"
  },
  "cashRegister": {
    "cashier": "DELIVERY1",
    "name": "Delivery"
  },
  "customer": {
    "name": "Ana Martinez"
  },
  "otrosDatos": {
    "turno": 1,
    "nroCheque": "CHK-12345",
    "llamarPor": "Ana",
    "Fecha": "2025-12-26",
    "Direccion": "Av. Principal 123, Piso 4"
  },
  "products": [
    {
      "productId": "COMBO002",
      "name": "COMBO MEGA",
      "amount": 1,
      "category": "COMBOS"
    },
    {
      "productId": "EXTRA001",
      "name": "ALITAS BBQ x6",
      "amount": 1,
      "category": "EXTRAS"
    }
  ]
}
```

## 5. Orden PedidosYa

Orden del canal PedidosYa:

```json
{
  "id": "ORD-PEYA-20251226-001",
  "orderId": "PY-5001",
  "createdAt": "2025-12-26T14:50:00.000Z",
  "channel": {
    "id": 3,
    "name": "PEDIDOSYA",
    "type": "DELIVERY"
  },
  "cashRegister": {
    "cashier": "PEYA",
    "name": "PedidosYa"
  },
  "customer": {
    "name": "Cliente PedidosYa"
  },
  "products": [
    {
      "productId": "BUCKET001",
      "name": "BUCKET 8 PRESAS",
      "amount": 1,
      "category": "BUCKETS"
    },
    {
      "productId": "SIDE001",
      "name": "ENSALADA COLESLAW",
      "amount": 2,
      "category": "SIDES"
    }
  ]
}
```

## 6. Orden Rappi

Orden del canal Rappi:

```json
{
  "id": "ORD-RAPPI-20251226-001",
  "orderId": "RP-6001",
  "createdAt": "2025-12-26T14:55:00.000Z",
  "channel": {
    "id": 4,
    "name": "RAPPI",
    "type": "DELIVERY"
  },
  "cashRegister": {
    "cashier": "RAPPI",
    "name": "Rappi"
  },
  "customer": {
    "name": "Cliente Rappi"
  },
  "products": [
    {
      "productId": "TWISTER001",
      "name": "TWISTER CLASICO",
      "amount": 3,
      "category": "TWISTERS"
    }
  ]
}
```

## 7. Orden Completa (Ejemplo Real de Producción)

Este es un ejemplo completo con todos los campos disponibles, tal como se envía desde un sistema POS real:

```json
{
  "id": "ORD-COMPLETA-20251226-001",
  "orderId": "FULL-7001",
  "createdAt": "2025-12-26T15:00:00.000Z",
  "channel": {
    "id": 1,
    "name": "Local",
    "type": "LOCAL"
  },
  "cashRegister": {
    "cashier": "CAJERO1",
    "name": "Caja Principal"
  },
  "customer": {
    "name": "Juan Carlos Mendoza"
  },
  "otrosDatos": {
    "turno": 1,
    "nroCheque": "CHK-98765",
    "llamarPor": "Juan Carlos",
    "Fecha": "2025-12-26",
    "Direccion": "Av. Principal 456, Piso 2, Oficina 201"
  },
  "statusPos": "PEDIDO TOMADO",
  "products": [
    {
      "productId": "COMBO001",
      "name": "MEGA COMBO FAMILIAR 15 PRESAS",
      "amount": 2,
      "category": "COMBOS",
      "modifier": "Tamaño Familiar, Para compartir",
      "content": [
        "*SIN SAL EN PAPAS",
        "*PRESAS BIEN COCIDAS",
        "*MITAD ORIGINAL MITAD CRISPY"
      ],
      "comments": "Cliente pide que las presas esten crujientes"
    },
    {
      "productId": "ALITAS001",
      "name": "ALITAS BBQ x24",
      "amount": 1,
      "category": "EXTRAS",
      "modifier": "Salsa BBQ, Extra picante",
      "content": [
        "*EXTRA SALSA BBQ",
        "*EXTRA SALSA RANCH",
        "*MUY PICANTES"
      ],
      "comments": "Agregar servilletas extra"
    },
    {
      "productId": "TWISTER001",
      "name": "TWISTER SUPREME",
      "amount": 3,
      "category": "SANDWICHES",
      "modifier": "Pan tostado, Pollo crispy",
      "content": [
        "*SIN CEBOLLA",
        "*SIN TOMATE",
        "*EXTRA MAYONESA"
      ],
      "comments": "Uno de los twisters sin lechuga"
    },
    {
      "productId": "POSTRE001",
      "name": "SUNDAE CHOCOLATE",
      "amount": 4,
      "category": "POSTRES",
      "modifier": "Extra chocolate, Con galleta",
      "content": [
        "*EXTRA CHOCOLATE",
        "*CON GALLETA"
      ]
    }
  ],
  "comments": "ORDEN PARA EVENTO CORPORATIVO - ENTREGAR EN RECEPCION - LLAMAR 10 MIN ANTES - FACTURA A NOMBRE DE EMPRESA XYZ S.A. RUC 1234567890001"
}
```

## 8. Envío de Múltiples Órdenes (Batch)

Para enviar múltiples órdenes en una sola petición usando `/api/tickets/receive-batch`:

```json
{
  "comandas": [
    {
      "id": "BATCH-1-20251226",
      "orderId": "B001",
      "createdAt": "2025-12-26T15:10:00.000Z",
      "channel": {
        "id": 1,
        "name": "LOCAL",
        "type": "LOCAL"
      },
      "cashRegister": {
        "cashier": "CAJERO1",
        "name": "Caja 1"
      },
      "customer": {
        "name": "Cliente Batch 1"
      },
      "products": [
        {
          "name": "COMBO 1",
          "amount": 1
        }
      ]
    },
    {
      "id": "BATCH-2-20251226",
      "orderId": "B002",
      "createdAt": "2025-12-26T15:10:01.000Z",
      "channel": {
        "id": 1,
        "name": "LOCAL",
        "type": "LOCAL"
      },
      "cashRegister": {
        "cashier": "CAJERO2",
        "name": "Caja 2"
      },
      "customer": {
        "name": "Cliente Batch 2"
      },
      "products": [
        {
          "name": "COMBO 2",
          "amount": 2
        },
        {
          "name": "PAPAS GRANDES",
          "amount": 1
        }
      ]
    },
    {
      "id": "BATCH-3-20251226",
      "orderId": "B003",
      "createdAt": "2025-12-26T15:10:02.000Z",
      "channel": {
        "id": 2,
        "name": "DELIVERY",
        "type": "DELIVERY"
      },
      "cashRegister": {
        "cashier": "DELIVERY1",
        "name": "Delivery"
      },
      "customer": {
        "name": "Cliente Delivery Batch"
      },
      "products": [
        {
          "name": "BUCKET 12 PRESAS",
          "amount": 1
        }
      ]
    }
  ]
}
```

## 9. Batch con Múltiples Canales

Envío de órdenes de diferentes canales en una sola petición:

```json
{
  "comandas": [
    {
      "id": "MIX-LOCAL-20251226",
      "orderId": "L1001",
      "createdAt": "2025-12-26T15:15:00.000Z",
      "channel": { "id": 1, "name": "LOCAL", "type": "LOCAL" },
      "cashRegister": { "cashier": "C1", "name": "Caja 1" },
      "customer": { "name": "Cliente Local" },
      "products": [{ "name": "COMBO LOCAL", "amount": 1 }]
    },
    {
      "id": "MIX-DELIVERY-20251226",
      "orderId": "D2001",
      "createdAt": "2025-12-26T15:15:01.000Z",
      "channel": { "id": 2, "name": "DELIVERY", "type": "DELIVERY" },
      "cashRegister": { "cashier": "DEL", "name": "Delivery" },
      "customer": { "name": "Cliente Delivery" },
      "products": [{ "name": "COMBO DELIVERY", "amount": 2 }]
    },
    {
      "id": "MIX-PEYA-20251226",
      "orderId": "PY3001",
      "createdAt": "2025-12-26T15:15:02.000Z",
      "channel": { "id": 3, "name": "PEDIDOSYA", "type": "DELIVERY" },
      "cashRegister": { "cashier": "PEYA", "name": "PedidosYa" },
      "customer": { "name": "Cliente PedidosYa" },
      "products": [{ "name": "BUCKET PEYA", "amount": 1 }]
    },
    {
      "id": "MIX-RAPPI-20251226",
      "orderId": "RP4001",
      "createdAt": "2025-12-26T15:15:03.000Z",
      "channel": { "id": 4, "name": "RAPPI", "type": "DELIVERY" },
      "cashRegister": { "cashier": "RAPPI", "name": "Rappi" },
      "customer": { "name": "Cliente Rappi" },
      "products": [{ "name": "TWISTER RAPPI", "amount": 3 }]
    }
  ]
}
```

## Respuestas del API

### Respuesta Exitosa (Orden Individual)

```json
{
  "success": true,
  "orderId": "clx1234567890abcdef"
}
```

### Respuesta Exitosa (Batch)

```json
{
  "total": 3,
  "success": 3,
  "failed": 0,
  "results": [
    { "id": "BATCH-1-20251226", "success": true, "orderId": "clx111..." },
    { "id": "BATCH-2-20251226", "success": true, "orderId": "clx222..." },
    { "id": "BATCH-3-20251226", "success": true, "orderId": "clx333..." }
  ]
}
```

### Respuesta de Error

```json
{
  "success": false,
  "error": "Modo API no habilitado. Active el modo API en configuración."
}
```

## Colección de Postman

Para probar estos ejemplos, importa la colección de Postman incluida en el proyecto:

- **Ubicación**: `backend/KDS_API_Tickets.postman_collection.json`
- **Variables requeridas**:
  - `baseUrl`: URL del backend (ej: `http://localhost:3000/api`)
  - `accessToken`: Se obtiene automáticamente al ejecutar el login

### Pasos para usar la colección:

1. Importar `KDS_API_Tickets.postman_collection.json` en Postman
2. Ejecutar "1. Login" para obtener el token
3. Verificar modo API con "2. Verificar Modo API"
4. Si no está activo, ejecutar "Activar Modo API"
5. Usar cualquier endpoint de tickets para crear órdenes

## Canales Comunes

| ID | Nombre | Tipo | Descripción |
|----|--------|------|-------------|
| 1 | LOCAL | LOCAL | Pedidos en mostrador |
| 2 | DELIVERY | DELIVERY | Delivery propio |
| 3 | PEDIDOSYA | DELIVERY | PedidosYa |
| 4 | RAPPI | DELIVERY | Rappi |
| 5 | UBEREATS | DELIVERY | UberEats |
| 6 | DRIVE | DRIVE | Drive-Thru |
| 7 | KIOSKO | LOCAL | Autoservicio/Kiosko |
| 8 | APP | DELIVERY | App móvil propia |

## Notas Importantes

1. **Campo `id` vs `orderId`**:
   - `id`: Identificador único de la transacción (para duplicados)
   - `orderId`: Número visible de la orden

2. **Campo `content`**: Array de strings para modificaciones/notas. Se recomienda usar prefijo `*` para mejor visualización.

3. **Campo `modifier`**: String descriptivo de variantes del producto.

4. **Campo `statusPos`**: Valores comunes:
   - `"TOMANDO PEDIDO"`: Orden en proceso de captura
   - `"PEDIDO TOMADO"`: Orden lista para preparación

5. **Fechas**: Siempre usar formato ISO 8601 con zona horaria (ej: `2025-12-26T15:00:00.000Z`)
