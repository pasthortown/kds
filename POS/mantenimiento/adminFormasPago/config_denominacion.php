<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla denominacion de billetes /////////////////////////
///////TABLAS INVOLUCRADAS: Billete_Denominacion, /////////////////////////////
///////FECHA CREACION: 20-02-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 04/06/2015 //////////////////////////////////
///////USUARIO QUE MODIFICO: Daniel Llerena ///////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Se incluyo la denominación de moneda , ///////
///////campo billete o moneda y se adapta alos nuevos estilos con Modales /////
///////////////////////////////////////////////////////////////////////////////


include_once"../../system/conexion/clase_sql.php";
include_once "../../clases/clase_denominacion.php";

$lc_config = new categoria();

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$idUsuario = $_SESSION["usuarioId"];

if (isset($_GET["cargarDenominacionesBilletes"])) {
    $lc_condiciones[0] = $_GET["accion"]; //accion para cargar
    $lc_condiciones[1] = $_SESSION['cadenaId'];
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = $idUsuario;

    print $lc_config->fn_consultar("administrarDenominacionesBilletes", $lc_condiciones);

} else if (isset($_GET["modificarDenominacionesBilletes"])) {
    $lc_condiciones[0] = $_GET["accion"];
    $lc_condiciones[1] = $_SESSION['cadenaId'];
    $lc_condiciones[2] = $_GET["btd_id"];
    $lc_condiciones[3] = $_GET["btd_Descripcion"];
    $lc_condiciones[4] = $_GET["btd_Valor"];
    $lc_condiciones[5] = $_GET["btd_Tipo"];
    $lc_condiciones[6] = $_GET["std_id"];
    $lc_condiciones[7] = $idUsuario;

    print $lc_config->fn_consultar("administrarDenominacionesBilletes", $lc_condiciones);

} else if (isset($_GET["agregarDenominacionesBilletes"])) {
    $lc_condiciones[0] = $_GET["accion"];
    $lc_condiciones[1] = $_SESSION['cadenaId'];
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = $_GET["btd_Descripcion"];
    $lc_condiciones[4] = $_GET["btd_Valor"];
    $lc_condiciones[5] = $_GET["btd_Tipo"];
    $lc_condiciones[6] = $_GET["std_id"];
    $lc_condiciones[7] = $idUsuario;
    print $lc_config->fn_consultar("administrarDenominacionesBilletes", $lc_condiciones);
}