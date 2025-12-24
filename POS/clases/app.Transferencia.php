<?php

class Transferencia extends sql {

    function cargarCabecera( $idCadena, $idRestaurante, $idDestino, $codigo ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_cabecera_pedido_transferencia $idCadena, $idRestaurante, $idDestino, '$codigo'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "codigoApp" => $row['codigo_app'],
                                        "codRestaurante" => intval($row['cod_Restaurante']),
                                        "fechaPedido" => $row['fecha_Pedido'],
                                        "telefonoCliente" => $row['telefono_cliente'],
                                        "consumidorFinal" => $row['consumidor_final'],
                                        "identificacionCliente" => $row['identificacion_cliente'],
                                        "nombresCliente" => utf8_encode($row['nombres_cliente']),
                                        "direccionCliente" => utf8_encode($row['direccion_cliente']),
                                        "emailCliente" => $row['email_cliente'],
                                        "calle1Domicilio" => utf8_encode($row['calle1_domicilio']),
                                        "calle2Domicilio" => utf8_encode($row['calle2_domicilio']),
                                        "observacionesDomicilio" => utf8_encode($row['observaciones_domicilio']),
                                        "numDirecciondomicilio" => utf8_encode($row['numDireccion_domicilio']),
                                        "codZipCode" => $row['cod_ZipCode'],
                                        "tipoInmueble" => intval($row['tipo_Inmueble']),
                                        "totalFactura" => floatval($row['total_Factura']),
                                        "observacionesPedido" => utf8_encode($row['observacion_pedido']),
                                        "transaccion" => $row['transaccion'],
                                        "dispositivo" => $row['dispositivo'],
                                        "medio" => $row['medio'],
                                        "Codigo_pickup" => $row['Codigo_pickup'],
                                        "latitud" => $row['latitud'],
                                        "longitud" => $row['longitud'],
                                        "tipo_descuento" => $row['tipo_descuento'],
                                        "valor_descuento" => $row['valor_descuento'],
                                        "valor_descuento_aplicado" => $row['valor_descuento_aplicado'],
                                        "operador" => $row['operador'],
                                        "perfilOperador" => $row['perfilOperador'],
                                        "idFactura" => $row['idFactura'],
                                        "fidelizacion" => $row['fidelizacion'],
                                        "montoTotalDescuentos" => $row['montoTotalDescuentos'],
                                        "descuentoMontoFijo" => $row['descuentoMontoFijo'],
                                        "descuentoPorcentaje" => $row['descuentoPorcentaje']);
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function cargarConfirmarTransferenciaPickup( $idCadena, $idRestaurante, $idUsuario, $codigo, $transferencia, $respuesta ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_transferir_pedido_pickup $idCadena, $idRestaurante, '$idUsuario', '$codigo', '$transferencia', '$respuesta'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "codigo" => $row['codigo'], "mensaje" => $row['mensaje']);
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }
    
    function cargarURLServicioLocalesPickup( $idRestaurante ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC config.USP_Retorna_Direccion_Webservice $idRestaurante, 'DISTRIBUIDOR', 'LOCALES PICKUP', 0;";
        //echo $lc_sql;
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "estado" => $row['estado'], "direccionws" => $row['direccionws']);
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function cargarURLServicioTransferenciaPickup( $idRestaurante ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC config.USP_Retorna_Direccion_Webservice $idRestaurante, 'DISTRIBUIDOR', 'TRANSFERENCIA', 0;";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "estado" => $row['estado'], "direccionws" => $row['direccionws']);
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function cargarURLServicioAutenticacionServidor( $idRestaurante ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC config.USP_Retorna_Direccion_Webservice $idRestaurante, 'DISTRIBUIDOR TRADE', 'AUTENTICACION', 0;";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "estado" => $row['estado'], "direccionws" => $row['direccionws']);
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function cargarConfiguracionPoliticasPickup( $idCadena ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_datos_pickup " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "client_id" => $row['client_id'],
                                        "client_secret" => $row['client_secret'] );
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function cargarLocalesPickup( $idCadena, $idRestaurante, $idUsuario ) {
        $urlServicio = $this->cargarURLServicioLocalesPickup( $idRestaurante );
        $error = new \stdClass();
        if ( $urlServicio["estado"] == 1 ) {
            $url = $urlServicio["direccionws"];
            $idCadena = intval($idCadena);
            $idRestaurante = intval($idRestaurante);
            $parametros = array("idCadena" => $idCadena, "idRestaurante" => $idRestaurante );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, ($url));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parametros));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20); //timeout in seconds
                curl_setopt($ch, CURLOPT_TIMEOUT, 20); //timeout in seconds
                $result = curl_exec($ch);
                //print $result;

                $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                             
                
                if (!isset($status) || $status != 200) {

                    if($status == 404){
                        $error->mensaje = "Error:  Ruta de transferencia " .   $url . " No valida ";
                        $error->codigo = 0;
                        $this->guardarAuditoria( $idCadena, $idRestaurante, $idUsuario, 200 , 'TRANSFERENCIA', 'CARGAR LOCALES', json_encode($parametros), json_encode($error) );
                        return json_encode($error);  
                    }

                    $error->mensaje = "Error: " . curl_error( $ch ) . ", " . curl_errno( $ch );
                    $error->codigo = 0;
                    $this->guardarAuditoria( $idCadena, $idRestaurante, $idUsuario, 200, 'TRANSFERENCIA', 'CARGAR LOCALES', json_encode($parametros), json_encode($error) );
                    return json_encode($error);
                }
                $response = json_decode($result);
                //print "Status: " . $status . "<br/>";
            
                // Validar Estado
                if ( $status == 200 ) {

                    // Validar respuesta 200
                    return json_encode($response);
                }
                curl_close($ch);   
        } else {
            // Error, no está configurada la url del servicio de transacciones
            $error->mensaje = "Servicio de transferencias no configurado.";
            $error->codigo = 0;
            $this->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'CARGAR LOCALES', json_encode($parametros), json_encode($error) );
            return json_encode($error);

        }
    }

    function mostrarBotonTransferenciaPickup ($idCadena, $idRestaurante) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_politicas_transferencia_pickup $idCadena, $idRestaurante";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "respuesta" => $row['respuesta'] );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function cargarMotivosPickup( $idCadena ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_motivos_transferencia_pickup $idCadena";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "id" => $row['id'], "descripcion" => $row['descripcion'] );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function cargarDetalle( $idCadena, $idRestaurante, $codigo, $aplicaDescuentos = false) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_detalle_pedido_transferencia $idCadena, $idRestaurante, '$codigo'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($aplicaDescuentos) {
                while ($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array('orden' => number_format((float) $row['orden'], 2, '.', ''),
                                             'codPlu' => intval($row['cod_plu']),
                                             'cantidad' => intval($row['cantidad']),
                                             'codigoApp' => $row['codigo_app'],
                                             'impuestos' => array(
                                                array(
                                                    'nombre' => 'IVA '.number_format((float) $row['porcentajeIva'], 2, '.', ''),
                                                    'porcentaje' => number_format((float) $row['porcentajeIva'], 2, '.', '')
                                                ),    
                                             ),
                                            'detalleApp' => $row['detalle_app'],
                                            'PVPUnitario' => $row['precio_Bruto']/$row['cantidad'],
                                            'descuentoMontoFijoProducto' => number_format((float) $row['descuentoMontoFijoProducto'], 2, '.', ''),
                                            'descuentoPorcentajeProducto' => number_format((float) $row['descuentoPorcentajeProducto'], 2, '.', ''));
                }
            } else {
                while ($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array("codigoApp" => $row['codigo_app'],
                                            "detalleApp" => $row['detalle_app'],
                                            "codPlu" => intval($row['cod_plu']),
                                            "cantidad" => intval($row['cantidad']),
                                            "precioBruto" => floatval($row['precio_Bruto']),
                                            "canje" => intval($row['canje']));
                }
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function cargarFormaPago( $idCadena, $idRestaurante, $codigo ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_formapago_pedido_transferencia $idCadena, $idRestaurante, '$codigo'";
        try {
            $this->fn_ejecutarquery($lc_sql);
                while ($row = $this->fn_leerarreglo()) {
                array_push($this->lc_regs, array( "codformaPago" => intval($row['cod_formaPago']),
                "codigoApp" => $row['codigo_app'],
                "totalPagar" => intval($row['total_Pagar']),
                "billete" => intval($row['billete']))); 
                }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function cargarModificadores( $idCadena, $idRestaurante, $codigo ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_modificadores_pedido_transferencia $idCadena, $idRestaurante, '$codigo'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "detalleApp" => $row['detalle_app'], "codModificador" => intval($row['cod_Modificador']));
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function cargarURLServicioTransferencia( $idRestaurante ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC config.USP_Retorna_Direccion_Webservice $idRestaurante, 'TRANSFERENCIA', 'PEDIDOS', 0;";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "estado" => $row['estado'], "direccionws" => $row['direccionws']);
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function cargarConfirmarTransferencia( $idCadena, $idRestaurante, $idUsuario, $codigo, $transferencia, $respuesta ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_transferir_pedido $idCadena, $idRestaurante, '$idUsuario', '$codigo', '$transferencia', '$respuesta'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "codigo" => $row['codigo'], "mensaje" => $row['mensaje']);
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function cargarLocales( $idCadena, $idRestaurante ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC App_cargar_locales_transferencia $idCadena, $idRestaurante";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "id" => $row['id'], "codigo" => $row['codigo'], "descripcion" => $row['descripcion'] );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function cargarMotivos( $idCadena ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_motivos_transferencia $idCadena";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "id" => $row['id'], "descripcion" => $row['descripcion'] );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function guardarAuditoria( $idCadena, $idRestaurante, $idUsuario, $codigo, $descripcion, $accion, $request, $response ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_Guardar_Auditoria_Transferencia $idCadena, $idRestaurante, '$idUsuario', '$codigo', '$descripcion', '$accion', '$request', '$response'";
        try {
            $this->fn_ejecutarquery($lc_sql);
        } catch (Exception $e) {
            print $e;
        }
    }

    function cargarDatosFormaPago( $idCadena, $idRestaurante, $idFactura ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC App_verificar_forma_pago $idCadena, $idRestaurante, '$idFactura'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo())  {
                array_push($this->lc_regs, array( "numeroSeguridad" => $row['numeroSeguridad'], 
                "idMotivoAnulacion" => $row['idMotivoAnulacion'],
                "tipoFormaPago" => $row['tipoFormaPago'],
                "codigoUno" => $row['codigoUno'],
                "codigoDos" => $row['codigoDos'],
                "idFormaPago" => $row['idFormaPago'],
                "aut_trama" => $row['aut_trama'],
                "aut_fecha" => $row['aut_fecha'],
                "aut_ttra_codigo" => $row['aut_ttra_codigo'],
                "aut_cres_codigo" => $row['aut_cres_codigo'],
                "aut_respuesta" => $row['aut_respuesta'],
                "aut_secuencial_transaccion" => $row['aut_secuencial_transaccion'],
                "aut_hora_autorizacion" => $row['aut_hora_autorizacion'],
                "aut_fecha_autorizacion" => $row['aut_fecha_autorizacion'],
                "aut_numero_autorizacion" => $row['aut_numero_autorizacion'],
                "aut_terminal_id" => $row['aut_terminal_id'],
                "aut_grupo_tarjeta" => $row['aut_grupo_tarjeta'],
                "aut_red_adquiriente" => $row['aut_red_adquiriente'],
                "aut_merchant_id" => $row['aut_merchant_id'],
                "aut_numero_tarjeta" => $row['aut_numero_tarjeta'],
                "aut_observacion" => $row['aut_observacion'],
                "aut_estado" => $row['aut_estado'],
                "rqaut_fecha" => $row['rqaut_fecha'],
                "rqaut_ip" => $row['rqaut_ip'],
                "rqaut_puerto" => $row['rqaut_puerto'],
                "rqaut_trama" => $row['rqaut_trama'],
                "aut_tpenv_id" => $row['aut_tpenv_id'],
                "aut_tarjetahabiente" => $row['aut_tarjetahabiente'],
                "fpf_codigo" => $row['fpf_codigo'],
                "id_forma_pago" => $row['id_forma_pago'],
                "fmp_descripcion" => $row['fmp_descripcion'],
                "id_forma_pago_factura" => $row['id_forma_pago_factura'],
                "fpf_total_pagar"  => $row['fpf_total_pagar'],
                ));
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            print $e;
        }
        return $result;
    }

}
