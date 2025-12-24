<?php

session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_administracionCadena.php';

$administracionCadena = new AdministracionCadena();

$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];

function specialChars($a) {
    return htmlspecialchars($a, ENT_QUOTES, 'UTF-8');
}

$request = (object) (array_map('specialChars', $_POST));

if(isset($request->metodo)) {
    //Cargar claves por semanas
    if ($request->metodo === "cargarClavesPorSemanasPorAnio") {
        $anio = $request->anio;
        print $administracionCadena->cargarClavesPorSemana($idCadena, $anio);

    } else if ($request->metodo === "cargarRestaurantes") {
        print $administracionCadena->cargarRestaurantes($idCadena);

    } else if ($request->metodo === "cargarRestaurantesWifi") {
        print $administracionCadena->cargarRestaurantesWifi($idCadena);
        
    } else if ($request->metodo === "guardarRestaurantesWifi") {
        $restaurantes = $request->restaurantes;
        print $administracionCadena->guardarRestaurantesWifi($idCadena, $restaurantes, $idUsuario);
    }

} else if (isset($request->pk)) {
    //Modificar clave de una semana
    if ($request->pk == "modificarClavePorSemana") {
        $fecha = $request->name; //Fecha
        $hasta = $request->endDate; //Hasta
        $clave = $request->value;
        print $administracionCadena->mergeClaveWifiPorSemana($idCadena, $idUsuario, $fecha, $hasta, $clave);
    }
}