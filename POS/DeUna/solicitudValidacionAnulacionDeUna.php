<?php

include_once "./modelos/TransaccionesDeUna.php";
include_once "./consultaApiDeUna/deUnaApi.php";
include_once "./auditoriaDeUna/auditoriaDeUna.php";

if (isset($_POST["anularPagoDeUna"]) && isset($_POST["cfac_id"]) 
&& isset($_POST["usr_id"]) && isset($_POST["cdn_id"]) && isset($_POST["rst_id"]) && isset($_POST["est_id"]))
    {
    $usr_id = $_POST["usr_id"];
    $rst_id = $_POST["rst_id"];
    $cdn_id = $_POST["cdn_id"];
    $cfac_id = $_POST["cfac_id"];
    $IDEstacion = $_POST["est_id"];
    $urlRequest = "";
    $datosDeUna = new TransaccionesDeUna;
    $validacion = json_decode($datosDeUna->validacionAnulacionDeUna($cfac_id),true);
    //var_dump($validacion);
    if ($validacion[0]["esValida"] == 1) {
        echo json_encode(array("status"=> 200, "error" => ""));
        return;
    } else {
        echo json_encode(array("status"=> 400, "error" => "No se puede realizar la Nota de Credito, la fecha de ANULACION debe ser la MISMA que la fecha del PAGO."));
        return;
    }
    echo json_encode(array("status"=> 400, "error" => "No se puede realizar la Nota de Credito, la fecha de ANULACION debe ser la MISMA que la fecha del PAGO."));
    return;
}

echo json_encode(array("status"=> 0, "error" => "Error de parametros de envio"));
return;

