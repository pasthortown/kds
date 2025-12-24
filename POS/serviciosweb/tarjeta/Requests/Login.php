<?php
include_once 'CurlRetryHttp.php';

class Login {
    private $curlRetry;
    private $endPoint;

    public function __construct($url, $endpointLogin, $defaultHeaders = []) {
        $this->endPoint = $endpointLogin;
        $this->curlRetry = new CurlRetryHttp($url, $defaultHeaders);
    }

    public function run($username, $password) {
        try {
            if ($this->isTokenSetAndNotEmpty() && !$this->isTokenExpired()) {
                return true;
            }

            return $this->obtenerToken($username, $password);
        } catch (Exception $e) {
            return false;
        }
    }

    private function isTokenSetAndNotEmpty() {
        return isset($_SESSION['TarjetaAccesToken'], $_SESSION['TarjetaExpires']) &&
               !empty($_SESSION['TarjetaAccesToken']) && 
               !empty($_SESSION['TarjetaExpires']);
    }

    private function isTokenExpired() {
        $fechaServicioDateTime = DateTime::createFromFormat('Y/m/d H:i', $_SESSION['TarjetaExpires']);
        $fechaActual = new DateTime();
        return $fechaServicioDateTime < $fechaActual;
    }

    private function obtenerToken($username, $password) {
        $payload = json_encode(['usuario' => $username, 'clave' => $password]);

        try {
            $response = $this->curlRetry->http($this->endPoint, 'POST', $payload, 3);
            $responseData = json_decode($response, true);

            if (isset($responseData['error']) && !$responseData['error'] && isset($responseData['datos'][0])) {
                $tokenData = $responseData['datos'][0];
    
                if (isset($tokenData['tokenType'], $tokenData['accessToken'], $tokenData['expires'])) {
                    $_SESSION['TarjetaAccesToken'] = $tokenData['accessToken'];
                    $_SESSION['TarjetaExpires'] = $tokenData['expires'];
                    $_SESSION['TarjetaType'] = $tokenData['tokenType'];
                    return true;
                }
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }
}