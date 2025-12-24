i<?php
///////////////////////////////////////////////////////////
///////DESARROLLADO POR: Cristhian Castro ////////////////////
///////DESCRIPCION: ///////////////////
///////TABLAS INVOLUCRADAS: Menu_Agrupacion, ////////
////////////////Menu_Agrupacionproducto////////
////////////////Detalle_Orden_Pedido////////
///////////////////Plus, Precio_Plu, Mesas///////////////////
///////FECHA CREACION: 24-03-2014//////////////////////////
///////FECHA ULTIMA MODIFICACION: 31-03-2014////////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro///////////////
///////DECRIPCION ULTIMO CAMBIO: Funciones Gerente///////
/////////////////////////////////////////////////////////// 


include_once"../system/conexion/clase_sql.php";
include_once"../clases/clase_anularOrden.php";
include_once "../clases/clase_webservice.php";

$servicioWebObj=new webservice();
$lc_config   = new menuPedido();	

if(htmlspecialchars(isset($_GET["cuentasAbiertas"])))
{
	$lc_condiciones[0]=htmlspecialchars($_GET["rst_id"]);
	print  $lc_config->fn_consultar("cuentasAbiertas",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["cuentasCerradas"])))
{
	$lc_condiciones[0]=htmlspecialchars($_GET["rst_id"]);
	print  $lc_config->fn_consultar("cuentasCerradas",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["motivoAnulacion"])))
{
	$lc_condiciones[0]=htmlspecialchars($_GET["motivo"]);
	print  $lc_config->fn_consultar("motivoAnulacion",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["anularOrden"])))
{
	$lc_condiciones[0]=htmlspecialchars($_GET["cfac_id"]);
	$lc_condiciones[1]=htmlspecialchars($_GET["mtv_id"]);
	$lc_condiciones[2]=htmlspecialchars($_GET["cfac_observacion"]);
	print  $lc_config->fn_consultar("anularOrden",$lc_condiciones);
}

/*---------------------------------*/

if(htmlspecialchars(isset($_GET["verificarDop"])))
{
	$lc_condiciones[0]=htmlspecialchars($_GET["odp_id"]);
	$lc_condiciones[1]=htmlspecialchars($_GET["dop_id"]);
	print  $lc_config->fn_consultar("verificarDop",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["verificarPlu"])))
{
	$lc_condiciones[0]=htmlspecialchars($_GET["odp_id"]);
	$lc_condiciones[1]=htmlspecialchars($_GET["plu_id"]);
	$lc_condiciones[2]=htmlspecialchars($_GET["dop_cuenta"]);
	print  $lc_config->fn_consultar("verificarPlu",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["verificarCantidad"])))
{
	$lc_condiciones[0]=htmlspecialchars($_GET["odp_id"]);
	$lc_condiciones[1]=htmlspecialchars($_GET["dop_id"]);
	print  $lc_config->fn_consultar("verificarCantidad",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["verificarCantidadPlu"])))
{
	$lc_condiciones[0]=htmlspecialchars($_GET["odp_id"]);
	$lc_condiciones[1]=htmlspecialchars($_GET["plu_id"]);
	$lc_condiciones[2]=htmlspecialchars($_GET["dop_id"]);
	print  $lc_config->fn_consultar("verificarCantidadPlu",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["incrementarPlu"])))
{
	$lc_condiciones[0]=htmlspecialchars($_GET["dop_id"]);
	$lc_condiciones[1]=htmlspecialchars($_GET["odp_id"]);
	$lc_condiciones[2]=htmlspecialchars($_GET["plu_id"]);
	$lc_condiciones[3]=htmlspecialchars($_GET["dop_cantidad"]);
	$lc_condiciones[4]=htmlspecialchars($_GET["old_dop_id"]);
	print  $lc_config->fn_consultar("incrementarPlu",$lc_condiciones);
}

?>