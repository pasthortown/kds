<?php

header('Content-type:application/json;charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once "../system/conexion/clase_sql.php";
include_once "./clase_turnero.php";

$lc_turnero = new Turnero();
$body = json_decode(file_get_contents('php://input'),true);

if(isset($_GET["getOrder"]))
{
    print $lc_turnero->getOrder();
}

if(isset($_GET["getTheme"]))
{
    print $lc_turnero->getTheme();
}

if(isset($_GET["getTimesToMoveOrders"]))
{
    print $lc_turnero->getTimesToMoveOrders();
}

if(isset($_GET["updateOrderState"]))
{
    print $lc_turnero->updateOrderState($_GET["id"]);
}

if(isset($_GET["deleteOrder"]))
{
    print $lc_turnero->deleteOrder($_GET["id"]);
}

if(isset($_GET["updateTheme"]))
{
    $tema = $body;
    print $lc_turnero->updateTheme(json_encode($tema));
}
