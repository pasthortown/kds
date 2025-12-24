<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of clase_facturacionServicios
 *
 * @author jose.fernandez
 */
class clase_facturacionServicios extends sql 
{
   function __construct()
   {	
	parent::__construct();
   }
   
   public function fn_consultaUrlWs($lc_datos)
   {
       $lc_sql="EXECUTE [facturacion].[USP_consultaURLWs] $lc_datos[1],$lc_datos[0],'$lc_datos[2]'";
                    if($this->fn_ejecutarquery($lc_sql)) 
			{ 
                            while($row = $this->fn_leerarreglo()) 
                            {									
                                $this->lc_regs[] = 
                                    array(
                                        "url"=>$row['url']);                                        
                            }	                            
                            return $this->lc_regs;
                        }
   }
}
