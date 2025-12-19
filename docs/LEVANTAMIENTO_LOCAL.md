# Levantamiento Local - KDS System

Esta guía describe el proceso para levantar el proyecto KDS System en un entorno de desarrollo local.

## Requisitos Previos

- **Docker** y **Docker Compose** instalados
- **Node.js 20+** (LTS)
- **npm** o **yarn**

## Estructura del Proyecto

```
kds-system/
├── backend/          # API Node.js/Express + Prisma
├── kds-frontend/     # Frontend pantallas KDS (React + Vite)
├── backoffice/       # Panel de administración (React + Ant Design)
├── infra/            # Docker Compose y configuración
└── docs/             # Documentación
```

## Paso 1: Levantar Contenedores de Base de Datos

Iniciar PostgreSQL y Redis usando Docker Compose:

```bash
docker compose -f infra/docker-compose.dev.yml up -d
```

Esto levanta:
- **PostgreSQL** (puerto 5432) - Base de datos principal
- **Redis** (puerto 6379) - Cache y pub/sub
- **Adminer** (puerto 8082) - GUI para PostgreSQL
- **Redis Commander** (puerto 8083) - GUI para Redis

Verificar que los contenedores estén corriendo:

```bash
docker ps --filter "name=kds"
```

## Paso 2: Configurar Variables de Entorno

### Backend (.env)

Crear el archivo `backend/.env`:

```env
# Base de Datos PostgreSQL
DATABASE_URL="postgresql://kds_dev:kds_dev_password@localhost:5432/kds_dev?schema=public"

# Redis
REDIS_URL="redis://localhost:6379"

# Servidor
PORT=3000
NODE_ENV=development

# JWT
JWT_SECRET="dev_jwt_secret_key_for_local_testing_32chars"
JWT_ACCESS_EXPIRATION="1h"
JWT_REFRESH_EXPIRATION="7d"

# MAXPOINT (deshabilitado para desarrollo)
MXP_ENABLED=false
MXP_HOST=""
MXP_USER=""
MXP_PASSWORD=""
MXP_DATABASE=""
MXP_PORT=1433

# Polling
POLLING_INTERVAL=3000
ORDER_LIFETIME_HOURS=24

# WebSocket
HEARTBEAT_INTERVAL=10000
HEARTBEAT_TIMEOUT=30000

# Logs
LOG_LEVEL=debug
LOG_RETENTION_DAYS=5

# CORS
CORS_ORIGINS="http://localhost:5173,http://localhost:5174,http://localhost:3000,http://localhost:8080"

# Admin por defecto
ADMIN_EMAIL="admin@kds.local"
ADMIN_PASSWORD="admin123"
```

### KDS Frontend (.env)

Crear el archivo `kds-frontend/.env`:

```env
VITE_API_URL=http://localhost:3000/api
VITE_WS_URL=http://localhost:3000
```

### Backoffice (.env)

Crear el archivo `backoffice/.env`:

```env
VITE_API_URL=http://localhost:3000/api
VITE_WS_URL=http://localhost:3000
```

## Paso 3: Instalar Dependencias

```bash
# Backend
cd backend && npm install

# KDS Frontend
cd ../kds-frontend && npm install

# Backoffice
cd ../backoffice && npm install
```

## Paso 4: Configurar Base de Datos

Desde el directorio `backend/`:

```bash
# Generar cliente Prisma y sincronizar schema
npx prisma generate
npx prisma db push

# Cargar datos de prueba (seed)
npx prisma db seed
```

El seed crea:
- 2 usuarios admin (`admin@kds.local` / `admin123` y `admin@kfc.com.ec` / `cx-dsi2025`)
- 2 colas (LINEAS y SANDUCHE) con canales configurados
- 3 pantallas (Pantalla1, Pantalla2, Pantalla3)
- Configuración general del sistema

## Paso 5: Iniciar los Servicios

Abrir 3 terminales y ejecutar:

**Terminal 1 - Backend:**
```bash
cd backend
npm run dev
```

**Terminal 2 - KDS Frontend:**
```bash
cd kds-frontend
npm run dev
```

**Terminal 3 - Backoffice:**
```bash
cd backoffice
npm run dev
```

## Paso 6: Generar Órdenes de Prueba

Obtener token de autenticación:

```bash
curl -X POST http://localhost:3000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@kds.local","password":"admin123"}'
```

Generar órdenes de prueba (reemplazar `<TOKEN>` con el accessToken obtenido):

```bash
curl -X POST "http://localhost:3000/api/orders/generate-test?count=10" \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json"
```

## URLs de Acceso

| Servicio | URL | Descripción |
|----------|-----|-------------|
| **KDS Pantalla 1** | http://localhost:8080/kds/1 | Pantalla de cocina 1 (Cola LINEAS) |
| **KDS Pantalla 2** | http://localhost:8080/kds/2 | Pantalla de cocina 2 (Cola LINEAS) |
| **KDS Pantalla 3** | http://localhost:8080/kds/3 | Pantalla de cocina 3 (Cola SANDUCHE) |
| **Selector KDS** | http://localhost:8080 | Selector de pantallas |
| **Backoffice** | http://localhost:5174 | Panel de administración |
| **API Backend** | http://localhost:3000/api | API REST |
| **Health Check** | http://localhost:3000/api/config/health | Estado del sistema |
| **Adminer** | http://localhost:8082 | GUI PostgreSQL |
| **Redis Commander** | http://localhost:8083 | GUI Redis |

## Credenciales

### Backoffice / API
- **Email:** `admin@kds.local`
- **Password:** `admin123`

### PostgreSQL (Adminer)
- **Sistema:** PostgreSQL
- **Servidor:** `kds-postgres-dev`
- **Usuario:** `kds_dev`
- **Password:** `kds_dev_password`
- **Base de datos:** `kds_dev`

## Verificar Funcionamiento

### Health Check del Backend
```bash
curl http://localhost:3000/api/config/health
```

Respuesta esperada:
```json
{
  "status": "healthy",
  "checks": {
    "database": true,
    "redis": true,
    "polling": true
  }
}
```

### Verificar Pantallas
```bash
curl http://localhost:8080/api/screens/by-number/1
```

### Verificar Órdenes
```bash
curl http://localhost:3000/api/orders \
  -H "Authorization: Bearer <TOKEN>"
```

## Detener el Entorno

```bash
# Detener contenedores de base de datos
docker compose -f infra/docker-compose.dev.yml down

# Para eliminar también los volúmenes (datos)
docker compose -f infra/docker-compose.dev.yml down -v
```

## Solución de Problemas

### Error de conexión en KDS Frontend
Si las pantallas muestran "Error de conexión con el servidor", verificar que el archivo `kds-frontend/vite.config.ts` tenga el proxy configurado:

```typescript
server: {
  port: 8080,
  host: true,
  proxy: {
    '/api': {
      target: 'http://localhost:3000',
      changeOrigin: true,
    },
    '/socket.io': {
      target: 'http://localhost:3000',
      changeOrigin: true,
      ws: true,
    },
  },
},
```

### Error de conexión en Backoffice
Verificar que `backoffice/vite.config.ts` tenga el proxy apuntando al puerto correcto (3000):

```typescript
proxy: {
  '/api': {
    target: 'http://localhost:3000',
    changeOrigin: true,
  },
  '/socket.io': {
    target: 'http://localhost:3000',
    changeOrigin: true,
    ws: true,
  },
},
```

### Puerto ocupado
Si algún puerto está ocupado, verificar y matar el proceso:

```bash
# Ver qué proceso usa el puerto
lsof -i :3000

# Matar proceso por PID
kill -9 <PID>
```

### Reiniciar base de datos
Si necesitas reiniciar la base de datos desde cero:

```bash
cd backend
npx prisma db push --force-reset
npx prisma db seed
```

## Comandos Útiles

```bash
# Ver logs de contenedores
docker compose -f infra/docker-compose.dev.yml logs -f

# Acceder a PostgreSQL
docker exec -it kds-postgres-dev psql -U kds_dev -d kds_dev

# Acceder a Redis
docker exec -it kds-redis-dev redis-cli

# Abrir Prisma Studio (GUI para la base de datos)
cd backend && npx prisma studio
```
