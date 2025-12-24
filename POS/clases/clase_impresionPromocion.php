<?php

class impresionPromocion extends sql
{
    //constructor de la clase
    function __construct() 
    {
        parent ::__construct();
    }
    
    function fn_impresionPromocion($lc_datos)
    {
        $lc_query="EXEC [facturacion].[PROMOCIONES_USP_ImpresionDinamicaPromocion] '$lc_datos[0]', '$lc_datos[1]'"; 
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return true; }else{ return false; };
    }
    
}


