<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . "..") . $ds;
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}CallREST.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Request.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Response.php";
include_once "{$base_dir}{$ds}../../system{$ds}conexion{$ds}clase_sql.php";
include_once "{$base_dir}{$ds}../../clases{$ds}app.Cadena.php";
include_once "{$base_dir}{$ds}../../clases{$ds}clase_webservice.php";

class App extends sql
{



    public $idCadena;
    public $idRestaurante;
    public $timeOut;
    public $callREST;
    public $request;
    public $response;
    public $config;
    public $token;
    public $serviceUrl;
    public $expiresAt;
    public $tokenType;

    function __construct( $idCadena, $idRestaurante, $timeOut = 3)
    {
        parent::__construct();
        $this->idCadena = $idCadena;
        $this->idRestaurante = $idRestaurante;
        $this->timeOut = $timeOut;
        $this->serviceUrl = new webservice();
        $this->config = new Cadena();
        $this->tokenType = "Bearer";
        $this->expiresAt = date("Y-m-d H:i:s");
        $this->consultarToken();
    }

    function solicitarToken()
    {

        $this->request = new Request;
        $this->response = new Response;

        $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "APP", "AUTHENTICATION");
        $this->request->url = $urlServicio["urlwebservice"];
        //Obtener campos bd

        $client = $this->config->cargarConfiguracionPoliticasPorMedio($this->idRestaurante , 'APP');

        $client_id = $client["client_id"];
        $client_secret = $client["client_secret"];
        //https://api.v2.desarrollo-redbrand.com/api/transactional/token
        $this->request->timeout = $this->timeOut;
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->timeOut,
            CURLOPT_CONNECTTIMEOUT => $this->timeOut,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => json_encode(array(
                "grant_type" => "client_credentials",
                "client_id" => $client_id,
                "client_secret" => $client_secret
            )),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            )
        );

        //Clase genérica para el consumo de REST
        $this->callREST = new CallREST;
        $this->response = $this->callREST->call($this->request, $this->timeOut);
        $data = $this->response->data;
        return $data;
    }

    function cambioEstado($codigoApp, $estado)
    {

        $this->request = new Request;
        $this->response = new Response;

        $this->consultarToken();

        //$datosFactura = $this->config->obtenerCodigoApp( $idFactura );
        //$codigoApp = $datosFactura["codigo_app"];

        $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "APP", "CAMBIO ESTADO");
        $this->request->url = $urlServicio["urlwebservice"];

        $this->request->timeout = $this->timeOut;
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->timeOut,
            CURLOPT_CONNECTTIMEOUT => $this->timeOut,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => json_encode(array(
                "order_id" => $codigoApp,
                "status" => $estado
            )),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $this->token,
                "Accept-Encoding: gzip,deflate,sdch",
                "Content-Type: application/json"
            )
        );

        //Clase genérica para el consumo de REST
        $this->callREST = new CallREST;
        $this->response = $this->callREST->call($this->request, $this->timeOut);

        $data = json_decode($this->response->data);

        if (isset($data) && isset($data->status) && $data->status === "success") {
            $this->insertaAuditoria($urlServicio["urlwebservice"], 'TRADE - Cambio a Estado: ' . $estado . ', CodigoApp: ' . $codigoApp, $this->response->httpStatus, json_encode($data));
        } else {
            $this->insertaAuditoria($urlServicio["urlwebservice"], 'TRADE - Cambio a Estado: ' . $estado . ', CodigoApp: ' . $codigoApp, 'ERROR', json_encode($data). ' - '. json_encode($this->response) );
        }

        return json_encode($data);
    }

    function notificar($idFactura, $estado, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion)
    {

        $this->request = new Request;
        $this->response = new Response;

        $datosFactura = $this->config->obtenerCodigoApp($idFactura);
        $codigoApp = $datosFactura["codigo_app"];

        //$urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "APP", "CAMBIO ESTADO");
        $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "APP NOTIFICATIONS", "ANULACION");
        $this->request->url = $urlServicio["urlwebservice"];

        $this->request->timeout = $this->timeOut;
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->timeOut,
            CURLOPT_CONNECTTIMEOUT => $this->timeOut,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => json_encode(array(
                "order_id" => $codigoApp,
                "status" => $estado
            )),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $this->token,
                "Content-Type: application/json"
            )
        );

        //Clase genérica para el consumo de REST
        $this->callREST = new CallREST;
        $this->response = $this->callREST->call($this->request, $this->timeOut);

        $data = json_decode($this->response->data);

        if ($data->code === 200) {
            $anulacion = $this->config->generarNotaCredito($this->idRestaurante, $idFactura, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion);
            $data->idFactura = $anulacion["idFactura"];
            $data->idAnulacion = $anulacion["idNotaCredito"];
        }

        return json_encode($data);
    }

    function notificarTransferencia($codigo, $localOrigen, $localDestino, $usuario, $motivo, $direccion, $medio)
    {

        $validacionConsumoServicio = $this->validacionConsumoServicio($medio, 'TRANSFERENCIA');

        if(isset($validacionConsumoServicio) && $validacionConsumoServicio == 'APLICA'){

            $this->request = new Request;
            $this->response = new Response;
    
            // OBTENER RUTA
            $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "APP", "TRANSFERENCIA");
    
            $this->request->timeout = $this->timeOut;
            $this->request->headers = array(
                CURLOPT_URL => $urlServicio["urlwebservice"],
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => $this->timeOut,
                CURLOPT_CONNECTTIMEOUT => $this->timeOut,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_AUTOREFERER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POSTFIELDS => json_encode(array(
                    "order" => $codigo,
                    "from_store" => $localOrigen,
                    "to_store" => $localDestino,
                    "processed_by" => $usuario,
                    "transfer_reason" => $motivo,
                    "shipping_address" => $direccion
                )),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . $this->token,
                    "Content-Type: application/json"
                )
            );
    
            //Clase genérica para el consumo de REST
            $this->callREST = new CallREST;
            $this->response = $this->callREST->call($this->request, $this->timeOut);
    
            $this->insertaAuditoria($urlServicio["urlwebservice"], 'TRADE - TRANSFERENCIA' . ', ObjetoTransferencia: ' . json_encode(array(
                "order" => $codigo,
                "from_store" => $localOrigen,
                "to_store" => $localDestino,
                "processed_by" => $usuario,
                "transfer_reason" => $motivo,
                "shipping_address" => $direccion
            )), $this->response->httpStatus, json_encode($this->response));
    
    
            $data = json_decode($this->response->data);
    
            return $data;    

        }

    }

    function verificarExpiracion()
    {
        $flag = false;
        $file = $this->obtenerRutaArchivoToken();
        if (file_exists($file)) {
            $jsonString = file_get_contents($file);
            $tokenList = json_decode($jsonString, true);
            foreach ($tokenList as $key => $token) {
                if ($token['idAssociated'] === $this->idRestaurante) {
                    $now = date("Y-m-d H:i:s");
                    if ($token['expiresAt'] > $now) {
                        $flag = true;
                        $this->token = $token['token'];
                        $this->expiresAt = $token['expiresAt'];
                    }
                }
            }
        }
        return $flag;
    }

    function consultarToken()
    {
        if (!$this->verificarExpiracion()) {
            $data = $this->solicitarToken();
            $this->setToken($data);
        };
    }

    function setToken($data)
    {
        $dt = json_decode($data);
        if (isset($dt->access_token)) {

            $this->token = $dt->access_token;
            $this->tokenType = $dt->token_type;
            $this->expiresAt = $dt->expires_in;

            $fechaFormateada = $this->obtenerFechaVencimientoFormateada($this->expiresAt);
            $file = $this->obtenerRutaArchivoToken();
            file_exists($file)
                ? $this->actualizarArchivoJsonStore($this->idRestaurante, $this->token, $this->tokenType, $fechaFormateada)
                : $this->crearArchivoJson($this->idRestaurante, $this->token, $this->tokenType, $fechaFormateada);

            //cargo en variables de sesion
            $_SESSION["tokenApp"] = $dt->access_token;
            $_SESSION["expiresAt"] = $dt->expires_in;
        }
    }

    function tracking($objetoTracking, $medio)
    {

        $validacionConsumoServicio = $this->validacionConsumoServicio($medio, 'TRACKING');

        if(isset($validacionConsumoServicio) && $validacionConsumoServicio == 'APLICA'){
            $this->request = new Request;
            $this->response = new Response;
    
            $this->consultarToken();
    
            $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "APP", "TRACKING");
            //$urlServicio = 'https://api.v2.desarrollo-redbrand.com/api/order/tracking';
    
            $this->request->url = $urlServicio["urlwebservice"];
            //$this->request->url = $urlServicio;
    
            $this->request->timeout = $this->timeOut;
            $this->request->headers = array(
                CURLOPT_URL => $this->request->url,
                CURLOPT_ENCODING => "",
                //CURLOPT_NOBODY => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => $this->timeOut,
                CURLOPT_CONNECTTIMEOUT => $this->timeOut,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_AUTOREFERER => true,
		        CURLOPT_SSL_VERIFYHOST => FALSE,
                
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POSTFIELDS => json_encode($objetoTracking),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . $this->token,
                    "Accept-Encoding: gzip,deflate,sdch",
                    "Content-Type: application/json"
                )
            );
    
            //Clase genérica para el consumo de REST
            $this->callREST = new CallREST;
            $this->response = $this->callREST->call($this->request, $this->timeOut);
    
            $data = json_decode($this->response->data);
    
            if (isset($data) && isset($data->status) && $data->status === "success") {
                $this->insertaAuditoria($urlServicio["urlwebservice"], 'TRADE - TRACKING - Cambio a Estado: Por Asignar' . ', ObjetoTracking: ' . json_encode($objetoTracking), $this->response->httpStatus, json_encode($data));
            } else {
                $this->insertaAuditoria($urlServicio["urlwebservice"],
                 'TRADE  - TRACKING - Cambio a Estado: Por Asignar' . ', ObjetoTracking: ' . json_encode($objetoTracking),
                  'ERROR',
                   json_encode($data).''.json_encode($this->response) );
            }
    
            //printf($data);
            return json_encode($data);
        }
        return json_encode(["NO_APLICA"]);
    }


    public function insertaAuditoria($url, $peticion, $estado, $mensaje)
    {
        $query = "INSERT INTO dbo.Auditoria_EstadosApp
            ( url, peticion, estado, mensaje, fecha )
        VALUES  
            ( '$url', 'Tracking', '$estado', '$mensaje', GETDATE())
            
           EXEC [config].[IAE_Auditoria_Cambio_Estados] 1, '$url', '$peticion', '$estado', '$mensaje'  " ;
	
        $result = $this->fn_ejecutarquery($query);
        if ($result){ return true; }else{ return false; };
    }


    public function validacionConsumoServicio($medio, $servicio)
    {
        $query = "EXEC [dbo].[validacionServicioTercero] '$medio', '$servicio' ";

        if ($this->fn_ejecutarquery($query)) {
            $row = $this->fn_leerarreglo();
            if (isset($row) && isset($row['respuesta'])) {
                return $row['respuesta'];
            }
        }else{
            return  'NO_APLICA';
        }
    }


    function cambioEstadoTradePorFactura($cdn_id, $cfac_id, $estado)
    {

        $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "APP", "CAMBIO ESTADO");
        $query = "EXEC [dbo].[App_codigoApp_aplicaTrade_porFactura] $cdn_id, '$cfac_id'";
        if ($this->fn_ejecutarquery($query)) {
            $row = $this->fn_leerarreglo();

            if (isset($row) && isset($row['cambio_estado']) && $row['cambio_estado'] == 'SI') {
                return $this->cambioEstado($row['codigo_app'], $estado);
            }
            $this->insertaAuditoria($urlServicio["urlwebservice"], 'TRADE - Cambio a Estado: ' . $estado . ', CAMBIO ESTADO: NO', 'ERROR', 'POLITICAS DE CAMBIO DE ESTADO INACTIVAS, factura: ' . $cfac_id);
            $respuesta = new \stdClass;
            $respuesta->mensaje = 'Politica de Cambio de estado inactiva, factura: ' . $cfac_id;
            return json_encode($respuesta);
        } 
        $this->insertaAuditoria($urlServicio["urlwebservice"], 'TRADE - Cambio a Estado: ' . $estado . ', CodigoApp: NO EXISTE', 'ERROR', 'No existe un codigo de App asociado a la factura ' . $cfac_id);
        $respuesta = new \stdClass;
        $respuesta->mensaje = 'No existe un codigo de App asociado a la factura ' . $cfac_id;
        return json_encode($respuesta);
    }

    function obtenerRutaArchivoToken()
    {
        $archivoRaiz = __FILE__;
        $ruta = substr($archivoRaiz, 0, -7);
        $file = $ruta . 'appTokenStore.json';
        return $file;
    }

    function obtenerFechaVencimientoFormateada($segundos)
    {
        $cantidadHoras = $segundos / (60 * 60);
        $cantidadDias = $cantidadHoras / 24;
        $now = date("Y-m-d H:i:s");
        $fechaVencimientoFormateada = date("Y-m-d H:i:s", strtotime($now . "+ {$cantidadDias} days"));

        return $fechaVencimientoFormateada;
    }

    function actualizarArchivoJsonStore($idToken, $token, $tokenType, $fechaExpiracion)
    {
        $file = $this->obtenerRutaArchivoToken();
        $jsonString = file_get_contents($file);
        $tokenList = json_decode($jsonString, true);
        $editado = false;
        foreach ($tokenList as $key => $valorToken) {
            if ($valorToken['idAssociated'] === $idToken) {
                $tokenList[$key]['token'] = $token;
                $tokenList[$key]['tokenType'] = $tokenType;
                $tokenList[$key]['expiresAt'] = $fechaExpiracion;
                $editado = true;
            }
        }

        if (!$editado) {
            $nuevoToken = new stdClass();

            $nuevoToken->idAssociated = $idToken;
            $nuevoToken->token = $token;
            $nuevoToken->tokenType = $tokenType;
            $nuevoToken->expiresAt = $fechaExpiracion;

            array_push($tokenList, $nuevoToken);
        }

        $nuevoJson = json_encode($tokenList);
        file_put_contents($file, $nuevoJson);
    }

    function crearArchivoJson($idToken, $nuevoToken, $tokenType, $nuevaFechaExpiracion)
    {
        $file = $this->obtenerRutaArchivoToken();

        $tokenList[0]['idAssociated'] = $idToken;
        $tokenList[0]['token'] = $nuevoToken;
        $tokenList[0]['tokenType'] = $tokenType;
        $tokenList[0]['expiresAt'] = $nuevaFechaExpiracion;

        $json_string = json_encode($tokenList);
        file_put_contents($file, $json_string);
    }
}
