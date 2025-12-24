<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////
////////      DESARROLLADO POR: JOSEPH PURIFICACION                ////////////////////////////////////////
////////      DESCRIPCION: INTEGRACION MAXPOINT - MONITOR        ////////////////////////////////////////
////////      FECHA DE CREACION: 29/05/2023                    ////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
@session_start();
include("../system/conexion/clase_sql.php");
include("../clases/app.Cadena.php");

// REQUEST
$request = (object) filter_input_array(INPUT_POST);

$apps = new Cadena();
$response = $apps->obtenerURLMonitor();
$url = $response['url'];

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '{
                "codigo_id": "'.$request->id.'",
                "timeline": ["'.implode('","',$request->timeline).'"],
                "data": {
                    "canal": "'.$request->canal.'",
                    "telefono": "'.$request->telefono.'",
                    "cfac_id": "'.$request->cfac_id.'",
                    "motorolo": "'.(trim($request->motorolo) == "" ? null : $request->motorolo).'",
                    "tracking": "'.(trim($request->tracking) == "" ? null : $request->tracking).'",
                    "cliente": "'.$request->cliente.'"
                }
            }',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
