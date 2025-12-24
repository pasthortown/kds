<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . "..") . $ds;
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}CallREST.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Request.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Response.php";
include_once "{$base_dir}{$ds}../../clases{$ds}clase_webservice.php";

//$_SESSION["ContrasenaWebServicesFidelizacion"];
include_once "{$base_dir}{$ds}../module{$ds}fidelizacion{$ds}Token.php";
include_once "{$base_dir}{$ds}../module{$ds}fidelizacion{$ds}TokenManager.php";

class RecargaWS
{
    public $idCadena;
    public $idRestaurante;
    public $codTienda;
    public $cashierDocument;
    public $cashierName;
    public $callREST;
    public $request;
    public $response;
    public $serviceUrl;
    public $tokenLoyalty;
    public $loyaltyTokenManager;
    public $app;

    function __construct($idCadena, $idRestaurante, $app, $codTienda, $cashierDocument, $cashierName)
    {
        $this->idCadena = $idCadena;
        $this->idRestaurante = $idRestaurante;
        $this->app = $app;
        $this->codTienda = $codTienda;
        $this->cashierDocument = $cashierDocument;
        $this->cashierName = $cashierName;
        $this->request = new Request;
        $this->response = new Response;
        $this->serviceUrl = new webservice();
        $this->tokenLoyalty = new TokenLoyalty();
        $this->loyaltyTokenManager = new TokenManager();
    }

    function realizarRecargaBody($codigoIngreso, $valor, $clienteTipoDocumento, $clienteDocumento, $clienteNombre)
    {

        $body = array(
            'storeId' => $this->idRestaurante,
            'storeCode' => $this->codTienda,
            'vendorId' => $this->idCadena,
            'reloadBalanceCode' => $codigoIngreso,
            'total' => $valor,
            'invoiceCode' => '0',
            'customer' => array(
                'documentType' => $clienteTipoDocumento,
                'document' => $clienteDocumento,
                'name' => $clienteNombre,
                'address' => '',
                'uid'=>isset($_SESSION['uid'])? $_SESSION['uid'] : '' ),              
            'cashier' => array(
                'document' => $this->cashierDocument,
                'name' => $this->cashierName)
        );
        return $body;
    }

    /*  function realizarRecargaBody($codigoIngreso, $valor, $clienteTipoDocumento, $clienteDocumento, $clienteNombre , $clienteDireccion) { // V2
              $body = array(
                  "storeId" => $this->idRestaurante,//555
                  "storeCode" => $this->codTienda, //V030
                  "vendorId" => "12",//strval($this->idCadena),  //12  $this->idCadena
                  "reloadBalanceCode" => $codigoIngreso,
                  "total" => $valor,
                  "customer" => array(
                      "documentType" => $clienteTipoDocumento,
                      "document" => $clienteDocumento,
                      "name" => $clienteNombre,
                      "address" => $clienteDireccion
                  ),
              "cashier" => array(
                  "document" => $this->cashierDocument,
                  "name" => $this->cashierName)
              );
              print_r($body);
          return $body;

      }*/

    function realizarRecarga($body, $claveConexion)
    {

        // $this->tokenLoyalty->getToken(); // ******* V2 FIDELIZACION *******
        if ($this->app === "jvz") {
            if(isset($_SESSION['canje_v2']) && !empty($_SESSION['canje_v2']) && trim($_SESSION['canje_v2'])==1){
                $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIREBASE', "EFECTIVOV2");
            }
            else{
                $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'RECARGAS', 'EFECTIVO');
            }
        } else {
            $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "RECARGAS " . $this->app, "EFECTIVO");
        }          
        $this->request->url = $urlServicio["urlwebservice"];
        $newTokenLogin = $this->loyaltyTokenManager->generateNewAccessTokenApp($this->idRestaurante, $claveConexion);

        $this->request->timeout = 60;
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_CONNECTTIMEOUT => 25,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => array(
                'authorization: Bearer ' . $newTokenLogin,
                'content-type: application/json',
                'accept: application/json'
            )
        );
        //Clase genérica para el consumo de REST
        $this->callREST = new CallREST;
        $this->response = $this->callREST->call($this->request);
        $this->response->url=$urlServicio;
        $this->response->body=$body;
        $this->response->newTokenLogin=$newTokenLogin;
        return $this->response;
    }

    function consultarEstadoRecarga($codigoIngreso, $claveConexion)
    {
        //Verifica Token
        //$this->tokenLoyalty->getToken(); // ******* V2 FIDELIZACION *******
        if ($this->app === 'jvz') {
            if(isset($_SESSION['canje_v2']) && !empty($_SESSION['canje_v2']) && trim($_SESSION['canje_v2'])==1){
                $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIREBASE', "EFECTIVOV2");
            }
            else{
                $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "RECARGAS", "EFECTIVO");
            }            
        } else {
            $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "RECARGAS " . $this->app, "EFECTIVO");
        }

        $this->request->url = $urlServicio["urlwebservice"] . "/" . $codigoIngreso;
        $newTokenLogin = $this->loyaltyTokenManager->generateNewAccessTokenApp($this->idRestaurante, $claveConexion);
        $this->request->timeout = 60;
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            //CURLOPT_POSTFIELDS => "{\r\n  \"type\":\"BALANCE RELOAD\"\r\n}", //******* V2 FIDELIZACION *******
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $newTokenLogin
            ));
        //Clase genérica para el consumo de REST
        $this->callREST = new CallREST;
        $this->response = $this->callREST->call($this->request);
        $this->response->url=$urlServicio;
        $this->response->body=$codigoIngreso;
        return $this->response;
    }

    function consumirRecargaBody($secuencialRecarga, $idFactura, $clienteCodigoSeguridad, $valor, $clienteTipoDocumento, $clienteDocumento, $clienteNombre)
    {
        $body = array(
            'storeId' => $this->idRestaurante,
            'storeCode' => $this->codTienda,
            'vendorId' => $this->idCadena,
            'balanceRedemptionCode' => $secuencialRecarga,
            'token' => $clienteCodigoSeguridad,
            'invoiceCode' => $idFactura,
            'total' => $valor,
            'customer' => array(
                'documentType' => $clienteTipoDocumento,
                'document' => $clienteDocumento,
                'name' => $clienteNombre,
                'address' => ''
            ),
            'cashier' => array(
                'document' => $this->cashierDocument,
                'name' => $this->cashierName)
        );
        return $body;
    }

    function consumirRecarga($body,$claveConexion)
    {
        
        if ($this->app === 'jvz') {
            if(isset($_SESSION['canje_v2']) && !empty($_SESSION['canje_v2']) && trim($_SESSION['canje_v2'])==1){
                $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'RECARGAS', 'CONSUMOV2');
            }
            else{
                $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'RECARGAS', 'CONSUMO');
            }
        } else {
            $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'RECARGAS ' . $this->app, 'CONSUMO');
        }
        $this->request->url = $urlServicio['urlwebservice'];

        $newTokenLogin = $this->loyaltyTokenManager->generateNewAccessTokenApp($this->idRestaurante, $claveConexion);
        
        $this->request->timeout = 60;
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $newTokenLogin,
                'content-type: application/json'
            )
        );
        //Clase genérica para el consumo de REST
        $this->callREST = new CallREST;
        $this->response = $this->callREST->call($this->request);
        $this->response->url=$urlServicio;
        $this->response->body=$body;
        $this->response->token=$newTokenLogin;
        return $this->response; 
    }

    function consultarEstadoConsumoRecarga($secuencialRecarga, $claveConexion)
    {

        //$this->tokenLoyalty->getToken(); ******* V2 FIDELIZACION *******
        if ($this->app === "jvz") {
            $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "RECARGAS", "CONSUMO");
        } else {
            $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "RECARGAS " . $this->app, "CONSUMO");
        }
        $this->request->url = $urlServicio["urlwebservice"] . "/" . $secuencialRecarga;
        $newTokenLogin = $this->loyaltyTokenManager->generateNewAccessTokenApp($this->idRestaurante, $claveConexion);
        $this->request->timeout = 60;
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            // CURLOPT_POSTFIELDS => "{\r\n  \"type\": \"BALANCE REDEPTION\"\r\n}", // ******* V2 FIDELIZACION *******
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $newTokenLogin
            ));

        //Clase genérica para el consumo de REST
        $this->callREST = new CallREST;
        $this->response = $this->callREST->call($this->request);

        return $this->response;
    }

    function reversarRecargaBody($secuencialConsumo)
    {
        $body = array(
            /*"type"=>"BALANCE REDEMPTION REVERSE",
            "code" => $secuencialConsumo */// ******* V2 FIDELIZACION *******
            "balanceRedemptionCode" => $secuencialConsumo //******* V1 FIDELIZACION *******

        );

        return $body;
    }

    function reversarRecarga($body, $claveConexion)
    {

        //$this->tokenLoyalty->getToken(); // ******* V2 FIDELIZACION *******
        if ($this->app === "jvz") {
            $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "RECARGAS", "REVERSO");
        } else {
            $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "RECARGAS " . $this->app, "REVERSO");
        }
        $this->request->url = $urlServicio["urlwebservice"];
        $newTokenLogin = $this->loyaltyTokenManager->generateNewAccessTokenApp($this->idRestaurante, $claveConexion);
        $this->request->timeout = 60;
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $newTokenLogin,
                "content-type: application/json"
            )
        );

        //Clase genérica para el consumo de REST
        $this->callREST = new CallREST;
        $this->response = $this->callREST->call($this->request);

        return $this->response;
    }

    function reversarPickupBody($secuencialConsumo)
    {
        $body = array(
            "type" => "BALANCE REDEMPTION REVERSE",  //cambiar por nuevo type de TRADE
            "code" => $secuencialConsumo           //cambiar por nuevo code de TRADE
        );

        return $body;
    }

    function reversarPickup($body, $claveConexion)
    {

        //$this->tokenLoyalty->getToken(); // ******* V2 FIDELIZACION *******
        //$urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "RECARGAS", "REVERSO");
        $urlServicio = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, "PICKUP", "REVERSO");
        $this->request->url = $urlServicio["urlwebservice"];
        $newTokenLogin = $this->loyaltyTokenManager->generateNewAccessTokenApp($this->idRestaurante, $claveConexion);
        $this->request->timeout = 60;
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $newTokenLogin,
                "content-type: application/json"
            )
        );

        //Clase genérica para el consumo de REST
        $this->callREST = new CallREST;
        $this->response = $this->callREST->call($this->request);

        return $this->response;
    }

}