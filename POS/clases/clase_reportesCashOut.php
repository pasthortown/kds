<?php
	//////////////////////////////////////////////////////////////////////////////////////
	////////DESARROLLADO POR: Jose Fernandez//////////////////////////////////////////////
	////////DESCRIPCION: Pantalla de reportes/////////////////////////////////////////////
	///////TABLAS INVOLUCRADAS: Cabecera_factura, formas_pago, forma_pago_factura/////////
	//////////////////////////control_estacion,usuarios///////////////////////////////////
	///////FECHA CREACION: 21/08/2014/////////////////////////////////////////////////////	
	//////////////////////////////////////////////////////////////////////////////////////	
	
	
class reportes extends sql{
	//Constructor de la Clase
	function _construct(){
		parent ::_construct();
	}
	
	//Función que permite armar la sentencia sql de consulta
	function fn_consultar($lc_sqlQuery,$lc_datos){
		switch($lc_sqlQuery){
			case "reporte_cashOut":
			$lc_sql = " SET DATEFORMAT dmy
						EXEC spu_rep_cashout ".$lc_datos[2].", '".$lc_datos[0]."', '".$lc_datos[1]."'";
				$result = $this->fn_ejecutarquery($lc_sql);
				if ($result){ return true; }else{ return false; };
			break;			
		}
	}
}
?>