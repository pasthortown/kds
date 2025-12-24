<?php

session_start();

include("../clases/clase_payvalida.php");

$idCadena       = $_SESSION['cadenaId'];
$idRestaurante  = $_SESSION['rstId'];
$ip             = $_SESSION['direccionIp'];
$usuario        = $_SESSION['usuarioId'];


$lc_payvalida = new Payvalida($idCadena, $idRestaurante);
$request = (object) filter_input_array(INPUT_POST);

// Consulta de Saldos
if ($request->metodo === "consultaSaldo") {
    $numeroTarjeta = $request->numeroTarjeta;
    print $lc_payvalida->consultaSaldo($numeroTarjeta);
}

//Ejecucion de Cobro
if ($request->metodo === "cobrar") {
    $lc_payvalida->insertaLog("");
    $lc_payvalida->insertaLog("====================================================================================================================================");
    $lc_payvalida->insertaLog("INICIA VENTA CUPON EFECTIVO");
    $lc_payvalida->insertaLog("====================================================================================================================================");
    $lc_payvalida->insertaLog("Tipo de Venta: CUPON EFECTIVO ");
    $lc_payvalida->insertaLog("Tipo de Servicio: REST - PAYVALIDA ");
    $lc_payvalida->insertaLog("Datos de IP: " . $ip);
    $lc_payvalida->insertaLog("Monto transaccion: " . $request->monto);

    $merchant = $lc_payvalida->merchant();

    if (isset($merchant) && $merchant != 'ERROR') {

        $hashFixed = $lc_payvalida->fixedHash();

        if (isset($hashFixed) && $hashFixed != 'ERROR') {
            

            $objetoCobro = new stdClass();
            $objetoCobro->cardnumber    =  $request->numeroTarjeta;
            $objetoCobro->amount        = (float)$request->monto;
            $objetoCobro->locationid    = strval($idRestaurante);
            $objetoCobro->merchant      = $merchant;
            $objetoCobro->currency      = 'USD';

            $url = $lc_payvalida->urlCobrar();

            if (isset($url) && $url != '' && substr($url, 0, 4) === "http") {

                $requerimiento = $lc_payvalida->insertaRequerimientoAutorizacion(
                    $ip,
                    '',
                    $request->idFactura,
                    '01',
                    $request->monto,
                    $url
                );


                if (isset($requerimiento) && isset($requerimiento->errores) && sizeof($requerimiento->errores) > 0) {
                    $mensajeError = json_encode($lc_payvalida->utf8ize($requerimiento->errores), 0, 512);
                    $lc_payvalida->insertaLog("TRANSACCION NO EJECUTADA  ");
                    $lc_payvalida->insertaLog("ERROR - " . $mensajeError);
                    $arr = array('status' => 'ERROR', 'message' => $mensajeError);
                    print json_encode($arr);
                    return;
                }

                //Si se inserta en la tabla SWT_Requerimiento_Autorizacion se envia el consumo al servicio de Payvalida 
                if (isset($requerimiento) && isset($requerimiento->datos) && isset($requerimiento->datos[0])  &&   $requerimiento->datos[0]['response'] == 'SUCCESS') {

                    $objetoCobro->orderid = $requerimiento->datos[0]['idRequerimiento'] . '_' . $request->idFactura;
                    $objetoCobro->description   = $request->idFactura;
                    $checksum = hash('sha512', $objetoCobro->cardnumber . $objetoCobro->amount. $objetoCobro->merchant.$objetoCobro->locationid. $objetoCobro->orderid . $hashFixed);
                    $objetoCobro->checksum = $checksum;

                    $objetoCobroJson = $lc_payvalida->construccionJson($objetoCobro);

                    $objCobroLog = clone $objetoCobro;
                    $objCobroLog->cardnumber = $lc_payvalida->enmascarar($request->numeroTarjeta);
                    $objetoCobroLogJson = $lc_payvalida->construccionJson($objCobroLog);

                    $lc_payvalida->insertaLog("Datos de Trama de Envio: " . $objetoCobroLogJson);
                    //Actualizamos la tabla SW_Requerimiento_Autorizacion
                    $actualizacion = $lc_payvalida->actualizaRequerimientoAutorizacion($requerimiento->datos[0]['idRequerimiento'], $objetoCobroJson);
                
                    if($actualizacion == true){

                        $respuestaPayvalidaJson = $lc_payvalida->cobrar($url, $objetoCobroJson, $request->numeroTarjeta);
                        $respuestaPayvalida = json_decode($respuestaPayvalidaJson);

                        if(isset($respuestaPayvalida) && isset($respuestaPayvalida->status) && $respuestaPayvalida->status == 'ERROR'){
                            $arr = array('status' => 'ERROR', 'message' => $respuestaPayvalida->message);
                            print json_encode($arr);
                            return;
                        }


    
                        if (isset($respuestaPayvalidaJson) &&  isset($respuestaPayvalida) && isset($respuestaPayvalida->CODE) && $respuestaPayvalida->CODE == '0000') {
    
                            $lc_payvalida->insertaLog("TRANSACCION PROCESADA ");    
                            $lc_payvalida->insertaLog("Datos Trama Respuesta:  " . $respuestaPayvalidaJson);
    
                            $respuestaAutorizacion =  $lc_payvalida->insertaRespuestaAutorizacion(
                                $respuestaPayvalidaJson,
                                date('Ymd'),
                                date('h:m'),
                                $request->numeroTarjeta,
                                $request->idFactura,
                                $idRestaurante,
                                $ip,
                                $_SERVER['HTTP_HOST'],
                                $usuario,
                                $idCadena,
                                $request->monto,
                                'APROBADA',
                                $objetoCobro->orderid,
                                $respuestaPayvalida->CODE,
                                $respuestaPayvalida->DATA->status
                            );
    
    
                            if (isset($respuestaAutorizacion) &&  isset($respuestaAutorizacion->datos) && isset($respuestaAutorizacion->datos[0])  &&   $respuestaAutorizacion->datos[0]['response'] == 'SUCCESS') {
                                $lc_payvalida->insertaLog("Inserta Respuesta de la Autorizacion  ");
                                $lc_payvalida->insertaLog("Inserta Registro en Canal Movimiento  ");
                            } else {
                                $lc_payvalida->insertaLog("NO SE LOGRO insertar Respuesta de la Autorizacion  ");
                                $lc_payvalida->insertaLog("NO SE LOGRO insertar el Registro en Canal Movimiento. REIMPRIMIR el VOUCHER mediante Ultima Transaccion  ");
                            }
    
                            $lc_payvalida->insertaLog("Fin de la Trasaccion de Autorizacion  ");    
                            print json_encode($respuestaPayvalida);

                        } else if (isset($respuestaPayvalida) && isset($respuestaPayvalida->CODE) && $respuestaPayvalida->CODE != '0000') {
    
                            $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA ");
                            $lc_payvalida->insertaLog("Datos Trama Respuesta:  " . $respuestaPayvalidaJson);
    
                            $respuestaAutorizacion =  $lc_payvalida->insertaRespuestaAutorizacion(
                                $respuestaPayvalidaJson,
                                date('d-m-Y'),
                                date('h:m'),
                                $request->numeroTarjeta,
                                $request->idFactura,
                                $idRestaurante,
                                $ip,
                                $_SERVER['HTTP_HOST'],
                                $usuario,
                                $idCadena,
                                $request->monto,
                                'RECHAZADO',
                                $objetoCobro->orderid,
                                $respuestaPayvalida->CODE,
                                $respuestaPayvalida->DESC
                            );
    
                            $arr = array('status' => 'ERROR', 'message' => $respuestaPayvalida->DESC);
                            print json_encode($arr);

                        } else if (isset($respuestaPayvalida)) {
    
                            $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA  ");
                            $lc_payvalida->insertaLog("ERROR - " . $respuestaPayvalidaJson);
    
                            $respuestaAutorizacion =  $lc_payvalida->insertaRespuestaAutorizacion(
                                'ERROR ' . $respuestaPayvalidaJson,
                                date('d-m-Y'),
                                date('h:m'),
                                $request->numeroTarjeta,
                                $request->idFactura,
                                $idRestaurante,
                                $ip,
                                $_SERVER['HTTP_HOST'],
                                $usuario,
                                $idCadena,
                                $request->monto,
                                'RECHAZADO',
                                $objetoCobro->orderid,
                                '9999',
                                'ERROR'
                            );
    
                            $arr = array('status' => 'ERROR', 'message' => 'ERROR ' . $respuestaPayvalidaJson);
                            print json_encode($arr);
                            
                        } else {
    
                            $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA  ");
                            $lc_payvalida->insertaLog("ERROR - SIN RESPUESTA DEL SERVICIO PAYVALIDA");
    
                            $respuestaAutorizacion =  $lc_payvalida->insertaRespuestaAutorizacion(
                                'SIN RESPUESTA DEL SERVICIO PAYVALIDA',
                                date('d-m-Y'),
                                date('h:m'),
                                $request->numeroTarjeta,
                                $request->idFactura,
                                $idRestaurante,
                                $ip,
                                $_SERVER['HTTP_HOST'],
                                $usuario,
                                $idCadena,
                                $request->monto,
                                'RECHAZADO',
                                $objetoCobro->orderid,
                                '9999',
                                'ERROR'
                            );
    
                            $arr = array('status' => 'ERROR', 'message' => 'SIN RESPUESTA DEL SERVICIO PAYVALIDA');
                            print json_encode($arr);
                        }
    
                    }else{
                        $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA  ");
                        $lc_payvalida->insertaLog("ERROR - No se pudo actualizar el registro en la tabla [SWT_Requerimiento_Autorizacion] de Base de Datos  ");
                        $arr = array('status' => 'ERROR', 'message' => "ERROR - No se pudo actualizar el registro en la tabla [SWT_Requerimiento_Autorizacion] de Base de Datos  ");
                        print json_encode($arr);
    
                    }



                } else if (isset($requerimiento) && isset($requerimiento->datos) && isset($requerimiento->datos[0])  &&   $requerimiento->datos[0]['response'] == 'ERROR') {
                    $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA  ");
                    $lc_payvalida->insertaLog("ERROR - No se pudo insertar el registro en la tabla [SWT_Requerimiento_Autorizacion] de Base de Datos  porque :" . $requerimiento->datos[0]['message']);
                    $arr = array('status' => 'ERROR', 'message' => $requerimiento->datos[0]['message']);
                    print json_encode($arr);
                } else {
                    $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA  ");
                    $lc_payvalida->insertaLog("ERROR - No se pudo insertar el registro en la tabla [SWT_Requerimiento_Autorizacion] de Base de Datos");

                    $arr = array('status' => 'ERROR', "message" => "ERROR - No se pudo insertar el registro en la tabla [SWT_Requerimiento_Autorizacion] de Base de Datos");
                    print json_encode($arr);
                }
            } else {
                $lc_payvalida->insertaLog("TRANSACCION NO EJECUTADA  ");
                $lc_payvalida->insertaLog($url);
                $arr = array('status' => 'ERROR', 'message' => $url);
                print json_encode($arr);
                return;
            }
        }else{
            $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA");
            $lc_payvalida->insertaLog("ERROR -  No se encuentra configurara la politica PAYVALIDA FIXED_HASH en la coleccion WS CONFIGURACIONES");
    
            $arr = array('status' => 'ERROR', "message" => "ERROR -  No se encuentra configurara la politica PAYVALIDA FIXED_HASH en la coleccion WS CONFIGURACIONES");
            print json_encode($arr);    
        }
    } else {
        $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA  ");
        $lc_payvalida->insertaLog("ERROR -  No se encuentra configurara la politica PAYVALIDA MERCHANT en la coleccion WS CONFIGURACIONES");

        $arr = array('status' => 'ERROR', "message" => "ERROR -  No se encuentra configurara la politica PAYVALIDA MERCHANT en la coleccion WS CONFIGURACIONES");
        print json_encode($arr);
    }
}


//Anular
if ($request->metodo === "anular") {

    //Obtener codigoUnico (orderId)
    $orderidRequest = $lc_payvalida->obtenerCodigoUnicoTransaccionPorFormaPago($request->idFactura, $request->idFormaPago);


    if (isset($orderidRequest) && isset($orderidRequest->errores) && sizeof($orderidRequest->errores) > 0) {
        $mensajeError = json_encode($lc_payvalida->utf8ize($orderidRequest->errores), 0, 512);
        $lc_payvalida->insertaLog("TRANSACCION NO EJECUTADA  ");
        $lc_payvalida->insertaLog("ERROR - " . $mensajeError);
        $arr = array('status' => 'ERROR', 'message' => $mensajeError);
        print json_encode($arr);
        return;
    }

    if (isset($orderidRequest) && isset($orderidRequest->datos) && isset($orderidRequest->datos[0])  &&   $orderidRequest->datos[0]['response'] && $orderidRequest->datos[0]['response'] == 'SUCCESS') {
        $lc_payvalida->insertaLog("");
        $lc_payvalida->insertaLog("====================================================================================================================================");
        $lc_payvalida->insertaLog("INICIA ANULACION CUPON EFECTIVO");
        $lc_payvalida->insertaLog("====================================================================================================================================");
        $lc_payvalida->insertaLog("Tipo de Venta: CUPON EFECTIVO ");
        $lc_payvalida->insertaLog("Tipo de Servicio: REST - PAYVALIDA ");
        $lc_payvalida->insertaLog("Datos de IP: " . $ip);
    
        $merchant           = $lc_payvalida->merchant();
        $orderid            = $orderidRequest->datos[0]['codigoUnicoTransaccion'];
        $monto              = $orderidRequest->datos[0]['montoTransaccion'];
        $idFormaPagoFactura = $orderidRequest->datos[0]['idFormaPagoFactura'];
        
        if (isset($merchant) && $merchant != 'ERROR') {

            $hashFixed = $lc_payvalida->fixedHash();

            if (isset($hashFixed) && $hashFixed != 'ERROR') {

                $checksum = hash('sha512', $merchant. $orderid . $idRestaurante . $hashFixed);

                $urlBase = $lc_payvalida->urlAnular();
                $url = $urlBase . '/' . $merchant . '?orderid=' . $orderid . '&locationid=' . $idRestaurante . '&checksum=' . $checksum;

                $requerimiento = $lc_payvalida->insertaRequerimientoAnulacion($ip, $url, $request->idFactura,  $monto, $idFormaPagoFactura);
                
                if (isset($requerimiento) && isset($requerimiento->errores) && sizeof($requerimiento->errores) > 0) {
                    $mensajeError = json_encode($lc_payvalida->utf8ize($requerimiento->errores), 0, 512);
                    $lc_payvalida->insertaLog("TRANSACCION NO EJECUTADA  ");
                    $lc_payvalida->insertaLog("ERROR - " . $mensajeError);
                    $arr = array('status' => 'ERROR', 'message' => $mensajeError);
                    print json_encode($arr);
                    return;
                }
    
                //Si se inserta en la tabla SWT_Requerimiento_Autorizacion se envia el consumo al servicio de anulacion de Payvalida
                if (isset($requerimiento) && isset($requerimiento->datos) && isset($requerimiento->datos[0])  &&   $requerimiento->datos[0]['response'] == 'SUCCESS') {
    
                    $lc_payvalida->insertaLog("Numero de transaccion a anular : " . $orderid);
    
                    $respuestaPayvalidaJson = $lc_payvalida->anular($url);

                    $respuestaPayvalida = json_decode($respuestaPayvalidaJson);
    
    
                    if (isset($respuestaPayvalidaJson) &&  isset($respuestaPayvalida) ) {
                        if (isset($respuestaPayvalidaJson) &&  isset($respuestaPayvalida) && isset($respuestaPayvalida->CODE) && $respuestaPayvalida->CODE == '0000') 
                            {
                            $lc_payvalida->insertaLog("TRANSACCION PROCESADA ");
    
                            $lc_payvalida->insertaLog("Datos Trama Respuesta:  " . json_encode($respuestaPayvalida));

                            $respuestaAutorizacion =  $lc_payvalida->insertaRespuestaAutorizacion(
                                $respuestaPayvalidaJson,
                                date('Ymd'),
                                date('h:m'),
                                '',
                                $request->idFactura,
                                $idRestaurante,
                                $ip,
                                $_SERVER['HTTP_HOST'],
                                $usuario,
                                $idCadena,
                                $monto,
                                'APROBADA',
                                $orderid,
                                $respuestaPayvalida->CODE,
                                $respuestaPayvalida->DATA->status
                            );

    
                            if (isset($respuestaAutorizacion) &&  isset($respuestaAutorizacion->datos) && isset($respuestaAutorizacion->datos[0])  &&   $respuestaAutorizacion->datos[0]['response'] == 'SUCCESS') {
                                $lc_payvalida->insertaLog("Inserta Respuesta de la Autorizacion  ");
                                $lc_payvalida->insertaLog("Inserta Registro en Canal Movimiento  ");
                            } else {
                                $lc_payvalida->insertaLog("NO SE LOGRO insertar Respuesta de la Autorizacion  ");
                                $lc_payvalida->insertaLog("NO SE LOGRO insertar el Registro en Canal Movimiento. ");
                            }
    
                            $lc_payvalida->insertaLog("Fin de la Trasaccion de Autorizacion  ");    
                            print json_encode($respuestaPayvalida);

                        } else if (isset($respuestaPayvalida) && isset($respuestaPayvalida->CODE) && $respuestaPayvalida->CODE != '0000') {
    
                            $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA  ");
                            $lc_payvalida->insertaLog("ERROR - " . $respuestaPayvalidaJson);
    
                            $respuestaAutorizacion =  $lc_payvalida->insertaRespuestaAutorizacion(
                                'ERROR ' . $respuestaPayvalidaJson,
                                date('d-m-Y'),
                                date('h:m'),
                                '',
                                $request->idFactura,
                                $idRestaurante,
                                $ip,
                                $_SERVER['HTTP_HOST'],
                                $usuario,
                                $idCadena,
                                $monto,
                                'RECHAZADO',
                                $orderid,
                                '9999',
                                'ERROR'
                            );
    
                            $arr = array('status' => 'ERROR', 'message' => 'ERROR ' . $respuestaPayvalidaJson);
                            print json_encode($arr);
                        } else {
    
                            $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA  ");
                            $lc_payvalida->insertaLog("ERROR - SIN RESPUESTA DEL SERVICIO PAYVALIDA");
    
                            $respuestaAutorizacion =  $lc_payvalida->insertaRespuestaAutorizacion(
                                'SIN RESPUESTA DEL SERVICIO PAYVALIDA',
                                date('d-m-Y'),
                                date('h:m'),
                                '',
                                $request->idFactura,
                                $idRestaurante,
                                $ip,
                                $_SERVER['HTTP_HOST'],
                                $usuario,
                                $idCadena,
                                $monto,
                                'RECHAZADO',
                                $orderid,
                                '9999',
                                'ERROR'
                            );
    
                            $arr = array('status' => 'ERROR', 'message' => 'SIN RESPUESTA DEL SERVICIO PAYVALIDA');
                            print json_encode($arr);
                        }
                    } else if(isset($respuestaPayvalidaJson)){
                        $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA  ");
                        $lc_payvalida->insertaLog("ERROR - ".$respuestaPayvalidaJson);

                        $respuestaAutorizacion =  $lc_payvalida->insertaRespuestaAutorizacion(
                            'ERROR - '.$respuestaPayvalidaJson,
                            date('d-m-Y'),
                            date('h:m'),
                            '',
                            $request->idFactura,
                            $idRestaurante,
                            $ip,
                            $_SERVER['HTTP_HOST'],
                            $usuario,
                            $idCadena,
                            $monto,
                            'RECHAZADO',
                            $orderid,
                            '9999',
                            'ERROR'
                        );

                        $arr = array('status' => 'ERROR', 'message' => $respuestaPayvalidaJson);
                        print json_encode($arr);
                    }                     
                    else {
                        $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA  ");
                        $lc_payvalida->insertaLog("ERROR - SIN RESPUESTA DEL SERVICIO PAYVALIDA");

                        $respuestaAutorizacion =  $lc_payvalida->insertaRespuestaAutorizacion(
                            'SIN RESPUESTA DEL SERVICIO PAYVALIDA',
                            date('d-m-Y'),
                            date('h:m'),
                            '',
                            $request->idFactura,
                            $idRestaurante,
                            $ip,
                            $_SERVER['HTTP_HOST'],
                            $usuario,
                            $idCadena,
                            $monto,
                            'RECHAZADO',
                            $orderid,
                            '9999',
                            'ERROR'
                        );

                        $arr = array('status' => 'ERROR', 'message' => 'SIN RESPUESTA DEL SERVICIO PAYVALIDA');
                        print json_encode($arr);
                    }
                } else if (isset($requerimiento) && isset($requerimiento->datos) && isset($requerimiento->datos[0])  &&   $requerimiento->datos[0]['response'] == 'ERROR') {
    
                    $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA ");
                    $lc_payvalida->insertaLog("ERROR - " . $requerimiento->datos[0]['message']);
    
                    $arr = array('status' => 'ERROR', 'message' => $requerimiento->datos[0]['message']);
                    print json_encode($arr);
                } else {
    
                    $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA ");
                    $lc_payvalida->insertaLog("No se pudo insertar el requerimiento de anulacion ");
    
    
                    $arr = array('status' => 'ERROR', 'message' => 'No se pudo insertar el requerimiento de anulacion');
                    print json_encode($arr);
                }


            }else{
                $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA");
                $lc_payvalida->insertaLog("ERROR -  No se encuentra configurara la politica PAYVALIDA FIXED_HASH en la coleccion WS CONFIGURACIONES");
        
                $arr = array('status' => 'ERROR', "message" => "ERROR -  No se encuentra configurara la politica PAYVALIDA FIXED_HASH en la coleccion WS CONFIGURACIONES");
                print json_encode($arr);    
            }

                
        } else {
            $lc_payvalida->insertaLog("TRANSACCION NO PROCESADA  ");
            $lc_payvalida->insertaLog("ERROR -  No se encuentra configurara la politica PAYVALIDA MERCHANT en la coleccion WS CONFIGURACIONES");
    
            $arr = array('status' => 'ERROR', "message" => "ERROR -  No se encuentra configurara la politica PAYVALIDA MERCHANT en la coleccion WS CONFIGURACIONES");
            print json_encode($arr);
        }
    


    }

}
