<?php 
//session_start();
/////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Christian Pinto////////////////////////
////////DESCRIPCION		: Impresion Dinamica de Corte de Caja/////////
////////TABLAS			: ARQUEO_CAJA,BILLETE_ESTACION,//////////////
//////////////////////////CONTROL_ESTACION,ESTACION//////////////
//////////////////////////BILLETE_DENOMINACION///////////////////
////////FECHA CREACION	: 17/09/2015/////////////////////////////
/////////////////////////////////////////////////////////////////
include_once("../system/conexion/clase_sql.php");	
include_once ("../clases/clase_corteCaja.php");

$lc_corteCaja = new corteCaja();
$lc_periodo = $_GET['periodo'];
$lc_estacion = $_GET['estacion'];
$lc_usuarioId_Admin = $_GET['usr_id_admin'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php
	
	$lc_datos[0] = $lc_periodo;
	$lc_datos[1] = $lc_estacion;
	$lc_datos[2] = $lc_usuarioId_Admin;
	if($lc_corteCaja->fn_consultar('impresion_FindelDia', $lc_datos)) {
            if($lc_row = $lc_corteCaja->fn_leerObjeto()) {	
                echo utf8_encode($lc_row->html);
                echo utf8_encode($lc_row->htmla);
                echo utf8_encode($lc_row->htmlb);
                echo utf8_encode($lc_row->htmlc);
            }
	}

?>

</html> 
 
