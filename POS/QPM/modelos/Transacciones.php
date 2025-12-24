<?php
include("../system/conexion/clase_sql.php");

class TransaccionesQPM extends sql{
    public function consultarFactura($_parametros)
    {
        $this->lc_regs = Array();
        $lc_sql = "EXEC [config].[transacciones_QPM] '".$_parametros['accion']."', '".$_parametros['idTransaccion']."', ".$_parametros['rst_id'].",".$_parametros['cdn_id'];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("cfac_id" => utf8_encode($row['cfac_id']),
                    "IDEstacion" => utf8_encode($row['IDEstacion']),
                    "cajero" => utf8_encode($row['usr_descripcion']),
                    "IDCabeceraOrdenPedido" => utf8_encode($row['IDCabeceraOrdenPedido']),
                    "IDUsersPos" => utf8_encode($row['IDUsersPos']),
                    "fechaCreacion" => $row['cfac_fechacreacion']->format('Y-m-d H:i:s:uZ'),
                    "PosSystemIdentifier" => $row['PosSystemIdentifier'],
                    "NonDepleting" => $row['NonDepleting'],
                    "currency" => utf8_encode($row['pais_moneda'])
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }
    public function consultarDetalleFactura($_parametros)
    {
        $this->lc_regs = Array();
        $lc_sql = "EXEC [config].[transacciones_QPM] '$_parametros[0]', '$_parametros[1]',$_parametros[2],$_parametros[3]";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDDetalleFactura" =>  utf8_encode($row['IDDetalleFactura']),
                    "cfac_id" =>  utf8_encode($row['cfac_id']),
                    "plu_id" =>  utf8_encode($row['plu_id']),
                    "nombre" =>  utf8_encode($row['plu_descripcion']),
                    "cantProducto" =>  $row['dtfac_cantidad'],
                    "precio_unitario" =>  $row['dtfac_precio_unitario'],
                    "iva" =>  $row['dtfac_iva'],
                    "total" =>  $row['dtfac_total']
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    /**
     *  @fn consultarCupon
     * 
     *  @brief Consulta a base la ultima factura, setea valores a cero y devuelve enmascarada con datos del cupon
     * 
     *  @author Alejandro Salas
     *  @param array Paramtros del cupon y demas validadores de caja, restaurante, cadena.
     *  @return array Devuelve la cabecera de factura. 
     */
    public function consultarCupon($_parametros)
    {
        $detalle = $this->obtenerDetalleCupon($_parametros["detalle"]);
        $this->lc_regs = Array();
        $lc_sql = "EXEC [config].[transacciones_QPM_cupon] '".$_parametros['accion']."', '".$_parametros['idTransaccion']."', ".$_parametros['rst_id'].",".$_parametros['cdn_id']. ", '".$_parametros['idTransaccion']."', '".$detalle['codPlu']."','".$detalle['descripcion_plu']."','".$detalle['cantidad']."','".$detalle['iva']."','".$detalle['precioBruto']."','".$detalle['precioNeto'] ."';";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("cfac_id" => utf8_encode($row['cfac_id']),
                    "IDEstacion" => utf8_encode($row['IDEstacion']),
                    "cajero" => utf8_encode($row['usr_descripcion']),
                    "IDCabeceraOrdenPedido" => utf8_encode($row['IDCabeceraOrdenPedido']),
                    "IDUsersPos" => utf8_encode($row['IDUsersPos']),
                    "fechaCreacion" => $row['cfac_fechacreacion']->format('Y-m-d H:i:s:uZ'),
                    "PosSystemIdentifier" => $row['PosSystemIdentifier'],
                    "NonDepleting" => $row['NonDepleting'],
                    "currency" => utf8_encode($row['pais_moneda'])
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }
    /**
     *  @fn obtenerDetalleCupon
     * 
     *  @brief Permite convertir el detalle json a String separados por comas de cada campo ej precio, plu
     * 
     *  @author Alejandro Salas
     *  @param array Detalle de cupon tipo array json
     *  @return array Detalle de cupon separado por comas.
     */
    function obtenerDetalleCupon($detalle) {
        $nDetalle = count($detalle);
        $i = 0;
        $codPlu = "";
        $descripcionPlu = "";
        $cantidad = "";
        $iva = "";
        $precioBruto = "";
        $precioNeto = "";//total
        $separador = ",";
        for($i=0;$i<$nDetalle;$i++) {
    
            if (($nDetalle-1)==$i) {
                $codPlu .= trim($detalle[$i]["codPlu"]);
                $descripcionPlu .= trim($detalle[$i]["descripcion_plu"]);
                $cantidad .= trim($detalle[$i]["cantidad"]);
                $iva = trim($detalle[$i]["iva"]);
                $precioBruto .= trim($detalle[$i]["precioBruto"]);
                $precioNeto .= trim($detalle[$i]["precioNeto"]);
            } else {
                $codPlu .= trim($detalle[$i]["codPlu"]) . $separador ;
                $descripcionPlu .= trim($detalle[$i]["descripcion_plu"]) . $separador ;
                $cantidad .= trim($detalle[$i]["cantidad"]) . $separador ;
                $iva = trim($detalle[$i]["iva"]) . $separador ;
                $precioBruto .= trim($detalle[$i]["precioBruto"]) . $separador ;
                $precioNeto .= trim($detalle[$i]["precioNeto"]) . $separador ;
            }
    
    
        }
        $detalle_aux = array();
        $detalle_aux["codPlu"] = $codPlu;
        $detalle_aux["descripcion_plu"] = $descripcionPlu;
        $detalle_aux["cantidad"] = $cantidad;
        $detalle_aux["iva"] = $iva;
        $detalle_aux["precioBruto"] = $precioBruto;
        $detalle_aux["precioNeto"] = $precioNeto;
    
        return $detalle_aux;
    
    }

    /**
     *  @fn consultarDetalleCupon
     * 
     *  @brief Consulta a base la ultima factura, setea valores a cero y devuelve enmascarada con datos del cupon
     * 
     *  @author Alejandro Salas
     *  @param array Parametros del cupon y demas validadores de caja, restaurante, cadena.
     *  @return array Detalle de factura del Cupon 
     */
    public function consultarDetalleCupon($_parametros)
    {
        $this->lc_regs = Array();
        $detalle = $this->obtenerDetalleCupon($_parametros[4]);
        $lc_sql = "EXEC [config].[transacciones_QPM_cupon] '$_parametros[0]', '$_parametros[1]',$_parametros[2],$_parametros[3]". ", '".$_parametros[1]."', '".$detalle['codPlu']."','".$detalle['descripcion_plu']."','".$detalle['cantidad']."','".$detalle['iva']."','".$detalle['precioBruto']."','".$detalle['precioNeto'] ."';";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDDetalleFactura" =>  utf8_encode($row['IDDetalleFactura']),
                    "cfac_id" =>  utf8_encode($row['cfac_id']),
                    "plu_id" =>  utf8_encode($row['plu_id']),
                    "nombre" =>  utf8_encode($row['plu_descripcion']),
                    "cantProducto" =>  $row['dtfac_cantidad'],
                    "precio_unitario" =>  $row['dtfac_precio_unitario'],
                    "iva" =>  $row['dtfac_iva'],
                    "total" =>  $row['dtfac_total']
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }


    public function validarDisponibilidadServicioQPM($_parametros)
    {
        $this->lc_regs = Array();
        $lc_sql = "EXEC [config].[USP_integracion_QPM] '".$_parametros['cdn_id']."', '".$_parametros['rst_id']."'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "isActive" => $row['isActive'],
                    "ipTienda" => utf8_encode($row['ipTienda']),
                    "url" => utf8_encode($row['url']),
                    "activity" => utf8_encode($row['activity'])
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function ingresarTransaccionParaAuditoria($_parametros)
    {
                
        $this->lc_regs = Array();
        $lc_sql = "EXEC [seguridad].[IAE_Auditoria_Transaccion] @IDAuditoriaTransaccion='".$_parametros['IDAuditoriaTransaccion']."',
                                                            @rst_id='".$_parametros['rst_id']."',
                                                            @atran_modulo='".$_parametros['atran_modulo']."',
                                                            @atran_descripcion='".$_parametros['atran_descripcion']."',
                                                            @atran_accion='".$_parametros['atran_accion']."',
                                                            @Auditoria_TransaccionVarchar1='".$_parametros['Auditoria_TransaccionVarchar1']."',
                                                            @Auditoria_TransaccionVarchar2='".$_parametros['Auditoria_TransaccionVarchar2']."'
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
}
?>