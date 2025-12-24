<?php
include_once("parametros.php");
include_once("../../system/conexion/clase_sql.php");
include_once("../../seguridades/seguridad_niv3.inc");
include_once ("../../clases/clase_adminPromociones.php");
include_once ("../../clases/clase_admdescuentos.php");


$promocionesObj=new configuracionPromociones();
$descuentosObj=new configuracionPromociones();


$productosCadena=$promocionesObj->fn_cargar_Plus_Cadena($_SESSION["cadenaId"]);
$restaurantes=$promocionesObj->fn_cargar_RestauranteP($_SESSION["cadenaId"]);
$descuentos = $descuentosObj->fn_consultarListaDescuentosP($_SESSION["cadenaId"]);
?>
