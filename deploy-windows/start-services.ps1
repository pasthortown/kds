# =============================================================================
# KDS System - Iniciar Servicios
# =============================================================================

Write-Host "Iniciando servicios KDS..." -ForegroundColor Cyan

$services = @("KDS-Backend", "KDS-Frontend", "KDS-Backoffice")

foreach ($service in $services) {
    $svc = Get-Service $service -ErrorAction SilentlyContinue
    if ($svc) {
        if ($svc.Status -ne "Running") {
            Start-Service $service
            Write-Host "[OK] $service iniciado" -ForegroundColor Green
        } else {
            Write-Host "[--] $service ya esta corriendo" -ForegroundColor Gray
        }
    } else {
        Write-Host "[!!] $service no encontrado (no instalado)" -ForegroundColor Yellow
    }
}

Write-Host ""
Get-Service "KDS-*" | Format-Table Name, Status -AutoSize
