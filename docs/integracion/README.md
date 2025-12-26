# Guía de Integración KDS Regional

Esta documentación describe cómo integrar un sistema externo (POS, ERP, etc.) con el Sistema de Visualización de Cocina (KDS Regional).

## Contenido

| Documento | Descripción |
|-----------|-------------|
| [estructura-json.md](./estructura-json.md) | Estructura del objeto JSON de comanda |
| [ejemplos-json.md](./ejemplos-json.md) | Ejemplos prácticos de JSON (de Postman) |
| [autenticacion.md](./autenticacion.md) | Autenticación JWT y renovación de tokens |
| [politicas.md](./politicas.md) | Configuración y validación de políticas |
| [flujo-integracion.md](./flujo-integracion.md) | Flujo completo del proceso de integración |
| [ejemplos.md](./ejemplos.md) | Ejemplos de código en diferentes lenguajes |

## Resumen Rápido

### Endpoints Principales

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | `/api/auth/login` | Obtener tokens de autenticación | No |
| POST | `/api/auth/refresh` | Renovar access token | No |
| POST | `/api/tickets/receive` | Enviar una comanda | Sí |
| POST | `/api/tickets/receive-batch` | Enviar múltiples comandas | Sí |
| GET | `/api/config/health` | Verificar estado del sistema | No |

### Flujo Básico

```
1. Autenticación
   POST /api/auth/login → { accessToken, refreshToken }

2. Envío de Comanda
   POST /api/tickets/receive
   Header: Authorization: Bearer {accessToken}
   Body: { comanda JSON }

3. Renovación de Token (cuando expire)
   POST /api/auth/refresh → { accessToken }
```

### Requisitos Previos

1. **Credenciales de acceso**: Usuario y contraseña configurados en el KDS
2. **URL del backend**: Dirección del servidor KDS (ej: `https://kds.empresa.com`)
3. **Modo API habilitado**: El administrador debe activar el modo API en la configuración del KDS

## Soporte

Para soporte técnico o consultas sobre la integración, contactar al equipo de desarrollo.
