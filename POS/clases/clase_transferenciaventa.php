<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Francisco Sierra//////////////////////
////////DESCRIPCION: Funciones para Transferencia de venta//////
/////////////////////En las Cajas/////////////////////////////
//////////////////////////////////////////////////////////////
///////FECHA CREACION: 10-Enero-2017////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 

class TransferenciaVenta extends sql {
    //constructor de la clase
    function __construct()
    {
	//con herencia 
	parent::__construct();
    }
    
    function fn_encuentra_bdd($parametros){
        $this->lc_regs = array ();
        $lc_sql = "EXEC facturacion.USP_TRANSFERENCIAVENTA_DatosRestaurante 2,$parametros[0],$parametros[1]";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array(
                    "NombreBdd"=>$row['NombreBdd'],
                    "Cadena"=>$row['cdn_id'],
                    "Restaurante"=>$row['rst_id']
                    );	
            }
            $this->lc_regs['str']=$this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
            return $this->lc_regs;
    }  
    
    function fn_encuentra_periodo_relacionado($parametros){
        $this->lc_regs = array ();
        $lc_sql = "EXEC facturacion.USP_TRANSFERENCIAVENTA_PeriodoRelacionado '$parametros[0]'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array(
                    "PeriodoRelacionado"=>$row['PeriodoRelacionado']
                    );	
            }
            $this->lc_regs['str']=$this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
            return $this->lc_regs;
    }
    
    function fn_encuentra_numeroTransaccionesOrigenUsuario($parametros){
        $this->lc_regs = array ();
        $lc_sql = "EXEC [facturacion].[USP_TransferenciaVenta_numeroTransaccionesUsuario] '$parametros[3]','$parametros[2]'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array(
                    "txHeladeriaUsuario"=>$row['txHeladeriaUsuario']
                    );	
            }
            $this->lc_regs['str']=$this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
            return $this->lc_regs;
    }
    
    function fn_inyecta_valoresVentaTransferencia($parametros)
    {        
        $lc_sql = "EXEC facturacion.IAE_TRANSFERENCIAVENTA_valoresVentaTransferencia '$parametros[0]','$parametros[1]','$parametros[2]','$parametros[3]'";
        //try {
            $this->fn_ejecutarquery($lc_sql);            
           // }            
        /*} catch (Exception $e) {
            return json_encode($e);
        }*/
            
    }
}