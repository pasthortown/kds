<?php

session_start();

include_once"../../system/conexion/clase_sql.php";
include_once "../../clases/clase_fidelizacionMonitor.php";

function specialChars($a) {
    return htmlspecialchars($a, ENT_QUOTES, 'UTF-8');
}

$request = (object) (array_map('specialChars', $_POST));

$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];
$idRestaurante = $_SESSION['rstId'];

if($request->metodo === "cargarTotalVentas") {
    $result = new Monitor();
    //Formato Fechas [YYYYMMdd]
    $fechaDesde = $request->fechaDesde;
    $fechaHasta = $request->fechaHasta;
    print $result->cargarTotalVentas($idRestaurante, $fechaDesde, $fechaHasta);
} else if($request->metodo === "cargarTopDiezProductosRedimidos") {
    $result = new Monitor();
    //Formato Fechas [YYYYMMdd]
    $fechaDesde = $request->fechaDesde;
    $fechaHasta = $request->fechaHasta;
    print $result->cargarTopDiezProductosRedimidos($idRestaurante, $fechaDesde, $fechaHasta);
} else if($request->metodo === "cargarFormasPagoHora") {
    $result = new Monitor();
    //Formato Fechas [YYYYMMdd]
    $fechaDesde = $request->fechaDesde;
    $fechaHasta = $request->fechaHasta;
    print $result->cargarFormasPagoHora($idRestaurante, $fechaDesde, $fechaHasta);
}

//if($request->metodo === "cargarPeriodosRestaurante") {
//    $result = new Periodo();
//    print $result->cargarPeriodosRestaurante($idCadena, $idRestaurante, $idUsuario);
//} else if($request->metodo === "cargarFacturasErrorPorPeriodo") {
//    $result = new Periodo();
//    $idPeriodo = $request->idPeriodo;
//    print $result->cargarFacturasErrorPorPeriodo($idRestaurante, $idPeriodo);
//}

