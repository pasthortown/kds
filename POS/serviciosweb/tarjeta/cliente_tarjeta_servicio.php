<?php

header('Content-Type: application/json');
session_start();

include_once 'Requests/Login.php';
include_once 'Requests/Orquestador.php';
include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_ServicioTarjeta.php';
include_once '../../clases/clase_log.php';

$idEstacion     = $_SESSION['estacionId'];
$restaurante    = $_SESSION['rstId'];
$idUsuario      = $_SESSION['usuarioId'];

const ERROR_GENERIC = 'Ocurrió un error, verifique nuevamente';

$json = file_get_contents("php://input");
$request = json_decode($json);

$return = ['status' => true, 'error' => null, 'message' => 'Se realizo el pago', 'data' => ''];

try {
    $puerto = $_SESSION['servicioTarjeta']['puerto'];
    $protocolo = $_SESSION['servicioTarjeta']['protocolo'];
    $lc_ip = $_SESSION['direccionIp'];
    $url = $protocolo . "://" . $lc_ip . ":" . $puerto;
    $username = $_SESSION['servicioTarjeta']['username'];
    $password = $_SESSION['servicioTarjeta']['password'];
    $endpointLogin = $_SESSION['servicioTarjeta']['endpointLogin'];
    $endpointOrquestador = $_SESSION['servicioTarjeta']['endpointOrquestador'];

    $login = new Login($url, $endpointLogin);
    
    if (!$login->run($username, $password)) {
        throw new Exception('Error no se pudo autenticar al usuario. Por favor, verifica e inténtalo de nuevo.');
    }

    $orquestador = new Orquestador($url, $endpointOrquestador);
    
    $responseOrquestador = $orquestador->run(
        $request->tipo,
        $request->dispositivo,
        $request->factura,
        $request->valor,
        $request->valorPropina,
        $request->formaPagoIdentificador,
        $restaurante,
        $idEstacion,
        $idUsuario
    );

    if($responseOrquestador['status'] === false) {
        $return['status']  = false;
    }

    $codigoRespuestaAutorizador = $responseOrquestador['response']['datosRespuesta'][0]['codigorespuestaAutorizador'];
    $mensajeRespuesta = $responseOrquestador['response']['datosRespuesta'][0]['mensajeRespuesta'];
    $mensajeValidacion = $responseOrquestador['response']['mensaje'];
    $rsautId = $responseOrquestador['response']['insertedId'];
    $tipoRespuesta = $responseOrquestador['response']['datosRespuesta'][0]['tipoRespuesta'];

    $return['data'] = [
        'codigoRespuestaAutorizador' => $codigoRespuestaAutorizador,
        'mensajeRespuesta'           => $mensajeRespuesta,
        'mensajeValidacion'          => $mensajeValidacion,
        'rsautId'                    => $rsautId,
        'tipoRespuesta'              => $tipoRespuesta,
    ];

} catch (Exception $e) {
    $return['status']  = false;
    $return['message'] = ($e->getCode() == 1) ? $e->getMessage() : ERROR_GENERIC;
    $return['error']   = ($e->getCode() != 1) ? $e->getMessage() : null;
}

print json_encode($return);