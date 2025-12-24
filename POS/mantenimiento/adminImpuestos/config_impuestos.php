<?php

///////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO////////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE IMPUESTOS, CREAR MODIFICAR IMPUESTO /////////
////////////////TABLAS: impuestos ///////////////////////////////////////////////
////////FECHA CREACION: 10/03/2016///////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////

include_once"../../system/conexion/clase_sql.php";
include_once "../../clases/clase_adminimpuestos.php";

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_config = new impuestos();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];
$lc_restaurante = $_SESSION['rstId'];

if (htmlspecialchars(isset($_GET["cargarImpuestos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = '0';
    $lc_condiciones[2] = htmlspecialchars($_GET["estado"]);
    print $lc_config->fn_consultar("cargarImpuestos", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["guardarImpuesto"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["descripcion"]));
    $lc_condiciones[3] = htmlspecialchars($_GET["porcentaje"]);
    $lc_condiciones[4] = $lc_usuario;
    $lc_condiciones[5] = '0';
    $lc_condiciones[6] = htmlspecialchars($_GET["feCodigo"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["feCodigoPorcentaje"]);
    $lc_condiciones[8] = htmlspecialchars($_GET["estado"]);
    $lc_condiciones[9] = htmlspecialchars($_GET["ordenImpN"]);
    print $lc_config->fn_ejecutar("guardarImpuesto", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarImpuestosMod"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["IDImpuestos"]);
    $lc_condiciones[2] = 0;
    print $lc_config->fn_consultar("cargarImpuestosMod", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["guardarImpuestosMod"]))) {

    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["descripcion"]));
    $lc_condiciones[3] = htmlspecialchars($_GET["porcentaje"]);
    $lc_condiciones[4] = $lc_usuario;
    $lc_condiciones[5] = htmlspecialchars($_GET["IDImpuestos"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["feCodigo"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["feCodigoPorcentaje"]);
    $lc_condiciones[8] = htmlspecialchars($_GET["estado"]);
    $lc_condiciones[9] = htmlspecialchars($_GET["ordenImpM"]);
    print $lc_config->fn_ejecutar("guardarImpuesto", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarPais"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = '0';
    print $lc_config->fn_consultar("cargarPais", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["numeroMaximoImpuestos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = '0';
    $lc_condiciones[2] = 0;
    print $lc_config->fn_consultar("numeroMaximoImpuestos", $lc_condiciones);
}