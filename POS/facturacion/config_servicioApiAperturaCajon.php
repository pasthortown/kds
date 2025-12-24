<?php

session_start();

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Isaac Betancourt/////////////////////
////////DESCRIPCION: Cong servicio API apertura///////////////////
///////FECHA CREACION: 25-Octubre-2023//////////////////////////
///////USUARIO QUE MODIFICO: Isaac Betancourt///////////////////
///////DECRIPCION ULTIMO CAMBIO: nuevo api apertura/////////
//////////////////////////////////////////////////////////////

include("../system/conexion/clase_sql.php");
include("../clases/clase_servicioApiAperturaCajon.php");

if (isset($_GET["SinSession"])) {

    $SinSession = $_GET["SinSession"];
    $servicioApiImpresion = json_decode($SinSession['servicioImpresion']);

    $idEstacion=$SinSession['IDEstacion']; 
    $idUsuario = '';
    $cadena = $SinSession['idcadena'];

    // Api impresion
    $ruta = $servicioApiImpresion->ruta_servicio_apertura_cajon;
    $url = $servicioApiImpresion->url;
    $ruta = $servicioApiImpresion->ruta_servicio_apertura_cajon;
    $timeout = $servicioApiImpresion->timeout;
    $reintentos = $servicioApiImpresion->reintentos;
    $impresora = $servicioApiImpresion->impresora_apertura_cajon;  
    $asignacion_retiro_fondo = $servicioApiImpresion->asignacion_retiro_fondo;
    $apertura_cajon_caja_chica = $servicioApiImpresion->apertura_cajon_caja_chica;

        }else{

    $idUsuario = $_SESSION['usuarioId'];
    $cadena = $_SESSION['cadenaId'];
    $idEstacion = $_SESSION['estacionId'];
    
    // Api impresion
    $servicioApiImpresion = json_decode($_SESSION['servicioApiImpresion']);
    $url = $servicioApiImpresion->url;
    $ruta = $servicioApiImpresion->ruta_servicio_apertura_cajon;
    $timeout = $servicioApiImpresion->timeout;
    $reintentos = $servicioApiImpresion->reintentos;
    $impresora = $servicioApiImpresion->impresora_apertura_cajon;  
    
    $asignacion_retiro_fondo = $servicioApiImpresion->asignacion_retiro_fondo;
    $apertura_cajon_caja_chica = $servicioApiImpresion->apertura_cajon_caja_chica;
    
    }

    $curl = $url.$ruta.'/'.$impresora;


$servicio = new servicioApiAperturaCajon();

if (isset($_GET["servicioApiAperturaCajon"])) {
    
    $idFormaPago = '';
    if(isset($_GET["idFormaPago"])){
        $idFormaPago = $_GET["idFormaPago"];
    }

    if($impresora == ''){
        $result["error"] = true;
        $result["mensaje"] = "No tiene impresora registrada para la apertura de cajon";
        print json_encode($result);
        die();
    }

    if($idFormaPago != ''){
        $resultResponse = $servicio->impresionAperturaCajon($cadena, $idFormaPago);
    }else{
        $resultResponse['aplicaAperturaCajon'] = 1;
    }

    if($resultResponse['aplicaAperturaCajon'] == 1){
        curlRetry($curl, $timeout, $reintentos, $idFormaPago, $servicio, $impresora, $idEstacion, $idUsuario);
    }else{
        $result["error"] = false;
        $result["mensaje"] = "No aplica apertura de cajon.";
        print json_encode($result);
        die();
    }

}

function curlRetry($curl, $timeout, $reintentos, $idFormaPago, $servicio, $impresora, $idEstacion, $idUsuario) {
    $header = array();
    $header[] = "Content-Type: application/json";
    $header[] = "Accept: application/json";
    $ch = curl_init($curl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
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

        if (in_array($status_code, array(200, 404))) {

           auditoriaApiImpresion($servicio, 'Apertura cajon', 'SUCCESS', $response, $impresora, $idEstacion, $idUsuario, $curl);
     
        } else {
            if ($intento == $reintentos) {
                $result["error"] = true;
                $result["mensaje"] = "Ha ocurrido un error al momento de imprimir, por favor comuniquese con soporte!!";
                $result = json_encode($result);

                auditoriaApiImpresion($servicio, 'Apertura cajon', 'ERROR', $response, $impresora, $idEstacion, $idUsuario, $curl);

                if($imp_error == 0){
                    print $result;
                }

                break;
            }
        }

        $intento = $intento + 1;
    }

    curl_close($ch);
}

    function auditoriaApiImpresion($servicio, $tipo, $estado, $response, $impresora, $idEstacion, $idUsuario, $curl){

        $servicio->auditoriaApiImpresion('Apertura Cajon', 'SUCCESS', $impresora, $idEstacion, $idUsuario, $curl, '', $response, '', '', '', '', '', 122);
        print $response; 
        die();

    } 

?>