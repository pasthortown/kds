<?php

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_adminmodulos.php";

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$modulo = new Modulo();

$request = (object) ($_POST);
$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];

//Cargar todos los módulos
if ($request->metodo === "cargarModulos") {
    print $modulo->cargarModulos();
//Guardar nuevo módulo
} else if ($request->metodo === "guardarModulo") {
    print $modulo->guardarModulo($request->accion, $request->idModulo, $request->descripcion, $request->abreviatura, $request->nivel, $request->estado, $idCadena, $idUsuario);
//Cargar todos los estados
} else if ($request->metodo === "cargarEstados") {
    print $modulo->cargarEstados($request->idModulo, $idCadena);
//Guardar nuevo estado
} else if ($request->metodo === "guardarEstado") {
    print $modulo->guardarEstado($request->accion, $request->idEstado, $request->descripcion, $request->factor, $request->nivel, $request->idModulo, $idCadena, $idUsuario);
//Cargar factores multiplicadores de estado
} else if ($request->metodo === "cargarFactorMultiplicador") {
    print $modulo->cargarFactorMultiplicador($idCadena);
}