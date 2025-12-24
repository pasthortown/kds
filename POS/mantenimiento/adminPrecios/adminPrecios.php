<?php
session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jose Fernandez //////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de precios///////////////////////
///////FECHA CREACION: 22-06-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
include_once"../../seguridades/seguridad.inc";
include_once"../../system/conexion/clase_sql.php";
include_once"../../clases/clase_seguridades.php";
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Administración Precios</title>

        <link rel="stylesheet" type="text/css" href="../../css/jquery-ui.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/TableTools.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />    
        <!-- <link rel="stylesheet" type="text/css" href="../../bootstrap/css/timepicker.css" />    -->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />    
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/font-awesome.css" />                    
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/build.css" />                
        <!--<link href="../../prettify.css" rel="stylesheet">-->
    </head>

    <body>
        <input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
        <input inputmode="none"  id="cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>  
        <input inputmode="none"  id="cat_id" type="hidden"/>   
        <input inputmode="none"  id="columnas_categorias" type="hidden"/>   

        <!--<div class="superior" id="div_nuevo">
                <div class="menu"  align="center">
                    <ul>
                        <li><input inputmode="none"  id="btn_agregar" type="button" onClick="fn_Modificar('Todos')" class="botonhabilitado" value="Modificar"/></li>
                    </ul>
                </div>	
    
                <div class="tituloPantalla">
                    <h1>ADMINISTRACI&Oacute;N DE PRECIOS</h1>
                </div>
            </div>-->

        <div class="inferior" id="inicio_categorias" align="center">
        </div>

        <div class="inferior">  
            <div class="container">   
                <div role="tabpanel" id="tab_principal">
                    <ul id="pestanasMod" class="nav nav-tabs" role="tablist">
                        <li role="presentation" id="uno" class="active" onClick="fn_bandera(this.id);">
                            <a href="#listaProgramacionPrecios" aria-controls="listaProgramacionPrecios" role="tab" data-toggle="tab">
                                <h5>Programación de Precios</h5>
                            </a>
                        </li>
                        <li role="presentation" id="dos" onClick="fn_wizard();">
                            <a href="#configPrecios" aria-controls="configPrecios" role="tab" data-toggle="tab">
                                <h5>Configuración Precios</h5>
                            </a>
                        </li>
                    </ul>
                    <div id="TabContentMod" class="tab-content">     
                        <div role="tabpanel" class="tab-pane active" id="listaProgramacionPrecios">
                            <br />   
                            <div class="panel panel-default"> 
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-sm-8" align="left">
                                            <div id='opciones_estado' class="btn-group" data-toggle="buttons">
                                                <button class='btn btn-primary' style='width: 150px' onclick="fn_aplicarPrecios()">Aplicar Precios</button>
                                                <!--<label class="btn btn-default btn active" onClick="fn_muestraDetallePreguntas();">Todos
                                                        <input inputmode="none"  id="check_todos" type="radio" name="uno">
                                                    </label>
                                                    <label class="btn btn-default" onClick="fn_muestraDetallePreguntasPorEstado(0,11,'activo');">Activos
                                                        <input inputmode="none"  type="radio" name="uno" id="check_activos" >
                                                    </label>
                                                    <label class="btn btn-default"  onclick="fn_muestraDetallePreguntasPorEstado(0,11,'inactivo');">
                                                        <input inputmode="none"  type="radio" name="uno" id="check_inactivos">Inactivos	
                                                    </label>-->                       	
                                            </div>
                                        </div>                    
                                    </div>            
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-1"></div>                          
                                        <div class="col-xs-10">
                                            <div class="form-group" id="div_detalle_programaciones">
                                                <!--<table id="tabla_cabecera"  class="table table-bordered table-hover"></table>-->
                                                <div>
                                                    <table id="tabla_detalle_programaciones"  class="table table-bordered table-hover"></table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                </div>
                            </div>
                        </div>     
                        <div role="tabpanel" class="tab-pane" id="configPrecios">
                            <br />
                            <div id="rootwizard">
                                <div class="navbar">                                    	
                                    <div class="navbar-inner">
                                        <div class="container">
                                            <!--<div id="bar" class="progress progress-striped active">
                                                    <div class="bar"></div>
                                                </div>-->
                                            <ul>
                                                <li><a href="#tab1" data-toggle="tab">1.1 Categoría(s)</a></li>
                                                <li><a href="#tab2" data-toggle="tab">1.2 Precios</a></li>
                                                <li><a href="#tab3" data-toggle="tab">1.3 Fecha</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-content">
                                    <div class="tab-pane" id="tab1">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-sm-8" align="left">
                                                        <h5>
                                                            <div id='opciones_estado' class="btn-group" data-toggle="buttons"> 
                                                                <!--<label class="btn btn-default btn active" onClick="fn_muestraDetallePreguntas();">Todos
                                                                        <input inputmode="none"  id="check_todos" type="radio" name="uno">
                                                                    </label>
                                                                    <label class="btn btn-default" onClick="fn_muestraDetallePreguntasPorEstado(0,11,'activo');">Activos
                                                                        <input inputmode="none"  type="radio" name="uno" id="check_activos" >
                                                                    </label>
                                                                    <label class="btn btn-default"  onclick="fn_muestraDetallePreguntasPorEstado(0,11,'inactivo');">
                                                                        <input inputmode="none"  type="radio" name="uno" id="check_inactivos">Inactivos	
                                                                    </label>-->                        	
                                                            </div>
                                                        </h5>
                                                    </div>                    
                                                </div>            
                                            </div>
                                            <div id="div_detalle_categorias" class="panel-body">
                                                <table id="tabla_detalle_categorias"  class="table table-bordered table-hover"></table>
                                            </div>
                                            <div id="panel_footer_agregar" class="panel-footer text-center">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab2">   
                                        <div class="panel panel-default">	
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-sm-8" align="left">
                                                        <h5></h5>
                                                    </div>
                                                </div>
                                            </div>                                     	
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-sm-1" align="left"></div>
                                                    <div class="col-sm-6" align="left">
                                                        <h5>
                                                            <div id='opciones_canal' class="form-group" data-toggle="buttons"> 
                                                                <div class="row">                                                                     	
                                                                    <label id="option1" class="btn btn-primary" onClick="fn_validarAccion('Canal', this.id);">
                                                                        Plus
                                                                    </label>
                                                                    <label id="option2" class="btn btn-default" onClick="fn_validarAccion('Master', this.id);">
                                                                        MasterPlus
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div id="opcionesDeCanal" class="row">
                                                            <!--<input inputmode="none"  id="check_todos" type="checkbox" name="uno"/> Todos
                                                                <input inputmode="none"  type="checkbox" name="uno" id="check_salon" /> Salon
                                                                <input inputmode="none"  type="checkbox" name="uno" id="check_llevar" /> Llevar
                                                                <input inputmode="none"  type="checkbox" name="uno" id="check_domicilio" /> Domicilio
                                                                <input inputmode="none"  type="checkbox" name="uno" id="check_drive"/> Drive
                                                                <input inputmode="none"  type="checkbox" name="uno" id="check_drive" onclick="fn_validarAccion('Master');"/> Master Plu-->
                                                            </div> 
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>   
                                            <div class="row">
                                                <div class="col-xs-1"></div>                          
                                                <div class="col-xs-10">
                                                    <div class="form-group" id="div_detalle_categorias">
                                                    <!--<table id="tabla_cabecera"  class="table table-bordered table-hover"></table>-->
                                                        <div style="overflow:auto;width:1000px; height:350px;">
                                                            <table width="100%" id="tabla_detalle_precios"  class="table table-bordered table-hover"></table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                          
                                        </div>                
                                    </div>
                                    <div class="tab-pane" id="tab3">
                                        <div class="panel panel-default">	
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-sm-8" align="left">
                                                        <h5>                                                    
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-xs-1"></div>
                                                    <div class="col-xs-3"><h5>Fecha de Aplicación:</h5></div>
                                                    <div class="col-xs-3">                                                    	
                                                        <div class="form-group">                                                            
                                                            <div class="input-prepend input-group">
                                                                <span class="add-on input-group-addon">
                                                                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                                </span>
                                                                <input inputmode="none"  class="form-control" type="text" id="FechaInicial" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-1"></div>
                                                </div>                                                    
                                                <div class="row">
                                                    <div class="col-xs-1"></div>
                                                    <div class="col-xs-3">
                                                        <h5>Hora de Aplicación:</h5>
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <div class="form-group">
                                                            <div class="input-prepend input-group">
                                                                <span class="add-on input-group-addon">
                                                                    <i class="glyphicon glyphicon-time"></i>
                                                                </span>
                                                                <input inputmode="none"  id="timepicker1" type="text" class="form-control" />
                                                            </div>
                                                        </div>     
                                                    </div>
                                                    <div class="col-xs-1"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>		
                                    <ul class="pager wizard">
                                        <li class="previous"><a href="javascript:;"><==Anterior</a></li>
                                        <li class="next" id="next"><a href="javascript:;">Siguiente==></a></li>
                                        <li class="next finish" style="display:none;" onClick="fn_grabaCadenaPrecios()"><a href="javascript:;">Finalizar</a></li>
                                    </ul>
                                </div>	
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mdl_traerCategorias" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">                	
                        <h4 class="modal-title" id="myModalLabel">Categoría</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-1"></div>                          
                            <div class="col-xs-10">
                                <div class="form-group">
                                    <select class="form-control" id="selCategorias" style="text-transform:uppercase;"></select>           
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onClick="fn_aceptaModalCategoria();">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    </div>                           
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_reporte" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">                	
                        <h4 class="modal-title" id="myModalLabel">Reporte de Precios Modificados</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-1"></div>                          
                            <div class="col-xs-12">
                                <div class="form-group">                                    	                                        	    
                                    <div class="printable" style="overflow:auto;  height:400px;">                                         
                                        <table border="0px" id="header_reporte">
                                            <tr>
                                                <td align="center" rowspan="4" width="20%"><img src="../../imagenes/Logos/<?php echo $_SESSION['logo']; ?>" /></td>
                                                <td rowspan="4" align="center" width="25%">PROGRAMACI&Oacute;N DE PRECIOS</td>
                                            </tr>
                                            <tr>
                                                <td align="right" width="20%">Usuario: <?php echo $_SESSION['usuario']; ?></td>                                               
                                            </tr>
                                            <tr>                                            	
                                                <td align="right" width="17%">Fecha: <?php echo date("d-m-Y"); ?></td>

                                            </tr>
                                            <tr>                                            	                                                
                                                <td align="right" width="15%">Hora: <?php echo date("H:i:s", time()); ?></td>
                                            </tr>
                                        </table>
                                        </br>     
                                        </br>
                                        <table id="dateAplicacion"></table>
                                        <table border="1px" id="detalle_reporte" class="table table-bordered"></table>
                                    </div>	
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="modal-footer">               
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" onClick="fn_imprimir();">Imprimir</button>
                    </div>                           
                </div>
            </div>
        </div>

        <!-- Modal de preview de precios-->
        <div class="modal fade" id="modal_previewPrecios" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">                	
                        <h4 class="modal-title" id="Labelpreview"></h4>
                    </div>
                    <div class="modal-body">  
                        <div role="tabpanel" id="tab_principal">
                            <ul id="pestanasMod" class="nav nav-tabs" role="tablist">
                                <li role="presentation" id="uno" class="active" onClick="fn_bandera(this.id);">
                                    <a href="#Precios" aria-controls="Precios" role="tab" data-toggle="tab">
                                        <h5>Precios</h5>
                                    </a>
                                </li>
                                <li role="presentation" id="dos" onClick="fn_wizard();">
                                    <a href="#ciudades" aria-controls="ciudades" role="tab" data-toggle="tab">
                                        <h5>Ciudades</h5>
                                    </a>
                                </li>
                            </ul>	<!-- nombre de los tabs -->
                            <div id="TabContentMod" class="tab-content"> 
                                <div role="tabpanel" class="tab-pane active" id="Precios"> <br />
                                    <div class="row">
                                        <div class="col-xs-1"></div>                          
                                        <div class="col-xs-10">
                                            <div class="form-group">
                                                <table id="cabecera_reporteee" class="table active table-bordered"></table>
                                                <div style="overflow:auto; height:400px; margin-top:-20px;">
                                                    <table id="detalle_preview" class="table table-bordered"></table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="ciudades"> <br />
                                    <div class="row">
                                        <div class="col-xs-1"></div>                          
                                        <div class="col-xs-10">
                                            <div class="form-group" id="div_prev" style="overflow:auto; height:350px;">                                                                                                              
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">               
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                        </div>                           
                    </div>
                </div>
            </div>
        </div>

        <!-- Fin de modal de preview de modal de precios-->

        <input inputmode="none"  type="hidden" id="hid_indice" />
        <input inputmode="none"  type="hidden" id="hid_categoriaId" />
        <input inputmode="none"  type="hidden" id="hid_bandera" />
        <input inputmode="none"  type="hidden" id="hid_codigosProgramacion" />
        <input inputmode="none"  type="hidden" id="hid_canalBanda" value="Canal" />
        <input inputmode="none"  type="hidden" id="hid_codReporte" />

        <!--<script src="../../js/jquery-1.11.3.min.js"></script>   -->
        <script src="../../js/jquery-1.11.3.min.js"></script>       
        <script src="../../js/ajax_datatables.js"></script>
        <script src="../../bootstrap/js/bootstrap.js"></script>     
        <script src="../../bootstrap/js/bootstrap-dataTables.js"></script>    
        <script src="../../bootstrap/js/DataTable.TableTools.js"></script>  
        <script type="text/javascript" src="../../js/ajax_imprimir.js"></script>    
        <script type="text/javascript" src="../../js/ajax_admin_precios.js"></script>    
        <script language="javascript1.1"  src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-min.js"></script>     
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-wizard.js"></script>
        <!--<script type="text/javascript" src="../../prettify.js"></script>-->
        <script src="../../bootstrap/js/bootbox.min.js"></script>  
        <script src="../../bootstrap/js/moment.js"></script>  
        <script src="../../bootstrap/js/daterangepicker.js"></script>     
        <script src="../../bootstrap/js/timepicker.js"></script> <!-- librerias .js -->    
        <script src="../../js/ajax_tableFilter.js"></script> <!-- librerias .js -->
        <script type="text/javascript" src="../../js/jquery.dataTables.grouping.js"></script>  
    </body>
</html>
