<?php
session_start();
include_once("datos_descuentos.php");
///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Francisco Sierra ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de descuentos //////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 07-08-2017 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <title>Administración</title>

        <!-- ESTILOS -->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
        <style type="text/css">
            #contenedor_lst_rest_no_activos{
                height: 550px; 
                overflow-y: auto;
            }
        </style>
    </head>
    <body>
        <input inputmode="none"  id="sess_usr_id" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
        <input inputmode="none"  id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>       
        <div class="superior">
            <div class="menu" align="center" style="width: 300px;">
                <ul>
                    <li>
                        <button id="agregar" onClick="fn_agregarDescuento()" class="botonMnSpr l-basic-elaboration-document-plus">
                            <span>Nuevo</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
              <h1>ADMINISTRACI&Oacute;N DESCUENTOS</h1>
            </div>
          </div>
        <div class="contenedor">
           
            <div class="inferior">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div  id="mdl_rdn_pdd_crgnd" class="modal_cargando" style="display:none">
                            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                                <img src="../../imagenes/admin_resources/progressBar.gif" />
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-sm-8">
                                <h5>Lista de Descuentos</h5>
                                <div id="opciones_estado" class="btn-group" data-toggle="buttons" style="display:none">
                                    <label id="opciones_1" class="btn btn-default btn-sm active"><input inputmode="none"  id="opt_Todos" type="radio" value="Todos" checked="" autocomplete="off" name="options">Todos</label>
                                    <label class="btn btn-default btn-sm"><input inputmode="none"  id="opt_Activos" type="radio" value="Activos" autocomplete="off" name="options">Activos</label>
                                    <label class="btn btn-default btn-sm"><input inputmode="none"  id="opt_Inactivos" type="radio" value="Inactivos" autocomplete="off" name="options">Inactivos</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table id="listaDescuentos" class="table table-bordered table-hover"></table>
                    </div>
                </div>
                <!-- Fin Contenedor Inferior -->
            </div>
                     
            <!-- Fin Contenedor -->
        </div>

        <!-- Modal Pos Plus -->
        <div class="modal fade " id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 id="titulomodal" class="modal-title">Modificar Descuento</h4>
                    </div>
                    <div class="modal-body">
                        <div role="tabpanel">

                            <!-- Nav tabs -->
                            <ul id="pestanas" class="nav nav-tabs" role="tablist">
                                <li role="presentation" id="inicio"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><h5>Configuraci&oacute;n General</h5></a></li>
                                <li role="presentation" id="medio2"><a href="#procat" aria-controls="procat" role="tab" data-toggle="tab"><h5 id="titulo_pest3_">Productos a Aplicar</h5></a></li>
                                <li role="presentation" id="fin"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab"><h5>Tiendas a Aplicar</h5></a></li>
                            </ul>

                            <!-- Tab panes -->
                            <div id="pst_cnt" class="tab-content">
                                <!-- Configuracion Descuentos -->
                                <div role="tabpanel" class="tab-pane" id="profile">
                                      <br/>
                                    <div class="row">
                                        <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-3">
                                            <h5><b>Aplica en:</b></h5>
                                        </div>    
                                        <div class="col-md-7">
                                            <div id="pcn_apld_id" class="btn-group" data-toggle="buttons">
                                                <?php foreach ($datosAplicaDescuento["datos"] as $aplicaDescuento) {
                                                    $claseDiscrecional= "Discrecional" == trim($aplicaDescuento["apld_descripcion"]) ?"bt-aplica-discrecional":"";
                                                    ?>
                                                    <label class="btn btn-default bt-aplica-descuento <?php print($claseDiscrecional) ?>" data-nombreaplica="<?php print(trim($aplicaDescuento["apld_descripcion"])) ?>">
                                                        <input inputmode="none"  name="grp_apld" type="radio" autocomplete="off" value="<?php print($aplicaDescuento["apld_id"]) ?>"><?php print(trim($aplicaDescuento["apld_descripcion"])) ?></label>
                                                <?php } ?>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-sm-1"></div>
                                        <div class="col-sm-10">
                                            <div class="form-group">
                                                <label for="dsct_dscrp"><h5><b>Descripci&oacute;n:</b></h5></label>
                                                <input inputmode="none"  type="text" class="form-control" id="dsct_dscrp" placeholder="Descripcion del Descuento">
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <div class="row">
                                        
                                        <div class="col-md-1"></div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Tipo Descuento:</label>
                                                <select id="slct_tp_dsct" class="form-control">
                                                    <option value="0"> - Seleccionar Tipo de Descuento - </option>
                                                    <?php foreach ($tiposDescuentos["datos"] as $tipoDescuento) {?>
                                                        <option value="<?php print($tipoDescuento["tpd_id"]) ?>"><?php print($tipoDescuento["tpd_descripcion"]) ?></option>
                                                    <?php } ?>
                                                </select>
                                        </div>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="dsct_valor">Valor:</label>
                                            <input inputmode="none"  type="text" class="form-control" id="dsct_valor" placeholder="Valor Descuento">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-2"><h5 class="text-left"><b>Período de validez:</b></h5></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="FechaInicial" class="control-label">Fecha Inicio</label>
                                                <div class="input-prepend input-group">
                                                    <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                    <input inputmode="none"  type="text" value="" class="form-control" name="FechaInicial" id="FechaInicial" placeholder="Fecha Inicio"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="FechaFinal" class="control-label">Fecha Fin</label>
                                                <div class="input-prepend input-group">
                                                    <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                    <input inputmode="none"  type="text" value="" class="form-control" name="FechaFinal" id="FechaFinal" placeholder="Fecha Fin"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-10">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label><h5><b>Aplica:</b></h5></label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="dscto_aplica_minimo_maximo"><input inputmode="none"  type="checkbox" name="dscto_aplica_minimo_maximo"  id="dscto_aplica_minimo_maximo" />&nbsp; Mínimo y Máximo</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="dsct_aplica_cantidad"><input inputmode="none"  type="checkbox" name="dsct_aplica_cantidad"  id="dsct_aplica_cantidad" />&nbsp; Cantidad</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="dsct_mnt_min"><h5><b><span id="labelInputMinimo">Cantidad</span> Desde</b></h5></label>
                                                        <input inputmode="none"  type="text" class="form-control"  name="dsct_mnt_min"  id="dsct_mnt_min" placeholder="Cantidad Hasta">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="dsct_mnt_max"><h5><b><span id="labelInputMaximo">Cantidad</span> Hasta</b></h5></label>
                                                        <input inputmode="none"  type="text" class="form-control" name="dsct_mnt_max" id="dsct_mnt_max" placeholder="Cantidad Hasta">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-10">
                                            <div class="row">
                                                <div class="col-md-12">
                                                   
                                                </div>
                                            </div>
                                            <div class="row">
                                                
                                            </div>
                                        </div>
                                    </div>
                                        </div>
                                        <div class="col-sm-3">
                                             <label><h5><b>Configuraciones adicionales:</b></h5></label>
                                                <div class="btn-group">
                                                    <label for="dsct_std_id"><input inputmode="none"  type="checkbox" name="dsct_std_id"  id="dsct_std_id" />&nbsp; Est&aacute; Activo?:</label>
                                                </div>
                                                <div>
                                                    <label for="dscto_automatico"><input inputmode="none"  type="checkbox" name="dscto_automatico"  id="dscto_automatico" />&nbsp; Descuento automático</label>
                                                </div>
                                                <div>
                                                    <label for="dsct_cupones"><input inputmode="none"  type="checkbox" name="dsct_cupones"  id="dsct_cupones" />&nbsp; Descuento cupones</label>
                                                </div>
<!--                                                <div>
                                                    <label for="dscto_seguridad"><input inputmode="none"  type="checkbox" name="dscto_seguridad"  id="dscto_seguridad" />&nbsp; Requiere seguridad</label>
                                                </div>-->
                                            
                                        </div>
                                    </div>

                                    </div>

                                <!-- Productos/Categorias -->
                                <div role="tabpanel" class="tab-pane" id="procat" style="min-height: 280px">
                                    <br/>
                                    <div id="dsct_apld_id">
                                        <div class="row">
                                            <div class="col-md-1"></div>
                                            <div class="col-md-10">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <select id="select-plus-cadena">
                                                            <option value="0">---- Seleccione un producto</option>
                                                            <?php foreach ($productosCadena as $productoCadena) { 
                                                                $descripcionPLU=$productoCadena["plu_descripcion"];
                                                                ?>
                                                                <option value=<?php print($productoCadena["plu_id"]) ?> data-plu_num_plu="<?php print($productoCadena["plu_num_plu"]); ?>" data-plu_descripcion="<?php print($descripcionPLU); ?>" data-plu_id="<?php print($productoCadena["plu_id"]); ?>">
                                                                    <?php print($productoCadena["plu_num_plu"].' | '.$descripcionPLU) ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <button class="btn btn-sm btn-success btn-block" id="btn-agregar-plu-descuento">Agregar</button>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <ul id="listado-plus-agregardos-descuento" class="list-group"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br/>
                                    </div>

                                </div>


                                <!-- Locales Aplicar -->
                                <div role="tabpanel" class="tab-pane" id="settings">
                                    <div class="row" style="padding-top:1em">
                                        <div class="col-md-6">
                                            <div id="btnAgregarTodosRestaurantes" class="btn btn-success btn-block ">Agregar Todos &gt;&gt;</div>
                                            <hr>
                                            <!-- Busqueda -->
                                            <div class="control-group">
                                                <div class="controls">
                                                    <div class="input-prepend input-group">
                                                        <span class="add-on input-group-addon" id="icono_buscar_restaurantes"><i class="glyphicon glyphicon-search fa fa-calendar"></i></span>
                                                        <input inputmode="none"  id="buscar_Restaurantes" type="text" class="form-control" placeholder="Buscar" aria-describedby="sizing-addon1">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div  id="contenedor_lst_rest_no_activos">
                                                <ul id="lst_rst_id" class="list-group">
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div id="btnQuitarTodosRestaurantes" class="btn btn-warning btn-block">&lt;&lt; Quitar Todos</div>
                                            <hr>
                                            <div id="contenedor_lst_rest_activos">
                                                <div id="lst_rst_dscto" class="list-group">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div id="pnl_pcn_btn" class="modal-footer"></div>
                </div>
            </div>
        </div>

        <!-- JavaScript -->
        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/query.bootstrap-growl.min.js"></script>
       
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        
        <script type="text/javascript" src="../../js/jquery.uitablefilter.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        
        <script type="text/javascript" src="../../js/chosen.jquery.js"></script>
        <script type="text/javascript" src="../../js/ajax_admdescuentos.js"></script>

    </body>
</html>