<?php
session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de Configuraci�n de Pantallas ////////////////////
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
    <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css" />
	<link rel="stylesheet" type="text/css" href="../../css/alertify.default.css" />
    <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
    <link rel="stylesheet" type="text/css" href="../../bootstrap/templete/css/icons.css" />
	<link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
	
</head>
<body>
	
    <input inputmode="none"  id="sess_usr_id" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
    <input inputmode="none"  id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
        
    <div class="superior">
        <div class="menu" style="width: 240px;">
            <ul>
				<li><input inputmode="none"  id="btn_agregar" type="button" onclick="fn_agregar()" class="botonhabilitado" value="Agregar"/></li>
            </ul>
        </div>
        <div class="tituloPantalla">
			<h1>PANTALLAS</h1>
		</div>
    </div>
        
	<div class="contenedor">
        
        <div class="inferior">
			
			
            <div class="panel panel-default">
            	<!-- Cabecera -->
                <div class="panel-heading">
                	<h3 id="tqt_mdl" class="panel-title">Lista de Pantallas</h3>
                	<br/>
                    <div id="opciones_estado" class="btn-group" data-toggle="buttons">
	                    <label id="opciones_1" class="btn btn-default btn-sm active" onclick="fn_cargarPantallas(2);">
                        	<input inputmode="none"  type="radio" value="Activos" autocomplete="off"  name="ptns_std_prfl">Activos
                        </label>
                        <label class="btn btn-default btn-sm" onclick="fn_cargarPantallas(1);">
                        	<input inputmode="none"  type="radio" value="Inactivos" autocomplete="off"  name="ptns_std_prfl">Inactivos
                        </label>
                        <label onclick="fn_cargarPantallas(0);" class="btn btn-default btn-sm">
                        	<input inputmode="none"  type="radio" value="Todos" autocomplete="off" name="ptns_std_prfl" checked="checked">Todos
                        </label>
                    </div>
                </div>
                <!-- Cuerpo -->
                <div class="panel-body">
                	
                    <center>
                    	<table class="table table-bordered table-hover" id="tbl_lst_pntlls"></table>
                    </center>
                    
                </div>
                <!-- Pie de P�gina -->
                <div class="panel-footer"></div>
            </div>
            
        <!-- Fin Contenedor Inferior -->
        </div>
    
    <!-- Fin Contenedor -->
 	</div>
        
    <!-- Modal Pantalla -->
    <div id="mdl_pnt" class="modal fade" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
               		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                	<h4 id="tqt_mdl_pntll" class="modal-title"></h4>
                </div>
               	<div class="modal-body">
                           
                    <!-- Nav tabs -->
                    <ul id="pestanas" class="nav nav-tabs" role="tablist">
                        <li role="presentation" id="tag_inicio" class="active"><a href="#inicio" aria-controls="inicio" role="tab" data-toggle="tab"><h5>Pantalla</h5></a></li>
                        <li role="presentation" id="tag_maxpoint"><a href="#id_acc_pnt" aria-controls="id_acc_pnt" role="tab" data-toggle="tab"><h5>Accesos Pantalla</h5></a></li>
                    </ul>
                    
                    <!-- Tab panes -->
                    <div id="pst_accs" class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="inicio">
                    	<br/>
                    
                        <div class="row">
                            <div class="col-md-9"></div>
                            <div class="col-xs-12 col-md-2">
                                <div class="btn-group">
                                    <h5 class="text-right"><b>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" id="pntll_std_id"></b></h5>
                                </div>
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                         <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-xs-12 col-md-2"><h6><b>Tipo de Pantalla</b></h6></div>
                            <div class="col-xs-12 col-md-8">
                                <div id="pcn_tp_dmnstrcn" class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default active" id="tp_Admin" onclick="fn_seleccionarMaxPointMenu(); fn_cargarOrdenMenuModulo('Admi');">
                                    <input inputmode="none"  type="radio" id="pcn_tp_adm" name="tp_dmnstrcn" autocomplete="off" checked> Men&uacute; MaxPoint
                                    </label>
                                    <label class="btn btn-default" id="tp_Menu"  onclick="fn_seleccionarMaxPointSubMenu(); fn_cargarOrdenMenuModulo('Menu');">
                                    <input inputmode="none"  type="radio" id="pcn_tp_ope" name="tp_dmnstrcn" autocomplete="off"> SubMen&uacute; MaxPoint
                                    </label>
                                    <label class="btn btn-default" id="tp_Funciones" onclick="fn_seleccionarFuncionesGerente();">
                                    <input inputmode="none"  type="radio" id="pcn_tp_ope" name="tp_dmnstrcn" autocomplete="off"> Funciones Gerente
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-xs-12 col-md-2"><h6><b>Nombre MaxPoint</b></h6></div>
                            <div class="col-xs-12 col-md-8">
                                <div class="form-group">
                                    <input inputmode="none"  type="text" class="form-control" id="pntll_nmbr_mstrr" placeholder="Descripci&oacute;n Pantalla">
                                </div>
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-xs-12 col-md-2"><h6><b>Imagen</b></h6></div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <select id="pnt_img_mn" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-md-5"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-xs-12 col-md-2"><h6><b>Configuraci&oacute;n</b></h6></div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <select id="pnt_cnfg" class="form-control" onchange="fn_verificarConfiguracion();">
                                        <option value="0">NINGUNO</option>
                                        <option value="1">M&Oacute;DULO</option>
                                        <option value="2">SUBM&Oacute;DULO</option>
                                        <option value="3">INICIO</option>
                                        <option value="4">PANTALLA</option>
                                        <option value="5">SUBPANTALLA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-xs-6 col-md-4"><h6><b>Predecesor</b></h6></div>
                            <div class="col-xs-6 col-md-4"><h6><b>Sub Nivel</b></h6></div>
                            <div class="col-md-1"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <select id="pnt_prdcsr" onchange="fn_cargarOrdenMenu();" class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <select id="pnt_sb_prdcsr" onchange="fn_calcularSubNivel();" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-xs-12 col-md-2"><h6><b>Orden Men&uacute;</b></h6></div>
                            <div class="col-xs-12 col-md-4">
                                <input inputmode="none"  type="text" class="form-control" id="pntll_rdn_mn" placeholder="Orden Men&uacute;">
                            </div>
                            <div class="col-md-5"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-xs-6 col-md-4"><h6><b>Carpeta</b></h6></div>
                            <div class="col-xs-6 col-md-4"><h6><b>Formulario</b></h6></div>
                            <div class="col-md-1"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <input inputmode="none"  type="text" class="form-control" id="pntll_crpt" placeholder="Directorio del Archivo">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <input inputmode="none"  type="text" class="form-control" id="pntll_frmlr" placeholder="Extensi&oacute;n del Archivo">
                                </div>
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                        
                    </div>
                    
                    <!-- Accesos Pos -->
                    <div role="tabpanel" class="tab-pane active" id="id_acc_pnt">
                    	<br/>
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-xs-12 col-md-6"><h6><b>Accesos:</b></h6></div>
                            <div class="col-md-2 text-right">Marcar Todos &nbsp <input inputmode="none"  id="chck_acc_tds" name="chck_acc_tds" onclick="fn_seleccionarTodosAccesos();" value="1" type="checkbox"></div>
                            <div class="col-md-2"></div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-xs-12 col-md-8">
                                <!-- Carga Locales -->
                                <div style="height: 260px; overflow-y: auto;">
                                    <div id="lst_acc_pnt" class="list-group"></div>
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                        </div>
                    </div>
                    
                    <!-- Fin Tags -->
                    </div>
                    
                </div>
                <div id="btn_pcn_pntll" class="modal-footer"></div>
            </div>
        </div>
    </div>
    
    
    <input inputmode="none"  type="hidden" id="hdn_pnt_id" value=""/>
    
    <!-- JavaScript -->
    <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
    <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
    <script language="javascript1.1"  src="../../js/alertify.js"></script>
	<script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
	<script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>
    <script type="text/javascript" src="../../js/ajax_admpantalla.js"></script>

</body>
</html>