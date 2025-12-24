<?php

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_adminreportes.php';

$ambiente = array();

try {
    $configuraciones = parse_ini_file('../../system/conexion/replica.ini', true);
    $ambiente['tipoambiente'] = $configuraciones['tipoambiente']['db.config.tipoambiente'];
    } 
catch (Exception $e) 
    {
    $ambiente['tipoambiente'] = "0";
    }
//print( $ambiente[0]) ;

$reporte = new Reporte();

$request = (object) $_POST;
$idCadena = $_SESSION['cadenaId'];
$idRestaurante= $_SESSION['rstId'];

//Cargar todas las categorías
if ($request->metodo === "cargarCategorias") {
    print $reporte->cargarCategorias($idCadena,$ambiente['tipoambiente'],$idRestaurante);
//Cargar una categoría
} else if ($request->metodo === "cargarCategoria") {
    print $reporte->cargarCategoria($request->idCategoria, $idCadena);
//muestra el combo de las rutas de la carpeta para los reportes
} else if ($request->metodo === "cargaComboRutaCarpeta") {
    print $reporte->cargarComboRutaCarpetaReportes( $idCadena);
//Guardar o modificar una categoría
} else if ($request->metodo === "guardarCategoria") {
    print $reporte->guardarCategoria($request->accion, $request->idCategoria, $request->descripcion, $request->estado, $idCadena, $request->opcionRuta,$ambiente['tipoambiente'],$idRestaurante);
//Cargar los reportes de una categoría
} else if ($request->metodo === "cargarReportes") {
    print $reporte->cargarReportes($request->idCategoria, $idCadena);
//Guardar o modificar un reporte
} else if ($request->metodo === "guardarReporte") {
    print $reporte->guardarReporte($request->accion, $request->idReporte, $request->label, $request->descripcion, $request->url, $request->estado, $request->idCategoria, $idCadena,$ambiente['tipoambiente'],$idRestaurante);
//Cargar los parámetros de un reporte
} else if ($request->metodo === "cargarParametros") {
    print $reporte->cargarParametros($request->idReporte, $idCadena);
//Cargar las variables de sesión
} else if ($request->metodo === "cargarVariablesSesion") {
    print $reporte->cargarVariablesSesion($idCadena);
//Cargar los tipos de dato
} else if ($request->metodo === "cargarTiposDato") {
    print $reporte->cargarTiposDato($idCadena);
//Guardar o modificar un parámetro
} else if ($request->metodo === "guardarParametro") {
    print $reporte->guardarParametro($request->accion, $request->idParametro, $request->etiqueta, $request->variable, $request->tipoDato, $request->obligatorio, $request->tablaIntegracion, $request->columnaIntegracion,  $request->query, $request->orden, $request->estado, $request->idReporte, $idCadena,$ambiente['tipoambiente'],$idRestaurante);
//Actualizar orden de parámetro
} else if ($request->metodo === "actualizarOrdenParametro") {
    print $reporte->actualizarOrdenParametro($request->html);
//Eliminar categoría
} else if ($request->metodo === "eliminarCategoria") {
    print $reporte->eliminarCategoria($request->accion, $request->idCategoria, $idCadena,$ambiente['tipoambiente'],$idRestaurante);
//Eliminar reporte
} else if ($request->metodo === "eliminarReporte") {
    print $reporte->eliminarReporte($request->accion, $request->idCategoria, $request->idReporte, $idCadena,$ambiente['tipoambiente'],$idRestaurante);
//Eliminar parámetro
} else if ($request->metodo === "eliminarParametro") {
    print $reporte->eliminarParametro($request->accion, $request->idReporte, $request->idParametro, $idCadena,$ambiente['tipoambiente'],$idRestaurante);
}