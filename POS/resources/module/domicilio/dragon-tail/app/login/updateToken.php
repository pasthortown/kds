<?php
require "../../../../system/conexion/clase_sql.php";
require "../../../../clases/clase_dragontailApiService.php";
require "../../../../resources/models/webservices/CallREST.php";
require "../../../../resources/models/webservices/Request.php";
require "../../../../clases/clase_dragonTailConfig.php";
require "../../../../clases/clase_DragontailToken.php";
require "../../../../exceptions/GeneralException.php";
function updateToken($restaurantId) {
    $cadenaId = DragonTailConfig::getCadenaId();
    $active = (new DragonTailConfig)->getDragonTailActive($restaurantId, $cadenaId);
    if ($active['registros'] > 0) {
        if ($active[0]['active']) {
            $data = DragontailToken::getTokenData();
            $urlDragonTail = DragonTailConfig::getUrlDragontail('LOGIN');
            $endPoint = DragonTailApiService::getEndPoint('login');
            $payload = array(
                "cdn_id" => $cadenaId,
                "medio"  => "DRAGONTAIL",
                "url"    => $urlDragonTail,
                "rst_id" => $restaurantId,
                "token"  => 'token',
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

            if ($response->error!="") {
                header("HTTP/1.1 400 Internal Server Error");
                return json_encode($response->error);
            }
            $data = json_decode($response->data);
            //DragonTailConfig::saveAuditoria($endPoint["url"],$peticion,$response->httpStatus,$body);
            if ($response->httpStatus==200 || $response->httpStatus==202) {
                DragontailToken::saveToken($data->jsonResult->message->token);
                return json_encode($data->response->mensaje);
            }
            header("HTTP/1.1 400 Internal Server Error");
            return json_encode($data->response->mensaje);
        } else {
            return "Petición no procesada en DragonTail. Política Inactiva";
        }
    } else {
        return "Petición no procesada en DragonTail. Política Inactiva";
    }
}
