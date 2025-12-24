/* global alertify, netscape */

////////////////////////////////////////////////////////////////////////////////
///////FECHA CREACION: 11/05/2016 /////////////////////////////////////////////
///////DESARROLLADOR: Daniel Llerena //////////////////////////////////////////
///////DESCRIPCION: Test de impresion pantalla inicio maxpos //////////////////
///////FECHA ULTIMA MODIFICACION:           ///////////////////////////////////
///////USUARIO QUE MODIFICO:                ///////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO:                            ///////////////////
///////////////////////////////////////////////////////////////////////////////

$(document).ready(function () {
    fn_validaEstacionConfigurada();
});

function fn_abrirmodalinfo() {
    $('#modalInfo').modal('show');
    fn_infoimpresoras();
}

function fn_infoimpresoras() {
    var send;
    var html = "<thead><tr class='active'><th class='text-center'>Canal Impresi&oacute;n</th><th class='text-center'>Impresora</th><th class='text-center'>Tipo</th></tr></thead>";
    send = {"infoimpresoras": 1};
    send.estacion_ip = $("#hid_ip").val();
    $.getJSON("test_impresion/config_infoestacion.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                html = html + "<tr><td class='text-center'>" + datos[i]['canal_impresion'] + "</td><td class='text-center'>" + datos[i]['impresora'] + "</td><td class='text-center'>" + datos[i]['tipo_impresora'] + "</td></tr>";
                $("#tablaimpresoras").html(html);
                $("#btn_imprimir").show();
                document.getElementById("btn_cerrar_navegador").style.marginRight = "240px";
            }
        } else {
            html = html + '<tr><th colspan="3" class="text-center">Impresoras NO Configuradas</th></tr>';
            $("#tablaimpresoras").html(html);
            $("#btn_imprimir").show();
            document.getElementById("btn_cerrar_navegador").style.marginRight = "200px";
        }
    });
}

function fn_testimpresion() {
    var send;
    send = {"infoAplicaApiImpresion": 1};
    IDEstacion = $("#hid_ip").val();
    send.estacion = IDEstacion;

    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "test_impresion/config_infoestacion.php", data: send, success: function (datos) {
            if(datos.aplicaTienda == 1){
                let result = new apiServicioImpresionTest('test_impresion', 'pruebaImpresionApi', 0, datos);
                let imprime = result["imprime"];
                if (!imprime) {
                    alertify.success('Imprimiendo Prueba de impresion con API...');
                } else {
                    alertify.success('Error...');
                }


            }else{
            
            send.infoAplicaApiImpresion = 0;
            send.canalmovimiento_testimpresion = 1;
            $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded",
                url: "test_impresion/config_infoestacion.php", data: send, success: function (datos) {
                    alertify.success("Imprimiendo....");
                }});


            }

        }

        });



}

function fn_aplicarReplica() {
    var send;
//  var html = "<thead><tr class='active'><th class='text-center'>M&oacute;dulo</th><th class='text-center'>N&uacute;mero Lote</th><th class='text-center'>Fecha Creaci&oacute;n</th><th class='text-center'>Hora Creaci&oacute;n</th></tr></thead>";
    send = {"aplicar_replica": 1};
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "test_impresion/config_infoestacion.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
//                  html = html + "<tr><td class='text-center'>" + datos[i]['Modulo'] + "</td><td class='text-center'>" + datos[i]['NumeroDeLote'] + "</td><td class='text-center'>" + datos[i]['FechaCreacion'] + "</td><td class='text-center'>" + datos[i]['HoraCreacion'] + "</td></tr>";
//                  $("#tblReplica").html(html);
                    alertify.success("Tramas aplicadas correctamente.");
                }
//              $("#mdlErrorReplica").modal("show");
            }
//          else {
//              html = html + '<tr><th colspan="4" class="text-center">No existe replicas</th></tr>';
//              $("#tblReplica").html(html);
//          }
        }
    });

    fn_cargaErrorReplica();
}

function fn_cargaErrorReplica() {
    var send;
    var html = "<thead><tr class='active'><th class='text-center'>M&oacute;dulo</th><th class='text-center'>N&uacute;mero Lote</th><th class='text-center'>Fecha Creaci&oacute;n</th><th class='text-center'>Hora Creaci&oacute;n</th></tr></thead>";
    send = {"errores_replica": 1};
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "test_impresion/config_infoestacion.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html = html + "<tr><td class='text-center'>" + datos[i]['Modulo'] + "</td><td class='text-center'>" + datos[i]['NumeroDeLote'] + "</td><td class='text-center'>" + datos[i]['FechaCreacion'] + "</td><td class='text-center'>" + datos[i]['HoraCreacion'] + "</td></tr>";
                    $("#tblErrorReplica").html(html);
                }
                $("#mdlErrorReplica").modal("show");
            } else {
                html = html + '<tr><th colspan="4" class="text-center">No existe replicas</th></tr>';
                $("#tblReplica").html(html);
            }
        }
    });
}

function cerrarNavegador() {
    alertify.set({
        labels: {
            ok: "Si",
            cancel: "No"
        }
    });

    alertify.confirm("Desea salir del sistema.?", function (e) {
        if (e) {
            window.close();
        }
    });
}

function fn_validaEstacionConfigurada() {
    var send;
    var IPEstacion = $("#hid_ip").val();
    send = {"validaEstacion": 1};
    send.estacion_ip = IPEstacion;
    $.getJSON("test_impresion/config_infoestacion.php", send, function (datos) {
        if (datos.str > 0) {
            if (datos.existeip === 1) {
                $("#btn_apagar").show();
            } else {
                $("#btn_apagar").hide();
            }
        }
    });
}

function fn_apagarEstacion() {
    var send;
    var IPEstacion = $("#hid_ip").val();

    alertify.set({
        labels: {
            ok: "Apagar",
            cancel: "Cancelar"
        }
    });

    alertify.confirm("Est&aacute; seguro/a que desea apagar est&aacute; estaci&oacute;n.?", function (e) {
        if (e) {
            send = {"apagar_Estacion": 1};
            send.estacion_ip = IPEstacion;
            $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded",
                url: "test_impresion/config_infoestacion.php", data: send, success: function (datos) {
                }});
            alertify.alert('Apagando Estaci&oacute;n...');
        }
    });
}