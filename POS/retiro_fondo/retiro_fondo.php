<?php
session_start();

///////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jose Fernandez////////////////////
///////DESCRIPCION: Pantalla de Reportes///////////////////
///////TABLAS INVOLUCRADAS:////////////////////////////////
///////FECHA CREACION: 20/08/2014//////////////////////////
///////////////////////////////////////////////////////////
///////MODIFICADO POR: Christian Pinto ////////////////////
///////DESCRIPCION: Cierre Periodo abierto mas de un día //
///////FECHA MODIFICACIÓN: 07/07/2016 /////////////////////
///////////////////////////////////////////////////////////  

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Retiro de Fondo Asignado</title>
    
    <!-- Llamada a los Estilos --> 
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.css" />   
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css"/>
    <link rel="StyleSheet" href="../css/resumenVentas.css" type="text/css"/>
    <link rel="StyleSheet" type="text/css" href="../css/retiro_fondo.css" />   
    <link rel="stylesheet" href="../css/alertify.core.css" />
    <link rel="stylesheet" href="../css/alertify.default.css" /> 
    
    
    <!-- Llamada a los Ajax -->
    <script src="../js/jquery1.11.1.js"></script>     
	<script src="../js/jquery-ui.js"></script>
    <script src="../bootstrap/js/bootstrap.js"></script>
    <script language="javascript1.1"  src="../js/alertify.js"></script>      
    <script src="../js/moment.js"></script>
    <script src="../js/teclado_credenciales.js"></script> 
    <script src="../js/ajax_retiro_fondo.js"></script> 
    <script src="../js/ajax_api_impresion.js"></script>

  

</head>
<body>

<?php 
	$usr_nombre = $_SESSION['nombre'];
	$moneda     = $_SESSION['simboloMoneda'];
        $bloqueado  = $_SESSION['bloqueoacceso'];
        $banderaDesasignar = $_GET['bandera'];
?>

<input inputmode="none"  type="hidden" id="est_id"  value="<?php echo $_SESSION['estacionId']; ?>"/>
<input inputmode="none"  type="hidden" id="usrrid"  value="<?php echo $_SESSION['usuarioId']; ?>"/>
<input inputmode="none"  type="hidden" id="dirIp"  value="<?php echo $_SESSION['direccionIp']; ?>"/>
<input inputmode="none"  type="hidden" id="moneda"  value="<?php echo $moneda; ?>"/>
<input inputmode="none"  type="hidden" id="banderaCierrePeriodo"  value="<?php echo $_SESSION['sesionbandera']; ?>"/>
<input inputmode="none"  type="hidden" id="txt_bloqueado" value="<?php echo $bloqueado ?>"/>
<input inputmode="none"  type="hidden" id="txt_banderaDesasignar" value="<?php echo $banderaDesasignar ?>"/>

<div id="cntndr_pntll_rsmn_vnts" class="cntndr_pntll_rsmn_vnts">
    	
        <div id="cntndr_cbcr_rsmn_vnts" class="row cntndr_cbcr_rsmn_vnts small">
            <br/>
            <div class="row sn_mrgn_pdng">
                <div class="col-md-1"><b>Periodo:</b></div>
                <div id="tqt_fch_prd" class="col-md-8"></div>
                <div class="col-md-1"><b>Fecha:</b></div>
                <div id="tqt_fch_ctl" class="col-md-2"></div>
            </div>
            <br/>
            <div class="row sn_mrgn_pdng">
                <div class="col-md-1"><b>Usuario:</b></div>
				<div class="col-md-2"><?php echo $usr_nombre; ?></div>
            </div>
        </div>
        
        <!-- Contenedor Detalle -->
        <div style="background: #fff; width: 1024; height:610px; " align="center">            
            <div id="cntndr_dtll_rsmn_vnts" class="row cntndr_dtll_rsmn_vnts"> <br/>
                <div class="col-md-6 text-left"><h5><b>Retiro de Fondo Asignado:</b></h5></div>
                <div id="cntndr_dtll_rsmn_vnts" class="row sn_mrgn_pdng">
                
                    <!-- Detalle  -->
                    <div class="col-md-12">
                        <table id="tbl_detalle_Retiro" class="table table-bordered small">
                            <tr class="active">
                            	<th class="text-center">Fondo <br />Asignado</th>
                                <th class="text-center">Asignado por <br />(Administrador)</th>
                                <th class="text-center">Cajero</th>                                                                
                                <th class="text-center">Fondo Confirmado <br />(Cajero/a)</th>
                                <th class="text-center">Fondo a Retirar</th>
                                <th class="text-center">Retirado por <br />(Administrador)</th>
                                <th class="text-center">Fecha de Retiro</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </table> 
                        
                        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                        <div align="center">
                        <table>
                            <tr>                	
                                <td><input inputmode="none"  class="btn btn-primary" id="btn_cash" opcion="cash" style="width:200px; height:70px; text-align:center" type="button" onclick="fn_validaAdmin();" value="Retirar Fondo"/>                    
                                </td>
                            </tr>               
                        </table>                   
                       </div>
                    </div>
    
                </div>
                
            </div>
        </div> 
 
<div id="credencialesAdmin" style="min-height:87px">
  <div class="preguntasTitulo">Ingrese Credenciales del Administrador</div>
  <div class="anulacionesSeparador">
       <div class="anulacionesInput"><span><img src="../imagenes/admin_resources/users_two_48.png" height="55px" width="55px"></span> &nbsp;
       	<input inputmode="none"  type="password" id="usr_claveAdmin" style="width: 380px; font-size: 20px;"/>				
  	   </div>       
  </div>
  </div>
 
 <div id="credencialesAdminteclado" style="left:35%; top:450px; position:absolute; display:block; z-index:99999;">
  	<table id="tabla_credencialesAdmin" align="center">
  		<tr>
        	<td><button class='btnVirtual' onClick="fn_agregarCaracterNum(usr_claveAdmin,7)">7</button></td>
            <td><button class='btnVirtual' onClick="fn_agregarCaracterNum(usr_claveAdmin,8)">8</button></td>
            <td><button class='btnVirtual' onClick="fn_agregarCaracterNum(usr_claveAdmin,9)">9</button></td>
            <td><button class='btnVirtualOKpq' onClick="fn_eliminarNumero(usr_claveAdmin);">&larr;</button></td>
        </tr>
        <tr>
        	<td><button class='btnVirtual' onClick="fn_agregarCaracterNum(usr_claveAdmin,4)">4</button></td>
            <td><button class='btnVirtual' onClick="fn_agregarCaracterNum(usr_claveAdmin,5)">5</button></td>
            <td><button class='btnVirtual' onClick="fn_agregarCaracterNum(usr_claveAdmin,6)">6</button></td>
            <td><button class='btnVirtualOKpq' onClick="fn_eliminarTodo(usr_claveAdmin);">&lArr;</button></td>
        </tr>
        <tr>
        	<td><button class='btnVirtual' onClick="fn_agregarCaracterNum(usr_claveAdmin,1)">1</button></td>
            <td><button class='btnVirtual' onClick="fn_agregarCaracterNum(usr_claveAdmin,2)">2</button></td>
            <td><button class='btnVirtual' onClick="fn_agregarCaracterNum(usr_claveAdmin,3)">3</button></td>
            <td><button class='btnVirtualOKpq' onClick="fn_retirofondo();">OK</button></td>
        </tr>
         <tr>
        	<td><button class='btnVirtual' onClick="fn_agregarCaracterNum(usr_claveAdmin,0)">0</button></td>
            <td colspan="4"><button class='btnVirtualCancelar' onClick="fn_cerrarValidaAdmin();">Cancelar</button></td>           
        </tr>
    </table> 
</div>

<div id="numPadAdmin"></div> 
   
<!-- Menu Opciones -->
<div class="cnt_mn_nfrr_btns">            	
	<input inputmode="none"  type="button" id="boton_sidr" value="Menu" class="boton_Accion" onclick="" style="margin-right: 14px;"/>         
</div>

<!-- SubMenu Opciones -->
<div id="id_menu_desplegable" class="menu_desplegable">
    <div id="id_modal_opciones_drc" class="modal_opciones_drc">  
        <button class="boton_Opcion" id='funcionesGerente' onclick='fn_funcionesGerente()'>Funciones Gerente</button> 
        <button class="boton_Opcion" id="nuevaorden" onclick="fn_TomaPedido()">Orden Pedido</button>
        <button class="boton_Opcion" onclick="fn_salirSistema()">Salir Sistema</button>  
    </div>
</div>

<div id="cntFormulario"></div>

</body>
</html>