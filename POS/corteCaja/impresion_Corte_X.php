<?php
/*
  FECHA CREACION	   : 17/09/2015
  DESARROLLADO POR   : Christian Pinto
  DESCRIPCION        : Impresion Dinamica de Corte de Caja
  TABLAS	           : ARQUEO_CAJA,BILLETE_ESTACION, CONTROL_ESTACION,ESTACION, BILLETE_DENOMINACION
  FECHA MODIFICACION : 28/09/2016
  MODIFICADO POR     : Daniel Llerena
  DESCRIPCION        : Se modifica el archivo para que impreima Corte XX o Arqueo dependiendo de la variblae que envié.
 */

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_desmontadoCajero.php";
include_once "../clases/clase_facturacion.php";

if (isset($_GET["usr_id"])) {
    $lc_usuarioId = htmlspecialchars($_GET["usr_id"]);
    $lc_datos[0] = $lc_usuarioId;
}
if (isset($_GET["ctrc_id"])) {
    $lc_controlEstacionId = htmlspecialchars($_GET["ctrc_id"]);
    $lc_datos[1] = $lc_controlEstacionId;
}
if (isset($_GET["usr_id_admin"])) {
    $lc_usuarioId_Admin = htmlspecialchars($_GET["usr_id_admin"]);
    $lc_datos[2] = $lc_usuarioId_Admin;
}

$lc_TipoReporte = htmlspecialchars($_GET["tipoReporte"]);
$lc_CorteX = "CorteX";
$lc_Arqueo = "Arqueo";
$lc_Comprobante = "Comprobante";
$lc_ConsumoRecarga = "ConsumoRecarga";
$lc_ReversoConsumoRecarga = "ReversoConsumoRecarga";
$lc_ImpresionVoucherKiosko = "Kiosko";
$lc_desasignacion_motorizado = "DesasignacionMotorizado";


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>

<?php

    if ($lc_TipoReporte === $lc_CorteX) {
        include_once "../clases/clase_desmontadoCajero.php";
        $lc_apertura = new desmontaCaja();
        if ($lc_apertura->fn_impresionDinamicaCorteX($lc_datos)) {
            while ($lc_row = $lc_apertura->fn_leerObjeto()) {
                echo utf8_encode($lc_row->html);
                echo utf8_encode($lc_row->htmla);
                echo utf8_encode($lc_row->htmlb);
                echo utf8_encode($lc_row->htmlc);
            }
        }
    } else if ($lc_TipoReporte === $lc_Arqueo) {
        include_once "../clases/clase_desmontadoCajero.php";
        $lc_apertura = new desmontaCaja();
        if ($lc_apertura->fn_impresionDinamicaArquero($lc_datos)) {
            while ($lc_row = $lc_apertura->fn_leerObjeto()) {
                echo utf8_encode($lc_row->html);
                echo utf8_encode($lc_row->htmla);
                echo utf8_encode($lc_row->htmlb);
                echo utf8_encode($lc_row->htmlc);
            }
        }
    } else if ($lc_TipoReporte === $lc_ImpresionVoucherKiosko) {
        $rsaut_id = htmlspecialchars($_GET['rsaut_id']);
        $tipo = htmlspecialchars($_GET['tipo']);
        $facturasImpresion = new facturas();
        if ($tipo === 'CM' || $tipo === 'CL') {
            print $facturasImpresion->fn_impresionVoucherNuevoFormato($rsaut_id, $tipo);
        }
    } else if ($lc_TipoReporte === $lc_desasignacion_motorizado) {
        $lc_apertura = new desmontaCaja();
        $id_motorizado = htmlspecialchars($_GET['id_motorizado']);
        $id_periodo = htmlspecialchars($_GET['id_periodo']);

        if($lc_apertura->fn_impresionDesasignacionMotorizado($id_motorizado, $id_periodo)){
            while($lc_row = $lc_apertura->fn_leerObjeto()){
                echo utf8_encode($lc_row->html);
            }

        }
    //Comprobante de recarga
    } else if ($lc_TipoReporte === $lc_Comprobante) {
        include_once("../clases/clase_recargaIngreso.php");
        $impresion = new Recargas();
        $respuesta = $impresion->impresionRecarga($lc_usuarioId, $lc_usuarioId_Admin, $lc_controlEstacionId); //idCadena, idRestaurante, idTransaccion
        print $respuesta["head"];
        print $respuesta["totales"];
        print $respuesta["firma"];
        print $respuesta["mensaje"];

    //Comprobante de consumo de recarga
    } else if ($lc_TipoReporte === $lc_ConsumoRecarga) {
        include_once("../clases/clase_recargaIngreso.php");
        $impresion = new Recargas();
        $respuesta = $impresion->impresionRecarga($lc_usuarioId, $lc_usuarioId_Admin, $lc_controlEstacionId); //idCadena, idRestaurante, idTransaccion
        print $respuesta["head"];
        print $respuesta["totales"];
        print $respuesta["firma"];
        print $respuesta["mensaje"];

    } else if ($lc_TipoReporte === $lc_ReversoConsumoRecarga) {
        include_once("../clases/clase_recargaIngreso.php");
        $impresion = new Recargas();
        $respuesta = $impresion->impresionRecarga($lc_usuarioId, $lc_usuarioId_Admin, $lc_controlEstacionId); //idCadena, idRestaurante, idTransaccion
        print $respuesta["head"];
        print $respuesta["totales"];
        print $respuesta["firma"];
        print $respuesta["mensaje"];

    } else if ($lc_TipoReporte === $lc_ImpresionVoucherKiosko) {
        $rsaut_id = htmlspecialchars($_GET['rsaut_id']);
        $tipo = htmlspecialchars($_GET['tipo']);
        $facturasImpresion = new facturas();
        if ($tipo === 'CM' || $tipo === 'CL') {
            print $facturasImpresion->fn_impresionVoucherNuevoFormato($rsaut_id, $tipo);
        }
    }
?>

</html>


