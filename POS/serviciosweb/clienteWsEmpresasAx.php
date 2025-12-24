<?php

include_once '../system/conexion/clase_sql.php';
include_once "../clases/clase_webservice.php";
require_once'../soap/lib/nusoap.php';

$servicioWebObj=new webservice();

             /*
            $url= "http://azdynatec.cloudapp.net:55180/WsClReMaxPoint/WsClReMaxPoint.asmx?wsdl";       
                   
            $param = array();
            $client = new nusoap_client($url,true);    
            //var_dump($client);
            $Confirmacion = $client->call('ClientesRelacionados');
            var_dump($client);
             
             */
$puerto="55180";
$wsdl="http://azdynatec.cloudapp.net:".$puerto."/WsClReMaxPoint/WsClReMaxPoint.asmx?wsdl";
try {
$parametros = array();
//$parametros['parameters']=$servicioWebObj;
//print_r(file_get_contents($wsdl));
//$test="test".$puerto."test";
//$url="http:azdynatec.cloudapp.net:".$puerto."/WsClReMaxPoint/WsClReMaxPoint.asmx?wsdl"
$client = new SoapClient ($wsdl,$parametros);//nusoap_client("http://200.124.230.154:8200/ABIS12QRF.Integracion/RCService.asmx?WSDL",true);    
var_dump($client->__getFunctions());
$resultado=$client->ClientesRelacionados($parametros)->ClientesRelacionadosResponse;
var_dump($resultado);
}
catch (SoapFault $e) 
{
   echo "ERROR!";
   echo "WSDL: ".$wsdl;
   echo $e -> getMessage ();
   echo 'Last response: '. $client->__getLastResponse();
}

