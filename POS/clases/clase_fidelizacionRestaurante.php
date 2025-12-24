<?php

class Restaurante extends sql {
	
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
    
    function guardarConfiguracionRestaurante($idUsuario, $idCadena, $idRestaurante, $latitud, $longitud, $aplicaPlan,$imprimePuntosRide) {
        $lc_sql = "EXEC fidelizacion.IAE_Restaurante '$idUsuario', $idCadena, $idRestaurante, '$latitud', '$longitud', $aplicaPlan , $imprimePuntosRide" ;
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

    function cargarListaRestaurantes($idCadena) {
        $lc_sql = "EXEC fidelizacion.ConsultasGenerales 5, " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[$row['idRestaurante']] = array("idTienda" => $row['idTienda'], "nombre" => utf8_encode($row['nombre']), "descripcion" => utf8_encode($row['descripcion']));
            }
            if($this->fn_numregistro() == 0) {
                $this->lc_regs = null;
            }  
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }
    
}

