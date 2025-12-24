<?php
session_start();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8"/>
        <title>Ingresos - Egresos Dep&oacute;sitos</title>
        <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>        
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
        <link media="all" href="../../css/progressBar.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />               
        
        
    </head>
    <body>
        <div class="superior">
            <div class="menu" align="center" style="width: 400px;">
                <ul>
                    <li>
                        <button id="btnAgregarNuevoMotivoIngresosEgresosDeposito" class="botonMnSpr l-basic-elaboration-document-plus" onclick="agregarNuevoMotivoIngresosEgresosDeposito()">
                            <span>Nuevo</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>MOTIVOS DE INGRESOS Y EGRESOS DE DEP&Oacute;SITO</h1>
            </div>
        </div>
        
        <div class="contenedor">
            <div class="inferior">
                <div class="panel panel-default" id="botonesActivosInactivos">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-3">
                                <div id="opciones_estados" class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default btn-sm active" onclick="fn_cargaDetalleConceptosDepositos('Activo');"><input inputmode="none"  id="opt_Activos" type="radio" value="Activos" checked="checked" autocomplete="off" name="estados" />Activos</label>
                                    <label class="btn btn-default btn-sm" onclick="fn_cargaDetalleConceptosDepositos('Inactivo');"><input inputmode="none"  id="opt_Inactivos" type="radio" value="Inactivos" autocomplete="off" name="estados" />Inactivos</label>
                                    <label class="btn btn-default btn-sm" onclick="fn_cargaDetalleConceptosDepositos('Todos');"><input inputmode="none"  id="opt_Todos" type="radio" value="Todos" autocomplete="off" name="estados" />Todos</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tbl_conceptosDepositos">
                    <table class="table table-bordered table-hover" id="tbl_detalleDepositos" border="1" cellpadding="1" cellspacing="0"></table>
                </div>
            </div>
        </div>
        
        
        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../../imagenes/admin_resources/progressBar.gif" />
            </div>
        </div>
        
        <!-------------------------------------INICIO MODAL NUEVO BOOTSTRAP---------------------------------------------->
         <div class="modal fade" id="ModalNuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulomodalNuevo"><!--<input inputmode="none"  style="width:50px; text-align:center; border:hidden" disabled="disabled" id="txt_estacionsuperior" onkeypress="return fn_numeros(event);"/></b>--></h4>
                    </div>
                    <br/>
                    <div align="right" class="col-xs-12 col-x"> Está Activo?
                        <input inputmode="none"  type="checkbox" id="option" checked="checked" />
                    </div>
                    <br/>
                    <div class="modal-body">
                         <div class="row">
                            <div class="col-xs-1"></div>
                            <div class="col-xs-3">
                                <h5>Descripci&oacute;n:</h5>
                            </div>
                            <div class="col-xs-7">
                                            <div class="form-group">
                                                <input inputmode="none"  type="text" rel="tooltip" style="text-transform:uppercase;" class="form-control" id="txt_desConcepto" />                 
                                            </div>
                            </div>
                            <div class="col-xs-1"></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-1"></div>
                            <div class="col-xs-3">
                                <h5>Signo:</h5>
                            </div>
                            <div class="col-xs-7">
                                            <div class="form-group">
                                                <select id="selSigno" class="form-control">
                                                                                    <option value="+">+</option>
                                                                                    <option value="-">-</option>
                                                                                </select>
                                            </div>
                            </div>
                            <div class="col-xs-1"></div>
                        </div>
                    </div>
                    <div class="modal-footer panel-footer" id="botonessalir">
                        <button type="button" class="btn btn-primary" onclick="fn_guardar();" id='botonGuardarEstacion' data-dismiss="modal">Aceptar</button>
                        <button type="button" class="btn btn-default" onclick="fn_cerrarModal()" data-dismiss="modal" style="width:10%">Salir</button>
                    </div>
                </div>
            </div>
        </div>
                        
                                                    
        
        
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
        <script type="text/javascript" src="../../js/ajax_adminIngresosEgresosDepositos.js"></script>
    </body>
</html>