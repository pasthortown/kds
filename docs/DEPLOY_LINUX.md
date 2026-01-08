# Despliegue Rapido en Linux - KDS System

Guia rapida para desplegar el sistema KDS en un servidor Linux usando Docker.

---

## Requisitos Previos

### Software Requerido

| Software | Version Minima | Verificar con |
|----------|---------------|---------------|
| Docker | 20.10+ | `docker --version` |
| Docker Compose | 1.29+ o 2.0+ | `docker-compose --version` |
| Git | 2.0+ | `git --version` |

### Instalar Docker (si no esta instalado)

```bash
# Ubuntu/Debian
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER

# Cerrar sesion y volver a entrar para aplicar el grupo docker
```

### Recursos Minimos

| Recurso | Minimo | Recomendado |
|---------|--------|-------------|
| CPU | 2 cores | 4 cores |
| RAM | 4 GB | 8 GB |
| Disco | 10 GB | 20 GB |

---

## Despliegue

### Paso 1: Clonar el repositorio

```bash
git clone https://github.com/tu-organizacion/kds-system.git
cd kds-system
```

### Paso 2: Configurar variables de entorno (opcional)

El sistema incluye valores por defecto funcionales. Para personalizar:

```bash
cp .env.example .env
nano .env
```

Variables principales:

| Variable | Default | Descripcion |
|----------|---------|-------------|
| POSTGRES_PASSWORD | kds_secure_password_2025 | Password de PostgreSQL |
| REDIS_PASSWORD | redis_secure_password_2025 | Password de Redis |
| JWT_SECRET | (incluido) | Secret para tokens JWT |
| TZ | America/Bogota | Zona horaria |

### Paso 3: Levantar el sistema

```bash
docker-compose up -d
```

Esto construira las imagenes y levantara todos los servicios. La primera vez puede tomar 3-5 minutos.

---

## Validacion del Despliegue

### 1. Verificar estado de contenedores

```bash
docker-compose ps
```

**Salida esperada** (todos en estado `Up` y `healthy`):

```
     Name                   Command                  State                        Ports
---------------------------------------------------------------------------------------------------------
kds-backend      ./docker-entrypoint.sh           Up (healthy)   0.0.0.0:3000->3000/tcp
kds-backoffice   /docker-entrypoint.sh ngin ...   Up (healthy)   0.0.0.0:8081->80/tcp
kds-frontend     /docker-entrypoint.sh ngin ...   Up (healthy)   0.0.0.0:8080->80/tcp
kds-postgres     docker-entrypoint.sh postgres    Up (healthy)   0.0.0.0:5432->5432/tcp
kds-redis        docker-entrypoint.sh redis ...   Up (healthy)   0.0.0.0:6379->6379/tcp
```

### 2. Verificar health del backend

```bash
curl -s http://localhost:3000/api/config/health | head -c 100
```

**Salida esperada**:
```json
{"status":"ok","timestamp":"..."}
```

### 3. Verificar conectividad de servicios

```bash
# PostgreSQL
docker exec kds-postgres pg_isready -U kds

# Redis
docker exec kds-redis redis-cli -a redis_secure_password_2025 ping
```

**Salidas esperadas**:
- PostgreSQL: `localhost:5432 - accepting connections`
- Redis: `PONG`

### 4. Verificar logs (sin errores criticos)

```bash
# Ver ultimas 50 lineas del backend
docker-compose logs --tail=50 backend

# Ver logs en tiempo real de todos los servicios
docker-compose logs -f
```

### 5. Probar acceso web

```bash
# KDS Frontend
curl -s -o /dev/null -w "%{http_code}" http://localhost:8080

# Backoffice
curl -s -o /dev/null -w "%{http_code}" http://localhost:8081

# API
curl -s -o /dev/null -w "%{http_code}" http://localhost:3000/api/config/health
```

**Salida esperada**: `200` para cada uno.

---

## Acceso a los Servicios

| Servicio | URL | Descripcion |
|----------|-----|-------------|
| KDS Frontend | http://IP_SERVIDOR:8080 | Pantallas de cocina |
| Backoffice | http://IP_SERVIDOR:8081 | Panel de administracion |
| API Backend | http://IP_SERVIDOR:3000/api | API REST |

### Credenciales por defecto

| Usuario | Password | Rol |
|---------|----------|-----|
| admin@kds.local | admin123 | Administrador |
| admin@kfc.com.ec | cx-dsi2025 | Administrador |

---

## Comandos Utiles

### Gestion basica

```bash
# Levantar servicios
docker-compose up -d

# Detener servicios
docker-compose down

# Reiniciar todo
docker-compose restart

# Ver estado
docker-compose ps

# Ver logs
docker-compose logs -f
```

### Mantenimiento

```bash
# Reiniciar un servicio especifico
docker-compose restart backend

# Reconstruir y actualizar
docker-compose up -d --build

# Limpiar todo (ELIMINA DATOS)
docker-compose down -v

# Ver uso de recursos
docker stats
```

### Base de datos

```bash
# Acceder a PostgreSQL
docker exec -it kds-postgres psql -U kds -d kds

# Backup
docker exec kds-postgres pg_dump -U kds kds > backup_$(date +%Y%m%d_%H%M%S).sql

# Restaurar backup
cat backup.sql | docker exec -i kds-postgres psql -U kds -d kds

# Re-ejecutar seed
docker exec kds-backend npx prisma db seed
```

---

## Solucion de Problemas

### Contenedor no inicia o se reinicia

```bash
# Ver logs del contenedor problematico
docker-compose logs backend

# Verificar que postgres y redis esten healthy primero
docker-compose ps postgres redis
```

### Puerto en uso

```bash
# Verificar que puerto esta ocupado
sudo lsof -i :8080

# Cambiar puerto en .env
KDS_FRONTEND_PORT=8082
```

### Permisos de Docker

```bash
# Si hay errores de permisos
sudo usermod -aG docker $USER
# Cerrar sesion y volver a entrar
```

### Resetear completamente

```bash
# Detener y eliminar todo (contenedores, volumenes, redes)
docker-compose down -v --remove-orphans

# Eliminar imagenes del proyecto
docker rmi $(docker images | grep kds-system | awk '{print $3}')

# Volver a construir desde cero
docker-compose up -d --build
```

---

## Actualizaciones

Para actualizar a una nueva version:

```bash
# 1. Obtener cambios
git pull origin main

# 2. Reconstruir y reiniciar
docker-compose up -d --build

# 3. Verificar estado
docker-compose ps
```

---

## Resumen de Comandos

| Accion | Comando |
|--------|---------|
| Desplegar | `docker-compose up -d` |
| Verificar estado | `docker-compose ps` |
| Ver logs | `docker-compose logs -f` |
| Detener | `docker-compose down` |
| Reiniciar | `docker-compose restart` |
| Actualizar | `git pull && docker-compose up -d --build` |
| Limpiar todo | `docker-compose down -v` |
