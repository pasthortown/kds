<?php
if(file_exists("../../system/conexion/clase_sql.php")){
    require_once("../../system/conexion/clase_sql.php");

}elseif("../system/conexion/clase_sql.php"){
    require_once("../system/conexion/clase_sql.php");
}



class PoliticasTokens extends sql {

    function obtenerValorParametroSirApiIntegracion($cadena, $parametro) {
        $lc_sql = "EXEC [config].[USP_Sir_Api_Integracion] '1',$cadena,'$parametro'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "valorParametro" => trim($row['valorParametro'])
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }
}

?>