<?php
///////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO//////////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE COLECCIONES DE DATOS, CREAR MODIFICAR /////////
////////////////TABLAS: Colecciones Varias ////////////////////////////////////////
////////FECHA CREACION: 16/03/2016/////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////


class colecciones extends sql{
	//private $lc_regs;
	//constructor de la clase
	function __construct(){
		parent ::__construct();
	}
	//funcion que permite armar la sentencia sql de consulta
	
	function fn_consultar($lc_sqlQuery,$lc_datos){ 	
		switch($lc_sqlQuery){
			
			case "cargarTablasCabeceracolecciones":
				$lc_sql="EXECUTE config.USP_adminColeccionesDeDatos $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("num_tabla"=>$row['num_tabla'],
												 "nombre_tabla"=>  utf8_decode($row['nombre_tabla']),
												 "nombre_coleccion"=>  utf8_decode($row['nombre_coleccion']));
												
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			case "cargarTablaCabecera":
				$lc_sql="EXECUTE config.USP_adminColeccionesDeDatos $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("ID_Coleccion"=>$row['ID_Coleccion'],
												 "Descripcion"=>$row['Descripcion'],
												 "emp_id"=>$row['emp_id'],
												 "emp_nombre"=>$row['emp_nombre'],
												 "cdn_id"=>$row['cdn_id'],
												 "cdn_descripcion"=>$row['cdn_descripcion'],
												 "mdl_id"=>$row['mdl_id'],
												 "configuracion"=>$row['configuracion'],
												 "reporte"=>$row['reporte'],
												 "cubo"=>$row['cubo'],
												 "repetirConfiguracion"=>$row['repetirConfiguracion'],
												 "estatus1"=>$row['estatus1'],
												 "estatus2"=>$row['estatus2'],
												 "isActive"=>$row['isActive'],
												 "lastUpdate"=>$row['lastUpdate'],
												 "lastUser"=>$row['lastUser'],
												 "idIntegracion"=>$row['idIntegracion'],
												 "intDescripcion"=>$row['intDescripcion']);
												
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			case "cargarTablaColeccionDeDatos":
				$lc_sql="EXECUTE config.USP_adminColeccionesDeDatos $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("ID_Coleccion"=>$row['ID_Coleccion'],
												 "ID_ColeccionDeDatos"=>$row['ID_ColeccionDeDatos'],					  
												 "Descripcion"=>$row['Descripcion'],
												 "DescripcionColeccion"=>$row['DescripcionColeccion'],
												 "especificarValor"=>$row['especificarValor'],
												 "obligatorio"=>$row['obligatorio'],
												 "tipodedato"=>$row['tipodedato'],
												 "estatus1"=>$row['estatus1'],
												 "estatus2"=>$row['estatus2'],
												 "idIntegracion"=>$row['idIntegracion'],
												 "intDescripcion"=>$row['intDescripcion'],
												 "isActive"=>$row['isActive'],
												 "lastUpdate"=>$row['lastUpdate'],
												 "lastUser"=>$row['lastUser']);
												
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			case "cargarTablaColecciones":
				$lc_sql="EXECUTE config.USP_adminColeccionesDeDatos $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("DescripcionColeccion"=>$row['DescripcionColeccion'],
												 "DescripcionColeccionDeDatos"=>$row['DescripcionColeccionDeDatos'],
												 "ID_Coleccion"=>$row['ID_Coleccion'],
												 "ID_ColeccionDeDatos"=>$row['ID_ColeccionDeDatos'],
												 "variableV"=>$row['variableV'],
												 "variableI"=>$row['variableI'],
												 "variableD"=>$row['variableD'],
												 "variableB"=>$row['variableB'],
												 "variableN"=>$row['variableN'],
												 "fechaIni"=>$row['fechaIni'],
												 "fechaFin"=>$row['fechaFin'],
												 "min"=>$row['min'],
												 "max"=>$row['max'],
												 "idIntegracion"=>$row['idIntegracion'],
												 "intDescripcion"=>$row['intDescripcion'],
												 "isActive"=>$row['isActive']);
												
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			case "cargarNombreTablasColecciones":
				$lc_sql="EXECUTE config.USP_adminColeccionesDeDatos $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("num_tabla"=>$row['num_tabla'],
												 "nombre_tabla"=>$row['nombre_tabla']);
												
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			case "traeTipoDeDato":
				$lc_sql="EXECUTE config.USP_adminColeccionesDeDatos $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("ID_DATA_TYPE"=>$row['ID_DATA_TYPE'],
												 "DATA_TYPE"=>$row['DATA_TYPE']);
												
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			case "traeColeccionesCabecera":
				$lc_sql="EXECUTE config.USP_adminColeccionesDeDatos $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("ID_Coleccion"=>$row['ID_Coleccion'],
												 "Descripcion"=>$row['Descripcion']);
												
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			case "traeColeccionesModificar":
				$lc_sql="EXECUTE config.USP_adminColeccionesDeDatos $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("ID_Coleccion"=>$row['ID_Coleccion'],
												 "Descripcion"=>$row['Descripcion'],
												 "cdn_id"=>$row['cdn_id'],
												 "configuracion"=>$row['configuracion'],
												 "reporte"=>$row['reporte'],
												 "cubo"=>$row['cubo'],
												 "repetirConfiguracion"=>$row['repetirConfiguracion'],
												 "estatus1"=>$row['estatus1'],
												 "estatus2"=>$row['estatus2'],
												 "isActive"=>$row['isActive'],
												 "lastUpdate"=>$row['lastUpdate'],
												 "lastUser"=>$row['lastUser'],
												 "idIntegracion"=>$row['idIntegracion'],
												 "intDescripcion"=>$row['intDescripcion']);						
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			
			case "traeColeccionDeDatosModificar":
				$lc_sql="EXECUTE config.USP_adminColeccionesDeDatos $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("ID_Coleccion"=>$row['ID_Coleccion'],
												 "ID_ColeccionDeDatos"=>$row['ID_ColeccionDeDatos'],					  
												 "Descripcion"=>$row['Descripcion'],
												 "especificarValor"=>$row['especificarValor'],
												 "obligatorio"=>$row['obligatorio'],
												 "tipodedato"=>$row['tipodedato'],
												 "estatus1"=>$row['estatus1'],
												 "estatus2"=>$row['estatus2'],
												 "idIntegracion"=>$row['idIntegracion'],
												 "intDescripcion"=>$row['intDescripcion'],
												 "isActive"=>$row['isActive'],
												 "lastUpdate"=>$row['lastUpdate'],
												 "lastUser"=>$row['lastUser']);					
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			

		}
	}
	
	function fn_ejecutar($lc_sqlQuery,$lc_datos){ 	
		switch($lc_sqlQuery){
			
			case "guardarCabeceraColeccion":
				$lc_sql="EXECUTE config.IAE_adminColeccionesDeDatos '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', $lc_datos[5], $lc_datos[6], '$lc_datos[7]', '$lc_datos[8]', '$lc_datos[9]', '$lc_datos[10]', '$lc_datos[11]', $lc_datos[12], '$lc_datos[13]', $lc_datos[14], '$lc_datos[15]', '$lc_datos[16]', '$lc_datos[17]', '$lc_datos[18]', '$lc_datos[19]'";
			
				$result = $this->fn_ejecutarquery($lc_sql);
				if ($result) {
					return true;
				}else{
					return false;
				}
			break;
			
			case "guardarColeccionDeDatos":
				$lc_sql="EXECUTE config.IAE_adminColeccionesDeDatos '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', $lc_datos[5], $lc_datos[6], '$lc_datos[7]', '$lc_datos[8]', '$lc_datos[9]', '$lc_datos[10]', '$lc_datos[11]', $lc_datos[12], '$lc_datos[13]', $lc_datos[14], '$lc_datos[15]', '$lc_datos[16]', '$lc_datos[17]', '$lc_datos[18]', '$lc_datos[19]'";
			
				$result = $this->fn_ejecutarquery($lc_sql);
				if ($result) {
					return true;
				}else{
					return false;
				}
			break;
				
		}
	}
}

?>