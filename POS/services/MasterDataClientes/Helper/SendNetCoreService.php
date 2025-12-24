<?php

class SendNetCoreService {

    const ENDPOINT_TOKEN = '/api/auth/token';
    const ENDPOINT_GETCLIENT = '/api/client/';
    const ENDPOINT_POSTCLIENT = '/api/client/';
    const ENDPOINT_PUTCLIENT = '/api/client/';
    const ENDPOINT_TIMEOUT = 30;

    public function getApiAuthToken($params)
    {
        try {
            $credentials = array(
                'clientID' => $params['clientid'],
                'clientSecret' => $params['clientsecret']
            );
            $header = array();
            $header[] = "Content-Type: application/json";
            $header[] = "Accept: application/json";
            $url = $params['url'].self::ENDPOINT_TOKEN;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($credentials));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_TIMEOUT, self::ENDPOINT_TIMEOUT);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::ENDPOINT_TIMEOUT);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            $response = curl_exec($ch);
            if (!$response) {
                $error = curl_error($ch);
                return ['statusCode'=>'500','success'=>'false','response'=>'Error al responder el servicio getApiAuthToken ProteccionDatosClientesEC '. $error];
            }
            curl_close($ch);
            return json_decode($response, true);
        } catch (Exception $th) {
            return ['statusCode'=>'500','success'=>'false','response'=>$th->getMessage()];
        }
    }

    public function getApiClient($policy,$parameters,$token)
    {
        try {
            $url = $policy['url'].self::ENDPOINT_GETCLIENT.$parameters['cdn_id'].'/'.$parameters['documento'];

            $header_array = array(
                'Authorization: Bearer ' . $token
            );
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_TIMEOUT, self::ENDPOINT_TIMEOUT);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (!$response || $httpCode >= 400) {
                $error = curl_error($ch);
                return ['statusCode'=>'500','success'=>'false','response'=>'Error al responder el servicio getApiClient ProteccionDatosClientesEC '.$error];
            }
            curl_close($ch);
            return json_decode($response, true);
        } catch (Exception $th) {
            return ['statusCode'=>'500','success'=>'false','response'=>$th->getMessage()];
        }
    }

    public function postApiClient($policy,$parameters,$token)
    {
        try {
            if (!empty($parameters['primerNombre'])) {
                $nombres = explode(' ', $parameters['primerNombre']);
                $count = count($nombres) / 2;
    
                $nombre = array_slice($nombres, 0, $count);
                $apellido = array_slice($nombres, $count);
    
                $parameters['primerNombre'] = implode(' ', $nombre);
                $parameters['apellidos'] = implode(' ', $apellido);
            }

            $header = array();
            $header[] = "Content-Type: application/json";
            $header[] = "Accept: application/json";
            $header[] = "Authorization: Bearer " . $token;

            $url = $policy['url'].self::ENDPOINT_POSTCLIENT;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_TIMEOUT, self::ENDPOINT_TIMEOUT);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::ENDPOINT_TIMEOUT);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (!$response || $httpCode >= 400) {
                $error = curl_error($ch);
                return ['statusCode'=>'500','success'=>'false','response'=>'Error al responder el servicio postApiClient ProteccionDatosClientesEC '. $error];
            }
            curl_close($ch);
            return json_decode($response, true);
        } catch (Exception $th) {
            return ['statusCode'=>'500','success'=>'false','response'=>$th->getMessage()];
        }
    }

    public function putApiClient($policy,$parameters,$token,$_id)
    {
        try {
            if (!empty($parameters['primerNombre'])) {
                $nombres = explode(' ', $parameters['primerNombre']);
                $count = count($nombres) / 2;
    
                $nombre = array_slice($nombres, 0, $count);
                $apellido = array_slice($nombres, $count);
    
                $parameters['primerNombre'] = implode(' ', $nombre);
                $parameters['apellidos'] = implode(' ', $apellido);
            }

            $header = array();
            $header[] = "Content-Type: application/json";
            $header[] = "Accept: application/json";
            $header[] = "Authorization: Bearer " . $token;

            $parameters['_id'] = $_id;
            $hora_unix = time();
            $parameters['fechaActualizacion'] = date('Y-m-d\TH:i:s\Z', $hora_unix);

            $url = $policy['url'].self::ENDPOINT_PUTCLIENT;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_TIMEOUT, self::ENDPOINT_TIMEOUT);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::ENDPOINT_TIMEOUT);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (!$response || $httpCode >= 400) {
                $error = curl_error($ch);
                return ['statusCode'=>'500','success'=>'false','response'=>'Error al responder el servicio postApiClient ProteccionDatosClientesEC '. $error];
            }
            curl_close($ch);
            return json_decode($response, true);
        } catch (Exception $th) {
            return ['statusCode'=>'500','success'=>'false','response'=>$th->getMessage()];
        }
    }
}