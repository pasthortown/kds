<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require './app/riders/rider.php';
    $motoroloId = $_POST["motoroloId"];
    $restaurantId = $_POST["restaurantId"];
    $accion = $_POST["accion"];

    print json_encode(createNewRider($motoroloId, $restaurantId, $accion));
}
