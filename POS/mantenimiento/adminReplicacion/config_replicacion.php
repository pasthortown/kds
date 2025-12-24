<?php

session_start();

include_once '../../system/conexion/clase_sqlMultiple.php';
include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_replicacion.php';
include_once '../../clases/clase_replicacionLocal.php';
include_once '../../clases/clase_ambiente.php';

$idCadena = $_SESSION['cadenaId'];

if (htmlspecialchars(isset($_POST['cargarEstados']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    print $replicacion->cargarEstados();

} else if (htmlspecialchars(isset($_POST['cargarModulos']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    print $replicacion->cargarModulos($idCadena);

} else if (htmlspecialchars(isset($_POST['cargarLotesReplicaAzure']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    print $replicacion->cargarLotesReplicaAzure($idCadena, htmlspecialchars($_POST['fechaDesde']), htmlspecialchars($_POST['fechaHasta']), htmlspecialchars($_POST['idModulo']), $_POST['idEstados'], htmlspecialchars($_POST['cantidadEstados']));
    
} else if (htmlspecialchars(isset($_POST['cargarUpdateStoreAzure']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    print $replicacion->cargarUpdateStoreAzure($idCadena, htmlspecialchars($_POST['idModulo']), htmlspecialchars($_POST['idLote']));
    
} else if (htmlspecialchars(isset($_POST['confirmarCantidadReplicacionAzure']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    print $replicacion->confirmarCantidadReplicacionAzure($idCadena, htmlspecialchars($_POST['idModulo']));
    
} else if (htmlspecialchars(isset($_POST['replicacionAzure']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    print $replicacion->replicacionAzure($idCadena, htmlspecialchars($_POST['idModulo']));
    
} else if (htmlspecialchars(isset($_POST['cargarLotesReplicaDistribuidor']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    print $replicacion->cargarLotesReplicaDistribuidor($idCadena, htmlspecialchars($_POST['fechaDesde']), htmlspecialchars($_POST['fechaHasta']), htmlspecialchars($_POST['idModulo']), $_POST['idEstados'], htmlspecialchars($_POST['cantidadEstados']));
} else if (htmlspecialchars(isset($_POST['cargarTUpdateStoreDistribuidor']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    print $replicacion->cargarTUpdateStoreDistribuidor($idCadena, htmlspecialchars($_POST['idLote']));

} else if (htmlspecialchars(isset($_POST['verificarLotesPendientesDistribuidor']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    print $replicacion->verificarLotesPendientesDistribuidor($idCadena, htmlspecialchars($_POST['fechaDesde']), htmlspecialchars($_POST['fechaHasta']));
    
} else if (htmlspecialchars(isset($_POST['cargarUpdateStoreDistribuidor']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));

    print $replicacion->cargarUpdateStoreDistribuidor($idCadena, htmlspecialchars($_POST['idLote']), htmlspecialchars($_POST['idRestaurante']));
    
} else if (htmlspecialchars(isset($_POST['cargarUpdateStoreTiendasDistribuidor']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    print $replicacion->cargarUpdateStoreTiendasDistribuidor($idCadena, htmlspecialchars($_POST['idUpdateStore']));
    
} else if (htmlspecialchars(isset($_POST['aplicarReplicacionDistribuidor']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    $resultadoReplica = $replicacion->aplicarReplicacionDistribuidor($idCadena, htmlspecialchars($_POST['lote']));
    print json_encode($resultadoReplica);
    
} else if (htmlspecialchars(isset($_POST['transmitirReplicacionDistribuidor']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    $resultadoReplica = $replicacion->transmitirReplicacionDistribuidor($idCadena, htmlspecialchars($_POST['lote']));
    print json_encode($resultadoReplica);
    
} else if (htmlspecialchars(isset($_POST['cargarLotesReplicaTienda']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    print $replicacion->cargarLotesReplicaTienda($idCadena, htmlspecialchars($_POST['fechaDesde']), htmlspecialchars($_POST['fechaHasta']), htmlspecialchars($_POST['idModulo']), $_POST['idEstados'], htmlspecialchars($_POST['cantidadEstados']));
    
} else if (htmlspecialchars(isset($_POST['verificarLotesPendientesTienda']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    print $replicacion->verificarLotesPendientesTienda($idCadena, htmlspecialchars($_POST['fechaDesde']), htmlspecialchars($_POST['fechaHasta']));
    
} else if (htmlspecialchars(isset($_POST['cargarUpdateStoreTienda']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    print $replicacion->cargarUpdateStoreTienda($idCadena, htmlspecialchars($_POST['idLote']));
    
} else if (htmlspecialchars(isset($_POST['replicacionTienda']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    print $replicacion->replicacionTienda(htmlspecialchars($_POST['lote']));
    
} else if (htmlspecialchars(isset($_POST['cargarAmbiente']))) {
    $ambiente = new Ambiente();
    print $ambiente->cargarAmbiente();

} else if (htmlspecialchars(isset($_POST['pingMonitoreoLinkedServers']))) {
    set_time_limit(600);
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    $resultadoPing = $replicacion->pingMonitoreoLinkedServers(htmlspecialchars($idCadena));
    echo json_encode($resultadoPing);

} else if (htmlspecialchars(isset($_GET['CargarIps']))) {
    $replicacion = determinarAmbiente(htmlspecialchars($_GET['ambiente']));
    $resultadoReplica = $replicacion->CargarIpsLocales(htmlspecialchars($_GET['rstId']));
    print $resultadoReplica;

} else if (htmlspecialchars(isset($_POST['desactivarLoteReplica']))) {
    if (!isset($_POST['numeroLote'])) {
        header('Content-Type: application/json');
        echo "{estado:false,mensaje:'No se envió el número de lote',datos:{}}";
        die();
    }
    if (!isset($_POST['observacion'])) {
        header('Content-Type: application/json');
        echo "{estado:false,mensaje:'No se envió el número de lote',datos:{}}";
        die();
    } else {
        $observacion = $_POST['observacion'];
        if (10 > strlen($observacion)) {
            header('Content-Type: application/json');
            echo "{estado:false,mensaje:'El texto de la observación es muy corto',datos:{}}";
            die();
        }
    }

    $usuarioLogueado = $_SESSION["usuarioId"];
    $cadenaId = $_SESSION["cadenaId"];

    $numeroLote = htmlspecialchars($_POST['numeroLote']);
    $replicacion = determinarAmbiente(htmlspecialchars($_POST['ambiente']));
    $datos = array(
        "numeroLote" => $numeroLote,
        "idCadena" => $cadenaId,
        "usuarioLogueado" => $usuarioLogueado,
        "observacion" => $observacion,
    );
    $resultadoInactivar = $replicacion->desactivarLoteReplica($datos);
    header('Content-Type: application/json');
    echo json_encode($resultadoInactivar);
}

function determinarAmbiente($ambiente) {
    if ($ambiente == "tienda") {
        $replicacion = new ReplicarLocal();
    } else {
        $replicacion = new ReplicarAmbiente($ambiente);
    }
    return $replicacion;
}