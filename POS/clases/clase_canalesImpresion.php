<?php
///////////////////////////////////////////////////////////
///////DESARROLLADO POR: Cristhian Castro ////////////////////
///////DESCRIPCION: ///////////////////
///////TABLAS INVOLUCRADAS: Menu_Agrupacion, ////////
////////////////Menu_Agrupacionproducto////////
////////////////Detalle_Orden_Pedido////////
///////////////////Plus, Precio_Plu, Mesas///////////////////
///////FECHA CREACION: 16-04-2014//////////////////////////
///////FECHA ULTIMA MODIFICACION: 07-05-2014////////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro///////////////
///////DECRIPCION ULTIMO CAMBIO: Documentación y validación en Internet Explorer 9+///////
/////////////////////////////////////////////////////////// 

class menuPedido extends sql
{
	//Constructor de la Clase
	function _construct()
	{
		parent ::_construct();
	}
	
	//Función que permite armar la sentencia sql de consulta
	function fn_consultar($lc_sqlQuery,$lc_datos)
	{
		switch($lc_sqlQuery)
		{	

/*----------------------------------------------------------------------------------------------------
Obtiene la orden al momento que carga la página
Función de llamada: $(document).ready(function()
-----------------------------------------------------------------------------------------------------*/
			case "obtenerOrden":
				$lc_sql="SELECT * FROM Cabecera_Orden_Pedido odp
						inner join Detalle_Orden_Pedido dop on odp.odp_id=dop.odp_id
						inner join Menu_Agrupacionproducto map on map.plu_id=dop.plu_id
						inner join Users_Pos usr on usr.usr_id=odp.usr_id
						inner join Plus plu on plu.plu_id=dop.plu_id
						WHERE dop.odp_id=$lc_datos[0] AND plu.cprn_id=$lc_datos[1] AND dop.dop_estado=1 ORDER BY dop.dop_id ASC";
				if($this->fn_ejecutarquery($lc_sql)) 
					{ 
						while($row = $this->fn_leerarreglo()) 
						{		
							$this->lc_regs[] = array("magp_desc_impresion"=>trim($row['magp_desc_impresion'])
												  ,"plu_id"=>$row['plu_id']
												  ,"mesa_id"=>$row['mesa_id']
												  ,"dop_cantidad"=>$row['dop_cantidad']
												  ,"usr_descripcion"=>$row['usr_descripcion']
												  ,"odp_id"=>$row['odp_id']
												  ,"dop_anulacion"=>$row['dop_anulacion']
												  ,"dop_id"=>$row['dop_id']
												  ,"dop_impresion"=>$row['dop_impresion']);		
						}
					}	
				$this->lc_regs['str'] = $this->fn_numregistro();  
				return json_encode($this->lc_regs);	
			break;
/*----------------------------------------------------------------------------------------------------
Actualiza los items que fueron impresos
Función de llamada: fn_actualizarImpresion(plu_id,dop_id)
-----------------------------------------------------------------------------------------------------*/
			case "actualizarImpresion":
				$lc_sql="UPDATE Detalle_Orden_Pedido set dop_impresion=0
						WHERE dop_id=$lc_datos[1] AND odp_id=$lc_datos[0] AND plu_id=$lc_datos[2] AND dop_estado=1 AND dop_anulacion=1
						
						SELECT * FROM Detalle_Orden_Pedido WHERE dop_id=$lc_datos[1] AND odp_id=$lc_datos[0] AND plu_id=$lc_datos[2]";
				if($this->fn_ejecutarquery($lc_sql)) 
					{ 
						while($row = $this->fn_leerarreglo()) 
						{		
							$this->lc_regs[] = array("plu_id"=>$row['plu_id']
												  ,"dop_cantidad"=>$row['dop_cantidad']
												  ,"odp_id"=>$row['odp_id']
												  ,"dop_anulacion"=>$row['dop_anulacion']);		
						}
					}	
				$this->lc_regs['str'] = $this->fn_numregistro();  
				return json_encode($this->lc_regs);	
			break;
/*----------------------------------------------------------------------------------------------------
Actualiza los items anulados que fueron impresos
Función de llamada: fn_actualizarImpresionAnulacion(plu_id,dop_id)
-----------------------------------------------------------------------------------------------------*/
			case "actualizarImpresionAnulacion":
				$lc_sql="UPDATE Detalle_Orden_Pedido set dop_impresion=2
						WHERE dop_id=$lc_datos[1] AND odp_id=$lc_datos[0] AND plu_id=$lc_datos[2] AND dop_estado=1 AND dop_anulacion=0
						
						SELECT * FROM Detalle_Orden_Pedido WHERE dop_id=$lc_datos[1] AND odp_id=$lc_datos[0] AND plu_id=$lc_datos[2]";
				if($this->fn_ejecutarquery($lc_sql)) 
					{ 
						while($row = $this->fn_leerarreglo()) 
						{		
							$this->lc_regs[] = array("plu_id"=>$row['plu_id']
												  ,"dop_cantidad"=>$row['dop_cantidad']
												  ,"odp_id"=>$row['odp_id']
												  ,"dop_anulacion"=>$row['dop_anulacion']);		
						}
					}	
				$this->lc_regs['str'] = $this->fn_numregistro();  
				return json_encode($this->lc_regs);	
			break;
			
		}
	}
}