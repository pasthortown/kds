<?php
session_start(); 	
//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Jose Fernandez//////////////////////
////////DESCRIPCION: Archivo para manejo de decuentos/////////
///////FECHA CREACION: 123-02-2015/////////////////////////////
////////////////////////////////////////////////////////////// 

include("../system/conexion/clase_sql.php");
include("../clases/clase_descuentos.php");

 	
$lc_facturas = new descuentos();

$restaurante=$_SESSION['rstId'];
$usuario=$_SESSION['usuarioId'];

///////////////////////////Busca descuentos para el restaurante////////////////////////////
if(htmlspecialchars(isset($_GET["buscaDescuentos"]))) 
{ 
	$lc_parametros[0]=$restaurante;
	print $lc_facturas->fn_consultar("buscaDescuentos",$lc_parametros);	
}

///////////////////////////Verifica si existe una forma de pago aplicada a la factura////////////////////////////
if(htmlspecialchars(isset($_GET["validaExisteFormaPago"])))
{ 
	$lc_parametros[0]= htmlspecialchars($_GET['factAevaluar']);
	print $lc_facturas->fn_consultar("validaExisteFormaPago",$lc_parametros);	
}

if(htmlspecialchars(isset($_GET["validaDescuentoPorProducto"])))
{ 
	$lc_parametros[0]= htmlspecialchars($_GET['factA']);
	$lc_parametros[1]= htmlspecialchars($_GET['id_desc']);	
	$lc_parametros[2]= htmlspecialchars($_GET['valorD']);	
	//$lc_parametros[3]= htmlspecialchars($_GET['miniD']);	
	//$lc_parametros[4]= htmlspecialchars($_GET['cantiD']);	
	$lc_parametros[5]= htmlspecialchars($_GET['aplicaD']);	
	//$lc_parametros[6]= htmlspecialchars($_GET['inicioD']);	
	//$lc_parametros[7]= htmlspecialchars($_GET['finD']);	

	print $lc_facturas->fn_consultar("validaDescuentoPorProducto",$lc_parametros);	
}

if(htmlspecialchars(isset($_GET["validacuponYaAplicado"])))
{ 
	$lc_parametros[0]= htmlspecialchars($_GET['factAplicado']);
	print $lc_facturas->fn_consultar("validacuponYaAplicado",$lc_parametros);	
}

if(htmlspecialchars(isset($_GET["validaDescuentoPorCategoria"])))
{ 
	$lc_parametros[0]= htmlspecialchars($_GET['factC']);
	$lc_parametros[1]= htmlspecialchars($_GET['id_descC']);	
	$lc_parametros[2]= htmlspecialchars($_GET['valorC']);		
	$lc_parametros[5]= htmlspecialchars($_GET['aplicaC']);	

	print $lc_facturas->fn_consultar("validaDescuentoPorCategoria",$lc_parametros);	
}

if(htmlspecialchars(isset($_GET["insertaDescuento"])))
{ 
	$lc_parametros[0]= htmlspecialchars($_GET['id_descF']);
	$lc_parametros[1]= htmlspecialchars($_GET['valorDeDescuento']);	
	$lc_parametros[2]=$usuario;
	$lc_parametros[3]= htmlspecialchars($_GET['factF']);		
	print $lc_facturas->fn_consultar("insertaDescuento",$lc_parametros);	
}

if(htmlspecialchars(isset($_GET["consultaDescuentos"])))
{ 
	$lc_parametros[0]= htmlspecialchars($_GET['factDescu']);		
	print $lc_facturas->fn_consultar("consultaDescuentos",$lc_parametros);	
}


if(htmlspecialchars(isset($_GET["valida_seguridad_usuario"])))
{ 
	$lc_parametros[0]= htmlspecialchars($_GET['idDescuento']);
	print $lc_facturas->fn_consultar("valida_seguridad_usuario",$lc_parametros);	
}


if(htmlspecialchars(isset($_GET["validarUsuario"])))
{
	$lc_parametros[0]=$restaurante;
	$lc_parametros[1]= htmlspecialchars($_GET["usr_clave"]);
	print  $lc_facturas->fn_consultar("validarUsuario",$lc_parametros);
}

if(htmlspecialchars(isset($_GET["insertaDescuentoEntradaManual"])))
{
	$lc_parametros[0]= htmlspecialchars($_GET['id_des']);
	$lc_parametros[1]= htmlspecialchars($_GET['valorDesscuento']);	
	$lc_parametros[2]=$usuario;
	$lc_parametros[3]= htmlspecialchars($_GET['factCabecera']);		
	print  $lc_facturas->fn_consultar("insertaDescuentoEntradaManual",$lc_parametros);
}


if(htmlspecialchars(isset($_GET["muestraTotalesConDescuento"])))
{
	$lc_parametros[0]= htmlspecialchars($_GET['cfac_idd']);	
	print  $lc_facturas->fn_consultar("muestraTotalesConDescuento",$lc_parametros);
}

if(htmlspecialchars(isset($_GET["muestraTipoCuenta"])))
{	
	print  $lc_facturas->fn_consultar("muestraTipoCuenta",'');
}

?>


