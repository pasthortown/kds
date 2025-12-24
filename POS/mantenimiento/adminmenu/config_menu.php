<?php

session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_menu.php';

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

$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];

$request = (object)(array_map('utf8_decode', $_POST));

//Cargar menús por cadena
if ($request->metodo === "cargarTodosMenus") {
    print $menu->cargarTodosMenus($idCadena);
//Cargar clasificaciones (canales de distribución)
} else if ($request->metodo === "cargarClasificaciones") {
    print $menu->cargarClasificaciones();
} else if ($request->metodo === "guardarMenu") {
    print $menu->guardarMenu(
        $request->accion
        , $idCadena
        , $idUsuario
        , $request->idMenu
        , $request->menu
        , $request->nombreMaxpoint
        , $request->idClasificacion        
        , $request->estado
        , $request->idMedio
    );
} else if ($request->metodo === "cargarMenusPorEstado") {
    print $menu->cargarMenusPorEstado($idCadena, $request->estado);
//Guardar menú duplicado    
}else if ($request->metodo === "fn_guardarduplicacion") {
    print $menu->fn_guardarduplicacion($idCadena, $idUsuario, $request->idMenuOriginal, $request->nombreMenuDuplicado, $request->nombreMenuMaxPoint, $request->idClasificacion, $request->estado);
}else if ($request->metodo === "cargarListaMedios") {
    print $menu->cargarListaMedios($idCadena);
}