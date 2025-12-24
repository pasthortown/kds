<?php

session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Configuración de Pantalla /////////////////////////////////
///////FECHA CREACION: 25-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_adminReimpresionRides.php';

$lc_config = new adminReimpresionRides();

$lc_rest = $_SESSION['rstId'];

if (htmlspecialchars(isset($_GET["cargarRides"]))) 
{
    $accion         ='1';
    $restaurante    = $lc_rest;
    $dias           = '1';
    $opcion         = '1';
    
    print $lc_config->fn_consultarRides($accion,$restaurante, $dias, $opcion);    
}

else if (htmlspecialchars(isset($_GET["cargaLabelComprobantes"]))) 
{
    $accion         ='2';   
    print $lc_config->fn_cargaTipoComprobantes($accion);    
}

else if (htmlspecialchars(isset($_POST["visualizarComprobante"]))) 
{
    $codigoComprobante   = $_POST["factura"];
    $tipo                = $_POST["tipo"];    
        
    print $lc_config->fn_visualizarComprobante($codigoComprobante, $tipo);    
}





