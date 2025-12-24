<?php

/////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO////////////////////////////////////////////////
///////////DESCRIPCION: TIPO DE IMPRESORA, CREAR MODIFICAR TIPO DE IMPRESORA/////////////
////////////////TABLAS: tipo_impresora///////////////////////////////////////////////////
////////FECHA CREACION: 19/06/2015///////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////

include_once"../../system/conexion/clase_sql.php";
include_once "../../clases/clase_admintipoimpresora.php";

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_config = new tipoImpresora();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];
$lc_restaurante = $_SESSION['rstId'];

if (htmlspecialchars(isset($_GET["cargaIdPantalla"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["pnt_nombre"]));
    print $lc_config->fn_consultar("cargaIdPantalla", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarMenuPantalla"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["pnt_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["usr_id"]);
    print $lc_config->fn_consultar("cargarMenuPantalla", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["administracionTipoImpresora"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[2] = '0';
    $lc_condiciones[3] = '0';
    $lc_condiciones[4] = '0';
    $lc_condiciones[5] = $lc_usuario;
    $lc_condiciones[6] = '0';
    $lc_condiciones[7] = '0';
    $lc_condiciones[8] = '0';
    print $lc_config->fn_consultar("administracionTipoImpresora", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["nuevoTipoImpresora"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["descripcion"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["estado"]);
    $lc_condiciones[4] = '0';
    $lc_condiciones[5] = $lc_usuario;
    $lc_condiciones[6] = htmlspecialchars($_GET["codigoaperturacaja"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["codigocortepapel"]);
    $lc_condiciones[8] = htmlspecialchars($_GET["codigoimpresionnormal"]);
    print $lc_config->fn_consultar("administracionTipoImpresora", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarTipoImpresoraMod"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[2] = '0';
    $lc_condiciones[3] = '0';
    $lc_condiciones[4] = htmlspecialchars($_GET["timp_id"]);
    $lc_condiciones[5] = $lc_usuario;
    $lc_condiciones[6] = '0';
    $lc_condiciones[7] = '0';
    $lc_condiciones[8] = '0';
    print $lc_config->fn_consultar("administracionTipoImpresora", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["guardaTipoImpresoraMod"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["descripcion"]));
    $lc_condiciones[3] = htmlspecialchars($_GET["estado"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["timp_id"]);
    $lc_condiciones[5] = $lc_usuario;
    $lc_condiciones[6] = htmlspecialchars($_GET["codigoaperturacaja"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["codigocortepapel"]);
    $lc_condiciones[8] = htmlspecialchars($_GET["codigoimpresionnormal"]);
    print $lc_config->fn_consultar("administracionTipoImpresora", $lc_condiciones);
}