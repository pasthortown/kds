<?php

class Cliente extends sql {
    
    // function guardarLog($descripcion, $accion, $idRestaurante, $idCadena, $idUsuario, $trama) {
    //     $this->lc_regs = Array();
    //     $lc_sql = "EXEC fidelizacion.I_Auditorias '$descripcion', '$accion', $idRestaurante, $idCadena, '$idUsuario', '$trama'";
    //     try {
    //         $this->fn_ejecutarquery($lc_sql);
    //         $this->lc_regs = array("mensaje" => utf8_encode("Registro Exitoso"), "estado" => 1);
    //     } catch (Exception $e) {
    //         return $this->lc_regs = array("mensaje" => utf8_encode($e->getMessage()), "estado" => 0);
    //     }
    //     $result = $this->lc_regs;
    //     return $result;
    // }
    
    // function actualizarFacturaLog($idFactura, $accion, $puntos, $mensaje) {
    //     $this->lc_regs = Array();
    //     $lc_sql = "EXEC fidelizacion.IAE_ActualizaCabeceraFacturaError '$idFactura', $accion, $puntos, '$mensaje'";
    //     try {
    //         $this->fn_ejecutarquery($lc_sql);
    //         $this->lc_regs = array("mensaje" => utf8_encode("Registro Exitoso"), "estado" => 1);
    //     } catch (Exception $e) {
    //         return $this->lc_regs = array("mensaje" => utf8_encode($e->getMessage()), "estado" => 0);
    //     }
    //     $result = $this->lc_regs;
    //     return $result;
    // }
    
    
    //     function actualizarNotaCreditoLog($idFactura, $accion, $puntos, $mensaje) {
    //     $this->lc_regs = Array();
    //     $lc_sql = "EXEC fidelizacion.IAE_ActualizaCabeceraNotaCreditoError '$idFactura', $accion, $puntos, '$mensaje'";
    //     try {
    //         $this->fn_ejecutarquery($lc_sql);
    //         $this->lc_regs = array("mensaje" => utf8_encode("Registro Exitoso"), "estado" => 1);
    //     } catch (Exception $e) {
    //         return $this->lc_regs = array("mensaje" => utf8_encode($e->getMessage()), "estado" => 0);
    //     }
    //     $result = $this->lc_regs;
    //     return $result;
    // }
	
    /*
    function cargarConfiguracionRestaurante($idCadena) {
        $lc_sql = "EXEC fidelizacion.ConsultasGenerales 1, " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idRestaurante" => $row['idRestaurante']
                        ,"aplicaPlan" => $row['aplicaPlan']
                        ,"latitud" => $row['latitud']
                        ,"longitud" => $row['longitud']
                        ,"idTienda" => $row['idTienda']
                        ,"nombre" => utf8_encode($row['nombre']));
            }
            if($this->fn_numregistro() > 0){
                $result['data'] = $this->lc_regs;
            } else {
                $result['data'] = null;
            }
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }
    
    function guardarConfiguracionRestaurante($idUsuario, $idCadena, $idRestaurante, $latitud, $longitud, $aplicaPlan) {
        $lc_sql = "EXEC fidelizacion.IAE_Restaurante '$idUsuario', $idCadena, $idRestaurante, '$latitud', '$longitud', $aplicaPlan";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("mensaje" => utf8_encode($row['mensaje']), "estado" => $row['estado']);
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }
    */
    
}

