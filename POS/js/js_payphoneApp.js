async function fn_payphoneAppMovilAbrirModal() {

    $("#modalTransaccionApp").show();
    var codificacion = await fn_PayPhoneObtenerClavesGeneral();
    $('#codeCountri').attr("disabled", !(codificacion.datos["modificarCodigoPais"] === 1));
    $("#codeCountri").val(codificacion.datos["countryCode"]);
}

function fn_CancelarpayphoneTransaccionAppMovil() {
    cerrarTeclados();
    $("#modalTransaccionApp").hide();
    $("#payTelefonoApp").val("");
}


async function fn_finalizarTransaccion() {
    $("#mdl_rdn_pdd_crgnd1").show();
    activarDesactivarBtnPagos("btnPagarApp", "btnCancelarApp", true);
    $("#procesandoPago").hide();
    $("#btnCancelarApp").show();
    $("#btnfinalizarTransaccion").hide();
    $("#lblInfoTransaccion").html("");
    clearInterval(TEM_ESPERA);
    if (token !== "-1" && transactionIdPayphone !== 0) {
        const datos = await   payphoneReverse();

    }
    $("#mdl_rdn_pdd_crgnd1").hide();
}


function mostrarMensajeApp(icono, titulo, mensaje) {
    Swal.fire({
        title: titulo,
        text: mensaje,
        icon: icono, //,'warning',
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'OK'
    }).then((result) => {
        pararProcesoApp();
    })
}

var token = "-1";
var jsonDTransaccion ="";
async function cobrarPagoAppMovilPayphone() {

    if ($("#payTelefonoApp").val() === "") {
        mostrarMensajeApp('warning', 'Número de telefono no válido.', "El campo número teléfono es obligatorio, no puede estar vacío.");
        return;
    }

    activarDesactivarBtnPagos("btnPagarApp", "btnCancelarApp", false);
    $("#procesandoPago").show();
    $("#procesandoPago").css({
        top: "135",
        left: "75%",
    });



    var ClientID = $("#txtNumFactura").val() + "_a_" + generarAleatorio();
    $("#txtclientIdPaypone").val(ClientID);
    var codificacion = await fn_PayPhoneObtenerClavesGeneral();

    // JSON para crear una transaccion de pago en PayPhone.
    var dataTransactionCreateTemp = {
        "phoneNumber": $("#payTelefonoApp").val(),
        "countryCode": $("#codeCountri").val(),
        "clientTransactionId": $("#txtclientIdPaypone").val(),
        "storeId": codificacion.datos["StoreId"]
    };
    token = codificacion.datos["Token"];


    var dataCreateTransaccionApp = AdjuntarValoresTransaccionParaCrearTransaccion(dataTransactionCreateTemp);
    jsonDTransaccion=dataCreateTransaccionApp;
    var responseTransaccionApp = await fn_PayPhoneCreateTransaccionApp(dataCreateTransaccionApp, codificacion.datos["Token"]);

    if (responseTransaccionApp.estado === 200) {
        TEM_ESPERA = setInterval(function () {
            esperarConfirmacionAppCliente(responseTransaccionApp.transactionId, codificacion.datos["Token"]);
        }, 3000);
    } else {




        Swal.fire({
            title: 'Pago NO Enviado',
            text: responseTransaccionApp.mensaje,
            icon: 'warning',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
        }).then((result) => {

            activarDesactivarBtnPagos("btnPagarApp", "btnCancelarApp", true);
            $("#procesandoPago").hide();
            fn_PayPhoneGuardarRespuestaAutorizacionError(responseTransaccionApp.mensaje,dataCreateTransaccionApp, jsonDTransaccion);
            if (result.value) {
                alertify.success("Intentando reverso.");
                payphoneReverseAutomaticoClientID();

            } else {
                alertify.success("Intentando reverso.");
                payphoneReverseAutomaticoClientID();
            }
        })
    }

}


function fn_PayPhoneCreateTransaccionApp(json, token) {
    return new Promise(function (resolve, reject) {

        var send = {"PayPhoneEnviarTransaccionApp": 1};
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
                if (datos.transactionId === undefined) {

                    try {
                        if (datos.errors[0].errorDescriptions[0] === undefined) {
                            respuesta = {
                                "estado": 204,
                                "mensaje": datos.message
                            }
                        } else {
                            try {
                                respuesta = {
                                    "estado": 204,
                                    "mensaje": datos.errors[0].errorDescriptions[0]
                                }
                            } catch (e) {
                                respuesta = {
                                    "estado": 204,
                                    "mensaje": datos.message
                                }
                            }


                        }
                    } catch (e) {
                        respuesta = {
                            "estado": 204,
                            "mensaje": (datos.message).toString() + "."
                        }
                    }



                } else {

                    respuesta = {
                        "estado": 200,
                        "mensaje": "Aprovado",
                        "transactionId": datos.transactionId
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

var TEM_ESPERA = 0;
var contad = 0;
var transactionIdPayphone = 0;
async function esperarConfirmacionAppCliente(idTransaccion, token) {
    const datos = await fn_PayPhoneObtenerDatosTransaccionApp(idTransaccion, token);
    transactionIdPayphone = datos.transactionId;

    $("#lblInfoTransaccion").html("Esperando respuesta del cliente...");
    $("#btnfinalizarTransaccion").show();
    $("#btnCancelarApp").hide();

    if (datos.transactionStatus !== "Pending") {
        $("#lblInfoTransaccion").html("");
        $("#btnfinalizarTransaccion").hide();
        $("#btnCancelarApp").show();
        guardarConfirmacionAppCliente(datos, idTransaccion, token);
    }
}


function fn_PayPhoneObtenerDatosTransaccionApp(transactionId, token) {
    return new Promise(function (resolve, reject) {

        var send = {"PayPhoneObtenerDatosTransaccion": 1};
        send.token = token;
        send.transactionId = transactionId;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function (datos) {
                resolve(datos);
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


//$("#cardNumberSinEnmascarar").val()
async function guardarConfirmacionAppCliente(datosCompletosTransaccion, idTransaccion, token) {

    $("#cardNumberSinEnmascarar").val(datosCompletosTransaccion.bin);

    clearInterval(TEM_ESPERA);

    activarDesactivarBtnPagos("btnPagarApp", "btnCancelarApp", true);
    $("#procesandoPago").hide();
    $("#procesandoPago").css({
        top: "280",
        left: "80%",
    });

    console.log("respuesta ", datosCompletosTransaccion);
    switch (datosCompletosTransaccion.statusCode) {
        case  3:

            // Insertrar Resultados en la tabla requerimiento Autorizacion.
            var resultadoRequerimientoAutorizacion = await fn_PayPhoneGuardarRespuestaAutorizacion(datosCompletosTransaccion, "CT", "OK", jsonDTransaccion);

            if (resultadoRequerimientoAutorizacion.estado !== 200) {
                fn_PayPhoneGuardarRespuestaAutorizacionError(resultadoRequerimientoAutorizacion.mensaje, jsonDTransaccion);
                activarDesactivarBtnPagos("btnPagarApp", "btnCancelarApp", true);
                $("#procesandoPago").hide();
                Swal.fire({
                    title: 'Pago NO realizado',
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
                    } else {
                        alertify.success("Intentando reverso.");
                        payphoneReverseAutomaticoClientID();
                    }
                })
                return
            }

            cerrarTeclados();
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
                    fn_payphoneGuadarDatosRespuesta(resultadoRequerimientoAutorizacion.transaccionID, 8); // RSauid
                }
            })


            break;

        case  2:

            Swal.fire({
                title: 'Pago NO realizado..',
                text: datosCompletosTransaccion.message,
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            }).then((result) => {
                fn_PayPhoneGuardarRespuestaAutorizacionError(datosCompletosTransaccion.message, jsonDTransaccion);
                if (result.value) {
                    alertify.success("Intentando reverso.");
                    payphoneReverseAutomaticoClientID();
                } else {
                    alertify.success("Intentando reverso.");
                    payphoneReverseAutomaticoClientID();
                }
            })


            break;

        default:
            break;
    }
}



function payphoneReverse() {
    return new Promise(function (resolve, reject) {
        var send = {"payphoneReverse": 1};
        send.transaccionId = transactionIdPayphone;
        send.token = token;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function (datos) {
                console.log("REspuesta de cancelar", datos);
                var respuesta;
                if (datos.errorCode === undefined) {

                    if (datos) {
                        respuesta = {
                            "estado": 200,
                            "mensaje": "Reversado Corretamente"
                        }
                    } else {
                        respuesta = {
                            "estado": 204,
                            "mensaje": datos.message
                        }
                    }

                } else {
                    respuesta = {
                        "estado": 204,
                        "mensaje": datos.message
                    }
                }
                resolve(respuesta);
            },
            error: function (e) {
                reject({
                    "estado": 500,
                    "mensaje": "Error de conexion."
                });
            }
        });
    })
}



