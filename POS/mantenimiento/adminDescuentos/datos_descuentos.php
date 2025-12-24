<?php
include_once("../../system/conexion/clase_sql.php");
include_once("../../clases/clase_seguridades.php");
include_once("../../seguridades/seguridad_niv3.inc");

include_once ("../../clases/clase_admdescuentos.php");
include_once ("../../clases/clase_clasificacion.php");

$descuentosObj=new descuentos();
$clasificacionesObj=new clasificacion();

$datosAplicaDescuento=$descuentosObj->fn_cargar_AplicaDescuento(array("estado"=>1));
$tiposDescuentos=$descuentosObj->fn_cargar_TipoDescuento(array("estado"=>1));
$clasificacionesMenu=$clasificacionesObj->fn_cargar_Clasificaciones(array("estado"=>1));
$productosCadena=$descuentosObj->fn_cargar_Plus_Cadena($_SESSION["cadenaId"]);