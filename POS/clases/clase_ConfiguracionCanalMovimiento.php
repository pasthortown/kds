<?php

/*
FECHA CREACION   : 04/10/2016 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion de promociones
*/

class ConfiguracionCanalMovimiento extends sql {
    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }   
        
    function fn_cargarDetalle($lc_datos){
        $lc_sql = "EXEC [config].[CANALMOVIMIENTO_USP_Administracion] ".$lc_datos[0].", ".$lc_datos[1];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("IDConfiguracionCanalMovimiento" => $row['IDConfiguracionCanalMovimiento'],
                                         "Valor" => utf8_encode($row['Valor']),
                                         "Descripcion" => utf8_encode($row['Descripcion']),
                                         "Codigo" => utf8_encode($row['Codigo']),
                                         "Estado" => $row['Estado']);
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }
    
    function fn_guardarRegistro($lc_datos){
        $lc_sql = "EXEC [config].[CANALMOVIMIENTO_IAE_Administracion] ".$lc_datos[0].", '".$lc_datos[1]."', '".$lc_datos[2]."', '".$lc_datos[3]."', ".$lc_datos[4].", '".$lc_datos[5]."', '".$lc_datos[6]."'";
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result){ return true; }else{ return false; };
    } 
}


