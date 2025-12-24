<?php
///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Francisco Sierra ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de promociones //////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 15-08-2017 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once("datos_promociones.php");

use Maxpoint\Mantenimiento\promociones\Clases\PromocionesController;

$conexionTienda = $conexionDinamica->conexionTienda();
$promocionesController = new PromocionesController($conexionTienda);

$lc_cadena = $_SESSION["cadenaId"];
$idUsuario = $_SESSION["usuarioId"];

$cargarConfiguraciones = $promocionesController->cargarConfiguraciones($lc_cadena);
$cargarClasificacionesCupones = $promocionesController->buscarCategoriasCupon($lc_cadena);

?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

        <title>Administración</title>

        <!-- ESTILOS -->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/spectrum.css" />

        <link rel="stylesheet" type="text/css" href="../../css/admin_cupones.css"/>
    </head>
    <body>
    <input inputmode="none"  id="sess_usr_id" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
    <input inputmode="none"  id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
    <div class="superior">
        <div class="menu" align="center" style="width: 300px;">
            <ul>
                <li>
                    <button id="agregar" onClick="fn_agregarPromocion()"
                            class="botonMnSpr l-basic-elaboration-document-plus">
                        <span>Nuevo</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="tituloPantalla">
            <h1>ADMINISTRACI&Oacute;N PROMOCIONES</h1>
        </div>
    </div>
    <br>
    <div class="inferior">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#tabListado" data-toggle="tab">Lista de Promociones</a>
            </li>
            <li>
                <a href="#tabConfiguraciones" data-toggle="tab">Configuraciones</a>
            </li>
        </ul>
        <div class="tab-content clearfix">
            <div class="tab-pane active" id="tabListado">
                <table id="listaPromociones" class="table table-bordered table-hover">
                    <thead>
                    <tr class="active">
                        <th>Nombre</th>
                        <th>Nombre Imprimible</th>
                        <th>Código Externo</th>
                        <th>Codigo Amigable</th>
                        <th>Activo Desde</th>
                        <th>Activo Hasta</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="tab-pane" id="tabConfiguraciones" style="padding:1em;">
                <div class="row">
                    <div class="col-sm 12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Clasificaciones
                            </div>
                            <div class="panel-body" style="padding:1em;">
                                <?php
                                echo(crearHTMLTablaClasificacionesCupon($cargarClasificacionesCupones));

                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php
                    if (0 === $cargarConfiguraciones["estado"]) { ?>
                        <div class="col-sm-12"><h3>Error al consultar las configuraciones. </h3></div>
                    <?php } else {
                        $configuraciones = $cargarConfiguraciones["datos"][0];
                        $tipoAmbiente = $configuraciones["tipoAmbiente"] != "0" ? $configuraciones["tipoAmbiente"] : "";
                        $direccionServidorSOA = $configuraciones["direccionServidorSOA"] != "0" ? $configuraciones["direccionServidorSOA"] : "";
                        $direccionServidorMasterData = $configuraciones["direccionServidorMasterData"] != "0" ? $configuraciones["direccionServidorMasterData"] : "";
                        $direccionServidorTrade = $configuraciones["direccionServidorTrade"] != "0" ? $configuraciones["direccionServidorTrade"] : "";

                        $endpointMasterDataSincronizarCanje = $configuraciones["endpointMasterDataSincronizarCanje"] != "0" ? $configuraciones["endpointMasterDataSincronizarCanje"] : "";
                        $endpointMasterdataAnulacion = $configuraciones["endpointMasterDataAnulacion"] != "0" ? $configuraciones["endpointMasterDataAnulacion"] : "";
                        $endpointMasterDataCanjesCliente = $configuraciones["endpointMasterDataCanjesCliente"] != "0" ? $configuraciones["endpointMasterDataCanjesCliente"] : "";
                        $endpointCanjeTrade = $configuraciones["endpointCanjeTrade"] != "0" ? $configuraciones["endpointCanjeTrade"] : "";
                        $claveJWTTrade = $configuraciones["claveJWTTrade"] != "0" ? $configuraciones["claveJWTTrade"] : ""; ?>

                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Trade
                                </div>
                                <div class="panel-body configuracionesServidor" style="padding:1em;" data-servidor="<?php echo(trim($direccionServidorTrade)); ?>">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <h4>Webservice</h4>
                                            <div class="form-endpoint-trade">
                                            <div class="form-horizontal">
                                                <div class="form-group">
                                                    <label class="col-sm-3" for="rutaEndpointCanjesTotales">Servidor</label>
                                                    <div class="col-sm-7">
                                                        <div>
                                                            <input inputmode="none"  class="form-control direccionservidor" id="servidorCanjesTrade"
                                                                   placeholder="Ruta"
                                                                   value="<?php echo($direccionServidorTrade) ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <button class="btn btn-success" id="btnGuardarUrlServidorTrade">Guardar</button>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <hr/>
                                            <div class="form-endpoint-trade">
                                                <div class="form-horizontal">
                                                    <div class="form-group">
                                                        <label class="col-sm-3" for="rutaEndpointCanjeTrade">Endpoint aviso canje</label>
                                                        <div class="col-sm-7">
                                                            <input inputmode="none"  class="form-control rutaEndpoint" id="rutaEndpointCanjeTrade"
                                                                   placeholder="Ruta" value="<?php print($endpointCanjeTrade) ?>">
                                                            <div class="class-sm-12 rutacompletaws"><?php print("http://" . $direccionServidorTrade . $endpointCanjeTrade) ?></div>
                                                        </div>
                                                        <div class="col-sm-2"><button class="btn btn-success" id="btnGuardarRutaEndpointCanjeTrade">
                                                                Guardar
                                                            </button></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <h4>Encripción</h4>
                                            <div class="form-horizontal">
                                                <div class="form-group">
                                                    <label class="col-sm-3" for="claveJWTTrade">Clave desencripción
                                                        JWT</label>
                                                    <div class="col-sm-9">
                                                        <input inputmode="none"  type="password" class="form-control"
                                                               id="inputClaveJWTTrade" placeholder="Clave"
                                                               value="<?php print($claveJWTTrade) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <button class="btn btn-success" id="btnGuardarClaveJWT">Guardar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Azure
                                </div>
                                <div class="panel-body configuracionesServidor"  data-servidor="<?php echo(trim($direccionServidorMasterData)); ?>">
                                    <div class="row" style="padding:1em;">
                                        <div class="col-sm-12" >
                                            <h4>Webservice</h4>
                                            <div class="col-sm-3">Servidor</div>
                                            <div class="col-sm-9" id="urlServidorMasterData"></div>
                                            <div class="form-horizontal">
                                                <div class="form-group">
                                                    <div class="class-sm-9 divservidor"><?php echo($direccionServidorMasterData) ?></div>
                                                </div>
                                            </div>

                                            <div class="form-endpoint-trade">
                                                <div class="form-horizontal">
                                                    <div class="form-group">
                                                        <label class="col-sm-3" for="rutaEndpointCanjesTotales">Endpoint canjes cliente</label>
                                                        <div class="col-sm-7">
                                                            <div>
                                                                <input inputmode="none"  class="form-control rutaEndpoint" id="rutaEndpointCanjesTotales"
                                                                       placeholder="Ruta"
                                                                       value="<?php echo($endpointMasterDataCanjesCliente) ?>" >
                                                            </div>
                                                            <div>
                                                                <div class="class-sm-12 rutacompletaws" style="word-break:break-all" id="rutaCompletaAvisoCanjeTrade"><?php echo("http://".$direccionServidorMasterData.$endpointMasterDataCanjesCliente) ?></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <button class="btn btn-success" id="btnGuardarWsCanjesTotales">Guardar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-endpoint-trade">
                                                <div class="form-horizontal">
                                                    <div class="form-group">
                                                        <label class="col-sm-3" for="rutaEndpointSincronizarCanjes">Endpoint sincronizar canjes</label>
                                                        <div class="col-sm-7">
                                                            <div>
                                                                <input inputmode="none"  class="form-control rutaEndpoint"
                                                                       id="rutaEndpointSincronizarCanjes"
                                                                       placeholder="Ruta"
                                                                       value="<?php echo($endpointMasterDataSincronizarCanje) ?>" >
                                                            </div>
                                                            <div>
                                                                <div class="class-sm-12 rutacompletaws" style="word-break:break-all" id="rutaCompletaAvisoCanjeTrade"><?php echo("http://".$direccionServidorMasterData.$endpointMasterDataSincronizarCanje) ?></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <button class="btn btn-success" id="btnGuardarWsCanjesTotales">Guardar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-endpoint-trade">
                                                <div class="form-horizontal">
                                                    <div class="form-group">
                                                        <label class="col-sm-3" for="rutaEndpointAnularCanjes">Endpoint anular canje</label>
                                                        <div class="col-sm-7">
                                                            <div>
                                                                <input inputmode="none"  class="form-control rutaEndpoint" id="rutaEndpointAnularCanjes"
                                                                       placeholder="Ruta"
                                                                       value="<?php echo($endpointMasterdataAnulacion) ?>" >
                                                            </div>
                                                            <div>
                                                                <div class="class-sm-12 rutacompletaws"  style="word-break:break-all" id="rutaCompletaAvisoCanjeTrade"><?php echo("http://".$direccionServidorMasterData.$endpointMasterdataAnulacion) ?></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <button class="btn btn-success" id="btnGuardarWsCanjesTotales">Guardar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalDetalleCupon" tabindex="-1" role="dialog" data-backdrop="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Detalles cupón</h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Guardar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="modalTipoCupon" tabindex="-1" role="dialog" data-backdrop="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Tipo Cupón</h4>
                </div>
                <div class="modal-body">
                    <input inputmode="none"  type="hidden" id="idParametroTipoCupon" >
                    <input inputmode="none"  type="hidden" id="nombreTipoCupon" >
                    <div class="form-group">
                        <label for="etiquetaTipoCupon">Etiqueta</label>
                        <input inputmode="none"  type="email" class="form-control" id="etiquetaTipoCupon" placeholder="Etiqueta">
                    </div>
                    <div class="form-group">
                        <div><label for="colorTipoCupon">Color</label></div>
                        <div><input inputmode="none"  type="text" id="colorTipoCupon"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarTipoCupon">Guardar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div id="mdl_rdn_pdd_crgnd" class="modal_cargando" style="display:none">
        <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
            <img src="../../imagenes/admin_resources/progressBar.gif"/>
        </div>
    </div>
    <!-- JavaScript -->
    <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/query.bootstrap-growl.min.js"></script>

    <script type="text/javascript" src="../../js/ajax_datatables.js"></script>

    <script type="text/javascript" src="../../js/jquery.uitablefilter.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>

    <script type="text/javascript" src="../../js/chosen.jquery.js"></script>
    <script type="text/javascript" src="../../js/spectrum.js"></script>
    <script type="text/javascript" src="../../js/ajax_admin_promociones.js"></script>

    </body>
    </html>

<?php
function crearHTMLTablaClasificacionesCupon($cargarClasificacionesCupones)
{
    if ($cargarClasificacionesCupones["estado"] == 0) {
        return "ERROR al cargar el listado de Tipos de cupones.";
    }
    $clasificacionesCupones = $cargarClasificacionesCupones["datos"];
    if (count($clasificacionesCupones) < 1) {
        return "No se ha creado ningún tipo de cupón";
    }
    $html = "<table class='table table-bordered' id='tblTiposCupon'>
                <thead>
                    <tr>
                        <th>Nombre configuración</th>
                        <th>Etiqueta</th>
                        <th>Color</th>
                    </tr>
                </thead>
                <tbody>";
    foreach ($clasificacionesCupones as $clasificacion) {
        $html .= crearHTMLClasificacionCupon($clasificacion);
    }
    $html .= "</tbody>
             </table>";
    return $html;
}

function crearHTMLClasificacionCupon($clasificacion)
{
    $html = "<tr class='trTipoCupon' data-idparametro='" . $clasificacion["ID_ColeccionDeDatosCadena"] . "'>
             <td><div class='nombreTipo'>" . $clasificacion["Descripcion"] . "</div></td>   
             <td><div class='etiquetaTipo'>" . $clasificacion["variableV"] . "</div></td>
             <td><div class='colorTipo' style='background-color:#" . dechex($clasificacion["variableI"]) . "' data-hexcolor='#" . dechex($clasificacion["variableI"]) . "'>&nbsp;</div></td>
            </tr>";
    return $html;
}