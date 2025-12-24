<?php

//Clases para consumo de web services
$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . '..') . $ds;
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}CallREST.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Request.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Response.php";
require_once "{$base_dir}{$ds}../module{$ds}fidelizacion{$ds}Token.php";
require_once "{$base_dir}{$ds}../module{$ds}fidelizacion{$ds}TokenManager.php";

class Credito
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
        $this->tokenLoyalty = new TokenLoyalty();
        $this->loyaltyTokenManager = new TokenManager();
        $this->app = $app;
    }

    function revertir($factura,$origen)
    {   
        $urlWSRetornaPrecios='';
        $urlWSRetornaOrden='';
        $repuesta=null;
        if($origen==1){
            //$urlWSRetornaPrecios = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIREBASE', 'CREDITOPUNTOS'); //jk Cuando se tenga activa el web service para generar nota de credito por compras de puntos.
            echo json_encode(array('str'=>0,'mensaje'=>'No se puede generar un nota crédito en transacción con canje de puntos.'));
            die; 
        }
        else if($origen==2){
            $urlWSRetornaPrecios = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIREBASE', 'CREDITOSALDO');
            $urlWSRetornaOrden =  $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIREBASE', 'CREDITOSACUMULACIONPUNTOS');
        }
        $this->request->url = $urlWSRetornaPrecios['urlwebservice'];
        $newGenerateCode = $this->loyaltyTokenManager->generateNewAccessTokenApp($this->idRestaurante, $_SESSION['claveConexion']);        
        
        $data['balanceRedemptionCode']=$factura;
        $JSON = json_encode($data);

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
            CURLOPT_POSTFIELDS => $JSON,
            CURLOPT_HTTPHEADER => array(
                'authorization: Bearer ' . $newGenerateCode,
                'content-type: application/json'
            ));   
        $this->request->timeout = 60;
        $this->callREST = new CallREST;
        $this->response = $this->callREST->call($this->request);
        $this->response->url=$this->request->url;    
        $respuesta = $this->response;
        if(!empty($urlWSRetornaOrden)){
            if(isset($respuesta->exception)){
                return $respuesta;
            }else{
                if ($respuesta->numberError == 0) {
                    $this->request->url = $urlWSRetornaOrden['urlwebservice'];
                    unset($data); 
                    $data['invoiceCode']=$factura;
                    $JSON = json_encode($data);
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
                        CURLOPT_POSTFIELDS => $JSON,
                        CURLOPT_HTTPHEADER => array(
                            'authorization: Bearer ' . $newGenerateCode,
                            'content-type: application/json'
                        ));   
                    $this->request->timeout = 60;
                    $this->callREST = new CallREST;
                    $this->response = $this->callREST->call($this->request);
                    $this->response->url=$this->request->url;
                    return $this->response;
                }            
            }
        }
        return $this->response;
    }

    function revertir_puntos($factura)
    {   
        $urlWSRetornaOrden =  $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIREBASE', 'CREDITOSACUMULACIONPUNTOS');
        $this->request->url = $urlWSRetornaOrden['urlwebservice'];
        $newGenerateCode = $this->loyaltyTokenManager->generateNewAccessTokenApp($this->idRestaurante, $_SESSION['claveConexion']);        
        $data['invoiceCode']=$factura;
                    $JSON = json_encode($data);
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
                        CURLOPT_POSTFIELDS => $JSON,
                        CURLOPT_HTTPHEADER => array(
                            'authorization: Bearer ' . $newGenerateCode,
                            'content-type: application/json'
                        ));   
                    $this->request->timeout = 60;
                    $this->callREST = new CallREST;
                    $this->response = $this->callREST->call($this->request);
                    $this->response->url=$this->request->url;
                    return $this->response;
    }
}