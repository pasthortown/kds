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
        <h1>Logs Fidelización</h1>
    </div>
</div>

<!-- Start .page-content-inner -->
<div class="row">
    <div class="col-lg-12 col-md-12 sortable-layout">
        <div class="panel panel-default toggle panelMove panelClose panelRefresh">

            <!-- Start .panel -->
            <div class="panel-heading">
                <h4 class="panel-title">Filtros</h4>
            </div>
            <div class="panel-body pt1 pb0">

                <div class="row">
                    <div class="col-md-2">
                        <strong> Inicio: </strong> 
                        <input inputmode="none"  type="date" step="1"  name="fechaInicio" id="init"   max="<?php echo date("Y-m-d"); ?>"    value="<?php echo date("Y-m-d"); ?>">
                    </div>
                    <div class="col-md-2">
                        <strong> Fin: </strong>
                        <input inputmode="none"  type="date" name="fechaFin"  id="fin" step="1"  max="<?php echo date("Y-m-d"); ?>"   value="<?php echo date("Y-m-d"); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="button" style="margin-left: 15%;" onclick="cargarLogs()"class="btn btn-primary">Cargar</button>
                    </div>
                </div>

                 <form id="frmConfiguracionesCadena" class="form-horizontal group-border stripped tabSettingsNotasCredito pt5">
                    <div class="panel panel-default toggle panelMove panelClose panelRefresh">
                        <!-- TABLA LOGS -->
                        <table id="tblLogs" class="table table-bordered" style="font-size: 11px">
                            <thead>
                                <tr>
                                    <th class="per20">Acci&oacute;n</th>
                                    <th>descripcion</th>
                                    <th class="per10">usuario</th>
                                    <th class="per10">Fecha</th>
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
</div>


<input inputmode="none"  id="inTipoAmbiente" type="hidden" value="<?php echo $tipoAmbiente->tipoambiente; ?>" />
<input inputmode="none"  id="inAplicaPlan" type="hidden" value="<?php echo $aplicaPlan["aplicaConfiguracion"]; ?>" />

<div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
    <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
        <img src="../../imagenes/admin_resources/progressBar.gif" />
    </div>
</div>

<script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
<script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
<script type="text/javascript" src="../../js/alertify.js"></script>
<script type="text/javascript" src="../../bootstrap/templete/plugins/tables/datatables/jquery.dataTables.js"></script>
<script type="text/javascript" src="../../bootstrap/templete/plugins/tables/datatables/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="../../bootstrap/templete/plugins/ui/bootbox/bootbox.js"></script>
<script type="text/javascript" src="../../js/ajax_fidelizacion_logs.js"></script>
<script type="text/javascript">
            cargando(0);
</script>
