<?php
session_start();

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_servicioImpresion.php";

$request = json_decode(file_get_contents("php://input"));
$tipoDocumentoReimpresion = "";
if (isset($request->session)) {
    // Only for ./clases/app.Cadena.php method requestClientWsServicePrint
    $session = $request->session;
    $idRestaurante = $session->rstId;
    $idCadena =  $session->cadenaId;
    $idEstacion = $session->estacionId;
    $idPeriodo = $session->IDPeriodo;
    $idUsuario = $session->usuarioId;
    $ipEstacion = $session->direccionIp;
    $idControlEstacion = $session->IDControlEstacion;
    $tipoServicioTienda = $session->TipoServicio;
    $lc_usuarioIdAdmin = isset($session->usuarioIdAdmin) ? $session->usuarioIdAdmin : null;
    // Politicas Sericio Api Impresion
    $servicioApiImpresion = json_decode($session->servicioApiImpresion);
    $codigo_app = $request->codigo_app;
    $codigo_factura = $request->codigo_factura;
    $codigo_orden = $request->codigo_orden;
} else {
    $request = (object) filter_input_array(INPUT_POST);
    if($request->tipo == 'reimpresionDesmontadoCajero' || $request->tipo == 'reimpresionFinDelDia'){
        $datosAdicionales=$request->datosAdicionales;
        $ipEstacion=$datosAdicionales['ipEstacion'];
        $idEstacion=$datosAdicionales['IDEstacion'];       
        $servicioApiImpresion = json_decode($datosAdicionales['servicioImpresion']);
        $idUsuario=$datosAdicionales['idUsuario'];
        $tipoDocumentoReimpresion = $request->tipo;
    }
    else if($request->tipo == 'creacionReporteDesmontadoCajero' || $request->tipo == 'creacionReporteFinDelDia'){
        $datosAdicionales=$request->datosAdicionales;
        $ipEstacion=$datosAdicionales['IPEstacion'];
        $idEstacion=$datosAdicionales['IDEstacion'];
        $servicioApiImpresion = json_decode($datosAdicionales['servicioImpresion']);
        $idUsuario=$datosAdicionales['idUsuarioAdmin'];
    }
    else if($request->tipo != 'test_impresion'){
        $idRestaurante = $_SESSION['rstId'];
        $idCadena = $_SESSION['cadenaId'];
        $idEstacion = $_SESSION['estacionId'];
        $idPeriodo = $_SESSION['IDPeriodo'];
        $idUsuario = $_SESSION['usuarioId'];
        $ipEstacion = $_SESSION['direccionIp'];
        $idControlEstacion = $_SESSION['IDControlEstacion'];
        $tipoServicioTienda = $_SESSION['TipoServicio'];
        $lc_usuarioIdAdmin = isset($_SESSION['usuarioIdAdmin']) ? $_SESSION['usuarioIdAdmin'] : null;
        // Politicas Sericio Api Impresion
        $servicioApiImpresion = json_decode($_SESSION['servicioApiImpresion']);
    }
    else{
        $datosAdicionales=$request->datosAdicionales;
        $ipEstacion=$datosAdicionales['estacion'];
        $idEstacion=$datosAdicionales['IDEstacion'];       
        $servicioApiImpresion = json_decode($datosAdicionales['servicioImpresion']);
        $idUsuario=''; 
    }

    if($request->tipo == 'impresionCampanaSolidaria') {
        $datosAdicionales = $request->datosAdicionales;
        $campanaSolidaria = $datosAdicionales['codigo'];
        $ruta = $datosAdicionales['ruta'];
    }

    $codigo_app = null;
    $codigo_factura = null;
    $codigo_orden = null;
}

$timeout = $servicioApiImpresion->timeout;
$reintentos = $servicioApiImpresion->reintentos;
$url = $servicioApiImpresion->url;
$ruta = $servicioApiImpresion->ruta;
$curl = $url.$ruta;
$imp_error = 0;

$servicio = new ServicioImpresion();

if ($request->metodo == "apiServicioImpresion") {
    try {
        $tipoDocumento = $request->tipo;
        $transaccion = $request->transaccion;
        $idCabeceraOrdenPedido = $request->idCabeceraOrdenPedido;
        $datosAdicionales = $request->datosAdicionales;

        $result = array(
            "error" => false,
            "mensaje" => ""
        );

        /* Se valida que la politica se ecuente configurada */
        if ($url == "0" || $ruta == "0") {
            $result["error"] = true;
            $result["mensaje"] = "Las politicas SERVICIO API IMPRESION no se encuentran configuradas, por favor comuniquese con soporte!!";

            $servicio->auditoriaApiImpresion($tipoDocumento, 'ERROR', null, $idEstacion, $idUsuario, $curl, null, null, $_SESSION["servicioApiImpresion"]);

            print json_encode($result);

            die();
        }

        $parametros = 0;
        $parametroOpcional = '';
        $parametroOpcionalOrdenPedido = '';
        $parametroOpcionalfactura = '';
        /* Impresion de documentos */
        if ($tipoDocumento == 'factura') {
            $parametros = $servicio->impresionFactura($transaccion, $idEstacion);
            $parametroOpcional = $transaccion;
            $parametroOpcionalfactura = $transaccion;
        }else if($tipoDocumento == 'nota_credito') {
            $idEstacion = $datosAdicionales['idEstacion'];
            $curl = $datosAdicionales['servidorUrlApi'].$ruta;
            $parametroOpcional = $transaccion;
            $parametros = $servicio->impresionNotaCredito($idRestaurante, $idCadena, $idEstacion, $transaccion, $idUsuario, $ipEstacion);
        }else if ($tipoDocumento == 'reimpresion_factura') {
            $parametros = $servicio->impresionReimpresionFactura($idRestaurante, $idCadena, $idEstacion, $transaccion, $idUsuario);
        }else if ($tipoDocumento == 'impresion_cambio_datos_cliente') {
            $idEstacion = $datosAdicionales['idEstacion'];
            $ipEstacionCambioDatos = $datosAdicionales['ipEstacionCambioDatos'];
            $parametroOpcional = $transaccion;
            $curl = $datosAdicionales['servidorUrlApi'].$ruta;
            if(!empty($datosAdicionales) && isJson($datosAdicionales)){
                $datosAdicionales = json_decode($datosAdicionales);
                $parametros = $servicio->impresionCambioDatosCliente($idRestaurante, $idCadena, $idEstacion, $transaccion, $datosAdicionales->accion, $datosAdicionales->documentoCliente, $idUsuario, $ipEstacionCambioDatos);
            }else{
                $parametros = $servicio->impresionCambioDatosCliente($idRestaurante, $idCadena, $idEstacion, $transaccion, $datosAdicionales['accion'], $datosAdicionales['documentoCliente'], $idUsuario, $ipEstacionCambioDatos);
            }    
        }else if ($tipoDocumento == 'orden_pedido') {
            $parametroOpcional = $idCabeceraOrdenPedido;
            $parametroOpcionalOrdenPedido = $idCabeceraOrdenPedido;
            if (isset($datosAdicionales['fidelizacion'])): $fidelizacion = 1; else: $fidelizacion = 0; endif;
            $parametros = $servicio->impresionOrdenPedido($idCabeceraOrdenPedido, $idUsuario, $idRestaurante, $datosAdicionales['dop_cuenta'], $datosAdicionales['guardarOrden'], $datosAdicionales['imprimeTodas'], 1, $fidelizacion);

            if (count($parametros) == 0) {
                    $result["error"] = false;
                    $result["mensaje"] = "No existen ordenes pendientes por imprimir.";
                    print json_encode($result);
                die();
            }
        }else if($tipoDocumento == 'reimprimir_orden') {
            $parametros = $servicio->impresionReimprimirOrden($transaccion, $idCadena, $idRestaurante, $datosAdicionales['dop_id'], 0, $idEstacion);
        }else if($tipoDocumento == 'impresion_precuenta') {
            if ($datosAdicionales['dop_cuenta'] == ''){
                $datosAdicionales['dop_cuenta'] = 1;
            }
            $parametros = $servicio->impresionPreCuenta($transaccion, $idCadena, $idRestaurante, $datosAdicionales['dop_cuenta'], $idEstacion, $datosAdicionales['est_ipd'], $datosAdicionales['opcionImpresion'], $_SESSION['usuarioIdAdmin']);
        }else if ($tipoDocumento == 'factura_orden') {
            $parametroOpcional = $transaccion;
            $parametroOpcionalfactura = $transaccion;
            $entrega=$servicio->app_tipo_entrega_inmediata($idRestaurante);
            if($entrega=='PARCIAL'){
                $parametros = $servicio->impresionFactura($transaccion, $idEstacion);
            }elseif($entrega=='DESPACHO'){
                $NumeroOrden = $servicio->impresionNumeroPedido($transaccion, $idRestaurante);
                $servicio->impresionOrdenPedido($NumeroOrden, $idUsuario, $idRestaurante, 1, 0, 0, 0, 0);
                $parametros = $servicio->impresionFactura($transaccion, $idEstacion);
                $parametroOpcionalOrdenPedido = $NumeroOrden;                
            }            
        }else if ($tipoDocumento == 'promocion_factura'){
            $parametros = $servicio->impresionPromocionFactura($transaccion, $idUsuario, $idRestaurante, $datosAdicionales['dop_id']);
            
            if (count($parametros) == 0) {
                $result["error"] = false;
                $result["mensaje"] = "No existen promociones pendientes por imprimir.";
                print json_encode($result);
                die();
            }
        
        }else if($tipoDocumento == 'factura_tarjeta'){
            $parametros1 = $servicio->impresionOrden($idCadena, $idRestaurante,$idControlEstacion,$transaccion);
            $parametros2 = $servicio->impresionFactura($transaccion, $idEstacion);
            $parametros = null;
            $parametros=array_merge($parametros1,$parametros2);
        }else if($tipoDocumento == 'Voucher'){
            $parametros = $servicio->impresionVoucher($transaccion,'CM',$idEstacion,$idRestaurante);
        }else if($tipoDocumento == 'VoucherAnulacionTransaccion'){
            $parametros = $servicio->impresionVoucherAnulacionTransaccion($transaccion,'CM',$idEstacion,$idRestaurante);
        }else if($tipoDocumento == 'VoucherNo'){
            $parametros = $servicio->impresionVoucherNo($transaccion,$idEstacion,$idRestaurante);
        }else if ($tipoDocumento == 'impresion_credito_empresa'){
            $parametros = $servicio->impresionCreditoEmpresa($transaccion, $datosAdicionales['frmPagoCredito_id'], $datosAdicionales['frmPagoCredito_numSeg'], $datosAdicionales['frmPagoBilleteCredito'], $datosAdicionales['fctTotalCredito'], 0, $datosAdicionales['tfpSwtransaccionalCredito'], $idUsuario, $datosAdicionales['cliCredito'], $datosAdicionales['banderaCredito'], $datosAdicionales['opcionFp'], $datosAdicionales['observacion'], $datosAdicionales['documentoClienteAX'], $datosAdicionales['telefonoClienteAx'], $datosAdicionales['direccionClienteAx'], $datosAdicionales['correoClienteAx'], $datosAdicionales['tipoIdentificacionCLienteExt'], $datosAdicionales['nombreCLienteCredito'], $datosAdicionales['tipoCliCredito'], $datosAdicionales['banderaVitality'], $datosAdicionales['valorCampoCodigo']);
            if (count($parametros) == 0) {
                $result["error"] = false;
                $result["mensaje"] = "No hay nada que imprimir.";
                print json_encode($result);
                die();
            }
        }else if ($tipoDocumento == 'test_impresion'){
            $parametros = $servicio->testImpresion($datosAdicionales['estacion']);
        }else if ($tipoDocumento == 'retiros'){
            $parametros = $servicio->impresionRetiros($datosAdicionales['accion'],$lc_usuarioIdAdmin,$datosAdicionales['estado_asentado_refectivo'],$idUsuario,$datosAdicionales['efectivo_posCalculado'],$datosAdicionales['valor_retiro_efectivo'],$datosAdicionales['estadoRetiro']);
        }else if ($tipoDocumento == 'arqueo'){
            $parametros = $servicio->impresionArqueo($datosAdicionales['usr_id'],$datosAdicionales['ctrc_id'],$lc_usuarioIdAdmin);
        }else if ($tipoDocumento == 'corteX'){
            $parametros = $servicio->corteX($datosAdicionales['usr_id'],$datosAdicionales['ctrc_id'],$lc_usuarioIdAdmin);
        }else if ($tipoDocumento == 'desmontadoCajero'){
            $parametros = $servicio->desmontadoCajero($datosAdicionales['usr_id'],$datosAdicionales['ctrc_id'],$lc_usuarioIdAdmin);
            $parametroOpcional = $datosAdicionales['ctrc_id'];
        }else if($tipoDocumento == 'reimpresionDesmontadoCajero'){
            $parametros = $servicio->desmontadoCajero($datosAdicionales['idUsuario'],$datosAdicionales['idControlEstacion'],$datosAdicionales['idUsuario']);
            $parametroOpcional = $datosAdicionales['idControlEstacion'];
        }else if($tipoDocumento == 'creacionReporteDesmontadoCajero'){
            $parametros = $servicio->desmontadoCajero($datosAdicionales['idUsuarioCajero'],$datosAdicionales['IDControlEstacion'],$datosAdicionales['idUsuarioAdmin']);
            $parametroOpcional = $datosAdicionales['IDControlEstacion'];
        }
        else if ($tipoDocumento == 'findeldia'){
            $parametros = $servicio->findeldia($datosAdicionales['periodo'],$datosAdicionales['estacion'],$lc_usuarioIdAdmin);
            $parametroOpcional = $datosAdicionales['periodo'];
        }else if($tipoDocumento == 'reimpresionFinDelDia'){
            $parametros = $servicio->findeldia($datosAdicionales['periodo'],$datosAdicionales['estacion'],$datosAdicionales['idUsuario']);
            $parametroOpcional = $datosAdicionales['periodo'];
        }
        else if($tipoDocumento == 'creacionReporteFinDelDia'){
            $parametros = $servicio->findeldia($datosAdicionales['IDPeriodo'],$datosAdicionales['IDEstacion'],$datosAdicionales['idUsuarioAdmin']);
            $parametroOpcional = $datosAdicionales['IDPeriodo'];
        }
        else if ($tipoDocumento == 'pickupDesmontarCajero'){
            $parametros = $servicio->impresionDesmontarCajeroPickup($datosAdicionales['usr_id'], $datosAdicionales['ctrc_id'], $lc_usuarioIdAdmin);
        }else if ($tipoDocumento == 'retiraFondos'){
            $parametros = $servicio->retiraFondos('U',$ipEstacion, $datosAdicionales['usr_claveAdmin'],$datosAdicionales['tarjeta'],$idControlEstacion);
        }else if ($tipoDocumento == 'desmontarMotorizado'){
            $parametros = $servicio->desmontarMotorizado( $datosAdicionales['idMotorizado'],$datosAdicionales['idPeriodo'],$idRestaurante,$idUsuario);
        } else if ($tipoDocumento == 'delivery') {
            $entrega = $servicio->app_tipo_entrega_inmediata($idRestaurante);
            if ($entrega == 'INMEDIATA') {               
                $NumeroOrden = $servicio->impresionNumeroPedido($transaccion, $idRestaurante);
                $parametros1 = $servicio->impresionOrdenPedido($NumeroOrden, $idUsuario, $idRestaurante, 1, 0, 0, 0, 0);
                $parametros2 = $servicio->impresionFactura($transaccion, $idEstacion);
                $parametroOpcionalOrdenPedido = $NumeroOrden;
                
                if (!empty($parametros1) &&  !empty($parametros2)) {
                    $parametros = array_merge($parametros1,$parametros2);
                }
                else if (!empty($parametros1)) {
                    $parametros = $parametros1;
                }
                else if (!empty($parametros2)) {
                    $parametros = $parametros2;
                }
            }
            if ($entrega == 'PARCIAL') {
                $NumeroOrden = $servicio->impresionNumeroPedido($transaccion, $idRestaurante);
                $parametros = $servicio->impresionOrdenPedido($NumeroOrden, $idUsuario, $idRestaurante, 1, 0, 0, 0, 0);
                $parametroOpcionalOrdenPedido = $NumeroOrden;
            }
        }else if ($tipoDocumento == 'impresionCupon') {
            $jsonDataRegistro =  array([
                "registrosDetalle" => $datosAdicionales["detalle"]
            ]);
            $nombre_usuario = $_SESSION['nombre'];
            $parametros = $servicio->impresionCupon($transaccion, $idUsuario, $idEstacion, $idRestaurante, $nombre_usuario, json_encode($jsonDataRegistro));

        }else if ($tipoDocumento == 'impresionCuponDigital') {
            $tipoDocumento = 'impresionCupon';

            $datosAdicionales["detail"][0]["descripcion_plu"] = $datosAdicionales["detail"][0]["Descripcion"];
            unset($datosAdicionales["detail"][0]["Descripcion"]);
            
            $datosAdicionales["detail"][0]["cantidad"] = $datosAdicionales["detail"][0]["Cantidad"];
            unset($datosAdicionales["detail"][0]["Cantidad"]);            

            $jsonDataRegistro =  array([
                "registrosDetalle" => $datosAdicionales["detail"]
            ]);
            $nombre_usuario = $_SESSION['nombre'];
            $parametros = $servicio->impresionCupon($transaccion, $idUsuario, $idEstacion, $idRestaurante, $nombre_usuario, json_encode($jsonDataRegistro));

        }else if ($tipoDocumento == 'codigo_confirmacion_delivery') {
            $codigoConfirmacionDelivery = $datosAdicionales['codigoConfirmacionDelivery'];
            $parametros = $servicio->infoCodigoConfirmacionDelivery($idRestaurante, $idEstacion, $codigoConfirmacionDelivery, 1);

        }else if ($tipoDocumento == 'transferencia') {
            $parametros = $servicio->impresionTransferencia($idControlEstacion,$idUsuario);

            if (count($parametros) == 0) {
                $result["error"] = false;
                $result["mensaje"] = "No existen transferencia de venta por imprimir.";
                print json_encode($result);
                die();
            }

        }else if($tipoDocumento == 'VoucherNoCancelar'){
            $parametros = $servicio->impresionVoucherNoCancelar($transaccion,$idEstacion,$idRestaurante,$idUsuario);
        }else if($tipoDocumento == 'impresionFidelizacionRecarga'){
            $parametros = $servicio->impresionFidelizacionRecarga($datosAdicionales,$idCadena,$idRestaurante,$idEstacion);

        }else if($tipoDocumento == 'generarReporte'){
            $parametros = "";

        }else if($tipoDocumento == 'impresionCampanaSolidaria'){
            $parametros = $servicio->impresionCampanaSolidaria($idCadena, $idRestaurante, $campanaSolidaria, $idEstacion, $idUsuario);
        }

        $contador = 1;
        $cantidad=count($parametros);

        $aplicabalanceo = '0';
        $impresorasBalancear = '';

        for ($i = 0; $i < $cantidad; $i++) {


            $impresora = $parametros[$i]["impresora"];
            // Verificar local storage
            $infoDistribucionImpresion = false;
            if($tipoDocumento == 'orden_pedido'){

                if (isset($_SESSION["ImpresoraEnviar"])) {
                    $impresora = $_SESSION["ImpresoraEnviar"];
                }

                // Aplica distribucion de impresion
                $infoDistribucionImpresion = $servicio->distribucionImpresion($impresora, $idCabeceraOrdenPedido);
                
                if($infoDistribucionImpresion){
                    // Verificar si aplica
                    if($infoDistribucionImpresion[0]["aplicaEjecucion"] == '1'){
                        $aplicabalanceo = '1';
                        // Preguntar si es distinto en el local storage
                        $impresora = $infoDistribucionImpresion[0]["impresoraEnviar"];
                        if (isset($_SESSION["ImpresoraEnviar"])) {

                                    if ($_SESSION["ImpresoraEnviar"] != $infoDistribucionImpresion[0]["impresoraEnviar"]){
                                        // Guardar localstorage
                                        $_SESSION["ImpresoraEnviar"] = $impresora;
                                    
                                    }
                            }else{ 
                               $_SESSION["ImpresoraEnviar"] = $impresora;
                            }

                        }else{
                        //  Eliminar local storage 
                        unset($_SESSION["ImpresoraEnviar"]);
                        // Obtener impresora original
                        $impresora = $parametros[$i]["impresora"];
                        }

                    }else{

                        $result["error"] = true;
                        $result["mensaje"] = "Las politicas DISTRIBUCION DE IMPRESION mal configurada, por favor comuniquese con soporte!!";
                        
                    }

                }

            $idMarcaImpresora = $servicio->descripcionTipoImpresora($impresora);

            if ($tipoDocumento == 'delivery' || $tipoDocumento == 'factura_orden') {
                $infoDistribucionImpresion = $servicio->distribucionImpresion($impresora, '');
                    if($infoDistribucionImpresion){
                        if($infoDistribucionImpresion[0]["aplicaEjecucion"] == '1'){
                            $aplicabalanceo = '1';
                            $impresorasBalancear = $infoDistribucionImpresion[0]["impresorasBalancear"];
                        }
                    }
            }

            $dataPayloadEnviar = $parametros[$i]["jsonData"];

            $dataPost = array(
                "idImpresora" => $impresora
                , "idMarca" => $idMarcaImpresora
                , "aplicaBalanceo" => $aplicabalanceo
                , "impresorasBalancear" => $impresorasBalancear
                , "idPlantilla" => ($parametros[$i]["formatoXML"])
                , "data" => json_decode($dataPayloadEnviar, true)
                , "registros" => $dataRegistro = json_decode($parametros[$i]["jsonRegistros"], true)
            );

            $dataFormatedBackSlashes = str_replace("\\",'\\\\',json_encode($dataPost['data']));
            $arrayDataFormatedBackSlashes = explode("\\n",$dataFormatedBackSlashes);
            $arrayDataFormatedBackSlashes = array_map(function($v) { return ltrim($v); }, $arrayDataFormatedBackSlashes);
            $dataFormatedBackSlashes = implode("\\n", $arrayDataFormatedBackSlashes);
            $dataPost['data'] = json_decode($dataFormatedBackSlashes,true);

            $dataString = stripslashes(json_encode($dataPost, JSON_UNESCAPED_UNICODE));

            for ($x = 0; $x < $parametros[$i]["numeroImpresiones"]; $x++) {
                
                if(isset($impresora)){
                    $urlApiImpresion = $servicio->obtenerIpImpresora($impresora);
                    if($urlApiImpresion['urlApiImpresion'] != ''){
                        $curl = $urlApiImpresion['urlApiImpresion'].$ruta;
                    } 
                }
                // Validar si se imprime inmediatamente
                if($parametros[$i]["tipo"] == 'orden_pedido' or $parametros[$i]["tipo"] == 'orden_pedido_hd'){

                    $parametroOpcional = $parametroOpcionalOrdenPedido;
                    $imprimirOrdenesKDS = $servicio->imprimeKDS($idRestaurante, $parametroOpcionalOrdenPedido);
                    
                    if($imprimirOrdenesKDS == 1){

                        $servicio->auditoriaApiImpresion($parametros[$i]["tipo"], 'PENDIENTE', $impresora, $idEstacion, $idUsuario, $curl, $dataString, '', $parametroOpcionalOrdenPedido);

                        $result["error"] = false;
                        $result["mensaje"] = "Las ordenes de pedidos se imprimiran por el KDS.";
                        print json_encode($result);
                        continue;

                    }

                }
                
                 if($parametros[$i]["tipo"] == 'factura'){
                    $parametroOpcional = $parametroOpcionalfactura;
                 }
                 
                curlRetry($curl, $dataString, $timeout, $reintentos, $servicio, count($parametros), $contador, $parametros[$i]["tipo"], $impresora, $idEstacion, $idUsuario,$imp_error, $codigo_app, $codigo_factura, $tipoDocumento, $tipoDocumentoReimpresion, $result, $infoDistribucionImpresion, $ruta, $parametroOpcional);
            }

            $contador = $contador + 1;
        }
    } catch (\Throwable $th) {
        print json_encode($th);
    }    
}

function curlRetry($curl, $payload, $timeout, $reintentos, $servicio, $cantidadRegistros, $contador, $tipoDocumento, $impresora, $idEstacion, $idUsuario,$imp_error, $codigo_app, $codigo_factura, $tipoEntrega, $tipoDocumentoReimpresion, $result, $infoDistribucionImpresion, $ruta, $opcional = null) {

    $intento = 1;

    for ($i = 0; $i < $reintentos; $i++) { 
            $header = array();
            $header[] = "Content-Type: application/json";
            $header[] = "Accept: application/json";
            $ch = curl_init($curl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

            $response = curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if($response){

                $resposeDecode = json_decode($response);
                $errorRespuesta = $resposeDecode->error;

            }else{

                $errorRespuesta = true;
            }

            if (in_array($status_code, array(200, 404)) && !$errorRespuesta) {

                if($tipoDocumentoReimpresion != "reimpresionFinDelDia" && $tipoDocumentoReimpresion != "reimpresionDesmontadoCajero"){
                    $servicio->auditoriaApiImpresion($tipoDocumento, 'SUCCESS', $impresora, $idEstacion, $idUsuario, $curl, $payload, $response, $opcional, $codigo_app, $codigo_factura, $tipoEntrega);
                }
    
                if ($cantidadRegistros == $contador) {
                    print ($response);
                    break;
                }
    
                break;
            } else {

                if($infoDistribucionImpresion){
        
                    if($infoDistribucionImpresion[0]["aplicaEjecucion"] == '1'){

                        $result["error"] = true;
                        $result["mensaje"] = "Ha ocurrido un error al momento de imprimir, por favor comuniquese con soporte!!";
        
                        $servicio->auditoriaApiImpresion($tipoDocumento, 'ERROR', $impresora, $idEstacion, $idUsuario, $curl, $payload, $response, $opcional, $codigo_app, $codigo_factura, $tipoEntrega);

                        if ($intento == $reintentos) {

                            if($imp_error == 0){
                                print json_encode($result);
                            }
                           
                                break;
                        }

                    }

                }


                if ($intento == $reintentos) {

                    $result["error"] = true;
                    $result["mensaje"] = "Ha ocurrido un error al momento de imprimir, por favor comuniquese con soporte!!";
    
                    $servicio->auditoriaApiImpresion($tipoDocumento, 'ERROR', $impresora, $idEstacion, $idUsuario, $curl, $payload, $response, $opcional, $codigo_app, $codigo_factura, $tipoEntrega);
                    if($imp_error == 0){
                        print json_encode($result);
                    }
                   
                        break;

                    }
         
                $intento = $intento + 1;

                if($errorRespuesta){

                    if($infoDistribucionImpresion){
        
                        if($infoDistribucionImpresion[0]["aplicaEjecucion"] == '1'){
                
                            $dataPost = json_decode($payload,true);
                
                            $impresoraNuevaEnviar = $servicio->cambiarImpresora($impresora);
                
                            $dataPost['idImpresora'] = $impresoraNuevaEnviar['nuevaImpresora'];
                
                            $urlApiImpresion = $servicio->obtenerIpImpresora($impresoraNuevaEnviar['nuevaImpresora']);
                            if($urlApiImpresion['urlApiImpresion'] != ''){
                                $curl = $urlApiImpresion['urlApiImpresion'].$ruta;
                            }
                
                            $payloadDistribucion =  json_encode($dataPost);

                            $header = array();
                            $header[] = "Content-Type: application/json";
                            $header[] = "Accept: application/json";
                            $chDistribucionImpresion = curl_init($curl);
                            curl_setopt($chDistribucionImpresion, CURLOPT_CUSTOMREQUEST, "POST");
                            curl_setopt($chDistribucionImpresion, CURLOPT_POSTFIELDS, $payloadDistribucion);
                            curl_setopt($chDistribucionImpresion, CURLOPT_RETURNTRANSFER, TRUE);
                            curl_setopt($chDistribucionImpresion, CURLOPT_HTTPHEADER, $header);
                            curl_setopt($chDistribucionImpresion, CURLOPT_TIMEOUT, $timeout);
                            curl_setopt($chDistribucionImpresion, CURLOPT_CONNECTTIMEOUT, $timeout);
                            curl_setopt($chDistribucionImpresion, CURLOPT_FRESH_CONNECT, TRUE);
                            curl_setopt($chDistribucionImpresion, CURLOPT_SSL_VERIFYPEER, FALSE);
                            curl_setopt($chDistribucionImpresion, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                
                            $responseDistribucionImpresion = curl_exec($chDistribucionImpresion);
                            $status_codeDistribucionImpresion = curl_getinfo($chDistribucionImpresion, CURLINFO_HTTP_CODE);
                
                            if (in_array($status_codeDistribucionImpresion, array(200, 404))) {
                
                                $servicio->auditoriaApiImpresion($tipoDocumento, 'SUCCESS', $impresoraNuevaEnviar['nuevaImpresora'], $idEstacion, $idUsuario, $curl, $payloadDistribucion, $responseDistribucionImpresion, $opcional, $codigo_app, $codigo_factura, $tipoEntrega);
                
                                    if ($cantidadRegistros == $contador) {
                                        print ($responseDistribucionImpresion);
                                        break;
                                    }
                
                                break;
                            } else {
                
                
                                $result["error"] = true;
                                $result["mensaje"] = "Las dos impresoras establecidas en al distribucion de impresion se encuentran fuera de servicio, no se pudo imprimir";
                
                                $servicio->auditoriaApiImpresion($tipoDocumento, 'ERROR', $impresoraNuevaEnviar['nuevaImpresora'], $idEstacion, $idUsuario, $curl, $payloadDistribucion, json_encode($result), $opcional, $codigo_app, $codigo_factura, $tipoEntrega);
                                if($imp_error == 0){
                                    print json_encode($result);
                                }
                
                                if ($intento == $reintentos) {
                                        break;
                
                                }else{
                                    $intento = $intento + 1;
                                    continue;
                                }
                
                                    
                                
                            }
                
                        }
                
                    }

                }

            }


    }

    curl_close($ch);
}


function isJson($string) {
    return ((is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))))) ? true : false;
}
