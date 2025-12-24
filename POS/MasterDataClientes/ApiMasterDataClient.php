<?php
define("ROOT_PATH","../");
require_once ROOT_PATH."services/MasterDataClientes/ApiController.php";
require_once ROOT_PATH."services/MasterDataClientes/Client/ClientService.php";
require_once ROOT_PATH."services/MasterDataClientes/Auth/AuthService.php";
require_once ROOT_PATH."clases/clase_masterdatacliente.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authService = new AuthService();
    $modelmasterdatacliente = new ServicioMasterdata();
    $clienteService = new ClienteService($authService, $modelmasterdatacliente);
    $ApiController = new ApiController($clienteService);

    $json = file_get_contents('php://input');
    $data = json_decode($json);
    $type = $data->accion;
    $info = json_decode(json_encode($data->info),true);

    $mapeoMetodos = [
        'BUSCAR' => 'search',
        'MODIFICAR' => 'modify',
        'GUARDAR' => 'save'
    ];
    if (array_key_exists($type, $mapeoMetodos)) {
        try {
            $metodo = $mapeoMetodos[$type];
            $message = $ApiController->$metodo($info);
            print json_encode($message,true);
        } catch (Exception $th) {
            print json_encode(["statusCode" => 500, "message" => $th->getMessage(), "error" => $th->getMessage()]);
        }
    } else {
        print json_encode(["statusCode" => 500, "message" => "Invalid document type"]);
    }
}elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

}else{
    return http_response_code(404);
}