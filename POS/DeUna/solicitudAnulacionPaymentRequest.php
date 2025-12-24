<?php

include_once "./modelos/TransaccionesDeUna.php";
include_once "./consultaApiDeUna/deUnaApi.php";
include_once "./auditoriaDeUna/auditoriaDeUna.php";

if (
    isset($_POST["formaPagoDeUna"]) && isset($_POST["valor"]) && isset($_POST["rst_id"])
    && isset($_POST["cdn_id"]) && isset($_POST["usr_id"]) && isset($_POST["odp_id"]) && isset($_POST["IDEstacion"])
) {
    $datosDeUna = new TransaccionesDeUna;
    $deUnaApi = new deUnaApi;
    $cdn_id = $_POST["cdn_id"];
    $rst_id = $_POST["rst_id"];
    $odp_id = $_POST["odp_id"];
    $usr_id = $_POST["usr_id"];
    $IDEstacion = $_POST["IDEstacion"];
    $urlRequest = "";
    $datosUrl = json_decode($datosDeUna->consultarUrl($rst_id, 'DE UNA', 'PAYMENT REQUEST CANCEL'), true);
    foreach ($datosUrl as $value) {
        $urlRequest = $value["direccionws"];
    }
    $parametrosDeUna = json_decode($datosDeUna->consultarParametrosDeUna($cdn_id, $rst_id, $IDEstacion), true);
    //$productosDeuna = json_decode($datosDeUna->obtenobtenerProductosDe_ODP_By_ODPId_PaymentRequest($odp_id), true);

    $transactionId =json_decode($datosDeUna->obtenerTransactionIdBy_ODP_Id($odp_id), true);
    if (isset($transactionId)) {
        $transactionId = $transactionId[0]["transactionId"];
    }
    $API_KEY = "";
    $API_SECRET = "";
    $format = "";
    $internalTransactionReference = $odp_id;
    $pointOfSale = "";
    $qrType = "";
    $detail = "";
    $timeoutCurl = "";
    $empRuc = "";
    $idSucursal = "";
    foreach ($parametrosDeUna as $key => $value) {
        if ($value["Descripcion"] == "INTEGRACION DE UNA") {
            if ($value["parametro"] == "API KEY") {
                $API_KEY = $value["variableV"];
            } else if ($value["parametro"] == "API SECRET") {
                $API_SECRET = $value["variableV"];
            } else if ($value["parametro"] == "format") {
                $format = $value["variableV"];
            } else if ($value["parametro"] == "pointOfSale") {
                $pointOfSale = $value["variableV"];
            } else if ($value["parametro"] == "qrType") {
                $qrType = $value["variableV"];
            } else if ($value["parametro"] == "detail") {
                $detail = $value["variableV"];
            } else if ($value["parametro"] == "TIMEOUT EN SEGUNDOS") {
                $timeoutCurl = intval($value["variableI"]);
            } else if ($value["parametro"] == "emp_ruc") {
                $empRuc = $value["variableV"];
            } else if ($value["parametro"] == 'ID SUCURSAL') {
                $idSucursal = $value["variableV"];
            }
        }
    }
    $respuestaDeUna = json_decode($deUnaApi->desmontarPago($urlRequest, $idSucursal, $pointOfSale, $transactionId, $empRuc, $API_KEY, $API_SECRET, $timeoutCurl), true);
    $auditoriaDeuna = new AuditoriaDeuna;
    $auditoriaDeuna->guardarAuditoriaDeUna(json_encode([]), $respuestaDeUna, "DesmontarPagoDeUna", $odp_id, $rst_id, $urlRequest, $usr_id);

    //BORRAR COP_VARCHAR7
    if ($respuestaDeUna["status"] == "200") {
            $respBase = json_decode($datosDeUna->limpiarOrdenBy_ODP_Id($odp_id), true);
            if ($respBase[0]["existeTransaccionId"] == 1) {
                echo json_encode($respuestaDeUna);
                    return;
            
        }
    }
    //CANCELAR LA ORDEN 
    echo json_encode(array("status" => 0, "error" => "Error al desmontar el pago en DeUna"));
    return;
}

echo json_encode(array("status" => 0, "error" => "Error de parametros de envio"));
return;
