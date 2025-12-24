<?php

session_start();

//Conexion
include_once "../../system/conexion/clase_sql.php";
//WebServices
include_once "../../clases/clase_webservice.php";
include("../../clases/clase_facturacion.php");
//Entidades
include_once "../../clases/clase_fidelizacionCadena.php";
include_once "../../clases/clase_fidelizacionPeriodo.php";
include_once "../../clases/clase_fidelizacionAuditoria.php";
include_once "../../clases/clase_webservice.php";

require_once('../resources/module/fidelizacion/Token.php');
require_once('../resources/module/fidelizacion/TokenManager.php');


$servicioWebObj = new webservice();
$loyaltyTokenManager = new TokenManager();
function specialChars($a)
{
    return htmlspecialchars($a, ENT_QUOTES, 'UTF-8');
}

//Parametros Entrada
$request = (object)(array_map('specialChars', $_POST));

if (isset($request->idFactura)) {


    //Variables de sesion
    $idCadena = $_SESSION['cadenaId'];
    $idUsuario = $_SESSION['usuarioId'];
    $idRestaurante = $_SESSION['rstId'];


    $curl = curl_init();
    $cfac_id = $request->idFactura;
    $cdn_id = $_SESSION['cadenaId'];
    $fidelizacionCadena = new Cadena();
    $respuesta = $fidelizacionCadena->cargarConfiguracionPoliticas($cdn_id);
    $respuesta = json_decode($respuesta);
    // ******* V2 FIDELIZACION *******
    //$tokenLoyalty->getToken();
    // *******

    //$_SESSION["claveConexion"] = $respuesta->seguridad;
    // $_SESSION["ContrasenaWebServicesFidelizacion"] = "Bearer ".$respuesta->ContrasenaWebServicesFidelizacion;

    $urlWSPreRegistroFirebase = $servicioWebObj->retorna_rutaWS($_SESSION['rstId'], 'FIREBASE', 'DAR BAJA A TRANSACCION');
    $urlWSPreRegistroFirebase = $urlWSPreRegistroFirebase["urlwebservice"];
    $newTokenGenerate = $loyaltyTokenManager->generateNewAccessTokenApp($idRestaurante,$_SESSION["claveConexion"]);

    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlWSPreRegistroFirebase,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\r\n  \"invoiceCode\": \"" . $cfac_id . "\"\r\n}", //******* V2 FIDELIZACION *******
        /*CURLOPT_POSTFIELDS => "{
            \r\n  \"type\":\"POINTS ORDER REVERSE\",\r\n           
            \r\n  \"invoiceCode\": \"" . $cfac_id . "\"\r\n
            }", // ******* V2 FIDELIZACION *******
            */
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer " . $newTokenGenerate,
            "cache-control: no-cache",
            "content-type: application/json",
            "postman-token: 61dba5e9-9447-e881-98c7-18cd9cc884f0"
        ),
    ));
//Obtengo respuesta
    $response = curl_exec($curl);
    $err = curl_error($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    $respuestaLog = (object)array();
    // si el estado es positivo. 
    if ($http_status == 200) {

        $vectorDatos = json_decode($response);
        $auditorias->guardarLog('REENVIAR TRANSACCION ID:' . $request->idFactura, 'REENVIAR TRANSACCION', $idRestaurante, $idCadena, $idUsuario, json_encode($respuestaLog));
        $auditorias->actualizarFacturaLog($request->idFactura, 2, '', '');
        print ($response);
    } else {

        /*  $vectorDatos = json_decode($response);
          $mensajeredemptionCode = $vectorDatos->errors->invoiceCode;

          $lc_facturas = new facturas();
          $lc_condiciones[0] = $cfac_id;
          $lc_condiciones[1] = 1;
          $lc_condiciones[2] = -1;
          $lc_condiciones[3] = utf8_decode($mensajeredemptionCode);
          $lc_facturas->fn_consultar("estadoErrorNotaCredito", $lc_condiciones);
         */
        print ($response);
    }
}
 