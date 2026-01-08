# =============================================================================
# KDS System - Configuracion de Despliegue Windows
# =============================================================================
# Copiar este archivo como config.ps1 y ajustar los valores
# =============================================================================

# -----------------------------------------------------------------------------
# Rutas de instalacion
# -----------------------------------------------------------------------------
$KDS_ROOT = "C:\kds-system"
$KDS_BACKEND = "$KDS_ROOT\backend"
$KDS_FRONTEND = "$KDS_ROOT\kds-frontend"
$KDS_BACKOFFICE = "$KDS_ROOT\backoffice"
$KDS_LOGS = "$KDS_ROOT\logs"
$KDS_DATA = "$KDS_ROOT\data"

# -----------------------------------------------------------------------------
# PostgreSQL
# -----------------------------------------------------------------------------
$POSTGRES_HOST = "localhost"
$POSTGRES_PORT = 5432
$POSTGRES_USER = "kds"
$POSTGRES_PASSWORD = "kds_secure_password_2025"
$POSTGRES_DB = "kds"

# Ruta de instalacion de PostgreSQL (ajustar segun version instalada)
$POSTGRES_BIN = "C:\Program Files\PostgreSQL\15\bin"

# -----------------------------------------------------------------------------
# Redis
# -----------------------------------------------------------------------------
$REDIS_HOST = "localhost"
$REDIS_PORT = 6379
$REDIS_PASSWORD = "redis_secure_password_2025"

# Ruta de instalacion de Redis/Memurai
$REDIS_PATH = "C:\Program Files\Memurai"

# -----------------------------------------------------------------------------
# Backend Node.js
# -----------------------------------------------------------------------------
$NODE_ENV = "production"
$BACKEND_PORT = 3000
$JWT_SECRET = "kds_jwt_secret_key_very_secure_2025_production"
$JWT_REFRESH_SECRET = "kds_jwt_refresh_secret_key_very_secure_2025"
$JWT_EXPIRES_IN = "1h"
$JWT_REFRESH_EXPIRES_IN = "7d"

# -----------------------------------------------------------------------------
# Frontends
# -----------------------------------------------------------------------------
$KDS_FRONTEND_PORT = 8080
$BACKOFFICE_PORT = 8081
$VITE_API_URL = "/api"

# -----------------------------------------------------------------------------
# Configuracion adicional
# -----------------------------------------------------------------------------
$RESTAURANT_ID = "K027"
$TZ = "America/Bogota"
$HEARTBEAT_INTERVAL = 10000
$HEARTBEAT_TIMEOUT = 30000

# -----------------------------------------------------------------------------
# NSSM (Non-Sucking Service Manager) para servicios Windows
# -----------------------------------------------------------------------------
$NSSM_PATH = "$KDS_ROOT\tools\nssm.exe"
