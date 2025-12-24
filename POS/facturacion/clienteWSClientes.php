<?php

session_start();

include_once '../system/conexion/clase_sql.php';
include_once '../clases/clase_clientes.php';
include_once "../clases/clase_webservice.php";
include_once "../clases/clase_fidelizacionCadena.php";
include("../clases/clase_facturacion.php");

require_once('../resources/module/fidelizacion/TokenManager.php');

$tokenLoyaltieManager = new TokenManager();
$servicioWebObj = new webservice();
$cliente = new Cliente();
$array_ini = parse_ini_file("../serviciosweb/interface/config.ini");
$idUsuario = $_SESSION['usuarioId'];
$restaurante = $_SESSION['rstId'];
$cadena = $_SESSION['cadenaId'];
//var_dump($_SESSION["ContrasenaWebServicesFidelizacion"]);
$request = (object)filter_input_array(INPUT_POST);

// Inicio la clase token OAuth
// $tokenLoyalty = new TokenLoyalty(); ******* V2 FIDELIZACION *******

if (isset($request->documento)) {
    $documento = trim($request->documento);
} else {
    $documento = "";
}

if (isset($request->revocado)) {
    $revocado = trim($request->revocado);
} else {
    $revocado = 0;
}

if (isset($request->tipoDocumento)) {
    $tipoDocumento = trim($request->tipoDocumento);
} else {
    $tipoDocumento = "";
}

if (isset($request->app)) {
    $app = trim($request->app);
    $_SESSION["appid"] = $app;
} else {
    $_SESSION["appid"] = "jvz";
    $app = "jvz";
}

if ($request->metodo === "buscarClienteV2") {
    $respuesta = array(
        "type" => "",
        "codigo" => 0,
        "estado" => 0,
        "mensaje" => "",
        "cliente" => array(
            "alias" => "",
            "apellido" => "",
            "autorizacion" => "",
            "celular" => "",
            "correo" => "",
            "descripcion" => "",
            "direccionDomicilio" => "",
            "fechaNacimiento" => "",
            "fechaUltimaActualizacion" => "",
            "identificacion" => "",
            "nombre" => "",
            "telefonoDomiclio" => "",
            "tipoIdentificacion" => "",
            "IDCliente" => "",
            "tipoCliente" => ""
        )
    );

    try {
        $datosCliente = $cliente->fn_buscarCliente($documento, $revocado, $tipoDocumento);
        if ($datosCliente["str"] > 0) {
            $respuesta["estado"] = 1;
            $respuesta["mensaje"] = "baselocal";
            $respuesta["cliente"]["descripcion"] = $datosCliente["descripcion"];
            $respuesta["cliente"]["correo"] = $datosCliente["email"];
            $respuesta["cliente"]["direccionDomicilio"] = $datosCliente["direccion"];
            $respuesta["cliente"]["identificacion"] = $datosCliente["documento"];
            $respuesta["cliente"]["telefonoDomiclio"] = $datosCliente["telefono"];
            $respuesta["cliente"]["IDCliente"] = $datosCliente["IDCliente"];
            $respuesta["cliente"]["tipoCliente"] = $datosCliente["tipoCliente"];
        }
        print json_encode($respuesta);
    }catch (Exception $e) {
        print json_encode($e);
    }
} else if ($request->metodo === "buscarCliente") {
    // Seteo de variables
    $respuesta = array(
        "type" => "",
        "codigo" => 0,
        "estado" => 0,
        "mensaje" => "",
        "cliente" => array(
            "alias" => "",
            "apellido" => "",
            "autorizacion" => "",
            "celular" => "",
            "correo" => "",
            "descripcion" => "",
            "direccionDomicilio" => "",
            "fechaNacimiento" => "",
            "fechaUltimaActualizacion" => "",
            "identificacion" => "",
            "nombre" => "",
            "telefonoDomiclio" => "",
            "tipoIdentificacion" => "",
            "IDCliente" => "",
            "tipoCliente" => ""
        )
    );

    try {
        $restaurante = $_SESSION['rstId'];
        $datosCliente = $cliente->fn_buscarCliente($documento, $revocado);
        if ($datosCliente["str"] > 0) {
            $respuesta["estado"] = 3;
            $respuesta["mensaje"] = "baselocal";
            $respuesta["cliente"]["descripcion"] = $datosCliente["descripcion"];
            $respuesta["cliente"]["correo"] = $datosCliente["email"];
            $respuesta["cliente"]["direccionDomicilio"] = $datosCliente["direccion"];
            $respuesta["cliente"]["identificacion"] = $datosCliente["documento"];
            $respuesta["cliente"]["telefonoDomiclio"] = $datosCliente["telefono"];
            $respuesta["cliente"]["IDCliente"] = $datosCliente["IDCliente"];
            $respuesta["cliente"]["tipoCliente"] = $datosCliente["tipoCliente"];
        } else {
            $respuesta["estado"] = 2;
            $respuesta["cliente"]["identificacion"] = $documento;
            $respuesta["cliente"]["tipoIdentificacion"] = $tipoDocumento;
        }
        if ($respuesta["estado"] !== 0) {
            print json_encode($respuesta);
        } else {
            print 1;
        }
    } catch (Exception $e) {
        print json_encode($e);
    }
} else if ($request->metodo === "enviarCliente") {
} else if ($request->metodo === "obtenerDatosCliente") {
    // Seteo de variables
    $obtenerCliente = array(
        "type" => "",
        "codigo" => 0,
        "estado" => 0,
        "mensaje" => "",
        "cliente" => array(
            "alias" => "",
            "apellido" => "",
            "autorizacion" => "",
            "celular" => "",
            "correo" => "",
            "descripcion" => "",
            "direccionDomicilio" => "",
            "fechaNacimiento" => "",
            "fechaUltimaActualizacion" => "",
            "identificacion" => "",
            "nombre" => "",
            "telefonoDomiclio" => "",
            "tipoIdentificacion" => "",
            "IDCliente" => "",
            "tipoCliente" => ""
        )
    );

    try {
        $restaurante = $_SESSION['rstId'];
        $datosWebservice = $servicioWebObj->retorna_WS_Clientes_Cliente($restaurante);
        $urlServicioWeb = trim($datosWebservice["urlwebservice"]);

        $ch = curl_init($urlServicioWeb);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['identificacion' => $documento, 'tipoIdentificacion' => $tipoDocumento]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        $respuestaSolicitud = json_decode($result);
        if ($respuestaSolicitud !== null){
            if ($respuestaSolicitud->estado === 1) {
                $obtenerDatos = $respuestaSolicitud->cliente['0'];
                $obtenerCliente["estado"] = $respuestaSolicitud->estado;
                $obtenerCliente["cliente"]["descripcion"] = $obtenerDatos->descripcion;
                $obtenerCliente["cliente"]["correo"] = $obtenerDatos->correo;
                $obtenerCliente["cliente"]["direccionDomicilio"] = $obtenerDatos->direccionDomicilio;
                $obtenerCliente["cliente"]["identificacion"] = $obtenerDatos->identificacion;
                $obtenerCliente["cliente"]["telefonoDomiclio"] = $obtenerDatos->telefonoDomiclio;
                $obtenerCliente["cliente"]["tipoIdentificacion"] = $obtenerDatos->tipoIdentificacion;
                $obtenerCliente["cliente"]["IDCliente"] = "";
                $obtenerCliente["cliente"]["autorizacion"] = $obtenerDatos->autorizacion;
                $obtenerCliente["cliente"]["tipoCliente"] = $obtenerDatos->tipoCliente;

                $_SESSION['fdznDocumento'] = $obtenerDatos->identificacion;
                $_SESSION['fdznNombres'] = $obtenerDatos->descripcion;
                //v2 api
                $_SESSION['fdznDireccion'] = $obtenerDatos->direccionDomicilio;
            }

            if ($respuestaSolicitud->estado === 2) {

                $obtenerDatos = $respuestaSolicitud->cliente['0'];
                $obtenerCliente["estado"] = $respuestaSolicitud->estado;
                $obtenerCliente["cliente"]["autorizacion"] = $obtenerDatos->autorizacion;
            }
        }
        
        print json_encode($obtenerCliente);
    } catch (Exception $exc) {
        print json_encode($exc);
    }
} else if ($request->metodo === "buscaClienteAx") {
    // Seteo de variables
    $respuesta = array(
        "type" => "",
        "codigo" => 0,
        "estado" => 0,
        "mensaje" => "",
        "cliente" => array(
            "alias" => "",
            "apellido" => "",
            "autorizacion" => "",
            "celular" => "",
            "correo" => "",
            "descripcion" => "",
            "direccionDomicilio" => "",
            "fechaNacimiento" => "",
            "fechaUltimaActualizacion" => "",
            "identificacion" => "",
            "nombre" => "",
            "telefonoDomiclio" => "",
            "tipoIdentificacion" => "",
            "IDCliente" => ""
        )
    );

    try {

        $datosWebserviceCreditos = $servicioWebObj->retorna_WS_Clientes_Clientes_Externos($_SESSION['rstId']);
        $urlServicioWebCreditos = trim($datosWebserviceCreditos["urlwebservice"]);
        
        $ch = curl_init($urlServicioWebCreditos);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['descripcion' => $documento, 'tipoCliente' => $tipoDocumento]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection                        
        curl_close($ch);
        $respuestaSolicitud = json_decode($result);


        print json_encode($respuestaSolicitud);
    } catch (Exception $e) {

        print json_encode($e);
    }
} else if ($request->metodo === "preRegistroFireBase") {

    $factura = new facturas();

    // $CUSTOMREQUEST = ( $request->accion === 1) ? "POST" : "PUT";
    $parametros = array(
        "documentType" => ($request->tipoDocumento == "CEDULA") ? "CI" : "RUC",
        "document" => $request->documento, // $request->documento
        "name" => $request->descripcion,
        "phone" => $request->telefono,
        "email" => $request->correo,
        "photoUrl" => "",
        "birthdate" => "",
        "genre" => "",
        "maritalStatus" => ""
    );
    $parametrosWS = json_encode($parametros);

    $curl = curl_init();

    $urlWSPreRegistroFirebase = $servicioWebObj->retorna_rutaWS($_SESSION['rstId'], 'FIREBASE', 'PREREGISTRO');
    $urlWSPreRegistroFirebase = $urlWSPreRegistroFirebase["urlwebservice"];
    $accessToken = $tokenLoyaltieManager->generateNewAccessTokenApp($restaurante,$_SESSION["claveConexion"]);
    // ******* V2 FIDELIZACION *******
    // Verificar Token
    //$tokenLoyalty->getToken();
    // ****
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlWSPreRegistroFirebase, //"http://us-central1-juan-valdez-development.cloudfunctions.net/api/preRegisteredUsers/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $parametrosWS,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => array(
            // var_dump("Bearer ".$_SESSION["ContrasenaWebServicesFidelizacion"]);
            "authorization: Bearer " . $accessToken,
            "content-type: application/json"
        )
    ));

    $response = curl_exec($curl);
    // print "Response: " . $response;
    $err = curl_error($curl);
    // print "Error: " . $err;
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    // print "Status: " . $http_status;

    curl_close($curl);

    if ($http_status == 200) {

        $respuesta = json_decode($response);
        $_SESSION['fb_document'] = $request->documento;
        $_SESSION['fdznDocumento'] = $request->documento; // para cuando se cierre erroneamente en la pantalla facturacin.
        $_SESSION['fb_name'] = $respuesta->data->name;
        $_SESSION['fb_status'] = $respuesta->data->status;
        $_SESSION['fb_points'] = 0;
        $_SESSION['fdznNombres'] = $respuesta->data->name;
        $_SESSION['fdznDireccion'] = $respuesta->data->address;
        $_SESSION['fb_econtroDatos'] = 1;
        $_SESSION['fb_security'] = "";
        $_SESSION['fb_money'] = 0;

        //Guardar LogRegistro Cliente Plan Fidelizacion por Cajeros
        $trama = '{ "cliente":  "' . $request->descripcion . '", "clienteDocumento": "' . $request->documento . '"}';
        $accion = 'REGISTRO DE CLIENTE';
        $descripcionLog = "Registro de cliente a plan de fidelizacion.";
        //Guardar Log
        $factura->logProcesosFidelizacion($descripcionLog, $accion, $restaurante, $cadena, $idUsuario, $trama);
        $respuesta->estado = 1;

        //  return json_encode($response);
        print json_encode($response);
    } else {
        $respuesta = json_decode($response);
        if (isset($respuesta->message)) {
            $mensaje = utf8_encode($respuesta->message);
        } else {
            $mensaje = "Error no definido.";
        }

        $trama = '{ "cliente":  "' . $request->descripcion . '", "clienteDocumento": "' . $request->documento . '"}';
        $accion = 'REGISTRO DE CLIENTE';
        $descripcionLog = "Error al registrar al cliente. " . $mensaje;
        //Guardar Log
        $factura->logProcesosFidelizacion($descripcionLog, $accion, $restaurante, $cadena, $idUsuario, $trama);

        $respuesta->estado = -1;

        print json_encode($respuesta);
    }
} else if ($request->metodo === 'consultaEstadoFireBase') {
    $factura = new facturas();
    $fb_econtroDatos = 0;
    $curl = curl_init();
    $jwtClienteDecoded = null;
    $response_validator = new stdClass();
    if (!isset($request->codigoSeguridad)) {
        $response_validator->mensaje = 'No se encontro el contenido en el codigo QR';
        $response_validator->error = true;
        print (json_encode($response_validator));
        $factura->logProcesosFidelizacion("Error al registrar al cliente. ", "FIDELIZACION", $restaurante, $cadena, $idUsuario, '');
        return;
    }
    $jwtClienteDecoded = $tokenLoyaltieManager->decodeValidJwt($request->codigoSeguridad);
    if(!$jwtClienteDecoded['status']){
        // si el estado es falso entonces ver cual es el error
        $response_validator->mensaje = $jwtClienteDecoded['error'];
        $response_validator->error = true;
        print (json_encode($response_validator));
        $factura->logProcesosFidelizacion("Error al registrar al cliente. ", "FIDELIZACION", $restaurante, $cadena, $idUsuario, '');
        return;
    }
    $factura->logProcesosFidelizacion("Cliente registrado por QR", "FIDELIZACION", $restaurante, $cadena, $idUsuario, '');
    $cdn_id = $_SESSION['cadenaId'];
    $fidelizacionCadena = new Cadena();

    // ******
    // Aqui se debe enviar el parametr app
    // ******

    $respuesta = $fidelizacionCadena->cargarConfiguracionPoliticas($cdn_id);
    $respuesta = json_decode($respuesta);
    // ******* V1 FIDELIZACION *******
    $_SESSION['claveConexion'] = $respuesta->seguridad;
    // ******* V2 FIDELIZACION *******
    // Verificar Token
    // *******
    /*
    Aqui se debe obtener las urls por app
    */
    if ($app === 'jvz') {
        $urlWSPreRegistroFirebase = null;
        $uid='';
        if(isset($_SESSION['canje_v2']) && !empty($_SESSION['canje_v2']) && trim($_SESSION['canje_v2'])==1){
            $urlWSPreRegistroFirebase = $servicioWebObj->retorna_rutaWS($_SESSION['rstId'], 'FIREBASE', 'ESTADOCLIENTEV2');
            $uid=$jwtClienteDecoded['data']->uid;
            $urlWSPreRegistroFirebase=str_replace(':uid',$uid,$urlWSPreRegistroFirebase);
            $documento='';
        }
        else{
            $urlWSPreRegistroFirebase = $servicioWebObj->retorna_rutaWS($_SESSION['rstId'], 'FIREBASE', 'ESTADOCLIENTE');
        }  
        $urlWSPreRegistroFirebase = $urlWSPreRegistroFirebase['urlwebservice'];
    } else {
        $urlWSPreRegistroFirebase = $servicioWebObj->retorna_rutaWS($_SESSION['rstId'], 'FIDELIZACION ' . $app, 'ESTADOCLIENTE');
        $urlWSPreRegistroFirebase = $urlWSPreRegistroFirebase['urlwebservice'];
    }
    $accessToken = $tokenLoyaltieManager->generateNewAccessTokenApp($restaurante,$_SESSION['claveConexion']);
 
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlWSPreRegistroFirebase . $documento,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $accessToken,
            'cache-control: no-cache',
            'content-type: application/json',
            'postman-token: 61dba5e9-9447-e881-98c7-18cd9cc884f0'
        ),
    ));
    //Obtengo respuesta
    $response = curl_exec($curl);
    //print "Response: " . $response.' token en :'.$_SESSION["claveConexion"].' expira en :'.$_SESSION["expiresAt"];
    $err = curl_error($curl);
    // print "Error: " . $err;
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    // print "Status: " . $http_status;
    curl_close($curl);
    // si el estado es positivo. 
    if ($http_status == 200) {
        $vectorDatos = json_decode($response);
        //$vectorDatos = $vectorDatos->data; // ******* V1 FIDELIZACION *******
        $_SESSION['fidelizacionActiva'] = 1;
        $_SESSION['fb_document'] = $vectorDatos->document;
        $_SESSION['fdznDocumento'] = $vectorDatos->document; // para cuando se cierre erroneamente en la pantalla facturacin.
        $_SESSION['fb_name'] = $vectorDatos->name;
        if(isset($_SESSION['canje_v2']) && !empty($_SESSION['canje_v2']) && trim($_SESSION['canje_v2'])==1){
            $_SESSION['fb_status'] = 'REGISTERED';
            $vectorDatos->status='REGISTERED';
        }else {
            $_SESSION['fb_status'] = $vectorDatos->status;
        }
        if ($vectorDatos->status == 'BLOCKED') {
            $_SESSION['fb_points'] = 0;
        } else {
            $_SESSION['fb_points'] = $vectorDatos->points; // ******* V1 FIDELIZACION *******
            //$_SESSION['fb_points'] = $vectorDatos->balance->points;// ******* V2 FIDELIZACION *******
        }
        $_SESSION['fdznNombres'] = $vectorDatos->name;
        $_SESSION['fdznDireccion'] = null; //verficar
        $_SESSION['fb_econtroDatos'] = 1;
        $_SESSION['fb_security'] = $request->codigoSeguridad; // Viene desde el ingreso del código del cliente
        $_SESSION['fb_money'] = $vectorDatos->balance; // ******* V1 FIDELIZACION *******
        $_SESSION['vectorDatos_CanjePuntos'] =$response;
        $_SESSION['uid'] =$uid;
        
        $objeto = new stdClass();
        $objeto->respuesta = $vectorDatos;
        $objeto->url = $urlWSPreRegistroFirebase . $documento;
        $objeto->Token =$accessToken;
        $factura->logProcesosFidelizacion("Datos Obtenidos por QR", "FIDELIZACION", $restaurante, $cadena, $idUsuario, json_encode($objeto));        
        print ($response);
    } else {

        $resp = new stdClass();
        $vectorDatos = json_decode($response);
        // print_r($vectorDatos);
        $_SESSION['fidelizacionActiva'] = 0;
        $_SESSION['fb_document'] = null;
        $_SESSION['fdznDocumento'] = null; // para cuando se cierre erroneamente en la pantalla facturacin.
        $_SESSION['fb_name'] = null;
        $_SESSION['fb_status'] = null;
        $_SESSION['fb_points'] = null;
        $_SESSION['fdznNombres'] = null;
        $_SESSION['fdznDireccion'] = null;
        $_SESSION['fb_econtroDatos'] = 0;
        $_SESSION['fb_money'] = 0;

        if ($http_status == 404) {
            $mensaje = $vectorDatos->message;
            if ($mensaje == "Usuario no encontrado") {
                /*                 * ******************************************* */
                // Buscar base local
                /*                 * ******************************************* */
                $datosCliente = $cliente->fn_buscarCliente($documento);
                if ($datosCliente["str"] > 0) {
                    $resp->cliente = new stdClass();
                    $resp->cliente->descripcion = $datosCliente["descripcion"];
                    $resp->cliente->correo = $datosCliente["email"];
                    $resp->cliente->direccionDomicilio = $datosCliente["direccion"];
                    $resp->cliente->identificacion = $datosCliente["documento"];
                    $resp->cliente->telefonoDomiclio = $datosCliente["telefono"];
                    $resp->cliente->idCliente = $datosCliente["IDCliente"];
                    $resp->cliente->estadoCliente = 0; //Encontro el dato en la base local

                    $objeto = new stdClass();
                    $objeto->respuesta = $vectorDatos;
                    $objeto->url = $urlWSPreRegistroFirebase . $documento;
                    $factura->logProcesosFidelizacion("Usuario no encontrado", "FIDELIZACION", $restaurante, $cadena, $idUsuario, json_encode($objeto));
                } else {
                    /*                     * ******************************************* */
                    // Consultar datos maestros
                    /*                     * ******************************************* */
                    $restaurante = $_SESSION['rstId'];
                    $datosWebservice = $servicioWebObj->retorna_WS_Clientes_Cliente($restaurante);
                    $urlServicioWeb = trim($datosWebservice["urlwebservice"]);

                    //Consumo Datos Maestros
                    $ch = curl_init($urlServicioWeb);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['identificacion' => $documento, 'tipoIdentificacion' => (strlen($documento) > 10 ? "RUC" : "CEDULA")]));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
                    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                    //execute post
                    $result = curl_exec($ch);
                    // print "Respuesta: " . $result;
                    //close connection
                    curl_close($ch);
                    $respuestaSolicitud = json_decode($result);
                    if ($respuestaSolicitud->estado === 1) {
                        $clienteDatos = $respuestaSolicitud->cliente['0'];

                        $resp->cliente = new stdClass();
                        $resp->cliente->estadoCliente = $respuestaSolicitud->estado;

                        // $_SESSION['fdznDocumento'] = $clienteDatos->identificacion;
                        // $_SESSION['fdznNombres'] = $clienteDatos->descripcion;

                        $resp->cliente->descripcion = $clienteDatos->descripcion;
                        $resp->cliente->correo = $clienteDatos->correo;
                        $resp->cliente->direccionDomicilio = $clienteDatos->direccionDomicilio;
                        $resp->cliente->identificacion = $clienteDatos->identificacion;
                        $resp->cliente->telefonoDomiclio = $clienteDatos->telefonoDomiclio;
                        $resp->cliente->tipoIdentificacion = $clienteDatos->tipoIdentificacion;
                        $resp->cliente->tipoCliente = $clienteDatos->tipoCliente;
                        $resp->cliente->idCliente = "";
                        //ADD
                        // $resp["cliente"]["autorizacion"] = $clienteDatos->autorizacion;
                        // Guarda datos del cliente
                        $registroCliente = $cliente->fn_registrarClienteWS('I', 'W', $clienteDatos->tipoIdentificacion, $clienteDatos->identificacion, $clienteDatos->descripcion, $clienteDatos->direccionDomicilio, $clienteDatos->telefonoDomiclio, $clienteDatos->correo, $idUsuario, 1, $clienteDatos->tipoCliente);
                        // Recupero el IDCliente
                        $resp->cliente->idCliente = $registroCliente["IDCliente"];
                        $resp->cliente->estado = 1; //Encontro el dato en master data
                    } else if ($respuestaSolicitud->estado === 2) {
                        /*                         * ********************************************************************** */
                        // Cuando el cliente no existe en los datos maestros ni en la base del local
                        /*                         * ********************************************************************** */
                        $resp->cliente = new stdClass();
                        $resp->cliente->descripcion = "";
                        $resp->cliente->correo = "";
                        $resp->cliente->direccionDomicilio = "";
                        $resp->cliente->identificacion = $documento;
                        $resp->cliente->telefonoDomiclio = "";
                        $resp->cliente->tipoIdentificacion = (strlen($documento) > 10 ? "RUC" : "CEDULA");
                        $resp->cliente->idCliente = "";
                        $resp->cliente->estadoCliente = -1; //No encontro se encontr� el dato
                        // $resp->cliente->autorizacion = $clienteDatos->autorizacion;
                    }
                }
            }
        } else {
            $mensaje = "";
        }

        $resp->mensaje = $mensaje;
        $resp->codigo = -1;
        $resp->estadoPeticion = $http_status;

        print json_encode($resp);
    }
} else if ($request->metodo === "anularCanjePuntos") {
    $curl = curl_init();
    $cfac_id = $request->cfac_id;

    // Obtener app
    $app = $request->app;

    $cdn_id = $_SESSION['cadenaId'];
    $fidelizacionCadena = new Cadena();
    $respuesta = $fidelizacionCadena->cargarConfiguracionPoliticas($cdn_id);
    $respuesta = json_decode($respuesta);

    // // var_dump("Bearer ".$_SESSION["ContrasenaWebServicesFidelizacion"]);
    ///$_SESSION["claveConexion"] = $respuesta->seguridad;
    // ******* V2 FIDELIZACION *******
    // Verificar Token
    //$tokenLoyalty->getToken();
    // *******

    if ($app === "jvz") {
        $urlWSPreRegistroFirebase = $servicioWebObj->retorna_rutaWS($_SESSION['rstId'], 'FIREBASE', 'DAR BAJA A TRANSACCION');
    } else {
        $urlWSPreRegistroFirebase = $servicioWebObj->retorna_rutaWS($_SESSION['rstId'], 'FIDELIZACION ' . $app, 'DAR BAJA A TRANSACCION');
    }
    $urlWSPreRegistroFirebase = $urlWSPreRegistroFirebase["urlwebservice"];
    $newTokenGenerate = $tokenLoyaltieManager->generateNewAccessTokenApp($restaurante,$_SESSION["claveConexion"]);
    $datosEnvio = array(
        //"type"=>"POINTS ORDER REVERSE", // ******* V2 FIDELIZACION *******
        //"code"=>$cfac_id // ******* V2 FIDELIZACION *******
        "invoiceCode" => $cfac_id, // ******* V1 FIDELIZACION *******
    );

    $datosEnvio = json_encode($datosEnvio);
    //http_build_query


    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlWSPreRegistroFirebase,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS => $datosEnvio,
        CURLOPT_HTTPHEADER => array(
            // var_dump("Bearer ".$_SESSION["ContrasenaWebServicesFidelizacion"]);
            "authorization: Bearer " . $newTokenGenerate,
            "cache-control: no-cache",
            "content-type: application/json",
            "postman-token: 61dba5e9-9447-e881-98c7-18cd9cc884f0"
        ),
    ));
    //print_r($cfac_id);
//Obtengo respuesta
    $response = curl_exec($curl);
    $err = curl_error($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // si el estado es positivo. 
    if ($http_status == 200) {

        $vectorDatos = json_decode($response);
        print ($response);
    } else {

        $vectorDatos = json_decode($response);
        $mensajeredemptionCode = null;
        //$mensajeredemptionCode = $vectorDatos->errors->invoiceCode;

        if ($mensajeredemptionCode == null) {
            $mensajeredemptionCode = json_encode($vectorDatos);
            $mensajeredemptionCode = str_replace('"', '', $mensajeredemptionCode);
            $mensajeredemptionCode = str_replace('{', '', $mensajeredemptionCode);
            $mensajeredemptionCode = str_replace('}', '', $mensajeredemptionCode);
// 
        }


        $lc_facturas = new facturas();
        $lc_condiciones[0] = $cfac_id;
        $lc_condiciones[1] = 1;
        $lc_condiciones[2] = -1;
        $lc_condiciones[3] = utf8_decode($mensajeredemptionCode);
        $lc_facturas->fn_consultar("estadoErrorNotaCredito", $lc_condiciones);
        print (' {
                    "datos": {
                     "message": "",
                      "errors":  {
                        "invoiceCode": "' . $mensajeredemptionCode . '"
                      }
                    }
                  }');
    }
}