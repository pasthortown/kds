<?php

//////////////////////////////////////////////////////////////////////////////////
////////MODIFICADO POR: CHRISTIAN PINTO///////////////////////////////////////////
///////////DESCRIPCION: NUEVOS ESTILOS INGRESO Y ACTUALIZACION DE ESTACION CON////
/////////////////////// TABLA MODAL //////////////////////////////////////////////
////////////////TABLAS: Estacion,SWT_Tipo_Envio///////////////////////////////////
////////FECHA CREACION: 01/06/2015////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////

class estacion extends sql

{		
	public function fn_consultar($lc_opcion, $lc_datos)
	{
		switch($lc_opcion)
		{
					
			case "Cargar_Restaurante": 
							$lc_query="EXECUTE config.USP_restaurantesporcadena $lc_datos[0],$lc_datos[1],$lc_datos[2]";
						 
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								$this->lc_regs[] = array("rst_id"=>$row['rst_id'],
										"Descripcion"=>htmlentities(trim($row['Descripcion'])));
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			case "cargarmenu": 
							$lc_query="EXECUTE config.USP_administracionestacion $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3]";
						 
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								$this->lc_regs[] = array("menu_id"=>$row['menu_id'],
										"menu_nombre"=>htmlentities(trim($row['menu_nombre'])));
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			case "cargarTipoCobro": 
							$lc_query="EXECUTE config.USP_administracionestacion $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3]";
						 
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								$this->lc_regs[] = array("tpenv_id"=>$row['tpenv_id'],
										"tpenv_descripcion"=>htmlentities(trim($row['tpenv_descripcion'])));
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
						
			case "cargarDetalle": 
							$lc_query="EXECUTE config.USP_administracionestacion $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3]";
						 
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								$this->lc_regs[] = array("est_id"=>$row['est_id'],
										"est_ip"=>$row['est_ip'],
										"est_nombre"=>$row['est_nombre'],
										"Estado"=>$row['Estado'],
										"menu_Nombre"=>utf8_encode($row['menu_Nombre']),
										"menu_id"=>$row['menu_id']);
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);
				
				case "cargarDetalleInactivos": 
							$lc_query="EXECUTE config.USP_administracionestacion $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3]";
						 
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								$this->lc_regs[] = array("est_id"=>$row['est_id'],
										"est_ip"=>$row['est_ip'],
										"est_nombre"=>$row['est_nombre'],
										"Estado"=>$row['Estado'],
										"menu_Nombre"=>utf8_encode($row['menu_Nombre']),
										"menu_id"=>$row['menu_id']);
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);
				
				break;
			
						case "cargarNumeroNombreCaja": 
							$lc_query="EXECUTE config.USP_administracionestacion $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3]";
						 
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								
										$this->lc_regs['MaximaCaja'] = $row["MaximaCaja"];								
									
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);		
			break;
			
			
			case "cargarestacionModifica": 
							$lc_query="EXECUTE usp_cnf_datosinsertamodificaestacion $lc_datos[2],$lc_datos[3],$lc_datos[0] ";
						 
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								
										$this->lc_regs['Restaurante'] = $row["Restaurante"];
										$this->lc_regs['primer_octeto_ip'] = $row["primer_octeto_ip"];
										$this->lc_regs['segundo_octeto_ip'] = $row["segundo_octeto_ip"];										
										$this->lc_regs['tercer_octeto_ip'] = $row["tercer_octeto_ip"];
										$this->lc_regs['cuarto_octeto_ip'] = $row["cuarto_octeto_ip"];
										$this->lc_regs['numero_estacion'] = $row["numero_estacion"];
										$this->lc_regs['menu_id'] = $row["menu_id"];										
										$this->lc_regs['tid'] = ltrim($row["tid"]);
										$this->lc_regs['tpenv_id'] = $row["tpenv_id"];	
										$this->lc_regs['std_id'] = $row["std_id"];	
									
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			
			case "cargarselmenu": 
							$lc_query="EXECUTE config.USP_administracionestacion $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3]";
						 
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								$this->lc_regs[] = array("menu_id"=>$row['menu_id'],
										"menu_Nombre"=>htmlentities(trim($row['menu_Nombre'])));
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			
			
				case "cargarNuevo": 
							$lc_query="EXECUTE usp_cnf_iprestaurante $lc_datos[0],$lc_datos[1]";
						 
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{													
										$this->lc_regs['Restaurante'] = $row["Restaurante"];
										$this->lc_regs['primer_octeto_ip'] = $row["primer_octeto_ip"];
										$this->lc_regs['segundo_octeto_ip'] = $row["segundo_octeto_ip"];
										$this->lc_regs['tercer_octeto_ip'] = $row["tercer_octeto_ip"];
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;

		

		}
	}
		public function fn_ejecutar($lc_opcion,$lc_datos)
	{
		switch($lc_opcion)
		{
			case "grabamodificaestacion": 
					$lc_query="EXECUTE IAE_usp_cnf_insertamodificaestacion $lc_datos[12],$lc_datos[10],$lc_datos[11],$lc_datos[5],'$lc_datos[0].$lc_datos[1].$lc_datos[2].$lc_datos[3]','$lc_datos[4]',$lc_datos[6],'$lc_datos[8]',$lc_datos[7],$lc_datos[9]";
					
			
					if($this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{													
										$this->lc_regs['Existe'] = $row["Existe"];
										$this->lc_regs['idestacioninsert'] = $row["idestacioninsert"];	
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			case "administracionCanalesImpresion": 
					$lc_query="EXECUTE config.USP_administracioncanalimpresionestacion $lc_datos[0],$lc_datos[1],$lc_datos[2],$lc_datos[3],$lc_datos[4],$lc_datos[5],$lc_datos[6],'$lc_datos[7]','$lc_datos[8]'";
					
			
					if($this->fn_ejecutarquery($lc_query)) 
							{ 						
								while($row = $this->fn_leerarreglo()) 
								{					
								$this->lc_regs[] = array("cimp_id"=>$row['cimp_id'],
														 "cimp_descripcion"=>utf8_encode($row['cimp_descripcion']),
														 "imp_id"=>$row['imp_id'],
														 "imp_nombre"=>utf8_encode($row['imp_nombre']),
														 "est_id"=>$row['est_id'],
														 "pto_id"=>$row['pto_id'],
														 "pto_descripcion"=>$row['pto_descripcion']);
								
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;	
			
		}
	}
}

	
