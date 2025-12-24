<?php

session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de administracion de perfiles ////////////////////
///////TABLAS INVOLUCRADAS: ///////////////////////////////////////////////////
///////FECHA CREACION: 28-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////


include_once"../../system/conexion/clase_sql.php"; 
include_once"../../clases/clase_admperfil.php";


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

$lc_config  = new perfil();

if (htmlspecialchars(isset($_GET["administracionSeguridad"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["resultado"]));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET["codigo"]));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["cdn_id"]));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["std_id"]));
    print $lc_config->fn_consultar(htmlspecialchars(utf8_decode($_GET["accion"])), $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["actualizarPerfil"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["accion"]));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET["prf_id"]));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["prf_descripcion"]));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["prf_nivel"]));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET["prf_acceso"]));
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_GET["std_id"]));
    $lc_condiciones[6] = htmlspecialchars(utf8_decode($_GET["usr_id"]));
    $lc_condiciones[7] = htmlspecialchars(utf8_decode($_GET["vEsCajero"]));
    print $lc_config->fn_consultar('actualizarPerfil', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["verPantallasPerfil"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["resultado"]));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET["prf_id"]));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["acc_nivel"]));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["pnt_id"]));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET["std_id"]));
    print $lc_config->fn_consultar('verPantallasPerfil', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["verAccesosPerfil"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["resultado"]));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET["prf_id"]));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["acc_nivel"]));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["pnt_id"]));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET["std_id"]));
    print $lc_config->fn_consultar('verAccesosPerfil', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["restablecerClavePerfil"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET["accion"]));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET["prf_id"]));
	$lc_condiciones[2] = '';
	$lc_condiciones[3] = '';
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET["usr_log"]));
	print $lc_config->fn_consultar('restablecerClavePerfil', $lc_condiciones);
}

