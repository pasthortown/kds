# Configuración de Apariencia JSON

Este documento describe la estructura del archivo JSON de configuración de apariencia del KDS, utilizado para exportar e importar configuraciones visuales entre pantallas.

## Tabla de Contenidos

1. [Estructura General](#estructura-general)
2. [Metadatos](#metadatos)
3. [Configuración de Apariencia](#configuración-de-apariencia)
   - [Colores Generales](#colores-generales)
   - [Tipografía Header](#tipografía-header)
   - [Tipografía Timer](#tipografía-timer)
   - [Tipografía Cliente](#tipografía-cliente)
   - [Tipografía Cantidad](#tipografía-cantidad)
   - [Tipografía Producto](#tipografía-producto)
   - [Tipografía Subitems](#tipografía-subitems)
   - [Tipografía Modificadores](#tipografía-modificadores)
   - [Tipografía Notas](#tipografía-notas)
   - [Tipografía Comentarios](#tipografía-comentarios)
   - [Tipografía Canal](#tipografía-canal)
   - [Layout](#layout)
4. [Colores SLA (cardColors)](#colores-sla-cardcolors)
5. [Colores de Canal (channelColors)](#colores-de-canal-channelcolors)
6. [Plantilla HTML de Órdenes](#plantilla-html-de-órdenes)
7. [Ejemplo Completo](#ejemplo-completo)

---

## Estructura General

```json
{
  "version": "1.0",
  "exportedAt": "2025-12-23T12:00:00.000Z",
  "screenName": "Pantalla 1",
  "appearance": { ... },
  "cardColors": [ ... ],
  "channelColors": [ ... ]
}
```

---

## Metadatos

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `version` | string | Versión del formato de exportación |
| `exportedAt` | string | Fecha y hora de exportación en formato ISO 8601 |
| `screenName` | string | Nombre de la pantalla desde la cual se exportó |

---

## Configuración de Apariencia

### Colores Generales

| Campo | Tipo | Valor por defecto | Descripción |
|-------|------|-------------------|-------------|
| `backgroundColor` | string | `"#f0f2f5"` | Color de fondo general de la pantalla |
| `headerColor` | string | `"#1a1a2e"` | Color de fondo del header de la tarjeta |
| `headerTextColor` | string | `"#ffffff"` | Color del texto del header |
| `cardColor` | string | `"#ffffff"` | Color de fondo de las tarjetas de orden |
| `textColor` | string | `"#1a1a2e"` | Color del texto general |
| `accentColor` | string | `"#e94560"` | Color de acento para elementos destacados |

### Tipografía Header

Configuración de la tipografía para el encabezado de la orden (ej: "Orden #123").

| Campo | Tipo | Valor por defecto | Opciones | Descripción |
|-------|------|-------------------|----------|-------------|
| `headerFontFamily` | string | `"Inter, sans-serif"` | Cualquier fuente CSS válida | Familia tipográfica |
| `headerFontSize` | string | `"medium"` | `xsmall`, `small`, `medium`, `large`, `xlarge`, `xxlarge` | Tamaño de fuente |
| `headerFontWeight` | string | `"bold"` | `normal`, `medium`, `semibold`, `bold` | Peso de la fuente |
| `headerFontStyle` | string | `"normal"` | `normal`, `italic` | Estilo de la fuente |
| `headerBgColor` | string | `""` | Hex color | Color de fondo (vacío = usa color SLA) |
| `headerTextColorCustom` | string | `"#ffffff"` | Hex color | Color de texto personalizado |
| `showHeader` | boolean | `true` | `true`, `false` | Mostrar/ocultar header |
| `showOrderNumber` | boolean | `true` | `true`, `false` | Mostrar número de orden |
| `headerShowChannel` | boolean | `true` | `true`, `false` | Mostrar canal en header |
| `headerShowTime` | boolean | `true` | `true`, `false` | Mostrar tiempo en header |

### Tipografía Timer

Configuración del cronómetro de tiempo transcurrido (ej: "05:30").

| Campo | Tipo | Valor por defecto | Descripción |
|-------|------|-------------------|-------------|
| `timerFontFamily` | string | `"monospace"` | Familia tipográfica |
| `timerFontSize` | string | `"medium"` | Tamaño de fuente |
| `timerFontWeight` | string | `"bold"` | Peso de la fuente |
| `timerFontStyle` | string | `"normal"` | Estilo de la fuente |
| `timerTextColor` | string | `"#ffffff"` | Color del texto |
| `showTimer` | boolean | `true` | Mostrar/ocultar timer |

### Tipografía Cliente

Configuración del nombre del cliente.

| Campo | Tipo | Valor por defecto | Descripción |
|-------|------|-------------------|-------------|
| `clientFontFamily` | string | `"Inter, sans-serif"` | Familia tipográfica |
| `clientFontSize` | string | `"small"` | Tamaño de fuente |
| `clientFontWeight` | string | `"normal"` | Peso de la fuente |
| `clientFontStyle` | string | `"normal"` | Estilo de la fuente |
| `clientTextColor` | string | `"#ffffff"` | Color del texto |
| `clientBgColor` | string | `""` | Color de fondo (vacío = usa color SLA) |
| `showClient` | boolean | `true` | Mostrar/ocultar cliente |

### Tipografía Cantidad

Configuración de la cantidad de productos (ej: "5x").

| Campo | Tipo | Valor por defecto | Descripción |
|-------|------|-------------------|-------------|
| `quantityFontFamily` | string | `"Inter, sans-serif"` | Familia tipográfica |
| `quantityFontSize` | string | `"medium"` | Tamaño de fuente |
| `quantityFontWeight` | string | `"bold"` | Peso de la fuente |
| `quantityFontStyle` | string | `"normal"` | Estilo de la fuente |
| `quantityTextColor` | string | `""` | Color del texto (vacío = usa color SLA) |
| `showQuantity` | boolean | `true` | Mostrar/ocultar cantidad |

### Tipografía Producto

Configuración del nombre del producto.

| Campo | Tipo | Valor por defecto | Descripción |
|-------|------|-------------------|-------------|
| `productFontFamily` | string | `"Inter, sans-serif"` | Familia tipográfica |
| `productFontSize` | string | `"medium"` | Tamaño de fuente |
| `productFontWeight` | string | `"bold"` | Peso de la fuente |
| `productFontStyle` | string | `"normal"` | Estilo de la fuente |
| `productTextColor` | string | `""` | Color del texto (vacío = usa textColor general) |
| `productBgColor` | string | `""` | Color de fondo (vacío = transparente) |
| `productUppercase` | boolean | `true` | Convertir a mayúsculas |

### Tipografía Subitems

Configuración de subproductos/subitems (ej: "1x Pepsi, 1x Crispy").

| Campo | Tipo | Valor por defecto | Descripción |
|-------|------|-------------------|-------------|
| `subitemFontFamily` | string | `"Inter, sans-serif"` | Familia tipográfica |
| `subitemFontSize` | string | `"small"` | Tamaño de fuente |
| `subitemFontWeight` | string | `"normal"` | Peso de la fuente |
| `subitemFontStyle` | string | `"normal"` | Estilo de la fuente |
| `subitemTextColor` | string | `"#333333"` | Color del texto |
| `subitemBgColor` | string | `""` | Color de fondo |
| `subitemIndent` | number | `24` | Indentación en píxeles |
| `showSubitems` | boolean | `true` | Mostrar/ocultar subitems |

### Tipografía Modificadores

Configuración de modificadores/notas de productos (ej: "*10x PRESAS").

| Campo | Tipo | Valor por defecto | Descripción |
|-------|------|-------------------|-------------|
| `modifierFontFamily` | string | `"Inter, sans-serif"` | Familia tipográfica |
| `modifierFontSize` | string | `"small"` | Tamaño de fuente |
| `modifierFontWeight` | string | `"normal"` | Peso de la fuente |
| `modifierFontStyle` | string | `"italic"` | Estilo de la fuente |
| `modifierFontColor` | string | `"#666666"` | Color del texto |
| `modifierBgColor` | string | `""` | Color de fondo |
| `modifierIndent` | number | `24` | Indentación en píxeles |
| `showModifiers` | boolean | `true` | Mostrar/ocultar modificadores |

### Tipografía Notas

Configuración de notas especiales (ej: "* SIN MAYONESA").

| Campo | Tipo | Valor por defecto | Descripción |
|-------|------|-------------------|-------------|
| `notesFontFamily` | string | `"Inter, sans-serif"` | Familia tipográfica |
| `notesFontSize` | string | `"small"` | Tamaño de fuente |
| `notesFontWeight` | string | `"normal"` | Peso de la fuente |
| `notesFontStyle` | string | `"italic"` | Estilo de la fuente |
| `notesTextColor` | string | `"#ff9800"` | Color del texto |
| `notesBgColor` | string | `""` | Color de fondo |
| `notesIndent` | number | `24` | Indentación en píxeles |
| `showNotes` | boolean | `true` | Mostrar/ocultar notas |

### Tipografía Comentarios

Configuración de comentarios adicionales del producto.

| Campo | Tipo | Valor por defecto | Descripción |
|-------|------|-------------------|-------------|
| `commentsFontFamily` | string | `"Inter, sans-serif"` | Familia tipográfica |
| `commentsFontSize` | string | `"small"` | Tamaño de fuente |
| `commentsFontWeight` | string | `"normal"` | Peso de la fuente |
| `commentsFontStyle` | string | `"italic"` | Estilo de la fuente |
| `commentsTextColor` | string | `"#4CAF50"` | Color del texto (verde) |
| `commentsBgColor` | string | `""` | Color de fondo |
| `commentsIndent` | number | `24` | Indentación en píxeles |
| `showComments` | boolean | `true` | Mostrar/ocultar comentarios |

### Tipografía Canal

Configuración del indicador de canal/footer (ej: "KIOSKO-EFECTIVO").

| Campo | Tipo | Valor por defecto | Descripción |
|-------|------|-------------------|-------------|
| `channelFontFamily` | string | `"Inter, sans-serif"` | Familia tipográfica |
| `channelFontSize` | string | `"small"` | Tamaño de fuente |
| `channelFontWeight` | string | `"bold"` | Peso de la fuente |
| `channelFontStyle` | string | `"normal"` | Estilo de la fuente |
| `channelTextColor` | string | `"#ffffff"` | Color del texto |
| `channelUppercase` | boolean | `true` | Convertir a mayúsculas |
| `showChannel` | boolean | `true` | Mostrar/ocultar canal |

### Layout

Configuración del diseño y disposición de las órdenes.

| Campo | Tipo | Valor por defecto | Descripción |
|-------|------|-------------------|-------------|
| `columnsPerScreen` | number | `4` | Número de columnas por pantalla |
| `rows` | number | `3` | Número de filas |
| `maxItemsPerColumn` | number | `6` | Máximo de items por columna |
| `animationEnabled` | boolean | `true` | Habilitar animaciones |
| `screenSplit` | boolean | `true` | Dividir pantalla |

---

## Colores SLA (cardColors)

Los colores SLA definen el esquema de colores según el tiempo transcurrido de la orden. Cada nivel de SLA cambia el color de la tarjeta para indicar urgencia.

### Estructura

```json
"cardColors": [
  {
    "color": "#4CAF50",
    "quantityColor": "#FFFFFF",
    "minutes": "03:00",
    "order": 1,
    "isFullBackground": false
  },
  {
    "color": "#FFC107",
    "quantityColor": "#000000",
    "minutes": "05:00",
    "order": 2,
    "isFullBackground": false
  },
  {
    "color": "#FF5722",
    "quantityColor": "#FFFFFF",
    "minutes": "07:00",
    "order": 3,
    "isFullBackground": true
  },
  {
    "color": "#F44336",
    "quantityColor": "#FFFFFF",
    "minutes": "10:00",
    "order": 4,
    "isFullBackground": true
  }
]
```

### Campos

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `color` | string | Color hexadecimal del SLA (se aplica al header o a toda la tarjeta) |
| `quantityColor` | string | Color hexadecimal para la cantidad del producto (vacío = usa color del SLA) |
| `minutes` | string | Tiempo límite en formato "MM:SS" para activar este nivel de SLA |
| `order` | number | Orden de prioridad (1 = primer nivel, 2 = segundo nivel, etc.) |
| `isFullBackground` | boolean | Si es `true`, el color se aplica a toda la tarjeta; si es `false`, solo al header |

### Funcionamiento

1. **Order 1**: Se aplica desde el inicio hasta el tiempo especificado en `minutes`
2. **Order 2**: Se aplica cuando se supera el tiempo del nivel 1
3. **Order 3**: Se aplica cuando se supera el tiempo del nivel 2
4. **Order 4**: Se aplica cuando se supera el tiempo del nivel 3

### Ejemplo Visual

| Nivel | Tiempo | Color | Significado |
|-------|--------|-------|-------------|
| 1 | 0:00 - 3:00 | Verde (#4CAF50) | En tiempo |
| 2 | 3:00 - 5:00 | Amarillo (#FFC107) | Precaución |
| 3 | 5:00 - 7:00 | Naranja (#FF5722) | Urgente |
| 4 | > 7:00 | Rojo (#F44336) | Crítico |

---

## Colores de Canal (channelColors)

Los colores de canal personalizan la apariencia según el origen de la orden (Local, Delivery, PedidosYa, etc.).

### Estructura

```json
"channelColors": [
  {
    "channel": "LOCAL",
    "color": "#4a90e2",
    "textColor": "#ffffff"
  },
  {
    "channel": "DELIVERY",
    "color": "#e94560",
    "textColor": "#ffffff"
  },
  {
    "channel": "PEDIDOSYA",
    "color": "#FF0000",
    "textColor": "#ffffff"
  },
  {
    "channel": "RAPPI",
    "color": "#FF6B00",
    "textColor": "#ffffff"
  }
]
```

### Campos

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `channel` | string | Nombre del canal (debe coincidir exactamente con el nombre en el sistema) |
| `color` | string | Color hexadecimal de fondo para el indicador de canal |
| `textColor` | string | Color hexadecimal del texto del indicador de canal |

---

## Plantilla HTML de Órdenes

El modelo `Order` incluye campos adicionales para renderizado HTML personalizado de las órdenes.

### Campos en el Modelo Order

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `templateHTML` | string (nullable) | Plantilla HTML para renderizado personalizado de la orden |
| `valuesHTML` | string (nullable) | Valores JSON para reemplazar en la plantilla HTML |

### Ejemplo de templateHTML

```html
<div class="order-card">
  <div class="header" style="background-color: {{headerColor}};">
    <span class="order-number">{{orderNumber}}</span>
    <span class="timer">{{timer}}</span>
  </div>
  <div class="customer">{{customerName}}</div>
  <div class="items">
    {{#items}}
    <div class="item">
      <span class="quantity">{{quantity}}x</span>
      <span class="name">{{name}}</span>
    </div>
    {{/items}}
  </div>
  <div class="channel" style="background-color: {{channelColor}};">
    {{channel}}
  </div>
</div>
```

### Ejemplo de valuesHTML

```json
{
  "headerColor": "#4CAF50",
  "orderNumber": "123",
  "timer": "05:30",
  "customerName": "Juan Pérez",
  "items": [
    { "quantity": 2, "name": "COMBO FAMILIAR" },
    { "quantity": 1, "name": "PAPAS GRANDES" }
  ],
  "channelColor": "#4a90e2",
  "channel": "LOCAL"
}
```

### Uso

El sistema utiliza estos campos para:
1. Renderizar órdenes con estilos personalizados
2. Imprimir tickets con formato específico
3. Mostrar información adicional en la pantalla KDS

---

## Ejemplo Completo

```json
{
  "version": "1.0",
  "exportedAt": "2025-12-23T12:00:00.000Z",
  "screenName": "Pantalla Principal",
  "appearance": {
    "backgroundColor": "#f0f2f5",
    "headerColor": "#1a1a2e",
    "headerTextColor": "#ffffff",
    "cardColor": "#ffffff",
    "textColor": "#1a1a2e",
    "accentColor": "#e94560",
    "headerFontFamily": "Inter, sans-serif",
    "headerFontSize": "medium",
    "headerFontWeight": "bold",
    "headerFontStyle": "normal",
    "headerBgColor": "",
    "headerTextColorCustom": "#ffffff",
    "showHeader": true,
    "showOrderNumber": true,
    "headerShowChannel": true,
    "headerShowTime": true,
    "timerFontFamily": "monospace",
    "timerFontSize": "medium",
    "timerFontWeight": "bold",
    "timerFontStyle": "normal",
    "timerTextColor": "#ffffff",
    "showTimer": true,
    "clientFontFamily": "Inter, sans-serif",
    "clientFontSize": "small",
    "clientFontWeight": "normal",
    "clientFontStyle": "normal",
    "clientTextColor": "#ffffff",
    "clientBgColor": "",
    "showClient": true,
    "quantityFontFamily": "Inter, sans-serif",
    "quantityFontSize": "medium",
    "quantityFontWeight": "bold",
    "quantityFontStyle": "normal",
    "quantityTextColor": "",
    "showQuantity": true,
    "productFontFamily": "Inter, sans-serif",
    "productFontSize": "medium",
    "productFontWeight": "bold",
    "productFontStyle": "normal",
    "productTextColor": "",
    "productBgColor": "",
    "productUppercase": true,
    "subitemFontFamily": "Inter, sans-serif",
    "subitemFontSize": "small",
    "subitemFontWeight": "normal",
    "subitemFontStyle": "normal",
    "subitemTextColor": "#333333",
    "subitemBgColor": "",
    "subitemIndent": 24,
    "showSubitems": true,
    "modifierFontFamily": "Inter, sans-serif",
    "modifierFontSize": "small",
    "modifierFontWeight": "normal",
    "modifierFontStyle": "italic",
    "modifierFontColor": "#666666",
    "modifierBgColor": "",
    "modifierIndent": 24,
    "showModifiers": true,
    "notesFontFamily": "Inter, sans-serif",
    "notesFontSize": "small",
    "notesFontWeight": "normal",
    "notesFontStyle": "italic",
    "notesTextColor": "#ff9800",
    "notesBgColor": "",
    "notesIndent": 24,
    "showNotes": true,
    "commentsFontFamily": "Inter, sans-serif",
    "commentsFontSize": "small",
    "commentsFontWeight": "normal",
    "commentsFontStyle": "italic",
    "commentsTextColor": "#4CAF50",
    "commentsBgColor": "",
    "commentsIndent": 24,
    "showComments": true,
    "channelFontFamily": "Inter, sans-serif",
    "channelFontSize": "small",
    "channelFontWeight": "bold",
    "channelFontStyle": "normal",
    "channelTextColor": "#ffffff",
    "channelUppercase": true,
    "showChannel": true,
    "columnsPerScreen": 4,
    "rows": 3,
    "maxItemsPerColumn": 6,
    "animationEnabled": true,
    "screenSplit": true
  },
  "cardColors": [
    {
      "color": "#4CAF50",
      "quantityColor": "#FFFFFF",
      "minutes": "03:00",
      "order": 1,
      "isFullBackground": false
    },
    {
      "color": "#FFC107",
      "quantityColor": "#000000",
      "minutes": "05:00",
      "order": 2,
      "isFullBackground": false
    },
    {
      "color": "#FF5722",
      "quantityColor": "#FFFFFF",
      "minutes": "07:00",
      "order": 3,
      "isFullBackground": true
    },
    {
      "color": "#F44336",
      "quantityColor": "#FFFFFF",
      "minutes": "10:00",
      "order": 4,
      "isFullBackground": true
    }
  ],
  "channelColors": [
    {
      "channel": "LOCAL",
      "color": "#4a90e2",
      "textColor": "#ffffff"
    },
    {
      "channel": "DELIVERY",
      "color": "#e94560",
      "textColor": "#ffffff"
    },
    {
      "channel": "PEDIDOSYA",
      "color": "#FF0000",
      "textColor": "#ffffff"
    },
    {
      "channel": "RAPPI",
      "color": "#FF6B00",
      "textColor": "#ffffff"
    }
  ]
}
```

---

## Notas Importantes

1. **Compatibilidad**: El formato JSON es compatible con la versión 1.0 del sistema de exportación.

2. **Colores**: Todos los colores deben estar en formato hexadecimal (ej: `#4CAF50`).

3. **Campos vacíos**: Los campos de color vacíos (`""`) indican que se debe usar el valor por defecto o heredar del padre.

4. **Importación**: Al importar, el sistema validará la estructura y aplicará los valores. Los campos faltantes mantendrán sus valores actuales.

5. **Orden de SLA**: Los `cardColors` deben tener valores de `order` únicos (1, 2, 3, 4).

6. **Canales**: Los nombres de canal en `channelColors` deben coincidir exactamente con los configurados en el sistema.
