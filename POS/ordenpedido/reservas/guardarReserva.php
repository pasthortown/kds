<?php
//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Carga de Combos//////////////////////////
/////////////////////En las Mesas/////////////////////////////
///////TABLAS INVOLUCRADAS: Mesas  /////////////////////////// 
///////FECHA CREACION: 28-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 

	require_once('../../system/conexion/clase_sql.php');
	include_once('../../clases/clase_seguridades.php');
	include_once('../../clases/clase_reservas.php');
	
	include_once('../../seguridades/Adm_seguridad_niv2.inc');	
	
	$piso			= $_POST["piso"];
	$area			= $_POST["area"];
	$fecha			= $_POST["txtfechaI"];
	$horaInicia		= $_POST["txtHoraI"];
	$horaFin 		= $_POST["txtHoraF"];
	$mesa 			= $_POST["txtMesaId"];
	$clienteNombre	= $_POST["txtClienteB"];
	$clienteFono 	= $_POST["txtClienteFono"];
	$descripcion 	= $_POST["txtMotivo"];

	$reserva= new reservas();
		
	$reserva->fn_guardarReservas($descripcion, $fecha, $horaInicia, $horaFin, $clienteNombre, $clienteFono, $mesa);
    	
?>		