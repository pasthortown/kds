<?php


include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_servicioKds.php';


$json = file_get_contents("php://input");
// Decodificar JSON a un objeto PHP
$request = json_decode($json);


$servicio = new ServicioKds();

if ($request->metodo == 'apiServicioKds') {
 
    $data= null;
    try {
        $idRestaurante = isset($_SESSION['rstId'])? $_SESSION['rstId'] : $request->IDRestaurante;
        $row=$servicio->politica_kds($idRestaurante);
        if(isset($row['APIKDS']) && $row['APIKDS']!=1){
            return null;
        }
        $IDOrdenPedido = $request->IDOrdenPedido;
        $cuenta=isset($request->cuenta)? $request->cuenta : -1;
        $dataPost=$servicio->cuerpoKDS($IDOrdenPedido,0,$cuenta);
        EnviarData($dataPost,$row, $servicio, $request);
    } catch (\Throwable $th) {
        print json_encode($th);
    }
}
else if ($request->metodo == 'apiServicioKdsDomicilioDescuento') {
    $data= null;
    try {
        $idRestaurante = isset($_SESSION['rstId'])? $_SESSION['rstId'] : $request->IDRestaurante;
        if($idRestaurante==0)
            return null;
        $row=$servicio->politica_kds($idRestaurante);
        if(isset($row['APIKDS']) && $row['APIKDS']!=1)
                return null;
        $IDOrdenPedido = $request->IDOrdenPedido;
        $dataPost=$servicio->cuerpoKDS($IDOrdenPedido,6);
        EnviarData($dataPost,$row, $servicio, $request);
    } catch (\Throwable $th) {
        print json_encode($th);
    }
} else if ($request->metodo == 'apiServicioKdsDomicilio') {
    $data= null;
    try {
        $idRestaurante = isset($_SESSION['rstId'])? $_SESSION['rstId'] : $request->IDRestaurante;
        if($idRestaurante==0)
            return null;
        $row=$servicio->politica_kds($idRestaurante);
        if(isset($row['APIKDS']) && $row['APIKDS']!=1)
                return null;
        $IDOrdenPedido = $request->IDOrdenPedido;
        $dataPost=$servicio->cuerpoKDS($IDOrdenPedido,1);
        EnviarData($dataPost,$row, $servicio, $request);
    } catch (\Throwable $th) {
        print json_encode($th);
    }
} else if ($request->metodo == 'apiKDSKioskoTarjeta') {
    $data= null;
    try {
        $idRestaurante = $request->IDRestaurante;
        if($idRestaurante==0)
            return null;
        $row=$servicio->politica_kds($idRestaurante);
        if(isset($row['APIKDS']) && $row['APIKDS']!=1)
                return null;
        $IDOrdenPedido = $request->IDOrdenPedido;
        $dataPost=$servicio->cuerpoKDS($IDOrdenPedido,2);
        EnviarData($dataPost,$row, $servicio, $request);
    } catch (\Throwable $th) {
        print json_encode($th);
    }
}
else if ($request->metodo == 'apiKDSPickupTarjeta') {
    try {
        $idRestaurante = isset($_SESSION['rstId'])? $_SESSION['rstId'] : $request->IDRestaurante;
        if($idRestaurante==0)
            return null;
        $row=$servicio->politica_kds($idRestaurante);
        if(isset($row['APIKDS']) && $row['APIKDS']!=1)
                return null;
        $IDOrdenPedido = $request->IDOrdenPedido;
        $dataPost=$servicio->cuerpoKDS($IDOrdenPedido,3);
        EnviarData($dataPost,$row, $servicio, $request);
    } catch (\Throwable $th) {
        print json_encode($th);
    }
}else if ($request->metodo == 'apiDomicilio') {
    $data= null;
    try {
        $idRestaurante = $request->IDRestaurante;
        if($idRestaurante==0)
            return null;
        $row=$servicio->politica_kds($idRestaurante);
        if(isset($row['APIKDS']) && $row['APIKDS']!=1)
                return null;
        $IDOrdenPedido = $request->IDOrdenPedido;
        $dataPost=$servicio->cuerpoKDS($IDOrdenPedido,4);
        EnviarData($dataPost,$row, $servicio, $request);
    } catch (\Throwable $th) {
        print json_encode($th);
    }
}else if ($request->metodo == 'apiServicioKdsDomicilioParcial' || $request->metodo == 'apiServicioKdsDomicilioDescuentoParcial') {
    $data= null;
    try {
        $idRestaurante = $request->IDRestaurante;
        if($idRestaurante==0)
            return null;
        $row=$servicio->politica_kds($idRestaurante);
        if(isset($row['APIKDS']) && $row['APIKDS']!=1)
                return null;
        $IDOrdenPedido = $request->IDOrdenPedido;
        $dataPost=$servicio->cuerpoKDS($IDOrdenPedido,7);
        EnviarData($dataPost,$row, $servicio, $request);
    } catch (\Throwable $th) {
        print json_encode($th);
    }
}else if ($request->metodo == 'apiKDSKioskoEfectivo') {
    $data= null;
    try {
        $idRestaurante = $request->IDRestaurante;
        if($idRestaurante==0)
            return null;
        $row=$servicio->politica_kds($idRestaurante);
        if(isset($row['APIKDS']) && $row['APIKDS']!=1)
                return null;
        $IDOrdenPedido = $request->IDOrdenPedido;
        $dataPost=$servicio->cuerpoKDS($IDOrdenPedido,8);
        EnviarData($dataPost,$row, $servicio, $request);
    } catch (\Throwable $th) {
        print json_encode($th);
    }
}

function EnviarData($dataPost,$row, $servicio, $request) {

    $idRestaurante = isset($_SESSION['rstId'])? $_SESSION['rstId'] : $request->IDRestaurante;
    $row_segundo=$servicio->politica_kds_proveedor($idRestaurante);
    if(isset($row_segundo['habilitado']) && $row_segundo['habilitado']==1)
    {
        if(!empty($dataPost))
        foreach ($dataPost as $valor){
            if(isset($valor['Productos']) && !empty($valor['Productos'])){
                $dataString = stripslashes(json_encode($valor, JSON_UNESCAPED_UNICODE));            
                curlRetry($row['URL'],$dataString,$row['TIMEOUT'],$row['INTENTOS']);
            }
        }
    }
}

function curlRetry($curl, $payload, $timeout, $reintentos) {
    $header = array();
    $header[] = "Content-Type: application/json";
    $header[] = "Accept: application/json";
    $ch = curl_init($curl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

    $result = array(
        'error' => 0,
        'mensaje' => 'No Hubo respuesta'
    );
    $intento = 1;   
    for ($i = 0; $i < $reintentos; $i++) { 
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (in_array($status_code, array(200, 404))) {
            print ($response);
            break;
        }
        $intento = $intento + 1;
    }
    print_r($result);
    curl_close($ch);
}