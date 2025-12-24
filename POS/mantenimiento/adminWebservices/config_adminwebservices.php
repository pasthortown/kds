<?php
/////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO////////////////////////////////////////////////
///////////DESCRIPCION: TIPO DE IMPRESORA, CREAR MODIFICAR TIPO DE IMPRESORA/////////////
////////////////TABLAS: tipo_impresora///////////////////////////////////////////////////
////////FECHA CREACION: 19/06/2015///////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_adminwebservices.php";

header('Content-Type: application/json');
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

$requestGET = (object)(array_map('utf8_decode', $_GET));
$requestPOST = (object)(array_map('utf8_decode', $_POST));

$adminwebservicesObj = new adminwebservices();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];

if (isset($requestPOST->accion)) {
    switch ($requestPOST->accion) {
        case "cargarPoliticasWebservicesCreadas":
            $lc_condiciones["cadena"] = $lc_cadena;
            print $adminwebservicesObj->fn_consultar("cargarMenuPantalla", $lc_condiciones);
            break;
        case "guardarRuta":
            $lc_parametros["accion"] = 1;
            $lc_parametros["nombrecoleccion"] = 'WS RUTA SERVICIO';
            $lc_parametros["usuario"] = $lc_usuario;
            $lc_parametros["cadena"] = $lc_cadena;
            $lc_parametros["estado"] = ("on" === $requestPOST->checkActivo) ? 1 : 0;
            $lc_parametros["idcoleccioncadena"] = $requestPOST->idColeccionRuta;
            $lc_parametros["idcolecciondedatoscadena"] = $requestPOST->idParametroRuta;
            $lc_parametros["nombrecolecciondedatoscadena"] = $requestPOST->nombreRuta;
            $lc_parametros["valor"] = $requestPOST->inputValorRuta;
            print json_encode($adminwebservicesObj->administrarColeccionWebService($lc_parametros));
            break;
        case "guardarServidor":
            $lc_parametros["accion"] = 1;
            $lc_parametros["nombrecoleccion"] = 'WS SERVIDOR';
            $lc_parametros["usuario"] = $lc_usuario;
            $lc_parametros["cadena"] = $lc_cadena;
            $lc_parametros["estado"] = ("on" === $requestPOST->checkActivo) ? 1 : 0;
            $lc_parametros["idcoleccioncadena"] = $requestPOST->idColeccionServidor;
            $lc_parametros["idcolecciondedatoscadena"] = $requestPOST->idParametroServidor;
            $lc_parametros["nombrecolecciondedatoscadena"] = $requestPOST->nombreServidor;
            $lc_parametros["valor"] = $requestPOST->inputValorServidor;
            print json_encode($adminwebservicesObj->administrarColeccionWebService($lc_parametros));
            break;
    }
}

