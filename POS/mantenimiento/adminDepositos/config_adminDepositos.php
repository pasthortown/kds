<?php

///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: JOSE FERNANDEZ///////////////////////////////////
///////////DESCRIPCION: PANTALLA DE DEPOSITOS /////////////////////////////
////////////////TABLAS: BILLTE_ESTACION, ARQUEO_CAJA///////////////////////
////////FECHA CREACION: 18-03-2016/////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

include_once"../../system/conexion/clase_sql.php";
include_once "../../clases/clase_adminDepositos.php";

$lc_config = new depositos();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuarioId = $_SESSION['usuarioId'];
$lc_rest = $_SESSION['rstId'];
$lc_ip = $_SESSION['direccionIp'];


if (isset($_GET["cargaCabeceraDeposito"])) {
    $lc_condiciones[0] = 2;
    $lc_condiciones[1] = htmlspecialchars($_GET["prd_idModifica"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["depositoId"]);
    print $lc_config->fn_consultar("cargaCabeceraDeposito", $lc_condiciones);
}

if (isset($_GET["cargaTotalesDepositoAModificar"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["deposiT"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["periT"]);
    print $lc_config->fn_consultar("cargaTotalesDepositoAModificar", $lc_condiciones);
}

if (isset($_GET["cargaTotalesDepositoNuevo"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["deposiN"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["periN"]);
    print $lc_config->fn_consultar("cargaTotalesDepositoNuevo", $lc_condiciones);
}

if (isset($_GET["consultaExisteArqueo"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_GET["periodoC"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["depositoC"]);
    print $lc_config->fn_consultar("consultaExisteArqueo", $lc_condiciones);
}

if (isset($_GET["cargaComboDepositos"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = '0'; //periodo
    $lc_condiciones[3] = '0'; //deposito
    print $lc_config->fn_consultar("cargaComboDepositos", $lc_condiciones);
}


if (isset($_GET["cargaConceptosAjuste"])) {
    print $lc_config->fn_consultar("cargaConceptosAjuste", '');
}

if (isset($_GET["asientaDepositoModificado"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["opcion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["depositoAs"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["periodoAs"]);
    $lc_condiciones[4] = $lc_usuarioId;
    $lc_condiciones[5] = htmlspecialchars($_GET["referenciaM"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["paleletaM"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["monedasM"]);
    $lc_condiciones[8] = htmlspecialchars($_GET["depositoViaM"]);
    print $lc_config->fn_ejecutar("asientaDepositoModificado", $lc_condiciones);
}

if (isset($_GET["eliminaAjusteAgregado"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = htmlspecialchars($_GET["depositoE"]);
    print $lc_config->fn_ejecutar("eliminaAjusteAgregado", $lc_condiciones);
}

if (isset($_GET["cargaAjusteModificar"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["perioddo"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["depositoo"]);
    $lc_condiciones[3] = $lc_cadena;
    print $lc_config->fn_consultar("cargaAjusteModificar", $lc_condiciones);
}

if (isset($_GET["cargaDepositos"])) {
    $lc_condiciones[0] = 1;
    $lc_condiciones[1] = htmlspecialchars($_GET["prd_id"]);
    $lc_condiciones[2] = '0';
    print $lc_config->fn_consultar("cargaDepositos", $lc_condiciones);
}

if (isset($_GET["insertaNuevoDeposito"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["periodoNuevo"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["referenciaNuevo"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["papeletaNuevo"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["fechaDepositoNuevo"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["monedasNuevo"]);
    $lc_condiciones[6] = $lc_usuarioId;
    $lc_condiciones[7] = '0';
    $lc_condiciones[8] = $lc_cadena;
    $lc_condiciones[9] = htmlspecialchars($_GET["depositoViaNUevo"]);
    $lc_condiciones[10] = htmlspecialchars($_GET["comentarioNuevo"]);
    print $lc_config->fn_consultar("insertaNuevoDeposito", $lc_condiciones);
}

if (isset($_GET["asientaDeposito"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accionAsienta"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["idperiodoAsienta"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["referenciaAsienta"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["papeletaAsienta"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["fechaDepositoAsienta"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["monedasAsienta"]);
    $lc_condiciones[6] = $lc_usuarioId;
    $lc_condiciones[7] = htmlspecialchars($_GET["depositoAsienta"]);
    $lc_condiciones[8] = $lc_cadena;
    $lc_condiciones[9] = htmlspecialchars($_GET["depositoViaAsienta"]);
    $lc_condiciones[10] = htmlspecialchars($_GET["comentarioAsienta"]);
    print $lc_config->fn_consultar("asientaDeposito", $lc_condiciones);
}

if (isset($_GET["consultaformaPago"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["prd_id"]);
    $lc_condiciones[2] = $lc_cadena;
    $lc_condiciones[3] = ''; //IDdeposito
    print $lc_config->fn_consultar("consultaformaPago", $lc_condiciones);
}

if (isset($_GET["consultaDetalleDepositoModificado"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["prd_idM"]);
    $lc_condiciones[2] = $lc_cadena;
    $lc_condiciones[3] = htmlspecialchars($_GET["depositoM"]);
    print $lc_config->fn_consultar("consultaDetalleDepositoModificado", $lc_condiciones);
}

if (isset($_GET["validaPeriodosAbiertos"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["periodoI"]);
    print $lc_config->fn_consultar("validaPeriodosAbiertos", $lc_condiciones);
}

if (isset($_GET["cargarBotonesDias"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_ip;
    $lc_condiciones[2] = $lc_cadena;
    print $lc_config->fn_consultar("cargarBotonesDias", $lc_condiciones);
}

if (isset($_GET["cargarfechas"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_GET["dias"]);
    $lc_condiciones[3] = $lc_rest;
    print $lc_config->fn_consultar("cargarfechas", $lc_condiciones);
}

if (isset($_GET["muestraUsuariosEstado"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["fecha"]);
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["prd_id"]);
    print $lc_config->fn_consultar("muestraUsuariosEstado", $lc_condiciones);
}

if (isset($_GET["formasPagoInactivo"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_ip;
    $lc_condiciones[3] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["fechai"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["ctrc_id"]);

    //$lc_condiciones[6]=	$lc_usuarioId;
    print $lc_config->fn_consultar("formasPagoInactivo", $lc_condiciones);
}

if (isset($_GET["totalesPagoInactivo"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_ip;
    $lc_condiciones[3] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["fechai"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["ctrc_id"]);

    print $lc_config->fn_consultar("totalesPagoInactivo", $lc_condiciones);
}

if (isset($_GET["formasPagoActivo"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_ip;
    $lc_condiciones[3] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["fechai"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["ctrc_id"]);
    print $lc_config->fn_consultar("formasPagoActivo", $lc_condiciones);
}

if (isset($_GET["totalesPagoActivo"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_ip;
    $lc_condiciones[3] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["fechai"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["ctrc_id"]);
    print $lc_config->fn_consultar("totalesPagoActivo", $lc_condiciones);
}

if (isset($_GET["consultaValorRetiroEfectivo"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_ip;
    $lc_condiciones[2] = $lc_usuarioId;
    $lc_condiciones[3] = htmlspecialchars($_GET["usr_id_cajero"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["ctrc_id"]);

    print $lc_config->fn_consultar("consultaValorRetiroEfectivo", $lc_condiciones);
}
if (isset($_GET["consultatotalEstacion"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_ip;
    $lc_condiciones[2] = htmlspecialchars($_GET["usr_id_cajero"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["ctrc_id"]);

    print $lc_config->fn_consultar("consultatotalEstacion", $lc_condiciones);
}

if (isset($_GET["consultatotalformaPago"])) {
    $lc_condiciones[0] = $lc_ip;
    $lc_condiciones[1] = htmlspecialchars($_GET["idforma"]);
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["usr_id_cajero"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["ctrc_id"]);

    print $lc_config->fn_consultar("consultatotalformaPago", $lc_condiciones);
}
if (isset($_GET["consultaBilletes"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["periodoI"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["idDep"]);
    print $lc_config->fn_consultar("consultaBilletes", $lc_condiciones);
}

if (isset($_GET["consultaBilletesModificados"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["periodoI"]); //$lc_ip;
    $lc_condiciones[3] = htmlspecialchars($_GET["idDep"]);
    print $lc_config->fn_consultar("consultaBilletesModificados", $lc_condiciones);
}

if (isset($_GET["grabaBilletes"])) {
    //$lc_condiciones6=$_GET["resNuevo"];//
    $lc_condiciones5 = htmlspecialchars($_GET["cantidades2"]); //cantidad de billetes
    $lc_condiciones = htmlspecialchars($_GET["resultado2"]); //valor total de billetes
    $lc_condiciones3 = htmlspecialchars($_GET["oculto"]); //id de billete
    $lc_condiciones4 = htmlspecialchars($_GET["oculto2"]); //id de deposito
    //$lc_condiciones6=$_GET["oculto3"];//id de usuario
    $longitudArrayBill = count($lc_condiciones5);
    for ($i = 0; $i < $longitudArrayBill; $i++) {
        $lc_condiciones2[0] = htmlspecialchars($_GET["accion"]);
        $lc_condiciones2[1] = $lc_condiciones[$i];
        $lc_condiciones2[2] = $lc_condiciones3[$i]; //id de billete
        $lc_condiciones2[3] = htmlspecialchars($_GET["oculto2"]); //id de deposito
        $lc_condiciones2[4] = $lc_condiciones5[$i];
        $lc_condiciones2[5] = $lc_usuarioId;
        $lc_condiciones2[6] = htmlspecialchars($_GET["tipoEfectivo"]);
        print $lc_config->fn_ejecutar("grabaBilletes", $lc_condiciones2);
    }
}

if (isset($_GET["grabaBilletesDirecto"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]); //cantidad de billetes
    $lc_condiciones[1] = htmlspecialchars($_GET["valoresD"]); //valor total de billetes
    $lc_condiciones[2] = htmlspecialchars($_GET["depositoD"]); //id de billete
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = htmlspecialchars($_GET["tipoEfectivo"]); //id de deposito
    print $lc_config->fn_ejecutar("grabaBilletesDirecto", $lc_condiciones);
}

if (isset($_GET["auditoriaEfectivo"])) {
    $lc_condiciones10[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones10[1] = $lc_usuarioId;
    $lc_condiciones10[2] = $lc_rest;
    $lc_condiciones10[3] = htmlspecialchars($_GET["auditoriaTotal"]);

    print $lc_config->fn_ejecutar("auditoriaEfectivo", $lc_condiciones10);
}

if (isset($_GET["consultaidBilletes"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = htmlspecialchars($_GET["top"]);
    $lc_condiciones[3] = 0;
    print $lc_config->fn_consultar("consultaidBilletes", $lc_condiciones);
}

if (isset($_GET["grabaArqueo"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["formaPago"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["resNuevo"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["accion_int"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["ctrEstacion"]);
    $lc_condiciones[7] = 0;

    print $lc_config->fn_ejecutar("grabaArqueo", $lc_condiciones);
}

if (isset($_GET["consultaformaPagoModificado"])) {
    $lc_condiciones[0] = $lc_ip; //$_GET["id_Estacion"];
    $lc_condiciones[1] = htmlspecialchars($_GET["id_usuariO"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = htmlspecialchars($_GET["ctr_estacion"]);

    print $lc_config->fn_consultar("consultaformaPagoModificado", $lc_condiciones);
}

if (isset($_GET["calculatotalesModificados"])) {
    $lc_condiciones[0] = $lc_ip; //$_GET["idestacion"];	
    $lc_condiciones[1] = htmlspecialchars($_GET["userId"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = htmlspecialchars($_GET["ctr_estacion"]);

    print $lc_config->fn_consultar("calculatotalesModificados", $lc_condiciones);
}

if (isset($_GET["consultaTarjeta"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_GET["idPago"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["periodoId"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["depositoCh"]);

    print $lc_config->fn_consultar("consultaTarjeta", $lc_condiciones);
}

if (isset($_GET["grabaarqueotarjeta"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["idPago"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["totaltarjeta"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["accion_int"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["ctrEstacion"]);
    $lc_condiciones[7] = 0;

    print $lc_config->fn_ejecutar("grabaarqueotarjeta", $lc_condiciones);
}//
if (isset($_GET["auditoriaTarjeta"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["totalTarjeta"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["tipoTarjeta"]);

    print $lc_config->fn_ejecutar("auditoriaTarjeta", $lc_condiciones);
}

if (isset($_GET["consultaformaPagoModificadoTarjeta"])) {
    $lc_condiciones[0] = $lc_ip; //$_GET["estaciond"];
    $lc_condiciones[1] = htmlspecialchars($_GET["id_User"]);
    $lc_condiciones[2] = $lc_usuarioId;
    $lc_condiciones[3] = htmlspecialchars($_GET["ctrEstacion"]);

    print $lc_config->fn_consultar("consultaformaPagoModificadoTarjeta", $lc_condiciones);
}

if (isset($_GET["totalesIngresados"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["User"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = htmlspecialchars($_GET["ctrEstacion"]);

    print $lc_config->fn_consultar("totalesIngresados", $lc_condiciones);
}

if (isset($_GET["totalesPos"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["t_idUsuario"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["ctrEstacion"]);
    print $lc_config->fn_consultar("totalesPos", $lc_condiciones);
}

if (isset($_GET["validaMontoDescuadre"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_GET["fpf_total_pagar"]);
    print $lc_config->fn_consultar("validaMontoDescuadre", $lc_condiciones);
}

if (isset($_GET["actualizaCajeroMotivo"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = strtoupper(utf8_decode($_GET["motivoDescuadre"]));
    $lc_condiciones[4] = htmlspecialchars($_GET["accion_int"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["usr_id_cajero"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["controlEstacion"]);

    print $lc_config->fn_ejecutar("actualizaCajeroMotivo", $lc_condiciones);
}

if (isset($_GET["auditoriaCajero"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = htmlspecialchars($_GET["accion_int"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["usr_id_cajero"]);
    $lc_condiciones[6] = htmlspecialchars($_GET['controlEstacion']);
    print $lc_config->fn_ejecutar("auditoriaCajero", $lc_condiciones);
}

if (isset($_GET["actualizaCajero"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["usr_id_cajero"]);
    $lc_condiciones[2] = $_SESSION['rstId'];
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = htmlspecialchars($_GET['controlEstacion']);

    print $lc_config->fn_ejecutar("actualizaCajero", $lc_condiciones);
}

if (isset($_GET["traeMotivosDescuadre"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    //$lc_condiciones[2]=	$lc_est_id;

    print $lc_config->fn_consultar("traeMotivosDescuadre", $lc_condiciones);
}

if (isset($_GET["traeUsuarioAdmin"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = /* $lc_est_id */$lc_ip;

    print $lc_config->fn_consultar("traeUsuarioAdmin", $lc_condiciones);
}

if (isset($_GET["imprimeDesmontadoCajero"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["ctrc_id"]);
    $lc_condiciones[2] = $lc_usuarioId;
    print $lc_config->fn_ejecutar("imprimeDesmontadoCajero", $lc_condiciones);
}

if (isset($_GET["cargarFormasPago"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["ctr_id"]);
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = 0;

    print $lc_config->fn_consultar("cargarFormasPago", $lc_condiciones);
}

if (isset($_GET["grabaarqueoAjuste"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["fmp_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["totalFormaPago"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["accion_int"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["codDepositoModificado"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["banderafp"]);
    $lc_condiciones[8] = htmlspecialchars($_GET["operadorAjuste"]);
    $lc_condiciones[9] = htmlspecialchars($_GET["ajusteNuevo"]);
    print $lc_config->fn_ejecutar("grabaarqueoAjuste", $lc_condiciones);
}

if (isset($_GET["eliminaFormasPagoAgregadas"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = htmlspecialchars($_GET["ctrc_id"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["banderafp"]);
    print $lc_config->fn_ejecutar("eliminaFormasPagoAgregadas", $lc_condiciones);
}

if (isset($_GET["traeValorFormaPago"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["ctrc_id"]);
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["fmp_id"]);

    print $lc_config->fn_consultar("traeValorFormaPago", $lc_condiciones);
}

if (isset($_GET["grabaarqueoformapago"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["fmp_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["totalFormaPago"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["accion_int"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["ctrc_id"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["banderafp"]);

    print $lc_config->fn_ejecutar("grabaarqueoformapago", $lc_condiciones);
}

if (isset($_GET["existeCuentaAbiertaMesa"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_usuarioId;
    print $lc_config->fn_consultar("existeCuentaAbiertaMesa", $lc_condiciones);
}

if (isset($_GET["existeCuentaAbierta"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_usuarioId;
    print $lc_config->fn_consultar("existeCuentaAbierta", $lc_condiciones);
}

if (isset($_GET["retirofondo"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["usr_id_cajero"]);

    //$lc_condiciones[2]=	$lc_est_id;

    print $lc_config->fn_consultar("retirofondo", $lc_condiciones);
}

if (isset($_GET["consultaCupones"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_ip;
    $lc_condiciones[3] = htmlspecialchars($_GET["usr_id_cajero"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["ctrc_id"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["prd_id"]);
    print $lc_config->fn_consultar("consultaCupones", $lc_condiciones);
}

if (isset($_GET["eliminaBilletes"])) {
    $lc_condiciones = htmlspecialchars($_GET["eliminaBillete"]);
    $arrayBill = count($lc_condiciones);
    for ($i = 0; $i < $arrayBill; $i++) {
        $lc_condiciones2[0] = htmlspecialchars($_GET["accion"]);
        $lc_condiciones2[1] = $lc_condiciones[$i];
        $lc_condiciones2[2] = 0;
        $lc_condiciones2[3] = htmlspecialchars($_GET["ctrc_id"]);
        print $lc_config->fn_ejecutar("eliminaBilletes", $lc_condiciones2);
    }
}

if (isset($_GET["DesmontadoDirecto"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["usr_id_cajero"]);
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = $lc_usuarioId;
    print $lc_config->fn_ejecutar("DesmontadoDirecto", $lc_condiciones);
}