<?php
/*
  -------------------------------------------------------------------------------
  FECHA CREACION      : 24-03-2014
  DESARROLLADO POR    : Cristhian Castro
  DESCRIPCION         :
  -------------------------------------------------------------------------------
  FECHA MODIFICACION  : 07-05-2014
  MODIFICADO POR      : Cristhian Castro
  DECRIPCION CAMBIO   : Documentación y validación en Internet Explorer 9
  -------------------------------------------------------------------------------
  FECHA MODIFICACION  : 22-05-2015
  MODIFICADO POR      : Christian Pinto
  DECRIPCION CAMBIO   : Aumento de campos cajero y estado factura, permisos para reimprimir factura
  -------------------------------------------------------------------------------
  FECHA MODIFICACION  : 02/03/2017
  MODIFICADO POR      : Daniel Llerena
  DECRIPCION CAMBIO   : Nota de crédito -cambio datos de cliente
  -------------------------------------------------------------------------------
  FECHA MODIFICACION  : 10/09/2018
  MODIFICADO POR      : Juan Esteban Canelos
  DECRIPCION CAMBIO   : Reverso de recargas - amigos Juan Valdez
  -------------------------------------------------------------------------------
 */
session_start();

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_anularOrden.php";
include_once "../seguridades/seguridad_niv2.inc";

$lc_menuPedido = new menuPedido();
include_once "../clases/clase_loguin.php";
$lc_loguin = new loguin();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <title>Anular Orden</title>
        <link rel="StyleSheet" type="text/css" href="../css/anularorden.css"/>
        <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css" />
        <!-- Scripts para scroll-->
        <link rel="stylesheet" href="../css/jquery.jb.shortscroll.css" />
        <!-- Estilos para keyboard buscador -->
        <link rel="stylesheet" type="text/css" href="../css/est_teclado.css" />
        <link rel="stylesheet" type="text/css" href="../css/est_modal.css" />
        <link rel="stylesheet" type="text/css" href="../css/visor_factura.css"/>
        <link rel="stylesheet" type="text/css" href="../css/est_botonesbarra.css"/>
        <!-- Estilos para Altertas -->
        <link rel="stylesheet" type="text/css" href="../css/alertify.core.css" />
        <link rel="stylesheet" type="text/css" href="../css/alertify.default.css" />
        <!-- Estilos para keyboard credenciales -->
        <link rel="stylesheet" type="text/css" href="../css/teclado.css"/>  
        <link rel="stylesheet" type="text/css" href="../css/media_anular_orden.css" />

        <link rel="stylesheet" type="text/css" href="../css/CalendarPicker.style.css" />

        <style>
            .contenedor-fecha {    
                position: absolute;
                height: auto;
                width: 600px;
                top: 100px;
                left: 300px;
                background: #fff;
                border: 1px solid #a6c9e2;
                padding: 20px;
                box-shadow: 10px 10px 8px rgb(0 0 0 / 50%);
                border-radius: 5px;
            }

            .input-field{
                margin: 6px 0 6px 25px; 
                width: 225px; 
                height: 40px; 
                font-size: 30px; 
                color: #444;
            }

            .icon{
                position: absolute;
                padding-top: 17px;
                margin-left: -30px;
            }
            div[aria-describedby="anulacionesContenedor"] {
                left: 259px !important;
                top: 80px !important;
                width: auto !important;
            }
            div[aria-describedby="datosFactura"] {
                width: 970px !important;
            }
            div[aria-describedby="anulacionesMotivo"] {
                width: 762px !important;
                top: 80px !important;
                left: 10%  !important;
                height: 647px !important;
            }
            .anulacionesMotivo  { 
                height: 715px !important;
            }
            #keyboard {
                top: 10px !important;
                width: 728px !important; 
                position: initial !important; 
                
            } 
            div[aria-describedby="anulacionesMotivo"] > .anulacionesMotivo > .anulacionesSeparador > div > div > div > #motivosAnulacion  {
                width: 98% !important;
            }

            div[aria-describedby="anulacionesMotivo"] > .anulacionesMotivo > .anulacionesSeparador > div > div.row:nth-child(2) > div > #motivoObservacion  {
                width: 98% !important;               
            }
            div[aria-describedby="anulacionesMotivo"] > .anulacionesMotivo > .anulacionesSeparador {
                width: 100% !important;
            }

        </style>
    </head>
    <?php
    $usr_id = $_SESSION["usuarioId"];
    $cdn_id = $_SESSION["cadenaId"];
    $rst_id = $_SESSION["rstId"];
    $est_id = $_SESSION["estacionId"];
    $est_ip = $_SESSION["direccionIp"];
    $bloqueado = $_SESSION["bloqueoacceso"];
    $ValidacionRucCodigo = trim($_SESSION['ValidacionRucCodigo']);
    $ValidacionErrorRUC = trim($_SESSION['ValidacionErrorRUC']);
    $HabilitarValidacionRUC = trim($_SESSION['HabilitarValidacionRUC']);
    $fidelizacionActiva = ( (isset($_SESSION["fidelizacionActiva"])) ) ? $_SESSION["fidelizacionActiva"] : 0;
    $turneroActivo = isset($_SESSION["turneroActivo"])?$_SESSION["turneroActivo"]:0;
    $turneroUri =  isset($_SESSION["turneroURl"])?$_SESSION["turneroURl"]:'';

    $ValidacionRUCintento = trim($_SESSION['ValidacionRUCintento']);
    $ValidacionRUCdirecto = trim($_SESSION['ValidacionRUCdirecto']);
    $ValidacionRUCdirectoN = trim($_SESSION['ValidacionRUCdirectoN']);

    $lc_condiciones[0]=2;
	$lc_condiciones[1]=$est_ip;
	$periodo = json_decode($lc_loguin ->fn_consultar("validaPeriodoAbierto",$lc_condiciones))->prd_fechaapertura;
    $dias = json_decode($lc_menuPedido->getRestauranteColeccionDeDatos($rst_id, 'CAMBIO DE DATOS PERIODOS ANTERIORES', 'CANTIDAD DE DIAS ANTERIORES'));
    if ($dias->str > 0) {
        $aplicaMesVigentePA = $dias->variableB;
        $cantidadDiasPA = $dias->variableI;
    }

    $porcentajeImpuesto = json_decode($lc_menuPedido->getImpuestoRestaurante($cdn_id, $rst_id))->porcentajeImpuesto;
    $ValidacionAnulacionFacturaTiempoApp = trim($_SESSION['ValidacionAnulacionFacturaTiempoApp']);
    $ValidacionAnulacionFacturaTiempoFast = trim($_SESSION['ValidacionAnulacionFacturaTiempoFast']);
    ?>
    <body style="overflow-y: auto">

        <input inputmode="none"  type="hidden" name="hide_cdn_id" id="hide_cdn_id" value="<?php echo $cdn_id; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_usr_id" id="hide_usr_id" value="<?php echo $usr_id; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_rst_id" id="hide_rst_id" value="<?php echo $rst_id; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_est_id" id="hide_est_id" value="<?php echo $est_id; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_est_ip" id="hide_est_ip" value="<?php echo $est_ip; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_periodo" id="hide_periodo" value="<?php echo $periodo; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_aplicaMesVigentePA" id="hide_aplicaMesVigentePA" value="<?php echo $aplicaMesVigentePA; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_cantidadDiasPA" id="hide_cantidadDiasPA" value="<?php echo $cantidadDiasPA; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_porcentajeImpuesto" id="hide_porcentajeImpuesto" value="<?php echo $porcentajeImpuesto; ?>"/>
        <input inputmode="none"  type="hidden" name="hide_tipo_servicio" id="hide_tipo_servicio"/>
        <input inputmode="none"  type="hidden" id="hide_saut_id" value=""/>
        <input inputmode="none"  type="hidden" id="hide_ncre_id"/>
        <input inputmode="none"  type="hidden" id="tiempoEspera" value="<?php echo $_SESSION["tiempoEsperaTarjetas"]; ?>"/>
        <input inputmode="none"  type="hidden" id="txt_bloqueado" value="<?php echo $bloqueado ?>"/>
        <input inputmode="none"  type="hidden" id="hid_notaCredito" />
        <input inputmode="none"  type="hidden" id="hid_PlanFidelizacion" value="<?php echo $fidelizacionActiva; ?>" />
        <input inputmode="none" type="hidden" id="ValidacionRUCNIntentos" value="0"/>
        <input inputmode="none"  id="hide_turneroActivo"  type="hidden"  value="<?php echo $turneroActivo;?>"/>
        <input inputmode="none"  id="hide_turneroURl"     type="hidden"  value="<?php echo $turneroUri;?>"/>
        <input inputmode="none"  id="hide_opcion_nota_credito" type="hidden" value="0"/>
        <input inputmode="none"  id="hide_cliente" type="hidden"/>
        <input inputmode="none"  id="aplica_nc_sinconsumidor" type="hidden"/>
        <input id="proveedor_tracking"          type="hidden"/>
        <input inputmode="none" type="hidden" id="ValidacionRucCodigo" value="<?php echo $ValidacionRucCodigo; ?>"/>
        <input inputmode="none" type="hidden" id="ValidacionErrorRUC" value="<?php echo $ValidacionErrorRUC; ?>"/>
        <input inputmode="none" type="hidden" id="HabilitarValidacionRUC" value="<?php echo $HabilitarValidacionRUC; ?>"/>
        <input inputmode="none" type="hidden" id="ValidacionRUCintento" value="<?php echo $ValidacionRUCintento; ?>"/>
        <input inputmode="none" type="hidden" id="ValidacionRUCdirecto" value="<?php echo $ValidacionRUCdirecto; ?>"/>
        <input inputmode="none" type="hidden" id="ValidacionRUCdirectoN" value="<?php echo $ValidacionRUCdirectoN; ?>"/>
        <input inputmode="none" type="hidden" name="ValidacionAnulacionFacturaTiempoApp" id="ValidacionAnulacionFacturaTiempoApp" value="<?php echo $ValidacionAnulacionFacturaTiempoApp; ?>"/>
        <input inputmode="none" type="hidden" name="ValidacionAnulacionFacturaTiempoFast" id="ValidacionAnulacionFacturaTiempoFast" value="<?php echo $ValidacionAnulacionFacturaTiempoFast; ?>"/>

        <div class="cntn_glbl_trnsccns">
            <div id="contenedorIzquierda">
                <div id="busqueda">
                    <div class="botonesLista">
                        <b>&ensp; Transacciones</b>
                    </div>
                    <div>
                        <input inputmode="none"  type="text" id="parBusqueda" value="" style="margin: 6px 0 6px 25px; width: 225px; height: 40px; font-size: 30px; color: #444;"/>
                    </div>
                </div>
                <div id="busquedaTransacciones">
                    <div class="botonesLista">
                        <b>Transacciones de Periodos Anteriores</b>
                    </div>
                    
                    <div>
                        <div style="padding-left: 25px; padding-top: 80px;"> 
                            Fecha Transacción:
                            <label id="documento_obligatorios" style="color:#F00">(*)</label>
                        </div>
                        <input inputmode="none" type="text" id="fechaTran" value="" class="input-field">
                        <i class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </i>
                    </div>

                    <div>
                        <div style="padding-left: 25px; padding-top: 10px;"> Código Transacción:</div>
                        <input inputmode="none" type="text" id="codigoTran" value="" class="input-field">
                    </div>

                    <div>
                        <div style="padding-left: 25px; padding-top: 10px;"> Nro. Factura:</div>
                        <input inputmode="none" type="text" id="nroFactura" value="" class="input-field">
                    </div>

                    <div>
                        <div style="padding-left: 25px; padding-top: 10px;"> Identificacion:</div>
                        <input inputmode="none" type="text" id="identificacion" value="" class="input-field">
                    </div>

                    <div>
                        <button class="btn btn-success btn-lg" onclick="buscarPeriodosAnteriores();" style="margin: 6px 0 6px 65px; height: 50px;">Buscar</button>
                    </div>
                </div>

                <div id="opciones" style="height: 402px;"></div>
                <div class="calculosTransacciones" style="padding: 0;">
                    <div class="calculosTrans">Transacciones: <b><span id="calculoTransNum"></span></b></div>
                    <div class="calculosTrans">Anulaciones: <b><span id="calculoAnulacion">0</span></b></div>
                    <div class="calculosTrans">Ventas: <b>$<span id="calculoTransTotal"></span></b></div>
                </div>
                <!-- Fin Izquierda -->
            </div>

            <!-- Contenedor Derecha -->
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
                            <!-- <div class='listaStatus'><b>Estado</b></div> -->
                            <div class="listaComentario"><b>Observaci&oacute;n</b></div>
                        </div>
                        <ul id="listadoPedido"></ul>
                    </div>

                    <div id="listadoTxTarjetas">
                        <div class="cabeceraOrden">
                            <div class="listaFactura">&nbsp;<b>Tipo</b></div>
                            <div class="listaMesa"><b>Valor</b></div>
                            <div class="listaComentario"><b>Tarjeta</b></div>   
                            <div class="listaComentario"><b>Fecha</b></div>
                            <div class="listaComentario"><b>Hora</b></div>
                        </div>
                        <ul id="listadoTxTarjetass"></ul>
                    </div>

                    <div id="divReversos">
                        <div class="cabeceraOrden">
                            <div class="listaFactura"><b>Transacci&oacute;n</b></div>
                            <div class="listaCaja"><b>Valor</b></div>
                            <div class="listaComentario"><b>Cajero</b></div>
                            <div class="listaStatus"><b>Estado</b></div>
                            <div class="listaCliente"><b>Cliente</b></div>
                        </div>
                        <ul id="listadoReversos"></ul>
                    </div>
                </div>

            </div>
            <!-- Contenedor Inferior -->
            <div class="cnt_mn_nfrr_btns">
                <input type="button" id="boton_sidr" value="Menu" class="boton_Accion" onclick="" style="float: right; margin: 5px 20px 0 0;"/>
                <input type="button" id="anularOrden" value="Nota de Cr&eacute;dito" class="boton_Opcion" onclick="valorarCambioSobreFactura('notaCredito')" style="float: left; margin: 5px 10px 0 0;"/>
                <input class="boton_Opcion" type="button" id="retomarOrden" value="Recuperar" style="float: left; margin: 5px 10px 0 0;"/>
                <input class="boton_Opcion" type="button" id="visualizarFactura" value="Visualizar" onclick="fn_visualizarFactura()" style="float: left; margin: 5px 10px 0 0;"/>
                <input class="boton_Opcion" type="button" id="verFormasPago" value="Formas de Pago" onclick="fn_visualizarFormasPago()" style="float: left; margin: 5px 10px 0 0;"/>
                <input class="boton_Opcion" type="button" id="imprimirTransaccion" value="Imprimir" onclick="fn_reimpresion()" style="float: left; margin: 5px 10px 0 0;" />
                <input class="boton_Opcion" type="button" id="cambiarDatosCliente" value="Cambiar Datos Cliente" onclick="valorarCambioSobreFactura('cambioDato')" style="float: left; margin: 5px 10px 0 0;"/>
            </div>
            <!-- Fin Derecha -->
        </div>

        <div id="anulacionesContenedor" align="center">
            <div class="preguntasTitulo">Ingrese Credenciales de Administrador</div>
            <div class="anulacionesSeparador">
                <div class="anulacionesInput">
                    <input inputmode="none"  type="password" name="usr_clave" id="usr_clave" style="height: 35px; width: 454px; font-size: 20px;"/>
                </div>
            </div> 
            <div id="numPad" align="center" style="left: 17%; font-size: 34px;"></div>
        </div>

        <div id="anulacionesPago"  align="center">
            <div class="preguntasTitulo">Forma de pago a anular</div>   
            <div class="anulacionesSeparador">
                <div class="anulacionesFormas">
                    <table class="anulacionesFormasTablas">
                        <tr class="anulacionesFormasTr"></tr>
                    </table>
                </div>
                <br>
            </div>
            <div class="anulacionesSeparadorFin">
                <div class="anulacionesSubmit"><button class="botonesbarra" id="btn_anulacancela" onclick="fn_cerrarDialogoPago()">Cancelar</button></div>
            </div>        
        </div>         
        <div id="anulacionesMotivo" class="anulacionesMotivo container d-flex align-items-center justify-content-center">
            <div class="preguntasTitulo">Ingrese el motivo</div>
            <div class="anulacionesSeparador">
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                <select id="motivosAnulacion" style="width: 460px;"></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                            <div class="anulacionesLabel" style="font-size:20px;"><br />Observaci&oacute;n:</div>
                                <textarea inputmode="none" name="motivoObservacion" id="motivoObservacion"
                                    style="width: 460px;"></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col">
                                <div id="keyboard"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="anulacionesSeparadorFin">
                <div class="anulacionesSubmit"></div>
            </div>
        </div> 

        <div id="advertenciaAnulacionesPago">
            <div class="preguntasTitulo">Advertencia</div>               
                <div id="advertenciaAnulacion"></div>
        </div> 

        <!--<div id="numPad"></div>-->
       

        <div id="loading" align="center" style="display:none;background:#FFF;text-align:center;overflow:hidden;">
            <img src="../imagenes/procesando.gif" height="100"/>
        </div>

        <div id="visorFacturas" class="overlay">
            <div id="detalleFactura" class="modal" style="width: 400px">
                <div id="cabecerafactura"></div>
                <div id="opcionesfactura">
                    <input inputmode="none"  class="boton_Opcion" type="button" id="imprimirFactura" value="Imprimir" onclick="fn_reimpresionFactura()" />
                    <input inputmode="none"  class="boton_Opcion" type="button" id="salirVisor" value="Cerrar" onclick="fn_cerrarVisorFacturas()" />
                </div>
            </div>
        </div>

        <div id="lectorTrama">
            <input inputmode="none"  type="text" name="txt_trama" id="txt_trama" />
        </div>

        <div id="contenedorRetomarOrden"></div>

        <!-- PAD PARA EL INGRESO DE NUMEROS DE SEGURIDAD DE TARJETA - CVV -->
        <div id="div_cvv">
            <label style="font-size:24px;"> Ingrese CVV </label>
            <input inputmode="none"  style="font-size:18px;" type="text" name="txt_cvv" id="txt_cvv" value="" maxlength="4"/>
            <table>
                <tr>
                    <td><button onclick="fn_agregarNumeroCVV('7')" value=" 7 " >7</button></td>
                    <td><button onclick="fn_agregarNumeroCVV('8')" value=" 8 "  >8</button></td>
                    <td><button onclick="fn_agregarNumeroCVV('9')" value=" 9 " >9</button></td>
                </tr>
                <tr>
                    <td><button onclick="fn_agregarNumeroCVV('4')" value=" 4 "  >4</button></td>
                    <td><button onclick="fn_agregarNumeroCVV('5')" value=" 5 "  >5</button></td>
                    <td><button onclick="fn_agregarNumeroCVV('6')" value=" 6 "  >6</button></td>
                </tr>
                <tr>
                    <td><button onclick="fn_agregarNumeroCVV('1')" value=" 1 "  >1</button></td>
                    <td><button onclick="fn_agregarNumeroCVV('2')" value=" 2 "  >2</button></td>
                    <td><button onclick="fn_agregarNumeroCVV('3')" value=" 3 "  >3</button></td>
                </tr> 
                <tr>
                    <td><button style="background-color:#81F781;" id="fn_okCVV">OK</button></td>
                    <td align="center"><button onclick="fn_agregarNumeroCVV('0')" >0</button></td> 
                    <td><button id="btn_borrarCVV" onclick="fn_eliminarCantidadCVV()" value=" &lArr; " >Borrar</button></td>           
                </tr> 
                <tr align="center">
                    <td colspan="3">
                        <button style="background-color:#F66;" id="fn_canCVV" onclick="fn_canCVV()">Cancelar</button>         
                    </td>            
                </tr> 
            </table>
        </div>

        <div id="div_tipoCuentaTarjeta" title="Seleccione Tipo de Cuenta."></div>

        <div id="visorFormasPago" class="overlay">
            <div id="detalleFormasPago" class="modalFormasPago">
                <div class="cnt_frms_pg_trnsccns">
                    <div id="lista_formaspago"></div>
                    <div id="trans_formaspago">
                        <div class="titulo_transacciones"><h4>Transacciones</h4></div>
                        <table id="detalles_transaccionesformapago"></table>
                    </div>
                </div>
                <!-- FIN FACTURA -->
                <div class="cntndr_pcns_trnsccns_frms_pg">
                    <div style="width: 300px; margin: 0 auto;">
                        <input inputmode="none"  class="boton_Opcion" type="button" id="imprimirFactura2" value="Imprimir" onclick="fn_imprimirFormasPago()" style="display: none; width:120px; margin-top: 5px;"/>
                        <input inputmode="none"  class="boton_Opcion" type="button" id="salirVisorFormas" value="Cerrar" onclick="fn_cerrarVisorFormasPago()" style="width: 120px; margin-top: 5px;"/>
                    </div>
                </div>
            </div>
        </div>

        <div id="menu_desplegable" class="menu_desplegable">
            <div id="cnt_mn_dsplgbl_pcns_drch" class="modal_opciones_drc">
                <button class="boton_Opcion" id="cuentasAbiertas" onclick="fn_cuentasAbiertas()">Cuentas Abiertas</button>
                <button class="boton_Opcion" id="cuentasCerradas" onclick="fn_cuentasCerradas()">Cuentas Cerradas</button>
                <button class="boton_Opcion" id="cuentasPeriodosAnteriores" onclick="fn_cuentasPeriodosAnteriores()" style="display: none;">Cuentas Periodos Anteriores</button>
                <button class="boton_Opcion" id="reversosRecargas" onclick="fn_recargas()">Recargas</button>
                <button class="boton_Opcion" id="tarjetas" onclick="fn_txTarjetas()">Tarjetas</button>
                <button class="boton_Opcion" onclick="fn_funcionesGerente()">Funciones Gerente</button>
                <button class="boton_Opcion" onclick="fn_salirSistema()">Salir Sistema</button>
                <button class="boton_Opcion" id="nuevaorden" onclick="fn_obtenerMesa()">Orden Pedido</button>
            </div>
        </div>

        <div id="cntFormulario"></div>
        <div style="display: none;" id="countdown"></div>
        <div id="modalBloquearCargaCronometro" class="modal_cargando" style="display: none"/>
            <!--Pantalla Cambio de Datos Cliente-->
            <?php
            include_once "../anulacion/cambioDatosCliente.php";
            ?>
        </div>

        <div id="mdl_rdn_pdd_crgnd1" class="modal_cargando" style="display: none">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img id="loadingAnularImg" src="../imagenes/loading.gif"/>
            </div>
        </div>

        <div id="contenedorFechaTransaccion" class="contenedor-fecha" style="display: none;">
            <div class="preguntasTitulo" style="margin-bottom: 20px;">Ingrese la Fecha</div>
            <div id="myCalendarWrapper"></div>
            <button class="btnVirtualCancelar" onclick="closeContenedorFecha()" style="margin-left: 300px;">Cancelar</button>
        </div>
    </div>
    </div>
    
    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script type="text/javascript" src="../js/jquery-ui.js"></script>
    <!-- Script para Altertas -->
    <script type="text/javascript" src="../js/alertify.js"></script>
    <!-- Scripts para keyboard buscador -->
    <script type="text/javascript" src="../js/jquery.keypad.js"></script>
    <!-- Scripts para scroll-->
    <script type="text/javascript" src="../js/mousewheel.js"></script>
    <script type="text/javascript" src="../js/jquery.jb.shortscroll.js"></script>
    <!-- Script Pantalla Anular Orden -->
    <script type="text/javascript" src="../js/jquery.countdown360.js"></script>

    <script type="text/javascript" src="../js/ajax_anularOrden.js"></script>
	<script type="text/javascript" src="../js/ajax_pagoTarjetaDinamico.js"></script>																			 
    <!-- Scripts para keyboard credenciales -->
    <script type="text/javascript" src="../js/teclado.js"></script>
    <!-- JavaScript Cambio datos cliente  -->
    <script type="text/javascript" src="../js/ajax_cambioDatosCliente.js"></script>
    <!-- Javascript para notificacioon a Trade -->
    <script type="text/javascript" src="../js/ajax_trade.js"></script>

    <script type="text/javascript" src="../js/CalendarPicker.js"></script>
       <!-- Javascript api para impresion -->
    <script type="text/javascript" src="../js/ajax_api_impresion.js"></script>
    <script>var modulo = 'TRANSACCIONES';</script>
    <script type="text/javascript" src="../js/ajax_pagoServicioTarjeta.js"></script>
    <script>
        $(document).ready(function() {
            $('#keyboard').css({
                top : '410px !important',
                left: '154px  !important'
            }); 

            $("#btn_ok_teclado").on("mouseover", function () {
                $(this).focus();
            });
        });
    </script>    
    <!-- <script src="../css/sweetAleter1.css"></script>
    <script src="../css/sweetAleter2.css"></script> -->
    <link rel="stylesheet" type="text/css" href="../css/sweetAleter1.css" />
    <link rel="stylesheet" type="text/css" href="../css/sweetAleter2.css" />

    <script type="text/javascript" src="../js/toastify-js.js"></script>

    </body>
</html>