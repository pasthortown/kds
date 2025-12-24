<?php
session_start();
////////////////////////////////////////////////////////////////////////////////////////////
////////MODIFICADO POR: CHRISTIAN PINTO/////////////////////////////////////////////////////
////////DECRIPCION: VALIDACION DE USUARIO PERFIL Y PERIODO /////////////////////////////////
////////TABLAS INVOLUCRADAS: Users_Pos,Perfil_Pos, Periodo//////////////////////////////////////////
////////FECHA CREACION: 26/08/2015//////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////

require_once'../system/conexion/clase_sql.php';
include_once'../clases/clase_infoestacion.php';
include_once'../clases/clase_direccion.php';

$ip 		= new direccion();
$infoestacion   = new infoestacion();

if(htmlspecialchars(isset($_GET["infoimpresoras"]))) {
    $lc_condiciones[0] = 'C';    
    $lc_condiciones[1] = htmlspecialchars($_GET['estacion_ip']);
    print $infoestacion->fn_infoimpresoras($lc_condiciones);
}
else if(htmlspecialchars(isset($_GET["canalmovimiento_testimpresion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['estacion']);   
    print $infoestacion->fn_canalmovimiento_testimpresion($lc_condiciones);
}
else if(htmlspecialchars(isset($_GET["validaEstacion"]))) {
    $lc_condiciones[0] = 'V';    
    $lc_condiciones[1] = htmlspecialchars($_GET['estacion_ip']);
    print $infoestacion->fn_validaEstacion($lc_condiciones);
}
else if(htmlspecialchars (isset($_GET["apagar_Estacion"]))) {
    $lc_condiciones[0] = 'A';
    $lc_condiciones[1] = htmlspecialchars($_GET['estacion_ip']);
    $lc_condiciones[2] = '0';			
    print $infoestacion->fn_apagar_Estacion($lc_condiciones);	
}
else if(htmlspecialchars(isset($_GET["aplicar_replica"]))) {    
    print $infoestacion->aplicarReplica();	
}
else if(htmlspecialchars(isset($_GET["errores_replica"]))) {    
    print $infoestacion->muestraErroresReeplica();	
}else if(htmlspecialchars(isset($_GET["infoAplicaApiImpresion"]))) { 
    $lc_condiciones[0] = htmlspecialchars($_GET['estacion']);    
    print $infoestacion->infoAplicaApiImpresion($lc_condiciones);	
}

?>