<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: CREACION DE CLIENTES  //////////////////////////////////////////////////////////
////////////////TABLAS: Cliente ////////////////////////////////////////////////////////////////////////
////////FECHA CREACION: 23/08/2015//////////////////////////////////////////////////////////////////////
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
<title>Clientes</title>
	<!---------------------------------------------------
                           ESTILOS
    ----------------------------------------------------->
<link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
<link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
<link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
<link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
<link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
<!--<link rel="stylesheet" type="text/css" href="../../bootstrap/css/select2.css" />-->
<!--<link rel="stylesheet" type="text/css" href="../../css/select2.css" />-->
<link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
		<!---------------------------------------------------
                           JSQUERY
    	----------------------------------------------------->
<script src="../../js/jquery1.11.1.js"></script>
<script language="javascript1.1" type="text/javascript" src="../../js/ajax_datatables.js"></script>
<!--<script src="../../js/ajax_select2.js"></script>-->
<script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
<script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
<script language="javascript1.1"  src="../../js/alertify.js"></script>
<script type="text/javascript" src="../../js/ajax_adminclientes.js"></script>
<script language="javascript1.1" type="text/javascript" src="../../js/js_validaciones.js"></script>
<script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.js"></script>
<script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
<script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.js"></script>
<script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>


</head>

<body>
<input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
<div class="superior">
  <div class="menu" style="width: 500px;" align="center">
    <ul>
      <li>
        <input inputmode="none"  id="btn_agregar" type="button"  onclick="fn_agregarClienteFormasPago();" class="botonhabilitado" value="Agregar"/>
      </li>
     <!-- <li>
        <input inputmode="none"  id="btn_cancelar" type="button" onclick="fn_cancelar()" class="botonhabilitado" value="Cancelar"/>
      </li>-->
    </ul>
  </div>
  <div class="tituloPantalla">
    <h1>CLIENTES</h1>
  </div>
</div>
</br>


<div class="contenedor">
        
        <div class="inferior">
            
            <div class="panel panel-default">
				<div class="panel-heading">
                    <div class="row">
                    		<div class="col-sm-8"><h5>Lista de Clientes</h5>
                            	<div id="opciones_estados" class="btn-group" data-toggle="buttons">
                                   
                            	</div>
                            </div>
                            
                        </div>
                    </div>
                    
                                        
            </div>
            
         	<div id="divtabla_clientes">
            	<table class="table table-bordered table-hover" id="tablaclientes" border="1" cellpadding="1" cellspacing="0">
      			</table>
            </div>
               
        <!-- Fin Contenedor Inferior -->
       </div>
    
    <!-- Fin Contenedor -->
 	</div>
    
<!-------------------------------------INICIO MODAL NUEVO BOOTSTRAP---------------------------------------------->

<!------------------------------------------------ MODAL MONEDA SIMBOLO ----------------------------------------------->

<div class="modal fade"  data-backdrop="static" id="modalClientes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="titulomodalclientes"></h4>
                                      </div>
                                      </br>
                                          <div class="modal-body">          
                                            <div class="row">
                                            	<div class="col-xs-1"></div>
                                                <div class="col-xs-2"><h5>Nombres:</h5></div>
                                                <div class="col-xs-8">
                                                    <div class="form-group">
                                                        <input inputmode="none"  type="text" id="cli_nombres" onkeyup="aMays(event, this)" class="form-control"  maxlength="100" onkeypress="return fn_letras(event);"/>               
                                                    </div>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                           <div class="row">
                                            	<div class="col-xs-1"></div>
                                                <div class="col-xs-2"><h5>Apellidos:</h5></div>
                                                    <div class="col-xs-8">
                                                        <div class="form-group">
                                                            <input inputmode="none"  type="text" id="cli_apellidos" onkeypress="return fn_letras(event);" onkeyup="aMays(event, this)" class="form-control" maxlength="100"/>                 
                                                        </div>
                                                    </div>
                                                 <div class="col-xs-1"></div>
                                            </div>
                                            
                                            <div class="row">
                                            	<div class="col-xs-1"></div>
                                                <div class="col-xs-2"><h5>Documento:</h5></div>
                                                    <div class="col-xs-4">
                                                        <div class="form-group">
                                                           <select id="sel_tipodocumento" class="form-control" onchange="fn_limpiaDocumento();"></select>
                                                        </div>
                                                    </div>
                                                     <div class="col-xs-4">
                                                        <div class="form-group">
                                                           <input inputmode="none"  type="text" id="cli_documento" onKeyPress="return fn_numerosDocumento(event);"class="form-control" maxlength="13"/>               
                                                        </div>
                                                    </div>
                                                 <div class="col-xs-1"></div>
                                            </div> 
                                            <div class="row">
                                            	<div class="col-xs-1"></div>
                                                <div class="col-xs-2"><h5>Tel&eacute;fono:</h5></div>
                                                    <div class="col-xs-8">
                                                        <div class="form-group">
                                                            <input inputmode="none"  type="text" id="cli_telefono" onKeyPress="return fn_numeros(event);" class="form-control" maxlength="11"/>                        
                                                        </div>
                                                    </div>
                                                 <div class="col-xs-1"></div>
                                            </div>
                                            
                                            <div class="row">
                                            	<div class="col-xs-1"></div>
                                                <div class="col-xs-2"><h5>Ciudad:</h5></div>
                                                    <div class="col-xs-8">
                                                        <div class="form-group">
                                                           <select id="sel_ciudad" class="form-control"></select>
                                                        </div>
                                                    </div>
                                                 <div class="col-xs-1"></div>
                                            </div>
                                            
                                              <div class="row">
                                              	<div class="col-xs-1"></div>
                                                <div class="col-xs-2"><h5>Direcci&oacute;n:</h5></div>
                                                    <div class="col-xs-8">
                                                        <div class="form-group">
                                                            <input inputmode="none"  type="text" id="cli_direccion" class="form-control" maxlength="200"/>                        
                                                        </div>
                                                    </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                                                                         
                                            <div class="row">
                                            	<div class="col-xs-1"></div>
                                                <div class="col-xs-2"><h5>Email:</h5></div>
                                                    <div class="col-xs-8">
                                                        <div class="form-group">
                                                           <input inputmode="none"  type="text" id="cli_email" class="form-control" maxlength="50"/>
                                                        </div>
                                                    </div>
                                                 <div class="col-xs-1"></div>
                                            </div>
                                            
                                                                                                 
                                          </div>
                                      <div class="modal-footer" id="pnl_pcn_btn">
                                       <!-- <button type="button" class="btn btn-primary" onclick="fn_guardarClienteFormasPago();" >Guardar</button>
                                        <button type="button" class="btn btn-default" onclick="" data-dismiss="modal">Cancelar</button>-->       
                                      </div>
                                    </div>
                                  </div>
                                </div>

<!---------------------------------------------- FIN MODAL MONEDA SIMBOLO ---------------------------------------------->  

<input inputmode="none"  type="hidden" id="txt_cliente"/>

</body>
</html>