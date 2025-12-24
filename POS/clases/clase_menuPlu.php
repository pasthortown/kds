<?php
///////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jorge Sánchez ////////////////////
///////DESCRIPCION:Facturas Preimpresas ///////////////////
///////TABLAS INVOLUCRADAS: Facturas_Preimpresas//////// //
///////FECHA CREACION: 10-10-2012//////////////////////////
///////FECHA ULTIMA MODIFICACION:26/09/2013////////////////
///////USUARIO QUE MODIFICO:  Jose Fernandez///////////////
///////DECRIPCION ULTIMO CAMBIO: Detalle de Facturas///////
///////////////////////////////////////////////////////////  


class plu extends sql
{
	//private $lc_regs;
	//constructor de la clase
	function __construct()
	{
		parent ::__construct();
	}
	//funcion que permite armar la sentencia sql de consulta
	
	function fn_consultar($lc_sqlQuery,$lc_datos)
		{ 	
			switch($lc_sqlQuery)
			{
				case 'eliminarPlu':
		 		 	$lc_sql="DELETE FROM Menu_Agrupacionproducto 
				 		  WHERE magp_id=$lc_datos[0]";
					$result = $this->fn_ejecutarquery($lc_sql);
					if ($result){ return true; }else{ return false; };
		 		break;
				
				case "cargarCaracteristica":
				$lc_sql="SELECT p.plu_id, p.plu_descripcion, p.cdn_id, mg.magp_color, mg.magp_colortexto,                     	 mg.magp_desc_impresion, mg.magp_orden, mg.magp_imagen_boton, mg.magp_id  
						  FROM Plus p
						  inner join Menu_Agrupacionproducto mg on p.plu_id=mg.plu_id
						  --WHERE mg.magp_id=1+1
						  ORDER by magp_id asc";   
						  //$_SESSION[sess_restaurante]
						if($result = $this->fn_ejecutarquery($lc_sql)) 
						{ 
							while($row = $this->fn_leerarreglo()) 
							{		
								$this->lc_regs[] = array("plu_descripcion"=>$row['plu_descripcion']
													  ,"magp_desc_impresion"=>$row['magp_desc_impresion']
													  ,"magp_colortexto"=>$row['magp_colortexto']
													  ,"magp_color"=>$row['magp_color']
													  ,"plu_id"=>$row['plu_id']
													  ,"magp_id"=>$row['magp_id']);							
							}
						}	
						$this->lc_regs['str'] = $this->fn_numregistro();  
						
					return json_encode($this->lc_regs);	
				break;
				
						
				case "buscarCaracteristica": 
				$lc_sql="SELECT * FROM Menu_Agrupacionproducto  mg
						inner join Plus p on p.plu_id=mg.plu_id
						WHERE p.plu_descripcion like '%$lc_datos%'";
					if($result = $this->fn_ejecutarquery($lc_sql)) {
						while($row = $this->fn_leerarreglo()) {					
							$this->lc_regs[] = array("plu_descripcion"=>$row['plu_descripcion']
													,"value"=>strtoupper(utf8_encode(trim($row['plu_descripcion'])))
													  ,"magp_desc_impresion"=>$row['magp_desc_impresion']
													  ,"magp_colortexto"=>$row['magp_colortexto']
													  ,"magp_color"=>$row['magp_color']
													  ,"plu_id"=>$row['plu_id']
													  ,"magp_id"=>$row['magp_id']);
						}	
					}
					return json_encode($this->lc_regs);	
				break;
				
				case "cargaPlu":
				$lc_sql="SELECT p.plu_id, p.plu_descripcion, p.cdn_id, p.std_id, r.rst_id, plu_num_plu
						FROM Plus p
						inner join Restaurante r on p.cdn_id=r.cdn_id
						WHERE p.std_id not in (0)
						and r.rst_id=239
						ORDER by plu_id desc";   
						 
						if($result = $this->fn_ejecutarquery($lc_sql)) 
						{ 
							while($row = $this->fn_leerarreglo()) 
							{		
								$this->lc_regs[] = array("plu_descripcion"=>$row['plu_descripcion']
														 ,"plu_id"=>$row['plu_id']
														 ,"plu_num_plu"=>$row['plu_num_plu']);							
							}
						}	
						$this->lc_regs['str'] = $this->fn_numregistro();  
						
					return json_encode($this->lc_regs);	
				break;
				
				case "duplicarPlu":
					$lc_sql= "
					declare @nombrePluid varchar(150),@nombrePlu varchar(150),@colorTexto char(20),@colorFondo varchar(50),@codigoPlu int
					 set @nombrePluid='$lc_datos[0]'
					 set @nombrePlu='$lc_datos[1]'
					 set @colorTexto='$lc_datos[2]'
					 set @colorFondo='$lc_datos[3]'
					 set @codigoPlu='$lc_datos[4]'
					
					INSERT into Menu_Agrupacionproducto (std_id, plu_id, magp_desc_impresion, magp_colortexto, magp_color)				values(0,@codigoPlu,@nombrePlu,@colorTexto,@colorFondo)
					
					SELECT p.plu_id, p.plu_descripcion, p.cdn_id, mg.magp_color, mg.magp_colortexto, mg.magp_desc_impresion, mg.magp_orden, mg.magp_imagen_boton, magp_id  
						  FROM Plus p
						  inner join Menu_Agrupacionproducto mg on p.plu_id=mg.plu_id
						  WHERE p.std_id not in (0)
						  ORDER by magp_id asc";
							 
					if($result = $this->fn_ejecutarquery($lc_sql)) 
						{ 
							while($row = $this->fn_leerarreglo()) 
							{		
								$this->lc_regs[] = array("plu_descripcion"=>$row['plu_descripcion']
													  ,"magp_desc_impresion"=>$row['magp_desc_impresion']
													  ,"magp_colortexto"=>$row['magp_colortexto']
													  ,"magp_color"=>$row['magp_color']
													  ,"plu_id"=>$row['plu_id']
													  ,"magp_id"=>$row['magp_id']);							
							}
						}	
						$this->lc_regs['str'] = $this->fn_numregistro();  
						
					return json_encode($this->lc_regs);	
				break;
				
				case "guardarPlu":
					$lc_sql= "
					declare @nombrePluid varchar(150),@nombrePlu varchar(150),@colorTexto char(20),@colorFondo varchar(50),@codigoPlu int
					 set @nombrePluid='$lc_datos[0]'
					 set @nombrePlu='$lc_datos[1]'
					 set @colorTexto='$lc_datos[2]'
					 set @colorFondo='$lc_datos[3]'
					 set @codigoPlu='$lc_datos[4]'
					 if exists(select * from Menu_Agrupacionproducto where magp_id=@codigoPlu)
					 begin
					 	UPDATE Menu_Agrupacionproducto set magp_desc_impresion=@nombrePlu,magp_colortexto=@colorTexto, magp_color=@colorFondo where magp_id=@codigoPlu
					 end
					 else 
					 begin
					INSERT into Menu_Agrupacionproducto (std_id, plu_id, magp_desc_impresion, magp_colortexto, magp_color)				values(0,@codigoPlu,@nombrePlu,@colorTexto,@colorFondo)
					end
					SELECT p.plu_id, p.plu_descripcion, p.cdn_id, mg.magp_color, mg.magp_colortexto, mg.magp_desc_impresion, mg.magp_orden, mg.magp_imagen_boton, magp_id  
						  FROM Plus p
						  inner join Menu_Agrupacionproducto mg on p.plu_id=mg.plu_id
						  WHERE p.std_id not in (0)
						  ORDER by magp_id asc";
							 
					if($result = $this->fn_ejecutarquery($lc_sql)) 
						{ 
							while($row = $this->fn_leerarreglo()) 
							{		
								$this->lc_regs[] = array("plu_descripcion"=>$row['plu_descripcion']
													  ,"magp_desc_impresion"=>$row['magp_desc_impresion']
													  ,"magp_colortexto"=>$row['magp_colortexto']
													  ,"magp_color"=>$row['magp_color']
													  ,"plu_id"=>$row['plu_id']
													  ,"magp_id"=>$row['magp_id']);							
							}
						}	
						$this->lc_regs['str'] = $this->fn_numregistro();  
						
					return json_encode($this->lc_regs);	
				break;
			}
	}
}
?>			