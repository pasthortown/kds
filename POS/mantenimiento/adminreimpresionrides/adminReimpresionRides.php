<?php
session_start();

ini_set("memory_limit", "256M");
header("Expires: Fri, 25 Dec 1980 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Configuraci�n de Pantallas ////////////////////////////////
///////FECHA CREACION: 25-01-2016 /////////////////////////////////////////////
///////FECHA DE MODIFICACION: 22-12-2016 //////////////////////////////////////
///////USUARIO QUE MODIFICO: Juan Estevez /////////////////////////////////////
///////DESCRIPCION DEL ULTIMO CAMBIO: Creacion de panel ///////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_seguridades.php';
include_once '../../seguridades/seguridad_niv3.inc';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Administraci&oacute;n</title>

        <!-- ESTILOS -->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />       
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css" />
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
       
    </head>
    <body>       
        <div class="superior">
            <div class="menu" align="center" style="width: 400px;">
                <ul>
                    <li>                       
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>Reimpresion Documentos</h1>
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
                            <div class="col-sm-8"><h5>Documentos</h5>
                                <div id="opciones_estado" class="btn-group" data-toggle="buttons">                                   
                                </div>
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
                        <table id="listaRestaurantes" class="table table-bordered table-hover"></table>
                    </div>
                </div>                   
            </div> <!-- Fin Contenedor Inferior -->                                
        </div>  <!-- Fin Contenedor -->

       

        <!-------------------------------------INICIO MODAL REPORTE FIN DE DIA---------------------------------------------->
        <div class="modal fade" id="ModalReporteFinDeDia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog " style="width: 400px">
                <div class="modal-content">
                    <div class="modal-header panel-footer">                       
                        <h4 class="modal-title" id="titulomodalReporteFinDeDia">REIMPRESI&Oacute;N DE DOCUMENTO</h4>
                    </div>
                    <br>
                    <div id="div_reporteFinDeDia"></div>
                    <div class="modal-footer panel-footer">
                        <button type="button" class="btn btn-primary" onclick="fn_imprimirRide('ModalReporteFinDeDia');">Imprimir</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>                        
                        <!--                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>-->
                    </div>
                </div>
            </div>
        </div>
        <!-------------------------------------FIN MODAL REPORTE FIN DE DIA---------------------------------------------->                      

        <!-- JavaScript -->
        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>
        <script language="javascript1.1" src="../../js/alertify.js"></script>       
        <script type="text/javascript" src="../../js/ajax_adminReimpresionRides.js"></script>

        <script>indice = 1;</script>
    </body>
</html>