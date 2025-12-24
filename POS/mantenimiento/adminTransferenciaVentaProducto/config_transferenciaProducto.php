<?php

session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_transferenciaProducto.php';

$transferencia = new TransferenciaProducto();

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
} else if($request->metodo === "cargarConfiguracionTransferenciaVentaProducto"){
    $idColeccion = $request->idColeccion;
    $idParametro = $request->idParametro;
    $estado = $request->estado ;
    print $transferencia->cargarConfiguracionTransferenciaVentaProducto(1, $idCadena, $idColeccion, $idParametro ,$estado);
//Cargar Locales Origen No Configurados de Transferencia de Venta
} else if($request->metodo === "cargarProductoCadenaOrigen"){
    $idColeccion = $request->idColeccion;
    $idParametro = $request->idParametro;
    $opcion = $request->tipo ; // 2 origen, 3 Destino
    $DestinoIncluir = $request ->DestinoIncluir ;
    
    print $transferencia->cargarProductoCadenaOrigenDestino($opcion, $idCadena, $idColeccion, $idParametro,$DestinoIncluir);
//Cargar Locales Destino No Configurados de Transferencia de Venta
} else if($request->metodo === "cargarLocalesDestinoTransferenciaVentaCadenaSinConfiguracion"){
    $idCadenaDestino = $request->idCadenaDestino;
    $idColeccion = $request->idColeccion;
    $idParametro = $request->idParametro;
    $accion = $request->accion;
    print $transferencia->cargarLocalesTransferenciaVentaCadenaSinConfiguracion($accion, $idCadenaDestino, $idColeccion, $idParametro);
//Configurar Transferencia Venta Producto
} else if($request->metodo === "configurarTransferenciaVentaProducto"){
    $accion = $request->accion;
    $idParametroIntegracion = $request->idParametroCadena;
    $origen = $request->origen; 
    $destino = $request->destino;
    $cdn_id=$idCadena; 
    $estadoColeccion = $request->estadoColecc ;   
    print $transferencia->GuardarTransferenciaVentaProducto($accion, $cdn_id , $idParametroIntegracion, $usuario, $origen, $destino, 1);
}
 else if($request->metodo === "ActualizarTransferenciaVentaProducto"){
    $accion = $request->accion;
    $idParametroIntegracion = $request->idParametroCadena;
    $origen = $request->origen;
    $destino = $request->destino;
    $estadoColeccion = $request->estadoColecc ;   
    $oldDestino = $request->oldDestino ;
     $cdn_id=$idCadena; 
    print $transferencia->ActualizarTransferenciaVentaProducto($accion, $cdn_id, $idParametroIntegracion, $usuario, $origen, $destino, $estadoColeccion, $oldDestino);
}
 else if($request->metodo === "cargarorigendestino"){
    $accion = $request->accion;
    $ID_ColeccionCadena = $request->coleccioncadena;
    $ID_ColeccionDeDatosCadena = $request->colecciondedatoscadena;
    $cdn_id = $idCadena;
    $estado= $request->estadoColecc ;   
     print $transferencia->cargarInformacionProductoCadenaOrigenDestino($accion, $cdn_id, $ID_ColeccionCadena, $ID_ColeccionDeDatosCadena, $estado);
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

