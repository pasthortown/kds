<?php
///////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO//////////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE COLECCIONES DE DATOS, CREAR MODIFICAR /////////
////////////////TABLAS: Colecciones Varias ////////////////////////////////////////
////////FECHA CREACION: 16/03/2016/////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////
	
	session_start();
	include_once("../../system/conexion/clase_sql.php");
	include_once("../../clases/clase_seguridades.php");
	include_once("../../clases/clase_adminColeccionesDeDatos.php");
	include_once("../../seguridades/seguridad.inc");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
<title>Impuestos</title>
	<!---------------------------------------------------
                           ESTILOS
    ----------------------------------------------------->
<link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
<link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
<link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
<link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
<link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="../../css/collapser.css" />
		<!---------------------------------------------------
                           JSQUERY
    	----------------------------------------------------->
<script src="../../js/jquery1.11.1.js"></script>
<script language="javascript1.1" type="text/javascript" src="../../js/ajax_datatables.js"></script>
<script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>  
<script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
<script type="text/javascript" src="../../js/jquery.collapser.js"></script>
<!--<script type="text/javascript" src="../../js/jquery.collapser.min.js"></script>-->

<script language="javascript1.1"  src="../../js/alertify.js"></script>
<script type="text/javascript" src="../../js/ajax_adminColeccionesDeDatos.js"></script>
<script language="javascript1.1" type="text/javascript" src="../../js/js_validaciones.js"></script>

</head>

<body>
<input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
<div class="superior">
  <div class="menu" style="width: 500px;" align="center">
    <ul>
      <li>
        <input inputmode="none"  id="btn_agregar" type="button"  onclick="fn_accionar('Nuevo')" class="botonhabilitado" value="Agregar"/>
      </li>
     <!-- <li>
        <input inputmode="none"  id="btn_cancelar" type="button" onclick="fn_cancelar()" class="botonhabilitado" value="Cancelar"/>
      </li>-->
    </ul>
  </div>
  <div class="tituloPantalla">
    <h1>POL&Iacute;TICAS</h1>
  </div>
</div>
</br>


<div class="contenedor">
        
        <div class="inferior">
            
        <div class="panel panel-default">
				<div class="panel-heading">
                    <div class="row">
                    		<div class="col-sm-8"><h5>Lista de Pol&iacute;ticas</h5>
                            	<!--<div id="opciones_estados" class="btn-group" data-toggle="buttons">
                                                                        
                                    <label class="btn btn-default btn-sm active" onclick="fn_cargarImpuestos('Activo');"><input inputmode="none"  id="opt_Activos" type="radio" value="Activos" checked="checked" autocomplete="off" name="estados">Activos</label>
                                    
                                    <label class="btn btn-default btn-sm" onclick="fn_cargarImpuestos('Inactivo');"><input inputmode="none"  id="opt_Inactivos" type="radio" value="Inactivos" autocomplete="off" name="estados">Inactivos</label>
                                    
                                    <label class="btn btn-default btn-sm " onclick="fn_cargarImpuestos('Todos');"><input inputmode="none"  id="opt_Todos" type="radio" value="Todos" autocomplete="off" name="estados">Todos</label>
                            	</div>-->
                            </div>
                            
                        </div>
                    </div>
                    
                                        
            </div>
            
            </div>
                                
            <div role="tabpanel">
              <!-- Nav tabs -->
              <ul id="pestanas_colecciones" class="nav nav-tabs" role="tablist"> 
              </ul>
              
              <div id="tab_contenido" class="tab-content">
              
              </div>
			</div>
        <!-- Fin Contenedor Inferior -->
       </div>
    
    <!-- Fin Contenedor -->
 	</div>
                    
<!-------------------------------------MODAL NUEVA CABECERA COLECCIONES---------------------------------------------->
<div class="modal fade" id="ModalNuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header panel-footer">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="titulomodalNuevo"> </h4>
      </div>
      <br>
      <div align="right" class="col-xs-12 col-x"> Est&aacute; Activo?
                <input inputmode="none"  type="checkbox" id="option_estado" checked="checked">
              </div>
              </br>
           
      <div class="modal-body">       
        
              <!--<div class="row">-->
                      
                <div class="row">
                  <div class="col-xs-1"></div>
                  <div class="col-xs-3">
                    <h5>Descripci&oacute;n:</h5>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group" class="col-xs-1">   
                        <input inputmode="none"  style="text-align:center; "  onKeyUp="this.value=this.value.toUpperCase();"class="form-control" maxlength="50"  id="txt_descripcion"/>                    	
                    </div>
                  </div>
                  <div class="col-xs-1"></div>
                </div>
                
                <div class="row">
                  <div class="col-xs-1"></div>
                  <div class="col-xs-3">
                    <h5>ID Integraci&oacute;n:</h5>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group" class="col-xs-1">   
                        <input inputmode="none"  style="text-align:center" class="form-control" maxlength="50" id="txt_idintegracion"/>                    	
                    </div>
                  </div>
                  <div class="col-xs-1"></div>
                </div>
                
                <div class="row">
                  <div class="col-xs-1"></div>
                  <div class="col-xs-3">
                    <h5>ID Descripci&oacute;n:</h5>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group" class="col-xs-1">   
                        <input inputmode="none"  style="text-align:center" class="form-control" maxlength="50" id="txt_iddescripcion"/>                    	
                    </div>
                  </div>
                  <div class="col-xs-1"></div>
                </div>
                
                <div class="row">
                  <div class="col-xs-1"></div>
                  
                  <div class="col-xs-3">
                    <h5>Estatus 1:</h5>
                  </div>
                  <div class="col-xs-2">
                  	<div class="form-group" class="col-xs-1">
                  		<input inputmode="none"  style="text-align:center" class="form-control" maxlength="3" id="txt_estatus1"/>
                    </div> 
                  </div>
                  
                  <div class="col-xs-2">
                    <h5>Estatus 2:</h5>
                  </div>
                  <div class="col-xs-2">
                  	<div class="form-group" class="col-xs-1">
                  		<input inputmode="none"  style="text-align:center" class="form-control" maxlength="3" id="txt_estatus2"/>
                    </div>
                  </div>
                  
                  <div class="col-xs-1"></div>
                </div>
                
                <div class="row">
                  <div class="col-xs-1"></div>
                  
                  <div class="col-xs-4">
                    <h5>Configuraci&oacute;n:</h5>
                  </div>
                  <div class="col-xs-1">
                  	<div class="form-group" class="col-xs-1">
                  		<input inputmode="none"  type="checkbox" id="option_configuracion">
                    </div>
                  </div>
                  
                  <div class="col-xs-2">
                    <h5>Reporte:</h5>
                  </div>
                  <div class="col-xs-1">
                  	<div class="form-group" class="col-xs-1">
                  		<input inputmode="none"  type="checkbox" id="option_reporte">
                    </div>
                  </div>
 
                  <div class="col-xs-1"></div>
                  
                </div>
                
                 <div class="row">
                  <div class="col-xs-1"></div>
                  
                  <div class="col-xs-4">
                    <h5>Repetir Configuraci&oacute;n:</h5>
                  </div>
                  <div class="col-xs-1">
                  	<div class="form-group" class="col-xs-1">
                  		<input inputmode="none"  type="checkbox" id="option_rconfiguracion">
                    </div>
                  </div>
				  
                   <div class="col-xs-2">
                    <h5>Cubo:</h5>
                  </div>
                  <div class="col-xs-1">
                  	<div class="form-group" class="col-xs-1">
                  		<input inputmode="none"  type="checkbox" id="option_cubo">
                    </div>
                  </div>
                  	
                  <div class="col-xs-1"></div>
                </div>                
           <!-- </div>-->
      </div>
      <div class="modal-footer panel-footer">
      <button type="button" class="btn btn-primary" onclick="fn_accionar('Grabar')" >Aceptar</button>
       <button type="button" class="btn btn-default" onclick="" data-dismiss="modal">Cancelar</button>
        
      </div>
    </div>
  </div>
</div>

<!-------------------------------------FIN MODAL MODIFICAR BOOTSTRAP---------------------------------------------->

<div class="modal fade" id="ModalMod" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header panel-footer">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="titulomodalMod"> <b></h4>
      </div>
      <br>
      <div align="right" class="col-xs-12 col-x"> Est&aacute; Activo?
                <input inputmode="none"  type="checkbox" id="optionmod">
              </div>
              </br>
              </br>
      <div class="modal-body">       
        
             <!-- <div class="row">-->
                   
                <div class="row">
                  <div class="col-xs-1"></div>
                  <div class="col-xs-4">
                    <h5>Descripci&oacute;n:</h5>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group" class="col-xs-1"> 
                        <input inputmode="none"  style="text-align:center" class="form-control" maxlength="50" id="txt_descripcionMod"/>                    	
                    </div>
                  </div>
                  <div class="col-xs-1"></div>
                </div>
                
                <div class="row">
                  <div class="col-xs-1"></div>
                  <div class="col-xs-4">
                    <h5>Porcentaje:</h5>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group" class="col-xs-1">
                        <input inputmode="none"  style="text-align:center" class="form-control" maxlength="10" id="txt_porcentajeMod" onkeypress='return NumCheck(event,txt_porcentajeMod)'/>                    	
                    </div>
                  </div>
                  <div class="col-xs-1"></div>
                </div>
                
                <div class="row">
                  <div class="col-xs-1"></div>
                  <div class="col-xs-4">
                    <h5>FE C&oacute;digo:</h5>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group" class="col-xs-1">
                        <input inputmode="none"  style="text-align:center" class="form-control" maxlength="10" id="txt_feCodigoMod" onkeypress='return NumCheck(event,txt_porcentaje)'/>                    	
                    </div>
                  </div>
                  <div class="col-xs-1"></div>
                </div>
                
                <div class="row">
                  <div class="col-xs-1"></div>
                  <div class="col-xs-4">
                    <h5>FE C&oacute;digo Porcentaje:</h5>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group" class="col-xs-1">
                        <input inputmode="none"  style="text-align:center" class="form-control" maxlength="10" id="txt_feCodigoPorcentajeMod" onkeypress='return NumCheck(event,txt_porcentaje)'/>                    	
                    </div>
                  </div>
                  <div class="col-xs-1"></div>
                </div>
       <!--     </div>-->
      </div>
      <div class="modal-footer panel-footer">
      <button type="button" class="btn btn-primary" onclick="fn_accionar('Grabar')" >Aceptar</button>
       <button type="button" class="btn btn-default" onclick="" data-dismiss="modal">Cancelar</button>
        
      </div>
    </div>
  </div>
</div>
<!-------------------------------------FIN MODAL MODIFICAR BOOTSTRAP---------------------------------------------->

<input inputmode="none"  type="hidden" id="txt_nombreTabla"/>
<input inputmode="none"  type="hidden" id="idPais"/>
<input inputmode="none"  type="hidden" id="txt_filarestaurante"/>
<input inputmode="none"  type="hidden" id="txt_cadenahid"/>

</body>
</html>