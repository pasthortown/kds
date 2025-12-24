<?php
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if(strcasecmp($contentType, 'application/json') != 0){
    throw new Exception('Content type must be: application/json');
}

$content = trim(file_get_contents("php://input"));
$dataContent = json_decode($content, true);

include_once "./modelos/Transacciones.php";
if (isset($dataContent['transaccion']) && isset($dataContent['parametros'])) {

    $parametros = $dataContent['parametros'];

    $datosQPM = new TransaccionesQPM;
    $datosTienda = $datosQPM->validarDisponibilidadServicioQPM($parametros);
    $datosTienda = json_decode($datosTienda, true);

    if ($datosTienda[0]['isActive'] == 1 && $datosTienda[0]['ipTienda'] && $datosTienda[0]['url'] && $datosTienda[0]['activity']) {
        
        $parametros['url'] = $datosTienda[0]['ipTienda'] . $datosTienda[0]['url'];
        $parametros['ipTransaccion'] = $datosTienda[0]['ipTienda'];
        $parametros['activity'] = $datosTienda[0]['activity'];

        set_time_limit(300);
        if (isset($parametros['idTransaccion']) && $parametros['idTransaccion'] !== '') {

            include_once "./consultas_api_QPM/Transaccion.php";
            $Transaccion = new Transaccion;

            switch ($dataContent['transaccion']) {
                case 'transaccionVendida':
                    echo $Transaccion->ingresarTransaccionVendidaQPM($parametros);
                    break;
                case 'anularTransaccion':
                    echo $Transaccion->anularTransaccionVendidaQPM($parametros);
                    break;
                default:
                    echo 'Error al enviar los datos.';
                    break;
            }
        }

        if (isset($parametros['transaccion']) && $dataContent['transaccion'] === 'SalesSummary') {
            include_once "./consultas_api_QPM/SalesSummary.php";
            $SalesSummary = new SalesSummary;
            echo $SalesSummary->enviarSalesSummary($parametros);
        }

    } else {
        echo 'El restaurante no tiene la politica activa para la integracion con QPM.';
    }
} else {
    echo 'Error al enviar los datos.';
}
