<?php
session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuraci�n de accesos //////////////////////
///////FECHA CREACION: 25-01-2016 /////////////////////////////////////////////
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
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/daterangepicker-bs3.css" />
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
    <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css" />
	<link rel="stylesheet" type="text/css" href="../../css/alertify.default.css" />
    <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
    
</head>
<body>
	
    <input inputmode="none"  id="sess_usr_id" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
    <input inputmode="none"  id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
        
    <div class="superior">
        <div class="menu" style="width: 240px;">
            <ul>
				<li><input inputmode="none"  id="btn_agregar" type="button" onclick="fn_agregar()" class="botonhabilitado" value="Agregar"/></li>
				<!-- <li><input inputmode="none"  id="btn_duplicar" type="button" onclick="fn_duplicarPerfil()" class="botonhabilitado" value="Duplicar"/></li> -->
                <!-- <li><input inputmode="none"  id="btn_restablecer" type="button" onclick="fn_restablecer()" class="botonhabilitado" value="Restablecer"/></li> -->
            </ul>
        </div>
        <div class="tituloPantalla">
			<h1>Accesos</h1>
		</div>
    </div>
        
	<div class="contenedor">
        
        <div class="inferior">
			
			
            <div class="panel panel-default">
            	<!-- Cabecera -->
                <div class="panel-heading">
                	<h3 id="tqt_mdl" class="panel-title">Lista de Accesos</h3>
                </div>
                <!-- Cuerpo -->
                <div class="panel-body">
                	
                    <center>
                    	<table class="table table-bordered table-hover" id="tbl_lst_ccs"></table>
                    </center>
                    
                </div>
                <!-- Pie de P�gina -->
                <div class="panel-footer"></div>
            </div>
            
			
        <!-- Fin Contenedor Inferior -->
        </div>
    
    <!-- Fin Contenedor -->
 	</div>
        
    <!-- Modal Perfiles -->
    <div id="mdl_acc" class="modal fade" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
               		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                	<h4 id="tqt_mdl_acc" class="modal-title"></h4>
                </div>
               	<div class="modal-body">
                    <br/>
                    <div id="cnt_frmlr_acc">
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-xs-12 col-md-2"><h6><b>Nombre:</b></h6></div>
                        <div class="col-xs-12 col-md-8">
                            <div class="form-group">
                                <input inputmode="none"  type="text" class="form-control" id="acc_nombre" placeholder="Descripci&oacute;n del Perfil">
                            </div>
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-xs-12 col-md-2"><h6><b>Descripci&oacute;n:</b></h6></div>
                        <div class="col-xs-12 col-md-8">
                            <div class="form-group">
                                <textarea id="acc_descripcion" class="form-control" placeholder="Descripci&oacute;n del acceso"></textarea>
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
                                <input inputmode="none"  id="acc_nvl_seg" type="text" onkeypress="return justNumbers(event);" onchange="fn_validarMaximo()" class="form-control text-center" value="0" min="0" max="100">
                                <span class="add-on input-group-addon btn btn-primary" data-dir="up"><i class="glyphicon glyphicon-plus fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-md-5"></div>
                    </div>
                    <br/>
                </div>
                <div id="btn_pcn_acc" class="modal-footer"></div>
            </div>
        </div>
    </div>
    
    
    <input inputmode="none"  type="hidden" id="hdn_ccs_id" value=""/>
    
    <!-- JavaScript -->
    <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
    <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
	<script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
	<script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
    <script language="javascript1.1"  src="../../js/alertify.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/inputnumber.js"></script>
    <script type="text/javascript" src="../../js/ajax_admacceso.js"></script>
    <script>
		indice = 10;
	</script>

</body>
</html>