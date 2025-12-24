<?php


class NotaCreditoCliente extends sql {
    
    function __construct() {
        parent::__construct();
    }
 
    function fn_generarReportetxt($lc_datos) {
        $lc_sql = "EXEC [reporte].[BACK_USP_NotaCreditoCliente] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]'";
        
        if ($this->fn_ejecutarquery($lc_sql)) {
            $this->lc_regs['Respuesta'] = 1;
        } else {
            $this->lc_regs['Respuesta'] = 0;
        }
        $this->lc_regs['Registros'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }
    
    function fn_cargaClientes($lc_datos) {
        $lc_sql = "EXEC [reporte].[BACK_USP_NotaCreditoCliente] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]'";

        if($this->fn_ejecutarquery($lc_sql)) { 
            while($row = $this->fn_leerarreglo()) {					
                $this->lc_regs[] = array(
                    "Documento"=>$row['Documento'],
                    "Cliente"=>htmlentities(trim(utf8_encode($row['Cliente'])))
                );
            }	
                $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function obtener_transacciones($param) {
        $lc_sql = "EXEC [reporte].[BACK_USP_NotaCreditoCliente] 'N', '$param[1]', '$param[2]', '$param[3]', '$param[4]'";

        if($this->fn_ejecutarquery($lc_sql)) { 
            while($row = $this->fn_leerarreglo()) {					
                $this->lc_regs[] = array(
                    "existe_datos"          => $row['existe_datos']
                    , "json_notas_credito"  => utf8_encode($row['json_notas_credito'])
                    , "token"               => $row['token']
                );
            }	
            
            $this->lc_regs['str'] = $this->fn_numregistro();
        }

        return ($this->lc_regs);    
    }

    function aplica_web_Services($param) {
        $lc_sql = "EXEC [reporte].[BACK_USP_NotaCreditoCliente] 'P', '$param', '', '', ''";

        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("aplica_ws" => $row["aplica_ws"]);
        } catch (Exception $e) {
            return $e;
        }

        return $this->lc_regs;
    }                     
}
