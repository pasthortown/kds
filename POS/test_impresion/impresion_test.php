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
include_once"../system/conexion/clase_sql.php";
include_once'../clases/clase_infoestacion.php';
include_once"../clases/clase_impresionCashless.php";
$orden = new impresion_cashless();
$lc_infoestacion = new infoestacion();
@$lc_IDEstacion = $_GET['IDEstacion'];
$opcion = htmlspecialchars($_GET["opcion"]);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php
	
	if($opcion == '1') {
		$lc_datos[0] = $lc_IDEstacion; 
		$lc_datos[1] = $opcion;       
		if($lc_infoestacion->fn_consultar('impresion_test', $lc_datos))
		{
			if($lc_row = $lc_infoestacion->fn_leerObjeto())
			{	
						echo utf8_encode($lc_row->html);			
						echo utf8_encode($lc_row->htmlf);
			}
		}
	}
	else if($opcion == '0') {
		if (htmlspecialchars(isset($_GET["odp_id"]))) {      
				$cfac_id= htmlspecialchars($_GET["cfac_id"]);
				$odp_id = htmlspecialchars($_GET["odp_id"]);
										
				//$tipoServicio = htmlspecialchars($_GET["tipoServicio"]);
				//$canalImpresion = htmlspecialchars($_GET["canalImpresion"]);
				//$guardaOrden = htmlspecialchars($_GET["guardaOrden"]);
				//$numeroCuenta = htmlspecialchars($_GET["numeroCuenta"]);
				$lc_datos[0] = $cfac_id;
				$lc_datos[1] = $odp_id;
				$lc_datos[2] = $opcion;
										
				//$lc_datos[2] = $canalImpresion;
				//$lc_datos[3] = $guardaOrden;
				//$lc_datos[4] = $numeroCuenta;
				if ($orden->fn_impresionDinamicaOrdenPedidoCashless($lc_datos)) {
						while ($lc_row = $orden->fn_leerObjeto()) {
								echo utf8_encode($lc_row->html);
						}
				}
		}
}
	//Impresion de Codigo de Confirmacion Pickup Agregadores QR
	else if($opcion == 3){
		$lc_datos[0] = htmlspecialchars($_GET["rsaut_id"]);
		$lc_datos[1] = htmlspecialchars($_GET["est_id"]);
		$lc_datos[2] = htmlspecialchars($_GET["codigo"]);


		if($lc_infoestacion->infoCodigoConfirmacionDelivery($lc_datos))
		{
			if($lc_row = $lc_infoestacion->fn_leerObjeto())
			{	
				echo utf8_encode($lc_row->html);			
				echo utf8_encode($lc_row->htmlf);
			}
		}
	}


?>

</html> 
 
