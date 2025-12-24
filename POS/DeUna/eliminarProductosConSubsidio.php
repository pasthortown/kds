<?php

include_once "./modelos/TransaccionesDeUna.php";
include_once "./consultaApiDeUna/deUnaApi.php";
include_once "./auditoriaDeUna/auditoriaDeUna.php";
if (isset($_POST["solicitudValidacionDeUna"]) && isset($_POST["rst_id"])
&& isset($_POST["cdn_id"]) && isset($_POST["odp_id"]) && isset($_POST["est_id"]) && isset($_POST["cli_id"]) 
) {
    
    $datosDeUna = new TransaccionesDeUna;
    $odp_id = $_POST["odp_id"];
    $cdn_id = $_POST["cdn_id"];
    $resp = json_decode($datosDeUna->eliminarProductosConSubsidio($odp_id, $cdn_id),true);
    //var_dump($resp);
    if ($resp[0]["RowsDeleted"] > 0 ) {
        echo json_encode(array("recargar" => true));
        return;
    }
    echo json_encode(array("recargar" => false));
    return;
}
echo json_encode(array("recargar" => false));
return;