<?php

session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_adminMotivoIngresosEgresosCaja.php';


if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$motivo = new Motivo();
$request = (object) ($_POST);
$lc_usuario = $_SESSION['usuarioId'];

if ($request->metodo === "cargarMotivosIngresosEgresosCajaActivos") {
    print $motivo->cargarMotivosIngresosEgresosCajaActivos();
} else if ($request->metodo === "cargarMotivosIngresosEgresosCajaInactivos") {
    print $motivo->cargarMotivosIngresosEgresosCajaInactivos();
} else if ($request->metodo === "cargarMotivosIngresosEgresosCajaTodos") {
    print $motivo->cargarMotivosIngresosEgresosCajaTodos();
} else if ($request->metodo === "guardarMotivoIngresosEgresosCaja") {
    print $motivo->guardarMotivoIngresosEgresosCaja($request->accion, $request->idMotivoIngresosEgresosCaja, $request->concepto, $request->signo, $request->nivel, $request->estado, $lc_usuario);
}