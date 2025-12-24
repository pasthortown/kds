<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: ADMINISTRACION DE RED ADQUIRIENTE, LISTADO, AGREGAR Y MODIFICAR     ////////////
////////////////TABLAS: Red_Adquiriente ////////////////////////////////////////////////////////////////
////////FECHA CREACION: 13/07/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

class administracionredadquiriente extends sql{
	//private $lc_regs;
	//constructor de la clase
	function __construct(){
		parent ::__construct();
	}
	//funcion que permite armar la sentencia sql de consulta
	
	function fn_consultar($lc_sqlQuery,$lc_datos){ 	
		switch($lc_sqlQuery){		
						
			case "administracionRedadquiriente":
				$lc_sql="EXECUTE config.USP_administracionredadquiriente $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]',$lc_datos[4],'$lc_datos[5]','$lc_datos[6]'";
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("rda_id"=>$row['rda_id'],
												 "rda_red_adquiriente"=>$row['rda_red_adquiriente'],
												 "rda_descripcion"=>utf8_encode(trim($row['rda_descripcion'])),
												 "std_id"=>$row['std_id']);
												 
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
						
		}
	}
}

?>