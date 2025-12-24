<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: CONFIGURACION IMPRESORA, CREAR MODIFICAR CONFIGURACION DE IMPRESORA ////////////
////////////////TABLAS: Impresora, Tipo_impresora, Canal_Impresora_Estacion, Restaurante ///////////////
////////FECHA CREACION: 22/06/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

class configuracionImpresora extends sql{
	private $lc_regs;
	//constructor de la clase
	function __construct(){
		parent ::__construct();
	}
	//funcion que permite armar la sentencia sql de consulta
	
	function fn_consultar($lc_sqlQuery,$lc_datos){ 	
		switch($lc_sqlQuery){
			
			case "Cargar_Restaurante": 
							$lc_query="EXECUTE config.USP_restaurantesporcadena $lc_datos[0],$lc_datos[1],$lc_datos[2]";
						 
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{	

								$this->lc_regs[] = array("rst_id"=>$row['rst_id'],
														"Descripcion"=>htmlentities(trim($row['Descripcion'])));
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			case "administracionImpresora":
				$lc_sql="EXECUTE config.USP_administracionimpresora '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]','$lc_datos[8]','$lc_datos[9]','$lc_datos[10]'";
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("imp_id"=>$row['imp_id'],
												 "rst_id"=>$row['rst_id'], 
												 "timp_id"=>$row['timp_id'],
												 "timp_descripcion"=>utf8_encode(trim($row['timp_descripcion'])),
												 "mdl_id"=>$row['mdl_id'],
												 "imp_nombre"=>utf8_encode(trim($row['imp_nombre'])),
												 "imp_descripcion"=>utf8_encode(trim($row['imp_descripcion'])),
												 "std_id"=>$row['std_id'],
												 "timp_descripcion_todos"=>utf8_encode(trim($row['timp_descripcion_todos'])),
												 "timp_id_todos"=>$row['timp_id_todos'],
												 "est_id"=>$row['est_id'],
												 "est_nombre"=>$row['est_nombre']);
												 
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			case "nombrerestaurante":
				$lc_sql="config.USP_restaurantesporcadena $lc_datos[0],$lc_datos[1],$lc_datos[2]";   
				if($this->fn_ejecutarquery($lc_sql)){
					if($row = $this->fn_leerarreglo()){
						$this->lc_regs[] = array("descripcion"=>$row['descripcion']);							
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			
			
		}
	}
}


