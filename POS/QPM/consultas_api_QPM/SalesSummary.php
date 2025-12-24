<?php
include_once('./QPM_Service.php');
include_once('./modelos/SalesSummary.php');
include_once('./modelos/DTD_Sales_Summary.php');

class SalesSummary
{
    private $arraySalesSummary;
    public $rst_id;


    function obtenerArraySalesSummary()
    {
        return $this->arraySalesSummary;
    }

    function setearArraySalesSummary($arraySalesSummary)
    {
        $this->arraySalesSummary = $arraySalesSummary;
    }

    function txtFormateado($texto)
    {
        $exepciones = [
            '<' => '%26lt;',
            '>' => '%26gt;',
            '"' => '%26quot;',
            "'" => '%26apos;',
            '&' => '%26amp;',
            '%' => '%25'
        ];

        foreach ($exepciones as $key => $value) {
            if (strpos($texto, $key)) {
                $texto = str_replace($key, $value, $texto);
            }
        }

        return $texto;
    }

    public function generarDatosSalesSummary($parametros)
    {
        try {
            $transacciones = new SalesSummaryQPM;

            $salesSummary = $transacciones->consultarSalesSummary($parametros);
            $this->rst_id = $parametros['rst_id'];
            $salesSummary = json_decode($salesSummary, true);

            $datosSalesSummary = array();
            $cantidadFilas = $salesSummary['str'];

            if ($cantidadFilas > 0) {
                for ($i = 0; $i < $cantidadFilas; $i++) {

                    $currency = $this->txtFormateado($salesSummary[$i]['moneda']);

                    $datosSalesSummary['Date'] = $salesSummary[$i]['fecha_apertura'];
                    $datosSalesSummary['SalesTotal'] = $salesSummary[$i]['venta_bruta'];
                    $datosSalesSummary['Currency'] = $currency;
                }
            }
            $this->setearArraySalesSummary($datosSalesSummary);
            
        } catch (Exception $e) {
            echo 'Fallo: ',  $e->getMessage(), "\n";
        }
    }
    
    public function enviarSalesSummary($_parametros)
    {
        $this->generarDatosSalesSummary($_parametros);
        $QPM = new QPM;
        $QPM->ipTransaccionQPM = $_parametros['ipTransaccion'];
        $QPM->urlQPM = $_parametros['url'];
        $QPM->parametroActivity = $_parametros['activity'];

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding = "UTF-8"?><!DOCTYPE SalesSummary><SalesSummary Version="1.0" EventType="' . $_parametros['EventType'] . '"></SalesSummary>'); //crear obj XML
        $datosSalesSummary = $this->obtenerArraySalesSummary();
        $QPM->convertirArrayToXml($datosSalesSummary, '', $xml);
        $xmlParse = $xml->asXML();
        $DTD_Sales_Summary = new DTD_Sales_Summary;
        $salesSummaryValido = $DTD_Sales_Summary->validarSalesSummaryDTD($datosSalesSummary);

        $strSalesTotal = str_replace(',', '.', $datosSalesSummary['SalesTotal']);
        $salesTotal = floatval($strSalesTotal);

        $responseSalesSummary='No Enviado, error al ingresar los valores';

        if ($salesSummaryValido && $salesTotal > 0) {
            $QPM->consultarKitchenAdvisor($xmlParse);
            $this->guardarTransaccionParaAuditoriaSalesSummary($datosSalesSummary, $QPM->responseArrayFromXML, 'Transaccion Sales Summary enviada', 'SalesSummary', $this->rst_id);
            $responseSalesSummary = 'Transaccion Sales Summary Enviada';
        }

        if (!$salesTotal > 0) {
            $this->guardarTransaccionParaAuditoriaSalesSummary($datosSalesSummary, 'No enviado', 'El total de ventas es menor o igual a 0', 'SalesSummary', $this->rst_id);
            $responseSalesSummary = 'Sales Summary No enviado, el total de ventas es menor o igual a 0';
        }

        if (!$salesSummaryValido) {
            $this->guardarTransaccionParaAuditoriaSalesSummary($datosSalesSummary, 'No enviado', 'Fallo al ingresar SalesSummary, verifique el DTD', 'SalesSummary', $this->rst_id);
            $responseSalesSummary = 'Datos Sales Summary No Enviados, verifique el DTD';
        }
        if ($responseSalesSummary === 'No Enviado, error al ingresar los valores') {
            $this->guardarTransaccionParaAuditoriaSalesSummary($datosSalesSummary, 'responseSalesSummary', 'Fallo al ingresar SalesSummary, verifique el DTD', 'SalesSummary', $this->rst_id);
        }

        echo $responseSalesSummary;
    }

    function guardarTransaccionParaAuditoriaSalesSummary($datosTransaccion, $respuestaKitchenAdvice, $accion, $idCabeceraOrdenPedidoQPM, $rst_id)
    {

        $datosAuditoria['IDAuditoriaTransaccion'] = $idCabeceraOrdenPedidoQPM;
        $datosAuditoria['rst_id'] = $rst_id;
        $datosAuditoria['atran_modulo'] = 'FACTURACION';
        $datosAuditoria['atran_descripcion'] = 'SalesSummary QPM';
        $datosAuditoria['atran_accion'] = $accion;
        $datosAuditoria['Auditoria_TransaccionVarchar1'] = json_encode($datosTransaccion);
        $datosAuditoria['Auditoria_TransaccionVarchar2'] = json_encode($respuestaKitchenAdvice);


        $SalesSummaryQPM = new SalesSummaryQPM;
        $SalesSummaryQPM->ingresarTransaccionParaAuditoriaSalesSummary($datosAuditoria);
        
    }
}
