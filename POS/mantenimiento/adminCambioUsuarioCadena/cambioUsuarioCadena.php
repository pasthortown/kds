<?php

/*
FECHA CREACION   : 07/05/2018 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Pantalla que realiza el cambio de cadena a usuarios MP 
*/

session_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8" />
        <title>Cambiar Usuario de Cadena</title>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
    </head>
    <body>
        
        <input inputmode="none"  type="hidden" id="hdn_IDUsuario"/>
        <input inputmode="none"  type="hidden" id="hdn_IDCadena"/>
        
        <div class="superior">            
            <div class="tituloPantalla">
                <h1>Cambiar Usuario de Cadena</h1>
            </div>   
        </div>
        <div class="contenedor">
            <br>
            <div class="panel panel-default text-left">
                <div class="panel-body">
                    <div class="col-xs-2 text-right"><h5>Perfil:</h5></div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            <select id="selectPerfil" class="form-control" ></select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="tablaUsuarios" class="col-md-12">
                <table class="table table-bordered table-hover" id="tablaDetalleUsuarios" border="1" cellspacing="0" width="100%">
                </table>
            </div>
        </div>
        
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" >
            <div class="modal-dialog modal-lg" style="height: 700px;">
                <div class="modal-content">
                    <div class="modal-header panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="lbl_descripcion"></h4>       
                    </div>
                    
                    <div class="modal-body">
                        <ul id="pestanas" class="nav nav-tabs">
                            <li role="presentation" id="tabDetalle" value="0" class="active"><a data-toggle="tab" href="#tab_detalle" onclick="posicionTab(1);">Datos Usuario</a></li>
                            <li role="presentation" id="tabRestaurantes" value="1"><a data-toggle="tab" href="#tab_aplica" onclick="posicionTab(2);">Restaurantes Asignado</a></li>
                            <li role="presentation" id="tabCadena" value="2"><a data-toggle="tab" href="#tab_cadena" onclick="posicionTab(3);">Agregar Restaurante</a></li>
                        </ul>
                        <div class="tab-content" id="tabContenedor">
                            <div id="tab_detalle" role="tabpanel" class="tab-pane active">                                
                                <p>
                                    <div class="modal-body"> 
                                        <br>
                                        <div class="row col-lg-12">
                                            <div class="col-xs-3 text-right"><h5>Nombres y Apellidos:</h5></div>
                                            <div class="col-xs-9">
                                                <div class="form-group">
                                                    <input inputmode="none"  maxlength="100" type="text" class="form-control" id="txt_descripcion" disabled="disabled" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row col-lg-12">
                                            <div class="col-xs-3 text-right"><h5>Identificación:</h5></div>
                                            <div class="col-xs-3">
                                                <div class="form-group">
                                                    <input inputmode="none"  maxlength="100" type="text" class="form-control" id="txt_identificacion" disabled="disabled" />
                                                </div>
                                            </div>
                                            <div class="col-xs-1 text-right"><h5>Usuario:</h5></div>
                                            <div class="col-xs-5">
                                                <div class="form-group">
                                                    <input inputmode="none"  maxlength="100" type="text" class="form-control" id="txt_usuario" disabled="disabled" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row col-lg-12">
                                            <div class="col-xs-3 text-right"><h5>Telefono:</h5></div>
                                            <div class="col-xs-3">
                                                <div class="form-group">
                                                    <input inputmode="none"  maxlength="100" type="text" class="form-control" id="txt_telefono" disabled="disabled" />
                                                </div>
                                            </div>
                                            <div class="col-xs-1 text-right"><h5>E-mail:</h5></div>
                                            <div class="col-xs-5">
                                                <div class="form-group">
                                                    <input inputmode="none"  maxlength="100" type="text" class="form-control" id="txt_email" disabled="disabled" />
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="row col-lg-12"> 
                                            <div class="col-xs-3 text-right"><h5>Direcci&oacute;n Domicilio:</h5></div>
                                            <div class="col-xs-9">
                                                <div class="form-group">
                                                    <input inputmode="none"  maxlength="100" type="text" class="form-control" id="txt_direccion" disabled="disabled" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row col-lg-12"> 
                                            <div class="col-xs-3 text-right"><h5>Nombre POS:</h5></div>
                                            <div class="col-xs-7">
                                                <div class="form-group">
                                                    <input inputmode="none"  maxlength="100" type="text" class="form-control" id="txt_nombrePos" disabled="disabled" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row col-lg-12"> 
                                            <div class="col-xs-3 text-right"><h5>Perfil:</h5></div>
                                            <div class="col-xs-7">
                                                <div class="form-group">
                                                    <input inputmode="none"  maxlength="100" type="text" class="form-control" id="txt_perfil" disabled="disabled" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </p>
                            </div>
                            <div id="tab_aplica" class="tab-pane fade"> 
                                <br>
                                <div class="row" style="margin-top: 20px; margin-left: 90px;">
                                    <div class="col-md-10"><b>Lista Restaurantes Asignados:</b></div>                                     
                                </div> 
                                    <div id="btnEliminarTodos" class="row">                                       
                                    <div class="col-md-10 text-right">                                        
                                        <button type="button" class="btn btn-danger" aria-label="Left Align" onclick="eliminarTodosUsuarioRestaurantes();">
                                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar todos
                                        </button>  
                                    </div>                                    
                                </div>
                                <div class="row">                                       
                                    <div class="col-md-10">                                        
                                        <div style="height:320px; overflow:auto; margin-top: 5px; margin-left: 100px;">                                            
                                            <table id="listaRestaurantesAsignados" class="table table-bordered" style="width: 100%"></table>                                            
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                            <div id="tab_cadena" class="tab-pane fade" style="height:430px !important;"> 
                                <br>
                                <div class="row">
                                    <div class="col-xs-2 text-right"><h5>Cadena:</h5></div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <select id="selectCadena" class="form-control" ></select>
                                        </div>
                                    </div>                        
                                </div>
                                <br>
                                <div class="row" id="contenedorRegion">
                                    <div class="col-xs-2 text-right"><h5>Region:</h5></div>
                                    <div class="col-sm-9">	
                                        <div id='selectRegion' class="btn-group" data-toggle="buttons"></div>           
                                    </div>
                                </div>
                                <br>
                                <div id="contenedorTiendas">
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-xs-12 col-md-7"><h5><b>Tienda:</b></h5></div>
                                        <div class="col-md-3 text-right">Marcar Todos &nbsp 
                                            <input inputmode="none"  id="cbx_todosRestaurantes" name="cbx_todosRestaurantes" onclick="seleccionarTodosRestaurantes();" value="1" type="checkbox">
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-xs-12 col-md-10">                                	
                                            <div style="height: 260px; overflow-y: auto;">
                                                <div id="listaRestaurantesCadena" class="list-group"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                </div>                                 
                            </div>
                            <br>
                        </div>
                        <div id="btn_accion" class="modal-footer"></div>
                    </div>
                </div>
            </div>
        </div>        
        
        <div id="cargando" class="modal_cargando">
            <div class="modal_cargando_contenedor">
                <img src="../../imagenes/admin_resources/progressBar.gif" />
            </div>
        </div>    
       
        <script type="text/javascript" src="../../js/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="../../js/chosen.proto.min.js"></script>
        <script type="text/javascript" src="../../js/ajax_adminCambioUsuarioCadena.js"></script>
    </body>
</html>


