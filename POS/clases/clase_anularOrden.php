<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Cristhian Castro ///////////////////////////////////////////////////////////////
///////DESCRIPCION: /////////////////////////////////////////////////////////////////////////////////////
///////TABLAS INVOLUCRADAS: Menu_Agrupacion, ////////////////////////////////////////////////////////////
////////////////Menu_Agrupacionproducto//////////////////////////////////////////////////////////////////
////////////////Detalle_Orden_Pedido/////////////////////////////////////////////////////////////////////
///////////////////Plus, Precio_Plu, Mesas///////////////////////////////////////////////////////////////
///////FECHA CREACION: 24-06-2014////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 08-05-2014/////////////////////////////////////////////////////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro///////////////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Documentación y validación en Internet Explorer 9+ /////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 08-07-2014/////////////////////////////////////////////////////////////
///////USUARIO QUE MODIFICO:  Jose Fernandez/////////////////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Modificacion del case generarNotaCredito ///////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 09-04-2015/////////////////////////////////////////////////////////////
///////USUARIO QUE MODIFICO:  Jorge Tinoco //////////////////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Proceso de anulación formas de pago ////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 05/05/2015 ////////////////////////////////////////////////////////////
///////USUARIO QUE MODIFICO: Jorge Tinoco ///////////////////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Muestra ordenes de pedido en pantalla de cuentas abiertas //////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 22/05/2015 ////////////////////////////////////////////////////////////
///////USUARIO QUE MODIFICO: Christian Pinto ////////////////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Cambio consulta por procedimiento usp_ven_cuentasAbiertas, nuevos //////
//////////////////////////////// campos cajero y estado de factura //////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////

class menuPedido extends sql {

    function _construct() {
        parent ::_construct();
    }

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {

            case 'payphoneObtieneTransaccionID':
                $lc_sql = "exec [facturacion].[USP_payphoneObtieneTransaccionID]'$lc_datos[0]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("transaccionID" => $row['transaccionID']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case 'esFacturaPlanAmigos':
                $lc_sql = "EXEC [fidelizacion].[USP_facturaConPuntos] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("puntos" => $row['puntos']);
                    }
                }
                return json_encode($this->lc_regs);

            case 'impresion_notacredito':
                $lc_query = "EXEC [facturacion].[USP_impresiondinamica_NotaCredito] '$lc_datos[0]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'impresion_factura':
                $lc_sql = "EXEC [facturacion].[USP_impresiondinamica_factura] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("html" => $row['html'],
                            "html2" => $row['html2'],
                            "htmlf" => $row['htmlf']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'impresion_voucher':
                $lc_sql = "EXEC [facturacion].[USP_impresiondinamica_Voucher] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("html" => ($row['html']),
                            "htmla" => ($row['htmla']),
                            "htmlb" => ($row['htmlb']),
                            "htmlf" => ($row['htmlf']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'reimpresion_factura':
                $lc_query = "EXEC [facturacion].[IAE_TRN_reimpresion_documentos] '$lc_datos[0]','$lc_datos[1]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'cargarConfiguracionRestaurante':
                $lc_sql = "EXEC facturacion.TRN_cargar_configuracion_restaurante ".$lc_datos[0].", '".$lc_datos[1]."', '".$lc_datos[2]."', ".$lc_datos[3]."";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "tpsrv_descripcion" => $row['tpsrv_descripcion'],
                            "aplica_nc_sinconsumidor" => $row['aplica_nc_sinconsumidor'],
                            "servicioDomicilio" => $row['servicioDomicilio'],
                            "servicioPickup" => $row['servicioPickup']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'cargarAccesosPerfil':
                $lc_sql = "EXEC facturacion.TRN_cargar_accesos_permisos_pantalla_usuario '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("acc_id" => $row['acc_id'],
                            "acc_descripcion" => $row['acc_descripcion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'cargarTipoEnvioFacturaFormaPago':
                $lc_sql = "EXEC facturacion.TRN_cargar_tipo_envio_factura_pago '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fpf_swt" => $row['fpf_swt'],
                            "tpenv_descripcion" => $row['tpenv_descripcion'],
                            "descripcion" => $row['descripcion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'verifica_numautorizacion':
                $lc_sql = "EXEC facturacion.TRN_verificar_tipo_facturacion '" . $lc_datos[0] . "'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'verifica_empresa':
                $lc_sql = "EXEC facturacion.TRN_cargar_informacion_empresa '" . $lc_datos[0] . "'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'claveAcceso':
                $lc_sql = "EXEC facturacion.USP_ClaveAcceso '" . $lc_datos[0] . "', '" . $lc_datos[1] . "'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'grabaCanalImpresionAnulacionPreimpresa':
                $lc_sql = "EXEC facturacion.TRN_impresion_anulacionPreImpresa '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['Respuesta'] = 1;
                } else {
                    $this->lc_regs['Respuesta'] = 0;
                }
                return json_encode($this->lc_regs);

            case 'impresionFactura':
                $lc_sql = "EXEC facturacion.TRN_impresion_factura '" . $lc_datos[0] . "','" . $lc_datos[1] . "'," . $lc_datos[2];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['Respuesta'] = 1;
                } else {
                    $this->lc_regs['Respuesta'] = 0;
                }
                return json_encode($this->lc_regs);

            case 'impresionNotaCredito':
                $lc_sql = "EXEC facturacion.TRN_imprimir_nota_credito '" . $lc_datos[0] . "', '" . $lc_datos[1] . "', " . $lc_datos[2];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['Respuesta'] = 1;
                } else {
                    $this->lc_regs['Respuesta'] = 0;
                }
                return json_encode($this->lc_regs);

            case 'cabecera_factura':
                $lc_sql = "EXEC facturacion.TRN_impresion_cabecera_factura '" . $lc_datos[0] . "'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'visorCabeceraFactura':
                $lc_sql = "EXEC facturacion.TRN_voucher_cabecera_factura '" . $lc_datos[0] . "'";
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
                break;
            case 'visorCabeceraNotaCredito':
                $lc_sql = "EXEC facturacion.TRN_impresion_cabecera_nota_credito '" . $lc_datos[0] . "'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
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
                break;
            case 'visorDetalleFactura':
                $lc_sql = "EXEC facturacion.TRN_voucher_detalle_factura '" . $lc_datos[0] . "'";
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

            case 'visorDetalleNotaCredito':
                $lc_sql = "EXEC facturacion.TRN_impresion_detalle_nota_credito '" . $lc_datos[0] . "'";
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

            case 'totalDetalleFactura':
                $lc_sql = "EXEC facturacion.TRN_cargar_totales_factura '" . $lc_datos[0] . "'";
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

            case 'totalDetalleNotaCredito':
                $lc_sql = "EXEC facturacion.TRN_cargar_totales_nota_credito '" . $lc_datos[0] . "'";
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

            case 'formasPagoDetalleFactura':
                $lc_sql = "EXEC facturacion.TRN_cargar_formas_pago_factura '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fmp_descripcion" => utf8_encode(trim($row['fmp_descripcion'])),
                            "cfac_total" => number_format(($row['cfac_total']), 2, ".", ""));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'formasPagoDetalleNotaCredito':
                $lc_sql = "EXEC facturacion.TRN_cargar_formas_pago_nota_credito '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fmp_descripcion" => utf8_encode(trim($row['fmp_descripcion'])),
                            "cfac_total" => number_format(($row['cfac_total']), 2, ".", ""));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'formas_Pago':
                $lc_sql = "EXEC facturacion.TRN_cargar_formas_pago_nota_credito '" . $lc_datos[0] . "'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'detalle_factura_impresion':
                $lc_sql = "EXEC facturacion.TRN_impresion_detalle_anulacion '" . $lc_datos[0] . "'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'totales_factura_anulacion':
                $lc_sql = "EXEC facturacion.TRN_cargar_totales_Anulacion '" . $lc_datos[0] . "'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'obtiene_autorizacion_anulacion':
                $lc_sql = "EXEC facturacion.TRN_cargar_autorizacion_anulacion '" . $lc_datos[0] . "'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'obtiene_claveAcceso_anulacion':
                $lc_sql = "EXEC facturacion.TRN_cargar_clave_acceso '" . $lc_datos[0] . "'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'grabacanalmovimientoImpresionAnulacionElectronica':
                $idEstacion = $_SESSION['estacionId'];
                $lc_sql = "EXEC facturacion.TRN_impresion_anulacion $lc_datos[3],'$lc_datos[2]','$lc_datos[0]','$lc_datos[1]', '$idEstacion'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case "cuentasAbiertas":
                set_time_limit(300);
                $lc_sql = "EXEC facturacion.TRN_cargar_cuentas_abiertas " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cfac_id" => trim($row['cfac_id']),
                            "mesa_descripcion" => $row['mesa_descripcion'],
                            "odp_id" => trim($row['odp_id']),
                            "cfac_subtotal" => ROUND($row['cfac_subtotal'], 2),
                            "cfac_total" => ROUND($row['cfac_total'], 2),
                            "est_id" => $row['est_id'],
                            "est_nombre" => $row['est_nombre'],
                            "usuario" => $row['usuario'],
                            "descripcionEstado" => htmlentities($row['descripcionEstado']),
                            "odp_observacion" => utf8_encode($row['odp_observacion']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                
                return json_encode($this->lc_regs);

            case "consultarMesaOrden":
                $lc_sql = "EXEC facturacion.recuperaOrdenYFacturaTransacciones '" . $lc_datos[0] . "', 1, 2, '" . $lc_datos[1] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("direccion" => $row['direccion'], "url" => $row['url']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "retomarCuentaAbierta":
                $lc_sql = "EXEC facturacion.TRN_cargar_mesa_cuenta_abierta '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("odp_id" => trim($row['odp_id']),
                            "mesa_id" => $row['mesa_id'],
                            "dop_cuenta" => $row['dop_cuenta']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "esMiMesaEnFacturacion":
                $lc_sql = "EXEC config.retomarSiesMiMesaEnFacturacionUordenPedido '" . $lc_datos[0] . "','" . $lc_datos[1] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "retomar" => $row['retomar']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "busquedaCuentasAbiertas":
                $lc_sql = "EXEC facturacion.TRN_buscar_cuentas_abiertas " . $lc_datos[0] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "', '" . $lc_datos[1] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cfac_id" => trim($row['cfac_id']),
                            "mesa_descripcion" => $row['mesa_descripcion'],
                            "descripcionEstado" => htmlentities($row['descripcionEstado']),
                            "usuario" => $row['usuario'],
                            "odp_id" => trim($row['odp_id']),
                            "cfac_subtotal" => ROUND($row['cfac_subtotal'], 2),
                            "cfac_total" => ROUND($row['cfac_total'], 2),
                            "est_id" => $row['est_id'],
                            "est_nombre" => $row['est_nombre'],
                            "odp_observacion" => htmlentities($row['odp_observacion']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "logErrores":
                $lc_sql = "EXEC facturacion.TRN_cargar_log_errores " . $lc_datos[0];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("lg_clave_acceso" => trim($row['lg_clave_acceso']),
                            "lg_estado" => $row['lg_estado'],
                            "lg_mensaje" => trim($row['lg_mensaje']),
                            "lg_fecha" => trim($row['lg_fecha']),
                            "cfac_id" => $row['cfac_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'ws_logAutorizacion':
                $lc_sql = "declare @cfac varchar(20)
					set @cfac=(select ncre_id from Cabecera_Nota_Credito where ncre_claveAcceso='$lc_datos[9]')
					insert into log_facturacion_electronica values('$lc_datos[9]','$lc_datos[10]','$lc_datos[11]',$lc_datos[12],@cfac,getdate())";
                    $result = $this->fn_ejecutarquery($lc_sql);
                    if ($result){ return true; }else{ return false; };

            case 'ws_logRecepcion':
                $lc_sql = "declare @cfac varchar(20)
					set @cfac=(select ncre_id from Cabecera_Nota_Credito where ncre_claveAcceso='$lc_datos[0]')
					insert into log_facturacion_electronica values('$lc_datos[0]','$lc_datos[1]','$lc_datos[2]',$lc_datos[3],@cfac,getdate())";
                    $result = $this->fn_ejecutarquery($lc_sql);
                    if ($result){ return true; }else{ return false; };

            case "cuentasCerradas":
                $lc_sql = "EXEC facturacion.TRN_cargar_cuentas_cerradas '" . $lc_datos[0] . "', '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "cfac_id" => trim($row['cfac_id']),
                            "mesa_descripcion" => $row['mesa_descripcion'],
                            "odp_id" => trim($row['odp_id']),
                            "cfac_subtotal" => ROUND($row['cfac_subtotal'], 2),
                            "cfac_total" => ROUND($row['cfac_total'], 2),
                            "est_id" => $row['est_id'],
                            "est_nombre" => $row['est_nombre'],
                            "PlanFidelizacionAutoConsumo" => $row['PlanFidelizacionAutoConsumo'],
                            "ncre_id" => trim($row['ncre_id']),
                            "usuario" => trim($row['usuario']),
                            "cfac_observacion" => htmlentities($row['cfac_observacion']),
                            "descripcionEstado" => htmlentities($row['descripcionEstado']),
                            "descripcionEstatus" => htmlentities($row['descripcionEstatus']),
                            "documento_con_datos" => ($row['documento_con_datos']),
                            "impresion" => $row['impresion'],
                            "ncdre_ant" => $row['ncdre_ant']
                        );
                    }
                }

                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "txTarjetas":
                $lc_sql = "EXEC [facturacion].[TRN_cargar_transacciones_tarjetas] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]' ";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDCanalMovimiento" => utf8_encode($row['IDCanalMovimiento']),
                            "rsaut_id" => utf8_encode($row['rsaut_id']),
                            "tipo" => utf8_encode($row['tipo']),
                            "fpf_total_pagar" => $row['fpf_total_pagar'],
                            "fmp_descripcion" => utf8_encode($row['fmp_descripcion']),
                            "anulado" => $row['anulado'],
                            "fecha" => utf8_encode($row['fecha']),
                            "hora" => utf8_encode($row['hora']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "busquedaCuentasCerradas":
                $lc_sql = "EXEC facturacion.TRN_buscar_cuentas_cerradas " . $lc_datos[0] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "', '" . $lc_datos[1] . "'";
                
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "cfac_id" => trim($row['cfac_id']),
                            "mesa_descripcion" => $row['mesa_descripcion'],
                            "odp_id" => trim($row['odp_id']),
                            "cfac_subtotal" => ROUND($row['cfac_subtotal'], 2),
                            "cfac_total" => ROUND($row['cfac_total'], 2),
                            "est_id" => $row['est_id'],
                            "est_nombre" => $row['est_nombre'],
                            "ncre_id" => trim($row['ncre_id']),
                            "usuario" => trim($row['usuario']),
                            "cfac_observacion" => htmlentities($row['cfac_observacion']),
                            "descripcionEstado" => htmlentities($row['descripcionEstado']),
                            "documento_con_datos" => $row['documento_con_datos'],
                            "impresion" => $row['impresion']
                        );
                    }
                }
                
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "consultar_formasPagoFactura":
                $lc_sql = "	SELECT fpf.fpf_id, fpf_codigo, fp.fmp_descripcion, fpf.fpf_swt
							FROM Formapago_Factura AS fpf 
							INNER JOIN Formapago AS fp ON fp.fmp_id = fpf.fmp_id
							WHERE fpf.cfac_id = '" . $lc_datos[0] . "' AND fp.fmp_descripcion not like '%EFECTIVO%'
							ORDER BY fp.fmp_descripcion";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fpf_id" => trim($row['fpf_id']),
                            "fpf_codigo" => $row['fpf_codigo'],
                            "fmp_descripcion" => trim($row['fmp_descripcion']),
                            "fpf_swt" => $row['fpf_swt']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "consultar_transaccionformaPagoFactura":
                $lc_sql = "	SELECT RANK() OVER(ORDER BY sra.rsaut_id DESC) btd_rank, sra.rsaut_id, CONVERT(VARCHAR, sra.rsaut_fecha, 103) AS fecha, fpf.fpf_total_pagar AS total, CAST(sra.rsaut_fecha AS TIME(0)) AS hora, sra.rsaut_respuesta, sra.rsaut_hora_autorizacion, sra.rsaut_numero_autorizacion, sra.std_id
							FROM SWT_Respuesta_Autorizacion AS sra
							INNER JOIN Formapago_Factura AS fpf ON fpf.fpf_id = sra.fpf_id
							WHERE sra.rsaut_movimiento = '" . $lc_datos[0] . "' AND sra.fpf_id = '" . $lc_datos[1] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("btd_rank" => trim($row['btd_rank']),
                            "rsaut_id" => trim($row['rsaut_id']),
                            "fecha" => $row['fecha'],
                            "hora" => $row['hora'],
                            "total" => $row['total'],
                            "rsaut_respuesta" => trim($row['rsaut_respuesta']),
                            "rsaut_hora_autorizacion" => trim($row['rsaut_hora_autorizacion']),
                            "rsaut_numero_autorizacion" => trim($row['rsaut_numero_autorizacion']),
                            "std_id" => $row['std_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "impresionVaucher":
                $lc_sql = " EXEC imp_vaucher " . $lc_datos[0] . ", " . $lc_datos[2] . ", '" . $lc_datos[1] . "', " . $lc_datos[3];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Confirmar" => $row['Confirmar']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "motivoAnulacion":
                $lc_sql = "EXEC facturacion.TRN_cargar_motivo_anulacion " . $lc_datos[0];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("mtv_id" => trim($row['mtv_id']),
                            "mtv_descripcion" => $row['mtv_descripcion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "obtienencreid":
                $lc_sql = "EXEC facturacion.TRN_cargartipofacturacion '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['ncre_id'] = $row["ncre_id"];
                        $this->lc_regs['tf_descripcion'] = $row["tf_descripcion"];
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "anulaFacturaSinFacturacionElectronica":
                $lc_sql = "update Formapago_Factura set std_id=8 where cfac_id='$lc_datos[0]'
						update cabecera_factura set std_id=35 where cfac_id='$lc_datos[0]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case "impresionFacturaError":
                    $lc_sql = "EXEC facturacion.TRN_reimpresion_factura_error '$lc_datos[0]'";
                    if ($result = $this->fn_ejecutarquery($lc_sql)) {
                        $this->lc_regs['Confirmar'] = 1;
                    } else {
                        $this->lc_regs['Confirmar'] = 0;
                    }
                    return json_encode($this->lc_regs);
                    break;
            case "validarUsuario":
                $lc_sql = "EXEC facturacion.TRN_validar_usuario_administrador " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['usr_id'] = $row["usr_id"];
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "validarCreencialesUsuario":
                $lc_sql = "EXEC facturacion.TRN_validar_credenciales_usuario " . $lc_datos[0] . ", '" . $lc_datos[3] . "', '" . $lc_datos[5] . "', '" . $lc_datos[1] . "', '" . $lc_datos[2] . "', '" . $lc_datos[4] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("usr_id" => $row['usr_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "actualizarMotivoAnulacion":
                $lc_sql = "EXEC facturacion.TRN_actualizar_motivo_anulacion_factura '" . $lc_datos[0] . "', '" . $lc_datos[2] . "', '" . $lc_datos[1] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['Confirmar'] = 1;
                } else {
                    $this->lc_regs['Confirmar'] = 0;
                }
                return json_encode($this->lc_regs);

            case "anularOrden":
                $lc_sql = "EXEC facturacion.TRN_anularFacturaFormaPagoEfectivo '" . $lc_datos[3] . "', '" . $lc_datos[0] . "', '" . $lc_datos[1] . "', '" . $lc_datos[2] . "','$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                    // masivo
                    if(isset($lc_datos[0], $lc_datos[3]) && $this->revertirMasivoApi($lc_datos[0], $lc_datos[3])){
                        $this->lc_regs['AnularMasivo'] = true;
                    }else{
                        $this->lc_regs['AnularMasivo'] = false;
                    }
                    // fin
                } else {
                    $this->lc_regs['str'] = 0;
                }
                return json_encode($this->lc_regs);

            case "generarNotaCredito":
                $lc_sql = "EXEC facturacion.TRN_crearNotaCredito '" . $lc_datos[3] . "', '" . $lc_datos[16] . "', " . $lc_datos[1] . ", " . $lc_datos[5] . ", '" . $lc_datos[18] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['cfac_id'] = $row["cfac_id"];
                        $this->lc_regs['tf_descripcion'] = htmlentities($row["tf_descripcion"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case "generarNotaCreditoOtros":
                $lc_sql = "	EXEC facturacion.TRN_crear_nota_credito_otros " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "', '" . $lc_datos[4] . "', '" . $lc_datos[5] . "', '" . $lc_datos[6] . "';
							EXEC facturacion.TRN_impresion_anulacion " . $lc_datos[0] . ", '" . $lc_datos[2] . "', '" . $lc_datos[1] . "', '" . $lc_datos[3] . "';";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cfac_id" => trim($row['cfac_id']),
                            "mesa_descripcion" => $row['mesa_descripcion'],
                            "odp_id" => trim($row['odp_id']),
                            "cfac_subtotal" => ROUND($row['cfac_subtotal'], 2),
                            "cfac_total" => ROUND($row['cfac_total'], 2),
                            "est_nombre" => $row['est_nombre'],
                            "ncre_id" => trim($row['ncre_id']),
                            "usuario" => trim($row['usuario']),
                            "descripcionEstado" => htmlentities($row['descripcionEstado']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "formasPago":
                $lc_sql = "EXEC facturacion.TRN_consulta_formas_pago '" . $lc_datos[0] . "';";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "cfac_id" => trim($row['cfac_id'])                  //factura 
                            , "fpf_id" => $row['fpf_id']                        //IDFormapagoFactura
                            , "fmp_id" => $row['fmp_id']                        //IDFormapago
                            , "cf_nombre" => $row['cf_nombre']                  // PICK UP TARJETA
                            , "fmp_descripcion" => $row['fmp_descripcion']       //DEBITO
                            , "tfp_id" => $row['tfp_id']                        //TARJETA DE DEBITO
                            , "fpf_swt" => $row['fpf_swt']                      // 5
                            , "secuenciaConfigurada" => $row['secuenciaConfigurada']  // 1
                            , "secuencia" => $row['secuencia']);                // Armar Trama->Esperar_Respuesta->Anular_Pago
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'muestraTipoCuenta':
                $lc_sql = "EXEC fac_muestraTipoCuenta";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("tptar_id" => trim($row['tptar_id']),
                            "tptar_descripcion" => $row['tptar_descripcion'],
                            "tptar_codigo" => $row['tptar_codigo']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'armaTramaSWTbanda':
                $lc_sql = "EXEC [facturacion].[IAE_SWT_InsertaRequerimientoAutorizacionBanda] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[3]', $lc_datos[5], '$lc_datos[4]', '$lc_datos[9]', '$lc_datos[6]','$lc_datos[7]','$lc_datos[8]', 0, 0, 2";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'esperaRespuestaRequerimientoAutorizacion':
                $lc_sql = "EXEC [facturacion].[USP_esperaRespuestaRequerimientoAutorizacion] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['existe'] = $row["existe"];
                        if ($row["existe"] == 1) {
                            $this->lc_regs['rsaut_respuesta'] = $row["rsaut_respuesta"];
                            $this->lc_regs['cres_codigo'] = $row["cres_codigo"];
                            $this->lc_regs['fpf_id'] = $row["fpf_id"];
                            $this->lc_regs['rsaut_id'] = $row["rsaut_id"];
                            $this->lc_regs['errorTrama'] = $row["errorTrama"];
                        }
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case "anu_insertarRequerimientoAutorizacion":
                $lc_sql = "EXEC  [facturacion].[IAE_InsertaRequerimientoAutorizacion]
     					@tipo_transaccion = N'$lc_datos[0]',
      					@cfac_id = N'$lc_datos[1]',
      					@estId = '$lc_datos[3]',
      					@rstId = $lc_datos[5],
      					@user = '$lc_datos[4]',
						@formaPago='$lc_datos[2]',
						@propina = N'0',
						@valorTransaccion = N'0.0',
                                                @tipoEnvio=$lc_datos[6];";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    $this->lc_regs['str'] = 0;
                }
                return json_encode($this->lc_regs);

            case 'lee_canalXMLfirmado':
                $lc_sql = "declare @claveAcceso varchar(50)
				set @claveAcceso=(select ncre_claveAcceso from Cabecera_Nota_Credito where ncre_id='$lc_datos[0]')
				if exists(select * from Canal_Movimiento_comprobante where std_id=51 and cmp_nombre_comprobante=@claveAcceso)
					begin
						select 'si' as existe,cmp_id,cmp_nombre_comprobante as nombreComprobante from Canal_Movimiento_comprobante where cmp_nombre_comprobante=@claveAcceso and  
						std_id=51 
					end
				else
						select 'no' as existe";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['existe'] = $row["existe"];
                        if ($row["existe"] == 'si') {
                            $this->lc_regs['cmp_id'] = $row["cmp_id"];
                            $this->lc_regs['nombreComprobante'] = $row["nombreComprobante"];
                        }
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'ws_recepcion':
                $lc_sql = "insert into respuesta_recepcion_comprobante 	 (rcp_clave_acceso,rcp_estado) 
						 values('$lc_datos[0]','$lc_datos[1]')					 
						 select rcp_clave_acceso,rcp_estado
						 from respuesta_recepcion_comprobante
						 where rcp_clave_acceso='$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['rcp_clave_acceso'] = $row["rcp_clave_acceso"];
                        $this->lc_regs['rcp_estado'] = $row["rcp_estado"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

								   
																																										   
																						 
																		  
																										   
											  
											 
													   
															
																					
																									
																					  
																			  
																								  
					 
																	
				 
												   

            case 'anu_consultaTipoEnvio':
                $lc_sql = "EXEC anu_consultaTipoEnvio '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['fpf_swt'] = $row["fpf_swt"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'recibirSWT':
                $lc_sql = "EXEC [facturacion].[USP_esperaRespuestaRequerimientoAutorizacion] '$lc_datos[0]'";
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

            case 'insertaNotaDeCredito':
                $lc_sql = "exec [facturacion].[TRN_crearNotaCredito] '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['cfac_id'] = $row["cfac_id"];
                        $this->lc_regs['tf_descripcion'] = $row["tf_descripcion"];
                        $this->lc_regs['aplicaEnEstacion'] = $row["aplicaEnEstacion"];
                        $this->lc_regs['idEstacion'] = $row["idEstacion"];
                        $this->lc_regs['servidorUrlApi'] = $row["servidorUrlApi"];
                    }
                   
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'actualizarEstadoFormaEliminada':
                $lc_sql = "EXEC anu_actualizarEstadoFormaEliminada '$lc_datos[0]', $lc_datos[1]";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'grabacanalmovimientoVoucher':
                $lc_sql = "EXEC [facturacion].[IAE_grabacanalMovimientoVoucher] 'I','$lc_datos[0]',$lc_datos[1],'$lc_datos[2]',$lc_datos[3],$lc_datos[4]";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'cancelaTarjetaForma':
                $lc_sql = "EXEC [facturacion].[anula_formaPagoTarjeta] 'U','$lc_datos[0]','$lc_datos[1]',$lc_datos[2],'$lc_datos[3]','$lc_datos[4]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case "obtenerMesa":
                $lc_query = "EXEC pedido.ORD_asignar_mesaordenpedido ".$lc_datos[0].", '".$lc_datos[1]."', '".$lc_datos[2]."', '".$lc_datos[3]."'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['respuesta'] = $row['respuesta'];
                        $this->lc_regs['IDFactura'] = $row['IDFactura'];
                        $this->lc_regs['IDOrdenPedido'] = $row['IDOrdenPedido'];
                        $this->lc_regs['IDMesa'] = $row['IDMesa'];
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        
            case "restauranteCashless":
                $lc_sql = "EXEC config.USP_PayCard_Restaurante 2," . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['ID_ColeccionRestaurante'] = $row["ID_ColeccionRestaurante"];
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "facturaCashless":
                $lc_sql = "EXEC config.USP_PayCard_factura " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                try {

                    if ($this->fn_ejecutarquery($lc_sql)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs['codigo_barras'] = $row["codigo_barras"];
                            $this->lc_regs['producto_cahsless'] = $row["mensaje"];
                        }
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    if($this->lc_regs['str'] > 0){
                        if($this->lc_regs['codigo_barras'] === NULL){
                            $observaciones = 'Nota de Credito cashless: La factura:'. $lc_datos[1] .' tiene un producto cahsless pero NO tiene el codigo del barras asignado';
                            $lc_sql_aud = "EXEC [seguridad].[IAE_Audit_registro] 'i','" . $lc_datos[3] . "',0,'FACTURACION','$observaciones','INFO'";
                            $ejecucion = $this->fn_ejecutarquery($lc_sql_aud);
                            $this->lc_regs['str'] = 0;
                        }
                    }
                return json_encode($this->lc_regs);
                }catch (Exception $e) {
                    $this->lc_regs['str'] = -1;
                    return json_encode($this->lc_regs);
                }

            case "insertaAuditoria":
                $lc_sql = "EXEC [seguridad].[IAE_Audit_registro] 'i','" . $lc_datos[0] . "',0,'FACTURACION','" . $lc_datos[1] . "','INFO'";
                $ejecucion = $this->fn_ejecutarquery($lc_sql);
                return $ejecucion; 
                 
            case "retorna_WS_URL_Cashless":
                $lc_sql = "EXEC config.USP_PayCard_Restaurante 3," . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $row = $this->fn_leerarreglo();
                    $this->lc_regs["datos"] = [
                        "urlwebservice" => utf8_decode($row[0])
                    ];
                }
                return $this->lc_regs["datos"];
            
            case "retorna_cedula_user":
                $lc_sql = "EXEC config.USP_PayCard_Restaurante 4," . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $row = $this->fn_leerarreglo();
                    $this->lc_regs["datos"] = [
                    "cedula_user" => utf8_decode($row[0])
                    ];
                }
                return $this->lc_regs["datos"];
        


        }
    }

    // POLITCAS
    function fn_politicaApiKeyMaisivo($idCadena){
        
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'WS CONFIGURACIONES', 'MASIVO API') AS api";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();

        if(isset($row['api']) || !empty($row['api'])){
            $data = (object)[
                "api" => $row['api']
            ];
            return $data;
        }

        return false;
    }
    // ATORIZACION

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

    // CHANNEL_ID
    
    function fn_politicaApiChannelId($idCadena){
        
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'WS CONFIGURACIONES', 'MASIVO CHANEL ID') AS channel_id";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();

        if(isset($row['chennel_id']) || !empty($row['chennel_id'])){
            $data = (object)[
                "channel" => $row['chennel_id']
            ];
            return $data;
        }
        return false;
    }

    // politica url autorizacion

    function fn_politicUrlAutorizacion($idCadena){
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'WS RUTA SERVICIO', 'MASIVO OBTENER BARER') AS barer";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();

        if(isset($row['barer']) || !empty($row['barer'])){
            $data = (object)[
                "url" => $row['barer']
            ];
            return $data;
        }
        return false;
    }

    function fn_politicUrlRevertirEventoMasivo($idCadena){
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'WS RUTA SERVICIO', 'MASIVO REVERTIR EVENTO') AS evento";
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

    // function para obtener autorizacion barer masivo
    public function obtenerAutorizacionMasivo($cadena,$nombreCadena){
        $curl = curl_init();

        if($cadena > 0){
            $data = $this->fn_politicUrlAutorizacion($cadena);
            $data2 = $this->fn_politicaApiKeyMaisivo($cadena);

            if($data && $data2){
                $array_data=json_decode($data2->api,true);
                if (is_array($array_data)) {
                    $array_return=[];
                    foreach ($array_data as $key => $value) {
                        $curl = curl_init();
                        
                        curl_setopt_array($curl, [
                            CURLOPT_URL => $data->url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "GET",
                            CURLOPT_HTTPHEADER => [
                                "x-api-key: ".$value['api-key']
                            ],
                        ]);
                
                        $response = json_decode(curl_exec($curl));
                        $err = curl_error($curl);
                
                        curl_close($curl);
                
                        if (!$err) {
                            if(isset($response->data)){
                                $array_return[$key]=array(
                                    "token"=>$response->data,
                                    "marca"=>$value['marca'],
                                    "activo"=>$value['activo']
                                );
                            }
                        }
                    }
                    return $nombreCadena!="JUAN VALDEZ"?$array_return[1]['token']:$array_return[2]['token'];
                }else {
                    return 'Error al decodificar el JSON.';
                }
            }

            /*if($data && $data2){
                curl_setopt_array($curl, [
                    CURLOPT_URL => $data->url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [
                        "x-api-key: $data2->api"
                    ],
                ]);
        
                $response = json_decode(curl_exec($curl));
                $err = curl_error($curl);
        
                curl_close($curl);
        
                if ($err || isset($response->error)) {
                    return 'La cadena no éxiste';
                }
                return $response->data;
            }*/
        }
        return 'La cadena no existe';
    }

    // fucion masivo api
    public function revertirMasivoApi($factura, $idUser='', $rason='Cliente cancelo la transaccion'){

        /*
        Tabla de apiMasivo tendra id, rason, status, cfac_id, id_event_masivo, created_at, updated_at
        */
        $sqlFactura = "SELECT * FROM Cabecera_Factura WHERE cfac_id = '$factura'";
        $idRestaurante = 0;
        $idEventMasivo = '';
        if ($this->fn_ejecutarquery($sqlFactura)) {
            while ($row = $this->fn_leerarreglo()) {
                $idEventMasivo = $row['evento_masivo'];
                $idRestaurante = $row['rst_id'];
            }

            // masivo
            if($idEventMasivo != ''){
                $curl = curl_init();

                $sql = "SELECT * FROM cadena";

                $cadena=0;
                $nombreCadena="JUAN VALDEZ";

                if($this->fn_ejecutarquery($sql)){
                    while ($row = $this->fn_leerarreglo()) {
                        $cadena = $row['cdn_id'] * 1;
                        $nombreCadena = strtoupper(trim($row['cdn_descripcion']));
                    }
                }
    
                $barer = $this->obtenerAutorizacionMasivo($cadena,$nombreCadena);

                if($cadena>0 && $barer){

                    $data = $this->fn_politicUrlRevertirEventoMasivo($cadena);

                    if($data){

                        $url = str_replace('varId', $idEventMasivo, $data->url);
    
                        $masivoBody = json_encode([
                            "reason"=>$rason
                        ]);
    
                        curl_setopt_array($curl, [
                            CURLOPT_URL => $url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => $masivoBody,
                            CURLOPT_HTTPHEADER => [
                                "Authorization: Bearer $barer"
                            ],
                        ]);
            
                        $response = json_decode(curl_exec($curl));
                        $err = curl_error($curl);
                        curl_close($curl);
            
                        if ($err || isset($response->error)) {
                            $sql = "EXEC [facturacion].[IAE_Logs_auditoria_transaccion] $idRestaurante, 'API MASIVO', 'Error reverso: Ocurrio n error en la respuesta - Peticion: ".json_encode($masivoBody)." - Respuesta: ".str_replace("'","",json_encode($response))."', 'REVERTIR', '', '','$idUser'";
    
                            if(!$this->fn_ejecutarquery($sql)){
                                // echo "1 ".$sql;
                                return false;
                            }
                            return false;
                        }
    
                        // INSERTAMOS EN LA AUDITORA
                        $sql = "EXEC [facturacion].[IAE_Logs_auditoria_transaccion] $idRestaurante, 'API MASIVO', 'REVERSION EN LA API DE MASIVO - Peticion: ".json_encode($masivoBody)." - Respuesta: ".str_replace("'","",json_encode($response))."', 'REVERTIR', '', '','$idUser'";
                        
                        if(!$this->fn_ejecutarquery($sql)){
                            // echo "2 ".$sql;
                            return false;
                        }
                        return true;
                    }else{
                        
                        $sql = "EXEC [facturacion].[IAE_Logs_auditoria_transaccion] $idRestaurante, 'API MASIVO', 'Error reverso: Politicas mal configuradas.', 'REVERTIR', '', '','$idUser'";

                        if(!$this->fn_ejecutarquery($sql)){
                            // echo "1 ".$sql;
                            return false;
                        }
                        return false;
                    }

                }
                return false;
            }else{
                return false;
            }
        }
        return true;   
    }

    public function fn_insertarRequerimientoAutorizacionUnired($lc_datos) {
        $lc_sql = "EXEC [facturacion].[IAE_SWT_InsertaRequerimientoAutorizacionPinPadProduccion]
     					@tipo_transaccion = N'$lc_datos[0]',
      					@cfac_id = N'$lc_datos[1]',
      					@estId = '$lc_datos[3]',
      					@rstId = $lc_datos[5],
      					@user = '$lc_datos[4]',
                                        @formaPago='$lc_datos[2]',
                                        @propina = N'0',
                                        @valorTransaccion = N'0.0',
                                        @tipoEnvio=5;";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $this->lc_regs['str'] = 1;
        } else {
            $this->lc_regs['str'] = 0;
        }
        return json_encode($this->lc_regs);
    }

    public function fn_validaCajeroActivoParaAnulacion($lc_datos) {
        $lc_query = "EXEC [facturacion].[USP_TRN_ValidaCajeroActivoParaAnulacion] '$lc_datos[0]'";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['estado'] = $row['estado'];
                $this->lc_regs['mensaje'] = utf8_encode($row['mensaje']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }  

    public function fn_CajeSecuenciaOrigen($IDFactura,$IDRestaurante) {
        $lc_query = "EXEC [facturacion].[Caje_secuencia_origen] '$IDFactura','$IDRestaurante';";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array(
                    'codigo' => trim(($row['codigo'])),
                    'origen' => trim($row['origen']));
            }
        }
        return $this->lc_regs;
    }

    public function fn_consultarRecargas($idPeriodo) {
        $lc_query = "EXEC [recargas].[recargasPorPeriodo] '$idPeriodo'";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "transaccion" => $row["transaccion"],
                    "valor" => $row["valor"],
                    "caja" => $row["caja"],
                    "cajero" => utf8_encode($row["cajero"]),
                    "estado" => $row["estado"],
                    "clienteDocumento" => $row["clienteDocumento"],
                    "cliente" => utf8_encode($row["cliente"]));
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function validarTransaccionConTransferencia($transaccion, $id_restaurante, $id_cadena) {
        $lc_query = "EXECUTE [facturacion].[USP_TRN_Valida_Transferencia] '$transaccion', $id_restaurante, $id_cadena";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['transferencia'] = $row['transferencia'];                        
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }


    public function secuencia_canje($IDFactura) {
        $lc_query = "EXECUTE [facturacion].[USP_TRN_Valida_Transferencia] '$transaccion', $id_restaurante, $id_cadena";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['transferencia'] = $row['transferencia'];                        
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function validarTransaccionConTiempo($transaccion, $id_restaurante,$ValidacionAnulacionFacturaTiempoApp,$ValidacionAnulacionFacturaTiempoFast) {
        $lc_query = "EXECUTE [facturacion].[USP_NotaCredito_Valida_tiempo] '$transaccion', $id_restaurante,$ValidacionAnulacionFacturaTiempoApp,$ValidacionAnulacionFacturaTiempoFast";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['tiempo'] = $row['tiempo'];                        
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function validacionConsumoServicio($medio, $servicio)
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


    public function buscarCuentasPeriodosAnteriores($parametros){
        
        $lc_sql = "EXEC facturacion.TRN_cargar_cuentas_periodos_anteriores " . $parametros['rstId']
                                                                            . ", '" . $parametros['fechaTran'] . "'"
                                                                            . ", '" . $parametros['codigoTran'] . "'"
                                                                            . ", '" . $parametros['nroFactura'] . "'"
                                                                            . ", '" . $parametros['identificacion'] . "'";
        
        if ($result = $this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "cfac_id" => trim($row['cfac_id']),
                    "mesa_descripcion" => $row['mesa_descripcion'],
                    "odp_id" => trim($row['odp_id']),
                    "cfac_subtotal" => ROUND($row['cfac_subtotal'], 2),
                    "cfac_total" => ROUND($row['cfac_total'], 2),
                    "est_id" => $row['est_id'],
                    "est_nombre" => $row['est_nombre'],
                    "ncre_id" => trim($row['ncre_id']),
                    "anuladaPA" => $row['anuladaPA'],
                    "usuario" => trim($row['usuario']),
                    "cfac_observacion" => htmlentities($row['cfac_observacion']),
                    "descripcionEstado" => htmlentities($row['descripcionEstado']),
                    "descripcionEstatus" => htmlentities($row['descripcionEstatus']),
                    "documento_con_datos" => $row['documento_con_datos'],
                    "impresion" => $row['impresion'],
                    "impuesto" => $row['impuesto'],
                    "ncdre_ant" => $row['ncdre_ant']
                );
            }
        }
        
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function getRestauranteColeccionDeDatos($rst_id, $coleccion, $parametro){
        
        $lc_sql = "SELECT   dato.variableV
                          , dato.variableI
                          , dato.variableD
                          , CASE dato.variableB WHEN 1 THEN 'SI' ELSE 'NO' END AS variableB
                          , dato.variableN
                          , CONVERT(DATE, dato.fechaIni, 103) AS fechaIni
                          , CONVERT(DATE, dato.fechaFin, 103) AS fechaFin
                          , dato.min
                          , dato.max
                     FROM ColeccionRestaurante AS coleccion WITH(NOLOCK)
                          INNER JOIN ColeccionDeDatosRestaurante AS parametro WITH(NOLOCK) 
                                  ON coleccion.ID_ColeccionRestaurante = parametro.ID_ColeccionRestaurante
                          INNER JOIN RestauranteColeccionDeDatos AS dato WITH(NOLOCK) 
                                  ON     dato.ID_ColeccionDeDatosRestaurante = parametro.ID_ColeccionDeDatosRestaurante 
                                     AND dato.ID_ColeccionRestaurante = coleccion.ID_ColeccionRestaurante
                    WHERE     coleccion.Descripcion = '" . $coleccion . "'
                          AND parametro.Descripcion = '" . $parametro . "'
                          AND coleccion.isActive = 1
                          AND parametro.isActive = 1
                          AND dato.isActive = 1 
                          AND dato.rst_id = " . $rst_id . " ;";
        
        if ($result = $this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["variableV"] = trim($row['variableV']);
                $this->lc_regs["variableI"] = $row['variableI'];
                $this->lc_regs["variableD"] = $row['variableD'];
                $this->lc_regs["variableB"] = $row['variableB'];
                $this->lc_regs["variableN"] = $row['variableN'];
                $this->lc_regs["fechaIni"] = $row['fechaIni'];
                $this->lc_regs["fechaFin"] = $row['fechaFin'];
                $this->lc_regs["min"] = $row['min'];
                $this->lc_regs["max"] = $row['max'];
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function getImpuestoRestaurante($cnd_id, $rst_id){
        
        $lc_sql = "SELECT porcentaje FROM [config].[fn_ColeccionRestaurante_Impuestos](" . $cnd_id . ", " . $rst_id . ")";
        
        if ($result = $this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["porcentajeImpuesto"] = trim($row['porcentaje']);
            }
        }
        return json_encode($this->lc_regs);
    }
    
    function valorarCambioSobreFactura($cfac_id) {
        $error = 1;
        $cambio = 0;
        $transactSQL = "EXEC [facturacion].[valorarCambioSobreFactura] '$cfac_id';";

        if($this->fn_ejecutarquery($transactSQL) AND $this->fn_numregistro() > 0) {
            $registro = $this->fn_leerarreglo();
            $error = $registro['error'];
            $cambio = $registro['cambio'];
        }

        return json_encode(['error' => $error, 'cambio' => $cambio]);
    }
}
