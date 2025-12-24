<?php
class ServicioImpresion extends sql {
    function __construct() {
        parent::__construct();
    }

    public function impresionTransferencia($idcontrolestacion,$user) {
        $lc_sql = "EXEC [impresion].[IAE_TransferenciaVenta_Egreso] '$idcontrolestacion','$user'";
        $this->lc_regs = array();
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    'numeroImpresiones' => $row['numeroImpresiones'],
                    'tipo' => $row['tipo'],
                    'impresora' => utf8_encode($row['impresora']),
                    'formatoXML' => utf8_encode($row['formatoXML']),
                    'jsonData' => utf8_encode($row['jsonData']),
                    'jsonRegistros' => utf8_encode($row['jsonRegistros'])
                );
            }
            return ($this->lc_regs); 
        }
    }
    
    public function impresionFactura($transaccion, $ipEstacion) {
        //$lc_sql = "EXEC [impresion].[IAE_Facturacion] $idRestaurante, $idCadena, '$idEstacion', '$transaccion'";
        $lc_sql = "EXEC [facturacion].[IAE_TipoFacturacion] '$transaccion', '$ipEstacion'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            }
            
            return ($this->lc_regs); 
        }
    }


    public function app_tipo_entrega_inmediata($idRestaurante) {
        $lc_sql = "EXEC [dbo].[app_tipo_entrega_inmediata] '$idRestaurante'";
        $entrega='';
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $entrega=trim($row['ENTREGA']);
            }
            return ($entrega); 
        }
    }

    public function impresionNotaCredito($idRestaurante, $idCadena, $idEstacion, $transaccion, $idUsuario, $ipEstacion) {

        $lc_sql = "EXEC [facturacion].[TRN_impresion_anulacion] $idRestaurante, '$idUsuario', '$transaccion', '$ipEstacion', '$idEstacion'";

        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            }
            
            return ($this->lc_regs); 
        }
    }


    public function impresionReimpresionFactura($idRestaurante, $idCadena, $idEstacion, $transaccion, $idUsuario) {

        $lc_sql = "EXEC [facturacion].[TRN_reimpresion_factura_error] '$transaccion', $idRestaurante,  $idCadena, '$idEstacion', '$idUsuario'";

        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            }
            
            return ($this->lc_regs); 
        }
    }


    public function impresionCambioDatosCliente($idRestaurante, $idCadena, $idEstacion, $transaccion, $accion, $documentoCliente, $idUsuario, $ipEstacionCambioDatos) {
        $lc_sql = "EXEC [facturacion].[TRANSACCIONES_IAE_NotaCreditoCambioDatosCliente] '$accion', '$transaccion', '$idUsuario', $documentoCliente, '$ipEstacionCambioDatos'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            }
            return ($this->lc_regs); 
        }
    }
    
    public function impresionOrdenPedido($idCabeceraOrdenPedido, $idUsuario, $idRestaurante, $cuenta, $guardarOrden, $todas, $origen, $fidelizacion) {
        $lc_sql = "EXEC [pedido].[ORD_impresion_ordenpedido] '$idCabeceraOrdenPedido', '$idUsuario', $idRestaurante, $cuenta, $guardarOrden, $todas, $origen, $fidelizacion";
        $this->lc_regs = array();
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $row["jsonRegistros"] = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $row["jsonRegistros"]);
                $row["jsonData"] = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $row["jsonData"]);
                
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    'jsonRegistros' => utf8_encode($row["jsonRegistros"])
                );
            }

            return ($this->lc_regs); 
        }
    }

    public function impresionReimprimirOrden($transaccion, $idCadena, $idRestaurante, $dop_id, $guardarOrden, $idEstacion) {
        $lc_sql = "EXEC [pedido].[IAE_ImpresionOrdenPedidoFastFoodKioskoFactura] '$transaccion', $idCadena, $idRestaurante, $dop_id, $guardarOrden, '$idEstacion'";
        $this->lc_regs = array();

        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $row["jsonRegistros"] = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $row["jsonRegistros"]);
                $row["jsonData"] = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $row["jsonData"]);

                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            }

            return ($this->lc_regs);
        }
    }
    
    public function impresionPreCuenta($transaccion, $idCadena, $idRestaurante, $dop_cuenta, $idEstacion, $est_ipd, $opcionImpresion, $usuarioIdAdmin) {

        $lc_sql = "EXEC [pedido].[ORD_impresion_precuenta] '$transaccion', '$usuarioIdAdmin', '$est_ipd', $idRestaurante, $dop_cuenta, '$idEstacion', $opcionImpresion";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            }
            
            return ($this->lc_regs); 
        }
    }
    

    public function impresionOrden($idCadena, $idRestaurante,$idControlEstacion,$transaccion) {
        $lc_sql = "EXEC [dbo].[app_impresion_ordenpedido] '$idCadena','$idRestaurante','$idControlEstacion','$transaccion'";
        
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => 1,
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            }
            
            return ($this->lc_regs); 
        }
    }

    public function impresionVoucher($rsaut_id,$Tipo,$est_id,$rst) {
        $lc_sql = "EXEC [impresion].[impresion_Voucher] '$rsaut_id','$Tipo','$est_id','$rst';";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    'numeroImpresiones' => 1,
                    'tipo' => $row['tipo'],
                    'impresora' => utf8_encode(trim($row['impresora'])),
                    'formatoXML' => utf8_encode(trim($row['formatoXML'])),
                    'jsonData' => utf8_encode(trim($row['jsonData'])),
                    'jsonRegistros' => utf8_encode('[{"registrosDesgloseImpuestos":'.trim(utf8_encode($row['jsonRegistros']).'}]'))
                );
            }
            unset($lc_sql);
            return ($this->lc_regs); 
        }
    }

    public function impresionVoucherAnulacionTransaccion($rsaut_id, $Tipo, $est_id, $rst) {
        $lc_sql = "EXEC [impresion].[impresion_Voucher_Anulacion_Transaccion] '$rsaut_id','$Tipo','$est_id','$rst';";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    'numeroImpresiones' => 1,
                    'tipo' => $row['tipo'],
                    'impresora' => utf8_encode(trim($row['impresora'])),
                    'formatoXML' => utf8_encode(trim($row['formatoXML'])),
                    'jsonData' => utf8_encode(trim($row['jsonData'])),
                    'jsonRegistros' => utf8_encode('[{"registrosDesgloseImpuestos":'.trim(utf8_encode($row['jsonRegistros']).'}]'))
                );
            }
            unset($lc_sql);
            return ($this->lc_regs); 
        }
    }

        public function impresionVoucherNo($rsaut_id,$est_id,$rst) {
        $lc_sql = "EXEC [impresion].[impresion_VoucherNo] '$rsaut_id','$est_id','$rst';";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    'numeroImpresiones' => 1,
                    'tipo' => $row['tipo'],
                    'impresora' => utf8_encode(trim($row['impresora'])),
                    'formatoXML' => utf8_encode(trim($row['formatoXML'])),
                    'jsonData' => utf8_encode(trim($row['jsonData'])),
                    'jsonRegistros' => ''
                );
            }
            unset($lc_sql);
            return ($this->lc_regs); 
        }
    }


    public function impresionVoucherNoCancelar($rsaut_id,$est_id,$rst) {
        $lc_sql = "[impresion].[usp_impresion_VoucherCanceladoNoAprobado] '$rsaut_id[codigo_app]',7,'$est_id','$rst','';";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    'numeroImpresiones' => 1,
                    'tipo' => $row['tipo'],
                    'impresora' => utf8_encode(trim($row['impresora'])),
                    'formatoXML' => utf8_encode(trim($row['formatoXML'])),
                    'jsonData' => utf8_encode(trim($row['jsonData'])),
                    'jsonRegistros' => utf8_encode($row["jsonRegistros"])
                );
            }
            unset($lc_sql);
            return ($this->lc_regs); 
        }
    }

    public function impresionPromocionFactura($transaccion, $idUsuario, $idRestaurante, $dop_id) {
        $lc_sql = "EXEC [pedido].[ORD_impresion_promocion_facturacion] '$transaccion', $dop_id";
        $this->lc_regs = array();
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            }
            
            return ($this->lc_regs); 
        }
    }


    public function impresionCreditoEmpresa($transaccion, $frmPagoCredito_id, $frmPagoCredito_numSeg, $frmPagoBilleteCredito, $fctTotalCredito, $prop, $tfpSwtransaccionalCredito, $idUsuario, $cliCredito, $banderaCredito, $opcionFp, $observacion, $documentoClienteAX, $telefonoClienteAx, $direccionClienteAx, $correoClienteAx, $tipoIdentificacionCLienteExt, $nombreCLienteCredito, $tipoCliCredito, $banderaVitality, $valorCampoCodigo) {

        $lc_sql = "EXEC [facturacion].[IAE_insertaFormaPagoCreditoEmpresa] '$transaccion', '$frmPagoCredito_id', $frmPagoCredito_numSeg, $frmPagoBilleteCredito, $fctTotalCredito, $prop, $tfpSwtransaccionalCredito, '$idUsuario', '$cliCredito', '$banderaCredito', '$opcionFp', '$observacion', '$documentoClienteAX', '$telefonoClienteAx', '$direccionClienteAx', '$correoClienteAx', '$tipoIdentificacionCLienteExt', '$nombreCLienteCredito', '$tipoCliCredito', $banderaVitality, '$valorCampoCodigo'";
        $this->lc_regs = array();
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            }
            
            return ($this->lc_regs); 
        }
    }

    public function testImpresion($estacion) {
        $lc_sql = "EXEC [facturacion].[IAE_CanalMovimiento_TestImpresion] '$estacion'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            }
            return ($this->lc_regs); 
        }
    }

    public function impresionRetiros( $accion,$lc_usuarioIdAdmin,$estado_asentado_refectivo,$idUsuario,$efectivo_posCalculado,$valor_retiro_efectivo,$asentarRetiroEfectivo  ) {
        
        $lc_sql = "EXECUTE [seguridad].[IAE_asientaBilletesefectivo]  '$accion','$lc_usuarioIdAdmin','$estado_asentado_refectivo','$idUsuario',$efectivo_posCalculado,$valor_retiro_efectivo,$asentarRetiroEfectivo";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            } 
            return ($this->lc_regs); 
        }
    }

    public function impresionArqueo( $idUsuario,$ctrc_id,$lc_usuarioIdAdmin ) {

        $lc_sql = "EXECUTE [reporte].[ARQUEO_IAE_ArqueoCaja]  '$idUsuario','$ctrc_id','$lc_usuarioIdAdmin'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            } 
            return ($this->lc_regs); 
        }
    }

    public function corteX( $idUsuario,$ctrc_id,$lc_usuarioIdAdmin ) {

        $lc_sql = "EXECUTE [seguridad].[IAE_CanalMovimiento_CorteCajaX]  '$idUsuario','$ctrc_id','$lc_usuarioIdAdmin'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            } 
            return ($this->lc_regs); 
        }
    }

    public function desmontadoCajero( $idUsuario,$ctrc_id,$lc_usuarioIdAdmin ) {

        $lc_sql = "EXECUTE seguridad.IAE_inserta_canalMovimiento_desmontadoCajero  '$idUsuario','$ctrc_id','$lc_usuarioIdAdmin'";
        
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            } 
            return ($this->lc_regs); 
        }
    }

    public function findeldia( $IDPeriodo,$IDEstacion,$usr_id_admin ) {

        $lc_sql = "EXEC [seguridad].[IAE_CanalMovimiento_FindelDia] '$IDPeriodo','$IDEstacion','$usr_id_admin'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            } 
            return ($this->lc_regs); 
        }
    }
    
    public function retiraFondos( $accion,$ip,$usr_clave_admin,$tarjeta,$id_ctr_est ) {

        $lc_sql = "EXEC [seguridad].[USP_Retiro_FondoAsignado] $accion,'$ip','$usr_clave_admin','$tarjeta','$id_ctr_est'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            } 
            return ($this->lc_regs); 
        }
    }

    public function desmontarMotorizado( $idMotorizado,$idPeriodo,$idRestaurante,$idUsuario) {

        $lc_sql = "EXEC [dbo].[App_ImpresionDesasignacionMotorizado] '$idMotorizado', '$idPeriodo', '$idRestaurante', '$idUsuario' ";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            } 
            return ($this->lc_regs); 
        }
    }

    public function impresionCupon($transaccion, $idUsuario, $idEstacion, $idRestaurante, $nombre_usuario, $jsonDataRegistro) {

        $lc_sql = "EXEC [pedido].[ORD_impresion_ordenpedido_cupon] '$transaccion', '$idUsuario', '$idEstacion', '$idRestaurante', '$nombre_usuario'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($jsonDataRegistro)
                );
            } 
            return ($this->lc_regs); 
        }
    }

    public function impresionFidelizacionRecarga($transaccion,$idCadena,$idRestaurante,$idEstacion) {

        $lc_sql = "EXEC [impresion].[IAE_FidelizacionRecarga] '$transaccion', $idCadena, $idRestaurante, '$idEstacion'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            } 
            return ($this->lc_regs); 
        }
    }

    public function obtenerIpImpresora($nombreImpresora){
        $this->lc_regs = [];
        $query = "SELECT [impresion].[fn_ColeccionRestaurante_ServicioApiImpresionUrl]('$nombreImpresora') AS nombre_impresora";
        try {
            $this->fn_ejecutarquery( $query );
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['urlApiImpresion'] = $row['nombre_impresora'];
         }
            $this->lc_regs['registros'] = $this->fn_numregistro();  
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }
    


    public function auditoriaApiImpresion($tipoDocumento, $estado, $impresora, $idEstacion, $idUsuario, $url, $payload, $response, $datosAdicionales = null, $codigo_app = null, $codigo_factura = null, $tipoEntrega = null) {
        $sqlSetence = $tipoEntrega != 'delivery' ? 'INSERT' : 'UPDATE';
        $response = $estado == 'ERROR CONEXION API NET CORE' ? str_replace("'", '"', json_encode($response)) : $response;
        $lc_sql = "EXEC [impresion].[IAE_AuditoriaApiImpresion] '$tipoDocumento', '$estado', '$impresora', '$idEstacion', '$idUsuario', '$url', '".utf8_decode($payload)."', '$response', '$datosAdicionales', '$sqlSetence', '$codigo_app', '$codigo_factura'";
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result){ return true; }else{ return false; };
    }

    public function impresionNumeroPedido($transaccion, $idRestaurante) {
        $lc_sql = "EXEC [dbo].[USB_Obtener_CabeceraPedido] '$transaccion', $idRestaurante";
        $numeroID='';
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $numeroID= trim($row['IDCabeceraOrdenpedido']);           
            }
            return ($numeroID); 
        }
    }

    public function impresionDesmontarCajeroPickup($usr, $ctrc, $usrAdmin) {
        $lc_sql = "EXECUTE seguridad.IAE_inserta_canalMovimiento_desmontadoCajero '$usr','$ctrc', '$usrAdmin'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            } 
            return ($this->lc_regs); 
        }
    }
    
    public function descripcionTipoImpresora( $imp_nombre ) {
        $consulta = "SELECT [impresion].[fnServicioApiImpresionObtenerDescripcionTipoImpresora]('$imp_nombre')";
        $descripcionTipoImpresora = '';

        try {
            if ( $this->fn_ejecutarquery( $consulta ) ) {
                $registro = array();
                $registro = $this->fn_leerarreglo();
                $descripcionTipoImpresora = $registro[0];
            }
        } catch (Exception $e) { ; }

        return $descripcionTipoImpresora;
    }

    
    /**
     * PROCESO PICKING AGREGADORES
     */
    public function infoCodigoConfirmacionDelivery($idRestaurante, $idEstacion, $codigo, $opcion){
        $lc_sql ="EXEC [impresion].[USP_impresion_codigo_confirmacion_delivery] '$idRestaurante', '$idEstacion', $codigo, $opcion ";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    'numeroImpresiones' => 1,
                    'tipo' => $row['tipo'],
                    'impresora' => utf8_encode(trim($row['impresora'])),
                    'formatoXML' => utf8_encode(trim($row['formatoXML'])),
                    'jsonData' => utf8_encode(trim($row['jsonData'])),
                    'jsonRegistros' => ''
                );
            }
            unset($lc_sql);
            return ($this->lc_regs); 
        }
        return $this->fn_ejecutarquery($lc_sql);
    }

    public function impresionCampanaSolidaria($cadena, $restaurante, $campanaSolidaria, $IDEstacion, $IDUsuario) {
        $lc_sql = "EXEC [config].[campana_solidaria_imprimir] '$cadena','$restaurante','$campanaSolidaria','$IDEstacion','$IDUsuario'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            } 
            return ($this->lc_regs); 
        }
    }    

    

    public function distribucionImpresion($imp_nombre, $idCabeceraOrdenPedido) {
        unset($this->lc_regs);
        if ($idCabeceraOrdenPedido != ''){
            $lc_sql = "EXEC [impresion].[USP_distribucionImpresion] '$imp_nombre', '$idCabeceraOrdenPedido'";
        }else{
            $lc_sql = "EXEC [impresion].[USP_distribucionImpresion] '$imp_nombre'";
        }
    
        $response = false;

        try {
            if ($this->fn_ejecutarquery($lc_sql)) {
                while ($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array(
                        'impresoraEnviar' => $row['impresoraEnviar'],
                        'aplicaEjecucion' => $row['aplicaEjecucion'],
						'impresorasBalancear' => $row['impresorasBalancear']
                        
                    );
                }
                
                return ($this->lc_regs); 
            }
    
        } catch (Exception $e) { ;  }

            return $response;
    }

    public function cambiarImpresora($nombreImpresora){
        $this->lc_regs = [];
        $query = "SELECT * from [impresion].[cambiar_impresora_distribucion]('$nombreImpresora') as nuevaImpresora";
        try {
            $this->fn_ejecutarquery( $query );
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['nuevaImpresora'] = $row['nuevaImpresora'];
         }
            $this->lc_regs['registros'] = $this->fn_numregistro();  
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    public function imprimeKDS($idRestaurante, $idCabeceraOrdenPedido){
        $imprimeKDS = 0;
        $query = "SELECT  [config].[fn_ColeccionRestaurante_LocalImprimeKDS]($idRestaurante, '$idCabeceraOrdenPedido') as imprimeKDS";
        try {
            $this->fn_ejecutarquery( $query );
            while ($row = $this->fn_leerarreglo()) {
                $imprimeKDS = $row['imprimeKDS'];
         } 
        } catch (Exception $e) {
            return $imprimeKDS;
        }
        return $imprimeKDS;
    }
    
}
