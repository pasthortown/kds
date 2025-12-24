<?php

class Reporte extends sql {

    function __construct() {
        parent ::__construct();
    }

    function cargarMenuReportes() {
        $query = "EXEC reporte.REPORTES_configuraciones 0, 0, '', ''";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idCategoria" => $row['idCategoria'],
                    "idReporte" => $row['idReporte'],
                    "url" => $row['url'],
                    "label" => utf8_encode($row['label']),
                    "descripcion" => utf8_encode($row['descripcion']),
                    "tipo" => utf8_encode($row['tipo']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarParametrosReporte($idReporte) {
        $query = "EXEC reporte.REPORTES_configuraciones 1, 0, '', '" . $idReporte . "'";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idParametro" => $row['idParametro'],
                    "tipoDato" => $row['tipoDato'],
                    "orden" => $row['orden'],
                    "obligatorio" => $row['obligatorio'],
                    "nombre" => utf8_encode($row['nombre']),
                    "label" => utf8_encode($row['label']),
                    "columnaIntegracion" => utf8_encode($row['columnaIntegracion']),
                    "tablaIntegracion" => utf8_encode($row['tablaIntegracion']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarObjetoParametrosReporte($idReporte) {
        $query = "EXEC reporte.REPORTES_configuraciones 1, 0, '', '" . $idReporte . "'";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array(
                    "idParametro" => $row['idParametro'],
                    "tipoDato" => $row['tipoDato'],
                    "orden" => $row['orden'],
                    "obligatorio" => $row['obligatorio'],
                    "nombre" => utf8_encode($row['nombre']),
                    "label" => utf8_encode($row['label']),
                    "columnaIntegracion" => utf8_encode($row['columnaIntegracion']),
                    "tablaIntegracion" => utf8_encode($row['tablaIntegracion']));
            }
        } catch (Exception $e) {
            return json_encode($e);
        }
        return $result;
    }

    function cargarInformacionReportePorId($idReporte) {
        $query = "EXEC reporte.REPORTES_configuraciones 2, 0, '', '" . $idReporte . "'";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result = array(
                    "idReporte" => $row['idReporte'],
                    "url" => $row['url'],
                    "label" => utf8_encode($row['label']),
                    "descripcion" => utf8_encode($row['descripcion']));
            }
        } catch (Exception $e) {
            return json_encode($e);
        }
        return $result;
    }

}