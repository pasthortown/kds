<?php

class PayPhone extends sql{

	function __construct(){
		parent ::__construct();
	}

	function configuracionPayPhone($datos){
		$query = "EXEC [facturacion].[PAYPHONE_configuracion] ".$datos;
		if($this->fn_ejecutarquery($query)){
			return $this->fn_leerObjeto();
		}
	}

	function configuracionTransaccion($datos){
		$query = "EXEC [facturacion].[PAYPHONE_datosfactura] '".$datos."'";
		if($this->fn_ejecutarquery($query)){
			return $this->fn_leerObjeto();
		}
	}

	function agregarFormaPagoFactura($datos){
		$query = "EXEC [facturacion].[PAYPHONE_IAE_formapago] ".$datos[0].", '".$datos[1]."', ".$datos[2].", ".$datos[3].", '".$datos[4]."', '".$datos[5]."', '".$datos[6]."', '".$datos[7]."', '".$datos[8]."'";
		if($this->fn_ejecutarquery($query)){
			return $this->fn_leerObjeto();
		}
	}

	function cargarDatosFactura($datos){
		$query = "EXEC [facturacion].[PAYPHONE_datosfactura] '".$datos[0]."'";
		if($this->fn_ejecutarquery($query)){
			while($row = $this->fn_leerarreglo()) {
				$this->lc_regs[] = array("Total"=>$row['Total'],
										"SubTotal"=>$row['SubTotal']);
			}
		}
		$this->lc_regs['str'] = $this->fn_numregistro();
		return json_encode($this->lc_regs);
	}

	function fn_cargarInformacionTransaccion($datos){
		$query = "[facturacion].[PAYPHONE_TRN_cargar_datos_pago_aprobado] ".$datos[0].", ".$datos[1].", '".$datos[2]."'";
		if($this->fn_ejecutarquery($query)){
			return $this->fn_leerObjeto();
		}
	}

}

