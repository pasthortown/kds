<?php

class turnero extends sql {

    function __construct() {
        parent ::__construct();
    }

    function cargarConfiguraciones($idCadena, $idRestaurante) {
        $lc_sql = "EXEC config.USP_cargaConfiguracionTurnero " . $idCadena . ", " . $idRestaurante;
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array($row["descripcion"] => $row["valor"]);
            }
            $this->lc_regs["str"] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        return null;
    }

}