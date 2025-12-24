<?php

/*
FECHA CREACION   : 05/02/2019 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion de productos Up Selling
*/

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_productosUpSelling.php";

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

$lc_config = new ConfiguracionProductosUpSelling();	
$lc_cadena = $_SESSION["cadenaId"];
$idUsuario = $_SESSION["usuarioId"];

if (htmlspecialchars(isset($_POST["productosConfigurados"]))) {
    $accion = 1;
    $idProducto = 0;
    
    print $lc_config->cargarProductosConfigurados($accion, $lc_cadena, $idProducto);
}
else if (htmlspecialchars(isset($_POST["productos"]))) {
    $accion = htmlspecialchars($_POST["accion"]);
    $idProducto = htmlspecialchars($_POST["idProductoBase"]);
    
    print $lc_config->productos($accion, $lc_cadena, $idProducto);
}
else if (htmlspecialchars(isset($_POST["validaColeccionUpSelling"]))) {
    $accion = "V";
    $idCadena = $lc_cadena;
    $idProductoBase = htmlspecialchars($_POST["idProductoBase"]);
    $idProductoMejora = htmlspecialchars($_POST["idProductoMejora"]);
    
    print $lc_config->validaColeccionUpSelling($accion, $idCadena, $idProductoBase, $idProductoMejora, $idUsuario);
}
else if (htmlspecialchars(isset($_POST["guardar"]))) {
    $accion = htmlspecialchars($_POST["accion"]);
    $idCadena = $lc_cadena;
    $idProductoBase = htmlspecialchars($_POST["idProductoBase"]);
    $idProductoMejora = htmlspecialchars($_POST["idProductoMejora"]);
    
    print $lc_config->guardar($accion, $idCadena, $idProductoBase, $idProductoMejora, $idUsuario);
}
else if (htmlspecialchars(isset($_POST["productosMejora"]))) {
    $accion = 3;
    $idProducto = htmlspecialchars($_POST["idProductoBase"]);
    
    print $lc_config->productosMejora($accion, $lc_cadena, $idProducto);
}
