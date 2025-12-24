<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Jose Fernandez//////////////////////
////////DESCRIPCION: Archivo para manejo de decuentos/////////
///////FECHA CREACION: 123-02-2015/////////////////////////////
////////////////////////////////////////////////////////////// 


class descuentos extends sql 
{
  //constructor de la clase
 function __construct()
   {
	//con herencia 
	parent::__construct();
   }

	
/////////////////////////////////////////FUNCION CONSULTAR////////////////////////////////////////////////////

function fn_consultar($lc_sqlQuery,$lc_datos) {

	switch($lc_sqlQuery){	
	

	
		case 'buscaDescuentos':  	
			$lc_sql="exec MuestraCupones $lc_datos[0]";
			
					if($lc_datos = $this->fn_ejecutarquery($lc_sql)) 
					{
						while($row = $this->fn_leerarreglo()) 
						{
							$this->lc_regs[] = array("dsct_id"=>$row['dsct_id'],
													"dsct_descripcion"=>$row['dsct_descripcion'],
													"dsct_maximo"=>$row['dsct_maximo'],
													"dsct_tipo"=>$row['dsct_tipo'],
													"dsct_valor"=>$row['dsct_valor'],
													"tpd_id"=>$row['tpd_id'],
													"dsct_precio_minimo"=>$row['dsct_precio_minimo'],
													"dsct_cantidad_minima"=>$row['dsct_cantidad_minima'],
													"apld_id"=>$row['apld_id'],
													"dsct_porfactura"=>$row['dsct_porfactura'],
													"dsct_fechainicio"=>$row['dsct_fechainicio'],
													"dsct_fechafin"=>$row['dsct_fechafin'],
													"std_id"=>$row['std_id'],
													"rst_id"=>$row['rst_id']);	
						}	
						$this->lc_regs['str']=$this->fn_numregistro();
						return json_encode($this->lc_regs);	
					}						
		break;
	
		case 'validaExisteFormaPago':  	
			$lc_sql="exec desc_valida_forma_pago '$lc_datos[0]'";
			
					if($this->fn_ejecutarquery($lc_sql)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs['Existe'] = $row["Existe"];										
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);				
		break;
		
		case 'validacuponYaAplicado':  	
			$lc_sql="exec desc_validacuponYaAplicado '$lc_datos[0]'";
			
					if($result = $this->fn_ejecutarquery($lc_sql)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs['aplicado'] = $row["aplicado"];										
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);				
		break;
		
		
		case 'validaDescuentoPorProducto':  	
			$lc_sql="exec desc_validaDescuentoPorcentajeProducto '$lc_datos[0]',$lc_datos[1],$lc_datos[2],$lc_datos[5]";
				
				if($result = $this->fn_ejecutarquery($lc_sql)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs['aplica'] = $row["aplica"];
									if($row["aplica"]==1)
									{
										$this->lc_regs['cantidad_minima'] = $row["cantidad_minima"];																			
									}
								}	
								$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);				
		break;
		
		case 'validaDescuentoPorCategoria':  	
			$lc_sql="exec desc_validaDescuentoPorCategoria '$lc_datos[0]',$lc_datos[1],$lc_datos[2],$lc_datos[5]";
				
				if($result = $this->fn_ejecutarquery($lc_sql)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs['aplica'] = $row["aplica"];		
									if($row["aplica"]==1)
									{
										$this->lc_regs['precio_factura'] = $row["precio_factura"];																			
									}									
								}	
								$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);				
		break;


		case 'insertaDescuento':  	
			$lc_sql="exec desc_insertaDescuento '$lc_datos[3]',$lc_datos[0],$lc_datos[1],$lc_datos[2]";
						if($result = $this->fn_ejecutarquery($lc_sql)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs['dsct_descripcion'] = $row["dsct_descripcion"];				
									$this->lc_regs['desf_valor'] = $row["desf_valor"];				
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);								
		break;
		
		case 'consultaDescuentos':  	
			$lc_sql="exec fac_consultaDescuentos $lc_datos[0]";
						if($result = $this->fn_ejecutarquery($lc_sql)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs['dsct_descripcion'] = $row["dsct_descripcion"];				
									$this->lc_regs['desf_valor'] = $row["desf_valor"];				
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);								
		break;
		
		
		case 'insertaDescuentoEntradaManual':  	
			$lc_sql="exec desc_insertaDescuentoEntradaManual '$lc_datos[3]',$lc_datos[0],$lc_datos[1],$lc_datos[2]";
						if($result = $this->fn_ejecutarquery($lc_sql)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs['dsct_descripcion'] = $row["dsct_descripcion"];				
									$this->lc_regs['desf_valor'] = $row["desf_valor"];				
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);										
		break;
		
		
		case 'muestraTotalesConDescuento':  	
			//$lc_sql="exec fac_listaFactura '$lc_datos[0]'";
			$lc_sql="exec desc_muestraTotalesLuegoDeDescuento '$lc_datos[0]'";
						if($result = $this->fn_ejecutarquery($lc_sql)) { 
					while($row = $this->fn_leerarreglo()) 
					{
						$this->lc_regs[] = array("cdn_tipoimpuesto"=>trim($row['cdn_tipoimpuesto']),												
												 "cfac_base_cero"=>$row['cfac_base_cero'],
												 "cfac_base_iva"=>$row['cfac_base_iva'],
												 "cfac_subtotal"=>$row['cfac_subtotal'],
												 "plu_impuesto"=>$row['plu_impuesto'],
												 "cfac_iva"=>trim($row['cfac_iva']));																	
					}					
				}	
				$this->lc_regs['str'] = $this->fn_numregistro();  
				return json_encode($this->lc_regs);									
		break;
		
		
		
		case 'valida_seguridad_usuario':  	
			$lc_sql="exec desc_valida_seguridad_descuento $lc_datos[0]";
						if($result = $this->fn_ejecutarquery($lc_sql)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs['prf_id'] = $row["prf_id"];										
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);							
		break;
		
		
		case 'validarUsuario':  	
			$lc_sql="exec desc_validaUsuario $lc_datos[0], '$lc_datos[1]'";
						if($result = $this->fn_ejecutarquery($lc_sql)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs['admini'] = $row["admini"];										
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);							
		break;
		
		case 'muestraTipoCuenta':  	
			$lc_sql="exec fac_muestraTipoCuenta";
						if($result = $this->fn_ejecutarquery($lc_sql)) 
							{ 
							while($row = $this->fn_leerarreglo()) 
								{
										$this->lc_regs[] = array(
																 "tptar_id"=>trim($row['tptar_id']),												
																"tptar_descripcion"=>$row['tptar_descripcion'],
																"tptar_codigo"=>$row['tptar_codigo']
																);													
								}					
							}	
						$this->lc_regs['str'] = $this->fn_numregistro();  
						return json_encode($this->lc_regs);										
		break;
		
		case 'lee_canalXMLfirmado':  	
		$lc_sql="declare @claveAcceso varchar(50)
				set @claveAcceso=(select cfac_claveAcceso from Cabecera_Factura where cfac_id='$lc_datos[0]')
				if exists(select * from Canal_Movimiento_comprobante where std_id=51 and cmp_nombre_comprobante=@claveAcceso)
					begin
						select 'si' as existe,cmp_id,cmp_nombre_comprobante as nombreComprobante from Canal_Movimiento_comprobante where cmp_nombre_comprobante=@claveAcceso and  
						std_id=51 
					end
				else
						select 'no' as existe
					/*if exists(select fir_id,substring(fir_nombre_comprobante,1,49) from canal_firma_comprobante)
					begin
					select 'si' as existe,fir_id,substring(fir_nombre_comprobante,1,49) as nombreComprobante, fir_nombre_comprobante from canal_firma_comprobante
					end
					else
					select 'no' as existe*/";
			if($result = $this->fn_ejecutarquery($lc_sql)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs['existe'] = $row["existe"];
									//echo $row["existe"];
									if($row["existe"]=='si')
									{
										$this->lc_regs['cmp_id'] = $row["cmp_id"];
										$this->lc_regs['nombreComprobante'] = $row["nombreComprobante"];
										//$this->lc_regs['fir_nombre_comprobante'] = $row["fir_nombre_comprobante"];								
									}
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
		break;
					
					
		case 'verifica_respuesta':  	
			$lc_sql="SELECT ltrim(rtrim(cres_codigo)) as cres_codigo FROM SWT_Respuesta_Autorizacion where rsaut_id=$lc_datos[2]";
			$result = $this->fn_ejecutarquery($lc_sql);
			if ($result){ return true; }else{ return false; };
		break;
		


}
///////////////////////////////////////////////FIN DE LA CLASE/////////////////////////////////////////
}

}


?>