<?php
session_start();

///////////////////////////////////////////////////////////////////////////////
//FECHA CREACION: 22/02/2016
//DESARROLLADOR: Daniel Llerena
//DESCRIPCION: Mantenimiento Tipo Facturacion
//FECHA ULTIMA MODIFICACION: 
///USUARIO QUE MODIFICO: 
//DECRIPCION ULTIMO CAMBIO: 
///////////////////////////////////////////////////////////////////////////////
	
	include_once"../../system/conexion/clase_sql.php";
	include_once"../../clases/clase_seguridades.php";
	include_once"../../clases/clase_menu.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" /> 
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
    <title>Administraci&oacute;n Men&uacute;</title>
    
    <!---------------------------------------------------
                           ESTILOS
    ----------------------------------------------------->
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" /> 
    <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/> 
    <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />    
    
</head>

<body style="overflow-y: auto;">
	
    <input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
    <input inputmode="none"  id="cadenas" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>   
    <input inputmode="none"  id="restaurante" type="hidden" value="<?php echo $_SESSION['rstId']; ?>"/>

<div class="superior">
    <div class="menu" style="width: 466px;" align="center">
        <ul>
            <li><input inputmode="none"  id="btn_agregar" type="button" onclick="fn_agregar()" class="botonhabilitado" value="Agregar"/></li>                 
        </ul>        
    </div> 
    <div class="tituloPantalla">
        <h1>TIPO FACTURACI&Oacute;N</h1>
    </div>   
</div>
    
<div class="contenedor">
    <div class="inferior">  

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-8"><h5>
                        <div id='opciones_estado' class="btn-group" data-toggle="buttons">                        
                            <label id="opt_Activos" class="btn btn-default btn-sm active" onclick="fn_CargaTipoFacturacionXestado('Activo');">
                                <input inputmode="none"  type="radio" name="options" id="opt_Activos" autocomplete="off" value="1" />Activos
                            </label>
                            <label class="btn btn-default btn-sm" onclick="fn_CargaTipoFacturacionXestado('Inactivo');">
                                <input inputmode="none"  type="radio" name="options" id="opt_Inactivos" autocomplete="off" value="2"/>Inactivos
                            </label>
                            <label id='opciones_1' class="btn btn-default btn-sm " onclick="fn_CargaTipoFacturacion();">
                                <input inputmode="none"  type="radio" name="options" id="opt_Todos" autocomplete="off" value="0"/>Todos
                            </label>
                         </div>                                   
                    </h5></div>									
                </div> 
            </div>
            <div class="panel-body">
                <div id="aplicacion" class="center-block">    
                    <table id="tabladetalletipofacturacion" class="table table-bordered table-hover"></table>                          
                </div>
            </div>
            <div class="panel-footer"></div>
        </div>  

        <!-- Modal Modificar -->
        <div class="modal fade" id="modalmodificar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header panel-footer">
                        <button type="button" class="close" onclick="fn_cancelar()" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">
                            <label style="font-size:13px; color:#666; font-style:normal"  id="nombreMenu2"></label>
                        </h4>
                    </div>
                    <div class="modal-body">         
                        <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" id="check_activo" /></h6></div>            
                        <div class="row">
                            <div class="col-xs-4 text-right"><h5>Tipo de Facturaci&oacute;n:</h5></div>
                            <div class="col-xs-8">
                                <div class="form-group">
                                    <input inputmode="none"  maxlength="50" type="text" class="form-control" name="tipofacturacion_descripcionModificar" id="tipofacturacion_descripcionModificar" onkeyup="aMays(event, this)" onblur="aMays(event, this)"/>
                                </div>
                            </div>
                        </div>  

                        <div class="row">
                            <div class="col-xs-4 text-right"><h5>URL de Impresi&oacute;n:</h5></div>
                            <div class="col-xs-8">
                                <div class="form-group">
                                    <input inputmode="none"  maxlength="200" type="text" class="form-control" name="ruta_archivoimpresionModificar" id="ruta_archivoimpresionModificar"  />
                                </div>
                            </div>
                        </div>          
                    </div>          
                    <div class="modal-footer panel-footer">
                        <button type="button" class="btn btn-primary" onclick="fn_modificarTipoFacturacion();" >Aceptar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>        
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Nuevo -->
        <div class="modal fade" id="modalnuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header panel-footer">
                        <button type="button" class="close" onclick="fn_cancelar()"  data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel1">Nuevo Tipo de Facturaci&oacute;n:</h4>
                    </div>
                    <div class="modal-body">               
                        <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" checked="checked" disabled id="check_activonuevo"/></h6></div>          
                        <div class="row">
                            <div class="col-xs-4 text-right"><h5>Tipo de Facturaci&oacute;n:</h5></div>
                            <div class="col-xs-8">
                                <div class="form-group">
                                    <input inputmode="none"  maxlength="50" type="text" class="form-control" name="tipofacturacion_descripcion" id="tipofacturacion_descripcion"  onkeyup="aMays(event, this)" onblur="aMays(event, this)"/>
                                </div>
                            </div>
                        </div>  
                        <div class="row">
                            <div class="col-xs-4 text-right"><h5>URL de Impresi&oacute;n:</h5></div>
                            <div class="col-xs-8">
                                <div class="form-group">
                                    <input inputmode="none"  maxlength="200" type="text" class="form-control" name="ruta_archivoimpresion" id="ruta_archivoimpresion" />
                                </div>
                            </div>
                        </div>                      
                    </div>
                    <div class="modal-footer panel-footer">
                        <button type="button" class="btn btn-primary" onclick="fn_guardaNuevoTipoFacturacion();" >Aceptar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>        
                    </div>
                </div>
            </div>
        </div>
     		
    </div>
</div>
        
    <!---------------------------------------------------
                       JSQUERY
    ----------------------------------------------------->
    <script src="../../js/jquery1.11.1.js"></script>
    <script src="../../bootstrap/js/bootstrap.js"></script>
    <script src="../../js/jquery-ui.js"></script>
    <script language="javascript1.1"  src="../../js/alertify.js"></script>
    <script type="text/javascript" src="../../js/ajax_tipofacturacion.js"></script>
    <script src="../../js/ajax_datatables.js"></script>
    <script src="../../bootstrap/js/bootstrap-dataTables.js"></script> 
    
    <input inputmode="none"  type="hidden" id="IDTipoFacturacion"/>
   
</body>
</html>