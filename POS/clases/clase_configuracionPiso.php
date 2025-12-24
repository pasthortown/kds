<?php

///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR JOSE FERNANDEZ////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE CONFIGURACION DE PISOS//////////////
////////////////TABLAS: PISOS///////////////////////////////////////////
////////FECHA CREACION: 26/03/2014/////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
////////MODIFICADO POR: CHRISTIAN PINTO////////////////////////////////////////
///////////DESCRIPCION: NUEVOS ESTILOS INGRESO Y ACTUALIZACION DE PISOS CON////
/////////////////////// TABLA MODAL ///////////////////////////////////////////
////////////////TABLAS: PISOS//////////////////////////////////////////////////
////////FECHA CREACION: 28/05/2015/////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
class estacion extends sql

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
							$lc_query="select pis_id,rst_id,pis_numero,case std_id
								when 39 then 'Activo'
								when 40 then 'Inactivo'
								end as Estado from Pisos
								where rst_id=$lc_datos[0]";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								$this->lc_regs[] = array("pis_id"=>$row['pis_id'],
										"rst_id"=>$row['rst_id'],
										"pis_numero"=>$row['pis_numero'],
										"Estado"=>$row['Estado']);
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			/////////////////////////////////////////////CP//////////////////////////////////////////////////////////////////
			case "cargarDetallePiso": 
							$lc_query="select ltrim(rtrim(c.cdn_descripcion)) as Cadena ,rst_cod_tienda + Space(2)+ 									                                      rst_descripcion as Restaurante,p.pis_numero AS PisoNumero,
									  CASE  p.std_id 
									  WHEN 39 THEN 'Activo' 
									  WHEN 40 THEN 'Inactivo' END AS Estado
									  from Restaurante r 
									  INNER JOIN Cadena c on r.cdn_id=c.cdn_id
									  INNER JOIN Pisos AS p ON p.rst_id=r.rst_id
									  WHERE p.pis_id=$lc_datos[0]";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								$this->lc_regs[] = array("Cadena"=>utf8_encode($row['Cadena']),
										"Restaurante"=>$row['Restaurante'],
										"PisoNumero"=>$row['PisoNumero'],
										"Estado"=>$row['Estado']);
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			case "cargarestacionModifica": 
							$lc_query="select pis_id,rst_id,pis_numero, std_id
										from Pisos e
										where pis_id=$lc_datos[0]";
						 
							if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								
										$this->lc_regs['pis_id'] = $row["pis_id"];
										$this->lc_regs['rst_id'] = $row["rst_id"];
										$this->lc_regs['pis_numero'] = $row["pis_numero"];										
										$this->lc_regs['std_id'] = $row["std_id"];									
									
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
								$this->lc_regs[] = array("Restaurante"=>$row['Restaurante']);
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
			case "grabaEstacion": 
					$lc_query=" EXECUTE usp_cnf_insertaPiso $lc_datos[5],$lc_datos[3]";
						 
					if($result = $this->fn_ejecutarquery($lc_query)) 
							{ 
								while($row = $this->fn_leerarreglo()) 
								{					
								
									$this->lc_regs[] = array("ExistePiso"=>$row['ExistePiso']);							
									
								}	
					$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);
			break;
			case "actualizaEstacion": 
				$lc_query="update Pisos set pis_numero =$lc_datos[3], std_id=$lc_datos[6] where pis_id=$lc_datos[5]";
						 
				$result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };
				
			break;
		}
	}
}
?>
	
