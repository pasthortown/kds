<?php
session_start();

ini_set("memory_limit", "256M");
header("Expires: Fri, 25 Dec 1980 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Configuraci�n de Pantallas ////////////////////////////////
///////FECHA CREACION: 25-01-2016 /////////////////////////////////////////////
///////FECHA DE MODIFICACION: 22-12-2016 //////////////////////////////////////
///////USUARIO QUE MODIFICO: Juan Estevez /////////////////////////////////////
///////DESCRIPCION DEL ULTIMO CAMBIO: Creacion de panel ///////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_seguridades.php';
include_once '../../seguridades/seguridad_niv3.inc';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <!-- ESTILOS -->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap-editable.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css" />
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />

        <style>
            .btn-file {
                position: relative;
                overflow: hidden;
            }

            .btn-file input[type=file] {
                position: absolute;
                top: 0;
                right: 0;
                min-width: 100%;
                min-height: 100%;
                font-size: 100px;
                text-align: right;
                filter: alpha(opacity=0);
                opacity: 0;
                outline: none;
                background: white;
                cursor: inherit;
                display: block;
            }
        </style>
    </head>
    <body>
        <input id="sess_usr_id" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
        <input id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
        <input id="id_td_ruc" type="hidden" />
        <input id="id_td_cedula" type="hidden" />
        <input id="url_api_motorizados" type="hidden" />
        <input id="token_api_mdm_cliente" type="hidden" />
        <input id="id_pais" type="hidden" />
        <input id="nombre_pais" type="hidden" />
        <input id="id_ciudad" type="hidden" />
        <input id="nombre_ciudad" type="hidden" />

        <div class="superior">
            <div class="menu" style="width: 200px;">
                <ul>
                    <li>
                        <button id="btnSincronizarRestaurantes" class="botonMnSpr l-basic-elaboration-briefcase-download" onclick="crear_motorizado()">
                            <span>Nuevo</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>Motorizados</h1>
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
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-8"><h5>Lista de Motorizados</h5>
                                <div id="opciones_estado" class="btn-group" data-toggle="buttons">
                                    <label id="opciones_1" class="btn btn-default btn-sm active">
                                        <input type="radio" value="Activos" autocomplete="off" id="flt_activo" name="ptns_std_rst" onchange="cargar_motorizados('Activo')" checked="checked" />Activos
                                    </label>
                                    <label id="opciones_2" class="btn btn-default btn-sm">
                                        <input type="radio" value="Inactivos" autocomplete="off" id="flt_inactivo" name="ptns_std_rst" onchange="cargar_motorizados('Inactivo')" />Inactivos
                                    </label>
                                    <!--
                                    <label class="btn btn-default btn-sm">
                                        <input type="radio" value="Todos" autocomplete="off" name="ptns_std_rst" onchange="fn_consultarListaRestaurantes(0)" />Todos
                                    </label>
                                    -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-8">
                            <h5></h5>
                        </div>
                        <div class="col-sm-4"></div>
                    </div>
                    <div class="panel-body">
                        <table id="lstMotorizados" class="table table-bordered table-hover"></table>
                    </div>
                </div>
            </div> <!-- Fin Contenedor Inferior -->
        </div>  <!-- Fin Contenedor -->

        <!-- Modal Motorizado -->
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" style="width:800px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 id="titulomodal" class="modal-title">Modificar</h3>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-7"></div>
                            <div class="col-md-3 text-right">
                                <label class="control-label" for="inpMdlEstado"><b>Activo</b></label>
                            </div>
                            <div class="col-md-2">
                                <div class="toggle-custom">
                                    <label class="toggle" data-off="NO" data-on="SI">
                                        <input id="in_estado" type="checkbox" name="checkbox-toggle" />
                                        <span class="button-checkbox"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-7"></div>
                            <div class="col-md-3 text-right">
                                <label class="control-label" for="Dragontail"><b>Crear en Dragontail</b></label>
                            </div>
                            <div class="col-md-2">
                                <div class="toggle-custom">
                                    <label class="toggle" data-off="NO" data-on="SI">
                                        <input id="Dragontail" type="checkbox" name="checkbox-toggle" />
                                        <span class="button-checkbox"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 control-label" for="in_tipo"><b>Tipo:</b></label>
                            <div class="col-md-9">
                                <select class="form-control" id="in_tipo">
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <label for="in_empresa" class="col-md-2 control-label"><b>Empresa(*):</b></label>
                                <div class="col-md-9">
                                <select class="form-control" id="in_empresa">
                                </select>
                                </div>
                            </div>
                        </div>

                        <br/>
                        <div class="row">
                            <div class="form-group">
                                <label for="in_empresa" class="col-md-2 control-label"><b>Tipo Identificacion(*):</b></label>
                                <div class="col-md-9">
                                <select class="form-control" id="in_tipo_identificacion">
                                </select>
                                </div>
                            </div>
                        </div>

                        <br/>

                        <div class="form-group row">
                            <label for="in_documento" class="col-md-2 control-label"><b>Identificación(*):</b></label>
                            <div class="col-md-4">
                                <input id="in_documento" class="form-control" type="text">
                            </div>
                        </div>

                        <div class="form-group row" id="div_nombres">
                            <label for="in_nombres" class="col-md-2 control-label"><b>Nombres(*):</b></label>
                            <div class="col-md-4">
                                <input id="in_nombres" class="form-control" type="text">
                            </div>
                            <label for="in_apellidos" class="col-md-2 control-label"><b>Apellidos(*):</b></label>
                            <div class="col-md-4">
                                <input id="in_apellidos" class="form-control" type="text">
                            </div>
                        </div>

                        <div class="form-group row" id="div_ciudad">
                            <label for="in_telefono" class="col-md-2 control-label"><b>Ciudad(*):</b></label>
                            <div class="col-md-4">
                                <input id="in_ciudad_formulario" class="form-control" type="text">
                            </div>
                        </div>


                        <div class="form-group row" id="div_telefono">
                            <label for="in_telefono" class="col-md-2 control-label"><b>Telefono(*):</b></label>
                            <div class="col-md-4">
                                <input id="in_telefono" class="form-control" type="text">
                            </div>
                        </div>
                        
                        <div class="form-group row" id="div_nomina">
                            <label for="in_nomina" class="col-md-2 control-label"><b>Codigo Nomina:</b></label>
                            <div class="col-md-4">
                                <input id="in_nomina" class="form-control" type="text">
                            </div>
                        </div>                            

                    </div>
                    <div class="modal-footer quitarMarginTop" id="div_botones">
                        <button class="btn btn-default" type="button" data-dismiss="modal">Cancelar</button>
                        <button id="btnEstGuardarCambios" class="btn btn-primary" type="button" onclick="guardarMotorizado()">
                            <i class="glyphicon glyphicon-floppy-saved mr10"></i>Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando" ng-show="cargando" onclick="cargando( false )">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../../imagenes/admin_resources/progressBar.gif"/>
            </div>
        </div>

        <!-- JavaScript -->
        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>     
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-editable.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/switch.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>
        <script language="javascript1.1" src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/inputnumber.js"></script>
        <script type="text/javascript" src="../../js/ajax_adminMotorizado.js"></script>
        <script type="text/javascript" src="../../js/ajax_dragontailRider.js"></script>
        <script src="../../bootstrap/js/switch.js"></script>

    </body>
</html>