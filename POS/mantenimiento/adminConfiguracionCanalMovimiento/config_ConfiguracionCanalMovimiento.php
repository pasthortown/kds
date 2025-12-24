<?php

/*
FECHA CREACION   : 04/10/2016 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion de promociones
*/

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_ConfiguracionCanalMovimiento.php";

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

$lc_config = new ConfiguracionCanalMovimiento();
$idUsuario = $_SESSION['usuarioId'];

if (htmlspecialchars(isset($_POST["cargarDetalle"]))) {
    $lc_condiciones[0] = $_POST["accion"];
    $lc_condiciones[1] = $_POST["estado"];
    print $lc_config->fn_cargarDetalle($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["guardarRegistro"]))) {
    $lc_condiciones[0] = $_POST["accion"];
    $lc_condiciones[1] = utf8_decode($_POST["descripcion"]);
    $lc_condiciones[2] = utf8_decode($_POST["codigo"]);
    $lc_condiciones[3] = $_POST["valor"];
    $lc_condiciones[4] = $_POST["estado"];
    $lc_condiciones[5] = $_POST["IDConfiguracionCanalMovimiento"];
    $lc_condiciones[6] = $idUsuario;
    print $lc_config->fn_guardarRegistro($lc_condiciones);
}


