<?php 
session_start();
/////////////////////////////////////////////////////////////////
////////DESARROLLADO POR:Jose Fernandez//////////////////////////
////////DESCRIPCION: Impresion Resumen de Ventas//////////////////
////////TABLAS		: ///////////////////////////////////////////
////////FECHA CREACION	: 15/04/2014/////////////////////////////
/////////////////////////////////////////////////////////////////
////////FECHA ULTIMA MODIFICACION:18/07/2014/////////////////////
////////USUARIO QUE MODIFICO: Jose Fernandez/////////////////////
////////DECRIPCION ULTIMO CAMBIO: Impresion de factura///////////
/////////////////////////////////////////////////////////////////
////////FECHA ULTIMA MODIFICACION:21/07/2014/////////////////////
////////USUARIO QUE MODIFICO: Jose Fernandez/////////////////////
////////DECRIPCION ULTIMO CAMBIO: Configuracion E-fact///////////
/////////////////////////////////////////////////////////////////
include_once("../../system/conexion/clase_sql.php");
include_once ("../../clases/clase_impresionFactura.php");
$lc_cfacId=/*'K004F000000201';*/$_GET['cfac_id'];
$lc_impresion = new impresion_factura();
$lc_datos[0]=$lc_cfacId;
//$restaurante=@$_SESSION['rstId'];
//$lc_datos[1]=$restaurante;
//$cadena=@$_SESSION['cadenaId'];
//$lc_datos[2]=$cadena;
$lc_impresion->fn_consultar('verifica_numautorizacion',$lc_datos);
$lc_row = $lc_impresion->fn_leerObjeto();
$tipode_facturacion=$lc_row->tf_id;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>impresion_factura</title>

<link rel="stylesheet" type="text/css" href="../../css/style_impresion_factura.css" media="screen"/>
<link rel="stylesheet" type="text/css" href="../../css/style_impresion_factura.css" media="print"/>

</head>
<body>
</br>

<?php 
	if($tipode_facturacion==2)
	{
?>
	<div id="plus" class="facturacion">
		<table width="220px" align="center" >
        	<?php if($lc_impresion->fn_consultar('cabecera_factura',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
            <!--<tr>
            	<th align="center" colspan="4"><?php //echo $lc_row->emp_razon_social;?>	</th>               
            </tr> 
            <tr>
            	<td align="center" colspan="4">MATRIZ:  <?php //echo strtoupper($lc_row->emp_direccion);?>	</td>               
            </tr>
            <tr>
            	<th align="center" colspan="4">RUC:  <?php //echo $lc_row->emp_ruc;?>	</th>               
            </tr>
            <tr>
            	<td align="center" colspan="4">CONTRIBUYENTE ESPECIAL RESOLUCION: 214</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">SUCURSAL:  <?php //echo strtoupper($lc_row->rst_direccion);?>	</td>               
            </tr>-->             
             <tr>
            	<td align="left" colspan="4"># DOCUMENTO:  <?php echo $lc_row->documento;?>	</td>               
            </tr>
             <tr>
            	<td align="left" colspan="4">SERV:  <?php echo strtoupper($lc_row->usr_usuario);?>	</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">FECHA EMISION:  <?php echo $lc_row->cfac_fechacreacion;?>	</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">CLIENTE:  <?php echo strtoupper($lc_row->cli_nombres." ".$lc_row->cli_apellidos);?>	</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">RUC/CI:  <?php echo $lc_row->cli_documento;?>	</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">FONO:  <?php echo $lc_row->cli_telefono;?>	</td>               
            </tr>
             <tr>
            	<td align="left" colspan="4">DIREC.:  <?php echo strtoupper($lc_row->cli_direccion);?>	</td>               
            </tr>
           <tr>
            	<td align="left" colspan="4">==============================================</td>               
            </tr>
             <?php }}?>
             <tr>
            	<th align="center">CANT.</th>               
                <th align="center">DESCRIPCION</th> 
                <th align="center">P. UNIT</th>
                <th align="center">VALOR</th> 
            </tr>
            <?php 
				   if($lc_impresion->fn_consultar('detalle_factura',$lc_datos))
					{ 	$i=0;
						$j=1;
						while($lc_row = $lc_impresion->fn_leerObjeto())
						{								
				?>	
                 <tr>
                    <td  id="fila_<?php echo $i.$j;?>"><?php echo $lc_row->dtfac_cantidad;?></td><?php $j++;?>
                    <td align="left"><?php echo strtoupper($lc_row->plu_descripcion);?>	</td>
                    <td>$ <?php echo number_format(($lc_row->dtfac_precio_unitario),2,".","");?></td>
                    <td>$ <?php echo number_format(($lc_row->dtfac_total),2,".","");?></td>                 	
                </tr>
    		   <?php 
					}} 			
			?>
            	<tr>
            	<td align="center" colspan="4">==============================================</td>               
            </tr>
           			 <?php if($lc_impresion->fn_consultar('totales_factura',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
             <tr>
            	<td align="right" colspan="2">SUBTOTAL:</td> 
                <td align="right" colspan="2">$ <?php echo round(($lc_row->cfac_subtotal),2);?>	</td>               
            </tr>
            <tr>
            	<td align="right" colspan="4">=============</td>       
            </tr>
             <?php if($lc_row->cdn_tipoimpuesto=='Diferenciado')
			{?>
            
            <tr>
            	<td align="right" colspan="2">BASE 12%.....$</td> 
                <td id="numero_general" align="right" colspan="2"> <?php echo round(($lc_row->cfac_base_iva),2);?>	</td>               
            </tr>
            <tr>
            	<td align="right" colspan="2">BASE 0%.....$</td> 
                <td id="numero_general" align="right" colspan="2"> <?php echo round(($lc_row->cfac_base_cero),2);?>	</td>               
            </tr>
            <?php } ?>
            <tr>
            	<td align="right" colspan="2">I.V.A.12%.....$</td> 
                <td id="numero_general" align="right" colspan="2"> <?php echo round(($lc_row->cfac_iva),2);?>	</td>               
            </tr>
            <tr>
            	<td align="right" colspan="4">=============</td>       
            </tr>
            
            
            <th align="right" colspan="2">TOTAL.....$:</th> 
            <th id="numero_general" align="right" colspan="2"> <?php echo round(($lc_row->cfac_total),2);?>	</th> 

           	            		 <?php }}?>
                	 <?php if($lc_impresion->fn_consultar('formas_pago',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>
            <tr>
            	<td align="left" colspan="3"><?php echo $lc_row->fmp_descripcion;?></td> 
                <td align="right" colspan="1">$ <?php echo round(($lc_row->cfac_total),2);?>	</td>               
            </tr>
            		 <?php }}?>
                     <tr>
            	<td align="center" colspan="4">==============================================</td>               
            </tr>
		</table>
        </br>
        </br>
        </br>
        </br>
        </br>
        <div align="center">
        <div class="promociones">
              
        
        <?php //valida so rst tiene promociones//
				if($lc_impresion->fn_consultar('verifica_cupon',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{
							$promo=$lc_row->existe_promocion;
							if($promo==1)
							{
								if($lc_impresion->fn_consultar('detalle_cupon',$lc_datos))
								{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
								{
									echo "***********************************************************";
									echo "<br>";
									echo strtoupper($lc_row->pro_nombre);
									echo "<br>";
									echo "<br>";
									echo strtoupper($lc_row->pro_descripcion);
									/*$text=$lc_row->pro_descripcion;
									//$exploded = explode(array(" "),$text);
									$exploded=explode(' ', $text);
									echo $exploded[0];
									echo "  ".$exploded[1];
									//print_r($exploded);*/
					
		?>
         *********************************************************
         <?php	
                date_default_timezone_set("America/Lima");
                setlocale(LC_TIME, "spanish");
               // $lc_fecha = strftime("%d / %b / %Y  %hh:%mm" );
                $fecha_actual = date("d/m/Y H:i"); 
				echo "Factura No: ";
				echo "<br>";
                echo "Fecha Emision:  ".$fecha_actual; 
		?>
         *********************************************************
          <?php	
                echo "PRESENTA TU FACTURA ADJUNTA A ESTE CUPON PARA PODER CANJEAR TU BENEFICIO NO APLICA EN DOMICILIO"
		?>
        **********************************************************
        <?php	 
								echo "*********** CADUCA EL  ".$lc_row->pro_fechafin." **********";
								echo "<br>";
								echo "**********************************************************";
								echo "<br>";
        						echo "**********************************************************";
								
								}
								}
								
							}
					}
					}
								?>
       
        </br>
        </br>
        </br>
        </br>
        </div>
        </div>
	</div>
    <?php
	}
	if($tipode_facturacion==1)
	{	
	
	?>
    
    	<div id="plus" class="facturacion">
		<table width="220px" align="center">
        	<?php
			$lc_impresion->fn_consultar('verifica_empresa',$lc_datos);
			$lc_row = $lc_impresion->fn_leerObjeto();
			$empresa=$lc_row->emp_nombre;
			$leyenda=$empresa." HA INICIADO LA EMISION DE FACTURAS ELECTRONICAS, BRINDANDO UN IMPORTANTE SERVICIO A SUS CLIENTES.";
			?>             
			 <tr>
            	<td style="padding-bottom:10px;" align="justify" colspan="4"><?php echo $leyenda;?>	</td>               
            </tr>
			
			
			<?php
			if($lc_impresion->fn_consultar('cabecera_factura',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
           
            <tr>
            	<td align="center" colspan="4"><?php echo $lc_row->emp_razon_social;?>	</td>               
            </tr> 
            <tr>
            	<td align="center" colspan="4">MATRIZ:  <?php echo strtoupper($lc_row->emp_direccion);?>	</td>               
            </tr>
            <tr>
            	<td style="padding-bottom:10px;" align="center" colspan="4">RUC:  <?php echo $lc_row->emp_ruc;?>	</td>               
            </tr>
            <tr>
            	<td align="center" colspan="4">DETALLE DE FACTURA ELECTRONICA</td>               
            </tr>
            <tr>
            	<td style="padding-bottom:10px;" align="center" colspan="4">DOCUMENTO SIN VALOR TRIBUTARIO</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4"># DOCUMENTO:  <?php echo $lc_row->documento;?>	</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">SUCURSAL:  <?php echo strtoupper($lc_row->rst_direccion);?>	</td>               
            </tr>             
             <tr>
            	<td align="left" colspan="4">SERV:  <?php echo strtoupper($lc_row->usr_usuario);?>	</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">FECHA EMISION:  <?php echo $lc_row->cfac_fechacreacion;?>	</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">CLIENTE:  <?php echo strtoupper($lc_row->cli_nombres." ".$lc_row->cli_apellidos);?>	</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">RUC/CI:  <?php echo $lc_row->cli_documento;?>	</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">FONO:  <?php echo $lc_row->cli_telefono;?>	</td>               
            </tr>
             <tr>
            	<td align="left" colspan="4">DIREC.:  <?php echo strtoupper($lc_row->cli_direccion);?>	</td>               
            </tr>
           <tr>
            	<td align="left" colspan="4">==============================================</td>               
            </tr>
             <?php }}?>
             <tr>
            	<th align="center">CANT.</th>               
                <th align="center">DESCRIPCION</th> 
                <th align="center">P. UNIT</th>
                <th align="center">VALOR</th> 
            </tr>
            <?php 
				   if($lc_impresion->fn_consultar('detalle_factura',$lc_datos))
					{ 	$i=0;
						$j=1;
						while($lc_row = $lc_impresion->fn_leerObjeto())
						{								
				?>	
                 <tr>
                    <td  id="fila_<?php echo $i.$j;?>"><?php echo $lc_row->dtfac_cantidad;?></td><?php $j++;?>
                    <td align="left"><?php echo strtoupper($lc_row->plu_descripcion);?>	</td>
                    <td id="numero_general"> <?php echo number_format(($lc_row->dtfac_precio_unitario),2,".","");?></td>
                    <td id="numero_general"> <?php echo number_format(($lc_row->dtfac_total),2,".","");?></td>                 	
                </tr>
    		   <?php 
					}} 			
			?>
            	<tr>
            	<td align="center" colspan="4">==============================================</td>               
            </tr>
           			 <?php if($lc_impresion->fn_consultar('totales_factura',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
             <tr>
            	<td align="right" colspan="2">SUBTOTAL.....$</td> 
                <td id="numero_general" align="right" colspan="2"> <?php echo round(($lc_row->cfac_subtotal),2);?>	</td>               
            </tr>
            <tr>
            	<td align="right" colspan="4">=============</td>       
            </tr>
            <?php if($lc_row->cdn_tipoimpuesto=='Diferenciado')
			{?>
            
            <tr>
            	<td align="right" colspan="2">BASE 12%.....$</td> 
                <td id="numero_general" align="right" colspan="2"> <?php echo round(($lc_row->cfac_base_iva),2);?>	</td>               
            </tr>
            <tr>
            	<td align="right" colspan="2">BASE 0%.....$</td> 
                <td id="numero_general" align="right" colspan="2"> <?php echo round(($lc_row->cfac_base_cero),2);?>	</td>               
            </tr>
            <?php } ?>
            <tr>
            	<td align="right" colspan="2">I.V.A.12%.....$</td> 
                <td id="numero_general" align="right" colspan="2"> <?php echo round(($lc_row->cfac_iva),2);?>	</td>               
            </tr>
            <tr>
            	<td align="right" colspan="4">=============</td>       
            </tr>
            <tr>
            	<th align="right" colspan="2">TOTAL.....$:</th> 
                <th id="numero_general" align="right" colspan="2"> <?php echo round(($lc_row->cfac_total),2);?>	</th>               
            </tr>
           	            		 <?php }}?>
                	 <?php if($lc_impresion->fn_consultar('formas_pago',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>
            <tr>
            	<td align="left" colspan="3"><?php echo strtoupper($lc_row->fmp_descripcion);?></td> 
                <td id="numero_general" align="right" colspan="1"> <?php echo round(($lc_row->cfac_total),2);?>	</td>               
            </tr>
            		 <?php }}?>
                     <tr>
            	<td align="center" colspan="4">==============================================</td>               
            </tr>
             <?php
			 	if($lc_impresion->fn_consultar('obtiene_autorizacion',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
						{
								$auto=$lc_row->rsp_autorizacion;																					
			 ?>
             
             <tr>
            	<td align="justify" colspan="4">PUEDE CONSULTAR SU FACTURA EN LA PAGINA WEB DEL SRI CON EL SIGUIENTE NUMERO DE AUTORIZACION:</td>                          
            </tr>
            <tr>            	
                <td style="font-size:13px; letter-spacing:0.2px" align="center" colspan="4"><?php echo $auto?>	</td>               
            </tr>
            <?php 		}	
					}?>
		</table>       
        </br>
        </br>
        </br>
        </br>
        <div align="center">
        <div class="promociones">
               
        
        
        <?php //valida so rst tiene promociones//
				if($lc_impresion->fn_consultar('verifica_cupon',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{
							$promo=$lc_row->existe_promocion;
							if($promo==1)
							{
								if($lc_impresion->fn_consultar('detalle_cupon',$lc_datos))
								{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
								{
									echo "***********************************************************";
									echo "<br>";
									echo $lc_row->pro_nombre;
									echo "<br>";
									echo "<br>";
									echo $lc_row->pro_descripcion;
									/*$text=$lc_row->pro_descripcion;
									//$exploded = explode(array(" "),$text);
									$exploded=explode(' ', $text);
									echo $exploded[0];
									echo "  ".$exploded[1];
									//print_r($exploded);*/
					
		?>  
         *********************************************************
         <?php	
                date_default_timezone_set("America/Lima");
                setlocale(LC_TIME, "spanish");
               // $lc_fecha = strftime("%d / %b / %Y  %hh:%mm" );
                $fecha_actual = date("d/m/Y H:i"); 
				echo "Factura No: ";
				echo "<br>";
                echo "Fecha Emision:  ".$fecha_actual; 
		?>
         *********************************************************
          <?php	
                echo "PRESENTA TU FACTURA ADJUNTA A ESTE CUPON PARA PODER CANJEAR TU BENEFICIO NO APLICA EN DOMICILIO"
		?>
        **********************************************************
        <?php	 
								echo "*********** CADUCA EL  ".$lc_row->pro_fechafin." **********";
								echo "<br>";
								echo "**********************************************************";
								echo "<br>";
        						echo "**********************************************************";
								
								}
								}
								
							}
					}
					}
								?>
       
        </br>
        </br>
        </br>
        </br>
        </div>
        </div>
	</div>
    
    <?php 
	}
	/*empieza el tipo de facturacion de plan market*/
	if($tipode_facturacion==4)
	{
	?>
    <div id="plus" class="facturacion">
		<table width="220px" align="center">
        	<?php
			$lc_impresion->fn_consultar('verifica_empresa',$lc_datos);
			$lc_row = $lc_impresion->fn_leerObjeto();
			$empresa=$lc_row->emp_nombre;
			$leyenda=$empresa." HA INICIADO LA EMISION DE FACTURAS ELECTRONICAS, BRINDANDO UN IMPORTANTE SERVICIO A SUS CLIENTES.";
			?>             
			 <tr>
            	<td style="padding-bottom:10px;" align="justify" colspan="4"><?php echo $leyenda;?>	</td>               
            </tr>
			
			
			<?php
			if($lc_impresion->fn_consultar('cabecera_factura',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
           
            <tr>
            	<td align="center" colspan="4"><?php echo $lc_row->emp_razon_social;?>	</td>               
            </tr> 
            <tr>
            	<td align="center" colspan="4">MATRIZ:  <?php echo strtoupper($lc_row->emp_direccion);?>	</td>               
            </tr>
            <tr>
            	<td style="padding-bottom:10px;" align="center" colspan="4">RUC:  <?php echo $lc_row->emp_ruc;?>	</td>               
            </tr>
            <tr>
            	<td align="center" colspan="4">DETALLE DE FACTURA ELECTRONICA</td>               
            </tr>
            <tr>
            	<td style="padding-bottom:10px;" align="center" colspan="4">DOCUMENTO SIN VALOR TRIBUTARIO</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4"># DOCUMENTO:  <?php echo $lc_row->documento;?>	</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">SUCURSAL:  <?php echo strtoupper($lc_row->rst_direccion);?>	</td>               
            </tr>             
             <tr>
            	<td align="left" colspan="4">SERV:  <?php echo strtoupper($lc_row->usr_usuario);?>	</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">FECHA EMISION:  <?php echo $lc_row->cfac_fechacreacion;?>	</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">CLIENTE:  <?php echo strtoupper($lc_row->cli_nombres." ".$lc_row->cli_apellidos);?>	</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">RUC/CI:  <?php echo $lc_row->cli_documento;?>	</td>               
            </tr>
            <tr>
            	<td align="left" colspan="4">FONO:  <?php echo $lc_row->cli_telefono;?>	</td>               
            </tr>
             <tr>
            	<td align="left" colspan="4">DIREC.:  <?php echo strtoupper($lc_row->cli_direccion);?>	</td>               
            </tr>
           <tr>
            	<td align="left" colspan="4">==============================================</td>               
            </tr>
             <?php }}?>
             <tr>
            	<th align="center">CANT.</th>               
                <th align="center">DESCRIPCION</th> 
                <th align="center">P. UNIT</th>
                <th align="center">VALOR</th> 
            </tr>
            <?php 
				   if($lc_impresion->fn_consultar('detalle_factura',$lc_datos))
					{ 	$i=0;
						$j=1;
						while($lc_row = $lc_impresion->fn_leerObjeto())
						{								
				?>	
                 <tr>
                    <td  id="fila_<?php echo $i.$j;?>"><?php echo $lc_row->dtfac_cantidad;?></td><?php $j++;?>
                    <td align="left"><?php echo strtoupper($lc_row->plu_descripcion);?>	</td>
					<td id="numero_general"> <?php echo number_format(($lc_row->dtfac_precio_unitario),2,".","");?></td>
                    <td id="numero_general"> <?php echo number_format(($lc_row->dtfac_total),2,".","");?></td>                 	
                </tr>
    		   <?php 
					}} 			
			?>
            	<tr>
            	<td align="center" colspan="4">==============================================</td>               
            </tr>
           			 <?php if($lc_impresion->fn_consultar('totales_factura',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
             <tr>
            	<td align="right" colspan="2">SUBTOTAL.....$</td> 
                <td id="numero_general" align="right" colspan="2"> <?php echo round(($lc_row->cfac_subtotal),2);?>	</td>               
            </tr>
            <tr>
            	<td align="right" colspan="4">=============</td>       
            </tr>
            <?php if($lc_row->cdn_tipoimpuesto=='Diferenciado')
			{?>
            
            <tr>
            	<td align="right" colspan="2">BASE 12%.....$</td> 
                <td id="numero_general" align="right" colspan="2"> <?php echo round(($lc_row->cfac_base_iva),2);?>	</td>               
            </tr>
            <tr>
            	<td align="right" colspan="2">BASE 0%.....$</td> 
                <td id="numero_general" align="right" colspan="2"> <?php echo round(($lc_row->cfac_base_cero),2);?>	</td>               
            </tr>
            <?php } ?>
            <tr>
            	<td align="right" colspan="2">I.V.A.12%.....$</td> 
                <td id="numero_general" align="right" colspan="2"> <?php echo round(($lc_row->cfac_iva),2);?>	</td>               
            </tr>
            <tr>
            	<td align="right" colspan="4">=============</td>       
            </tr>
            <tr>
            	<th align="right" colspan="2">TOTAL.....$:</th> 
                <th id="numero_general" align="right" colspan="2"> <?php echo round(($lc_row->cfac_total),2);?>	</th>               
            </tr>
           	            		 <?php }}?>
                	 <?php if($lc_impresion->fn_consultar('formas_pago',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>
            <tr>
            	<td align="left" colspan="3"><?php echo strtoupper($lc_row->fmp_descripcion);?></td> 
                <td id="numero_general" align="right" colspan="1"> <?php echo round(($lc_row->cfac_total),2);?>	</td>               
            </tr>
            		 <?php }}?>
                     <tr>
            	<td align="center" colspan="4">==============================================</td>               
            </tr>
             <?php
			 	if($lc_impresion->fn_consultar('obtener_clave_acceso',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
						{
								$auto=$lc_row->cfac_claveAcceso;																					
			 ?>
             
             <tr>
            	<td align="justify" colspan="4">CLAVE DE ACCESO::</td>                          
            </tr>
            <tr>            	
                <td style="font-size:13px; letter-spacing:0.2px" align="center" colspan="4"><?php echo $auto?>	</td>               
            </tr>
            <?php 		}	
					}?>
		</table>       
        </br>
        </br>
        </br>
        </br>
        <div align="center">
        <div class="promociones">
               
        
        
        <?php //valida so rst tiene promociones//
				if($lc_impresion->fn_consultar('verifica_cupon',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{
							$promo=$lc_row->existe_promocion;
							if($promo==1)
							{
								if($lc_impresion->fn_consultar('detalle_cupon',$lc_datos))
								{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
								{
									echo "***********************************************************";
									echo "<br>";
									echo $lc_row->pro_nombre;
									echo "<br>";
									echo "<br>";
									echo $lc_row->pro_descripcion;
									/*$text=$lc_row->pro_descripcion;
									//$exploded = explode(array(" "),$text);
									$exploded=explode(' ', $text);
									echo $exploded[0];
									echo "  ".$exploded[1];
									//print_r($exploded);*/
					
		?>  
         *********************************************************
         <?php	
                date_default_timezone_set("America/Lima");
                setlocale(LC_TIME, "spanish");
               // $lc_fecha = strftime("%d / %b / %Y  %hh:%mm" );
                $fecha_actual = date("d/m/Y H:i"); 
				echo "Factura No: ";
				echo "<br>";
                echo "Fecha Emision:  ".$fecha_actual; 
		?>
         *********************************************************
          <?php	
                echo "PRESENTA TU FACTURA ADJUNTA A ESTE CUPON PARA PODER CANJEAR TU BENEFICIO NO APLICA EN DOMICILIO"
		?>
        **********************************************************
        <?php	 
								echo "*********** CADUCA EL  ".$lc_row->pro_fechafin." **********";
								echo "<br>";
								echo "**********************************************************";
								echo "<br>";
        						echo "**********************************************************";
								
								}
								}
								
							}
					}
					}
								?>
       
        </br>
        </br>
        </br>
        </br>
        </div>
        </div>
	</div>
    
   <?php 
	}
	/*fin de el tipo de facturacion de plan market*/
	
	?>
</body>
</html>

