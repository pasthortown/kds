<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: ADMINISTRACION DE RED ADQUIRIENTE, LISTADO, AGREGAR Y MODIFICAR     ////////////
////////////////TABLAS: Red_Adquiriente ////////////////////////////////////////////////////////////////
////////FECHA CREACION: 13/07/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

include_once"../../system/conexion/clase_sql.php";
include_once "../../clases/clase_adminredadquiriente.php";

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_config = new administracionredadquiriente();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];
$lc_restaurante = $_SESSION['rstId'];

if (isset($_GET["administracionRedadquiriente"])) {
    $lc_condiciones[0] = $_GET["accion"];
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = 0;
    print $lc_config->fn_consultar("administracionRedadquiriente", $lc_condiciones);

} else if (isset($_GET["nuevoRedadquiriente"])) {
    $lc_condiciones[0] = $_GET["accion"];
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = utf8_decode($_GET["descripcion"]);
    $lc_condiciones[4] = $_GET["codredaquiriente"];
    $lc_condiciones[5] = $_GET["estado"];
    $lc_condiciones[6] = 0;
    print $lc_config->fn_consultar("administracionRedadquiriente", $lc_condiciones);
}
if (isset($_GET["cargarRedadquirienteMod"])) {
    $lc_condiciones[0] = $_GET["accion"];
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = $_GET["rda_id"];

    print $lc_config->fn_consultar("administracionRedadquiriente", $lc_condiciones);

} else if (isset($_GET["guardaRedaquirienteMod"])) {
    $lc_condiciones[0] = $_GET["accion"];
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = utf8_decode($_GET["descripcion"]);
    $lc_condiciones[4] = $_GET["codredaquiriente"];
    $lc_condiciones[5] = $_GET["estado"];
    ;
    $lc_condiciones[6] = $_GET["rda_id"];

    print $lc_config->fn_consultar("administracionRedadquiriente", $lc_condiciones);
}