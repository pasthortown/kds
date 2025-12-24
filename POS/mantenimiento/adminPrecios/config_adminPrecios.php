<?php

session_start();
///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jose Fernandez //////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de precios///////////////////////
///////FECHA CREACION: 22-06-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once"../../system/conexion/clase_sql.php";
include_once "../../clases/clase_adminPrecios.php";
$lc_estacion = new adminPrecios();
$lc_rest = $_SESSION['rstId'];
$lc_usuarioId = $_SESSION['usuarioId'];
//$_SESSION['cadenaId']=7;

if (htmlspecialchars(isset($_GET["detalleReportePrecios"]))) {
    $lc_datos[0] = $_SESSION['cadenaId'];
    $lc_datos[1] = 'cadena';
    $lc_datos[2] = htmlspecialchars($_GET["valoresReporte"]);
    
    print $lc_estacion->fn_consultar("detalleReportePrecios", $lc_datos);
    
} else if (htmlspecialchars(isset($_GET["preview"]))) {
    $lc_datos[0] = htmlspecialchars($_GET["idProgramacion"]);
    $lc_datos[1] = 'programacion';
    $lc_datos[2] = htmlspecialchars($_GET["idProgramacion"]);
    
    print $lc_estacion->fn_consultar("detalleReportePrecios", $lc_datos);
    
} else if (htmlspecialchars(isset($_GET["previewCiudadesYrestaurantes"]))) {
    $lc_datos[0] = htmlspecialchars($_GET["idProPrecios"]);
    
    print $lc_estacion->fn_consultar("previewCiudadesYrestaurantes", $lc_datos);
    
} else if (htmlspecialchars(isset($_GET["guardaTemporalmenteTrama"]))) {
    $lc_datos[0] = htmlspecialchars($_GET["pvpNuevoTemporal"]);
    $lc_datos[1] = htmlspecialchars($_GET["pvpAntiguoTemporal"]);
    $lc_datos[2] = htmlspecialchars($_GET["catTemporal"]);
    $lc_datos[3] = htmlspecialchars($_GET["pluTemporal"]);
    
    print $lc_estacion->fn_consultar("guardaTemporalmenteTrama", $lc_datos);
    
} else if (htmlspecialchars(isset($_GET["cargarDetalleCategorias"]))) {
    $lc_datos[0] = htmlspecialchars($_GET["cadenaDetalle"]);
    
    print $lc_estacion->fn_consultar("cargarDetalleCategorias", $lc_datos);
    
} else if (htmlspecialchars(isset($_GET["cargaCategoriasTraerPrecios"]))) {
    $lc_datos[0] = htmlspecialchars($_GET["cdnIdTraePrecios"]);
    
    print $lc_estacion->fn_consultar("cargarDetalleCategorias", $lc_datos);
    
} else if (htmlspecialchars(isset($_GET["traerPreciosUnaCategoria"]))) {
    $lc_datos[1] = $_SESSION['cadenaId'];
    $lc_datos[0] = htmlspecialchars($_GET["catTraePrecios"]);
    
    print $lc_estacion->fn_consultar("traerPreciosUnaCategoria", $lc_datos);
    
} else if (htmlspecialchars(isset($_GET["cargaPreciosCategorias"]))) {
    $lc_datos[0] = htmlspecialchars($_GET["cdnId"]);
    $lc_datos[1] = htmlspecialchars($_GET["idCategorias"]);
    $lc_datos[2] = htmlspecialchars($_GET["opcionCanal"]);
    $lc_datos[3] = htmlspecialchars($_GET["idCanales"]);
    
    print $lc_estacion->fn_consultar("cargaPreciosCategorias", $lc_datos);
    
} else if (htmlspecialchars(isset($_GET["cargaPreciosCategoriasPorMasterPlu"]))) {
    $lc_datos[0] = htmlspecialchars($_GET["cdnId"]);
    $lc_datos[1] = htmlspecialchars($_GET["idCategorias"]);
    
    print $lc_estacion->fn_consultar("cargaPreciosCategoriasPorMasterPlu", $lc_datos);
    
} else if (htmlspecialchars(isset($_GET["cargarCanales"]))) {
    print $lc_estacion->fn_consultar("cargarCanales", '');
    
} else if (htmlspecialchars(isset($_POST["grabaCadenaPrecios"]))) {
    $lc_datos[0] = htmlspecialchars($_POST["cadenaPrecios"]);
    $lc_datos[1] = htmlspecialchars($_POST["fechaAplicacion"]);
    $lc_datos[2] = $lc_usuarioId;
    $lc_datos[3] = htmlspecialchars($_POST["opcion"]);
    $lc_datos[4] = htmlspecialchars($_POST["cadenaPreciosAntiguos"]);
    $lc_datos[5] = $_SESSION['cadenaId'];
    $lc_datos[6] = htmlspecialchars($_POST['horaAplicacion']);
    $lc_datos[7] = htmlspecialchars($_POST['categoria']);
    $lc_datos[8] = htmlspecialchars($_POST['canalIndica']);
    $lc_datos[9] = htmlspecialchars($_POST['idOpciondecanal']);
    
    print $lc_estacion->fn_consultar("grabaCadenaPrecios", $lc_datos);
    
} else if (htmlspecialchars(isset($_GET["cargaProgramaciones"]))) {
    $lc_datos[0] = $_SESSION['cadenaId'];
    
    print $lc_estacion->fn_consultar("cargaProgramaciones", $lc_datos);
    
} else if (htmlspecialchars(isset($_GET["cancelaProgramacion"]))) {
    $lc_datos[0] = htmlspecialchars($_GET['programacionId']);
    $lc_datos[1] = 'A';
    
    print $lc_estacion->fn_consultar("cancelaProgramacion", $lc_datos);
    
} else if (htmlspecialchars(isset($_GET["aplicarPrecios"]))) {
    print $lc_estacion->aplicarPrecios();
}