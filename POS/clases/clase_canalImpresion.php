<?php

///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR JOSE FERNANDEZ////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE CONFIGURACION DE POMOCION//////////////
////////////////TABLAS: PROMOCION///////////////////////////////////////////
////////FECHA CREACION: 26/03/2014/////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

class canales extends sql

{		
	public function fn_consultar($lc_opcion, $lc_datos)
	{
		switch($lc_opcion)
		{

			case 'cargaRestaurante':
				$lc_query="SELECT rst_id,rst_cod_tienda + Space(2)+ rst_descripcion as Descripcion  
						 	FROM Restaurante
							WHERE cdn_id = $lc_datos[0] AND std_id = 1
						 	order by rst_cod_tienda";								
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
							while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs[] = array("rst_id"=>$row['rst_id'],
										"Descripcion"=> utf8_encode($row['Descripcion']));
										
								}	
							$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			case 'cargarDetalleImpresoras':
			$lc_query="exec man_cargaImpresoras $lc_datos[0]";								
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
							while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs[] = array("imp_id"=>$row['imp_id'],
										"mdl_descripcion"=> utf8_encode($row['mdl_descripcion']),
										"imp_nombre"=> utf8_encode($row['imp_nombre']));
										
								}	
							$this->lc_regs['str'] = $this->fn_numregistro();
							}
			return json_encode($this->lc_regs);	
			break;
			
			/*case 'cargarImpresora':
			$lc_query="exec man_cargaImpresoraModificada $lc_datos[0]";								
							if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
							while($row = $this->fn_leerarreglo()) 
								{											
										$this->lc_regs['imp_id'] = $row["imp_id"];
										$this->lc_regs['mdl_descripcion'] = utf8_encode($row['mdl_descripcion']);
										$this->lc_regs['imp_nombre'] = utf8_encode($row['imp_nombre']);										
										$this->lc_regs['mdl_id'] = $row["mdl_id"];											
								}	
							$this->lc_regs['str'] = $this->fn_numregistro();
							}
			return json_encode($this->lc_regs);				
			break;*/
			
			case 'cargarImpresoraModificada':
			$lc_query="exec man_cargaImpresoraModificada $lc_datos[0]";								
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
							while($row = $this->fn_leerarreglo()) 
								{											
										$this->lc_regs['imp_id'] = $row["imp_id"];
										$this->lc_regs['mdl_descripcion'] = utf8_encode($row['mdl_descripcion']);
										$this->lc_regs['imp_nombre'] = utf8_encode($row['imp_nombre']);										
										$this->lc_regs['mdl_id'] = $row["mdl_id"];											
								}	
							$this->lc_regs['str'] = $this->fn_numregistro();
							}
			return json_encode($this->lc_regs);				
			break;
			
			case 'cargarmodulo':
			$lc_query="exec man_cargaModulo";								
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
							while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs[] = array("mdl_id"=>$row['mdl_id'],
										"mdl_descripcion"=> utf8_encode($row['mdl_descripcion']));										
								}	
							$this->lc_regs['str'] = $this->fn_numregistro();
							}
			return json_encode($this->lc_regs);	
			break;
			
			case 'actualizaImpresora':
			$lc_query="exec man_actualizaImpresora $lc_datos[0], $lc_datos[2], $lc_datos[3]";	
			
				$result = $this->fn_ejecutarquery($lc_query);
				if ($result){ return true; }else{ return false; };

			break;
			
			case 'grabaImpresora':
			$lc_query="exec man_grabaImpresora $lc_datos[1], $lc_datos[2], '$lc_datos[3]'";	
			
				$result = $this->fn_ejecutarquery($lc_query);
				if ($result){ return true; }else{ return false; };	

			break;
			
			case 'eliminaImpresora':
			$lc_query="exec man_eliminaImpresora $lc_datos[1]";	
			
				$result = $this->fn_ejecutarquery($lc_query);
				if ($result){ return true; }else{ return false; };
				
			break;			
			
			
		}
	}
	
}
?>
	
