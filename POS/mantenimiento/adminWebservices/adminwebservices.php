<?php
/////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: FRANCISCO SIERRA////////////////////////////////////////////////
///////////DESCRIPCION: Administración de wervidores y rutas de WebServices de MP////////
////////////////TABLAS: ColeccionCadena,CadenaColecciondeDatos,ColeccionDeDatosCadena////
////////FECHA CREACION: 08/04/2018///////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////

session_start();
include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_seguridades.php";
include_once "../../seguridades/seguridad.inc";
require_once "datos_adminwebservices.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Políticas de Web Services</title>
    <!---------------------------------------------------
                           ESTILOS
    ----------------------------------------------------->
    <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css"/>
    <!---------------------------------------------------
                       JSQUERY
    ----------------------------------------------------->
    <script src="../../js/jquery1.11.1.js"></script>
    <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
    <script type="text/javascript" src="../../js/alertify.js"></script>
    <script type="text/javascript" src="../../js/js_validaciones.js"></script>
    <script type="text/javascript" src="../../js/ajax_adminwebservices.js"></script>

</head>
<body>
<div class="superior">
    <div class="menu" style="width: 500px;" align="center">
    </div>
    <div class="tituloPantalla">
        <h1>Rutas de WebServices - <?php print $_SESSION["cadenaNombre"] ?></h1>
    </div>
</div>
</br>
<div class="contenedor">
    <div class="inferior">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-8"><h4>Rutas de WebServices</h4></div>
                    <div class="col-sm-8"><h5>Esta base de datos está configurada para el entorno de: <span
                                    class="bg-success" style="padding:.5em"><?php echo($ambienteBDD); ?></span></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <table class="table table-bordered table-condensed table-hover" id="tabla_webservices">
                <tbody>
                <?php foreach ($servidoresconfigurables as $nombre => $valores) { ?>
                    <tr>
                        <th colspan="3"><?php print $nombre ?></th>
                    </tr>
                    <?php foreach ($ambientes as $ambiente) {
                        $nombreParametro = $nombre . " " . $ambiente;
                        $reg = array_key_exists($nombreParametro, $servidoresconfigurados) ? $servidoresconfigurados[$nombreParametro] : false;
                        ?>
                        <tr class="tr-url-servidor"
                            data-nombreservidor="<?php print($nombre); ?>"
                            data-nombreparametro="<?php print($nombreParametro); ?>"
                            data-idcoleccioncadena="<?php print($reg ? $reg[1] : ""); ?>"
                            data-idcolecciondedatoscadena="<?php print($reg ? $reg[2] : ""); ?>">
                            <td>SERVIDOR <?php print($ambiente) ?></td>
                            <td <?php print($reg ? "class='valorpolitica'" : ""); ?>>
                                <?php print($reg ? $reg[0] : "NO CONFIGURADO"); ?>
                            </td>
                        </tr>
                    <?php }
                    if ("SOA" === $nombre) {
                        foreach ($ambientes as $ambiente) {
                            $nombreParametro = $nombre . " " . $ambiente . " IP";
                            $reg = array_key_exists($nombreParametro, $servidoresconfigurados) ? $servidoresconfigurados[$nombreParametro] : false;
                            ?>
                            <tr class="tr-url-servidor"
                                data-nombreservidor="<?php print($nombre . " IP"); ?>"
                                data-nombreparametro="<?php print($nombreParametro); ?>"
                                data-idcoleccioncadena="<?php print($reg ? $reg[1] : ""); ?>"
                                data-idcolecciondedatoscadena="<?php print($reg ? $reg[2] : ""); ?>">
                                <td>SERVIDOR <?php print($ambiente . " IP") ?></td>
                                <td <?php print($reg ? "class='valorpolitica'" : ""); ?>>
                                    <?php print($reg ? $reg[0] : "NO CONFIGURADO"); ?>
                                </td>
                            </tr>
                        <?php }
                    } ?>
                    <tr data-nombreservidor="<?php print($nombre); ?>" class="tr-rutas">
                        <td>Rutas:</td>
                        <td>
                            <table class="table table-bordered table-condensed table-hover">
                                <thead>
                                <tr>
                                    <th>NOMBRE</th>
                                    <th>ENDPOINT</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $rutasconfigurables = $valores["rutas"];
                                foreach ($rutasconfigurables as $nombreruta) {
                                    $nombreCompletoRuta = $nombre . " " . $nombreruta;
                                    $reg = array_key_exists($nombreCompletoRuta, $rutasconfiguradas) ? $rutasconfiguradas[$nombreCompletoRuta] : false;
                                    ?>
                                    <tr class="tr-ruta-servicio"
                                        data-nombreparametro="<?php print($nombreCompletoRuta); ?>"
                                        data-idcoleccioncadena="<?php print($reg ? $reg[1] : ""); ?>"
                                        data-idcolecciondedatoscadena="<?php print($reg ? $reg[2] : ""); ?>">
                                        <td width="300"><?php print $nombreruta ?></td>
                                        <td class="valorpolitica"><?php print($reg ? $reg[0] : "NO CONFIGURADO"); ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <!-- Fin Contenedor Inferior -->
    </div>
    <!-- Fin Contenedor -->
</div>
<!-------------------------------------FIN MODAL MODIFICAR BOOTSTRAP---------------------------------------------->

<div class="modal fade" id="modalUrlServidor" tabindex="-1" role="dialog" aria-hidden="true"
     data-backdrop="static">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header panel-footer">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"></h4>
            </div>
            <form action="../adminwWebservices/config_adminwebservices.php" id="formGuardarServidor">
                <div class="modal-body">
                    <div class="form-group">
                        <div align="right" class="col-xs-12">
                            Está Activo? <input inputmode="none"  type="checkbox" name="checkActivo" checked="checked">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputValorServidor">Dirección del servidor: </label>
                        <input inputmode="none"  type="text" class="form-control" name="inputValorServidor"/>
                    </div>
                </div>
                <div class="modal-footer panel-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <input inputmode="none"  type="reset" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                </div>
                <input inputmode="none"  type="hidden" name="nombreServidor" value=""/>
                <input inputmode="none"  type="hidden" name="idColeccionServidor" value=""/>
                <input inputmode="none"  type="hidden" name="idParametroServidor" value=""/>
                <input inputmode="none"  type="hidden" name="accion" value="guardarServidor"/>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalRutaServicio" tabindex="-1" role="dialog" aria-hidden="true"
     data-backdrop="static">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header panel-footer">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"></h4>
            </div>
            <form action="../adminWebservices/config_adminwebservices.php" id="formGuardarRuta">
                <div class="modal-body">
                    <div class="errores">
                    </div>
                    <div class="form-group">
                        <div align="right" class="col-xs-12">
                            Está Activo? <input inputmode="none"  type="checkbox" name="checkActivo" checked="checked">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputValorRuta">Ruta absoluta del servicio: </label>
                        <input inputmode="none"  type="text" class="form-control" name="inputValorRuta"/>
                    </div>
                    <div id="rutasFinales"></div>
                </div>
                <div class="modal-footer panel-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <input inputmode="none"  type="reset" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                </div>
                <input inputmode="none"  type="hidden" name="nombreRuta" value=""/>
                <input inputmode="none"  type="hidden" name="idColeccionRuta" value=""/>
                <input inputmode="none"  type="hidden" name="idParametroRuta" value=""/>
                <input inputmode="none"  type="hidden" name="accion" value="guardarRuta"/>
            </form>
        </div>
    </div>
</div>
<!-------------------------------------FIN MODAL MODIFICAR BOOTSTRAP---------------------------------------------->
</body>
</html>