# Guia de Despliegue - Sistema KDS v2.0

## Instrucciones Paso a Paso para Produccion

---

## 1. Despliegue con Docker (Recomendado)

### Prerrequisitos
- Docker 20.10+
- Docker Compose 2.0+
- 4 GB RAM minimo
- 20 GB disco

### Paso 1: Copiar Proyecto al Servidor

```bash
# Opcion A: Copiar desde maquina de desarrollo
scp -r /path/to/kds-system usuario@servidor:/docker/kds-system

# Opcion B: Clonar repositorio
ssh usuario@servidor
cd /docker
git clone <url-repositorio> kds-system
```

### Paso 2: Crear archivo .env

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

# BASE DE DATOS - PostgreSQL
POSTGRES_USER=kds
POSTGRES_PASSWORD=<CONTRASEÑA_SEGURA>
POSTGRES_DB=kds
POSTGRES_PORT=5432

# CACHE - Redis
REDIS_PASSWORD=<CONTRASEÑA_SEGURA>
REDIS_PORT=6379

# AUTENTICACION - JWT (MINIMO 32 CARACTERES)
JWT_SECRET=<STRING_ALEATORIO_32_CARACTERES_MINIMO>
JWT_REFRESH_SECRET=<OTRO_STRING_ALEATORIO_32_CARACTERES>
JWT_EXPIRES_IN=15m
JWT_REFRESH_EXPIRES_IN=7d

# MAXPOINT - Integracion POS (si aplica)
MXP_ENABLED=true
MXP_HOST=<IP_SERVIDOR_SQL>
MXP_SERVER=<IP_SERVIDOR_SQL>
MXP_DATABASE=<NOMBRE_BD_MAXPOINT>
MXP_USER=<USUARIO_SQL>
MXP_PASSWORD=<CONTRASEÑA_SQL>
MXP_PORT=1433
MXP_POLLING_INTERVAL=3000

# CONFIGURACION
HEARTBEAT_INTERVAL=10000
HEARTBEAT_TIMEOUT=30000

# PUERTOS
BACKEND_PORT=3000
KDS_FRONTEND_PORT=8080
BACKOFFICE_PORT=8081

# URLs (dejar vacias - se auto-detectan)
VITE_API_URL=
VITE_WS_URL=

# LOGS
LOG_LEVEL=info
```

**IMPORTANTE:**
- `JWT_SECRET` y `JWT_REFRESH_SECRET` deben tener **minimo 32 caracteres**
- `MXP_HOST` y `MXP_SERVER` deben tener el mismo valor
- Las variables `VITE_*` pueden dejarse vacias (el frontend auto-detecta la IP)

### Paso 3: Construir e Iniciar

```bash
cd /docker/kds-system

# Construir imagenes (5-10 minutos primera vez)
docker compose --env-file .env -f infra/docker-compose.yml build

# Iniciar servicios
docker compose --env-file .env -f infra/docker-compose.yml up -d

# Verificar estado
docker ps
```

Debe mostrar 5 contenedores con estado "healthy":
- kds-postgres
- kds-redis
- kds-backend
- kds-frontend
- kds-backoffice

### Paso 4: Inicializar Base de Datos

```bash
# Aplicar esquema
docker exec kds-backend npx prisma db push
```

### Paso 5: Ejecutar Seed (Datos Iniciales)

El seed crea el usuario admin, colas y pantallas por defecto:

```bash
docker exec kds-backend node -e "
const { PrismaClient } = require('@prisma/client');
const bcrypt = require('bcryptjs');
const prisma = new PrismaClient();

async function main() {
  console.log('Seeding database...');

  // Usuario admin
  const adminPassword = await bcrypt.hash('cx-dsi2025', 10);
  await prisma.user.upsert({
    where: { email: 'admin@kfc.com.ec' },
    update: { password: adminPassword },
    create: {
      email: 'admin@kfc.com.ec',
      password: adminPassword,
      name: 'Administrador KFC',
      role: 'ADMIN',
    },
  });

  // Configuracion general
  await prisma.generalConfig.upsert({
    where: { id: 'general' },
    update: {},
    create: { id: 'general', pollingInterval: 2000, orderLifetime: 4, logRetentionDays: 5 },
  });

  // Cola LINEAS
  const queueLineas = await prisma.queue.upsert({
    where: { name: 'LINEAS' },
    update: {},
    create: { name: 'LINEAS', description: 'Cola principal (pollo)', distribution: 'DISTRIBUTED' },
  });

  // Cola SANDUCHE
  const queueSanduche = await prisma.queue.upsert({
    where: { name: 'SANDUCHE' },
    update: {},
    create: { name: 'SANDUCHE', description: 'Cola sanduches', distribution: 'DISTRIBUTED' },
  });

  // Pantallas
  await prisma.screen.upsert({ where: { name: 'Pantalla1' }, update: {}, create: { name: 'Pantalla1', queueId: queueLineas.id, status: 'OFFLINE' } });
  await prisma.screen.upsert({ where: { name: 'Pantalla2' }, update: {}, create: { name: 'Pantalla2', queueId: queueLineas.id, status: 'OFFLINE' } });
  await prisma.screen.upsert({ where: { name: 'Pantalla3' }, update: {}, create: { name: 'Pantalla3', queueId: queueSanduche.id, status: 'OFFLINE' } });

  console.log('Seed completado!');
}

main().catch(console.error).finally(() => prisma.\$disconnect());
"
```

### Paso 6: Verificar Instalacion

```bash
# Health check
curl http://localhost:3000/health

# Verificar pantallas
curl http://localhost:8080/api/screens/by-number/1
curl http://localhost:8080/api/screens/by-number/2
curl http://localhost:8080/api/screens/by-number/3
```

### Paso 7: Acceder al Sistema

| Servicio | URL | Credenciales |
|----------|-----|--------------|
| Selector Pantallas | http://IP_SERVIDOR:8080 | - |
| KDS Pantalla 1 | http://IP_SERVIDOR:8080/kds/1 | - |
| KDS Pantalla 2 | http://IP_SERVIDOR:8080/kds/2 | - |
| KDS Pantalla 3 | http://IP_SERVIDOR:8080/kds/3 | - |
| Backoffice | http://IP_SERVIDOR:8081 | admin@kfc.com.ec / cx-dsi2025 |

---

## 2. Configuracion de Red

### 2.1 Firewall (UFW - Ubuntu)
```bash
# Permitir puertos
sudo ufw allow 8080/tcp   # KDS Frontend
sudo ufw allow 8081/tcp   # Backoffice

# Bloquear acceso directo a BD (recomendado)
sudo ufw deny 5432/tcp    # PostgreSQL
sudo ufw deny 6379/tcp    # Redis

# Activar firewall
sudo ufw enable
```

### 2.2 Firewall (Windows)
```powershell
netsh advfirewall firewall add rule name="KDS Frontend" dir=in action=allow protocol=tcp localport=8080
netsh advfirewall firewall add rule name="KDS Backoffice" dir=in action=allow protocol=tcp localport=8081
```

---

## 3. Configuracion de Pantallas

### 3.1 URLs de Acceso

El frontend KDS **auto-detecta la IP del servidor** desde la URL del navegador:

```
http://10.101.27.79:8080/kds/1  -> Conecta a API en http://10.101.27.79:3000
http://192.168.1.50:8080/kds/2  -> Conecta a API en http://192.168.1.50:3000
```

No es necesario configurar `VITE_API_URL` o `VITE_SOCKET_URL`.

### 3.2 Autoarranque en Raspberry Pi (Kiosk)

```bash
mkdir -p ~/.config/autostart
nano ~/.config/autostart/kds.desktop
```

Contenido:
```ini
[Desktop Entry]
Type=Application
Name=KDS
Exec=chromium-browser --kiosk --disable-infobars --noerrdialogs http://SERVIDOR:8080/kds/1
```

### 3.3 Autoarranque en Windows (Kiosk)

1. Crear acceso directo a Chrome
2. Propiedades -> Destino:
```
"C:\Program Files\Google\Chrome\Application\chrome.exe" --kiosk http://SERVIDOR:8080/kds/1
```
3. Mover a `shell:startup`

---

## 4. Configuracion MAXPOINT

### 4.1 Variables de Entorno

```env
MXP_ENABLED=true
MXP_HOST=192.168.1.100        # IP SQL Server
MXP_SERVER=192.168.1.100      # Mismo valor que MXP_HOST
MXP_DATABASE=MAXPOINT_K027    # Nombre de la BD
MXP_USER=kds_user             # Usuario SQL
MXP_PASSWORD=contraseña       # Contraseña SQL
MXP_PORT=1433                 # Puerto (default 1433)
```

### 4.2 Permisos SQL Server

```sql
-- Crear usuario con permisos de lectura
CREATE LOGIN kds_user WITH PASSWORD = 'contraseña_segura';
USE MAXPOINT;
CREATE USER kds_user FOR LOGIN kds_user;
GRANT SELECT ON dbo.Tickets TO kds_user;
GRANT SELECT ON dbo.TicketItems TO kds_user;
```

---

## 5. Backup y Restore

### 5.1 Backup Manual
```bash
docker exec kds-postgres pg_dump -U kds kds > backup_$(date +%Y%m%d).sql
```

### 5.2 Backup Automatico (Cron)
```bash
# Agregar a crontab (backup diario 2 AM)
0 2 * * * docker exec kds-postgres pg_dump -U kds kds | gzip > /backups/kds_$(date +\%Y\%m\%d).sql.gz
```

### 5.3 Restore
```bash
docker exec -i kds-postgres psql -U kds kds < backup_20251215.sql
```

---

## 6. Monitoreo

### 6.1 Logs en Tiempo Real
```bash
# Todos los servicios
docker compose --env-file .env -f infra/docker-compose.yml logs -f

# Solo backend
docker logs -f kds-backend
```

### 6.2 Estado de Servicios
```bash
docker ps
docker stats
```

### 6.3 Health Check
```bash
curl http://localhost:3000/health
```

---

## 7. Actualizacion del Sistema

```bash
# 1. Backup
docker exec kds-postgres pg_dump -U kds kds > backup_pre_update.sql

# 2. Detener
docker compose --env-file .env -f infra/docker-compose.yml down

# 3. Actualizar codigo
git pull origin main  # o copiar archivos con scp

# 4. Reconstruir
docker compose --env-file .env -f infra/docker-compose.yml build

# 5. Aplicar cambios de BD
docker compose --env-file .env -f infra/docker-compose.yml up -d postgres redis
sleep 10
docker exec kds-backend npx prisma db push

# 6. Iniciar todo
docker compose --env-file .env -f infra/docker-compose.yml up -d

# 7. Verificar
docker ps
curl http://localhost:3000/health
```

---

## 8. Troubleshooting

### Error: POSTGRES_PASSWORD es requerido
```bash
# Asegurarse de usar --env-file
docker compose --env-file .env -f infra/docker-compose.yml up -d
```

### Error: JWT_SECRET muy corto
```bash
# Generar secreto de 32+ caracteres
openssl rand -base64 32
```

### Error: Pantallas no encontradas
```bash
# Ejecutar seed (Paso 5)
```

### Error: Conexion MAXPOINT
```bash
# Verificar conectividad
docker exec kds-backend nc -zv <IP_MAXPOINT> 1433

# Ver logs
docker logs kds-backend | grep -i mxp
```

### Reiniciar completamente
```bash
docker compose --env-file .env -f infra/docker-compose.yml down -v
docker compose --env-file .env -f infra/docker-compose.yml build --no-cache
docker compose --env-file .env -f infra/docker-compose.yml up -d
# Luego ejecutar Paso 4 y 5
```

---

## 9. Checklist de Produccion

### Antes del Despliegue
- [ ] Archivo .env creado con valores seguros
- [ ] JWT_SECRET tiene 32+ caracteres
- [ ] Contraseñas no son valores por defecto
- [ ] Firewall configurado

### Despues del Despliegue
- [ ] 5 contenedores corriendo (docker ps)
- [ ] Health check responde OK
- [ ] Login funciona en backoffice
- [ ] Pantallas cargan correctamente
- [ ] Backup configurado

---

## 10. Notas Importantes

1. **Frontend auto-detecta IP**: No es necesario configurar `VITE_API_URL`. El frontend usa `window.location.origin` para conectar al backend.

2. **Nginx como proxy**: El contenedor kds-frontend incluye Nginx que proxea `/api/*` y `/socket.io/*` al backend.

3. **Variables MXP duplicadas**: `MXP_HOST` y `MXP_SERVER` deben tener el mismo valor por compatibilidad.

4. **Seed manual**: El seed se ejecuta con `node -e` porque `tsx` no esta disponible en produccion.

5. **--env-file obligatorio**: Siempre usar `--env-file .env` en los comandos docker compose.

---

**Documento**: Guia de Despliegue
**Sistema**: KDS v2.0 - Kitchen Display System
**Fecha**: Diciembre 2025
