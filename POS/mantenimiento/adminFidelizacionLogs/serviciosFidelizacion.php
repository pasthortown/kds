<?php

session_start();

include_once"../../system/conexion/clase_sql.php";
include_once "../../clases/clase_fidelizacionPeriodo.php";

function specialChars($a) {
    return htmlspecialchars($a, ENT_QUOTES, 'UTF-8');
}

$request = (object) (array_map('specialChars', $_POST));

$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];
$idRestaurante = $_SESSION['rstId'];

if ($request->metodo === "cargarLogsPorPeriodo") {
    $result = new Periodo();
    $fechaInicio = $request->fechaInicio;
    $fechaFin = $request->fechaFin;
    print $result->cargarLogs($idRestaurante, $fechaInicio, $fechaFin);
}
