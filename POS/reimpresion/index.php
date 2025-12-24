<?php

session_start();

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_reimpresion.php";
include_once "../seguridades/seguridad_niv2.inc";

$lc_menuPedido = new menuPedido();
include_once "../clases/clase_loguin.php";
$lc_loguin = new loguin();

$usr_id = $_SESSION["usuarioId"];
$cdn_id = $_SESSION["cadenaId"];
$rst_id = $_SESSION["rstId"];
$est_id = $_SESSION["estacionId"];
$est_ip = $_SESSION["direccionIp"];
$lc_condiciones[0]=2;
$lc_condiciones[1]=$est_ip;
$periodo = json_decode($lc_loguin ->fn_consultar("validaPeriodoAbierto",$lc_condiciones))->prd_fechaapertura;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <title>Reimpresión</title>

        <link rel="StyleSheet" type="text/css" href="../css/reimpresion.css"/>
        <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css" />
        <link rel="stylesheet" type="text/css" href="../css/jquery.jb.shortscroll.css" />
        <link rel="stylesheet" type="text/css" href="../css/est_teclado.css" />
        <link rel="stylesheet" type="text/css" href="../css/est_modal.css" />
        <link rel="stylesheet" type="text/css" href="../css/visor_factura.css"/>
        <link rel="stylesheet" type="text/css" href="../css/est_botonesbarra.css"/>
        <link rel="stylesheet" type="text/css" href="../css/alertify.core.css" />
        <link rel="stylesheet" type="text/css" href="../css/alertify.default.css" />
        <link rel="stylesheet" type="text/css" href="../css/teclado.css"/>  
        <link rel="stylesheet" type="text/css" href="../css/media_anular_orden.css" />
        <link rel="stylesheet" type="text/css" href="../css/CalendarPicker.style.css" />
        <link rel="stylesheet" type="text/css" href="../css/style_ver_factura.css"/>
    </head>
    <body>

        <input inputmode="none"  type="hidden" name="hide_cdn_id" id="hide_cdn_id" value="<?php echo $cdn_id; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_usr_id" id="hide_usr_id" value="<?php echo $usr_id; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_rst_id" id="hide_rst_id" value="<?php echo $rst_id; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_est_id" id="hide_est_id" value="<?php echo $est_id; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_est_ip" id="hide_est_ip" value="<?php echo $est_ip; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_periodo" id="hide_periodo" value="<?php echo $periodo; ?>"/>
        <input inputmode="none"  type="hidden" name="hid_transaccion" id="hid_transaccion"/>
        <input inputmode="none"  type="hidden" name="hid_factura" id="hid_factura"/>

        <!-- tabla  transacciones -->
        <div class="cntn_glbl_trnsccns">
            <div id="contenedorDerecha">
                <div id="listaPedido">
                    <div id="listado">
                        <div class="cabeceraOrden">
                            <div class="listaFactura">&nbsp;<b>Transacci&oacute;n</b></div>
                            <div class="listaMesa"><b>Mesa</b></div>
                            <div class="listaSubtotal"><b>Subtotal</b></div>
                            <div class="listaTotal"><b>Total</b></div>
                            <div class="listaCaja"><b>Caja</b></div>
                            <div class="listaCajero"><b>Cajero</b></div>
                            <div class="listaComentario"><b>Observaci&oacute;n</b></div>
                        </div>
                        <ul id="listadoPedido"></ul>
                    </div>
                </div>
            </div>

            <div class="cnt_mn_nfrr_btns" id="tipoDocumentos">
            </div>
        </div>

        <!--VISUALIZAR FACTURA-->
        <div id="visorFacturas" class="overlay" >
            <div id="detalleFactura" class="modalfactura">
                <div id="cabecerafactura" style=" margin: 0; padding: 0; overflow-y: auto;"></div>   		
                <div>
                    <!-- <center> -->
                        <input inputmode="none"  class="botonesbarra" type="button" id="salirVisor" value="OK" onclick="fn_cerrarVisorFacturas()" style="height: 60px; width: 120px; margin: 5px 0 0 0;" />
                    <!-- </center>   -->
                </div>
            </div>
        </div>

        <!-- modal seleccionar impresora -->
        <div id="modalImpresora" style="width: 470px;">
            <div class="preguntasTitulo">Seleccione la impresora</div>
            <div class="anulacionesSeparador">
                <select id="impresora" name="impresora" style="width: 460px;"></select>
            </div>
            <div class="anulacionesSeparadorFin">
                <button id="btnClienteCancelarAnulacion" class="botonesbarra" onclick="fn_botonOk();">Generar</button>
                <button id="btnClienteCancelarAnulacion" class="botonesbarra" onclick="fn_botonCancelar();">Cancelar</button>
            </div>        
        </div> 

        <div id="loading" align="center" style="display:none;background:#FFF;text-align:center;overflow:hidden;">
            <img src="../imagenes/procesando.gif" height="100"/>
        </div>

        <div id="menu_desplegable" class="menu_desplegable">
            <div id="cnt_mn_dsplgbl_pcns_drch" class="modal_opciones_drc">
                <button class="boton_Opcion" onclick="fn_funcionesGerente()">Funciones Gerente</button>
                <button class="boton_Opcion" onclick="fn_salirSistema()">Salir Sistema</button>
            </div>
        </div>

        <script type="text/javascript" src="../js/jquery.min.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.js"></script>
        <script type="text/javascript" src="../js/alertify.js"></script>
        <script type="text/javascript" src="../js/jquery.keypad.js"></script>
        <script type="text/javascript" src="../js/mousewheel.js"></script>
        <script type="text/javascript" src="../js/jquery.jb.shortscroll.js"></script>
        <script type="text/javascript" src="../js/jquery.countdown360.js"></script>
        <script type="text/javascript" src="../js/ajax_reimpresion.js"></script>
        <script type="text/javascript" src="../js/ajax_reimpresionTipoDocumento.js"></script>
        <script type="text/javascript" src="../js/teclado.js"></script>
        <script type="text/javascript" src="../js/ajax_api_reimpresion.js"></script>
        <script type="text/javascript" src="../js/ajax_utility.js"></script>
    </body>
</html>