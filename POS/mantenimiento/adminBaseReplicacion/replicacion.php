<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Replicación</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1 user-scalable=no">
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/DanGrossmanDateRangePicker/daterangepicker.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/templete/css/plugins.css" />
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap-editable.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css" />
        <link rel="stylesheet" type="text/css" href="../../css/jquery.treetable.css" />
        <link rel="stylesheet" type="text/css" href="../../css/jquery.treetable.theme.default.css" />
    </head>
    <body>
        <div class="page-content-wrapper">
            <div class="page-content-inner small">
                <div id="page-header" class="clearfix">
                    <div class="page-header">
                        <br/>
                        <div class="tituloPantalla">
                            <h1>REPLICACION</h1>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div id="tab_principal" class="tabs">
                            <ul id="pestanasMod" class="nav nav-tabs">
                                <li id="liAzure" class="" onclick="recargarAzure()">
                                    <a href="#tabAzure" data-toggle="tab">
                                        <h5>Azure</h5>
                                    </a>
                                </li>
                                <li id="liDistribuidor" class="" onclick="recargarDistribuidor()">
                                    <a href="#tabDistribuidor" data-toggle="tab">
                                        <h5>Distribuidor</h5>
                                    </a>
                                </li>
                                <li id="liTiendas" class="" onclick="recargarTiendas()">
                                    <a href="#tabTiendas" data-toggle="tab">
                                        <h5>Tiendas</h5>
                                    </a>
                                </li>
                            </ul>
                            <div id="tabContentMod" class="tab-content">
                                <div id="tabAzure" class="tab-pane fade">
                                    <br />
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label id="lblFechasAzure" class="col-md-4 control-label" for="inpFechasAzure">Desde - Hasta:</label>
                                            <div class="input-prepend input-group col-lg-7">
                                                <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                <input inputmode="none"  type="text" value="" class="form-control text-center" id="inpFechasAzure" readonly="readonly" style="cursor: pointer" />
                                            </div>
                                        </div>
                                    </div>
                                    <br /><br /><br />
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                                <b>Módulos:&nbsp;</b>
                                                <select id="slcAzure"></select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div style="text-align: right">
                                                <div id="opcionesAzureReplicacion" class="btn-group" data-toggle="buttons"></div>
                                            </div>
                                        </div>
                                        <br />
                                        <div id='divTableAzure'></div>
                                    </div>
                                </div>
                                <div id="tabDistribuidor" class="tab-pane fade">
                                    <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label id="lblFechasDistribuidor" class="col-md-4 control-label" for="inpFechasDistribuidor">Desde - Hasta:</label>
                                            <div class="input-prepend input-group col-lg-7">
                                                <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                <input inputmode="none"  type="text" value="" class="form-control text-center" id="inpFechasDistribuidor" readonly="readonly" style="cursor: pointer" />
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <br />
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                                <b>Módulos:&nbsp;</b>
                                                <select id="slcDistribuidor"></select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div style="text-align: right">
                                                <div id="opcionesDistribuidorReplicacion" class="btn-group" data-toggle="buttons"></div>
                                            </div>
                                        </div>
                                        <br />
                                        <div id='divTableDistribuidor'></div>
                                    </div>         
                                </div>

                                <div id="tabTienda" class="tab-pane fade">
                                    <br />
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label id="lblFechasTienda" class="col-md-4 control-label" for="inpFechasTienda">Desde - Hasta:</label>
                                            <div class="input-prepend input-group col-lg-7">
                                                <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                <input inputmode="none"  type="text" value="" class="form-control text-center" id="inpFechasTienda" readonly="readonly" style="cursor: pointer" />
                                            </div>
                                        </div>
                                    </div>
                                    <br /><br /><br />
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                                <b>Módulos:&nbsp;</b>
                                                <select id="slcTiendas"></select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div style="text-align: right">
                                                <div id="opcionesTiendaReplicacion" class="btn-group" data-toggle="buttons"></div>
                                            </div>
                                        </div>
                                        <br />
                                        <div id='divTableTienda'></div>
                                    </div>                                                 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Estado PING Linked Servers -->
        <div id="modalEstadoReplicacion" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Estado Replicación</h4>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Estado PING Linked Servers -->
        <div id="modalAnulacionLoteDistribuidor" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Inactivar Lote<span id="tituloModalNumeroLote"></span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <label for="observacionAnulacionLote"></label>
                                <textarea placeholder="Describa el motivo por el cual se desea anular el lote."
                                          class="form-control" name="observacionAnulacionLote"
                                          id="observacionAnulacionLote" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="btnEjecutarAnulacionLote">Anular Lote</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando" style="display:none">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../../imagenes/admin_resources/progressBar.gif"/>
            </div>
        </div>

        <script src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/DanGrossmanDateRangePicker/daterangepicker.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-editable.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/switch.js"></script>
        <script type="text/javascript" src="../../js/jquery.treetable.js"></script>
        <script type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="../../js/chosen.proto.min.js"></script>
        <script type="text/javascript" src="../../js/alertify.js"></script>        
        <script type="text/javascript" src="../../js/ajax_replicacion.js"></script>
    </body>
</html>