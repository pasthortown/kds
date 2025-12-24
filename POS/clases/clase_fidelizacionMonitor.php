<?php

class Monitor extends sql {

    function cargarTotalVentas($idRestaurante, $fechaDesde, $fechaHasta) {
        $this->lc_regs = array();
        $lc_sql = "EXEC fidelizacion.MONITOR_cargarPorcentaje $idRestaurante, '$fechaDesde', '$fechaHasta'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array("totalVenta" => round($row['totalVenta'], 2)
                    , "totalVentaGeneral" => round($row['totalVentaGeneral'], 2)
                    , "porcentajeVentaGeneral" => round($row['porcentajeVentaGeneral'], 2)
                    , "totalVentaAutoconsumo" => round($row['totalVentaAutoconsumo'], 2)
                    , "porcentajeVentaAutoconsumo" => round($row['porcentajeVentaAutoconsumo'], 2));
            } else {
                $this->lc_regs = array("totalVenta" => 0
                    , "fechaPeriodo" => ""
                    , "totalVentaGeneral" => 0
                    , "porcentajeVentaGeneral" => 0
                    , "totalVentaAutoconsumo" => 0
                    , "porcentajeVentaAutoconsumo" => 0);
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }

    function cargarTotalRecargas($idRestaurante, $fechaDesde, $fechaHasta) {
        $this->lc_regs = array();
        $lc_sql = "EXEC fidelizacion.MONITOR_cargarPorcentajeRecargas $idRestaurante, '$fechaDesde', '$fechaHasta'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(utf8_encode($row['cajero']), round($row['total'], 2));
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }

    function cargarTopDiezProductosRedimidos($idRestaurante, $fechaDesde, $fechaHasta) {
        $this->lc_regs = array();
        $lc_sql = "EXEC fidelizacion.MONITOR_topDiezProductosRedimientos $idRestaurante, '$fechaDesde', '$fechaHasta'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(utf8_encode($row['Producto']), round($row['Total'], 2));
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }

    function cargarFormasPagoHora($idRestaurante, $fechaDesde, $fechaHasta) {
        $this->lc_regs = array();
        $lc_sql = "EXEC fidelizacion.MONITOR_cargarFormasPagoPorHora $idRestaurante, '$fechaDesde', '$fechaHasta'";
        $anterior = "0";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $datos = array();
            while ($row = $this->fn_leerarreglo()) {
                if ($anterior == $row['formaPago']) {
                    $datos[] = round($row['total'], 2);
                } else {
                    if ($anterior != "0") {
                        $this->lc_regs[] = array("name" => $anterior, "data" => $datos);
                    }
                    $datos = array();
                    $datos[] = round($row['total'], 2);
                    $anterior = $row['formaPago'];
                }
            }
            $this->lc_regs[] = array("name" => $anterior, "data" => $datos);
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }

}
