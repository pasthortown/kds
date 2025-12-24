<?php

session_start();

//Conexion
include_once "../../system/conexion/clase_sql.php";
//WebServices
include_once "../../clases/clase_webservice.php";
include ("../../clases/clase_facturacion.php");
//Entidades
include_once "../../clases/clase_fidelizacionCadena.php";
include_once "../../clases/clase_fidelizacionPeriodo.php";
include_once "../../clases/clase_fidelizacionAuditoria.php";
require_once('../resources/module/fidelizacion/Token.php');

function specialChars($a) {
    return htmlspecialchars($a, ENT_QUOTES, 'UTF-8');
}

//Parametros Entrada
$request = (object) (array_map('specialChars', $_POST));

// Inicio la clase token OAuth
$tokenLoyalty = new TokenLoyalty();

//Validar parámetros obligatorios
if(isset($request->idFactura)){

    // Verificar Token
    // ******* V2 FIDELIZACION *******
    //$tokenLoyalty->getToken();
    // *******

    $idFactura = $request->idFactura;

    //Variables de sesion
    $idCadena = $_SESSION['cadenaId'];
    $idUsuario = $_SESSION['usuarioId'];
    $idRestaurante = $_SESSION['rstId'];
    $clienteCodigoSeguridad = $_SESSION['fb_security'];
    //Configuraciones
    $fidelizacionCadena = new Cadena();
    $respuesta = $fidelizacionCadena->cargarConfiguracionPoliticas($idCadena);
    $respuesta = json_decode($respuesta);
    $claveConexion = $respuesta->seguridad;
    // $_SESSION["ContrasenaWebServicesFidelizacion"] = "Bearer ".$respuesta->ContrasenaWebServicesFidelizacion;

    //Clase Url Servicios
    $servicioWebObj = new webservice();
    $urlRegistroTransacciones = $servicioWebObj->retorna_rutaWS($idRestaurante, 'FIREBASE', 'REGISTRO TRANSACCION');
    $urlRegistroTransacciones = $urlRegistroTransacciones["urlwebservice"];


    //Cargar Información Factura
    $infoFactura = new facturas();
    $lc_condiciones[0] = $idFactura;
    $lc_condiciones[1] = $idRestaurante;
    $lc_condiciones[2] = $idCadena;

    $datos['customer'] = json_decode($infoFactura->fn_consultar("obtenerDatosTotalesClienteResturante", $lc_condiciones));
    //print_r($datos['customer']);

    $products = json_decode($infoFactura->fn_consultar("obtenerDatosFacturaProductos", $lc_condiciones));
    //print_r($products);

    $paymentMethods = json_decode($infoFactura->fn_consultar("obtenerDatosFacturaFormaPago", $lc_condiciones));
    //print_r($paymentMethods);


    //print_r($datos['customer']);
    //print ;


    $datosEnviar = Array (
        "storeId" => $datos['customer'][0]->storeId,
        "storeCode" => $datos['customer'][0]->storeCode,
        "vendorId" => $datos['customer'][0]->vendorId,
        "invoice" => $datos['customer'][0]->invoice,
        "invoiceCode" => $idFactura,
        "summary" => Array (
            "subtotal" => $datos['customer'][0]->subtotal,
            "vat" => $datos['customer'][0]->vat,
            "vatTaxBase" => $datos['customer'][0]->vatTaxBase,
            "vatCalculated" => $datos['customer'][0]->vatCalculated,
            "total" => $datos['customer'][0]->total
        ),
        "products"=> $products,
        "customer" => Array(
            "documentType" => $datos['customer'][0]->documentType,
            "document" => $datos['customer'][0]->document,
            "name" => $datos['customer'][0]->cli_nombres,
            "address" => $datos['customer'][0]->address
        ),
        "paymentMethods"=> $paymentMethods,
        "token"=>$clienteCodigoSeguridad
    );
    //print json_encode($datosEnviar);

    //Auditoria Reenvío Transacción
    $auditorias = new Auditoria();
    //$auditorias->guardarLog('REENVIAR TRANSACCION ID:' . $idFactura, 'REENVIAR TRANSACCION', $idRestaurante, $idCadena, $idUsuario, $respuesta);

    //Datos Enviar convertir JSON
    $stringDatosEnviar = json_encode($datosEnviar);

    //CurlInit
    $curl = curl_init();

    curl_setopt_array($curl, array (
        CURLOPT_URL => $urlRegistroTransacciones,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $stringDatosEnviar,
        CURLOPT_HTTPHEADER => array (
            "authorization: Bearer " . $claveConexion,
            "cache-control: no-cache",
            "content-type: application/json"
        ),
    ));

    // Puntos que me quedan pointsByCustomer
    $response = curl_exec($curl);
    $err = curl_error($curl);
    $vectorDatos = json_decode($response);

    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $respuestaLog = (object) Array();

    if ($http_status == 200) {

        $respuestaLog->pointsByCustomer = $vectorDatos->data->pointsByCustomer;
        $respuestaLog->pointsByTransacction = $vectorDatos->data->pointsByTransaction;
        $respuestaLog->message = $vectorDatos->message;
        $respuestaLog->invoiceCode = $idFactura;
        $respuestaLog->status = 1;

        //$lc_facturas->fn_consultar("estadoErrorFactura", $lc_condiciones);
        $auditorias->guardarLog('REENVIAR TRANSACCION ID:' . $idFactura, 'REENVIAR TRANSACCION', $idRestaurante, $idCadena, $idUsuario, json_encode($respuestaLog));
        $auditorias->actualizarFacturaLog($idFactura, 3, $vectorDatos->data->pointsByTransaction, $vectorDatos->message);
        print('{ "message": "' . $vectorDatos->message . '", "status": "1"}');

    } else {

        $respuestaLog->status = 0;
        $respuestaLog->message = $vectorDatos->message;
        $respuestaLog->status_code = $vectorDatos->status_code;
        $auditorias->guardarLog('REENVIAR TRANSACCION ID:' . $idFactura, 'REENVIAR TRANSACCION', $idRestaurante, $idCadena, $idUsuario, json_encode($respuestaLog));

        if (isset($response)) {
            print('{ "message": "' . $vectorDatos->message . '", "status": "0"}');
        } else {
            print('{ "message": "Error no identificado.", "status": "0"}');
        }

    }

}