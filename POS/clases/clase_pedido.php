<?php 

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Aldo Navarrete //////////////////////////////////////////
///////DESCRIPCION: Consulta y reporte de pedidos //////////////////////
///////FECHA CREACION: 17-07-2020 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////


class Pedido extends sql{
	
	//constructor de la clase
	function __construct(){
		parent ::__construct();
	}


	function aplicaPickup($cadena, $restaurante){

		$result = 0;
		$lc_sql = "EXECUTE config.PICKUP_activo  $cadena, $restaurante ";
		
		try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
				$row = $this->fn_leerarreglo();
				if(isset($row['activo'])){
					$result =$row['activo'];
				}
            }
        } catch (Exception $e) {

			return $result;
		}
        return $result;

	}



	function listaLazyPickup($pagina, $tamanio, $fechaDesde, $fechaHasta, $ingresados, $preparando, $listos, $entregados, $transferido, $nombreCliente, $identificacion, $codigoApp){
		$this->lc_regs = [];
        $lc_sql = "EXEC [dbo].[App_lazy_pickup]  $pagina, $tamanio, '$fechaDesde', '$fechaHasta', $ingresados, $preparando, $listos, $entregados, $transferido, '$nombreCliente', '$identificacion', '$codigoApp' ";

		try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "id" 				=> $row['id'],
										  "codigoApp" 		=> $row['codigo_app'],
										  "identificacion"	=> $row['identificacion'],
										  "cliente"			=> $row['cliente'],
                                          "tipoPago"		=> $row['tipo_pago'],
                                          "tipoTarjeta"     => $row['tipo_tarjeta'],
										  "telefono"		=> $row['telefono'],
										  "horaPedido"		=> $row['hora_pedido'],
										  "horaPickup"		=> $row['hora_pickup'],
										  "tiempoDespacho"	=> $row['tiempo_despacho'],
										  "estado"			=> $row['estado']
                                          );
            }
			$this->lc_regs['registros'] = $this->fn_numregistro();
			$this->lc_regs['totalRegistros'] = $this->cantidadLazyPickup($fechaDesde, $fechaHasta, $ingresados, $preparando, $listos, $entregados, $transferido, $nombreCliente, $identificacion, $codigoApp);

        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
	}

	
	function cantidadLazyPickup($fechaDesde, $fechaHasta, $ingresados, $preparando, $listos, $entregados, $transferido , $nombreCliente, $identificacion, $codigoApp){

		$result = 0;
		$lc_sql = "EXEC [dbo].[App_cantidad_lazy_pickup]  '$fechaDesde', '$fechaHasta', $ingresados, $preparando, $listos, $entregados, $transferido , '$nombreCliente', '$identificacion', '$codigoApp' ";
		
		try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
				$row = $this->fn_leerarreglo();
				$result =$row['cantidad'];
            }
        } catch (Exception $e) {
            return $e;
		}
		
        return $result;

	}

	function listaLazyPickupPorCfacId($pagina, $tamanio, $fechaDesde, $fechaHasta, $ingresados, $preparando, $listos, $entregados, $transferidos , $nombreCliente, $identificacion, $codigoApp, $cfac_id){
		$this->lc_regs = [];
        $lc_sql = "EXEC [dbo].[App_lazy_pickup_orden_pedido]  $pagina, $tamanio, '$fechaDesde', '$fechaHasta', $ingresados, $preparando, $listos, $entregados, $transferidos , '$nombreCliente', '$identificacion', '$codigoApp', '$cfac_id'";
		
		try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "id" 				=> $row['id'],
										  "codigoApp" 		=> $row['codigo_app'],
										  "identificacion"	=> $row['identificacion'],
										  "cliente"			=> $row['cliente'],
										  "telefono"		=> $row['telefono'],
										  "horaPedido"		=> $row['hora_pedido'],
										  "horaPickup"		=> $row['hora_pickup'],
										  "tiempoDespacho"	=> $row['tiempo_despacho'],
										  "estado"			=> $row['estado']
                                          );
            }
			$this->lc_regs['registros'] = $this->fn_numregistro();
			$this->lc_regs['totalRegistros'] = $this->cantidadLazyPickup($fechaDesde, $fechaHasta, $ingresados, $preparando, $listos, $entregados, $transferidos, $nombreCliente, $identificacion, $codigoApp);

        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
	}


	function detallePedidoPickup($kioskoCabeceraId, $estadoPedido){
		$this->lc_regs = [];
		$lc_sql = "EXEC [dbo].[App_detalle_pickup]  $kioskoCabeceraId ";
		
		try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
				$row = $this->fn_leerarreglo();
				
				$jsonPedidoString = utf8_encode($row['jsonPedido']);
				$jsonPedidoDecode = json_decode($jsonPedidoString);
				$this->lc_regs = array( "codigoApp" 			=> $row['codigoApp'],
										"jsonPedido"			=> $jsonPedidoDecode,
										"facturaId"				=> $row['facturaId'],
										"facturaFechaInsercion" => $row['facturaFechaInsercion'],
										"facturaTotal"			=> $row['facturaTotal'],
										"facturaSubtotal"		=> $row['facturaSubtotal'],
										"facturaIva"			=> $row['facturaIva'],
										"facturaDescuento"		=> $row['facturaDescuento'],
										"estadoPedido"			=> $estadoPedido,
										"ordenPedidoId"			=> $row['ordenPedidoId']);
            }
			$result = $this->lc_regs;

        } catch (Exception $e) {
            return $e;
		}
		
        return json_encode($result);

	}


	function detalleImpresionesPickup($kioskoCabeceraId){
		$this->lc_regs = [];
		$lc_sql = "EXEC [dbo].[App_detalle_impresion_pickup]  $kioskoCabeceraId ";
		try {
			$this->fn_ejecutarquery($lc_sql);
			while ($row = $this->fn_leerarreglo()) {
				
				$this->lc_regs[] = array( "id" 			=> $row['IDCanalMovimiento'],
										"fecha"			=> $row['imp_fecha'],
										"estacion"		=> $row['imp_ip_estacion'],
										"impresora" 	=> $row['imp_impresora'],
										"url"			=> $row['imp_url'],
										"estado"		=> $row['estado'],
										"canal"			=> $row['canal']);
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();

        } catch (Exception $e) {
            return $e;
		}
		
        return json_encode($this->lc_regs);

	}

	function informacionProductosPedidoPickup($kioskoCabeceraId){

		$this->lc_regs = [];
		$lc_sql = "EXEC [dbo].[App_descripcion_producto_pickup]  $kioskoCabeceraId ";
		try {
			$this->fn_ejecutarquery($lc_sql);
			while ($row = $this->fn_leerarreglo()) {
				
				$this->lc_regs[] = array( "producto_id" 	=> $row['producto_id'],
										  "producto_nombre"	=> $row['producto_nombre']);
            }

        } catch (Exception $e) {
            return $e;
		}
		
        return json_encode($this->lc_regs);

	}


	function cantidadPorEstadoPickup($fechaDesde, $fechaHasta){
		$this->lc_regs = [];
		$lc_sql = "EXEC [dbo].[App_cantidad_por_pedido_pickup]  '$fechaDesde', '$fechaHasta' ";
		
		try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
				$row = $this->fn_leerarreglo();				
				$this->lc_regs = array( "ingresados" 			=> $row['ingresados'],
										"preparando"			=> $row['preparando'],
										"listos"				=> $row['listos'],
										"entregados" 			=> $row['entregados'],
										"transferidos"          => $row["transferidos"]);
            }
			$result = $this->lc_regs;

        } catch (Exception $e) {
            return $e;
		}
		
        return json_encode($result);

	}


	function detalleFacturaPickup($idFactura){
		$this->lc_regs = [];
		$lc_sql = "EXEC [dbo].[App_detalle_factura_pickup]  '$idFactura' ";
		try {
			$this->fn_ejecutarquery($lc_sql);
			while ($row = $this->fn_leerarreglo()) {
				
				$this->lc_regs[] = array( "plu_descripcion" => $row['plu_descripcion'],
										"plu_id"			=> $row['plu_id'],
										"dop_cantidad"		=> $row['dop_cantidad'],
										"dop_precio" 		=> $row['dop_precio'],
										"dop_total"			=> $row['dop_total'],
										"es_producto"		=> $row['es_producto']);
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();

        } catch (Exception $e) {
            return $e;
		}
		
        return json_encode($this->lc_regs);

	}

	function reimprimir($idCanalMovimiento){
		$this->lc_regs = [];
		$lc_sql = "EXEC [dbo].[reimpresion]  '$idCanalMovimiento' ";
		$result = $this->fn_ejecutarquery($lc_sql);
        if ($result){ return true; }else{ return false; };
	}



	//////////////////////////////////////////////////////////
	/////           METODOS API CENTRAL          /////////////
	//////////////////////////////////////////////////////////


	function listaApiCentral($idRestaurante, $documento, $codigoApp, $fechaDesde, $fechaHasta, $urlCentral)
	{
		try {


			$parametros = [
				"rstId" => $idRestaurante,
				"documento" => $documento == null ? '' : $documento,
				"codigoApp" => $codigoApp == null ? '' : $codigoApp,
				"fechaDesde" => $fechaDesde,
				"fechaHasta" => $fechaHasta,

			];

			$urlBase = '';

			if(substr( $urlCentral, 0, 4 ) === "http"){
				$urlBase = $urlCentral;


			$url = $urlBase.'/maxpoint/ordeneslocal';

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parametros));


			$result = curl_exec($ch);
//			printf('RESULTADO CONSULTA');
//			printf(var_dump($result));

			$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);


			if (curl_errno($ch) || $http_status === 400 || $http_status === 404 || $http_status === 0) {

				$mensaje = ($http_status === 0) ? "El WebService centralizado no responde." : (($http_status === 400) ? "Errores dentro de los parametros del WebService " : (($http_status === 404) ? "WebService no encontrado " : "Error de Ws en Destino."));
				return '{"response": "ERROR", "mensaje": "'.$mensaje.'", "data": [] }';
			} else {

				$respuesta = json_decode($result, true);

				if (empty($respuesta)) {
					return '{"response": "SUCCESS", "mensaje": "El servicio web no devolvio valores", "data": [] }';
				}else{
					return $result;
				}
			}

		}else{
			return '{"response": "SUCCESS", "mensaje": "No aplica el servicio", "data": [] }';
		}


		} catch (Exception $e) {
			return '{"response": "ERROR", "mensaje": "Servicio web no disponible", "data": [] }';
		}
	}

	function informacionProductosPedidoPickupJson($jsonDetalles)
	{

		$this->lc_regs = [];
		$lc_sql = "EXEC [dbo].[App_descripcion_producto_pickup_json]  '$jsonDetalles' ";
		try {
			$this->fn_ejecutarquery($lc_sql);
			while ($row = $this->fn_leerarreglo()) {

				$this->lc_regs[] = array(
					"producto_id" 	=> $row['producto_id'],
					"producto_nombre"	=> $row['producto_nombre']
				);
			}
		} catch (Exception $e) {
			return $e;
		}

		return json_encode($this->lc_regs);
	}

	function cargarInformacionFacturaCentral($codigoApp, $estadoPedido)
	{

		$this->lc_regs = [];
		$lc_sql = "EXEC [dbo].[Carga_informacion_factura_central]  '$codigoApp' ";

		try {
			$this->fn_ejecutarquery($lc_sql);
			if ($this->fn_numregistro() > 0) {
				$row = $this->fn_leerarreglo();

				$jsonPedidoString = utf8_encode($row['jsonPedido']);
				$jsonPedidoDecode = json_decode($jsonPedidoString);
				$this->lc_regs = array(
					"codigoApp" 			=> $row['codigoApp'],
					"jsonPedido"			=> $jsonPedidoDecode,
					"facturaId"				=> $row['facturaId'],
					"facturaFechaInsercion" => $row['facturaFechaInsercion'],
					"facturaTotal"			=> $row['facturaTotal'],
					"facturaSubtotal"		=> $row['facturaSubtotal'],
					"facturaIva"			=> $row['facturaIva'],
					"facturaDescuento"		=> $row['facturaDescuento'],
					"estadoPedido"			=> $estadoPedido,
					"ordenPedidoId"			=> $row['ordenPedidoId']
				);
			}
			$result = $this->lc_regs;
		} catch (Exception $e) {
			return $e;
		}

		return json_encode($result);
	}


	function detalleImpresionesCentral($codigoApp)
	{
		$this->lc_regs = [];
		$lc_sql = "EXEC [dbo].[App_detalle_impresion_pickup_central]  '$codigoApp' ";
		try {
			$this->fn_ejecutarquery($lc_sql);
			while ($row = $this->fn_leerarreglo()) {

				$this->lc_regs[] = array(
					"id" 			=> $row['IDCanalMovimiento'],
					"fecha"			=> $row['imp_fecha'],
					"estacion"		=> $row['imp_ip_estacion'],
					"impresora" 	=> $row['imp_impresora'],
					"url"			=> $row['imp_url'],
					"estado"		=> $row['estado'],
					"canal"			=> $row['canal']
				);
			}
			$this->lc_regs['registros'] = $this->fn_numregistro();
		} catch (Exception $e) {
			return $e;
		}

		return json_encode($this->lc_regs);
	}

	function urlServidorCentral()
	{
		$this->lc_regs = [];
		$lc_sql = "EXEC [config].[USP_retorna_url_ruta] 'DISTRIBUIDOR', 'PICKUP CONFIRMACION' ";
		$this->fn_ejecutarquery($lc_sql);
		$row = $this->fn_leerarreglo();
		return json_encode(utf8_encode($row['servidor']));
	}


	



}
			