<html>
    <head>
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/templete/css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css" />
    </head>
    <body>
        <div class="superior">
            <div class="menu" align="center" style="width: 300px;"></div>
            <div class="tituloPantalla">
                <h1>Reportes Diarios</h1>
            </div>
        </div>
        <div class="inferior">
            <div id="contenedor_plus" class="panel panel-default" style="min-height: 100%">
                <div class="panel-body">
                    <div id="divParametros" style="height: 90px; margin-top: 10px;">
                        <div class="form-group col-lg-3 col-md-3">
                            <label for="rprmDesde" class="control-label">Desde</label>
                            <div class="input-prepend input-group">
                                <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                <input inputmode="none"  value="" class="form-control" name="rprmDesde" id="rprmDesde" placeholder="Desde" type="text" />
                            </div>
                        </div>
                        <div class="form-group col-lg-1 col-md-1"></div>
                        <div class="form-group col-lg-3 col-md-3">
                            <label for="rprmHasta" class="control-label">Hasta</label>
                            <div class="input-prepend input-group">
                                <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                <input inputmode="none"  value="" class="form-control" name="rprmHasta" id="rprmHasta" placeholder="Hasta" type="text" />
                            </div>
                        </div>
                    </div>
                    <div style="margin-left: 15px;">
                        <button id="btnReporteGenerar" class="btn btn-success mr5 mb10">Generar</button>
                        <button id="btnReporteExportar" class="btn btn-success mr5 mb10" style="margin-left: 20px; display: none;">Exportar</button>
                    </div>
                    <div id="divReporte">
                        <table id="tblReporte">
                            
                        </table>
                    </div>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 sortable-layout">
                <div>
                    <div class="tabs mb20">
                        <ul id="tabsSettings" class="nav nav-tabs">
                            <li class="active">
                                <a href="#tabSettingsTransacciones" data-toggle="tab" aria-expanded="true"><h5>Transacciones</h5></a>
                            </li>
                            <li class="lead">
                                <a href="#tabSettingsProducto" data-toggle="tab"><h5>Productos</h5></a>
                            </li>
                        </ul>
                        <div id="myTabContent2" class="tab-content">
                            <div class="tab-pane fade active in" id="tabSettingsTransacciones">
                                <!--Inicio: DailySummary-->
                                <div class="panel panel-default toggle panelMove panelClose panelRefresh">
                                    <!-- Start .panel -->
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Transacciones por Local</h4>
                                    </div>
                                    <div class="panel-body pt0 pb0">
                                        <div class="panel panel-default toggle panelMove panelClose panelRefresh">

                                            <!-- TABLA TRANSACCIONES -->
                                            <table id="tblDailySummary" class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Week</th>
                                                        <th>Store</th>
                                                        <th>Point Earned Orders</th>
                                                        <th>Point Earned Coupons</th>
                                                        <th>Point Earned Balance</th>
                                                        <th>Point Redeemed</th>
                                                        <th>Point Expired</th>
                                                        <th>Point Reversed Coupon</th>
                                                        <th>Point Reversed Order</th>
                                                        <th>Preregistered Pos</th>
                                                        <th>Subscribed After Pos</th>
                                                        <th>Registered App</th>
                                                        <th>Registered Web</th>
                                                        <th>Referrals</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                            <!-- END TABLA TRANSACCIONES -->

                                        </div>
                                    </div>
                                <!--Fin: DailySummary-->
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tabSettingsProducto">
                                 <!--Inicio: DailySummaryProduct-->
                                <div class="panel panel-default toggle panelMove panelClose panelRefresh">
                                    <!-- Start .panel -->
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Transacciones Producto</h4>
                                    </div>
                                    <div class="panel-body pt0 pb0">
                                        <div class="panel panel-default toggle panelMove panelClose panelRefresh">

                                            <!-- TABLA PRODUCTS -->
                                            <table id="tblDailySummaryProducts" class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Week</th>
                                                        <th>Store</th>
                                                        <th>Product</th>
                                                        <th>Num Orders</th>
                                                        <th>Num Products</th>
                                                        <th>Point Redeemed</th>
                                                        <th>Point Earned Orders</th>
                                                        <th>Point Earned Coupons</th>
                                                        <th>Point Earned Balance</th>
                                                        <th>Num Redeemed</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                            <!-- END TABLA PRODUCTS -->

                                        </div>
                                    </div>
                                <!--Fin: DailySummaryProduct-->
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
        <!-- JavaScript -->
        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../bootstrap/templete/plugins/tables/datatables/jquery.dataTables.js"></script>
        <script type="text/javascript" src="../../bootstrap/templete/plugins/tables/datatables/dataTables.bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/templete/plugins/ui/bootbox/bootbox.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
        <script type="text/javascript" src="../../js/ajax_reporteFidelizacionTransacciones.js"></script>
    </body>
</html>