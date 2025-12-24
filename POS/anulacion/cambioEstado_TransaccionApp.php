<?php

    @session_start();

    include("../resources/module/domicilio/Utilitario.php");
    include("../resources/module/anulacion/App.php");
    include("../resources/module/anulacion/Multimarca.php");
    include("../resources/module/anulacion/Web.php");
    include("../resources/module/anulacion/Callcenter.php");
    include("../resources/module/anulacion/Llamada.php");
    include("../resources/module/anulacion/Tictuk.php");
    include_once "../clases{$ds}app.Cadena.php";

    // Data
    $idCadena       = $_SESSION['cadenaId'];
    $idRestaurante  = $_SESSION['rstId'];
    $idEstacion     = $_SESSION['estacionId'];
    $ip             = $_SESSION['direccionIp'];

    $request = (object) filter_input_array(INPUT_POST);


    if ($request->metodo === "generarNotaCreditoApp") {
        $idFactura = $request->idFactura;
        $idMotivoAnulacion = $request->idMotivoAnulacion;
        $observacion = $request->observacion;
        $idUsuario = $request->idUsuario;
        $cedula=$request->cedula;
        $utilitarioConsulta = new Utilitario();
        $medio = $utilitarioConsulta->retornarMedio($idFactura);
        $medio = strtoupper($medio["Cabecera_FacturaVarchar2"]);
        
        switch ($medio){
            case "APP ACUMULAPTS":
            case "APP CANJEPTS":
            case "APP":
                $appAnulacion = new App( $idCadena, $idRestaurante );

                $respuesta = $appAnulacion->anular( $idFactura, "Anulada", $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion,$cedula );


                if (validarPedidoDuna($idFactura)) {
                    $appAnulacion->anularPedidoDuna( $idFactura, $estado = 'anulado', $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion );
                } 


                print $respuesta;
                break;

            case "MULTIMARCA":
            case "MULTIMARCA ACUMULAPTS":
            case "MULTIMARCA CANJEPTS":
            case "MULTIMARCA EFECTIVO":
            case "MULTIMARCA EFECTIVO ACUMULAPTS":
            case "MULTIMARCA EFECTIVO CANJEPTS":
                $multimarcaAnulacion = new Multimarca( $idCadena, $idRestaurante );

                $respuesta = $multimarcaAnulacion->anular( $idFactura, "Anulada", $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion,$cedula );

                if (validarPedidoDuna($idFactura)) {
                    $multimarcaAnulacion->anularPedidoDuna( $idFactura, $estado = 'anulado', $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion );

                } 

                print $respuesta;
                break;

            case "WEB-E":
            case "WEB-E ACUMULAPTS":
            case "WEB-E CANJEPTS":

                $utilitarioHomologacion = new Utilitario();
                $estado = $utilitarioHomologacion->obtenerEstadoHomologado("Anulada");
                $estado = $estado["estado"];
                $webAnulacion = new Web( $idCadena, $idRestaurante );
                $respuesta = $webAnulacion->anular( $idFactura, $estado, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion,$cedula);
                print $respuesta;
                break;

            case "CALL CENTER REGIONAL":
            case "CALL CENTER REGIONAL ACUMULAPTS":
            case "CALL CENTER REGIONAL CANJEPTS":
                $callcenterAnulacion = new Callcenter( $idCadena, $idRestaurante );

                $respuesta = $callcenterAnulacion->anular( $idFactura, "Anulada", $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion,$cedula );

                if (validarPedidoDuna($idFactura)) {
                    $callcenterAnulacion->anularPedidoDuna( $idFactura, $estado = 'anulado', $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion );
                } 
                print $respuesta;
                break;

            case "TICTUK":
            case "TICTUK ACUMULAPTS":
            case "TICTUK CANJEPTS":
                $tictukAnulacion = new Tictuk( $idCadena, $idRestaurante );

                $respuesta = $tictukAnulacion->anular( $idFactura, "Anulada", $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion,$cedula );

                if (validarPedidoDuna($idFactura)) {
                    $tictukAnulacion->anularPedidoDuna( $idFactura, $estado = 'anulado', $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion );
                } 
                print $respuesta;
                break;
                
            case "LLAMADA":
            case "LLAMADA ACUMULAPTS":
            case "LLAMADA CANJEPTS":
                $llamadaAnulacion = new Llamada( $idCadena, $idRestaurante );

                $respuesta = $llamadaAnulacion->anular( $idFactura, "Anulada", $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion,$cedula );

                if (validarPedidoDuna($idFactura)) {
                    $llamadaAnulacion->anularPedidoDuna( $idFactura, $estado = 'anulado', $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion );
                } 
                print $respuesta;
                break;

            default:

                $respuesta = anular( $idFactura, $idRestaurante, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion,$cedula );
                print $respuesta;
                break;
        }
    }

    function anular( $idFactura, $idRestaurante, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion,$cedula ) {

        $data = new stdClass;
        $config = new Cadena();
        $anulacion = $config->generarNotaCredito($idRestaurante, $idFactura, $idUsuario, $idEstacion, $idMotivoAnulacion, $observacion,$cedula );
        $data->idFactura = $anulacion["idFactura"];
        $data->idAnulacion = $anulacion["idNotaCredito"];
        $data->servidorUrlApi   = $anulacion["servidorUrlApi"];
        $data->idEstacion       = $idEstacion;
        $data->codigo = 200;
        $data->mensaje = "Factura anulada correctamente.";

        return json_encode( $data );

    }

    function validarPedidoDuna($cfac_id)
    {
        $helperValidarFactura = new Utilitario();
        $datosCambioEstadoFactura = $helperValidarFactura->consultarFacturaValidaDuna($cfac_id);
        if (array_key_exists("cambio_estado_automatico",$datosCambioEstadoFactura) && $datosCambioEstadoFactura["cambio_estado_automatico"] === 'SI' && $datosCambioEstadoFactura["nombre_proveedor"] === 'DUNA') {
           return true;
        }

        return false;
    }
