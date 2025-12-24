<?php

class TransferenciaProducto extends sql {

    function __construct() {
        parent ::__construct();
    }

    function cargarConfiguracionTransferenciaVentaCadena($accion, $idCadena) {
        $lc_sql = "config.RESTAURANTE_TRANSFERENCIA_VENTA_cargarTranferenciasPorCadena " . $accion . ", " . $idCadena . ", '', '','1'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("coleccion" => $row['coleccion'], "IDCadenaDestino" => $row['IDCadenaDestino'], "parametro" => $row['parametro'], "descripcion" => utf8_encode($row['descripcion']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarConfiguracionTransferenciaVentaProducto($accion, $idCadena, $idColeccion, $idParametro, $estado) {
        $lc_sql = "config.PRODUCTO_TRANSFERENCIA_VENTA_cargarTranferenciasPorProducto " . $accion . ", " . $idCadena . ", '" . $idColeccion . "', '" . $idParametro . "', '',  '" . $estado . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idProductoOrigen" => $row['idProductoOrigen'],
                    "productoOrigen" => utf8_encode($row['productoOrigen']),
                    "numProductoOrigen" => $row['numProductoOrigen'],
                    "idProductoDestino" => $row['idProductoDestino'],
                    "productoDestino" => utf8_encode($row['productoDestino']),
                    "numProductoDestino" => ($row['numProductoDestino']),
                    "estado" => $row['estado']
                    ,"claProductoOrigen" => utf8_encode($row['claProductoOrigen'])
                    ,"claProductoDestino" => utf8_encode($row['claProductoDestino'])    
                        )
                        ;
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarProductoCadenaOrigenDestino($accion, $idCadena, $idColeccion, $idParametro, $idDestinoInluir) {
        $lc_sql = "config.PRODUCTO_TRANSFERENCIA_VENTA_cargarTranferenciasPorProducto " . $accion . ", " . $idCadena . ", '" . $idColeccion . "', '" . $idParametro . "','" . $idDestinoInluir . "','1' ";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "plu_id" => $row['plu_id'],
                    "plu_descripcion" => utf8_encode($row['plu_descripcion']),
                    "plu_num_plu" => $row['plu_num_plu'],
                    "cla_Nombre" => $row['cla_Nombre'],
                    
                    );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarInformacionProductoCadenaOrigenDestino($accion, $idCadena, $idColeccion, $idParametro, $estado) {
        $lc_sql = "config.PRODUCTO_TRANSFERENCIA_VENTA_CARGAR_origen_destino " . $accion . ", " . $idCadena . ", '" . $idColeccion . "', '" . $idParametro . "','" . $estado . "' ";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "origen" => $row['origen'],
                    "destino" => utf8_encode($row['destino']),
                    "ID_ColeccionCadena" => utf8_encode($row['ID_ColeccionCadena']),
                    "ID_ColeccionDeDatosCadena" => utf8_encode($row['ID_ColeccionDeDatosCadena']),
                    "descripcion_coleccion" => $row['descripcion_coleccion']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function GuardarTransferenciaVentaProducto($accion, $cadena , $idParametroIntegracion, $usuario, $origen, $destino, $estado) {
         $lc_sql = "config.PRODUCTO_TRANSFERENCIA_VENTA_IA_transferencia_producto " . $accion . "," . $cadena . ", '" . $idParametroIntegracion . "', '" . $usuario . "', '" . $origen . "', '" . $destino . "', " . $estado . " ,''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("respuesta" => utf8_encode($row["respuesta"]),
                    "mensaje" => utf8_encode($row["mensaje"]));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function ActualizarTransferenciaVentaProducto($accion, $cadena   ,$idParametroIntegracion, $usuario, $origen, $destino, $estado, $oldDestino) {
        $lc_sql = "config.PRODUCTO_TRANSFERENCIA_VENTA_IA_transferencia_producto " . $accion . "," . $cadena . ", '" . $idParametroIntegracion . "', '" . $usuario . "', '" . $origen . "', '" . $destino . "', " . $estado . ", '" . $oldDestino . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("respuesta" => utf8_encode($row["respuesta"]),
                    "mensaje" => utf8_encode($row["mensaje"]));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

}
