<?php
session_start();
include_once '../../seguridades/seguridad.inc';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8"/>
        <title>Modificadores</title>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
        <link rel="stylesheet" type="text/css" href="../../css/jquery-ui.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/jquery-ui-sortable.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/productos.css"/>
    </head>
    <body>
        <div class="superior">
            <div class="menu" align="center" style="width: 400px;">
                <ul>
                    <li>
                        <button id="agregar" onclick="actualizarListaModificadores();" class="botonMnSpr l-basic-elaboration-briefcase-download">
                            <span>Modif.</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>Modificadores Domicilio</h1>
            </div>
        </div>

        <div class="inferior">
            <!-- Tabla Plus -->
            <div id="contenedor_plus" class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-sm-8">
                            <div id="filtroClasificacion" class="btn-group" data-toggle="buttons"></div>
                        </div>
                    </div>
                </div>
                <div id="cntDepartamentos" class="panel-body">
                    <table id="tblDepartamentos" class="table table-bordered table-hover"></table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mdlDepartamento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" style="width:600px;">
                <div class="modal-content">
                    <div class="modal-header active panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title active" id="tituloDepartamento"></h4>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-7"></div>
                        <div class="col-md-3 text-right">
                            <div class="btn-group">
                                <h6 class="text-right"><b>Está Activo? <input inputmode="none"  type="checkbox" id="inEstadoDepartamento"></b></h6>
                            </div>
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-3 text-right"><h6><b>Departamento:</b></h6></div>
                        <div class="col-xs-7">
                            <div class="form-group">
                                <input inputmode="none"  type="text" id="inDescripcionDepartamento" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div class="modal-footer panel-footer">
                        <button id="btnGuardarDepartamento" type="button" class="btn btn-primary" onclick="">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando" ng-show="cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../../imagenes/admin_resources/progressBar.gif" />
            </div>
        </div>

        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../js/jquery-latest.min.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../js/jquery-sortable.js"></script>
        <script type="text/javascript" src="../../js/jquery.uitablefilter.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/switch.js"></script>
        <script type="text/javascript" src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="../../js/chosen.proto.min.js"></script>
        <script type="text/javascript" src="../../js/ajax_modificadores.js"></script>
    </body>
</html>