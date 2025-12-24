<?php

/////////////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR:     JOSE FERNANDEZ /////////////////////////////////////////
///////DESCRIPCION:          Archivo que contiene los queries del modulo Corte Caja /
///////TABLAS:               ARQUEO_CAJA,BILLETE_ESTACION, //////////////////////////
/////////////////////////////CONTROL_ESTACION,ESTACION //////////////////////////////
/////////////////////////////BILLETE_DENOMINACION ///////////////////////////////////
////////FECHA CREACION:      20/12/2013 /////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR:       JIMMY CAZARO ///////////////////////////////////////////
///////DESCRIPCION:          Para recuperar las mesas y las cuentas /////////////////
////////TABLAS: /////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR:       JUAN ESTEBAN CANELOS ///////////////////////////////////
///////FECHA MODIFICACION:   26/03/2018 /////////////////////////////////////////////
///////DESCRIPCION:          Validación restaurantes 24 horas ///////////////////////
/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR:        XAVIER AUCANSHALA P. //////////////////////////////////
///////FECHA MODIFICACION:    15/10/2019 ////////////////////////////////////////////
///////DESCRIPCION:           Configuraciones y Desmontar Estacion Pickup ///////////
/////////////////////////////////////////////////////////////////////////////////////

class corteCaja extends sql
{
    private $lc_regs;

    public function fn_consultar($lc_opcion, $lc_datos)
    {
        switch ($lc_opcion) {
            case 'consultaEstacion':
                $lc_query = "EXEC [seguridad].[USP_CC_ConsultaEmpleadoAsignado] '$lc_datos[0]', $lc_datos[1], '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "usr_usuario" => $row['usr_usuario'],
                            "est_id" => $row['est_id'],
                            "ctrc_id" => $row['ctrc_id'],
                            "usr_id" => $row['usr_id'],
                            "usr_usuario" => $row['usr_usuario'],
                            "desmontado" => $row['desmontado']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;
            case 'consultaMotorizados':
                $lc_query = "EXEC [dbo].[App_cargar_motorizado_periodo] $lc_datos[0], $lc_datos[1], '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "idMotorizado" => $row['idMotorizado'],
                            "motorizado" => utf8_encode($row['motorizado']),
                            "documento" => $row['documento'],
                            "empresa" => $row['empresa'],
                            "tipo" => $row['tipo'],
                            "total" => $row['total'],
                            "maximo_ordenes" => $row['maximo_ordenes'],
                            "telefono" => $row['telefono'],
                            "estado" => $row['estado']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;
            case 'devuelveControlEstacion':
                $lc_query = "EXEC [dbo].[ControlEstacionDeUnPeriodo] '$lc_datos'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "IDControlEstacion" => $row['IDControlEstacion'],
                            "IDEstacion" => utf8_encode($row['IDEstacion']),
                            "ctrc_fecha_inicio" => $row['ctrc_fecha_salida'],
                            "rst_id" => $row['rst_id'],
                            "rst_localizacion" => $row['rst_localizacion'],
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                //var_dump($lc_datos);

                $this->fn_liberarecurso();
                break;
            case 'devuelveTotalVentaPorEstacion':
                $lc_query = "EXEC [dbo].[CalculaTotalVentaDeEstacion] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "total" => $row['total'],

                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                //var_dump($lc_datos);

                $this->fn_liberarecurso();
                break;

            case 'politicaControlCajaActiva':
                $lc_query = "exec dbo.ControlSirPorPoliticaActivaRestaurante'$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "variableB" => utf8_encode($row['variableB']),
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                //var_dump($lc_datos);

                $this->fn_liberarecurso();
                break;

            case 'comandasMotorizados':

                $data = "'" . implode("','", $lc_datos) . "'";
                $lc_query = "select count(*) numeroComandas ,mt.empresa_motorolo, mt.nombres ,mt.apellidos ,ca.IDMotorolo,mt.TipoMotorolo from Cabecera_App as ca
                inner join Motorolo as mt on mt.IDMotorolo = ca.IDMotorolo
                where ca.IDMotorolo in (" . $data . ")
                and estado = 'entregado' 
                and CONVERT(date,fecha_Pedido)  = '" . date("Ymd") . "'
                group by mt.empresa_motorolo,mt.nombres, mt.apellidos,ca.IDMotorolo,mt.TipoMotorolo";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "IDMotorolo" => $row['IDMotorolo'],
                            "numeroComandas" => $row['numeroComandas'],
                            "empresaMotorolo" => utf8_encode($row['empresa_motorolo']),
                            "nombres" => $row['nombres'],
                            "apellidos" => $row['apellidos'],
                            "tipoMotorolo" => $row['TipoMotorolo']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;
            case 'pedidosMotorizado':

                $IdMotorolo = "'" . $lc_datos . "'";
                $lc_query = "select cod_cabeceraApp,codigo_app,medio,total_Factura from Cabecera_App 
                where IDMotorolo =" . $IdMotorolo . "
                and estado = 'entregado' 
                and CONVERT(date,fecha_Pedido)  = '" . date("Ymd") . "'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "cod_cabeceraApp" => $row['cod_cabeceraApp'],
                            "codigo_app" => $row['codigo_app'],
                            "medio" => $row['medio'],
                            "total_Factura" => $row['total_Factura']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;
            case 'consultaPedidosApp':
                $lc_query = "EXEC [dbo].[App_cargar_pedidos_app] $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', ''";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "codigo" => $row['codigo'],
                            "cliente" => utf8_encode($row['cliente']),
                            "estado" => $row['estado'],
                            "medio" => $row['medio'],
                            "total" => intval($row['total'])
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'validaEstaciondesmontado':
                $lc_query = "EXEC [seguridad].[USP_CC_ConsultaEmpleadoAsignado] '$lc_datos[0]', $lc_datos[1], '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("desmontado" => $row['desmontado']);
                    }
                    //$this->retomarCuentaAbiertalc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;
                // Cambios PickUP
            case 'PickupConfiguracionEstacion':
                $lc_query = "EXEC config.PICKUP_DatosDeConfiguracionEstacion '$lc_datos[0]', '$lc_datos[1]'";
                //todo:EXEC config.PICKUP_DatosDeConfiguracionEstacion '8', '27' desmontar empleado
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "ActivoEstacion_Pickup" => $row['aplicaPickup'],
                            "ActivoCadena_Pickup"   => $row['aplicaPickupCadena'],
                            "IdEstacion_Pickup"     => $row['idEstacion'],
                            "IP_Pickup"             => $row['EstIpMaxpoint'],
                            "Nombre_Pickup"         => $row['NombreCajero'],
                            "Clave_Pickup"          => $row['NombreCajero']
                        );
                    }
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
                // fin Cambios PickUP              

            case "consultarMesaOrden":
                $lc_query = "EXEC seguridad.USP_CC_ConsultaDetalleCuentas $lc_datos[0], $lc_datos[1], '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("mesa_id" => $row['mesa_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

                /* ----------------------------------------------------------------------------------------------------
              Función Retomar mesa_id y odp_id de una factura
              ----------------------------------------------------------------------------------------------------- */
            case "retomarCuentaAbierta":
                $lc_query = "seguridad.USP_CC_ConsultaDetalleCuentas $lc_datos[0], '$lc_datos[1]', $lc_datos[2]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "odp_id" => trim($row['odp_id']),
                            "mesa_id" => $row['mesa_id'],
                            "dop_cuenta" => $row['dop_cuenta'],
                            "es_agregador" => $row['es_agregador']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'consultadetalleCuenta':
                $lc_query = "seguridad.USP_CC_ConsultaDetalleCuentas $lc_datos[0], '$lc_datos[1]', $lc_datos[2]";
                if ($this->fn_ejecutarquery($lc_query)) {

                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "dtfac_cantidad" => $row['dtfac_cantidad'],
                            "dtfac_precio_unitario" => round(floatval($row['dtfac_precio_unitario']), 2),
                            "plu_descripcion" => $row['plu_descripcion'],
                            "subtotal" => round(floatval($row['subtotal']), 2)
                        );
                    }

                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();

                break;

            case 'consultatotalesCuenta':
                $lc_query = "seguridad.USP_CC_ConsultaDetalleCuentas $lc_datos[0], $lc_datos[1], $lc_datos[2]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['precioNeto'] = round($row["precioNeto"], 2);
                        $this->lc_regs['IVA'] = round($row["IVA"], 2);
                        $this->lc_regs['total'] = round($row["total"], 2);
                        /* $this->lc_regs[] = array("precioNeto"=>round($row['precioNeto'],2),"IVA"=>round($row['IVA'],2),"total"=>$row['total']); */
                    }

                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();

                break;

            case 'consultaformaPago':
                $lc_query = "EXEC [seguridad].[USP_CC_ConsultaFormaPagoEfectivo] $lc_datos[2],'$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "fmp_id" => $row['fmp_id'],
                            "fmp_descripcion" => $row['fmp_descripcion'],
                            "fpf_total_pagar" => $row['fpf_total_pagar'],
                            "Transacciones" => $row['Transacciones'],
                            "ctrc_id" => $row['ctrc_id']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();

                break;


            case 'consultaformaPagoModificado':
                $lc_query = "EXEC [seguridad].[USP_CC_FormaPagoModificadoEfectivo] $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3]";
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

            case 'consultaformaPagoModificadoTarjeta':
                $lc_query = "EXEC [seguridad].[USP_CC_FormaPagoModificadoTarjeta] $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3]";
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

            case 'calculatotalesMofificados':
                $lc_query = "	EXEC [seguridad].[USP_CC_CalculaTotalesMofificados] $lc_datos[0],$lc_datos[1],$lc_datos[2]";

                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['totalModificado'] = $row["totalModificado"];
                    }

                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'totalesPos':
                $lc_query = "EXEC [seguridad].[UPS_CC_TotalesPos] $lc_datos[0],$lc_datos[1],$lc_datos[2]";

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

            case 'totalesIngresados':
                $lc_query = "EXEC [seguridad].[USP_CC_TotalesIngresados] $lc_datos[0],$lc_datos[1],$lc_datos[2]";

                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "fmp_id" => $row['fmp_id'],
                            "arc_valor" => $row['arc_valor'],
                            "fmp_descripcion" => $row['fmp_descripcion']
                        );
                        //$this->lc_regs[] = array("totalModificado"=>$row['totalModificado']);						 
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaBilletes':
                $lc_query = "select bd.btd_id,bd.btd_Valor,bd.btd_Descripcion
					   from Billete_Denominacion as bd
					   WHERE bd.std_id=23
					   group by bd.btd_id,bd.btd_Descripcion,bd.btd_Valor
					   order by 2 desc";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "btd_id" => $row['btd_id'],
                            "btd_Valor" => $row['btd_Valor'],
                            "btd_Descripcion" => $row['btd_Descripcion']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaBilletesModificados':
                $lc_query = "EXEC [seguridad].[USP_CC_CosultaBilletesModificados] $lc_datos[0],$lc_datos[1]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        //str_replace(' ', '.', 'Kevin van Zonneveld');
                        $this->lc_regs[] = array(
                            "btd_id" => $row['btd_id'],
                            "btd_Valor" => $row['btd_Valor'],
                            "btd_Descripcion" => $row['btd_Descripcion'],
                            "bte_cantidad" => $row['bte_cantidad'],
                            "bte_total" => $row['bte_total']
                        );
                    } //
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaTarjeta':
                $lc_query = "EXEC [seguridad].[USP_CC_totalFormaPagoTarjeta] $lc_datos[0],$lc_datos[1],$lc_datos[2]";
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

            case 'consultatotalEstacion':
                $lc_query = "EXEC cc_consultatotalEstacion $lc_datos[0],$lc_datos[1]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['total'] = $row['total'];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'consultatotalformaPago':
                $lc_query = "EXEC cc_totalFormaPago $lc_datos[2],$lc_datos[0],$lc_datos[1]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['total'] = $row['total'];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

                //CUENTAS ABIERTAS
            case 'consultaMesa':
                $lc_query = "EXEC [seguridad].[USP_CC_ConsultaCuentasAbiertas] $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]', $lc_datos[4]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            'mesa_descripcion' => $row['mesa_descripcion'],
                            'odp_id' => $row['odp_id'],
                            'total' => round($row['total'], 2),
                            'est_id' => $row['est_id'],
                            'cuenta' => $row['cuenta'],
                            'nombre_estacion' => $row['nombre_estacion'],
                            "usr_usuario" => $row['usr_usuario'],
                            "mesa_descripcion" => $row['mesa_descripcion']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

                //CUENTAS ABIERTAS
            case 'consultaMesaFS':
                $lc_query = "EXEC [seguridad].[USP_CC_ConsultaCuentasAbiertas_FullServices] $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "mesa_descripcion" => $row['mesa_descripcion'],
                            "odp_id" => $row['odp_id'],
                            "total" => round($row['total'], 2),
                            "est_id" => $row['est_id'],
                            "nombre_estacion" => $row['nombre_estacion'],
                            "IDMesa" => $row['IDMesa'],
                            "std_descripcion" => $row['std_descripcion'],
                            "odp_fecha_creacion" => $row["odp_fecha_creacion"]
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultadetalleMesa':
                $lc_query = "EXEC [seguridad].[USP_CC_ConsultaCuentasAbiertas] $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]', $lc_datos[4]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "dop_cantidad" => $row['dop_cantidad'],
                            "est_id" => $row['est_id'],
                            "dop_precio" => round($row['dop_precio'], 2),
                            "plu_descripcion" => utf8_encode($row['plu_descripcion']),
                            "subtotal" => round($row['subtotal'], 2)
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultatotalesMesa':
                $lc_query = "EXEC [seguridad].[USP_CC_ConsultaCuentasAbiertas] $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]', $lc_datos[4]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['precioNeto'] = round($row["precioNeto"], 2);
                        $this->lc_regs['IVA'] = round($row["IVA"], 2);
                        $this->lc_regs['total'] = floatval($row["total"]);
                        $this->lc_regs['odp_observacion'] = utf8_encode($row["observacion"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'validacuenta':
                $lc_query = "EXEC [seguridad].[USP_CC_ConsultaCuentasAbiertas] $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]', $lc_datos[4]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("valida" => $row['valida']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'limpiarcuentacero':
                $lc_query = "EXEC [seguridad].[limpiarCuentasAbiertasYporFacturar] '$lc_datos[0]'";
                
                try {
                    if ($this->fn_ejecutarquery($lc_query)) {
                        $arr['limpiarcuentas'] = 1;
                    } else {
                        $arr['limpiarcuentas'] = 0;
                    }
                    $arr['limpiarcuentas'] = 1;
                } catch (Exception $e) {
                    $arr['limpiarcuentas'] = 0;
                }

                return json_encode($arr);

                break;

                //CUENTAS POR FACTURAR
            case 'consultaCuenta':
                $lc_query = "	EXEC seguridad.USP_CC_cuentasAbiertas $lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            'cfac_id' => $row['cfac_id'],
                            'mesa_id' => $row['mesa_id'],
                            'est_id' => $row['est_id'],
                            'cfac_total' => round($row['cfac_total'], 2),
                            'nombre_estacion' => $row['nombre_estacion'],
                            'cualquiera_estacion' => $row['cualquiera_estacion'],
                            'usr_usuario' => $row['usr_usuario'],
                            'mesa_descripcion' => $row['mesa_descripcion'],
                            'IDCabeceraOrdenPedido' => $row['IDCabeceraOrdenPedido'],
                            'dop_cuenta' => $row['dop_cuenta']
                            
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultaidBilletes':
                $lc_query = "select top($lc_datos[0])bte_id from Billete_Estacion ORDER BY  bte_id DESC";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("bte_id" => $row['bte_id']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'impresion_FindelDia':
                $lc_query = "EXEC [seguridad].[USP_impresion_dinamica_FindelDia] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]'";
                return $this->fn_ejecutarquery($lc_query);
            
                case 'consultaEstadoOrdenPedido':


                $lc_query = "EXEC [seguridad].[USP_CC_ConsultaEstadoCuentasAbiertas] $lc_datos[0],'$lc_datos[1]'";
                    if ($this->fn_ejecutarquery($lc_query)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array(
                                "IDCabeceraOrdenPedido" => $row['IDCabeceraOrdenPedido'],
                                "odp_total" => $row['odp_total']
                            );
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
            case 'validaRetomaOrdenCualquierEstacion':
                $lc_query = "SELECT ordenes FROM [config].[ColeccionRestaurante_RecuperarDocumento] ($lc_datos[0])";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "retoma_ordenes" => $row['ordenes'],
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'ejecutarCambioIva':
                $lc_query = "EXEC [config].[IAE_EjecucionCambioIva]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['data'] = $row['resp'];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return $this->lc_regs['data'] ;    
                }
                $this->fn_liberarecurso();
                break;

                
        }
    }

    public function fn_ejecutar($lc_opcion, $lc_datos)
    {
        switch ($lc_opcion) {
                // Cambios PickUP
            case 'desmontarEstacionPickup':
                $lc_query = "EXECUTE Pickup_FinVenta $lc_datos[1], $lc_datos[0], '$lc_datos[2]'";
                return $this->fn_ejecutarquery($lc_query);
                //break;
            case 'imprimeDesmontadoCajeroPickUp':
                $lc_query = "EXECUTE seguridad.IAE_inserta_canalMovimiento_desmontadoCajero '$lc_datos[0]','$lc_datos[1]', '$lc_datos[2]'";
                return $this->fn_ejecutarquery($lc_query);
                // Fin Cambios PickUP
            case 'grabaBilletes':
                $lc_query = "EXEC cc_grabaBilletes $lc_datos[1],$lc_datos[2],$lc_datos[4],$lc_datos[3],$lc_datos[0]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'grabaArqueo':
                $lc_query = "EXEC [seguridad].[IAE_Corte_Caja] $lc_datos[0],'$lc_datos[1]',$lc_datos[2],$lc_datos[3],'$lc_datos[4]',$lc_datos[5],$lc_datos[6]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'grabaarqueotarjeta':
                $lc_query = "EXEC [seguridad].[IAE_Corte_Caja] $lc_datos[0],'$lc_datos[1]',$lc_datos[2],$lc_datos[3],'$lc_datos[4]',$lc_datos[5],$lc_datos[6]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'actualizaCajero':
                $lc_query = "EXEC [seguridad].[IAE_Corte_Caja] $lc_datos[0],'$lc_datos[1]',$lc_datos[2],$lc_datos[3],'$lc_datos[4]',$lc_datos[5],$lc_datos[6]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'actualizaCajeroMotivo':
                $lc_query = "EXEC [seguridad].[IAE_Corte_Caja] $lc_datos[0],'$lc_datos[1]',$lc_datos[2],$lc_datos[3],'$lc_datos[4]',$lc_datos[5],$lc_datos[6]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'inserta_canal_desmontado':
                $lc_query = "EXEC cc_inserta_canal_desmontado '$lc_datos[0]',$lc_datos[1],'$lc_datos[2]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'auditoriaEfectivo':
                $lc_query = "EXEC [seguridad].[IAE_Auditoria_CorteCaja] $lc_datos[0],$lc_datos[6],$lc_datos[5],$lc_datos[4],'$lc_datos[7]','$lc_datos[8]','$lc_datos[9]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'auditoriaEfectivoModificado':
                $lc_query = "declare @id int
								set @id=(select MAX(atran_id) from Auditoria_Transaccion)
								if exists(select atran_id from Auditoria_Transaccion where atran_id=@id)
								begin
								update Auditoria_Transaccion set 
								usr_id=$lc_datos[5],rst_id=$lc_datos[4],atran_fechaaudit=GETDATE(),atran_modulo='DESMONTADO DE CAJERO',atran_descripcion='TOTAL INGRESADO EN EFECTIVO: $lc_datos[0] USD',atran_accion='ACTUALIZAR'
								end
								else
									begin
		insert into Auditoria_Transaccion (usr_id,rst_id,atran_fechaaudit,atran_modulo,atran_descripcion,atran_accion) values($lc_datos[5],$lc_datos[4],GETDATE(),'DESMONTADO DE CAJERO','TOTAL INGRESADO EN EFECTIVO: $lc_datos[0] USD','ACTUALIZAR')
									end";
            $result = $this->fn_ejecutarquery($lc_query);
            if ($result){ return true; }else{ return false; };

            case 'auditoriaTarjeta':
                $lc_query = "EXEC [seguridad].[IAE_Auditoria_CorteCaja] $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3],'$lc_datos[4]','$lc_datos[5]','$lc_datos[6]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'auditoriaCancelar':
                $lc_query = "EXEC [seguridad].[IAE_Auditoria_CorteCaja] $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3],'$lc_datos[4]','$lc_datos[5]','$lc_datos[6]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'auditoriaCajero':
                $lc_query = "EXEC [seguridad].[IAE_Auditoria_CorteCaja] $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3],'$lc_datos[4]','$lc_datos[5]','$lc_datos[6]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'auditoriaCajeroMotivo':
                $lc_query = "EXEC [seguridad].[IAE_Auditoria_CorteCaja] $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3],'$lc_datos[4]','$lc_datos[5]','$lc_datos[6]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case "ValidaDescuadre":
                $lc_query = "EXEC seguridad.[USP_CC_ValidaDescuadre] $lc_datos[0],$lc_datos[1],$lc_datos[2]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Confirmar" => $row['Confirmar']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "ValidaRegreso":
                $lc_query = "EXEC [seguridad].[USP_CC_ValidaRegreso] $lc_datos[0],'$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Confirmar" => $row['Confirmar']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'eliminaBilletes':
                $lc_query = "DELETE FROM Billete_Estacion WHERE bte_id=$lc_datos[0]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'eliminaArqueo':
                $lc_query = "[seguridad].[IAE_CC_EliminaArqueo] $lc_datos[0],$lc_datos[1],$lc_datos[2]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'finDia':
                $lc_query = "EXEC [seguridad].[IAE_CC_FinDia] $lc_datos[0],'$lc_datos[1]',$lc_datos[2]";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

                /*
            case 'finDia':
                $lc_query ="EXEC [seguridad].[IAE_CC_FinDia] $lc_datos[0],'$lc_datos[1]',$lc_datos[2]";
                if($this->fn_ejecutarquery($lc_query)){
                    while($row = $this->fn_leerarreglo()){
                        $this->lc_regs[] = array("salir"=>$row['salir']);
                       }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break; */

            case 'grabaCanalCierraSistema':
                $lc_query = "EXEC [facturacion].[IAE_ApagarCaja] $lc_datos[0], '$lc_datos[2]', '$lc_datos[1]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case "validarCreencialesUsuario":
                $lc_query = "EXEC [seguridad].[USP_CC_validar_credenciales_usuario] $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("usr_id" => $row['usr_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'ValidaFondoRetirado':
                $lc_query = "EXEC [seguridad].[USP_Retiro_FondoAsignado] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['valida'] = $row["valida"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case 'InsertcanalmovimientoFindelDia':
                $lc_query = "EXEC [seguridad].[IAE_CanalMovimiento_FindelDia] '$lc_datos[0]','$lc_datos[1]', '$lc_datos[2]'";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'validaFindeDia':
                $lc_query = "EXEC [seguridad].[USP_Valida_FindeDia] $lc_datos[0],'$lc_datos[1]',$lc_datos[2]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "salir" => $row['salir'],
                            "fechaAperturaPeriodo" => $row['fechaAperturaPeriodo'],
                            "horaSalida" => $row['horaSalida']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

                case 'anularMesaOdp':
                    $lc_query = "EXEC [seguridad].[IAE_CC_ActualizaCuentasAbieras] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]', $lc_datos[6]";
                    if ($this->fn_ejecutarquery($lc_query)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array(
                                "resp" => $row['resp'],
                                "mensaje" => $row['mensaje']
                            );
                        }
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                    case 'anularCuentaPorFacturar':
                        $lc_query = "EXEC [seguridad].[IAE_CC_ActualizaCuentasAbieras] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]', $lc_datos[6]";
                        if ($this->fn_ejecutarquery($lc_query)) {
                            while ($row = $this->fn_leerarreglo()) {
                                $this->lc_regs[] = array(
                                    "resp" => $row['resp'],
                                    "mensaje" => $row['mensaje']
                                );
                            }
                        }
                        $this->lc_regs['str'] = $this->fn_numregistro();
                        return json_encode($this->lc_regs);

                case 'actualizaEstacionOdp':
                    $lc_query = "EXEC [seguridad].[IAE_CC_ActualizaCuentasAbieras] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]', $lc_datos[6]";
                    if ($this->fn_ejecutarquery($lc_query)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array(
                                "resp" => $row['resp'],
                                "mensaje" => $row['mensaje']
                            );
                        }
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);                    
        }
    }

    public function valida24Horas($codRest, $fechaAperturaPeriodo)
    {
        $lc_query = "EXEC config.USP_validaPeriodo24Horas " . $codRest . ", '" . $fechaAperturaPeriodo . "'";
        if ($this->fn_ejecutarquery($lc_query)) {
            $row = $this->fn_leerarreglo();
            $this->lc_regs[] = array("valido" => $row['valido']);
        }
        return json_encode($this->lc_regs);
    }

    public function cambiarEstadoEstaciones($codRest, $fechaAperturaPeriodo)
    {
        $lc_query = "EXEC config.IAE_CambiarEstacionesFondoSinConfirmar " . $codRest . ", '" . $fechaAperturaPeriodo . "'";
        if ($this->fn_ejecutarquery($lc_query)) {
            $row = $this->fn_leerarreglo();
            $this->lc_regs[] = array("Respuesta" => $row['Respuesta']);
        }
        return json_encode($this->lc_regs);
    }

    function validarCuentasAbiertas($accion, $id_restaurante, $id_usuario)
    {
        $lc_query = "EXEC [seguridad].[USP_cuentasAbiertas] $accion, $id_restaurante, '$id_usuario'";
        if ($result = $this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['permite_cuantas_abiertas'] = $row["permite_cuantas_abiertas"];
            }

            $this->lc_regs['str'] = $this->fn_numregistro();
        }

        return json_encode($this->lc_regs);
    }

    function App_pedido_pickup_actividad()
    {
        $rst_id = 2;
        $est_ip = $_SESSION['direccionIp'];
        $lc_query = "EXEC App_pedido_pickup_actividad $rst_id,'$est_ip';";
        if ($result = $this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['estado'] = $row['entregadosbit'];
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function validarMotorizadoAsignados($idCadena, $idRestaurante, $idPeriodo)
    {
        $lc_query = "EXEC dbo.App_validar_motorizado_asignados $idCadena, $idRestaurante, '$idPeriodo'";
        if ($result = $this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['estado'] = $row["estado"];
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function validarPendientesApp($idCadena, $idRestaurante, $idPeriodo)
    {
        $lc_query = "EXEC dbo.App_validar_pendientes_app $idCadena, $idRestaurante, '$idPeriodo'";
        if ($result = $this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['estado'] = $row["estado"];
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function  actualizarComandasMotorizados($data)
    {
        $lc_query = "EXEC dbo.App_Transferir_pedido_motorizado '" . $data['idMotorizado'] . "', '" . $data['idPedido'] . "','" . $data['idMotorizadoPedido'] . "'";
        return $this->fn_ejecutarquery($lc_query);
    }

    public function  validarPendienteKiUP($data)
    {
        $lc_query = "EXEC dbo.App_Transferir_pedido_motorizado '" . $data['idMotorizado'] . "', '" . $data['idPedido'] . "','" . $data['idMotorizadoPedido'] . "'";
        return $this->fn_ejecutarquery($lc_query);
    }

    public function añadirComandasMotorizados($data)
    {
        foreach ($data as list($id, $value)) {
            $lc_query = " UPDATE Cabecera_App
           SET transaccion = " . json_decode($value) . "
            where fecha_Pedido = (select max(fecha_Pedido) from Cabecera_App where IDMotorolo ='" . $id . "'" . ")
            and IDMotorolo ='" . $id . "'";
            $response = $this->fn_ejecutarquery($lc_query);
        }
        return $response;
    }

    public function  obtenerUrlSir()
    {
        $lc_query = "EXEC [dbo].[Obtener_Url_Servicios_Sir]";
        if ($result = $this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['url'] = $row["url"];
                $this->lc_regs['activa'] = $row["activa"];
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    public function finDiaControlCuentas($IDUsersPos)
    {
        $consulta = "EXEC [seguridad].[finDiaControlCuentas] '$IDUsersPos'";

        try {
            if ($this->fn_ejecutarquery($consulta)) {
                return 1;
            }
        } catch (Exception $e) {
            //echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        }

        return 0;
    }

    function condicionFacturacionOrdenPedido( $IDCabeceraOrdenPedido )
    {
        $this->lc_regs = array();

        $lc_sql = "EXEC	[pedido].[condicionFacturacionOrdenPedido] '$IDCabeceraOrdenPedido';";        

        if( $this->fn_ejecutarquery( $lc_sql ) ) 
        {
            $row = $this->fn_leerarreglo();

            $this->lc_regs["condicionFOP"] =  array(    "error"                 => $row["error"],
                                                        "errorDescripcion"      => utf8_encode( $row["errorDescripcion"] ),
                                                        "condicion"             => $row["condicion"],
                                                        "condicionDescripcion"  => utf8_encode( $row["condicionDescripcion"] ),
                                                        "promesaPendiente"      => $row["promesaPendiente"],
                                                        "IDFormapagoPromPend"   => $row["IDFormapagoPromPend"],
                                                        "montoPagadoPromPend"   => $row["montoPagadoPromPend"]
                                                    );
                                                    
            $this->lc_regs["str"] = $this->fn_numregistro();
        }
        else 
        {
            $this->lc_regs["str"] = 0;
        }

        return json_encode( $this->lc_regs );
    }
}
