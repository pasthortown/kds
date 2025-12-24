<?php

header('Content-type:application/json;charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once "../system/conexion/clase_sql.php";
include_once "./clase_orden_pedido_posterior.php";

$lc_orden_pedido_posterior = new OrdenPedidoPosterior();
$body = json_decode(file_get_contents('php://input'),true);

if(isset($_GET["getOrdenPedidoPosterior"]))
{   
    $ip = $_GET['ip'];
    print $lc_orden_pedido_posterior->getOrdenPedidoPosterior($ip);
}