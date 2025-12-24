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
	include_once("../../clases/clase_reportesCashOut.php"); 
	
	$lc_reportes = new reportes();
	$usr_id=$_SESSION['usuarioId'];
	$cdn_id=$_SESSION['cadenaId'];
	$rst_id=$_SESSION['rstId'];
	$est_id=$_SESSION['estacionId'];
	$lc_opcion = $_GET['lc_opcion'];
	$tipo = $_GET['visualizar'];
	
	if($tipo=='excel'){
		header("Content-type: application/vnd.ms-excel");
	}
	
	$rst_nombre = $_SESSION['rstNombre'];
	$inicioFecha = $_GET['inicio'];
	$finFecha = $_GET['fin'];	

	$lc_datos[0] = $inicioFecha;
	$lc_datos[1] = $finFecha;
	$lc_datos[2] = $rst_id;
	
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
                    <td><p class="titulo_cabecera"><strong>REPORTE DE CUADRE DE CAJA - CASHOUT</strong></p></td>
                    <td><p class="titulo_informativo" style="padding: 0; margin: 0;"><strong>Local: </strong><?php echo htmlentities($rst_nombre);?></p></td>
                </tr>
                <tr>
                    <td><p class="titulo_informativo" style="padding: 0; margin: 0; text-align: center;"><b>Desde:</b> <?php echo $lc_datos[0];?> - <b>Hasta:</b> <?php echo $lc_datos[1];?></p></td>
                    <td><p class="titulo_informativo" style="padding: 0; margin: 0;"><strong>Usuario: </strong><?php echo htmlentities($_SESSION['usuario']);?></p></td>
                </tr>
            </table>
        </div>
        	
        <!-- DETALLE INFORME -->
        <div id="contenido_informe">
<?php
	//Comienzo del armado de los reportes dependiendo del tipo
	if ($lc_opcion=='cash'){
		$cajero = -1;
		$lc_reportes->fn_consultar('reporte_cashOut', $lc_datos);
        $lc_row = $lc_reportes->fn_numregistro() > 0 ? $lc_reportes->fn_leerobjeto() : array();
		$total = 0;
		$total_Total = 0;
		$fecha = -1;
		$pago = -1;
		$fecha_apertura = '--';
		$numero = 0;
?>

		<table class="contenido_reporte" width="94%" border="0" align="center">
        	<tr><td width="16%"></td><td width="16%"></td><td width="16%"></td><td width="16%"></td><td width="16%"></td><td width="16%"></td><tr>
<?php
			if($lc_row){
				$usuario = $lc_row->usr_descripcion . "(" .$lc_row->usr_usuario . ")";
				$fechaInicio = $lc_row->ctrc_fecha_inicio;
				$fechaSalida = trim($lc_row->ctrc_fecha_salida);
				$formaPago = $lc_row->fmp_descripcion;
				$transaccion = $lc_row->cfac_id;
				$totalTransaccion = $lc_row->cfac_total;
				$fechaFactura = $lc_row->cfac_horacreacion;
				$total_Total += $totalTransaccion;
?>
			<tr class="estilo_usuario" align="center">
				<td align='left' colspan="6"><?php echo htmlentities(strtoupper($usuario)); ?></td>
			</tr>
			<tr class="estilo_fechasesion">
				<td align='left' colspan="2"><b>Inicio Cajero:</b><?php echo $fechaInicio;?></td>
				<td align='left' colspan="2"><b>Fin Cajero:</b>
<?php 
					if(trim($fechaSalida) != ''){
						echo $fechaSalida;
					}else{
						echo 'ESTACION NO CERRADA';
					}
?>
				</td>
				<td colspan="2"></td>
			</tr>
			<tr>
                <td colspan="6" align="left"><?php echo $formaPago = $lc_row->fmp_descripcion; ?></td>
            </tr>
            <tr class="estilo_subcabecera">
            	<td colspan="1"></td>
            	<th class="solo_bordes" align="center">Transacci&oacute;n</th>
				<th class="solo_bordes" align="center">Hora</th>
				<th class="solo_bordes" align="center">Total</th>
                <td colspan="2"></td>
			</tr>
            <tr class="estilo_detalle2">
	            <td colspan="1"></td>
            	<td class="solo_bordes" align="center"><?php echo $transaccion;?>&nbsp;</td>
				<td class="solo_bordes" align="center"><?php echo $fechaFactura;?>&nbsp;</td>
				<td class="solo_bordes" align="right"><?php echo $totalTransaccion;?>&nbsp;</td>
                <td colspan="2"></td>
			</tr>
<?php
				$cajero = $usuario;
				$fecha_apertura = $fechaInicio;
				$total += $totalTransaccion;
				$forma_anterior = $formaPago;
				
				while($lc_row = $lc_reportes->fn_leerobjeto()){
					$usuario = $lc_row->usr_descripcion . "(" .$lc_row->usr_usuario . ")";
					$fechaInicio = $lc_row->ctrc_fecha_inicio;
					$fechaSalida = trim($lc_row->ctrc_fecha_salida);
					$formaPago = $lc_row->fmp_descripcion;
					$transaccion = $lc_row->cfac_id;
					$totalTransaccion = $lc_row->cfac_total;
					$fechaFactura = $lc_row->cfac_horacreacion;
					$total_Total += $totalTransaccion;
					
					if($cajero!=$usuario){
						$cajero = $usuario;
						$fecha_apertura = $fechaInicio;
?>
			<tr>
            	<td colspan="2"></td>
				<th class="estilo_subcabecera" align='right'><b>Total:</b></th> 
				<td class="estilo_subcabecera" añ8lign='right'><b><?php echo $total; ?></b></td>
                <td colspan="2"></td>
			</tr>
<?php		
						$total=0;
						$i=0;
					}else{
						if($formaPago!=$forma_anterior){
							$forma_anterior = $formaPago;
?>
            <tr>
                <th colspan="2"></th>
                <th class="estilo_subcabecera" align='right'><b>Total:</b></th> 
                <td class="estilo_subcabecera" align='right'><b><?php echo $total; ?></b></td>
                <td colspan="2"></td>
		    <tr>
            <tr>
                <td colspan="6" align="left"><?php echo $formaPago = $lc_row->fmp_descripcion; ?></td>
            </tr>
            <tr class="estilo_subcabecera">
                <td colspan="1"></td>
                <th class="solo_bordes" align="center">Transacci&oacute;n</th>
                <th class="solo_bordes" align="center">Hora</th>
                <th class="solo_bordes" align="center">Total</th>
                <td colspan="2"></td>
            </tr>
<?php
						$total = 0;
						}
						$total += $totalTransaccion;
					}
?>						
			<tr class="estilo_detalle2">
	            <td colspan="1"></td>
            	<td class="solo_bordes" align="center"><?php echo $transaccion;?>&nbsp;</td>
				<td class="solo_bordes" align="center"><?php echo $fechaFactura;?>&nbsp;</td>
				<td class="solo_bordes" align="right"><?php echo $totalTransaccion;?>&nbsp;</td>
                <td colspan="2"></td>
			</tr>
<?php
			}//fin while
		}
?>      
        <tr>
            <th colspan="2"></th>
            <th class="estilo_subcabecera" align='right'><b>Total:</b></th> 
            <td class="estilo_subcabecera" align='right'><b><?php echo $total; ?></b></td>
            <td colspan="2"></td>
        <tr>
        <tr>
            <td colspan="6" class="separador">&nbsp;</td>
        </tr>
        <tr>
            <th colspan="2"></th>
            <th class="estilo_subcabecera" align='right'><b>Suma Total:</b></th> 
            <td class="estilo_subcabecera" align='right'><b><?php echo $total_Total; ?></b></td>
            <td colspan="2"></td>
        <tr>        
<?php
	}
?>
		</table>    
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
<?php
	}
?>
		</div>
        
	<!-- FIN CONTENEDOR REPORTE -->
	</div>
	
</body>
</html>