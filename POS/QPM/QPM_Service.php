<?php
class QPM
{
    public $responseArrayFromXML;
    public $ipTransaccionQPM;
    public $idTransaccionQPM;
    public $urlQPM;
    public $parametroActivity;

    function consultarKitchenAdvisor($_xml)
    {
        try {
            $url = $this->urlQPM;

            $headers = array(
                "Content-type: application/x-www-form-urlencoded",
                "Connection: close",
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "activity=" . $this->parametroActivity . "&XMLData=" . $_xml);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


            $data = curl_exec($ch);
            
            if (!$data) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }

            $this->convertirRespuestaXmlEnArray($data);
            curl_close($ch);

        } catch (Exception $e) {
            echo 'Error CURL: ',  $e->getMessage(), "\n";
        }
    }

    function convertirRespuestaXmlEnArray($dataXML)
    {
        try {
            $xmlParse = new SimpleXMLElement($dataXML);

            $msjRespuesta = [
                'nombre' => "{$xmlParse->getName()}",
                'version' => "{$xmlParse['Version']}",
                'status' => "{$xmlParse->Status}",
                'messages' => []
            ];
            if ($xmlParse->Messages) {
                foreach ($xmlParse->Messages->children() as $message) {
                    $atributos = $message->attributes();
                    $code = 0;
                    $text = '';

                    foreach ($message->children() as $childMessage) {
                        if ($childMessage->getName() == 'Code') {
                            $code = "{$childMessage}";
                        }
                        if ($childMessage->getName() == 'Text') {
                            $text = "{$childMessage}";
                        }
                    }
                    $mensaje = [
                        'tipo' => "{$atributos->Type}",
                        'code' => $code,
                        'text' => $text
                    ];
                    array_push($msjRespuesta['messages'], $mensaje);
                }
            }

            $this->responseArrayFromXML = $msjRespuesta;
            
        } catch (Exception $e) {
            echo 'Fallo: ',  $e->getMessage(), "\n";
        }
    }

    public function convertirArrayToXml($array, $referencia, &$xml) //es recursiva
    {
        try {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    if (!is_numeric($key)) {
                        $subnode = $xml->addChild("$key");
                        $this->convertirArrayToXml($value, $referencia, $subnode);
                    } else { //los que entran aca son los nodo sin nombre,es decir son numericos.
                        $subnode = $xml->addChild("$referencia");
                        $this->convertirArrayToXml($value, $referencia, $subnode);
                    }
                } else {
                    $xml->addChild("$key", htmlspecialchars("$value"));
                }
            }
        } catch (Exception $e) {
            echo 'Fallo: ',  $e->getMessage(), "\n";
        }
    }

    public function guardarTransaccionParaAuditoria($datosTransaccion, $respuestaKitchenAdvice, $accion, $idCabeceraOrdenPedidoQPM, $rst_id)
    {

        $datosAuditoria['IDAuditoriaTransaccion'] = $idCabeceraOrdenPedidoQPM;
        $datosAuditoria['rst_id'] = $rst_id;
        $datosAuditoria['atran_modulo'] = 'FACTURACION';
        $datosAuditoria['atran_descripcion'] = 'TX QPM: ' . $this->idTransaccionQPM . ' IP:' . $this->ipTransaccionQPM;
        $datosAuditoria['atran_accion'] = $accion;
        $datosAuditoria['Auditoria_TransaccionVarchar1'] = json_encode($datosTransaccion);
        $datosAuditoria['Auditoria_TransaccionVarchar2'] = json_encode($respuestaKitchenAdvice);


        $transacciones = new TransaccionesQPM;
        $transacciones->ingresarTransaccionParaAuditoria($datosAuditoria);
    }
}
