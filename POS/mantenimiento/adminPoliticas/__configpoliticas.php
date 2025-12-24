<?php

//$request = json_decode(file_get_contents("php://input"));
//$request->metodo;
echo "Prueba";

/*
@session_start();

include_once("../../system/conexion/clase_sql.php");
include_once ("../../clases/clase_politicas.php");

$politicas = new Politicas();


$idCadena = $_SESSION['cadenaId'];
$idUser = $_SESSION['usuarioId'];

$request = json_decode(file_get_contents("php://input"));

if ($request->metodo==="cargarPoliticasPorCadena") {
    $parametro[0] = $idCadena;
    $parametro[1] = $request->coleccion;
    try {
        print $politicas->consultar($request->metodo, $parametro);
    } catch (Exception $e){
        print $e;
    }
}else if ($request->metodo==="cargarParametrosPolitica") {
    $parametro[0] = $request->idColeccion;
    $parametro[1] = $request->coleccion;
    try {
        print $politicas->consultar($request->metodo, $parametro);
    } catch (Exception $e){
        print $e;
    }
}else if ($request->metodo==="cargarModulosConfiguracion"){
    $parametro[0] = 0;
    try {
        print $politicas->consultar("cargarModulosConfiguracion", $parametro);
    } catch (Exception $e){
        print $e;
    }
//Modificar Politica
}else if ($request->metodo==="updatePolitica"){
    $parametro[0] = $idCadena;
    $parametro[1] = $idUser;
    $parametro[2] = $request->coleccion;
    $parametro[3] = $request->idColeccion;
    $parametro[4] = $request->descripcion;
    $parametro[5] = $request->idModulo;
    $parametro[6] = $request->configuracion;
    $parametro[7] = $request->reporte;
    $parametro[8] = $request->cubo;
    $parametro[9] = $request->repetirConfiguracion;
    $parametro[10] = $request->estado1;
    $parametro[11] = $request->estado2;
    $parametro[12] = $request->activo;
    $parametro[13] = $request->observaciones;
    try {
        print $politicas->consultar($request->metodo, $parametro);
    } catch (Exception $e){
        print $e;
    }
//Agregar Politica
}else if ($request->metodo==="createPolitica"){
    $parametro[0] = $idCadena;
    $parametro[1] = $idUser;
    $parametro[2] = $request->coleccion;
    $parametro[3] = $request->idColeccion;
    $parametro[4] = $request->descripcion;
    $parametro[5] = $request->idModulo;
    $parametro[6] = $request->configuracion;
    $parametro[7] = $request->reporte;
    $parametro[8] = $request->cubo;
    $parametro[9] = $request->repetirConfiguracion;
    $parametro[10] = $request->estado1;
    $parametro[11] = $request->estado2;
    $parametro[12] = $request->activo;
    $parametro[13] = $request->observaciones;
    try {
        print $politicas->consultar($request->metodo, $parametro);
    } catch (Exception $e){
        print $e;
    }
//Modificar Parametro Politica
}else if ($request->metodo==="updateParametro"){
    $parametro[0] = $idCadena;
    $parametro[1] = $idUser;
    $parametro[2] = $request->coleccion;
    $parametro[3] = $request->idColeccion;
    $parametro[4] = $request->idParametro;
    $parametro[5] = $request->descripcion;
    $parametro[6] = $request->especificarValor;
    $parametro[7] = $request->obligatorio;
    $parametro[8] = $request->tipoDato;
    $parametro[9] = $request->estado1;
    $parametro[10] = $request->estado2;
    $parametro[11] = $request->activo;
    try {
        print $politicas->consultar($request->metodo, $parametro);
    } catch (Exception $e){
        print $e;
    }
}else if ($request->metodo==="createParametro"){
    $parametro[0] = $idCadena;
    $parametro[1] = $idUser;
    $parametro[2] = $request->coleccion;
    $parametro[3] = $request->idColeccion;
    $parametro[4] = $request->idParametro;
    $parametro[5] = $request->descripcion;
    $parametro[6] = $request->especificarValor;
    $parametro[7] = $request->obligatorio;
    $parametro[8] = $request->tipoDato;
    $parametro[9] = $request->estado1;
    $parametro[10] = $request->estado2;
    $parametro[11] = $request->activo;
    try {
        print $politicas->consultar($request->metodo, $parametro);
    } catch (Exception $e){
        print $e;
    }
}
*/
?>