<?php
session_start();

include_once '../system/conexion/clase_sql.php';
include_once '../clases/clase_transferenciaventa.php';
include_once "../clases/clase_desmontadoCajero.php";
include_once "../clases/clase_webservice.php";

$lc_apertura = new desmontaCaja();
$cliente = new TransferenciaVenta();
$servicioWebObj=new webservice();

$array_ini = parse_ini_file("../serviciosweb/interface/config.ini");
$idUsuario = $_SESSION['usuarioId'];
$lc_cadena = $_SESSION['cadenaId'];
$lc_rest = $_SESSION['rstId'];
$lc_IDControlEstacion = $_SESSION['IDControlEstacion'];

$request = (object) filter_input_array(INPUT_POST);  
//$documento = $request->documento;
//$tipoDocumento = $request->tipoDocumento;
//$urlServicioWeb = ($array_ini["urlServicioWebCliente"]);
//$urlEnvioDatosClienteWS = ($array_ini["urlEnvioDatosClienteWS"]);
$lc_condiciones[0]=$lc_rest;
$lc_condiciones[1]=$lc_cadena;
$datosDestino=$cliente->fn_encuentra_bdd($lc_condiciones);
$basedatos=$datosDestino["NombreBdd"];
$restauranteDestino=$datosDestino["Restaurante"];
$cadenaDestino=$datosDestino["Cadena"];



if($request->accion==="consultaExisteCajeroActivo"){
    try {                 
        //$urlWSCajeroActivo=($array_ini["urlWSCajeroActivo"]);
        $datosWebservice=$servicioWebObj->retorna_WS_Trans_Venta_ValidaCajero($lc_rest);
        
        
        
        
        $urlWSCajeroActivo=$datosWebservice["urlwebservice"];
        //$url = $urlServicioWeb . $documento . '&tipoIdentificacion=' .$tipoDocumento; 
 
       
        $fecha_prd =  $_SESSION['fecha_prd'];
        $url=$urlWSCajeroActivo."?rst_id=".$restauranteDestino."&cdn_id=".$cadenaDestino."&bdd=".$basedatos."&fecha_prd=".$fecha_prd;
  
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        //$datos=new stdClass();
        //print_r($result);
        /*if(curl_error($ch))
        {
            
            $datos->respuesta=array('No',0,'No se pudo consultar si existe un cajero activo');   
            print_r($datos);
            print json_encode($datos); 
            die();
        }*/
        curl_close($ch);
        $respuestaSolicitud = json_decode($result); 
        //print_r($respuestaSolicitud);
        //$tamRespuesta=count($respuestaSolicitud);
        //$respuesta["estado"] = $respuestaSolicitud[1];         
        $respuesta2=array("respuesta"=>$respuestaSolicitud);
        
   
      //  $respuesta2=array("mensaje"=>$respuestaSolicitud);
        //$respuesta=($tamRespuesta>0)?$respuestaSolicitud[0]:$respuestaSolicitud;
 
        print json_encode($respuesta2);        
    } catch (Exception $e) {
        print json_encode($e);
    }
}

if ($request->accion === "inyectaIngresoDestino") {    
    $lc_condiciones[0] = 2;
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_cadena;
    $lc_condiciones[3] = $lc_IDControlEstacion;
    $valor = $_POST["valor"];
    $datos = $_POST["datos"]; 
    $periodo = $_POST["periodo"];
    $tipo = $_POST["tipo_transferencia"];
    $IDControlEstacionAutomatico = $_POST["IDControlEstacionAutomatico"];
    
    $datosWebservice = $servicioWebObj->retorna_WS_Trans_Venta_InyeccionIngresoDestino($lc_rest);   
    $urlWSInyeccionIngresoDestino = $datosWebservice["urlwebservice"];
    
    $datosNumeroTxHeladeriaLocal = $cliente->fn_encuentra_numeroTransaccionesOrigenUsuario($lc_condiciones);    
    $transacciones = $datosNumeroTxHeladeriaLocal["txHeladeriaUsuario"];    
      
    try {
        $url = $urlWSInyeccionIngresoDestino."?valor=".$valor."&bdd=".$basedatos."&IDPeriodo=".$periodo."&transaccion=".$transacciones."&IDControlEstacion=".$lc_IDControlEstacion."&IDUsersPos=".$idUsuario."&tipo=".$tipo."&datos=".$datos."&rest=".$lc_rest."&cadena=".$lc_cadena."&IDControlEstacionAutomatico=".$IDControlEstacionAutomatico;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        $respuestaSolicitud = json_decode($result);
        $tamRespuesta=count($respuestaSolicitud);
        $respuesta=($tamRespuesta>0)?$respuestaSolicitud[0]:$respuestaSolicitud;
        print json_encode($respuesta);        
    } catch (Exception $e) {
        print json_encode($e);
    }
}
if($request->accion==="validaTransferenciaHeladeria"){
    
    $datosWebservice=$servicioWebObj->retorna_WS_Trans_Venta_ValidacionTransferencia($lc_rest);
    $urlWSValidacionTransferencia=$datosWebservice["urlwebservice"];
    //$urlWSValidacionTransferencia=($array_ini["urlWSValidacionTransferencia"]);

//if (htmlspecialchars(isset($_POST["validaTransferenciaHeladeria"]))) {
try {
    $url=$urlWSValidacionTransferencia."?rst_id=".$restauranteDestino."&cdn_id=".$cadenaDestino."&bdd=".$basedatos;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    //execute post
    $result = curl_exec($ch);
    //close connection
    $datos=new stdClass();
    if(curl_error($ch)){
        $datos->respuesta=array('No',0,'No se pudo consultar si existe un cajero activo');
        print json_encode($datos); 
        die();
    }
    curl_close($ch);
    $respuestaSolicitud = json_decode($result);
    $tamRespuesta=count($respuestaSolicitud);
    $respuesta2=array("respuesta"=>$respuestaSolicitud);
    print json_encode($respuesta2);        
    }
        catch (Exception $e) {
        print json_encode($e);
    }
}

if ($request->accion==="consultaCajeroAutomaticoActivo"){
  try {                 
    $datosWebservice=$servicioWebObj->retorna_WS_Trans_Venta_ValidaCajeroAutomatico($lc_rest);
    $urlWSCajeroActivo=$datosWebservice["urlwebservice"];
    $fecha_prd =  $_SESSION['fecha_prd'];
    $url=$urlWSCajeroActivo."?rst_id=".$restauranteDestino."&cdn_id=".$cadenaDestino."&bdd=".$basedatos."&fecha_prd=".$fecha_prd;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $result = curl_exec($ch);
    curl_close($ch);
    $respuestaSolicitud = json_decode($result);      
    $respuesta2=array("respuesta"=>$respuestaSolicitud);
    print json_encode($respuesta2);        
  } catch (Exception $e) {
    print json_encode($e);
  }
}