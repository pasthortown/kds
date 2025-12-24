$(document).ready(function() {
    cargarCanalesClasificacion();
    cargarListaMedios();
    cargarMenusPorEstado("Activo");
});

function cargarTodosMenus() {
    $('#mdl_rdn_pdd_crgnd').show();
    var html = "<thead><tr class='active'><th class='text-center'>Nombre Men&uacute;</th><th class='text-center'>Nombre en MaxPoint</th><th class='text-center'>Clasificacion</th><th class='text-center'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "cargarTodosMenus";
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminmenu/config_menu.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    if (datos[i]['estado'] === 'Activo') {
                        html += "<tr id='idMenu_" + datos[i]["idMenu"] + "' onclick='seleccionarMenu(\"" + datos[i]['idMenu'] + "\")' ondblclick='modificarMenu(\"" + datos[i]['idMenu'] + "\", \"" + datos[i]['menu'] + "\", \"" + datos[i]['nombreMaxpoint'] + "\", \"" + datos[i]['idClasificacion'] + "\", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['menu'] + "</td><td>" + datos[i]['nombreMaxpoint'] + "</td><td>" + datos[i]['clasificacion'] + "</td><td class='text-center'><input id='checked_estado' type='checkbox' checked='checked' disabled/></td></tr>";
                    } else {
                        html += "<tr id='idMenu_" + datos[i]["idMenu"] + "' onclick='seleccionarMenu(\"" + datos[i]['idMenu'] + "\")' ondblclick='modificarMenu(\"" + datos[i]['idMenu'] + "\", \"" + datos[i]['menu'] + "\", \"" + datos[i]['nombreMaxpoint'] + "\", \"" + datos[i]['idClasificacion'] + "\", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['menu'] + "</td><td>" + datos[i]['nombreMaxpoint'] + "</td><td>" + datos[i]['clasificacion'] + "</td><td class='text-center'><input id='checked_estado' type='checkbox' disabled/></td></tr>";
                    }
                }
            }
            $("#tabladetallemenu").html(html);
            $("#tabladetallemenu").dataTable({ destroy: true });
            $("#tabladetallemenu_length").hide();
            $("#tabladetallemenu_paginate").addClass("col-xs-10");
            $("#tabladetallemenu_info").addClass("col-xs-10");
            $("#tabladetallemenu_length").addClass("col-xs-6");
            $("#madalmodificar").modal("show");
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
        }
    });
    $('#mdl_rdn_pdd_crgnd').hide();
}

var cargarListaMedios = function() {
    var html = "";
    var send = {};
    send.metodo = "cargarListaMedios";
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        accept: "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminmenu/config_menu.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                var html = "<option value='0'>Seleccione una opción.</option>";
                for (var i = 0; i < datos.str; i++) {
                    html += "<option value='" + datos[i]['idMedio'] + "'>" + datos[i]['medio'] + "</option>";
                }
                $("#inMedio").html(html);
            } else {
                alert("No existe la colección 'LISTA DE AGREGADORES Y CANALES");
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("No existe la colección 'LISTA DE AGREGADORES Y CANALES");
        }
    });
};

function cargarMenusPorEstado(estado) {
    $('#mdl_rdn_pdd_crgnd').show();
    var html = "<thead><tr class='active'><th class='text-center'>Nombre Men&uacute;</th><th class='text-center'>Nombre en MaxPoint</th><th class='text-center'>Canal</th><th class='text-center'>Medio</th><th class='text-center'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "cargarMenusPorEstado";
    send.estado = estado;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminmenu/config_menu.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    if (datos[i]['estado'] === 'Activo') {
                        html += "<tr id='idMenu_" + datos[i]["idMenu"] + "' onclick='seleccionarMenu(\"" + datos[i]['idMenu'] + "\")' ondblclick='modificarMenu(\"" + datos[i]['idMenu'] + "\", \"" + datos[i]['menu'] + "\", \"" + datos[i]['nombreMaxpoint'] + "\", \"" + datos[i]['idClasificacion'] + "\", \"" + datos[i]['idMedio'] + "\", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['menu'] + "</td><td>" + datos[i]['nombreMaxpoint'] + "</td><td class='text-center'>" + datos[i]['clasificacion'] + "</td><td class='text-center'>" + datos[i]['medio'] + "</td><td class='text-center'><input id='checked_estado' type='checkbox' checked='checked' disabled/></td></tr>";
                    } else {
                        html += "<tr id='idMenu_" + datos[i]["idMenu"] + "' onclick='seleccionarMenu(\"" + datos[i]['idMenu'] + "\")' ondblclick='modificarMenu(\"" + datos[i]['idMenu'] + "\", \"" + datos[i]['menu'] + "\", \"" + datos[i]['nombreMaxpoint'] + "\", \"" + datos[i]['idClasificacion'] + "\", \"" + datos[i]['idMedio'] + "\", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['menu'] + "</td><td>" + datos[i]['nombreMaxpoint'] + "</td><td class='text-center'>" + datos[i]['clasificacion'] + "</td><td class='text-center'>" + datos[i]['medio'] + "</td><td class='text-center'><input id='checked_estado' type='checkbox' disabled/></td></tr>";
                    }
                }
            }
            $("#tabladetallemenu").html(html);
            $("#tabladetallemenu").dataTable({ destroy: true });
            $("#tabladetallemenu_length").hide();
            $("#tabladetallemenu_paginate").addClass("col-xs-10");
            $("#tabladetallemenu_info").addClass("col-xs-10");
            $("#tabladetallemenu_length").addClass("col-xs-6");
            $("#madalmodificar").modal("show");
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
        }
    });
    $('#mdl_rdn_pdd_crgnd').hide();
}

function seleccionarMenu(idMenu) {
    $("#tabladetallemenu tr").removeClass("success");
    $("#idMenu_" + idMenu).addClass("success");
}

function modificarMenu(idMenu, menu, nombreMaxpoint, idClasificacion, idMedio, estado) {
    if (estado === "Activo") {
        $("#check_activo").prop("checked", true);
    } else {
        $("#check_activo").prop("checked", false);
    }
    if (idMedio != null) {
        $("#inMedio").val(idMedio);
    } else {
        $("#inMedio").val(0);
    }
    $("#nombreMenu").val(menu);
    $("#myModalLabel").text(menu);
    $("#nombreMenuMaxMod").val(nombreMaxpoint);
    $("#inClasificacion").val("idCla_" + idClasificacion);
    $("#modalmodificar").modal("show");
    $("#btnGuardar").attr("onclick", "validarCamposMenu(0, '" + idMenu + "')");
}

function agregarMenu() {
    $("#check_activo").prop("checked", true);
    $("#nombreMenu").val("");
    $("#myModalLabel").text("Nuevo Menu");
    $("#nombreMenuMaxMod").val("");
    $('#inClasificacion').val(0);
    $('#inMedio').val(0);
    $("#modalmodificar").modal("show");
    $("#btnGuardar").attr("onclick", "validarCamposMenu(1, '')");
}

function cargarCanalesClasificacion() {
    var html = "";
    send = {};
    send.metodo = "cargarClasificaciones";
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminmenu/config_menu.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    html += "<option value='idCla_" + datos[i]['idClasificacion'] + "'>" + datos[i]['clasificacion'] + "</option>";
                }
                $("#inClasificacion").html(html);
                $('#selClasificacionduplicacion').html(html);
            }
        }
    });
}

function validarCamposMenu(accion, idMenu) {
    $('#mdl_rdn_pdd_crgnd').show();
    var menu = $("#nombreMenu").val().toUpperCase();
    var nombreMaxpoint = $("#nombreMenuMaxMod").val().toUpperCase();
    var idClasificacion = $("#inClasificacion").val();
    var estado = "";
    if ($("#check_activo").prop("checked"))
        estado = "Activo";
    else
        estado = "Inactivo";
    if (menu.length >= 4) {
        if (nombreMaxpoint.length >= 4) {
            if (idClasificacion !== null) {
                idClasificacion = idClasificacion.substring(6);
                guardarMenu(accion, idMenu, menu, nombreMaxpoint, idClasificacion, estado);
            } else {
                alertify.error("Seleccionar una clasificación.");
            }
        } else {
            alertify.error("El nombre en MaxPoint debe tener por lo menos 4 caracteres.");
        }
    } else {
        alertify.error("El nombre del menú debe tener por lo menos 4 caracteres.");
    }
    $('#mdl_rdn_pdd_crgnd').hide();
}

function guardarMenu(accion, idMenu, menu, nombreMaxpoint, idClasificacion, estado) {
    var html = "<thead><tr class='active'><th class='text-center'>Nombre Men&uacute;</th><th class='text-center'>Nombre en MaxPoint</th><th class='text-center'>Clasificacion</th><th class='text-center'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "guardarMenu";
    send.accion = accion;
    send.idMenu = idMenu;
    send.menu = menu;
    send.nombreMaxpoint = nombreMaxpoint;
    send.idClasificacion = idClasificacion;
    send.idMedio = $('#inMedio option:selected').val();
    send.estado = estado;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminmenu/config_menu.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {

                alertify.success("Datos guardados correctamente.");

                for (var i = 0; i < datos.str; i++) {
                    if (datos[i]['estado'] === 'Activo') {
                    /*    
                    html += "<tr id='idMenu_" + datos[i]["idMenu"] + "' onclick='seleccionarMenu(\"" + datos[i]['idMenu'] + "\")' ondblclick='modificarMenu(\"" + datos[i]['idMenu'] + "\", \"" + datos[i]['menu'] + "\", \"" + datos[i]['nombreMaxpoint'] + "\", \"" + datos[i]['idClasificacion'] + "\", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['menu'] + "</td><td>" + datos[i]['nombreMaxpoint'] + "</td><td class='text-center'>" + datos[i]['clasificacion'] + "</td><td class='text-center'><input id='checked_estado' type='checkbox' checked='checked' disabled/></td></tr>";
                    } else {
                        html += "<tr id='idMenu_" + datos[i]["idMenu"] + "' onclick='seleccionarMenu(\"" + datos[i]['idMenu'] + "\")' ondblclick='modificarMenu(\"" + datos[i]['idMenu'] + "\", \"" + datos[i]['menu'] + "\", \"" + datos[i]['nombreMaxpoint'] + "\", \"" + datos[i]['idClasificacion'] + "\", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['menu'] + "</td><td>" + datos[i]['nombreMaxpoint'] + "</td><td class='text-center'>" + datos[i]['clasificacion'] + "</td><td class='text-center'><input id='checked_estado' type='checkbox' disabled/></td></tr>";
                    }
                    */
                    html += "<tr id='idMenu_" + datos[i]["idMenu"] + "' onclick='seleccionarMenu(\"" + datos[i]['idMenu'] + "\")' ondblclick='modificarMenu(\"" + datos[i]['idMenu'] + "\", \"" + datos[i]['menu'] + "\", \"" + datos[i]['nombreMaxpoint'] + "\", \"" + datos[i]['idClasificacion'] + "\", \"" + datos[i]['idMedio'] + "\", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['menu'] + "</td><td>" + datos[i]['nombreMaxpoint'] + "</td><td class='text-center'>" + datos[i]['clasificacion'] + "</td><td class='text-center'>" + datos[i]['medio'] + "</td><td class='text-center'><input id='checked_estado' type='checkbox' checked='checked' disabled/></td></tr>";
                    } else {
                        html += "<tr id='idMenu_" + datos[i]["idMenu"] + "' onclick='seleccionarMenu(\"" + datos[i]['idMenu'] + "\")' ondblclick='modificarMenu(\"" + datos[i]['idMenu'] + "\", \"" + datos[i]['menu'] + "\", \"" + datos[i]['nombreMaxpoint'] + "\", \"" + datos[i]['idClasificacion'] + "\", \"" + datos[i]['idMedio'] + "\", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['menu'] + "</td><td>" + datos[i]['nombreMaxpoint'] + "</td><td class='text-center'>" + datos[i]['clasificacion'] + "</td><td class='text-center'>" + datos[i]['medio'] + "</td><td class='text-center'><input id='checked_estado' type='checkbox' disabled/></td></tr>";
                    }
                }
                $("#tabladetallemenu").html(html);
                $("#tabladetallemenu").dataTable({ destroy: true });
                $("#tabladetallemenu_length").hide();
                $("#tabladetallemenu_paginate").addClass("col-xs-10");
                $("#tabladetallemenu_info").addClass("col-xs-10");
                $("#tabladetallemenu_length").addClass("col-xs-6");
                $('#opciones_estado label').removeClass("active");
                $('#opt_Activos').addClass("active");
                $("#modalmodificar").modal("hide");
            } else {
                $("#modalmodificar").modal("hide");
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $("#madalmodificar").modal("hide");
            alert(jqXHR);
        }
    });
}

function fn_duplicarmenu() {
    var menu_id = $("#tabladetallemenu").find("tr.success").attr("id");
    var clasificacion = $("#tabladetallemenu").find("tr.success").find("td:nth-child(3)").text();
    if (menu_id) {
        Accion = "C";
        $("#check_activoduplicar").prop("checked", true);
        $("#nombreMenuoriginal").val($("#tabladetallemenu").find("tr.success").find("td:nth-child(1)").text());
        $("#nombreMenuduplicar").val('');
        $("#nombreMenuMaxduplicado").val('');
        $("#selClasificacionduplicacion option").filter(function() {
            return $(this).text() === clasificacion;
        }).prop('selected', true);
        $("#modalduplicar").modal("show");
    } else {
        alertify.error("Seleccionar un men&uacute;");
    }
}

function fn_verificarduplicacion() {
    $('#mdl_rdn_pdd_crgnd').show();
    var idMenuOriginal = $("#tabladetallemenu").find(".success").attr("id").substring(7);
    var nombreMenuDuplicado = $("#nombreMenuduplicar").val().toUpperCase();
    var nombreMenuMaxPoint = $("#nombreMenuMaxduplicado").val().toUpperCase();
    var idClasificacion = $("#selClasificacionduplicacion").val().substring(6);
    var estado = "Inactivo";
    if ($("#check_activoduplicar").prop("checked")) {
        estado = "Activo";
    }
    if (nombreMenuDuplicado.length >= 4) {
        if (nombreMenuMaxPoint.length >= 4) {
            if (idClasificacion !== null) {
                fn_guardarduplicacion(idMenuOriginal, nombreMenuDuplicado, nombreMenuMaxPoint, idClasificacion, estado);
            } else {
                alertify.error("Seleccionar una clasificación.");
            }
        } else {
            alertify.error("El nombre en MaxPoint debe tener por lo menos 4 caracteres.");
        }
    } else {
        alertify.error("El nombre del menú debe tener por lo menos 4 caracteres.");
    }
    $('#mdl_rdn_pdd_crgnd').hide();
}

function fn_guardarduplicacion(idMenuOriginal, nombreMenuDuplicado, nombreMenuMaxPoint, idClasificacion, estado) {
    var html = "<thead><tr class='active'><th class='text-center'>Nombre Men&uacute;</th><th class='text-center'>Nombre en MaxPoint</th><th class='text-center'>Clasificacion</th><th class='text-center'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "fn_guardarduplicacion";
    send.idMenuOriginal = idMenuOriginal;
    send.nombreMenuDuplicado = nombreMenuDuplicado;
    send.nombreMenuMaxPoint = nombreMenuMaxPoint;
    send.idClasificacion = idClasificacion;
    send.estado = estado;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminmenu/config_menu.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                if (datos[i]['estado'] === 'Activo') {
                    html += "<tr id='idMenu_" + datos[i]["idMenu"] + "' onclick='seleccionarMenu(\"" + datos[i]['idMenu'] + "\")' ondblclick='modificarMenu(\"" + datos[i]['idMenu'] + "\", \"" + datos[i]['menu'] + "\", \"" + datos[i]['nombreMaxpoint'] + "\", \"" + datos[i]['idClasificacion'] + "\", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['menu'] + "</td><td>" + datos[i]['nombreMaxpoint'] + "</td><td class='text-center'>" + datos[i]['clasificacion'] + "</td><td class='text-center'><input id='checked_estado' type='checkbox' checked='checked' disabled/></td></tr>";
                } else {
                    html += "<tr id='idMenu_" + datos[i]["idMenu"] + "' onclick='seleccionarMenu(\"" + datos[i]['idMenu'] + "\")' ondblclick='modificarMenu(\"" + datos[i]['idMenu'] + "\", \"" + datos[i]['menu'] + "\", \"" + datos[i]['nombreMaxpoint'] + "\", \"" + datos[i]['idClasificacion'] + "\", \"" + datos[i]['estado'] + "\")'><td>" + datos[i]['menu'] + "</td><td>" + datos[i]['nombreMaxpoint'] + "</td><td class='text-center'>" + datos[i]['clasificacion'] + "</td><td class='text-center'><input id='checked_estado' type='checkbox' disabled/></td></tr>";
                }
            }
            $('#opciones_estado label').removeClass("active");
            $('#opt_Activos').addClass("active");
            $("#modalduplicar").modal("hide");
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $("#modalduplicar").modal("hide");
            alert(jqXHR);
        }
    });
}