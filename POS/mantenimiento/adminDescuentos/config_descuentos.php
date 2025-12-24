<?php

session_start();
///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Francisco Sierra ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de descuentos //////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 07-08-2017 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 12/09/2018 //////////////////////////////////
///////USUARIO QUE MODIFICO: Eduardo Valencia /////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Se añadieron funciones para consumir SP del////
//////////////////////////////// módulo de una promoción /////////////////////


include_once("../../system/conexion/clase_sql.php");
include_once ("../../clases/clase_admdescuentos.php");

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {

    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_config = new descuentos();
 
if (htmlspecialchars(isset($_GET["consultarListaDescuentos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["dsct_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
    print $lc_config->fn_consultar("consultarListaDescuentos", $lc_condiciones);
}

if (htmlspecialchars(isset($_POST["cargarDetalleDescuento"]))) {
    $lc_condiciones["resultado"] = htmlspecialchars($_POST["resultado"]);
    $lc_condiciones["dsct_id"] = htmlspecialchars($_POST["dsct_id"]);
    $lc_condiciones["cdn_id"] = htmlspecialchars($_POST["cdn_id"]);
    print $lc_config->fn_consultar("cargarDetalleDescuento", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarPlusDescuento"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["dsct_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["parametro"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["pagina"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["registros"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["cla_id"]);
    print $lc_config->fn_consultar("cargarPlusDescuento", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["pluReportNumber"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["parametro"]);
    print $lc_config->fn_consultar("pluReportNumber", $lc_condiciones);
}

///////////////////////////////////	
//// AÑADIDO PARA PROMOCIONES /////
///////////////////////////////////	
if(htmlspecialchars(isset($_GET["cargarRestaurantesNoAsignadosPromociones"]))){
	$lc_condiciones[1] = htmlspecialchars($_GET["Id_Promociones"]);
	$lc_condiciones[0] = htmlspecialchars($_GET["cdn_id"]);
	print $lc_config->fn_consultar("cargarRestaurantesNoAsignadosPromociones", $lc_condiciones);
}


if (htmlspecialchars(isset($_GET["cargarRestaurantesNoAsignadosDescuentos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["dsct_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["parametro"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["pagina"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["registros"]);
    $lc_condiciones[6] = 0;
    print $lc_config->fn_consultar("cargarRestaurantesNoAsignadosDescuentos", $lc_condiciones);
}


///////////////////////////////////	
//// AÑADIDO PARA PROMOCIONES /////
///////////////////////////////////	
if(htmlspecialchars(isset($_GET["cargarCiudades"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
	$lc_condiciones[1] = htmlspecialchars($_GET["dsct_id"]);
	$lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
	$lc_condiciones[3] = htmlspecialchars($_GET["parametro"]);
	$lc_condiciones[4] = htmlspecialchars($_GET["pagina"]);
	$lc_condiciones[5] = htmlspecialchars($_GET["registros"]);
	$lc_condiciones[6] = 0;
	print $lc_config->fn_consultar("cargarCiudades", $lc_condiciones);
}


///////////////////////////////////	
//// AÑADIDO PARA PROMOCIONES /////
///////////////////////////////////	
if(htmlspecialchars(isset($_GET["cargarBeneficiosPromociones"])))
{

	$lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
	$lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
	$lc_condiciones[3] = htmlspecialchars($_GET["parametro"]);
	$lc_condiciones[4] = htmlspecialchars($_GET["pagina"]);
	$lc_condiciones[5] = htmlspecialchars($_GET["registros"]);
	$lc_condiciones[6] = htmlspecialchars($_GET["cla_id"]);
	print $lc_config->fn_consultar("cargarBeneficiosPromociones", $lc_condiciones);
}


///////////////////////////////////	
//// AÑADIDO PARA PROMOCIONES /////
///////////////////////////////////	
if(htmlspecialchars(isset($_GET["cargarRequeridosPromociones"])))
{
	$lc_condiciones[0] = htmlspecialchars($_GET["Id_Promociones"]);
	print $lc_config->fn_consultar("cargarRequeridosPromociones", $lc_condiciones);
}



///////////////////////////////////	
//// AÑADIDO PARA PROMOCIONES /////
///////////////////////////////////	
if(htmlspecialchars(isset($_GET["cargarRegiones"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
	$lc_condiciones[1] = htmlspecialchars($_GET["dsct_id"]);
	$lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
	$lc_condiciones[3] = htmlspecialchars($_GET["parametro"]);
	$lc_condiciones[4] = htmlspecialchars($_GET["pagina"]);
	$lc_condiciones[5] = htmlspecialchars($_GET["registros"]);
	$lc_condiciones[6] = 0;
	print $lc_config->fn_consultar("cargarRegiones", $lc_condiciones);
}


///////////////////////////////////	
//// AÑADIDO PARA PROMOCIONES /////
///////////////////////////////////	
if(htmlspecialchars(isset($_GET["cargarRestaurantesCiudades"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
	$lc_condiciones[1] = htmlspecialchars($_GET["dsct_id"]);
	$lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
	$lc_condiciones[3] = htmlspecialchars($_GET["parametro"]);
	$lc_condiciones[4] = htmlspecialchars($_GET["pagina"]);
	$lc_condiciones[5] = htmlspecialchars($_GET["registros"]);
	$lc_condiciones[6] = 0;
	print $lc_config->fn_consultar("cargarRestaurantesCiudades", $lc_condiciones);
}


///////////////////////////////////	
//// AÑADIDO PARA PROMOCIONES /////
///////////////////////////////////	
if(htmlspecialchars(isset($_GET["cargarRestaurantesCiudadesTotal"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["cdn_id"]);
	print $lc_config->fn_consultar("cargarRestaurantesCiudadesTotal", $lc_condiciones);
}


///////////////////////////////////	
//// AÑADIDO PARA PROMOCIONES /////
///////////////////////////////////	
if(htmlspecialchars(isset($_GET["cargarRestaurantesPromociones"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["Id_Promociones"]);
	$lc_condiciones[1] = htmlspecialchars($_GET["cdn_id"]);
	print $lc_config->fn_consultar("cargarRestaurantesPromociones", $lc_condiciones);
}


///////////////////////////////////	
//// AÑADIDO PARA PROMOCIONES /////
///////////////////////////////////	
if(htmlspecialchars(isset($_GET["cargarPlusBeneficiosPromocion"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["Id_Promociones"]);
	print $lc_config->fn_consultar("cargarPlusBeneficiosPromocion", $lc_condiciones);
}


///////////////////////////////////	
//// AÑADIDO PARA PROMOCIONES /////
///////////////////////////////////	
if(htmlspecialchars(isset($_GET["cargarRestaurantesRegiones"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
	$lc_condiciones[1] = htmlspecialchars($_GET["dsct_id"]);
	$lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
	$lc_condiciones[3] = htmlspecialchars($_GET["parametro"]);
	$lc_condiciones[4] = htmlspecialchars($_GET["pagina"]);
	$lc_condiciones[5] = htmlspecialchars($_GET["registros"]);
	$lc_condiciones[6] = 0;
	print $lc_config->fn_consultar("cargarRestaurantesRegiones", $lc_condiciones);
}


if (htmlspecialchars(isset($_GET["cargarRestaurantesAsignadosDescuentos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["dsct_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["parametro"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["pagina"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["registros"]);
    $lc_condiciones[6] = 0;
    print $lc_config->fn_consultar("cargarRestaurantesAsignadosDescuentos", $lc_condiciones);
}

///////////////////////////////////	
//// AÑADIDO PARA PROMOCIONES /////
///////////////////////////////////	
if(htmlspecialchars(isset($_GET["cargarClasificacion"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
	print $lc_config->fn_consultar("cargarClasificacion", $lc_condiciones);
}


///////////////////////////////////	
//// AÑADIDO PARA PROMOCIONES /////
///////////////////////////////////	
if(htmlspecialchars(isset($_GET["cargarRestaurantesTotal"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["cdn_id"]);
	print $lc_config->fn_consultar("cargarRestaurantesTotal", $lc_condiciones);
}


///////////////////////////////////	
//// AÑADIDO PARA PROMOCIONES /////
///////////////////////////////////	
if(htmlspecialchars(isset($_GET["cargarCanal"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["Id_Promociones"]);
	print $lc_config->fn_consultar("cargarCanal", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarCategoriasDescuentos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["dsct_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["parametro"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["pagina"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["registros"]);
    $lc_condiciones[6] = 0;
    print $lc_config->fn_consultar("cargarCategoriasDescuentos", $lc_condiciones);
}

if (htmlspecialchars(isset($_POST["insertarDescuento"]))) {
    $lc_condiciones["accion"] = 0; //Con esto se escoge la accion de insertar
    $lc_condiciones["idDescuentos"] = '';
    $lc_condiciones["descripcion"] = utf8_decode($_POST["dsct_descripcion"]);
    $lc_condiciones["maximo"] = empty($_POST["dsct_max"]) ? 0 : htmlspecialchars($_POST["dsct_max"]);
    $lc_condiciones["minimo"] = empty($_POST["dsct_min"]) ? 0 : htmlspecialchars($_POST["dsct_min"]);
    $lc_condiciones["automatico"] = htmlspecialchars($_POST["automatico"]);
    $lc_condiciones["valor"] = htmlspecialchars($_POST["dsct_valor"]);
    $lc_condiciones["aplica_min_max"] = htmlspecialchars($_POST["aplica_minimos"]);
    $lc_condiciones["apld_id"] = htmlspecialchars($_POST["apld_id"]);
    $lc_condiciones["seguridad"] = htmlspecialchars($_POST["seguridad"]);
    $lc_condiciones["IDTipoDescuento"] = htmlspecialchars($_POST["tpd_id"]);
    $lc_condiciones["IDStatus"] = htmlspecialchars($_POST["estado"]);
    $lc_condiciones["replica"] = 0;
    $lc_condiciones["aplica_cantidad"] = htmlspecialchars($_POST["aplica_cantidad"]);
    $lc_condiciones["IDUsersPos"] = $_SESSION["usuarioId"];
    $lc_condiciones["estado"] = htmlspecialchars($_POST["estado"]);
    $lc_condiciones["plus"] = htmlspecialchars($_POST["productos"]);
    $lc_condiciones["restaurantes"] = htmlspecialchars($_POST["restaurantes"]);
    $lc_condiciones["cdn_id"] = htmlspecialchars($_POST["cdn_id"]);
    $lc_condiciones["dsct_cupones"]=$_POST["dsct_cupones"];

    $retorno = $lc_config->fn_consultar("insertarDescuento", $lc_condiciones);
    print json_encode($retorno);
}

if (htmlspecialchars(isset($_POST["modificarDescuento"]))) {
    $lc_condiciones["accion"] = 1; //Con esto se escoge la accion de actualizar
    $lc_condiciones["idDescuentos"] = htmlspecialchars($_POST["dsct_id"]);
    $lc_condiciones["descripcion"] = utf8_decode($_POST["dsct_descripcion"]);
    $lc_condiciones["minimo"] = empty($_POST["dsct_min"]) ? 0 : htmlspecialchars($_POST["dsct_min"]);
    $lc_condiciones["maximo"] = empty($_POST["dsct_max"]) ? 0 : htmlspecialchars($_POST["dsct_max"]);
    $lc_condiciones["automatico"] = htmlspecialchars($_POST["automatico"]);
    $lc_condiciones["valor"] = htmlspecialchars($_POST["dsct_valor"]);
    $lc_condiciones["aplica_min_max"] = htmlspecialchars($_POST["aplica_minimos"]);
    $lc_condiciones["apld_id"] = htmlspecialchars($_POST["apld_id"]);
    $lc_condiciones["seguridad"] = htmlspecialchars($_POST["seguridad"]);
    $lc_condiciones["IDTipoDescuento"] = htmlspecialchars($_POST["tpd_id"]);
    $lc_condiciones["IDStatus"] = htmlspecialchars($_POST["estado"]);
    $lc_condiciones["replica"] = 0;
    $lc_condiciones["aplica_cantidad"] = htmlspecialchars($_POST["aplica_cantidad"]);
    $lc_condiciones["IDUsersPos"] = $_SESSION["usuarioId"];
    $lc_condiciones["estado"] = htmlspecialchars($_POST["estado"]);
    $lc_condiciones["plus"] = htmlspecialchars($_POST["productos"]);
    $lc_condiciones["restaurantes"] = htmlspecialchars($_POST["restaurantes"]);
    $lc_condiciones["cdn_id"] = htmlspecialchars($_POST["cdn_id"]);
    $lc_condiciones["dsct_cupones"]=$_POST["dsct_cupones"];

    $retorno = $lc_config->fn_consultar("insertarDescuento", $lc_condiciones);
    print json_encode($retorno);
}

if (htmlspecialchars(isset($_POST["cargarAplicaDescuento"]))) {
    $lc_condiciones["estado"] = [1]; // 0: Todos	// 1: Activos	// 2: Inactivos
    $retorno = $lc_config->fn_cargar_AplicaDescuento($lc_condiciones);
    print json_encode($retorno);
}

if (htmlspecialchars(isset($_POST["cargarTipoDescuento"]))) {
    $lc_condiciones["estado"] = [1]; // 0: Todos	// 1: Activos	// 2: Inactivos
    $retorno = $lc_config->fn_cargar_TipoDescuento($lc_condiciones);
    print json_encode($retorno);
}

if (htmlspecialchars(isset($_POST["validarCondicionesPLU"]))) {
    $lc_condiciones["plu_id"] = htmlspecialchars($_POST["plu_id"]);
    $lc_condiciones["cdn_id"] = htmlspecialchars($_POST["cdn_id"]);
    $retorno = $lc_config->fn_validarCondicionesPLU($lc_condiciones);
    print json_encode($retorno);
}

if (htmlspecialchars(isset($_POST["cargarTiposDescuentos"]))) {
    $lc_condiciones["estado_id"] = htmlspecialchars($_POST["estado"]);
    $retorno = $lc_config->fn_cargarTiposDescuento($lc_condiciones);
    print json_encode($retorno);
}

if (htmlspecialchars(isset($_POST["guardarTipoDescuentos"]))) {
    $lc_condiciones["IDTipoDescuento"] = htmlspecialchars($_POST["IDTipoDescuento"]);
    $lc_condiciones["tpd_descripcion"] = utf8_decode($_POST["tpd_descripcion"]);
    $lc_condiciones["estado"] = htmlspecialchars($_POST["estado"]);
    $lc_condiciones["IDUsersPos"] = $_SESSION["usuarioId"];
    $retorno = $lc_config->fn_guardarTipoDescuentos($lc_condiciones);
    print json_encode($retorno);
}

if (htmlspecialchars(isset($_POST["cargarAplicaDescuentos"]))) {
    $lc_condiciones["estado_id"] = htmlspecialchars($_POST["estado"]);
    $retorno = $lc_config->fn_cargarAplicaDescuento($lc_condiciones);
    print json_encode($retorno);
}

if (htmlspecialchars(isset($_POST["guardarAplicaDescuentos"]))) {
    $lc_condiciones["IDTipoDescuento"] = htmlspecialchars($_POST["IDTipoDescuento"]);
    $lc_condiciones["apld_descripcion"] = htmlspecialchars($_POST["apld_descripcion"]);
    $lc_condiciones["estado"] = htmlspecialchars($_POST["estado"]);
    $retorno = $lc_config->fn_guardarAplicaDescuentos($lc_condiciones);
    print json_encode($retorno);
}

if(htmlspecialchars(isset($_POST['TipoAplicaDescuento']))){
	$lc_condiciones[0] = 2;
	$lc_condiciones[1] = htmlspecialchars($_POST["IDDescuento"]);
	print $lc_config->fn_consultar("TipoAplicaDescuento", $lc_condiciones);
}