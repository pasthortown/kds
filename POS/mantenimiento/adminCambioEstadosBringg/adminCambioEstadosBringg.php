<?php
/**
 * Created by PhpStorm.
 * User: nathaly.sanchez
 * Date: 25/9/2020
 * Time: 10:45
 */
session_start();

ini_set("memory_limit", "256M");
header("Expires: Fri, 25 Dec 1980 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

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
    <link href="../../bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../../bootstrap/css/bootstrap-switch.css" rel="stylesheet">
    <!--<link rel="stylesheet" type="text/css" href="../../css/switch/switch.css" />-->
    <!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">-->

    <style type="text/css" media="screen">
        .colorRow {
            background-color: #a6a6a6 !important;

        }
        .colorBlack{
            background-color: #f67c84 !important;
        }
        table.dataTable tbody tr.selected {
            color: white;
            background-color: #eeeeee;
        }

    </style>

</head>
<body>
<input type="hidden" name="hide_rst_id" id="hide_rst_id" value="<?php echo $rst_id; ?>"/>
<input type="hidden" name="hide_cdn_id" id="hide_cdn_id" value="<?php echo $cdn_id; ?>"/>
<input id="sess_usr_id" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
<input id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
<input id="IDPeriodo" name="IDPeriodo" type="hidden" value="0"/>
<input id="estado" name="estado" type="hidden" value="0"/>
<input id="codFactura" name="codFactura" type="hidden" value="0"/>
<input id="codApp" name="codApp" type="hidden" value="0"/>
<input id="idMotivo" name="idMotivo" type="hidden" value="0"/>
<input id="idMotorolo" name="idMotorolo" type="hidden" value="0"/>
<input id="banderaMotorolo" name="banderaMotorolo" type="hidden" value="0"/>
<input id="medioMotorolo" name="medioMotorolo" type="hidden" value="0"/>


<div class="superior">
    <div class="tituloPantalla">
        <h1>Cambio Estados Bringg</h1>
    </div>
</div>
<div class="contenedor">
    <div class="inferior">

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
            <div id="divCambioEstadosBringg"  class="panel-body">
                <table id="tablaCambioEstados" class="table table-striped table-bordered table-sm table-hover"
                    style="margin-left: 1px; margin-top: 10px;" width="100%"></table>
            </div>
            <div id="divCambioEstadosUtimaMilla" class="panel-body">
                <table id="sasdsadas" class="table table-striped table-bordered table-sm table-hover"
                    style="margin-left: 1px; margin-top: 10px;" width="100%"></table>
            </div>
        </div>
    </div> <!-- Fin Contenedor Inferior -->
</div>  <!-- Fin Contenedor -->

<!--Inicio de modal de cambios de estado Ultima Milla -->
<div class="modal fade" id="ordenChanger" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">cambiar estado a ENTREGADO</h4>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="cambiarEstado()">Save changes</button>
        </div>
        </div>
    </div>
</div>

<!--Inicio de modal de Motivos de Cambio de Estados -->
<div class="modal fade" id="mdl_Motivos" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 700px;">
        <div class="modal-content" >
            <div class="modal-header">
                <h4 class="modal-title">Seleccione un Motivo para el Cambio de Estados</label></h4>
            </div>
            <div class="modal-body">
                <table id="tablaMotivos" align="center" class="table table-sm table-hover table-bordered"   width="70%"></table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" id="btnGurdaMotivo" class="btn btn-primary" onclick="fn_guardaMotivoCambiEstado()" >Guardar</button>
            </div>
            <!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
</div><!-- /.modal -->
<!--fin-->
<!--Inicio de modal Motorolos -->
<div id="mdl_Motorolos" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalPopoversLabel">Asignar Motorizado</h4>
            </div>
            <div class="modal-body">
                <table id="tablaMotorolo"  class="table table-sm table-hover table-bordered"  width="70%"></table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"  data-dismiss="modal">Cancelar</button>
                <button type="button" id="btnGurdaMotorolo" class="btn btn-primary"  onclick="fn_asignarMotorizado()">Guardar</button>
            </div>
        </div>
    </div>
</div>
<!--fin-->

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
<!--<script src='https://kit.fontawesome.com/a076d05399.js'></script>-->
<!--<script type="text/javascript" src="../../js/ajax_trade.js"></script>-->
<script type="text/javascript" src="../../js/ajax_adminCambioEstadosBringg.js"></script>

</body>
</html>
