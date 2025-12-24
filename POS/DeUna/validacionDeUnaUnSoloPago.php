<?php

include_once "./modelos/TransaccionesDeUna.php";

if (isset($_POST["validacionDeUnaUnSoloPago"]) && isset($_POST["cfac_id"])) {
    
    $cfac_id = $_POST["cfac_id"];
    $datosDeUna = new TransaccionesDeUna;
    $tieneFormaDePagoDeUna = json_decode($datosDeUna->consultarSiLaFacturaTieneFormaPagoDeUna($cfac_id), true);
    //var_dump($tieneFormaDePagoDeUna);
    if ($tieneFormaDePagoDeUna[0]["tieneFormaPagoDeUna"] == 1) {
        echo json_encode(["status" => 200, "data" => "La factura tiene forma de pago de DeUna"]);
    } else {
        echo json_encode(["status" => 400, "data" => "La factura no tiene forma de pago de DeUna"]);
    }
}