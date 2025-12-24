<?php
///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jorge Tinoco /////////////////////////////////////////
///////DESCRIPCION: Impresion orden pedido ////////////////////////////////////
///////TABLAS INVOLUCRADAS: Cabecera_Orden_Pedido, ////////////////////////////
/////// Detalle_Orden_Pedidio, Texto_Detalle_Orden_Pedido /////////////////////
///////////////////Plus, Precio_Plu ///////////////////////////////////////////
///////FECHA CREACION: 11-05-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once"../../system/conexion/clase_sql.php";
include_once"../../clases/clase_impresion_precuenta.php";

$orden = new impresion_precuenta();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Impresion Detalle Pedido</title>
        <link rel="StyleSheet" href="../../css/style_impresion_precuenta.css" type="text/css"/>

    </head>
    <body>
        <?php
        $usuario = "";
        $mesa = "";
        $fecha = "";

        if (isset($_GET["odp_id"])) {
            if (isset($_GET["rst_id"])) {
                if(isset($_GET["split_num"])) {
                    $rst_id = $_GET["rst_id"];
                    $odp_id = $_GET["odp_id"];
                    $dop_cuenta=$_GET["split_num"];
                    $lc_datos[0] = $rst_id;
                    $lc_datos[1] = $odp_id;
                    $lc_datos[2] = $dop_cuenta;
                    if ($orden->fn_consultar('Cargar_Precuenta', $lc_datos)) {
                        if ($lc_row = $orden->fn_leerObjeto()) {
                            echo utf8_encode($lc_row->html);
                        }
               
                
                    }
                }
            }
        }
        ?>

    </body>
</html>