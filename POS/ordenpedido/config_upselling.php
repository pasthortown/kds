<?php

session_start();

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

header('Access-Control-Allow-Origin: ' . $origin);
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'OPTIONS') {
    die();
}

$parametros = [];

try {
    $parametros = [
        'chain'             => isset($_SESSION['cadenaId']) ? $_SESSION['cadenaId'] : null,
        'restaurant'        => isset($_SESSION['rstId']) ? $_SESSION['rstId'] : null,
        'menu'              => isset($_SESSION['tmp_menu']) ? $_SESSION['tmp_menu'] : null,
        'classification'    => isset($_SESSION['tmp_clacificacion']) ? $_SESSION['tmp_clacificacion'] : null,
        'category'          => isset($_SESSION['tmp_categoria']) ? $_SESSION['tmp_categoria'] : null,
        'item'              => isset($_SESSION['tmp_item']) ? $_SESSION['tmp_item'] : null,
        'period'            => isset($_SESSION['IDPeriodo']) ? $_SESSION['IDPeriodo'] : null,
        'user'              => isset($_SESSION['usuarioId']) ? $_SESSION['usuarioId'] : null,
        'userName'          => isset($_SESSION['nombre']) ? $_SESSION['nombre'] : null,
        'quantity'          => isset($_SESSION['tmp_quantity']) ? (int)$_SESSION['tmp_quantity'] : 1,
    ];
} catch (Exception $e) {
    print_r($e);
}

echo json_encode($parametros);
?>
