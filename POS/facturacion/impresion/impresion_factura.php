<?php
session_start();
/////////////////////////////////////////////////////////////////
////////DESARROLLADO POR:Jose Fernandez//////////////////////////
////////DESCRIPCION: Impresion Resumen de Ventas//////////////////
////////TABLAS		: ///////////////////////////////////////////
////////FECHA CREACION	: 15/04/2014/////////////////////////////
/////////////////////////////////////////////////////////////////
////////FECHA ULTIMA MODIFICACION:18/07/2014/////////////////////
////////USUARIO QUE MODIFICO: Jose Fernandez/////////////////////
////////DECRIPCION ULTIMO CAMBIO: Impresion de factura///////////
/////////////////////////////////////////////////////////////////
////////FECHA ULTIMA MODIFICACION:21/07/2014/////////////////////
////////USUARIO QUE MODIFICO: Jose Fernandez/////////////////////
////////DECRIPCION ULTIMO CAMBIO: Configuracion E-fact///////////
/////////////////////////////////////////////////////////////////
////////FECHA ULTIMA MODIFICACION: 01/10/2015 ///////////////////
////////USUARIO QUE MODIFICO: Daniel Llerena ////////////////////
////////DECRIPCION ULTIMO CAMBIO: Impresion de factura por SP ///
/////////////////////////////////////////////////////////////////
/// ////////FECHA ULTIMA MODIFICACION: 05/04/2018 ///////////////
////////USUARIO QUE MODIFICO: Gabriel Mafla /////////////////////
///DECRIPCION ULTIMO CAMBIO: Concatenacion subfijo en llamada a//
///  los SP /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
/// ////////FECHA ULTIMA MODIFICACION: 03/07/2018 ///////////////
////////USUARIO QUE MODIFICO: Daniel Llerena /////////////////////
///DECRIPCION ULTIMO CAMBIO: generar código QR para encuesta //
///  pluh them voc /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////

include_once("../../system/conexion/clase_sql.php");
include_once ("../../clases/clase_impresionFactura.php");

$lc_cfacId = htmlspecialchars($_GET['cfac_id']);
$tipo_comprobante = htmlspecialchars($_GET['tipo_comprobante']);
$comprobanteF = 'F';
$comprobanteN = 'N';
$lc_impresion = new impresion_factura();
$paisIsoAlfa2 = isset($_SESSION['paisIsoAlfa2']) ? $_SESSION['paisIsoAlfa2'] : '';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <script type="text/javascript" src="../../js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="../../js/jquery-qrcode-0.14.0.min.js"></script>

        <?php
        if ($comprobanteF == $tipo_comprobante) {
            $lc_datos[0] = $lc_cfacId;
            $lc_datos[1] = $tipo_comprobante;
            $lc_datos[2] = $paisIsoAlfa2;
            if ($lc_impresion->fn_consultar('impresion_factura', $lc_datos)) {
                while ($lc_row = $lc_impresion->fn_leerObjeto()) {
                    echo html_entity_decode(utf8_encode($lc_row->html));
                    echo html_entity_decode(utf8_encode($lc_row->html3));
                    echo html_entity_decode(utf8_encode($lc_row->html2));

                    if (isset($lc_row->codigoQR)) {
                        $URL_QR = $lc_row->codigoQR;
                        ?>

                        <script>
                            jQuery('#codigoQR').qrcode({
                                render: 'canvas'
                                , ecLevel: 'L'
                                , size: 200
                                , color: '#3A3'
                                , text: '<?PHP echo $URL_QR; ?>'
                            });
                        </script>

                        <?php
                    }

                    echo html_entity_decode(utf8_encode($lc_row->htmlf));
                }
            }
        } else if ($comprobanteN == $tipo_comprobante) {
            $lc_datos[0] = $lc_cfacId;
            $lc_datos[1] = $tipo_comprobante;
            $lc_datos[2] = $paisIsoAlfa2;
            if ($lc_impresion->fn_consultar('impresion_notacredito', $lc_datos)) {
                while ($lc_row = $lc_impresion->fn_leerObjeto()) {
                    echo utf8_encode($lc_row->html);
                    echo utf8_encode($lc_row->html3);
                    echo utf8_encode($lc_row->html2);
                    echo utf8_encode($lc_row->htmlf);
                }
            }
        }
        ?>

</html>