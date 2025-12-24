<?php
///////////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jorge Tinoco /////////////////////////////////////////////
///////DESCRIPCION: Impresion orden pedido ////////////////////////////////////////
///////TABLAS INVOLUCRADAS: Menu_Agrupacion, //////////////////////////////////////
/////// Menu_Agrupacionproducto ///////////////////////////////////////////////////
/////// Detalle_Orden_Pedido //////////////////////////////////////////////////////
/////// Plus, Precio_Plu, Mesas ///////////////////////////////////////////////////
///////FECHA CREACION: 11-05-2015 /////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

class impresion_precuenta extends sql{
	//Constructor de la Clase
	function _construct(){
		parent ::_construct();
	}
	
	//Función que permite armar la sentencia sql de consulta
	function fn_consultar($lc_sqlQuery, $lc_datos){
		switch($lc_sqlQuery){
			case 'Cabecera_Precuenta':
				$lc_query="EXEC pedido.ORD_impresion_cabecera_precuenta ".$lc_datos[0].", ".$lc_datos[1];
                return $this->fn_ejecutarquery($lc_query);
			break;
			case 'Detalle_Pre_Cuenta':
					$lc_query="EXEC pedido.ORD_impresion_detalle_precuenta ".$lc_datos[0];
					return $this->fn_ejecutarquery($lc_query);
			break;
			case 'Cargar_Precuenta':
					$lc_query="EXEC pedido.ORD_impresion_dinamica_precuenta $lc_datos[0],'$lc_datos[1]',$lc_datos[2]";
					return $this->fn_ejecutarquery($lc_query);
			break;
		}
	}
}

?>