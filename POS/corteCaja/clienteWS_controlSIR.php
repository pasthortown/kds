<?php

$request = (object) filter_input_array(INPUT_POST);

if ($request->metodo == "controlSIR") {    

    $url_sir = $request->url_sir;
    $data = $request->data;
    
    try {
        $header = array();
        $header[] = "Content-Type: application/json";
        $header[] = "Accept: application/json";

        $dataString = json_encode($data);

        $curl = $url_sir;
        $ch = curl_init($curl);         
           
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);  

        $result = curl_exec($ch);

        print ($result);
        
        curl_close($ch);
    } catch (Exception $exc) {
        print json_encode($exc);
    }
}