var tblFacturasErrores;
var tblFacturasErroresNC;
var tblLogsTable;
$(document).ready(function() {
    $("#inPeriodoRestaurante").change(function() {
        var idPeriodo = $("#inPeriodoRestaurante :selected").val();
        if (idPeriodo != "0") {
            cargarFacturasErrorPorPeriodo(idPeriodo);
            cargarNotasCreditoErrorPorPeriodo(idPeriodo);
        } else {
            var table = $('#tblNotasCreditoError').DataTable();
            table.clear();
            table.draw();
            var table = $('#tblFacturasError').DataTable();
            table.clear();
            table.draw();
        }
    });
    // $("#inPeriodoRestauranteNc").change(function() {
    //     var idPeriodoNC = $("#inPeriodoRestauranteNc :selected").val();
    //     if (idPeriodoNC != "0") {
    //         cargarNotasCreditoErrorPorPeriodo(idPeriodoNC);
    //     } else alert("Limpiar tabla");
    // });
});

function reiniciarCampos(mensaje) {
    $("#inPeriodoRestaurante").html('');
    $("#inPeriodoRestauranteNc").html('');
    alertify.error(mensaje);
}

function CargarPeriodos() {
    cargando(1);
    var table = $('#tblNotasCreditoError').DataTable();
    table.clear();
    table.draw();
    var table = $('#tblFacturasError').DataTable();
    table.clear();
    table.draw();
    var fechaInicio = $("#init").val();
    var fechaFin = $("#fin").val();
    if (fechaInicio === "" || fechaFin === "") {
        cargando(0);
        reiniciarCampos("Los campos de fecha no tienen el formato correcto.");
        return;
    }
    if (fechaInicio > fechaFin) {
        cargando(0);
        reiniciarCampos("La fecha de inicio no puede ser mayor a la fecha de fin.");
        return;
    }
    // cargarLogs();
    cargarPeriodosLocal();
    cargando(0);
}
var cargarPeriodosLocal = function() {
    cargando(1);
    var html = "<option value='0'>-- Seleccione un periodo --</option>";
    send = {};
    send.metodo = "cargarPeriodosRestaurante";
    send.fechaInicio = $("#init").val();
    send.fechaFin = $("#fin").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminFidelizacionError/serviciosFidelizacion.php",
        data: send,
        success: function(datos) {
            for (var i = 0; i < datos.str; i++) {
                html += "<option value='" + datos[i]["idPeriodo"] + "'>" + datos[i]["apertura"] + " - " + datos[i]["apertura"] + " - " + datos[i]["estado"] + "</option>";
            }
            $("#inPeriodoRestaurante").html(html);
            $("#inPeriodoRestauranteNc").html(html);
        },
        error: function() {
            alertify.error("Lo sentimos, ha ocurrido un error.");
        }
    });
};
var cargarFacturasErrorPorPeriodo = function(idPeriodo) {
    var table = $('#tblFacturasError').DataTable();
    table.destroy();
    tblFacturasErrores = $('#tblFacturasError').DataTable({
        "ajax": {
            type: 'POST',
            url: "../adminFidelizacionError/serviciosFidelizacion.php",
            data: {
                "metodo": "cargarFacturasErrorPorPeriodo",
                "idPeriodo": idPeriodo
            },
            "dataSrc": function(json) {
                for (var i = 0, ien = json.data.length; i < ien; i++) {
                    json.data[i]['opcion'] = '<button type="button" class="btn btn-success glyphicon glyphicon-upload" style="width: 40px; height: 40px;" onclick="reenviarTransaccion(\'' + json.data[i]['idFactura'] + '\')" ></button>';
                }
                return json.data;
            }
        },
        "createdRow": function(row, data, dataIndex) {
            $(row).attr('fecha', data.fechaCreacion);
            $(row).attr('id', 'rowFactura' + data.idFactura);
            $(row).attr('secuencial', data.secuencial);
            $(row).attr('total', data.total);
            $(row).attr('codigo', data.codigo);
            $(row).attr('cliente', data.cliente);
            $(row).attr('cedulaCliente', data.cedulaCliente);
            $(row).attr('mensaje', data.mensaje);
            $(row).attr('opcion', data.opcion);
        },
        "columns": [{
            "data": "fechaCreacion"
        }, {
            "data": "idFactura"
        }, {
            "data": "secuencial"
        }, {
            "data": "total"
        }, {
            "data": "cedulaCliente"
        }, {
            "data": "cliente"
        }, {
            "data": "codigo"
        }, {
            "data": "mensaje"
        }, {
            "data": "opcion"
        }]
    });
    $('#tblFacturasError tbody').on('click', 'tr', function() {
        $('#tblFacturasError').find('tr').removeClass("active");
        $('#' + $(this).attr('id')).addClass("active");
    });
};
var cargarNotasCreditoErrorPorPeriodo = function(idPeriodo) {
    var table = $('#tblNotasCreditoError').DataTable();
    table.destroy();
    tblFacturasErroresNC = $('#tblNotasCreditoError').DataTable({
        "ajax": {
            type: 'POST',
            url: "../adminFidelizacionError/serviciosFidelizacion.php",
            data: {
                "metodo": "cargarNotasCreditoErrorPorPeriodo",
                "idPeriodo": idPeriodo
            },
            "dataSrc": function(json) {
                for (var i = 0, ien = json.data.length; i < ien; i++) {
                    json.data[i]['opcion'] = '<button type="button" class="btn btn-success glyphicon glyphicon-upload" style="width: 40px; height: 40px;" onclick="reenviarTransaccionNC(\'' + json.data[i]['secuencial'] + '\')" ></button>';
                }
                return json.data;
            }
        },
        "createdRow": function(row, data, dataIndex) {
            $(row).attr('fecha', data.fechaCreacion);
            $(row).attr('id', 'rowFactura' + data.idNotaCredito);
            $(row).attr('secuencial', data.secuencial);
            $(row).attr('total', data.total);
            $(row).attr('codigo', data.codigo);
            $(row).attr('cliente', data.cliente);
            $(row).attr('cedulaCliente', data.cedulaCliente);
            $(row).attr('mensaje', data.mensaje);
            $(row).attr('opcion', data.opcion);
        },
        "columns": [{
            "data": "fechaCreacion"
        }, {
            "data": "idNotaCredito"
        }, {
            "data": "secuencial"
        }, {
            "data": "total"
        }, {
            "data": "cedulaCliente"
        }, {
            "data": "cliente"
        }, {
            "data": "codigo"
        }, {
            "data": "mensaje"
        }, {
            "data": "opcion"
        }]
    });
    $('#tblNotasCreditoError tbody').on('click', 'tr', function() {
        $('#tblNotasCreditoError').find('tr').removeClass("active");
        $('#' + $(this).attr('id')).addClass("active");
    });
};
// function cargarLogs() {
//     var table = $('#tblLogs').DataTable();
//     table.destroy();
//     var fechaInicio = $("#init").val();
//     var fechaFin = $("#fin").val();
//     tblLogsTable = $('#tblLogs').DataTable({
//         "ajax": {
//             type: 'POST',
//             url: "../adminFidelizacion/serviciosFidelizacion.php",
//             data: {
//                 "metodo": "cargarLogsPorPeriodo",
//                 "fechaInicio": fechaInicio,
//                 "fechaFin": fechaFin
//             },
//             "dataSrc": function(json) {
//                 return json.data;
//             }
//         },
//         "createdRow": function(row, data, dataIndex) {
//             $(row).attr('fechaAuditoria', data.fechaAuditoria);
//             $(row).attr('descripcion', data.descripcion);
//             $(row).attr('usuario', data.usuario);
//             $(row).attr('mensaje', data.mensaje);
//         },
//         "columns": [{
//             "data": "fechaAuditoria"
//         }, {
//             "data": "descripcion"
//         }, {
//             "data": "usuario"
//         }, {
//             "data": "mensaje"
//         }]
//     });
//     $('#tblLogs tbody').on('click', 'tr', function() {
//         $('#tblLogs').find('tr').removeClass("active");
//         $('#' + $(this).attr('id')).addClass("active");
//     });
// };
var cargando = function(estado) {
    if (estado) {
        $("#mdl_rdn_pdd_crgnd").show();
        $("#mdl_pcn_rdn_pdd_crgnd").show();
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
        $("#mdl_pcn_rdn_pdd_crgnd").hide();
    }
};
var reenviarTransaccion = function(idFactura) {
    bootbox.confirm({
        message: "¿Estás seguro de reenviar esta transacción?",
        title: "Reenviar Transacción " + idFactura,
        callback: function(result) {
            if (result) {
                cargando(1);
                send = {};
                send.idFactura = idFactura;
                $.ajax({
                    async: false,
                    type: "POST",
                    dataType: "json",
                    "Accept": "application/json, text/javascript2",
                    contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
                    url: "../adminFidelizacionError/serviciosReenviarFactura.php",
                    data: send,
                    success: function(datos) {
                        cargando(0);
                        bootbox.dialog({
                            message: datos.message,
                            title: "Mensaje",
                            buttons: {
                                success: {
                                    label: "Continuar",
                                    className: "btn-primary btn-alt",
                                    callback: function() {
                                        if (datos.status > 0) {
                                            tblFacturasErrores.row($('#rowFactura' + idFactura)).remove().draw(false);
                                        }
                                        cargando(0);
                                    }
                                }
                            }
                        });
                    },
                    error: function() {
                        cargando(0);
                        alertify.error("Lo sentimos, ha ocurrido un error.");
                    }
                });
            }
            cargando(0);
        }
    });
};
var reenviarTransaccionNC = function(idFactura) {
    bootbox.confirm({
        message: "¿Estás seguro de reenviar esta transacción?",
        title: "Reenviar Transacción " + idFactura,
        callback: function(result) {
            if (result) {
                cargando(1);
                send = {};
                send.idFactura = idFactura;
                $.ajax({
                    async: false,
                    type: "POST",
                    dataType: "json",
                    "Accept": "application/json, text/javascript2",
                    contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
                    url: "../adminFidelizacionError/serviciosReenviarNotaCredito.php",
                    data: send,
                    success: function(datos) {
                        if (datos["message"] === "Transacción dada de baja exitosamente") {
                            alertify.success(datos["message"]);
                            bootbox.dialog({
                                message: datos.message,
                                title: "Mensaje",
                                buttons: {
                                    success: {
                                        label: "Continuar",
                                        className: "btn-primary btn-alt",
                                        callback: function() {
                                            if (datos.status > 0) {
                                                tblFacturasErroresNC.row($('#rowFactura' + idFactura)).remove().draw(false);
                                            }
                                            cargando(0);
                                        }
                                    }
                                }
                            });
                        } else {
                            alertify.error("No se pudo reversar los puntos"); // alertify.error(datos["errors"]["invoiceCode"]);
                        }
                        cargando(0);
                    },
                    error: function() {
                        cargando(0);
                        alertify.error("Lo sentimos, ha ocurrido un error.");
                    }
                });
            }
        }
    });
};