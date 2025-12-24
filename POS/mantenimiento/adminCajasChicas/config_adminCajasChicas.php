<?php

////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: ANDRES ROMERO/////////////////////////////////////////////
///////////DESCRIPCION: DES-RELACIONAR CAJAS CHICAS ////////////////////////////////
////////////////API: servicios web sir /////////////////////////////////////////////
////////FECHA CREACION: 03/02/2022//////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

include_once("../../system/conexion/clase_sql.php");
include_once ("../../clases/clase_adminCajasChicas.php");

$lc_config = new cajasChicas();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuarioId = $_SESSION['usuarioId'];
$lc_rest = $_SESSION['rstId'];
$lc_ip = $_SESSION['direccionIp'];

if (htmlspecialchars(isset($_GET["ruta_servidor"]))) {
    $lc_condiciones[0] = $_SESSION['rstId'];
    $lc_condiciones[1] = 'CCL';
    $lc_condiciones[2] = htmlspecialchars($_GET['tipoTransaccion']);
    $lc_condiciones[3] = 0;
    print $lc_config->fn_consultar("ruta_servidor", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["consultar_localizacion"]))) {
    $lc_condiciones[0] = $lc_rest;
    print $lc_config->fn_consultar("consultar_localizacion", $lc_condiciones);

} elseif (htmlspecialchars(isset($_POST["cargarListaCajasChicas"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["fecha_inicio"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["fecha_fin"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["url"]);
    print $lc_config->fn_consultar("cargarListaCajasChicas", $lc_condiciones);

} elseif (htmlspecialchars(isset($_POST["desrelacionarCajasChicas"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["url"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["listaCodCierreChica"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["listaCajasChicas"]);
    $lc_condiciones[3] = htmlspecialchars($_POST["listaMovInv"]);
    print $lc_config->fn_consultar("desrelacionarCajasChicas", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_GET["cajasChicasSIR"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["url"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cod_cajero"]);
    $lc_condiciones[3] = $lc_rest;
    $lc_condiciones[4] = htmlspecialchars($_GET["fecha"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["localizacion"]);
    print $lc_config->fn_consultar("cajasChicasSIR", $lc_condiciones);
} 