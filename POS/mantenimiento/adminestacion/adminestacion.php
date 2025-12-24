<?php
session_start();
include_once '../../seguridades/seguridad.inc';

//////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////
///////////DESCRIPCION: NUEVOS ESTILOS INGRESO Y ACTUALIZACION DE ESTACION CON////
/////////////////////// TABLA MODAL //////////////////////////////////////////////
////////////////TABLAS: Estacion,SWT_Tipo_Envio///////////////////////////////////
////////FECHA CREACION: 01/06/2015////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Estacion</title>
        <!-- LIBRERIAS -->
        <!---------------------------------------------------
        ESTILOS
----------------------------------------------------->
        <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
        <!--<link rel="stylesheet" href="../../css/est_pantallas.css" type="text/css"/>-->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
    </head>
    <body>
        <div class="superior">
            <div class="menu" style="width: 400px; align: center; margin-top: 5px;">
                <ul>
                    <li>
                        <input inputmode="none"  id="btn_agregar" type="button" onclick="fn_accionar('Nuevo')" class="botonhabilitado" value="Agregar"/>
                    </li>
                    <!-- <li>
                       <input inputmode="none"  id="btn_cancelar" type="button" onclick="fn_accionar('Cancelar')" class="botonhabilitado" value="Cancelar"/>
                     </li>-->
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>ESTACIÓN</h1>
            </div>
        </div>        
        
        <br/>
        <div class="contenedor" >
            <div class="inferior" align="center" >
                <div class="panel panel-default text-left">
                    <div class="panel-body">
                        <tr>
                            <td width="150">Seleccionar Restaurante: </td>
                            <td>
                                <select id="selrest" class="form-control" ></select>
                            </td>
                        </tr>    
                    </div>
                </div>
                <div class="panel panel-default" id="botonesActivosInactivos">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-3"><h5>Lista de Estaciones</h5>
                                <div id="opciones_estados" class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default btn-sm active" onclick="fn_OpcionSeleccionada('Activos');"><input inputmode="none"  id="opt_Activos" type="radio" value="Activos" checked="checked" autocomplete="off" name="estados" />Activos</label>
                                    <label class="btn btn-default btn-sm" onclick="fn_OpcionSeleccionada('Inactivos');"><input inputmode="none"  id="opt_Inactivos" type="radio" value="Inactivos" autocomplete="off" name="estados" />Inactivos</label>
                                    <label class="btn btn-default btn-sm" onclick="fn_OpcionSeleccionada('Todos');"><input inputmode="none"  id="opt_Todos" type="radio" value="Todos" autocomplete="off" name="estados" />Todos</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tabla_estacion">
                    <table class="table table-bordered table-hover" id="detalle_estacion" border="1" cellpadding="1" cellspacing="0"></table>
                </div>
            </div>
        </div>
        <!-------------------------------------INICIO MODAL NUEVO BOOTSTRAP---------------------------------------------->
        <div class="modal fade" id="ModalNuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulomodalNuevo">Ingreso de Nueva Estación: </b><!--<input inputmode="none"  style="width:50px; text-align:center; border:hidden" disabled="disabled" id="txt_estacionsuperior" onkeypress="return fn_numeros(event);"/></b>--></h4>
                    </div>
                    <br/>
                    <div align="right" class="col-xs-12 col-x"> Está Activo?
                        <input inputmode="none"  type="checkbox" id="option" checked="checked" />
                    </div>
                    <br/>
                    <div class="modal-body">
                        <div role="tabpanel">
                            <!-- Nav tabs -->
                            <ul id="pestanas" class="nav nav-tabs" role="tablist">
                                <li role="presentation" id="inicio" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">
                                        <h5>Estación</h5>
                                    </a>
                                </li>                                                                
                            </ul>
                            <div id="pst_cnt" class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="home"> <br/>
                                    <br/>        
                                    <!---------------------------------- CONTENEDOR ADMINISTRACION NUEVA ESTACION ------------------------------------->
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Restaurante:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group" class="col-xs-1">  
                                                <input inputmode="none"  style="text-align:center" class="form-control" id="txt_rstNuevo" readonly="readonly" />
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Ingrese IP:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-xs-3">
                                                        <input inputmode="none"  id="ip1" type="text" class="form-control text-center" maxlength="3" onKeyPress="return fn_numeros(event);" />
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <input inputmode="none"  id="ip2" type="text" class="form-control text-center" maxlength="3" onKeyPress="return fn_numeros(event);" />
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <input inputmode="none"  id="ip3" type="text" class="form-control text-center"  maxlength="3" onKeyPress="return fn_numeros(event);" />
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <input inputmode="none"  id="ip4" type="text" class="form-control text-center" maxlength="3" onKeyPress="return fn_numeros(event);" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Nombre Estación:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <div class="input-prepend input-group number-spinner">
                                                    <span class="add-on input-group-addon btn btn-primary" data-dir="dwn"><i class="glyphicon glyphicon-minus fa fa-calendar"></i></span>
                                                    <input inputmode="none"  style="text-align:center" readonly="readonly" class="form-control text-center" id="txt_estacion" onkeypress="return fn_numeros(event);" value="0" min="0" />
                                                    <span class="add-on input-group-addon btn btn-primary" data-dir="up"><i class="glyphicon glyphicon-plus fa fa-calendar"></i></span>
                                                </div>
                                                <input inputmode="none"  style="text-transform:uppercase;" readonly="readonly" value="CAJA" id="txt_nombre" type="hidden"/>
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Seleccione Menú:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select data-placeholder="Seleccione Menú" multiple="multiple" id="selmenu" class="chosen-select" style="text-transform:uppercase;"></select>                    
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Medios Autorizadores:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <!--<select id="selTipoCobro" class="form-control" style="text-transform:uppercase;">-->
                                                <select data-placeholder="Seleccione Medio Autorizador" multiple="multiple" id="selTipoCobro" class="chosen-select" style="text-transform:uppercase;"></select>                                                                
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>TID(Tarjeta de Credito):</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <input inputmode="none"  type="text" rel="tooltip" style="text-transform:uppercase;" placeholder="Ingrese TID" maxlength="8" class="form-control text-center" id="txt_tid" />
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Desasignar en:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select id="selDesasignarEstacion" class="form-control" style="text-transform:uppercase;"></select>                   
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>

                                    <!--<div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Tipo Envio:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select id="selTipoEnvio" class="form-control" style="text-transform:uppercase;">
                                                </select>                   
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>-->

                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Punto Emisión:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <input inputmode="none"  type="text" rel="tooltip" style="text-transform:uppercase;" placeholder="Ingrese Punto de Emisión" maxlength="3" class="form-control" id="txt_puntoEmision" />                 
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Pago Predeterminado:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select id="selPagoPredeterminado" class="form-control" style="text-transform:uppercase;"></select> 
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div id="divNuevoSeleccionarMesa" class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Seleccione Mesa:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select id="selNuevoMesa" class="form-control" style="text-transform:uppercase;"></select> 
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane" id="settings" > <br/>
                                    <!-- <div class="panel panel-default">-->
                                    <!-- <div class="panel-heading"> --> 
                                    <br/>

                                    <div class="row">

                                        <div class="col-md-2" style="width:auto"></div>
                                        <div class="col-md-11">
                                            <div class="form-group" id="canalesImpresion">
                                                <div style="height: 50px; width:800px;">
                                                    <div id="CabeceralistaCanalesImpresion" class="list-group ">

                                                    </div>
                                                </div>

                                                <div style="height: 300px; width:800px; overflow-y: auto;">
                                                    <div id="listaCanalesImpresion" class="list-group">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--<div class="col-md-2"></div>-->     
                                    </div>

                                    <!--</div>-->
                                    <!--</div>-->
                                </div>                                
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer panel-footer" id="botonesguardarcancelar">
                        <button type="button" class="btn btn-primary" id='botonGuardarEstacion' onclick="fn_accionar('Grabar');" >Aceptar</button>
                        <button type="button" class="btn btn-default" onclick="" data-dismiss="modal">Cancelar</button>

                    </div>
                    <div class="modal-footer panel-footer" id="botonessalir">
                        <button type="button" class="btn btn-primary" id='botonGuardarEstacion' data-dismiss="modal">Aceptar</button>
                        <button type="button" class="btn btn-default" onclick="" data-dismiss="modal" style="width:10%">Salir</button>
                    </div>

                </div>
            </div>
        </div>
        <!-------------------------------------FIN MODAL NUEVO BOOTSTRAP---------------------------------------------->

        <!---------------------------------INICIO MODAL MODIFICAR BOOTSTRAP------------------------------------------->
        <div class="modal fade" id="ModalModificar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" style="width: 1000px;">
                <div class="modal-content">
                    <div class="modal-header panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulomodalModificar">Modificar Estación: </b><!--<input inputmode="none"  style="width:50px; text-align:center; border:hidden" disabled="disabled" id="txt_estacionsuperior" onkeypress="return fn_numeros(event);"/></b>--></h4>
                    </div>
                    <br/>

                    <div align="right" class="col-xs-12 col-x"> Está Activo?
                        <input inputmode="none"  type="checkbox" id="optionmod">
                    </div>

                    <br/>
                    <div class="modal-body">       

                        <div role="tabpanel">
                            <!-- Nav tabs -->
                            <ul id="pestanasMod" class="nav nav-tabs" role="tablist">
                                <li role="presentation" id="uno" class="active"><a href="#tabEstacionMod" aria-controls="tabEstacionMod" role="tab" data-toggle="tab">
                                        <h5>Estación</h5>
                                    </a></li>
                                <li role="presentation" id="dos" ><a href="#tabConfiguracionImpresoraMod" aria-controls="tabConfiguracionImpresoraMod" role="tab" data-toggle="tab">
                                        <h5>Impresión</h5>
                                    </a></li>
                                <li role="presentation" id="dos" ><a href="#tabConfiguracionPoliticas" aria-controls="tabConfiguracionPoliticas" role="tab" data-toggle="tab">
                                        <h5>Políticas de configuración</h5>
                                    </a></li>
                            </ul>
                            <div id="TabContentMod" class="tab-content">

                                <div role="tabpanel" class="tab-pane active" id="tabEstacionMod"> <br/>
                                    <br/>        
                                    <!---------------------------------- CONTENEDOR ADMINISTRACION MODIFICAR ESTACION ------------------------------------->
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Restaurante:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group" class="col-xs-1">
                                                <input inputmode="none"  style="text-align:center" class="form-control" id="txt_rstModifica" readonly="readonly" />                      
                                                <input inputmode="none"  type="hidden" name="mod_magid" id="mod_magid"/>
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Ingrese IP:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-xs-3">
                                                        <input inputmode="none"  id="ipM1" type="text" class="form-control text-center"  maxlength="3" onKeyPress="return fn_numeros(event);" />
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <input inputmode="none"  id="ipM2" type="text" class="form-control text-center"  maxlength="3" onKeyPress="return fn_numeros(event);" />
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <input inputmode="none"  id="ipM3" type="text" class="form-control text-center"  maxlength="3" onKeyPress="return fn_numeros(event);" />
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <input inputmode="none"  id="ipM4" type="text" class="form-control text-center" maxlength="3" onKeyPress="return fn_numeros(event);" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Nombre Estación:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <div class="input-prepend input-group number-spinner">
                                                    <span class="add-on input-group-addon btn btn-primary" data-dir="dwn"><i class="glyphicon glyphicon-minus fa fa-calendar"></i></span>
                                                    <input inputmode="none"  style="text-align:center" readonly="readonly" class="form-control text-center" id="txt_estacionmod" onkeypress="return fn_numeros(event);" value="0" min="0"/> </b>
                                                    <span class="add-on input-group-addon btn btn-primary" data-dir="up"><i class="glyphicon glyphicon-plus fa fa-calendar"></i></span>
                                                </div>      
                                                <input inputmode="none"  style="text-transform:uppercase;" readonly="readonly" value="CAJA" id="txt_nombre" type="hidden"/>
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Seleccione Menu:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">

                                                <select data-placeholder="Seleccione Menú" multiple="multiple" id="selmenumodifica" class="chosen-select" style="text-transform:uppercase;"></select>                     
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Medios Autorizadores:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <!--<select id="selTipoCobroMod" class="form-control" style="text-transform:uppercase;">-->
                                                <select data-placeholder="Seleccione Medio Autorizador;" multiple="multiple" id="selTipoCobroMod" class="chosen-select" style="text-transform:uppercase;"></select>     

                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>TID(Tarjeta de Credito):</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <input inputmode="none"  style="text-transform:uppercase;" maxlength="8" class="form-control" id="txt_tidMod"/>       

                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Desasignar en:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select id="selDesasignarEstacionMod" class="form-control" style="text-transform:uppercase;">
                                                </select>                   
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>

                                    <!--<div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Tipo Envio:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select id="selTipoEnvioMod" class="form-control" style="text-transform:uppercase;">
                                                </select>                   
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>-->

                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Punto Emisión:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <input inputmode="none"  type="text" rel="tooltip" style="text-transform:uppercase;" placeholder="Ingrese Punto de Emisión" maxlength="3" class="form-control" id="txt_puntoEmisionMod"/>                 
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Pago Predeterminado:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select id="selPagoPredeterminadoM" class="form-control" style="text-transform:uppercase;">
                                                </select> 
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div id="divModificarSeleccionarMesa" class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Seleccione Mesa:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select id="selModificarMesa" class="form-control" style="text-transform:uppercase;"></select> 
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                </div>

                                <div role="tabpanel" class="tab-pane" id="tabConfiguracionImpresoraMod"> <br/>
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-2" style="width:auto"></div>
                                        <div class="col-md-1">
                                            <div class="form-group" id="canalesImpresionMod">
                                                <div style="height: 50px; width:800px;">
                                                    <div id="CabeceralistaCanalesImpresionMod" class="list-group ">
                                                    </div>
                                                </div>
                                                <div style="height: 300px; width:800px; overflow-y: auto; ">                                       
                                                    <div id="listaCanalesImpresionMod" class="list-group "></div>
                                                </div>
                                            </div>
                                        </div>    
                                    </div>
                                </div> 
                                <div role="tabpanel" class="tab-pane" id="tabConfiguracionPoliticas">    
                                    <br/>
                                    <div class="row">
                                        <div class="col-xs-12 col-md-12">
                                            <!-- DIV QUE CONTIENE LOS BOTONES DE MAS Y MENOS PARA AGREGAR Y MODIFICAR -->                                           
                                            <div>
                                                <button type="button" class="close col-xs-1" onclick="fn_accionarPoliticas('Modificar', 1)" ><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                <button type="button" class="close" onclick="fn_accionarPoliticas('Nuevo', 1)" ><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
                                            </div>                                                                                                                                            
                                            <br/><br/><br/>
                                            <div class="form-group" id="div_detalle_estacion_coleccion">
                                                <table id="tbl_estacion_coleccion" class="table table-bordered" style="font-size:10px;"></table>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer panel-footer" id="botonesguardarcancelarMod">
                        <button type="button" class="btn btn-primary" onclick="fn_accionar('Grabar');" >Aceptar</button>
                        <button type="button" class="btn btn-default" onclick="" data-dismiss="modal">Cancelar</button>  
                    </div>
                </div>
            </div>
        </div>

        <!--Inicio de modal de coleccion -->
        <div class="modal fade" id="mdl_nuevaColeccion" tabindex="-1" role="dialog" data-backdrop="static" style="height: 600px">
            <div class="modal-dialog" style="width: 1000px;">
                <div class="modal-content">
                    <div class="modal-header">                  
                        <h4 class="modal-title">Colección: <label id="lblNombreColeccion"></label></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <br/> 
                            <div class="col-xs-12 col-md-12">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group" id="detalle_restaurante_coleccion" style="height: 100px; overflow: auto;">
                                        <table id="listaColecciones" class="table table-bordered" style="font-size: 11px;"></table>                                
                                    </div> 
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group" id="_detalle_restaurante_coleccion" style="height: 100px; overflow: auto;">
                                        <table id="lista_datos" class="table table-bordered" style="font-size: 11px;"></table>                                
                                    </div> 
                                </div>
                            </div>
                        </div>                    
                        <div id="div_caracteristicas">
                            <div class="row text-center">
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Especifica Valor: <input inputmode="none"  disabled="disabled" type="checkbox" value="1" id="check_especifica" /></h5>
                                    </div>
                                </div>        
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Obligatorio: <input inputmode="none"  disabled="disabled" type="checkbox" value="1" id="check_obligatorio" /></h5>
                                    </div>
                                </div>       
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Tipo de dato: <label value="1" id="lbl_tipoDato"></label></h5>
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <div class="row">                                                                
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Caracter:</b></h6></div>
                                <div class="col-xs-12 col-md-4">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_caracter" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>                              																			
                            </div>
                            <div class="row">                                                                
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Entero:</b></h6></div>
                                <div class="col-xs-12 col-md-4">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_entero" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>                              																			
                            </div>
                            <div class="row">                                                                
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Fecha:</b></h6></div>
                                <div class="col-xs-12 col-md-4 form-group">
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_fechaSImple" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>                              																			
                            </div>
                            <div class="row"> 
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Selección:</b></h6></div>
                                <div class="col-xs-12 col-md-4 form-group">
                                    <div class="input-prepend input-group">                                        
                                        <input inputmode="none"  type="checkbox" id="sel_seleccione" data-off-text="No" data-on-text="Si" />  
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>                                                                                                                   	                                                                                                            
                            </div>
                            <div class="row">                                                                
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Numerico:</b></h6></div>
                                <div class="col-xs-12 col-md-4 form-group">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_numerico" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>                              																			
                            </div>
                            <div class="row">                                                                
                                <div class="col-md-1"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Fecha Inicio:</b></h6></div>
                                <div class="col-xs-12 col-md-3 form-group">
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_fechaInicio" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>     
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Fecha Fin:</b></h6></div>
                                <div class="col-xs-12 col-md-3 form-group">
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_fechaFin" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>                                     
                            </div>
                            <div class="row">                                                                
                                <div class="col-md-1"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Mínimo:</b></h6></div>
                                <div class="col-xs-12 col-md-3">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control" id="txt_minimo" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>     
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Máximo:</b></h6></div>
                                <div class="col-xs-12 col-md-3">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control" id="txt_maximo" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>                                     
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="fn_guardarColeccion();">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mdl_editaColeccion" tabindex="-1" role="dialog" data-backdrop="static">
            <div class="modal-dialog" style="width: 1050px;">
                <div class="modal-content">
                    <div class="modal-header">                  
                        <h4 class="modal-title">Colección: <label id="lblNombreColeccionModificar"></label></h4>
                    </div>
                    <div class="modal-body">                                   
                        <div id="div_caracteristicasM">
                            <div class="row">
                                <div class="col-xs-12 col-x text-right"><h6>Está Activo?: <input inputmode="none"  type="checkbox" value="" id="check_activo" enabled=""></h6></div>
                            </div>
                            <div class="row text-center">                                                                
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Especifica Valor: <input inputmode="none"  disabled="disabled" type="checkbox" value="1" id="check_especificaM"></h5>
                                    </div>
                                </div>        
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Obligatorio: <input inputmode="none"  disabled="disabled" type="checkbox" value="1" id="check_obligatorioM"></h5>
                                    </div>
                                </div>       
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Tipo de dato: <label value="1" id="lbl_tipoDatoM"></label></h5>
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <div class="row">                                                                
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Caracter:</b></h6></div>
                                <div class="col-xs-12 col-md-4">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_caracterM" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>                              																			
                            </div>
                            <div class="row">                                                                
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Entero:</b></h6></div>
                                <div class="col-xs-12 col-md-4">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_enteroM" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>                              																			
                            </div>
                            <div class="row">                                                                
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Fecha:</b></h6></div>
                                <div class="col-xs-12 col-md-4 form-group">
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_fechaSImpleM" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>                              																			
                            </div>
                            <div class="row"> 
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Selección:</b></h6></div>
                                <div class="col-xs-12 col-md-4 form-group">
                                    <div class="input-prepend input-group">                                        
                                        <input inputmode="none"  type="checkbox" id="sel_seleccioneM" data-off-text="No" data-on-text="Si" />  
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>                                                                                                                   	                                                                                                            
                            </div>
                            <div class="row">                                                                
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Numerico:</b></h6></div>
                                <div class="col-xs-12 col-md-4 form-group">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_numericoM" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>                              																			
                            </div>
                            <div class="row">                                                                
                                <div class="col-md-1"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Fecha Inicio:</b></h6></div>
                                <div class="col-xs-12 col-md-3 form-group">
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_fechaInicioM" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>     
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Fecha Fin:</b></h6></div>
                                <div class="col-xs-12 col-md-3 form-group">
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_fechaFinM" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>                                     
                            </div>
                            <div class="row">                                                                
                                <div class="col-md-1"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Mínimo:</b></h6></div>
                                <div class="col-xs-12 col-md-3">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_minimoM" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>     
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Máximo:</b></h6></div>
                                <div class="col-xs-12 col-md-3">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_maximoM" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>                                     
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="fn_actualizaColeccion();">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-------------------------------------FIN MODAL MODIFICAR BOOTSTRAP---------------------------------------------->
        <input inputmode="none"  type="hidden" id="hid_ip"/>
        <input inputmode="none"  type="hidden" id="cod_estacion"/>
        <input inputmode="none"  type="hidden" id="cadena"/>
        <input inputmode="none"  type="hidden" id="descripcionCadena"/>
        <input inputmode="none"  type="hidden" id="descripcionRestaurante"/>
        <input inputmode="none"  type="hidden" id="descripcionMenu"/>
        <input inputmode="none"  type="hidden" id="nombreMenu"/>
        <input inputmode="none"  type="hidden" id="idMenu"/>
        <input inputmode="none"  type="hidden" id="idTipoEnvio"/>
        <input inputmode="none"  type="hidden" id="idimpresora"/>
        <input inputmode="none"  type="hidden" id="idestacionnueva"/>
        <input inputmode="none"  type="hidden" id="desasigna"/>
        <input inputmode="none"  type="hidden" id="idTipoEnvioMod"/>


        <script src="../../js/jquery1.11.1.js"></script>
        <script	type="text/javascript" src="../../js/jquery-ui.js"></script>
        <script language="javascript1.1"  src="../../js/alertify.js"></script>


        <script language="javascript1.1" type="text/javascript" src="../../js/idioma.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/js_validaciones.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/ajax_datatables.js"></script>

        <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-tooltip.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/inputnumber.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/switch.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.order.jquery.min.js"></script>            
        <script language="javascript" type="text/javascript" src="../../js/ajax_adminestacion.js"></script>

    </body>
</html>