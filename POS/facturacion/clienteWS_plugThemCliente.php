<?php
session_start();

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_plugThem.php";
include_once "../clases/clase_webservice.php";

$configWS = new webservice();
$plugThem = new PlugThem();
$idRestaurante = $_SESSION["rstId"];
$ipEstacion = $_SESSION["direccionIp"];

$request = (object) filter_input_array(INPUT_POST);

if ($request->metodo == "plugThemPost") {
    
    $BrandId = $request->BrandId;
    $idCajero = $request->idCajero;
    $EmpId = $request->EmpId;
    $EmpName = $request->EmpName;
    $SiteId = $request->SiteId;
    $SiteName = $request->SiteName;
    $ShiftManagerId = $request->ShiftManagerId;
    $ShiftManagerName = $request->ShiftManagerName;
    $Categories = $request->Categories;
    $CustomerDoc = $request->CustomerDoc;
    $CustomerName = $request->CustomerName;
    $CustomerEmail = $request->CustomerEmail;
    $CustomerMobile = $request->CustomerMobile;
    $EffortValue = $request->EffortValue;
    $EffortReason = $request->EffortReason;
    $EffortComment = $request->EffortComment;
    $InvRange = $request->InvRange;
    $qr_enable = $request->qr_enable;
    $transaccion = $request->transaccion;
    
    $token_type = $request->token_type;
    $acces_token = $request->acces_token;

    
    $respuesta = array(
        "respuesta" => 0,
        "mensaje" => ""
    );    

    $datoWebService = $configWS->retorna_WS_PlugThem_Post($idRestaurante);
    $urlWebService = $datoWebService["urlwebservice"];
    $estadoURL = $datoWebService["estado"];

    if ($estadoURL == 0) {
        $respuesta["respuesta"] = 0;
        $respuesta["mensaje"] = utf8_encode($urlWebService);

        print json_encode($respuesta);

        return;
    }
    
    $parametrosProductos = $plugThem->parametrosEncuestaProductos($BrandId, $transaccion);

    foreach($parametrosProductos as $index => $value) {
        $int_channel    = $value['int_channel'];
        $survey_type    = $value['survey_type'];
        $product        = $value['product'];
    }
    
    try {
        $header = array();
        $header[] = "Content-Type: application/json";
        $header[] = "Accept: application/json";
        $header[] = "Authorization: ".$token_type." ".$acces_token;
        
        $dataPost = array(
            "BrandId" => $BrandId
            , "EmpId" => $EmpId
            , "EmpName" => $EmpName
            , "SiteId" => $SiteId
            , "SiteName" => $SiteName
            , "ShiftManagerId" => $ShiftManagerId
            , "ShiftManagerName" => $ShiftManagerName
            , "Categories" => $Categories
            , "CustomerDoc" => $CustomerDoc
            , "CustomerName" => $CustomerName
            , "CustomerEmail" => $CustomerEmail
            , "CustomerMobile" => $CustomerMobile
            , "EffortValue" => $EffortValue
            , "EffortReason" => $EffortReason
            , "EffortComment" => $EffortComment
            , "InvRange" => $InvRange
            , "qr_enable" => $qr_enable
            , "id" => $transaccion
            , "IntChannel" => $int_channel
            , "SurveyType" => $survey_type
            , "Products" => json_decode($product, true) 
        );

        $dataString = json_encode($dataPost);

        $curl = $urlWebService;
        $ch = curl_init($curl);         
           
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);  
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); 
  

        $result = curl_exec($ch);  
        $informacion = curl_getinfo($ch);        
    
        $respuestaSolicitud = json_decode($result);


        if ($informacion["http_code"] == 200) {            
            //$respuesta["respuesta"] = $respuestaSolicitud->status;
            //$respuesta["mensaje"] = $respuestaSolicitud->msg;
            
            $respuesta["respuesta"] = $informacion["http_code"];

            if ($dataPost["qr_enable"] == "1") {
                try {
                    $varData2 = $transaccion."/".$idCajero;     
                    $datoWebService = $configWS->retorna_WS_PlugThem_Get($idRestaurante);
                    $urlWebService = $datoWebService["urlwebservice"];

                    // Crear el URL para posterior generación del código QR e impresión en la factura del cliente. 
                    $URL_QR = $urlWebService.$varData2;

                    $codigoQR = $plugThem->guardarCodigoQR($transaccion, $URL_QR);                    
                } catch (Exception $exc) {
                    $error = "Codigo QR: " .$URL_QR. ", Error: [" .$exc. "]";
                    $log = $plugThem->logPlugThem($idRestaurante, $respuesta["respuesta"], $respuesta["respuesta"], $error, $transaccion. " - QR", $ipEstacion); 
                }               
            }            
        } else {
            //$respuesta["respuesta"] = $respuestaSolicitud->status;
            //$respuesta["mensaje"] = $respuestaSolicitud->msg;

            $respuestaHTTP = isset($informacion["http_code"]) ? $informacion["http_code"] : 0; 
            $respuesta["respuesta"] = $respuestaHTTP;
             
            $error = "Header: " .json_encode($header). ", Body: " . $dataString. ", Url: [".$urlWebService."], IDCajero: ".$idCajero.", Error: [" .json_encode($respuestaSolicitud). "]";
            $log = $plugThem->logPlugThem($idRestaurante, $respuesta["respuesta"], $respuestaHTTP, $error, $transaccion. " - Post", $ipEstacion); 
        }
       
        print json_encode($respuesta);
        
        curl_close($ch);
        
    } catch (Exception $exc) {
        print json_encode($exc);
    }
}
