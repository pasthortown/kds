<?php

class ReplicarLocal extends sql {

    public function procesarFecha($fecha) {
        if (is_null($fecha)) {
            return '';
        } else {
            return date_format($fecha, "d/m/Y");
        }
    }

    public function procesarHora($hora) {
        if (is_null($hora)) {
            return '';
        } else {
            return date_format($hora, "H:i:s");
        }
    }

    public function cargarModulos($idCadena) {
        $query = "EXEC replica.REPLICA_configuraciones 0, " . $idCadena;
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array(
                    "descripcion" => utf8_encode($row['descripcion']),
                    "idModulo" => $row['idModulo']
                );
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    public function cargarEstados() {
        $query = "EXEC replica.REPLICA_configuraciones 1, 0";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array(
                    'idEstado' => $row['idEstado'],
                    'estado' => utf8_encode($row['estado'])
                );
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    public function cargarLotesReplicaTienda($idCadena, $fechaDesde, $fechaHasta, $idModulo, $idEstados, $cantidadEstados) {
        $query = "EXEC replica.REPLICA_cargarReplicas 0, " . $idCadena . ", '" . $fechaDesde . "', '" . $fechaHasta . "', " . $idModulo . ", '" . json_encode($idEstados) . "', " . $cantidadEstados . ", 0";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array(
                    'idLote' => $row['idLote'],
                    'fechaHoraLote' => $row['fechaHoraLote'],
                    'numeroLote' => $row['numeroLote'],
                    'idModuloLote' => $row['idModuloLote'],
                    'moduloLote' => utf8_encode($row['moduloLote']),
                    'estadoLote' => utf8_encode($row['estadoLote'])
                );
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    public function verificarLotesPendientesTienda($idCadena, $fechaDesde, $fechaHasta) {
        $query = "EXEC replica.REPLICA_cargarReplicas 2, " . $idCadena . ", '" . $fechaDesde . "', '" . $fechaHasta . "', 0, '', 0, 0";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array('fechaLote' => $this->procesarFecha($row['fechaLote']));
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    public function cargarUpdateStoreTienda($idCadena, $idLote) {
        $query = "EXEC replica.REPLICA_cargarReplicas 1, " . $idCadena . ", '', '', 0, '', 0, " . $idLote;
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array(
                    'idUpdateStore' => $row['idUpdateStore'],
                    'fechaUpdateStore' => $this->procesarFecha($row['fechaUpdateStore']),
                    'horaUpdateStore' => $this->procesarHora($row['horaUpdateStore']),
                    'tablaUpdateStore' => utf8_encode($row['tablaUpdateStore']),
                    'tramaUpdateStore' => utf8_encode($row['tramaUpdateStore']),
                    'restauranteUpdateStore' => utf8_encode($row['restauranteUpdateStore'])
                );
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    public function replicacionTienda($lote) {
        $query = "EXEC dbo.USP_EjecutaLoteReplicacionTiendas '" . $lote . "'";
        try {
            $this->fn_ejecutarquery($query);
            if ($this === false) {
                $result = array('resultado' => 'No se pudo aplicar');
            } else {
                $row = $this->fn_leerarreglo();
                $result = array('resultado' => $row['Respuesta']);
            }
        } catch (Exception $e) {
            $result = array('resultado' => json_encode($e));
        }
        return json_encode($result);
    }

}
