<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de menú /////////////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 20-02-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////


include_once"../../system/conexion/clase_sql.php";
include_once "../../clases/clase_tipofacturacion.php";

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}
$request = (object) (array_map('utf8_decode', $_GET));
$lc_config = new tipofacturacio();

$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];

if (isset($request->CargaTipoFacturacionXestado)) {
    $lc_condiciones[0] = $request->Accion;
    $lc_condiciones[1] = $request->Opcion;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = $lc_usuario;
    print $lc_config->fn_consultar("CargaTipoFacturacionXestado", $lc_condiciones);
}

if (isset($request->CargaTipoFacturacion)) {
    $lc_condiciones[0] = $request->Accion;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = $lc_usuario;
    print $lc_config->fn_consultar("CargaTipoFacturacionXestado", $lc_condiciones);
}

if (isset($request->AccionNuevo)) {
    $lc_condiciones[0] = $request->Accion;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = $request->descripcion_tf;
    $lc_condiciones[3] = $request->ruta_impresion;
    $lc_condiciones[4] = $lc_usuario;
    $lc_condiciones[5] = 0;
    print $lc_config->fn_consultar("AccionNuevo", $lc_condiciones);
}

if (isset($request->CargaModificarTipoFacturacion)) {
    $lc_condiciones[0] = $request->Accion;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = $request->IDTipoFacturacion;
    $lc_condiciones[3] = $lc_usuario;
    print $lc_config->fn_consultar("CargaModificarTipoFacturacion", $lc_condiciones);
}

if (isset($request->AccionModificar)) {
    $lc_condiciones[0] = $request->Accion;
    $lc_condiciones[1] = $request->IDTipoFacturacion;
    $lc_condiciones[2] = $request->descripcion_tf;
    $lc_condiciones[3] = $request->ruta_impresion;
    $lc_condiciones[4] = $lc_usuario;
    $lc_condiciones[5] = $request->estado;
    print $lc_config->fn_consultar("AccionNuevo", $lc_condiciones);
}