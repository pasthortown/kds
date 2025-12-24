<?php

															
//header('Content-Type: text/html; charset=UTF-8');

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para Facturacion///////////////////
/////////////////////En las Mesas/////////////////////////////
///////TABLAS INVOLUCRADAS: Cadena, Restaurante //////////////
////////////////////////////Pisos, AreaPisos, Mesas///////////
////////////////////////////Facturas//////////////////////////
///////FECHA CREACION: 28-Enero-2014//////////////////////////
///////FECHA ULTIMA MODIFICACION:21/04/2014///////////////////
///////USUARIO QUE MODIFICO: Jose Fernandez///////////////////
///////DECRIPCION ULTIMO CAMBIO: varios para mejorar /////////
////////////////////////////////funcionalidad/////////////////
//////////////////////////////////////////////////////////////

class facturas extends sql {

    function __construct() {
        parent::__construct();
    }

    function fn_consultar($lc_sqlQuery, $lc_datos) {
							   

        switch ($lc_sqlQuery) {
            /////////////////////////////////////////FORMAS DE PAGO////////////////////////////////////////////////////
                        
                        case 'obtenerHTMLCorreroPayphone':
                            $this->lc_regs = Array();
                            $lc_sql = "exec payphone.USP_htmlCorreoCobro '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]'";
                            if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                                while ($row = $this->fn_leerarreglo()) {
                                    $this->lc_regs[] = array(
                                        "html" => utf8_encode($row['html'])
                                    );
                                }
                                $this->lc_regs['str'] = $this->fn_numregistro();
                                return json_encode($this->lc_regs);
                            }
                           
                            $this->fn_liberarecurso();
                            break;
            
            
            
                        case 'obtenerConfiguracionSMTPyCorreo':
                            $this->lc_regs = Array();
                            $lc_sql = "exec payphone.USP_obtenerCredencialCorreoYConfiguracionSMTP '$lc_datos[0]'";
                            if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                                while ($row = $this->fn_leerarreglo()) {
                                    $this->lc_regs[] = array("estado" => $row['estado'],
                                        "correo" => utf8_encode($row['correo']),
                                        "password" => utf8_encode($row['password']),
                                        "mensaje" => utf8_encode($row['mensaje']),
                                        "host" => utf8_encode($row['host']),
                                        "puerto" => $row['puerto'],
                                        "asunto" => utf8_encode($row['asunto']),
                                        "nombreUsuario" => utf8_encode($row['nombreUsuario']),
                                        "tiempoEsperaTransaccion" => utf8_encode($row['tiempoEsperaTransaccion']),
                                    );
                                }
                                $this->lc_regs['str'] = $this->fn_numregistro();
                                return json_encode($this->lc_regs);
                            }
                            $this->fn_liberarecurso();
                            break;
            
            
                        case 'consultarMediosPagoPayphoneDisponible':
                            $this->lc_regs = Array();
                            $lc_sql = "exec payphone.USP_ObtenerMediosPago '$lc_datos[0]'   ";
                            if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                                while ($row = $this->fn_leerarreglo()) {
                                    $this->lc_regs[] = array("id" => $row['id'],
                                        "tipo" => utf8_encode($row['tipo']),
                                        "texto" => utf8_encode($row['texto'])
                                    );
                                }
                                $this->lc_regs['str'] = $this->fn_numregistro();
                                return json_encode($this->lc_regs);
                            }
                            $this->fn_liberarecurso();
                            break;

            case 'estadoErrorNotaCredito':
                $this->lc_regs = Array();
                $lc_sql = "EXEC [fidelizacion].[IAE_ActualizaCabeceraNotaCreditoError] '$lc_datos[0]', '$lc_datos[1]' , '$lc_datos[2]' , '$lc_datos[3]'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("mensaje" => $row['mensaje']);
                    }
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'estadoErrorFactura':
                if (count($lc_datos) > 4) {
                    $lc_sql = "EXEC [fidelizacion].[IAE_ActualizaCabeceraFacturaError] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]'";
                } else {
                    $lc_sql = "EXEC [fidelizacion].[IAE_ActualizaCabeceraFacturaError] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
                }

                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("mensaje" => $row['mensaje']
                        );
                    }
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'obtenerDatosFacturaFormaPago':
                $this->lc_regs = Array();
                $lc_sql = "EXEC [facturacion].[FIDELIZACION_DatosFactura_MetodosPago] '$lc_datos[0]'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("paymentMethod" => $row['paymentMethod'],
                            "amount" => $row['amount']
                        );
                    }
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

                case 'PayPhoneObtenerClaves':
                $this->lc_regs = Array();
                $lc_sql = "exec [facturacion].[USP_PayPhoneObtenerClaves] '$lc_datos[0]'   ";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Token" => $row['Token'],
                        "ContraseniaCodificacion" => $row['ContraseniaCodificacion'],
                        "StoreId" => $row['StoreId'],
                        "countryCode" => $row['countryCode'],
                        "currency" => $row['currency'],
                        "modificarCodigoPais" => $row['modificarCodigoPais']
                        );
                    }
                   $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;
                case 'PayPhoneGuardarRespuestaAutorizacion':
                $this->lc_regs = Array();
                $lc_sql = "exec  [facturacion].[IAE_SWT_InsertaRespuestaAutorizacion]  '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]', '$lc_datos[7]', '$lc_datos[8]', '$lc_datos[9]', '$lc_datos[10]',  '$lc_datos[11]', '$lc_datos[12]', '$lc_datos[13]',  '$lc_datos[14]', '$lc_datos[15]', '$lc_datos[16]', '$lc_datos[17]', '$lc_datos[18]', '$lc_datos[19]',  '$lc_datos[20]', '$lc_datos[21]', '$lc_datos[22]', '$lc_datos[23]', '$lc_datos[24]', '$lc_datos[25]'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                                "respuesta" => $row['respuesta'] 
                        );
                    }
                    //  $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'obtenerDatosFacturaProductos':
                $this->lc_regs = Array();
                $lc_sql = "EXEC [facturacion].[FIDELIZACION_DatosFactura_Productos] '$lc_datos[0]'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("productId" => $row['productId'],
                            "name" => utf8_encode($row['name']),
                            "unitPrice" => round($row['unitPrice'], 2),
                            "amount" => $row['amount'],
                            "vat" => $row['vat'],
                            "vatTaxBase" => round($row['vatTaxBase'], 2),
                            "vatCalculated" => round($row['vatcalculated'], 2),
                            "totalPrice" => round($row['totalPrice'], 2),
                            "answers" => $row['answers']
                        );
                    }
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'obtenerDatosTotalesClienteResturante':
                $this->lc_regs = Array();
                $lc_sql = "EXEC [facturacion].[FIDELIZACION_DatosFactura_TotalesClienteResturante] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("storeId" => $row['storeId'],
                            "storeCode" => $row['storeCode'],
                            "vendorId" => $row['vendorId'],
                            "invoice" => $row['invoice'],
                            "invoiceCode" => $row['invoiceCode'],
                            "subtotal" => $row['subtotal'],
                            "vat" => $row['vat'],
                            "vatTaxBase" => $row['vatTaxBase'],
                            "vatCalculated" => $row['vatCalculated'],
                            "total" => $row['total'],
                            "documentType" => $row['documentType'],
                            "document" => $row['document'],
                            "cli_nombres" => utf8_encode($row['cli_nombres']),
                            "address" => utf8_encode($row['address']),
                            "cashierDocument" => $row['cashierDocument'],
                            "cashierName" => utf8_encode($row['cashierName']),
                            "token" => isset($_SESSION['fb_security'])?$_SESSION['fb_security']:''
                        );
                    }
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'obtenerDatosEnvioPuntos':
                $Valor = "$lc_datos[0]";
                $lc_sql = "EXEC [fidelizacion].[USP_ObtenerDatosParaCanjePuntos] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        if ($Valor == 1) {
                            $this->lc_regs[] = array("storeId" => $row['storeId'],
                                "storeCode" => $row['storeCode'],

                                "storeName" => $row['storeName'],
                                "storeCity" => $row['storeCity'],
                                "lat" => $row['lat'],
                                "lng" => $row['lng'],

                                "vendedorId" => $row['vendedorId'],
                                "redemptionCode" => $row['invoiceCode'], 
                                "cashier" => array(
                                    "document" => $row['cashierDocument'],
                                    "name" => $row['cashierName']
                                ));
                        } else if ($Valor == 2) {
                            $this->lc_regs[] = array("productId" => $row['prouductId'],
                                "unitPrice" => $row['unitPrice'],
                                "amount" => $row['amount'],
                                "vat" => $row['vat'],
                                "vatTaxBase" => $row['vatTaxBase'],
                                "vatCalculated" => $row['vatCalculated'],
                                "totalPrice" => $row['totalPrice'],
                                "points" => $row['points'],
                            );
                        } else if ($Valor == 3) {
                            $this->lc_regs[] = array("documentType" => $row['documentType'],
                                "document" => $row['document']);
                        }
                    }
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'obtenerDatosVitalityFac':
                $Valor = "$lc_datos[0]";
                $lc_sql = "EXEC [creditos].[USP_ObtenerDatosFacturaCanjeVoucher] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        if ($Valor == 1) {
                            $this->lc_regs[] = array(
                                "code" => $row['code'],
                                "storeCode" => $row['storeCode'],
                                "invoiceCode" => $row['invoiceCode']);
                        } else if ($Valor == 2) {
                            $this->lc_regs[] = array(
                                "subtotal" => $row['subtotal'],
                                "vat" => $row['vat'],
                                "vatTaxBase" => $row['vatTaxBase'],
                                "vatCalculated" => $row['vatCalculated'],
                                "total" => $row['total'],

                            );
                        } else if ($Valor == 3) {
                            $this->lc_regs[] = array(
                                "productCode" => $row['productCode'],
                                "name" => utf8_decode($row['name']),
                                "unitPrice" => $row['unitPrice'],
                                "amount" => $row['amount'],
                                "vat" => $row['vat'],
                                "vatTaxBase" => $row['vatTaxBase'],
                                "vatCalculated" => $row['vatCalculated'],
                                "totalPrice" => $row['totalPrice']
                            );
                        }
                    }
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'aplicaAcumulacionPuntos':
                $lc_sql = "EXEC [fidelizacion].[AplicaAcumulacionPuntos] '$lc_datos[0]', '$lc_datos[1]'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("estado" => intval($row['estado']));
                    }
                    $this->lc_regs['str'] = intval($this->fn_numregistro());
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'TieneDivisionCuenta':
                $lc_sql = "SELECT [config].[fn_orden_division_cuentas]('$lc_datos[0]') AS respuesta";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("respuesta" => $row['respuesta']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'dividir':
                $lc_sql = "EXEC [config].[USP_MontoLimiteVoucherAutomatico] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("direccion" => $row['direccion'],
                            "url" => $row['url']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'SepararCuentasAlExecerVoucher':
                $lc_sql = "EXEC [config].[USP_SepararCuentasAlExecerVoucher] '$lc_datos[0]'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("estado" => $row['estado'],
                            "mensaje" => $row['mensaje']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'EstadoAbrirCajon':
                $lc_sql = "EXEC [facturacion].[EstadoAbrirCajon] '$lc_datos[0]'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("respuesta" => $row['respuesta'],
                            "mensaje" => $row['mensaje']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'formaPago':
                $lc_sql = "EXEC [facturacion].[USP_cargaFormasPago] $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array('fmp_id' => $row['fmp_id'],
                            'fpf_codigo' => $row['fpf_codigo'],
                            'fmp_descripcion' => $row['fmp_descripcion'],
                            'tfp_id' => $row['tfp_id'], "fmp_imagen" => $row['fmp_imagen'],
                            'requiereAutorizacion' => $this->ifNum($row['requiereAutorizacion']),
                            'tfp_descripcion' => $row['tfp_descripcion']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'buscaClienteAx':
                $lc_sql = "EXEC [facturacion].[USP_busqueda_clienteAx] '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cli_id" => $row['cli_id'],
                            "cli_nombres" => utf8_encode($row['cli_nombres']),
                            "cli_documento" => utf8_encode($row['cli_documento']),
                            "cli_telefono" => utf8_encode($row['cli_telefono']),
                            "cli_direccion" => utf8_encode($row['cli_direccion']),
                            "cli_email" => utf8_encode($row['cli_email']));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaAgrupacionSWT':
                $lc_sql = "EXEC [facturacion].[USP_consulta_agrupacionSWT] $lc_datos[1], '$lc_datos[2]', $lc_datos[3]";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("tpenv_agrupacion" => $row['tpenv_agrupacion'], "envio_id" => $row['envio_id']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            /* Validar Forma Pago aplicada a factura para pagos con PayPhone */
            case 'verificarFormasPagoAplicadasFacturaPayPhone':
                $lc_sql = "EXEC facturacion.PAYPHONE_formaspagofactura '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    if ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Confirmar" => $row['Confirmar'], "Diferencia" => $row['Diferencia']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'cargaTeclasEmail':
                $lc_sql = "EXEC [facturacion].[USP_cargaTeclasEmail] $lc_datos[0]";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("descripcionEmail" => $row['descripcionEmail']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            /* Arma e inserta la trama cuando la lectura de la tarjeta es por banda */
            case 'armaTramaSWTbanda':
                $lc_sql = "EXEC [facturacion].[IAE_SWT_InsertaRequerimientoAutorizacionBanda] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[3]', $lc_datos[5], '$lc_datos[4]', '$lc_datos[2]', $lc_datos[6], '$lc_datos[7]', '$lc_datos[8]', '$lc_datos[9]', '$lc_datos[10]', $lc_datos[11]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    $this->lc_regs['str'] = 0;
                }
                return json_encode($this->lc_regs);

            case 'ingresaCanalMovimientoCredito':
                $lc_sql = "EXEC fac_grabacanalMovimientoImpresionCreditoSinCupon'$lc_datos[0]', '$lc_datos[1]', $lc_datos[2]";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'cabecera_creditoSinCupon':
                $lc_sql = "EXEC imp_voucherCreditoSinCupon'$lc_datos[0]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'cabecera_creditoSinCupon':
                $lc_sql = "EXEC [SWT_InsertaRequerimientoAutorizacion]
     					@tipo_transaccion = N'$lc_datos[0]',
      					@cfac_id = N'$lc_datos[1]',
      					@estId = $lc_datos[3],
      					@rstId = $lc_datos[5],
      					@user = $lc_datos[4],
     					@formaPago = $lc_datos[2];";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'detalle_CreditoSinCupon':
                $lc_sql = "EXEC imp_voucherDetalleCreditoSinCupon '$lc_datos[0]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'totales_CreditoSinCupon':
                $lc_sql = "EXEC imp_totalesCreditoSinCupon '$lc_datos[0]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'anula_formaPagoEfectivo':
                $lc_sql = "EXEC[facturacion].[anula_formaPagoEfectivo] 'U', '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    $this->lc_regs['str'] = 0;
                }
                return json_encode($this->lc_regs);

            case "anula_formaPagoCredito":
                $lc_sql = "EXEC  [facturacion].[anula_formaPagoCredito] 'U', '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    $this->lc_regs['str'] = 0;
                }
                return json_encode($this->lc_regs);


            case 'anula_formaPagoPayvalida':
                    $lc_sql = "EXEC[facturacion].[anula_formaPagoPayvalida] 'U', '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
                    if ($this->fn_ejecutarquery($lc_sql)) {
                        $this->lc_regs['str'] = 1;
                    } else {
                        $this->lc_regs['str'] = 0;
                    }
                    return json_encode($this->lc_regs);
    

            case 'cancelaTarjetaForma':
                $lc_sql = "EXEC [facturacion].[anula_formaPagoTarjeta] 'U','$lc_datos[0]','$lc_datos[1]',$lc_datos[2],'$lc_datos[3]','$lc_datos[4]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

			case 'ws_autorizacion':
                $lc_sql = "EXEC fac_insertaRespuestaAutorizacionComprobante '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['rsp_claveAcceso'] = $row["rsp_claveAcceso"];
                        $this->lc_regs['rsp_estado_autorizacion'] = $row["rsp_estado_autorizacion"];
                        $this->lc_regs['rsp_autorizacion'] = $row["rsp_autorizacion"];
                        $this->lc_regs['rsp_ambiente'] = $row["rsp_ambiente"];
                        $this->lc_regs['rsp_fecha_autorizacion'] = $row["rsp_fecha_autorizacion"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);	   

            case 'consultaIdSWtimeoutBanda':
                $lc_sql = "EXEC  [facturacion].[USP_grabaCanalSwtTimeout] '$lc_datos[0]',$lc_datos[1],'$lc_datos[2]'";

                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['confirma'] = $row["confirma"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'verificaBinExistente':
                $lc_sql = "EXEC [facturacion].[USP_verificaBinTarjetaConfigurado] '$lc_datos[1]'";

                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['confirma'] = $row["confirma"];
                        $this->lc_regs['mensaje'] = $row["mensaje"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'validaFormaBinDatafast':
                $lc_sql = "EXEC [facturacion].[USP_verificaBinTarjetaFormaPago] '$lc_datos[1]',$lc_datos[2],'$lc_datos[3]','$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['idFormaPago'] = $row["idFormaPago"];
                        $this->lc_regs['confirma'] = $row["confirma"];
                        $this->lc_regs['mensaje'] = $row["mensaje"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'fac_listaTotales':
                $lc_sql = "EXEC [facturacion].[USP_listaTotalesFactura] '$lc_datos[1]',$lc_datos[2],$lc_datos[3]";

                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("descripcion" => trim($row['descripcion']),
                            "valor" => trim($row['valor']));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'validaColeccionEstacionTipoEnvio':
                $lc_sql = "EXEC [facturacion].[USP_valida_coleccionEstacion_TipoEnvio] '$lc_datos[1]',$lc_datos[2]";

                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Descripcion" => trim($row['Descripcion']),
                            "idIntegracion" => trim($row['idIntegracion']),
                            "autorizado" => trim($row['autorizado']),
                            "secuenciaConfigurada" => trim($row['secuenciaConfigurada']),
                            "secuencia" => trim($row['secuencia']));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }

                try {
                    @error_log( 
                        date('d-m-Y H:i:s')
                        ." - Documento: js/ajax_facturacion.js"
                        ." - Consulta: validaColeccionEstacionTipoEnvio"
                        ." - Sentencia: $lc_sql"
                        ." - Salida:"
                        ." Descripcion; ".$this->lc_regs[0]['Descripcion']
                        ." , idIntegracion; ".$this->lc_regs[0]['idIntegracion']
                        ." , autorizado; " .$this->lc_regs[0]['autorizado']
                        ." , secuenciaConfigurada; ".$this->lc_regs[0]['secuenciaConfigurada']
                        ." , secuencia; ".$this->lc_regs[0]['secuencia']
                        ."\n"
                    , 3, "../logs/info.log" );
                } catch (Exception $e) { ; }

                return json_encode($this->lc_regs);

            case 'PromocionesMovistar':
                $lc_sql = "EXEC [facturacion].[USP_PromocionesMovistar] '$lc_datos[0]'";
                $response = [];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {                        
                        if (isset($row['Respuesta']) && $row['Respuesta'] == 'NO APLICA') {
                            $response['url_ws'] = $row['Respuesta']; 
                            $response['plu_id'] = 0;   
                        } else {
                            $response['url_ws'] = $row["url_ws"]; 
                            $response['plu_id'] = $row["plu_id"];
                        }                         
                    }
                }
                return json_encode($response); 
            case 'checkTransferencia':
                $lc_sql = "SELECT PlusOrigen, PlusDestino FROM [config].[fn_ColeccionPlus_TransferenciaVentaProductos] (".$lc_datos[2].") WHERE PlusOrigen=".$lc_datos[0];
                $response = [];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $response['PlusOrigen'] = $row["PlusOrigen"]; 
                        $response['PlusDestino'] = $row["PlusDestino"]; 
                    }
                }
                $lc_sql = "EXEC [facturacion].[USP_TRANSFERENCIAVENTA_DatosRestaurante] 2," . $lc_datos[1] . "," . $lc_datos[2];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $response['Restaurante'] = $row["rst_id"]; 
                        $response['Cadena'] = $row["cdn_id"]; 
                    }
                }
                return json_encode($response); 
            case 'SetQRPromocionesMovistar':
                $lc_sql = "EXEC [facturacion].[USP_SetQRPromocionesMovistar] '$lc_datos[0]','$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $toReturn = '';
                    while ($row = $this->fn_leerarreglo()) {
                        $toReturn = trim($row['Respuesta']);
                    }
                }
                return json_encode(['Respuesta'=>$toReturn]);
            
            case 'auditoria_cupones_movistar' :
                $lc_sql = "EXEC [facturacion].[IAE_Logs_auditoria_transaccion] $lc_datos[0],'$lc_datos[1]', '$lc_datos[2]','$lc_datos[3]', '$lc_datos[4]','$lc_datos[5]','$lc_datos[6]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $toReturn = '';
                    while ($row = $this->fn_leerarreglo()) {
                        $toReturn = trim($row['Respuesta']);
                    }
                }
                return json_encode(['Respuesta'=>$toReturn]);
                
            case 'verificaConfiguracionSWT':
                $lc_sql = "EXEC [facturacion].[USP_consulta_configuracionEstacionSWT] $lc_datos[1],'$lc_datos[2]',$lc_datos[3],'$lc_datos[4]'";

                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['existe'] = $row["existe"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'imprimirOrden':
                $lc_sql = " EXEC pedido.ORD_impresion_ordenpedido '$lc_datos[0]','$lc_datos[1]',$lc_datos[2],$lc_datos[3],$lc_datos[4]";

                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['Confirmar'] = $this->ifNum($row["Confirmar"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

                case 'imprimirPromociones':
                    $lc_sql = " EXEC pedido.ORD_impresion_promocion_facturacion '$lc_datos[0]', $lc_datos[1]";
                    
                    if ($this->fn_ejecutarquery($lc_sql)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs['Confirmar'] = $this->ifNum($row["Confirmar"]);
                        }
                        $this->lc_regs['str'] = $this->fn_numregistro();
                    }
                    return json_encode($this->lc_regs);

            case 'reimprimirOrden':
                $lc_sql = " EXEC [pedido].[IAE_ImpresionOrdenPedidoFastFoodKioskoFactura] '$lc_datos[0]','$lc_datos[1]',$lc_datos[2],$lc_datos[3],$lc_datos[4],'$lc_datos[5]'";

                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['Confirmar'] = $row["Confirmar"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'validacionOrdenPedidoKiosko':
                $lc_sql = "EXEC [config].[VerificarOrdenPedido_PerteneceKiosko] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "Resultado" => $row['Resultado']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'validaDetalleEnFactura':
                $lc_sql = "EXEC [facturacion].[USP_validaDetalleEnFactura] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['existe'] = $row["existe"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'valida_abreFinTransaccion':
                $lc_sql = "EXEC fac_validaAperturaCajonFinTransaccion $lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['rst_cajon_fin_transaccion'] = $row["rst_cajon_fin_transaccion"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'obtieneTotalApagar':
                $lc_sql = "EXEC facturacion.USP_total_cancelacionPago '$lc_datos[0]','$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['total'] = $row["total"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'consultaSwtTransaccionalCancelacion':
                $lc_sql = "EXEC [facturacion].[USP_obtieneTipoEnvioTarjetas] '$lc_datos[0]','$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fpf_id" => $row['fpf_id'],
                            "fpf_codigo" => $row['fpf_codigo'],
                            "fmp_descripcion" => $row['fmp_descripcion'],
                            "fpf_swt" => $row['fpf_swt'], 
							"fmp_id" => $row['fmp_id'], 
                            "secuenciaConfigurada" => $row['secuenciaConfigurada'], 
                            "secuencia" => $row['secuencia']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'activaOpcionCobroTarjeta':
                $lc_sql = "EXEC [facturacion].[IAE_activa_opcion_cobroTarjetas] '$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    $this->lc_regs['str'] = 0;
                }
                return json_encode($this->lc_regs);

            case "obtenerMesa":
                $lc_query = "EXEC pedido.ORD_asignar_mesa " . $lc_datos[0] . ", '" . $lc_datos[1] . "'" . ", '" . $lc_datos[2] . "'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['mesa_asignada'] = $row['mesa_asignada'];
                        $this->lc_regs['fidelizacion_Activa'] = intval($row["fidelizacion_Activa"]);
                        $this->lc_regs["esDivisionCuenta"] = $row["esDivisionCuenta"];
                        $this->lc_regs["rst_cat"] = $row["rst_cat"];
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'consulta_cancelacionTarjeta':
                $lc_sql = "EXEC facturacion.USP_consulta_cancelacionFormaPago '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['existe'] = $row["existe"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'ws_recepcion':
                $lc_sql = "EXEC fac_insertaRespuestaRecepcionComprobante '$lc_datos[0]','$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['rcp_clave_acceso'] = $row["rcp_clave_acceso"];
                        $this->lc_regs['rcp_estado'] = $row["rcp_estado"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
            
            //valida_tipo_facturacion_puntos
                case 'valida_tipo_facturacion_puntos':
                    $lc_sql = "EXEC [facturacion].[USP_ValidaTipoFacturacion] $lc_datos[0]";
                    if ($this->fn_ejecutarquery($lc_sql)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs['rst_tipo_facturacion'] = $row["rst_tipo_facturacion"];
                        }
                        $this->lc_regs['str'] = $this->fn_numregistro();
                        // masivo
                        if (isset($lc_datos[2], $lc_datos[7], $_SESSION['fidelizacionActiva']) && $_SESSION['fidelizacionActiva'] == 1){
                            $this->lc_regs['appedir'] = $lc_datos[2];
                            $statusMasivo = $this->acumulacionMasivo($lc_datos[3], $lc_datos[4], $lc_datos[1], $lc_datos[5], $lc_datos[6], 1); //con el 1 activamos el canje de puntos
                            $this->lc_regs['statusMasivo'] = $statusMasivo;
                        }
                        // fin masivo
                    }
                    return json_encode($this->lc_regs);

            //valida_tipo_facturacion
            case 'valida_tipo_facturacion':
                $lc_sql = "EXEC [facturacion].[USP_ValidaTipoFacturacion] $lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['rst_tipo_facturacion'] =$this->ifNum($row["rst_tipo_facturacion"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    // masivo
                    if (isset($lc_datos[2], $lc_datos[7], $_SESSION['fidelizacionActiva']) && $_SESSION['fidelizacionActiva'] == 1){
                        $this->lc_regs['appedir'] = $lc_datos[2];
                        $statusMasivo = $this->acumulacionMasivo($lc_datos[3], $lc_datos[4], $lc_datos[1], $lc_datos[5], $lc_datos[6]);
                        $this->lc_regs['statusMasivo'] = $statusMasivo;
                    }
                    // fin masivo
                }
                return json_encode($this->lc_regs);

            case 'validarUsuarioAdministrador':
                $lc_sql = "EXEC [facturacion].[USP_validaUsuario] $lc_datos[1], '$lc_datos[0]', '$lc_datos[2]','$lc_datos[3]', '$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['admini'] = $this->ifNum($row["admini"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'validarUsuarioCreditoSinCupon':
                $lc_sql = "EXEC [facturacion].[USP_validaUsuario] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]','$lc_datos[3]', '$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['admini'] = $row["admini"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'claveAcceso':
                $lc_sql = "EXEC [facturacion].[USP_ClaveAcceso] '$lc_datos[0]','$lc_datos[1]'";
                $result=$this->fn_ejecutarquery($lc_sql);
                if(isset($_SESSION['medioMensaje']) && $_SESSION['medioMensaje']!="-1"){
                    $resmedio=$this->actualizarFacturaMedio($lc_datos[0], $_SESSION['medioTipo'], $_SESSION['totalFacturaPuntosCanjeados'], $_SESSION['medioMensaje']);
                    //una vez se termine la facturacion se resetea el valor
                    $_SESSION['medioMensaje']="-1";
                }
                if ($result){ return true; }else{ return false; };
            case 'actualiza_estados_OrdenYfactura':
                $lc_sql = "EXEC [facturacion].[IAE_estados_OrdenYfactura] '$lc_datos[0]','$lc_datos[1]' , '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {

                        $this->lc_regs = array(
                            "direccion" => $row['direccion'],
                            "url" => $row['url']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'actualizaFacturacion':
                $lc_sql = "EXEC [facturacion].[IAE_TipoFacturacion] '$lc_datos[0]','$lc_datos[1]', '$lc_datos[2]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };


            case 'autoConsumoCupon':
                $lc_sql = "exec [facturacion].[IAE_AutoConsumoCupon] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]',$lc_datos[3],'$lc_datos[4]','$lc_datos[5]','$lc_datos[6]',$lc_datos[7],$lc_datos[8]";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'validaExisteFormaPagoSalir':/**/
                $lc_sql = "exec [facturacion].[USP_verifica_forma_pago] '$lc_datos[0]' , '$lc_datos[1]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['pagado'] = $row["pagado"];
                        $this->lc_regs['impreso'] = $row["impreso"];
                        //Retorna si un descuento fua aplicado a la factura
                        $this->lc_regs['descuento'] = $row["descuento"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'ticketPromedio':
                $lc_sql = "EXEC facturacion.USP_TicketPromedio '$lc_datos[1]',$lc_datos[2],$lc_datos[3]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['ticketProyectado'] = $row["ticketProyectado"];
                        $this->lc_regs['ticketActual'] = $row["ticketActual"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'aplicaPagoPredeterminado':
                $lc_sql = "EXEC [facturacion].[USP_aplicaPagoPredeterminado] $lc_datos[3], '$lc_datos[1]', '$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['fmp_descripcion'] = $row["fmp_descripcion"];
                        $this->lc_regs['fmp_id'] = $row["fmp_id"];
                        $this->lc_regs['tfp_id'] = $row["tfp_id"];
                        $this->lc_regs['autorizacion'] = $this->ifNum($row["autorizacion"]);
                        $this->lc_regs['tfp_descripcion'] = $row["tfp_descripcion"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'validaSalirOrden':
                $lc_sql = "EXEC fac_validaSalirOrden $lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['rst_retoma_orden'] = $row["rst_retoma_orden"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'actualizaTipoFacturacion':
                $lc_sql = "update Cabecera_Factura set tf_id=1 where cfac_id='$lc_datos[0]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'ws_logRecepcion':
                $lc_sql = "declare @cfac varchar(20)
					set @cfac=(select cfac_id from Cabecera_Factura where cfac_claveAcceso='$lc_datos[0]')
					insert into log_facturacion_electronica values('$lc_datos[0]','$lc_datos[1]','$lc_datos[2]',$lc_datos[3],@cfac,getdate())";

                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'ws_logAutorizacion':
                $lc_sql = "declare @cfac varchar(20)
					set @cfac=(select cfac_id from Cabecera_Factura where cfac_claveAcceso='$lc_datos[9]')
					insert into log_facturacion_electronica values('$lc_datos[9]','$lc_datos[10]','$lc_datos[11]',$lc_datos[12],@cfac,getdate())";

	            $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'consultaSwtTransaccional':
                $lc_sql = "EXEC [facturacion].[USP_consulta_tipoEnvio] $lc_datos[1],'$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['tpenv_id'] = $row["tpenv_id"];
                        $this->lc_regs['tpenv_descripcion'] = $row["tpenv_descripcion"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'TransaccionalSWT':
                $lc_sql = "select tpenv_id from Estacion where est_ip='$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['tpenv_id'] = $row["tpenv_id"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'inserta_canalComprobante':
                $lc_sql = "DECLARE @claveAcceso VARCHAR(50), @claveReversa varchar(49)
					set @claveAcceso=(select REPLACE(convert(varchar(10), getdate(), 103),'/','') +  tc.tcp_codigo+
						e.emp_ruc+ '1' +r.rst_serie+ r.rst_puntoemision + SUBSTRING(cf.cfac_id,6,10) + '41261533' +te.tem_codigo
						from Empresa e 
						inner join Pais p on p.pais_id=e.pais_id
						inner join Tipo_Emision te on te.tem_id=e.tem_id
						inner join Tipo_Ambiente ta on e.tam_id=ta.tam_id
						inner join Cadena c on e.emp_id=c.emp_id
						inner join Restaurante r on r.cdn_id=c.cdn_id
						inner join Cabecera_Factura cf on cf.rst_id=r.rst_id
						inner join cliente cli on cli.cli_id=cf.cli_id
						inner join Tipo_Documento td on td.tpdoc_id=cli.tpdoc_id
						inner join dbo.Tipo_Comprobante tc on tc.tcp_id=cf.tcp_id 
						WHERE cf.cfac_id='$lc_datos[0]')
						select @claveAcceso+		
						dbo.fn_digitoVerificador(@claveAcceso) as claveAcceso";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['claveAcceso'] = $row["claveAcceso"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'direccion_empreson_esa':
                $lc_sql = "select upper(e.emp_direccion) as direccion from Restaurante r inner join Cadena c on r.cdn_id=c.cdn_id
							inner join Empresa e on e.emp_id=c.emp_id
						where r.rst_id=$lc_datos[0]";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'lee_canalXMLfirmado':
                $lc_sql = "declare @claveAcceso varchar(50)
				set @claveAcceso=(select cfac_claveAcceso from Cabecera_Factura where cfac_id='$lc_datos[0]')
				if exists(select * from Canal_Movimiento_comprobante where std_id=51 and cmp_nombre_comprobante=@claveAcceso)
					begin
						select 'si' as existe,cmp_id,cmp_nombre_comprobante as nombreComprobante from Canal_Movimiento_comprobante where cmp_nombre_comprobante=@claveAcceso and  
						std_id=51 
					end
				else
						select 'no' as existe
					/*if exists(select fir_id,substring(fir_nombre_comprobante,1,49) from canal_firma_comprobante)
					begin
					select 'si' as existe,fir_id,substring(fir_nombre_comprobante,1,49) as nombreComprobante, fir_nombre_comprobante from canal_firma_comprobante
					end
					else
					select 'no' as existe*/";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['existe'] = $row["existe"];
                        //echo $row["existe"];
                        if ($row["existe"] == 'si') {
                            $this->lc_regs['cmp_id'] = $row["cmp_id"];
                            $this->lc_regs['nombreComprobante'] = $row["nombreComprobante"];
                            //$this->lc_regs['fir_nombre_comprobante'] = $row["fir_nombre_comprobante"];								
                        }
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'verifica_respuesta':
                $lc_sql = "SELECT ltrim(rtrim(cres_codigo)) as cres_codigo FROM SWT_Respuesta_Autorizacion where rsaut_id=$lc_datos[2]";
		        $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'mid_tid':
                $lc_sql = "select r.rst_mid,e.est_tid from Restaurante r inner join Estacion e on r.rst_id=e.rst_id
                    where e.est_id=$lc_datos[1]";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'consulta_tarjeta':
                $lc_sql = "select f.fmp_descripcion from SWT_Respuesta_Autorizacion swt inner join Formapago f on swt.fpf_id=f.fmp_id where SWT.rsaut_id=$lc_datos[2]";
		        $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'cabecera_respuesta':
                $lc_sql = "SET DATEFORMAT DMY
					selecT replace(CONVERT(VARCHAR (15),rsaut_fecha,103),'/','') as lote, 						
					rsaut_numero_tarjeta,rsaut_numero_autorizacion,rsaut_secuencial_transaccion
 					from SWT_Respuesta_Autorizacion   where rsaut_id=$lc_datos[2]";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'grabacanalmovimientoVoucher':
                $lc_sql = "EXEC [facturacion].[IAE_grabacanalMovimientoVoucher]  'I','$lc_datos[0]','$lc_datos[1]','$lc_datos[2]',$lc_datos[3],'$lc_datos[4]'";
		        $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'grabacanalmovimientoImpresionFactura':
                $lc_sql = "EXEC facturacion.IAE_grabacanalMovimientoFactura  '$lc_datos[2]','$lc_datos[0]','$lc_datos[1]'";
		        $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'grabacanalmovimientoImpresionFacturaElectronica':
                $lc_sql = "EXEC facturacion.IAE_grabacanalMovimientoFactura  '$lc_datos[2]','$lc_datos[0]','$lc_datos[1]'";
		        $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'referencia_noaprobado':
                $lc_sql = "selecT rsaut_secuencial_transaccion
 					from SWT_Respuesta_Autorizacion   where rsaut_id=$lc_datos[2]";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'detalle_noaprobado':
                $lc_sql = "declare @fact varchar(20),@formapago int
                            set @fact=(select rsaut_movimiento from SWT_Respuesta_Autorizacion where rsaut_id=$lc_datos[2])
                            set @formapago=(select fpf_id from SWT_Respuesta_Autorizacion where rsaut_id=$lc_datos[2])
                            SELECT fpf_id,fpf_total_pagar FROM Formapago_Factura where cfac_id=@fact and fmp_id=@formapago";
		        $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'valores_voucher':
                $lc_sql = "/*declare @total float,@valor_total float,@monto_iva float,@monto_noiva float,@iva float,@cfac_id varchar(20),@formaPago int,@respuesta_id int
			set @respuesta_id=$lc_datos[2]
                        set @cfac_id=(select rsaut_movimiento  from SWT_Respuesta_Autorizacion where rsaut_id=@respuesta_id)
                        set @formaPago=(select fpf_id from SWT_Respuesta_Autorizacion where rsaut_id=@respuesta_id)
                        set @total=(select sum(round((cb.cfac_total-ff.fpf_total_pagar),2))as Total
			from Cabecera_Factura cb inner join Formapago_Factura ff on cb.cfac_id=ff.cfac_id
			where ff.cfac_id=@cfac_id and ff.fmp_id=@formaPago)

			
if(@total=0)
	BEGIN
		--select 'Ingreso forma pago 0'
			set @valor_total=(select round(cfac_total,2) as cfac_tota  from Cabecera_Factura where cfac_id=@cfac_id)						
			set @monto_iva=(select round(cfac_base_iva,2) as cfac_iva  from Cabecera_Factura where cfac_id=@cfac_id)			
			set @monto_noiva=(select round(cfac_base_cero,2) as cfac_iva  from Cabecera_Factura where cfac_id=@cfac_id)			
			set @iva=(select round(cfac_iva,2) as cfac_iva  from Cabecera_Factura where cfac_id=@cfac_id)			
	END
else
		begin		
			if exists(select top 1(dtfac_id) from Cabecera_Factura cf inner join Detalle_Factura df on cf.cfac_id=df.cfac_id
						inner join Plus pl on df.plu_id=pl.plu_id								
						where cf.cfac_id=@cfac_id and pl.plu_impuesto=1)
				begin
					--select 'con impuestos y mixtos'
					set @valor_total=(select round(fpf_total_pagar,2) as cfac_tota  from Formapago_Factura where cfac_id=@cfac_id and fmp_id=@formaPago
									and fpf_id=(select max(fpf_id) from Formapago_Factura where cfac_id=@cfac_id and fmp_id=@formaPago))						
					--select @valor_total															
					set @monto_iva=(select round(convert(float,@valor_total)/1.12,2))					
					set @monto_iva=CONVERT(varchar(20),@monto_iva)																					
					set @monto_noiva='0'
					set @iva=(select round(convert(float,@valor_total)-convert(float,@monto_iva),2) as cfac_iva)													
				end	
			else
				begin
				--select 'sin impuestos impuestos'
					set @valor_total=(select round(fpf_total_pagar,2) as cfac_tota  from Formapago_Factura where cfac_id=@cfac_id and fmp_id=@formaPago
									and fpf_id=(select max(fpf_id) from Formapago_Factura where cfac_id=@cfac_id and fmp_id=@formaPago ))																				
					set @monto_iva='0'
					set @monto_noiva='0'
					set @iva=@valor_total											
				end
					
		end

select @valor_total as MontoTotal,@monto_iva as BaseIva,@monto_noiva as BaseNOiva,@iva as Iva*/
					EXEC	[SWT_Totales_Voucher]						
							@respuesta_id=$lc_datos[2];";
            $result = $this->fn_ejecutarquery($lc_sql);
            if ($result){ return true; }else{ return false; };

            ///////////////////////////////////////////////CONSULTA ORDEN DE PEDIDO////////////////////////////////
            case "listaFactura":
                $lc_sql = "EXEC facturacion.USP_listaFactura '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cdn_tipoimpuesto" => trim($row['cdn_tipoimpuesto']),
                            "rst_tipoServicio" => trim($row['rst_tipo_servicio']),
                            "cfac_id" => trim($row['cfac_id']),
                            "usr_id" => trim($row['usr_id']),
                            "est_id" => $row['est_id'],
                            "cfac_fechacreacion" => $row['cfac_fechacreacion'],
                            "plu_id" => trim($row['plu_id']),
                            "plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
                            "dtfac_cantidad" => $row['dtfac_cantidad'],
                            "dtfac_precio_unitario" => $row['dtfac_precio_unitario'],
                            "dtfac_iva" => $row['dtfac_iva'],
                            "servicio" => $row['servicio'],
                            "dtfac_total" => $row['dtfac_total'],
                            "totalizado" => $row['totalizado'],
                            "plu_impuesto" => trim($row['plu_impuesto']),
                            "cfac_descuento_empresa" => $row['cfac_descuento_empresa']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "resumenFormaPago":
                $lc_sql = "EXEC [facturacion].[USP_muestra_resumenFormasPago] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "fmp_descripcion" => trim($row['fmp_descripcion']),
                            "fpf_total_pagar" => round($row['fpf_total_pagar'], 2));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargaBilletes":
                //todo:Guillermo revision cuentas pendientes y recuperacion
                $lc_sql = "EXEC [facturacion].[USP_consulta_billetes] $lc_datos[0], $lc_datos[1]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("btd_id" => trim($row['btd_id']),
                            "btd_Valor" => $this->ifNum($row['btd_Valor']),
                            "descFp" => $row['descFp'],
                            "idFp" => $row['idFp'],
                            "tfpId" => $row['tfpId'],
                            "descTfp" => $row['descTfp'],
                            "autoriza" => $row['autoriza']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            ///////////////////////////////////////////////GUARDAR FACTURA////////////////////////////////
            case "insertarFactura":            
                $lc_sql = " EXEC facturacion.IAE_Fac_InsertFactura $lc_datos[0],'$lc_datos[1]','$lc_datos[2]',$lc_datos[3],'$lc_datos[4]','$lc_datos[5]','$lc_datos[6]',0,$lc_datos[8]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "Cod_Factura" => utf8_encode($row['Cod_Factura']),
                            "btn_cancela_pago" => $this->ifNum($row['btn_cancela_pago']),
                            "cdn_tipoimpuesto" => trim($row['cdn_tipoimpuesto']),
                            "rst_tipoServicio" => trim($row['rst_tipo_servicio']),
                            "cfac_id" => trim($row['cfac_id']),
                            "usr_id" => trim($row['usr_id']),
                            "est_id" => $row['est_id'],
                            "cfac_fechacreacion" => $row['cfac_fechacreacion'],
                            "plu_id" => trim($row['plu_id']),
                            "plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
                            "dtfac_cantidad" => $this->ifNum($row['dtfac_cantidad']),
                            "dtfac_precio_unitario" => $row['dtfac_precio_unitario'],
                            "dtfac_iva" => $this->ifNum($row['dtfac_iva']),
                            "servicio" => $this->ifNum($row['servicio']),
                            "dtfac_total" => $row['dtfac_total'],
                            "totalizado" => $row['totalizado'],
                            "plu_impuesto" => trim($row['plu_impuesto']),
                            "cfac_descuento_empresa" => $this->ifNum($row['cfac_descuento_empresa']),
                            //devueleve el valor si el producto aplica descuento
                            "descuento" => $row['desc_producto'],
                            "desc_valorFijo" => $this->ifNum($row['valorFijo']),
                            "desc_porcentaje" => $this->ifNum($row['porcentaje']),
                            "canje_puntos" => $this->ifNum($row['canje_puntos']),
                            "tipoBeneficioCupon" => $this->ifNum($row['tipoBeneficioCupon']),
                            "puntos" => $this->ifNum($row['puntos']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            ///////////////////////////////////////////////GUARDAR FACTURA////////////////////////////////
            case "insertarFactura_FS":
                $lc_sql = " EXEC facturacion.IAE_Fac_InsertFactura_FS $lc_datos[0],'$lc_datos[1]','$lc_datos[2]',$lc_datos[3],'$lc_datos[4]','$lc_datos[5]','$lc_datos[6]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "Cod_Factura" => utf8_encode($row['Cod_Factura']),
                            "btn_cancela_pago" => $row['btn_cancela_pago'],
                            "cdn_tipoimpuesto" => trim($row['cdn_tipoimpuesto']),
                            "rst_tipoServicio" => trim($row['rst_tipo_servicio']),
                            "cfac_id" => trim($row['cfac_id']),
                            "usr_id" => trim($row['usr_id']),
                            "est_id" => $row['est_id'],
                            "cfac_fechacreacion" => $row['cfac_fechacreacion'],
                            "plu_id" => trim($row['plu_id']),
                            "plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
                            "dtfac_cantidad" => $row['dtfac_cantidad'],
                            "dtfac_precio_unitario" => $row['dtfac_precio_unitario'],
                            "dtfac_iva" => $row['dtfac_iva'],
                            "servicio" => $row['servicio'],
                            "dtfac_total" => $row['dtfac_total'],
                            "totalizado" => $row['totalizado'],
                            "plu_impuesto" => trim($row['plu_impuesto']),
                            "cfac_descuento_empresa" => $row['cfac_descuento_empresa'],
                            //devueleve el valor si el producto aplica descuento
                            "descuento" => $row['desc_producto'],
                            "desc_valorFijo" => $row['valorFijo'],
                            "desc_porcentaje" => $row['porcentaje']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            ///////////////////////////////////////////////ACTUALIZAR FACTURA////////////////////////////////
            case "actualizarFactura":

                $lc_sql = "EXEC [facturacion].[USP_FacturaCliente] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
    //            $lc_sql = "EXEC [facturacion].[USP_FacturaCliente] '6C4557FE-660E-E611-80C7-0050568602D0', 'V009F000398723', '67F1A02E-9A00-E911-80DD-000D3A019254'";

                $respuesta = $this->fn_ejecutarquery($lc_sql);

                try {
					  
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "respuesta" => $row['respuesta']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
					
	
                    return json_encode($this->lc_regs);
                }catch (Exception $e){
                    if ($respuesta){
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                
                

            case "insertaCanalAperturaCajon":
                $lc_sql = "EXEC [facturacion].[IAE_abreCajon] '$lc_datos[1]', '$lc_datos[0]', '$lc_datos[2]','I','$lc_datos[3]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'impresion_creditoSinCupon':
                $lc_query = "EXEC [facturacion].[USP_impresiondinamica_creditoSinCupon] '$lc_datos[0]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'impresion_creditoEmpleado':
                $lc_query = "EXEC [facturacion].[USP_impresiondinamica_creditoEmpleado] '$lc_datos[0]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'impresion_creditoProducto':
                $lc_query = "EXEC [facturacion].[USP_impresiondinamica_creditoProducto] '$lc_datos[0]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            /////////////////////////////////////MOVIMIENTOS INGRESO Y EGRESO////////////////////////////////////////                
            ////////////////////////////////////////////////////////////////////////////////////////////////////////
            ///////////////////////////////////DESARROLLADO POR:Juan Estévez///////////////////////////////////////
            //////////////////////////////////DESCRIPCION: Ingreso y Egreso///////////////////////////////////////
            /////////////////////////////////FECHA CREACION  : 14/12/2016////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////////////////////////////
            case "impresion_ingreso_egreso":
                $lc_query = "EXEC [facturacion].[USP_impresiondinamica_ingreso_y_egreso] '$lc_datos[0]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            ///////////////////////////////////////////////GUARDAR FORMAS DE PAGO///////////////////////////
            case "insertarFormaPagoCredito":
                $lc_sql = "EXEC [facturacion].[IAE_insertaFormaPagoCreditoEmpresa]  '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', $lc_datos[3],$lc_datos[4], $lc_datos[5],$lc_datos[8],'$lc_datos[7]','$lc_datos[9]','$lc_datos[10]','$lc_datos[11]','$lc_datos[12]','$lc_datos[13]','$lc_datos[14]','$lc_datos[15]','$lc_datos[16]','$lc_datos[17]','$lc_datos[18]','$lc_datos[19]',$lc_datos[20],'$lc_datos[21]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    $this->lc_regs['str'] = 0;
                }
                return json_encode($this->lc_regs);

            case "insertarFormaPago":
                $lc_sql = "EXEC [facturacion].[fac_insertaFormaPago] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', $lc_datos[3],$lc_datos[4], $lc_datos[5],$lc_datos[8],'$lc_datos[7]',$lc_datos[9]";
                $stmt = $this->fn_ejecutarquery($lc_sql);
                if ($stmt && $stmt->columnCount() > 0) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "fmp_descripcion" => isset($row['fmp_descripcion']) ? trim($row['fmp_descripcion']) : '',
                            "fpf_total_pagar" => isset($row['fpf_total_pagar']) ? round($row['fpf_total_pagar'], 2) : 0
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();

                try {
                    @error_log( 
                        date('d-m-Y H:i:s')
                        ." - Documento: js/ajax_pagoTarjetaDinamico.js"
                        ." - Consulta: ingresaFormaPagoTarjeta"
                        ." - Sentencia: $lc_sql"
                        ." - Salida:"
                        ." fmp_descripcion; ".$this->lc_regs[0]['fmp_descripcion']
                        ." , fpf_total_pagar; ".$this->lc_regs[0]['fpf_total_pagar']
                        ."\n"
                    , 3, "../logs/info.log" );
                } catch (Exception $e) { ; }

                return json_encode($this->lc_regs);

            case "insertarRequerimientoAutorizacion":
                $lc_sql = "EXEC	[facturacion].[IAE_InsertaRequerimientoAutorizacion]'$lc_datos[8]','$lc_datos[0]','$lc_datos[2]',$lc_datos[4],'$lc_datos[3]','$lc_datos[1]','$lc_datos[5]','$lc_datos[6]',$lc_datos[7]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    $this->lc_regs['str'] = 0;
                }
                return json_encode($this->lc_regs);

            case "eliminaformadepago":
                $lc_sql = "declare @codigo int
						set @codigo=(select MAX(fpf_id) from Formapago_Factura where cfac_id='$lc_datos[0]' and fmp_id<>1)
						delete from Formapago_Factura where fpf_id=@codigo";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            /////////////////////////////////////////FORMAS DE PAGO////////////////////////////////////////////////////
            case 'validaItemPagado':
                $lc_sql = "EXEC [facturacion].[USP_validaItemPagado] '" . $lc_datos[0] . "'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("dop_estado" => $row['dop_estado']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                $_SESSION['fdznDocumento']=""; //vaciamos al final de todo fdznDocumento
                break;

            case 'obtenerurlsplit':
                $lc_sql = "EXEC [facturacion].[UPS_ESTADO_ORDEN_Y_FACTURA] '" . $lc_datos[0] . "','" . $lc_datos[1] . "'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("direccion" => $row['direccion'],
                            "url_cuenta" => $row['url_cuenta']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break; 

            case 'esperaRespuestaRequerimientoAutorizacion':
                $lc_sql = "EXEC [facturacion].[USP_esperaRespuestaRequerimientoAutorizacion] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['existe'] = $row["existe"];
                        //echo $row["existe"];
                        if ($row["existe"] == 1) {
                            $this->lc_regs['rsaut_respuesta'] = $row["rsaut_respuesta"];
                            $this->lc_regs['cres_codigo'] = $row["cres_codigo"];
                            $this->lc_regs['fpf_id'] = $row["fpf_id"];
                            $this->lc_regs['rsaut_id'] = $row["rsaut_id"];
                            $this->lc_regs['errorTrama'] = utf8_encode($row["errorTrama"]);
                            $this->lc_regs['codigoAutorizador'] = $row["codigoAutorizador"];
							$this->lc_regs['respuestaAutorizacion'] = $row["respuestaAutorizacion"];																																			 
                        }
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
            /* Obtiene los informacion de la cabecera de la factura para una reimpresion */
            case 'visorCabeceraFactura':
                $lc_sql = "EXEC imp_voucherCabeceraFactura '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("tf_id" => trim($row['tf_id']),
                            "emp_razon_social" => utf8_encode(trim($row['emp_razon_social'])),
                            "emp_direccion" => utf8_encode(trim($row['emp_direccion'])),
                            "emp_ruc" => utf8_encode(trim($row['emp_ruc'])),
                            "rst_direccion" => utf8_encode(trim($row['rst_direccion'])),
                            "usr_usuario" => trim($row['usr_usuario']),
                            "cfac_fechacreacion" => utf8_encode(trim($row['cfac_fechacreacion'])),
                            "cli_nombres" => utf8_encode(trim($row['cli_nombres'])),
                            "cli_apellidos" => utf8_encode(trim($row['cli_apellidos'])),
                            "cli_documento" => trim($row['cli_documento']),
                            "cli_telefono" => trim($row['cli_telefono']),
                            "cli_direccion" => trim($row['cli_direccion']),
                            "documento" => trim($row['documento']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* Obtiene los informacion del detalle de la factura para una reimpresion */
            case 'visorDetalleFactura':
                $lc_sql = "EXEC imp_voucherDetalleFactura '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("dtfac_cantidad" => $row['dtfac_cantidad'],
                            "plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
                            "dtfac_precio_unitario" => number_format(($row['dtfac_precio_unitario']), 2, ".", ""),
                            "dtfac_total" => number_format(($row['dtfac_total']), 2, ".", ""));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* Obtiene los informacion del detalle de la factura para una reimpresion */
            case 'totalDetalleFactura':
                $lc_sql = " SELECT cf.cfac_subtotal, cf.cfac_iva, cf.cfac_total, c.cdn_tipoimpuesto, cf.cfac_base_cero, cf.cfac_base_iva
							FROM Cabecera_Factura cf inner join Restaurante r on cf.rst_id=r.rst_id
							INNER JOIN Cadena c ON c.cdn_id=r.cdn_id
							WHERE cf.cfac_id = '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cfac_subtotal" => number_format(($row['cfac_subtotal']), 2, ".", ""),
                            "cfac_iva" => number_format(($row['cfac_iva']), 2, ".", ""),
                            "cfac_total" => number_format(($row['cfac_total']), 2, ".", ""),
                            "cdn_tipoimpuesto" => number_format(($row['cdn_tipoimpuesto']), 2, ".", ""),
                            "cfac_base_cero" => number_format(($row['cfac_base_cero']), 2, ".", ""),
                            "cfac_base_iva" => number_format(($row['cfac_base_iva']), 2, ".", ""));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            /* Obtiene informacion de las fomas de pago de la factura para una reimpresion */
            case 'formasPagoDetalleFactura':
                $lc_sql = "EXEC imp_formasPago '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fmp_descripcion" => utf8_encode(trim($row['fmp_descripcion'])),
                            "cfac_total" => number_format(($row['cfac_total']), 2, ".", ""));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'impresion_voucher':
                $lc_query = "EXEC [facturacion].[USP_impresiondinamica_Voucher] $lc_datos[2]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };
            case 'validaFactura':
                $data = '';
                $lc_query = "EXEC [facturacion].[USP_ValidaFacturacion] '$lc_datos[0]', '$lc_datos[1]';";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $data = $row['resultado_json'];
                    }
                }
                return $data;
            case 'condicionConfiguracionLocalizador':
                $data = '';
                $lc_query = "SELECT [config].[fn_ColeccionRestaurante_condicionConfiguracionLocalizador] ($lc_datos[0], $lc_datos[1], '$lc_datos[2]') AS condicionConfiguracionLocalizador";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $data = $row['condicionConfiguracionLocalizador'];
                    }
                }
                return $data;
            case 'guardarConfiguracionLocalizador':
                $data = '';
                $lc_query = "UPDATE Cabecera_Orden_Pedido SET Cabecera_Orden_PedidoVarchar5 = '$lc_datos[0]' where IDCabeceraOrdenPedido = '$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    $data = true;
                }
                return $data;

        }
    }

    // MASIVO
    
    function fn_obtenerCandena(){
        $sql = "SELECT * FROM cadena";

        $cadena=0;

        if($this->fn_ejecutarquery($sql)){
            while ($row = $this->fn_leerarreglo()) {
                $cadena = $row['cdn_id'] * 1;
            }
        }

        return $cadena;
    }

    function fn_politicaApiBrandid($idCadena){
        
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'WS CONFIGURACIONES', 'MASIVO BRAND ID') AS brand_id";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();

        if(isset($row['brand_id']) || !empty($row['brand_id'])){
            $data = (object)[
                "brand" => $row['brand_id']
            ];
            return $data;
        }
        return false;
    }

	

    // REDEM_ID
    function fn_politicaApiRedemId($idCadena){
        
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'WS CONFIGURACIONES', 'MASIVO REDEM ID') AS redem_id";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();

        if(isset($row['redem_id']) || !empty($row['redem_id'])){
            $data = (object)[
                "redem" => $row['redem_id']
            ];
            return $data;
        }

        return false;
    }

    // CHANNEL_ID
    
    function fn_politicaApiChannelId($idCadena){
        
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'WS CONFIGURACIONES', 'MASIVO CHANEL ID') AS channel_id";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();

        if(isset($row['channel_id']) || !empty($row['channel_id'])){
            $data = (object)[
                "channel" => $row['channel_id']
            ];
            return $data;
        }

        return false;
    }

    // politica url autorizacion

    function fn_politicUrlCreateEventoMasivo($idCadena){
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'WS RUTA SERVICIO', 'MASIVO CREAR EVENTO') AS evento";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();

        if(isset($row['evento']) || !empty($row['evento'])){
            $data = (object)[
                "url" => $row['evento']
            ];
            return $data;
        }

        return false;
    }

      function fn_politicUrlRedemUrl($idCadena){
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'WS RUTA SERVICIO', 'MASIVO OBTENER RECOMPENSA') AS evento";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();

        if(isset($row['evento']) || !empty($row['evento'])){
            $data = (object)[
                "url" => $row['evento']
            ];
            return $data;
        }

        return false;
    }

      function acumulacionMasivo($factura, $barer, $uid, $tipoPago, $idUser, $canjePuntos=0){
		$tipoMAsivo='';
        try {
            /*if(isset($_SESSION['medioMensaje']) && $_SESSION['medioMensaje']!="-1"){
                return true;
            }*/
            //code...
            $suma_puntos=0;
            $suma_factura=0;
            $envia_puntos_factura_nueva=isset($_SESSION['tipoCanjeFinal']) && $_SESSION['tipoCanjeFinal']=="CANJE Y ACUMULACION"?true:false;
            $_SESSION['totalFacturaPuntosCanjeados']=0;
            $_SESSION['cantidadPuntosCanjeados']=0;
            $_SESSION['tipoCanjeFinal']="ACUMULACION";
            $_SESSION['medioMensaje']=isset($_SESSION['medioMensaje']) && $_SESSION['medioMensaje']!="-1"?$_SESSION['medioMensaje']:"-1";
            $_SESSION['medioTipo']=isset($_SESSION['medioTipo']) && $_SESSION['medioTipo']=="CANJE Y ACUMULACION"?$_SESSION['medioTipo']:"ACUMULACION";
            
            $cadena = $this->fn_obtenerCandena();
            $sql = "SELECT de.*, isnull(pt.puntos,0) puntos FROM Detalle_Factura de
                        LEFT JOIN [webservices].[FIDELIZACION_Puntos] pt on pt.IDProducto=de.plu_id
                        where cfac_id='$factura' and dtfac_total>0";
    
            $sqlFactura = "SELECT * FROM Cabecera_Factura WHERE cfac_id = '$factura'";
            
            $numeroFactura = '0';
            $restaurante = '';
            $valorFactura = 0;
            $idRestaurante = 0;
    
            if ($this->fn_ejecutarquery($sqlFactura)) {
                while ($row = $this->fn_leerarreglo()) {
                    $numeroFactura = $row['cfac_id'];
                    $sqlRestaurante = "SELECT * FROM Restaurante WHERE rst_id = ".$row['rst_id'];
                    $valorFactura = $row['cfac_total'];
                    $idRestaurante = $row['rst_id'];

                    if ($this->fn_ejecutarquery($sqlRestaurante)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $restaurante = $row['rst_cod_tienda'];
                        }
                    }
                }
            }
    
            $tipoMAsivo=$canjePuntos==0?"ACUMULACION":"CANJE";
            //$redemId=$this->fn_consultaRedemId($cadena, $barer);
            $redemId = $this->fn_politicaApiRedemId($cadena);
                                    
            if(!$redemId){
                $_SESSION['medioMensaje']="ERROR POLITICA REDEM ID";
                $_SESSION['medioTipo']="ERROR";
                //$resmedio=$this->actualizarFacturaMedio($factura, "ERROR", 0, "POLITICA REDEM ID");
                //guardamos respuesta en el log
                $sql = "EXEC [facturacion].[IAE_Logs_auditoria_transaccion] $idRestaurante, 'API MASIVO REDEM ID', 'Error: NO SE PUEDE OBTENER POLITICA REDEM ID', '$tipoMAsivo', '', '','$idUser'";

                if(!$this->fn_ejecutarquery($sql)){
                    return false;
                }
                return false;
            }


            $productos = [];
            if ($this->fn_ejecutarquery($sql)) {
                while ($row = $this->fn_leerarreglo()) {
                    $producto = [
                        "sku" => $row['plu_id'],
                        "amount" => (int) $row['dtfac_cantidad'],
                        "value" => round(($row['dtfac_total']/($row['dtfac_cantidad']<1?1:$row['dtfac_cantidad'])),2), //$canjePuntos==1?$row['puntos']:$row['dtfac_total']
                    ];
                    $suma_factura=round(($suma_factura+$row['dtfac_total']),2);

                    //añadimos el canje de puntos
                    //Detalle_FacturaVarchar4 suficiente para validar si pasa el canje
                    $validaPuntos=(int)$row['Detalle_FacturaVarchar4'];
					$puntos=(int) $row['puntos'];
                    if($puntos>0 && ($validaPuntos>0 || ($envia_puntos_factura_nueva && $tipoMAsivo="CANJE"))){
                        if($tipoMAsivo=="ACUMULACION" || $tipoMAsivo=="CANJE Y ACUMULACION"){
                            $tipoMAsivo="CANJE Y ACUMULACION";
                        }else{
                            $redeem = [
                                [
                                    "id" => $redemId->redem,  // El ID puede ser dinámico si lo necesitas
                                    "amount" => (int) $row['puntos']  // El amount también puede ser dinámico
                                ]
                            ];
                            $suma_puntos=$suma_puntos+$row['puntos'] * $row['dtfac_cantidad'];
                            $producto['redeem'] = $redeem;
                            
                            //solo se añade el producto si es 
                            array_push($productos, $producto);
                        }
                    }else{
                        array_push($productos, $producto);
                    }
    
                }
            }

												 
            if($cadena > 0){
                $channel = $this->fn_politicaApiChannelId($cadena);
                $brand = $this->fn_politicaApiBrandid($cadena);
                $createEventoUrl = $this->fn_politicUrlCreateEventoMasivo($cadena);

                if($channel && $brand && $createEventoUrl){

                    $orden = [
                        "purchase_id" => $numeroFactura,
                        "channel_id" => $channel->channel,
                        "store_id" => $restaurante,
                        "value" => (float) number_format((float) $valorFactura, 2, '.', ''),
                        "products" => $productos,
                        "payment_method" => $tipoPago //$canjePuntos==1?"POINTS":$tipoPago
                    ];
            
                    $masivoBody = [
                        "customer_id" => $uid,
                        "brand_id" => $brand->brand,
                        "type" => "PURCHASE", //AMIGOSCASA SOLO ES PARA ACUMULAR PUNTO PARA UN CAJERO $canjePuntos==1 && $_SESSION['marcaMasivo']!="APPEDIR"?"AMIGOSCASA":"PURCHASE"
                        "order" => $orden,
                    ];
            
                    $curl = curl_init();
    
                    curl_setopt_array($curl, [
                        CURLOPT_URL => $createEventoUrl->url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => json_encode($masivoBody),
                        CURLOPT_HTTPHEADER => [
                            "Authorization: Bearer ".$barer,
                            "Content-Type: application/json"
                        ],
                    ]);
            
                    $response = json_decode(curl_exec($curl));
                    $err = curl_error($curl);
                    curl_close($curl);
        
                    if (json_last_error() !== JSON_ERROR_NONE || !empty($err) || (is_object($response) && isset($response->error))) {
                        if(isset($response->error) && $response->error=="Invalid or expired access token"){
							require_once("../clases/clase_seguridades.php");
                            $seguridades = new seguridades();
                            $autorizacionApiMasivo = json_decode($seguridades->getAutorizacionApiMasivo());
                            $posicionMarca=$_SESSION['marcaMasivo']!="APPEDIR"?2:1;
                            $barer=$autorizacionApiMasivo->{$posicionMarca}->token;
                            return $this->acumulacionMasivo($factura, $barer, $uid, $tipoPago, $idUser, $canjePuntos);
                        }
                        
                        
                        $_SESSION['medioMensaje']="-1";
                        $_SESSION['medioTipo']="ERROR";
                        //$resmedio=$this->actualizarFacturaMedio($factura, "ERROR", $suma_puntos, "ERROR EN PETICION");
                        $sql = "EXEC [facturacion].[IAE_Logs_auditoria_transaccion] $idRestaurante, 'API MASIVO', 'Error: Ocurrio un error en la petición - Peticion: ".json_encode($masivoBody)." - Respuesta: ".str_replace("'","",json_encode($response))."', '$tipoMAsivo', '', '','$idUser'";
                        if(!$this->fn_ejecutarquery($sql)){
                            return false;
                        }
    
                        return false;
                    }
    
                    $evento = $response->data->id;
    
                    // Editamos el campo evento masivo
                    $sqlInsertar = "EXEC [facturacion].[ActualizarEventoMasivo] '$factura', '$evento'";
                    
                    if ($this->fn_ejecutarquery($sqlInsertar)) {
                        // INSERTAMOS EN LA AUDITORA
                        $_SESSION['medioMensaje']="";
                        if($_SESSION['medioTipo']=="CANJE Y ACUMULACION"){
                            $_SESSION['medioTipo']="ACUMULACION";
                        }else{
                            $_SESSION['medioTipo']=$tipoMAsivo;
                        }
                        //$resmedio=$this->actualizarFacturaMedio($factura, $tipoMAsivo, $suma_puntos, "");
                        $sql = "EXEC [facturacion].[IAE_Logs_auditoria_transaccion] $idRestaurante, 'API MASIVO', '$tipoMAsivo EN LA API DE MASIVO - Peticion: ".json_encode($masivoBody)." - Respuesta: ".str_replace("'","",json_encode($response))."', '$tipoMAsivo', '', '','$idUser'";
    
                        
                        if(!$this->fn_ejecutarquery($sql)){
                            return false;
                        }
                        //transacciones para responder en el servicio
                        $_SESSION['totalFacturaPuntosCanjeados']=$suma_factura;
                        $_SESSION['cantidadPuntosCanjeados']=$suma_puntos;
                        $_SESSION['tipoCanjeFinal']=$tipoMAsivo;
                        return true;
                    }
                    
                    return false;
                }
            }
            return false;
        } catch (\Exception $th) {
			$sql = "EXEC [facturacion].[IAE_Logs_auditoria_transaccion] $idRestaurante, 'ERROR EN API MASIVO', '$tipoMAsivo EN LA API DE MASIVO - Error: ".$th->getMessage().".', '$tipoMAsivo', '', '','$idUser'";
			$this->fn_ejecutarquery($sql);
            //throw $th;
            return false;
        } finally {
			if($tipoMAsivo!="CANJE Y ACUMULACION"){
				$_SESSION['fidelizacionActiva']=0;
			}
        }
    }

    
    function fn_consultaRedemId($cadena, $barer){
        if($cadena <= 0){
            return false;
        }
        $brand = $this->fn_politicaApiBrandid($cadena);
        $data = $this->fn_politicUrlRedemUrl($cadena);

        if($data){
            $curl = curl_init();
            $url_final=$data->url;
            curl_setopt_array($curl, [
                CURLOPT_URL => $url_final,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $barer"
                ],
            ]);

            $response=curl_exec($curl);
            $response = json_decode($response,true);
            $err = curl_error($curl);

            curl_close($curl);
            if(!$err && isset($response['data'])){
                if (isset($response['data'][0]) && isset($response['data'][0]['id'])) {
                    return $response['data'][0]['id'];
                }else{
                    $_SESSION['fb_mensaje_puntos']="TOTALS NO EXISTE - URL: ".$url_final." DETALLE: ".json_encode($response);
                    return false;
                }
            }else{
                $_SESSION['fb_mensaje_puntos']="RESPUESTA ERRONEA: ".$url_final." DETALLE: ".json_encode($response);
                return false;
            }
        }else{
            $_SESSION['fb_mensaje_puntos']="POLITICA URL MASIVO CLIENTE NO CONFIGURADA";
            return false;
        }
    }

    function actualizarFacturaMedio($idFactura, $tipoVenta="ACUMULACION", $puntos, $mensaje) {
        $lc_sql = "EXEC fidelizacion.IAE_ActualizaCabeceraFacturaMedio '$idFactura', '$tipoVenta', $puntos, '$mensaje'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array(
                    "mensaje" => utf8_encode("Registro Exitoso"), 
                    "estado" => 1,
                    "respuesta" => $row['mensaje']
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    function fn_cargaCadenaEmpresa($lc_datos) {
        $lc_sql = "EXEC [facturacion].[USP_cargaCadenaEmpresa] '$lc_datos[0]','$lc_datos[1]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("cdn_id" => $row['cdn_id'],
                    "cdn_descripcion" => utf8_encode($row['cdn_descripcion']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    function fn_cargaConceptosAyuda($lc_datos) {
        $lc_sql = "EXEC [facturacion].[USP_conceptosAyudaAuditoria] '$lc_datos[0]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idColeccionDatosPais" => $row['idColeccionDatosPais'], "idColeccionPais" => $row['idColeccionPais']
                    , "Descripcion" => utf8_encode($row['Descripcion']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function fn_cargaRestauranteCadena($lc_datos) {
        $lc_sql = "EXEC  [facturacion].[USP_cargaTiendasCadena] $lc_datos[0],'$lc_datos[1]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                    "rst_descripcion" => utf8_encode($row['rst_descripcion']),
                    "rst_cod_tienda" => utf8_encode($row['rst_cod_tienda']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function fn_impresionVoucherComercio($lc_datos) {
        $lc_sql = "EXEC [facturacion].[VOUCHER_USP_ImpresionDinamicaComercio] $lc_datos[0], '$lc_datos[1]'";
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result){ return true; }else{ return false; };
    }

    public function fn_impresionVoucherCliente($lc_datos) {
        $lc_sql = "EXEC [facturacion].[VOUCHER_USP_ImpresionDinamicaCliente] $lc_datos[0], '$lc_datos[1]'";
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result){ return true; }else{ return false; };
    }

    public function fn_impresionVoucherNuevoFormato($rsaut_id, $tipo) {
        $lc_sql = "EXEC facturacion.VOUCHER_USP_ImpresionDinamicaClienteKiosko '$rsaut_id', '', '$tipo'";
        if($this->fn_ejecutarquery($lc_sql)){
            $row = $this->fn_leerarreglo();
            return $row["html"];
        }
    }

    public function fn_insertarRequerimientoPinpadUnired($lc_datos) {
        $lc_sql = "EXEC [facturacion].[IAE_SWT_InsertaRequerimientoAutorizacionPinPadProduccion] '$lc_datos[6]','$lc_datos[1]','$lc_datos[8]','$lc_datos[7]','$lc_datos[9]','$lc_datos[2]','$lc_datos[4]','$lc_datos[3]','$lc_datos[5]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $this->lc_regs['str'] = 1;
        } else {
            $this->lc_regs['str'] = 0;
        }
        return json_encode($this->lc_regs);
    }
 
	public function insertarRequerimientoTramaDinamica($lc_datos) 
            {
        $lc_sql = "EXEC [facturacion].[IAE_SWT_InsertaTramaDeRequerimientoDinamica]  '$lc_datos[6]','$lc_datos[1]','$lc_datos[8]','$lc_datos[7]','$lc_datos[9]','$lc_datos[2]','$lc_datos[12]','$lc_datos[10]','$lc_datos[11]','$lc_datos[4]','$lc_datos[3]','$lc_datos[5]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $this->lc_regs['str'] = 1;
        } else {
            $this->lc_regs['str'] = 0;
        }
        return json_encode($this->lc_regs);
    }

	public function fn_validaCuadreFormasPago($lc_datos) {
        $lc_sql = "EXEC [facturacion].[USP_validaCuadreFormasPago] '$lc_datos[1]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['mensaje'] = $row["mensaje"];
                $this->lc_regs['diferencia'] = $this->ifNum($row["diferencia"]);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_esperaRespuestaPinpadMultired($lc_datos) {
        $lc_sql = "EXEC [facturacion].[USP_esperaRespuestaRequerimientoAutorizacionPinpadMultiredes] '$lc_datos[0]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['existe'] = $row["existe"];
                if ($row["existe"] == 1) {
                    $this->lc_regs['rsaut_respuesta'] = $row["rsaut_respuesta"];
                    $this->lc_regs['cres_codigo'] = $row["cres_codigo"];
                    $this->lc_regs['fpf_id'] = $row["fpf_id"];
                    $this->lc_regs['rsaut_id'] = $row["rsaut_id"];
                    $this->lc_regs['errorTrama'] = utf8_encode($row["errorTrama"]);
                    $this->lc_regs['codigoAutorizador'] = $row["codigoAutorizador"];
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function fn_impresionDinamicaFaltanteCxC($lc_datos) {
        $lc_query = "EXEC [facturacion].[USP_impresiondinamica_faltanteCxC] '$lc_datos[0]'";
        return $this->fn_ejecutarquery($lc_query);
    }

    public function fn_url_cuentas($lc_datos) {
        $lc_sql = "[facturacion].[UPS_ESTADO_ORDEN_Y_FACTURA] '$lc_datos[0]', '$lc_datos[1]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['direccion'] = $row["direccion"];
                $this->lc_regs['url_cuenta'] = $row["url_cuenta"];
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_lista_descuentos($lc_datos) {
        $lc_sql = "EXEC [facturacion].[DESCUENTOS] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("iddescuentos" => $row["IDDescuentos"],
                    "apld_descripcion" => $row["dsct_descripcion"],
                    "tipoDescuento" => $row["tpd_descripcion"]);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_agrega_descuentos($lc_datos) {
        $lc_sql = "EXEC [facturacion].[DESCUENTOS_facturaManual] '$lc_datos[4]', '$lc_datos[2]', '$lc_datos[1]', '$lc_datos[0]', '$lc_datos[3]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "desc_descripcion" => utf8_encode($row["plu_descripcion"]),
                    "desc_valor" => $row["dtfac_total"],
                    "desc_estado" => $row["estado"],
                    "desc_mensaje" => utf8_encode($row["mensaje"]),
                    "desc_descuento" => $row["desc_producto"],
                    "desc_plu" => $row["dtfac_precio_unitario"],
                    "desc_cantidad" => $row['dtfac_cantidad'],
                    "totalizado" => $row['totalizado'],
                    "porcentaje" => $row['porcentaje']
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_validarUsuarioDescuentos($lc_datos) {
        $lc_sql = "EXEC [facturacion].[TRN_validar_usuario_administrador] '$lc_datos[0]', '$lc_datos[1]', ' '";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['usr_id'] = $row["usr_id"];
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_cargarAccesosPerfil($lc_datos) {
        $lc_sql = "EXEC [config].[USP_verificanivelacceso] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("acc_id" => $row['acc_id'],
                    "acc_descripcion" => trim($row['acc_descripcion']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_eliminar_descuentos($lc_datos) {
        $lc_sql = "EXEC [facturacion].[DESCUENTOS_eliminarDescuentosFactura] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("elim_estado" => $row["estado"],
                    "elim_mensaje" => $row["mensaje"],
                    "elim_valor" => $row["valor"]);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_descuentosDiscrecionales($lc_datos) {
        $lc_sql = "EXEC [Descuentos].[DESCUENTOS_listaDescuentosDiscrecionales] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("plu_id" => $row["plu_id"],
                    "descripcion" => utf8_encode($row["descripcion"]));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_guardarDescuentosDiscrecionales($lc_datos) {
        $lc_sql = "EXEC [Descuentos].[IA_DescuentosDiscrecionales] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("discrecional_estado" => $row["estado"],
                    "discrecional_mensaje" => utf8_encode($row["mensaje"]),
                    "discrecional_valor" => $row["valor"]);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_validaCuponAX($lc_datos) {
        $lc_sql = "select * from  config.fn_verificaVoucherAerolineas ( '$lc_datos[0]' , '$lc_datos[1]' )";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("Monto" => $row["Monto"],
                    "Descripcion" => json_encode($row["Descripcion"]),
                    "Cliente" => json_encode($row["Cliente"])
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_listaPorcentajesDiscrecionales($lc_datos) {
        $lc_sql = "EXEC [Descuentos].[DESCUENTOS_listaDescuentosDiscrecionales] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("valorDiscrecional" => $row["valorDiscrecional"]);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_detallesDescuentosDiscrecionales($lc_datos) {
        $lc_sql = "EXEC [Descuentos].[DESCUENTOS_detallesDescuentosDiscrecionales] '$lc_datos[0]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    //"Cod_Factura" => utf8_encode($row['Cod_Factura']),
                    //"btn_cancela_pago" => $row['btn_cancela_pago'],
                    "cdn_tipoimpuesto" => trim($row['cdn_tipoimpuesto']),
                    "rst_tipoServicio" => trim($row['rst_tipo_servicio']),
                    "cfac_id" => trim($row['cfac_id']),
                    "usr_id" => trim($row['usr_id']),
                    "est_id" => $row['est_id'],
                    "cfac_fechacreacion" => $row['cfac_fechacreacion'],
                    "plu_id" => trim($row['plu_id']),
                    "plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
                    "dtfac_cantidad" => $row['dtfac_cantidad'],
                    "dtfac_precio_unitario" => $row['dtfac_precio_unitario'],
                    "dtfac_iva" => $row['dtfac_iva'],
                    //"servicio" => $row['servicio'],
                    //"dtfac_total" => $row['dtfac_total'],
                    "totalizado" => $row['totalizado'],
                    "plu_impuesto" => trim($row['plu_impuesto']),
                    "cfac_descuento_empresa" => $row['cfac_descuento_empresa'],
                    //devueleve el valor si el producto aplica descuento
                    "descuento" => $row['desc_producto'],
                    "desc_valorFijo" => $row['valorFijo'],
                    "desc_porcentaje" => $row['porcentaje'],
                    "porcentajeDiscrecional" => $row['porcentajeDiscrecional']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_buscaPagoCredito($lc_datos) {
        $lc_sql = "EXEC [facturacion].[USP_validar_PagoCreditos] '$lc_datos[0]', $lc_datos[1]";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("valida" => $this->ifNum($row["valida"]), "documento" => $row["documento"]);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function fn_cierraFacturaConCredito($lc_datos) {
        $lc_sql = "EXEC [facturacion].[IAE_cierraFacturaCredito] '$lc_datos[0]','$lc_datos[2]','$lc_datos[3]', $lc_datos[1]";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $this->lc_regs['str'] = 1;
        } else {
            $this->lc_regs['str'] = 0;
        }
        return json_encode($this->lc_regs);
    }

    public function fn_validarPoliticaDescuentosSeguridad($lc_datos) {
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_Descuentos_Seguridad_$lc_datos[0]] ($lc_datos[1],$lc_datos[2])";
        $resultadoSQL = $this->fn_ejecutarquery($lc_sql);
        $this->lc_regs['str'] = 0;
        if (!$resultadoSQL) {
            return $this->lc_regs;
        }
        $datosResultado = $this->fn_leerarreglo();

        $this->lc_regs['str'] = $datosResultado[0];
        return $this->lc_regs;
    }
    
    public function obtener_agregadores($id_cadena) {
        $lc_sql = "EXECUTE [config].[USP_FormasPago_Agregadores] $id_cadena";
        
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "id_agregador" => $row["id_agregador"],
                    "agregador" => utf8_decode($row["agregador"]),
                    "imagen" => $row["imagen"],
                    "IDTipoFormaPago" => ($row["IDTipoFormaPago"]),
                    "tipo_forma_pago" => utf8_decode($row["tipo_forma_pago"]),
                    "requiere_autorizacion" => ($row["requiere_autorizacion"]),
                    "tipo" => utf8_decode($row["tipo"])
                );
            }
            
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        
        return json_encode($this->lc_regs);
    }
    
    public function logProcesosFidelizacion($descripcion, $accion, $IDRestaurante, $IDCadena, $IDUsuario, $trama) {
        $lc_sql = "EXEC fidelizacion.I_Auditorias '$descripcion', '$accion', $IDRestaurante, $IDCadena, '$IDUsuario', '$trama'";
        $this->fn_ejecutarquery($lc_sql);
    }

    public function autoconsumo($cfac_id, $rst_id, $secuencial, $documentoCliente, $nombreCliente, $puntosCanjeados, $marketingCost, $storeCost) {
        $puntosCanjeados = (int) $puntosCanjeados;
        $marketingCost = number_format((float) $marketingCost, 2, '.', '');
        $storeCost = number_format((float) $storeCost, 2, '.', '');
        
        $lc_sql = "EXEC [fidelizacion].[Autoconsumo] 
                    '$cfac_id', '$rst_id', '$secuencial', '$documentoCliente', 
                    '$nombreCliente', $puntosCanjeados, $marketingCost, $storeCost";

        if ($this->fn_ejecutarquery($lc_sql)) {
            // Recorrer todos los registros que devuelva el SP (uno o varios resultsets)
            while ($row = $this->fn_leerarreglo()) {
                // Solo procesar si es el resultset correcto
                if (isset($row["respuestaFacturacion"]) && isset($row["estadoFacturacion"]) && isset($row["nuevocfac_id"])) {
                    $this->lc_regs[] = array(
                        "respuestaFacturacion" => $row["respuestaFacturacion"],
                        "estadoFacturacion"    => $row["estadoFacturacion"],
                        "nuevocfac_id"         => $row["nuevocfac_id"]
                    );
                }
            }

            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
    }



    function fn_configuracionTurnero($idCadena, $idRestaurante) {

        $this->lc_regs['Respueseta'] = array(
                                    "estado"   => 0,
                                    "mensaje"  => "error",
                                    "url"      => '',
                                    "activo"   => '');

        $lc_sql = "EXEC [config].[configuracionTurnero] $idCadena, $idRestaurante";
        $this->fn_ejecutarquery($lc_sql);
        if (($this->fn_numregistro()) < 1) {
            $this->lc_regs['Respueseta']["mensaje"] = "La configuracion no es correcta";
            return $this->lc_regs;
        }
        $row = $this->fn_leerarreglo();
        $this->lc_regs['Respueseta']["estado"] = 1;
        $this->lc_regs['Respueseta']["mensaje"] = "OK";
        $this->lc_regs['Respueseta']["url"] = $row["url"] . '/transaction';
        $this->lc_regs['Respueseta']["activo"] = $row["activo"];
        return $this->lc_regs;
    }
    
    function fn_obtieneNumeroOrden($IDCabeceraOrdenPedido){
        $this->lc_regs = [];
        $lc_sql = "SELECT cfac_id as orden from Cabecera_Factura where IDCabeceraOrdenPedido = '$IDCabeceraOrdenPedido' ";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                if ($row["orden"] !== null){
                    $this->lc_regs[] = array(
                        "orden" => $row["orden"]
                    );
                }
             }
        }    
        return json_encode($this->lc_regs);
    }

    function fn_turneroHabilitadoPorEstacion($idEstacion){
        $lc_sql = "EXEC [config].[configuracionTurneroPorEstacion] '$idEstacion'";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();
        $this->lc_regs['Respuesta']['habilitado'] = $row["habilitado"];
        return $this->lc_regs;

    }

    function fn_consultarPedidoApp($codigo_app){
        $lc_sql="SELECT  identificacion_cliente,nombres_cliente  FROM Cabecera_App WHERE codigo_app = '$codigo_app'";
        if($this->fn_ejecutarquery($lc_sql)) 
            { 
               
                while($row = $this->fn_leerarreglo()) 
                {		
                    $this->lc_regs['identificacion_cliente'] = $row['identificacion_cliente'];
                    $this->lc_regs['nombres_cliente'] = utf8_encode($row['nombres_cliente']);		
                }
            }	
        $this->lc_regs['str'] = $this->fn_numregistro();  
        return json_encode($this->lc_regs);	
    }

    public function fn_impresionDesasignacionMotorizado($id_motorizado, $id_periodo) {
        $lc_sql = "EXEC facturacion.App_impresion_dinamica_desasignacion_motorizado '$id_motorizado', '', '$id_periodo'";
        if($this->fn_ejecutarquery($lc_sql)){
            $row = $this->fn_leerarreglo();
            return $row["html"];
        }
    }

    public function fn_ValidarFacturaTarjeta($cabecera) {
        $lc_sql = "EXEC [pedido].[USP_ValidarFacturaTarjeta] '$cabecera' ";
        if($this->fn_ejecutarquery($lc_sql)){
            $row = $this->fn_leerarreglo();
            if($row['TipoPago']=='TARJETA'){
                unset($row,$lc_sql,$cabecera);
                return json_encode(array('estado'=>1));
            }else {
                unset($row,$lc_sql,$cabecera);
                return json_encode(array('estado'=>0));;
            }            
        }
    }
    
    function condicionFacturacionOrdenPedido( $IDCabeceraOrdenPedido )
    {
        $this->lc_regs = array();

        $lc_sql = "EXEC	[pedido].[condicionFacturacionOrdenPedido] '$IDCabeceraOrdenPedido';";        

        if( $this->fn_ejecutarquery( $lc_sql ) ) 
        {
            $row = $this->fn_leerarreglo();

            $this->lc_regs["condicionFOP"] =  array(    "error"                 => $this->ifNum($row["error"]),
                                                        "errorDescripcion"      => utf8_encode( $row["errorDescripcion"] ),
                                                        "condicion"             => $this->ifNum($row["condicion"]),
                                                        "condicionDescripcion"  => utf8_encode( $row["condicionDescripcion"] ),
                                                        "promesaPendiente"      => $this->ifNum($row["promesaPendiente"]),
                                                        "IDFormapagoPromPend"   => $row["IDFormapagoPromPend"],
                                                        "montoPagadoPromPend"   => $this->ifNum($row["montoPagadoPromPend"]),
                                                        "monto_total_propina" => $this->ifNum($row["monto_total_propina"]),
                                                    );
                                                    
            $this->lc_regs["str"] = $this->fn_numregistro();
        }
        else 
        {
            $this->lc_regs["str"] = 0;
        }

        return json_encode( $this->lc_regs );
    }

    function ordenPedidOAgregador($id_cabecera_pedido, $id_agregador) {
        $lc_sql = "EXEC [pedido].[IAE_OrdenPedidoAgregadores] '$id_cabecera_pedido', '$id_agregador'";

        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["respuesta"] = $row["respuesta"];

            }
        }

        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function aceptaBeneficioClienteApi($cadena, $restaurante, $dop_id, $uid)
    {
        $result = '';
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($cadena, 'DATOS PERSONALES', 'URL') AS url";

        try {
            $this->fn_ejecutarquery($lc_sql);

            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $urlServicioWeb = $row['url'];

                $token = $this->validarTokenApiCliente();

                $DateTime = new DateTime();
                $fechaActualizacionCliente = $DateTime->format('Y-m-d\TH:i:s.u\Z');

                $plu_id = $this->agregarBeneficioCliente( $cadena );
                $plu_id = $plu_id["producto"];

                $sql_restaurantes = "SELECT * FROM RESTAURANTE WHERE rst_id = '".$restaurante."';";
                $this->fn_ejecutarquery($sql_restaurantes);
                $restaurantes = $this->fn_leerarreglo();

                $cod_tienda = $restaurantes["rst_cod_tienda"];
                $dir_tienda = $restaurantes["rst_direccion"];
                $rst_descripcion = $restaurantes["rst_descripcion"];

                $sql_cadenas = "SELECT * FROM cadena WHERE cdn_id = '".$cadena."';";
                $this->fn_ejecutarquery($sql_cadenas);
                $cadenas = $this->fn_leerarreglo();

                $sql_producto = "SELECT * FROM Plus where plu_id = '".$plu_id."';";
                $this->fn_ejecutarquery($sql_producto);
                $producto = $this->fn_leerarreglo();

                if (isset($uid) && ($uid !== '')) {
                    if ($dop_id != ''){
                        $aplicarBeneficio = array(
                            "uid_cliente" => $uid,
                            "cdn_id" => $cadena,
                            "rst_id" => $restaurante,
                            "nombreCadena" => utf8_encode($cadenas["cdn_descripcion"]),
                            "local" => $cod_tienda.' - '.$rst_descripcion,
                            "direccionRestaurante" => utf8_encode($dir_tienda),
                            "pluId" => $plu_id,
                            "nombrePlus" => $producto["plu_descripcion"],
                            "fechaCanje" => $fechaActualizacionCliente
                        );

                        $header_array = array(
                            'Content-Type: application/json; charset=UTF-8',
                            'Accept: application/json',
                            'Authorization: Bearer ' . $token["token"]
                        );
                        
                        $url = $urlServicioWeb . '/api/client/acepta_beneficio';
                        $maxIntentos = 3;
                        for( $intento = 1; $intento <= $maxIntentos; $intento++ ){ 
                            $ch = curl_init($url);
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //esto hay que cambiarlo a lo que tengamos SSL 
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($aplicarBeneficio));
                            $result = curl_exec($ch);

                            if($result !== false){
                                $result = json_decode($result, true);
                                break; 
                            }

                            if($intento < $maxIntentos){
                                sleep(1); 
                            }
                        }

                        if ($result === false) {
                            $result = 'cURL Error: ' . curl_error($ch);
                        }

                        curl_close($ch);
                    }
                } else {
                    $result = ''; 
                }
            }
        } catch (Exception $e) {
            return $e;
        }

        return json_encode($result);
    }

    function agregarBeneficioCliente( $cadena )
    {
        $this->lc_regs = [];
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($cadena, 'DATOS PERSONALES', 'PRODUCTO') AS producto";
        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( 
                    "producto" => $row['producto']
                );
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function validarTokenApiCliente() {
        $arrayToken = array();
        $mensaje = '';
        $token = '';
        $path_json = '';

        $fileName = 'tokenApiMdmCliente.json';
        $folderName = 'tokens';
        $permisos = '0777';
        $base_dir = realpath(__DIR__  . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $filePath = $base_dir . $folderName . DIRECTORY_SEPARATOR . $fileName;
        $filePathToken = $base_dir . $folderName;
        //Si no existe la carpeta tokens la crea
        if (!file_exists($filePathToken)) {
            mkdir($filePathToken, $permisos, true);
        }
        if (!file_exists($filePath)) {
          $tokenApiMDMCliente = $this->generarTokenApiMDMCliente();
          file_put_contents($filePath,$tokenApiMDMCliente);
        }

        $configContents =  file_get_contents($filePath);

        if ($configContents !== false) {
            $config = json_decode($configContents, true);

            if (isset($config['token'])) {
                $token = $config['token'];
                $tokenParts = explode('.', $token);
                $tokenPayload = base64_decode($tokenParts[1]);
                $payload = json_decode($tokenPayload, true);

                $tokenExpirationTime = $payload['exp'];
                $currentTimestamp = time();

                if ($currentTimestamp <= $tokenExpirationTime) {
                    $mensaje = utf8_decode("El token API MDM CLIENTE es válido y no ha caducado.");
                } else {
                    $tokenData = $this->generarTokenApiMDMCliente();

                    if (isset($tokenData["token"])) {
                        $token = $tokenData["token"];

                        $jsonDatos = json_encode($tokenData);
                       file_put_contents($filePath, $jsonDatos);
                    } else {
                        $mensaje = 'No se pudo generar un nuevo token.';
                    }
                }
            } else {
                $tokenData = $this->generarTokenApiMDMCliente();

                if (isset($tokenData["token"])) {
                    $token = $tokenData["token"];

                    $jsonDatos = json_encode($tokenData);
                    file_put_contents($filePath, $jsonDatos);

                } else {
                    $mensaje = 'No se pudo generar un nuevo token.';
                }
            }
        } else {
            $mensaje = "No se pudo leer el archivo JSON.";
        }

        $arrayToken = array('token' => $token, 'mensaje' => utf8_encode($mensaje));
        return $arrayToken;

    }

    function generarTokenApiMDMCliente() {
        $idRestaurante     = $_SESSION['rstId'];
        $idCadena          = $_SESSION['cadenaId'];

        $sql_url_base = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'DATOS PERSONALES', 'URL') AS url;";
        $this->fn_ejecutarquery($sql_url_base);
        $arreglo_url_base = $this->fn_leerarreglo();
        $urlAPIMDM = $arreglo_url_base['url'];

        $sql_idCliente = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'DATOS PERSONALES', 'CLIENTID') AS idApi;";
        $this->fn_ejecutarquery($sql_idCliente);
        $arreglo_idCliente = $this->fn_leerarreglo();
        $api_clientID = $arreglo_idCliente['idApi'];

        $sql_secretCliente = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'DATOS PERSONALES', 'CLIENTSECRET') AS secretApi;";
        $this->fn_ejecutarquery($sql_secretCliente);
        $arreglo_secretCliente  = $this->fn_leerarreglo();
        $api_secretCliente = $arreglo_secretCliente['secretApi'];

        $header_array = array(
            'Content-Type: application/json; charset=UTF-8',
            'Accept: application/json'
        );

        $credentials = array(
            'clientID' => $api_clientID,
            'clientSecret' => $api_secretCliente
        );
        $url = $urlAPIMDM . '/api/Auth/token';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($credentials));

        $result = curl_exec($ch);

        if ($result === false) {
            $result = 'cURL Error: ' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($result, true);

    }
    function VoucherCanceladoNoAprobado($rsaut_id,$est_id,$rst,$IDUserPos) {
        $lc_sql = "[impresion].[usp_impresion_VoucherCanceladoNoAprobado] '$rsaut_id','$est_id','$rst','$IDUserPos'";
        $this->fn_ejecutarquery($lc_sql);
    }

    function getPayloadQualtrics($fact,$rst, $IDUserPos) {
        $lc_sql = "[facturacion].[USP_ProcesoQualtrics] '1','$fact','$rst', '$IDUserPos'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["payload"] = $row["payload"];
            }
        }
        return $this->lc_regs;
    }

    function auditoriadQualtrics($fact,$rst, $IDUserPos, $dataResponse) {
        $lc_sql = "[facturacion].[USP_ProcesoQualtrics] '2','$fact','$rst', '$IDUserPos', '$dataResponse'";
        $this->fn_ejecutarquery($lc_sql);
    }

}