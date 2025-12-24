<?php
/**
 * Created by PhpStorm.
 * User: kevin.francisco.ibadango
 * Date: 21/6/2019
 * Time: 13:29
 */

if (!isset($_POST['turneroAccion'])) {
    echo 'Error: La variable turneroAccion no está definida. <br/>';
    die();
}
if (!isset($_POST['turneroURl'])) {
    echo 'Error: La variable turneroURl no está definida. <br/>';
    die();
}
if (!isset($_POST['transaccion'])) {
    echo 'Error: La variable transaccion no está definida  <br/>';
    die();
}
if (!isset($_POST['estado'])) {
    echo 'Error: La variable estado no está definida <br/>';
    die();
}
if (!isset($_POST['orden'])) {
    echo 'Error: La variable orden no está definida  <br/>';
    die();
}
$turneroAccion      = htmlspecialchars($_POST['turneroAccion']);
$turneroURl         = htmlspecialchars($_POST['turneroURl']);
$transaccion        = htmlspecialchars($_POST['transaccion']);
$estado             = htmlspecialchars($_POST['estado']);
$orden              = htmlspecialchars($_POST['orden']);
$cliente            = htmlspecialchars($_POST['cliente']);
$clienteDocumento   = htmlspecialchars($_POST['clienteDocumento']);
$tipo               = htmlspecialchars($_POST['tipo']);
$especial           = htmlspecialchars(isset($_POST['especial']))?htmlspecialchars($_POST['especial']):'';
$parametros = [
    'transaccion'           =>  "$transaccion",
    'estado'                =>  "$estado",
    'orden'                 =>  "$orden",
    'cliente'               =>  "$cliente",
    'clienteDocumento'      =>  "$clienteDocumento",
    'tipo'                  =>  "$tipo",
    'especial'                  =>  "$especial"
];
    //key1=value1&key2=value2
//$ch = curl_init($turneroURl);
$ch = curl_init($turneroURl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parametros));
curl_setopt($ch, CURLOPT_FAILONERROR, true);

if ($turneroAccion =='agregarTurno') {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
} elseif ($turneroAccion =='anularTurno') {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
}

try {
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpcode == 200) {
        if ($turneroAccion =='agregarTurno') {
            echo "!Se ha generado el turno " . $orden." con éxito!" . $response;
        } elseif ($turneroAccion =='anularTurno') {
            echo "!Se ha eliminado el turno " . $orden . " con éxito!" . $response;
        }
    } else {
        echo "Error de servidor: " . $httpcode . ' - ' . curl_error($ch);
    }
} catch (Exception $e) {
    trigger_error(sprintf(
        'Curl failed with error #%d: %s',
        $e->getCode(), $e->getMessage()), E_USER_ERROR);
} finally {
    curl_close($ch);
}