<?php

@session_start();

include("../../system/conexion/clase_sql.php");
include("../../clases/clase_adminMotorizado.php");

// Objects
$motorizado = new Motorizado();

// Data
$idRestaurante     = $_SESSION['rstId'];
$idCadena          = $_SESSION['cadenaId'];
$idUsuario         = $_SESSION['usuarioId'];
$idPais            = 1; //REVISAR DONDE EXTRAER EL PAIS

// REQUEST
$request = ( object ) filter_input_array( INPUT_POST );

// CARGAR MOTORIZADOS
if ( $request->metodo === "cargarMotorizados" ) {
    $estado = $request->estado;
    print json_encode($motorizado->cargarMotorizadosPorEstado( $idCadena, $idRestaurante, $estado ));
} else
// CREAR Y MODIFICAR MOTORIZADOS
if ( $request->metodo === "crearMotorizado" ) {
    $idMotorolo = $request->idMotorolo;
    $estado = $request->estado;
    $tipo = $request->tipo;
    $empresa = $request->empresa;
    $documento = $request->documento;
    $nombres = $request->nombres;
    $apellidos = $request->apellidos;
    $telefono = $request->telefono;
    $nomina = $request->nomina;
    $tipoIdentificacion = $request->tipoIdentificacion;
    $urlApi = $request->urlApi;
    $idCiudad = $request->idCiudad;
    $nombreTipoIdentificacion = $request->nombreTipoIdentificacion;
    $nombreCiudad = $request->nombreCiudad;

    print json_encode( $motorizado->guardarMotorizado( $idMotorolo, $estado, $tipo, $empresa, $documento, $nombres, $apellidos, $telefono, $tipoIdentificacion, $nomina, $idCiudad, $urlApi, $nombreTipoIdentificacion, $nombreCiudad ) );
} else
// CARGAR TIPOS MOTORIZADOS
if ( $request->metodo === "cargarTiposMotorizados" ) {
    print json_encode($motorizado->cargarTiposMotorizados( $idCadena ));
}else
// CARGAR EMPRESAS
if ( $request->metodo === "cargarEmpresas" ){
    $tipoEmpresa = $request->tipoEmpresa;
    print json_encode($motorizado->cargarEmpresas($idPais, $idCadena, $tipoEmpresa));
}else
// CARGAR TIPOS DOCUMENTOS
if ( $request->metodo === "cargarTiposDocumentos" ) {
    print json_encode($motorizado->cargarTiposDocumentos());
}
else
// BUSCAR MOTORIZADO
if ( $request->metodo === "buscarMotorizado" ) {
    $documento  = $request->documento;
    $estado     = $request->estado;
    $url        = $request->url;
    print json_encode($motorizado->buscarMotorizado($url, $documento, $estado));
}
else
// CARGA POLITICA DE URL API MOTORIZADO
if ( $request->metodo === "cargarUrlApiMotorizados" ) {
    print json_encode($motorizado->cargarUrlApiMotorizados($idRestaurante));
}
else
// CARGA UBICACION RESTAURANTE
if ( $request->metodo === "cargarUbicacionRestaurante" ) {
    print json_encode($motorizado->cargarUbicacionRestaurante( $idRestaurante ));
}
else
// CREAR ARCHIVO TOKEN
if ( $request->metodo === "validarTokenApiCliente" ) {
    print json_encode($motorizado->validarTokenApiCliente());
}
else
// CARGA CIERRE MOTORIZADO
if ( $request->metodo === "impresionDesasignacionMotorizado" ) {
    $id_motorizado  = $request->id_motorizado;
    $id_periodo     = $request->id_periodo;
    print $motorizado->impresionDesasignacionMotorizado($id_motorizado, $id_periodo);
}
