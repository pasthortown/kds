/* global bootbox */

var tblLogsTable;

function cargarLogs() {
    cargando(1);
    var table = $('#tblLogs').DataTable();
    table.destroy();
    var fechaInicio = $("#init").val();
    var fechaFin = $("#fin").val();
    if (fechaInicio != "" && fechaFin) {
        tblLogsTable = $('#tblLogs').DataTable({
            "ajax": {
                type: 'POST',
                url: "../adminFidelizacionLogs/serviciosFidelizacion.php",
                data: {
                    "metodo": "cargarLogsPorPeriodo",
                    "fechaInicio": fechaInicio,
                    "fechaFin": fechaFin
                },
                "dataSrc": function (json) {
                    return json.data;
                }
            },
            "createdRow": function (row, data, dataIndex) {
                $(row).attr('id', data.id);
                $(row).attr('accion', data.accion);
                $(row).attr('descripcion', data.descripcion);
                $(row).attr('usuario', data.usuario);
                $(row).attr('fechaAuditoria', data.fechaAuditoria);
                $(row).attr('respuesta', data.respuesta);
            },
            "columns": [
                {"data": "accion"},
                {"data": "descripcion"},
                {"data": "usuario"},
                {"data": "fechaAuditoria"}
            ]
        });
        //Clic Columnas
        $('#tblLogs tbody').on('click', 'tr', function () {
            $('#tblLogs').find('tr').removeClass("active");
            $('#' + $(this).attr('id')).addClass("active");
        });
        //Doble Clic Columnas
        $('#tblLogs tbody').on('dblclick', 'tr', function () {
            var id = $(this).attr('id');
            abrirRespuesta(id);
        });
    }
    cargando(0);
}

var abrirRespuesta = function (id) {
    cargando(1);
    bootbox.dialog({
        title: $("#" + id).attr('accion'),
        message: $("#" + id).attr('respuesta')
    });
    cargando(0);
};

var cargando = function (estado) {
    if (estado) {
        $("#mdl_rdn_pdd_crgnd").show();
        $("#mdl_pcn_rdn_pdd_crgnd").show();
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
        $("#mdl_pcn_rdn_pdd_crgnd").hide();
    }
};