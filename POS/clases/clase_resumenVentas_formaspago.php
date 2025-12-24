<?php
////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jorge Tinoco //////////////////////////////////
///////DESCRIPCION: Pantalla de Resumen de Ventas //////////////////////
///////FECHA CREACION: 19-10-2015///////////////////////////////////////
////////////////////////////////////////////////////////////////////////

class resumen extends sql{
	
	function _construct(){
		parent ::_construct();
	}
	
	function fn_consultar($lc_sqlQuery,$lc_datos){
		switch($lc_sqlQuery){
			case "cargarResumenVentasFacturas":
				$lc_sql="EXEC reporte].[VNT_resumen_facturas_cerradas_formaspago ".$lc_datos[0].", '".$lc_datos[1]."'";
					if($this->fn_ejecutarquery($lc_sql)){
						while($row = $this->fn_leerarreglo()){
							$this->lc_regs[] = array("usuario"=>$row['usuario'],
													"Efectivo"=>$row['Efectivo'],
													"Tarjetas"=>$row['Tarjetas'],
													"RetencionI"=>$row['RetencionI'],
													"RetencionF"=>$row['RetencionF'],
													"Transacciones"=>$row['Transacciones'],
													"Ticket"=>$row['Ticket'],
													"Total"=>$row['Total'],
                                                                                                        "PAYPHONE"=>$row['PAYPHONE'],
                                                                                                        "CHEQUE"=>$row['CHEQUE'],
													"Cupones"=>$row['Cupones'],
                                                                                                        "EMPLEADO"=>$row['EMPLEADO'],
													"fecha_inicio"=>$row['fecha_inicio'],
													"fecha_salida"=>$row['fecha_salida']);
						}
					}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			case "cargarConfiguracionResumenVentas":
				$lc_sql="EXEC reporte.VNT_resumen_ventas_configuracion ".$lc_datos[0].", '".$lc_datos[1]."'";
					if($this->fn_ejecutarquery($lc_sql)){
						while($row = $this->fn_leerarreglo()){
							$this->lc_regs[] = array("prd_id"=>$row['prd_id'],
													"fecha"=>$row['fecha'],
													"hora"=>$row['hora']);
						}
					}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
			case "cargarAccesosPerfil":
				$lc_sql="config.USP_verificanivelacceso '".$lc_datos[0]."', '".$lc_datos[1]."', '".$lc_datos[2]."'";
				if($this->fn_ejecutarquery($lc_sql)){ 
					while($row = $this->fn_leerarreglo()){
						$this->lc_regs[] = array("acc_id"=>$row['acc_id'],
												 "acc_descripcion"=>trim($row['acc_descripcion']));
						}
					}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);	
			break;
			case "obtenerMesa":
				$lc_query="EXEC pedido.ORD_asignar_mesaordenpedido ".$lc_datos[0];
				if($this->fn_ejecutarquery($lc_query)){
					while($row = $this->fn_leerarreglo()){
						$this->lc_regs['mesa_asignada'] = $row['mesa_asignada'];
					}
				}
				$this->lc_regs['str'] = $this->fn_numregistro();
				return json_encode($this->lc_regs);
			break;
		}
	}
}

