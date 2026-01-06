<?php

include_once "../system/conexion/clase_sql.php";
include_once "./clase_kds.php";

session_start();

$rst_id = $_SESSION['rstId'];
$est_id = $_SESSION['estacionId'];

$lc_kds = new kdsRegional();

if (isset($_POST["get_politicas_kds"])) {
    print($lc_kds->fn_get_politicas_kds_regional($rst_id, $est_id));
}

if (isset($_GET["obtener_rst_categoria"])) {
    $rst_id_param = isset($_GET["rst_id"]) ? intval($_GET["rst_id"]) : $rst_id;
    print($lc_kds->fn_obtener_rst_categoria($rst_id_param));
}