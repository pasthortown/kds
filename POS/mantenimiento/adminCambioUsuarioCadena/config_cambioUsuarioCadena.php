<?php

/*
FECHA CREACION   : 07/05/2018 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Pantalla que realiza el cambio de cadena a usuarios MP 
*/

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_adminCambioUsuarioCadena.php";

if (empty($_SESSION['rstId']) || empty($_SESSION['usuarioId']) || empty($_SESSION['cadenaId'])) {
    die(json_encode((object)[
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$cambioUsuarioCadena = new CambioUsuarioCadena();
$idUsuario = $_SESSION["usuarioId"];

if (htmlspecialchars(isset($_POST["obtenerPerfiles"]))) {
    $param[0] = 1;
    $param[1] = 0;
    $param[2] = 0;
    $param[3] = 0;
    $param[4] = 0;
    
    print $cambioUsuarioCadena->obtenerPerfiles($param);
}
else if (htmlspecialchars(isset($_POST["obtenerUsuarios"]))) {
    $param[0] = 2;
    $param[1] = htmlspecialchars($_POST["IDPerfil"]);
    $param[2] = 0;
    $param[3] = 0;
    $param[4] = 0;
    
    print $cambioUsuarioCadena->obtenerUsuarios($param);
}
else if (htmlspecialchars(isset($_POST["obtenerRestaurantesAsinados"]))) {
    $param[0] = 3;
    $param[1] = 0;
    $param[2] = htmlspecialchars($_POST["idUsuario"]);
    $param[3] = 0;
    $param[4] = 0; 
    
    print $cambioUsuarioCadena->obtenerRestaurantesAsignados($param);
}
else if (htmlspecialchars(isset($_POST["obtenerCadena"]))) {
    $param[0] = 4;
    $param[1] = 0;
    $param[2] = 0;
    $param[3] = 0;
    $param[4] = 0;
    
    print $cambioUsuarioCadena->obtenerCadena($param);
}
else if (htmlspecialchars(isset($_POST["obtenerRegion"]))) {
    $param[0] = 5;
    $param[1] = 0;
    $param[2] = 0;
    $param[3] = 0;
    $param[4] = 0;
    
    print $cambioUsuarioCadena->obtenerRegion($param);
}
else if (htmlspecialchars(isset($_POST["obtenerRestaurantesXRegion"]))) {
    $param[0] = 6;
    $param[1] = 0;
    $param[2] = htmlspecialchars($_POST["idUsuario"]);
    $param[3] = htmlspecialchars($_POST["idCadena"]);
    $param[4] = htmlspecialchars($_POST["idRegion"]);
    
    print $cambioUsuarioCadena->obtenerRestaurantes($param);
}
else if (htmlspecialchars(isset($_POST["agregarUsuarioRestaurante"]))) {
    $param[0] = 1;
    $param[1] = htmlspecialchars($_POST["idRestaurante"]);
    $param[2] = htmlspecialchars($_POST["idUsuario"]);
    $param[3] = $idUsuario;
    
    print $cambioUsuarioCadena->administracionUsuarioRestaurante($param);
}
else if (htmlspecialchars(isset($_POST["eliminarUsuarioRestaurante"]))) {
    $param[0] = 2;
    $param[1] = htmlspecialchars($_POST["idRestaurante"]);
    $param[2] = htmlspecialchars($_POST["idUsuario"]);
    $param[3] = $idUsuario;
    
    print $cambioUsuarioCadena->administracionUsuarioRestaurante($param);
}
else if (htmlspecialchars(isset($_POST["eliminarTodosUsuarioRestaurantes"]))) {
    $param[0] = 3;
    $param[1] = 0;
    $param[2] = htmlspecialchars($_POST["idUsuario"]);
    $param[3] = $idUsuario;
    
    print $cambioUsuarioCadena->administracionUsuarioRestaurante($param);
}
