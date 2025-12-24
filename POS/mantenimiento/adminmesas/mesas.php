<?php
session_start();
include_once("../../seguridades/seguridad.inc");

////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: ADMINISTRACION DE MESAS ////////////////////////////////////////////////////////
////////FECHA CREACION: 27/07/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////


if (!isset($_SESSION['validado'])) {
    include_once('../../seguridades/Adm_seguridad.inc');
} else {
    include_once('../../system/conexion/clase_sql.php');
    include_once('../../clases/clase_seguridades.php');
    include_once('../../clases/clase_mesa.php');
    //$cantMesas = $_SESSION['numMesa'];
    $mesa = new mesa();
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 


            <!---------------------------------------------------
            ESTILOS
    --------------------------------------------------- -->

            <style>
                #plano {height:625px;padding:0 px;border:1px solid #aaaaaa;}

                /*#makeMeDraggable { width: 300px; height: 300px; background: red; }*/
            </style>

            <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
            <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
            <link rel="stylesheet" type="text/css" href="../../css/jquery-ui.css" />
            <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
            <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>   
            <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
            <link rel="stylesheet" type="text/css" href="../../css/ImageSelect.css">
                <!--------------------------------LLAMADA AL MENU CONTEXTO---------------------------------- -->

                <link rel="stylesheet" type="text/css" href="../../css/jquery.contextMenu.css" />

                <script language="javascript1.1" type="text/javascript" src="../../js/jquery1.11.1.js"></script>
                <!--<script language="javascript1.1" type="text/javascript" src="../../js/jquery-ui-drag.js"></script>-->
                <script language="javascript1.1" type="text/javascript" src="../../js/jquery-ui.js"></script>
                <!--<script src="../../js/jquery1.11.1.js"></script>-->
                <script src="../../bootstrap/js/bootstrap.js"></script>
                <script language="javascript1.1" type="text/javascript" src="../../js/ajax_datatables.js"></script>
                <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
                <script type="text/javascript" src="../../js/ajax.js"></script>
                <script type="text/javascript" src="../../js/ajax_mesas.js"></script>
                <script language="javascript1.1"  src="../../js/alertify.js"></script>  
                <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.js"></script>
                <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
                <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.js"></script>
                <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>
                <!--------------------------------LLAMADA AL MENU CONTEXTO--------------------------------------- -->
                <script type="text/javascript" src="../../js/jquery.contextMenu.js" ></script>
                <script type="text/javascript" src="../../js/ajax_menu_contexto.js"></script>
                <script type="text/javascript" src="../../js/ImageSelect.jquery.js"></script>

      <!-- <script>
     $(function() {
       $( "#mesa" ).draggable();
     }); 
      ////
    </script>-->
                <title>Administraci&oacute;n - Mesas</title>
        </head>
        <body>

            <input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
            <input inputmode="none"  id="cadenas" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>   
            <input inputmode="none"  id="restaurante" type="hidden" value="<?php echo $_SESSION['rstId']; ?>"/>

            <div class="superior">
                <div class="menu" style="width: 386px;" align="center">
                    <ul>
                        <li><input inputmode="none"  style="margin-top: 7px;" id="btn_agregar" type="button" onclick="fn_agregar()" class="botonhabilitado" value="Agregar"/></li>
                        <!-- <li><input inputmode="none"  id="btn_cancelar" type="button" onclick="fn_borrar()" class="botonhabilitado" value="Cancelar"/></li>-->  
                        <!-- <li><input inputmode="none"  id="btn_agregar" type="button" onclick="fn_agregarcate()" class="botonhabilitado" value="Agregar"/></li>  -->   
                    </ul>        
                </div> 
                <div class="tituloPantalla">
                    <h1>MESAS</h1>
                </div>   
            </div>

            <div class="contenedor">
                <div class="inferior">

                    <br />
                    <br />

                    <div class="panel panel-default center-block">
                        <div class="panel-body">
                            <!--FORMULARIO-->
                            <div class="row">
                                <div class="col-xs-4 text-right"><h5>Restaurante:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group input-group-sm">
                                        <select name="txtRestaurante" id="txtRestaurante" class="form-control"></select>
                                    </div>
                                </div>
                            </div>

                            <!--<div class="row">
                            <div class="col-xs-4 text-right"><h5>Piso:</h5></div>
                            <div class="col-xs-4">
                            <div class="form-group input-group-sm">
                            <select name="txtPiso" id="txtPiso" class="form-control"></select>
                            </div>
                            </div>
                            </div>-->

                            <div class="row">
                                <div class="col-xs-4 text-right"><h5>Piso:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group input-group-sm">
                                        <div id='selec_piso' class="btn-group" data-toggle="buttons">
                                            <!--<label class="btn btn-default btn-sm active">-->
                                            <!--<input inputmode="none"  type="radio" name="txtPiso" id="txtPiso" autocomplete="off" checked >-->
                                            <!--</label>-->
                                        </div>
                                    </div>
                                </div>
                            </div>  

                            <!--<div class="row">
                            <div class="col-xs-4 text-right"><h5>&Aacute;rea:</h5></div>
                            <div class="col-xs-4">
                            <div class="form-group input-group-sm">
                            <select name="txtArea" id="txtArea" class="form-control"></select>
                            </div>
                            </div>
                            </div> -->

                            <div class="row">
                                <div class="col-xs-4 text-right"><h5>&Aacute;rea:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group input-group-sm">
                                        <div id='selec_area' class="btn-group" data-toggle="buttons">
                                            <!--<label class="btn btn-default btn-sm active">
                                              <input inputmode="none"  type="radio" name="txtPiso" id="txtPiso" autocomplete="off" checked ></label>-->
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            <!--FIN FORMULARIO--> 
                        </div>
                    </div>

                    <!--INICIO TABS-->
                    <div id="tabcontenedor" class="container">
                        <!-- Nav tabs --> 
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tabubicacion"><h5>Ubicaci&oacute;n</h5></a></li>  
                            <li><a data-toggle="tab" href="#tabmesa"><h5>Mesas</h5></a></li>     
                        </ul>
                        <div class="tab-content">
                            <!--INICIO PRIMER TAB-->
                            <div id="tabmesa" class="tab-pane fade">      
                                <p>
                                    <!--INICIO PANEL-->
                                    <div class="panel panel-default" id="botonesTodos">
                                        <div class="panel-heading">


                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-sm-8"><h5>Lista de Mesas</h5>
                                                        <div id="opciones_estados" class="btn-group" data-toggle="buttons">
                                                            <label id="std_activo" class="btn btn-default btn-sm active" onclick="fn_OpcionSeleccionada('Activo');"><input inputmode="none"  id="opt_Activos" type="radio" value="Activos" checked="checked" autocomplete="off" name="estados">Activos</label>
                                                            <label class="btn btn-default btn-sm" onclick="fn_OpcionSeleccionada('Inactivo');"><input inputmode="none"  id="opt_Inactivos" type="radio" value="Inactivos" autocomplete="off" name="estados">Inactivos</label>
                                                            <label class="btn btn-default btn-sm" onclick="fn_OpcionSeleccionada('Todos');"><input inputmode="none"  id="opt_Todos" type="radio" value="Todos" autocomplete="off" name="estados">Todos</label>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>                         

                                        </div>
                                        <div class="panel-body">   

                                            <!--AQUI SE CARGA EL DETALLE DE LAS MESAS-->
                                            <div id="aplicacion">    
                                                <table id="mesas" class="table table-bordered table-hover" border="1" cellpadding="1" cellspacing="0"></table>                          
                                            </div>
                                            <!--FIN DETALLE-->     
                                        </div>

                                        <!--PIE DE PAGINA-->
                                        <div class="panel-footer text-center">
                                            <nav><ul id="paginador" class="pagination"></ul></nav>
                                        </div>
                                        <!--FIN PIE DE PAGINA-->
                                    </div>
                                    <!--FIN PANEL-->
                                </p>
                            </div>
                            <!--FIN PRIMER TAB-->

                            <!--INICIO SEGUNDO TAB-->
                            <div id="tabubicacion" class="tab-pane fade in active">      
                                <p>
                                    <div class="row center-block">
                                        <div class="col-xs-2">
                                            <!--<div id="mesamueva" style="width: 280px; height: 625px; border:#33F; border-style: dashed; border-width: 1px;">Mesas:-->
                                            <div id="mesanueva">
                                                <div class="row">
                                                    <h5>Mesa:</h5>
                                                    <div class="col-xs-6">
                                                        <label class="titulo_campo">Valor X</label>
                                                        <input inputmode="none"  class="form-control"  type="text" id="posx" name="posx" size="4" readonly="readonly"/>   
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <label class="titulo_campo">Valor Y</label>
                                                        <input inputmode="none"  class="form-control"  type="text" id="posy" name="posy" size="4" readonly="readonly" /> 
                                                    </div>
                                                </div>
                                                <div class="row">  
                                                    <h5>Estado: </h5>
                                                    <div class="col-xs-12" id="mesas_total">
                                                    </div>
                                                    <div class="col-xs-12" id="mesas_parciales">
                                                    </div>
                                                </div>  
                                            </div> 
                                        </div>
                                        <div class="col-xs-9">
                                            <div class="form-group">
                                                <!--<div id="plano" style="width: 740px; height: 625px; border:#33F; border-style: dashed; border-width: 1px;">Plano</div>-->
                                                <div id="plano">

                                                </div>

                                                <!-- <div id="mesas" style="float: left; height: 200px; width: 100% "></div>-->
                                                <div id="imagen" > </div> 
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div style="width: 740px; height: 625px; border:#33F; border-style: dashed; border-width: 1px;">contenido</div> -->
                                </p>
                            </div>
                            <!--FIN SEGUNDO TAB-->  
                        </div>
                    </div>
                    <!--FIN TABS-->


                </div>
            </div>

            <!-- INICIO MODAL PARA AGREGAR MESAS -->
            <div class="modal fade" id="Modal_agregarmesa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Agregar Mesa</h4>
                        </div>
                        <div class="modal-body" style="overflow-y:visible;">
                            <div class="row">
                                <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" checked="checked" disabled id="check_activonuevo"/></h6></div> 
                            </div>



<!--                            <div class="row">
                                <div class="col-xs-3 text-right"><h5> Tipos:</h5></div>

                                    <div class="row">
                                  
                                        <div class="col-xs-4">
                                            <div class="form-group input-group-sm">
                                                <select name="select_tipo_mesa" id="select_tipo_mesa" class="form-control"></select>
                                            </div>
                                        </div>
                                    </div>     
                                -->
                                
                                
<!--                                <div class="col-xs-6">

                                    <div id="opciones_estados" class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-default btn-sm active" id="lblCuadradas" onclick="fn_OpcionSeleccionada('Cuadradas');"><input inputmode="none"  id="opt_Activos" type="radio" value="Activos" checked="checked" autocomplete="off" name="estados" />Cuadradas</label>
                                        <label class="btn btn-default btn-sm" id="lblRedondas"  onclick="fn_OpcionSeleccionada('Redondas');"><input inputmode="none"  id="opt_Inactivos"   type="radio"      value="Inactivos"  autocomplete="off" name="estados" />Redondas</label>
                                        <label class="btn btn-default btn-sm" id="lblRectangulares"  onclick="fn_OpcionSeleccionada('Rectangulares');"><input inputmode="none"  id="opt_Todos" type="radio"     value="Todos"     autocomplete="off" name="estados" />Rectangulares</label>
                                    </div>
                                </div>-->
<!--                            </div> -->



                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Descripci&oacute;n:</h5></div>
                                <div class="col-xs-5">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control" name="nombreMesaNew" id="nombreMesaNew"/>

                                    </div>
                                </div>
                                <div class="col-xs-2" style="background-color: #FFF;height: 30px;"></div>
                            </div>
                            
<!--                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>N° Personas:</h5></div>
                                <div class="col-xs-2">
                                    <div class="side-by-side clearfix">
                                          <input inputmode="none"  type="text" class="form-control" name="nombreMesaNew" id="numeroPersonas"/>
                                    </div>
                                </div>
                            </div>  
                            -->
                            
                            
                            
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Mesas:</h5></div>
                                <div class="col-xs-5">
                                    <div class="side-by-side clearfix">
                                        <select name="selec_tipo_nuevo" id="selec_tipo_nuevo" class="my-select"></select> 
                                    </div>
                                </div>
                            </div>              
                            <!--</div>-->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="fn_guardarNuevo()">Aceptar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div> 
            <!--FIN MODAL AGREGAR MESAS-->  

            <!-- INICIO MODAL PARA MODIFICAR MESAS -->
            <div class="modal fade" id="Modal_modificarrmesa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Modificar <label style="font-size:13px; color:#666; font-style:normal"  id="nombreMesa2"></label></h4>
                        </div>
                        <div class="modal-body" style="overflow-y:visible;">
                            <div class="row">
                                <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" id="check_activo"/></h6></div> 
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Mesa:</h5></div>
                                <div class="col-xs-7">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control" name="nombreMesa" id="nombreMesa"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Tipo:</h5></div>
                                <div class="col-xs-7">
                                    <div class="form-group input-group-sm">
                                        <select name="selec_tipo" id="selec_tipo" class="form-control"></select>
                                    </div>
                                </div>
                            </div>           
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="fn_guardarModificar(2)">Aceptar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div> 
            <!--FIN MODAL MODIFICAR MESAS-->

            <input inputmode="none"  type="hidden" id="txt_codpiso_hidden"/>
            <input inputmode="none"  type="hidden" id="txt_codmesa_hidden"/>
            <input inputmode="none"  type="hidden" id="txt_codarea_hidden"/>
            <input inputmode="none"  type="hidden" id="codigomesa" />
        </body>
    </html>
<?php } ?>