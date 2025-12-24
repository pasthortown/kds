<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: PIERRE QUITIAQUEz///////////////////////////////////////////////////////////////
///////////DESCRIPCION: PLUS/PRECIOS  //////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

session_start();
include_once"../../system/conexion/clase_sql.php";
include_once"../../clases/clase_seguridades.php";
include_once"../../seguridades/seguridad.inc";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <title>Descarga de Plu/Precio</title>
    <!---------------------------------------------------
                           ESTILOS
    ----------------------------------------------------->
    <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
    <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
    <!--<link rel="stylesheet" type="text/css" href="../../bootstrap/css/select2.css" />-->
    <!--<link rel="stylesheet" type="text/css" href="../../css/select2.css" />-->
    <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
    <link media="all" href="../../css/progressBar.css" rel="stylesheet" />
    <!---------------------------------------------------
                       JSQUERY
    ----------------------------------------------------->
    <script src="../../js/jquery1.11.1.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/ajax_datatables.js"></script>
    <!--<script src="../../js/ajax_select2.js"></script>-->
    <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
    <script language="javascript1.1"  src="../../js/alertify.js"></script>
    <!-- <script type="text/javascript" src="../../js/ajax_adminDescargaPluprecios.js"></script> -->
    <script type="text/javascript" src="../../js/ajax_adminMantenimientoTienda.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/js_validaciones.js"></script>

    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>
    <script type="text/javascript" src="../../js/ajax_adminMantenimientoTienda.js"></script>



</head>

<body>
<input id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
<input id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
<div class="superior">
    <div class="tituloPantalla">
        <h1>Administraci&oacute;n de Plus/Precios</h1>
    </div>
</div>
</br>

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div>
                            <div id="tab_principal" class="tabs">
                                <ul id="pestanasMod" class="nav nav-tabs">
                                    <li id="liPlu"class="active">
                                        <a href="#tabPlus" data-toggle="tab">
                                            <h5>Igualar Registros Nuevos</h5>
                                        </a>
                                    </li>
                                    <li id="liPrecios" class="nav nav-tabs" >
                                        <a href="#tabPrecios" data-toggle="tab">
                                            <h5>Igualar Botones Menú</h5>
                                        </a>
                                    </li>
                                  </ul>
                                <div id="tabContentMod" class="tab-content">
                                    <div id="tabPlus"  class="tab-pane fade in active">
                                        <br />
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Igualación de registros nuevos</label>
                                                <br>
                                                <input type="button" class="btn btn-primary" value="Igualar Data" onclick="fn_IgualarTablaMenu();"/>
                                            </div>
                                        </div>
                                        <br /><br />
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-3 form-group">

                                                </div>
                                                <div class="col-md-2 form-group">

                                                <input id="btcarga" style="display:none" class="btn btn-primary" type="button" value="Cargar Plu a Azure-Maxpoint" onclick="fn_confirmar();"/>
                                            </div>
                                            </div>

                                            <br />
                                            <div id="divtabla_plus">
                                                <label id="tituloSG" style="display: none"><u>Informaci&oacute;n de Sistema Gerente</u></label>
                                                <table class="table table-bordered table-hover" id="tablaplus" border="1" cellpadding="1" cellspacing="0">
                                                </table>
                                            </div>
                                        </div>
                                        <br />
                                        <hr  style="color: #0056b2;" size="2" width="100%" />
                                        <div id="respuesta_registros">

                                        </div>
                                        <br />
                                    
                                    </div>

                                <div id="tabPrecios" class="tab-pane fade">
                                    <br />
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Nombre del Menú
                                           
                                            <!-- <input id="txnumpluprecios" type="text"/> -->
                                            </label>
                                        </div>
                                    </div>
                                    <br /><br />
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                                <select id="menucadena" class="form-control"></select>
                                                <br>
                                                <input type="button" class="btn btn-primary" value="Igualar Data" onclick="fn_igualarBotones();"/>
                                            </div>
                                            
                                        </div>

                                        <br />
                                        
                                        <br />
                                        <hr style="color: #0056b2;" size="2" width="100%" />
                                        <div id="tablas_involucradas">
                                            <p><b>Tablas Involucradas:</b> dbo.MenuCategorias, MenuCategorias, CategoriasBotones, BotonesTiendas.  </p>                                            
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                    </div>

                </div>
            </div>





        <!-- Fin Contenedor Inferior -->
    </div>
    <div id="cargando" style="display:none" class="modal_cargando">
        <div id="cargando" class="modal_cargando_contenedor">
            <img src="../../imagenes/admin_resources/progressBar.gif"/>
        </div>
    </div>

    <!-- Fin Contenedor -->

</body>
</html>