<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Impresion Detalle Pedido</title>
        <link rel="StyleSheet" href="../../css/style_impresion_ordenpedido.css" type="text/css" />
    </head>
    <body>
        <?php
        require_once "../../system/conexion/clase_sql.php";
        include_once "../../clases/clase_impresionCupones.php";
        $ImpresionCupones = new ImpresionCupones();
        $cupon = $_GET['cupon'];
        $nombre = $_GET['usr_descripcion'];
        $html = $ImpresionCupones->printCouponDetails($cupon, $nombre);
        echo $html;

        ?>
    </body>
</html>