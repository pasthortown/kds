$(document).ready(function () {
    cargarMotivosIngresosEgresosCajaActivos();
});

var cargarMotivosIngresosEgresosCajaActivos = function () {
    $("#mdl_rdn_pdd_crgnd").show();
    var html = "<thead><tr class='active'><th width='55%' class='text-center'>Concepto</th><th width='15%' style='text-align:center'>Signo</th><th width='15%' style='text-align:center'>Nivel</th><th width='15%' style='text-align:center'>Activo</th></tr></thead>";
    var send = {};
    send.metodo = "cargarMotivosIngresosEgresosCajaActivos";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminMotivoIngresosEgresosCaja/config_motivoIngresosEgresosCaja.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += "<tr id='tbrMotivosIngresosEgresosCaja" + datos[i]['idMotivoIngresosEgresosCaja'] + "' onclick='seleccionarFilaTablaMotivosIngresosEgresosCaja(\"" + datos[i]['idMotivoIngresosEgresosCaja'] + "\")' ondblclick='modificarFilaTablaMotivosIngresosEgresosCaja(\"" + datos[i]['idMotivoIngresosEgresosCaja'] + "\", \"" + datos[i]['concepto'] + "\", \"" + datos[i]['signo'].trim() + "\", " + datos[i]['nivel'] + ", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['concepto'] + "</td><td class='text-center'>" + datos[i]['signo'].trim() + "</td><td class='text-center'>" + datos[i]['nivel'] + "</td><td class='text-center'><input type='checkbox' checked='checked' disabled='disabled'></td></tr>";
                }
            } else {
                alertify.error("No existen motivos de ingresos y egresos de caja activos");
            }
            $("#tblMotivosIngresosEgresosCaja").html(html);
            $("#mdl_rdn_pdd_crgnd").hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#mdl_rdn_pdd_crgnd").hide();
            alert("Error: " + thrownError);
        }
    });
};

var cargarMotivosIngresosEgresosCajaInactivos = function () {
    $("#mdl_rdn_pdd_crgnd").show();
    var html = "<thead><tr class='active'><th width='55%' class='text-center'>Concepto</th><th width='15%' style='text-align:center'>Signo</th><th width='15%' style='text-align:center'>Nivel</th><th width='15%' style='text-align:center'>Activo</th></tr></thead>";
    var send = {};
    send.metodo = "cargarMotivosIngresosEgresosCajaInactivos";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminMotivoIngresosEgresosCaja/config_motivoIngresosEgresosCaja.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += "<tr id='tbrMotivosIngresosEgresosCaja" + datos[i]['idMotivoIngresosEgresosCaja'] + "' onclick='seleccionarFilaTablaMotivosIngresosEgresosCaja(\"" + datos[i]['idMotivoIngresosEgresosCaja'] + "\")' ondblclick='modificarFilaTablaMotivosIngresosEgresosCaja(\"" + datos[i]['idMotivoIngresosEgresosCaja'] + "\", \"" + datos[i]['concepto'] + "\", \"" + datos[i]['signo'].trim() + "\", " + datos[i]['nivel'] + ", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['concepto'] + "</td><td class='text-center'>" + datos[i]['signo'].trim() + "</td><td class='text-center'>" + datos[i]['nivel'] + "</td><td class='text-center'><input type='checkbox' disabled='disabled'></td></tr>";
                }
            } else {
                alertify.error("No existen motivos de ingresos y egresos de caja inactivos");
            }
            $("#tblMotivosIngresosEgresosCaja").html(html);
            $("#mdl_rdn_pdd_crgnd").hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#mdl_rdn_pdd_crgnd").hide();
            alert("Error: " + thrownError);
        }
    });
};

var cargarMotivosIngresosEgresosCajaTodos = function () {
    $("#mdl_rdn_pdd_crgnd").show();
    var html = "<thead><tr class='active'><th width='55%' class='text-center'>Concepto</th><th width='15%' style='text-align:center'>Signo</th><th width='15%' style='text-align:center'>Nivel</th><th width='15%' style='text-align:center'>Activo</th></tr></thead>";
    var send = {};
    send.metodo = "cargarMotivosIngresosEgresosCajaTodos";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminMotivoIngresosEgresosCaja/config_motivoIngresosEgresosCaja.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === 'Activo') {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrMotivosIngresosEgresosCaja" + datos[i]['idMotivoIngresosEgresosCaja'] + "' onclick='seleccionarFilaTablaMotivosIngresosEgresosCaja(\"" + datos[i]['idMotivoIngresosEgresosCaja'] + "\")' ondblclick='modificarFilaTablaMotivosIngresosEgresosCaja(\"" + datos[i]['idMotivoIngresosEgresosCaja'] + "\", \"" + datos[i]['concepto'] + "\", \"" + datos[i]['signo'].trim() + "\", " + datos[i]['nivel'] + ", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['concepto'] + "</td><td class='text-center'>" + datos[i]['signo'].trim() + "</td><td class='text-center'>" + datos[i]['nivel'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            } else {
                alertify.error()("No existen motivos de ingresos y egresos de caja");
            }
            $("#tblMotivosIngresosEgresosCaja").html(html);
            $("#mdl_rdn_pdd_crgnd").hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#mdl_rdn_pdd_crgnd").hide();
            alert("Error: " + thrownError);
        }
    });
};

var seleccionarFilaTablaMotivosIngresosEgresosCaja = function (idMotivoIngresosEgresosCaja) {
    if ($("#tblMotivosIngresosEgresosCaja").find(".success").attr("id") !== "tbrMotivosIngresosEgresosCaja" + idMotivoIngresosEgresosCaja) {
        $("#tblMotivosIngresosEgresosCaja tr").removeClass("success");
        $("#tbrMotivosIngresosEgresosCaja" + idMotivoIngresosEgresosCaja).addClass("success");
    }
};

var modificarFilaTablaMotivosIngresosEgresosCaja = function (idMotivoIngresosEgresosCaja, concepto, signo, nivel, estado) {
    $("#mdl_rdn_pdd_crgnd").show();
    $("#mdlMotivosIngresosEgresosCajaTitulo").html(concepto);
    $("#inpMieConcepto").val(concepto);
    $("#slcMieSigno").val(signo);
    $("#inpMieNivel").val(nivel);
    if (estado === "Activo") {
        $("#inpMieEstado").prop("checked", true);
    } else {
        $("#inpMieEstado").prop("checked", false);
    }
    $("#btnMieGuardarCambios").attr("onclick", "validarParametrosMotivoIngresosEgresosCaja(0, '" + idMotivoIngresosEgresosCaja + "')");
    $("#mdlNuevoMotivoIngresosEgresosCaja").modal("show");
    $("#mdl_rdn_pdd_crgnd").hide();
};

var validarParametrosMotivoIngresosEgresosCaja = function (accion, idMotivoIngresosEgresosCaja) {
    $("#mdl_rdn_pdd_crgnd").show();
    var concepto = $("#inpMieConcepto").val();
    var signo = $("#slcMieSigno").val();
    var nivel = $("#inpMieNivel").val();
    var estado = "Inactivo";
    if ($("#inpMieEstado").is(':checked')) {
        estado = "Activo";
    }
    if (concepto.length > 3) {
        if (signo !== null) {
            guardarMotivoIngresosEgresosCaja(accion, idMotivoIngresosEgresosCaja, concepto, signo, nivel, estado);
        } else {
            alertify.error("Escoger un signo para el motivo de ingresos y egresos de caja");
        }
    } else {
        alertify.error("El concepto del motivo de ingresos y egresos de caja no puede tener menos de 4 caracteres");
    }
    $("#mdl_rdn_pdd_crgnd").hide();
};

var guardarMotivoIngresosEgresosCaja = function (accion, idMotivoIngresosEgresosCaja, concepto, signo, nivel, estado) {
    var html = "<thead><tr class='active'><th width='55%' class='text-center'>Concepto</th><th width='15%' style='text-align:center'>Signo</th><th width='15%' style='text-align:center'>Nivel</th><th width='15%' style='text-align:center'>Activo</th></tr></thead>";
    var send = {};
    send.accion = accion;
    send.idMotivoIngresosEgresosCaja = idMotivoIngresosEgresosCaja;
    send.concepto = concepto;
    send.signo = signo;
    send.nivel = nivel;
    send.estado = estado;
    send.metodo = "guardarMotivoIngresosEgresosCaja";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminMotivoIngresosEgresosCaja/config_motivoIngresosEgresosCaja.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += "<tr id='tbrMotivosIngresosEgresosCaja" + datos[i]['idMotivoIngresosEgresosCaja'] + "' onclick='seleccionarFilaTablaMotivosIngresosEgresosCaja(\"" + datos[i]['idMotivoIngresosEgresosCaja'] + "\")' ondblclick='modificarFilaTablaMotivosIngresosEgresosCaja(\"" + datos[i]['idMotivoIngresosEgresosCaja'] + "\", \"" + datos[i]['concepto'] + "\", \"" + datos[i]['signo'].trim() + "\", " + datos[i]['nivel'] + ", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['concepto'] + "</td><td class='text-center'>" + datos[i]['signo'].trim() + "</td><td class='text-center'>" + datos[i]['nivel'] + "</td><td class='text-center'><input type='checkbox' checked='checked' disabled='disabled'></td></tr>";
                }
            } else {
                alertify.error("No existen motivos de ingresos y egresos de caja activos");
            }
            $("#tblMotivosIngresosEgresosCaja").html(html);
            $('#opcionesMotivosIngresosEgresosCaja label').removeClass("active");
            $('#lblMieOpcionActivos').addClass("active");
            $("#mdlNuevoMotivoIngresosEgresosCaja").modal("hide");
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert("Error: " + thrownError);
        }
    });
};

var agregarNuevoMotivoIngresosEgresosCaja = function () {
    $("#mdl_rdn_pdd_crgnd").show();
    $("#mdlMotivosIngresosEgresosCajaTitulo").html("Nuevo Motivo de Ingresos y Egresos de Caja");
    $("#inpMieConcepto").val("");
    $("#slcMieSigno").val("");
    $("#inpMieNivel").val(0);
    $("#inpMieEstado").prop("checked", true);
    $("#btnMieGuardarCambios").attr("onclick", "validarParametrosMotivoIngresosEgresosCaja(1, '')");
    $("#mdlNuevoMotivoIngresosEgresosCaja").modal("show");
    $("#mdl_rdn_pdd_crgnd").hide();
};