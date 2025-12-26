# Configuración y Validación de Políticas

Las políticas controlan el comportamiento de la integración entre el sistema POS y el KDS. Deben configurarse correctamente antes de iniciar la comunicación.

## Políticas Principales

| Política | Tipo | Descripción |
|----------|------|-------------|
| `URL` | String | URL base del backend KDS |
| `EMAIL` | String | Credencial de autenticación |
| `PASSWORD` | String | Contraseña de autenticación |
| `ACTIVO` | Boolean | Si la estación está habilitada para KDS |
| `IMPRESION_A_TIEMPO_REAL` | Boolean | Si la impresión se realiza al enviar |
| `CANALES_EXCLUIDOS` | String | Lista de canales a no enviar al KDS |

## Obtención de Políticas

### Estructura de Respuesta

Al consultar las políticas del KDS, el sistema devuelve:

```json
{
  "url": "https://kds.empresa.com",
  "email": "operador@empresa.com",
  "password": "clave_segura",
  "activo": 1,
  "impresion_a_tiempo_real": 0,
  "canales_excluidos": "INTERNO,EMPLEADOS,CORTESIA"
}
```

### Interpretación de Valores

#### URL
Base del servidor KDS. Los endpoints se construyen a partir de esta URL:
- Login: `{url}/api/auth/login`
- Comandas: `{url}/api/tickets/receive`
- Health: `{url}/api/config/health`

#### ACTIVO
| Valor | Significado |
|-------|-------------|
| `1` | Estación habilitada - enviar comandas al KDS |
| `0` | Estación deshabilitada - no enviar comandas |

#### IMPRESION_A_TIEMPO_REAL
| Valor | Significado |
|-------|-------------|
| `1` | Imprimir ticket al momento de enviar la comanda |
| `0` | No imprimir automáticamente |

#### CANALES_EXCLUIDOS
Lista de canales separados por coma que NO deben enviarse al KDS:

```
"INTERNO,EMPLEADOS,CORTESIA,ANULADO"
```

## Flujo de Validación de Políticas

```
┌─────────────────────────────────────────────────────────────────┐
│               VALIDACIÓN DE POLÍTICAS                           │
└─────────────────────────────────────────────────────────────────┘

  ┌──────────┐                         ┌──────────┐
  │   POS    │                         │   KDS    │
  └────┬─────┘                         └────┬─────┘
       │                                    │
       │ 1. Consultar políticas             │
       │    (desde BD local/remota)         │
       │                                    │
       ▼                                    │
  ┌──────────────────────┐                  │
  │ ¿ACTIVO = 1?         │                  │
  └──────────┬───────────┘                  │
             │                              │
     No      │      Sí                      │
     ▼       │      ▼                       │
  ┌──────┐   │  ┌──────────────────────┐    │
  │ FIN  │   │  │ Verificar URL válida │    │
  └──────┘   │  └──────────┬───────────┘    │
             │             │                │
             │     No      │      Sí        │
             │     ▼       │      ▼         │
             │  ┌──────┐   │  ┌───────────────────┐
             │  │ ERROR│   │  │ Autenticar con    │
             │  └──────┘   │  │ EMAIL y PASSWORD  │
             │             │  └─────────┬─────────┘
             │             │            │
             │             │            ▼
             │             │    ┌───────────────────┐
             │             │    │ ¿Token obtenido?  │
             │             │    └─────────┬─────────┘
             │             │              │
             │             │      No      │      Sí
             │             │      ▼       │      ▼
             │             │   ┌──────┐   │  ┌───────────────┐
             │             │   │ ERROR│   │  │ LISTO PARA    │
             │             │   └──────┘   │  │ ENVIAR        │
             │             │              │  └───────────────┘
```

## Validación de Canales Excluidos

Antes de enviar una comanda, validar si el canal está excluido:

```
Entrada:
  - Canal de la orden: "INTERNO"
  - Canales excluidos: "INTERNO,EMPLEADOS,CORTESIA"

Proceso:
  1. Separar canales excluidos por coma
  2. Verificar si el canal de la orden está en la lista
  3. Si está: NO enviar la comanda
  4. Si no está: Proceder a enviar

Resultado: NO ENVIAR (canal "INTERNO" está en la lista)
```

### Pseudocódigo

```javascript
function debeEnviarAlKDS(canalOrden, canalesExcluidos) {
  // Si no hay canales excluidos, enviar todo
  if (!canalesExcluidos || canalesExcluidos.trim() === '') {
    return true;
  }

  // Convertir a array y normalizar
  const excluidos = canalesExcluidos
    .split(',')
    .map(c => c.trim().toUpperCase());

  // Verificar si está excluido
  const canalNormalizado = canalOrden.trim().toUpperCase();

  return !excluidos.includes(canalNormalizado);
}

// Ejemplos:
debeEnviarAlKDS("Kiosko-Efectivo", "INTERNO,EMPLEADOS")  // true
debeEnviarAlKDS("INTERNO", "INTERNO,EMPLEADOS")          // false
debeEnviarAlKDS("PedidosYa", "")                         // true
```

## Proceso Completo de Inicialización

### 1. Al Iniciar el Sistema

```
1. Obtener ID de restaurante y estación
2. Consultar políticas KDS para esta estación
3. Validar que ACTIVO = 1
4. Si está activo:
   a. Validar formato de URL
   b. Intentar autenticación con EMAIL/PASSWORD
   c. Almacenar tokens de forma segura
   d. Programar renovación automática de tokens
5. Registrar estado de conexión
```

### 2. Al Generar una Orden

```
1. Verificar que el sistema KDS esté activo
2. Obtener canal de la orden
3. Validar si el canal está excluido
4. Si no está excluido:
   a. Construir objeto ApiComanda
   b. Enviar al KDS
   c. Manejar respuesta
5. Si está excluido:
   a. No enviar al KDS
   b. Continuar flujo normal del POS
```

### 3. Manejo de Errores de Políticas

| Escenario | Acción |
|-----------|--------|
| URL vacía o inválida | No intentar conexión, registrar warning |
| EMAIL/PASSWORD vacíos | No intentar autenticación, registrar error |
| ACTIVO = 0 | No enviar comandas, operación silenciosa |
| Error de autenticación | Reintentar con backoff, alertar si persiste |
| Canal excluido | No enviar, continuar normalmente |

## Actualización de Políticas

Las políticas pueden cambiar durante la operación. Se recomienda:

1. **Recargar periódicamente**: Consultar políticas cada 5-10 minutos
2. **Recargar al fallar**: Si falla la autenticación, recargar políticas
3. **Cache local**: Mantener copia local para operación offline

```
┌─────────────────────────────────────────┐
│       CICLO DE ACTUALIZACIÓN            │
└─────────────────────────────────────────┘

  ┌─────────────┐
  │   Inicio    │
  └──────┬──────┘
         │
         ▼
  ┌─────────────────┐
  │ Cargar políticas│◄────────────┐
  └──────┬──────────┘             │
         │                        │
         ▼                        │
  ┌─────────────────┐             │
  │ Operar con      │             │
  │ políticas       │             │
  └──────┬──────────┘             │
         │                        │
         ▼                        │
  ┌─────────────────┐             │
  │ ¿Pasaron N min? │─────Sí──────┘
  │ ¿Error de auth? │
  └──────┬──────────┘
         │
         No
         │
         ▼
  ┌─────────────────┐
  │ Continuar       │
  │ operando        │
  └─────────────────┘
```

## Ejemplo de Configuración en Base de Datos

### Estructura SQL (Maxpoint)

Las políticas se almacenan en tablas de colección:

```sql
-- Políticas a nivel de restaurante
-- Tabla: RestauranteColeccionDeDatos

-- URL del KDS
Colección: 'KDS REGIONAL'
Descripción: 'URL'
variableV: 'https://kds.empresa.com'
variableB: 1 (activo)

-- Email de autenticación
Colección: 'KDS REGIONAL'
Descripción: 'EMAIL'
variableV: 'operador@empresa.com'
variableB: 1

-- Password de autenticación
Colección: 'KDS REGIONAL'
Descripción: 'PASSWORD'
variableV: 'clave_segura_123'
variableB: 1

-- Canales excluidos
Colección: 'KDS REGIONAL'
Descripción: 'CANALES EXCLUIDOS'
variableV: 'INTERNO,EMPLEADOS'
variableB: 1
```

```sql
-- Políticas a nivel de estación
-- Tabla: EstacionColeccionDeDatos

-- Activación de estación
Colección: 'KDS REGIONAL'
Descripción: 'ACTIVO'
variableB: 1 (1=activo, 0=inactivo)

-- Impresión a tiempo real
Colección: 'KDS REGIONAL'
Descripción: 'IMPRESION A TIEMPO REAL'
variableB: 0 (1=sí, 0=no)
```

## Resumen de Validaciones

| Paso | Validación | Si falla |
|------|------------|----------|
| 1 | Política ACTIVO = 1 | No enviar, operación silenciosa |
| 2 | URL no vacía y formato válido | Registrar error, no conectar |
| 3 | EMAIL y PASSWORD disponibles | Registrar error, no autenticar |
| 4 | Autenticación exitosa | Reintentar con backoff |
| 5 | Canal no excluido | No enviar, continuar |
| 6 | Modo API habilitado en KDS | Esperar configuración |
