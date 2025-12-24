<?php
session_start();

include "../system/conexion/clase_sql.php";
include "../clases/clase_plugThem.php";

$plugThem = new PlugThem();
$idRestaurante = $_SESSION['rstId'];

if(htmlspecialchars(isset($_POST["aplicaPlugThem"]))) {    
    $param[0] = 1;
    $param[1] = $idRestaurante;
    print  $plugThem->aplicaPlugThem($param);
}
elseif (htmlspecialchars(isset($_POST["valorConfiguracionPlugThem"]))) {
    $param[0] = 3;
    $param[1] = $idRestaurante;
    print  $plugThem->valorConfiguracionPlugThem($param);
}
elseif (htmlspecialchars(isset($_POST["datosPlugThemPost"]))) {
    $param[0] = 4;
    $param[1] = $idRestaurante;
    $param[2] = htmlspecialchars($_POST["transaccion"]);
    print  $plugThem->datosPlugThemPost($param);
}
elseif (htmlspecialchars(isset($_POST["datosCliente"]))) {

    $param[0] = $_POST["transaccion"];

    print  $plugThem->datosClientePlugThemPost($param);
}
elseif (htmlspecialchars(isset($_POST["tokenLogin"]))) {
    $param[0] = 6;
    $param[1] = $idRestaurante;
    $param[2] = "";
    print  $plugThem->tokenLogin($param);
}