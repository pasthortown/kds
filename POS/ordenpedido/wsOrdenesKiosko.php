<?php

session_start();
// Autor: Juan Esteban Canelos
// Fecha: 20/07/2019

if (!isset($_POST['turneroAccion'])) {
    echo 'Error: La variable turneroAccion no está definida.<br/>';
    die();
}
if (!isset($_POST['turneroURl'])) {
    echo 'Error: La variable turneroURl no está definida.<br/>';
    die();
}
if (!isset($_POST['idFactura'])) {
    echo 'Error: La variable factura no está definida.<br/>';
    die();
}

$cdn_id        = $_SESSION['cadenaId'];
$restaurante   = $_SESSION['rstId'];
$turneroAccion = $_POST['turneroAccion'];
$turneroURl    = $_POST['turneroURl'];
$idFactura     = $_POST['idFactura'];
$parametros = [
    'idFactura'     => "$idFactura",
    'idCadena'      => "$cdn_id",
    'idRestaurante' => "$restaurante"
];

$ch = curl_init($turneroURl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parametros));
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

if ($turneroAccion =='anularOrden') {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
}

try {
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpcode == 200) {
        if ($turneroAccion =='anularOrden') {
            echo $response;
        }
    } else {
        echo "Error de servidor: " . $httpcode . ' - ' . curl_error($ch);
    }
} catch (Exception $e) {
    trigger_error(sprintf(
        'Curl failed with error #%d: %s',
        $e->getCode(), $e->getMessage()),E_USER_ERROR);
} finally {
    curl_close($ch);
}