<?php 
session_start();
/////////////////////////////////////////////////////////////////
////////DESARROLLADO POR:Jose Fernandez//////////////////////////
////////DESCRIPCION: Impresion Resumen de Ventas/////////////////
////////TABLAS		: ARQUEO_CAJA,BILLETE_ESTACION,//////////////
//////////////////////////CONTROL_ESTACION,ESTACION//////////////
//////////////////////////BILLETE_DENOMINACION///////////////////
////////FECHA CREACION	: 04/02/2014/////////////////////////////
/////////////////////////////////////////////////////////////////
include_once("../system/conexion/clase_sql.php");	
include_once ("../clases/clase_desmontadoCajero.php");
$lc_apertura = new desmontaCaja();



$lc_control=$_GET['ctrc'];
$lc_datos[0]=$lc_control;

$hora = date("H:i:s");						
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<link rel="stylesheet" type="text/css" href="../css/style_impresion_factura.css" media="screen"/>
<link rel="stylesheet" type="text/css" href="../css/style_impresion_factura.css" media="print"/>

</head>
<body>
  <div class="facturacion">
 <table width="220px" align="center">
 		 <?php
 			$lc_apertura->fn_consultar('cabecera_reporte_desmontado',$lc_datos);
			$lc_row = $lc_apertura->fn_leerObjeto();
			$empresa=$lc_row->emp_nombre;
			$cajero=$lc_row->usr_usuario;
		?>
		<tr align="center">
    		<th align="center" colspan="2">
            <?php
        		echo $empresa;
			?>
        	</th>
      	</tr>
        <tr align="center">
    		<td colspan="2" style="padding-top:15px;">
            RESUMEN DE VENTAS
        	</td>
      	</tr>
        <tr align="center">
    		<td colspan="2" style="padding-top:15px;">
            CAJERO:<?php echo " ".$cajero; ?>
        	</td>
      	</tr>
        <tr>
        	<td align="right" width="auto">
            Hora:
        	</td>
    		<td align="left">
            <?php
        		echo $hora;
			?>
        	</td>
      	</tr>
<?php


					if($lc_apertura->fn_consultar('reporteDesmontado',$lc_datos))
					{ 	$i=0;
						$j=1;
						while($lc_row = $lc_apertura->fn_leerObjeto())
						{
?>				
                <tr><td colspan="2">=======================</td>
                <tr><td id="fila_<?php echo $i.$j;?>"><?php echo $lc_row->fmp_descripcion;?></td></tr><?php $j++;?>
        		<tr><td>Pos calculado: </td><td><?php echo number_format($lc_row->fpf_total_pagar,2,".","");?>	</td></tr>
				<tr><td>Pos declarado: </td><td><?php echo  number_format($lc_row->arc_valor,2,".","");?></td></tr>                              
    		   <?php 
						}
					} 	
					
					if($lc_apertura->fn_consultar('totalesReporte',$lc_datos))
					{ 	/*$i=0;
						$j=1;*/
						while($lc_row = $lc_apertura->fn_leerObjeto())
						{							
				?>
                		<tr><td colspan="2">==================</td>
                		<tr><td>Mas o Menos: </td><td><?php echo $lc_row->diferencia;?></td></tr>
                        <tr><td colspan="2">==================</td>
                <?php 
						}
					}					
				?>
                        
              
  </table>
</div>
</body>
</html>






