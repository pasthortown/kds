<?php
@session_start();

include("../system/conexion/clase_sql.php");
include("../clases/clase_campanaSolidaria.php");

$cadena             = $_SESSION['cadenaId'];
$restaurante        = $_SESSION['rstId'];
$usuario            = $_SESSION['usuarioId'];
$controlEstacion    = $_SESSION['IDControlEstacion'];
$estacion           = $_SESSION['estacionId'];

$lc_config = new CampanaSolidaria();

// REQUEST
$request = (object) filter_input_array(INPUT_POST);

if ($request->metodo === "aplicaCampanaSolidaria") {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $cadena;
    print $lc_config->fn_consultar("aplicaCampanaSolidaria", $lc_condiciones);
}

if ($request->metodo === "registrarCampanaSolidaria") {
    $lc_condiciones[0] = $cadena;
    $lc_condiciones[1] = $restaurante;
    $lc_condiciones[2] = $_POST["valorTotal"];
    $lc_condiciones[3] = $_POST["valorUnitario"];
    $lc_condiciones[4] = $_POST["cantidad"];
    $lc_condiciones[5] = $_POST["secuencia"];
    $lc_condiciones[6] = $controlEstacion;
    $lc_condiciones[7] = $estacion;
    $lc_condiciones[8] = $usuario;
    print $lc_config->fn_consultar("registrarCampanaSolidaria", $lc_condiciones);
}

if ($request->metodo === "anularCampanaSolidaria") {
    $lc_condiciones[0] = $_POST["IDretiro"];
    print $lc_config->fn_consultar("anularCampanaSolidaria", $lc_condiciones);
}