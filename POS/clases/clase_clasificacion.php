<?php
///////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Francisco Sierra////////////////////////////////////////
///////////DESCRIPCION: Clase Clasificacion ///////////////
////////////////TABLAS: Clasificacion ///////////////////////////////////////////////
////////FECHA CREACION: 05/05/2016///////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////


class clasificacion extends sql{
	//private $lc_regs;
	//constructor de la clase
	function __construct(){
		parent ::__construct();
	}
	//funcion que permite armar la sentencia sql de consulta
	
        function fn_cargar_Clasificaciones($lc_datos){
            $this->lc_regs['str']=0;
            $this->lc_regs["datos"]=array();
            $lc_sql = "exec Facturacion.USP_Cargar_Clasificaciones_Menu ". $lc_datos['estado'];
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()) {
                    $this->lc_regs["datos"][]=[
                        "cla_id"=>$row['IDClasificacion'],
                        "cla_nombre"=>utf8_decode($row['cla_Nombre']),
                        "cla_nivel"=>$row['nivel']
                    ]; 
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return $this->lc_regs;
        }
	
}