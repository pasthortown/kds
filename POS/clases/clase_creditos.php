<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . '..') . $ds;
include_once "{$base_dir}resources{$ds}models{$ds}webservices{$ds}CallREST.php";
include_once "{$base_dir}{$ds}resources{$ds}models{$ds}webservices{$ds}Request.php";
include_once "{$base_dir}{$ds}resources{$ds}models{$ds}webservices{$ds}Response.php";

include_once "../clases/clase_webservice.php";
include_once "../clases/clase_creditosCadena.php";
include_once "../clases/clase_creditosAuditoria.php";
include_once '../seguridades/AesEncryption.php';
include_once "../clases/clase_seguridades.php";


class Creditos {
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

    function AutenticarTokenSeguridadVitality($idCadena, $idUsuario) {
        $cadena = new Cadenas();
        $clave = $cadena->cargarTokenSeguridadVitality($idCadena);
        //Obtener url endpoint
        $datos = array();
        $datos[0] = $this->idRestaurante;
        $datos[1] = 'VITALITY';
        $datos[2] = 'SEGURIDAD';
        $datos[3] = 0;
        // // var_dump("Bearer ".$_SESSION["ContrasenaWebServicesVitality"]);
        $urlServicio = $this->serviceUrl->retorna_Direccion_Webservice($datos);


        $this->request->url = $urlServicio["urlwebservice"];

        //Cabecera solicitud  
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_POST => 1,
            // // var_dump("Bearer ".$_SESSION["ContrasenaWebServicesVitality"]);
            CURLOPT_POSTFIELDS => "client_id=1&client_secret=" . $clave["claveSeguridad"] . "&grant_type=client_credentials",
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array("Content-Type: application/x-www-form-urlencoded"),
            CURLOPT_RETURNTRANSFER => true
        );

        $this->request->timeout = 60;
        //Clase generica para el consumo de REST
        $this->callREST = new CallREST;
        // funcion call() para consumir ws
        $this->response = $this->callREST->call($this->request);
        // print_r($this->response);

        $auditoriasC = new AuditoriaCreditos();
        if ($this->response->httpStatus == 200) {
            $auditoriasC->guardarLogCreditos('Token Seguridad Politica:' . $clave["claveSeguridad"], 'OBTENER TOKEN VITALITY', $datos[1], $this->idRestaurante, $this->idCadena, $idUsuario, json_encode($this->response));
        } else {
            $auditoriasC->guardarLogCreditos('Error WS Autorizacion Token: ' . $clave["claveSeguridad"], 'OBTENER TOKEN VITALITY', $datos[1], $this->idRestaurante, $this->idCadena, $idUsuario, json_encode($this->response));
        }

        return $this->response;
    }

    function ValidarVoucherVitality($codigoVitality, $tokenSeguridadV, $idUsuario) {
        $datos = array();
        $datos[0] = $this->idRestaurante;
        $datos[1] = 'VITALITY';
        $datos[2] = 'CANJE';
        $datos[3] = 0;
        //Obtener url endpoint
        $urlServicio = $this->serviceUrl->retorna_Direccion_Webservice($datos);
        $this->request->url = $urlServicio["urlwebservice"];
       

        //URLservicio con parametros

        //Cabecera solicitud
        $this->request->headers = array(
            CURLOPT_URL => $this->request->url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => "code=$codigoVitality",
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                // // var_dump("Bearer ".$_SESSION["ContrasenaWebServicesVitality"]);
                "Authorization: $tokenSeguridadV",
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"
            ),
            CURLOPT_RETURNTRANSFER => true
        );
        $this->request->timeout = 60;
        $this->callREST = new CallREST;
        $this->response = $this->callREST->call($this->request);

        $auditoriasC = new AuditoriaCreditos();
        if ($this->response->httpStatus == 200) {
            $auditoriasC->guardarLogCreditos('Cupon: ' . $codigoVitality, 'VALIDAR INFORMACION CUPON ', $datos[1], $this->idRestaurante, $this->idCadena, $idUsuario, json_encode($this->response));
        } else {
            $auditoriasC->guardarLogCreditos('Error Cupon :' . $codigoVitality, 'VALIDAR INFORMACION CUPON ', $datos[1], $this->idRestaurante, $this->idCadena, $idUsuario, json_encode($this->response));
        }
        return $this->response;
    }

    function setIdCadena($idCadena) {
        $this->idCadena = $idCadena;
    }

    function obtenerIdClienteExterno($idCiudad, $nombre, $apellido, $tipoDocumento, $documento, $telefono, $direccion, $email, $tipoCliente, $idUsuario) {
        $cadena = new Cadenas();
        return $cadena->obtenerIdClienteExterno($idCiudad, $nombre, $apellido, $tipoDocumento, $documento, $telefono, $direccion, $email, $tipoCliente, $idUsuario);
    }

    function voucherTransaccion($codigoVoucher, $tokenSeguridadV, $json, $idUsuario) {
        $datos = array();
        $datos[0] = $this->idRestaurante;
        $datos[1] = 'VITALITY';
        $datos[2] = 'TRANSACCIONES';
        $datos[3] = 0;
        //Obtener url endpoint
        $urlServicio = $this->serviceUrl->retorna_Direccion_Webservice($datos);
        $this->request->url = $urlServicio["urlwebservice"];
        if ($codigoVoucher !== null) {
            $this->request->headers = array(
                CURLOPT_URL => $this->request->url,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $json,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    // // var_dump("Bearer ".$_SESSION["ContrasenaWebServicesVitality"]);
                    "Authorization: $tokenSeguridadV",
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
                CURLOPT_RETURNTRANSFER => true
            );
            $this->request->timeout = 60;
            $auditoriasC = new AuditoriaCreditos();
            $this->callREST = new CallREST;
            $this->response = $this->callREST->call($this->request);

            if ($this->response->httpStatus == 200) {
                $auditoriasC->guardarLogCreditos('Cupon: ' . $codigoVoucher, 'CANJEAR CUPON ', $datos[1], $this->idRestaurante, $this->idCadena, $idUsuario, json_encode($this->response));

            } else {
                $auditoriasC->guardarLogCreditos('Error Cupon:' . $codigoVoucher, 'CANJEAR CUPON', $datos[1], $this->idRestaurante, $this->idCadena, $idUsuario, json_encode($this->response));

            }
            return $this->response;

        } else {
            return $this->response;
        }
    }


    function consultarEstadoTransaccionVitality($CodigoFactura, $codigoVitality, $tokenSeguridadV, $idUsuario)
    {
        $datos = array();
        $datos[0] = $this->idRestaurante;
        $datos[1] = 'VITALITY';
        $datos[2] = 'TRANSACCIONES LOG';
        $datos[3] = 0;
        $urlServicio = $this->serviceUrl->retorna_Direccion_Webservice($datos);
        $this->request->url = $urlServicio["urlwebservice"] . $CodigoFactura;


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
            CURLOPT_HTTPHEADER => array(
                // // var_dump("Bearer ".$_SESSION["ContrasenaWebServicesVitality"]);
                "Authorization: " . $tokenSeguridadV,
                "content-type: application/json"));
        $this->request->timeout = 60;

        $this->callREST = new CallREST;
        $this->response = $this->callREST->call($this->request);
        $auditoriasC = new AuditoriaCreditos();

        if ($this->response->httpStatus == 200) {
            $auditoriasC->guardarLogCreditos('Factura:' . $CodigoFactura . '-' . $codigoVitality, 'ESTADO TRANSACCION VITALITY', $datos[1], $this->idRestaurante, $this->idCadena, $idUsuario, json_encode($this->response));
            $auditoriasC->ingresarCodigoVitalityFactura($CodigoFactura, $codigoVitality, json_encode($this->response));
        } else {
            $auditoriasC->guardarLogCreditos('Error Ws Estado Factura', 'ESTADO TRANSACCION VITALITY', $datos[1], $this->idRestaurante, $this->idCadena, $idUsuario, json_encode($this->response));
            $auditoriasC->ingresarCodigoVitalityFactura($CodigoFactura, $codigoVitality, json_encode($this->response));

        }
        return $this->response;
    }


}

