<?php
session_start();

ini_set("memory_limit", "256M");
header("Expires: Fri, 25 Dec 1980 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jose Fernandez ////////////////////////////////////////////
///////DESCRIPCION: Configuracion de cadena ////////////////////////////////
///////FECHA CREACION: 25-05-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_seguridades.php';
include_once '../../seguridades/seguridad_niv3.inc';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

        <title>Cadena</title>

        <!-- ESTILOS -->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap-editable.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css" />
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
        <link rel="stylesheet" type="text/css" href="../../css/productos.css"/>
    </head>
    <body>
        <input inputmode="none"  id="sess_usr_id" type="hidden" valuebtnAgregarTransferenciaCadena="<?php echo $_SESSION['usuarioId']; ?>" />
        <input inputmode="none"  id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>" />
        <input inputmode="none"  id="sess_cdn_descripcion" type="hidden" value="<?php echo $_SESSION['cadenaNombre']; ?>" />
        <div class="superior">
            <div class="menu" align="center" style="width: 400px;">
                <!--
                            <ul>
                                <li>
                                  <button id="btnAgregarTransferenciaCadena" class="botonMnSpr l-basic-elaboration-document-plus" onclick="">
                                        <span>Nuevo</span>
                                    </button>
                    </li>
                </ul>
                -->
            </div>
            <div class="tituloPantalla">
                <h1>CADENAS</h1>
            </div>
        </div>
        <div class="contenedor">
            <div class="inferior">
                <div id="prb_img" class="row" style="display: none;">
                    <div class="col-sm-6">
                        <canvas height="300px" width="300px" id="micanvas"></canvas>
                    </div>
                    <div class="col-sm-6">
                        <textarea id="txt_area_imagen"></textarea>
                    </div>
                </div>

                <div class="panel panel-default">
                    <!--Dynamic Tavs-->
                    <div class="container-fluid">

                        <div class="panel-heading">
                            <h2>Configuraci&oacute;n</h2>
                        </div>

                        <div class="panel-body">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#menu1">Transferencia Venta</a></li>
                                <li><a data-toggle="tab" href="#menu2">Pol&iacute;ticas de Configuraci&oacute;n</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="menu1" class="tab-pane fade in active">

                                    <!--<div class="panel panel-default" id="botonesActivosInactivos">
                                        <div class="panel-heading">
                                            <div class="row">
        
                                    <div class="col-sm-3"><h5>Lista de Transferencia de Ventas</h5>
                                        <div id="opciones_estados" class="btn-group" data-toggle="buttons">
                                            <label class="btn btn-default btn-sm active" id="lblActivo" onclick="fn_OpcionSeleccionada('Activos');"><input inputmode="none"  id="opt_Activos" type="radio" value="Activos" checked="checked" autocomplete="off" name="estados" />Activos</label>
                                            <label class="btn btn-default btn-sm" id="lblInactivo"  onclick="fn_OpcionSeleccionada('Inactivos');"><input inputmode="none"  id="opt_Inactivos" type="radio" value="Inactivos" autocomplete="off" name="estados" />Inactivos</label>
                                            <label class="btn btn-default btn-sm" id="lblTodos"  onclick="fn_OpcionSeleccionada('Todos');"><input inputmode="none"  id="opt_Todos" type="radio" value="Todos" autocomplete="off" name="estados" />Todos</label>
                                        </div>
                                    </div>
        
                                            </div>
                                        </div>
                                    </div>
                                    -->
                                    <div id="div_tabla_transferenciaVentas">
                                        <br>
                                            <table class="table table-bordered table-hover" id="tabla_transferenciaVentas" border="1" cellpadding="0" cellspacing="0">
                                            </table>
                                    </div>
                                </div>
                                <div id="menu2" class="tab-pane fade">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <h5></h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <button type="button" class="close col-xs-1" onclick="fn_accionar('Modificar', 1)" ><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                            <button type="button" class="close" onclick="fn_accionar('Nuevo', 1)" ><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>    
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div>
                                            <table style="font-size: 9px;" id="listaCadenas" class="table table-bordered"></table>
                                        </div>
                                    </div>
                                    <!-- Fin Contenedor Inferior -->
                                </div>
                            </div>
                        </div>
                        <!--Fin Dynamic Tavs-->
                    </div>
                </div>
            </div>
            <!-- Fin Contenedor -->
        </div>

        <!--Inicio de modal Transferencia de ventas-->
        <div class="modal fade" id="modalTransferenciaVentas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="tituloModalNuevaColeccion"></b></h4>
                    </div>
                    </br>
                    <div align="right" class="col-xs-12 col-x">Activo &nbsp;&nbsp;
                        <input inputmode="none"  type="checkbox" name="option" data-size="mini" data-on-text="SI" data-off-text="NO"
                               checked="checked">
                    </div>
                    </br>
                    <div class="modal-body">

                        <!--<div class="row">-->
                        <div class="row">

                            <div class="col-xs-12 col-md-12">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group" id="div_tabla_origen" style="height: 200px; overflow: auto;">
                                        <table id="tabla_origen" class="table table-bordered table-hover" style="font-size:px;">
                                            <thead>
                                                <tr class="bg-primary">
                                                    <th style="text-align: center" colspan="2">Origen</th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <div class="form-group" class="col-xs-1">
                                            <input inputmode="none"  style="text-align:center" class="form-control" id="txt_cadena_origen"
                                                   readonly="readonly"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group" id="div_tabla_destino" style="height: 200px; overflow: auto;">
                                        <table id="tabla_destino" class="table table-bordered table-hover"
                                               style="font-size:px;">
                                            <thead>
                                                <tr class="bg-primary">
                                                    <th style="text-align: center">Destino</th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <div class="form-group" class="col-xs-1">
                                            <select id="sel_cadena_destino" class="form-control"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer panel-footer">
                        <button type="button" class="btn btn-primary" onclick="fn_guardarTransferenciaVentas()"><span
                                class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span>&nbsp;Guardar
                        </button>
                        <button type="button" class="btn btn-default" onclick="" data-dismiss="modal">Cancelar</button>

                    </div>
                </div>
            </div>
        </div>
        <!--Fin de modal Transferencia de ventas-->

        <!--Inicio de modal de coleccion -->
        <div class="modal fade" id="mdl_nuevaColeccion" tabindex="-1" role="dialog" data-backdrop="static">
            <div class="modal-dialog" style="width: 700px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Colecci&oacute;n: <label id="lblNombreColeccion"></label></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <br/>
                            <div class="col-xs-12 col-md-12">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group" id="detalle_restaurante_coleccion"
                                         style="height: 200px; overflow: auto;">
                                        <table id="listaColecciones" class="table table-bordered"
                                               style="font-size: 11px;"></table>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group" id="_detalle_restaurante_coleccion"
                                         style="height: 200px; overflow: auto;">
                                        <table id="lista_datos" class="table table-bordered" style="font-size: 11px;"></table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="div_caracteristicas">
                            <div class="row text-center">

                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Especifica Valor: <input inputmode="none"  disabled="disabled" type="checkbox"
                                                                                        value="1" id="check_especifica"></h5>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Obligatorio: <input inputmode="none"  disabled="disabled" type="checkbox" value="1"
                                                                                   id="check_obligatorio"></h5>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Tipo de dato: <label value="1" id="lbl_tipoDato"></label></h5>
                                    </div>
                                </div>

                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Varchar:</b></h6></div>
                                <div class="col-xs-12 col-md-4">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_caracter"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Entero:</b></h6></div>
                                <div class="col-xs-12 col-md-4">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_entero">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Fecha:</b></h6></div>
                                <div class="col-xs-12 col-md-4 form-group">
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i
                                                class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_fechaSImple"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Selecci&oacute;n:</b></h6></div>
                                <div class="col-xs-12 col-md-4 form-group">
                                    <div class="input-prepend input-group">
                                        <input inputmode="none"  type="checkbox" id="sel_seleccione" data-off-text="No" data-on-text="Si"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Numerico:</b></h6></div>
                                <div class="col-xs-12 col-md-4 form-group">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_numerico"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Fecha Inicio:</b></h6></div>
                                <div class="col-xs-12 col-md-3 form-group">
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i
                                                class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_fechaInicio"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Fecha Fin:</b></h6></div>
                                <div class="col-xs-12 col-md-3 form-group">
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i
                                                class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_fechaFin"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>M&iacute;nimo:</b></h6></div>
                                <div class="col-xs-12 col-md-3">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control" id="txt_minimo"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>M&aacute;ximo:</b></h6></div>
                                <div class="col-xs-12 col-md-3">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control" id="txt_maximo"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="fn_guardarColeccion();">Guardar</button>
                    </div>
                    <!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
        </div><!-- /.modal -->
        <!--fin-->


        <!--Inicio de modal de coleccion -->
        <div class="modal fade" id="mdl_editaColeccion" tabindex="-1" role="dialog" data-backdrop="static">
            <div class="modal-dialog" style="width: 750px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Colecci&oacute;n: <label id="lblNombreColeccionModificar"></label></h4>
                    </div>
                    <div class="modal-body">
                        <div id="div_caracteristicasM">
                            <div class="row">
                                <div class="col-xs-12 col-x text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" value=""
                                                                                                        id="check_activo"
                                                                                                        enabled=""></h6>
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Especifica Valor: <input inputmode="none"  disabled="disabled" type="checkbox"
                                                                                        value="1" id="check_especificaM"></h5>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Obligatorio: <input inputmode="none"  disabled="disabled" type="checkbox" value="1"
                                                                                   id="check_obligatorioM"></h5>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Tipo de dato: <label value="1" id="lbl_tipoDatoM"></label></h5>
                                    </div>
                                </div>

                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Varchar:</b></h6></div>
                                <div class="col-xs-12 col-md-4">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_caracterM"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Entero:</b></h6></div>
                                <div class="col-xs-12 col-md-4">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_enteroM"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Fecha:</b></h6></div>
                                <div class="col-xs-12 col-md-4 form-group">
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i
                                                class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_fechaSImpleM"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Selecci&oacute;n:</b></h6></div>
                                <div class="col-xs-12 col-md-4 form-group">
                                    <div class="input-prepend input-group">
                                        <input inputmode="none"  type="checkbox" id="sel_seleccioneM" data-off-text="No" data-on-text="Si"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Numerico:</b></h6></div>
                                <div class="col-xs-12 col-md-4 form-group">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_numericoM"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Fecha Inicio:</b></h6></div>
                                <div class="col-xs-12 col-md-3 form-group">
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i
                                                class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_fechaInicioM"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>Fecha Fin:</b></h6></div>
                                <div class="col-xs-12 col-md-3 form-group">
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i
                                                class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_fechaFinM"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>M&iacute;nimo:</b></h6></div>
                                <div class="col-xs-12 col-md-3">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_minimoM">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                                <div class="col-md-2"></div>
                                <div class="col-xs-12 col-md-2"><h6><b>M&aacute;ximo:</b></h6></div>
                                <div class="col-xs-12 col-md-3">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control limpiar" id="txt_maximoM">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4"></div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="fn_actualizaColeccion();">Guardar</button>
                    </div>
                    <!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
        </div><!-- /.modal -->
        <!--fin-->


        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando" ng-show="cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../../imagenes/admin_resources/progressBar.gif"/>
            </div>
        </div>


        <input inputmode="none"  id="slccn_rst_id" type="hidden" value=""/>
        <input inputmode="none"  id="emp_confirmacion_ok" type="hidden" value=""/>
        <input inputmode="none"  id="txt_hidden_cdn_id" type="hidden" value=""/>
        <input inputmode="none"  id="txt_hidden_ID_ColeccionCadena" type="hidden" value=""/>
        <input inputmode="none"  id="txt_hidden_ID_ColeccionDeDatosCadena" type="hidden" value=""/>

        <!-- JavaScript -->
        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/switch.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>
        <script language="javascript1.1" src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/inputnumber.js"></script>
        <script type="text/javascript" src="../../js/ajaxAdminCadena.js"></script>
    </body>
</html>