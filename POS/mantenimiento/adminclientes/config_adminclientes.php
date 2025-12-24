<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: CREACION DE CLIENTES  //////////////////////////////////////////////////////////
////////////////TABLAS: Cliente ////////////////////////////////////////////////////////////////////////
////////FECHA CREACION: 23/08/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

include_once"../../system/conexion/clase_sql.php"; 
include_once "../../clases/clase_adminclientes.php";
 
$lc_config   = new clientes();	
$lc_cadena	 = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];
$lc_restaurante = $_SESSION['rstId'];

if(htmlspecialchars(isset($_GET["cargarClientes"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = $lc_restaurante;
	$lc_condiciones[2] = $lc_cadena;
	$lc_condiciones[3] = 0;
	print $lc_config->fn_consultar("cargarClientes", $lc_condiciones);
}

if(htmlspecialchars(isset($_GET["traerTipoDocumento"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = $lc_restaurante;
	$lc_condiciones[2] = $lc_cadena;
	$lc_condiciones[3] = 0;
	print $lc_config->fn_consultar("traerTipoDocumento", $lc_condiciones);
}

if(htmlspecialchars(isset($_GET["traerCiudad"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = $lc_restaurante;
	$lc_condiciones[2] = $lc_cadena;
	$lc_condiciones[3] = 0;
	print $lc_config->fn_consultar("traerCiudad", $lc_condiciones);
}

if(htmlspecialchars(isset($_GET["guardarClienteFormasPago"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = $lc_usuario;
	$lc_condiciones[2] = $lc_restaurante;
	$lc_condiciones[3] = $lc_cadena;
	$lc_condiciones[4] = htmlspecialchars($_GET["sel_tipodocumento"]);
	$lc_condiciones[5] = htmlspecialchars($_GET["sel_ciudad"]);
	$lc_condiciones[6] = htmlspecialchars($_GET["cli_nombres"]);
	$lc_condiciones[7] = htmlspecialchars($_GET["cli_apellidos"]);
	$lc_condiciones[8] = htmlspecialchars($_GET["cli_documento"]);
	$lc_condiciones[9] = htmlspecialchars($_GET["cli_telefono"]);
	$lc_condiciones[10] = htmlspecialchars($_GET["cli_direccion"]);
	$lc_condiciones[11] = htmlspecialchars($_GET["cli_email"]);
	$lc_condiciones[12] = 0;
	$lc_condiciones[13] = 0;
	
	print $lc_config->fn_consultar("guardarClienteFormasPago", $lc_condiciones);
}

if(htmlspecialchars(isset($_GET["traerCliente"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = $lc_restaurante;
	$lc_condiciones[2] = $lc_cadena;
	$lc_condiciones[3] = htmlspecialchars($_GET["cli_id"]);
	print $lc_config->fn_consultar("traerCliente", $lc_condiciones);
}

if(htmlspecialchars(isset($_GET["guardarClienteModifica"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = $lc_usuario;
	$lc_condiciones[2] = $lc_restaurante;
	$lc_condiciones[3] = $lc_cadena;
	$lc_condiciones[4] = htmlspecialchars($_GET["sel_tipodocumento"]);
	$lc_condiciones[5] = htmlspecialchars($_GET["sel_ciudad"]);
	$lc_condiciones[6] = htmlspecialchars($_GET["cli_nombres"]);
	$lc_condiciones[7] = htmlspecialchars($_GET["cli_apellidos"]);
	$lc_condiciones[8] = htmlspecialchars($_GET["cli_documento"]);
	$lc_condiciones[9] = htmlspecialchars($_GET["cli_telefono"]);
	$lc_condiciones[10] = htmlspecialchars($_GET["cli_direccion"]);
	$lc_condiciones[11] = htmlspecialchars($_GET["cli_email"]);
	$lc_condiciones[12] = 0;
	$lc_condiciones[13] = htmlspecialchars($_GET["cli_id"]);
	
	print $lc_config->fn_consultar("guardarClienteModifica", $lc_condiciones);
}

?>