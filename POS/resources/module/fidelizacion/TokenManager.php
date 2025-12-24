<?php
/**
 * TokenLoyaltyManager
 *
 * @uses CallRest, Request, Response, fidelizacion.Cadena
 * @version $1$
 * @author Joy Lino
 *
 * Description: Realiza la consulta de las configuraciones para credenciales de las politicas, renueva el token y lo guarda los parametros en sesion si este esta caducado.
 * Creation Date: 26-09-2019
 * Modification Date: 1-10-2019
 *
 **/
$currentDirectory = getcwd();
chdir(__DIR__);
require_once '../../../vendor/autoload.php';
chdir($currentDirectory);
//Clases para consumo de web services
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . '..') . $ds;
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}CallREST.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Request.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Response.php";
include_once "{$base_dir}{$ds}../../clases{$ds}clase_fidelizacionCadena.php";


class TokenManager
{

    public $callREST;
    public $serviceUrl;
    public $app;
    public $request;
    public $response;

    function __construct()
    {
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
    }


    function generateNewAccessTokenApp($restaurante, $currentAccessToken)
    {
        //Obtener url endpoint
        $credentials = $this->getCredentialsSecurity(true);
        $expiredPreviousCredentials = $credentials['expired'];
        if (!$expiredPreviousCredentials && isset($_SESSION['authorizationTradeAppFidelizacion'])) {
            return $_SESSION['authorizationTradeAppFidelizacion'];
        }
        if ($this->app === 'jvz') {
            if(isset($_SESSION['canje_v2']) && !empty($_SESSION['canje_v2']) && trim($_SESSION['canje_v2'])==1){
                $retornaUrlWS = $this->serviceUrl->retorna_rutaWS($restaurante, 'FIREBASE', 'TOKENV2');
            }
            else{
                $retornaUrlWS = $this->serviceUrl->retorna_rutaWS($restaurante, 'FIREBASE', 'TOKEN');
            } 
        } else {
            $retornaUrlWS = $this->serviceUrl->retorna_rutaWS($restaurante, 'FIDELIZACION ' . $this->app, 'TOKEN');
        }
        $url = $retornaUrlWS['urlwebservice'];
        $data = array(
            'client_id' => $credentials['client_id'],
            'client_secret' => $credentials['client_secret'],
            'grant_type' => $credentials['grant_type']
        );

        $this->request->url = $url;
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
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'authorization: ' . $currentAccessToken,
                'content-type: application/json'
            )
        );
        //Clase genérica para el consumo de REST
        $this->callREST = new CallREST;
        $this->response = $this->callREST->call($this->request);
        $dataDecoded = json_decode($this->response->data);
        if ($dataDecoded) {
            $accessToken = $dataDecoded->accessToken;
            $this->saveTokenInSession($accessToken);
            return $accessToken;
        }
        return null;
    }

    /**
     * @throws Exception Lanza excepciones cuando no se encuentra las credenciales en la base de datos
     */
    function getClaveJwt()
    {
        $credentials = $this->getCredentialsSecurity(true);
        $expiredPreviousCredentials = $credentials['expired'];
        if (!$expiredPreviousCredentials && isset($_SESSION["fidelizacion_trade_clavejwt"])) {
            return $_SESSION["fidelizacion_trade_clavejwt"];
        }
        if (isset($credentials['clave_jwt_trade'])) {
            return $credentials['clave_jwt_trade'];
        }
        return null;
    }

    /**
     * @throws Exception getClaveJwt (Lanza excepcion)
     */
    function decodeValidJwt($jwt){
        // Se hace la validación del JWT caso contrario retornará un error
        $resultado = array(
            "status" => false,
            "error" => "El codigo QR esta vacio"
        );
        if(empty($jwt)){
            return $resultado;
        }
        $claveJwtDecode = $this->getClaveJwt();
        try {
            $decoded = JWT::decode(trim($jwt),trim($claveJwtDecode), array('HS256'));
            $resultado["status"] = true;
            $resultado["data"] = $decoded;
        } catch(SignatureInvalidException $ex){
            $resultado["error"] = "No se confirmó la autenticidad del código QR. Solicitar al cliente cerrar y abrir la aplicación.";
        } catch(BeforeValidException $ex){
            $resultado["error"] = "El Código QR aún no puede ser utilizado. Solicitar al cliente cerrar y abrir la aplicación";
        } catch(ExpiredException $ex){
            $resultado["error"] = "El Código QR ha expirado. Solicitar al cliente cerrar y abrir la aplicación.";
        } catch(UnexpectedValueException $ex){
            $resultado["error"] = "El Código QR no esta correctamente formado. Solicitar al cliente cerrar y abrir la aplicación.";
        } catch(Exception $ex){
            $resultado["error"] = $ex->getMessage();
        }
        return $resultado;
    }

    /**
     * @throws Exception lanza excepcion cuando no se encuentra las politicas en la base de datos
     */
    function getCredentialsSecurity($saveInSession = false)
    {
        $cadena = new Cadena();
        $today = date("Y-m-d H:i:s");
        $tokenExpireIn = isset($_SESSION["fidelizacion_trade_expireIn"]) ? $_SESSION["fidelizacion_trade_expireIn"] : null;
        if ($tokenExpireIn != null) {
            if ($tokenExpireIn > $today) { // Aun no ha pasado el tiempo de expiración
                return array(
                    'client_id' => $_SESSION["fidelizacion_trade_client_id"],
                    'client_secret' => $_SESSION["fidelizacion_trade_client_secret"],
                    'grant_type' => $_SESSION["fidelizacion_trade_grant_type"],
                    'clave_jwt_trade' => $_SESSION["fidelizacion_trade_clavejwt"],
                    'expired' => false
                );
            }
        }
        //Cargo datos para OAuthtoken Fidelizacion
        $respuestaOAuth = $cadena->cargarConfiguracionPoliticasToken($_SESSION['cadenaId'], $this->app);
        $respuestaOAuth = json_decode($respuestaOAuth, true);
        if (!isset($respuestaOAuth['client_id'])) {
            throw new Exception('No se encontró credenciales para fidelización en Trade, revise TokenManager.php');
        }
        if(!isset($respuestaOAuth['key_client_jwt'])){
            throw new Exception("No se encontró la clave de key_client_jwt en politicas de cadena");
        }
        // Cargar JWT DECODE para guardarlo en sesion
        if ($saveInSession) {
            $_SESSION["fidelizacion_trade_client_id"] = $respuestaOAuth['client_id'];
            $_SESSION["fidelizacion_trade_client_secret"] = $respuestaOAuth['client_secret'];
            $_SESSION["fidelizacion_trade_grant_type"] = $respuestaOAuth['grant_type'];
            $expire = date("Y-m-d H:i:s", strtotime('+1 hour'));
            $_SESSION["fidelizacion_trade_expireIn"] = $expire;
            $_SESSION["fidelizacion_trade_clavejwt"] = $respuestaOAuth['key_client_jwt'];
        }
        return array(
            'client_id' => $respuestaOAuth['client_id'],
            'client_secret' => $respuestaOAuth['client_secret'],
            'grant_type' => $respuestaOAuth['grant_type'],
            'clave_jwt_trade' => $respuestaOAuth['key_client_jwt'],
            'expired' => true
        );
    }


    function saveTokenInSession($token)
    {
        //cargo en variables de sesion
        if (isset($token)) {
            $_SESSION["authorizationTradeAppFidelizacion"] = $token;
        }
    }

}