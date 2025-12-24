<?php
@session_start();

include("../system/conexion/clase_sql.php");
include("../resources/module/domicilio/Utilitario.php");
include("../resources/module/domicilio/App.php");
include("../resources/module/domicilio/Multimarca.php");
include("../resources/module/domicilio/Web.php");
include("../resources/module/domicilio/Callcenter.php");

$idCadena = $_SESSION['cadenaId'];
$idRestaurante = $_SESSION['rstId'];
$timeOutDefault = 3;


// REQUEST
$request = (object) filter_input_array(INPUT_POST);

if ($request->metodo === "cambioEstadoTrade") {
    $codigo_app = $request->codigo_app;
    $estado = $request->estado;
    $medio = strtoupper($request->medio);
    $timeOut = isset($request->timeOut) ? $request->timeOut : $timeOutDefault;

    switch($medio){
        case "APP":
        case "APP ACUMULAPTS":
        case "APP CANJEPTS":
            $appComunicacion = new App( $idCadena, $idRestaurante, $timeOut );
            $respuesta = $appComunicacion->cambioEstado( $codigo_app, $estado);
            print $respuesta;
            break;
        case "MULTIMARCA":
        case "MULTIMARCA EFECTIVO":
        case "MULTIMARCA ACUMULAPTS":
        case "MULTIMARCA CANJEPTS":
        case "MULTIMARCA EFECTIVO ACUMULAPTS":
        case "MULTIMARCA EFECTIVO CANJEPTS":
            $multimarcaComunicacion = new Multimarca( $idCadena, $idRestaurante, $timeOut );
            $respuesta = $multimarcaComunicacion->cambioEstado( $codigo_app, $estado);
            print $respuesta;
            break;
        case "WEB-E":
        case "WEB-E ACUMULAPTS":
        case "WEB-E CANJEPTS":
            $utilitarioHomologacion = new Utilitario();
            $estado = $utilitarioHomologacion->obtenerEstadoHomologado($estado);
            $webComunicacion = new Web( $idCadena, $idRestaurante, $timeOut );
            $respuesta = $webComunicacion->cambioEstado( $codigo_app, $estado);
            print $respuesta;
            break;
        case "CALL CENTER REGIONAL":
        case "CALL CENTER REGIONAL ACUMULAPTS":
        case "CALL CENTER REGIONAL CANJEPTS":
            $callcenterComunicacion = new Callcenter( $idCadena, $idRestaurante, $timeOut );
            $respuesta = $callcenterComunicacion->cambioEstado( $codigo_app, $estado);
            print $respuesta;
            break;
    }
}


if ($request->metodo === "trackingTrade") {
    $objetoTracking = $request->objetoTracking;
    $medio = $request->medio;
    $appComunicacion = new App( $idCadena, $idRestaurante );
    $respuesta = $appComunicacion->tracking($objetoTracking, $medio);
    print $respuesta;
}


if ($request->metodo === "cambioEstadoTradePorFactura") {
    $cfac_id = $request->cfac_id;
    $estado = $request->estado;
    $utilitarioConsulta = new Utilitario();
    $medio = $utilitarioConsulta->retornarMedio($cfac_id);
    $medio = strtoupper($medio["Cabecera_FacturaVarchar2"]);

    switch ($medio){
        case "APP ACUMULAPTS":
        case "APP CANJEPTS":
        case "APP":
            $appComunicacion = new App( $idCadena, $idRestaurante );
            $respuesta = $appComunicacion->cambioEstadoTradePorFactura( $idCadena, $cfac_id, $estado);
            print $respuesta;
            break;
        case "MULTIMARCA":
        case "MULTIMARCA ACUMULAPTS":
        case "MULTIMARCA CANJEPTS":
        case "MULTIMARCA EFECTIVO":
        case "MULTIMARCA EFECTIVO ACUMULAPTS":
        case "MULTIMARCA EFECTIVO CANJEPTS":
            $multimarcaComunicacion = new Multimarca( $idCadena, $idRestaurante );
            $respuesta = $multimarcaComunicacion->cambioEstadoTradePorFactura( $idCadena, $cfac_id, $estado);
            print $respuesta;
            break;
        case "WEB-E":
        case "WEB-E ACUMULAPTS":
        case "WEB-E CANJEPTS":
            $webComunicacion = new Web( $idCadena, $idRestaurante );
            $utilitarioHomologacion = new Utilitario();
            $estado = $utilitarioHomologacion->obtenerEstadoHomologado($estado);
            //$estado = $estado["estado"];
            $respuesta = $webComunicacion->cambioEstadoTradePorFactura( $idCadena, $cfac_id, $estado);
            print $respuesta;
            break;
        case "CALL CENTER REGIONAL":
        case "CALL CENTER REGIONAL ACUMULAPTS":
        case "CALL CENTER REGIONAL CANJEPTS":
            $callcenterComunicacion = new Callcenter( $idCadena, $idRestaurante );
            $respuesta = $callcenterComunicacion->cambioEstadoTradePorFactura( $idCadena, $cfac_id, $estado);
            print $respuesta;
            break;
        default:
            print "El medio no Aplica";
            break;

    }
}

if($request->metodo === "validarServicioTercero"){
    $medio      = $request->medio;
    $servicio   = $request->servicio;
    $appComunicacion = new App( $idCadena, $idRestaurante );
    $respuesta  = $appComunicacion->validacionConsumoServicio($medio, $servicio);
    print json_encode($respuesta);
}

?>