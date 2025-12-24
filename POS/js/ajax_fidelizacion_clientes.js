var tblReloadByCustomers;
var tblPointsByCustomer;
var tokenSeguridad = "";
var seguridad = "";
var urlLoyalty = "";
var urlReports = "";
var urlPoints = "";
var restaurantes = null;
var endDate = null;
$(document).ready(function() {
    $("#inCedula").val("");
    cargarConfiguracionCadena();
    // cargarTokenSeguridad();
    cargarListaRestaurantes();
    $("#btnBuscar").click(function() {
        cargando(1);
        var cedula = $("#inCedula").val();
        //Validar Cédula o RUC
        limpiarCampos();
        obtenerFechaActual();
        cargarDatosCliente(cedula);
        cargarTransaccionesRecargasCliente(cedula);
        cargarTransaccionesPuntosCliente(cedula);
        cargando(0);
    });
});
var obtenerFechaActual = function() {
    endDate = new Date();
    var day = ceros(endDate.getDate());
    var monthIndex = ceros((endDate.getMonth()) + 1);
    var year = endDate.getFullYear();
    endDate = year + '-' + monthIndex + '-' + day;
};
var ceros = function(number) {
    if (number < 10) number = '0' + number;
    return number;
};
var cargando = function(estado) {
    if (estado) {
        $("#mdl_rdn_pdd_crgnd").css("display", "block");
        $("#mdl_pcn_rdn_pdd_crgnd").css("display", "block");
    } else {
        $("#mdl_rdn_pdd_crgnd").css("display", "none");
        $("#mdl_pcn_rdn_pdd_crgnd").css("display", "none");
    }
};
var auditoria = function(descripcion, accion, trama) {
    var send = {
        "metodo": "guardarAuditoria",
        "descripcion": descripcion,
        "accion": accion,
        "trama": trama
    };
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        accept: "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminFidelizacionCliente/serviciosCliente.php",
        data: send,
        success: function(datos) {
            // console.log(datos);
        }
    });
};
var cargarDatosCliente = function(cedula) {
    var html = "";
    send = {};
    send.metodo = "cargarListaModificadores";
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        headers: {
            "Authorization": seguridad,
            "Content-Type": "application/x-www-form-urlencoded; charset=utf-8; application/json",
            "Accept": "application/json, text/javascript2"
        },
        url: urlReports + cedula,
        data: send,
        success: function(datos) {
            person = datos.data;
            // console.log(datos);
            //Auditoria
            auditoria("CONSULTAR DATOS CLIENTE", "BUSCAR CLIENTE", JSON.stringify(person));
            $("#cName").html("<strong>Nombre: </strong>" + person.name);
            $("#cDocument").html("<strong>" + person.documentType + ": </strong>" + person.document);
            if (person.status == "REGISTERED") {
                $("#cStatus").html("<strong>Estado</strong> <span class='label label-success' style='font-size: 12px'>REGISTRADO</span>");
            } else if (person.status == "PREREGISTERED") {
                $("#cStatus").html("<strong>Estado:</strong> <span class='label label-primary' style='font-size: 12px'>PREREGISTRADO</span>");
            } else {
                $("#cStatus").html("<strong>Estado:</strong> <span class='label label-danger' style='font-size: 12px'>BLOQUEADO</span>");
            }
            if (person.gender == "MALE") {
                $("#cGender").html("<strong>Género: </strong>MASCULINO");
            } else {
                $("#cGender").html("<strong>Género: </strong>FEMENINO");
            }
            $("#cBirthDate").html("<strong>Fecha de Nacimiento: </strong>" + person.birthdate);
            $("#cEmail").html("<strong>Correo: </strong>" + person.email);
            $("#cPoints").html("<strong>Puntos: </strong><span class='label label-default' style='font-size: 16px'>" + person.points + "</span>");
            $("#cBalance").html("<strong>Recargas: </strong><span class='label label-info' style='font-size: 16px'>$" + person.balance + "</span>");
            $("#cPhone").html("<strong>Teléfono: </strong>" + person.phone);
        }
    });
};
var limpiarCampos = function() {
    $("#cName").html("");
    $("#cDocument").html("");
    $("#cStatus").html("");
    $("#cGender").html("");
    $("#cBirthDate").html("");
    $("#cEmail").html("");
    $("#cPoints").html("");
    $("#cBalance").html("");
    $("#cPhone").html("");
};
var cargarConfiguracionCadena = function() {
    var send = {
        "metodo": "cargarConfiguracionCadena"
    };
    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        accept: "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminFidelizacionCliente/serviciosCliente.php",
        data: send,
        success: function(datos) {
            // console.log(datos);
            if (datos.aplicaConfiguracion) {
                $("#btnBuscar").prop('disabled', false);
                aplicaPlan = datos.aplicaConfiguracion;
                seguridad = datos.claveSeguridad;
                tokenSeguridad = datos.tokenSeguridad;
                urlLoyalty = datos.loyalty;
                urlReports = datos.reports;
                urlPoints = datos.points;
            } else {
                $("#btnBuscar").prop('disabled', true);
                alert("Esta cadena no tiene este módulo activo.");
            }
        }
    });
};
var cargarListaRestaurantes = function() {
    var send = {
        "metodo": "cargarListaRestaurantes"
    };
    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        accept: "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminFidelizacionCliente/serviciosCliente.php",
        data: send,
        success: function(datos) {
            // console.log(datos);
            restaurantes = datos;
            // alert(JSON.stringify(restaurantes));
        }
    });
};
var cargarTransaccionesRecargasCliente = function(cedula) {
    tblReloadByCustomers = $('#tblReloadByCustomers').DataTable({
        "destroy": true,
        "ajax": {
            type: 'GET',
            url: urlLoyalty + "/?document=" + cedula + "&from=2018-01-01&to=" + endDate,
            headers: {
                "Authorization": tokenSeguridad,
                "content-type": "application/json",
                "Accept": "application/json"
            },
            "dataSrc": function(json) {
                // console.log(json);
                for (var i = 0, ien = json.data.length; i < ien; i++) {
                    if (restaurantes.hasOwnProperty(json.data[i]['store_id'])) {
                        json.data[i]['store'] = restaurantes[json.data[i]['store_id']]['descripcion'];
                    } else {
                        json.data[i]['store'] = "-";
                    }
                }
                return json.data;
            }
        },
        "createdRow": function(row, data, dataIndex) {},
        "columns": [{
            "data": "movement"
        }, {
            "data": "date_and_hour"
        }, {
            "data": "total"
        }, {
            "data": "DoC"
        }, {
            "data": "store"
        }],
        "footerCallback": function(row, data, start, end, display) {
            //console.log(row, data, start, end, display);
        }
    });
};
var cargarTransaccionesPuntosCliente = function(cedula) {
    tblPointsByCustomer = $('#tblPointsByCustomer').DataTable({
        "destroy": true,
        "ajax": {
            type: 'GET',
            url: urlPoints + "/?document=" + cedula + "&from=2018-01-01&to=" + endDate,
            headers: {
                "Authorization": tokenSeguridad,
                "content-type": "application/json",
                "Accept": "application/json"
            },
            "dataSrc": function(json) {
                // console.log(json);
                for (var i = 0, ien = json.data.length; i < ien; i++) {
                    if (restaurantes.hasOwnProperty(json.data[i]['store_id'])) {
                        json.data[i]['store'] = restaurantes[json.data[i]['store_id']]['descripcion'];
                    } else {
                        json.data[i]['store'] = "-";
                    }
                }
                return json.data;
            }
        },
        "createdRow": function(row, data, dataIndex) {},
        "columns": [{
            "data": "movement"
        }, {
            "data": "date_and_hour"
        }, {
            "data": "total"
        }, {
            "data": "DoC"
        }, {
            "data": "store"
        }],
        "footerCallback": function(row, data, start, end, display) {
            //console.log(row, data, start, end, display);
        }
    });
};