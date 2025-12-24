<?php

session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de administracion de seguridades /////////////////
///////FECHA CREACION: 25-01-2016 /////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////


include_once"../../system/conexion/clase_sql.php";
include_once"../../clases/clase_admpantalla.php";

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_config = new pantalla();

if (htmlspecialchars(isset($_GET["cargarPantallas"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["pnt_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["nivel"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["std_id"]);
    print $lc_config->fn_consultar('cargarPantallas', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarPredecesores"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["pnt_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["nivel"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["std_id"]);
    print $lc_config->fn_consultar('cargarPredecesores', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["administrarPantallas"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["pnt_id"]);
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["pnt_Nombre_Mostrar"]));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["pnt_Nombre_Formulario"]));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET["pnt_Descripcion"]));
    $lc_condiciones[5] = htmlspecialchars($_GET["pnt_Orden_Menu"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["pnt_Nivel"]);
    $lc_condiciones[7] = htmlspecialchars(utf8_decode($_GET["pnt_Ruta"]));
    $lc_condiciones[8] = htmlspecialchars(utf8_decode($_GET["pnt_Imagen"]));
    $lc_condiciones[9] = htmlspecialchars($_GET["std_id"]);
    $lc_condiciones[10] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[11] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[12] = htmlspecialchars($_GET["acc_id"]);
    print $lc_config->fn_consultar('administrarPantallas', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarOrdenMenu"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["pnt_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["nivel"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["std_id"]);
    print $lc_config->fn_consultar('cargarOrdenMenu', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargaSubModulo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["pnt_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["nivel"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["std_id"]);
    print $lc_config->fn_consultar('cargarPredecesores', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarAccesos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["pnt_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["nivel"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["std_id"]);
    print $lc_config->fn_consultar('cargarAccesos', $lc_condiciones);
}
