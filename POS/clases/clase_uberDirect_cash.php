<?php

class uberDirectCash {

    public static function actualizarFormaPagoUberDirect($idCadena, $codigo_app, $accion) {
        $sql = new sql();
        $query = "EXEC [config].[ActualizacionFormaPagoOrdenesUber] $idCadena,'$codigo_app','$accion'";
        try {
            $sql->fn_ejecutarquery($query);
            $data = $sql->fn_leerarreglo();
            return ($data !== null) ? $data : null;
        } catch (Exception $e) {
            return $e;
        }
    }

    public static function reversarFormaPagoUberDirect($idCadena, $codigo_app, $medio, $accion) {
        $sql = new sql();
        $query = "EXEC [config].[ReversoActualizacionFormaPagoOrdenesUber] $idCadena,'$codigo_app','$accion', '$medio'";
        try {
            $sql->fn_ejecutarquery($query);
            $data = $sql->fn_leerarreglo();
            return ($data !== null) ? $data : null;
        } catch (Exception $e) {
            return $e;
        }
    }

    public static function validacionMontoLimiteUber($idCadena, $idRestaurante, $descriptionCR, $descriptionCDR, $codigo_app) {
        $sql = new sql();
        $query = "EXEC [dbo].[ValidacionMontoLimiteUber] $idCadena, $idRestaurante,'$descriptionCR', '$descriptionCDR', '$codigo_app'";
        try {
            $sql->fn_ejecutarquery($query);
            if ($sql->fn_numregistro() > 0) {
                $data = $sql->fn_leerarreglo();
                return $data['Resultado'];
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    public static function getUberCashConfig($idCadena, $idRestaurante, $descriptionCR, $descriptionCDR, $config) {
        $sql = new sql();
        if ($descriptionCDR == 'validaMedioUD') {
            $query = "EXEC [dbo].[getMedioValidoUberDirect] '$idCadena', $idRestaurante, '$descriptionCR'";
        } else {
            $query = "EXEC [dbo].[getConfigPoliticaRestaurante] '$idCadena', $idRestaurante,'$descriptionCR', '$descriptionCDR'";
        }
        try {
            $sql->fn_ejecutarquery($query);
            if ($sql->fn_numregistro() > 0) {
                $row = $sql->fn_leerarreglo();
                return $row[$config];
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    public static function verificaFactura($idRestaurante, $codigo_app) {
        $sql = new sql();
        $query = "select top 1 cfac_id from Cabecera_App where codigo_app = '$codigo_app' and cod_Restaurante = '$idRestaurante' and cfac_id is not null";
        try {
            $sql->fn_ejecutarquery($query);
            if ($sql->fn_numregistro() > 0) {
                $row = $sql->fn_leerarreglo();
                return isset($row['cfac_id']) ? $row['cfac_id'] : false;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    public static function crearCotizacion($cdn_id, $idRestaurante, $medio, $codigo_factura) {
        $sql = new sql();
        $query = "EXEC config.USP_Retorna_Direccion_Webservice ".$idRestaurante.", '".$medio." API', 'EFECTIVO COTIZACION', 0";
        try {
            $sql->fn_ejecutarquery($query);
            $row = $sql->fn_leerarreglo();
            set_time_limit(120); //2 MINS

            $objetoFactura = array(
                'cdn_id'  => $cdn_id,
                'rst_id'  => $idRestaurante,
                'medio'   => $medio,
                'cfac_id' => $codigo_factura,
                'cotizar' => true,
                'data'    => array(self::obtenerDataUberCash($cdn_id, $idRestaurante, $codigo_factura, $medio)['dataAgregador'])
            );

            if (!isset($objetoFactura['data'][0]['dropoff_payment'])) {
                header('Content-Type: application/json; charset=utf-8');
                $result = array(
                    "error"    => "ocurrio un error",
                    "response" => "No se pudo realizar la cotización. Hubo un error en el procesamiento de la información",
                    "payload"  => $objetoFactura
                );

                return json_encode($result);
            }

            $url = $row['direccionws'];
            $url=str_replace("http://https://", "https://",$url);
            $url=str_replace("http://http://", "http://",$url);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($objetoFactura));
            $result = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Comprobar si occurió algún error
            if(curl_errno($ch) || $status==0){
                self::insertaAuditoriaUberCash($url, 'Crear Cotizacion Uber Cash: ' . $codigo_factura, 101, json_encode(array("mensaje"=>"Ocurrio un error con la petición CURL","respuesta"=>"ocurrio un error CURL o estado 0: ".curl_error($ch)), true));
                header('Content-Type: application/json; charset=utf-8');
                $result = json_encode(array(
                    "error"=>"ocurrio un error CURL o estado 0: ".curl_error($ch),
                    "response"=>$result,
                    "payload"=>$objetoFactura
                ));
                curl_close($ch);
                exit(0);
            }

            curl_close($ch);

            if (($status == 200 || $status == 204) && !empty($result) && !strpos(strtolower($result), "error")) {
                $result_string = $result;
                $result = json_decode($result_string, true);

                if (array_key_exists('id', $result["jsonResult"]) && array_key_exists('kind', $result["jsonResult"])) {
                    self::insertaAuditoriaUberCash($url, 'Crear Cotizacion Uber Cash: '.$codigo_factura, $status, $result_string);
                    if ($result["jsonResult"]["kind"] == 'delivery_quote') {
                        $result_codigo = $result["jsonResult"]["id"];
                        $result_json = $result["jsonResult"];
                        self::updateQuoteId($idRestaurante, $codigo_factura, $result_codigo);
                    }
                } else {
                    self::insertaAuditoriaUberCash($url, 'Crear Cotizacion Uber Cash: ' . $codigo_factura, 100, json_encode(array("mensaje"=>"Respuesta correpta en maxpoint, pero payload no se adapta y no se encuentra id","respuesta"=>$result_string), true));
                    header('Content-Type: application/json; charset=utf-8');
                    $result = (array(
                        "error"    => "ocurrio un error",
                        "response" => $result,
                        "payload"  => $objetoFactura
                    ));
                }
            } else {
                if (strpos(strtolower($result), "status")) { //ERROR SIN CODIGO, ERROR INTERNO
                    $result = json_decode($result, true);
                    $responseText = '';
                    self::insertaAuditoriaUberCash($url, 'Crear Cotizacion Uber Cash: ' . $codigo_factura, $result["status"], json_encode($result, true));
                    header('Content-Type: application/json; charset=utf-8');
                    if ($result['jsonResult']['code'] == 'PROVEEDOR_ADDRESS_UNDELIVERABLE') {
                        $responseText = 'La ubicación de entrega especificada en la orden, no se encuentra en un área de reparto. No se pudo procesar con UBER DIRECT';
                    }
                    if ($result['jsonResult']['code'] == 'PROVEEDOR_NOT_ALLOWED' && ("Requested cash amount can't exceed" == substr($result['jsonResult']['error'], 0, 34))) {
                        $responseText = 'El monto de la orden es superior al límite permitido. No se pudo procesar con UBER DIRECT';
                    }

                    $result = (array(
                        "error"    => "ocurrio un error",
                        "response" => $responseText != '' ? $responseText : $result,
                        "payload"  => $objetoFactura
                    ));
                } else {
                    self::insertaAuditoriaUberCash($url, 'Crear Cotizacion Uber Cash: ' . $codigo_factura, $status, $result);
                    header('Content-Type: application/json; charset=utf-8');
                    $result = json_decode($result, true);
                    $responseText = '';
                    if ($result['message'] == '') {
                        $responseText = 'La URL de UBER DIRECT para generar la cotización no es válida. Verifique la politica';
                    }
                    $result = (array(
                        "error"    => "ocurrio un error",
                        "response" => $responseText != '' ? $responseText : $result,
                        "payload"  => $objetoFactura
                    ));

                }
            }
            header('Content-Type: application/json; charset=utf-8');
            return json_encode($result);
        } catch (Exception $e) {
            return $e;
        }
    }

    public static function obtenerDataUberCash($cdn_id, $idRestaurante, $cfacId, $medio) {
        $sql = new sql();
        $query = "EXEC facturacion.BuildJSON_Facturacion_Agregadores ".$cdn_id.", ".$idRestaurante.", '".$cfacId."', '".$medio."';";
        try {
            $sql->fn_ejecutarquery($query);
            if ($sql->fn_numregistro() > 0) {
                $row = $sql->fn_leerarreglo();
                return array( "dataAgregador" => json_decode(utf8_encode($row['jsonFactura']), true));
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    public static function validarUberDirectCash($idCadena, $idRestaurante, $codigo_app, $medio) {
        $response = [];
        // Verifica si la política está activa
        if (!self::getUberCashConfig($idCadena, $idRestaurante, 'UBER DIRECT EFECTIVO', 'ACTIVO', 'variableB')) {
            $response['msj'] = "UBER DIRECT EFECTIVO: OFF";
            $response['status'] = false;
            return $response;
        }
        // Verifica la configuración de Lista Medios
        if (!self::getUberCashConfig($idCadena, $idRestaurante, $medio, 'validaMedioUD', 'variableV')) {
            $response['msj'] = "Uber Direct Cash: La política LISTA MEDIO ". $medio . " no existe o no se encuentra activa.";
            $response['status'] = false;
            return $response;
        }
        // Verifica si el medio está incluido para Uber Cash
        $medios = self::getUberCashConfig($idCadena, $idRestaurante, 'UBER DIRECT EFECTIVO', 'MEDIOS', 'variableV');
        $upperMedio = strtoupper($medio);
        $mediosArray = explode(', ', $medios);

        $containsMedio = false;
        foreach ($mediosArray as $m) {
            $m = strtoupper(trim($m));
            if ($m === $upperMedio) {
                $containsMedio = true;
                break;
            }
        }

        if (!$containsMedio) {
            $response['msj'] = "MEDIO INCORRECTO";
            $response['status'] = false;
            return $response;
        }
        // Verifica si el monto es menor al establecido
        if (!self::validacionMontoLimiteUber($idCadena, $idRestaurante, 'UBER DIRECT EFECTIVO', 'MONTO_LIMITE', $codigo_app)) {
            $response['msj'] = "El monto es mayor al establecido para UBER DIRECT EFECTIVO";
            $response['status'] = false;
            return $response;
        }
        // Valida si tiene cotización
        if (self::cotizacionUberDirect($idRestaurante, $codigo_app)) {
            $response['msj'] = "Cotización validada para UBER DIRECT EFECTIVO";
            $response['status'] = true;
            return $response;
        }

        // Verifica si la forma de pago es correcta y la actualiza
        $factura = self::verificaFactura($idRestaurante, $codigo_app);
        if ($factura == null || $factura == '') {
            $response['msj'] = "ORDEN NO FACTURADA" . $factura;
            $response['status'] = false;
            return $response;
        }
        $resultado = self::actualizarFormaPagoUberDirect($idCadena, $codigo_app, 'ACTUALIZAR');
        $medioOriginal = $medio;
        if ($resultado == null || $resultado['cfac_id'] != $factura ) {
            self::reversarFormaPagoUberDirect($idCadena, $codigo_app, $medioOriginal, 'REVERSAR');
            $response['msj'] = "FORMA DE PAGO INCORRECTA";
            $response['status'] = false;
            return $response;
        }
        $medio = 'UBER';
        $quote = json_decode(self::crearCotizacion($idCadena, $idRestaurante, $medio, $factura), true);
        if (isset($quote["error"]) && $quote["error"] == "ocurrio un error") {
            self::reversarFormaPagoUberDirect($idCadena, $codigo_app, $medioOriginal, 'REVERSAR');
            $response['msj'] = $quote['response'];
            $response['status'] = false;
            return $response;
        }

        $response['msj'] = "Cotización de UBER DIRECT EFECTIVO, generada correctamente";
        $response['status'] = true;
        return $response;
    }

    public static function validarUberDirectCashMedio($idCadena, $idRestaurante, $codigo_app, $medio) {
        $response = true;
        // Verifica si la política está activa
        if (!self::getUberCashConfig($idCadena, $idRestaurante, 'UBER DIRECT EFECTIVO', 'ACTIVO', 'variableB')) {
            $response = false;
        }

        // Verifica si el medio está incluido para Uber Cash
        if ($medio !== 'UBER') {
            $response = false;
        }
        // Verifica si el monto es menor al establecido
        if (!self::validacionMontoLimiteUber($idCadena, $idRestaurante, 'UBER DIRECT EFECTIVO', 'MONTO_LIMITE', $codigo_app)) {
            $response = false;
        }

        if (!$response) {
            $medioOriginal = self::medioActual($idRestaurante, $codigo_app);
            self::reversarFormaPagoUberDirect($idCadena, $codigo_app, $medioOriginal, 'REVERSAR');
            $response = false;
        }

        return $response;
    }

    public static function insertaAuditoriaUberCash($url, $peticion, $estado, $mensaje) {
        $sql = new sql();
        $query = "INSERT INTO dbo.Auditoria_EstadosApp ( url, peticion, estado, mensaje, fecha )
            VALUES ( '$url', '$peticion', '$estado', '$mensaje', GETDATE())";

        return $sql->fn_ejecutarquery($query);
    }

    public static function updateQuoteId($restaurante, $factura, $quoteId) {
        $sql = new sql();
        $query = "UPDATE Cabecera_App 
                  SET cotizacion_id = '$quoteId'
                  WHERE cfac_id = '$factura' AND cod_Restaurante = '$restaurante'";

        return $sql->fn_ejecutarquery($query);
    }

    public static function medioActual($idRestaurante, $codigo_app) {
        $sql = new sql();
        $query = "select top 1 medio from Cabecera_App where codigo_app = '$codigo_app' and cod_Restaurante = '$idRestaurante'";
        try {
            $sql->fn_ejecutarquery($query);
            $row = $sql->fn_leerarreglo();
            return isset($row['medio']) ? $row['medio'] : null;
        } catch (Exception $e) {
            return $e;
        }
    }

    public static function cotizacionUberDirect($idRestaurante, $codigo_app) {
        $sql = new sql();
        $query = "select top 1 cotizacion_id from Cabecera_App where codigo_app = '$codigo_app' and cod_Restaurante = '$idRestaurante' and cotizacion_id is not null";
        try {
            $sql->fn_ejecutarquery($query);
            if ($sql->fn_numregistro() > 0) {
                $row = $sql->fn_leerarreglo();
                return isset($row['cotizacion_id']) ? true : false;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e;
        }
    }
}