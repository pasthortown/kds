<?php
class deUnaApi
{


    function montarPago($_url, $datos_post, $apiKey, $apiSecret, $timeoutCurl)
    {
        // URL a la que deseas enviar la petición POST

        $url = $_url;

        // Los datos que deseas enviar

        // Inicializar cURL
        $ch = curl_init($url);

        // Establecer las opciones de cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Devolver la respuesta en lugar de imprimir
        curl_setopt($ch, CURLOPT_POST, true);             // Establecer el método de la petición como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos_post));  // Establecer los datos POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'x-api-key: ' . $apiKey, 'x-api-secret: ' . $apiSecret));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeoutCurl);

        // Ejecutar la petición y guardar la respuesta
        $respuesta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //echo 'Error:' . curl_error($ch);
        if ($errorNo = curl_errno($ch)) {
            $errorMsg = curl_error($ch);

            $errorString = "Error HTTP: Error Number: $errorNo - Error Message: $errorMsg";

            $dataError = json_encode(["code" => $errorNo, "message" => $errorString]);

            $resp = ["status" => $httpCode, "data" => $dataError];
            curl_close($ch);
            return json_encode($resp);
        }
        // Cerrar la conexión cURL
        curl_close($ch);
        $resp = ["status" => $httpCode, "data" => $respuesta];
        // Imprimir la respuesta
        return json_encode($resp);
    }

    function montarPagoTransId($_url, $requestId, $apiKey, $apiSecret, $timeoutCurl)
    {
        $url = $_url . "/" .  $requestId;
        $ch = curl_init($url);
        // Establecer las opciones de cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Devolver la respuesta en lugar de imprimir
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'x-api-key: ' . $apiKey, 'x-api-secret: ' . $apiSecret));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeoutCurl);
        $respuesta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($errorNo = curl_errno($ch)) {
            $errorMsg = curl_error($ch);

            $errorString = "Error HTTP: Error Number: $errorNo - Error Message: $errorMsg";

            $dataError = json_encode(["code" => $errorNo, "message" => $errorString]);

            $resp = ["status" => $httpCode, "data" => $dataError];
            curl_close($ch);
            return json_encode($resp);
        }
        // Cerrar la conexión cURL
        curl_close($ch);
        $resp = ["status" => $httpCode, "data" => $respuesta];
        // Imprimir la respuesta
        return json_encode($resp);
    }

    function desmontarPago($_url, $idSucursal, $posId, $transactionId, $empRuc, $apiKey, $apiSecret, $timeoutCurl)
    {
        // URL a la que deseas enviar la petición POST
        $url = $_url . "/" . $idSucursal . "/" . $posId . "/" . $transactionId . "/" . $empRuc;

        // Los datos que deseas enviar

        // Inicializar cURL
        $ch = curl_init($url);

        // Establecer las opciones de cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Devolver la respuesta en lugar de imprimir
        curl_setopt($ch, CURLOPT_POST, true);             // Establecer el método de la petición como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));  // Establecer los datos POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'x-api-key: ' . $apiKey, 'x-api-secret: ' . $apiSecret));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeoutCurl);

        // Ejecutar la petición y guardar la respuesta
        $respuesta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($errorNo = curl_errno($ch)) {
            $errorMsg = curl_error($ch);

            $errorString = "Error HTTP: Error Number: $errorNo - Error Message: $errorMsg";

            $dataError = json_encode(["code" => $errorNo, "message" => $errorString]);

            $resp = ["status" => $httpCode, "data" => $dataError];
            curl_close($ch);
            return json_encode($resp);
        }
        // Cerrar la conexión cURL
        curl_close($ch);

        $resp = ["status" => $httpCode, "data" => $respuesta];
        // Imprimir la respuesta
        return json_encode($resp);
    }

    function infoPago($_url, $idTransaccionReference, $apiKey, $apiSecret, $userAgent, $typeId)
    {
        // URL a la que deseas enviar la petición POST

        $url = $_url;

        // Los datos que deseas enviar
        $datos_post = array(
            'idTransacionReference' => $idTransaccionReference,
            'idType' => $typeId
        );

        // Inicializar cURL
        $ch = curl_init($url);

        // Establecer las opciones de cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Devolver la respuesta en lugar de imprimir
        curl_setopt($ch, CURLOPT_POST, true);             // Establecer el método de la petición como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos_post));  // Establecer los datos POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'x-api-key: ' . $apiKey, 'x-api-secret: ' . $apiSecret));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

        // Ejecutar la petición y guardar la respuesta
        $respuesta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($errorNo = curl_errno($ch)) {
            $errorMsg = curl_error($ch);

            $errorString = "Error HTTP: Error Number: $errorNo - Error Message: $errorMsg";

            $dataError = json_encode(["code" => $errorNo, "message" => $errorString]);

            $resp = ["status" => $httpCode, "data" => $dataError];
            curl_close($ch);
            return json_encode($resp);
        }
        // Cerrar la conexión cURL
        curl_close($ch);

        // Imprimir la respuesta
        $resp = ["status" => $httpCode, "data" => $respuesta];
        // Imprimir la respuesta
        return json_encode($resp);
    }


    function reversarPago($_url, $transferNumber, $apiSecret, $apiKey, $timeoutCurl)
    {
        // URL a la que deseas enviar la petición POST
        $url = $_url . "/" . $transferNumber;

        // Inicializar cURL
        $ch = curl_init($url);

        // Establecer las opciones de cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Devolver la respuesta en lugar de imprimir
        curl_setopt($ch, CURLOPT_POST, true);             // Establecer el método de la petición como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));  // Establecer los datos POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'x-api-key: ' . $apiKey, 'x-api-secret: ' . $apiSecret));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeoutCurl);

        // Ejecutar la petición y guardar la respuesta
        $respuesta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($errorNo = curl_errno($ch)) {
            $errorMsg = curl_error($ch);

            $errorString = "Error HTTP: Error Number: $errorNo - Error Message: $errorMsg";

            $dataError = json_encode(["code" => $errorNo, "message" => $errorString]);

            $resp = ["status" => $httpCode, "data" => $dataError];
            curl_close($ch);
            return json_encode($resp);
        }
        // Cerrar la conexión cURL
        curl_close($ch);

        $resp = ["status" => $httpCode, "data" => $respuesta];
        // Imprimir la respuesta
        return json_encode($resp);
    }

    function getTransferNumberByTransId($_url, $transId, $timeoutCurl)
    {
        // URL a la que deseas enviar la petición POST
        $url = $_url . "/" . trim($transId);

        // Inicializar cURL
        $ch = curl_init($url);

        // Establecer las opciones de cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Devolver la respuesta en lugar de imprimir
        curl_setopt($ch, CURLOPT_POST, true);             // Establecer el método de la petición como POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));  // Establecer los datos POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeoutCurl);

        // Ejecutar la petición y guardar la respuesta
        $respuesta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($errorNo = curl_errno($ch)) {
            $errorMsg = curl_error($ch);

            $errorString = "Error HTTP: Error Number: $errorNo - Error Message: $errorMsg";

            $dataError = json_encode(["code" => $errorNo, "message" => $errorString]);

            $resp = ["status" => $httpCode, "data" => $dataError];
            curl_close($ch);
            return json_encode($resp);
        }
        // Cerrar la conexión cURL
        curl_close($ch);

        $resp = ["status" => $httpCode, "data" => $respuesta];
        // Imprimir la respuesta
        return json_encode($resp);
    }



    function estadoPago($_url, $requestId, $timeoutCurl) {

    // URL a la que deseas enviar la petición POST

        $url = $_url . "/" . $requestId;
        //var_dump($url);
        // Los datos que deseas enviar
        // Inicializar cURL
        $ch = curl_init($url);
        // Establecer las opciones de cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Devolver la respuesta en lugar de imprimir
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');  // Establecer el método de la petición como GET
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeoutCurl);

        // Ejecutar la petición y guardar la respuesta
        $respuesta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //echo 'Error:' . curl_error($ch);
        if ($errorNo = curl_errno($ch)) {
            $errorMsg = curl_error($ch);

            $errorString = "Error HTTP: Error Number: $errorNo - Error Message: $errorMsg";

            $dataError = json_encode(["code" => $errorNo, "message" => $errorString]);

            $resp = ["status" => $httpCode, "data" => $dataError];
            curl_close($ch);
            return json_encode($resp);
        }
        // Cerrar la conexión cURL
        curl_close($ch);
        $resp = ["status" => $httpCode, "data" => $respuesta];
        // Imprimir la respuesta
        return json_encode($resp);
    }
}