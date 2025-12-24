<?php

class Auditoria extends sql 
{
    //constructor de la clase
    function __construct() 
    {    
        //con herencia 
        parent::__construct();
    }
    
    function fn_guardar_auditoria($lc_datos) 
    {
                               
        $lc_sql = "EXEC seguridad.Usp_Insertar_Auditoria_Ruc '$lc_datos[4]','$lc_datos[1]','$lc_datos[0]','$lc_datos[3]','$lc_datos[2]'";
        $result = $this->fn_ejecutarquery($lc_sql);
                 
    }
    
    
}

