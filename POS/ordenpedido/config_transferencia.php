<?php

@session_start();

include("../system/conexion/clase_sql.php");
include("../clases/app.Transferencia.php");
include("../resources/module/domicilio/transferencia/TransferenciaPedido.php");
include("../resources/module/domicilio/App.php");
include("../resources/module/domicilio/Multimarca.php");
include("../resources/module/domicilio/Web.php");

// Data
$idCadena           = $_SESSION['cadenaId'];
$idRestaurante      = $_SESSION['rstId'];
$codigoRestaurante  = $_SESSION['rstCodigoTienda'];
$restaurante        = $_SESSION['rstNombre'];
$idEstacion         = $_SESSION['estacionId'];
$ip                 = $_SESSION['direccionIp'];
$idUsuario          = $_SESSION['usuarioId'];
$usuario            = $_SESSION['usuario'];
$idControlEstacion  = $_SESSION['IDControlEstacion'];
$idPeriodo          = $_SESSION['IDPeriodo'];

// REQUEST
$request = (object) filter_input_array(INPUT_POST);

// Transferir Pedido
if ($request->metodo === "transferirPedido") {

    $idDestino      = $request->idLocal;
    $codigoDestino  = $request->codigoLocal;
    $destino        = $request->local;
    $idMotivo       = $request->idMotivo;
    $motivo         = $request->motivo;
    $codigo         = $request->codigo;
    $idOrigen       = $idRestaurante;
    $codigoOrigen   = $codigoRestaurante;
    $origen         = $restaurante;
    $medio          = $request->medio;
    $direccion      = "";


    $transferencia = new TransferenciaPedido( $idCadena, $idOrigen, $codigoOrigen, $origen, $codigo );
    $respuesta = $transferencia->transferir_pedido( $idDestino, $codigoDestino, $destino, $idUsuario, $usuario, $idMotivo, $motivo, $medio, $idEstacion );

    if(isset($respuesta) && isset($respuesta->codigo)  && $respuesta->codigo == 1){
        if($medio == 'App'){
            $app = new App( $idCadena, $idRestaurante );
            $notificacion = $app->notificarTransferencia( $codigo, $codigoOrigen, $codigoDestino, $usuario, $motivo, $direccion, $medio );
            print_r(var_dump($notificacion));
        }else if($medio == 'Multimarca' || $medio == 'Multimarca Efectivo'){
            $multimarca = new Multimarca( $idCadena, $idRestaurante );
            $notificacion = $multimarca->notificarTransferencia( $codigo, $codigoOrigen, $codigoDestino, $usuario, $motivo, $direccion, $medio );
            print_r(var_dump($notificacion));
            print get_object_vars($notificacion);
        }else if(stristr($medio,'Web')){
            $web = new Web( $idCadena, $idRestaurante );
            $notificacion = $web->notificarTransferencia($codigo,$codigoOrigen,$codigoDestino,$usuario,$motivo,$direccion,$medio);
            print_r(var_dump($notificacion));
        }
        else{
            print "Medio no registrado para cambios de estado";
        }
    }

    // Respuesta
    print json_encode($respuesta);

} else
// Cargar Locales Transferencia
if ($request->metodo === "cargarLocales") {
    $transferencia = new Transferencia();
    print $transferencia->cargarLocales( $idCadena, $idRestaurante );
} else
// Cargar Locales Transferencia
if ($request->metodo === "cargarMotivos") {
    $transferencia = new Transferencia();
    print $transferencia->cargarMotivos( $idCadena, $idRestaurante );
}
else
// Cargar Locales Transferencia
if ($request->metodo === "cargarMotivosPickup") {
    $transferencia = new Transferencia();
    print $transferencia->cargarMotivosPickup( $idCadena );
} else
// Cargar Locales Transferencia
if ($request->metodo === "mostrarBotonTransferenciaPickup") {
    $transferencia = new Transferencia();
    print $transferencia->mostrarBotonTransferenciaPickup( $idCadena, $idRestaurante);
}
else 
//Cargar Locales Transferencia
if ($request->metodo === "cargarLocalesPickup") {
    $transferencia = new Transferencia();
    print ($transferencia->cargarLocalesPickup($idCadena,$idRestaurante,$idUsuario));
} else 
//Transferir pedido pickup


if ($request->metodo === "transferirPedidoPickup") {

    $idDestino      = $request->idLocal;
    $codigoDestino  = $request->codigoLocal;
    $destino        = $request->local;
    $idMotivo       = $request->idMotivo;
    $motivo         = $request->motivo;
    $codigo         = $request->codigo;
    $idOrigen       = $idRestaurante;
    $codigoOrigen   = $codigoRestaurante;
    $origen         = $restaurante;
    $forma_pago     = $request->forma_pago;
    $direccion      = "";


    $transferencia = new TransferenciaPedido( $idCadena, $idOrigen, $codigoOrigen, $origen, $codigo );
    $respuesta = $transferencia->transferir_pedido_pickup( $idDestino, $codigoDestino, $destino, $idUsuario, $usuario, $idMotivo, $motivo, $forma_pago, $idEstacion );
    $auxRespuesta = json_decode($respuesta);
    //var_dump($respuesta);
    if(isset($auxRespuesta) && isset($auxRespuesta->codigo)  && $auxRespuesta->codigo == 1){
        /*$res = array("idDestino" => $idDestino,
                    "codigo" => $codigo,
                    "codigoDestino" => $codigoDestino,
                    "codigoORigen" => $codigoOrigen,
                    "destino" => $destino,
                    "idUsuario" => $idUsuario,
                    "usuario" => $usuario,
                    "idMotivo" => $idMotivo,
                    "motivo" => $motivo,
                    "forma_pago" => $forma_pago,
                    "idEstacion" => $idEstacion
                    );*/
                    //var_dump($res);
        //notificar a trade
        $multimarca = new Multimarca( $idCadena, $idRestaurante );
        $notificacion = $multimarca->notificarTransferenciaPickup( $codigo, $codigoOrigen, $codigoDestino, $usuario, $motivo, $direccion, $idUsuario);
        }
        print ($respuesta);
}

    // Respuesta
   

?>