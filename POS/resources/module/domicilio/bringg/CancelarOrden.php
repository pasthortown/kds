<?php

/////////////////////////////////////////////////////////////
//// DESARROLLADO POR: Vanessa Soria C.                   ///
//// DESCRIPCION: Información de pedidos a Bringg         ///
//// FECHA CREACION: 09/11/2018                           ///
//// FECHA ULTIMA MODIFICACION:                           ///
//// USUARIO QUE MODIFICO:                                ///
//// DECRIPCION ULTIMO CAMBIO:                            ///
/////////////////////////////////////////////////////////////

include '../system/clase_sql.php';
include '../clases/clase_seguridades.php';
include '../clases/clase_bringg.php';
$bringg = new Bringg();
$codFactura = $_GET['codFactura'];
$cod_bringg;
$idBringg;
if(isset($_GET['cod_bringg'])){
    $cod_bringg = $_GET['cod_bringg'];
    $idBringg = $cod_bringg;
}else{
    //Consulta cabecera, datos de cliente, inmueble
    $bringg->consultaIdBringg($codFactura, '0,7');
    $lc_rowdatos = $bringg->fn_leerobjeto();
    $idBringg = $lc_rowdatos->cod_Bringg;
}
// cuerpo de la petición
if ($idBringg != null) {
    $data = array(
        'id' => $idBringg,
    );
    $content = json_encode($data);
    $bringg->fn_liberarecurso();
    $url = 'https://admin-api.bringg.com/services/kmae04kd/942fe84f-2d5d-4ae9-b4bf-777f5a900c46/4033aa0d-efb3-4e73-b779-f04da705a01d/';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, ($url));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Content-Length: '.strlen($content)));
    $result = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status != 200) {
        echo "Error: call to URL $url failed with status $status, response $result, curl_error ".curl_error($curl).', curl_errno '.curl_errno($curl);
        die("Error: call to URL $url failed with status $status, response $result, curl_error ".curl_error($curl).', curl_errno '.curl_errno($curl));
    }
    $response = json_decode($result);
    if ($response->success) {
        $bringg->insertaAuditoria($url, 'Cancelar orden: '.$codFactura, $status, $response->success);
        echo 'OK';
    } else {
        $bringg->insertaAuditoria($url, 'Cancelar orden: '.$codFactura, $status, $result);
        echo 'error';
    }
}
/*
if (isset($resultadoCambioEstado->status) || array_key_exists('status', $resultadoCambioEstado)) {
        $status = $resultadoCambioEstado->status;
        $message = $resultadoCambioEstado->data;
        $pedido->insertaAuditoria($url.$endPoint, http_build_query($data), $status, $message);
    } else {
        $code = $resultadoCambioEstado->code;
        $message = $resultadoCambioEstado->message;
        $pedido->insertaAuditoria($url.$endPoint, http_build_query($data), $code, $message);
    }
} else {
    $code = $resultObj->code;
    $message = $resultObj->message;
    $pedido->insertaAuditoria($url.$endPoint, http_build_query($data), $code, $message);
}*/