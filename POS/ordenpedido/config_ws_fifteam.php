<?php
session_start();
include_once '../system/conexion/clase_sql.php';
include_once "../clases/clase_ordenPedido.php";
include_once "../clases/clase_webservice.php";
include_once "../clases/clase_httpResponseCode.php";

$ordenPedido = new menuPedido();
$servicioWebObj = new webservice();
$code_http = new CodigosHTTP();

$lc_rest = $_SESSION['rstId'];
$lc_cadena = $_SESSION['cadenaId'];

$request = (object) filter_input_array(INPUT_POST);

if ($request->metodo === "validaCodigo") {
    $codigo = $request->codigo;

    // Parametros paar el consumo del WS
    $param = $ordenPedido->fn_parametrosFifteam($lc_cadena, $lc_rest, $codigo);
    $datos = $param[0];
    $url_ws = $datos["parametros_url"];
    $username = $datos["username"];
    $password = $datos["user_password"];    

    // Ruta del WS
    $datosWebservice = $servicioWebObj->retorna_WS_fifteam($lc_rest);
    $urlServicioWeb = $datosWebservice["urlwebservice"];
    
    $fifteam_url = $urlServicioWeb . $url_ws;

    // Datos pruebas
    //$fifteam_url = "https://dev.myfifteam.com/lib/checkcode/?brand=134&local=001&code=213&version=002";
    
    // Respuestas
    $http_code = "http_code";
    $code = "code";    
    $mensaje = "mensaje";
    $path = "path";
    $description = "descripcion";

    $respuesta = array(
        $http_code => ""
        , $code => ""
        , $mensaje => ""
        , $path => ""
        , $description => ""
    );

    // Valida que la ruta del ws se encuentre configurada
    if ($urlServicioWeb == "" || strrpos($urlServicioWeb, "RUTA SERVICIO") || strrpos($urlServicioWeb, "WS SERVIDOR")) {
        $respuesta[$code] = "003";
        $respuesta[$mensaje] = utf8_encode($urlServicioWeb);

        print json_encode($respuesta);
        die();   
    }

    // Valida que el usuario y contraseña se encuentre configurado
    if ($username == "" || $password == "") {
        $respuesta[$code] = "004";
        $respuesta[$mensaje] = "Credenciales de autentificación no configuradas";

        print json_encode($respuesta);
        die();
    }
    
    try {
        $result = "";

        $header = array();
        $header[] = "Content-Type: application/json";
        $header[] = "Accept: application/json";
        $header[] = "Authorization: Basic " . base64_encode($username . ":" . $password);

        $url = $fifteam_url;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $result = curl_exec($ch);
        $informacion = curl_getinfo($ch);
        $codigo_http = $informacion["http_code"];        

        if ($result !== false) {
            $respuestaSolicitud = json_decode($result);

            if ($codigo_http != 200) {
                if ($codigo_http == 401) {
                    $respuesta[$http_code] = $codigo_http;
                    $respuesta[$mensaje] = $result;
                    $respuesta[$path] = $fifteam_url;
                    $respuesta[$description] = ($codigo_http != 0 ? $code_http->http_response_code($codigo_http) : curl_error($ch)); 

                    print json_encode($respuesta);
                } else {
                    $respuesta[$http_code] = $codigo_http;
                    $respuesta[$code] = isset($respuestaSolicitud->errorcode) ? $respuestaSolicitud->errorcode : "02";
                    $respuesta[$mensaje] = isset($respuestaSolicitud->error) ? $respuestaSolicitud->error : "Ha ocurrido un error al consultar la información";
                    $respuesta[$path] = $fifteam_url;
                    $respuesta[$description] = ($codigo_http != 0 ? $code_http->http_response_code($codigo_http) : curl_error($ch));

                    print json_encode($respuesta);   
                }            
            } else {
                $respuesta[$http_code] = $codigo_http;
                $respuesta[$code] = $respuestaSolicitud->id;
                $respuesta[$mensaje] = $respuestaSolicitud->message;            
                
                print json_encode($respuesta);
            }
        } else {
            $respuesta[$http_code] = $codigo_http;
            $respuesta[$code] = "01";
            $respuesta[$mensaje] = "Ha ocurrido un error al consultar la información";
            $respuesta[$path] = $fifteam_url;
            $respuesta[$description] = ($codigo_http != 0 ? $code_http->http_response_code($codigo_http) : curl_error($ch));      
            
            print json_encode($respuesta);
        }
        
        curl_close($ch);
    } catch (Exception $e) {
        print json_encode($e);
    }
}
