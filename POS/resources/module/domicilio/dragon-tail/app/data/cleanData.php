<?php
require "../../../../system/conexion/clase_sql.php";
require "../../../../resources/models/webservices/CallREST.php";
require "../../../../resources/models/webservices/Request.php";
require "../../../../clases/clase_dragontailApiService.php";
require "../../../../clases/clase_DragontailCleanOrders.php";
require "../../../../clases/clase_DragontailCleanRiders.php";
require "../../../../clases/clase_dragonTailConfig.php";
require "../../../../exceptions/GeneralException.php";
function cleanData($restaurantId){
    $cadenaId = DragonTailConfig::getCadenaId();
    $active = (new DragonTailConfig)->getDragonTailActive($restaurantId, $cadenaId);

    if ($active['registros'] > 0) {
        if ($active[0]['active']) {
            $token = DragonTailConfig::getDragontailToken();
            $urlDragonTail = DragonTailConfig::getUrlDragontail('EMPLOYEE');
            $endPoint = DragonTailApiService::getEndPoint('cleanData');
            $dataCleanRiders = DragontailCleanRiders::from($restaurantId)->toJson();
            makeRequest($dataCleanRiders, $endPoint, $token, $cadenaId, $restaurantId, $urlDragonTail);
            $urlDragonTail = DragonTailConfig::getUrlDragontail('CREATEORDER');
            $dataCleanOrders = DragontailCleanOrders::from($restaurantId)->toJson();
            return makeRequest($dataCleanOrders, $endPoint, $token, $cadenaId, $restaurantId, $urlDragonTail);
        } else {
            return "Petición no procesada en DragonTail. Política Inactiva";
        }
    } else {
        return "Petición no procesada en DragonTail. Política Inactiva";
    }
}

function makeRequest($data, $endPoint, $token, $cadenaId, $restaurantId, $urlDragonTail){
    $payload = array(
        "cdn_id" => $cadenaId,
        "medio"  => "DRAGONTAIL",
        "url"    => $urlDragonTail,
        "rst_id" => $restaurantId,
        "token"  => $token,
        "data"   => $data,
    );
    $body = json_encode($payload);
    $request = new Request;
    $request->headers = array(
        CURLOPT_URL           => $endPoint["url"],
        CURLOPT_CUSTOMREQUEST => $endPoint["method"],
        CURLOPT_POSTFIELDS    => $body,
        CURLOPT_HTTPHEADER    => array("Content-Type: application/json")
    );
    //Clase genérica para el consumo de REST
    $callREST = new CallREST;
    $response = $callREST->call($request, 60);
    $data = json_decode($response->data);

    if ($data->status == 400) {
        header("HTTP/1.1 400 Internal Server Error");
        return json_encode($data->response->mensaje);
    }

    if ($data->status == 200 || $data->status == 202) {
        header("HTTP/1.1 200 OK");
        return json_encode($data->response->mensaje);
    }
    header("HTTP/1.1 200 OK");
    return json_encode($data->response->mensaje);
}