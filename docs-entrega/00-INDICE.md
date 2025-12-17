# Documentación KDS v2.0 - Índice

## Kitchen Display System - Entrega de Proyecto

---

## Documentos Incluidos

### 1. [Arquitectura del Sistema](./01-ARQUITECTURA.md)
Descripción técnica de la arquitectura del sistema, componentes, flujos de datos y tecnologías utilizadas.

**Contenido**:
- Diagrama de arquitectura general
- Descripción de componentes (Backend, KDS Frontend, Backoffice)
- Infraestructura Docker
- Flujos de datos principales
- Modelo de datos simplificado
- Seguridad y escalabilidad

---

### 2. [Requerimientos del Sistema](./02-REQUERIMIENTOS.md)
Especificaciones de hardware y software necesarios para ejecutar el sistema.

**Contenido**:
- Requerimientos del servidor (mínimos y recomendados)
- Requerimientos de software
- Requerimientos de red y puertos
- Requerimientos de pantallas KDS
- Requerimientos de botonera física
- Requerimientos de impresoras
- Requerimientos de base de datos
- Matriz de compatibilidad
- Escenarios de despliegue

---

### 3. [Manual de Usuario](./03-MANUAL-USUARIO.md)
Guía para operadores y administradores del sistema.

**Contenido**:
- Uso de la pantalla KDS
- Uso del teclado/botonera
- Navegación y finalización de órdenes
- Modo standby
- Uso del Backoffice
- Gestión de pantallas, colas, órdenes
- Configuración de apariencia y SLA
- Gestión de usuarios
- Solución de problemas comunes

---

### 4. [Manual Técnico](./04-MANUAL-TECNICO.md)
Guía para instalación, configuración y mantenimiento del sistema.

**Contenido**:
- Instalación con Docker (producción)
- Instalación para desarrollo
- Variables de entorno completas
- Configuración de Nginx y SSL
- Migraciones de base de datos
- Backup y restore
- API REST básica
- WebSocket events
- Monitoreo y logs
- Troubleshooting

---

### 5. [Detalle de Desarrollo](./05-DETALLE-DESARROLLO.md)
Especificación técnica detallada de todo el código desarrollado.

**Contenido**:
- Estructura de archivos completa
- Servicios del backend (con líneas de código)
- Componentes del frontend KDS
- Páginas del backoffice
- Hooks y stores (Zustand)
- Middlewares y validaciones
- Infraestructura Docker
- Funcionalidades especiales
- Pendientes y mejoras futuras

---

### 6. [Referencia de API](./06-API-REFERENCE.md)
Documentación completa de todos los endpoints REST.

**Contenido**:
- Autenticación (login, refresh, me)
- Usuarios (CRUD)
- Pantallas (CRUD + configuración)
- Colas (CRUD + canales + filtros)
- Órdenes (CRUD + stats)
- Configuración general
- Polling MAXPOINT
- Mirror KDS
- WebSocket events

---

### 7. [Guía de Despliegue](./07-GUIA-DESPLIEGUE.md)
Instrucciones paso a paso para poner el sistema en producción.

**Contenido**:
- Despliegue rápido con Docker
- Configuración de firewall
- Configuración SSL con Let's Encrypt
- Configuración de pantallas (autoarranque)
- Configuración MAXPOINT
- Backup automático
- Monitoreo
- Actualización del sistema
- Checklist de producción

---

## Información del Proyecto

| Campo | Valor |
|-------|-------|
| **Sistema** | KDS - Kitchen Display System |
| **Versión** | 2.0.0 |
| **Fecha** | Diciembre 2025 |
| **Stack** | Node.js + React + PostgreSQL + Redis |
| **Arquitectura** | Monorepo (3 aplicaciones) |

---

## Accesos por Defecto

### Backoffice
- **URL**: http://servidor:8081
- **Usuario**: admin@kds.local
- **Contraseña**: admin123

### Base de Datos (Desarrollo)
- **Host**: localhost:5432
- **Usuario**: kds_dev
- **Contraseña**: kds_dev_password
- **Base de datos**: kds_dev

---

## Contacto

Para soporte técnico sobre esta entrega, contactar al equipo de desarrollo.

---

## Archivos Adicionales

Además de esta documentación, el repositorio incluye:

| Archivo | Descripción |
|---------|-------------|
| `.env.example` | Plantilla de variables de entorno |
| `KDS-API.postman_collection.json` | Colección Postman con todos los endpoints |
| `Makefile` | Comandos útiles para desarrollo |
| `infra/docker-compose.yml` | Configuración Docker producción |
| `infra/docker-compose.dev.yml` | Configuración Docker desarrollo |

---

**Generado**: Diciembre 2025
**Total de documentos**: 7
**Páginas aproximadas**: ~150
