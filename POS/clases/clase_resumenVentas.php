<?php

////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jorge Tinoco //////////////////////////////////
///////DESCRIPCION: Pantalla de Resumen de Ventas //////////////////////
///////FECHA CREACION: 19-10-2015///////////////////////////////////////
////////////////////////////////////////////////////////////////////////

class resumen extends sql {

    function _construct() {
        parent ::_construct();
    }

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case "cargarResumenVentasFacturas":
                $lc_sql = "EXEC reporte.VNT_resumen_facturas_cerradas " . $lc_datos[0] . ", '" . $lc_datos[1] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "nombreUsuario" => $row['nombreUsuario'],
                            "cuponesCanjeados" => $row['cuponesCanjeados'],
                            "Transacciones" => $row['Transacciones'],
                            "Ticket" => $row['Ticket'],
                            "totalVenta" => $row['totalVenta'],
                            "fechaInicio" => $row['fechaInicio'],
                            "fechaSalida" => $row['fechaSalida']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
            case "cargarConfiguracionResumenVentas":
                $lc_sql = "EXEC reporte.VNT_resumen_ventas_configuracion " . $lc_datos[0] . ", '" . $lc_datos[1] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("prd_id" => $row['prd_id'],
                            "fecha" => $row['fecha'],
                            "hora" => $row['hora']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
            case "cargarAccesosPerfil":
                $lc_sql = "config.USP_verificanivelacceso '" . $lc_datos[0] . "', '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("acc_id" => $row['acc_id'],
                            "acc_descripcion" => trim($row['acc_descripcion']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
            case "obtenerMesa":
                $lc_query = "EXEC pedido.ORD_asignar_mesaordenpedido ".$lc_datos[0].", '".$lc_datos[1]."', '".$lc_datos[2]."'";
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
                break;
        }
    }
    
    function fn_consultaValoresTotalesCuentas($lc_datos)
    {
        $lc_sql = "EXEC [reporte].[VNT_resumen_cuentas_cerradas_abiertas] '$lc_datos[0]','$lc_datos[1]' ";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("tipo" => $row['tipo'],
                            "total" => $row['total'],
                            "simbolo" => $row['simbolo']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
    }

}

