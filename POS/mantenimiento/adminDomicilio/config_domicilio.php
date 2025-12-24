<?php

@session_start();

include("../../system/conexion/clase_sql.php");
include("../../clases/clase_adminDomicilio.php");

// Objects
$domicilio = new Domicilio();

// Data
$idRestaurante     = $_SESSION['rstId'];
$idCadena          = $_SESSION['cadenaId'];
$idUsuario         = $_SESSION['usuarioId'];

// REQUEST
$request = ( object ) filter_input_array( INPUT_POST );

// CARGAR PERIODO ABIERTO
if ( $request->metodo === "cargarPeriodoAbierto" ) {
    print json_encode($domicilio->cargarPeriodoAbierto( $idCadena, $idRestaurante ));
} else 
// CARGAR MOTORIZADOS ASIGNADOS
if ( $request->metodo === "cargarMotorizadosAsignadosPeriodo" ) {
    $idPeriodo = $request->idPeriodo;
    print json_encode( $domicilio->cargarMotorizadosAsignadosPeriodo( $idCadena, $idRestaurante, $idPeriodo ) );
} else 
// CARGAR TRANSACCIONES MOTORIZADO
if ( $request->metodo === "cargarTransaccionesPorMotorizado" ) {
    $idMotorizado = $request->idMotorizado;
    print json_encode( $domicilio->cargarTransaccionesAsignadasPorMotorizado( $idCadena, $idRestaurante, $idMotorizado ) );
} else 
// FINALIZAR TURNO MOTORIZADO
if ( $request->metodo === "finalizarTurnoMotorizado" ) {
    $idMotorizado = $request->idMotorizado;
    $idPeriodo = $request->idPeriodo;
    print json_encode( $domicilio->finalizarTurnoMotorizado( $idCadena, $idRestaurante, $idPeriodo, $idUsuario, $idMotorizado ) );
} else 
// ASIGNAR TURNO MOTORIZADO
if ( $request->metodo === "asignarTurnoMotorizado" ) {
    $idMotorizado = $request->idMotorizado;
    $idPeriodo = $request->idPeriodo;
    print json_encode( $domicilio->asignarTurnoMotorizado( $idCadena, $idRestaurante, $idPeriodo, $idUsuario, $idMotorizado ) );
} else 
// CARGAR MOTORIZADO ACTIVOS
if ( $request->metodo === "cargarMotorizadosActivos" ) {
    $idPeriodo = $request->idPeriodo;
    print json_encode( $domicilio->cargarMotorizadosActivos( $idPeriodo ) );
} else 
// CARGAR MOTORIZADO ACTIVOS
if ( $request->metodo === "cargarTurnosMotorizado" ) {
    $idPeriodo = $request->idPeriodo;
    print json_encode( $domicilio->cargarTurnosMotorizado( $idPeriodo ) );
} else 
// ACTUALIZAR TRANSACCION A ESTADO EN CAMINO
if ( $request->metodo === "cambiarEstadoPedidoAEnCamino" ) {
    $idPeriodo = $request->idPeriodo;
    $idMotorizado = $request->idMotorizado;
    print json_encode( $domicilio->cambiarEstadoPedidoAEnCamino( $idPeriodo, $idMotorizado, $idUsuario ) );
} else 
// ACTUALIZAR TRANSACCION A ESTADO ENTREGADO
if ( $request->metodo === "cambiarEstadoPedidoAEntregado" ) {
    $idPeriodo = $request->idPeriodo;
    $idMotorizado = $request->idMotorizado;
    print json_encode( $domicilio->cambiarEstadoPedidoAEntregado( $idPeriodo, $idMotorizado, $idUsuario ) );
}
else
// CARGA POLITICA DE URL API MOTORIZADO
if ( $request->metodo === "cargarUrlApiMotorizados" ) {
    print json_encode($domicilio->cargarUrlApiMotorizados($idRestaurante));
}
else
// CARGA LISTA DE MOTORIZADOS ACTIVOS PARA ASIGNACION
if ( $request->metodo === "cargarMotorizadosActivosAPI" ) {
    $url = $request->url;
    $parametro = $request->parametro;
    print json_encode($domicilio->cargarMotorizadosActivosAPI($url, $parametro));
}
else 
// NOTIFICACION DE TRANSACCIONES DE MOTORIZADO AL SISTEMA GERENTE
if ( $request->metodo === "notificacionMotorizadoGerente" ) {
    $url    = $request->url;
    $idPeriodo = $request->idPeriodo;
    $idMotorizado = $request->idMotorizado;
    print json_encode($domicilio->notificacionMotorizadoGerente($url, $idPeriodo, $idMotorizado));
}
else
// CARGA POLITICA DE URL API NOTIFICACION GERENTE MOTORIZADO
if ( $request->metodo === "cargarUrlApiMotorizadosGerente" ) {
    print json_encode($domicilio->cargarUrlApiMotorizadosGerente($idRestaurante));
}
else 
// IMPRIMIR FIN DE TURNO MOTORIZADO
if ( $request->metodo === "imprimirFinTurnoMotorizado" ) {
    $idPeriodo = $request->idPeriodo;
    $idMotorizado = $request->idMotorizado;
    print json_encode($domicilio->imprimirFinTurnoMotorizado($idPeriodo, $idMotorizado, $idRestaurante, $idUsuario ));
}
else 
// CARGAR CONFIGURACION DOMICILIO
if ( $request->metodo === "cargarConfiguracionDomicilio" ) {
    $cdn_id = $idCadena;
	$rst_id = $idRestaurante;
    $usr_id = $idUsuario;    
    print json_encode($domicilio->cargarConfiguracionDomicilio($cdn_id, $rst_id, $usr_id));
}


