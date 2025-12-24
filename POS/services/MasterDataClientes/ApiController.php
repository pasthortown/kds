<?php
require_once ROOT_PATH."services/MasterDataClientes/Client/ClientService.php";

class ApiController {
    private $clienteService;

    public function __construct(
        ClienteService $clienteService
    ) 
    {
        $this->clienteService = $clienteService;
    }

    public function search($parameters)
    {
        $cliente = $this->clienteService->searchClient($parameters);
        return $cliente;
    }

    public function modify($parameters)
    {
        $cliente = $this->clienteService->modifyCliente($parameters);
        return $cliente;
    }

    public function save($parameters)
    {
        $cliente = $this->clienteService->createCliente($parameters);
        return $cliente;
    }
}
