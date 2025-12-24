<?php
/*
FECHA CREACION   : 04/10/2016 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion canal movimiento
*/
session_start();

?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8" />
        <title>Configuraci&oacute;n de Promociones</title>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />
    </head>
    <body>        
        <div class="superior">
            <div class="menu" style="width: 400px;" align="center">
                <ul>
                    <li>
                        <input inputmode="none"  id="btn_agregar" type="button"  onclick="fn_nuevoRegistro();" class="botonhabilitado" value="Agregar"/>
                    </li>            
                </ul>        
            </div> 
            <div class="tituloPantalla">
                <h1>Configuraci&oacute;n Canal Movimiento</h1>
            </div>   
        </div>
        <div class="contenedor">
            <div class="inferior">                           
                <br/>
                <div class="panel panel-default" id="botonesTodos">
                    <div class="panel-heading">
                        <div class="row">                            
                            <div id="opciones_estados" class="btn-group" data-toggle="buttons">
                                <label id="opcion_1" class="btn btn-default btn-sm active" onclick="fn_cargaDetalle('Activo');">
                                    <input inputmode="none"  id="opt_Activos" type="radio" value="1" checked="checked" autocomplete="off" name="estados">Activos
                                </label>
                                <label id="opcion_2" class="btn btn-default btn-sm" onclick="fn_cargaDetalle('Inactivo');">
                                    <input inputmode="none"  id="opt_Inactivos" type="radio" value="2" autocomplete="off" name="estados">Inactivos
                                </label>
                                <label id="opcion_3" class="btn btn-default btn-sm" onclick="fn_cargaDetalle('Todos');">
                                    <input inputmode="none"  id="opt_Todos" type="radio" value="0" autocomplete="off" name="estados">Todos
                                </label>
                            </div>
                        </div>
                    </div>
                </div>                         
            </div>           
            <div id="Tbl_ConfigCanalMovimiento">
                <table class="table table-bordered table-hover" id="TblDetalle_ConfigCanalMovimiento" border="1" cellpadding="1" cellspacing="0">
                </table>
            </div>
        </div>
        
        <div id="ModalConfiguracionCanalMovimiento" class="modal fade" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 id="tituloModal" class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" id="check_isactive"/></h6></div>                    
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
                            <div class="col-xs-2 text-right"><h5>C&oacute;digo:</h5></div>
                            <div class="col-xs-9">
                                <div class="form-group">
                                    <input inputmode="none"  maxlength="100" type="text" class="form-control" id="txt_codigo" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-2 text-right"><h5>Valor:</h5></div>
                            <div class="col-xs-9">
                                <div class="form-group">
                                    <input inputmode="none"  maxlength="100" type="text" class="form-control" id="txt_valor" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="btn_accion" class="modal-footer"></div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->                
    
        <input inputmode="none"  type="hidden" id="hdn_IDConfiguracionCanalMovimiento"/>
        
    <script type="text/javascript" src="../../js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
    <script type="text/javascript" src="../../js/ajax_ConfiguracionCanalMovimiento.js"></script>
        
    </body>
</html>

