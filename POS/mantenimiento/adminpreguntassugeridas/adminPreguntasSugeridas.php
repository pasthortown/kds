<?php
session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jose Fernandez //////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de preguntas sugeridas///////////
///////FECHA CREACION: 22-04-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 01/06/2015///////////////////////////////////
///////USUARIO QUE MODIFICO: Jose Fernandez////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Aplicacion nuevo estilo, configuraciones en //
////////////////////////////////pantalla modal, cambio de etiquetas///////////
///////////////////////////////////////////////////////////////////////////////
include_once '../../seguridades/seguridad.inc';
include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_seguridades.php';
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Preguntas Sugeridas</title>

        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="../../css/jquery-ui.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/est_modal.css" />
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
        <link rel="stylesheet" type="text/css" href="../../css/progressBar.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap-editable.css" />
    </head>
    <body>
        <input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>" />
        <input inputmode="none"  id="cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>" />
        <input inputmode="none"  id="pre_id" type="hidden" />
        <input inputmode="none"  id="plu" type="hidden" />
        <input inputmode="none"  id="hid_psug_id" type="hidden" />

        <div class="superior" id="div_nuevo">
            <div class="menu" style="width: 300px;" align="center">
                <ul>
                    <li>
                        <button class="botonMnSpr l-basic-elaboration-document-plus" onclick="agregar()" value="Agregar">
                            <span>Nuevo</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>PREGUNTAS SUGERIDAS</h1>
            </div>
        </div>

        <div id="inicio_preguntas" class="inferior" align="center">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-sm-8" align="left">
                            <div id='opciones_estado' class="btn-group" data-toggle="buttons">
                                <label id="check_activos" class="btn btn-default btn-sm btn active" onClick="fn_muestraDetallePreguntasPorEstado(0, 11, 'activo');">Activos
                                    <input inputmode="none"  type="radio" name="uno" id="check_activos" />
                                </label>
                                <label class="btn btn-default btn-sm"  onclick="fn_muestraDetallePreguntasPorEstado(0, 11, 'inactivo');">
                                    <input inputmode="none"  type="radio" name="uno" id="check_inactivos" />Inactivos
                                </label>
                                <label class="btn btn-default btn-sm " onClick="fn_muestraDetallePreguntas();">Todos
                                    <input inputmode="none"  id="check_todos" type="radio" name="uno" />
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group input-group-sm">                                
                                <input inputmode="none"  id="par_numplu" type="text" class="form-control" placeholder="" aria-describedby="sizing-addon1" />
                            </div>
                        </div>
                    </div>            
                </div>
                <div id="menu_producto" class="panel-body">
                    <table id="detalle_preguntas" class="table table-bordered table-hover"></table>
                </div>                       
            </div>  
        </div>

        <div class="modal fade" id="mdl_productos" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header active panel-footer">
                        <h4 class="modal-title" id="myModalLabel">Respuestas</h4>
                    </div>
                    <br/>
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3 text-right"><h5>Productos:</h5></div>
                            <div class="col-sm-9">
                                <div id='opciones_respuesta' class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default btn-sm active" onClick="fn_cargaPlusRespuestaModificar('Todo');" id="check_todo" >Todos
                                        <input inputmode="none"  id="check_todo" type="radio" name="option_producto" value="Todo" />
                                    </label>
                                    <label class="btn btn-default btn-sm" onClick="fn_cargaPlusRespuestaModificar('Producto');">Producto
                                        <input inputmode="none"  id="check_venta" type="radio" name="option_producto" value="Venta" />
                                    </label>
                                    <label class="btn btn-default btn-sm" onClick="fn_cargaPlusRespuestaModificar('Modificador');" >Modificador
                                        <input inputmode="none"  id="check_modificador" type="radio" name="option_producto" value="Modificador" />
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-1"></div>
                        <div class="col-xs-10">
                            <select class="form-control" id="selPlus" style="text-transform: uppercase;"></select>
                        </div>
                    </div>
                    <br/> 
                    <div class="row"> 
                        <div class="col-sm-7">
                            <div> 
                                <input inputmode="none"  class="form-control" type="hidden" id="txt_nombrerespuesta" />                                         	
                            </div> 
                        </div>                    
                    </div>
                    <br/>      
                    <div class="modal-footer active panel-footer" align="right">
                        <button class="btn btn-primary" onClick="fn_grabaRespuestaModificada();">Aceptar</button>
                        <button class="btn btn-default" data-dismiss="modal" onClick="fn_modalesModificados();">Cancelar</button>                
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mdl_productosModificados" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header active panel-footer">
                        <h4 class="modal-title" id="myModalLabel">Respuestas</h4>
                    </div>
                    <br/>
                    <div class="panel-heading">
                        <div class="row">                                         
                            <div class="col-xs-3 text-right">
                                <h5>Productos:</h5>
                            </div>
                            <div class="col-sm-9">
                                <div id='opciones_respuestanuevas' class="btn-group" data-toggle="buttons"> 
                                    <label class="btn btn-default btn-sm active" onClick="fn_cargaPlusRespuesta('Todo');" id="check_todosnuevo">Todos
                                        <input inputmode="none"  id="check_todosnuevo" type="radio" name="option_productonuevo" value="Todo" />
                                    </label>                                   
                                    <label class="btn btn-default btn-sm" onClick="fn_cargaPlusRespuesta('Producto');">Producto
                                        <input inputmode="none"  id="check_ventanuevo" type="radio" name="option_productonuevo" value="Venta" />
                                    </label>
                                    <label class="btn btn-default btn-sm" onClick="fn_cargaPlusRespuesta('Modificador');" >Modificador
                                        <input inputmode="none"  id="check_modificadornuevo" type="radio" name="option_productonuevo" value="Modificador" />
                                    </label>                                         	
                                </div>
                            </div>                    
                        </div>
                    </div>
                    <div class="row" >
                        <div class="col-xs-1"></div>
                        <div class="col-xs-10">
                            <select class="form-control" id="selPlusNuevo" style="text-transform:uppercase;"></select>     
                        </div>
                    </div>
                    <br/>
                    <div class="row">    
                        <div class="col-sm-7">
                            <div> 
                                <input inputmode="none"  class="form-control" type="hidden" id="txt_nombrerespuestanueva" />                                         	
                            </div>
                        </div>                    
                    </div>
                    <br/>
                    <div class="modal-footer active panel-footer" align="right">
                        <button class="btn btn-primary" onClick="fn_grabaRespuesta();">Aceptar</button>
                        <button class="btn btn-default" data-dismiss="modal" onClick="fn_cierraNuevos();">Cancelar</button>                
                    </div>
                </div>
            </div>
        </div>

        <div id="mdlPreguntasSugeridas" class="modal fade" aria-hidden="true" data-backdrop="static" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" style="display: none;">
            <div class="modal-dialog" style="width: 800px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true">×</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 id="mdlPreguntasSugeridasTitulo" class="modal-title"></h4>
                    </div>
                    <div class="contenedor_tabs">
                        <ul id="tabs" class="nav nav-tabs">
                            <li id="tabPreguntasSugeridas" class="active"><a data-toggle="tab" href="#tabPsgPreguntasSugeridas"><b>Pregunta Sugerida</b></a></li>
                            <li id="tabRespuestas"><a data-toggle="tab" href="#tabPsgRespuestas"><b>Respuestas</b></a></li>
                            <li id="tabPlusAsociados"><a data-toggle="tab" href="#tabPsgPlusAsociados"><b>Plus Asociados a Pregunta</b></a></li>
                        </ul>
                        <div id="tabsPsg" class="tab-content">
                            <div id="tabPsgPreguntasSugeridas" class="tab-pane fade in active center-block">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="form-horizontal">
                                            <div class="col-lg-10 col-md-10"></div>
                                            <div class="col-lg-2 col-md-2">
                                                <h5 class="text-right">
                                                    <b>
                                                        Activo:
                                                        <input inputmode="none"  id="inpPsgEstado" value="1" type="checkbox">
                                                    </b>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label id="lblPsgDescripcionPregunta" class="col-lg-4 col-md-5 control-label" for="inpPsgDescripcionPregunta"><h6>Grupo:</h6></label>
                                        <div class="col-lg-8 col-md-7">
                                            <div class="form-group">
                                                <select id="inGrupoPregunta" class="form-control"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label id="lblPsgDescripcionPregunta" class="col-lg-4 col-md-5 control-label" for="inpPsgDescripcionPregunta"><h6>Descripción pregunta:</h6></label>
                                        <div class="col-lg-8 col-md-7">
                                            <div class="form-group">
                                                <input inputmode="none"  id="inpPsgDescripcionPregunta" class="form-control" type="text">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label id="lblPsgDescripcionPreguntaPOS" class="col-lg-4 col-md-5 control-label" for="inpPsgDescripcionPreguntaPOS"><h6>Descripción pregunta en POS:</h6></label>
                                        <div class="col-lg-8 col-md-7">
                                            <div class="form-group">
                                                <input inputmode="none"  id="inpPsgDescripcionPreguntaPOS" class="form-control" type="text" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label id="lblPsgCantidadMinimaRespuestas" class="col-lg-4 col-md-5 control-label" for="inpPsgCantidadMinimaRespuestas"><h6>Cantidad mínima de respuestas:</h6></label>
                                        <div class="col-lg-8 col-md-7">
                                            <div class="input-prepend input-group number-spinner">
                                                <span class="add-on input-group-addon btn btn-primary" data-dir="dwn"><i class="glyphicon glyphicon-minus fa fa-calendar"></i></span>
                                                <input inputmode="none"  id="inpPsgCantidadMinimaRespuestas" type="text" class="form-control text-center" value="0" min="0" max="20" disabled="disabled" />
                                                <span class="add-on input-group-addon btn btn-primary" data-dir="up"><i class="glyphicon glyphicon-plus fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label id="lblPsgCantidadMaximaRespuestas" class="col-lg-4 col-md-5 control-label" for="inpPsgCantidadMaximaRespuestas"><h6>Cantidad máxima de respuestas:</h6></label>
                                        <div class="col-lg-8 col-md-7">
                                            <div class="input-prepend input-group number-spinner">
                                                <span class="add-on input-group-addon btn btn-primary" data-dir="dwn"><i class="glyphicon glyphicon-minus fa fa-calendar"></i></span>
                                                <input inputmode="none"  id="inpPsgCantidadMaximaRespuestas" type="text" class="form-control text-center" value="0" min="0" max="20" disabled="disabled" />
                                                <span class="add-on input-group-addon btn btn-primary" data-dir="up"><i class="glyphicon glyphicon-plus fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="tabPsgRespuestas" class="tab-pane fade">
                                <div class="panel-body">
                                    <div id="areaTrabajo">
                                        <div class="row">
                                            <div class="col-md-12" style="height: 280px; overflow:auto;">
                                                <div id="scroll" class="row" style="overflow-x: hidden; overflow-y: auto">
                                                    <div class="col-xs-1"></div>
                                                    <div class="col-xs-10">
                                                        <br/>
                                                        <br/>
                                                        <table id="tblPsgRespuestas" class="table table-bordered table-condensed"></table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 20px">
                                            <div id="btn_aplicaRespuestaModificada" align="center">
                                                <button class="btn btn-primary" onClick="fn_aplicarRespuestaModificada();">Agregar Respuesta</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>  
                            </div>
                            <div id="tabPsgPlusAsociados" class="tab-pane fade">
                                <div class="panel-body">
                                    <div id="areaTrabajo">
                                        <div class="col-md-12">
                                            <div style="height:300px; overflow:auto;">
                                                <br/>
                                                <br/>
                                                <table id="tblPsgPlusAsociados" class="table table-bordered"></table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" type="button" data-dismiss="modal">Cancelar</button>
                        <button id="btnPsgGuardarCambios" class="btn btn-primary" type="button" onclick="">
                            <i class="glyphicon glyphicon-floppy-saved mr10"></i>
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../../imagenes/admin_resources/progressBar.gif" />
            </div>
        </div>

        <script src="../../js/jquery1.11.1.js"></script>
        <script src="../../js/ajax_datatables.js"></script>    
        <script src="../../bootstrap/js/bootstrap.js"></script>    
        <script src="../../bootstrap/js/bootstrap-dataTables.js"></script>     
        <script language="javascript1.1" src="../../js/alertify.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-editable.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/inputnumber.js"></script>
        <script type="text/javascript" src="../../bootstrap/templete/plugins/forms/spinner/jquery.bootstrap-touchspin.min.js"></script>
        <script type="text/javascript" src="../../js/ajax_preguntas_sugeridas.js"></script>

    </body>
</html>