<?php

////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////
///////////DESCRIPCION: DESMONTAR CAJERO ///////////////////////////////////////////
////////////////TABLAS: Control_Estacion, Periodo, Estacion/////////////////////////
////////FECHA CREACION: 13/10/2015//////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

class desmontarCajero extends sql {
    function __construct() {
        parent ::__construct();
    }


    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case "cargarBotonesDias":
                $lc_sql = "EXECUTE config.USP_botonesDiasDesasignarCajero $lc_datos[0], '$lc_datos[1]', $lc_datos[2]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"id_dias" => $row['id_dias'],
							"dias" => utf8_encode(trim($row['dias']))
						);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarfechas":
                $lc_sql = "EXECUTE config.USP_traeFechasPeriodo $lc_datos[0], $lc_datos[1], $lc_datos[2], $lc_datos[3]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"prd_id" => $row['prd_id'],
                            "prd_fechaapertura" => $row['prd_fechaapertura'],
                            "prd_usuarioapertura" => $row['prd_usuarioapertura'],
							"rst_id" => $row['rst_id']
						);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "muestraUsuariosEstado":
                $lc_sql = "EXECUTE config.USP_estadoUsuarios $lc_datos[0], '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"ctrc_id" => $row['ctrc_id'],
                            "usr_id" => $row['usr_id'],
                            "usr_usuario" => $row['usr_usuario'],
                            "std_id" => $row['std_id'],
                            "estado_usuario" => $row['estado_usuario'],
                            "ctrc_usuario_desmontarcaja" => $row['ctrc_usuario_desmontarcaja'],
                            "est_id" => $row['est_id'],
                            "estadoPeriodo" => $row['estadoPeriodo'],
                            "IDPeriodo" => $row['IDPeriodo'],
							"PorEnviar" => intval($row['PorEnviar']),
							"esOperador" => intval($row['esOperador'])
						);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'formasPagoInactivo':
                $lc_query = "EXECUTE config.USP_desmontadoCajeroAdministracion $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "fmp_id" => $row['fmp_id'],
                            "fmp_descripcion" => $row['fmp_descripcion'],
                            "retiro_efectivo" => $row['retiro_efectivo'],
                            "Transacciones" => $row['Transacciones'],
                            "arc_valor" => $row['arc_valor'],
                            "fpf_total_pagar" => $row['fpf_total_pagar'],
                            "diferenciaValor" => $row['diferenciaValor'],
                            "tpenv_id" => $row['tpenv_id'],
                            "es_agregador_o_transferencia" => $row['es_agregador_o_transferencia'],   
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'totalesPagoInactivo':
                $lc_query = "EXECUTE config.USP_desmontadoCajeroAdministracion $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"retiro_efectivo" => $row['retiro_efectivo'],
                            "arc_numero_transacciones" => $row['arc_numero_transacciones'],
                            "arc_valor" => $row['arc_valor'],
                            "fpf_total_pagar" => $row['fpf_total_pagar'],
							"diferencia" => $row['diferencia']
						);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'formasPagoActivo':
                $lc_query = "EXECUTE config.USP_desmontadoCajeroAdministracion $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"fmp_descripcion" => $row['fmp_descripcion'],
                            "retiro_efectivo" => $row['retiro_efectivo'],
                            "numero_transacciones" => $row['numero_transacciones'],
                            "cfac_total" => $row['cfac_total'],
							"diferenciaValor" => $row['diferenciaValor']
						);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'totalesPagoActivo':
                $lc_query = "EXECUTE config.USP_desmontadoCajeroAdministracion $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"totalTransacciones" => $row['totalTransacciones'],
                            "totalRetiros" => $row['totalRetiros'],
                            "totalDiferencia" => $row['totalDiferencia'],
							"totalEstacion" => $row['totalEstacion']
						);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cabecera_reporte_desmontado':
                $lc_query = "exec cc_cabecera_reporte_voucher $lc_datos[0]";
                return $this->fn_ejecutarquery($lc_query);

            case 'consultaformaPago':
                $lc_query = "EXECUTE config.USP_obtieneformapagoestacionusuario $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"fmp_id" => $row['fmp_id'],
                            "fmp_descripcion" => $row['fmp_descripcion'],
                            "fpf_total_pagar" => $row['fpf_total_pagar'],
                            "Transacciones" => $row['Transacciones'],
                            "usr_id" => $row['usr_id'],
                            "ctrc_id" => $row['ctrc_id'],
                            "retiroValor" => $row['retiroValor'],
                            "diferenciaValor" => $row['diferenciaValor'],
                            "estadoSwt" => $row['estadoSwt']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaValorRetiroEfectivo':
                $lc_query = "EXECUTE config.USP_obtieneValorRetiroEfectivo $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]',  '$lc_datos[3]', '$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['ValorTotalEfectivo'] = $row["ValorTotalEfectivo"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultatotalEstacion':
                $lc_query = "EXECUTE config.USP_consultatotalestacion $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['total'] = $row['total'];
                        $this->lc_regs['Transacciones'] = $row['Transacciones'];
                        $this->lc_regs['totalDiferencia'] = $row['totalDiferencia'];
                        $this->lc_regs['totalRetiros'] = $row['totalRetiros'];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'consultatotalformaPago':
                $lc_query = "EXECUTE config.USP_totalformapago $lc_datos[2], '$lc_datos[0]' , '$lc_datos[1]', '$lc_datos[3]', '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['total'] = $row['total'];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'consultaBilletes':
                $lc_query = "EXECUTE config.USP_consultabilletes '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"btd_id" => $row['btd_id'], 
							"btd_Valor" => $row['btd_Valor'],
							"bte_cantidad" => $row['bte_cantidad'],
							"bte_total" => $row['bte_total'],
							"btd_Descripcion" => $row['btd_Descripcion'], 
							"btd_Tipo" => $row['btd_Tipo']
						);
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

            case 'consultaformaPagoModificado':
                $lc_query = "EXECUTE config.USP_formaPagoModificado '$lc_datos[0]','$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', '$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"fmp_id" => $row['fmp_id'],
							"fmp_descripcion" => $row['fmp_descripcion'],
							"fpf_total_pagar" => $row['fpf_total_pagar'],
							"Transacciones" => $row['Transacciones'],
							"ctrc_id" => $row['ctrc_id'],
							"arc_valor" => $row['arc_valor'],
							"diferencia" => $row['diferencia']
						);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'calculatotalesModificados':
                $lc_query = "EXECUTE config.USP_totalesmodificados '$lc_datos[0]','$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', '$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['totalModificado'] = $row["totalModificado"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaTarjeta':
                $lc_query = "EXECUTE config.USP_consultatarjeta $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', $lc_datos[3], '$lc_datos[4]', '$lc_datos[5]', $lc_datos[6]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"total" => $row['total'],
							"fmp_descripcion" => $row['fmp_descripcion']
						);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaformaPagoModificadoTarjeta':
                $lc_query = "EXECUTE config.USP_formaPagoModificadoTarjeta '$lc_datos[0]','$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"fmp_id" => $row['fmp_id'],
							"fmp_descripcion" => trim($row['fmp_descripcion']),
							"posCalculadoValor" => $row['posCalculadoValor'],
							"Transacciones" => $row['Transacciones'],
							"ctrc_id" => $row['ctrc_id'],
							"arc_valor" => $row['arc_valor'],
							"diferencia" => $row['diferencia'],
							"estadoSwt" => $row['estadoSwt']
						);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'totalesIngresados':
                $lc_query = "EXECUTE config.USP_totalesingresados '$lc_datos[0]',$lc_datos[1], $lc_datos[2], '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"fmp_id" => $row['fmp_id'],
							"arc_valor" => $row['arc_valor'],
							"fmp_descripcion" => $row['fmp_descripcion'],
							"estadoSwt" => $row['estadoSwt']
						);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'totalesPos':
                $lc_query = "EXECUTE config.USP_totalespos $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]' ";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['totalRetiro'] = $row["totalRetiro"];
                        $this->lc_regs['totalTransacciones'] = $row["totalTransacciones"];
                        $this->lc_regs['totalMontoActual'] = $row["totalMontoActual"];
                        $this->lc_regs['totalPos'] = $row["totalPos"];
                        $this->lc_regs['totalDiferencia'] = $row["totalDiferencia"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'validaMontoDescuadre':
                $lc_query = "EXECUTE config.USP_validamontodescuadreporcadena $lc_datos[0],$lc_datos[1], $lc_datos[2]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("validamontodescuadre" => $row['validamontodescuadre']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'traeUsuarioAdmin':
                $lc_query = "EXECUTE config.USP_validausuarioclaveperfil $lc_datos[0],'$lc_datos[1]', '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['usr_id'] = $row["usr_id"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cargarFormasPago':
                $lc_query = "EXECUTE config.USP_consultaFormasPago $lc_datos[0], '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"fmp_id" => $row['fmp_id'],
							"fmp_descripcion" => $row['fmp_descripcion']
						);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'traeValorFormaPago':
                $lc_query = "EXECUTE config.USP_consultaFormasPago $lc_datos[0], '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("arc_valor" => $row['arc_valor']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'existeCuentaAbiertaMesa':
                $lc_query = "EXECUTE config.USP_cuentasAbiertas $lc_datos[0],$lc_datos[1], '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['cuentaAbiertaMesa'] = $row["cuentaAbiertaMesa"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'existeCuentaAbierta':
                $lc_query = "EXECUTE config.USP_cuentasAbiertas $lc_datos[0],$lc_datos[1], '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['cuentaAbierta'] = $row["cuentaAbierta"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'retirofondo':
                $lc_query = "EXECUTE config.USP_validaretirofondo '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['retirofondo'] = $row["retirofondo"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;
                /**
                *consultaCupones 
                *Obttiene formaas de pago de cada una de las estaciones.
                *
                */
            case 'consultaCupones':
                $lc_query = "EXECUTE config.USP_obtieneformapagoestacionusuario '$lc_datos[0]', $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							'prd_id' => $row['prd_id'],
							'fmp_descripcion' => $row['fmp_descripcion'],
							'est_id' => $row['est_id'],
							'Transacciones' => $row['Transacciones'],
							'mensaje' => $row['mensaje'],
                            'valor' => $row['valor']
						);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'reporteFinDeDia':
                $lc_query = "EXECUTE [seguridad].[USP_impresion_dinamica_FindelDia] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
                //echo $lc_query;
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"html" => utf8_encode($row['html']),
                            "htmla" => utf8_encode($row['htmla']),
                            "htmlb" => utf8_encode($row['htmlb']),
							"htmld" => utf8_encode($row['htmld']),
                            "htmlc" => utf8_encode($row['htmlc']),
							"htmlf" => utf8_encode($row['htmlf'])
						);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
				break;

            case 'validarUsuarioAdministrador':
                $lc_sql = "exec seguridad.USP_validaUsuario $lc_datos[1], '$lc_datos[0]', '$lc_datos[2]','$lc_datos[3]', '$lc_datos[4]'";
                if ( $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['admini'] = $row["admini"];
                        $this->lc_regs['IDUsersPosAdmin'] = $row["IDUsersPosAdmin"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
        }
    }

    public function fn_ejecutar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case 'grabaBilletes':
                $lc_query = "EXEC config.IAE_grabaBilletesestacion '$lc_datos[0]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[5]', $lc_datos[4], $lc_datos[1], $lc_datos[6]";
                return $this->fn_ejecutarquery($lc_query);

            case 'auditoriaEfectivo':
                $lc_query = "EXECUTE config.IAE_auditoriaefectivo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], $lc_datos[3]";
                return $this->fn_ejecutarquery($lc_query);

            case 'grabaArqueo':
                $lc_query = "EXECUTE config.IAE_grabaarqueo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], '$lc_datos[6]', $lc_datos[7],$lc_datos[8], $lc_datos[9], $lc_datos[10], $lc_datos[11], $lc_datos[12]";
                return $this->fn_ejecutarquery($lc_query);

            case 'grabaarqueotarjeta':
                $lc_query = "EXECUTE config.IAE_grabaarqueo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], '$lc_datos[6]', $lc_datos[7], $lc_datos[8], $lc_datos[9], $lc_datos[10], $lc_datos[11], $lc_datos[12]";
                return $this->fn_ejecutarquery($lc_query);

            case 'auditoriaTarjeta':
                $lc_query = "EXECUTE config.IAE_auditoriatarjeta '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2],$lc_datos[3], '$lc_datos[4]'";
                return $this->fn_ejecutarquery($lc_query);

            case 'actualizaCajeroMotivo':
                $lc_query = "EXECUTE config.IAE_actualizacajeromotivodescuadre '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], '$lc_datos[5]', '$lc_datos[6]'";
                return $this->fn_ejecutarquery($lc_query);

            case 'auditoriaCajero':
                $lc_query = "EXECUTE config.IAE_actualizacajeromotivodescuadre '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], '$lc_datos[5]', '$lc_datos[6]'";
                return $this->fn_ejecutarquery($lc_query);

            case 'actualizaCajero':
                $lc_query = "EXECUTE config.IAE_actualizacajeromotivo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', '$lc_datos[4]'";
                return $this->fn_ejecutarquery($lc_query);

            case 'imprimeDesmontadoCajero':
                $lc_query = "EXECUTE config.IAE_inserta_canalMovimiento_desmontadoCajero '$lc_datos[0]','$lc_datos[1]', '$lc_datos[2]'";
                return $this->fn_ejecutarquery($lc_query);

            case 'grabaarqueoformapago':
                $lc_query = "EXECUTE config.IAE_grabaarqueo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], '$lc_datos[6]', $lc_datos[7],$lc_datos[8], $lc_datos[9], $lc_datos[10], $lc_datos[11], $lc_datos[12]";
                return $this->fn_ejecutarquery($lc_query);

            case 'eliminaFormasPagoAgregadas':
                $lc_query = "EXECUTE config.IAE_grabaarqueo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], '$lc_datos[6]', $lc_datos[7], $lc_datos[8], $lc_datos[9], $lc_datos[10], $lc_datos[11], $lc_datos[12]";
                return $this->fn_ejecutarquery($lc_query);

            case 'eliminaBilletes':
                $lc_query = "EXECUTE config.IAE_eliminabilletes '$lc_datos[0]', '$lc_datos[1]'";
                return $this->fn_ejecutarquery($lc_query);

            case 'DesmontadoDirecto':
                $lc_query = "EXECUTE config.IAE_desmontadoDirecto '$lc_datos[0]','$lc_datos[1]',$lc_datos[2],'$lc_datos[3]', '$lc_datos[4]'";
                return $this->fn_ejecutarquery($lc_query);

            case 'actualizarValorDeclarado':
                $lc_query = "EXECUTE config.IAE_ActualizaValoresDeclarados '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]',$lc_datos[4],$lc_datos[5]";
                return $this->fn_ejecutarquery($lc_query);
            
        }
    }       
    
    public function validar_eventos_restaurante($accion,$cadena,$restaurante){
        $lc_query = "EXECUTE interface.USP_Venta_Por_Eventos $accion,$cadena,$restaurante";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(                    
                    "es_eventos" => utf8_encode($row['es_eventos'])
                );
            }
        }
        return json_encode($this->lc_regs);     
    }     
    public function validar_periodo($periodo){
        $lc_query = "EXECUTE interface.USP_existe_cabecera_factura $periodo";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(                    
                    "existe" => utf8_encode($row['existe'])
                );
            }
        }
        return json_encode($this->lc_regs);     
    }
    public function host_local(){
        $lc_query = "EXECUTE interface.USP_Venta_Por_Eventos_ServicioEventos 2,0,0";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(                    
                    "host" => utf8_encode($row['host'])
                );
            }

        }
        return json_encode($this->lc_regs);     
    }
    public function datos_conexion(){
        $host=$_SESSION["lc_host"]; 
        $database=$_SESSION["Database"]; 
        $username=$_SESSION["UID"];
        $password=$_SESSION["PWD"];
        $this->lc_regs[]=array('host'=>utf8_encode($host),
                        'database'=>utf8_encode($database),
                        'username'=>utf8_encode($username),
                        'password'=>utf8_encode($password));
        return json_encode($this->lc_regs);     
    }

    public function datos_telegram($dato){
        $lc_query = "EXECUTE interface.USP_Datos_Telegram $dato";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(                    
                    "datos" => utf8_encode($row['datos'])
                );
            }
        }
        return json_encode($this->lc_regs);        
    }

    public function eliminarRegistroCajaChica($datos){
        $lc_query = "EXECUTE [seguridad].[IAE_eliminar_registro_caja_chica] '$datos[cod_cajero]','$datos[fecha]',$_SESSION[rstId],'$_SESSION[usuarioId]','$datos[idControlEstacion]'";
        $this->fn_ejecutarquery($lc_query);
        return json_encode(array('estado'=>'ok'));        
    }
}