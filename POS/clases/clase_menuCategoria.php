<?php
/////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jorge Tinoco ///////////////////////////////////////
///////DESCRIPCION: Carga de Menus y Menus por Agrupacion ///////////////////
///////TABLAS INVOLUCRADAS: Menu_Agrupacion, Menu ///////////////////////////
///////FECHA CREACION: 21-08-2015////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

class categoria extends sql {
	//private $lc_regs;
	//constructor de la clase
	function __construct(){
		parent ::__construct();
	}
	//funcion que permite armar la sentencia sql de consulta
	
	function fn_consultar($lc_sqlQuery,$lc_datos){
			switch($lc_sqlQuery){
				case "cargarMenus":
					$lc_sql = "EXEC config.USP_Menu_Secciones ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3];
					if($this->fn_ejecutarquery($lc_sql)){
						while($row = $this->fn_leerarreglo()){
							$this->lc_regs[] = array("menu_id"=>$row['menu_id'],
													"menu_Nombre"=>trim($row['menu_Nombre']));
						}
					}
					$this->lc_regs['str'] = $this->fn_numregistro();
					return json_encode($this->lc_regs);
				break;				
				case "cargarCategoria":
					$lc_sql = "EXEC config.USP_Menu_Secciones ".$lc_datos[0].", ".$lc_datos[1].", '".$lc_datos[2]."', '".$lc_datos[3]."'";
					if($this->fn_ejecutarquery($lc_sql)) {
						while($row = $this->fn_leerarreglo()) {
							$this->lc_regs[] = array("mag_id"=>$row['mag_id'],
													"mag_descripcion"=>$row['mag_descripcion'],
													"mag_colortexto"=>$row['mag_colortexto'],
													"mag_color"=>$row['mag_color'],
													"std_id"=>$row['std_id']);
						}
					}
					$this->lc_regs['str'] = $this->fn_numregistro();
					return json_encode($this->lc_regs);
				break;
				case "administrarCategoria":
					$lc_sql= "EXEC config.IAE_Menu_Secciones  ".$lc_datos[0].", '".$lc_datos[1]."', '".utf8_decode($lc_datos[2])."', '".$lc_datos[3]."', '".$lc_datos[4]."', ".$lc_datos[5].", ".$lc_datos[6].", '".$lc_datos[7]."', '".$lc_datos[8]."'";
					if($this->fn_ejecutarquery($lc_sql)){
						while($row = $this->fn_leerarreglo()){
							$this->lc_regs[] = array("mag_id"=>$row['mag_id'],
													"mag_descripcion"=>utf8_encode($row['mag_descripcion']),
													"mag_colortexto"=>$row['mag_colortexto'],
													"mag_color"=>$row['mag_color'],
													"std_id"=>$row['std_id']);
						}
					}
					$this->lc_regs['str'] = $this->fn_numregistro();
					return json_encode($this->lc_regs);
				break;
			}
	}
}
			