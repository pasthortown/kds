<?php

session_start();

/*
MODIFICADO POR  : José Fernández
DESCRIPCION     : Administracion de MotivoDeIngresosyEgresosDeDeposito
TABLAS          : MotivoDeIngresosyEgresosDeDeposito
FECHA CREACION  : 04-10-2016
*/

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_adminMotivoIngresosEgresosDeposito.php";

$lc_adminDepositos = new AdminConceptosDepositos();
$lc_usuario = $_SESSION['usuarioId'];

if (htmlspecialchars(isset($_POST["cargaDetalleConceptos"]))) 
{
    $lc_condiciones[0] = htmlspecialchars($_POST['opcionEstado']);
    print $lc_adminDepositos->fn_cargaDetalleConceptosDepositos($lc_condiciones);
}

if (htmlspecialchars(isset($_POST["guardaConceptosDepositos"]))) 
{
    $lc_condiciones[0] = htmlspecialchars($_POST['opcionActualiza']);
    $lc_condiciones[1] = $lc_usuario;
    $lc_condiciones[2] = htmlspecialchars($_POST['idCon']);
    $lc_condiciones[3] = htmlspecialchars($_POST['desCon']);    
    $lc_condiciones[4] = htmlspecialchars($_POST['selCon']);
    $lc_condiciones[5] = htmlspecialchars($_POST['estadoD']);
    
    print $lc_adminDepositos->fn_guardaConceptosDepositos($lc_condiciones);
}



