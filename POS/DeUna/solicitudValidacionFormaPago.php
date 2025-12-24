<?php

include_once "./modelos/TransaccionesDeUna.php";
include_once "./consultaApiDeUna/deUnaApi.php";
include_once "./auditoriaDeUna/auditoriaDeUna.php";

if (
    isset($_POST["validarFormaPagoDeuna"])  && isset($_POST["odp_id"]) 
) {
    $datosDeUna = new TransaccionesDeUna;
    $deUnaApi = new deUnaApi;
    $odp_id = $_POST["odp_id"];

    $resp = false;

    $productosDeuna = json_decode($datosDeUna->obtenerProductosDe_ODP_By_ODPId_PaymentRequest($odp_id), true);
    if (isset($productosDeuna)) {
        foreach ($productosDeuna as $productos) {
            if (floatval($productos["subsidy"]) > 0 ) {
                $resp = true;
                break;
            }
        }
    }

    echo json_encode(array("tieneSubsidio" => $resp));
    return;

}

echo json_encode(array("status" => 0, "error" => "Error de parametros de envio"));
return;
