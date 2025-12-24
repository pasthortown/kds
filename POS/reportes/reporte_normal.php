<?php
	session_start();
	//////////////////////////////////////////////////////////////////////////////////////
	////////DESARROLLADO POR: Jose Fernandez//////////////////////////////////////////////
	////////DESCRIPCION: Pantalla de reportes/////////////////////////////////////////////
	///////TABLAS INVOLUCRADAS: Cabecera_factura, formas_pago, forma_pago_factura/////////
	//////////////////////////control_estacion,usuarios///////////////////////////////////
	///////FECHA CREACION: 21/08/2014/////////////////////////////////////////////////////	
	//////////////////////////////////////////////////////////////////////////////////////
	///////FECHA ULTIMA MODIFICACION: 06-04-2015 /////////////////////////////////////////
	///////USUARIO QUE MODIFICO: Jimmy Cazaro ////////////////////////////////////////////
	///////DECRIPCION ULTIMO CAMBIO: Modificar los reportes //////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////	
	//INCLUYE CLASES
	//header("Content-type: application/vnd.ms-excel");
	include_once("../system/conexion/clase_sql.php");
	include_once("../clases/clase_reportes.php"); 
	//include("../clases/clase_seguridades.php");	
	$lc_reportes=new reportes();
	$usr_id=$_SESSION['usuarioId'];
	$cdn_id=$_SESSION['cadenaId'];
	$rst_id=$_SESSION['rstId'];
	$est_id=$_SESSION['estacionId'];
	$lc_opcion = htmlspecialchars($_GET['lc_opcion']);
	$tipo = htmlspecialchars($_GET['visualizar']);
	
	if($tipo=='excel'){ header("Content-type: application/vnd.ms-excel"); }
	$rst_nombre = $_SESSION['rstNombre'];
	$inicioFecha=htmlspecialchars($_GET['inicio']);
	$finFecha=htmlspecialchars($_GET['fin']);

	$lc_datos[0]=$inicioFecha;
	$lc_datos[1]=$finFecha;
	$lc_datos[2]=$rst_id;
?><head>
	<link rel="stylesheet" type="text/css" href="../css/est_reporte.css"/>
</head>
<table width="90%" border="0" align="center">
		<tr>
			<td width="21%" rowspan="4" align="center"><img src=" ../imagenes/cadena/<?php echo $_SESSION['logo'];?>" width="140" height="78"></td>
			  <td width="55%" rowspan="4">
			  <div align="center">
				<p class="titulo_cadena"><strong>MAX POINT</strong></p>
				<p class="titulo_cadena"><strong><?php if ($lc_opcion=='cash') {?>REPORTE DE CUADRE DE CAJA<?php } else if ($lc_opcion=='ventas') {?>REPORTE DE VENTAS POR PLU<?php } else if ($lc_opcion=='transacciones') {?>RESUMEN DE TRANSACCIONES<?php } else if ($lc_opcion=='tax')	{?>RESUMEN DE IMPUESTOS<?php } else if ($lc_opcion=='anulaciones') { ?>REPORTE DE ANULACIONES<?php } ?></strong></p>
				<p class="titulo" style="margin:0px; padding:0px;">Desde: <?php echo $lc_datos[0];?> - Hasta: <?php echo $lc_datos[1];?></p>                        
			 </div></td>
			 <td width="24%" class="titulo_parametro"><strong>Fecha: </strong><?php echo date("d/m/Y H:m:s");?> </td>
		</tr>
		<tr>
			<td  class="titulo_parametro"><strong>Local: </strong><?php echo htmlentities($rst_nombre);?></td>
		</tr>
		<tr>
			<td class="titulo_parametro"><strong>Usuario: </strong><?php echo htmlentities($_SESSION['usuario']);?></td>
		</tr>
		<tr>
			<td class="titulo_parametro"><strong></strong></p></td>
		</tr>
	</table>
<?php
	//Comienzo del armado de los reportes dependiendo del tipo
	if ($lc_opcion=='cash')
	{
		$cajero=-1;
		$lc_reportes->fn_consultar('reporte_cashOut', $lc_datos);
		$total=0;
		$total_Total=0;
		$fecha=-1;
		$pago=-1;
		$fecha_apertura='--';
		$numero = 0;
	?>

<!--<table width="84%" border="0" align="center">
		<tr>
			<td width="21%" rowspan="4" align="center"><img src=" ../imagenes/cadena/<?php echo $_SESSION['logo'];?>" width="140" height="78"></td>
			  <td width="55%" rowspan="4">
			  <div align="center">
				<p class="titulo_cadena"><strong>MAX POINT</strong></p>
				<p class="titulo_cadena"><strong>EMPLOYEE CASHOUT REPORT</strong></p>
				<p class="titulo" style="margin:0px; padding:0px;">Desde: <?php echo $lc_datos[0];?> - Hasta: <?php echo $lc_datos[1];?></p>                        
			 </div></td>
			 <td width="24%" class="titulo_parametro"><strong>Fecha: </strong><?php echo date("d/m/Y H:m:s");?> </td>
		</tr>
		<tr>
			<td  class="titulo_parametro"><strong>Local: </strong><?php echo htmlentities($rst_nombre);?></td>
		</tr>
		<tr>
			<td class="titulo_parametro"><strong>Usuario: </strong><?php echo htmlentities($_SESSION['usuario']);?></td>
		</tr>
		<tr>
			<td class="titulo_parametro"><strong></strong></p></td>
		</tr>
	</table>-->

	<table width="750" border="0" align="center">
	<?php
		while($lc_row = $lc_reportes->fn_leerobjeto())
		{
			$usuario=$lc_row->usr_descripcion;
			$fechaInicio=$lc_row->ctrc_fecha_inicio;
			$fechaSalida=trim($lc_row->ctrc_fecha_salida);
			$formaPago=$lc_row->fmp_descripcion;
			$transaccion=$lc_row->cfac_id;
			$totalTransaccion=$lc_row->cfac_total;
			$fechaFactura = $lc_row->cfac_fechacreacion;
			
			if(($cajero!=$usuario) || ($fecha_apertura!=$fechaInicio))
			{
				if(($cajero!=-1) || ($fecha_apertura != '--'))
				{
				?>
   	  <tr><td colspan="3"></td><td colspan="2" class="separador">&nbsp;</td></tr>
					<tr>
						<th colspan=3></th>
						<th class="estilo_subcabecera" align='right'><b>Total:</b></th> 
						<td class="estilo_subcabecera" align='right'><b><?php echo $total; ?></b></td>
					<tr>
                    <tr>
                    	<th colspan="3">&nbsp;</th>
                    </tr>
				<?php
				}
				?>
				<tr class="estilo_subcabecera" align="center">
                 	<td align='center' colspan="5"><B><?php echo htmlentities(strtoupper($usuario)); ?></b></td>
				</tr>
                <tr align="center" class="estilo_subcabecera">
                    <th class="solo_bordes" align='left'><B>Fecha Entrada:</b></th>
                    <th class="solo_bordes" align='left'><?php echo $fechaInicio;?>&nbsp;</th>
                    <th class="solo_bordes" align='left'><B>Fecha Salida:</b></th>
                    <th class="solo_bordes" align='left'><?php if (trim($fechaSalida) != '') { echo $fechaSalida; } else { echo 'ESTACION NO CERRADA'; }?>&nbsp;</th>
	                <th class="solo_bordes" align='left'>&nbsp;</th>    
				</tr>                
                <tr><td colspan="5" class="separador">&nbsp;</td></tr>
				<tr class="estilo_subcabecera">                    	
                	<th class="solo_bordes" align="center"># Transacciones</th>
                    <th class="solo_bordes" align="center">Fecha Factura</th>
                    <th class="solo_bordes" align="center">Forma Pago</th>
                    <th class="solo_bordes" align="center">Transacci&oacute;n</th>
                    <th class="solo_bordes" align="center">Total</th>
				</tr>         
				<?php		
				$total=0;
				$i=0;																									
			}				 
			$cajero=$usuario;
			$fecha_apertura = $fechaInicio;
			$total+=$totalTransaccion;
			$i++;
			?>						
            <tr class="estilo_detalle2">	
                <td class="solo_bordes" align="center"><?php echo $i;?>&nbsp;</td>
                <td class="solo_bordes" align="center"><?php echo $fechaFactura;?>&nbsp;</td>
                <td class="solo_bordes" align="center"><?php echo $formaPago;?>&nbsp;</td>                         
                <td class="solo_bordes" align="center"><?php echo $transaccion;?>&nbsp;</td>
                <td class="solo_bordes" align="right"><?php echo $totalTransaccion;?>&nbsp;</td>                        
            </tr>
			<?php      
			$total_Total+=$totalTransaccion;
		}//fin while			
		?>                        
        <tr><td colspan="3"></td><td colspan="2" class="separador">&nbsp;</td></tr>
        <tr>
            <th colspan=3></th>
            <th class="estilo_subcabecera" align='right'><b>Total:</b></th> 
            <td class="estilo_subcabecera" align='right'><b><?php echo $total; ?></b></td>
        <tr>
        <tr>
            <th colspan=3></th>
            <th class="estilo_subcabecera" align='right'><b>Suma Total:</b></th> 
            <td class="estilo_subcabecera" align='right'><b><?php echo $total_Total; ?></b></td>
        <tr>        
		<?php
	}
	?>
</table>
<!--****************************************************************************-->
	<?php /*?><?php
	if($tipo!='excel'){ 
	?><?php */?>
<!--    <table width="30%" border="0" align="center">
        <tr>
            <td width="200" align="center"><input inputmode="none"  id="btn_imp" style="width:200px; height:60px; text-align:center" type="button" onclick="window.print();" value="Imprimir"/></td>
      		<td width="200" align="center"><input inputmode="none"  id="btn_can" style="width:200px; height:60px; text-align:center" type="button" onclick="window.close();" value="Cancelar"/></td>        </tr>
	</table>   --> 
    <?php /*?><?php } ?><?php */?>
<!--****************************************************************************-->    
<?php    
    if ($lc_opcion=='ventas')
	{
		//$lc_reportes->fn_consultar('reporte_ventasPlu', $lc_datos);
?>
	<table width="700" border="0" align="center">
              <!--<tr>                  
                  <td width="285" rowspan="2">
                      <div align="center">
                      <p><strong><?php echo $rst_nombre;?></strong></p>                      
                      <p align="center"><strong>SALES BY PLU</strong></p>
                      </div>
                  </td>
<td width="100">
                  <div align="right">                      
                      <p align="left" class="titulo_parametro"><strong>Desde:</strong>&nbsp; &nbsp;<?php echo $lc_datos[0];?>                     
                      </div>
                </td>     
              </tr>   
               <tr>
              <td width="100">
                  <div align="right">                     
                      <p align="left" class="titulo_parametro"><strong>Hasta:</strong>&nbsp; &nbsp;<?php echo $lc_datos[1];?>                     
                      </div>
                </td>     
              </tr>-->    
          </table>
	
	<table width="700" border="0" align="center">
    <tr><td colspan="6" class="separador">&nbsp;</td></tr>
    <tr class="estilo_subcabecera">                    	
        <td align="center" class="solo_bordes"><B>Plu</B></td>
        <td align="center" class="solo_bordes"><B>Descripcion</B></td>
        <td align="center" class="solo_bordes"><B>Cantidad</B></td>
        <td align="center" class="solo_bordes"><B>% Cantidad</B></td>
        <td align="center" class="solo_bordes"><B>Valor</B></td>           
        <td align="center" class="solo_bordes"><B>% Valor</B></td>             
     </tr>
 <?php
 
	$lc_reportes->fn_consultar('reporte_ventasPlu', $lc_datos);
 	while($lc_row = $lc_reportes->fn_leerobjeto())
		{
		$num_plu=$lc_row->plu_num_plu;
		$plu_nombre=$lc_row->plu_descripcion;
		$plu_cantidad=$lc_row->cantidad;
		$plu_total=$lc_row->total;
		$plu_porcentualCantidad=$lc_row->porcentajeCantidad;
		$plu_porcentualValor=$lc_row->porcentajeValor;
			
 ?>     	
     
    <tr class="estilo_detalle2">                    	
        <td align="center" class="solo_bordes"><?php echo $num_plu; ?></td>
        <td class="solo_bordes"><?php echo $plu_nombre; ?></td>
        <td align="right" class="solo_bordes"><?php echo $plu_cantidad; ?></td>
        <td align="right" class="solo_bordes"><?php echo $plu_porcentualCantidad; ?></td>
        <td align="right" class="solo_bordes"><?php echo $plu_total; ?></td>                        
        <td align="right" class="solo_bordes"><?php echo $plu_porcentualValor; ?></td>
     </tr>
    
 <?php
		}//fin del while
		
 ?>
 		

 <?php
		
		$lc_reportes->fn_consultar('reporte_totalesventasPlu', $lc_datos);
		while($lc_row = $lc_reportes->fn_leerobjeto())
		{
			$totalCantidad=$lc_row->TotalCantidad;
			$totalvalor=$lc_row->TotalValor;
 ?>          
 <tr>                    	
                        <td class="estilo_subcabecera" align="right" colspan="2"><B>Total:</B></td>             
     	  
 		                    	
                        <td class="estilo_subcabecera" align="right"><B><?php echo $totalCantidad; ?></B></td>
                        <td class="estilo_subcabecera" align="left"><B></B></td>
                        <td class="estilo_subcabecera" align="right"><B><?php echo $totalvalor; ?></B></td>
						<td class="estilo_subcabecera" align="left"><B></B></td>
    </tr>     
<?php
		}//fin del while
		
 ?>    
 		</table>
<?php	}
 
 
 
 
 
  if ($lc_opcion=='transacciones')
	{
		//$lc_reportes->fn_consultar('reporte_ventasPlu', $lc_datos);
?>
	<table width="700" border="0" align="center">
              <!--<tr>               
                  <td width="285" rowspan="2">
                      <div align="center">
                      <p><strong><?php echo $rst_nombre;?></strong></p>                      
                      <p align="center"><strong>Resumen de Transacciones</strong></p>
                      </div>
                  </td>
                  <td width="100">
                  <div align="right">                      
                      <p align="left" class="titulo_parametro"><strong>Desde:</strong>&nbsp; &nbsp;<?php echo $lc_datos[0];?>                     
                      </div>
                </td>     
              </tr>   
               <tr>
              <td width="100">
                  <div align="right">                     
                      <p align="left" class="titulo_parametro"><strong>Hasta:</strong>&nbsp; &nbsp;<?php echo $lc_datos[1];?>                     
                      </div>
                </td>     
              </tr> -->   
          </table>
	<table width="700" border="0" align="center">
    <tr><td colspan="6" class="separador">&nbsp;</td></tr>
    <tr class="estilo_subcabecera">                    	
        <td align="center" class="solo_bordes"><B>Transacci&oacute;n</B></td>
        <td align="center" class="solo_bordes"><B>Factura</B></td>
        <td align="center" class="solo_bordes"><B>Fecha</B></td>
        <td align="center" class="solo_bordes"><B>Usuario</B></td>
        <td align="center" class="solo_bordes"><B>Venta Neta</B></td>           
        <td align="center" class="solo_bordes"><B>Total</B></td>             
     </tr>
 <?php
 
	$lc_reportes->fn_consultar('reporte_transacciones', $lc_datos);
 	while($lc_row = $lc_reportes->fn_leerobjeto())
		{
		$trans=$lc_row->transaccion;
		$factura=$lc_row->cfac_numero_factura;
		$fecha=$lc_row->fechaCreacion;
		$usuario=$lc_row->usr_descripcion;
		$subtotal=$lc_row->Subtotal;
		$Total=$lc_row->total;
			
 ?>     	
     
    <tr class="estilo_detalle2">                    	
        <td align="center" class="solo_bordes"><?php echo $trans; ?></td>
        <td align="center" class="solo_bordes"><?php echo $factura; ?></td>
        <td align="center" class="solo_bordes"><?php echo $fecha; ?></td>
        <td class="solo_bordes"><?php echo $usuario; ?></td>
        <td align="right" class="solo_bordes"><?php echo $subtotal; ?></td>                        
        <td align="right" class="solo_bordes"><?php echo $Total; ?></td>
     </tr>
    
 <?php
		}//fin del while
		
 ?>
 		

 <?php
		
		$lc_reportes->fn_consultar('reporte_totalesTransacciones', $lc_datos);
		while($lc_row = $lc_reportes->fn_leerobjeto())
		{
			$granSubtotal=$lc_row->granSub;
			$granTotal=$lc_row->granTotal;
 ?>          
 <tr>                    	
                        <td colspan="3">&nbsp;</td>
                        <td class="estilo_subcabecera" align="right"><B>Total:</B></td>             
                        <td class="estilo_subcabecera" align="right"><B><?php echo $granSubtotal; ?></B></td>                       
                        <td class="estilo_subcabecera" align="right"><B><?php echo $granTotal; ?></B></td>                                      
    </tr>     
<?php
		}//fin del while
		
 ?>    
 		</table>
<?php	}
 if ($lc_opcion=='tax')
	{
		//$lc_reportes->fn_consultar('reporte_ventasPlu', $lc_datos);
?>
	<table width="700" align="center">
              <!--<tr>                 
                  <td width="285" rowspan="2">
                      <div align="center">
                      <p><strong><?php echo $rst_nombre;?></strong></p>                      
                      <p align="center"><strong>Resumen de Impuestos</strong></p>
                      </div>
                  </td>                     
             <td width="100">
                  <div align="right">                      
                      <p align="left" class="titulo_parametro"><strong>Desde:</strong>&nbsp; &nbsp;<?php echo $lc_datos[0];?>                     
                      </div>
                </td>     
              </tr>  
               <tr>
              <td width="100">
                  <div align="right">                     
                      <p align="left" class="titulo_parametro"><strong>Hasta:</strong>&nbsp; &nbsp;<?php echo $lc_datos[1];?>                     
                      </div>
                </td>     
              </tr> -->    
          </table>
	<table width="700" border="0" align="center">
    <tr><td colspan="3" class="separador">&nbsp;</td></tr>
    <tr class="estilo_subcabecera">                    	
        <td align="center" class="solo_bordes"><B>Ventas Netas</B></td>
        <td align="center" class="solo_bordes"><B>IVA</B></td>
        <td align="center" class="solo_bordes"><B>Total Impuestos</B></td>                                   
     </tr>
 <?php
 
	$lc_reportes->fn_consultar('reporte_taxes', $lc_datos);
 	while($lc_row = $lc_reportes->fn_leerobjeto())
		{
		$trans=$lc_row->numTrans;
		$iva=$lc_row->totalImpuestos;	
		$neta=$lc_row->subtotal;	
 ?>     	
     
    <tr class="estilo_detalle2">                    	
        <td class="solo_bordes" align="right"><?php echo $neta; ?></td>
        <td class="solo_bordes" align="right"><?php echo $iva; ?></td>
        <td class="solo_bordes" align="right"><?php echo $iva; ?></td>                       
     </tr>
    
 <?php
		}//fin del while
		
 ?>
 		

 <?php
		
		$lc_reportes->fn_consultar('reporte_totalesTaxes', $lc_datos);
		while($lc_row = $lc_reportes->fn_leerobjeto())
		{
			$grantot=$lc_row->total;			
 ?>          
 <tr>                    	
                        <td>&nbsp;</td>
                        <td class="estilo_subcabecera" align="right" colspan="1"><B>Total:</B></td>             
                        <td class="estilo_subcabecera" align="right"><B><?php echo $grantot; ?></B></td>                                             
    </tr>     
<?php
		}//fin del while
		
 ?>    
 		</table>
<?php	}
 if ($lc_opcion=='anulaciones')
	{
		$usuarioFactura=-1;		
		$totalAnula=0;
		$total_TotalAnula=0;
		$transaccion_Anulacion=-1;
		$cantidadParcial = 0;
		$pedidoParcial = 0;
		//$total_Pedido = 0;
?>
	<table width="700" align="center">
              <!--<tr>                  
                  <td width="285" rowspan="2">
                      <div align="center">
                      <p><strong><?php echo $rst_nombre;?></strong></p>                      
                      <p align="center"><strong>Reporte de Anulaciones</strong></p>
                      </div>
                  </td>
                 <td width="100">
                  <div align="right">                      
                      <p align="left" class="titulo_parametro"><strong>Desde:</strong>&nbsp; &nbsp;<?php echo $lc_datos[0];?>                     
                      </div>
                </td>     
              </tr>   
               <tr>
              <td width="100">
                  <div align="right">                     
                      <p align="left" class="titulo_parametro"><strong>Hasta:</strong>&nbsp; &nbsp;<?php echo $lc_datos[1];?>                     
                      </div>
                </td>     
              </tr> -->   
          </table>
	<table width="700" border="0" align="center">    
 <?php
 
	$lc_reportes->fn_consultar('reporte_anulaciones', $lc_datos);
 	while($lc_row = $lc_reportes->fn_leerobjeto())
	{
		$usuarioAutoriza=$lc_row->autoriza;
		$usuarioDescripcion=$lc_row->usr_descripcion;
		$motivoAnulacion=$lc_row->mtv_descripcion;	
		$transaccionAnulacion=$lc_row->transaccion;
		$detallePlu=$lc_row->plu_descripcion;
		$cantidadPedido=$lc_row->dncre_cantidad;
		$totalPedido=$lc_row->total;
		
		if(($usuarioFactura!=$usuarioDescripcion) || ($transaccion_Anulacion != $transaccionAnulacion))
		{
			if($usuarioFactura!=-1 || $transaccion_Anulacion != -1)
			{
				
				?>
					<tr><td colspan="2"></td><td colspan="3" class="separador">&nbsp;</td></tr>
					<tr>
						<th colspan=2></th>
						<th class="estilo_subcabecera" align='right'><b>Total:<b/></th> 
                        <th class="estilo_subcabecera" align='center'><?php echo $cantidadParcial; ?><b/></th> 
						<td class="estilo_subcabecera" align='right'><b/><?php echo round($pedidoParcial,2); ?><b/></td>
					<tr>
					<tr>
						<th colspan="3">&nbsp;</th>
					</tr>            
				<?php
				$cantidadParcial = 0;
				$pedidoParcial = 0;
			}
			?>       	
				<tr align="center" class="estilo_subcabecera">
					<th class="solo_bordes" align='left'><B>Autoriza:</b></th>
					<th class="solo_bordes" align='left'><?php echo htmlentities(strtoupper($usuarioAutoriza)); ?>&nbsp;</th>
					<th class="solo_bordes" align='left'><B>Cajero:</b></th>
					<th class="solo_bordes" align='left'><?php echo htmlentities(strtoupper($usuarioDescripcion)); ?>&nbsp;</th>
					<th class="solo_bordes" align='left'>&nbsp;</th>    
				</tr>
				<tr align="center" class="estilo_subcabecera">
					<th class="solo_bordes" align='left'><B>Motivo Anulaci&oacute;n</b></th>
					<th class="solo_bordes" align='left' colspan="4"><?php echo $motivoAnulacion; ?>&nbsp;</th>
				</tr>                             
				<tr><td colspan="5" class="separador">&nbsp;</td></tr>
				<tr class="estilo_subcabecera">                    	                        
					<td align="center" class="solo_bordes"><B>Fecha Factura</B></td>
					<td align="center" class="solo_bordes"><B># Transacci&oacute;n</B></td> 
					<td align="center" class="solo_bordes"><B>Descripci&oacute;n</B></td>
					<td align="center" class="solo_bordes"><B>Cantidad</B></td>                                   
					<td align="center" class="solo_bordes"><B>Total</B></td>
				</tr>
			<?php		
			$totalAnula=0;
			$i=0;
			//$cantidadParcial += $cantidadPedido;
			//$cantidadParcial = 0;
		}	
		$cantidadParcial += $cantidadPedido;
		//if(($total_Pedido != 0) || ($total_Pedido != $totalPedido))
//		{
			$pedidoParcial += $lc_row->detalle_bruto;
		//}
		//$total_Pedido = $totalPedido;
		
		$usuarioFactura=$usuarioDescripcion;					
		$totalAnula+=$totalPedido;
		$transaccion_Anulacion = $transaccionAnulacion;
		$i++;
		//$cantidadParcial = 0;
	?>	
    <tr class="estilo_detalle2">						
        <td class="solo_bordes"><?php echo trim($lc_row->cfac_fechacreacion);?>&nbsp;</td>                        
        <td class="solo_bordes" align="center"><?php echo $transaccionAnulacion;?>&nbsp;</td>
        <td class="solo_bordes"><?php echo $detallePlu;?>&nbsp;</td>                         
        <td class="solo_bordes" align="center"><?php echo $cantidadPedido;?>&nbsp;</td>
        <td align="right" class="solo_bordes"><?php echo round($lc_row->detalle_bruto,2);?>&nbsp;</td>                        
    </tr>
	<?php
	$total_TotalAnula+=$totalPedido;
//	$cantidadParcial = 0;
}//fin del while
?>
    <tr><td colspan="2"></td><td colspan="3" class="separador">&nbsp;</td></tr>
    <tr>
        <th colspan=2></th>
        <th class="estilo_subcabecera" align='right'><b>Total:</b></th>
        <th class="estilo_subcabecera" align='center'><b><?php echo $cantidadParcial; ?><b/></th> 
        <td class="estilo_subcabecera" align='right'><b><?php echo /*$totalAnula*/round($pedidoParcial,2); ?></b></td>
    </tr>
 	</table>
<?php	
		}//fij reporte de anulaciones
 ?>
 
 	<?php
	if($tipo!='excel'){ 
	?>
    <table width="30%" border="0" align="center">
        <tr>
            <td width="200" align="center"><input inputmode="none"  id="btn_imp" style="width:200px; height:60px; text-align:center" type="button" onclick="window.print();" value="Imprimir"/></td>
      		<td width="200" align="center"><input inputmode="none"  id="btn_can" style="width:200px; height:60px; text-align:center" type="button" onclick="window.close();" value="Cancelar"/></td>        </tr>
	</table>    
    <?php } ?>