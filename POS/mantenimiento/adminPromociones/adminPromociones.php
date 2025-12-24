<?php

/*
FECHA CREACION   : 16/07/2016 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion de promociones
*/

session_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8" />
        <title>Configuraci&oacute;n de Promociones</title>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
    </head>
    <body>
        
        <input inputmode="none"  id="sessionIDCadena" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
        
        <div class="superior">
            <div class="menu" style="width: 400px;" align="center">
                <ul>
                    <li>
                        <input inputmode="none"  id="btn_agregar" type="button"  onclick="fn_agregarPromocion();" class="botonhabilitado" value="Agregar"/>
                    </li>            
                </ul>        
            </div> 
            <div class="tituloPantalla">
                <h1>Cupones</h1>
            </div>   
        </div>
        <div class="contenedor">
            <div class="inferior">                           
                <br/>
                <div class="panel panel-default" id="botonesTodos">
                    <div class="panel-heading">
                        <div class="row">                            
                            <div id="opciones_estados" class="btn-group" data-toggle="buttons">
                                <label id="opcion_1" class="btn btn-default btn-sm active" onclick="fn_detallePromociones(1);">
                                    <input inputmode="none"  id="opt_Activos" type="radio" value="1" checked="checked" autocomplete="off" name="estados">Activos
                                </label>
                                <label id="opcion_2" class="btn btn-default btn-sm" onclick="fn_detallePromociones(2);">
                                    <input inputmode="none"  id="opt_Inactivos" type="radio" value="2" autocomplete="off" name="estados">Inactivos
                                </label>
                                <label id="opcion_3" class="btn btn-default btn-sm" onclick="fn_detallePromociones(0);">
                                    <input inputmode="none"  id="opt_Todos" type="radio" value="0" autocomplete="off" name="estados">Todos
                                </label>
                            </div>
                        </div>
                    </div>
                </div>                         
            </div>
           
            <div id="tabla_promociones">
                <table class="table table-bordered table-hover" id="tabla_detallePromociones" border="1" cellpadding="1" cellspacing="0">
                </table>
            </div>
        </div>
        
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="lbl_descripcion"></h4>       
                    </div>
                    
                    <div class="modal-body">
                        <ul id="pestanas" class="nav nav-tabs">
                            <li role="presentation" id="tabDetalle" class="active"><a data-toggle="tab" href="#tab_detalle">Detalle</a></li>
                            <li role="presentation" id="tabRestaurantes"><a data-toggle="tab" href="#tab_aplica">Aplica en Restaurante</a></li>                            
                        </ul>
                        <div class="tab-content" id="tabContenedor">
                            <div id="tab_detalle" role="tabpanel" class="tab-pane active">                                
                                <p>
                                    <div class="modal-body">              
                                        <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" id="check_isactive"/></h6></div>                    
                                        <br>
                                        <div class="row">                      
                                            <div class="col-xs-1 text-right"></div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="FechaInicial" class="control-label">Fecha Inicio</label>
                                                    <div class="input-prepend input-group">
                                                        <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                        <input inputmode="none"  type="text" value="" class="form-control" name="FechaInicial" id="FechaInicial" placeholder="Fecha Inicio"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="FechaFinal" class="control-label">Fecha Fin</label>
                                                    <div class="input-prepend input-group">
                                                        <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                        <input inputmode="none"  type="text" value="" class="form-control" name="FechaFinal" id="FechaFinal" placeholder="Fecha Fin"/>
                                                    </div>
                                                </div>
                                            </div>                      
                                        </div>
                                            <br>
                                        <div class="row">
                                            <div class="col-xs-2 text-right"><h5>Descripci&oacute;n:</h5></div>
                                            <div class="col-xs-9">
                                                <div class="form-group">
                                                    <input inputmode="none"  maxlength="100" type="text" class="form-control" id="txt_descripcion" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-1"><h5 class="text-right">Contenido:</h5></div>
                                        </div> 
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                    
                                                    <textarea class="form-control custom-control" rows="5" style="resize:none" id="txt_contenido"></textarea>  
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <h5><input inputmode="none"  type="checkbox" id="check_etiqueta" onclick="fn_mostrarEtiqueta();" />&nbsp; Mostrar etiqueta:</h5>
                                            </div> 
                                        </div> 
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                    
                                                    <!--<input inputmode="none"  maxlength="100" type="text" class="form-control" id="txt_etiqueta" disabled="disabled" />-->
                                                    <textarea class="form-control custom-control" rows="4" style="resize:none" id="txt_etiqueta" disabled="disabled"></textarea>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                </p>
                            </div>
                            <div id="tab_aplica" class="tab-pane fade"> 
                                <br>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-6"><h6><b>Tienda:</b></h6></div>
                                    <div class="col-md-3 text-right">Marcar Todos &nbsp 
                                        <input inputmode="none"  id="chck_todos" name="chck_todos" onclick="fn_seleccionarTodosRestaurantes()" value="1" type="checkbox">
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div id="rdo_restaurantes" class="btn-group" data-toggle="buttons">
                                            <label id="rdo_localizacion" class="btn btn-default btn-sm active" onclick="fn_cargarRestaurante(0);">
                                                <input inputmode="none"  type="radio" autocomplete="off" name="rdo_rest_todos" checked="checked">Todos
                                            </label>
                                            <label class="btn btn-default btn-sm" onclick="fn_cargarRestaurante(1);">
                                                <input inputmode="none"  type="radio" autocomplete="off"  name="rdo_rest_uio">Quito
                                            </label>
                                            <label class="btn btn-default btn-sm" onclick="fn_cargarRestaurante(2);">
                                                <input inputmode="none"  type="radio" autocomplete="off"  name="rdo_rest_gye">Guayaquil
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                            <br/>
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-xs-12 col-md-10">                                	
                                    <div style="height: 260px; overflow-y: auto;">
                                        <div id="lst_restaurantes" class="list-group"></div>
                                    </div>
                                </div>
                                <div class="col-md-2"></div>
                            </div>                            
                        </div>
                    </div>
                    <div id="btn_accion" class="modal-footer"></div>
                </div>
            </div>
        </div>
    </div>
        
        <input inputmode="none"  type="hidden" id="hdn_checkedTodos"/>
        <input inputmode="none"  type="hidden" id="hdn_IDRestaurante"/>
        <input inputmode="none"  type="hidden" id="hdn_DescripcionRestaurante"/>
        <input inputmode="none"  type="hidden" id="hdn_IDPromocion"/>
        
        <script type="text/javascript" src="../../js/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script> 
        <script type="text/javascript" src="../../bootstrap/js/switch.js"></script>
        <script type="text/javascript" src="../../js/ajax_adminPromociones.js"></script>
        
    </body>
</html>

