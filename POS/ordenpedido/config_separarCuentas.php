<?php

///////////////////////////////////////////////////////////
///////DESARROLLADO POR: Cristhian Castro ////////////////////
///////DESCRIPCION: ///////////////////
///////TABLAS INVOLUCRADAS: Menu_Agrupacion, ////////
////////////////Menu_Agrupacionproducto////////
////////////////Detalle_Orden_Pedido////////
///////////////////Plus, Precio_Plu, Mesas///////////////////
///////FECHA CREACION: 28-02-2014//////////////////////////
///////FECHA ULTIMA MODIFICACION: 07-05-2014////////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro///////////////
///////DECRIPCION ULTIMO CAMBIO: Documentaci�n y validaci�n en Internet Explorer 9+///////
/////////////////////////////////////////////////////////// 


include_once"../system/conexion/clase_sql.php";
include_once "../clases/clase_separarCuentas.php";

$lc_config = new menuPedido();

/* ----------------------------------------------------------------------------------------------------
  Carga la p�gina de separaci�n de cuentas
  Funci�n de llamada: $(document).ready(function()
  ----------------------------------------------------------------------------------------------------- */
if (htmlspecialchars(isset($_GET["numeroCuentas"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odp_id"]);
    print $lc_config->fn_consultar("numeroCuentas", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["guardarCop"]))) {
    $lc_condiciones[0] = $_GET["mesa_id"];
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    print $lc_config->fn_consultar("guardarCop", $lc_condiciones);
}
/* ----------------------------------------------------------------------------------------------------
  Carga el detalle de la orden del pedido y devuelve los estados de la cuenta
  Funci�n de llamada: fn_inicio()
  ----------------------------------------------------------------------------------------------------- */
if (htmlspecialchars(isset($_GET["listaPendiente"]))) {
    /* $lc_condiciones[0]=$_GET["cdn_id"];
      $lc_condiciones[1]=$_GET["odp_id"];
      $lc_condiciones[2]=$_GET["dop_cuenta"]; */
    //$lc_condiciones[3]=$_GET["cat_id"];
    $lc_condiciones[0] = $_GET["rst_id"];
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[2] = $_GET["cat_id"];
    $lc_condiciones[3] = $_GET["dop_cuenta"];
    print $lc_config->fn_consultar("listaPendiente", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["listaPendiente_impuesto"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[1] = $_GET["idc"];
    print $lc_config->fn_consultar("listaPendiente_impuesto", $lc_condiciones);
}
//listacompleta
/*
  Retorna la lista completa de la orden
 */
if (htmlspecialchars(isset($_GET["listacompleta"]))) {
    $lc_condiciones[0] = $_GET["cdn_id"];
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    print $lc_config->fn_consultar("listacompleta", $lc_condiciones);
}
/* ----------------------------------------------------------------------------------------------------
  Valida el detalle de la orden de pedido con la cabecera de la orden de pedido
  Funci�n de llamada: fn_verificarPlu(dop_id,dop_cuenta)
  ----------------------------------------------------------------------------------------------------- */
if (htmlspecialchars(isset($_GET["verificarDop"]))) {
    $lc_condiciones[0] = htmlspecialchars(isset($_GET["odp_id"]))?htmlspecialchars($_GET["odp_id"]):'';
    $lc_condiciones[1] = htmlspecialchars(isset($_GET["dop_id"]))?htmlspecialchars($_GET["dop_id"]):'';
    
    if(!empty($lc_condiciones[0]) && !empty($lc_condiciones[1])){
        print $lc_config->fn_consultar("verificarDop", $lc_condiciones);
    }
    
}
/* ----------------------------------------------------------------------------------------------------
  Verifica si el plu seleccionado ya existe en la cuenta para realizar el c�lculo
  Funci�n de llamada: fn_verificarPlu(dop_id,dop_cuenta)
  ----------------------------------------------------------------------------------------------------- */
if (htmlspecialchars(isset($_GET["verificarPlu"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[1] = $_GET["dop_id"];
    $lc_condiciones[2] = $_GET["plu_id"];
    $lc_condiciones[3] = $_GET["dop_cuenta"];
    $lc_condiciones[4] = $_GET["mesa_id"];
    $lc_condiciones[5] = $_GET["cantidad_plus"];
    print $lc_config->fn_consultar("verificarPlu", $lc_condiciones);
}
/* ----------------------------------------------------------------------------------------------------
  Verifica la cantidad del plu y si es mayor a 1 realiza proceso
  Funci�n de llamada: fn_agregarPlu(dop_id,new_dop_cuenta)
  ----------------------------------------------------------------------------------------------------- */
if (htmlspecialchars(isset($_GET["verificarCantidad"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[1] = $_GET["dop_id"];
    print $lc_config->fn_consultar("verificarCantidad", $lc_condiciones);
}
/* ----------------------------------------------------------------------------------------------------
  Verificar la cantidad del plu que ya existe en una cuenta
  Funci�n de llamada: fn_incrementarPlu(dop_id,plu_id,old_dop_id)
  ----------------------------------------------------------------------------------------------------- */
if (htmlspecialchars(isset($_GET["verificarCantidadPlu"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[1] = $_GET["plu_id"];
    $lc_condiciones[2] = $_GET["dop_id"];
    print $lc_config->fn_consultar("verificarCantidadPlu", $lc_condiciones);
}
/* ----------------------------------------------------------------------------------------------------
  Incrementa la cantidad del plu que ya existe en una cuenta
  Funci�n de llamada: fn_incrementarPlu(dop_id,plu_id,old_dop_id)
  ----------------------------------------------------------------------------------------------------- */
if (htmlspecialchars(isset($_GET["incrementarPlu"]))) {
    $lc_condiciones[0] = $_GET["dop_id"];
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[2] = $_GET["plu_id"];
    $lc_condiciones[3] = $_GET["dop_cantidad"];
    $lc_condiciones[4] = $_GET["old_dop_id"];
    print $lc_config->fn_consultar("incrementarPlu", $lc_condiciones);
}
/* ----------------------------------------------------------------------------------------------------
  Agrega un plu si no existe
  Funci�n de llamada: fn_agregarPlu(dop_id,new_dop_cuenta)
  ----------------------------------------------------------------------------------------------------- */
if (htmlspecialchars(isset($_GET["agregarPlu"]))) {
    $lc_condiciones[0] = $_GET["dop_id"];
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[2] = $_GET["plu_id"];
    $lc_condiciones[3] = $_GET["dop_cantidad"];
    $lc_condiciones[4] = $_GET["dop_iva"];
    $lc_condiciones[5] = $_GET["dop_precio_unitario"];
    $lc_condiciones[6] = $_GET["dop_total"];
    $lc_condiciones[7] = $_GET["dop_cuenta"];
    $lc_condiciones[8] = $_GET["dop_estado"];
    $lc_condiciones[9] = $_GET["new_dop_cuenta"];
    print $lc_config->fn_consultar("agregarPlu", $lc_condiciones);
}
/* ----------------------------------------------------------------------------------------------------
  Actualiza el dop_cuenta del plu con el id obtenido
  Funci�n de llamada: fn_actualizarPlu(dop_id,dop_cuenta)
  ----------------------------------------------------------------------------------------------------- */
if (htmlspecialchars(isset($_GET["actualizarPlu"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[1] = $_GET["dop_cuenta"];
    $lc_condiciones[2] = $_GET["dop_id"];
    print $lc_config->fn_consultar("actualizarPlu", $lc_condiciones);
}
/* ----------------------------------------------------------------------------------------------------
  Actualiza el detalle de las cuentas//
  Funci�n de llamada: fn_actualizarCuenta()
  ----------------------------------------------------------------------------------------------------- */
if (htmlspecialchars(isset($_GET["actualizarCuenta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[1] = $_GET["dop_cuenta"];
    print $lc_config->fn_consultar("actualizarCuenta", $lc_condiciones);
}
/* ----------------------------------------------------------------------------------------------------
  Genera los canales de impresi�n de la separaci�n de cuentas
  Funci�n de llamada: fn_canalesImpresion()
  ----------------------------------------------------------------------------------------------------- */
if (htmlspecialchars(isset($_GET["canalesImpresion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odp_id"]);
    print $lc_config->fn_consultar("canalesImpresion", $lc_condiciones);
}
/* ----------------------------------------------------------------------------------------------------
  Envia la impresi�n de la separaci�n de cuentas
  Funci�n de llamada: fn_canalesImpresion()
  ----------------------------------------------------------------------------------------------------- */
if (htmlspecialchars(isset($_GET["enviarImpresion"]))) {
    $lc_condiciones[0] = $_GET["imp_ip_estacion"];
    $lc_condiciones[1] = $_GET["imp_url"];
    $lc_condiciones[2] = $_GET["imp_impresora"];
    $lc_condiciones[3] = $_GET["usr_id"];
    print $lc_config->fn_consultar("enviarImpresion", $lc_condiciones);
}

if (isset($_POST["finalizarSplits"])) {
    $lc_condiciones[0] = $_POST["odp_id"];
    $lc_condiciones[1] = $_POST["splits"];
    $lc_condiciones[2] = $_POST["dop_id"];
    $lc_condiciones[3] = $_POST["dop_cuenta"];
    $lc_condiciones[4] = $_POST["mesa_id"];

    print $lc_config->fn_consultar("finalizarSplits", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["recuperar_cuenta_dividida"]))) {
    $lc_condiciones[0]=2; //Opción para actualizar
    $lc_condiciones[1]=$_GET["odp_id"];
    
    print $lc_config->fn_consultar("recuperar_cuenta_dividida", $lc_condiciones);
}

if (isset($_POST["estaDividido"])) {
    $lc_condiciones[0] = $_POST["dop_id"];
    print $lc_config->fn_consultar("estaDividido", $lc_condiciones);
}
if (isset($_POST["esFullService"])) {
    $lc_condiciones[0] = $_POST["rst_id"];
    print $lc_config->fn_consultar("esFullService", $lc_condiciones);
}
if (isset($_POST["obtieneParametrosVoucher"])) {
    $lc_condiciones[0] = $_POST["odp_id"];
    $lc_condiciones[1] = $_POST["dop_cuenta"];
    print $lc_config->fn_consultar("obtieneParametrosVoucher", $lc_condiciones);
}

if (isset($_POST["validarCuentaEnCero"])) {
  $detalleOrdenPedido = $_POST["dop_id"];
  $numeroDeCuenta = $_POST["dop_cuenta"];
  $idEstacion = $_POST["IDEstacion"];

  $lc_condiciones[0] = $_POST["dop_id"];
  $lc_condiciones[1] = $_POST["dop_cuenta"];
  $lc_condiciones[2] = $_POST["IDEstacion"];
  print $lc_config->validarCuentasEnCero($numeroDeCuenta, $detalleOrdenPedido);
}

?>