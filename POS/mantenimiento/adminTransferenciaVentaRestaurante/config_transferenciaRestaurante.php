<?php

session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_transferenciaRestaurante.php';

$transferencia = new TransferenciaRestaurante();

$idCadena = $_SESSION['cadenaId'];
$usuario = $_SESSION['usuarioId'];

function specialChars($a) {
    return htmlspecialchars($a, ENT_QUOTES,'UTF-8');
}

$request = (object) (array_map('specialChars', $_POST));

//Cargar Configuración Transferencia de Venta de Cadena
if ($request->metodo === "cargarConfiguracionTransferenciaVentaCadena") {
    print $transferencia->cargarConfiguracionTransferenciaVentaCadena(0, $idCadena);
//
} else if($request->metodo === "cargarConfiguracionTransferenciaVentaRestaurante"){
    $idColeccion = $request->idColeccion;
    $idParametro = $request->idParametro;
    $estado = $request->estado ;
    print $transferencia->cargarConfiguracionTransferenciaVentaRestaurante(1, $idCadena, $idColeccion, $idParametro ,$estado);
//Cargar Locales Origen No Configurados de Transferencia de Venta
} else if($request->metodo === "cargarLocalesTransferenciaVentaCadenaSinConfiguracion"){
    $idColeccion = $request->idColeccion;
    $idParametro = $request->idParametro;
    print $transferencia->cargarLocalesTransferenciaVentaCadenaSinConfiguracion(2, $idCadena, $idColeccion, $idParametro);
//Cargar Locales Destino No Configurados de Transferencia de Venta
} else if($request->metodo === "cargarLocalesDestinoTransferenciaVentaCadenaSinConfiguracion"){
    $idCadenaDestino = $request->idCadenaDestino;
    $idColeccion = $request->idColeccion;
    $idParametro = $request->idParametro;
    $accion = $request->accion;
    print $transferencia->cargarLocalesTransferenciaVentaCadenaSinConfiguracion($accion, $idCadenaDestino, $idColeccion, $idParametro);
//Configurar Transferencia Venta Restaurante
} else if($request->metodo === "configurarTransferenciaVentaRestaurante"){
    $accion = $request->accion;
    $idColeccionCadena = $request->idColeccionCadena;
    $idParametroCadena = $request->idParametroCadena;
    $idColeccionRestaurante = $request->idColeccionRestaurante;
    $idParametroRestaurante = $request->idParametroRestaurante;
    $origen = $request->origen;
    $origenBD = $request->origenBD;
    $destino = $request->destino;
    $destinoBD = $request->destinoBD;
    $estadoColeccion = $request->estadoColecc ;
    print $transferencia->modificarConfiguracionTransferenciaVentaRestaurante($accion, $idCadena, $idColeccionCadena, $idParametroCadena, $idColeccionRestaurante, $idParametroRestaurante, $origen, $origenBD, $destino, $destinoBD, $usuario, $estadoColeccion);
}
else if ($request->metodo === "inactivarTransferenciaVentas"  ){
   
   
    print $transferencia->modificarConfiguracionTransferenciaVentaRestaurante($accion, $idCadena, $idColeccionCadena, $idParametroCadena, $idColeccionRestaurante, $idParametroRestaurante, $origen, $origenBD, $destino, $destinoBD, $usuario, $isActive);

//    $lc_condiciones[0] = htmlspecialchars($_POST['accion']);
//    $lc_condiciones[1] = '0';
//    $lc_condiciones[2] = '0';
//    $lc_condiciones[3] = $lc_usuario;
//    $lc_condiciones[4] = htmlspecialchars($_POST['ID_ColeccionCadena']);
//    $lc_condiciones[5] = htmlspecialchars($_POST['ID_ID_ColeccionDeDatosCadena']);
//    $lc_condiciones[6] = htmlspecialchars($_POST['estado']);
//    print $lc_adminCadena->fn_inactivarTransferencia($lc_condiciones);  
}

