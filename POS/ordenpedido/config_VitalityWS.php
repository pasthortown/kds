<?php

session_start();

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_creditos.php";

$idRestaurante = $_SESSION['rstId'];
$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];

$vitality = new Creditos($idRestaurante);
$vitality->setIdCadena($idCadena);
$auditoriasC = new AuditoriaCreditos();

function specialChars($a)
{
    return htmlspecialchars($a, ENT_QUOTES, 'UTF-8');
}

$request = (object)(array_map('specialChars', $_POST));

if ($request->metodo === "cargarTokenSeguridadVitality") {
    $respuesta = $vitality->AutenticarTokenSeguridadVitality($idCadena, $idUsuario);
    $resp = new stdClass();
    $resp = json_decode($respuesta->data);

    if (empty($resp->access_token)) {
        $resultado["access_token"] = "";
        if (!empty($resp->message)) {
            $resultado["mensajes"] = $resp->message;
        } else {
            $resultado["mensajes"] = 'Servicio no disponible, intentelo luego de unos minutos.';
        }
    } else {
        $resultado["token_type"] = $resp->token_type;
        $resultado["expires_in"] = $resp->expires_in;
        $resultado["access_token"] = $resp->access_token;
    }
    print json_encode($resultado);

} else if ($request->metodo === "ValidarVoucherVitality") {
    $codigoVitality = $request->codigoQRVitality;
    $tokenSeguridadV = $request->TokenSeguridadVitality;
    $respuesta = $vitality->ValidarVoucherVitality($codigoVitality, $tokenSeguridadV, $idUsuario);
    $resp = new stdClass();
    $resp = json_decode($respuesta->data);

    if (property_exists($resp, 'message') == true) {
        $resultado["errores"] = $resp->message;
        print json_encode($resultado);
    } else {
        $resp->errores = "";
        $_SESSION['vitality'] = 1;
        $_SESSION['codigoQRVitality'] = $request->codigoQRVitality;
        $_SESSION['tokenSeguridadVitality'] = $request->TokenSeguridadVitality;
        $_SESSION['balanceVitality'] = $resp->data->currentBalance;
        $_SESSION['documentNumber'] = $resp->data->documentNumber;
        $_SESSION['legalNameV'] = $resp->data->legalName;
        $_SESSION['addressV'] = $resp->data->address;
        $_SESSION['phoneNumberV'] = $resp->data->phoneNumber;
        print json_encode($resp);
    }

} else if ($request->metodo === "obtenerIdClienteExterno") {
    $idCiudad = $request->idCiudad;
    $nombre = $request->nombre;
    $apellido = $request->apellido;
    $tipoDocumento = $request->tipoDocumento;
    $documento = $request->documento;
    $telefono = $request->telefono;
    $direccion = $request->direccion;
    $email = $request->email;
    $tipoCliente = $request->tipoCliente;
    $respuesta = $vitality->obtenerIdClienteExterno($idCiudad, $nombre, $apellido, $tipoDocumento, $documento, $telefono, $direccion, $email, $tipoCliente, $idUsuario);
    $_SESSION['idClienteVitality'] = $respuesta["idCliente"];
    print json_encode($respuesta);

} else if ($request->metodo === "VoucherTransaccionesVitality") {
    $accion = "CANJEAR CUPON";
    $tipoMo = 'Vitality';
    $modulo = 'VITALITY';
    $codigoVitality = $request->codigoQRVitality;
    $tokenSeguridadV = $request->TokenSeguridadVitality;
    $jsonVitality = $_POST["JSON_VoucherVitality"];
    $codigoFactura = $request->codigoFactura;
    $totalReintentos = 3;
    for ($i = 0; $i < $totalReintentos; $i++) {
        $res = $auditoriasC->validarAutorizacionVitality($idRestaurante, $idUsuario, $tipoMo);
        $proceso = $res["proceso"];
        $codigoIngreso = $res["secuencia"];
        $respuesta= ''; // revisar 

        if ($proceso == "Iniciado") {
            $respuesta = $vitality->voucherTransaccion($codigoVitality, $tokenSeguridadV, $jsonVitality, $idUsuario);
        } else if ($proceso == "En Proceso") {
            sleep(5);
            $respuesta = $vitality->consultarEstadoTransaccionVitality($codigoFactura, $codigoVitality, $tokenSeguridadV, $idUsuario);
            //$respuesta = $vitality->voucherTransaccion($codigoVitality, $tokenSeguridadV, $jsonVitality, $idUsuario);
        }

        if ($respuesta->httpStatus == 200) {
            $_SESSION['vitality'] = 0;
            $_SESSION['idClienteVitality'] = null;
            $_SESSION['codigoQRVitality'] = null;
            $_SESSION['tokenSeguridadVitality'] = null;
            $_SESSION['balanceVitality'] = null;
            $_SESSION['documentNumber'] = null;
            $_SESSION['legalNameV'] = null;
            $_SESSION['addressV'] = null;
            $_SESSION['phoneNumberV'] = null;
            //cierra el estado de Autorizacion
            // $auditoriasC->guardarLogCreditos('Transaccion Redencion Codigo Voucher :' . $codigoVitality, $accion, $modulo, $idRestaurante, $idCadena, $idUsuario, json_encode($respuesta));
            $auditoriasC->finalizarAutorizacionVitality($idRestaurante, $codigoIngreso, $tipoMo);
            $vitality->consultarEstadoTransaccionVitality($codigoFactura, $codigoVitality, $tokenSeguridadV, $idUsuario);
            break;
        } else if ($respuesta->httpStatus == 28) {
            $auditoriasC->guardarLogCreditos("Timeout: intento " . ($i + 1) . " de " . $totalReintentos, $accion, $modulo, $idRestaurante, $idCadena, $idUsuario, json_encode($respuesta));
            if (($i + 1) == $totalReintentos) {
                $auditoriasC->finalizarAutorizacionVitality($idRestaurante, $codigoIngreso, $tipoMo);
            }
        } else {
            $errorS = "";
            $errorR = "";
            if (is_string($respuesta->data)) {
                $respuesta->data = json_decode($respuesta->data);
                $data = $respuesta->data;
                $status = isset($respuesta->data->status) ? ($respuesta->data->status) : "error";
                if ($status == 'error' or $respuesta->httpStatus == 404) {
                    $errorR = isset($respuesta->data->error) ? ($respuesta->data->error) : "";
                    $errorS = $status;
                }
            }
            if ($errorS !== null) {
                $auditoriasC->guardarLogCreditos("Timeout: intento " . ($i + 1) . " de " . $totalReintentos, $accion, $modulo, $idRestaurante, $idCadena, $idUsuario, json_encode($respuesta));
                if (($i + 1) == $totalReintentos) {
                    $auditoriasC->finalizarAutorizacionVitality($idRestaurante, $codigoIngreso, $tipoMo);
                }
            } else {
                //Error Guardar Log
                $auditoriasC->guardarLogCreditos("Error, servicio no disponible.", $accion, $modulo, $idRestaurante, $idCadena, $idUsuario, json_encode($respuesta));
                $auditoriasC->finalizarAutorizacionVitality($idRestaurante, $codigoIngreso, $tipoMo);
                break;
            }
        }
    }

    print json_encode($respuesta);
}
