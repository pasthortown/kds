<?php

include "../../system/conexion/clase_sql.php";
include "clase_statusVersion.php";

$statusVersion = new StatusVersion();

if(isset($_GET['validaLimpiaCache']))
{   
	$ipEstacion = $_GET['ip'];
	$isUrlAdmin = $_GET['isUrlAdmin'];
	$estacionAdmin = $_GET['estacionAdmin'];
	$idEstacion = isset($_SESSION['estacionId']) ? $_SESSION['estacionId'] : '';
	print $statusVersion->getStatusLimpiaCachePendiente($ipEstacion, $idEstacion, $isUrlAdmin, $estacionAdmin);	
}else if(isset($_POST['limpiaCacheEstacion'])){
    $idEstacion = $_POST['idEstacion'];
	print $statusVersion->limpiaCacheEstacion($idEstacion);
}else if(isset($_GET['limpiarDataCache'])){
	print $statusVersion->deleteDataCache();
}else if(isset($_GET['verificaStatusServices'])){
	$ipEstacion = $_GET['ip'];
	$idEstacion = isset($_SESSION['estacionId']) ? $_SESSION['estacionId'] : '';
	print $statusVersion->verificaStatusServices($ipEstacion, $idEstacion);
}
