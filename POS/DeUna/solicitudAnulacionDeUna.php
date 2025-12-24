<?php

include_once "./modelos/TransaccionesDeUna.php";
include_once "./consultaApiDeUna/deUnaApi.php";
include_once "./auditoriaDeUna/auditoriaDeUna.php";

if (isset($_POST["anularPagoDeUna"]) && isset($_POST["cfac_id"]) && isset($_POST["fpf_id"]) && isset($_POST["fmp_id"])
&& isset($_POST["usr_id"]) && isset($_POST["cdn_id"]) && isset($_POST["rst_id"]) && isset($_POST["est_id"]))
    {
    $usr_id = $_POST["usr_id"];
    $rst_id = $_POST["rst_id"];
    $cdn_id = $_POST["cdn_id"];
    $cfac_id = $_POST["cfac_id"];
    $IDEstacion = $_POST["est_id"];
    $ID_FormaPagoFactura = $_POST["fpf_id"];
    $ID_FormaPago = $_POST["fmp_id"];
    $urlRequest = "";
    $datosDeUna = new TransaccionesDeUna;
    $deUnaApi = new deUnaApi;
    
    $auditoriaDeuna = new AuditoriaDeuna;


    $datosUrl = json_decode($datosDeUna->consultarUrl($rst_id, 'DE UNA', 'PAYMENT VOID'), true);
    foreach ($datosUrl as $value) {
        $urlRequest = $value["direccionws"];
    }

    $datosUrlGetTransferNumber = json_decode($datosDeUna->consultarUrl($rst_id, 'DE UNA', 'GET TRANSFER NUMBER'), true);
    foreach ($datosUrlGetTransferNumber as $value) {
        $urlRequestTransferNumber = $value["direccionws"];
    }

    $API_KEY = "";
    $API_SECRET = "";
    $format = "";
    $internalTransactionReference = "odp_id";
    $pointOfSale = "";
    $qrType = "";
    $detail = "";
    $timeoutCurl = "";
    $parametrosDeUna = json_decode($datosDeUna->consultarParametrosDeUna($cdn_id, $rst_id,$IDEstacion),true);
    foreach ($parametrosDeUna as $key => $value) {
        if ($value["Descripcion"] == "INTEGRACION DE UNA") {
            if ($value["parametro"] == "API KEY") {
                $API_KEY= $value["variableV"];
            } else if ($value["parametro"] == "API SECRET") {
                $API_SECRET = $value["variableV"];
            } else if ($value["parametro"] == "format") {
                $format = $value["variableV"];
            } else if ($value["parametro"] == "pointOfSale") {
                $pointOfSale = $value["variableV"];
            } else if ($value["parametro"] == "qrType") {
                $qrType = $value["variableV"];
            }  else if ($value["parametro"] == "detail") {
                $detail = $value["variableV"];
            } else if ($value["parametro"] == "TIMEOUT EN SEGUNDOS") {
                $timeoutCurl = intval($value["variableI"]);
            }
        }
    }
    
    $transferNumber = "";
   
    $odp_id = "";
    
    $datosTransaction = json_decode($datosDeUna->obtenerTransferNumberYOdpIdByCfacId($cfac_id), true);
    if (isset($datosTransaction)) {
        if (isset($datosTransaction[0]["odp_id"])) {
            $odp_id = $datosTransaction[0]["odp_id"];
        }
        
        if (isset($datosTransaction[0]["transactionId"])) {
            $transactionId = $datosTransaction[0]["transactionId"];
            $respuestaDeUnaTransferNumber = json_decode($deUnaApi->getTransferNumberByTransId($urlRequestTransferNumber, $transactionId, $timeoutCurl), true);
            if ($respuestaDeUnaTransferNumber["status"] == "200") {
                $respDeUnaTransferNumberData = json_decode($respuestaDeUnaTransferNumber["data"], true);
                if ($respDeUnaTransferNumberData["error"] == false && isset($respDeUnaTransferNumberData["transferNumber"])) {
                    $transferNumber = $respDeUnaTransferNumberData["transferNumber"];
                } else {
                    echo json_encode(array("status" => 0, "error" => "Error: No se puede recuperar el transferNumber"));
                    return;
                }
            }

        } else {
            echo json_encode(array("status" => 0, "error" => "Error: No se puede recuperar el transactionId"));
            return;
        }

       
    }
    $datos_post = ["transferNumber" => $transferNumber];
    $respuestaDeUna = json_decode($deUnaApi->reversarPago($urlRequest, $transferNumber, $API_KEY, $API_SECRET, $timeoutCurl), true);
    $auditoriaDeuna->guardarAuditoriaDeUna($datosTransaction, $respuestaDeUna, "AnularPagoDeUna", $odp_id,$rst_id, $urlRequest, $usr_id);
    if ($respuestaDeUna["status"] == "200") {
        $datosDeUna->anularTransaccionDeuna($transactionId, $odp_id, 'ESTADO REVERSADO', $cdn_id);
        echo json_encode($respuestaDeUna);
        return;
    }
    echo json_encode(array("status" => 0, "error" => "Error al anular el pago en DeUna"));
    return;
}

echo json_encode(array("status"=> 0, "error" => "Error de parametros de envio"));
return;

