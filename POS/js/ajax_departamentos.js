var send = {};

$(document).ready(function () {
    $('#mdl_rdn_pdd_crgnd').hide();
    cargarDepartamentosPorCadena();
     
});

var cargarDepartamentosPorCadena = function () {
    $('#mdl_rdn_pdd_crgnd').show();
    var html = "<thead><tr class='active'><th style='text-align:center'>Num. Departamento</th><th style='text-align:center'>Departamento</th><th style='text-align:center' width='10%'>Estado</th></tr></thead>";
    send = {};
    send.metodo = "cargarDepartamentosPorCadena";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminDepartamentos/config_departamentos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = "<input type='checkbox' disabled='disabled' checked>";
                    if (datos[i]["estado"] < 1)
                        estado = "<input type='checkbox' disabled='disabled'>";
                    html = html + "<tr id='flDepartamento" + datos[i]['idParametro'] + "' onclick=\"seleccionarFilaTablaDepartamento(\'" + datos[i]['idParametro'] + "\')\" ondblclick=\"modificarDepartamento(\'" + datos[i]['idParametro'] + "\', " + datos[i]['idDepartamento'] + ", \'" + datos[i]['departamento'] + "\', " + datos[i]["estado"] + ")\"><td class='text-center'>" + datos[i]['NumDepartamento'] + "</td><td style='text-align: left;'>" + datos[i]['departamento'] + "</td><td style='text-align: center;'>" + estado + "</td></tr>";
                }
            }
            $('#tblDepartamentos').html(html);
            $('#tblDepartamentos').dataTable({'destroy': true});
            $("#tblDepartamentos_length").hide();
            $('#mdl_rdn_pdd_crgnd').hide();
          
        }
    });
};

var seleccionarFilaTablaDepartamento = function (idDepartamento) {
    $("#tblDepartamentos tr").removeClass("success");
    $("#flDepartamento" + idDepartamento).addClass("success");
};

var crearDepartamento = function () {
    $('#mdl_rdn_pdd_crgnd').show();
    $('#mdlDepartamento').modal("show");
    $('#tituloDepartamento').html("Nuevo Departamento");
    $("#inEstadoDepartamento").prop("checked", "checked");
    $('#inDescripcionDepartamento').val("");
    $('#btnGuardarDepartamento').attr("onclick", "mergeDepartamentos(0, '', 0)");
    $('#mdl_rdn_pdd_crgnd').hide();
};

var modificarDepartamento = function (idParametro, idDepartamento, departamento, estado) {
    $('#mdl_rdn_pdd_crgnd').show();
    $('#mdlDepartamento').modal("show");
    $('#tituloDepartamento').html(departamento);
    $('#inDescripcionDepartamento').val(departamento);
    if (estado > 0) {
        $("#inEstadoDepartamento").prop("checked", "checked");
    } else {
        $("#inEstadoDepartamento").prop("checked", "");
    }
    $('#btnGuardarDepartamento').attr("onclick", "mergeDepartamentos(1, '" + idParametro + "', " + idDepartamento + ")");
    $('#mdl_rdn_pdd_crgnd').hide();
};

var mergeDepartamentos = function (opcion, idParametro, idDepartamento) {
 
   $('#mdl_rdn_pdd_crgnd').show();
 
 
    var estado = 0;
    var descripcion = $('#inDescripcionDepartamento').val();
    if ($('#inEstadoDepartamento').prop('checked')) {
        estado = 1;
    }
    
    var html = "<thead><tr class='active'><th style='text-align:center'>Num. Departamento</th><th style='text-align:center'>Departamento</th><th style='text-align:center' width='10%'>Estado</th></tr></thead>";
    send = {};
    send.metodo = "actualizarDepartamentosPorCadena";
    send.opcion = opcion;
    send.descripcion = descripcion;
    send.idParametro = idParametro;
    send.idDepartamento = idDepartamento;
    send.estado = estado;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminDepartamentos/config_departamentos.php",
        data: send,
         success: function (datos) {
         
             if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = "<input type='checkbox' disabled='disabled' checked>";
                    if (datos[i]["estado"] < 1)
                        estado = "<input type='checkbox' disabled='disabled'>";
                    html = html + "<tr id='flDepartamento" + datos[i]['idParametro'] + "' onclick=\"seleccionarFilaTablaDepartamento(\'" + datos[i]['idParametro'] + "\')\" ondblclick=\"modificarDepartamento(\'" + datos[i]['idParametro'] + "\', " + datos[i]['idDepartamento'] + ", \'" + datos[i]['departamento'] + "\', " + datos[i]["estado"] + ")\"><td class='text-center'>" + datos[i]['NumDepartamento'] + "</td><td style='text-align: left;'>" + datos[i]['departamento'] + "</td><td style='text-align: center;'>" + estado + "</td></tr>";
                }
            }
           alertify.success(datos.mensaje);
            $('#tblDepartamentos').html(html);
            $('#tblDepartamentos').dataTable({'destroy': true});
            $("#tblDepartamentos_length").hide();
            $('#mdl_rdn_pdd_crgnd').hide();
            $('#mdlDepartamento').modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('#mdl_rdn_pdd_crgnd').hide();
            $('#mdlDepartamento').modal("hide");
            alertify.error("Error: "+errorThrown);
      
         
        }
    });
};