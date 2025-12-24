<?php
require_once ROOT_PATH."services/MasterDataClientes/Helper/HelperNetCoreService.php";

class AuthService {
    public function getToken($policy) {
        try{
            $helperNetCoreService = new HelperNetCoreService();
            $response = $helperNetCoreService->validarTokenApiCliente($policy);
            return $response;
        }catch (Exception $th) {
            return ['statusCode'=>'500','success'=>'false','response'=>'Error AuthService, getToken(): '.$th->getMessage()];
        }
    }
}