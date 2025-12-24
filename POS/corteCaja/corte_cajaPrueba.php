<?php
 ////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR JOSE FERNANDEZ//////////////////////////////////////////
////////DESCRIPCION			: PANTALLA DE CORTE DE CAJA//////////////////////////
////////TABLAS				: ARQUEO_CAJA,BILLETE_ESTACION,//////////////////////
//////////////////////////////CONTROL_ESTACION,ESTACION//////////////////////////
//////////////////////////////BILLETE_DENOMINACION///////////////////////////////
///////FECHA CREACION  		: 20/12/2013/////////////////////////////////////////
////////FECHA		 		:14/01/2014//////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Corte de Caja</title>
<script language="javascript" type="text/javascript" src="../js/jquery.js"></script>
<script  type="text/javascript" src="../js/jquery-1.9.1.js"></script>
<script  type="text/javascript" src="../js/smartpaginator.js"></script> 

<script  type="text/javascript" src="../js/jquery-ui.js"></script>
<script type="text/javascript" src="../js/jquery.keypad.js"></script>
<!--<script type="text/javascript" src="../js/jquery.keypad.Num.js"></script>-->
<script language="javascript" type="text/javascript" src="../js/ajax_corteCaja.js"></script>
<script language="javascript1.1"  src="../js/js_validaciones.js"></script>
<link rel="stylesheet" type="text/css" href="../css/modal.css"/>
<link rel="stylesheet" type="text/css" href="../css/est_botones.css"/>
<link type="text/css" href="../css/jquery.keypad.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../css/paginador.css"/>         
<link rel="stylesheet" type="text/css" href="../css/est_pantallas.css"/>
<!--<link rel="stylesheet" type="text/css" href="../css/screen.css"/>-->






</head>
<body>

<table width="100%" height="100%">
 <td>
<div>
<table border="1px" width="100%" height="100%" class="fondo_tabla">
	<tr>	
		<th class="tabla_cabecera" width="34%">Mesas Abiertas...</th>
   		<th class="tabla_cabecera" width="31%">Cuentas Abiertas...</th>
   		<th class="tabla_cabecera" width="35%">Empleados en Estacion...</th>    
	</tr>
    <tr>
    
     <td align="center">
     		<div id="detalle_plu"  style="overflow-y:scroll; height:450px">          
         	</div>       
	 </td>
    	<!--<td align="center"><input inputmode="none"  class="btn" type="button"  value="No Open Tables"></td>-->
        <td align="center">
        	<div id="cuenta"  style="overflow-y:scroll; height:450px">      
          </div>
        </td>
        <td align="center">
            <div id="empleado"  style="overflow-y:scroll; height:450px">      
          </div>       
	 	</td>   
	<TR>
    	<TD>
			<TABLE align="center">
				<TR>
                	<TD align="center"><input inputmode="none"  class="botonnormal" type="button"  value="Ver"></TD>
                    <TD align="center"><input inputmode="none"  class="botonnormal" type="button"  value="Close All Empty"></TD>
				</TR>
			</TABLE>
		</TD>
		<TD><TABLE align="center">
				<TR>
                	<TD align="center"><input inputmode="none"  class="botonnormal" type="button"  value="Settle Charges"></TD>
                    <TD align="center"><input inputmode="none"  class="botonnormal" type="button"  value="Settle Batch"></TD>
				</TR>
			</TABLE></TD>
            <TD><TABLE align="center">
				<TR>
                	<TD align="center"><input inputmode="none"  class="botonnormal" type="button"  value="Clock Out All"></TD>
                    <TD align="center"><input inputmode="none"  class="botonnormal" type="button"  value="Corte de Caja"></TD>
				</TR>
			</TABLE></TD>
	</TR>
</TABLE>
</div>
 </td>
 <td>
 <div align="right">
 <table  width="100%" height="100px" align="right">
	<tr>
    	<td>
        <input inputmode="none"  id="btn_aceptar" type="button" class="botonimagen"  value"ACEPTAR" onclick="fn_finDia();"/>
        </td>        
    </tr>
 </table>
 </div>
  </td>
</table>

<div id="dialog" title="Corte de Caja" style="width:100%">
  <table border="1" width="100%">
	<tr>
        <th>Formas de Pago</th>
        <th>Transacciones</th>
        <th>Monto Actual</th>
        <th>POS Calculado</th>
        <th>Mas o Menos</th>
	</tr>  
    <tr>
    	<td colspan="5">
    	<div id="formaPago">       
    	</div>
        </td>
	</tr>	

    <tfoot>
        <tr>
        
        <td colspan="5" class="tabla_cabecera">
        <table id="tpie" border="1">                                           
                <tr align="right">
                        <td style="width:312px" align="center">Totales>>></td>
                        <td align="center" style="width:143px; text-align:center"></td>
                        <td align="center" style="width:138px; text-align:center"></td>
                        <td align="center" style="width:130px; text-align:center"></td>
                </tr>                                    
        </table>
        </td>
        </tr>
	</tfoot>
 
</table>
<table align="center">
 <td><input inputmode="none"  type="button" style='height:60px;width:90px; color:#FFFFFF; background: #70903B' id="btn_okgeneral"  value="OK"/></td>
 <td><input inputmode="none"  type="button" style='height:60px;width:90px; color: #FFFFFF; background: #295C9A' id="btn_cancelargeneral"  value="Cancelar"/></td>	    
</table>
</div>

<div id="dialog2" title="Corte de Caja" style="width:100%">
  <table border="1" width="100%" cellpadding="10" id="mt">
	<tr>
        <th style="width:5%">Denominaciones</th>
        <th style="width:5%">Cantidad</th>
        <th style="width:5%">Total</th>       
	</tr>  
     <tr>
    	<td colspan="5">
    	<div id="billetes">        
    	</div>
        </td>
	</tr>
    <tfoot>
        <tr>
        
        <td colspan="3">
        <table id="tpie3" border="1">                                           
                <tr align="right">
                        <td style="width:450px" align="center">Total>>></td>
                        <td align="center" style="width:100px; text-align:center"><input inputmode="none"  style="text-align:center" type="text" id="totalNuevo"></td>
                </tr> 
                 <tr align="right">
                        <td style="width:450px" align="center">POS calculado>>></td>
                        <td align="center" style="width:100px; text-align:center"><input inputmode="none"  style="text-align:center" type="text" id="totalPos"></td>
                </tr>                                                
        </table>
        </td>
        </tr>
	</tfoot>
    
</table>

<table align="center">  
 	       
 <td><input inputmode="none"  type="button" id="ok"  value="OK"/></td>
 <td><input inputmode="none"  type="button" id="cancelar"  value="Cancelar"/></td>	       
			  </table> 
</div>

<div id="dialogTarjetas" title="Corte de Caja" style="width:100%">
  <!--<table border="1" width="100%">
	<tr>
        <th>Ingrese Monto:</th>       
	</tr>  
    <tr>
    	<td>
    	<div id="formaPago">       
    	</div>
        </td>
	</tr>	
    
    
    <tfoot>
        <tr>
        
        <td colspan="5" class="tabla_cabecera">
        <table id="tpie" border="1">                                           
                <tr align="right">
                        <td style="width:450px" align="center">Totales>>></td>
                        <td align="center" style="width:100px"></td>
                </tr>                                    
        </table>
        </td>
        </tr>
	</tfoot>
 
</table>-->
</div>

<div id="billetesElimina">
</div>


<div id="content" title="Mesas Abiertas...">
	<table class="tabla_cabecera">
		<tr>
			<td>
					<div>
                    <table>
                    
  						<thead>                        
    						<tr>
						    	<th style="width:60px" class="tabla_cabecera">Cantidad</th>
      							<th style="width:150px" class="tabla_cabecera">Descripcion</th>
      							<th style="width:120px" class="tabla_cabecera">Precio</th>
   							</tr>
  						</thead>  							
					</table>
                    <div style="overflow-y:scroll; height:250px; width:auto">
					<table id="t1">                      						
  							<tr>
                                <td>
                                <div id="detalleMesa">       
                                </div>
                                </td>
  							</tr>
					</table>
					</div>
                    </div>
			</td>
            <td style="width:500px">
            	      <table align="center" style="top:inherit">            
    						<tr align="center">
						    	<th style="width:100px; font-size:16px" class="tabla_cabecera">Total Neto:</th>
                                <td><input inputmode="none"  style="font-size:16px" id="txt_txt_totalNeto" readonly="readonly" type="text"></td>
   							</tr>
                            <tr align="center">
						    	<th style="width:60px; font-size:16px" class="tabla_cabecera">IVA:</th>
                                <td><input inputmode="none"  style="font-size:16px" id="txt_iva" readonly="readonly" type="text"></td>
   							</tr>
                            <tr align="center">
						    	<th style="width:100px; font-size:16px" class="tabla_cabecera"></th>
                                <td style="font-size:18px">----------------------------</td>
   							</tr>
                            <tr align="center">
						    	<th style="width:60px;  font-size:16px" class="tabla_cabecera">Total>>></th>
                                <td><input inputmode="none"  style="font-size:16px" id="txt_totalDetalle" readonly="readonly" type="text"></td>
   							</tr>                                                                                   
                             
					</table>   
                    
                    <table height="125px">
                     <tr>
                              
                            <td rowspan="2" colspan="2" align="center" style="width:400px; position:inherit; margin-top:10px"><input inputmode="none"  type="button" style='height:100px;width:115px; color:#FFFFFF; background: #70903B; font-size:24px' id="btn_okDetalle"  value="OK"/></td>                 		
                            </tr>
                    </table>
            </td>
		</tr>        
	</table>
 </div>

<input inputmode="none"  type="hidden" id="hid_controlEfectivo"/>
<input inputmode="none"  type="hidden" id="hid_estacion"/>
<input inputmode="none"  type="hidden" id="hid_usuario"/>
<input inputmode="none"  type="hidden" id="hid_controlMesa"/>
<input inputmode="none"  type="hidden" id="hid_controlCuenta"/>
<input inputmode="none"  type="hidden" id="hid_controlEstacion"/>
<input inputmode="none"  type="hidden" id="hid_controlDiferencia"/>

</body>
</html>