<?php 
  /////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Christian Pinto//////////////////////////////////////
////////DESCRIPCION: Página que permite comprobar que se //////////////////////
///////////////////  que el usuario que ingreso en el  ////////////////////////
///////////////////  sistema exista y este activo /////////////////////////////
///////TABLAS INVOLUCRADAS: Perfil_Pos,Users_Pos,Acceso_Pos,Pantalla_Pos //////
/////////////////////////   Permisos_Perfil_Pos ///////////////////////////////
///////////////////////////////////////////////////////////////////////////////
//-- =================================================================
//--FECHA ULTIMA MODIFICACION	: 11:53 10/1/2017
//--USUARIO QUE MODIFICO	: Mychael Castro
//--DECRIPCION ULTIMO CAMBIO	:  Aumento devariable de session que almacene el campo que contendra el nombre de la cadena$_SESSION['cadenaNombre']
//-- =================================================================

include "../system/mensaje.php";

require_once '../system/conexion/clase_sql.php';
include_once '../clases/clase_seguridades.php';
include_once '../clases/clase_direccion.php';

$obj_cadena =  new seguridades();
// $usuario = new seguridades();
// $obj_periodo = new seguridades();

// Verificar Usuario
// $lc_usuario = $_SESSION['usuario'];
// $lc_condiciones[0] = $lc_usuario;
//$obj_periodo->fn_verificausuario('verifica',$lc_condiciones);


////////////////////////DIRECCIONAMINETO DE LAS PANTALLAS EN CASO DE SER CORRECTO O FALLIDO EL ACCESO/////////////////////////////////////
$correcto = "../mantenimiento/inicio/home.php";
////////////////////////////////////////////////////////////////////////////////////////////////

/*
//Recuperar Ruta del SG segun el Periodo
$lc_condiciones[0] = trim($_POST['selperiodo']);
if ($obj_periodo->fn_armarquery('ruta_x_periodo', $lc_condiciones)) {
	if ($lc_rows = $obj_periodo->fn_leerObjeto()) {
		$lc_rutaSG = $lc_rows->Ruta;
		$_SESSION['sess_periodo'] = $lc_rows->Descripcion;
	}
}*/

//Recuperar Cadena 
$_SESSION['access'] = true;
$lc_cadena = trim($_POST['selrestaurante']);
$_SESSION['cadenaId'] = $lc_cadena;
 
$lc_condiciones_c[0]=$lc_cadena;
if ($obj_cadena->fn_armarquery('cadena_x_restaurante', $lc_condiciones_c)){
	if ($lc_rows = $obj_cadena->fn_leerObjeto()){
		$_SESSION['cadenaId']=$lc_rows->cdn_id;
		$_SESSION['CadenaLogo']=$lc_rows->cdn_logotipo;
		$_SESSION['cadenaNombre']=$lc_rows->cdn_descripcion;
	}
}
 
 	
if(!empty($_SESSION['rstId']) or !empty($lc_clave)) {	/*
	if($usuario->fn_getUsrAdmin($txtUsuario, $txtClave, 'Usuario_Id')) {
		session_start(); 
		$_SESSION['validado']			= TRUE;
		$_SESSION['usuarioId'] 			= $usuario->fn_getUsrAdmin($txtUsuario, $txtClave, 'Usuario_Id');
		$_SESSION['usuario'] 			= $usuario->fn_getUsrAdmin($txtUsuario, $txtClave, 'Usuario');
		$_SESSION['nombre'] 			= $usuario->fn_getUsrAdmin($txtUsuario, $txtClave, 'Usuario_Nombre');
		$_SESSION['perfil']		 		= $usuario->fn_getUsrAdmin($txtUsuario, $txtClave, 'Perfil_Id');
		$_SESSION['rstId']              = $usuario->fn_getUsrAdmin($txtUsuario, $txtClave, 'Resturante_Id');
		
		$_SESSION['direccionIp'] 		= $ip->fn_getIp();	
		$_SESSION['numPiso'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Resturante_NumPiso');
		$_SESSION['numMesa'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Resturante_NumMesa');
		$_SESSION['cadenaId'] 			= $usuario->fn_getCdn($_SESSION['rstId'],'Cadena_Id');
		$_SESSION['cadenaNombre'] 		= $usuario->fn_getCdn($_SESSION['rstId'],'Cadena_Nombre');
		$_SESSION['logo'] 				= $usuario->fn_getCdn($_SESSION['rstId'],'Logotipo');
		*/		
		
		header("Location: " . $correcto);
	//}
	//header("Location: $lc_rutaSG");
} else {
	mensaje(utf8_decode("No tiene asignado un RESTAURANTE, comuníquese con el ADMINISTRADOR"), 1);
}
