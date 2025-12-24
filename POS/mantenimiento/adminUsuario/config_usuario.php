<?php

session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de cambio de clave ///////////////////////////////
///////TABLAS INVOLUCRADAS: Usuario del Sistema ///////////////////////////////
///////FECHA CREACION: 22-01-2015 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once("../../system/conexion/clase_sql.php");
include_once("../../clases/clase_admusuario.php");

$lc_config = new usuario();

if (isset($_POST["actualizarClave"])) {
    $lc_condiciones[0] = $_POST["accion"];
    $lc_condiciones[1] = $_POST["usr_id"];
    $lc_condiciones[2] = $_POST["actual"];
    $lc_condiciones[3] = $_POST["nueva"];
    $lc_condiciones[4] = $_POST["confirmar"];
    print $lc_config->fn_consultar("actualizarClave", $lc_condiciones);

} else if (isset($_POST["consultarInformacionUsuario"])) {
    $lc_condiciones[0] = 1;
    $lc_condiciones[1] = $_POST["usr_id"];
    print $lc_config->fn_consultar("consultarInformacionUsuario", $lc_condiciones);
}