<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de menú /////////////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 20-02-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////


class motivoanulacion extends sql{
	//private $lc_regs;
	//constructor de la clase
	function __construct(){
		parent ::__construct();
	}
	//funcion que permite armar la sentencia sql de consulta
	
	function fn_consultar($lc_sqlQuery,$lc_datos){ 	
		switch($lc_sqlQuery){
			case "cargaIdPantalla":
				$lc_sql="select pnt_id from pantalla_pos where pnt_Nombre_Mostrar = '".$lc_datos[0]."' and pnt_Nivel = 1";   
				if($this->fn_ejecutarquery($lc_sql)){
					if($row = $this->fn_leerarreglo()){
						$this->lc_regs[] = array("pnt_id"=>$row['pnt_id']);							
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			case "cargarMenuPantalla":
				$lc_sql="   select ap.acc_id, ap.acc_descripcion from Pantalla_Pos as pp
							inner join Permisos_Perfil_Pos as ppp on ppp.pnt_id = pp.pnt_id
							inner join Acceso_Pos as ap on ap.acc_id = ppp.acc_id
							inner join Users_Pos as up on up.prf_id = ppp.prf_id
							where pp.pnt_id = ".$lc_datos[0]." and usr_id = ".$lc_datos[1];
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()) {
						$this->lc_regs[] = array("acc_id"=>$row['acc_id'],
												 "acc_descripcion"=>utf8_encode(trim($row['acc_descripcion'])));
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			case "administrarMotivosAnulacion":
				$lc_sql = "EXEC admin_Motivo_Anulacion ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", '".$lc_datos[3]."', ".$lc_datos[4];
				if($this->fn_ejecutarquery($lc_sql)){
					while($row = $this->fn_leerarreglo()){
						$this->lc_regs[]= array("mtv_orden" => $row['mtv_orden'],
												"mtv_id" => $row['mtv_id'],
												"std_id" => $row['std_id'],
												"mtv_descripcion" => $row['mtv_descripcion']);
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;

		}
	}
}
			