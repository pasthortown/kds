<?php 
/*
FECHA CREACION	   : 15-12-2016 
DESARROLLADO POR   : Jose Fernandez
DESCRIPCION        : Impresion Dinamica del Faltante de Caja CxC
TABLAS	           : retiros, control_estacion
FECHA MODIFICACION : 
MODIFICADO POR     : 
DESCRIPCION        : 
 */
 
include_once "../system/conexion/clase_sql.php";	
include_once "../clases/clase_desmontadoCajero.php";

$lc_apertura = new desmontaCaja();
$lc_controlEstacionId = htmlspecialchars($_GET["ctrc_id"]);
$lc_TipoReporte = htmlspecialchars($_GET["tipo"]);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php

    $lc_datos[1] = $lc_controlEstacionId;

    if($lc_TipoReporte == "FALTANTE") {              
	if($lc_apertura->fn_impresionDinamicaFaltanteCxC($lc_datos)) {                               
            while($lc_row = $lc_apertura->fn_leerObjeto()) {	                   
                echo utf8_encode($lc_row->html);
                echo utf8_encode($lc_row->html2);
                echo utf8_encode($lc_row->htmlf);
            }
        }       
    }      
	
?>

</html> 
 
