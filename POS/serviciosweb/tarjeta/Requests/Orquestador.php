<?php
include_once 'CurlRetryHttp.php';

class Orquestador 
{
    private $valid_methods = ['ENVIO' => 1, 'ANULACION' => 3];
    private $curlRetry;
    private $endPoint;
    private $timeOut;

    public function __construct($url, $endpointOrquestador) {
        $this->endPoint = $endpointOrquestador;
        $defaultHeaders = [
            'type'  => $this->getSessionValue('TarjetaType'),
            'token' => $this->getSessionValue('TarjetaAccesToken'),
        ];
        $this->curlRetry = new CurlRetryHttp($url, $defaultHeaders);
        $this->timeOut = !isset($_SESSION['servicioTarjeta']['timeout']) ? 70 : $_SESSION['servicioTarjeta']['timeout'];
    }

    public function run($tipo, $dispositivo, $factura, $valor, $valorPropina, $formaPago, $codigoRestaurante, $idEstacion, $idUsuario) {
        $return = [
            'status'    => true,
            'error'     => null,
            'message'   => '',
            'payload'   => null,
            'response'  => null
        ];

        try {
            if (!isset($this->valid_methods[$tipo])) {
                throw new Exception('Tipo de transacción no válido.', 1);
            }

            $servicioTarjeta = new ServicioTarjeta();
            $dataTransaccion = $servicioTarjeta->servicioTarjetaOrquestador($tipo, $dispositivo, $factura, $valor, $valorPropina, $formaPago, $codigoRestaurante, $idEstacion, $idUsuario);

            $transaccion = new stdClass();
            $transaccion->tipo                 = (int) $this->valid_methods[$tipo];
            $transaccion->tipoMensaje          = (int) $dataTransaccion['tipoMensaje'];
            $transaccion->tipoTransaccion      = (int) $dataTransaccion['tipoTransaccion'];
            $transaccion->codigoAdquiriente    = (int) $dataTransaccion['codigoAdquiriente'];
            $transaccion->codigoDiferido       = !empty($dataTransaccion['codigoDiferido']) ? $dataTransaccion['codigoDiferido'] : null;
            $transaccion->plazoDiferido        = !empty($dataTransaccion['plazoDiferido']) ? $dataTransaccion['plazoDiferido'] : null;
            $transaccion->mesGracia            = !empty($dataTransaccion['mesGracia']) ? $dataTransaccion['mesGracia'] : null;
            $transaccion->montoTotal           = (int) round($dataTransaccion['montoTotal'] * 100);
            $transaccion->montoBaseIva         = (int) round($dataTransaccion['montoBaseIva'] * 100);
            $transaccion->montoBaseSinIva      = (int) round($dataTransaccion['montoBaseSinIva'] * 100);
            $transaccion->montoIva             = (int) round($dataTransaccion['montoIva'] * 100);
            $transaccion->impuestoServicio     = (int) round($dataTransaccion['impuestoServicio']  * 100);
            $transaccion->propinaServicio      = (int) round($dataTransaccion['propinaServicio']  * 100);
            $transaccion->montoFijo            = !empty($dataTransaccion['montoFijo']) ? $dataTransaccion['montoFijo'] : null;
            $transaccion->secuencial           = (int) !empty($dataTransaccion['secuencial']) ? $dataTransaccion['secuencial'] : 0;
            $transaccion->hora                 = $dataTransaccion['hora'];
            $transaccion->fecha                = $dataTransaccion['fecha'];
            $transaccion->numeroAutorizacion   = !empty($dataTransaccion['numeroAutorizacion']) ? $dataTransaccion['numeroAutorizacion'] : null;
            $transaccion->mid                  = $dataTransaccion['mid'];
            $transaccion->tid                  = $dataTransaccion['tid'];
            $transaccion->cid                  = $dataTransaccion['cid'];

            $log = new Log();
            $validarLog = $log->registrarLog($_SESSION['rstId'], 'SERVICIO TARJETA', json_encode($transaccion), 'PAGO - BODY');
            if($validarLog === false) {
                throw new Exception('Error registrando log pagos. Por favor, verifica e inténtalo de nuevo.', 1);
            }

            $response = $this->curlRetry->http($this->endPoint, 'POST', json_encode($transaccion), $this->timeOut);


            $responseData = json_decode($response, true);
            $registerResponse = $servicioTarjeta->servicioTarjetaOrquestadorResponse($factura, $response, $dataTransaccion['rqaut_id']);

            if (isset($registerResponse['IDGenerado'])) {
                $responseData['insertedId'] = $registerResponse['IDGenerado'];
            }

            $validarLog = $log->registrarLog($_SESSION['rstId'], 'SERVICIO TARJETA', json_encode($response), 'PAGO - RESPUESTA');

            if($validarLog === false) {
                throw new Exception('Error registrando log pagos. Por favor, verifica e inténtalo de nuevo.', 1);
            }

            if (isset($responseData['error']) && $responseData['error'] === true) {
                $return['status']  = false;
            }

            $return['payload'] = $transaccion;
            $return['response'] = $responseData;

        } catch (Exception $e) {
            $return['status']  = false;
            $return['message'] = ($e->getCode() == 1) ? $e->getMessage() : ERROR_GENERIC;
            $return['error']   = ($e->getCode() != 1) ? $e->getMessage() : null;
        }

        return $return;
    }

    private function getSessionValue($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
}