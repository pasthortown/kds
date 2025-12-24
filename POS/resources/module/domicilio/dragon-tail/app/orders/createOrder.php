<?php
require_once "../../../../exceptions/GeneralException.php";
require_once "../../../../clases/clase_dragontailApiService.php";
require_once "../../../../clases/clase_dragonTailOrders.php";
require_once "../../../../clases/clase_DragontailOrderItems.php";
require_once "../../../../clases/clase_DragontailRequest.php";
require_once "../../../../resources/models/webservices/CallREST.php";
require_once "../../../../resources/models/webservices/Request.php";
require_once "../../../../clases/clase_dragonTailConfig.php";
require_once "../../../../system/conexion/clase_sql.php";

function createOrder($codApp, $accion, $medio) {
    try {


        date_default_timezone_set("America/Guayaquil");
        $token = DragonTailConfig::getDragontailToken();
        $cadenaId = DragonTailConfig::getCadenaId();
        $restaurantId = DragonTailConfig::getRestaurantId();
        $urlDragonTail = DragonTailConfig::getUrlDragontail('CREATEORDER');
        $orderData = DragonTailConfig::getDataOrder($codApp, $restaurantId, $cadenaId, $accion);
        $seguimientoPedido = seguimientoPedido($cadenaId, $restaurantId, $orderData, $codApp, $medio);
        if ($seguimientoPedido == null || $seguimientoPedido == "") {
            throw new GeneralException(json_encode("DragonTail: Error con la política LISTA MEDIO " . $medio .
                " -CAMBIO ESTADOS AUTOMATICO. No existe o está inactiva", JSON_UNESCAPED_UNICODE));
        }
        $order = Clase_dragonTailOrders::from($orderData, $restaurantId, $cadenaId, $accion,
            $seguimientoPedido, $codApp, $medio)->toJson();
        $items = addPositionPrincipal($orderData['items']);
        $items = addPositionOther($items);

        foreach ($items as $item ) {
            $orderItems[] = DragonTailOrderItems::from($item, $restaurantId, $accion, $seguimientoPedido)->tojson();
        }

        $request = DragonTailOrderRequest::from($order[0]['orderTime'], $restaurantId, $order, $orderItems)->tojson();
        $request = convertirCodificacionUTF8($request);

        if (isset($request['orders']) AND count($request['orders']) > 0)
            foreach ($request['orders'] as $clave => $valor) {
                if (isset($valor['orderTotal']) AND is_string($valor['orderTotal']) AND trim($valor['orderTotal']) != '') 
                    $valor['orderTotal'] = (float) $valor['orderTotal'];
                    
                if (isset($valor['source']) AND is_string($valor['source']) AND trim($valor['source']) != '') 
                    $valor['source'] = (float) $valor['source'];
                    
                if (isset($valor['cash']) AND is_string($valor['cash']) AND trim($valor['cash']) != '') 
                    $valor['cash'] = (float) $valor['cash'];
                   
                $request['orders'][$clave] = $valor;
            }


        if (isset($request['orderItems']) AND count($request['orderItems']) > 0)
            foreach ($request['orderItems'] as $clave => $valor) 
                if (isset($valor['quantity']) AND is_string($valor['quantity']) AND trim($valor['quantity']) != '') {
                    $valor['quantity'] = (float) $valor['quantity'];
                    $request['orderItems'][$clave] = $valor;
                }

        $payload = array(
            "cdn_id"      => (int) $cadenaId,
            "rst_id"      => (int) $restaurantId,
            "medio"       => "DRAGONTAIL",
            "url"         => $urlDragonTail,
            "token"       => $token,
            "trackingId"  => (float) $seguimientoPedido,
            "channelName" => $orderData['medio'],
            "codigoApp"   => $codApp,
            "data"        => $request,
        );
        $body = json_encode($payload);

        $request = new Request;
        $endPoint = DragonTailApiService::getEndPoint('createOrder');
        $peticion = $endPoint["method"].' crear orden';
        
        $request->headers = array(
            CURLOPT_URL           => $endPoint["url"],
            CURLOPT_CUSTOMREQUEST => $endPoint["method"],
            CURLOPT_POSTFIELDS    => $body,
            CURLOPT_HTTPHEADER    => array("Content-Type: application/json")
        );

        //Clase genérica para el consumo de REST
        $callREST = new CallREST;
        $response = $callREST->call($request, 60);

        DragonTailConfig::saveAuditoria($endPoint["url"], $peticion, $response->httpStatus, $body);
        $data = json_decode($response->data);

        if (json_encode($data->status) == 400) {
            return json_encode($data->response->mensaje, JSON_UNESCAPED_UNICODE);
        }

        if ($response->httpStatus == 400) {
            return json_encode($response->error, JSON_UNESCAPED_UNICODE);
        }

        if ($response->httpStatus == 200 || $response->httpStatus == 202) {
            if ($seguimientoPedido != $orderData['seguimientoPedido']) {
                DragonTailConfig::updateSeguimientoPedido($seguimientoPedido, $orderData['cod_cabeceraApp']);
            }
            DragonTailConfig::updateResponseToDB(json_encode($data->jsonResult->message, JSON_UNESCAPED_UNICODE),
                $orderData['cod_cabeceraApp']);
            return json_encode($data->jsonResult->message, JSON_UNESCAPED_UNICODE);
        }
        throw new GeneralException(json_encode($response->error, JSON_UNESCAPED_UNICODE));
    } catch (GeneralException $e) {
        DragonTailConfig::saveAuditoria("DRAGONTAIL", '', -1, $e->getTraceAsString());
        return $e->errorMessage();
    }
}

function addCeroToPosition($number) {
    $length = strlen(strval($number));
    if ($length == 1) {
        return '00' . $number;
    } elseif ($length == 2) {
        return '0' . $number;
    } elseif ($length == 3) {
        return $number;
    }
}

function findMainItemPosition($items, $belongs){
    foreach ($items as $item ) {
        if ($item['codModificador'] == $belongs & $item['mainItem'] == 'PRINCIPAL') {
            return $item['position'];
        }
    }
}

function addPositionPrincipal($items) {
    $index = 1;
    $result = array();
    foreach ($items as $item ) {
        if ($item['mainItem'] == 'PRINCIPAL') {
            $item['position'] = addCeroToPosition($index);
            $index++;
        }
        
        $result[] = $item;
    }
    return $result;
}

function addPositionOther($items) {
    $index = 1;
    $result = array();
    foreach ($items as $item) {
        if ($item['mainItem'] != 'PRINCIPAL') {
            $item['position'] = findMainItemPosition($items, $item['belongs']) . addCeroToPosition($index);
            $index++;
        }

        $result[] = $item;
    }
    return $result;
}

function seguimientoPedido($cadenaId, $restaurantId, $orderData, $codApp, $medio) {
    $saleType = Clase_dragonTailOrders::getSaleType($cadenaId, $restaurantId, $medio, $codApp);

    if ($saleType == null) {
        return null;
    } else if ($saleType == 2) {
        if (isset($orderData['seguimientoPedido'])) {
            $segPedido = substr($orderData['seguimientoPedido'], 0, 1) !== '2' ? '2' . $orderData['seguimientoPedido']
                : $orderData['seguimientoPedido'];
        } else {
            $segPedido = '3' . $orderData['cod_cabeceraApp'];
        }
    } else {
        $segPedido = '3' . $orderData['cod_cabeceraApp'];
    }
    $partes = str_split($segPedido, 10);
    $numeros = array_map('intval', $partes);
    return implode("", $numeros);
}

function convertirCodificacionUTF8(mixed $dato): mixed {
    if (isset($dato))
        if (is_array($dato) AND count($dato) > 0):
            foreach ($dato as $clave => $valor) 
                $dato[$clave] = convertirCodificacionUTF8($valor);
        elseif (is_string($dato) AND trim($dato) != ''):
            $dato = mb_convert_encoding($dato, 'UTF-8');
        endif;

    return $dato;
}