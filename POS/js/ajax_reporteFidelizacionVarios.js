var idReporte = '';
var titulo = '';
var tokenSeguridadConsumo = '';
var table = '';
var filtros = new Array();
$(document).ready(function () {
    inicializarInputs();

    cargarTokenSeguridad();

    $('.tree-toggle').click(function () {
        $(this).parent().children('ul.tree').toggle(200);
    });

    $('#reportes li a').click(function (e) {

        $('#InformacionReportes').hide();
        var id = $(this).attr('data-identificador');
        e.preventDefault();
        titulo = $(this).html();
        filtrosReporte(id, titulo);
        $('#rprmProducto').val("-1");
        $('#rprmTienda').val("-1");
        $('#rprmDesdeR').val("");
        $('#rprmHastaR').val("");
    });
    cargarProducto();
    cargarTienda();
    cargando(false);


});

/*
$('#filtrosActivos').change(function () {
        if ($('#filtrosActivos').is(':checked')) {
        $("#rprmDesdeR").prop('disabled', false);
        $("#rprmHastaR").prop('disabled', false);
        $("#rprmTienda").prop('disabled', false);
        $("#rprmProducto").prop('disabled', false);
    }else{
        $("#rprmDesdeR").prop('disabled', true);
        $("#rprmHastaR").prop('disabled', true);
        $("#rprmTienda").prop('disabled', true);
        $("#rprmProducto").prop('disabled', true);
    }
});*/


var cargando = function (cargando) {
    if (cargando) {
        $("#mdl_rdn_pdd_crgnd").show();
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
    }
};


$("#btnReporte").click(function () {
    validarParametrosR();
    //ConsumirWSReportes();
});

var ConsumirWSReportes = function (startDateR, endDateR, Tienda, Producto) {
    cargando(true);
    $('#InformacionReportes').show();
    $('#Prueba').show();
    $('#Prueba').html('  <br><table id="tblTablaReportes" class="table table-bordered "></table>');
    $("#InformacionReportes").show();
    $("#tituloTabla").html(titulo);

    send = {"enviarParametros": 1};
    send.metodo = "enviarParametros",
        send.nombreWs = idReporte,
        send.startDateR = startDateR,
        send.endDateR = endDateR,
        send.Tienda = Tienda,
        send.Producto = Producto,
        send.token = tokenSeguridadConsumo
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminReportesFidelizacion/configReporteFidelizacion.php", data: send,
        success: function (datos) {
            cargando(false);
            if (datos.header.length !== 0) {
                table = $('#tblTablaReportes').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'copyHtml5',
                        'excelHtml5',
                        'pdfHtml5'
                    ],
                    "destroy": true,
                    "columns": datos["header"],
                    "data": datos["body"]
                });
            } else {

                table = $('#tblTablaReportes').DataTable({
                    "destroy": true,
                    "columns": [{title: "<center><p style='size: 15px' >Respuesta Reporte WS</p></center>"}],
                    "data": [["<center><p style='color: #FF0000'>No Existe Registros</p></center>"]]
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            cargando(false);
        }
    });
}


var validarParametrosR = function () {
    cargando(true);

    var Producto = $("#rprmProducto").val();
    var Tienda = $("#rprmTienda").val();

    var startDate = moment($("#rprmDesdeR").val(), 'DD/MM/YYYY', true);
    var endDate = moment($("#rprmHastaR").val(), 'DD/MM/YYYY', true);
    if (startDate.isValid()) {
        if (endDate.isValid()) {
            if (endDate.diff(startDate, 'days') >= 0) {
                var startDateR = startDate.format('YYYY-MM-DD');
                var endDateR = endDate.format('YYYY-MM-DD');
                ConsumirWSReportes(startDateR, endDateR, Tienda, Producto);

            } else {
                cargando(false);
                alertify.error("La fecha final no puede ser menor a la fecha inicial");
                $('#InformacionReportes').hide();
            }
        } else {
            cargando(false);
            alertify.error("Seleccionar una fecha final");
            $('#InformacionReportes').hide();
        }
    } else {
        cargando(false);
        alertify.error("Seleccionar una fecha inicial");
        $('#InformacionReportes').hide();
    }
};

var cargarTokenSeguridad = function () {
    send = {};
    send.metodo = "cargarTokenSeguridadConsumo";
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminReportesFidelizacion/configReporteFidelizacion.php",
        data: send,
        success: function (datos) {
            tokenSeguridadConsumo = datos.access_token;
            //alert(tokenSeguridad);
        }
    });
};

var inicializarInputs = function () {
    $("#rprmDesdeR").keypress(function (e) {
        e.preventDefault();
    });
    $("#rprmDesdeR").daterangepicker({
        singleDatePicker: true,
        format: "DD/MM/YYYY",
        drops: "down",
        defaultDate: moment(),
        setDate: moment()
    });
    $("#rprmHastaR").keypress(function (e) {
        e.preventDefault();
    });
    $("#rprmHastaR").daterangepicker({
        singleDatePicker: true,
        format: "DD/MM/YYYY",
        drops: "down",
        defaultDate: moment(),
        setDate: moment()
    });

};

var filtrosReporte = function (id, titulo) {
    $("#divFiltros").show();
    $("#titulofiltros").html('Filtros: ' + titulo);
    $("#tituloTabla").html('');
    idReporte = id;
    $("#filtrosR").show();
    $('#Prueba').hide();
    $("#Desde").show();
    $("#Hasta").show();

    if (id == 'balance_store' || id == 'consumption_total_store') {
        $("#idTienda").show();
        $("#idProducto").hide();
    } else if (id == 'consumption_store') {
        $("#idProducto").show();
        $("#idTienda").show();
    } else if (id == 'consumption_points_resumen') {
        $("#idProducto").show();
        $("#idTienda").hide();
    } else if (id == 'consumption_points') {
        $("#idProducto").show();
        $("#idTienda").hide();
        inicializarInputs();
    } else if (id == 'best_clients') {
        $("#idProducto").hide();
    }
    else if (id == 'pvp_month') {
        $("#idProducto").hide();
        $("#idTienda").show();

    }

}


var cargarProducto = function () {

    $.ajax({
        type: 'POST',
        url: "../adminFidelizacion/serviciosFidelizacion.php",
        data: {
            metodo: "cargarConfiguracionProductos"
        },
        success: function (datos) {
            var json = JSON.parse(datos);

            $("#rprmProducto").html("");
            $('#rprmProducto').html("<option selected value='-1'>----------Todos----------</option>");
            for (var i = 0, ien = json.data.length; i < ien; i++) {
                var html = "<option value='" + json.data[i]['nombreProducto'] + "'>" + json.data[i]['nombreProducto'] + "</option>";
                $("#rprmProducto").append(html);

            }
        }
    });
}

var cargarTienda = function () {
    $.ajax({
        type: 'POST',
        url: "../adminFidelizacion/serviciosFidelizacion.php",
        data: {
            metodo: "cargarListaRestaurantes"
        },
        success: function (datos) {
            var res = JSON.parse(datos);
            //console.log(datos);
            $("#rprmTienda").html("");
            $('#rprmTienda').html("<option selected value='-1'>----------Todos----------</option>");
            for (var i in res) {
                var html = "<option value='" + res[i]['nombre'] + "'>" + res[i]['nombre'] + "</option>";
                $("#rprmTienda").append(html);

            }

        }
    });


}


