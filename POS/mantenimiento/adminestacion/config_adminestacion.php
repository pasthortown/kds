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
include_once '../../clases/clase_adminestacion.php';

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

$lc_estacion = new estacion();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];
$lc_ip = $_SESSION['direccionIp'];


if (htmlspecialchars(isset($_POST["USPcoleccionPagoPredeterminado"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_POST['idEstacionModifica']));
    $lc_condiciones[2] = $lc_cadena;
    print $lc_estacion->fn_consultaColeccionPagoPredeterminado($lc_condiciones);

} else if (htmlspecialchars(isset($_POST["USPcoleccionMediosAutorizadores"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_POST['idEstacionModificaMedioAutorizador']));
    $lc_condiciones[2] = $lc_cadena;
    print $lc_estacion->fn_consultaColeccionMediosAutorizadores($lc_condiciones);

} else if (htmlspecialchars(isset($_POST["cargaColeccionDatosTabla"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_POST['idColeccionTabla']));
    $lc_condiciones[2] = $lc_cadena;
    print $lc_estacion->fn_consultar("cargaColeccionDatosTabla", $lc_condiciones);

} else if (htmlspecialchars(isset($_POST["IAEColeccionPagoPredeterminado"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_POST['idFormaPagoN']));
    $lc_condiciones[2] = $lc_cadena;
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_POST['idEstacionNueva']));
    $lc_condiciones[4] = $lc_usuario;
    $lc_condiciones[5] = $lc_ip;    
    print $lc_estacion->fn_insertaColeccionPagoPredeterminado($lc_condiciones);

} else if (htmlspecialchars(isset($_POST["cargaColeccionDeDatos"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_POST['idColeccioncadena']));
    print $lc_estacion->fn_cargaColeccionDeDatos($lc_condiciones);

} else if (htmlspecialchars(isset($_POST["cargaColeccionDatosC"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_POST['idColeccionCadenaa']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_POST['idestac']));    
    print $lc_estacion->fn_cargaColeccionDatosC($lc_condiciones);

} else if (htmlspecialchars(isset($_POST["editarColeccionDeDatos"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_POST['idColeccioncadenaM']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_POST['idDatosColeccionM']));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_POST['lc_idacesta']));
    print $lc_estacion->fn_editarColeccionDeDatos($lc_condiciones);

} else if (htmlspecialchars(isset($_POST["grabaCadenaColeccionDatos"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_POST['idColeccionDatoscadena']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_POST['caracter']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_POST['entero']));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_POST['fecha']));
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_POST['numerico']));
    $lc_condiciones[6] = htmlspecialchars(utf8_decode($_POST['fechaInicio']));
    $lc_condiciones[7] = htmlspecialchars(utf8_decode($_POST['fechaFin']));
    $lc_condiciones[8] = htmlspecialchars(utf8_decode($_POST['minimo']));
    $lc_condiciones[9] = htmlspecialchars(utf8_decode($_POST['maximo']));
    $lc_condiciones[10] = $lc_usuario;
    $lc_condiciones[11] = htmlspecialchars(utf8_decode($_POST['idColeccionCadena']));
    $lc_condiciones[12] = htmlspecialchars(utf8_decode($_POST['idgrabaesta']));
    $lc_condiciones[13] = htmlspecialchars(utf8_decode($_POST['seleccione']));
    $lc_condiciones[14] = htmlspecialchars(utf8_decode($_POST['isactive']));
    print $lc_estacion->fn_grabaCadenaColeccionDatos($lc_condiciones);

} else if (htmlspecialchars(isset($_POST["actualizaCadenaColeccionDatos"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_POST['idColeccionDatoscadenaM']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_POST['caracterM']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_POST['enteroM']));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_POST['fechaM']));
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_POST['numericoM']));
    $lc_condiciones[6] = htmlspecialchars(utf8_decode($_POST['fechaInicioM']));
    $lc_condiciones[7] = htmlspecialchars(utf8_decode($_POST['fechaFinM']));
    $lc_condiciones[8] = htmlspecialchars(utf8_decode($_POST['minimoM']));
    $lc_condiciones[9] = htmlspecialchars(utf8_decode($_POST['maximoM']));
    $lc_condiciones[10] = $lc_usuario;
    $lc_condiciones[11] = htmlspecialchars(utf8_decode($_POST['idColeccionCadenaM']));
    $lc_condiciones[12] = htmlspecialchars(utf8_decode($_POST['idactualizaesta']));
    $lc_condiciones[13] = htmlspecialchars(utf8_decode($_POST['seleccioneM']));
    $lc_condiciones[14] = htmlspecialchars(utf8_decode($_POST['isactive']));
    print $lc_estacion->fn_actualizaCadenaColeccionDatos($lc_condiciones);

} else if (htmlspecialchars(isset($_POST["IAEColeccionMedioAutorizador"]))) {
    $lc_condiciones[0] = 'D'; //$_POST['accion'];   
    $lc_condiciones[1] = '0'; //$_POST['madioA'];   
    $lc_condiciones[2] = $lc_cadena;
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_POST['idEstacionNueva']));
    $lc_condiciones[4] = $lc_usuario;
    $lc_condiciones[5] = $lc_ip;
    $lc_estacion->fn_IAEColeccionMedioAutorizador($lc_condiciones);
    $lc_condiciones2 = array_map('utf8_decode', $_POST['madioA']);
    $longitudArrayMediosA = count($lc_condiciones2);
    for ($i = 0; $i < $longitudArrayMediosA; $i++) {
        $lc_condiciones1[0] = htmlspecialchars(utf8_decode($_POST['accion']));
        $lc_condiciones1[1] = htmlspecialchars(utf8_decode($lc_condiciones2[$i]));
        $lc_condiciones1[2] = $lc_cadena;
        $lc_condiciones1[3] = htmlspecialchars(utf8_decode($_POST['idEstacionNueva']));
        $lc_condiciones1[4] = $lc_usuario;
        $lc_condiciones1[5] = $lc_ip; //id_menu (implode separa elementos de array por comas )        
        print $lc_estacion->fn_IAEColeccionMedioAutorizador($lc_condiciones1);
    }

} else if (htmlspecialchars(isset($_POST["cargaPagoPredeterminado"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_POST['idEstacionPagoPredeterminado']));
    $lc_condiciones[2] = $lc_cadena;
    print $lc_estacion->fn_cargaPagoPredeterminado($lc_condiciones);

} else if (htmlspecialchars(isset($_POST["cargaMedioAutorizador"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_POST['idEstacionPagoPredeterminado']));
    $lc_condiciones[2] = $lc_cadena;
    print $lc_estacion->fn_cargaMedioAutorizador($lc_condiciones);

} else if (htmlspecialchars(isset($_POST["cargaConfiguracionPoliticasModificar"]))) {
    $lc_condiciones[0] = '1';
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_POST['idEstacionConfigM']));
    $lc_condiciones[2] = $lc_cadena;
    print $lc_estacion->fn_cargaConfiguracionPoliticasModificar($lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarrestaurante"]))) {
    $lc_condiciones[0] = 1;
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = 0;
    print $lc_estacion->fn_cargar_Restaurante($lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarDetalle"]))) {
    $lc_condiciones[0] = 0;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['lc_res']));
    $lc_condiciones[3] = 0;
    print $lc_estacion->fn_cargarDetalle($lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarNuevo"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['descripcionRestaurante']));
    print $lc_estacion->fn_cargarNuevo($lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarNumeroNombreCaja"]))) {
    $lc_condiciones[0] = 4;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['descripcionRestaurante']));
    $lc_condiciones[3] = 0;
    print $lc_estacion->fn_consultar("cargarNumeroNombreCaja", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarmenu"]))) {
    $lc_condiciones[0] = 1;
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $_GET["estacionid"];
    $lc_condiciones[3] = 0;
    print $lc_estacion->fn_consultar("cargarmenu", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarTipoCobro"]))) {
    $lc_condiciones[0] = 2;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = 0;
    print $lc_estacion->fn_consultar("cargarTipoCobro", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["grabamodificaestacion"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET['ip1']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['ip2']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['ip3']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET['ip4']));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET['lc_nombrenumeroIp']));
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_GET['lc_selres']));
    $lc_condiciones1 = array_map('utf8_decode', $_GET['menu']);
    //isset($_GET["grabamodificaestacion"]),ENT_QUOTES,'UTF-8')
    for ($i = 0; $i < 1; $i++) {
        $lc_condiciones[6] = $lc_condiciones1[$i];
    }
    $lc_condiciones[7] = htmlspecialchars(utf8_decode($_GET['tipoenvio']));
    $lc_condiciones[8] = htmlspecialchars(utf8_decode(ltrim($_GET['tid'])));
    $lc_condiciones[9] = htmlspecialchars(utf8_decode($_GET['estado']));
    $lc_condiciones[10] = htmlspecialchars(utf8_decode($_GET['lc_estacion']));
    $lc_condiciones[11] = htmlspecialchars(utf8_decode($_GET['lc_control']));
    $lc_condiciones[12] = $lc_usuario;
    $lc_condiciones[13] = htmlspecialchars(utf8_decode($_GET['lc_puntoemision']));
    $lc_condiciones[14] = htmlspecialchars(utf8_decode($_GET['idMesa']));
    $lc_condiciones[15] = 0;    
    print $lc_estacion->fn_ejecutar("grabamodificaestacion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["guardaVariosMenus"]))) {
    $lc_condiciones2 = array_map('utf8_decode', $_GET['menu']);
    $longitudArrayMenus = count($lc_condiciones2);
    for ($i = 0; $i < $longitudArrayMenus; $i++) {
        $lc_condiciones1[0] = 0;
        $lc_condiciones1[1] = 0;
        $lc_condiciones1[2] = 0;
        $lc_condiciones1[3] = 0;
        $lc_condiciones1[4] = 0;
        $lc_condiciones1[5] = htmlspecialchars(utf8_decode($_GET['lc_selres']));
        $lc_condiciones1[6] = $lc_condiciones2[$i]; //id_menu
        $lc_condiciones1[7] = 0;
        $lc_condiciones1[8] = 0;
        $lc_condiciones1[9] = 0;
        $lc_condiciones1[10] = htmlspecialchars(utf8_decode($_GET['lc_estacion']));
        $lc_condiciones1[11] = htmlspecialchars(utf8_decode($_GET['lc_control']));
        $lc_condiciones1[12] = $lc_usuario;
        $lc_condiciones1[13] = 0;
        $lc_condiciones1[14] = 0;
        $lc_condiciones1[15]=$i;
        print $lc_estacion->fn_ejecutar("grabamodificaestacion", $lc_condiciones1);
    }

} else if (htmlspecialchars(isset($_GET["guardaVariosMenusMod"]))) {
    $lc_condiciones[0] = 0;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_GET['lc_selres']));
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = 0;
    $lc_condiciones[8] = 0;
    $lc_condiciones[9] = 0;
    $lc_condiciones[10] = htmlspecialchars(utf8_decode($_GET['lc_estacion']));
    $lc_condiciones[11] = 5;
    $lc_condiciones[12] = $lc_usuario;
    $lc_condiciones[13] = 0;
    $lc_condiciones[14] = 0;
    $lc_condiciones[15] = 0;    
    print $lc_estacion->fn_ejecutar("grabamodificaestacion", $lc_condiciones);

    $lc_condiciones2 = array_map('utf8_decode', $_GET['menu']);
    $longitudArrayMenusM = count($lc_condiciones2);

    for ($i = 0; $i < $longitudArrayMenusM; $i++) {
        $lc_condiciones1[0] = 0;
        $lc_condiciones1[1] = 0;
        $lc_condiciones1[2] = 0;
        $lc_condiciones1[3] = 0;
        $lc_condiciones1[4] = 0;
        $lc_condiciones1[5] = htmlspecialchars(utf8_decode($_GET['lc_selres']));
        $lc_condiciones1[6] = $lc_condiciones2[$i]; //id_menu (implode separa elementos de array por comas )
        $lc_condiciones1[7] = 0;
        $lc_condiciones1[8] = 0;
        $lc_condiciones1[9] = 0;
        $lc_condiciones1[10] = htmlspecialchars(utf8_decode($_GET['lc_estacion']));
        $lc_condiciones1[11] = htmlspecialchars(utf8_decode($_GET['lc_control']));
        $lc_condiciones1[12] = $lc_usuario;
        $lc_condiciones1[13] = 0;
        $lc_condiciones1[14] = 0;
        $lc_condiciones1[15] = $i;    
        print $lc_estacion->fn_ejecutar("grabamodificaestacion", $lc_condiciones1);
    }

} else if (htmlspecialchars(isset($_POST["cargarestacionModifica"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['estacion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_POST['menu']));
    $lc_condiciones[2] = $lc_cadena;
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_POST['descripcionRestaurante']));
    print_r($lc_estacion->fn_consultar("cargarestacionModifica", $lc_condiciones));

} else if (htmlspecialchars(isset($_GET["cargarselmenu"]))) {
    $lc_condiciones[0] = 5;
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['lc_estacion']));
    $lc_condiciones[3] = 0;
    print $lc_estacion->fn_consultar("cargarselmenu", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["traerCanalesImpresion"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[2] = '0';
    $lc_condiciones[3] = '0';
    $lc_condiciones[4] = '0';
    $lc_condiciones[5] = '0';
    $lc_condiciones[6] = '0';
    $lc_condiciones[7] = '0';
    $lc_condiciones[8] = '0';
    $lc_condiciones[9] = htmlspecialchars(utf8_decode($_GET['est_id']));
    print $lc_estacion->fn_ejecutar("administracionCanalesImpresion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["traerImpresoras"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['restaurante']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET['cimp_id']));
    $lc_condiciones[4] = '0';
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_GET['idestacion']));
    $lc_condiciones[6] = '0';
    $lc_condiciones[7] = '0';
    $lc_condiciones[8] = '0';
    $lc_condiciones[9] = '0';
    print $lc_estacion->fn_ejecutar("administracionCanalesImpresion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["traerPuertos"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['restaurante']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET['cimp_id']));
    $lc_condiciones[4] = '0';
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_GET['idestacion']));
    $lc_condiciones[6] = '0';
    $lc_condiciones[7] = '0';
    $lc_condiciones[8] = '0';
    $lc_condiciones[9] = '0';
    print $lc_estacion->fn_ejecutar("administracionCanalesImpresion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["guardaConfiguracionImpresion"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['restaurante']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET['canalimpresion']));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET['impresora']));
    $lc_condiciones[5] = '0';
    $lc_condiciones[6] = $lc_usuario;
    $lc_condiciones[7] = htmlspecialchars(utf8_decode($_GET['puerto']));
    $lc_condiciones[8] = htmlspecialchars(utf8_decode($_GET['impresorarespaldo']));
    $lc_condiciones[9] = '0';
    print $lc_estacion->fn_ejecutar("administracionCanalesImpresion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["traerImpresorasMod"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['restaurante']));
    $lc_condiciones[3] = $_GET['cimp_id'];
    $lc_condiciones[4] = '0';
    $lc_condiciones[5] = $_GET['idestacion'];
    $lc_condiciones[6] = $lc_usuario;
    $lc_condiciones[7] = '0';
    $lc_condiciones[8] = '0';
    $lc_condiciones[9] = '0';
    print $lc_estacion->fn_ejecutar("administracionCanalesImpresion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["guardaConfiguracionImpresionMod"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['restaurante']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET['canalimpresion']));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET['impresora'])); //
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_GET['idestacion']));
    $lc_condiciones[6] = $lc_usuario;
    $lc_condiciones[7] = htmlspecialchars(utf8_decode($_GET['puerto']));
    $lc_condiciones[8] = htmlspecialchars(utf8_decode($_GET['impresorarespaldo']));
    $lc_condiciones[9] = '0';
    print $lc_estacion->fn_ejecutar("administracionCanalesImpresion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarDetalleInactivos"]))) {
    $lc_condiciones[0] = 3;
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['lc_res']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET['estado']));
    print $lc_estacion->fn_consultar("cargarDetalleInactivos", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["validaPuertos"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['restaurante']));
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = $lc_usuario;
    $lc_condiciones[7] = htmlspecialchars(utf8_decode($_GET['puerto']));
    $lc_condiciones[8] = '0';
    $lc_condiciones[9] = '0';
    print $lc_estacion->fn_ejecutar("administracionCanalesImpresion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["validaPuertosMod"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['restaurante']));
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_GET['idestacion']));
    $lc_condiciones[6] = $lc_usuario;
    $lc_condiciones[7] = htmlspecialchars(utf8_decode($_GET['puerto']));
    $lc_condiciones[8] = '0';
    $lc_condiciones[9] = '0';
    print $lc_estacion->fn_ejecutar("administracionCanalesImpresion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["grabamodificaestacionnuevo"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET['ip1']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['ip2']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['ip3']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET['ip4']));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET['lc_nombrenumeroIp']));
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_GET['lc_selres']));
    $lc_condiciones[6] = htmlspecialchars(utf8_decode($_GET['menu']));
    $lc_condiciones[7] = htmlspecialchars(utf8_decode($_GET['tipoenvio']));
    $lc_condiciones[8] = htmlspecialchars(utf8_decode(ltrim($_GET['tid'])));
    $lc_condiciones[9] = htmlspecialchars(utf8_decode($_GET['estado']));
    $lc_condiciones[10] = 0;
    $lc_condiciones[11] = htmlspecialchars(utf8_decode($_GET['lc_control']));
    $lc_condiciones[12] = $lc_usuario;
    $lc_condiciones[13] = 0;
    $lc_condiciones[14] = 0;
     $lc_condiciones[15] = 0;    
    print $lc_estacion->fn_ejecutar("grabamodificaestacion", $lc_condiciones);

    $lc_condiciones2 = array_map('utf8_decode', $_GET['menu']);

    $longitudArrayEstacion = count($lc_condiciones);
    for ($i = 0; $i < $longitudArrayEstacion; $i++) {
        $lc_condiciones1[0] = 0;
        $lc_condiciones1[1] = 0;
        $lc_condiciones1[2] = 0;
        $lc_condiciones1[3] = 0;
        $lc_condiciones1[4] = 0;
        $lc_condiciones1[5] = htmlspecialchars(utf8_decode($_GET['lc_selres']));
        $lc_condiciones1[6] = $lc_condiciones2[$i]; //id_menu
        $lc_condiciones1[7] = 0;
        $lc_condiciones1[8] = 0;
        $lc_condiciones1[9] = 0;
        $lc_condiciones1[10] = htmlspecialchars(utf8_decode($_GET['lc_estacion']));
        $lc_condiciones1[11] = 0;
        $lc_condiciones1[12] = $lc_usuario;
        $lc_condiciones1[13] = 0;
        $lc_condiciones1[14] = 0;
        $lc_condicionesl[15] = $i;    
        print $lc_estacion->fn_ejecutar("grabamodificaestacion", $lc_condiciones1);
    }

} else if (htmlspecialchars(isset($_GET["guardaDesasignarEstacion"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['est_id']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['restaurante']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET['desasigna']));
    $lc_condiciones[4] = $lc_usuario;
    print $lc_estacion->fn_ejecutar("guardaDesasignarEstacion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargaDesasignarEstacion"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['est_id']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['restaurante']));
    $lc_condiciones[3] = $lc_usuario;
    print $lc_estacion->fn_consultar("cargaDesasignarEstacion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["modificaDesasignarEstacion"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['est_id']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['restaurante']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET['desasigna']));
    $lc_condiciones[4] = $lc_usuario;
    print $lc_estacion->fn_ejecutar("guardaDesasignarEstacion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["guardaTipoEnvio"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['est_id']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['restaurante']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET['tipoEnvio']));
    $lc_condiciones[4] = $lc_usuario;
    print $lc_estacion->fn_ejecutar("guardaTipoEnvio", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargaTipoEnvio"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['est_id']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['restaurante']));
    $lc_condiciones[3] = $lc_usuario;
    print $lc_estacion->fn_consultar("cargaTipoEnvio", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["modificaTipoEnvio"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['est_id']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET['restaurante']));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET['tipoEnvio']));
    $lc_condiciones[4] = $lc_usuario;
    print $lc_estacion->fn_ejecutar("guardaTipoEnvio", $lc_condiciones);

} else if (htmlspecialchars(isset($_POST["eliminarCanalImpresoraEstacion"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_POST['IDCanalImpresion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_POST['IDEstacion']));
    print $lc_estacion->fn_eliminarCanalImpresoraEstacion($lc_condiciones);

} else if (htmlspecialchars(isset($_POST["consultaColeccionMesa"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($lc_cadena));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_POST['idRestaurante']));
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_POST['idEstacion']));
    print $lc_estacion->fn_consultaColeccionMesa($lc_condiciones);
    
}