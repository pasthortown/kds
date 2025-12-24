<?php
require_once "../../clases/clase_adminCliente.php";

$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];

$adminClientes = new AdminClientes();
$politicaPantallaClientes = $adminClientes->cargarPoliticaPantallaClientes($idCadena);
if(true === $politicaPantallaClientes->estado && 0 < $politicaPantallaClientes->numRegistros){
    $politica = $politicaPantallaClientes->datos[0];
    $cargarCamposConfigurados = $adminClientes->cargarCamposConfigurados($idCadena);
    $camposConfigurados=$cargarCamposConfigurados->datos;

    $cargarCamposTablaCliente = $adminClientes->cargarcamposTablaCliente($idCadena);
    $camposTablaCliente = $cargarCamposTablaCliente->datos;

}

