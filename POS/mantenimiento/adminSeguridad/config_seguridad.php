<?php

session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de administracion de seguridades /////////////////
///////TABLAS INVOLUCRADAS: ///////////////////////////////////////////////////
///////FECHA CREACION: 06-07-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 26/07/2016 
///////USUARIO QUE MODIFICO: Daniel Llerena
///////DECRIPCION ULTIMO CAMBIO: Convertir caracteres especiales en entidades 
/////// HTML (htmlspecialchars)
///////////////////////////////////////////////////////////////////////////////

include_once"../../system/conexion/clase_sql.php"; 
include_once"../../clases/clase_admseguridad.php";

$lc_config  = new seguridad();

if(htmlspecialchars(isset($_GET["cargarUsuarios"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["codigo"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["std_id"]);
    print $lc_config->fn_cargarUsuarios($lc_condiciones);
}

if(htmlspecialchars(isset($_GET["cargarPerfiles"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["codigo"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["std_id"]);
    print $lc_config->fn_cargarPerfiles($lc_condiciones);
}

if(htmlspecialchars(isset($_GET["actualizarPerfil"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["prf_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["prf_descripcion"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["prf_nivel"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["std_id"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["usr_id"]);
    print $lc_config->fn_actualizarPerfil($lc_condiciones);
}



