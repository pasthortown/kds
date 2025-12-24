<?php
session_start();
/////////////////////////////////////////////////////////////////
////////DESARROLLADO POR:Jose Fernandez//////////////////////////
////////DESCRIPCION	: Impresion Voucher//////////////////////////
////////TABLAS		: 
////////FECHA CREACION	: 24/04/2014/////////////////////////////
/////////////////////////////////////////////////////////////////
////////FECHA ULTIMA MODIFICACION: 03/10/2015 ///////////////////
////////USUARIO QUE MODIFICO: Daniel Llerena ////////////////////
////////DECRIPCION ULTIMO CAMBIO: Impresion de voucher por SP ///
/////////////////////////////////////////////////////////////////

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_facturacion.php";

$lc_resul_id = htmlspecialchars($_GET['rsaut_id']);
$tipo = htmlspecialchars($_GET['tipo']);
$tipoCM = 'CM';
$tipoCL = 'CL';
$lc_impresion = new facturas();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
    if ($tipo === $tipoCM) {
        $lc_datos[0] = $lc_resul_id;
        $lc_datos[1] = $tipo;
        if ($lc_impresion->fn_impresionVoucherComercio($lc_datos)) {
            if ($lc_row = $lc_impresion->fn_leerObjeto()) {
                echo utf8_encode($lc_row->html);
                echo utf8_encode($lc_row->htmlf);
            }
        }

    } else if ($tipo === $tipoCL) {
        $lc_datos[0] = $lc_resul_id;
        $lc_datos[1] = $tipo;
        if ($lc_impresion->fn_impresionVoucherCliente($lc_datos)) { 	
            if ($lc_row = $lc_impresion->fn_leerObjeto()) {
                echo utf8_encode($lc_row->html);
                echo utf8_encode($lc_row->htmlf);
            }
        }
    }
?>

</html>