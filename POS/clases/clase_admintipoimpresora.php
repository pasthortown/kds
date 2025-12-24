<?php
/////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO////////////////////////////////////////////////
///////////DESCRIPCION: TIPO DE IMPRESORA, CREAR MODIFICAR TIPO DE IMPRESORA/////////////
////////////////TABLAS: tipo_impresora///////////////////////////////////////////////////
////////FECHA CREACION: 19/06/2015///////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////


class tipoImpresora extends sql{
	//private $lc_regs;
	//constructor de la clase
	function __construct(){
		parent ::__construct();
	}
	//funcion que permite armar la sentencia sql de consulta
	
	function fn_consultar($lc_sqlQuery,$lc_datos){ 	
		switch($lc_sqlQuery){
			
			case "administracionTipoImpresora":
				$lc_sql="EXECUTE config.USP_administraciontipoimpresora $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]','$lc_datos[8]'";
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("timp_id"=>$row['timp_id'],
												 "timp_descripcion"=>utf8_encode(trim($row['timp_descripcion'])),
												 "timp_codigo_apertura_caja"=>$row['timp_codigo_apertura_caja'],
												 "timp_corte_papel"=>$row['timp_corte_papel'],
												 "timp_impresion_normal"=>$row['timp_impresion_normal'],
												 "std_id"=>$row['std_id']);
						
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			
		}
	}
}
