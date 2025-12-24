<?php

class CampanaSolidaria extends sql 
{
    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case "aplicaCampanaSolidaria":
                $lc_sql = "EXEC [config].[campana_solidaria_validar_siaplica] $lc_datos[0],$lc_datos[1]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs = array('aplica' => $row['aplica'], 'valor' => $row['valor'], 'secuencia' => $row['secuencia'], 'cantidadLimite' => $row['cantidadLimite']);
                    }
                }

                return json_encode($this->lc_regs);
            break;

            case "registrarCampanaSolidaria":
                $lc_sql = "EXEC [config].[campana_solidaria_registrar] $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3],$lc_datos[4],'$lc_datos[5]','$lc_datos[6]','$lc_datos[7]','$lc_datos[8]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs = array(
                            'estado' => intval($row['estado']),
                            'codigo' => $row['codigo']
                        );
                    }
                }

                return json_encode($this->lc_regs);
            break;

            case "anularCampanaSolidaria":
                $lc_sql = "EXEC [config].[campana_solidaria_anular] '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs = array(
                            'estado' => $row['estado']
                        );
                    }
                }

                return json_encode($this->lc_regs);
            break;
        }
    }

    function impresionCampanaSolidaria($idTransaccion, $idCadena, $idRestaurante) {
        $data = array(
            "head" => '',
            "totales" => '',
            "firma" => '',
            "mensaje" => '',
        );

        $lc_sql = "EXEC [config].[campana_solidaria_imprimirHTML] '$idTransaccion', $idCadena, $idRestaurante;";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();

            if(isset($row)) {
                $data['head'] = utf8_encode($row['head']);
                $data['totales'] = utf8_encode($row['totales']);
                $data['firma'] = utf8_encode($row['firma']);
                $data['mensaje'] = utf8_encode($row['mensaje']);
            }
            
            $result = $data;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }
}