<?php
session_start();
///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR JOSE FERNANDEZ////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE FUNCIONES DEL GERENTE//////////////////
////////////////TABLAS: PANTALLA,PERMISOS_PERFIL///////////////////////////
////////FECHA CREACION: 25/03/2014/////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

include_once "../system/conexion/clase_sql.php";	
include_once "../clases/clase_funcionesGerente.php";

$lc_apertura = new funciones_gerente();
$lc_perfil = $_SESSION['perfil'];
$lc_usuarioId = $_SESSION['usuarioId'];
$ip = $_SESSION['direccionIp'];
$idCadena = $_SESSION['cadenaId'];
$idRestaurante = $_SESSION['rstId'];

if(htmlspecialchars(isset($_GET["consultapantallaGerente"]))) {		
    $lc_condiciones[0] = $lc_perfil;
    print $lc_apertura->fn_consultapantallaGerente($lc_condiciones);				
}
else if(htmlspecialchars (isset($_GET["apagar_Estacion"]))) {
    $lc_condiciones[0] = 'A';
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = $lc_usuarioId;				
    print $lc_apertura->fn_apagar_Estacion($lc_condiciones);	
}
 else if(htmlspecialchars (isset($_GET["obtenerMesa"]))){
    $lc_condiciones[0] = $_GET['rst_id'];	
    $lc_condiciones[1] = $_SESSION['estacionId'];
    $lc_condiciones[2] = $_SESSION['usuarioId'];
    print $lc_apertura->fn_obtenerMesa($lc_condiciones);
}	
else if (htmlspecialchars(isset($_POST["reiniciarImpresion"]))) {
    $lc_condiciones[0] = $ip;
    $lc_condiciones[1] = $_SESSION['usuarioId'];  
    print $lc_apertura->fn_reiniciarImpresion($lc_condiciones);
} else if (htmlspecialchars(isset($_POST["configuracionTurnero"]))) {
    print $lc_apertura->configuracionTurnero($idCadena, $idRestaurante);
}
?>