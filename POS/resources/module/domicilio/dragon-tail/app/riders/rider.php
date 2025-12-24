<?php

require "../../../../system/conexion/clase_sql.php";
require "../../../../clases/clase_dragontailApiService.php";
require "../../../../clases/clase_DragonTailVehicles.php";
require "../../../../resources/models/webservices/CallREST.php";
require "../../../../resources/models/webservices/Request.php";
require "../../../../clases/clase_dragonTailConfig.php";
require "../../../../clases/clase_DragontailRiderRequest.php";

function createNewRider($motoroloId, $restaurantId, $accion) {
    $cadenaId = DragonTailConfig::getCadenaId();
    $active = (new DragonTailConfig)->getDragonTailActive($restaurantId, $cadenaId);

    if ($active['registros'] > 0) {
        if ($active[0]['active']) {
            $data = DragonTailConfig::getRider($motoroloId);
            $rider = DragonTailVehicles::from($data, $motoroloId, $restaurantId, $accion)->getPayload();
            $employeeRequest = DragontailRiderRequest::from($rider)->toJson();
            $endPoint = DragonTailApiService::getEndPoint('createRider');
            $urlDragonTail = DragonTailConfig::getUrlDragontail('EMPLOYEE');
            $token = DragonTailConfig::getDragontailToken();
            $payload = array(
                "cdn_id" => $cadenaId,
                "rst_id" => $restaurantId,
                "medio"  => "DRAGONTAIL",
                "url"    => $urlDragonTail,
                "token"  => $token,
                "data"   => $employeeRequest,
            );

            return makeRequest($endPoint, $payload);
        } else {
            return "Petición no procesada en DragonTail. Política Inactiva";
        }
    } else {
        return "Petición no procesada en DragonTail. Política Inactiva";
    }
}
function makeRequest($endPoint, $payload) {
    $request = new Request;
    $request->url = $endPoint['url'];
    $request->headers = array(
        CURLOPT_URL           => $endPoint['url'],
        CURLOPT_CUSTOMREQUEST => $endPoint['method'],
        CURLOPT_POSTFIELDS    => json_encode($payload),
        CURLOPT_HTTPHEADER    => array("Content-Type: application/json")
    );    //Clase genérica para el consumo de REST
    $callREST = new CallREST;

    $response = $callREST->call($request, 60);

    if ($response->httpStatus == 408) {
        header("HTTP/1.1 400 Internal Server Error");
        return $response->exceptionMessage;
    }
    if ($response->error != "") {
        header("HTTP/1.1 400 Internal Server Error");
        return $response->error;
    }

    $data = json_decode($response->data);

    if ($response->httpStatus == 400) {
        header("HTTP/1.1 400 Internal Server Error");
        return json_encode($data->response->mensaje, JSON_UNESCAPED_UNICODE);
    }
    if ($response->httpStatus == 200 || $response->httpStatus == 202) {
        return json_encode($data->response->mensaje, JSON_UNESCAPED_UNICODE);
    }
    header("HTTP/1.1 400 Internal Server Error");

    return json_encode($data->response->mensaje, JSON_UNESCAPED_UNICODE);
}
