<?php

session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_menu.php';
include_once "../../clases/clase_tomapedidoMenu.php";


if (
    empty($_SESSION['rstId'])
    OR empty($_SESSION['usuarioId'])
    OR empty($_SESSION['cadenaId'])
) {
    die(json_encode((object)[
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$menu = new Menu();
$lc_config = new menuPedido();


$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];

$request = (object)(array_map('utf8_decode', $_POST));

if(isset($_GET["fn_IgualarTablaMenu"])){
    $lc_condiciones[0] = $idCadena;
//    var_dump($lc_condiciones);
    print $menu->fn_IgualarTablaMenu( $lc_condiciones);

}else if(isset($_GET["igualarBotonesMenu"])){
    $lc_condiciones[0] = $idCadena;
    $lc_condiciones[1] =  $_GET["menu_id"];
    print $menu->fn_IgualarBotonesMenu( $lc_condiciones);

}else if(isset($_GET["fn_NombreMenu"])){
    $lc_condiciones[0] = $idCadena;
    $lc_condiciones[1] =  $_GET["menu_id"];
    print $menu->fn_NombreMenu( $lc_condiciones);

}