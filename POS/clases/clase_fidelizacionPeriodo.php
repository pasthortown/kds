<?php

class Periodo extends sql {

    function cargarPeriodosRestaurante($idCadena, $idRestaurante, $idUsuario, $fechaInicio, $fechaFin) {
        $lc_sql = "EXEC fidelizacion.LOGS_generales $idCadena, $idRestaurante, '$idUsuario', '$fechaInicio', '$fechaFin'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idPeriodo" => $row['idPeriodo']
                    , "apertura" => $row['apertura']
                    , "cierre" => $row['cierre']
                    , "estado" => utf8_encode($row['estado']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            $result = $this->lc_regs;
        } catch (Exception $e) {
            $result = $this->lc_regs["mensaje"] = $e->getMessage();
        }
        return json_encode($result);
    }

    function cargarFacturasErrorPorPeriodo($idRestaurante, $idPeriodo) {
        $lc_sql = "EXEC fidelizacion.LOGS_cargarFacturasError " . $idRestaurante . ", '" . $idPeriodo . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "fechaCreacion" => $row['fechaCreacion']
                    , "idFactura" => $row['idFactura']
                    , "secuencial" => $row['secuenciaFacturacion']
                    , "total" => $row['total']
                    , "codigo" => $row['codigo']
                    , "mensaje" => utf8_encode($row['mensaje'])
                    , "cedulaCliente" => $row['cedulaCliente']
                    , "cliente" => utf8_encode($row['cliente']));
            }
            if ($this->fn_numregistro() > 0) {
                $result['data'] = $this->lc_regs;
            } else {
                $result['data'] = null;
            }
        } catch (Exception $e) {
            return json_encode($e->getMessage());
        }
        return json_encode($result);
    }

    function cargarNotasCreditoErrorPorPeriodo($idRestaurante, $idPeriodo) {
        $lc_sql = "EXEC fidelizacion.LOGS_cargarNotasCreditoError " . $idRestaurante . ", '" . $idPeriodo . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "fechaCreacion" => $row['fechaCreacion']
                    , "idNotaCredito" => $row['idNotaCredito']
                    , "secuencial" => $row['secuenciaFacturacion']
                    , "total" => $row['total']
                    , "codigo" => $row['codigo']
                    , "mensaje" => utf8_encode($row['mensaje'])
                    , "cedulaCliente" => $row['cedulaCliente']
                    , "cliente" => utf8_encode($row['cliente']));
            }
            if ($this->fn_numregistro() > 0) {
                $result['data'] = $this->lc_regs;
            } else {
                $result['data'] = null;
            }
        } catch (Exception $e) {
            return json_encode($e->getMessage());
        }
        return json_encode($result);
    }

    function cargarLogs($idRestaurante, $fechaInicio, $fechaFin) {
        $lc_sql = "EXEC fidelizacion.LOGS_cargarLogs " . $idRestaurante . ", '" . $fechaInicio . "', '" . $fechaFin . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "id" => $row['id']
                    , "fechaAuditoria" => $row['fechaAuditoria']
                    , "descripcion" => utf8_encode($row['descripcion'])
                    , "usuario" => $row['usuario']
                    , "accion" => utf8_encode($row['accion'])
                    , "respuesta" => utf8_encode($row['respuesta'])
                );
            }
            if ($this->fn_numregistro() > 0) {
                $result['data'] = $this->lc_regs;
            } else {
                $result['data'] = null;
            }
        } catch (Exception $e) {
            return json_encode($e->getMessage());
        }
        return json_encode($result);
    }

}
