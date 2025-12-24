<?php 
include_once "./modelos/TransaccionesDeUna.php";
include_once "./consultaApiDeUna/deUnaApi.php";
if (isset($_POST["consultarPago"]) && isset($_POST["odp_id"])
&& isset($_POST["rst_id"]) && isset($_POST["cdn_id"]) && isset($_POST["IDEstacion"]) && isset($_POST["requestId"])
) {
    if ($_POST["odp_id"] != "") {
        $deUnaApi = new deUnaApi;
        $datosDeUna = new TransaccionesDeUna;
        $cdn_id= $_POST["cdn_id"];
        $rst_id = $_POST["rst_id"];
        $odp_id = $_POST["odp_id"];
        $usr_id = $_POST["usr_id"];
        $IDEstacion = $_POST["IDEstacion"];
        $requestId = $_POST["requestId"];

        $urlRequest = "";

        $datosUrl = json_decode($datosDeUna->consultarUrl($rst_id,'DE UNA','PAYMENT REQUEST TRANS ID'),true);
        foreach ($datosUrl as $value) {
            $urlRequest = $value["direccionws"];
        }

        $timeoutCurl = "";
        $estadoAprobado = "";
        $parametrosDeUna = json_decode($datosDeUna->consultarParametrosDeUna($cdn_id, $rst_id,$IDEstacion),true);
        
        foreach ($parametrosDeUna as $key => $value) {
            if ($value["Descripcion"] == "INTEGRACION DE UNA") {
                if ($value["parametro"] == "TIMEOUT EN SEGUNDOS") {
                    $timeoutCurl = intval($value["variableI"]);
                } else if ($value["parametro"] == 'ESTADO APROBADO') {
                    $estadoAprobado = $value["variableV"];
                }
            }
        }


        $respuestaEstado = json_decode($deUnaApi->estadoPago($urlRequest,$requestId,$timeoutCurl),true);
        //var_dump($respuestaEstado);
        if ($respuestaEstado["status"] >= 200 && $respuestaEstado["status"] < 300) {
            $bodyResp = json_decode($respuestaEstado["data"],true);
            $estadoPago = strtoupper($bodyResp["status"]);
            if ($estadoPago == $estadoAprobado) {
                $datosDeUna->actualizarEstadoPagoDeuna($odp_id, $cdn_id);
                $datosDeUna->insertarMedioVentaEnCabeceraFacturaBy_ODPId_Deuna($odp_id);
                echo json_encode(array("existePago" => true));
                return;
            }
        }
        // $existePagoAprobado = json_decode($datosDeUna->consultarCambioDeEstadoDelPago($_POST["odp_id"]),true);
        // if ($existePagoAprobado[0]["existePago"] == 1) {
        //     $datosDeUna->insertarMedioVentaEnCabeceraFacturaBy_ODPId_Deuna($_POST["odp_id"]);
        //     echo json_encode(array("existePago" => true));
        //     return;
        // }
        echo json_encode(array("existePago" => false));
        return;
    }
}
echo json_encode(array("status"=> 0, "error" => "Error de parametros de envio"));
return;
