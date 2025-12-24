<?php

session_start();

include_once "../../system/conexion/clase_sql.php";

//Clases para consumo de web services
include_once "../../resources/module/fidelizacion/Reporte.php";
//Clase Cadena
include_once "../../clases/clase_fidelizacionCadena.php";


include_once '../../clases/clase_fidelizacionRestaurante.php';

$idRestaurante = $_SESSION['rstId'];
$idCadena = $_SESSION['cadenaId'];

$reportes = new Reporte($idRestaurante);
$reportes->setIdCadena($idCadena);

function specialChars($a) {
    return htmlspecialchars($a, ENT_QUOTES, 'UTF-8');
}

$request = (object) (array_map('specialChars', $_POST));

if ($request->metodo === "enviarParametrosTransacciones") {
    $startDate = $request->startDate;
    $endDate = $request->endDate;
    $token = $request->token;
    // print "Desde: " . $startDate;
    // print "Hasta: " . $endDate;
    
    // $objUrlWS = $servicioWebObj->retorna_rutaWS($restaurante, 'FIREBASE', 'REPORTES TRANSACCIONES');
    // $urlWS = $objUrlWS['urlwebservice'];

    $respuesta = $reportes->cargarTransacciones($startDate, $endDate, $token);
    // print_r($respuesta);
    $respuesta = $respuesta->data;
    $respuesta = json_decode($respuesta);
    $resp = new stdClass();
    $resp->data = $respuesta->data;
    
    //Respuesta
    print json_encode($resp);

} else if ($request->metodo === "enviarParametrosProducto") {
    $startDate = $request->startDate;
    $endDate = $request->endDate;
    $token = $request->token;
    //Consumir endpoint
    $respuesta = $reportes->cargarTransaccionesProducto($startDate, $endDate, $token);
    // print_r($respuesta);
    $respuesta = $respuesta->data;
    $respuesta = json_decode($respuesta);
    $resp = new stdClass();
    $resp->data = $respuesta->data;
    
    //Respuesta
    print json_encode($resp);

} else if ($request->metodo === "cargarTokenSeguridad") {
    $respuesta = $reportes->solicitarTokenSeguridad($idCadena);
    print $respuesta->data;

} else if ($request->metodo === "enviarParametros") {
    $startDate = $request->startDateR;
    $endDate = $request->endDateR;
    $Fcomienza = date('Y-m-d', strtotime($startDate));
    $Ftermina = date('Y-m-d', strtotime($endDate));
    $Producto = $request->Producto;
    $Tienda = $request->Tienda;
    $nombreWS = $request->nombreWs;
    $token = $request->token;

    $respuesta = $reportes->cargarReportesFidelizacion($nombreWS, $token);
    $respuesta = $respuesta->data;
    $respuesta = json_decode($respuesta);
    $resp = new stdClass();
    $resp->data = $respuesta->data;


    if ($Producto != '-1' && $Tienda != '-1') {
        $prueba = array_filter($resp->data, function ($datos) use ($Producto, $Tienda, $startDate, $endDate) {
            return ($datos->product_name == $Producto && $datos->store_name == $Tienda
                && (date('Y-m-d', strtotime($datos->date)) >= $startDate)
                && (date('Y-m-d', strtotime($datos->date) <= $endDate)));
        });
    } else if ($Producto == '-1' && $Tienda != '-1') {
        $prueba = array_filter($resp->data, function ($datos) use ($Tienda, $startDate, $endDate) {
            return ($datos->store_name == $Tienda
                && (date('Y-m-d', strtotime($datos->date)) >= $startDate)
                && (date('Y-m-d', strtotime($datos->date) <= $endDate)));
        });
    } else if ($Producto != '-1' && $Tienda == '-1') {
        $prueba = array_filter($resp->data, function ($datos) use ($Producto, $startDate, $endDate) {
            return ($datos->product_name == $Producto
                && (date('Y-m-d', strtotime($datos->date)) >= $startDate)
                && (date('Y-m-d', strtotime($datos->date) <= $endDate)));
        });
    } else {
        $prueba = array_filter($resp->data, function ($datos) use ($startDate, $endDate) {
            return ((date('Y-m-d', strtotime($datos->date)) >= $startDate)
                && (date('Y-m-d', strtotime($datos->date) <= $endDate)));
        });
    }

    if (!empty($prueba)) {
        $encabezado = array();

        foreach ($prueba as $valoresArreglos) {
            unset($data);
            foreach ($valoresArreglos as $tittle => $value) {
                if (!in_array($tittle, $encabezado)) {
                    array_push($encabezado, $tittle);
                }
                $data[] = $value;

            }
            $tablerows[] = $data;
        }

        foreach ($encabezado as $valor) {
            $titulo[] = array('title' => $valor);
        }
        $arrayrespuesta["header"] = $titulo;
        $arrayrespuesta["body"] = $tablerows;
    } else {
        $arrayrespuesta["header"] = [];
        $arrayrespuesta["body"] = [];
    }
    print json_encode($arrayrespuesta);

} else if ($request->metodo === "cargarTokenSeguridadConsumo") {
    $respuesta = $reportes->solicitarTokenSeguridadConsumo($idCadena);
    print $respuesta->data;

}
