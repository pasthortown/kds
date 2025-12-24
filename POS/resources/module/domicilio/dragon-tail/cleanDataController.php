<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require './app/data/cleanData.php';
    $restaurantId = $_POST["restaurantId"];
    print cleanData($restaurantId);
} else {
    return http_response_code(404);
}