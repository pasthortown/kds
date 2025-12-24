<?php

session_start();

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_fidelizacionCadena.php";
include_once "../../clases/clase_fidelizacionRestaurante.php";
include_once "../../clases/clase_fidelizacionProducto.php";

function specialChars($a) {
    return htmlspecialchars($a, ENT_QUOTES, 'UTF-8');
}

$request = (object) (array_map('specialChars', $_POST));

$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];
$rst_id = $_SESSION['rstId'];
if ($request->metodo === "cargarConfiguracionProductos") {
    $result = new Producto();
    print $result->cargarConfiguracionProductos($idCadena);
} else if ($request->metodo === "cargarConfiguracionRestaurante") {
    $result = new Restaurante();
    print $result->cargarConfiguracionRestaurante($idCadena);
} else if ($request->metodo === "guardarConfiguracionProductos") {
    $result = new Producto();
    $idProducto = $request->idProducto;
    $puntos = $request->puntos;
    $aplicaPlan = $request->aplicaPlan;
    $descripcion = utf8_decode($request->descripcion);
    print $result->guardarConfiguracionProductos($idUsuario, $idCadena, $idProducto, $descripcion, $puntos, $aplicaPlan);
} else if ($request->metodo === "guardarConfiguracionRestaurante") {
    $result = new Restaurante();
    $idRestaurante = $request->idRestaurante;
    $latitud = $request->latitud;
    $longitud = $request->longitud;
    $aplicaPlan = $request->aplicaPlan;
    $imprimePuntosRide = $request->ImprimePuntosRide;
    print $result->guardarConfiguracionRestaurante($idUsuario, $idCadena, $idRestaurante, $latitud, $longitud, $aplicaPlan, $imprimePuntosRide);
} else if ($request->metodo === "guardarConfiguracionCadena") {
    $result = new Cadena();
    $parametro = $request->parametro;
    $valor = utf8_decode($request->valor);
    print $result->guardarConfiguracionPoliticas($idUsuario, $idCadena, $parametro, $valor);
} else if ($request->metodo === "cargarConfiguracionCadena") {
    $result = new Cadena();
    print $result->cargarConfiguracionPoliticas($rst_id);
} else if ($request->metodo === "cargarConfiguracionAplicaCadena") {
    $result = new Cadena();
    print $result->guardarConfiguracionPoliticaAplicaCadena($idCadena);
} else if ($request->metodo === "desactivarModuloFidelizacion") {
    $result = new Cadena();
    print $result->desactivarPlanFidelizacion($idCadena, $idUsuario);
} else if ($request->metodo === "activarModuloFidelizacion") {
    $result = new Cadena();
    print $result->activarPlanFidelizacion($idCadena, $idUsuario);
} else if ($request->metodo === "cargarConfiguracionFormaPago") {
    $result = new Cadena();
    print $result->cargarConfiguracionFormaPago($rst_id);
} else if ($request->metodo === "guardarConfiguracionFormaPago") {
    $result = new Cadena();
    $idCadena = $_SESSION['cadenaId'];
    $idUsuario = $_SESSION['usuarioId'];
    $idFormaPago = $request->idFormaPago;
    $estado = $request->estado;
    print $result->guardarConfiguracionFormaPago($idFormaPago , $idUsuario , $idCadena, $estado);
} else if ($request->metodo === "cargarListaRestaurantes") {
    $result = new Restaurante();
    print $result->cargarListaRestaurantes($idCadena);
}