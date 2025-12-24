<?php

@session_start();

include("../system/conexion/clase_sql.php");
include("../clases/app.Cadena.php");
include("../resources/module/domicilio/App.php");
include("../resources/module/domicilio/bringg/BringgApp.php");
include("../resources/module/domicilio/duna/DunaApp.php");
include("../resources/module/domicilio/proveedorNoAsignado/ProveedorNoAsignado.php");
include("../clases/clase_uberDirect_cash.php");
include("../clases/clase_tirillaPromocion.php");
include_once "../clases/clase_webservice.php";

// Objects
$apps = new Cadena();
$uberDirectCash = new uberDirectCash();
$tirillaPromocion = new TirillaPromocionOTP();

// Data
$idCadena          = $_SESSION['cadenaId'];
$idRestaurante     = $_SESSION['rstId'];
$idEstacion        = $_SESSION['estacionId'];
$ip                = $_SESSION['direccionIp'];
$idUsuario         = $_SESSION['usuarioId'];
$idControlEstacion = $_SESSION['IDControlEstacion'];
$idPeriodo         = $_SESSION['IDPeriodo'];

$bringgApp = new BringgApp($idCadena, $idRestaurante);
$dunaApp = new DunaApp($idCadena, $idRestaurante);
$proveedorNoAsignado= new ProveedorNoAsignado($idCadena, $idRestaurante);

// Webservice
$servicioWebObj = new Webservice();

// REQUEST
$request = (object) filter_input_array(INPUT_POST);


// CARGAR TRANSACCIONES
if ($request->metodo === "cargarPedidosAppPorEstado") {
    $estado = $request->estado;
    print $apps->cargarPedidosPorEstado( $idCadena, $idRestaurante, $estado );
} 
// CARGAR DETALLE TRANSACCION
else if ($request->metodo === "cargarDetallePedidoApp") {
    $codigo = $request->codigo;
    $medio = $request->medio;
    print $apps->cargarDetallePedidoApp( $idCadena, $idRestaurante, $codigo, $medio );
} 
// FACTURAR TRANSACCION
else if ($request->metodo === "facturarPedidoApp") {
    $codigo = $request->codigo;
    print $apps->facturarPedidoApp( $idCadena, $idRestaurante, $codigo, $idEstacion, $idUsuario, $idControlEstacion );
// CONSULTA PRODUCTOS HABILITADOS PARA CUPONES
} else if ($request->metodo === "promocionesMovistar") {
    $datos[0] = $request->idFactura; 
    print $apps->cuponesMovistar($datos);
// [INICIO] METODOS FUNCIONALIDAD CUPONES MOVISTAR
} else if ($request->metodo === "SetQRPromocionesMovistar") {
    $datos[0] = $request->cfac; 
    $datos[1] = $request->QRData; 
    print $apps->setQRPromocionesMovistar($datos);
} else if ($request->metodo === "auditoria_cupones_movistar") {
    $datos[0] = $request->rst_id;
    $datos[1] = $request->atran_modulo;
    $datos[2] = $request->atran_descripcion;
    $datos[3] = $request->atran_accion;
    $datos[4] = $request->atran_varchar1;
    $datos[5] = $request->atran_varchar2;
    $datos[6] = $request->IDUsersPos;
    print $apps->auditoria_cupones_movistar($datos);
} else if ($request->metodo === "consumir_ws") {
    $url = $request->url_data; 
    $codFactura = $request->codFactura; 
    $codRestaurante = $request->codRestaurante; 
    $codCadena = $request->codCadena; 
    $address = $url.'?codFactura='.$codFactura.'&codCadena='.$codCadena.'&codRestaurante='.$codRestaurante;

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $address,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => 1,
        CURLOPT_CUSTOMREQUEST => 'POST', 
        CURLOPT_HTTPHEADER => array( 
            "cache-control: no-cache",
            "content-type: application/json",
            "Content-Length: 0"
        ),
    ));
    $response = curl_exec($curl);
    if (curl_error($curl)) {
        $response = ["status"=>"error comm", "message"=>curl_error($curl)];
    }
    curl_close($curl);
    print $response; 
}
// [FIN] METODOS FUNCIONALIDAD CUPONES MOVISTAR
else if ($request->metodo === "cargarMotorizados") {
    $idCadena = $request->idCadena;
    $idRestaurante = $request->idRestaurante;
    $idPeriodo = $request->idPeriodo;
    $medio = $request->medio;
    print $apps->cargarMotorizados($idCadena,$idRestaurante,$idPeriodo,$medio);
}
// Cargar lista de pedidos app
else if ($request->metodo === "cargarPedidosApp") {
    $estadoBusqueda = $request->estadoBusqueda;
    $parametroBusqueda = $request->parametroBusqueda;
    print $apps->cargarPedidos( $idCadena, $idRestaurante, $idPeriodo, $estadoBusqueda, $parametroBusqueda, $idEstacion );
} 
// Cargar lista de pedidos entregados
else if ($request->metodo === "cargarPedidosEntregados") {
    $estadoBusqueda = $request->estadoBusqueda;
    $parametroBusqueda = $request->parametroBusqueda;
    print $apps->cargarPedidosEntregados( $idCadena, $idRestaurante, $idPeriodo, $estadoBusqueda, $parametroBusqueda );
} 
else if ($request->metodo === "cargarMotorizados") {
    $idCadena = $request->idCadena;
    $idRestaurante = $request->idRestaurante;
    $idPeriodo = $request->idPeriodo;
    print $apps->cargarMotorizados($idCadena,$idRestaurante,$idPeriodo);
}
else if ($request->metodo === "asignarMotorizado") {
    $idMotorizado = $request->idMotorizado;
    $codigo_app = $request->codigo_app;
    print $apps->asignarMotorizado( $idMotorizado, $codigo_app, $idUsuario );
} 
else if ($request->metodo === "listaTransaccionesAsignadas") {
    $idCadena = $request->idCadena;
    $idRestaurante = $request->idRestaurante;
    $idMotorizado = $request->idMotorizado;
    print $apps->listaTransaccionesAsignadas($idCadena,$idRestaurante,$idMotorizado);
} 
else if ($request->metodo === "cambioTransaccionesEnCamino") {
    $idPeriodo = $request->idPeriodo;
    $idMotorizado = $request->idMotorizado;
    print $apps->cambioTransaccionesEnCamino( $idPeriodo, $idMotorizado, $idUsuario );
} 
else if ($request->metodo === "cambioTransaccionesEntregado") {
    $idMotorizado = $request->idMotorizado;
    $idPeriodo = $request->idPeriodo;
    print $apps->cambioTransaccionesEntregado( $idPeriodo, $idMotorizado, $idUsuario );
} 
else if ($request->metodo === "confirmarOrden") {
    $codigo_app = $request->codigo_app;
    $idUserPos = $request->idUserPos;
    print $apps->confirmarOrden( $idUserPos,$codigo_app, $idRestaurante);
} 
else
// Cargar lista de impresiones en error
if ( $request->metodo === "cargarImpresionesError" ) {
    print $apps->cargarImpresionesError( $idCadena, $idRestaurante, $idPeriodo );
} else 
// Reimprimir transaccion error
if ( $request->metodo === "impresionTransaccionError" ) {
    $idCanalMovimiento = $request->idCanalMovimiento;
    print $apps->impresionTransaccionError( $idCanalMovimiento );
}else 
// 
if ($request->metodo === "cambioEstadoPedido") {
    $codigo_app = $request->codigo_app;
    $estado = $request->estado;
    print $apps->cambioEstadoPedido( $codigo_app, $estado, $idPeriodo );
}else 
//Obtener la factura y nota credito con 
if($request->metodo === "facturaPorPedido"){
    $codigo_app = $request->codigo_app;
    print $apps->facturaPorPedido( $codigo_app );
}else
//Carga de Politica de Proveedor de Tracking
if($request->metodo === "cargarPoliticaProveedorTracking"){
    
  
    
    if(!isset($request->cdn_id)){
        $cdn_id = $idCadena;
    }else{
        $cdn_id = $request->cdn_id;
    }

    print $apps->cargarPoliticaProveedorTracking( $cdn_id );
}
else if ($request->metodo === "cambioEstadoBringg") {
    $idFactura  = $request->idFactura;
    $idApp      = $request->idApp;
    $url        = $request->url;
    $respuesta = $bringgApp->crearOrden($url, $idFactura, $idApp);
    print $respuesta;
}
else 
//Notificacion de Anulacion de Orden a Bringg
if ($request->metodo === "anulacionOrdenBringg") {
    $idBringg  = $request->idBringg;
    $url        = $request->url;
    $cfac_id    = $request->cfac_id;
    $respuesta = $bringgApp->anularOrden($url, $idBringg, $cfac_id);
    print $respuesta;
}

else
//Carga de Politica de Proveedor de Tracking
if($request->metodo === "cargarSemaforoConfig"){
    $cdn_id = $request->cdn_id;
    print $apps->cargarSemaforoConfig( $cdn_id );
}
else
//Carga de Politica de URL Creacion de Pedido en Bringg
if($request->metodo === "cargarURLCrearPedidoBringg"){
    $rst_id = $request->rst_id;
    print $apps->cargarURLCrearPedidoBringg( $rst_id );
}
else
//Carga de Politica de URL Anulacion Pedido en Bringg
if($request->metodo === "cargarURLAnularPedidoBringg"){
    $rst_id = $request->rst_id;
    print $apps->cargarURLAnularPedidoBringg( $rst_id );
}
else
//Carga de Politica de Cambio de Estados Automatico
if($request->metodo === "cambioEstadosAutomatico"){
    $cdn_id = $request->cdn_id != 'NO' ? $request->cdn_id : $idRestaurante;
    print $apps->cambioEstadosAutomatico( $cdn_id );
}
else
//Obtenicion de codigo externo (BRINGG) de un pedido
if($request->metodo === "obtenerCodigoExterno"){
    $codigo_app = $request->codigo_app;
    print $apps->obtenerCodigoExterno( $codigo_app );
}
else
//Obtenicion de cantidades por estado de pedidos App
if($request->metodo === "obtenerCantidadEstadosPedidosApp"){
    $idCadena = $request->idCadena;
    $idRestaurante = $request->idRestaurante;
    $idPeriodo = $request->idPeriodo;
    print $apps->obtenerCantidadEstadosPedidosApps( $idCadena, $idRestaurante, $idPeriodo );
}
else
//Desasignar motorizado
if($request->metodo === "reversarAsignacionMotorolo"){
    $idMotorizado = $request->idMotorizado;
    $codigo_app = $request->codigo_app;
    print $apps->reversarAsignacionMotorolo( $idMotorizado,$codigo_app);
}
else
//Cargar lista medios
if($request->metodo === "cargarListaMedios"){
    $idCadena = $request->idCadena;
    $idRestaurante = $idRestaurante;
    print $apps->cargarListaMedios( $idCadena, $idRestaurante );
}
else
//Completar Transaccion Pedido Agregador
if($request->metodo === "completarTransaccionAgregador"){
    $codigo_app = $request->codigo_app;
    $medio      = $request->medio;
    print $apps->completarTransaccionAgregador($codigo_app, $medio, $idUsuario, $idPeriodo);
} else
//Cargar Configuraciones Alerta App
if($request->metodo === "configuracionAlertaMedios"){
    $idCadena = $request->idCadena;
    $idRestaurante = $request->idRestaurante;
    print $apps->configuracionAlertaMedios($idCadena, $idRestaurante);
} else
// Cargar lista de impresiones en error
if ( $request->metodo === "cargarImpresionesError" ) {
    print $apps->cargarImpresionesError( $idCadena, $idRestaurante, $idPeriodo );
} else 
// Reimprimir transaccion error
if ( $request->metodo === "impresionTransaccionError" ) {
    $idCanalMovimiento = $request->idCanalMovimiento;
    print $apps->impresionTransaccionError( $idCanalMovimiento );
} else 
// Cargar Transferencias
if ( $request->metodo === "cargarPedidosTransferidos" ) {
    $estado = $request->estadoBusqueda;
    $parametro = $request->parametroBusqueda;
    print $apps->cargarPedidosTransferidos( $idCadena, $idRestaurante, $idPeriodo, $estado, $parametro );
}   else 
// Verificar si existe un usuario 
if ( $request->metodo === "verificarMotorizadoAgregador" ) {
    $periodo = $request->periodo;
    $medio = $request->medio;
    print $apps->verificarMotorizadoAgregador( $periodo, $medio );
}else 
//Obtener proveedor para delivery segun el medio
if ( $request->metodo === "obtenerNombreProveedorDeliveryPorMedio" ) {
    $medio = $request->nombreMedio;
    print $apps->obtenerNombreProveedorDeliveryPorMedio( $idCadena,$idRestaurante,$medio );
}else
//Carga de Politica de URL Creacion de Pedido en DUNA
if($request->metodo === "obtenerUrlCrearPedidoDuna"){
    $rst_id = $request->rst_id;
    print $apps->obtenerUrlCrearPedidoDuna( $rst_id );
}else 
//Carga de Politica de URL Anulacion de Pedido en DUNA
if($request->metodo === "obtenerUrlAnularPedidoDuna"){
    $rst_id = $request->rst_id;
    print $apps->obtenerUrlAnularPedidoDuna( $rst_id );
}else 
//Notificacion de Creacion de Orden a DUNA
if ($request->metodo === "crearOrdenDuna") {
    $idFactura  = $request->idFactura;
    $idApp      = $request->idApp;
    $url        = $request->url;
    $respuesta = $dunaApp->crearOrden($url, $idFactura, $idApp);
    print $respuesta;
}else//Notificacion de Creacion de Orden a DUNA motorizado
if ($request->metodo === "crearOrdenDunaMotorizado") {
    $url        = $request->url;
    $idApp      = $request->codigo;
    $respuesta = $dunaApp->crearOrdenMotorizado($url,$idApp);
    print $respuesta;
}else
//Notificacion de Anulacion de Orden a DUNA
if ($request->metodo === "anulacionOrdenDuna") {
    $idDuna  = $request->idDuna;
    $url        = $request->url;
    $cfac_id    = $request->cfac_id;
    $respuesta = $dunaApp->anularOrden($url, $idDuna, $cfac_id);
    print $respuesta;
}else 
//Obtener Configuracion CAMBIO ESTADO AUTOMATICO por Factura
if ($request->metodo === "obtenerConfiguracionCambioEstadosAutomatico") {
    $cfac_id = $request->cfac_id;
    $respuesta = $apps->obtenerConfiguracionCambioEstadosAutomatico($cfac_id);
    print $respuesta;
}else if ($request->metodo === "cambioEstadoAutomaticoSinProveedor") {
    $idFactura  = $request->idFactura;
    $idApp      = $request->idApp;
    $medio      = isset($request->medio) ? $request->medio : "Domicilio";
    $respuesta = $proveedorNoAsignado->actualizarPedido($idFactura,$idApp,$medio,$idRestaurante);
    print $respuesta;
}
if ($request->metodo == "enviarConsultaAgregadores"){
    $medio = trim($request->medio);
    $codigo_factura = $request->codigo_factura;
    //$idMotorizado = $request->idMotorizado;
    print $apps->enviarConsultaAgregadores($idCadena, $idRestaurante, $medio, $codigo_factura, 'false'); //, $idMotorizado
}
if ($request->metodo === "agregadores") {
    print $apps->agregadores();
} 
//Pickup Agregadores Motorizados QR
if ($request->metodo === "getPoliticaTiempoEsperaUltimaMilLa") {
    $descriptionCR = $request->descriptionCR;
    $descriptionCDR= $request->descriptionCDR;
    $respuesta = $apps->getPoliticaTiempoEsperaUltimaMilLa($idCadena,$idRestaurante,$descriptionCR,$descriptionCDR);
    print json_encode($respuesta);
}
elseif ($request->metodo === "getMotorizado") {
    $ccdescription = $request->ccdescription;
    $cdcdescription = $request->cdcdescription;
    $riderConfig= $apps->getRiderConfig("$ccdescription ","$cdcdescription");
    print json_encode ($apps->getMotorizado($riderConfig));
}
elseif ($request->metodo === "getConfigAnulationMotive") {
    $ccdescription = $request->ccdescription;
    $cdcdescription = $request->cdcdescription;
    $ConfigCancelMotives=$apps->getConfigCancelMotives($ccdescription,$cdcdescription);
    print json_encode ($apps->getAnulacionID($ConfigCancelMotives));
}
elseif ($request->metodo === "getDragonTailStatus") {
    print json_encode ($apps->getDragonTailStatus($idRestaurante, $idCadena, 'DRAGONTAIL CONFIGS', 'ACTIVE'));

}
elseif ($request->metodo === "getRiderAgregador") {
    $medio = $request->medio;
    print json_encode(['riderId'=>Rider::getRiderAgregador($medio)]);
}  elseif ($request->metodo === "obtenerActualizarCodigo") {
    $action = $request->action;
    print $apps->obtenerActualizarCodigo($idCadena,$idRestaurante,$action,$idPeriodo);
}
elseif ($request->metodo === "getRestaurantConfig") {
    print json_encode($apps->getRestaurantConfig($idCadena,$idRestaurante,$request->collection,$request->parameter,$request->config));
}
elseif ($request->metodo === "getPickingFlaw") {
    print json_encode($apps->getPickingFlaw($request->codApp));
}
else if ($request->metodo === "imprimirCodigo") {
    $lc_condiciones[0] = $request->codigo;
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = $idUsuario;
    $lc_condiciones[3] = $idEstacion;
    $lc_condiciones[4] = $idRestaurante;
    print $apps->imprimirCodigo($lc_condiciones);
}
else if ($request->metodo === "cargarDeliveryPedidosPendientes") {
    print $apps->cargarDeliveryPedidosPendientes( $idCadena, $idRestaurante, $idPeriodo );
}

if ($request->metodo == "validarPoliticaRestauranteMonitor"){
    print $apps->validarPoliticaRestauranteMonitor();
}
if ($request->metodo == "cargarDatosPedido"){
    $codigo_app = $request->codigo_app;
    print $apps->cargarDatosPedido($codigo_app, $idRestaurante);
}
if ($request->metodo === "ActualizacionFormaPagoOrdenesUber"){
    $codigo_app = $request->codigo_app;
    print json_encode($uberDirectCash->actualizarFormaPagoUberDirect($codigo_app));
}
if ($request->metodo === "ValidacionMontoLimiteUber"){
    print json_encode($uberDirectCash->validacionMontoLimiteUber($idCadena,$idRestaurante,$request->collection,$request->parameter,$request->codigo_app));
}
if ($request->metodo === "validarOTP"){
    print json_encode($tirillaPromocion->validacionCodigoOTP($idCadena,$idRestaurante,$request->codigo_otp));
}
if ($request->metodo === "obtenerProducto"){
    print json_encode($tirillaPromocion->obtenerProducto($idRestaurante, $request->productId, $request->odp_id, $request->cat_id,$request->menuId));
}
if ($request->metodo === "consumoOTP"){
    print json_encode($tirillaPromocion->consumoCodigoOTP($idCadena,$idRestaurante,$request->codigo_otp, $request->codigo_factura, $idUsuario));
}
if ($request->metodo === "notificarPedido"){

    try {

    $codigo = $request->codigo;
    $medio = $request->medio;

    if (isset($medio) && $medio != '') {

        if (strtolower($medio) === 'pedidosya') {
            $medio = 'peya';
        }
        $urlNotificarPedido = $servicioWebObj->retorna_WS_NotificarPedido($idRestaurante);

        if (isset($urlNotificarPedido) && isset($urlNotificarPedido["urlwebservice"])){
        
        $urlNotificarPedido = $urlNotificarPedido["urlwebservice"];
        $urlNotificarPedido = $urlNotificarPedido.strtolower($medio).'/pickup/ecuador';

        }else {
            echo json_encode([
                'status' => 0,
                'message' => 'No esta la politica configurada',
                'error' => true
            ]);
            die();
        }

    }

    if (!preg_match('/http/', $urlNotificarPedido)) {

        echo json_encode([
            'status' => 0,
            'message' => $urlNotificarPedido,
            'error' => true
        ]);
        die();
    }

    $dataSend = [
        'order_id' => $codigo,
    ];
    
    $ch = curl_init($urlNotificarPedido);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json'
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataSend));
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $response = json_decode($response, true);

    curl_close($ch);

    if ($http_status >= 200 && $http_status < 300) {
        echo json_encode([
            'status' => $http_status,
            'message' => $response,
            'error' => false
        ]);
    } else {
        echo json_encode([
            'status' => $http_status,
            'message' => $response,
            'error' => true
        ]);
    }

    // guardar auditoria

   $dataAudit =  json_encode([
            'payload' => $dataSend,
            'url' => $urlNotificarPedido,
            'response' => $response,
            'http_status' => $http_status
        ]);

    $apps->notificarPedidoAuditoria(
        $idRestaurante,
        $idUsuario,
        $dataAudit,
        $codigo
    );


} catch (Exception $e) {

    echo json_encode([
        'status' => 0,
        'message' => $e->getMessage(),
        'error' => true
    ]);

} 

}
?>