<?php
session_start();

include_once"../../system/conexion/clase_sql.php";
include_once"../../clases/clase_politicas.php";

$politicas = new Politicas();

$idCadena = $_SESSION['cadenaId'];
$idUser = $_SESSION['usuarioId'];
/*$contenido = file_get_contents("php://input"); 
$codificado = utf8_encode($contenido);*/
$request = json_decode(file_get_contents("php://input"));
/*
$request = json_encode($_POST);
$request = json_decode($request);
 * */
 
//print_r($request);
//$request = json_decode($request); 

//Cargar Politicas por Cadena
if ($request->metodo==="cargarPoliticasPorCadena") {
    
    $parametro[0] = $idCadena;
    $parametro[1] = $request->coleccion;
    
    print $politicas->consultar("cargarPoliticasPorCadena", $parametro);
//Cargar Parametros de Politica
}else if ($request->metodo==="cargarParametrosPolitica") {
    $parametro[0] = $request->idColeccion;
    $parametro[1] = $request->coleccion;
    $parametro[2] = $idCadena;
    print $politicas->consultar($request->metodo, $parametro);
//Cargar Modulos de Configuracion
}else if ($request->metodo==="cargarModulosConfiguracion"){
    $parametro[0] = 0;
    print $politicas->consultar("cargarModulosConfiguracion", $parametro);
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
    $parametro[14] = $request->descripcionIntegracion;
    $parametro[15] = $request->idIntegracion;
    print $politicas->consultar($request->metodo, $parametro);
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
    $parametro[14] = $request->descripcionIntegracion;
    $parametro[15] = $request->idIntegracion;
    print $politicas->consultar($request->metodo, $parametro);
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
    $parametro[12] = $request->descripcionIntegracion;
    $parametro[13] = $request->idIntegracion;
    print $politicas->consultar($request->metodo, $parametro);
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
    $parametro[12] = $request->descripcionIntegracion;
    $parametro[13] = $request->idIntegracion;
    print $politicas->consultar($request->metodo, $parametro);
//Tablas Integracion
}else if ($request->metodo==="cargarTablasIntegracion"){
    $parametro[0] = 1;
    print $politicas->consultar("cargarTablasIntegracion", $parametro);
//Registros Tablas Integracion
}else if ($request->metodo==="cargarIdTablasIntegracion"){
    $parametro[0] = $request->tabla;
    print $politicas->consultar("cargarIdTablasIntegracion", $parametro);
//Tipos de Datos Parametros
}else if ($request->metodo==="cargarTiposDatosParametros"){
    $parametro[0] = 2;
    $parametro[1] = $idCadena;
    print $politicas->consultar("cargarTiposDatosParametros", $parametro);
}
    
?>