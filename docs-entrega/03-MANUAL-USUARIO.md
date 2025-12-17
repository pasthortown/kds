# Manual de Usuario - Sistema KDS v2.0

## Kitchen Display System - Guía de Uso

---

## 1. Introducción

El **KDS (Kitchen Display System)** es un sistema de visualización de pedidos en tiempo real para cocinas de restaurantes. Este manual cubre el uso diario del sistema tanto para operadores de cocina como para administradores.

### 1.1 Componentes del Sistema

| Componente | URL | Descripción |
|------------|-----|-------------|
| **Pantalla KDS** | http://servidor:8080 | Visualización de órdenes en cocina |
| **Backoffice** | http://servidor:8081 | Panel de administración |

---

## 2. Pantalla KDS (Cocina)

### 2.1 Acceso a la Pantalla

1. Abrir el navegador en el dispositivo de cocina
2. Navegar a `http://servidor:8080`
3. Seleccionar el número de pantalla asignado
4. La pantalla comenzará a mostrar órdenes automáticamente

### 2.2 Interfaz Principal

```
┌─────────────────────────────────────────────────────────────────┐
│  [LOGO]     PANTALLA 1 - POLLOS           Cola: Cocina Principal│ ← Header
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐        │
│  │ #123     │  │ #124     │  │ #125     │  │ #126     │        │
│  │ 00:45    │  │ 01:23    │  │ 00:12    │  │ 02:15    │        │
│  │          │  │          │  │          │  │          │        │
│  │ 2x Pollo │  │ 1x Combo │  │ 3x Alitas│  │ 1x Burger│        │
│  │ Frito    │  │ Familiar │  │ BBQ      │  │ Doble    │        │
│  │          │  │          │  │          │  │          │        │
│  │ [LOCAL]  │  │ [LLEVAR] │  │ [RAPPI]  │  │ [KIOSKO] │        │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘        │ ← Órdenes
│                                                                  │
│  Página 1 de 3                              < Anterior | Sig >   │
├─────────────────────────────────────────────────────────────────┤
│  Órdenes pendientes: 12                                          │ ← Footer
└─────────────────────────────────────────────────────────────────┘
```

### 2.3 Elementos de una Tarjeta de Orden

| Elemento | Descripción |
|----------|-------------|
| **Número (#123)** | Identificador de la orden |
| **Timer (00:45)** | Tiempo transcurrido desde que llegó |
| **Color de fondo** | Indica urgencia según SLA configurado |
| **Productos** | Lista de items a preparar |
| **Badge de canal** | LOCAL, LLEVAR, RAPPI, KIOSKO, etc. |

### 2.4 Colores del Timer (SLA)

Los colores cambian según el tiempo transcurrido:

| Color | Significado | Tiempo típico |
|-------|-------------|---------------|
| Verde | Tiempo normal | 0 - 3 minutos |
| Amarillo | Atención | 3 - 5 minutos |
| Naranja | Urgente | 5 - 8 minutos |
| Rojo | Crítico | > 8 minutos |

*Los tiempos son configurables desde el Backoffice*

### 2.5 Uso del Teclado/Botonera

#### Teclas por Defecto

| Tecla | Acción |
|-------|--------|
| **H** | Finalizar orden 1 (primera visible) |
| **3** | Finalizar orden 2 |
| **1** | Finalizar orden 3 |
| **F** | Finalizar orden 4 |
| **J** | Finalizar orden 5 |
| **I** | Siguiente página |
| **G** | Página anterior |
| **C** | Deshacer última acción |
| **R** | Resetear timer de orden seleccionada |

#### Combinaciones de Teclas

| Combinación | Acción |
|-------------|--------|
| **I + G** (mantener 3 seg) | Activar/desactivar modo Standby |

### 2.6 Finalizar una Orden

**Con teclado:**
1. Presionar la tecla correspondiente a la posición de la orden (H, 3, 1, F, J)
2. La orden desaparecerá de la pantalla

**Con pantalla táctil (si está habilitado):**
1. Tocar la tarjeta de la orden
2. Confirmar en el modal que aparece

### 2.7 Deshacer una Acción

Si finalizaste una orden por error:
1. Presionar **C** inmediatamente
2. La última orden finalizada volverá a aparecer

### 2.8 Modo Standby

El modo standby muestra una pantalla negra para ahorrar energía o durante tiempos sin actividad.

**Activar Standby:**
- Mantener presionadas las teclas **I + G** durante 3 segundos

**Desactivar Standby:**
- Presionar cualquier tecla
- Tocar la pantalla (si es táctil)

### 2.9 Navegación entre Páginas

Cuando hay más órdenes de las que caben en pantalla:

| Tecla | Acción |
|-------|--------|
| **I** | Ir a página siguiente |
| **G** | Ir a página anterior |
| **Q** | Ir a página 1 |
| **W** | Ir a página 2 |
| **T** | Ir a última página |

---

## 3. Backoffice (Administración)

### 3.1 Acceso al Sistema

1. Abrir navegador
2. Ir a `http://servidor:8081`
3. Ingresar credenciales:
   - **Email**: admin@kds.local (por defecto)
   - **Password**: admin123 (por defecto)
4. Clic en "Iniciar Sesión"

### 3.2 Menú Principal

```
┌─────────────────────────────────────────┐
│  KDS Admin                              │
├─────────────────────────────────────────┤
│  📊 Dashboard                           │
│  🖥️ Pantallas                           │
│  📋 Colas                               │
│  📦 Órdenes                             │
│  🎨 Apariencia                          │
│  ⏱️ SLA                                 │
│  ⚙️ Configuración                       │
│  👥 Usuarios                            │
├─────────────────────────────────────────┤
│  🚪 Cerrar Sesión                       │
└─────────────────────────────────────────┘
```

---

## 4. Dashboard

El Dashboard muestra un resumen del estado del sistema en tiempo real.

### 4.1 KPIs Principales

| Indicador | Descripción |
|-----------|-------------|
| **Órdenes Hoy** | Total de órdenes procesadas en el día |
| **Tiempo Promedio** | Tiempo promedio de preparación |
| **Pantallas Online** | Número de pantallas activas |
| **Órdenes Pendientes** | Órdenes en espera de preparación |

### 4.2 Gráficos

- **Órdenes por Hora**: Distribución de pedidos durante el día
- **Tiempo Promedio**: Evolución del tiempo de preparación
- **Por Canal**: Distribución de órdenes según origen

### 4.3 Estado de Pantallas

Lista de todas las pantallas con su estado actual:
- 🟢 **Online**: Funcionando correctamente
- 🔴 **Offline**: Sin conexión
- 🟡 **Standby**: En modo pausa

---

## 5. Gestión de Pantallas

### 5.1 Ver Pantallas

1. Ir a **Pantallas** en el menú
2. Se muestra tabla con todas las pantallas configuradas

| Campo | Descripción |
|-------|-------------|
| Número | Identificador numérico (1, 2, 3...) |
| Nombre | Nombre descriptivo (Pollos 1, Bebidas, etc.) |
| Cola | Cola asignada |
| Estado | ONLINE / OFFLINE / STANDBY |
| Acciones | Editar, Configurar, Eliminar |

### 5.2 Crear Nueva Pantalla

1. Clic en **+ Nueva Pantalla**
2. Completar formulario:
   - **Nombre**: Nombre descriptivo (ej: "Cocina Pollos 1")
   - **Cola**: Seleccionar cola de distribución
3. Clic en **Guardar**

### 5.3 Configurar Pantalla

Para cada pantalla se puede configurar:

#### Apariencia
- Colores de fondo, texto, tarjetas
- Tipografías
- Número de columnas y filas
- Tema (claro/oscuro)

#### Preferencias
- Mostrar/ocultar datos del cliente
- Formato del identificador
- Comportamiento de paginación

#### Teclado
- Reasignar teclas para acciones
- Configurar combinaciones (combos)
- Ajustar tiempo de debounce

#### Impresora
- IP de la impresora
- Puerto (default: 9100)
- Habilitar/deshabilitar

### 5.4 Poner en Standby

1. Encontrar la pantalla en la lista
2. Clic en icono de **Standby** (media luna)
3. La pantalla mostrará pantalla negra

### 5.5 Activar Pantalla

1. Encontrar la pantalla en la lista
2. Clic en icono de **Activar** (play)
3. La pantalla volverá a mostrar órdenes

---

## 6. Gestión de Colas

Las colas definen cómo se distribuyen las órdenes entre las pantallas.

### 6.1 Ver Colas

1. Ir a **Colas** en el menú
2. Se muestra lista de colas configuradas

### 6.2 Crear Cola

1. Clic en **+ Nueva Cola**
2. Completar:
   - **Nombre**: Nombre de la cola (ej: "Cocina Principal")
   - **Distribución**: DISTRIBUTED (Round-Robin) o SINGLE (una pantalla)
3. Clic en **Guardar**

### 6.3 Configurar Canales

Los canales definen los orígenes de pedidos:

1. Seleccionar una cola
2. En sección **Canales**, clic en **+ Agregar Canal**
3. Configurar:
   - **Nombre**: local, kiosko-efectivo, rappi, uber, etc.
   - **Color**: Color del badge en las tarjetas
   - **Prioridad**: Orden de procesamiento

#### Canales Típicos

| Canal | Color sugerido | Descripción |
|-------|---------------|-------------|
| local | Azul (#4a90e2) | Pedidos en mostrador |
| llevar | Verde (#52c41a) | Para llevar |
| kiosko-efectivo | Naranja (#fa8c16) | Kiosko pago efectivo |
| kiosko-tarjeta | Morado (#722ed1) | Kiosko pago tarjeta |
| rappi | Naranja (#ff5722) | Delivery Rappi |
| uber | Negro (#000000) | Delivery Uber |
| pedidosya | Rojo (#e53935) | Delivery PedidosYa |
| drive | Verde oscuro (#2e7d32) | Drive-thru |
| app | Cyan (#00bcd4) | App propia |

### 6.4 Configurar Filtros

Los filtros permiten ocultar o resaltar productos específicos:

1. En sección **Filtros**, clic en **+ Agregar Filtro**
2. Configurar:
   - **Patrón**: Expresión regular (ej: `^BEBIDA.*`)
   - **Suprimir**: Si está marcado, oculta productos que coincidan

---

## 7. Gestión de Órdenes

### 7.1 Ver Órdenes

1. Ir a **Órdenes** en el menú
2. Se muestra historial de órdenes

### 7.2 Filtros Disponibles

| Filtro | Opciones |
|--------|----------|
| Estado | PENDING, IN_PROGRESS, FINISHED, CANCELLED |
| Pantalla | Cualquier pantalla configurada |
| Canal | local, kiosko, delivery, etc. |
| Fecha | Rango de fechas |
| Búsqueda | Número de orden, cliente |

### 7.3 Acciones sobre Órdenes

| Acción | Descripción |
|--------|-------------|
| **Ver detalle** | Muestra todos los items y tiempos |
| **Cancelar** | Cancela la orden (solo admin) |
| **Exportar** | Descarga reporte PDF |

---

## 8. Configuración de Apariencia

### 8.1 Acceder al Editor

1. Ir a **Apariencia** en el menú
2. Seleccionar pantalla a configurar

### 8.2 Secciones Configurables

#### Layout
- **Columnas por pantalla**: 1-10 columnas
- **Filas**: 1-6 filas
- **Tamaño de columna**: Ancho en píxeles

#### Tema
- **Tema base**: Oscuro o Claro
- **Color de fondo**: Color general
- **Color del header**: Barra superior
- **Color de tarjetas**: Fondo de órdenes
- **Color de texto**: Texto principal
- **Color de acento**: Elementos destacados

#### Tipografías
Para cada elemento se puede configurar:
- Familia de fuente
- Tamaño
- Peso (bold, normal)
- Color
- Transformación (mayúsculas)

**Elementos configurables**:
- Header (título de orden)
- Timer
- Nombre cliente
- Cantidad
- Nombre producto
- Subitems
- Modificadores
- Notas
- Canal/Footer

### 8.3 Preview en Vivo

El editor incluye un preview que muestra cómo se verá la pantalla con los cambios aplicados.

1. Hacer cambios en el formulario
2. Ver resultado en el preview de la derecha
3. Clic en **Guardar** cuando esté conforme

---

## 9. Configuración SLA

### 9.1 Qué es el SLA

El SLA (Service Level Agreement) define los tiempos objetivo para preparar órdenes y los colores que indican el estado.

### 9.2 Configurar Tiempos

1. Ir a **SLA** en el menú
2. Seleccionar pantalla
3. Definir intervalos de tiempo:

| Intervalo | Color | Tiempo |
|-----------|-------|--------|
| Normal | Verde | 0:00 - 3:00 |
| Atención | Amarillo | 3:00 - 5:00 |
| Urgente | Naranja | 5:00 - 8:00 |
| Crítico | Rojo | > 8:00 |

4. Clic en **Guardar**

### 9.3 Aplicar a Todas las Pantallas

1. Configurar una pantalla
2. Clic en **Aplicar a todas**
3. Confirmar la acción

---

## 10. Configuración General

### 10.1 Configuración MAXPOINT

1. Ir a **Configuración** > **MAXPOINT**
2. Completar datos de conexión:
   - **Servidor**: IP o hostname del SQL Server
   - **Puerto**: 1433 (por defecto)
   - **Usuario**: Usuario de SQL Server
   - **Contraseña**: Contraseña
   - **Base de datos**: Nombre de la BD
3. Clic en **Probar conexión**
4. Si es exitosa, clic en **Guardar**

### 10.2 Modos de Operación

| Modo | Descripción |
|------|-------------|
| **Modo Ticket** | POLLING (lee de MAXPOINT) o API (recibe por HTTP) |
| **Modo Impresión** | LOCAL (TCP directo) o CENTRALIZED (servidor de impresión) |
| **Modo Prueba** | Activa sandbox para testing sin afectar producción |

### 10.3 Configuración de Polling

| Parámetro | Descripción | Default |
|-----------|-------------|---------|
| **Intervalo** | Cada cuánto consultar MAXPOINT | 2000 ms |
| **Lifetime** | Tiempo que una orden permanece activa | 4 horas |

---

## 11. Gestión de Usuarios

### 11.1 Roles de Usuario

| Rol | Permisos |
|-----|----------|
| **ADMIN** | Control total del sistema |
| **OPERATOR** | Gestión de pantallas y órdenes |
| **VIEWER** | Solo lectura |

### 11.2 Crear Usuario

1. Ir a **Usuarios** en el menú
2. Clic en **+ Nuevo Usuario**
3. Completar:
   - **Nombre**: Nombre completo
   - **Email**: Correo electrónico (login)
   - **Contraseña**: Mínimo 6 caracteres
   - **Rol**: Seleccionar nivel de acceso
4. Clic en **Guardar**

### 11.3 Editar Usuario

1. Encontrar usuario en la lista
2. Clic en **Editar**
3. Modificar datos necesarios
4. Clic en **Guardar**

### 11.4 Desactivar Usuario

1. Encontrar usuario en la lista
2. Clic en **Toggle activo**
3. El usuario no podrá iniciar sesión

---

## 12. Modo de Prueba (Sandbox)

### 12.1 Activar Modo Prueba

1. Ir a **Configuración** > **Modos**
2. Activar **Modo de Prueba**
3. Guardar

### 12.2 Generar Órdenes de Prueba

En la pantalla KDS aparecerá un panel flotante:

1. Clic en el icono de **tubo de ensayo** (esquina)
2. Se abre panel de pruebas
3. Seleccionar pantalla destino
4. Clic en **Generar Orden**

### 12.3 Limpiar Órdenes de Prueba

1. En el panel de pruebas
2. Clic en **Limpiar Órdenes Test**
3. Se eliminan todas las órdenes generadas en modo prueba

---

## 13. Solución de Problemas

### 13.1 La pantalla no muestra órdenes

| Posible causa | Solución |
|---------------|----------|
| Pantalla offline | Verificar conexión de red |
| Sin asignar a cola | Asignar cola en Backoffice |
| Polling detenido | Iniciar polling en Configuración |
| MAXPOINT sin conexión | Verificar datos de conexión |

### 13.2 Las órdenes no se finalizan

| Posible causa | Solución |
|---------------|----------|
| Teclado desconectado | Verificar conexión USB |
| Teclas mal configuradas | Revisar config de teclado |
| Touch deshabilitado | Habilitar en Preferencias |

### 13.3 La pantalla se ve mal

| Posible causa | Solución |
|---------------|----------|
| Resolución incorrecta | Ajustar columnas en Apariencia |
| Navegador antiguo | Actualizar a Chrome 90+ |
| Cache corrupta | Limpiar cache del navegador |

### 13.4 No puedo acceder al Backoffice

| Posible causa | Solución |
|---------------|----------|
| Credenciales incorrectas | Verificar email y contraseña |
| Usuario desactivado | Contactar administrador |
| Token expirado | Cerrar sesión e ingresar de nuevo |

---

## 14. Atajos de Teclado (Resumen)

### Pantalla KDS

| Tecla | Acción |
|-------|--------|
| H | Finalizar orden 1 |
| 3 | Finalizar orden 2 |
| 1 | Finalizar orden 3 |
| F | Finalizar orden 4 |
| J | Finalizar orden 5 |
| I | Siguiente página |
| G | Página anterior |
| C | Deshacer |
| Q | Ir a página 1 |
| T | Ir a última página |
| I+G (3s) | Toggle Standby |

### Backoffice

| Atajo | Acción |
|-------|--------|
| Ctrl+S | Guardar cambios |
| Esc | Cerrar modal |

---

## 15. Glosario

| Término | Definición |
|---------|------------|
| **KDS** | Kitchen Display System - Sistema de pantallas de cocina |
| **Orden** | Pedido de un cliente con sus productos |
| **Pantalla** | Monitor que muestra órdenes en cocina |
| **Cola** | Grupo de pantallas que comparten órdenes |
| **Canal** | Origen del pedido (local, delivery, kiosko) |
| **SLA** | Service Level Agreement - Tiempos objetivo |
| **Standby** | Modo pausa con pantalla negra |
| **Polling** | Consulta periódica a MAXPOINT |
| **Round-Robin** | Distribución equitativa de órdenes |

---

**Documento**: Manual de Usuario
**Sistema**: KDS v2.0 - Kitchen Display System
**Fecha**: Diciembre 2025
