<?php 
session_start();
/////////////////////////////////////////////////////////////////
////////DESARROLLADO POR:Juan Estévez////////////////////////////
////////DESCRIPCION: Impresion ingresos y egresos ///////////////
////////FECHA CREACION: 14/12/2016///////////////////////////////
/////////////////////////////////////////////////////////////////

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_facturacion.php";

$lc_cfacId = htmlspecialchars($_GET['id_controlestacion']);
$lc_opcionFp = htmlspecialchars($_GET['tipo_opcion']);
$lc_impresion = new facturas();
$lc_datos[0] = $lc_cfacId;

    if($lc_opcionFp == 'CajaChica') {
        if($lc_impresion->fn_consultar('impresion_ingreso_egreso',$lc_datos)) { 	
            while ($lc_row = $lc_impresion->fn_leerObjeto()) {		
                echo utf8_encode($lc_row->htmlf);
            }   
        }
    }
    else if($lc_opcionFp == 'FaltanteCxC') {
        if($lc_impresion->fn_impresionDinamicaFaltanteCxC($lc_datos)) { 	
            while ($lc_row = $lc_impresion->fn_leerObjeto()) {		
                echo utf8_encode($lc_row->html);
                echo utf8_encode($lc_row->html2);
                echo utf8_encode($lc_row->htmlf);
            }   
        }
    }

?>	
