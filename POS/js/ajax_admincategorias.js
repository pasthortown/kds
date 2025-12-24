var send = {};

$(document).ready(function () {
    cargarCategoriasPreciosActivos();
});

var cargarCategoriasPreciosActivos = function () {
     $('#mdl_rdn_pdd_crgnd').show();
    var html = "<thead><tr class='active'><th width='35%'>Descripción</th><th width='25%'>Abreviatura</th><th width='15%' style='text-align:center'>Nivel</th><th width='15%'>Integración</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "cargarCategoriasPreciosActivos";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminCategorias/config_categorias.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo") {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrCategoriaPrecios" + datos[i]['idCategoria'] + "' onclick='seleccionarFilaTablaCategoriaPrecios(\"" + datos[i]['idCategoria'] + "\")' ondblclick='modificarFilaTablaCategoriaPrecios(\"" + datos[i]['idCategoria'] + "\", \"" + datos[i]['descripcion'] + "\", \"" + datos[i]['abreviatura'] + "\", " + datos[i]['nivel'] + ", " + datos[i]['idIntegracion'] + ", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['descripcion'] + "</td><td>" + datos[i]['abreviatura'] + "</td><td class='text-center'>" + datos[i]['nivel'] + "</td><td>" + datos[i]['idIntegracion'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $('#tblCategoriasPrecios').html(html);
            $('#tblCategoriasPrecios').dataTable({'destroy': true});
            $("#tblCategoriasPrecios_length").hide();
            $('#mdl_rdn_pdd_crgnd').hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            alert("Error: " + thrownError);
        }
    });
};

var cargarCategoriasPreciosInactivos = function () {
    $('#mdl_rdn_pdd_crgnd').show();
    var html = "<thead><tr class='active'><th width='35%'>Descripción</th><th width='25%'>Abreviatura</th><th width='15%' style='text-align:center'>Nivel</th><th width='15%'>Integración</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "cargarCategoriasPreciosInactivos";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminCategorias/config_categorias.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo") {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrCategoriaPrecios" + datos[i]['idCategoria'] + "' onclick='seleccionarFilaTablaCategoriaPrecios(\"" + datos[i]['idCategoria'] + "\")' ondblclick='modificarFilaTablaCategoriaPrecios(\"" + datos[i]['idCategoria'] + "\", \"" + datos[i]['descripcion'] + "\", \"" + datos[i]['abreviatura'] + "\", " + datos[i]['nivel'] + ", " + datos[i]['idIntegracion'] + ", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['descripcion'] + "</td><td>" + datos[i]['abreviatura'] + "</td><td class='text-center'>" + datos[i]['nivel'] + "</td><td>" + datos[i]['idIntegracion'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $('#tblCategoriasPrecios').html(html);
            $('#tblCategoriasPrecios').dataTable({'destroy': true});
            $("#tblCategoriasPrecios_length").hide();
            $('#mdl_rdn_pdd_crgnd').hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            alert("Error: " + thrownError);
        }
    });
};

var cargarCategoriasPreciosTodos = function () {
     $('#mdl_rdn_pdd_crgnd').show();
    var html = "<thead><tr class='active'><th width='35%'>Descripción</th><th width='25%'>Abreviatura</th><th width='15%' style='text-align:center'>Nivel</th><th width='15%'>Integración</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "cargarCategoriasPreciosTodos";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminCategorias/config_categorias.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo") {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrCategoriaPrecios" + datos[i]['idCategoria'] + "' onclick='seleccionarFilaTablaCategoriaPrecios(\"" + datos[i]['idCategoria'] + "\")' ondblclick='modificarFilaTablaCategoriaPrecios(\"" + datos[i]['idCategoria'] + "\", \"" + datos[i]['descripcion'] + "\", \"" + datos[i]['abreviatura'] + "\", " + datos[i]['nivel'] + ", " + datos[i]['idIntegracion'] + ", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['descripcion'] + "</td><td>" + datos[i]['abreviatura'] + "</td><td class='text-center'>" + datos[i]['nivel'] + "</td><td>" + datos[i]['idIntegracion'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $('#tblCategoriasPrecios').html(html);
            $('#tblCategoriasPrecios').dataTable({'destroy': true});
            $("#tblCategoriasPrecios_length").hide();
            $('#mdl_rdn_pdd_crgnd').hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            alert("Error: " + thrownError);
        }
    });
};

var seleccionarFilaTablaCategoriaPrecios = function (idCategoria) {
    if ($("#tblCategoriasPrecios").find(".success").attr("id") !== "tbrCategoriaPrecios" + idCategoria) {
        $("#tblCategoriasPrecios tr").removeClass("success");
        $("#tbrCategoriaPrecios" + idCategoria).addClass("success");
    }
};

var modificarFilaTablaCategoriaPrecios = function (idCategoria, descripcion, abreviatura, nivel, idIntegracion, estado) {
    $('#mdl_rdn_pdd_crgnd').show();
    $('#mdlCategoriaPreciosTitulo').html(descripcion.trim());
    $('#inpCgpDescripcion').val(descripcion.trim());
    $('#inpCgpAbreviatura').val(abreviatura.trim());
    $('#inpCgpNivel').val(nivel);
    $('#inpCgpIntegracion').val(idIntegracion);
    if (estado === "Activo")
        $("#inpCgpEstado").prop("checked", true);
    else
        $("#inpCgpEstado").prop("checked", false);
    $('#divIntegracion').show();
    $('#divHeredar').hide();
    $('#btnCgpGuardarCambios').attr("onclick", "validarParametrosCategoriaPrecios(0, '" + idCategoria + "')");
    eventoKeyPressCategoriaPrecios(validarParametrosCategoriaPrecios, 0, idCategoria);
    $('#mdlNuevaCategoriaPrecios').modal("show");
    $('#mdl_rdn_pdd_crgnd').hide();
};

var eventoKeyPressCategoriaPrecios = function (funcion, accion, idCategoria) {
    $('#mdlNuevaCategoriaPrecios').unbind("keypress");
    $('#mdlNuevaCategoriaPrecios').keypress(function (e) {
        if (e.which === 13) {
            e.preventDefault();
            funcion(accion, idCategoria);
        }
    });
};

var confirmarHeredar = function (accion, idCategoria) {
    alertify.confirm('¿Está seguro que desea heredar todos los precios de la categoría ' + $('#slcCgpHeredar :selected').html() + '?', function (e) {
        if (e) {
            validarParametrosCategoriaPrecios(accion, idCategoria);
        }
    });
};

var validarParametrosCategoriaPrecios = function (accion, idCategoria) {
    var integracion = "";
    $('#mdl_rdn_pdd_crgnd').show();
    var descripcion = $('#inpCgpDescripcion').val().toUpperCase();
    var abreviatura = $('#inpCgpAbreviatura').val().toUpperCase();
    var nivel = $('#inpCgpNivel').val();
    var idIntegracion = $('#inpCgpIntegracion').val();
    var estado = "Inactivo";
    if ($("#inpCgpEstado").is(':checked'))
        estado = "Activo";
    if (accion > 0) {
        integracion = $('#slcCgpHeredar').val();
        idIntegracion = $('#slcCgpHeredar :selected').attr("integracion");
    }
    if (idIntegracion !== "null") {
        if (idIntegracion.length > 0) {
            if (descripcion.length >= 4) {
                if (abreviatura.length > 0) {
                    if (abreviatura.length <= 3) {
                        guardarCategoriaPrecios(accion, idCategoria, descripcion, abreviatura, nivel, integracion, idIntegracion, estado);
                    } else {
                        alertify.error("La abreviatura de la categoría no puede tener más de 3 caracteres");
                    }
                } else {
                    alertify.error("Ingresar la abreviatura de la categoría");
                }
            } else {
                alertify.error("La descripción de la categoría debe tener por lo menos 4 caracteres");
            }
        } else {
            alertify.error("El registro no puede ser modificado debido a que la información no está sincronizada con Sistema Gerente");
        }
    } else {
        alertify.error("No puede agregarse el registro debido a que la información de " + $('#slcCgpHeredar :selected').html() + " no está sincronizada con Sistema Gerente");
    }
    $('#mdl_rdn_pdd_crgnd').hide();
};

var guardarCategoriaPrecios = function (accion, idCategoria, descripcion, abreviatura, nivel, integracion, idIntegracion, estado) {
    var html = "<thead><tr class='active'><th width='35%'>Descripción</th><th width='25%'>Abreviatura</th><th width='15%' style='text-align:center'>Nivel</th><th width='15%'>Integración</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "guardarCategoriaPrecios";
    send.accion = accion;
    send.idCategoria = idCategoria;
    send.descripcion = descripcion;
    send.abreviatura = abreviatura;
    send.nivel = nivel;
    send.integracion = integracion;
    send.idIntegracion = idIntegracion;
    send.estado = estado;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminCategorias/config_categorias.php", data: send,
        success: function (datos) {
            if (datos.estado > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo") {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrCategoriaPrecios" + datos[i]['idCategoria'] + "' onclick='seleccionarFilaTablaCategoriaPrecios(\"" + datos[i]['idCategoria'] + "\")' ondblclick='modificarFilaTablaCategoriaPrecios(\"" + datos[i]['idCategoria'] + "\", \"" + datos[i]['descripcion'] + "\", \"" + datos[i]['abreviatura'] + "\", " + datos[i]['nivel'] + ", " + datos[i]['idIntegracion'] + ", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['descripcion'] + "</td><td>" + datos[i]['abreviatura'] + "</td><td class='text-center'>" + datos[i]['nivel'] + "</td><td>" + datos[i]['idIntegracion'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
                $('#tblCategoriasPrecios').html(html);
                $('#opcionesCategoriasPrecios label').removeClass("active");
                $('#lblOpcionActivos').addClass("active");
                $('#tblCategoriasPrecios').dataTable({'destroy': true});
                $('#tblCategoriasPrecios_length').hide();
                $('#mdlNuevaCategoriaPrecios').modal("hide");
                alertify.success("Transación realizada exitosamente");
            } else {
                alertify.error(datos.mensaje);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            $("#mdlNuevaCategoriaPrecios").modal("hide");
          
              alertify.error("Error: " + thrownError);
        }
    });
};

var agregarNuevaCategoriaPrecios = function () {
    $('#mdlCategoriaPreciosTitulo').html("Nueva Categoría de Precios");
    $('#inpCgpDescripcion').val("");
    $('#inpCgpAbreviatura').val("");
    $('#inpCgpNivel').val(0);
    $('#inpCgpIntegracion').val("");
    $('#inpCgpEstado').prop("checked", true);
    cargarCategoriasPreciosSelect();
    $('#divIntegracion').hide();
    $('#divHeredar').show();
    $('#btnCgpGuardarCambios').attr("onclick", "confirmarHeredar(1, 0)");
    eventoKeyPressCategoriaPrecios(confirmarHeredar, 1, 0);
    $('#mdlNuevaCategoriaPrecios').modal("show");
};

var cargarCategoriasPreciosSelect = function () {
    var html = "";
    send = {};
    send.metodo = "cargarCategoriasPreciosSelect";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminCategorias/config_categorias.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += "<option value='" + datos[i]['idCategoria'] + "' integracion=\"" + datos[i]['idIntegracion'] + "\">" + datos[i]['descripcion'] + "</option>";
                }
            }
            $('#slcCgpHeredar').html(html);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert("Error: " + thrownError);
        }
    });
};