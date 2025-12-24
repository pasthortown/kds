<?php

/*
FECHA CREACION   : 04/10/2016 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion de promociones
*/

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_ConsultaSoporte.php";

$lc_config = new ConsultaSoporte();
$idUsuario = $_SESSION['usuarioId'];
$lc_cadena = $_SESSION['cadenaId'];

if (htmlspecialchars(isset($_POST["cargaRestaurantes"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = "0";
    $lc_condiciones[5] = "0";
    print $lc_config->fn_cargarRestaurantes($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["cargarDetallePeriodo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_POST["IDRestaurante"]);
    $lc_condiciones[3] = htmlspecialchars($_POST["SelecionConsulta"]);
    $lc_condiciones[4] = "0";
    $lc_condiciones[5] = "0";
    print $lc_config->fn_cargarDetalle($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["cargarDetalleDesmontado"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_POST["IDRestaurante"]);
    $lc_condiciones[3] = htmlspecialchars($_POST["SelecionConsulta"]);
    $lc_condiciones[4] = "0";
    $lc_condiciones[5] = "0";
    print $lc_config->fn_cargarDetalleDesmontado($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["cargarDetalleFindeDia"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_POST["IDRestaurante"]);
    $lc_condiciones[3] = htmlspecialchars($_POST["SelecionConsulta"]);
    $lc_condiciones[4] = "0";
    $lc_condiciones[5] = "0";
    print $lc_config->fn_cargarDetalleFindeDia($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["imprimirReporte"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["tipo"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["IDCanalMovimiento"]);
    $lc_condiciones[3] = $idUsuario;
    $lc_condiciones[4] = htmlspecialchars($_POST["IDRestaurante"]);
    $lc_condiciones[5] = "0";
    $lc_condiciones[6] = "0";
    $lc_condiciones[7] = "0";
    $lc_condiciones[8] = "0";
    $lc_condiciones[9] = "0";
    print $lc_config->fn_imprimirReporte($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["abrirPeriodo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = $idUsuario;
    $lc_condiciones[4] = htmlspecialchars($_POST["IDRestaurante"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["IDPeriodo"]);
    $lc_condiciones[6] = "0";
    $lc_condiciones[7] = "0";
    $lc_condiciones[8] = "0";
    $lc_condiciones[9] = "0";
    print $lc_config->fn_imprimirReporte($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["cargarSeleccionPeriodo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_POST["IDRestaurante"]);
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = "0";
    $lc_condiciones[5] = "0";
    print $lc_config->fn_cargarSeleccionPeriodo($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["cargarSeleccionCajero"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_POST["IDRestaurante"]);
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = "0";
    $lc_condiciones[5] = htmlspecialchars($_POST["IDPeriodo"]);
    print $lc_config->fn_cargarSeleccionCajero($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["cargarSeleccionAdmin"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = "0";
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = "0";
    $lc_condiciones[5] = "0";
    print $lc_config->fn_cargarSeleccionAdmin($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["cargarSeleccionEstacion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_POST["IDRestaurante"]);
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = "0";
    $lc_condiciones[5] = htmlspecialchars($_POST["IDPeriodo"]);
    print $lc_config->fn_cargarSeleccionEstacion($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["crearReporte"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["tipo"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["reporte"]);
    $lc_condiciones[3] = $idUsuario;
    $lc_condiciones[4] = "0";
    $lc_condiciones[5] = "0";
    $lc_condiciones[6] = htmlspecialchars($_POST["IDControlEstacion"]);
    $lc_condiciones[7] = htmlspecialchars($_POST["IDUsuarioCajero"]);
    $lc_condiciones[8] = htmlspecialchars($_POST["IDUsuarioAdmin"]);
    $lc_condiciones[9] = htmlspecialchars($_POST["IDEstacion"]);
    print $lc_config->fn_imprimirReporte($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["infoAplicaApiImpresionCrearReporte"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["IDParametro"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["Accion"]);
    print $lc_config->fn_infoAplicaApiImpresionCrearReporte($lc_condiciones);
}

else if (htmlspecialchars(isset($_POST["cargarFacturas"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);

    print $lc_config->fn_cargaFacturas($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["cargarNotasCredito"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);

    print $lc_config->fn_cargaNotasCredito($lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["cargarOrdenespedido"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);

    print $lc_config->fn_cargaOrdenesPedido($lc_condiciones);
    //echo json_encode([]);
    //var_dump(json_decode($lc_config->fn_cargaOrdenesPedido($lc_condiciones)));
    
}

else if (htmlspecialchars(isset($_POST["reimprimirDocumentos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["idcanalmovimiento"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["transaccion"]);

    print $lc_config->fn_reImprimirDocumento($lc_condiciones);
}