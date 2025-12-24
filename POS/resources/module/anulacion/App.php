<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . "..") . $ds;
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}CallREST.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Request.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Response.php";
include_once "{$base_dir}{$ds}../../system{$ds}conexion{$ds}clase_sql.php";
include_once "{$base_dir}{$ds}../../clases{$ds}app.Cadena.php";
include_once "{$base_dir}{$ds}../../clases{$ds}clase_webservice.php";

class App {

    public $idCadena;
    public $idRestaurante;
    public $callREST;
    public $request;
    public $response;
    public $config;
    public $token;
    public $serviceUrl;

    function __construct( $idCadena, $idRestaurante ) {
        $this->idCadena = $idCadena;
        $this->idRestaurante = $idRestaurante;
        $this->serviceUrl = new webservice();
        $this->config = new Cadena();
    }

    function solicitarToken() {

        $this->request = new Request;
        $this->response = new Response;

        $urlServicio = $this->serviceUrl->retorna_rutaWS( $this->idRestaurante, "APP", "AUTHENTICATION" );
        $this->request->url = $urlServicio["urlwebservice"];

        //Obtener campos bd

        $client = $this->config->cargarConfiguracionPoliticas( $this->idRestaurante );
        $client_id = $client["client_id"];
        $client_secret = $client["client_secret"];

        //https://api.v2.desarrollo-redbrand.com/api/transactional/token
        $this->request->timeout = 60;
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_CONNECTTIMEOUT => 25,
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
        $this->response = $this->callREST->call($this->request);
        $data = json_decode($this->response->data);
        $this->token = $data->access_token;
    }

    function cambioEstado( $idFactura, $estado, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion ) {
        $this->request = new Request;
        $this->response = new Response;

        $datosFactura = $this->config->obtenerCodigoApp( $idFactura );
        $codigoApp = $datosFactura["codigo_app"];

        $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "APP", "CAMBIO ESTADO");
        $this->request->url = $urlServicio["urlwebservice"];

        $this->request->timeout = 60;
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_CONNECTTIMEOUT => 25,
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
        $this->response = $this->callREST->call($this->request);

        $data = json_decode($this->response->data);

        if ( $data->code === 200 ) {
            $anulacion = $this->config->generarNotaCredito($this->idRestaurante, $idFactura, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion );
            $data->idFactura = $anulacion["idFactura"];
            $data->idAnulacion = $anulacion["idNotaCredito"];
        }

        /*
        // Para pruebas usar este bloque
        $anulacion = $this->config->generarNotaCredito($this->idRestaurante, $idFactura, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion );
        $data = array(
            "code" => 200,
            "idFactura" => $anulacion["idFactura"],
            "idAnulacion" => $anulacion["idNotaCredito"]
        );
        */

        return json_encode($data);
    }

    function anularPedidoDuna( $idFactura, $estado, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion ) {
        $this->request = new Request;
        $this->response = new Response;

        $datosFactura = $this->config->obtenerCodigoApp( $idFactura );
        $codigoApp = $datosFactura["codigo_app"];

        $client = $this->config->cargarMerchantIdWeb($this->idCadena);

        $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "WEB", "CAMBIO ESTADO");
        $this->request->url = ($urlServicio["urlwebservice"].$client["merchant_Id"]); // Concatenar url con el merchantId

        $strDatosCodigoExterno = $this->config->obtenerCodigoExterno($codigoApp);
        $datosCodigoExterno= json_decode($strDatosCodigoExterno, true);
        $codigoExterno = $datosCodigoExterno[0]["codigo_externo"];

        $autentication = json_decode($this->config->obtenerTokenAutenticationDuna($this->idCadena),true);

        $urlparts = parse_url($urlServicio["urlwebservice"]);
        
        $urlValido = array_key_exists("scheme", $urlparts);

        if ($urlValido && $autentication["registros"] > 0) {
            $this->request->timeout = 60;
            $this->request->headers = array(
                CURLOPT_URL => $this->request->url,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 25,
                CURLOPT_CONNECTTIMEOUT => 25,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_AUTOREFERER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POSTFIELDS => json_encode(array(
                        "order" => $codigoExterno,
                        "status" => $estado
                )),
                CURLOPT_HTTPHEADER => array(
                    "Accept-Encoding: gzip,deflate,sdch",
                    "Content-Type: application/json",
                    "Authorization:".$autentication["identity_token"]
                )
            );
    
            //Clase genérica para el consumo de REST
            $this->callREST = new CallREST;
            $this->response = $this->callREST->call($this->request);
    
            $data = json_decode($this->response->data);
    
            if (isset($data) && isset($data->code) && $data->code === "success") {
                $this->config->guardarAuditoriaEstadosApp('1',$urlServicio["urlwebservice"], 'APP - Cambio a Estado: ' . $estado . ', CodigoApp: ' . $codigoApp.' CodigoExterno: '.$codigoExterno, $this->response->httpStatus, json_encode($data).'Codigo - status: '.$codigoApp.'-'.$estado);
            } else {
                $this->config->guardarAuditoriaEstadosApp('1',$urlServicio["urlwebservice"], 'APP - Cambio a Estado: ' . $estado . ', CodigoApp: ' . $codigoApp.' CodigoExterno: '.$codigoExterno, 'ERROR', json_encode($data). ' - '. json_encode($this->response).'Codigo - status: '.$codigoApp.'-'.$estado );
            }
            return json_encode($data);
        }else {
            $this->config->guardarAuditoriaEstadosApp('1',$urlServicio["urlwebservice"], 'APP - Cambio a Estado: ' . $estado . ', CodigoApp: ' . $codigoApp.' CodigoExterno: '.$codigoExterno, 500, "NO APLICA");
        }
        
    }

    function notificar( $idFactura, $estado, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion ) {

        $this->request = new Request;
        $this->response = new Response;

        $datosFactura = $this->config->obtenerCodigoApp( $idFactura );
        $codigoApp = $datosFactura["codigo_app"];

        //$urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "APP", "CAMBIO ESTADO");
        $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "APP NOTIFICATIONS", "ANULACION");
        $this->request->url = $urlServicio["urlwebservice"];

        $this->request->timeout = 60;
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_CONNECTTIMEOUT => 25,
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
        $this->response = $this->callREST->call($this->request);

        return $this->response->data;

    }

    function anular( $idFactura, $estado, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion,$cedula ) {

        $data = new stdClass;
        $anulacion = $this->config->generarNotaCredito($this->idRestaurante, $idFactura, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion,$cedula );
        $data->idFactura = $anulacion["idFactura"];
        $data->idAnulacion = $anulacion["idNotaCredito"];
        $data->servidorUrlApi   = $anulacion["servidorUrlApi"];
        $data->idEstacion       = $idEstacion;
        $data->codigo = 200;
        $data->mensaje = "Factura anulada correctamente.";

        return json_encode( $data );

    }

}