<?php
session_start();

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_CambioDatosCliente.php";

$lc_config = new CambioDatosCliente();

if (htmlspecialchars(isset($_POST["obtenerCambioDatosCliente"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["codigoFactura"]);
    print $lc_config->fn_obtenerDatosClientes($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["duplicarFacturaActual"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["codigoFactura"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["usuarioAdmin"]);
    $lc_condiciones[3] = htmlspecialchars($_POST["documentoCliente"]);
    print $lc_config->fn_duplicarFacturaActual($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["anulacionFormasPago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["codigoFactura"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["usuarioAdmin"]);
    $lc_condiciones[3] = htmlspecialchars($_POST["documentoCliente"]);
    print $lc_config->fn_anulacionFormasPago($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["crearNotaDeCredito"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["codigoFactura"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["usuarioAdmin"]);
    $lc_condiciones[3] = htmlspecialchars($_POST["documentoCliente"]);
    print $lc_config->fn_crearNotaDeCredito($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["informacionSRI"]))) {
    $lc_condiciones[0] = $_SESSION['cadenaId'];

    print $lc_config->informacion_sri($lc_condiciones);
}

?>
