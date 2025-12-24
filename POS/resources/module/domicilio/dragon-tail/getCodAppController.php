<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require './app/orders/getCodApp.php';
    $cfac_id = $_POST["cfac_id"];
    print getCodApp($cfac_id);
} else {
    return http_response_code(404);
}
