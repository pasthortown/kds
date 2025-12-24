<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Modulos</title>
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
            </div>
            <div class="tituloPantalla">
                <h1>MODULOS</h1>
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
                                <div class="col-md-6">
                                    <h4 class="panel-title">Módulos</h4>
                                </div>
                                <div class="col-md-6">
                                    <h4 class="panel-title">Estados</h4>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div id="modulos" class="col-md-6">
                                        <div id="brrModulos" class="row">
                                            <div class="col-lg-12 col-md-12 text-right">
                                                <button id="btnAgregarNuevoModulo" class="btn btn-success" type="button" onclick="agregarNuevoModulo()">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <table id="tblModulos" class="table table-striped table-bordered" width="100%" cellspacing="0"></table>
                                        <div id="mdlNuevoModulo" class="modal" aria-hidden="true" data-backdrop="static" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" style="display: none;">
                                            <div class="modal-dialog quitarMarginTop5">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button class="close" data-dismiss="modal" type="button">
                                                            <span aria-hidden="true">×</span>
                                                            <span class="sr-only">Close</span>
                                                        </button>
                                                        <h4 id="mdlModuloTitulo" class="modal-title"></h4>
                                                    </div>
                                                    <div class="modal-body quitarPaddingBottom">
                                                        <div class="row">
                                                            <div class="panel panel-default">
                                                                <div class="panel-body">
                                                                    <div class="form-horizontal">
                                                                        <div class="form-group">
                                                                            <div class="col-lg-8 col-md-6"></div>
                                                                            <label id="lblMdlEstado" class="col-lg-2 col-md-3 control-label" for="inpMdlEstado"><b>Activo</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-off="NO" data-on="SI">
                                                                                        <input inputmode="none"  id="inpMdlEstado" type="checkbox" name="checkbox-toggle" />
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label id="lblMdlDescripcion" class="col-lg-2 col-md-3 control-label" for="inpMdlDescripcion"><b>Descripción</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <input inputmode="none"  id="inpMdlDescripcion" class="form-control" type="text">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label id="lblMdlAbreviatura" class="col-lg-2 col-md-3 control-label" for="inpMdlAbreviatura"><b>Abreviatura</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <input inputmode="none"  id="inpMdlAbreviatura" class="form-control" type="text">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label id="lblMdlNivel" class="col-lg-2 col-md-3 control-label" for="inpMdlNivel"><b>Nivel</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <div class="input-prepend input-group number-spinner">
                                                                                    <span class="add-on input-group-addon btn btn-primary" data-dir="dwn"><i class="glyphicon glyphicon-minus fa fa-calendar"></i></span>
                                                                                    <input inputmode="none"  id="inpMdlNivel" type="text" class="form-control text-center" value="0" min="0" max="50" disabled="disabled"/>
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
                                                        <button id="btnMdlGuardarCambios" class="btn btn-primary" type="button" onclick="">
                                                            <i class="glyphicon glyphicon-floppy-saved mr10"></i>
                                                            Guardar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="estados" class="col-md-6">
                                        <div id="brrEstados" class="row">
                                            <div class="col-lg-12 col-md-12 text-right">
                                                <button id="btnAgregarNuevoEstado" class="btn btn-success" type="button" onclick="agregarNuevoEstado(null)">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <table id="tblEstadosModulos" class="table table-striped table-bordered" width="100%" cellspacing="0"></table>
                                        <div id="mdlNuevoEstado" class="modal" aria-hidden="true" data-backdrop="static" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" style="display: none;">
                                            <div class="modal-dialog quitarMarginTop5">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button class="close" data-dismiss="modal" type="button">
                                                            <span aria-hidden="true">×</span>
                                                            <span class="sr-only">Close</span>
                                                        </button>
                                                        <h4 id="mdlEstadoTitulo" class="modal-title"></h4>
                                                    </div>
                                                    <div class="modal-body quitarPaddingBottom">
                                                        <div class="row">
                                                            <div class="panel panel-default">
                                                                <div class="panel-body">
                                                                    <div class="form-horizontal">
                                                                        <div class="form-group">
                                                                            <label class="col-lg-2 col-md-3 control-label"><b>Módulo</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <label id="lblEstModulo" class="control-label"></label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label id="lblEstDescripcion" class="col-lg-2 col-md-3 control-label" for="inpEstDescripcion"><b>Descripción</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <input inputmode="none"  id="inpEstDescripcion" class="form-control" type="text">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label id="lblEstFactor" class="col-lg-2 col-md-3 control-label"><b>Factor</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <select id="slcEstFactor" class="form-control"></select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label id="lblEstNivel" class="col-lg-2 col-md-3 control-label" for="inpEstNivel"><b>Nivel</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <div class="input-prepend input-group number-spinner">
                                                                                    <span class="add-on input-group-addon btn btn-primary" data-dir="dwn"><i class="glyphicon glyphicon-minus fa fa-calendar"></i></span>
                                                                                    <input inputmode="none"  id="inpEstNivel" type="text" class="form-control text-center" value="0" min="0" max="50" disabled="disabled"/>
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
                                                        <button id="btnEstGuardarCambios" class="btn btn-primary" type="button" onclick="">
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
                <img src="../../imagenes/admin_resources/progressBar.gif"/>
            </div>
        </div>
        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../js/jquery-latest.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.uitablefilter.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/inputnumber.js"></script>
        <script type="text/javascript" src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../js/ajax_adminmodulos.js"></script>
    </body>
</html>