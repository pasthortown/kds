# Estructura del Objeto JSON de Comanda

Este documento describe la estructura completa del objeto JSON que debe enviarse al KDS para registrar una comanda/orden.

## Objeto Principal: ApiComanda

```typescript
interface ApiComanda {
  id: string;                    // ID único de la comanda (requerido)
  orderId: string;               // ID de la orden/pedido (requerido)
  createdAt: string;             // Fecha/hora de creación ISO 8601 (requerido)
  channel: Channel;              // Información del canal (requerido)
  cashRegister: CashRegister;    // Información de la caja (requerido)
  customer?: Customer;           // Información del cliente (opcional)
  products: Product[];           // Lista de productos (requerido, mínimo 1)
  otrosDatos?: OtrosDatos;       // Datos adicionales (opcional)
  impresion?: string;            // Dato de impresión (opcional)
  comments?: string;             // Comentarios generales (opcional)
  templateHTML?: string;         // Plantilla HTML personalizada (opcional)
  valuesHTML?: string;           // Valores para plantilla HTML (opcional)
  statusPos?: string;            // Estado del POS (opcional)
}
```

## Objetos Anidados

### Channel (Canal)

Define el origen y tipo de la orden.

```typescript
interface Channel {
  id: number;      // ID numérico del canal
  name: string;    // Nombre del canal
  type: string;    // Tipo de servicio
}
```

**Valores comunes de `name`:**
| Valor | Descripción |
|-------|-------------|
| `"Kiosko-Efectivo"` | Autoservicio con pago en efectivo |
| `"Kiosko-Tarjeta"` | Autoservicio con pago con tarjeta |
| `"PedidosYa"` | Delivery via PedidosYa |
| `"RAPPI"` | Delivery via Rappi |
| `"UberEats"` | Delivery via UberEats |
| `"Drive"` | Servicio Drive-Thru |
| `"APP"` | Pedido desde aplicación móvil |
| `"MOSTRADOR"` | Pedido en mostrador |
| `"MESA"` | Pedido de mesa/salón |

**Valores de `type`:**
| Valor | Descripción |
|-------|-------------|
| `"SALON"` | Consumo en el local |
| `"LLEVAR"` | Para llevar |
| `"DELIVERY"` | Entrega a domicilio |
| `"DRIVE"` | Drive-thru |

### CashRegister (Caja/Estación)

Información de la caja o estación que genera la orden.

```typescript
interface CashRegister {
  cashier: string;   // Número o código del cajero
  name: string;      // Nombre de la caja/estación
}
```

### Customer (Cliente) - Opcional

Información del cliente (si está disponible).

```typescript
interface Customer {
  name: string;      // Nombre del cliente
}
```

### Product (Producto)

Cada producto de la orden.

```typescript
interface Product {
  productId?: string;    // ID del producto en el sistema origen (opcional)
  name: string;          // Nombre del producto (requerido)
  amount?: number;       // Cantidad (default: 1)
  category?: string;     // Categoría del producto (opcional)
  content?: string[];    // Array de modificadores/notas especiales (opcional)
  modifier?: string;     // Modificador descriptivo (opcional)
  comments?: string;     // Comentarios adicionales (opcional)
}
```

**Sobre el campo `content`:**
Este campo es un array de strings que representa modificadores o instrucciones especiales. Se recomienda usar el prefijo `*` para indicar modificaciones:

```json
{
  "content": [
    "*SIN SAL",
    "*EXTRA QUESO",
    "*BIEN COCIDO",
    "*SIN CEBOLLA"
  ]
}
```

**Sobre el campo `modifier`:**
Descripción textual de variantes del producto:

```json
{
  "name": "Combo Familiar 15pcs",
  "modifier": "8 Original, 7 Crispy"
}
```

### OtrosDatos (Datos Adicionales) - Opcional

Información adicional de la orden.

```typescript
interface OtrosDatos {
  turno?: number | string;   // Número de turno
  nroCheque?: string;        // Número de cheque/factura
  llamarPor?: string;        // Nombre para llamar al cliente
  Fecha?: string;            // Fecha de la orden
  Direccion?: string;        // Dirección de entrega
}
```

## Ejemplo Completo

```json
{
  "id": "ORD-2025-001234",
  "orderId": "ORD-2025-001234",
  "createdAt": "2025-01-15T14:30:00.000Z",
  "channel": {
    "id": 1,
    "name": "Kiosko-Efectivo",
    "type": "LLEVAR"
  },
  "cashRegister": {
    "cashier": "CAJ001",
    "name": "Caja Principal"
  },
  "customer": {
    "name": "Juan Pérez"
  },
  "products": [
    {
      "productId": "PROD-001",
      "name": "Combo Familiar 15pcs",
      "amount": 1,
      "category": "Combos",
      "content": [
        "*8 PIEZAS ORIGINAL",
        "*7 PIEZAS CRISPY"
      ],
      "modifier": "8 Original, 7 Crispy"
    },
    {
      "productId": "PROD-002",
      "name": "Papas Grandes",
      "amount": 2,
      "category": "Acompañamientos",
      "content": [
        "*SIN SAL"
      ]
    },
    {
      "productId": "PROD-003",
      "name": "Coca-Cola 500ml",
      "amount": 3,
      "category": "Bebidas"
    }
  ],
  "otrosDatos": {
    "turno": 145,
    "nroCheque": "FAC-2025-001234",
    "llamarPor": "Juan"
  },
  "comments": "Cliente VIP - Prioridad alta",
  "statusPos": "PEDIDO TOMADO"
}
```

## Ejemplo Mínimo

La estructura mínima requerida:

```json
{
  "id": "ORD-001",
  "orderId": "ORD-001",
  "createdAt": "2025-01-15T14:30:00.000Z",
  "channel": {
    "id": 1,
    "name": "MOSTRADOR",
    "type": "LLEVAR"
  },
  "cashRegister": {
    "cashier": "1",
    "name": "Caja 1"
  },
  "products": [
    {
      "name": "Hamburguesa Clásica",
      "amount": 1
    }
  ]
}
```

## Validaciones

| Campo | Validación |
|-------|------------|
| `id` | No vacío, único por transacción |
| `orderId` | No vacío, se usa para identificar duplicados |
| `createdAt` | Formato ISO 8601 válido |
| `channel.name` | No vacío |
| `products` | Array con al menos 1 elemento |
| `products[].name` | No vacío |
| `products[].amount` | Número positivo (default: 1) |

## Estados del POS (`statusPos`)

| Valor | Descripción | Acción en KDS |
|-------|-------------|---------------|
| `"TOMANDO PEDIDO"` | Pedido en proceso de captura | Muestra orden como "en captura" |
| `"PEDIDO TOMADO"` | Pedido finalizado | Orden lista para preparación |
| `null` o vacío | Sin estado específico | Comportamiento normal |

## Notas Importantes

1. **Duplicados**: Si se envía una orden con un `orderId` que ya existe, el sistema actualizará la orden existente (sin moverla de pantalla).

2. **Órdenes vacías**: Si se envía una orden con `products` vacío o `amount: 0` en todos los productos, la orden será eliminada del sistema.

3. **Caracteres especiales**: Se recomienda usar UTF-8 para todos los textos. Los caracteres especiales (tildes, ñ, etc.) son soportados.

4. **Fechas**: Usar siempre formato ISO 8601 con zona horaria o UTC (`Z`).
