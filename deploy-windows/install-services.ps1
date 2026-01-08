# =============================================================================
# KDS System - Instalar Servicios de Windows
# =============================================================================
# Ejecutar como Administrador
# =============================================================================

# Verificar ejecucion como administrador
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "ERROR: Este script debe ejecutarse como Administrador" -ForegroundColor Red
    exit 1
}

# Cargar configuracion
$configFile = "$PSScriptRoot\config.ps1"
if (-not (Test-Path $configFile)) {
    Write-Host "ERROR: No se encontro config.ps1. Ejecute install.ps1 primero." -ForegroundColor Red
    exit 1
}
. $configFile

Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "  KDS System - Instalador de Servicios" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host ""

# Verificar NSSM
if (-not (Test-Path $NSSM_PATH)) {
    Write-Host "ERROR: NSSM no encontrado en $NSSM_PATH" -ForegroundColor Red
    Write-Host "Descargue NSSM de https://nssm.cc y copie nssm.exe a esa ubicacion" -ForegroundColor Yellow
    exit 1
}

# -----------------------------------------------------------------------------
# Servicio: KDS Backend
# -----------------------------------------------------------------------------
Write-Host "Instalando servicio: KDS-Backend..." -ForegroundColor Cyan

$serviceName = "KDS-Backend"
$nodePath = (Get-Command node).Source
$backendScript = "$KDS_BACKEND\dist\index.js"

# Remover si existe
& $NSSM_PATH stop $serviceName 2>$null
& $NSSM_PATH remove $serviceName confirm 2>$null

# Instalar servicio
& $NSSM_PATH install $serviceName $nodePath $backendScript
& $NSSM_PATH set $serviceName AppDirectory $KDS_BACKEND
& $NSSM_PATH set $serviceName DisplayName "KDS System - Backend API"
& $NSSM_PATH set $serviceName Description "API REST del sistema KDS Kitchen Display System"
& $NSSM_PATH set $serviceName Start SERVICE_AUTO_START
& $NSSM_PATH set $serviceName AppStdout "$KDS_LOGS\backend-stdout.log"
& $NSSM_PATH set $serviceName AppStderr "$KDS_LOGS\backend-stderr.log"
& $NSSM_PATH set $serviceName AppRotateFiles 1
& $NSSM_PATH set $serviceName AppRotateBytes 10485760

# Variables de entorno
& $NSSM_PATH set $serviceName AppEnvironmentExtra "NODE_ENV=production" "PORT=$BACKEND_PORT"

Write-Host "[OK] Servicio KDS-Backend instalado" -ForegroundColor Green

# -----------------------------------------------------------------------------
# Servicio: KDS Frontend
# -----------------------------------------------------------------------------
Write-Host "Instalando servicio: KDS-Frontend..." -ForegroundColor Cyan

$serviceName = "KDS-Frontend"
$httpServerPath = (Get-Command http-server -ErrorAction SilentlyContinue).Source

if ($httpServerPath) {
    & $NSSM_PATH stop $serviceName 2>$null
    & $NSSM_PATH remove $serviceName confirm 2>$null

    & $NSSM_PATH install $serviceName $httpServerPath "$KDS_FRONTEND\dist" -p $KDS_FRONTEND_PORT -c-1
    & $NSSM_PATH set $serviceName DisplayName "KDS System - Frontend (Pantallas)"
    & $NSSM_PATH set $serviceName Description "Interfaz de pantallas de cocina del sistema KDS"
    & $NSSM_PATH set $serviceName Start SERVICE_AUTO_START
    & $NSSM_PATH set $serviceName AppStdout "$KDS_LOGS\frontend-stdout.log"
    & $NSSM_PATH set $serviceName AppStderr "$KDS_LOGS\frontend-stderr.log"

    Write-Host "[OK] Servicio KDS-Frontend instalado" -ForegroundColor Green
} else {
    Write-Host "[WARN] http-server no encontrado. Instale con: npm install -g http-server" -ForegroundColor Yellow
}

# -----------------------------------------------------------------------------
# Servicio: KDS Backoffice
# -----------------------------------------------------------------------------
Write-Host "Instalando servicio: KDS-Backoffice..." -ForegroundColor Cyan

$serviceName = "KDS-Backoffice"

if ($httpServerPath) {
    & $NSSM_PATH stop $serviceName 2>$null
    & $NSSM_PATH remove $serviceName confirm 2>$null

    & $NSSM_PATH install $serviceName $httpServerPath "$KDS_BACKOFFICE\dist" -p $BACKOFFICE_PORT -c-1
    & $NSSM_PATH set $serviceName DisplayName "KDS System - Backoffice (Admin)"
    & $NSSM_PATH set $serviceName Description "Panel de administracion del sistema KDS"
    & $NSSM_PATH set $serviceName Start SERVICE_AUTO_START
    & $NSSM_PATH set $serviceName AppStdout "$KDS_LOGS\backoffice-stdout.log"
    & $NSSM_PATH set $serviceName AppStderr "$KDS_LOGS\backoffice-stderr.log"

    Write-Host "[OK] Servicio KDS-Backoffice instalado" -ForegroundColor Green
} else {
    Write-Host "[WARN] http-server no encontrado" -ForegroundColor Yellow
}

# -----------------------------------------------------------------------------
# Iniciar servicios
# -----------------------------------------------------------------------------
Write-Host ""
Write-Host "Iniciando servicios..." -ForegroundColor Cyan

Start-Service "KDS-Backend" -ErrorAction SilentlyContinue
Start-Sleep -Seconds 3
Start-Service "KDS-Frontend" -ErrorAction SilentlyContinue
Start-Service "KDS-Backoffice" -ErrorAction SilentlyContinue

Write-Host ""
Write-Host "=============================================" -ForegroundColor Green
Write-Host "  Servicios instalados e iniciados" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green
Write-Host ""

# Mostrar estado
Get-Service "KDS-*" | Format-Table Name, Status, DisplayName -AutoSize

Write-Host ""
Write-Host "Comandos utiles:" -ForegroundColor Yellow
Write-Host "  Start-Service KDS-Backend      # Iniciar backend"
Write-Host "  Stop-Service KDS-Backend       # Detener backend"
Write-Host "  Restart-Service KDS-Backend    # Reiniciar backend"
Write-Host "  Get-Service KDS-*              # Ver estado de todos"
Write-Host ""
Write-Host "Logs en: $KDS_LOGS" -ForegroundColor Cyan
