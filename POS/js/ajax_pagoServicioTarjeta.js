$(document).ready(function () {
    configurarURL();
    consultarConfiguracion();
});

var TEMPORIZADORUNIRED;
var FMP_ID_ANULACION = '';
lc_control = 0;

function configurarURL() {
    URL = "config_configuracionServicioTarjeta.php";

    if (typeof modulo !== 'undefined') {
        if (modulo === 'TRANSACCIONES') {
            URL = "../facturacion/config_configuracionServicioTarjeta.php";
        }
    }
}

function consultarConfiguracion() {
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: URL,
        success: function (datos) {
            if(datos.status === true) {
                localStorage.setItem('servicio_tarjeta_aplica', datos.data.aplica);
            }else {
                alertify.error("No se consulto información servicio tarjeta");
            }
        }, error: function (err) {
            console.log(err);
            alertify.error("No se consulto información servicio tarjeta");
        }
    });
}

function validarTipoEnvio() {
    try {
        let tipoEnvio = localStorage.getItem('servicio_tarjeta_aplica');
        if (tipoEnvio == 0) {
            fn_insertaPagoTarjeta();
        } else {
            var html = "";
            var send = {"validaColeccionEstacionTipoEnvio": 1};
            $.getJSON("config_facturacion.php", send, function (datos) {
                if (datos.str > 0) {
                    if (datos.str > 1) {
                        $("#tblSWT").empty();
                        html = "";
                        for (i = 0; i < datos.str; i++) {
                            html += "<td><button class='ui-state-default ui-corner-all' onclick='fn_validaEnvioSWT2(event,\"" + datos[i]['idIntegracion'] + "\");'>" + datos[i]['Descripcion'] + "</button></td>";
                        }
                        $("#tblSWT").html(html);
                        $(".jb-shortscroll-wrapper").hide();
                        $("#modalSWT").dialog({
                            modal: true,
                            resizable: false,
                            closeOnEscape: false,
                            position: "center",
                            draggable: false,
                            width: 500,
                            heigth: 500,
                            opacity: 0,
                            show: "none",
                            hide: {
                                effect: "none",
                                duration: 0
                            },
                            duration: 500,
                            buttons: {
                                "Cancelar": function () {
                                    $(this).dialog("close");
                                    $(".jb-shortscroll-wrapper").show();
                                    $("#pagado").val("");
                                }
                            }
                        });
                    } else {
                        idIntegracion = datos[0]['idIntegracion'];
                        fn_generarPagoTarjeta(idIntegracion);
                    } 
                } else {
                    $("#pagado").val("");
                    alertify.alert("No existe la configuraci&oacute;n para pagos con tarjetas para &eacute;sta estaci&oacute;n");
                }
            });
        }
    } catch (err) {
        console.log(err);
        alertify.error("No se consulto información politica servicio tarjeta");
    }
}

function fn_validaEnvioSWT2(event, idIntegracion) {
    event.stopPropagation();
    $("#modalsubSWT").dialog("close");
    $("#modalSWT").dialog("close");
    $(".jb-shortscroll-wrapper").show();

    fn_generarPagoTarjeta(idIntegracion);
}

function validarTipoEnvioAnulacion() {
    try {
        let tipoEnvio = localStorage.getItem('servicio_tarjeta_aplica');
        if (tipoEnvio == 0) {
        } else {
            fn_generarAnulacionTarjeta();
        }
    } catch (err) {
        console.log(err);
        alertify.error("No se consulto información politica servicio tarjeta");
    }
}

function fn_generarPagoTarjeta(idIntegracion) {
    showModalPagos();

    /*let aPagar = parseFloat($("#pagoTotal").val());
    let pagadoo = parseFloat($("#pagado").val());
    const valorRestante =  aPagar - pagadoo;
    if ( valorRestante < 0 ){
        pagadoo = aPagar;
        $("#pagado").val(pagadoo);
    } else if ( pagadoo < aPagar ) {
        aPagar = pagadoo
    }*/

    valordelaTransaccionPinPadP = parseFloat($("#pagoTotal").val());
    banderaa = $("#hid_bandera_cvv").val();
    if (banderaa == 1) {
        valorTransaccionTramaDinamica = $("#pagado").val();
    } else {
        if (valordelaTransaccionPinPadP != 0) {
            valorTransaccionTramaDinamica = $("#pagado").val();
        } else {
            valorTransaccionTramaDinamica = $("#pagoGranTotal").val();
        }
    }

    $("#txtClienteCI").focus();

    let send = {
        'tipo': 'ENVIO',
        'dispositivo': idIntegracion,
        'factura': $("#txtNumFactura").val(),
        'valor': valorTransaccionTramaDinamica,
        'valorPropina': 0,
        'formaPagoIdentificador': $("#btnFormaPagoId").val()
    };

    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        contentType: "application/json",
        url: "../serviciosweb/tarjeta/cliente_tarjeta_servicio.php",
        data: JSON.stringify(send),
        timeout: 70000,
        success: function (datos) {

            if ( datos.status === true && datos.data.codigoRespuestaAutorizador === "00" && datos.data.tipoRespuesta === 0 ) 
            {
                fn_insertarFormaPagoConsumo("APROBADA",idIntegracion, datos.data.mensajeRespuesta, datos.data.rsautId);
            } else {
                fn_insertarFormaPagoConsumo("NO APROBADO", idIntegracion, datos.data.mensajeRespuesta, datos.data.rsautId);
            }

            hiddenModalPagos(datos.status);
        }, error: function (err, textStatus) {
            console.log(err);
            if (textStatus === "timeout") {
                hiddenModalPagos(false);
            } else {
                alertify.error("El pago correspondiente al servicio de tarjeta no fue efectuado.");
                hiddenModalPagos(true);
            }
        }
    });
}

/* function fn_generarAnulacionTarjeta(idIntegracion, cfac_id) {
    showModalPagos();

    let aPagar = parseFloat($("#pagoTotal").val());
    console.log(aPagar);

    $("#txtClienteCI").focus();

    let send = {
        'tipo': 'ANULACION',
        'dispositivo': idIntegracion,
        'factura': cfac_id,
        'valor': aPagar
    };

    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        contentType: "application/json",
        url: "../serviciosweb/tarjeta/cliente_tarjeta_servicio.php",
        data: JSON.stringify(send),
        timeout: 70000,
        success: function (datos) {
            console.log(datos);
            if ( datos.status === true ) {
            } else {
                alertify.error("La anulación correspondiente al servicio de tarjeta no fue efectuado.");
            }

            hiddenModalPagos();
        }, error: function (err) {
            console.log(err);
            if (textStatus === "timeout") {
                hiddenModalPagos(false);
            } else {
                alertify.error("La anulación correspondiente al servicio de tarjeta no fue efectuado.");
                hiddenModalPagos(true);
            }
        }
    });
} */

function fn_insertarFormaPagoConsumo(respuestaAutorizacion, idDispositivo, respuesta, idRespuesta) {
    if (respuestaAutorizacion == "APROBADA") {
        send = { "ingresaFormaPagoTarjeta": 1 };
        send.codFactTarjeta = $("#txtNumFactura").val();
        send.fmpIdTarjeta = $("#btnFormaPagoId").val();
        send.fmpNumSegtar = 0;
        var pagadoSinSeparador = $("#pagado").val();
        var pagadoTotalSinSeparador = $("#pagoTotal").val();
        if (pagadoTotalSinSeparador >= pagadoSinSeparador || pagadoTotalSinSeparador == pagadoSinSeparador) {
            send.frmPagoTotalTarjeta = pagadoSinSeparador;
            send.fctTotalTarjeta = pagadoSinSeparador;
        } else {
            send.frmPagoTotalTarjeta = pagadoSinSeparador;
            send.fctTotalTarjeta = pagadoSinSeparador;
        }
        send.SwtTipo = idDispositivo;
        send.aplicaServicioV2 = 1;
        if ($("#cantidad").val() == "") {
            send.propina_valor = 0;
        } else {
            send.propina_valor = parseFloat($("#cantidad").val());
        }
        $.getJSON("config_facturacion.php", send, function(datos) {
            let apiImpresion = getConfiguracionesApiImpresion();
            if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                fn_cargando(1);
                result = new apiServicioImpresion('Voucher', idRespuesta, 0, send);        
                let imprime = result['imprime'];
                let mensaje = result['mensaje'];
                if (!imprime) {
                    alertify.success('Imprimiendo Váucher...');
                    fn_cargando(0);
                } else {
                    alertify.success('Error al imprimir...'+mensaje);
                    fn_cargando(0);
                }
            } else {
                send = { "grabacanalmovimientoVoucher": 1 };
                send.respuesta = idRespuesta;
                $.getJSON("config_facturacion.php", send, function(datos) {});
            }
            lc_control = 0;
            let aPagar = parseFloat(pagadoTotalSinSeparador);
            let pagado = parseFloat(pagadoSinSeparador);
            let valor = aPagar - pagado;
            let btnNombre = $("#btnAplicarPago").attr("title");
            $("#pagoTotal").val(valor.toFixed(2));
            $("#pagado").val("");
            fn_resumenFormaPago();
            if ((valor <= 0 && btnNombre != "EFECTIVO")) {
                fn_envioFactura();
            }
        });
    } else {
        $("#div_cvv").dialog("close");
        $("#pagado").val("");
        let apiImpresion = getConfiguracionesApiImpresion();
        if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
            fn_cargando(1);
                result = new apiServicioImpresion('VoucherNo', idRespuesta, 0, send);        
                let imprime = result['imprime'];
                let mensaje = result['mensaje'];
                if (!imprime) {
                    alertify.success('Error en el Váucher...');
                    fn_cargando(0);
                } else {
                    alertify.success('Error al imprimir...'+mensaje);
                    fn_cargando(0);
                }
        }
        alertify.alert(respuesta);
        cargando(1);
        return false;
    }
}

function fn_anularFormaPagoConsumo(respuestaAutorizacion, idFormaPagoFatura, factura) {
    FMP_ID_ANULACION = idFormaPagoFatura;

    if (respuestaAutorizacion == "APROBADA") {
        send = { "cancelaTarjetaForma": 1 };
        send.can_respuesta = null;
        send.cancela_codFact = factura;
        send.cancela_idPago = FMP_ID_ANULACION;
        $.getJSON("config_anularOrden.php", send, function(datos) {
            let apiImpresion = getConfiguracionesApiImpresion();

            if ( apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1 ) {
                fn_cargando(1);

                result = new apiServicioImpresion('VoucherAnulacionTransaccion', null, null, null);

                let imprime = result['imprime'];
                let mensaje = result['mensaje'];

                if (!imprime) {
                    alertify.success('Imprimiendo váucher...');
                    fn_cargando(0);
                } else {
                    alertify.success('Error al imprimir váucher...' + mensaje);
                    fn_cargando(0);
                }
            }

            fn_formasPago();
            Tipo_FormaPago = 0;
            $("#btn_anulacancela").hide();

            $("#countdown").countdown360().settings.seconds = 0;
            $(countdown).countdown360().settings.onComplete = function(){   
                if (typeof cargarCountDown === 'function') {
                    cargarCountDown(3);
                }          
            };
        });
    } else {
        cleanIntervalModal();
        let apiImpresion = getConfiguracionesApiImpresion();
        if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
            fn_cargando(1);
            send.codigo_app = null;
            result = new apiServicioImpresion('VoucherNoCancelar', send);        
            let imprime = result['imprime'];
            let  = result['mensaje'];
            if (!imprime) {
                alertify.success('Imprimiendo Váucher...');
                fn_cargando(0);
            } else {
                alertify.success('Error al imprimir...'+mensaje);
                fn_cargando(0);
            }
        }else{
            let send = { "grabaVoucherNoCancelar": 1 };
            send.respuesta = null;
            $.getJSON("config_facturacion.php", send, function(datos) { });
            alertify.alert(tramaError);
            cleanIntervalModal();
            alertify.success('Imprimiendo Váucher...');
        }   

        return false;
    }
}

function showModalPagos(){
    cargando(0);
}

function hiddenModalPagos(status) {
    cargando(1);

    if ( status === true ) {
        return;
    }

    lc_control = 1;
    clearInterval(TEMPORIZADORUNIRED);
    $("#pagado").val("");
    alertify.alert("Expiro el tiempo de espera.Vuelva a intentarlo");

    $("#alertify-ok").click(function (event) {
        event.stopPropagation();
        lc_control = 0;
        return false;
    });
}