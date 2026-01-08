# =============================================================================
# KDS System - Detener Servicios
# =============================================================================

Write-Host "Deteniendo servicios KDS..." -ForegroundColor Cyan

$services = @("KDS-Backoffice", "KDS-Frontend", "KDS-Backend")

foreach ($service in $services) {
    $svc = Get-Service $service -ErrorAction SilentlyContinue
    if ($svc) {
        if ($svc.Status -eq "Running") {
            Stop-Service $service -Force
            Write-Host "[OK] $service detenido" -ForegroundColor Green
        } else {
            Write-Host "[--] $service ya esta detenido" -ForegroundColor Gray
        }
    } else {
        Write-Host "[!!] $service no encontrado" -ForegroundColor Yellow
    }
}

Write-Host ""
Get-Service "KDS-*" -ErrorAction SilentlyContinue | Format-Table Name, Status -AutoSize
