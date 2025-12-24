<?php

class AdministracionCadena extends sql {

    function cargarClavesPorSemana($idCadena, $anio) {
        $lc_sql = "EXEC config.CLAVEWIFI_obtenerSemanasPorAnio $idCadena, $anio";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("semana" => $row['semana']
                    , "anio" => $row['anio']
                    , "desde" => $row['desde']
                    , "hasta" => $row['hasta']
                    , "descripcion" => $row['descripcion']
                    , "clave" => $row['clave']);
            }
            if ($this->fn_numregistro() > 0) {
                $result = $this->lc_regs;
                $result["str"] = $this->fn_numregistro();
            } else {
                $result = null;
            }
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }

    function mergeClaveWifiPorSemana($idCadena, $idUsuario, $fecha, $hasta, $clave) {
        $lc_sql = "EXEC config.CLAVEWIFI_mergeClaveWifi $idCadena, '$idUsuario', '$fecha', '$hasta', '$clave'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $result["estado"] = 1;
            $result["mensaje"] = "Operacion exitosa";
        } catch (Exception $e) {
            $result["estado"] = 0;
            $result["mensaje"] = $e->message;
        }
        return json_encode($result);
    }

    function cargarRestaurantes($idCadena) {
        $lc_sql = "EXEC config.CLAVEWIFI_obtenerRestaurantes $idCadena";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idRestaurante" => $row["idRestaurante"],
                    "descripcion" => utf8_encode($row["descripcion"]));
            }
            if ($this->fn_numregistro() > 0) {
                $result = $this->lc_regs;
                $result["str"] = $this->fn_numregistro();
            } else {
                $result = null;
            }
        } catch (Exception $e) {
            
        }
        return json_encode($result);
    }

    function cargarRestaurantesWifi($idCadena) {
        $lc_sql = "EXEC config.CLAVEWIFI_obtenerRestaurantesWifi $idCadena";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idRestaurante" => $row["idRestaurante"]);
            }
            if ($this->fn_numregistro() > 0) {
                $result = $this->lc_regs;
                $result["str"] = $this->fn_numregistro();
            } else {
                $result = null;
            }
        } catch (Exception $e) {
            
        }
        return json_encode($result);
    }

    function guardarRestaurantesWifi($idCadena, $restaurantes, $idUsuario) {
        $lc_sql = "EXEC config.CLAVEWIFI_guardarRestaurantesWifi $idCadena, '$restaurantes', '$idUsuario'";
        $result["resp"] = 0;
        try {
            if ($this->fn_ejecutarquery($lc_sql)) {
                $row = $this->fn_leerarreglo();
                $result["resp"] = $row["resp"];
            }
        } catch (Exception $e) {
            
        }
        return json_encode($result);
    }

}
