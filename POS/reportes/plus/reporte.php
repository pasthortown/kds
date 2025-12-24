<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
	
	session_start();
	
	/////////////////////////////////////////////////////////////////////////////////////
	////////DESARROLLADO POR: Jorge Tinoco //////////////////////////////////////////////
	////////DESCRIPCION: Pantalla de reportes ///////////////////////////////////////////
	///////TABLAS INVOLUCRADAS: Cabecera_factura, formas_pago, forma_pago_factura ///////
	//////////////////////////control_estacion,usuarios//////////////////////////////////
	///////FECHA CREACION: 18/05/2015 ///////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////

	include_once("../../system/conexion/clase_sql.php");
	include_once("../../clases/clase_reportesVentaPlu.php"); 
	
	$lc_reportes = new reportes();
	$usr_id = $_SESSION['usuarioId'];
	$cdn_id = $_SESSION['cadenaId'];
	$rst_id = $_SESSION['rstId'];
	$est_id = $_SESSION['estacionId'];
	$rst_nombre = $_SESSION['rstNombre'];
	
	if($tipo=='excel'){
		header("Content-type: application/vnd.ms-excel");
	}
	
	$lc_opcion = $_GET['lc_opcion'];
	$tipo = $_GET['visualizar'];
	$inicioFecha = $_GET['inicio'];
	$finFecha = $_GET['fin'];

	$lc_datos[0] = $rst_id;
	$lc_datos[1] = $inicioFecha;
	$lc_datos[2] = $finFecha;
	
?>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Cuadre de Caja</title>
	<link rel="stylesheet" type="text/css" href="../../css/est_reporte.css"/>
</head>

<body>

<div>
    	<!-- CABECERA REPORTE -->
        <div id="cabecera_reporte">
            <table>
                <tr>
                    <td rowspan="3" width="20%"><img src=" ../../imagenes/cadena/<?php echo $_SESSION['logo'];?>" width="140" height="78"></td>
                    <td><p class="titulo_cabecera"><strong>MAX POINT</strong></p></td>
                    <td width="22%"><p class="titulo_informativo" style="padding: 0; margin: 0;"><strong>Fecha: </strong><?php echo date("d/m/Y H:m:s");?></p></td>
                </tr>
                <tr>
                    <td><p class="titulo_cabecera"><strong>REPORTE DE VENTAS POR PLU</strong></p></td>
                    <td><p class="titulo_informativo" style="padding: 0; margin: 0;"><strong>Local: </strong><?php echo htmlentities($rst_nombre);?></p></td>
                </tr>
                <tr>
                    <td><p class="titulo_informativo" style="padding: 0; margin: 0; text-align: center;"><b>Desde:</b> <?php echo $lc_datos[1];?> - <b>Hasta:</b> <?php echo $lc_datos[1];?></p></td>
                    <td><p class="titulo_informativo" style="padding: 0; margin: 0;"><strong>Usuario: </strong><?php echo htmlentities($_SESSION['usuario']);?></p></td>
                </tr>
            </table>
        </div>
        	
        <!-- DETALLE INFORME -->
        <div id="contenido_informe">
			
<?php    
	if ($lc_opcion=='ventas'){
?>
	
        <table class="contenido_reporte" width="80%" border="0" align="center">
            <tr class="estilo_subcabecera">                    	
                <td align="center" class="solo_bordes linea_inferior"><B>Plu</B></td>
                <td align="center" class="solo_bordes linea_inferior"><B>Descripcion</B></td>
                <td align="center" class="solo_bordes linea_inferior"><B>Cantidad</B></td>
                <td align="center" class="solo_bordes linea_inferior"><B>% Cantidad</B></td>
                <td align="center" class="solo_bordes linea_inferior"><B>Valor</B></td>           
                <td align="center" class="solo_bordes linea_inferior"><B>% Valor</B></td>             
             </tr>
 <?php
 
	$lc_reportes->fn_consultar('reporte_ventasPlu', $lc_datos);
    $lc_row = $lc_reportes->fn_numregistro() > 0 ? $lc_reportes->fn_leerobjeto() : array();
 	while($lc_row ){
		$num_plu=$lc_row->plu_num_plu;
		$plu_nombre=$lc_row->plu_descripcion;
		$plu_cantidad=$lc_row->cantidad;
		$plu_total=$lc_row->total;
		$plu_porcentualCantidad=$lc_row->porcentajeCantidad;
		$plu_porcentualValor=$lc_row->porcentajeValor;
		$totalCantidad=$lc_row->TotalCantidad;
		$totalvalor=$lc_row->TotalValor;
			
 ?>     	
     
            <tr>                    	
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
			<tr>                    	
                <td class="estilo_subcabecera" align="right" colspan="2"><B>Total:</B></td>                                     
                <td class="estilo_subcabecera" align="right"><B><?php echo $totalCantidad; ?></B></td>
                <td class="estilo_subcabecera" align="left"><B></B></td>
                <td class="estilo_subcabecera" align="right"><B><?php echo $totalvalor; ?></B></td>
                <td class="estilo_subcabecera" align="left"><B></B></td>
    		</tr>
 		</table>
<?php
	}
?>
            
	</div>

<?php
	if($tipo!='excel'){
?>
		<!-- OPCIONES DE REPORTE -->
        <div id="opciones_reporte">
			<table>
				<tr>
					<td align="center">
                    	<input inputmode="none"  id="btn_imp" type="button" onclick="window.print();" value="Imprimir"/>
                    </td>
					<td align="center">
                    	<input inputmode="none"  id="btn_can" type="button" onclick="window.close();" value="Cerrar"/>
                    </td>
				</tr>
			</table>
        </div>
<?php
	}
?>
		
        
	<!-- FIN CONTENEDOR REPORTE -->
	</div>
	
</body>
</html>