<?php
session_start();
/* 
 * Daniel Llerena
 * 31/07/2018
 * Colleción de datos usuarios
 */

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_adminColeccionUsuarios.php';

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(
        json_encode((object)[
            "estado" => "ERROR",
            "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
        ])
    );
}

$coleccionUsuarios = new ColeccionUsuarios();
$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];

if (htmlspecialchars(isset($_POST["detalleColeccionUsuarios"]))) {

    $idColeccionUsuario = 0;
    print $coleccionUsuarios->detalleColeccionUsuarios(1, $idCadena, $idColeccionUsuario, $idUsuario);
}
else if (htmlspecialchars(isset($_POST["detalleColeccionDeDatosUsuarios"]))) {

    $idColeccionUsuario = htmlspecialchars($_POST["idColeccionUsuarios"]);
    $idUsuarioAdmin = htmlspecialchars($_POST["idUsuario"]);
    print $coleccionUsuarios->detalleColeccionDeDatosUsuarios(2, $idCadena, $idColeccionUsuario, $idUsuarioAdmin);
}
else if (htmlspecialchars(isset ($_POST["guardarInformacionColeccion"]))) {
    
    $datos[0] = htmlspecialchars($_POST["accion"]);
    $datos[1] = htmlspecialchars($_POST["idColeccionDeDatosUsuarios"]);
    $datos[2] = htmlspecialchars($_POST["idColeccionUsuarios"]);
    $datos[3] = htmlspecialchars($_POST["idUsuario"]);
    $datos[4] = htmlspecialchars($_POST["tipo_varchar"]);
    $datos[5] = htmlspecialchars($_POST["tipo_entero"]);
    $datos[6] = htmlspecialchars($_POST["fecha"]);
    $datos[7] = htmlspecialchars($_POST["tipo_bit"]);
    $datos[8] = htmlspecialchars($_POST["tipo_numerico"]);
    $datos[9] = htmlspecialchars($_POST["fecha_inicio"]);
    $datos[10] = htmlspecialchars($_POST["fecha_fin"]);
    $datos[11] = htmlspecialchars($_POST["rango_minimo"]);
    $datos[12] = htmlspecialchars($_POST["rango_maximo"]);
    $datos[13] = htmlspecialchars($_POST["isActive"]);
    $datos[14] = $idUsuario;
    
    print $coleccionUsuarios->guardarUsuarioColeccionDeDatos($datos);
}
else if (htmlspecialchars(isset ($_POST["detalleUsuarioColeccionDeDatos"]))) {
    
    $idColeccionUsuario = 0;
    $idUsuarioAdmin = htmlspecialchars($_POST["idUsuario"]);
    print $coleccionUsuarios->detalleUsuarioColeccionDeDatos(3, $idCadena, $idColeccionUsuario, $idUsuarioAdmin);
    
}