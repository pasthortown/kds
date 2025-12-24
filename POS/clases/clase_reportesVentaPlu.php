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
	function fn_consultar($lc_sqlQuery, $lc_datos){
		switch($lc_sqlQuery){
			case "reporte_ventasPlu":
			$lc_sql="set dateformat dmy 
					 EXEC spu_rep_ventasporplus ".$lc_datos[0].", '".$lc_datos[1]."', '".$lc_datos[2]."'";
				$result = $this->fn_ejecutarquery($lc_sql);
				if ($result){ return true; }else{ return false; };
			break;
			case "reporte_totalesventasPlu":
			$lc_sql="set dateformat dmy
					select round(SUM(do.dop_cantidad),2)as TotalCantidad,round(SUM(do.dop_cantidad*do.dop_total),2)as TotalValor
					from Cabecera_Factura cf
					inner join Cabecera_Orden_Pedido co on co.odp_id = cf.odp_id
					inner join Detalle_Factura df on df.cfac_id = cf.cfac_id
					inner join Detalle_Orden_Pedido do on do.odp_id = co.odp_id
					inner join Plus p on p.plu_id = do.plu_id
					inner join Restaurante res on res.rst_id = cf.rst_id
					where convert(varchar(15),cf.cfac_fechacreacion,103) between '".$lc_datos[1]."' and '".$lc_datos[2]."' and cf.rst_id = ".$lc_datos[0];
				$result = $this->fn_ejecutarquery($lc_sql);
				if ($result){ return true; }else{ return false; };
			break;
		}
	}
}
?>