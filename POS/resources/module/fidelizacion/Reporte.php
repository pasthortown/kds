<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__)  . $ds . '..') . $ds;
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}CallREST.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Request.php";
include_once "{$base_dir}{$ds}../models{$ds}webservices{$ds}Response.php";

include_once "../../clases/clase_webservice.php";
include_once "../../clases/clase_fidelizacionCadena.php";

include_once '../../seguridades/AesEncryption.php';
include_once "../../clases/clase_seguridades.php";

class Reporte {

    public $idRestaurante;
    public $idCadena;
    public $callREST;
    public $request;
    public $response;
    public $serviceUrl;

    function __construct($idRestaurante) {
        $this->idRestaurante = $idRestaurante;
        $this->request = new Request;
        $this->response = new Response;
        $this->serviceUrl = new webservice();
    }

    /*Metodo para consultar transacciones por locales consolidado*/
    function cargarTransacciones($startDate, $endDate, $token) {

        //Obtener url endpoint
        $datos = array(5);
        $datos[0] = $this->idRestaurante;
        $datos[1] = 'REPORTES FIDELIZACION';
        $datos[2] = 'TRANSACCIONES';
        $datos[3] = 0;
        $datos[4] = $this->idCadena;
        $urlServicio = $this->serviceUrl->retorna_Direccion_Webservice_Cadena($datos);
        $this->request->url = $urlServicio["urlwebservice"];
        //URLservicio con parametros
        $this->request->url .= "?startDate=" . $startDate . "&endDate=" . $endDate;

        //Cabecera solicitud
        $this->request->headers = array (
            CURLOPT_URL => $this->request->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            // CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array ("Authorization: " . $token, "content-type: application/json"));
        $this->request->timeout = 60;

        //Clase generica para el consumo de REST
        $this->callREST = new CallREST;
        // funcion call() para consumir ws
        $this->response = $this->callREST->call($this->request);

        return $this->response;
    }

    /*Metodo para consultar transacciones productos por locales consolidado*/
    function cargarTransaccionesProducto($startDate, $endDate, $token) {

        //Obtener url endpoint
        $datos = array(5);
        $datos[0] = $this->idRestaurante;
        $datos[1] = 'REPORTES FIDELIZACION';
        $datos[2] = 'PRODUCTOS';
        $datos[3] = 0;
        $datos[4] = $this->idCadena;
        $urlServicio = $this->serviceUrl->retorna_Direccion_Webservice_Cadena($datos);
        $this->request->url = $urlServicio["urlwebservice"];
        //URLservicio con parametros
        $this->request->url .= "?startDate=" . $startDate . "&endDate=" . $endDate;

        //Cabecera solicitud
        $this->request->headers = array (
            CURLOPT_URL => $this->request->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            // CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array ("Authorization: " . $token, "content-type: application/json"));
        $this->request->timeout = 60;

        //Clase generica para el consumo de REST
        $this->callREST = new CallREST;
        // funcion call() para consumir ws
        $this->response = $this->callREST->call($this->request);

        return $this->response;
    }

    /*Metodo para consultar transacciones por locales consolidado*/
    function solicitarTokenSeguridad($idCadena) {
        $cadena = new Cadena();
        $clave = $cadena->cargarTokenSeguridad($idCadena);

        //  $AesEncrypt = new AESEncriptar();
        //$lc_seguridad = new seguridadesUsuarioPerfilPeriodo();
        //$respuestaJSON = $lc_seguridad->obtenerClavesSeguridad(4, 'Fidelizacion', $idCadena);
        //$arregloRepuesta = json_decode($respuestaJSON);
        //$password = $arregloRepuesta->clave;
        //$key = 'd480863a4dbd1b245608b9d28c2fc02cdbb2443e1bcb19d0df82d67ab6ba60c9';
        //$FidelizacionClaveSeguridadWebS = $AesEncrypt->DesencriptarDatos($password, $key);
        //$_SESSION["ContrasenaWebServicesFidelizacion"] = $FidelizacionClaveSeguridadWebS;

        //Obtener url endpoint
        $datos = array(5);
        $datos[0] = $this->idRestaurante;
        $datos[1] = 'REPORTES FIDELIZACION';
        $datos[2] = 'SEGURIDAD';
        $datos[3] = 0;
        $datos[4] = $idCadena;
        $urlServicio = $this->serviceUrl->retorna_Direccion_Webservice_Cadena($datos);
        $this->request->url = $urlServicio["urlwebservice"];


        //Cabecera solicitud
        $this->request->headers = array (
            CURLOPT_URL => $this->request->url,
            // CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => "client_id=2&client_secret=" . $clave["claveSeguridad"] . "&grant_type=client_credentials",
            // CURLOPT_ENCODING => "",
            // CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            // CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_FOLLOWLOCATION => true,
            // CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array ("Content-Type: application/x-www-form-urlencoded"),
            CURLOPT_RETURNTRANSFER => true
        );
        $this->request->timeout = 60;
        // print_r($this->request->headers);

        //Clase generica para el consumo de REST
        $this->callREST = new CallREST;
        // funcion call() para consumir ws
        $this->response = $this->callREST->call($this->request);
        // print_r($this->response);

        return $this->response;
    }

    function solicitarTokenSeguridadConsumo($idCadena)
    {
        $cadena = new Cadena();
        $clave = $cadena->cargarTokenSeguridad($idCadena);

        $datos = array(5);
        $datos[0] = $this->idRestaurante;
        $datos[1] = 'REPORTES FIDELIZACION';
        $datos[2] = 'SEGURIDAD';
        $datos[3] = 0;
        $datos[4] = $idCadena;
        $urlServicio = $this->serviceUrl->retorna_Direccion_Webservice_Cadena($datos);
        $this->request->url = $urlServicio["urlwebservice"];

        //  $AesEncrypt = new AESEncriptar();
        //$lc_seguridad = new seguridadesUsuarioPerfilPeriodo();
        //$respuestaJSON = $lc_seguridad->obtenerClavesSeguridad(4, 'Fidelizacion', $idCadena);
        //$arregloRepuesta = json_decode($respuestaJSON);
        //$password = $arregloRepuesta->clave;
        //$key = 'd480863a4dbd1b245608b9d28c2fc02cdbb2443e1bcb19d0df82d67ab6ba60c9';
        //$FidelizacionClaveSeguridadWebS = $AesEncrypt->DesencriptarDatos($password, $key);
        //$_SESSION["ContrasenaWebServicesFidelizacion"] = $FidelizacionClaveSeguridadWebS;

        //Cabecera solicitud
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => "client_id=2&client_secret=" . $clave["claveSeguridad"] . "&grant_type=client_credentials",
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array("Content-Type: application/x-www-form-urlencoded"),
            CURLOPT_RETURNTRANSFER => true
        );
        $this->request->timeout = 60;

        //Clase generica para el consumo de REST
        $this->callREST = new CallREST;
        // funcion call() para consumir ws
        $this->response = $this->callREST->call($this->request);
        // print_r($this->response);

        return $this->response;
    }

    function setIdCadena($idCadena) {
        $this->idCadena = $idCadena;
    }


    function cargarReportesFidelizacion($nombreWS, $token)
    {

        //Obtener url endpoint
        $datos = array();
        $datos[0] = $this->idRestaurante;
        $datos[1] = 'REPORTES FIDELIZACION';
        $datos[2] = 'CONSUMOS';
        $datos[3] = 0;
        $datos[4] = $this->idCadena;
        $urlServicio = $this->serviceUrl->retorna_Direccion_Webservice_Cadena($datos);
        $this->request->url = $urlServicio["urlwebservice"];
        //URLservicio con parametros
        $this->request->url .= "/" . $nombreWS;


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
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            // CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array("Authorization: " . $token, "content-type: application/json"));
        $this->request->timeout = 60;

        $this->callREST = new CallREST;

        $this->response = $this->callREST->call($this->request);
        return $this->response;
    }

}