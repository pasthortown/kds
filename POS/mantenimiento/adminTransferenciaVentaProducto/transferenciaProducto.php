<?php
session_start();
include_once '../../seguridades/seguridad.inc';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8"/>
        <title>Administración Plus</title>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css"/>

        <link rel="stylesheet" type="text/css" href="../../css/jquery-ui.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/jquery-ui-sortable.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/productos.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/textArea.css" />
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
    </head>
    <body>
        <div class="superior">
            <div class="menu" align="center" style="width: 25%;">
                <ul>
                    <li>
                        <button id="agregar" onclick="agregarLocalTransferenciaVenta()" class="botonMnSpr l-basic-elaboration-document-plus">
                            <span>Nuevo</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>PRODUCTOS</h1>
            </div>
        </div>

        <div class="inferior">
            <!-- Tabla Plus -->
            <div id="contenedor_plus" class="panel panel-default">
                <div id="cntColeccionTransferenciaVentaCadena" class="panel-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="btn-group">
                                <h5><b>Transferencia Ventas</b></h5>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <select id="lstColeccionCadena" class="form-control" onchange="cargarLocalesConfiguradosTransferenciaVentaPorCadena('Activos')"></select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="container">
                <div class="panel-heading">
                    <h4>Productos Configurados</h4>
                    <input inputmode="none"  type="hidden" id="oldDestino" value="">
                </div>
                <div class="panel-body">
                    <div class="tab-content">
                        <div id="menu1" class="tab-pane fade in active">
                            <div class="panel panel-default" id="botonesActivosInactivos">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-sm-3"><h5>Lista de Transferencia de Ventas</h5>
                                            <div id="opciones_estados" class="btn-group" data-toggle="buttons">
                                                <label class="btn btn-default btn-sm active" onclick="fn_OpcionSeleccionada('Activos');"><input inputmode="none"  id="opt_Activos" type="radio" value="Activos" checked="checked" autocomplete="off" name="estados" />Activos</label>
                                                <label class="btn btn-default btn-sm" onclick="fn_OpcionSeleccionada('Inactivos');"><input inputmode="none"  id="opt_Inactivos" type="radio" value="Inactivos" autocomplete="off" name="estados"  />Inactivos</label>
                                                <label class="btn btn-default btn-sm" onclick="fn_OpcionSeleccionada('Todos');"><input inputmode="none"  id="opt_Todos" type="radio" value="Todos" autocomplete="off" name="estados"/>Todos</label>
                                                <input inputmode="none"  id="txt_hidden_ID_ColeccionCadena" type="hidden" value=""/>
                                                <input inputmode="none"  id="txt_hidden_ID_ColeccionDeDatosCadena" type="hidden" value=""/>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="div_tabla_transferenciaVentas">
                                <div id="tabla_transferenciaVentas_length"></div>
                            <!--<button class="btn btn-primary" onclick="fn_inactivarTransferenciaVentas(0)" id="btnInactivar" disabled="true">Inactivar &nbsp;<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>-->


                                <table class="table table-bordered table-hover" style="cursor:pointer" id="tblLocalesConfiguradosTransferenciaVenta" border="1" cellpadding="1" cellspacing="0"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="modal fade" id="mdlTransferenciaVentaLocal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" style="width:800px;">
                <div class="modal-content">
                    <div class="modal-header active panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title active" id="ttlModalTransferenciaVenta">Transferencia de Venta</h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-0"><h4><b></b></h4></div>
                            <div class="col-xs-6"><h4><b id="lblOrigen">Origen</b></h4></div>
                            <div class="col-xs-4"><h4><b id="lblDestino">Destino</b></h4></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-1"><h6><b>Origen</b></h6></div>
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <input inputmode="none"  id="inLocalOrigen" type="text" class="form-control" value="" style="display: none;" disabled="disabled"/>
                                    <select id="lstLocalOrigen" class="form-control" style="display: none"></select>
                                </div>
                            </div>
                            <div class="col-xs-2 text-right"><h6><b>Destino</b></h6></div>
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <select id="lstLocalDestino" class="form-control"></select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer panel-footer">
                          <button id="btnEliminar" type="button"  class="btn btn-danger" onclick="">Eliminar</button>
                        <button id="btnCambiarEstado" type="button"  class="" onclick="">Inactivar</button>
                        <button id="btnGuardar" type="button" class="btn btn-primary" onclick="">Guardar</button>
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
        
        
        <input inputmode="none"  type="hidden" value="" id="cadenaOrigen">
        <input inputmode="none"  type="hidden" value="" id="cadenaDestino">



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
                <script type="text/javascript" src="../../js/calendario.js"></script>
                <script type="text/javascript" src="../../js/idioma.js"></script>
                <script type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
                <script type="text/javascript" src="../../js/chosen.proto.min.js"></script>
                <script type="text/javascript" src="../../js/ajax_transferenciaProducto.js"></script>
                </body>
                </html>