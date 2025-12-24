<?php
ini_set("max_execution_time", 360); //Extiende el tiempo de la ejecucion de la peticion
session_start();
include_once "../../system/conexion/clase_sql.php";
$request = (object) filter_input_array(INPUT_POST);
if($request->metodo=='telegram'){
$token=$request->token;
$id=$request->chatid;$chatid  =$id;
$porciones = explode(";", $chatid);
//$id = "1141795991";
//$id = "1244509951";
foreach($porciones as $p){
    var_dump($p);

$datos=json_decode($request->json);
$msg = $datos->descuadre[0]->mensaje." detalles:\nTienda: ".$datos->descuadre[0]->tienda."\nFecha Periodo: ".$datos->descuadre[0]->fecha_periodo.
"\nId Periodo: ".$datos->descuadre[0]->id_periodo."\nTipo Cuadre: ".$datos->descuadre[0]->tipo_cuadre."\nVenta Bruta: ".$datos->descuadre[0]->totales[0]->venta_bruta.
"\nForma de Pago: ".$datos->descuadre[0]->totales[0]->formas_pago."\nPlus: ".$datos->descuadre[0]->totales[0]->plus."\nDeclarado: ".$datos->descuadre[0]->totales[0]->declarado.
"\nTransferencia: ".$datos->descuadre[0]->totales[0]->transferencia."\nDiferencia: ".$datos->descuadre[0]->totales[0]->diferencia;
$URL = "https://api.telegram.org/bot$token"; 

$json = ['chat_id'       => $p,
             'text'          => $msg,
             'parse_mode'    => 'HTML'];
$respuesta=http_post($URL.'/sendMessage', $json);
print($respuesta);
}
}
else{
    $respuesta ='Método no definido.';
    print ($respuesta); 
}
function http_post($url, $json)
{
    $ans = null;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); 
    try
    {
        $data_string = json_encode($json);
        // desabilita verificacion SSl
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        // si la respuesta es falsa la imprime
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: '. strlen($data_string))
        );
        $ans = json_decode(curl_exec($ch));
        if($ans->ok !== TRUE)
        {
            $ans = null;
            return "Mensaje No Enviado Verificar Servicio.";
        }
        return "Mensaje Enviado :).";
    }
    catch(Exception $e)
    {
        echo "Error: ", $e->getMessage(), "\n";
    }
    curl_close($ch);
    return $ans;
}