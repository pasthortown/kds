<?php 

@session_start();

include("../../system/conexion/clase_sql.php");
include("../../clases/clase_pedido.php");


// Objects
$pedido = new Pedido();
// Data
$idCadena          = $_SESSION['cadenaId'];
$idRestaurante     = $_SESSION['rstId'];
$idEstacion        = $_SESSION['estacionId'];
$ip                = $_SESSION['direccionIp'];
$idUsuario         = $_SESSION['usuarioId'];
$idControlEstacion = $_SESSION['IDControlEstacion'];
$idPeriodo         = $_SESSION['IDPeriodo'];







// REQUEST
$request = (object) filter_input_array(INPUT_POST);

/////////////////////////////////////////////////
/////////////////       PICKUP     //////////////
/////////////////////////////////////////////////
if($request->metodo == 'aplicaPickup'){
    print $pedido->aplicaPickup($idCadena, $idRestaurante);
}else
// BUSCAR PEDIDOS
if ($request->metodo === "buscar") {
    date_default_timezone_set('America/Guayaquil');
    $hoy            = new DateTime();
    $hoyFormato     = date_format($hoy, 'Y-d-m');
    $pagina         = $request->pagina;
    $tamanio        = $request->tamanio;
    $ingresado      = $request->ingresado;
    $preparando     = $request->preparando;
    $listo          = $request->listo;
    $entregado      = $request->entregado;
    $transferido    = $request->transferido;
    $nombreCliente  = $request->nombreCliente;
    $identificacion = $request->identificacion;
    $codigoApp      = $request->codigoApp;
    
    print $pedido->listaLazyPickup($pagina, $tamanio, $hoyFormato.' 00:00:00', $hoyFormato.' 23:59:59', $ingresado, $preparando, $listo, $entregado, $transferido, $nombreCliente, $identificacion, $codigoApp);
} else 
//buscar por cfac_id
if ($request->metodo === "buscarPorCfacId") {
    date_default_timezone_set('America/Guayaquil');
    $hoy            = new DateTime();
    $hoyFormato     = date_format($hoy, 'Y-d-m');
    $pagina         = 0;
    $tamanio        = 10;
    $ingresado      = true;
    $preparando     = true;
    $listo          = true;
    $entregado      = true;
    $transferido    = true;
    $nombreCliente  = "";
    $identificacion = "";
    $codigoApp      = "";
    $cfacId         = $request->cfac_id;
    
    print $pedido->listaLazyPickupPorCfacId($pagina, $tamanio, $hoyFormato.' 00:00:00', $hoyFormato.' 23:59:59', $ingresado, $preparando, $listo, $entregado, $transferido, $nombreCliente, $identificacion, $codigoApp, $cfacId);
}
else
//DETALLE PEDIDO SELECCIONADO
if($request->metodo === "detalle"){
    $kioskoCabeceraId   = $request->kioskoCabeceraId;
    $estadoPedido       = $request->estadoPedido;
    print $pedido->detallePedidoPickup($kioskoCabeceraId, $estadoPedido);
}else
//DETALLE IMPRESIONES PEDIDO SELECCIONADO
if($request->metodo === "detalleImpresiones"){
    $kioskoCabeceraId   = $request->kioskoCabeceraId;
    print $pedido->detalleImpresionesPickup($kioskoCabeceraId);
} 
//INFORMACION DE PRODUCTOS DE  PEDIDO SELECCIONADO
if($request->metodo === "informacionProductosPedido"){
    $kioskoCabeceraId   = $request->kioskoCabeceraId;
    print $pedido->informacionProductosPedidoPickup($kioskoCabeceraId);
} 
//INFORMACION DE CANTIDAD POR ESTADO
if($request->metodo === "cantidadPorEstado"){
    date_default_timezone_set('America/Guayaquil');
    $hoy            = new DateTime();
    $hoyFormato     = date_format($hoy, 'Y-d-m');
    print $pedido->cantidadPorEstadoPickup($hoyFormato.' 00:00:00', $hoyFormato.' 23:59:59');
} 
//DETALLE FACTURA POR PEDIDO PICKUP
if($request->metodo === "detalleFacturaPickup"){
    $idFactura   = $request->idFactura;
    print $pedido->detalleFacturaPickup($idFactura);
} //REIMPRIMIR
if($request->metodo === "reimprimir"){
    $idCanalMovimiento   = $request->idCanalMovimiento;
    print $pedido->reimprimir($idCanalMovimiento);
} 


//////////////////////////////////////////////////////////
/////           METODOS API CENTRAL          /////////////
//////////////////////////////////////////////////////////

if($request->metodo === "buscarCentral"){
    date_default_timezone_set('America/Guayaquil');
    $hoy            = new DateTime();
//    $hoy->modify('-2 days');
    $hoyFormato     = date_format($hoy, 'Y-m-d');
    // $ingresado      = $request->ingresado;
    // $preparando     = $request->preparando;
    // $listo          = $request->listo;
    // $entregado      = $request->entregado;
    $documento =  isset($request->identificacion) ? $request->identificacion : '';
    $codigoApp = isset($request->codigoApp) ? $request->codigoApp : '';
    $url =  isset($request->url) ? $request->url : '' ;
    print $pedido->listaApiCentral($idRestaurante, $documento, $codigoApp, $hoyFormato, $hoyFormato, $url);
} 

//INFORMACION DE PRODUCTOS DE  PEDIDO SELECCIONADO MEDIANTE UN JSON 
if($request->metodo === "informacionProductosPedidoJson"){
    $jsonDetalles   = $request->jsonDetalles;
    print $pedido->informacionProductosPedidoPickupJson(json_encode($jsonDetalles));
} 

//CARGA DE LA SECCION DE FACTURA DE ACUERDO AL CODIGO DE APP
if($request->metodo === "cargarInformacionFacturaCentral"){
    $codigoApp   = $request->codigoApp;
    $estado   = $request->estado;
    print $pedido->cargarInformacionFacturaCentral($codigoApp, $estado);
} 

//CARGA DE LA SECCION DE DETALLE DE IMPRESION DE ACUERDO AL CODIGO DE APP
if($request->metodo === "detalleImpresionesCentral"){
    $codigoApp   = $request->codigoApp;
    print $pedido->detalleImpresionesCentral($codigoApp);
} 


//CARGA DE LA SECCION DE DETALLE DE IMPRESION DE ACUERDO AL CODIGO DE APP
if($request->metodo === "urlServidorCentral"){
    print  $pedido->urlServidorCentral();
} 




?>