<?php

session_start();


include("../system/conexion/clase_sql.php");
include("../clases/clase_adicionarFondo.php");


$usuario            = $_SESSION['usuarioId'];
$idEstacion         = $_SESSION['estacionId'];
$periodo            = $_SESSION['IDPeriodo'];
$controlEstacion    = $_SESSION['IDControlEstacion'];
$userAdmin          = $_SESSION['usuarioIdAdmin'];

$lc_fondo = new AdicionarFondo();

if (isset($_GET["adicionaFondo"])) 
{
    $lc_condiciones[0] = "I";
    $lc_condiciones[1] = $_GET["valorFondo"];
    $lc_condiciones[2] = $idEstacion;
    $lc_condiciones[3] = $userAdmin;
    $lc_condiciones[4] = $controlEstacion;

    print $lc_fondo->fn_ingresarAdicionFondoCaja($lc_condiciones);
}

if (isset($_GET["validaCajeroActivo"])) 
{    
    $lc_condiciones[0] = $idEstacion;
    $lc_condiciones[1] = $periodo; 
    $lc_condiciones[2] = '1'; 

    print $lc_fondo->validaCajeroActivo($lc_condiciones);
}
