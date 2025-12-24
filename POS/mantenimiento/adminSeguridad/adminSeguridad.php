<?php
session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuraci�n de formas pago //////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 07-06-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
	
	include_once"../../system/conexion/clase_sql.php";
	include_once"../../clases/clase_seguridades.php";
	include_once"../../seguridades/seguridad_niv3.inc";
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" /> 
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
    <title>Administraci&oacute;n</title>
    
    <!-- ESTILOS -->
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
    <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
    
</head>
<body>
	
    <input inputmode="none"  id="sess_usr_id" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
    <input inputmode="none"  id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
    
	
        
    <div class="superior">
        <div class="menu" style="width: 200px;">
            <ul>
				<li><input inputmode="none"  id="btn_agregar" type="button" onclick="fn_agregar()" class="botonhabilitado" value="Agregar"/></li>
				<li><input inputmode="none"  id="btn_eliminar" type="button" onclick="fn_eliminar()" class="botonhabilitado" value="Eliminar"/></li>
				<li><input inputmode="none"  id="btn_cancelar" type="button" onclick="fn_cancelar()" class="botonhabilitado" value="Cancelar"/></li>
            </ul>
        </div>
        <div class="tituloPantalla">
			<h1>Administraci&oacute;n Seguridad</h1>
		</div>
    </div>
        
	<div class="contenedor">
        
        <div class="inferior">
			
			
            <div class="panel panel-default">
            	<!-- Cabecera -->
                <div class="panel-heading">
                	<h3 id="tqt_mdl" class="panel-title"></h3>
                	<br/>
                    <div id="opciones_estado" class="btn-group" data-toggle="buttons"></div>
                </div>
                <!-- Cuerpo -->
                <div class="panel-body">
                	
                    <div>
                        <!-- Pesta�as -->
                        <ul id="pstns_pcns" class="nav nav-tabs" role="tablist">
                            <li role="presentation" onclick="fn_seleccionPestana(1)" class="active"><a href="#cntdr_prfl" aria-controls="home" role="tab" data-toggle="tab"><h5>Perfiles</h5></a></li>
                            <li role="presentation" onclick="fn_seleccionPestana(0)"><a href="#cntdr_srs" aria-controls="messages" role="tab" data-toggle="tab"><h5>Usuarios</h5></a></li>
                        </ul>
                        
                        <!-- Contenedor Pesta�as -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="cntdr_prfl">
                            	<br/>
                                <center>
                            	<table style="width: 600px;" class="table table-bordered table-hover" id="tbl_lst_prfls"></table>
                                </center>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="cntdr_srs">
                            	<br/>
                                <center>
                            	<table style="width: 900px;" class="table table-bordered table-hover" id="tbl_lst_srs"></table>
                                </center>
                            </div>
                        </div>
                    
                    </div>
                    
                </div>
                <!-- Pie de P�gina -->
                <div class="panel-footer"></div>
            </div>
            
			
        <!-- Fin Contenedor Inferior -->
        </div>
    
    <!-- Fin Contenedor -->
 	</div>
    
    <!-- Modal Perfiles -->
    <div id="mdl_prfl" class="modal fade" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
               		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                	<h4 id="tqt_mdl_prfl" class="modal-title"></h4>
                </div>
               	<div class="modal-body">
                	
                    <br/>
                    <div class="row">
                        <div class="col-md-8"></div>
                        <div class="col-xs-12 col-md-4">
                            <div class="btn-group">
                                <h5 class="text-right"><b>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" id="prfl_std_id"></b></h5>
                            </div>
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-xs-12 col-md-2"><h6><b>Descripci&oacute;n:</b></h6></div>
                        <div class="col-xs-12 col-md-8">
                            <div class="form-group">
                                <input inputmode="none"  type="text" class="form-control" id="prf_descripcion" placeholder="Descripci&oacute;n del Perfil">
                            </div>
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-xs-12 col-md-2"><h6><b>Nivel de Seguridad:</b></h6></div>
                        <div class="col-xs-12 col-md-4">
                            <div class="input-prepend input-group number-spinner">
                                <span class="add-on input-group-addon btn btn-primary" data-dir="dwn"><i class="glyphicon glyphicon-minus fa fa-calendar"></i></span>
                                <input inputmode="none"  id="prf_nvl_seg" type="text" class="form-control text-center" value="0" min="0" max="500">
                                <span class="add-on input-group-addon btn btn-primary" data-dir="up"><i class="glyphicon glyphicon-plus fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-md-5"></div>
                    </div>
                    
                </div>
                <div id="btn_pcn_prfl" class="modal-footer"></div>
            </div>
        </div>
    </div>
    
    <!-- Modal Usuario -->
    <div id="mdl_usr" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
               		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                	<h4 class="modal-title">Titulo</h4>
                </div>
               	<div class="modal-body">
                	
                    
                    
                </div>
                <div class="modal-footer">
	                <button type="button" class="btn btn-primary" onclick="">Guardar</button>
                	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <input inputmode="none"  type="hidden" id="hdn_prf_id" value=""/>
    
    <!-- JavaScript -->
    <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
    <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
	<script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
	<script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/query.bootstrap-growl.min.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/inputnumber.js"></script>
    <script type="text/javascript" src="../../js/ajax_admseguridad.js"></script>
    <script>
		indice = 1;
	</script>

</body>
</html>