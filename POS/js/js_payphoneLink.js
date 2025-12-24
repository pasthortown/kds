function AdjuntarValoresTransaccionParaCrearTransaccionLink(jsonCrearT) {

    var json = JSON_TOTALES_FACTURA;
    var jsonCrearTransaccion = jsonCrearT
    for (var clave in json) {
        if (json.hasOwnProperty(clave)) {
            if (json[clave]["descripcion"] !== undefined)
                var Cadenasubtotal = json[clave]["descripcion"];

            if (Cadenasubtotal.indexOf("SUBTOTAL") === 0) {

                if (Cadenasubtotal.indexOf("SUBTOTAL ") === 0) {
                    var posicioEspacio = Cadenasubtotal.indexOf(" ") + 1;
                    var posicionPorcentaje = Cadenasubtotal.indexOf("%");
                    var porcentajeIva = Cadenasubtotal.substring(posicioEspacio, posicionPorcentaje);

                    if (porcentajeIva === "0") {


                        var AmountWithoutTax = (json[clave]["valor"] * 100).toString();
                        if (AmountWithoutTax.indexOf(".") >= 0) {
                            jsonCrearTransaccion["amountWithoutTax"] = Math.round((json[clave]["valor"] * 100));
                        } else {
                            jsonCrearTransaccion["amountWithoutTax"] = parseInt(json[clave]["valor"] * 100);
                        }



                    } else {

                        var amountWithTax = (json[clave]["valor"] * 100).toString();
                        if (amountWithTax.indexOf(".") >= 0) {
                            jsonCrearTransaccion["amountWithTax"] = Math.round((json[clave]["valor"] * 100));
                        } else {
                            jsonCrearTransaccion["amountWithTax"] = parseInt(json[clave]["valor"] * 100);
                        }

                    }
                }
            }

            if (Cadenasubtotal.indexOf("IVA") === 0) {
                if (Cadenasubtotal.indexOf("IVA ") === 0) {
                    var posicioEspacio = Cadenasubtotal.indexOf(" ") + 1;
                    var posicionPorcentaje = Cadenasubtotal.indexOf("%");
                    var porcentajeIva = Cadenasubtotal.substring(posicioEspacio, posicionPorcentaje);
                    if (porcentajeIva !== "0") {

                        var Tax = (json[clave]["valor"] * 100).toString();
                        if (Tax.indexOf(".") >= 0) {
                            jsonCrearTransaccion["tax"] = Math.round((json[clave]["valor"] * 100));
                        } else {
                            jsonCrearTransaccion["tax"] = parseInt(json[clave]["valor"] * 100);
                        }


                    }
                }
            }

            if (Cadenasubtotal.indexOf("SERVICIO") === 0) {
                if (Cadenasubtotal.indexOf("SERVICIO ") === 0) {
                    var posicioEspacio = Cadenasubtotal.indexOf(" ") + 1;
                    var posicionPorcentaje = Cadenasubtotal.indexOf("%");
                    var porcentajeIva = Cadenasubtotal.substring(posicioEspacio, posicionPorcentaje);
                    if (porcentajeIva !== "0") {

                        var Service = (json[clave]["valor"] * 100).toString();
                        if (Service.indexOf(".") >= 0) {
                            jsonCrearTransaccion["service"] = Math.round((json[clave]["valor"] * 100));
                        } else {
                            jsonCrearTransaccion["service"] = parseInt(json[clave]["valor"] * 100);
                        }
                    }
                }
            }

            if (Cadenasubtotal.indexOf("TOTAL") === 0) {

                var amount = (json[clave]["valor"] * 100).toString();
                if (amount.indexOf(".") >= 0) {
                    jsonCrearTransaccion["amount"] = Math.round((json[clave]["valor"] * 100));
                } else {
                    jsonCrearTransaccion["amount"] = parseInt(json[clave]["valor"] * 100);
                }




            }
            Cadenasubtotal = "";
        }
    }



    return jsonCrearTransaccion;

}

function fn_CancelarpayphoneTransaccionLinkPagos() {
    cerrarTeclados();
    $("#correoLink").val("");
    $("#modalTransaccionLink").hide();

}


function guardarMensajeDB(mensaje) {
    
    cerrarTeclados();
    clearInterval(INTERVAL_ESPERA_LINK);
    clearInterval(INTERVAL_CONTADOR);
    tiempoEsperaTransaccion = 0;
    $("#iconoCargandoLink").hide();
    $("#TextoinfoAccion").html("");
    $("#contenedorCirculo").hide();
    $("#conteo").html("");
    activarDesactivarBtnPagos("btnPagarLinks", "cancelarPagoLink", true);
    $("#btncancelarEsperaCliente").hide();
    $("#cancelarPagoLink").show();
    fn_PayPhoneGuardarRespuestaAutorizacionError(mensaje);

}
function pararProcesoCorreo() {
    console.log("Cancelar");
    cerrarTeclados();
    clearInterval(INTERVAL_ESPERA_LINK);
    clearInterval(INTERVAL_CONTADOR);
    tiempoEsperaTransaccion = 0;
    $("#iconoCargandoLink").hide();
    $("#TextoinfoAccion").html("");
    $("#contenedorCirculo").hide();
    $("#conteo").html("");

    activarDesactivarBtnPagos("btnPagarLinks", "cancelarPagoLink", true);
    fn_PayPhoneGuardarRespuestaAutorizacionError("Cancelado por el cajero.", jsonDTransaccionLink);
    $("#btncancelarEsperaCliente").hide();
    $("#cancelarPagoLink").show();


}

function fn_modalAplicarFormaPagoLinkPayphone() {
    $("#modalTransaccionLink").show();
}

function validarEmail(email) {
    var emailRegex = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    if (emailRegex.test(email)) {
        return true;
    } else {
        return false;
    }
}
var jsonDTransaccionLink = "";
async function AplicarFormaPagoLinkPayphone() {
    cerrarTeclados();

    if ($("#correoLink").val() === "") {
        mostrarMensaje('warning', 'Campo vacío', "Debe llenar el campo del correo.", true);
        $("#correoLink").focus();
        return;
    }

    if (!validarEmail($("#correoLink").val())) {
        mostrarMensaje('warning', 'Correo No válido', "Verifique que el correo ingresado este correcto.", true);
        return;
    }

    var ClientID = $("#txtNumFactura").val().substring(0, 4) + "_" + generarAleatorio();//
    $("#txtclientIdPaypone").val(ClientID);

    // Obtengo Token, StoreId y currency para posterior enviar en la transaccion de generar link.
    const data = await fn_PayPhoneObtenerClavesGeneral();

    // Obtener configuraciones SMTP
    const configSMTPyCorreo = await fn_obtenerConfiguracionSMTPyCorreo();

    // Si ocurre un error de configuración, advertir con un mensaje.
    if (configSMTPyCorreo.estado !== 200) {
        mostrarMensaje('warning', 'Configuraciones de correo incompletas.', configSMTPyCorreo.mensaje, false);
        fn_PayPhoneGuardarRespuestaAutorizacionError(configSMTPyCorreo.mensaje);
        return;
    }


    // FIN VALIDACIONES Y OBTENCION DE DATA.
    $("#TextoinfoAccion").html("Enviando correo..");
    tiempoEsperaTransaccion = configSMTPyCorreo.tiempoEsperaTransaccion;
    activarDesactivarBtnPagos("btnPagarLinks", "cancelarPagoLink", false);

    $("#iconoCargandoLink").show();
    $("#iconoCargandoLink").css({
        top: "135",
        left: "75%"
    });
    $("#TextoinfoAccion").show(1500);




    // Añadir los valores de total e iva a la transaccion
    var dataObtenerLinkPayphone = AdjuntarValoresTransaccionParaCrearTransaccionLink(
            {
                "clientTransactionId": $("#txtclientIdPaypone").val(),
                "storeId": data.datos["StoreId"],
                "currency": data.datos["currency"]
            }
    );
    jsonDTransaccionLink = dataObtenerLinkPayphone;


    // Genero el link de pagos para enviar al cliente a su correo.
    const Link = await    fn_PayPhoneObtenerLinkDePagosPayphone(dataObtenerLinkPayphone, data.datos["Token"]);
    if (Link.estado !== 200) {
        mostrarMensaje('warning', 'Link de pagos NO generado', Link.mensaje, false);
        fn_PayPhoneGuardarRespuestaAutorizacionError(Link.mensaje, dataObtenerLinkPayphone);
        return;
    }


    const htmlCorreo = await fn_obtenerHTMLCorreroPayphone(Link.link);
    if (htmlCorreo.estado !== 200) {
        fn_PayPhoneGuardarRespuestaAutorizacionError(htmlCorreo.mensaje, dataObtenerLinkPayphone);
        mostrarMensaje('warning', 'Contenido del correo no generado', htmlCorreo.mensaje, false);
        return;
    }


    const respuestaEnvioCorreo = await   enviarCorreo(configSMTPyCorreo, htmlCorreo.html, $("#correoLink").val());
    if (respuestaEnvioCorreo.statusCode !== 200) {
        fn_PayPhoneGuardarRespuestaAutorizacionError(respuestaEnvioCorreo.message, dataObtenerLinkPayphone);
        mostrarMensaje('warning', 'Correo no enviado.', respuestaEnvioCorreo.message, false);
        return;
    }
    alertify.success("¡Correo enviado exitosamente!");


    $("#iconoCargandoLink").hide();
    $("#cancelarPagoLink").hide();
    $("#btncancelarEsperaCliente").show();

    $("#TextoinfoAccion").html("Esperando respuesta del cliente...");
    $("#contenedorCirculo").show(1000);


    $("#conteo").html(tiempoEsperaTransaccion);


    INTERVAL_CONTADOR = setInterval(function () {
        EjecutarcronometroContador();
    }, 1000);


    INTERVAL_ESPERA_LINK = setInterval(function () {
        esperarRespuestaCliente(data.datos["Token"], ClientID);
    }, 4000);



}

var tiempoEsperaTransaccion = 120;
function mostrarMensaje(icono, titulo, mensaje, guardarLog = false) {
    Swal.fire({
        title: titulo,
        text: mensaje,
        icon: icono, //,'warning',
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (guardarLog) {
            guardarMensajeDB(mensaje);
        }

    })
}

function fn_obtenerConfiguracionSMTPyCorreo() {
    return new Promise(function (resolve, reject) {
        var send = {"obtenerConfiguracionSMTPyCorreo": 1};
        send.rst_id = $("#txtRestaurante").val();
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function (datos) {
                resolve(datos[0]);
            }
            , error: function (e) {
                resolve({
                    "estado": 500,
                    "mensaje": "Error de conexion."
                });
            }

        });
    })
}

function fn_PayPhoneObtenerLinkDePagosPayphone(json, token) {

    return new Promise(function (resolve, reject) {
        var send = {"PayPhoneObtenerLinkDePagosPayphone": 1};
        send.token = token;
        send.json = json;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function (datos) {

                var respuesta;

                if (datos.errors === undefined) {
                    respuesta = {
                        "link": datos,
                        "estado": 200,
                        "mensaje": "Ok"
                    }
                } else {
                    try {
                        try {
                            respuesta = {
                                "estado": 204,
                                "mensaje": datos.errors[0].errorDescription
                            }
                        } catch (e) {
                            respuesta = {
                                "estado": 204,
                                "mensaje": datos.message
                            }
                        }
                    } catch (e) {
                        respuesta = {
                            "estado": 204,
                            "mensaje": (datos.message).toString() + "."
                        }
                    }
                }
                resolve(respuesta);
            },
            error: function (e) {
                resolve({
                    "estado": 500,
                    "mensaje": "Error de conexion."
                });
            }
        });
    })
}


function fn_obtenerHTMLCorreroPayphone(link) {
    return new Promise(function (resolve, reject) {
        var send = {"obtenerHTMLCorreroPayphone": 1};
        send.rst_id = $("#txtRestaurante").val();
        send.cfac_id = $("#txtNumFactura").val();
        send.link = link;

        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function (datos) {

                if (datos.str > 0) {
                    resolve({
                        "estado": 200,
                        "html": datos[0].html
                    });
                } else {
                    resolve({
                        "estado": 500,
                        "mensaje": "No se pudo generar el contenido de información para el envío del correo."
                    });
                }

            }
            , error: function (e) {

                resolve({
                    "estado": 500,
                    "mensaje": "Error de conexion."
                });
            }

        });

    })
}


function enviarCorreo(configuracionSMTP, htmlCorreo, emailDestino) {
    return new Promise(function (resolve, reject) {

        var send = {"enviarMail": 1};
        send.rst_id = $("#txtRestaurante").val();
        send.host = configuracionSMTP.host;
        send.puerto = configuracionSMTP.puerto;
        send.correo = configuracionSMTP.correo;
        send.password = configuracionSMTP.password;
        send.nombreUsuario = configuracionSMTP.nombreUsuario;
        send.asunto = configuracionSMTP.asunto;
        send.htmlCorreo = htmlCorreo;
        send.emailDestino = emailDestino;

        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../SendMail/EnviarEmail.php",
            data: send,
            success: function (datos) {
                resolve(datos);
            }
            , error: function (e) {

                resolve({
                    "estado": 500,
                    "mensaje": "Error al tratar de enviar el correo."
                });
            }

        });

    })




}


var INTERVAL_ESPERA_LINK = 0;
var INTERVAL_CONTADOR = 0;

function  EjecutarcronometroContador() {

    $("#conteo").html(tiempoEsperaTransaccion);
    if (tiempoEsperaTransaccion === 0) { //  ¿Se acabo el tiempo ?

        clearInterval(INTERVAL_ESPERA_LINK);
        clearInterval(INTERVAL_CONTADOR);
        $("#TextoinfoAccion").hide();
        $("#contenedorCirculo").hide();
        activarDesactivarBtnPagos("btnPagarLinks", "cancelarPagoLink", true); // True: ACtivar boton pagar, cancelar.
        $("#cancelarPagoLink").show();
        $("#btncancelarEsperaCliente").hide();
        mostrarMensaje('warning', 'Tiempo agotado.', "El tiempo para que el usuario complete su transacción se ha agotado.", true);

    } else {
        tiempoEsperaTransaccion--;
    }

}



async function esperarRespuestaCliente(token, ClientID) {
    const TransaccionLink = await fn_PayPhoneObtenerDatosTransaccionClientID(token, ClientID);
    console.log("Response : ", TransaccionLink);

    // Verificar que se aprobo el pago.
    if (TransaccionLink.estado === 200) {
        clearInterval(INTERVAL_CONTADOR);
        clearInterval(INTERVAL_ESPERA_LINK);

        tiempoEsperaTransaccion = 0;
        cerrarTeclados();
        $("#modalTransaccionLink").hide();
        GuardarTransaccionAprovada(TransaccionLink.respuesta);
    }

}

function fn_PayPhoneObtenerDatosTransaccionClientID(token, clientID) {
    return new Promise(function (resolve, reject) {

        var send = {"PayPhoneObtenerDatosTransaccionClientID": 1};
        send.token = token;
        send.clientID = clientID;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function (datos) {

                var codigo = datos.errorCode;
                var respuesta;
                switch (codigo) {
                    case 20:
                        respuesta = {
                            "estado": 204,
                            "mensaje": datos.message
                        };
                        break;

                    default:
                        respuesta = {
                            "estado": 200,
                            "respuesta": datos[0]
                        };
                        break;
                }

                resolve(respuesta);

            },
            error: function (e) {
                resolve({
                    "estado": 500,
                    "mensaje": "Error de conexion."
                });
            }
        });


    })
}

async function GuardarTransaccionAprovada(Transaccion) {

    $("#cardNumberSinEnmascarar").val(Transaccion.bin);
    // Insertrar Resultados en la tabla requerimiento Autorizacion.
    var resultadoRequerimientoAutorizacion = await fn_PayPhoneGuardarRespuestaAutorizacion(Transaccion, "CT", "OK", jsonDTransaccionLink);

    if (resultadoRequerimientoAutorizacion.estado !== 200) {

        fn_PayPhoneGuardarRespuestaAutorizacionError(resultadoRequerimientoAutorizacion.mensaje, jsonDTransaccionLink);

        $("#procesandoPago").hide();
        Swal.fire({
            title: 'Ocurrio un Error.',
            text: resultadoRequerimientoAutorizacion.mensaje,
            icon: 'error',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.value) {
                alertify.success("Intentando reverso.");
                payphoneReverseAutomaticoClientID();
            }
        })
        return
    }

    $("#procesandoPago").hide(10);
    $("#modalPay").hide();
    cerrarTeclados();


    activarDesactivarBtnPagos("btnPagarLinks", "cancelarPagoLink", true);



    Swal.fire({
        title: 'Pago exitoso',
        text: "",
        icon: 'success',
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.value) {

            fn_payphoneGuadarDatosRespuesta(resultadoRequerimientoAutorizacion.transaccionID, 8); // RSauid
        } else {
            fn_payphoneGuadarDatosRespuesta(resultadoRequerimientoAutorizacion.transaccionID, 8); //  en caso que de click fuera del pop up-
        }
    })



}