<?php
ini_set("max_execution_time", 360); //Extiende el tiempo de la ejecucion de la peticion
session_start();
include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_webservice.php";
         
$config_ws = new webservice();
$rutaWS;
$descifrado = '';



$ASCIIcifrado = 0;            
$request = (object) filter_input_array(INPUT_POST);

if($request->metodo=="conexion")
{
$rutaWS=$config_ws->retorna_WS_Eventos_Conexion($_SESSION['rstId']);
$url = $rutaWS["urlwebservice"];
}
if($request->metodo=="service") 
{
$rutaWS=$config_ws->retorna_WS_Eventos_Valida_WS($_SESSION['rstId']);
$url = $rutaWS["urlwebservice"];
}
        $header = array();
        $header[] = "Content-Type: application/json";
        $header[] = "Accept: application/json";
        if($request->metodo=="conexion")
        {
            $dataString=[];
            $dataString["hostLocal"]=$request->hostLocal;
            $dataString["host"]=$request->host;
            $dataString["database"]=$request->database;
            $dataString["username"]=$request->username;
            for ($i = 0; $i < strlen($request->password); $i++) {
                $ASCIIcifrado = ord(substr($request->password, $i));
                $ASCIIcifrado = $ASCIIcifrado - 5 % 255;
                $descifrado = $descifrado . chr($ASCIIcifrado);
            }
            $dataString["password"]=$descifrado;
            $dataString["id"]=$request->IDPeriodo;
            $data=json_encode($dataString);
        }
        $result = "";
        $response = false;
        $curl = $url;
        $ch = curl_init($curl);
        if($request->metodo=="conexion")
        {
       curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
       curl_setopt($ch, CURLOPT_POSTFIELDS,$data);}
       else{
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    }
       // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);}
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);            
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        $result = curl_exec($ch);
        if ($result !== false) {
            $response = $result;

            print ($response);
        } else {
            $respuesta_ajax = array(
                "status" => 1005
                , "mensaje" => "Error al consultar el servicio web."
                , "ruta" => $rutaWS
            );
            print json_encode($respuesta_ajax);
        } 
        curl_close($ch);    
            
        