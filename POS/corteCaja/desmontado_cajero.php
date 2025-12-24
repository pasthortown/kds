<?php
session_start();

include_once '../system/conexion/clase_sql.php';
include_once "../clases/clase_desmontadoCajero.php";
$lc_apertura = new desmontaCaja();
$estado_unificacion_transferencia_de_venta=$lc_apertura->fn_unificacion_transferencia_de_venta($_SESSION ['rstId']);

///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR CHRISTIAN PINTO////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE DESMONTADO DE CAJERO///////////////////
////////////////TABLAS: ARQUEO_CAJA,BILLETE_ESTACION,//////////////////////
////////////////////////CONTROL_ESTACION,ESTACION//////////////////////////
////////////////////////BILLETE_DENOMINACION///////////////////////////////
////////FECHA CREACION: 08/09/2015/////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
$bandera = $_SESSION["sesionbandera"];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Desmontado de Cajero</title> 
        <!-- Scripts para scroll-->
        <link rel="stylesheet" href="../css/jquery.jb.shortscroll.css" />
        <link rel="stylesheet" type="text/css" href="../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../css/teclado_billetes.css" /> 
        <link rel="stylesheet" type="text/css" href="../css/est_modal.css" />
        <link rel="stylesheet" href="../css/style_index.css" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="../css/style_home_seleccion.css"/>
        <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="../css/teclado_cortecaja.css"/>
        <link rel="stylesheet" type="text/css" href="../css/movimientos.css"/>
        <link rel="stylesheet" type="text/css" href="../css/media_desmontado_cajero.css"/>
    </head>
    <body>
        <input inputmode="none"  type="hidden" id="bandera" value="<?php echo $bandera; ?>"/>
        <input inputmode="none"  type="hidden" name="IDControlEstacion" id="IDControlEstacion"  value="<?php echo $_SESSION['IDControlEstacion']; ?>"/>
        <input inputmode="none"  type="hidden" name="unificacion_transferencia_de_venta" id="unificacion_transferencia_de_venta"  value="<?php echo $estado_unificacion_transferencia_de_venta; ?>"/>
        <?php
        $estacionId=$_SESSION['estacionId'];
        ?>
        <div class="modal fade" id="modal_formaPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="width:1065px">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title" >DESASIGNAR CAJERO</h4>
                    </div> 
                    <div class="modal-body">
                        <div id="div_formaPago" style="height:388px; width:1025px; ">
                            <table class="table table-bordered table-hover" id="formaPago" border="1" cellpadding="1" cellspacing="0"></table>
                        </div>

                        <div id="div_tpie" style="width:1025px; ">
                            <table class="table table-bordered table-hover" width="900px" id="tpie" border="1" cellpadding="1" cellspacing="0">
                                <tr align="right" class="success">
                                    <td class="txt_total" style="vertical-align:middle;  height: 65px; width: 125px;" align="center" ><b>Totales>>></b></td>
                                    <td class="total" style="vertical-align:middle; width: 119px" align="center"></td>
                                    <td class="total" style="vertical-align:middle; width: 119px" align="center"></td>
                                    <td class="total" style="vertical-align:middle; width: 119px" align="center"></td>
                                    <td class="total" style="vertical-align:middle; width: 120px" align="center"></td>
                                    <td class="total" style="vertical-align:middle; width: 123px" align="center"></td>
                                    <td class="total" style="vertical-align:middle; width: 120px" align="center"></td>
                                    <td class="total" style="vertical-align:middle; width: 113px" align="center"></td>                                    
                                </tr> 
                            </table>
                        </div>
                        <div class="">
                            <div align="center">  
                                <div class="row">	
                                    <div class="col-xs-3">
                                        <button type="button" id="btn_agregarFormaPago" class="btn btn-success" style='height:70px;width:180px;' onclick="fn_modal_credencialesAdmin();" >Agregar <br /> Forma de Pago</button>
                                    </div>
                                    <div class="col-xs-6">
                                        <button type="button" id="btn_okgeneral" class="btn btn-primary " style='height:70px;width:100px;' onclick="fn_desmontarCajeroArqueo();" value="OK" >OK</button>  
                                        <button type="button" class="btn btn-default" id="btn_cancelargeneral" style='height:70px;width:100px;' onclick="fn_eliminaFormasPagoAgregadas();" data-dismiss="modal" value="Cancelar">Cancelar</button>
                                    </div>

                                </div>  
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_agregarFormaPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" >
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title">Agregar Forma de Pago</h4>
                    </div> 
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-6 col-md-offset-1">
                                <div class="form-group col-xs-1">
                                    <select id="sel_formasPago" style="height:60px; width:480px; font-size:30px" class="form-control" ></select> 
                                </div>
                            </div>
                            <div class="col-xs-1"></div>
                        </div>
                        <br /><br /><br />
                        <div class="">
                            <div align="center">
                                <button type="button" id="btn_ok_FormasPago" class="btn btn-primary " style='height:70px;width:100px;' onclick="fn_agregarFormaPago();">OK</button>
                                <button type="button" class="btn btn-default" id="btn_cancelar_FormasPago" style='height:70px;width:100px;' onclick="" data-dismiss="modal" value="Cancelar">Cancelar</button>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_formaPagoEfectivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="width:1065px">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title" id="titulomodalRetiros"></h4>
                    </div> 
                    <div class="modal-body"  >  
                        <div id="div_formaPagoEfectivo" style="height:388px; width:1025px; ">
                            <table class="table table-bordered table-hover" id="formaPagoEfectivo" border="1" cellpadding="1" cellspacing="0"></table>
                        </div>                       
                        <div style="width:1025px">
                            <table class="table table-bordered table-hover" width="900px" id="tpieEfectivo" border="1" cellpadding="1" cellspacing="0">
                                <tr align="right" class="success">
                                    <td class="txt_total" style="vertical-align:middle; height: 65px" align="center" ><b>Totales>>></b></td>
                                    <td class="total" style="vertical-align:middle" align="center"></td>
                                    <td class="total" style="vertical-align:middle" align="center"></td>
                                    <td class="total" style="vertical-align:middle" align="center"></td>
                                    <td class="total" style="vertical-align:middle" align="center"></td>
                                    <td class="total" id="totalArqueoTransacciones" style="vertical-align:middle; width: 120px" align="center"></td>
                                    <td class="total" style="vertical-align:middle" align="center"></td>
                                    <td class="total" style="vertical-align:middle" align="center"></td>
                                    <td class="total" style="vertical-align:middle" align="center"></td>
                                </tr> 
                            </table>
                        </div>

                        <div class="">
                            <div class="row">
                                <div align="center">
                                    <div id ="botonCCL" class="col-xs-3 pull-left">    
                                       <button type="button" id="btn_cajachicalocal" class="btn btn-warning" style='height:70px;width:180px;' data-toggle="modal" onclick="fn_modal_cajaChicaLocal();" >Caja Chica <br /> Tienda</button>  
                                    </div>   
                                    <div class="col-xs-6">
                                        <button type="button" id="btn_okEfectivo" class="btn btn-primary " style='height:70px;width:100px;' onclick="fn_ejecutaBotonOkEfectivo();" value="OK" >OK</button>
                                        <button type="button" class="btn btn-default" id="btn_cancelarEfectivo" style='height:70px;width:100px;' onclick="fn_eliminarBilletesPendiente();" data-dismiss="modal" value="Cancelar">Cancelar</button>
                                    </div> 
                                    <div id ="div_FondoCaja" class="col-xs-3 pull-right">    
                                       <button type="button" id="btn_FondoCaja" class="btn btn-warning" style='height:70px;width:180px; display: none ' data-toggle="modal" ></button>  
                                    </div>   
                                </div>

                            </div>
                            <br />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal_cuadreTarjetas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="width:965px">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title" id="titulomodalCuadreTarjetas"></h4>
                    </div> 
                    <div class="modal-body">       
                        <table class="table table-bordered table-hover" id="cuadreTarjetas" border="1" cellpadding="1" cellspacing="0"></table>                   

                        <div class="">
                            <div align="center">
                                <button type="button" id="btn_okCuadreTarjetas" class="btn btn-primary " style='height:70px;width:100px;' onclick="fn_cerrarModalCuadreTarjetas();" value="OK" >OK</button>
                                <button type="button" class="btn btn-default" id="btn_cancelarEfectivo" style='height:70px;width:100px;' onclick="" data-dismiss="modal" value="Cancelar">Cancelar</button>
                            </div>    
                        </div>
                        <br />
                    </div>
                </div>
            </div>
        </div>       

        <div class="col-md-8 col-md-offset-2 " id="login">
            <div>
                <div>
                    <p class="nombremax" style="text-align:center">Max<span class="nombrepoint">Point</span></p>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class=" form-group col-md-10 col-md-offset-1">
                        <button class="btn btn-lg btn-primary btn-block iniciarsesion" style="height:80px" id="btn_retiroEfectivo" onclick="fn_validarCuentasAbiertasRetiroEfectivo(1);" >RETIROS</button>
                    </div>                      
                </div>

                <div class="row">
                    <div class=" form-group col-md-10 col-md-offset-1">
                        <button class="btn btn-lg btn-primary btn-block iniciarsesion" style="height:80px" id="btn_arqueoRetiroEfectivo" onclick="fn_validarCuentasAbiertasRetiroEfectivo(3);" >ARQUEO</button>
                    </div>                      
                </div>

                <div class="row">
                    <div class=" form-group col-md-10 col-md-offset-1">
                        <button class="btn btn-lg btn-primary btn-block iniciarsesion" style="height:80px" id="btn_impresionCorteX" onclick="fn_imprime_CorteX();" >CORTE EN X</button>
                    </div>                      
                </div>
                <?php if($estado_unificacion_transferencia_de_venta==0){ ?>
                <div class="row " id="contBtnTransferencia">

                </div> 
                <?php } ?> 
                <div class="row ">
                    <div class=" form-group col-md-10 col-md-offset-1">
                        <button class="btn btn-lg btn-primary btn-block iniciarsesion" style="height:80px" id="btn_corteCaja" >DESASIGNAR CAJERO</button>
                    </div>                      
                </div> 
                <div class="row">   
                    <div class=" form-group col-md-10 col-md-offset-1">
                        <button class="btn btn-lg btn-danger btn-block iniciarsesion" onclick="fn_desmontadoDirecto();" style="height:80px" id="btn_desasignarDirecto">DESASIGNAR CAJERO</button>
                    </div>                      
                </div> 

                <div class="row">   
                    <div class=" form-group col-md-10 col-md-offset-1">
                        <button class="btn btn-lg btn-danger btn-block iniciarsesion" onclick="" style="height:80px" id="btn_cancelarInicio">CANCELAR</button>
                    </div>                      
                </div>    
            </div>
        </div>

        <div class="modal fade" id="ModalTarjetas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" style="width:400px">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title" id="titulomodalTarjeta"></h4>
                    </div> 
                    <div class="modal-body">       
                        <div id="dialogTarjetas" style="width:100%"></div>
                        <br />

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ModalTecladoNuevaFormaPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" style="width:400px">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title" id="tituloModalTecladoNuevaFormaPago"></h4>
                    </div> 
                    <div class="modal-body">       
                        <div id="tecladoNuevaFormaPago" style="width:100%"></div>
                        <br />
                    </div>
                </div>
            </div>
        </div>

        <!--<div id="dialogTarjetas" title="Corte de Caja" style="width:100%"></div>-->

        <div class="modal fade" id="ModalBilletesDesmontarCajero" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" style=" padding-bottom:10px">
            <div class="modal-dialog modal-lg" style="width:965px">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title" id="titulomodalNuevo">BILLETES </h4>
                    </div> 
                    <div class="modal-body">
                        <div id="div_billetes" style="height:370px; width:920px; ">
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
                        <div align="center" style="width:920px">
                            <button type="button" id="ok" class="btn btn-primary " style='height:70px;width:100px;' onclick="fn_guardaTotalesBilletes();" value="OK" >OK</button>
                            <button type="button" class="btn btn-default" id="cancelar" style='height:70px;width:100px;' onclick="fn_cerrarCalculadora();" data-dismiss="modal" value="Cancelar">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--<div id="dialog2"  title="Corte en Z - Billetes">
            <div id=div_billetes style="height:460px; width:920px; ">
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
            
                    <div align="center" style="width:920px">
                  <button type="button" id="ok" class="btn btn-primary " style='height:70px;width:100px;' onclick="fn_guardaTotalesBilletes();" value="OK" >OK</button>
                   <button type="button" class="btn btn-default" id="cancelar" style='height:70px;width:100px;' onclick="" data-dismiss="modal" value="Cancelar">Cancelar</button>
                </div>    
            
             
            </div>-->

        <div class="modal fade" id="ModalBilletesEfectivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="width:965px">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title" id="titulomodalRetiroBilletes">BILLETES </h4>
                    </div> 
                    <div class="modal-body">
                        <div id="div_billetesEfectivo" style="height:400px; width:920px; ">
                            <table class="table table-bordered table-hover" id="billetesEfectivo" border="1" cellpadding="1" cellspacing="0"></table>
                        </div> 
                        <table id="tpie_EfectivoBilletes" border="1" class="table table-bordered table-hover" cellpadding="1" cellspacing="0" style="width:920px;">                                           
                            <tr align="right" class="success">
                                <td style="width:540px; vertical-align:middle" align="center"><b>Total>>></b></td>
                                <td align="center" style="width:220px; text-align:center"></td>
                            </tr> 

                            <tr align="right" class="danger">
                                <td style="width:540px; vertical-align:middle" align="center"><b>Diferencia>>></b></td>
                                <td align="center" id="td_masomenosRetiros" style="width:220px; text-align:center; vertical-align:middle"></td>
                            </tr>
                            <tr align="right" class="success">
                                <td style="width:540px; vertical-align:middle" align="center"><b>Retiros Previos Efectivo>>></b></td>
                                <td align="center" style="width:220px; text-align:center; vertical-align:middle"></td>
                            </tr>
                            <tr align="right" class="success">
                                <td style="width:540px; vertical-align:middle" align="center"><b>POS calculado>>></b></td>
                                <td align="center" style="width:220px; text-align:center; vertical-align:middle" id="totalPosEfectivo"></td>
                            </tr>                                                
                        </table>

                        <div align="center" style="width:920px">
                            <button type="button" id="ok_BilletesEfectivo" class="btn btn-primary " style='height:70px;width:100px;' onclick="fn_guardarTotalBilletesEfectivo();" value="OK" >OK</button>
                            <button type="button" class="btn btn-default" id="cancelarEfectivo" style='height:70px;width:100px;' onclick="" data-dismiss="modal">Cancelar</button>
                        </div>
                        <br />
                    </div>
                </div>
            </div>
        </div>  

        <div class="modal fade" id="ModalMotivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title" id="titulomodalNuevo">Ingrese el Motivo del descuadre de valores </h4>
                    </div> 
                    <div class="modal-body">
                        <table align="center">
                            <tr>
                                <td>
                                    <textarea inputmode='none' style="width:825px; height:80px; text-transform:uppercase; resize:none; font-size: 36px" onclick="" class="form-control" id="txtArea"></textarea>
                                </td>
                            </tr>
                        </table>               
                        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                        <table style="position:inherit; z-index: 3;">
                            <tr>
                                <td>
                                    <div id="motivos_descuadre1" style="position:inherit; z-index: 3;"></div>
                                </td>
                            </tr>
                        </table>
                        
                        <div style="position:inherit; z-index: 2;" align="center">
                            <button type="button" id="btn_okmotivo" class="btn btn-primary " style='height:80px;width:110px;' onclick="" value="OK" >OK</button>
                            <button type="button" class="btn btn-default" id="btn_cmotivo" style='height:80px;width:110px;' onclick="" data-dismiss="modal" value="Cancelar">Cancelar</button>
                        </div>
                        <br />
                        <div id="keyboard" style="position:absolute; z-index: 1;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="Modal_credencialesAdmin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" style="width:450px">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h4 class="modal-title" >Ingrese las Credenciales del Administrador </h4>
                    </div> 
                    <div class="modal-body">
                        <div id="credencialesAdmin">
                            <div class="anulacionesSeparador">
                                <div class="anulacionesInput"><input inputmode="none"  type="password" id="usr_claveAdmin" style="height: 35px; width: 420px; font-size: 16px;"/>				
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-10">
                                    <table id="tabla_credencialesAdmin">
                                        <tr>
                                            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 7)">7</button></td>
                                            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 8)">8</button></td>
                                            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 9)">9</button></td>
                                            <td><button style="font-size:45px;" class='btnVirtualOKpq' onclick="fn_eliminarNumero(usr_claveAdmin);">&larr;</button></td>
                                        </tr>
                                        <tr>
                                            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 4)">4</button></td>
                                            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 5)">5</button></td>
                                            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 6)">6</button></td>
                                            <td><button style="font-size:45px;" class='btnVirtualOKpq' onclick="fn_eliminarTodo(usr_claveAdmin);">&lArr;</button></td>
                                        </tr>
                                        <tr>
                                            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 1)">1</button></td>
                                            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 2)">2</button></td>
                                            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 3)">3</button></td>
                                            <td><button style="font-size:45px;" class='btnVirtualOKpq' onclick="fn_validaAdmin();">OK</button></td>
                                        </tr>
                                        <tr>
                                            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(usr_claveAdmin, 0)">0</button></td>
                                            <td colspan="4"><button style="font-size:45px;" class='btnVirtualCancelarAdmin' onclick="fn_cerrarValidaAdmin();">Cancelar</button></td>           
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br />
                    </div>
                </div>
            </div>
        </div> 

        <div class="modal fade" id="ModalTransferenciaVenta" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h2 class="modal-title" >Transferencia de Venta</h2>
                    </div> 
                    <div class="modal-body">
                        <div style="font-size:24px" id="mensajeModalTransferenciaVenta">
                        </div>
                        <div style="font-size:45px;text-align:center;padding-top:20px;padding-bottom:20px">$<span id="totalTransferenciaVenta"></span></div>
                        <hr />
                        <div align="center">
                            <button id="btnAceptarTransferenciaVenta" type="button" class="btn btn-primary " style="height:70px;width:100px;" value="OK" >OK</button>
                            <button id="btnCancelarTransferenciaVenta" type="button" class="btn btn-default"  style="height:70px; width:100px; margin-left: 90px;" data-dismiss="modal" value="Cancelar">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ModalCajaChicaLocal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" style="width: 850px;">
                <div class="modal-content">
                    <div class="modal-header btn-primary">
                        <h2 class="modal-title" >Caja Chica Local</h2>
                    </div> 
                    <div class="modal-body">   
                        <div class="cntSelectMovimiento">
                            <label>Movimiento: </label>
                            <select id="tipoMovimiento"></select>
                        </div>
                        <hr></hr>
                        <div class="cntFiltros">
                            <label for="finalFecha" class="labelPadding">Hasta: </label>
                            <input inputmode="none"  type="text" id="finalFecha" value="" disabled/>
                        </div>

                        <div class="cntEntradas">
                            <label for="totalMovimiento">Total: </label>
                            <input inputmode="none"  type="text" id="totalMovimiento" value=""/>
                        </div>

                        <div class="cntMensaje">
                            <label>Mensaje: </label>
                            <span id="msnInformacion"></span>
                        </div>

                        <div class="cntBotones">
                            <input inputmode="none"  type="button" id="btnEnviar1" class="boton" value="Crear Movimiento"/>
                            <input inputmode="none"  type="button" id="btnConfirmar1" class="botonBloqueado" value="Confirmar Total" disabled="disabled"/>
                            <input inputmode="none"  type="button" class="boton" data-dismiss="modal" value="Cancelar"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ModalCajerosTransferenciaVenta" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header btn-primary">
                <h3 class="modal-title">Transferencia de Venta - Listado de Cajeros</h3>
              </div> 
              <div class="modal-body">
                <div style="font-size:24px" id="mensajeModalCajerosTransferenciaVenta">
                </div>
                <div style="font-size:11px;text-align:center;" id="listadoModalCajerosTransferenciaVenta"></div>
                <!--<div style="font-size:45px;text-align:center;padding-top:20px;padding-bottom:20px"></div>-->
                <hr />
                <div align="center">
                  <button id="btnCancelarCajerosTransferenciaVenta" type="button" class="btn btn-default" style="height:50px; width:100px; margin-left:0px;" data-dismiss="modal" value="Cancelar">Cancelar</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <input inputmode="none"  type="hidden" id="hid_controlEfectivo"/>
        <input inputmode="none"  type="hidden" id="hid_estacion"/>
        <input inputmode="none"  type="hidden" id="hid_usuario"/>
        <input inputmode="none"  type="hidden" id="hid_controlMesa"/>
        <input inputmode="none"  type="hidden" id="hid_controlCuenta"/>
        <input inputmode="none"  type="hidden" id="hid_controlEstacion"/>
        <input inputmode="none"  type="hidden" id="hid_usuarioDescripcion"/>
        <input inputmode="none"  type="hidden" id="hid_controlDiferencia"/>
        <input inputmode="none"  type="hidden" id="hid_descuadre"/>
        <input inputmode="none"  type="hidden" id="hid_restaurante"/>
        <input inputmode="none"  type="hidden" id="hide_totalBilletesEfectivo"/>
        <input inputmode="none"  type="hidden" id="hide_totalPosEfectivo"/>
        <input inputmode="none"  type="hidden" id="valorEfectivoTotal"/>
        <input inputmode="none"  type="hidden" id="hide_totalBilletes"/>
        <input inputmode="none"  type="hidden" id="hid_usuario_efectivo"/>
        <input inputmode="none"  type="hidden" id="hid_formaPago"/>
        <input inputmode="none"  type="hidden" id="array"/>
        <input inputmode="none"  type="hidden" id="hid_diferencia"/>
        <input inputmode="none"  type="hidden" id="hid_totalNuevo" />
        <input inputmode="none"  type="hidden" id="hid_masomenos"/>
        <input inputmode="none"  type="hidden" id="retiroEfectivoModalBilletes"/>
        <input inputmode="none"  type="hidden" id="totalPos"/>
        <input inputmode="none"  type="hidden" id="valorsumabilletes"/>
        <input inputmode="none"  type="hidden" id="valormasomenos"/>
        <input inputmode="none"  type="hidden" id="totalPosCalculado"/>
        <input inputmode="none"  type="hidden" id="diferenciaTotales"/>
        <input inputmode="none"  type="hidden" id="hid_controlRetiroEfectivo"/>
        <input inputmode="none"  type="hidden" id="totalNuevoEfectivo" />
        <input inputmode="none"  type="hidden" id="id_formaPago" />
        <input inputmode="none"  type="hidden" id="hide_fmp_descripcion" />
        <input inputmode="none"  type="hidden" id="hide_fmp_descripcion_val" />
        <input inputmode="none"  type="hidden" id="totalRetirado" />
        <input inputmode="none"  type="hidden" id="transacciones" />
        <input inputmode="none"  type="hidden" id="transaccionesIngresadas" />
        <input inputmode="none"  type="hidden" id="posCalculadoValor" />
        <input inputmode="none"  type="hidden" id="diferencia" />
        <input inputmode="none"  type="hidden" id="formaPagoDescripcion" />
        <input inputmode="none"  type="hidden" id="descipcionTarjetas" />    
        <input inputmode="none"  type="hidden" id="cedulaCajero" />  
        <input inputmode="none"  type="hidden" id="fecha" />  
        <!-- interface ger (paramentro)-->
        <input inputmode="none"  id="IDPeriodo" type="hidden" value="<?php echo $_SESSION['IDPeriodo']; ?>"/>
        <!-- interface ger (modal)-->
        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../imagenes/loading.gif"/>
            </div>
        </div>


        <script type="text/javascript" src="../js/jquery.js"></script> 
        <script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.js"></script>
        <!-- Scripts para scroll-->
        <script type="text/javascript" src="../js/ajax_movimientos.js"></script>
        <script type="text/javascript" src="../js/idioma.js"></script>
        <script type="text/javascript" src="../js/mousewheel.js"></script>
        <script type="text/javascript" src="../js/jquery.jb.shortscroll.js"></script>
        <script type="text/javascript" src="../js/teclado_coretecaja.js"></script>
        <script type="text/javascript" src="../js/ajax_desmontadoCajero.js?v=050417"></script>
        <script type="text/javascript" src="../js/js_validaciones.js"></script>
        <script type="text/javascript" src="../js/alertify.js"></script>  
        <script type="text/javascript" src="../bootstrap/js/bootstrap.js"></script>
        <script	type="text/javascript" src="../js/teclado_billetes.js"></script>
        <!--js para interface-->
        <script language="javascript" type="text/javascript" src="../js/ajax_cliente_interface.js"></script>       
        <script type="text/javascript" language="javascript" src="../js/ajax_telegram.js"></script> 
        <script type="text/javascript" src="../js/ajax_api_impresion.js"></script>
        <!--js para status version -->
        <script language="javascript" type="text/javascript" src="../js/cnd/jsdelivr/net/npm/sweetalert2@11.js"></script>
        <script language="javascript" type="text/javascript" src="../js/ajax_statusVersion.js"></script>


    </body>
</html>