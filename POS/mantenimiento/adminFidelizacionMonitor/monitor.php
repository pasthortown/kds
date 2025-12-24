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
<link rel="stylesheet" type="text/css" href="../../css/monitor.css" />
    <!-- Menú superior -->
    <div class="superior">
        <div class="tituloPantalla">
            <h1>Monitor Fidelización</h1>
        </div>
    </div>
    <!-- Start .page-content-inner -->
    
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <h4>Rango de Fecha</h4>
        </div>
    </div>
    
    <div class="row">
        
        <!--INPUTs-->
        <div class="col-lg-12 col-md-12">
            <div class="form-group">
                <label class="col-lg-2 col-md-2 control-label" for="inFechaDesde">Fecha Desde:</label>
                <div class="col-lg-2 col-md-2">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input inputmode="none"  id="inFechaDesde" type="text" class="form-control">
                    </div>
                </div>
                <label class="col-lg-2 col-md-2 control-label" for="inFechaHasta">Fecha Hasta:</label>
                <div class="col-lg-2 col-md-2">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input inputmode="none"  id="inFechaHasta" type="text" class="form-control">
                    </div>
                </div>
                <div class="col-lg-2 col-md-2">
                    <button id="btnCargarDatos" type="button" class="btn btn-success mr5 mb10">Consultar</button>
                </div>
            </div>
        </div>
    
    </div>
        
    <div class="row">
        
        <!--OUTPUTs-->
        <div class="col-lg-12 col-md-12 sortable-layout">
            
            <div id="cntMonitor" class="cntMonitor">
                
                <div id="cntMonitorSuperior" class="cntMonitorSuperior">
                    <div id="superiorIzquierda" class="superiorIzquierda"></div>
                    <div id="superiorDerecha" class="superiorDerecha"></div>
                </div>
                <div id="cntMonitorInferior" class="cntMonitorInferior">
                    <div id="inferiorIzquierda" class="inferior"></div>
                    <div id="inferiorDerecha" class="inferiorDerecha"></div>
                </div>
                
            </div>
              
        </div>
    </div>
    
    <input inputmode="none"  id="inTipoAmbiente" type="hidden" value="<?php echo $tipoAmbiente->tipoambiente;?>" />
    <input inputmode="none"  id="inAplicaPlan" type="hidden" value="<?php echo $aplicaPlan["aplicaConfiguracion"];?>" />
    
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

    <script src="../../bootstrap/plugins/forms/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    
    <script type="text/javascript" src="../../bootstrap/highcharts/highcharts.js"></script>
    <script type="text/javascript" src="../../bootstrap/highcharts/modules/exporting.js"></script>

    <script type="text/javascript" src="../../js/ajax_fidelizacion_monitor.js"></script>