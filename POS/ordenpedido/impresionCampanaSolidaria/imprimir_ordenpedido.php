<?php

include_once "../../system/conexion/clase_sql.php";

$retiro ='';
$cadena='';
$restaurante='';

if (isset($_GET["retiro"])) {
    $retiro = htmlspecialchars($_GET["retiro"]);
}
if (isset($_GET["cadena"])) {
    $cadena = htmlspecialchars($_GET["cadena"]);
}
if (isset($_GET["restaurante"])) {
    $restaurante = htmlspecialchars($_GET["restaurante"]);
}

$lc_TipoReporte = htmlspecialchars($_GET["tipoReporte"]);
$lc_campañaSolidaria = "CampanaSolidaria";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>

<?php

    if ($lc_TipoReporte === $lc_campañaSolidaria) {
        include_once("../../clases/clase_campanaSolidaria.php");
        $impresion = new CampanaSolidaria();
        $respuesta = $impresion->impresionCampanaSolidaria($retiro, $cadena, $restaurante);
        print $respuesta["head"];
        print $respuesta["totales"];
        print $respuesta["firma"];
        print $respuesta["mensaje"];

    }
?>

</html>


