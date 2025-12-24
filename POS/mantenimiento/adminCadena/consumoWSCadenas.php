<?php

session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_adminCadena.php';

$cadena = new adminCadena();
$idCadena = $_SESSION['cadenaId'];
$request = (object) $_POST;

//Sincronizar cadenas con Sistema Gerente
if ($request->metodo === "sincronizarCadenas") {
    $ch = curl_init("http://srvv-devsoa:8080/GerenteNacional.ServiciosWeb/webresources/cadenas/cargarcadenas/");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $result = curl_exec($ch);
    $respuesta = json_decode($result);
    curl_close($ch);
    $cadenas = $respuesta->cadenas;
    print $cadena->sincronizarCadenas($cadenas);
}