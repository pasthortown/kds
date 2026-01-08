# =============================================================================
# KDS System - Desinstalar Servicios de Windows
# =============================================================================

# Verificar ejecucion como administrador
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "ERROR: Este script debe ejecutarse como Administrador" -ForegroundColor Red
    exit 1
}

# Cargar configuracion
$configFile = "$PSScriptRoot\config.ps1"
if (Test-Path $configFile) {
    . $configFile
}

Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "  KDS System - Desinstalar Servicios" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host ""

$services = @("KDS-Backend", "KDS-Frontend", "KDS-Backoffice")

foreach ($service in $services) {
    Write-Host "Deteniendo y removiendo: $service..." -ForegroundColor Yellow

    # Detener servicio
    Stop-Service $service -ErrorAction SilentlyContinue -Force

    # Remover con NSSM si existe
    if (Test-Path $NSSM_PATH) {
        & $NSSM_PATH remove $service confirm 2>$null
    } else {
        # Usar sc.exe si NSSM no esta disponible
        sc.exe delete $service 2>$null
    }

    Write-Host "[OK] $service removido" -ForegroundColor Green
}

Write-Host ""
Write-Host "=============================================" -ForegroundColor Green
Write-Host "  Todos los servicios han sido removidos" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green
