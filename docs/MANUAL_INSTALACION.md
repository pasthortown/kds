# Manual de Instalacion - KDS System

Este manual describe el proceso completo para instalar el sistema KDS desde cero utilizando Docker.

## Indice

1. [Requisitos Previos](#requisitos-previos)
2. [Estructura del Proyecto](#estructura-del-proyecto)
3. [Configuracion de Variables de Entorno](#configuracion-de-variables-de-entorno)
4. [Construccion de Contenedores](#construccion-de-contenedores)
5. [Inicializacion de la Base de Datos](#inicializacion-de-la-base-de-datos)
6. [Ejecucion del Seed](#ejecucion-del-seed)
7. [Verificacion del Sistema](#verificacion-del-sistema)
8. [Acceso a los Servicios](#acceso-a-los-servicios)
9. [Comandos Utiles](#comandos-utiles)
10. [Solucion de Problemas](#solucion-de-problemas)

---

## Requisitos Previos

### Software Requerido

| Software | Version Minima | Proposito |
|----------|---------------|-----------|
| Docker | 20.10+ | Contenedores |
| Docker Compose | 2.0+ | Orquestacion |
| Git | 2.30+ | Control de versiones |

### Recursos de Hardware Recomendados

| Recurso | Minimo | Recomendado |
|---------|--------|-------------|
| CPU | 2 cores | 4 cores |
| RAM | 4 GB | 8 GB |
| Disco | 20 GB | 50 GB |

### Verificar Instalacion

```bash
docker --version
docker compose version
git --version
```

---

## Estructura del Proyecto

```
kds-system/
├── backend/           # API Node.js/Express
│   ├── prisma/        # Schema y migraciones
│   │   ├── schema.prisma
│   │   └── seed.ts    # Datos iniciales
│   └── src/           # Codigo fuente
├── backoffice/        # Panel de administracion (React)
├── kds-frontend/      # Pantallas de cocina (React)
├── sync/              # Servicio de sincronizacion (.NET)
├── infra/             # Infraestructura Docker
│   ├── docker-compose.yml
│   ├── Dockerfile.backend
│   ├── Dockerfile.backoffice
│   ├── Dockerfile.kds-frontend
│   └── Dockerfile.sync
├── docs/              # Documentacion
├── .env.example       # Plantilla de variables
└── .env               # Variables de entorno (crear)
```

---

## Configuracion de Variables de Entorno

### Paso 1: Copiar plantilla

```bash
cp .env.example .env
```

### Paso 2: Generar secrets seguros

```bash
# Generar password para PostgreSQL
openssl rand -base64 24

# Generar password para Redis
openssl rand -base64 24

# Generar JWT Secret
openssl rand -base64 64

# Generar JWT Refresh Secret
openssl rand -base64 64
```

### Paso 3: Editar archivo .env

```bash
nano .env
```

### Variables Requeridas

```env
# =============================================================================
# GENERAL
# =============================================================================
NODE_ENV=production
TZ=America/Bogota

# =============================================================================
# BASE DE DATOS - PostgreSQL
# =============================================================================
POSTGRES_USER=kds
POSTGRES_PASSWORD=<password_generado>
POSTGRES_DB=kds
POSTGRES_PORT=5432

# =============================================================================
# CACHE - Redis
# =============================================================================
REDIS_PASSWORD=<password_generado>
REDIS_PORT=6379

# =============================================================================
# AUTENTICACION - JWT
# =============================================================================
JWT_SECRET=<secret_generado>
JWT_REFRESH_SECRET=<refresh_secret_generado>
JWT_EXPIRES_IN=15m
JWT_REFRESH_EXPIRES_IN=7d

# =============================================================================
# PUERTOS DE SERVICIOS
# =============================================================================
BACKEND_PORT=3000
KDS_FRONTEND_PORT=8080
BACKOFFICE_PORT=8081

# =============================================================================
# URLs DE FRONTEND
# =============================================================================
VITE_API_URL=/api
VITE_WS_URL=
```

---

## Construccion de Contenedores

### Paso 1: Construir todas las imagenes

```bash
cd /ruta/al/kds-system
docker compose -f infra/docker-compose.yml build
```

Este proceso puede tomar varios minutos la primera vez.

### Paso 2: Verificar imagenes creadas

```bash
docker images | grep kds
```

Deberia mostrar:
- `infra-backend`
- `infra-kds-frontend`
- `infra-backoffice`
- `infra-sync`

---

## Inicializacion de la Base de Datos

### Paso 1: Iniciar solo PostgreSQL y Redis

```bash
docker compose -f infra/docker-compose.yml up -d postgres redis
```

### Paso 2: Verificar que esten saludables

```bash
docker compose -f infra/docker-compose.yml ps
```

Esperar hasta que ambos muestren status `healthy`.

### Paso 3: Iniciar el backend

```bash
docker compose -f infra/docker-compose.yml up -d backend
```

### Paso 4: Aplicar migraciones de Prisma

```bash
docker exec kds-backend npx prisma db push
```

Esto creara todas las tablas en la base de datos segun el schema de Prisma.

---

## Ejecucion del Seed

El seed crea los datos iniciales necesarios para el funcionamiento del sistema:

- Usuarios administradores
- Configuracion general
- Canales de venta predefinidos
- Colas de produccion (LINEAS, SANDUCHE)
- Pantallas con configuracion de apariencia
- Colores SLA

### Ejecutar Seed

```bash
docker exec kds-backend npx prisma db seed
```

### Salida Esperada

```
Seeding database...
User admin@kfc.com.ec created
User admin@kds.local created
GeneralConfig created
9 global channels created
Queue LINEAS created with channels and filters
Queue SANDUCHE created with channels and filters
Screen Pantalla1 created with appearance, preferences, and keyboard config
Screen Pantalla2 created with appearance, preferences, and keyboard config
Screen Pantalla3 created with appearance, preferences, and keyboard config
Seeding completed!
```

### Datos Creados por el Seed

#### Usuarios

| Email | Password | Rol |
|-------|----------|-----|
| admin@kfc.com.ec | cx-dsi2025 | ADMIN |
| admin@kds.local | admin123 | ADMIN |

#### Canales Globales

| Canal | Color Fondo | Prioridad |
|-------|-------------|-----------|
| Local | #7ed321 | 10 |
| Kiosko-Efectivo | #0299d0 | 9 |
| Kiosko-Tarjeta | #d0021b | 8 |
| PedidosYa | #d0021b | 7 |
| RAPPI | #ff5a00 | 6 |
| UberEats | #06c167 | 5 |
| Glovo | #ffc244 | 4 |
| Drive | #9b59b6 | 3 |
| Delivery | #e74c3c | 1 |

#### Colas

| Cola | Distribucion | Descripcion |
|------|--------------|-------------|
| LINEAS | DISTRIBUTED | Cola principal, balanceada entre pantallas |
| SANDUCHE | SINGLE | Cola especializada para sanduches |

#### Colores SLA

| Orden | Tiempo | Color | Fondo Completo |
|-------|--------|-------|----------------|
| 1 | 01:00 | #3e961f (Verde) | No |
| 2 | 02:00 | #9b9728 (Amarillo) | No |
| 3 | 03:00 | #cf1d09 (Rojo) | Si |

---

## Verificacion del Sistema

### Paso 1: Iniciar todos los servicios

```bash
docker compose -f infra/docker-compose.yml up -d
```

### Paso 2: Verificar estado de contenedores

```bash
docker compose -f infra/docker-compose.yml ps
```

Todos los servicios deben estar en estado `Up` o `healthy`:

```
NAME              STATUS                   PORTS
kds-backend       Up (healthy)             0.0.0.0:3000->3000/tcp
kds-backoffice    Up                       0.0.0.0:8081->80/tcp
kds-frontend      Up                       0.0.0.0:8080->80/tcp
kds-postgres      Up (healthy)             0.0.0.0:5432->5432/tcp
kds-redis         Up (healthy)             0.0.0.0:6379->6379/tcp
kds-sync          Up                       0.0.0.0:8100->8100/tcp
```

### Paso 3: Verificar health del backend

```bash
curl http://localhost:3000/api/health
```

Respuesta esperada:
```json
{"status":"ok","timestamp":"...","uptime":...}
```

### Paso 4: Verificar logs

```bash
# Ver logs de todos los servicios
docker compose -f infra/docker-compose.yml logs -f

# Ver logs de un servicio especifico
docker compose -f infra/docker-compose.yml logs -f backend
```

---

## Acceso a los Servicios

### URLs de Acceso

| Servicio | URL | Proposito |
|----------|-----|-----------|
| **Backoffice** | http://localhost:8081 | Panel de administracion |
| **KDS Frontend** | http://localhost:8080 | Pantallas de cocina |
| **API Backend** | http://localhost:3000/api | API REST |
| **Sync Service** | http://localhost:8100 | Sincronizacion MaxPoint |

### Primer Inicio de Sesion

1. Abrir el Backoffice: http://localhost:8081
2. Usar credenciales:
   - Email: `admin@kds.local`
   - Password: `admin123`
3. Cambiar la contrasena inmediatamente por seguridad

### Configurar Pantallas KDS

1. En Backoffice, ir a **Pantallas**
2. Copiar el **API Key** de cada pantalla
3. Abrir KDS Frontend: http://localhost:8080
4. Ingresar el API Key de la pantalla correspondiente

---

## Comandos Utiles

### Gestion de Contenedores

```bash
# Iniciar todos los servicios
docker compose -f infra/docker-compose.yml up -d

# Detener todos los servicios
docker compose -f infra/docker-compose.yml down

# Reiniciar un servicio especifico
docker compose -f infra/docker-compose.yml restart backend

# Ver logs en tiempo real
docker compose -f infra/docker-compose.yml logs -f

# Reconstruir imagenes
docker compose -f infra/docker-compose.yml build --no-cache

# Reconstruir y reiniciar
docker compose -f infra/docker-compose.yml up -d --build
```

### Base de Datos

```bash
# Acceder a PostgreSQL
docker exec -it kds-postgres psql -U kds -d kds

# Backup de la base de datos
docker exec kds-postgres pg_dump -U kds kds > backup_$(date +%Y%m%d).sql

# Restaurar backup
cat backup.sql | docker exec -i kds-postgres psql -U kds -d kds

# Re-ejecutar seed (cuidado: puede duplicar datos)
docker exec kds-backend npx prisma db seed

# Resetear base de datos (ELIMINA TODO)
docker exec kds-backend npx prisma db push --force-reset
docker exec kds-backend npx prisma db seed
```

### Prisma

```bash
# Generar cliente Prisma
docker exec kds-backend npx prisma generate

# Ver estado de la base de datos
docker exec kds-backend npx prisma db pull

# Abrir Prisma Studio (GUI para la BD)
docker exec -it kds-backend npx prisma studio
```

---

## Solucion de Problemas

### El backend no inicia

**Sintoma**: El contenedor `kds-backend` se reinicia constantemente.

**Solucion**:
```bash
# Ver logs del backend
docker logs kds-backend

# Causas comunes:
# 1. PostgreSQL no esta listo - esperar a que este healthy
# 2. Variables de entorno incorrectas - verificar .env
# 3. Error en migraciones - ejecutar prisma db push
```

### Error de conexion a la base de datos

**Sintoma**: `Error: connect ECONNREFUSED`

**Solucion**:
```bash
# Verificar que PostgreSQL este corriendo
docker compose -f infra/docker-compose.yml ps postgres

# Verificar conectividad
docker exec kds-backend nc -zv postgres 5432

# Verificar DATABASE_URL en .env
# Debe ser: postgresql://kds:PASSWORD@postgres:5432/kds?schema=public
```

### Las pantallas no se conectan

**Sintoma**: Las pantallas muestran "Offline" o no reciben ordenes.

**Solucion**:
1. Verificar que el API Key sea correcto
2. Verificar que el backend este saludable
3. Revisar la consola del navegador para errores
4. Verificar CORS si se accede desde otro dominio

### Error de zona horaria

**Sintoma**: Las horas se muestran incorrectas.

**Solucion**:
```bash
# Verificar TZ en .env
TZ=America/Bogota

# Reconstruir contenedores
docker compose -f infra/docker-compose.yml up -d --build
```

### Limpiar todo y empezar de nuevo

```bash
# Detener y eliminar contenedores, redes y volumenes
docker compose -f infra/docker-compose.yml down -v

# Eliminar imagenes
docker rmi $(docker images | grep infra | awk '{print $3}')

# Reconstruir todo
docker compose -f infra/docker-compose.yml build --no-cache
docker compose -f infra/docker-compose.yml up -d

# Inicializar base de datos
docker exec kds-backend npx prisma db push
docker exec kds-backend npx prisma db seed
```

---

## Resumen de Pasos

1. Clonar repositorio
2. Copiar `.env.example` a `.env`
3. Generar y configurar secrets en `.env`
4. Construir contenedores: `docker compose -f infra/docker-compose.yml build`
5. Iniciar servicios: `docker compose -f infra/docker-compose.yml up -d`
6. Aplicar schema: `docker exec kds-backend npx prisma db push`
7. Ejecutar seed: `docker exec kds-backend npx prisma db seed`
8. Acceder a Backoffice: http://localhost:8081
9. Configurar pantallas y usuarios

---

## Actualizaciones

Para actualizar el sistema a una nueva version:

```bash
# 1. Obtener cambios del repositorio
git pull origin main

# 2. Reconstruir imagenes
docker compose -f infra/docker-compose.yml build

# 3. Reiniciar servicios
docker compose -f infra/docker-compose.yml up -d

# 4. Aplicar nuevas migraciones (si las hay)
docker exec kds-backend npx prisma db push
```
