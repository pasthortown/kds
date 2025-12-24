<?php
require_once "../parametros.php";
include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_webservice.php";
include_once '../clases/clase_clientes.php';

use Maxpoint\Mantenimiento\adminReplicacion\Clases\ConexionDinamica;
use Maxpoint\Mantenimiento\promociones\Clases\PromocionesController;
use Carbon\Carbon;

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

$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];
$rstId = $_SESSION["rstId"];

$request = (object)(array_map('utf8_decode', $_POST));
$conexionDinamica = new ConexionDinamica();
$conexionTienda = $conexionDinamica->conexionTienda();
$promocionesControllerObj = new PromocionesController($conexionTienda);

//Variables recibidas por POST
$idCabeceraFactura = isset($request->cfac_id)?$request->cfac_id:null ;
$idOrdenPedido = isset($request->odp_id)?$request->odp_id:null ;
$dop_cuenta = isset($request->dop_cuenta)?$request->dop_cuenta:1 ;
$token = $request->cod_promo;
$cantidadCanjes = $request->cantidadCanjes;

if (isset($request->canjearPromocionTrade) && (1 == $request->canjearPromocionTrade)) {
    $resultado = array(
        "estadoPromocion"=>"ERROR",
        "mensajePromocion"=>"No se pudo canjear la promoción, error interno",
        "estadoCanje"=>"ERROR",
        "mensajeCanje"=>"No se realiza el canje"
    );

    //Consultar servicioswebsir procesar canje
    $canjeCupon = generarCanjeCupon($_SESSION["rstId"], $_SESSION['cadenaId'], $token, $cantidadCanjes, $idOrdenPedido);
    if($canjeCupon->success == false){
        $resultado['mensajePromocion'] = $canjeCupon->message;
        enviarRespuestaJson($resultado);
    }

    $datosPromocion = $canjeCupon->data->datosPromocion[0];
    $persona = $canjeCupon->data->persona->cliente;

    $beneficiosPromocion =[];
    foreach ($canjeCupon->data->beneficiosPromociones as $key => $value) {
        $beneficiosPromocion[] = [
            'Id_Promociones'            => $value[0]->Id_Promociones,
            'IDDescuentos'              => ($value[0]->IDDescuentos) ? $value[0]->IDDescuentos : '',
            'Plu_id'                    => $value[0]->Plu_id,
            'Cantidad_plu'              => $value[0]->Cantidad_plu,
            'Tipo_aplica'               => $value[0]->Tipo_aplica,
            'Id_BeneficiosPromociones'  => $value[0]->Id_BeneficiosPromociones,
            'Modo_factura'              => ($value[0]->Modo_factura) ? $value[0]->Modo_factura : '',
        ];
    }

    $parametrosPromocion = [
        'Requiere_productos'                => $datosPromocion->Requiere_productos,
        'Requiere_forma_Pago'               => $datosPromocion->Requiere_forma_Pago,
        'Bruto_minimo_factura'              => $datosPromocion->Bruto_minimo_factura,
        'Bruto_maximo_factura'              => $datosPromocion->Bruto_maximo_factura,
        'Cantidad_minima_productos_factura' => $datosPromocion->Cantidad_minima_productos_factura,
        'Permite_otras_promociones'         => $datosPromocion->Permite_otras_promociones,
        'Requiere_canal'                    => $datosPromocion->Requiere_canal,
        'Maximo_canje_multiple'             => $datosPromocion->Maximo_canje_multiple,
        'Permite_descuento_sobre_descuento' => $datosPromocion->Permite_descuento_sobre_descuento,
        'PromocionesOrdenPedido'            => $beneficiosPromocion,
        'PersonaIdentificacion' => $persona->identificacion
    ];

    $validacionesOrdenFacturaPromocion = $promocionesControllerObj->validacionesOrdenFacturaPromocion($datosPromocion->Id_Promociones, $idOrdenPedido, $idCabeceraFactura, $dop_cuenta, $parametrosPromocion);
    if($validacionesOrdenFacturaPromocion["estado"] == 0)
    {
        inactivarMasterData($promocionesControllerObj, $canjeCupon->data->id);

        $resultado['mensajePromocion'] = $validacionesOrdenFacturaPromocion["mensaje"];
        enviarRespuestaJson($resultado);
    }

    $insercionCanje = $promocionesControllerObj->ejecutarCanje(
        $datosPromocion->Id_Promociones,
        $datosPromocion->Codigo_amigable,
        '',
        buscarClienteLocalmente((object)$persona),
        $cantidadCanjes,
        $idUsuario,
        $idOrdenPedido,
        $dop_cuenta,
        $canjeCupon->data->id,
        $datosPromocion->Nombre,
        end($beneficiosPromocion)
    );

    if($insercionCanje["estado"]!==1){
        inactivarMasterData($promocionesControllerObj, $canjeCupon->data->id);

        $resultado["mensajeCanje"]      ="Error al registrar la promoción, vuelva a intentarlo.";
        $resultado["mensajePromocion"]  ="Error al registrar la promoción, vuelva a intentarlo.";
        enviarRespuestaJson($resultado);
    }

    //Enviar Resultados
    $resultado = array(
        "Id_Promociones"        => $datosPromocion->Id_Promociones,
        "estadoPromocion"       => "OK",
        "mensajePromocion"      =>"Canjeado Correctamente",
        "estadoCanje"           =>"OK",
        "mensajeCanje"          =>"Canjeado Correctamente",
        "beneficioPromocion"    => $beneficiosPromocion
    );

    enviarRespuestaJson($resultado);
}

function iaClientePromocionesTrade($datosCliente){
    $clienteObj = new Cliente();
    $resultadoInsercionCliente = $clienteObj->fn_registrarClienteWS(
        'I', 'W',
        $datosCliente->tipoIdentificacion,
        $datosCliente->identificacion,
        $datosCliente->descripcion,
        $datosCliente->direccionDomicilio,
        $datosCliente->celular,
        $datosCliente->correo,
        $_SESSION['usuarioId'], 1,
        '');

    $idCliente = ($resultadoInsercionCliente["str"]!==1)?false:$resultadoInsercionCliente["IDCliente"];
    return $idCliente;
}

function generarCanjeCupon($idRestaurante, $idCadena, $token, $cantidadCanjes, $ordenPedido){
    $servicioWebObj = new webservice();

    $data_string = json_encode([
        'idrestaurante'         => $idRestaurante,
        'idcadena'              => $idCadena,
        'token'                 => $token,
        'cantidadCanjes'        => $cantidadCanjes,
        'cabeceraOrdenPedido'   => $ordenPedido
    ]);

    $datosWebservice = $servicioWebObj->retorna_WS_Cupones_Canje($idRestaurante);
    $urlEnvioCanjeWS = $datosWebservice["urlwebservice"];
    
    $url = trim($urlEnvioCanjeWS);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 40);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 40);
    //execute post
    $result = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //close connection
    curl_close($ch);

    return json_decode($result);
}

function buscarClienteLocalmente($datosClienteBuscar){
    $clienteObj = new Cliente();
    // Buscar cliente localmente
    $datosCliente = $clienteObj->fn_buscarCliente($datosClienteBuscar->identificacion, 0, $datosClienteBuscar->tipoIdentificacion);

    if ($datosCliente["str"] == 0) {
        // No existe el cliente en el local, insertarlo
        $idCliente = iaClientePromocionesTrade($datosClienteBuscar);
    }else{
        //Si existe el cliente, retornar el ID
        $idCliente = $datosCliente["IDCliente"];
    }
    return $idCliente;
}

function enviarRespuestaJson($resultadoFinal){
    header('Content-Type: application/json');
    print(json_encode($resultadoFinal));
    die();
}

function inactivarMasterData($promocionesControllerObj, $codigo){
    $inactivarMasterData = $promocionesControllerObj->consumirWSInactivarCanjeMasterData(['CanjesInactivar' => ['IDCanjeMasterData' => $codigo]]);
    if($inactivarMasterData->estado=='ERROR') {
        enviarRespuestaJson($inactivarMasterData->mensaje);
    }

    return;
}