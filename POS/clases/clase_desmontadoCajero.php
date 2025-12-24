<?php

//////////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: JOSE FERNANDEZ///////////////////////////////////////////
///////DESCRIPCION	   : Archivo que contiene los queries del modulo Corte Caja///
////////TABLAS		   : ARQUEO_CAJA,BILLETE_ESTACION,////////////////////////////
//////////////////////////CONTROL_ESTACION,ESTACION///////////////////////////////
//////////////////////////BILLETE_DENOMINACION////////////////////////////////////
///////FECHA CREACION  : 20/12/2013///////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////

class desmontaCaja extends sql {

    public function fn_consultar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case 'reversarCCL':
                $lc_query = "EXECUTE [seguridad].[IAE_cajaChicaLocal] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_query)){
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array (
                            "fechaInicia"=>$row['fechaInicia'],
                            "fechaFinaliza"=>$row['fechaFinaliza'],
                            "Retiro"=>$row['Retiro']	
                        );
                    }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
            break;
            case 'consultaformaPago':
                $lc_query = "EXECUTE seguridad.USP_obtieneformapagoestacionusuario $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]',$lc_datos[4]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "fmp_id"                    => $row['fmp_id'],
                            "fmp_descripcion"           => $row['fmp_descripcion'],
                            "fpf_total_pagar"           => $this->ifNum($row['fpf_total_pagar']),
                            "Transacciones"             => $row['Transacciones'],
                            "ctrc_id"                   => $row['ctrc_id'],
                            "usr_id"                    => $row['usr_id'],
                            "totalRetirado"             => $this->ifNum($row['totalRetirado']),
                            "diferenciaValor"           => $this->ifNum($row['diferenciaValor']),
                            "estadoSwitch"              => $this->ifNum($row['estadoSwitch']),
                            "transaccionesIngresadas"   => $this->ifNum($row['transaccionesIngresadas']),
                            "TotalEgresos"              => $this->ifNum($row['TotalEgresos']),
                            "TotalIngresos"             => $this->ifNum($row['TotalIngresos']),
                            "TotalIngresos"             => $this->ifNum($row['TotalIngresos']),
                            "es_transferencia"          => $row['es_transferencia'],
                            "es_agregador"              => $row['es_agregador']
                        );
                    }
                    
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                
                $this->fn_liberarecurso();
                break;

            case 'consultaCupones':
                $lc_query = "EXECUTE seguridad.USP_obtieneformapagoestacionusuario $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]',$lc_datos[4]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "prd_id"            => $row['prd_id']
                            , "fmp_descripcion" => $row['fmp_descripcion']
                            , "est_id"          => $row['est_id']
                            , "Transacciones"   => $row['Transacciones']
                            , "mensaje"         => $row['mensaje']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cabecera_reporte_desmontado':
                $lc_query = "exec cc_cabecera_reporte_voucher $lc_datos[0]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'consultaformaPagoDesmontadoDirecto':
                $lc_query = "EXECUTE seguridad.USP_formapagodesmontadodirecto $lc_datos[0],'$lc_datos[1]'";
                if ($result = $this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['Valida'] = $row['Valida'];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'consultatotalEstacion':
                $lc_query = "EXEC seguridad.USP_consultatotalestacion $lc_datos[0], '$lc_datos[1]'";
                //todo:por alguna razon no esta funcionando EXECUTE seguridad.USP_consultatotalestacion 1, '192.168.0.138'
                try {
                    if ($result = $this->fn_ejecutarquery($lc_query)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs['total'] = $row['total'];
                            $this->lc_regs['totalRetirosFormaPagos'] = $row['totalRetirosFormaPagos'];
                            $this->lc_regs['totalDiferenciaFormaPagos'] = $row['totalDiferenciaFormaPagos'];
                            $this->lc_regs['transacciones'] = $row['transacciones'];
                            $this->lc_regs['TotalEgresos'] = $row['TotalEgresos'];
                            $this->lc_regs['TotalIngresos'] = $row['TotalIngresos'];
                        }
                        $this->lc_regs['str'] = $this->fn_numregistro();
                    }
                    return json_encode($this->lc_regs);
                }catch (Exception $e){
                    $this->lc_regs['str'] = -1;
                    return json_encode($this->lc_regs);
                }

            case 'consultausuarioenEstacion':
                $lc_query = "EXECUTE seguridad.USP_consultausuarioenestacion $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($result = $this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['usr_id'] = $row['usr_id'];
                        $this->lc_regs['usr_usuario'] = $row['usr_usuario'];
                        $this->lc_regs['cedula'] = $row['cedula'];
                        $this->lc_regs['rst_id'] = $row['rst_id'];
                        $this->lc_regs['fecha'] = $row['fecha'];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'reporteDesmontado':
                $lc_query = "exec dc_reporteDesmontadoCajero $lc_datos[0]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'totalesReporte':
                $lc_query = "exec dc_totalesReporteDesmontadoCajero  $lc_datos[0]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'consultatotalformaPago':
                $lc_query = "EXECUTE seguridad.USP_totalformapago $lc_datos[2], '$lc_datos[0]' , '$lc_datos[1]'";
                if ($result = $this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['total'] = $row['total'];
                        $this->lc_regs['totalRetiros'] = $row['totalRetiros'];
                        $this->lc_regs['totalArqueo'] = $row['totalArqueo'];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'consultaBilletes':
                $lc_query = "EXECUTE seguridad.USP_consultabilletes $lc_datos[0], '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("btd_id" => $row['btd_id'], "btd_Valor" => $this->ifNum($row['btd_Valor']), "btd_Descripcion" => $row['btd_Descripcion'], "btd_Tipo" => $row['btd_Tipo']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'totalesIngresados':
                $lc_query = "EXECUTE seguridad.USP_totalesingresados '$lc_datos[0]',$lc_datos[1], '$lc_datos[2]', '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fmp_id" => $row['fmp_id'], "arc_valor" => $this->ifNum($row['arc_valor']), "fmp_descripcion" => $row['fmp_descripcion']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'totalesPos':
                $lc_query = "EXECUTE seguridad.USP_totalespos $lc_datos[0], '$lc_datos[1]' ";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['totalesPos'] = $row["totalesPos"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaTarjeta':
                $lc_query = "EXECUTE seguridad.USP_consultatarjeta $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', $lc_datos[3], '$lc_datos[4]', $lc_datos[5], $lc_datos[6]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "total" => $this->ifNum($row['total'])
                            , "fmp_descripcion" => utf8_encode(trim($row['fmp_descripcion']))
                            , "es_transferencia" => $row['es_transferencia']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'calculatotalesModificados':
                $lc_query = "EXECUTE seguridad.USP_totalesmodificados '$lc_datos[0]','$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], '$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['retiroValor'] = $this->ifNum($row["retiroValor"]);
                        $this->lc_regs['diferencia'] = $this->ifNum($row["diferencia"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaformaPagoModificado':
                $lc_query = "EXECUTE seguridad.USP_formaPagoModificado '$lc_datos[0]','$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', '$lc_datos[4]', $lc_datos[5], '$lc_datos[6]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "fmp_id" => $row['fmp_id'],
                            "fmp_descripcion" => $row['fmp_descripcion'],
                            "fpf_total_pagar" => $this->ifNum($row['fpf_total_pagar']),
                            "Transacciones" => $this->ifNum($row['Transacciones']),
                            "ctrc_id" => $row['ctrc_id'],
                            "arc_valor" => $this->ifNum($row['arc_valor']),
                            "diferencia" => $this->ifNum($row['diferencia']),
                            "transaccionesIngresadas" => $this->ifNum($row['transaccionesIngresadas']),
                            "retiroValor" => $this->ifNum($row['retiroValor']));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaformaPagoModificadoTarjeta':
                $lc_query = "EXECUTE seguridad.USP_formaPagoModificadoTarjeta '$lc_datos[0]','$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', $lc_datos[4], $lc_datos[5], '$lc_datos[6]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fmp_id" => $row['fmp_id'],
                            "fmp_descripcion" => trim($row['fmp_descripcion']),
                            "fpf_total_pagar" => $this->ifNum($row['fpf_total_pagar']),
                            "Transacciones" => $this->ifNum($row['Transacciones']),
                            "ctrc_id" => $row['ctrc_id'],
                            "arc_valor" => $this->ifNum($row['arc_valor']),
                            "diferencia" => $this->ifNum($row['diferencia']),
                            "retiroValor" => $this->ifNum($row['retiroValor']),
                            "transaccionesIngresadas" => $this->ifNum($row['transaccionesIngresadas']));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaBilletesModificados':
                $lc_query = "seguridad.USP_consultaBilletes $lc_datos[0], '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("btd_id" => $row['btd_id'], "btd_Valor" => $row['btd_Valor'], "btd_Descripcion" => $row['btd_Descripcion'], "bte_cantidad" => $row['bte_cantidad'], "bte_total" => $row['bte_total'], "btd_Tipo" => $row['btd_Tipo']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaidBilletes':
                $lc_query = "EXECUTE seguridad.USP_consultaBilletes $lc_datos[0], '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("bte_id" => $row['bte_id']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'validaMontoDescuadre':
                $lc_query = "EXECUTE seguridad.USP_validamontodescuadreporcadena $lc_datos[0],$lc_datos[1], $lc_datos[2]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("validamontodescuadre" => $this->ifNum($row['validamontodescuadre']));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'impresionDinamicaCorteCaja':
                $lc_query = "EXECUTE [seguridad].[USP_impresion_dinamica_CorteZ] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };
                break;
            case 'existeCuentaAbierta':
                $lc_query = "EXECUTE seguridad.USP_cuentasAbiertas $lc_datos[0],$lc_datos[1], '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['cuentaAbierta'] = $this->ifNum($row["cuentaAbierta"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'existeCuentaAbiertaMesa':
                $lc_query = "EXECUTE seguridad.USP_cuentasAbiertas $lc_datos[0],$lc_datos[1], '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['cuentaAbiertaMesa'] = $this->ifNum($row["cuentaAbiertaMesa"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'verificaCuentasAbiertaTodasEstaciones':
                $lc_query = "EXECUTE seguridad.USP_cuentasAbiertasEnTodasEstaciones $lc_datos[0],'$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['cuentas_abiertas'] = $row["cuentas_abiertas"];
                        $this->lc_regs['cuentas_abiertas_mesa'] = $row["cuentas_abiertas_mesa"];
                        $this->lc_regs['es_ultima_caja'] = $row["es_ultima_caja"];
                        $this->lc_regs['mensaje'] = $row["mensaje"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaformaPagoEfectivo':
                $lc_query = "EXECUTE seguridad.USP_obtieneFormaPagoEfectivo $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "fmp_id" => $row['fmp_id'],
                            "fmp_descripcion" => $row['fmp_descripcion'],
                            "fpf_total_pagar" => $this->ifNum($row['fpf_total_pagar']),
                            "Transacciones" => $this->ifNum($row['Transacciones']),
                            "ctrc_id" => $row['ctrc_id'],
                            "usr_id" => $row['usr_id'],
                            "arc_valor" => $this->ifNum($row['arc_valor']),
                            "diferencia" => $this->ifNum($row['diferencia']),
                            "TransaccionesIngresadas" => $this->ifNum($row['TransaccionesIngresadas']),
                            "estadoSwitch" => $this->ifNum($row['estadoSwitch']),
                            "TotalEgresos" => $this->ifNum($row['TotalEgresos']),
                            "TotalIngresos" => $this->ifNum($row['TotalIngresos']),
                            "es_agregador" => $row['es_agregador']
                        );
                    }
                    
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                
                $this->fn_liberarecurso();
                break;

            case 'consultaValorRetiroEfectivo':
                $lc_query = "EXECUTE seguridad.USP_obtieneValorRetiroEfectivo $lc_datos[0],'$lc_datos[1]', '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['ValorTotalEfectivo'] = $row["ValorTotalEfectivo"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'desasignarEnEstacion':
                $lc_query = "EXECUTE seguridad.USP_validaDesasignarEnEstacion $lc_datos[0],'$lc_datos[1]', $lc_datos[2]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['desasignaEstacion'] = $this->ifNum($row["desasignaEstacion"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'traeUsuarioAdmin':
                $lc_query = "EXECUTE seguridad.USP_validausuarioclaveperfil $lc_datos[0],'$lc_datos[1]', '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['usr_id'] = $row["usr_id"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'traeMotivosDescuadre':
                $lc_query = "EXECUTE seguridad.USP_motivosDescuadreCierreCaja $lc_datos[0], $lc_datos[1]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("mtv_id" => $row['mtv_id'],
                            "IdCabeceraMotivoAnulacion" => $row['IdCabeceraMotivoAnulacion'],
                            "mtv_descripcion" => $row['mtv_descripcion']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;
            
            case 'validaDesasignarCajero':
                $lc_query = "EXECUTE config.desasignar_cajero_por_horario $lc_datos[0], $lc_datos[1],'$lc_datos[2]'";

                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['resp'] = $this->ifNum($row["resp"]);
                        $this->lc_regs['salida'] = $this->ifNum($row["salida"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'validaDesasignarCajero':
                $lc_query = "EXECUTE config.desasignar_cajero_por_horario $lc_datos[0], $lc_datos[1],'$lc_datos[2]'";

                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['resp'] = $row["resp"];
                        $this->lc_regs['salida'] = $row["salida"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'retirofondo':
                $lc_query = "EXECUTE seguridad.USP_validaretirofondo $lc_datos[0], '$lc_datos[1]','$lc_datos[2]', $lc_datos[3], $lc_datos[4]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['retirofondo'] = $this->ifNum($row["retirofondo"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cargarFormasPago':
                $lc_query = "EXECUTE seguridad.USP_consultaFormasPago $lc_datos[0], '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fmp_id" => $row['fmp_id'],
                            "fmp_descripcion" => $row['fmp_descripcion']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'traeValorFormaPago':
                $lc_query = "EXECUTE seguridad.USP_consultaFormasPago $lc_datos[0], '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("arc_valor" => $row['arc_valor']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'validarUsuarioAdministrador':
                $lc_sql = "exec seguridad.USP_validaUsuario $lc_datos[1], '$lc_datos[0]', '$lc_datos[2]','$lc_datos[3]', '$lc_datos[4]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['admini'] = $row["admini"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
        }
    }

    public function fn_ejecutar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case 'grabaBilletes':
                $lc_query = "EXEC seguridad.IAE_grabaBilletesestacion '$lc_datos[0]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[5]', $lc_datos[4], $lc_datos[1], $lc_datos[6], $lc_datos[7]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'grabaArqueo':
                $lc_query = "EXECUTE seguridad.IAE_grabaarqueo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], $lc_datos[6], $lc_datos[7], $lc_datos[8], $lc_datos[9], $lc_datos[10], '$lc_datos[11]', $lc_datos[12],$lc_datos[13], $lc_datos[14], $lc_datos[15], '$lc_datos[16]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'grabaarqueotarjeta':
                $lc_query = "EXECUTE seguridad.IAE_grabaarqueo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], $lc_datos[6], $lc_datos[7], $lc_datos[8], $lc_datos[9], $lc_datos[10], '$lc_datos[11]', $lc_datos[12], $lc_datos[13], $lc_datos[14], $lc_datos[15], '$lc_datos[16]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'grabaarqueoformapago':
                $lc_query = "EXECUTE seguridad.IAE_grabaarqueo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], $lc_datos[6], $lc_datos[7], $lc_datos[8], $lc_datos[9], $lc_datos[10], '$lc_datos[11]', $lc_datos[12], $lc_datos[13], $lc_datos[14], $lc_datos[15], '$lc_datos[16]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'eliminaFormasPagoAgregadas':
                $lc_query = "EXECUTE seguridad.IAE_grabaarqueo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], $lc_datos[6], $lc_datos[7], $lc_datos[8], $lc_datos[9], $lc_datos[10], '$lc_datos[11]', $lc_datos[12], $lc_datos[13], $lc_datos[14], $lc_datos[15], '$lc_datos[16]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'eliminaBilletes':
                $lc_query = "EXECUTE seguridad.IAE_eliminabilletes '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'eliminaArqueo':
                $lc_query = "EXECUTE seguridad.IAE_grabaarqueo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], $lc_datos[6], $lc_datos[7], $lc_datos[8], $lc_datos[9], $lc_datos[10], '$lc_datos[11]', $lc_datos[12], $lc_datos[13], $lc_datos[14],$lc_datos[15], '$lc_datos[16]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'actualizaCajero':
                $lc_query = "EXECUTE seguridad.IAE_actualizacajeromotivo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', '$lc_datos[4]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'actualizaCajeroMotivo':
                $lc_query = "EXECUTE seguridad.IAE_actualizacajeromotivodescuadre '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], '$lc_datos[5]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'auditoriaCajero':
                $lc_query = "EXECUTE seguridad.IAE_actualizacajeromotivodescuadre '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], '$lc_datos[5]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'auditoriaCajeroMotivo':
                $lc_query = "EXECUTE seguridad.IAE_actualizacajeromotivodescuadre '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], '$lc_datos[5]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'auditoriaEfectivo':
                $lc_query = "EXECUTE seguridad.IAE_auditoriaefectivo '$lc_datos[0]', $lc_datos[1], $lc_datos[2], $lc_datos[3]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'auditoriaTarjeta':
                $lc_query = "EXECUTE seguridad.IAE_auditoriatarjeta '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2],$lc_datos[3], '$lc_datos[4]', $lc_datos[5]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'DesmontadoDirecto':
                $lc_query = "EXECUTE seguridad.IAE_desmontadoDirecto '$lc_datos[0]','$lc_datos[1]',$lc_datos[2],'$lc_datos[3]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'imprimeDesmontadoCajero':
                $lc_query = "EXECUTE seguridad.IAE_inserta_canalMovimiento_desmontadoCajero '$lc_datos[0]','$lc_datos[1]', '$lc_datos[2]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'auditoriaRetiroEfectivo':
                $lc_query = "EXECUTE seguridad.IAE_auditoria_RetiroEfectivo '$lc_datos[0]', $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', $lc_datos[5]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'grabaBilletesMod':
                $lc_query = "EXECUTE seguridad.IAE_auditoria_RetiroEfectivo '$lc_datos[0]', $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', $lc_datos[5]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'asentarRetiroEfectivo':
                $lc_query = "EXECUTE seguridad.IAE_asientaBilletesefectivo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], $lc_datos[6]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; }; 

            case 'eliminaBilletesPendiente':
                $lc_query = "EXECUTE seguridad.IAE_asientaBilletesefectivo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], $lc_datos[6]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'InsertcanalmovimientoCorteX':
                $lc_query = "EXECUTE [seguridad].[IAE_CanalMovimiento_CorteCajaX] '$lc_datos[0]','$lc_datos[1]', '$lc_datos[2]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'retiroCashless':
                $lc_query = "EXECUTE [seguridad].[IAE_inserta_retiroCashless_desmontadoCajero] '$lc_datos[0]','$lc_datos[1]', '$lc_datos[2]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };
                
            case 'eliminaFormasPagoAgregadasCashless':
                $lc_query = "EXECUTE seguridad.IAE_grabaarqueo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], $lc_datos[6], $lc_datos[7], $lc_datos[8], $lc_datos[9], $lc_datos[10], '$lc_datos[11]', $lc_datos[12], $lc_datos[13], $lc_datos[14], $lc_datos[15], '$lc_datos[16]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; }; 
        }
    }

    function fn_impresionDinamicaCorteX($lc_datos) {
        $lc_query = "EXECUTE [seguridad].[USP_impresion_dinamica_CorteX] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return $result; }else{ return false; };
    }

    function fn_canalMovimientoArqueo($lc_datos) {
        $lc_query = "EXECUTE [reporte].[ARQUEO_IAE_ArqueoCaja] '$lc_datos[0]','$lc_datos[1]', '$lc_datos[2]'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return $result; }else{ return false; };
    }

    function fn_impresionDinamicaArquero($lc_datos) {
        $lc_query = "EXECUTE [reporte].[ARQUEO_USP_ImpresionDinamica_ArqueoCaja] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return $result; }else{ return false; };
    }

    function fn_fn_impresionDinamicaRetiroEfectivo($lc_datos) {
        $lc_query = "EXECUTE [reporte].[RETIROS_USP_impresiondinamicaEfectivo] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return $result; }else{ return false; };
    }

    function fn_fn_impresionDinamicaRetiroFormasPago($lc_datos) {
        $lc_query = "EXECUTE [reporte].[RETIROS_USP_impresiondinamicaFormasPago] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return $result; }else{ return false; }
    }

    function fn_impresionDinamicaDesmontadoCajeroEfectivo($lc_datos) {
        $lc_query = "EXECUTE [reporte].[DESASIGNARCAJERO_USP_impresiondinamicaEfectivo] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return $result; }else{ return false; };
    }

    function fn_validaTransferencia($lc_datos) {
        $lc_query = "EXECUTE facturacion.USP_TRANSFERENCIAVENTA_validacionOrigenDestino $lc_datos[0], $lc_datos[1]";
        if ($result = $this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["Valida"] = $row["Valida"];
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }
    
    function fn_consultaValorTransferencia($lc_datos,$formatoJSON=true) {
        $lc_query = "EXECUTE facturacion.USP_TRANSFERENCIAVENTA_totalventacajero_ORIGEN $lc_datos[0],$lc_datos[1], $lc_datos[2],'$lc_datos[3]'";
        if ($result = $this->fn_ejecutarquery($lc_query)) {
            while($row = $this->fn_leerarreglo()) {
                $this->lc_regs['totalTransferencia'] = $row["totalTransferencia"];
                $this->lc_regs['tipo_transferencia'] = $this->ifNum($row["tipo_transferencia"]);
                $this->lc_regs['json_datosTransferencia'] = $row["json_datosTransferencia"];                
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        
        //Esta funcion se llama desde ajax y desde PHP, por eso 
        //se manda una bandera opcional para formatear como JSON o devolver
        //un array de PHP
        if(true===$formatoJSON)return json_encode($this->lc_regs);
        return $this->lc_regs;
    }
    
    function fn_unificacion_transferencia_de_venta($idRestaurante) {
        $lc_query = "EXECUTE [seguridad].[USP_unificacion_transferencia_de_venta] '$idRestaurante'";
        $estado=0;
        if ($this->fn_ejecutarquery($lc_query)) 
        {
            while ($row = $this->fn_leerarreglo()){
                $estado=trim($row['aplica_transferencia']);
            }
        }
        unset($lc_query,$idEstacion);
        return $estado;
    }
    
    function fn_generaEgresoTransferenciaOrigen($lc_datos) {
        $lc_query = "EXECUTE facturacion.IAE_TransferenciaVenta_Egreso '$lc_datos[0]', '$lc_datos[1]'";
        $resultado = $this->fn_ejecutarquery($lc_query);
        $respuesta = ($resultado !== false ? true : false);
        return json_encode($respuesta);
    }
    
    function fn_impresionDinamicaFaltanteCxC($lc_datos){
        $lc_query = "EXECUTE [facturacion].[USP_impresiondinamica_faltanteCxC] '$lc_datos[1]'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return $result; }else{ return false; };
    }
    function fn_consultaFondoAsignado($lc_datos) 
    {
        $lc_query = "EXECUTE [seguridad].[USP_Retiro_FondoAsignado] '$lc_datos[0]','0','0','0', '$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_query)) 
                {
                    while ($row = $this->fn_leerarreglo()) 
                    {
                        $this->lc_regs[] = array("fondo" => $row['fondo']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
    }


    function fn_impresionDesasignacionMotorizado($id_motorizado, $id_periodo) {
        $lc_sql = "EXEC facturacion.App_impresion_dinamica_desasignacion_motorizado '$id_motorizado',  '$id_periodo'";
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result){ return $result; }else{ return false; };
    }

    function validaInterfaceCuadreVenta($accion, $id_periodo, $id_usuario, $id_control) {
        $lc_query = "EXECUTE [interface].[USP_Valida_Cuadre_Venta] $accion, '$id_periodo', '$id_usuario', '$id_control'";

        if ($this->fn_ejecutarquery($lc_query)) {
            while($row = $this->fn_leerarreglo()) {
                $this->lc_regs['existe_diferencia'] = $row["existe_diferencia"];
                $this->lc_regs['mensaje'] = utf8_encode($row["mensaje"]);
                $this->lc_regs['tipo'] = $row["tipo_cuadre"];
                $this->lc_regs['interface_json'] = $row["interface_json"];
                $this->lc_regs['interface_eventos'] = $row["interface_eventos"]; 
                $this->lc_regs['log_descuadre'] = $row["log_descuadre"];                
               
            }
        }

        $this->lc_regs['str'] = $this->fn_numregistro();

        return json_encode($this->lc_regs);
    }
}