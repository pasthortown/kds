<?php
////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////
///////////DESCRIPCION: CANALES DE IMPRESION, CREAR MODIFICAR CANAL DE IMPRESION////
/////////////////////// POR CADENA /////////////////////////////////////////////////
////////////////TABLAS: canal_impresion, cadena/////////////////////////////////////
////////FECHA CREACION: 18/06/2015//////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////


class canalesImpresion extends sql{
	//private $lc_regs;
	//constructor de la clase
	function __construct(){
		parent ::__construct();
	}
	//funcion que permite armar la sentencia sql de consulta
	
	function fn_consultar($lc_sqlQuery,$lc_datos){ 	
		switch($lc_sqlQuery){
			
			case "cargarCanalesImpresion":
				$lc_sql="EXECUTE config.USP_administracioncanalesimpresion $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("cimp_id"=>$row['cimp_id'],
												 "cimp_descripcion"=>utf8_encode(trim($row['cimp_descripcion'])),
												 "cdn_id"=>$row['cdn_id'],
												 "std_id"=>$row['std_id'],
												 "cdn_descripcion"=>utf8_encode(trim($row['cdn_descripcion'])));
												
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			
		}
	}
}
