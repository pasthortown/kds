# Manual de Usuario - Configuracion de Apariencia

Este manual describe todas las opciones disponibles en el modulo de **Apariencia** del Backoffice del sistema KDS.

## Indice

1. [Acceso al Modulo](#acceso-al-modulo)
2. [Seleccion de Pantalla](#seleccion-de-pantalla)
3. [Colores Generales](#colores-generales)
4. [Configuracion de Tipografias](#configuracion-de-tipografias)
5. [Disposicion de Columnas](#disposicion-de-columnas)
6. [Opciones Generales](#opciones-generales)
7. [Colores de Canales](#colores-de-canales)
8. [Colores SLA (Tiempo de Espera)](#colores-sla-tiempo-de-espera)
9. [Exportar e Importar Configuracion](#exportar-e-importar-configuracion)
10. [Vista Previa en Tiempo Real](#vista-previa-en-tiempo-real)

---

## Acceso al Modulo

1. Ingresar al Backoffice (puerto 8081 por defecto)
2. Iniciar sesion con credenciales de administrador
3. En el menu lateral, seleccionar **Apariencia**

---

## Seleccion de Pantalla

Cada pantalla del sistema KDS puede tener su propia configuracion de apariencia.

- Usar el selector **"Seleccionar pantalla"** en la parte superior
- Los cambios se guardan de forma independiente para cada pantalla
- El boton **Recargar** actualiza la configuracion desde el servidor
- El boton **Por defecto** restaura todos los valores a su configuracion inicial
- El boton **Guardar** persiste los cambios en la base de datos

### Copiar Configuracion

Es posible copiar la configuracion completa de apariencia desde otra pantalla:

1. Click en el boton **"Copiar / Exportar"**
2. Seleccionar **"Copiar de otra pantalla"**
3. Elegir la pantalla origen
4. Se copiaran: apariencia, colores SLA y colores de canal

---

## Colores Generales

Estos colores definen la estetica base de las pantallas KDS:

| Campo | Descripcion |
|-------|-------------|
| **Fondo Pantalla** | Color de fondo general de la pantalla (area detras de las tarjetas) |
| **Fondo Tarjetas** | Color de fondo de cada tarjeta de orden |
| **Texto General** | Color del texto por defecto en las tarjetas |
| **Color Acento** | Color para elementos destacados y botones |

### Recomendaciones

- Para ambientes oscuros (cocinas con poca luz): usar fondo oscuro (#000000 o #1a1a2e)
- Para ambientes claros: usar fondo claro (#f0f2f5 o #ffffff)
- Mantener buen contraste entre fondo y texto para legibilidad

---

## Configuracion de Tipografias

El sistema permite configurar la tipografia de cada elemento de las tarjetas de orden de forma independiente:

### Elementos Configurables

| Elemento | Ejemplo | Descripcion |
|----------|---------|-------------|
| **Cabecera** | `Orden #123` | Numero y encabezado de la orden |
| **Timer** | `02:45` | Tiempo transcurrido desde que se creo la orden |
| **Cliente** | `CONSUMIDOR FINAL` | Nombre del cliente |
| **Cantidad** | `5x` | Cantidad de cada producto |
| **Producto** | `SUPER COMBO 2` | Nombre del producto principal |
| **Subproductos** | `1x Pepsi, 1x Crispy` | Items incluidos en un combo |
| **Modificadores** | `* 10x PRESAS, 5x ARROZ` | Personalizaciones del producto |
| **Notas** | `* nota del cliente` | Instrucciones especiales |
| **Comentarios** | `comentarios del producto` | Comentarios adicionales |
| **Canal** | `KIOSKO-EFECTIVO` | Origen de la orden |

### Propiedades de Cada Elemento

Para cada elemento se puede configurar:

| Propiedad | Opciones | Descripcion |
|-----------|----------|-------------|
| **Fuente** | Inter, Roboto, Arial, Helvetica, Monospace, etc. | Familia tipografica |
| **Tamano** | Extra Pequeno, Pequeno, Mediano, Grande, Extra Grande, Muy Grande | Tamano del texto |
| **Peso** | Normal (400), Medio (500), Semi-Bold (600), Bold (700) | Grosor del texto |
| **Estilo** | Normal, Cursiva | Estilo del texto |
| **Color** | Selector de color | Color del texto |
| **Visible** | Si/No | Mostrar u ocultar el elemento |

### Propiedades Adicionales

Algunos elementos tienen propiedades adicionales:

- **Fondo**: Color de fondo del elemento (Cabecera, Cliente, Producto, Subitems, Modificadores, Notas, Comentarios)
- **Indentacion**: Margen izquierdo en pixeles (Subitems, Modificadores, Notas, Comentarios)
- **Mayusculas**: Convertir texto a mayusculas (Producto, Canal)

### Caso Especial: Color de Cantidad

Si el color de la **Cantidad** se deja vacio, automaticamente usara el **color SLA** correspondiente al tiempo de espera de la orden. Esto permite que el color de la cantidad cambie dinamicamente segun el tiempo.

---

## Disposicion de Columnas

| Campo | Descripcion |
|-------|-------------|
| **Columnas** | Numero de ordenes que se muestran por fila (1-8) |

Ejemplos:
- **4 columnas**: 4 ordenes por fila (recomendado para pantallas grandes)
- **3 columnas**: 3 ordenes por fila (ordenes mas anchas)
- **2 columnas**: 2 ordenes por fila (para pantallas pequenas)

---

## Opciones Generales

| Opcion | Descripcion |
|--------|-------------|
| **Animaciones** | Habilita transiciones suaves al agregar/remover ordenes |
| **Dividir Ordenes Largas** | Cuando una orden tiene mas items de los que caben en una columna, se divide automaticamente en multiples tarjetas |

---

## Colores de Canales

Los canales representan el origen de cada orden (Local, Kiosko, PedidosYa, RAPPI, etc.).

### Gestion de Canales

1. **Nuevo Canal**: Crear un canal personalizado
2. **Canales por Defecto**: Crear los canales predefinidos del sistema
3. **Editar**: Modificar nombre, colores y prioridad de un canal
4. **Eliminar**: Remover un canal del sistema

### Propiedades de Canal

| Propiedad | Descripcion |
|-----------|-------------|
| **Nombre** | Identificador unico del canal (ej: "PedidosYa") |
| **Color de Fondo** | Color que se mostrara en el footer de las ordenes de este canal |
| **Color de Texto** | Color del texto sobre el fondo del canal |
| **Prioridad** | Orden de importancia (mayor numero = mayor prioridad) |
| **Activo** | Si el canal esta habilitado |

### Canales Predefinidos

| Canal | Color | Descripcion |
|-------|-------|-------------|
| Local | Verde (#7ed321) | Ordenes en el local |
| Kiosko-Efectivo | Azul (#0299d0) | Autoservicio pago efectivo |
| Kiosko-Tarjeta | Rojo (#d0021b) | Autoservicio pago tarjeta |
| PedidosYa | Rojo (#d0021b) | Delivery PedidosYa |
| RAPPI | Naranja (#ff5a00) | Delivery Rappi |
| UberEats | Verde (#06c167) | Delivery UberEats |
| Glovo | Amarillo (#ffc244) | Delivery Glovo |
| Drive | Morado (#9b59b6) | AutoServicio Drive |
| Delivery | Rojo (#e74c3c) | Delivery general |

---

## Colores SLA (Tiempo de Espera)

Los colores SLA (Service Level Agreement) cambian automaticamente segun el tiempo transcurrido desde que se creo la orden.

### Configuracion Tipica

| Orden | Tiempo | Color | Descripcion |
|-------|--------|-------|-------------|
| 1 | 01:00 | Verde (#3e961f) | Orden reciente, todo bien |
| 2 | 02:00 | Amarillo (#9b9728) | Orden en progreso normal |
| 3 | 03:00 | Rojo (#cf1d09) | Orden retrasada, atencion urgente |

### Propiedades de Color SLA

| Propiedad | Descripcion |
|-----------|-------------|
| **Color** | Color del timer y cabecera |
| **Color Cantidad** | Color especifico para las cantidades (opcional) |
| **Minutos** | Tiempo limite en formato MM:SS |
| **Fondo Completo** | Si es true, todo el fondo de la tarjeta cambia a este color |

### Funcionamiento

1. Si una orden tiene menos de 1 minuto, se muestra en **verde**
2. Si tiene entre 1 y 2 minutos, se muestra en **amarillo**
3. Si tiene mas de 2 minutos, se muestra en **rojo**
4. Con "Fondo Completo" activado, toda la tarjeta cambia de color para mayor visibilidad

---

## Exportar e Importar Configuracion

### Exportar

1. Click en **"Copiar / Exportar"**
2. Seleccionar **"Exportar a JSON"**
3. Se descargara un archivo JSON con:
   - Configuracion de apariencia completa
   - Colores SLA
   - Colores de canal

### Importar

1. Click en **"Copiar / Exportar"**
2. Seleccionar **"Importar desde JSON"**
3. Subir archivo o pegar contenido JSON
4. Click en **"Importar"**

Esto es util para:
- Respaldo de configuracion
- Migrar configuracion entre ambientes
- Compartir estilos entre restaurantes

---

## Vista Previa en Tiempo Real

En el panel derecho de la pantalla se muestra una **Vista Previa** que:

- Muestra como se veran las ordenes con la configuracion actual
- Se actualiza en tiempo real al modificar cualquier valor
- Si hay ordenes reales en el sistema (via Mirror), las muestra

### Ordenes de Ejemplo

Si no hay ordenes reales, se muestran ordenes de ejemplo para previsualizar:
- Diferentes canales
- Productos con subitems
- Modificadores y notas
- Diferentes tiempos de espera

---

## Mejores Practicas

1. **Consistencia**: Mantener la misma configuracion en todas las pantallas de produccion
2. **Contraste**: Asegurar que el texto sea legible sobre el fondo
3. **Prioridad Visual**: Usar colores llamativos para elementos criticos (cantidad, timer)
4. **Testing**: Probar la configuracion con ordenes reales antes de desplegar
5. **Respaldo**: Exportar la configuracion antes de hacer cambios importantes

---

## Solucion de Problemas

### Los colores no se guardan

- Verificar que se presiono el boton **Guardar**
- Recargar la pagina y verificar si los cambios persisten

### El preview no muestra ordenes reales

- Verificar que el servicio Mirror este conectado
- Las ordenes deben existir en el sistema para mostrarse

### Los colores de canal no aparecen

- Verificar que el canal existe en la lista de canales globales
- Ejecutar "Canales por Defecto" para crear los canales predefinidos

---

## Referencia Rapida de Campos

| Seccion | Campos Principales |
|---------|-------------------|
| Colores Generales | backgroundColor, cardColor, textColor, accentColor |
| Cabecera | headerFont*, showHeader, showOrderNumber |
| Timer | timerFont*, showTimer |
| Cliente | clientFont*, showClient |
| Cantidad | quantityFont*, showQuantity |
| Producto | productFont*, productUppercase |
| Subitems | subitemFont*, subitemIndent, showSubitems |
| Modificadores | modifierFont*, modifierIndent, showModifiers |
| Notas | notesFont*, notesIndent, showNotes |
| Comentarios | commentsFont*, commentsIndent, showComments |
| Canal | channelFont*, channelUppercase, showChannel |
