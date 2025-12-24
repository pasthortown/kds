<?php

class Departamento extends sql {

    function __construct() {
        parent ::__construct();
    }

    function cargarDepartamentosPorCadena($idCadena) {
        $this->lc_regs = array();
        $lc_sql = "EXEC config.DEPARTAMENTO_configuraciones 0, " . $idCadena . ", ''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idParametro" => $row['idParametro'], "idColeccion" => $row['idColeccion'], "NumDepartamento" => $row['NumDepartamento'], "idDepartamento" => $row['idDepartamento'], "departamento" => utf8_encode($row['departamento']), "estado" => $row['estado']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return $this->lc_regs;
    }

    function actualizarDepartamentosPorCadena($opcion, $idCadena, $idUsuario, $descripcion, $idParametro, $idDepartamento, $estado) {
        $this->lc_regs = array();
        $lc_sql = "[config].[DEPARTAMENTO_IA_departamento] " . $opcion . " , " . $idCadena . ", '" . $idUsuario . "', '" . $idParametro . "', '" . $descripcion . "', " . $idDepartamento . ", " . $estado;
        // die($lc_sql);
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array("idParametro" => $row['idParametro'], "idColeccion" => $row['idColeccion'], "NumDepartamento" => $row['NumDepartamento'], "idDepartamento" => $row['idDepartamento'], "departamento" => utf8_decode($row['departamento']), "estado" => $row['estado']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return $this->lc_regs;
    }

}
