<?php
session_start();
include_once '../../seguridades/seguridad.inc';
?>
<html>
    <head>
        <title>Administraci&oacute;n Men&uacute;</title>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
        <link rel="stylesheet" type="text/css" href="../../css/jquery-ui.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
        <link media="all" href="../../css/progressBar.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="../../css/productos.css"/>
    </head>
    <body>
        <div class="superior">
            <div class="menu" style="width: 400px;" align="center">
                <ul>
                    <li>
                        <button id="agregar" class="botonMnSpr quitarPaddingBottom l-basic-elaboration-document-plus" onclick="agregarMenu()">
                            <span>Nuevo</span>
                        </button>
                    </li>
                    <li>
                        <button id="editar" class="botonMnSpr quitarPaddingBottom l-basic-sheet-multiple" onclick="fn_duplicarmenu()">
                            <span>Copiar</span>
                        </button>
                    </li>              
                </ul>        
            </div> 
            <div class="tituloPantalla">
                <h1>P&aacute;gina de Men&uacute;</h1>
            </div>   
        </div>
        <!-- Contenedor Grid -->
        <div class="contenedor">
            <div class="inferior">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-8">
                                <h5>
                                    <div id='opciones_estado' class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-default btn-sm active" onclick="cargarMenusPorEstado('Activo');">
                                            <input inputmode="none"  type="radio" name="options" id="opt_Activos" autocomplete="off" value="1" />Activos
                                        </label>
                                        <label class="btn btn-default btn-sm" onclick="cargarMenusPorEstado('Inactivo');">
                                            <input inputmode="none"  type="radio" name="options" id="opt_Inactivos" autocomplete="off" value="2" />Inactivos
                                        </label>
                                        <label id='opciones_1' class="btn btn-default btn-sm " onclick="cargarTodosMenus()">
                                            <input inputmode="none"  type="radio" name="options" id="opt_Todos" autocomplete="off" value="0" />Todos
                                        </label>
                                    </div>
                                </h5>
                            </div>									
                        </div> 
                    </div>
                    <div class="panel-body">
                        <div id="aplicacion" class="center-block">    
                            <table id="tabladetallemenu" class="table table-bordered table-hover"></table>                          
                        </div>
                    </div>
                    <div class="panel-footer"></div>
                </div>   
                <!-- BEGIN: Modal Modificar -->
                <div class="modal fade" id="modalmodificar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header panel-footer">
                                <button type="button" class="close" onclick="fn_cancelar()" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Modificar Men&uacute;: <label style="font-size:13px; color:#666; font-style:normal"  id="nombreMenu2"></label></h4>
                            </div>
                            <div class="modal-body">
                                <!-- <div class="row">-->
                                <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" id="check_activo" /></h6></div> 
                                <!--</div>-->
                                <div class="row">
                                    <div class="col-xs-4 text-right"><h5>Nombre Men&uacute;:</h5></div>
                                    <div class="col-xs-8">
                                        <div class="form-group">
                                            <input inputmode="none"  maxlength="50" type="text" class="form-control" name="nombreMenu" id="nombreMenu" style="text-transform:uppercase;" />
                                        </div>
                                    </div>
                                </div>  

                                <div class="row">
                                    <div class="col-xs-4 text-right"><h5>Nombre en MaxPoint:</h5></div>
                                    <div class="col-xs-8">
                                        <div class="form-group">
                                            <input inputmode="none"  maxlength="50" type="text" class="form-control" name="nombreMenuMaxMod" id="nombreMenuMaxMod" style="text-transform:uppercase;" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-4 text-right"><h5>Canal:</h5></div>
                                    <div class="col-xs-8">
                                        <div class="form-group">
                                            <select id="inClasificacion" class="form-control" style="text-transform:uppercase;"></select>  
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-4 text-right"><h5>Medio:</h5></div>
                                    <div class="col-xs-8">
                                        <div class="form-group">
                                            <select id="inMedio" class="form-control" style="text-transform:uppercase;"></select>  
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer panel-footer">
                                <button type="button" id="btnGuardar" class="btn btn-primary">Guardar</button>
                                <button type="button" id="btnCancelar" class="btn btn-default" data-dismiss="modal">Cancelar</button>        
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: Modal Modificar -->
                <!-- BEGIN: Modal Duplicar -->
                <div class="modal fade" id="modalduplicar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header panel-footer">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel2">Duplicar Men&uacute;:</h4>       
                            </div>
                            <div class="modal-body">
                                <!--<div class="row">-->
                                <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" disabled id="check_activoduplicar"/></h6></div> 
                                <!-- </div>-->
                                <div class="row">
                                    <div class="col-xs-3 text-right"><h5>Nombre Men&uacute; Original:</h5></div>
                                    <div class="col-xs-8">
                                        <div class="form-group">
                                            <input inputmode="none"  maxlength="50" type="text" class="form-control" name="nombreMenuoriginal" id="nombreMenuoriginal" disabled="disabled" />
                                            <input inputmode="none"  id="menu_id" type="hidden" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-3 text-right"><h5>Nombre Men&uacute; Duplicado:</h5></div>
                                    <div class="col-xs-8">
                                        <div class="form-group">
                                            <input inputmode="none"  maxlength="50" type="text" class="form-control" name="nombreMenuduplicar" id="nombreMenuduplicar" style="text-transform:uppercase;" />
                                        </div>
                                    </div>
                                </div> 
                                <div class="row">
                                    <div class="col-xs-3 text-right"><h5>Nombre en MaxPoint:</h5></div>
                                    <div class="col-xs-8">
                                        <div class="form-group">
                                            <input inputmode="none"  maxlength="50" type="text" class="form-control" name="nombreMenuMaxduplicado" id="nombreMenuMaxduplicado" style="text-transform:uppercase;" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-3 text-right"><h5>Canal:</h5></div>
                                    <div class="col-xs-8">
                                        <div class="form-group">
                                            <select id="selClasificacionduplicacion" class="form-control"></select>  
                                        </div>
                                    </div>
                                </div>         
                            </div>
                            <div class="modal-footer panel-footer">
                                <button type="button" class="btn btn-primary" onclick="fn_verificarduplicacion()" >Aceptar</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>        
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: Modal Duplicar -->
            </div>
        </div>
        
        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../../imagenes/admin_resources/progressBar.gif"/>
            </div>
        </div>
        
        <input inputmode="none"  type="hidden" id="menu_id_hidden"/>
        <input inputmode="none"  type="hidden" id="menu_id_nuevo"/>

        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../js/jquery-ui.js"></script>
        <script type="text/javascript" language="javascript1.1" src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../js/ajax_menu.js"></script>

    </body>
</html>