# KDS v2.0 - Guia de Despliegue con Docker

## Tabla de Contenidos

1. [Requisitos Previos](#requisitos-previos)
2. [Estructura de Archivos Docker](#estructura-de-archivos-docker)
3. [Arquitectura de Contenedores](#arquitectura-de-contenedores)
4. [Despliegue Paso a Paso](#despliegue-paso-a-paso)
5. [Configuracion de Variables](#configuracion-de-variables)
6. [Comandos Docker Compose](#comandos-docker-compose)
7. [Desarrollo Local](#desarrollo-local)
8. [Produccion](#produccion)
9. [Troubleshooting](#troubleshooting)
10. [Actualizaciones](#actualizaciones)

---

## Requisitos Previos

### Software Requerido

| Software | Version Minima | Verificar |
|----------|----------------|-----------|
| Docker | 20.10+ | `docker --version` |
| Docker Compose | 2.0+ | `docker compose version` |
| Git | 2.30+ | `git --version` |

### Recursos de Hardware

| Ambiente | RAM | CPU | Disco |
|----------|-----|-----|-------|
| Desarrollo | 4GB | 2 cores | 10GB |
| Produccion | 8GB | 4 cores | 50GB |

---

## Estructura de Archivos Docker

```
kds-system/
├── infra/
│   ├── docker-compose.yml        # Produccion
│   ├── docker-compose.dev.yml    # Desarrollo
│   ├── Dockerfile.backend        # Backend Node.js
│   ├── Dockerfile.kds-frontend   # Frontend KDS
│   ├── Dockerfile.backoffice     # Backoffice
│   └── nginx/
│       ├── kds-frontend.conf     # Config Nginx KDS
│       └── backoffice.conf       # Config Nginx Backoffice
├── .env                          # Variables de entorno (crear)
└── .dockerignore                 # Archivos a excluir del build
```

---

## Arquitectura de Contenedores

```
┌─────────────────────────────────────────────────────────────────┐
│                       kds-network (bridge)                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────┐  ┌──────────────┐  ┌────────────────────────┐ │
│  │   postgres   │  │    redis     │  │       backend          │ │
│  │   (5432)     │  │    (6379)    │  │       (3000)           │ │
│  │              │  │              │  │                        │ │
│  │  PostgreSQL  │  │  Redis 7    │  │  Node.js/Express       │ │
│  │  15-alpine   │  │  alpine     │  │  + Prisma + Socket.IO  │ │
│  └──────┬───────┘  └──────┬──────┘  └───────────┬────────────┘ │
│         │                 │                      │              │
│         └─────────────────┼──────────────────────┘              │
│                           │                                      │
│              ┌────────────┴────────────┐                        │
│              │                         │                         │
│  ┌───────────▼──────────┐  ┌──────────▼───────────┐            │
│  │    kds-frontend      │  │      backoffice      │            │
│  │       (8080)         │  │        (8081)        │            │
│  │                      │  │                      │            │
│  │  Nginx + React       │  │  Nginx + React       │            │
│  │  (Pantallas cocina)  │  │  (Panel admin)       │            │
│  └──────────────────────┘  └──────────────────────┘            │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
                              │
                    Puertos Expuestos:
                    • 3000  - API Backend
                    • 5432  - PostgreSQL
                    • 6379  - Redis
                    • 8080  - KDS Frontend
                    • 8081  - Backoffice
```

---

## Despliegue Paso a Paso

### Paso 1: Copiar Proyecto al Servidor

```bash
# Desde la maquina de desarrollo, copiar al servidor
scp -r /path/to/kds-system usuario@servidor:/docker/kds-system

# O clonar directamente en el servidor
ssh usuario@servidor
cd /docker
git clone <url-repositorio> kds-system
```

### Paso 2: Crear Archivo .env

Crear el archivo `.env` en la raiz del proyecto (`/docker/kds-system/.env`):

```bash
cd /docker/kds-system
nano .env
```

**Contenido del archivo .env:**

```env
# =============================================================================
# KDS System - Variables de Entorno (Produccion)
# =============================================================================

# GENERAL
NODE_ENV=production
TZ=America/Mexico_City

# =============================================================================
# BASE DE DATOS - PostgreSQL
# =============================================================================
POSTGRES_USER=kds
POSTGRES_PASSWORD=<CONTRASEÑA_SEGURA>
POSTGRES_DB=kds
POSTGRES_PORT=5432

# =============================================================================
# CACHE - Redis
# =============================================================================
REDIS_PASSWORD=<CONTRASEÑA_SEGURA>
REDIS_PORT=6379

# =============================================================================
# AUTENTICACION - JWT (MINIMO 32 CARACTERES)
# =============================================================================
JWT_SECRET=<STRING_ALEATORIO_32_CARACTERES_MINIMO>
JWT_REFRESH_SECRET=<OTRO_STRING_ALEATORIO_32_CARACTERES>
JWT_EXPIRES_IN=15m
JWT_REFRESH_EXPIRES_IN=7d

# =============================================================================
# MAXPOINT - Integracion POS (Opcional)
# =============================================================================
MXP_ENABLED=true
MXP_HOST=<IP_SERVIDOR_SQL>
MXP_SERVER=<IP_SERVIDOR_SQL>
MXP_DATABASE=<NOMBRE_BD_MAXPOINT>
MXP_USER=<USUARIO_SQL>
MXP_PASSWORD=<CONTRASEÑA_SQL>
MXP_PORT=1433
MXP_POLLING_INTERVAL=3000

# =============================================================================
# CONFIGURACION DE PANTALLAS
# =============================================================================
HEARTBEAT_INTERVAL=10000
HEARTBEAT_TIMEOUT=30000

# =============================================================================
# PUERTOS DE SERVICIOS
# =============================================================================
BACKEND_PORT=3000
KDS_FRONTEND_PORT=8080
BACKOFFICE_PORT=8081

# =============================================================================
# URLs DE FRONTEND (dejar vacias - se auto-detectan)
# =============================================================================
VITE_API_URL=
VITE_WS_URL=

# =============================================================================
# LOGS
# =============================================================================
LOG_LEVEL=info
```

**IMPORTANTE:**
- `JWT_SECRET` y `JWT_REFRESH_SECRET` deben tener **minimo 32 caracteres**
- `MXP_HOST` y `MXP_SERVER` deben tener el mismo valor (IP del servidor SQL)
- Las variables `VITE_API_URL` y `VITE_WS_URL` pueden dejarse vacias, el frontend auto-detecta la IP del servidor

### Paso 3: Construir Imagenes

```bash
cd /docker/kds-system

# Construir todas las imagenes
docker compose --env-file .env -f infra/docker-compose.yml build
```

**Nota:** El build puede tomar 5-10 minutos la primera vez.

### Paso 4: Iniciar Servicios

```bash
# Iniciar todos los servicios
docker compose --env-file .env -f infra/docker-compose.yml up -d

# Verificar que todos los contenedores esten corriendo
docker ps
```

Deberias ver 5 contenedores:
- `kds-postgres` (healthy)
- `kds-redis` (healthy)
- `kds-backend` (healthy)
- `kds-frontend` (healthy)
- `kds-backoffice` (healthy)

### Paso 5: Inicializar Base de Datos

```bash
# Aplicar esquema de base de datos
docker exec kds-backend npx prisma db push
```

### Paso 6: Ejecutar Seed (Datos Iniciales)

El seed crea:
- Usuario administrador KFC (admin@kfc.com.ec)
- Configuracion general
- Colas LINEAS y SANDUCHE
- Pantallas 1, 2 y 3

```bash
# Ejecutar seed con Node directamente
docker exec kds-backend node -e "
const { PrismaClient } = require('@prisma/client');
const bcrypt = require('bcryptjs');

const prisma = new PrismaClient();

async function main() {
  console.log('Seeding database...');

  // Crear usuario admin KFC
  const kfcAdminPassword = await bcrypt.hash('cx-dsi2025', 10);
  const kfcAdmin = await prisma.user.upsert({
    where: { email: 'admin@kfc.com.ec' },
    update: { password: kfcAdminPassword },
    create: {
      email: 'admin@kfc.com.ec',
      password: kfcAdminPassword,
      name: 'Administrador KFC',
      role: 'ADMIN',
    },
  });
  console.log('KFC Admin:', kfcAdmin.email);

  // Crear configuracion general
  await prisma.generalConfig.upsert({
    where: { id: 'general' },
    update: {},
    create: {
      id: 'general',
      pollingInterval: 2000,
      orderLifetime: 4,
      logRetentionDays: 5,
    },
  });
  console.log('General config created');

  // Crear cola LINEAS
  const queueLineas = await prisma.queue.upsert({
    where: { name: 'LINEAS' },
    update: {},
    create: {
      name: 'LINEAS',
      description: 'Cola principal para productos de linea (pollo)',
      distribution: 'DISTRIBUTED',
    },
  });
  console.log('Queue LINEAS:', queueLineas.id);

  // Crear cola SANDUCHE
  const queueSanduche = await prisma.queue.upsert({
    where: { name: 'SANDUCHE' },
    update: {},
    create: {
      name: 'SANDUCHE',
      description: 'Cola para sanduches, twisters y rusters',
      distribution: 'DISTRIBUTED',
    },
  });
  console.log('Queue SANDUCHE:', queueSanduche.id);

  // Crear Pantalla 1
  const screen1 = await prisma.screen.upsert({
    where: { name: 'Pantalla1' },
    update: {},
    create: {
      name: 'Pantalla1',
      queueId: queueLineas.id,
      status: 'OFFLINE',
    },
  });
  console.log('Screen 1:', screen1.name);

  // Crear Pantalla 2
  const screen2 = await prisma.screen.upsert({
    where: { name: 'Pantalla2' },
    update: {},
    create: {
      name: 'Pantalla2',
      queueId: queueLineas.id,
      status: 'OFFLINE',
    },
  });
  console.log('Screen 2:', screen2.name);

  // Crear Pantalla 3
  const screen3 = await prisma.screen.upsert({
    where: { name: 'Pantalla3' },
    update: {},
    create: {
      name: 'Pantalla3',
      queueId: queueSanduche.id,
      status: 'OFFLINE',
    },
  });
  console.log('Screen 3:', screen3.name);

  console.log('Seeding completed!');
}

main()
  .catch(console.error)
  .finally(() => prisma.\$disconnect());
"
```

### Paso 7: Verificar Instalacion

```bash
# Verificar health del backend
curl http://localhost:3000/health

# Verificar que las pantallas estan creadas
curl http://localhost:8080/api/screens/by-number/1
curl http://localhost:8080/api/screens/by-number/2
curl http://localhost:8080/api/screens/by-number/3
```

### Paso 8: Acceder a los Servicios

| Servicio | URL | Credenciales |
|----------|-----|--------------|
| KDS Frontend | http://IP_SERVIDOR:8080 | (seleccionar pantalla) |
| KDS Pantalla 1 | http://IP_SERVIDOR:8080/kds/1 | - |
| KDS Pantalla 2 | http://IP_SERVIDOR:8080/kds/2 | - |
| KDS Pantalla 3 | http://IP_SERVIDOR:8080/kds/3 | - |
| Backoffice | http://IP_SERVIDOR:8081 | admin@kfc.com.ec / cx-dsi2025 |
| API | http://IP_SERVIDOR:3000/api | (requiere JWT) |

---

## Configuracion de Variables

### Variables de Entorno Obligatorias

| Variable | Descripcion | Ejemplo |
|----------|-------------|---------|
| `POSTGRES_PASSWORD` | Contraseña PostgreSQL | `MiPassword123*` |
| `REDIS_PASSWORD` | Contraseña Redis | `RedisPass456*` |
| `JWT_SECRET` | Secreto JWT (min 32 chars) | `abc123...` (32+ chars) |
| `JWT_REFRESH_SECRET` | Secreto refresh (min 32 chars) | `xyz789...` (32+ chars) |

### Variables MAXPOINT (Opcional)

| Variable | Descripcion | Ejemplo |
|----------|-------------|---------|
| `MXP_ENABLED` | Habilitar integracion | `true` |
| `MXP_HOST` | IP servidor SQL | `192.168.1.100` |
| `MXP_SERVER` | IP servidor SQL (mismo que HOST) | `192.168.1.100` |
| `MXP_DATABASE` | Nombre base de datos | `MAXPOINT_K027` |
| `MXP_USER` | Usuario SQL | `kds_user` |
| `MXP_PASSWORD` | Contraseña SQL | `SqlPass789*` |
| `MXP_PORT` | Puerto SQL Server | `1433` |

---

## Comandos Docker Compose

### Comandos Basicos

```bash
# IMPORTANTE: Siempre usar --env-file .env
cd /docker/kds-system

# Iniciar servicios
docker compose --env-file .env -f infra/docker-compose.yml up -d

# Detener servicios
docker compose --env-file .env -f infra/docker-compose.yml down

# Ver estado
docker compose --env-file .env -f infra/docker-compose.yml ps

# Ver logs de todos los servicios
docker compose --env-file .env -f infra/docker-compose.yml logs -f

# Ver logs de un servicio especifico
docker compose --env-file .env -f infra/docker-compose.yml logs -f backend
```

### Comandos de Mantenimiento

```bash
# Reiniciar un servicio
docker compose --env-file .env -f infra/docker-compose.yml restart backend

# Reconstruir un servicio
docker compose --env-file .env -f infra/docker-compose.yml up -d --build backend

# Reconstruir frontend (despues de cambios)
docker compose --env-file .env -f infra/docker-compose.yml up -d --build kds-frontend
```

### Comandos de Base de Datos

```bash
# Acceder a PostgreSQL
docker exec -it kds-postgres psql -U kds -d kds

# Ver usuarios
docker exec kds-postgres psql -U kds -d kds -c "SELECT email, name, role FROM \"User\";"

# Ver pantallas
docker exec kds-postgres psql -U kds -d kds -c "SELECT name, status FROM \"Screen\";"

# Backup
docker exec kds-postgres pg_dump -U kds kds > backup_$(date +%Y%m%d).sql
```

---

## Desarrollo Local

Para desarrollo, solo necesitas levantar PostgreSQL y Redis:

```bash
# Solo PostgreSQL y Redis
docker compose -f infra/docker-compose.dev.yml up -d

# Ejecutar backend localmente
cd backend
npm install
npm run dev

# Ejecutar frontend localmente
cd kds-frontend
npm install
npm run dev
```

---

## Troubleshooting

### Error: Variable POSTGRES_PASSWORD es requerida

```bash
# Asegurarse de usar --env-file
docker compose --env-file .env -f infra/docker-compose.yml up -d

# Verificar que .env existe
cat .env | grep POSTGRES_PASSWORD
```

### Error: JWT_SECRET muy corto

El JWT_SECRET debe tener **minimo 32 caracteres**. Generar uno aleatorio:

```bash
openssl rand -base64 32
```

### Error: Health check failing (backend unhealthy)

```bash
# Ver logs del backend
docker logs kds-backend

# Verificar endpoint de health
curl http://localhost:3000/health
```

### Error: Pantallas no encontradas (404)

Ejecutar el seed para crear las pantallas:

```bash
# Ver Paso 6 de este documento
```

### Error: Conexion MAXPOINT rechazada

```bash
# Verificar conectividad desde el contenedor
docker exec kds-backend nc -zv <IP_MAXPOINT> 1433

# Verificar credenciales en logs
docker logs kds-backend | grep -i mxp
```

### Limpiar y reiniciar completamente

```bash
# ADVERTENCIA: Esto elimina todos los datos
docker compose --env-file .env -f infra/docker-compose.yml down -v
docker system prune -af
docker compose --env-file .env -f infra/docker-compose.yml build --no-cache
docker compose --env-file .env -f infra/docker-compose.yml up -d

# Luego ejecutar Paso 5 y 6 nuevamente
```

---

## Actualizaciones

### Actualizar a Nueva Version

```bash
# 1. Hacer backup
docker exec kds-postgres pg_dump -U kds kds > backup_pre_update.sql

# 2. Detener servicios
docker compose --env-file .env -f infra/docker-compose.yml down

# 3. Obtener nueva version (git pull o copiar archivos)
git pull origin main
# O copiar archivos actualizados con scp

# 4. Reconstruir imagenes
docker compose --env-file .env -f infra/docker-compose.yml build

# 5. Aplicar cambios de base de datos (si hay)
docker compose --env-file .env -f infra/docker-compose.yml up -d postgres redis
docker exec kds-backend npx prisma db push

# 6. Iniciar todos los servicios
docker compose --env-file .env -f infra/docker-compose.yml up -d

# 7. Verificar
docker ps
curl http://localhost:3000/health
```

---

## Notas Importantes

1. **Frontend auto-detecta IP**: El frontend KDS detecta automaticamente la IP del servidor desde la URL del navegador. No es necesario configurar `VITE_API_URL`.

2. **Nginx como proxy**: El contenedor kds-frontend incluye Nginx que proxea `/api/*` y `/socket.io/*` al backend.

3. **Seed simplificado**: El seed se ejecuta con Node directamente porque `tsx` no esta disponible en el contenedor de produccion.

4. **Variables MXP duplicadas**: `MXP_HOST` y `MXP_SERVER` deben tener el mismo valor por compatibilidad.

---

## Referencias

- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Prisma Migrations](https://www.prisma.io/docs/concepts/components/prisma-migrate)
- [Nginx Proxy Configuration](https://nginx.org/en/docs/http/ngx_http_proxy_module.html)
