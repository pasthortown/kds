# Autenticación y Renovación de Tokens

El KDS utiliza autenticación basada en JWT (JSON Web Tokens) para proteger los endpoints de la API.

## Conceptos Básicos

### Tipos de Tokens

| Token | Duración | Uso |
|-------|----------|-----|
| **Access Token** | 15 minutos | Autorizar peticiones a la API |
| **Refresh Token** | 7 días | Obtener nuevos access tokens sin re-autenticarse |

### Roles de Usuario

| Rol | Permisos |
|-----|----------|
| `ADMIN` | Acceso total: configuración, órdenes, usuarios |
| `OPERATOR` | Enviar comandas, actualizar estados de órdenes |
| `VIEWER` | Solo lectura de órdenes y configuraciones |

Para enviar comandas se requiere rol `ADMIN` u `OPERATOR`.

## Proceso de Autenticación

### 1. Obtener Tokens (Login)

**Endpoint:** `POST /api/auth/login`

**Request:**
```http
POST /api/auth/login HTTP/1.1
Host: kds.empresa.com
Content-Type: application/json

{
  "email": "usuario@empresa.com",
  "password": "contraseña_segura"
}
```

**Response (200 OK):**
```json
{
  "accessToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "refreshToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "userId": "clx1234567890",
    "email": "usuario@empresa.com",
    "role": "OPERATOR"
  }
}
```

**Response (401 Unauthorized):**
```json
{
  "error": "Invalid credentials"
}
```

### 2. Usar Access Token

Incluir el token en el header `Authorization` de todas las peticiones protegidas:

```http
POST /api/tickets/receive HTTP/1.1
Host: kds.empresa.com
Content-Type: application/json
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...

{
  "id": "ORD-001",
  ...
}
```

### 3. Renovar Access Token

Cuando el access token expire (error 401), usar el refresh token para obtener uno nuevo:

**Endpoint:** `POST /api/auth/refresh`

**Request:**
```http
POST /api/auth/refresh HTTP/1.1
Host: kds.empresa.com
Content-Type: application/json

{
  "refreshToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Response (200 OK):**
```json
{
  "accessToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Response (401 Unauthorized):**
```json
{
  "error": "Invalid refresh token"
}
```

Si el refresh token también es inválido o expiró, se debe realizar login nuevamente.

## Diagrama de Flujo

```
┌─────────────────────────────────────────────────────────────────┐
│                    FLUJO DE AUTENTICACIÓN                       │
└─────────────────────────────────────────────────────────────────┘

  ┌──────────┐                                    ┌──────────┐
  │  Cliente │                                    │   KDS    │
  └────┬─────┘                                    └────┬─────┘
       │                                               │
       │  1. POST /api/auth/login                      │
       │  { email, password }                          │
       │──────────────────────────────────────────────>│
       │                                               │
       │  { accessToken, refreshToken, user }          │
       │<──────────────────────────────────────────────│
       │                                               │
       │  2. POST /api/tickets/receive                 │
       │  Authorization: Bearer {accessToken}          │
       │──────────────────────────────────────────────>│
       │                                               │
       │  { success: true }                            │
       │<──────────────────────────────────────────────│
       │                                               │
       │  ... (15 minutos después)                     │
       │                                               │
       │  3. POST /api/tickets/receive                 │
       │  Authorization: Bearer {accessToken}          │
       │──────────────────────────────────────────────>│
       │                                               │
       │  401 Unauthorized (token expirado)            │
       │<──────────────────────────────────────────────│
       │                                               │
       │  4. POST /api/auth/refresh                    │
       │  { refreshToken }                             │
       │──────────────────────────────────────────────>│
       │                                               │
       │  { accessToken: nuevo }                       │
       │<──────────────────────────────────────────────│
       │                                               │
       │  5. Reintentar con nuevo token                │
       │  POST /api/tickets/receive                    │
       │  Authorization: Bearer {nuevoAccessToken}     │
       │──────────────────────────────────────────────>│
       │                                               │
       │  { success: true }                            │
       │<──────────────────────────────────────────────│
       │                                               │
```

## Estructura del JWT

### Access Token Payload

```json
{
  "userId": "clx1234567890",
  "email": "usuario@empresa.com",
  "role": "OPERATOR",
  "iat": 1705330800,
  "exp": 1705331700
}
```

| Campo | Descripción |
|-------|-------------|
| `userId` | ID único del usuario |
| `email` | Email del usuario |
| `role` | Rol del usuario (ADMIN, OPERATOR, VIEWER) |
| `iat` | Issued At - Fecha de emisión (Unix timestamp) |
| `exp` | Expiration - Fecha de expiración (Unix timestamp) |

### Refresh Token Payload

```json
{
  "userId": "clx1234567890",
  "iat": 1705330800,
  "exp": 1705935600
}
```

## Códigos de Error de Autenticación

| Código | Error | Descripción | Acción |
|--------|-------|-------------|--------|
| 400 | `"Refresh token required"` | No se envió refresh token | Incluir token en body |
| 401 | `"No authorization header"` | Falta header Authorization | Agregar header |
| 401 | `"Invalid authorization format"` | Formato incorrecto | Usar `Bearer {token}` |
| 401 | `"Invalid or expired token"` | Token inválido o expirado | Renovar o re-autenticar |
| 401 | `"Invalid credentials"` | Email o contraseña incorrectos | Verificar credenciales |
| 401 | `"Invalid refresh token"` | Refresh token inválido | Realizar login nuevamente |
| 403 | `"Insufficient permissions"` | Rol insuficiente | Usar usuario con rol adecuado |

## Estrategia de Renovación Recomendada

### Opción 1: Renovación Proactiva

Renovar el token antes de que expire (recomendado):

```
1. Al recibir accessToken, calcular tiempo de expiración
2. Programar renovación 2-3 minutos antes de expirar
3. Renovar automáticamente en segundo plano
```

### Opción 2: Renovación Reactiva

Renovar al recibir error 401:

```
1. Intentar petición con accessToken actual
2. Si recibe 401:
   a. Intentar renovar con refreshToken
   b. Si éxito: reintentar petición original
   c. Si falla: realizar login completo
```

## Manejo de Sesión

### Iniciar Sesión (Startup)

```
1. Verificar si hay tokens guardados
2. Si hay refreshToken guardado:
   a. Intentar renovar accessToken
   b. Si éxito: usar tokens
   c. Si falla: realizar login
3. Si no hay tokens: realizar login
```

### Almacenamiento Seguro

- **Access Token**: Puede mantenerse solo en memoria (volátil)
- **Refresh Token**: Guardar de forma segura (archivo cifrado, keychain, etc.)
- **Credenciales**: Nunca guardar en texto plano

## Consideraciones de Seguridad

1. **HTTPS**: Siempre usar conexiones HTTPS en producción
2. **Credenciales**: No hardcodear credenciales en el código fuente
3. **Tokens**: No exponer tokens en logs o mensajes de error
4. **Refresh Token**: Tratarlo como dato sensible (equivale a credenciales)
5. **Expiración**: Respetar los tiempos de expiración configurados
