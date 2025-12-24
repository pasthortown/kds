<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    define("ROOT_PATH", '../../../../');
    require './app/orders/createOrder.php';
    $codApp = $_POST["codApp"];
    $restauranId = $_POST["restauranId"];
    $accion = $_POST["accion"];
    $medio = $_POST["medio"];
    print createOrder($codApp, $accion, $medio);
} else {
    return http_response_code(404);
}
