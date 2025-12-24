<?php
ini_set('max_execution_time', 120);

class CurlRetryHttp {
    private $allowedRequests = ['GET', 'POST'];
    private $defaultHeaders;
    private $baseUrl;

    public function __construct($baseUrl, $defaultHeaders = []) {
        $this->baseUrl = $baseUrl;
        $this->defaultHeaders = $defaultHeaders;
    }

    public function http($endpoint, $request, $payload, $timeout) {
        if (!$this->isRequestAllowed($request)) {
            return $this->getErrorResult('Tipo de request no permitido');
        }

        $ch = $this->initializeCurl($endpoint, $request, $payload, $timeout);

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        unset($ch);

        if ($this->isSuccessfulResponse($status_code)) {
            return $response;
        }

        return $this->getErrorResult();
    }

    private function initializeCurl($endpoint, $request, $payload, $timeout) {
        $url = $this->validateURL($this->baseUrl, $endpoint);

        $header = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        if (!empty($this->defaultHeaders['type']) && !empty($this->defaultHeaders['token'])) {
            $header[] = 'Authorization: ' . $this->defaultHeaders['type'] . ' ' . $this->defaultHeaders['token'];
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        return $ch;
    }

    private function validateURL($baseUrl, $endpoint) {
        if (!preg_match('/^https?:\/\//', $baseUrl)) {
            $baseUrl = 'http://' . $baseUrl;
        }

        $baseUrl = rtrim($baseUrl, '/');
        $endpoint = ltrim($endpoint, '/');
        
        return $baseUrl . '/' . $endpoint;
    }

    private function isRequestAllowed($request) {
        return in_array(strtoupper($request), $this->allowedRequests);
    }

    private function isSuccessfulResponse($status_code) {
        return in_array($status_code, [200, 404, 400]);
    }

    private function getErrorResult($message = 'No se tiene respuesta') {
        return [
            'error' => 0,
            'mensaje' => $message
        ];
    }
}
