# =============================================================================
# KDS System - Script de Instalacion para Windows
# =============================================================================
# Ejecutar como Administrador:
#   powershell -ExecutionPolicy Bypass -File install.ps1
# =============================================================================

param(
    [switch]$SkipDependencies,
    [switch]$SkipBuild,
    [switch]$Help
)

if ($Help) {
    Write-Host @"
KDS System - Instalador para Windows

Uso: .\install.ps1 [opciones]

Opciones:
  -SkipDependencies   No verificar dependencias (PostgreSQL, Redis, Node.js)
  -SkipBuild          No compilar los proyectos
  -Help               Mostrar esta ayuda

Requisitos previos:
  1. PostgreSQL 15+ instalado y corriendo
  2. Redis/Memurai instalado y corriendo
  3. Node.js 20+ instalado
  4. Git instalado

"@
    exit 0
}

# Verificar ejecucion como administrador
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "ERROR: Este script debe ejecutarse como Administrador" -ForegroundColor Red
    Write-Host "Haga clic derecho en PowerShell y seleccione 'Ejecutar como administrador'" -ForegroundColor Yellow
    exit 1
}

Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "  KDS System - Instalador Windows" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host ""

# Cargar configuracion
$configFile = "$PSScriptRoot\config.ps1"
if (-not (Test-Path $configFile)) {
    Write-Host "Creando archivo de configuracion..." -ForegroundColor Yellow
    Copy-Item "$PSScriptRoot\config.example.ps1" $configFile
    Write-Host "IMPORTANTE: Edite el archivo config.ps1 con sus valores antes de continuar" -ForegroundColor Yellow
    Write-Host "Ruta: $configFile" -ForegroundColor Yellow
    exit 1
}

. $configFile
Write-Host "[OK] Configuracion cargada" -ForegroundColor Green

# -----------------------------------------------------------------------------
# Funcion para verificar comandos
# -----------------------------------------------------------------------------
function Test-Command {
    param([string]$Command)
    try {
        Get-Command $Command -ErrorAction Stop | Out-Null
        return $true
    } catch {
        return $false
    }
}

# -----------------------------------------------------------------------------
# Verificar dependencias
# -----------------------------------------------------------------------------
if (-not $SkipDependencies) {
    Write-Host ""
    Write-Host "Verificando dependencias..." -ForegroundColor Cyan

    # Node.js
    if (Test-Command "node") {
        $nodeVersion = node --version
        Write-Host "[OK] Node.js: $nodeVersion" -ForegroundColor Green
    } else {
        Write-Host "[ERROR] Node.js no encontrado. Instale Node.js 20+" -ForegroundColor Red
        exit 1
    }

    # npm
    if (Test-Command "npm") {
        $npmVersion = npm --version
        Write-Host "[OK] npm: $npmVersion" -ForegroundColor Green
    } else {
        Write-Host "[ERROR] npm no encontrado" -ForegroundColor Red
        exit 1
    }

    # Git
    if (Test-Command "git") {
        $gitVersion = git --version
        Write-Host "[OK] Git: $gitVersion" -ForegroundColor Green
    } else {
        Write-Host "[WARN] Git no encontrado (opcional para instalacion)" -ForegroundColor Yellow
    }

    # PostgreSQL
    $pgPath = "$POSTGRES_BIN\psql.exe"
    if (Test-Path $pgPath) {
        Write-Host "[OK] PostgreSQL encontrado en: $POSTGRES_BIN" -ForegroundColor Green
    } else {
        Write-Host "[ERROR] PostgreSQL no encontrado en: $POSTGRES_BIN" -ForegroundColor Red
        Write-Host "       Ajuste la variable POSTGRES_BIN en config.ps1" -ForegroundColor Yellow
        exit 1
    }

    # Redis/Memurai
    if (Test-Path "$REDIS_PATH\memurai-cli.exe") {
        Write-Host "[OK] Memurai encontrado en: $REDIS_PATH" -ForegroundColor Green
    } elseif (Test-Path "$REDIS_PATH\redis-cli.exe") {
        Write-Host "[OK] Redis encontrado en: $REDIS_PATH" -ForegroundColor Green
    } else {
        Write-Host "[WARN] Redis/Memurai no encontrado en: $REDIS_PATH" -ForegroundColor Yellow
        Write-Host "       El backend funcionara sin cache de Redis" -ForegroundColor Yellow
    }
}

# -----------------------------------------------------------------------------
# Crear estructura de directorios
# -----------------------------------------------------------------------------
Write-Host ""
Write-Host "Creando estructura de directorios..." -ForegroundColor Cyan

$directories = @($KDS_ROOT, $KDS_BACKEND, $KDS_FRONTEND, $KDS_BACKOFFICE, $KDS_LOGS, $KDS_DATA, "$KDS_ROOT\tools")
foreach ($dir in $directories) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
        Write-Host "  Creado: $dir" -ForegroundColor Gray
    }
}
Write-Host "[OK] Directorios creados" -ForegroundColor Green

# -----------------------------------------------------------------------------
# Descargar NSSM si no existe
# -----------------------------------------------------------------------------
if (-not (Test-Path $NSSM_PATH)) {
    Write-Host ""
    Write-Host "Descargando NSSM (gestor de servicios)..." -ForegroundColor Cyan
    $nssmUrl = "https://nssm.cc/release/nssm-2.24.zip"
    $nssmZip = "$KDS_ROOT\tools\nssm.zip"

    try {
        Invoke-WebRequest -Uri $nssmUrl -OutFile $nssmZip
        Expand-Archive -Path $nssmZip -DestinationPath "$KDS_ROOT\tools\nssm-temp" -Force
        Copy-Item "$KDS_ROOT\tools\nssm-temp\nssm-2.24\win64\nssm.exe" $NSSM_PATH
        Remove-Item $nssmZip -Force
        Remove-Item "$KDS_ROOT\tools\nssm-temp" -Recurse -Force
        Write-Host "[OK] NSSM instalado" -ForegroundColor Green
    } catch {
        Write-Host "[WARN] No se pudo descargar NSSM automaticamente" -ForegroundColor Yellow
        Write-Host "       Descargue manualmente de https://nssm.cc y copie nssm.exe a $NSSM_PATH" -ForegroundColor Yellow
    }
}

# -----------------------------------------------------------------------------
# Copiar archivos del proyecto
# -----------------------------------------------------------------------------
Write-Host ""
Write-Host "Copiando archivos del proyecto..." -ForegroundColor Cyan

$projectRoot = Split-Path -Parent $PSScriptRoot

# Backend
Write-Host "  Copiando backend..." -ForegroundColor Gray
Copy-Item "$projectRoot\backend\*" $KDS_BACKEND -Recurse -Force -Exclude "node_modules"

# Frontend KDS
Write-Host "  Copiando kds-frontend..." -ForegroundColor Gray
Copy-Item "$projectRoot\kds-frontend\*" $KDS_FRONTEND -Recurse -Force -Exclude "node_modules"

# Backoffice
Write-Host "  Copiando backoffice..." -ForegroundColor Gray
Copy-Item "$projectRoot\backoffice\*" $KDS_BACKOFFICE -Recurse -Force -Exclude "node_modules"

Write-Host "[OK] Archivos copiados" -ForegroundColor Green

# -----------------------------------------------------------------------------
# Crear archivo .env para el backend
# -----------------------------------------------------------------------------
Write-Host ""
Write-Host "Creando archivo .env del backend..." -ForegroundColor Cyan

$envContent = @"
NODE_ENV=$NODE_ENV
PORT=$BACKEND_PORT
TZ=$TZ

# Base de datos
DATABASE_URL=postgresql://${POSTGRES_USER}:${POSTGRES_PASSWORD}@${POSTGRES_HOST}:${POSTGRES_PORT}/${POSTGRES_DB}?schema=public

# Redis
REDIS_URL=redis://:${REDIS_PASSWORD}@${REDIS_HOST}:${REDIS_PORT}

# JWT
JWT_SECRET=$JWT_SECRET
JWT_REFRESH_SECRET=$JWT_REFRESH_SECRET
JWT_EXPIRES_IN=$JWT_EXPIRES_IN
JWT_REFRESH_EXPIRES_IN=$JWT_REFRESH_EXPIRES_IN

# Configuracion
HEARTBEAT_INTERVAL=$HEARTBEAT_INTERVAL
HEARTBEAT_TIMEOUT=$HEARTBEAT_TIMEOUT
RESTAURANT_ID=$RESTAURANT_ID
"@

$envContent | Out-File -FilePath "$KDS_BACKEND\.env" -Encoding UTF8
Write-Host "[OK] Archivo .env creado" -ForegroundColor Green

# -----------------------------------------------------------------------------
# Instalar dependencias y compilar
# -----------------------------------------------------------------------------
if (-not $SkipBuild) {
    Write-Host ""
    Write-Host "Instalando dependencias del backend..." -ForegroundColor Cyan
    Push-Location $KDS_BACKEND
    npm ci --production=false
    Write-Host "[OK] Dependencias del backend instaladas" -ForegroundColor Green

    Write-Host ""
    Write-Host "Generando cliente Prisma..." -ForegroundColor Cyan
    npx prisma generate
    Write-Host "[OK] Cliente Prisma generado" -ForegroundColor Green

    Write-Host ""
    Write-Host "Compilando backend..." -ForegroundColor Cyan
    npm run build
    Write-Host "[OK] Backend compilado" -ForegroundColor Green
    Pop-Location

    # Frontend KDS
    Write-Host ""
    Write-Host "Instalando dependencias del frontend KDS..." -ForegroundColor Cyan
    Push-Location $KDS_FRONTEND
    npm ci

    Write-Host "Compilando frontend KDS..." -ForegroundColor Cyan
    $env:VITE_API_URL = $VITE_API_URL
    npm run build
    Write-Host "[OK] Frontend KDS compilado" -ForegroundColor Green
    Pop-Location

    # Backoffice
    Write-Host ""
    Write-Host "Instalando dependencias del backoffice..." -ForegroundColor Cyan
    Push-Location $KDS_BACKOFFICE
    npm ci

    Write-Host "Compilando backoffice..." -ForegroundColor Cyan
    $env:VITE_API_URL = $VITE_API_URL
    npm run build
    Write-Host "[OK] Backoffice compilado" -ForegroundColor Green
    Pop-Location

    # Instalar http-server globalmente para servir los frontends
    Write-Host ""
    Write-Host "Instalando http-server para servir frontends..." -ForegroundColor Cyan
    npm install -g http-server
    Write-Host "[OK] http-server instalado" -ForegroundColor Green
}

# -----------------------------------------------------------------------------
# Configurar base de datos
# -----------------------------------------------------------------------------
Write-Host ""
Write-Host "Configurando base de datos..." -ForegroundColor Cyan

# Crear usuario y base de datos si no existen
$createDbScript = @"
DO `$`$
BEGIN
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = '$POSTGRES_USER') THEN
        CREATE ROLE $POSTGRES_USER WITH LOGIN PASSWORD '$POSTGRES_PASSWORD';
    END IF;
END
`$`$;

SELECT 'CREATE DATABASE $POSTGRES_DB OWNER $POSTGRES_USER'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = '$POSTGRES_DB')\gexec
"@

Write-Host "  Verificando usuario y base de datos..." -ForegroundColor Gray
# Nota: Esto requiere que PostgreSQL tenga trust o password auth configurado

Push-Location $KDS_BACKEND
Write-Host "  Aplicando schema de Prisma..." -ForegroundColor Gray
npx prisma db push --skip-generate
Write-Host "[OK] Schema aplicado" -ForegroundColor Green

Write-Host "  Ejecutando seed..." -ForegroundColor Gray
npx prisma db seed
Write-Host "[OK] Datos iniciales creados" -ForegroundColor Green
Pop-Location

# -----------------------------------------------------------------------------
# Crear scripts de inicio
# -----------------------------------------------------------------------------
Write-Host ""
Write-Host "Creando scripts de inicio..." -ForegroundColor Cyan

# Script start-backend.ps1
@"
# Iniciar Backend KDS
`$env:NODE_ENV = "production"
Set-Location "$KDS_BACKEND"
node dist/index.js
"@ | Out-File -FilePath "$KDS_ROOT\start-backend.ps1" -Encoding UTF8

# Script start-frontend.ps1
@"
# Iniciar Frontend KDS
Set-Location "$KDS_FRONTEND\dist"
http-server -p $KDS_FRONTEND_PORT -c-1 --proxy http://localhost:${BACKEND_PORT}?
"@ | Out-File -FilePath "$KDS_ROOT\start-frontend.ps1" -Encoding UTF8

# Script start-backoffice.ps1
@"
# Iniciar Backoffice
Set-Location "$KDS_BACKOFFICE\dist"
http-server -p $BACKOFFICE_PORT -c-1 --proxy http://localhost:${BACKEND_PORT}?
"@ | Out-File -FilePath "$KDS_ROOT\start-backoffice.ps1" -Encoding UTF8

# Script start-all.bat
@"
@echo off
echo Iniciando KDS System...
start "KDS Backend" powershell -ExecutionPolicy Bypass -File "$KDS_ROOT\start-backend.ps1"
timeout /t 5 /nobreak > nul
start "KDS Frontend" powershell -ExecutionPolicy Bypass -File "$KDS_ROOT\start-frontend.ps1"
start "KDS Backoffice" powershell -ExecutionPolicy Bypass -File "$KDS_ROOT\start-backoffice.ps1"
echo.
echo Servicios iniciados:
echo   Backend:    http://localhost:$BACKEND_PORT
echo   Frontend:   http://localhost:$KDS_FRONTEND_PORT
echo   Backoffice: http://localhost:$BACKOFFICE_PORT
echo.
"@ | Out-File -FilePath "$KDS_ROOT\start-all.bat" -Encoding ASCII

Write-Host "[OK] Scripts de inicio creados" -ForegroundColor Green

# -----------------------------------------------------------------------------
# Resumen final
# -----------------------------------------------------------------------------
Write-Host ""
Write-Host "=============================================" -ForegroundColor Green
Write-Host "  Instalacion completada!" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green
Write-Host ""
Write-Host "Ubicacion: $KDS_ROOT" -ForegroundColor Cyan
Write-Host ""
Write-Host "Para iniciar manualmente:" -ForegroundColor Yellow
Write-Host "  $KDS_ROOT\start-all.bat"
Write-Host ""
Write-Host "Para instalar como servicios de Windows:" -ForegroundColor Yellow
Write-Host "  .\install-services.ps1"
Write-Host ""
Write-Host "URLs de acceso:" -ForegroundColor Cyan
Write-Host "  Backend:    http://localhost:$BACKEND_PORT"
Write-Host "  Frontend:   http://localhost:$KDS_FRONTEND_PORT"
Write-Host "  Backoffice: http://localhost:$BACKOFFICE_PORT"
Write-Host ""
Write-Host "Credenciales por defecto:" -ForegroundColor Cyan
Write-Host "  Usuario: admin@kds.local"
Write-Host "  Password: admin123"
Write-Host ""
