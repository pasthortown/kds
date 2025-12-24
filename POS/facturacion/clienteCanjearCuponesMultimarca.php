<?php
session_start();
error_reporting(E_ALL);
include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_ordenPedido.php";
include_once "../clases/clase_webservice.php";
include_once '../clases/clase_cuponMultimarca.php';

header('Content-Type: text/javascript; charset=utf-8');
$servicioWebObj=new webservice();

$ordenpedido = new menuPedido();

$cadena = $_SESSION['cadenaId'];
$ip = $_SESSION['direccionIp'];
$usuario = $_SESSION['usuarioId'];
$estacion = $_SESSION['estacionId'];
$tipo_servicio = $_SESSION['TipoServicio'];
$nombre_usuario = $_SESSION['nombre'];
$perfil = $_SESSION['perfil'];
$control = $_SESSION['IDControlEstacion'];
$restaurante = $_SESSION['rstId'];

$request = (object) (array_map('specialChars', $_POST));

$estado=0;
$mensaje="";
$codigoCupon="";
$monto=0;

//Canjear Cupon Proceso Automatico
if ($request->metodo === "canjear") {
    $dataSolicitud = array(
        "opcion" => 2,
        "codigoRestaurante" => $restaurante,
        "incremental" => 0,
        "codigoSolicitud" => "",
        "codigoSeguridad" => "",
        "detalle" => "",
        "codigoUsuario" => $request->codigo,
        "estado" =>0 ,
        "mensaje" => "",
        "codigoCupon" => "",
        "monto" => 0,
        );
    
//    $datosWebservice=$servicioWebObj->retorna_WS_Cupones_CanjearAutomatico($tienda);
//    $url=$datosWebservice["urlwebservice"];
    //$url = "http://srvv-devsoa:8080/GerenteNacional.ServiciosWeb/webresources/cupon.multimarca/canjear/";
    $datosWebservice = $servicioWebObj->retorna_WS_CuponesMultimarca_Canjear($restaurante);
    $url = $datosWebservice["urlwebservice"];
    $resultadoConsultaWS=fn_ejecutar_consulta_webservice($url,$dataSolicitud);
    $objRespuestaSolicitud = json_decode($resultadoConsultaWS);

    //Si el cupon se canjeo correctamente en gerente, regitrarlo en la tabla cuponCanjeado
    if($objRespuestaSolicitud->estado===1){
        $lc_datos = array(
            $_SESSION["IDControlEstacion"], $request->codigo,
            "Cupon canjeado exitosamente", 1,
            1,$request->monto,
            0, 0,
            "Cupon Multimarca", 0
        );
        $objCuponMultimarca = new clase_cuponMultimarca();
        $objCuponMultimarca->fn_registrarCanjeCuponMultimarca($lc_datos);
    }
    echo($resultadoConsultaWS);
//Canjear Cupon Proceso Manual
} 
else if($request->metodo === "verificarAuto"){
    if(!isset($request->codigo)) { enviarRespuestaInválida("El campo código no tiene un valor"); }
    if(strlen($request->codigo)!== clase_cuponMultimarca::TAMANO_CODCUPON){ enviarRespuestaInválida("Código inválido"); };
    $objCuponMultimarca=new clase_cuponMultimarca($request->codigo);
    $dataSolicitud = array(
        "opcion" => 1,
        "codigoRestaurante" => $restaurante,
        "incremental" => $objCuponMultimarca->incremental,
        "codigoSolicitud" => $objCuponMultimarca->codigoSolicitud,
        "codigoSeguridad" => $objCuponMultimarca->codigoSeguridad,
        "detalle" => $objCuponMultimarca->detalle,
        "codigoUsuario" => "",
        "estado" =>$estado ,
        "mensaje" => $mensaje,
        "codigoCupon" => $codigoCupon,
        "monto" => $monto,
    );
    //$url = "http://srvv-devsoa:8080/GerenteNacional.ServiciosWeb/webresources/cupon.multimarca/verificar/";
    $datosWebservice = $servicioWebObj->retorna_WS_CuponesMultimarca_Verificar($restaurante);
    $url = $datosWebservice["urlwebservice"];
    $resultadoConsultaWS=fn_ejecutar_consulta_webservice($url,$dataSolicitud);
    echo($resultadoConsultaWS);
}
else if ($request->metodo === "verificarManual") {
    if(!isset($request->incremental ) && !is_numeric( $request->incremental )) { enviarRespuestaInválida(); }
    if(!isset($request->codigoSolicitud)) { enviarRespuestaInválida(); }
    if(!isset($request->codigoSeguridad)) { enviarRespuestaInválida(); }
    if(!isset($request->detalle) && !is_numeric( $request->detalle )) { enviarRespuestaInválida(); }

    $dataSolicitud = array(
        "opcion" => 1,
        "codigoRestaurante" => $restaurante,
        "incremental" => $request->incremental,
        "codigoSolicitud" => $request->codigoSolicitud,
        "codigoSeguridad" => $request->codigoSeguridad,
        "detalle" => $request->detalle,
        "codigoUsuario" => $request->codigoUsuario,
        "estado" =>$estado ,
        "mensaje" => $mensaje,
        "codigoCupon" => $codigoCupon,
        "monto" => $monto,
        );
    //$url = "http://srvv-devsoa:8080/GerenteNacional.ServiciosWeb/webresources/cupon.multimarca/verificar/";
    $datosWebservice = $servicioWebObj->retorna_WS_CuponesMultimarca_Verificar($restaurante);
    $url = $datosWebservice["urlwebservice"];
    $resultadoConsultaWS=fn_ejecutar_consulta_webservice($url,$dataSolicitud);
    echo($resultadoConsultaWS);
   // $url=$datosWebservice["urlwebservice"];
   // $url = $url . "canjear/manual/";
}else if ($request->metodo === "anularCanje") {
    if(!isset($request->codigoCupon ) && (""===$request->codigoCupon )) { enviarRespuestaInválida(); }
    $dataSolicitud = array(
        "opcion" => 3,
        "codigoRestaurante" => $restaurante,
        "incremental" => 0,
        "codigoSolicitud" => "",
        "codigoSeguridad" => "",
        "detalle" => "",
        "codigoUsuario" => $request->codigoCupon,
        "estado" =>$estado ,
        "mensaje" => $mensaje,
        "codigoCupon" => $codigoCupon,
        "monto" => $monto,
    );
    //$url = "http://srvv-devsoa:8080/GerenteNacional.ServiciosWeb/webresources/cupon.multimarca/reversar/";
    $datosWebservice = $servicioWebObj->retorna_WS_CuponesMultimarca_Reversar($restaurante);
    $url = $datosWebservice["urlwebservice"];
    $resultadoConsultaWS=fn_ejecutar_consulta_webservice($url,$dataSolicitud);
    echo($resultadoConsultaWS);
}

function fn_ejecutar_consulta_webservice($urlWS,$datosPeticion){
    try {
        //Convert Data tipo object a json
        $dataString = json_encode($datosPeticion);

        //Consumo WebServices
        $solicitud = curl_init($urlWS);
        curl_setopt($solicitud, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($solicitud, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($solicitud, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($solicitud, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($solicitud, CURLOPT_TIMEOUT, 10);
        curl_setopt($solicitud, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($solicitud, CURLOPT_FRESH_CONNECT, TRUE);

        $respuestaSolicitud = curl_exec($solicitud);
        $info = curl_getinfo($solicitud);
        if ($respuestaSolicitud === false || $info['http_code'] != 200) {
            $mensajeErrorCURL=curl_error($solicitud);
            if ($mensajeErrorCURL){
                curl_close($solicitud);
                enviarRespuestaInválida("Error al consumir WebService: ".$mensajeErrorCURL);
            }
            switch ($info["http_code"]) {
                case 404:  # OK
                    curl_close($solicitud);
                    enviarRespuestaInválida("Error al consumir WebService: Ruta de servicio no encontrada");
                    break;
                default:
                    curl_close($solicitud);
                    enviarRespuestaInválida("Error al consumir WebService");
            }
        }
        else {
            curl_close($solicitud);
            return $respuestaSolicitud;
        }
    } catch (Exception $e) {
        enviarRespuestaInválida("No se pudo realizar la solicitud al WebService");
    }
}
function enviarRespuestaInválida($mensaje="La solicitud enviada no es válida"){
    $respuesta=new stdClass();
    $respuesta->estado=0;
    $respuesta->Mensaje=$mensaje;
    $respuesta->datos=array();
    echo(json_encode($respuesta));
    die();
}

function specialChars($a) {
    return htmlspecialchars($a, ENT_QUOTES,'UTF-8');
}
