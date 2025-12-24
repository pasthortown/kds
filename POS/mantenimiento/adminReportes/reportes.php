<?php
    session_start();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8"/>
        <title>Reportes</title>
        <link media="all" href="../../bootstrap/normalize/normalize.css" rel="stylesheet" />
        <link media="all" href="../../bootstrap/templete/css/icons.css" rel="stylesheet" />
        <link media="all" href="../../bootstrap/templete/css/bootstrap.css" rel="stylesheet" />
        <link media="all" href="../../bootstrap/templete/css/plugins.css" rel="stylesheet" />
        <link media="all" href="../../bootstrap/templete/css/main.css" rel="stylesheet" />
        <link media="all" href="../../bootstrap/templete/css/custom.css" rel="stylesheet" />
        <link media="all" href="../../css/progressBar.css" rel="stylesheet" />
        <link media="all" href="../../css/alertify.core.css" rel="stylesheet"  type="text/css" />
        <link media="all" href="../../css/alertify.default.css" rel="stylesheet" type="text/css" />
        <link media="all" href="../../css/textArea.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div class="superior">
            <div class="menu" align="center" style="width: 400px;">
            </div>
            <div class="tituloPantalla">
                <h1>REPORTES</h1>
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
                                <div class="col-md-2">
                                    <h4 class="panel-title">Categorías</h4>
                                </div>
                                <div class="col-md-3">
                                    <h4 class="panel-title">Reportes</h4>
                                </div>
                                <div class="col-md-7">
                                    <h4 class="panel-title">Parámetros</h4>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div id="categorias" class="col-md-2">
                                        <div id="brrCategorias" class="row">
                                            <div class="col-lg-12 col-md-12 text-right">
                                                <button id="btnAgregarNuevaCategoria" class="btn btn-success" type="button" onclick="agregarNuevaCategoria()">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                </button>
                                                <button id="btnEliminarCategoria" class="btn btn-danger" type="button" onclick="validarEliminarCategoria()">
                                                    <i class="glyphicon glyphicon-remove"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <table id="tblCategorias" class="table table-striped table-bordered" width="100%" cellspacing="0"></table>
                                        <div id="mdlNuevaCategoria" class="modal" aria-hidden="true" data-backdrop="static" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" style="display: none;">
                                            <div class="modal-dialog quitarMarginTop5">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button class="close" data-dismiss="modal" type="button">
                                                            <span aria-hidden="true">×</span>
                                                            <span class="sr-only">Close</span>
                                                        </button>
                                                        <h4 id="mdlCategoriaTitulo" class="modal-title"></h4>
                                                    </div>
                                                    <div class="modal-body quitarPaddingBottom">
                                                        <div class="row">
                                                            <div class="panel panel-default">
                                                                <div class="panel-body">
                                                                    <div class="form-horizontal">
                                                                        <div class="form-group">
                                                                            <div class="col-lg-8 col-md-6"></div>
                                                                            <label id="lblCtgEstado" class="col-lg-2 col-md-3 control-label" for="inpCtgEstado"><b>Activo</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-off="NO" data-on="SI">
                                                                                        <input inputmode="none"  id="inpCtgEstado" type="checkbox" name="checkbox-toggle" />
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label id="lblCtgDescripcion" class="col-lg-2 col-md-3 control-label" for="inpCtgDescripcion"><b>Descripción</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <input inputmode="none"  id="inpCtgDescripcion" class="form-control" type="text">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label id="lblCarpetaDirectorio" class="col-lg-2 col-md-3 control-label"><b>Ruta </b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                               <select class="form-control" id="sel_ruta"></select>                                                                                
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer quitarMarginTop">
                                                        <button class="btn btn-default" type="button" data-dismiss="modal">Cancelar</button>
                                                        <button id="btnCtgGuardarCambios" class="btn btn-primary" type="button" onclick="">
                                                            <i class="glyphicon glyphicon-floppy-saved mr10"></i>
                                                            Guardar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="reportes" class="col-md-3 sortable-layout">
                                        <div id="brrReportes" class="row">
                                            <div class="col-lg-12 col-md-12 text-right">
                                                <button id="btnAgregarNuevoReporte" class="btn btn-success" type="button" onclick="agregarNuevoReporte(null)">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                </button>                                            
                                                <button id="btnEliminarReporte" class="btn btn-danger" type="button" onclick="validarEliminarReporte()">
                                                    <i class="glyphicon glyphicon-remove"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <table id="tblReportes" class="table table-striped table-bordered" width="100%" cellspacing="0"></table>
                                        <div id="mdlNuevoReporte" class="modal" aria-hidden="true" data-backdrop="static" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" style="display: none;">
                                            <div class="modal-dialog quitarMarginTop5">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button class="close" data-dismiss="modal" type="button">
                                                            <span aria-hidden="true">×</span>
                                                            <span class="sr-only">Close</span>
                                                        </button>
                                                        <h4 id="mdlReporteTitulo" class="modal-title"></h4>
                                                    </div>
                                                    <div class="modal-body quitarPaddingBottom">
                                                        <div class="panel panel-default">
                                                            <div class="panel-body">
                                                                <div class="form-horizontal">
                                                                    <div class="form-group">
                                                                        <label class="col-lg-2 col-md-2 control-label"><b>Categoría</b></label>
                                                                        <div class="col-lg-6 col-md-6">
                                                                            <label id="lblRptCategoria" class="control-label"></label>
                                                                        </div>
                                                                        <label id="lblRptEstado" class="col-lg-2 col-md-2 control-label" for="inpRptEstado"><b>Activo</b></label>
                                                                        <div class="col-lg-2 col-md-2">
                                                                            <div class="toggle-custom">
                                                                                <label class="toggle" data-off="NO" data-on="SI">
                                                                                    <input inputmode="none"  id="inpRptEstado" type="checkbox" name="checkbox-toggle" />
                                                                                    <span class="button-checkbox"></span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label id="lblRptLabel" class="col-lg-2 col-md-3 control-label" for="inpRptLabel"><b>Nombre</b></label>
                                                                        <div class="col-lg-10 col-md-9">
                                                                            <input inputmode="none"  id="inpRptLabel" class="form-control" type="text" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label id="lblRptDescripcion" class="col-lg-2 col-md-3 control-label" for="inpRptDescripcion"><b>Descripción</b></label>
                                                                        <div class="col-lg-10 col-md-9">
                                                                            <textArea id="inpRptDescripcion" class="form-control noResizable" rows="3"></textArea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label id="lblRptCarpeta" class="col-lg-2 col-md-3 control-label" for="inpRptCarpeta"><b>Carpeta</b></label>
                                                                        <div class="col-lg-10 col-md-9">
                                                                            <input inputmode="none"  id="inpRptCarpeta" class="form-control" type="text" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label id="lblRptArchivo" class="col-lg-2 col-md-3 control-label" for="inpRptUrl"><b>Archivo</b></label>
                                                                        <div class="col-lg-10 col-md-9">
                                                                            <input inputmode="none"  id="inpRptArchivo" class="form-control" type="text" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer quitarMarginTop">
                                                        <button class="btn btn-default" type="button" data-dismiss="modal">Cancelar</button>
                                                        <button id="btnRptGuardarCambios" class="btn btn-primary" type="button" onclick="">
                                                            <i class="glyphicon glyphicon-floppy-saved mr10"></i>
                                                            Guardar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="parametros" class="col-md-7 sortable-layout">
                                        <div id="brrParametros" class="row">
                                            <div class="col-lg-12 col-md-12 text-right">
                                                <button id="btnAgregarNuevoParametro" class="btn btn-success" type="button" onclick="agregarNuevoParametro(null)">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                </button>
                                                <button id="btnEliminarParametro" class="btn btn-danger" type="button" onclick="validarEliminarParametro()">
                                                    <i class="glyphicon glyphicon-remove"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <table id="tblParametros" class="table table-striped table-bordered" width="100%" cellspacing="0"></table>
                                        <div id="mdlNuevoParametro" class="modal" aria-hidden="true" data-backdrop="static" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" style="display: none;">
                                            <div class="modal-dialog quitarMarginTop5">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button class="close" data-dismiss="modal" type="button">
                                                            <span aria-hidden="true">×</span>
                                                            <span class="sr-only">Close</span>
                                                        </button>
                                                        <h4 id="mdlParametroTitulo" class="modal-title"></h4>
                                                    </div>
                                                    <div class="modal-body quitarPaddingBottom">
                                                        <div class="panel panel-default">
                                                            <div class="panel-body">
                                                                <div class="form-horizontal">
                                                                    <div class="form-group">
                                                                        <label class="col-lg-2 col-md-2 control-label"><b>Reporte</b></label>
                                                                        <div class="col-lg-6 col-md-6">
                                                                            <label id="lblPrmReporte" class="control-label"></label>
                                                                        </div>
                                                                        <label id="lblPrmEstado" class="col-lg-2 col-md-2 control-label" for="inpPrmEstado"><b>Activo</b></label>
                                                                        <div class="col-lg-2 col-md-2">
                                                                            <div class="toggle-custom">
                                                                                <label class="toggle" data-off="NO" data-on="SI">
                                                                                    <input inputmode="none"  id="inpPrmEstado" type="checkbox" name="checkbox-toggle" />
                                                                                    <span class="button-checkbox"></span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label id="lblPrmEtiqueta" class="col-lg-2 col-md-3 control-label" for="inpPrmEtiqueta"><b>Descripción</b></label>
                                                                        <div class="col-lg-10 col-md-9">
                                                                            <input inputmode="none"  id="inpPrmEtiqueta" class="form-control" type="text" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label id="lblPrmVariable" class="col-lg-2 col-md-3 control-label" for="inpPrmVariable"><b>Parámetro</b></label>
                                                                        <div class="col-lg-10 col-md-9">
                                                                            <input inputmode="none"  id="inpPrmVariable" class="form-control" type="text" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label id="lblPrmTipoDato" class="col-lg-2 col-md-3 control-label" for="inpPrmTipoDato"><b>Tipo de Dato</b></label>
                                                                        <div class="col-lg-10 col-md-9">
                                                                            <select id="slcTipoDato" class="form-control"></select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="toggle-custom">
                                                                            <label id="lblPrmObligatorio" class="col-lg-2 col-md-3 control-label" for="inpPrmObligatorio"><b>Obligatorio</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <label class="toggle" data-off="NO" data-on="SI">
                                                                                    <input inputmode="none"  id="inpPrmObligatorio" type="checkbox" name="checkbox-toggle" />
                                                                                    <span class="button-checkbox"></span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div id="divTablaIntegracion" class="form-group">
                                                                        <label id="lblPrmTablaIntegracion" class="col-lg-2 col-md-3 control-label"><b>Tabla de Integración</b></label>
                                                                        <div class="col-lg-10 col-md-9">
                                                                            <select id="slcTablaIntegracion" class="form-control" disabled="disabled"></select>
                                                                        </div>
                                                                    </div>
                                                                    <div id="divColumnaIntegracion" class="form-group">
                                                                        <label id="lblPrmColumnaIntegracion" class="col-lg-2 col-md-3 control-label" for="inpPrmColumnaIntegracion"><b>Columna de Integración</b></label>
                                                                        <div class="col-lg-10 col-md-9">
                                                                            <select id="slcColumnaIntegracion" class="form-control"></select>
                                                                        </div>
                                                                    </div>
                                                                    <div id="divQuery" class="form-group">
                                                                        <label id="lblPrmQuery" class="col-lg-2 col-md-3 control-label" for="txaPrmQuery"><b>Query</b></label>
                                                                        <div class="col-lg-10 col-md-9">
                                                                            <textArea id="txaPrmQuery" class="form-control noResizable" rows="3"></textArea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer quitarMarginTop">
                                                        <button class="btn btn-default" type="button" data-dismiss="modal">Cancelar</button>
                                                        <button id="btnPrmGuardarCambios" class="btn btn-primary" type="button" onclick="">
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
        <script type="text/javascript" src="../../bootstrap/templete/js/bootstrap/bootstrap.min.js"></script>
        <script type="text/javascript" src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../js/jquery-sortable.js"></script>
        <script type="text/javascript" src="../../js/ajax_adminreportes.js"></script>
    </body>
</html>