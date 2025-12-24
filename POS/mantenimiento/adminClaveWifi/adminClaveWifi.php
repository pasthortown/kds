<?php
//session_start();
//include_once '../../seguridades/seguridad.inc';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8"/>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css"/>
        <!-- Plugins stylesheets (all plugin custom css) -->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/plugins.css" rel="stylesheet" />
        <!-- Main stylesheets (template main css file) -->
        <link rel="stylesheet" type="text/css" href="c../../bootstrap/css/main.css" rel="stylesheet" />
        <!-- Custom stylesheets ( Put your own changes here ) -->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/custom.css" rel="stylesheet" />
        <!-- Editable -->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap-editable.css" />

        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
    </head>
    <body>
        <div class="superior">
            <div class="menu" align="center" style="width: 300px;">
                <ul>
                    <li>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>Clave Wi-Fi</h1>
            </div>
        </div>
        
        <div class="inferior">  
            <div class="container">   
                <div role="tabpanel">
                    <br/>
                    <ul id="pestanasClaveWifi" class="nav nav-tabs" role="tablist">
                        <li id="tabClaves" role="presentation" class="active">
                            <a href="#administracionClaves" aria-controls="administracionClaves" role="tab" data-toggle="tab">
                                <h5>Administración de Claves</h5>
                            </a>
                        </li>
                        <li id="tabRestaurantes" role="presentation">
                            <a href="#administracionRestaurantes" aria-controls="administracionRestaurantes" role="tab" data-toggle="tab" onclick="cargarRestaurantesWifi()">
                                <h5>Administración de Restaurantes</h5>
                            </a>
                        </li>
                    </ul>
                    <div id="TabContentMod" class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="administracionClaves">
                            <br/>
                            <div id="contenedor_plus" class="panel panel-default">                
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div id="filtroAnio" class="btn-group" data-toggle="buttons"></div>
                                        </div>
                                    </div>
                                </div>
                                <div id="tablaClavesSemana" class="panel-body">
                                    <table id="tblSemanas" class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="active">
                                                <th width="25%">Semana</th>
                                                <th width="25%">Desde</th>
                                                <th width="25%">Hasta</th>
                                                <th width="25%">Clave</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="administracionRestaurantes">
                            <h5 style="margin: 30px 20px 0px 15px"><b>Seleccionar restaurantes <u>sin</u> Wi-Fi:</b></h5>
                            <br/>
                            <div style="width: 900px; height: 302px; overflow-y: auto; margin-left: 50px;">
                                <div id="listaRestaurantes" class="list-group"></div>
                            </div>
                            <button type="button" class="btn btn-primary" style="float: right; margin: 20px 200px;" onclick="guardarRestaurantesWifi()">Guardar</button>
                        </div>
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

        <script type="text/javascript" src="../../bootstrap/js/bootstrap-editable.min.js"></script>

        <script type="text/javascript" src="../../bootstrap/js/switch.js"></script>
        <script type="text/javascript" src="../../js/alertify.js"></script>

        <script type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="../../js/chosen.proto.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/templete/plugins/ui/calendar/fullcalendar.min.js"></script>
        <script type="text/javascript" src="../../js/administracion/administracion.claveWifi.js"></script>
    </body>
</html>