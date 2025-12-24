<?php

class Modificador extends sql {

    function __construct() {
        parent ::__construct();
    }
    
    function cargarListaModificadores($accion) {
        $this->lc_regs = array();
        $lc_sql = "EXEC config.PRODUCTOS_modificadores 0";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idModificador" => $row['idModificador'], "Estado" => $row['Estado'], "Modificador" => utf8_encode($row['Modificador']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }
    
    function actualizarListaModificadores($accion, $modificadores, $idUsuario) {
        $this->lc_regs = array();
        $lc_sql = "EXEC config.PRODUCTOS_IA_modificadores $accion, '$modificadores', '$idUsuario'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idModificador" => $row['idModificador'], "Estado" => $row['Estado'], "Modificador" => utf8_encode($row['Modificador']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

}
