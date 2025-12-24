<?php

session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_departamentos.php';
include_once "../../clases/clase_webservice.php";

if (
    empty($_SESSION['rstId'])
    OR empty($_SESSION['usuarioId'])
    OR empty($_SESSION['cadenaId'])
) {
    die(json_encode((object)[
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}
$servicioWebObj=new webservice();
$departamento = new Departamento();

$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];


//$request = (object)($_POST);

$request = (object)(array_map('utf8_decode', $_POST));

//Cargar Departamentos por Cadena
if ($request->metodo === "cargarDepartamentosPorCadena") {
    print json_encode($departamento->cargarDepartamentosPorCadena($idCadena));
//Actualizar Departamentos por Cadena MaxPoint - Gerente Nacional
} else if ($request->metodo === "actualizarDepartamentosPorCadena") {
    $consulta = array();
    try {

        $array_ini = parse_ini_file("../../serviciosweb/interface/config.ini");

        //Servidor WS Gerente Nacional
        $servidorGerenteNacional = $array_ini["servidorWSGerenteNacional"];

        //Actualizar Departamento MaxPoint
        $deptoPlus = $departamento->actualizarDepartamentosPorCadena($request->opcion, $idCadena, $idUsuario, $request->descripcion, $request->idParametro, $request->idDepartamento, $request->estado);
 

        //Objeto Enviar
        $depto = array(
            "codCadena" => $idCadena,
            "codDeptoPlu" => $deptoPlus["idDepartamento"],
            "descripcion" => utf8_encode($request->descripcion),
            "estado" => $request->estado,
            "numDepto" => $deptoPlus["NumDepartamento"]);


        /*-- Desactivado hasta que se creen departamentos en MaxPoint
        $restaurante = $_SESSION['rstId'];
        $datosWebservice=$servicioWebObj->retorna_WS_Clientes_Cliente($restaurante);
        $url=$datosWebservice["urlwebservice"];
        //$url = $servidorGerenteNacional . "GerenteNacional.ServiciosWeb/webresources/departamentos/actualizar/";

         $data_string = json_encode($depto);
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));

        //Actualizar Departamento GerenteNacional
         $result = curl_exec($ch);
        //Close Connection
         $respuesta =  json_decode($result);
          curl_close($ch);
        // $consulta["mensaje"] = $respuesta->mensaje;
        */

        //Lista de Departamentos
         $consulta = $departamento->cargarDepartamentosPorCadena($idCadena);
        $consulta["mensaje"] = "Actualizado Correctamente";

    } catch (Exception $exc) {
        $consulta["mensaje"] = $exc->getTraceAsString();
    }
    $retorno = json_encode($consulta);
    print  $retorno;
}