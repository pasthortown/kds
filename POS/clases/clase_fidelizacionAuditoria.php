<?php

class Auditoria extends sql {

    function guardarLog($descripcion, $accion, $idRestaurante, $idCadena, $idUsuario, $trama) {
        $this->lc_regs = Array();
        $lc_sql = "EXEC fidelizacion.I_Auditorias '$descripcion', '$accion', $idRestaurante, $idCadena, '$idUsuario', '$trama'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $this->lc_regs = array("mensaje" => utf8_encode("Registro Exitoso"), "estado" => 1);
        } catch (Exception $e) {
            return $this->lc_regs = array("mensaje" => utf8_encode($e->getMessage()), "estado" => 0);
        }
        $result = $this->lc_regs;
        return $result;
    }

    function actualizarFacturaLog($idFactura, $accion, $puntos, $mensaje) {
        $this->lc_regs = Array();
        $lc_sql = "EXEC fidelizacion.IAE_ActualizaCabeceraFacturaError '$idFactura', $accion, $puntos, '$mensaje'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $this->lc_regs = array("mensaje" => utf8_encode("Registro Exitoso"), "estado" => 1);
        } catch (Exception $e) {
            return $this->lc_regs = array("mensaje" => utf8_encode($e->getMessage()), "estado" => 0);
        }
        $result = $this->lc_regs;
        return $result;
    }

    function actualizarNotaCreditoLog($idFactura, $accion, $puntos, $mensaje) {
        $this->lc_regs = Array();
        $lc_sql = "EXEC fidelizacion.IAE_ActualizaCabeceraNotaCreditoError '$idFactura', $accion, $puntos, '$mensaje'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $this->lc_regs = array("mensaje" => utf8_encode("Registro Exitoso"), "estado" => 1);
        } catch (Exception $e) {
            return $this->lc_regs = array("mensaje" => utf8_encode($e->getMessage()), "estado" => 0);
        }
        $result = $this->lc_regs;
        return $result;
    }

    function solicitarSecuencialProceso($idRestaurante, $proceso, $idUsuario) {
        $lc_sql = "EXEC fidelizacion.validarAutorizacion $idRestaurante, '$proceso', '$idUsuario'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array(
                'estadoProceso' => utf8_encode($row['proceso']),
                'secuencia' => $row['secuencia'],
                'cashierDocument' => $row['cashierDocument'],
                'cashierName' => utf8_encode($row['cashierName']));
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function finalizarSecuencialProceso($idRestaurante, $proceso, $secuencia) {
        $lc_sql = "EXEC fidelizacion.finalizarAutorizacion $idRestaurante, '$proceso', '$secuencia'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $this->lc_regs = array("confirmacion" => 1, "mensaje" => "Transacciones exitosa");
        } catch (Exception $e) {
            $this->lc_regs = array("confirmacion" => 0, "mensaje" => $e->getMessage());
        }
        return $this->lc_regs;
    }

}