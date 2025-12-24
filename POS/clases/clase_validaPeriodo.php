<?php
//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Jose Fernandez//////////////////////
///////////DESCRIPCION: PANTALLA DE APERTURA DE PERIODO///////
////////////////TABLAS: PERIODO///////////////////////////////
////////FECHA CREACION: 20/12/2013////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 
///////FECHA MODIFICACION: 10-04-2015 ////////////////////////
///////USUARIO QUE MODIFICO: Jimmy Cazaro ////////////////////
///////DECRIPCION ULTIMO CAMBIO: Anexo funci�n que ///////////
///////devuelva el nombre de la estaci�n /////////////////////
////////////////////////////////////////////////////////////// 
class periodo extends sql 
{
  //constructor
 function __construct()
   {
	   
	//herencia 
	parent::__construct();
   }
   
 function fn_validaPeriodo($lc_rest)
 {	
	$lc_query = "
declare @tiporest int
set @tiporest=(select rst_horarioatencion from Restaurante where rst_id=".$lc_rest.")	
if(@tiporest=0)
begin
	declare @fecha_actual date,@fecha_periodo date,@dia int,@estado int
	set @fecha_actual =(select CONVERT(varchar (max),GETDATE(),121) as fecha_actual)
	set @fecha_periodo =(select TOP 1 convert(varchar (max),max(prd_fechaapertura),121) as fecha_periodo from Periodo where rst_id=".$lc_rest." AND std_id <> 9)	
	set @dia=(SELECT DATEDIFF(DAY,@fecha_periodo, @fecha_actual) as dia)
	set @estado=(select std_id from Periodo where prd_id=(select MAX(prd_id) from Periodo where rst_id=".$lc_rest."))
	if @fecha_periodo is null
	begin 
		select 9 Estado_Periodo
	end
	if (@dia=1)
	begin
		select std_id as Estado_Periodo
		from Periodo 
		where rst_id=".$lc_rest." and prd_id=(select max(prd_id) from Periodo where rst_id=".$lc_rest.")
		/*select MAX(prd_id)as prd_id, std_id as Estado_Periodo
		from Periodo 
		where rst_id=100
		group by std_id	*/
	end
	if (@dia=0)
	begin
		select 6 as Estado_Periodo
	end	
	if (@dia>1 and @estado=9)
	begin
		select 9 as Estado_Periodo
	end	
	if (@dia>1 and @estado=6)
	begin
		select 6 as Estado_Periodo
	end
end  
else
begin
	select 24 as Estado_Periodo
end";
	if($this->fn_ejecutarquery($lc_query))
	 {
		
		 while($lc_row = $this->fn_leerobjeto())
		  {
			$lc_numreg = $this->fn_numregistro();
			if ( $lc_numreg > 0)
			{ 
			 return $lc_row->Estado_Periodo;
			  
			}
			else
			{  
				return 0;
			}//='".$_SESSION['rstId']."'
		 } 
     
 	}  
 }
 
 function fn_validaIpestacion($ip)
	{
		$lc_query = "if exists(select e.est_ip from Estacion e where e.est_ip='".$ip."')
begin
select 1 as Existe
end
else
select 2 as Existe";		
if($this->fn_ejecutarquery($lc_query))
	 {
		
		 while($lc_row = $this->fn_leerobjeto())
		  {
			$lc_numreg = $this->fn_numregistro();
			if ( $lc_numreg > 0)
			{ 
			 return $lc_row->Existe;
			  
			}
			else
			{  
				return 0;
			}//='".$_SESSION['rstId']."'
		 } 
     
 	}
	}//
	
	function fn_getEstacionId($ip)
	{
		$lc_query = "select est_id from Estacion where est_ip='".$ip."'";		
		if($this->fn_ejecutarquery($lc_query))
	 	{
			while($lc_row = $this->fn_leerobjeto())
		  	{
				$lc_numreg = $this->fn_numregistro();
				if ( $lc_numreg > 0)
				{ 
				 	return $lc_row->est_id;
				}
				else
				{  
					return 0;
				}//='".$_SESSION['rstId']."'
		 	} 
 		}
	}
	
	function fn_getEstacionNombre($ip)
	{
		$lc_query = "SELECT est_nombre FROM Estacion WHERE est_ip='".$ip."'";		
		if($this->fn_ejecutarquery($lc_query))
	 	{
			while($lc_row = $this->fn_leerobjeto())
		  	{
				$lc_numreg = $this->fn_numregistro();
				if ( $lc_numreg > 0)
				{ 
					return $lc_row->est_nombre;
				}
				else
				{  
					return '';
				}//='".$_SESSION['rstId']."'
		 	} 
 		}
	}	
	
 function fn_validaPeriodoCerrado($rest)
	{
		$lc_query = "declare @fecha_apertura date, @fecha_actual date
set @fecha_apertura=(select max(CONVERT(varchar (11),prd_fechaapertura,121)) from Periodo where rst_id=".$rest." and std_id=9)
set @fecha_actual=(select CONVERT(varchar(11),GETDATE(),121))
if (@fecha_actual=@fecha_apertura)
begin
	select 1 as Periodo_Abierto
end
else
begin
	select 2 as Periodo_Abierto
end


";		
if($this->fn_ejecutarquery($lc_query))
	 {
		
		 while($lc_row = $this->fn_leerobjeto())
		  {
			$lc_numreg = $this->fn_numregistro();
			if ( $lc_numreg > 0)
			{ 
			 return $lc_row->Periodo_Abierto;
			  
			}
			else
			{  
				return 0;
			}//='".$_SESSION['rstId']."'
		 } 
     
 	}
	}
//fn_validaLogueo
	 function fn_validaLogueo($idUsuario,$lc_ip)
	{
		$lc_query = "declare @ipSesion varchar(16),@estacion int
set @ipSesion='".$lc_ip."'
set @estacion=(select est_id from Estacion where est_ip=@ipSesion)

if exists(select * from Control_Estacion where usr_id=".$idUsuario." and std_id=14 and est_id <> @estacion)
begin
select 1 as Logueado
end
else
begin
select 2 as Logueado
end
		/*if exists(select * from Control_Estacion where usr_id=".$idUsuario." and std_id=14)
begin
select 1 as Logueado
end
else
begin
select 2 as Logueado
end*/";		
if($this->fn_ejecutarquery($lc_query))
	 {
		
		 while($lc_row = $this->fn_leerobjeto())
		  {
			$lc_numreg = $this->fn_numregistro();
			if ( $lc_numreg > 0)
			{ 
			 return $lc_row->Logueado;
			  
			}
			else
			{  
				return 0;
			}//='".$_SESSION['rstId']."'
		 } 
     
 	}
	}


	 function fn_validaLogueoEstacion($lc_ip)
	{
		$lc_query = "exec ind_verifica_usuario_logueado_caja '".$lc_ip."' ";		
if($this->fn_ejecutarquery($lc_query))
	 {
		
		 while($lc_row = $this->fn_leerobjeto())
		  {
			$lc_numreg = $this->fn_numregistro();
			if ( $lc_numreg > 0)
			{ 
			 return $lc_row->EnUso;
			  
			}
			else
			{  
				return 0;
			}//='".$_SESSION['rstId']."'
		 } 
     
 	}
	}






 function fn_grabacontrolEstacion($lc_rest,$lc_ip,$lc_Idusuario)
	{
		$lc_query = "declare @periodo int, @est_id varchar (11)
					set @periodo=(select max(prd_id) as Periodo 
									from Periodo where rst_id=".$lc_rest."  and std_id=6 and  convert(varchar(10),prd_fechaapertura,103)= 									
									convert(varchar(10),GETDATE(),103))
					set @est_id=(select est_id from Estacion where est_ip = '".$lc_ip."')
		if exists(select * from Control_Estacion where rst_id=".$lc_rest." and est_id=@est_id and 
						convert(varchar (10),ctrc_fecha_inicio,103)=convert(varchar (10),GETDATE(),103)
						and usr_id=".$lc_Idusuario." and prd_id=@periodo and std_id=14)
				begin
						update Control_Estacion set rst_id=".$lc_rest." where rst_id=".$lc_rest." and est_id=@est_id and 
						convert(varchar (10),ctrc_fecha_inicio,103)=convert(varchar (10),GETDATE(),103)
						and usr_id=".$lc_Idusuario." and prd_id=@periodo 
				end
				else
				begin
					insert into Control_Estacion (rst_id,est_id,std_id,usr_id,prd_id,ctrc_fecha_inicio,ctrc_fecha_salida,ctrc_fondo,ctrc_usuario_desmontarcaja,ctrc_motivo_descuadre)
					values(".$lc_rest.",@est_id,14,".$lc_Idusuario.",@periodo,GETDATE(),NULL,NULL,NULL,NULL)
				end	
					
			
				/*insert into Control_Estacion values(".$lc_rest.",@est_id,14,".$lc_Idusuario.",@periodo,GETDATE(),NULL,NULL,NULL,NULL)*/";		
		$this->fn_ejecutarquery($lc_query);	 
	}
 
function fn_validaSesion($ip,$lc_Idusuario)
	{
		$lc_query = "declare @estado int, @usuarioEstacion int, @usuarioSesion int
set @usuarioSesion=".$lc_Idusuario."
set @estado=(select ce.std_id from Control_Estacion ce inner join Estacion e on  ce.est_id=e.est_id where e.est_ip='".$ip."' and 
convert(varchar (10),ce.ctrc_fecha_inicio,103)=CONVERT(varchar (10),GETDATE(),103)  /*ce.std_id=14*/ /*and ce.usr_id=12*/
			and ce.ctrc_id = (select MAX(ctrc_id)from Control_Estacion /*where ce.std_id=14*/))

set @usuarioEstacion=(select ce.usr_id from Control_Estacion ce inner join Estacion e on  ce.est_id=e.est_id where e.est_ip='".$ip."'
 and convert(varchar (10),ce.ctrc_fecha_inicio,103)=CONVERT(varchar (10),GETDATE(),103) and ce.ctrc_id = (select MAX(ctrc_id)from Control_Estacion 
 /*where ce.std_id=14*/) /*ce.usr_id=13 *//*and ce.std_id=14*/ )
if(@estado is null and @usuarioEstacion is null)
begin
select 'ingresa_primera_vez' as std_id
end

if(@estado=14 and @usuarioEstacion=@usuarioSesion)
begin
select 'ingresa_mismo_usuario' as std_id
end

if(@estado=14 and @usuarioEstacion<>@usuarioSesion)
begin
select 'usuario_en_estacion' as std_id
end

if(@estado=15 and @usuarioEstacion is null)
begin
select 'ingresa_otro_usuario' as std_id
end

if(@estado=15 and @usuarioEstacion<>@usuarioSesion)
begin
select 'ingresa_otro_usuario' as std_id
end

if(@estado=15 and @usuarioEstacion=@usuarioSesion)
begin
select 'ingresa_otro_usuario' as std_id
end

if(@estado=14 and @usuarioEstacion is null)
begin
select 'usuario_en_estacion' as std_id
end



/*set @estado=(select ce.std_id from Control_Estacion ce inner join Estacion e on  ce.est_id=e.est_id where e.est_ip='".$ip."' and 
convert(varchar (10),ce.ctrc_fecha_inicio,103)=CONVERT(varchar (10),GETDATE(),103) /*and ce.std_id=14*/ /*and ce.usr_id=12*/)

set @usuarioEstacion=(select ce.usr_id from Control_Estacion ce inner join Estacion e on  ce.est_id=e.est_id where e.est_ip='".$ip."'
 and convert(varchar (10),ce.ctrc_fecha_inicio,103)=CONVERT(varchar (10),GETDATE(),103) and ce.usr_id=".$lc_Idusuario." /*and ce.std_id=14*/ )

if(@estado is null and @usuarioEstacion is null)
begin
select 'ingresa_primera_vez' as std_id
end

if(@estado=14 and @usuarioEstacion=@usuarioSesion)
begin
select 'ingresa_mismo_usuario' as std_id
end

if(@estado=14 and @usuarioEstacion<>@usuarioSesion)
begin
select 'usuario_en_estacion' as std_id
end

if(@estado=15 and @usuarioEstacion is null)
begin
select 'ingresa_otro_usuario' as std_id
end

if(@estado=14 and @usuarioEstacion is null)
begin
select 'usuario_en_estacion' as std_id
end*/
		
		/*declare @estado int, @usuarioEstacion int, @usuarioSesion int
set @usuarioSesion=".$lc_Idusuario."

set @estado=(select ce.std_id from Control_Estacion ce inner join Estacion e on  ce.est_id=e.est_id where e.est_ip='".$ip."' and 
convert(varchar (10),ce.ctrc_fecha_inicio,103)=CONVERT(varchar (10),GETDATE(),103) and ce.usr_id=".$lc_Idusuario.")

set @usuarioEstacion=(select ce.usr_id from Control_Estacion ce inner join Estacion e on  ce.est_id=e.est_id where e.est_ip='".$ip."' and convert(varchar (10),ce.ctrc_fecha_inicio,103)=CONVERT(varchar (10),GETDATE(),103) and ce.usr_id=".$lc_Idusuario." /*and ce.std_id=14*/ )

if(@estado is null and @usuarioEstacion is null)
begin
select 'ingresa_primera_vez' as std_id
end

if(@estado=14 and @usuarioEstacion=@usuarioSesion)
begin
select 'ingresa_mismo_usuario' as std_id
end

if(@estado=14 and @usuarioEstacion<>@usuarioSesion)
begin
select 'usuario_en_estacion' as std_id
end

if(@estado=15 /*and @usuarioEstacion<>@usuarioSesion*/)
begin
select 'ingresa_otro_usuario' as std_id
end*/

		
		/*if exists(select * from Control_Estacion ce inner join Estacion e on  ce.est_id=e.est_id where e.est_ip='".$ip."' and 
convert(varchar (10),ce.ctrc_fecha_inicio,103)=CONVERT(varchar (10),GETDATE(),103)and ce.usr_id=".$lc_Idusuario." and ce.std_id=14 )
		begin
select 1 as std_id
end
else
begin
select 2 as std_id
end*/";		
if($this->fn_ejecutarquery($lc_query))
	 {
		
		 while($lc_row = $this->fn_leerobjeto())
		  {
			$lc_numreg = $this->fn_numregistro();
			if ( $lc_numreg > 0)
			{ 
			 return $lc_row->std_id;
			  
			}
			else
			{  
				return 0;
			}//='".$_SESSION['rstId']."'
		 } 
     
 	}
	} 
}