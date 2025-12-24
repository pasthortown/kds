<?php
class ReplicarAmbiente extends SqlMultiple {

    function __construct($ambiente) {
        parent ::__construct($ambiente);
    }

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

    public function cargarModulos($idCadena) {
        $query = "EXEC replica.REPLICA_configuraciones 0, " . $idCadena;
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array(
                    'descripcion' => utf8_encode($row['descripcion']),
                    'idModulo' => $row['idModulo']
                );
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    public function cargarLotesReplicaAzure($idCadena, $fechaDesde, $fechaHasta, $idModulo, $idEstados, $cantidadEstados) {
        $query = "EXEC replica.REPLICA_cargarReplicas 0, " . $idCadena . ", '" . $fechaDesde . "', '" . $fechaHasta . "', " . $idModulo . ", '" . json_encode($idEstados) . "', " . $cantidadEstados . ", 0";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array(
                    'idLote' => $row['idLote'],
                    'fechaLote' => $this->procesarFecha($row['fechaLote']),
                    'horaLote' => $this->procesarHora($row['horaLote']),
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

    public function cargarUpdateStoreAzure($idCadena, $idModulo, $idLote) {
        $query = "EXEC replica.REPLICA_cargarReplicas 1, " . $idCadena . ", '', '', " . $idModulo . ", '', 0, " . $idLote;
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

    public function confirmarCantidadReplicacionAzure($idCadena, $idModulo) {
        $query = "EXEC replica.REPLICA_cargarReplicas 2, " . $idCadena . ", '', '', " . $idModulo . ", '', 0, 0";
        try {
            $this->fn_ejecutarquery($query);
            $row = $this->fn_leerarreglo();
            $result = array('cantidad' => $row['cantidad']);
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    public function replicacionAzure($idCadena, $idModulo) {
        $query = "EXEC dbo.USP_ReplicacionDownDistribuidorPorModulo " . $idCadena . ", " . $idModulo;
        try {
            $this->fn_ejecutarquery($query);
            if ($this === false) {
                $result = array('resultado' => 'No se pudo transmitir');
            } else {
                $row = $this->fn_leerarreglo();
                $result = array('resultado' => $row['Respuesta']);
            }
        } catch (Exception $e) {
            $result = array('resultado' => json_encode($e));
        }
        return json_encode($result);
    }

    public function cargarLotesReplicaDistribuidor($idCadena, $fechaDesde, $fechaHasta, $idModulo, $idEstados, $cantidadEstados) {
        $query = "EXEC replica.REPLICA_cargarReplicas 0, " . $idCadena . ", '" . $fechaDesde . "', '" . $fechaHasta . "', " . $idModulo . ", '" . json_encode($idEstados) . "', " . $cantidadEstados . ", 0, 0,0";
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

    public function verificarLotesPendientesDistribuidor($idCadena, $fechaDesde, $fechaHasta) {
        $query = "EXEC replica.REPLICA_cargarReplicas 3, " . $idCadena . ", '" . $fechaDesde . "', '" . $fechaHasta . "', 0, '', 0, 0, 0,0";
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

    public function cargarTUpdateStoreDistribuidor($idCadena, $idLote)
    {
        $query = "EXEC replica.REPLICA_cargarReplicas 4, " . $idCadena . ", '', '', 0, '', 0, " . $idLote . ", 0,0";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array(
                    'TotalTramasLocal' => $row['TotalTramasLocal'],
                    'restauranteUpdateStore' => utf8_encode($row['restauranteUpdateStore']),
                    'idRestaurante' => $row['idRestaurante'],
                    'estado' => $row['estado']
                );
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    public function cargarUpdateStoreDistribuidor($idCadena, $idLote, $idRestaurante)
    {
        $query = "EXEC replica.REPLICA_cargarReplicas 1, " . $idCadena . ", '', '', 0, '', 0, " . $idLote . ", 0,$idRestaurante";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array(
                    'idUpdateStore' => $row['idUpdateStore'],
                    'fechaUpdateStore' => $this->procesarFecha($row['fechaUpdateStore']),
                    'horaUpdateStore' => $this->procesarHora($row['horaUpdateStore']),
                    'tablaUpdateStore' => utf8_encode($row['tablaUpdateStore']),
                    'tramaUpdateStore' => utf8_encode($row['tramaUpdateStore']),
                    'restauranteUpdateStore' => utf8_encode($row['restauranteUpdateStore']),
                    'estado' => trim(utf8_encode($row['ESTADO'])),
                    'errornumber' => trim($row['ERRORNUMBER']),
                    'errormessage' => utf8_encode($row['ERRORMESSAGE']),
                );
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    public function cargarUpdateStoreTiendasDistribuidor($idCadena, $idUpdateStore) {
        $query = "EXEC replica.REPLICA_cargarReplicas 2, " . $idCadena . ", '', '', 0, '', 0, 0, " . $idUpdateStore;
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array(
                    'idUpdateStoreTiendas' => $row['idUpdateStoreTiendas'],
                    'fechaUpdateStoreTiendas' => $this->procesarFecha($row['fechaUpdateStoreTiendas']),
                    'horaUpdateStoreTiendas' => $this->procesarHora($row['horaUpdateStoreTiendas']),
                    'tablaUpdateStoreTiendas' => utf8_encode($row['tablaUpdateStoreTiendas']),
                    'tramaUpdateStoreTiendas' => utf8_encode($row['tramaUpdateStoreTiendas']),
                    'restauranteUpdateStoreTiendas' => utf8_encode($row['restauranteUpdateStoreTiendas']),
                    'estadoUpdateStoreTiendas' => $row['estadoUpdateStoreTiendas']
                );
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    public function aplicarReplicacionDistribuidor($idCadena, $lote) {
        $query = "EXEC dbo.USP_EjecutaLoteReplicaDistribuidor " . $idCadena . ", '" . $lote . "'";
        try {
            $resultado = $this->fn_ejecutarquery2($query);
        } catch (Exception $e) {
            $resultado = new stdClass();
            $resultado->estado = false;
            $resultado->errores[] = $e->getMessage();
        }
        return $resultado;
    }

    public function transmitirReplicacionDistribuidor($idCadena, $lote) {
        $query = "EXEC dbo.USP_ReplicacionDownTiendasPorLote " . $idCadena . ", '" . $lote . "'";
        try {
            $resultado = $this->fn_ejecutarquery2($query);
        } catch (Exception $e) {
            $resultado = new stdClass();
            $resultado->estado = false;
            $resultado->errores[] = json_encode($e);
        }
        return $resultado;
    }

    public function desactivarLoteReplica($datos)
    {
        $query = "EXEC replica.IAE_Desactivar_Lote_Distribuidor @idCadena = " . $datos["idCadena"] . ", @numeroLote = '" . $datos["numeroLote"] . "', @usuarioCambio = '" . $datos["usuarioLogueado"] . "', @observacion = '" . $datos["observacion"] . "'";
        try {
            $resultado = $this->fn_ejecutarquery2($query);
        } catch (Exception $e) {
            $resultado = new stdClass();
            $resultado->estado = false;
            $resultado->errores[] = json_encode($e);
        }
        return $resultado;
    }
    public function pingMonitoreoLinkedServers($idCadena)
    {
        $query = "EXEC dbo.USP_PingMonitoreoLinkedServers @idCadena = " . $idCadena;

        //$query = "EXEC dbo.USP_EjecutaLoteReplicaDistribuidorr";
        try {
            $resultado = $this->fn_ejecutarquery2($query);
        } catch (Exception $e) {
            $resultado = new stdClass();
            $resultado->estado = false;
            $resultado->errores[] = json_encode($e);
        }
        return $resultado;
    }

    public function cargarInformacionLotes($idCadena, $fechaDesde, $fechaHasta, $idModulo){
        ini_set('max_execution_time', 300000);
        ini_set("memory_limit", 8192);
        $query = "EXEC replica.USP_InformacionLotes " . $idCadena . ", '" . $fechaDesde . "', '" . $fechaHasta . "', " . $idModulo ;
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array(
                    'cod_tienda' => utf8_encode($row['cod_tienda']),
                    'lote' => utf8_encode($row['lote']),
                    'modulo' => utf8_encode($row['modulo']),
                    'estado' => utf8_encode($row['estado']),
                    'fecha' => $row['fecha'],
                    'servidor' => utf8_encode($row['servidor'])
                );
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

    public function CargarIpsLocales($idRestaurante)
    {
        $query = "EXEC config.USP_CargarIpsRestaurantes " . $idRestaurante;
        //$query = "EXEC dbo.USP_EjecutaLoteReplicaDistribuidorr";
        try {
            $this->fn_ejecutarquery($query);
            while ($row = $this->fn_leerarreglo()) {
                $result[] = array(
                    'ip' => $row['ip']
                );
            }
            $result['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($result);
    }

}
