<?php
session_start();
set_time_limit(60);


include '../system/conexion/clase_sql.php';
include '../clases/clase_webservice.php';
include '../clases/clase_recargaIngreso.php';
include '../resources/module/fidelizacion/RecargaWS.php';


//Datos entrada
$request = (object)filter_input_array(INPUT_POST);

$idCadena = $_SESSION['cadenaId'];
$idTienda = $_SESSION['rstCodigoTienda'];
$idRestaurante = $_SESSION['rstId'];
$idEstacion = $_SESSION['estacionId'];
$idControlEstacion = $_SESSION['IDControlEstacion'];
$idUsuario = $_SESSION['usuarioId'];

$clienteDocumento = $_SESSION['fb_document'];
$clienteEstado = $_SESSION['fb_status'];
$clintePuntos = $_SESSION['fb_points'];
$cliente = $_SESSION['fdznNombres'];
$clienteCodigoSeguridad = isset($_SESSION['fb_security']) ? $_SESSION['fb_security'] : "";
$clienteNombre = $_SESSION['fdznNombres'];
$clienteDireccion = $_SESSION['fdznDireccion'];

$app = 'jvz';
if (!empty($_SESSION['appid'])) {
    $app = $_SESSION['appid'];
}

//var_dump($_SESSION["ContrasenaWebServicesFidelizacion"]);
$servicioWebURL = new webservice();
$recarga = new Recargas();

$clienteTipoDocumento = (strlen($clienteDocumento) > 10) ? "RUC" : "CI";

if ($request->metodo === 'recargarEfectivoCliente') {
    $accion = 'RECARGA EFECTIVO';
    $tipoMov = 'Recargas';

    //Valor a recargar
    $valor = $request->valor;

    if ($clienteEstado == "REGISTERED" || $clienteEstado == "BLOCKED") { //Revisar estado BLOCKED para recargas
        if ($valor >= 1) {
            //Obtener tipo documento
            $clienteTipoDocumento = "CI";
            if (strlen($clienteDocumento) > 10) {
                $clienteTipoDocumento = "RUC";
            }

            $result = array(
                "estado" => -1,
                "mensaje" => "Error, servicio no disponible.",
                "valorRecarga" => "",
                "totalRecargado" => "");

            $totalIntentosRecargas = 4;
            for ($i = 0; $i < $totalIntentosRecargas; $i++) {
                //Codigo Secuencial
                $res = $recarga->validarAutorizacionRecargas($idRestaurante, $idUsuario, $tipoMov);
                $recargaWS = new RecargaWS($idCadena, $idRestaurante, $app, $idTienda, $res['cashierDocument'], $res['cashierName']);
                $proceso = $res['proceso'];
                $codigoIngreso = $res['secuencia'];                
                $body = $recargaWS->realizarRecargaBody($codigoIngreso, $valor, $clienteTipoDocumento, $clienteDocumento, $clienteNombre);
                if (true ||$proceso == 'Iniciado') {
                    // var_dump("Bearer ".$_SESSION["ContrasenaWebServicesFidelizacion"]);
                    $respuesta = $recargaWS->realizarRecarga($body, $_SESSION['claveConexion']);
                } else if ($proceso == 'En Proceso') {
                    sleep(10);
                    $respuesta = $recargaWS->consultarEstadoRecarga($codigoIngreso, $_SESSION["claveConexion"]);
                }
                //Si la repuesta es correcta
                if ($respuesta->httpStatus == 200) {
                    $respuesta->data = json_decode($respuesta->data);
                    $data = $respuesta->data;
                    $dataRespuesta = $data->data;
                    $codigo = $data->code;          // $codigo = $respuesta->httpStatus;
                    $mensaje = utf8_decode($data->message);  //$mensaje = "Recarga exitosa...";
                    $recargaEfectivo = $dataRespuesta->balanceByTransaction;
                    $totalRecargado = $dataRespuesta->balanceByCustomer;
                    $puntos = $dataRespuesta->pointsByTransaction;
                    $totalPuntos = $dataRespuesta->pointsByCustomer;
                    //Actualizar Variables de Sesion
                    $_SESSION['fb_points'] = $totalPuntos;
                    $_SESSION['fb_money'] = $totalRecargado;
                    $recargaPromocional = 0;
                    $result = $recarga->registroIngresoRecarga(
                        $codigoIngreso,
                        $codigo,
                        $mensaje,
                        $clienteDocumento,
                        $recargaEfectivo,
                        $totalRecargado,
                        $recargaPromocional,
                        $puntos,
                        $totalPuntos,
                        $idCadena,
                        $idRestaurante,
                        $idTienda,
                        $idEstacion,
                        $idControlEstacion,
                        $idUsuario,
                        $cliente);
                    $respuesta->request = $body;
                    

                    $recarga->logProcesosRecargas($mensaje, $accion, $idRestaurante, $idCadena, $idUsuario, json_encode($respuesta));
                    $recarga->finalizarAutorizacionRecargas($idRestaurante, $codigoIngreso, $tipoMov);

                    $result['estado'] = 1;
                    break;
                } else if ( !(isset($_SESSION['canje_v2']) && !empty($_SESSION['canje_v2']) && trim($_SESSION['canje_v2'])==1) && $respuesta->numberError == 28) {
                    //realiza recarga
                    $recarga->logProcesosRecargas("Timeout: intento " . ($i + 1) . " de " . $totalIntentosRecargas, $accion, $idRestaurante, $idCadena, $idUsuario, json_encode($respuesta));
                    if (($i + 1) == $totalIntentosRecargas) {
                        $recarga->pendienteAutorizacionRecargas($idRestaurante, $codigoIngreso, $tipoMov);
                    }
                } else {
                    $warning = "";
                    $error = "";

                    if (is_string($respuesta->data)) {
                        $respuesta->data = json_decode($respuesta->data);
                        
                        $data = $respuesta->data;
                        if (property_exists($data,'warning') && count($data->warning) > 0 && property_exists($data->warning[0], 'value')) {
                            $warning = $data->warning[0]->value;
                        }
                        if (property_exists($data,'error') && count($data->error) > 0 && property_exists($data->error[0],'value')) {
                            $error = $data->error[0]->value;
                        }

                    }
                    if ($warning === "El campo code enviado no existe.") {
                    //realiza recarga
                        $recarga->logProcesosRecargas("Timeout: intento " . ($i + 1) . " de " . $totalIntentosRecargas, $accion, $idRestaurante, $idCadena, $idUsuario, json_encode($respuesta));
                        if (($i + 1) == $totalIntentosRecargas) {
                            $recarga->pendienteAutorizacionRecargas($idRestaurante, $codigoIngreso, $tipoMov);
                        }
                    } else {
                        //Error Guardar Log
                    //realiza recarga
                        $recarga->logProcesosRecargas("Error, servicio no disponible.", $accion, $idRestaurante, $idCadena, $idUsuario, json_encode($respuesta));
                        $recarga->finalizarAutorizacionRecargas($idRestaurante, $codigoIngreso, $tipoMov);
                        break;
                    }
                }
            }
        } else {
            $result["estado"] = 3;
            $result["mensaje"] = "No cumple con el valor m&iacute;nimo";
        }

        //Si el cliente aún no ha terminado su registro en la app
    } else {
        $result["estado"] = 2;
        $result["mensaje"] = "El cliente debe terminar su registro en la url: amigosjuanvaldez.kfc.com.ec o descargandose la APP para Android o iOS.";
    }

    print json_encode($result);

//Iniciar proceso de consumo de recarga
} else if ($request->metodo === 'consumoRecargarEfectivoCliente') {
    $accion = 'CONSUMO DE RECARGA';
    $tipoMov = 'Consumo Recargas';
    $valor = $request->valor;
    $idFactura = $request->idFactura;
    $totalFactura = $request->totalFactura;
    //Respuesta
    $result = array(
        'estado' => 0,
        'mensaje' => '',
        'valor' => $valor);

    $totalIntentosConsumo = 4;
    $respuesta ='';
    for ($i = 0; $i < $totalIntentosConsumo; $i++) {
        //Codigo Secuencial
        $res = $recarga->validarAutorizacionRecargas($idRestaurante, $idUsuario, $tipoMov);
        $recargaWS = new RecargaWS($idCadena, $idRestaurante, $app, $idTienda, $res["cashierDocument"], $res["cashierName"]);
        $proceso = $res['proceso'];
        $secuencialRecarga = $res['secuencia'];
        $body = $recargaWS->consumirRecargaBody($secuencialRecarga, $idFactura, $clienteCodigoSeguridad, $valor, $clienteTipoDocumento, $clienteDocumento, $clienteNombre);
        if ($proceso == 'Iniciado') {
            $respuesta = $recargaWS->consumirRecarga($body, $_SESSION['claveConexion']);
        } else if ($proceso == 'En Proceso') {
            sleep(10);
            if(isset($_SESSION['canje_v2']) && !empty($_SESSION['canje_v2']) && trim($_SESSION['canje_v2'])==1){
                $respuesta = $recargaWS->consumirRecarga($body, $_SESSION['claveConexion']);
            }
            else{
                $respuesta = $recargaWS->consultarEstadoConsumoRecarga($secuencialRecarga, $_SESSION["claveConexion"]);
            }
            
        }
        //Si la repuesta es correcta
        if ($respuesta->httpStatus == 200) {
            $respuesta->data = json_decode($respuesta->data);
            $data = $respuesta->data;
            $dataRespuesta = $data->data;
            $mensaje = $data->message;
            $result = $recarga->registroConsumoRecarga($idFactura,
                $idUsuario,
                $totalFactura,
                $valor,
                $secuencialRecarga,
                $cliente,
                $clienteDocumento,
                $dataRespuesta->pointsByCustomer,
                $dataRespuesta->balanceByCustomer,
                $data->code,
                $mensaje,
                $idCadena,
                $idRestaurante,
                $idEstacion);

            $result['mensaje'] = $mensaje;
            $result['estado'] = 1;
            $recarga->logProcesosRecargas($mensaje, $accion, $idRestaurante, $idCadena, $idUsuario, json_encode($respuesta));
            $recarga->finalizarAutorizacionRecargas($idRestaurante, $secuencialRecarga, $tipoMov);
            break;
        } else if ($respuesta->numberError == 28) {
            //consume recarga
            $recarga->logProcesosRecargas('Timeout: intento ' . ($i + 1) . ' de ' . $totalIntentosConsumo, $accion, $idRestaurante, $idCadena, $idUsuario, json_encode($respuesta));
            if (($i + 1) == $totalIntentosConsumo) {
                //Entrar al proceso de reverso
                // var_dump("Bearer ".$_SESSION["ContrasenaWebServicesFidelizacion"]);
                $respuestaReverso = realizarReverso($idFactura, $idCadena, $idUsuario, $idRestaurante, $idEstacion, $cliente, $clienteDocumento, $recarga, $recargaWS, $_SESSION["claveConexion"]);
                $result['estado'] = -1;
                if ($respuestaReverso->code == 200) {
                    $recarga->finalizarAutorizacionRecargas($idRestaurante, $secuencialRecarga, $tipoMov);
                    $result['mensaje'] = 'Servicio no disponible. Por favor intentar nuevamente.';
                } else {
                    $recarga->pendienteAutorizacionRecargas($idRestaurante, $codigoIngreso, $tipoMov);
                    $result['mensaje'] = 'Servicio no disponible.';
                }
            }
        } else {
            $warning = '';
            $error = '';
            if (is_string($respuesta->data)) {
                $respuesta->data = json_decode($respuesta->data);
                $data = $respuesta->data;
                if (property_exists($data, 'warning') && count($data->warning) > 0 && property_exists($data->warning[0], 'value')) {
                    $warning = $data->warning[0]->value;
                }
                if (property_exists($data, 'error') && count($data->error) > 0 && property_exists($data->error[0], 'value')) {
                    $error = $data->error[0]->value;
                }
            }
            if ($warning === 'El campo code enviado no existe.') {
                $recarga->logProcesosRecargas('Timeout: intento ' . ($i + 1) . ' de ' . $totalIntentosConsumo, $accion, $idRestaurante, $idCadena, $idUsuario, json_encode($respuesta));
                if (($i + 1) == $totalIntentosConsumo) {
                    //Entrar al proceso de reverso
                    $respuestaReverso = realizarReverso($idFactura, $idCadena, $idUsuario, $idRestaurante, $idEstacion, $cliente, $clienteDocumento, $recarga, $recargaWS, $_SESSION['claveConexion']);
                    $result['estado'] = -1;
                    if ($respuestaReverso->code == 200) {
                        $recarga->finalizarAutorizacionRecargas($idRestaurante, $secuencialRecarga, $tipoMov);
                        $result['mensaje'] = 'Servicio no disponible. Por favor intentar nuevamente.';
                    } else {
                        $recarga->pendienteAutorizacionRecargas($idRestaurante, $codigoIngreso, $tipoMov);
                        $result['mensaje'] = 'Servicio no disponible.';
                    }
                }
            } else {
                $logData = new \stdClass();
                $logData->request = $body;
                if ($respuesta->httpStatus == 422) {
                    if ($error === 'token inválido') {
                        $result['mensaje'] = 'Código de seguridad no válido. Solicite al cliente que cierre la aplicación de Juan Valdez y vuelva a abrirla para obtener un nuevo código.';
                        $result['estado'] = 203; //Estado para mostrar modal de lectura de código de seguridad y mostrar mensaje
                    } else {
                        $result['mensaje'] = $respuesta->data->error; //'Código de seguridad requerido.';
                        $result['estado'] = 201; //Estado para mostrar modal de lectura de código de seguridad
                    }
                    $logData->response = $respuesta;
                } else if ($respuesta->httpStatus == 500) {
                    $result['mensaje'] = 'Servicio no disponible.';
                    $result['estado'] = -1;
                    $logData->response = new \stdClass();
                    $logData->response->message = $result['mensaje'];
                    $logData->response->status = $respuesta->httpStatus;
                } else {
                    $result['mensaje'] = 'Servicio no disponible.';
                    $result['estado'] = -1;
                    $logData->response = new \stdClass();
                    $logData->response = $respuesta;
                    $logData->response->httpStatus = $respuesta->httpStatus;
                }

                $recarga->logProcesosRecargas($result['mensaje'], $accion, $idRestaurante, $idCadena, $idUsuario, json_encode($logData));
                $recarga->finalizarAutorizacionRecargas($idRestaurante, $secuencialRecarga, $tipoMov);
                break;
            }
        }
    }

    print json_encode($result);

//Reverso de un consumo con recarga
} else if ($request->metodo === "reversoConsumoRecargarCliente") {
    $recargaWS = new RecargaWS($idCadena, $idRestaurante, $idTienda, "", "");
    // var_dump("Bearer ".$_SESSION["ContrasenaWebServicesFidelizacion"]);
    print json_encode(realizarReverso($request->idFactura, $idCadena, $idUsuario, $idRestaurante, $idEstacion, $cliente, $clienteDocumento, $recarga, $recargaWS, $_SESSION["claveConexion"]));

} else if ($request->metodo === "reversoConsumoPickup") {  // pickup
    $recargaWS = new RecargaWS($idCadena, $idRestaurante, $app, $idTienda, "", "");
    // var_dump("Bearer ".$_SESSION["ContrasenaWebServicesFidelizacion"]);
    print json_encode(realizarReversoPickup($request->idFactura, $idCadena, $idUsuario, $idRestaurante, $idEstacion, $cliente, $clienteDocumento, $recarga, $recargaWS, $_SESSION["claveConexion"]));

}

function realizarReverso($idFactura, $idCadena, $idUsuario, $idRestaurante, $idEstacion, $cliente, $clienteDocumento, $recarga, $recargaWS, $claveConexion)
{
    //Recorrer recargas ingresadas y activas
    $consumosRecarga = $recarga->cargarConsumosRecargaPorFactura($idCadena, $idFactura);

    $result = array(
        "estado" => 0,
        "mensaje" => "");

    $body = $recargaWS->reversarRecargaBody($consumosRecarga[0]["secuencialConsumo"]);
    //print_r(json_encode($body));
    $respuesta = $recargaWS->reversarRecarga($body, $claveConexion);
    $respuesta->data = json_decode($respuesta->data);
    //print_r(json_encode($respuesta));
    $data = $respuesta->data;
    //print_r(json_encode($data));
    $warning = "";
    if (property_exists($data, "warning") && count($data->warning) > 0 && property_exists($data->warning[0], "value")) {
        $warning = $data->warning[0]->value;
    }

    //Si la repuesta es correcta
    if ($respuesta->httpStatus == 200
        || ($respuesta->httpStatus == 422 && $warning == "Ya existe una transacción con el code enviado")
    ) {
        $mensaje = $data->message;

        //Anular forma de pago, registro de auditoría se realiza dentro del SP
        $recarga->cancelarFormaPagoConsumoRecarga($consumosRecarga[0]["idFormaPagoFactura"],
            $idFactura,
            $idUsuario,
            $idRestaurante,
            $idCadena,
            $idEstacion,
            $respuesta->httpStatus,
            $mensaje,
            $consumosRecarga[0]["secuencialConsumo"],
            $cliente,
            $clienteDocumento);

        $result["mensaje"] = $mensaje;
        $result["estado"] = 1;
    } else {

        $data->message = $data->message == "User has not enough balance to reverse the transaction" ? "Usuario no cuenta con suficiente saldo para realizar la transacción" : $data->message;
        $mensaje = $data->message;
        $logData = new \stdClass();
        $logData->request = $body;
        $logData->response = $respuesta;
        $result["estado"] = -1;
        $result["mensaje"] = $data->message; // se enviaba vacio
        $descripcion = $result["mensaje"];
        $accion = "REVERSO CONSUMO DE RECARGA";
        //consume recarga cancelada
        $recarga->logProcesosRecargas($descripcion, $accion, $idRestaurante, $idCadena, $idUsuario, json_encode($logData));
    }

    return $result;
}

function realizarReversoPickup($idFactura, $idCadena, $idUsuario, $idRestaurante, $idEstacion, $cliente, $clienteDocumento, $recarga, $recargaWS, $claveConexion)
{
    //Recorrer recargas ingresadas y activas
    $consumosRecarga = $recarga->cargarConsumosPickupPorFactura($idCadena, $idFactura);

    $result = array(
        "estado" => 0,
        "mensaje" => "");

    $body = $recargaWS->reversarPickupBody($consumosRecarga[0]["secuencialConsumo"]);
    //print_r(json_encode($body));
    $respuesta = $recargaWS->reversarPickup($body, $claveConexion);
    $respuesta->data = json_decode($respuesta->data);
    //print_r(json_encode($respuesta));
    $data = $respuesta->data;
    //print_r(json_encode($data));
    $warning = "";
    if (property_exists($data, "warning") && count($data->warning) > 0 && property_exists($data->warning[0], "value")) {
        $warning = $data->warning[0]->value;
    }

    //Si la repuesta es correcta
    if ($respuesta->httpStatus == 200 || ($respuesta->httpStatus == 422 && $warning == "Ya existe una transacción con el code enviado")) {
        $mensaje = $data->message;

        //Anular forma de pago, registro de auditoría se realiza dentro del SP de clase_recargaIngreso.php -135
        //$recarga->cancelarFormaPagoConsumoRecarga($consumosRecarga[0]["idFormaPagoFactura"],
        $recarga->cancelarFormaPagoPickup($consumosRecarga[0]["idFormaPagoFactura"],
            $idFactura,
            $idUsuario,
            $idRestaurante,
            $idCadena,
            $idEstacion,
            $respuesta->httpStatus,
            $mensaje,
            $consumosRecarga[0]["secuencialConsumo"],
            $cliente,
            $clienteDocumento);

        $result["mensaje"] = $mensaje;
        $result["estado"] = 1;
    } else {
        $mensaje = $data->message;
        $logData = new \stdClass();
        $logData->request = $body;
        $logData->response = $respuesta;
        $result["estado"] = -1;
        $result["mensaje"] = "";//$mensaje;//"Error: "+  $data->message;
        $descripcion = $result["mensaje"];
        //$accion = "REVERSO CONSUMO DE RECARGA"; SP de clase_recargaIngreso.php -135
        $accion = "REVERSO PICKUP";
        $recarga->logProcesosRecargas($descripcion, $accion, $idRestaurante, $idCadena, $idUsuario, json_encode($logData));
    }

    return $result;
}