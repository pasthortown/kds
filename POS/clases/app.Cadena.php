<?php
class Cadena extends sql {

    function cargarConfiguracionPoliticas( $idCadena ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_datos " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "client_id" => $row['client_id'],
                                        "client_secret" => $row['client_secret'] );
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }


    function cargarMerchantIdWeb( $idCadena ) {

        $lc_sql = "EXEC pedido.USP_ConfiguracionWeb '$idCadena'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array("merchant_Id" => $row['merchant_Id']);

            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

/** 
    * @fn cargarParametrosNotificacionTransferenciaWeb
    * @brief Obtener parametros de notificación de transferencia web
    * @author Jacximir Salazar
    * @param integer idCadena -> ID de la cadena
    * @return array Retorna los valores de las politicas para CONFIGURACIÓN WEB (TRANSFERENCIA IDENTITY TOKEN), (TRANSFERENCIA MERCHANT ID)
*/

    function cargarParametrosNotificacionTransferenciaWeb($idCadena){
        $lc_sql = "EXEC [config].[USP_ObtenerParametrosNotificacionTransferenciaWeb] '$idCadena'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array("merchant_id" => $row['merchant_id'],
                                       "identity_token" => $row['identity_token']);
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }


    function cargarConfiguracionPoliticasPorMedio( $idCadena, $medio ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_datos_por_medio " . $idCadena. ',' ."'$medio'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "client_id" => $row['client_id'],
                                        "client_secret" => $row['client_secret'] );
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function ObtenerCadenaRestaurante($idRestaurante){
        $lc_sql = "EXEC [config].[USP_Obtener_IdCadena] '$idRestaurante'";
        try{
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "idCadena" => $row['valorCadena'] );
            }
            $result = $this->lc_regs;
        }catch (Exception $e) {
            return $e;
        }
        return $result;

    }
    function obtenerCodigoApp( $idFactura ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_obtener_codigoApp '$idFactura'" ;
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "codigo_app" => $row['codigo_app'] );
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }


    function generarNotaCredito($idRestaurante, $idFactura, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion, $cedula) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.APP_generar_NotaCredito $idRestaurante, '$idFactura', '$idUsuario', '$idEstacion', '$idMotivoAnulacion', '$observacion','$cedula'";
  
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "idNotaCredito" => $row['IDNotaCredito'],
                                        "idFactura" => $row['IDFactura'], 
                                        "servidorUrlApi"    => $row['servidorUrlApi']
                                    );
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function cargarPedidosPorEstado( $idCadena, $idRestaurante, $estado ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_pedidos_estado $idCadena, $idRestaurante, '$estado'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "codigo_app" => $row['codigo_app'],
                                          "forma_pago" => $row['forma_pago'],
                                          "codigo" => $row['codigo'],
                                          "documento" => $row['documento'],
                                          "cliente" => utf8_encode($row['cliente']),
                                          "fecha" => $row['fecha'],
                                          "total" => $row['total'],
                                          "telefono" => $row['telefono'],
                                          "direccion_factura" => utf8_encode($row['direccion_factura']),
                                          "email" => $row['email'],
                                          "direccion_despacho" => utf8_encode($row['direccion_despacho']),
                                          "datos_envio" => utf8_encode($row['datos_envio']),
                                          "observacion" => utf8_encode($row['observacion']),
                                          "medio" => $row['medio']
                                          );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function cargarConfiguracionPoliticasPickupAutenticacionDistribuidor( $idCadena ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_datos_pickup_autenticacion " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "client_id" => $row['client_id'],
                                        "client_secret" => $row['client_secret'] );
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function cargarConfiguracionPoliticasPickupAutenticacionTrade( $idCadena ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_datos_pickup " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "client_id" => $row['client_id'],
                                        "client_secret" => $row['client_secret'] );
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function guardarAuditoria( $idCadena, $idRestaurante, $idUsuario, $codigo, $descripcion, $accion, $request, $response ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_Guardar_Auditoria_Transferencia $idCadena, $idRestaurante, '$idUsuario', '$codigo', '$descripcion', '$accion', '$request', '$response'";
        try {
            $this->fn_ejecutarquery($lc_sql);
        } catch (Exception $e) {
            print $e;
        }
    }

    function generarNotaCreditoPickup($idRestaurante, $codigo_app, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.APP_generar_NotaCredito_Pickup $idRestaurante, '$codigo_app', '$idUsuario', '$idEstacion', '$idMotivoAnulacion', '$observacion'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "idNotaCredito" => $row['IDNotaCredito'],
                                        "idFactura" => $row['IDFactura'] );
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function contarPedidosPorEstado( $idCadena, $idRestaurante, $estado ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_contar_pedidos_estado $idCadena, $idRestaurante, '$estado'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "total_pedidos" => $row['total_pedidos'] );
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function cargarDetallePedidoApp( $idCadena, $idRestaurante, $codigo, $medio ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_detalle_pedido $idCadena, $idRestaurante, '$codigo', '$medio'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "num_plu" => $row['num_plu'],
                                          "cantidad" => $row['cantidad'],
                                          "descripcion" => utf8_encode($row['descripcion']),
                                          "total_app" => $row['total_app'],
                                          "total_mxp" => $row['total_mxp'],
                                          "sucesor" => $row['sucesor'],
                                          "total" => $row['total'],
                                          "montoTotalDescuentos" => $row['montoTotalDescuentos'],
                                          "tipo" => $row['tipo'],
                                          "fidelizacion" => $row['fidelizacion']);
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function facturarPedidoApp( $idCadena, $idRestaurante, $codigo, $idEstacion, $idUsuario, $idControlEstacion ) {
        $this->lc_regs = [];
       $lc_sql = "EXEC dbo.App_facturarPedidoApp '$idCadena', '$idRestaurante', '$codigo', '$idEstacion', '$idUsuario', '$idControlEstacion'";
        //echo $lc_sql;
       try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "mensaje" => utf8_encode($row['mensaje']),
                                        "idFactura" => $row['idFactura'],
                                        "codigo" => $row['codigo']

                );
            }           
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }

    function cuponesMovistar($datos){
        $lc_sql = "EXEC [facturacion].[USP_PromocionesMovistar] '$datos[0]'";
        $toReturn = [];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $toReturn = ["Respuesta"=>trim($row['Respuesta'])];
            }
        }
        return json_encode($toReturn); 
    }

    function setQRPromocionesMovistar($datos){
        $lc_sql = "EXEC [facturacion].[USP_SetQRPromocionesMovistar] '$datos[0]','$datos[1]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $toReturn = '';
            while ($row = $this->fn_leerarreglo()) {
                $toReturn = trim($row['Respuesta']);
            }
        }
        return json_encode(['Respuesta'=>$toReturn]);
    }

    function auditoria_cupones_movistar($datos){
        $lc_sql = "EXEC [facturacion].[IAE_Logs_auditoria_transaccion] $datos[0],'$datos[1]', '$datos[2]','$datos[3]', '$datos[4]','$datos[5]','$datos[6]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $toReturn = '';
            while ($row = $this->fn_leerarreglo()) {
                $toReturn = trim($row['Respuesta']);
            }
        }
        return json_encode(['Respuesta'=>$toReturn]);
    }

    function cargarPedidos($idCadena, $idRestaurante, $idPeriodo, $estado, $parametroBusqueda, $idEstacion)
    {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_pedidos_cantidad_app $idCadena, $idRestaurante, '$idPeriodo', '$estado', '$parametroBusqueda','$idEstacion'";
        try {
            if ($this->fn_ejecutarquery($lc_sql)) {
                while ($row = $this->fn_leerarreglo()) {

                    if(isset($row['data'])) {
                        $registros = json_decode(utf8_encode($row['data']));
                    }else {
                        $registros = [];
                    }

                    $data = [
                        $registros, 'registros' => count($registros)
                    ];

                    $this->lc_regs[$row['tipo']] = $data;
                }
            }

            $this->lc_regs['registros'] = $this->fn_numregistro();

        } catch (Exception $e) {
            print('EXCEPCION');
            print($e);
            return $e;
        }

        return json_encode($this->lc_regs);
    }

    /**
     * @param $idCadena
     * @param $idRestaurante
     * @param $idPeriodo
     * @param $estado
     * @return array
     */
    private function taskPrintOrdersDelivery($idCadena, $idRestaurante, $idPeriodo, $estado = 'PRINCIPAL')
    {
        $orders = [];
        try {
            $lc_sql = "EXEC dbo.App_cargar_pedidos_app $idCadena, $idRestaurante, '$idPeriodo', '$estado', ''";
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $transactionId = $row['codigo_factura'] ? $row['codigo_factura'] : $row['IDCabeceraOrdenPedido'];
                $orders[] = [
                    'codigo_app' => $row['codigo_app'],
                    'transaction' => $transactionId,
                    'codigo_factura' => $row['codigo_factura'],
                    'codigo_orden' => $row['IDCabeceraOrdenPedido'],
                ];
            }
            if(isset($orders) && !empty($orders)){
                foreach ($orders as $order) {
                    if (isset($order['transaction']) && !empty($order['transaction']) && $this->validatePrintOrder($order)) {
                        $this->requestClientWsServicePrint($order);
                    }
                }
            }
            return $orders;
        } catch (Exception $exception) {
            return $orders;
        }
    }

    /**
     * @param array $order
     * @return void
     */
    private function requestClientWsServicePrint(array $order)
    {
        $httpHost = $_SERVER['HTTP_HOST'];
        $path = explode('/', $_SERVER['REQUEST_URI'])[1];
        $endPoint = "$httpHost/$path/impresion/cliente_ws_servicioImpresion.php";
        $headers = [
            'Content-Type: application/json',
        ];
        try {
            $body = [
                'metodo' => 'apiServicioImpresion',
                'tipo' => 'delivery',
                'transaccion' => $order['transaction'],
                'idCabeceraOrdenPedido' => null,
                'datosAdicionales' => null,
                'session' => $_SESSION,
                'codigo_app' => $order['codigo_app'],
                'codigo_factura' => $order['codigo_factura'],
                'codigo_orden' => $order['codigo_orden']
            ];
            $cURLConnection = curl_init($endPoint);
            curl_setopt($cURLConnection, CURLOPT_POST, 1);
            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($body));
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
            $apiResponse = curl_exec($cURLConnection);
            curl_close($cURLConnection);
            return;
        } catch (Exception $exception) {
            return;
        }
    }

    private function validatePrintOrder(array $order)
    {
        $idRestaurante = $_SESSION['rstId'];
        $codeApp = $order['codigo_app'];
        $codeInvoince = $order['codigo_factura'];
        $lc_sql = "EXEC [dbo].[VerificarCanalMovimientoExistente] $idRestaurante, '$codeApp', '$codeInvoince'";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();
        return ($row['exist']==1);
    }


    function cargarPedidosEntregados($idCadena, $idRestaurante, $idPeriodo, $estado, $parametroBusqueda)
    {

        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_pedidos_entregados $idCadena, $idRestaurante, '$idPeriodo', '$estado', '$parametroBusqueda'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "codigo_app" => $row['codigo_app'],
                                          "forma_pago" => $row['forma_pago'],
                                          "codigo" => $row['codigo'],
                                          "documento" => $row['documento'],
                                          "cliente" => utf8_encode($row['cliente']),
                                          "fecha" => $row['fecha'],
                                          "total" => $row['total'],
                                          "telefono" => $row['telefono'],
                                          "direccion_factura" => utf8_encode($row['direccion_factura']),
                                          "email" => utf8_encode($row['email']),
                                          "direccion_despacho" => utf8_encode($row['direccion_despacho']),
                                          "datos_envio" => utf8_encode($row['datos_envio']),
                                          "observacion" => utf8_encode($row['observacion']),
                                          "estado" => $row['estado'],
                                          "idMotorizado" => $row['idMotorolo'],
                                          "motorizado" => utf8_encode($row['motorolo']),
                                          "motorizado_telefono" => $row['motorolo_telefono'],
                                          "tiempo" => $row['tiempo'],
                                          "medio" => $row['medio'],
                                          "color_fila" => $row['color_fila'],
                                          "color_texto" => $row['color_texto'],
                                          "audio" => $row['audio'],
                                          "cambio_estado" => $row['cambio_estado'],
                                          "codigo_externo" => $row['codigo_externo'],
                                          "fecha_estado" => $row['fecha_estado'],
                                          "user_estado" => $row['user_estado'],
                                          "mediopago" => $row['medio_pago'],
                                          "observacion_factura" => utf8_encode($row['observacion_factura']),
                                          "motivo_anulacion" => utf8_encode($row['motivo_anulacion']),
                                          "codigo_factura" => $row['codigo_factura'],
                                          "notificar_medio" => $row['notificar_medio'],
                                          "notificar_listo" => $row['notificar_listo']);
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            print('EXCEPCION');
            print($e);
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function cargarMotorizados($idCadena,$idRestaurante, $idPeriodo, $medio){

        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_motorizado_interno_externo_periodo '$idCadena','$idRestaurante', '$idPeriodo', '$medio'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "idMotorizado" => $row['idMotorizado'],
                                          "motorizado" => utf8_encode($row['motorizado']),
                                          "documento" => $row['documento'],
                                          "tipo" => $row['tipo'],
                                          "empresa" => utf8_encode($row['empresa']),
                                          "total" => $row['total'],
                                          "maximo_ordenes" => $row['maximo_ordenes'],
                                          "estado" => $row['estado'],
                                         );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }
    function modificarMotorizados( $idMotorizado, $tipo,$empresa,$documento,$nombre,$apellido,$codigo,$celular,$estado){
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_IA_motorolo '$idMotorizado', '$tipo','$empresa','$documento','$nombre','$apellido','$documento','$codigo','$celular','$estado'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "mensaje" => utf8_encode($row['mensaje']),
                                          "estado" => $row['estado']
                                         );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }
    function asignarMotorizado( $idMotorizado, $codigo_app, $idUsuario ){
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_asignar_pedido_motorolo '$idMotorizado','$codigo_app', '$idUsuario'";
        //var_dump($lc_sql);
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "mensaje" => utf8_encode($row['mensaje']),
                                          "estado" => $row['estado']);
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }
    function listaTransaccionesAsignadas( $idCadena,$idRestaurante,$idMotorizado){
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_pedidos_motorolo '$idCadena','$idRestaurante','$idMotorizado'";
        //var_dump($lc_sql);
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(   "codigo_app" => $row['codigo_app'],
                                            "codigo" => $row['codigo'],
                                            "telefono" => $row['telefono'],
                                            "cliente" => utf8_encode($row['cliente']),
                                            "total" => $row['total'],
                                            "forma_pago" => $row['forma_pago'],
                                            "estado" => $row['estado'],
                                            "medio" => trim($row['medio']));
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }
    function cambioTransaccionesEnCamino( $idPeriodo, $idMotorizado, $idUsuario ){
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cambiar_pedido_en_camino '$idPeriodo','$idMotorizado', '$idUsuario'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "mensaje" => utf8_encode($row['mensaje']),
                                          "estado" => $row['estado']);
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function cambioTransaccionesEntregado( $idPeriodo, $idMotorizado, $idUsuario ){
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cambiar_pedido_entregado '$idPeriodo','$idMotorizado', '$idUsuario'";
        //var_dump($lc_sql);
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "mensaje" => utf8_encode($row['mensaje']),
                                          "estado" => $row['estado']);
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }
    
    function cargarImpresionesError( $idCadena, $idRestaurante, $idPeriodo ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.IMP_cargar_impresiones_error $idCadena, $idRestaurante, '$idPeriodo'";
        //var_dump($lc_sql);
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "idFactura" => $row['idFactura'],
                                          "total" => $row['total'],
                                          "idCanalMovimiento" => $row['idCanalMovimiento'],
                                          "ipEstacion" => $row['ipEstacion'],
                                          "fecha" => $row['fecha'],
                                          "url" => $row['url'],
                                          "impresora" => $row['impresora'],
                                          "tipo_error" => $row['tipo_error'],
                                          "estado" => $row['estado'],
                                          "tipo" => $row['tipo']);
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function confirmarOrden( $idUserPos,$codigo_app, $idRestaurante){
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_confirmar_pedido '$idUserPos','$codigo_app', '$idRestaurante'";
        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "mensaje" => utf8_encode($row['mensaje']),
                                        "estado" => $row['estado']);
            }
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function cambioEstadoPedido( $codigo_app, $nuevo_estado, $idPeriodo ) {
        $this->lc_regs = [];

        $lc_sql = "EXEC dbo.App_cambiar_estado_pedido '$nuevo_estado', '$codigo_app', '$idPeriodo'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array( "mensaje" => utf8_encode($row['mensaje']),
                                          "estado" => $row['estado']);
            }
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function impresionTransaccionError( $idCanalMovimiento ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC facturacion.TRN_reimpresion_canal_movimiento_error '$idCanalMovimiento'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "mensaje" => $row['mensaje'],
                                        "estado" => $row['estado'] );
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }

    function facturaPorPedido( $codigo_app){
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_factura_por_pedido '$codigo_app'";
        //var_dump($lc_sql);
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( 
                                            "factura"               =>  $row['factura'],
                                            "notaCredito"           =>  $row['notaCredito'],
                                            "documento_con_datos"   =>  $row['documento_con_datos'],
                                            "mensaje"               =>  utf8_encode($row['mensaje']),
                                            "estado"                =>  $row['estado']
                                         );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function cargarPoliticaProveedorTracking( $cdn_id){
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_metodo_cambio_estados '$cdn_id'";
     
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "metodo" =>  $row['metodo']);
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function cargarSemaforoConfig( $cdn_id){
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_semaforo_pedidos '$cdn_id'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "semaforo" =>  $row['semaforo'],
                "tiempo" =>  $row['tiempo'],
                "color" =>  $row['color']);
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function cargarURLCrearPedidoBringg($rst_id){
        $this->lc_regs = [];
        $lc_sql = "EXEC config.USP_Retorna_Direccion_Webservice '$rst_id', 'BRINGG', 'CREAR PEDIDO', 0";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( 
                    "estado"    =>  $row['estado'],
                    "url"       =>  $row['direccionws']
                );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function cargarURLAnularPedidoBringg($rst_id){
        $this->lc_regs = [];
        $lc_sql = "EXEC config.USP_Retorna_Direccion_Webservice '$rst_id', 'BRINGG', 'ANULAR PEDIDO', 0";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( 
                    "estado"    =>  $row['estado'],
                    "url"       =>  $row['direccionws']
                );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function obtenerUrlCrearPedidoDuna($rst_id){
        $this->lc_regs = [];
        $lc_sql = "EXEC config.USP_Retorna_Direccion_Webservice '$rst_id', 'DUNA', 'CREAR PEDIDO', 0";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( 
                    "estado"    =>  $row['estado'],
                    "url"       =>  $row['direccionws']
                );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function obtenerUrlAnularPedidoDuna($rst_id){
        $this->lc_regs = [];
        $lc_sql = "EXEC config.USP_Retorna_Direccion_Webservice '$rst_id', 'DUNA', 'ANULAR PEDIDO', 0";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( 
                    "estado"    =>  $row['estado'],
                    "url"       =>  $row['direccionws']
                );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function cambioEstadosAutomatico($cdn_id){
        $this->lc_regs = [];
        $lc_sql = "EXEC [dbo].[App_configuracion_cambio_estados_autormatico] $cdn_id";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( 
                    "automatico"    =>  $row['automatico']
                );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function obtenerCodigoExterno($codigo_app){
        $this->lc_regs = [];
        $lc_sql = "EXEC [dbo].[App_codigo_externo] '$codigo_app'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( 
                    "codigo_externo"    =>  $row['codigo_externo'],
                    "cfac_id"           =>  $row['cfac_id']
                );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function obtenerCantidadEstadosPedidosApps( $idCadena, $idRestaurante, $idPeriodo){

        $this->lc_regs = [];
        $lc_sql = "EXEC [dbo].[App_cantidad_estados_pedidos_app] $idCadena, $idRestaurante, '$idPeriodo'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( 
                    "EnProceso"    =>  $row['EnProceso'],
                    "Pendiente"    =>  $row['Pendiente'],
                    "Recibido"    =>  $row['Recibido'],
                    "PorAsignar"    =>  $row['PorAsignar'],
                    "Asignado"    =>  $row['Asignado'],
                    "EnCamino"    =>  $row['EnCamino'],
                    "Anulada"    =>  $row['Anulada']
                );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        $this->taskPrintOrdersDelivery($idCadena, $idRestaurante, $idPeriodo);
        //$asyncServicePrint = new AsyncServicePrint($idCadena, $idRestaurante, $idPeriodo, $_SESSION);
        return json_encode($this->lc_regs);
    }

    function reversarAsignacionMotorolo( $idMotorizado,$codigo_app){
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_reversar_asignacion_motorolo '$idMotorizado','$codigo_app'";
        //var_dump($lc_sql);
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "mensaje" => utf8_encode($row['mensaje']),
                                          "estado" => $row['estado']
                                         );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function cargarListaMedios( $idCadena, $idRestaurante ){
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_lista_medios $idCadena, $idRestaurante";
        //var_dump($lc_sql);
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "codigo" => $row['codigo'],
                                          "cambio_estado" => $row['cambio_estado'],
                                          "color_fila" => $row['color_fila'],
                                          "color_texto" => $row['color_texto'],
                                          "audio" => $row['audio'],
                                          "interfaces" => $row['interfaces'],
                                          "automatico" => $row['automatico'] );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function completarTransaccionAgregador($codigo_app, $medio, $idUsuario, $idPeriodo){
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_completar_transaccion_agregador '$codigo_app','$medio', '$idUsuario', '$idPeriodo' ";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "mensaje" => utf8_encode($row['mensaje']),
                                          "estado" => $row['estado']);
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function configuracionAlertaMedios($idCadena, $idRestaurante){
        $this->lc_regs = [];
        $lc_sql = "EXEC config.configuracionAlertaMedios $idCadena, $idRestaurante";

        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "url" => $row['url'],
                                          "activo" => $row['activo'],
                                          "t_frecuencia" => $row['t_frecuencia']
                                         );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function cargarPedidosTransferidos( $idCadena, $idRestaurante, $idPeriodo, $estado, $parametro ){
        $this->lc_regs = [];
        $lc_sql = "EXEC [dbo].[App_cargar_pedidos_transferidos] $idCadena, $idRestaurante, '$idPeriodo', '$estado', '$parametro'";

        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "codigo_app" => $row['codigo_app'],
                                            "forma_pago" => $row['forma_pago'],
                                            "codigo" => $row['codigo'],
                                            "documento" => $row['documento'],
                                            "cliente" => utf8_encode($row['cliente']),
                                            "fecha" => $row['fecha'],
                                            "total" => $row['total'],
                                            "telefono" => $row['telefono'],
                                            "direccion_factura" => utf8_encode($row['direccion_factura']),
                                            "email" => utf8_encode($row['email']),
                                            "direccion_despacho" => utf8_encode($row['direccion_despacho']),
                                            "datos_envio" => utf8_encode($row['datos_envio']),
                                            "observacion" => utf8_encode($row['observacion']),
                                            "estado" => $row['estado'],
                                            "idMotorizado" => $row['idMotorolo'],
                                            "motorizado" => utf8_encode($row['motorolo']),
                                            "motorizado_telefono" => $row['motorolo_telefono'],
                                            "tiempo" => $row['tiempo'],
                                            "medio" => $row['medio'],
                                            "color_fila" => $row['color_fila'],
                                            "color_texto" => $row['color_texto'],
                                            "audio" => $row['audio'],
                                            "cambio_estado" => $row['cambio_estado'],
                                            "codigo_externo" => $row['codigo_externo'],
                                            "codigo_factura" => $row['codigo_factura'],
                                            "tipo_transferencia" => $row['tipo_transferencia'],
                                            "idLocal" => $row['idLocal'],
                                            "local" => $row['local'],
                                            "codigoLocal" => $row['codigoLocal'],
                                            "usuarioTransfiere" => $row['usuarioTransfiere'],
                                            "motivo" => $row['motivo']);
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function verificarMotorizadoAgregador($periodo, $medio){
        $this->lc_regs = [];
        $lc_sql = "EXEC [dbo].[ExistenciaMotorizadoAgregadorPeriodo] '$periodo', '$medio'";

        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "respuesta" => $row['respuesta']);
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }
    
function agregadores (){
    $this->lc_regs = [];
    $query = "SELECT * FROM [config].[fn_listaAgregadores]()";
    try {
        $this->fn_ejecutarquery( $query );
        while ($row = $this->fn_leerarreglo()) {
            $this->lc_regs[] = array( "id" => $row['id'],
            "descripcion" => $row['descripcion'] );
}
        $this->lc_regs['registros'] = $this->fn_numregistro();
            
    } catch (Exception $e) {
        return $e;
    }
    return json_encode($this->lc_regs);
}

    function obtenerNombreProveedorDeliveryPorMedio($idCadena,$idRestaurante,$medio)
    {
        $this->lc_regs = [];
        $lc_sql = "EXEC config.[USP_ObtenerNombreProveedorDeliveryPorMedio] $idCadena, $idRestaurante,'$medio'";

        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( 
                                        "nombre_proveedor" => utf8_encode($row['nombre_proveedor'])
                                         );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function obtenerConfiguracionCambioEstadosAutomatico($cfac_id)
    {
        $lc_sql = "EXEC [config].[USP_ObtenerConfiguracionDeliveryPorCanalFactura] '$cfac_id'";
  
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array("cambio_estado_automatico" => $row['cambio_estado_automatico'],
                                        "nombre_proveedor" => $row['nombre_proveedor']);

            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($result);
    }

    function obtenerTokenAutenticationDuna($idCadena)
    {
        $lc_sql = "EXEC config.USP_ObtenerIdentityTokenDuna ".$idCadena;

        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array("identity_token" => $row['identity_token']);
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }

        return json_encode($result);
    }

    function guardarAuditoriaEstadosApp($opcion,$url, $peticion, $estado, $mensaje)
    {
        $lc_sql = "EXEC [config].[IAE_Auditoria_Estados_App] '$opcion','$url', '$peticion', '$estado', '$mensaje'";

        try {
            $this->fn_ejecutarquery($lc_sql);
            return true;
        } catch (Exception $e) {
            return $e;
        }
        return false;
        
    }

    

    /**
     * Desarrollado por jean meza cevallos, extrae JSON para enviar a pedidos ya el
     * api intermedio
     */
    function enviarConsultaAgregadores($cdn_id, $idRestaurante, $medio, $codigo_factura, $cotizar = false){ //, $idMotorizado
        $this->lc_regs = [];
        $lc_sql = "EXEC config.USP_Retorna_Direccion_Webservice ".$idRestaurante.", '".$medio." API', 'CREAR ORDEN', 0";
        try {
            $this->fn_ejecutarquery( $lc_sql );
            $row = $this->fn_leerarreglo();
            set_time_limit(120); //2 MINS

            $objetoFactura = array(
                'cdn_id'  => $cdn_id,
                'rst_id'  => $idRestaurante,
                'medio'   => $medio,
                'factura' => $codigo_factura,
                'cotizar' => $cotizar,
                'data'    => array($this->obtenerDataAgregador($cdn_id, $idRestaurante, $codigo_factura, $medio)['dataAgregador'])
            );
            
            $url = $row['direccionws'];
            $url=str_replace("http://https://", "https://",$url);
            $url=str_replace("http://http://", "http://",$url);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($objetoFactura));
            $result = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Comprobar si occurió algún error
            if(curl_errno($ch) || $status==0){
                $this->insertaAuditoriaDuna($url, 'Crear orden: ' . $codigo_factura, 101, json_encode(array("mensaje"=>"Ocurrio un error con la petición CURL","respuesta"=>"ocurrio un error CURL o estado 0: ".curl_error($ch)), true));
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array(
                    "error"=>"ocurrio un error CURL o estado 0: ".curl_error($ch),
                    "response"=>$result,
                    "payload"=>$objetoFactura
                ));
                curl_close($ch); 
                exit(0);
            }

            curl_close($ch); 
            
            if (($status == 200 || $status == 204)
                && !empty($result) && !strpos(strtolower($result), "error")){
                $result_string=$result;
                $result = json_decode($result_string, true);

                if (array_key_exists('id', $result["jsonResult"])){
                    $this->insertaAuditoriaDuna($url, 'Crear Orden: '.$codigo_factura, $status, $result_string);
                    $result_codigo = $result["jsonResult"]["id"];
                    $result_json = $result["jsonResult"];
                }else{
                    $this->insertaAuditoriaDuna($url, 'Crear orden: ' . $codigo_factura, 100, json_encode(array("mensaje"=>"Respuesta correpta en maxpoint, pero payload no se adapta y no se encuentra id","respuesta"=>$result_string), true));
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(array(
                        "error"=>"ocurrio un error",
                        "response"=>$result,
                        "payload"=>$objetoFactura
                    ));
                    //throw new Exception(curl_error($ch), curl_errno($ch));//para detener el modal
                    exit(0);
                }

                if  ($result_mot = $this->asignarMotorizadosAgregadores($result_codigo, $result_json, $codigo_factura, $medio)) { //, $idMotorizado
                    $result = array(
                        'success' => 'correcto',
                        'motorizado' => $medio,
                        'pedido' => $result_json,
                    );                    
                }

            }else {

                if (strpos(strtolower($result), "status")) { //ERROR SIN CODIGO, ERROR INTERNO
                    $result = json_decode($result, true);
                    $this->insertaAuditoriaDuna($url, 'Crear orden: ' . $codigo_factura, $result["status"], json_encode($result, true));
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(array(
                        "error"=>"ocurrio un error",
                        "response"=>$result,
                        "payload"=>$objetoFactura
                    ));
                    //throw new Exception(curl_error($ch), curl_errno($ch));//para detener el modal
                    exit(0);
                }else{
                    $this->insertaAuditoriaDuna($url, 'Crear orden: ' . $codigo_factura, $status, $result);
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(array(
                        "error"=>"ocurrio un error",
                        "response"=>json_decode($result, true),
                        "payload"=>$objetoFactura
                    ));
                    //throw new Exception(curl_error($ch), curl_errno($ch));//para detener el modal
                    exit(0);
                }
            }      

            header('Content-Type: application/json; charset=utf-8');
            return json_encode($result);
                
        } catch (Exception $e) {
            //return $e;
            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);
        }
    }

    /**
     * obtiene los datos para el agregador, esta funcion es llamada dentro de enviarConsultaAgregadores
     */
    function obtenerDataAgregador( $cdn_id, $idRestaurante, $cfacId, $medio ) {        
        $this->lc_regs = [];
        $lc_sql = "SET NOCOUNT ON; EXEC [facturacion].[BuildJSON_Facturacion_Agregadores] ".$cdn_id.", ".$idRestaurante.",'".$cfacId."', '".$medio."'" ;
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "dataAgregador" => json_decode(utf8_encode($row['jsonFactura']), true));
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    function asignarMotorizadosAgregadores($id_agregador, $json_all, $codigo_factura, $medio) { //, $idMotorizado
        $cadena = $_SESSION['cadenaId'];

        $result_all = array(
            'Cabecera_FacturaVarchar1' => $id_agregador, 
            'Cabecera_FacturaVarchar2' => $json_all,
            'Cabecera_FacturaVarchar3' => $cadena,
            'Cabecera_FacturaVarchar4' => $medio
        );

        if ($medio == "UBER") {
            $sql_quoteUberCash = "UPDATE Cabecera_App SET cotizacion_id = ISNULL(cotizacion_id, '".$id_agregador."') WHERE cfac_id = '".$codigo_factura."' or codigo_app = '".$codigo_factura."'";
            $this->fn_ejecutarquery( $sql_quoteUberCash );
            $quoteUberCash = $this->fn_leerarreglo();
        }

        if ($medio != 'DRAGONTAIL') {
            $sql_agregador = "UPDATE Cabecera_App SET
                    respuesta_Agregador = '".json_encode($result_all)."'
                WHERE cfac_id = '".$codigo_factura."' or codigo_app = '".$codigo_factura."'";
        } else {
            $sql_agregador = "UPDATE Cabecera_App SET
                    respuesta_bringg = '".json_encode($result_all)."'
                WHERE cfac_id = '".$codigo_factura."' or codigo_app = '".$codigo_factura."'";
        }
        $this->fn_ejecutarquery( $sql_agregador );
        $agregador = $this->fn_leerarreglo();

        $idUsuario = $_SESSION['usuarioId'];

        $sql_codigo = "SELECT codigo_app FROM Cabecera_App WHERE (cfac_id = '".$codigo_factura."'  or codigo_app = '".$codigo_factura."') AND estado = 'POR ASIGNAR'";
        $this->fn_ejecutarquery( $sql_codigo );
        $codigo = $this->fn_leerarreglo();

        $this->insertaAuditoriaDuna($medio." - ".$codigo_factura, 'Crear Orden Motorizado: '.$id_agregador, 200, "null");

        //$result = $this->asignarMotorizado( $idMotorizado, $codigo["codigo_app"], $idUsuario );
        //return $result;
        return true;
    }

    public function insertaAuditoriaDuna($url, $peticion, $estado, $mensaje)
    {
        $query = "INSERT INTO dbo.Auditoria_EstadosApp
            ( url, peticion, estado, mensaje, fecha )
        VALUES  
            ( '$url', '$peticion', '$estado', '$mensaje', GETDATE())";

        return $this->fn_ejecutarquery($query);
    }

    function getPoliticaTiempoEsperaUltimaMilLa($idCadena, $idRestaurante, $descriptionCR, $descriptionCDR)
    {
        $this->lc_regs = [];
        $lc_sql = "EXEC [dbo].[getConfigPoliticaRestaurante] '$idCadena', $idRestaurante,'$descriptionCR', '$descriptionCDR'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = $row['variableI'];
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function getRiderConfig($ccdescription, $cdcdescription)
    {
        $lc_sql = "EXEC [config].[getConfigCadena] '$ccdescription','$cdcdescription'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $result = $row['variableV'];

        } catch (Exception $e) {
            return $e;
        }
        return ($result);
    }

    function getMotorizado($IDMotorolo)
    {
        $lc_sql = "select IDMotorolo from motorolo where documento = '$IDMotorolo'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array('motorizado' => $row['IDMotorolo']);

        } catch (Exception $e) {
            return $e;
        }
        return ($this->lc_regs);
    }

    function getAnulacionID($mtv_descripcion)
    {
        $lc_sql = "select IDMotivoAnulacion from motivo_Anulacion where  mtv_descripcion = '$mtv_descripcion'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array('ID' => $row['IDMotivoAnulacion']);

        } catch (Exception $e) {
            return $e;
        }
        return ($this->lc_regs);
    }

    function getConfigCancelMotives($ccdescription, $cdcdescription)
    {
        $lc_sql = "EXEC [config].[getConfigCadena] '$ccdescription','$cdcdescription'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $result = $row['variableV'];

        } catch (Exception $e) {
            return $e;
        }
        return ($result);
    }

    function getDragonTailStatus($rst_id, $cadena_id, $ccdescription, $cdcdescription)
    {
        $this->lc_regs = [];
        $lc_sql = "EXEC [dbo].[getConfigPoliticaRestaurante] $cadena_id, $rst_id, '$ccdescription', '$cdcdescription'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "active" => $row['variableB']
                );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return ($this->lc_regs);
    }

    /**
     * PROCESO PICKING AGREGADORES
     */
    function obtenerActualizarCodigo($cdn_id, $restaurante, $accion, $periodo){
        if ($accion == 1){
            $codigo = '';
        }
        if ($accion == 2){
            $codigo = $this->generateRandomString();
        }
        $this->lc_regs = [];
        $lc_sql = "SET NOCOUNT ON; EXEC [config].[sp_mostrar_codigo_restaurante_estacion] $cdn_id, $restaurante, $accion, '".$codigo."', '".$periodo."'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ($this->fn_numregistro() > 0) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "codigo" => $row['estado'],
                                        "aplica_politicas" => $row['aplica_politicas'] );
            }
            $this->lc_regs['registros'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function generateRandomString($length = 4) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function imprimirCodigo($lc_datos){
        $lc_sql = "EXEC [facturacion].[IAE_grabacanalMovimientoFactura_CodigoConfirmacionDelivery]  '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]', $lc_datos[4]";
        return $this->fn_ejecutarquery($lc_sql);
    }

    function getRestaurantConfig($idCadena, $idRestaurante, $descriptionCR, $descriptionCDR, $config)
    {
        $this->lc_regs = [];
        $lc_sql = "EXEC [dbo].[getConfigPoliticaRestaurante] '$idCadena', $idRestaurante,'$descriptionCR', '$descriptionCDR'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = isset($row[$config]) ? $row[$config] : null;
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function getPickingFlaw($codApp) {
        $lc_sql = "SELECT  pickupAgregador
        FROM Cabecera_App AS CA
        INNER JOIN Periodo AS PE ON PE.rst_id = CA.cod_Restaurante
        WHERE PE.prd_fechacierre IS NULL 
        AND PE.prd_varchar1 IS NOT NULL
        ANd CA.pickupAgregador IS NOT NULL
        AND CA.codigo_app = '$codApp'";

        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $result= $row['pickupAgregador'];

        } catch (Exception $e) {
            return $e;
        }
        return ($result);
    }

    /**
     * Desarrollado por: Jean Meza Cevallos
     * Permite cargar los pedidos pendientes que se envia a los proveedores para obtener delivery
     * esto en el caso de que los pedidos fallen
     */
    function cargarDeliveryPedidosPendientes( $idCadena, $idRestaurante, $idPeriodo ){
       $this->lc_regs = [];
       $lc_sql = "EXEC dbo.App_delivery_pedidos_pendientes $idCadena, $idRestaurante, '$idPeriodo'";
       try {
           $this->fn_ejecutarquery($lc_sql);
           while ($row = $this->fn_leerarreglo()) {
               $this->lc_regs[] = array( 
                                        "fecha" => $row['fecha'],
                                        "medio" => $row['medio'],
                                        "codigo_app" => $row['codigo_app'],
                                        "codigo_factura" => $row['codigo_factura'],
                                        "codigo_externo" => $row['codigo_externo'],
                                        "duna_reintentos" => $row['duna_reintentos'],
                                        "crea_duna" => $row['crea_duna'],
                                        "asigna_duna" => $row['asigna_duna'],
                                        "envio_inmediato" => $row['envio_inmediato'],
                                        "opciones_proveedor" => urlencode($row['opciones_proveedor']),
										"retira_efectivo" => $row['retira_efectivo'],
                                       );
           }
           $this->lc_regs['registros'] = $this->fn_numregistro();
       } catch (Exception $e) {
           print('EXCEPCION');
           print($e);
           return $e;
       }
       return json_encode($this->lc_regs);
   }

    function validarPoliticaRestauranteMonitor()
    {
        $lc_sql = "SELECT ccdd.variableB as habilitado
        FROM ColeccionRestaurante AS cc WITH(NOLOCK)
        INNER JOIN ColeccionDeDatosRestaurante AS cddc WITH(NOLOCK) ON cddc.ID_ColeccionRestaurante = cc.ID_ColeccionRestaurante
        INNER JOIN RestauranteColeccionDeDatos AS ccdd WITH(NOLOCK) ON ccdd.ID_ColeccionRestaurante = cc.ID_ColeccionRestaurante AND ccdd.ID_ColeccionDeDatosRestaurante = cddc.ID_ColeccionDeDatosRestaurante
        WHERE 
        cc.Descripcion = 'CONFIGURACION MONITOR'
        AND cddc.Descripcion = 'INTEGRAR MONITOR'
        AND cc.isActive =1
        AND cddc.isActive = 1
        AND ccdd.isActive = 1";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array("habilitado" => ($row['habilitado'] == 1) ? "SI" : "NO");
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }

        return json_encode($result);
    }

    function cargarDatosPedido($codigo_app, $cod_restaurante)
    {
        $lc_sql = "	SELECT TOP 1
                    Ap.estado as 'estado',
                    ISNULL(fd.cfac_id, '') as 'cfac_id',
                    Ap.cod_Restaurante as 'cod_restaurante',
                    Ap.codigo_app as 'codigo_app',
                    Ap.nombres_cliente as 'nombre_cliente',
                    Ap.telefono_cliente as 'telefono',
                    Ap.medio as 'medio',
                    ISNULL(CONCAT(m.nombres,' ',m.apellidos), '') as 'motorolo',
                    Ap.respuesta_bringg as 'respuesta_bringg'
                    --, Ap.urlUltimaMIlla as 'urlultimamilla'
                    FROM
                    Cabecera_App AS Ap WITH (NOLOCK)
				    LEFT JOIN Cabecera_Factura fd on fd.cfac_id = Ap.cfac_id 
				    LEFT JOIN Motorolo m WITH (NOLOCK) on Ap.IDMotorolo = m.IDMotorolo
                    WHERE
                    Ap.cod_Restaurante = " . $cod_restaurante . "
                    AND Ap.codigo_app LIKE '%" . $codigo_app . "'";

        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $respuesta_bringg = json_decode($row["respuesta_bringg"]);
            $tracking = "";
            if ($respuesta_bringg != null) {
                if (isset($respuesta_bringg->orders)) {
                    $tracking = $respuesta_bringg->orders[0]->trackUrl;
                }
            }

            $this->lc_regs = array(
                "estado" => $row['estado'],
                "cfac_id" => $row['cfac_id'],
                "cod_restaurante" => $row["cod_restaurante"],
                "codigo_app" => $row["codigo_app"],
                "nombre_cliente" => $row["nombre_cliente"],
                "telefono" => $row["telefono"],
                "medio" => $row["medio"],
                "motorolo" => $row["motorolo"],
                "tracking" => $tracking
            );
        } catch (Exception $e) {
            return $e;
        }
        return json_encode($this->lc_regs);
    }

    function obtenerURLMonitor()
    {
        $lc_sql = "SELECT [config].[fn_coleccionDatosRestaurantesDomicilioCaracter] ('CONFIGURACION MONITOR', 'URL MONITOR') as url";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array("url" => $row['url']);
            }
            $result = $this->lc_regs;
        } catch (Exception $e) {
            return $e;
        }

        return $result;
    }

    function notificarPedidoAuditoria(
        $idRestaurante, 
        $idUsuario, 
        $dataAudit,
        $codigo
    ) {

        $dataAudit = str_replace("'", "''", $dataAudit);
        $lc_sql = "EXEC [config].[IAE_Audit_registro] 'i', '$idUsuario', $idRestaurante, 'NOTIFICAR PEDIDO', '$dataAudit', '$codigo'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            return true;
        } catch (Exception $e) {
            return $e;
        }
    }

}