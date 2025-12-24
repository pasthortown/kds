<?php

session_start();

include_once("../../system/conexion/clase_sql.php");
include_once ("../../clases/clase_productos.php");

$producto = new Producto();

$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];

$request = (object) $_POST;

if ($request->metodo === "actualizarColeccionDepartamentos") {
    $mensaje = array("mensaje" => "", "estado" => 0, "departamentos" => array());
    try {
        $url = "http://srvv-devsoa:8080/GerenteNacional.ServiciosWeb/webresources/departamentos/cargarporcadena/?cadena=" . $idCadena;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //Execute WSDepartamentos
        $result = curl_exec($ch);
        curl_close($ch);

        $respuesta = json_decode($result);
        $departamentos = $respuesta->departamentos;
        $registros = count($departamentos);

        for ($i = 0; $i < $registros; $i++) {
            //Llamar a funcion actualizar lista de Departamento
            $respuestaMaxPoint = $producto->actualizarDepartamentosMaxPoint($idCadena, $idUsuario, $departamentos[$i]->descripcion, $departamentos[$i]->codDeptoPlu, $departamentos[$i]->numDepto);
        }
        $mensaje["mensaje"] = "Departamentos actualizados correctamente.";
        $mensaje["estado"] = 1;
        $mensaje["departamentos"] = $producto->cargarDepartamentosPorCadena($idCadena);
        print json_encode($mensaje);
    } catch (Exception $e) {
        $mensaje["mensaje"] = "Servicio no Disponible";
        $mensaje["estado"] = 0;
        print json_encode($mensaje);
    }
}