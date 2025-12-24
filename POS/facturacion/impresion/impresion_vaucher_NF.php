<?php
session_start();
/* //////////////////////////////////////////////////////////////////////////////////////////
// Autor: Juan Esteban Canelos
// Fecha: 01/08/2019
// Descripción: Impresión de voucher con nuevo formato (para certificación pinpad con kiosko)
////////////////////////////////////////////////////////////////////////////////////////// */

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_facturacion.php";

$rsaut_id = htmlspecialchars($_GET['rsaut_id']);
$tipo = htmlspecialchars($_GET['tipo']);
$facturasImpresion = new facturas();

?>
<!DOCTYPE html>
<html>

<?php
    if ($tipo === 'CM' || $tipo === 'CL') {
        if ($facturasImpresion->fn_impresionVoucherNuevoFormato($rsaut_id, $tipo)) {
            if ($lc_row = $facturasImpresion->fn_leerObjeto()) {
                echo utf8_encode($lc_row->html);
                echo utf8_encode($lc_row->htmlf);
            }
        }

    }
?>

</html>