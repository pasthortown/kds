var tblDailySummary;
var tblDailySummaryProducts;
var tokenSeguridad = "";
$(document).ready(function() {
    inicializarInputs();
    cargarTokenSeguridad();
    cargando(false);
});
var inicializarInputs = function() {
    $("#rprmDesde").keypress(function(e) {
        e.preventDefault();
    });
    $("#rprmDesde").daterangepicker({
        singleDatePicker: true,
        format: "DD/MM/YYYY",
        drops: "down",
        defaultDate: moment(),
        setDate: moment()
    });
    $("#rprmHasta").keypress(function(e) {
        e.preventDefault();
    });
    $("#rprmHasta").daterangepicker({
        singleDatePicker: true,
        format: "DD/MM/YYYY",
        drops: "down",
        defaultDate: moment(),
        setDate: moment()
    });
    $("#btnReporteGenerar").click(function() {
        validarParametros();
    });
};
var validarParametros = function() {
    cargando(true);
    var startDate = moment($("#rprmDesde").val(), 'DD/MM/YYYY', true);
    var endDate = moment($("#rprmHasta").val(), 'DD/MM/YYYY', true);
    if (startDate.isValid()) {
        if (endDate.isValid()) {
            if (endDate.diff(startDate, 'days') >= 0) {
                startDate = startDate.format('YYYY-MM-DD');
                endDate = endDate.format('YYYY-MM-DD');
                cargarTransacciones(startDate, endDate);
                cargarProductos(startDate, endDate);
                cargando(false);
            } else {
                cargando(false);
                alertify.error("La fecha final no puede ser menor a la fecha inicial");
            }
        } else {
            cargando(false);
            alertify.error("Seleccionar una fecha final");
        }
    } else {
        cargando(false);
        alertify.error("Seleccionar una fecha inicial");
    }
};
var cargarTransacciones = function(startDate, endDate) {
    tblDailySummary = $('#tblDailySummary').DataTable({
        "destroy": true,
        "ajax": {
            type: 'POST',
            url: "../adminReportesFidelizacion/configReporteFidelizacion.php",
            data: {
                metodo: "enviarParametrosTransacciones",
                startDate: startDate,
                endDate: endDate,
                token: tokenSeguridad
            },
            "dataSrc": function(json) {
                for (var i = 0, ien = json.data.length; i < ien; i++) {
                    if (json.data[i]['store'] == "SIN LOCAL") {
                        json.data[i]['store'] = "TOTAL";
                        i = json.data.length;
                    }
                }
                return json.data;
            }
        },
        "createdRow": function(row, data, dataIndex) {},
        "columns": [{
            "data": "date"
        }, {
            "data": "week"
        }, {
            "data": "store"
        }, {
            "data": "pointEarnedOrders"
        }, {
            "data": "pointEarnedCoupons"
        }, {
            "data": "pointEarnedBalance"
        }, {
            "data": "pointRedeemed"
        }, {
            "data": "pointExpired"
        }, {
            "data": "pointReversedCoupon"
        }, {
            "data": "pointReversedOrder"
        }, {
            "data": "preregisteredPos"
        }, {
            "data": "subscribedAfterPos"
        }, {
            "data": "registeredApp"
        }, {
            "data": "registeredWeb"
        }, {
            "data": "referrals"
        }],
        "footerCallback": function(row, data, start, end, display) {
            //console.log(row, data, start, end, display);
        }
    });
};
var cargarProductos = function(startDate, endDate) {
    tblDailySummaryProducts = $('#tblDailySummaryProducts').DataTable({
        "destroy": true,
        "ajax": {
            type: 'POST',
            url: "../adminReportesFidelizacion/configReporteFidelizacion.php",
            data: {
                metodo: "enviarParametrosProducto",
                startDate: startDate,
                endDate: endDate,
                token: tokenSeguridad
            },
            "dataSrc": function(json) {
                for (var i = 0, ien = json.data.length; i < ien; i++) {
                    if (json.data[i]['store'] == "SIN LOCAL") {
                        json.data[i]['store'] = "TOTAL";
                        i = json.data.length;
                    }
                }
                return json.data;
            }
        },
        "createdRow": function(row, data, dataIndex) {},
        "columns": [{
            "data": "date"
        }, {
            "data": "week"
        }, {
            "data": "store"
        }, {
            "data": "codProduct"
        }, {
            "data": "numOrders"
        }, {
            "data": "numProducts"
        }, {
            "data": "pointRedeemed"
        }, {
            "data": "pointEarnedOrders"
        }, {
            "data": "pointEarnedCoupons"
        }, {
            "data": "pointEarnedBalance"
        }, {
            "data": "numRedeemed"
        }],
        "footerCallback": function(row, data, start, end, display) {
            //console.log(row, data, start, end, display);
        }
    });
};
var cargarTokenSeguridad = function() {
    send = {};
    send.metodo = "cargarTokenSeguridad";
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminReportesFidelizacion/configReporteFidelizacion.php",
        data: send,
        success: function(datos) {
            tokenSeguridad = datos.access_token;
            // alert(tokenSeguridad);
        }
    });
};
var cargando = function(cargando) {
    if (cargando) {
        $("#mdl_rdn_pdd_crgnd").show();
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
    }
};