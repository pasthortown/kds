<?php
////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Jose Fernandez////////////////////////////////
///////////DESCRIPCION: PANTALLA DE APERTURA DE PERIODO/////////////////
////////////////TABLAS: PERIODO/////////////////////////////////////////
////////FECHA CREACION: 20/12/2013//////////////////////////////////////
////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 09-07-2014////////////////////////////
///////USUARIO QUE MODIFICO:  Jorge Tinoco /////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Verificaci�n tipo de Servicio que /////
///////ofrece el Restaurante. //////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 08-04-2015////////////////////////////
///////USUARIO QUE MODIFICO:  Jimmy Cazaro /////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Validar funciones gerente seg�n ///////
///////perfil. /////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////

session_start();
include"../system/conexion/clase_sql.php";
include"../clases/clase_validaPeriodo.php";
include"../clases/clase_seguridades.php";
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
$lc_periodo  =  new periodo(); 
$permisos 		= new seguridades();
$failPage = "../index.php";
$fichas = "../ordenpedido/fichas.php";
$mesas = "../ordenpedido/userMesas.php";

$orden = "../ordenpedido/tomaPedido.php";

$lc_perfil=$_SESSION['perfil'];
$apertura = "../cierre/apertura.php";
$desmotarcaja = "../corteCaja/desmontado_cajero.php";
$funciones_gerente = "../funciones/funciones_gerente.php";
$lc_rest=$_SESSION['rstId'];
$lc_ip=$_SESSION['direccionIp'];//'192.168.100.182';
$lc_Idusuario=$_SESSION['usuarioId'];
$lc_OpcionSeleccion = $_SESSION['OpcionSeleccion'];//1 es Desmontar Cajero y 0 es Ingresar al Sistema

$lc_validaLogueoCaja=$lc_periodo->fn_validaLogueoEstacion($lc_ip);	
//--------------------------------------------
//VERIFICAMOS LA OPCION DE DESMONTAR EL CAJERO
if ($lc_OpcionSeleccion == 1)
{
/*	if($lc_perfil==1 or $lc_perfil==2 or $lc_perfil==3 or $lc_perfil==4)
	{*/
		if ($lc_validaLogueoCaja == 1)
		{
			header("Location: " . $desmotarcaja);
		}
		else
		{
			?>
			<script type="text/javascript">
				alertify.alert("La caja no se encuentra en uso");
				$("#alertify-ok").click(
							function()
							{
							window.location.href="../index.php";	
							}
							)
			</script> 
			<?php		
		}
/*	}
	else
	{
		?>
			<script type="text/javascript">
				alertify.alert("El usuario no tiene el perfil para desmontar la Estaci\xf3n");
				$("#alertify-ok").click(
							function()
							{
							window.location.href="../index.php";	
							}
							)
			</script> 
		<?php		
	}*/
}
else
{
	//--------------------------------------------
	if($lc_perfil==1 or $lc_perfil==2 or $lc_perfil==3 or $lc_perfil==4)
	//if($lc_perfil==1)
	{
//		if($lc_validaLogueoCaja==1)
//		{
			if($lc_validaIp=$lc_periodo->fn_validaIpestacion($lc_ip))
			{			
				if($lc_validaIp==1)
				{
					$_SESSION['estacionId']	= $lc_periodo->fn_getEstacionId($lc_ip);		
					$_SESSION['EstacionNombre'] = $lc_periodo->fn_getEstacionNombre($lc_ip);
					
					if($lc_valida=$lc_periodo->fn_validaPeriodo($lc_rest))
					{//Inicio de lc_valida funcion		
					
	/*				echo $_SESSION['DesmotarCaja']; 
					return false;*/
						if ($lc_valida != 9)
						{//inicio IF lc_valida 
/*							if($_SESSION['DesmotarCaja']==1)
							{			
								header("Location: " . $desmotarcaja);					
							}
							else
							{	*/
								if ($_SESSION['EstacionNombre']=='CAJA1' and ($lc_perfil==1 or $lc_perfil==2 or $lc_perfil==3 or $lc_perfil==4))
								{
									header("Location: " . $funciones_gerente);
								}else if($_SESSION['TipoServicio']==1)
								{								
									header("Location: " . $orden);	
								}else
								{		
									$botones = $permisos->fn_accesoPermisosPerfilBotones($lc_Idusuario,"Mesas");								
									if($botones != NULL)
									{
										header("Location: " . $mesas);	
									}
									else
									{
										?>
										<script type="text/javascript">
											alertify.alert("No tienes permisos para acceder a la pantalla de Mesas...");
											$("#alertify-ok").click(
														function()
														{
														window.location.href="../index.php";	
														}
														)
										</script> 
										<?php											
									}
								}	
/*							}*/
						}
						else 
						{		
							if ($_SESSION['EstacionNombre']=='CAJA1' and ($lc_perfil==1 or $lc_perfil==2 or $lc_perfil==3 or $lc_perfil==4))
							{						
								header("Location: " . $apertura);
							}
							else
							{
								?>
								<script type="text/javascript">
									alertify.alert("El periodo no se encuentra abierto, solo lo puede aperturar los perfiles autorizados en la CAJA 1");
									$("#alertify-ok").click(
												function()
												{
												window.location.href="../index.php";	
												}
												)
								</script> 
								<?php								
							}
							//header("Location: " . $failPage);
						}
					}
				}
				else
				{
				?>
				<script type="text/javascript">
					alertify.alert("Estaci\xf3n no configurada");
					$("#alertify-ok").click(
								function()
								{
								window.location.href="../index.php";	
								}
								)
				</script> 
				<?php
				}
/*			}*/					
		}
//		if($lc_validaLogueoCaja==2)
//		{
//	/*		if($lc_valida=$lc_periodo->fn_validaPeriodo($lc_rest))		
//			{*/
//				if($lc_validaIp=$lc_periodo->fn_validaIpestacion($lc_ip))
//				{				
//					if($lc_validaIp==1)
//					{
//						$_SESSION['estacionId']	= $lc_periodo->fn_getEstacionId($lc_ip);
//						$_SESSION['EstacionNombre'] = $lc_periodo->fn_getEstacionNombre($lc_ip);
//						if($lc_valida=$lc_periodo->fn_validaPeriodo($lc_rest))
//						{					
//							if ($lc_valida != 9)
//							{//inicio IF lc_valida 
//								if($_SESSION['DesmotarCaja']==1)
//								{			
//									header("Location: " . $desmotarcaja);					
//								}
//								else
//								{		
//									if ($_SESSION['EstacionNombre']=='CAJA1' and $_SESSION['perfil']==1)
//									{
//										header("Location: " . $funciones_gerente);
//									}							
//									if($_SESSION['TipoServicio']==1)
//									{								
//											//$lc_periodo->fn_grabacontrolEstacion($lc_rest,$lc_ip,$lc_Idusuario);
//											header("Location: " . $orden);	
//									}else
//									{		
//											//$lc_periodo->fn_grabacontrolEstacion($lc_rest,$lc_ip,$lc_Idusuario);
//											header("Location: " . $mesas);	
//									}	
//								}
//	/*						}*/
//							}
//							else 
//							{									
//								header("Location: " . $apertura);
//							}
//						}
//					}
//					else
//					{ 
						?>
<!--						<script type="text/javascript">
							alertify.alert("Estaci\xf3n no configurada");
							$("#alertify-ok").click(
										function()
										{
										window.location.href="../index.php";	
										}
										)
						</script>--> 
						<?php
//					}
//				}
///*			}*/
//		}
	}
	else
	{
		if($lc_validaLogueo=$lc_periodo->fn_validaLogueo($lc_Idusuario,$lc_ip))
		{
			if($lc_validaLogueo==2)
			{
				if($lc_validaIp=$lc_periodo->fn_validaIpestacion($lc_ip))
				{				
					if($lc_validaIp==1)
					{
						$_SESSION['estacionId']	= $lc_periodo->fn_getEstacionId($lc_ip);	
						$_SESSION['EstacionNombre'] = $lc_periodo->fn_getEstacionNombre($lc_ip);

						if ($_SESSION['EstacionNombre']!='CAJA1')
						{
	/*					if($lc_validaPeriodoCerrado=$lc_periodo->fn_validaPeriodoCerrado($lc_rest))
						{
							if($lc_validaPeriodoCerrado==2)
							{*/										
								if($lc_validaSesion=$lc_periodo->fn_validaSesion($lc_ip,$lc_Idusuario))
								{// Inicio lc_validaSesion funcion
									if($lc_validaSesion=='ingresa_mismo_usuario' or $lc_validaSesion=='ingresa_otro_usuario' 
									or $lc_validaSesion=='ingresa_primera_vez' or $lc_perfil==1)
									{//inicio de IF lc_validaSesion
										if($lc_valida=$lc_periodo->fn_validaPeriodo($lc_rest))
										{//Inicio de lc_valida funcion				 				 				  
											if ($lc_valida != 9)
											{//inicio IF lc_valida 	
/*												if($_SESSION['DesmotarCaja']==1)
												{			
													header("Location: " . $desmotarcaja);					
												}
												else
												{*/									
													if($_SESSION['TipoServicio']==1)
													{
/*														$lc_periodo->fn_grabacontrolEstacion($lc_rest,$lc_ip,$lc_Idusuario);*/	
														//header("Location: " . $fichas);	
														header("Location: " . $orden);	
													}else
													{
/*														$lc_periodo->fn_grabacontrolEstacion($lc_rest,$lc_ip,$lc_Idusuario);*/	
														$botones = $permisos->fn_accesoPermisosPerfilBotones($lc_Idusuario,"Mesas");
														if($botones != NULL)
														{															
															header("Location: " . $mesas);	
														}
														else
														{
															?>
															<script type="text/javascript">
																alertify.alert("No tienes permisos para acceder a la pantalla de Mesas...");
																$("#alertify-ok").click(
																			function()
																			{
																			window.location.href="../index.php";	
																			}
																			)
															</script> 
															<?php
														}
													}	
/*												}*/
											}
											else 
											{									
												//header("Location: " . $apertura);
												?>
													<script type="text/javascript">
														alertify.alert("No se encuentra aperturado el periodo comunicar al Administrador para que lo aperture");
														$("#alertify-ok").click(
																	function()
																	{
																	window.location.href="../index.php";	
																	}
																	)
													</script> 
												<?php											
											}	//fin de ELSE lc_valida
										}//fin lc_valida funcion
									}
									else
									{
										?>
											<script type="text/javascript">
												alertify.alert("Estaci\xf3n en uso");
												$("#alertify-ok").click(
															function()
															{
															window.location.href="../index.php";	
															}
															)
												//document.location.href="<?php echo $failPage?>";
											</script> 
										<?php
									}//fin ELSE lc_validaSesion
								}//fin lc_validaSesion funcion
	/*						}
							else
							{*/
								?>
	<!--								<script type="text/javascript">
										alertify.alert("El per\xEDodo ya se encuentra cerrado");
										$("#alertify-ok").click(
													function()
													{
													window.location.href="../index.php";	
													}
													)
									</script> -->
								<?php
							}
							else
							{
								?>
									<script type="text/javascript">
										alertify.alert("Estaci\xf3n configurada solo para Administrador");
										$("#alertify-ok").click(
													function()
													{
													window.location.href="../index.php";	
													}
													)
									</script> 
								<?php								
							}
		/*				}*/
					}
					else
					{
						?>
							<script type="text/javascript">
								alertify.alert("Estaci\xf3n no configurada");
								$("#alertify-ok").click(
											function()
											{
											window.location.href="../index.php";	
											}
											)
							</script> 
						<?php
					}
				}
			}
			else			
			{
				?>
					<script type="text/javascript">
						alertify.alert("Usuario ya se encuentra logueado en otra Estaci\xf3n");
						$("#alertify-ok").click(
									function()
									{
									window.location.href="../index.php";	
									}
									)
					</script> 
				<?php
			}
		}
	}
}
?>
</body>
</html>

	
	 
	 