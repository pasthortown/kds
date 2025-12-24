<?php
session_start();

include_once"../system/conexion/clase_sql.php";
include_once"../clases/clase_seguridades.php";
include_once"../clases/clase_ordenPedido.php";
include_once"../seguridades/seguridad_niv2.inc";

/////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO: Jorge Tinoco ///////////////////////////////////////////
///////FECHA CREACION: 06/02/2016 ///////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR: Christian Pinto //////////////////////////////////////
///////DESCRIPCION: Cierre Periodo abierto mas de un dia ////////////////////
///////FECHA MODIFICACION: 07/07/2016 ///////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>Fidelización</title>

        <!-- Librerias CSS -->
        <link rel="StyleSheet" href="../css/tomaPedido.css" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="../css/teclado.css"/>
        <link rel="stylesheet" type="text/css" href="../css/bloquear_acceso.css"/>
        <link rel="stylesheet" type="text/css" href="../css/fidelizacion_inicio.css"/>
        <!-- Librerias JavaScript -->
        <script src="../js/jquery.min.js"></script>
        <script src="../js/jquery-ui.js"></script>
        <!-- Scripts para scroll-->
        <script type="text/javascript" src="../js/alertify.js"></script>
        <script type="text/javascript" src="../js/ajax_fidelizacion_menu.js"></script>
        <script type="text/javascript" src="../js/ajax_fidelizacion_fin.js"></script>
        <script type="text/javascript" src="../js/ajax_api_impresion.js"></script>
    </head>
    <?php
    if (htmlspecialchars(isset($_GET["numMesa"]))) {
        $mesa_id = htmlspecialchars($_GET["numMesa"]);
    } else {
        $mesa_id = "";
    }


    if (htmlspecialchars(isset($_GET["numFactura"]))) {
        $numFactura = htmlspecialchars($_GET["numFactura"]);
    } else {
        $numFactura = "";
    }

    if (htmlspecialchars(isset($_GET["rst_id"]))) {
        $rst_id = htmlspecialchars($_GET["rst_id"]);
    } else {
        $rst_id = "";
    }

    if (htmlspecialchars(isset($_GET["cdn_id"]))) {
        $cdn_id = htmlspecialchars($_GET["cdn_id"]);
    } else {
        $cdn_id = "";
    }

    if (htmlspecialchars(isset($_GET["op_id"]))) {
        $op_id = htmlspecialchars($_GET["op_id"]);
    } else {
        $op_id = "";
    }

    if (htmlspecialchars(isset($_GET["tipo_s"]))) {
        $tipo_s = htmlspecialchars($_GET["tipo_s"]);
    } else {
        $tipo_s = "";
    }
    ?>

    <input inputmode="none"  type="hidden" name="numMesa" id="numMesa" value="<?php echo $mesa_id; ?>">


        <input inputmode="none"  type="hidden" name="numMesa" id="hidden_numFactura" value="<?php echo $numFactura; ?>">
            <input inputmode="none"  type="hidden" name="numMesa" id="hidden_rst_id" value="<?php echo $rst_id; ?>">
                <input inputmode="none"  type="hidden" name="numMesa" id="hidden_cdn_id" value="<?php echo $cdn_id; ?>">


                    <input inputmode="none"  type="hidden" name="txtOrdenPedidoId" id="txtOrdenPedidoId" value="<?php echo $op_id; ?>">
                    <input inputmode="none"  type="hidden" name="txtTipoServicio" id="txtTipoServicio" value="<?php echo $tipo_s; ?>">


                    <body style="overflow-y: hidden">
                        <div class="container" id="pre1">
                            <div class="row">
                                <div class="col-md-2 text-right"></div>
                                <div class="col-md-8">
                                    <div  class="marco">
                                        <div class="titulo"  id="PuntosAcumulados"></div>
                                        <div class="botones">
                                            <input inputmode="none"  type="button"  class="button_fdzn" onclick="redireccionar('<?php echo $mesa_id; ?>')" value="Ok"></input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                        </div>

                        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
                            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                                <img src="../imagenes/loading.gif"/>
                            </div>
                        </div>

                    </body>
                    </html>