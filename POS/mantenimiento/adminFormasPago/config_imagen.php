<?php

////////////////////////////////////////////////////////////////////////////////////////////
////////MODIFICADO POR: CHRISTIAN PINTO/////////////////////////////////////////////////////
////////DECRIPCION: CONSULTA Y ACTUALIZA IMAGEN DE FORMAS DE PAGO //////////////////////////
////////TABLAS INVOLUCRADAS: Formapago//////////////////////////////////////////////////////
////////FECHA CREACION: 11/02/2016//////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////
ini_set("memory_limit","256M");

include_once("../../system/conexion/clase_sql.php"); 
 
$lc_config   = new sql();	
$fmp_id = $_POST['codigofp'];
$accion = $_POST['accion'];

	if($accion = 1){
		$imagen = '0';
		$lc_sql = "EXECUTE config.USP_FormasPagoImagen $accion, '$fmp_id'";
			if($result = $lc_config->fn_ejecutarquery($lc_sql)){
				while($row = $lc_config->fn_leerarreglo()) {
					$imagen = $row['fmp_imagen'];
				}
			}
		$lc_regs['imagen'] = $imagen;
	
		print json_encode($lc_regs);

		$lc_regs['imagen'] = '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';
	}
	
?>