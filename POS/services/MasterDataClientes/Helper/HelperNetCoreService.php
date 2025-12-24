<?php
require_once ROOT_PATH."services/MasterDataClientes/Helper/SendNetCoreService.php";

class HelperNetCoreService {

    public function validarTokenApiCliente($policy) {
        $sendNetCoreService = new SendNetCoreService();
        $arrayToken = array();
        $mensaje = '';
        $token = '';
        $path_json = '';
        $status='';
        $fileName = 'tokenApiMdmCliente.json';
        $folderName = 'tokens';
        $permisos = '0777';
        $base_dir = realpath(__DIR__  . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $filePath = $base_dir . $folderName . DIRECTORY_SEPARATOR . $fileName;
        $filePathToken = $base_dir . $folderName;

        //Si no existe la carpeta tokens la crea
        if (!file_exists($filePathToken)) {
            mkdir($filePathToken, $permisos, true);
        }
        if (!file_exists($filePath)) {
          $tokenApiMDMCliente = $sendNetCoreService->getApiAuthToken($policy);
          file_put_contents($filePath,$tokenApiMDMCliente);
        }

        $configContents =  file_get_contents($filePath);
        if ($configContents !== false) {
            $config = json_decode($configContents, true);
            if (isset($config['token'])) {
                $token = $config['token'];
                $tokenParts = explode('.', $token);
                $tokenPayload = base64_decode($tokenParts[1]);
                $payload = json_decode($tokenPayload, true);

                $tokenExpirationTime = $payload['exp'];
                $currentTimestamp = time();

                if ($currentTimestamp <= $tokenExpirationTime) {
                    $mensaje = utf8_decode("El token API MDM CLIENTE es válido y no ha caducado.");
                    $status=200;
                } else {
                    $tokenData = $sendNetCoreService->getApiAuthToken($policy);
                    if (isset($tokenData["token"])) {
                        $token = $tokenData["token"];
                        $jsonDatos = json_encode($tokenData);
                       file_put_contents($filePath, $jsonDatos);
                    } else {
                        $mensaje = 'No se pudo generar un nuevo token.';
                        $status=500;
                    }
                }
            } else {
                $tokenData = $sendNetCoreService->getApiAuthToken($policy);
                if (isset($tokenData["token"])) {
                    $token = $tokenData["token"];
                    $jsonDatos = json_encode($tokenData);
                    file_put_contents($filePath, $jsonDatos);
                } else {
                    $mensaje = 'No se pudo generar un nuevo token.';
                    $status=500;
                }
            }
        } else {
            $mensaje = "No se pudo leer el archivo JSON.";
        }
        $arrayToken = array('statusCode'=>$status, 'token' => $token, 'mensaje' => utf8_encode($mensaje));
        return $arrayToken;
    }
}