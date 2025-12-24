<?php
session_start();

include_once '../../seguridades/seguridad.inc';
include_once"../../system/conexion/clase_sql.php";
include_once '../../clases/clase_ambiente.php';
include_once '../../clases/clase_fidelizacionCadena.php';

$idCadena = $_SESSION['cadenaId'];
$idRestaurante = $_SESSION['rstId'];
$idUsuario = $_SESSION['usuarioId'];

$ambiente = new Ambiente();

$oCadena = new Cadena();
$aplicaPlan = $oCadena->guardarConfiguracionPoliticaAplicaCadenaObjeto($idCadena);

$tipoAmbiente = json_decode($ambiente->cargarAmbiente());
?>

<!-- CSS -->
<link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
<link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
<link rel="stylesheet" type="text/css" href="../../css/progressBar.css" />
<style type="text/css">
    input[type="text"]:disabled {
        background-color: #fff;
    }
</style>
<!-- Menú superior -->
<div class="superior">
    <div class="tituloPantalla">
        <h1>Transacciones en Error</h1>
    </div>
</div>

<!-- Start .page-content-inner -->
<div class="row">

    <div class="col-lg-12 col-md-12 sortable-layout">
        <div class="panel panel-default toggle panelMove panelClose panelRefresh">
            <div class="panel-heading">
                <h4 class="panel-title">Filtros</h4>
            </div>
            <div class="row mt10 pl10">
                <div class="col-md-2">
                    <strong> Inicio: </strong> 
                    <input inputmode="none"  type="date" step="1"  name="fechaInicio" id="init"   max="<?php echo date("Y-m-d"); ?>"    value="<?php echo date("Y-m-d"); ?>">
                </div>
                <div class="col-md-2">
                    <strong> Fin: </strong>
                    <input inputmode="none"  type="date" name="fechaFin"  id="fin" step="1"  max="<?php echo date("Y-m-d"); ?>"   value="<?php echo date("Y-m-d"); ?>">
                </div>
                <div class="col-md-3">
                    <button type="button" style="margin-left: 15%;" onclick="CargarPeriodos()"class="btn btn-primary">Cargar Periodos</button> 
                </div>
            </div>
            <div class="row mt10 pl10">
                <!-- PERIODO -->
                <div class="form-group">
                    <div class="col-lg-6 col-md-6">
                        <strong>Periodo: </strong> 
                        <select id="inPeriodoRestaurante" class="form-control"></select>
                    </div>
                </div>
            </div>
            <div class="tabs mt20">
                <ul id="tabsSettings" class="nav nav-tabs">
                    <li class="active">
                        <a href="#tabSettingsCadena" data-toggle="tab" aria-expanded="true"><h5>Facturas</h5></a>
                    </li>
                    <li class="lead">
                        <a href="#tabSettingsProductosNC" data-toggle="tab"><h5>Notas de cr&eacute;dito</h5></a>
                    </li>
                </ul>

                <!-- BEGIN Tabs -->
                <div id="myTabContent2" class="tab-content">
                    <!-- Facturas -->
                    <div class="tab-pane fade active in" id="tabSettingsCadena">
                        <div id="cntCancelar" style="display: none">
                            <button id="btnListo" type="button" class="btn btn-primary mr5 mb10">Listo</button>
                        </div>
                        <!-- col-lg-12 start here -->
                        <div class="panel panel-default toggle panelMove panelClose panelRefresh">

                            <div class="panel-heading">
                                <h4 class="panel-title">Facturas en Error</h4>
                            </div>
                            <!-- Start .panel -->
                            <div class="panel-body pt0 pb0">
                                <form id="frmConfiguracionesCadena" class="form-horizontal group-border stripped tabSettingsNotasCredito">
                                    
                                    <div class="panel panel-default toggle panelMove panelClose panelRefresh">
                                        <!-- TABLA PRODUCTOS -->
                                        <table id="tblFacturasError" class="table table-bordered" style="font-size: 11px">
                                            <thead>
                                                <tr>
                                                    <th class="per10">Fecha</th>
                                                    <th class="per10">IDFactura</th>
                                                    <th class="per10">Secuencial</th>
                                                    <th class="per10">Total</th>
                                                    <th class="per10">C&eacute;dula Cliente</th>
                                                    <th class="per20">Cliente</th>
                                                    <th class="per10">Error</th>
                                                    <th class="per20">Mensaje</th>
                                                    <th class="per10">Opción</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Notas de Credito -->
                    <div class="tab-pane fade" id="tabSettingsProductosNC">
                        <div class="panel panel-default toggle panelMove panelClose panelRefresh">
                            <!-- Start .panel -->
                            <div class="panel-heading">
                                <h4 class="panel-title">Notas de Cr&eacute;dito en Error</h4>
                            </div>
                            <div class="panel-body pt0 pb0">
                                <form id="frmConfiguracionesCadena" class="form-horizontal group-border stripped tabSettingsNotasCredito">
                                    <div class="panel panel-default toggle panelMove panelClose panelRefresh">
                                        <!-- TABLA PRODUCTOS -->
                                        <table id="tblNotasCreditoError" class="table table-bordered" style="font-size: 11px">
                                            <thead>
                                                <tr>
                                                    <th class="per10">Fecha</th>
                                                    <th class="per10">IDFactura</th>
                                                    <th class="per10">Secuencial</th>
                                                    <th class="per5">Total</th>
                                                    <th class="per10">C&eacute;dula Cliente</th>
                                                    <th class="per10">Cliente</th>
                                                    <th class="per20">Error</th>
                                                    <th class="per10">Mensaje</th>
                                                    <th class="per10">Opción</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                        <!-- END TABLA PRODUCTOS -->
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <!-- END Tabs -->
                </div>
            </div>
        </div>
    </div>
</div>
<input inputmode="none"  id="inTipoAmbiente" type="hidden" value="<?php echo $tipoAmbiente->tipoambiente; ?>" />
<input inputmode="none"  id="inAplicaPlan" type="hidden" value="<?php echo $aplicaPlan["aplicaConfiguracion"]; ?>" />
<!-- Modal Cargando -->
<div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
    <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
        <img src="../../imagenes/admin_resources/progressBar.gif" />
    </div>
</div>
<!-- Scripts -->
<script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
<script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
<script type="text/javascript" src="../../js/alertify.js"></script>
<script type="text/javascript" src="../../bootstrap/templete/plugins/tables/datatables/jquery.dataTables.js"></script>
<script type="text/javascript" src="../../bootstrap/templete/plugins/tables/datatables/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="../../bootstrap/templete/plugins/ui/bootbox/bootbox.js"></script>
<script type="text/javascript" src="../../js/ajax_fidelizacion_error.js"></script>
<script type="text/javascript">
            cargando(0);
</script>
