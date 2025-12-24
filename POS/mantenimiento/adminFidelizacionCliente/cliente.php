<?php
session_start();


include_once '../../seguridades/seguridad.inc';
include_once"../../system/conexion/clase_sql.php";
include_once '../../clases/clase_ambiente.php';
include_once '../../clases/clase_fidelizacionCadena.php';

$idCadena = $_SESSION['cadenaId'];
$idRestaurante = $_SESSION['rstId'];
$ambiente = new Ambiente();

$oCadena = new Cadena();
$aplicaPlan = $oCadena->guardarConfiguracionPoliticaAplicaCadenaObjeto($idCadena);
$imprimePuntosRide = $oCadena->guardarConfiguracionPoliticaAplicaCadenaObjetoPuntosRide($idCadena,$idRestaurante);
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
    
    .centrar {
         display: flex;  border: 0;  justify-content: center; align-items: center;
    }
</style>
<!-- Menú superior -->
<div class="superior">
    <div class="tituloPantalla">
        <h1>Clientes</h1>
    </div>
</div>


<div class="panel-body pt0 pb0">
    <div class="row mt5">
        <div class="col-lg-4">
            <label class="control-label mt5" for=""><b>Búsqueda Cédula / RUC</b></label>
            <div class="input-group input-sm pl0 ml0">
                <input inputmode="none"  id="inCedula" type="text" class="form-control">
                <span class="input-group-btn">
                    <button id="btnBuscar" class="btn btn-success" type="button"><i class="glyphicon glyphicon-search mr5"></i> Buscar</button>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-5 col-md-12 sortable-layout p0">

        <!-- Inicio Datos de Cliente -->
        <div class="panel-body p1">
            <div class="well">
                <h4 class="pt1 mt1">Plan Amigos <?php echo $_SESSION['cadenaNombre']; ?></h4>
                <ul class="list-unstyled mb0">
                    <li id="cStatus" class="mt5"></li>
                    <li id="cName" class="mt5"></li>
                    <li id="cDocument" class="mt5"></li>
                    <li id="cGender" class="mt5"></li>
                    <li id="cGender" class="mt5"></li>
                    <li id="cPhone" class="mt5"></li>
                    <li id="cEmail" class="mt5"></li>
                    <li id="cBirthDate" class="mt5"></li>
                    <li id="cPoints" class="mt5"></li>
                    <li id="cBalance" class="mt5"></li>
                </ul>
            </div>
            <!-- <div class="well">Hola</div> -->
        </div>
    <!-- Fin Datos de Cliente-->
    </div>

    <!-- Inicio Recargas -->
    <div class="col-lg-7 col-md-12 sortable-layout pl0 pr0">
        <div class="panel-body pl0 pr1">
            <div class="well">


                <div class="tabs mb20">
                    <ul id="tabsSettings" class="nav nav-tabs">
                        <li class="active">
                            <a href="#tabReloads" data-toggle="tab" aria-expanded="true"><h5>Recargas</h5></a>
                        </li>
                        <li class="lead">
                            <a href="#tabPoints" data-toggle="tab"><h5>Puntos</h5></a>
                        </li>
                    </ul>


                    <!-- Reloads -->
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="tabReloads">
                            <!-- TABLA TRANSACCIONES -->
                            <table id="tblReloadByCustomers" class="table table-bordered" style="font-size: 10px">
                                <thead>
                                    <tr>
                                        <th>Movement</th>
                                        <th>Date</th>
                                        <th>Balance</th>
                                        <th>DoC</th>
                                        <th>Store</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <!-- END TABLA TRANSACCIONES -->
                        </div>
                        <!-- Points -->
                        <div class="tab-pane" id="tabPoints">
                            <!-- TABLA TRANSACCIONES -->
                            <table id="tblPointsByCustomer" class="table table-bordered" style="font-size: 10px">
                                <thead>
                                    <tr>
                                        <th>Movement</th>
                                        <th>Date</th>
                                        <th>Points</th>
                                        <th>DoC</th>
                                        <th>Store</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <!-- END TABLA TRANSACCIONES -->
                        </div>
                    </div>
                </div>





                
                
            </div>
        </div>
    </div>
    <!-- Fin Recargas -->

</div>




<input inputmode="none"  id="inTipoAmbiente" type="hidden" value="<?php echo $tipoAmbiente->tipoambiente; ?>" />
<input inputmode="none"  id="inAplicaPlan" type="hidden" value="<?php echo $aplicaPlan["aplicaConfiguracion"]; ?>" />
<input inputmode="none"  id="inImprimePuntosRide" type="hidden" value="<?php echo $imprimePuntosRide["imprimePuntosRide"]; ?>" />
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
<script type="text/javascript" src="../../js/ajax_fidelizacion_clientes.js"></script>
<script type="text/javascript">
    cargando(0);
</script>