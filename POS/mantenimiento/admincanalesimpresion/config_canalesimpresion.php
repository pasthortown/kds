<?php

////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////
///////////DESCRIPCION: CANALES DE IMPRESION, CREAR MODIFICAR CANAL DE IMPRESION////
/////////////////////// POR CADENA /////////////////////////////////////////////////
////////////////TABLAS: canal_impresion, cadena/////////////////////////////////////
////////FECHA CREACION: 18/06/2015//////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

include_once"../../system/conexion/clase_sql.php";
include_once "../../clases/clase_admincanalesimpresion.php";

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_config = new canalesImpresion();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];
$lc_restaurante = $_SESSION['rstId'];

if (htmlspecialchars(isset($_GET["cargarCanalesImpresion"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[2] = '0';
    $lc_condiciones[3] = '0';
    $lc_condiciones[4] = '0';
    $lc_condiciones[5] = $lc_usuario;
    print $lc_config->fn_consultar("cargarCanalesImpresion", $lc_condiciones);
}
if (htmlspecialchars(isset($_GET["nombrecadena"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[2] = '0';
    $lc_condiciones[3] = '0';
    $lc_condiciones[4] = '0';
    $lc_condiciones[5] = $lc_usuario;
    print $lc_config->fn_consultar("cargarCanalesImpresion", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["nuevoCanalImpresion"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["descripcion"]));
    $lc_condiciones[3] = htmlspecialchars($_GET["estado"]);
    $lc_condiciones[4] = '0';
    $lc_condiciones[5] = $lc_usuario;
    print $lc_config->fn_consultar("cargarCanalesImpresion", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarCanalesImpresionMod"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[2] = '0';
    $lc_condiciones[3] = '0';
    $lc_condiciones[4] = htmlspecialchars($_GET["cimp_id"]);
    $lc_condiciones[5] = $lc_usuario;
    print $lc_config->fn_consultar("cargarCanalesImpresion", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["guardaCanalImpresionMod"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["descripcion"]));
    $lc_condiciones[3] = htmlspecialchars($_GET["estado"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["cimp_id"]);
    $lc_condiciones[5] = $lc_usuario;
    print $lc_config->fn_consultar("cargarCanalesImpresion", $lc_condiciones);
}