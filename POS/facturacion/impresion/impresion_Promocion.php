<?php 
session_start(); 

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_impresionPromocion.php";

$lc_codigoFactura = htmlspecialchars($_GET["cfac"]);
$lc_codigoCupon = htmlspecialchars($_GET["cupon"]);

$lc_impresion = new impresionPromocion();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php 
        $lc_datos[0] = $lc_codigoFactura;        
        $lc_datos[1] = $lc_codigoCupon;        
        if($lc_impresion->fn_impresionPromocion($lc_datos)){ 	
            while($lc_row = $lc_impresion->fn_leerObjeto()){ 
                echo str_replace( "\n", "<br />", utf8_encode($lc_row->html));
                echo str_replace( "\n", "<br />", utf8_encode($lc_row->htmlf));
            } 
        }			
?>	   

</html>