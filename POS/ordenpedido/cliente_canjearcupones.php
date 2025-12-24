<?php

session_start();
include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_ordenPedido.php";
include_once "../clases/clase_webservice.php";
include_once "../tokens/MainApiToken.php";
$servicioWebObj = new webservice();
function specialChars($a)
{
    return htmlspecialchars($a, ENT_QUOTES, 'UTF-8');
}

$ordenpedido = new menuPedido();

$cadena = $_SESSION['cadenaId'];
$ip = $_SESSION['direccionIp'];
$usuario = $_SESSION['usuarioId'];
$estacion = $_SESSION['estacionId'];
$tipo_servicio = $_SESSION['TipoServicio'];
$nombre_usuario = $_SESSION['nombre'];
$perfil = $_SESSION['perfil'];
$control = $_SESSION['IDControlEstacion'];
$tienda = $_SESSION['rstId'];

$request = (object) (array_map('specialChars', $_POST));

//Canjear Cupon Proceso Automatico
if ($request->metodo === "canjearCuponesAutomatico" || $request->metodo == "canjearCuponesAutomaticoCuponesDescuento") {

    $dataSolicitud = array(
        "codCadena" => $cadena,
        "codRestaurante" => $tienda,
        "cupon" => $request->cupon,
        "incrementa" => 0,
        "solicitud" => "",
        "codigofinal" => "",
        "detalle" => "",
        "tipo" => 0
    );

    $datosWebservice = $servicioWebObj->retorna_WS_Cupones_CanjearAutomatico($tienda);
    $url = $datosWebservice["urlwebservice"];
    consumirMetodoDeSirParaCuponesPrepagados($cadena, $dataSolicitud, $url, $request, $usuario, $control, $tienda, $nombre_usuario, $estacion, $ordenpedido);
    //$url = "http://azsoa.cloudapp.net:7380/GerenteNacional.ServiciosWeb/webresources/cupones/canjear/automatico/";
    //Canjear Cupon Proceso Manual
} else if ($request->metodo === "canjearCuponesManual" || $request->metodo == "canjearCuponesManualCuponesDescuento") {
    $dataSolicitud = array(
        "codCadena" => $cadena,
        "codRestaurante" => $tienda,
        "cupon" => "",
        "incrementa" => $request->incremental,
        "solicitud" => $request->solicitud,
        "codigofinal" => $request->cupon,
        "detalle" => $request->text_for,
        "tipo" => 0
    );
    $datosWebservice = $servicioWebObj->retorna_WS_Cupones_CanjearManual($tienda);
    $url = $datosWebservice["urlwebservice"];
    consumirMetodoDeSirParaCuponesPrepagados($cadena, $dataSolicitud, $url, $request, $usuario, $control, $tienda, $nombre_usuario, $estacion, $ordenpedido);
    // Consultar cupon empresarial digital.
} else if ($request->metodo === "obtenerEstadoCupon") {
    $dataSolicitud = array(
        "id" => $request->cupon,
        "cod_restaurante" => $tienda,
        "cajero" => $nombre_usuario
    );
    $datosWebservice = $servicioWebObj->retorna_WS_Cupones_Digitales_ObtenerEstado($tienda);
    $url = $datosWebservice["urlwebservice"];
    obtenerEstadoCuponSirIntegration($cadena, $dataSolicitud, $url, $request, $usuario, $control, $tienda, $nombre_usuario, $estacion, $ordenpedido);
} else if ($request->metodo === "actualizarEstadoCupon") {
    $dataSolicitud = array(
        "id" => $request->cupon,
        "cod_restaurante" => $tienda,
        "cajero" => $nombre_usuario,
        "status" => $request->status
    );
    $datosWebservice = $servicioWebObj->retorna_WS_Cupones_Digitales_ActualizarEstado($tienda);
    $url = $datosWebservice["urlwebservice"];
    actualizarEstadoCuponSirIntegration($cadena, $dataSolicitud, $url);
}

// Metodo que consulta los cupones prepagados para ser canjeados.
function consumirMetodoDeSirParaCuponesPrepagados($cadena, $dataSolicitud, $url, $request, $usuario, $control, $tienda, $nombre_usuario, $estacion, $ordenpedido)
{
    try {
        $token = apiTokenIntegracion($cadena, 'TokenOrdenPedido');
        $tokenType = trim(apiTokenIntegracion($cadena, 'TokenTypeOrdenPedido'));
        $tokenHeader = "Authorization: " . $tokenType . " " . $token;

        //Convert Data tipo object a json
        $dataString = json_encode($dataSolicitud);

        //Consumo WebServices
        $solicitud = curl_init($url);
        curl_setopt($solicitud, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($solicitud, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($solicitud, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($solicitud, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($solicitud, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader));
        curl_setopt($solicitud, CURLOPT_TIMEOUT, 30);
        curl_setopt($solicitud, CURLOPT_CONNECTTIMEOUT, 30);

        $respuestaSolicitud = curl_exec($solicitud);
        $estado = curl_getinfo($solicitud);
        $respuesta = json_decode($respuestaSolicitud);

        curl_close($solicitud);

        if ($estado['http_code'] != 200) {

            apiTokenIntegracion($cadena, 'CrearToken');

            $token = apiTokenIntegracion($cadena, 'TokenOrdenPedido');
            $tokenType = trim(apiTokenIntegracion($cadena, 'TokenTypeOrdenPedido'));
            $tokenHeader = "Authorization: " . $tokenType . " " . $token;

            //Convert Data tipo object a json
            $dataString = json_encode($dataSolicitud);

            //Consumo WebServices
            $solicitud = curl_init($url);
            curl_setopt($solicitud, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($solicitud, CURLOPT_POSTFIELDS, $dataString);
            curl_setopt($solicitud, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($solicitud, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($solicitud, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader));
            curl_setopt($solicitud, CURLOPT_TIMEOUT, 30);
            curl_setopt($solicitud, CURLOPT_CONNECTTIMEOUT, 30);

            $respuestaSolicitud = curl_exec($solicitud);

            $respuesta = json_decode($respuestaSolicitud);

            curl_close($solicitud);
        }

        if ($respuesta->estado == 1 && $respuesta->retorno == 1) {
            if ($request->metodo === "canjearCuponesAutomatico" || $request->metodo === "canjearCuponesManual") {
                $lc_condiciones[0] = $respuesta->cupon;
                $lc_condiciones[1] = $usuario;
                $lc_condiciones[2] = $control;
                $lc_condiciones[3] = $tienda;
                $lc_condiciones[4] = $nombre_usuario;
                $lc_condiciones[5] = $respuesta->respuesta;
                $lc_condiciones[6] = $respuesta->retorno;
                $lc_condiciones[7] = 1;
                $lc_condiciones[8] = $respuesta->precioBruto;
                $lc_condiciones[9] = $respuesta->precioNeto;
                $lc_condiciones[10] = $respuesta->iva;
                $lc_condiciones[11] = $respuesta->tipoCupon;
                $lc_condiciones[12] = $respuesta->codTipoCupon;
                $lc_condiciones[13] = $estacion;
                $detalle = obtenerDetalleCupon($respuesta->detalle);
                $lc_condiciones[14] = $detalle["codPlu"];
                $lc_condiciones[15] = $detalle["descripcion_plu"];
                $lc_condiciones[16] = $detalle["cantidad"];
                $lc_condiciones[17] = $detalle["iva"];
                $lc_condiciones[18] = $detalle["precioBruto"];
                $lc_condiciones[19] = $detalle["precioNeto"];
                $lc_condiciones[20] = $request->apiImpresionAplica;
                $lc_condiciones[21] = "";
                $lc_condiciones[22] = 0;
                $ordenpedido->fn_consultar("impresionDetalleCuponOrdenPedido", $lc_condiciones);
            } elseif ($request->metodo == "canjearCuponesAutomaticoCuponesDescuento" || $request->metodo == "canjearCuponesManualCuponesDescuento") {
                $salida = $ordenpedido->iAuditoriaTransaccionCuponDescuento($respuestaSolicitud, $usuario, $request->IDCabeceraOrdenPedido, $request->pluIdCuponDescuento);
            }
            print $respuestaSolicitud;
        } else {
            print $respuestaSolicitud;
        }
    } catch (Exception $e) {
        print json_encode($e);
    }
}

function obtenerEstadoCuponSirIntegration($cadena, $dataSolicitud, $url, $request, $usuario, $control, $tienda, $nombre_usuario, $estacion, $ordenpedido)
{
    try {
        $token = apiTokenIntegracion($cadena, 'TokenOrdenPedido');
        $tokenType = trim(apiTokenIntegracion($cadena, 'TokenTypeOrdenPedido'));
        $tokenHeader = "Authorization: " . $tokenType . " " . $token;

        //Convert Data tipo object a json
        $dataString = json_encode($dataSolicitud);

        //Consumo WebServices
        $solicitud = curl_init($url);
        curl_setopt($solicitud, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($solicitud, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($solicitud, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($solicitud, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($solicitud, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader));
        curl_setopt($solicitud, CURLOPT_TIMEOUT, 30);
        curl_setopt($solicitud, CURLOPT_CONNECTTIMEOUT, 30);

        $respuestaSolicitud = curl_exec($solicitud);
        $estado = curl_getinfo($solicitud);
        $respuesta = json_decode($respuestaSolicitud);
        curl_close($solicitud);

        if ($estado['http_code'] != 200) {
            apiTokenIntegracion($cadena, 'CrearToken');

            $token = apiTokenIntegracion($cadena, 'TokenOrdenPedido');
            $tokenType = trim(apiTokenIntegracion($cadena, 'TokenTypeOrdenPedido'));
            $tokenHeader = "Authorization: " . $tokenType . " " . $token;

            //Convert Data tipo object a json
            $dataString = json_encode($dataSolicitud);

            //Consumo WebServices
            $solicitud = curl_init($url);
            curl_setopt($solicitud, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($solicitud, CURLOPT_POSTFIELDS, $dataString);
            curl_setopt($solicitud, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($solicitud, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($solicitud, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader));
            curl_setopt($solicitud, CURLOPT_TIMEOUT, 30);
            curl_setopt($solicitud, CURLOPT_CONNECTTIMEOUT, 30);

            $respuestaSolicitud = curl_exec($solicitud);
            $respuesta = json_decode($respuestaSolicitud);
            curl_close($solicitud);
        }

        if ($respuesta->response) {
            $lc_condiciones[0] = $request->cupon;
            $lc_condiciones[1] = $usuario;
            $lc_condiciones[2] = $control;
            $lc_condiciones[3] = $tienda;
            $lc_condiciones[4] = $nombre_usuario;
            $lc_condiciones[5] = "Cupon Canjeado Exitosamente";
            $lc_condiciones[6] = 1;
            $lc_condiciones[7] = 1;
            $resultado = sumatoriaDeValores($respuesta->data->detail);
            $lc_condiciones[8] = $resultado['sumatoriaBruto'];
            $lc_condiciones[9] = $resultado['sumatoriaNeto'];
            $lc_condiciones[10] = $resultado['sumatoriaIVA'];
            $descripcionTipo = obtenerDescripcion($respuesta->data->header->type);
            $lc_condiciones[11] = $descripcionTipo;
            $lc_condiciones[12] = $respuesta->data->header->type;
            $lc_condiciones[13] = $estacion;
            $detalle = obtenerDetalleCuponDigital($respuesta->data->detail);
            $lc_condiciones[14] = $detalle["codPlu"];
            $lc_condiciones[15] = $detalle["descripcion_plu"];
            $lc_condiciones[16] = $detalle["cantidad"];
            $lc_condiciones[17] = $detalle["iva"];
            $lc_condiciones[18] = $detalle["precioBruto"];
            $lc_condiciones[19] = $detalle["precioNeto"];
            $lc_condiciones[20] = $request->apiImpresionAplica;
            $lc_condiciones[21] = json_encode($respuesta->data);
            $lc_condiciones[22] = 1;
            $ordenpedido->fn_consultar("impresionDetalleCuponOrdenPedido", $lc_condiciones);
        }

        print $respuestaSolicitud;
    } catch (Exception $e) {
        print json_encode($e);
    }
}

function actualizarEstadoCuponSirIntegration($cadena, $dataSolicitud, $url)
{
    try {
        $token = apiTokenIntegracion($cadena, 'TokenOrdenPedido');
        $tokenType = trim(apiTokenIntegracion($cadena, 'TokenTypeOrdenPedido'));
        $tokenHeader = "Authorization: " . $tokenType . " " . $token;

        //Convert Data tipo object a json
        $dataString = json_encode($dataSolicitud);

        //Consumo WebServices
        $solicitud = curl_init($url);
        curl_setopt($solicitud, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($solicitud, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($solicitud, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($solicitud, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($solicitud, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader));
        curl_setopt($solicitud, CURLOPT_TIMEOUT, 30);
        curl_setopt($solicitud, CURLOPT_CONNECTTIMEOUT, 30);

        $respuestaSolicitud = curl_exec($solicitud);
        $estado = curl_getinfo($solicitud);
        curl_close($solicitud);

        if ($estado['http_code'] != 200) {
            apiTokenIntegracion($cadena, 'CrearToken');

            $token = apiTokenIntegracion($cadena, 'TokenOrdenPedido');
            $tokenType = trim(apiTokenIntegracion($cadena, 'TokenTypeOrdenPedido'));
            $tokenHeader = "Authorization: " . $tokenType . " " . $token;

            //Convert Data tipo object a json
            $dataString = json_encode($dataSolicitud);

            //Consumo WebServices
            $solicitud = curl_init($url);
            curl_setopt($solicitud, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($solicitud, CURLOPT_POSTFIELDS, $dataString);
            curl_setopt($solicitud, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($solicitud, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($solicitud, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader));
            curl_setopt($solicitud, CURLOPT_TIMEOUT, 30);
            curl_setopt($solicitud, CURLOPT_CONNECTTIMEOUT, 30);

            $respuestaSolicitud = curl_exec($solicitud);
            curl_close($solicitud);
        }

        print $respuestaSolicitud;
    } catch (Exception $e) {
        print json_encode($e);
    }
}

function sumatoriaDeValores($detalles)
{
    $sumatoriaIVA = 0;
    $sumatoriaNeto = 0;
    $sumatoriaBruto = 0;

    foreach ($detalles as $detalle) {
        $sumatoriaIVA += floatval($detalle->IVA);
        $sumatoriaNeto += floatval($detalle->Neto);
        $sumatoriaBruto += floatval($detalle->Bruto);
    }

    return [
        'sumatoriaIVA' => $sumatoriaIVA,
        'sumatoriaNeto' => $sumatoriaNeto,
        'sumatoriaBruto' => $sumatoriaBruto
    ];
}

function obtenerDescripcion($tipo)
{
    $descripciones = [
        1 => "Credito",
        2 => "Canje",
        3 => "Cortesia Operaciones",
        4 => "Cortesia Mercadeo"
    ];

    if (array_key_exists($tipo, $descripciones)) {
        return $descripciones[$tipo];
    } else {
        return "Tipo no válido";
    }
}

function obtenerDetalleCuponDigital($detalle)
{
    $nDetalle = count($detalle);
    $i = 0;
    $codPlu = "";
    $descripcionPlu = "";
    $cantidad = "";
    $iva = "";
    $precioBruto = "";
    $precioNeto = ""; //total
    $separador = ",";
    for ($i = 0; $i < $nDetalle; $i++) {

        if (($nDetalle - 1) == $i) {
            $codPlu .= trim($detalle[$i]->Cod_Plu);
            $descripcionPlu .= trim($detalle[$i]->Descripcion);
            $cantidad .= trim($detalle[$i]->Cantidad);
            $iva .= trim($detalle[$i]->IVA);
            $precioBruto .= trim($detalle[$i]->Bruto);
            $precioNeto .= trim($detalle[$i]->Neto);
        } else {
            $codPlu .= trim($detalle[$i]->Cod_Plu) . $separador;
            $descripcionPlu .= trim($detalle[$i]->Descripcion) . $separador;
            $cantidad .= trim($detalle[$i]->Cantidad) . $separador;
            $iva .= trim($detalle[$i]->IVA) . $separador;
            $precioBruto .= trim($detalle[$i]->Bruto) . $separador;
            $precioNeto .= trim($detalle[$i]->Neto) . $separador;
        }
    }
    $detalle_aux = array();
    $detalle_aux["codPlu"] = $codPlu;
    $detalle_aux["descripcion_plu"] = $descripcionPlu;
    $detalle_aux["cantidad"] = $cantidad;
    $detalle_aux["iva"] = $iva;
    $detalle_aux["precioBruto"] = $precioBruto;
    $detalle_aux["precioNeto"] = $precioNeto;

    return $detalle_aux;
}

/**
 *  @fn obtenerDetalleCupon
 * 
 *  @brief Permite convertir el detalle json a String separados por comas de cada campo ej precio, plu
 * 
 *  @author Alejandro Salas
 *  @param array Detalle de cupon tipo array json
 *  @return array Detalle de cupon separado por comas.
 */
function obtenerDetalleCupon($detalle)
{
    $nDetalle = count($detalle);
    $i = 0;
    $codPlu = "";
    $descripcionPlu = "";
    $cantidad = "";
    $iva = "";
    $precioBruto = "";
    $precioNeto = ""; //total
    $separador = ",";
    for ($i = 0; $i < $nDetalle; $i++) {

        if (($nDetalle - 1) == $i) {
            $codPlu .= trim($detalle[$i]->codPlu);
            $descripcionPlu .= trim($detalle[$i]->descripcion_plu);
            $cantidad .= trim($detalle[$i]->cantidad);
            $iva .= trim($detalle[$i]->iva);
            $precioBruto .= trim($detalle[$i]->precioBruto);
            $precioNeto .= trim($detalle[$i]->precioNeto);
        } else {
            $codPlu .= trim($detalle[$i]->codPlu) . $separador;
            $descripcionPlu .= trim($detalle[$i]->descripcion_plu) . $separador;
            $cantidad .= trim($detalle[$i]->cantidad) . $separador;
            $iva .= trim($detalle[$i]->iva) . $separador;
            $precioBruto .= trim($detalle[$i]->precioBruto) . $separador;
            $precioNeto .= trim($detalle[$i]->precioNeto) . $separador;
        }
    }
    $detalle_aux = array();
    $detalle_aux["codPlu"] = $codPlu;
    $detalle_aux["descripcion_plu"] = $descripcionPlu;
    $detalle_aux["cantidad"] = $cantidad;
    $detalle_aux["iva"] = $iva;
    $detalle_aux["precioBruto"] = $precioBruto;
    $detalle_aux["precioNeto"] = $precioNeto;

    return $detalle_aux;
}
