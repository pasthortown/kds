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
            <div class="menu" align="center" style="width: 300px;">
                <ul>
                    <li>
                        <button id="btnAgregarNuevaCategoriaPrecios" class="botonMnSpr l-basic-elaboration-document-plus" onclick="agregarNuevaCategoriaPrecios()">
                            <span>Nuevo</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>CATEGORIAS DE PRECIOS</h1>
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
                                        <div id="opcionesCategoriasPrecios" class="btn-group" data-toggle="buttons">
                                            <label id="lblOpcionActivos" class="btn btn-default btn-sm active" onclick="cargarCategoriasPreciosActivos()">
                                                Activos
                                                <input inputmode="none"  id="inpOpcionActivos" type="radio" name="uno" />
                                            </label>
                                            <label id="inpOpcionInactivos" class="btn btn-default btn-sm" onclick="cargarCategoriasPreciosInactivos()">
                                                Inactivos
                                                <input inputmode="none"  id="lblOpcionInactivos" type="radio" name="uno" />
                                            </label>
                                            <label id="lblOpcionTodos" class="btn btn-default btn-sm" onclick="cargarCategoriasPreciosTodos()">
                                                Todos
                                                <input inputmode="none"  id="inpOpcionTodos" type="radio" name="uno" />
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div id="categoriasPrecios" class="col-md-12">
                                        <table id="tblCategoriasPrecios" class="table table-striped table-bordered" width="100%" cellspacing="0"></table>
                                        <div id="mdlNuevaCategoriaPrecios" class="modal" aria-hidden="true" data-backdrop="static" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" style="display: none;">
                                            <div class="modal-dialog quitarMarginTop5">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button class="close" data-dismiss="modal" type="button">
                                                            <span aria-hidden="true">×</span>
                                                            <span class="sr-only">Close</span>
                                                        </button>
                                                        <h4 id="mdlCategoriaPreciosTitulo" class="modal-title"></h4>
                                                    </div>
                                                    <div class="modal-body quitarPaddingBottom">
                                                        <div class="row">
                                                            <div class="panel panel-default">
                                                                <div class="panel-body">
                                                                    <div class="form-horizontal">
                                                                        <div class="form-group">
                                                                            <div class="col-lg-8 col-md-6"></div>
                                                                            <label id="lblCgpEstado" class="col-lg-2 col-md-3 control-label" for="inpCgpEstado"><b>Activo</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-off="NO" data-on="SI">
                                                                                        <input inputmode="none"  id="inpCgpEstado" type="checkbox" name="checkbox-toggle" />
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label id="lblCgpDescripcion" class="col-lg-2 col-md-3 control-label" for="inpCgpDescripcion"><b>Descripción</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <input inputmode="none"  id="inpCgpDescripcion" class="form-control" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label id="lblCgpAbreviatura" class="col-lg-2 col-md-3 control-label" for="inpCgpAbreviatura"><b>Abreviatura</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <input inputmode="none"  id="inpCgpAbreviatura" class="form-control" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label id="lblCgpNivel" class="col-lg-2 col-md-3 control-label" for="inpCgpNivel"><b>Nivel</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <div class="input-prepend input-group number-spinner">
                                                                                    <span class="add-on input-group-addon btn btn-primary" data-dir="dwn"><i class="glyphicon glyphicon-minus fa fa-calendar"></i></span>
                                                                                    <input inputmode="none"  id="inpCgpNivel" type="text" class="form-control text-center" value="0" min="0" max="50" disabled="disabled" />
                                                                                    <span class="add-on input-group-addon btn btn-primary" data-dir="up"><i class="glyphicon glyphicon-plus fa fa-calendar"></i></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div id="divIntegracion" class="form-group">
                                                                            <label id="lblCgpIntegracion" class="col-lg-2 col-md-3 control-label" for="inpCgpIntegracion"><b>Integración</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <input inputmode="none"  id="inpCgpIntegracion" class="form-control" disabled="disabled"/>
                                                                            </div>
                                                                        </div>
                                                                        <div id="divHeredar" class="form-group">
                                                                            <label id="lblCgpHeredar" class="col-lg-2 col-md-3 control-label" for="slcCgpHeredar"><b>Heredar precios de</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <select id="slcCgpHeredar" class="form-control"></select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer quitarMarginTop">
                                                        <button class="btn btn-default" type="button" data-dismiss="modal">Cancelar</button>
                                                        <button id="btnCgpGuardarCambios" class="btn btn-primary" type="button" onclick="">
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
        <script type="text/javascript" src="../../js/ajax_admincategorias.js"></script>
    </body>
</html>