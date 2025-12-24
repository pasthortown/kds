<?php

/* * *******************************************************
 *          DESARROLLADO POR: Alex Merino                *  
 *          DESCRIPCION: Clase negocio bines             *
 *          FECHA CREACION: 16/04/2018                   *  
 * ******************************************************* */

include("../../system/conexion/clase_sql.php");
include("../../clases/clase_adminConfiguracionBines.php");

$lc_objeto = new bines();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];

if (isset($_GET["lstBines"])) {
    $lc_condiciones[0] = 1; //Accion 1 del procedimiento almacenado
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    $lc_condiciones[2] = htmlspecialchars('NA');
    print $lc_objeto->fn_consultar("lstBines", $lc_condiciones);
} else
if (isset($_GET["infBines"])) {
    $lc_condiciones[0] = 2; //Accion 2 del procedimiento almacenado   
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    $lc_condiciones[2] = htmlspecialchars($_GET['idColecciondedatosCadena']);
    print $lc_objeto->fn_consultar("infBines", $lc_condiciones);
} else
if (isset($_GET["infFormaPago"])) {
    $lc_condiciones[0] = 3; //Accion 3 del procedimiento almacenado  
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    $lc_condiciones[2] = htmlspecialchars('');
    print $lc_objeto->fn_consultar("infFormaPago", $lc_condiciones);
} else
if (isset($_GET["lsPoliticas"])) {
    $lc_condiciones[0] = 4; //Accion 4 del procedimiento almacenado  
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    $lc_condiciones[2] = htmlspecialchars('');
    print $lc_objeto->fn_consultar("lsPoliticas", $lc_condiciones);
} else
if (isset($_GET["lstDefiniciones"])) {
    $lc_condiciones[0] = 5; //Accion 5 del procedimiento almacenado
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    $lc_condiciones[2] = htmlspecialchars($_GET['idColeccionCadena']);
    print $lc_objeto->fn_consultar("lstDefiniciones", $lc_condiciones);
} else
if (isset($_GET["validateMinimo"])) {
    $lc_condiciones[0] = 6; //Accion 6 del procedimiento almacenado
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    $lc_condiciones[2] = htmlspecialchars($_GET['minimo']);
    print $lc_objeto->fn_consultar("validateMinimo", $lc_condiciones);
} else
if (isset($_GET["validateMaximo"])) {
    $lc_condiciones[0] = 7; //Accion 7 del procedimiento almacenado
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    $lc_condiciones[2] = htmlspecialchars($_GET['maximo']);
    print $lc_objeto->fn_consultar("validateMaximo", $lc_condiciones);
} else
if (isset($_GET["infValorConfiguracion"])) {
    $lc_condiciones[0] = 8; //Accion 9 del procedimiento almacenado
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    $lc_condiciones[2] = htmlspecialchars('');
    print $lc_objeto->fn_consultar("infValorConfiguracion", $lc_condiciones);
} else
if (isset($_GET["guardarDatosModificadosBines"])) {
    $lc_condiciones[0] = 1; //Modificar
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    $lc_condiciones[2] = htmlspecialchars($_GET['idCadena']);
    $lc_condiciones[3] = htmlspecialchars($_GET['idColeccionCadena']);
    $lc_condiciones[4] = htmlspecialchars($_GET['minimo']);
    $lc_condiciones[5] = htmlspecialchars($_GET['maximo']);
    $lc_condiciones[6] = htmlspecialchars($_GET['formpago']);
    $lc_condiciones[7] = htmlspecialchars($_GET['bandera']);  // Puede ser 1->Activo || 0-> Inactivo 
    $lc_condiciones[8] = $lc_usuario;
    print $lc_objeto->fn_ingresarBines("guardarDatosModificadosBines", $lc_condiciones);
} else
if (isset($_GET["guardarNuevoBin"])) {
    $lc_condiciones[0] = 2; //Insertar
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    $lc_condiciones[2] = htmlspecialchars($_GET['politicas']);
    $lc_condiciones[3] = htmlspecialchars($_GET['definicion']);
    $lc_condiciones[4] = htmlspecialchars($_GET['minimo']);
    $lc_condiciones[5] = htmlspecialchars($_GET['maximo']);
    $lc_condiciones[6] = htmlspecialchars($_GET['formapago']);
    $lc_condiciones[7] = htmlspecialchars($_GET['bandera']);  // Puede ser 1->Activo || 0-> Inactivo 
    $lc_condiciones[8] = $lc_usuario;
    print $lc_objeto->fn_ingresarBines("guardarNuevoBin", $lc_condiciones);
}
?>