#!/bin/sh
set -e

echo "=== KDS Backend Startup ==="
echo "Waiting for database to be ready..."

# Esperar a que la base de datos esté lista
max_attempts=30
attempt=0
until npx prisma db push --skip-generate 2>/dev/null; do
  attempt=$((attempt + 1))
  if [ $attempt -ge $max_attempts ]; then
    echo "ERROR: Database not ready after $max_attempts attempts"
    exit 1
  fi
  echo "Database not ready, waiting... (attempt $attempt/$max_attempts)"
  sleep 2
done

echo "Database schema synchronized!"

# Ejecutar seed
echo "Running database seed..."
npx prisma db seed || echo "Seed already executed or failed (continuing...)"

echo "Starting application..."
exec node dist/index.js
