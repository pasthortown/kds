<?php
/**
 * TokenLoyalty
 *
 * @uses CallRest, Request, Response, fidelizacion.Cadena
 * @version $1$
 * @author Fabián Quirola
 *
 * Description: Realiza la consulta de las configuraciones para credenciales de las politicas, renueva el token y lo guarda los parametros en sesion si este esta caducado.
 * Creation Date: 26-09-2019
 * Modification Date: 1-10-2019
 *
 **/

//Clases para consumo de web services
$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__)  . $ds . '..') . $ds;
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}CallREST.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Request.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Response.php";
include_once "{$base_dir}{$ds}../../clases{$ds}clase_fidelizacionCadena.php";

class TokenLoyalty
{

    public $callREST;
    public $request;
    public $response;
    public $serviceUrl;
    public $client_id;
    public $client_secret;
    public $grant_type;
    public $company_code;
    public $accessToken;
    public $tokenType;
    public $expiresAt;
    public $app;

    function __construct() {
        date_default_timezone_set("America/Lima");
        setlocale(LC_TIME, "spanish");
        $this->request = new Request;
        $this->response = new Response;
        $this->serviceUrl = new webservice();

        if (empty($_SESSION["appid"])) {
            $this->app = "jvz";
        } else {
            $this->app = $_SESSION["appid"];
        }

        /*
        Este es un workaround que reduce el número de consultas que se realizan
        a las politicas de configuración de fidelización, esto ocurría si la consulta
        a esas tablas colocaba en sesion valores vacíos, para controlar ese comportamiento
        se coloca una bandera que caducará automáticamente luego de 3 minutos, de manera
        que la consulta se realice una unica vez pero manteniendo la posibilidad de que
        las configuraciones se refresquen en cualquier momento con un pequeño retraso.
        */
        if(!isset($_SESSION["loyalty_session_creation_time"])){
            if( isset($_SESSION["client_id"]) && !empty($_SESSION["client_id"]) && $_SESSION["client_id"] !== "" ){
                $this->client_id = $_SESSION["client_id"];
                $this->client_secret =  $_SESSION["client_secret"];
                $this->grant_type = $_SESSION["grant_type"];
            }else{
                $this->getTokenCredentials();
            };
        }else{
            // Tiempo (en segundos) que debe transcurrir antes de que se pueda volver a
            // consultar las credenciales
            $delay=60;

            $momentoInicioPeticion = $_SERVER['REQUEST_TIME'];
            $momentoCreacionSesionPoliticasFidelizacion = $_SESSION["loyalty_session_creation_time"];
            if($momentoInicioPeticion > ($momentoCreacionSesionPoliticasFidelizacion+$delay)){
                unset($_SESSION["loyalty_session_creation_time"]);
            }
        }



        $this->tokenType= "Bearer";
        $this->expiresAt= isset($_SESSION["expiresAt"])?$_SESSION["expiresAt"]:date("Y-m-d H:i:s");
        $this->idRestaurante = $_SESSION['rstId'];
    }


    function requestToken(){
        //Obtener url endpoint
        if ($this->app === "jvz") {
            $retornaUrlWS = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIREBASE', 'TOKEN');
        } else {
            $retornaUrlWS = $this->serviceUrl->retorna_rutaWS($this->idRestaurante, 'FIDELIZACION ' . $this->app, 'TOKEN');
        }
        $this->request->url = $retornaUrlWS["urlwebservice"];

        $data = array (
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret,
            "grant_type" => $this->grant_type
        );

        $dataPS = http_build_query($data);

        //Cabecera solicitud
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $dataPS,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"
            ),
            CURLOPT_RETURNTRANSFER => true
        );

        $this->request->timeout = 60;

        //Clase generica para el consumo de REST
        $this->callREST = new CallREST;
        // funcion call() para consumir ws
        $this->response = $this->callREST->call($this->request);

        return $this->response;
    }
    function checkExpires() {
        $valid = false;

        $expiresAt = date("Y-m-d H:i:s",strtotime($this->expiresAt));

        $now = date("Y-m-d H:i:s");

        if ($now < $expiresAt) {
            $valid = true;
        }
        else{
            $valid = false;
        }

        return $valid;
    }

    function setToken($data){

        $dt = json_decode(stripslashes($data->data));

        $this->accessToken = $dt->accessToken;
        $this->tokenType = $dt->tokenType;
        $this->expiresAt = $dt->expiresAt;
        //cargo en variables de sesion
        $_SESSION["claveConexion"] = $dt->accessToken;
        $_SESSION["expiresAt"] = $dt->expiresAt;

    }

    function getToken(){
        if(!$this->checkExpires()){
            $data = $this->requestToken();
            $this->setToken($data);
        };

        //return $this->accessToken;

    }

    function getTokenCredentials(){
        $this->fidelizacionCadena = new Cadena();
        //Cargo datos para OAuthtoken Fidelizacion
        $respuestaT = $this->fidelizacionCadena->cargarConfiguracionPoliticasToken($_SESSION['cadenaId'], $this->app);
        $respuestaT = json_decode($respuestaT);
        $_SESSION["client_id"] = $respuestaT->client_id;
        $_SESSION["client_secret"] = $respuestaT->client_secret;
        $_SESSION["grant_type"] = $respuestaT->grant_type;
        $_SESSION["loyalty_session_creation_time"] = $_SERVER['REQUEST_TIME'];

        $this->client_id = $respuestaT->client_id;
        $this->client_secret = $respuestaT->client_secret;
        $this->grant_type = $respuestaT->grant_type;

    }

}