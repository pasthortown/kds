<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . "..") . $ds;

include_once "{$base_dir}{$ds}../../system{$ds}conexion{$ds}clase_sql.php";


class Utilitario extends sql
{



    public function retornarMedio($cfac_id)
    {
        $lc_sql = "select dbo.fn_ObtenerMedioCabeceraFactura('$cfac_id') as Cabecera_FacturaVarchar2";

        $this->lc_regs = [];

        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array("Cabecera_FacturaVarchar2" => $row['Cabecera_FacturaVarchar2']);
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }

        
        return $result;
    }


    function obtenerEstadoHomologado($estado)
    {

        $lc_sql = "EXEC dbo.App_homologar_cambios_estado '$estado'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array("estado" => $row['estado']);
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function consultarFacturaValidaDuna($cfac_id)
    {
        $lc_sql = "EXEC config.USP_ObtenerConfiguracionCambioEstadoAutomatico '$cfac_id'";
        $this->lc_regs = [];
        
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array(
                    "cambio_estado_automatico" => $row['cambio_estado_automatico'],
                    "nombre_proveedor" => $row['nombre_proveedor']
                );
            }
        } catch (Exception $e) {
            return $e;
        }
        $result = $this->lc_regs;
        return $result;
    }
}
