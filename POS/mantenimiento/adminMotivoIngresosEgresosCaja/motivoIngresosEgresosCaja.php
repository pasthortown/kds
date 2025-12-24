<?php
session_start();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8"/>
        <title>Categorías</title>
        <link media="all" href="../../bootstrap/normalize/normalize.css" rel="stylesheet" />
        <link media="all" href="../../bootstrap/templete/css/icons.css" rel="stylesheet" />
        <link media="all" href="../../bootstrap/templete/css/bootstrap.css" rel="stylesheet" />
        <link media="all" href="../../bootstrap/templete/css/plugins.css" rel="stylesheet" />
        <link media="all" href="../../bootstrap/templete/css/main.css" rel="stylesheet" />
        <link media="all" href="../../bootstrap/templete/css/custom.css" rel="stylesheet" />
        <link media="all" href="../../css/progressBar.css" rel="stylesheet" />
        <link media="all" href="../../css/alertify.core.css" rel="stylesheet"  type="text/css" />
        <link media="all" href="../../css/alertify.default.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div class="superior">
            <div class="menu" align="center" style="width: 400px;">
                <ul>
                    <li>
                        <button id="btnAgregarNuevoMotivoIngresosEgresosCaja" class="botonMnSpr l-basic-elaboration-document-plus" onclick="agregarNuevoMotivoIngresosEgresosCaja()">
                            <span>Nuevo</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>MOTIVOS DE INGRESOS Y EGRESOS DE CAJA</h1>
            </div>
        </div>
        <div class="inferior">
            <div class="page-content-wrapper">
                <div id="page-header" class="clearfix">
                    <div class="page-header">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 sortable-layout">
                        <div class="panel panel-default toggle panelMove panelClose panelRefresh">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div id="opcionesMotivosIngresosEgresosCaja" class="btn-group" data-toggle="buttons">
                                            <label id="lblMieOpcionActivos" class="btn btn-default btn-sm active" onclick="cargarMotivosIngresosEgresosCajaActivos()">
                                                Activos
                                                <input inputmode="none"  id="inpMieOpcionActivos" type="radio" name="uno" />
                                            </label>
                                            <label id="inpMieOpcionInactivos" class="btn btn-default btn-sm" onclick="cargarMotivosIngresosEgresosCajaInactivos()">
                                                Inactivos
                                                <input inputmode="none"  id="lblMieOpcionInactivos" type="radio" name="uno" />
                                            </label>
                                            <label id="lblMieOpcionTodos" class="btn btn-default btn-sm" onclick="cargarMotivosIngresosEgresosCajaTodos()">
                                                Todos
                                                <input inputmode="none"  id="inpMieOpcionTodos" type="radio" name="uno" />
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div id="motivosIngresosEgresosCaja" class="col-md-12">
                                        <table id="tblMotivosIngresosEgresosCaja" class="table table-striped table-bordered" width="100%" cellspacing="0"></table>
                                        <div id="mdlNuevoMotivoIngresosEgresosCaja" class="modal" aria-hidden="true" data-backdrop="static" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" style="display: none;">
                                            <div class="modal-dialog quitarMarginTop5">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button class="close" data-dismiss="modal" type="button">
                                                            <span aria-hidden="true">×</span>
                                                            <span class="sr-only">Close</span>
                                                        </button>
                                                        <h4 id="mdlMotivosIngresosEgresosCajaTitulo" class="modal-title"></h4>
                                                    </div>
                                                    <div class="modal-body quitarPaddingBottom">
                                                        <div class="row">
                                                            <div class="panel panel-default">
                                                                <div class="panel-body">
                                                                    <div class="form-horizontal">
                                                                        <div class="form-group">
                                                                            <div class="col-lg-8 col-md-6"></div>
                                                                            <label id="lblMieEstado" class="col-lg-2 col-md-3 control-label" for="inpMieEstado"><b>Activo</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-off="NO" data-on="SI">
                                                                                        <input inputmode="none"  id="inpMieEstado" type="checkbox" name="checkbox-toggle" />
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label id="lblMieConcepto" class="col-lg-2 col-md-3 control-label" for="inpMieConcepto"><b>Concepto:</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <input inputmode="none"  id="inpMieConcepto" class="form-control" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label id="lblMieSigno" class="col-lg-2 col-md-3 control-label" for="slcMieSigno"><b>Signo:</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <select id="slcMieSigno" class="form-control">
                                                                                    <option value="+">+</option>
                                                                                    <option value="-">-</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label id="lblMieNivel" class="col-lg-2 col-md-3 control-label" for="inpMieNivel"><b>Nivel:</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <div class="input-prepend input-group number-spinner">
                                                                                    <span class="add-on input-group-addon btn btn-primary" data-dir="dwn"><i class="glyphicon glyphicon-minus fa fa-calendar"></i></span>
                                                                                    <input inputmode="none"  id="inpMieNivel" type="text" class="form-control text-center" value="0" min="0" max="50" disabled="disabled" />
                                                                                    <span class="add-on input-group-addon btn btn-primary" data-dir="up"><i class="glyphicon glyphicon-plus fa fa-calendar"></i></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer quitarMarginTop">
                                                        <button class="btn btn-default" type="button" data-dismiss="modal">Cancelar</button>
                                                        <button id="btnMieGuardarCambios" class="btn btn-primary" type="button" onclick="">
                                                            <i class="glyphicon glyphicon-floppy-saved mr10"></i>
                                                            Guardar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../../imagenes/admin_resources/progressBar.gif" />
            </div>
        </div>
        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../bootstrap/templete/js/bootstrap/bootstrap.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/templete/plugins/forms/spinner/jquery.bootstrap-touchspin.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.uitablefilter.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/inputnumber.js"></script>
        <script type="text/javascript" src="../../bootstrap/templete/plugins/tables/datatables/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../js/ajax_adminMotivoIngresosEgresosCaja.js"></script>
    </body>
</html>