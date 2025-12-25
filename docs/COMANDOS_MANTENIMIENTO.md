# Comandos de Mantenimiento KDS

## Limpiar Órdenes para Pruebas

Cuando necesites borrar todas las órdenes de la base de datos para hacer pruebas, es importante también limpiar la caché de Redis para evitar inconsistencias.

```bash
docker exec kds-postgres psql -U kds -d kds -c "DELETE FROM \"OrderItem\"; DELETE FROM \"Order\";" && docker exec kds-redis redis-cli -a redis_secure_password_2025 FLUSHDB
```

**Importante:** Si solo borras de PostgreSQL sin limpiar Redis, las nuevas órdenes pueden no asignarse correctamente a las pantallas.

## Verificar Estado de Órdenes

```bash
docker exec kds-postgres psql -U kds -d kds -c "SELECT id, \"externalId\", \"screenId\", identifier, status, \"statusPos\" FROM \"Order\";"
```

## Verificar Pantallas y Colas

```bash
# Ver colas
docker exec kds-postgres psql -U kds -d kds -c "SELECT id, name, distribution, active FROM \"Queue\";"

# Ver pantallas
docker exec kds-postgres psql -U kds -d kds -c "SELECT id, name, number, status, \"queueId\" FROM \"Screen\";"
```

## Ver Logs del Backend

```bash
docker logs kds-backend --tail 50
```

## Asignar Órdenes Huérfanas a Pantalla

Si hay órdenes sin `screenId` asignado, puedes asignarlas manualmente:

```bash
# Asignar a Pantalla1 (reemplazar el ID según corresponda)
docker exec kds-postgres psql -U kds -d kds -c "UPDATE \"Order\" SET \"screenId\" = 'cmjjil0kc001n1sk30cphmeyb' WHERE \"screenId\" IS NULL OR \"screenId\" = '';"
```

## Sincronizar Archivos con Servidor de Laboratorio

```bash
# Servidor: laboratorio@192.168.100.30
# Clave: jcjajplae*88
# Ruta base: /docker.files/_grupoKFC.Docker.MaxPointLegacy/maxpoint/

# Subir ajax_kds.js
sshpass -p 'jcjajplae*88' scp toMaxpoint/kds/ajax_kds.js laboratorio@192.168.100.30:/docker.files/_grupoKFC.Docker.MaxPointLegacy/maxpoint/kds/

# Subir ajax_facturacion.js
sshpass -p 'jcjajplae*88' scp toMaxpoint/js/ajax_facturacion.js laboratorio@192.168.100.30:/docker.files/_grupoKFC.Docker.MaxPointLegacy/maxpoint/js/

# Subir factura.php
sshpass -p 'jcjajplae*88' scp toMaxpoint/facturacion/factura.php laboratorio@192.168.100.30:/docker.files/_grupoKFC.Docker.MaxPointLegacy/maxpoint/facturacion/
```
