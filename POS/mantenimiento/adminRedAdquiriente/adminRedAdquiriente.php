<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: ADMINISTRACION DE RED ADQUIRIENTE, LISTADO, AGREGAR Y MODIFICAR     ////////////
////////////////TABLAS: Red_Adquiriente ////////////////////////////////////////////////////////////////
////////FECHA CREACION: 13/07/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	session_start();
	include_once"../../system/conexion/clase_sql.php";
	include_once"../../clases/clase_seguridades.php";
	include_once"../../clases/clase_menu.php";
	include_once"../../seguridades/seguridad.inc";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
<title>Configuraci&oacute;n Red Adquiriente</title>
	<!---------------------------------------------------
                           ESTILOS
    ----------------------------------------------------->
<link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
<link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
<link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
<link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
<link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />

		<!---------------------------------------------------
                           JSQUERY
    	----------------------------------------------------->
<script src="../../js/jquery1.11.1.js"></script>
<script language="javascript1.1" type="text/javascript" src="../../js/ajax_datatables.js"></script>
<script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
<script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
<script language="javascript1.1"  src="../../js/alertify.js"></script>
<script type="text/javascript" src="../../js/ajax_adminredadquiriente.js"></script>
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
      <!--<li>
        <input inputmode="none"  id="btn_cancelar" type="button" onclick="fn_cancelar()" class="botonhabilitado" value="Cancelar"/>
      </li>-->
    </ul>
  </div>
  <div class="tituloPantalla">
    <h1>RED ADQUIRIENTE</h1>
  </div>
</div>
</br>


<div class="contenedor">
        
        <div class="inferior">
                        
            </br>
    
            <div class="panel panel-default" id="botonesTodos">
				<div class="panel-heading">
                    <div class="row">
                    		<div class="col-sm-8"><h5>Lista de Red Adquiriente</h5>
                            	<div id="opciones_estados" class="btn-group" data-toggle="buttons">
                                                                        
                                    <label class="btn btn-default btn-sm active" onclick="fn_OpcionSeleccionada('Activos');"><input inputmode="none"  id="opt_Activos" type="radio" value="Activos" checked="checked" autocomplete="off" name="estados">Activos</label>
                                    
                                    <label class="btn btn-default btn-sm" onclick="fn_OpcionSeleccionada('Inactivos');"><input inputmode="none"  id="opt_Inactivos" type="radio" value="Inactivos" autocomplete="off" name="estados">Inactivos</label>
                                    
                                    <label class="btn btn-default btn-sm" onclick="fn_OpcionSeleccionada('Todos');"><input inputmode="none"  id="opt_Todos" type="radio" value="Todos" autocomplete="off" name="estados">Todos</label>
                            	</div>
                            </div>
                            
                        </div>
                    </div>                         
            </div>
           
         	<div id="redadquiriente">
            	<table class="table table-bordered table-hover" id="tabla_redadquiriente" border="1" cellpadding="1" cellspacing="0">
      			</table>
            </div>
               
        <!-- Fin Contenedor Inferior -->
       </div>
    
    <!-- Fin Contenedor -->
 	</div>

<!-------------------------------------INICIO MODAL NUEVO BOOTSTRAP---------------------------------------------->
<div class="modal fade" id="ModalNuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header panel-footer">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
        <h4 class="modal-title" id="titulomodalNuevo">Nuevo Red Adquiriente: </b></h4>
      </div>
       </br>
      <div align="right" class="col-xs-12 col-x"> Est&aacute; Activo?
                <input inputmode="none"  type="checkbox" id="option" checked="checked">
              </div>
              </br>
              </br>
      <div class="modal-body">       
        
              <!--<div class="row">-->
                <div class="row">
                  <div class="col-xs-1"></div>
                  <div class="col-xs-4">
                    <h5>Codigo Red Adquiriente:</h5>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group" class="col-xs-1"> 
                        <input inputmode="none"  style="text-align:center;" class="form-control" id="txt_codreadquiriente" onkeypress="return fn_numeros(event);"/>                    	
                    </div>
                  </div>
                  <div class="col-xs-1"></div>
                </div>
                
                <div class="row">
                  <div class="col-xs-1"></div>
                  <div class="col-xs-4">
                    <h5>Descripci&oacute;n:</h5>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group" class="col-xs-1"> 
                        <input inputmode="none"  style="text-align:center;" class="form-control" maxlength="100" id="txt_descripcion"/>                    	
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
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header panel-footer">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="titulomodalMod"> </b></h4>
      </div>
       </br>
      <div align="right" class="col-xs-12 col-x"> Est&aacute; Activo?
                <input inputmode="none"  type="checkbox" id="optionmod" >
              </div>
              </br>
              </br>
      <div class="modal-body">       
        
             <!--<div class="row">-->
              	
               <div class="row">
                  <div class="col-xs-1"></div>
                  <div class="col-xs-4">
                    <h5>Codigo Red Adquiriente:</h5>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group" class="col-xs-1"> 
                        <input inputmode="none"  style="text-align:center;" class="form-control" id="txt_codredadquirienteMod" onkeypress="return fn_numeros(event);"/>                    	
                    </div>
                  </div>
                  <div class="col-xs-1"></div>
                </div>
                
                <div class="row">
                  <div class="col-xs-1"></div>
                  <div class="col-xs-4">
                    <h5>Descripci&oacute;n:</h5>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group" class="col-xs-1"> 
                        <input inputmode="none"  style="text-align:center;" class="form-control" maxlength="100" id="txt_descripcionMod"/>                    	
                    </div>
                  </div>
                  <div class="col-xs-1"></div>
                </div>
                
                                
            <!--</div>-->

      </div>
      <div class="modal-footer panel-footer">
      <button type="button" class="btn btn-primary" onclick="fn_accionar('Grabar')" >Aceptar</button>
       <button type="button" class="btn btn-default" onclick="" data-dismiss="modal">Cancelar</button>
        
      </div>
    </div>
  </div>
</div>
<!-------------------------------------FIN MODAL MODIFICAR BOOTSTRAP---------------------------------------------->

<input inputmode="none"  type="hidden" id="txt_idredadquiriente"/>
<input inputmode="none"  type="hidden" id="txt_cadenahid"/>

</body>
</html>