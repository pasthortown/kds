<?php

session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Configuración de Pantalla /////////////////////////////////
///////FECHA CREACION: 21-01-2016 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 23-12-2016 ////////////////
///////USUARIO QUE MODIFICO: Juan Estévez ////////////////////
///////DECRIPCION ULTIMO CAMBIO: Se agrego panel mesa ////////
///////////////////////////////////////////////////////////////////////////////

include_once"../../system/conexion/clase_sql.php"; 
include_once "../../clases/clase_adminrestaurante.php";

$lc_config  = new restaurante();

	$arp_id = $_GET["name"];
	$arp_id = substr($arp_id, 11, strlen($_GET["name"]));
	$lc_condiciones[0] = 2;
	$lc_condiciones[1] = -1;
	$lc_condiciones[2] = 0;
	$lc_condiciones[3] = 0;
	$lc_condiciones[4] = $arp_id;
	$lc_condiciones[5] = 0;
	$lc_condiciones[6] = $_GET["value"];
	print $lc_config->fn_consultar("administrarPisoArea", $lc_condiciones);


?>