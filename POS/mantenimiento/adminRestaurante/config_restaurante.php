<?php

session_start();
///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Configuración de Pantalla /////////////////////////////////
///////FECHA CREACION: 25-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_adminrestaurante.php';

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object)[
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_config = new restaurante();

$cadenaId = $_SESSION['cadenaId'];
$usuarioId = $_SESSION['usuarioId'];

if (htmlspecialchars(isset($_GET["cargarListaRestaurantes"]))) {
    $resultado = htmlspecialchars($_GET["resultado"]);
    $rst_id = htmlspecialchars($_GET["rst_id"]);
    $cdn_id = htmlspecialchars($_GET["cdn_id"]);

    print $lc_config->cargarListaRestaurantes($resultado, $rst_id, $cdn_id);

} else if (htmlspecialchars(isset($_GET["cargarCiudades"]))) {
    $rst_id = htmlspecialchars($_GET["rst_id"]);
    $cdn_id = htmlspecialchars($_GET["cdn_id"]);

    print $lc_config->cargarCiudades($rst_id, $cdn_id);

} else if (htmlspecialchars(isset($_GET["cargarCategoria"]))) {
    $rst_id = htmlspecialchars($_GET["rst_id"]);
    $cdn_id = htmlspecialchars($_GET["cdn_id"]);

    print $lc_config->cargarCategoria($rst_id, $cdn_id);

} else if (htmlspecialchars(isset($_GET["cargarTipoServicio"]))) {
    $rst_id = htmlspecialchars($_GET["rst_id"]);
    $cdn_id = htmlspecialchars($_GET["cdn_id"]);

    print $lc_config->cargarTipoServicio($rst_id, $cdn_id);

} else if (htmlspecialchars(isset($_GET["cargarTipoFacturacion"]))) {
    $rst_id = htmlspecialchars($_GET["rst_id"]);
    $cdn_id = htmlspecialchars($_GET["cdn_id"]);

    print $lc_config->cargarTipoFacturacion($rst_id, $cdn_id);

} else if (htmlspecialchars(isset($_GET["cargarInformacionRestaurante"]))) {
    $resultado = htmlspecialchars($_GET["resultado"]);
    $rst_id = htmlspecialchars($_GET["rst_id"]);
    $cdn_id = htmlspecialchars($_GET["cdn_id"]);

    print $lc_config->cargarInformacionRestaurante($resultado, $rst_id, $cdn_id);

} else if (htmlspecialchars(isset($_GET["cargarPisos"]))) {
    $resultado = htmlspecialchars($_GET["resultado"]);
    $rst_id = htmlspecialchars($_GET["rst_id"]);
    $cdn_id = htmlspecialchars($_GET["cdn_id"]);

    print $lc_config->cargarPisos($resultado, $rst_id, $cdn_id);

} else if (htmlspecialchars(isset($_GET["cargarAreas"]))) {
    $resultado = htmlspecialchars($_GET["resultado"]);
    $cdn_id = htmlspecialchars($_GET["cdn_id"]);
    $pis_id = htmlspecialchars($_GET["pis_id"]);

    print $lc_config->cargarAreas($resultado, $cdn_id, $pis_id);

} else if (htmlspecialchars(isset($_GET["administrarPisoArea"]))) {
    $accion = htmlspecialchars($_GET["accion"]);
    $resultado = htmlspecialchars($_GET["resultado"]);
    $rst_id = htmlspecialchars($_GET["rst_id"]);
    $pis_id = htmlspecialchars($_GET["pis_id"]);
    $arp_id = htmlspecialchars($_GET["arp_id"]);
    $std_id = htmlspecialchars($_GET["std_id"]);
    $descripcion = htmlspecialchars($_GET["descripcion"]);

    print $lc_config->administrarPisoArea($accion, $resultado, $rst_id, $pis_id, $arp_id, $std_id, $descripcion, $usuarioId);

} else if (htmlspecialchars(isset($_GET["agregarPiso"]))) {
    $accion = htmlspecialchars($_GET["accion"]);
    $resultado = htmlspecialchars($_GET["resultado"]);
    $rst_id = htmlspecialchars($_GET["rst_id"]);
    $pis_id = htmlspecialchars($_GET["pis_id"]);
    $arp_id = htmlspecialchars($_GET["arp_id"]);
    $std_id = htmlspecialchars($_GET["std_id"]);
    $descripcion = htmlspecialchars($_GET["descripcion"]) ;

    print $lc_config->agregarPiso($accion, $resultado, $rst_id, $pis_id, $arp_id, $std_id, $descripcion, $usuarioId);

} else if (htmlspecialchars(isset($_GET["agregarArea"]))) {
    $accion = htmlspecialchars($_GET["accion"]);
    $resultado = htmlspecialchars($_GET["resultado"]);
    $rst_id = htmlspecialchars($_GET["rst_id"]);
    $pis_id = htmlspecialchars($_GET["pis_id"]);
    $arp_id = htmlspecialchars($_GET["arp_id"]);
    $std_id = htmlspecialchars($_GET["std_id"]);
    $descripcion = htmlspecialchars($_GET["descripcion"]);

    print $lc_config->agregarArea($accion, $resultado, $rst_id, $pis_id, $arp_id, $std_id, $descripcion, $usuarioId);

} else if ((isset($_GET["pk"])) == 'modificarDescripcionArea') {
    $accion = 2;
    $resultado = -1;
    $rst_id = 0;
    $pis_id = 0;
    $arp_id = htmlspecialchars($_GET["name"]);
    $arp_id = substr($arp_id, 11, strlen($arp_id));
    $std_id = 0;
    $descripcion = htmlspecialchars($_GET["value"]);

    print $lc_config->administrarPisoArea($accion, $resultado, $rst_id, $pis_id, $arp_id, $std_id, $descripcion, $usuarioId);

} else if (htmlspecialchars(isset($_GET["modificarRestaurante"]))) {
    $accion = htmlspecialchars($_GET["accion"]);
    $resultado = htmlspecialchars($_GET["resultado"]);
    $rst_id = htmlspecialchars($_GET["rst_id"]);
    $rst_direccion = htmlspecialchars($_GET["rst_direccion"]);
    $rst_fono = htmlspecialchars($_GET["rst_fono"]);
    $ciu_id = htmlspecialchars($_GET["ciu_id"]);
    $tpsrv_rst = htmlspecialchars($_GET["tpsrv_rst"]);
    $rst_mid = htmlspecialchars($_GET["rst_mid"]);
    $tpfct_rst = htmlspecialchars($_GET["tpfct_rst"]);
    $rst_nmr_sr = htmlspecialchars($_GET["rst_nmr_sr"]);
    $rst_pnt_msn = htmlspecialchars($_GET["rst_pnt_msn"]);
    $rst_tmp_pdd = htmlspecialchars($_GET["rst_tmp_pdd"]);
    $rst_cnclr_pg = htmlspecialchars($_GET["rst_cnclr_pg"]);
    $rst_nmr_prsns = htmlspecialchars($_GET["rst_nmr_prsns"]);
    $rst_srvc = htmlspecialchars($_GET["rst_srvc"]);
    $rst_cntd_grms = htmlspecialchars($_GET["rst_cntd_grms"]);
    $rst_br_cjn = htmlspecialchars($_GET["rst_br_cjn"]);
    $std_id = htmlspecialchars($_GET["std_id"]);
    $cdn_id = htmlspecialchars($_GET["cdn_id"]);
    $idMetodoImpuesto = htmlspecialchars($_GET['idMetodoImpuesto']);
    $cat_rst = htmlspecialchars($_GET['cat_rst']);
    $horarioatencion = htmlspecialchars($_GET['horarioatencion']);

    print $lc_config->modificarRestaurante($accion, $resultado, $rst_id, $rst_direccion, $rst_fono, $ciu_id, $tpsrv_rst, $rst_mid, $tpfct_rst, $rst_nmr_sr, $rst_pnt_msn, $rst_tmp_pdd, $rst_cnclr_pg, $rst_nmr_prsns, $rst_srvc, $rst_cntd_grms, $rst_br_cjn, $std_id, $cdn_id, $usuarioId, $idMetodoImpuesto, $cat_rst, $horarioatencion);

} else if (htmlspecialchars(isset($_GET["cargarAutorizacionesRestaurantes"]))) {
    $resultado = htmlspecialchars($_GET["resultado"]);
    $rst_id = htmlspecialchars($_GET["rst_id"]);
    $cdn_id = htmlspecialchars($_GET["cdn_id"]);

    print $lc_config->cargarAutorizacionesRestaurantes($resultado, $rst_id, $cdn_id);

} else if (htmlspecialchars(isset($_GET["administrarAutorizacionRestaurante"]))) {
    $accion = htmlspecialchars($_GET["accion"]);
    $rst_id = htmlspecialchars($_GET["rst_id"]);
    $cdn_id = htmlspecialchars($_GET["cdn_id"]);
    $aur_id = htmlspecialchars($_GET["aur_id"]);
    $sec_ini = htmlspecialchars($_GET["sec_ini"]);
    $sec_fin = htmlspecialchars($_GET["sec_fin"]);
    $fecha_ini = htmlspecialchars($_GET["fecha_ini"]);
    $fecha_fin = htmlspecialchars($_GET["fecha_fin"]);

    print $lc_config->administrarAutorizacionRestaurante($accion, $rst_id, $cdn_id, $aur_id, $sec_ini, $sec_fin, $fecha_ini, $fecha_fin, $usuarioId);

} else if (htmlspecialchars(isset($_GET["cargarMetodoCalculoImpuesto"]))) {
    $cdn_id = htmlspecialchars($_GET["cdn_id"]);

    print $lc_config->cargarMetodoCalculoImpuesto($cdn_id);

} else if (htmlspecialchars(isset($_GET["cargarImpuestosCadena"]))) {
    print $lc_config->cargarImpuestosCadena($cadenaId);

} else if (htmlspecialchars(isset($_GET["cargarImpuestosRestaurante"]))) {
    $rst_id = htmlspecialchars($_GET['rst_id']);

    print $lc_config->cargarImpuestosRestaurante($rst_id, $cadenaId);

} else if (htmlspecialchars(isset($_GET["guardarImpuestosRestaurante"]))) {
    $accion = htmlspecialchars($_GET['accion']);
    $rst_id = htmlspecialchars($_GET['rst_id']);
    $impuestos = htmlspecialchars($_GET['impuestos']);

    print $lc_config->guardarImpuestosRestaurante($accion, $rst_id, $cadenaId, $usuarioId, $impuestos);

} else if (htmlspecialchars(isset($_GET["validarImpuestosRestaurante"]))) {
    $accion = htmlspecialchars($_GET['accion']);
    $rst_id = htmlspecialchars($_GET['rst_id']);
    $impuestos = htmlspecialchars($_GET['impuestos']);

    print $lc_config->validarImpuestosRestaurante($accion, $rst_id, $cadenaId, $usuarioId, $impuestos);

} else if (htmlspecialchars(isset($_GET["administrarColeccionRestaurante"]))) {
    $accion = htmlspecialchars($_GET['accion']);
    $rst_id = htmlspecialchars($_GET['rst_id']);

    print $lc_config->administrarColeccionRestaurante($accion, $rst_id);

} else if (htmlspecialchars(isset($_GET["detalleColeccionRestaurante"]))) {
    $accion = htmlspecialchars($_GET['accion']);
    $rst_id = htmlspecialchars($_GET['rst_id']);

    print $lc_config->detalleColeccionRestaurante($accion, $rst_id);

} else if (htmlspecialchars(isset($_GET["datosColeccionRestaurante"]))) {
    $accion = htmlspecialchars($_GET['accion']);
    $rst_id = htmlspecialchars($_GET['rst_id']);
    $IDColeccionRestaurante = htmlspecialchars($_GET['IDColeccionRestaurante']);

    print $lc_config->datosColeccionRestaurante($accion, $rst_id, $IDColeccionRestaurante);

} else if (htmlspecialchars(isset($_GET["guardarRestauranteColeccion"]))) {
    $accion = htmlspecialchars($_GET["accion"]);
    $IDColecciondeDatosRestaurante = htmlspecialchars($_GET["IDColecciondeDatosRestaurante"]);
    $IDColeccionRestaurante = htmlspecialchars($_GET["IDColeccionRestaurante"]);
    $IDRestaurante = htmlspecialchars($_GET["IDRestaurante"]);
    $varchar = htmlspecialchars($_GET["varchar"]);
    $entero = htmlspecialchars($_GET["entero"]);
    $fecha = htmlspecialchars($_GET["fecha"]);
    $seleccion = htmlspecialchars($_GET["seleccion"]);
    $numerico = htmlspecialchars($_GET["numerico"]);
    $fecha_inicio = htmlspecialchars($_GET["fecha_inicio"]);
    $fecha_fin = htmlspecialchars($_GET["fecha_fin"]);
    $minimo = htmlspecialchars($_GET["minimo"]);
    $maximo = htmlspecialchars($_GET["maximo"]);
    $IDUsuario = htmlspecialchars($_GET["IDUsuario"]);
    $estado = 0;

    print $lc_config->guardarRestauranteColeccion($accion, $IDColecciondeDatosRestaurante, $IDColeccionRestaurante, $IDRestaurante, $varchar, $entero, $fecha, $seleccion, $numerico, $fecha_inicio, $fecha_fin, $minimo, $maximo, $IDUsuario, $estado);

} else if (htmlspecialchars(isset($_GET["cargaRestauranteColeccion_edit"]))) {
    $accion = htmlspecialchars($_GET['accion']);
    $rst_id = htmlspecialchars($_GET['rst_id']);
    $IDColeccionRestaurante = htmlspecialchars($_GET['IDColeccionRestaurante']);
    $IDColecciondeDatosRestaurante = htmlspecialchars($_GET['IDColecciondeDatosRestaurante']);

    print $lc_config->cargaRestauranteColeccion_edit($accion, $rst_id, $IDColeccionRestaurante, $IDColecciondeDatosRestaurante);

} else if (htmlspecialchars(isset($_GET["modificarRestauranteColeccion"]))) {
    $accion = htmlspecialchars($_GET["accion"]);
    $IDColecciondeDatosRestaurante = htmlspecialchars($_GET["IDColecciondeDatosRestaurante"]);
    $IDColeccionRestaurante = htmlspecialchars($_GET["IDColeccionRestaurante"]);
    $IDRestaurante = htmlspecialchars($_GET["IDRestaurante"]);
    $varchar = htmlspecialchars($_GET["varchar"]);
    $entero = htmlspecialchars($_GET["entero"]);
    $fecha = htmlspecialchars($_GET["fecha"]);
    $seleccion = htmlspecialchars($_GET["seleccion"]);
    $numerico = htmlspecialchars($_GET["numerico"]);
    $fecha_inicio = htmlspecialchars($_GET["fecha_inicio"]);
    $fecha_fin = htmlspecialchars($_GET["fecha_fin"]);
    $minimo = htmlspecialchars($_GET["minimo"]);
    $maximo = htmlspecialchars($_GET["maximo"]);
    $IDUsuario = htmlspecialchars($_GET["IDUsuario"]);
    $estado = htmlspecialchars($_GET["estado"]);

    print $lc_config->guardarRestauranteColeccion($accion, $IDColecciondeDatosRestaurante, $IDColeccionRestaurante, $IDRestaurante, $varchar, $entero, $fecha, $seleccion, $numerico, $fecha_inicio, $fecha_fin, $minimo, $maximo, $IDUsuario, $estado);

//COLECCION IMPRESION TIPO DE DOCUMENTO     
} else if (htmlspecialchars(isset($_GET["CanalesImpresion"]))) {
    $accion = htmlspecialchars($_GET['accion']);
    $rst_id = htmlspecialchars($_GET['rst_id']);
    $cadena = htmlspecialchars($_GET['cadena']);

    print $lc_config->CanalesImpresion($accion, $rst_id, $cadena);

} else if (htmlspecialchars(isset($_POST["cargarTipoDocumento"]))) {
    $accion = htmlspecialchars($_POST['accion']);
    $rst_id = htmlspecialchars($_POST['rst_id']);
    $cadena = htmlspecialchars($_POST['cadena']);

    print $lc_config->cargarTipoDocumento($accion, $rst_id, $cadena);

} else if (htmlspecialchars(isset($_POST["cargarTipoDocumentoFactura"]))) {
    $accion = htmlspecialchars($_POST['accion']);
    $rst_id = htmlspecialchars($_POST['rst_id']);
    $cadena = htmlspecialchars($_POST['cadena']);
    $idColeccionDeDatosRestaurante = '0';

    print $lc_config->cargarTipoDocumentoRestaurante($accion, $rst_id, $cadena, $idColeccionDeDatosRestaurante);

} else if (htmlspecialchars(isset($_POST["cargarTipoDocumentoVoucher"]))) {
    $accion = htmlspecialchars($_POST['accion']);
    $rst_id = htmlspecialchars($_POST['rst_id']);
    $cadena = htmlspecialchars($_POST['cadena']);
    $idColeccionDeDatosRestaurante = '0';

    print $lc_config->cargarTipoDocumentoRestaurante($accion, $rst_id, $cadena, $idColeccionDeDatosRestaurante);

} else if (htmlspecialchars(isset($_POST["cargarTipoDocumentoLinea"]))) {
    $accion = htmlspecialchars($_POST['accion']);
    $rst_id = htmlspecialchars($_POST['rst_id']);
    $cadena = htmlspecialchars($_POST['cadena']);
    $idColeccionDeDatosRestaurante = '0';

    print $lc_config->cargarTipoDocumentoRestaurante($accion, $rst_id, $cadena, $idColeccionDeDatosRestaurante);

} else if (htmlspecialchars(isset($_POST["guardarImpresionTipoDocumentos"]))) {
    $accion = 'U';
    $rst_id = htmlspecialchars($_POST['rst_id']);
    $cadena = htmlspecialchars($_POST['cadena']);
    $factura = '0';
    $voucher = '0';
    $linea = '0';
    $usuario = htmlspecialchars($_POST['usuario']);

    $lc_config->guardarImpresionTipoDocumentos($accion, $rst_id, $cadena, $factura, $voucher, $linea, $usuario);

    $lc_condicionesF = $_POST['tipodocumentoF'];
    for ($i = 0; $i < count($lc_condicionesF); $i++) {
        $accion = 'F';
        $rst_id = htmlspecialchars($_POST['rst_id']);
        $cadena = htmlspecialchars($_POST['cadena']);
        $factura = $lc_condicionesF[$i];
        $voucher = '0';
        $linea = '0';
        $usuario = htmlspecialchars($_POST['usuario']);

        print $lc_config->guardarImpresionTipoDocumentos($accion, $rst_id, $cadena, $factura, $voucher, $linea, $usuario);
    }

    $lc_condicionesV = $_POST['tipodocumentoV'];
    for ($i = 0; $i < count($lc_condicionesV); $i++) {
        $accion = 'V';
        $rst_id = htmlspecialchars($_POST['rst_id']);
        $cadena = htmlspecialchars($_POST['cadena']);
        $factura = '0';
        $voucher = $lc_condicionesV[$i];
        $linea = '0';
        $usuario = htmlspecialchars($_POST['usuario']);

        print $lc_config->guardarImpresionTipoDocumentos($accion, $rst_id, $cadena, $factura, $voucher, $linea, $usuario);
    }

    $lc_condicionesL = $_POST['tipodocumentoL'];
    for ($i = 0; $i < count($lc_condicionesL); $i++) {
        $accion = 'L';
        $rst_id = htmlspecialchars($_POST['rst_id']);
        $cadena = htmlspecialchars($_POST['cadena']);
        $factura = '0';
        $voucher = '0';
        $linea = $lc_condicionesL[$i];
        $usuario = htmlspecialchars($_POST['usuario']);

        print $lc_config->guardarImpresionTipoDocumentos($accion, $rst_id, $cadena, $factura, $voucher, $linea, $usuario);
    }

} else if (htmlspecialchars(isset($_POST["validaTipoDocumentoCanalImpresion"]))) {
    $accion = htmlspecialchars($_POST['accion']);
    $rst_id = htmlspecialchars($_POST['rst_id']);
    $cadena = htmlspecialchars($_POST['cadena']);
    $tipodocumentoV = htmlspecialchars($_POST['tipodocumentoV']);

    print $lc_config->validaTipoDocumentoCanalImpresion($accion, $rst_id, $cadena, $tipodocumentoV);
}