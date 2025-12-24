<?php

session_start();

///////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Daniel Llerena////////////////////////////////////
///////DESCRIPCION	   : Archivo de configuracion del Modulo Retiro Fondo /
////////FECHA CREACION : 10/11/2015 ///////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR  :  /////////////////////////////////////////////////
///////DESCRIPCION	   :  /////////////////////////////////////////////////
///////TABLAS		   : //////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

include_once"../system/conexion/clase_sql.php";
include_once "../clases/clase_retiroFondo.php";
$lc_retiro = new retiroFondo();
$lc_rest = $_SESSION['rstId'];
$lc_usuarioId = $_SESSION['usuarioId'];
$ip = $_SESSION['direccionIp'];
$controlEstacion    = $_SESSION['IDControlEstacion'];


if (htmlspecialchars(isset($_GET["CargaDetallesFondoAsignado"]))) {
    $lc_condiciones[0] = 'D';
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = htmlspecialchars($_GET["usr_claveAdmin"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["tarjeta"]);
    $lc_condiciones[4] = $controlEstacion;
    print $lc_retiro->fn_consultar("CargaDetallesFondoAsignado", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["ConsultaFondoAsignado"]))) {
    $lc_condiciones[0] = 'C';
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = htmlspecialchars($_GET["usr_claveAdmin"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["tarjeta"]);
    $lc_condiciones[4] = $controlEstacion;
    print $lc_retiro->fn_consultar("ConsultaFondoAsignado", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["ValidaFondoRetirado"]))) {
    $lc_condiciones[0] = 'V';
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = htmlspecialchars($_GET["usr_claveAdmin"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["tarjeta"]);
    $lc_condiciones[4] = $controlEstacion;
    print $lc_retiro->fn_consultar("ValidaFondoRetirado", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["RetirarFondoAsignado"]))) {
    $lc_condiciones[0] = 'U';
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = htmlspecialchars($_GET["usr_claveAdmin"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["tarjeta"]);
    $lc_condiciones[4] = $controlEstacion;
    print $lc_retiro->fn_consultar("RetirarFondoAsignado", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["validarUsuarioAdministrador"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["usr_claveAdmin"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["usr_claveCajero"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["est_ip"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["tarjeta"]);
    print $lc_retiro->fn_consultar("validarUsuarioAdministrador", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["obtenerMesa"]))) {
    $lc_condiciones[0] = $lc_rest;
    $lc_condiciones[1]=$_SESSION['estacionId'];
    $lc_condiciones[2]=$_SESSION['usuarioId'];
    print $lc_retiro->fn_consultar("obtenerMesa", $lc_condiciones);
}
?>
