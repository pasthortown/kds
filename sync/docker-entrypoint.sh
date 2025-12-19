#!/bin/sh
set -e

# Crear symlink para compatibilidad de rutas Windows/Linux
# La app .NET busca 'config.txt' en el directorio actual
cd /app

# Si config.txt no existe en /app pero sí en /config, copiarlo
if [ -f "/config/config.txt" ] && [ ! -f "/app/config.txt" ]; then
    cp /config/config.txt /app/config.txt
fi

# Verificar que config.txt existe
if [ ! -f "/app/config.txt" ]; then
    echo "ERROR: config.txt no encontrado en /app/"
    exit 1
fi

echo "KDS Sync Service iniciando..."
echo "Archivo de configuracion: /app/config.txt"

# Ejecutar la aplicacion
exec dotnet KDS.dll "$@"
