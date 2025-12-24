<?php 
session_start();
/////////////////////////////////////////////////////////////////
////////DESARROLLADO POR:Jose Fernandez//////////////////////////
////////DESCRIPCION: Impresion credito sin cupon/////////////////
////////FECHA CREACION	: 30/03/2015/////////////////////////////
/////////////////////////////////////////////////////////////////

include_once("../../system/conexion/clase_sql.php");
include_once ("../../clases/clase_facturacion.php");
$lc_cfacId=/*'K004F000000201';*/$_GET['cfac_id'];
$lc_opcionFp=/*'K004F000000201';*/$_GET['opcionFp'];
$lc_impresion = new facturas();
$lc_datos[0]=$lc_cfacId;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>


    
    <?php 
        if($lc_opcionFp=='EMPRESA')
        {
            if($lc_impresion->fn_consultar('impresion_creditoSinCupon',$lc_datos))
                    { 	
                                    if($lc_row = $lc_impresion->fn_leerObjeto())
                                    {		
                                            echo $lc_row->html;
                                            echo $lc_row->html2;
                                            echo $lc_row->htmlf;
                                    }
                    }
        }
        ELSE if($lc_opcionFp=='EMPLEADO')
        {
           if($lc_impresion->fn_consultar('impresion_creditoEmpleado',$lc_datos))
                    { 	
                                    if($lc_row = $lc_impresion->fn_leerObjeto())
                                    {		
                                            echo $lc_row->html;
                                            echo $lc_row->html2;
                                            echo $lc_row->htmlf;
                                    }
                    }  
        }
        else
        {
            if($lc_impresion->fn_consultar('impresion_creditoProducto',$lc_datos))
                    { 	
                        if($lc_row = $lc_impresion->fn_leerObjeto())
                        {		
                                echo $lc_row->html;
                                echo $lc_row->html2;
                                echo $lc_row->htmlf;
                        }
                    }                                                                                                                                                                                                                                
        }
	?>	                               
</html>

