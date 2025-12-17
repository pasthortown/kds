# Manual Técnico - Sistema KDS v2.0

## Guía de Instalación, Configuración y Mantenimiento

---

## 1. Instalación

### 1.1 Prerrequisitos

#### Software Requerido
```bash
# Docker y Docker Compose
docker --version    # >= 20.10
docker compose version  # >= 2.0

# Git (para clonar repositorio)
git --version       # >= 2.30

# Node.js (solo para desarrollo local)
node --version      # >= 18.x LTS
npm --version       # >= 9.x
```

#### Verificar Puertos Disponibles
```bash
# Linux/macOS
netstat -tlnp | grep -E '3000|5432|6379|8080|8081'

# Windows
netstat -an | findstr "3000 5432 6379 8080 8081"
```

### 1.2 Instalación con Docker (Producción)

#### Paso 1: Clonar Repositorio
```bash
git clone <url-repositorio> kds-system
cd kds-system
```

#### Paso 2: Configurar Variables de Entorno
```bash
# Copiar plantilla
cp .env.example .env

# Editar con valores de producción
nano .env
```

**Variables obligatorias**:
```bash
# Base de datos
POSTGRES_USER=kds
POSTGRES_PASSWORD=<contraseña_segura_32_chars>
POSTGRES_DB=kds

# Cache
REDIS_PASSWORD=<contraseña_segura_32_chars>

# JWT (generar con: openssl rand -base64 32)
JWT_SECRET=<aleatorio_32_chars>
JWT_REFRESH_SECRET=<aleatorio_32_chars>

# MAXPOINT (si aplica)
MXP_ENABLED=true
MXP_SERVER=192.168.1.100
MXP_DATABASE=MAXPOINT
MXP_USER=kds_user
MXP_PASSWORD=<contraseña_maxpoint>
```

#### Paso 3: Construir Imágenes
```bash
docker compose -f infra/docker-compose.yml build
```

#### Paso 4: Iniciar Servicios
```bash
docker compose -f infra/docker-compose.yml up -d
```

#### Paso 5: Verificar Estado
```bash
docker compose -f infra/docker-compose.yml ps

# Todos deben mostrar: Up (healthy)
```

#### Paso 6: Ejecutar Migraciones
```bash
docker compose -f infra/docker-compose.yml exec backend npx prisma migrate deploy
```

#### Paso 7: Cargar Datos Iniciales
```bash
docker compose -f infra/docker-compose.yml exec backend npx prisma db seed
```

### 1.3 Instalación para Desarrollo

#### Paso 1: Instalar Dependencias
```bash
# Backend
cd backend
npm install

# KDS Frontend
cd ../kds-frontend
npm install

# Backoffice
cd ../backoffice
npm install
```

#### Paso 2: Iniciar Infraestructura
```bash
# Desde raíz del proyecto
docker compose -f infra/docker-compose.dev.yml up -d
```

#### Paso 3: Configurar Backend
```bash
cd backend
cp .env.example .env
# Editar .env con valores de desarrollo
```

**.env de desarrollo**:
```bash
NODE_ENV=development
PORT=3000

# Base de datos (docker-compose.dev.yml)
DATABASE_URL=postgresql://kds_dev:kds_dev_password@localhost:5432/kds_dev

# Redis
REDIS_URL=redis://localhost:6379

# JWT
JWT_SECRET=dev_secret_change_in_production
JWT_REFRESH_SECRET=dev_refresh_secret_change

# MAXPOINT (opcional en desarrollo)
MXP_ENABLED=false
```

#### Paso 4: Ejecutar Migraciones
```bash
cd backend
npx prisma migrate dev
npx prisma db seed
```

#### Paso 5: Iniciar Servicios
```bash
# Terminal 1 - Backend
cd backend
npm run dev

# Terminal 2 - KDS Frontend
cd kds-frontend
npm run dev

# Terminal 3 - Backoffice
cd backoffice
npm run dev
```

**URLs de desarrollo**:
- Backend: http://localhost:3000
- KDS Frontend: http://localhost:5173
- Backoffice: http://localhost:5174
- Adminer: http://localhost:8082
- Redis Commander: http://localhost:8083

---

## 2. Configuración

### 2.1 Variables de Entorno Completas

```bash
# =============================================================================
# GENERAL
# =============================================================================
NODE_ENV=production          # development | production
TZ=America/Mexico_City       # Zona horaria

# =============================================================================
# BASE DE DATOS - PostgreSQL
# =============================================================================
POSTGRES_USER=kds
POSTGRES_PASSWORD=<segura>   # Mínimo 16 caracteres
POSTGRES_DB=kds
POSTGRES_PORT=5432
DATABASE_URL=postgresql://${POSTGRES_USER}:${POSTGRES_PASSWORD}@postgres:5432/${POSTGRES_DB}

# =============================================================================
# CACHE - Redis
# =============================================================================
REDIS_PASSWORD=<segura>      # Mínimo 16 caracteres
REDIS_PORT=6379
REDIS_URL=redis://:${REDIS_PASSWORD}@redis:6379

# =============================================================================
# AUTENTICACIÓN - JWT
# =============================================================================
JWT_SECRET=<aleatorio_32_chars>
JWT_REFRESH_SECRET=<aleatorio_32_chars>
JWT_EXPIRES_IN=15m           # Duración access token
JWT_REFRESH_EXPIRES_IN=7d    # Duración refresh token

# =============================================================================
# MAXPOINT - Integración POS
# =============================================================================
MXP_ENABLED=false            # true para habilitar polling
MXP_SERVER=                  # IP o hostname SQL Server
MXP_DATABASE=MAXPOINT        # Nombre de la base de datos
MXP_USER=                    # Usuario SQL Server
MXP_PASSWORD=                # Contraseña
MXP_POLLING_INTERVAL=2000    # Intervalo en ms

# =============================================================================
# MONITOREO - Heartbeat
# =============================================================================
HEARTBEAT_INTERVAL=10000     # Intervalo ping pantallas (ms)
HEARTBEAT_TIMEOUT=30000      # Timeout para marcar offline (ms)

# =============================================================================
# PUERTOS
# =============================================================================
BACKEND_PORT=3000
KDS_FRONTEND_PORT=8080
BACKOFFICE_PORT=8081

# =============================================================================
# FRONTEND
# =============================================================================
VITE_API_URL=/api            # URL base API
VITE_WS_URL=                 # URL WebSocket (vacío = mismo servidor)

# =============================================================================
# IMPRESIÓN
# =============================================================================
PRINTER_ENABLED=false
PRINTER_HOST=                # IP impresora térmica
PRINTER_PORT=9100            # Puerto TCP

# =============================================================================
# LOGS
# =============================================================================
LOG_LEVEL=info               # debug | info | warn | error

# =============================================================================
# CORS
# =============================================================================
CORS_ORIGINS=                # Orígenes permitidos (vacío = todos)
```

### 2.2 Configuración de Nginx (Producción)

Para servir los frontends con proxy reverso:

**/etc/nginx/sites-available/kds**:
```nginx
# KDS Frontend
server {
    listen 80;
    server_name kds.ejemplo.com;

    location / {
        proxy_pass http://localhost:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }

    location /api {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }

    location /socket.io {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
    }
}

# Backoffice
server {
    listen 80;
    server_name admin.kds.ejemplo.com;

    location / {
        proxy_pass http://localhost:8081;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
    }

    location /api {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

### 2.3 Configuración SSL (Let's Encrypt)

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx

# Obtener certificados
sudo certbot --nginx -d kds.ejemplo.com -d admin.kds.ejemplo.com

# Renovación automática
sudo crontab -e
# Agregar:
0 0 1 * * certbot renew --quiet
```

---

## 3. Base de Datos

### 3.1 Esquema Prisma

El esquema completo está en `backend/prisma/schema.prisma`.

**Principales modelos**:

```prisma
model User {
  id        String   @id @default(cuid())
  email     String   @unique
  password  String
  name      String
  role      UserRole @default(OPERATOR)
  active    Boolean  @default(true)
}

model Queue {
  id           String           @id @default(cuid())
  name         String           @unique
  distribution DistributionType @default(DISTRIBUTED)
  screens      Screen[]
  channels     QueueChannel[]
}

model Screen {
  id         String       @id @default(cuid())
  number     Int          @unique
  name       String       @unique
  queueId    String
  status     ScreenStatus @default(OFFLINE)
  apiKey     String       @unique
  appearance Appearance?
  preference Preference?
  keyboard   KeyboardConfig?
  orders     Order[]
}

model Order {
  id         String      @id @default(cuid())
  externalId String      @unique
  screenId   String?
  channel    String
  status     OrderStatus @default(PENDING)
  items      OrderItem[]
}
```

### 3.2 Migraciones

```bash
# Crear nueva migración
npx prisma migrate dev --name nombre_migracion

# Aplicar migraciones en producción
npx prisma migrate deploy

# Reset completo (CUIDADO: borra datos)
npx prisma migrate reset

# Ver estado
npx prisma migrate status
```

### 3.3 Seed (Datos Iniciales)

El archivo `backend/prisma/seed.ts` crea:
- Usuario admin (admin@kds.local / admin123)
- Cola "Cocina Principal"
- Pantalla de ejemplo
- Configuración general

```bash
# Ejecutar seed
npx prisma db seed

# O manualmente
npx tsx prisma/seed.ts
```

### 3.4 Backup y Restore

**Backup**:
```bash
# Desde Docker
docker compose exec postgres pg_dump -U kds kds > backup_$(date +%Y%m%d).sql

# Manual
pg_dump -h localhost -U kds -d kds > backup.sql
```

**Restore**:
```bash
# Desde Docker
docker compose exec -T postgres psql -U kds kds < backup.sql

# Manual
psql -h localhost -U kds -d kds < backup.sql
```

### 3.5 Prisma Studio (GUI)

```bash
cd backend
npx prisma studio
# Abre en http://localhost:5555
```

---

## 4. API REST

### 4.1 Autenticación

Todas las rutas protegidas requieren header:
```
Authorization: Bearer <access_token>
```

**Obtener tokens**:
```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@kds.local",
  "password": "admin123"
}

# Response:
{
  "access_token": "eyJhbGc...",
  "refresh_token": "eyJhbGc...",
  "user": { "id": "...", "email": "...", "role": "ADMIN" }
}
```

**Refrescar token**:
```bash
POST /api/auth/refresh
Content-Type: application/json

{
  "refresh_token": "eyJhbGc..."
}
```

### 4.2 Endpoints Principales

| Método | Endpoint | Auth | Descripción |
|--------|----------|------|-------------|
| GET | /api/health | No | Health check |
| POST | /api/auth/login | No | Login |
| GET | /api/screens | JWT | Listar pantallas |
| POST | /api/screens | JWT/Admin | Crear pantalla |
| GET | /api/queues | JWT | Listar colas |
| GET | /api/orders | JWT | Listar órdenes |
| POST | /api/orders/:id/finish | JWT | Finalizar orden |
| GET | /api/config/general | JWT | Config general |
| PUT | /api/config/general | JWT/Admin | Actualizar config |

### 4.3 WebSocket Events

**Conexión**:
```javascript
import { io } from 'socket.io-client';

const socket = io('http://localhost:3000', {
  auth: { token: 'Bearer <access_token>' }
});
```

**Eventos del cliente → servidor**:
```javascript
// Registrar pantalla
socket.emit('screen:register', { screenId: 'screen_123' });

// Heartbeat
socket.emit('screen:heartbeat', { screenId: 'screen_123' });

// Finalizar orden
socket.emit('order:finish', { orderId: 'order_456', screenId: 'screen_123' });

// Deshacer
socket.emit('order:undo', { orderId: 'order_456', screenId: 'screen_123' });
```

**Eventos del servidor → cliente**:
```javascript
// Órdenes actualizadas
socket.on('screen:orders:update', (orders) => { ... });

// Configuración actualizada
socket.on('screen:configUpdated', (config) => { ... });

// Estado de pantalla
socket.on('screen:statusChanged', (status) => { ... });
```

---

## 5. Servicios del Backend

### 5.1 Polling Service

Consulta periódica a MAXPOINT:

```typescript
// backend/src/services/polling.service.ts
class PollingService {
  private interval: NodeJS.Timeout | null = null;

  async start(intervalMs: number = 2000) {
    this.interval = setInterval(async () => {
      await this.pollOrders();
    }, intervalMs);
  }

  async stop() {
    if (this.interval) {
      clearInterval(this.interval);
    }
  }

  private async pollOrders() {
    // 1. Query a SQL Server MAXPOINT
    // 2. Filtrar órdenes nuevas
    // 3. Upsert en PostgreSQL
    // 4. Distribuir a pantallas
    // 5. Notificar via WebSocket
  }
}
```

**Controlar polling**:
```bash
# Iniciar
POST /api/config/polling/start

# Detener
POST /api/config/polling/stop

# Forzar ciclo
POST /api/config/polling/force

# Estado
GET /api/config/polling
```

### 5.2 Balancer Service

Distribución Round-Robin de órdenes:

```typescript
// backend/src/services/balancer.service.ts
class BalancerService {
  async distributeOrder(order: Order, queue: Queue): Promise<Screen | null> {
    const screens = await this.getActiveScreens(queue.id);

    if (queue.distribution === 'SINGLE') {
      return screens[0];
    }

    // Round-Robin: usar caché Redis para tracking
    const nextIndex = await this.getNextScreenIndex(queue.id, screens.length);
    return screens[nextIndex];
  }
}
```

### 5.3 WebSocket Service

Comunicación en tiempo real:

```typescript
// backend/src/services/websocket.service.ts
class WebSocketService {
  private io: Server;

  async notifyScreenOrders(screenId: string, orders: Order[]) {
    this.io.to(`screen:${screenId}`).emit('screen:orders:update', orders);
  }

  async broadcastConfig(screenId: string, config: any) {
    this.io.to(`screen:${screenId}`).emit('screen:configUpdated', config);
  }
}
```

### 5.4 Printer Service

Impresión TCP directa:

```typescript
// backend/src/services/printer.service.ts
class PrinterService {
  async print(printerIp: string, port: number, data: Buffer): Promise<boolean> {
    return new Promise((resolve, reject) => {
      const client = new net.Socket();
      client.connect(port, printerIp, () => {
        client.write(data);
        client.end();
        resolve(true);
      });
      client.on('error', reject);
    });
  }
}
```

---

## 6. Frontend KDS

### 6.1 Estructura de Estado (Zustand)

```typescript
// store/orderStore.ts
interface OrderState {
  orders: Order[];
  currentPage: number;
  setOrders: (orders: Order[]) => void;
  finishOrder: (orderId: string) => void;
  undoLastFinish: () => void;
}

// store/configStore.ts
interface ConfigState {
  appearance: Appearance;
  preference: Preference;
  keyboard: KeyboardConfig;
  setConfig: (config: ScreenConfig) => void;
}

// store/screenStore.ts
interface ScreenState {
  screenId: string | null;
  isStandby: boolean;
  setStandby: (value: boolean) => void;
}
```

### 6.2 Hook de WebSocket

```typescript
// hooks/useWebSocket.ts
export function useWebSocket(screenId: string) {
  const { setOrders } = useOrderStore();
  const { setConfig } = useConfigStore();

  useEffect(() => {
    socket.emit('screen:register', { screenId });

    socket.on('screen:orders:update', setOrders);
    socket.on('screen:configUpdated', setConfig);

    // Heartbeat cada 10 segundos
    const heartbeat = setInterval(() => {
      socket.emit('screen:heartbeat', { screenId });
    }, 10000);

    return () => {
      clearInterval(heartbeat);
      socket.off('screen:orders:update');
      socket.off('screen:configUpdated');
    };
  }, [screenId]);
}
```

### 6.3 Hook de Teclado

```typescript
// hooks/useKeyboard.ts
export function useKeyboard(config: KeyboardConfig) {
  const [pressedKeys, setPressedKeys] = useState<Set<string>>(new Set());

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      const key = e.key.toLowerCase();
      setPressedKeys(prev => new Set(prev).add(key));

      // Verificar combos
      for (const combo of config.combos) {
        if (combo.keys.every(k => pressedKeys.has(k))) {
          // Iniciar timer para holdTime
        }
      }

      // Acciones simples
      if (key === config.finishFirstOrder) {
        finishOrder(0);
      }
      // ... otras teclas
    };

    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [config, pressedKeys]);
}
```

---

## 7. Monitoreo y Logs

### 7.1 Logs del Backend

```bash
# Ver logs en tiempo real
docker compose logs -f backend

# Últimas 100 líneas
docker compose logs --tail=100 backend

# Filtrar por nivel
docker compose logs backend 2>&1 | grep ERROR
```

**Niveles de log**:
- `debug`: Información detallada de desarrollo
- `info`: Operaciones normales
- `warn`: Situaciones inesperadas no críticas
- `error`: Errores que requieren atención

### 7.2 Métricas de Base de Datos

```sql
-- Órdenes por hora (hoy)
SELECT
  date_trunc('hour', "createdAt") as hora,
  COUNT(*) as total
FROM "Order"
WHERE "createdAt" >= CURRENT_DATE
GROUP BY hora
ORDER BY hora;

-- Tiempo promedio de preparación
SELECT
  AVG(EXTRACT(EPOCH FROM ("finishedAt" - "createdAt"))) as avg_seconds
FROM "Order"
WHERE status = 'FINISHED'
AND "finishedAt" IS NOT NULL
AND "createdAt" >= CURRENT_DATE;

-- Órdenes por pantalla
SELECT
  s.name,
  COUNT(o.id) as total
FROM "Screen" s
LEFT JOIN "Order" o ON o."screenId" = s.id
WHERE o."createdAt" >= CURRENT_DATE
GROUP BY s.id, s.name;
```

### 7.3 Health Check

```bash
# Endpoint de salud
curl http://localhost:3000/api/health

# Response esperado:
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

### 7.4 Monitoreo de Pantallas

El sistema detecta automáticamente pantallas offline cuando no reciben heartbeat en 30 segundos.

```sql
-- Pantallas con último heartbeat
SELECT
  s.name,
  s.status,
  MAX(h.timestamp) as last_heartbeat
FROM "Screen" s
LEFT JOIN "Heartbeat" h ON h."screenId" = s.id
GROUP BY s.id
ORDER BY last_heartbeat DESC NULLS LAST;
```

---

## 8. Mantenimiento

### 8.1 Tareas Programadas

**Limpieza de órdenes antiguas** (automático):
- Las órdenes se eliminan después del `orderLifetime` configurado (default: 4 horas)

**Limpieza de heartbeats** (recomendado):
```sql
-- Eliminar heartbeats mayores a 7 días
DELETE FROM "Heartbeat" WHERE timestamp < NOW() - INTERVAL '7 days';
```

**Limpieza de logs de auditoría**:
```sql
-- Mantener solo últimos 30 días
DELETE FROM "AuditLog" WHERE timestamp < NOW() - INTERVAL '30 days';
```

### 8.2 Actualización del Sistema

```bash
# 1. Detener servicios
docker compose -f infra/docker-compose.yml down

# 2. Actualizar código
git pull origin main

# 3. Reconstruir imágenes
docker compose -f infra/docker-compose.yml build

# 4. Aplicar migraciones
docker compose -f infra/docker-compose.yml run --rm backend npx prisma migrate deploy

# 5. Reiniciar
docker compose -f infra/docker-compose.yml up -d
```

### 8.3 Rollback

```bash
# 1. Detener servicios
docker compose down

# 2. Volver a versión anterior
git checkout <commit_anterior>

# 3. Restaurar backup de BD si es necesario
docker compose exec -T postgres psql -U kds kds < backup_previo.sql

# 4. Reconstruir y reiniciar
docker compose build
docker compose up -d
```

---

## 9. Troubleshooting

### 9.1 Backend no inicia

```bash
# Verificar logs
docker compose logs backend

# Problemas comunes:
# - DATABASE_URL incorrecto
# - PostgreSQL no está listo
# - Puerto 3000 ocupado
```

### 9.2 WebSocket no conecta

```bash
# Verificar CORS
# En .env: CORS_ORIGINS=http://localhost:5173,http://localhost:8080

# Verificar proxy Nginx (si aplica)
# Asegurar headers Upgrade y Connection
```

### 9.3 Polling no funciona

```bash
# Verificar conexión MAXPOINT
curl -X POST http://localhost:3000/api/config/mxp/test \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json"

# Verificar logs
docker compose logs backend | grep -i maxpoint
docker compose logs backend | grep -i polling
```

### 9.4 Pantalla offline persistente

```bash
# Verificar heartbeat
# En consola del navegador de la pantalla:
# socket.connected → debe ser true

# Verificar red
ping servidor-kds

# Limpiar caché y recargar
# Ctrl+Shift+R en la pantalla
```

### 9.5 Órdenes no se distribuyen

```bash
# Verificar:
# 1. Pantallas asignadas a cola
# 2. Pantallas en estado ONLINE
# 3. Canal de la orden coincide con canales de la cola

# Query de diagnóstico
SELECT
  q.name as cola,
  s.name as pantalla,
  s.status,
  qc.channel
FROM "Queue" q
JOIN "Screen" s ON s."queueId" = q.id
LEFT JOIN "QueueChannel" qc ON qc."queueId" = q.id
WHERE q.active = true;
```

---

## 10. Seguridad

### 10.1 Checklist de Producción

- [ ] Cambiar contraseñas por defecto
- [ ] Generar JWT_SECRET aleatorio
- [ ] Habilitar HTTPS
- [ ] Configurar firewall
- [ ] Deshabilitar acceso directo a PostgreSQL
- [ ] Deshabilitar acceso directo a Redis
- [ ] Configurar CORS restrictivo
- [ ] Habilitar rate limiting
- [ ] Configurar backups automáticos

### 10.2 Generación de Secrets

```bash
# JWT Secret
openssl rand -base64 32

# Contraseña PostgreSQL
openssl rand -base64 24

# Contraseña Redis
openssl rand -base64 24
```

### 10.3 Hardening de Docker

```yaml
# docker-compose.yml
services:
  backend:
    security_opt:
      - no-new-privileges:true
    read_only: true
    tmpfs:
      - /tmp
    cap_drop:
      - ALL
```

---

## 11. Comandos Útiles

### Docker
```bash
# Estado de contenedores
docker compose ps

# Logs en tiempo real
docker compose logs -f

# Reiniciar servicio específico
docker compose restart backend

# Shell en contenedor
docker compose exec backend sh

# Limpiar todo (CUIDADO)
docker compose down -v --rmi all
```

### Prisma
```bash
# Generar cliente
npx prisma generate

# Migrar desarrollo
npx prisma migrate dev

# Migrar producción
npx prisma migrate deploy

# Reset BD
npx prisma migrate reset

# GUI
npx prisma studio
```

### NPM
```bash
# Desarrollo
npm run dev

# Build
npm run build

# Lint
npm run lint

# Test
npm test
```

---

**Documento**: Manual Técnico
**Sistema**: KDS v2.0 - Kitchen Display System
**Fecha**: Diciembre 2025
