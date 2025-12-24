<?php 
session_start();
///////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Daniel Llerena////////////////////////////////////
///////DESCRIPCION	   : Archivo de configuracion del Modulo Retiro Fondo /
////////FECHA CREACION : 12/11/2015 ///////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR  :  /////////////////////////////////////////////////
///////DESCRIPCION	   :  /////////////////////////////////////////////////
///////TABLAS		   : //////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

include_once"../system/conexion/clase_sql.php";
include_once "../clases/clase_retiroFondo.php";

$lc_retiro = new retiroFondo();
$lc_control = $_GET['ctrc_id'];
//$lc_usuarioId = $_GET['usr_id'];
//$lc_est_id = $_SESSION['estacionId'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php

	$lc_datos[0]=$lc_control;
	//$lc_datos[1]=$lc_usuarioId;		
	if($lc_retiro->fn_consultar('impresion_retiroFondo', $lc_datos))
	{
		if($lc_row = $lc_retiro->fn_leerObjeto())
		{		
			echo $lc_row->html;		
			echo $lc_row->htmlf;
		}
	}

?>

</html>

