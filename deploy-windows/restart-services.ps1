# =============================================================================
# KDS System - Reiniciar Servicios
# =============================================================================

Write-Host "Reiniciando servicios KDS..." -ForegroundColor Cyan

$services = @("KDS-Backend", "KDS-Frontend", "KDS-Backoffice")

foreach ($service in $services) {
    $svc = Get-Service $service -ErrorAction SilentlyContinue
    if ($svc) {
        Restart-Service $service -Force
        Write-Host "[OK] $service reiniciado" -ForegroundColor Green
    } else {
        Write-Host "[!!] $service no encontrado" -ForegroundColor Yellow
    }
}

Write-Host ""
Get-Service "KDS-*" | Format-Table Name, Status -AutoSize
