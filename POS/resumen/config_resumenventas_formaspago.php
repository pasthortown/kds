<?php

session_start();

////////////////////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Juan Méndez ///////////////////////////////////////////////////////
///////DESCRIPCION: ////////////////////////////////////////////////////////////////////////
///////TABLAS INVOLUCRADAS: Menu_Agrupacion, ///////////////////////////////////////////////
////////////////Menu_Agrupacionproducto/////////////////////////////////////////////////////
////////////////Detalle_Orden_Pedido////////////////////////////////////////////////////////
///////////////////Plus, Precio_Plu, Mesas//////////////////////////////////////////////////
///////FECHA CREACION: 20-01-2014///////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 07-05-2014////////////////////////////////////////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro//////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Documentación y validación en Internet Explorer 9+/////////
///////FECHA ULTIMA MODIFICACION: 09/09/2014////////////////////////////////////////////////
///////USUARIO QUE MODIFICO:  Jose Fernandez////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Validacion de productos para///////////////////////////////
/////////////////////////////////Salon y llevar/////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////

include_once"../system/conexion/clase_sql.php";
include_once"../clases/clase_resumenVentas_formaspago.php";

$lc_config = new resumen();	

$cdn_id = $_SESSION['cadenaId'];
$usr_id = $_SESSION['usuarioId'];
$rst_id = $_SESSION['rstId'];
$est_id = $_SESSION['estacionId'];
$usr_nombre = $_SESSION['nombre'];
$usr_usuario = $_SESSION['usuario'];
$prf_id = $_SESSION['perfil'];

if(isset($_GET["cargarConfiguracionResumenVentas"])){
	$lc_condiciones[0]=$rst_id;
	$lc_condiciones[1]=$usr_id;
	print $lc_config->fn_consultar("cargarConfiguracionResumenVentas", $lc_condiciones);
}

if(isset($_GET["cargarResumenVentasFacturas"])){
	$lc_condiciones[0]=$rst_id;
	$lc_condiciones[1]=$usr_id;
	print $lc_config->fn_consultar("cargarResumenVentasFacturas", $lc_condiciones);
}

if(isset($_GET["cargarAccesosPerfil"])){
	$lc_condiciones[0] = $_GET["pnt_id"];
	$lc_condiciones[1] = $usr_id;
	$lc_condiciones[2] = $prf_id;
	print $lc_config->fn_consultar("cargarAccesosPerfil", $lc_condiciones);
}

if(isset($_GET["obtenerMesa"])){
	$lc_condiciones[0]=$rst_id;
	print $lc_config->fn_consultar("obtenerMesa",$lc_condiciones);
}

?>