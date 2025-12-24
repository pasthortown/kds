<?php

///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR JOSE FERNANDEZ////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE CONFIGURACION DE POMOCION//////////////
////////////////TABLAS: PROMOCION///////////////////////////////////////////
////////FECHA CREACION: 26/03/2014/////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

class promociones extends sql

{		
	public function fn_consultar($lc_opcion, $lc_datos)
	{
		switch($lc_opcion)
		{
		
			
			case 'Cargar_Cadena':
				$lc_query="select cdn_id, cdn_descripcion from Cadena order by cdn_descripcion";								
							if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
							while($row = $this->fn_leerarreglo()) 
								{					
								$this->lc_regs[] = array("cdn_id"=>$row['cdn_id'],
										"cdn_descripcion"=>htmlentities(trim($row['cdn_descripcion'])));
								}	
							$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			case "Cargar_Restaurante": 
							$lc_query="SELECT rst_id,rst_cod_tienda + Space(2)+ rst_descripcion as Descripcion  
						 	FROM Restaurante
						 	WHERE cdn_id=$lc_datos --and Estado=1 
						 	order by rst_cod_tienda";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
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
						
			
			case "cargarDetalle": 
							$lc_query="select distinct(p.pro_id) as pro_id,pro_descripcion,p.pro_nombre,convert( varchar (15),p.pro_fechainicio ,103) as pro_fechainicio,convert( varchar (15),p.pro_fechafin ,103) as pro_fechafin
from Restaurante r inner join Restaurante_Promocion rp on r.rst_id=rp.rst_id
					inner join Promocion p on rp.pro_id=p.pro_id
where r.cdn_id=$lc_datos[0]
							
							/*select rp.pro_id, p.pro_nombre, convert( varchar (15),p.pro_fechainicio ,103) as pro_fechainicio, convert( varchar (15), p.pro_fechafin 
										,103) as  pro_fechafin 
										from Restaurante r inner join Restaurante_Promocion rp on rp.rst_id=r.rst_id  
				   											inner join Promocion p on p.pro_id=rp.pro_id where r.rst_id=$lc_datos[0]*/";						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								$this->lc_regs[] = array("pro_id"=>$row['pro_id'],
										"pro_descripcion"=>$row['pro_descripcion'],
										"pro_nombre"=>$row['pro_nombre'],
										"pro_fechainicio"=>$row['pro_fechainicio'],
										"pro_fechafin"=>$row['pro_fechafin']);
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;//
			
			case "cargarDetalleRegion": 
							$lc_query="select rst_id,re.rst_cod_tienda + Space(2)+ re.rst_descripcion as Descripcion 
	from Region r inner join Provincia p on r.rgn_id=p.rgn_id
					inner join Ciudad c on c.prv_id=p.prv_id
					inner join Restaurante re on re.ciu_id=c.ciu_id
					where r.rgn_id=$lc_datos[0] and re.cdn_id=$lc_datos[1]
					order by rst_cod_tienda";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
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
			
			
			case "cargardetalleregionModificada": 
							$lc_query="select rst_id,re.rst_cod_tienda + Space(2)+ re.rst_descripcion as Descripcion 
										from Region r inner join Provincia p on r.rgn_id=p.rgn_id
										inner join Ciudad c on c.prv_id=p.prv_id
										inner join Restaurante re on re.ciu_id=c.ciu_id
										where r.rgn_id=$lc_datos[0] and re.cdn_id=$lc_datos[1] and re.rst_id not in($lc_datos[2])
										order by rst_cod_tienda";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
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
			
			case "cargarDetalleCiudadmodificada": 
							$lc_query="select rst_id,re.rst_cod_tienda + Space(2)+ re.rst_descripcion as Descripcion 
										from Region r inner join Provincia p on r.rgn_id=p.rgn_id
										inner join Ciudad c on c.prv_id=p.prv_id
										inner join Restaurante re on re.ciu_id=c.ciu_id
										where c.ciu_id=$lc_datos[0] and re.cdn_id=$lc_datos[1] and re.rst_id not in($lc_datos[2])
										order by rst_cod_tienda";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
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
			
			case "cargarDetalleProvinciamodificada": 
							$lc_query="select rst_id,re.rst_cod_tienda + Space(2)+ re.rst_descripcion as Descripcion 
										from Region r inner join Provincia p on r.rgn_id=p.rgn_id
										inner join Ciudad c on c.prv_id=p.prv_id
										inner join Restaurante re on re.ciu_id=c.ciu_id
										where p.prv_id=$lc_datos[0] and re.cdn_id=$lc_datos[1] and re.rst_id not in($lc_datos[2])
										order by rst_cod_tienda";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
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
			
			
			case "cargarDetalleProvincia": 
							$lc_query="select rst_id,re.rst_cod_tienda + Space(2)+ re.rst_descripcion as Descripcion 
	from Region r inner join Provincia p on r.rgn_id=p.rgn_id
					inner join Ciudad c on c.prv_id=p.prv_id
					inner join Restaurante re on re.ciu_id=c.ciu_id
					where p.prv_id=$lc_datos[0] and re.cdn_id=$lc_datos[1]
					order by rst_cod_tienda";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
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
			
			case "cargarProvincia": 
							$lc_query="select prv_id,prv_descripcion from Provincia";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								$this->lc_regs[] = array("prv_id"=>$row['prv_id'],										
										"prv_descripcion"=>utf8_encode($row['prv_descripcion']));
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			case "cargarCiudad": 
							$lc_query="select ciu_id,ciu_nombre from ciudad";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								$this->lc_regs[] = array("ciu_id"=>$row['ciu_id'],										
										"ciu_nombre"=>$row['ciu_nombre']);
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			
			case "cargatxtcadena": 
							$lc_query="select ltrim(rtrim(cdn_descripcion)) as cdn_descripcion from Cadena  where cdn_id=$lc_datos[0]";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								$this->lc_regs['cdn_descripcion'] =utf8_encode($row["cdn_descripcion"]);
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			case "cargarRegion": 
							$lc_query="select rgn_id,rgn_descripcion from Region";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{													
								$this->lc_regs[] = array("rgn_id"=>$row['rgn_id'],
										"rgn_descripcion"=>$row['rgn_descripcion']);
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;  
			
			case "cargarcheckRestaurantes": 
							$lc_query="select r.rst_id,rp.pro_id,rst_cod_tienda + Space(2)+ rst_descripcion as Restaurante 
from Restaurante r inner join Restaurante_Promocion rp on rp.rst_id=r.rst_id  
				   inner join Promocion p on p.pro_id=rp.pro_id where p.pro_id=$lc_datos[0]
				   order by rst_cod_tienda";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{													
								$this->lc_regs[] = array("pro_id"=>$row['pro_id'],
										"Restaurante"=> utf8_encode($row['Restaurante']),
										"rst_id"=>$row['rst_id']);
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break; 
			
			case "cargarestacionModifica": 
							$lc_query="select r.cdn_id,e.est_id,SUBSTRING(e.est_ip,1,3) as ipUno,SUBSTRING(e.est_ip,5,3) as ipDos,SUBSTRING(e.est_ip,9,3) as ipTres,SUBSTRING(e.est_ip,13,3) as ipCuatro
,e.est_nombre,e.rst_id,e.std_id
from Estacion e inner join Restaurante r on r.rst_id=e.rst_id where est_id=$lc_datos[0]";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								
										$this->lc_regs['cdn_id'] = $row["cdn_id"];
										$this->lc_regs['est_id'] = $row["est_id"];
										$this->lc_regs['est_nombre'] = $row["est_nombre"];										
										$this->lc_regs['ipUno'] = $row["ipUno"];
										$this->lc_regs['ipDos'] = $row["ipDos"];
										$this->lc_regs['ipTres'] = $row["ipTres"];
										$this->lc_regs['ipCuatro'] = $row["ipCuatro"];										
										$this->lc_regs['rst_id'] = $row["rst_id"];
										$this->lc_regs['std_id'] = $row["std_id"];
									
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;//
			
			
			case "cargarpromocionModificada": 
							$lc_query="select p.pro_descripcion, p.pro_nombre, convert( varchar (15),p.pro_fechainicio ,103) as pro_fechainicio, convert( varchar (15), p.pro_fechafin ,103) as  pro_fechafin 
from Promocion p  where p.pro_id=$lc_datos[0]/*select rp.pro_id,p.pro_descripcion, p.pro_nombre, convert( varchar (15),p.pro_fechainicio ,103) as pro_fechainicio, convert( varchar (15), p.pro_fechafin ,103) as  pro_fechafin 
from Restaurante r inner join Restaurante_Promocion rp on rp.rst_id=r.rst_id  
				   inner join Promocion p on p.pro_id=rp.pro_id where r.rst_id=$lc_datos[0]*/";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								
										//$this->lc_regs['pro_id'] = $row["pro_id"];
										$this->lc_regs['pro_descripcion'] = $row["pro_descripcion"];
										$this->lc_regs['pro_nombre'] = $row["pro_nombre"];										
										$this->lc_regs['pro_fechainicio'] = $row["pro_fechainicio"];
										$this->lc_regs['pro_fechafin'] = $row["pro_fechafin"];														
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			
				case "cargarNuevo": 
							$lc_query="select ltrim(rtrim(c.cdn_descripcion)) as cdn_descripcion ,rst_cod_tienda + Space(2)+ rst_descripcion as Restaurante
from Restaurante r inner join Cadena c on r.cdn_id=c.cdn_id
where r.rst_id=$lc_datos[1]";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{													
										$this->lc_regs['cdn_descripcion'] =utf8_encode( $row["cdn_descripcion"]);
										$this->lc_regs['Restaurante'] = $row["Restaurante"];																																													
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			
			case "cargarDetallePromocion": 
							$lc_query="SELECT rst_id,rst_cod_tienda + Space(2)+ rst_descripcion as Descripcion  
						 	FROM Restaurante where cdn_id=$lc_datos[0] order by rst_cod_tienda";
						
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
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
			
			case "cargarDetalleCiudad": 
							$lc_query="select rst_id,re.rst_cod_tienda + Space(2)+ re.rst_descripcion as Descripcion 
	from Region r inner join Provincia p on r.rgn_id=p.rgn_id
					inner join Ciudad c on c.prv_id=p.prv_id
					inner join Restaurante re on re.ciu_id=c.ciu_id
					where re.ciu_id=$lc_datos[0] and re.cdn_id=$lc_datos[1]
					order by rst_cod_tienda";
						
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
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
		

		}
	}
		public function fn_ejecutar($lc_opcion,$lc_datos)
	{
		switch($lc_opcion)
		{
			case "grabaPromocion": 
					$lc_query="set dateformat dmy
								declare @id int
								set @id=(Select MAX(pro_id +1) from Promocion)
								if (@id is null)
								begin
									set @id = 1
								end																							
								insert into Promocion values(@id,'$lc_datos[0]','$lc_datos[1]',UPPER('$lc_datos[2]'),UPPER('$lc_datos[3]'),NULL)";
						 
								$result = $this->fn_ejecutarquery($lc_query);
								if ($result){ return true; }else{ return false; };
			break;
			case "actualizaEstacion": 
$lc_query="update Estacion set est_ip ='$lc_datos[0].$lc_datos[1].$lc_datos[2].$lc_datos[3]',est_nombre=UPPER('$lc_datos[4]'), std_id=$lc_datos[6] where est_id=$lc_datos[5]";
						 
							$result = $this->fn_ejecutarquery($lc_query);
							if ($result){ return true; }else{ return false; };
			break;//
			
			case "grabaPromocionRestaurante": 
		$lc_query="declare @codPromocion int 
								set @codPromocion=(select MAX(pro_id) from Promocion)
								
								insert into Restaurante_Promocion values($lc_datos[0],@codPromocion)";
						 
								$result = $this->fn_ejecutarquery($lc_query);
								if ($result){ return true; }else{ return false; };
			break;//
			
			
			case "actualizacheckRestauranteagregados": 
		$lc_query="insert into Restaurante_Promocion values($lc_datos[0],$lc_datos[1])";
						 
		$result = $this->fn_ejecutarquery($lc_query);
		if ($result){ return true; }else{ return false; };
			break;
			
			
			case "actualizaPromocion": 
		$lc_query="set dateformat dmy
				update Promocion set pro_descripcion=UPPER('$lc_datos[3]'), pro_fechafin='$lc_datos[1]', pro_fechainicio='$lc_datos[0]', pro_nombre=UPPER('$lc_datos[2]') where pro_id=$lc_datos[4]";
						 
				$result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };
			break;
			
			case "actualizacheckRestaurante": 
				$lc_query="select * into #tmp_original from  Restaurante_Promocion  where pro_id=$lc_datos[0]
				delete from Restaurante_Promocion where rst_id in(select rst_id from #tmp_original where rst_id not in ($lc_datos[1]))
				drop table #tmp_original";
						 
				$result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };
			break;
		}
	}
}
?>
	
