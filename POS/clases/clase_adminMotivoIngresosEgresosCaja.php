<?php

class Motivo extends sql {

    function cargarMotivosIngresosEgresosCajaActivos() {
        $result = array();
        $query = "EXEC config.MOTIVOSINGRESOSEGRESOSCAJA_configuraciones 1, 'Activo'";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array(
                    "idMotivoIngresosEgresosCaja" => $row['idMotivoIngresosEgresosCaja'],
                    "concepto" => utf8_encode($row['concepto']),
                    "signo" => $row['signo'], 
                    "nivel" => $row['nivel'], 
                    "estado" => $row['estado']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $ex) {
            return json_encode($ex);
        }
        return json_encode($result);
    }
    
    function cargarMotivosIngresosEgresosCajaInactivos() {
        $result = array();
        $query = "EXEC config.MOTIVOSINGRESOSEGRESOSCAJA_configuraciones 1, 'Inactivo'";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idMotivoIngresosEgresosCaja" => $row['idMotivoIngresosEgresosCaja'], "concepto" => $row['concepto'], "signo" => $row['signo'], "nivel" => $row['nivel'], "estado" => $row['estado']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $ex) {
            return json_encode($ex);
        }
        return json_encode($result);
    }
    
    function cargarMotivosIngresosEgresosCajaTodos() {
        $result = array();
        $query = "EXEC config.MOTIVOSINGRESOSEGRESOSCAJA_configuraciones 0, ''";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idMotivoIngresosEgresosCaja" => $row['idMotivoIngresosEgresosCaja'], "concepto" => $row['concepto'], "signo" => $row['signo'], "nivel" => $row['nivel'], "estado" => $row['estado']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $ex) {
            return json_encode($ex);
        }
        return json_encode($result);
    }

    function guardarMotivoIngresosEgresosCaja($accion, $idMotivoIngresosEgresosCaja, $concepto, $signo, $nivel, $estado, $idUserPos) {
        $result = array();
        $query = "EXEC config.MOTIVOSINGRESOSEGRESOSCAJA_IA_motivos " . $accion . ", '" . $idMotivoIngresosEgresosCaja . "', '" . $concepto . "', '" . $signo . "', " . $nivel . ", '" . $estado . "', '$idUserPos'";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idMotivoIngresosEgresosCaja" => $row['idMotivoIngresosEgresosCaja'], "concepto" => $row['concepto'], "signo" => $row['signo'], "nivel" => $row['nivel'], "estado" => $row['estado']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $ex) {
            return json_encode($ex);
        }
        return json_encode($result);
    }

}