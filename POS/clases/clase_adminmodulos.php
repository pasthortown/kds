<?php

class Modulo extends sql {
    
    function cargarModulos() {
        $query = "EXEC config.MODULOS_configuraciones 0, 0, 0";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idModulo" => $row['idModulo'], "descripcion" => $row['descripcion'], "abreviatura" => $row['abreviatura'], "nivel" => $row['nivel'], "estado" => intval($row['estado']));
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }

        return json_encode($result);
    }
    
    function guardarModulo($accion, $idModulo, $descripcion, $abreviatura, $nivel, $estado, $idCadena, $idUsuario) {
        $query = "EXEC config.MODULOS_IA_modulos " . $accion . ", " . $idModulo . ", '" . $descripcion . "', '" . $abreviatura . "', " . $nivel . ", " . $estado . ", " . $idCadena . ",'" . $idUsuario . "'";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idModulo" => $row['idModulo'], "descripcion" => $row['descripcion'], "abreviatura" => $row['abreviatura'], "nivel" => $row['nivel'], "estado" => intval($row['estado']));
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }
    
    function cargarEstados($idModulo, $idCadena) {
        $query = "EXEC config.MODULOS_configuraciones 1, " . $idModulo . ", " . $idCadena;
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idEstado" => $row['idEstado'], "descripcion" => utf8_encode($row['descripcion']), "factor" => $row['factor'], "idFactor" => $row['idFactor'], "nivel" => $row['nivel']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }
    
    function guardarEstado($accion, $idEstado, $descripcion, $factor, $nivel, $idModulo, $idCadena, $idUsuario) {
        $query = "EXEC config.MODULOS_IA_estados " . $accion . ", '" . $idEstado . "', '" . utf8_decode($descripcion) . "', '" . $factor . "', " . $nivel . ", " . $idModulo . ", " . $idCadena . ",'" . $idUsuario. "'";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idEstado" => $row['idEstado'], "descripcion" => utf8_encode($row['descripcion']), "factor" => $row['factor'], "idFactor" => $row['idFactor'], "nivel" => $row['nivel']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }
    
    function cargarFactorMultiplicador($idCadena) {
        $query = "EXEC config.MODULOS_configuraciones 2, 0, " . $idCadena;
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array("idFactor" => $row['idFactor'], "factor" => $row['factor']);
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }
    
}