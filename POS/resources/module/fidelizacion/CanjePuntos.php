<?php

//Clases para consumo de web services
$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . '..') . $ds;
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}CallREST.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Request.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Response.php";
require_once "{$base_dir}{$ds}../module{$ds}fidelizacion{$ds}Token.php";
require_once "{$base_dir}{$ds}../module{$ds}fidelizacion{$ds}TokenManager.php";
//Consulta de urls de servicios
// include_once "../../clases/clase_webservice.php";

class CanjePuntos
{

    public $callREST;
    public $request;
    public $response;
    public $idRestaurante;
    public $serviceUrl;
    public $tokenLoyalty;
    public $loyaltyTokenManager;
    public $app;

    function __construct($idRestaurante, $app)
    {
        $this->idRestaurante = $idRestaurante;
        $this->request = new Request;
        $this->response = new Response;
        $this->serviceUrl = new webservice();
        // ******* V2 *******
        $this->tokenLoyalty = new TokenLoyalty();
        $this->loyaltyTokenManager = new TokenManager();
        $this->app = $app;
    }

    /*Metodo para canje de puntos*/
    function canjear($data)
    {
        // Verificar Token
        //Obtener url endpoint
        if ($this->app === "jvz") {
            if(isset($_SESSION['canje_v2']) && !empty($_SESSION['canje_v2']) && trim($_SESSION['canje_v2'])==1){
                $urlWSRetornaPrecios = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIREBASE','CANJEPUNTOSV2');
            }
            else{
                $urlWSRetornaPrecios = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIREBASE', 'CANJEPUNTOS');
            }
        } else {
            $urlWSRetornaPrecios = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIDELIZACION' . $this->app, 'CANJEPUNTOS');
        }
        $this->request->url = $urlWSRetornaPrecios['urlwebservice'];
        $newGenerateCode = $this->loyaltyTokenManager->generateNewAccessTokenApp($this->idRestaurante, $_SESSION['claveConexion']);
        
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'authorization: Bearer ' . $newGenerateCode,
                'content-type: application/json'
            ));
        $this->request->timeout = 60;
        //Clase generica para el consumo de REST
        $this->callREST = new CallREST;
        // funcion call() para consumir ws
        $this->response = $this->callREST->call($this->request);
        $this->response->url=$this->request->url;    
        return $this->response;
    }

    function estadoCanje($redemtionCode)
    {

        // Verificar Token
        // $this->tokenLoyalty->getToken(); ******* V2 FIDELIZACION *******
        //Obtener url endpoint por app
        if ($this->app === "jvz") {
            $retornaUrlWS = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIREBASE', 'ESTADOCANJEPUNTOS');
        } else {
            $retornaUrlWS = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIDELIZACION' . $this->app, 'ESTADOCANJEPUNTOS');
        }
        $this->request->url = $retornaUrlWS["urlwebservice"];
        $this->request->url = $this->request->url . "/" . $redemtionCode;
        // print_r($this->request->url);
        $newGenerateCode = $this->loyaltyTokenManager->generateNewAccessTokenApp($this->idRestaurante, $_SESSION["claveConexion"]);
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            //CURLOPT_POSTFIELDS => "{\r\n  \"type\": \"POINTS REDEMPTION\"\r\n}", // ******* V2 FIDELIZACION *******
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $newGenerateCode,
                "content-type: application/json"
            )
        );
        $this->request->timeout = 60;
        //Clase generica para el consumo de REST
        $this->callREST = new CallREST;
        // funcion call() para consumir ws
        $this->response = $this->callREST->call($this->request);
        return $this->response;
    }
}