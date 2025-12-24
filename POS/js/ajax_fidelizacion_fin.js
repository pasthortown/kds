/* global alertify */

function validarRUC(documento) {
    var estado = false;
    var send = {"validarRUC": 1};
    send.documento = documento;
    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "../seguridades/config_usuario.php",
        data: send,
        success: function (datos) {
            if (datos["estado"] === "1") {
                estado = true;
            }
        },
        error: function (e) {
        }
    });
    return estado;
}

$(document).ready(function () {
    $("#mdl_rdn_pdd_crgnd").show();
    obtenerDatosTotalesClienteResturante();
    var estado = false;
    var ci = RESPONSE_JSONCLIENTE[0]["document"];
    if (ci.length === 13) {
        if (validarRUC(ci)) {
            estado = true;
        }
    } else if (ci.length === 10) {
        estado = true;
    }
    if (fn_aplicaAcumulacionPuntos() === 1 && estado) {
        obtenerDatosFacturaProductos();
        obtenerDatosFacturaFormaPago();
        var JSON_Transaccion = {
            "storeId": RESPONSE_JSONCLIENTE[0]["storeId"],
            "storeCode": RESPONSE_JSONCLIENTE[0]["storeCode"],
            "vendorId": RESPONSE_JSONCLIENTE[0]["vendorId"],
            "invoice": RESPONSE_JSONCLIENTE[0]["invoice"],
            "invoiceCode": RESPONSE_JSONCLIENTE[0]["invoiceCode"],
            "summary": {
                "subtotal": RESPONSE_JSONCLIENTE[0]["subtotal"],
                "vat": RESPONSE_JSONCLIENTE[0]["vat"],
                "vatTaxBase": RESPONSE_JSONCLIENTE[0]["vatTaxBase"],
                "vatCalculated": RESPONSE_JSONCLIENTE[0]["vatCalculated"],
                "total": RESPONSE_JSONCLIENTE[0]["total"]
            },
            "products": RESPONSE_JSONProducto,
            "customer": {
                "documentType": RESPONSE_JSONCLIENTE[0]["documentType"],
                "document": RESPONSE_JSONCLIENTE[0]["document"],
                "name": RESPONSE_JSONCLIENTE[0]["cli_nombres"],
                "address": RESPONSE_JSONCLIENTE[0]["address"]
            },
            "paymentMethods": RESPONSE_JSONFORMA_PAGO,
            "cashier": {
                "document": RESPONSE_JSONCLIENTE[0]["cashierDocument"], "name": RESPONSE_JSONCLIENTE[0]["cashierName"]
            },
            "token": RESPONSE_JSONCLIENTE[0]["token"]
        };
        executeWsTransaccionFB(JSON_Transaccion);
    } else {
        //if (ci !== "9999999999999") {
        fn_validaItemPagado();
        //}
        document.getElementById('PuntosAcumulados').innerHTML = "Clic en ok para continuar.";
    }
    $("#mdl_rdn_pdd_crgnd").hide();
    if (document.getElementById('PuntosAcumulados').innerHTML === "") {
        document.getElementById('PuntosAcumulados').innerHTML = "Clic en ok para continuar.";
    }
});

function fn_aplicaAcumulacionPuntos() {
    var respuesta = 0;
    var send = {"aplicaAcumulacionPuntos": 1};
    send.cfac_id = $("#hidden_numFactura").val();
    if (RESPONSE_JSONCLIENTE[0]["token"] != '') {
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../facturacion/config_facturacion.php",
            data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    respuesta = datos[0]["estado"];
                }
            }
        });
    } else {
        console.log('No se envia Token no acumula Puntos');
    }
    return respuesta;
}

function executeWsTransaccionFB(JSON_Transaccion) {
    var send = {"RegistroTransaccionFB": 1};
    send.json = JSON.stringify(JSON_Transaccion);
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/config_facturacion.php",
        data: send,
        success: function (datos) {
            console.log(datos);
            if (datos !== undefined) {
                if (datos["code"] === 200) { // ******* V1 FIDELIZACION *******
                    //if (datos["code"] == 200) { // ******* V2 FIDELIZACION *******
                    //datos = datos["data"];  // ******* V2 FIDELIZACION *******
                    document.getElementById('PuntosAcumulados').innerHTML = "Felicitaciones <br />Acumulaste  " + datos["data"]["pointsByTransaction"] + " pts.";
                } else {
                    document.getElementById('PuntosAcumulados').innerHTML = "Clic en OK para continuar."; //  document.getElementById('PuntosAcumulados').innerHTML = datos["message"];
                }
            } else {
                document.getElementById('PuntosAcumulados').innerHTML = "Clic en ok para continuar.";
            }
        }
    });
    fn_validaItemPagado();
}

function fn_validaItemPagado() {
    var txtOrdenPedidoId = $("#txtOrdenPedidoId").val();
    var txtTipoServicio = $("#txtTipoServicio").val();
    if (txtTipoServicio == 2) {
        send = {"valida_tipo_facturacion": 1};
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../facturacion/config_facturacion.php",
            data: send,
            success: (datos) => {
                facturacion = datos.rst_tipo_facturacion;
                if (facturacion == 1) {
                    cargando(0);
                    fn_timeout2();
                    //fn_esperaXMLfirmado();
                    //fin de facturacion electronica
                } else if (facturacion == 2) {
                    cfac_idd = $("#hidden_numFactura").val();
                    send = {"grabacanalmovimientoImpresionFactura": 1};
                    send.idfactura = cfac_idd;
                    $.ajax({
                        async: false,
                        type: "GET",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "../facturacion/config_facturacion.php",
                        data: send,
                        success: (datos) => {
                            fn_direccionaMesasUorden(txtTipoServicio, txtOrdenPedidoId);
                        }
                    });
                    //fin de facturacion preimpresa
                } else if (facturacion == 4) {
                    send = {"claveAcceso": 1};
                    send.factt = $("#hidden_numFactura").val();
                    send.char = "F";
                    $.getJSON("../facturacion/config_facturacion.php", send, (datos) => {
                        var apiImpresion = getConfiguracionesApiImpresion();

                        if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                            fn_cargando(1);

                            var result = new apiServicioImpresion('factura', $("#hidden_numFactura").val());
                            var imprime = result["imprime"];
                            var mensaje = result["mensaje"];
                            localStorage.removeItem("requests");
                        } else {
                            facturita = $("#hidden_numFactura").val();
                            send = {"actualizaFacturacion": 1};
                            send.nuFactu = $("#hidden_numFactura").val();
                            $.getJSON("../facturacion/config_facturacion.php", send, (datos) => {
                            });
                        }
                    });
                    //fin facturacion Plan Market
                } else if (facturacion == 3) {
                    send = {"claveAcceso": 1};
                    send.factt = $("#hidden_numFactura").val();
                    send.char = "F";
                    $.getJSON("../facturacion/config_facturacion.php", send, (datos) => {
                        send = {"grabacanalmovimientoImpresionFacturaElectronica": 1};
                        send.idfactura = $("#hidden_numFactura").val();
                        $.getJSON("../facturacion/config_facturacion.php", send, (datos) => {
                            //  fn_direccionaMesasUorden(txtTipoServicio, txtOrdenPedidoId);
                        });
                    });
                } //fin de facturacion en contingencia
            }
        });
    } else if (txtTipoServicio == 1) {
        send = {"valida_tipo_facturacion": 1};
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../facturacion/config_facturacion.php",
            data: send,
            success: (datos) => {
                facturacion = datos.rst_tipo_facturacion;
                if (facturacion == 1) {
                    cargando(0);
                    fn_timeout2();
                } else if (facturacion == 2) {
                    cfac_idd = $("#hidden_numFactura").val();
                    send = {"grabacanalmovimientoImpresionFactura": 1};
                    send.idfactura = cfac_idd;
                    $.ajax({
                        async: false,
                        type: "GET",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "../facturacion/config_facturacion.php",
                        data: send,
                        success: (datos) => {
                            //  fn_direccionaMesasUorden(txtTipoServicio, txtOrdenPedidoId);
                        }
                    });
                    //inicio facturacion tipo plan market
                } else if (facturacion == 4) {
                    send = {"claveAcceso": 1};
                    send.factt = $("#hidden_numFactura").val();
                    send.char = "F";
                    $.getJSON("../facturacion/config_facturacion.php", send, (datos) => {
                        facturita = $("#hidden_numFactura").val();
                        send = {"actualizaFacturacion": 1};
                        send.nuFactu = $("#hidden_numFactura").val();
                        $.getJSON("../facturacion/config_facturacion.php", send, (datos) => {
                            //  fn_direccionaMesasUorden(txtTipoServicio, txtOrdenPedidoId);
                        });
                    });
                    //fin facturacion Plan Market
                } else if (facturacion == 3) {
                    send = {"claveAcceso": 1};
                    send.factt = $("#hidden_numFactura").val();
                    send.char = "F";
                    $.getJSON("../facturacion/config_facturacion.php", send, (datos) => {
                        send = {"grabacanalmovimientoImpresionFacturaElectronica": 1};
                        send.idfactura = $("#hidden_numFactura").val();
                        $.getJSON("../facturacion/config_facturacion.php", send, (datos) => {
                            //   fn_direccionaMesasUorden(txtTipoServicio, txtOrdenPedidoId);
                        });
                    });
                } //fin de facturacion en contingencia
            }
        });
        //fin de tipo de servicio=1
    } else {
        alertify.error("No existe configuracion del tipo de servicio.");
        return false;
    }
}

var RESPONSE_JSONCLIENTE;

function obtenerDatosTotalesClienteResturante() {
    var send = {"obtenerDatosTotalesClienteResturante": 1};
    send.cfac_id = $("#hidden_numFactura").val();
    send.rst_id = $("#hidden_rst_id").val();
    send.cdn_id = $("#hidden_cdn_id").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/config_facturacion.php",
        data: send,
        success: (datos) => {
            if (datos !== undefined) {
                RESPONSE_JSONCLIENTE = datos;
            }
        }
    });
}

var RESPONSE_JSONProducto;

function obtenerDatosFacturaProductos() {
    var send = {"obtenerDatosFacturaProductos": 1};
    send.cfac_id = $("#hidden_numFactura").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/config_facturacion.php",
        data: send,
        success: (datos) => {
            if (datos !== undefined) {
                RESPONSE_JSONProducto = datos;
            }
        }
    });
}

var RESPONSE_JSONFORMA_PAGO;

function obtenerDatosFacturaFormaPago() {
    var send = {"obtenerDatosFacturaFormaPago": 1};
    send.cfac_id = $("#hidden_numFactura").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/config_facturacion.php",
        data: send,
        success: (datos) => {
            if (datos !== undefined) {
                RESPONSE_JSONFORMA_PAGO = datos;
            }
        }
    });
}