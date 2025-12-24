var send = {};

$(document).ready(function () {
    cargarModulos();
    cargarFactorMultiplicador();
});

var cargarModulos = function () {
    $('#mdl_rdn_pdd_crgnd').show();
    var html = "<thead><tr class='active'><th width='40%'>Descripción</th><th width='35%'>Abreviatura</th><th width='15%' style='text-align:center'>Nivel</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "cargarModulos";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminModulos/config_modulos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === 1) {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrModulo" + datos[i]['idModulo'] + "' onclick='seleccionarFilaTablaModulos(" + datos[i]['idModulo'] + ")' ondblclick='modificarFilaTablaModulos(" + datos[i]['idModulo'] + ", \"" + datos[i]['descripcion'] + "\", \"" + datos[i]['abreviatura'] + "\", " + datos[i]['nivel'] + ", " + datos[i]['estado'] + ")'><td>" + datos[i]['descripcion'] + "</td><td>" + datos[i]['abreviatura'] + "</td><td class='text-center'>" + datos[i]['nivel'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $('#tblModulos').html(html);
            $('#tblModulos').dataTable({'destroy': true});
            $("#tblModulos_length").hide();
            $('#mdl_rdn_pdd_crgnd').hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            alert("Error: " + thrownError);
        }
    });
};

var seleccionarFilaTablaModulos = function (idModulo) {
    if ($("#tblModulos").find(".success").attr("id") !== "tbrModulo" + idModulo) {
        $("#tblModulos tr").removeClass("success");
        $("#tbrModulo" + idModulo).addClass("success");
        cargarEstados(idModulo);
        $('#btnAgregarNuevoEstado').attr("onclick", "agregarNuevoEstado('" + idModulo + "')");
        $("#lblEstModulo").html("&nbsp;&nbsp;&nbsp;&nbsp;" + $("#tbrModulo" + idModulo + " td").html());
    }
};

var modificarFilaTablaModulos = function (idModulo, descripcion, abreviatura, nivel, estado) {
    $('#mdl_rdn_pdd_crgnd').show();
    $('#mdlModuloTitulo').html(descripcion);
    $('#inpMdlDescripcion').val(descripcion);
    if (estado === 1) {
        $('#inpMdlEstado').prop("checked", true);
    } else {
        $('#inpMdlEstado').prop("checked", false);
    }
    $('#inpMdlAbreviatura').val(abreviatura);
    $('#inpMdlNivel').val(nivel);
    $('#btnMdlGuardarCambios').attr("onclick", "validarParametrosModulo(0, " + idModulo + ")");
    eventoKeyPressModulo(0, idModulo);
    $('#mdlNuevoModulo').modal("show");
    $('#mdl_rdn_pdd_crgnd').hide();
};

var eventoKeyPressModulo = function (accion, idModulo) {
    $('#mdlNuevoModulo').unbind("keypress");
    $('#mdlNuevoModulo').keypress(function (e) {
        if (e.which === 13) {
            e.preventDefault();
            validarParametrosModulo(accion, idModulo);
        }
    });
};

var validarParametrosModulo = function (accion, idModulo) {
    $('#mdl_rdn_pdd_crgnd').show();
    var descripcion = $('#inpMdlDescripcion').val().trim();
    var abreviatura = $('#inpMdlAbreviatura').val();
    var nivel = $('#inpMdlNivel').val();
    var estado = 0;
    if ($("#inpMdlEstado").is(':checked'))
        estado = 1;
    if (descripcion.length >= 4) {
        if (nivel.length > 0) {
            guardarModulo(accion, idModulo, descripcion, abreviatura, nivel, estado);
        } else {
            alertify.error("Ingresar el nivel del módulo.");
        }
    } else {
        alertify.error("La descripción del módulo debe tener por lo menos 4 caracteres.");
    }
    $('#mdl_rdn_pdd_crgnd').hide();
};

var guardarModulo = function (accion, idModulo, descripcion, abreviatura, nivel, estado) {
    var html = "<thead><tr class='active'><th width='40%'>Descripción</th><th width='35%'>Abreviatura</th><th width='15%' style='text-align:center'>Nivel</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.accion = accion;
    send.idModulo = idModulo;
    send.descripcion = descripcion;
    send.abreviatura = abreviatura;
    send.nivel = nivel;
    send.estado = estado;
    send.metodo = "guardarModulo";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminModulos/config_modulos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === 1) {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrModulo" + datos[i]['idModulo'] + "' onclick='seleccionarFilaTablaModulos(" + datos[i]['idModulo'] + ")' ondblclick='modificarFilaTablaModulos(" + datos[i]['idModulo'] + ", \"" + datos[i]['descripcion'] + "\", \"" + datos[i]['abreviatura'] + "\", " + datos[i]['nivel'] + ", " + datos[i]['estado'] + ")'><td>" + datos[i]['descripcion'] + "</td><td>" + datos[i]['abreviatura'] + "</td><td class='text-center'>" + datos[i]['nivel'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
                $('#tblModulos').html(html);
                $('#tblModulos').dataTable({'destroy': true});
                $("#tblModulos_length").hide();
                $('#tblEstadosModulos').html("");
                $("#tblEstadosModulos_info").hide();
                $("#tblEstadosModulos_paginate").hide();
                $("#tblEstadosModulos_filter").hide();
                $('#btnAgregarNuevoEstado').attr("onclick", "agregarNuevoEstado(null)");
                $('#mdlNuevoModulo').modal("hide");
            } else {
                alertify.error("Ya existe un módulo con la descripción " + descripcion);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            $('#mdlNuevoModulo').modal("hide");
            alert("Error: " + thrownError);
        }
    });
};

var agregarNuevoModulo = function () {
    $('#mdlModuloTitulo').html("Nuevo Módulo");
    $('#inpMdlDescripcion').val("");
    $('#inpMdlAbreviatura').val("");
    $('#inpMdlNivel').val(0);
    $('#inpMdlEstado').prop("checked", true);
    $('#btnMdlGuardarCambios').attr("onclick", "validarParametrosModulo(1, 0)");
    eventoKeyPressModulo(1, 0);
    $('#mdlNuevoModulo').modal("show");
};

var cargarEstados = function (idModulo) {
    $('#mdl_rdn_pdd_crgnd').show();
    var html = "<thead><tr class='active'><th width='60%'>Descripción</th><th style='text-align:center' width='25%'>Factor</th><th style='text-align:center' width='15%'>Nivel</th></tr></thead>";
    send = {};
    send.idModulo = idModulo;
    send.metodo = "cargarEstados";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminModulos/config_modulos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += "<tr id='tbrEstado" + datos[i]['idEstado'] + "' onclick='seleccionarFilaTablaEstados(\"" + datos[i]['idEstado'] + "\")' ondblclick='modificarFilaTablaEstados(\"" + datos[i]['idEstado'] + "\", \"" + datos[i]['descripcion'] + "\", \"" + datos[i]['idFactor'] + "\", " + datos[i]['nivel'] + ", " + idModulo + ")'><td>" + datos[i]['descripcion'] + "</td><td class='text-center'>" + datos[i]['factor'] + "</td><td class='text-center'>" + datos[i]['nivel'] + "</td></tr>";
                }
            }
            $('#tblEstadosModulos').html(html);
            $('#tblEstadosModulos').dataTable({'destroy': true});
            $("#tblEstadosModulos_length").hide();
            $('#mdl_rdn_pdd_crgnd').hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            alert("Error: " + thrownError);
        }
    });
};

var seleccionarFilaTablaEstados = function (idEstado) {
    if ($("#tblEstadosModulos").find(".info").attr("id") !== "tbrEstado" + idEstado) {
        $("#tblEstadosModulos tr").removeClass("info");
        $("#tbrEstado" + idEstado).addClass("info");
    }
};

var modificarFilaTablaEstados = function (idEstado, descripcion, factor, nivel, idModulo) {
    $('#mdl_rdn_pdd_crgnd').show();
    $('#mdlEstadoTitulo').html(descripcion);
    $('#inpEstDescripcion').val(descripcion);
    $('#slcEstFactor').val(factor);
    $('#inpEstNivel').val(nivel);
    $('#btnEstGuardarCambios').attr("onclick", "validarParametrosEstado(0, '" + idEstado + "', " + idModulo + ")");
    eventoKeyPressEstado(0, idEstado, idModulo);
    $('#mdlNuevoEstado').modal("show");
    $('#mdl_rdn_pdd_crgnd').hide();
};

var eventoKeyPressEstado = function (accion, idEstado, idModulo) {
    $('#mdlNuevoEstado').unbind("keypress");
    $('#mdlNuevoEstado').keypress(function (e) {
        if (e.which === 13) {
            e.preventDefault();
            validarParametrosEstado(accion, idEstado, idModulo);
        }
    });
};

var validarParametrosEstado = function (accion, idEstado, idModulo) {
    $('#mdl_rdn_pdd_crgnd').show();
    var descripcion = $('#inpEstDescripcion').val().trim();
    var factor = $('#slcEstFactor').val();
    var nivel = $('#inpEstNivel').val();
    if (descripcion.length >= 4) {
        if (factor !== null) {
            if (nivel.length > 0) {
                guardarEstado(accion, idEstado, descripcion, factor, nivel, idModulo);
            } else {
                alertify.error("Ingresar el nivel del estado.");
            }
        } else {
            alertify.error("Seleccionar el factor multiplicador del estado.");
        }
    } else {
        alertify.error("La descripción del estado debe tener por lo menos 4 caracteres.");
    }
    $('#mdl_rdn_pdd_crgnd').hide();
};

var guardarEstado = function (accion, idEstado, descripcion, factor, nivel, idModulo) {
    var html = "<thead><tr class='active'><th width='60%'>Descripción</th><th style='text-align:center' width='25%'>Factor</th><th style='text-align:center' width='15%'>Nivel</th></tr></thead>";
    send = {};
    send.accion = accion;
    send.idEstado = idEstado;
    send.descripcion = descripcion;
    send.factor = factor;
    send.nivel = nivel;
    send.idModulo = idModulo;
    send.metodo = "guardarEstado";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminModulos/config_modulos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += "<tr id='tbrEstado" + datos[i]['idEstado'] + "' onclick='seleccionarFilaTablaEstados(\"" + datos[i]['idEstado'] + "\")' ondblclick='modificarFilaTablaEstados(\"" + datos[i]['idEstado'] + "\", \"" + datos[i]['descripcion'] + "\", \"" + datos[i]['idFactor'] + "\", " + datos[i]['nivel'] + ", " + idModulo + ")'><td>" + datos[i]['descripcion'] + "</td><td class='text-center'>" + datos[i]['factor'] + "</td><td class='text-center'>" + datos[i]['nivel'] + "</td></tr>";
                }
                $('#tblEstadosModulos').html(html);
                $('#tblEstadosModulos').dataTable({'destroy': true});
                $("#tblEstadosModulos_length").hide();
                $('#mdlNuevoEstado').modal("hide");
            } else {
                alertify.error("Ya existe un estado con la descripción " + descripcion);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            $('#mdlNuevoEstado').modal("hide");
            alert("Error: " + thrownError);
        }
    });
};

var agregarNuevoEstado = function (idModulo) {
    if (idModulo !== null) {
        $('#mdlEstadoTitulo').html("Nuevo Estado");
        $('#inpEstDescripcion').val("");
        $('#slcEstFactor').val(0);
        $('#inpEstNivel').val(0);
        $('#btnEstGuardarCambios').attr("onclick", "validarParametrosEstado(1, 0, " + idModulo + ")");
        eventoKeyPressEstado(1, 0, idModulo);
        $('#mdlNuevoEstado').modal("show");
    } else {
        alertify.error("Seleccione el módulo del estado.");
    }
};

var cargarFactorMultiplicador = function () {
    var html = "";
    send = {};
    send.metodo = "cargarFactorMultiplicador";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminModulos/config_modulos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += "<option value='" + datos[i]['idFactor'] + "'>" + datos[i]['factor'] + "</option>";
                }
            }
            $('#slcEstFactor').html(html);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert("Error: " + thrownError);
        }
    });
    
};