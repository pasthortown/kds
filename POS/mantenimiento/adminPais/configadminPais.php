<?php

/* * *******************************************************
 *          DESARROLLADO POR: Alex Merino                *  
 *          DESCRIPCION: Clase negocio pais              *
 *          FECHA CREACION: 06/06/2018                   *  
 * ******************************************************* */

include("../../system/conexion/clase_sql.php");
include("../../clases/clase_adminPais.php");

$lc_objeto = new pais();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];

if (isset($_GET["lstPais"])) {
    $lc_condiciones[0] = 1; //Accion 1 del procedimiento almacenado
    $lc_condiciones[1] = htmlspecialchars($_GET['idPais']);
    print $lc_objeto->fn_consultar("lstPais", $lc_condiciones);
} else
if (isset($_GET["infPais"])) {
    $lc_condiciones[0] = 2; //Accion 2 del procedimiento almacenado   
    $lc_condiciones[1] = htmlspecialchars($_GET['idPais']);
    print $lc_objeto->fn_consultar("infPais", $lc_condiciones);
} else
if (isset($_GET["infConfiguracionPais"])) {
    $lc_condiciones[0] = 3; //Accion 3 del procedimiento almacenado  
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    print $lc_objeto->fn_consultar("infConfiguracionPais", $lc_condiciones);
} else
if (isset($_GET["guardarDatosPais"])) {
    $lc_condiciones[0] = 1; //Modificar    
    $lc_condiciones[1] = htmlspecialchars($_GET['idPais']);
    $lc_condiciones[2] = htmlspecialchars($_GET['simbolo']);
    $lc_condiciones[3] = htmlspecialchars($_GET['moneda']);
    $lc_condiciones[4] = htmlspecialchars($_GET['base']);
    $lc_condiciones[5] = $lc_usuario;
    print $lc_objeto->fn_ingresarBines("guardarDatosPais", $lc_condiciones);
} else
if (isset($_GET["CargarColeccionPais"])) {
    $lc_condiciones[0] = 1; //accion 1 cargar colecciones de Pais
    $lc_condiciones[1] = htmlspecialchars($_GET['id_emp']);
    print $lc_objeto->fn_consultar("cargarColeccionPais", $lc_condiciones);
} else
if (isset($_GET["ListarColeccionxPais"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['empresa']);
    $lc_condiciones[2] = htmlspecialchars($_GET['lc_IDColeccionPais_edit']);
    $lc_condiciones[3] = htmlspecialchars($_GET['lc_IDColeccionDeDatosPais_edit']);
    print $lc_objeto->fn_consultar("ListarColeccionxPais", $lc_condiciones);
} else

if (htmlspecialchars(isset($_GET["modificarPaisColeccion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["lc_IDColeccionPais_edit"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["lc_IDColeccionDeDatosPais_edit"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["varchar"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["entero"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["fecha"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["seleccion"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["numerico"]);
    $lc_condiciones[8] = htmlspecialchars($_GET["fecha_inicio"]);
    $lc_condiciones[9] = htmlspecialchars($_GET["fecha_fin"]);
    $lc_condiciones[10] = htmlspecialchars($_GET["minimo"]);
    $lc_condiciones[11] = htmlspecialchars($_GET["maximo"]);
    $lc_condiciones[12] = htmlspecialchars($_GET["IDUsuario"]);
    $lc_condiciones[13] = htmlspecialchars($_GET["estado"]);
    print $lc_objeto->fn_consultar("modificarPaisColeccion", $lc_condiciones);
} else
if (htmlspecialchars(isset($_GET["detalleColeccionPais"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['id_emp']);
    print $lc_objeto->fn_consultar("detalleColeccionPais", $lc_condiciones);
} else
if (htmlspecialchars(isset($_GET["datosColeccionPais"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['empresa']);
    $lc_condiciones[2] = htmlspecialchars($_GET['IDColeccionPais']);
    print $lc_objeto->fn_consultar("datosColeccionPais", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["insertarPaisColeccion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["IDColecciondeDatosPais"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["IDColeccionPais"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["varchar"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["entero"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["fecha"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["seleccion"]);
    $lc_condiciones[8] = htmlspecialchars($_GET["numerico"]);
    $lc_condiciones[9] = htmlspecialchars($_GET["fecha_inicio"]);
    $lc_condiciones[10] = htmlspecialchars($_GET["fecha_fin"]);
    $lc_condiciones[11] = htmlspecialchars($_GET["minimo"]);
    $lc_condiciones[12] = htmlspecialchars($_GET["maximo"]);
    $lc_condiciones[13] = htmlspecialchars($_GET["IDUsuario"]);
    $lc_condiciones[14] = 0;
    print $lc_objeto->fn_consultar("insertarPaisColeccion", $lc_condiciones);
}
?>