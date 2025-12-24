<?php

session_start();

//////////////////////////////////////////////////////////////////////////////////
////////MODIFICADO POR: CHRISTIAN PINTO///////////////////////////////////////////
///////////DESCRIPCION: NUEVOS ESTILOS INGRESO Y ACTUALIZACION DE ESTACION CON////
/////////////////////// TABLA MODAL //////////////////////////////////////////////
////////////////TABLAS: Estacion,SWT_Tipo_Envio///////////////////////////////////
////////FECHA CREACION: 01/06/2015////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_adminCadena.php';

if (
    empty($_SESSION['rstId'])
    OR empty($_SESSION['usuarioId'])
    OR empty($_SESSION['cadenaId'])
) {
    die(json_encode((object)[
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_adminCadena = new adminCadena();

$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];

if (htmlspecialchars(isset($_POST["cargaCadena"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['accion']);
    $lc_condiciones[1] = $lc_cadena;
    print $lc_adminCadena->fn_consultar("cargaCadena", $lc_condiciones);
} else if (htmlspecialchars(isset($_POST["cargaColeccionDeDatos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['accion']);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_POST['idColeccioncadena']);
    print $lc_adminCadena->fn_consultar("cargaColeccionDeDatos", $lc_condiciones);
} else if (htmlspecialchars(isset($_POST["cargaColeccionDatosC"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['accion']);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_POST['idColeccionCadenaa']);
    print $lc_adminCadena->fn_consultar("cargaColeccionDatosC", $lc_condiciones);
} else if (htmlspecialchars(isset($_POST["editarColeccionDeDatos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['accion']);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_POST['idColeccioncadenaM']);
    $lc_condiciones[3] = htmlspecialchars($_POST['idDatosColeccionM']);
    print $lc_adminCadena->fn_consultar("editarColeccionDeDatos", $lc_condiciones);
} else if (htmlspecialchars(isset($_POST["grabaCadenaColeccionDatos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['accion']);
    $lc_condiciones[1] = htmlspecialchars($_POST['idColeccionDatoscadena']);
    $lc_condiciones[2] = $_POST['caracter'];
    $lc_condiciones[3] = htmlspecialchars($_POST['entero']);
    $lc_condiciones[4] = htmlspecialchars($_POST['fecha']);
    $lc_condiciones[5] = htmlspecialchars($_POST['numerico']);
    $lc_condiciones[6] = htmlspecialchars($_POST['fechaInicio']);
    $lc_condiciones[7] = htmlspecialchars($_POST['fechaFin']);
    $lc_condiciones[8] = htmlspecialchars($_POST['minimo']);
    $lc_condiciones[9] = htmlspecialchars($_POST['maximo']);
    $lc_condiciones[10] = $lc_usuario;
    $lc_condiciones[11] = htmlspecialchars($_POST['idColeccionCadena']);
    $lc_condiciones[12] = $lc_cadena;
    $lc_condiciones[13] = htmlspecialchars($_POST['seleccione']);
    $lc_condiciones[14] = htmlspecialchars($_POST['isactive']);
    print $lc_adminCadena->fn_consultar("grabaCadenaColeccionDatos", $lc_condiciones);
} else if (htmlspecialchars(isset($_POST["actualizaCadenaColeccionDatos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['accion']);
    $lc_condiciones[1] = htmlspecialchars($_POST['idColeccionDatoscadenaM']);
    $lc_condiciones[2] = $_POST['caracterM'];
    $lc_condiciones[3] = htmlspecialchars($_POST['enteroM']);
    $lc_condiciones[4] = htmlspecialchars($_POST['fechaM']);
    $lc_condiciones[5] = htmlspecialchars($_POST['numericoM']);
    $lc_condiciones[6] = htmlspecialchars($_POST['fechaInicioM']);
    $lc_condiciones[7] = htmlspecialchars($_POST['fechaFinM']);
    $lc_condiciones[8] = htmlspecialchars($_POST['minimoM']);
    $lc_condiciones[9] = htmlspecialchars($_POST['maximoM']);
    $lc_condiciones[10] = $lc_usuario;
    $lc_condiciones[11] = htmlspecialchars($_POST['idColeccionCadenaM']);
    $lc_condiciones[12] = $lc_cadena;
    $lc_condiciones[13] = htmlspecialchars($_POST['seleccioneM']);
    $lc_condiciones[14] = htmlspecialchars($_POST['isactive']);
    print $lc_adminCadena->fn_consultar("actualizaCadenaColeccionDatos", $lc_condiciones);
} else if (htmlspecialchars(isset($_POST["cargaColeccionDeDatosT"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_POST['estado']));
    print $lc_adminCadena->fn_consultaColeccionDeDatosTrasferenciaVentas($lc_condiciones);
} else if (htmlspecialchars(isset($_POST["cargarCadenas"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = 1;
    print $lc_adminCadena->fn_consultaCadenas($lc_condiciones);
} else if (htmlspecialchars(isset($_POST["guardarTransferenciaVentas"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_POST['cdn_id_origen']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_POST['cdn_id_destino']));
    $lc_condiciones[3] = $lc_usuario;
    $lc_condiciones[4] = '0';
    $lc_condiciones[5] = '0';
    $lc_condiciones[6] = '0';
    print $lc_adminCadena->fn_grabaTransferenciaVentas($lc_condiciones);
} else if (htmlspecialchars(isset($_POST["inactivarTransferenciaVentas"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = '0';
    $lc_condiciones[2] = '0';
    $lc_condiciones[3] = $lc_usuario;
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_POST['ID_ColeccionCadena']));
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_POST['ID_ID_ColeccionDeDatosCadena']));
    $lc_condiciones[6] = htmlspecialchars(utf8_decode($_POST['estado']));
    print $lc_adminCadena->fn_inactivarTransferencia($lc_condiciones);
} elseif (htmlspecialchars(isset($_POST["guardarClienteExternoVoucher"]))) {

    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST["opcion"]));
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_POST["descripcion"]));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_POST["documento"]));
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_POST["valor"]));
    $lc_condiciones[6] = json_encode($_POST['informacionVoucher']);

    print $lc_adminCadena->fn_guardarClienteExternoVoucher($lc_condiciones);
} elseif (htmlspecialchars(isset($_POST["traerListaVoucherAerolineas"]))) {

    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST["opcion"]));
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_POST["descripcion"]));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_POST["documento"]));
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_POST["valor"]));
    $lc_condiciones[6] = json_encode($_POST['informacionVoucher']);

    print $lc_adminCadena->fn_traerListaVoucherAerolineas($lc_condiciones);
} elseif (htmlspecialchars(isset($_POST["buscarMontoVoucher"]))) {

    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST["opcion"]));
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = "";
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_POST["ID_ColeccionCadena"]));
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_POST["ID_ColeccionDeDatosCadena"]));

    print $lc_adminCadena->fn_buscarMontoVoucher($lc_condiciones);
} elseif (htmlspecialchars(isset($_POST["guardarMontoVoucher"]))) {

    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST["opcion"]));
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = empty($_POST["valor"]) ? '0' : htmlspecialchars(utf8_decode($_POST["valor"]));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_POST["ID_ColeccionCadena"]));
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_POST["ID_ColeccionDeDatosCadena"]));

    print $lc_adminCadena->fn_guardarMontoVoucher($lc_condiciones);
} elseif (htmlspecialchars(isset($_POST["actualizarStatusCliente"]))) {

    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST["opcion"]));
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_POST["ID_ColeccionCadena"]));
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_POST["ID_ColeccionDeDatosCadena"]));
    $lc_condiciones[6] = htmlspecialchars(utf8_decode($_POST["isActive"]));
    
    print $lc_adminCadena->fn_actualizarStatusCliente($lc_condiciones);
}

