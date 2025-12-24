<?php
include_once('./modelos/Transacciones.php');
include_once('./QPM_Service.php');
include_once('./modelos/DTD.php');

class Transaccion
{
    private $arrayProductEvent;
    public $idCabeceraOrdenPedidoQPM;
    public $rst_id;


    function obtenerArrayProductEvent()
    {
        return $this->arrayProductEvent;
    }

    function setearArrayProductEvent($arrayProductEvent)
    {
        $this->arrayProductEvent = $arrayProductEvent;
    }

    public function txtFormateado($texto, $campo = '')
    {
        $exepciones = [
            '<' => '%26lt;',
            '>' => '%26gt;',
            '"' => '%26quot;',
            "'" => '%26apos;',
            '&' => '%26amp;',
            '%' => '%25'
        ];

        $longPermitida = [
            'EventID' => 128,
            'DeviceID' => 128,
            'ServerID' => 200,
            'Name' => 64
        ];


        foreach ($exepciones as $key => $value) {
            if (strpos($texto, $key)) {
                $texto = str_replace($key, $value, $texto);
            }
        }

        if (array_key_exists($campo, $longPermitida) && strlen($texto) > $longPermitida[$campo]) {
            $texto = substr($texto, 0, $longPermitida[$campo]);
        }

        return $texto;
    }

    public function generarDatosTransaccionVendida($parametros)
    {
        try {
            $transacciones = new TransaccionesQPM;

            $cabeceraFactura = $transacciones->consultarFactura($parametros);
            $cabeceraFactura = json_decode($cabeceraFactura, true);

            $datosFactura = array();
            if ($cabeceraFactura['str'] > 0) {
                $t = microtime(true);
                $micro = sprintf("%07d", ($t - floor($t)) * 10000000);
                $hoy = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
                $creacion = explode(" ", $hoy->format("Y-m-d H:i:s:u"));
                $fecha = $creacion[0];
                $hora = $creacion[1];

                $utc = new DateTime();
                $time_zone = date_default_timezone_get();
                $current   = timezone_open($time_zone);
                $offset_s  = timezone_offset_get($current, $utc);
                $offset_h  = $offset_s / (60 * 60);

                $offset_h  = (string) $offset_h;
                if (strpos($offset_h, '-') === FALSE) {
                    $offset_h = '+0' . $offset_h;
                }
                if ($offset_h < 0  && $offset_h > -10) {
                    str_replace("-", "", $offset_h);
                    $offset_h = '-0' . $offset_h * (-1);
                }
                for ($i = 0; $i < $cabeceraFactura['str']; $i++) {
                    $this->idCabeceraOrdenPedidoQPM = $cabeceraFactura[$i]['IDCabeceraOrdenPedido'];
                    $this->rst_id = $parametros['rst_id'];

                    $SubTotal = 0;
                    $Tax = 0;
                    $Total = 0;
                    $currency = $this->txtFormateado($cabeceraFactura[$i]['currency']);

                    $datosFactura['EventID'] = $this->txtFormateado($cabeceraFactura[$i]['cfac_id'], 'EventID');
                    $datosFactura['DeviceID'] = $this->txtFormateado($cabeceraFactura[$i]['IDEstacion'], 'DeviceID');
                    $datosFactura['ServerID'] = $this->txtFormateado($cabeceraFactura[$i]['cajero'], 'ServerID');
                    $datosFactura['Date'] = $fecha;
                    $datosFactura['Time'] = $hora;
                    $datosFactura['ProductEventType'] = 'Sold';
                    $datosFactura['PosSystemIdentifier'] = $cabeceraFactura[$i]['PosSystemIdentifier'];
                    $datosFactura['Stage'] = 'ORDER_DELIVERED';
                    $datosFactura['NonDepleting'] = $cabeceraFactura[$i]['NonDepleting'] == 1 ? true : false;

                    $parametrosDetalle = ['2', $parametros['idTransaccion'], $parametros['rst_id'], $parametros['cdn_id']];
                    $detalleFactura = $transacciones->consultarDetalleFactura($parametrosDetalle);
                    $detalleFactura = json_decode($detalleFactura, true);
                    if ($detalleFactura['str'] > 0) {
                        for ($i = 0; $i < $detalleFactura['str']; $i++) {
                            $cantidad = intval($detalleFactura[$i]['cantProducto']);
                            $precioUnitario = floatval($detalleFactura[$i]['precio_unitario']);
                            $precioUnitario = round($precioUnitario, 4);
                            $precioTotalProducto = $cantidad * $precioUnitario;
                            $SubTotal += $precioTotalProducto;
                            $iva = floatval($detalleFactura[$i]['iva']);
                            $iva = round($iva, 4);
                            $Tax += $iva;
                            $detalleProducto = [];
                            $detalleProducto['ID'] = strval($detalleFactura[$i]['plu_id']);
                            $detalleProducto['Name'] = $this->txtFormateado($detalleFactura[$i]['nombre'], 'Name');
                            $detalleProducto['Qty'] = $cantidad;
                            $detalleProducto['UnitCost'] = number_format($precioUnitario, 2);
                            $detalleProducto['ItemTotal'] = number_format($precioTotalProducto, 2);
                            array_push($datosFactura,  $detalleProducto);
                        }
                    }
                    $SubTotal = number_format($SubTotal, 2);
                    $Tax = number_format($Tax, 2);
                    $Total = number_format(($SubTotal + $Tax), 2);

                    $datosFactura['SubTotal'] = $SubTotal;
                    $datosFactura['Tax'] = $Tax;
                    $datosFactura['Total'] = $Total;
                    $datosFactura['Currency'] = $currency;
                }
            }
            $this->setearArrayProductEvent($datosFactura);
        } catch (Exception $e) {
            echo 'Fallo: ',  $e->getMessage(), "\n";
        }
    }

/**
     *  @fn generarDatosTransaccionVendidaCupon
     * 
     *  @brief Generar una factura temporal con los datos del cupon
     * 
     *  @author Alejandro Salas
     *  @param array Parametros del cupon y demas validadores de caja, restaurante, cadena.
     *  @return array Datos de la factura temporal creada por el cupon 
     */
    public function generarDatosTransaccionVendidaCupon($parametros)
    {
        try {
            $transacciones = new TransaccionesQPM;

            $cabeceraFactura = $transacciones->consultarCupon($parametros);
            $cabeceraFactura = json_decode($cabeceraFactura, true);

            $datosFactura = array();
            if ($cabeceraFactura['str'] > 0) {
                $t = microtime(true);
                $micro = sprintf("%07d", ($t - floor($t)) * 10000000);
                $hoy = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
                $creacion = explode(" ", $hoy->format("Y-m-d H:i:s:u"));
                $fecha = $creacion[0];
                $hora = $creacion[1];

                $utc = new DateTime();
                $time_zone = date_default_timezone_get();
                $current   = timezone_open($time_zone);
                $offset_s  = timezone_offset_get($current, $utc);
                $offset_h  = $offset_s / (60 * 60);

                $offset_h  = (string) $offset_h;
                if (strpos($offset_h, '-') === FALSE) {
                    $offset_h = '+0' . $offset_h;
                }
                if ($offset_h < 0  && $offset_h > -10) {
                    str_replace("-", "", $offset_h);
                    $offset_h = '-0' . $offset_h * (-1);
                }
                for ($i = 0; $i < $cabeceraFactura['str']; $i++) {
                    $this->idCabeceraOrdenPedidoQPM = $cabeceraFactura[$i]['IDCabeceraOrdenPedido'];
                    $this->rst_id = $parametros['rst_id'];

                    $SubTotal = 0;
                    $Tax = 0;
                    $Total = 0;
                    $currency = $this->txtFormateado($cabeceraFactura[$i]['currency']);

                    $datosFactura['EventID'] = $this->txtFormateado($cabeceraFactura[$i]['cfac_id'], 'EventID');
                    $datosFactura['DeviceID'] = $this->txtFormateado($cabeceraFactura[$i]['IDEstacion'], 'DeviceID');
                    $datosFactura['ServerID'] = $this->txtFormateado($cabeceraFactura[$i]['cajero'], 'ServerID');
                    $datosFactura['Date'] = $fecha;
                    $datosFactura['Time'] = $hora;
                    $datosFactura['ProductEventType'] = 'Sold';
                    $datosFactura['PosSystemIdentifier'] = $cabeceraFactura[$i]['PosSystemIdentifier'];
                    $datosFactura['Stage'] = 'ORDER_DELIVERED';
                    $datosFactura['NonDepleting'] = $cabeceraFactura[$i]['NonDepleting'] == 1 ? true : false;

                    $parametrosDetalle = ['2', $parametros['idTransaccion'], $parametros['rst_id'], $parametros['cdn_id'], $parametros['detalle']];
                    $detalleFactura = $transacciones->consultarDetalleCupon($parametrosDetalle);
                    $detalleFactura = json_decode($detalleFactura, true);
                    if ($detalleFactura['str'] > 0) {
                        for ($i = 0; $i < $detalleFactura['str']; $i++) {
                            $cantidad = intval($detalleFactura[$i]['cantProducto']);
                            $precioUnitario = floatval($detalleFactura[$i]['precio_unitario']);
                            $precioUnitario = round($precioUnitario, 4);
                            $precioTotalProducto = $cantidad * $precioUnitario;
                            $SubTotal += $precioTotalProducto;
                            $iva = floatval($detalleFactura[$i]['iva']);
                            $iva = round($iva, 4);
                            $Tax += $iva;
                            $detalleProducto = [];
                            $detalleProducto['ID'] = strval($detalleFactura[$i]['plu_id']);
                            $detalleProducto['Name'] = $this->txtFormateado($detalleFactura[$i]['nombre'], 'Name');
                            $detalleProducto['Qty'] = $cantidad;
                            $detalleProducto['UnitCost'] = number_format($precioUnitario, 2);
                            $detalleProducto['ItemTotal'] = number_format($precioTotalProducto, 2);
                            array_push($datosFactura,  $detalleProducto);
                        }
                    }
                    $SubTotal = number_format($SubTotal, 2);
                    $Tax = number_format($Tax, 2);
                    $Total = number_format(($SubTotal + $Tax), 2);

                    $datosFactura['SubTotal'] = $SubTotal;
                    $datosFactura['Tax'] = $Tax;
                    $datosFactura['Total'] = $Total;
                    $datosFactura['Currency'] = $currency;
                }
            }
            $this->setearArrayProductEvent($datosFactura);
        } catch (Exception $e) {
            echo 'Fallo: ',  $e->getMessage(), "\n";
        }
    }



    public function datosAnularTransaccionQPM($parametros)
    {
        try {
            $transacciones = new TransaccionesQPM;

            $cabeceraFactura = $transacciones->consultarFactura($parametros);
            $cabeceraFactura = json_decode($cabeceraFactura, true);

            $datosFactura = array();

            if ($cabeceraFactura['str'] > 0) {
                $t = microtime(true);
                $micro = sprintf("%07d", ($t - floor($t)) * 10000000);
                $hoy = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
                $creacion = explode(" ", $hoy->format("Y-m-d H:i:s:u"));
                $fecha = $creacion[0];
                $hora = $creacion[1];

                $utc = new DateTime();
                $time_zone = date_default_timezone_get();
                $current   = timezone_open($time_zone);
                $offset_s  = timezone_offset_get($current, $utc);
                $offset_h  = $offset_s / (60 * 60);

                $offset_h  = (string) $offset_h;
                if (strpos($offset_h, '-') === FALSE) {
                    $offset_h = '+0' . $offset_h;
                }
                if ($offset_h < 0  && $offset_h > -10) {
                    str_replace("-", "", $offset_h);
                    $offset_h = '-0' . $offset_h * (-1);
                }

                for ($i = 0; $i < $cabeceraFactura['str']; $i++) {


                    $SubTotal = 0;
                    $Tax = 0;
                    $Total = 0;
                    $currency = $this->txtFormateado($cabeceraFactura[$i]['currency']);

                    $datosFactura['EventID'] = $this->txtFormateado($cabeceraFactura[$i]['cfac_id'], 'EventID');
                    $datosFactura['DeviceID'] = $this->txtFormateado($cabeceraFactura[$i]['IDEstacion'], 'DeviceID');
                    $datosFactura['ServerID'] = $this->txtFormateado($cabeceraFactura[$i]['cajero'], 'ServerID');
                    $datosFactura['Date'] = $fecha;
                    $datosFactura['Time'] = $hora;
                    $datosFactura['ProductEventType'] = 'Sold';
                    $datosFactura['PosSystemIdentifier'] = $cabeceraFactura[$i]['PosSystemIdentifier'];
                    $datosFactura['Stage'] = 'ORDER_DELIVERED';
                    $datosFactura['NonDepleting'] = $cabeceraFactura[$i]['NonDepleting'] == 1 ? true : false;

                    $SubTotal = number_format($SubTotal, 2);
                    $Tax = number_format($Tax, 2);
                    $datosFactura['SubTotal'] = $SubTotal;
                    $datosFactura['Tax'] = $Tax;
                    $Total = number_format(($SubTotal + $Tax), 2);
                    $datosFactura['Total'] = $Total;
                    $datosFactura['Currency'] = $currency;
                }
            }
            $this->setearArrayProductEvent($datosFactura);
        } catch (Exception $e) {
            echo 'Fallo: ',  $e->getMessage(), "\n";
        }
    }

    public function ingresarTransaccionVendidaQPM($_parametros)
    {
        $this->generarDatosTransaccionVendida($_parametros);
        $QPM = new QPM;
        $QPM->ipTransaccionQPM = $_parametros['ipTransaccion'];
        $QPM->idTransaccionQPM = $_parametros['idTransaccion'];
        $QPM->urlQPM = $_parametros['url'];
        $QPM->parametroActivity = $_parametros['activity'];

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding = "UTF-8"?><!DOCTYPE ProductEvent><ProductEvent Version="2.0"></ProductEvent>'); //crear obj XML
        $datosProductEvent = $this->obtenerArrayProductEvent();
        $QPM->convertirArrayToXml($datosProductEvent, 'Product', $xml);
        $xmlParse = $xml->asXML();
        $DTD = new DTD;
        $productEventValido = $DTD->validarProductEventDTD($datosProductEvent);

        if ($productEventValido) {
            $QPM->consultarKitchenAdvisor($xmlParse);
            $QPM->guardarTransaccionParaAuditoria($datosProductEvent, $QPM->responseArrayFromXML, 'Transaccion Vendida', $this->idCabeceraOrdenPedidoQPM, $this->rst_id);
            echo 'Transaccion Enviada';
        } else {
            $QPM->guardarTransaccionParaAuditoria($datosProductEvent, 'No enviado', 'Fallo al ingresar la Transaccion Vendida, verifique el DTD', $this->idCabeceraOrdenPedidoQPM, $this->rst_id);
            echo 'Transaccion No Enviada, verifique el DTD';
        }
    }

 /**
     *  @fn ingresarTransaccionVendidaQPMCupon
     * 
     *  @brief Consume el endpoint de QPM y guarda un log en la tabla Auditoria_Transaccion
     * 
     *  @author Alejandro Salas
     *  @param array Parametros del cupon y demas validadores de caja, restaurante, cadena.
     *  @return string Transaccion Enviada
     */
    public function ingresarTransaccionVendidaQPMCupon($_parametros)
    {
        $this->generarDatosTransaccionVendidaCupon($_parametros);
        $QPM = new QPM;
        $QPM->ipTransaccionQPM = $_parametros['ipTransaccion'];
        $QPM->idTransaccionQPM = $_parametros['idTransaccion'];
        $QPM->urlQPM = $_parametros['url'];
        $QPM->parametroActivity = $_parametros['activity'];

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding = "UTF-8"?><!DOCTYPE ProductEvent><ProductEvent Version="2.0"></ProductEvent>'); //crear obj XML
        $datosProductEvent = $this->obtenerArrayProductEvent();
        $QPM->convertirArrayToXml($datosProductEvent, 'Product', $xml);
        $xmlParse = $xml->asXML();
        $DTD = new DTD;
        $productEventValido = $DTD->validarProductEventDTD($datosProductEvent);

        if ($productEventValido) {
            $QPM->consultarKitchenAdvisor($xmlParse);
            $QPM->guardarTransaccionParaAuditoria($datosProductEvent, $QPM->responseArrayFromXML, 'Transaccion Vendida', $this->idCabeceraOrdenPedidoQPM, $this->rst_id);
            echo 'Transaccion Enviada';
        } else {
            $QPM->guardarTransaccionParaAuditoria($datosProductEvent, 'No enviado', 'Fallo al ingresar la Transaccion Vendida, verifique el DTD', $this->idCabeceraOrdenPedidoQPM, $this->rst_id);
            echo 'Transaccion No Enviada, verifique el DTD';
        }
    }

    public function anularTransaccionVendidaQPM($_parametros)
    {
        $this->datosAnularTransaccionQPM($_parametros);
        $QPM = new QPM;
        $QPM->ipTransaccionQPM = $_parametros['ipTransaccion'];
        $QPM->idTransaccionQPM = $_parametros['idTransaccion'];
        $QPM->urlQPM = $_parametros['url'];
        $QPM->parametroActivity = $_parametros['activity'];

        $xml = new SimpleXMLElement('<?xml version="1.0"?><!DOCTYPE ProductEvent><ProductEvent Version="2.0"></ProductEvent>'); //crear obj XML
        $datosProductEvent = $this->obtenerArrayProductEvent();
        $QPM->convertirArrayToXml($this->obtenerArrayProductEvent(), 'Producto', $xml);

        $xmlParse = $xml->asXML();

        $DTD = new DTD;
        $productEventValido = $DTD->validarProductEventDTD($datosProductEvent);

        if ($productEventValido) {
            $QPM->consultarKitchenAdvisor($xmlParse);
            $QPM->guardarTransaccionParaAuditoria($datosProductEvent, $QPM->responseArrayFromXML, 'Transaccion Anulada', $this->idCabeceraOrdenPedidoQPM, $this->rst_id);
            echo 'Transaccion Enviada';
        } else {
            echo 'Transaccion No Enviada, verifique el DTD';
            $QPM->guardarTransaccionParaAuditoria($datosProductEvent, 'No enviado', 'Fallo al anular la Transaccion Vendida, verifique el DTD', $this->idCabeceraOrdenPedidoQPM, $this->rst_id);
        }
    }
}
