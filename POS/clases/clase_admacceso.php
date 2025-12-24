<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de accesos //////////////////////
///////FECHA CREACION: 27-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////


class acceso extends sql{
	
	//constructor de la clase
	function __construct(){
		parent ::__construct();
	}
	//funcion que permite armar la sentencia sql de consulta
	
	function fn_consultar($lc_sqlQuery,$lc_datos){
		switch($lc_sqlQuery){
			case "cargarAccesos":
				$lc_sql = "EXEC config.USP_SEG_Accesos ".$lc_datos[0];
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("acc_id"=>$row['acc_id'],
											   	"acc_descripcion"=>utf8_encode($row['acc_descripcion']),
												"acc_Nombre"=>utf8_encode($row['acc_Nombre']),
											   	"acc_Nivel"=>$row['acc_Nivel']);
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			case "administrarAcceso":
				$lc_sql = "EXEC config.IAE_SEG_Accesos ".$lc_datos[0].", '".$lc_datos[1]."', '".utf8_decode($lc_datos[2])."', '".utf8_decode($lc_datos[3])."', ".$lc_datos[4].", '".$lc_datos[5]."', ".$lc_datos[6];
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("acc_id"=>$row['acc_id'],
											   	"acc_descripcion"=>utf8_encode($row['acc_descripcion']),
												"acc_Nombre"=>utf8_encode($row['acc_Nombre']),
											   	"acc_Nivel"=>$row['acc_Nivel']);
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
		}
	}
}
			