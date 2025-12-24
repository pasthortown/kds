<?php

include_once "./modelos/TransaccionesDeUna.php";
include_once "./consultaApiDeUna/deUnaApi.php";
include_once "./auditoriaDeUna/auditoriaDeUna.php";

if (isset($_POST["formaPagoDeUna"]) && isset($_POST["valor"]) && isset($_POST["rst_id"])
&& isset($_POST["cdn_id"]) && isset($_POST["usr_id"]) && isset($_POST["odp_id"]) && isset($_POST["IDEstacion"])
) {
    $datosDeUna = new TransaccionesDeUna;
    $deUnaApi = new deUnaApi;
    $cdn_id= $_POST["cdn_id"];
    $rst_id = $_POST["rst_id"];
    $odp_id = $_POST["odp_id"];
    $usr_id = $_POST["usr_id"];
    $IDEstacion = $_POST["IDEstacion"];
    $urlRequest = "";
    
    //var_dump($odp_id);
    $datosDB = json_decode($datosDeUna->consultarEstadoDelPagoDeUna_ReanudarPago($odp_id),true);
    //var_dump($datosDB);

    if (isset($datosDB[0]["requestId"]) && isset($datosDB[0]["status"]) ) {
        $status = $datosDB[0]["status"];
        $requestId = $datosDB[0]["requestId"];
        echo json_encode(array("status"=> 200, "statusOrden" => $status, "requestId" => $requestId  , "error" => ""));
        return;
    } else {
        echo json_encode(array("status"=> 400, "error" => ""));
        return;
    }
   
}

echo json_encode(array("status"=> 400, "error" => "Error de parametros de envio."));
return;

