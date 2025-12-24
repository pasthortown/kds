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