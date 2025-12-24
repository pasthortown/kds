/*
FECHA CREACION   : 16/07/2016 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion de promociones
*/

/* global alertify */
var seleccionarRestaurante = "";

$(document).ready(function() {

    fn_btn("agregar", 1);
    fn_detallePromociones(1);

});

/*===================================================*/
/*FUNCION PARA TRAER LOS BOTONES DE ADMINISTRACION   */
/*===================================================*/
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

function fn_agregarPromocion() {
    $("#modal").modal("show");
    $("#pestanas li").removeClass("active");
    $('#tabDetalle').addClass("active");
    $("#tabContenedor div").removeClass("active");
    $('#tab_detalle').addClass("active");
    $("#lbl_descripcion").text("Nueva Promoción");
    $("#check_isactive").prop("checked", true);
    $("#check_isactive").prop("disabled", true);
    $("#FechaInicial").val("");
    $("#FechaFinal").val("");
    $("#txt_descripcion").val("");
    $("#txt_contenido").val("");
    $("#FechaInicial").daterangepicker({ minDate: moment(), singleDatePicker: true, format: "DD/MM/YYYY", drops: "down" }, function(start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });
    $("#FechaFinal").daterangepicker({ singleDatePicker: true, format: "DD/MM/YYYY", drops: "down" }, function(start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    seleccionarRestaurante = "";
    fn_setearListaRestaurantes();
    $("#rdo_restaurantes label").removeClass("active");
    $('#rdo_localizacion').addClass("active");
    $("#chck_todos").prop("checked", false);
    $("#check_etiqueta").prop("checked", false);
    $("#txt_etiqueta").prop("disabled", true);
    $("#txt_etiqueta").val("");
    fn_cargarRestaurante(0);

    $("#btn_accion").html('<button type="button" class="btn btn-primary" onclick="fn_guardarPromocion(1);">Aceptar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');
}

function fn_cargarRestaurante(opcion) {
    $("#hdn_checkedTodos").val(opcion);
    var send;
    var cargarRestaurante = { "cargarRestaurante": 1 };
    var Accion = "R";
    var html = "";
    send = cargarRestaurante;
    send.accion = Accion;
    send.localizacion = opcion;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminPromociones/config_adminPromociones.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html = html + '<a class="list-group-item"><input id="cbx_idRestaurante_' + datos[i]['IDRestaurante'] + '" name="cbx_restaurante" onclick="fn_seleccionarRestaurante(' + datos[i]['IDRestaurante'] + ')" value="' + datos[i]['IDRestaurante'] + '" type="checkbox">&nbsp; ' + datos[i]['restaurante'] + '</a>';
                }
                $("#lst_restaurantes").html(html);
                fn_marcarRestaurantesSeleccionados();
            } else {
                html = html + '<a class="list-group-item">"No existen Tiendas"</a>';
                $("#lst_restaurantes").html(html);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_seleccionarTodosRestaurantes() {
    seleccionarRestaurante = "";
    if ($("#chck_todos").is(":checked")) {
        $(":input[name=cbx_restaurante]").each(function() {
            if (seleccionarRestaurante.indexOf($(this).val() + "_") < 0) {
                seleccionarRestaurante = seleccionarRestaurante + $(this).val() + "_";
            }
            $("#cbx_idRestaurante_" + $(this).val()).prop("checked", "checked");
        });
    } else {
        $(":input[name=cbx_restaurante]").each(function() {
            if (seleccionarRestaurante.indexOf($(this).val() + "_") >= 0) {
                seleccionarRestaurante = seleccionarRestaurante.replace($(this).val() + "_", "");
            }
            $("#cbx_idRestaurante_" + $(this).val()).prop("checked", false);
        });
    }
}

function fn_seleccionarRestaurante(IDRestaurante) {
    if ($("#cbx_idRestaurante_" + IDRestaurante).is(":checked")) {
        seleccionarRestaurante = seleccionarRestaurante + IDRestaurante + "_";
    } else {
        seleccionarRestaurante = seleccionarRestaurante.replace(IDRestaurante + "_", "");
    }
}

function fn_marcarRestaurantesSeleccionados() {
    var argumento = seleccionarRestaurante.split("_");
    for (var i = 0; i < argumento.length; i++) {
        $("#cbx_idRestaurante_" + argumento[i]).prop("checked", "checked");
    }
}

function fn_setearListaRestaurantes() {
    $(":input[name=cbx_restaurante]:checked").each(function() {
        $("#cbx_idRestaurante_" + $(this).val()).prop("checked", false);
    });
}

function fn_guardarPromocion(opcion) {
    fn_cargando(1);
    var send;
    var guardarPromocion = { "guardarPromocion": 1 };
    if (opcion === 1) { var Accion = "I"; } else { var Accion = "U"; }
    var fechaInicio = $("#FechaInicial").val();
    var fechaFin = $("#FechaFinal").val();
    var descripcion = $("#txt_descripcion").val();
    var contenido = $("#txt_contenido").val();
    var estado = $("#check_isactive").is(":checked");
    var IDPromocion = "";
    var aplicaPara;
    var checkActivo = $("#check_etiqueta").is(":checked");
    var mostrarEtiqueta = $("#txt_etiqueta").val();
    var checkMostarEtiqueta;

    if (fechaInicio === "") {
        alertify.error("<b>Alerta:</b> La fecha inicial es obligatoria.");
        fn_cargando(0);
        $("#FechaInicial").focus();
        return false;
    } else if (fechaFin === "") {
        alertify.error("<b>Alerta:</b> La fecha final es obligatoria.");
        fn_cargando(0);
        $("#FechaFinal").focus();
        return false;
    } else if (descripcion === "") {
        alertify.error("<b>Alerta:</b> La descripci&oacute;n para su promoci&oacute;n es obligatoria.");
        fn_cargando(0);
        $("#txt_descripcion").focus();
        return false;
    } else if (contenido === "") {
        alertify.error("<b>Alerta:</b> El contenido de su promoci&oacute;n es obligatorio.");
        fn_cargando(0);
        $("#txt_contenido").focus();
        return false;
    }

    if (Accion === "I") {
        estado = 1;
        IDPromocion = "0";
    } else {
        if (estado === true) {
            estado = 1;
        } else {
            estado = 0;
        }
        IDPromocion = $("#hdn_IDPromocion").val();
    }

    aplicaPara = '';

    if (checkActivo == true) {
        if (mostrarEtiqueta === "") {
            alertify.error("<b>Alerta:</b> El campo Mostrar Etiqueta es obligatorio.");
            fn_cargando(0);
            $("#txt_etiqueta").focus();
            return false;
        }

        checkMostarEtiqueta = 1;

    } else if (checkActivo == false) {
        mostrarEtiqueta = '';
        checkMostarEtiqueta = 0;
    }

    send = guardarPromocion;
    send.accion = Accion;
    send.fechaInicio = fechaInicio;
    send.fechaFin = fechaFin;
    send.descripcion = descripcion;
    send.contenido = contenido;
    send.IDRestaurante = seleccionarRestaurante;
    send.estado = estado;
    send.IDPromocion = IDPromocion;
    send.aplicaPara = aplicaPara;
    send.mostrarEtiqueta = mostrarEtiqueta;
    send.checkMostarEtiqueta = checkMostarEtiqueta;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminPromociones/config_adminPromociones.php",
        data: send,
        success: function(datos) {
            alertify.success("Datos guardados correctamente.");
            $("#modal").modal("hide");
            fn_setearListaRestaurantes();
            fn_labelEstados(estado);
            fn_cargando(0);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
            alertify.error("Error al momento de guardar.");
            fn_cargando(0);
        }
    });
}

function fn_detallePromociones(isactive) {
    var send;
    var cargarPromociones = { "cargarPromociones": 1 };
    var Accion = "P";
    var html = '<thead><tr class="active"><th class="text-center">Promoci&oacute;n</th><th class="text-center">Fecha Inicio</th><th class="text-center">Fecha Fin</th><th class="text-center">Contenido</th><th class="text-center">Activo</th></tr></thead>';
    send = cargarPromociones;
    send.accion = Accion;
    send.estado = isactive;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminPromociones/config_adminPromociones.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {

                    var estado = (datos[i]["estado"]);

                    html += '<tr id="' + i + "idPromocion" + '" onclick="fn_seleccionarPromocion(' + i + ')" ondblclick="fn_seleccionModificar(' + i + ',\'' + datos[i]["IDPromocion"] + '\',\'' + datos[i]["fechaInicio"] + '\',\'' + datos[i]["fechaFin"] + '\',\'' + datos[i]["pro_nombre"] + '\',\'' + datos[i]["contenido"] + '\',\'' + datos[i]["estado"] + '\',\'' + datos[i]["checkMostrarEtiqueta"] + '\',\'' + datos[i]["mostrarEtiqueta"] + '\')" class="text-center"><td>' + datos[i]["pro_nombre"] + '</td><td>' + datos[i]["fechaInicio"] + '</td><td>' + datos[i]["fechaFin"] + '</td><td>' + datos[i]["contenido"] + '</td>';

                    if (estado === "Inactivo") {
                        html += '<td><input type="checkbox" value="1" disabled/></td>';
                    } else {
                        html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    }
                    html += '</tr>';
                }
                $("#tabla_detallePromociones").html(html);
                $("#tabla_detallePromociones").dataTable({ "destroy": true });
                $("#tabla_detallePromociones_length").hide();
                $("#tabla_detallePromociones_paginate").addClass("col-xs-10");
                $("#tabla_detallePromociones_info").addClass("col-xs-10");
                $("#tabla_detallePromociones_length").addClass("col-xs-6");
            } else {
                html = html + '<tr><th colspan="5" class="text-center">No existen registros.</th></tr>';
                $("#tabla_detallePromociones").html(html);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_seleccionarPromocion(fila) {
    $("#tabla_detallePromociones tr").removeClass("success");
    $("#" + fila + 'idPromocion' + "").addClass("success");
}

function fn_seleccionModificar(fila, IDPromocion, fechaInicio, fechaFin, nombre, contenido, estado, checkEtiqueta, etiqueta) {
    var saltoLinea = contenido.replace(/<br>/g, "\n");
    var saltoLineaEtiqueta = etiqueta.replace(/<br>/g, "\n");

    $("#hdn_IDPromocion").val(IDPromocion);
    $("#modal").modal("show");
    $("#pestanas li").removeClass("active");
    $('#tabDetalle').addClass("active");
    $("#tabContenedor div").removeClass("active");
    $('#tab_detalle').addClass("active");
    $("#lbl_descripcion").text(nombre);
    $("#check_isactive").prop("checked", true);
    $("#check_isactive").prop("disabled", false);
    $("#FechaInicial").val(fechaInicio);
    $("#FechaFinal").val(fechaFin);
    $("#txt_descripcion").val(nombre);
    $("#txt_contenido").val(saltoLinea);

    if (estado === "Inactivo") {
        $("#check_isactive").prop("checked", false);
    } else {
        $("#check_isactive").prop("checked", true);
    }

    $("#FechaInicial").daterangepicker({ minDate: moment(), singleDatePicker: true, format: "DD/MM/YYYY", drops: "down" }, function(start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });
    $("#FechaFinal").daterangepicker({ singleDatePicker: true, format: "DD/MM/YYYY", drops: "down" }, function(start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    seleccionarRestaurante = "";
    fn_setearListaRestaurantes();
    $("#rdo_restaurantes label").removeClass("active");
    $('#rdo_localizacion').addClass("active");
    $("#chck_todos").prop("checked", false);

    if (checkEtiqueta == 0) {
        $("#check_etiqueta").prop("checked", false);
        $("#txt_etiqueta").prop("disabled", true);
    } else {
        $("#check_etiqueta").prop("checked", true);
        $("#txt_etiqueta").prop("disabled", false);
    }

    $("#txt_etiqueta").val(saltoLineaEtiqueta);

    fn_cargarRestaurante(0);
    fn_cargarPromocionRestaurantes(IDPromocion);
    $("#btn_accion").html('<button type="button" class="btn btn-primary" onclick="fn_guardarPromocion(2);">Aceptar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');
}

function fn_cargarPromocionRestaurantes(IDPromocion) {
    var send;
    var cargarPromocionRestaurantes = { "cargarPromocionRestaurantes": 1 };
    var Accion = "T";
    send = cargarPromocionRestaurantes;
    send.accion = Accion;
    send.IDPromocion = IDPromocion;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminPromociones/config_adminPromociones.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    seleccionarRestaurante = seleccionarRestaurante + datos[i]["IDRestaurante"] + "_";
                    $("#cbx_idRestaurante_" + datos[i]["IDRestaurante"]).prop("checked", "checked");
                }
            } else {
                fn_setearListaRestaurantes();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_labelEstados(estado) {
    if (estado === 1) {
        $("#opciones_estados label").removeClass("active");
        $("#opcion_1").addClass("active");
        fn_detallePromociones(1);
    } else if (estado === 0) {
        $("#opciones_estados label").removeClass("active");
        $("#opcion_2").addClass("active");
        fn_detallePromociones(2);
    } else {
        $("#opciones_estados label").removeClass("active");
        $("#opcion_3").addClass("active");
        fn_detallePromociones(0);
    }
}

function fn_mostrarEtiqueta() {
    var checkActivo;

    checkActivo = $("#check_etiqueta").is(":checked");

    if (checkActivo == true) {
        $("#txt_etiqueta").prop("disabled", false);
    } else if (checkActivo == false) {
        $("#txt_etiqueta").prop("disabled", true);
        $("#txt_etiqueta").val("");
    }

}