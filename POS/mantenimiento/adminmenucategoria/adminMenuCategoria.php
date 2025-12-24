<?php
///////////////////////////////////////////////////////////
///////DESARROLLADO POR: Juan M�ndez //////////////////////
//DESCRIPCION:Configuraci�n de Botones del Categoria///////
///////TABLAS INVOLUCRADAS: ///////////////////////////////
///////FECHA CREACION: 13-12-2013//////////////////////////
///////FECHA ULTIMA MODIFICACION: 09-04-2014///////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro/////////////
///////DECRIPCION ULTIMO CAMBIO: Tipo de Menu//////////////
///////////////////////////////////////////////////////////  
///////FECHA ULTIMA MODIFICACION: 25-05-2015 //////////////
///////USUARIO QUE MODIFICO: Jimmy Cazaro /////////////////
///////DECRIPCION ULTIMO CAMBIO: estilos, funcionalidad y /
/////// y filtros /////////////////////////////////////////
///////////////////////////////////////////////////////////
	
	session_start();
	include_once"../../system/conexion/clase_sql.php";
	include_once"../../clases/clase_seguridades.php";
	include_once"../../seguridades/seguridad_niv3.inc";
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" /> 
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
    <title>Administraci&oacute;n Categor&iacute;a</title>
    
    <!-- Librerias Estilos -->
  	<link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css" />
	<link rel="stylesheet" type="text/css" href="../../css/alertify.default.css" />
    <link rel="stylesheet" type="text/css" href="../../css/spectrum.css" />
    <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
            <link rel="stylesheet" type="text/css" href="../../css/productos.css"/>

</head>

<body>
	<input inputmode="none"  id="usuario" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
    <input inputmode="none"  id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
    <input inputmode="none"  id="magid" type="hidden" value="0"/>
    
    <div class="superior">
        <div class="menu" align="center" style="width: 466px;">
            <ul>
				<li><input inputmode="none"  id="btn_agregar" type="button" onclick="fn_agregar()" class="botonhabilitado" value="Agregar"/></li>
            </ul>
        </div>
        <div class="tituloPantalla">
			<h1>CATEGOR&Iacute;A</h1>
		</div>
    </div>

<div class="contenedor">
    <div class="inferior">
        <div class="aplicacion">
            	<div id="formulario">
                                       
<div id="contenedor_categorias" class="panel panel-default">
    <div class="panel-heading">                        	
        <div class="row">
            <div class="col-sm-8">
                <div id='opciones_estado' class="btn-group" data-toggle="buttons">
                    <label id='opciones_1' class="btn btn-default btn-sm active" onclick="fn_cargarCategoria(2, 'Activo', 0);">
                        <input inputmode="none"  type="radio" name="options" id="opt_Activos" autocomplete="off" checked value="1">Activos
                    </label>
                    <label  id='opciones_2' class="btn btn-default btn-sm" onclick="fn_cargarCategoria(2, 'Inactivo', 0);">
                        <input inputmode="none"  type="radio" name="options" id="opt_Inactivos" autocomplete="off" value="2">Inactivos
                    </label>
					<label  id='opciones_3' class="btn btn-default btn-sm" onclick="fn_cargarCategoria(0, '0', 0);">
						<input inputmode="none"  type="radio" name="options" id="opt_Todos" autocomplete="off" value="0">Todos
					</label>
                 </div>
			</div>
        </div>
    </div>
    <div id="panel_body_agregar" class="panel-body">
        <table id="lista_categorias" class="table table-bordered table-hover" border="1" cellpadding="1" cellspacing="0"></table>
    </div>
    <div id="panel_footer_agregar" class="panel-footer text-center">
        <nav><ul id="paginas" class="pagination"></ul></nav>
    </div>
</div>

<!-- Modal Agregar -->
<div class="modal fade" id="agregarCategoria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header panel-footer">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="fn_destruirPaletaAgregar();"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel1">Nueva Secci&oacute;n</h4>
      </div>
      <div class="modal-body">
      		<div class="row">
            	<div class="col-xs-11 text-right">
                	<h6><b>Est&aacute; Activo?:</b> <input inputmode="none"  type="checkbox" checked="checked" id="check_activo" value=""/></h6>
                </div>
            </div>
			<br/>
            <!--
            <div class="row">
            	<div class="col-xs-1"></div>
				<div class="col-xs-3 text-right"><h5>Men&uacute;:</h5></div>
            	<div class="col-xs-7 text-right">
	                <div class="form-group">
                		<select id="slct_mn_id" class="form-control"></select>
                    </div>
                </div>
            </div>
			<br/>
            -->
            <div class="row">
            	<div class="col-xs-1"></div>
				<div class="col-xs-3 text-right"><h5>Nombre:</h5></div>
                <div class="col-xs-7">
					<div class="form-group">
						<input inputmode="none"  class="form-control" maxlength="40" type="text" name="nombreCategoria" id="nombreCategoria" onkeyup="aMays(event, this), fn_actualizarTextoAgregar();" onblur="aMays(event, this)"/>
						<input inputmode="none"  type="hidden" name="idCategoria" id="idCategoria"/>           
					</div>
				</div>
                <div class="col-xs-1"></div>
			</div>
            <div class="row">
            	<div class="col-xs-1"></div>
				<div class="col-xs-3 text-right"><h5>Color de Texto:</h5></div>
				<div class="col-xs-7">
					<div class="form-group">
						<input inputmode="none"  class="form-control" type="color" name="colorTexto" id="colorTexto" style="width:30%;" onkeyup="aMays(event, this)" onblur="aMays(event, this)" onchange="fn_actualizarColorTextoAgregar();"/>
                 	</div>
				</div>
                <div class="col-xs-1"></div>
            </div>
			<div class="row">
            	<div class="col-xs-1"></div>
				<div class="col-xs-3 text-right"><h5>Color de Fondo:</h5></div>
				<div class="col-xs-7">
					<div class="form-group">
						<input inputmode="none"  type="color" class="form-control" name="colorFondo" id="colorFondo" style="width:30%;" onkeyup="aMays(event, this)" onblur="aMays(event, this)" onchange="fn_actualizarColorAgregar();"/>
                 	</div>
				</div>
                <div class="col-xs-1"></div>
            </div>
			<div class="row" style="display:none">
            	<div class="col-xs-1"></div>
				<div class="col-xs-3"><h5>Imagen:</h5></div>
				<div class="col-xs-7">
					<div class="form-group">
						<input inputmode="none"  type="file" class="form-control" name="imagen"/>
                 	</div>
				</div>
                <div class="col-xs-1"></div>
            </div> 
			<div class="row">
            	<div class="col-xs-1"></div>
				<div class="col-xs-3 text-right"><h5>Como se visualiza:</h5></div>
				<div class="col-xs-7">
					<div class="form-group">
						<input inputmode="none"  id='fondoEjemplo' class="form-control" readonly="readonly" />             
                 	</div>
				</div>
                <div class="col-xs-1"></div>
            </div>
      </div>
      <div id="pcn_mdl_mn_ctgrs_grgr" class="modal-footer panel-footer"></div>
    </div>
  </div>
</div>


<!-- Modal Modificar -->
<div class="modal fade" id="modificarCategoria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <!--<div class="modal-dialog  modal-lg">-->
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header panel-footer">
        <button type="button" class="close" data-dismiss="modal" onclick="fn_destruirPaletaModificar();" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modificar Secci&oacute;n</h4>
      </div>
      <div class="modal-body">
            <div class="row">
                <div class="col-xs-12 col-x text-right">
                    <h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" checked="checked" enabled id="check_activonuevo" value=""/></h6>
                </div>
            </div>
            <br/>
            <div class="row">
            	<div class="col-xs-1"></div>
				<div class="col-xs-3 text-right"><h5>Nombre:</h5></div>
                <div class="col-xs-7">
					<div class="form-group">
						<input inputmode="none"  class="form-control" type="text" name="mod_nombreCategoria" id="mod_nombreCategoria" onkeyup="aMays(event, this), fn_actualizarTexto();" onblur="aMays(event, this);"/>
						<input inputmode="none"  type="hidden" name="mod_magid" id="mod_magid"/>           
					</div>
				</div>
                <div class="col-xs-1"></div>
			</div>        
            
			<div class="row">
            	<div class="col-xs-1"></div>
				<div class="col-xs-3 text-right"><h5>Color de Texto:</h5></div>
				<div class="col-xs-7">
					<div class="form-group">
						<input inputmode="none"  class="form-control" type="color" name="mod_colorTexto" id="mod_colorTexto" onchange="fn_actualizarColorTexto()" style="width:30%;" onkeyup="aMays(event, this)" onblur="aMays(event, this)"/>
                 	</div>
				</div>
                <div class="col-xs-1"></div>
            </div>
            
			<div class="row">
            	<div class="col-xs-1"></div>
				<div class="col-xs-3 text-right"><h5>Color de Fondo:</h5></div>
				<div class="col-xs-7">
					<div class="form-group">
						<input inputmode="none"  type="color" class="form-control" name="mod_colorFondo" onchange="fn_actualizarColor()" id="mod_colorFondo" style="width:30%;" onkeyup="aMays(event, this)" onblur="aMays(event, this)"/>
                 	</div>
				</div>
                <div class="col-xs-1"></div>
            </div>
            
			<div class="row" style="display:none">
            	<div class="col-xs-1"></div>
				<div class="col-xs-3"><h5>Imagen:</h5></div>
				<div class="col-xs-7">
					<div class="form-group">
						<input inputmode="none"  type="file" class="form-control" name="imagen"/>
                 	</div>
				</div>
                <div class="col-xs-1"></div>
            </div>
			
			<div class="row">
            	<div class="col-xs-1"></div>
				<div class="col-xs-3 text-right"><h5>Como se visualiza:</h5></div>
				<div class="col-xs-7">
					<div class="form-group">
						<input inputmode="none"  id='mod_fondoEjemplo' class="form-control" readonly="readonly" />
                 	</div>
				</div>
                <div class="col-xs-1"></div>
            </div>
      </div>
      <div id="pcn_mdl_mn_ctgrs" class="modal-footer panel-footer"></div>
    </div>
  </div>
</div>
			</div>
		</div>
    </div> 
		
    
    
    
         <div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../../imagenes/admin_resources/progressBar.gif" />
            </div>
        </div>
    
    
    
        <!-- Librerias JS -->
        <script src="../../js/jquery1.11.1.js"></script>
        <script src="../../bootstrap/js/bootstrap.js"></script>
        <script language="javascript1.1" src="../../js/alertify.js"></script>
        <script src="../../js/ajax_datatables.js"></script>
        <script src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script src="../../js/spectrum.js"></script>
        <script type="text/javascript" src="../../js/ajax_menuCategoria.js"></script>

</body>
</html>