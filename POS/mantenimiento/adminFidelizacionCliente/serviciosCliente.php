<?php

session_start();

include_once"../../system/conexion/clase_sql.php";

//Entidades
include_once '../../clases/clase_ambiente.php';
include_once "../../resources/module/fidelizacion/Reporte.php";
include_once "../../clases/clase_fidelizacionCadena.php";
include_once "../../clases/clase_fidelizacionRestaurante.php";
include_once "../../clases/clase_fidelizacionAuditoria.php";
include_once "../../clases/clase_fidelizacionCliente.php";
include_once '../../clases/clase_webservice.php';

function specialChars($a) {
    return htmlspecialchars($a, ENT_QUOTES, 'UTF-8');
}

$request = (object) (array_map('specialChars', $_POST));

$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];
$idRestaurante = $_SESSION['rstId'];

//Reportes
$reportes = new Reporte($idRestaurante);

//Urls BD
$servicioWebURL = new webservice();

//Ambiente
$ambiente = new Ambiente();
$tipoAmbiente = json_decode($ambiente->cargarAmbiente());

//Cargar configuraciones fidelizacion por Cadena
if ($request->metodo === "cargarConfiguracionCadena") {
	$respuesta = new stdClass();
    $result = new Cadena();
    $respuesta = $result->guardarConfiguracionPoliticaAplicaCadenaObjeto($idCadena);
    $respuestaSeguridad = new stdClass();
    $respuestaSeguridad = $reportes->solicitarTokenSeguridad($idCadena);
    $seguridad = new stdClass();
    $seguridad = json_decode($respuestaSeguridad->data);
    $respuesta["tokenSeguridad"] = $seguridad->access_token;

    //Obtener Urls servicios
    $datos = array(5);
    $datos[0] = $idRestaurante;
    $datos[1] = 'REPORTES FIDELIZACION';
    $datos[2] = 'RECARGAS';
    $datos[3] = 0;
    $datos[4] = $idCadena;
	$urlWSPRecargaEfectivo = $servicioWebURL->retorna_Direccion_Webservice_Cadena($datos);
    $loyalty = $urlWSPRecargaEfectivo["urlwebservice"];
    $datos[1] = 'FIREBASE';
    $datos[2] = 'CONSULTA';
	$urlWSPRecargaEfectivo = $servicioWebURL->retorna_Direccion_Webservice_Cadena($datos);
    $reports = $urlWSPRecargaEfectivo["urlwebservice"];
    $datos[1] = 'REPORTES FIDELIZACION';
    $datos[2] = 'TRANSACCIONES CLIENTE';
    $urlWSPRecargaEfectivo = $servicioWebURL->retorna_Direccion_Webservice_Cadena($datos);
    $points = $urlWSPRecargaEfectivo["urlwebservice"];

	//Asignar rutas
	$respuesta["loyalty"] = $loyalty;
	$respuesta["reports"] = $reports;
    $respuesta["points"] = $points;

	//Respuesta
    print json_encode($respuesta);
//Proceso para guardar auditoría
} else if ($request->metodo === "guardarAuditoria") {
    $auditoria = new Auditoria();
    $idRestaurante = 0;
    $trama = str_replace('&quot;', '"', $request->trama);
    $respuesta = $auditoria->guardarLog($request->descripcion, $request->accion, $idRestaurante, $idCadena, $idUsuario, $trama);
    print json_encode($respuesta);
} else if ($request->metodo === "cargarListaRestaurantes") {
    $restaurante = new Restaurante();
    print $restaurante->cargarListaRestaurantes($idCadena);
}