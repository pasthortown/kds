<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Darwin Mora////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de menú del sistema//////////////
///////TABLAS INVOLUCRADAS: Pantalla, permisos_perfil_pos, users_pos///////////
///////FECHA CREACION: 10-06-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_menu_mantenimiento.php";
include_once "../../clases/clase_seguridades.php";

$lc_config = new mantenimiento();
$usuario = new seguridades();

if (htmlspecialchars(isset($_GET["capturarTrafico"]))){
    $lc_condiciones[0] = htmlspecialchars($_GET["pnt_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[2] = 1;
    print $lc_config->fn_capturarTrafico($lc_condiciones);
} 
else if (htmlspecialchars (isset($_GET["muestraTrafico"]))){
    $lc_condiciones[0] = htmlspecialchars($_GET["pnt_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[2] = 0;
    print $lc_config->fn_muestraTrafico($lc_condiciones);
} 
else if (htmlspecialchars (isset($_GET["muestra_menusuperior"]))){
    $lc_condiciones[0] = "'C'"; //Consulta datos para mostrar
    print $lc_config->fn_muestra_menusuperior($lc_condiciones);
} 
else if (htmlspecialchars (isset($_GET["muestra_restaurantes"]))){
    $lc_condiciones[0] = $_SESSION['cadenaId'];
    $lc_condiciones[1] = $_SESSION['usuarioId'];
    print $lc_config->fn_muestra_restaurantes($lc_condiciones);
} 
else if (htmlspecialchars (isset($_GET["selecciona_restaurante"]))){
    print $_SESSION['rstId'];
} 
else if (htmlspecialchars (isset($_GET["cambia_restaurante"]))){
    $_SESSION['rstId'] = htmlspecialchars($_GET["rstId"]);
    $_SESSION['rstNombre'] = $usuario->fn_nombrelocal($_SESSION['rstId']);
    print $_SESSION['rstId'];
} 
else if (htmlspecialchars (isset($_GET["nombrelocalporcadena"]))){
    $cadena = htmlspecialchars($_GET["cdn_id"]);
    $user = $_SESSION['usuarioId'];
    $_SESSION['rstNombre'] = $usuario->fn_nombrelocalporcadena($cadena,$user);

    print $cadena;
} else if (htmlspecialchars(isset($_GET["CargarSitioMaxpoint"]))) {
    $lc_condiciones[0] = $_GET["SitioM"];
    $lc_condiciones[1] = $_GET["rstId"];
    print $lc_config->fn_CargarSitioMaxpoint($lc_condiciones);
}
