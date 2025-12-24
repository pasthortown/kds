<?php

session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_modificadores.php';
include_once "../../clases/clase_webservice.php";

$servicioWebObj=new webservice();
$modificador = new Modificador();

$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];

function specialChars($a) {
    return htmlspecialchars($a, ENT_QUOTES, 'UTF-8');
}

$request = (object) (array_map('specialChars', $_POST));

//Cargar Lista de Modificadores
if ($request->metodo === "cargarListaModificadores") {
    print $modificador->cargarListaModificadores(0);
} else if ($request->metodo === "actualizarListaModificadores") {
    $accion = 0;
    $cadena = "";
    $array_ini = parse_ini_file("../../serviciosweb/interface/config.ini");

    //Servidor WS Gerente Nacional
    $restaurante = $_SESSION['rstId'];
    $datosWebservice=$servicioWebObj->retorna_WS_Modificadores_Cargar($restaurante);
    $url=$datosWebservice["urlwebservice"];    
//    $servidorGerenteNacional = $array_ini["servidorWSGerenteNacional"];
//    $url = $servidorGerenteNacional . "GerenteNacional.ServiciosWeb/webresources/modificadores/todos/";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
    //Execute WSDepartamentos
    $result = curl_exec($ch);
    curl_close($ch);

    $respuesta = json_decode($result);
    
    $modificadores = $respuesta->modificadores;
    
    $registros = count($modificadores);

    for ($i = 0; $i < $registros; $i++) {
        $modif = $modificadores[$i];
        //Llamar a funcion actualizar lista de Departamento
        $cadena .= $modif->codModificador . "_" . $modif->descripcion . "_" . $modif->estado . "_";
    }
    
    print $modificador->actualizarListaModificadores($accion, $cadena, $idUsuario);
    
//
}

/*
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
            "descripcion" => utf8_decode($request->descripcion),
            "estado" => $request->estado,
            "numDepto" => $deptoPlus["NumDepartamento"]);
        $data_string = json_encode($depto);

        $url = $servidorGerenteNacional . "GerenteNacional.ServiciosWeb/webresources/departamentos/actualizar/";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));

        //Actualizar Departamento GerenteNacional
        $result = curl_exec($ch);
        //Close Connection
        $respuesta = json_decode($result);
        curl_close($ch);

        //Lista de Departamentos
        $consulta = $departamento->cargarDepartamentosPorCadena($idCadena);
        $consulta["mensaje"] = $respuesta->mensaje;
    } catch (Exception $exc) {
        $consulta["mensaje"] = $exc->getTraceAsString();
    }
    print json_encode($consulta);
}
 *
 */