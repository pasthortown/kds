function fn_armaTramaPinPadMultired(opcion,tipoTransaccion)
{    
    valordelaTransaccionPinPad = parseFloat($("#pagoTotal").val());
    send = {"insertarRequerimientoAutorizacion": 1};
    send.cfac = $("#txtNumFactura").val();
    send.formaPagoID = $("#btnFormaPagoId").val();
    if(opcion=='compra')
    {
        send.valorTransaccion = parseFloat($("#pagoTotal").val());
        if ($("#cantidad").val() == "") {  send.prop_valor = 0;} 
        else { send.prop_valor = parseFloat($("#cantidad").val());}
        if (banderaa == 1) {send.valorTransaccionPinPad = $("#pagado").val();} 
        else 
        {
            if (valordelaTransaccionPinPad != 0) 
            {
                send.valorTransaccionPinPad = $("#pagado").val();
            }
            else 
            {
                send.valorTransaccionPinPad = $("#pagoGranTotal").val();
            }
        } 
    }
    else
    {
        send.valorTransaccion = 0;
        send.prop_valor=0;
        send.valorTransaccionPinPad=0;
    }
    
    send.tipoEnvioPinPad = lc_tipoEnvio;
    send.tipoTransaccionPinpad = tipoTransaccion;
    $.getJSON("config_facturacion.php", send, function (datos) {
        lc_control = 0;
        cargando(0);
        fn_timeout();
        if(opcion=='compra')
        {
            temporizadorPinpad = setInterval(function () {fn_esperaRespuesta();}, 1000);
        }
        else
        {
            temporizadorPinpad = setInterval(function () {fn_esperaRespuestaCancelacion();}, 1000);
        }
        
    });	   
}

function fn_timeout() {
    timeOut = setTimeout("fn_detenerProceso();", tiempoEspera);
}

function fn_esperaRespuesta() 
{
    send = {"esperaRespuestaRequerimientoAutorizacionPinpadMultired": 1};
    send.cfacMultired = $("#txtNumFactura").val();
    $.ajax({
        async: true,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.existe != 2) 
            {                
                fn_funcionMuestraRespuestaPinpad(datos.rsaut_respuesta, datos.cres_codigo, datos.fpf_id, datos.rsaut_id, datos.errorTrama, datos.codigoAutorizador);
            }
        }
    });
}

function fn_esperaRespuestaCancelacion() 
{
    send = {"esperaRespuestaRequerimientoAutorizacion": 1};
    send.cfac = $("#txtNumFactura").val();
    $.ajax({
        async: true,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) 
        {
            if (datos.existe === 1) 
            {                
                fn_funcionMuestraRespuestaCancelacion(datos.rsaut_respuesta, datos.cres_codigo, datos.fpf_id, datos.rsaut_id, datos.errorTrama);
            }
        }
    });
}

function fn_funcionMuestraRespuestaPinpad(respuesta, codRes, idFormaP, idRespuesta, errorTrama, mensaAutorizador) 
{
    timeOut = clearTimeout(timeOut);
    clearInterval(temporizadorPinpad);
    $("#countdown").countdown360().stop();
    $("#txt_trama").val("");
    lc_control = 1;
    cargando(1);
    if (errorTrama == 1) {
        
        if (codRes == "00")//00 es la respuesta del pinpad 'Exitosa'
        {
            if (mensaAutorizador == "00") {
                send = {"ingresaFormaPagoTarjeta": 1};
                send.codFactTarjeta = $("#txtNumFactura").val();
                send.fmpIdTarjeta = $("#btnFormaPagoId").val();
                send.fmpNumSegtar = 0;
                if ($("#pagoTotal").val() >= $("#pagado").val() || $("#pagoTotal").val() == $("#pagado").val()) {
                    send.frmPagoTotalTarjeta = $("#pagado").val();
                    send.fctTotalTarjeta = $("#pagado").val();
                } else {
                    send.frmPagoTotalTarjeta = $("#pagado").val();
                    send.fctTotalTarjeta = $("#pagoTotal").val();
                }
                send.SwtTipo = lc_tipoEnvio;
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
                    if ((valor <= 0 && btnNombre != "EFECTIVO")) {
                        fn_envioFactura();
                    }
                });
            } else {
                $("#div_cvv").dialog("close");
                $("#pagado").val("");
                send = {"grabacanalmovimientoVoucher": 1};
                send.respuesta = idRespuesta;
                $.getJSON("config_facturacion.php", send, function (datos) {});
                alertify.alert(respuesta);
                return false;
            } 
        } else if (codRes == "0") {
            $("#div_cvv").dialog("close");
            $("#pagado").val("");
            alertify.alert(errorTrama);
            return false;
        } else {
            $("#div_cvv").dialog("close");
            $("#pagado").val("");
            send = {"grabacanalmovimientoVoucher": 1};
            send.respuesta = idRespuesta;
            $.getJSON("config_facturacion.php", send, function (datos) {});
            alertify.alert(respuesta);
            return false;
        }
    } else {
        $("#div_cvv").dialog("close");
        $("#pagado").val("");
        alertify.alert(errorTrama);
        return false;

    }
}