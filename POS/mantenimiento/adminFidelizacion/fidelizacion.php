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
<!DOCTYPE html>
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
    <div class="menu" style="width: 300px;" align="center">
        <ul>
            <li>
<?php
if ($tipoAmbiente->tipoambiente == "azure") {
    if ($aplicaPlan["aplicaConfiguracion"] > 0) {
        ?>
                        <button id="btnDeshabilitarPlanFidelizacion" class="botonMnSpr quitarPaddingBottom l-basic-elaboration-document-plus" onclick="deshabilitarPlanFidelizacion();">
                            <span>Des.</span>
                        </button>
        <?php
    } else {
        ?>
                        <button id="btnHabilitarPlanFidelizacion" class="botonMnSpr quitarPaddingBottom l-basic-elaboration-document-plus" onclick="habilitarPlanFidelizacion();">
                            <span>Hab.</span>
                        </button>
        <?php
    }
}
?>
            </li>
        </ul>
    </div>
    <div class="tituloPantalla">
        <h1>Fidelización</h1>
    </div>
</div>
<!-- Start .page-content-inner -->
<div class="row">
    <div class="col-lg-12 col-md-12 sortable-layout">
        <div>
            <div class="tabs mb20">
                <ul id="tabsSettings" class="nav nav-tabs">
                    <li class="active">
                        <a href="#tabSettingsCadena" data-toggle="tab" aria-expanded="true"><h5>Cadena</h5></a>
                    </li>
                    <li class="lead">
                        <a href="#tabSettingsRestaurante" data-toggle="tab"><h5>Restaurante</h5></a>
                    </li>
                    <li class="">
                        <a href="#tabSettingsProductos" data-toggle="tab"><h5>Productos</h5></a>
                    </li>
                     <li class="">
                        <a href="#tabSettingsFormasPago" data-toggle="tab"><h5>Formas de pagos</h5></a>
                    </li>
                </ul>
                <div id="myTabContent2" class="tab-content">
                    <div class="tab-pane fade active in" id="tabSettingsCadena">
<?php if ($aplicaPlan["aplicaConfiguracion"] > 0) { ?>
                            <div id="cntEditar">
                                <button id="btnEditarConfiguracionCadena" type="button" class="btn btn-default mr5 mb10"><i class="glyphicon glyphicon-edit mr5"></i> Editar</button>
                            </div>
<?php } ?>
                        <div id="cntCancelar" style="display: none">
                            <button id="btnListo" type="button" class="btn btn-primary mr5 mb10">Listo</button>
                        </div>
                        <!-- col-lg-12 start here -->
                        <div class="panel panel-default toggle panelMove panelClose panelRefresh">

                            <!-- Start .panel -->
                            <div class="panel-heading">
                                <h4 class="panel-title">Configuraciones Generales</h4>
                            </div>
                            <div class="panel-body pt0 pb0">
                                <form id="frmConfiguracionesCadena" class="form-horizontal group-border stripped frmConfiguracionesCadena">

                                    <!-- NOMBRE PLAN -->
                                    <div class="form-group">
                                        <label class="col-lg-2 col-md-3 control-label" for="inNombrePlan"><b>Nombre del Plan</b></label>
                                        <div class="col-lg-10 col-md-9">
                                            <input inputmode="none"  id="inNombrePlan" parametro="NOMBRE PLAN" type="text" onchange="guardarConfiguracionCadena(this);" tipo="input" class="form-control input-rounded" disabled/>
                                        </div>
                                    </div>
                                    <!-- PREGUNTA INICIO -->
                                    <div class="form-group">
                                        <label class="col-lg-2 col-md-3 control-label" for="inBienvenida"><b>Bienvenida</b></label>
                                        <div class="col-lg-10 col-md-9">
                                            <input inputmode="none"  id="inBienvenida" parametro="BIENVENIDA" type="text" onchange="guardarConfiguracionCadena(this);" tipo="input" class="form-control input-rounded" disabled/>
                                        </div>
                                    </div>
                                    <!-- PREGUNTA -->
                                    <div class="form-group">
                                        <label class="col-lg-2 col-md-3 control-label" for="inPregunta"><b>Pregunta</b></label>
                                        <div class="col-lg-10 col-md-9">
                                            <input inputmode="none"  id="inPregunta" parametro="PREGUNTA REGISTRO" type="text" onchange="guardarConfiguracionCadena(this);" tipo="input" class="form-control input-rounded" disabled/>
                                        </div>
                                    </div>
                                    <!-- TEXTO DEPEDIDA -->
                                    <div class="form-group">
                                        <label class="col-lg-2 col-md-3 control-label" for="inDespedida"><b>Despedida</b></label>
                                        <div class="col-lg-10 col-md-9">
                                            <input inputmode="none"  id="inDespedida" parametro="DESPEDIDA" type="text" onchange="guardarConfiguracionCadena(this);" tipo="input" class="form-control input-rounded" disabled/>
                                        </div>
                                    </div>
                                    <!-- URL WEB -->
                                    <div class="form-group">
                                        <label class="col-lg-2 col-md-3 control-label" for="inUrlPaginaWeb"><b>Url P&aacute;gina Web</b></label>
                                        <div class="col-lg-10 col-md-9">
                                            <input inputmode="none"  id="inUrlPaginaWeb" parametro="URL WEB" type="text" onchange="guardarConfiguracionCadena(this);" tipo="input" class="form-control input-rounded" disabled/>
                                        </div>
                                    </div>
                                    <!-- NOMBRE APP -->
                                    <div class="form-group">
                                        <label class="col-lg-2 col-md-3 control-label" for="inNombreApp"><b>Nombre AppM&oacute;vil</b></label>
                                        <div class="col-lg-10 col-md-9">
                                            <input inputmode="none"  id="inNombreApp" parametro="NOMBRE PLAN" type="text" onchange="guardarConfiguracionCadena(this);" tipo="input" class="form-control input-rounded" disabled/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-12 col-md-12">
                                            <h5><b>AutoConsumo</b></h5>
                                        </label>
                                        <label class="col-lg-6 col-md-6" for="inRuc"><b>RUC</b></label>
                                        <label class="col-lg-6 col-md-6" for="inRazonSocial"><b>Raz&oacute;n Social</b></label>
                                        <div class="col-lg-6 col-md-6">
                                            <input inputmode="none"  id="inRuc" parametro="AUTOCONSUMO RUC" type="text" onchange="guardarConfiguracionCadena(this);" tipo="input" class="form-control input-rounded" disabled/>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <input inputmode="none"  id="inRazonSocial" parametro="AUTOCONSUMO RAZON SOCIAL" type="text" onchange="guardarConfiguracionCadena(this);" tipo="input" class="form-control input-rounded" disabled/>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-lg-12 col-md-12">
                                            <h5><b>Interface</b></h5>
                                        </label>
                                        <label class="col-lg-6 col-md-6" for="inRuc"><b>RUC</b></label>
                                        <label class="col-lg-6 col-md-6" for="inRazonSocial"><b>Raz&oacute;n Social</b></label>
                                        <div class="col-lg-6 col-md-6">
                                            <input inputmode="none"  id="txtRucInterface" parametro="INTERFACE RUC" type="text" onchange="guardarConfiguracionCadena(this);" tipo="input" class="form-control input-rounded" disabled/>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <input inputmode="none"  id="inRazonSocialInterface" parametro="INTERFACE RAZON SOCIAL" type="text" onchange="guardarConfiguracionCadena(this);" tipo="input" class="form-control input-rounded" disabled/>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-lg-12 col-md-12">
                                            <h5><b>Impresiones</b></h5>
                                        </label>
                                        <label class="col-lg-6 col-md-6" for="inRazonSocial"><b>T&iacute;tulo Voucher</b></label>
                                        <label class="col-lg-6 col-md-6" for="inTituloRide"><b>T&iacute;tulo RIDE</b></label>
                                        <div class="col-lg-6 col-md-6">
                                            <input inputmode="none"  id="inTituloVoucher" parametro="TITULO VOUCHER" type="text" onchange="guardarConfiguracionCadena(this);" tipo="input" class="form-control input-rounded" disabled/>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <input inputmode="none"  id="inTituloRide" parametro="TITULO RIDE" type="text" onchange="guardarConfiguracionCadena(this);" tipo="input" class="form-control input-rounded" disabled/>
                                        </div>
                                        <label class="col-lg-6 col-md-6 mt10" for="inFormatoVoucher"><b>Formato de Voucher</b></label>
                                        <label class="col-lg-6 col-md-6 mt10" for="inFormatoRide"><b>Formato RIDE</b></label>
                                        <div class="col-lg-6 col-md-6">
                                            <textarea id="inFormatoVoucher" parametro="FORMATO VOUCHER" onchange="guardarConfiguracionCadena(this);" tipo="text" class="form-control limitTextarea" maxlength="500" rows="8" style="resize: none; background: #FFF" disabled></textarea>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <textarea id="inFormatoRide" parametro="FORMATO RIDE" onchange="guardarConfiguracionCadena(this);" tipo="text" class="form-control limitTextarea" maxlength="500" rows="8" style="resize: none; background: #FFF" disabled></textarea>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane fade" id="tabSettingsRestaurante">

                        <!-- col-lg-12 start here -->
                        <div class="panel panel-default toggle panelMove panelClose panelRefresh">

                            <!-- TABLA RESTAURANTE -->
                            <table id="tblRestaurantes" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="per50">Restaurante</th>
                                        <th class="per20">Latitud</th>
                                        <th class="per20">Longitud</th>
                                        <th class="per10 text-center">Aplica</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <!-- END TABLA RESTAURANTE -->

                        </div>

                    </div>
                    <!--productos-->
                    <div class="tab-pane fade" id="tabSettingsProductos">

                        <div class="panel panel-default toggle panelMove panelClose panelRefresh">

                            <!-- Start .panel -->
                            <div class="panel-heading">
                                <h4 class="panel-title">Configuraciones Generales</h4>
                            </div>
                            <div class="panel-body pt0 pb0">

                                <div class="panel panel-default toggle panelMove panelClose panelRefresh">

                                    <!-- TABLA PRODUCTOS -->
                                    <table id="tblProductos" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="per10">NumPlu</th>
                                                <th class="per30">Producto</th>
                                                <th class="per10">Puntos</th>
                                                <th class="per40">Descripci&oacute;n</th>
                                                <th class="per10">Orden</th>
                                                <th class="per10 text-center">Aplica</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <!-- END TABLA PRODUCTOS -->

                                </div>


                            </div>
                        </div>


                    </div>
                    
                    
                    <!--tabSettingsFormasPago-->
                       <div class="tab-pane fade" id="tabSettingsFormasPago">

                        <div class="panel panel-default toggle panelMove panelClose panelRefresh">

                            <!-- Start .panel -->
                            <div class="panel-heading">
                                <h4 class="panel-title">Configuraciones Generales (Forma de pago no aplicantes)</h4>
                            </div>
                            <div class="panel-body pt0 pb0">

                                <div class="panel panel-default toggle panelMove panelClose panelRefresh">

                                    <!-- TABLA PRODUCTOS -->
                                    <table id="tblFormasPago" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="per5">Código</th>
                                                <th class="per30">Descripción</th>
                                                <th class="per10">Forma de Pago (tipo)</th>
                                                <th class="per10 text-center">Aplica Restricción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <!-- END TABLA PRODUCTOS -->

                                </div>


                            </div>
                        </div>


                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Panel Modal Productos-->
<div id="mdlFrmProductos" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="mdlTitleModalProduct"></h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal">

                    <!-- APLICA PLAN -->
                    <div class="col-lg-12 col-md-12">
                        <div class="toggle-custom control-label">
                            <label for="checkbox-toggle" class="control-label"><b>Aplica Plan</b>&nbsp;</label>
                            <label class="toggle" data-on="ON" data-off="OFF">
                                <input inputmode="none"  type="checkbox" id="inProductApply" name="checkbox-toggle" checked>
                                <span class="button-checkbox"></span>
                            </label>
                        </div>
                    </div>
                    <!-- PRODUCTO -->
                    <div class="form-group">
                        <label class="col-lg-12 col-md-12" for=""><b>Producto</b></label>
                        <div class="col-lg-12 col-md-12">
                            <input inputmode="none"  id="inProductName" type="text" class="form-control input-rounded" disabled="disabled"/>
                        </div>
                    </div>
                    <!-- PUNTOS Y ORDEN -->
                    <div class="form-group">
                        <label class="col-lg-6 col-md-6" for=""><b>Puntos</b></label>
                        <label class="col-lg-6 col-md-6" for=""><b>Orden</b></label>
                        <div class="col-lg-4 col-md-4">
                            <input inputmode="none"  id="inProductPoints" type="text" class="form-control input-rounded" />
                        </div>
                        <div class="col-lg-2 col-md-2"></div>
                        <div class="col-lg-4 col-md-4">
                            <input inputmode="none"  id="inProductOrder" type="text" class="form-control input-rounded" disabled="disabled"/>
                        </div>
                    </div>
                    <!-- DESCRIPCION -->
                    <div class="form-group">
                        <label class="col-lg-12 col-md-12" for=""><b>Descripcion</b></label>
                        <div class="col-lg-12 col-md-12">
                            <textarea id="inProductDescript" class="form-control limitTextarea" maxlength="250" rows="6" style="resize: none" ></textarea>
                        </div>
                    </div>

                </form>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button id="btnProductSave" type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Panel Modal Restaurante -->
<div id="mdlFrmRestaurante" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="mdlTitleModalRestaurant"></h4>
            </div>
            <div class="modal-body">


                <form class="form-horizontal">
                    <!-- APLICA PLAN -->
                    <div class="col-md-6">
                        <div class="toggle-custom control-label" style="text-align: center">
                            <label for="checkbox-toggle" class="control-label"><b>Aplica Plan</b>&nbsp;</label>
                            <label class="toggle" data-on="ON" data-off="OFF">
                                <input inputmode="none"  type="checkbox" id="inRestaurantApply" name="checkbox-toggle" checked>
                                <span class="button-checkbox"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="toggle-custom control-label" title="Ésta opción determina si se mostraran los puntos acumulados en el Ride de la factura impresa. "  style="text-align: center">
                            <label for="checkbox-toggle" class="control-label"><b>Imprimir Puntos en Ride</b>&nbsp;</label>
                            <label class="toggle" data-on="ON"  data-off="OFF">
                                <input inputmode="none"  type="checkbox" id="inPuntosRide" name="checkbox-toggle" checked>
                                <span class="button-checkbox"></span>
                            </label>
                        </div>
                    </div>
                    <!-- LATITUD -->
                    <div class="form-group">
                        <label class="col-lg-12 col-md-12" for=""><b>Latitud</b></label>
                        <div class="col-lg-6 col-md-6">
                            <input inputmode="none"  id="inRestaurantLatitude" type="text" class="form-control input-rounded">
                        </div>
                    </div>
                    <!-- LONGITUD -->
                    <div class="form-group">
                        <label class="col-lg-12 col-md-12" for=""><b>Longitud</b></label>
                        <div class="col-lg-6 col-md-6">
                            <input inputmode="none"  id="inRestaurantLongitude" type="text" class="form-control input-rounded">
                        </div>
                    </div>

                </form>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button id="btnGuardarRestaurante" type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!--Panel Formas de pago-->

<div id="mdlFrmFormasPago" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 65%;margin-left: 30%;margin-top: 40%;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="mdlTitleModalFormasPago"></h4>
            </div>
            <div class="modal-body">


                <form class="form-horizontal">
                    <!-- APLICA PLAN -->
                    <div class="col-md-12">
                        <div class="toggle-custom control-label" style="text-align: center">
                            <label for="checkbox-toggle" class="control-label"><b>Aplica Restricción</b>&nbsp;</label>
                            <label class="toggle" data-on="ON" data-off="OFF">
                                <input inputmode="none"  type="checkbox" id="inFormasPagoApply" name="checkbox-toggle" checked>
                                <span class="button-checkbox"></span>
                            </label>
                        </div>
                    </div>
               
                 

                </form>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button id="btnGuardarFormaPago" type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!--Fin panel formas de pago-->

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
<script type="text/javascript" src="../../js/ajax_fidelizacion.js"></script>
<script type="text/javascript">
                                                cargando(0);
</script>