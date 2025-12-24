<?php

class AuditoriaCreditos extends sql
{

    function guardarLogCreditos($descripcion, $accion, $modulo, $idRestaurante, $idCadena, $idUsuario, $trama)
    {
        $this->lc_regs = Array();
        $lc_sql = "EXEC creditos.I_Auditorias'$descripcion', '$accion','$modulo', $idRestaurante, $idCadena,'$idUsuario', '$trama'";

        try {
            $this->fn_ejecutarquery($lc_sql);
            $this->lc_regs = array("mensaje" => utf8_encode("Registro Exitoso"), "estado" => 1);
        } catch (Exception $e) {
            return $this->lc_regs = array("mensaje" => utf8_encode($e->getMessage()), "estado" => 0);
        }
        $result = $this->lc_regs;
        return $result;
    }

    function validarAutorizacionVitality($idRestaurante, $idUsuario, $proceso)
    {
        $this->lc_regs = [];
        $lc_sql = "EXEC [creditos].[validarAutorizacion] $idRestaurante, '$proceso', '$idUsuario'";
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


    public function finalizarAutorizacionVitality($idRestaurante, $secuencia, $proceso)
    {
        $lc_sql = "EXEC creditos.finalizarAutorizacion $idRestaurante, '$proceso', '$secuencia'";
        $this->fn_ejecutarquery($lc_sql);
    }

    public function pendienteAutorizacionVitality($idRestaurante, $secuencia, $proceso)
    {
        $lc_sql = "EXEC creditos.estadoPendienteAutorizacion $idRestaurante, '$proceso', '$secuencia'";
        $this->fn_ejecutarquery($lc_sql);
    }

    public function ingresarCodigoVitalityFactura($codigoFactura, $codigoVitality, $respuesta)
    {
        $lc_sql = "EXEC creditos.IAE_CodigoVitalityFactura '$codigoFactura', '$codigoVitality','$respuesta' ";
        $this->fn_ejecutarquery($lc_sql);
    }


}