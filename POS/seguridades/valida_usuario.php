<?php

/////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade/////////////////
////////DESCRIPCION: Validad ingreso de Usuario//////////
///////TABLAS INVOLUCRADAS: User_Pos,   /////////////////
///////FECHA CREACION: 29-06-2009////////////////////////
///////FECHA ULTIMA MODIFICACION:17/01/2014//////////////
///////USUARIO QUE MODIFICO: Darwin Mora/////////////////
///////DECRIPCION ULTIMO CAMBIO: Inclusi�n de funcion /// 
///////FECHA ULTIMA MODIFICACION:05/02/2014//////////////
///////USUARIO QUE MODIFICO: Darwin Mora/////////////////
///////DECRIPCION ULTIMO CAMBIO: Desmontar Cajero /////// 
///////FECHA ULTIMA MODIFICACION:18/03/2014//////////////
///////USUARIO QUE MODIFICO: Jose fernandez//////////////
///////DECRIPCION ULTIMO CAMBIO: Cambiar el estilo de ///
////////////////////////////las alertas y funcionalidad//
////////////////////////////de las mismas////////////////
/////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 02/04/2015 ////////////
///////USUARIO QUE MODIFICO: Jimmy Cazaro ///////////////
///////DECRIPCION ULTIMO CAMBIO: Anexar en las //////////
////////// variables de sesion la estacion nombres //////
////////// y direccionar a funciones gerente bajo ///////
////////// ciertas validaciones /////////////////////////
/////////////////////////////////////////////////////////

require_once'../system/conexion/clase_sql.php';
include_once'../clases/clase_seguridades.php';
include_once'../clases/clase_direccion.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<script language="javascript" type="text/javascript" src="../js/jquery.js"></script>
<script language="javascript1.1"  src="../js/alertify.js"></script>  
<link rel="stylesheet" type="text/css" href="../css/alertify.core.css"/>
<link rel="stylesheet" type="text/css" href="../css/alertify.default.css"/>

</head>
<body>
<?php
$clave = $_POST['txtClave'];
$tipo = $_POST['txtTipo'];
$estacion = $_POST['txtEstacion'];
$ip_dir = $_POST['txtIp'];

$failPage = "../index.php";
$correcto = "valida_periodo.php";
//$funciones = "../funciones/funciones_gerente.php";

$ip 		= new direccion();
$usuario 	= new seguridades();

//Desmontar Cajero

//$lc_perfil = $usuario->fn_getUsr($clave,'Perfil_Id', $estacion);
$lc_perfil = $usuario->fn_getUsr($clave,'Perfil_Id');
if($tipo==1){
   
	if($lc_perfil==1 or $lc_perfil==2 or $lc_perfil==3 or $lc_perfil==4)
	{
	
//		//Variables de sesion 
//	if($usuario->fn_getUsr($clave, 'Usuario_Id', $estacion)){
//			/*if(*/$usuario->fn_controlEstacion($usuario->fn_getUsr($clave, 'Usuario_Id',$estacion));/*=='Inactivo'){*/
//				session_start();
//				$_SESSION['validado']			= TRUE;
//				$_SESSION['usuarioId'] 			= $usuario->fn_getUsr($clave,'Usuario_Id',$estacion);
//				$_SESSION['usuario'] 			= $usuario->fn_getUsr($clave,'Usuario',$estacion);
//				$_SESSION['perfil']		 		= $lc_perfil;
//				$_SESSION['rstId'] 				= $usuario->fn_getUsr($clave,'Resturante_Id',$estacion);
//				$_SESSION['nombre'] 			= $usuario->fn_getUsr($clave,'Usuario_Nombre',$estacion);
//				$_SESSION['rstCodigoTienda'] 	= $usuario->fn_getUsr($clave,'Resturante_CodTienda',$estacion);
//				$_SESSION['rstNombre'] 			= $usuario->fn_getUsr($clave,'Resturante_Nombre',$estacion);
//				$_SESSION['TipoServicio'] 		= $usuario->fn_getUsr($clave,'TipoServicio',$estacion);
//				$_SESSION['direccionIp'] 		= $ip->fn_getIp();
//				$_SESSION['numPiso'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Resturante_NumPiso');
//				$_SESSION['numMesa'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Resturante_NumMesa');
//				$_SESSION['cadenaId'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Cadena_Id');
//				$_SESSION['cadenaNombre'] 		= $usuario->fn_getCdn($_SESSION['rstId'],'Cadena_Nombre');
//				$_SESSION['logo'] 				= $usuario->fn_getCdn($_SESSION['rstId'],'Logotipo');
//				$_SESSION['DesmotarCaja']		= $tipo;//Variable de sesion desmontar cajero
//				$_SESSION['EstacionNombre'] 	= $usuario->fn_getUsr($clave,'EstacionNombre',$estacion);
//				$_SESSION['estacionId']	    	= $usuario->fn_getUsr($clave,'estacionId',$estacion);
	
		//Variables de sesion 
	if($usuario->fn_getUsr($clave, 'Usuario_Id')){
			/*if(*/$usuario->fn_controlEstacion($usuario->fn_getUsr($clave, 'Usuario_Id'));/*=='Inactivo'){*/
				session_start();
				$_SESSION['validado']			= TRUE;
				$_SESSION['usuarioId'] 			= $usuario->fn_getUsr($clave,'Usuario_Id');
				$_SESSION['usuario'] 			= $usuario->fn_getUsr($clave,'Usuario');
				$_SESSION['perfil']		 		= $lc_perfil;
				$_SESSION['rstId'] 				= $usuario->fn_getUsr($clave,'Resturante_Id');
				$_SESSION['nombre'] 			= $usuario->fn_getUsr($clave,'Usuario_Nombre');
				$_SESSION['rstCodigoTienda'] 	= $usuario->fn_getUsr($clave,'Resturante_CodTienda');
				$_SESSION['rstNombre'] 			= $usuario->fn_getUsr($clave,'Resturante_Nombre');
				$_SESSION['TipoServicio'] 		= $usuario->fn_getUsr($clave,'TipoServicio');
				$_SESSION['direccionIp'] 		= $ip->fn_getIp();
				$_SESSION['numPiso'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Resturante_NumPiso');
				$_SESSION['numMesa'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Resturante_NumMesa');
				$_SESSION['cadenaId'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Cadena_Id');
				$_SESSION['cadenaNombre'] 		= $usuario->fn_getCdn($_SESSION['rstId'],'Cadena_Nombre');
				$_SESSION['logo'] 				= $usuario->fn_getCdn($_SESSION['rstId'],'Logotipo');
				$_SESSION['DesmotarCaja']		= $tipo;//Variable de sesion desmontar cajero
				$_SESSION['EstacionNombre'] 	= '';//$usuario->fn_getUsr($clave,'EstacionNombre');
				$_SESSION['estacionId']	    	= '';//$usuario->fn_getUsr($clave,'estacionId');	
	
				//Validad session de usuario
				
//				$usuario->fn_insertarSesion($_SESSION['rstId'], 14, $_SESSION['usuarioId']);
				//Selecciona informaci�n de estacion
				$_SESSION['EstacionNombre'] 	= $usuario->fn_SeleccionaEstacion($ip_dir, 'Estacion_Nombre');
				$_SESSION['estacionId'] 		= $usuario->fn_SeleccionaEstacion($ip_dir, 'Estacion_Codigo');
				$_SESSION['OpcionSeleccion'] 	= $tipo;//1 es Desmontar Cajero y 0 es Ingresar al Sistema
				
				header("Location: " . $correcto);
				
			//}
			/*else{
				?>
				<script type="text/javascript">
					alert("Existe una sesion abierta con este usuario");
					document.location.href="<?php echo $failPage?>";
				</script> <?php
				}*/

	}else{?>
		<script type="text/javascript">
			alertify.alert("Sus credenciales son incorrectas, vuelva a intentarlo");
			$("#alertify-ok").click(
						function()
						{
						window.location.href="../index.php";	
						}
						)
			//document.location.href="<?php //echo $failPage?>";
		</script> <?php
	}
		
	}else{

		?>
		<script type="text/javascript">
			alertify.alert("Usuario no autorizado para desmontar Cajero");
			$("#alertify-ok").click(
						function()
						{
						window.location.href="../index.php";	
						}
						)
			//document.location.href="<?php //echo $failPage?>";
		</script> <?php		
	}
}else {
	
//	//Variables de sesion 
//	if($usuario->fn_getUsr($clave, 'Usuario_Id', $estacion)){
//			/*if(*/$usuario->fn_controlEstacion($usuario->fn_getUsr($clave, 'Usuario_Id', $estacion));/*=='Inactivo'){*/
//				session_start();
//				$_SESSION['validado']			= TRUE;
//				$_SESSION['usuarioId'] 			= $usuario->fn_getUsr($clave,'Usuario_Id', $estacion);
//				$_SESSION['usuario'] 			= $usuario->fn_getUsr($clave,'Usuario', $estacion);
//				$_SESSION['perfil']		 		= $lc_perfil;
//				$_SESSION['rstId'] 				= $usuario->fn_getUsr($clave,'Resturante_Id', $estacion);
//				$_SESSION['nombre'] 			= $usuario->fn_getUsr($clave,'Usuario_Nombre', $estacion);
//				$_SESSION['rstCodigoTienda'] 	= $usuario->fn_getUsr($clave,'Resturante_CodTienda', $estacion);
//				$_SESSION['rstNombre'] 			= $usuario->fn_getUsr($clave,'Resturante_Nombre', $estacion);
//				$_SESSION['TipoServicio'] 		= $usuario->fn_getUsr($clave,'TipoServicio', $estacion);
//				$_SESSION['direccionIp'] 		= $ip->fn_getIp();
//				$_SESSION['numPiso'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Resturante_NumPiso');
//				$_SESSION['numMesa'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Resturante_NumMesa');
//				$_SESSION['cadenaId'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Cadena_Id');
//				$_SESSION['cadenaNombre'] 		= $usuario->fn_getCdn($_SESSION['rstId'],'Cadena_Nombre');
//				$_SESSION['logo'] 				= $usuario->fn_getCdn($_SESSION['rstId'],'Logotipo');
//				$_SESSION['DesmotarCaja']		= $tipo;//Variable de sesion desmontar cajero
//				$_SESSION['EstacionNombre'] 	= $usuario->fn_getUsr($clave,'EstacionNombre', $estacion);
//				$_SESSION['estacionId']	    	= $usuario->fn_getUsr($clave,'estacionId',$estacion);
	

	if($usuario->fn_getUsr($clave, 'Usuario_Id')){
			/*if(*/$usuario->fn_controlEstacion($usuario->fn_getUsr($clave, 'Usuario_Id'));/*=='Inactivo'){*/
				session_start();
				$_SESSION['validado']			= TRUE;
				$_SESSION['usuarioId'] 			= $usuario->fn_getUsr($clave,'Usuario_Id');
				$_SESSION['usuario'] 			= $usuario->fn_getUsr($clave,'Usuario');
				$_SESSION['perfil']		 		= $lc_perfil;
				$_SESSION['rstId'] 				= $usuario->fn_getUsr($clave,'Resturante_Id');
				$_SESSION['nombre'] 			= $usuario->fn_getUsr($clave,'Usuario_Nombre');
				$_SESSION['rstCodigoTienda'] 	= $usuario->fn_getUsr($clave,'Resturante_CodTienda');
				$_SESSION['rstNombre'] 			= $usuario->fn_getUsr($clave,'Resturante_Nombre');
				$_SESSION['TipoServicio'] 		= $usuario->fn_getUsr($clave,'TipoServicio');
				$_SESSION['direccionIp'] 		= $ip->fn_getIp();
				$_SESSION['numPiso'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Resturante_NumPiso');
				$_SESSION['numMesa'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Resturante_NumMesa');
				$_SESSION['cadenaId'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Cadena_Id');
				$_SESSION['cadenaNombre'] 		= $usuario->fn_getCdn($_SESSION['rstId'],'Cadena_Nombre');
				$_SESSION['logo'] 				= $usuario->fn_getCdn($_SESSION['rstId'],'Logotipo');
				$_SESSION['DesmotarCaja']		= $tipo;//Variable de sesion desmontar cajero
				$_SESSION['EstacionNombre'] 	= '';//$usuario->fn_getUsr($clave,'EstacionNombre');
				$_SESSION['estacionId']	    	= '';//$usuario->fn_getUsr($clave,'estacionId');
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//Validad session de usuario
//				$usuario->fn_insertarSesion($_SESSION['rstId'], 14, $_SESSION['usuarioId']);
				//Selecciona informaci�n de estacion
				$_SESSION['EstacionNombre'] 	= $usuario->fn_SeleccionaEstacion($ip_dir, 'Estacion_Nombre');
				$_SESSION['estacionId'] 		= $usuario->fn_SeleccionaEstacion($ip_dir, 'Estacion_Codigo');
				$_SESSION['OpcionSeleccion'] 	= $tipo;//1 es Desmontar Cajero y 0 es Ingresar al Sistema
				//$_SESSION['EstacionNombre'] = $usuario->fn_SeleccionaEstacion($ip_dir);
				
				/*if($_SESSION['EstacionNombre']=='CAJA1' and $lc_perfil==1)
				{	
					//validar periodo (valida periodo)
					header("Location: " . $funciones);
				}
				else*/  // if($_SESSION['EstacionNombre']=='CAJA1' and $lc_perfil!=1)
				//{
					?>
<!--					<script type="text/javascript">
						alertify.alert("No tienes permisos para acceder a esta estaci&oacute;n");
						$("#alertify-ok").click(
									function()
									{
									window.location.href="../index.php";	
									}
									)
						//document.location.href="<?php //echo $failPage?>";
					</script> -->
					<?php					
/*				}else
				{*/
					//echo $_SESSION['EstacionNombre']. ' - ' .$_SESSION['estacionId'];
					header("Location: " . $correcto);
/*				}*/
				
			//}
			/*else{
				?>
				<script type="text/javascript">
					alert("Existe una sesion abierta con este usuario");
					document.location.href="<?php echo $failPage?>";
				</script> <?php
				}*/
	}else{?>
		<script type="text/javascript">
			alertify.alert("Sus credenciales son incorrectas, vuelva a intentarlo");
			$("#alertify-ok").click(
						function()
						{
						window.location.href="../index.php";	
						}
						)
			//document.location.href="<?php //echo $failPage?>";
		</script> <?php
	}
	
}



?>
</body>
</html>
