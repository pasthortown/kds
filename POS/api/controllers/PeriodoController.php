<?php

require_once __DIR__ . '/../../system/conexion/clase_sql.php';
include_once __DIR__ . '/../../clases/clase_seguridades.php';
include_once __DIR__ . '/../../clases/clase_apertura.php';

class PeriodoController {

    private $lc_seguridad;
    private $lc_apertura;

    public function __construct() {
        $this->lc_seguridad = new seguridadesUsuarioPerfilPeriodo();
        $this->lc_apertura = new apertura();
    }


    public function crearPeriodo() {

        header('Content-Type: application/json');
        $dataresponse = [];

        $data = json_decode(file_get_contents("php://input"), true);

        $est_ip = isset($data['est_ip']) && trim($data['est_ip']) !== '' ? $data['est_ip'] : null;
        $fechaAperturaPeriodo = isset($data['fechaAperturaPeriodo']) && trim($data['fechaAperturaPeriodo']) !== '' ? $data['fechaAperturaPeriodo'] : null;
        $usr_clave = isset($data['usr_clave']) && trim($data['usr_clave']) !== '' ? $data['usr_clave'] : null;

        if ($est_ip == null or $usr_clave == null){

            $responseData = [
                'success' => false,
                'message' => "Todos los campos son necesarios",
                'data' => $dataresponse
            ];
            return json_encode($responseData);
        }

        $lc_condiciones[0] = 2;
        $lc_condiciones[1] = $est_ip;
        $lc_condiciones[2] = $fechaAperturaPeriodo;

        $response = $this->lc_seguridad->fn_consultar('validaAperturaPeriodo', $lc_condiciones);
        $response = json_decode($response, true);

        if (!$response['respuesta'] == 1){

            if (date('H:i') < $response['horaInicio']) {  
                $responseData = [
                    'success' => false,
                    'message' => "No puede abrir un nuevo Periodo, su Horario de Apertura es: ".$response['horaInicio'],
                    'data' => $response
                ];
                return json_encode($responseData);
            }

            $responseData = [
                'success' => false,
                'message' => (isset($response['mensajeCierrePeriodo']))
                ? $response['mensajeCierrePeriodo']
                : "Error apertura periodo",
                'data' => $response
            ];
            return json_encode($responseData);
        }

        // Valida periodo de heladeria
        if (isset($response['periodoAbiertoHeladeria']) and $response['periodoAbiertoHeladeria'] == 1){
            $responseData = [
                'success' => false,
                'message' => $response['mensajePeriodoAbiertoHeladeria'],
                'data' => $response
            ];
            return json_encode($responseData);
        }

        $lc_condiciones[0] = 1;
        $lc_condiciones[1] = $usr_clave;
        $lc_condiciones[2] = $est_ip;
        $response = $this->lc_seguridad->fn_consultar('validaUsuarioPerfil', $lc_condiciones);
        $response = json_decode($response, true);

        if (!$response[0]['existeusuario'] == 1){
            $responseData = [
                'success' => false,
                'message' => "Error, El usuario no existe o ip estacion no existe",
                'data' => $response
            ];
            return json_encode($responseData);
        }

        $rst_id = $response[0]['rst_id'];

        $lc_condiciones[0] = 3;
        $lc_condiciones[1] = $usr_clave;
        $lc_condiciones[2] = $est_ip;
        $response = $this->lc_seguridad->fn_consultar('traerUsuario', $lc_condiciones);
        $response = json_decode($response, true);

        if (!isset($response) or $response == ''){
            $responseData = [
                'success' => false,
                'message' => "Error al consultar usuario",
                'data' => $response
            ];
            return json_encode($responseData);
        }

        $usr_id = $response["usr_id"]; 

        $lc_condiciones[0] = 4;
        $lc_condiciones[1] = $usr_clave;
        $lc_condiciones[2] = $est_ip;
        $response = $this->lc_seguridad->fn_consultar('validaAccesoPerfil', $lc_condiciones);
        $response = json_decode($response, true);

        if (!$response['accesoperfil'] == 1){
            $responseData = [
                'success' => false,
                'message' => "Error, este usuario no tiene privilegios suficiente para aperturar periodo",
                'data' => $response
            ];
            return json_encode($responseData);
        }

        $lc_condiciones[0] = 'i';
        $lc_condiciones[1] = $rst_id;
        $lc_condiciones[2] = $usr_id;
        $lc_condiciones[3] = $est_ip;
        $lc_condiciones[4] = 1;
        $response =  $this->lc_apertura->fn_ejecutar("grabaperiodo", $lc_condiciones);
        $response = json_decode($response, true);

        $responseData = [
            'success' => true,
            'message' => "Periodo creado correctamente",
            'data' => $dataresponse
        ];

        return json_encode($responseData);
    }

    public function documentacion() {
        include_once __DIR__ .'../../views/periodo/documentacion.html';
    }
}

?>