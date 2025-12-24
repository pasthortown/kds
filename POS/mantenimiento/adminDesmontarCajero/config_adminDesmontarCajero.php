<?php

////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////
///////////DESCRIPCION: DESMONTAR CAJERO ///////////////////////////////////////////
////////////////TABLAS: Control_Estacion, Periodo, Estacion/////////////////////////
////////FECHA CREACION: 13/10/2015//////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR       : Christian Pinto //////////////////////////////////////
///////DESCRIPCION          : Remediación para mejorar la Mantenibilidad en el /////
////////////////////////////  código ///////////////////////////////////////////////
///////FECHA MODIFICACIÓN   : 17/10/2016 ///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

include_once("../../system/conexion/clase_sql.php");
include_once ("../../clases/clase_adminDesmontarCajero.php");

$lc_config = new desmontarCajero();
$lc_cadena = $_SESSION['cadenaId']/* 10 */;
$lc_usuarioId = $_SESSION['usuarioId']/* 8 */;
$lc_rest = $_SESSION['rstId']/* 40 */;
$lc_ip = $_SESSION['direccionIp']/* '::1' */;

if (htmlspecialchars(isset($_POST["cargarBotonesDias"]))) {

    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_ip;
    $lc_condiciones[2] = $lc_cadena;
    print $lc_config->fn_consultar("cargarBotonesDias", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["cargarfechas"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_POST["dias"]);
    $lc_condiciones[3] = $lc_rest;
    print $lc_config->fn_consultar("cargarfechas", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["muestraUsuariosEstado"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["fecha"]);
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_POST["prd_id"]);
    print $lc_config->fn_consultar("muestraUsuariosEstado", $lc_condiciones);
     
} elseif (htmlspecialchars(isset($_POST["formasPagoInactivo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_ip;
    $lc_condiciones[3] = htmlspecialchars($_POST["usr_id"]);
    $lc_condiciones[4] = htmlspecialchars($_POST["fechai"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["ctrc_id"]);
    print $lc_config->fn_consultar("formasPagoInactivo", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["totalesPagoInactivo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_ip;
    $lc_condiciones[3] = htmlspecialchars($_POST["usr_id"]);
    $lc_condiciones[4] = htmlspecialchars($_POST["fechai"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["ctrc_id"]);
    print $lc_config->fn_consultar("totalesPagoInactivo", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["formasPagoActivo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_ip;
    $lc_condiciones[3] = htmlspecialchars($_POST["usr_id"]);
    $lc_condiciones[4] = htmlspecialchars($_POST["fechai"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["ctrc_id"]);
    print $lc_config->fn_consultar("formasPagoActivo", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["totalesPagoActivo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_ip;
    $lc_condiciones[3] = htmlspecialchars($_POST["usr_id"]);
    $lc_condiciones[4] = htmlspecialchars($_POST["fechai"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["ctrc_id"]);
    print $lc_config->fn_consultar("totalesPagoActivo", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["consultaformaPago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_ip;
    $lc_condiciones[3] = htmlspecialchars($_POST["usr_id_cajero"]);
    $lc_condiciones[4] = htmlspecialchars($_POST["ctrc_id"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["prd_id"]);
    print $lc_config->fn_consultar("consultaformaPago", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["consultatotalEstacion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_ip;
    $lc_condiciones[2] = htmlspecialchars($_POST["usr_id_cajero"]);
    $lc_condiciones[3] = htmlspecialchars($_POST["ctrc_id"]);
    print $lc_config->fn_consultar("consultatotalEstacion", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["consultaBilletes"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_ip;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = htmlspecialchars($_POST["codigo_ctrEstacion"]);
    print $lc_config->fn_consultar("consultaBilletes", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["grabaBilletes"]))) {
    $lc_condiciones5 = ($_POST["cantidades2"]); //cantidad de billetes
    $lc_condiciones = ($_POST["resultado2"]); //valor total de billetes
    $lc_condiciones3 = ($_POST["oculto"]); //id de billete
    $lc_condiciones4 = ($_POST["oculto2"]); //id de control estacion
    $longitudArrayBilletes = count($lc_condiciones5);
    for ($i = 0; $i < $longitudArrayBilletes; $i++) {
        $lc_condiciones2[0] = htmlspecialchars($_POST["accion"]);
        $lc_condiciones2[1] = $lc_condiciones[$i];
        $lc_condiciones2[2] = $lc_condiciones3[$i]; //id de billete
        $lc_condiciones2[3] = $lc_condiciones4[$i];
        $lc_condiciones2[4] = $lc_condiciones5[$i];
        $lc_condiciones2[5] = $lc_usuarioId;
        $lc_condiciones2[6] = htmlspecialchars($_POST["tipoEfectivo"]);
        print $lc_config->fn_ejecutar("grabaBilletes", $lc_condiciones2);
    }
    
} elseif (htmlspecialchars(isset($_POST["auditoriaEfectivo"]))) {
    $lc_condiciones10[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones10[1] = $lc_usuarioId;
    $lc_condiciones10[2] = $lc_rest;
    $lc_condiciones10[3] = htmlspecialchars($_POST["auditoriaTotal"]);
    print $lc_config->fn_ejecutar("auditoriaEfectivo", $lc_condiciones10);
    
} elseif (htmlspecialchars(isset($_POST["consultaidBilletes"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = htmlspecialchars($_POST["top"]);
    $lc_condiciones[3] = 0;
    print $lc_config->fn_consultar("consultaidBilletes", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["grabaArqueo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_POST["formaPago"]);
    $lc_condiciones[4] = htmlspecialchars($_POST["montoActual"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["accion_int"]);
    $lc_condiciones[6] = htmlspecialchars($_POST["ctrEstacion"]);
    $lc_condiciones[7] = 0;
    $lc_condiciones[8] = htmlspecialchars($_POST["retiroValor"]);
    $lc_condiciones[9] = htmlspecialchars($_POST["transacciones"]);
    $lc_condiciones[10] = htmlspecialchars($_POST["posCalculado"]);
    $lc_condiciones[11] = htmlspecialchars($_POST["diferencia"]);
    $lc_condiciones[12] = htmlspecialchars($_POST["estadoSwt"]);
    print $lc_config->fn_ejecutar("grabaArqueo", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["consultaformaPagoModificado"]))) {
    $lc_condiciones[0] = $lc_ip;
    $lc_condiciones[1] = htmlspecialchars($_POST["id_usuariO"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = htmlspecialchars($_POST["ctr_estacion"]);
    print $lc_config->fn_consultar("consultaformaPagoModificado", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["calculatotalesModificados"]))) {
    $lc_condiciones[0] = $lc_ip;	
    $lc_condiciones[1] = htmlspecialchars($_POST["userId"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = htmlspecialchars($_POST["ctr_estacion"]);
    print $lc_config->fn_consultar("calculatotalesModificados", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["consultaTarjeta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_ip;
    $lc_condiciones[2] = htmlspecialchars($_POST["idPago"]);
    $lc_condiciones[3] = $lc_rest;
    $lc_condiciones[4] = htmlspecialchars($_POST["id_usuario"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["ctrEstacion"]);
    $lc_condiciones[6] = htmlspecialchars($_POST["estadoSwt"]);
    print $lc_config->fn_consultar("consultaTarjeta", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["grabaarqueotarjeta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_POST["idPago"]);
    $lc_condiciones[4] = htmlspecialchars($_POST["totaltarjeta"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["accion_int"]);
    $lc_condiciones[6] = htmlspecialchars($_POST["ctrEstacion"]);
    $lc_condiciones[7] = 0;
    $lc_condiciones[8] = htmlspecialchars($_POST["retiroValor"]);
    $lc_condiciones[9] = htmlspecialchars($_POST["transacciones"]);
    $lc_condiciones[10] = htmlspecialchars($_POST["posCalculado"]);
    $lc_condiciones[11] = htmlspecialchars($_POST["diferencia"]);
    $lc_condiciones[12] = htmlspecialchars($_POST["estadoSwt"]);
    print $lc_config->fn_ejecutar("grabaarqueotarjeta", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["auditoriaTarjeta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_POST["totalTarjeta"]);
    $lc_condiciones[4] = htmlspecialchars($_POST["tipoTarjeta"]);
    print $lc_config->fn_ejecutar("auditoriaTarjeta", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["consultaformaPagoModificadoTarjeta"]))) {
    $lc_condiciones[0] = $lc_ip;
    $lc_condiciones[1] = htmlspecialchars($_POST["id_User"]);
    $lc_condiciones[2] = $lc_usuarioId;
    $lc_condiciones[3] = htmlspecialchars($_POST["ctrEstacion"]);
    $lc_condiciones[4] = htmlspecialchars($_POST["formaPago"]);
    print $lc_config->fn_consultar("consultaformaPagoModificadoTarjeta", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["totalesIngresados"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["User"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = htmlspecialchars($_POST["ctrEstacion"]);
    print $lc_config->fn_consultar("totalesIngresados", $lc_condiciones);
   
} elseif (htmlspecialchars(isset($_POST["totalesPos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["t_idUsuario"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["ctrEstacion"]);
    print $lc_config->fn_consultar("totalesPos", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["validaMontoDescuadre"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_POST["fpf_total_pagar"]);
    print $lc_config->fn_consultar("validaMontoDescuadre", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["actualizaCajeroMotivo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars(strtoupper(utf8_decode($_POST["motivoDescuadre"])));
    $lc_condiciones[4] = htmlspecialchars($_POST["accion_int"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["usr_id_cajero"]);
    $lc_condiciones[6] = htmlspecialchars($_POST["controlEstacion"]);
    print $lc_config->fn_ejecutar("actualizaCajeroMotivo", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["auditoriaCajero"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = htmlspecialchars($_POST["accion_int"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["usr_id_cajero"]);
    $lc_condiciones[6] = htmlspecialchars($_POST['controlEstacion']);
    print $lc_config->fn_ejecutar("auditoriaCajero", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["actualizaCajero"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["usr_id_cajero"]);
    $lc_condiciones[2] = $_SESSION['rstId'];
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = htmlspecialchars($_POST['controlEstacion']);
    print $lc_config->fn_ejecutar("actualizaCajero", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["traeMotivosDescuadre"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    print $lc_config->fn_consultar("traeMotivosDescuadre", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["traeUsuarioAdmin"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_ip;
    print $lc_config->fn_consultar("traeUsuarioAdmin", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["imprimeDesmontadoCajero"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["usr_id"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["ctrc_id"]);
    $lc_condiciones[2] = $lc_usuarioId;
    print $lc_config->fn_ejecutar("imprimeDesmontadoCajero", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["cargarFormasPago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["ctr_id"]);
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = 0;
    print $lc_config->fn_consultar("cargarFormasPago", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["grabaarqueoformapago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_POST["fmp_id"]);
    $lc_condiciones[4] = htmlspecialchars($_POST["totalFormaPago"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["accion_int"]);
    $lc_condiciones[6] = htmlspecialchars($_POST["ctrc_id"]);
    $lc_condiciones[7] = htmlspecialchars($_POST["banderafp"]);
    $lc_condiciones[8] = 0;
    $lc_condiciones[9] = 0;
    $lc_condiciones[10] = 0;
    $lc_condiciones[11] = 0;
    $lc_condiciones[12] = 0;
    print $lc_config->fn_ejecutar("grabaarqueoformapago", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["eliminaFormasPagoAgregadas"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = htmlspecialchars($_POST["ctrc_id"]);
    $lc_condiciones[7] = htmlspecialchars($_POST["banderafp"]);
    $lc_condiciones[8] = 0;
    $lc_condiciones[9] = 0;
    $lc_condiciones[10] = 0;
    $lc_condiciones[11] = 0;
    $lc_condiciones[12] = 0;
    print $lc_config->fn_ejecutar("eliminaFormasPagoAgregadas", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["traeValorFormaPago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["ctrc_id"]);
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_POST["fmp_id"]);
    print $lc_config->fn_consultar("traeValorFormaPago", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["existeCuentaAbiertaMesa"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_usuarioId;
    print $lc_config->fn_consultar("existeCuentaAbiertaMesa", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["existeCuentaAbierta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_usuarioId;
    print $lc_config->fn_consultar("existeCuentaAbierta", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["retirofondo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["usr_id_cajero"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["controlEstacion"]);
    print $lc_config->fn_consultar("retirofondo", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["consultaCupones"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_ip;
    $lc_condiciones[3] = htmlspecialchars($_POST["usr_id_cajero"]);
    $lc_condiciones[4] = htmlspecialchars($_POST["ctrc_id"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["prd_id"]);
    print $lc_config->fn_consultar("consultaCupones", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["eliminaBilletes"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["ctrc_id"]);
    print $lc_config->fn_ejecutar("eliminaBilletes", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["DesmontadoDirecto"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["usr_id_cajero"]);
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = htmlspecialchars($_POST["controlEstacion"]);
    print $lc_config->fn_ejecutar("DesmontadoDirecto", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["reporteFinDeDia"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["periodo"]);
    $lc_condiciones[1] = '';
    $lc_condiciones[2] = $lc_usuarioId;
    print $lc_config->fn_consultar("reporteFinDeDia", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["actualizarValorDeclarado"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_POST["fpm_id"]);
    $lc_condiciones[2] = htmlspecialchars($_POST["ctrc_id"]);
    $lc_condiciones[3] = htmlspecialchars($_POST["usr_id_admin"]);
    $lc_condiciones[4] = htmlspecialchars($_POST["valor"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["tpenv_id"]);
    print $lc_config->fn_ejecutar("actualizarValorDeclarado", $lc_condiciones);
    
} elseif (htmlspecialchars(isset($_POST["validarUsuarioAdministrador"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["usr_Admin"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = 'A';
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = 0;
    print $lc_config->fn_consultar("validarUsuarioAdministrador", $lc_condiciones);
}elseif(htmlspecialchars(isset($_POST["validar_eventos_restaurante"]))) {
    $accion = htmlspecialchars($_POST["accion"]);
    $cadena = $lc_cadena ;
    $restaurante =$lc_rest;
    print $lc_config->validar_eventos_restaurante($accion,$cadena,$restaurante);//consultar parámetros
}elseif(htmlspecialchars(isset($_POST["validar_periodo"]))) {
    $periodo =htmlspecialchars($_POST["periodo"]);
    print $lc_config->validar_eventos_restaurante($accion,$cadena,$restaurante);//consultar parámetros
}elseif(htmlspecialchars(isset($_POST["host_local"]))) {
    print $lc_config->host_local();
}elseif(htmlspecialchars(isset($_POST["datos_conexion"]))) {
    print $lc_config->datos_conexion();
}elseif(htmlspecialchars(isset($_POST["datos_telegram"]))) {
    $dato =htmlspecialchars($_POST["dato"]);
    print $lc_config->datos_telegram($dato);
}elseif(htmlspecialchars(isset($_POST["eliminarRegistroCajaChica"]))) {
    $cod_cajero=htmlspecialchars($_POST["cod_cajero"]);
    $fecha=htmlspecialchars($_POST["fecha"]);
    $idControlEstacion = htmlspecialchars($_POST["idControlEstacion"]);
    print $lc_config->eliminarRegistroCajaChica(array('cod_cajero'=>$cod_cajero,'fecha'=>$fecha, 'idControlEstacion' => $idControlEstacion));
}