# Requerimientos del Sistema KDS v2.0

## Especificaciones de Hardware y Software

---

## 1. Requerimientos del Servidor

### 1.1 Requerimientos Mínimos

| Componente | Especificación |
|------------|----------------|
| **CPU** | 2 cores / 2 vCPUs |
| **RAM** | 4 GB |
| **Almacenamiento** | 20 GB SSD |
| **Sistema Operativo** | Ubuntu 20.04 LTS / Windows Server 2019 |
| **Red** | 100 Mbps |

**Capacidad aproximada**: 2-4 pantallas, ~200 órdenes/día

### 1.2 Requerimientos Recomendados

| Componente | Especificación |
|------------|----------------|
| **CPU** | 4 cores / 4 vCPUs |
| **RAM** | 8 GB |
| **Almacenamiento** | 50 GB SSD |
| **Sistema Operativo** | Ubuntu 22.04 LTS / Windows Server 2022 |
| **Red** | 1 Gbps |

**Capacidad aproximada**: 10+ pantallas, ~1000 órdenes/día

### 1.3 Requerimientos para Alta Disponibilidad

| Componente | Especificación |
|------------|----------------|
| **CPU** | 8+ cores |
| **RAM** | 16 GB |
| **Almacenamiento** | 100 GB SSD (NVMe preferido) |
| **Base de Datos** | PostgreSQL en servidor dedicado |
| **Cache** | Redis Cluster |
| **Load Balancer** | Nginx / HAProxy |

---

## 2. Requerimientos de Software del Servidor

### 2.1 Opción A: Docker (Recomendada)

| Software | Versión Mínima | Versión Recomendada |
|----------|----------------|---------------------|
| Docker Engine | 20.10 | 24.x |
| Docker Compose | 2.0 | 2.24+ |
| Sistema Operativo | Linux x64 / Windows con WSL2 | Ubuntu 22.04 LTS |

### 2.2 Opción B: Instalación Nativa

| Software | Versión Mínima | Versión Recomendada |
|----------|----------------|---------------------|
| Node.js | 18.x LTS | 20.x LTS |
| npm | 9.x | 10.x |
| PostgreSQL | 14 | 15+ |
| Redis | 6 | 7+ |
| Nginx | 1.18 | 1.24+ |

---

## 3. Requerimientos de Red

### 3.1 Puertos Requeridos

| Puerto | Protocolo | Servicio | Dirección |
|--------|-----------|----------|-----------|
| 3000 | TCP | Backend API | Inbound |
| 5432 | TCP | PostgreSQL | Internal |
| 6379 | TCP | Redis | Internal |
| 8080 | TCP | KDS Frontend | Inbound |
| 8081 | TCP | Backoffice | Inbound |
| 80/443 | TCP | HTTP/HTTPS (producción) | Inbound |

### 3.2 Conectividad MAXPOINT

| Puerto | Protocolo | Servicio | Dirección |
|--------|-----------|----------|-----------|
| 1433 | TCP | SQL Server MAXPOINT | Outbound |

### 3.3 Ancho de Banda

| Escenario | Mínimo | Recomendado |
|-----------|--------|-------------|
| 1-2 pantallas | 10 Mbps | 50 Mbps |
| 3-5 pantallas | 25 Mbps | 100 Mbps |
| 6+ pantallas | 50 Mbps | 1 Gbps |

### 3.4 Latencia

| Conexión | Máxima | Óptima |
|----------|--------|--------|
| Servidor ↔ Pantallas | 100ms | <20ms |
| Servidor ↔ MAXPOINT | 200ms | <50ms |
| Servidor ↔ Internet | 500ms | <100ms |

---

## 4. Requerimientos de las Pantallas KDS

### 4.1 Hardware de Pantalla (Mínimo)

| Componente | Especificación |
|------------|----------------|
| **Tipo** | Monitor industrial / TV Smart |
| **Resolución** | 1366x768 (HD) |
| **Tamaño** | 22" mínimo |
| **Conectividad** | Ethernet o WiFi 5GHz |
| **Dispositivo** | Android TV Box / Mini PC / Raspberry Pi 4 |

### 4.2 Hardware de Pantalla (Recomendado)

| Componente | Especificación |
|------------|----------------|
| **Tipo** | Monitor industrial táctil |
| **Resolución** | 1920x1080 (Full HD) |
| **Tamaño** | 27" - 32" |
| **Brillo** | 350+ cd/m² (para ambientes iluminados) |
| **Conectividad** | Ethernet (cableado) |
| **Dispositivo** | Mini PC Windows/Linux |

### 4.3 Dispositivos de Visualización Compatibles

| Dispositivo | RAM Mínima | Notas |
|-------------|------------|-------|
| **Mini PC (Windows/Linux)** | 4 GB | Mejor rendimiento |
| **Raspberry Pi 4** | 4 GB | Económico, buen rendimiento |
| **Android TV Box** | 2 GB | Requiere navegador Chromium |
| **Smart TV** | - | Solo si tiene navegador moderno |
| **Tablet Android/iPad** | 3 GB | Para estaciones móviles |

### 4.4 Navegadores Compatibles

| Navegador | Versión Mínima | Recomendado |
|-----------|----------------|-------------|
| Google Chrome | 90+ | 120+ |
| Microsoft Edge | 90+ | 120+ |
| Firefox | 90+ | 120+ |
| Safari | 14+ | 17+ |
| Chromium (Linux) | 90+ | Latest |

**No soportados**: Internet Explorer, navegadores antiguos sin ES2020

---

## 5. Requerimientos de la Botonera Física

### 5.1 Teclado USB Estándar

El sistema soporta cualquier teclado USB estándar. Las teclas por defecto son:

| Tecla | Acción |
|-------|--------|
| H | Finalizar orden 1 |
| 3 | Finalizar orden 2 |
| 1 | Finalizar orden 3 |
| F | Finalizar orden 4 |
| J | Finalizar orden 5 |
| I | Siguiente página |
| G | Página anterior |
| C | Deshacer última acción |
| I+G (3 seg) | Activar/desactivar standby |

### 5.2 Botonera Industrial (Opcional)

| Característica | Especificación |
|----------------|----------------|
| **Conexión** | USB HID |
| **Teclas** | 5-12 botones programables |
| **Protección** | IP54+ (resistente a salpicaduras) |
| **Marcas compatibles** | X-Keys, Genovation, PI Engineering |

---

## 6. Requerimientos de Impresoras

### 6.1 Impresoras Térmicas (Recomendadas)

| Característica | Especificación |
|----------------|----------------|
| **Tipo** | Impresora térmica de tickets |
| **Ancho papel** | 80mm (preferido) o 58mm |
| **Interfaz** | Ethernet (TCP/IP) o USB |
| **Puerto TCP** | 9100 (estándar RAW) |
| **Velocidad** | 200+ mm/s |

### 6.2 Marcas Compatibles

- Epson TM-T88 series
- Star Micronics TSP100/TSP600
- Bixolon SRP-350
- Citizen CT-S310
- POS-X EVO

### 6.3 Configuración de Red

```
┌─────────────┐      ┌─────────────┐
│  Servidor   │      │  Impresora  │
│    KDS      │─────▶│   Térmica   │
│             │ TCP  │  Puerto     │
│             │ 9100 │   9100      │
└─────────────┘      └─────────────┘
```

---

## 7. Requerimientos de Base de Datos

### 7.1 PostgreSQL

| Parámetro | Mínimo | Recomendado |
|-----------|--------|-------------|
| **Versión** | 14 | 15+ |
| **Almacenamiento** | 10 GB | 50 GB |
| **Conexiones máximas** | 50 | 100 |
| **shared_buffers** | 256 MB | 1 GB |
| **work_mem** | 64 MB | 256 MB |

### 7.2 Redis

| Parámetro | Mínimo | Recomendado |
|-----------|--------|-------------|
| **Versión** | 6 | 7+ |
| **Memoria** | 128 MB | 256 MB |
| **maxmemory-policy** | allkeys-lru | allkeys-lru |
| **Persistencia** | AOF | AOF + RDB |

---

## 8. Requerimientos de Integración MAXPOINT

### 8.1 SQL Server

| Parámetro | Requisito |
|-----------|-----------|
| **Versión** | SQL Server 2014+ |
| **Puerto** | 1433 (TCP) |
| **Autenticación** | SQL Server Auth |
| **Permisos** | SELECT en tablas de tickets |
| **Conectividad** | Red accesible desde servidor KDS |

### 8.2 Datos Requeridos de MAXPOINT

| Campo | Descripción | Obligatorio |
|-------|-------------|-------------|
| ID Ticket | Identificador único | Sí |
| Número Orden | Número visible al cliente | Sí |
| Canal | local, kiosko, delivery, etc. | Sí |
| Productos | Lista de items | Sí |
| Cliente | Nombre (opcional) | No |
| Fecha/Hora | Timestamp de creación | Sí |

---

## 9. Requerimientos de Seguridad

### 9.1 Certificados SSL/TLS

| Ambiente | Requisito |
|----------|-----------|
| **Desarrollo** | Opcional (HTTP) |
| **Producción** | Obligatorio (HTTPS) |
| **Tipo** | Let's Encrypt o comercial |

### 9.2 Firewall

| Regla | Origen | Destino | Puerto |
|-------|--------|---------|--------|
| Allow | Pantallas KDS | Servidor | 8080, 3000 |
| Allow | Admin | Servidor | 8081 |
| Allow | Servidor | MAXPOINT | 1433 |
| Deny | Internet | PostgreSQL | 5432 |
| Deny | Internet | Redis | 6379 |

### 9.3 Credenciales

| Servicio | Requisito |
|----------|-----------|
| JWT Secret | Mínimo 32 caracteres aleatorios |
| PostgreSQL Password | Mínimo 16 caracteres |
| Redis Password | Mínimo 16 caracteres |

---

## 10. Requerimientos de Backup

### 10.1 Base de Datos

| Tipo | Frecuencia | Retención |
|------|------------|-----------|
| Completo | Diario | 30 días |
| Incremental | Cada 6 horas | 7 días |
| Logs transaccionales | Cada hora | 24 horas |

### 10.2 Almacenamiento

| Datos | Tamaño aproximado/mes |
|-------|----------------------|
| PostgreSQL | 500 MB - 2 GB |
| Logs aplicación | 100 MB - 500 MB |
| Redis snapshots | 10 MB - 50 MB |

---

## 11. Matriz de Compatibilidad

### 11.1 Sistemas Operativos del Servidor

| Sistema Operativo | Soporte |
|-------------------|---------|
| Ubuntu 20.04 LTS | ✅ Completo |
| Ubuntu 22.04 LTS | ✅ Completo (Recomendado) |
| Debian 11/12 | ✅ Completo |
| CentOS 8/9 | ✅ Completo |
| Windows Server 2019 | ✅ Completo |
| Windows Server 2022 | ✅ Completo |
| macOS (desarrollo) | ⚠️ Solo desarrollo |

### 11.2 Navegadores de Pantallas

| Navegador | Versión | Soporte |
|-----------|---------|---------|
| Chrome | 90+ | ✅ Completo |
| Edge | 90+ | ✅ Completo |
| Firefox | 90+ | ✅ Completo |
| Safari | 14+ | ⚠️ Parcial (WebSocket) |
| Opera | 76+ | ✅ Completo |

---

## 12. Resumen de Recursos por Escenario

### Escenario 1: Restaurante Pequeño (1-2 pantallas)

```
┌────────────────────────────────────┐
│  SERVIDOR                          │
│  • 2 vCPU, 4 GB RAM               │
│  • 20 GB SSD                      │
│  • Docker instalado               │
├────────────────────────────────────┤
│  PANTALLAS                         │
│  • 2x Monitor 22" Full HD         │
│  • 2x Raspberry Pi 4 (4GB)        │
│  • 2x Teclado USB básico          │
├────────────────────────────────────┤
│  RED                               │
│  • Switch 8 puertos               │
│  • Cables Cat5e                   │
└────────────────────────────────────┘
```

### Escenario 2: Restaurante Mediano (3-6 pantallas)

```
┌────────────────────────────────────┐
│  SERVIDOR                          │
│  • 4 vCPU, 8 GB RAM               │
│  • 50 GB SSD                      │
│  • Docker + backups automáticos   │
├────────────────────────────────────┤
│  PANTALLAS                         │
│  • 6x Monitor 27" Full HD         │
│  • 6x Mini PC (Windows/Linux)     │
│  • 6x Botonera 5 teclas           │
│  • 2x Impresora térmica           │
├────────────────────────────────────┤
│  RED                               │
│  • Switch PoE 16 puertos          │
│  • Cables Cat6                    │
│  • UPS para servidor              │
└────────────────────────────────────┘
```

### Escenario 3: Cadena/Franquicia (10+ pantallas)

```
┌────────────────────────────────────┐
│  SERVIDOR PRINCIPAL                │
│  • 8 vCPU, 16 GB RAM              │
│  • 100 GB NVMe                    │
│  • PostgreSQL dedicado            │
│  • Redis Cluster                  │
├────────────────────────────────────┤
│  SERVIDOR BACKUP                   │
│  • 4 vCPU, 8 GB RAM               │
│  • Replica de BD                  │
├────────────────────────────────────┤
│  PANTALLAS (por local)             │
│  • 10x Monitor industrial 32"     │
│  • 10x Mini PC industrial         │
│  • 10x Botonera IP54              │
│  • 4x Impresora térmica           │
├────────────────────────────────────┤
│  RED                               │
│  • Switch managed 24 puertos      │
│  • VLANs configuradas             │
│  • UPS + generador respaldo       │
└────────────────────────────────────┘
```

---

## 13. Checklist de Pre-instalación

### Servidor
- [ ] Sistema operativo instalado y actualizado
- [ ] Docker y Docker Compose instalados
- [ ] Puertos 3000, 8080, 8081 disponibles
- [ ] Acceso a internet para descargar imágenes
- [ ] Espacio en disco verificado
- [ ] Usuario con permisos de Docker

### Red
- [ ] IPs fijas asignadas a servidor y pantallas
- [ ] DNS o /etc/hosts configurado
- [ ] Firewall con puertos abiertos
- [ ] Conectividad a MAXPOINT verificada

### Pantallas
- [ ] Dispositivos de visualización listos
- [ ] Monitores probados
- [ ] Teclados/botoneras conectados
- [ ] Navegador actualizado
- [ ] Resolución configurada

### MAXPOINT
- [ ] Credenciales de SQL Server
- [ ] IP y puerto del servidor
- [ ] Base de datos identificada
- [ ] Permisos de lectura verificados

---

**Documento**: Requerimientos del Sistema
**Sistema**: KDS v2.0 - Kitchen Display System
**Fecha**: Diciembre 2025
