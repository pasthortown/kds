<?php

session_start();
include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_webservice.php";

$idCadena = $_SESSION['cadenaId'];
$restaurante = $_SESSION['rstId'];
$request = (object) $_POST;

//Cargar códigos de respuesta DLL de Sistema Gerente
if ($request->metodo === "cargarCodigoRespuestaDLLGerente") {
    $servicioWebObj = new webservice();
    $datosWebservice = $servicioWebObj->retorna_WS_CodigosGerente_CargarDLL($restaurante);
    if (is_null($datosWebservice["urlwebservice"])) {
        $respuesta = new stdClass();
        $respuesta->str = "La política de Web Services WS RUTA SERVICIO::FORMASPAGO CADENA no esta configurada";
        die(json_encode($respuesta));
    }
    $url = $datosWebservice["urlwebservice"] . "?cadena=" . $idCadena;

    $ch = curl_init($url);
    //$ch = curl_init("http://srvv-devsoa:8080/GerenteNacional.ServiciosWeb/webresources/formaspago/cargarporcadena/?cadena=" . $idCadena);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
    $result = curl_exec($ch);
    $respuesta = json_decode($result);
    curl_close($ch);
    print json_encode($respuesta->formasPago);
}