<html>
<head>

    <link rel="stylesheet" type="text/css" href="../../css/buttons.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="../../css/adminReportesFidelizacionVarios.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/templete/css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css"/>

    <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>


</head>
<body>
<div class="superior">
    <div class="menu" align="center" style="width: 300px;"></div>
    <div class="tituloPantalla">
        <h1>Reportes</h1>
    </div>
</div>


<div class="container">
    <div class="row">
        <h1></h1>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-xs-4">
            <div class="container-fluid">
                <div class="well no-padding">
                    <div>
                        <ul class="nav nav-list nav-menu-list-style" id="filtrosC">
                            <li><label class="tree-toggle nav-header glyphicon-icon-rpad">
                                    <span class="glyphicon glyphicon-folder-close m5"></span>Reportes Mensuales
                                    <span class="menu-collapsible-icon glyphicon glyphicon-chevron-down"></span></label>
                                <ul class="nav nav-list tree bullets" id="reportes">

                                    <li><a href="#" data-identificador="balance_store">Consumo De Saldo Tiendas</a></li>
                                    <li><a href="#" data-identificador="consumption_total_store">Consumo De Total
                                            Tiendas</a></li>
                                    <li><a href="#" data-identificador="consumption_store">Consumo Por Tienda</a></li>
                                    <li><a href="#" data-identificador="best_clients">Mejores Clientes</a></li>
                                    <li><a href="#" data-identificador="consumption_points">Consumo de Puntos- Productos
                                            Canjeados</a></li>
                                    <li><a href="#" data-identificador="pvp_month">PVP Mensual</a></li>
                                    <li><a href="#" data-identificador="consumption_points_resumen">Resumen de Consumo
                                            de Puntos</a>

                                </ul>
                            </li>
                            <li class="divider"></li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-7" id="filtrosR" style="display: none">
            <div class="container-fluid"></div>
            <div class="col-lg-12 col-md-12 sortable-layout">
                <div class="tabs mb20">
                    <div id="myTabContent2" class="tab-content">
                        <div class="panel panel-default toggle panelMove panelClose panelRefresh">
                            <div class="panel-heading" style="background-color: #303946">
                                <h4 class="panel-title" align="center" style="font-weight: bold;color:white"
                                    id="titulofiltros"></h4>
                            </div>
                            <div class="panel-body pt0 pb0">
                                <div id="divFiltros" style="height: 90px; margin-top: 10px;" class="form-group">

                                    <div class="form-group col-lg-5 col-md-5" id="Desde">
                                        <label for="rprmDesde" class="control-label">Desde</label>
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i
                                                        class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  value="" class="form-control" name="rprmDesde" id="rprmDesdeR"
                                                   placeholder="Desde" type="text"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-1 col-md-1"></div>
                                    <div class="form-group col-lg-5 col-md-5" id="Hasta">
                                        <label for="rprmHasta" class="control-label">Hasta</label>
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i
                                                        class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  value="" class="form-control" name="rprmHasta" id="rprmHastaR"
                                                   placeholder="Hasta" type="text"/>
                                        </div>
                                    </div>

                                    <div class="col-lg-5 col-md-5 input-prepend form-group" id="idTienda">
                                        <label class="control-label">Local</label>
                                        <select id="rprmTienda" name="rprmTienda" class="form-control">

                                        </select>
                                    </div>
                                    <div class="form-group col-lg-1 col-md-1"></div>
                                    <div class="col-lg-5 col-md-5 input-prepend form-group" id="idProducto">
                                        <label class="control-label">Producto</label>
                                        <select id="rprmProducto" name="rprmProducto" class="form-control">
                                        </select>
                                    </div>

                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4 col-md-offset-5">
                                        <div class="input-group">
                                            <button id="btnReporte" class="btn btn-success mr5 mb10"></span>Generar
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

        <div class="row" id="InformacionReportes" style="display: none">
            <div class="col-lg-11 col-md-12 sortable-layout">
                <div>
                    <div class="tabs mb20">
                        <div id="myTabContent2" class="tab-content">
                            <div class="tab-pane fade active in">
                                <div class="panel panel-default toggle panelMove panelClose panelRefresh">
                                    <div class="panel-heading" style="background-color: #303946">
                                        <h4 class="panel-title" align="center" style="font-weight: bold; color:white"
                                            id="tituloTabla"></h4>
                                    </div>
                                    <div class="panel-body pt0 pb0">
                                        <div class="panel panel-default toggle panelMove panelClose panelRefresh"
                                             id="Prueba">
                                            <br>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input inputmode="none"  type="hidden" id="reporte1" name="reporte1" value="0">

        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../../imagenes/admin_resources/progressBar.gif"/>
            </div>
        </div>
        <!-- JavaScript -->
        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>


        <script type="text/javascript" src="../../js/alertify.js"></script>

        <script type="text/javascript" src="../../js/jquery.dataTables.js"></script>
        <script type="text/javascript"
                src="../../bootstrap/templete/plugins/tables/datatables/dataTables.bootstrap.js"></script>

        <script type="text/javascript" src="../../js/dataTables.buttons.js"></script>
        <script type="text/javascript" src="../../js/buttons.html5.js"></script>

        <script type="text/javascript" src="../../js/jszip.min.js"></script>
        <script type="text/javascript" src="../../js/pdfmake.min.js"></script>
        <script type="text/javascript" src="../../js/vfs_fonts.js"></script>

        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/templete/plugins/ui/bootbox/bootbox.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>

        <script type="text/javascript" src="../../js/ajax_reporteFidelizacionVarios.js"></script>


    </div>


</body>
</html>