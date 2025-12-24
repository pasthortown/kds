<?php

require_once "parametros.php";

use Maxpoint\Mantenimiento\adminReplicacion\Clases\ConexionDinamica;
use Maxpoint\Mantenimiento\promociones\Clases\PromocionesController;
use Maxpoint\Mantenimiento\promociones\Clases\TipoCuponController;
use Carbon\Carbon;

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}
$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];
$request = (object) (array_map('utf8_decode', $_POST));
$requestGET = (object)(array_map('utf8_decode', $_GET));

$conexionDinamica = new ConexionDinamica();
$conexionTienda = $conexionDinamica->conexionTienda();
$promocionesControllerObj = new PromocionesController($conexionTienda);
$tipoCuponControllerObj = new TipoCuponController($conexionTienda);

if (isset($requestGET->cargarListadoPromociones) && (1 == $requestGET->cargarListadoPromociones)) {
    $resultado = $promocionesControllerObj->cargarListadoPromociones($idCadena);
    enviarRespuestaJson($resultado);
}

if (isset($request->guardarPromocion) && (1 == $request->guardarPromocion)) {
    $resultadoGuardar = $promocionesControllerObj->guardarPromocion(
        $request->Id_Promociones,
        $request->cdn_id,
        $request->Codigo_externo,
        $request->Nombre,
        $request->Nombre_imprimible,
        $request->Codigo_amigable,
        $request->Limite_canjes_total,
        $request->Limite_canjes_cliente,
        $request->Total_canjeados,
        $request->Caduca_con_tiempo,
        $request->Unidad_Tiempo_validez,
        $request->Tiempo_validez,
        $request->Activo_desde,
        $request->Activo_Hasta,
        $request->Requiere_productos,
        $request->Requiere_forma_Pago,
        $request->Puntos_Acumulables,
        $request->Saldo_Acumulable,
        $request->Bruto_minimo_factura,
        $request->Bruto_maximo_factura,
        $request->Cantidad_minima_productos_factura,
        $request->Permite_otras_promociones,
        $request->Maximo_canje_multiple,
        $request->Requiere_dias,
        $request->Dias_canjeable,
        $request->Requiere_horario,
        $request->Horario_canjeable,
        $request->Requiere_rango_edad,
        $request->Rango_edad,
        $request->Requiere_genero,
        $request->genero,
        $request->Tiene_codigo_unico,
        $request->Activo,
        $request->Motivo_inactivacion,
        $request->Permite_descuento_sobre_descuento,
        $request->Concatenador_beneficios,
        $request->Concatenador_plus_promocion,
        $request->Requiere_canal,
        $request->Requiere_restaurante,
        $request->LastUser,
        $request->Beneficios_Promocion,
        $request->Productos_Requeridos_Promocion,
        $request->Forma_Pago_Promocion,
        $request->Restaurantes_Requeridos_Promocion,
        $request->Canales_Requeridos_Promocion,
        $request->IDColeccionPromociones,
        $request->categoriasSeleccionadasPromocion
    );

    if ($resultadoGuardar["estado"] !== 1) {
        enviarRespuestaJson($resultadoGuardar);
}

    $idPromocion = $resultadoGuardar["datos"][0]["idPromocion"];

    //Se eliminan los registros configurados actualmente
    $parametros = [
        "idpromocion" => $idPromocion,
        "idcadena" => $idCadena,
    ];

    if(isset($request->Id_Promociones) && !empty($request->Id_Promociones)) {
        $tipoCuponControllerObj->eliminarTiposCupon($parametros);
}

    // Buscar los nombres de las políticas que se quiere guardar  --OK??

    $tiposCuponActual = $tipoCuponControllerObj->retornarTiposCupon($request);

    // Con los nombres, encontrar los Ids de los parametros en la tabla ColeccionDeDatosCadena
    $parametros = [
        "idcadena" => $idCadena,
        "nombrestipos" => implode(",", $tiposCuponActual),
    ];

    $consultaTiposCuponActualBDD = $tipoCuponControllerObj->retornarIdTiposCupon($parametros);

    if ($consultaTiposCuponActualBDD["estado"] !== 1) {
        enviarRespuestaJson($consultaTiposCuponActualBDD);
    }

    $tiposCuponActualBDD = $consultaTiposCuponActualBDD["datos"];
        // Para cada ID de parámetro encontrado, insertar un registro en las tablas de politicas de promociones
    foreach ($tiposCuponActualBDD as $tipoCupon) {

        $parametros = [
            "idpromociones" => $idPromocion,
            "idcadena" => $idCadena,
            "descripcion" => $tipoCupon["Descripcion"],
            "idusuario" => $idUsuario,
            "ID_ColeccionDeDatosCadena" => $tipoCupon["ID_ColeccionDeDatosCadena"],
        ];

        $resultado = $tipoCuponControllerObj->insertarTipoCuponPromocion($parametros);
    }

    enviarRespuestaJson($resultadoGuardar);
}

if (isset($request->guardarUrlServidorTrade) && (1 == $request->guardarUrlServidorTrade)) {
    $resultado = $promocionesControllerObj->guardarUrlServidorTrade($idCadena, $request->urlServidor, $idUsuario);
    enviarRespuestaJson($resultado);
}

if (isset($request->guardarRutaEndpointCanjeTrade) && (1 == $request->guardarRutaEndpointCanjeTrade)) {
    $resultado = $promocionesControllerObj->guardarRutaEndpointCanjeTrade($idCadena, $request->rutaEndpoint,
        $idUsuario);
    enviarRespuestaJson($resultado);
}

if (isset($request->guardarClaveJWTTrade) && (1 == $request->guardarClaveJWTTrade)) {
    $resultado = $promocionesControllerObj->guardarClaveJWTTrade($idCadena, $request->claveJWT, $idUsuario);
    enviarRespuestaJson($resultado);
}

if (isset($request->cargarDetallesCupon) && (1 == $request->cargarDetallesCupon)) {
    $resultado = $promocionesControllerObj->cargarDetallesCupon($idCadena, $request->idCupon);
    enviarRespuestaJson($resultado);
}


if (isset($request->guardarTipoCupon) && (1 == $request->guardarTipoCupon)) {
    $resultado = $tipoCuponControllerObj->guardarTipoCupon($request, $idCadena, $idUsuario);
    enviarRespuestaJson($resultado);
}

if (isset($request->retornaRestaurantesNoSeleccionadosCiudades) && (1 == $request->retornaRestaurantesNoSeleccionadosCiudades)) {

    $resultado = $tipoCuponControllerObj->retornaRestaurantesNoSeleccionadosCiudades($idCadena,$ciudades, $idcupon);
    enviarRespuestaJson($resultado);
}
if (isset($request->retornaRestaurantesNoSeleccionadosRegiones) && (1 == $request->retornaRestaurantesNoSeleccionadosRegiones)) {
    $regiones=[];
    $restaurantes=[];
    if(isset($_REQUEST["regiones"])){
        $regiones=$_REQUEST["regiones"];
    }
    if(isset($_REQUEST["restaurantes"])){
        $restaurantes=$_REQUEST["restaurantes"];
    }

    $resultado = $tipoCuponControllerObj->retornaRestaurantesNoSeleccionadosRegiones($idCadena, $regiones, $restaurantes );
    if($resultado["estado"]!=1){
        enviarRespuestaJson($resultado);
    }

    //$regiones = array_unique(array_column($resultado["datos"],"rst_localizacion"));
    $restaurantesPorRegion=[];
    foreach ($resultado["datos"] as $restaurante){
        $restaurantesPorRegion[$restaurante["rst_localizacion"]]["localizacion"]=$restaurante["rst_localizacion"];
        $restaurantesPorRegion[$restaurante["rst_localizacion"]]["restaurantes"][]=$restaurante;
    }
    enviarRespuestaJson([
            "estado"=>1,
            "datos"=>$restaurantesPorRegion
        ]);
}


function enviarRespuestaJson($resultadoFinal)
{
    header('Content-Type: application/json');
    print(json_encode($resultadoFinal));
    die();
}

// TODO: Insertar/Actualizar Política de Servidor de Webservices Trade
function insertarActualizarServidorTrade()
{

}

// TODO: Insertar/Actualizar política de Endpoint Trade Datos Clientes
function insertarActualizarEndPointTradeDatosCliente()
{
}

// TODO: Insertar/Actualizar política de Endpoint Trade Aviso de canje
function insertarActualizarEndPointTradeAvisoCanje()
{
}

// TODO: Insertar/Actualizar Política de Servidor de Webservices Validaciones Azure
function insertarActualizarServidorValidacionesAzure()
{

}