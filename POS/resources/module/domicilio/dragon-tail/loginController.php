<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require './app/login/updateToken.php';
    $restaurantId = $_POST["restaurantId"];
    print updateToken($restaurantId);
} else {
    return http_response_code(404);
}
