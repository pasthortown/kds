<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}

session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_reportes.php';

$reporte = new Reporte();
if($_POST){
    $request = (object) $_POST;
}else{
    $request = (object) $_GET;
}

$idCadena = $_SESSION['cadenaId'];
$usuario = $_SESSION['usuarioId'];
$idRestaurante= $_SESSION['rstId'];
//return para react
if ($request->metodo === "cargarMenuReportes_react") {
    print $reporte->cargarMenuReportes($idCadena,'tienda',$idRestaurante);
}





$ambiente = array();

try {
    $configuraciones = parse_ini_file('../../system/conexion/replica.ini', true);
    $ambiente['tipoambiente'] = $configuraciones['tipoambiente']['db.config.tipoambiente'];
}
catch (Exception $e)
{
    $ambiente['tipoambiente'] = "0";
}

//Cargar Categorias de Reportes
if ($request->metodo === "cargarMenuReportes") {
    print $reporte->cargarMenuReportes($idCadena,$ambiente['tipoambiente'],$idRestaurante);
//Cargar Parametros de Reporte
} else if ($request->metodo === "cargarParametrosReporte") {
    print $reporte->cargarParametrosReporte($request->idReporte);
//Cargar Select Integracion Parametros
} else if ($request->metodo === "cargarIntegracionParametros") {
    print $reporte->cargarIntegracionParametros($request->idParametro);
}

