<?php

session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de administracion de usuarios ////////////////////
///////FECHA CREACION: 28-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////


include_once"../../system/conexion/clase_sql.php";
include_once"../../clases/clase_admusuarios.php";

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

$lc_config  = new usuarios();
$lc_restaurante = $_SESSION['rstId'];
$lc_usuario = $_SESSION['usuarioId'];

if (htmlspecialchars(isset($_GET["cargarPerfiles"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["resultado"]));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET["usr_id"]));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["cdn_id"]));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["std_id"]));
    print $lc_config->fn_cargarPerfiles($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["administracionSeguridad"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["resultado"]));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET["usr_id"]));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["cdn_id"]));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["std_id"]));
    print $lc_config->fn_cargarUsuarios($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["cargarLocalesUsuario"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["resultado"]));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET["usr_id"]));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["cdn_id"]));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["std_id"]));
    print $lc_config->fn_cargarLocalesUsuario($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["cargarLocales"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["resultado"]));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET["usr_id"]));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["cdn_id"]));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["std_id"]));
    print $lc_config->fn_cargarLocales($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["administrarUsuario"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["accion"]));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET["usr_id"]));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["cdn_id"]));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["prf_id"]));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET["std_id"]));
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_GET["usr_nombre_en_pos"]));
    $lc_condiciones[6] = htmlspecialchars(utf8_decode($_GET["usr_usuario"]));
    $lc_condiciones[7] = htmlspecialchars(utf8_decode($_GET["usr_iniciales"]));
    $lc_condiciones[8] = htmlspecialchars(utf8_decode($_GET["usr_descripcion"]));
    $lc_condiciones[9] = htmlspecialchars(utf8_decode($_GET["usr_tarjeta"]));
    $lc_condiciones[10] = htmlspecialchars(utf8_decode($_GET["usr_fecha_ingreso"]));
    $lc_condiciones[11] = htmlspecialchars(utf8_decode($_GET["usr_fecha_salida"]));
    $lc_condiciones[12] = htmlspecialchars(utf8_decode($_GET["usr_telefono"]));
    $lc_condiciones[13] = htmlspecialchars(utf8_decode($_GET["usr_email"]));
    $lc_condiciones[14] = htmlspecialchars(utf8_decode($_GET["usr_direccion"]));
    $lc_condiciones[15] = $lc_usuario;
    $lc_condiciones[16] = htmlspecialchars(utf8_decode($_GET["usr_rst"]));
    $lc_condiciones[17] = htmlspecialchars(utf8_decode($_GET["usr_cedula"]));


    $lc_condiciones[18] = trim(htmlspecialchars(utf8_decode($_GET["usr_clave"])));
    
    
    
    $lc_condiciones[19] = $lc_restaurante;      
    print $lc_config->fn_administrarUsuario($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["verificarUsuarioSistema"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["usuario"]));
    print $lc_config->fn_verificarUsuarioSistema($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["restablecerClaveUsuario"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["accion"]));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET["usr_id"]));
    $lc_condiciones[2] = '';
    $lc_condiciones[3] = '';
    $lc_condiciones[4] = $lc_usuario;
    $lc_condiciones[5] = trim(htmlspecialchars(utf8_decode($_GET["pass"])));
    print $lc_config->fn_restablecerClaveUsuario($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["ValidaDocumento"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["accion"]));
    $lc_condiciones[1] = $lc_restaurante;
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["documento"]));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["usuario"]));
    print $lc_config->fn_ValidaDocumento($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["ValidaUsuario"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["accion"]));
    $lc_condiciones[1] = $lc_restaurante;
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["documento"]));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["usuario"]));
    print $lc_config->fn_ValidaUsuario($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["listarRegiones"]))) {
    print $lc_config->fn_listarRegiones();
}
