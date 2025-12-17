# API Reference - Sistema KDS v2.0

## Documentación Completa de Endpoints

---

## 1. Información General

### Base URL
```
http://servidor:3000/api
```

### Autenticación
Todas las rutas protegidas requieren header:
```
Authorization: Bearer <access_token>
```

### Formato de Respuestas
```json
// Éxito
{
  "data": { ... },
  "message": "Operación exitosa"
}

// Error
{
  "error": "Mensaje de error",
  "details": { ... }  // opcional
}
```

### Códigos de Estado

| Código | Descripción |
|--------|-------------|
| 200 | OK - Operación exitosa |
| 201 | Created - Recurso creado |
| 400 | Bad Request - Datos inválidos |
| 401 | Unauthorized - Token inválido o ausente |
| 403 | Forbidden - Sin permisos |
| 404 | Not Found - Recurso no encontrado |
| 500 | Internal Server Error |

---

## 2. Autenticación

### POST /auth/login
Iniciar sesión y obtener tokens.

**Request:**
```json
{
  "email": "admin@kds.local",
  "password": "admin123"
}
```

**Response (200):**
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": "clq1234567890",
    "email": "admin@kds.local",
    "name": "Administrador",
    "role": "ADMIN"
  }
}
```

**Errores:**
- 400: Credenciales inválidas
- 401: Usuario desactivado

---

### POST /auth/refresh
Refrescar access token.

**Request:**
```json
{
  "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Response (200):**
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

---

### GET /auth/me
Obtener datos del usuario actual.

**Headers:** `Authorization: Bearer <token>`

**Response (200):**
```json
{
  "id": "clq1234567890",
  "email": "admin@kds.local",
  "name": "Administrador",
  "role": "ADMIN",
  "active": true,
  "createdAt": "2025-01-01T00:00:00.000Z"
}
```

---

### POST /auth/change-password
Cambiar contraseña del usuario actual.

**Headers:** `Authorization: Bearer <token>`

**Request:**
```json
{
  "currentPassword": "admin123",
  "newPassword": "newSecurePassword123"
}
```

**Response (200):**
```json
{
  "message": "Contraseña actualizada correctamente"
}
```

---

## 3. Usuarios

### GET /users
Listar todos los usuarios.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

**Response (200):**
```json
[
  {
    "id": "clq1234567890",
    "email": "admin@kds.local",
    "name": "Administrador",
    "role": "ADMIN",
    "active": true,
    "createdAt": "2025-01-01T00:00:00.000Z"
  }
]
```

---

### GET /users/:id
Obtener usuario por ID.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

**Response (200):**
```json
{
  "id": "clq1234567890",
  "email": "admin@kds.local",
  "name": "Administrador",
  "role": "ADMIN",
  "active": true,
  "createdAt": "2025-01-01T00:00:00.000Z"
}
```

---

### POST /users
Crear nuevo usuario.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

**Request:**
```json
{
  "email": "operador@kds.local",
  "password": "password123",
  "name": "Operador Cocina",
  "role": "OPERATOR"
}
```

**Response (201):**
```json
{
  "id": "clq9876543210",
  "email": "operador@kds.local",
  "name": "Operador Cocina",
  "role": "OPERATOR",
  "active": true
}
```

---

### PUT /users/:id
Actualizar usuario.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

**Request:**
```json
{
  "name": "Operador Principal",
  "role": "OPERATOR"
}
```

**Response (200):**
```json
{
  "id": "clq9876543210",
  "email": "operador@kds.local",
  "name": "Operador Principal",
  "role": "OPERATOR",
  "active": true
}
```

---

### DELETE /users/:id
Eliminar usuario.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

**Response (200):**
```json
{
  "message": "Usuario eliminado"
}
```

---

### POST /users/:id/toggle-active
Activar/desactivar usuario.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

**Response (200):**
```json
{
  "id": "clq9876543210",
  "active": false
}
```

---

## 4. Pantallas

### GET /screens
Listar todas las pantallas.

**Headers:** `Authorization: Bearer <token>`

**Response (200):**
```json
[
  {
    "id": "clq_screen_001",
    "number": 1,
    "name": "Cocina Pollos 1",
    "queueId": "clq_queue_001",
    "status": "ONLINE",
    "apiKey": "sk_screen_xxx",
    "queue": {
      "id": "clq_queue_001",
      "name": "Cocina Principal"
    },
    "createdAt": "2025-01-01T00:00:00.000Z"
  }
]
```

---

### GET /screens/:id
Obtener pantalla por ID.

**Headers:** `Authorization: Bearer <token>`

**Response (200):**
```json
{
  "id": "clq_screen_001",
  "number": 1,
  "name": "Cocina Pollos 1",
  "queueId": "clq_queue_001",
  "status": "ONLINE",
  "apiKey": "sk_screen_xxx",
  "queue": { ... },
  "appearance": { ... },
  "preference": { ... },
  "keyboard": { ... },
  "printer": { ... }
}
```

---

### GET /screens/by-number/:number
Obtener pantalla por número (para KDS frontend).

**Response (200):**
```json
{
  "id": "clq_screen_001",
  "number": 1,
  "name": "Cocina Pollos 1",
  ...
}
```

---

### POST /screens
Crear nueva pantalla.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

**Request:**
```json
{
  "name": "Cocina Bebidas",
  "queueId": "clq_queue_001"
}
```

**Response (201):**
```json
{
  "id": "clq_screen_002",
  "number": 2,
  "name": "Cocina Bebidas",
  "queueId": "clq_queue_001",
  "status": "OFFLINE",
  "apiKey": "sk_screen_yyy"
}
```

---

### PUT /screens/:id
Actualizar pantalla.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN, OPERATOR

**Request:**
```json
{
  "name": "Cocina Bebidas Principal",
  "queueId": "clq_queue_002"
}
```

---

### DELETE /screens/:id
Eliminar pantalla.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

---

### GET /screens/:id/config
Obtener configuración completa de pantalla.

**Headers:** `Authorization: Bearer <token>`

**Response (200):**
```json
{
  "screen": {
    "id": "clq_screen_001",
    "number": 1,
    "name": "Cocina Pollos 1"
  },
  "appearance": {
    "theme": "DARK",
    "columnsPerScreen": 4,
    "rows": 3,
    "backgroundColor": "#1a1a2e",
    "cardColors": [
      { "color": "#4caf50", "minutes": "03:00", "order": 1 },
      { "color": "#ffeb3b", "minutes": "05:00", "order": 2 },
      { "color": "#ff9800", "minutes": "08:00", "order": 3 },
      { "color": "#f44336", "minutes": "99:99", "order": 4 }
    ]
  },
  "preference": {
    "showClientData": true,
    "touchEnabled": false,
    "botoneraEnabled": true
  },
  "keyboard": {
    "finishFirstOrder": "h",
    "nextPage": "i",
    "previousPage": "g",
    "combos": "[{\"keys\":[\"i\",\"g\"],\"holdTime\":3000,\"action\":\"standby\"}]"
  }
}
```

---

### PUT /screens/:id/appearance
Actualizar apariencia de pantalla.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN, OPERATOR

**Request:**
```json
{
  "theme": "DARK",
  "columnsPerScreen": 5,
  "rows": 3,
  "backgroundColor": "#1a1a2e",
  "headerColor": "#2d2d44",
  "cardColor": "#ffffff",
  "textColor": "#1a1a2e",
  "accentColor": "#e94560",
  "headerFontFamily": "Inter, sans-serif",
  "headerFontSize": "large",
  "headerFontWeight": "bold",
  "timerFontFamily": "monospace",
  "productFontSize": "medium",
  "productUppercase": true
}
```

---

### PUT /screens/:id/preference
Actualizar preferencias de pantalla.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN, OPERATOR

**Request:**
```json
{
  "showClientData": true,
  "showName": true,
  "showIdentifier": true,
  "identifierMessage": "Orden",
  "touchEnabled": false,
  "botoneraEnabled": true,
  "showPagination": true
}
```

---

### PUT /screens/:id/keyboard
Actualizar configuración de teclado.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN, OPERATOR

**Request:**
```json
{
  "finishFirstOrder": "h",
  "finishSecondOrder": "3",
  "finishThirdOrder": "1",
  "finishFourthOrder": "f",
  "finishFifthOrder": "j",
  "nextPage": "i",
  "previousPage": "g",
  "undo": "c",
  "debounceTime": 200,
  "combos": "[{\"keys\":[\"i\",\"g\"],\"holdTime\":3000,\"action\":\"standby\"}]"
}
```

---

### PUT /screens/:id/printer
Configurar impresora de pantalla.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN, OPERATOR

**Request:**
```json
{
  "name": "Impresora Cocina 1",
  "ip": "192.168.1.100",
  "port": 9100,
  "enabled": true
}
```

---

### DELETE /screens/:id/printer
Eliminar impresora de pantalla.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

---

### POST /screens/:id/printer/test
Probar conexión de impresora.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN, OPERATOR

**Response (200):**
```json
{
  "success": true,
  "message": "Conexión exitosa"
}
```

---

### POST /screens/:id/standby
Poner pantalla en modo standby.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN, OPERATOR

---

### POST /screens/:id/activate
Activar pantalla.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN, OPERATOR

---

### POST /screens/:id/regenerate-key
Regenerar API key de pantalla.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

**Response (200):**
```json
{
  "apiKey": "sk_screen_new_xxx"
}
```

---

## 5. Colas

### GET /queues
Listar todas las colas.

**Headers:** `Authorization: Bearer <token>`

**Response (200):**
```json
[
  {
    "id": "clq_queue_001",
    "name": "Cocina Principal",
    "description": "Cola principal de cocina",
    "distribution": "DISTRIBUTED",
    "active": true,
    "channels": [
      { "id": "ch_001", "channel": "local", "color": "#4a90e2", "priority": 1 },
      { "id": "ch_002", "channel": "llevar", "color": "#52c41a", "priority": 2 }
    ],
    "filters": [],
    "screens": [
      { "id": "scr_001", "name": "Cocina Pollos 1" }
    ]
  }
]
```

---

### GET /queues/:id
Obtener cola por ID.

---

### POST /queues
Crear nueva cola.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

**Request:**
```json
{
  "name": "Cocina Secundaria",
  "description": "Para bebidas y postres",
  "distribution": "DISTRIBUTED"
}
```

---

### PUT /queues/:id
Actualizar cola.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

---

### DELETE /queues/:id
Eliminar cola.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

---

### POST /queues/:id/channels
Agregar canal a cola.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

**Request:**
```json
{
  "channel": "rappi",
  "color": "#ff5722",
  "priority": 3
}
```

---

### PUT /queues/:id/channels/:channelId
Actualizar canal.

---

### DELETE /queues/:id/channels/:channelId
Eliminar canal.

---

### POST /queues/:id/filters
Agregar filtro a cola.

**Request:**
```json
{
  "pattern": "^BEBIDA.*",
  "suppress": true
}
```

---

### DELETE /queues/:id/filters/:filterId
Eliminar filtro.

---

### GET /queues/:id/stats
Estadísticas de cola.

**Response (200):**
```json
{
  "totalOrders": 150,
  "pendingOrders": 12,
  "avgPrepTime": 245,
  "ordersByChannel": {
    "local": 80,
    "llevar": 45,
    "rappi": 25
  }
}
```

---

### POST /queues/:id/reset-balance
Resetear balance Round-Robin.

---

## 6. Órdenes

### GET /orders
Listar órdenes con filtros.

**Headers:** `Authorization: Bearer <token>`

**Query params:**
| Param | Tipo | Descripción |
|-------|------|-------------|
| status | string | PENDING, IN_PROGRESS, FINISHED, CANCELLED |
| screenId | string | ID de pantalla |
| channel | string | Nombre del canal |
| from | date | Fecha inicio (ISO 8601) |
| to | date | Fecha fin (ISO 8601) |
| search | string | Búsqueda por número |
| page | number | Página (default: 1) |
| limit | number | Por página (default: 50) |

**Response (200):**
```json
{
  "data": [
    {
      "id": "ord_001",
      "externalId": "MXP_12345",
      "screenId": "scr_001",
      "channel": "local",
      "customerName": "Juan Pérez",
      "identifier": "123",
      "status": "PENDING",
      "createdAt": "2025-12-15T10:30:00.000Z",
      "finishedAt": null,
      "items": [
        { "id": "item_001", "name": "Pollo Frito", "quantity": 2, "notes": null }
      ]
    }
  ],
  "total": 150,
  "page": 1,
  "limit": 50
}
```

---

### GET /orders/:id
Obtener orden por ID.

---

### GET /orders/screen/:screenId
Obtener órdenes de una pantalla.

**Response (200):**
```json
[
  {
    "id": "ord_001",
    "identifier": "123",
    "channel": "local",
    "status": "PENDING",
    "createdAt": "2025-12-15T10:30:00.000Z",
    "items": [ ... ]
  }
]
```

---

### GET /orders/recently-finished/:screenId
Obtener órdenes recién finalizadas (para undo).

**Query params:**
| Param | Tipo | Default |
|-------|------|---------|
| limit | number | 10 |

---

### GET /orders/stats
Estadísticas generales de órdenes.

**Response (200):**
```json
{
  "today": {
    "total": 150,
    "pending": 12,
    "finished": 135,
    "cancelled": 3
  },
  "avgPrepTime": 245,
  "byChannel": {
    "local": 80,
    "llevar": 45,
    "rappi": 25
  },
  "byHour": [
    { "hour": 10, "count": 15 },
    { "hour": 11, "count": 25 },
    { "hour": 12, "count": 45 }
  ]
}
```

---

### GET /orders/dashboard-stats
Estadísticas para dashboard.

---

### POST /orders/:id/finish
Marcar orden como finalizada.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN, OPERATOR

**Response (200):**
```json
{
  "id": "ord_001",
  "status": "FINISHED",
  "finishedAt": "2025-12-15T10:35:00.000Z"
}
```

---

### POST /orders/:id/undo
Deshacer finalización.

---

### POST /orders/:id/cancel
Cancelar orden.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

---

### DELETE /orders/cleanup
Limpiar órdenes antiguas.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

**Query params:**
| Param | Tipo | Default |
|-------|------|---------|
| olderThanHours | number | 4 |

---

### POST /orders/generate-test
Generar órdenes de prueba.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

**Request:**
```json
{
  "screenId": "scr_001",
  "count": 5
}
```

---

### DELETE /orders/test-orders
Eliminar todas las órdenes de prueba.

---

## 7. Configuración

### GET /config/health
Health check del sistema.

**Response (200):**
```json
{
  "status": "ok",
  "timestamp": "2025-12-15T10:30:00.000Z",
  "services": {
    "database": "connected",
    "redis": "connected",
    "polling": "running"
  }
}
```

---

### GET /config/general
Obtener configuración general.

**Headers:** `Authorization: Bearer <token>`

**Response (200):**
```json
{
  "testMode": false,
  "ticketMode": "POLLING",
  "printMode": "LOCAL",
  "pollingInterval": 2000,
  "orderLifetime": 4,
  "mxpHost": "192.168.1.50",
  "mxpDatabase": "MAXPOINT"
}
```

---

### PUT /config/general
Actualizar configuración general.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

**Request:**
```json
{
  "pollingInterval": 3000,
  "orderLifetime": 6
}
```

---

### GET /config/modes
Obtener modos de operación.

---

### PUT /config/modes
Actualizar modos de operación.

**Request:**
```json
{
  "testMode": false,
  "ticketMode": "POLLING",
  "printMode": "LOCAL"
}
```

---

### GET /config/mxp
Obtener configuración MAXPOINT.

**Headers:** `Authorization: Bearer <token>`
**Rol requerido:** ADMIN

---

### PUT /config/mxp
Actualizar configuración MAXPOINT.

**Request:**
```json
{
  "mxpHost": "192.168.1.50",
  "mxpPort": 1433,
  "mxpUser": "kds_user",
  "mxpPassword": "password",
  "mxpDatabase": "MAXPOINT"
}
```

---

### POST /config/mxp/test
Probar conexión a MAXPOINT.

**Response (200):**
```json
{
  "success": true,
  "message": "Conexión exitosa",
  "version": "Microsoft SQL Server 2019"
}
```

---

### GET /config/polling
Estado del polling.

**Response (200):**
```json
{
  "isRunning": true,
  "interval": 2000,
  "lastPollTime": "2025-12-15T10:30:00.000Z",
  "ordersProcessed": 150
}
```

---

### POST /config/polling/start
Iniciar polling.

---

### POST /config/polling/stop
Detener polling.

---

### POST /config/polling/force
Forzar un ciclo de polling.

---

## 8. Mirror KDS

### POST /mirror/configure
Configurar KDS remota.

**Request:**
```json
{
  "remoteUrl": "http://kds-remota:3000",
  "apiKey": "api_key_remota"
}
```

---

### GET /mirror/test
Probar conexión con KDS remota.

---

### GET /mirror/stats
Estadísticas del mirror.

---

### GET /mirror/orders
Obtener órdenes del mirror.

---

### GET /mirror/screens
Obtener pantallas del mirror.

---

### POST /mirror/disconnect
Desconectar mirror.

---

## 9. WebSocket Events

### Conexión
```javascript
const socket = io('http://servidor:3000', {
  auth: { token: 'Bearer <access_token>' }
});
```

### Eventos Cliente → Servidor

| Evento | Payload | Descripción |
|--------|---------|-------------|
| screen:register | { screenId: string } | Registrar pantalla |
| screen:heartbeat | { screenId: string } | Ping de vida |
| order:finish | { orderId: string, screenId: string } | Finalizar orden |
| order:undo | { orderId: string, screenId: string } | Deshacer |

### Eventos Servidor → Cliente

| Evento | Payload | Descripción |
|--------|---------|-------------|
| screen:orders:update | Order[] | Órdenes actualizadas |
| screen:configUpdated | ScreenConfig | Config modificada |
| screen:statusChanged | { status: string } | Cambio de estado |
| order:finished | { orderId: string } | Confirmación finish |
| order:undone | { orderId: string } | Confirmación undo |

---

**Documento**: API Reference
**Sistema**: KDS v2.0 - Kitchen Display System
**Fecha**: Diciembre 2025
