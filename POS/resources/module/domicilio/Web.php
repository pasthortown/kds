<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . "..") . $ds;
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}CallREST.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Request.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Response.php";
include_once "{$base_dir}{$ds}../../system{$ds}conexion{$ds}clase_sql.php";
include_once "{$base_dir}{$ds}../../clases{$ds}app.Cadena.php";
include_once "{$base_dir}{$ds}../../clases{$ds}clase_webservice.php";

class Web extends sql
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

    function __construct($idCadena, $idRestaurante, $timeOut = 3) {
    {
        parent::__construct();
        $this->idCadena = $idCadena;
        $this->idRestaurante = $idRestaurante;
        $this->timeOut = $timeOut;
        $this->serviceUrl = new webservice();
        $this->config = new Cadena();
    }

    function cambioEstado($codigoApp, $estado)
    {

        $this->request = new Request;
        $this->response = new Response;

        $client = $this->config->cargarMerchantIdWeb($this->idCadena);
        $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "WEB", "CAMBIO ESTADO");

        $estado = $estado["estado"];

        $this->request->url = ($urlServicio["urlwebservice"].$client["merchant_Id"]); // Concatenar url con el merchantId

        $peticion =  json_encode(array(
            "order" => $codigoApp,
            "status" => $estado
        )); //para almacenar en la auditoria

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
                "order" => $codigoApp,
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
        $this->response = $this->callREST->call($this->request, $this->timeOut);

        $data = json_decode($this->response->data);

        if (isset($data) && isset($data->code) && $data->code === "success") {
            $this->insertaAuditoria($urlServicio["urlwebservice"], 'WEB-E - Cambio a Estado: ' . $estado . ', CodigoApp: ' . $codigoApp, $this->response->httpStatus, json_encode($data).'JSON: '.$peticion);
        } else {
            $this->insertaAuditoria($urlServicio["urlwebservice"], 'WEB-E - Cambio a Estado: ' . $estado . ', CodigoApp: ' . $codigoApp, 'ERROR', json_encode($data). ' - '. json_encode($this->response).'JSON: '.$peticion );
        }

        return json_encode($data);

    }

/** 
    * @fn notificarTransferencia
    * @brief Transferir orden de pedido
    * @author Jacximir Salazar
    * @param string codigo -> Código de la orden del pedido
    * @param string localOrigen -> Código del local de origen
    * @param string localDestino -> Código del local de destino
    * @param string usuario -> Identificador del usuario
    * @param string motivo -> Motivo de la transferencia
    * @param string direccion -> Dirección de la transferencia
    * @param string medio -> Medio para transferir pedido
    * @return object Retorna respuesta de la WEB SERVICE como "success" o "error"
*/

    function notificarTransferencia($codigo,$localOrigen,$localDestino,$usuario,$motivo,$direccion,$medio)
    {
        
        $validacionConsumoServicio = $this->validacionConsumoServicio('Web-e','TRANSFERENCIA');

        if(isset($validacionConsumoServicio) && $validacionConsumoServicio == 'APLICA'){

            $this->request = new Request;
            $this->response = new Response;
            
            // OBTENER RUTA
            $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante,"WEB","TRANSFERENCIA");
            $client = $this->config->cargarParametrosNotificacionTransferenciaWeb($this->idCadena);

            $this->request->url = ($urlServicio["urlwebservice"].$client["merchant_id"].'/'.$codigo.'/transfer/'.$localOrigen.'/'.$localDestino);

            //INICIO DE CURL
            $this->request->timeout = $this->timeOut;
            $this->request->headers = array(
                CURLOPT_URL => $this->request->url,
                CURLOPT_RETURNTRANSFER => true,
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
                    "merchant-id" => $client["merchant_id"],
                    "order-id" => $codigo,
                    "current-store" => $localOrigen,
                    "new-store" => $localDestino,
                    "processed_by" => $usuario,
                    "transfer_reason" => $motivo,
                    "shipping_address" => $direccion
                )),
                CURLOPT_HTTPHEADER => array(
                    "Authorization:" . $client["identity_token"],
                    "Content-Type: application/json"
                )
            );

            //CLASE GENERICA PARA EL CONSUMO DE REST
            $this->callREST = new CallREST;
            $this->response = $this->callREST->call($this->request, $this->timeOut);

            $this->insertarParametrosAuditoria($this->request->url, 'WEB - TRANSFERENCIA' . ', ObjetoTransferencia: ' . json_encode(array(
                "merchant-id" => $client["merchant_id"],
                "order" => $codigo,
                "from_store" => $localOrigen,
                "to_store" => $localDestino,
                "processed_by" => $usuario,
                "transfer_reason" => $motivo,
                "shipping_address" => $direccion
            )),$this->response->httpStatus,$this->response->data);

            $data = json_decode($this->response->data);
            return $data;
        }
    }

    function validacionConsumoServicio($medio, $servicio)
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

        $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "WEB", "CAMBIO ESTADO");
        $query = "EXEC [dbo].[App_codigoApp_aplicaTrade_porFactura] $cdn_id, '$cfac_id'";


        if ($this->fn_ejecutarquery($query)) {
            $row = $this->fn_leerarreglo();

            if (isset($row) && isset($row['cambio_estado']) && $row['cambio_estado'] == 'SI') {
                return $this->cambioEstado($row['codigo_app'], $estado);
            }
        } else {
            $this->insertaAuditoria($urlServicio["urlwebservice"], 'WEB-E - Cambio a Estado: ' . $estado . ', CodigoApp: NO EXISTE', 'ERROR', 'No existe un codigo de App asociado a la factura ' . $cfac_id);
            $respuesta = new \stdClass;
            $respuesta->mensaje = 'No existe un codigo de App asociado a la factura ' . $cfac_id;
            return json_encode($respuesta);
        }
    }


    function     insertaAuditoria($url, $peticion, $estado, $mensaje)
    {
        $query = "INSERT INTO dbo.Auditoria_EstadosApp
            ( url, peticion, estado, mensaje, fecha )
        VALUES  
            ( '$url', '$peticion', '$estado', '$mensaje', GETDATE())";

        $result = $this->fn_ejecutarquery($query);
        if ($result){ return true; }else{ return false; };
    }

/** 
    * @fn insertarParametrosAuditoria
    * @brief Insertar los parametros para auditoría en la tabla Auditoria_EstadosApp
    * @author Jacximir Salazar
    * @param string url -> Url o Ruta de la web service a consumir
    * @param string peticion -> Parametros importantes como nombre de la transferencia y objetos
    * @param string estado -> Estado de la respuesta de la WEB SERVICE consumida (200,400)
    * @param string mensaje -> Mensaje de la WEB SERVICE consumida como satisfactorio o error
    * @return boolean Validacion 1 = Si 0 = No
*/

    function insertarParametrosAuditoria($url, $peticion, $estado, $mensaje)
    {
        $query = "EXEC [config].[IAE_InsertarAuditoria] '$url','$peticion','$estado','$mensaje'";

        $result = $this->fn_ejecutarquery($query);
        if ($result){ return true; }else{ return false; };
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


}
}