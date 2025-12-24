<html>
    <head>
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/normalize/normalize.css"/>
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/templete/css/icons.css"/>
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/templete/css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/templete/css/plugins.css"/>
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/templete/css/main.css"/>
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/templete/css/custom.css"/>
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../css/alertify.core.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../css/alertify.default.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../css/reportes.css"/>
        <title>Administración Productos</title>
    </head>
    <body>
        <div class="superior">
            <div class="menu" align="center" style="width: 300px;"></div>
            <div class="tituloPantalla">
                <h1>Reportes</h1>
            </div>
        </div>
        <div class="inferior">
            <div id="contenedor_plus" class="panel panel-default" style="min-height: 100%">
                <div class="panel-heading">
                    <div class="row">
                        <div id="cntCategoriaReporte" class="col-sm-9 btn-group"></div>
                        <div class="col-sm-3">
                            <div id="cntFormatoReportes" class="btn-group aling-btn-right" data-toggle="buttons">
                                <button id="formatoViews" class="btn btn-default" tabindex="-1" data-hide="true" data-original-title="Picture">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button id="formatoPdf" class="btn btn-default active" tabindex="-1" data-hide="true" data-original-title="Picture">
                                    <i class="fa fa-file-pdf-o"></i>
                                </button>
                                <button id="formatoExcel" class="btn btn-default" tabindex="-1" data-hide="true" data-original-title="Picture">
                                    <i class="fa fa-file-excel-o"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div id="cntParametrosReporte" class="row">
                        <div id="mnReportes" class="col-lg-3 col-md-3 col-sm-4"></div>
                        <div class="col-lg-6 col-md-6 col-sm-8">
                            <div class="panel-body pt0 pb0">
                                <h4 id="titleReporte"></h4>
                                <form id="frmParametrosReporteElementos" onsubmit="" action="../reportes/vista_reporte.php" method="post" target="_blank" style="display: none">
                                    <div id="cntParametrosReporteElementos"></div>
                                    <div>
                                        <input type="hidden" name="inView" id="inView" value="views"/>
                                        <input type="hidden" name="idReporte" id="idReporte" value="" />
                                        <button id="btnParametrosReporteElementos" class='btn btn-success mr5 mb10'>Enviar</button>
                                    </div>
                                </form>
                                <div id="cntRespuestaParametros" class="row" style="display: none">
                                    <h5>&nbsp;&nbsp;&nbsp;&nbsp;No existen parametros configurados para este reporte</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="cntRespuestaParametros" class="row" style="display: none">
                    <p>No existen parametros configurados para este reporte</p>
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
        <script type="text/javascript" src="../../bootstrap/templete/js/bootstrap/bootstrap.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
        <script type="text/javascript" src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../js/calendario.js"></script>
        <script type="text/javascript" src="../../js/idioma.js"></script>
        <script type="text/javascript" src="../../js/reportes.js"></script>

    </body>
</html>