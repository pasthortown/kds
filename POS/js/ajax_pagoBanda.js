var myVariable;

/*==========PAGOS CON BANDA==========*/

/*abre modale del teclado cvv*/
function fn_muestraTecladoCVV(tipoTransaccion,idDispositivo,secuencia,caracTarjeta,codigoFactura,idFormaPagoFatura,modulo) 
{
    
    $("#txt_cvv").val("");
    $("#div_cvv").dialog({
        modal: true,
        autoOpen: false,
        show: {
            effect: "blind",
            duration: 500
        },
        hide: {
            effect: "explode",
            duration: 500
        },
        width: "auto",
        open: function (event, ui) {
            $(".ui-dialog-titlebar").hide();
            $('#fn_okCVV').attr('disabled', false);
        }
    });
    $("#div_cvv").dialog("open");
  
    $("#fn_okCVV").unbind().click(function () {
        if ($("#txt_cvv").val() == "") 
        {
            alertify.error("Ingrese CVV.");
            return false;
        }        
        if(secuencia[0]=='Armar_Trama')
        {
            $("#div_cvv").dialog("close");
            var longitud=0;            
            longitud=secuencia.length;            
            secuencia = secuencia.splice(1,longitud);              
            Armar_Trama_Dinamica(tipoTransaccion, idDispositivo,secuencia,caracTarjeta,$("#txt_cvv").val(),$("#txtNumFactura").val(),$("#btnFormaPagoId").val(),'FACTURACION');              
        }
    });
}

/*----------------------------------------------------------------------------------------------------
 Funci�n para agregar un n�mero del teclado del CVV PARA PAGOS con banda
 -----------------------------------------------------------------------------------------------------*/
function fn_agregarNumeroCVV(valor) 
{ 
    lc_cantidad = document.getElementById("txt_cvv").value;
    if (lc_cantidad == 0 && valor == ".") {
        //si escribimos una coma al principio del n�mero
        document.getElementById("txt_cvv").value = "0."; //escribimos 0.
        coma2 = 1;
    } else {
        //continuar escribiendo un n�mero
        if (valor == "." && coma2 == 0) {
            //si escribimos una coma decimal p�r primera vez
            lc_cantidad = lc_cantidad + valor;
            document.getElementById("txt_cvv").value = lc_cantidad;
            coma2 = 1; //cambiar el estado de la coma  
        }
        //si intentamos escribir una segunda coma decimal no realiza ninguna acci�n.
        else if (valor == "." && coma2 == 1) {
        }
        //Resto de casos: escribir un n�mero del 0 al 9: 	 
        else {
            $("#txt_cvv").val('');
            lc_cantidad = lc_cantidad + valor;
            document.getElementById("txt_cvv").value = lc_cantidad;
        }
    }
    fn_focusLectorCVV();
}

function fn_focusLectorCVV() 
{
    $("#txt_cvv").focus();
}

function fn_eliminarCantidadCVV() 
{
    var lc_cantidad = document.getElementById("txt_cvv").value.substring(0, document.getElementById("cantidad").value.length - 1);
    if (lc_cantidad == "") { lc_cantidad = ""; coma2 = 0;}
    if (lc_cantidad == ".") { coma2 = 0; }
    document.getElementById("txt_cvv").value = lc_cantidad;
    fn_focusLectorCVV();
}

function fn_okCVV(event) 
{
    event.stopPropagation();
    if ($("#txt_cvv").val() == "") 
    {
        alertify.error("Ingrese CVV.");
        return false;
    }
    $("#fn_okCVV").attr("disabled", true);
    bandera = $("#hid_bandera_cvv").val();
    formaPagoId = $("#hid_descTipoFp").val();
    if (bandera == 1) {
        if (formaPagoId == 'TARJETA DE DEBITO') {
            send = {"muestraTipoCuenta": 1};
            $.getJSON("config_descuentos.php", send, function (datos) {
                if (datos.str > 0) {
                    $("#div_tipoCuentaTarjeta").empty();
                    banderatarjeta = 'Debito';
                    tipo_tran = '01';
                    for (i = 0; i < datos.str; i++) {
                        html = "<table>";
                        html += "<tr><td><button style='height:60px; width:270px;' onclick=fn_armaTrama(banderatarjeta," + datos[i]['tptar_id'] + ",tipo_tran) id='" + datos[i]['tptar_id'] + "'>" + datos[i]['tptar_descripcion'] + "</button></td></tr>";
                        html += "</table>";
                        $("#div_tipoCuentaTarjeta").append(html);
                    }
                    $("#div_tipoCuentaTarjeta").dialog({
                        modal: true,
                        autoOpen: false,
                        show: {
                            effect: "blind",
                            duration: 500
                        },
                        hide: {
                            effect: "explode",
                            duration: 500
                        },
                        width: "auto",
                        heigh: "auto",
                    });
                    $("#div_tipoCuentaTarjeta").dialog("open");
                }
            });
            //fin tarjeta de debito
        } else if (formaPagoId == 'TARJETA DE CREDITO') {
            banderatarjeta = 'Credito'
            fn_armaTrama(banderatarjeta, 3, '01');
        } else {
            alertify.alert("Forma de pago configurada incorrectamente.");
            return false;
        }
    } else if (bandera == 2) {
        if (formaPagoId == 'TARJETA DE DEBITO') {
            send = {"muestraTipoCuenta": 1};
            $.getJSON("config_descuentos.php", send, function (datos) {
                if (datos.str > 0) {
                    $("#div_tipoCuentaTarjeta").empty();
                    banderatarjeta = 'Debito';
                    tipo_tran = '03';
                    for (i = 0; i < datos.str; i++) {
                        html = "<table>";
                        html += "<tr><td><button style='height:60px; width:270px;' onclick=fn_armaTrama(banderatarjeta," + datos[i]['tptar_id'] + ",tipo_tran) id='" + datos[i]['tptar_id'] + "'>" + datos[i]['tptar_descripcion'] + "</button></td></tr>";
                        html += "</table>";
                        $("#div_tipoCuentaTarjeta").append(html);
                    }
                    $("#div_tipoCuentaTarjeta").dialog({
                        modal: true,
                        autoOpen: false,
                        show: {
                            effect: "blind",
                            duration: 500
                        },
                        hide: {
                            effect: "explode",
                            duration: 500
                        },
                        width: "auto",
                        heigh: "auto",
                    });
                    $("#div_tipoCuentaTarjeta").dialog("open");
                }
            });
        } else if (formaPagoId == 'TARJETA DE CREDITO') {
            banderatarjeta = 'Credito'
            fn_armaTrama(banderatarjeta, 3, '03');
        } else {
            alertify.alert("Forma de pago configurada incorrectamente.");
            return false;
        }
    }
}
/*
function fn_armaTrama(bandera, tptar, transaccion) 
{
    valordelaTransaccion = parseFloat($("#pagoTotal").val());
    send = {"armaTramaSWTbanda": 1};
    send.tipoTransaccion = transaccion;
    send.numMovimiento = $("#txtNumFactura").val();
    send.formaIdPagoFact = $("#btnFormaPagoId").val();
    send.tipoTarjeta = tptar;
    send.trackTarjeta = $("#txt_trama").val();
    send.cvvtarjeta = $("#txt_cvv").val();
    if ($("#cantidad").val() == '') {
        send.prop_valor = 0;
    } else {
        send.prop_valor = parseFloat($("#cantidad").val());
    }
    if (bandera == 1) {
        send.valorTransaccionBanda = $("#pagado").val();
    } else {
        if (valordelaTransaccion != 0) {
            send.valorTransaccionBanda = $("#pagado").val();
        } else {
            send.valorTransaccionBanda = $("#pagoGranTotal").val();
        }
    }
    send.tipoEnvioBanda = lc_tipoEnvio;
    $.getJSON("config_facturacion.php", send, function (datos) {
        $("#txt_cvv").val('');
        $("#div_cvv").dialog("close");
        lc_control = 0;
        cargando(0);
        fn_timeoutBanda();
        banderas = $("#hid_bandera_cvv").val();
        //alert(banderas);
        if (banderas == 1) {
            myVariable = setInterval(function () {
                fn_esperaRespuestaBanda();
            }, 1000);
        } else if (banderas == 2) {
            myVariable = setInterval(function () {
                fn_esperaRespuestaBandaAnulacion();
            }, 1000);
        }
    });
}
*/
function fn_canCVV() {
    $("#pagado").val("");
    $("#div_cvv").dialog("destroy");
}

function fn_esperaRespuestaBanda() 
{
    send = {"esperaRespuestaRequerimientoAutorizacion": 1};
    send.cfac = $("#txtNumFactura").val();
    $.ajax({async: true, url: "config_facturacion.php", data: send, dataType: "json",
        success: function (datos) {
            if (datos.existe != 2) {
                fn_funcionMuestraRespuesta(datos.rsaut_respuesta, datos.cres_codigo, datos.fpf_id, datos.rsaut_id, datos.errorTrama, datos.codigoAutorizador);
            }
        }
    });
}

function  fn_funcionMuestraRespuesta(respuesta, codigoRespuesta, formadePago, idRespuesta, tramaError, codigoAutorizador) 
{
    timeOut = clearTimeout(timeOut);
    clearInterval(myVariable);
    $("#countdown").countdown360().stop();
    $("#txt_trama").val("");
    lc_control = 1;
    cargando(1);
    if (tramaError == 1) {
        if (codigoRespuesta == "00" && codigoAutorizador != "04") {
            send = {"ingresaFormaPagoTarjeta": 1};
            send.codFactTarjeta = $("#txtNumFactura").val();
            send.fmpIdTarjeta = $("#btnFormaPagoId").val();
            send.fmpNumSegtar = 0;
            if ($("#pagoTotal").val() >= $("#pagado").val() || $("#pagoTotal").val() == $("#pagado").val()) {
                send.frmPagoTotalTarjeta = $("#pagado").val();
                send.fctTotalTarjeta = $("#pagado").val();
            } else {
                send.frmPagoTotalTarjeta = $("#pagado").val();
                send.fctTotalTarjeta = $("#pagado").val();
            }
            send.SwtTipo = 2;//lc_tipoEnvio;
            if ($("#cantidad").val() == "") {
                send.propina_valor = 0;
            } else {
                send.propina_valor = parseFloat($("#cantidad").val());
            }
            $.getJSON("config_facturacion.php", send, function (datos) {
                $("#btnCancelarPago").attr('disabled',false);
                send = {"grabacanalmovimientoVoucher": 1};
                send.respuesta = idRespuesta;
                $.getJSON("config_facturacion.php", send, function (datos) {});
                lc_control = 0;
                var aPagar = parseFloat($("#pagoTotal").val());
                var pagado = parseFloat($("#pagado").val());
                var valor = aPagar - pagado;
                var btnNombre = $("#btnAplicarPago").attr("title");
                $("#pagoTotal").val(valor.toFixed(2));
                $("#pagado").val("");
                fn_resumenFormaPago();
                if ((valor <= 0 && btnNombre != "EFECTIVO"))
                {
                    fn_buscaPagoCredito();
                    //fn_envioFactura();
                }
            });
        } else if (codigoRespuesta == "0")
        {
            $("#div_cvv").dialog("close");
            $("#pagado").val("");
            alertify.alert(tramaError);
            return false;
        } else
        {
            $("#div_cvv").dialog("close");
            $("#pagado").val("");
            send = {"grabacanalmovimientoVoucher": 1};
            send.respuesta = idRespuesta;
            $.getJSON("config_facturacion.php", send, function (datos)
            {

            });
            alertify.alert(respuesta);
            return false;
        }
    } else
    {
        $("#div_cvv").dialog("close");
        $("#pagado").val("");
        alertify.alert(tramaError /*+ ". Es posible que el lector de banda magn&eacute;tica no este funcionando correctamente."*/);
        return false;

    }
}

function fn_timeoutBanda() 
{
    timeOut = setTimeout("fn_detenerProcesoBanda();", tiempoEspera);
}

function fn_detenerProcesoBanda() 
{
    $("#txt_trama").val("");
    cargando(1);
    clearInterval(myVariable);
    lc_control = 1;
    alertify.alert("Expiro el tiempo de espera.Vuelva a intentarlo");
    
    send = {"consultaIdSWtimeoutBanda": 1};
    send.movtimeOutBanda = $("#txtNumFactura").val();
    send.accionSwtTimeouBanda = 2;//bandera de banda
    $.getJSON("config_facturacion.php", send, function (datos) {
        lc_control = 0;
        $("#pagado").val("");
        $("#div_tipoCuentaTarjeta").dialog("destroy");
        $("#div_cvv").dialog("destroy");
        fn_resumenFormaPago();
        return false;
    });
}
function fn_esperaRespuestaBandaAnulacion() {
    send = {"esperaRespuestaRequerimientoAutorizacion": 1};
    send.cfac = $("#txtNumFactura").val();
    $.ajax({async: true, url: "config_facturacion.php", data: send, dataType: "json",
        success: function (datos) {
            if (datos.existe != 2) {
                fn_funcionMuestraRespuestaCancelacion(datos.rsaut_respuesta, datos.cres_codigo, datos.fpf_id, datos.rsaut_id, datos.errorTrama);
            }
        }
    });
}

function fn_funcionMuestraRespuestaCancelacion(respuesta, codigoRespuesta, formadePago, idRespuesta, tramaError) {
    timeOut = clearTimeout(timeOut);    
    clearTimeout(temporizadorPinpad);
    $("#countdown").countdown360().stop()
    lc_control = 1;
    cargando(1);
    //alert(codigoRespuesta);
    if (tramaError == 1) {
        if (codigoRespuesta == "00") {           
            send = {"cancelaTarjetaForma": 1};
            send.can_respuesta = idRespuesta;
            send.cancela_codFact = $("#txtNumFactura").val();
            send.cancela_idPago = $("#btnFormaPagoId").val();
            $.getJSON("config_facturacion.php", send, function (datos) {
                alertify.alert(respuesta);
                fn_obtieneTotalApagar();
                fn_resumenFormaPago();
                //fn_cancelarPago();
                $("#pagado").val("");
            });
        } else {
            send = {"grabacanalmovimientoVoucher": 1};
            send.respuesta = idRespuesta;
            $.getJSON("config_facturacion.php", send, function (datos) {});
            alertify.alert(respuesta);
            return false;
        }
    } else {
        alertify.alert(tramaError);
        return false;
    }
}