<?php

session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de administracion de accesos /////////////////////
///////FECHA CREACION: 27-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////


include_once"../../system/conexion/clase_sql.php";
include_once"../../clases/clase_admacceso.php";

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_config = new acceso();

if (isset($_GET["cargarAccesos"])) {
    $lc_condiciones[0] = $_GET["resultado"];
    print $lc_config->fn_consultar('cargarAccesos', $lc_condiciones);
}

if (isset($_GET["administrarAcceso"])) {
    $lc_condiciones[0] = $_GET["accion"];
    $lc_condiciones[1] = $_GET["acc_id"];
    $lc_condiciones[2] = utf8_decode($_GET["acc_descipcion"]);
    $lc_condiciones[3] = utf8_decode($_GET["acc_nombre"]);
    $lc_condiciones[4] = $_GET["acc_nivel"];
    $lc_condiciones[5] = $_GET["usr_id"];
    $lc_condiciones[6] = $_GET["cdn_id"];
    print $lc_config->fn_consultar('administrarAcceso', $lc_condiciones);
}