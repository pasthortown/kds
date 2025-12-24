<?php
include("../system/conexion/clase_sql.php");

class TransaccionesDeUna extends sql {

    public function insertarMedioVentaEnCabeceraFacturaBy_ODPId_Deuna($odp_id)
    {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.insertarMedioVentaBy_ODPId_Deuna '$odp_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "transactionFlag" => utf8_encode($row['transactionFlag']),
                );
            }
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }
    public function verificarProductosMaximosPorFactura($cdn_id) {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.consultarNumeroMaximoPluPorFactura $cdn_id";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "variableI" => utf8_encode($row['variableI']),
                    "plu_id" => utf8_encode($row['plu_id']),
                    "subsidio" => utf8_encode($row['subsidio']),
                );
            }
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function verificarProductosMaximosPorCliente($cdn_id) {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.consultarNumeroMaximoPluPorCliente $cdn_id";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "variableI" => utf8_encode($row['variableI']),
                    "plu_id" => utf8_encode($row['plu_id']),
                    "subsidio" => utf8_encode($row['subsidio']),
                );
            }
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }


    public function obtenerProductosDe_ODP_By_ODPId_PaymentRequest($odp_id) {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.consultarProductosDe_ODP_By_ODPId_DeUna '$odp_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    //"variableI" => utf8_encode($row['variableI']),
                    "id" => intval(utf8_encode($row['plu_id'])),
                    "name" => utf8_encode($row['plu_descripcion']),
                    "totalPrice" => floatval(utf8_encode($row['totalPrice'])),
                    "realPrice" => floatval(utf8_encode($row['realPrice'])),
                    "subsidy" => floatval(utf8_encode($row['subsidio'])),
                    "quantity" => floatval(utf8_encode($row['dop_cantidad'])),
                    "unitSubsidy" => floatval(utf8_encode($row['subsidioUnitario'])),
                );
            }
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }


    public function obtenerTransactionIdBy_ODP_Id($odp_id)
    {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.consultarTransactionIdBy_ODP_ID '$odp_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    //"variableI" => utf8_encode($row['variableI']),
                    "transactionId" => utf8_encode($row['transactionId']),
                );
            }
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function obtenerTransferNumberYOdpIdByCfacId($cfac_id)
    {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.consultarTransferNumber_y_ODP_Id_By_cfac_id '$cfac_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    //"variableI" => utf8_encode($row['variableI']),
                    "odp_id" => utf8_encode($row['odp_id']),
                    "transferNumber" => utf8_encode($row['transferNumber']),
                    "transactionId" => utf8_encode($row['transactionId']),
                );
            }
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    

    public function obtenerProductosDe_ODP_By_ODPId($odp_id)
    {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.consultarProductosDe_ODP_By_ODPId_DeUna '$odp_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    //"variableI" => utf8_encode($row['variableI']),
                    "plu_id" => utf8_encode($row['plu_id']),
                    "dop_cantidad" =>utf8_encode($row['dop_cantidad']),
                    "subsidio" => utf8_encode($row['subsidio'])
                );
            }
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function consultarUrl($rst_id, $wsServidor, $wsRuta)
    {
        $this->lc_regs = array();
        $lc_sql = "EXEC config.USP_Retorna_Direccion_Webservice $rst_id, '$wsServidor', '$wsRuta', 0";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "estado" => utf8_encode($row['estado']),
                    "direccionws" => utf8_encode($row['direccionws']),
                );
            }
            //$this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }
    public function consultarParametrosDeUna($cdn_id,$rst_id,$IDEstacion) {
        $this->lc_regs = Array();
        $lc_sql = "EXEC dbo.consultarParametrosDeUna $cdn_id, $rst_id, '$IDEstacion'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("Descripcion" => utf8_encode($row['Descripcion']),
                    "parametro" => utf8_encode($row['parametro']),
                    "variableV" => utf8_encode($row['variableV']),
                    "variableI" => utf8_encode($row['variableI'])
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }
    public function consultarDetalleDeUna($rst_id, $IDEstacion) {
        $this->lc_regs = Array();
        $lc_sql = "EXEC dbo.obtenerDetalleDeUna $rst_id, '$IDEstacion'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "detalleDeUna" => utf8_encode($row['detalleDeUna'])
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function consultarTransaccionIdDeUna($odp_id) {
        $this->lc_regs = Array();
        $lc_sql = "EXEC dbo.obtenerTransaccionIdDeUna '$odp_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "transactionId" => utf8_encode($row['transactionId'])
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function consultarCFacIdPorOdpId($odp_id) {
        $this->lc_regs = Array();
        $lc_sql = "EXEC dbo.obtenerCFacIdPorOdpId '$odp_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "cfac_id" => utf8_encode($row['cfac_id'])
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function guardarPaymentInfoDeUna($odp_id,$respDeuna) {
        $this->lc_regs = Array();
        $lc_sql = "EXEC dbo.guardarRespuestaPaymentInfoDeUna '$odp_id', '$respDeuna'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "respDeUna" => utf8_encode($row['respDeUna'])
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function obtenerPaymentInfoBaseDeUna($odp_id) {
        $this->lc_regs = Array();
        $lc_sql = "EXEC dbo.obtenerPaymentInfoBaseDeUna '$odp_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "paymentInfo" => utf8_encode($row['paymentInfo'])
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }
    public function consultarCambioDeEstadoDelPago($odp_id) {
        $this->lc_regs = Array();
        $lc_sql = "EXEC dbo.consultarEstadoDelPagoDeUna '$odp_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("existePago" => utf8_encode($row['existePago']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function actualizarEstadoPagoDeuna($odp_id, $cdn_id) {
        $this->lc_regs = Array();
        $lc_sql = "EXEC dbo.ActualizarEstadoDeUnaAprobado '$odp_id', '$cdn_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("existePago" => utf8_encode($row['existePago']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }


    public function guardarDeUnaIdEnCabeceraOrdenPedido($requestId, $trasaccionId, $odp_id, $estado, $cdn_id) {
        $this->lc_regs = Array();
        $lc_sql = "EXEC dbo.guardarIdDeDeunaEnCabeceraOrdenPedido '$trasaccionId','$odp_id','$estado',$cdn_id, '$requestId'";
        //echo $lc_sql;
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("existeTransaccionId" => utf8_encode($row['existeTransaccionId']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function guardarRequestIdEnCabeceraOrdenPedido($requestId, $odp_id, $estado, $cdn_id) {
        $this->lc_regs = Array();
        $lc_sql = "EXEC dbo.guardarRequestIdEnCabeceraOrdenPedidoDeuna '$requestId','$odp_id','$estado',$cdn_id";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("existeRequestId" => utf8_encode($row['existeRequestId']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function consultarEstadoDelPagoDeUna_ReanudarPago($odp_id) {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.consultarEstadoDelPagoDeUna_ReanudarPago '$odp_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "requestId" => utf8_encode($row['requestId']),
                    "status" => utf8_encode($row['status']),
                );
            }
            //$this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function ocultarProductosConSubsidio($cdn_id) {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.ocultarProductosConSubsidioDeUna '$cdn_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "RowsAffected" => utf8_encode($row['RowsAffected']),
                );
            }
            //$this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function obtenerRequestId_By_Odp_Id($odp_id) {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.obtenerRequestIdDeUna '$odp_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "requestId" => utf8_encode($row['requestId']),
                );
            }
            //$this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function anularTransaccionDeuna($trasaccionId, $odp_id, $estado, $cdn_id)
    {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.anularTransactionDeUna '$trasaccionId','$odp_id','$estado',$cdn_id";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("existeTransaccionId" => utf8_encode($row['existeTransaccionId']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function limpiarOrdenBy_ODP_Id($odp_id)
    {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.limpiarVarchar6_ODP_Deuna '$odp_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("existeTransaccionId" => utf8_encode($row['existeTransaccionId']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function obtenerProductosSubsidiadosPorClienteDeUna ($cdn_id,$cli_id,$plu_id) {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.obtenerProductosSubsidiadosPorClienteDeUna $cdn_id, '$cli_id', $plu_id";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "plu_id" => utf8_encode($row['plu_id']),
                    "cantidad" => utf8_encode($row['cantidad']),
                );
            }
            //$this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function eliminarProductosConSubsidio ($odp_id, $cdn_id) {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.eliminarProductosConSubsidio '$odp_id', $cdn_id ";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "RowsDeleted" => utf8_encode($row['RowsDeleted']),
                );
            }
            //$this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }


    public function ingresarAuditoriaDeUna($_parametros)
    {
                
        $this->lc_regs = Array();
        $lc_sql = "EXEC [seguridad].[IAE_Auditoria_Transaccion] @IDAuditoriaTransaccion='".$_parametros['IDAuditoriaTransaccion']."',
                                                            @rst_id='".$_parametros['rst_id']."',
                                                            @atran_modulo='".$_parametros['atran_modulo']."',
                                                            @atran_descripcion='".$_parametros['atran_descripcion']."',
                                                            @atran_accion='".$_parametros['atran_accion']."',
                                                            @IDUsersPos='".$_parametros['IDUsersPos']."',
                                                            @Auditoria_TransaccionVarchar1='".$this->eliminarComillasSimples($_parametros['Auditoria_TransaccionVarchar1'])."',
                                                            @Auditoria_TransaccionVarchar2='".$this->eliminarComillasSimples($_parametros['Auditoria_TransaccionVarchar2'])."'
                    ";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "IDAuditoriaTransaccion" => utf8_encode($row['IDAuditoriaTransaccion'])
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    private function eliminarComillasSimples($cadena ) {
        $cadena = str_replace("'", "", $cadena);
        return $cadena;
    }

    public function validacionAnulacionDeUna ($cfac_id) {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.validacionAnulacionDeUna '$cfac_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "esValida" => utf8_encode($row['esValida']),
                );
            }
            //$this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function consultarSiLaFacturaTieneFormaPagoDeUna ($cfac_id) {
        $this->lc_regs = array();
        $lc_sql = "EXEC dbo.consultarSiLaFacturaTieneFormaPagoDeUna '$cfac_id'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "tieneFormaPagoDeUna" => utf8_encode($row['tieneFormaPagoDeUna']),
                );
            }
            //$this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }
}
?>