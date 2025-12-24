<?php
session_start();
////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////
///////////DESCRIPCION: DESMONTAR CAJERO ///////////////////////////////////////////
////////////////TABLAS: Control_Estacion, Periodo, Estacion/////////////////////////
////////FECHA CREACION: 13/10/2015//////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR       : Christian Pinto //////////////////////////////////////
///////DESCRIPCION          : Remediación para mejorar la Mantenibilidad en el /////
////////////////////////////  código ///////////////////////////////////////////////
///////FECHA MODIFICACIÓN   : 17/10/2016 ///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_menu.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
        <title>Desmontar Cajero</title>

        <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
        <link rel="stylesheet" href="../../css/jquery-confirm.css" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" href="../../css/jquery.jb.shortscroll.css" />
        <link rel="stylesheet" type="text/css" href="../../css/teclado_cortecaja.css"/>
    </head>

    <body>
        <input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
        <div class="superior">
            <div class="menu" style="width: 500px;" align="center">
                <ul>
                    <li>
                        <input inputmode="none"  id="btn_agregar" type="button"  onclick="fn_accionar('Nuevo')" class="botonhabilitado" value="Agregar"/>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>DESMONTAR CAJERO</h1>
            </div>
        </div>
        <br/>

        <div class="contenedor1">
            <div class="inferior">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <center>
                            <div id="div_tabla_usuarios" class="panel-title"  >
                                <table class="table table-bordered " id="tabla_usuarios"></table>
                            </div>
                            <div class="row" style="height: 350px; width:1160px; ">
                                <div id="div_fechas" class="col-xs-2" style="height: 360px; overflow-y: auto;">
                                    <table id="tabla_fechas"></table>
                                </div>
                                <div align="left" style="width:960px; height:360px; overflow-y: auto;" >
                                    <table id = 'tabla_estado_usuarios' ></table>
                                </div>
                            </div>
                        </center>
                    </div>
                    <div class="panel-footer"></div>
                </div>
            </div>
        </div>
        <!--Inicio div Inactivo-->
        <!-------------------------------------INICIO MODAL NUEVO BOOTSTRAP---------------------------------------------->
        <div class="modal fade" id="ModalFormasPagoInactivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg ">
                <div class="modal-content cp">
                    <div class="modal-header panel-footer">                      
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulomodalNuevo"></h4>  
                        <!--<h5 class="modal-title" style="color: #000"><label>DF es "Data Fast"</label></h5>-->
                    </div>
                    <div class="modal-body"> 
                        <div id=div_formaPago style="height:250px; width:845px; ">
                            <table class="table table-bordered " id="formaPago" border="1" cellpadding="1" cellspacing="0"></table>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div id=div_formaPago_totales style="width:845px; ">
                            <table class="table table-bordered " id="formaPagoTotales" border="1" cellpadding="1" cellspacing="0"></table>
                        </div>
                    </div>  
                    <div>
                        <h5 class="modal-title" style="color: #000">
                            <label>&nbsp;&nbsp;&nbsp;&nbsp; Nota: Las formas de pago tarjetas con DF, fueron aplicadas por "Data Fast".</label>
                        </h5>
                    </div>
                    <div class="modal-footer panel-footer" id="pie_formaPagoInactivo"></div>
                </div>
            </div>
        </div>
        <!-------------------------------------FIN MODAL MODIFICAR BOOTSTRAP---------------------------------------------->
        <!--Fint div Inactivo-->

        <!-------------------------------------INICIO MODAL NUEVO BOOTSTRAP---------------------------------------------->
        <div class="modal fade" id="ModalFormasPagoActivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg ">
                <div class="modal-content">
                    <div class="modal-header panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulomodalNuevoActivo"></h4>
                    </div>
                    <br/>
                    <div class="modal-body">
                        <div id="div_formaPagoActivo" style="height:250px; width:845px; ">
                            <table class="table table-bordered " id="formaPagoActivo" border="1" cellpadding="1" cellspacing="0"></table>
                        </div>
                    </div>
                    <div>
                        <h5 class="modal-title" style="color: #000"><label>&nbsp;&nbsp;&nbsp;&nbsp; Nota: Las formas de pago tarjetas con DF, fueron aplicadas por "Data Fast".</label></h5>
                    </div>
                    <div class="modal-footer panel-footer" >
                        <div align="center">
                            <div class="row" id="pie_formaPagoActivo"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-------------------------------------FIN MODAL MODIFICAR BOOTSTRAP---------------------------------------------->

        <!-------------------------------------INICIO MODAL POR CERRAR---------------------------------------------->
        <div class="modal fade" id="modal_formaPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="width:965px">
                <div class="modal-content cp">
                    <div class="modal-header panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulomodalformapagoPorCerrar"></h4>
                    </div> 
                    <div class="modal-body"> 
                        <div id=div_formaPagoPorCerrar style="height:340px; width:924px;  overflow-y: auto;">
                            <table class="table table-bordered table-hover" id="tabla_formaPagoPorCerrar" border="1" cellpadding="1" cellspacing="0"></table>
                        </div>
                        <div id=div_tpie style="width:924px; ">
                            <table class="table table-bordered table-hover" width="900px" id="tpie" border="1" cellpadding="1" cellspacing="0">
                                <tr align="right" class="success">
                                    <td class="txt_total" width="205px" style="vertical-align:middle" align="center" ><b>Totales>>></b></td>
                                    <td class="total" width="170px" style="vertical-align:middle" align="center"></td>
                                    <td class="total" width="185px" style="vertical-align:middle" align="center"></td>
                                    <td class="total" width="183px" style="vertical-align:middle" align="center"></td>
                                    <td width="170px" class="total" style="vertical-align:middle" align="center"></td>
                                    <td width="185px" class="total" style="vertical-align:middle"  align="center"></td>
                                </tr> 
                            </table>
                        </div>
                    </div>  
                    <div class="modal-footer panel-footer">
                        <div align="center">
                            <div class="row">	
                                <div class="col-xs-3">
                                    <button type="button" id="btn_agregarFormaPago" class="btn btn-success" onclick="fn_consultaFormaPagoNueva();" ><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Agregar Forma de Pago</button>
                                </div>
                                <div class="col-xs-6">
                                    <button type="button" class="btn btn-primary" id="btn_okgeneral" onclick="fn_validaMontoDescuadreCajero();">Aceptar</button>
                                    <button type="button" class="btn btn-default" id="btn_cancelargeneral" data-dismiss="modal" onclick="fn_btnCancelarFormasPago();">Cancelar</button>
                                </div>
                            </div>
                        </div>    
                    </div>
                </div>
            </div>
        </div>
        <!-------------------------------------FIN MODAL POR CERRAR---------------------------------------------->

        <!-------------------------------------INICIO MODAL TARJETAS DATAFAST---------------------------------------------->
        <div class="modal fade" id="modal_tarjetasDatafast" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="width:965px">
                <div class="modal-content">
                    <div class="modal-header panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulomodaltarjetasDatafast">TARJETAS DATAFAST</h4>
                    </div> 
                    <div class="modal-body">  
                        <div id=div_tarjetasDatafast style="height:400px; width:924px;  overflow-y: auto;">
                            <table class="table table-bordered table-hover" id="tabla_tarjetasDatafast" border="1" cellpadding="1" cellspacing="0"></table>
                        </div>
                    </div> 
                    <div class="modal-footer panel-footer">
                        <div align="center">
                            <div class="row">	
                                <div class="col-xs-12">
                                    <button type="button" class="btn btn-primary" id="btn_okgeneral" onclick="fn_btnAceptarTarjetasDatafast();">Aceptar</button>
                                    <button type="button" class="btn btn-default" id="btn_cancelargeneral" data-dismiss="modal" onclick="fn_eliminaFormasPagoDatafast();">Cancelar</button>
                                </div>
                            </div>
                        </div>    
                    </div>
                </div>
            </div>
        </div>
        <!-------------------------------------FIN MODAL MODAL TARJETAS DATAFAST---------------------------------------------->

        <!-------------------------------------MODAL BILLETES---------------------------------------------->
        <div class="modal fade" id="ModalBilletesDesmontarCajero" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" style=" padding-bottom:10px">
            <div class="modal-dialog modal-lg" style="width:965px">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title" id="titulomodalNuevo">Billetes </h4>
                    </div> 
                    <div class="modal-body">
                        <div id=div_billetes style="height:300px; width:920px; overflow-y:auto; ">
                            <table class="table table-bordered table-hover" id="billetes" border="1" cellpadding="1" cellspacing="0"></table>
                        </div> 
                        <table id="tpie3" border="1" class="table table-bordered table-hover" cellpadding="1" cellspacing="0" style="width:920px;">                                           
                            <tr align="right" class="success">
                                <td style="width:440px; vertical-align:middle" align="center"><b>Total Billetes>>></b></td>
                                <td align="center" style="width:220px; text-align:center"></td>
                            </tr> 
                            <tr id="tr_masomenos" align="right" class="danger">
                                <td style="width:540px; vertical-align:middle" align="center"><b>Mas o Menos>>></b></td>
                                <td align="center" id="td_masomenos" style="width:220px; text-align:center; vertical-align:middle"></td>
                            </tr>
                            <tr align="right" class="success">
                                <td style="width:540px; vertical-align:middle" align="center"><b>Retiros Previos Efectivo>>></b></td>
                                <td align="center" style="width:220px; text-align:center; vertical-align:middle"></td>
                            </tr>
                            <tr align="right" class="success">
                                <td style="width:540px; vertical-align:middle" align="center"><b>POS calculado>>></b></td>
                                <td align="center" style="width:220px; text-align:center; vertical-align:middle"></td>
                            </tr>                                                
                        </table>   
                    </div>  
                    <div class="modal-footer panel-footer">
                        <button type="button"  class="btn btn-primary" id="ok" onclick="fn_guardaTotalesBilletes();">Aceptar</button>
                        <button type="button" class="btn btn-default" id="" value="Cancelar" data-dismiss="modal" onclick="">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-------------------------------------FIN MODAL BILLETES---------------------------------------------->

        <!--MODAL TARJETAS-->
        <div class="modal fade" id="ModalTarjetas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" style="width:400px">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title" id="titulomodalTarjetas"></h4>
                    </div> 
                    <div class="modal-body">       
                        <div id="dialogTarjetas" style="width:100%"></div>
                        <br/>
                    </div>
                </div>
            </div>
        </div>
        <!--FIN MODAL TARJETAS-->

        <!-------------------------------------INICIO MODAL REPORTE FIN DE DIA---------------------------------------------->
        <div class="modal fade" id="ModalReporteFinDeDia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog " style="width: 300px">
                <div class="modal-content">
                    <div class="modal-header panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulomodalReporteFinDeDia">FIN DEL D&Iacute;A</h4>
                    </div>
                    <br/>
                    <div id="div_reporteFinDeDia"></div>
                    <div class="modal-footer panel-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Salir</button>
                        <!--                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>-->
                    </div>
                </div>
            </div>
        </div>
        <!-------------------------------------FIN MODAL REPORTE FIN DE DIA---------------------------------------------->

        <div class="modal fade" id="ModalMotivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" style="">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title" id="titulomodalNuevo">Ingrese el Motivo del descuadre de valores </h4>
                    </div> 
                    <div class="modal-body"> 
                        <table align="center">
                            <tr>
                                <td>
                                    <textarea style="width:560px; height:80px; text-transform:uppercase; resize:none; font-size: 25px" onclick="" class="form-control" id="txtArea"></textarea>
                                </td>
                            </tr>
                        </table>               
                        <br/>
                        <table style="position:inherit; z-index: 3;">
                            <tr>
                                <td>
                                    <div id="motivos_descuadre1" style="position:inherit; z-index: 3;"></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer panel-footer">
                        <button type="button"  class="btn btn-primary " id="btn_okmotivo" onclick="fn_actualizaCajeroMotivoDescuadre()">Aceptar</button>
                        <button type="button" class="btn btn-default" id="btn_cmotivo" value="Cancelar" data-dismiss="modal" onclick="fn_modalDegradado('normal')">Cancelar</button>
                    </div>   
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_agregarFormaPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" style="width:400px" >
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title" id="titulomodalNuevo">Agregar Forma de Pago</h4>
                    </div> 
                    <div class="modal-body"> 
                        <div class="row">
                            <div class="col-xs-10 col-md-offset-1">
                                <div class="form-group" class="col-xs-1">
                                    <select id="sel_formasPago" class="form-control" ></select> 
                                </div>
                            </div>
                            <div class="col-xs-1"></div>
                        </div>
                        <br/>
                        <br/>
                        <br/>
                    </div>
                    <div align="center" class="modal-footer panel-footer">
                        <button type="button" id="btn_ok_FormasPago" class="btn btn-primary " onclick="fn_agregarFormaPago();">Aceptar</button>
                        <button type="button" class="btn btn-default" id="btn_cancelar_FormasPago" onclick="fn_cerrarModalAgregaFormaPago()" data-dismiss="modal" value="Cancelar">Cancelar</button>
                    </div>    
                </div>
            </div>
        </div>

        <!--MODAL TARJETAS-->
        <div class="modal fade" id="ModalTecladoNuevaFormaPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" style="width:330px">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title" id="tituloModalTecladoNuevaFormaPago"></h4>
                    </div> 
                    <div class="modal-body">       
                        <div id="tecladoNuevaFormaPago" style="width:100%"></div>
                        <br/>
                    </div>
                </div>
            </div>
        </div>
        <!--FIN MODAL TARJETAS-->


        <div id="cargando" class="overlayCargando" style="display: none;">
            <div id="cargandoimg" class="modalCargando" style="display: none;">
                <img src="../../imagenes/cargando.gif"/>
            </div>
        </div>

        <input inputmode="none"  id="hide_fecha"  type="hidden" value="" />
        <input inputmode="none"  id="elimina" type="hidden" values="" />
        <input inputmode="none"  type="hidden" id="hid_controlEfectivo" />
        <input inputmode="none"  type="hidden" id="hid_estacion" />
        <input inputmode="none"  type="hidden" id="hid_usuario" />
        <input inputmode="none"  type="hidden" id="hid_controlMesa" />
        <input inputmode="none"  type="hidden" id="hid_controlCuenta" />
        <input inputmode="none"  type="hidden" id="hid_controlEstacion" />
        <input inputmode="none"  type="hidden" id="hid_usuarioDescripcion" />
        <input inputmode="none"  type="hidden" id="hid_controlDiferencia" />
        <input inputmode="none"  type="hidden" id="hid_descuadre" />
        <input inputmode="none"  type="hidden" id="hid_restaurante" />
        <input inputmode="none"  type="hidden" id="hide_totalBilletesEfectivo" />
        <input inputmode="none"  type="hidden" id="hide_totalPosEfectivo" />
        <input inputmode="none"  type="hidden" id="valorEfectivoTotal" />
        <input inputmode="none"  type="hidden" id="hide_totalBilletes" />
        <input inputmode="none"  type="hidden" id="hid_usuario_efectivo" />
        <input inputmode="none"  type="hidden" id="hid_formaPago" />
        <input inputmode="none"  type="hidden" id="array" />
        <input inputmode="none"  type="hidden" id="hid_diferencia" />
        <input inputmode="none"  type="hidden" id="hid_totalNuevo" />
        <input inputmode="none"  type="hidden" id="hid_masomenos" />
        <input inputmode="none"  type="hidden" id="retiroEfectivoModalBilletes" />
        <input inputmode="none"  type="hidden" id="totalPos" />
        <input inputmode="none"  type="hidden" id="valorsumabilletes" />
        <input inputmode="none"  type="hidden" id="valormasomenos" />
        <input inputmode="none"  type="hidden" id="totalPosCalculado" />
        <input inputmode="none"  type="hidden" id="diferenciaTotales" />
        <input inputmode="none"  type="hidden" id="hid_controlRetiroEfectivo" />
        <input inputmode="none"  type="hidden" id="totalNuevoEfectivo" />
        <input inputmode="none"  type="hidden" id="hid_usr_id_cajero" />
        <input inputmode="none"  type="hidden" id="hide_periodo" />
        <input inputmode="none"  type="hidden" id="hide_nombre" />
        <input inputmode="none"  type="hidden" id="id_formaPago" />
        <input inputmode="none"  type="hidden" id="hide_fmp_descripcion" />
        <input inputmode="none"  type="hidden" id="hide_fmp_descripcion_val" />
        <input inputmode="none"  type="hidden" id="hide_num_cupones" />
        <input inputmode="none"  type="hidden" id="retiroValor" />
        <input inputmode="none"  type="hidden" id="transacciones" /> 
        <input inputmode="none"  type="hidden" id="posCalculado" /> 
        <input inputmode="none"  type="hidden" id="diferencia" />
        <input inputmode="none"  type="hidden" id="estadoSwt" />    
        <!-- interface ger (paramentro)-->
        <input inputmode="none"  type="hidden" id="IDPeriodo" value="" />

        <!------- ARCHIVOS JS------------>
        <script src="../../js/jquery1.11.1.js"></script>
        <script src="../../js/jquery-confirm.js"></script>
        <script src="../../js/ajax_datatables.js"></script>
        <script src="../../js/bootstrap_nofocus.js"></script>
        <script src="../../js/alertify.js"></script>
        <script src="../../js/js_validaciones.js"></script>
        <script src="../../js/teclado_coretecaja.js"></script>
        <script src="../../js/ajax_adminDesmontarCajero.js"></script>
        <script src="../../js/jquery.numeric.js" type="text/javascript"></script>
        <script type="text/javascript" language="javascript" src="../../js/ajax_telegram.js"></script> 
    </body> 
</html>