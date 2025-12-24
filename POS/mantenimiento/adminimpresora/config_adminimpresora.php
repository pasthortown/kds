<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: CONFIGURACION IMPRESORA, CREAR MODIFICAR CONFIGURACION DE IMPRESORA ////////////
////////////////TABLAS: Impresora, Tipo_impresora, Canal_Impresora_Estacion, Restaurante ///////////////
////////FECHA CREACION: 22/06/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_adminimpresora.php";

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
$request = (object)(array_map('utf8_decode', $_GET));
$lc_config = new configuracionImpresora();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];
$lc_restaurante = $_SESSION['rstId'];

if (isset($request->cargarrestaurante)) {
    $lc_condiciones[0] = 1;
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = 0;
    print $lc_config->fn_consultar("Cargar_Restaurante", $lc_condiciones);
}

if (isset($request->administracionImpresora)) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = $request->restaurante;
    $lc_condiciones[2] = $request->accion;
    $lc_condiciones[3] = '0';
    $lc_condiciones[4] = '0';
    $lc_condiciones[5] = '0';
    $lc_condiciones[6] = '0';
    $lc_condiciones[7] = '0';
    $lc_condiciones[8] = '0';
    $lc_condiciones[9] = '0';
    $lc_condiciones[10] = '0';
    print $lc_config->fn_consultar("administracionImpresora", $lc_condiciones);
}

if (isset($request->cargartipoimpresora)) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = '0';
    $lc_condiciones[2] = $request->accion;
    $lc_condiciones[3] = '0';
    $lc_condiciones[4] = '0';
    $lc_condiciones[5] = '0';
    $lc_condiciones[6] = '0';
    $lc_condiciones[7] = '0';
    $lc_condiciones[8] = '0';
    $lc_condiciones[9] = '0';
    $lc_condiciones[10] = '0';
    print $lc_config->fn_consultar("administracionImpresora", $lc_condiciones);
}

if (isset($request->nuevaConfiguracionImpresora)) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = $request->restaurante;
    $lc_condiciones[2] = $request->accion;
    $lc_condiciones[3] = $request->tipoimpresora;
    $lc_condiciones[4] = $request->nombre;
    $lc_condiciones[5] = $request->descripcion;
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = $request->estado;
    $lc_condiciones[8] = $lc_usuario;
    $lc_condiciones[9] = '0';
    $lc_condiciones[10] = $request->estacion;
    print $lc_config->fn_consultar("administracionImpresora", $lc_condiciones);
}

if (isset($request->cargarImpresoraMod)) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = '0';
    $lc_condiciones[2] = $request->accion;
    $lc_condiciones[3] = '0';
    $lc_condiciones[4] = '0';
    $lc_condiciones[5] = '0';
    $lc_condiciones[6] = '0';
    $lc_condiciones[7] = '0';
    $lc_condiciones[8] = $lc_usuario;
    $lc_condiciones[9] = $request->imp_id;
    $lc_condiciones[10] = '0';
    print $lc_config->fn_consultar("administracionImpresora", $lc_condiciones);
}

if (isset($request->guardaImpresoraMod)) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = $request->restaurante;
    $lc_condiciones[2] = $request->accion;
    $lc_condiciones[3] = $request->timp_id;
    $lc_condiciones[4] = $request->imp_nombre;
    $lc_condiciones[5] = $request->imp_descripcion;
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = $request->estado;
    $lc_condiciones[8] = $lc_usuario;
    $lc_condiciones[9] = $request->imp_id;
    $lc_condiciones[10] = $request->estacion;
    print $lc_config->fn_consultar("administracionImpresora", $lc_condiciones);
}

if (isset($request->nombrerestaurante)) {
    $lc_condiciones[0] = 2;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = $request->idrestauante;
    print $lc_config->fn_consultar("nombrerestaurante", $lc_condiciones);
}

if (isset($request->traerEstaciones)) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = $request->idrestauante;
    $lc_condiciones[2] = $request->accion;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = 0;
    $lc_condiciones[8] = 0;
    $lc_condiciones[9] = 0;
    $lc_condiciones[10] = 0;
    print $lc_config->fn_consultar("administracionImpresora", $lc_condiciones);
}


?>

