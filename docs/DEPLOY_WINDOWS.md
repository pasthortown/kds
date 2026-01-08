# Despliegue en Windows (Sin Docker) - KDS System

Guia completa para desplegar el sistema KDS en Windows sin utilizar Docker.

---

## Indice

1. [Requisitos Previos](#requisitos-previos)
2. [Instalacion de Dependencias](#instalacion-de-dependencias)
3. [Despliegue del Sistema](#despliegue-del-sistema)
4. [Configuracion como Servicios](#configuracion-como-servicios)
5. [Validacion](#validacion)
6. [Comandos Utiles](#comandos-utiles)
7. [Solucion de Problemas](#solucion-de-problemas)

---

## Requisitos Previos

### Software Requerido

| Software | Version | Descarga |
|----------|---------|----------|
| Windows | 10/11 o Server 2016+ | - |
| PostgreSQL | 15+ | https://www.postgresql.org/download/windows/ |
| Redis (Memurai) | 4+ | https://www.memurai.com/get-memurai |
| Node.js | 20 LTS | https://nodejs.org/ |
| Git | 2.0+ | https://git-scm.com/download/win |

### Recursos de Hardware

| Recurso | Minimo | Recomendado |
|---------|--------|-------------|
| CPU | 2 cores | 4 cores |
| RAM | 4 GB | 8 GB |
| Disco | 10 GB | 20 GB |

---

## Instalacion de Dependencias

### 1. PostgreSQL

1. Descargar el instalador de https://www.postgresql.org/download/windows/
2. Ejecutar el instalador y seguir el asistente
3. Durante la instalacion:
   - Recordar el password del usuario `postgres`
   - Puerto por defecto: `5432`
   - Incluir pgAdmin (opcional pero recomendado)

4. Verificar instalacion:
```cmd
"C:\Program Files\PostgreSQL\15\bin\psql.exe" --version
```

5. Crear usuario y base de datos para KDS:
```cmd
"C:\Program Files\PostgreSQL\15\bin\psql.exe" -U postgres
```

```sql
CREATE USER kds WITH PASSWORD 'kds_secure_password_2025';
CREATE DATABASE kds OWNER kds;
GRANT ALL PRIVILEGES ON DATABASE kds TO kds;
\q
```

### 2. Redis (Memurai)

Memurai es la version de Redis para Windows mas compatible y mantenida.

1. Descargar de https://www.memurai.com/get-memurai
2. Ejecutar el instalador
3. El servicio se instala automaticamente

4. Configurar password (opcional pero recomendado):
   - Abrir `C:\Program Files\Memurai\memurai.conf`
   - Agregar linea: `requirepass redis_secure_password_2025`
   - Reiniciar servicio: `Restart-Service Memurai`

5. Verificar:
```cmd
"C:\Program Files\Memurai\memurai-cli.exe" ping
```
Respuesta esperada: `PONG`

### 3. Node.js

1. Descargar Node.js 20 LTS de https://nodejs.org/
2. Ejecutar el instalador
3. Marcar la opcion de agregar al PATH
4. Verificar:
```cmd
node --version
npm --version
```

### 4. Git

1. Descargar de https://git-scm.com/download/win
2. Ejecutar el instalador con opciones por defecto
3. Verificar:
```cmd
git --version
```

---

## Despliegue del Sistema

### Paso 1: Clonar el repositorio

```cmd
cd C:\
git clone https://github.com/tu-organizacion/kds-system.git
cd kds-system
```

### Paso 2: Configurar

```powershell
cd deploy-windows

# Copiar plantilla de configuracion
Copy-Item config.example.ps1 config.ps1

# Editar configuracion
notepad config.ps1
```

**Variables importantes a configurar:**

```powershell
# Rutas (ajustar si es necesario)
$KDS_ROOT = "C:\kds-system"

# PostgreSQL
$POSTGRES_PASSWORD = "tu_password_seguro"
$POSTGRES_BIN = "C:\Program Files\PostgreSQL\15\bin"

# Redis
$REDIS_PASSWORD = "tu_password_redis"
$REDIS_PATH = "C:\Program Files\Memurai"

# JWT (generar valores unicos)
$JWT_SECRET = "generar_string_aleatorio_largo"
$JWT_REFRESH_SECRET = "otro_string_aleatorio_diferente"
```

### Paso 3: Ejecutar instalacion

Abrir PowerShell **como Administrador** y ejecutar:

```powershell
cd C:\kds-system\deploy-windows
Set-ExecutionPolicy -ExecutionPolicy Bypass -Scope Process
.\install.ps1
```

El script realizara:
- Verificar dependencias instaladas
- Crear estructura de directorios
- Copiar archivos del proyecto
- Instalar dependencias npm
- Compilar backend y frontends
- Configurar base de datos
- Ejecutar seed con datos iniciales

### Paso 4: Iniciar el sistema

**Opcion A: Inicio manual (para pruebas)**
```cmd
C:\kds-system\start-all.bat
```

**Opcion B: Instalar como servicios (recomendado para produccion)**
```powershell
.\install-services.ps1
```

---

## Configuracion como Servicios

Los servicios de Windows permiten que el sistema inicie automaticamente con Windows.

### Instalar servicios

```powershell
# Como Administrador
cd C:\kds-system\deploy-windows
.\install-services.ps1
```

Esto crea tres servicios:
- **KDS-Backend**: API REST (puerto 3000)
- **KDS-Frontend**: Pantallas de cocina (puerto 8080)
- **KDS-Backoffice**: Panel admin (puerto 8081)

### Gestionar servicios

```powershell
# Ver estado
Get-Service KDS-*

# Iniciar todos
.\start-services.ps1

# Detener todos
.\stop-services.ps1

# Reiniciar todos
.\restart-services.ps1

# Gestionar individualmente
Start-Service KDS-Backend
Stop-Service KDS-Backend
Restart-Service KDS-Backend
```

### Desinstalar servicios

```powershell
.\uninstall-services.ps1
```

---

## Validacion

### 1. Verificar servicios

```powershell
Get-Service KDS-* | Format-Table Name, Status, DisplayName
```

**Salida esperada:**
```
Name            Status DisplayName
----            ------ -----------
KDS-Backend    Running KDS System - Backend API
KDS-Backoffice Running KDS System - Backoffice (Admin)
KDS-Frontend   Running KDS System - Frontend (Pantallas)
```

### 2. Verificar puertos

```powershell
netstat -an | findstr ":3000 :8080 :8081"
```

**Salida esperada** (LISTENING en cada puerto):
```
TCP    0.0.0.0:3000    0.0.0.0:0    LISTENING
TCP    0.0.0.0:8080    0.0.0.0:0    LISTENING
TCP    0.0.0.0:8081    0.0.0.0:0    LISTENING
```

### 3. Verificar health del backend

```powershell
Invoke-RestMethod http://localhost:3000/api/config/health
```

**Salida esperada:**
```
status    : ok
timestamp : 2026-01-08T...
```

O usando curl:
```cmd
curl http://localhost:3000/api/config/health
```

### 4. Verificar conectividad de base de datos

```cmd
"C:\Program Files\PostgreSQL\15\bin\psql.exe" -U kds -d kds -c "SELECT COUNT(*) FROM users;"
```

### 5. Verificar Redis

```cmd
"C:\Program Files\Memurai\memurai-cli.exe" -a redis_secure_password_2025 ping
```

**Salida esperada:** `PONG`

### 6. Probar acceso web

Abrir en navegador:
- Frontend KDS: http://localhost:8080
- Backoffice: http://localhost:8081

### 7. Verificar logs

```powershell
# Ver ultimas lineas del log del backend
Get-Content C:\kds-system\logs\backend-stdout.log -Tail 50

# Monitorear en tiempo real
Get-Content C:\kds-system\logs\backend-stdout.log -Wait
```

---

## Acceso a los Servicios

| Servicio | URL | Descripcion |
|----------|-----|-------------|
| KDS Frontend | http://localhost:8080 | Pantallas de cocina |
| Backoffice | http://localhost:8081 | Panel de administracion |
| API Backend | http://localhost:3000/api | API REST |

### Credenciales por defecto

| Usuario | Password | Rol |
|---------|----------|-----|
| admin@kds.local | admin123 | Administrador |
| admin@kfc.com.ec | cx-dsi2025 | Administrador |

---

## Comandos Utiles

### Gestion de servicios

```powershell
# Ver estado de todos los servicios KDS
Get-Service KDS-*

# Iniciar/detener/reiniciar
Start-Service KDS-Backend
Stop-Service KDS-Backend
Restart-Service KDS-Backend

# Ver informacion detallada
Get-Service KDS-Backend | Select-Object *
```

### Base de datos

```cmd
# Acceder a PostgreSQL
"C:\Program Files\PostgreSQL\15\bin\psql.exe" -U kds -d kds

# Backup
"C:\Program Files\PostgreSQL\15\bin\pg_dump.exe" -U kds kds > C:\backups\kds_backup.sql

# Restaurar
"C:\Program Files\PostgreSQL\15\bin\psql.exe" -U kds -d kds < C:\backups\kds_backup.sql

# Re-ejecutar seed
cd C:\kds-system\backend
npx prisma db seed
```

### Logs

```powershell
# Ver logs del backend
Get-Content C:\kds-system\logs\backend-stdout.log -Tail 100

# Ver errores
Get-Content C:\kds-system\logs\backend-stderr.log -Tail 100

# Monitorear en tiempo real
Get-Content C:\kds-system\logs\backend-stdout.log -Wait
```

### Actualizacion

```powershell
# 1. Detener servicios
.\stop-services.ps1

# 2. Actualizar codigo
cd C:\kds-system
git pull origin main

# 3. Reinstalar
cd deploy-windows
.\install.ps1 -SkipDependencies

# 4. Iniciar servicios
.\start-services.ps1
```

---

## Solucion de Problemas

### El servicio no inicia

1. Verificar logs de error:
```powershell
Get-Content C:\kds-system\logs\backend-stderr.log -Tail 50
```

2. Verificar que PostgreSQL este corriendo:
```powershell
Get-Service postgresql*
```

3. Verificar que Redis/Memurai este corriendo:
```powershell
Get-Service Memurai
```

### Error de conexion a base de datos

1. Verificar credenciales en `C:\kds-system\backend\.env`
2. Probar conexion manual:
```cmd
"C:\Program Files\PostgreSQL\15\bin\psql.exe" -U kds -d kds
```

3. Verificar firewall de Windows:
```powershell
Get-NetFirewallRule | Where-Object {$_.DisplayName -like "*postgres*"}
```

### Puerto en uso

```powershell
# Ver que proceso usa el puerto
netstat -ano | findstr ":3000"
# El ultimo numero es el PID

# Ver detalles del proceso
Get-Process -Id <PID>

# Terminar proceso (si es necesario)
Stop-Process -Id <PID> -Force
```

### Permisos de PowerShell

```powershell
# Permitir ejecucion de scripts (como Administrador)
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope LocalMachine
```

### Resetear completamente

```powershell
# 1. Desinstalar servicios
.\uninstall-services.ps1

# 2. Eliminar directorio de instalacion
Remove-Item C:\kds-system -Recurse -Force

# 3. Eliminar base de datos
"C:\Program Files\PostgreSQL\15\bin\psql.exe" -U postgres -c "DROP DATABASE kds;"
"C:\Program Files\PostgreSQL\15\bin\psql.exe" -U postgres -c "DROP USER kds;"

# 4. Reinstalar desde cero
.\install.ps1
```

---

## Estructura de Archivos

Despues de la instalacion:

```
C:\kds-system\
├── backend\              # API compilada
│   ├── dist\             # Codigo compilado
│   ├── node_modules\     # Dependencias
│   ├── prisma\           # Schema de BD
│   └── .env              # Variables de entorno
├── kds-frontend\
│   └── dist\             # Frontend compilado
├── backoffice\
│   └── dist\             # Backoffice compilado
├── logs\                 # Logs de servicios
│   ├── backend-stdout.log
│   ├── backend-stderr.log
│   └── ...
├── tools\
│   └── nssm.exe          # Gestor de servicios
├── start-all.bat         # Iniciar manual
├── start-backend.ps1
├── start-frontend.ps1
└── start-backoffice.ps1
```

---

## Firewall

Si se accede desde otras maquinas, abrir los puertos necesarios:

```powershell
# Backend API
New-NetFirewallRule -DisplayName "KDS Backend" -Direction Inbound -Port 3000 -Protocol TCP -Action Allow

# Frontend
New-NetFirewallRule -DisplayName "KDS Frontend" -Direction Inbound -Port 8080 -Protocol TCP -Action Allow

# Backoffice
New-NetFirewallRule -DisplayName "KDS Backoffice" -Direction Inbound -Port 8081 -Protocol TCP -Action Allow
```

---

## Resumen de Comandos

| Accion | Comando |
|--------|---------|
| Instalar | `.\install.ps1` |
| Instalar servicios | `.\install-services.ps1` |
| Iniciar servicios | `.\start-services.ps1` |
| Detener servicios | `.\stop-services.ps1` |
| Reiniciar servicios | `.\restart-services.ps1` |
| Ver estado | `Get-Service KDS-*` |
| Ver logs | `Get-Content C:\kds-system\logs\backend-stdout.log -Tail 50` |
| Desinstalar servicios | `.\uninstall-services.ps1` |
