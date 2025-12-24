<?php
require_once "parametros.php";

use Maxpoint\Mantenimiento\adminReplicacion\Clases\ReplicacionAzureController;
use Maxpoint\Mantenimiento\adminReplicacion\Clases\ReplicacionDistribuidorController;
use Maxpoint\Mantenimiento\adminReplicacion\Clases\ReplicacionTiendaController;
use Maxpoint\Mantenimiento\adminReplicacion\Clases\ReplicacionLogDistribuidorController;
use Maxpoint\Mantenimiento\adminReplicacion\Clases\ConexionDinamica;
use Doctrine\DBAL\Connection;
use Carbon\Carbon;
use Maxpoint\Mantenimiento\adminReplicacion\Utils\Ping;

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
$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];
$resumenTramas[] = array();
$request = (object)(array_map('utf8_decode', $_POST));

$conexionAzure = $conexionDinamica->conexionAzure();
$conexionDistribuidor = $conexionDinamica->conexionDistribuidor();
$conexionLogDistribuidor=$conexionDinamica->conexionLogDistribuidor();

$replicacionAzureControllerObj = new ReplicacionAzureController($conexionAzure);
$replicacionDistribuidorControllerObj = new ReplicacionDistribuidorController($conexionDistribuidor);
$formatoFechasDistribuidor = $replicacionDistribuidorControllerObj->getDateTimeFormatString();
$replicacionLogControllerObj = new ReplicacionLogDistribuidorController($conexionLogDistribuidor);


if (isset($request->replicacionAzure) && (1 == $request->replicacionAzure)) {
    $resultadoInsercionAzure = $replicacionAzureControllerObj->replicacionAzure($idUsuario, $idCadena, $request->idModulo);
   
    //Lote
    $lote = $resultadoInsercionAzure["datos"][0][0];
    //Tramas
    $tramas = $resultadoInsercionAzure["datos"][1];
    //Insertar el lote en el Distribuidor
    $stmtLote = $replicacionDistribuidorControllerObj->prepararConsultaInsercionLote();
    $stmtLote->bindParam("IDLoteReplica", $lote["IDLoteReplica"]);
    $stmtLote->bindParam("mdl_id", $lote["mdl_id"]);
    $stmtLote->bindParam("cdn_id", $lote["cdn_id"]);
    $stmtLote->bindParam("rst_id", $lote["rst_id"]);
    $stmtLote->bindParam("numeroLote", $lote["numeroLote"]);
    $stmtLote->bindParam("FechaCreacion", $lote["FechaCreacion"]);
    $stmtLote->bindParam("HoraCreacion", $lote["HoraCreacion"]);
    $stmtLote->bindParam("FechaUpdate", $lote["FechaUpdate"]);
    $stmtLote->bindParam("HoraUpdate", $lote["HoraUpdate"]);
    $stmtLote->bindParam("UserCreacion", $lote["UserCreacion"]);
    $stmtLote->bindParam("UserUpdate", $lote["UserUpdate"]);
    $stmtLote->bindParam("IDStatus", $lote["IDStatus"]);
    try {
        $stmtLote->execute();
    } catch (Exception $ex) {

        //TODO: Hacer algo con la excepción capturada
    }

    //Insertar las tramas en el Distribuidor
    $stmtTramas = $replicacionDistribuidorControllerObj->prepararConsultaInsercionTramas();
    foreach ($tramas as $trama) {
        $stmtTramas->bindParam("IDUpdateStore", $trama["IDUpdateStore"]);
        $stmtTramas->bindParam("IDLoteReplica", $trama["IDLoteReplica"]);
        $stmtTramas->bindParam("tabla", $trama["tabla"]);
        $stmtTramas->bindParam("trama", $trama["trama"]);
        $stmtTramas->bindParam("mdl_id", $trama["mdl_id"]);
        $stmtTramas->bindParam("cdn_id", $trama["cdn_id"]);
        $stmtTramas->bindParam("rst_id", $trama["rst_id"]);
        $stmtTramas->bindParam("usr_id", $trama["usr_id"]);
        $stmtTramas->bindParam("Fecha", $trama["Fecha"]);
        $stmtTramas->bindParam("Hora", $trama["Hora"]);
        $stmtTramas->bindParam("replica", $trama["replica"]);
        try {
            $stmtTramas->execute();
        } catch (Exception $ex) {
            //TODO: Hacer algo con la excepción capturada
        }

    }

    //Modificar estado del lote y tramas en Azure, Llenar LOG,Enviar Email
    // $resultadoActualizacion = $replicacionAzureControllerObj->actualizarEstados($idCadena, $lote["mdl_id"],$lote["IDLoteReplica"]);
    $resultadoActualizacion = $replicacionAzureControllerObj->actualizarEstados($idUsuario, $idCadena, $lote["mdl_id"], $lote["IDLoteReplica"]);
    $resultado = array("estado" => 1, "datos" => []);
    enviarRespuestaJson($resultado);

} else if (isset($request->aplicarReplicacionDistribuidor) && (1 == $request->aplicarReplicacionDistribuidor)) {
    $resultado = $replicacionDistribuidorControllerObj->aplicarReplicacionDistribuidor($idUsuario,$idCadena, $request->lote, 0);
    enviarRespuestaJson($resultado);

} else if (isset($request->anularLoteDistribuidor) && (1 == $request->anularLoteDistribuidor)) {
    $idLoteNumerico = preg_replace('([A-Za-z])', '', $request->lote);
    $resultado = $replicacionDistribuidorControllerObj->anularLoteDistribuidor($idUsuario, $idUsuario, $idCadena, $idLoteNumerico, $request->observacion);
    enviarRespuestaJson($resultado);

} else if (isset($request->transmitirReplicacionDistribuidor) && (1 == $request->transmitirReplicacionDistribuidor)) {

    $conexionDinamicaObj = new ConexionDinamica();
    $estado = 1;
    $errores = array();
    $datos = array();
    $idLoteNumerico = preg_replace('([A-Za-z])', '', $request->lote);

    //En el SP que se ejecuta solo carga el lote si está en estado "Aplicado"
    $cargarLote = $replicacionDistribuidorControllerObj->cargarLote($idUsuario, $idCadena, '', $idLoteNumerico);
    if (0 == $cargarLote["estado"]) {
        $resultadoFinal = [
            "estado" => $cargarLote["estado"],
            "errores" => $cargarLote["estado"],
            "datos" => array(),
        ];

        enviarRespuestaJson($resultadoFinal);
    }

    //$actualizarEstadoLote = $replicacionDistribuidorControllerObj->actualizarEstadoLote($idUsuario,$idCadena, 0, $idLoteNumerico,"Procesando");
    $cargarBasesReplicacionLote = $replicacionDistribuidorControllerObj->cargarConexionesLote($idUsuario, $idCadena, $idLoteNumerico, 0);
    $basesReplicacionLote = $cargarBasesReplicacionLote["datos"];

    $basesReplicacionLoteOrdenadas = array();
    foreach ($basesReplicacionLote as $clave => $valor) {
        $idRestauranteActual = $valor["rst_id"];
        $basesReplicacionLoteOrdenadas[$idRestauranteActual] = $valor;
    }

    $lote = $cargarLote["datos"][0];

    $numeroTotalBases = count($basesReplicacionLoteOrdenadas);
    $numeroBasesReplicacionInvalidas = 0;
    $numeroBasesReplicacionActivas = 0;
    $numeroBasesReplicacionInactivas = 0;
    $numeroBasesReplicacionRemodeladas = 0;
    $basesReplicacionEjecutadas = 0;
    $basesReplicacionError = 0;
    foreach ($basesReplicacionLoteOrdenadas as $restauranteActual) {

        $idRestaurante = $restauranteActual["rst_id"];
        $tipoBase = $restauranteActual["Tipo"];
        $replicacionDistribuidorControllerObj->actualizarTramasReplicaTiendaRollback($idUsuario, $idCadena, $idLoteNumerico, $idRestaurante);
        $cargarTramas = $replicacionDistribuidorControllerObj->cargarTramasLoteTienda($idUsuario, $idCadena, $idLoteNumerico, $idRestaurante);
        $tramasRestaurante = $cargarTramas["datos"];

        //Cualquier valor distinto de -1 o 2 es un valor inconsistente
        if ($tipoBase <> "-1" && $tipoBase <> "2" && $tipoBase <> "3") {
            $numeroBasesReplicacionInvalidas++;
            foreach ($tramasRestaurante as $trama) {

                $estadostramas[] = array(
                    "IDLOTE" => $trama["IDLoteReplica"],
                    "IDTRAMA" => $trama["IDUpdateStore"],
                    "IDRESTAURANTE" => $trama["rst_id"],                    
                    "ESTADO" => "ERROR",
                    "ERRORNUMBER" => 0,
                    "ERRORMESSAGE" => "No existe configuración de conexión para el local",
                    "LASTUSER" => $idUsuario,
                    "LASTUPDATE" => date("Y-m-d H:i:s"),
                );
            }

            $replicacionLogControllerObj->llenarLogTramas($estadostramas);
            continue;
        }


        //Si el tipo es -1, el local está desactivado y no debe ser tomado en cuenta
        if ($tipoBase == "-1") {
            $numeroBasesReplicacionInactivas++;

            $estadostramas = array();
            $estado = 0;
            $errActual = [
                "rst_id" => $restauranteActual["Descripcion"],
                "mensaje" => "El local se encuentra desactivado para replicación"
            ];
            $errores[] = $errActual;

            foreach ($tramasRestaurante as $trama) {
                $estadostramas[] = array(
                    "IDLOTE" => $trama["IDLoteReplica"],
                    "IDTRAMA" => $trama["IDUpdateStore"],
                    "IDRESTAURANTE" => $trama["rst_id"],
                    "ESTADO" => "ERROR",
                    "ERRORNUMBER" => 0,
                    "ERRORMESSAGE" => "El local se encuentra desactivado para replicación",
                    "LASTUSER" => $idUsuario,
                    "LASTUPDATE" => date("Y-m-d H:i:s"),
                );
            }

            $replicacionLogControllerObj->llenarLogTramas($estadostramas);
            continue;
        }
        if ($tipoBase == "3") {
            $numeroBasesReplicacionRemodeladas++;
            $estadostramas = array();
            $estado = 0;
            $errActual = [
                "rst_id" => $restauranteActual["Descripcion"],
                "mensaje" => "El local se encuentra en remodelamiento"
            ];
            $errores[] = $errActual;

            foreach ($tramasRestaurante as $trama) {
                $estadostramas[] = array(
                    "IDLOTE" => $trama["IDLoteReplica"],
                    "IDTRAMA" => $trama["IDUpdateStore"],
                    "IDRESTAURANTE" => $trama["rst_id"],
                    "ESTADO" => "ERROR",
                    "ERRORNUMBER" => 0,
                    "ERRORMESSAGE" => "El local se encuentra en remodelamiento",
                    "LASTUSER" => $idUsuario,
                    "LASTUPDATE" => date("Y-m-d H:i:s"),
                );
            }

            $replicacionLogControllerObj->llenarLogTramas($estadostramas);
            continue;
        }

        $estadostramas = array();

        $parametros = crearParametrosConexion($restauranteActual);
        $conexionActual = $conexionDinamica->conexion($parametros);

        try {
            $conexionActual->connect();
            $numeroBasesReplicacionActivas++;
        } catch (Exception $ex) {
            $basesReplicacionError++;
            $estado=0;
        }
        // Intentar coectarse al local

        // TODO: Revisar conectividad al local mediante un PING

        //Si no hay tramas por replicar, pasar de largo e ir al siguiente local.
        $totalTramas = count($tramasRestaurante);
        if (0 == $totalTramas) {
            $basesReplicacionEjecutadas++;
            $resumenTramas[] = array(
                "Rest" => $restauranteActual["Descripcion"],
                "Total" => $totalTramas,
                "Correctas" => 0
            );
            continue;
        }
        $totalTramasCorrectas = 0;
        $tramasError = 0;

        // Intentar conexión a la base, si falla llenar log de tramas y salir
        try {
            $conexionActual->connect();
            $conexionActual->beginTransaction();
        } catch (Exception $ex) {
            $estadostramas = array();
            $estado = 0;
            $errActual = [
                "rst_id" => $restauranteActual["Descripcion"],
                "mensaje" => "Los datos de conexión a la base del local no son válidos"
            ];
            $errores[] = $errActual;

            foreach ($tramasRestaurante as $trama) {
                $estadostramas[] = array(
                    "IDLOTE" => $trama["IDLoteReplica"],
                    "IDTRAMA" => $trama["IDUpdateStore"],
                    "IDRESTAURANTE" => $trama["rst_id"],

                    "ESTADO" => "ERROR",
                    "ERRORNUMBER" => 0,
                    "ERRORMESSAGE" => $ex->getMessage(),
                    "LASTUSER" => $idUsuario,
                    "LASTUPDATE" => date("Y-m-d H:i:s"),
                );
            }

            $replicacionLogControllerObj->llenarLogTramas($estadostramas);
            continue;
        }

        // Crear Controlador de replica del local
        $replicacionTiendaControllerObj = new ReplicacionTiendaController($conexionActual);

        //Crear statement de inserción del lote
        $stmtInsercionLoteTienda = $replicacionTiendaControllerObj->crearConsultaInsercionLotesReplicaTienda($lote, $idRestaurante);

        // Asignar los valores de inserción del lote
        $stmtInsercionLoteTienda->bindValue(1, $lote["IDLoteReplica"]);
        $stmtInsercionLoteTienda->bindValue(2, $lote["IDLoteReplica"]);
        $stmtInsercionLoteTienda->bindValue(3, $lote["mdl_id"]);
        $stmtInsercionLoteTienda->bindValue(4, $lote["cdn_id"]);
        $stmtInsercionLoteTienda->bindValue(5, $idRestaurante);
        $stmtInsercionLoteTienda->bindValue(6, $lote["numeroLote"]);
        $stmtInsercionLoteTienda->bindValue(7, $lote["FechaCreacion"]);
        $stmtInsercionLoteTienda->bindValue(8, $lote["HoraCreacion"]);
        $stmtInsercionLoteTienda->bindValue(9, $lote["FechaUpdate"]);
        $stmtInsercionLoteTienda->bindValue(10, $lote["HoraUpdate"]);
        $stmtInsercionLoteTienda->bindValue(11, $lote["UserCreacion"]);
        $stmtInsercionLoteTienda->bindValue(12, $lote["UserCreacion"]);

        //Crear el statement de insercion de la trama en el UpdateStore de la tienda
        $stmtInsercionTramaUpdateStore = $replicacionTiendaControllerObj->crearConsultaInsercionUpdateStoreTienda();

        foreach ($tramasRestaurante as $trama) {
            $stmtInsercionTramaUpdateStore->bindValue("IDLoteReplica", $trama["IDLoteReplica"]);
            $stmtInsercionTramaUpdateStore->bindValue("tabla", $trama["tabla"]);
            $stmtInsercionTramaUpdateStore->bindValue("trama", $trama["trama"]);
            $stmtInsercionTramaUpdateStore->bindValue("mdl_id", $trama["mdl_id"]);
            $stmtInsercionTramaUpdateStore->bindValue("cdn_id", $trama["cdn_id"]);
            $stmtInsercionTramaUpdateStore->bindValue("rst_id", $trama["rst_id"]);
            $stmtInsercionTramaUpdateStore->bindValue("usr_id", $trama["usr_id"]);
            $stmtInsercionTramaUpdateStore->bindValue("fecha", $trama["Fecha"]);
            $stmtInsercionTramaUpdateStore->bindValue("hora", $trama["Hora"]);
            $stmtInsercionTramaUpdateStore->bindValue("replica", $trama["replica"]);
            try {
                $stmtInsercionTramaUpdateStore->execute();
            } catch (Exception $e) {
                $estadostramas[] = array(
                    "IDLOTE" => $trama["IDLoteReplica"],
                    "IDTRAMA" => $trama["IDUpdateStore"],
                    "IDRESTAURANTE" => $trama["rst_id"],

                    "ESTADO" => "ERROR",
                    "ERRORNUMBER" => 0,
                    "ERRORMESSAGE" => $e->getMessage(),
                    "LASTUSER" => $idUsuario,
                    "LASTUPDATE" => date("Y-m-d H:i:s"),
                );
                $errActual = [
                    "rst_id" => $restauranteActual["Descripcion"],
                    "mensaje" => $e->getMessage()
                ];
                $errores[] = $errActual;
                continue;
            }
            $totalTramasCorrectas++;
            $estadostramas[] = array(
                "IDLOTE" => $trama["IDLoteReplica"],
                "IDTRAMA" => $trama["IDUpdateStore"],
                "IDRESTAURANTE" => $trama["rst_id"],

                "ESTADO" => "OK",
                "ERRORNUMBER" => 0,
                "ERRORMESSAGE" => "Insertada correctamente",
                "LASTUSER" => $idUsuario,
                "LASTUPDATE" => date("Y-m-d H:i:s"),
            );
        }

        $replicacionLogControllerObj->llenarLogTramas($estadostramas);
        $replicacionLogControllerObj->EliminarDuplicados();
		
        $resumenTramas[] = array(
            "Rest" => $restauranteActual["Descripcion"],
            "Total" => $totalTramas,
            "Correctas" => $totalTramasCorrectas
        );

        if ($totalTramasCorrectas === $totalTramas) {
            try {

                $stmtInsercionLoteTienda->execute();
                //$ejecucion = $conexionActual->executeUpdate($stmtInsercionLoteTienda);
                $replicacionDistribuidorControllerObj->actualizarTramasReplicadasTienda($idUsuario, $idCadena, $idLoteNumerico, $idRestaurante);
                $conexionActual->commit();
                $basesReplicacionEjecutadas++;
            } catch (Exception $ex) {
                $errActual = [
                    "rst_id" => $restauranteActual["Descripcion"],
                    "mensaje" => $ex->getMessage()
                ];

                $errores[] = $errActual;
                $conexionActual->rollBack();
            }
        } else {
            $conexionActual->rollBack();

        }

    }


    $sumaEjecucionBasesReplicacion = $basesReplicacionEjecutadas + $numeroBasesReplicacionInactivas + $numeroBasesReplicacionInvalidas;


    // Evaluar el resultado de la transmisión de las tramas a todos los locales.
    if ($basesReplicacionEjecutadas == 0) {
        $nuevoEstadoLoteDistribuidor = 'Error';
    } else if ($numeroBasesReplicacionActivas == $basesReplicacionEjecutadas && $basesReplicacionError === 0) {
        $nuevoEstadoLoteDistribuidor = 'Transmitido';
        $estado = 1;
    } else {
        $nuevoEstadoLoteDistribuidor = 'Parcial';
    }

    //Modificar el estado del lote en el distribuidor.
    $actualizarEstadoLote = $replicacionDistribuidorControllerObj->actualizarEstadoLote($idUsuario, $idCadena, 0, $idLoteNumerico, $nuevoEstadoLoteDistribuidor);

    //Responder json con el estado final de la replicación.
    $resultadoFinal = [
        "estado" => $estado,
        "errores" => $errores,
        "datos" => $datos,
        "numero" => array(
            "numeroTotalBases" => $numeroTotalBases,
            "numeroBasesReplicacionInvalidas" => $numeroBasesReplicacionInvalidas,
            "numeroBasesReplicacionActivas" => $numeroBasesReplicacionActivas,
            "numeroBasesReplicacionInactivas" => $numeroBasesReplicacionInactivas,
            "basesReplicacionEjecutadas" => $basesReplicacionEjecutadas,
            "resumenTramas" => $resumenTramas,
        )
    ];

    $replicacionLogControllerObj->EliminarDuplicados();
    enviarRespuestaJson($resultadoFinal);

}else if(isset($request->replicarTrama) && (1 == $request->replicarTrama)){
    $idTrama = $request->idTrama;
    $cargarTrama=$replicacionDistribuidorControllerObj->cargarTrama($idTrama);
    $trama=$cargarTrama["datos"][0];
    $numeroTramasErrorLote=$trama["numeroTramasError"];
    $cargaBaseReplicacion=$replicacionDistribuidorControllerObj->cargarParametrosConexionTrama($idTrama);
    if(count($cargaBaseReplicacion["datos"])<1){
        $resultadoFinal =  [
            "estado" => 0,
            "errores" => ["No existen parametros de conexión a la tienda"],
            "datos" => [],
        ];
        enviarRespuestaJson($resultadoFinal);
    }
    $baseReplicacion = $cargaBaseReplicacion["datos"][0];
    $parametrosConexion = crearParametrosConexion($baseReplicacion);
    $conexionLocal = $conexionDinamica->conexion($parametrosConexion);
    // Crear Controlador de replica del local
    $replicacionTiendaControllerObj = new ReplicacionTiendaController($conexionLocal);

    list($estadoInsercionBloqueTramas, $tramasCorrectas) = $replicacionTiendaControllerObj->insertarTramasTienda(array($trama));

    $replicacionLogControllerObj->llenarLogTramas($estadoInsercionBloqueTramas);
    if($tramasCorrectas>0 && $numeroTramasErrorLote == 1){
        $idLoteNumerico=$trama["IDLoteReplica"];
        $replicacionDistribuidorControllerObj->actualizarEstadoLote($idUsuario,$idCadena, 0, $idLoteNumerico,'Transmitido');
    }
    enviarRespuestaJson($estadoInsercionBloqueTramas);
} else if (isset($request->probarConectividad)) {
    $idLoteNumerico = preg_replace('([A-Za-z])', '', $request->lote);
    $cargarBasesReplicacionLote = $replicacionDistribuidorControllerObj->cargarConexionesLote($idUsuario, $idCadena, $idLoteNumerico, 0);
    $basesReplicacionLote = $cargarBasesReplicacionLote["datos"];

    $resultados = array();

    $cargarNumeroTramasLote = $replicacionDistribuidorControllerObj->cargarNumeroTramasLote($idUsuario, $idCadena, $idLoteNumerico, 0);
    $numeroTramasLote = $cargarNumeroTramasLote["datos"][0];
    $resultados["numerotramas"] = $numeroTramasLote["numerotramas"];

    foreach ($basesReplicacionLote as $restauranteActual) {

        $rstId = $restauranteActual["rst_id"];
        $tipoBase = $restauranteActual["Tipo"];

        if ($tipoBase <> "-1" && $tipoBase <> "2") {
            $resultados["invalidos"][] = $restauranteActual;
            continue;
        }

        if ($tipoBase == "-1") {
            $resultados["desactivados"][] = $restauranteActual;
            continue;
        }

        $pingObj = new Ping($restauranteActual["IP"]);
        $pingObj->setTimeout(2);
        $resultadoPing = $pingObj->ping();

        if (!$resultadoPing) {
            $resultados["error"][] = array(
                "restaurante" => $restauranteActual,
                "mensaje" => "No hay conexión de red con el restaurante");
            continue;
        }

        $parametros = crearParametrosConexion($restauranteActual);
        $conexionActual = $conexionDinamica->conexion($parametros);
        try {
            $conexionActual->connect();
        } catch (\Doctrine\DBAL\Driver\PDOException $ex) {
            $resultados["error"][] = array("restaurante" => $restauranteActual, "mensaje" => $ex->getMessage());
            continue;
        }
        $resultados["ok"][] = $restauranteActual;


    }
    enviarRespuestaJson($resultados);

    //echo json_encode($_SESSION);
} else {
    die(json_encode((object)[
        "estado" => 0,
        "mensaje" => "No se especificó ninguna acción"
    ]));
}

function enviarRespuestaJson($resultadoFinal){
    header('Content-Type: application/json');
    print(json_encode($resultadoFinal));
    die();
}

function crearParametrosConexion($arrayBasesReplicacion){
    $parametros = array(
        'dbname' => $arrayBasesReplicacion["Databasename"],
        'host' => join("\\", array($arrayBasesReplicacion["IP"], $arrayBasesReplicacion["Instancia"])),
        'user' => 'sis_maxpoint',
        'password' => 'S1sM4xp01nt*510620',
        'driver' => 'pdo_sqlsrv',
    );

    return $parametros;
}