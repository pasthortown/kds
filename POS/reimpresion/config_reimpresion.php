<?php

@session_start();

$cadena = $_SESSION['cadenaId'];
$restaurante = $_SESSION['rstId'];
$tipo_servicio = $_SESSION['TipoServicio'];
$usuario = $_SESSION['usuarioId'];
$ip = $_SESSION['direccionIp'];
$estacion_id = $_SESSION['estacionId'];

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_reimpresion.php";
include_once "../clases/clase_webservice.php";
include_once "../xml/generar_xml.php";

require_once('../resources/module/fidelizacion/Token.php');
require_once('../resources/module/fidelizacion/TokenManager.php');
$lc_config = new menuPedido();
$servicioWebObj = new webservice();
$loyaltieTokenManager = new TokenManager();

if (isset($_GET["tipoDocumentos"])) {
    $lc_condiciones[0] = $_GET["tipo"];
    print $lc_config->fn_consultar("tipoDocumentos", $lc_condiciones);
}

if (isset($_GET["listarTransacciones"])) {
    $lc_condiciones[0] = $_GET["tipo"];
    $lc_condiciones[1] = $_GET["restaurante"];
    $lc_condiciones[2] = $_GET["estacion"];
    print $lc_config->fn_consultar("listarTransacciones", $lc_condiciones);
}

if (isset($_GET["cargarImpresora"])) {
    $lc_condiciones[0] = $_GET["estacion"];
    $lc_condiciones[1] = $_GET["restaurante"];
    print $lc_config->fn_consultar("cargarImpresora", $lc_condiciones);
}

if (isset($_GET["obtenerMesa"])) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $_SESSION['estacionId'];
    $lc_condiciones[2] = $_SESSION['usuarioId'];
    print $lc_config->fn_consultar("obtenerMesa", $lc_condiciones);
}

if (isset($_GET["visorCabeceraFactura"])) {
    $lc_condiciones[0] = $_GET["cfac_id"];
    print $lc_config->fn_consultar("visorCabeceraFactura", $lc_condiciones);
}

if (isset($_GET["visorDetalleFactura"])) {
    $lc_condiciones[0] = $_GET["cfac_id"];
    print $lc_config->fn_consultar("visorDetalleFactura", $lc_condiciones);
}

if (isset($_GET["totalDetalleFactura"])) {
    $lc_condiciones[0] = $_GET["cfac_id"];
    print $lc_config->fn_consultar("totalDetalleFactura", $lc_condiciones);
}

if (isset($_GET["formasPagoDetalleFactura"])) {
    $lc_condiciones[0] = $_GET["cfac_id"];
    print $lc_config->fn_consultar("formasPagoDetalleFactura", $lc_condiciones);
}

if (isset($_GET["visorCabeceraOrdenPedido"])) {
    $lc_condiciones[0] = $_GET["cfac_id"];
    print $lc_config->fn_consultar("visorCabeceraOrdenPedido", $lc_condiciones);
}

?>
