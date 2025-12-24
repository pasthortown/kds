<?php
if (file_exists("Modelos/PoliticasTokens.php")) {
    include_once("./Modelos/PoliticasTokens.php");
} elseif (file_exists("../tokens/api_sir_integrations/Modelos/PoliticasTokens.php")) {
    include_once("../tokens/api_sir_integrations/Modelos/PoliticasTokens.php");
} elseif (file_exists("../../tokens/api_sir_integrations/Modelos/PoliticasTokens.php")) {
    include_once("../../tokens/api_sir_integrations/Modelos/PoliticasTokens.php");
} else {
    include_once("./api_sir_integrations/Modelos/PoliticasTokens.php");
}

class ConsultarApiTokens
{
    private $idCadena;
    private $token;
    private $tokenType;
    public $politicasCreadas = array();

    public function __construct($idCadena)
    {
        $this->idCadena = $idCadena;
        $this->consultarToken();
    }

    function solicitarToken()
    {
        try {

            $client_id = $this->consultarClientId($this->idCadena);
            $client_secret = $this->consultarClientSecret($this->idCadena);
            $url = $this->consultarUrlToken($this->idCadena);
            if ($client_id !== "N/A" && $client_secret !== "N/A" && $url !== "N/A") {
                $session = curl_init($url);
                curl_setopt_array($session, array(
                    CURLOPT_POST => 1,
                    CURLOPT_POSTFIELDS => array(
                        "grant_type" => "client_credentials",
                        "client_id" => $client_id,
                        "client_secret" => $client_secret
                    ),
                    CURLOPT_RETURNTRANSFER => true,
                ));
                $response = curl_exec($session);
                $estado = curl_getinfo($session);
                curl_close($session);
                if ($estado['http_code'] === 200) {
                    return $response;
                } else {
                    $this->politicasCreadas = array("codigo" => 0, "mensaje" => "Error al intentar accecer al endPoint para obtener el token.");
                    return false;
                }
            }
            $this->politicasCreadas = array("codigo" => 0, "mensaje" => "No se encuentran las políticas para crear el token");
            return false;
        } catch (Exception $e) {
            echo 'Error CURL: ',  $e->getMessage(), "\n";
        }
    }

    function consultarToken()
    {
        if (!$this->verificarExpiracion()) {
            $data = $this->solicitarToken();
            if ($data) {
                $this->setToken($data);
            }
        };
    }

    private function verificarExpiracion()
    {
        $flag = false;
        $file = $this->obtenerRutaArchivoToken();
        if (file_exists($file)) {
            $jsonString = file_get_contents($file);
            $tokenList = json_decode($jsonString, true);
            foreach ($tokenList as $key => $token) {
                if ($token['idAssociated'] === $this->idCadena) {
                    $now = date("Y-m-d H:i:s");
                    if ($token['expiresAt'] > $now) {
                        $flag = true;
                        $this->token = $token['token'];
                        $this->expiresAt = $token['expiresAt'];
                        $this->tokenType = $token['tokenType'];
                    }
                }
            }
        }
        return $flag;
    }

    private function setToken($data)
    {
        $dt = json_decode($data);
        if (isset($dt->access_token)) {

            $this->token = $dt->access_token;
            $this->tokenType = $dt->token_type;
            $this->expiresAt = $dt->expires_in;

            $fechaFormateada = $this->obtenerFechaVencimientoFormateada($this->expiresAt);
            $file = $this->obtenerRutaArchivoToken();

            file_exists($file)
                ? $this->actualizarArchivoJsonStore($this->idCadena, $this->token, $this->tokenType, $fechaFormateada)
                : $this->crearArchivoJson($this->idCadena, $this->token, $this->tokenType, $fechaFormateada);

            $this->token = $dt->access_token;
            $this->tokenType = $dt->token_type . " ";
        }
    }

    private function obtenerRutaArchivoToken()
    {
        $archivoRaiz = __FILE__;
        //$archivoRaizLongitud = strlen("ConsultarApiSirMacromatix.php");
        $archivoRaizLongitud = strlen("ConsultarApiTokens.php");
        $ruta = substr($archivoRaiz, 0, - ($archivoRaizLongitud));
        $file = $ruta . 'TokenApiSir.json';
        return $file;
    }

    private function obtenerFechaVencimientoFormateada($segundos)
    {
        $cantidadHoras = $segundos / (60 * 60);
        $cantidadDias = $cantidadHoras / 24;
        $now = date("Y-m-d H:i:s");
        $fechaVencimientoFormateada = date("Y-m-d H:i:s", strtotime($now . "+ {$cantidadDias} days"));

        return $fechaVencimientoFormateada;
    }

    private function actualizarArchivoJsonStore($idToken, $token, $tokenType, $fechaExpiracion)
    {
        $file = $this->obtenerRutaArchivoToken();
        $jsonString = file_get_contents($file);
        $tokenList = json_decode($jsonString, true);
        $editado = false;
        foreach ($tokenList as $key => $valorToken) {
            if ($valorToken['idAssociated'] === $idToken) {
                $tokenList[$key]['token'] = $token;
                $tokenList[$key]['tokenType'] = $tokenType;
                $tokenList[$key]['expiresAt'] = $fechaExpiracion;
                $editado = true;
            }
        }

        if (!$editado) {
            $nuevoToken = new stdClass();

            $nuevoToken->idAssociated = $idToken;
            $nuevoToken->token = $token;
            $nuevoToken->tokenType = $tokenType;
            $nuevoToken->expiresAt = $fechaExpiracion;

            array_push($tokenList, $nuevoToken);
        }

        $nuevoJson = json_encode($tokenList);
        file_put_contents($file, $nuevoJson);
    }

    private function crearArchivoJson($idToken, $nuevoToken, $tokenType, $nuevaFechaExpiracion)
    {
        $file = $this->obtenerRutaArchivoToken();

        $tokenList[0]['idAssociated'] = $idToken;
        $tokenList[0]['token'] = $nuevoToken;
        $tokenList[0]['tokenType'] = $tokenType;
        $tokenList[0]['expiresAt'] = $nuevaFechaExpiracion;

        $json_string = json_encode($tokenList);
        file_put_contents($file, $json_string);
    }

    private function consultarClientId($idCadena)
    {
        $PoliticasTokens = new PoliticasTokens;
        $datosParametro = json_decode($PoliticasTokens->obtenerValorParametroSirApiIntegracion($idCadena, "CLIENT_ID"), true);
        return $datosParametro[0]["valorParametro"];
    }

    private function consultarClientSecret($idCadena)
    {
        $PoliticasTokens = new PoliticasTokens;
        $datosParametro = json_decode($PoliticasTokens->obtenerValorParametroSirApiIntegracion($idCadena, "CLIENT_SECRET"), true);
        return $datosParametro[0]["valorParametro"];
    }

    private function consultarUrlToken($idCadena)
    {
        $PoliticasTokens = new PoliticasTokens;
        $datosParametro = json_decode($PoliticasTokens->obtenerValorParametroSirApiIntegracion($idCadena, "URL OBTENER TOKEN"), true);
        return $datosParametro[0]["valorParametro"];
    }
    public function obtenerToken()
    {
        return $this->token;
    }

    public function obtenerTokenType()
    {
        return $this->tokenType;
    }

    function crearToken()
    {
        $data = $this->solicitarToken();
        if ($data) {
            $this->setToken($data);
        }
    }
}

?>