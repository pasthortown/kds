<?php
require_once ROOT_PATH."services/MasterDataClientes/Auth/AuthService.php";
require_once ROOT_PATH . "clases/clase_masterdatacliente.php";

class ClienteService {

    private $authService;
    private $claseMasterDataCliente;


    public function __construct(
        AuthService $authService, 
        ServicioMasterdata $claseMasterDataCliente
    ) 
    {
        $this->authService = $authService;
        $this->claseMasterDataCliente = $claseMasterDataCliente;
    }

    public function searchClient($parameters) {
        $policy = $this->claseMasterDataCliente->getConfigApi($parameters['cdn_id']);
        $token = $this->authService->getToken($policy);
        $sendNetCoreService = new SendNetCoreService();
        $responsegetApiClient = $sendNetCoreService->getApiClient($policy,$parameters,$token['token']);
        $this->claseMasterDataCliente->insertLogApi($parameters['idUserPos'],$parameters['rst_id'],$responsegetApiClient,"searchClient");
        return $responsegetApiClient;
    }

    public function createCliente($parameters) {
        $policy = $this->claseMasterDataCliente->getConfigApi($parameters['cdn_id']);
        $token = $this->authService->getToken($policy);
        $sendNetCoreService = new SendNetCoreService();
        $responsepostApiClient = $sendNetCoreService->postApiClient($policy,$parameters,$token['token']);
        $this->claseMasterDataCliente->insertLogApi($parameters['idUserPos'],$parameters['rst_id'],$responsepostApiClient,"createCliente");
        return $responsepostApiClient;
    }

    public function modifyCliente($parameters) {
        $policy = $this->claseMasterDataCliente->getConfigApi($parameters['cdn_id']);
        $token = $this->authService->getToken($policy);
        
        $sendNetCoreService = new SendNetCoreService();
        $responseputApiClient = $sendNetCoreService->getApiClient($policy,$parameters,$token['token']);
        if($responseputApiClient['success'] == "true") {
            $sendNetCoreService = new SendNetCoreService();
            $_id=$responseputApiClient['data']['cliente']['_id'];
            $_tipo_doc=$responseputApiClient['data']['cliente']['tipoDocumento'];
            $responseputApiClient = $sendNetCoreService->putApiClient($policy,$parameters,$token['token'],$_id);
            $responseputApiClient['_uid']=$_id;
            $responseputApiClient['documento']=$parameters['documento'];
            $responseputApiClient['tipoDocumento']=$_tipo_doc;
            $this->claseMasterDataCliente->insertLogApi($parameters['idUserPos'],$parameters['rst_id'],$responseputApiClient,"modifyCliente");
            return $responseputApiClient;
        }
        $this->claseMasterDataCliente->insertLogApi($parameters['idUserPos'],$parameters['rst_id'],$responseputApiClient,"modifyCliente");
        return $responseputApiClient;
    }
}