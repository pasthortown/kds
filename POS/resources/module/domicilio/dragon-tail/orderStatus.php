<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require './app/orders/changeStatus.php';
    $codApp = $_POST["codApp"];
    $medio = $_POST["medio"];
    $estado = $_POST["estado"];
    print json_encode(changeStatus($codApp, $medio, $estado));
} else {
    return http_response_code(404);
}
