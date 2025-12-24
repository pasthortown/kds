<?php


include_once"../../system/conexion/clase_sql.php";
include_once "../../clases/clase_adminDesPluprecio.php";

$lc_config   = new DescargaPluPrecio();
$lc_cadena	 = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];
$lc_restaurante = $_SESSION['rstId'];

if(htmlspecialchars(isset($_GET["cargarGerente"]))){

    $lc_condiciones[1] = htmlspecialchars($_GET["plu"]);
    $lc_condiciones[2] = $lc_cadena;
    print $lc_config->fn_consultar("cargarPluSG", $lc_condiciones);
}

if(htmlspecialchars(isset($_GET["cargarPluMaxp"]))){

    $lc_condiciones[1] = htmlspecialchars($_GET["plu"]);
    $lc_condiciones[2] = $lc_cadena;
    print $lc_config->fn_consultar("cargarPluMaxp", $lc_condiciones);
}

if(htmlspecialchars(isset($_GET["cargarPluPrecioMaxp"]))){

    $lc_condiciones[1] = htmlspecialchars($_GET["plu"]);
    $lc_condiciones[2] = $lc_cadena;
    print $lc_config->fn_consultar("cargarPluPrecioMaxp", $lc_condiciones);
}

if(htmlspecialchars(isset($_GET["cargarGerentePlus"]))){

    $lc_condiciones[1] = htmlspecialchars($_GET["plu"]);
    $lc_condiciones[2] = $lc_cadena;
    print $lc_config->fn_consultar("cargarGerentePlus", $lc_condiciones);
}

if(htmlspecialchars(isset($_GET["CargarTablaMaxPlus"]))){

    $lc_condiciones[1] = htmlspecialchars($_GET["plu"]);
    $lc_condiciones[2] = $lc_cadena;
    print $lc_config->fn_consultar("CargarTablaMaxPlus", $lc_condiciones);
}

if(htmlspecialchars(isset($_GET["CargarTablaMaxPrecios"]))){

    $lc_condiciones[1] = htmlspecialchars($_GET["plu"]);
    $lc_condiciones[2] = $lc_cadena;
    print $lc_config->fn_consultar("CargarTablaMaxPrecios", $lc_condiciones);
}

?>