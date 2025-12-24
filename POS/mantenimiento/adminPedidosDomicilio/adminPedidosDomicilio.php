<?php
session_start();

ini_set("memory_limit", "256M");
header("Expires: Fri, 25 Dec 1980 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Configuracion de Pantallas ////////////////////////////////
///////FECHA CREACION: 25-01-2016 /////////////////////////////////////////////
///////FECHA DE MODIFICACION: 22-12-2016 //////////////////////////////////////
///////USUARIO QUE MODIFICO: Juan Estevez /////////////////////////////////////
///////DESCRIPCION DEL ULTIMO CAMBIO: Creacion de panel ///////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_seguridades.php';
include_once '../../seguridades/seguridad_niv3.inc';


$cdn_id = $_SESSION['cadenaId'];
$rst_id = $_SESSION['rstId'];
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
        <input type="hidden" name="hide_rst_id" id="hide_rst_id" value="<?php echo $rst_id; ?>"/>
        <input type="hidden" name="hide_cdn_id" id="hide_cdn_id" value="<?php echo $cdn_id; ?>"/>
        <input id="sess_usr_id" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
        <input id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
        <input id="url_api_motorizados" type="hidden" />
        <input id="url_api_motorizados_gerente" type="hidden" />
        <input id="id_pais" type="hidden" />
        <input id="nombre_pais" type="hidden" />
        <input id="id_ciudad" type="hidden" />
        <input id="nombre_ciudad" type="hidden" />


        <div class="superior">
            <div class="tituloPantalla">
                <h1>Gestión Motorizado</h1>
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
                            <div class="col-sm-3">
                                <h5>
                                    <b>Periodo: </b>
                                    <span id="prd_fecha"></span>
                                </h5>
                               
                            </div>
                            <div class="col-sm-4">
                                <h5>
                                    <b>Usuario: </b>
                                    <span id="prd_usuario"></span>
                                </h5>
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
                        <table id="listado_pedido_app" class="table table-bordered table-hover"></table>
                    </div>
                </div>
            </div> <!-- Fin Contenedor Inferior -->
        </div>  <!-- Fin Contenedor -->

        <!-- Modal Pedidos Motorizado -->
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" style="width:1000px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 id="titulomodal" class="modal-title">Motorizado</h3>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-2"><b>Motorizado:</b></div>
                            <div id="tqt_motorizado" class="col-sm-3"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2"><b>Documento:</b></div>
                            <div id="tqt_documento" class="col-sm-3"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2"><b>Telefono:</b></div>
                            <div id="tqt_telefono" class="col-sm-3"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2"><b>Estado:</b></div>
                            <div id="tqt_estado" class="col-sm-3"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2"><b>Fecha Ingreso:</b></div>
                            <div id="tqt_fecha_inicio" class="col-sm-3"></div>
                            <div class="col-sm-2"><b>Fecha Salida:</b></div>
                            <div id="tqt_fecha_fin" class="col-sm-3"></div>
                        </div>
                        <br/>
                        <table id="lst_transacciones" class="table table-bordered table-hover"></table>
                    </div>
                    <div class="modal-footer quitarMarginTop">
                        <button class="btn btn-default" type="button" data-dismiss="modal">Cancelar</button>
                        <button id="btnEstGuardarCambios" class="btn btn-primary" type="button" onclick="finalizarTurnoMotorizado()">
                            <i class="glyphicon glyphicon-floppy-saved mr10"></i>Finalizar Turno
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Pedidos Motorizado -->
        <div class="modal fade" id="mdl_motorizados" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" style="width:1000px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 id="titulomodal" class="modal-title">Motorizado</h3>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-9"></div>
                            <div class="col-sm-3">
                            <div  class="dataTables_filter">
                                <label>Buscar:
                                    <input id="lst_mtrzds_ctvs_filter" type="search" class="form-control input-sm" placeholder="" aria-controls="lst_mtrzds_ctvs">
                                </label>
                            </div>
                            </div>
                        </div> 
                        <table id="lst_mtrzds_ctvs" class="table table-bordered table-hover"></table>
                    </div>
                    <div class="modal-footer quitarMarginTop">
                        <button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="modalAsignarMotorizado" class="modal fade bs-example-modal-sm" style="overflow-y: hidden;" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document" style="width:1000px;">
                <div class="modal-content">
                    <div class="panel panel-primary" style="margin-bottom:0px; ">
                        <div class="panel-heading"> <span id="asignarHeader"></span></div>
                        <div class="panel-body" style="padding: 0">
                            <!--inicio panel body-->
                            <div class="container-fluid">
                               
                                <table id="lstPeriodoMotorizados" class="table table-bordered table-hover"></table>
                            </div>
                        </div> <!--    FIn panel body-->
                        <div class="panel-footer" id="asignarFooter" >
                        <button class="btn btn-primary" onClick="$('#modalAsignarMotorizado').modal('toggle');">Cerrar</button>
                    </div>
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
        <script type="text/javascript" src="../../js/ajax_trade.js"></script>
        <script type="text/javascript" src="../../js/ajax_adminPedidosDomicilio.js"></script>
        <script src="../../bootstrap/js/switch.js"></script>  

    </body>
</html>