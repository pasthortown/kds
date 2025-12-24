<?php

/*
MODIFICADO POR  : José Fernández
DESCRIPCION     : Administracion de MotivoDeIngresosyEgresosDeDeposito
TABLAS          : MotivoDeIngresosyEgresosDeDeposito
FECHA CREACION  : 04-10-2016
*/

class AdminConceptosDepositos extends sql 
{

    function __construct() 
    {
        parent ::__construct();
    }

    public function fn_cargaDetalleConceptosDepositos($lc_datos) 
    {
        $lc_query = "EXECUTE [config].[USP_adminIngresosEgresosDeposito] '$lc_datos[0]'";
        if ($this->fn_ejecutarquery($lc_query)) 
            {
            while ($row = $this->fn_leerarreglo()) 
                {
                    $this->lc_regs[] = array("idConcepto" => $row['idConcepto'],"descripcionConcepto" => utf8_encode($row['descripcionConcepto']), "signo" => $row['signo'],"estado" => $row['estado']);
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
            }
        return json_encode($this->lc_regs);
    }

    public function fn_cargarNuevo($lc_datos) 
    {
        $lc_query = "EXECUTE config.USP_iprestaurante $lc_datos[0],$lc_datos[1]";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['Restaurante'] = $row["Restaurante"];
                $this->lc_regs['primer_octeto_ip'] = $row["primer_octeto_ip"];
                $this->lc_regs['segundo_octeto_ip'] = $row["segundo_octeto_ip"];
                $this->lc_regs['tercer_octeto_ip'] = $row["tercer_octeto_ip"];
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }
    

    public function fn_guardaConceptosDepositos($lc_condiciones) 
    {
        $lc_query = "EXECUTE [config].[IAE_ingresosEgresosDepositos] '$lc_condiciones[0]','$lc_condiciones[1]','$lc_condiciones[2]','$lc_condiciones[3]','$lc_condiciones[4]','$lc_condiciones[5]'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return true; }else{ return false; };
    }
}