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
include_once"../../clases/clase_impresionOrdenPedido.php";

$orden = new impresion_pedido();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Impresion Detalle Pedido</title>
        <link rel="StyleSheet" href="../../css/style_impresion_ordenpedido.css" type="text/css"/>

    </head>

    <?php
        if (isset($_GET["codigo_app"]) && htmlspecialchars($_GET["codigo_app"])) {
            $codigo_app = $_GET["codigo_app"];
            $result_html = $orden->fn_impresionPreOrdenImpresionSonido($codigo_app);
            die(utf8_encode($result_html['html']));
        }
        $usuario = "";
        $mesa = "";
        $fecha = "";

        if (htmlspecialchars(isset($_GET["odp_id"]))) {
            $odp_id = htmlspecialchars($_GET["odp_id"]);
            $tipoServicio = htmlspecialchars($_GET["tipoServicio"]);
            $canalImpresion = htmlspecialchars($_GET["canalImpresion"]);
            $guardaOrden = htmlspecialchars($_GET["guardaOrden"]);
            $numeroCuenta = htmlspecialchars($_GET["numeroCuenta"]);

            $lc_datos[0] = $odp_id;
            $lc_datos[1] = $tipoServicio;
            $lc_datos[2] = $canalImpresion;
            $lc_datos[3] = $guardaOrden;
            $lc_datos[4] = $numeroCuenta;

            if ($orden->fn_impresionDinamicaOrdenPedido($lc_datos)) {
                while ($lc_row = $orden->fn_leerObjeto()) {
                    echo utf8_encode($lc_row->html);
                }
            }
        }
    ?>

    <body>

    </body>
</html>