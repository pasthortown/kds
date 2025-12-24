/*
 DESARROLLADO POR: JOSE FERNANDEZ
 DESCRIPCION: PANTALLA DE DEPOSITOS 
 TABLAS: BILLTE_ESTACION, DEPOSITOS, RETIROS
 FECHA CREACION: 18-03-2016
 */

/* global alertify, parseFloat */

var arrayBilletes2 = [];

array = 50;
var ocultos2 = new Array(array);
var ocultos = new Array(array);
var ocultos3 = new Array(array);//id de usuario
var resultado = new Array(array);
var denominaciones = new Array(array);
var multiplicaciones = new Array(array);
var cantidades = new Array(array);
var subtotales = new Array(array);//array q contiene los datos de la columna de totales
var lc_guardado = -1;
var valoresBilletesNuevos = [];
var lc_modificado = -1;
var contadorDeFormasDepago = 0;
var validadorFormasDePago = 0;
var banderaAjuste = -1;
var arrayDescripcionFormasPago = [];
var arrayDescripcionFormasPagoModificado = [];
var totalesIngresados = [];
var lc_grabaDirecto = -1;
var lc_chequeGraba = -1;
var bandera = -1;
banderaIdDias = -1;
banderaDias = -1;
var id_botones = [];
var simboloDeLaMoneda = "";
var numeroFormasPago = -1;
var banderaAceptaDeposito = -1;

$(document).ready(function () {
    $("#modal_ingresoNuevoDeposito").hide();
    fn_btn("agregar", 1);
    fn_cargarBotonesDias();
    banderaArqueoTarjeta = 0;
    fn_cargaComboViaDepositos();
    simboloDeLaMoneda = $("#hide_moneda").val();
    fn_cargaConceptosAjuste();
    $("#txt_monedas").numeric();
    $("#txt_monedasModifica").numeric();
    $("#txt_numReferencia").numeric();
    $("#txt_numReferenciaModifica").numeric();
    $("#txt_codigoReferencia").numeric();
    $("#txt_codigoReferenciaModifica").numeric();
});

function fn_btn(boton, estado) {
    if (estado) {
        $("#btn_" + boton).css("background", " url('../../imagenes/admin_resources/" + boton + ".png') 14px 4px no-repeat");
        $("#btn_" + boton).removeAttr("disabled");
        $("#btn_" + boton).addClass("botonhabilitado");
        $("#btn_" + boton).removeClass("botonbloqueado");
    } else {
        $("#btn_" + boton).css("background", " url('../../imagenes/admin_resources/" + boton + "_bloqueado.png') 14px 4px no-repeat");
        $("#btn_" + boton).prop("disabled", true);
        $("#btn_" + boton).addClass("botonbloqueado");
        $("#btn_" + boton).removeClass("botonhabilitado");
    }
}

function fn_accionar(accion) {
    if (accion == "Nuevo") {
        if ($("#hide_periodo").val() != "") {
            $("#total").val() == "";
            send = {"validaPeriodosAbiertos": 1};
            send.periodoI = $("#hide_periodo").val();
            $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
                if (datos.valida == 1) {
                    $("#txt_fechaDeEntrada").val("");
                    fn_ingresoNuevoDeposito();
                    $("#txt_depositoVia").val(0);
                } else {
                    alertify.error(datos.mensaje);
                    return false;
                }
            });
        } else {
            alertify.error("Seleccione el Per&iacute;odo en el cual quiere crear el dep&oacute;ito");
            return false;
        }
    }
}

function fn_actualizaValorMonedas() {
    if ($("#txt_monedas").val() == "") {
        $("#txt_monedas").val(0);
    }
    $("#hide_totalMonedasNuevo").val($("#txt_monedas").val());
    monedasValor = $("#hide_totalMonedasNuevo").val();
    if (isNaN(monedasValor)) {
        monedasValor = 0;
    }

    $("#tpieingresoNuevoDeposito tr:eq(0) td:eq(2) b:eq(0)").html(parseFloat(monedasValor).toFixed(2));
}

function fn_cargaComboViaDepositos() {
    Accion = "M";
    send = {"cargaComboDepositos": 1};
    send.accion = 4;
    $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
        if (datos.str > 0) {
            ///$("#txt_depositoVia").prop("disabled", false);
            $("#txt_depositoVia").html("");
            $("#txt_depositoVia").html("<option selected value='0'>---Seleccione Via---</option>");
            for (i = 0; i < datos.str; i++) {
                $("#txt_depositoVia").append("<option selected value=\"" + datos[i]['id'] + "\" >" + datos[i]['descripcion'] + "</option>");
            }
            $("#txt_depositoVia").val(0);

            $("#txt_depositoViaModifica").html("");
            //$('#txt_depositoViaModifica').html("<option selected value='0'>---Seleccione Via---</option>");
            for (i = 0; i < datos.str; i++) {
                $("#txt_depositoViaModifica").append("<option selected value=\"" + datos[i]['id'] + "\" >" + datos[i]['descripcion'] + "</option>");
            }
            //$("#txt_depositoViaModifica").val(0);
        }
    });
}

function fn_cargaConceptosAjuste() {
    send = {"cargaConceptosAjuste": 1};
    $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
        if (datos.str > 0) {
            ///$("#txt_depositoVia").prop("disabled", false);
            $("#sel_Ajuste").html("");
            $("#sel_Ajuste").html("<option selected value='0'>---Seleccione Concepto---</option>");
            for (i = 0; i < datos.str; i++) {
                $("#sel_Ajuste").append("<option selected value=\"" + datos[i]['id'] + "\" >" + datos[i]['concepto'] + "</option>");
            }
            $("#sel_Ajuste").val(0);
        }
    });
}

function fn_ingresoNuevoDeposito() {
    send = {"consultaExisteArqueo": 1};
    send.accion = 5;
    send.periodoC = $("#hide_periodo").val();
    send.depositoC = "0";
    $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
        if (datos.existe == 1) {
            var d = new Date();
            $("#txt_fechaDeEntrada").val(d.getDate() + "/" + (d.getMonth() + 1) + "/" + d.getFullYear(), ", " + d.getHours(), ":" + d.getMinutes(), ":" + d.getSeconds());
            $("#titulomodalingresoNuevoDeposito").html("Dep&oacute;sitos: Nuevo Ingreso");
            $("#modal_ingresoNuevoDeposito").modal("show");
            lc_guardado = -1;
            $("#hid_controlEfectivo").val(0);

            $("#hide_totalBilletes").val("");
            send = {"consultaformaPago": 1};
            send.accion = 1;
            lc_chequeGraba = 1;
            send.prd_id = $("#hide_periodo").val();
            $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                success: function (datos) {
                    var html = "<thead><tr class='active'><th width='130px' style='text-align:center'>Formas de Pago</th><th style='text-align:center' width='130px'>Monto Actual</th><!-- <th style='text-align:center' width='130px'>POS Calculado</th>--> </thead>";
                    if (datos.str > 0) {
                        numeroFormasPago = datos.str;
                        contadorDeFormasDepago = datos.str;//para verificar que se ingresen tdos los valores a depositar.
                        //arrayDescripcionFormasPago.length=datos.str;
                        arrayDescripcionFormasPago.length = 0;
                        for (i = 0; i < datos.str; i++) {
                            arrayDescripcionFormasPago.push(datos[i]["fmp_descripcion"]);
                            html += "<tr><td width='180' align='center' style='vertical-align:middle'>" + datos[i]['fmp_descripcion'] + "</td>";
                            if (datos[i]["fmp_descripcion"] == "EFECTIVO") {
                                html += "<td align='center'><input id='btnT" + datos[i]['fmp_descripcion'].replace(/\s/g, "") + "' type='button'  style='width:130px;' class='btn btn-primary' value='Presione Aqui' onclick='fn_validaModal(\"" + datos[i]['fmp_id'] + "\",\"" + datos[i]['fmp_descripcion'] + "\"," + datos[i]['montoActual'] + ",\"" + datos[i]['IDPeriodo'] + "\")'></td>";
                                idformaPago = datos[i]["fmp_id"];
                            } else {
                                html += "<td align='center'><input id='btnT" + datos[i]['fmp_descripcion'].replace(/\s/g, "") + "' style='width:130px;' type='button' value='Presione Aqui' class='btn btn-primary'	onclick='fn_validaModal(\"" + datos[i]['fmp_id'] + "\",\"" + datos[i]['fmp_descripcion'] + "\"," + datos[i]['montoActual'] + ",\"" + datos[i]['IDPeriodo'] + "\")'></td>";
                            }
                            /*html+="<td><input readonly='readonly' class='form-control' style='text-align:center; type='text' type='text' id='"+datos[i]['fmp_descripcion']+"' value='$"+datos[i]['montoCalculado'].toFixed(2)+"'></td>";*/

                            $("#tabla_ingresoNuevoDeposito").html(html);
                            id_botones[i] = "btnT" + datos[i]["fmp_descripcion"].replace(/\s/g, "") + "";
                        }
                        fn_cargaTotalesDepositoNuevo("0", $("#hide_periodo").val(), 2);//se envia cero xq aun no hay id de deposito
                    }
                }
            });
        } else {
            alertify.error(datos.mensaje);
            return false;
        }
    });
}

function fn_validaModal(idFmp, descripcionFmp, montoActual, periodoID) {
    if (lc_guardado == -1) { //-1 significa que es un nuevo ingreso
        if ($("#txt_numReferencia").val() == "") {
            alertify.error("Ingrese n&uacute;mero de referencia.");
            return false;
        }
        if ($("#txt_fechaDeEntrada").val() == "") {
            alertify.error("Seleccione fecha de dep&oacute;sito");
            return false;
        }
        if ($("#txt_depositoVia").val() == 0) {
            alertify.error("Seleccione via de dep&oacute;sito");
            return false;
        }
        if ($("#txt_codigoReferencia").val() == "") {
            alertify.error("Ingrese c&oacute;digo de referencia.");
            return false;
        }
        if ($("#txt_monedas").val() == "") {
            $("#txt_monedas").val(0);
        }
        if ($("#txt_numReferencia").val() == "") {
            alertify.error("Ingrese n&uacute;mero de referencia.");
            return false;
        }
        if ($("#txt_fechaDeEntrada").val() == "") {
            alertify.error("Seleccione fecha de dep&oacute;sito");
            return false;
        }
        if ($("#txt_depositoVia").val() == 0) {
            alertify.error("Seleccione via de dep&oacute;sito");
            return false;
        }
        if ($("#txt_codigoReferencia").val() == "") {
            alertify.error("Ingrese c&oacute;digo de referencia.");
            return false;
        }
        if ($("#txt_monedas").val() == "") {
            $("#txt_monedas").val(0);
        }
        if (isNaN($("#txt_monedas").val())) {
            alertify.error("Ingrese un n&uacute;mero v&aacute;lido.");
            $("#txt_monedas").focus();
            return false;
        }

        send = {"insertaNuevoDeposito": 1};
        send.accion = 1;
        send.periodoNuevo = periodoID;//$("#hide_periodo").val();						
        send.referenciaNuevo = $("#txt_numReferencia").val();
        send.papeletaNuevo = $("#txt_codigoReferencia").val();
        send.fechaDepositoNuevo = $("#txt_fechaDeEntrada").val();
        send.monedasNuevo = $("#txt_monedas").val();
        send.depositoViaNUevo = $("#txt_depositoVia").val();
        send.comentarioNuevo = $("#txt_AreaNuevo").val();
        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
            success: function (datos) {
                banderaAceptaDeposito = -1;
                if (datos.str > 0) {
                    lc_guardado = 1;//significa que ya se guardo y se obtuvo el id del deposito
                    $("#hide_codigoDeposito").val(datos.idDepositos);
                    if (descripcionFmp == "EFECTIVO") {
                        fn_modalBilletes(idFmp, periodoID);
                    } else if (descripcionFmp == "CHEQUES") {
                        lc_chequeGraba = -1;
                        fn_modalTarjetas(idFmp, periodoID);
                    }
                } else {
                    alertify.error("No existen valores de retiros para realizar el deporsito!");
                }
            }
        });
    } else { //ya inserto cabecera de deposito, solo trae los billetes
        if (descripcionFmp === "EFECTIVO") {
            fn_modalBilletes(idFmp, periodoID);
        } else if (descripcionFmp === "CHEQUE") {
            fn_modalTarjetas(idFmp, periodoID);
        }
    }
}

/*funcion que carga los totales de un nuevo ingreso de deposito*/
function fn_cargaTotalesDepositoNuevo(deposito, periodo, accion) {
    simbMo = $("#hide_moneda").val();
    send = {"cargaTotalesDepositoNuevo": 1};
    send.accion = accion;
    send.deposiN = deposito;
    send.periN = periodo;
    $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
        totalMonedasNuevas = parseFloat($("#hide_totalMonedasNuevo").val());
        if (isNaN(totalMonedasNuevas)) {
            totalMonedasNuevas = 0;
        }

        if (lc_guardado == -1) {
            //$("#tpieingresoNuevoDeposito tr:eq(0) td:eq(1)").html((0).toFixed(2));
            descripcionTotales = "";
            for (i = 0; i < arrayDescripcionFormasPago.length; i++) {
                descripcionTotales = descripcionTotales + arrayDescripcionFormasPago[i] + "" + " + ";
            }
            descripcionTotales = descripcionTotales + " MONEDAS: ==>";
            $("#tpieingresoNuevoDeposito tr:eq(0) td:eq(1)").html(descripcionTotales);
            $("#tpieingresoNuevoDeposito tr:eq(0) td:eq(2) b:eq(0)").html(simbMo + " " + totalMonedasNuevas.toFixed(2));
        } else {
            $("#tpieingresoNuevoDeposito tr:eq(0) td:eq(2) b:eq(0)").html(simbMo + " " + (datos.totalDeposito + totalMonedasNuevas).toFixed(2));
        }
    });
}

/*Totales cuando se modifica*/
function fn_cargaTotalesDepositoAModificar(deposito, periodo) {
    send = {"cargaTotalesDepositoAModificar": 1};
    send.accion = 1;
    send.deposiT = deposito;
    send.periT = periodo;
    $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
        valorMo = parseFloat($("#txt_monedasModifica").val());
        if (isNaN(valorMo)) {
            valorMo = 0;
        }

        //$("#tpieingresoModificaDeposito tr:eq(0) td:eq(1)").html((datos.totalDeposito+valorMo).toFixed(2));
        descripcionTotalesM = "";
        for (i = 0; i < arrayDescripcionFormasPagoModificado.length; i++) {
            descripcionTotalesM = descripcionTotalesM + arrayDescripcionFormasPagoModificado[i] + "" + " + ";
        }
        descripcionTotalesM = descripcionTotalesM + " MONEDAS: ==>";
        $("#tpieingresoModificaDeposito tr:eq(0) td:eq(1)").html(descripcionTotalesM);
        $("#tpieingresoModificaDeposito tr:eq(0) td:eq(2) b:eq(0)").html(simboloDeLaMoneda + " " + (datos.totalDeposito + valorMo).toFixed(2));
    });
}

/*funcion que carga los depositos para modificar*/
function fn_cargaDepositoModifica(deposito, periodo) {
    //carga los datos de cabecera
    $("#hide_codigoDepositoModificado").val(deposito);
    $("#hide_codigoPeriodoModificado").val(periodo);
    send = {"cargaCabeceraDeposito": 1};
    send.depositoId = deposito;
    send.prd_idModifica = periodo;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            $("#txt_numReferenciaModifica").val(datos.numeroDeReferencia);
            $("#txt_fechaDeEntradaModifica").val(datos.fechaDeDeposito);
            $("#txt_depositoViaModifica").val(datos.viaDeposito);
            $("#txt_registradoPorModifica").val(datos.usuario);
            $("#txt_codigoReferenciaModifica").val(datos.numeroDePapeleta);
            $("#txt_monedasModifica").val(datos.monedas);
            $("#txt_AreaModifica").val(datos.comentario);
            //$("#txt_depositoViaModifica").val(datos.comentario);				
        }
    });

    $("#titulomodalingresoModificaDeposito").html("Dep&oacute;sitos");
    $("#modal_ingresoModificaDeposito").modal("show");
    lc_modificado = -1;
    $("#hid_controlEfectivo").val(0);

    $("#hide_totalBilletes").val("");
    send = {"consultaDetalleDepositoModificado": 1};
    send.accion = 2;
    send.prd_idM = periodo;
    send.depositoM = deposito;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            var html = "<thead><tr class='active'><th width='130px' style='text-align:center'>Formas de Pago</th><th style='text-align:center' width='130px'>Monto Actual</th></thead>";
            if (datos.str > 0) {
                arrayDescripcionFormasPagoModificado.length = 0;
                numeroFormasPago = datos.str;
                for (i = 0; i < datos.str; i++) {
                    arrayDescripcionFormasPagoModificado.push(datos[i]["fmp_descripcion"]);
                    html += "<tr><td width='180' align='center' style='vertical-align:middle'>" + datos[i]['fmp_descripcion'] + "</td>";
                    if (datos[i]["fmp_descripcion"].match(/^AJUSTE.*$/)) {
                        html += "<td align='center'><input id='btnT" + datos[i]['fmp_descripcion'].replace(/\s/g, "") + "' type='button'  style='width:130px;' class='btn btn-primary' value='" + simboloDeLaMoneda + " " + datos[i]['montoActual'].toFixed(2) + "' onclick='fn_cargaAjusteModificar()'></td>";
                        $("#btn_agregarAjuste").hide();
                        banderaAjuste = 2;
                    } else {
                        html += "<td align='center'><input id='btnT" + datos[i]['fmp_descripcion'].replace(/\s/g, "") + "' type='button'  style='width:130px;' class='btn btn-primary' value='" + simboloDeLaMoneda + " " + datos[i]['montoActual'].toFixed(2) + "'></td>";
                        $("#btn_agregarAjuste").show();
                    }
                    idformaPago = datos[i]["fmp_id"];

                    $("#tabla_ingresoModificaDeposito").html(html);
                }
                fn_cargaTotalesDepositoAModificar(deposito, periodo);
            }
        }
    });
}

function fn_asientaDepositoModificado() {
    if ($("#txt_numReferenciaModifica").val() == "") {
        alertify.error("Ingrese n&uacute;mero de referencia.");
        return false;
    }
    if ($("#txt_depositoViaModifica").val() == 0) {
        alertify.error("Seleccione via de dep&oacute;sito");
        return false;
    }
    if ($("#txt_codigoReferenciaModifica").val() == "") {
        alertify.error("Ingrese c&oacute;digo de referencia.");
        return false;
    }
    if ($("#txt_monedasModifica").val() == "") {
        $("#txt_monedas").val(0);
    }
    if (isNaN($("#txt_monedasModifica").val())) {
        alertify.error("Ingrese un n&uacute;mero v&aacute;lido.");
        $("#txt_monedas").focus();
        return false;
    }

    $("#hide_codigoDepositoModificado").val();
    $("#hide_codigoPeriodoModificado").val();
    send = {"asientaDepositoModificado": 1};
    send.opcion = "U";
    send.accion = "1";
    send.periodoAs = $("#hide_codigoPeriodoModificado").val();
    send.depositoAs = $("#hide_codigoDepositoModificado").val();
    send.referenciaM = $("#txt_numReferenciaModifica").val();
    send.paleletaM = $("#txt_codigoReferenciaModifica").val();
    send.monedasM = $("#txt_monedasModifica").val();
    send.depositoViaM = $("#txt_depositoViaModifica").val();
    //send.comentarioAsienta=$("#txt_AreaNuevo").val();
    $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
        $("#modal_ingresoModificaDeposito").modal("hide");
        fn_muestraUsuariosEstado("", $("#hide_codigoPeriodoModificado").val());
    });
}

function fn_cargaAjusteModificar() {
    send = {"cargaAjusteModificar": 1};
    send.accion = 3;
    send.perioddo = $("#hide_codigoPeriodoModificado").val();
    send.depositoo = $("#hide_codigoDepositoModificado").val();
    $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
        $("#modal_ingresoModificaDeposito").modal("hide");
        $("#modal_agregarAjuste").modal("show");
        $("#txtAjuste").val(datos.montoActual);
        $("#sel_Ajuste").val(datos.concepto);
        if (datos.signo == "+") {
            $("#opt_Mas").addClass("active");
            $("#opt_Menos").removeClass("active");
            $("#rd_mas").prop("checked", true);
            $("#rd_menos").prop("checked", false);
        }
        if (datos.signo == "-") {
            $("#opt_Mas").removeClass("active");
            $("#opt_Menos").addClass("active");
            $("#rd_mas").prop("checked", false);
            $("#rd_menos").prop("checked", true);
        }
    });
}

function fn_cargarBotonesDias() {
    send = {"cargarBotonesDias": 1};
    send.accion = 1;
    $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
        if (datos.str > 0) {
            $("#tabla_usuarios").empty();
            html = "<tr class=''>";
            html += "<td style = 'width:170px; text-align:center; border: hidden'></td>";
            for (i = 0; i < datos.str; i++) {
                if (i == 0) {
                    banderaIdDias = datos[i]["id_dias"];
                    banderaDias = datos[i]["dias"];

                }
                html += "<td align='center' style=' border: hidden'><button id=" + datos[i]['id_dias'] + " onclick='fn_muestraFechas(\"" + datos[i]['id_dias'] + "\", " + datos[i]['dias'] + ");' class='btn btn-default btn-lg'>" + datos[i]['dias'] + "</button></td>";
            }
            html += "</tr>";

            $("#tabla_usuarios").append(html);
            fn_muestraFechas(banderaIdDias, banderaDias);
        }
    });
}

function fn_muestraFechas(id_boton_dia, dias) {
    send = {"cargarfechas": 1};
    send.accion = 1;
    send.dias = dias;
    $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
        if (datos.str > 0) {
            $("#tabla_fechas").empty();

            for (i = 0; i < datos.str; i++) {
                html = "<tr>";
                html += "<td align='' style='width:170px; height:50px; text-align:justify;'><button id='b" + datos[i]['prd_id'] + "' value= '" + datos[i]['prd_fechaapertura'] + "' onclick='fn_muestraUsuariosEstado(" + datos[i]['prd_fechaapertura'] + ",\"" + datos[i]['prd_id'] + "\");' style='width:150px;' class='btn btn-default btn-lg btn-primary'>" + datos[i]['prd_fechaapertura'] + "</button></td></tr>";
                $("#tabla_fechas").append(html);
                $("#hide_fecha").val(datos[i]["prd_fechaapertura"]);
                $("#t" + datos[i]["prd_id"]).show();
            }
        } else {
            alertify.error("No existen periodos para mostrar.");
        }
    });
}

function fn_muestraUsuariosEstado(fechaAperturaPeriodo, prd_id) {
    fecha = $("#b" + prd_id + "").val();
    moneda = $("#hide_moneda").val();
    $("#hide_fecha").val("" + fecha + "");
    $("#hide_periodo").val(prd_id);
    id = $("#elimina").val();
    if ($("#elimina").val() != "") {
        $("#t" + id).empty();
    }

    send = {"cargaDepositos": 1};
    send.prd_id = prd_id;
    $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
        if (datos.str > 0) {
            $("#tabla_estado_usuarios").empty();

            html = "<tr>";
            for (i = 0; i < datos.str; i++) {
                var nFilas = $("#tabla_estado_usuarios tr").length;
                var nColumnas = $("#tabla_estado_usuarios tr:last td").length;

                if (nColumnas < 9) {
                    //$("#tabla_estado_usuarios").append('</br>');

                    html += "<td align='center'>";
                    html += "<div class=''><div class=''><h6>Ref: " + datos[i]['numeroDeReferencia'] + "<br/>Cod: " + datos[i]['numeroDePapeleta'] + "</h6></div><div class=''><img src='../../imagenes/moneyBag.png'/></div>";
                    html += "<div class='btn-group'>";
                    /*jf*/
                    html += "<div class='row' style='width:125px'><button id='' value= 'Ref: " + datos[i]['numeroDeReferencia'] + "' onclick='fn_cargaDepositoModifica(\"" + datos[i]['IDDepositos'] + "\", \"" + datos[i]['IDPeriodo'] + "\")' style='width:90px;' class='btn btn-success'>" + moneda + " " + datos[i]['arc_valor'].toFixed(2) + "</button></div>";
                    html += "</div></td>";
                    $("#tabla_estado_usuarios").html(html);
                } else {
                    html += "</tr>";
                    html += "<td align='center'>";
                    html += "<div class=''><div class=''><h6>" + datos[i]['usr_usuario'] + "</h6><img src='../../imagenes/admin_resources/icon-user_activo.png'/></div>";
                    html += "<div class='btn-group'>";
                    if (datos[i]["estado_usuario"] == "Inactivo") {
                        html += "<div class='row' style='width:125px'><button id='' value= '" + datos[i]['estado_usuario'] + "' onclick='fn_formasPagoInactivo(\"" + datos[i]['usr_id'] + "\",\"" + datos[i]['ctrc_id'] + "\", \"" + datos[i]['usr_usuario'] + "\",\"" + prd_id + "\")' style='width:90px;' class='btn btn-info'>" + datos[i]['estado_usuario'] + "</button></div>";
                    } else {
                        if (datos[i]["estado_usuario"] == "Activo") {
                            html += "<div class='row' style='width:125px'><button id='' value= '" + datos[i]['estado_usuario'] + "' onclick='fn_formasPagoActivo(\"" + datos[i]['usr_id'] + "\", \"" + datos[i]['ctrc_id'] + "\", \"" + datos[i]['usr_usuario'] + "\",\"" + datos[i]['ctrc_usuario_desmontarcaja'] + "\",\"" + datos[i]['est_id'] + "\",\"" + prd_id + "\")' style='width:90px;' class='btn btn-success'>" + datos[i]['estado_usuario'] + "</button></div>";
                        } else {
                            if (datos[i]["estado_usuario"] == "Por Cerrar") {
                                html += "<div class='row' style='width:125px'><button id='' value= '" + datos[i]['estado_usuario'] + "' onclick='fn_formasPagoPorCerrar(\"" + datos[i]['usr_id'] + "\",\"" + datos[i]['ctrc_usuario_desmontarcaja'] + "\", \"" + datos[i]['est_id'] + "\", \"" + datos[i]['ctrc_id'] + "\", \"" + datos[i]['usr_usuario'] + "\", \"" + prd_id + "\")' style='width:90px;' class='btn btn-danger'>" + datos[i]['estado_usuario'] + "</button></div>";
                            }
                        }
                    }
                    html += "</div></td>";
                    $("#tabla_estado_usuarios").html(html);
                }
            }
            html += "</tr>";
        } else {
            $("#tabla_estado_usuarios").empty();
            alertify.error("No existen dep&oacute;sitos para el periodo seleccionado.");
        }
    });
}

function fn_formasPagoInactivo(usr_id, ctrc_id, usr_usuario, prd_id) {
    fechai = $("#hide_fecha").val();
    $("#ModalFormasPagoInactivo").modal("show");

    $("#titulomodalNuevo").html("Desmontado de Cajero: <span class='glyphicon glyphicon-user' aria-hidden='true'></span>" + usr_usuario + "");

    send = {"formasPagoInactivo": 1};
    send.accion = 1;
    send.fechai = fechai;
    send.usr_id = usr_id;
    send.ctrc_id = ctrc_id;

    $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
        html = "<tr class='bg-primary'>";
        html += "<td style='text-align:center; width:;'>Formas de Pago</td>";
        html += "<td style='text-align:center; width:;'>Retiro Efectivo</td>";
        html += "<td style='text-align:center; width:;'>Transacciones</td>";
        html += "<td style='text-align:center; width:;'>Monto Actual</td>";
        html += "<td style='text-align:center; width:;'>POS Calculado</td>";
        html += "<td style='text-align:center; width:;'>Mas &oacute; Menos</td>";
        html += "</tr>";
        $("#formaPago").empty();
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += "<tr id='" + i + "'>";
                html += "<td style='text-align:center;'>" + datos[i]['fmp_descripcion'] + "</td>";
                if (datos[i]["retiro_efectivo"] != "-") {
                    html += "<td style='text-align:center;'>" + parseFloat(datos[i]['retiro_efectivo']).toFixed(2) + "";
                } else {
                    html += "<td style='text-align:center;'>" + datos[i]["retiro_efectivo"] + "";
                }
                html += "</td>";
                html += "<td style='text-align:center;'>" + datos[i]["Transacciones"] + "</td>";
                html += "<td style='text-align:center;'>" + parseFloat(datos[i]["arc_valor"]).toFixed(2) + "</td>";
                html += "<td style='text-align:center;'>" + parseFloat(datos[i]["fpf_total_pagar"]).toFixed(2) + "</td>";
                html += "<td style='text-align:center;'>" + parseFloat(datos[i]["arc_valor"] - datos[i]["fpf_total_pagar"]).toFixed(2) + "</td></tr>";

                $("#formaPago").html(html);
            }

            //---------------------------------------CUPONES------------------------------------------------------//
            $("#hide_num_cupones").val(0);

            send = {"consultaCupones": 1};
            send.accion = 3;
            //idformaPago = 0;
            send.usr_id_cajero = usr_id;
            send.ctrc_id = ctrc_id;
            send.prd_id = prd_id;

            $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                success: function (datos) {
                    if (datos.str > 0) {
                        for (j = 0; j < datos.str; j++) {
                            html += "<tr><td width='180' align='center' style='vertical-align:middle'>" + datos[j]["fmp_descripcion"] + "</td>";
                            html += "<td style='text-align:center;'>-</td>";
                            html += "<td style='text-align:center;'>" + datos[j]["Transacciones"] + "</td>";
                            html += "<td style='text-align:center;'>-</td>";
                            html += "<td style='text-align:center;'>-</td>";
                            html += "<td style='text-align:center;'>-</td></tr>";

                            $("#hide_num_cupones").val(datos[j]["Transacciones"]);
                        }
                        $("#formaPago").html(html);
                    }
                }
            });
            //-----------------------------------------------------------------------------------------------------//

            send = {"totalesPagoInactivo": 1};
            send.accion = 2;
            send.fechai = fechai;
            send.usr_id = usr_id;
            send.ctrc_id = ctrc_id;

            num_cupones = $("#hide_num_cupones").val();

            $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
                if (datos.str > 0) {
                    for (i = 0; i < datos.str; i++) {
                        html += "<tr id='" + i + "' class='success'>";
                        html += "<th style='text-align:center;'>TOTALES:</th>";
                        html += "<th style='text-align:center;'>$ " + parseFloat(datos[i]["retiro_efectivo"]).toFixed(2) + "</th>";
                        html += "<th style='text-align:center;'>" + (parseInt(datos[i]["arc_numero_transacciones"]) + parseInt(num_cupones)) + "</th>";
                        html += "<th style='text-align:center;'>$ " + parseFloat(datos[i]["arc_valor"]).toFixed(2) + "</th>";
                        html += "<th style='text-align:center;'>$ " + parseFloat(datos[i]["fpf_total_pagar"]).toFixed(2) + "</th>";
                        html += "<th style='text-align:center;'>$ " + parseFloat(datos[i]["diferencia"]).toFixed(2) + "</th></tr>";
                        $("#formaPago").html(html);
                    }
                }
            });
        } else {
            //alertify.error("No existen datos para esta cadena.");
            html += "<tr>";
            html += "<td colspan='6'>No existen datos para el usuario.</td></tr>";
            $("#formaPago").html(html);
        }
    });
}

function fn_formasPagoActivo(usr_id, ctrc_id, usr_usuario, usr_id_admin, est_id, prd_id) {
    //banderaModalActivo = 1;
    fechai = $("#hide_fecha").val();
    $("#ModalFormasPagoActivo").modal("show");
    $("#titulomodalNuevoActivo").html("Activo: <span class='glyphicon glyphicon-user modal-title' aria-hidden='true'></span> " + usr_usuario + "");

    send = {"formasPagoActivo": 1};
    send.accion = 3;
    send.fechai = fechai;
    send.usr_id = usr_id;
    send.ctrc_id = ctrc_id;

    $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
        html = "<tr class='bg-primary'>";
        html += "<td style='text-align:center; width:;'>Formas de Pago</td>";
        html += "<td style='text-align:center; width:;'>Retiro Efectivo</td>";
        html += "<td style='text-align:center; width:;'>Transacciones</td>";
        html += "<td style='text-align:center; width:;'>POS Calculado</td>";
        html += "</tr>";
        $("#formaPagoActivo").empty();
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += "<tr id='" + i + "'>";
                html += "<td style='text-align:center;'>" + datos[i]["fmp_descripcion"] + "</td>";
                if (datos[i]["retiro_efectivo"] != "-") {
                    html += "<td style='text-align:center;'>" + parseFloat(datos[i]["retiro_efectivo"]).toFixed(2) + "</td>";
                } else {
                    html += "<td style='text-align:center;'>" + datos[i]["retiro_efectivo"] + "</td>";
                }
                html += "<td style='text-align:center;'>" + datos[i]["numero_transacciones"] + "</td>";
                html += "<td style='text-align:center;'>" + parseFloat(datos[i]["cfac_total"]).toFixed(2) + "</td></tr>";

                $("#formaPagoActivo").html(html);
            }

            //---------------------------------------CUPONES------------------------------------------------------//
            $("#hide_num_cupones").val(0);

            send = {"consultaCupones": 1};
            send.accion = 3;
            //idformaPago = 0;
            send.usr_id_cajero = usr_id;
            send.ctrc_id = ctrc_id;
            send.prd_id = prd_id;

            $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                success: function (datos) {
                    if (datos.str > 0) {
                        for (j = 0; j < datos.str; j++) {
                            html += "<tr><td width='180' align='center' style='vertical-align:middle'>" + datos[j]["fmp_descripcion"] + "</td>";
                            html += "<td style='text-align:center;'>-</td>";
                            html += "<td style='text-align:center;'>" + datos[j]["Transacciones"] + "</td>";
                            html += "<td style='text-align:center;'>-</td></tr>";
                            //html+="<td style='text-align:center;'>-</td>";
                            //html+="<td style='text-align:center;'>-</td></tr>";

                            $("#hide_num_cupones").val(datos[j]["Transacciones"]);
                        }
                        $("#formaPagoActivo").html(html);
                    }
                }
            });
            //-----------------------------------------------------------------------------------------------------//

            num_cupones = $("#hide_num_cupones").val();

            send = {"totalesPagoActivo": 1};
            send.accion = 4;
            send.fechai = fechai;
            send.usr_id = usr_id;
            send.ctrc_id = ctrc_id;
            $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
                if (datos.str > 0) {
                    for (i = 0; i < datos.str; i++) {
                        html += "<tr id='" + i + "' class='success'>";
                        html += "<th style='text-align:center;'>TOTALES:</th>";
                        html += "<th style='text-align:center;'>$ " + parseFloat(datos[i]["retiro_efectivo"]).toFixed(2) + "</th>";
                        html += "<th style='text-align:center;'>" + (parseInt(datos[i]["numero_transacciones"]) + parseInt(num_cupones)) + "</th>";
                        html += "<th style='text-align:center;'>$ " + parseFloat(datos[i]["cfac_total"]).toFixed(2) + "</th></tr>";
                        $("#formaPagoActivo").html(html);
                    }
                }
            });
        } else {
            //alertify.error("No existen datos para esta cadena.");
            html += "<tr>";
            html += "<td colspan='6'>No existen datos para el usuario.</td></tr>";
            $("#formaPagoActivo").html(html);
        }
    });

    html = "<div class='col-xs-2'><button type='button' id='btn_agregarFormaPago' class='btn btn-danger' onclick='fn_desmontadoDirecto(\"" + usr_id + "\",\"" + usr_id_admin + "\", \"" + est_id + "\", \"" + ctrc_id + "\", \"" + usr_usuario + "\",\"" + prd_id + "\")' >Desasignar Cajero</button></div>";
    html += "<div class='col-md-offset-9'><button type='button' onclick='fn_cerraModal();' class='btn btn-primary' data-dismiss='modal'>Aceptar</button><button type='button' onclick='fn_cerraModal();' class='btn btn-default' data-dismiss='modal'>Cancelar</button></div>";
    $("#pie_formaPagoActivo").html(html);
}

function fn_cerraModal() {
    banderaModalActivo = 0;
}

//DESMONTADO DIRECTO DE CAJERO SIN CUADRE DE VENTAS//
function fn_desmontadoDirecto(usr_id_cajero, usr_id_admin, est_id, ctrc_id, usr_usuario, prd_id) {
    send = {"existeCuentaAbiertaMesa": 1};
    send.accion = 2;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                if (datos.cuentaAbiertaMesa == 0) {
                    send = {"existeCuentaAbierta": 1};
                    send.accion = 1;

                    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                        success: function (datos) {
                            if (datos.str > 0) {
                                if (datos.cuentaAbierta == 1) {
                                    nombreUsr = $("#hid_usuarioDescripcion").val();
                                    alertify.alert("No puede desasignar el cajero porque el usuario tiene cuentas abiertas");
                                    $("#alertify-ok").click(function () {
                                        return false;
                                    });
                                } else {
                                    send = {"retirofondo": 1};
                                    send.accion = 1;
                                    send.usr_id_cajero = usr_id_cajero;

                                    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                                        success: function (datos) {
                                            if (datos.str > 0) {
                                                if (datos.retirofondo == 1) {
                                                    alertify.confirm("Esta seguro que desea Desasignar el Cajero??");
                                                    $("#alertify-ok").click(function () {
                                                        send = {"DesmontadoDirecto": 1};
                                                        send.accion = "U";
                                                        send.usr_id_cajero = usr_id_cajero;
                                                        send.usr_id_admin = usr_id_admin;
                                                        //usr_id_cajero, usr_id_admin, est_id, ctrc_id, usr_usuario, prd_id
                                                        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                                                            success: function (datos) {
                                                                alertify.alert("Desmontado exitoso");
                                                                $("#alertify-ok").click(function () {
                                                                    $("#ModalFormasPagoActivo").modal("hide");
                                                                    fecha_periodo = $("#hide_fecha").val();
                                                                    periodo = $("#hide_periodo").val();
                                                                    fn_muestraUsuariosEstado(fecha_periodo, periodo);
                                                                    /*bandera = $("#bandera").val();
                                                                     
                                                                     if (bandera == "Inicio"){
                                                                     window.location.href="../index.php";
                                                                     }else {
                                                                     window.location.href="corteCaja.php";
                                                                     };*/
                                                                });
                                                            }
                                                        });
                                                    });
                                                } else {
                                                    alertify.alert("No puede desasignar el cajero porque el Administrador no ha retirado el fondo");
                                                    return false;
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    });
                } else {
                    nombreUsr = $("#hid_usuarioDescripcion").val();
                    alertify.alert("No puede desasignar el cajero porque el usuario tiene mesas abiertas");
                    $("#alertify-ok").click(function () {
                        return false;
                    });
                }
            }
        }
    });
}

function fn_formasPagoPorCerrar(usr_id_cajero, usr_id_admin, est_id, ctrc_id, usr_usuario, prd_id) {
    $("#titulomodalformapagoPorCerrar").html("Corte en Z - Desasignar Cajero: <span class='glyphicon glyphicon-user' aria-hidden='true'></span>" + usr_usuario + "");

    $("#hide_totalBilletes").val("");
    $("#hid_usr_id_cajero").val(usr_id_cajero);
    $("#hid_controlEstacion").val(ctrc_id);
    send = {"consultaformaPago": 1};
    send.accion = 1;
    idformaPago = 0;
    send.usr_id_cajero = usr_id_cajero;
    send.ctrc_id = ctrc_id;
    send.prd_id = prd_id;

    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            var html = "<thead><tr class='active'><th width='130px' style='text-align:center'>Formas de Pago</th><th width='130px' style='text-align:center'>Retirado</th><th width='130px' style='text-align:center'>Transacciones</th><th style='text-align:center' width='130px'>Monto Actual</th><th style='text-align:center' width='130px'>POS Calculado</th><th style='text-align:center' width='130px'>Mas o Menos</th></thead>";
            if (datos.str > 0) {
                numeroFormasPago = datos.str;
                for (i = 0; i < datos.str; i++) {
                    //arrayDescripcionFormasPago.push(datos[i]['fmp_descripcion']);
                    //console.log("hola"+arrayDescripcionFormasPago);
                    html += "<tr><td width='180' align='center' style='vertical-align:middle'>" + datos[i]["fmp_descripcion"] + "</td>";

                    if (datos[i]["fmp_descripcion"] == "EFECTIVO") {
                        //CONSULTA RETIRO EN EFECTIVO//-------------------------------------------------------------------------
                        send = {"consultaValorRetiroEfectivo": 1};
                        send.accion = 1;
                        send.usr_id_cajero = usr_id_cajero;
                        send.ctrc_id = ctrc_id;

                        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                            success: function (datos) {
                                if (datos.str > 0) {
                                    var valor_total_efectivo = datos.ValorTotalEfectivo;
                                    html += "<td><input id='' readonly='readonly' style='text-align:center;' class='form-control' value='$" + valor_total_efectivo.toFixed(2) + "'></td>";

                                    $("#tpie tr:eq(0) td:eq(1)").html("$" + valor_total_efectivo.toFixed(2) + "");
                                    $("#valorEfectivoTotal").val(valor_total_efectivo);
                                    $("#retiroEfectivoModalBilletes").val("$" + valor_total_efectivo.toFixed(2) + "");
                                    $("#tpie3 tr:eq(2) td:eq(1)").html("");
                                    $("#tpie3 tr:eq(2) td:eq(1)").html(valor_total_efectivo.toFixed(2));
                                }
                            }
                        });
                        //--------------------------------------------------------------------------------------------------------
                    } else {
                        html += "<td></td>";
                    }

                    html += "<td style='text-align:center;'><input readonly='readonly' style='text-align:center;' class='form-control' value=" + datos[i]["Transacciones"] + "></td>";//
                    if (datos[i]["fmp_descripcion"] == "EFECTIVO") {
                        html += "<td><input id='btnT" + datos[i]['fmp_descripcion'].replace(/\s/g, "") + "' type='button'  style='width:130px;' class='btn btn-primary' value='Presione Aqui' onclick='fn_modalBilletes(\"" + datos[i]['fmp_id'] + "\",\"" + datos[i]['ctrc_id'] + "\",\"" + datos[i]['usr_id'] + "\")'></td>";
                        idformaPago = datos[i]["fmp_id"];

                    } else {
                        html += "<td><input id='btnT" + datos[i]['fmp_descripcion'].replace(/\s/g, "") + "' style='width:130px;' type='button' value='Presione Aqui' class='btn btn-primary'	onclick='fn_modalTarjetas(\"" + datos[i]['fmp_id'] + "\",\"" + datos[i]['ctrc_id'] + "\",\"" + datos[i]['usr_id'] + "\")'></td>";
                        idformaPago = datos[i]["fmp_id"];
                    }
                    html += "<td><input readonly='readonly' class='form-control' style='text-align:center; type='text' type='text' id='" + datos[i]['fmp_descripcion'] + "' value='$" + datos[i]['fpf_total_pagar'].toFixed(2) + "'></td>";

                    //sumaTotales = 0;
                    if (datos[i]["fmp_descripcion"] == "EFECTIVO") {
                        valorRetiroEfectivo = $("#valorEfectivoTotal").val();
                        valorTotalPos = datos[i]["fpf_total_pagar"];
                        diferencia = 0;
                        diferencia = parseFloat(valorRetiroEfectivo) - parseFloat(valorTotalPos);
                        $("#hid_diferencia").val(diferencia);
                        $("#hid_masomenos").val("$" + diferencia.toFixed(2) + "");
                        $("#tpie3 tr:eq(1) td:eq(1)").html(diferencia.toFixed(2));

                        html += "<td><input class='form-control' readonly='readonly' style='text-align:center;' type='text' id='diferenciat" + datos[i]['fmp_descripcion'] + "' value = " + parseFloat(diferencia).toFixed(2) + " ></td>";

                    } else {
                        diferenciaTarjeta = 0 - datos[i]["fpf_total_pagar"].toFixed(2);
                        html += "<td><input class='form-control' readonly='readonly' style='text-align:center;' type='text' id='diferenciat" + datos[i]['fmp_descripcion'] + "' value = '" + diferenciaTarjeta.toFixed(2) + "'></td>";
                    }

                    html += "<input type='hidden' value=" + datos[i]['fmp_id'] + "></tr>";
                    //idcontrolEstacion=datos[i]['ctrc_id'];
                    $("#hid_controlEstacion").val(datos[i]["ctrc_id"]);
                    $("#tabla_formaPagoPorCerrar").html(html);

                    id_botones[i] = "btnT" + datos[i]["fmp_descripcion"].replace(/\s/g, "") + "";

                    //---------------------------------------CUPONES------------------------------------------------------//
                    $("#hide_num_cupones").val(0);

                    send = {"consultaCupones": 1};
                    send.accion = 3;
                    //idformaPago = 0;
                    send.usr_id_cajero = usr_id_cajero;
                    send.ctrc_id = ctrc_id;
                    send.prd_id = prd_id;

                    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                        success: function (datos) {
                            if (datos.str > 0) {
                                for (j = 0; j < datos.str; j++) {
                                    html += "<tr><td width='180' align='center' style='vertical-align:middle'>" + datos[j]["fmp_descripcion"] + "</td>";
                                    html += "<td style='text-align:center;'><input readonly='readonly' class='form-control' style='text-align:center; type='text' value='-'></td>";
                                    html += "<td style='text-align:center;'><input readonly='readonly' class='form-control' style='text-align:center; type='text' value='" + datos[j]['Transacciones'] + "'></td>";
                                    html += "<td style='text-align:center;'><input readonly='readonly' class='form-control' style='text-align:center;  width:130px' type='text' value='-'></td>";
                                    html += "<td style='text-align:center;'><input readonly='readonly' class='form-control' style='text-align:center;' type='text' value='-'></td>";
                                    html += "<td style='text-align:center;'><input readonly='readonly' class='form-control' style='text-align:center; type='text' value='-'></td></tr>";

                                    $("#hide_num_cupones").val(datos[j]["Transacciones"]);
                                }
                                $("#tabla_formaPagoPorCerrar").html(html);
                            }
                        }
                    });
                    //------------------------------------------------------------------------------------------------------//
                }

                $("#modal_formaPago").modal("show");
                $("#hid_controlEfectivo").val(0);

                fn_cargaTotal(ctrc_id, idformaPago, usr_id_cajero);
                $("#valorEfectivoTotal").val();
                $("#totalPosCalculado").val();

                if ($("#valorEfectivoTotal").val() == "") {
                    $("#valorEfectivoTotal").val(0);
                }
                if ($("#totalPosCalculado").val() == "") {
                    $("#totalPosCalculado").val(0);
                }
                diferenciaTotalFormasPago = parseFloat($("#valorEfectivoTotal").val()) - parseFloat($("#totalPosCalculado").val());

                $("#tpie tr:eq(0) td:eq(5)").html(diferenciaTotalFormasPago.toFixed(2));

            } else {
                alertify.error("No existen datos de esta estacion");
                $("#alertify-ok").click(function () {
                    return false;
                });
            }
        }
    });
}

function fn_formasPagoPorCerrarActivo(usr_id_cajero, usr_id_admin, est_id, ctrc_id, usr_usuario, prd_id) {
    send = {"existeCuentaAbiertaMesa": 1};
    send.accion = 2;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                if (datos.cuentaAbiertaMesa == 0) {
                    send = {"existeCuentaAbierta": 1};
                    send.accion = 1;
                    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                        success: function (datos) {
                            if (datos.str > 0) {
                                if (datos.cuentaAbierta == 1) {
                                    nombreUsr = $("#hid_usuarioDescripcion").val();
                                    alertify.alert("No puede desasignar el cajero porque el usuario tiene ordenes abiertas");
                                    $("#alertify-ok").click(function () {
                                        return false;
                                    });
                                } else {
                                    send = {"retirofondo": 1};
                                    send.accion = 1;
                                    send.usr_id_cajero = usr_id_cajero;
                                    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                                        success: function (datos) {
                                            if (datos.str > 0) {
                                                if (datos.retirofondo == 1) {
                                                    alertify.set({labels: {ok: "SI", cancel: "NO"}});
                                                    alertify.confirm("Esta seguro que desea Desasignar el Cajero?");
                                                    $("#alertify-ok").click(function () {
                                                        $("#ModalFormasPagoActivo").modal("hide");

                                                        $("#titulomodalformapagoPorCerrar").html("Corte en Z - Desasignar Cajero: <span class='glyphicon glyphicon-user' aria-hidden='true'></span>" + usr_usuario + "");

                                                        $("#hide_totalBilletes").val("");
                                                        $("#hid_usr_id_cajero").val(usr_id_cajero);
                                                        $("#hid_controlEstacion").val(ctrc_id);
                                                        send = {"consultaformaPago": 1};
                                                        send.accion = 2;
                                                        idformaPago = 0;
                                                        send.usr_id_cajero = usr_id_cajero;
                                                        send.ctrc_id = ctrc_id;
                                                        send.prd_id = prd_id;

                                                        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                                                            success: function (datos) {
                                                                var html = "<thead><tr class='active'><th width='130px' style='text-align:center'>Formas de Pago</th><th width='130px' style='text-align:center'>Retirado</th><th width='130px' style='text-align:center'>Transacciones</th><th style='text-align:center' width='130px'>Monto Actual</th><th style='text-align:center' width='130px'>POS Calculado</th><th style='text-align:center' width='130px'>Mas o Menos</th></thead>";
                                                                if (datos.str > 0) {
                                                                    numeroFormasPago = datos.str;

                                                                    for (i = 0; i < datos.str; i++) {
                                                                        html += "<tr><td width='180' align='center' style='vertical-align:middle'>" + datos[i]["fmp_descripcion"] + "</td>";

                                                                        if (datos[i]["fmp_descripcion"] == "EFECTIVO") {
                                                                            //CONSULTA RETIRO EN EFECTIVO//-------------------------------------------------------------------------
                                                                            send = {"consultaValorRetiroEfectivo": 1};
                                                                            send.accion = 2;
                                                                            send.usr_id_cajero = usr_id_cajero;
                                                                            send.ctrc_id = ctrc_id;

                                                                            $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                                                                                success: function (datos) {
                                                                                    if (datos.str > 0) {
                                                                                        var valor_total_efectivo = datos.ValorTotalEfectivo;
                                                                                        html += "<td><input id='' readonly='readonly' style='text-align:center;' class='form-control' value='$" + valor_total_efectivo.toFixed(2) + "'></td>";

                                                                                        $("#tpie tr:eq(0) td:eq(1)").html("$" + valor_total_efectivo.toFixed(2) + "");
                                                                                        $("#valorEfectivoTotal").val(valor_total_efectivo);
                                                                                        $("#retiroEfectivoModalBilletes").val("$" + valor_total_efectivo.toFixed(2) + "");
                                                                                        $("#tpie3 tr:eq(2) td:eq(1)").html("");
                                                                                        $("#tpie3 tr:eq(2) td:eq(1)").html(valor_total_efectivo.toFixed(2));
                                                                                    }
                                                                                }
                                                                            });
                                                                            //--------------------------------------------------------------------------------------------------------
                                                                        } else {
                                                                            html += "<td></td>";
                                                                        }

                                                                        html += "<td style='text-align:center;'><input readonly='readonly' style='text-align:center;' class='form-control' value=" + datos[i]['Transacciones'] + "></td>";//
                                                                        if (datos[i]["fmp_descripcion"] == "EFECTIVO") {
                                                                            html += "<td><input id='btnT" + datos[i]['fmp_descripcion'].replace(/\s/g, "") + "' type='button'  style='width:130px;' class='btn btn-primary' value='Presione Aqui' onclick='fn_modalBilletes(\"" + datos[i]['fmp_id'] + "\",\"" + datos[i]['ctrc_id'] + "\",\"" + datos[i]['usr_id'] + "\")'></td>";
                                                                            idformaPago = datos[i]["fmp_id"];
                                                                        } else {
                                                                            html += "<td><input id='btnT" + datos[i]['fmp_descripcion'].replace(/\s/g, "") + "' style='width:130px;' type='button' value='Presione Aqui' class='btn btn-primary'	onclick='fn_modalTarjetas(\"" + datos[i]['fmp_id'] + "\",\"" + datos[i]['ctrc_id'] + "\",\"" + datos[i]['usr_id'] + "\")'></td>";
                                                                            idformaPago = datos[i]["fmp_id"];
                                                                        }
                                                                        html += "<td><input readonly='readonly' class='form-control' style='text-align:center; type='text' type='text' id='" + datos[i]['fmp_descripcion'] + "' value='$" + datos[i]['fpf_total_pagar'].toFixed(2) + "'></td>";

                                                                        //sumaTotales = 0;
                                                                        if (datos[i]["fmp_descripcion"] == "EFECTIVO") {
                                                                            valorRetiroEfectivo = $("#valorEfectivoTotal").val();
                                                                            valorTotalPos = datos[i]["fpf_total_pagar"];
                                                                            diferencia = 0;
                                                                            diferencia = parseFloat(valorRetiroEfectivo) - parseFloat(valorTotalPos);
                                                                            $("#hid_diferencia").val(diferencia);
                                                                            $("#hid_masomenos").val("$" + diferencia.toFixed(2) + "");
                                                                            $("#tpie3 tr:eq(1) td:eq(1)").html(diferencia.toFixed(2));

                                                                            html += "<td><input class='form-control' readonly='readonly' style='text-align:center;' type='text' id='diferenciat" + datos[i]['fmp_descripcion'] + "' value = " + parseFloat(diferencia).toFixed(2) + " ></td>";

                                                                        } else {
                                                                            diferenciaTarjeta = 0 - datos[i]["fpf_total_pagar"].toFixed(2);
                                                                            html += "<td><input class='form-control' readonly='readonly' style='text-align:center;' type='text' id='diferenciat" + datos[i]['fmp_descripcion'] + "' value = '" + diferenciaTarjeta.toFixed(2) + "'></td>";
                                                                        }

                                                                        html += "<input type='hidden' value=" + datos[i]["fmp_id"] + "></tr>";
                                                                        //idcontrolEstacion=datos[i]['ctrc_id'];
                                                                        $("#hid_controlEstacion").val(datos[i]["ctrc_id"]);
                                                                        $("#tabla_formaPagoPorCerrar").html(html);

                                                                        id_botones[i] = "btnT" + datos[i]["fmp_descripcion"].replace(/\s/g, "") + "";

                                                                        //---------------------------------------CUPONES------------------------------------------------------//
                                                                        $("#hide_num_cupones").val(0);

                                                                        send = {"consultaCupones": 1};
                                                                        send.accion = 3;
                                                                        //idformaPago = 0;
                                                                        send.usr_id_cajero = usr_id_cajero;
                                                                        send.ctrc_id = ctrc_id;
                                                                        send.prd_id = prd_id;

                                                                        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                                                                            success: function (datos) {
                                                                                if (datos.str > 0) {
                                                                                    for (j = 0; j < datos.str; j++) {
                                                                                        html += "<tr><td width='180' align='center' style='vertical-align:middle'>" + datos[j]["fmp_descripcion"] + "</td>";
                                                                                        html += "<td style='text-align:center;'><input readonly='readonly' class='form-control' style='text-align:center; type='text' type='text' value='-'></td>";
                                                                                        html += "<td style='text-align:center;'><input readonly='readonly' class='form-control' style='text-align:center; type='text' type='text' value='" + datos[j]["Transacciones"] + "'></td>";
                                                                                        html += "<td style='text-align:center;'><input readonly='readonly' class='form-control' style='text-align:center; type='text' type='text' value='-'></td>";
                                                                                        html += "<td style='text-align:center;'><input readonly='readonly' class='form-control' style='text-align:center; type='text' type='text' value='-'></td>";
                                                                                        html += "<td style='text-align:center;'><input readonly='readonly' class='form-control' style='text-align:center; type='text' type='text' value='-'></td></tr>";

                                                                                        $("#hide_num_cupones").val(datos[j]["Transacciones"]);
                                                                                    }
                                                                                    $("#tabla_formaPagoPorCerrar").html(html);
                                                                                }
                                                                            }
                                                                        });
                                                                        //------------------------------------------------------------------------------------------------------//
                                                                    }

                                                                    $("#modal_formaPago").modal("show");
                                                                    $("#hid_controlEfectivo").val(0);

                                                                    fn_cargaTotal(ctrc_id, idformaPago, usr_id_cajero);
                                                                    $("#valorEfectivoTotal").val();
                                                                    $("#totalPosCalculado").val();

                                                                    if ($("#valorEfectivoTotal").val() == "") {
                                                                        $("#valorEfectivoTotal").val(0);
                                                                    }
                                                                    if ($("#totalPosCalculado").val() == "") {
                                                                        $("#totalPosCalculado").val(0);
                                                                    }

                                                                    diferenciaTotalFormasPago = parseFloat($("#valorEfectivoTotal").val()) - parseFloat($("#totalPosCalculado").val());
                                                                    $("#tpie tr:eq(0) td:eq(5)").html(diferenciaTotalFormasPago.toFixed(2));
                                                                } else {
                                                                    alertify.error("No existen datos de esta estacion");
                                                                    $("#alertify-ok").click(function () {
                                                                        return false;
                                                                    });
                                                                }
                                                            }
                                                        });
                                                    });
                                                } else {
                                                    alertify.alert("No puede desasignar el cajero porque el Administrador no ha retirado el fondo");
                                                    return false;
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    });
                } else {
                    nombreUsr = $("#hid_usuarioDescripcion").val();
                    alertify.alert("No puede desasignar el cajero porque el usuario tiene cuentas abiertas");
                    $("#alertify-ok").click(function () {
                        return false;
                    });
                }
            }
        }
    });
}

function fn_desmontarCajeroArqueo() {
    if ($("#txt_numReferencia").val() == "") {
        alertify.error("Ingrese n&uacute;mero de referencia.");
        return false;
    }
    if ($("#txt_fechaDeEntrada").val() == "") {
        alertify.error("Seleccione fecha de dep&oacute;sito");
        return false;
    }
    if ($("#txt_depositoVia").val() == 0) {
        alertify.error("Seleccione via de dep&oacute;sito");
        return false;
    }
    if ($("#txt_codigoReferencia").val() == "") {
        alertify.error("Ingrese c&oacute;digo de referencia.");
        return false;
    }
    if ($("#txt_monedas").val() == "") {
        $("#txt_monedas").val(0);
    }

    if ($("#txt_numReferencia").val() == "") {
        alertify.error("Ingrese n&uacute;mero de referencia.");
        return false;
    }
    if ($("#txt_fechaDeEntrada").val() == "") {
        alertify.error("Seleccione fecha de dep&oacute;sito");
        return false;
    }
    if ($("#txt_depositoVia").val() == 0) {
        alertify.error("Seleccione via de dep&oacute;sito");
        return false;
    }
    if ($("#txt_codigoReferencia").val() == "") {
        alertify.error("Ingrese c&oacute;digo de referencia.");
        return false;
    }
    if ($("#txt_monedas").val() == "") {
        $("#txt_monedas").val(0);
    }
    if (isNaN($("#txt_monedas").val())) {
        alertify.error("Ingrese un n&uacute;mero v&aacute;lido.");
        $("#txt_monedas").focus();
        return false;
    }

    hide_totalBilletes = $("#hide_totalBilletes").val();
    if (banderaAceptaDeposito != 1) {
        alertify.alert("Ingrese valores de formas de pago.");
        return false;
    }
//    if (contadorDeFormasDepago != validadorFormasDePago) {
//        alertify.error('Falta ingresar valores de formas de pago a depositar.');
//        return false;
//    }

//    for (i = 0; i < numeroFormasPago; i++) {
//        if ((isNaN($("#" + id_botones[i] + "").val()))) {
//            alertify.set({labels: {ok: "OK"}});
//            alertify.alert("Faltan valores por ingresar");
//            return false;
//        }
//    }

    if (banderaArqueoTarjeta != 1) {
        alertify.error("Ingrese valores de Arqueo de Caja");
        return false;
    }
    send = {"asientaDeposito": 1};
    send.accionAsienta = 2;
    send.idperiodoAsienta = $("#hide_periodo").val();
    send.referenciaAsienta = $("#txt_numReferencia").val();
    send.papeletaAsienta = $("#txt_codigoReferencia").val();
    send.fechaDepositoAsienta = $("#txt_fechaDeEntrada").val();
    send.monedasAsienta = $("#txt_monedas").val();
    send.depositoAsienta = $("#hide_codigoDeposito").val();
    send.depositoViaAsienta = $("#txt_depositoVia").val();
    send.comentarioAsienta = $("#txt_AreaNuevo").val();
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            alertify.success("Dep&oacute;sito grabado correctamente");
            $("#hide_fechaPeriodo").val("");
            $("#hide_codigoDeposito").val("");
            $("#hide_totalDepositoBilletes").val("");
            $(".cabecera").val("");
            $("#modal_ingresoNuevoDeposito").modal("hide");
            validadorFormasDePago = 0;
            fn_muestraUsuariosEstado("", $("#hide_periodo").val());
        }
    });
}

function fn_modalTarjetas(formaPago, periodoId/*,id_usuario*/) {
    $("#modal_ingresoNuevoDeposito").modal("hide");//oculto la modal de formas de pago
    if (lc_chequeGraba == 2) {
        send = {"consultaTarjeta": 1};
        send.accion = 2;
        send.idPago = formaPago;
        send.periodoId = periodoId;
        send.depositoCh = $("#hide_codigoDeposito").val();
        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    $("#dialogTarjetas").empty();
                    for (i = 0; i < datos.str; i++) {
                        descripcionTarjeta = datos[i]["fmp_descripcion"];
                        totals = datos[i]["total"].toFixed(2);
                        html = "<table align='center'><tr></tr>";
                        html += "<tr><th>Ingrese Monto:" + datos[i]["fmp_descripcion"] + "</th></tr>";
                        html += "<tr><td><input id='txt_montoTarjeta' disabled='disabled' onkeypress='return NumCheck(event,txt_montoTarjeta)'  style='text-align:center; font-size:26px' type='text' value=" + totals + "></td></tr>";
                        html += "</table></br>";
                        html += "<div class=''><div align='center'><button type='button' id='btn_okTarjeta" + datos[i]["fmp_descripcion"] + "' class='btn btn-primary ' style='height:65px;width:140px; font-size: 30px;' onclick='fn_guardarTarjetas(\"" + formaPago + "\",\"" + periodoId + "\");' value='OK' >OK</button><button type='button' class='btn btn-default' id='btn_cancelTarjeta' style='height:65px;width:140px; font-size: 30px;' onclick='fn_cerrarBilletes()' data-dismiss='modal' value='Cancelar'>Cancelar</button></div></div>";
                        $("#dialogTarjetas").append(html);
                    }
                    $("#ModalTarjetas").modal("show");
                    $("#modal_formaPago").modal("hide");
                }
            }
        });
    } else {
        send = {"consultaTarjeta": 1};
        send.accion = 1;
        send.idPago = formaPago;
        send.periodoId = periodoId;
        send.depositoCh = $("#hide_codigoDeposito").val();
        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    $("#dialogTarjetas").empty();
                    for (i = 0; i < datos.str; i++) {
                        descripcionTarjeta = datos[i]["fmp_descripcion"];
                        totals = datos[i]["total"].toFixed(2);
                        html = "<table align='center'><tr></tr>";
                        html += "<tr><th>Ingrese Monto:" + datos[i]["fmp_descripcion"] + "</th></tr>";
                        html += "<tr><td><input id='txt_montoTarjeta' disabled='disabled' onkeypress='return NumCheck(event,txt_montoTarjeta)'  style='text-align:center; font-size:26px' type='text' value=" + totals + "></td></tr>";
                        html += "</table></br>";
                        html += "<div class=''><div align='center'><button type='button' id='btn_okTarjeta" + datos[i]["fmp_descripcion"] + "' class='btn btn-primary ' style='height:65px;width:140px; font-size: 30px;' onclick='fn_guardarTarjetas(\"" + formaPago + "\",\"" + periodoId + "\");' value='OK' >OK</button><button type='button' class='btn btn-default' id='btn_cancelTarjeta' style='height:65px;width:140px; font-size: 30px;' onclick='fn_cerrarBilletes()' data-dismiss='modal' value='Cancelar'>Cancelar</button></div></div>";
                        $("#dialogTarjetas").append(html);
                        //$("#txt_montoTarjeta").keypad();//VERIFICAR EL TECLADO////////////////////////											
                    }
                    $("#ModalTarjetas").modal("show");
                    $("#modal_formaPago").modal("hide");
                    /*
                     $("#btn_cancelTarjeta").click(function () 
                     { 
                     //$("#dialogTarjetas" ).dialog( "close" );	
                     $("#modal_formaPago").modal('show');
                     });*/

                    //$( "#dialogTarjetas" ).dialog( "open" );	
                }
            }
        });
        //$("#hid_controlEfectivo").val(1)
    }
}

function fn_guardarTarjetas(formaPago, periodo) {
    banderaAceptaDeposito = 1;
    idUsuario = $("#hid_usuario_efectivo").val();
    if (($("#txt_montoTarjeta").val()) == "") {
        alertify.error("Ingrese una cantidad");
        return false;
    }
    if (isNaN($("#txt_montoTarjeta").val())) {
        alertify.error("Ingrese monto valido");
        return false;
    }

    send = {"grabaarqueotarjeta": 1};
    totalTarjeta = $("#txt_montoTarjeta").val();
    send.accion = "I";
    send.accion_int = 2;
    send.idPago = formaPago;
    send.totaltarjeta = totalTarjeta;
    send.idUser = idUsuario;
    send.ctrEstacion = $("#hide_codigoDeposito").val();//ctrEstacion;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            lc_chequeGraba = 2;//2 es que fue aceptado el valor del cheque
            validadorFormasDePago = (validadorFormasDePago + 1);
            send = {"auditoriaTarjeta": 1};
            send.accion = "I";

            send.tipoTarjeta = descripcionTarjeta;
            send.totalTarjeta = $("#txt_montoTarjeta").val();
            $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                success: function (datos) {

                }
            });
            /*
             send={"consultaformaPagoModificadoTarjeta":1};				
             send.id_User=idUsuario;
             send.ctrEstacion = ctrEstacion;
             $.ajax({async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"../adminDepositos/config_adminDepositos.php",data:send,success:function(datos)
             {	totaleS=0;
             for(i=0;i<datos.str;i++)
             {
             //var sinespacios = datos[i]['fmp_descripcion'];
             //ELIMINA ESPACIOS EN BLANCO (AMERICAN EXPRESS) = (AMERICANEXPRESS)//CP
             //formapago = sinespacios.replace( /\s/g, "")
             $("#diferenciat"+datos[i]['fmp_descripcion']+"").val(datos[i]['diferencia'].toFixed(2))	
             //$("#hid_controlEfectivo").val(2);										
             }																			
             }	
             })
             */
            //$("#dialogTarjetas").dialog( "close" );	
            $("#ModalTarjetas").modal("hide");
            $("#modal_ingresoNuevoDeposito").modal("show");//oculto la modal de formas de pago
            //$("#modal_formaPago").modal("show");				
            fn_totalesIngresados(0, $("#hide_codigoDeposito").val());
            fn_cargaTotalesDepositoNuevo($("#hide_codigoDeposito").val(), "0", 1);
            //fn_calculatotalesModificados(id_usuario, ctrEstacion);
            //fn_totalesIngresados(id_usuario, ctrEstacion);
            //fn_totalesPos(id_usuario, ctrEstacion);
        }
    });
}


function fn_cargaTotal(ctrc_id, idformaPago, usr_id_cajero) {
    send = {"consultatotalEstacion": 1};
    send.accion = 1;
    send.usr_id_cajero = usr_id_cajero;
    send.ctrc_id = ctrc_id;

    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#tpie tr:eq(0) td:eq(4)").html("$" + (datos.total).toFixed(2));

                $("#tpie tr:eq(0) td:eq(2)").html(parseInt(datos.Transacciones) + parseInt($("#hide_num_cupones").val()));
                $("#totalPosCalculado").val(datos.total);
            }
        }
    });

    send = {"consultatotalformaPago": 1};
    send.idforma = idformaPago;
    send.usr_id_cajero = usr_id_cajero;
    send.ctrc_id = ctrc_id;

    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#totalPos").val("$" + datos.total.toFixed(2));
                $("#tpie3 tr:eq(3) td:eq(1)").html("");
                //$("#tpie3 tr:eq(3) td:eq(1)").html(datos.total.toFixed(2));				
            }
        }
    });
}

function fn_totalesIngresados(id_usuarioo, IDdepositos) {
    monedaSimbo = $("#hide_moneda").val();
    send = {"totalesIngresados": 1};
    send.User = id_usuarioo;
    send.accion = 1;
    send.ctrEstacion = IDdepositos;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            totalesIngresados.length = 0;
            totalesIngresados.length = datos.str;
            for (i = 0; i < datos.str; i++) {
                totalesIngresados.push(datos[i]["arc_valor"]);
                if (datos[i]["arc_valor"] != 0) {
                    $("#btnT" + datos[i]["fmp_descripcion"].replace(/\s/g, "") + "").val(monedaSimbo + " " + datos[i]["arc_valor"].toFixed(2));
                }

                banderaArqueoTarjeta = 1;
            }
        }
    });
}

function fn_btnCancelarFormasPago() {
    $(".cabecera").val("");

    banderaModalActivo = 0;
    if (banderaModalActivo != 0) {
        $("#ModalFormasPagoActivo").modal("show");
    }

    fn_eliminaFormasPagoAgregadas();
    id_botones.length = 0;

    if (($("#hid_controlEfectivo").val()) == 0) {
        $("#modal_formaPago").modal("hide");
        $("#hid_controlEfectivo").val(0);
        $("#tpie tr:eq(0) td:eq(3)").html("");
    } else {
        if (arrayBilletes2.length > 0) {
            fn_eliminaBilletes(arrayBilletes2);
        }
        //fn_eliminacortecaja(arrayBilletes2);
        $("#dialog").dialog("close");
    }
    $("#modal_formaPago").modal("hide");
    $("#tpie tr:eq(0) td:eq(3)").html("");
    $("#hide_totalBilletes").val("");
}

function fn_eliminaBilletes(arrayBilletes2, ctrc_id) {
    ctrc_id = $("#hid_controlEstacion").val();
    arrayBilletes2 = arrayBilletes2;
    send = {"eliminaBilletes": 1};
    send.accion = "D1";
    send.eliminaBillete = arrayBilletes2;
    send.ctrc_id = ctrc_id;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            //window.location.href="../index.php";					
        }
    });
}

function fn_modalBilletes(codigo_formaPago, periodoID) {
    simbMoN = $("#hide_moneda").val();
    total = $("#tpie tr:eq(0) td:eq(4)").text();

    $("#tpie3 tr:eq(3) td:eq(1)").html(total.substring(1));

    $("#tpie3 tr:eq(0) td:eq(1)").html("");

    controla = $("#hid_controlEfectivo").val();

    if (controla == 0) {
        $("#hid_totalNuevo").val("");
        codigo_formaPago = codigo_formaPago;//codigo id de la forma de pago efectivo	
        send = {"consultaBilletes": 1};
        send.accion = 3;
        send.periodoI = periodoID;
        send.idDep = $("#hide_codigoDeposito").val();
        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
            success: function (datos) {
                var html = "<thead><tr class='active'><th width='266px' colspan='2' style='text-align:center' class='tituloEtiqueta'>Denominaciones</th><th width='266px' class='tituloEtiqueta' style='text-align:center'>Calculado</th><th width='266px' class='tituloEtiqueta' style='text-align:center'>Ingresado</th><th width='266px' class='tituloEtiqueta' style='text-align:center'>Total</th></thead>";
                if (datos.str > 0) {
                    $("#modal_ingresoNuevoDeposito").modal("hide");//oculto la modal de formas de pago
                    $("#billetes").empty();
                    miarray = new Array(datos.str);
                    miarray_denominaciones = new Array(datos.str);
                    valoresBilletesNuevos.length = 0;
                    for (i = 0; i < datos.str; i++) {
                        valoresBilletesNuevos.push(datos[i]["btd_id"] + "*" + datos[i]["valorIngresado"] + "*" + datos[i]["bte_total"] + "*");

                        miarray[i] = "" + datos[i]["btd_Valor"] + "";
                        miarray[i] = miarray[i].replace(/\./g, "");
                        miarray_denominaciones[i] = datos[i]["btd_Valor"];

                        if ($.trim(datos[i]["btd_Tipo"]) == "BILLETE") {
                            html += "<tr><td align='center'><img src='../../imagenes/billete_admin.png'/></td><td width='266px' style='text-align:center'><input width='266px' class='form-control' align='center' style='text-align:center;' readonly='readonly' type='text' id=" + miarray[i] + "  value=" + datos[i]["btd_Valor"].toFixed(2) + "></td>";
                        } else {
                            html += "<tr><td align='center'><img src='../../imagenes/moneda_admin.png'/></td><td width='266px' style='text-align:center'><input width='266px' class='form-control' align='center'  style='text-align:center;' readonly='readonly' type='text' id=" + miarray[i] + "  value=" + datos[i]["btd_Valor"].toFixed(2) + "></td>";
                        }
                        html += "<td width='266px' style='text-align:center'><input class='form-control' readonly='readonly' align='center' width='266px' maxlength='7' id='cal" + miarray[i] + "' align='right' style='text-align:center;' type='text' value=" + datos[i]["bte_cantidad"] + ">";

                        html += "<td width='266px' style='text-align:center'><input class='form-control' align='center' width='266px' onkeypress='return fn_numeros(event)' maxlength='7' name = " + datos[i]["btd_id"] + " onclick='fn_borrarcantidad(\"" + datos[i]["btd_id"] + "\")' id='bi" + miarray[i] + "' align='right' style='text-align:center;' type='text' value=" + datos[i]["valorIngresado"] + "";
                        if (datos[i]["bte_cantidad"] == 0) {
                            html += " readonly='readonly'></td>";
                        } else {
                            html += " ></td>";// value="+datos[i]['bte_cantidad']+" JF	
                        }
                        html += "<td width='266px' style='text-align:center'><input class='form-control sumar_linea' name = 't" + datos[i]["btd_id"] + "' align='center' width='266px' id='t" + miarray[i] + "' align='right' style='text-align:center;' type='text' readonly='readonly' value=" + datos[i]["bte_total"].toFixed(2) + "></td>";//																												
                        html += "<input type='hidden' id='h" + miarray[i] + "' value=" + datos[i]["btd_id"] + ">";
                        html += "<input type='hidden' id='h2" + miarray[i] + "'>";
                        html += "<input type='hidden' id='h3" + miarray[i] + "'></tr>";//VERIFICAR VARIABLE SESION USUARIO
//                        html+="<input type='hidden' id='audit"+miarray[i]+"' value="+idUsuario+">
                        $("#billetes").html(html);
                    }

                    /*Sumamos los valores de la clase sumar_linea*/
                    sumaBilletesNuevos_total = 0;
                    $(".sumar_linea").each(function (index, value) {
                        sumaBilletesNuevos_total = sumaBilletesNuevos_total + eval($(this).val());
                    });
                    $("#hide_totalDepositoBilletes").val(sumaBilletesNuevos_total);
                    //console.log(sumaBilletesNuevos_total);
                    $("#tpie3 tr:eq(0) td:eq(1)").html(simbMoN + " " + 0/*sumaBilletesNuevos_total*/.toFixed(2));

                    //$("#modal_formaPago").modal("hide");
                    diferencia = $("#hid_diferencia").val();
                    $("#hid_masomenos").val("$" + parseFloat(diferencia).toFixed(2) + "");
                    $("#tpie3 tr:eq(1) td:eq(2)").html("" + parseFloat(diferencia).toFixed(2) + "");

                    fn_calculaSubtotales(miarray, codigo_formaPago, "0", "0"/*idUsuario, codigo_ctrEstacion*/);
                }
            }
        });
    } else {
        send = {"consultaBilletesModificados": 1};
        send.accion = 1;
        send.periodoI = periodoID;
        send.idDep = $("#hide_codigoDeposito").val();
        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
            success: function (datos) {
                var html = "<thead><tr class='active'><th width='266px' colspan='2' style='text-align:center' class='tituloEtiqueta'>Denominaciones</th><th width='266px' class='tituloEtiqueta' style='text-align:center'>Calculado</th><th width='266px' class='tituloEtiqueta' style='text-align:center'>Ingresado</th><th width='266px' class='tituloEtiqueta' style='text-align:center'>Total</th></thead>";
                if (datos.str > 0) {
                    $("#modal_ingresoNuevoDeposito").modal("hide");//oculto la modal de formas de pago
                    $("#billetes").empty();
                    miarray = new Array(datos.str);
                    miarray_denominaciones = new Array(datos.str);
                    valoresBilletesNuevos.length = 0;
                    lc_grabaDirecto = 1;
                    for (i = 0; i < datos.str; i++) {
                        valoresBilletesNuevos.push(datos[i]["btd_id"] + "*" + datos[i]["valorIngresado"] + "*" + datos[i]["bte_total"] + "*");
                        miarray[i] = "" + datos[i]["btd_Valor"] + "";
                        miarray[i] = miarray[i].replace(/\./g, "");
                        miarray_denominaciones[i] = datos[i]["btd_Valor"];
                        if ($.trim(datos[i]["btd_Tipo"]) == "BILLETE") {
                            html += "<tr><td align='center'><img src='../../imagenes/billete_admin.png'/></td><td width='266px' style='text-align:center'><input width='266px' class='form-control' align='center'  style='text-align:center;' readonly='readonly' type='text' id=" + miarray[i] + "  value=" + datos[i]["btd_Valor"].toFixed(2) + "></td>";
                        } else {
                            html += "<tr><td align='center'><img src='../../imagenes/moneda_admin.png'/></td><td width='266px' style='text-align:center'><input width='266px' class='form-control' align='center'  style='text-align:center;' readonly='readonly' type='text' id=" + miarray[i] + "  value=" + datos[i]["btd_Valor"].toFixed(2) + "></td>";
                        }

                        html += "<td width='266px' style='text-align:center'><input class='form-control' readonly='readonly' align='center' width='266px' maxlength='7' id='cal" + miarray[i] + "' align='right' style='text-align:center;' type='text' value=" + datos[i]["bte_cantidad"] + ">";
                        html += "<td width='266px' style='text-align:center'><input class='form-control' align='center' width='266px' onkeypress='return fn_numeros(event)' maxlength='7' name = " + datos[i]["btd_id"] + " onclick='fn_borrarcantidad(\"" + datos[i]["btd_id"] + "\")' id='bi" + miarray[i] + "' align='right' style='text-align:center;' type='text' value=" + datos[i]["valorIngresado"] + "";
                        if (datos[i]["bte_cantidad"] == 0) {
                            html += " readonly='readonly'></td>";
                        } else {
                            html += " ></td>";// value="+datos[i]['bte_cantidad']+" JF	
                        }
                        html += "<td style='text-align:center'><input class='form-control sumar_linea2' id='t" + miarray[i] + "' align='right' style='text-align:center;' readonly='readonly' name ='t" + datos[i]["btd_id"] + "' type='text' value=" + datos[i]["bte_total"] + "></td>";//																												
                        html += "<input type='hidden' id='h" + miarray[i] + "' value=" + datos[i]["btd_id"] + ">";
                        html += "<input type='hidden' id='h2" + miarray[i] + "'>";
                        html += "<input type='hidden' id='h3" + miarray[i] + "'></tr>";
                        $("#billetes").html(html);
                    }

                    sumaBilletesNuevos_total = 0;
                    $(".sumar_linea2").each(function (index, value) {
                        sumaBilletesNuevos_total = sumaBilletesNuevos_total + eval($(this).val());
                    });
                    $("#hide_totalDepositoBilletes").val(sumaBilletesNuevos_total);
                    //console.log(sumaBilletesNuevos_total);
                    $("#tpie3 tr:eq(0) td:eq(1)").html(simbMoN + " " + 0/*sumaBilletesNuevos_total*/.toFixed(2));

                    diferencia = $("#hid_diferencia").val();
                    $("#hid_masomenos").val(parseFloat(diferencia).toFixed(2) + "");
                    $("#tpie3 tr:eq(1) td:eq(1)").html("" + parseFloat(diferencia).toFixed(2) + "");

                    //$("#modal_formaPago").dialog( "close" );
                    //$("#modal_formaPago").modal("hide");

                    //fn_calculaSubtotalesMod(miarray,codigo_formaPago/*,id_estacion*/,idUsuario, codigo_ctrEstacion);
                    fn_calculaSubtotalesMod(miarray, codigo_formaPago, "0", "0"/*idUsuario, codigo_ctrEstacion*/);
                }
            }
        });
    }
}

function fn_calculaSubtotales(array, codigo_formaPago, idUsuario, codigo_ctrEstacion) {
    lc_grabaDirecto = -1;
    $("#hid_usuario_efectivo").val(idUsuario);
    $("#hid_controlEstacion").val(codigo_ctrEstacion);

    $("#hid_formaPago").val(codigo_formaPago);
    $("#array").val(array);

    for (i = 0; i < subtotales.length; i++) {
        subtotales[i] = ($("#t" + array[i] + "").val());
    }
    for (i = 0; i < ocultos.length; i++) {
        ocultos[i] = ($("#h" + array[i] + "").val());
    }
    for (i = 0; i < ocultos2.length; i++) {
        ocultos2[i] = ($("#h2" + array[i] + "").val());
    }
    for (i = 0; i < ocultos3.length; i++) {
        ocultos3[i] = ($("#h3" + array[i] + "").val());
    }

    $("#ModalBilletesDesmontarCajero").modal("show");
//    lc_grabaDirecto=1;
    monSimb = $("#hide_moneda").val();
    $("#cancelar").click(function () {
        $("#dialog2").dialog("close");
        //$("#modal_formaPago").dialog( "open" );
        $("#modal_formaPago").modal("show");
    });
    $("#dialog2").dialog("open");

    for (i = 0; i < array.length; i++) {
        $("#bi" + array[i] + "").blur(function () {
            idBill = $(this).attr("id");
            subIdCalculado = idBill.substring(2, 6);
            valorDelCalculado = $("#cal" + subIdCalculado).val();
            valorDelIngresado = $("#" + idBill + "").val();
            if (parseInt(valorDelIngresado) > parseInt(valorDelCalculado)) {
                $("#" + idBill + "").val(valorDelCalculado);
            }

            lc_grabaDirecto = 2;
            for (i = 0; i < denominaciones.length; i++) {
                denominaciones[i] = (miarray_denominaciones[i]);
            }

            for (i = 0; i < cantidades.length; i++) {
                if (($("#bi" + array[i] + "").val() == "")) {
                    $("#bi" + array[i] + "").val(0);
                }
                //cantidades[i] = $("input:text[name="+array+"]").val();
                cantidades[i] = ($("#bi" + array[i] + "").val());
            }

            for (i = 0; i < resultado.length; i++) {
                resultado[i] = denominaciones[i] * cantidades[i];
            }

            for (i = 0; i < array.length; i++) {
                ($("#t" + array[i] + "").val(resultado[i].toFixed(2)));
            }

            suma = 0;
            for (var i = 0; i < array.length; i++) {
                suma += parseFloat(resultado[i]);
            }
            suma2 = suma;//totalNuevo
            $("#hid_totalNuevo").val(suma2);
            $("#tpie3 tr:eq(0) td:eq(1)").html(monSimb + " " + suma2.toFixed(2));

            dif = $("#hid_diferencia").val();
            dif_Total = parseFloat(dif) + parseFloat(suma2);
            $("#hid_masomenos").val(dif_Total.toFixed(2));
            $("#tpie3 tr:eq(1) td:eq(1)").html(dif_Total.toFixed(2));

            if (dif_Total == 0) {
                $("#tr_masomenos").addClass("success");
                $("#tr_masomenos").removeClass("danger");
            } else {
                $("#tr_masomenos").addClass("danger");
                $("#tr_masomenos").removeClass("success");
            }

        });
        valorsumabilletes = $("#valorsumabilletes").val();
        valormasomenos = $("#valormasomenos").val();
        //$("#tpie3 tr:eq(0) td:eq(1)").html(valorsumabilletes);
        //$("#tpie3 tr:eq(1) td:eq(1)").html(valormasomenos);					

        input_dif_Total = $("#hid_masomenos").val();

        if (parseFloat(input_dif_Total) == 0) {
            $("#tr_masomenos").addClass("success");
            $("#tr_masomenos").removeClass("danger");
        } else {
            $("#tr_masomenos").addClass("danger");
            $("#tr_masomenos").removeClass("success");
        }
    }
}

function fn_calculaSubtotalesMod(array, codigo_formaPago, idUsuario) {
    lc_grabaDirecto = -1;
    $("#hid_usuario_efectivo").val(idUsuario);
    $("#hid_formaPago").val(codigo_formaPago);
    $("#array").val(array);

    for (i = 0; i < subtotales.length; i++) {
        subtotales[i] = ($("#t" + array[i] + "").val());
    }
    for (i = 0; i < ocultos.length; i++) {
        ocultos[i] = ($("#h" + array[i] + "").val());
    }
    for (i = 0; i < ocultos2.length; i++) {
        ocultos2[i] = ($("#h2" + array[i] + "").val());
    }
    for (i = 0; i < ocultos3.length; i++) {
        ocultos3[i] = ($("#h3" + array[i] + "").val());
    }

    $("#ModalBilletesDesmontarCajero").modal("show");

    $("#cancelar").click(function () {
        $("#dialog2").dialog("close");
        //$("#modal_formaPago").dialog( "open" );
        $("#modal_formaPago").modal("show");
    });
    $("#dialog2").dialog("open");

    for (i = 0; i < array.length; i++) {
        $("#bi" + array[i] + "").blur(function () {
            idBill = $(this).attr("id");
            subIdCalculado = idBill.substring(2, 6);
            valorDelCalculado = $("#cal" + subIdCalculado).val();
            valorDelIngresado = $("#" + idBill + "").val();
            if (parseInt(valorDelIngresado) > parseInt(valorDelCalculado)) {
                $("#" + idBill + "").val(valorDelCalculado);
            }
            lc_grabaDirecto = 2;

            for (i = 0; i < denominaciones.length; i++) {
                denominaciones[i] = (miarray_denominaciones[i]);
            }

            for (i = 0; i < cantidades.length; i++) {
                if (($("#bi" + array[i] + "").val() == "")) {
                    $("#bi" + array[i] + "").val(0);
                }
                //cantidades[i] = $("input:text[name="+array+"]").val();
                cantidades[i] = ($("#bi" + array[i] + "").val());
            }

            for (i = 0; i < resultado.length; i++) {
                resultado[i] = denominaciones[i] * cantidades[i];

            }
            for (i = 0; i < array.length; i++) {
                ($("#t" + array[i] + "").val(resultado[i].toFixed(2)));
            }

            suma = 0;
            for (var i = 0; i < array.length; i++) {
                suma += parseFloat(resultado[i]);
            }

            suma2 = suma;//totalNuevo
            $("#hid_totalNuevo").val(suma2);
            $("#tpie3 tr:eq(0) td:eq(1)").html(monSimb + " " + suma2.toFixed(2));

            dif = $("#hid_diferencia").val();
            dif_Total = parseFloat(dif) + parseFloat(suma2);
            $("#hid_masomenos").val(dif_Total.toFixed(2));
            $("#tpie3 tr:eq(1) td:eq(1)").html(dif_Total.toFixed(2));

            if (dif_Total == 0) {
                $("#tr_masomenos").addClass("success");
                $("#tr_masomenos").removeClass("danger");
            } else {
                $("#tr_masomenos").addClass("danger");
                $("#tr_masomenos").removeClass("success");
            }
        });
        valorsumabilletes = $("#valorsumabilletes").val();
        valormasomenos = $("#valormasomenos").val();
        $("#tpie3 tr:eq(0) td:eq(1)").html(valorsumabilletes);
        $("#tpie3 tr:eq(1) td:eq(1)").html(valormasomenos);

        input_dif_Total = $("#hid_masomenos").val();

        if (parseFloat(input_dif_Total) == 0) {
            $("#tr_masomenos").addClass("success");
            $("#tr_masomenos").removeClass("danger");
        } else {
            $("#tr_masomenos").addClass("danger");
            $("#tr_masomenos").removeClass("success");
        }
    }
}

function fn_borrarcantidad(array) {
    $("input:text[name=" + array + "]").val("");
}

function fn_guardaTotalesBilletes() {
    $("#modal_ingresoNuevoDeposito").modal("show");//muestro la modal de formas de pago	
    valorsumabilletes = $("#tpie3 tr:eq(0) td:eq(1)").text();
    valormasomenos = $("#tpie3 tr:eq(1) td:eq(1)").text();
    $("#valorsumabilletes").val(valorsumabilletes);
    $("#valormasomenos").val(valormasomenos);
    valorsumabilletes = (valorsumabilletes.substr(1, 8));
    if (parseFloat(valorsumabilletes) == 0/*($("#hide_totalDepositoBilletes").val()=="")||($("#hide_totalDepositoBilletes").val()==0)*//*($("#hid_totalNuevo").val()) == ""*/) {
        alertify.error("Ingrese cantidades");
        return false;
    }
    if (lc_grabaDirecto == 2) {
        resultado = $.grep(resultado, function (n) {
            return n == 0 || n
        }); //eliminar posiciones vacias de un array//cp
        $("#hid_controlEfectivo").val(1);
        totalNuevo = $("#hid_totalNuevo").val();

        send = {"grabaBilletes": 1};
        send.accion = "I2";
        send.cantidades2 = cantidades;
        send.resultado2 = resultado;
        send.oculto = ocultos;
        send.oculto2 = $("#hide_codigoDeposito").val();
        //send.oculto3=ocultos3;
        send.tipoEfectivo = 0;
        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
            success: function (datos) {
                controla = $("#hid_controlEfectivo").val();
                send = {"auditoriaEfectivo": 1};
                send.accion = "I";
                send.auditoriaTotal = totalNuevo.substring(1);
                $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                    success: function (datos) {

                    }
                });

                array = $("#array").val();

                /*send={"consultaidBilletes":1};	
                 send.accion = 2;
                 send.top=array.length;
                 $.ajax
                 ({						
                 async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"../adminDepositos/config_adminDepositos.php",data:send,success:function(datos)
                 {	
                 
                 arrayBilletes = new Array(datos.str);
                 for(i=0;i<datos.str;i++)
                 {										
                 arrayBilletes[i] = ""+datos[i]["bte_id"]+"";																																							
                 }
                 arrayBilletes2=arrayBilletes;
                 
                 }
                 
                 });*/

                codigo_formaPago = $("#hid_formaPago").val();
                idUsuario = $("#hid_usuario_efectivo").val();
                ctrEstacion = $("#hid_controlEstacion").val();

                send = {"grabaArqueo": 1};
                send.accion = "I";
                send.accion_int = 1;
                send.resNuevo = totalNuevo;
                send.formaPago = codigo_formaPago;
                send.idUsuario = idUsuario;
                send.ctrEstacion = $("#hide_codigoDeposito").val();
                $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                    success: function (datos) {
                        banderaAceptaDeposito = 1;
                        lc_grabaDirecto = -1;
                        validadorFormasDePago = (validadorFormasDePago + 1);
                        $("#hid_controlEfectivo").val(1);
                        $("#ModalBilletesDesmontarCajero").modal("hide");
                        fn_totalesIngresados(idUsuario, $("#hide_codigoDeposito").val());
                        fn_cargaTotalesDepositoNuevo($("#hide_codigoDeposito").val(), "0", 1);
                        //fn_totalesIngresados(idUsuario, $("#hide_codigoDeposito").val());

                        /*
                         send={"consultaformaPagoModificado":1};
                         send.accion = 1;
                         send.ctr_estacion = ctrEstacion;
                         send.id_usuariO=idUsuario;							
                         $.ajax
                         ({
                         async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"../adminDepositos/config_adminDepositos.php",data:send,success:function(datos)
                         {	
                         if(datos.str>0)							
                         {
                         $("#diferenciat"+datos.fmp_descripcion+"").empty();
                         for(i=0;i<datos.str;i++)
                         {
                         
                         $("#diferenciat"+datos[i]["fmp_descripcion"]+"").val(datos[i]["diferencia"].toFixed(2))																										
                         }
                         
                         
                         }	
                         $("#ModalBilletesDesmontarCajero").modal("hide");
                         //$("#modal_formaPago").dialog( "open" );
                         //$("#modal_formaPago").modal("show");
                         //fn_calculatotalesModificados(idUsuario, ctrEstacion);
                         //fn_totalesIngresados(idUsuario, ctrEstacion);
                         //fn_totalesPos(idUsuario, ctrEstacion);	
                         
                         
                         }
                         });
                         */
                        // FIN DE LA CONSULTA DE CONSULTApAGOmOFDIFICADO
                    }
                });
            }
        });
    } else {
        send = {"grabaBilletesDirecto": 1};
        send.accion = "ID";
        send.valoresD = valoresBilletesNuevos.toString();
        send.depositoD = $("#hide_codigoDeposito").val();//ocultos2;
        send.tipoEfectivo = 0;
        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
            success: function (datos) {
                valoresBilletesNuevos.length = 0;
                controla = $("#hid_controlEfectivo").val();
                codigo_formaPago = $("#hid_formaPago").val();
                idUsuario = $("#hid_usuario_efectivo").val();
                totalNuevo = $("#hid_totalNuevo").val();

                send = {"grabaArqueo": 1};
                send.accion = "I";
                send.accion_int = 1;
                send.resNuevo = $("#tpie3 tr:eq(0) td:eq(1)").html();
                send.formaPago = codigo_formaPago;
                send.idUsuario = idUsuario;
                send.ctrEstacion = $("#hide_codigoDeposito").val();
                $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                    success: function (datos) {
                        banderaAceptaDeposito = 1;
                        validadorFormasDePago = (validadorFormasDePago + 1);
                        $("#hid_controlEfectivo").val(1);
                        $("#ModalBilletesDesmontarCajero").modal("hide");
                        fn_totalesIngresados(idUsuario, $("#hide_codigoDeposito").val());
                        fn_cargaTotalesDepositoNuevo($("#hide_codigoDeposito").val(), "0", 1);
                        //lc_grabaDirecto=2;												
                    }
                });
            }
        });
    }
}

function fn_totalesPos(id_usuario, ctrEstacion) {
    send = {"totalesPos": 1};
    send.accion = 1;
    send.t_idUsuario = id_usuario;
    send.ctrEstacion = ctrEstacion;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            $("#tpie tr:eq(0) td:eq(3)").html(datos.totalesPos.toFixed(2));
            $("#hide_totalBilletes").val(datos.totalesPos);
        }
    });
}

function fn_calculatotalesModificados(id_usuario, ctrEstacion) {
    send = {"calculatotalesModificados": 1};
    send.userId = id_usuario;
    send.accion = 1;
    send.ctr_estacion = ctrEstacion;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            $("#tpie tr:eq(0) td:eq(5)").html(datos.totalModificado.toFixed(2));
            $("#hid_descuadre").val(datos.totalModificado);
            $("#hid_controlDiferencia").val(datos.totalModificado);
        }
    });
}

function fn_limpiarCantidad() {
    $("#txt_montoTarjeta").val("");
    coma = 0;
}

function fn_agregarNumero(valor) {
    var cantidad = $("#txt_montoTarjeta").val();
    if (cantidad.length < 8) {
        //presionamos la primera vez punto
        if ((cantidad == "" || cantidad == 0) && valor == ".") {
            //si escribimos una coma al principio del número
            $("#txt_montoTarjeta").val("0."); //escribimos 0.
            coma = 1;
        } else {
            //continuar escribiendo un n�mero
            if (valor == "." && coma == 0) {
                //si escribimos una coma decimal por primera vez
                cantidad = cantidad + "" + valor;
                $("#txt_montoTarjeta").val(cantidad);
                coma = 1; //cambiar el estado de la coma

            } else if (!(valor == "." && coma == 1)) { //si intentamos escribir una segunda coma decimal no realiza ninguna acción, resto de casos: escribir un número del 0 al 9
                var variable = cantidad;
                var indice = 0;
                indice = variable.indexOf(".") + 1;
                if (indice > 0) {
                    variable = variable.substring(indice, variable.length);
                    if (variable.length <= 2) {
                        cantidad = cantidad + "" + valor;
                        $("#txt_montoTarjeta").val(cantidad);
                        fn_focusTarjeta();
                    }
                } else {
                    cantidad = cantidad + "" + valor;
                    $("#txt_montoTarjeta").val(cantidad);
                    fn_focusTarjeta();
                }
            }
        }
    }
    fn_focusTarjeta();
}

function fn_focusTarjeta() {
    $("#txt_montoTarjeta").focus();
}

function fn_eliminarCantidad() {
    var lc_cantidad = document.getElementById("txt_montoTarjeta").value.substring(0, document.getElementById("txt_montoTarjeta").value.length - 1);

    if (lc_cantidad == "") {
        lc_cantidad = "";
        coma = 0;
    }
    if (lc_cantidad == ".") {
        coma = 0;
    }

    document.getElementById("txt_montoTarjeta").value = lc_cantidad;
    fn_focusTarjeta();
}

function fn_botonesDinamicosTecladoDescuadre() {
    send = {"traeMotivosDescuadre": 1};
    send.accion = 1;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#motivos_descuadre1").empty();
                for (i = 0; i < datos.str; i++) {
                    html = "<div class='col-xs-3'><button class='btnVirtualSugerencias' style = 'width:185px; height:80px' onclick='fn_borrarTextArea(); fn_agregarCaracter(txtArea,\"" + datos[i]["mtv_descripcion"] + "\")'>" + datos[i]["mtv_descripcion"] + "</button></div>";
                    $("#motivos_descuadre1").append(html);
                }
            } else {
                alertify.error("Motivos de Descuadre no configurados");
            }
        }
    });
}

function fn_imprimeDesmontadoCajero() {
    usr_id = $("#hid_usuario_efectivo").val();
    ctrc_id = $("#hid_controlEstacion").val();
    //usr_clave = $("#usr_clave").val();

    send = {"traeUsuarioAdmin": 1};
    send.accion = 5;
    //send.usr_clave = usr_clave;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                usr_id_admin = datos.usr_id;
                send = {"imprimeDesmontadoCajero": 1};
                send.usr_id = usr_id;
                send.ctrc_id = ctrc_id;
                send.usr_id_admin = usr_id_admin;
                $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                    success: function (datos) {
                        //window.open("impresionCorteCajaDinamico.php?ctrc_id=" + ctrc_id + "&usr_id= "+ usr_id +"&usr_id_admin= "+ usr_id_admin +"&"," "," scrollbars=yes, resizable=yes, top=500, left=500, width=400, height=800");

                        //window.open("http://www.w3schools.com", "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=500, left=500, width=400, height=400");	
                    }
                });
            }
        }
    });
}

function fn_validaAdmin() {
    $("#txtAjuste").val("");
    $("#modal_ingresoModificaDeposito").modal("hide");
    $("#modal_agregarAjuste").modal("show");
}

function fn_cerrarModalAjuste() {
    $("#modal_ingresoModificaDeposito").modal("show");
    $("#modal_agregarAjuste").modal("hide");
}

function fn_agregarFormaPago() {
    monedaSimboll = $("#hide_moneda").val();
    if (!$("input:radio[name=options]:checked").is(":checked")) {
        alertify.error("seleccione un operador + -");
        return false;
    }

    if ($("#txtAjuste").val() == "") {
        alertify.error("Ingrese valor de Ajuste.");
        return false;
    }
    valorAjuste = parseFloat($("#txtAjuste").val());
    if (valorAjuste == 0) {
        alertify.error("Ingrese un valor diferente de 0 (cero).");
        return false;
    } // validaciones de ingreso de ajuste
    if ($("#sel_Ajuste").val() == 0) {
        alertify.error("Seleccione concepto de Ajuste.");
        return false;
    }

    ctrc_id = $("#hid_controlEstacion").val();
    fmp_descripcion = $("#hide_fmp_descripcion").val();
    fmp_id = $("#id_formaPago").val();

    send = {"grabaarqueoAjuste": 1};
    send.accion = "I";
    send.accion_int = 7;
    send.fmp_id = -1;
    send.totalFormaPago = valorAjuste;
    send.banderafp = 1;
    send.codDepositoModificado = $("#hide_codigoDepositoModificado").val();//ctrc_id;
    send.operadorAjuste = $("input:checked").val();
    send.ajusteNuevo = $("#sel_Ajuste").val();
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            banderaAjuste = 1;
            $("#modal_agregarAjuste").modal("hide");
            $("#modal_ingresoModificaDeposito").modal("show");
            $("#tabla_ingresoModificaDeposito").empty();
            $("#btn_agregarAjuste").hide();
            send = {"consultaDetalleDepositoModificado": 1};
            send.accion = 2;
            send.prd_idM = $("#hide_codigoPeriodoModificado").val();
            send.depositoM = $("#hide_codigoDepositoModificado").val();
            $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                success: function (datos) {
                    var html = "<thead><tr class='active'><th width='130px' style='text-align:center'>Formas de Pago</th><th style='text-align:center' width='130px'>Monto Actual</th></thead>";
                    if (datos.str > 0) {
                        arrayDescripcionFormasPagoModificado.length = 0;
                        numeroFormasPago = datos.str;
                        for (i = 0; i < datos.str; i++) {
                            arrayDescripcionFormasPagoModificado.push(datos[i]["fmp_descripcion"]);
                            html += "<tr><td width='180' align='center' style='vertical-align:middle'>" + datos[i]["fmp_descripcion"] + "</td>";
                            if (datos[i]["fmp_descripcion"].match(/^AJUSTE.*$/)) {
                                html += "<td align='center'><input id='btnT" + datos[i]["fmp_descripcion"].replace(/\s/g, "") + "' type='button'  style='width:130px;' class='btn btn-primary' value='" + simboloDeLaMoneda + " " + datos[i]["montoActual"].toFixed(2) + "' onclick='fn_cargaAjusteModificar()'></td>";
                                //banderaAjuste=2;
                            } else {
                                html += "<td align='center'><input id='btnT" + datos[i]["fmp_descripcion"].replace(/\s/g, "") + "' type='button'  style='width:130px;' class='btn btn-primary' value='" + monedaSimboll + " " + datos[i]["montoActual"].toFixed(2) + "'></td>";
                            }
                            idformaPago = datos[i]["fmp_id"];

                            $("#tabla_ingresoModificaDeposito").html(html);
                        }
                        fn_cargaTotalesDepositoAModificar($("#hide_codigoDepositoModificado").val(), $("#hide_codigoPeriodoModificado").val());
                    }
                }
            });
            /*	
             html = "";
             html+="<tr><td width='180' align='center' style='vertical-align:middle'>"+fmp_descripcion+"</td>";
             html+="<td></td>";
             html+="<td style='text-align:center;'><input readonly='readonly' style='text-align:center;' class='form-control' value='1'></td>";
             html+="<td><input id='btnT"+fmp_descripcion.replace( /\s/g, "")+"' style='width:130px;' type='button' value='Presione Aqui' class='btn btn-primary'	onclick='fn_modalTecladoFormasPago(\""+fmp_descripcion+"\",\""+fmp_id+"\")'></td>";
             html+="<td><input readonly='readonly' class='form-control' style='text-align:center;' type='text' type='text' id='inputT"+fmp_descripcion.replace( /\s/g, "")+"' value=''></td>";
             html+="<td><input class='form-control' readonly='readonly' style='text-align:center;' type='text' id='diferenciat"+fmp_descripcion.replace( /\s/g, "")+"' value = '' ></td>";
             
             $("#tabla_ingresoModificaDeposito").append(html);								
             numeroFormasPago++;
             id_botones.push("btnT"+fmp_descripcion.replace( /\s/g, ""));
             */
        }
    });

    //id_botones[id_botones_posicionArray+1] = "btnT"+fmp_descripcion.replace( /\s/g, "")+"";

    //$("#modal_agregarFormaPago").modal('hide');
    //$("#modal_formaPago").modal('show');
}

function fn_eliminaFormasPagoAgregadas() {
    ctrc_id = $("#hid_controlEstacion").val();
    send = {"eliminaFormasPagoAgregadas": 1};
    send.accion = "D";
    send.banderafp = 1;
    send.ctrc_id = $("#hide_codigoDeposito").val();//ctrc_id;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
        }
    });
}

function fn_modalTecladoFormasPago(fmp_descripcion, fmp_id) {
    $("#tituloModalTecladoNuevaFormaPago").html("Corte en Z - " + fmp_descripcion);

    ctrc_id = $("#hid_controlEstacion").val();
    send = {"traeValorFormaPago": 1};
    send.accion = 2;
    send.fmp_id = fmp_id;
    send.ctrc_id = ctrc_id;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            $("#tecladoNuevaFormaPago").empty();

            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    if (datos[i]["arc_valor"] == 0) {
                        $("#txt_montoFormaPago").val("");
                    }
                    html = "<table align='center'><tr></tr>";
                    html += "<tr><th>Ingrese Monto: " + fmp_descripcion + "</th></tr>";

                    if (datos[i]["arc_valor"] == "0") {
                        html += "<tr><td><input id='txt_montoFormaPago' onkeypress='return fn_numeros(event)'  style='text-align:center; font-size:26px' type='text' value=''></td></tr>";
                    } else {
                        html += "<tr><td><input id='txt_montoFormaPago' onkeypress='return fn_numeros(event)'  style='text-align:center; font-size:30px' type='text' value='" + datos[i]["arc_valor"].toFixed(2) + "'></td></tr>";
                    }

                    html += "</table></br>";
                    html += "<table align='center'><tr><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('7') >7</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('8')>8</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('9')>9</button></td></tr><tr><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('4')>4</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('5')>5</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('6')>6</button></td></tr><tr><td><button class='btnVirtual'  style='font-size: 34px;' onclick=fn_agregarNumerof('1')>1</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('2')>2</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('3')>3</button></td></tr><tr><td ><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('0')>0</button></td><td><button class='btnVirtualBorrarTarjetas' style='font-size: 34px;' id='btn_punto' onclick=fn_agregarNumerof('.')>.</button></td><td><button class='btnVirtualBorrarTarjetas' style='font-size: 34px;' id='btn_borrar' onclick=fn_eliminarCantidadf()>&larr;</button></td></tr><tr><td colspan='3'><button class='btnVirtualLimpiar' style='font-size: 34px;' id='btn_limpiar' onclick='fn_limpiarCantidadf()'>LIMPIAR</button></td></tr></table><br/>";

                    html += "<div class=''><div align='center'><button type='button' id='btn_okTarjeta" + fmp_descripcion + "' class='btn btn-primary ' style='height:65px;width:140px; font-size: 30px;' onclick='fn_guardarFormaPago(\"" + fmp_id + "\",\"" + fmp_descripcion + "\");'>OK</button><button type='button' class='btn btn-default' id='btn_cancelTarjeta' style='height:65px;width:140px; font-size: 30px;' onclick='' data-dismiss='modal' value='Cancelar'>Cancelar</button></div></div>";
                    $("#tecladoNuevaFormaPago").append(html);

                    $("#ModalTecladoNuevaFormaPago").modal("show");
                }
            }
        }
    });
}

function fn_guardarFormaPago(fmp_id, fmp_descripcion) {
    if (($("#txt_montoFormaPago").val()) == "") {
        alertify.error("Ingrese una cantidad");
        return false;
    }

    if (($("#txt_montoFormaPago").val()) == "0.00") {
        alertify.error("Ingrese una cantidad mayor de cero");
        return false;
    }

    if (isNaN($("#txt_montoFormaPago").val())) {
        alertify.error("Ingrese monto valido");
        return false;
    }

    ctrc_id = $("#hid_controlEstacion").val();
    banderaFormaPagoCantidad = 1;
    send = {"grabaarqueoformapago": 1};
    totalFormaPago = $("#txt_montoFormaPago").val();
    send.accion = "I";
    send.accion_int = 2;
    send.fmp_id = fmp_id;
    send.totalFormaPago = totalFormaPago;
    send.ctrc_id = ctrc_id;
    send.banderafp = 1;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
        success: function (datos) {
            send = {"auditoriaTarjeta": 1};
            send.accion = "I";
            send.tipoTarjeta = fmp_descripcion;
            send.totalTarjeta = $("#txt_montoFormaPago").val();
            send.banderafp = 1;
            $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDepositos/config_adminDepositos.php", data: send,
                success: function (datos) {
                }
            });

            totalPosFormaPagoAgregada = 0;
            $("#btnT" + fmp_descripcion.replace(/\s/g, "") + "").val(parseFloat(totalFormaPago).toFixed(2));
            $("#inputT" + fmp_descripcion.replace(/\s/g, "") + "").val("$" + parseFloat(totalPosFormaPagoAgregada).toFixed(2));

            diferenciaFormaPagoAgregada = (parseFloat(totalFormaPago) - parseFloat(totalPosFormaPagoAgregada));
            $("#diferenciat" + fmp_descripcion.replace(/\s/g, "") + "").val(diferenciaFormaPagoAgregada.toFixed(2));

            /*send={"consultaformaPagoModificadoTarjeta":1};				
             //send.id_User=id_usuario;
             $.ajax({async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_desmontadoCajero.php",data:send,success:function(datos)
             {	totaleS=0;
             for(i=0;i<datos.str;i++)
             {
             //var sinespacios = datos[i]["fmp_descripcion'];
             //ELIMINA ESPACIOS EN BLANCO (AMERICAN EXPRESS) = (AMERICANEXPRESS)//CP
             //formapago = sinespacios.replace( /\s/g, "")
             $("#diferenciat"+fmp_descripcion+"").val(datos[i]['diferencia'].toFixed(2))	
             //$("#hid_controlEfectivo").val(2);										
             }																			
             }	
             })*/

            //$("#dialogTarjetas").dialog( "close" );	
            $("#ModalTecladoNuevaFormaPago").modal("hide");

            id_usuario = 0;
            fn_calculatotalesModificados(id_usuario, ctrc_id);
            fn_totalesIngresados(id_usuario, ctrc_id);
            fn_totalesPos(id_usuario, ctrc_id);
        }
    });
}

function fn_agregarNumerof(valor) {
    var cantidad = $("#txt_montoFormaPago").val();
    if (cantidad.length < 8) {
        //presionamos la primera vez punto
        if ((cantidad == "" || cantidad == 0) && valor == ".") {
            //si escribimos una coma al principio del n�mero
            $("#txt_montoFormaPago").val("0."); //escribimos 0.
            coma = 1;
        } else {
            //continuar escribiendo un n�mero
            if (valor == "." && coma == 0) {
                //si escribimos una coma decimal p�r primera vez
                cantidad = cantidad + "" + valor;
                $("#txt_montoFormaPago").val(cantidad);
                coma = 1; //cambiar el estado de la coma
            } else if (!(valor == "." && coma == 1)) { //si intentamos escribir una segunda coma decimal no realiza ninguna acción, resto de casos: escribir un número del 0 al 9
                var variable = cantidad;
                var indice = 0;
                indice = variable.indexOf(".") + 1;
                if (indice > 0) {
                    variable = variable.substring(indice, variable.length);
                    if (variable.length <= 2) {
                        cantidad = cantidad + "" + valor;
                        $("#txt_montoFormaPago").val(cantidad);
                        fn_focusFormasPago();
                    }
                } else {
                    cantidad = cantidad + "" + valor;
                    $("#txt_montoFormaPago").val(cantidad);
                    fn_focusFormasPago();
                }
            }
        }
    }
    fn_focusFormasPago();
}

function fn_focusFormasPago() {
    $("#txt_montoFormaPago").focus();
}

function fn_eliminarCantidadf() {
    var lc_cantidad = document.getElementById("txt_montoFormaPago").value.substring(0, document.getElementById("txt_montoFormaPago").value.length - 1);

    if (lc_cantidad == "") {
        lc_cantidad = "";
        coma = 0;
    }
    if (lc_cantidad == ".") {
        coma = 0;
    }

    document.getElementById("txt_montoFormaPago").value = lc_cantidad;
    fn_focusFormasPago();
}

function fn_limpiarCantidadf() {
    $("#txt_montoFormaPago").val("");
    coma = 0;
}

function NumCheck(e, field) {
    key = e.keyCode ? e.keyCode : e.which;
    // backspace

    if (key == 8) {
        return true;
    }
    // 0-9

    if (key > 47 && key < 58) {
        if (field.value == "") {
            return true;
        }
        regexp = /.[0-9]{2}$/;
        return !(regexp.test(field.value));
    }
    // .

    if (key == 46) {
        if (field.value == "") {
            return false;
        }
        regexp = /^[0-9]+$/;
        return regexp.test(field.value);
    }
    // other key

    return false;

}

function fn_btnCancelarDepositoModificado() {
    if (banderaAjuste == 1) {
        send = {"eliminaAjusteAgregado": 1};
        send.accion = "DA";
        send.depositoE = $("#hide_codigoDepositoModificado").val();
        $.getJSON("../adminDepositos/config_adminDepositos.php", send, function (datos) {
        });
    }
}

function fn_cerrarBilletes() {
    $("#modal_ingresoNuevoDeposito").modal("show");
}