# Flujo Completo de Integración

Este documento describe el flujo completo de integración desde que se genera una orden en el sistema POS hasta que aparece en las pantallas del KDS.

## Diagrama General

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          FLUJO DE INTEGRACIÓN KDS                           │
└─────────────────────────────────────────────────────────────────────────────┘

┌──────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐
│   POS    │────>│ Validar  │────>│  Login   │────>│  Enviar  │────>│   KDS    │
│ (Orden)  │     │ Políticas│     │  JWT     │     │ Comanda  │     │(Pantalla)│
└──────────┘     └──────────┘     └──────────┘     └──────────┘     └──────────┘
                      │                │                │                │
                      │                │                │                │
                      ▼                ▼                ▼                ▼
                 ┌─────────┐     ┌─────────┐     ┌─────────┐     ┌─────────┐
                 │ ¿Activo?│     │ Tokens  │     │ HTTP    │     │ WebSocket│
                 │ ¿Canal? │     │ Storage │     │ Request │     │ Notify  │
                 └─────────┘     └─────────┘     └─────────┘     └─────────┘
```

## Fase 1: Inicialización del Sistema

### 1.1 Obtención de Políticas

Al iniciar el sistema cliente (POS, sync service, etc.):

```
┌────────────────────────────────────────────────────────────────┐
│                    INICIALIZACIÓN                              │
└────────────────────────────────────────────────────────────────┘

[Cliente]                                          [Base de Datos]
    │                                                    │
    │  1. Consultar políticas KDS                        │
    │    (restaurante + estación)                        │
    │───────────────────────────────────────────────────>│
    │                                                    │
    │  {url, email, password, activo,                    │
    │   impresion_a_tiempo_real, canales_excluidos}      │
    │<───────────────────────────────────────────────────│
    │                                                    │
    ├─ 2. Validar: activo == 1                           │
    │   └─ Si no: terminar (no conectar)                 │
    │                                                    │
    ├─ 3. Validar: url no vacía                          │
    │   └─ Si vacía: error de configuración              │
    │                                                    │
    ├─ 4. Guardar canales excluidos en memoria           │
    │                                                    │
    └─ 5. Proceder a autenticación                       │
```

### 1.2 Autenticación Inicial

```
[Cliente]                                          [KDS Backend]
    │                                                    │
    │  POST /api/auth/login                              │
    │  { email, password }                               │
    │───────────────────────────────────────────────────>│
    │                                                    │
    │              ┌─────────────────────────────┐       │
    │              │ Verificar credenciales      │       │
    │              │ Generar tokens JWT          │       │
    │              └─────────────────────────────┘       │
    │                                                    │
    │  { accessToken, refreshToken, user }               │
    │<───────────────────────────────────────────────────│
    │                                                    │
    ├─ Almacenar accessToken (memoria)                   │
    ├─ Almacenar refreshToken (persistente)              │
    └─ Calcular tiempo de expiración                     │
```

## Fase 2: Envío de Comandas

### 2.1 Generación de Orden en POS

Cuando se genera/confirma una orden en el sistema POS:

```
[Usuario]           [POS]                          [Cliente KDS]
    │                 │                                  │
    │ Confirmar orden │                                  │
    │────────────────>│                                  │
    │                 │                                  │
    │                 ├─ 1. Obtener datos de la orden    │
    │                 │                                  │
    │                 ├─ 2. Obtener canal de la orden    │
    │                 │                                  │
    │                 │  Notificar orden confirmada      │
    │                 │─────────────────────────────────>│
    │                 │                                  │
    │                 │                                  ├─ 3. Validar canal
    │                 │                                  │    no excluido
    │                 │                                  │
    │                 │                                  ├─ 4. Construir
    │                 │                                  │    ApiComanda
    │                 │                                  │
    │                 │                                  └─ 5. Enviar al KDS
```

### 2.2 Validación Pre-Envío

```
function validarAntesDeEnviar(orden, politicas) {
  // 1. Verificar que el sistema esté activo
  if (!politicas.activo) {
    return { enviar: false, razon: "Sistema KDS inactivo" };
  }

  // 2. Verificar que tengamos token válido
  if (!tieneTokenValido()) {
    intentarRenovarToken();
    if (!tieneTokenValido()) {
      return { enviar: false, razon: "Sin autenticación" };
    }
  }

  // 3. Verificar que el canal no esté excluido
  if (esCanalExcluido(orden.canal, politicas.canales_excluidos)) {
    return { enviar: false, razon: "Canal excluido" };
  }

  // 4. Verificar que la orden tenga productos
  if (!orden.productos || orden.productos.length === 0) {
    return { enviar: false, razon: "Orden sin productos" };
  }

  return { enviar: true };
}
```

### 2.3 Construcción del Objeto ApiComanda

```
function construirApiComanda(ordenPOS) {
  return {
    id: ordenPOS.id,
    orderId: ordenPOS.id,
    createdAt: new Date().toISOString(),
    channel: {
      id: ordenPOS.canalId,
      name: ordenPOS.canalNombre,       // "Kiosko-Efectivo"
      type: ordenPOS.tipoServicio       // "LLEVAR"
    },
    cashRegister: {
      cashier: ordenPOS.cajeroId,
      name: ordenPOS.cajaNombre
    },
    customer: ordenPOS.clienteNombre ? {
      name: ordenPOS.clienteNombre
    } : undefined,
    products: ordenPOS.productos.map(p => ({
      productId: p.id,
      name: p.nombre,
      amount: p.cantidad,
      category: p.categoria,
      content: p.modificadores,         // ["*SIN SAL", "*EXTRA QUESO"]
      modifier: p.descripcionVariante,
      comments: p.comentarios
    })),
    otrosDatos: {
      turno: ordenPOS.turno,
      nroCheque: ordenPOS.numeroFactura,
      llamarPor: ordenPOS.nombreCliente
    },
    comments: ordenPOS.comentariosGenerales,
    statusPos: ordenPOS.estado          // "PEDIDO TOMADO"
  };
}
```

### 2.4 Envío HTTP al KDS

```
[Cliente]                                          [KDS Backend]
    │                                                    │
    │  POST /api/tickets/receive                         │
    │  Authorization: Bearer {accessToken}               │
    │  Content-Type: application/json                    │
    │  Body: { ApiComanda }                              │
    │───────────────────────────────────────────────────>│
    │                                                    │
    │              ┌─────────────────────────────┐       │
    │              │ 1. Validar JWT              │       │
    │              │ 2. Verificar modo API       │       │
    │              │ 3. Procesar comanda         │       │
    │              │ 4. Guardar en BD            │       │
    │              │ 5. Distribuir a pantallas   │       │
    │              │ 6. Notificar via WebSocket  │       │
    │              └─────────────────────────────┘       │
    │                                                    │
    │  200 OK                                            │
    │  { success: true, orderId: "clx123..." }           │
    │<───────────────────────────────────────────────────│
```

## Fase 3: Procesamiento en KDS

### 3.1 Recepción y Validación

```
[KDS Backend]
    │
    ├─ 1. Extraer y validar JWT del header
    │   └─ Si inválido: 401 Unauthorized
    │
    ├─ 2. Verificar que modo API esté habilitado
    │   └─ Si no: 400 "Modo API no habilitado"
    │
    ├─ 3. Parsear body como ApiComanda
    │   └─ Si error: 400 "Invalid JSON"
    │
    ├─ 4. Convertir a formato interno
    │
    └─ 5. Procesar orden
```

### 3.2 Persistencia y Distribución

```
[KDS Backend]
    │
    ├─ 1. Verificar si orden ya existe (por externalId)
    │   ├─ Si existe: Actualizar (sin mover de pantalla)
    │   └─ Si no existe: Crear nueva
    │
    ├─ 2. Guardar orden en base de datos
    │   └─ Crear items asociados
    │
    ├─ 3. Obtener colas activas
    │   ├─ DISTRIBUTED: Reparte entre pantallas
    │   └─ SINGLE: Una sola pantalla por cola
    │
    ├─ 4. Calcular distribución
    │   └─ Asignar a pantalla con menos carga
    │
    └─ 5. Notificar pantallas via WebSocket
        └─ Mensaje: { type: 'new_order', order: {...} }
```

### 3.3 Notificación a Pantallas

```
[KDS Backend]                                      [Pantalla KDS]
    │                                                    │
    │  WebSocket: new_order                              │
    │  { type: 'new_order', order: {...} }               │
    │───────────────────────────────────────────────────>│
    │                                                    │
    │                                                    ├─ Agregar a lista
    │                                                    │
    │                                                    ├─ Reproducir sonido
    │                                                    │
    │                                                    └─ Actualizar UI
```

## Fase 4: Manejo de Errores y Reintentos

### 4.1 Estrategia de Reintentos

```
┌─────────────────────────────────────────────────────────────────┐
│                  ESTRATEGIA DE REINTENTOS                       │
└─────────────────────────────────────────────────────────────────┘

function enviarConReintentos(comanda, maxReintentos = 3) {
  let intento = 0;
  let esperaBase = 1000; // 1 segundo

  while (intento < maxReintentos) {
    try {
      return enviarComanda(comanda);
    } catch (error) {
      intento++;

      if (error.status === 401) {
        // Token expirado - renovar y reintentar
        await renovarToken();
        continue;
      }

      if (error.status >= 500) {
        // Error del servidor - esperar y reintentar
        await sleep(esperaBase * Math.pow(2, intento));
        continue;
      }

      // Error del cliente (400) - no reintentar
      throw error;
    }
  }

  // Máximo de reintentos alcanzado
  guardarParaReintentoPosterior(comanda);
}
```

### 4.2 Cola de Pendientes

```
┌────────────────────────────────────────────────────────────────┐
│                    COLA DE PENDIENTES                          │
└────────────────────────────────────────────────────────────────┘

Si el envío falla después de todos los reintentos:

1. Guardar comanda en cola local persistente
2. Registrar timestamp del fallo
3. Programar reintento para más tarde

Proceso de reintentos periódicos:
1. Cada N minutos, revisar cola de pendientes
2. Para cada comanda pendiente:
   a. Verificar conectividad con KDS
   b. Si hay conexión: reintentar envío
   c. Si éxito: remover de cola
   d. Si falla: actualizar contador de reintentos
3. Alertar si hay comandas muy antiguas sin enviar
```

### 4.3 Códigos de Error y Acciones

| Código | Significado | Acción |
|--------|-------------|--------|
| 200 | Éxito | Continuar |
| 400 | Error en datos | Revisar formato, no reintentar |
| 401 | Token expirado | Renovar token y reintentar |
| 403 | Sin permisos | Verificar rol del usuario |
| 404 | Endpoint no existe | Verificar URL |
| 500 | Error del servidor | Reintentar con backoff |
| 503 | Servicio no disponible | Reintentar con backoff |
| Timeout | Sin respuesta | Reintentar |
| Network Error | Sin conexión | Guardar en cola local |

## Fase 5: Renovación de Token

### 5.1 Renovación Proactiva

```
┌────────────────────────────────────────────────────────────────┐
│                 RENOVACIÓN PROACTIVA                           │
└────────────────────────────────────────────────────────────────┘

Al recibir tokens:
1. Decodificar JWT para obtener tiempo de expiración
2. Calcular: tiempoRenovacion = expiracion - 3 minutos
3. Programar timer para renovar en tiempoRenovacion

Cuando el timer se dispare:
1. Llamar POST /api/auth/refresh
2. Si éxito: actualizar accessToken, reprogramar timer
3. Si falla: intentar login completo
```

### 5.2 Renovación Reactiva

```
function manejarRespuesta(response) {
  if (response.status === 401) {
    // Intentar renovar token
    const nuevoToken = await renovarToken();

    if (nuevoToken) {
      // Reintentar petición original con nuevo token
      return reintentarPeticion();
    } else {
      // Renovación falló - hacer login completo
      const tokens = await hacerLogin();

      if (tokens) {
        return reintentarPeticion();
      } else {
        throw new Error("No se pudo autenticar");
      }
    }
  }

  return response;
}
```

## Resumen del Ciclo Completo

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         CICLO COMPLETO                                      │
└─────────────────────────────────────────────────────────────────────────────┘

         INICIO
            │
            ▼
   ┌────────────────┐
   │ Obtener        │
   │ Políticas      │
   └───────┬────────┘
           │
           ▼
   ┌────────────────┐     No     ┌─────────┐
   │ ¿Activo?       │───────────>│   FIN   │
   └───────┬────────┘            └─────────┘
           │ Sí
           ▼
   ┌────────────────┐
   │ Autenticar     │
   │ (Login)        │
   └───────┬────────┘
           │
           ▼
   ┌────────────────┐
   │ Esperar        │◄──────────────────────────────┐
   │ Orden          │                               │
   └───────┬────────┘                               │
           │                                        │
           ▼                                        │
   ┌────────────────┐     Sí     ┌─────────────┐   │
   │ ¿Canal         │───────────>│ No enviar   │───┘
   │ Excluido?      │            └─────────────┘
   └───────┬────────┘
           │ No
           ▼
   ┌────────────────┐
   │ Construir      │
   │ ApiComanda     │
   └───────┬────────┘
           │
           ▼
   ┌────────────────┐
   │ Enviar         │
   │ POST /receive  │
   └───────┬────────┘
           │
           ▼
   ┌────────────────┐     401    ┌─────────────┐
   │ ¿Éxito?        │───────────>│ Renovar     │
   └───────┬────────┘            │ Token       │
           │                     └──────┬──────┘
           │ Sí                         │
           │                            ▼
           │                    ┌─────────────┐
           │                    │ Reintentar  │
           │                    └─────────────┘
           │
           ▼
   ┌────────────────┐
   │ Registrar      │
   │ Éxito          │
   └───────┬────────┘
           │
           └──────────────────────────────────────┐
                                                  │
                                                  ▼
                                          (Volver a esperar)
```

## Checklist de Integración

- [ ] Configurar políticas KDS en base de datos
- [ ] Crear usuario con rol OPERATOR o ADMIN en KDS
- [ ] Verificar conectividad de red al servidor KDS
- [ ] Habilitar modo API en configuración del KDS
- [ ] Implementar obtención de políticas al iniciar
- [ ] Implementar autenticación JWT
- [ ] Implementar renovación de tokens
- [ ] Implementar filtrado por canales excluidos
- [ ] Implementar construcción de ApiComanda
- [ ] Implementar envío HTTP con headers correctos
- [ ] Implementar manejo de errores y reintentos
- [ ] Implementar cola de pendientes para fallos
- [ ] Probar flujo completo en ambiente de pruebas
- [ ] Monitorear logs en producción
