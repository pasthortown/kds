<?php 
session_start(); 	
/////////////////////////////////////////////////////////////////
////////DESARROLLADO POR:Jose Fernandez//////////////////////////
////////DESCRIPCION	: Impresion Voucher//////////////////////////
////////TABLAS		: 
////////FECHA CREACION	: 24/04/2014/////////////////////////////
/////////////////////////////////////////////////////////////////
include_once("../../system/conexion/clase_sql.php");
include_once ("../../clases/clase_facturacion.php");
//$lc_cfacId='K004F000000201';//$_GET['cfac_id'];
$lc_nombreRes=$_SESSION['cadenaNombre'];
$lc_idRes=$_SESSION['rstId']; 
$lc_idEst=$_SESSION['estacionId'];
$lc_impresion = new facturas();
$lc_datos[0]=$lc_idRes;
$lc_datos[1]=$lc_idEst;
$lc_datos[2]=/*5040;//*/$_GET['rsaut_id'];
//$lc_datos[0]=$lc_cfacId;
//$codigo='01';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
<link rel="stylesheet" type="text/css" href="../../css/style_impresion_factura.css" media="screen"/>
<link rel="stylesheet" type="text/css" href="../../css/style_impresion_factura.css" media="print"/>
</head>

<body>

</br>
	<?php if($lc_impresion->fn_consultar('verifica_respuesta',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{
						$codigo=$lc_row->cres_codigo;
						//$codigo=='01';
					}
					}
			if($codigo=='00')
			{
	?>
	<div id="plus" class="facturacion">
		<table width="220px" align="center">
        	
            <tr>
            	<td align="center" colspan="2"><?php echo $lc_nombreRes; ?>	</td>               
            </tr> 
            <?php if($lc_impresion->fn_consultar('direccion_empresa',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
            <tr>
           
            	<td align="center" colspan="2"><?php echo $lc_row->direccion;?>	</td>               
            </tr>
             <?php }}?>
              <?php if($lc_impresion->fn_consultar('mid_tid',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
            <tr>
            	<td align="center" colspan="1">MID:  <?php echo $lc_row->rst_mid;?>	</td>     
                <td align="center" colspan="1">TID:  <?php echo $lc_row->est_tid;?>	</td>          
            </tr>
            <tr>
            	<th></th>
            </tr>
            <tr>
            	<th></th>
            </tr>
             <?php }}?>
             
            <tr>           
            	<td align="center" colspan="2">COMERCIO</td>               
            </tr>
            <tr>
            	<td align="center" colspan="2">VENTA</td>               
            </tr>
             <?php if($lc_impresion->fn_consultar('consulta_tarjeta',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
             <tr>
            	<td align="center" colspan="2"><?php echo $lc_row->fmp_descripcion; ?></td>               
            </tr>
             <?php }}?>
             <?php if($lc_impresion->fn_consultar('cabecera_respuesta',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
                    <tr>
            	<th></th>
            </tr>
            <tr>
            	<th></th>
            </tr>
            <tr>
            	<th></th>
            </tr>           
               <tr>
            	<td align="left" colspan="2">Lote:  <?php echo $lc_row->lote;?>	</td>               
            </tr>
             <tr>
            	<td align="left" colspan="2">Tarjeta:  <?php echo $lc_row->rsaut_numero_tarjeta;?>	</td>               
            </tr>
            <tr>
            	<td align="left">Aprobacion:  <?php echo $lc_row->rsaut_numero_autorizacion;?></td>               				
                <td align="right">Referencia <?php echo $lc_row->rsaut_secuencial_transaccion;?></td>
            </tr>
            <?php }}?>
            <?php 
             date_default_timezone_set("America/Lima");
                setlocale(LC_TIME, "spanish");
               // $lc_fecha = strftime("%d / %b / %Y  %hh:%mm" );
                $fecha= date("d/m/Y"); 
				$Hora= date("H:i:s"); 
			?>
            <tr>            	
            	<td align="left">Fecha:  <?php echo $fecha;?>	</td>  
                <td align="right">Hora:  <?php echo $Hora;?>	</td>             
            </tr> 
            <tr>
            	<th> <p>&nbsp;</p> </th>
            </tr>
            <?php if($lc_impresion->fn_consultar('valores_voucher',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
            <tr>            	
            	<td align="left">Base consumo 12</td> 
                <td align="left">: USD <?php echo $lc_row->BaseIva;?></td>                                  
            </tr>
            <tr>            	
              	<td align="left">Base consumo 0</td>
                <td align="left">: USD <?php echo $lc_row->BaseNOiva;?>	</td>                         
            </tr>
            <tr>            	
            	<td align="justify">Subtotal Consumo</td>     
                <td align="left">: USD <?php echo $lc_row->BaseIva;?></td>                    
            </tr>
            <tr>            	
            	<td align="left">IVA</td>  
                <td align="left">: USD <?php echo $lc_row->Iva;?></td>                  
            </tr>
            <tr>            	
            	<td align="left">TOTAL</td>
                <td align="left">: USD <?php echo $lc_row->MontoTotal;?></td>                             
            </tr>
             <?php }}?>
            <tr>            	
            	<td></td>  
                <td></td>             
            </tr> 
            <tr>
            	<th></th>
            </tr>
            <tr>
            	<th></th>
            </tr>  
            <tr>
            	<td align="center" colspan="2">CAPTURA ELECTRONICA</td>               
            </tr>
            
            <tr>
            	<th></th>
            </tr> 
            
             <tr>
            	<td colspan="2" align="justify">DEBO Y PAGARE INCONDICIONALMENTE AL EMISOR Y SIN PROTESTO EL TOTAL DE ESTE PAGARE MAS LOS INTERESES POR CARGOS Y SERVICIOS. EN CASO DE MORA PAGARE LA TASA MAXIMA DE AUTORIZADA POR EL EMISOR.</td>
            </tr>
            <tr>
            	<th></th>
            </tr>
            <tr>
            	<th></th>
            </tr> 
            <tr>
            	<td colspan="2" align="justify"><p>DECLARO QUE EL PRODUCTO DE ESTA TRANSACCION NO SERA UTILIZADO EN ACTIVIDADES DE LAVADO DE DINERO Y ACTIVO (LEY 108).</p>
                	 <p>&nbsp;</p> 
                      <p>&nbsp;</p> 
                      
                       <p align="center">--------------------------------------------------------</p>
                           
                                     	 <p align="center">Firma Cliente</p>       
                </td>
            </tr>   
            <tr>
            	<td colspan="2" align="left">C.I.:__________________________________</td>
            </tr>  
             <tr>
            	<td colspan="2" align="left">Tlf.:__________________________________</td>
            </tr>  
             <tr>
            	<td colspan="2" align="justify">EL ESTABLECIMIENTO CERTIFICA QUE LA FIRMA DEL CLIENTE ES AUTENTICA</td>
            </tr>                                                                                        
           
		</table>
        </br>
        </br>
        </br>
        </br>
        </br>

	</div>
    
    
    
    
    
    
    
    
    
    
    
    
    	<div id="plus" class="facturacion">
		<table width="220px" align="center">
        	
            <tr>
            	<td align="center" colspan="2"><?php echo $lc_nombreRes; ?>	</td>               
            </tr> 
            <?php if($lc_impresion->fn_consultar('direccion_empresa',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
            <tr>
           
            	<td align="center" colspan="2"><?php echo $lc_row->direccion;?>	</td>               
            </tr>
             <?php }}?>
              <?php if($lc_impresion->fn_consultar('mid_tid',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
            <tr>
            	<td align="center" colspan="1">MID:  <?php echo $lc_row->rst_mid;?>	</td>     
                <td align="center" colspan="1">TID:  <?php echo $lc_row->est_tid;?>	</td>          
            </tr>
            <tr>
            	<th></th>
            </tr>
            <tr>
            	<th></th>
            </tr>
             <?php }}?>
             
            <tr>           
            	<td align="center" colspan="2">COMERCIO</td>               
            </tr>
            <tr>
            	<td align="center" colspan="2">VENTA</td>               
            </tr>
             <?php if($lc_impresion->fn_consultar('consulta_tarjeta',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
             <tr>
            	<td align="center" colspan="2"><?php echo $lc_row->fmp_descripcion; ?></td>               
            </tr>
             <?php }}?>
             <?php if($lc_impresion->fn_consultar('cabecera_respuesta',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
                    <tr>
            	<th></th>
            </tr>
            <tr>
            	<th></th>
            </tr>
            <tr>
            	<th></th>
            </tr>           
               <tr>
            	<td align="left" colspan="2">Lote:  <?php echo $lc_row->lote;?>	</td>               
            </tr>
             <tr>
            	<td align="left" colspan="2">Tarjeta:  <?php echo $lc_row->rsaut_numero_tarjeta;?>	</td>               
            </tr>
            <tr>
            	<td align="left">Aprobacion:  <?php echo $lc_row->rsaut_numero_autorizacion;?></td>               				
                <td align="right">Referencia <?php echo $lc_row->rsaut_secuencial_transaccion;?></td>
            </tr>
            <?php }}?>
            <?php 
             date_default_timezone_set("America/Lima");
                setlocale(LC_TIME, "spanish");
               // $lc_fecha = strftime("%d / %b / %Y  %hh:%mm" );
                $fecha= date("d/m/Y"); 
				$Hora= date("H:i:s"); 
			?>
            <tr>            	
            	<td align="left">Fecha:  <?php echo $fecha;?>	</td>  
                <td align="right">Hora:  <?php echo $Hora;?>	</td>             
            </tr> 
            <tr>
            	<th> <p>&nbsp;</p> </th>
            </tr>
            <?php if($lc_impresion->fn_consultar('valores_voucher',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
            <tr>            	
            	<td align="left">Base consumo 12</td> 
                <td align="left">: USD <?php echo $lc_row->BaseIva;?></td>                                  
            </tr>
            <tr>            	
              	<td align="left">Base consumo 0</td>
                <td align="left">: USD <?php echo $lc_row->BaseNOiva;?>	</td>                         
            </tr>
            <tr>            	
            	<td align="justify">Subtotal Consumo</td>     
                <td align="left">: USD <?php echo $lc_row->BaseIva;?></td>                    
            </tr>
            <tr>            	
            	<td align="left">IVA</td>  
                <td align="left">: USD <?php echo $lc_row->Iva;?></td>                  
            </tr>
            <tr>            	
            	<td align="left">TOTAL</td>
                <td align="left">: USD <?php echo $lc_row->MontoTotal;?></td>                             
            </tr>
             <?php }}?>
            <tr>            	
            	<td></td>  
                <td></td>             
            </tr> 
            <tr>
            	<th></th>
            </tr>
            <tr>
            	<th></th>
            </tr>  
            <tr>
            	<td align="center" colspan="2">CAPTURA ELECTRONICA</td>               
            </tr>
            
            <tr>
            	<th></th>
            </tr> 
            
             <tr>
            	<td colspan="2" align="justify">DEBO Y PAGARE INCONDICIONALMENTE AL EMISOR Y SIN PROTESTO EL TOTAL DE ESTE PAGARE MAS LOS INTERESES POR CARGOS Y SERVICIOS. EN CASO DE MORA PAGARE LA TASA MAXIMA DE AUTORIZADA POR EL EMISOR.</td>
            </tr>
            <tr>
            	<th></th>
            </tr>
            <tr>
            	<th></th>
            </tr> 
            <tr>
            	<td colspan="2" align="justify"><p>DECLARO QUE EL PRODUCTO DE ESTA TRANSACCION NO SERA UTILIZADO EN ACTIVIDADES DE LAVADO DE DINERO Y ACTIVO (LEY 108).</p>
                	 <p>&nbsp;</p> 
                      <p>&nbsp;</p> 
                      
                       <p align="center">--------------------------------------------------------</p>
                           
                                     	 <p align="center">Firma Cliente</p>       
                </td>
            </tr>   
            <tr>
            	<td colspan="2" align="left">C.I.:__________________________________</td>
            </tr>  
             <tr>
            	<td colspan="2" align="left">Tlf.:__________________________________</td>
            </tr>  
             <tr>
            	<td colspan="2" align="justify">EL ESTABLECIMIENTO CERTIFICA QUE LA FIRMA DEL CLIENTE ES AUTENTICA</td>
            </tr>                                                                                        
           
		</table>
        </br>
        </br>
        </br>
        </br>
        </br>

	</div>
    
    
    
    
    
    
    
    
    
    
    
    <?php }
		else{
	?>
    <div id="noAprobado" class="facturacion">
    <table width="220px" align="center">
    	<tr>
            	<td colspan="2" align="center">=========================================</td>
        </tr>  
        <tr>
            	<th colspan="2" align="center">**NO APROBADO**</th>
        </tr>  
        <tr>
            	<th colspan="2" align="center">PAGO MOVIL</th>                
        </tr> 
         <tr>
            	<td colspan="2"><p></p></td>
            </tr>
            
            <?php if($lc_impresion->fn_consultar('referencia_noaprobado',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
         <tr>
            	<td colspan="2"><p></p></td>
            </tr>
        <tr>                
            	<th colspan="2" align="center">REF: <?php echo $lc_row->rsaut_secuencial_transaccion;?></th>
        </tr>  
        		<?php }}?>
        <tr>
            	<th colspan="2" align="center">CC: 30001</th>
        </tr>
        <tr>
            	<td colspan="2" align="center">Supervisor  <?php  echo  $fecha_actual = date("d/m/Y H:i"); ?></th>
        </tr>
        <tr>
            	<td colspan="2" align="center">=========================================</td>
        </tr>  
        <?php if($lc_impresion->fn_consultar('detalle_noaprobado',$lc_datos))
					{ 	while($lc_row = $lc_impresion->fn_leerObjeto())
					{?>	
        <tr>
            	<td colspan="2"  align="left">Transaccion # <?php echo $lc_row->fpf_id;?></td>
               	<!--<td  colspan="1" align="left"><?php //echo $lc_row->fpf_id;?></td>-->
        </tr>
        <tr>
            	<td colspan="2"><p></p></td>
            </tr>
        <tr>
            	<td colspan="1"  align="left">   Catidad $ <?php echo $lc_row->fpf_total_pagar; ?></td>
               	<!--<td colspan="1" align="left"><?php //echo $lc_row->fpf_total_pagar;?></td>-->
        </tr>
        <?php }
					}?>
        <tr>
            	<td colspan="2" align="center">=========================================</td>
        </tr>  
        <tr>
            	<th colspan="2" align="center">**NO APROBADO**</th>
        </tr>
        <tr>
            	<td colspan="2" align="center">=========================================</td>
        </tr>   
    </table>    	
    </div>
	<?php 
		}
	?>
</body>
</html>