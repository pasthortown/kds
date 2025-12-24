<?php
//session_start();
//////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Juan Méndez /////////////////////////
///////DESCRIPCION: Modifica informacion del boton Producto //
///////TABLAS INVOLUCRADAS: Restaurante///////////////////////
///////FECHA CREACION: 17-12-2013/////////////////////////////
///////FECHA ULTIMA MODIFICACION: 09-04-2014//////////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro////////////////
///////DECRIPCION ULTIMO CAMBIO: Tipo de Menu/////////////////
//////////////////////////////////////////////////////////////


include_once"../../system/conexion/clase_sql.php"; 
include_once "../../clases/clase_menuCategoria.php";
 
$lc_config   = new categoria();	

if(isset($_GET["cargarMenus"])){
	$lc_condiciones[0]=$_GET["resultado"];
	$lc_condiciones[1]=$_GET["cdn_id"];
	$lc_condiciones[2]=$_GET["mag_id"];
	$lc_condiciones[3]=$_GET["std_id"];
	print $lc_config->fn_consultar("cargarMenus", $lc_condiciones);
}

if(isset($_GET["cargarCategoria"])){
	$lc_condiciones[0]=$_GET["resultado"];
	$lc_condiciones[1]=$_GET["cdn_id"];
	$lc_condiciones[2]=$_GET["mag_id"];
	$lc_condiciones[3]=$_GET["std_id"];
	print  $lc_config->fn_consultar("cargarCategoria",$lc_condiciones);
}

if(isset($_GET["administrarCategoria"])){
	$lc_condiciones[0] = $_GET["accion"];
	$lc_condiciones[1] = $_GET["mag_id"];
	$lc_condiciones[2] = $_GET["mag_descripcion"];
	$lc_condiciones[3] = $_GET["mag_colortexto"];
	$lc_condiciones[4] = $_GET["mag_color"];
	$lc_condiciones[5] = $_GET["menu_id"];
	$lc_condiciones[6] = $_GET["cdn_id"];
	$lc_condiciones[7] = $_GET["usr_id"];
	$lc_condiciones[8] = $_GET["std_id"];
	print $lc_config->fn_consultar("administrarCategoria",$lc_condiciones);
}

?>