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
        $this->solicitarToken();
    }

    /* function solicitarToken() {

        $this->request = new Request;
        $this->response = new Response;

        $urlServicio = $this->serviceUrl->retorna_rutaWS( $this->idRestaurante, "APP", "AUTHENTICATION" );
        $this->request->url = $urlServicio["urlwebservice"];

        //Obtener campos bd

        $client = $this->config->cargarConfiguracionPoliticas( $this->idCadena );
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

        return json_encode($data);
    } */

}