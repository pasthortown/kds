<?php

//session_start();
///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Christian Pinto /////////////////////////////////////////
///////DESCRIPCION: Pantalla de Formas Pago Subir Imagen //////////////////////
///////TABLAS INVOLUCRADAS: Formaspago, ///////////////////////////////////////
///////FECHA CREACION: 2-07-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once "../../system/conexion/clase_sql.php";

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$idUsuario = $_SESSION["usuarioId"];
$lc_config = new sql();
$fmp_id = $_POST['codigofp'];
$imagen = $_POST['imagen'];
$accion = 1;

$lc_sql = "EXECUTE config.IAE_FormasPagoImagen $accion, '$fmp_id', '$imagen', '$idUsuario'";

if ($result = $lc_config->fn_ejecutarquery($lc_sql)) {
    $lc_regs['Confirmar'] = 1;
} else {
    $lc_regs['Confirmar'] = 0;
}

print json_encode($lc_regs);