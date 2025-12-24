<?php
session_start();

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_servicioReImpresion.php";

$request = json_decode(file_get_contents("php://input"));
if (isset($request->session)) {
    // Only for ./clases/clase_appCadena.php method requestClientWsServicePrint
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
    if($request->tipo != 'test_impresion'){
        $idRestaurante = $_SESSION['rstId'];
        $idCadena = $_SESSION['cadenaId'];
        $idEstacion = $request->estacion;;
        $idPeriodo = $_SESSION['IDPeriodo'];
        $idUsuario = $_SESSION['usuarioId'];
        $ipEstacion = $_SESSION['direccionIp'];
        $idControlEstacion = $_SESSION['IDControlEstacion'];
        $tipoServicioTienda = $_SESSION['TipoServicio'];
        $lc_usuarioIdAdmin = isset($_SESSION['usuarioIdAdmin']) ? $_SESSION['usuarioIdAdmin'] : null;
        // Politicas Sericio Api Impresion
        $servicioApiImpresion = json_decode($_SESSION['servicioApiImpresion']);
    }else{
        $datosAdicionales=$request->datosAdicionales;
        $ipEstacion=$datosAdicionales['estacion'];
        $idEstacion=$datosAdicionales['IDEstacion'];       
        $servicioApiImpresion = json_decode($datosAdicionales['servicioImpresion']);
        $idUsuario=''; 
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

$servicio = new ServicioReImpresion();

if ($request->metodo == "apiServicioImpresion") {
    try {
        $tipoDocumento = $request->tipo;
        $transaccion = $request->transaccion;
        $idCabeceraOrdenPedido = $request->idCabeceraOrdenPedido;
        $datosAdicionales = $request->datosAdicionales;
        $nombreImpresora = $request->impresora;

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
        /* Impresion de documentos */
        if ($tipoDocumento == 'factura') {
            $parametros = $servicio->impresionFactura($transaccion, $idEstacion, $nombreImpresora);
            $parametroOpcional = $transaccion;
        }else if($tipoDocumento == 'nota_credito') {
            $idEstacion = $idEstacion;
            $parametroOpcional = $transaccion;
            $parametros = $servicio->impresionNotaCredito($idRestaurante, $idEstacion, $transaccion, $nombreImpresora);
        }else if ($tipoDocumento == 'orden_pedido') {
            $parametros = $servicio->impresionOrdenPedido($transaccion, $idEstacion, $nombreImpresora);

            if (count($parametros) == 0) {
                $result["error"] = false;
                $result["mensaje"] = "No existen ordenes pendientes por imprimir.";

                print json_encode($result);

                die();
            }
        }

        $contador = 1;
        $cantidad=count($parametros);

        for ($i = 0; $i < $cantidad; $i++) {
            $idMarcaImpresora = $servicio->descripcionTipoImpresora( isset( $parametros[$i]["impresora"] ) ? $parametros[$i]["impresora"] : '' );

            $dataPost = array(
                "idImpresora" => $parametros[$i]["impresora"]
                , "idMarca" => $idMarcaImpresora
                , "idPlantilla" => ($parametros[$i]["formatoXML"])
                , "data" => json_decode($parametros[$i]["jsonData"], true)
                , "registros" => json_decode($parametros[$i]["jsonRegistros"], true)
            );

            $dataFormatedBackSlashes = str_replace("\\",'\\\\',json_encode($dataPost['data']));
            $arrayDataFormatedBackSlashes = explode("\\n",$dataFormatedBackSlashes);
            $arrayDataFormatedBackSlashes = array_map(function($v) { return ltrim($v); }, $arrayDataFormatedBackSlashes);
            $dataFormatedBackSlashes = implode("\\n", $arrayDataFormatedBackSlashes);
            $dataPost['data'] = json_decode($dataFormatedBackSlashes,true);

            $dataString = stripslashes(json_encode($dataPost, JSON_UNESCAPED_UNICODE));
            for ($x = 0; $x < $parametros[$i]["numeroImpresiones"]; $x++) {
                
                if(isset($parametros[$i]["impresora"])) {
                    $urlApiImpresion = $servicio->obtenerIpImpresora($parametros[$i]["impresora"]);
                    if($urlApiImpresion['urlApiImpresion'] != '') {
                        $curl = $urlApiImpresion['urlApiImpresion'].$ruta;
                    } 
                }
                curlRetry($curl, $dataString, $timeout, $reintentos, $servicio, count($parametros), $contador, $parametros[$i]["tipo"], $parametros[$i]["impresora"], $idEstacion, $idUsuario,$parametroOpcional,$imp_error, $codigo_app, $codigo_factura, $tipoDocumento);
            }

            $contador = $contador + 1;
        }
    } catch (\Throwable $th) {
        print json_encode($th);
    }    
}

function curlRetry($curl, $payload, $timeout, $reintentos, $servicio, $cantidadRegistros, $contador, $tipoDocumento, $impresora, $idEstacion, $idUsuario, $opcional = null,$imp_error, $codigo_app, $codigo_factura, $tipoEntrega) {
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

    $result = array(
        "error" => false,
        "mensaje" => ""
    );

    $intento = 1;

    for ($i = 0; $i < $reintentos; $i++) { 
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($tipoDocumento=='orden_pedido'){
            print ($response);
            return;
        }

        if (in_array($status_code, array(200, 404))) {
            $servicio->auditoriaApiImpresion($tipoDocumento, 'SUCCESS', $impresora, $idEstacion, $idUsuario, $curl, $payload, $response, $opcional, $codigo_app, $codigo_factura, $tipoEntrega);

            if ($cantidadRegistros == $contador) {
                print ($response);
                break;
            }

            break;
        } else {
            if ($intento == $reintentos) {
                $result["error"] = true;
                $result["mensaje"] = "Ha ocurrido un error al momento de imprimir, por favor comuniquese con soporte!!";

                $servicio->auditoriaApiImpresion($tipoDocumento, 'ERROR', $impresora, $idEstacion, $idUsuario, $curl, $payload, $response, $opcional, $codigo_app, $codigo_factura, $tipoEntrega);
                if($imp_error == 0){
                    print json_encode($result);
                }

                break;
            }
        }

        $intento = $intento + 1;
    }

    curl_close($ch);
}


function isJson($string) {
    return ((is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))))) ? true : false;
}