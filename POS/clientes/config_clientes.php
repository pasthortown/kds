<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Archivo de Configuración para////////////
/////////////////////Clientes/////////////////////////////////
///////TABLAS INVOLUCRADAS://///////////////////////////////// 
///////FECHA CREACION: 19-02-2014/////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 

include"../system/conexion/clase_sql.php";
include"../clases/clase_clientes.php";
 
$lc_cliente = new cliente();

///////////////////////////CIUDADES/////////////////////////////////
if (htmlspecialchars(isset($_GET["cargarCiudad"]))) {
	$lc_datos[0]='';
	print  $lc_cliente->fn_consultar("cargarCiudad",$lc_datos);

///////////////////////////CLIENTE REPETIDO///////////////////////////
} else if (htmlspecialchars(isset($_GET["clienteRepetido"]))) {
	$lc_datos[0]=htmlspecialchars($_GET["clienteCedulaRepetido"]);
	print  $lc_cliente->fn_consultar("clienteRepetido",$lc_datos);

///////////////////////////BUSCAR CLIENTES/////////////////////////
} else if(htmlspecialchars(isset($_GET["clienteCedula"]))) {
	$lc_datos[0]=htmlspecialchars($_GET["clienteCedula"]);
	print  $lc_cliente->fn_consultar("clienteBuscar",$lc_datos);

////////////////////////////BUSCAR CLIENTE - PREDICTIVO//////////////
} else if(htmlspecialchars(isset($_GET["autoCompletar"]))){
	$lc_datos[0]=htmlspecialchars($_GET["term"]);
	print  $lc_cliente->fn_consultar("buscadorPredictivo",$lc_datos);

///////////////////////////NUEVO CLIENTE///////////////////////////
} else if(htmlspecialchars(isset($_GET["nuevoCliente"]))){  
 	$lc_datos[0]=htmlspecialchars($_GET["clienteTipoDoc"]);
	$lc_datos[1]=htmlspecialchars($_GET["clienteCiudad"]);
	$lc_datos[2]=htmlspecialchars(strtoupper($_GET["clienteNombre"]));
	$lc_datos[3]=htmlspecialchars(strtoupper($_GET["clienteApellido"]));
	$lc_datos[4]=htmlspecialchars($_GET["clienteCedula"]);
	$lc_datos[5]=htmlspecialchars($_GET["clienteFono"]);
	$lc_datos[6]=htmlspecialchars(strtoupper($_GET["clienteDireccion"]));
	$lc_datos[7]=strtolower($_GET["clienteCorreo"]);
	
	print $lc_cliente->fn_ejecutar("nuevoCliente",$lc_datos);    

///////////////////////////ACTUALIZAR CLIENTE///////////////////////
} else if (htmlspecialchars(isset($_GET["actualizarCliente"]))){  
 	$lc_datos[0]=htmlspecialchars($_GET["clienteTipoDoc"]);
	$lc_datos[1]=htmlspecialchars($_GET["clienteCiudad"]);
	$lc_datos[2]=htmlspecialchars(strtoupper($_GET["clienteNombre"]));
	$lc_datos[3]=htmlspecialchars(strtoupper($_GET["clienteApellido"]));
	$lc_datos[4]=htmlspecialchars($_GET["clienteCedula"]);
	$lc_datos[5]=htmlspecialchars($_GET["clienteFono"]);
	$lc_datos[6]=htmlspecialchars(strtoupper($_GET["clienteDireccion"]));
	$lc_datos[7]=strtolower($_GET["clienteCorreo"]);
	
	print $lc_cliente->fn_ejecutar("actualizarCliente",$lc_datos);
}