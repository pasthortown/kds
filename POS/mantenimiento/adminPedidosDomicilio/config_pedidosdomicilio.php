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

// CARGA LISTA DE MOTORIZADOS ACTIVOS PARA ASIGNACION
if ( $request->metodo === "cargarMotorizadosActivosAPI" ) {
    $url = $request->url;
    $parametro = $request->parametro;
    print json_encode($domicilio->cargarMotorizadosActivosAPI($url, $parametro));
} else

// CARGA LISTA DE PEDIDOS
if ($request->metodo === "cargarPedidosEntregados") {
    $estadoBusqueda = $request->estadoBusqueda;
    $parametroBusqueda = $request->parametroBusqueda;
    $idPeriodo = $request->idPeriodo;
    print json_encode($domicilio->cargarPedidosEntregados( $idCadena, $idRestaurante, $idPeriodo, $estadoBusqueda, $parametroBusqueda ));
} else 

if ($request->metodo === "cargarMotorizados") {
    $idPeriodo = $request->idPeriodo;
    print json_encode($domicilio->cargarMotorizados($idCadena,$idRestaurante,$idPeriodo));
} else

if ($request->metodo === "asignarMotorizado") {
    $idMotorizado = $request->idMotorizado;
    $codigo_app = $request->codigo_app;
    print json_encode($domicilio->asignarMotorizado( $idMotorizado, $codigo_app, $idUsuario ));
} 
if ($request->metodo === "agregadores") {
    print json_encode($domicilio->agregadores());
} 