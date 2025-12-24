<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . "..") . $ds;

include("../system/conexion/clase_sql.php");
include_once "clase_webservice.php";
include_once "clase_webservice.php";
include_once "{$base_dir}{$ds}resources{$ds}models{$ds}webservices{$ds}Request.php";
include_once "{$base_dir}{$ds}resources{$ds}models{$ds}webservices{$ds}Response.php";
include_once "{$base_dir}{$ds}resources{$ds}models{$ds}webservices{$ds}CallREST.php";



class  Payvalida extends sql
{

    public $idCadena;
    public $idRestaurante;
    public $serviceUrl;


    function __construct($idCadena, $idRestaurante)
    {
        $this->idCadena = $idCadena;
        $this->idRestaurante = $idRestaurante;
        $this->serviceUrl = new webservice();
        parent::__construct();
    }

    function consultaSaldo($numeroTarjeta)
    {

        $this->request = new Request;
        $this->response = new Response;


        $tarjetaRegistrada = $this->gerenteTarjetaValida($numeroTarjeta);

        if (isset($tarjetaRegistrada) &&  isset($tarjetaRegistrada->response) && $tarjetaRegistrada->response == 'SUCCESS') {
            $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "PAYVALIDA", "CONSULTAR");
            $merchant = $this->merchant();

            if (isset($merchant) && $merchant != 'ERROR') {

                $hashFixed = $this->fixedHash();

                if (isset($hashFixed) && $hashFixed != 'ERROR') {

                    $tiempo = (new DateTime())->getTimestamp();
                    $checksum = hash('sha512', $merchant . $numeroTarjeta . $this->idRestaurante . $tiempo . $hashFixed);

                    $this->request->url = $urlServicio["urlwebservice"] . '/' . $merchant . '?cardnumber=' . $numeroTarjeta . '&locationid=' . $this->idRestaurante . '&amp;timestamp=' . $tiempo . '&checksum=' . $checksum;

                    $this->request->timeout = 30;
                    $this->request->headers = array(
                        CURLOPT_URL => $this->request->url,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 25,
                        CURLOPT_CONNECTTIMEOUT => 25,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_AUTOREFERER => true,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_HTTPHEADER => array(
                            "Content-Type: application/json"
                        )
                    );

                    //Clase genérica para el consumo de REST
                    $this->callREST = new CallREST;
                    $this->response = $this->callREST->call($this->request);

                    $data = json_decode($this->response->data);

                    return json_encode($data);
                } else {
                    $arr = array('status' => 'ERROR', "message" => "ERROR -  No se encuentra configurara la politica PAYVALIDA FIXED_HASH en la coleccion WS CONFIGURACIONES");
                    print json_encode($arr);
                }
            } else {
                $arr = array('status' => 'ERROR', "message" => "ERROR -  No se encuentra configurara la politica PAYVALIDA MERCHANT en la coleccion WS CONFIGURACIONES");
                print json_encode($arr);
            }
        } else {
            $arr = array('status' => 'ERROR', "message" => "ERROR -  Tarjeta no registrada en sistema GERENTE");
            print json_encode($arr);
        }
    }


    function construccionJson($objetoCobro)
    {

        return '{
            "cardnumber":' . $objetoCobro->cardnumber . ',
            "amount":' . $objetoCobro->amount . ',
            "currency": "' . $objetoCobro->currency . '",
            "merchant": "' . $objetoCobro->merchant . '",
            "locationid": "' . $objetoCobro->locationid . '",
            "orderid": "' . $objetoCobro->orderid . '",
            "description": "' . $objetoCobro->description . '",
            "checksum": "' . $objetoCobro->checksum . '"
        }';
    }

    function cobrar($url, $objetoCobro, $numeroTarjeta)
    {
        try {

            $tarjetaRegistrada = $this->gerenteTarjetaValida($numeroTarjeta);

            if (isset($tarjetaRegistrada) &&  isset($tarjetaRegistrada->response) && $tarjetaRegistrada->response == 'SUCCESS') {


                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $objetoCobro);
                $result = curl_exec($ch);
                curl_close($ch);

                return $result;
            } else {
                $result = array('status' => 'ERROR', "message" => "ERROR -  Tarjeta no registrada en sistema GERENTE");
                return  json_encode($result);
            }
        } catch (Exception $e) {
            return '{"codigoRespuesta": "ERROR", "glosaRespuesta":"SERVICIO NO DISPONIBLE"}';
        }
    }

    function anular($url)
    {
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50);
            curl_setopt($ch, CURLOPT_TIMEOUT, 190);
            $result = curl_exec($ch);
            curl_close($ch);

            return $result;
        } catch (Exception $e) {
            return '{"codigoRespuesta": "ERROR", "glosaRespuesta":"SERVICIO NO DISPONIBLE"}';
        }
    }

    function urlCobrar()
    {
        $respuesta = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "PAYVALIDA", "COBRAR");

        if (isset($respuesta) && isset($respuesta["urlwebservice"])) {
            return $respuesta["urlwebservice"];
        } else {
            return '';
        }
    }

    function urlAnular()
    {
        $respuesta = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "PAYVALIDA", "ANULAR");

        if (isset($respuesta) && isset($respuesta["urlwebservice"])) {
            return $respuesta["urlwebservice"];
        } else {
            return '';
        }
    }

    function merchant()
    {
        $this->fn_ejecutarquery("SELECT [config].[fn_ColeccionCadena_VariableV](" . $this->idCadena . ", 'WS CONFIGURACIONES', 'PAYVALIDA MERCHANT')");
        $row = $this->fn_leerarreglo();

        if (isset($row) && isset($row[0])) {
            return $row[0];
        } else {
            return 'ERROR';
        }
    }

    function fixedHash()
    {
        $this->fn_ejecutarquery("SELECT [config].[fn_ColeccionCadena_VariableV](" . $this->idCadena . ", 'WS CONFIGURACIONES', 'PAYVALIDA FIXED_HASH')");
        $row = $this->fn_leerarreglo();

        if (isset($row) && isset($row[0])) {
            return $row[0];
        } else {
            return 'ERROR';
        }
    }

    function obtenerCodigoUnicoTransaccion($cfacId, $monto)
    {
        $this->fn_ejecutarquery("SELECT TOP 1  SWT_Respuesta_AutorizacionVarchar2
		FROM dbo.SWT_Respuesta_Autorizacion 
		WHERE rsaut_movimiento =" . $cfacId . " AND 
		SWT_Respuesta_AutorizacionVarchar1= 'APROBADO' AND 
		SWT_Respuesta_AutorizacionDecimal1 = " . $monto . "
		ORDER BY rsaut_id desc");
        $row = $this->fn_leerarreglo();

        if (isset($row) && isset($row[0])) {
            return $row[0];
        } else {
            return 'ERROR';
        }
    }

    function obtenerCodigoUnicoTransaccionPorFormaPago($cfacId, $idFormaPago)
    {
        $consulta = "EXEC [facturacion].[codigoUnicoTransaccionPayvalidaPorFormaPago]  '" . $cfacId . "', '"  . $idFormaPago . "' ";
        $ejecucion = $this->fn_ejecutarquery3($consulta);
        return $ejecucion;
    }

    /**
     * Insertar en la tabla SWT_Requerimiento_Autorizacion
     */
    function insertaRequerimientoAutorizacion($ip, $trama, $cfacId,  $tipoCobro, $monto, $urlConsumo)
    {
        $consulta = " [facturacion].[IAE_SWT_InsertaRequerimientoAutorizacionPayvalida]  '" . $ip . "', '" . $trama . "' , '" .
            $cfacId . "' , '"  . $tipoCobro . "' ,  '" .  $urlConsumo . "' ," . $monto;

        $ejecucion = $this->fn_ejecutarquery3($consulta);
        return $ejecucion;
    }

    function actualizaRequerimientoAutorizacion($id, $trama)
    {
        $actualizacion = $this->fn_ejecutarquery("UPDATE SWT_Requerimiento_Autorizacion SET rqaut_trama ='" . $trama . "' WHERE rqaut_id = " . $id);
        return $actualizacion;
    }


    /**
     * Inserta Requerimiento Anulacion
     */
    function insertaRequerimientoAnulacion($ip,  $trama, $cfacId, $monto, $idFormaPagoFactura)
    {

        $consulta = "EXEC [facturacion].[IAE_SWT_InsertaRequerimientoAutorizacionAnulacioPayvalida]  '" . $ip . "', '"  . $trama . "' , '" .
            $cfacId . "' , " . $monto . " , '" . $idFormaPagoFactura . "' ";

        $ejecucion = $this->fn_ejecutarquery3($consulta);
        return $ejecucion;
    }

    /**
     * Inserta en la tabla SWT_Respuesta_Autorizacion
     */
    function insertaRespuestaAutorizacion(
        $trama,
        $fechaAprobacion,
        $horaAprobacion,
        $numeroTarjeta,
        $cfacId,
        $idRestaurante,
        $ipEstacion,
        $dominio,
        $idUsuario,
        $idCadena,
        $monto,
        $glosaRespuesta,
        $codigoUnicoTransaccion,
        $code,
        $status
    ) {

        $consulta = " EXEC [facturacion].[IAE_SWT_InsertaRespuestaAutorizacionPayvalida]   '" . $trama . "', '" . $fechaAprobacion . "', '" . $horaAprobacion . "' , '" .
            $numeroTarjeta . "' , '" . $cfacId . "' ,'"  . $idRestaurante . "' , '" . $ipEstacion . "' ,'" . $dominio . "' , '" . $idUsuario . "' ," . $idCadena . " , " .
            $monto . " , '" . $glosaRespuesta . "' , '" . $codigoUnicoTransaccion . "' , '" . $code . "' , '" . $status . "'";
        $ejecucion = $this->fn_ejecutarquery3($consulta);
        return $ejecucion;
    }



    /**
     * Manejo de Logs
     */
    function insertaLog($mensaje)
    {
        $dataToLog = array(
            date("Y-m-d H:i:s"),
            $_SERVER['REMOTE_ADDR'],
            $mensaje
        );

        $data = implode(" - ", $dataToLog);
        $data .= PHP_EOL;
        $path = '';

        if (stripos(strtolower(PHP_OS), 'windows') !== false) {
            $path = 'C:\impresionKFC\logtarjetas';
        } else if (stripos(strtolower(PHP_OS), 'linux') !== false){
            $path = '../impresionKFC/logtarjetas';
        }else{
            $path = 'impresionKFC\logtarjetas';
        }

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $pathToFile =  $path. '/LogEventos'.  $_SESSION['EstacionNombre'] . '_' . date('dmy') . '.log';
        file_put_contents($pathToFile, $data, FILE_APPEND);
    }

    function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        } elseif (is_string($mixed)) {
            return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
        }
        return $mixed;
    }

    function enmascarar($numeroTarjeta)
    {
        $l = strlen($numeroTarjeta) / 3;
        $e = floor($l);


        if ($e * 3 == strlen($numeroTarjeta)) {
            return  substr($numeroTarjeta, 0, $e) . $this->encriptar(substr($numeroTarjeta, $e, $e)) . substr($numeroTarjeta, $e * 2, $e);
        } else {

            $f = strlen($numeroTarjeta) - ($e * 3);

            return substr($numeroTarjeta, 0, $e) . $this->encriptar(substr($numeroTarjeta, $e, $e)) . substr($numeroTarjeta, $e * 2, $e + $f);
        }
    }

    function encriptar($palabra)
    {

        $respuesta = '';

        for ($i = 0; $i <=  strlen($palabra); $i++) {
            $respuesta .= 'X';
        }

        return $respuesta;
    }


    /**
     * Conexion con el sistema Gerente
     */
    function gerenteTarjetaValida($numeroTarjeta)
    {
        $this->request = new Request;
        $this->response = new Response;

        $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "PAYVALIDA GERENTE", "VALIDACION TARJETA");

        $this->request->url = $urlServicio["urlwebservice"] . '/' . substr($numeroTarjeta, -10);

        $this->request->timeout = 30;
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_CONNECTTIMEOUT => 25,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            )
        );

        //Clase genérica para el consumo de REST
        $this->callREST = new CallREST;
        $this->response = $this->callREST->call($this->request);

        if (isset($this->response) && isset($this->response->data)) {
            $data = json_decode($this->response->data);
        } else {
            $data = null;
        }

        return $data;
    }
}
