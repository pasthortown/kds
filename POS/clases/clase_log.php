<?php
class Log extends sql {
    function __construct() {
        parent::__construct();
    }

    public function registrarLog($restaurante, $modulo, $descripcion, $accion) {
        $validate = true;
        $this->lc_regs = Array();

        try{
            $lc_sql = "EXEC [seguridad].[IAE_Auditoria_Transaccion] @rst_id='".$restaurante."',
                                                                @atran_modulo='".$modulo."',
                                                                @atran_descripcion='".$descripcion."',
                                                                @atran_accion='".$accion."'";
            if (!$this->fn_ejecutarquery($lc_sql)) {
                throw new Exception('Error');
            }

            $this->lc_regs = $this->fn_leerarreglo();

            if(!isset($this->lc_regs['IDAuditoriaTransaccion'])) {
                throw new Exception('Error');
            }

            $validate = true;
        } catch (Exception $e) {
            $validate = false;
        }

        return $validate;
    }
}
