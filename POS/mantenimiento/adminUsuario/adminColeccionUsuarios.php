<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<input inputmode="none"  type="hidden" id="idColeccionUsuarios">
<input inputmode="none"  type="hidden" id="idColeccionDeDatosUsuarios">

<div class="modal fade" id="modalAgregarColeccionUsuario" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true" >
    <div class="modal-dialog" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header">                  
                <h4 class="modal-title">Colecci&oacute;n:
                    <label id="nombreColeccion"></label>
                </h4>
            </div>
            <div class="modal-body">
                <!--DETALLE Y DATOS DE COLECCION -->
                <div class="row">                   
                    <div class="col-xs-12 col-md-12">
                        <div class="col-xs-12 col-md-6">
                            <table id="cabeceraColeccion" class="table table-bordered bg-primary" style="font-size: 12px;">
                                <thead>
                                    <tr>
                                        <th class="bg-primary text-center">Descripci&oacute;n</th>                                                   
                                    </tr>
                                </thead>
                            </table>
                            <div class="form-group" style="height: 200px; overflow: auto;">                                        
                                <table id="coleccion_descripcion" class="table table-bordered" style="font-size: 11px;"></table>                                
                            </div> 
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <table id="cabeceraParametros" class="table table-bordered bg-primary" style="font-size: 12px;">
                                <thead>
                                    <tr>
                                        <th class="bg-primary text-center">Par&aacute;metro</th>                                                   
                                    </tr>
                                </thead>
                            </table>
                            <div class="form-group" style="height: 200px; overflow: auto;">
                                <table id="coleccion_datos" class="table table-bordered" style="font-size: 11px;"></table>                                
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cerrarModalAgregar();">Cancelar</button>              
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTipoDatos_AgregarColeccionUsuario" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true" >
    <div class="modal-dialog" style="width: 700px; margin: 5px auto;">
        <div class="modal-content">
            <div class="modal-header">                  
                <h4 class="modal-title">Par&aacute;metro:
                    <label id="nombreParametro"></label>
                </h4>
            </div>
            <div class="modal-body">
                <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" checked="checked" id="check_estado"/></h6></div> 
                <!--TIPOS DE DATOS-->
                <div id="tipos_de_dato">
                    <div class="row"> 
                        <div class="col-xs-6 col-md-4">
                            <div class="btn-group">
                                <h5 class="text-right">Especifica Valor: <input inputmode="none"  disabled="disabled" type="checkbox" value="1" id="check_especifica"/></h5>
                            </div>
                        </div>        
                        <div class="col-xs-6 col-md-4">
                            <div class="btn-group">
                                <h5 class="text-right">Obligatorio: <input inputmode="none"  disabled="disabled" type="checkbox" value="1" id="check_obligatorio"/></h5>
                            </div>
                        </div>       
                        <div class="col-xs-6 col-md-4">
                            <div class="btn-group">
                                <h5 class="text-right">Tipo de dato: <label value="1" id="lbl_tipoDato"></label></h5>
                            </div>
                        </div>       

                    </div>
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>Car&aacute;cter:</h5></div>
                        <div class="col-xs-7">
                            <div class="form-group">
                                <input inputmode="none"  type="text" class="form-control" id="tipo_varchar" />
                            </div>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>Entero:</h5></div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_entero" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>Fecha:</h5></div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <div class="input-prepend input-group">
                                    <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                    <input inputmode="none"  type="text" value="" class="form-control" name="tipo_fecha" id="tipo_fecha" placeholder="Fecha" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">                     	
                        <div class="col-xs-3 text-right"><h5>Seleci&oacute;n:</h5></div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <input inputmode="none"  type="checkbox" id="tipo_bit" data-off-text="No" data-on-text="Si" />         
                            </div>
                        </div>                             
                    </div>
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>Num&eacute;rico:</h5></div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_numerico" />
                            </div>
                        </div>
                    </div>                    
                    <div class="row">                      
                        <div class="col-md-3"><h5 class="text-right">Rango Fecha:</h5></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="FechaInicial" class="control-label">Fecha Inicio</label>
                                <div class="input-prepend input-group">
                                    <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                    <input inputmode="none"  type="text" value="" class="form-control" name="FechaInicial" id="FechaInicial" placeholder="Fecha Inicio" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="FechaFinal" class="control-label">Fecha Fin</label>
                                <div class="input-prepend input-group">
                                    <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                    <input inputmode="none"  type="text" value="" class="form-control" name="FechaFinal" id="FechaFinal" placeholder="Fecha Fin" />
                                </div>
                            </div>
                        </div>                      
                    </div> 

                    <div class="row">                      
                        <div class="col-md-3"><h5 class="text-right">Rango Decimal:</h5></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="Min" class="control-label">Minimo</label>                                
                                <div class="form-group">
                                    <input inputmode="none"  maxlength="50" type="text" class="form-control" id="rango_minimo" />
                                </div>                                
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="Max" class="control-label">M&aacute;ximo.</label>                                
                                <div class="form-group">
                                    <input inputmode="none"  maxlength="50" type="text" class="form-control" id="rango_maximo" />
                                </div>                                
                            </div>
                        </div>                      
                    </div>                     
                </div>
            </div>
            <div id="btn_accion" class="modal-footer">
            </div>
        </div>
    </div>
</div>