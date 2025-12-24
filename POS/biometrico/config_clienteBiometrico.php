<?php
@session_start(); 	

/*
DESARROLLADO POR: Darwin Mora
DESCRIPCION: pruebas con biometrika
FECHA CREACION: 10-02-2014
FECHA ULTIMA MODIFICACION:
USUARIO QUE MODIFICO: 
DESCRIPCION ULTIMO CAMBIO: 
*/
require_once('../soap/lib/nusoap.php');
/*Llamada al WebService de biometrica*/
if(isset($_POST["biometrika"])) 
{
    $wsdl = "http://sazappbiom.kfc.com.ec/abismp16/servicio/RCService.asmx?WSDL";

    $client = new nusoap_client($wsdl, 'wsdl');
    $param = array('trama' => $_POST["hid_bio"], 'identificacion' => $_POST["cedula_bio"],'operacion'=> 2);	
    $Confirmacion = $client -> call('ProcesarWU2', $param);	
    $respuesta=$Confirmacion['ProcesarWU2Result'];
   /* $respuesta=explode("|",$respuesta);
    $respuesta=$respuesta[0];
    $respuesta=$respuesta[1];*/
    $res2[]=array('respuesta'=>$respuesta);	
    print json_encode($res2);	
}
?>
 


