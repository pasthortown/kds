/* global alertify, bootbox */

var tablaProductos;
var tablaRestaurantes;
var tablaFormaPago;
var tipoAmbiente = "";
var aplicaPlan = 0;
var puntosRide = 0;
$(document).ready(function () {
    tipoAmbiente = $("#inTipoAmbiente").val();
    aplicaPlan = $("#inAplicaPlan").val();
    $('#inPuntosRide').prop("checked", ($("#inImprimePuntosRide").val() === "1") ? true : false);
    cargarConfiguracionCadena();
    cargarProductos();
    cargarRestaurantes();
    cargarFormaPago();
    $('#btnListo').click(function () {
        $("#frmConfiguracionesCadena").find("input").attr("disabled", "disabled");
        $("#frmConfiguracionesCadena").find("textarea").attr("disabled", "disabled");
        $("#cntCancelar").css("display", "none");
        $("#cntEditar").css("display", "block");
    });
    $('#btnEditarConfiguracionCadena').click(function () {
        $("#frmConfiguracionesCadena").find("input").removeAttr("disabled");
        $("#frmConfiguracionesCadena").find("textarea").removeAttr("disabled");
        $("#cntEditar").css("display", "none");
        $("#cntCancelar").css("display", "block");
    });
    $('#btnListo').click(function () {
        $("#frmConfiguracionesCadena").find("input").attr("disabled", "disabled");
        $("#frmConfiguracionesCadena").find("textarea").attr("disabled", "disabled");
        $("#cntCancelar").css("display", "none");
        $("#cntEditar").css("display", "block");
    });
    $('#btnEditarConfiguracionCadena').click(function () {
        $("#frmConfiguracionesCadena").find("input").removeAttr("disabled");
        $("#frmConfiguracionesCadena").find("textarea").removeAttr("disabled");
        $("#cntEditar").css("display", "none");
        $("#cntCancelar").css("display", "block");
    });
});

var cargarProductos = function () {
    tablaProductos = $('#tblProductos').DataTable({
        "ajax": {
            type: 'POST',
            url: "../adminFidelizacion/serviciosFidelizacion.php",
            data: {
                metodo: "cargarConfiguracionProductos"
            },
            "dataSrc": function (json) {
                for (var i = 0, ien = json.data.length; i < ien; i++) {
                    if (json.data[i]['aplicaPlan'] > 0) {
                        json.data[i]['aplica'] = '<div class="checkbox-custom"><input class="check" type="checkbox" disabled="disabled" checked><label for=""></label></div>';
                    } else {
                        json.data[i]['aplica'] = '<div class="checkbox-custom"><input id="" class="check" type="checkbox" disabled="disabled"><label for=""></label></div>';
                    }
                }
                return json.data;
            }
        },
        "createdRow": function (row, data, dataIndex) {
            $(row).attr('id', 'rowProducto' + data.idProducto);
            $(row).attr('key', data.idProducto);
            $(row).attr('number', data.numProducto);
            $(row).attr('points', data.puntos);
            $(row).attr('name', data.nombreProducto);
            $(row).attr('text', data.descripcionProducto);
            $(row).attr('apply', data.aplicaPlan);
        },
        "columns": [
            {"data": "numProducto"},
            {"data": "nombreProducto"},
            {"data": "puntos"},
            {"data": "descripcionProducto"},
            {"data": "aplica"}
        ]
    });
    $('#tblProductos tbody').on('click', 'tr', function () {
        $('#tblProductos').find('tr').removeClass("active");
        $('#' + $(this).attr('id')).addClass("active");
    });
    if (tipoAmbiente === "azure" /* && aplicaPlan > 0*/) {
        $('#tblProductos tbody').on('dblclick', 'tr', function () {
            var id = $(this).attr('id');
            $('#inProductName').val($('#' + id).attr("name"));
            $('#inProductPoints').val($('#' + id).attr("points"));
            $('#inProductDescript').text($('#' + id).attr("text"));
            $('#inProductOrder').val($('#' + id).attr("order"));
            $('#mdlTitleModalProduct').text($('#' + id).attr("number") + ' - ' + $('#' + id).attr("name"));
            if ($('#' + id).attr("apply") > 0) {
                $('#inProductApply').prop("checked", true);
            } else {
                $('#inProductApply').prop("checked", false);
            }
            $('#btnProductSave').attr("onclick", "guardarConfiguracionProducto(" + id.replace("rowProducto", "") + ")");
            $('#mdlFrmProductos').modal({
                show: true
            });
        });
    }
};

var cargarRestaurantes = function () {
    tablaRestaurantes = $('#tblRestaurantes').DataTable({
        "ajax": {
            type: 'POST',
            url: "../adminFidelizacion/serviciosFidelizacion.php",
            data: {
                metodo: "cargarConfiguracionRestaurante"
            },
            "dataSrc": function (json) {
                for (var i = 0, ien = json.data.length; i < ien; i++) {
                    json.data[i]['restaurante'] = json.data[i]['idTienda'] + ' ' + json.data[i]['nombre'];
                    if (json.data[i]['aplicaPlan'] > 0) {
                        json.data[i]['aplica'] = '<div class="checkbox-custom"><input id="" class="check" type="checkbox" disabled="disabled" checked><label for=""></label></div>';
                    } else {
                        json.data[i]['aplica'] = '<div class="checkbox-custom"><input id="" class="check" type="checkbox" disabled="disabled"><label for=""></label></div>';
                    }
                }
                return json.data;
            }
        },
        "createdRow": function (row, data, dataIndex) {
            $(row).attr('id', 'rowRestaurante' + data.idRestaurante);
            $(row).attr('key', data.idTienda);
            $(row).attr('name', data.nombre);
            $(row).attr('latitude', data.latitud);
            $(row).attr('longitude', data.longitud);
            $(row).attr('apply', data.aplicaPlan);
        },
        "columns": [
            {"data": "restaurante"},
            {"data": "latitud"},
            {"data": "longitud"},
            {"data": "aplica"}
        ]
    });
    $('#tblRestaurantes tbody').on('click', 'tr', function () {
        $('#tblRestaurantes').find('tr').removeClass("active");
        $('#' + $(this).attr('id')).addClass("active");
    });
    if (tipoAmbiente === "azure" /* && aplicaPlan > 0*/) {
        $('#tblRestaurantes tbody').on('dblclick', 'tr', function () {
            var id = $(this).attr('id');
            $('#mdlTitleModalRestaurant').text($('#' + id).attr("key") + ' ' + $('#' + id).attr("name"));
            $('#inRestaurantLatitude').val($('#' + id).attr("latitude"));
            $('#inRestaurantLongitude').val($('#' + id).attr("longitude"));
            var aplica = $('#' + id).attr("apply");
            if (aplica > 0) {
                $('#inRestaurantApply').prop("checked", true);
            } else {
                $('#inRestaurantApply').prop("checked", false);
            }
            $('#btnGuardarRestaurante').attr("onclick", "guardarConfiguracionRestaurante(" + id.replace("rowRestaurante", "") + ")");
            $('#mdlFrmRestaurante').modal({
                show: true
            });
        });
    }
};

var cargarFormaPago = function () {
    tablaFormaPago = $('#tblFormasPago').DataTable({
//        "columnDefs": [
//            {"className": "centrar", "targets": [3]}
//        ],
        "ajax": {
            type: 'POST',
            url: "../adminFidelizacion/serviciosFidelizacion.php",
            data: {
                metodo: "cargarConfiguracionFormaPago"
            },
            "dataSrc": function (json) {
                for (var i = 0, ien = json.data.length; i < ien; i++) {
                    //  json.data[i]['restaurante'] = json.data[i]['idTienda'] + ' ' + json.data[i]['nombre'];
                    if (json.data[i]['aplicaRestriccion'] > 0) {
                        json.data[i]['aplica'] = '<div  class="checkbox-custom"><input id="" class="check" type="checkbox" disabled="disabled" checked><label for=""></label></div>';
                    } else {
                        json.data[i]['aplica'] = '<div  class="checkbox-custom"><input id="" class="check" type="checkbox" disabled="disabled"><label for=""></label></div>';
                    }
                }
                return json.data;
            }
        },
        "createdRow": function (row, data, dataIndex) {
            $(row).attr('id', 'IDFormapago' + data.IDFormapago);
            $(row).attr('codigo', data.codigo);
            $(row).attr('descripcion', data.descripcion);
            $(row).attr('formaPago', data.formaPago);
            $(row).attr('apply', data.aplicaRestriccion);
        },
        "columns": [
            {"data": "codigo"},
            {"data": "descripcion"},
            {"data": "formaPago"},
            {"data": "aplica"}
        ]
    });
    $('#tblFormasPago tbody').on('click', 'tr', function () {
        $('#tblFormasPago').find('tr').removeClass("active");
        $('#' + $(this).attr('id')).addClass("active");
    });
    if (tipoAmbiente === "azure") {
        $('#tblFormasPago tbody').on('dblclick', 'tr', function () {
            var id = $(this).attr('id');
            $('#mdlTitleModalFormasPago').text('FORMA DE PAGO: ' + $('#' + id).attr("descripcion"));
            var aplica = $('#' + id).attr("apply");
            if (aplica > 0) {
                $('#inFormasPagoApply').prop("checked", true);
            } else {
                $('#inFormasPagoApply').prop("checked", false);
            }
            $('#btnGuardarFormaPago').attr("onclick", "guardarConfiguracionFormaPago('" + id.replace("IDFormapago", "") + "')");
            $('#mdlFrmFormasPago').modal({
                show: true
            });
        });
    }
};

var guardarConfiguracionProducto = function (idProducto) {
    cargando(1);
    //Lectura Variables
    var descripcion = $("#inProductDescript").val();
    var aplicaPlan = 0;
    var puntos = $("#inProductPoints").val();
    if ($("#inProductApply").is(':checked')) {
        aplicaPlan = 1;
    }
    send = {};
    send.metodo = "guardarConfiguracionProductos";
    send.idProducto = idProducto;
    send.puntos = puntos;
    send.aplicaPlan = aplicaPlan;
    send.descripcion = descripcion;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminFidelizacion/serviciosFidelizacion.php", data: send,
        success: function (datos) {
            if (datos.estado > 0) {
                var fila = tablaProductos.row("#rowProducto" + idProducto).data();
                fila.puntos = puntos;
                fila.aplicaPlan = aplicaPlan;
                fila.descripcionProducto = descripcion;
                if (aplicaPlan > 0) {
                    fila.aplica = '<div class="checkbox-custom"><input id="" class="check" type="checkbox" disabled="disabled" checked><label for=""></label></div>';
                } else {
                    fila.aplica = '<div class="checkbox-custom"><input id="" class="check" type="checkbox" disabled="disabled"><label for=""></label></div>';
                }
                tablaProductos.ajax.reload();
                $('#mdlFrmProductos').modal("hide");
                cargando(0);
            } else {
                cargando(0);
                alertify.success(datos.mensaje);
            }
        },
        error: function () {
            cargando(0);
            alertify.error("Lo sentimos, ha ocurrido un error.");
        }
    });
};

var guardarConfiguracionRestaurante = function (idRestaurante) {
    cargando(1);
    //Lectura Variables
    var latitud = $("#inRestaurantLatitude").val();
    var longitud = $("#inRestaurantLongitude").val();
    var aplicaPlan = 0;
    if ($("#inRestaurantApply").is(':checked')) {
        aplicaPlan = 1;
    }
    var ImprimePuntosRide = 0;
    if ($("#inPuntosRide").is(':checked')) {
        ImprimePuntosRide = 1;
    }
    send = {};
    send.metodo = "guardarConfiguracionRestaurante";
    send.idRestaurante = idRestaurante;
    send.latitud = latitud;
    send.aplicaPlan = aplicaPlan;
    send.longitud = longitud;
    send.ImprimePuntosRide = ImprimePuntosRide;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminFidelizacion/serviciosFidelizacion.php", data: send,
        success: function (datos) {
            if (datos.estado > 0) {
                var fila = tablaRestaurantes.row("#rowRestaurante" + idRestaurante).data();
                fila.latitud = latitud;
                fila.longitud = longitud;
                if (aplicaPlan > 0) {
                    fila.aplica = '<div class="checkbox-custom"><input id="" class="check" type="checkbox" disabled="disabled" checked><label for=""></label></div>';
                } else {
                    fila.aplica = '<div class="checkbox-custom"><input id="" class="check" type="checkbox" disabled="disabled"><label for=""></label></div>';
                }
                tablaRestaurantes.ajax.reload();
                $('#mdlFrmRestaurante').modal("hide");
                cargando(0);
            } else {
                cargando(0);
                alertify.success(datos.mensaje);
            }
        },
        error: function () {
            cargando(0);
            alertify.error("Lo sentimos, ha ocurrido un error.");
        }
    });
};

var guardarConfiguracionFormaPago = function (idFormaPago) {
    cargando(1);
    var estado = 0;
    if ($("#inFormasPagoApply").is(':checked')) {
        estado = 1;
    }
    send = {};
    send.metodo = "guardarConfiguracionFormaPago";
    send.idFormaPago = idFormaPago;
    send.estado = estado;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminFidelizacion/serviciosFidelizacion.php", data: send,
        success: function (datos) {
            var fila = tablaFormaPago.row("#IDFormapago" + idFormaPago).data();
            if (estado > 0) {
                fila.aplica = '<div class="checkbox-custom"><input id="" class="check" type="checkbox" disabled="disabled" checked><label for=""></label></div>';
            } else {
                fila.aplica = '<div class="checkbox-custom"><input id="" class="check" type="checkbox" disabled="disabled"><label for=""></label></div>';
            }
            tablaFormaPago.ajax.reload();
            $('#mdlFrmFormasPago').modal("hide");
            cargando(0);
            alertify.success("Datos actualizados correctamente.");
        },
        error: function () {
            cargando(0);
            alertify.error("Lo sentimos, ha ocurrido un error.");
        }
    });
};

var cargando = function (estado) {
    if (estado) {
        $("#mdl_rdn_pdd_crgnd").css("display", "block");
        $("#mdl_pcn_rdn_pdd_crgnd").css("display", "block");
    } else {
        $("#mdl_rdn_pdd_crgnd").css("display", "none");
        $("#mdl_pcn_rdn_pdd_crgnd").css("display", "none");
    }
};

var cargarConfiguracionCadena = function () {
    send = {};
    send.metodo = "cargarConfiguracionCadena";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminFidelizacion/serviciosFidelizacion.php", data: send,
        success: function (datos) {
            $("#inNombrePlan").val(datos.nombrePlan);
            $("#inFormatoRide").text(datos.formatoRide);
            $("#inFormatoVoucher").text(datos.formatoVoucher);
            $("#inBienvenida").val(datos.bienvenida);
            $("#inTituloVoucher").val(datos.tituloVoucher);
            $("#inUrlPaginaWeb").val(datos.urlWeb);
            $("#inDespedida").val(datos.despedida);
            $("#inNombreApp").val(datos.app);
            $("#inTituloRide").val(datos.tituloRide);
            $("#inRuc").val(datos.autoconsumoRuc);
            $("#inRazonSocial").val(datos.autoconsumoRazonSocial);
        }
    });
};

var guardarConfiguracionCadena = function (id) {
    cargando(1);
    var parametro = $(id).attr('parametro');
    var tipo = $(id).attr('tipo');
    var valor = $(id).val();
    send = {};
    send.metodo = "guardarConfiguracionCadena";
    send.parametro = parametro;
    send.valor = valor;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminFidelizacion/serviciosFidelizacion.php", data: send,
        success: function (datos) {
            cargando(0);
            alertify.success(datos.mensaje);
        },
        error: function () {
            cargando(0);
            alertify.error("Lo sentimos, ha ocurrido un error.");
        }
    });
};

var cargarConfiguracionCadena = function () {
    send = {};
    send.metodo = "cargarConfiguracionCadena";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminFidelizacion/serviciosFidelizacion.php", data: send,
        success: function (datos) {
            $("#inNombrePlan").val(datos.nombrePlan);
            $("#inFormatoRide").text(datos.formatoRide);
            $("#inFormatoVoucher").text(datos.formatoVoucher);
            $("#inBienvenida").val(datos.bienvenida);
            $("#inTituloVoucher").val(datos.tituloVoucher);
            $("#inUrlPaginaWeb").val(datos.urlWeb);
            $("#inDespedida").val(datos.despedida);
            $("#inNombreApp").val(datos.app);
            $("#inTituloRide").val(datos.tituloRide);
            $("#inRuc").val(datos.autoconsumoRuc);
            $("#inRazonSocial").val(datos.autoconsumoRazonSocial);
            $("#inPregunta").val(datos.preguntaRegistro);
            $("#txtRucInterface").val(datos.interfaceRuc);
            $("#inRazonSocialInterface").val(datos.intefaceRazonSocial);
        }
    });
};

var guardarConfiguracionCadena = function (id) {
    cargando(1);
    var parametro = $(id).attr('parametro');
    var tipo = $(id).attr('tipo');
    var valor = $(id).val();
    send = {};
    send.metodo = "guardarConfiguracionCadena";
    send.parametro = parametro;
    send.valor = valor;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminFidelizacion/serviciosFidelizacion.php", data: send,
        success: function (datos) {
            cargando(0);
            alertify.success(datos.mensaje);
        },
        error: function () {
            cargando(0);
            alertify.error("Lo sentimos, ha ocurrido un error.");
        }
    });
};

$('#btnHabilitarPlanFidelizacion').click(function () {
    bootbox.confirm({
        message: "¿Estás seguro que deseas activar las configuraciones del plan de fidelización?",
        title: "Activar Plan Amigos",
        callback: function (result) {
            if (result) {
                cargando(1);
                send = {};
                send.metodo = "activarModuloFidelizacion";
                $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
                    url: "../adminFidelizacion/serviciosFidelizacion.php", data: send,
                    success: function (datos) {
                        cargando(0);
                        bootbox.dialog({
                            message: datos.mensaje,
                            title: "Mensaje",
                            buttons: {
                                success: {
                                    label: "Continuar",
                                    className: "btn-primary btn-alt",
                                    callback: function () {
                                        location.reload();
                                    }
                                }
                            }
                        });
                    },
                    error: function () {
                        cargando(0);
                        alertify.error("Lo sentimos, ha ocurrido un error.");
                    }
                });
            }
        }
    });
});

$('#btnDeshabilitarPlanFidelizacion').click(function () {
    bootbox.confirm({
        message: "¿Estás seguro que deseas desactivar las configuraciones del plan de fidelización? Se pueden perder varias configuraciones que se han realizado.",
        title: "Desactivar Plan Amigos",
        callback: function (result) {
            if (result) {
                cargando(1);
                send = {};
                send.metodo = "desactivarModuloFidelizacion";
                $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
                    url: "../adminFidelizacion/serviciosFidelizacion.php", data: send,
                    success: function (datos) {
                        cargando(0);
                        bootbox.dialog({
                            message: datos.mensaje,
                            title: "Mensaje",
                            buttons: {
                                success: {
                                    label: "Continuar",
                                    className: "btn-primary btn-alt",
                                    callback: function () {
                                        location.reload();
                                    }
                                }
                            }
                        });
                    },
                    error: function () {
                        cargando(0);
                        alertify.error("Lo sentimos, ha ocurrido un error.");
                    }
                });
            }
        }
    });
});