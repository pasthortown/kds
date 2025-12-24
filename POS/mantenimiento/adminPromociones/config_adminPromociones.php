<?php

/*
FECHA CREACION   : 16/07/2016 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion de promociones
*/

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_adminPromociones.php";

if (
    empty($_SESSION['rstId'])
    OR empty($_SESSION['usuarioId'])
    OR empty($_SESSION['cadenaId'])
) {
    die(json_encode((object)[
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_config = new configuracionPromociones();	
$lc_cadena = $_SESSION["cadenaId"];
$idUsuario = $_SESSION["usuarioId"];

if (isset($_POST["cargarRestaurante"])) {
    $lc_condiciones[0] = utf8_decode($_POST["accion"]);
    $lc_condiciones[1] = utf8_decode($lc_cadena);
    $lc_condiciones[2] = utf8_decode($_POST["localizacion"]);
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = "0";
    print $lc_config->fn_cargarRestaurante($lc_condiciones);
} else if (isset($_POST["cargarPromociones"])) {
    $lc_condiciones[0] = utf8_decode($_POST["accion"]);
    $lc_condiciones[1] = $lc_cadena;  
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = utf8_decode($_POST["estado"]);
    $lc_condiciones[4] = "0";
    print $lc_config->fn_cargarPromociones($lc_condiciones);
} else if (isset($_POST["guardarPromocion"])) {
    $lc_condiciones[0] = utf8_decode($_POST["accion"]);
    $lc_condiciones[1] = utf8_decode($_POST["fechaInicio"]);
    $lc_condiciones[2] = utf8_decode($_POST["fechaFin"]);
    $lc_condiciones[3] = utf8_decode($_POST["descripcion"]);
    $lc_condiciones[4] = utf8_decode($_POST["contenido"]);
    $lc_condiciones[5] = utf8_decode($_POST["estado"]);
    $lc_condiciones[6] = utf8_decode($_POST["IDRestaurante"]);
    $lc_condiciones[7] = $idUsuario;
    $lc_condiciones[8] = utf8_decode($_POST["IDPromocion"]);
    $lc_condiciones[9] = utf8_decode($_POST["aplicaPara"]);
    $lc_condiciones[10] = $lc_cadena;
    $lc_condiciones[11] = utf8_decode($_POST["mostrarEtiqueta"]);
    $lc_condiciones[12] = utf8_decode($_POST["checkMostarEtiqueta"]);
    print $lc_config->fn_guardarPromocion($lc_condiciones);
} else if (isset($_POST["cargarPromocionRestaurantes"])) {
    $lc_condiciones[0] = utf8_decode($_POST["accion"]);
    $lc_condiciones[1] = $lc_cadena;  
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = utf8_decode($_POST["IDPromocion"]);
    print $lc_config->fn_cargarPromocionRestaurantes($lc_condiciones);
}
