<?php
session_start();

include_once '../../system/conexion/clase_sql.php';
require_once "../../clases/clase_adminCliente.php";

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

$adminClientes = new AdminClientes();
$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];
$requestPOST = (object)(array_map_recursive("utf8_decode", $_POST));
$requestGET = (object)(array_map("utf8_decode", $_GET));

if (isset($requestPOST->crearPoliticaPantallaClientes) && (1 == $requestPOST->crearPoliticaPantallaClientes)) {
    $parametros =array(
        "cdn_id" => $idCadena,
        "usr_id" => $idUsuario,
    );
    $resultado = $adminClientes->insertarColeccionPantallaClientes($parametros);

    enviarRespuestaJson($resultado);

}else if(isset($requestPOST->guardarValoresCamposFormulario) && (1 == $requestPOST->guardarValoresCamposFormulario)){

    $valoresActivos=$requestPOST->valoresActivos;
    $valoresInactivos=$requestPOST->valoresInactivos;
    //$adminClientes->actualizarCamposInactivos($valoresActivos);
    $parametros =array(
        "cdn_id" => $idCadena,
        "usr_id" => $idUsuario,
        "valoresactivos" => $valoresActivos,
        "valoresinactivos" => $valoresInactivos,
    );
    $adminClientes->modificarCampos($parametros);

    enviarRespuestaJson($resultado);
}

die(json_encode((object)[
    "estado" => 0,
    "mensaje" => "No se especificó ninguna acción válida"
]));

function enviarRespuestaJson($resultadoFinal){
    header('Content-Type: application/json');
    print(json_encode($resultadoFinal));
    die();
}

function array_map_recursive($callback, $array)
{
    $func = function ($item) use (&$func, &$callback) {
        return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
    };

    return array_map($func, $array);
}

