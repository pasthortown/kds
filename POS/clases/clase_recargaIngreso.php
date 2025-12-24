<?php

class Recargas extends sql {

    function impresionRecarga($idCadena, $idRestaurante, $idTransaccion) {
        $this->lc_regs = [];
        $lc_sql = "EXEC recargas.impresionRecarga '$idTransaccion', $idCadena, $idRestaurante;";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("head" => utf8_encode($row['head'])
                , "totales" => utf8_encode($row['totales'])
                , "firma" => utf8_encode($row['firma'])
                , "mensaje" => utf8_encode($row['mensaje']));
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function impresionConsumoRecarga($idCadena, $idRestaurante, $idTransaccion) {
        $this->lc_regs = [];
        $lc_sql = "EXEC recargas.impresionConsumoRecarga '$idTransaccion', $idCadena, $idRestaurante;";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("head" => utf8_encode($row['head'])
                , "totales" => utf8_encode($row['totales'])
                , "firma" => utf8_encode($row['firma'])
                , "mensaje" => utf8_encode($row['mensaje']));
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function impresionReversoConsumoRecarga($idCadena, $idRestaurante, $idTransaccion) {
        $this->lc_regs = [];
        $lc_sql = "EXEC recargas.impresionReversoConsumoRecarga '$idTransaccion', $idCadena, $idRestaurante;";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("head" => utf8_encode($row['head'])
                , "totales" => utf8_encode($row['totales'])
                , "firma" => utf8_encode($row['firma'])
                , "mensaje" => utf8_encode($row['mensaje']));
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function registroIngresoRecarga($IDSecuencial, $Codigo, $Mensaje, $ClienteDocumento, $Recarga, $TotalRecargado, $RecargaPromocional, $Puntos, $TotalPuntos, $IDCadena, $IDRestaurante, $IDTienda, $IDEstacion, $IDControlEstacion, $IDUsuario, $cliente) {
        $this->lc_regs = [];
        $lc_sql = "EXEC recargas.registrarRecargaCliente	'$IDSecuencial'
                                                                ,$Codigo
                                                                ,'$Mensaje'
                                                                ,'$ClienteDocumento'
                                                                ,$Recarga
                                                                ,$TotalRecargado
                                                                ,$RecargaPromocional
                                                                ,$Puntos
                                                                ,$TotalPuntos
                                                                ,$IDCadena
                                                                ,$IDRestaurante
                                                                ,'$IDTienda'
                                                                ,'$IDEstacion'
                                                                ,'$IDControlEstacion'
                                                                ,'$IDUsuario'
                                                                ,'$cliente'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("idSecuencial" => $row['IDSecuencial']
                , "idTransaccion" => $row['IDTransaccion']
                , "codigo" => $row['Codigo']
                , "mensaje" => utf8_encode($row['Mensaje'])
                , "clienteDocumento" => $row['ClienteDocumento']
                , "recarga" => $row['Recarga']
                , "totalRecargado" => $row['TotalRecargado']
                , "recargaPromocional" => $row['RecargaPromocional']
                , "puntos" => $row['Puntos']
                , "totalPuntos" => $row['TotalPuntos']);
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function obtenerSecuencialConsumoRecarga($idRestaurante) {
        $this->lc_regs = [];
        $lc_sql = "EXEC recargas.generaSecuencialCodigoConsumoRecarga $idRestaurante";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("secuencialRecarga" => $row['secuencialRecarga']);
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function registroConsumoRecarga($IDFactura, $IDUsuario, $TotalFactura, $Pagado, $SecuencialConsumo, $Cliente, $ClienteDocumento, $TotalPuntos, $TotalRecarga, $Codigo, $Mensaje, $IDCadena, $IDRestaurante, $IDEstacion) {
        $this->lc_regs = [];
        $lc_sql = "EXEC recargas.registrarConsumoRecargaCliente	'$IDFactura'
                                                                ,'$IDUsuario'
                                                                ,$TotalFactura
                                                                ,$Pagado
                                                                ,'$SecuencialConsumo'
                                                                ,'$Cliente'
                                                                ,'$ClienteDocumento'
                                                                ,$TotalPuntos
                                                                ,$TotalRecarga
                                                                ,$Codigo
                                                                ,'$Mensaje'
                                                                ,$IDCadena
                                                                ,$IDRestaurante
                                                                ,'$IDEstacion'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("total" => $row['Total']
                    , "descripcion" => utf8_encode($row['Descripcion']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    public function logProcesosRecargas($descripcion, $accion, $IDRestaurante, $IDCadena, $IDUsuario, $trama) {
        $lc_sql = "EXECUTE fidelizacion.I_Auditorias '$descripcion', '$accion', $IDRestaurante, $IDCadena, '$IDUsuario', '$trama'";
        $this->fn_ejecutarquery($lc_sql);
    }

    function cargarConsumosRecargaPorFactura($IDCadena, $IDFactura) {
        $this->lc_regs = [];
        $lc_sql = "EXEC recargas.cargarConsumosRecargaPorFactura $IDCadena, '$IDFactura'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idFormaPagoFactura" => $row['idFormaPagoFactura']
                    , "valor" => $row['valor']
                    , "secuencialConsumo" => $row['secuencialConsumo']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function cargarConsumosPickupPorFactura($IDCadena, $IDFactura) {
        $this->lc_regs = [];
        $lc_sql = "EXEC [config].[Pickup_cargarConsumosPickUpPorFactura] $IDCadena, '$IDFactura'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idFormaPagoFactura"    => $row['idFormaPagoFactura']
                    , "valor"               => $row['valor']
                    , "secuencialConsumo"   => $row['secuencialConsumo']
                    , "formapago"           => $row['formapago']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    public function cancelarFormaPagoConsumoRecarga($idFormaPagoFactura, $idFactura, $idUsuario, $idRestaurante, $idCadena, $idEstacion, $codigo, $mensaje, $secuencial, $cliente, $documentoCliente) {
        $lc_sql = "EXECUTE recargas.cancelarConsumosRecargaPorFactura  '$idFormaPagoFactura'
                                                                        ,'$idFactura'
                                                                        ,'$idUsuario'
                                                                        ,$idRestaurante
                                                                        ,$idCadena
                                                                        ,'$idEstacion'
                                                                        ,$codigo
                                                                        ,'$mensaje'
                                                                        ,'$secuencial'
                                                                        ,'$cliente'
                                                                        ,'$documentoCliente'";
        $this->fn_ejecutarquery($lc_sql);
    }

    function validarAutorizacionRecargas($idRestaurante, $idUsuario, $proceso) {
        $this->lc_regs = [];
        $lc_sql = "EXEC fidelizacion.validarAutorizacion $idRestaurante, '$proceso', '$idUsuario'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array('proceso' => $row['proceso'],
                                    'secuencia' => $row['secuencia'],
                                    'cashierDocument' => $row['cashierDocument'],
                                    'cashierName' => utf8_encode($row['cashierName']));
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }
    
    public function finalizarAutorizacionRecargas($idRestaurante, $secuencia, $proceso) {
        $lc_sql = "EXEC fidelizacion.finalizarAutorizacion $idRestaurante, '$proceso', '$secuencia'";
        $this->fn_ejecutarquery($lc_sql);
    }
    
    public function pendienteAutorizacionRecargas($idRestaurante, $secuencia, $proceso) {
        $lc_sql = "EXEC fidelizacion.estadoPendienteAutorizacion $idRestaurante, '$proceso', '$secuencia'";
        $this->fn_ejecutarquery($lc_sql);
    }

}