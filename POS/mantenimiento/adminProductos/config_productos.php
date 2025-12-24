<?php

session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_productos.php';

$producto = new Producto();

$idCadena = $_SESSION['cadenaId'];
$usuario = $_SESSION['usuarioId'];

function specialChars($a) {
    return htmlspecialchars($a, ENT_QUOTES,'UTF-8');
}

$request = (object) ($_POST);

//Cargar Canales de Despacho (Clasificacion)
if ($request->metodo === "cargarCanalesClasificacion") {
    print $producto->cargarClasificaciones($idCadena);
//Cargar Plus por Clasificacion
} else if ($request->metodo === "cargarPlusPorClasificacion") {
    print $producto->cargarProductosPorClasificacion($idCadena, $request->idClasificacion);
//Cargar Plus por Cadena
} else if ($request->metodo === "cargarPlusPorCadena") {
    print $producto->cargarProductosPorCadena($idCadena);
//Cargar Configuración de Producto
} else if ($request->metodo === "cargarConfiguracionProducto") {
    print $producto->cargarConfiguracionProducto($idCadena, $request->idProducto);
//Cargar Tipos de Productos
} else if ($request->metodo === "cargarTiposProductos") {
    print $producto->cargarTiposProducto($idCadena);
//Cargar Impuestos por Pais
} else if ($request->metodo === "cargarImpuestos") {
    print $producto->cargarImpuestos($idCadena);
//Cargar Categorias por Cadena
} else if ($request->metodo === "cargarCategoriasPorCadena") {
    print $producto->cargarCategoriasPorCadena($idCadena);
//Cargar Precios por Categoria por Producto
} else if ($request->metodo === "cargarPrecioPorCategoriasPorPlu") {
    print $producto->cargarPreciosPorCategoriasPorProducto($idCadena, $request->idProducto);
//Cargar Canales de Impresión por Cadena
} else if ($request->metodo === "cargarCanalesImpresionPorCadena") {
    print $producto->cargarCanalImpresionPorCadena($idCadena);
//Cargar Canales de Impresión por Producto
} else if ($request->metodo === "cargarCanalesImpresionPorProducto") {
    print $producto->cargarCanalImpresionPorProducto($request->idProducto);
//Cargar MasterPlus
} else if ($request->metodo === "cargarMasterPlus") {
    print $producto->cargarMasterPlus($idCadena, $request->parametro);
//Cargar Preguntas Sugeridas por Producto
} else if ($request->metodo === "cargarPreguntasSueridasPorProducto") {
    print $producto->cargarPreguntasSueridasPorProducto($idCadena, $request->idProducto);
//Cargar Preguntas Sugeridas por Cadena
} else if ($request->metodo === "cargarPreguntasSueridasPorCadena") {
    print $producto->cargarPreguntasSueridasPorCadena($idCadena);
} else if ($request->metodo === "cargarColeccionDepartamentos") {
    print $producto->cargarDepartamentosPorCadena($idCadena);
} else if ($request->metodo === "cargarPlusColeccionDeDatos") {
    print $producto->cargarPlusColeccionDeDatos($idCadena, $request->idProducto);
} else if ($request->metodo === "editarColeccionPlu") {
    print $producto->editarColeccionPlu($idCadena, $request->idColeccionPlus, $request->idColeccionDeDatosPlus, $request->idPlu, $request->varchar, $request->entero, $request->fecha, $request->seleccion, $request->numerico, $request->fechaIni, $request->fechaFin, $request->min, $request->max, $request->estado, $usuario);
} else if ($request->metodo === "cargarPluColeccionDescripcion") {
    print $producto->cargarPluColeccionDescripcion($idCadena);
} else if ($request->metodo === "cargarPluColeccionDatos") {
    print $producto->cargarPluColeccionDatos($request->idColeccionPlus);
} else if ($request->metodo === "guardarNuevaColeccionPlus") {
    print $producto->guardarNuevaColeccionPlus($idCadena, $request->idColeccionDatosPlus, $request->idColeccionPlus, $request->idPlu, $request->varchar, $request->entero, $request->fecha, $request->seleccion, $request->numerico, $request->fechaIni, $request->fechaFin, $request->min, $request->max, $usuario);
//Cargar lista de Modificadores
} else if ($request->metodo === "cargarListaModificadores"){
    print $producto->cargarListaModificadores($request->accion);
}