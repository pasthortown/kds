<?php

///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: JOSE FERNANDEZ///////////////////////////////////
///////////DESCRIPCION: PANTALLA DE DEPOSITOS /////////////////////////////
////////////////TABLAS: BILLTE_ESTACION, ARQUEO_CAJA///////////////////////
////////FECHA CREACION: 18-03-2016/////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////


class depositos extends sql {

    //private $lc_regs;
    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }

    //funcion que permite armar la sentencia sql de consulta

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {

            case 'validaPeriodosAbiertos':
                $lc_query = "EXECUTE [config].[USP_validaDepositosAbiertosDepositos] '$lc_datos[0]'";

                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['valida'] = $row["valida"];
                        $this->lc_regs['mensaje'] = $row["mensaje"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'insertaNuevoDeposito':
                $lc_query = "EXECUTE [config].[IAE_adminDepositos] '$lc_datos[0]','$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]',$lc_datos[8],'$lc_datos[9]','$lc_datos[10]'";

                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['idDepositos'] = $row["idDepositos"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cargaTotalesDepositoAModificar':
                $lc_query = "EXECUTE [config].[USP_cargaTotalesDepositoAModificar] $lc_datos[0],'$lc_datos[1]', '$lc_datos[2]'";

                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['totalDeposito'] = $row["totalDeposito"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cargaTotalesDepositoNuevo':
                $lc_query = "EXECUTE [config].[USP_cargaTotalesDepositoAModificar] $lc_datos[0],'$lc_datos[1]', '$lc_datos[2]'";

                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['totalDeposito'] = $row["totalDeposito"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaExisteArqueo':
                $lc_query = "EXECUTE [config].[USP_consultaDetalleMontoDepositos] $lc_datos[0],$lc_datos[1], '$lc_datos[2]', '$lc_datos[3]'";

                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['existe'] = $row["existe"];
                        $this->lc_regs['mensaje'] = $row["mensaje"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cargaAjusteModificar':
                $lc_query = "EXECUTE [config].[USP_consultaDetalleMontoDepositos] $lc_datos[0],$lc_datos[3], '$lc_datos[1]', '$lc_datos[2]'";

                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['montoActual'] = $row["montoActual"];
                        $this->lc_regs['signo'] = $row["signo"];
                        $this->lc_regs['concepto'] = $row["concepto"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'asientaDeposito':
                $lc_query = "EXECUTE [config].[IAE_adminDepositos] '$lc_datos[0]','$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]',$lc_datos[8],'$lc_datos[9]','$lc_datos[10]'";

                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['idDepositos'] = $row["idDepositos"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case "cargarBotonesDias":
                $lc_sql = "EXECUTE config.USP_botonesDiasDesasignarCajero $lc_datos[0], '$lc_datos[1]', $lc_datos[2]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("id_dias" => $row['id_dias'],
                            "dias" => utf8_encode(trim($row['dias'])));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargaComboDepositos":
                $lc_sql = "EXECUTE [config].[USP_consultaDetalleMontoDepositos] $lc_datos[0], $lc_datos[1], '$lc_datos[2]','$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("id" => $row['id'],
                            "descripcion" => utf8_encode(trim($row['descripcion'])));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargaConceptosAjuste":
                $lc_sql = "EXECUTE [config].[USP_cargaConceptosAjuste] ";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("id" => $row['id'],
                            "concepto" => utf8_encode(trim($row['concepto'])));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarfechas":
                $lc_sql = "EXECUTE config.USP_traeFechasPeriodo $lc_datos[0], $lc_datos[1], $lc_datos[2], $lc_datos[3]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("prd_id" => $row['prd_id'],
                            "prd_fechaapertura" => $row['prd_fechaapertura'],
                            "prd_usuarioapertura" => $row['prd_usuarioapertura'],
                            "rst_id" => $row['rst_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargaCabeceraDeposito":
                $lc_sql = "EXECUTE [config].[USP_cargaDepositos]  $lc_datos[0],'$lc_datos[1]','$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['IDDepositos'] = $row["IDDepositos"];
                        $this->lc_regs['numeroDeReferencia'] = $row["numeroDeReferencia"];
                        $this->lc_regs['numeroDePapeleta'] = $row["numeroDePapeleta"];
                        $this->lc_regs['fechaDeDeposito'] = $row["fechaDeDeposito"];
                        $this->lc_regs['IDPeriodo'] = $row["IDPeriodo"];
                        $this->lc_regs['arc_valor'] = $row["arc_valor"];
                        $this->lc_regs['usuario'] = $row["usuario"];
                        $this->lc_regs['monedas'] = $row["monedas"];
                        $this->lc_regs['viaDeposito'] = $row["viaDeposito"];
                        $this->lc_regs['comentario'] = utf8_encode($row["comentario"]);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargaDepositos":
                $lc_sql = "EXECUTE [config].[USP_cargaDepositos] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDDepositos" => $row['IDDepositos'],
                            "numeroDeReferencia" => $row['numeroDeReferencia'],
                            "numeroDePapeleta" => $row['numeroDePapeleta'],
                            "fechaDeDeposito" => $row['fechaDeDeposito'],
                            "IDPeriodo" => $row['IDPeriodo'],
                            "arc_valor" => $row['arc_valor']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'formasPagoInactivo':
                $lc_query = "EXECUTE config.USP_desmontadoCajeroAdministracion $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {

                        $this->lc_regs[] = array("fmp_id" => $row['fmp_id'],
                            "fmp_descripcion" => $row['fmp_descripcion'],
                            "retiro_efectivo" => $row['retiro_efectivo'],
                            "Transacciones" => $row['Transacciones'],
                            "arc_valor" => $row['arc_valor'],
                            "fpf_total_pagar" => $row['fpf_total_pagar']);
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

                        $this->lc_regs[] = array("retiro_efectivo" => $row['retiro_efectivo'],
                            "arc_numero_transacciones" => $row['arc_numero_transacciones'],
                            "arc_valor" => $row['arc_valor'],
                            "fpf_total_pagar" => $row['fpf_total_pagar'],
                            "diferencia" => $row['diferencia']);
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

                        $this->lc_regs[] = array("fmp_descripcion" => $row['fmp_descripcion'],
                            "retiro_efectivo" => $row['retiro_efectivo'],
                            "numero_transacciones" => $row['numero_transacciones'],
                            "cfac_total" => $row['cfac_total']);
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

                        $this->lc_regs[] = array("retiro_efectivo" => $row['retiro_efectivo'],
                            "numero_transacciones" => $row['numero_transacciones'],
                            "cfac_total" => $row['cfac_total']);
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

            case 'consultaDetalleDepositoModificado':
                $lc_query = "EXECUTE [config].[USP_consultaDetalleMontoDepositos] $lc_datos[0], $lc_datos[2], '$lc_datos[1]', '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {

                        $this->lc_regs[] = array("fmp_id" => $row['fmp_id'], "fmp_descripcion" => $row['fmp_descripcion'], "montoActual" => $row['montoActual']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaformaPago':
                $lc_query = "EXECUTE [config].[USP_consultaDetalleMontoDepositos] $lc_datos[0], $lc_datos[2], '$lc_datos[1]', '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {

                        $this->lc_regs[] = array("fmp_id" => $row['fmp_id'], "fmp_descripcion" => $row['fmp_descripcion'], "montoActual" => $row['montoActual'], "montoCalculado" => $row['montoCalculado'], "IDPeriodo" => $row['IDPeriodo']);
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
                $lc_query = "EXECUTE  [config].[USP_consultabilletesDepositos] $lc_datos[0], '$lc_datos[2]', '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        //str_replace(' ', '.', 'Kevin van Zonneveld');
                        $this->lc_regs[] = array("btd_id" => $row['btd_id'], "btd_Valor" => $row['btd_Valor'], "btd_Descripcion" => $row['btd_Descripcion'], "bte_cantidad" => $row['bte_cantidad'], "valorIngresado" => $row['valorIngresado'], "bte_total" => $row['bte_total'], "btd_Tipo" => $row['btd_Tipo']);
                    }//
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaBilletesModificados':
                $lc_query = "EXECUTE [config].[USP_consultabilletesDepositos] $lc_datos[0], '$lc_datos[1]', '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        //str_replace(' ', '.', 'Kevin van Zonneveld');
                        $this->lc_regs[] = array("btd_id" => $row['btd_id'], "btd_Valor" => $row['btd_Valor'], "btd_Descripcion" => $row['btd_Descripcion'], "bte_cantidad" => $row['bte_cantidad'], "valorIngresado" => $row['valorIngresado'], "bte_total" => $row['bte_total'], "btd_Tipo" => $row['btd_Tipo']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaidBilletes':
                $lc_query = "EXECUTE config.USP_consultaBilletes $lc_datos[0], '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]'";
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
                        $this->lc_regs[] = array("fmp_id" => $row['fmp_id'], "fmp_descripcion" => $row['fmp_descripcion'], "fpf_total_pagar" => $row['fpf_total_pagar'], "Transacciones" => $row['Transacciones'], "ctrc_id" => $row['ctrc_id'], "arc_valor" => $row['arc_valor'], "diferencia" => $row['diferencia']);
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
                        //$this->lc_regs[] = array("totalModificado"=>$row['totalModificado']);						 
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaTarjeta':
                $lc_query = "EXECUTE [config].[USP_consultaCheques] $lc_datos[0], '$lc_datos[2]', '$lc_datos[1]', '$lc_datos[3]','$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("total" => $row['total'], "fmp_descripcion" => $row['fmp_descripcion']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaformaPagoModificadoTarjeta':
                $lc_query = "EXECUTE config.USP_formaPagoModificadoTarjeta '$lc_datos[0]','$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fmp_id" => $row['fmp_id'], "fmp_descripcion" => trim($row['fmp_descripcion']), "fpf_total_pagar" => $row['fpf_total_pagar'], "Transacciones" => $row['Transacciones'], "ctrc_id" => $row['ctrc_id'], "arc_valor" => $row['arc_valor'], "diferencia" => $row['diferencia']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'totalesIngresados':
                $lc_query = "EXECUTE [config].[USP_totalesingresadosDepositos] '$lc_datos[0]',$lc_datos[1], $lc_datos[2], '$lc_datos[3]'";

                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fmp_id" => $row['fmp_id'], "arc_valor" => $row['arc_valor'], "fmp_descripcion" => $row['fmp_descripcion']);
                        //$this->lc_regs[] = array("totalModificado"=>$row['totalModificado']);						 
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
                        $this->lc_regs['totalesPos'] = $row["totalesPos"];
                        //$this->lc_regs[] = array("totalModificado"=>$row['totalModificado']);						 
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

                        $this->lc_regs[] = array("fmp_id" => $row['fmp_id'],
                            "fmp_descripcion" => $row['fmp_descripcion']);
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
                $lc_query = "EXECUTE config.USP_validaretirofondo $lc_datos[0], '$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['retirofondo'] = $row["retirofondo"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaCupones':
                $lc_query = "EXECUTE config.USP_obtieneformapagoestacionusuario $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {

                        $this->lc_regs[] = array("prd_id" => $row['prd_id'], "fmp_descripcion" => $row['fmp_descripcion'], "est_id" => $row['est_id'], "Transacciones" => $row['Transacciones'], "mensaje" => $row['mensaje']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;
        }
    }

    public function fn_ejecutar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case 'grabaBilletes':
                $lc_query = "EXEC config.IAE_grabaBilleteDepositos '$lc_datos[0]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[5]', $lc_datos[4], $lc_datos[1], $lc_datos[6]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'grabaBilletesDirecto':
                $lc_query = "EXEC [config].[IAE_grabaBilleteDirectoDepositos] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', $lc_datos[4]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'asientaDepositoModificado':
                $lc_query = "EXEC config.IAE_asientaDepositoModificado '$lc_datos[0]', $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]', $lc_datos[7], '$lc_datos[8]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'auditoriaEfectivo':
                $lc_query = "EXECUTE config.IAE_auditoriaefectivo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], $lc_datos[3]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'grabaArqueo':
                $lc_query = "EXECUTE [config].[IAE_grabaarqueoDepositos] '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], '$lc_datos[6]', $lc_datos[7],'+', '01'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'grabaarqueotarjeta':
                $lc_query = "EXECUTE [config].[IAE_grabaarqueoDepositos] '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], '$lc_datos[6]', $lc_datos[7],'+', '01'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'auditoriaTarjeta':
                $lc_query = "EXECUTE config.IAE_auditoriatarjeta '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2],$lc_datos[3], '$lc_datos[4]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'actualizaCajeroMotivo':
                $lc_query = "EXECUTE config.IAE_actualizacajeromotivodescuadre '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], '$lc_datos[5]', '$lc_datos[6]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'auditoriaCajero':
                $lc_query = "EXECUTE config.IAE_actualizacajeromotivodescuadre '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], '$lc_datos[5]', '$lc_datos[6]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'actualizaCajero':
                $lc_query = "EXECUTE config.IAE_actualizacajeromotivo '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', '$lc_datos[4]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'imprimeDesmontadoCajero':
                $lc_query = "EXECUTE config.IAE_inserta_canalMovimiento_desmontadoCajero '$lc_datos[0]','$lc_datos[1]', '$lc_datos[2]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'grabaarqueoAjuste':
                $lc_query = "EXECUTE [config].[IAE_grabaarqueoDepositos] '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], '$lc_datos[6]', $lc_datos[7],'$lc_datos[8]','$lc_datos[9]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'eliminaFormasPagoAgregadas':
                $lc_query = "EXECUTE [config].[IAE_grabaarqueoDepositos] '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], '$lc_datos[6]', $lc_datos[7],'+','01'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'eliminaAjusteAgregado':
                $lc_query = "EXECUTE [config].[IAE_adminAjusteDeposito] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            /*
            case 'grabaarqueoformapago':
                $lc_query ="EXECUTE c[config].[IAE_grabaarqueoDepositos] '$lc_datos[0]', '$lc_datos[1]', $lc_datos[2], '$lc_datos[3]', $lc_datos[4], $lc_datos[5], '$lc_datos[6]', $lc_datos[7]";
                return $this->fn_ejecutarquery($lc_query);

            break;
             */

            case 'eliminaBilletes':
                $lc_query = "EXECUTE config.IAE_eliminabilletes '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'DesmontadoDirecto':
                $lc_query = "EXECUTE config.IAE_desmontadoDirecto '$lc_datos[0]','$lc_datos[1]',$lc_datos[2],'$lc_datos[3]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };
        }
    }

}
