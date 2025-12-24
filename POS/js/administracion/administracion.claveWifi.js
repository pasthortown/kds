/* global alertify */

cargando(false);

$.fn.editable.defaults.ajaxOptions = {type: "POST"};

var listaAnios = [];

$(document).ready(function () {
    cargarAnios();
    cargarRestaurantes();
    cargarRestaurantesWifi();
});

var cargarAnios = function () {
    var html = "";
    //El 2018 inicio el desarrollo de este modulo
    var inicio = 2018;
    var date = new Date();
    var anioActual = date.getFullYear();
    while (inicio < anioActual + 4) {
        listaAnios.push(inicio);
        html = html + "<label class='btn btn-default' onclick='cargarClavesPorAnio(\"" + inicio + "\")'><h6><input id='optionsClas' type='radio' name='options_checks' value='" + inicio + "'>" + inicio + "</h6></label>";
        inicio++;
    }
    $("#filtroAnio").html(html);
};

var cargarClavesPorAnio = function (anio) {
    var send = {};
    var html = "";
    $("#tblSemanas tbody").html(html);
    send.metodo = "cargarClavesPorSemanasPorAnio";
    send.anio = anio;
    $.ajax({async: false, type: "POST", dataType: "json", Accept: "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminClaveWifi/servicios.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html = "<tr><td>" + datos[i]['descripcion'] + "</td><td>" + datos[i]['desde'] + "</td><td>" + datos[i]['hasta'] + "</td><td id=\"" + datos[i]['desde'] + "\">" + datos[i]['clave'] + "</td></tr>";
                    $("#tblSemanas tbody").append(html);
                    $('#' + datos[i]['desde']).editable({
                        type: 'text',
                        url: '../adminClaveWifi/servicios.php',
                        pk: 'modificarClavePorSemana',
                        params: {
                            endDate: datos[i]['hasta']
                        },
                        title: 'Clave de la Semana',
                        ajaxOptions: {
                            type: 'POST',
                            dataType: 'json'
                        },
                        success: function (response, newValue) {
                            if (response.estado > 0) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    });
                }
            }
        }
    });
};

function cargarRestaurantes() {
    var html = "";
    var send = {};
    send.metodo = "cargarRestaurantes";
    $.ajax({async: false, type: "POST", dataType: "json", Accept: "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminClaveWifi/servicios.php", data: send,
        success: function (datos) {
            if (datos !== null) {
                for (var i = 0; i < datos.str; i++) {
                    html += "<a class='list-group-item'><input id='cbx" + datos[i]["idRestaurante"] + "' value='" + datos[i]["idRestaurante"] + "' type='checkbox'>&nbsp;&nbsp;" + datos[i]["descripcion"] + "</a>";
                }
                $("#listaRestaurantes").html(html);
            }
        }
    });
}

function obtenerRestaurantesMarcados() {
    var restaurantes = "";
    $("#listaRestaurantes input:checked").each(function () {
        restaurantes += $(this).val() + ",";
    });
    return restaurantes.slice(0, -1);
}

function cargarRestaurantesWifi() {
    cargando(true);
    var send = {};
    send.metodo = "cargarRestaurantesWifi";
    $.ajax({async: false, type: "POST", dataType: "json", Accept: "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminClaveWifi/servicios.php", data: send,
        success: function (datos) {
            if (datos !== null) {
                $("#listaRestaurantes input").each(function () {
                    $(this).prop("checked", false);
                });
                for (var i = 0; i < datos.str; i++) {
                    $("#cbx" + datos[i]["idRestaurante"]).prop("checked", true);
                }
            }
        }
    });
    cargando(false);
}

function guardarRestaurantesWifi() {
    var send = {};
    send.metodo = "guardarRestaurantesWifi";
    send.restaurantes = obtenerRestaurantesMarcados();
    $.ajax({async: false, type: "POST", dataType: "json", Accept: "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminClaveWifi/servicios.php", data: send,
        success: function (datos) {
            if (datos !== null) {
                if (datos.resp === 1) {
                    alertify.success("Restaurantes actualizados correctamente");
                } else {
                    alertify.error("Error al actualizar los restaurantes!");
                }
            }
        }
    });
}

function cargando(cargando) {
    if (cargando) {
        $("#mdl_rdn_pdd_crgnd").show();
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
    }
}