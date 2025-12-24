<?php

class Producto extends sql {
	
    function cargarConfiguracionProductos($idCadena) {
        $lc_sql = "EXEC fidelizacion.ConsultasGenerales 0, " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idProducto" => $row['idProducto']
                        ,"numProducto" => $row['numProducto']
                        ,"puntos" => $row['puntos']
                        ,"aplicaPlan" => $row['aplicaPlan']
                        ,"nombreImpresion" => utf8_encode($row['nombreImpresion'])
                        ,"descripcionProducto" => utf8_encode($row['descripcionProducto'])
                        ,"nombreProducto" => utf8_encode($row['nombreProducto']));
            }
            //$this->lc_regs['str'] = $this->fn_numregistro();
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
    
    
    function guardarConfiguracionProductos($idUsuario, $idCadena, $idProducto, $descripcion, $puntos, $aplicaPlan) {
        $lc_sql = "EXEC [fidelizacion].[IAE_Productos] '$idUsuario', $idCadena, $idProducto, '$descripcion', $puntos, $aplicaPlan";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("mensaje" => $row['mensaje'], "estado" => $row['estado']);
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }
            
}

