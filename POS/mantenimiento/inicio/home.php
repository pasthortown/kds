<?php
///////////////////////////////////////////////////////////////////////
////////MODIFICADO POR: Jorge Tinoco //////////////////////////////////
////////FECHA DE MODIFICACION: 22-01-2016 /////////////////////////////
///////////////////////////////////////////////////////////////////////

session_start();
include_once"../../seguridades/Adm_seguridad.inc";
require_once'../../system/conexion/clase_sql.php';
include_once'../../clases/clase_seguridades.php';
include_once"../../system/conexion/clase_sqlMultiple.php"; 

$lc_perfil = $_SESSION['perfil'];
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];
$lc_nombre_usuario = $_SESSION['nombre'];
$lc_cadena_logo = $_SESSION['CadenaLogo'];

$pantalla = new seguridades();
?>
<!doctype html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <title>Max Point</title>
        <!-- Mobile specific metas -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1 user-scalable=no">

        <!-- Css files -->
        <!-- Iconos Menu -->
        <link href="../../bootstrap/templete/css/icons.css" rel="stylesheet" />
        <!-- Menu Superior Derecha -->
        <link href="../../bootstrap/templete/css/bootstrap.css" rel="stylesheet" />
        <!-- CSS Menu Izquierda -->
        <link href="../../bootstrap/templete/css/main.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
        <link href="../../bootstrap/templete/css/plugins.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <!-- .page-navbar -->
        <div id="header" class="page-navbar header-fixed"><!--header dark-bg-->
            <!-- .navbar-brand -->
            <a href="home.php" class="navbar-brand hidden-xs hidden-sm">
                <img src="../../bootstrap/templete/img/logo.png" class="logo hidden-xs" alt="Max Point"/>
                <img src="../../bootstrap/templete/img/logosm.png" class="logo-sm hidden-lg hidden-md" alt="MP"/>
            </a>
            <!-- / navbar-brand -->
            <!-- .no-collapse -->
            <div id="navbar-no-collapse" class="navbar-no-collapse">
                <!-- top left nav -->
                <ul class="nav navbar-nav">
                    <li class="toggle-sidebar">
                        <a href="#" style="background-color:#FFF">
                            <i class="fa fa-reorder"></i>
                            <span class="sr-only">Collapse sidebar</span>
                        </a>
                    </li>
                </ul>

                <ul class="nav navbar-nav navbar-right" style="margin-top:2px">
                    <li class="dropdown">                       	
                        <a data-toggle="dropdown" href="#">
                            <span class="profile-ava">
                                <img style="top:auto" src="../../imagenes/Logos/<?php echo $lc_cadena_logo; ?>" class="img-circle" width="35" height="35"/>
                            </span>&nbsp;
                            <?php echo strtoupper($lc_nombre_usuario); ?>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu right dropdown-notification" role="menu" id="menu_superior"></ul>
                    </li>
                    <li>
                        <a id="toggle-right-sidebar" href="#" class="tipB hide-right-sidebar" title="Ver Opciones">
                            <i class="l-software-layout-sidebar-right"></i>
                            <span class="sr-only">Opciones</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- / page-navbar -->

        <!-- #wrapper -->
        <div id="wrapper">
            <!-- .page-sidebar -->
            <aside id="sidebar" class="page-sidebar sidebar-fixed">
                <!-- Start .sidebar-inner -->
                <div class="sidebar-inner">
                    <!-- Start .sidebar-scrollarea -->
                    <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 100%;">                       
                        <div class="sidebar-panel">
                            <h5 class="sidebar-panel-title"><span id="nombre_restaurante"><?php echo strtoupper(utf8_encode($_SESSION['rstNombre'])); ?></span></h5>
                        </div>

                        <!-- / .sidebar-panel -->
                        <!-- .side-nav -->
                        <div class="side-nav">
                            <ul class="nav" id="id_menu">

                                <?php
                                $orden = 0;
                                $nextorden = 0;
                                $nextdescripcion = '';
                                $descripcion = "";

                                if ($lc_result = $pantalla->fn_menu($lc_perfil)) {
                                    while ($lc_row = $pantalla->fn_leerObjeto()) {
                                        $nextorden = $lc_row->NextOrden;
                                        $nextnivel = $lc_row->NextNivel;
                                        $nextdescripcion = $lc_row->NextDescripcion;

                                        if ($lc_row->pnt_Nivel == 2) {

                                            echo "<li><a href='" . $lc_row->pnt_Nombre_Formulario . "' target='_parent'><i class='l-basic-laptop'></i><span class='txt'>" . $lc_row->pnt_Nombre_Mostrar . "</span></a></li>";
                                        } else if ($lc_row->pnt_Descripcion == 'MODULO') {

                                            $orden = $lc_row->pnt_Orden_Menu;
                                            $nivel = $lc_row->pnt_Nivel;
                                            $descripcion = $lc_row->pnt_Descripcion;

                                            echo "<li class='hasSub'>";
                                            echo "<a href='#' class='notExpand'><i class='l-arrows-right sideNav-arrow'></i><i class='" . $lc_row->pnt_Imagen . "'></i><span class='txt'>" . $lc_row->pnt_Nombre_Mostrar . "</span></a>";
                                            echo '<ul class="sub">';
                                        } else if ($lc_row->pnt_Descripcion == 'PANTALLA' && $lc_row->pnt_Nivel == 3) {

                                            echo "<li id=" . $lc_row->pnt_id . " name=" . $lc_row->pnt_Ruta . ">";
                                            echo "<a href='#' onClick='fn_captura_trafico(\"" . $lc_row->pnt_id . "\",\"" . $lc_usuario . "\");'>";
                                            echo "<span class='txt'>" . $lc_row->pnt_Nombre_Mostrar . "</span>";
                                            echo "</a>";
                                            echo "</li>";
                                        } else if ($lc_row->pnt_Descripcion == 'SUBMODULO' && $lc_row->NextDescripcion == 'SUBPANTALLA') {
                                            echo "<li class='hasSub'>";
                                            echo "<a href='#' class='notExpand'><i class='l-arrows-right sideNav-arrow'></i><span class='txt'>" . $lc_row->pnt_Nombre_Mostrar . "</span></a>";
                                            echo "<ul class='sub'>";
                                        } else if ($lc_row->pnt_Descripcion == 'SUBPANTALLA' && $lc_row->pnt_Nivel == 4) {
                                            echo "<li id=" . $lc_row->pnt_id . " name=" . $lc_row->pnt_Ruta . " onClick='fn_captura_trafico(\"" . $lc_row->pnt_id . "\",\"" . $lc_usuario . "\");'>";
                                            echo "<a href='#'><span class='txt'>" . $lc_row->pnt_Nombre_Mostrar . "</span></a>";
                                            echo "</li>";

                                            //control para cerrar submodulo
                                            if ($lc_row->NextDescripcion <> 'SUBPANTALLA') {
                                                echo "</ul>";
                                                echo "</li>";
                                            }
                                        }

                                        //control para cerrar modulo
                                        if ($descripcion == 'MODULO' && $nextorden >= ($orden + 1)) {
                                            echo "</ul></li>";
                                        }
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <!-- / side-nav -->
                        <!--  .sidebar-panel -->
                    </div>
                    <!-- End .sidebar-scrollarea -->
                </div>
                <!-- End .sidebar-inner -->
            </aside>
            <!-- Start #right-sidebar -->
            <aside id="right-sidebar" class="right-sidebar hide-sidebar">
                <!-- Start .sidebar-inner -->
                <div class="sidebar-inner">
                    <!-- Start .sidebar-scrollarea -->
                    <div class="sidebar-scrollarea">
                        <div class="tabs">
                            <!-- Start .rs tabs -->
                            <ul id="rstab" class="nav nav-tabs nav-justified">
                                <li class="active">
                                    <a href="#activity" data-toggle="tab">Configuraciones </a>
                                </li>
                            </ul>
                            <div id="rstab-content" class="tab-content">
                                <div class="tab-pane fade active in" id="activity">
                                    <p>
                                        Restaurantes disponibles:
                                    </p>
                                    <p>
                                        <select name="cmb_restaurante" id="cmb_restaurante" class="form-control"></select>                                       
                                    </p>

                                    <table>
                                        <p>
                                            <button type="button" class="btn btn-primary" id="irLocal"
                                                    onclick="RedireccionarLocal();">Ir al Backoffice del Local
                                            </button>
                                        </p>

                                    </table>
                                    <p>
                                        <button type="button" class="btn btn-primary" id='botonEliminarCache' onclick="clearjQueryCache();" >Limpiar Cache</button>                                        
                                    </p>                                        
                                </div>
                            </div>
                        </div>
                        <!-- End .rs tabs -->
                    </div>
                    <!-- End .sidebar-scrollarea -->
                </div>
                <!-- End .sidebar-inner -->
            </aside>
            <!-- End #right-sidebar -->
            <!-- .page-content -->

            <div class="page-content sidebar-page right-sidebar-page clearfix">
                <!-- .page-content-wrapper -->
                <!--<div class="page-content-wrapper">-->
                <div class="page-content-inner">
                    <div id="page-header" class="clearfix">
                        
                        
                        <div class="row" id="incluirPagina">
                            <div class="col-lg-6">
                                <nav class="navbar navbar-fixed-bottom ">
                                    <div id="id_atajo" class="btn pull-right">
                                        <div  id="tabla">

                                        </div>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <!-- </div>-->
                    <!-- / .page-content-inner -->
                </div>
                <!-- / page-content-wrapper -->
            </div>
            <!-- / page-content -->
        </div>
        <!-- / #wrapper -->
        <!-- End #footer  -->
        <!-- Back to top -->
        <div id="back-to-top"><a href="#">Back to Top</a></div>
        <script src="../../js/jquery1.11.1.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>
		
        <script src="../../js/alertify.js" type="text/javascript"></script>
        <script src="../../js/ajax_home_mantenimiento.js" type="text/javascript"></script>

        <!-- Bootstrap plugins -->
        <script src="../../bootstrap/templete/js/bootstrap/bootstrap.js"></script>
        <!-- JS Menu Superior Derecha -->
        <script src="../../bootstrap/templete/js/libs/modernizr.custom.js"></script>
        <!-- JS Menu Izquierdo -->
        <script src="../../bootstrap/templete/js/jRespond.min.js"></script>
        <script src="../../bootstrap/templete/plugins/core/slimscroll/jquery.slimscroll.min.js"></script>
    <!-- <script src="../../bootstrap/templete/plugins/ui/weather/skyicons.js"></script> -->
         <script type="text/javascript" src="../../js/jquery-sortable.js"></script>
        <script src="../../bootstrap/templete/js/jquery.Dynamic.js"></script>
        <script src="../../bootstrap/templete/plugins/core/fastclick/fastclick.js"></script>
        <script src="../../bootstrap/templete/js/main.js"></script>


    </body>
</html>