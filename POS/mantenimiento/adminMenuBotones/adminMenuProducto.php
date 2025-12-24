<?php
//////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Juan Méndez /////////////////////////////////////
//DESCRIPCION:Configuración de Botones del Producto///////////////////////
///////TABLAS INVOLUCRADAS: //////// /////////////////////////////////////
///////FECHA CREACION: 13-12-2013/////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 17-02-2014//////////////////////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Corrección de errores////////////////////
///////FECHA ULTIMA MODIFICACION: 26/05/2015//////////////////////////////
///////USUARIO QUE MODIFICO:  Jose Fernandez//////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Nuevo estilo en pantalla y///////////////
//////////////////////////////mejora de la funcionalidad//////////////////
//////////////////////////////Buscador todos los campos///////////////////
//////////////////////////////Cambio de etiquetas y estados con check/////
//////////////////////////////////////////////////////////////////////////   
	
session_start();
include_once"../../seguridades/seguridad.inc";
include_once"../../system/conexion/clase_sql.php";
include_once"../../clases/clase_seguridades.php";
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=8"/>
    
    <title>Administrador Producto</title>
   
    <!---------------------------------------------------
                           ESTILOS
    ----------------------------------------------------->    
	<link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />   
    <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />   
    <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css" />
	<link rel="stylesheet" type="text/css" href="../../css/alertify.default.css" />
    <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
    <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />   
	<link rel="stylesheet" type="text/css" href="../../css/spectrum.css" /> 
 	
    <!---------------------------------------------------
                           JQUERY
    ----------------------------------------------------->
   	<script src="../../js/jquery1.11.1.js"></script>     
    <script src="../../js/ajax_datatables.js"></script>  
    <script src="../../bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
    <script src="../../bootstrap/js/bootstrap-dataTables.js"></script>              
    <script language="javascript1.1"  src="../../js/alertify.js"></script>
	<script type="text/javascript" src="../../js/ajax.js"></script> 
	<script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>    
    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>
    <script src="../../js/spectrum.js"></script>
    <script type="text/javascript" src="../../js/ajax_menuProducto.js"></script>
     

</head>

<body> 
  	<input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
    <input inputmode="none"  id="rest_id" type="hidden" value="<?php echo $_SESSION['rstId']; ?>"/>
    
    <div class="superior"><!-- titulo -->
        <div class="menu" style="width: 466px;" align="center">
            <ul>
				<li><input inputmode="none"  id="btn_agregar" type="button" onClick="fn_agregar()" class="botonhabilitado" value="Agregar"/></li>
            </ul>
        </div>
        <div class="tituloPantalla">
			<h1>BOTONES</h1>
		</div>        
    </div>
 
<div class="inferior"> 
   <div id="contenedor_categorias" class="panel panel-default">
   		<div class="panel-heading"><!--inicio de cabecera-->
        	<div class="row"><!--inicio imput radio button y buscador-->
                <div class="col-sm-6"><!--inicio radio button-->
                	<h5><div id='opciones_estado' class="btn-group" data-toggle="buttons"> 
                    <label id="check_activos" class="btn btn-default btn-sm active" onClick="fn_cargarCaracteristicaPorEstado(0,11,'activo');">Activos
                   <input inputmode="none"  type="radio" name="uno" id="check_activos" value="1" >
                    </label>
                   <label class="btn btn-default btn-sm" onclick="fn_cargarCaracteristicaPorEstado(0,11,'inactivo');">
                   <input inputmode="none"  type="radio" name="uno" id="check_inactivos" value="2">Inactivos
                   </label>  
                   <label class="btn btn-default btn-sm" onClick="fn_cargarCaracteristica(0,11);">Todos
                   <input inputmode="none"  id="check_todos" type="radio" name="uno" value="0">
                   </label>                                                        	
                </div></h5>
                </div><!--fin radio button-->
                <div class="col-sm-6"><!--inicio buscador-->
                    <div><h4><b>Restaurante
                                Seleccionado: </b> <?php echo($_SESSION["rstNombre"] . ' - <b>ID: </b>' . $_SESSION["rstId"]) ?>
                        </h4></div>
					<div class="input-group input-group-sm">                       
                         <span class="input-group-addon glyphicon glyphicon-search" id="img_buscar"></span>
                         <span class="input-group-addon glyphicon glyphicon-remove" id="img_remove" onClick="fn_limpiaBuscador(); fn_cargarCaracteristica(0,11);"></span>
                        <input inputmode="none"  id="par_numplu" aria-controls="DataTables_Table_0" type="search" class="form-control" placeholder="Buscar" aria-describedby="sizing-addon1">
                	</div>
                </div> <!-- fin buscador -->
         	</div> <!-- fin radio button y buscador -->
        </div><!--fin cabecera-->   
        
        <div id="menu_producto" class="panel-body"><!-- inicio de div detalle de productos -->						        	
            <table id="detalle_preguntas" class="table table-bordered table-hover"></table>                       	
        </div>   <!-- fin de div detalle de productos -->             	
		<div id="panel_footer_agregar" class="panel-footer text-center">
              <nav><ul id="paginas" class="pagination"></ul></nav>
        </div>
        </div>
  </div>
  

  
  
		
<div class="modal fade" id="mdl_menuProducto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">   
<div class="modal-dialog modal-lg">
	        <div class="modal-content">
    		    <div class="modal-header panel-footer">
        			<button type="button" class="close" data-dismiss="modal" onclick="fn_limpiarpapeleta();" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        			<h4 class="modal-title" id="myModalLabel">Producto</h4>
        		</div>                
                
        		<div class="modal-body"> 
<!--INICIO TAB--> 
<div class="contenedor_tabs">  
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">Bot&oacute;n</a></li>
    <li><a data-toggle="tab" href="#menu1">Tiendas Aplicar</a></li>   
  </ul>

  <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
    
<div class="col-xs-12 col-x text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" enabled id="check_activo" value=""/></h6>
</div>
<div class="row">
    <div class="col-xs-1"></div>
    <div class="col-xs-3 text-right"><h5>Nombre del Producto:</h5></div>
    <div class="col-xs-6">
        <div class="form-group">
            <input inputmode="none"  class="form-control" type="text" id="nombrePlu" maxlength="40" disabled />
         </div>
    </div>
    <div class="col-xs-1"></div>
</div>                   
<div class="row">
    <div class="col-xs-1"></div>
    <div class="col-xs-3 text-right"><h5>Nombre del Bot&oacute;n:</h5></div>
    <div class="col-xs-6">
        <div class="form-group">
            <input inputmode="none"  class="form-control" id="impresionPluModificar"  maxlength="40"/>               
        </div>
     </div>
    <div class="col-xs-1"></div>
</div>
 <div class="row">
    <div class="col-xs-1"></div>
    <div class="col-xs-3 text-right"><h5>Impresi&oacute;n en Factura:</h5></div>
    <div class="col-xs-6">
        <div class="form-group">
            <input inputmode="none"  class="form-control" id="impresionfactura"  maxlength="40"/>               
        </div>
     </div>
    <div class="col-xs-1"></div>
</div>
<div class="row">
    <div class="col-xs-1"></div>
    <div class="col-xs-3 text-right"><h5>Color de Texto:</h5></div>
    <div class="col-xs-7">
            <div class="form-group">
                <input inputmode="none"  class="form-control" type="color" id="colorTextoo" style="width:30%;" />               
            </div>
        </div>
    <div class="col-xs-1"></div>
</div>

<div class="row">
    <div class="col-xs-1"></div>
    <div class="col-xs-3 text-right"><h5>Color de Fondo:</h5></div>
    <div class="col-xs-7">
        <div class="form-group">
            <input inputmode="none"  type="color" class="form-control" id="colorFondoo" style="width:30%;" onKeyUp="aMays(event, this)" onBlur="aMays(event, this)"/>
        </div>
    </div>
    <div class="col-xs-1"></div>
</div>
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-3"><h5 class="text-right">Per&iacute;odo de Validez:</h5></div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="FechaInicial" class="control-label">Fecha Inicio</label>
            <div class="input-prepend input-group">
                <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                <input inputmode="none"  type="text" value="" class="form-control" name="FechaInicial" id="FechaInicial" placeholder="Fecha Inicio"/>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="FechaFinal" class="control-label">Fecha Fin</label>
            <div class="input-prepend input-group">
                <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                <input inputmode="none"  type="text" value="" class="form-control" name="FechaFinal" id="FechaFinal" placeholder="Fecha Fin"/>
            </div>
        </div>
    </div>
    <div class="col-md-2"></div>
</div> 
</div>

<div role="tabpanel" class="tab-pane fade" id="menu1"> <br/>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-8"><h5>Tiendas:</h5>
                    <div id="opciones_aplicar" class="btn-group" data-toggle="buttons">
                        <label id="opcion_aplica" class="btn btn-default btn-sm active" onclick="marcarmod(':checkbox');">
                        <input inputmode="none"  id="opt_Todos" type="radio" value="Todos" checked="" autocomplete="off" name="options">Todos
                        </label>
                        <label class="btn btn-default btn-sm" onclick="desmarcarmod(':checkbox')">
                        <input inputmode="none"  id="opt_Activos" type="radio" value="Activos" autocomplete="off" name="options">Ninguno
                        </label>
                    </div>
                </div>
            </div>            
         </div>
         
         <div class="form-group" id="rest">
             <div style="height: 280px; overflow-y: auto;">
                <div id="rst_agregado" class="list-group"></div>         
             </div>
     	</div>
    </div>
</div>

    </div>    
  </div>
</div>
<!--FIN TAB MODIFICAR-->
                 
</div>
<div class="modal-footer panel-footer">
    <button type="button" class="btn btn-primary" onClick="fn_guardarInfoMenuProducto();">Aceptar</button>
    <button type="button" class="btn btn-default" onClick="fn_limpiarpapeleta();" data-dismiss="modal">Cancelar</button>
</div>
</div>
</div>
</div>
</div>
         
        <div class="modal fade" id="mdl_menuProductoNuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">         <div class="modal-dialog modal-lg">
	        <div class="modal-content">
    		    <div class="modal-header panel-footer">
        			<button type="button" class="close" data-dismiss="modal" onclick="fn_limpiarpapeleta();" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        			<h4 class="modal-title" id="myModalLabel">Nuevo Bot&oacute;n</h4>
        		</div>
        		<div class="modal-body">
                
<div class="contenedor_tabs_nuevo">  
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home_nuevo">Bot&oacute;n</a></li>
    <li><a data-toggle="tab" href="#menu_nuevo">Tiendas Aplicar</a></li>    
  </ul>

  <div class="tab-content">
    <div id="home_nuevo" class="tab-pane fade in active">
                                	
<div class="col-xs-12 col-x text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" disabled="disabled" checked="checked" id="check_activo" value=""/></h6>
</div>
<div class="row">
    <div class="col-xs-1"></div>
    <div class="col-xs-3 text-right"><h5>Nombre del Producto:</h5></div>
    <div class="col-xs-6">
             <select class="form-control" id="selPlus" ></select>	                                            
    </div>
    <div class="col-xs-1">
    </div>
</div>                   
<div class="row">
    <div class="col-xs-1"></div>
    <div class="col-xs-3 text-right"><h5>Nombre de Bot&oacute;n:</h5></div>
    <div class="col-xs-6">
        <div class="form-group">
            <input inputmode="none"  class="form-control" id="impresionPluNuevo" maxlength="40"/> 
       </div>
     </div>
    <div class="col-xs-1"></div>
</div>
<div class="row">
    <div class="col-xs-1"></div>
    <div class="col-xs-3 text-right"><h5>Impresi&oacute;n en Factura:</h5></div>
    <div class="col-xs-6">
        <div class="form-group">
            <input inputmode="none"  class="form-control" id="impresionfacturanuevo"  maxlength="40"/>               
        </div>
     </div>
    <div class="col-xs-1"></div>
</div>
<div class="row">
    <div class="col-xs-1"></div>
    <div class="col-xs-3 text-right"><h5>Color de Texto:</h5></div>
    <div class="col-xs-7">
            <div class="form-group">
            	<input inputmode="none"  type='color' name='colorTextooNuevo' id="colorTextooNuevo"/>                                                     	       
            </div>
        </div>
    <div class="col-xs-1"></div>
</div>
<div class="row">
    <div class="col-xs-1"></div>
    <div class="col-xs-3 text-right"><h5>Color de Fondo:</h5></div>
    <div class="col-xs-7">
        <div class="form-group">
            <input inputmode="none"  type="color" class="form-control" id="colorFondooNuevo" style="width:30%;" onKeyUp="aMays(event, this)" onBlur="aMays(event, this)"/>
        </div>
    </div>
    <div class="col-xs-1"></div>
</div>
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-3"><h5 class="text-right">Per&iacute;odo de Validez:</h5></div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="FechaInicialNuevo" class="control-label">Fecha Inicio</label>
            <div class="input-prepend input-group">
                <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                <input inputmode="none"  type="text" value="" class="form-control" name="daterange" id="FechaInicialNuevo" placeholder="Fecha Inicio"/>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="FechaFinalNuevo" class="control-label">Fecha Fin</label>
            <div class="input-prepend input-group">
                <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                <input inputmode="none"  type="text" value="<?php echo date("01/01/2030"); ?>" class="form-control" name="daterange" id="FechaFinalNuevo" placeholder="Fecha Fin"/>
            </div>
        </div>
    </div>
    <div class="col-md-2"></div>
</div>                         
</div>
    
<div role="tabpanel_nuevo" class="tab-pane fade" id="menu_nuevo"> <br/>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-8"><h5>Tiendas Aplicar:</h5>
                    <div id="opciones_aplicarnuevo" class="btn-group" data-toggle="buttons">
                        <label id="opciones_aplicanuevos" class="btn btn-default btn-sm active" onclick="marcar(':checkbox');">
                        <input inputmode="none"  id="opt_Todosnuevo" type="radio" value="Todos" checked="" autocomplete="off" name="options_aplica">Todos
                        </label>
                        <label class="btn btn-default btn-sm" onclick="desmarcar(':checkbox');">
                        <input inputmode="none"  id="opt_Activosnuevo" type="radio" value="Activos" autocomplete="off" name="options_aplica">Ninguno
                        </label>
                    </div>
                </div>
            </div>            
         </div>
         
         <div class="form-group" id="rest_nuevo">
             <div style="height: 290px; overflow-y: auto;">
                <div id="rst_agregadonuevo" class="list-group"></div>         
             </div>
     	</div>
    </div>
</div>       
      
  </div>
</div>              
            
                  </div>
                  <div class="modal-footer panel-footer">
                    <button type="button" class="btn btn-primary" onClick="fn_guardarInfoMenuProductoNuevo();">Aceptar</button>
                    <button type="button" class="btn btn-default" onClick="fn_limpiarpapeleta();" data-dismiss="modal">Cancelar</button>
                   </div>
                </div>
           	</div>
        </div>
</div>

	</div>      
</div>

    <input inputmode="none"  type="hidden" id="hid_magId" />
    <input inputmode="none"  type="hidden" id="txt_idrestaurante"/>
    <input inputmode="none"  type="hidden" id="txt_idrestaurantemod"/>
	<input inputmode="none"  type="hidden" id="txt_filarestaurante"/>
    <input inputmode="none"  type="hidden" id="IDMenuAgrupacionProducto"/>
    
</body>
</html>