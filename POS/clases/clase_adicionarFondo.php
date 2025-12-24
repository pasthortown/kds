<?php

class AdicionarFondo extends sql 
{
    //constructor de la clase
    function __construct() 
    {    
        //con herencia 
        parent::__construct();
    }
    
    function fn_ingresarAdicionFondoCaja($lc_datos) 
    {
        $lc_sql = "EXEC [facturacion].[IAE_grabaAdicionFondo]  '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[3]','$lc_datos[4]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $this->lc_regs['str'] = 1;
        } else {
            $this->lc_regs['str'] = 0;
        }
        return json_encode($this->lc_regs);
    }
    
    function validaCajeroActivo($lc_datos) 
    {
       $lc_sql = "exec [seguridad].[USP_validaCajeroActivo] '$lc_datos[2]','$lc_datos[0]','$lc_datos[1]'";
        if ($this->fn_ejecutarquery($lc_sql)) 
        {
            while ($row = $this->fn_leerarreglo()) 
            {
                $this->lc_regs[] = array("controlCajero" => $row['controlCajero'],"mensaje" => $row['mensaje']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            
        }
        else {
            $this->lc_regs['str'] = 0;
        }
        return json_encode($this->lc_regs);
        $this->fn_liberarecurso();
    }
}

