<?php

/*
DESARROLLADO POR: Jose Fernandez
DESCRIPCION: Consume el web service para para validar personal
TABLAS INVOLUCRADAS: 
FECHA CREACION:02/08/2016
 */

session_start();

include("../system/conexion/clase_sql.php");
include("../clases/clase_facturacionServicios.php");
include("../soap/lib/nusoap.php"); 

$array_ini = parse_ini_file("../serviciosweb/interface/config.ini");
//parametros para interface
$urlServicioWeb = ($array_ini['urlServicioWeb']);

$restaurante=$_SESSION['rstId'];
$cadena=$_SESSION['cadenaId'];
$usuario=$_SESSION['usuarioId'];

$lc_servicios = new clase_facturacionServicios();

if(htmlspecialchars(isset($_POST["validaExisteAsociado"])))
{ 	                        
        $lc_condiciones[0]= $restaurante;
        $lc_condiciones[1]=$cadena;
        $lc_condiciones[2]=$usuario;
         
        //$array = $lc_servicios->fn_consultaUrlWs($lc_condiciones);
        //$array2 = $array[0];
        //$url = $array2['url'];
        
        $cedulaA=$_POST["cedAso"];
        $wsdl = $urlServicioWeb;//"http://192.168.100.219/gerente_15/serviciosweb/interface/serviciopruebas.php?wsdl";
        
        $client = new nusoap_client($wsdl, 'wsdl');

        $param = array(
                'cedula' => $cedulaA,
                'cod_restaurante' => $restaurante);
        $Confirmacion = $client->call('ValidarPersonal', $param);           
        //$lc_cliente_interfaceGer->fn_consultar("InactivaSesionCajero",$lc_condiciones);        
        print $Confirmacion;
}