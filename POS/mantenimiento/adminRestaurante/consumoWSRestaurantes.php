<?php

session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_adminrestaurante.php';
include_once "../../clases/clase_webservice.php";

$servicioWebObj=new webservice();
$restaurante = new restaurante();
$idCadena = $_SESSION['cadenaId'];
$request = (object) $_POST;

//Sincronizar restaurantes con Sistema Gerente
if ($request->metodo === "sincronizarRestaurantes") {
    $rstId = $_SESSION['rstId'];
    $datosWebservice=$servicioWebObj->retorna_WS_Restaurantes_Cadena($rstId);
    $urlServicioWeb=$datosWebservice["urlwebservice"];
    $url=$urlServicioWeb.$idCadena;
    //$url = "http://srvv-devsoa:8080/GerenteNacional.ServiciosWeb/webresources/restaurantes/cargarrestaurantesporcadena?cadena=" . $idCadena;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $result = curl_exec($ch);
    $respuesta = json_decode($result);
    curl_close($ch);
    $restaurantes = $respuesta->restaurantes; 
    print $restaurante->sincronizarRestaurantes($restaurantes);
}