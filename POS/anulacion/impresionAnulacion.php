<?php 
session_start();
/////////////////////////////////////////////////////////////////
////////FECHA ULTIMA MODIFICACION:14/08/2014/////////////////////
////////USUARIO QUE MODIFICO: Jose Fernandez/////////////////////
////////DECRIPCION ULTIMO CAMBIO: Impresion de anulacion/////////
////////USUARIO QUE MODIFICO: Jorge Tinoco //////////////////////
////////DECRIPCION ULTIMO CAMBIO: Impresion de anulacion ////////
//////// Nota de credito ////////////////////////////////////////
/////////////////////////////////////////////////////////////////
////////FECHA ULTIMA MODIFICACION: 02/10/2015 ///////////////////
////////USUARIO QUE MODIFICO: Daniel Llerena ////////////////////
////////DECRIPCION ULTIMO CAMBIO: Impresion de anulacion ////////
//////// Nota de credito por SP /////////////////////////////////
/////////////////////////////////////////////////////////////////

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_anularOrden.php";
$lc_cfacId = $_GET['cfac_id'];
$lc_impresion = new menuPedido();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php 
		$lc_datos[0]=$lc_cfacId;	
		if($lc_impresion->fn_consultar('impresion_notacredito',$lc_datos))
		{
			if($lc_row = $lc_impresion->fn_leerObjeto())
			{		
				echo $lc_row->html;	
				echo $lc_row->html3;
				echo $lc_row->html2;
				echo $lc_row->htmlf;
			}
		}
?>	     

</html>

