<?php

session_start();

include("../system/conexion/clase_sql.php");
include("../clases/clase_pagoTarjetaDinamica.php");

$lc_facturas = new pagoTarjetas();

$ip             = $_SESSION['direccionIp'];
$usuario        = $_SESSION['usuarioId'];
$restaurante    = $_SESSION['rstId'];
$perfil         = $_SESSION['perfil'];
$cadena         = $_SESSION['cadenaId'];
$idEstacion     = $_SESSION['estacionId'];
$periodo        = $_SESSION['IDPeriodo'];
$controlEstacion= $_SESSION['IDControlEstacion'];
$tipoServicio   = $_SESSION['TipoServicio'];
$subfijoPais    = $_SESSION['paisIsoAlfa2'];

if (htmlspecialchars(isset($_POST["insertarRequerimientoTramaDinamica"]))) 
    {
    $lc_condiciones[1] = htmlspecialchars($_POST["cfacTramaDinamica"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["formaPagoIDTramaDinamica"]);
    $lc_condiciones[3] = htmlspecialchars($_POST["valorTransaccionTramaDinamica"]);
    $lc_condiciones[4] = htmlspecialchars($_POST["valorPropinaTramaDinamica"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["tipoEnvioTramaDinamcica"]);
    $lc_condiciones[6] = htmlspecialchars($_POST["tipoTransaccionTramaDinamica"]);
    $lc_condiciones[7] = $restaurante;
    $lc_condiciones[8] = $idEstacion;
    $lc_condiciones[9] = $usuario;
    $lc_condiciones[10] = htmlspecialchars($_POST["lecturaTarjetaTramaDinamica"]);
    $lc_condiciones[11] = htmlspecialchars($_POST["cvvTramaDinamica"]);
    $lc_condiciones[12] = '';    
    print $lc_facturas->insertarRequerimientoTramaDinamica($lc_condiciones);
    }
else if (htmlspecialchars(isset($_GET["esperaRespuestaRequerimientoAutorizacion"]))) 
{
     $lc_condiciones[0] = htmlspecialchars($_GET["cfac"]);
     print $lc_facturas->fn_esperaRequerimientoTramaDinamica($lc_condiciones);
}
else if (isset($_POST["consultaSecuenciaTimeOuts"])) 
   {
        $lc_condiciones[0] = htmlspecialchars($_POST["idTipoEnvioTarjeta"]);
        $lc_condiciones[1] = $cadena;
        $lc_condiciones[2] = ( isset($_POST["nivelErrorLog"]) ? $_POST["nivelErrorLog"] : 11 );
        $lc_condiciones[3] = ( isset($_POST["numFactura"]) ? $_POST["numFactura"] : '' );
        print $lc_facturas->fn_consultaSecuenciaTimeOut($lc_condiciones);
   }

else if (htmlspecialchars(isset($_GET["ValidacionSinTarjeta"]))){

     $lc_condiciones[0] = htmlspecialchars($_GET["cfac"]);
     print $lc_facturas->fn_validarSinTarjeta($lc_condiciones);

}
else if (isset($_POST["actualizaEstadoRequerimiento"])) {

     $lc_condiciones[0] = htmlspecialchars($_POST["rsaut_id"]);
     $lc_condiciones[1] = htmlspecialchars($_POST["codFact"]);
     $lc_condiciones[2] = $idEstacion;
     print $lc_facturas->fn_actualizaEstadoRequerimiento($lc_condiciones);
}
else if (htmlspecialchars(isset($_GET["consultaDiferenciaTiempo"]))) {
     $lc_condiciones[0] = htmlspecialchars($_GET["codFact"]);
     $lc_condiciones[1] = $idEstacion;
     print $lc_facturas->fn_consultaDiferenciaTiempo($lc_condiciones);
}