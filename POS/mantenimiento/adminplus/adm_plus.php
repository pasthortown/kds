<?php
    session_start();
///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de formas pago //////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 23-04-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 22/05/2015///////////////////////////////////
///////USUARIO QUE MODIFICO: JOSE FERNANDEZ////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: LECTURA DE CADENA POR VARIABLE DE SESION//////
/////////////////////////////////BUSCADOR ON KEY PRESS Y ENTER/////////////////
////////////////////////////////Aplicacion Bootstrap///////////////////////////
///////FECHA ULTIMA MODIFICACION: 11/06/2015///////////////////////////////////
///////USUARIO QUE MODIFICO: JOSE FERNANDEZ////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: BOTONES TIPO SWITCH, MODIFICACION DE REOPORT///
/////////////////////////////////NUMBER/////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 27/06/2015///////////////////////////////////
///////USUARIO QUE MODIFICO: Daniel Llerena////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Cambiar el tamaño de letra en los divs, en ///
///////la modal separa por tabs config, boton, config. preguntas //////////////
///////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 04/01/2016 //////////////////////////////////
///////USUARIO QUE MODIFICO: Christian Pinto //////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Creacion de lista dinamica de Preguntas //////
/////// Sugeridas para el orden y buscador en Lista ///////////////////////////
///////////////////////////////////////////////////////////////////////////////

    include_once"../../seguridades/seguridad.inc";
    include_once"../../system/conexion/clase_sql.php";
    include_once"../../clases/clase_seguridades.php";
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
        <title>Administraci&oacute;n</title>

        <!-- ESTILOS -->    
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />   
        <!--<link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />   -->
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css" />  
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
        <link rel="stylesheet" type="text/css" href="../../css/jquery-ui.css"/>  
        <link rel="stylesheet" type="text/css" href="../../css/jquery-ui-sortable.css"/>  
     <link rel="stylesheet" type="text/css" href="../../css/productos.css"/>
        <!-- JavaScript -->    
        <script src="../../js/jquery1.11.1.js"></script> 
        <script type="text/javascript" src="../../js/jquery-latest.min.js"></script> 
        <script src="../../js/jquery-sortable.js"></script> 

        <script src="../../js/jquery.uitablefilter.js"></script> 
        <script src="../../js/ajax_datatables.js"></script>    
        <script src="../../bootstrap/js/bootstrap.js"></script>    
        <script src="../../bootstrap/js/bootstrap-dataTables.js"></script>          
        <script language="javascript1.1"  src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../js/ajax.js"></script>    
        <script language="javascript1.1" type="text/javascript" src="../../js/calendario.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/idioma.js"></script>
        <script src="../../bootstrap/js/switch.js"></script>     
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>   
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>   
        <script type="text/javascript" src="../../js/ajax_admplus.js"></script>  

    </head>
    <body>

        <input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
        <input inputmode="none"  id="cadenas" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
        <input inputmode="none"  id="plu_id" type="hidden"/>
        <?php /* ?><input inputmode="none"  id="cadenasatributos" type="hidden" value="<?php echo $_SESSION['cadenaatributosId']; ?>"/><?php */ ?>



        <div class="superior">
            <div class="menu"  align="center" style="width: 466px;">
                <ul>
                    <li>
                        <!--
                        <input inputmode="none"  id="btn_procesar" type="button" onclick="fn_sincronizarProductos()" class="botonhabilitado" value="Procesar"/>
                        -->
                    </li>				
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>PRODUCTOS</h1>
            </div>


        </div>

        <div class="inferior">            
            <!-- Tabla Plus -->
            <div id="contenedor_plus" class="panel panel-default">                
                <div class="panel-heading"><!--inicio cabecera-->
                    <div class="row"><!--inicio imput radio button y buscador-->
                        <div class="col-sm-8"><!--inicio radio button-->
                            <h5>
                                <!-- <div class="form-group input-group-sm">-->
                                <div id='selec_categoria' class="btn-group" data-toggle="buttons">                        
                                </div>
                                <!--</div>-->
                            </h5>
                        </div><!--fin radio button--> 
                        <!--inicio buscador-->              
                        <div class="col-sm-4">
                            <div class="input-group input-group-sm">
                                            <!--<span class="input-group-addon glyphicon glyphicon-search" id="img_buscar"></span>-->
                             <!--<span class="input-group-addon glyphicon glyphicon-remove" id="img_remove" onClick="fn_limpiaBuscador(); fn_cargarPlus(0,11);"></span>-->                             

                                <input inputmode="none"  id="par_numplu" aria-controls="DataTables_Table_0" type="search" class="form-control" placeholder="" aria-describedby="sizing-addon1">
                            </div> 
                        </div> <!--fin buscador-->
                    </div> <!--fin radio button y buscador-->
                </div> <!--fin cabecera-->            
                <div id="producto_plu" class="panel-body">
                    <table id="plus"  class="table table-bordered table-hover"></table>
                </div>

                <!-- <div id="panel_footer_agregar" class="panel-footer text-center">
                             <nav>
                                 <ul id="paginas" class="pagination"></ul>
                             </nav>
                 </div>  --> 
            </div> 
        </div> 


        <div class="modal fade" id="contenedor_configuraciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="width:1000px;">
                <div class="modal-content">
                    <div class="modal-header active panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <!--  <h4 class="modal-title active" id="titulo0">Administraci&oacute;n Plus</h4>--->
                        <h4 class="modal-title active" id="titulo1">Administraci&oacute;n Plus</h4>


                    </div>
                    <div >

                        <div class="contenedor_tabs">  
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#home"><h5>Configuración de Botón</h5></a></li>    
                                <li><a data-toggle="tab" href="#menu2"><h5>Configuración de Preguntas</h5></a></li>
                                <li><a data-toggle="tab" href="#menu1"><h5>Recetas</h5></a></li>       
                            </ul>
                            <div class="tab-content">
                                <div id="home" class="tab-pane fade in active center-block">      
                                    <p><div><br />
                                        <!-- <table class="table table-bordered table-hover">  -->
                                        <div class="row">
                                            <div class="col-md-8"></div>
                                            <div class="col-md-2 text-right">
                                                <div class="btn-group">
                                                    <h5 class="text-right"><b>Está Activo? <input inputmode="none"  type="checkbox"
                                                                                                  id="estadoProducto"></b>
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="col-md-1"></div>
                                        </div>
                                        <div class="row">
                                                <div class="col-xs-3 text-right"><h5>Visualización del Botón:</h5></div>
                                                <div class="col-xs-3">
                                                    <div class="form-group">
                                                        <div id='muestra_boton' class="btn-group">
                                                        </div>
                                                    </div>
                                                </div>

                                            <div class="col-xs-2 text-right"><h5>Nivel de Seguridad:</h5></div>
                                                <div class="col-xs-3">
                                                    <div class="form-group">
                                                        <select name="selec_canal_imp" id="selec_seguridad" class="form-control"></select>
                                                    </div>
                                                </div>                                         
                                            </div>

                                            <div class="row"> 
                                                <div class="col-xs-3 text-right"><h5>Stock?</h5></div>
                                                <div class="col-xs-2">
                                                    <div class="form-group">
                                                        <input inputmode="none"  type="checkbox" id="config_stock" data-off-text="No" data-on-text="Si"/>      
                                                    </div>
                                                </div>
                                                <div class="col-xs-1">
                                                    <div class="form-group">
                                                        <input inputmode="none"  class="form-control"  type="text" id="txt_cantidadproducto" maxlength="6" onkeypress="return isNumberKey(event);"/>      
                                                    </div>
                                                </div>
                                                <div class="col-xs-3 text-right"><h5>Requiere autorizaci&oacute;n para anulaci&oacute;n?</h5></div>
                                                <div class="col-xs-2">
                                                    <div class="form-group">
                                                        <input inputmode="none"  type="checkbox" id="config_anuu" data-off-text="No" data-on-text="Si"/>          
                                                    </div>
                                                </div>                                                                                     
                                            </div>

                                            <div class="row">
                                                <div class="col-xs-3 text-right"><h5>Tiempo de Preparación (min):</h5></div>
                                                <div class="col-xs-1">
                                                    <div class="form-group">
                                                        <input inputmode="none"  class="form-control" type="text" id="txt_timepreparacion" maxlength="2" onkeypress="return isNumberKey(event);"/>    
                                                    </div>
                                                </div>                      
                                                <div class="col-xs-5 text-right"><h5>Es producto al peso?</h5></div>
                                                <div class="col-xs-3">
                                                    <div class="form-group">
                                                        <input inputmode="none"  type="checkbox" id="config_gramoss" data-off-text="No" data-on-text="Si"/>          
                                                    </div>
                                                </div>                                                 
                                            </div>

                                            <div class="row"> 
                                                <div class="col-xs-3 text-right"><h5>Tipo de Producto:</h5></div>
                                                <div class="col-xs-3">
                                                    <div class="form-group">
                                                        <select id="selec_tipoproducto" class="form-control"></select>
                                                    </div>
                                                </div> 
                                                <div class="col-xs-3 text-right"><h5>Se muestra en el display de cocina?</h5></div>
                                                <div class="col-xs-3">
                                                    <div class="form-group">
                                                        <input inputmode="none"  type="checkbox" id="config_qsrr" data-off-text="No" data-on-text="Si"/>         
                                                    </div>
                                                </div>                             
                                            </div>

                                            <div class="row">                    	
                                                <div class="col-xs-3 text-right"><h5>Tipo de Plato:</h5></div>
                                                <div class="col-xs-3">
                                                    <div class="form-group">                                 
                                                        <select class="form-control"  type="text" id="select_tipoplato" /> </select>
                                                    </div>
                                                </div>                                                
                                            </div> 

                                            <div class="row"> 
                                                <div class="col-xs-3 text-right"><h5>Impuestos:</h5></div>
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <select data-placeholder="Seleccionar Impuestos" multiple="multiple" id="selec_impuestos" class="chosen-select"></select>
                                                    </div>
                                                </div>                                                
                                            </div>

                                            <div class="row"> 
                                                <div class="col-xs-3 text-right"><h5>Canal de Impresión:</h5></div>
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <select data-placeholder="Seleccione canal de impresi&oacute;n" multiple="multiple" id="selec_canal_imp" class="chosen-select"></select>
                                                    </div>
                                                </div>                                                
                                            </div>

                                            <div class="row"> 
                                                <div class="col-xs-3 text-right"><h5>Master Plu:</h5></div>
                                                <div class="col-xs-6 chosen-container-active">
                                                    <div class="form-group">
                                                        <select class="page-select chzn-drop-up" id="cod_reportnumber" /> </select>                                 
                                                        <!--<input inputmode="none"  class="form-control" type="text" id="num_plu"/>-->           
                                                    </div>
                                                </div>
                                                <!--<div class="col-xs-3 text-right"><h5>Canal:</h5></div>-->
                                                <!--<div class="col-xs-2">
                                                    <div class="form-group">
                                                         <input inputmode="none"  class="form-control" type="text" id="cod_reportnumbercanal" readonly="readonly"/>         
                                                    </div>
                                                </div>  -->
                                            </div>

                                            <div class="row"> 
                                                <div class="col-xs-3 text-right"><h5>C&oacute;digo de barras:</h5></div>
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <input inputmode="none"  class="form-control" type="text" id="cod_barras"/>    
                                                    </div>
                                                </div>
                                            </div> 

                                            <!-- </table>-->                  

                                        </div></p>
                                </div>
                                <div id="menu2" class="tab-pane fade">      
                                    <p>
                                        <div class="panel-body">
                                            <!-- <div class="col-xs-12"></div>-->
                                            <div id="areaTrabajo" >
                                                <div class="col-xs-1 text-right"><h5>Buscar:</h5></div>
                                                <div class="col-xs-11">
                                                    <div class="form-group">
                                                        <input inputmode="none"  class="form-control" name="buscador" id="buscador" type="text" placeholder="Describa la palabra o pregunta a buscar."/>    
                                                    </div>
                                                </div>                   
                                                <div class="col-md-6">
                                                    <div style="height:300px; overflow:auto;">
                                                    <!--<table class="table table-bordered">
                                                    <tr>
                                                        <td class="active"><h5>Preguntas Sugeridas</h5></td>
                                                    </tr>
                                                    </table>-->
                                                        <table class="table table-bordered" id="preguntas"></table>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div style="height:300px; overflow:auto;">
                                                    <!--<table class="table table-bordered">
                                                            <tr>
                                                            <td class="active"><h5>Preguntas Asociadas al Producto</h5></td>
                                                            </tr>
                                                    </table>-->

                                                        <table class="table table-bordered" id="preguntas_agregadas"></table>
                                                        <ol id="ul_sortable" class="sortable" style="list-style-type:none; margin:0; padding:0; "></ol>

                                                    </div>
                                                </div>
                                            </div><!-- Fin Area Trabajo -->
                                        </div>                            

                                    </p>
                                </div>
                                <div id="menu1" class="tab-pane fade">      
                                    <p>  
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <div class="row"><!--inicio imput radio button y buscador-->
                                                    <input inputmode="none"  type="hidden" id="hid_num_plu" />
                                                    <div class="col-xs-3 text-right"><h5>Categoria de Precios:</h5></div>
                                                    <div class="col-sm-3"><!--inicio radio button--> 		
                                                        <!--<div id='selec_categorias2' class="btn-group" data-toggle="buttons">                        
                                                </div>-->
                                                        <select class="form-control"  type="text" id="selec_categorias2" onchange="fn_setSelectRestaurante(); fn_cargarRecetasdetalle();"/> </select>           
                                                    </div><!--fin radio button-->
                                                    <div class="col-xs-2 text-right"><h5>Restaurante:</h5></div>
                                                    <div class="col-sm-3"><!--inicio radio button--> 		
                                                        <!--<div id='selec_categorias2' class="btn-group" data-toggle="buttons">                        
                                                </div>-->
                                                        <select class="form-control"  type="text" id="selec_localusuario" onchange="fn_cargarRecetasdetalle();"/> </select>           
                                                    </div> 
                                                </div>
                                                <br/>
                                                <div class="row"><!--inicio imput radio button y buscador-->
                                                    <div class="col-xs-3 text-right"><h5>Recetas por Ubicación:</h5></div>
                                                    <div class="col-sm-9"><!--inicio radio button--> 		
                                                        <div id='selec_ubicacion' class="btn-group" data-toggle="buttons">                        
                                                        </div>           
                                                    </div><!--fin radio button--> 
                                                </div>
                                            </div>
                                            <div class="panel-body">
                                                <div class="col-md-12">
                                                    <div id="detallereceta">
                                                        <table class="table table-bordered" id="cabecera_subrecetas"></table>
                                                        <table class="table table-bordered" id="recetas_subrecetas"></table>
                                                        <table class="table table-bordered" id="recetas_total"></table>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>  			      

                                    </p>
                                </div>    
                            </div>
                        </div>

                        <div id="listapreguntas">
                            <div class="tabla">
                                <table id="lista_agregadas"></table>
                            </div>
                        </div> 

                    </div> <!-- Fin contenedor_configuraciones -->
                    <div class="modal-footer panel-footer">
                        <button type="button" class="btn btn-primary" onClick="fn_limpiarCampos(); fn_verificarstock();">Aceptar</button>

                        <!--<button type="button" class="btn btn-primary" onClick="fn_verificarstock();">Aceptar</button>-->
                        <button type="button" class="btn btn-default" data-dismiss="modal" >Cancelar</button>
                    </div>
                </div>
            </div> 
        </div> 

        <input inputmode="none"  type="hidden" id="num_orden_preguntas">



            <div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
                <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                    <img src="../../imagenes/admin_resources/progressBar.gif" />
                </div>
            </div> 



    </body>
</html>