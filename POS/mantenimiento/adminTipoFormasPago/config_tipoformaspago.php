<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: ADMINISTRACION DE TIPOS FORMAS DE PAGO TARJETAS, LISTADO, AGREGAR Y MODIFICAR //
////////////////TABLAS: Tipo_Forma_Pago ////////////////////////////////////////////////////////////////
////////FECHA CREACION: 13/07/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

include_once"../../system/conexion/clase_sql.php";
include_once "../../clases/clase_admintipoformaspago.php";

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_config = new administraciontipoformaspago();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];
$lc_restaurante = $_SESSION['rstId'];

if (isset($_GET["administracionTipoformaspago"])) {
    $lc_condiciones[0] = $_GET["accion"];
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;
    print $lc_config->fn_consultar("administracionTipoformaspago", $lc_condiciones);

} else if (isset($_GET["nuevoTipoformaspago"])) {
    $lc_condiciones[0] = $_GET["accion"];
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = utf8_decode($_GET["descripcion"]);
    $lc_condiciones[4] = $_GET["estado"];
    $lc_condiciones[5] = 0;
    print $lc_config->fn_consultar("administracionTipoformaspago", $lc_condiciones);

} else if (isset($_GET["cargarTipoformaspagoMod"])) {
    $lc_condiciones[0] = $_GET["accion"];
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = $_GET["tfp_id"];

    print $lc_config->fn_consultar("administracionTipoformaspago", $lc_condiciones);

} else if (isset($_GET["guardaTipoformaspagoMod"])) {
    $lc_condiciones[0] = $_GET["accion"];
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = utf8_decode($_GET["descripcion"]);
    $lc_condiciones[4] = $_GET["estado"];
    $lc_condiciones[5] = $_GET["tfp_id"];

    print $lc_config->fn_consultar("administracionTipoformaspago", $lc_condiciones);
}