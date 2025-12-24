<?php
session_start();
include_once("../../system/conexion/clase_sql.php"); 
require_once ("../../clases/clase_admdescuentos.php");
////////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Francisco Sierra /////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de formas  ///////////////////////
///////             de aplicación de descuentos ////////////////////////////////
///////TABLAS INVOLUCRADAS: Descuentos.aplica_decuento /////////////////////////
///////FECHA CREACION: 06-06-2017 //////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: //////////////////////////////////////////////
///////USUARIO QUE MODIFICO: ///////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: ///////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

$descuentosObj  = new descuentos();
$estadosModuloDescuentos=$descuentosObj->fn_cargarEstadosTiposDescuento();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" /> 
       
        <title>Administraci&oacute;n</title>

        <!-- ESTILOS -->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
        <style type="text/css">
            #contenedor_lst_rest_no_activos{
                height: 550px; 
                overflow-y: auto;
            }
        </style>
    </head>
    <body>
        <input inputmode="none"  id="sess_usr_id" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
        <input inputmode="none"  id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>       
        <div class="superior">
            <div class="menu" align="center" style="width: 300px;">
                <ul>
                    <li>
                        <button id="agregar" data-toggle="modal" data-target="#modal" class="botonMnSpr l-basic-elaboration-document-plus">
                            <span>Nuevo</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>ADMINISTRACI&Oacute;N FORMAS APLICACI&Oacute;N DESCUENTO</h1>
            </div>
          </div>
        <div class="contenedor">          
            <div class="inferior">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div  id="mdl_rdn_pdd_crgnd" class="modal_cargando" style="display:none">
                            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                                <img src="../../imagenes/admin_resources/progressBar.gif" />
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-sm-8">
                                <h5>Lista de Formas de Aplicaci&oacute;n de descuentos</h5>
                                <div id="opciones_estado" class="btn-group" data-toggle="buttons">
                                   <label  class="btn btn-default btn-sm active btn-estados" data-estado="0">
                                        <input inputmode="none"  type="radio" value="Todos"  autocomplete="off" name="estado">Todos</label>
                                    <?php foreach($estadosModuloDescuentos as $tipoDescuento){ ?>
                                        <label class="btn btn-default btn-sm btn-estados" data-estado="<?php echo($tipoDescuento["IDStatus"]) ?>">
                                            <input inputmode="none"  type="radio" value="<?php echo($tipoDescuento["std_descripcion"]) ?>" data-estado="<?php echo($tipoDescuento["IDStatus"]) ?>" autocomplete="off" name="estado"><?php echo($tipoDescuento["std_descripcion"]) ?></label>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="panel-body">                      
                            <div class="col-xs-8 col-xs-offset-2">
                                <table id="listaAplicaDescuentos" class="table table-bordered table-hover">
                                <thead>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                </thead>
                                <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fin Contenedor Inferior -->
            </div>
                     
            <!-- Fin Contenedor -->
        </div>

        <!-- Modal Pos Plus -->
        <div class="modal fade " id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 id="titulomodal" class="modal-title"><span>Nuevo</span> Aplica Descuento</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-8 col-xs-offset-2">
                          <form>
                            <div class="checkbox text-right">
                              <label>
                                <input inputmode="none"  type="checkbox" id="inputEstadoAplicaDescuento"> Está activo?
                              </label>
                            </div>
                            <div class="form-group">
                              <label for="descripcionAplicaDescuento">Descripción</label>
                              <input inputmode="none"  class="form-control" id="inputDescripcionAplicaDescuentoo" placeholder="Descripción">
                            </div>
                          </form>
                                </div>
                        </div>
                    </div>
                    <div class="modal-footer" id="pnl_pcn_btn">
                        <div class="row">
                            <div class="col-xs-8 col-xs-offset-2">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btnGuardarAplicaDescuento">Guardar</button
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript -->
        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/query.bootstrap-growl.min.js"></script>
       
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        
        <script type="text/javascript" src="../../js/jquery.uitablefilter.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../js/ajax_admaplicadescuentos.js"></script>

    </body>
</html>