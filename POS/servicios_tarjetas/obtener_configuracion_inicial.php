<?php

include("./system/conexion/clase_sql.php");

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
header('Access-Control-Allow-Origin: ' . $origin);
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET");
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'GET') {
    http_response_code(405);
    echo json_encode(["code" => 405, "messages" => ["Método no permitido"], "data" => []]);
    exit();
}

$ip = isset($_GET['ip']) ? $_GET['ip'] : '';
$fatherCode = isset($_GET['fatherCode']) ? $_GET['fatherCode'] : '';
$isValidIp = filter_var($ip, FILTER_VALIDATE_IP);

if (empty($ip)) {
    http_response_code(400);
    echo json_encode(["code" => 400, "messages" => ["El parámetro 'ip' es obligatorio"], "data" => []]);
    exit();
}

if (!$isValidIp) {
    http_response_code(400);
    echo json_encode(["code" => 400, "messages" => ["Parámetro 'ip' no valido"], "data" => []]);
    exit();
}                  


$catalogosArray = obtenerCatalogoConfiguracion();

if (empty($catalogosArray)) {
    http_response_code(500);
    echo json_encode(["code" => 500, "messages" => ["No se pudieron obtener los datos de configuración"], "data" => []]);
    exit();
}


$filteredData = isset($catalogosArray['catalogs']) ? $catalogosArray['catalogs'] : [];

if (!empty($fatherCode)) {
    $filteredData = array_filter($filteredData, function($item) use ($fatherCode) {
        return isset($item['fatherCode']) && $item['fatherCode'] === $fatherCode;
    });
}

if (!empty($ip)) {
    $filteredData = array_filter($filteredData, function($item) use ($ip) {
        return empty($item['EstacionIP']) || $item['EstacionIP'] === $ip;
    });
}

$responseData = array_map(function($item) {
    unset($item['EstacionIP']);
    return $item;
}, $filteredData);

$response = [
    "code" => 20,
    "messages" => ["Encontrado Correctamente"],
    "data" => ["catalogs" => array_values($responseData)]
];

echo json_encode($response);

function obtenerCatalogoConfiguracion() {
    $conexion = new sql();
    $sql_query = "EXEC [switch].[catalogoConfiguracionPago]";
    $catalogosArray = [];
    $jsonBuffer = '';

    if ($conexion->fn_ejecutarquery($sql_query)) {
        while ($catalogoRow = $conexion->fn_leerarreglo()) {
            $jsonString = utf8_encode($catalogoRow[0]);
            $jsonBuffer .= trim($jsonString);
        }

        $decodedCatalogoRow = json_decode($jsonBuffer, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(["code" => 500, "messages" => ["Error en la decodificación del JSON: " . json_last_error_msg()], "data" => []]);
            exit();
        }

        if (!empty($decodedCatalogoRow['catalogs'])) {
            $catalogosArray = $decodedCatalogoRow;
        }
    }

    return $catalogosArray;
}
?>
