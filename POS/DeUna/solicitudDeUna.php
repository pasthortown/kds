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
    $datosUrl = json_decode($datosDeUna->consultarUrl($rst_id,'DE UNA','PAYMENT REQUEST'),true);
    foreach ($datosUrl as $value) {
        $urlRequest = $value["direccionws"];
    }
    $parametrosDeUna = json_decode($datosDeUna->consultarParametrosDeUna($cdn_id, $rst_id,$IDEstacion),true);
    $productosDeuna = json_decode($datosDeUna->obtenerProductosDe_ODP_By_ODPId_PaymentRequest($odp_id),true);
    $tieneSubsidio = "0";
    $subsidioFlagValue = 0;
    foreach($productosDeuna as $key => $pDeUna) {
        if (floatval($pDeUna["subsidy"]) <= 0) {
            $productosDeuna[$key]["subsidy"] = null;
        }
        if (floatval($pDeUna["unitSubsidy"]) <= 0) {
            $productosDeuna[$key]["unitSubsidy"] = null;
        }
        $subsidioFlagValue += floatval($productosDeuna[$key]["subsidy"]);
    }

    if ($subsidioFlagValue > 0) {
        $tieneSubsidio = "1";
    }

    $API_KEY = "";
    $API_SECRET = "";
    $format = "";
    $internalTransactionReference = $odp_id;
    $pointOfSale = "";
    $qrType = "";
    $qrTypeAux = "";
    $formatAux = "";
    $detail = "";
    $timeoutCurl = "";
    $rst_cod_tienda = "";
    $idSucursal = "";
    $pais = "";
    $intervalo = 3;
    $tiempoEsperaDePagoEnSegundos = 60;
    foreach ($parametrosDeUna as $key => $value) {
        if ($value["Descripcion"] == "INTEGRACION DE UNA") {
            if ($value["parametro"] == "API KEY") {
                $API_KEY= $value["variableV"];
            } else if ($value["parametro"] == "API SECRET") {
                $API_SECRET = $value["variableV"];
            }  else if ($value["parametro"] == "format") {
                $formatAux = $value["variableV"];
            }  else if ($value["parametro"] == "qrType") {
                $qrTypeAux = $value["variableV"];
            }  else if ($value["parametro"] == "FORMAT ESTACION") {
                $format = $value["variableV"];
            } else if ($value["parametro"] == "pointOfSale") {
                $pointOfSale = $value["variableV"];
            } else if ($value["parametro"] == "QR TYPE ESTACION") {
                $qrType = $value["variableV"];
            }  else if ($value["parametro"] == "detail") {
                $detail = $value["variableV"];
            } else if ($value["parametro"] == "TIMEOUT EN SEGUNDOS") {
                $timeoutCurl = intval($value["variableI"]);
            } else if ($value["parametro"] == 'rst_cod_tienda') {
                $rst_cod_tienda = $value["variableV"];
            } else if ($value["parametro"] == 'ID SUCURSAL') {
                $idSucursal = $value["variableV"];
            } else if ($value["parametro"] == 'PAIS') {
                $pais = $value["variableV"];
            } else if ($value["parametro"] == 'INTERVALO DE CONSULTA PAGO EN SEGUNDOS') {
                $intervalo = intval($value["variableI"]);
            } else if ($value["parametro"] == 'TIEMPO DE ESPERA PAGO SEGUNDOS') {
                $tiempoEsperaDePagoEnSegundos = intval($value["variableI"]);
            }
        }
    }

    if ($pais != "" && $pais != null) {
        $detail = $rst_cod_tienda . "" . $pais;
        $detail = strtoupper($detail);
    }

    $cfac_id = json_decode($datosDeUna->consultarCFacIdPorOdpId($odp_id),true);
    $cfac_id = $cfac_id[0]["cfac_id"];
    $amount = $_POST["valor"];
    $timestamp = time();
    $requestId = "DEUNA-". $rst_cod_tienda . "-" . $pointOfSale . "-" .$timestamp;
    $datos_post_data = [
                "products" => $productosDeuna,
                "store" => ["id" => intval($rst_id), "vendorId" => intval($cdn_id)],
                "invoiceId" => $cfac_id,
                "headOrderId" => $odp_id
    ];

    if (isset($_POST["client"]))  {
        $client = $_POST["client"];
        $nodoCliente = ["name" => $client ["name"] ,
                        "documentId" => $client ["documentId"],
                        "email" => $client ["email"]
                        ];
        $datos_post_data["client"] = $nodoCliente;
    }

    if ($qrType == "") {
        $qrType = $qrTypeAux;
    }
    if ($format == "") {
        $format = $formatAux;
    }

    $datos_post = array(
            'requestId' => $requestId,
            'sucursalId' => $idSucursal,
            'pointOfSale' => $pointOfSale,
            'qrType' => $qrType,
            'amount' => round(floatval($amount),2),
            'detail' => $detail,
            'internalTransactionReference' => $cfac_id,
            'format' => $format,
            'data' => $datos_post_data
        );
    //echo (json_encode($datos_post));
    //echo ($urlRequest);
    $respuestaDeUna = json_decode($deUnaApi->montarPago($urlRequest,$datos_post,$API_KEY,$API_SECRET,$timeoutCurl),true);
    //var_dump(($respuestaDeUna));
    $auditoriaDeuna = new AuditoriaDeuna;
    $auditoriaDeuna->guardarAuditoriaDeUna($datos_post, $respuestaDeUna, "SolicitudDePagoDeUna", $odp_id, $rst_id, $urlRequest, $usr_id );
    //guaradr estatus pendiente
    $respuestaDeUnaData = json_decode($respuestaDeUna["data"], true);
    
    if (isset($respuestaDeUna["status"]) && $respuestaDeUna["status"] == "200") {
        $respBase = json_decode($datosDeUna->guardarRequestIdEnCabeceraOrdenPedido($requestId, $odp_id,"ESTADO PENDIENTE",$cdn_id), true);
            if ($respBase[0]["existeRequestId"] == 1) {
                $respuestaDeUna["requestId"] = $requestId;
                $respuestaDeUna["intervalo"] = $intervalo;
                $respuestaDeUna["tiempoEsperaDePagoEnSegundos"] = $tiempoEsperaDePagoEnSegundos;
                $respuestaDeUna["tieneSubsidio"] = $tieneSubsidio;
                echo json_encode($respuestaDeUna);
                return;
            }
    }
    else {
        if (isset($respuestaDeUna["status"]) && $respuestaDeUna["status"] != "200" && $respuestaDeUna["data"]) {
            $respBase = json_decode($datosDeUna->guardarRequestIdEnCabeceraOrdenPedido($requestId, $odp_id,"ESTADO CANCELADO",$cdn_id), true);
            if ($respBase[0]["existeRequestId"] == 1) {
                $message = json_decode($respuestaDeUna["data"], true);
                echo json_encode(array("status" => 400, "error" => $message["message"], "intervalo" => $intervalo));
                return;
            }
        }
        $respBase = json_decode($datosDeUna->guardarRequestIdEnCabeceraOrdenPedido($requestId, $odp_id,"ESTADO CANCELADO",$cdn_id), true);
            if ($respBase[0]["existeRequestId"] == 1) {
                echo json_encode(array("status" => 0, "error" => "No existe conexion con el Servicio de DeUna.", "intervalo" => $intervalo));
                return;
            }
    }

    echo json_encode(array("status" => 0, "error" => "Error al montar el pago en DeUna.", "intervalo" => $intervalo));
    return;
}

echo json_encode(array("status"=> 0, "error" => "Error de parametros de envio." , "intervalo" => 3));
return;

