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
        <title>Consulta Soporte</title>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
    </head>
    <body>        
        <div class="superior">
            <div class="menu" style="width: 400px;" align="center">
                <ul><li>
                    <input inputmode="none"  id="btn_agregar" type="button"  onclick="fn_nuevoRegistro();" class="botonhabilitado" value="Agregar"/>
                </li></ul>        
            </div> 
            <div class="tituloPantalla"><h1>Consultar</h1></div>   
        </div>
        <br/>
        <div class="panel panel-default text-left">
            <div class="panel-body">
                <div class="col-xs-4 text-right"><h5>Seleccionar Restaurante:</h5></div>
                <div class="col-xs-6">
                    <div class="form-group">
                        <select id="selectRestaurante" class="form-control" ></select>
                    </div>
                </div>
            </div>
        </div>
        <div id="divConsulta" class="contenedor">            
            <div id="divSeleccionarConsulta" class="panel panel-default text-left">
                <div class="panel-body">
                    <div class="col-xs-4 text-right"><h5>Seleccionar Consulta:</h5></div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <select id="selectConsulta" class="form-control" onchange="fn_seleccionarConsulta(this.value);">
                                <option value="0">---------------   Seleccionar Consulta   ---------------</option> 
                                <option value="1">Per&iacute;odo</option> 
                                <option value="2">Impresi&oacute;n Desmontado Cajero</option> 
                                <option value="3">Impresi&oacute;n Fin de D&iacute;a</option>
                                <option value="4">Reimpresi&oacute;n de Factura</option>
                                <option value="5">Reimpresi&oacute;n de Nota de Cr&eacute;dito</option>
                                <option value="6">Reimpresi&oacute;n de Ordenes de Pedido</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>                
            <div id="Tbl_ConsultaSoporte">
                <table class="table table-bordered table-hover" id="TblDetalle_ConsultaSoporte" border="1" cellpadding="1" cellspacing="0">
                </table>
            </div>
            <div id="Tbl_ConsultaSoporteDesasignado">
                <table class="table table-bordered table-hover" id="TblDetalle_ConsultaSoporteDesasignado" border="1" cellpadding="1" cellspacing="0">
                </table>
            </div>
            <div id="Tbl_ConsultaSoporteFindeDia">
                <table class="table table-bordered table-hover" id="TblDetalle_ConsultaSoporteFindeDia" border="1" cellpadding="1" cellspacing="0">
                </table>
            </div>
        </div> 
        <!--MODAL DE CONFIRMACION PARA LA REIMPRESION DE REPORTES-->
        <div class="modal fade" id="modalConfirmacion" role="dialog" data-backdrop="static">
            <div class="modal-dialog">              
                <div class="modal-content">  
                    <div class="modal-header">                     
                        <h4 id="lbl_tutulo" class="modal-title"></h4> 
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <p><h4><label id="lbl_Mensajeconfirmacion"></label></h4></p>
                            <h4><label id="lbl_RegistroConfirmacion"></label></h4>
                        </div>
                    </div>
                    <div id="btn_accion" class="modal-footer"></div>
                </div>
            </div>
        </div>
        <!--MODAL CREAR IMPRESION DE REPORTES-->
        <div class="modal fade" id="modalNuevoRegistro" role="dialog" data-backdrop="static">
            <div class="modal-dialog">              
                <div class="modal-content">  
                    <div class="modal-header">                     
                        <h4 id="tituloModal" class="modal-title"></h4> 
                    </div>
                    <div class="modal-body">
                        <div class="row">                            
                            <div class="col-xs-2"><h5>Per&iacute;odo:</h5></div>
                            <div class="col-xs-9">
                              <div class="form-group" class="col-xs-1">    
                                  <select id="SeleccionarPeriodo" class="form-control" ></select>                    	
                              </div>
                            </div>                            
                        </div>
                        <div id="divSeleccionarCajero" class="row">                            
                            <div class="col-xs-2"><h5>Cajero/a:</h5></div>
                            <div class="col-xs-9">
                              <div class="form-group" class="col-xs-1">    
                                  <select id="SeleccionarCajero" class="form-control" ></select>                    	
                              </div>
                            </div>                            
                        </div>
                        <div id="divSeleccionarEstacion" class="row">                            
                            <div class="col-xs-2"><h5>Estaci&oacute;n:</h5></div>
                            <div class="col-xs-9">
                              <div class="form-group" class="col-xs-1">    
                                  <select id="SeleccionarEstacion" class="form-control" ></select>                    	
                              </div>
                            </div>                            
                        </div>
                        <div class="row">                            
                            <div class="col-xs-3"><h5>Administrador/a:</h5></div>
                            <div class="col-xs-8">
                              <div class="form-group" class="col-xs-1">    
                                  <select id="SeleccionarAdmin" class="form-control" ></select>                    	
                              </div>
                            </div>                            
                        </div>
                    </div>
                    <div id="btn_acciones" class="modal-footer"></div>
                </div>
            </div>
        </div>
        <!-- VARIABLES -->
        <input inputmode="none"  type="hidden" id="hdn_IDRestaurante"/>
        <input inputmode="none"  type="hidden" id="hdn_ValorConsulta"/>
        <input inputmode="none"  type="hidden" id="hdn_IDPeriodo"/>
        <input inputmode="none"  type="hidden" id="hdn_fechaApertura"/>        
        <input inputmode="none"  type="hidden" id="hdn_IDCanalMovimientoDesmontado"/>
        <input inputmode="none"  type="hidden" id="hdn_cajeroDesmontado"/>        
        <input inputmode="none"  type="hidden" id="hdn_IDCanalMovimientoFindeDia"/>
        <input inputmode="none"  type="hidden" id="hdn_fechaPeriodoFindeDia"/>
        <input inputmode="none"  type="hidden" id="hdn_IPEstacion"/>
        <input inputmode="none"  type="hidden" id="hdn_IDUsuario"/>
        <input inputmode="none"  type="hidden" id="hdn_IDControlEstacion"/>
        <input inputmode="none"  type="hidden" id="hdn_IDUserPosCajero"/>
        <input inputmode="none"  type="hidden" id="hdn_IDUserPosAdmin"/>
        <input inputmode="none"  type="hidden" id="hdn_IDEstacion"/>
        
    <script type="text/javascript" src="../../js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>
    <script type="text/javascript" src="../../js/ajax_ConsultaSoporte.js"></script>
    <script type="text/javascript" src="../../js/ajax_api_impresion.js"></script>
        
    </body>
</html>

