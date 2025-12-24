<?php

include_once "./modelos/TransaccionesDeUna.php";
include_once "./consultaApiDeUna/deUnaApi.php";
include_once "./auditoriaDeUna/auditoriaDeUna.php";

if (isset($_POST["formaPagoDeUna"]) && isset($_POST["valor"]) && isset($_POST["rst_id"])
&& isset($_POST["cdn_id"]) && isset($_POST["usr_id"]) && isset($_POST["odp_id"]) && isset($_POST["IDEstacion"])
&& isset($_POST["requestId"])
) {
    $datosDeUna = new TransaccionesDeUna;
    $deUnaApi = new deUnaApi;
    $cdn_id= $_POST["cdn_id"];
    $rst_id = $_POST["rst_id"];
    $odp_id = $_POST["odp_id"];
    $usr_id = $_POST["usr_id"];
    $IDEstacion = $_POST["IDEstacion"];
    $requestId = $_POST["requestId"];
    $urlRequest = "";
    $datosUrl = json_decode($datosDeUna->consultarUrl($rst_id,'DE UNA','PAYMENT REQUEST TRANS ID'),true);
    $parametrosDeUna = json_decode($datosDeUna->consultarParametrosDeUna($cdn_id, $rst_id,$IDEstacion),true);
    foreach ($datosUrl as $value) {
        $urlRequest = $value["direccionws"];
    }

    $API_KEY = "";
    $API_SECRET = "";
    $format = "";
    $internalTransactionReference = $odp_id;
    $pointOfSale = "";
    $qrType = "";
    $detail = "";
    $timeoutCurl = "";
    $rst_cod_tienda = "";
    $maxIntentos = 3;
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
            } else if ($value["parametro"] == 'rst_cod_tienda') {
                $rst_cod_tienda = $value["variableV"];
            } else if ($value["parametro"] == 'NUMERO DE REINTENTOS TRANSACTION ID') {
                $maxIntentos = $value["variableI"];
            }
        }
    }
    
    $respuestaDeUna = json_decode($deUnaApi->montarPagoTransId($urlRequest,$requestId,$API_KEY,$API_SECRET,$timeoutCurl),true);
    //var_dump($respuestaDeUna);
    $auditoriaDeuna = new AuditoriaDeuna;
    $auditoriaDeuna->guardarAuditoriaDeUna($requestId, $respuestaDeUna, "MontarPagoTransIdDeUna", $odp_id, $rst_id, $urlRequest, $usr_id );
    $dataDeuna = json_decode($respuestaDeUna["data"],true);
    if (isset($respuestaDeUna["status"]) && $respuestaDeUna["status"] == "200") {
        if (isset($dataDeuna["transactionId"]) && isset($dataDeuna["status"]) && $dataDeuna["status"] == 'Requested') {
            //var_dump("entro");
            $respBase = json_decode($datosDeUna->guardarDeUnaIdEnCabeceraOrdenPedido($requestId, $dataDeuna["transactionId"] ,$odp_id,"ESTADO SOLICITADO",$cdn_id), true);
            //var_dump($respBase);
            if ($respBase[0]["existeTransaccionId"] == 1) {
                echo json_encode(array("status"=> 200, "error" => "", 
                "transactionId" => $dataDeuna["transactionId"],
                "pinCode" => $dataDeuna["pinCode"]));
                return;
            }
           
        }
    }

    if (isset($respuestaDeUna["status"]) && $respuestaDeUna["status"] == "409") {
         $respBase = json_decode($datosDeUna->guardarRequestIdEnCabeceraOrdenPedido($requestId, $odp_id,"ESTADO LIMITE EXCEDIDO",$cdn_id), true);
            if ($respBase[0]["existeRequestId"] == 1) {
                $respOcultar = json_decode($datosDeUna->ocultarProductosConSubsidio($cdn_id),true);
                echo json_encode(array("status"=> 409, "error" => "Error limite de subsidio excedido"));
                return;
            }
       
    }

    if (isset($respuestaDeUna["status"]) && $respuestaDeUna["status"] == "400") {
        $dataDeUna = json_decode($respuestaDeUna["data"],true);
        if (isset($dataDeUna["message"])) {
            echo json_encode(array("status"=> 400, "error" => $dataDeUna["message"]));
            return;
        }
    }

    //$maxIntentos = 6;
    for ($i = 1; $i <= $maxIntentos; $i++) {
        $respuestaDeUna = json_decode($deUnaApi->montarPagoTransId($urlRequest,$requestId,$API_KEY,$API_SECRET,$timeoutCurl),true);
        //var_dump($respuestaDeUna);
        $auditoriaDeuna->guardarAuditoriaDeUna($requestId, $respuestaDeUna, "MontarPagoTransIdDeUna", $odp_id, $rst_id, $urlRequest, $usr_id );
        //var_dump($respuestaDeUna);
        $dataDeuna = json_decode($respuestaDeUna["data"],true);
        if (isset($respuestaDeUna["status"]) && $respuestaDeUna["status"] == "200") {
            if (isset($dataDeuna["transactionId"]) && isset($dataDeuna["status"]) && $dataDeuna["status"] == 'Requested') {
                $respBase = json_decode($datosDeUna->guardarDeUnaIdEnCabeceraOrdenPedido($requestId, $dataDeuna["transactionId"] ,$odp_id,"ESTADO SOLICITADO",$cdn_id), true);
                if ($respBase[0]["existeTransaccionId"] == 1) {
                     echo json_encode(array("status"=> 200, "error" => "", 
                            "transactionId" => $dataDeuna["transactionId"],
                            "pinCode" => $dataDeuna["pinCode"]));
                    return;
                }
            
            }
        }

        if (isset($respuestaDeUna["status"]) && $respuestaDeUna["status"] == "409") {
            $respBase = json_decode($datosDeUna->guardarRequestIdEnCabeceraOrdenPedido($requestId, $odp_id,"ESTADO LIMITE EXCEDIDO",$cdn_id), true);
                if ($respBase[0]["existeRequestId"] == 1) {
                    $respOcultar = json_decode($datosDeUna->ocultarProductosConSubsidio($cdn_id),true);
                    if ($respOcultar[0]["RowsAffected"] > 0 ) {
                        echo json_encode(array("status"=> 409, "error" => "Error limite de subsidio excedido"));
                        return;
                    } else {
                        echo json_encode(array("status"=> 409, "error" => "Error limite de subsidio excedido"));
                        return;
                    }
                } else {
                    echo json_encode(array("status"=> 409, "error" => "Error limite de subsidio excedido"));
                    return;
                }
        }

        if (isset($respuestaDeUna["status"]) && $respuestaDeUna["status"] == "400") {
            $dataDeUna = json_decode($respuestaDeUna["data"],true);
            if (isset($dataDeUna["message"])) {
                echo json_encode(array("status"=> 400, "error" => $dataDeUna["message"]));
                return;
            }
        }
        sleep($i);
    }
    echo json_encode(array("status"=> 400, "error" => "Reintentos maximo: No se pudo solicitar el pago en el servicio de DeUna."));
    return;

}

echo json_encode(array("status"=> 400, "error" => "Error de parametros de envio."));
return;

