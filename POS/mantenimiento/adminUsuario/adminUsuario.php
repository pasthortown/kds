<?php

session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de cambio de clave ///////////////////////////////
///////TABLAS INVOLUCRADAS: Usuario del Sistema ///////////////////////////////
///////FECHA CREACION: 22-0612016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
	
	include_once("../../system/conexion/clase_sql.php");
	include_once("../../clases/clase_seguridades.php");
	include_once("../../seguridades/seguridad_niv3.inc");
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
    <title>Administraci&oacute;n</title>
    
    <!-- ESTILOS -->
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
    <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css" />
	<link rel="stylesheet" type="text/css" href="../../css/alertify.default.css" />
    <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
    
</head>
<body>
	
    <input inputmode="none"  id="sess_usr_id" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
    <input inputmode="none"  id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
    
	
        
    <div class="superior">
        <div class="menu" style="width: 300px;">
        </div>
        <div class="tituloPantalla">
			<h1>Usuario</h1>
		</div>
    </div>
        
	<div class="contenedor">
        
        <div class="inferior">
			
			
            <div class="panel panel-default">
            	<!-- Cabecera -->
                <div class="panel-heading"><h3 class="panel-title">Datos Personales</h3></div>
                <!-- Cuerpo -->
                <div class="panel-body">
                	
                    <div class="row">
	                    <div class="col-md-2"></div>
                        <div class="col-xs-12 col-md-3"><h5 class="text-right"><b>Nombre:</b></h5></div>
                        <div class="col-xs-12 col-md-4"><h6 id="nmbr_usr"></h6></div>
                        <div class="col-xs-12 col-md-3"></div>
                    </div>
                    
                    <div class="row">
	                    <div class="col-md-2"></div>
                        <div class="col-xs-12 col-md-3"><h5 class="text-right"><b>Cedula</b></h5></div>
                        <div class="col-xs-12 col-md-4"><h6 id="cdl_usr"></h6></div>
                        <div class="col-xs-12 col-md-3"></div>
                    </div>
                    
                    <div class="row">
	                    <div class="col-md-2"></div>
                    	<div class="col-xs-12 col-md-3"><h5 class="text-right"><b>Iniciales:</b></h5></div>
                        <div class="col-xs-12 col-md-4"><h6 id="ncls_usr"></h6></div>
                        <div class="col-xs-12 col-md-3"></div>
                    </div>
                    
                    <div class="row">
	                    <div class="col-md-2"></div>
                    	<div class="col-xs-12 col-md-3"><h5 class="text-right"><b>Fecha &Uacute;ltimo Ingreso:</b></h5></div>
                        <div class="col-xs-12 col-md-4"><h6 id="fch_ltm_ngrs_usr"></h6></div>
                        <div class="col-xs-12 col-md-3"></div>
                    </div>
                    
                    <div class="row">
	                    <div class="col-md-2"></div>
                    	<div class="col-xs-12 col-md-3"><h5 class="text-right"><b>Perfil:</b></h5></div>
                        <div class="col-xs-12 col-md-4"><h6 id="prfl_usr"></h6></div>
                        <div class="col-xs-12 col-md-3"></div>
                    </div>
                    
                    <div class="row">
	                    <div class="col-md-2"></div>
                    	<div class="col-xs-12 col-md-3"><h5 class="text-right"><b>Usuario:</b></h5></div>
                        <div class="col-xs-12 col-md-4"><h6 id="mxpnt_usr"></h6></div>
                        <div class="col-xs-12 col-md-3"></div>
                    </div>
                    
                    <div class="row">
	                    <div class="col-md-2"></div>
                    	<div class="col-xs-12 col-md-3"><h5 class="text-right"><b>Contrase&ntilde;a:</b></h5></div>
                        <div class="col-xs-12 col-md-4"><button type="button" onclick="fn_cambioContrasena()" class="btn btn-primary">Cambiar</button></div>
                        <div class="col-xs-12 col-md-3"></div>
                    </div>
                    
                </div>
                <!-- Pie de P�gina -->
                <div class="panel-footer"></div>
            </div>
            
			
        <!-- Fin Contenedor Inferior -->
        </div>
    
    <!-- Fin Contenedor -->
 	</div>
    
    <div id="mdl_clv" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
               		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                	<h4 class="modal-title">Cambio de Contrase&ntilde;a</h4>
                </div>
               	<div class="modal-body">
                	
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <strong>Alerta!</strong> Tu contrase&ntilde;a debe contener n&uacute;meros y letras.
                    </div>
                    
                	<div class="row">
	                    <div class="col-md-2"></div>
                        <div class="col-xs-12 col-md-3"><h6 class="text-right"><b>Contrase&ntilde;a Actual:</b></h6></div>
                        <div class="col-xs-12 col-md-5">
                        	<div class="form-group">
                        		<input inputmode="none"  type="password" class="form-control" id="ctsn_ctl" placeholder="Ingrese Contrase&ntilde;a Actual">
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-2"></div>
                    </div>
                    <div class="row">
	                    <div class="col-md-2"></div>
                        <div class="col-xs-12 col-md-3"><h6 class="text-right"><b>Contrase&ntilde;a Nueva:</b></h6></div>
                        <div class="col-xs-12 col-md-5">
	                        <div class="form-group">
                        		<input inputmode="none"  type="password" class="form-control" id="ctsn_nv" placeholder="Nueva Contrase&ntilde;a">
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-2"></div>
                    </div>
                    <div class="row">
	                    <div class="col-md-2"></div>
                        <div class="col-xs-12 col-md-3"><h6 class="text-right"><b>Confirmaci&oacute;n:</b></h6></div>
                        <div class="col-xs-12 col-md-5">
	                        <div class="form-group">
                        		<input inputmode="none"  type="password" class="form-control" id="cnfrmcn_ctsn_nv" placeholder="Confirmar Nueva Contrase&ntilde;a">
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4"></div>
                    </div>
                </div>
                <div class="modal-footer">
	                <button type="button" class="btn btn-primary" onclick="fn_actualizarContrasena()">Guardar</button>
                	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
    <!-- JavaScript -->
    <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
	<script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
	<script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
	<script language="javascript1.1"  src="../../js/alertify.js"></script>
    <script type="text/javascript" src="../../js/ajax_admusuario.js"></script>

</body>
</html>