<?php

header('Content-Type: application/json');
session_start();

include_once '../system/conexion/clase_sql.php';
include_once '../clases/clase_ServicioTarjeta.php';

$return = ['status' => true, 'error' => null, 'data' => null];

try {
    $servicioTarjeta = new ServicioTarjeta();
    $politicaServicioTarjeta = $servicioTarjeta->consutlarPolitica($_SESSION['cadenaId']);
    if (empty($politicaServicioTarjeta)) {
        throw new Exception('Error politica de servicio de tarjeta no parametrizado.  Por favor, verifica e inténtalo de nuevo.', 1);
    }
    
    $return['data'] = [
        'username' => $politicaServicioTarjeta['usuario'],
        'password' => $politicaServicioTarjeta['contrasena'],
        'endpointLogin' => $politicaServicioTarjeta['endpointLogin'],
        'endpointOrquestador' => $politicaServicioTarjeta['endpointOrquestador'],
        'timeout' => $politicaServicioTarjeta['timeout'],
        'aplica' => !empty($politicaServicioTarjeta['aplica']) ? $politicaServicioTarjeta['aplica'] : 0,
        'puerto' => !empty($politicaServicioTarjeta['puerto']) ? $politicaServicioTarjeta['puerto'] : 7165,
        'protocolo' => !empty($politicaServicioTarjeta['protocolo']) ? $politicaServicioTarjeta['protocolo'] : "https"
    ];

    $_SESSION['servicioTarjeta'] = $return['data'];

} catch (Exception $e) {
    $return['status'] = false;
    $return['error'] = $e->getMessage();
}

print json_encode($return);