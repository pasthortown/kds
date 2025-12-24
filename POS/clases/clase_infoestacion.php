<?php

/////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de menú /////////////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 20-02-2015 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 21/12/2015///////////////////////////////////
///////USUARIO QUE MODIFICO: Christian Pinto //////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Menu en Max Point Coleccion de Datos Estacion/
///////////////////////////////////////////////////////////////////////////////

class infoestacion extends sql{
	//private $lc_regs;
	//constructor de la clase
    function __construct(){
        parent ::__construct();
    }
	//funcion que permite armar la sentencia sql de consulta
	
    function fn_consultar($lc_sqlQuery,$lc_datos){ 	
        switch($lc_sqlQuery){         
            case 'impresion_test':
                $lc_sql="EXECUTE [facturacion].[USP_ImpresionDinamica_TestImpresion] '$lc_datos[0]'";	
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };
            break;
        
            case 'validaControlEstacion':  	
                $lc_sql="EXECUTE seguridad.USP_validaipperfilperiodo $lc_datos[0],'$lc_datos[1]'";
                if($this->fn_ejecutarquery($lc_sql)) { 
                    while($row = $this->fn_leerarreglo()) {					
                        $this->lc_regs['controlEstacionActivo'] = $row["controlEstacionActivo"];
                    }	
                        $this->lc_regs['str'] = $this->fn_numregistro();                                                }
                        return json_encode($this->lc_regs);				
            break;        
        }
    }
    
    public function fn_infoimpresoras($lc_datos) {
        $lc_sql = "EXEC [facturacion].[USP_TestImpresion] ".$lc_datos[0].", '".$lc_datos[1]."'";
        if($this->fn_ejecutarquery($lc_sql)){
            while($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "canal_impresion"=>utf8_encode(trim($row['canal_impresion'])),
                    "impresora"=>utf8_encode(trim($row['impresora'])),
                    "tipo_impresora"=>utf8_encode(trim($row['tipo_impresora']))
                );
            }
        }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
    }
    
    public function fn_canalmovimiento_testimpresion($lc_datos) {
        $lc_sql = "exec [facturacion].[IAE_CanalMovimiento_TestImpresion] '$lc_datos[0]'";
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result){ return true; }else{ return false; };
    }
    
    public function fn_validaEstacion($lc_datos) {
        $lc_sql = "EXEC [facturacion].[USP_TestImpresion] ".$lc_datos[0].", '".$lc_datos[1]."'";
        if($this->fn_ejecutarquery($lc_sql)) { 
            while($row = $this->fn_leerarreglo()) {					
                $this->lc_regs['existeip'] = (int)$row["existeip"];
            }	
                $this->lc_regs['str'] = $this->fn_numregistro();
        }
            return json_encode($this->lc_regs);
    }
    
    public function fn_apagar_Estacion($lc_datos) {
        $lc_sql = "EXECUTE [facturacion].[IAE_ApagarCaja] $lc_datos[0], '$lc_datos[2]', '$lc_datos[1]'";
        $this->fn_ejecutarquery($lc_sql);
    }

    public function aplicarReplica() {
        $lc_sql = "exec [dbo].[USP_EjecutaLotesPendientesReplicaTiendas]";
        if($this->fn_ejecutarquery($lc_sql)){
            while($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "Modulo" => utf8_encode(trim($row['Modulo'])),
                    "NumeroDeLote" => $row['NumeroDeLote'],
                    "FechaCreacion" => $row['FechaCreacion'],
                    "HoraCreacion" => $row['HoraCreacion']
                );
            }
        }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
    }
    
    public function muestraErroresReeplica() {
        $lc_sql ="exec [dbo].[USP_replica_error]";
        if($this->fn_ejecutarquery($lc_sql)){
            while($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "Modulo" => utf8_encode(trim($row['Modulo'])),
                    "NumeroDeLote" => $row['NumeroDeLote'],
                    "FechaCreacion" => $row['FechaCreacion'],
                    "HoraCreacion" => $row['HoraCreacion']
                );
            }
        }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);        
    }    

    public function infoAplicaApiImpresion($lc_datos) {
        $lc_sql ="exec [impresion].[InfoAplicaImpresion] '$lc_datos[0]'";

        if($this->fn_ejecutarquery($lc_sql)){
            while($row = $this->fn_leerarreglo()) {
                $this->lc_regs['aplicaTienda'] = $row['aplicaTienda'];
                $this->lc_regs['servicioImpresion'] = isset($row['servicioImpresion'])? $row['servicioImpresion'] : 0;
                if(isset($row['servicioImpresion'])){
                    $servicioApiImpresion = json_decode($row['servicioImpresion']);
   
                    $this->lc_regs['asignacion_retiro_fondo'] = $servicioApiImpresion->asignacion_retiro_fondo;
                    
                }else{ 
                    $this->lc_regs['asignacion_retiro_fondo'] = 0;
                }
                $this->lc_regs['estacion'] = isset($row['estacion'])? $row['estacion'] : '';
                $this->lc_regs['idcadena'] = isset($row['idcadena'])? $row['idcadena'] : '';
                $this->lc_regs['IDEstacion'] = isset($row['IDEstacion'])? $row['IDEstacion'] : '';
            }
        }
            return json_encode($this->lc_regs);        
    }  

    /**
     * PROCESO PICKING AGREGADORES
     */
    public function infoCodigoConfirmacionDelivery($lc_datos){
        $lc_sql ="EXEC [impresion].[USP_impresion_codigo_confirmacion_delivery] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', 0 ";
        return $this->fn_ejecutarquery($lc_sql);
    }
}
?>		