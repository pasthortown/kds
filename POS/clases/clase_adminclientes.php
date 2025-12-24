<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: CREACION DE CLIENTES  //////////////////////////////////////////////////////////
////////////////TABLAS: Cliente ////////////////////////////////////////////////////////////////////////
////////FECHA CREACION: 23/08/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////


class clientes extends sql{
	//private $lc_regs;
	//constructor de la clase
	function __construct(){
		parent ::__construct();
	}
	//funcion que permite armar la sentencia sql de consulta
	
	function fn_consultar($lc_sqlQuery,$lc_datos){ 	
		switch($lc_sqlQuery){
			
			case "cargarClientes":
				$lc_sql="EXEC config.USP_clientesformaspago $lc_datos[0], $lc_datos[1], $lc_datos[2], '$lc_datos[3]'";   
				if( $this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("cli_id"=>$row['cli_id'],
												 "cli_apellidos"=>utf8_encode(trim($row['cli_apellidos'])),
												 "cli_nombres"=>utf8_encode(trim($row['cli_nombres'])),
												 "cli_documento"=>$row['cli_documento'],
												 "cli_telefono"=>$row['cli_telefono'],
												 "cli_direccion"=>utf8_encode(trim($row['cli_direccion'])),
												 "ciu_nombre"=>utf8_encode(trim($row['ciu_nombre'])),
												 "cli_email"=>$row['cli_email']);	
						
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			case "traerTipoDocumento":
				$lc_sql="EXEC config.USP_clientesformaspago $lc_datos[0], $lc_datos[1], $lc_datos[2], '$lc_datos[3]'";   
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("tpdoc_id"=>$row['tpdoc_id'],
												 "tpdoc_descripcion"=>utf8_encode(trim($row['tpdoc_descripcion'])));	
						
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			case "traerCiudad":
				$lc_sql="EXEC config.USP_clientesformaspago $lc_datos[0], $lc_datos[1], $lc_datos[2], '$lc_datos[3]'";   
				if( $this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("ciu_id"=>$row['ciu_id'],
												 "ciu_nombre"=>utf8_encode(trim($row['ciu_nombre'])));	
						
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			case "guardarClienteFormasPago":
				$lc_sql="EXEC config.IAE_clientesformaspago $lc_datos[0], '$lc_datos[1]', $lc_datos[2], $lc_datos[3], '$lc_datos[4]', $lc_datos[5], '$lc_datos[6]', '$lc_datos[7]', '$lc_datos[8]', '$lc_datos[9]', '$lc_datos[10]', '$lc_datos[11]', $lc_datos[12], $lc_datos[13]";   
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("accion"=>$row['accion']);	
						
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			case "traerCliente":
				$lc_sql="EXEC config.USP_clientesformaspago $lc_datos[0], $lc_datos[1], $lc_datos[2], '$lc_datos[3]'";   
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("cli_id"=>$row['cli_id'],
												 "cli_apellidos"=>utf8_encode(trim($row['cli_apellidos'])),
												 "cli_nombres"=>utf8_encode(trim($row['cli_nombres'])),
												 "cli_documento"=>$row['cli_documento'],
												 "cli_telefono"=>$row['cli_telefono'],
												 "cli_direccion"=>utf8_encode(trim($row['cli_direccion'])),
												 "ciu_nombre"=>utf8_encode(trim($row['ciu_nombre'])),
												 "cli_email"=>$row['cli_email'],
												 "ciu_id"=>$row['ciu_id'],
												 "tpdoc_id"=>$row['tpdoc_id']);	
						
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			case "guardarClienteModifica":
				$lc_sql="EXEC config.IAE_clientesformaspago $lc_datos[0], '$lc_datos[1]', $lc_datos[2], $lc_datos[3], '$lc_datos[4]', $lc_datos[5], '$lc_datos[6]', '$lc_datos[7]', '$lc_datos[8]', '$lc_datos[9]', '$lc_datos[10]', '$lc_datos[11]', $lc_datos[12], '$lc_datos[13]'";   
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("accion"=>$row['accion']);	
						
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
		
				
		}
	}
}

