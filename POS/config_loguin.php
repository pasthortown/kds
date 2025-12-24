<?php
////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////
///////////DESCRIPCION: INICIO DE SESION INGRESO AL SISTEMA CON VALIDACIONES ///
////////////////TABLAS: Control_Estacion, Estacion, Peridodo, User_Pos /////////
////////FECHA CREACION: 12/08/2015//////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

include'system/conexion/clase_sql.php';
include'clases/clase_loguin.php';

$lc_loguin = new loguin(); 

 /*$lc_perfil = $_SESSION['perfil'];
$lc_usuarioId = $_SESSION['usuarioId'];
$ip = $_SESSION['direccionIp'];*/

if(htmlspecialchars(isset($_GET['validaIpConfigurada'])))
{   
	$lc_condiciones[0]=1;
	$lc_condiciones[1]= htmlspecialchars($_GET['ip']);
	print $lc_loguin ->fn_consultar('validaIpConfigurada',$lc_condiciones);	
}

if(htmlspecialchars(isset($_GET['validaPeriodoAbierto'])))
{ 
	$lc_condiciones[0]=2;
	$lc_condiciones[1]= htmlspecialchars($_GET['ip']);
	print $lc_loguin ->fn_consultar('validaPeriodoAbierto',$lc_condiciones);	
}

if(htmlspecialchars(isset($_GET['traerDatosCadena'])))
{ 
	$lc_condiciones[0]= htmlspecialchars($_GET['accion']);
	$lc_condiciones[1]= htmlspecialchars($_GET['est_ip']);
	
	print $lc_loguin ->fn_consultar('traerDatosCadena',$lc_condiciones);	
}

if(htmlspecialchars(isset($_GET['valida_usuario_logueado'])))
{ 
	//$lc_condiciones[0]= htmlspecialchars($_GET['accion']);
	$lc_condiciones[0]= htmlspecialchars($_GET['ip']);
	
	print $lc_loguin ->fn_consultar('valida_usuario_logueado',$lc_condiciones);	
}

if(htmlspecialchars(isset($_GET['validaEstacionActiva'])))
{   
	$lc_condiciones[0]=4;
	$lc_condiciones[1]= htmlspecialchars($_GET['ip']);
	print $lc_loguin ->fn_consultar('validaEstacionActiva',$lc_condiciones);	
}

if(htmlspecialchars(isset($_GET['validaControlEstacion'])))
{ 
	$lc_condiciones[0]= htmlspecialchars($_GET['accion']);
	$lc_condiciones[1]= htmlspecialchars($_GET['ip']);
	print $lc_loguin ->fn_consultar('validaControlEstacion',$lc_condiciones);	
}

if(htmlspecialchars(isset($_GET['validarUsuarioAdministrador'])))
{ 
	$lc_condiciones[0]= htmlspecialchars($_GET['accion']);
	$lc_condiciones[1]= htmlspecialchars($_GET['usr_claveAdmin']);	
	$lc_condiciones[2]= htmlspecialchars($_GET['usr_claveCajero']);	
	$lc_condiciones[3]= htmlspecialchars($_GET['est_ip']);	
	$lc_condiciones[4]= htmlspecialchars($_GET['tarjeta']);
	print $lc_loguin->fn_consultar('validarUsuarioAdministrador',$lc_condiciones);
}

if(htmlspecialchars(isset($_GET['IngresoAdministrador'])))
{
	$lc_condiciones[0]='A';
	$lc_condiciones[1]= htmlspecialchars($_GET['est_ip']);	
	$lc_condiciones[2]= htmlspecialchars($_GET['usr_claveAdmin']);	
	$lc_condiciones[3]= htmlspecialchars($_GET['tarjeta']);
	print $lc_loguin->fn_consultar('IngresoAdministrador',$lc_condiciones);	
}

if(htmlspecialchars(isset($_GET["InsertControlEstacionIngresoAdmin"])))
{		
	$lc_condiciones[0]='I';
	$lc_condiciones[1]= htmlspecialchars($_GET['est_ip']);	
	$lc_condiciones[2]= htmlspecialchars($_GET['usr_claveAdmin']);	
	$lc_condiciones[3]= htmlspecialchars($_GET['tarjeta']);
	print $lc_loguin->fn_consultar('InsertControlEstacionIngresoAdmin',$lc_condiciones);
}

if(htmlspecialchars(isset($_GET['periodo_secuencial']))) {
	$accion = 1;
	$ip_estacion = htmlspecialchars($_GET['ip_estacion']);

	print $lc_loguin->administracionPeriodo($accion, $ip_estacion);
}

if(htmlspecialchars(isset($_GET['existe_periodo']))) {
	$accion = 3;
	$ip_estacion = htmlspecialchars($_GET['ip_estacion']);
	print $lc_loguin->existePeriodoSecuencial($accion, $ip_estacion);
}

if (isset($_POST['actualizaTodasOdp'])) {
    $lc_datos[0]=$_POST['opcion'];
    $lc_datos[1]=$_POST['id_odp'];
    $lc_datos[2]=$_POST['estacion'];
    $lc_datos[3]=$_POST['usuario'];
	$lc_datos[4]=$_POST['periodo'];
    $lc_datos[5]='0';
	$lc_datos[6]='0';
	print $lc_loguin->fn_consultar('actualizaTodasOdp', $lc_datos);
}

if (isset($_GET['CierraCaja'])) {
	$ip_estacion=$_GET['ip_estacion'];
    echo $lc_loguin->cerrar_caja($ip_estacion);
}

?>