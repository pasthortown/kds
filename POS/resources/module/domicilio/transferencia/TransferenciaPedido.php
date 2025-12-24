<?php

/////////////////////////////////////////////////////////////
//// DESARROLLADO POR: Aldo Navarrete L.                  ///
//// DESCRIPCION: Creacion de Orden desde MaxPoint        ///
//// FECHA CREACION: 06/08/2020                           ///
//// FECHA ULTIMA MODIFICACION:                           ///
//// USUARIO QUE MODIFICO:                                ///
//// DECRIPCION ULTIMO CAMBIO:                            ///
/////////////////////////////////////////////////////////////

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . "..") . $ds;

include_once "{$base_dir}{$ds}../../../system{$ds}conexion{$ds}clase_sql.php";
include_once "{$base_dir}{$ds}../../../clases/app.Transferencia.php";
include_once "{$base_dir}{$ds}../../../clases/app.Cadena.php";


class TransferenciaPedido extends sql {

    public $transferencia;
    public $idCadena;
    public $idRestaurante;
    public $codigoRestaurante;
    public $restaurante;
    public $codigo;
    
    function __construct( $idCadena, $idRestaurante, $codigoRestaurante, $restaurante, $codigo ) {
        $this->transferencia = new Transferencia();
        $this->cadena = new Cadena();
        $this->idCadena = $idCadena;
        $this->idRestaurante = $idRestaurante;
        $this->codigoRestaurante = $codigoRestaurante;
        $this->restaurante = $restaurante;
        $this->codigo = $codigo;
    }

    function transferir_pedido( $idLocal, $codigoLocal, $local, $idUsuario, $usuario, $idMotivo, $motivo, $medio, $idEstacion ) {

        try {

            $error = new \stdClass();

            // Consultar URL Servicio
            $urlServicio = $this->transferencia->cargarURLServicioTransferencia( $this->idRestaurante );
            //print_r( $urlServicio ); exit();

            //Obtener Origen -> Estos datos se envian
            $request_transferencia = new \stdClass();
            $request_transferencia->idLocal = $this->idRestaurante;
            $request_transferencia->codigoLocal = $this->codigoRestaurante;
            $request_transferencia->local = $this->restaurante;
            $request_transferencia->idUsuario = $idUsuario;
            $request_transferencia->usuario = $usuario;
            $request_transferencia->idMotivo = $idMotivo;
            $request_transferencia->motivo = $motivo;
            $request_transferencia->mensaje = $motivo;

            //Obtener Destino -> Estos datos se guardan en la bd local
            $request_destino = new \stdClass();
            $request_destino->idLocal = $idLocal;
            $request_destino->codigoLocal = $codigoLocal;
            $request_destino->local = $local;
            $request_destino->idUsuario = $idUsuario;
            $request_destino->usuario = $usuario;
            $request_destino->idMotivo = $idMotivo;
            $request_destino->motivo = $motivo;
            $request_destino->mensaje = $motivo;
            
            //Consultar Partes del Pedido
            $cabecera = $this->transferencia->cargarCabecera( $this->idCadena, $this->idRestaurante, $idLocal, $this->codigo );
            $cabecera["transferencia"] = $request_transferencia;
            $cabecera["fidelizacion"] = json_decode($cabecera["fidelizacion"]);
            //print_r(json_encode($cabecera));

            // Obtener Motivo Anulacion y Obtener Datos Tarjeta
            $fpf = $this->transferencia->cargarDatosFormaPago( $this->idCadena, $this->idRestaurante, $cabecera["idFactura"] );
            //print json_encode($fpf);

            if (isset($fpf) && count($fpf) > 0) {
                $cabecera["transferencia"]->tarjeta = $fpf;
                $cabecera["transferencia"]->cantidadTarjetas = count($fpf);
            }else{
                $cabecera["transferencia"]->cantidadTarjetas = 0;
            }

            if($cabecera["calle1Domicilio"] == null || $cabecera["calle1Domicilio"] == ''){
                $cabecera["calle1Domicilio"] = '-';
            }

            if($cabecera["calle2Domicilio"] == null || $cabecera["calle2Domicilio"] == ''){
                $cabecera["calle2Domicilio"] = '-';
            }

            if($cabecera["observacionesDomicilio"] == null || $cabecera["observacionesDomicilio"] == ''){
                $cabecera["observacionesDomicilio"] = '-';
            }

            if($cabecera["numDirecciondomicilio"] == null || $cabecera["numDirecciondomicilio"] == ''){
                $cabecera["numDirecciondomicilio"] = '-';
            }

            if($cabecera["codZipCode"] == null || $cabecera["codZipCode"] == ''){
                $cabecera["codZipCode"] = '12345';
            }

            if($cabecera["tipoInmueble"] == null || $cabecera["tipoInmueble"] == ''){
                $cabecera["tipoInmueble"] = 1;
            }

            //Tranferencias con descuentos
            $aplicaDescuentos = false;
            if($cabecera["montoTotalDescuentos"] > 0)
                $aplicaDescuentos = true;
            //Fin Validacion Transferencias con DEscuentos
            

            $detalle = $this->transferencia->cargarDetalle( $this->idCadena, $this->idRestaurante, $this->codigo, $aplicaDescuentos );
            $modificadores = $this->transferencia->cargarModificadores( $this->idCadena, $this->idRestaurante, $this->codigo );
            $formaPago = $this->transferencia->cargarFormaPago( $this->idCadena, $this->idRestaurante, $this->codigo );

            // Armar Payload
            $transaccion = array (
                "cabecera" => array (
                    $cabecera
                ),
                "detalle" => $detalle,
                "modificadores" => $modificadores,
                "formasPago" => $formaPago
            );
            // Imprimir Payload
            //print "<br/><br/>";
            //print json_encode( $transaccion ); exit();
            
            if ( $urlServicio["estado"] == 1 ) {

                // Obtener URL Servicio
                $url = $urlServicio["direccionws"];
                //$url = 'http://192.168.101.29:8888/api/restApp/transferencia';


                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, ($url));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transaccion));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                $result = curl_exec($ch);
                //print $result; exit();

                $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);                
                
                if (!isset($status) || $status != 200) {

                    if($status == 404){
                        $error->mensaje = "Error:  Ruta de transferencia " .   $url . " No valida ";
                        $error->codigo = 0;
                        $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($transaccion), json_encode($error) );
                        return $error;    
                    }
                    
                    $errorMensaje = '';
                    $response = json_decode($result);
                    if (isset($response->mensaje)){
                        $errorMensaje = $response->mensaje;
                    }
                    $error->mensaje = "Error: " . curl_error( $ch ) . ", " . $errorMensaje;
                    $error->codigo = 0;
                    $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($transaccion), json_encode($error) );
                    return $error;
                }
                $response = json_decode($result);
                //print "Status: " . $status . "<br/>";
            
                // Validar Estado
                if ( $status == 200 ) {

                    // Validar respuesta 200
                    if (isset($response->codigo)){
                        if ( $response->codigo === 200 ) {

                            // Generar nota de crédito de un pedido
                            if ( $medio == 'Operador' || $medio == 'Llamada' ) {
                                // Generar nota de credito
                                $anulacion = $this->cadena->generarNotaCredito($this->idRestaurante, $this->codigo, $idUsuario, $idEstacion, $fpf["idMotivoAnulacion"], "Transferencia de Pedido" );
                                $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO - GENERACION NOTA CREDITO', json_encode($anulacion), '' );
                            }

                            // Confirmación Transferencia
                            $respuesta = $this->transferencia->cargarConfirmarTransferencia( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, json_encode($request_destino), json_encode($response) );
                            $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO - CONFIRMACION', json_encode($respuesta), '' );
                            return $respuesta;

                        } else {
                            $mensaje = "";
                            if (isset($response->mensaje) ) {
                                $mensaje = $response->mensaje;
                            } else if (isset($response->message) ) {
                                $mensaje = $response->message;
                            }

                            $resultadoPeticion = '';

                            if(isset($result)){
                                $resultadoPeticion .=  ' , '. json_encode($result);
                            }


                            $error->mensaje =  $mensaje;
                            $error->codigo = 0;
                            $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($transaccion), json_encode($error).' '.$resultadoPeticion );
                            return $error;
                        }
                    } else {
                        $mensaje = "";
                        if (isset($response->mensaje) ) {
                            $mensaje = $response->mensaje;
                        } else if (isset($response->message) ) {
                            $mensaje = $response->message;
                        }
                        $error->mensaje =  $mensaje;
                        $error->codigo = 0;
                        $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($transaccion), json_encode($error) );
                        return $error;
                    }
                    

                }

            } else {

                // Error, no está configurada la url del servicio de transacciones
                $error->mensaje = "Servicio de transferencias no configurado.";
                $error->codigo = 0;
                $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($transaccion), json_encode($error) );
                return $error;

            }

        } catch (Exception $e) {
            $error->mensaje = "Error interno del servidor.";
            $error->codigo = 0;
            $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($transaccion), json_encode($error) );
            return $error;
        }

        
    }




function obtenerTokenDistribuidor($idUsuario) {
    try {

        $error = new \stdClass();
        $urlServicio = $this->transferencia->cargarURLServicioAutenticacionServidor( $this->idRestaurante );
        $client = $this->cadena->cargarConfiguracionPoliticasPickupAutenticacionDistribuidor($this->idCadena);
        $client_id = $client["client_id"];
        $client_secret = $client["client_secret"];
        //Clase genérica para el consumo de REST
        //echo "CLIENTE->". $client_id;
        //echo "CLIENT_SECRET->". $client_secret;
        
        
        if ( $urlServicio["estado"] == 1 ) {
            $url = $urlServicio["direccionws"];
            $ch = curl_init();
            $headers = array();
            $headers[] = 'Authorization: Basic ' . base64_encode($client_id . ":" .$client_secret);
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_URL, ($url));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, "client_id=" . $client_id . "&client_secret=". $client_secret . "&grant_type=client_credentials");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);            
            
            $result = curl_exec($ch);
            
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (!isset($status) || $status != 200) {

                if($status == 404){
                    $error->mensaje = "Error Ruta de autenticacion del distribuidor " .   $url . " No valida ";
                    $error->codigo = 0;
                    $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($request_autenticacion), json_encode($error) );
                    return json_encode($error);    
                }
                $mensaje = "";
                    if (isset($response->error) ) {
                        $mensaje = $response->error;
                        if (isset($response->error_description)) {
                            $mensaje .=  $response->error_description;
                        }
                    } 
                $error->mensaje = "Error: " . $status. " " . $mensaje;
                $error->codigo = 0;
                $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($request_autenticacion), json_encode($error) );
                return json_encode($error);
            }
            $response = json_decode($result);
            
            //print "Status: " . $status . "<br/>";
        
            // Validar Estado
            
            if ( $status == 200 ) {
                
                // Validar respuesta 200
                if (isset($response->access_token)){
                    $token = $response->access_token;
                    $respuesta_token = array("token" => $token);
                    return json_encode($respuesta_token);
                   
                } else {
                    $mensaje = "";
                    if (isset($response->error) ) {
                        $mensaje = $response->error;
                        if (isset($response->error_description)) {
                            $mensaje .=  $response->error_description;
                        }
                    } 
                    $error->mensaje =  $mensaje;
                    $error->codigo = 0;
                    $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($request_autenticacion), json_encode($error) );
                    return json_encode($error);
                }             
            }
        } else {

            // Error, no está configurada la url del servicio de transacciones
            $error->mensaje = "Servicio de autenticacion al distribuidor no configurado.";
            $error->codigo = 0;
            $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($request_autenticacion), json_encode($error) );
            return json_encode($error);

        }
        curl_close($ch);   

    } catch (Exception $e) {
        $error->mensaje = "Error interno del servidor.";
        $error->codigo = 0;
        $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($request_autenticacion), json_encode($error) );
        return json_encode ($error);
    }
       
}

function transferir_pedido_pickup( $idLocal, $codigoLocal, $local, $idUsuario, $usuario, $idMotivo, $motivo, $forma_pago, $idEstacion ) {

    try {
        $token = "";
        $error = new \stdClass();
        $respuesta_token = $this->obtenerTokenDistribuidor($idUsuario);
        //echo $respuesta_token;
        $respuesta_token = json_decode($respuesta_token);
        if (isset($respuesta_token->token)) {
            $token = $respuesta_token->token;
            
        } else {
            $error->mensaje = "Error: credenciales invalidas";
            $error->codigo = 0;
            return json_encode($error);  
        }
        

        // Consultar URL Servicio
        $urlServicio = $this->transferencia->cargarURLServicioTransferenciaPickup( $this->idRestaurante );
        //print_r( $urlServicio );

        //Obtener Origen -> Estos datos se envian
        $request_transferencia = new \stdClass();
        $request_transferencia->idLocal = $this->idRestaurante;
        $request_transferencia->codigoLocal = $this->codigoRestaurante;
        $request_transferencia->local = $this->restaurante;
        $request_transferencia->idUsuario = $idUsuario;
        $request_transferencia->usuario = $usuario;
        $request_transferencia->idMotivo = $idMotivo;
        $request_transferencia->motivo = $motivo;
        $request_transferencia->mensaje = $motivo;

        //Obtener Destino -> Estos datos se guardan en la bd local
        $request_destino = new \stdClass();
        $request_destino->idLocal = $idLocal;
        $request_destino->idUsuario = $idUsuario;
        $request_destino->usuario = $usuario;
        $request_destino->idMotivo = $idMotivo;
        $request_destino->motivo = $motivo;
        $request_destino->mensaje = $motivo;
        $request_destino->codigo_app = $this->codigo;
        
        
        
        if ( $urlServicio["estado"] == 1 ) {

            // Obtener URL Servicio
            $headers = array();
            $headers[] = 'Authorization: Bearer ' . $token;
            $headers[] = 'Content-Type: application/json';
            $url = $urlServicio["direccionws"];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, ($url));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_destino));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $response = json_decode($result);
            if (!isset($status) || $status != 200) {

                if($status == 404){
                    $error->mensaje = "Error Ruta de transferencia " .   $url . " No valida ";
                    $error->codigo = 0;
                    $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($request_destino), json_encode($error) );
                    return json_encode($error);    
                }
                if (isset($response->msg)) {
                    $error->mensaje = "Error: ". $status . " " . $response->msg;
                } else {
                    $error->mensaje = "Error: ". $status;
                }
                
                $error->codigo = 0;
                $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($request_destino), json_encode($error) );
                return json_encode($error);
            }
            
            //print "Status: " . $status . "<br/>";
        
            // Validar Estado
            
            if ( $status == 200 ) {
                
                // Validar respuesta 200
                if (isset($response->codigo) && isset($response->data->id_transaccion)){
                    
                    if ( $response->codigo == "200" && $response->data->id_transaccion != null ) {
                        
                        // Generar nota de crédito de un pedido
                        if ( $forma_pago == 'tarjeta' ) {
                            // Generar nota de credito
                            $anulacion = $this->cadena->generarNotaCreditoPickup($this->idRestaurante, $this->codigo, $idUsuario, $idEstacion, $idMotivo, "Transferencia de Pedido Pickup" );
                            $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO PICKUP - GENERACION NOTA CREDITO', json_encode($anulacion), '' );
                        }

                        // Confirmación Transferencia

                        $respuesta = $this->transferencia->cargarConfirmarTransferenciaPickup( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, json_encode($request_destino), json_encode($response) );
                        $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO - CONFIRMACION', json_encode($respuesta), '' );
                        return json_encode($respuesta);

                    } else {
                        $mensaje = "";
                        if (isset($response->msg) ) {
                            $mensaje = "Error: Pedido no recibido en el restaurante " . $codigoLocal . "de destino.";
                        } 

                        $resultadoPeticion = '';

                        if(isset($result)){
                            $resultadoPeticion .=  ' , '. json_encode($result);
                        }


                        $error->mensaje =  $mensaje;
                        $error->codigo = 0;
                        $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($request_destino), json_encode($error).' - '.json_encode($resultadoPeticion) );
                        return json_encode($error);
                    }
                } else {
                    $mensaje = "";
                    if (isset($response->msg) ) {
                        $mensaje = "Error: Pedido no recibido en el restaurante " . $codigoLocal . " de destino.";
                    } 
                    $error->mensaje =  $mensaje;
                    $error->codigo = 0;
                    $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($request_destino), json_encode($error) );
                    return json_encode($error);
                }
                

            }

        } else {

            // Error, no está configurada la url del servicio de transacciones
            $error->mensaje = "Servicio de transferencias no configurado.";
            $error->codigo = 0;
            $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($request_destino), json_encode($error) );
            return json_encode($error);

        }
        curl_close($ch); 
    } catch (Exception $e) {
        $error->mensaje = "Error interno del servidor.";
        $error->codigo = 0;
        $this->transferencia->guardarAuditoria( $this->idCadena, $this->idRestaurante, $idUsuario, $this->codigo, 'TRANSFERENCIA', 'TRANSFERIR PEDIDO', json_encode($request_destino), json_encode($error) );
        return json_encode ($error);
    }

    
}

}
