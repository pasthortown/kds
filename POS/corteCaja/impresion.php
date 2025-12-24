<?php 
session_start();
//include ("../../seguridades/seguridad.inc");
/////////////////////////////////////////////////////////////////
////////DESARROLLADO POR:Jose Fernandez//////////////////////////
////////DESCRIPCION: Impresion Resumen de Ventas/////////////////
////////TABLAS		: ARQUEO_CAJA,BILLETE_ESTACION,//////////////
//////////////////////////CONTROL_ESTACION,ESTACION//////////////
//////////////////////////BILLETE_DENOMINACION///////////////////
////////FECHA CREACION	: 04/02/2014/////////////////////////////
/////////////////////////////////////////////////////////////////
exec(echo ^ T);
include("../system/conexion/clase_sql.php");
//include("../clases/clase_seguridades.php");
include_once ("../clases/clase_desmontadoCajero.php");
$lc_apertura = new desmontaCaja();
$lc_cadena=$_SESSION['cadenaNombre'];
$lc_rest = $_SESSION['rstId']; 	
$lc_usuarioId=$_SESSION['usuarioId'];
$hora = date("H:i:s");						
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<object id="factory" classid="clsid:1663ed61-23eb-11d2-b92f-008048fdd814"></object>
<script language="javascript">

 window.print();
// window.close();

</script>
</head>
<body>
  <div style="width:425px;">
  <table width="100%">
		<tr align="center">
    		<th align="center">
            <?php
        		echo $lc_cadena;
			?>
        	</th>
      	</tr>
        <tr align="center">
    		<td align="center">
            Resumen de Ventas
        	</td>
      	</tr>
        <tr>
        	<td width="auto">
            Hora:
        	</td>
    		<td align="left">
            <?php
        		echo $hora;
			?>
        	</td>
      	</tr>
<?php
//echo "Restaurante ".$lc_rest;
$lc_datos[0]=$lc_rest;
$lc_datos[1]=$lc_usuarioId;

					if($lc_apertura->fn_consultar('reporteDesmontado',$lc_datos))
					{ 	$i=0;
						$j=1;
						while($lc_row = $lc_apertura->fn_leerObjeto())
						{
?>				
                <tr><td colspan="2">=======================</td>
                <tr><td id="fila_<?php echo $i.$j;?>"><?php echo $lc_row->fmp_descripcion;?></td></tr><?php $j++;?>
        		<tr><td>Pos cobrado: </td><td><?php echo $lc_row->fpf_total_pagar;?>	</td></tr>
				<tr><td>Pos declarado: </td><td><?php echo $lc_row->arc_valor;?></td></tr>               
               
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
                        
                <tr>
          <td align="center"><input inputmode="none"  type="button" onClick="window.print();" value="Imprimir" />
          <input inputmode="none"  type="button" onClick="window.close();" value="Cancelar" /></td>
                </tr>
              
  </table>
</div>
</body>
</html>






