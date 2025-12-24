/*
 DESARROLLADO POR JOSE FERNANDEZ
 DESCRIPCION: PANTALLA DE FUNCIONES DEL GERENTE
 TABLAS: PANTALLA,PERMISOS_PERFIL
 FECHA CREACION: 25/03/2014
 FECHA ULTIMA MODIFICACION: 26/04/2016
 USUARIO QUE MODIFICO: DARWIN MORA
 DECRIPCION ULTIMO CAMBIO: AGREGAR BOTON INTERFACE DE VENTAS SG WEB
 FECHA ULTIMA MODIFICACION: 07/07/2016
 USUARIO QUE MODIFICO: CHRISTIAN PINTO
 DECRIPCION ULTIMO CAMBIO: CIERRE PERIODO ABIERTO MAS DE UN DIA
 */

/* global alertify */

$(document).ready(function() {
    alertify.set({
        labels: {
            ok: "Apagar",
            cancel: "Cancelar"
        }
    });

    var send;
    var html = "";
    send = { "consultapantallaGerente": 1 };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_funcionesGerente.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    //interface SG Web
                    if (datos[i]['pnt_Nombre_Mostrar'] == 'Interface SGWeb') {
                        html = html + "<input type='button' onclick='fn_generar_interface()' class='btnFuncionGerente boton' value='" + datos[i]['pnt_Nombre_Mostrar'] + "'/>";
                    } else {
                        html = html + "<input type='button' onclick='fn_ruta(\"" + datos[i]['pnt_id'] + "\",\"" + datos[i]['pnt_Nombre_Formulario'] + "\", \"" + datos[i]['ruta'] + "\")' class='btnFuncionGerente boton' value='" + datos[i]['pnt_Nombre_Mostrar'] + "'>";
                    }
                    $("#cntBotones").html(html);
                }
            } else {
                alertify.alert("No tiene los suficientes permisos para ingresar a esta pantalla.");
                $("#alertify-ok").click(function() {
                    $("#funciones_gerente").dialog("close");
                    fn_obtenerMesa();
                });
            }
        }
    });
});

function fn_ruta(id_pantalla, nombreformulario, ruta) {
    if (ruta === "retiro_fondo") {
        window.location.replace("../" + ruta + "/" + nombreformulario + "?bandera=NULL");
    } else {
        window.location.replace("../" + ruta + "/" + nombreformulario + "");
    }
}

function fn_apagarEstacion() {
    var send;
    alertify.confirm("Est&aacute; seguro/a que desea apagar est&aacute; estaci&oacute;n.", function(e) {
        if (e) {
            send = { "apagar_Estacion": 1 };
            $.ajax({
                async: false,
                type: "GET",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_funcionesGerente.php",
                data: send,
                success: function(datos) {
                    window.location.replace("../index.php");
                }
            });
            alertify.alert('Apagando...');
        }
    });
}

function fn_obtenerMesa() {
    if ($("#banderaCierrePeriodo").val() === 'FinDeDiaCierrePeriodo') {
        window.location.replace("../corteCaja/corteCaja.php");
    } else {
        window.location.replace("../index.php");
    }
}

function fn_reiniciarImpresion() {
    var send;
    send = { "reiniciarImpresion": 1 };
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_funcionesGerente.php",
        data: send,
        success: function(datos) {
            alertify.success('Reiniciando...');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

var reiniciarTurnero = function() {
    var send;
    send = { "configuracionTurnero": 1 };
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_funcionesGerente.php",
        data: send,
        success: function(datos) {
            if (datos.activo) {
                var urlTurnero = datos.url;
                /* 
                 * REINICIO DE TURNERO
                 */
                $.ajax({
                    async: false,
                    type: "POST",
                    dataType: "json",
                    contentType: "application/x-www-form-urlencoded",
                    url: urlTurnero + "/reload",
                    success: function(datos) {
                        alertify.success('Reiniciando Turnero...');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(jqXHR);
                        alert(textStatus);
                        alert(errorThrown);
                    }
                });
            } else {
                alertify.alert('Turnero no configurado');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
};