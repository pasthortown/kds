<?php

class TransferenciaRestaurante extends sql {

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

    function cargarConfiguracionTransferenciaVentaRestaurante($accion, $idCadena, $idColeccion, $idParametro, $estado) {
         $lc_sql = "config.RESTAURANTE_TRANSFERENCIA_VENTA_cargarTranferenciasPorCadena " . $accion . ", " . $idCadena . ", '" . $idColeccion . "', '" . $idParametro . "', '" . $estado . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "IDColeccionRestaurante" => $row['IDColeccionRestaurante'],
                    "IDParametroRestaurante" => $row['IDParametroRestaurante'],
                    "origen" => $row['origen'],
                    "origenCodTienda" => $row['origenCodTienda'],
                    "origenDescripcion" => utf8_encode($row['origenDescripcion']),
                    "origenBD" => $row['origenBD'],
                    "destino" => $row['destino'],
                    "destinoCodTienda" => $row['destinoCodTienda'],
                    "destinoDescripcion" => utf8_encode($row['destinoDescripcion']),
                    "destinoBD" => $row['destinoBD'] ,
                    "estado" => $row['estado']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarLocalesTransferenciaVentaCadenaSinConfiguracion($accion, $idCadena, $idColeccion, $idParametro) {
        $lc_sql = "config.RESTAURANTE_TRANSFERENCIA_VENTA_cargarTranferenciasPorCadena " . $accion . ", " . $idCadena . ", '" . $idColeccion . "', '" . $idParametro . "','1' ";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "IDRestaurante" => $row['IDRestaurante'],
                    "CodRestaurante" => $row['CodRestaurante'],
                    "Descripcion" => utf8_encode($row['Descripcion']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function modificarConfiguracionTransferenciaVentaRestaurante($accion, $idCadena, $idColeccionCadena, $idParametroCadena, $idColeccionRestaurante, $idParametroRestaurante, $origen, $origenBD, $destino, $destinoBD, $idUsuario, $estado) {
          $lc_sql = "config.RESTAURANTE_TRANSFERENCIA_VENTA_IA_transferencia_restaurante " . $accion . ", " . $idCadena . ", '" . $idColeccionCadena . "', '" . $idParametroCadena . "', '" . $idColeccionRestaurante . "', '" . $idParametroRestaurante . "', " . $origen . ", '" . $origenBD . "', " . $destino . ", '" . $destinoBD . "', '" . $idUsuario . "', " . $estado;
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "IDColeccionRestaurante" => $row['IDColeccionRestaurante'],
                    "IDParametroRestaurante" => $row['IDParametroRestaurante'],
                    "origen" => $row['origen'],
                    "origenCodTienda" => $row['origenCodTienda'],
                    "origenDescripcion" => utf8_encode($row['origenDescripcion']),
                    "origenBD" => $row['origenBD'],
                    "destino" => $row['destino'],
                    "destinoCodTienda" => $row['destinoCodTienda'],
                    "destinoDescripcion" => utf8_encode($row['destinoDescripcion']),
                    "destinoBD" => $row['destinoBD'],
                     "estado" => $row['estado'] 
                        );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

}