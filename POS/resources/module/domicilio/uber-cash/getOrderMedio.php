<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    define("ROOT_PATH", '../../../../');
    require './app/orders/createOrder.php';
    $cadenaId = $_SESSION['cadenaId'];
    $restauranId = $_SESSION['rstId'];
    $codigo_app = $_POST["codigo_app"];
    $medio = $_POST["medio"];
    print validarUberDirectCashMedio($cadenaId, $restauranId, $codigo_app, $medio);
} else {
    return http_response_code(404);
}
