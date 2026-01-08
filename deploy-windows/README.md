# Deploy Windows (Sin Docker)

Scripts para desplegar KDS System en Windows sin Docker.

## Archivos

| Archivo | Descripcion |
|---------|-------------|
| `config.example.ps1` | Plantilla de configuracion |
| `config.ps1` | Configuracion activa (crear desde example) |
| `install.ps1` | Script principal de instalacion |
| `install-services.ps1` | Instalar como servicios de Windows |
| `uninstall-services.ps1` | Desinstalar servicios |
| `start-services.ps1` | Iniciar todos los servicios |
| `stop-services.ps1` | Detener todos los servicios |
| `restart-services.ps1` | Reiniciar todos los servicios |

## Uso Rapido

```powershell
# 1. Copiar configuracion
Copy-Item config.example.ps1 config.ps1

# 2. Editar config.ps1 con tus valores

# 3. Ejecutar instalacion (como Administrador)
.\install.ps1

# 4. Instalar como servicios (opcional)
.\install-services.ps1
```

## Guia Completa

Consulta la documentacion en: [docs/DEPLOY_WINDOWS.md](../docs/DEPLOY_WINDOWS.md)
