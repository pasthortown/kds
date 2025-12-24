<?php

///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR JOSE FERNANDEZ////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE CONFIGURACION DE POMOCION//////////////
////////////////TABLAS: PROMOCION///////////////////////////////////////////
////////FECHA CREACION: 26/03/2014/////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

class usuarios extends sql

{		
	public function fn_consultar($lc_opcion, $lc_datos)
	{
		switch($lc_opcion)
		{
		
			
			case 'cargarCodUsuario':
				$lc_query="select MAX(usr_id)+1 as codUsuario from Users_Pos";								
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
							while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs['codUsuario'] = $row["codUsuario"];
								}	
							$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			case 'validaPassDuplicado':
				$lc_query="if exists(select usr_clave from Users_Pos where (PWDCOMPARE('$lc_datos[0]',usr_clave)=1) and rst_id=$lc_datos[1])
							begin
								select 1 as existe
							end
							else
							begin
							 select 0 as existe
							end";								
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
							while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs['existe'] = $row["existe"];
								}	
							$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			case 'validaTarjetaDuplicado':
				$lc_query="if exists(select usr_tarjeta from Users_Pos where (PWDCOMPARE('$lc_datos[0]',usr_tarjeta)=1) and rst_id=$lc_datos[1])
							begin
								select 1 as existe
							end
							else
							begin
							 select 0 as existe
							end";								
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
							while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs['existe'] = $row["existe"];
								}	
							$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			case 'cargarUsuarioModificado':
				$lc_query="declare @ini int,@fin int ,@pag int, @tien varchar(4),@eactivo int
						set @eactivo=(select std_id from Estado where mdl_id=7 and std_descripcion like 'activo')
				select u.usr_id, u.usr_descripcion,u.usr_usuario,p.prf_id ,p.prf_descripcion,u.usr_iniciales ,case u.std_id when 13 then 'Activo' WHEN 25 THEN 'Inactivo' when @eactivo then 'Activo' END AS std_id,r.rst_id
from Users_Pos u inner join Perfil_Pos p on p.prf_id=u.prf_id
inner join Restaurante r on r.rst_id=u.rst_id
where u.usr_id=$lc_datos[0]";								
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
							while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs['usr_id'] = $row["usr_id"];
									$this->lc_regs['usr_descripcion'] = $row["usr_descripcion"];
									$this->lc_regs['usr_usuario'] = $row["usr_usuario"];
									$this->lc_regs['prf_id'] = $row["prf_id"];
									$this->lc_regs['prf_descripcion'] = $row["prf_descripcion"];
									$this->lc_regs['usr_iniciales'] = $row["usr_iniciales"];
									$this->lc_regs['std_id'] = $row["std_id"];
									$this->lc_regs['rst_id'] = $row["rst_id"];
								}	
							$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
								
			case 'cargarPerfil':
				$lc_query="select prf_id,prf_descripcion from Perfil_Pos";								
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
							while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs[] = array("prf_id"=>$row['prf_id'],
										"prf_descripcion"=> utf8_encode($row['prf_descripcion']));
										
								}	
							$this->lc_regs['str'] = $this->fn_numregistro();
							}
				return json_encode($this->lc_regs);	
			break;
			
			case 'grabaUsuario':
				$lc_query="declare @var varbinary(256),@usu int, @varTarjeta  varbinary(256),@eActivo int
				set @eActivo=(select std_id from estado where mdl_id=7 and std_descripcion like 'Activo%')
				set @var=pwdencrypt('$lc_datos[5]')";
				if($lc_datos[7]!='')
				{
				$lc_query.=" set @varTarjeta=pwdencrypt('$lc_datos[7]')";
				}
				else
				{
					$lc_query.=" set @varTarjeta=NULL";
				}
				$lc_query.=" insert into Users_Pos 	values($lc_datos[3],$lc_datos[4],'$lc_datos[2]',@var,'$lc_datos[1]','$lc_datos[0]',@varTarjeta,@eActivo)							
					set @usu=(select MAX(usr_id) from Users_Pos)
					insert into Auditoria_Usuario values(GETDATE(),'nuevoUser',@usu,$lc_datos[3],$lc_datos[6])";								
				return $this->fn_ejecutarquery($lc_query);
			break;
			
			case 'eliminaUsuario':
				$lc_query="declare @estado int,@rst int
				set @estado=(select std_id from estado where mdl_id=7 and std_descripcion like 'Inactivo%')
				update Users_Pos set std_id=@estado where usr_id=$lc_datos[0]
				
				set @rst=(select rst_id from users_pos where usr_id=$lc_datos[0])
				insert into Auditoria_Usuario values(GETDATE(),'eliminaUser',$lc_datos[0],@rst,$lc_datos[1])";								
				return $this->fn_ejecutarquery($lc_query);
			break;
			
			case 'actualizaUsuario':
				$lc_query="declare @est int,@var varbinary(256), @varTarjeta varbinary(256)";
				
				if($lc_datos[10]!='')
				{
				$lc_query.=" set @varTarjeta=pwdencrypt('$lc_datos[10]') ";
				}
				else
				{
					$lc_query.=" set @varTarjeta=NULL ";
				}
								
				if($lc_datos[7]==1)
				{
				$lc_query.=" 
				set @var=pwdencrypt('$lc_datos[8]')";
				}
				$lc_query.=" 				
				update Users_Pos set rst_id=$lc_datos[3],prf_id=$lc_datos[4],usr_usuario='$lc_datos[2]',usr_descripcion='$lc_datos[0]',usr_iniciales='$lc_datos[1]',usr_tarjeta=@varTarjeta";
				
				if(isset($lc_datos[7]) && isset($lc_datos[8]))
						{
							if($lc_datos[7]==1)
								$lc_query.=" , usr_clave=@var ";							
						}
								
				$lc_query.=" where usr_id=$lc_datos[6] 
				insert into Auditoria_Usuario values(GETDATE(),'modificaUser',$lc_datos[6],$lc_datos[3],$lc_datos[9])";				
				
				if(isset($lc_datos[7]) && isset($lc_datos[8]))
						{
							if($lc_datos[7]==1)
								$lc_query.=" insert into Auditoria_Usuario values(GETDATE(),'modificaPassword',$lc_datos[6],$lc_datos[3],$lc_datos[9])";							
						}
				
				return $this->fn_ejecutarquery($lc_query);
			break;
			
			
			case 'cargaRestaurante':
				$lc_query="SELECT rst_id,rst_cod_tienda + Space(2)+ rst_descripcion as Descripcion  
						 	FROM Restaurante						 							 	
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
			
			case 'cargarUsuarios':
				$pagina=$lc_datos[0];
				$lc_query="declare @ini int,@fin int ,@pag int, @tien varchar(4),@eactivo int
						set @eactivo=(select std_id from Estado where mdl_id=7 and std_descripcion like 'activo')
						set @pag=".$pagina."
						set @ini=@pag*10
						set @fin=(@pag*10)+10						
						select * from (select ROW_NUMBER() OVER (ORDER BY u.usr_id asc) as fila,u.usr_id, u.usr_descripcion,u.usr_usuario,p.prf_descripcion,case u.std_id when 13 then 'Activo' WHEN 25 THEN 'Inactivo'  WHEN @eactivo THEN 'Activo' END AS std_id
from Users_Pos u inner join Perfil_Pos p on p.prf_id=u.prf_id";	
				if(isset($lc_datos[1]) && isset($lc_datos[2]))
										{
											if($lc_datos[1]=="usuario")
												$lc_query.=" where  u.usr_usuario like '%$lc_datos[2]%' ";
											if($lc_datos[1]=="nombre")
												$lc_query.=" where u.usr_descripcion like '%$lc_datos[2]%' ";
										}
				$lc_query.=" )as pg where fila > @ini and fila <= @fin";	
							if($this->fn_ejecutarquery($lc_query)) 
							{ 
							while($row = $this->fn_leerarreglo()) 
								{					
									$this->lc_regs[] = array("usr_id"=>$row['usr_id'],
										"usr_descripcion"=> utf8_encode($row['usr_descripcion']),
										"usr_usuario"=> utf8_encode($row['usr_usuario']),
										"prf_descripcion"=> utf8_encode($row['prf_descripcion']),
										"std_id"=> $row['std_id']);
										
								}	
							$this->lc_regs['str'] = $this->fn_numregistro();
							}
				$lc_query="select u.usr_id, u.usr_descripcion,u.usr_usuario,p.prf_descripcion,case u.std_id when 13 then 'Activo' WHEN 25 THEN 'Inactivo' END AS std_id
from Users_Pos u inner join Perfil_Pos p on p.prf_id=u.prf_id order by u.usr_id asc";

				if($this->fn_ejecutarquery($lc_query)) 
							{
								$lc_numeroRes = $this->fn_numregistro();	
								if($lc_numeroRes > 10)
								{									
									$lc_pagTotal = ceil($lc_numeroRes/10);
								} else 
								{
									$lc_pagTotal = 1;
								}
								$etiqOption = "<form id='formSel' style='margin:0px; padding:0px;'>P&aacute;gina <select onchange='";
								$etiqOption .="fn_paginado(0)' id='selOpt'>";
								for($i=0; $i<$lc_pagTotal; $i++) 
								{
								$etiqOption .= "<option>";
								$etiqOption .= ($i+1) ."</option>";
								}
								$etiqOption .= "</select></form>";
							}
					$this->lc_regs['str2'] = $etiqOption;	
					$this->lc_regs['paginas'] = $lc_pagTotal;	 
				return json_encode($this->lc_regs);	
			break;
			
		}
	}
	
}

	
