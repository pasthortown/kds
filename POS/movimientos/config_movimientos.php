<?php
//////////////////////////////////////////////////////////////////////////
////////DESARROLLADO:  ///////////////////////////////////////////////////
////////DESCRIPCION: Creación de campo cadena Cargar movimiento //////////
////////FECHA CREACION:   ////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 05-01-2017//////////////////////////////
///////USUARIO QUE MODIFICO: Juan Estévez/////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

session_start();

include_once("../system/conexion/clase_sql.php");
include_once("../clases/clase_movimientos.php");
include_once "../clases/clase_webservice.php";
include_once "../tokens/MainApiToken.php";
$servicioWebObj=new webservice();
$movimiento = new Movimiento();

$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];
$idRestaurante = $_SESSION['rstId'];
$idControlEstacion = $_SESSION['IDControlEstacion'];
$nombre_usuario = $_SESSION['nombre'];
$array_ini = parse_ini_file("../serviciosweb/interface/config.ini");


$token = apiTokenIntegracion($idCadena,'TokenOrdenPedido');
$tokenType = trim(apiTokenIntegracion($idCadena,'TokenTypeOrdenPedido'));
$tokenHeader = "Authorization: ".$tokenType." ".$token;

//Carga tipos de movimientos
if (htmlspecialchars(isset($_POST["cargarTiposMovimiento"]))) {
    $condicion[0] = htmlspecialchars($_POST["accion"]);
    $condicion[1] = $idRestaurante;
    $condicion[2] = $idCadena; //Agregado Cadena
    $condicion[3] = htmlspecialchars($_POST["tipo"]);
    print $movimiento->configuraciones($condicion);
//Llamada WebServices Verificar Compras Caja chica Sistema Gerente UIO o GYE
} else if (htmlspecialchars(isset($_POST["verificarCajaChicaLocalGerente"]))) {
    //api= /api/caja-chica/movimientos
    $datosWebservice=$servicioWebObj->retorna_WS_Caja_Chica_Verificar($idRestaurante);
    $urlServicioWeb=$datosWebservice["urlwebservice"];   
    //$urlServicioWeb = ($array_ini['verificarCajaChicaLocalGerente']);
    $url = $urlServicioWeb;
    $data = array(
        "mensaje" => "",
        "estado" => 0,
        "desde" => htmlspecialchars($_POST["fechaFin"]),
        "hasta" => htmlspecialchars($_POST["fechaFin"]),
        "total" => 0,
        "codRestaurante" => $idRestaurante,
        "codigo" => 0,
        "localizacion" => htmlspecialchars($_POST["localizacion"])
    );
    $dataString = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader));
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $result = curl_exec($ch);
    $estado = curl_getinfo($ch);
    curl_close($ch);

    // Verificar si el token es valido
    if ($estado['http_code'] == 401) {

        apiTokenIntegracion($idCadena,'CrearToken');
        $token = apiTokenIntegracion($idCadena,'TokenOrdenPedido');
        $tokenType = trim(apiTokenIntegracion($idCadena,'TokenTypeOrdenPedido'));
        $tokenHeader = "Authorization: ".$tokenType." ".$token;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $result = curl_exec($ch);

    }

    print $result;
//Guardar Movimiento
} else if (htmlspecialchars(isset($_POST["guardarMovimientoIngresoEgreso"]))) {
    $condicion[0] = $idCadena;
    $condicion[1] = $idRestaurante;
    $condicion[2] = $idControlEstacion;
    $condicion[3] = $idUsuario;
    $condicion[4] = htmlspecialchars($_POST["idMotivo"]);
    $condicion[5] = htmlspecialchars($_POST["valor"]);
    $condicion[6] = htmlspecialchars($_POST["signo"]);
    $condicion[7] = htmlspecialchars($_POST["hasta"]);
    $condicion[8] = htmlspecialchars($_POST["hasta"]);
    $condicion[9] = htmlspecialchars($_POST["numeroAutorizacion"]);
    print $movimiento->agregarMovimiento($condicion);
} else if (htmlspecialchars (isset($_POST["guardarMovimientoIngresoEgresoCCC"]))) {
    //api= /api/caja-chica/actualizar-movimientos
    $datosWebservice=$servicioWebObj->retorna_WS_Caja_Chica_GuardarIngresoEgreso($idRestaurante);
    $url=$datosWebservice["urlwebservice"];
    //$urlServicioWeb = ($array_ini['guardarMovimientoIngresoEgresoCCC']);
    //$url = $urlServicioWeb;
    
    $data = array(
        "mensaje" => "",
        "estado" => 0,
        "desde" => htmlspecialchars($_POST["hasta"]),
        "hasta" => htmlspecialchars($_POST["hasta"]),
        "total" => 0,
        "codRestaurante" => $idRestaurante,
        "codigo" => 0,
        "localizacion" => htmlspecialchars($_POST["localizacion"]),
        "usuario" => $nombre_usuario
    );
    $dataString = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader));
    //curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $result = curl_exec($ch);
    $estado = curl_getinfo($ch);
    $respuesta = json_decode($result);
    curl_close($ch);

    // Verificar si el token es valido
    if ($estado['http_code'] == 401) {

        apiTokenIntegracion($idCadena,'CrearToken');
        $token = apiTokenIntegracion($idCadena,'TokenOrdenPedido');
        $tokenType = trim(apiTokenIntegracion($idCadena,'TokenTypeOrdenPedido'));
        $tokenHeader = "Authorization: ".$tokenType." ".$token;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader));
        //curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $result = curl_exec($ch);
        $estado = curl_getinfo($ch);
        $respuesta = json_decode($result);
        curl_close($ch);

    }

    if ($respuesta->estado > 0) {
        $condicion[0] = $idCadena;
        $condicion[1] = $idRestaurante;
        $condicion[2] = $idControlEstacion;
        $condicion[3] = $idUsuario;
        $condicion[4] = htmlspecialchars($_POST["idMotivo"]);
        $condicion[5] = $respuesta->total;
        $condicion[6] = htmlspecialchars($_POST["signo"]);
        $condicion[7] = $respuesta->desde;
        $condicion[8] = $respuesta->hasta;
        $condicion[9] = '';
        $movimiento->agregarMovimiento($condicion);
    }
    print $result;
}
 else if (htmlspecialchars(isset($_POST["verificarCCLCancelacion"]))) {
    $datosWebservice=$servicioWebObj->retorna_WS_Caja_Chica_Cancelacion($idRestaurante);
    $url=$datosWebservice["urlwebservice"];
//    $urlServicioWeb = ($array_ini['verificarCCLCancelacion']);
//    $url = $urlServicioWeb;

    $codRestaurante = $idRestaurante;
    $desde = htmlspecialchars($_POST["fechaInicia"]);
    $hasta = htmlspecialchars($_POST["fechaFinaliza"]);
    $estado = "0";
    $mensaje = "0";
    $soap_request   = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:per="http://localhost/gerente_15/serviciosweb/personal/">';
    $soap_request  .= ' <soapenv:Header/>';
    $soap_request  .= '<soapenv:Body>';
    $soap_request  .=    '<per:EliminarRelacionCaja soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">';
    $soap_request  .=       '<cod_restaurante xsi:type="xsd:string">'.$codRestaurante.'</cod_restaurante>';
    $soap_request  .=       '<fechaInicio xsi:type="xsd:date">'.$desde.'</fechaInicio>';
    $soap_request  .=       '<fechaFinaliza xsi:type="xsd:date">'.$hasta.'</fechaFinaliza>';
    $soap_request  .=       '<estado xsi:type="xsd:string">'.$estado.'</estado>';
    $soap_request  .=       '<mensaje xsi:type="xsd:string">'.$mensaje.'</mensaje>';
    $soap_request  .=       '<usuario xsi:type="xsd:string">'.$nombre_usuario.'</usuario>';
    $soap_request  .=    '</per:EliminarRelacionCaja>';
    $soap_request  .= '</soapenv:Body>';
    $soap_request  .='</soapenv:Envelope>';
     
  $header = array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "SOAPAction: \"run\"",
    "Content-length: ".strlen($soap_request),
  );

   $soap_do = curl_init();
  curl_setopt($soap_do, CURLOPT_URL, $url );
  curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
  curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
  curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
  curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($soap_do, CURLOPT_POST,           true );
  curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_request);
  curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $header);
 
  $result = curl_exec($soap_do);
  print $result;
  
 }