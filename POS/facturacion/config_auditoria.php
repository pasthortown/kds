<?php

session_start();
//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Archivo de Configuración para////////////
/////////////////////Clientes/////////////////////////////////
///////TABLAS INVOLUCRADAS://///////////////////////////////// 
///////FECHA CREACION: 19-02-2014/////////////////////////////
//////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION:21/04/2014///////////////////
///////USUARIO QUE MODIFICO: Jose Fernandez//////////////////
///////DECRIPCION ULTIMO CAMBIO: cargar ciudad segun la tienda/
//////////////////////////////////////////////////////////////

include("../system/conexion/clase_sql.php");
include("../clases/clase_auditoria.php");

$lc_cliente = new Auditoria();

$restaurante = $_SESSION['rstId'];
$usuario = $_SESSION['usuarioId'];
$cadena = $_SESSION['cadenaId'];


if (htmlspecialchars(isset($_POST["auditoria"]))) {
    $lc_datos[0] = $usuario;
    $lc_datos[1] = $restaurante;
    $lc_datos[2] = htmlspecialchars($_POST["factura"]);
    $lc_datos[3] = htmlspecialchars($_POST["documento"]);
    $lc_datos[4] = htmlspecialchars($_POST["clave"]);
    print $lc_cliente->fn_guardar_auditoria($lc_datos);
} 