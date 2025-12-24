<?php
session_start();

////////////////////////////////////////////////////////////////////////////////////////////
////////MODIFICADO POR: CHRISTIAN PINTO/////////////////////////////////////////////////////
////////DECRIPCION: NUEVOS ESTILOS INGRESO Y ACTUALIZACION FORMAS DE PAGO///////////////////
///////////////////////////////// TABLA MODAL, COLECCION DE DATOS ATRIBUTO FORMA PAGO///////
////////TABLAS INVOLUCRADAS: Formapago,Cadena///////////////////////////////////////////////
////////FECHA CREACION: 09/06/2015//////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_seguridades.php';
include_once '../../clases/clase_menu.php';
include_once '../../seguridades/seguridad.inc';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">    
    <head>
        <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Administraci&oacute;n</title>
        <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/select2.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
        <link rel="stylesheet" type="text/css" href="../../css/select2.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
    </head>
    <body>
        <input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>" />
        <input inputmode="none"  id="cadenas" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>" />
        <div class="superior">
            <div class="menu" style="width: 500px;" align="center">
                <ul>
                    <li>
                        <input inputmode="none"  id="btn_agregar" type="button"  onclick="fn_agregarF(); fn_agregar(); fn_agregarMonedaSimbolo(); fn_agregarClienteFormasPago();" class="botonhabilitado" value="Agregar"/>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>FORMAS DE PAGO</h1>
            </div>
        </div>
        <br/>            
        <!-------------------------------------------CONTENEDOR FORMAS DE PAGO --------------------------------------------------->
        <div class="contenedor"> 
            <div class="inferior" align="center" >
                <br/>
                <div id="prb_img" class="row" style="display: none;">
                    <div class="col-sm-6">
                        <canvas height="300px" width="300px" id="micanvas"></canvas>
                    </div>
                    <div class="col-sm-6">
                        <textarea id="txt_area_imagen"></textarea>
                    </div>
                </div>

                <!--------------------------------INICIO TAV------------------------------->
                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul id="pestanas" class="nav nav-tabs" role="tablist">
                        <li role="presentation" id="inicio" onclick="fn_activaTavFormaPago();" class="active"><a href="#formapago" aria-controls="formapago" role="tab" data-toggle="tab"><h5>Formas de Pago</h5></a></li>
                        <li role="presentation" id="fin" onclick="fn_activaTavDenominacionBillete();"><a href="#billetes" aria-controls="billetes" role="tab" data-toggle="tab"><h5>Denominaci&oacute;n de Billetes</h5></a></li>
                        <li role="presentation" onclick="fn_activaTavMonedaSimbolo();"><a href="#monedasimbolo" aria-controls="monedasimbolo" role="tab" data-toggle="tab"><h5>S&iacute;mbolo Moneda</h5></a></li>

                    </ul>

                    <div id="pst_cnt" class="tab-content">
                        <!--------------TAV FORMAS DE PAGO------------>
                        <div role="tabpanel" class="tab-pane fade in active center-block" id="formapago"> 
                            <div class="panel panel-default" id="botonesTodos">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-sm-3"><h5><b>Lista de Formas de Pago</b></h5>
                                            <div id="opciones_estados" class="btn-group" data-toggle="buttons">
                                                <label class="btn btn-default btn-sm active" onclick="fn_OpcionSeleccionada('Activos');"><input inputmode="none"  id="opt_Activos" type="radio" value="Activos" checked="checked" autocomplete="off" name="estados">Activos</label>
                                                <label class="btn btn-default btn-sm" onclick="fn_OpcionSeleccionada('Inactivos');"><input inputmode="none"  id="opt_Inactivos" type="radio" value="Inactivos" autocomplete="off" name="estados">Inactivos</label>
                                                <label class="btn btn-default btn-sm " onclick="fn_OpcionSeleccionada('Todos');"><input inputmode="none"  id="opt_Todos" type="radio" value="Todos" autocomplete="off" name="estados">Todos</label>
                                            </div>
                                        </div> 
                                    </div>
                                </div>                         
                            </div>
                            <div id="tabla_formas_pago">
                                <table class="table table-bordered table-hover" id="formas_pago" border="1" cellpadding="1" cellspacing="0">
                                </table>
                            </div>
                        </div>
                        <!--FIN TAV FORMAS DE PAGO-->
                        <!--------------TAV DENOMINACION BILLETES------------>
                        <div role="tabpanel" class="tab-pane fade" id="billetes">
                            <div class="panel panel-default" id="botonesTodosBilletes">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-sm-3"><h5><b>Lista Denominaci&oacute;n de Billetes</b></h5>
                                            <div id="opciones_estados" class="btn-group" data-toggle="buttons">
                                                <label class="btn btn-default btn-sm active" onclick="fn_OpcionSeleccionadab('Activos');"><input inputmode="none"  id="opt_Activosb" type="radio" checked="checked" value="Activos" autocomplete="off" name="estadosb">Activos</label>
                                                <label class="btn btn-default btn-sm" onclick="fn_OpcionSeleccionadab('Inactivos');"><input inputmode="none"  id="opt_Inactivosb" type="radio" value="Inactivos" autocomplete="off" name="estadosb">Inactivos</label>
                                                <label class="btn btn-default btn-sm" onclick="fn_OpcionSeleccionadab('Todos');"><input inputmode="none"  id="opt_Todosb" type="radio" value="Todos" autocomplete="off" name="estadosb">Todos</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="denominacion_billetes">
                                <table class="table table-bordered table-hover" id="tabla_denominacion_billetes" border="1" cellpadding="1" cellspacing="0"></table>
                            </div>
                        </div>
                        <!--------------FIN TAV DENOMINACION BILLETES------------>
                        <!--------------TAV SIMBOLO MONEDA------------>
                        <div role="tabpanel" class="tab-pane fade" id="monedasimbolo">
                            <div class="panel panel-default" id="botonesTodosMoneda">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-sm-3"><h5><b>Lista S&iacute;mbolo Moneda</b></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="divtabla_simbolo">
                                <table class="table table-bordered table-hover" id="tabla_simbolo" border="1" cellpadding="1" cellspacing="0"></table>
                            </div>
                        </div>
                        <!--------------FIN TAV SIMBOLO MONEDA------------>
                    </div>
                </div>
            </div>
        </div>
        <!--FIN CONTENEDOR FORMAS DE PAGO -->
        <!----------------------------------------- MODAL DENOMINACION BILLETES ----------------------------------------------->
        <!-- Modal Modificar -->
        <div class="modal fade " data-backdrop="static" id="modalmodificarb" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="fn_activaBotonAgregar();"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" id="check_activo"/></h6></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><h5>Descripci&oacute;n:</h5></div>
                            <div class="col-xs-9">
                                <div class="form-group">
                                    <input inputmode="none"  size="30" type="text" id="btd_des" class="form-control"  maxlength="15" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><h5>Valor <span id="simbolo"></span>:</h5></div>
                            <div class="col-xs-9">
                                <div class="form-group">
                                    <input inputmode="none"  size="30" type="text" id="btd_val" class="form-control" onkeypress="return NumCheck(event, this);"  maxlength="6"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><h5>Tipo:</h5></div>
                            <div class="col-xs-9">
                                <div class="form-group">
                                    <select id="btd_tipo" class="form-control">
                                        <option value="BILLETE">BILLETE</option>
                                        <option value="MONEDA">MONEDA</option>
                                    </select>                        
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="fn_guardardenominacionbilletes();" >Aceptar</button>
                        <button type="button" class="btn btn-default" onclick="fn_activaBotonAgregar();" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Nuevo -->
        <div class="modal fade"  data-backdrop="static" id="modalnuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="fn_activaBotonAgregar();"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Nueva Denominaci&oacute;n:</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" id="check_activonuevo" checked="checked" disabled /></h6></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><h5>Descripci&oacute;n:</h5></div>
                            <div class="col-xs-9">
                                <div class="form-group">
                                    <input inputmode="none"  size="30" type="text" id="btd_desnuevo" class="form-control"  maxlength="15"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><h5>Valor $:</h5></div>
                            <div class="col-xs-9">
                                <div class="form-group">
                                    <input inputmode="none"  size="30" type="text" id="btd_valnuevo" class="form-control" onkeypress="return NumCheck(event, this);"  maxlength="8" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3"><h5>Tipo:</h5></div>
                            <div class="col-xs-9">
                                <div class="form-group">
                                    <select id="btd_tiponuevo" class="form-control">
                                        <option value="BILLETE">BILLETE</option>
                                        <option value="MONEDA">MONEDA</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="fn_guardardenominacionbilletes()" >Aceptar</button>
                        <button type="button" class="btn btn-default" onclick="fn_activaBotonAgregar();" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <!----------------------------------------- FIN MODAL DENOMINACION BILLETES ------------------------------------------->
        <!----------------------------------------- MODAL ADMINISTRACION FORMAS DE PAGO --------------------------------------->
        <div class="modal fade" id="ModalModificar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" style="width:1100px;">
                <div class="modal-content">
                    <div class="modal-header panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="fn_eliminarConfiguracionesNull();"><span aria-hidden="true">&times;</span></button>
                        <div class="row">
                            <div class="col-xs-5">
                                <h4 class="modal-title" id="titulomodal"><b></b></h4>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <!--------------------------------------INICIO NAV TABS---------------------------------------------->
                        <div role="tabpanel">
                            <!-- Nav tabs -->
                            <ul id="pestanas" class="nav nav-tabs" role="tablist">
                                <li role="presentation" id="inicio" class="active">
                                    <a href="#home" aria-controls="home" role="tab" data-toggle="tab">
                                        <h5>Formas de Pago</h5>
                                    </a>
                                </li>
                                <li role="presentation" id="fin">
                                    <a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">
                                        <h5>Tiendas Aplicar</h5>
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#clientesAplicar" aria-controls="clientesAplicar" role="tab" data-toggle="tab">
                                        <h5>Clientes Aplicar</h5>
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#ColeccionFP" aria-controls="ColeccionFP" role="tab" data-toggle="tab">
                                        <h5>Politicas de Configuraci&oacute;n</h5>
                                    </a>
                                </li>
                            </ul>
                            <div id="pst_cnt" class="tab-content">
                                <div role="tabpanel" class="tab-pane fade in active center-block" id="home"> <br/>
                                    <div align="right" class="col-xs-12 col-x"> Est&aacute; Activo?
                                        <input inputmode="none"  type="checkbox" id="option" checked="checked" />
                                    </div>
                                    <br/>
                                    <br/>
                                    <!-- CONTENEDOR ADMINISTRACION FORMAS DE PAGO -->
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-4">
                                            <h4 class="modal-title" id="imagentitulomodalModificar"></h4>
                                        </div>
                                        <div class="col-xs-3 text-right">
                                            <div class="form-group">
                                                <h5>Nivel de Seguridad:</h5>
                                            </div>
                                        </div>
                                        <div class="col-xs-3"></div>
                                        <div class="col-xs-3">
                                            <div class="form-group">
                                                <select id="select_perfil" class="form-control" style="width:234px"></select>
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Descripci&oacute;n:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <input inputmode="none"  class="form-control" type="text" id="fmp_descripcion" onkeyup="aMays(event, this)" onblur="aMays(event, this)" maxlength="50" style="width:108%"/>
                                                <input inputmode="none"  type="hidden" name="mod_magid" id="mod_magid"/>
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Tipo Medios de Pago:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select id="tfp_tipo" class="form-control" style="width:108%"></select>
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Adquiriente:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select id="rda_id" class="form-control" style="width:108%"></select>
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Codigo Respuesta DLL Gerente:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select id="slcDLL" class="form-control" style="width:108%" ></select>
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Tipo de Facturaci&oacute;n:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select id="sel_tipo_facturacion" class="form-control" style="width:108%"></select>
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row" id="contenedorconfiguraciones">
                                    </div>
                                    <div class="row" id = "div_urlImprimeTicket">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>URL Imprime Ticket:</h5>
                                        </div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <input inputmode="none"  class="form-control" type="text" id="url_imprimeTicket" style="width:108%" />
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row" id="divImagen">
                                        <div class="col-xs-1"></div>
                                        <div class="col-xs-3">
                                            <h5>Cargar Imagen:</h5>
                                        </div>
                                        <div class="col-xs-5">
                                            <div class="form-group">
                                                <input inputmode="none"  id="fileimagen" type="file" class="filestyle" data-buttonName="btn-primary" data-buttonBefore="true" data-buttonText="Imagen" onchange="fn_cargarImagen();">
                                            </div>
                                        </div>
                                        <div class="col-xs-3">
                                            <div class="form-group">
                                                <button id="verimagen" type="button" class="btn btn-default" aria-label="Left Align" onclick="fn_obtenerImagen();">
                                                    Visualizar
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                    <div class="row" id="divImagenNuevo">
                                        <div class="col-xs-1"></div>   
                                        <div class="col-xs-3">
                                            <h5>Cargar Imagen:</h5>
                                        </div>
                                        <div class="col-xs-5">
                                            <div class="form-group">
                                                <input inputmode="none"  id="fileimagenNuevo" type="file" class="filestyle" data-buttonName="btn-primary" data-buttonBefore="true" data-buttonText="Imagen" onchange="" />
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                </div>
                                <!--------------------------------------------LOCALES APLICAR------------------------------------------->
                                <div role="tabpanel" class="tab-pane fade" id="settings"> <br/>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <h5>Tiendas:</h5>
                                                    <div id="opciones_estado" class="btn-group" data-toggle="buttons">
                                                        <label id="opciones_1" class="btn btn-default btn-sm active" onclick="marcar(':checkbox');">
                                                            <input inputmode="none"  id="opt_Todos" type="radio" value="Todos" checked="" autocomplete="off" name="options" />
                                                            Todos
                                                        </label>
                                                        <label class="btn btn-default btn-sm" onclick="desmarcar(':checkbox')">
                                                            <input inputmode="none"  id="opt_Activos" type="radio" value="Activos" autocomplete="off" name="options" />
                                                            Ninguno
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" id="rest">
                                            <div style="height: 280px; overflow-y: auto;">
                                                <div id="rst_agregado" class="list-group"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--------------------------------------------FIN LOCALES APLICAR---------------------------------------->
                                <!--------------------------------------------CLIENTES APLICAR------------------------------------------->
                                <div role="tabpanel" class="tab-pane fade" id="clientesAplicar"> <br/>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <h5>Clientes:</h5>
                                                    <div id="opciones_estado" class="btn-group" data-toggle="buttons">
                                                        <label id="opciones_1" class="btn btn-default btn-sm active" onclick="marcar(':checkbox');">
                                                            <input inputmode="none"  id="opt_Todos" type="radio" value="Todos" checked="" autocomplete="off" name="options" />
                                                            Todos
                                                        </label>
                                                        <label class="btn btn-default btn-sm" onclick="desmarcar(':checkbox')">
                                                            <input inputmode="none"  id="opt_Activos" type="radio" value="Activos" autocomplete="off" name="options" />
                                                            Ninguno
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" id="rest">
                                            <div style="height: 280px; overflow-y: auto;">
                                                <div id="cliente_agregado" class="list-group"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- COLECCION RESTAURANTE -->
                                <div role="tabpanel" class="tab-pane" id="ColeccionFP">
                                    <br/> 
                                    <div class="panel panel-default" style="width:1070px;" align="center">
                                        <div class="panel-heading">
                                            <div class="row">                                
                                                <div class="col-xs-10 col-md-9"><h6><b>FORMAS DE PAGO COLECCI&Oacute;N DE DATOS</b></h6></div>                               
                                                <div class="col-md-1"></div>
                                                <div>                                            
                                                    <button type="button" class="btn btn-default" onclick="fn_nuevaColeccion();" >
                                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                                    </button>
                                                    <button type="button" class="btn btn-default" onclick="fn_editColeccionFormaPago();">
                                                        <span class="glyphicon glyphicon-pencil" style="opacity: none;" aria-hidden="true"></span>
                                                    </button>
                                                </div>   
                                            </div>
                                        </div>
                                        <!-- TABLA DETALLE COLECCECION RESTAURANTE -->
                                        <div  align="center" style="width: 1050px; height: 300px; overflow-x: auto; overflow-y: auto;">                                   
                                            <div class="form-group" id="_detalle_restaurante_coleccion">
                                                <table id="formaspago_coleccion" class="table table-bordered" style="width: auto; font-size: 11px;"></table>
                                            </div>                                    
                                        </div> 
                                    </div>
                                </div>
                            </div>
                            <!--------------------------------------------FIN CLIENTES APLICAR---------------------------------------->
                        </div>
                        <!--------------------------------------FIN NAV TABS---------------------------------------------->
                    </div>
                    <div class="modal-footer panel-footer" id="pnl_pcn_btn">
                    </div>
                    <div id="visor" class="overlayCargando" onclick="fn_visor(0)">
                        <div id="visor_img" class="modalCargando"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- COLECCION FORMAS DE PAGO - Inicio de modal de coleccion NUEVA-->
        <div class="modal fade" id="mdl_nuevaColeccion" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true" >
            <div class="modal-dialog" >
                <div class="modal-content">
                    <div class="modal-header">                  
                        <h4 class="modal-title">Colecci&oacute;n:
                            <label id="nombreColeccion"></label>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <!--DETALLE Y DATOS DE COLECCION -->
                        <div class="row">                   
                            <div class="col-xs-12 col-md-12">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group" style="height: 200px; overflow: auto;">
                                        <table id="coleccion_descripcion" class="table table-bordered" style="font-size: 11px;"></table>                                
                                    </div> 
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group" style="height: 200px; overflow: auto;">
                                        <table id="coleccion_datos" class="table table-bordered" style="font-size: 11px;"></table>                                
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <!--TIPOS DE DATOS-->
                        <div id="tipos_de_dato"  style="height: 180px; overflow: auto;">
                            <div class="row"> 
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Especifica Valor: <input inputmode="none"  disabled="disabled" type="checkbox" value="1" id="check_especifica"/></h5>
                                    </div>
                                </div>        
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Obligatorio: <input inputmode="none"  disabled="disabled" type="checkbox" value="1" id="check_obligatorio"/></h5>
                                    </div>
                                </div>       
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Tipo de dato: <label value="1" id="lbl_tipoDato"></label></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Varchar:</h5></div>
                                <div class="col-xs-7">
                                    <div class="form-group">
                                        <input inputmode="none"  maxlength="200" type="text" class="form-control" id="tipo_varchar"  />
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Entero:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_entero" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Fecha:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  type="text" value="" class="form-control" name="tipo_fecha" id="tipo_fecha" placeholder="Fecha" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">                     	
                                <div class="col-xs-3 text-right"><h5>Bit:</h5></div>
                                <div class="col-xs-3">
                                    <div class="form-group">
                                        <input inputmode="none"  type="checkbox" id="tipo_bit" data-off-text="No" data-on-text="Si" />         
                                    </div>
                                </div>                             
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Num&eacute;rico:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_numerico" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">                      
                                <div class="col-md-3"><h5 class="text-right">Rango Fecha:</h5></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="FechaInicial" class="control-label">Fecha Inicio</label>
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  type="text" value="" class="form-control" name="FechaInicial" id="FechaInicial" placeholder="Fecha Inicio" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="FechaFinal" class="control-label">Fecha Fin</label>
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  type="text" value="" class="form-control" name="FechaFinal" id="FechaFinal" placeholder="Fecha Fin"/>
                                        </div>
                                    </div>
                                </div>                      
                            </div>
                            <div class="row">                      
                                <div class="col-md-3"><h5 class="text-right">Rango Decimal:</h5></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Min" class="control-label">Minimo</label>                                
                                        <div class="form-group">
                                            <input inputmode="none"  maxlength="50" type="text" class="form-control" id="rango_minimo" />
                                        </div>                                
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Max" class="control-label">M&aacute;ximo.</label>                                
                                        <div class="form-group">
                                            <input inputmode="none"  maxlength="50" type="text" class="form-control" id="rango_maximo" />
                                        </div>                                
                                    </div>
                                </div>                      
                            </div>                     
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="fn_verModal();">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="fn_guardarFormasPagoColeccion();">Guardar</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!--Inicio de modal de coleccion MODIFICAR -->
        <div class="modal fade" id="mdl_editColeccion" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Colecci&oacute;n:
                            <label id="edit_nombreColeccion"></label>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" checked="checked" id="check_estado"/></h6></div> 
                        <!--TIPOS DE DATOS-->
                        <div id="tipos_de_dato">
                            <div class="row">
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Especifica Valor: <input inputmode="none"  disabled="disabled" type="checkbox" id="edit_check_especifica"/></h5>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Obligatorio: <input inputmode="none"  disabled="disabled" type="checkbox" id="edit_check_obligatorio"/></h5>
                                    </div>
                                </div>       
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Tipo de dato: <label value="1" id="edit_lbl_tipoDato"></label></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Varchar:</h5></div>
                                <div class="col-xs-7">
                                    <div class="form-group">
                                        <input inputmode="none"  maxlength="200" type="text" class="form-control" id="tipo_varchar_edit" />
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Entero:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_entero_edit" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Fecha:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  type="text" value="" class="form-control" name="tipo_fecha_edit" id="tipo_fecha_edit" placeholder="Fecha" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">                     	
                                <div class="col-xs-3 text-right"><h5>Bit:</h5></div>
                                <div class="col-xs-3">
                                    <div class="form-group">
                                        <input inputmode="none"  type="checkbox" id="tipo_bit_edit" data-off-text="No" data-on-text="Si" />         
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Num&eacute;rico:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_numerico_edit" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><h5 class="text-right">Rango Fecha:</h5></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="FechaInicial" class="control-label">Fecha Inicio</label>
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  type="text" value="" class="form-control" name="FechaInicial_edit" id="FechaInicial_edit" placeholder="Fecha Inicio" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="FechaFinal" class="control-label">Fecha Fin</label>
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  type="text" value="" class="form-control" name="FechaFinal_edit" id="FechaFinal_edit" placeholder="Fecha Fin" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">                      
                                <div class="col-md-3"><h5 class="text-right">Rango Decimal:</h5></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Min" class="control-label">Minimo</label>                                
                                        <div class="form-group">
                                            <input inputmode="none"  maxlength="50" type="text" class="form-control" id="rango_minimo_edit" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Max" class="control-label">M&aacute;ximo.</label>
                                        <div class="form-group">
                                            <input inputmode="none"  maxlength="50" type="text" class="form-control" id="rango_maximo_edit" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="fn_verModal();">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="fn_modificaFormaPagoColeccion();">Guardar</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!------------------------------------ FIN MODAL ADMINISTRACION FORMAS DE PAGO ---------------------------------------->
        <!------------------------------------------------ MODAL MONEDA SIMBOLO ----------------------------------------------->
        <div class="modal fade"  data-backdrop="static" id="modalMoneda" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulomodalmoneda"></h4>
                    </div>
                    <br/>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-1"></div>
                            <div class="col-xs-4"><h5>Pa&iacute;s Descripci&oacute;n</h5></div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <input inputmode="none"  type="text" id="pais_descripcion" readonly="readonly" style="text-align:center" class="form-control"  maxlength="100" />
                                </div>
                            </div>
                            <div class="col-xs-1"></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-1"></div>
                            <div class="col-xs-4"><h5>Moneda:</h5></div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <input inputmode="none"  type="text" id="pais_moneda" onkeyup="aMays(event, this)" class="form-control" maxlength="3" />
                                </div>
                            </div>
                            <div class="col-xs-1"></div>
                        </div> 
                        <div class="row">
                            <div class="col-xs-1"></div>
                            <div class="col-xs-4"><h5>Descripci&oacute;n Moneda:</h5></div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <input inputmode="none"  type="text" id="pais_desc_modeda" onkeyup="aMays(event, this)" class="form-control" maxlength="10" />
                                </div>
                            </div>
                            <div class="col-xs-1"></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-1"></div>
                            <div class="col-xs-4"><h5>Base Factura:</h5></div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <input inputmode="none"  type="text" id="pais_base_factura" onkeypress="return NumCheck(event, this);" class="form-control" maxlength="10" />
                                </div>
                            </div>
                            <div class="col-xs-1"></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-1"></div>
                            <div class="col-xs-4"><h5>Moneda S&iacute;mbolo:</h5></div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <input inputmode="none"  type="text" id="pais_moneda_simbolo" class="form-control" maxlength="2" />
                                </div>
                            </div>
                            <div class="col-xs-1"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="fn_guardarModificaSimboloMoneda();" >Aceptar</button>
                        <button type="button" class="btn btn-default" onclick="" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <input inputmode="none"  type="hidden" id="txt_formapago" />
        <input inputmode="none"  type="hidden" id="txt_idrestaurante" />
        <input inputmode="none"  type="hidden" id="txt_filarestaurante" />
        <input inputmode="none"  type="hidden" id="txt_porcentajepropina" />
        <input inputmode="none"  type="hidden" id="txt_pais_id" />
        <input inputmode="none"  type="hidden" id="txt_cliente_id" />
        <input inputmode="none"  type="hidden" id="IDFormaPago" />
        <!---------------------------------------------- FIN MODAL MONEDA SIMBOLO ----------------------------------------------> 
        <!---------------------------------------------------- JSQUERY  -------------------------------------------------------->
        <script language="javascript1.1" type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script	type="text/javascript" src="../../js/jquery-ui.js"></script>
        <script language="javascript1.1"  src="../../js/alertify.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/idioma.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/js_validaciones.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/switch.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-filestyle.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
        <script type="text/javascript" src="../../js/ajax_formaspago.js"></script>
        <script type="text/javascript" src="../../js/ajax_denominacion.js"></script>
    </body>
</html>