<?php
require '../api/routes/routes.php';
require '../api/controllers/PeriodoController.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

$routes = include '../api/routes/routes.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$pos = strpos($path, '/api');

if ($pos !== false) {
    $path = substr($path, $pos + 4);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada']);
}

if (isset($routes[$requestMethod][$path])) {
    $route = $routes[$requestMethod][$path];

    if (strpos($route, '@') !== false) {
        list($controller, $requestMethod) = explode('@', $route);

        if (class_exists($controller) && method_exists($controller, $requestMethod)) {
            $controllerInstance = new $controller();
            $response = $controllerInstance->$requestMethod();
            http_response_code(200);
            echo $response;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Controlador o método no encontrados']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Formato de ruta inválido']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada']);
}

?>