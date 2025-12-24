<?php

	session_start();
	//////////////////////////////////////////////////////////////////////////////////////
	////////DESARROLLADO POR: Jose Fernandez//////////////////////////////////////////////
	////////DESCRIPCION: Pantalla de reportes en pdf//////////////////////////////////////
	///////TABLAS INVOLUCRADAS: Cabecera_factura, formas_pago, forma_pago_factura/////////
	//////////////////////////control_estacion,usuarios///////////////////////////////////
	///////FECHA CREACION: 22/08/2014/////////////////////////////////////////////////////	
	//////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////
	///////FECHA ULTIMA MODIFICACION: 07-04-2015 /////////////////////////////////////////
	///////USUARIO QUE MODIFICO: Jimmy Cazaro ////////////////////////////////////////////
	///////DECRIPCION ULTIMO CAMBIO: Modificar los reportes //////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////	
	
	include_once("../system/conexion/clase_sql.php");
	include_once("../system/pdf/fpdf.php");
	include_once("../clases/clase_reportes.php");
	
	$lc_reportes= new reportes();
	$rst_id=$_SESSION['rstId'];
	$lc_opcion=$_GET['lc_opcion'];	
	$lc_datos[0]=$_GET['inicio'];	
	$lc_datos[1]=$_GET['fin'];	
	$lc_datos[2]=$rst_id;
	if ($lc_opcion=='cash')
	{
		$cajero=-1;
		//$lc_reportes->fn_consultar('reporte_cashOut', $lc_datos);
		//$total=0;
		//$total_Total=0;
		$fecha=-1;
		$pago=-1;
		$fecha_apertura='--';
		
		class PDF extends FPDF
		{
			function Header()
			{			
			
				//$cajero=-1;
				//$lc_reportes->fn_consultar('reporte_cashOut', $lc_datos);
				//$total=0;
				//$total_Total=0;	
				
				$rst_nombre = $_SESSION['rstNombre'];
//				
//				$this->SetFont('Arial','',14);
//				$this->Cell(170,5,$rst_nombre,0,0,'C');
//				$this->Cell(-20);
//				$this->Cell(10,7,'Desde:'.$_GET['inicio'],0,0,'L');
//				$this->Ln(10);
//				$this->Cell(150,5,'EMPLOYEE CASHOUT REPORT',0,0,'C');				
//				$this->Cell(1);
//				$this->Cell(10,7,'Hasta:'.$_GET['fin'],0,0,'L');
//				$this->Ln(10);	
				
				
//				$this->Image('../imagenes/cadena/'.$_SESSION['logo'],10,8,33);
//				//Arial bold 15
//				$this->SetFont('Arial','B',8);
//				//Move to the right
//				$this->Cell(80);
//				//Titulo
//				$this->Cell(10,7,'REPORTE DE CUADRE DE CAJA',0,0,'L');
//				$this->Cell(60);
//				$this->Cell(10,7,'Fecha:'.date( "d/m/Y", strtotime($lc_fecha)),0,0,'L');
//				$this->Ln(4);
//				$this->Cell(150);
//				$this->Cell(10,7,'Local:'.$lc_local,0,0,'L');
//				$this->Ln(4);
//				$this->Cell(150);
//				$this->Cell(10,7,'Usuario:'.htmlentities($_SESSION['usuario']),0,0,'L');

				$this->Image('../imagenes/cadena/'.$_SESSION['logo'],10,8,33);
				//Arial bold 15
				$this->SetFont('Arial','B',9);
				//Move to the right
				$this->Cell(80);
				//Titulo
				$this->Cell(10,7,'MAX POINT',0,0,'L');
				$this->Cell(60);
				$this->Cell(10,7,'Fecha: '.date('d-m-Y H:i:s'),0,0,'L');
				$this->Ln(4);
				$this->Cell(65);
				$this->Cell(8,7,'REPORTE DE CUADRE DE CAJA',0,0,'L');
				$this->Cell(77);
				$this->Cell(10,7,'Local: '.htmlentities($rst_nombre),0,0,'L');
				$this->Ln(4);
				$this->Cell(82);
				$this->Cell(68);
				$this->Cell(10,7,'Usuario: '.htmlentities($_SESSION['usuario']),0,0,'L');
				$this->Ln(4);
				$this->Cell(50);
				$this->Cell(12);
				$this->Cell(10,7,'Desde: '.$_GET['inicio'].' - Hasta: '.$_GET['fin'],0,0,'L');
				$this->Ln(8);
	//			$this->Cell(150);
	//			$this->Ln(10);				
			}
			
			function Footer()
			{
				//Position at 1.5 cm from bottom
				$this->SetY(-15);
				//Arial italic 8
				$this->SetFont('Arial','B',8);
				//Page number
				$this->Cell(0,10,'Página '.$this->PageNo(),0,0,'C');
			}		
			
			
		}//termina la clase pdf				
		$pdf=new PDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetFont('Times','',7);
		$cajero=-1;	
		$fecha_apertura='--';
		
		if($lc_reportes->fn_consultar('reporte_cashOut',$lc_datos))
		{
			if($lc_reportes->fn_numregistro() == 0)
			{
				$pdf->SetFont('Times','',12);
				$pdf->Ln(10);
				$pdf->Cell(195,7,"----No existen datos para la fecha seleccionada-----",'0',0,'C');
				$pdf->Ln(10);
			}
		$i=0;
		$total=0;
		$total_Total=0;
		while($lc_row = $lc_reportes->fn_leerobjeto())
		{
			$usuario=$lc_row->usr_descripcion;
			$fechaInicio=$lc_row->ctrc_fecha_inicio;
			$fechaSalida=$lc_row->ctrc_fecha_salida;
			$formaPago=$lc_row->fmp_descripcion;
			$transaccion=$lc_row->cfac_id;
			$totalTransaccion=$lc_row->cfac_total;
			$fechaFactura=$lc_row->cfac_fechacreacion;
			$arreglo[$i][0]=$usuario;
			$arreglo[$i][1]=$fechaInicio;
			$arreglo[$i][2]=$fechaSalida;
			$arreglo[$i][3]=$formaPago;
			$arreglo[$i][4]=$transaccion;
			$arreglo[$i][5]=$totalTransaccion;
			$arreglo[$i][6]=$fechaFactura;
			$total_Total += $totalTransaccion;
			$i++;
		}
		}
		$j=0;	
		$total = 0;
		$estacion_cerrada = '';
		for ($l=0;$l<$i;$l++ )
		{ 
			if(($cajero!=$arreglo[$l][0]) || ($fecha_apertura!=$arreglo[$l][1]))			
			{
				if(($cajero!=-1) || ($fecha_apertura != '--'))
				{
					$pdf->Ln(4);
					$pdf->Cell(115,7,'','0',0,'L');
					$pdf->Cell(30,7,'Total: ','0',0,'R');
					$pdf->Cell(30,7,$total,'T',0,'R');
					$pdf->Ln(5);
				}
				$pdf->SetFont('Arial','B',8);
				//$pdf->Ln(5);
				$pdf->Cell(200,7,htmlentities(strtoupper($arreglo[$l][0])),'0',0,'C');
				$pdf->Ln(6);
				$pdf->Cell(20,7,'Fecha Entrada: ',0,0,'L');
				$pdf->Cell(40,7,$arreglo[$l][1],0,0,'L');
				$pdf->Cell(20,7,'Fecha Salida: ',0,0,'L');
				if (trim($arreglo[$l][2])=='') 
				{ 
					$estacion_cerrada = 'ESTACION NO CERRADA';
				} 
				else 
				{ 
					$estacion_cerrada = $arreglo[$l][2];
				}
				$pdf->Cell(40,7,$estacion_cerrada,0,0,'L');				
				$pdf->Ln(7);
				$pdf->Cell(20,5,'# Transacciones','TB',0,'C');
				$pdf->Cell(40,5,'Fecha Factura','TB',0,'C');
				$pdf->Cell(55,5,'Forma Pago','TB',0,'C');
				$pdf->Cell(30,5,'Transacción','TB',0,'C');
				$pdf->Cell(30,5,'Total','TB',0,'C');
				$pdf->Ln(2);
				//205
				//$arreglo[$l][6]=0;
				$h=0;
				$estacion_cerrada = '';
				$fecha_apertura = $arreglo[$l][1];
				$total = 0;
			}
			$cajero=$arreglo[$l][0];
			//$total+=$arreglo[$l][6];
			$h++;
				$pdf->SetFont('Times','',8);				
				$pdf->Ln(4);
				$pdf->SetFont('Times','',8);
				$pdf->Cell(20,3,$h,0,0,'C');
				$pdf->Cell(40,3,$arreglo[$l][6],0,0,'C');
				$pdf->Cell(55,3,$arreglo[$l][3],0,0,'C');
				$pdf->Cell(30,3,$arreglo[$l][4],0,0,'C');
				$pdf->Cell(30,3,$arreglo[$l][5],0,0,'R');						
				//$pdf->Ln(3);
			// $total_Total+=$arreglo[$l][6];
				$total += $arreglo[$l][5];
		}//fin for
		$pdf->SetFont('Times','',8);
		$pdf->Ln(4);
		$pdf->Cell(115,7,'','0',0,'L');
		$pdf->Cell(30,7,'Total: ','0',0,'R');
		$pdf->Cell(30,7,$total,'T',0,'R');
		$pdf->Ln(5);
		$pdf->Cell(115,7,'','0',0,'L');
		$pdf->Cell(30,7,'Suma Total: ','0',0,'R');
		$pdf->Cell(30,7,$total_Total,'0',0,'R');
		$pdf->Ln(3);		

		$pdf->Output();

	}
	
	else if($lc_opcion=='ventas')
	{
		class PDF extends FPDF
		{
			function Header()
			{										
				$rst_nombre = $_SESSION['rstNombre'];
				
//				$this->SetFont('Arial','',14);
//				$this->Cell(170,5,$rst_nombre,0,0,'C');
//				$this->Cell(-20);
//				$this->Cell(10,7,'Desde:'.$_GET['inicio'],0,0,'L');				
//				$this->Ln(10);
//				$this->SetFont('Arial','',14);
//				$this->Cell(150,5,'REPORTE DE VENTAS POR PLU',0,0,'C');
//				$this->Cell(1);
//				$this->Cell(10,7,'Hasta:'.$_GET['fin'],0,0,'L');
//				$this->Ln(10);	

				$this->Image('../imagenes/cadena/'.$_SESSION['logo'],10,8,33);
				//Arial bold 15
				$this->SetFont('Arial','B',9);
				//Move to the right
				$this->Cell(80);
				//Titulo
				$this->Cell(10,7,'MAX POINT',0,0,'L');
				$this->Cell(60);
				$this->Cell(10,7,'Fecha: '.date('d-m-Y H:i:s'),0,0,'L');
				$this->Ln(4);
				$this->Cell(65);
				$this->Cell(8,7,'REPORTE DE VENTAS POR PLU',0,0,'L');
				$this->Cell(77);
				$this->Cell(10,7,'Local: '.htmlentities($rst_nombre),0,0,'L');
				$this->Ln(4);
				$this->Cell(82);
				$this->Cell(68);
				$this->Cell(10,7,'Usuario: '.htmlentities($_SESSION['usuario']),0,0,'L');
				$this->Ln(4);
				$this->Cell(50);
				$this->Cell(12);
				$this->Cell(10,7,'Desde: '.$_GET['inicio'].' - Hasta: '.$_GET['fin'],0,0,'L');
				$this->Ln(8);
			}
			
			function Footer()
			{
				//Position at 1.5 cm from bottom
				$this->SetY(-15);
				//Arial italic 8
				$this->SetFont('Arial','I',8);
				//Page number
				$this->Cell(0,10,'Página '.$this->PageNo(),0,0,'C');
			}		
			
			
		}//termina la clase pdf				
		$pdf=new PDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',8);
		$pdf->Ln(2);
		$pdf->Cell(20,5,'Plu','TB',0,'C');
		$pdf->Cell(60,5,'Descripción','TB',0,'C');
		$pdf->Cell(25,5,'Cantidad','TB',0,'C');
		$pdf->Cell(30,5,'% Cantidad','TB',0,'C');
		$pdf->Cell(25,5,'Valor','TB',0,'C');
		$pdf->Cell(25,5,'% Valor','TB',0,'C');
		$pdf->Ln(3);
		
		if($lc_reportes->fn_consultar('reporte_ventasPlu',$lc_datos))
		{
			if($lc_reportes->fn_numregistro() == 0)
			{
				$pdf->SetFont('Times','',12);
				$pdf->Ln(10);
				$pdf->Cell(195,7,"----No existen datos para la fecha seleccionada-----",'0',0,'C');
				$pdf->Ln(10);
			}
		$i=0;
		$total=0;
		$total_Total=0;
		while($lc_row = $lc_reportes->fn_leerobjeto())
		{
			$num_plu=$lc_row->plu_num_plu;
			$plu_nombre=$lc_row->plu_descripcion;
			$plu_cantidad=$lc_row->cantidad;
			$plu_total=$lc_row->total;
			$plu_porcentualCantidad=$lc_row->porcentajeCantidad;
			$plu_porcentualValor=$lc_row->porcentajeValor;
			$arreglo[$i][0]=$num_plu;
			$arreglo[$i][1]=$plu_nombre;
			$arreglo[$i][2]=$plu_cantidad;
			$arreglo[$i][3]=$plu_total;
			$arreglo[$i][4]=$plu_porcentualCantidad;
			$arreglo[$i][5]=$plu_porcentualValor;
			$i++;
		}
		}
		$j=0;	
		
		for ($l=0;$l<$i;$l++ )
		{ 		
				$pdf->Ln(4);					
				$pdf->SetFont('Times','',8);
				$pdf->Cell(20,3,$arreglo[$l][0],0,0,'C');
				$pdf->Cell(60,3,$arreglo[$l][1],0,0,'L');
				$pdf->Cell(25,3,$arreglo[$l][2],0,0,'R');
				$pdf->Cell(30,3,$arreglo[$l][3],0,0,'R');
				$pdf->Cell(25,3,$arreglo[$l][4],0,0,'R');
				$pdf->Cell(25,3,$arreglo[$l][5],0,0,'R');						
				//$pdf->Ln(4);			 
		}//fin for
		if($lc_reportes->fn_consultar('reporte_totalesventasPlu', $lc_datos))
	{
		$k=0;
		while($lc_row = $lc_reportes->fn_leerobjeto())
		{
			$totalCantidad=$lc_row->TotalCantidad;
			$totalValor=$lc_row->TotalValor;
			
			$pdf->Ln(5);
			$pdf->SetFont('Times','B',9);
			
			$pdf->Cell(20,7,"",0,0,'R');
			$pdf->Cell(60,7,"Totales:",0,0,'R');
			$pdf->Cell(25,7,$totalCantidad,'T',0,'R');		
			$pdf->Cell(30,7,"",0,0,'R');
			$pdf->Cell(25,7,$totalValor,'T',0,'R');
			
			
			//$arreglo2[$k][10]=$totalCantidad;
			//$arreglo2[$k][11]=$totalValor;
			$k++;
		}
	}
	/*
	if($lc_reportes->fn_consultar('reporte_totalesTaxes', $lc_datos))
	{
		$k=0;
		while($lc_row = $lc_reportes->fn_leerobjeto())
		{
			$grantot=$lc_row->total;
			
					$pdf->Ln(10);					
					$pdf->SetFont('Times','B',9);
					$pdf->Cell(120,7,"Total",'T',0,'R');
					
					$pdf->Cell(60,7,$grantot,'T',0,'C');							
			$k++;
		}
	}
	*/
		/*$pdf->Ln(20);					
		$pdf->SetFont('Times','B',9);
		$pdf->Cell(65,7,"Totales",0,0,'L');
		for ($l=0;$l<$i;$l++ )
		{ 						
			//$pdf->Cell(20,7,"Totales",0,0,'C');
			$pdf->Cell(32,7,$arreglo2[$l][10],0,0,'R');		
			$pdf->Cell(68,7,$arreglo2[$l][11],0,0,'R');						
				//$pdf->Ln(4);			 
		}*/
		$pdf->Output();
		
	}
	
	/*Empieza el reporte de transacciones*/
	else if($lc_opcion=='transacciones')
	{
		class PDF extends FPDF
		{
			function Header()
			{										
				$rst_nombre = $_SESSION['rstNombre'];
				
//				$this->SetFont('Arial','',14);
//				$this->Cell(170,5,$rst_nombre,0,0,'C');
//				$this->Cell(-20);
//				$this->Cell(10,7,'Desde:'.$_GET['inicio'],0,0,'L');
//				$this->Ln(10);
//				$this->SetFont('Arial','',14);
//				$this->Cell(150,5,'RESUMEN DE TRANSACCIONES',0,0,'C');
//				$this->Cell(1);
//				$this->Cell(10,7,'Hasta:'.$_GET['fin'],0,0,'L');
//				$this->Ln(10);	

				$this->Image('../imagenes/cadena/'.$_SESSION['logo'],10,8,33);
				//Arial bold 15
				$this->SetFont('Arial','B',9);
				//Move to the right
				$this->Cell(80);
				//Titulo
				$this->Cell(10,7,'MAX POINT',0,0,'L');
				$this->Cell(60);
				$this->Cell(10,7,'Fecha: '.date('d-m-Y H:i:s'),0,0,'L');
				$this->Ln(4);
				$this->Cell(65);
				$this->Cell(8,7,'RESUMEN DE TRANSACCIONES',0,0,'L');
				$this->Cell(77);
				$this->Cell(10,7,'Local: '.htmlentities($rst_nombre),0,0,'L');
				$this->Ln(4);
				$this->Cell(82);
				$this->Cell(68);
				$this->Cell(10,7,'Usuario: '.htmlentities($_SESSION['usuario']),0,0,'L');
				$this->Ln(4);
				$this->Cell(50);
				$this->Cell(12);
				$this->Cell(10,7,'Desde: '.$_GET['inicio'].' - Hasta: '.$_GET['fin'],0,0,'L');
				$this->Ln(8);
			}
			
			function Footer()
			{
				//Position at 1.5 cm from bottom
				$this->SetY(-15);
				//Arial italic 8
				$this->SetFont('Arial','I',8);
				//Page number
				$this->Cell(0,10,'Página '.$this->PageNo(),0,0,'C');
			}		
			
			
		}//termina la clase pdf				
		$pdf=new PDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',8);
		$pdf->Ln(2);
		$pdf->Cell(25,5,'Transacción','TB',0,'C');
		$pdf->Cell(50,5,'Factura','TB',0,'C');
		$pdf->Cell(25,5,'Fecha','TB',0,'C');
		$pdf->Cell(35,5,'Usuario','TB',0,'C');
		$pdf->Cell(25,5,'Venta Neta','TB',0,'C');
		$pdf->Cell(25,5,'Total','TB',0,'C');
		$pdf->Ln(3);
		
		if($lc_reportes->fn_consultar('reporte_transacciones',$lc_datos))
		{
			if($lc_reportes->fn_numregistro() == 0)
			{
				$pdf->SetFont('Times','',12);
				$pdf->Ln(10);
				$pdf->Cell(195,7,"----No existen datos para la fecha seleccionada-----",'0',0,'C');
				$pdf->Ln(10);
			}
		$i=0;
		$total=0;
		$total_Total=0;
		while($lc_row = $lc_reportes->fn_leerobjeto())
		{
			$trans=$lc_row->transaccion;
			$factura=$lc_row->cfac_numero_factura;
			$fecha=$lc_row->fechaCreacion;
			$usuario=$lc_row->usr_descripcion;
			$subtotal=$lc_row->Subtotal;
			$Total=$lc_row->total;
			
			$arreglo[$i][0]=$trans;
			$arreglo[$i][1]=$factura;
			$arreglo[$i][2]=$fecha;
			$arreglo[$i][3]=$usuario;
			$arreglo[$i][4]=$subtotal;
			$arreglo[$i][5]=$Total;
			$i++;
		}
		}
		$j=0;	
		
		for ($l=0;$l<$i;$l++ )
		{ 		
				$pdf->Ln(4);					
				$pdf->SetFont('Times','',8);
				$pdf->Cell(25,3,$arreglo[$l][0],0,0,'C');
				$pdf->Cell(50,3,$arreglo[$l][1],0,0,'C');
				$pdf->Cell(25,3,$arreglo[$l][2],0,0,'C');
				$pdf->Cell(35,3,$arreglo[$l][3],0,0,'C');
				$pdf->Cell(25,3,$arreglo[$l][4],0,0,'R');
				$pdf->Cell(25,3,$arreglo[$l][5],0,0,'R');						
				//$pdf->Ln(4);			 
		}//fin for
		if($lc_reportes->fn_consultar('reporte_totalesTransacciones', $lc_datos))
	{
		$k=0;
		while($lc_row = $lc_reportes->fn_leerobjeto())
		{
			$granSubtotal=$lc_row->granSub;
			$granTotal=$lc_row->granTotal;
			
					$pdf->Ln(5);					
					$pdf->SetFont('Times','B',9);
					$pdf->Cell(25,7,"",0,0,'L');
					$pdf->Cell(50,7,"",0,0,'L');
					$pdf->Cell(25,7,"",0,0,'L');
					$pdf->Cell(35,7,"Totales:",0,0,'R');
					$pdf->Cell(25,7,$granSubtotal,'T',0,'R');		
					$pdf->Cell(25,7,$granTotal,'T',0,'R');			
			$k++;
		}
	}			
		$pdf->Output();
		
	}
	/*fin del reporte de transacciones*/
	
	
	/*
	empieza reporte de taxes
	*/		
	else if($lc_opcion=='tax')
	{
		class PDF extends FPDF
		{
			function Header()
			{										
				$rst_nombre = $_SESSION['rstNombre'];
				
//				$this->SetFont('Arial','',14);
//				$this->Cell(170,5,$rst_nombre,0,0,'C');
//				$this->Cell(-20);
//				$this->Cell(10,7,'Desde:'.$_GET['inicio'],0,0,'L');
//				$this->Ln(10);
//				$this->SetFont('Arial','',14);
//				$this->Cell(150,5,'RESUMEN DE IMPUESTOS',0,0,'C');
//				$this->Cell(1);
//				$this->Cell(10,7,'Hasta:'.$_GET['fin'],0,0,'L');
//				$this->Ln(10);	
				
				$this->Image('../imagenes/cadena/'.$_SESSION['logo'],10,8,33);
				//Arial bold 15
				$this->SetFont('Arial','B',9);
				//Move to the right
				$this->Cell(80);
				//Titulo
				$this->Cell(10,7,'MAX POINT',0,0,'L');
				$this->Cell(60);
				$this->Cell(10,7,'Fecha: '.date('d-m-Y H:i:s'),0,0,'L');
				$this->Ln(4);
				$this->Cell(65);
				$this->Cell(8,7,'RESUMEN DE IMPUESTOS',0,0,'L');
				$this->Cell(77);
				$this->Cell(10,7,'Local: '.htmlentities($rst_nombre),0,0,'L');
				$this->Ln(4);
				$this->Cell(82);
				$this->Cell(68);
				$this->Cell(10,7,'Usuario: '.htmlentities($_SESSION['usuario']),0,0,'L');
				$this->Ln(4);
				$this->Cell(50);
				$this->Cell(12);
				$this->Cell(10,7,'Desde: '.$_GET['inicio'].' - Hasta: '.$_GET['fin'],0,0,'L');
				$this->Ln(8);				
			}
			
			function Footer()
			{
				//Position at 1.5 cm from bottom
				$this->SetY(-15);
				//Arial italic 8			
				$this->SetFont('Arial','I',8);
				//Page number
				$this->Cell(0,10,'Página '.$this->PageNo(),0,0,'C');
			}		
			
			//	
		}//termina la clase pdf				
		$pdf=new PDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',8);
		$pdf->Ln(2);
		$pdf->Cell(60,5,'Ventas Netas','TB',0,'C');
		$pdf->Cell(60,5,'IVA','TB',0,'C');
		$pdf->Cell(60,5,'Total Impuestos','TB',0,'C');
		$pdf->Ln(3);
		
		if($lc_reportes->fn_consultar('reporte_taxes',$lc_datos))
		{
			if($lc_reportes->fn_numregistro() == 0)
			{
				$pdf->SetFont('Times','',12);
				$pdf->Ln(10);
				$pdf->Cell(195,7,"----No existen datos para la fecha seleccionada-----",'0',0,'C');
				$pdf->Ln(10);
			}
		$i=0;
		$total=0;
		$total_Total=0;
		while($lc_row = $lc_reportes->fn_leerobjeto())
		{
			$trans=$lc_row->numTrans;
			$iva=$lc_row->totalImpuestos;	
			$neta=$lc_row->subtotal;	
			
			$arreglo[$i][0]=$trans;
			$arreglo[$i][1]=$iva;
			$arreglo[$i][2]=$neta;		
			$i++;
		}
		}
		$j=0;	
		
		for ($l=0;$l<$i;$l++ )
		{ 		
				$pdf->Ln(4);					
				$pdf->SetFont('Times','',9);
				$pdf->Cell(60,3,$arreglo[$l][2],0,0,'R');
				$pdf->Cell(60,3,$arreglo[$l][1],0,0,'R');
				$pdf->Cell(60,3,$arreglo[$l][1],0,0,'R');						
				//$pdf->Ln(4);			 
		}//fin for
		if($lc_reportes->fn_consultar('reporte_totalesTaxes', $lc_datos))
	{
		$k=0;
		while($lc_row = $lc_reportes->fn_leerobjeto())
		{
			$grantot=$lc_row->total;
			
					$pdf->Ln(5);					
					$pdf->SetFont('Times','B',9);
					$pdf->Cell(60,7,"",0,0,'R');
					$pdf->Cell(60,7,"Total",'T',0,'R');
					$pdf->Cell(60,7,$grantot,'T',0,'R');							
			$k++;
		}
	}			
		$pdf->Output();
		
	}

	//reporte de anulaciones
	else if($lc_opcion=='anulaciones')
	{
		$usuarioFactura=-1;		
		$total=0;
		$total_Total=0;
		class PDF extends FPDF
		{
			function Header()
			{										
				$rst_nombre = $_SESSION['rstNombre'];
				
//				$this->SetFont('Arial','',14);
//				$this->Cell(170,5,$rst_nombre,0,0,'C');
//				$this->Cell(-20);
//				$this->Cell(10,7,'Desde:'.$_GET['inicio'],0,0,'L');
//				$this->Ln(10);
//				$this->SetFont('Arial','',14);
//				$this->Cell(150,5,'REPORTE DE ANULACIONES',0,0,'C');
//				$this->Cell(1);
//				$this->Cell(10,7,'Hasta:'.$_GET['fin'],0,0,'L');
//				$this->Ln(10);		


				$this->Image('../imagenes/cadena/'.$_SESSION['logo'],10,8,33);
				//Arial bold 15
				$this->SetFont('Arial','B',9);
				//Move to the right
				$this->Cell(80);
				//Titulo
				$this->Cell(10,7,'MAX POINT',0,0,'L');
				$this->Cell(60);
				$this->Cell(10,7,'Fecha: '.date('d-m-Y H:i:s'),0,0,'L');
				$this->Ln(4);
				$this->Cell(65);
				$this->Cell(8,7,'REPORTE DE ANULACIONES',0,0,'L');
				$this->Cell(77);
				$this->Cell(10,7,'Local: '.htmlentities($rst_nombre),0,0,'L');
				$this->Ln(4);
				$this->Cell(82);
				$this->Cell(68);
				$this->Cell(10,7,'Usuario: '.htmlentities($_SESSION['usuario']),0,0,'L');
				$this->Ln(4);
				$this->Cell(50);
				$this->Cell(12);
				$this->Cell(10,7,'Desde: '.$_GET['inicio'].' - Hasta: '.$_GET['fin'],0,0,'L');
				$this->Ln(8);	
			}
			
			function Footer()
			{
				//Position at 1.5 cm from bottom
				$this->SetY(-15);
				//Arial italic 8			
				$this->SetFont('Arial','I',8);
				//Page number
				$this->Cell(0,10,'Página '.$this->PageNo(),0,0,'C');
			}		
			
			//	
		}//termina la clase pdf				
		$pdf=new PDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',10);		
		//$pdf->Ln(7);
		//$usuarioFactura=-1;		
		
		if($lc_reportes->fn_consultar('reporte_anulaciones',$lc_datos))
		{
			if($lc_reportes->fn_numregistro() == 0)
			{
				$pdf->Ln(3);
				$pdf->SetFont('Times','',12);
				$pdf->Ln(10);
				$pdf->Cell(195,7,"----No existen datos para la fecha seleccionada-----",'0',0,'C');
				$pdf->Ln(10);
			}		
		$i=0;
		$total=0;
		$total_Total=0;
		$totall=0;
		$usuarioFactura=-1;
		$transaccion_Anulacion=-1;
		$cantidadParcial = 0;
		$pedidoParcial = 0;		
		while($lc_row = $lc_reportes->fn_leerobjeto())
		{
			$totall=0;
			$usuarioAutoriza=$lc_row->autoriza;
			$usuarioDescripcion=$lc_row->usr_descripcion;
			$motivoAnulacion=$lc_row->mtv_descripcion;	
			$transaccionAnulacion=$lc_row->transaccion;
			$detallePlu=$lc_row->plu_descripcion;
			$cantidadPedido=$lc_row->dncre_cantidad;
			$totalPedido=$lc_row->total;
			$fechaFactura=$lc_row->cfac_fechacreacion;
			$detalleBruto=$lc_row->detalle_bruto;
			
			$arreglo[$i][0]=$usuarioDescripcion;
			$arreglo[$i][1]=$motivoAnulacion;
			$arreglo[$i][2]=$transaccionAnulacion;		
			$arreglo[$i][3]=$detallePlu;		
			$arreglo[$i][4]=$cantidadPedido;
			$arreglo[$i][5]=$totalPedido;
			$arreglo[$i][7]=$usuarioAutoriza;
			$arreglo[$i][8]=$fechaFactura;
			$arreglo[$i][9]=$detalleBruto;
			
			//$total=0;
			//$arreglo[$i][5]=0;//+=$total;	
			$totall+=$totalPedido;			
			$i++;
		}		
					
			
		}
		$j=0;	
		$j++;	
		
		for ($l=0;$l<$i;$l++ )
		{ 
		
			//$totall+=$arreglo[$i][5];
			if(($usuarioFactura!=$arreglo[$l][0]) || ($transaccion_Anulacion != $arreglo[$l][2]))
			{
				if($usuarioFactura!=-1 || $transaccion_Anulacion != -1)
				{	
					
					$pdf->Ln(5);	
					$pdf->SetFont('Times','B',9);
					$pdf->Cell(30,7,"",'0',0,'R');
					$pdf->Cell(35,7,"",'0',0,'R');
					$pdf->Cell(75,7,"Total:",'0',0,'R');
					$pdf->Cell(20,7,$cantidadParcial,'T',0,'C');
					$pdf->Cell(30,7,round($pedidoParcial,2),'T',0,'R');					
					$pdf->Ln(7);	
					$cantidadParcial = 0;
					$pedidoParcial = 0;					
				}
				
				$pdf->SetFont('Arial','B',8);
				$pdf->Ln(2);
				$pdf->Cell(30,5,"Autoriza: ",'0',0,'L');
				$pdf->Cell(35,5,htmlentities(strtoupper($arreglo[$l][7])),'0',0,'L');
//				$pdf->SetFont('Arial','B',8);
//				$pdf->Ln(10);
				$pdf->Cell(30,5,"Cajero: ",'0',0,'L');
				$pdf->Cell(40,5,htmlentities(strtoupper($arreglo[$l][0])),'0',0,'L');
				$pdf->Ln(4);
				$pdf->Cell(30,5,"Motivo Anulación: ",'0',0,'L');
				$pdf->Cell(80,5,htmlentities($arreglo[$l][1]),'0',0,'L');
				$pdf->Ln(6);
				$pdf->Cell(30,5,'Fecha Factura','TB',0,'C');
				$pdf->Cell(35,5,'# Transacción','TB',0,'C');
				$pdf->Cell(75,5,'Descripción','TB',0,'C');
				$pdf->Cell(20,5,'Cantidad','TB',0,'C');
				$pdf->Cell(30,5,'Total','TB',0,'C');	
				$pdf->Ln(3);
				//$arreglo[$l][6]=0;
				$total=0;
				$j=0;			
				//$totall=0;
				//$i=0;//nuevo

			}
			$cantidadParcial += $arreglo[$l][4];
			$pedidoParcial += $arreglo[$l][9];
			$transaccion_Anulacion = $arreglo[$l][2];
			
			$usuarioFactura=$arreglo[$l][0];
			$total+=$totalPedido;//$arreglo[$l][5];
			//$i++;
				$pdf->SetFont('Times','',9);				
				$pdf->Ln(4);
				$pdf->SetFont('Times','',9);
				$pdf->Cell(30,3,$arreglo[$l][8],0,0,'L');
				$pdf->Cell(35,3,$arreglo[$l][2],0,0,'C');
				$pdf->Cell(75,3,$arreglo[$l][3],0,0,'L');
				$pdf->Cell(20,3,$arreglo[$l][4],0,0,'C');
				$pdf->Cell(30,3,round($arreglo[$l][9],2),0,0,'R');						
				//$pdf->Ln(2);
				$total_Total+=$totalPedido;//[$l][5]; 			 
				
		}//fin for
		
		
				/*if($usuarioFactura!=-1)
				{*/
					$totalPedido+=$totalPedido;
					$total_TotalAnula+=$totalPedido;
					$pdf->SetFont('Times','B',9);
					$pdf->Ln(5);					
					$pdf->Cell(30,7,"",'0',0,'R');
					$pdf->Cell(35,7,"",'0',0,'R');
					$pdf->Cell(75,7,"Total:",'0',0,'R');
					$pdf->Cell(20,7,$cantidadParcial,'T',0,'C');
					$pdf->Cell(30,7,round($pedidoParcial,2),'T',0,'R');
					$pdf->Ln(2);									 
				/*}	*/				
		
		
		
		$pdf->Output();
		
	}//fin reporte anulaciones
?>