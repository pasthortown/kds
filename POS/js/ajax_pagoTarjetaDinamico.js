var URL = '';
var FMP_ID_ANULACION = '';
var MODULO_PATH = '';
var SECUENCIAS_TIME_OUT = new Array;

function Armar_Trama_Dinamica(tipoTransaccion, idDispositivo, secuencia, lecturaTarjeta, cvv, codigoFactura, idFormaPagoFatura, modulo) {
    FMP_ID_ANULACION = idFormaPagoFatura;
    MODULO_PATH = modulo;

    if (modulo == 'FACTURACION') {
        URL = "config_pagoTarjetaDinamica.php";
    } else {
        URL = "../facturacion/config_pagoTarjetaDinamica.php";
        $("#txt_trama").val('');
    }
    if (!validarTramaAnulada(codigoFactura, idDispositivo, tipoTransaccion, modulo)) {
        send = { "insertarRequerimientoTramaDinamica": 1 };
        send.cfacTramaDinamica = codigoFactura;
        send.formaPagoIDTramaDinamica = idFormaPagoFatura;
        send.valorPropinaTramaDinamica = 0;
        if ((tipoTransaccion == 'ENVIO') || (tipoTransaccion == 'REVERSO')) {
            valordelaTransaccionPinPadP = parseFloat($("#pagoTotal").val());
            if (banderaa == 1) {
                send.valorTransaccionTramaDinamica = $("#pagado").val();
            } else {
                if (valordelaTransaccionPinPadP != 0) {
                    send.valorTransaccionTramaDinamica = $("#pagado").val();
                } else {
                    send.valorTransaccionTramaDinamica = $("#pagoGranTotal").val();
                }
            }
        } else {
            send.valorTransaccionTramaDinamica = 0;
        }
        send.tipoEnvioTramaDinamcica = idDispositivo;
        send.tipoTransaccionTramaDinamica = tipoTransaccion;
        send.lecturaTarjetaTramaDinamica = lecturaTarjeta;
        send.cvvTramaDinamica = cvv;

        if ( modulo == "FACTURACION" )
        {

            var condicionFOP = $( "#condicionFacOrdenPedido" ).val();

            if ( condicionFOP != 4 && condicionFOP != 10 )
            {
                $.ajax({
                    async: false,
                    type: "POST",
                    dataType: "json",
                    contentType: "application/x-www-form-urlencoded",
                    url: URL,
                    data: send,
                    success: function(datos) {
                        if (datos.str > 0) {
                            if (datos.estado  !== 1) {
                           /*     let procesar=get_time();
                                tiempo_transcurrido=procesar.DiferenciaTiempo;
                                tiempo_defecto=$("#tiempoEspera").val()/1000;
                                if (tiempo_defecto-tiempo_transcurrido>0) {
                                    cargando(2);                    
                                    if (typeof(countdown) !== "undefined"){
                                        $(countdown).countdown360().settings.seconds = $("#tiempoEspera").val()/1000;
                                    }
                                    tiempoEspera=(tiempo_defecto-tiempo_transcurrido)*1000;
                                    // fn_timeOutPagoTarjeta(idDispositivo);
                                    TEMPORIZADORUNIRED = setInterval(function() { fn_esperaRespuestaTramaDinamica(codigoFactura, idDispositivo, tipoTransaccion, modulo); }, 1000);
                                    $(countdown).countdown360().settings.onComplete = function(){
                                        fn_detenerProcesoPagoTarjeta(idDispositivo)               
                                    };
                                    tiempoEspera=$("#tiempoEspera").val();
                                    return;
                                }*//////////
                                // location.reload();
                                alertify.alert(datos.mensaje);            
                                console.log(datos);
                                return;
                            }

                            if ( secuencia[0] == "Esperar_Respuesta" ) 
                            {
                                if ( tipoTransaccion == "ENVIO" && idDispositivo == 7 )
                                {
                                    var duracion = ($( "#tiempoEspera" ).val() / 1000)+1;
                                    var tinicial = new Date();
                                    var tfinal   = new Date();
                                  
                                    localStorage.tiempoCronometrado = JSON.stringify
                                        (
                                            {   
                                                duracion    :   duracion,
                                                tinicial    :   tinicial,
                                                tfinal      :   tfinal
                                            }
                                        );
                                }

                                lc_control = 0;
                                secuencia  = secuencia.splice(0, 1);

                                if ( typeof cargarCountDown === "function" ) 
                                {

                                    cargarCountDown(0);
                                }
                                cargando(0,1);   
                                        
                                if ( typeof(countdown) !== "undefined" )
                                {
                                    $(countdown).countdown360().settings.seconds = $( "#tiempoEspera" ).val() / 1000 +1;
                                }
                                //fn_timeoutPinpadUnired();
                                // fn_timeOutPagoTarjeta(idDispositivo);
                                TEMPORIZADORUNIRED = setInterval(
                                    function() 
                                    { 
                                        if ( tipoTransaccion == "ENVIO" && idDispositivo == 7 )
                                        {
                                            var parseaLStiempoCronometrado = JSON.parse( localStorage.tiempoCronometrado );
                                            var auxDuracion = parseaLStiempoCronometrado.duracion;
                                            var auxTinicial = parseaLStiempoCronometrado.tinicial;
                                            var auxtfinal   = new Date();                                       
                                            localStorage.tiempoCronometrado = JSON.stringify
                                                (
                                                    {   
                                                        duracion    :   auxDuracion,
                                                        tinicial    :   auxTinicial,
                                                        tfinal      :   auxtfinal
                                                    }
                                                );
                                        }
                                        
                                     fn_esperaRespuestaTramaDinamica(codigoFactura, idDispositivo, tipoTransaccion, modulo); 
                                    }
                                , 1000);
                                
                              
                            }
                        } else {
                            alertify.alert("ERROR AL INSERTAR TRAMA DE REQUERIMIENTO.");
                            $("#pagado").val('');
                            return false;
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alertify.alert(jqXHR);
                        alertify.alert(textStatus);
                        alertfy.alert(errorThrown);
                    }
                });
            }
            else
            {    

                if ( secuencia[0] == "Esperar_Respuesta" ) 
                {
                    var parseaLStiempoCronometrado = JSON.parse( localStorage.tiempoCronometrado );
                    var auxDuracion = parseaLStiempoCronometrado.duracion ;
                    var auxTinicial = new Date( parseaLStiempoCronometrado.tinicial );
                    var auxtfinal   = new Date( parseaLStiempoCronometrado.tfinal );
                    var tpresente   = new Date();
                    // distancia(seg) = duración(seg) - ( TiempoEsperaEnModulo(seg) + TiempoFueraDelModulo(seg) )
                    var distancia   = auxDuracion - ( ( ( auxtfinal.getTime() - auxTinicial.getTime() ) + ( tpresente.getTime() - auxtfinal.getTime() ) ) / 1000 ) ;
                    distancia = Math.trunc( distancia );

                    if ( distancia < 2 )
                    {
                        distancia = 2;
                    }

                    var duracion = distancia;
                    var tinicial = new Date();
                    var tfinal   = new Date();
                    localStorage.tiempoCronometrado = JSON.stringify
                        (
                            {   
                                duracion    :   duracion,
                                tinicial    :   tinicial,
                                tfinal      :   tfinal
                            }
                        );

                    // --

                    lc_control = 0;
                    secuencia  = secuencia.splice(0, 1);

                    if ( typeof cargarCountDown === "function" ) 
                    {
                        cargarCountDown(0);
                    }
                    cargando(0,2);   
                            
                    if ( typeof(countdown) !== "undefined" )
                    {
                        $(countdown).countdown360().settings.seconds = duracion +1;
                    }
                    //fn_timeoutPinpadUnired();
                    // fn_timeOutPagoTarjeta(idDispositivo);

                    TEMPORIZADORUNIRED = setInterval(
                        function() 
                        {
                            parseaLStiempoCronometrado = JSON.parse( localStorage.tiempoCronometrado );
                            auxDuracion = parseaLStiempoCronometrado.duracion;
                            auxTinicial = parseaLStiempoCronometrado.tinicial;
                            auxtfinal   = new Date();

                            localStorage.tiempoCronometrado = JSON.stringify
                                (
                                    {   
                                        duracion    :   auxDuracion +1,
                                        tinicial    :   auxTinicial,
                                        tfinal      :   auxtfinal
                                    }
                                );

                            fn_esperaRespuestaTramaDinamica(codigoFactura, idDispositivo, tipoTransaccion, modulo); 
                        }
                    , 1000);

                    $(countdown).countdown360().settings.onComplete = function()
                    {  
                        $( "#condicionFacOrdenPedido" ).val( "" );
                        $( "#condicionFacOrdenPedido" ).val( "-1" );

                        if ( typeof cargarCountDown === 'function' ) 
                        {
                            cargarCountDown(3);
                        } 

                        //persistirEsperaRespFPTarjetas( idDispositivo ); 
                        fn_detenerProcesoPagoTarjeta( idDispositivo, 1);          
                    };
                }
            }
        }
        else
        {
            $.ajax({
                async: false,
                type: "POST",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: URL,
                data: send,
                success: function(datos) {
                    if (datos.str > 0) {
                        if (datos.estado  !== 1) {
                       /*     let procesar=get_time();
                            tiempo_transcurrido=procesar.DiferenciaTiempo;
                            tiempo_defecto=$("#tiempoEspera").val()/1000;
                            if (tiempo_defecto-tiempo_transcurrido>0) {
                                cargando(2);                    
                                if (typeof(countdown) !== "undefined"){
                                    $(countdown).countdown360().settings.seconds = $("#tiempoEspera").val()/1000;
                                }
                                tiempoEspera=(tiempo_defecto-tiempo_transcurrido)*1000;
                                // fn_timeOutPagoTarjeta(idDispositivo);
                                TEMPORIZADORUNIRED = setInterval(function() { fn_esperaRespuestaTramaDinamica(codigoFactura, idDispositivo, tipoTransaccion, modulo); }, 1000);
                                $(countdown).countdown360().settings.onComplete = function(){
                                    fn_detenerProcesoPagoTarjeta(idDispositivo)               
                                };
                                tiempoEspera=$("#tiempoEspera").val();
                                return;
                            }*//////////
                            // location.reload();
                            alertify.alert(datos.mensaje);            
                            console.log(datos);
                            return;
                        }
                        if (secuencia[0] == 'Esperar_Respuesta') {
                            lc_control = 0;
                            secuencia = secuencia.splice(0, 1);
                            if (typeof cargarCountDown === 'function') {
                                cargarCountDown(0);
                            }
                            cargando(0,3);   
                                    
                            if (typeof(countdown) !== "undefined"){
                                    $(countdown).countdown360().settings.seconds = $("#tiempoEspera").val()/1000 +1;
                            }
                            //fn_timeoutPinpadUnired();
                            // fn_timeOutPagoTarjeta(idDispositivo);
                            TEMPORIZADORUNIRED = setInterval(function() { fn_esperaRespuestaTramaDinamica(codigoFactura, idDispositivo, tipoTransaccion, modulo); }, 1000);
                            $(countdown).countdown360().settings.onComplete = function(){  
                                if (typeof cargarCountDown === 'function') {
                                    cargarCountDown(3);
                                }                                  
                                fn_detenerProcesoPagoTarjeta(idDispositivo, 2)               
                            };
                        }
                    } else {
                        alertify.alert("ERROR AL INSERTAR TRAMA DE REQUERIMIENTO.");
                        $("#pagado").val('');
                        return false;
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alertify.alert(jqXHR);
                    alertify.alert(textStatus);
                    alertfy.alert(errorThrown);
                }
            });
        }
    }
}	

function get_time() {
    send={'consultaDiferenciaTiempo':1};
    send.codFact = $("#txtNumFactura").val();
    toReturn = null;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_pagoTarjetaDinamica.php",
        data: send,
        success: function(datos) {
            toReturn= datos;
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alertify.alert(jqXHR);
            alertify.alert(textStatus);
            alertfy.alert(errorThrown);
        }
    });
    return toReturn;
}

function fn_esperaRespuestaTramaDinamica(factura, idDispositivo, tipoTransaccion, modulo) {

    localStorage.setItem('dispositivo',idDispositivo);
    console.log('ESPERA TRAMA DINAMICA')
    //        if(modulo=='FACTURACION'){ url="config_pagoTarjetaDinamica.php"; }
    //        else {url="../facturacion/config_pagoTarjetaDinamica.php";}
    send = { "esperaRespuestaRequerimientoAutorizacion": 1 };
    send.cfac = factura;
    $.ajax({
        async: true,
        url: URL,
        data: send,
        dataType: "json",
        success: function(datos) {
            if (datos.existe === 1) {

                if ( modulo == "FACTURACION" )
                {
                    var condicionFOP = $( "#condicionFacOrdenPedido" ).val();
        
                    if ( condicionFOP == 4 || condicionFOP == 10 )
                    {
                        $( "#condicionFacOrdenPedido" ).val( "" );
                        $( "#condicionFacOrdenPedido" ).val( "-1" );
                    }
                }
                
                fn_funcionMuestraRespuestaTramaDinamica(datos.rsaut_respuesta, datos.cres_codigo, datos.fpf_id, datos.rsaut_id, datos.errorTrama, datos.codigoAutorizador, datos.respuestaAutorizacion, idDispositivo, factura, tipoTransaccion, modulo);

            }
        }
    });
}

function fn_funcionMuestraRespuestaTramaDinamica(respuesta, codigoRespuesta, formadePago, idRespuesta, tramaError, codigoAutorizador, respuestaAutorizacion, idDispositivo, factura, tipoTransaccion, modulo) {
    clearInterval(TEMPORIZADORUNIRED);
    // clearTimeout(timeOutPinpadUnired);
    lc_control = 1;
    cargando(1);
    console.log('respuesta trama dinamica');
    if (tipoTransaccion == 'ENVIO') {
        $("#countdown").countdown360().stop();
        $("#txt_trama").val("");
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
                }else{
                    send = { "grabacanalmovimientoVoucher": 1 };
                    send.respuesta = idRespuesta;
                    $.getJSON("config_facturacion.php", send, function(datos) {}); //jkquezada
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
        } else if (respuestaAutorizacion == "NO APROBADO") {
            $("#div_cvv").dialog("close");
            $("#pagado").val("");
            let apiImpresion = getConfiguracionesApiImpresion();
            if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                fn_cargando(1);
                    result = new apiServicioImpresion('VoucherNo', idRespuesta, 0, send);        
                    let imprime = result['imprime'];
                    let  = result['mensaje'];
                    if (!imprime) {
                        alertify.success('Error en el Váucher...');
                        fn_cargando(0);
                    } else {
                        alertify.success('Error al imprimir...'+mensaje);
                        fn_cargando(0);
                    }
            }else{
                send = { "grabacanalmovimientoVoucher": 1 };
                send.respuesta = idRespuesta;
                $.getJSON("config_facturacion.php", send, function(datos) {
    
                });
            }
            alertify.alert(respuesta);
            cargando(1);
            return false;

        } else if (respuestaAutorizacion == 'TIME OUT') {
            fn_detenerProcesoPagoTarjeta(idDispositivo, 3);

        } else if (respuestaAutorizacion == 'ERROR') {
            fn_actualizaEstadoRequerimiento(idRespuesta, tramaError);
        } else {
            $("#pagado").val("");
            alertify.alert(tramaError);
            cargando(0,4);
            return false;
        }
        ///////fin de envio de autorizacion
    } else if (tipoTransaccion == 'ANULACION') {
        lc_control = 1;
        console.log('ANULACION');
        if (respuestaAutorizacion == "APROBADA") {
            console.log('APROBADO');
            if (modulo == 'TRANSACCIONES') {
                send = { "cancelaTarjetaForma": 1 };
                send.can_respuesta = idRespuesta;
                send.cancela_codFact = factura;
                send.cancela_idPago = FMP_ID_ANULACION;
                $.getJSON("config_anularOrden.php", send, function(datos) {
                    let apiImpresion = getConfiguracionesApiImpresion();

                    if ( apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1 ) {
                        fn_cargando(1);

                        result = new apiServicioImpresion('VoucherAnulacionTransaccion', idRespuesta, null, null);

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
                $("#countdown").countdown360().stop();
                send = { "cancelaTarjetaForma": 1 };
                send.can_respuesta = idRespuesta;
                send.cancela_codFact = $("#txtNumFactura").val();
                send.cancela_idPago = $("#btnFormaPagoId").val();
                $.getJSON("config_facturacion.php", send, function(datos) {
                    let apiImpresion = getConfiguracionesApiImpresion();

                    if ( apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1 ) {
                        fn_cargando(1);

                        result = new apiServicioImpresion('VoucherAnulacionTransaccion', idRespuesta, null, null);

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

                    alertify.alert(respuestaAutorizacion);
                    fn_obtieneTotalApagar();
                    fn_resumenFormaPago();
                    //fn_cancelarPago();
                    $("#pagado").val("");
                });
            }
        } else if (respuestaAutorizacion === "NO APROBADO") {   /// jq
            if (modulo == 'FACTURACION') { $("#countdown").countdown360().stop(); }
            cleanIntervalModal();
            let apiImpresion = getConfiguracionesApiImpresion();
            if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                fn_cargando(1);
                send.codigo_app = idRespuesta;
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
                send.respuesta = idRespuesta;
                $.getJSON("config_facturacion.php", send, function(datos) { });
                alertify.alert(tramaError);
                cleanIntervalModal();
                console.log('NO APROBADOO EJECUTAR 444');
                alertify.success('Imprimiendo Váucher...');
            }   

            return false;
        } else {
            console.log('ELSE NO APROBADOO');
            if (modulo == 'FACTURACION') {
                $("#countdown").countdown360().stop();
                $("#pagado").val("");
            }
            alertify.alert(tramaError);
            cargando(0,5);
            return false;
        }
    }
}

function fn_actualizaEstadoRequerimiento(idRespuesta, tramaError) {
    //enviar solicitud a servidor para cambio de estado de transacción de 61 a 42 en tabla SWT_Requerimiento_Autorizacion
    send = { "actualizaEstadoRequerimiento": 1 };
    send.rsaut_id = idRespuesta;
    send.codFact = $("#txtNumFactura").val();

    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: 'config_pagoTarjetaDinamica.php',
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                alertify.alert('Error de comunicación con el proveedor: ' + tramaError + '. Vuelva a intentarlo');
            }
        }
    });

}

function Anular_Pago(codigoFactura, idFormaPagoFactura) {
    send = { "cancelaTarjetaForma": 1 };
    send.can_respuesta = idRespuesta;
    send.cancela_codFact = factura;
    send.cancela_idPago = formaPagoId;
    $.getJSON("config_anularOrden.php", send, function(datos) {
        fn_formasPago();
        Tipo_FormaPago = 0;
        $("#btn_anulacancela").hide();
    });
}

function fn_timeOutPagoTarjeta(idDispositivo) {
    timeOutPinpadUnired = setTimeout("fn_detenerProcesoPagoTarjeta(" + idDispositivo + ", 4);", tiempoEspera);
}

function fn_detenerProcesoPagoTarjeta(idDispositivo, nivelErrorLog = 10) {
    let cangandoModal = 0;
    if (MODULO_PATH === 'FACTURACION') {
        cangandoModal = 1;
    }
    cargando(cangandoModal);
    lc_control = 1;
    clearInterval(TEMPORIZADORUNIRED);
    //$("#pagado").val("");

    if ( MODULO_PATH == "FACTURACION" ) 
    {
        alertify.set(
            {
                labels: 
                {
                    ok: "OK"               
                }
            }
        );

        //var mensaje = "<p>Han trascurrido 70 segundos y no hemos recibido respuesta del banco. En consecuencia, la transacción ha sido <strong style=\"font-size: 20px; color: green;\">REVERSADA</strong>.</p>"
        mensaje = "<p>Expiró el tiempo de espera. Vuelva a intentarlo.</p>"
        
        alertify.alert( mensaje );
    }

    //    return false;
    //    send = {"consultaIdSWtimeoutBanda": 1};
    //    send.movtimeOutBanda = $("#txtNumFactura").val();
    //    send.accionSwtTimeouBanda = 2;//bandera de banda
    //    $.getJSON("config_facturacion.php", send, function () {
    //        fn_resumenFormaPago();
    //    });

    //consulta la secuencia configurada para pagos con tarjeta cuando de time out
    URL = (MODULO_PATH == 'FACTURACION') ? "config_pagoTarjetaDinamica.php" : "../facturacion/config_pagoTarjetaDinamica.php";
    //if (MODULO_PATH == 'FACTURACION') { URL = "config_pagoTarjetaDinamica.php"; } else { URL = "../facturacion/config_pagoTarjetaDinamica.php"; }

    send = { "consultaSecuenciaTimeOuts": 1 };
    send.idTipoEnvioTarjeta = idDispositivo;
    send.nivelErrorLog = nivelErrorLog;
    send.numFactura = $("#txtNumFactura").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: URL,
        data: send,
        success: function(datos) {

            if (datos.str > 0) {
                SECUENCIAS_TIME_OUT = datos.secuenciaConfigurada.split("->");
                if (SECUENCIAS_TIME_OUT[0] == 'Armar_Trama' && MODULO_PATH == 'FACTURACION') {
                    SECUENCIAS_TIME_OUT = SECUENCIAS_TIME_OUT.splice(1, SECUENCIAS_TIME_OUT.length - 1);
                    Armar_Trama_Dinamica('REVERSO', idDispositivo, SECUENCIAS_TIME_OUT, '', '', $("#txtNumFactura").val(), $("#btnFormaPagoId").val(), MODULO_PATH);
                    //Armar_Trama_Dinamica('ENVIO', integracion,secuencias,'','',$("#txtNumFactura").val(),$("#btnFormaPagoId").val(),'FACTURACION');  
                }
                //alert(datos.secuenciaConfigurada);         
            }
        }
    });

    if (MODULO_PATH == 'FACTURACION') {
        send = { "consultaIdSWtimeoutBanda": 1 };
        send.movtimeOutBanda = $("#txtNumFactura").val();
        send.accionSwtTimeouBanda = lc_tipoEnvio; //bandera de banda
        $.getJSON("config_facturacion.php", send, function(datos) {
            $("#pagado").val("");
            $("#div_cvv").dialog("destroy");
        });
    }
    $("#alertify-ok").click(function(event) {
        event.stopPropagation();
        lc_control = 0;
        //return false;
    });
}

function validarTramaAnulada(codigoFactura, idDispositivo, tipoTransaccion, modulo) {

    //let cfac_id = $('#listadoPedido').find("li.focus").attr("id");
    
    let transaccionAnuladaCorrectamente = false;

    if (modulo === 'TRANSACCIONES') {
        send = { "ValidacionSinTarjeta": 1 };
        send.cfac = codigoFactura;
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: URL,
            data: send,
            success: function(datos) {
                if (datos.existe === 1){
                    let {
                        rsaut_respuesta,
                        cres_codigo,
                        fpf_id,
                        rsaut_id,
                        errorTrama,
                        codigoAutorizador,
                        respuestaAutorizacion
                    } = datos; 
                    TEMPORIZADORUNIRED = setInterval(function() { console.log("TEMPORIZADORUNIRED") }, 1000);
                    if (typeof cargarCountDown === 'function') {
                        cargarCountDown(0);
                    }
                    fn_funcionMuestraRespuestaTramaDinamica(rsaut_respuesta, cres_codigo, fpf_id, rsaut_id, errorTrama, codigoAutorizador, respuestaAutorizacion, idDispositivo, codigoFactura, tipoTransaccion, modulo)
                    
                    transaccionAnuladaCorrectamente = true;
                }  
            },
            error: function(e) {
                console.log(e);
            }
        });
    }    

    return transaccionAnuladaCorrectamente;
}

function cleanIntervalModal() {
        clearInterval(TEMPORIZADORUNIRED);
        $("#countdown").attr("style", "display:none");
        $("#modalBloquearCargaCronometro").hide();

}

// solo falta añadir un local storage reiniciable al definir el requerimiento, que nos sirva como bandera para que el envió del reverso ocurra una sola vez 
// La función nos permite mantener activo el contador y la búsqueda de respuesta para promesas de tipo envió, hasta que la tengamos y cerremos el proceso, el contador se reinicia cada vez que llega a cero. Esta completa, solo falta lo del storag bandera. 
// para activarla solo tenemos que descomentarla de los puntos donde la estoy llamando, que son dos, y eso es todo.
function persistirEsperaRespFPTarjetas( idDispositivo ) 
{
    if ( idDispositivo == 7 )
    {
        var duracion = $( "#tiempoEspera" ).val() / 1000 +1;
        var tinicial = new Date();
        var tfinal   = new Date();

        localStorage.tiempoCronometrado = JSON.stringify
            (
                {   
                    duracion    :   duracion,
                    tinicial    :   tinicial,
                    tfinal      :   tfinal
                }
            );
    }

    if ( typeof cargarCountDown === "function" ) 
    {
        cargarCountDown(0);
    }
    cargando(0,6);   
            
    if ( typeof(countdown) !== "undefined" )
    {
        $(countdown).countdown360().settings.seconds = $( "#tiempoEspera" ).val() / 1000 +1;
    }
    
    $(countdown).countdown360().settings.onComplete = function()
    {  
        if ( typeof cargarCountDown === "function" ) 
        {
            cargarCountDown(3);
        }  

        persistirEsperaRespFPTarjetas( idDispositivo );
    };

    // --
    
    URL = "config_pagoTarjetaDinamica.php";
    
    send = { "consultaSecuenciaTimeOuts": 1 };
    send.idTipoEnvioTarjeta = idDispositivo;

    $.ajax(
        {
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: URL,
            data: send,
            success: function( datos ) 
            {
                if ( datos.str > 0 ) 
                {
                    SECUENCIAS_TIME_OUT = datos.secuenciaConfigurada.split( "->" );

                    if ( SECUENCIAS_TIME_OUT[0] == "Armar_Trama" ) 
                    {
                        SECUENCIAS_TIME_OUT = SECUENCIAS_TIME_OUT.splice(1, SECUENCIAS_TIME_OUT.length - 1);

                        Armar_Trama_Dinamica("REVERSO", idDispositivo, SECUENCIAS_TIME_OUT, "", "", $("#txtNumFactura").val(), $("#btnFormaPagoId").val(), MODULO_PATH);

                        alertify.set(
                            {
                                labels: 
                                {
                                    ok: "OK"               
                                }
                            }
                        );
    
                        var mensaje = "<p>Han trascurrido 70 segundos y no hemos recibido respuesta del banco. En consecuencia, la transacción ha sido <strong style=\"font-size: 20px; color: green;\">REVERSADA</strong>.</p>"
    
                        alertify.alert( mensaje );
                    }       
                }
            }
        }
    );

    send = { "consultaIdSWtimeoutBanda": 1 };
    send.movtimeOutBanda = $("#txtNumFactura").val();
    send.accionSwtTimeouBanda = lc_tipoEnvio; //bandera de banda

    $.getJSON("config_facturacion.php", send, function(datos) {} );
    
    $("#alertify-ok").click(
        function(event) 
        {
            event.stopPropagation();
            lc_control = 0;
        }
    );
}
