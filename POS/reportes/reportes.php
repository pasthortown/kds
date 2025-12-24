<?php
session_start();

///////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jose Fernandez////////////////////
///////DESCRIPCION: Pantalla de Reportes///////////////////
///////TABLAS INVOLUCRADAS:////////////////////////////////
///////FECHA CREACION: 20/08/2014//////////////////////////
/////////////////////////////////////////////////////////// 

//
//Declaracion de variables de sesion
//
$usr_id=$_SESSION['usuarioId'];
$cdn_id=$_SESSION['cadenaId'];
$rst_id=$_SESSION['rstId'];
$est_id=$_SESSION['estacionId'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Reportes</title>
    
    <!-- Llamada a los Estilos -->    
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css"/>	
    <link rel="stylesheet" type="text/css" href="../css/est_modal.css" />
    <link rel="stylesheet" href="../css/alertify.core.css" />
    <link rel="stylesheet" href="../css/alertify.default.css" />    
    <link rel="stylesheet" type="text/css" href="../css/est_botonesbarra.css"/>
    <link rel="StyleSheet" type="text/css" href="../css/est_reportes_caja.css" />
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.css" />
    
    <!-- Llamada a los Ajax -->
    <script type="text/javascript" src="../js/jquery-1.9.1.js"></script>     
	<script src="../js/jquery-ui.js"></script>
    <script src="../bootstrap/js/bootstrap.js"></script>   
    <script type="text/javascript" src="../js/ajax_reportes.js"></script>   
    <script type="text/javascript" src="../js/alertify.js"></script> 
    <script language="javascript1.1"  src="../js/idioma.js"></script> 

</head>
<body>

<div id="contenedorTotal">

    <div id="contenedorIzquierda">
        <div class="botonesLista"  align="center">
            Reportes:
            <br />
        </div>
    
        <div style="margin:70px 40px;" >
        	<table>
            	<tr>                	
            		<td width="400"><input inputmode="none"  class="btn btn-primary" id="btn_cash" opcion="cash" style="width:210px; height:60px; text-align:left" type="button" onclick="fn_validarReporte(this.id);" value="Reporte de CashOut"/>                    
                    </td>
                </tr>                
                <tr>
	                <td width="400"><br /><input inputmode="none"  class="btn btn-primary" style="width:210px; height:60px; text-align:left" type="button" id="btn_ventas" onclick="fn_validarReporte(this.id);" opcion="ventas" value="Reporte de ventas por plu"/>
                    </td>                	
                </tr>  
               <tr>
	                <td width="400"><br /><input inputmode="none"  class="btn btn-primary" style="width:210px; height:60px; text-align:left" type="button" id="btn_transacciones" onclick="fn_validarReporte(this.id);" opcion="transacciones" value="Resumen de Transacciones"/>
                    </td>                	
                </tr>     
                <tr>
	                <td width="400"><br /><input inputmode="none"  class="btn btn-primary" style="width:210px; height:60px; text-align:left" type="button" id="btn_tax" onclick="fn_validarReporte(this.id);" opcion="transacciones" value="Resumen de Impuestos"/>
                    </td>                	
                </tr> 
                <tr>
	                <td width="400"><br /><input inputmode="none"  class="btn btn-primary" style="width:210px; height:60px; text-align:left" type="button" id="btn_anulaciones" onclick="fn_validarReporte(this.id);" opcion="anulaciones" value="Reportes de Anulaciones"/>
                    </td>                	
                </tr>               
            </table>        
        </div>    
    </div>
    
    <div id="contenedorDerecha">   
        <div id="listaPedido">
            <div id="listadoss" >
                <div class="cabeceraOrden">
                    <div id="tituloreporte" class='listaFactura'>&nbsp;&nbsp;</div>                    
                </div>                      
                <table id="tbl_fecha" style="position:fixed; top:160px; width:600px; left:350px;" class="table table-bordered">               
  				<tr>
                	<td class="active" align="center" style="font-size:24px">Fecha Inicial:</td>
                    <td class="active" align="center" style="font-size:24px">Fecha Final:</td>                	
                </tr>
                <tr>
                	<td>
                		<input inputmode="none"  class="form-control" style="font-size:24px" type="text" id="txtfechaI" value="<?php print (date("d/m/Y"))?>" />			
                	</td>
                	<td>
                		<input inputmode="none"  class="form-control" style="font-size:24px" type="text" id="txtfechaF" value="<?php print (date("d/m/Y"))?>"/>	
                	</td>                
                </tr>                
                <tr>
	                <td colspan="2" align="center"><input inputmode="none"  class="btn btn-primary" style="width:180px; height:80px; text-align:center; margin-top:50px; font-size:18px;" type="button" id="lc_reporteDos" onclick="fn_tipoVisualizacion();" value="Generar"/></td>                	
                </tr>  
                </table>                          
            </div>                                                                      
        </div>
    </div>
    
    <div style="clear:both; height:24px;"></div>
    <div class="contenedorInferior">        
        <div id="barraPrincipal">
          <button id="cancelar" class="botonesbarra" onclick="fn_regresar()">Regresar</button>
        </div>
    </div>

</div>

<div id="modal_reportes" title="Qu&eacute; le gustar&iacute;a hacer?">
  <table border="1" width="100%" class="table table-bordered">
	  <tr>
    	<td>
    <input inputmode="none"  type="button" visualizar="ver" class="btn btn-primary" style='height:80px;width:100%;' id="btn_ver"  value="VISUALIZAR" onclick="fn_generarReporte(this.id)"/>              
        </td>
	</tr>  
     <tr>
    <td align="center"><input inputmode="none"  class="btn btn-default" type="button" style='height:60px;width:120px;' onclick="fn_cerrarModal()" id="btn_cancelarInicio"  value="Cancelar"/></td>
    	</tr>
</table>

</div>

</body>
</html>