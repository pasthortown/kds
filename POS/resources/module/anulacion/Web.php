<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . "..") . $ds;
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}CallREST.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Request.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Response.php";
include_once "{$base_dir}{$ds}../../system{$ds}conexion{$ds}clase_sql.php";
include_once "{$base_dir}{$ds}../../clases{$ds}app.Cadena.php";
include_once "{$base_dir}{$ds}../../clases{$ds}clase_webservice.php";

class Web {

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

    function cambioEstado( $idFactura, $estado, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion ) {
        $this->request = new Request;
        $this->response = new Response;

        $datosFactura = $this->config->obtenerCodigoApp( $idFactura );
        $codigoApp = $datosFactura["codigo_app"];
        $client = $this->config->cargarMerchantIdWeb($this->idCadena);


        $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "WEB", "CAMBIO ESTADO");
        $this->request->url = ($urlServicio["urlwebservice"].$client["merchant_Id"]); // Concatenar url con el merchantId

        $autentication = [];
        $autentication["identity_token"] = '';
        $lc_sql = "EXEC config.USP_ObtenerIdentityTokenDuna ".$this->idCadena;

        try {
            $this->config->fn_ejecutarquery($lc_sql);
            while ($row = $this->config->fn_leerarreglo()) {
                $autentication["identity_token"] = $row['identity_token'];
            }
            $autentication['registros'] = $this->config->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }

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
                "order" => $codigoApp, // "R024401877"
                "status" => $estado // "recibido"  -- "Pedido Recibido"
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