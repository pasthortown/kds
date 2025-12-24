<?php 
session_start();
/////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Daniel Llerena
////////DESCRIPCION: Impresion retiro efectivo y formas de pago
////////FECHA CREACION	: 01/10/2015
/////////////////////////////////////////////////////////////////

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_desmontadoCajero.php";

$lc_apertura = new desmontaCaja();
$lc_control = htmlspecialchars($_GET["ctrc_id"]);
$tipo = htmlspecialchars($_GET["tipo"]);
$idMotorizado = isset($_GET["idMotorizado"]) && !empty($_GET["idMotorizado"]) ? htmlspecialchars($_GET["idMotorizado"]) : null;
$tipoRE = "RE";
$tipoRF = "RFP";
$tipoDC = "DC";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php
$lc_datos[2]  = $idMotorizado;

    if($tipo === $tipoRE) {
        $lc_datos[0] = $lc_control;
	$lc_datos[1] = $tipo;	
	if($lc_apertura->fn_fn_impresionDinamicaRetiroEfectivo($lc_datos)) {
            if($lc_row = $lc_apertura->fn_leerObjeto()) {		
                echo utf8_encode($lc_row->html);		
                echo utf8_encode($lc_row->htmlf);
            }
	}        
    } else if($tipo === $tipoRF) {
        $lc_datos[0] = $lc_control;
	$lc_datos[1] = $tipo;	
	if($lc_apertura->fn_fn_impresionDinamicaRetiroFormasPago($lc_datos)) {
            if($lc_row = $lc_apertura->fn_leerObjeto()) {		
                echo utf8_encode($lc_row->html);		
                echo utf8_encode($lc_row->htmlf);
            }
	}
    } else if($tipo === $tipoDC) {
        $lc_datos[0] = $lc_control;
	$lc_datos[1] = $tipo;	
	if($lc_apertura->fn_impresionDinamicaDesmontadoCajeroEfectivo($lc_datos)) {
            if($lc_row = $lc_apertura->fn_leerObjeto()) {		
                echo utf8_encode($lc_row->html);		
                echo utf8_encode($lc_row->htmlf);
            }
	}
    }
?>

</html>

