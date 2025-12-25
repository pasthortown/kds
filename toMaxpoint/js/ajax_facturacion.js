/* global parseFloat, alertify */

var temporizadorPinpad;
var coma2 = 0;
var lc_timer = 0;
lc_control = 0;
lc_control_firma = 0;
lc_control_enlace = 0;
tfp_wst = -1;
swtTransaccionalmente = 0;
lc_control_factura = -1;
lc_tipoEnvio = -1;
lc_descripcionEnvio = "";
var tiempoEspera = 0;
lc_banderaOkAdmin = "";
var banderaUber = 0;
lc_agrupacion = "";
lc_subagrupacion = "";
var lc_idefectivo = -1;
lc_autorizacion = -1;
lc_banderaAplicapago = -1;
lc_clienteAx = -1;
lc_nombreclienteAx = "";
lc_cantidadPagada = 0;
var banderaUber = 0;
var valorCampoCodigo;
var lc_opcionCreditoEmpresa = -1;
var TEMPORIZADORUNIRED;
var procesoFidelizacion = "";
var TEMPORIZADOR_PAGO_TARJETA;
var status_cupon = 0;
var status_pago = 0;
var tipoLecturaCodigoDescuentoMultimarca = 0;
var cuponMultimarcaCanjeado = "";
var valorCampoCodigo = "";
var marcaMasivo=localStorage.getItem('marcaMasivo');
var estadoMasivo=localStorage.getItem('estadoMasivo');
var RESPONSE_JSON;
var RESPONSE_JSON2;
var TOTAL_PUNTOS_MASIVO=0;
var TOTAL_FACTURA_MASIVO=0;
var TIPO_CANJE_MASIVO="";
var SECUENCIA_FACTURA_MASIVO="";
var NUEVA_FACTURA_MASIVO="";
var RESPONSE_JSON3;
var RESPONSE_VJSON;
var RESPONSE_VJSON2;
var RESPONSE_VJSON3;
var EstadoRedimension = 0;
var EstadoCanjePuntos = -1;
var marketingCost = 0;
var storeCost = 0;
var redemptionCode = "";
////VARIABLE PARA CALCULAR EL DESCUENTO TOTAL DE LOS PRODUCTOS
//var lc_descuentoProducto;
var BANDERA_AGREGADOR = 0;
var ValorPagadoConDeUna = 0;
var cuenta = '_1';
if ((localStorage.getItem("cuenta") != '') && (localStorage.getItem("cuenta") != null)){
    cuenta = '_'+localStorage.getItem("cuenta");
}
// Monto de campaña solidaria
let monto_total_propina = 0;

var LS_ODP_ID = localStorage.getItem('ls_odp_id');
var LS_DOP_CUENTA = localStorage.getItem('ls_dop_cuenta');
var LS_MESA_ID = localStorage.getItem('ls_mesa_id');
var LS_RECUPERA_ORDEN = localStorage.getItem('ls_recupera_orden');
var LS_RECUPERA_ORDEN_ID = localStorage.getItem('ls_recupera_orden_id');
var LS_RECUPERA_ORDEN_MESA = localStorage.getItem('ls_recupera_orden_mesa');
var LS_RECUPERA_ORDEN_CUENTA = localStorage.getItem('ls_recupera_orden_cuenta');

// variable inicial de localizador

let numeroLocalizador = 0;
let showCondicionConfiguracionLocalizador = 0;
let noCerrarModalNumeroLocalizador = 0;

//variable bandera para redireccionar a pantallas de acuerdo a la facturaciòn correcta o incorrecta
$(document).ready(function () {
    $(".descuentosLabel").shortscroll();
    $("#credencialesContenedor").hide();
    $("#detalleFactura").hide();
    $("#visorFacturas").css("display", "none");
    $("#detalleFactura").css("display", "none");
    $("#visorFormasPago").css("display", "none");
    $("#detalleFormasPago").css("display", "none");
    $("#divRsts").css("display", "none");
    $("#divObservacion").css("display", "none");
    $("#ingresoValorCredito").hide();
    $("#ingresoValorCreditoTeclado").hide();
    $("#credencialesAdmin").hide();
    $("#modal_binDatafast").hide();
    $("#ticketPromedio").hide();
    $("#td_cambio").hide();
    $("#div_cvv").hide();
    $("#div_adminPasaporte").hide();
    $("#div_tipoCuentaTarjeta").hide();
    $("#aumentarContador").hide();
    $("#anulacionesContenedor").hide();
    $("#adminCreditoSinCupon").hide();
    $("#datosFactura").hide();
    $("#btnFacturaImprimir").hide();
    $("#btnClienteModificar").hide();
    $("#btnClienteGuardar").hide();
    $("#btnClienteCancelar").hide();
    $("#btnClienteGuardarActualiza").hide();
    $("#modalclienteAx").hide();
    $("#descuentosContenedor").hide();
    $("#descuentosDiscrecionalesContenedor").hide();
    $("#formCobrar").hide();

    /*
        Se llenan las 3 variables cuando se recarga la pagina o
        se recupera una orden por facturar ya que se pierden
        los valores inicales que llegan desde la orden de pedido
    */
    console.log('LS_RECUPERA_ORDEN :', LS_RECUPERA_ORDEN);
    lc_cantidadPagada = 0;
    fn_aplicaPagoPredeterminado();

if(localStorage.getItem("boton_atras") ==1)
{
    $("#btn_salirOrden").hide();
}
else{
    $("#btn_salirOrden").show();
}

    if (LS_RECUPERA_ORDEN == 1) {
        console.log('LS_RECUPERA_ORDEN_ID :', LS_RECUPERA_ORDEN_ID);
        console.log('LS_RECUPERA_ORDEN_MESA :', LS_RECUPERA_ORDEN_MESA);
        console.log('LS_RECUPERA_ORDEN_CUENTA :', LS_RECUPERA_ORDEN_CUENTA);

        $("#txtOrdenPedidoId").val(LS_RECUPERA_ORDEN_ID)
        $("#txtNumMesa").val(LS_RECUPERA_ORDEN_MESA);
        $("#txtNumCuenta").val(LS_RECUPERA_ORDEN_CUENTA);
    } else {
        $("#txtOrdenPedidoId").val() ? $("#txtOrdenPedidoId").val() : $("#txtOrdenPedidoId").val(LS_ODP_ID);
        $("#txtNumMesa").val() ? $("#txtNumMesa").val() : $("#txtNumMesa").val(LS_MESA_ID);
        $("#txtNumCuenta").val() ? $("#txtNumCuenta").val() : $("#txtNumCuenta").val(LS_DOP_CUENTA);
    }

    $( "#condicionFacOrdenPedido" ).val( "" );
    $( "#IDFormapagoPromesaPendiente" ).val( "" );
    $( "#montoPagadoPromesaPendiente" ).val( "" );
    
    condicionFacturacionOrdenPedido( $( "#txtOrdenPedidoId" ).val() );
    condicionConfiguracionLocalizador($( "#txtOrdenPedidoId" ).val());

    fn_cargarAccesosSistema(); //HABILITA BOTONES SEGUN ACCESO  
    fn_verificarCampanaSolidaria();

    tiempoEspera = $("#tiempoEspera").val();
    moneda = $("#simMoneda").val();

    $("#hid_bandera_teclado").val(1);

    fn_facturar();

    fn_cargaDivBilletes();

    $("#pagado").val("");
    $("#dividirCuenta").hide();

    if ($("#txtTipoServicio").val() == 2) {
        $("#btn_propina").show();
    } else {
        $("#btn_propina").hide();
    }
    fn_bloquearIngreso();

    $("#txtClienteNombre").attr("disabled", "-1");
    $("#txtClienteApellido").attr("disabled", "-1");
    $("#txtClienteDireccion").attr("disabled", "-1");
    $("#txtClienteFono").attr("disabled", "-1");
    $("#txtCorreo").attr("disabled", "-1");
    if ($("#pagoTotal").val() > 199) {
        $("#btnConsumidorFinal").hide();
    } else {
        $("#btnConsumidorFinal").show();
    }

    $("#rdn_pdd_brr_ccns").click(function () {
        $("#rdn_pdd_brr_ccns").css("display", "none");
        $("#cnt_mn_dsplgbl_pcns_drch").css("display", "none");
    });
    $("#modalSWT").hide();
    $("#modalSWTCancelacion").hide();
    $("#modalsubSWT").hide();

    $("#modalCuponMultimarca").dialog({
        title: "Leer Cupón Multimarca",
        modal: true,
        position: {
            my: "right",
            at: "top"
        },
        width: 600,
        resizable: false,
        opacity: 0,
        duration: 500,
        autoOpen: false,
        closeOnEscape: false,
        buttons: [{
            id: "btnVerificarCodigoCuponMultimarca",
            text: "Verificar",
            click: fn_validarCuponMultimarca
        },
        {
            text: "Cancelar",
            click: function () {
                $(this).dialog("close");
                $("#keyboard").hide();
            }
        }
        ],
        open: function (event, ui) {
            $(".cntInputCodigoCuponMultimarca input").val("");

            mostrarOcultarInputsLectura(0);
        },
        close: function (event, ui) {
            $("#keyboard").hide();
        }

    });

    $("#inputCodigoCuponMultimarcaAutomatico").keypress(function (evt) {
        var tecla = evt.keyCode;
        //  13 es el código  de la tecla ENTER, 
        //  al final de la lectura el propio scanner inserta ese caracter al final. 
        if (13 === tecla) {
            $("#btnVerificarCodigoCuponMultimarca").click();
        }
    });
    mostrarOcultarInputsLectura(tipoLecturaCodigoDescuentoMultimarca);
    $("#btnLecturaAutomaticaCuponMultimarca").click(function () {
        mostrarOcultarInputsLectura(0);
        // console.log(tipoLecturaCodigoDescuentoMultimarca);
    });
    $("#btnLecturaManualCuponMultimarca").click(function () {
        mostrarOcultarInputsLectura(1);
        // console.log(tipoLecturaCodigoDescuentoMultimarca);
    });

    $("#inputCodigoCuponMultimarcaManual1").change(function (evt) {
        var valorActual = this.value;
        var tamValor = valorActual.length;

        if (3 <= tamValor) {
            $("#inputCodigoCuponMultimarcaManual2").focus();
            var substringValor = this.value.substring(0, 3);
            $("#inputCodigoCuponMultimarcaManual1").val(substringValor);
            evt.stopPropagation();
            return false;
        }
    });

    $("#inputCodigoCuponMultimarcaManual2").change(function (evt) {
        var valorActual = this.value;
        var tamValor = valorActual.length;

        if (8 <= tamValor) {
            $("#inputCodigoCuponMultimarcaManual3").focus();
            var substringValor = this.value.substring(0, 8);
            $("#inputCodigoCuponMultimarcaManual2").val(substringValor);
            evt.stopPropagation();
            return false;
        }
    });
    $("#inputCodigoCuponMultimarcaManual3").change(function (evt) {
        var valorActual = this.value;
        var tamValor = valorActual.length;
        if (9 <= tamValor) {
            $("#inputCodigoCuponMultimarcaManual4").focus();
            var substringValor = this.value.substring(0, 9);
            $("#inputCodigoCuponMultimarcaManual3").val(substringValor);
            evt.stopPropagation();
            return false;
        }
    });
    $("#inputCodigoCuponMultimarcaManual4").change(function (evt) {
        var valorActual = this.value;
        var tamValor = valorActual.length;
        if (2 <= tamValor) {
            var substringValor = this.value.substring(0, 2);
            $("#inputCodigoCuponMultimarcaManual4").val(substringValor);
            evt.stopPropagation();
            return false;
        }
    });

    $("#inputCodigoCuponMultimarcaManual1").focus(function () {
        fn_cambiarInputTecladoAlfanumericoCuponesMultimarca("inputCodigoCuponMultimarcaManual1");
        $(this).select();
    });
    $("#inputCodigoCuponMultimarcaManual2").focus(function () {
        fn_cambiarInputTecladoAlfanumericoCuponesMultimarca("inputCodigoCuponMultimarcaManual2");
        $(this).select();
    });
    $("#inputCodigoCuponMultimarcaManual3").focus(function () {
        fn_cambiarInputTecladoAlfanumericoCuponesMultimarca("inputCodigoCuponMultimarcaManual3");
        $(this).select();
    });
    $("#inputCodigoCuponMultimarcaManual4").focus(function () {
        fn_cambiarInputTecladoAlfanumericoCuponesMultimarca("inputCodigoCuponMultimarcaManual4");
        $(this).select();
    });

	status_cupon && status_pago ? fn_facturarCupon() : '';

	//Impedir el uso del botón "Atrás" del navegador
	if (window.history && history.pushState) {
		history.pushState(null, null, null);
		addEventListener('popstate', function() {
			history.pushState(null, null, null);
			alertify.alert("<p style='font-size: 17px; margin-bottom: 15px; line-height: 2;'>Si desea ir a la anterior pantalla, por favor utilizar el botón de navegación de MaxPoint&nbsp;&nbsp;<img src='../imagenes/regresar2.png' style='border: 1px solid #caa; border-radius: 4px; height: 35px; position: absolute; background-color: #dfeffc;' /></p>");
		});
    }
    
    fn_cargarURLCrearPedidoBringg();


	//Impedir el uso del botón "Atrás" del navegador
	if (window.history && history.pushState) {
		history.pushState(null, null, null);
		addEventListener('popstate', function() {
			history.pushState(null, null, null);
			alertify.alert("<p style='font-size: 17px; margin-bottom: 15px; line-height: 2;'>Si desea ir a la anterior pantalla, por favor utilizar el botón de navegación de MaxPoint&nbsp;&nbsp;<img src='../imagenes/regresar2.png' style='border: 1px solid #caa; border-radius: 4px; height: 35px; position: absolute; background-color: #dfeffc;' /></p>");
		});
    }
    
    localStorage.removeItem('id_agregador');

    var id_menu_facturacion = localStorage.getItem("id_menu");
    var id_cla_facturacion = localStorage.getItem("id_cla");
    var es_menu_agregador_facturacion = localStorage.getItem("es_menu_agregador");

    localStorage.setItem("id_menu_facturacion", id_menu_facturacion);
    localStorage.setItem("id_cla_facturacion", id_cla_facturacion);
    localStorage.setItem("es_menu_agregador_facturacion", es_menu_agregador_facturacion);

    cargarConfiguracionCambioEstadosAutomaticoPorFactura();
    
    var condicionFOP = $( "#condicionFacOrdenPedido" ).val();

    if ( condicionFOP == 4 || condicionFOP == 10 )
    {
        /*
            Configuración Promesa de Forma de pago: Tarjetas:
                "Descripcion": "PINPAD MAIN RED",
                "autorizado": "1",
                "idIntegracion": "7",
                "secuencia": "Armar_Trama->Esperar_Respuesta->Insertar_Pago",
                "secuenciaConfigurada": "1"
        */

        persistirFPTarjetas();
    }
    validarSiExistenProductosSubsidiadosDeuna();
    validarEstadoDeOdp();
});

function validarEstadoDeOdp() {
    console.log("entro a validar estado");
    let cdn_id = $("#txtCadenaId").val();
    let usr_id = $("#txtUserId").val();
    let rst_id = $("#hide_rst_id").val();
    let IDEstacion = $("#IDEstacionDeUna").val();
    let valor = parseFloat($("#pagoGranTotal").val());
    let odp_id = $("#txtOrdenPedidoId").val();
    var send = { formaPagoDeUna: 1, valor: valor, cdn_id: cdn_id, usr_id: usr_id, rst_id: rst_id, odp_id, IDEstacion: IDEstacion };
    var es_menu_agregador = localStorage.getItem("es_menu_agregador");
    send.es_menu_agregador = es_menu_agregador;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        url: "../DeUna/verificarEstadoDeOdpDeUna.php",
        data: send,
        beforeSend: function () {
        },
        success: function (datos) {
            console.log("exito en consulta");
            console.log(datos);
            if (datos.status != null && datos.status != undefined && datos.status != "") {
                if (datos.status == 200) {
                    if (datos.statusOrden == "PENDING") {
                        fn_obtenerTransactionIdDeUna(datos.requestId, true);
                        let granTotal = $("#pagoGranTotal").val();
                        $("#pagado").val(granTotal);
                        let valor = 0 * -1;
                        $("#valorCambio").val(valor.toFixed(2))

                    }
                    if (datos.statusOrden == "REQUESTED") {
                        let granTotal = $("#pagoGranTotal").val();
                        $("#pagado").val(granTotal);
                        let valor = 0 * -1;
                        $("#valorCambio").val(valor.toFixed(2));
                        $("#hid_cambio").val(valor.toFixed(2));
                        fn_obtenerTransactionIdDeUna(datos.requestId, true);
                        cargandoDeUnaModal(1);
                        $("#RequestIdDeUna").val(datos.requestId);
                    }
                    if (datos.statusOrden == "APPROVED") {
                        let granTotal = $("#pagoGranTotal").val();
                        $("#pagado").val(granTotal);
                        let valor = 0 * -1;
                        $("#valorCambio").val(valor.toFixed(2));
                        $("#hid_cambio").val(valor.toFixed(2));
                        cargandoDeUnaModal(0);
                        fn_insertarFormaPago(true);
                        let falta = parseFloat($("#pagoTotal").val());
                        let pagoGranTotal = parseFloat($("#pagoGranTotal").val());
                        if (falta <= 0) {
                            let pagado = parseFloat($("#pagado").val());
                            if (pagado >= pagoGranTotal) {
                                fn_validaCuadreFormaPago();
                            }
                        }

                    }

                }
            }
        },
        error: function (e) {
            console.log("error");
            console.log(e);
        }
    });
}

function fn_cambioEstadosAutomatico() {
    send = { metodo: "cambioEstadosAutomatico" };
    send.cdn_id = $("#txtCadenaId").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_app.php",
        data: send,
        success: function( datos ) {
            if(datos && datos[0]){    
                $("#cambio_estados_automatico").val(datos[0].automatico);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        }
    }
    );
  
  }

function fn_emitirAlarmaLuces(numero_orden) {
    send = { getLucesConfig: 1 };
    $.getJSON("../luces/config_luces.php", send, function (datos) {
        console.log(datos);
        if (datos.str > 0) {
            let numOrden = datos[0].numOrden;
            if (numOrden != null && numero_orden.toString() == numOrden.toString()) {
                let url = datos[0].url;
                let duration = datos[0].duration;
                let sound = datos[0].sound;
                $.getJSON(url + '?tiempo=' + duration.toString() + '&sound=' + sound.toString(), {}, function (datosSirena) {
                    console.log(datosSirena);
                });
            }
        }
    });
}

function cargarConfiguracionCambioEstadosAutomaticoPorFactura() {
    let cfac_id = $("#txtNumFactura")?.val();

    if (cfac_id) {       
        let parametrosCambioEstadosAutomaticoPorFactura = { 
            metodo: "obtenerConfiguracionCambioEstadosAutomatico",
            cfac_id
        };
        
        $.ajax({
            async: false,
            type: "POST",
            dataType: 'json',
            contentType: "application/x-www-form-urlencoded",
            url: "../ordenpedido/config_app.php",
            data: parametrosCambioEstadosAutomaticoPorFactura,
            success: function( respuestaConfiguracion ) {   
                if ( $("#cambio_estados_automatico").length ) {
                    $("#cambio_estados_automatico").val(respuestaConfiguracion?.cambio_estado_automatico === "SI" ? 1 : 0 );
                }
                if ( $("#nombre_proveedor_por_medio").length ) {
                    $("#nombre_proveedor_por_medio").val(respuestaConfiguracion?.nombre_proveedor.toUpperCase().trim());  
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
              console.error(jqXHR);
              console.error(textStatus);
              console.error(errorThrown);
            },
        });
    }
}

function fn_cargarURLCrearPedidoBringg(){
    send = { metodo: "cargarURLCrearPedidoBringg" };
    send.rst_id = $("#txtRestaurante").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_app.php",
        data: send,
        success: function( datos ) {
            if(datos && datos[0]){    
                $("#url_bringg_crear").val(datos[0].url);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        }
    }
    );
}


function fn_facturarCupon() {
    $("#btnAplicarPago").css("display", "none");
    $("#btn_menuU").attr("disabled", "disabled");
    $(".btnDinero").attr("disabled", "disabled");
    $(".btnVirtualCalculadora").attr("disabled", "disabled");
    alertify.confirm("Esta seguro de realizar la factura de Autoconsumo ?");
    $("#alertify-ok").click(function () {
        fn_imprimirOrden(0);
        fn_validaCuponesAutoConsumo();
        fn_bloquearIngreso();
        $("#btnClienteModificar").hide();
        $("#btnFacturaImprimir").hide();
        fn_validaItemPagado();
    });
    $("#alertify-cancel").click(function () {
        alertify.error("No se realizó el canje");
    });
}

function fn_cambiarInputTecladoAlfanumericoCuponesMultimarca(idInputNuevo) {
    var inputNuevo = document.getElementById(idInputNuevo);
    fn_alfaNumerico(inputNuevo);
}

function mostrarOcultarInputsLectura(tipoLectura) {
    switch (tipoLectura) {
        case 0:
            //Tipo de lectura Automatico
            $("#cntInputCodigoCuponMultimarcaAutomatico").show();
            $("#inputCodigoCuponMultimarcaAutomatico").focus();
            $("#cntInputCodigoCuponMultimarcaManual").hide();
            $("#keyboard").hide();
            break;
        case 1:
            //Tipo de lectura Manual
            $("#cntInputCodigoCuponMultimarcaAutomatico").hide();
            $("#cntInputCodigoCuponMultimarcaManual").show();
            $("#inputCodigoCuponMultimarcaManual1").focus();
            break;
    }
    tipoLecturaCodigoDescuentoMultimarca = tipoLectura;
}

function fn_validarCuponMultimarca() {
    /* 
     * var pagadoo = parseFloat($("#pagado").val());
     */
    //Buscar el código que se insertó

    var codigo = "";
    if (tipoLecturaCodigoDescuentoMultimarca === 0) {
        // Tomar valor de código automático
        var codigoAutomatico = $("#inputCodigoCuponMultimarcaAutomatico").val();
        $("#inputCodigoCuponMultimarcaAutomatico").val("");
        var resultadoValidacion = codigoAutomatico.match(/^[A-Z]\d{6}[A-Z]\d{17}\w{5}\d{5}/gm);
        if (null === resultadoValidacion) {
            alertify.alert("Código Inválido");
            return false;
        }

        var codigoObj = {
            metodo: "verificarAuto",
            codigo: codigoAutomatico
        };
        $("#inputCodigoCuponMultimarcaAutomatico").val("");
        // Verificar validez del cupon en gerente
        var $peticionValidacion = $.post("clienteCanjearCuponesMultimarca.php",
            codigoObj,
            function (datos) {

                if (datos.estado === 0) {
                    if (datos.Mensaje) {
                        alertify.alert(datos.Mensaje);
                    } else {
                        alertify.alert(datos.mensaje);
                    }
                    return false;
                }
                alertify.confirm("Cupón válido, Monto: $" + datos.monto + ", Canjear?");
                $("#alertify-ok").click(function () {
                    $("#modalCuponMultimarca").dialog("close");
                    fn_canjearCuponMultimarca(codigoAutomatico, datos.monto);
                    return true;
                });
                $("#alertify-cancel").click(function () {
                    alertify.error("No se realizó el canje");
                });
            },
            "json"
        );

    } else if (tipoLecturaCodigoDescuentoMultimarca === 1) {
        // Tomar valor de código manual incremental - solicitud/cod_cupon/num_detalle
        var incremental = $("#inputCodigoCuponMultimarcaManual1").val();
        var solicitud = $("#inputCodigoCuponMultimarcaManual2").val();
        var cod_cupon = $("#inputCodigoCuponMultimarcaManual3").val();
        var num_detalle = $("#inputCodigoCuponMultimarcaManual4").val();
        var codigoObj = {
            metodo: "verificarManual",
            incremental: incremental,
            codigoSolicitud: solicitud,
            codigoSeguridad: cod_cupon,
            codigoUsuario: "",
            detalle: num_detalle
        };
        // Verificar validez del cupon en gerente
        $peticionValidacion = $.post("clienteCanjearCuponesMultimarca.php",
            codigoObj,
            function (datos) {
                if (datos.estado === 0) {
                    if (datos.Mensaje)
                        alertify.alert(datos.Mensaje);
                    else
                        alertify.alert(datos.mensaje);
                    return false;
                }
                alertify.confirm("Cupón válido, Monto: $" + datos.monto + ", Canjear?");
                $("#alertify-ok").click(function () {
                    $("#modalCuponMultimarca").dialog("close");
                    fn_canjearCuponMultimarca(datos.codigoCupon, datos.monto);
                    return true;
                });
                $("#alertify-cancel").click(function () {
                    alertify.error("No se realizó el canje");
                });
            },
            "json"
        );

        // Buscar codigo del cupon en gerente
    }
}

function fn_canjearCuponMultimarca(codigo, monto) {
    var codigoObj = {
        metodo: "canjear",
        codigo: codigo,
        monto: monto
    };
    // Ejecutar el canje en gerente
    $.post("clienteCanjearCuponesMultimarca.php",
        codigoObj,
        function (datos) {
            if (datos.estado === 0) {
                if (datos.Mensaje) {
                    alertify.alert(datos.Mensaje);
                } else {
                    alertify.alert(datos.mensaje);
                }
                return false;
            }
            cuponMultimarcaCanjeado = codigoObj.codigo;
            // Modificar el lugar donde se ponga el valor de la forma de pago
            var pagado = datos.monto;
            var aPagar = parseFloat($("#pagoTotal").val());

            var valor = aPagar - pagado;
            //  $("#pagoTotal").val(valor.toFixed(2));
            $("#pagado").val(pagado);
            // Aplicar Forma de pago
            fn_insertarFormaPago();
            fn_obtieneTotalApagar();
            fn_resumenFormaPago();
            alertify.success(datos.mensaje);
            if (valor <= 0) {
                fn_envioFactura();
            }
        },
        "json"
    );
}

function fn_modalCuponMultimarca() {
    $("#inputCodigoCuponMultimarcaAutomatico").html("");
    $("#modalCuponMultimarca").dialog("open");
}

function fn_aplicaPagoPredeterminado() {

    var send;

    var es_menu_agregador = localStorage.getItem("es_menu_agregador");

    send = { aplicaPagoPredeterminado: 1 };
    send.es_menu_agregador = es_menu_agregador;
    $.getJSON("config_facturacion.php", send, function(datos) {
      
        if (datos.str > 0) {
            
            fn_botonPago(datos.fmp_descripcion, datos.fmp_id, datos.tfp_id, datos.autorizacion, datos.tfp_descripcion);
        }
        validarSiExistenProductosSubsidiadosDeuna();
    });
}

function fn_validaSalirOrden() {
    send = { validaSalirOrden: 1 };
    $.getJSON("config_facturacion.php", send, function (datos) {
        if (datos.rst_retoma_orden == 1) {
            $("#btn_salirOrden").show();
        }
        if (datos.rst_retoma_orden == 0) {
            $("#btn_salirOrden").hide();
        }
    });
}

function fn_facturar() {
    fn_insertarFactura();
    fn_formasPago();
    fn_ticketPromedio();
    //fn_ValidarFacturaTarjeta();
}

function fn_ticketPromedio() {
    send = { ticketPromedio: 1 };
    $.getJSON("config_facturacion.php", send, function (datos) {
        $("#ticketPromedio").empty();
        if (datos.str > 0) {
            $("#ticketPromedio").show();
            html = "";
            html += "<label>Ticket Promedio: " + parseFloat(datos.ticketActual).toFixed(2) + "</label><br><label>Ticket Proyectado: " + datos.ticketProyectado + "</label>";
            $("#ticketPromedio").append(html);
        }
    });
}

function ocultarFormasDePagoDeUna() {
    var deUnaId = '';
    $('#tablaFormasPago button').each(function () {
        var id = $(this).attr('id');
        var title = $(this).attr('title');
        if (title == "DE UNA") {
            deUnaId = id;
        } else {
            $("#"+id).hide();
        }
    });
    $("#" + deUnaId).click();
}

function validarSiExistenProductosSubsidiadosDeuna(){
    let odp_id = $("#txtOrdenPedidoId").val();
    var send = { validarFormaPagoDeuna: 1, odp_id};
    //cargando(1);
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        url: "../DeUna/solicitudValidacionFormaPago.php",
        data: send,
        beforeSend: function () {
        },
        success: function (datos) {
            console.log("exito en consulta");
            console.log(datos);

            if (datos.tieneSubsidio != undefined) {
                if (datos.tieneSubsidio == true) {
                    //ocultar o desactivar
                    ocultarFormasDePagoDeUna();
                }
            }
        },
        error: function (e) {
            console.log("error");
            console.log(e);
        }
    });
}

///////////////////////////////////FORMAS DE PAGO//////////////////////////////////////////////////
function fn_formasPago() {
    var send = { formaPago: 1 };
    var descripcionVitality = "";
    var idTFPVitality = "";
    var requiereAVitality = "";
    var descripcionTFPVitality = "";

    var es_menu_agregador = localStorage.getItem("es_menu_agregador");

    send.es_menu_agregador = es_menu_agregador;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#formasPago").empty();
                html4 = "<table id='tablaFormasPago'>";
                for (i = 0; i < datos.str; i++) {
                   if(($("#hide_fidelizacionActiva").val()==0 || ($("#hide_fidelizacionActiva").val()==1 && estadoMasivo!== null && (!estadoMasivo || estadoMasivo=='false'))) && (datos[i]["fmp_descripcion"] === "FIDELIZACION" || datos[i]["fmp_descripcion"] === "CONSUMO RECARGA")) {
                        continue;
                    }
                        if(datos[i]["fmp_descripcion"] === "TARJETAS"){                        
                            $("#TarjetaDescripcion").val(datos[i]["fmp_descripcion"]);
                            $("#TarjetaId").val(datos[i]["fmp_id"]);
                            $("#TarjetaId_tfp").val(datos[i]["tfp_id"]);
                            $("#TarjetaRequiereAutorizacion").val(datos[i]["requiereAutorizacion"]);
                            $("#TarjetaDescripcion_tfp").val(datos[i]["tfp_descripcion"]);    
                        }
                        var tfp_descripcion =
                            datos[i]["fmp_descripcion"] === "FIDELIZACION" ||
                                datos[i]["fmp_descripcion"] === "CONSUMO RECARGA" ?
                                datos[i]["fmp_descripcion"] :
                                datos[i]["tfp_descripcion"];
                        if (datos[i]["fmp_descripcion"] == "EFECTIVO") {
                            $("#fmp_id").val(datos[i]["fmp_id"]);
                            $("#tfp_id").val(datos[i]["tfp_id"]);
                            lc_idefectivo = datos[i]["fmp_id"];
                        } else if (datos[i]["fmp_descripcion"] == "CREDITO EXTERNO") {
                            $("#hidIdCreditoExterno").val(datos[i]["fmp_id"]);
                        } else if (datos[i]["fmp_descripcion"] == "VITALITY") {
                            descripcionVitality = datos[i]["fmp_descripcion"];
                            idTFPVitality = datos[i]["tfp_id"];
                            requiereAVitality = datos[i]["requiereAutorizacion"];
                            descripcionTFPVitality = tfp_descripcion;
                        }

                        if (i % 2 == 0) {
                            html4 += "<tr>";
                            if (datos[i]["fmp_imagen"] == "0") {
                                if (datos[i]["fmp_descripcion"] == "EFECTIVO") {
                                    $("#fmp_id").val(datos[i]["fmp_id"]);
                                    $("#tfp_id").val(datos[i]["tfp_id"]);
                                    lc_idefectivo = datos[i]["fmp_id"];
                                }
                                html4 +=
                                    "<td style='vertical-align:middle;'><button style='background: #E6E6E6 no-repeat center center' class='btnVirtualCalculadora btnTarjetas' id='" +
                                    datos[i]["fmp_id"] +
                                    "'  title='" +
                                    datos[i]["fmp_descripcion"] +
                                    "'";
                                html4 +=
                                    "onclick='fn_botonPago(\"" +
                                    datos[i]["fmp_descripcion"] +
                                    '","' +
                                    datos[i]["fmp_id"] +
                                    '","' +
                                    datos[i]["tfp_id"] +
                                    '",' +
                                    datos[i]["requiereAutorizacion"] +
                                    ',"' +
                                    datos[i]["tfp_descripcion"] +
                                    "\")'><b>" +
                                    datos[i]["fmp_descripcion"] +
                                    "</b></button></td>&nbsp;";
                            } else {
                                if (datos[i]["fmp_descripcion"] == "EFECTIVO") {
                                    $("#fmp_id").val(datos[i]["fmp_id"]);
                                    $("#tfp_id").val(datos[i]["tfp_id"]);
                                    lc_idefectivo = datos[i]["fmp_id"];
                                }
                                html4 +=
                                    "<td style='vertical-align:middle;'><button style='background: #E6E6E6 no-repeat center center url(data:image/png;base64," +
                                    datos[i]["fmp_imagen"] +
                                    "' class='btnVirtualCalculadora btnTarjetas' id='" +
                                    datos[i]["fmp_id"] +
                                    "' title='" +
                                    datos[i]["fmp_descripcion"] +
                                    "'";
                                html4 +=
                                    "onclick='fn_botonPago(\"" +
                                    datos[i]["fmp_descripcion"] +
                                    '","' +
                                    datos[i]["fmp_id"] +
                                    '","' +
                                    datos[i]["tfp_id"] +
                                    '",' +
                                    datos[i]["requiereAutorizacion"] +
                                    ',"' +
                                    datos[i]["tfp_descripcion"] +
                                    "\")'></button></td>&nbsp;";
                            }
                        }
                        if (i % 2 == 1) {
                            if (datos[i]["fmp_imagen"] == "0") {
                                if (datos[i]["fmp_descripcion"] == "EFECTIVO") {
                                    $("#fmp_id").val(datos[i]["fmp_id"]);
                                    $("#tfp_id").val(datos[i]["tfp_id"]);
                                    lc_idefectivo = datos[i]["fmp_id"];
                                }
                                html4 +=
                                    "<td style='vertical-align:middle;'><button style='background: #E6E6E6 no-repeat center center' class='btnVirtualCalculadora btnTarjetas' id='" +
                                    datos[i]["fmp_id"] +
                                    "'  title='" +
                                    datos[i]["fmp_descripcion"] +
                                    "'";
                                html4 +=
                                    "onclick='fn_botonPago(\"" +
                                    datos[i]["fmp_descripcion"] +
                                    '","' +
                                    datos[i]["fmp_id"] +
                                    '","' +
                                    datos[i]["tfp_id"] +
                                    '",' +
                                    datos[i]["requiereAutorizacion"] +
                                    ',"' +
                                    datos[i]["tfp_descripcion"] +
                                    "\")'><b>" +
                                    datos[i]["fmp_descripcion"] +
                                    "</b></button></td>&nbsp;";
                            } else {
                                if (datos[i]["fmp_descripcion"] == "EFECTIVO") {
                                    $("#fmp_id").val(datos[i]["fmp_id"]);
                                    $("#tfp_id").val(datos[i]["tfp_id"]);
                                    lc_idefectivo = datos[i]["fmp_id"];
                                }
                                html4 +=
                                    "<td style='vertical-align:middle;'><button style='background: #E6E6E6 no-repeat center center url(data:image/png;base64," +
                                    datos[i]["fmp_imagen"] +
                                    "' class='btnVirtualCalculadora btnTarjetas' id='" +
                                    datos[i]["fmp_id"] +
                                    "' title='" +
                                    datos[i]["fmp_descripcion"] +
                                    "'";
                                html4 +=
                                    "onclick='fn_botonPago(\"" +
                                    datos[i]["fmp_descripcion"] +
                                    '","' +
                                    datos[i]["fmp_id"] +
                                    '","' +
                                    datos[i]["tfp_id"] +
                                    '",' +
                                    datos[i]["requiereAutorizacion"] +
                                    ',"' +
                                    datos[i]["tfp_descripcion"] +
                                    "\")'></button></td>&nbsp;";
                            }
                            html4 += "</tr>";
                        }
                }
                $("#formasPago").append(html4 + "</table>");
                $("#btnFormaPagoId").val(lc_idefectivo);
            }
        }

    });


    var soloPuntos = false;
    if ($("#listaFactura> li").html() === null) {
        soloPuntos = true;
    }
    var sinPlusPuntos = $("#listaFactura_canje_puntos").html() === null;
    var sinPlusNormales = $("#listaFactura > li").html() === null;
    var vitality = $("#hidVitality").val();
    if (vitality == 1) {
        $("#tablaFormasPago tr td").each(function () {
            if ($(this).find("button").attr("title") !== "VITALITY") {
                $(this).find("button").css("display", "none");
            }
        });
        fn_botonPago(descripcionVitality, "", idTFPVitality, requiereAVitality, descripcionTFPVitality);
    } else {
        $("#tablaFormasPago tr td").each(function () {
            if ($(this).find("button").attr("title") === "VITALITY") {
                $(this).find("button").css("display", "none");
            }
        });
        if (sinPlusPuntos) {
            $("#tablaFormasPago tr td").each(function () {
                if ($(this).find("button").attr("title") === "FIDELIZACION") {
                    $(this).find("button").css("display", "none");
                }
            });
        }
        if (sinPlusNormales) {
            $("#tablaFormasPago tr td").each(function () {
                if (
                    $(this)
                        .find("button")
                        .attr("title") !== "FIDELIZACION"
                ) {
                    $(this).find("button").css("display", "none");
                }

            });
            if (soloPuntos) {
                $("#tablaFormasPago tr td").each(function () {
                    if (
                        $(this)
                            .find("button")
                            .attr("title") !== "FIDELIZACION"
                    ) {
                        $(this).find("button").css("display", "none");
                    }
                });
                $("#hid_descTipoFp").val("FIDELIZACION");
                $("#btnAplicarPago").html("<b>Aplicar <br>FIDELIZACION</b>");
                $("#btnAplicarPago").attr("title", "FIDELIZACION");
                $("#btnCancelarPago").html("<b>Cancelar <br>FIDELIZACION</b>");
                $("#btnCancelarPago").attr("title", "FIDELIZACION");
            }

        }
    }
    //  if (soloPuntos && ($(this).find("button").attr("title") !== "FIDELIZACION") ){
    //               $(this).find("button").css("display", "none");
    //           }  

    if($("#Tarjeta").val()=='1'){
        fn_botonPago($("#TarjetaDescripcion").val(),$("#TarjetaId").val(),$("#TarjetaId_tfp").val(),$("#TarjetaRequiereAutorizacion").val(),$("#TarjetaDescripcion_tfp").val());   
    } 
}

function validacionDeUnaUnSoloPago() {
    let cfac_id = $("#txtNumFactura").val();
    let send = { validacionDeUnaUnSoloPago: 1, cfac_id };
    $("#btnAplicarPago").removeAttr("disabled");
    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        url: "../DeUna/validacionDeUnaUnSoloPago.php",
        data: send,
        beforeSend: function () {
            //cargando(0);
        },
        success: function (datos) {
            console.log(datos);
            if (datos.status == 200) {
                $("#btnAplicarPago").attr("disabled", "disabled");
                Swal.fire({
                    title: 'Error de Formas de Pago.',
                    text: "Una factura solo puede tener una forma de pago de De Una.",
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.value) {
                        console.log(result);
                    }
                })
                return false;
            } else {
                return true;
            }
        },
        error: function (e) {
            
        }
    });
}

/////////////////////////////BOTON APLICAR PAGO/////////////////////////////////
function fn_botonPago(descripcion, id, id_tfp, requiereAutorizacion, descripcion_tfp) {
    if (descripcion === "DE UNA" || descripcion === "DEUNA") {
        console.log("entro a validacion de una")
        validacionDeUnaUnSoloPago()
    }
    lc_autorizacion = requiereAutorizacion;
    $("#txt_tfpId").val(id_tfp);
    $("#hid_descTipoFp").val(descripcion_tfp);
    $("#hid_descFp").val(descripcion);
    $("#btnAplicarPago").empty();
    $("#btnAplicarPago").removeAttr("disabled");

    let tituloBoton = "<b>Aplicar <br/>" + descripcion + "</b>";
    $("#btnAplicarPago").append(tituloBoton);
    $("#btnAplicarPago").attr("title", descripcion);
    $("#btnFormaPagoId").val(id);
    $("#btnFormaPagoId").val(
        $("#hidVitality").val() == "1" ? $("#hidIdCreditoExterno").val() : id
    );
    if ($("#hidVitality").val() == "1") {
        $("#btnCancelarPago").hide();
    } else {
        $("#btnCancelarPago").attr("title", descripcion);
    }
    $("#btnCancelarPago").attr("title", descripcion);

    $("#btnCancelarPago").empty();
    let tituloCancelar = "<b>Cancelar <br/>" + descripcion + "</b>";
    $("#btnCancelarPago").append(tituloCancelar);
    comprobarSaldoRecarga(descripcion);
}

function comprobarSaldoRecarga(descripcion) {
    if (descripcion === "CONSUMO RECARGA") {
        let saldo = parseFloat($("#hide_saldo").val());
        let aPagar = parseFloat($("#pagoTotal").val());
        if (saldo < aPagar) {
            alertify.alert("Saldo ($" + saldo + ") insuficiente para cubrir el total de la transacción.");
        }
    }
}

//////////////////////////////////CERRAR VENTANA////////////////////////////////////////////////
function fn_cerrar() {
    $("#contenedor").hide({
        autoOpen: false,
        hide: {
            effect: "explode",
            duration: 500
        }
    });
}

////////////////////////////////LISTADO DE PRODUCTOS//////////////////////////////////////////
function fn_listaFacturar() {
    $("#listaFactura").empty();
    var totalItem = 0;
    var html = "";
    var send = { listaFactura: 1 };
    send.cdn_id = $("#txtCadenaId").val();
    send.cfac_id = $("#txtNumFactura").val();
    $.ajax({
        async: true,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {
                $("#listaFactura").empty();
                for (var i = 0; i < datos.str; i++) {
                    html = "<li id='item1'><div class='listaproductosCant'>";
                    if (datos[i]["totalizado"] != 0) {
                        html += "" + datos[i]["dtfac_cantidad"] + "";
                    } else {
                        html += "  &nbsp;&nbsp;";
                    }
                    html += "</div>";
                    html +=
                        "<div class='listaproductosDesc'>" +
                        datos[i]["plu_descripcion"] +
                        "</div>";
                    totalItem =
                        datos[i]["dtfac_precio_unitario"] * datos[i]["dtfac_cantidad"];
                    html += "<div class='listaproductosVal'>";
                    if (datos[i]["totalizado"] != 0) {
                        html +=
                            "" +
                            moneda +
                            " " +
                            parseFloat(datos[i]["totalizado"]).toFixed(2) +
                            "";
                    } else {
                        html += "";
                    }
                    html += "</div></li><br/>";
                    cdn_tipoImpuesto = datos[i]["cdn_tipoimpuesto"];
                    lc_cantidadPagada = parseFloat(datos[i]["dtfac_total"]).toFixed(2);
                    $("#btnBaseFactura").val(lc_cantidadPagada);
                    $("#listaFactura").append(html);
                }
                fn_listaTotales();
            }
        }
    });
}
var JSON_TOTALES_FACTURA = "";
function fn_listaTotales() {
    var send = { fac_listaTotales: 1 };
    send.codigodelaFactura = $("#txtNumFactura").val();
    $.ajax({
        async: false,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {
                $("#tblValoresTotales").empty();
                JSON_TOTALES_FACTURA = datos;
                for (var i = 0; i < datos.str; i++) {
                    if (datos[i]["descripcion"] == "TOTAL") {
                        valoresTotales = "<tr style='background-color:#F36;color:#FFF'>";
                        valoresTotales +=
                            "<th width='130px' style='font-size:19px;' align='right'>" +
                            datos[i]["descripcion"] +
                            "</th><th style='font-size:19px;' width='40px' align='center'>" +
                            moneda +
                            "</th>";
                        $("#pagoTotal").val(
                            /*((parseFloat(datos[i]['valor'])).toFixed(2))-*/
                            parseFloat(
                                lc_cantidadPagada
                            ).toFixed(2)
                        );
                        $("#diferenciaPago").val(
                            (parseFloat(datos[i]["valor"]) * -1).toFixed(2)
                        );

                        $("#pagoGranTotal").val(parseFloat(datos[i]["valor"]).toFixed(2));
                        $("#valor_total_factura").val(parseFloat(datos[i]["valor"]).toFixed(2));

                        if (parseFloat(datos[i]["valor"]) === 0 && $("#hide_fidelizacionActiva").val() === "1" && !($("#listaFactura_canje_puntos").html() === null || $("#listaFactura_canje_puntos").html() === "")) {
                            valoresTotales += "<th style='font-size:19px;' width='100px' align='center'> Sin cargos</th>";
                        } else {
                            valoresTotales += "<th style='font-size:19px;' width='100px' align='center'>" + parseFloat(datos[i]["valor"]).toFixed(2) + "</th>";
                        }
                        valoresTotales += "</tr>";

                        if (parseFloat(datos[i]["valor"]) === 0 && $("#hide_fidelizacionActiva").val() === "1" && !($("#listaFactura_canje_puntos").html() === null || $("#listaFactura_canje_puntos").html() === "")) {
                            status_pago = 1;
                            $("#pagoGranTotal").val("Sin Cargos");
                        }
                    } else {
                        valoresTotales = "<tr>";
                        valoresTotales +=
                            "<td width='130px' align='right'>" +
                            datos[i]["descripcion"] +
                            "</td><td width='40px' align='center'>" +
                            moneda +
                            "</td>";
                        valoresTotales +=
                            "<td width='100px' align='center'>" +
                            parseFloat(datos[i]["valor"]).toFixed(2) +
                            "</td>";
                        valoresTotales += "</tr>";
                    }
                    $("#tblValoresTotales").append(valoresTotales);
                }
                fn_resumenFormaPago();
            }
        }
    });
}



/////////////////////////GUARDAR FACTURA/////////////////////////////////////////
function fn_insertarFactura() {
    var esVoucher = $("#txt_esVoucher").val() ? $("#txt_esVoucher").val() : 0;
  
    var status = 0;
    var html = "";
    var html2 = "";
    var html_cabecera = '<div id="listaFactura_canje_puntos">   PUNTOS **************************************************';
    send = { "insertarFactura": 1 };
    send.numCuenta = $("#txtNumCuenta").val();
    send.rst_id = $("#txtRestaurante").val();
    send.odp_id = $("#txtOrdenPedidoId").val();
    send.usr_id = $("#txtUserId").val();
    send.divisionCuenta = esVoucher;
    send.recargaPantalla = window.performance.navigation.type;
    $.ajax({
        async: false,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    sessionStorage.setItem('factura',datos[i]["Cod_Factura"]);
                    $("#txtNumFactura").val(datos[i]["Cod_Factura"]);
                    if (typeof fn_update_order_id_kds === 'function') {
                        fn_update_order_id_kds($("#txtOrdenPedidoId").val(), $("#txtNumFactura").val());
                    }
                    if (datos[i]["btn_cancela_pago"] == 1) {
                        $("#btnCancelarPago").show();
                    } else {
                        $("#btnCancelarPago").hide();
                    }
                }
                $("#listaFactura").empty();
                for (var i = 0; i < datos.str; i++) {
                    if (datos[i]["descuento"] > 0) {
                        var porcenDesc = datos[i]["desc_porcentaje"];

                        if (porcenDesc != 0) {
                            html = "<li id='item1' style='background:#7FCF83;'><div class='listaproductosCant'>";
                        } else {
                            html = "<li id='item1' style='background:#4FB6D8;'><div class='listaproductosCant'>";
                        }

                        if (datos[i]["totalizado"] != 0) {
                            html += "" + parseFloat(datos[i]["dtfac_cantidad"]) + "";
                        } else {
                            html += "  &nbsp;&nbsp;";
                        }

                        html += "</div>";
                        html +=
                            "<div class='listaproductosDesc'>" +
                            datos[i]["plu_descripcion"] +
                            "</div>";
                        totalItem =
                            datos[i]["dtfac_precio_unitario"] * datos[i]["dtfac_cantidad"];
                        html += "<div class='listaproductosVal'>";

                        if (datos[i]["totalizado"] != 0) {
                            html +=
                                "" +
                                moneda +
                                " " +
                                parseFloat(datos[i]["totalizado"]).toFixed(2) +
                                "";
                        } else {
                            html += "";
                        }
                        html += "</div></li><br/>";

                        if (porcenDesc != 0) {
                            html += "<li id='item1' style='background:#7FCF83; font-size:13px; height:18px;'><div class='listaproductosCant'>";
                        } else {
                            html += "<li id='item1' style='background:#4FB6D8; font-size:13px; height:18px;'><div class='listaproductosCant'>";
                        }

                        html += "&ensp;&ensp;";
                        html += "</div>";

                        if (porcenDesc != 0) {
                            html += "<div class='listaproductosDesc' style='text-align: right;'>Descuento: " + porcenDesc + "%</div>";
                        } else {
                            html += "<div class='listaproductosDesc' style='text-align: right;'>Descuento: </div>";
                        }

                        html += "<div class='listaproductosVal'>";
                        html +=
                            "" +
                            moneda +
                            " " +
                            parseFloat(datos[i]["descuento"]).toFixed(2) +
                            "";
                        html += "</div></li><br/>";

                    } else {
                        if (datos[i]["canje_puntos"] === 1) {
                            html2 = "";
                            status = 1;
                            html2 = "<li id='item1' style='background:white;'><div class='listaproductosCant'>";
                            if (datos[i]["totalizado"] != 0) {
                                html2 += "" + datos[i]["dtfac_cantidad"] + "";
                            } else {
                                html2 += "  &nbsp;&nbsp;";
                            }
                            html2 += "</div>";
                            html2 +=
                                "<div class='listaproductosDesc'>" +
                                datos[i]["plu_descripcion"] +
                                "</div>";
                            totalItem =
                                datos[i]["dtfac_precio_unitario"] * datos[i]["dtfac_cantidad"];
                            html2 += "<div class='listaproductosVal'>";
                            if (datos[i]["totalizado"] != 0) {
                                html2 += parseFloat(datos[i]["puntos"]) + " Pts.";
                            } else {
                                html2 += "";
                            }
                            html2 += "</div></li><br/>";

                            html_cabecera += html2;

                            html = "";


                        }
                        if (datos[i]["tipoBeneficioCupon"] === 3) {
                            status_cupon = datos[i]["tipoBeneficioCupon"];
                            descripcion_autoconsumo = "********AUTOCONSUMO********";
                            html =
                                "<li id='item1' style='background:#99fcd5;'><div class='listaproductosCant'>";
                            if (datos[i]["totalizado"] != 0) {
                                html += "" + datos[i]["dtfac_cantidad"] + "";
                            } else {
                                html += "  &nbsp;&nbsp;";
                            }
                            html += "</div>";
                            html +=
                                "<div class='listaproductosDesc'>" +
                                descripcion_autoconsumo +
                                " " +
                                datos[i]["plu_descripcion"] +
                                "</div>";
                            totalItem =
                                datos[i]["dtfac_precio_unitario"] * datos[i]["dtfac_cantidad"];
                            html += "<div class='listaproductosVal'>";
                            if (datos[i]["totalizado"] != 0) {
                                html +=
                                    "" +
                                    moneda +
                                    " " +
                                    parseFloat(datos[i]["totalizado"]).toFixed(2) +
                                    "";
                            } else {
                                html += "";
                            }
                            html += "</div></li><br/>";
                        } else {
                            if (datos[i]["canje_puntos"] !== 1) {
                                html =
                                    "<li id='item1' style='background:white;'><div class='listaproductosCant'>";
                                if (datos[i]["totalizado"] != 0) {
                                    html += "" + datos[i]["dtfac_cantidad"] + "";
                                } else {
                                    html += "  &nbsp;&nbsp;";
                                }
                                html += "</div>";
                                html +=
                                    "<div class='listaproductosDesc'>" +
                                    datos[i]["plu_descripcion"] +
                                    "</div>";
                                totalItem =
                                    datos[i]["dtfac_precio_unitario"] * datos[i]["dtfac_cantidad"];
                                html += "<div class='listaproductosVal'>";
                                if (datos[i]["totalizado"] != 0) {
                                    html +=
                                        "" +
                                        moneda +
                                        " " +
                                        parseFloat(datos[i]["totalizado"]).toFixed(2) +
                                        "";
                                } else {
                                    html += "";
                                }
                                html += "</div></li><br/>";

                            }
                        }
                    }
                    cdn_tipoImpuesto = datos[i]["cdn_tipoimpuesto"];
                    lc_cantidadPagada = parseFloat(datos[i]["dtfac_total"]);
                    $("#btnBaseFactura").val(lc_cantidadPagada);
                    $("#listaFactura").append(html);
                }

                html_cabecera += " </div>";
                if (status === 1) {
                    $("#listaFactura").append(html_cabecera);
                }
                fn_listaTotales();
            } else {
                alertify.alert("Error al registrar la factura");
            }

        }
    });
}

/////////////////////////GUARDAR FORMAS DE PAGO///////////////////////////////////	
function fn_insertarFormaPago(validarRetomarTransDeUna = false,desdeTarjetas=false) {

    var html = "";
    var btnformadepago = $("#btnAplicarPago").attr("title");
    var pagooTotal = parseFloat($("#pagoTotal").val());
    var pagadoo = parseFloat($("#pagado").val());
    var send = { "insertarFormaPago": 1 };
    send.fct_id = $("#txtNumFactura").val();
    send.frmPago_id = $("#btnFormaPagoId").val();
    send.frmPago_numSeg = 0;
    send.tfpSwtransaccional = tfp_wst;

    if (pagooTotal > pagadoo) {
        send.frmPagoBillete = $("#pagado").val();
        send.fctTotal = $("#pagado").val();
    } else if (pagooTotal == pagadoo) {
        send.frmPagoBillete = $("#pagado").val();
        send.fctTotal = $("#pagado").val();
    } else {
        send.frmPagoBillete = $("#pagado").val();
        send.fctTotal = $("#pagoTotal").val();
    }

    $.ajax({
        async: false,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {
                $("#btnCancelarPago").attr("disabled", false);
                if(desdeTarjetas){
                    let totalAPagar = 0;
                    totalAPagar = pagooTotal - pagadoo;
                    $("#pagoTotal").val(totalAPagar.toFixed(2));
                    $("#pagado").val("");
                    $("#espacioCambio span").html("$" + "0.00");
                }
                for (i = 0; i < datos.str; i++) {
                    html += "<tr>";
                    html +=
                        "<td width='130px' align='right'>" +
                        datos[i]["fmp_descripcion"] +
                        "</td><td width='40px' align='center'>$</td>";
                    html +=
                        "<td width='100px' align='center'>" +
                        parseFloat(datos[i]["fpf_total_pagar"]).toFixed(2) +
                        "</td>";
                    html += "</tr>";
                }
                $("#formasPago2").html(html);
            } else {
                $("#btnCancelarPago").attr("disabled", true);
                 console.log("ERROR");
                if (validarRetomarTransDeUna == false) {
                    alertify.alert("Error al registrar la factura");
                }
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alertify.alert("Error al agregar la forma de pago.");
            $("#btnCancelarPago").attr("disabled", true);
        }
    });
}


function fn_timeoutPinpadUnired() {
    timeOutPinpadUnired = setTimeout("fn_detenerProcesoPinpadUnired();", tiempoEspera);
}


function fn_timeout2() {
    timeOut = setTimeout("fn_detenerProcesoEsperaFirma();", tiempoEspera);
}


function fc_timeout3() {
    timeOut = setTimeout("fn_detenerProcesoEsperaFirma();", tiempoEspera);
}

function fn_detenerProceso() {
    cargando(1);
    clearInterval(temporizadorPinpad);
    $("#pagado").val("");
    lc_control = 1;
    alertify.alert("Expiro el tiempo de espera.Vuelva a intentarlo");
    $("#alertify-ok").click(function () {
        event.stopPropagation();
        lc_control = 0;
        fn_resumenFormaPago();
        return false;
    });
}


function fn_detenerProcesoPinpadUnired() {
    cargando(1);
    lc_control = 1;
    clearInterval(TEMPORIZADORUNIRED);
    $("#pagado").val("");
    alertify.alert("Expiro el tiempo de espera.Vuelva a intentarlo");

    send = { "consultaIdSWtimeoutBanda": 1 };
    send.movtimeOutBanda = $("#txtNumFactura").val();
    send.accionSwtTimeouBanda = 2; //bandera de banda
    $.getJSON("config_facturacion.php", send, function () {
        fn_resumenFormaPago();
    });
    $("#alertify-ok").click(function (event) {
        event.stopPropagation();
        lc_control = 0;
        return false;
    });
}


function fn_esperaRespuestaPProduccion() {
    send = { esperaRespuestaRequerimientoAutorizacion: 1 };
    send.cfac = $("#txtNumFactura").val();
    $.ajax({
        async: true,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.existe === 1) {
                fn_funcionMuestraRespuestaPinpadUnired(datos.rsaut_respuesta, datos.cres_codigo, datos.fpf_id, datos.rsaut_id, datos.errorTrama, datos.codigoAutorizador);
            }
        }
    });
}

function fn_esperarPProduccion() {
    send = { "esperaRespuestaRequerimientoAutorizacion": 1 };
    send.cfac = $("#txtNumFactura").val();
    $.ajax({
        async: true,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.existe == 2) {
                fn_esperaRespuestaPProduccion();
            } else {
                fn_funcionMuestraRespuestaPinpadUnired(datos.rsaut_respuesta, datos.cres_codigo, datos.fpf_id, datos.rsaut_id, datos.errorTrama, datos.codigoAutorizador);
            }
        }
    });
}


function fn_funcionMuestraRespuestaPinpadUnired(respuesta, codigoRespuesta, formadePago, idRespuesta, tramaError, codigoAutorizador) {
    timeOutPinpadUnired = clearTimeout(timeOutPinpadUnired);
    clearInterval(TEMPORIZADORUNIRED);
    $("#countdown").countdown360().stop();
    $("#txt_trama").val("");
    lc_control = 1;
    cargando(1);
    if (tramaError == 1) {
        if (codigoRespuesta == "00" && codigoAutorizador != "04") {
            send = { "ingresaFormaPagoTarjeta": 1 };
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
            send.SwtTipo = 5; //lc_tipoEnvio;
            if ($("#cantidad").val() == "") {
                send.propina_valor = 0;
            } else {
                send.propina_valor = parseFloat($("#cantidad").val());
            }
            $.getJSON("config_facturacion.php", send, function (datos) {
                send = { "grabacanalmovimientoVoucher": 1 };
                send.respuesta = idRespuesta;
                $.getJSON("config_facturacion.php", send, function (datos) { });
                lc_control = 0;
                var aPagar = parseFloat($("#pagoTotal").val());
                var pagado = parseFloat($("#pagado").val());
                var valor = aPagar - pagado;
                var btnNombre = $("#btnAplicarPago").attr("title");
                $("#pagoTotal").val(valor.toFixed(2));
                $("#pagado").val("");
                fn_resumenFormaPago();
                if (valor <= 0 && btnNombre != "EFECTIVO") {
                    fn_envioFactura();
                }
            });
        } else if (codigoRespuesta == "0") {
            alertify.alert(tramaError);
            return false;
        } else {
            $("#pagado").val("");
            send = { "grabacanalmovimientoVoucher": 1 };
            send.respuesta = idRespuesta;
            $.getJSON("config_facturacion.php", send, function () { });
            alertify.alert(respuesta);
            return false;
        }
    } else {
        $("#pagado").val("");
        alertify.alert(tramaError /* + ". Es posible que el lector de banda magn&eacute;tica no este funcionando correctamente."*/);
        return false;
    }
}

function fn_abreCajon() {
    //    var tipoServicio = $("#txtTipoServicio").val();
    //    if (tipoServicio === "2") {
    //        send = {"EstadoAbrirCajon": 1};
    //        send.op_id = $("#txtOrdenPedidoId").val();
    //        $.getJSON("config_facturacion.php", send, function (datos)
    //        {
    //            if (datos[0]["respuesta"] === "0") {
    //                
    //                        send = {"insertaCanalAperturaCajon": 1};
    //                        send.banderaCajon = $("#txtNumFactura").val();
    //                        $.getJSON("config_facturacion.php", send, function (datos)
    //                        {
    //                        });
    //            }
    //        });
    //
    //    } else {

    //}

    var apiImpresion = getConfiguracionesApiImpresion();
    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {

        send = { "servicioApiAperturaCajon": 1 };
        send.idFormaPago = $("#btnFormaPagoId").val();
        $.getJSON("config_servicioApiAperturaCajon.php", send, function (datos) { 
        
            console.log(datos);

            });

    }else{

        send = { "insertaCanalAperturaCajon": 1 };
        send.banderaCajon = $("#txtNumFactura").val();
        $.getJSON("config_facturacion.php", send, function (datos) { 

        });

    }



}


/////////////////////////ACTUALIZAR FACTURA CLIENTE///////////////////////////////	
function fn_actualizarFactura() {
    fn_promociones_movistar($("#txtNumFactura").val());
	
	let pagoGranTotal = parseFloat($("#pagoGranTotal").val());
    if (isNaN(pagoGranTotal)) {
        pagoGranTotal = 0;
    }
	if(pagoGranTotal==0 && $("#btnAplicarPago").attr("title")=="FIDELIZACION"){
		return; //cuando es canje no se ejecuta esta función -- jean meza
	}
		
   if( localStorage.getItem("invalido") ==1)
     {
        fn_auditoriaRuc();
    } 
    send = { "actualizarFactura": 1 };
    send.cliente_id = $("#txtClienteCI").val();
    send.codFactura = $("#txtNumFactura").val();
    //debugger;
    $.ajax({
        
        async: false,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function(datos) {
            
            localStorage.removeItem("id_menu");
            localStorage.removeItem("id_cla");
            localStorage.removeItem("es_menu_agregador");
            localStorage.removeItem("id_menu_facturacion");
            localStorage.removeItem("id_cla_facturacion");
            localStorage.removeItem("es_menu_agregador_facturacion");

            
            
            if (BANDERA_AGREGADOR != 1) {
                console.log("Entre a notificacion Delivery");
            
                //Notificacion a Delivery
                let nombreProveedorDelivery = $("#nombre_proveedor_por_medio")?.val();
            
                if (nombreProveedorDelivery) {
                let metodosDelivery = {
                    "BRINGG": () => fn_crearOrdenBringg(),
                    "DUNA": () => crearOrdenDunaAjaxClientes(),
                    "NINGUNO":() => cambioEstadoAutomaticoSinProveedorAjaxClientes()
                };
            
                metodosDelivery[nombreProveedorDelivery]
                    ? metodosDelivery[nombreProveedorDelivery]()
                    : metodosDelivery["BRINGG"]();
                }
            }
            //debugger;
            if (datos.str == 0) {
                alertify.alert("Error al registrar la factura");
            }
                 
        }
    });

    if ( localStorage.getItem("ls_documento"+cuenta) ) {
        localStorage.removeItem("ls_correo"+cuenta);
        localStorage.removeItem("ls_direccion"+cuenta);
        localStorage.removeItem("ls_documento"+cuenta);
        localStorage.removeItem("ls_hayCliente"+cuenta);
        localStorage.removeItem("ls_nombres"+cuenta);
        localStorage.removeItem("ls_telefono"+cuenta);
        localStorage.removeItem("ls_PayPhoneFormularioEnOrdenPedido"+cuenta);
        console.log("remuevodatoslsDocumento")
        
    }

    
    

}

/////////////////////////CREAR FACTURA XML////////////////////////////////////////	
function fn_crearFacturaXml() {
    $.post("../xml/anexo_xml.php", { txtNumFactura: $("#txtNumFactura").val() });
    //alert("fn_crearFacturaXml");
}


function fn_direccionaMesasUorden(tipoDeServicio, idOrden) {
    var send;
    if (tipoDeServicio == 2) {

        var txtNumMesa = $("#txtNumMesa").val();
        var txtOrdenPedidoId = $("#txtOrdenPedidoId").val();
        var tipoImpuesto = $("#tipoImpuesto").val();
        send = { "validaItemPagado": 1 };
        send.odp_id = idOrden;
        $.ajax({
            async: false,
            url: "config_facturacion.php",
            data: send,
            dataType: "json",
            success: function (datos) {
                if (datos.str > 0) {
                    send1 = { "obtenerurlsplit": 1 };
                    send1.mesa_id = $("#txtNumMesa").val();
                    send1.odp_id = $("#txtOrdenPedidoId").val();
                    $.ajax({
                        async: false,
                        type: "GET",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "config_facturacion.php",
                        data: send1,
                        success: function (datos) {
                            if (datos.str > 0) {
                                window.location.replace(".." + datos[0]["url_cuenta"]);
                            }
                        }
                    });
                    //  window.location.replace("../ordenpedido/separarCuentas.php?mesa_id=" + txtNumMesa + "&odp_id=" + txtOrdenPedidoId + "&cdn_tipoimpuesto=incluido&est_ip=::1");
                } else {
                    var mesa_id = $("#txtNumMesa").val();

                    if (mesa_id) {
                        send = { 
                            "obtenerDatosRegresar": 1, 
                            mesa_id,
                            idOrden
                        };

                        $.ajax({
                            async: false,
                            type: "GET",
                            dataType: "json",
                            contentType: "application/x-www-form-urlencoded",
                            url: "../ordenpedido/config_ordenPedido.php",
                            data: send,
                            success: function (datos) {
                                if (datos.str > 0) {
                                    // aux = "?IDPisos=" + datos[0]["IDPisos"] + "&IDAreaPiso=" + datos[0]["IDAreaPiso"];
                                    window.location.href = datos[0]["url"];
                                }
                            }
                        });
                    } else {
                        window.location.replace("../ordenpedido/userMesas.php");
                    }
                }
            }
        });
    } else {
        send = { "validaItemPagado": 1 };
        send.odp_id = idOrden;
        $.ajax({
            async: false,
            url: "config_facturacion.php",
            data: send,
            dataType: "json",
            success: function (datos) {


                if (datos.str > 0) {
                    send1 = { "obtenerurlsplit": 1 };
                    send1.mesa_id = $("#txtNumMesa").val();
                    send1.odp_id = $("#txtOrdenPedidoId").val();
                    $.ajax({
                        async: false,
                        type: "GET",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "config_facturacion.php",
                        data: send1,
                        success: function (datos) {
                            if (datos.str > 0) {
                                window.location.replace(".." + datos[0]["url_cuenta"]);
                            }
                        }
                    });
                    //  window.location.replace("../ordenpedido/separarCuentas.php?mesa_id=" + txtNumMesa + "&odp_id=" + txtOrdenPedidoId + "&cdn_tipoimpuesto=incluido&est_ip=::1");
                } else {
                    fn_obtenerMesa();
                }
                //                if (datos.str > 0) {
                //                    window.location.replace("../ordenpedido/separarCuentas.php?mesa_id=" + txtNumMesa + "&odp_id=" + txtOrdenPedidoId + "&cdn_tipoimpuesto=" + tipoImpuesto);
                //                } else {
                //                    fn_obtenerMesa();
                //                }
            }
        });
    }
}


function fn_direccionaMesasUordenExecute(tipoDeServicio, idOrden) {
    var send;
    if (tipoDeServicio == 2) {

        var txtNumMesa = $("#txtNumMesa").val();
        var txtOrdenPedidoId = $("#txtOrdenPedidoId").val();
        var tipoImpuesto = $("#tipoImpuesto").val();
        send = { "validaItemPagado": 1 };
        send.odp_id = idOrden;
        $.ajax({
            async: false,
            url: "config_facturacion.php",
            data: send,
            dataType: "json",
            success: function (datos) {
                if (datos.str > 0) {
                    send1 = { "obtenerurlsplit": 1 };
                    send1.mesa_id = $("#txtNumMesa").val();
                    send1.odp_id = $("#txtOrdenPedidoId").val();
                    $.ajax({
                        async: false,
                        type: "GET",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "config_facturacion.php",
                        data: send1,
                        success: function (datos) {
                            if (datos.str > 0) {
                                window.location.replace(".." + datos[0]["url_cuenta"]);
                            }
                        }
                    });
                    //  window.location.replace("../ordenpedido/separarCuentas.php?mesa_id=" + txtNumMesa + "&odp_id=" + txtOrdenPedidoId + "&cdn_tipoimpuesto=incluido&est_ip=::1");
                } else {
                    var mesa_id = $("#txtNumMesa").val();

                    if (mesa_id) {
                        send = { "obtenerDatosRegresar": 1 };
                        send.mesa_id = mesa_id;
                        $.ajax({
                            async: false,
                            type: "GET",
                            dataType: "json",
                            contentType: "application/x-www-form-urlencoded",
                            url: "../ordenpedido/config_ordenPedido.php",
                            data: send,
                            success: function (datos) {
                                if (datos.str > 0) {
                                    //aux = "?IDPisos=" + datos[0]["IDPisos"] + "&IDAreaPiso=" + datos[0]["IDAreaPiso"];
                                    // window.location.href = "../ordenpedido/userMesas.php" + aux;
                                    window.location.href = datos[0]["url"];
                                }
                            }
                        });
                    } else {
                        window.location.replace("../ordenpedido/userMesas.php");
                    }
                }
            }
        });
    } else {
        send = { "validaItemPagado": 1 };
        send.odp_id = idOrden;
        $.ajax({
            async: false,
            url: "config_facturacion.php",
            data: send,
            dataType: "json",
            success: function (datos) {
                if (datos.str > 0) {
                    window.location.replace("../ordenpedido/separarCuentas.php?mesa_id=" + txtNumMesa + "&odp_id=" + txtOrdenPedidoId + "&cdn_tipoimpuesto=" + tipoImpuesto);
                } else {
                    fn_obtenerMesa();
                }
            }
        });
    }
}

// MAISOV FUNCIONES API
function fn_cancelar_masivo(){
    $("#reintentosMasivoApi").hide()
}

function fn_reintentar_masivo(){
    let rein = localStorage.getItem('reintentos') * 1 
    if(rein <= 1){
        let reintentos = (localStorage.getItem('reintentos') * 1) + 1
        localStorage.setItem('reintentos', reintentos)
    }
    $("#reintentosMasivoApi").hide()
    $("#continuarMasivoApi").show()
}

function fn_continuar_masivo() {
    $("#continuarMasivoApi").hide()
    fn_validaItemPagado()
}

//////////////////////////DIVISION CUENTAS////////////////////////////////////////
function fn_validaItemPagado() {
    let send;
    let txtOrdenPedidoId = $("#txtOrdenPedidoId").val();
    let txtTipoServicio = $("#txtTipoServicio").val();

    let existeUID = (localStorage.getItem('uid') == '' || localStorage.getItem('uid') == undefined) ? false : true
    let appedir = (localStorage.getItem('appedir') == '' || localStorage.getItem('appedir') == undefined) ? false: true;

    let pasa = true;
    let reintentos = localStorage.getItem('reintentos') * 1

    if(reintentos > 1){
        localStorage.setItem('reintentos', 1)
    }

    if(appedir && !existeUID){
        pasa = true;
        if(reintentos <= 1){
            pasa = false;
            console.log('activamos modal de reintentos')
            $("#reintentosMasivoApi").show()
        }
    }

    if((reintentos > 1) && (appedir)){
        pasa = true;
    }else if(!appedir && !existeUID){
        pase = true;
    }

    if(pasa){
        if (txtTipoServicio == 2) {
            RegistroCanjePuntosMasivo(0);
        } else if (txtTipoServicio == 1) {
            RegistroCanjePuntosMasivo(0);
        } //fin de tipo de servicio=1
        else {
            alertify.error("No existe configuracion del tipo de servicio.");
            return false;
        }
    }
    
}

///////////////////////////////////////////REDIRECCIONAR PAGINA///////////////////////////////
function redireccionar(pagina) {
    location.href = pagina;
}

//////////////////////////////////////////IMPRIMIR FACTURA////////////////////////////////////
function fn_printFactura() {
    enviarTransaccionQPM();
    if ($("#txtClienteFono").val() != "") {
        if (
            $("#txtClienteFono").val().length < 7 ||
            $("#txtClienteFono").val().length > 10
        ) {
            alertify.alert("Ingrese un numero de telefono correcto.");
            return false;
        }
    }
    
    // validar facturacion general

    const consumidor_final = $("#txtClienteCI").val();
    const soloNueves = /^9+$/ .test(consumidor_final);

    let sendValidate = { "validaFactura": 1 };
    sendValidate.cfac_id = $("#txtNumFactura").val();
    sendValidate.tipoValidacion = 'VALIDA MONTO CONSUMIDOR FINAL';

    let errorFactura = 0
    let mensajeFactura = '';

    $.ajax({ 
        async: false,
        url: "config_facturacion.php",
        data: sendValidate,
        dataType: "json",
        success: function (datos) {
            if (datos !== null && datos !== undefined && datos !== '') {
                    errorFactura = datos.error;
                    mensajeFactura = datos.mensaje;
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alertify.alert("Error al validar cuadre de factura");
            $("#btnCancelarPago").attr("disabled", true);
        }
    });

    if (errorFactura == 1 && soloNueves) {
        alertify.alert(mensajeFactura);
        $("#btnClienteConfirmarDatos").show();
        fn_clienteNuevo(consumidor_final)
        return false;
    }   


    var btnCambio = $("#btnAplicarPago").attr("title");
    var cambio = parseFloat($("#hid_cambio").val()).toFixed(2);
    cambio = (cambio * -1).toFixed(2);
    cambio = parseFloat(cambio).toFixed(2);
    if ($("#btnFormaPagoId").val() == 5) {
        send = { "ingresaCanalMovimientoCredito": 1 };
        send.FactCreditoCanal = $("#txtNumFactura").val();
        $.getJSON("config_facturacion.php", send, function (datos) { });
    }

    /* Plug Them : Aplica encuesta */
    var conDatos = $("#hid_conDatos").val();
    if (conDatos == 1) {
        validaLoginPlugThem();
        fn_APIkds('apiServicioKds');
    } else {
		fn_actualizarFactura();
		
        fn_APIkds('apiServicioKds');


        fn_validaItemPagado();
    }
    //validamos todos los items pagados
}

function enviarTransaccionQPM() {
    let send={
        transaccion : 'transaccionVendida',
        parametros:{            
            idTransaccion : $("#txtNumFactura").val(),
            rst_id:$("#txtRestaurante").val(),
            cdn_id:$("#txtCadenaId").val(),
            accion : '1',
        }
    };
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../QPM/solicitudMaxPoint.php",
        data: send,
        success: function (datos) {
        console.log(datos);
        }
        , error: function (e) {
            console.log(e);
        }
    });
}

//////funcion para imprimir la orden del pedido
function fn_imprimirOrden(origen) {
    var send = { imprimirOrden: 1 };
    send.odpOrden = $("#txtOrdenPedidoId").val();
    send.dop_id = $("#txtNumCuenta").val();
    send.guardarOrden = 0;
    send.imprimeTodas = 0;
    send.dop_cuenta = $("#txtNumCuenta").val();
    send.numeroLocalizador = numeroLocalizador;
    
    var apiImpresion = getConfiguracionesApiImpresion();
    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
        fn_cargando(1);

        var result = new apiServicioImpresion('orden_pedido', null, send.odpOrden, send);
        var imprime = result["imprime"];
        var mensaje = result["mensaje"];
       

        if (!imprime) {
                  if(mensaje == ''){
                  alertify.success('Imprimiendo Orden de pedido...');
                  }

            if (origen == 0){
                fn_imprimirPromocionFactura(send.odpOrden, send.dop_cuenta)
            }
           
            fn_cargando(0);
            

        } else {
        
            alertify.success('Error al imprimir...');
            fn_cargando(0);

        }
    } else{

        
        $.getJSON("config_facturacion.php", send, function (datos) { });
        fn_imprimirPromocionFactura(send.odpOrden, send.dop_cuenta);

    }

}

function fn_imprimirPromocionFactura(odpOrden, dop_id)
{

    var sendPromocion = { imprimirPromociones: 1 };
    sendPromocion.odpOrden = odpOrden;
    sendPromocion.dop_id = dop_id;
    
    var apiImpresion = getConfiguracionesApiImpresion();
    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
        fn_cargando(1);

        var result = new apiServicioImpresion('promocion_factura', sendPromocion.odpOrden, 0, sendPromocion);
        var imprime = result["imprime"];
        var mensaje = result["mensaje"];

        if (!imprime) {
            fn_cargando(0);
        } else {
            alertify.success('Error al imprimir...');
            fn_cargando(0);
        }

    } else{

        $.getJSON("config_facturacion.php", sendPromocion, function (datos) { });
    }

}

function fn_reimprimirOrdenKiosko() {
    send = { reimprimirOrden: 1 };
    send.odpOrden = $("#txtOrdenPedidoId").val();
    send.dop_id = $("#txtNumCuenta").val();

    var apiImpresion = getConfiguracionesApiImpresion();
    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
        fn_cargando(1);

        var result = new apiServicioImpresion('reimprimir_orden', send.odpOrden, 0, send);
        var imprime = result["imprime"];
        var mensaje = result["mensaje"];

        console.log('imprime: ', imprime);

        if (!imprime) {
            alertify.success('Imprimiendo Orden de pedido...');
            fn_cargando(0);

        } else {
        
            alertify.success('Error al imprimir...');
            fn_cargando(0);

        }
    } else{

        $.getJSON("config_facturacion.php", send, function (datos) { });

    }

}

function fn_actualizarOrdenPedidoApp() {
var nombre = $("#txtClienteNombre").val();
if (nombre != ''){
    var send = {"actualizarOrdenPedidoAppFac": 1};
    send.cfac_id = $("#txtNumFactura").val();
    send.odp_id = $("#txtOrdenPedidoId").val();
    send.nombre = nombre;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_ordenPedido.php",
        data: send,
        success: function (datos) {
  
        }
        , error: function (e) {

        }

    });
}
}

function validarOrdenPedidoKiosko() {
    let parametrosValidacionOrdenPedidoKiosko = {
        validacionOrdenPedidoKiosko: 1,
        odp_id : $("#txtOrdenPedidoId").val()
    };

    let ordenEsKiosko = 0;

    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: parametrosValidacionOrdenPedidoKiosko,
        success: function (datos) {
            if (datos.str > 0) {
                if(datos && Object.keys(datos).length > 0){
                    ordenEsKiosko = datos[0].Resultado;
                }
            } 
        }
    });

    return ordenEsKiosko;
}

//////////////////////////////////////////IMPRIMIR FACTURA////////////////////////////////////
function fn_envioFactura() {
    if ($("#hide_reimpresionKiosko").val() == 1) {
        let verificarOrdenKiosko = validarOrdenPedidoKiosko();

        if (verificarOrdenKiosko === 0) {
            fn_reimprimirOrdenKiosko();
        } 
    }

    fn_actualizarOrdenPedidoApp();
    fn_imprimirOrden(0);
    
    let numFactura = $("#txtNumFactura").val();
    let ultimosTresDigitos = numFactura.substring(numFactura.length - 3);
    fn_emitirAlarmaLuces(parseInt(ultimosTresDigitos));
    
    fn_abreCajon();
    fn_bloquearIngreso();
    $("#btnClienteModificar").hide();
    $("#btnFacturaImprimir").hide();
    fn_validaCuadreFormaPago();
    status_cupon ? fn_validaCuponesAutoConsumo() : "";     
    setTimeout(fn_actualizarOrdenPedidoApp, 2000);
}

function fn_validaCuponesAutoConsumo() {
    send = { autoConsumoCupon: 1 };
    send.IDCabeceraOrdenPedido = $("#txtOrdenPedidoId").val();
    send.dop_cuenta = $("#txtNumCuenta").val() ? $("#txtNumCuenta").val() : 1;
    send.status_cupon = status_cupon;
    send.status_pago = status_pago;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        success: function (datos) {
            alertify.success("La factura Autoconsumo se creó éxitosamente.");
        }
    });
}

/*funcion que valida que cuadren los valores de formas de pago contra el total de la factura*/
function fn_validaCuadreFormaPago() {

    let dataSend = {transaccion: $("#txtNumFactura").val(), 'tipoDocumento': 'factura', 'dataProccess': 'Impresion'};
    let requests = [];

    var apiImpresion = getConfiguracionesApiImpresion();
    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {

        const storedData = localStorage.getItem("requests");
        if (storedData) {
            requests = JSON.parse(storedData);
            requests.push(dataSend);
            localStorage.setItem("requests",  JSON.stringify(requests));
        } else {
            requests.push(dataSend);
            localStorage.setItem("requests", JSON.stringify(requests));
        }

    }

    send = { validaCuadreFormasPago: 1 };
    send.facturaAvalidar = $("#txtNumFactura").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                if (datos.diferencia == 0) {

                    fn_cargaTeclasEmail();
                    $("#btnAplicarPago").removeAttr("disabled");
                    $("#datosFactura").dialog({
                        title: "INFORMACION FISCAL S.R.I.",
                        modal: true,
                        position: "center",
                        closeOnEscape: false,
                        width: 1000,
                        height: 810,
                        show: "blind",
                        resizable: "false"
                    });

                    if ($("#hide_turneroActivo").val() == "1" && $("#hide_turneroHabilitadoPorEstacion").val() == "1") {
                        var txtOrdenPedidoId = $("#txtOrdenPedidoId").val();
                        fn_obtiene_orden(txtOrdenPedidoId);                        
                    }

                    if(localStorage.getItem("ls_hayCliente") === "1"){
                        $("#txtDocumentoClientePaypone").val(  localStorage.getItem("ls_documento"+cuenta));
                        $("#txtClienteDireccion").val(  localStorage.getItem("ls_direccion"+cuenta));
                        $("#txtClienteFono").val(localStorage.getItem("ls_telefono"+cuenta));
                        $("#txtCorreo").val(localStorage.getItem("ls_correo"+cuenta));
                        $("#txtClienteNombre").val(localStorage.getItem("ls_nombres"+cuenta));
                    }

                    if ($("#txtDocumentoClientePaypone").val() !== "") {

                        $("#txtClienteCI").val($("#txtDocumentoClientePaypone").val());

                        setTimeout(function () {
                            cerrar(); // Cierra modal..
                        }, 100);
                        setTimeout(function () {
                            continuar(); // esperar 1 segundo para que pueda ver el cambio.
                        }, 100);

                    }
                    
                    if (!fidelizacionNoActiva() && $("#hide_fidelizacionActiva").val()) {
                        //alertify.error("Su cambio es de $" + $("#valorCambio").val());
                        $("#txtClienteCI").val($("#fdznDocumento").val());
                        setTimeout(function () {
                            cerrar(); // Cierra modal..
                        }, 100);
                        setTimeout(function () {
                            continuar(); // esperar 1 segundo para que pueda ver el cambio.
                        }, 100);
                    }

                    if (!fidelizacionNoActiva() && $("#hide_fidelizacionActiva").val() === "1" && $("#hidNumeroDocumentoVitality").val() !== "0" && $("#hidVitality").val() === "1") {
                        alertify.error("Su cambio es de: " + $("#valorCambio").val());
                        $("#txtClienteCI").val($("#hidNumeroDocumentoVitality").val());
                        setTimeout(function () {
                            cerrar(); // Cierra modal..
                        }, 100);
                        setTimeout(function () {
                            continuar(); // esperar 1 segundo para que pueda ver el cambio.
                        }, 100);
                    } else if ($("#hide_ordenKiosko").val() == "1" || $("#hide_pickupActivo").val() == "1") {
                        validarVentaKiosko();
                    }

                    $(".jb-shortscroll-wrapper").hide();
                    $("#aumentarContador").dialog("open");
                    fn_numerico(txtClienteCI);
                    let tieneSubsidioDeUna = localStorage.getItem("tieneSubsidioDeUna");

                    if(localStorage.getItem("dop_beneficio"+cuenta)){
                        $("#txtClienteCI").attr("disabled", "true");
                        $("#numPadCliente").css('display', 'none');
                        $("#rdo_pasaporte").removeAttr("onClick");
                        $("#rdo_ruc").removeAttr("onClick");
                        var titulo = $("#ui-dialog-title-datosFactura").text();
                        $("#ui-dialog-title-datosFactura").text(titulo + " (NOTA: NO SE PERMITE MODIFICAR EL DOCUMENTO DEBIDO A QUE TIENE UN BENEFICIO ASOCIADO)");
                    } else if (tieneSubsidioDeUna && tieneSubsidioDeUna == "1") {
                        $("#txtClienteCI").attr("disabled", "true");
                        $("#numPadCliente").css('display', 'none');
                        $("#rdo_pasaporte").removeAttr("onClick");
                        $("#rdo_ruc").removeAttr("onClick");
                        var titulo = $("#ui-dialog-title-datosFactura").text();
                        $("#ui-dialog-title-datosFactura").text(titulo + " (NOTA: NO SE PERMITE MODIFICAR EL DOCUMENTO DEBIDO A QUE TIENE UN BENEFICIO ASOCIADO)");
                    }
                    else{
                        $("#txtClienteCI").attr("disabled", "false");
                        $("#numPadCliente").css('display', 'block');
                    }
                    
                } else {
                    console.log("aquiii nan!");
                    console.log(datos.mensaje);
                    alertify.error(datos.mensaje);
                    return false;
                }
            } else {
                alertify.error("ERROR AL VALIDAR CUADRE DE VALORES DE LA FACTURA.");
                return false;
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_obtiene_orden(IDCabeceraOrdenPedido) {       
    var orden = '';
    send = {"obtieneNumeroOrden": 1};
    send.IDCabeceraOrdenPedido = IDCabeceraOrdenPedido;
    /*
    $.getJSON("config_facturacion.php", send, function (datos) {                                
    }).done(function(datos) {
        console.log(datos[0].orden);
        orden = datos[0].orden;  
        
        return orden;
      });
     */
      $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        success: function (datos) {
            if (datos != ''){
                orden = datos[0].orden;                
                // 1-> politica activado
                var txtNumFactura = $("#txtNumFactura").val();
                
                var lc_send = new Object;
                lc_send['turneroAccion'] = 'agregarTurno';
                lc_send['turneroURl'] = $("#hide_turneroURl").val();
                lc_send['transaccion'] = txtNumFactura;
                lc_send['estado'] = 'Preparando';
                lc_send['orden'] = orden;
                
                console.log(orden);

                //txtNumFactura.slice(-2);
                lc_send['cliente'] = '';
                lc_send['clienteDocumento'] = '';
                lc_send['tipo'] = 'LOCAL';
                $.ajax({
                    async: true,
                    type: "POST",
                    dataType: "text",
                    contentType: "application/x-www-form-urlencoded",
                    url: "wsTurnero.php",
                    data: lc_send,
                    success: function (datos) {
                        console.log(datos);
                    }
                }); 
            }
        }
    });  
}

/////////////////////////////VALIDACION DE NUMEROS/////////////////////////////////	
function validarNumeros(e) {
    tecla = document.all ? e.keyCode : e.which; // 2
    if (tecla == 8)
        return true; // backspace
    if (tecla == 109)
        return true; // menos
    if (tecla == 110)
        return true; // punto
    if (tecla == 189)
        return true; // guion
    if (e.ctrlKey && tecla == 86) {
        return true;
    }
    ; //Ctrl v
    if (e.ctrlKey && tecla == 67) {
        return true;
    }
    ; //Ctrl c
    if (e.ctrlKey && tecla == 88) {
        return true;
    }
    ; //Ctrl x
    if (tecla >= 96 && tecla <= 105) {
        return true;
    } //numpad


    patron = /[0-9]/; // patron

    te = String.fromCharCode(tecla);
    return patron.test(te); // prueba
}

/////////////////////////////VALIDACION DE LETRAS/////////////////////////////////
function soloLetras(e) {
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
    especiales = [8, 37, 39, 46];
    tecla_especial = false;

    for (var i in especiales) {
        if (key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }
    if (letras.indexOf(tecla) == -1 && !tecla_especial) {
        return false;
    }
}

/////////////////////////////CAMBIO A MAYUSCULAS/////////////////////////////////
function aMays(e, elemento) {
    tecla = document.all ? e.keyCode : e.which;
    elemento.value = elemento.value.toUpperCase();
}

/////////////////////////////BUSCAR CLIENTES/////////////////////////////////
function fn_consultarCliente() {
    $("#txtClienteCI").autocomplete({
        source: "config_facturacion.php?autoCompletar",
        minLength: 2,
        select: function (event, ui) {
            $("#txtClienteCI").focus();
            $(".ui-helper-hidden-accessible").hide();
        },
        create: function (event, ui) { }
    });
}

/////////////////////////////BILLETES/////////////////////////////////
function fn_billete(billete, descEf, idFp, idTfp, desTfp, autoriza) {
    $("#can_sumar").val(billete);
    $(".desabilitado").attr("Disabled", true);
    //fn_sumarBillete();
    var cant = $("#pagado").val();
    if (cant == "") {
        cant = 0;
    }
    var valor = parseInt(cant) + billete;
    $("#pagado").val(valor.toFixed(2));
    coma = 1;

    fn_botonPago(descEf, idFp, idTfp, autoriza, desTfp);
}

/////////////////////////////BILLETES/////////////////////////////////
function fn_centavo(centavo) {
    var cant = $("#pagado").val();
    if (cant == "") {
        cant = 0;
    }
    var valor = parseFloat(cant) + centavo / 100;
    $("#pagado").val(valor.toFixed(2));
}


function obtenerDatosEnvioPuntos(accion) {
    var send = { "obtenerDatosEnvioPuntos": 1 };
    send.accion = accion;
    send.cfac_id = $("#txtNumFactura").val();
    send.cedulaCliente = $("#fdznDocumento").val();


    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        success: function (datos) {
            if (datos !== undefined) {
                if (accion === 1) {
                    RESPONSE_JSON = datos;
                }
                if (accion === 2) {

                    RESPONSE_JSON2 = datos;
                }
                if (accion === 3) {
                    RESPONSE_JSON3 = datos;
                }

            } else {

            }

        }
    });
}

var EstadoRedimension = 0;

async function ExecuteAutoconsumo(cfac_id, rst_id, secuencial, documentoCli) {
    var totalPuntosCanjeados = 0;
    for (var i = 0; i < RESPONSE_JSON2.length; i++) {
        totalPuntosCanjeados += RESPONSE_JSON2[i].points * RESPONSE_JSON2[i].amount;
    }

    if(totalPuntosCanjeados==0 && TOTAL_PUNTOS_MASIVO >0){
        totalPuntosCanjeados=TOTAL_PUNTOS_MASIVO;
    }

    if (documentoCli === undefined || documentoCli === null || documentoCli === '') {
        documentoCli = $("#txtClienteCI").val();
        if (documentoCli === undefined || documentoCli === null || documentoCli === '') {
            documentoCli = $("#fdznDocumento").val();
        }
    }

    var apiImpresion = getConfiguracionesApiImpresion();
    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {

        var sendOrden = { imprimirOrden: 1 };
        sendOrden.odpOrden = $("#txtOrdenPedidoId").val();
        sendOrden.dop_id = $("#txtNumCuenta").val();
        sendOrden.guardarOrden = 0;
        sendOrden.imprimeTodas = 0;
        sendOrden.dop_cuenta = $("#txtNumCuenta").val();
        sendOrden.fidelizacion = 1;
        sendOrden.numeroLocalizador = numeroLocalizador;
    
        fn_cargando(1);

        var result = new apiServicioImpresion('orden_pedido', null, sendOrden.odpOrden, sendOrden);
        var imprime = result["imprime"];
        var mensaje = result["mensaje"];

        console.log('imprime: ', imprime);

        if (!imprime) {
            alertify.success('Imprimiendo Orden de pedido...');
            fn_cargando(0);
            

        } else {
        
            alertify.success('Error al imprimir...');
            fn_cargando(0);

        }

    }

    if(storeCost==0){
        storeCost=TOTAL_FACTURA_MASIVO;
    }

    EstadoRedimension = 0;
    var send = { "Autoconsumo": 1 };
    send.cfac_id = cfac_id;
    send.rst_id = rst_id;
    send.secuencial = secuencial;
    send.documentoCliente = documentoCli;
    send.nombreCliente = $("#fdznNombre").val().replace("+", "");
    send.puntosCanjeados = totalPuntosCanjeados;
    send.marketingCost = marketingCost;
    send.storeCost = storeCost;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        success: function (datos) {
            if (datos && typeof datos.str !== "undefined" && datos.str > 0) {
                EstadoRedimension = datos[0]["estadoFacturacion"];
                var nuevocfac_id = datos[0]["nuevocfac_id"];
                NUEVA_FACTURA_MASIVO=nuevocfac_id;

                var apiImpresion = getConfiguracionesApiImpresion();

                if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                    fn_cargando(1);

                    var result = new apiServicioImpresion('factura', nuevocfac_id);
                    var imprime = result["imprime"];
                    var mensaje = result["mensaje"];
                    //localStorage.removeItem("requests");

                    console.log('imprime: ', imprime);

                    if (!imprime) {
                        alertify.success('Imprimiendo Factura...');
                    } else {
                        alertify.success('Error...');           
                    }


                } // Fin de la condicion del API
                else {
                    //impresion de la otra factura, revisar en la funcion RegistroCanjePuntosMasivo
                    send = { "actualizaFacturacion": 1 };
                    send.nuFactu = NUEVA_FACTURA_MASIVO;
                    $.getJSON("config_facturacion.php", send, function (datos) {
                        console.log(datos);
                    });
                }

            } else {
                console.log(datos);	
            }
        }
    });
}

var EstadoCanjePuntos = -1;

function RegistroCanjePuntosMasivo(activaCanje){
    TOTAL_PUNTOS_MASIVO=0;
    TOTAL_FACTURA_MASIVO=0;
    TIPO_CANJE_MASIVO="";
    SECUENCIA_FACTURA_MASIVO="";
    activaCanje = activaCanje !== undefined ? activaCanje : 1;
    /*let txtOrdenPedidoId = $("#txtOrdenPedidoId").val();
    let txtTipoServicio = $("#txtTipoServicio").val();*/
    var redimirPuntos=true;
    let listaFactura_canje_puntos=$("#listaFactura_canje_puntos").html();
    let pagoGranTotal = parseFloat($("#pagoGranTotal").val());
    if (isNaN(pagoGranTotal)) {
        pagoGranTotal = 0;
    }

    if (pagoGranTotal>0 && $("#btnAplicarPago").attr("title")!="FIDELIZACION" && $("#fdznDocumento").val() !== "0" && $("#hide_fidelizacionActiva").val() === "1" && (listaFactura_canje_puntos===undefined || listaFactura_canje_puntos === null || listaFactura_canje_puntos === '')){
        console.log("NO SE VAN A REDIMIR PUNTOS EN LA PETICION");
        redimirPuntos=false;
    }

    
    if (NUEVA_FACTURA_MASIVO!="" || (pagoGranTotal==0 && $("#btnAplicarPago").attr("title")=="FIDELIZACION") || activaCanje==1){
        send = { "RegistroCanjePuntosMasivo": 1 };
    }
    else{
        send = { "valida_tipo_facturacion": 1 };
    }
    var barer = localStorage.getItem('tokenM');
    let appedir = (localStorage.getItem('appedir') == 'JV' || localStorage.getItem('appedir') == '' || localStorage.getItem('appedir') == undefined) ? false: true;
    var uidS = (localStorage.getItem('uid') == '' || localStorage.getItem('uid') == undefined) ? '' : localStorage.getItem('uid')
    send.uid = uidS
    send.appedir = appedir
    send.redimirPuntos = redimirPuntos
    send.factura =$("#txtNumFactura").val();
    send.facturaCanje=NUEVA_FACTURA_MASIVO;
    send.IDUsersPos = $("#txtUserId").val();

	send.authorization = barer;
    $.ajax({
        async: false,
        beforeSend: function(xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${barer}`);
        },
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        error: function (response) {
            NUEVA_FACTURA_MASIVO="";
            console.log(response);
            fn_cargando(0);
            alertify.error('Ocurrio un problema con la validacion de facturacion');
            $("#modalCuponMultimarca").dialog("close");
        },
        success: function (datos) {
            facturacion = datos.rst_tipo_facturacion;

            if($("#hide_fidelizacionActiva").val() === "1" && !datos.statusMasivo){
                alertify.error('Ocurrio un problema con la API de masivo');
                NUEVA_FACTURA_MASIVO="";
                fn_cargando(0);
                $("#modalCuponMultimarca").dialog("close");
                return;
            }

            if($("#hide_fidelizacionActiva").val() === "1" && redimirPuntos){
                //canje y acumulacion factura doble aquí
                SECUENCIA_FACTURA_MASIVO=datos.redemptionCode;
                TOTAL_PUNTOS_MASIVO=datos.quantityPoints; // con esto podemos detectar si es canje
                TOTAL_FACTURA_MASIVO=datos.totalBillPoints;
                TIPO_CANJE_MASIVO=datos.tipoCanjeFinal;
                    
                //si es tipo canje y acumulacion hace doble factura
                if (TIPO_CANJE_MASIVO === "CANJE Y ACUMULACION") {
                    RESPONSE_JSON2="";
                    ExecuteAutoconsumo($("#txtNumFactura").val(), $("#txtRestaurante").val(), SECUENCIA_FACTURA_MASIVO, ($('#txtClienteCI').val()??$('#fdznDocumento').val()));
                    alertify.alert("Puntos canjeados exitosamente.");
                    $("#listaFactura_canje_puntos").html("");
                    fn_cargando(0);
                    return;
                }   
            }else{
                TIPO_CANJE_MASIVO="CANJE Y ACUMULACION";
            }
           
            
            /*localStorage.setItem('uid', '');
            localStorage.setItem('tokenM', '');
            localStorage.setItem('appedir', '');
            localStorage.setItem('nameMasivo', ''); */

            if (facturacion == 1) {
                cargando(0);
                fn_timeout2();
            } //fin de facturacion electronica
            if (facturacion == 2) {							
                cfac_idd = $("#txtNumFactura").val();
                send = { "grabacanalmovimientoImpresionFactura": 1 };
                send.idfactura = cfac_idd;
                $.ajax({
                    async: false,
                    type: "GET",
                    dataType: "json",
                    contentType: "application/x-www-form-urlencoded",
                    url: "config_facturacion.php",
                    data: send,
                    success: function (datos) {
                        let txtOrdenPedidoId = $("#txtOrdenPedidoId").val();
                        let txtTipoServicio = $("#txtTipoServicio").val();
                        fn_direccionaMesasUorden(txtTipoServicio, txtOrdenPedidoId);
                        //fn_inicioTomaPedido();

                    }
                });
            } //fin de facturacion preimpresa
            if (facturacion == 4) {
                send = { "claveAcceso": 1 };
                send.factt = $("#txtNumFactura").val();
                send.char = "F";
                $.getJSON("config_facturacion.php", send, function (datos) {
                    if (TIPO_CANJE_MASIVO === "CANJE") {
                        RESPONSE_JSON2="";
                        ExecuteAutoconsumo($("#txtNumFactura").val(), $("#txtRestaurante").val(), SECUENCIA_FACTURA_MASIVO, ($('#txtClienteCI').val()??$('#fdznDocumento').val()));
                        NUEVA_FACTURA_MASIVO="";
                        lc_autorizacion = 0;
                        $("#hid_descTipoFp").val("EFECTIVO");
                        $("#listaFactura_canje_puntos").hide(500);
                        $("#listaFactura_canje_puntos").html(null);
                        $("#mdl_rdn_pdd_crgnd").hide();
                        $("#btnAplicarPago").html("<b>Aplicar <br>EFECTIVO</b>");
                        $("#btnAplicarPago").attr("title", "EFECTIVO");
                        $("#btnCancelarPago").html("<b>Cancelar <br>EFECTIVO</b>");
                        $("#btnCancelarPago").attr("title", "EFECTIVO");
                        alertify.alert("Puntos canjeados exitosamente.");
                        let txtOrdenPedidoId = $("#txtOrdenPedidoId").val();
                        let txtTipoServicio = $("#txtTipoServicio").val();
                        fn_direccionaMesasUorden(txtTipoServicio, txtOrdenPedidoId);
                        //fn_inicioTomaPedido();
                    }else{
                        let apiImpresion = getConfiguracionesApiImpresion();
                        if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                            fn_cargando(1);
                            let result = new apiServicioImpresion('factura', $("#txtNumFactura").val());
                            let imprime = result["imprime"];
                            let mensaje = result["mensaje"];
                            
                            ///finaliza doble
                            if (!imprime) {
                                alertify.success('Imprimiendo Factura...');
                                let txtOrdenPedidoId = $("#txtOrdenPedidoId").val();
                                let txtTipoServicio = $("#txtTipoServicio").val();
                                fn_direccionaMesasUorden(txtTipoServicio, txtOrdenPedidoId);
                                //fn_inicioTomaPedido();
                                //send = { "validaItemPagado": 1 };
                                //send.odp_id = txtOrdenPedidoId;
                            } else {
                                alertify.alert(mensaje, function (e) {
                                    if (e) {
                                        alertify.set({ buttonFocus: "none", ok: "Continuar..." });
                                        let txtOrdenPedidoId = $("#txtOrdenPedidoId").val();
                                        let txtTipoServicio = $("#txtTipoServicio").val();
                                        fn_direccionaMesasUorden(txtTipoServicio, txtOrdenPedidoId);
                                        //fn_inicioTomaPedido();
                                        //send = { "validaItemPagado": 1 };
                                        //send.odp_id = txtOrdenPedidoId;
                                    }
                                });                                    
                            }
                        } else {
                            facturita = $("#txtNumFactura").val();
                            send = { "actualizaFacturacion": 1 };
                            send.nuFactu = $("#txtNumFactura").val();
                            $.getJSON("config_facturacion.php", send, function (datos) {
                                let txtOrdenPedidoId = $("#txtOrdenPedidoId").val();
                                let txtTipoServicio = $("#txtTipoServicio").val();
                                fn_direccionaMesasUorden(txtTipoServicio, txtOrdenPedidoId);
                                //fn_inicioTomaPedido();
                                //send = { "validaItemPagado": 1 };
                                //send.odp_id = txtOrdenPedidoId;
                            });
                        }  
                   }                      
                });
            } //fin facturacion Plan Market
            if (facturacion == 3) {
                send = { "claveAcceso": 1 };
                send.factt = $("#txtNumFactura").val();
                send.char = "F";
                $.getJSON("config_facturacion.php", send, function (datos) {
                    send = { "grabacanalmovimientoImpresionFacturaElectronica": 1 };
                    send.idfactura = $("#txtNumFactura").val();
                    $.getJSON("config_facturacion.php", send, function (datos) {
                        let txtOrdenPedidoId = $("#txtOrdenPedidoId").val();
                        let txtTipoServicio = $("#txtTipoServicio").val();
                        fn_direccionaMesasUorden(txtTipoServicio, txtOrdenPedidoId);
                        //fn_inicioTomaPedido();
                    });
                });
            } //fin de facturacion en contingencia
            
        }
    });
}

function RegistroCanjePuntos(JSON_DATA) {
    EstadoRedimension = 0;
    let send = { "RegistroCanjePuntos": 1 };
    var barer = localStorage.getItem('tokenM')
    let appedir = (localStorage.getItem('appedir') == '' || localStorage.getItem('appedir') == undefined) ? false: true;
    var uidS = (localStorage.getItem('uid') == '' || localStorage.getItem('uid') == undefined) ? '' : localStorage.getItem('uid')
    send.json = JSON.stringify(JSON_DATA);
    send.uid = uidS
    send.appedir = appedir
    send.factura = $("#txtNumFactura").val();
    send.IDUsersPos = $("#txtUserId").val();	
    $.ajax({
        async: false,
        beforeSend: function(xhr) {
            xhr.setRequestHeader('Authorization', `Bearer ${barer}`);
        },
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        success: function (datos) {
            if (datos["message"] === "Canje de puntos realizado exitosamente") {
                localStorage.setItem('uid', '');
                localStorage.setItem('tokenM', '');
                localStorage.setItem('appedir', '');
                localStorage.setItem('nameMasivo', '');
                EstadoCanjePuntos = 200;
            } else {
                if (datos["message"] === "El código de canje ya ha sido usado.") {
                    EstadoCanjePuntos = 1616;

                } else {
                    EstadoCanjePuntos = -1;
                }
            }
            if (datos["code"] == 200) {
                console.info(datos);
                EstadoCanjePuntos = datos["code"];
                marketingCost = datos["data"]["marketingCost"];
                storeCost = datos["data"]["storeCost"];
                redemptionCode = datos["redemptionCode"];
            } else {
                console.error(datos);
                EstadoCanjePuntos = datos["code"];
                $("alertify-cancel").css("display", "none");
                alertify.confirm(datos["message"], function (e) {
                    if (e) {
                        accionesCanjePuntos(EstadoCanjePuntos);
                    }
                }); //lectura PINPAD si esque activa la opcion
            }
            fn_cargando(0);
        },
        error: function () {
            $("#mdl_rdn_pdd_crgnd").hide();
            alertify.alert("Servicio no disponibles, por favor intentelo más tarde.");
        }
    });
}
let accionesCanjePuntos = function (value) {
    switch (value) {
        case 10001212:
            $("#inputCodigoSeguridad").val("");
            $("#cntSeguridadCliente").show();
            $("#inputCodigoSeguridad").focus();
            break;
        default:
            break;
    }
};

function cambiarCodigoSeguridad() {
    var cc = separarCedulaCodigo($("#inputCodigoSeguridad").val());
    var cedula = cc[0];
    var codigo = cc[1];
    if (validarCodigoSeguridad(codigo)) {
        $("#cntSeguridadCliente").hide();
        fn_cargando(1);
        var send = { actualizarCodigoSeguridadCliente: 1 };
        send.codigo = codigo;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function (datos) {
                //Canjear puntos
                $("#inputCodigoSeguridad").val("");
                if (procesoFidelizacion == "") {
                    //canjePuntos();
                    RegistroCanjePuntosMasivo(0);
                } else {
                    consumirRecargaEfectivo();
                }
            }
        });
    } else {
        $("#inputCodigoSeguridad").val("");
    }
}

function darFocoCodigoSeguridad() {
    $("#inputCodigoSeguridad").val("");
    $("#inputCodigoSeguridad").focus();
}

function cerrarModalCodigoSeguridad() {
    $("#inputCodigoSeguridad").val("");
    $("#cntSeguridadCliente").hide();
    fn_cargando(0);
}

function canjePuntos() {


    if ($("#btnAplicarPago").attr("title") === "FIDELIZACION") {
        if ($("#listaFactura_canje_puntos").html() !== null) {

            obtenerDatosEnvioPuntos(1);
            obtenerDatosEnvioPuntos(2);
            obtenerDatosEnvioPuntos(3);

            let JSON_Array = {
                storeId: RESPONSE_JSON[0].storeId,
                storeCode: RESPONSE_JSON[0].storeCode,

                store : {
                    name: RESPONSE_JSON[0].storeName,
                    latitude: RESPONSE_JSON[0].lat,
                    longitude: RESPONSE_JSON[0].lng,
                    city: RESPONSE_JSON[0].storeCity,
                },
                
                vendorId: RESPONSE_JSON[0].vendedorId,
                redemptionCode: RESPONSE_JSON[0].redemptionCode,
                products: RESPONSE_JSON2,
                invoiceCode: $("#txtNumFactura").val(),
                customer: {
                    documentType: RESPONSE_JSON3[0].documentType,
                    document: RESPONSE_JSON3[0].document
                }
            };
            RegistroCanjePuntos(JSON_Array);
            if (EstadoCanjePuntos === 1616) {
                alertify.confirm("El código de canje ya ha sido usado. ¿Desea reenviar solo la factura?");
                $("#alertify-cancel").html("No");
                $("#alertify-ok").html("Si");
                $("#alertify-ok").click(function () {
                    reenviarFactura(200);
                });
            }
            if (EstadoCanjePuntos == 200) {
                //Oculto el boton de redimir.
                $("#tablaFormasPago tr td").each(function () {
                    if (
                        $(this)
                            .find("button")
                            .attr("title") === "FIDELIZACION"
                    ) {
                        $(this).find("button").css("display", "none");
                    }
                });
                // Enviar JSON_Array y esperar respuesta ok si es ok ejecutar. 
                ExecuteAutoconsumo($("#txtNumFactura").val(), $("#txtRestaurante").val(), redemptionCode, RESPONSE_JSON3[0].document);
                //if (EstadoRedimension === 1) {
                    lc_autorizacion = 0;
                    $("#hid_descTipoFp").val("EFECTIVO");
                    $("#listaFactura_canje_puntos").hide(500);
                    $("#listaFactura_canje_puntos").html(null);                   
                    $("#btnAplicarPago").html("<b>Aplicar <br>EFECTIVO</b>");
                    $("#btnAplicarPago").attr("title", "EFECTIVO");
                    $("#btnCancelarPago").html("<b>Cancelar <br>EFECTIVO</b>");
                    $("#btnCancelarPago").attr("title", "EFECTIVO");
                    $("#hid_descFp").val("EFECTIVO");
                    $("#btnFormaPagoId").val(lc_idefectivo);
                    eliminarProductosVacios();
                    alertify.alert("Puntos canjeados exitosamente.");
                    fn_cargando(0); 
                    if (typeof($("#listaFactura> li").html()) == 'undefined' || $("#listaFactura> li").html() == null) {
                        $("#alertify-cancel").hide();
                        $("#alertify-ok").click(function () {
                            fn_inicio();
                        });
                    }
            } else {
                alertify.error("Lo sentimos, no puede canjear sus puntos. El servicio no se encuentra disponible.");
                // alertify.error("Ocurrió un problema al canjear los putnos.");
                // si ocurre un  problem volver a efectivo. 
                $("#hid_descTipoFp").val("EFECTIVO");
                $("#btnAplicarPago").html("<b>Aplicar <br>EFECTIVO</b>");
                $("#btnAplicarPago").attr("title", "EFECTIVO");
                $("#btnCancelarPago").html("<b>Cancelar <br>EFECTIVO</b>");
                $("#btnCancelarPago").attr("title", "EFECTIVO");
                $("#hid_descFp").val("EFECTIVO");
                $("#btnFormaPagoId").val(lc_idefectivo);
                fn_cargando(0);
            }

        } else {
            alertify.error("No hay puntos a canjear");
        }
    } else {
        if (
            $("#listaFactura_canje_puntos").html() == null ||
            $("#listaFactura_canje_puntos").html() == ""
        ) {
            $("#txtClienteCI").val($("#fdznDocumento").val()); //
            fn_aplicaPagoSegunTipo();
            fn_cargando(0);
        } else {
            alertify.error("Primero redima los productos con puntos. ");
        }
        fn_cargando(0);
    }
    fn_cargando(0);
}

function eliminarProductosVacios() {
    // Obtener el div listaFactura
    let listaFactura = document.getElementById("listaFactura");
    // Obtener todos los elementos li dentro del div listaFactura
    let elementosLi = listaFactura.getElementsByTagName("li");
    // Recorrer los elementos li
    let elementoLi = "";
    let elementoVal = "";

    for (let i = 0; i < elementosLi.length; i++) {
        elementoLi = elementosLi[i];
    // Verificar si el elementoLi no es el div listaFactura_canje_puntos
    if (!elementoLi.classList.contains("listaFactura_canje_puntos")) {
            // Obtener el elemento con la clase listaproductosVal dentro de elementoLi
            elementoVal = elementoLi.getElementsByClassName("listaproductosVal")[0];
            // Verificar si el elementoVal está vacío o tiene valor cero
            if (!elementoVal.innerText || elementoVal.innerText.trim() === "0") {
            // Eliminar el elementoLi
            elementoLi.remove();
            }
        }
    }
}   

function reenviarFactura(EstadoCanjePuntos) {
    fn_cargando(1);

    if (EstadoCanjePuntos === 200) {

        //Oculto el boton de redimir.
        $("#tablaFormasPago tr td").each(function () {
            if (
                $(this)
                    .find("button")
                    .attr("title") === "FIDELIZACION"
            ) {
                $(this).find("button").css("display", "none");
            }
        });
        // Enviar JSON_Array y esperar respuesta ok si es ok ejecutar. 
        ExecuteAutoconsumo($("#txtNumFactura").val(), $("#txtRestaurante").val(), RESPONSE_JSON[0].redemptionCode, RESPONSE_JSON3[0].document);

        lc_autorizacion = 0;
        $("#hid_descTipoFp").val("EFECTIVO");
        $("#listaFactura_canje_puntos").hide(500);
        $("#listaFactura_canje_puntos").html(null);
        $("#mdl_rdn_pdd_crgnd").hide();
        $("#btnAplicarPago").html("<b>Aplicar <br>EFECTIVO</b>");
        $("#btnAplicarPago").attr("title", "EFECTIVO");
        $("#btnCancelarPago").html("<b>Cancelar <br>EFECTIVO</b>");
        $("#btnCancelarPago").attr("title", "EFECTIVO");
        alertify.alert("Puntos canjeados exitosamente.");
        if ($("#listaFactura> li").html() === null) {
            $("#alertify-cancel").hide();
            $("#alertify-ok").click(function () {
                fn_inicio();
            });
        }
    } else {
        // alertify.error("Ocurrió un problema al canjear los putnos.");
        // si ocurre un  problem volver a efectivo. 
        $("#hid_descTipoFp").val("EFECTIVO");
        $("#btnAplicarPago").html("<b>Aplicar <br>EFECTIVO</b>");
        $("#btnAplicarPago").attr("title", "EFECTIVO");
        $("#btnCancelarPago").html("<b>Cancelar <br>EFECTIVO</b>");
        $("#btnCancelarPago").attr("title", "EFECTIVO");
    }
    fn_cargando(0);
}

function fn_habilitarIngreso() {
    $("#txtClienteNombre").removeAttr("disabled");
    $("#txtClienteDireccion").removeAttr("disabled");
    $("#txtClienteFono").removeAttr("disabled");
    $("#txtCorreo").removeAttr("disabled");
}

function continuar() {

    $("#txtClienteCI").attr("disabled", "true");

    $("#rdo_ruc").hide();
    $("#rdo_pasaporte").hide();

    var btn2 = document.getElementById("btnBuscaCliente");
    if (btn2 !== null) {
        btn2.click();
    }

}

function continuarPayPhone() {


    var btn2 = document.getElementById('btnBuscaCliente');
    if (btn2 !== null) {
        btn2.click();
    }

}
function redireccionar() {
    fn_inicio();
}

function cerrar() { }

function esperar() {
    $("#datosFactura").dialog("close");
}

///////////////////////////// DIFERENCIA /////////////////////////////////
async function fn_diferencia(event) {


    // Configuracion de localizador
    if (showCondicionConfiguracionLocalizador){
        await fn_popUpNumeroLocalizador();
    }

    if (noCerrarModalNumeroLocalizador){
        $("#btnAplicarPago").removeAttr("disabled");
        return;
    }

    localStorage.removeItem('tieneSubsidioDeUna');

    let aplicaFlag = true;
    let promocionOTP = JSON.parse(localStorage.getItem("inpCodigoPromocionOTP")) || [];
    let listaFactura_canje_puntos=$("#listaFactura_canje_puntos").html();
    let pagoGranTotal = parseFloat($("#pagoGranTotal").val());
    if (isNaN(pagoGranTotal)) {
        pagoGranTotal = 0;
    }

    if ($("#btnAplicarPago").attr("title")!="FIDELIZACION" && pagoGranTotal>0 && !(listaFactura_canje_puntos===undefined || listaFactura_canje_puntos === null || listaFactura_canje_puntos === '')){
        alertify.alert("Primero debe redimir los puntos.");
        return false;
    }

    if ($("#btnAplicarPago").attr("title")=="FIDELIZACION" && (listaFactura_canje_puntos===undefined || listaFactura_canje_puntos === null || listaFactura_canje_puntos === '')){
        alertify.alert("Sus puntos ya fueron redimidos, elija otra forma de pago.");
        return false;
    }


    if (localStorage.getItem("inpCodigoPromocionOTP") !== null && promocionOTP.length >0) {
        let promocionOTPArray = JSON.parse(localStorage.getItem("inpCodigoPromocionOTP")) || [];
        let codigoPromocionArray = promocionOTPArray.map(item => item.codigoPromocion);

        let result = await fn_validarOTPTirillaPromocion(codigoPromocionArray,$("#txtNumFactura").val())
        if (result.estado === 200) {
            alertify.success(result.mensaje);
            aplicaFlag = true;
            localStorage.removeItem("inpCodigoPromocionOTP");
        } else {
            aplicaFlag = false;
            alertify.error(result.mensaje);
        }

    }
    if(aplicaFlag){
        setTimeout(enableButton, 1000);

        if( fidelizacionNoActiva() && localStorage.getItem("ls_documento"+cuenta) ) {
            $("#txtClienteCI").val($("#fdznDocumento").val());
            fn_clienteBuscar(false, 0, 0);
            fn_aplicaPagoSegunTipo();
        // Fidelizacion Antigua SOLO APLICA CUANDO ES CANJE Y ACUMULACION
        } else if (pagoGranTotal>0 && $("#btnAplicarPago").attr("title")=="FIDELIZACION" && $("#fdznDocumento").val() !== "0" && $("#hide_fidelizacionActiva").val() === "1" && !(listaFactura_canje_puntos===undefined || listaFactura_canje_puntos === null || listaFactura_canje_puntos === '')) {
            fn_cargando(1);
            event.stopPropagation();
            setTimeout(function() {
                //canjePuntos();
                //EMITIMOS SOLO CANJE CUANDO ES CANJE Y ACUMULACIÓN
                requestAnimationFrame(() => {
                    RegistroCanjePuntosMasivo(0);
                });
            }, 300);
            //fn_cargando(0);
        } else if (pagoGranTotal==0 &&$("#btnAplicarPago").attr("title")=="FIDELIZACION" && $("#fdznDocumento").val() !== "0" && $("#hide_fidelizacionActiva").val() === "1" && !(listaFactura_canje_puntos===undefined || listaFactura_canje_puntos === null || listaFactura_canje_puntos === '')) {
            //cuando es solo canje tambien se aplica directo la facturación
            fn_cargando(1);
            event.stopPropagation();
            setTimeout(function() {
                //canjePuntos();
                //EMITIMOS SOLO CANJE CUANDO ES CANJE Y ACUMULACIÓN
                requestAnimationFrame(() => {
                    RegistroCanjePuntosMasivo(0);
                    fn_imprimirOrden(1);
                });
            }, 300);
            //fn_cargando(0);
        } else
        // Vitality
        if ( $("#hidVitality").val() === "1" ) {
            fn_cargando(1);
            event.stopPropagation();
            var NombreClienteAx = $("#hidNombreClienteVitality").val();
            var NDocClienteAx = $("#hidNumeroDocumentoVitality").val();
            var TelfClienteAx = $("#hidPhoneNumberVitality").val();
            var DireccionclienteAx = $("#hidAddressVitality").val();
            fn_PagoCreditoConfigurados(
                NombreClienteAx,
                NDocClienteAx,
                NDocClienteAx,
                TelfClienteAx,
                DireccionclienteAx,
                "",
                "RUC"
            );
            fn_cargando(0);
        } else {
            setTimeout(enableButton, 1000);
            event.stopPropagation();        
            let pagoGranTotal = parseFloat($("#pagoGranTotal").val());
            if (isNaN(pagoGranTotal)) {
                pagoGranTotal = 0;
            }
            if ( pagoGranTotal<= 0 && $("#btnAplicarPago").attr("title")!="FIDELIZACION") {
                alertify.alert("El valor de la factura no puede ser 0 (cero).");
                return false;
            }

            if ($("#td_falta").is(":visible") && parseFloat($("#pagoTotal").val()) == 0) {
                fn_envioFactura();
            } else {
                if ($("#td_cambio").is(":visible")) {
                    fn_envioFactura();
                } else {
                    if (lc_autorizacion == 1) {
                        console.log('I-8');

                        lc_banderaOkAdmin = "aplicaPago";
                        fn_abreModalCliente();
                        return false;
                    }
                    fn_cargando(1);
                    event.stopPropagation();
                    setTimeout(function() {
                        fn_cargando(0);
                        fn_aplicaPagoSegunTipo();

                    }, 300);

                }
            }
        }

    }

}

function fn_validarOTPTirillaPromocion(otp, codigo_factura) {
    return new Promise(function (resolve) {
        fn_cargando(1);
        existeEnMasterData = 0;
        var send = {
            metodo: "consumoOTP",
            codigo_otp: JSON.stringify(otp),
            codigo_factura: codigo_factura
        };
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            "Accept": "application/json, text/javascript2",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../ordenpedido/config_app.php",
            data: send,
            success: function (result) {
                console.log(result)
                const datos = JSON.parse(result);
                if (datos && datos['status'] == 200) {
                    console.log(datos)
                    resolve({
                        "estado": 200,
                        "mensaje": datos.message ,
                    });
                } else {
                    resolve({
                        "estado": datos.status,
                        "mensaje": datos.message
                    });
                }

                fn_cargando(0);

            },   error: function (xhr, status, error) {
                fn_cargando(0);
                resolve({
                    "estado": 500,
                    "mensaje": "Error de conexion."
                });
            }
        });
    })
}

function enableButton(){
    document.getElementById("btnAplicarPago").disabled = false;
}

function flujoCredito() {
    fn_cargando(1);
    if ($("#btnAplicarPago").attr("title") === "VITALITY") {
        if ($("#listaFactura").html() !== null) {
            obtenerDatosVitality(1);
            obtenerDatosVitality(2);
            obtenerDatosVitality(3);
            var codigoQRVitality = $("#hidCodigoQRVitality").val();
            var TokenSeguridadVitality = $("#hidTokenSeguridadVitality").val();

            var JSON_VoucherVitality = {
                code: RESPONSE_VJSON[0]["code"],
                storeCode: RESPONSE_VJSON[0]["storeCode"],
                invoiceCode: RESPONSE_VJSON[0]["invoiceCode"],
                summary: {
                    subtotal: RESPONSE_VJSON2[0]["subtotal"],
                    vat: RESPONSE_VJSON2[0]["vat"],
                    vatTaxBase: RESPONSE_VJSON2[0]["vatTaxBase"],
                    vatCalculated: RESPONSE_VJSON2[0]["vatCalculated"],
                    total: RESPONSE_VJSON2[0]["total"]
                },
                products: []
            };
            RESPONSE_VJSON3.forEach(function (RESPONSE_VJSON3) {
                JSON_VoucherVitality.products.push({
                    productCode: RESPONSE_VJSON3.productCode,
                    name: RESPONSE_VJSON3.name,
                    unitPrice: RESPONSE_VJSON3.unitPrice,
                    amount: RESPONSE_VJSON3.amount,
                    vat: RESPONSE_VJSON3.vat,
                    vatTaxBase: RESPONSE_VJSON3.vatTaxBase,
                    vatCalculated: RESPONSE_VJSON3.vatCalculated,
                    totalPrice: RESPONSE_VJSON3.totalPrice
                });
            });

            ventaVitality(codigoQRVitality, TokenSeguridadVitality, JSON_VoucherVitality, RESPONSE_VJSON[0]["invoiceCode"]);
        } else {
            fn_cargando(0);
            alertify.error("No se puede facturar");
        }

    } else {
        fn_cargando(0);
        $("#txtClienteCI").val($("#hidNumeroDocumentoVitality").val());
        fn_aplicaPagoSegunTipo();
    }
}


function ventaVitality(codigoQRVitality, TokenSeguridadVitality, JSON_VoucherVitality, codigoFactura) {
    var send = {};
    send.metodo = "VoucherTransaccionesVitality";
    send.codigoQRVitality = codigoQRVitality;
    send.TokenSeguridadVitality = TokenSeguridadVitality;
    send.JSON_VoucherVitality = JSON.stringify(JSON_VoucherVitality);
    send.codigoFactura = codigoFactura;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_VitalityWS.php",
        data: send,
        success: function (datos) {

            if (datos.httpStatus === 200) {
                fn_cargando(0);
                alertify.alert("Cupón Canjeado con éxito.", function () {
                    alertify.message("OK");
                });
                fn_insertaFormaPagoCreditoSinCupon(cantidadP);
                $("#btnAplicarPago").attr("disabled", "disabled");
            } else if (datos.httpStatus === 404) {
                fn_cargando(0);
                alertify.alert("Existen Problemas de Configuracion. Por favor comunicarse con la mesa de Servicio.", function () {
                    alertify.message("OK");
                }
                );
            } else {
                fn_cargando(0);
                alertify.alert("Error de canje de cupón, intente más tarde.", function () {
                    alertify.message("OK");
                }
                );
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error(jqXHR, textStatus, errorThrown);
            fn_cargando(0);
            alertify.error("Servicio no disponible, por favor intentalo más tarde.");
        }
    });
}


function obtenerDatosVitality(accion) {
    var send = { obtenerDatosVitalityFac: 1 };
    send.accion = accion;
    send.codigoQRVitality = $("#hidCodigoQRVitality").val();
    send.cfac_id = $("#txtNumFactura").val();
    send.cedulaCliente = $("#hidNumeroDocumentoVitality").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        success: function (datos) {
            if (datos !== undefined) {
                if (accion === 1) {
                    RESPONSE_VJSON = datos;
                } else if (accion === 2) {
                    RESPONSE_VJSON2 = datos;
                } else if (accion === 3) {
                    RESPONSE_VJSON3 = datos;
                }
            }
        }
    });
}


function eventoClick(paso) {
    if (paso === 1) {
        $("#voucherAE").hide(100);
        $("#seleccionMetodo").show(100);
        $("#infoMdal").val("Seleccione opción automático o manual");
    }
    if (paso === 2) {
        $("#seleccionMetodo").hide(100);
        $("#voucherAE").show(100);
        $("#infoMdal").val("Ingreso Cupón Sistema Gerente");
    }
}

function fn_aplicaPagoSegunTipo() {
    var formaPagoIDSeleccionada = $("#btnFormaPagoId").val();
    var btnNombre = $("#hid_descTipoFp").val();
    var btnNombreFormaPago = $("#hid_descFp").val();
    $(".desabilitado").removeAttr("Disabled");
    $("#visualizarFactura").hide();
    $("#hid_bandera_cvv").val(1);
    var aPagar = parseFloat($("#pagoTotal").val());
    var valoresTotales = "";
    var valoresCambio = "";
    lc_opcionCreditoEmpresa = "";
    var pagado = $("#pagado").val();
    var saldo = $("#hide_saldo").val();
    if ((pagado == "" || pagado == 0) && !(btnNombreFormaPago === "CUPON PREPAGADO")) {
        if (
            btnNombreFormaPago === "CONSUMO RECARGA" &&
            parseFloat(saldo) < aPagar
        ) {
            total = saldo;
        } else {
            total = $("#pagoTotal").val();
        }
        $("#pagado").val(total);
    }
    pagado = parseFloat($("#pagado").val());
    var valor = aPagar - pagado;
    valor = parseFloat(valor);
    var valorVuelto = Math.abs(valor).toFixed(2);
    $("#espacioCambio span").html("$" + valorVuelto);
    $("#hid_cambio").val(valor);
    var total_factura = $("#valor_total_factura").val();

    console.log('VALOR');
    console.log(valor);
    //Consumo de Recargas
    if (btnNombreFormaPago == "CONSUMO RECARGA") {
        if (saldo >= pagado) {
            fn_cargando(1);
            consumirRecargaEfectivo();
        } else {
            alertify.error("Saldo ($" + saldo + ") insuficiente para realizar esta transacción.");
        }
        return false;
    } else if (btnNombreFormaPago === "CUPON PREPAGADO") {
        //TODO: Mostrar Modal de lectura del Código
        fn_modalCuponMultimarca();
        //TODO: Enviar por ajax el valor de la forma de pago
        return false;
    }

    if ((valor <= 0 && btnNombre == "EFECTIVO") || (valor <= 0 && btnNombre == "RETENCIONES") || (valor <= 0 && btnNombre == "CHEQUES")) {
        $("#td_cambio").show();
        $("#td_falta").hide();
        var valorCambio = (valor * -1).toFixed(2);
        fn_insertarFormaPago();
        $("#valorCambio").val(valorCambio);
    } else if (
        valor <= 0 &&
        btnNombre != "EFECTIVO" &&
        (valor <= 0 && btnNombre != "CREDITO EMPRESA") &&
        (valor <= 0 && btnNombre != "RETENCIONES") &&
        (valor <= 0 && btnNombre != "CHEQUES")
    ) {
        if (btnNombre == "PAYPHONE") {
            fn_aplicarPagoPayPhone();
        } else if (btnNombre == "DE UNA") {
            cargandoDeUnaModalIntentos(1, () => {
                fn_aplicarPagoDeUna();
            });
        } else if (btnNombre == "CREDITO EMPLEADO") {
            lc_opcionCreditoEmpresa = "EMPLEADO";
            fn_pagoCredito("EMPLEADO", formaPagoIDSeleccionada);
        } else if (btnNombre == "CREDITO INVENTARIOS") {
            lc_opcionCreditoEmpresa = "INVENTARIO";
            fn_pagoCredito("INVENTARIO", formaPagoIDSeleccionada);
        } else if (btnNombre == "CREDITO EXTERNO") {
            lc_opcionCreditoEmpresa = "EXTERNO";
            fn_pagoCredito("EXTERNO", formaPagoIDSeleccionada);
        } else if (btnNombre == "VOUCHER AEROLINEAS") {
            lc_opcionCreditoEmpresa = "VOUCHERAEREOLINEA";
            fn_pagoCreditoVA("EXTERNO VOUCHERAEREOLINEA");
        } else if (btnNombre == "CREDITO INTERDEPARTAMENTAL") {
            lc_opcionCreditoEmpresa = "INTER";
            fn_pagoCredito("INTER", formaPagoIDSeleccionada);
        } else if (btnNombre == "CREDITO PRODUCTO") {
            lc_opcionCreditoEmpresa = "PRODUCTO";
            fn_pagoCredito("PRODUCTO", formaPagoIDSeleccionada);
        } else if (btnNombre == "FIDELIZACION") {
            $("#mdl_rdn_pdd_crgnd").show();
            lc_opcionCreditoEmpresa = "FIDELIZACION";

            if (true) {
                $("#hid_descTipoFp").val("EFECTIVO");
                $("#btnAplicarPago").html("<b>Aplicar <br>EFECTIVO</b>");
                $("#listaFactura_canje_puntos").hide(1000);
                $("#mdl_rdn_pdd_crgnd").hide();
                alertify.success("Puntos canjeados");
            } else {
                alertify.error("No hay comunicación con el WebService");
            }
        } else if (btnNombre == "AGREGADOR") {
            if (aPagar == total_factura || pagado == total_factura) {
                lc_opcionCreditoEmpresa = "EXTERNO";
                cargarModalAgreagdores();
            } else {
                alertify.alert(
                    "Para aplicar <b>" +
                    btnNombre +
                    "</b>, el valor a pagar debe ser el total de la factura...!!! <br><br>"
                );
                $("#pagado").val("");
                return false;
            }
        }else if(btnNombre == 'CUPON EFECTIVO'){
            console.log('Forma Pago Payvalida');
            fn_aplicarPagoPayvalida();
        }
        else {
            $("#pagado").val(aPagar.toFixed(2));
            // fn_insertaPagoTarjeta();
            validarTipoEnvio();
            $("#txtClienteCI").focus();
            fn_numerico(txtClienteCI);
        }
    } else if ((valor <= 0 && btnNombre == "CREDITO EMPRESA") || (valor > 0 && btnNombre == "CREDITO EMPRESA")) {
        lc_opcionCreditoEmpresa = "EMPRESA";
        fn_pagoCredito("EMPRESA", formaPagoIDSeleccionada);
    } else {
        if (btnNombre == "EFECTIVO" || btnNombre == "RETENCIONES" || btnNombre == "CHEQUES") {
            fn_insertarFormaPago();
            $("#pagado").val("");
            $("#pagoTotal").val(valor.toFixed(2));
        } else if (
            btnNombre != "EFECTIVO" &&
            btnNombre != "RETENCIONES" &&
            btnNombre != "CHEQUES" &&
            btnNombre != "CREDITO EMPRESA"
        ) {
            if (btnNombre == "CREDITO EMPLEADO") {
                if (parseFloat($("#pagado").val()) < parseFloat($("#pagoGranTotal").val())) {
                    alertify.error(
                        "Con la forma de pago seleccionada debe cancelar el valor total de la factura."
                    );
                }
                $("#pagado").val("");
                return false;
            } else if (btnNombre == "CREDITO INVENTARIOS") {
                lc_opcionCreditoEmpresa = "INVENTARIO";
                fn_pagoCredito("INVENTARIO", formaPagoIDSeleccionada);
            } else if (btnNombre == "CREDITO EXTERNO") {
                lc_opcionCreditoEmpresa = "EXTERNO";
                fn_pagoCredito("EXTERNO", formaPagoIDSeleccionada);
            } else if (btnNombre == "CREDITO INTERDEPARTAMENTAL") {
                lc_opcionCreditoEmpresa = "INTER";
                fn_pagoCredito("INTER", formaPagoIDSeleccionada);
            } else if (btnNombre == "CREDITO PRODUCTO") {
                lc_opcionCreditoEmpresa = "PRODUCTO";
                fn_pagoCredito("PRODUCTO", formaPagoIDSeleccionada);
            } else if (btnNombre == "AGREGADOR") {
                alertify.alert(
                    "Para aplicar <b>" +
                    btnNombre +
                    "</b>, el valor a pagar debe ser el total de la factura...!!! <br><br>"
                );
                $("#pagado").val("");
                return false;
            }else if(btnNombre == "CUPON EFECTIVO"){
                fn_aplicarPagoPayvalida();
            } 
            else if (btnNombre == "DE UNA") {
                cargandoDeUnaModalIntentos(1, () => {
                    fn_aplicarPagoDeUna();
                });
            }
            else {
                // fn_insertaPagoTarjeta();
                validarTipoEnvio();
            }
        }
    }

    $("#formasPagoFactura").append(valoresTotales);

    if (valoresCambio != "") {
        $("#formasPagoFactura").append(valoresCambio);
    }

    if ((valor <= 0 && btnNombre == "EFECTIVO") || (valor <= 0 && btnNombre == "RETENCIONES") || (valor <= 0 && btnNombre == "CHEQUES")) {
        fn_buscaPagoCredito();
    }
}

function fn_promociones_movistar(num_factura) {
    let data_to_send = { 'PromocionesMovistar': 1 };
    data_to_send.cfac = num_factura;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        url: "config_facturacion.php",
        data: data_to_send,
        success: function (datos) {
            if (datos.url_ws != 'NO APLICA') {
                dataToSend=fn_check_transferencia(datos.plu_id);
                fn_consume_ws_movistar(datos.url_ws, dataToSend);
            }
        }
    });
}
function fn_check_transferencia(plu_id){
    let dataToCheck = {'checkTransferencia': 1};
    dataToCheck.plu_id = plu_id;
    dataToCheck.codRestaurante = $("#txtRestaurante").val();
    dataToCheck.codCadena = $("#txtCadenaId").val();
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        url: "config_facturacion.php",
        data: dataToCheck,
        success: function (response) {
            if (Number.isInteger(response.PlusOrigen)) { 
                dataToSend = {
                    codFactura: $("#txtNumFactura").val(),
                    codRestaurante: response.Restaurante,
                    codCadena: response.Cadena            
                };
            } else {
                dataToSend = {
                    codFactura: $("#txtNumFactura").val(),
                    codRestaurante: $("#txtRestaurante").val(),
                    codCadena: $("#txtCadenaId").val()            
                };
            };    
        }
    });
    return dataToSend;
}

function fn_consume_ws_movistar(url_data, dataToSend) {
    dataToSend.consumir_ws=1
    dataToSend.url_data=url_data
    $.ajax({
        async: true,
        type: "GET",
        dataType: "json",
        url: "config_facturacion.php",
        data: dataToSend,
        success: function (datos) {
            if (datos[0].status == 'OK') {
                let QR_Data = datos[0].Num_Cupon;
                fn_auditoria_cupones_movistar(datos[0], 'SUCCESS', dataToSend, url_data);
                fn_SETQR_promociones_movistar(dataToSend.codFactura, QR_Data);
            } else {
                fn_auditoria_cupones_movistar(datos[0], 'ERROR', dataToSend, url_data);
            }
        },
        error: function(error) {
            fn_auditoria_cupones_movistar("Error al consumir WS Movistar", 'ERROR', dataToSend, url_data);
        }
    }); 
}

function fn_auditoria_cupones_movistar(respuesta, estado, params, url_ws) {
    let data_to_send = { 
        'auditoria_cupones_movistar': 1,
        atran_descripcion: JSON.stringify(respuesta),
        atran_accion: estado,
        atran_varchar1: JSON.stringify(params),
        atran_varchar2: url_ws,
        rst_id: $("#txtRestaurante").val(),
        IDUsersPos: $("#txtUserId").val(),
        atran_modulo: 'CUPONES MOVISTAR'
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        url: "config_facturacion.php",
        data: data_to_send,
        success: function (datos) {
            // IGNORED
        }
    });
}

function fn_SETQR_promociones_movistar(num_factura,QR_Data) {
    let data_to_send = { "SetQRPromocionesMovistar": 1 };
    data_to_send.cfac = num_factura;
    data_to_send.QRData = QR_Data;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        url: "config_facturacion.php",
        data: data_to_send,
        success: function (datos) {
            //IGNORED
        }
    });
}

function fn_datosTarjeta() {
    $('#datosClientePayphone').hide();
    $('#datosTarjetaClientePayphone').show();
}



// payphone Reverso automatico.
function payphoneReverseAutomaticoClientID() {

    var send = { "PayPhoneObtenerClaves": 1 };
    send.restaurante = $("#txtRestaurante").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        success: function (datos) {

            var send1 = { "payphoneReverseClientID": 1 };
            send1.clientIdTransaccion = $("#txtclientIdPaypone").val();
            send1.token = datos[0]["Token"];
            $.ajax({
                async: false,
                type: "POST",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_facturacion.php",
                data: send1,
                success: function (datos) {

                }
            });


        }
    });



}



function fn_PayPhoneObtenerClaves() {

    return new Promise(function (resolve, reject) {

        var send = { "PayPhoneObtenerClaves": 1 };
        send.restaurante = $("#txtRestaurante").val();

        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function (datos) {

                var codificacion;
                if (datos.str > 0) {

                    var datosTarjeta = {
                        "cardNumber": $("#cardNumberSinEnmascarar").val(),
                        "expirationMonth": $("#expirationMonth").val(),
                        "expirationYear": $("#expirationYear").val(),
                        "holderName": $("#holderName").val(),
                        "securityCode": $("#securityCode").val()
                    }

                    var key = CryptoJS.enc.Utf8.parse(datos[0]["ContraseniaCodificacion"]); // contraseña de codificacion
                    var iv = CryptoJS.enc.Utf8.parse('');
                    var encrypted = CryptoJS.AES.encrypt(JSON.stringify(datosTarjeta), key, { iv: iv });

                    codificacion = {
                        "estado": 200,
                        "token": datos[0]["Token"],
                        "data": encrypted.toString(),
                        "storeId": datos[0]["StoreId"]
                    }

                } else {
                    codificacion = {
                        "estado": 500,
                        "mensaje": "Error de conexion."
                    }
                }

                resolve(codificacion);
            }
            ,
            error: function (e) {
                resolve({
                    "estado": 500,
                    "mensaje": "Error de conexion."
                });
            }
        });

    })
}

function fn_PayPhoneObtenerDatosTransaccion(token, transactionId) {
    return new Promise(function (resolve, reject) {

        var send = { "PayPhoneObtenerDatosTransaccion": 1 };
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

                var respuesta;
                if (datos.statusCode === undefined) {
                    respuesta = {
                        "estado": 204,
                        "mensaje": datos.message
                    }
                } else {
                    respuesta = datos;
                    respuesta["estado"] = 200;
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




function fn_PayPhoneCreateTransaccion(token, json) {
    return new Promise(function (resolve, reject) {

        var send = { "PayPhoneEnviarTransaccion": 1 };
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
                if (datos.statusCode === undefined) {

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
                            "mensaje": (datos.message).toString() + "  [ERROR]"
                        }
                    }



                } else {
                    if (datos.statusCode === "3" || datos.statusCode === 3) {
                        respuesta = datos;
                        respuesta["estado"] = 200;
                    } else {
                        respuesta = {
                            "estado": 204,
                            "mensaje": datos.message
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

function fn_PayPhoneGuardarRespuestaAutorizacion(json, TipoTransaccion, respuesta = "OK", JsonData) {
    return new Promise(function (resolve, reject) {

        var send = {"PayPhoneGuardarRespuestaAutorizacion": 1};
        send.rsaut_trama = $("#cardNumberSinEnmascarar").val() + json["authorizationCode"] + "_#" + $("#txtclientIdPaypone").val(); // trama
        send.ttra_codigo = TipoTransaccion; //  TipoTransaccion CT
        send.cres_codigo = json["statusCode"]; // respuesta
        send.rsaut_respuesta = (json["transactionStatus"] === undefined ? "" : json["transactionStatus"]);
        send.rsaut_secuencial_transaccion = (json["transactionId"] === undefined ? "" : json["transactionId"]);
        send.rsaut_hora_autorizacion = json["date"];
        send.rsaut_fecha_autorizacion = json["date"];
        send.rsaut_numero_autorizacion = json["authorizationCode"].substring(0, 5);// max 6
        send.rsaut_terminal_id = ""; //json["xxx"];
        send.rsaut_grupo_tarjeta = (json["cardType"] === undefined ? "" : json["cardType"]);
        send.rsaut_red_adquiriente = (json["cardBrand"] === undefined ? "" : json["cardBrand"]);
        send.rsaut_merchant_id = "";// json["xxxx"]; // K004F000684966
        send.rsaut_numero_tarjeta = json["bin"] + "XXXX" + json["lastDigits"];
        send.rstaut_tarjetahabiente = (json["optionalParameter4"] === undefined ? "" : json["optionalParameter4"]);
        send.mlec_codigo = "";
        send.rsaut_identificacion_aplicacionemp = (json["storeName"] === undefined ? "" : json["storeName"]);
        send.rsaut_movimiento = $("#txtNumFactura").val();
        send.raut_observacion = (json["transactionStatus"] === undefined ? "" : json["transactionStatus"]);
        send.IDStatus = "E8C6D0A1-023F-2773-D846-1063F4AC58A6"; // TArjetaHabiente
        send.replica = 0; //"Kfc 004 Pp App Cci",
        send.nivel = 0;
        send.SWT_Respuesta_AutorizacionVarchar1 = "APROBADA";


        // Requerimiento
        send.rqaut_ip = $("#txt_est_ip").val();
        send.tpenv_id = 8;
        send.idUser = $("#idUser").val();
         
        send.SWT_Respuesta_AutorizacionVarchar2 = (JsonData !== "") ? JSON.stringify(JsonData) : "";
        
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function (datos) {

                resolve({
                    "estado": 200,
                    "mensaje": "OK ",
                    "transaccionID": datos[0].respuesta
                });


            }, error: function (e) {
                payphoneReverseAutomaticoClientID();
                resolve({
                    "estado": 500,
                    "mensaje": "Error al insertar la transacción en la BD, Pago NO realizado."
                });

            }
        });

    })
}
function fn_PayPhoneGuardarRespuestaAutorizacionError(mensaje, JsonData = "") {
    return new Promise(function (resolve, reject) {

        var send = {"PayPhoneGuardarRespuestaAutorizacion": 1};
        send.rsaut_trama = $("#cardNumberSinEnmascarar").val() + "_NO COBRO_#" + $("#txtclientIdPaypone").val(); // trama
        send.ttra_codigo = "CT"; //  TipoTransaccion CT
        send.cres_codigo = ""; // respuesta
        send.rsaut_respuesta = "-1";
        send.rsaut_secuencial_transaccion = "";
        send.rsaut_hora_autorizacion = "";
        send.rsaut_fecha_autorizacion = "";
        send.rsaut_numero_autorizacion = "-1";// max 6
        send.rsaut_terminal_id = ""; //json["xxx"];
        send.rsaut_grupo_tarjeta = "";
        send.rsaut_red_adquiriente = "";
        send.rsaut_merchant_id = "";// json["xxxx"]; // K004F000684966
        send.rsaut_numero_tarjeta = "";
        send.rstaut_tarjetahabiente = ""
        send.mlec_codigo = "";
        send.rsaut_identificacion_aplicacionemp = "";
        send.rsaut_movimiento = $("#txtNumFactura").val();
        send.raut_observacion = mensaje; // Correo
        send.IDStatus = "E8C6D0A1-023F-2773-D846-1063F4AC58A6"; // TArjetaHabiente
        send.replica = 0; //"Kfc 004 Pp App Cci",
        send.nivel = 0;
        send.SWT_Respuesta_AutorizacionVarchar1 = "NO COBRADO";
        send.SWT_Respuesta_AutorizacionVarchar2 = (JsonData !== "") ? JSON.stringify(JsonData) : "";
        // Requerimiento
        send.rqaut_ip = $("#txt_est_ip").val();
        send.tpenv_id = 8;
        send.idUser = $("#idUser").val();

        send.SWT_Respuesta_AutorizacionVarchar2 = (JsonData !== "") ? JSON.stringify(JsonData) : "";

        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function (datos) {
                resolve({
                    "estado": 200,
                    "mensaje": "OK",
                    "transaccionID": datos[0].respuesta
                });


            }, error: function (e) {
                payphoneReverseAutomaticoClientID();
                resolve({
                    "estado": 500,
                    "mensaje": "Error al insertar la transacción en la BD, Pago NO realizado."
                });

            }
        });

    })
}
function fn_PayPhoneGuardarRespuestaAutorizacionError(mensaje, JsonData = "") {
    return new Promise(function (resolve, reject) {

        var send = {"PayPhoneGuardarRespuestaAutorizacion": 1};
        send.rsaut_trama = $("#cardNumberSinEnmascarar").val() + "_NO COBRO_#" + $("#txtclientIdPaypone").val(); // trama
        send.ttra_codigo = "CT"; //  TipoTransaccion CT
        send.cres_codigo = ""; // respuesta
        send.rsaut_respuesta = "-1";
        send.rsaut_secuencial_transaccion = "";
        send.rsaut_hora_autorizacion = "";
        send.rsaut_fecha_autorizacion = "";
        send.rsaut_numero_autorizacion = "-1";// max 6
        send.rsaut_terminal_id = ""; //json["xxx"];
        send.rsaut_grupo_tarjeta = "";
        send.rsaut_red_adquiriente = "";
        send.rsaut_merchant_id = "";// json["xxxx"]; // K004F000684966
        send.rsaut_numero_tarjeta = "";
        send.rstaut_tarjetahabiente = ""
        send.mlec_codigo = "";
        send.rsaut_identificacion_aplicacionemp = "";
        send.rsaut_movimiento = $("#txtNumFactura").val();
        send.raut_observacion = mensaje; // Correo
        send.IDStatus = "E8C6D0A1-023F-2773-D846-1063F4AC58A6"; // TArjetaHabiente
        send.replica = 0; //"Kfc 004 Pp App Cci",
        send.nivel = 0;
        send.SWT_Respuesta_AutorizacionVarchar1 = "NO COBRADO";

        // Requerimiento
        send.rqaut_ip = $("#txt_est_ip").val();
        send.tpenv_id = 8;
        send.idUser = $("#idUser").val();

        send.SWT_Respuesta_AutorizacionVarchar2 = (JsonData !== "") ? JSON.stringify(JsonData) : "";
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function (datos) {
                resolve({
                    "estado": 200,
                    "mensaje": "Error en Insercion de base ",
                    "transaccionID": datos[0].respuesta
                });


            }, error: function (e) {
                payphoneReverseAutomaticoClientID();
                resolve({
                    "estado": 500,
                    "mensaje": "Error al insertar la transacción en la BD, Pago NO realizado."
                });

            }
        });

    })
}


function validarCampos(idInput) {
    if ($("#" + idInput).val() === "") {
        var txt = $("#" + idInput);
        alertify.error("Complete el campo " + txt.attr("desc"));
        txt.focus();
        txt.click();
        return false;
    } else {
        return true;
    }
}

async function enviarPago() {
    // Controlo varias touch a botones de pago.

    $("#txtclientIdPaypone").val($("#txtNumFactura").val() + "_a_" + generarAleatorio());

    if (!validarCampos('cardNumber'))
        return
    if (!validarCampos('expirationMonth'))
        return
    if (!validarCampos('expirationYear'))
        return
    if (!validarCampos('securityCode'))
        return
    if (!validarCampos('holderName'))
        return

    activarDesactivarBtnPagos("btnPagar", "btnCancelarPay", false);

    $("#procesandoPagoDirecto").show();

    // Codifica los datos de la tarjeta para enviar en el JSON a payphone.
    var codificacion = await fn_PayPhoneObtenerClaves();
    if (codificacion.estado !== 200) {
        alertify.error(codificacion.mensaje);
        $("#procesandoPagoDirecto").hide();
        activarDesactivarBtnPagos("btnPagar", "btnCancelarPay", true);
        return
    }



    // JSON para crear una transaccion de pago en PayPhone.
    var dataTransactionCreateTemp = {
        "data": codificacion["data"],
        "phoneNumber": $("pay_txtTelefono").val(),
        "email": $("#pay_txtCorreo").val(), // "desarrollos16@hotmail.com",
        "documentId": $("#pay_txtCedulaCliente").val(), // "1206395863",
        "clientTransactionId": $("#txtclientIdPaypone").val(), // "MCpruebasMaxpoint1",
        "storeId": codificacion["storeId"],

    }

    // AÑadir los valores de la factura al JSON, con iva0% o 12%
    var dataTransactionCreate = AdjuntarValoresTransaccionParaCrearTransaccion(dataTransactionCreateTemp);
    // Enviar Transaccion a Payphone para su aprobacion
    var transaccionCreada = await fn_PayPhoneCreateTransaccion(codificacion["token"], dataTransactionCreate);
    if (transaccionCreada.estado !== 200) {
        fn_PayPhoneGuardarRespuestaAutorizacionError(transaccionCreada.mensaje, dataTransactionCreate);
        $("#procesandoPagoDirecto").hide();
        activarDesactivarBtnPagos("btnPagar", "btnCancelarPay", true);
        // Si falla el envio, tratar de reversar la transaccion.

        Swal.fire({
            title: 'Ocurrio un Problema.',
            text: transaccionCreada.mensaje,
            icon: 'warning',
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

        return;
    }


    // Obtener los datos completos de la transaccion Realizada.
    var datosCompletosTransaccion = await fn_PayPhoneObtenerDatosTransaccion(codificacion["token"], transaccionCreada["transactionId"]);
    if (datosCompletosTransaccion.estado !== 200) {
        fn_PayPhoneGuardarRespuestaAutorizacionError(datosCompletosTransaccion.mensaje, dataTransactionCreate);
        alertify.error(datosCompletosTransaccion.mensaje);
        $("#procesandoPagoDirecto").hide();
        activarDesactivarBtnPagos("btnPagar", "btnCancelarPay", true);
        return
    }

    // Insertrar Resultados en la tabla requerimiento Autorizacion.
    var resultadoRequerimientoAutorizacion = await fn_PayPhoneGuardarRespuestaAutorizacion(datosCompletosTransaccion, "CT", "OK", dataTransactionCreate);

    if (resultadoRequerimientoAutorizacion.estado !== 200) {

        fn_PayPhoneGuardarRespuestaAutorizacionError(resultadoRequerimientoAutorizacion.mensaje, dataTransactionCreate);
        activarDesactivarBtnPagos("btnPagar", "btnCancelarPay", true);
        $("#procesandoPagoDirecto").hide();
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
            } else {
                alertify.success("Intentando reverso.");
                payphoneReverseAutomaticoClientID();
            }
        })
        return
    }

    $("#procesandoPagoDirecto").hide(10);
    $("#modalPay").hide();
    cerrarTeclados();
    activarDesactivarBtnPagos(true);
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
            fn_payphoneGuadarDatosRespuesta(resultadoRequerimientoAutorizacion.transaccionID, 8);
        }
    })



}
function activarDesactivarBtnPagos(enable) {

    if (enable) {
        $('#btnPagar').css("background-color", "rgb(246, 146, 31)"); // Orange
        $('#btnPagar').attr("disabled", false);

        $('#btnCancelarPay').css("background-color", "rgb(246, 146, 31)"); // Orange
        $('#btnCancelarPay').attr("disabled", false);


    } else {
        $('#btnPagar').css("background-color", "rgb(142, 137, 131)");
        $('#btnPagar').attr("disabled", true);

        $('#btnCancelarPay').css("background-color", "rgb(142, 137, 131)");
        $('#btnCancelarPay').attr("disabled", true);

    }

}
function pay_buscarCliente() {
    $("#pay_datosCliente").show(200);

    if ($("#paybtn1").text() === "Siguiente") {

        if (!validarCampos('pay_txtCedulaCliente'))
            return
        if (!validarCampos('pay_txtTelefono'))
            return
        if (!validarCampos('pay_txtCorreo'))
            return


        $("#modalRegistroCliente").hide();
        $("#modalPay").show(200);
    }
    $("#paybtn1").text('Siguiente');

}

function cerrarTeclados() {

    $("#modalRegistroCliente").hide(200);
    $("#modalPay").hide(200);
    $("#pay_TeladoNombres").empty();
    $("#pay_TeladoNombres").hide();
    $("#dominio3").hide();
    $("#dominio4").hide();
    $("#pay_TeladocedulaCliente").hide();
    $('#pay_datosCliente').hide();
    $('#paybtn1').hide();
    $("#modalTransaccionApp").hide();
}
function pay_cancelarBusquedaRegistroCliente() {
    $("#modalRegistroCliente").hide(200);
    $("#modalPay").hide(200);
    $("#txtDocumentoClientePaypone").val("");


    $("#pay_TeladoNombres").empty();
    $("#pay_TeladoNombres").hide();
    $("#dominio3").hide();
    $("#dominio4").hide();
    $("#pay_TeladocedulaCliente").hide();


    $('#cardNumber').val("");
    $('#cardNumberSinEnmascarar').val("");

    $('#expirationMonth').val("");
    $('#expirationYear').val("");
    $('#securityCode').val("");
    $('#holderName').val("");

    $('#pay_txtCedulaCliente').val("");
    $('#pay_txtNombres').val("");
    $('#pay_txtDireccion').val("");
    $('#pay_txtTelefono').val("");
    $('#pay_txtCorreo').val("");

    $('#pay_datosCliente').hide();

    $('#paybtn1').hide();

}

function pay_cancelarPago() {
    $("#modalPay").hide(200);
}

function AdjuntarValoresTransaccionParaCrearTransaccion(jsonCrearT) {

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
                            jsonCrearTransaccion["AmountWithoutTax"] = Math.round((json[clave]["valor"] * 100));
                        } else {
                            jsonCrearTransaccion["AmountWithoutTax"] = parseInt(json[clave]["valor"] * 100);
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
                            jsonCrearTransaccion["Tax"] = Math.round((json[clave]["valor"] * 100));
                        } else {
                            jsonCrearTransaccion["Tax"] = parseInt(json[clave]["valor"] * 100);
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
                            jsonCrearTransaccion["Service"] = Math.round((json[clave]["valor"] * 100));
                        } else {
                            jsonCrearTransaccion["Service"] = parseInt(json[clave]["valor"] * 100);
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


function fn_consultarMediosPagoPayphoneDisponible() {
    return new Promise(function (resolve, reject) {
        var send = {"consultarMediosPagoPayphoneDisponible": 1};
        send.rst_id = $("#txtRestaurante").val();
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function (datos) {
                resolve(
                        datos
                        );
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


function fn_payphoneTransaccionDirecta() {
    localStorage.removeItem("ls_hayCliente");
    if (estaActivoPayPhoneFormularioEnOrdenPedido()) {

        $("#cardNumberSinEnmascarar").val("");
        $("#modalRegistroCliente").hide();
        $("#modalPay").show();
        $("#metodoDirecto").css("margin-bottom", "15px");

        // Campo para que busque automatico el cliente.
        $("#txtDocumentoClientePaypone").val(localStorage.getItem("ls_documento"+cuenta));

        $("#pay_txtCedulaCliente").val(localStorage.getItem("ls_documento"+cuenta));
        $("#pay_txtNombres").val(localStorage.getItem("ls_nombres"+cuenta));
        $("#pay_txtDireccion").val(localStorage.getItem("ls_direccion"+cuenta));
        $("#pay_txtTelefono").val(localStorage.getItem("ls_telefono"+cuenta));
        $("#pay_txtCorreo").val(localStorage.getItem("ls_correo"+cuenta));


    } else {


        $("#cardNumberSinEnmascarar").val("");
        $("#modalRegistroCliente").show();
        eliminarLocalStorage();
        fn_numericoFDZN('#pay_txtCedulaCliente');

    }
}



function fn_PayPhoneObtenerClavesGeneral() {

    return new Promise(function (resolve, reject) {

        var send = {"PayPhoneObtenerClaves": 1};
        send.restaurante = $("#txtRestaurante").val();

        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function (datos) {
                var codificacion;
                if (datos.str > 0) {
                    codificacion = {
                        "estado": 200,
                        "datos": datos[0]
                    }
                } else {
                    codificacion = {
                        "estado": 500,
                        "mensaje": "Error de conexion."
                    }
                }
                resolve(codificacion);
            }
            ,
            error: function (e) {
                resolve({
                    "estado": 500,
                    "mensaje": "Error de conexion."
                });
            }
        });

    })
}


function activarDesactivarBtnPagos(idPagar, idCancelar, enable) {
    if (enable) {
        $('#' + idPagar).css("background-color", "rgb(246, 146, 31)"); // Orange
        $('#' + idPagar).attr("disabled", false);

        $('#' + idCancelar).css("background-color", "rgb(246, 146, 31)"); // Orange
        $('#' + idCancelar).attr("disabled", false);
    } else {
        $('#' + idPagar).css("background-color", "rgb(142, 137, 131)");
        $('#' + idPagar).attr("disabled", true);

        $('#' + idCancelar).css("background-color", "rgb(142, 137, 131)");
        $('#' + idCancelar).attr("disabled", true);
    }
}

//Inicio de Proceso Pago Payvalida

var apiConfigPayvalida = {
    async: true,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_payvalida.php",
    data: null
};


function fn_aplicarPagoPayvalida() {

    var html = '';

    html += `
            <div class="cargando-payvalida"   >
                <img id="div_loader_payvalida" name= "div_loader_payvalida" src="../imagenes/procesando.gif" height="100"/>
                <div id="div_informacion_payvlida"></div>
            </div>
            <div id="div_payvalida_1">
                <label id="lbl_tarjetaNumeroSaldos" for="tarjetaNumeroSaldos">N. Tarjeta</label>
            </div>
            <div id="div_payvalida_2">
                <input type="number" id="tarjetaNumeroSaldos" name="tarjetaNumeroSaldos" maxlength="40" onfocus="fn_mostrarTecladoPayValida()"> 
            </div>
            <br><br>
            <div align="center" id="div_payvalida_3">
                <button id="btn_consultar_payvalida" type="button" class="btn btn-outline-success boton-payvalida" onclick="fn_cargarLoaderPayvalida(); fn_consultaSaldoPayValida()">
                    Consultar Saldo
                </button>
                <button id="btn_cobrar_payvalida" type="button" class="btn btn-outline-success boton-payvalida" onclick="fn_cargarLoaderPayvalida(); fn_ejecutarCobroPayValida()">
                    Ejecutar Cobro
                </button>
                <button id="btn_ok_payvalida" type="button" class="btn btn-outline-success boton-payvalida" onclick="fn_cargarLoaderPayvalida(); fn_cancelarPagoPayvalida()">
                    OK
                </button>
             <div>
    `;

    $('#opcionesPayvalida').empty();
    $('#opcionesPayvalida').append(html);
    $("#modalSeleccionOpcionPayvalida").show();
    $("#div_loader_payvalida").hide();
    $("#btn_ok_payvalida").hide();

}

function fn_cargarLoaderPayvalida(){
    $("#div_loader_payvalida").show();
}


function fn_mostrarTecladoPayValida(){
    fn_alfaNumerico("#tarjetaNumeroSaldos");
    $("#keyboard").show();
    $("#btn_cancelar_teclado").attr("onclick","cerrarTeclado()");
    $("#btn_ok_teclado").attr("onclick","cerrarTeclado()");
}

function ocultarDivPayvalida(){
    $("#div_payvalida_1").hide();
    $("#div_payvalida_2").hide();
    $("#div_payvalida_3").hide();
}

function mostrarDivPayvalida(){    
    $("#div_payvalida_1").show();
    $("#div_payvalida_2").show();
    $("#div_payvalida_3").show();
}

function fn_consultaSaldoPayValida(){
    $("#keyboard").hide();
    $html = '';
    $('#div_informacion_payvlida').append($html);
    const numeroTarjeta = $('#tarjetaNumeroSaldos').val();
    ocultarDivPayvalida();
    if(numeroTarjeta && numeroTarjeta.length > 10){

        send = {};
        send.metodo = 'consultaSaldo';
        send.numeroTarjeta = numeroTarjeta;
        apiConfigPayvalida.data = send;
        $.ajax({
            ...apiConfigPayvalida,
            success: function(datos) {

                mostrarDivPayvalida();            

                $('#div_informacion_payvlida').empty();
                if(datos && datos.CODE && datos.CODE == '0000' && datos.DATA){
                    $html = `<label>Su saldo son ${Number(datos.DATA.balance).toFixed(2)}  USD</label>`;
                    $('#div_informacion_payvlida').append($html);
                    $("#btn_ok_payvalida").show();
                    $("#div_loader_payvalida").hide();
                }else if(datos && datos.CODE && datos.CODE != '0000'){
                    $html = `<label> No se ha podido efectuar la consulta. ${datos.DESC} </label>`;
                    $('#div_informacion_payvlida').append($html);
                    $("#btn_ok_payvalida").show();
                    $("#div_loader_payvalida").hide();
                }else if(datos && datos.status && datos.status == 'ERROR'){
                    $html = `<label> ${datos.message} </label>`;
                    $('#div_informacion_payvlida').append($html);
                    $("#div_loader_payvalida").hide();
                }

            },
           error(e1,e2,e3){
            mostrarDivPayvalida();            
            $("#div_loader_payvalida").hide();
            alertify.alert("No se ha podido realizar la consulta de saldo solicitada");
            }
        });
    }else{
        mostrarDivPayvalida();            
        $("#div_loader_payvalida").hide();
        alertify.alert("Ingrese un n\u00FAmero de tarjeta v\u00E1lido");
        $("#btn_ok_payvalida").show();            
    }

}

function fn_ejecutarCobroPayValida(){
    $html = '';
    $('#div_informacion_payvlida').append($html);
    $("#keyboard").hide();
    ocultarDivPayvalida();
    $("#div_loader_payvalida").show();

    const numeroTarjeta = $('#tarjetaNumeroSaldos').val();


    if(numeroTarjeta && numeroTarjeta.length > 10){
        send = {};
        send.metodo = 'cobrar';
        send.numeroTarjeta = numeroTarjeta;
        send.monto = $("#pagado").val();
        send.idFactura = $("#txtNumFactura").val();
        apiConfigPayvalida.data = send;
        $.ajax({
            ...apiConfigPayvalida,
            success: function(datos) {

                console.log('EJECUCION DE COBRO');
                console.log(datos);

                mostrarDivPayvalida();     
                

                var aPagar = parseFloat($("#pagoTotal").val());
                    var pagado = $("#pagado").val();
                    pagado = parseFloat($("#pagado").val());
                    var valor = aPagar - pagado;

                $('#div_informacion_payvlida').empty();
                if(datos && datos.CODE && datos.CODE == '0000' && datos.DATA){
                    $html = `
                            <div><label class="texto-exitoso-payvalida">${datos.DESC}<label></div>
                            <div><label>Su saldo restante es ${Number(datos.DATA.balance).toFixed(2)} USD</label></div>
                            `;
                    $('#div_informacion_payvlida').append($html);
                    $('#tarjetaNumeroSaldos').hide();
                    $("#div_loader_payvalida").hide();
                    $("#btn_cobrar_payvalida").hide();
                    $("#btn_consultar_payvalida").hide();
                    $("#lbl_tarjetaNumeroSaldos").hide();
                    $("#btn_ok_payvalida").show();

                    
                    valor = parseFloat(valor);

                    if (valor <= 0 ) { 
                        $("#td_cambio").show();
                        $("#td_falta").hide();
                        var valorCambio = (valor * -1).toFixed(2);
                        tfp_wst = 10;
                        fn_insertarFormaPago();
                        $("#valorCambio").val(valorCambio);                                                 
                    } else {
                        tfp_wst = 10;
                        fn_insertarFormaPago();
                        tfp_wst = -1;
                        $("#pagado").val("");
                        $("#pagoTotal").val(valor.toFixed(2));
                    }
                

                }else if(datos && datos.CODE && datos.CODE != '0000'){
                    $html = `<label> No se ha podido efectuar el cobro. ${datos.DESC} </label>`;
                    $('#div_informacion_payvlida').append($html);
                    $("#div_loader_payvalida").hide();
                    $("#btn_ok_payvalida").show();
                }else if(datos && datos.status && datos.status == 'ERROR'){
                    $html = `<label> ${datos.message} </label>`;
                    $('#div_informacion_payvlida').append($html);
                    $("#div_loader_payvalida").hide();
                    $("#btn_ok_payvalida").show();
                }

            },
            error(e1,e2,e3){
                alertify.alert("No se ha podido ejecutar el cobro");        
                $("#div_loader_payvalida").hide();
                mostrarDivPayvalida();            
            }
        });

    }else{
        $("#div_loader_payvalida").hide();
        alertify.alert("Ingrese un n\u00FAmero de tarjeta v\u00E1lido");
        mostrarDivPayvalida();
        $("#btn_ok_payvalida").show();            
    }


}

function fn_cancelarPagoPayvalida(){
    $("#keyboard").hide();
    $("#modalSeleccionOpcionPayvalida").hide();

    var aPagar = parseFloat($("#pagoTotal").val());
    var pagado = $("#pagado").val();
    pagado = parseFloat($("#pagado").val());
    var valor = aPagar - pagado;


    if (valor <= 0 ) { 
        fn_envioFactura();           
    }

}

function fn_anularPagoPayvalida(){

    send = {};
    send.metodo = 'anular';
    send.idFactura = $("#txtNumFactura").val();
    send.idFormaPago = $("#btnFormaPagoId").val()
    apiConfigPayvalida.data = send;

    $.ajax({
        ...apiConfigPayvalida,
        success: function(datos) {
            if(datos && datos.CODE && datos.CODE == '0000' && datos.DATA){

                send = { anula_formaPagoPayvalida: 1 };
                send.anu_codFact = $("#txtNumFactura").val();
                send.anu_idPago = $("#btnFormaPagoId").val();
                $.getJSON("config_facturacion.php", send, function (datos) {
                    fn_obtieneTotalApagar();
                    $("#pagado").val("");
                    fn_resumenFormaPago();
                    $("#td_cambio").hide();
                    $("#td_falta").show();
                    alertify.alert("Forma de Pago Anulada Correctamente !");
                });


            }else if(datos && datos.CODE && datos.CODE != '0000'){
                alertify.alert("NO se a podido ANULAR el cobro por "+datos.DESC);
            }else{
                alertify.alert("NO se a podido ANULAR el cobro ");
            }
        },
        error(e1,e2,e3){
            alertify.alert("No se ha podido ANULAR el cobro ");        
        }
    });
}

//Fin de Proceso Pago Payvalida


function cargandoDeUnaModal(isShown) {
    if (isShown === 1) {
        $("#loadingDeUna").show();
        $("#myModalDeuna").show();
    } else if (isShown === 0) {
        $("#myModalDeuna").hide();
        $("#loadingDeUna").hide();
    }
}

function cargandoDeUnaModalIntentos(isShown, callback) {
    console.log("ENTRO A CARGANDO MODAL REINTENTOS");
    if (isShown == 1) {
        console.log("DENTRO A MOSTRAR EL MODAL");
        $("#loadingDeUnaIntentos").show(0, ()=>{
            $("#myModalDeunaIntentos").show(0, callback);
        });
    } else if (isShown == 0) {
        $("#loadingDeUnaIntentos").hide(0, () =>{
            $("#myModalDeunaIntentos").hide(0, callback);
        });
    }
}



function cerrarModalDeUna(isShown) {
    $("#cancelarModalDeUna").prop("disabled", true);
    $("#esperarModalDeUna").prop("disabled", true);
    let requestId = $("#RequestIdDeUna").val();
    if (requestId !== "" && requestId !== null && requestId !== undefined && requestId !== "0") {
        existePagoDeunaDesdeCancelacion(requestId, isShown);
    }
}

function confirmacionParaCerrarElModal() {
    cargandoDeUnaModal(0);
    $("#loadingDeUna").show();
    $("#myModalDeunaConfirmarCancelacion").show();
    $("#cancelarModalDeUna").prop("disabled", false);
    $("#esperarModalDeUna").prop("disabled", false);
}

function reanudarTransaccionDeUna() {
    cargandoDeUnaModal(1);
    let requestId = $("#RequestIdDeUna").val();
    if (requestId !== "" && requestId !== null && requestId !== undefined && requestId !== "0") {
        existePagoDeunaDesdeReanudarPago(requestId);
    }
    $("#myModalDeunaConfirmarCancelacion").hide();
}

function estadoModalDeUna() {
    let estado = $("#myModalDeuna").css('display');
    if (estado === "block") {
        return 1
    } else if (estado === 'none') {
        return 0
    } else {
        return 0
    }
}

//payment Request Cancel
function fn_desmontarPagoDeuna() {

    //alert("Entro a DeUna");
    let cdn_id = $("#txtCadenaId").val();
    //**aqui revisar el usr_id */
    let usr_id = $("#txtUserId").val();
    let rst_id = $("#hide_rst_id").val();
    let IDEstacion = $("#IDEstacionDeUna").val();
    let valor = parseFloat($("#pagoGranTotal").val());
    let odp_id = $("#txtOrdenPedidoId").val();
    var send = { formaPagoDeUna: 1, valor: valor, cdn_id: cdn_id, usr_id: usr_id, rst_id: rst_id, odp_id, IDEstacion: IDEstacion };
    var es_menu_agregador = localStorage.getItem("es_menu_agregador");

    send.es_menu_agregador = es_menu_agregador;
    //cargando(1);
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        url: "../DeUna/solicitudAnulacionPaymentRequest.php",
        data: send,
        beforeSend: function () {
            //cargandoDeUnaModal(1);
        },
        success: function (datos) {
            console.log("exito en consulta");
            console.log(datos);
            if (datos.status == 200) {
                if(localStorage.getItem('esPinCode')){
                    alertify.success("Pago Deuna desmontado satisfactoriamente");
                    $("#pagado").val("");
                    Swal.fire({
                        title: 'Transacción cancelada',
                        text: "La transacción de DeUna ha excedido el limite establecido, por favor vuelve a intentar un nuevo pago.",
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.value) {
                            console.log(result);
                        }
                    })
                } else {
                    //cargandoDeUnaModal(0);
                    alertify.success("Pago Deuna desmontado satisfactoriamente");
                    $("#pagado").val("");
                    Swal.fire({
                        title: 'Solicitud de Pago Anulada',
                        text: "La solicitud de pago ha sido anulada correctamente.",
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.value) {
                            console.log(result);
                        }
                    })
                }
            } else {
                //cargandoDeUnaModal(0);
                Swal.fire({
                    title: 'Error al Cancelar el Pago.',
                    text: "La solicitud de pago no se pudo cancelar. Contactarse con Soporte",
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.value) {
                        console.log(result);
                    }
                })
            }
            //cargando(0);  
        },
        error: function (e) {
            //cargandoDeUnaModal(0);
            console.log("error");
            console.log(e);
        }
    });

}

function fn_aplicarPagoDeUna() {
    //alert("Entro a DeUna");
    console.log("ENTRO DE UNA");
    let cdn_id = $("#txtCadenaId").val();
    //**aqui revisar el usr_id */
    let usr_id = $("#txtUserId").val();
    let rst_id = $("#hide_rst_id").val();
    let IDEstacion = $("#IDEstacionDeUna").val();
    //let valor = parseFloat($("#pagoTotal").val());
    let pagado = parseFloat($("#pagado").val());
    ValorPagadoConDeUna = 0;
    ValorPagadoConDeUna = pagado;
    let odp_id = $("#txtOrdenPedidoId").val();

    let client_id = localStorage.getItem('ls_documento' + cuenta);
    let client_name = localStorage.getItem('ls_nombres' + cuenta);
    let client_email = localStorage.getItem('ls_correo' + cuenta);

    if (client_id == null || client_id.trim() == "") {
        client_id = undefined;
    }
    if (client_name == null || client_name.trim() == "") {
        client_name = undefined;
    }

    if (client_email == null || client_email.trim() == "") {
        client_email = undefined;
    }

    let client = undefined

    if (client_id && client_name) {
        client = {
            name: client_name,
            documentId: client_id,
            email: client_email
        }
    }

    var send = { formaPagoDeUna: 1, valor: pagado, cdn_id: cdn_id, usr_id: usr_id, rst_id: rst_id, odp_id, IDEstacion: IDEstacion, client };
    var es_menu_agregador = localStorage.getItem("es_menu_agregador");
    var requestIdDeUna = "";
    send.es_menu_agregador = es_menu_agregador;

    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        url: "../DeUna/solicitudDeUna.php",
        data: send,
        beforeSend: function () {
            //cargando(0);
        },
        success: function (datos) {

            console.log("exito en consulta");
            console.log(datos);
            if (datos.status !== null && datos.status !== undefined && datos.status !== "") {
                if (datos.status == 200) {
                    if (datos.requestId != null && datos.requestId != undefined && datos.requestId != "") {
                        requestIdDeUna = datos.requestId;
                        $("#RequestIdDeUna").val(requestIdDeUna);
                        $("#IntervaloConsultaEstadoPagoDeUna").val(datos.intervalo ? parseInt(datos.intervalo) * 1000 : 3000);
                        $("#tiempoEsperaDePagoEnSegundos").val(datos.tiempoEsperaDePagoEnSegundos ? parseInt(datos.tiempoEsperaDePagoEnSegundos) * 1000 : 120000);
                        localStorage.removeItem('tiempoEsperaDePagoEnSegundos');
                        localStorage.setItem('tiempoEsperaDePagoEnSegundos', datos.tiempoEsperaDePagoEnSegundos);
                        localStorage.removeItem('tieneSubsidioDeUna');
                        localStorage.setItem('tieneSubsidioDeUna', datos.tieneSubsidio);
                        fn_obtenerTransactionIdDeUna(datos.requestId);
                        //return;
                    }
                } else {
                    $("#IntervaloConsultaEstadoPagoDeUna").val(datos.intervalo ? parseInt(datos.intervalo) * 1000 : 3000);
                    cargandoDeUnaModalIntentos(0, () => {
                        alertify.error("No existe conexion al Servicio de Deuna")
                        Swal.fire({
                            title: 'Error de Conexión.',
                            text: datos.error ? datos.error : "No existe conexion al Servicio de Deuna",
                            icon: 'error',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.value) {
                                console.log(result);
                            }
                        })
                    });
                }
            } else {
                cargandoDeUnaModalIntentos(0, () => {
                    alertify.error("No existe conexion al Servicio de Deuna")
                    Swal.fire({
                        title: 'Error de Conexión.',
                        text: "No existe conexion al Servicio de Deuna",
                        icon: 'error',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.value) {
                            console.log(result);
                        }
                    })
                });
            }
        },
        error: function (e) {
            cargandoDeUnaModalIntentos(0, () => {
                Swal.fire({
                    title: 'Error de Conexión.',
                    text: "No existe conexion al Servicio de Deuna",
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.value) {
                        console.log(result);
                    }
                })
            });
            //cargandoDeUnaModal(0);
            console.log("error");
            console.log(e);
        }
    });


}

function validarPinCodeDeUna(pinCode) {
    if (pinCode == null || pinCode == undefined || pinCode == "") {
        $("#msgPagoDeUnaConQR").show();
        $("#msgPagoDeUnaConPinCode").hide();
        localStorage.setItem('esPinCode', false);
        IniciarCuentaRegresivaDeUna();
    } else {
        $("#msgPagoDeUnaConQR").hide();
        $("#msgPagoDeUnaConPinCode").show();
        // $("#pinCodeDeUna").html(pinCode);
        $("#PinCodeDeUnaValue").val(pinCode);
        localStorage.setItem('esPinCode', true);
        IniciarCuentaRegresivaDeUna();
        let formateado = "";
        for (let i = 0; i < pinCode.length; i++) {
            formateado += `<div style="
            width: 50px; 
            height: 50px; 
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid black;
            border-radius: 8px;
            font-size: 25px;
            font-weight: bold;
            font-family: Arial, sans-serif;
            padding: 5px 0;
        ">${pinCode[i]}</div>`;

            if ((i + 1) % 2 === 0 && i + 1 < pinCode.length) {
                formateado += `<div style="
                font-size: 25px;
                font-weight: bold;
                font-family: Arial, sans-serif;
            ">-</div>`;
            }
        }
        document.getElementById("pinCodeDeUna").innerHTML = formateado
    }
}
//Variables para el loader del pago de DeUna
let timerDeUna = undefined;
let tiempoRestanteDeUna = undefined;

function IniciarCuentaRegresivaDeUna() {
    document.getElementById('mensajeExpDeUna').textContent = "La transacción se cancelará automáticamente si el pago no se recibe en el tiempo establecido";
    console.log('iniciando cuenta regresiva deuna');
    // let tiempoTotalDeUna = $("#tiempoEsperaDePagoEnSegundos").val() / 1000;
    let tiempoTotalDeUna = localStorage.getItem('tiempoEsperaDePagoEnSegundos');
    console.log(tiempoTotalDeUna);
    updateLoaderDeUna(tiempoTotalDeUna);
    document.getElementById("tiempoTotalDeUna").textContent = tiempoTotalDeUna;
    timerDeUna = setInterval(() => {
        updateLoaderDeUna(tiempoTotalDeUna);
        if (tiempoRestanteDeUna <= 0) {
            clearInterval(timerDeUna);
            localStorage.removeItem('tiempoEsperaDePagoEnSegundos');
            document.getElementById('mensajeExpDeUna').textContent = "La solicitud de pago ha expirado";
            console.log('desmontando pago de una');
            existePagoDeunaDesdeCancelacion($("#RequestIdDeUna").val(), 0)
        }
    }, 1000);
}
function updateLoaderDeUna(tiempoTotalDeUna) {
    console.log('actualizando cuenta regresiva deuna');
    let tiempoInicialGuardado = parseInt(localStorage.getItem("tiempoInicialDeUna"), 10);
    let tiempoActual = Date.now();
    let diferenciaSegundos = Math.floor((tiempoActual - tiempoInicialGuardado) / 1000);

    tiempoRestanteDeUna = Math.max(tiempoTotalDeUna - diferenciaSegundos, 0);
    document.getElementById("cuenta_regresiva_deuna").textContent = (tiempoRestanteDeUna - tiempoTotalDeUna) * -1;
    let progress = (tiempoRestanteDeUna / tiempoTotalDeUna) * 283;
    document.querySelector(".progress-ring-deuna").style.strokeDashoffset = progress;
}

function fn_obtenerTransactionIdDeUna(requestId, esRefrescado = false) {

    //alert("Entro a DeUna");
    let cdn_id = $("#txtCadenaId").val();
    //**aqui revisar el usr_id */
    let usr_id = $("#txtUserId").val();
    let rst_id = $("#hide_rst_id").val();
    let IDEstacion = $("#IDEstacionDeUna").val();
    let valor = parseFloat($("#pagoGranTotal").val());
    let odp_id = $("#txtOrdenPedidoId").val();
    var send = { formaPagoDeUna: 1, valor: valor, cdn_id: cdn_id, usr_id: usr_id, rst_id: rst_id, odp_id, IDEstacion: IDEstacion, requestId: requestId };
    var es_menu_agregador = localStorage.getItem("es_menu_agregador");
    var requestIdDeUna = "";
    send.es_menu_agregador = es_menu_agregador;
    //cargando(1);
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        url: "../DeUna/solicitudTransactionId.php",
        data: send,
        beforeSend: function () {
            cargandoDeUnaModalIntentos(1, () => {

            })
        },
        success: function (datos) {
            console.log("exito en consulta");
            console.log(datos);

            if (datos.status != null && datos.status != undefined && datos.status != "") {
                if (datos.status == 200) {
                    //cargando(0);
                    cargandoDeUnaModalIntentos(0, () => {
                        cargandoDeUnaModal(1);
                        existePagoDeuna(requestId);
                    });
                    if(!esRefrescado){
                        let tiempoInicial = Date.now();
                        localStorage.setItem('tiempoInicialDeUna', tiempoInicial);
                    }
                    validarPinCodeDeUna(datos.pinCode);
                    //return;
                }
                else if (datos.status == 409) {
                    cargandoDeUnaModalIntentos(0, () => {
                        alertify.error(datos.error)
                        Swal.fire({
                            title: 'Error en la Solicitud de Pago.',
                            text: datos.error,
                            icon: 'error',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.value) {
                                console.log(result);
                            }
                        })
                    });

                }
                else {
                    cargandoDeUnaModalIntentos(0, () => {
                        alertify.error(datos.error)
                        Swal.fire({
                            title: 'Error en la Solicitud de Pago.',
                            text: datos.error,
                            icon: 'error',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.value) {
                                console.log(result);
                            }
                        })
                    });

                }
            }

        },
        error: function (e) {
            console.log("error");
            console.log(e);
        }
    });
}

function existePagoDeuna(requestId) {
    if (requestId == null || requestId == undefined || requestId == "") {
        console.log("No existe requestId");
        return;
    }
    let odp_id = $("#txtOrdenPedidoId").val();

    let usr_id = $("#txtUserId").val();
    let rst_id = $("#hide_rst_id").val();
    let IDEstacion = $("#IDEstacionDeUna").val();
    let cdn_id = $("#txtCadenaId").val();
    let intervalo = parseInt($("#IntervaloConsultaEstadoPagoDeUna").val());
    //console.log("Intervalo: " + intervalo);
    let send = { odp_id, consultarPago: 1, usr_id, rst_id, IDEstacion, cdn_id, requestId: requestId };
    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        url: "../DeUna/estadoPagoDeUna.php",
        data: send,
        beforeSend: function () {
        },
        success: function (datos) {
            console.log("exito en consulta cfac_id");
            console.log(datos);
            if (datos.existePago == true) {
                $("#btnAplicarPago").attr("disabled", "disabled");
                let estadoDeUna = estadoModalDeUna();
                if (estadoDeUna === 1) {
                    cargandoDeUnaModal(0);
                    fn_insertarFormaPago();
                    clearInterval(timerDeUna);
                    fn_buscaPagoCredito();
                    let pagoGranTotal = parseFloat($("#pagoGranTotal").val());
                    let falta = parseFloat($("#pagoTotal").val());
                    let pagado = parseFloat($("#pagado").val());

                    if (pagado >= pagoGranTotal) {
                        fn_validaCuadreFormaPago();
                    } else {
                        let pagoTotalDespuesDelPago = falta - ValorPagadoConDeUna;
                        $("#pagoTotal").val(pagoTotalDespuesDelPago.toFixed(2));
                        $("#pagado").val("");
                    }
                }
            } else {
                let estadoDeUna = estadoModalDeUna();
                if (estadoDeUna === 1) {
                    setTimeout(function () {
                        existePagoDeuna(requestId);
                    }, intervalo);
                }
            }
        },
        error: function (e) {
            cargandoDeUnaModal(0);
            console.log("error");
            console.log(e);
        }
    });

}

function existePagoDeunaDesdeCancelacion(requestId, isShown) {
    if (requestId == null || requestId == undefined || requestId == "") {
        console.log("No existe requestId");
        return false;
    }
    let odp_id = $("#txtOrdenPedidoId").val();

    let usr_id = $("#txtUserId").val();
    let rst_id = $("#hide_rst_id").val();
    let IDEstacion = $("#IDEstacionDeUna").val();
    let cdn_id = $("#txtCadenaId").val();
    let send = { odp_id, consultarPago: 1, usr_id, rst_id, IDEstacion, cdn_id, requestId: requestId };
    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        url: "../DeUna/estadoPagoDeUna.php",
        data: send,
        beforeSend: function () {
        },
        success: function (datos) {
            console.log("exito en consulta cfac_id");
            console.log(datos);
            if (datos.existePago == true) {
                cargandoDeUnaModal(isShown);
                
                $("#myModalDeunaConfirmarCancelacion").hide();
                $("#cancelarModalDeUna").prop("disabled", false);
                $("#esperarModalDeUna").prop("disabled", false);
                fn_insertarFormaPago();
                fn_buscaPagoCredito();
                let pagoGranTotal = parseFloat($("#pagoGranTotal").val());
                let falta = parseFloat($("#pagoTotal").val());
                let pagado = parseFloat($("#pagado").val());

                if (pagado >= pagoGranTotal) {
                    fn_validaCuadreFormaPago();
                } else {
                    let pagoTotalDespuesDelPago = falta - ValorPagadoConDeUna;
                    $("#pagoTotal").val(pagoTotalDespuesDelPago.toFixed(2));
                    $("#pagado").val("");
                }

                Swal.fire({
                    title: 'Pago recibido justo a tiempo.',
                    text: "Se detecto el pago, la factura se debe generar con normalidad.",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                })
                
                return true;
            } else {
                fn_desmontarPagoDeuna();
                setTimeout(() => {
                    cargandoDeUnaModal(isShown);
                    $("#myModalDeunaConfirmarCancelacion").hide();
                    $("#cancelarModalDeUna").prop("disabled", false);
                    $("#esperarModalDeUna").prop("disabled", false);
                }, 200);
                return false;
            }
        },
        error: function (e) {
            cargandoDeUnaModal(0);
            console.log("error");
            console.log(e);
            fn_desmontarPagoDeuna();
            setTimeout(() => {
                cargandoDeUnaModal(isShown);
                $("#myModalDeunaConfirmarCancelacion").hide();
                $("#cancelarModalDeUna").prop("disabled", false);
                $("#esperarModalDeUna").prop("disabled", false);
            }, 200);
            return false;
        }
    });

}

function existePagoDeunaDesdeReanudarPago(requestId) {
    if (requestId == null || requestId == undefined || requestId == "") {
        console.log("No existe requestId");
        return false;
    }
    let odp_id = $("#txtOrdenPedidoId").val();

    let usr_id = $("#txtUserId").val();
    let rst_id = $("#hide_rst_id").val();
    let IDEstacion = $("#IDEstacionDeUna").val();
    let cdn_id = $("#txtCadenaId").val();
    let send = { odp_id, consultarPago: 1, usr_id, rst_id, IDEstacion, cdn_id, requestId: requestId };
    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        url: "../DeUna/estadoPagoDeUna.php",
        data: send,
        beforeSend: function () {
        },
        success: function (datos) {
            console.log("exito en consulta cfac_id");
            console.log(datos);
            if (datos.existePago == true) {
                cargandoDeUnaModal(0);
                
                $("#myModalDeunaConfirmarCancelacion").hide();
                $("#cancelarModalDeUna").prop("disabled", false);
                $("#esperarModalDeUna").prop("disabled", false);
                fn_insertarFormaPago();
                fn_buscaPagoCredito();
                let pagoGranTotal = parseFloat($("#pagoGranTotal").val());
                let falta = parseFloat($("#pagoTotal").val());
                let pagado = parseFloat($("#pagado").val());

                if (pagado >= pagoGranTotal) {
                    fn_validaCuadreFormaPago();
                } else {
                    let pagoTotalDespuesDelPago = falta - ValorPagadoConDeUna;
                    $("#pagoTotal").val(pagoTotalDespuesDelPago.toFixed(2));
                    $("#pagado").val("");
                }

                Swal.fire({
                    title: 'Pago recibido justo a tiempo.',
                    text: "Se detecto el pago, la factura se debe generar con normalidad.",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                })
                
                return true;
            } else {
                existePagoDeuna(requestId);
            }
        },
        error: function (e) {
            cargandoDeUnaModal(0);
            console.log("error");
            console.log(e);
            fn_desmontarPagoDeuna();
            setTimeout(() => {
                cargandoDeUnaModal(isShown);
                $("#myModalDeunaConfirmarCancelacion").hide();
                $("#cancelarModalDeUna").prop("disabled", false);
                $("#esperarModalDeUna").prop("disabled", false);
            }, 200);
            return false;
        }
    });

}

// inicio Proceso Payphone
async function fn_aplicarPagoPayPhone() {

    const datos = fn_consultarMediosPagoPayphoneDisponible();
    if (datos._value.str >= 1) {
        cargarMediosPagoPayphone(datos._value);
        return
    }
    fn_payphoneTransaccionDirecta();
}

function cargarMediosPagoPayphone(datos) {

    if (datos.str === 1) {
        fn_EjecutarMetodoPayphone(datos[0].tipo);
    } else {

        var html = "";
        $("#contenedorBotonesDinamico").html("");
        for (var i = 0; i < datos.str; i++) {
            var item = " <div class=\"BotonMedioPayphone\" onclick=\"fn_EjecutarMetodoPayphone('" + datos[i].tipo + "')\" >\n" +
                "<h5 class=\"card-title\"> " + datos[i].tipo + "</h5>\n" +
                "<p class=\"card-text\"> " + datos[i].texto + "</p>\n" +
                "</div>";
            html += item;
        }
        $("#contenedorBotonesDinamico").append(html);
        $("#modalbotonesPayphone").show();

    }

}

// inicio Proceso Payphone
async function fn_aplicarPagoPayPhone() {

    const datos = fn_consultarMediosPagoPayphoneDisponible();
    if (datos._value.str >= 1) {
        cargarMediosPagoPayphone(datos._value);
        return
    }
    fn_payphoneTransaccionDirecta();
}

function cargarMediosPagoPayphone(datos) {

    if (datos.str === 1) {
        fn_EjecutarMetodoPayphone(datos[0].tipo);
    } else {

        var html = "";
        $("#contenedorBotonesDinamico").html("");
        for (var i = 0; i < datos.str; i++) {
            var item = " <div class=\"BotonMedioPayphone\" onclick=\"fn_EjecutarMetodoPayphone('" + datos[i].tipo + "')\" >\n" +
                    "<h5 class=\"card-title\"> " + datos[i].tipo + "</h5>\n" +
                    "<p class=\"card-text\"> " + datos[i].texto + "</p>\n" +
                    "</div>";
            html += item;
        }
        $("#contenedorBotonesDinamico").append(html);
        $("#modalbotonesPayphone").show();

    }

}

function fn_EjecutarMetodoPayphone(tipo) {
    $("#modalbotonesPayphone").hide();
    switch (tipo) {

        case  'TRANSACCION DIRECTA':
            fn_payphoneTransaccionDirecta();
            break;

        case  'APP MOVIL':
            fn_payphoneAppMovilAbrirModal();
            break;

        case  'LINK DE PAGOS':
            fn_modalAplicarFormaPagoLinkPayphone();
            break;

        default:
            break;
    }
}

function estaActivoPayPhoneFormularioEnOrdenPedido() {
    if (localStorage.getItem("ls_PayPhoneFormularioEnOrdenPedido") === null)
        return false;
    if (localStorage.getItem("ls_PayPhoneFormularioEnOrdenPedido") === "0")
        return false;
    if (localStorage.getItem("ls_PayPhoneFormularioEnOrdenPedido") === "1")
        return true;
}

function eliminarLocalStorage() {

    if (estaActivoPayPhoneFormularioEnOrdenPedido())
        return;

    localStorage.removeItem("ls_documento"+cuenta);
    localStorage.removeItem("ls_nombres"+cuenta);
    localStorage.removeItem("ls_direccion"+cuenta);
    localStorage.removeItem("ls_telefono"+cuenta);
    localStorage.removeItem("ls_correo"+cuenta);
}

// PAyphone
function fn_payphoneGuadarDatosRespuesta(idRespuesta, idDispositivo) {


    cargando(1);

    $("#countdown").countdown360().stop();
    $("#txt_trama").val("");

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



    $.getJSON("config_facturacion.php", send, function (datos) {

        send = { "grabacanalmovimientoVoucher": 1 };
        send.respuesta = idRespuesta;
        $.getJSON("config_facturacion.php", send, function (datos) { });
        lc_control = 0;
        var aPagar = parseFloat(pagadoTotalSinSeparador);
        var pagado = parseFloat(pagadoSinSeparador);
        var valor = aPagar - pagado;
        var btnNombre = $("#btnAplicarPago").attr("title");
        $("#pagoTotal").val(valor.toFixed(2));
        $("#pagado").val("");
        fn_resumenFormaPago();
        if ((valor <= 0 && btnNombre != "EFECTIVO")) {
            fn_envioFactura();
        }
    });



}

function generarAleatorio(minimo = 1, maximo = 10000) {
    return Math.floor(Math.random() * ((maximo + 1) - minimo) + minimo);
}

function fn_resumenFormaPago() {
    send = { "resumenFormaPago": 1 };
    send.cfactura = $("#txtNumFactura").val();
    $.ajax({
        async: true,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            //alert();
            $("#formasPago2").empty();
            if (datos.str > 0) {
                $("#btnCancelarPago").attr("disabled", false);
                for (i = 0; i < datos.str; i++) {
                    html = "<tr>";
                    //"<div id='formasPago2'><table  align='left' width='300px'>";
                    html +=
                        "<td width='130px' align='right'>" +
                        datos[i]["fmp_descripcion"] +
                        "</td><td width='40px' align='center'>$</td>";
                    html +=
                        "<td width='100px' align='center'>" +
                        parseFloat(datos[i]["fpf_total_pagar"]).toFixed(2) +
                        "</td>";
                    html += "</tr>";
                    $("#formasPago2").append(html);
                    $("#pagado").val("");
                }
            } else {
                $("#btnCancelarPago").attr("disabled", true);
            }
        }
    });
}

/////////////////////////////AGREGAR NUMERO/////////////////////////////////
function fn_agregarNumero(valor) {
    var lc_cantidad = $("#pagado").val();

    if (lc_cantidad.indexOf(".") != -1 && valor == ".") {
        lc_cantidad = lc_cantidad;
    } else {
        lc_cantidad += valor;
    }

    $("#pagado").val(lc_cantidad);
}

/////////////////////////////ELIMINAR NUMERO/////////////////////////////////
function fn_eliminarCantidad() {
    var lc_cantidad = document.getElementById("pagado").value.substring(0, document.getElementById("pagado").value.length - 1);
    if (lc_cantidad == "") {
        lc_cantidad = "";
    }
    document.getElementById("pagado").value = lc_cantidad;
}

/////////////////////////////AGREGAR NUMERO/////////////////////////////////
function teclado(elEvento) {
    evento = elEvento || window.event;
    k = evento.keyCode;
    if (k > 47 && k < 58) {
        p = k - 48; //buscar número a mostrar.
        p = String(p); //convertir a cadena para poder añádir en pantalla.
        fn_agregarNumero(p); //enviar para mostrar en pantalla
    }
    if (k > 95 && k < 106) {
        p = k - 96;
        p = String(p);
        fn_agregarNumero(p);
    }
    if (k == 110 || k == 190) {
        fn_agregarNumero(".");
    } //teclas de coma decimal
    if (k == 8) {
        fn_eliminarNumero();
    } //Retroceso en escritura : tecla retroceso.
    if (k > 57 && k < 210) {
        document.getElementById("pagado").value = "";
    }
}

////////////////////////////////CLIENTES BUSQUEDA//////////////////////////////////////////
function fn_clienteInfo() {
    if (validarDocumento($("#txtClienteCI").val())) {
        send = { "clienteInfo": 1 };
        send.clienteCedula = $("#txtClienteCI").val();
        $("#txtClienteB").val("");
        $("#txtClienteCI").val("");
        $("#txtClienteDireccion").val("");
        $("#txtClienteFono").val("");
        $("#txtCorreo").val("");
        $.ajax({
            async: true,
            url: "config_facturacion.php",
            data: send,
            dataType: "json",
            success: function (datos) {
                if (datos.str > 0) {
                    $("#txtClienteNombre").val(datos[0]["cli_nombres"]);
                    $("#txtClienteApellido").val(datos[0]["cli_apellidos"]);
                    $("#txtClienteCI").val(datos[0]["cli_documento"]);
                    $("#txtClienteDireccion").val(datos[0]["cli_direccion"]);
                    $("#txtClienteFono").val(datos[0]["cli_telefono"]);
                    $("#txtCorreo").val(datos[0]["cli_email"]);
                } else {
                    alertify.alert("No existe usuario con este Documento de Identificaci\u00f3n");
                }
            }
        });
    } else {
        fn_limpiarInfo();
        alertify.alert("N\u00famero de documento invalido");
    } //
}

/////////////////////////////////////////VALIDAR CEDULA/////////////////////////
function validarDocumento(campo) {
    if (campo.search(/\D/g) >= 0) {
        return false;
    }
    numero = campo;
    var suma = 0;
    var residuo = 0;
    var pri = false;
    var pub = false;
    var nat = false;
    var numeroProvincias = 22;
    var modulo = 11;
    //var ok=1;			/* Verifico que el campo no contenga letras */
    /* Aqui almacenamos los digitos de la cedula en variables. */
    d1 = numero.substr(0, 1);
    d2 = numero.substr(1, 1);
    d3 = numero.substr(2, 1);
    d4 = numero.substr(3, 1);
    d5 = numero.substr(4, 1);
    d6 = numero.substr(5, 1);
    d7 = numero.substr(6, 1);
    d8 = numero.substr(7, 1);
    d9 = numero.substr(8, 1);
    d10 = numero.substr(9, 1);
    /* El tercer digito es: */
    /* 9 para sociedades privadas y extranjeros */
    /* 6 para sociedades publicas */
    /* menor que 6 (0,1,2,3,4,5) para personas naturales */
    if (d3 == 7 || d3 == 8) {
        return false;
    }
    if (d10 == "") {
        return false;
    }
    /* Solo para personas naturales (modulo 10) */
    if (d3 < 6) {
        nat = true;
        p1 = d1 * 2;
        if (p1 >= 10) {
            p1 -= 9;
        }
        p2 = d2 * 1;
        if (p2 >= 10) {
            p2 -= 9;
        }
        p3 = d3 * 2;
        if (p3 >= 10) {
            p3 -= 9;
        }
        p4 = d4 * 1;
        if (p4 >= 10) {
            p4 -= 9;
        }
        p5 = d5 * 2;
        if (p5 >= 10) {
            p5 -= 9;
        }
        p6 = d6 * 1;
        if (p6 >= 10) {
            p6 -= 9;
        }
        p7 = d7 * 2;
        if (p7 >= 10) {
            p7 -= 9;
        }
        p8 = d8 * 1;
        if (p8 >= 10) {
            p8 -= 9;
        }
        p9 = d9 * 2;
        if (p9 >= 10) {
            p9 -= 9;
        }
        modulo = 10;
    } else if (d3 == 6) {
        /* Solo para sociedades publicas (modulo 11) */
        /* Aqui el digito verficador esta en la posicion 9, en las otras 2 en la pos. 10 */
        pub = true;
        p1 = d1 * 3;
        p2 = d2 * 2;
        p3 = d3 * 7;
        p4 = d4 * 6;
        p5 = d5 * 5;
        p6 = d6 * 4;
        p7 = d7 * 3;
        p8 = d8 * 2;
        p9 = 0;
    } else if (d3 == 9) {
        pri = true;
        p1 = d1 * 4;
        p2 = d2 * 3;
        p3 = d3 * 2;
        p4 = d4 * 7;
        p5 = d5 * 6;
        p6 = d6 * 5;
        p7 = d7 * 4;
        p8 = d8 * 3;
        p9 = d9 * 2;
    }
    suma = p1 + p2 + p3 + p4 + p5 + p6 + p7 + p8 + p9;
    residuo = suma % modulo;
    digitoVerificador = residuo == 0 ? 0 : modulo - residuo;
    /* Si residuo=0, dig.ver.=0, caso contrario 10 - residuo*/
    /* ahora comparamos el elemento de la posicion 10 con el dig. ver.*/
    if (pub == true) {
        if (digitoVerificador != d9) {
            return false;
        }
        /* El ruc de las empresas del sector publico terminan con 0001*/
        if (numero.substr(9, 4) != "0001") {
            return false;
        }
    } else if (pri == true) {
        if (digitoVerificador != d10) {
            return false;
        }
        if (numero.substr(10, 3) != "001") {
            return false;
        }
    } else if (nat == true) {
        if (digitoVerificador != d10) {
            return false;
        }
        if (numero.length > 10 && numero.substr(10, 3) != "001") {
            return false;
        }
    }
    return true;
}

function fn_agregarNumero(valor) {
    lc_cantidad = document.getElementById("cantidad").value;
    if (lc_cantidad == 0 && valor == ".") {
        //si escribimos una coma al principio del número
        document.getElementById("cantidad").value = "0."; //escribimos 0.
        coma = 1;
    } else {
        //continuar escribiendo un número
        if (valor == "." && coma == 0) {
            //si escribimos una coma decimal pòr primera vez
            lc_cantidad = lc_cantidad + valor;
            document.getElementById("cantidad").value = lc_cantidad;
            coma = 1; //cambiar el estado de la coma  
        }
        //si intentamos escribir una segunda coma decimal no realiza ninguna acción.
        else if (valor == "." && coma == 1) {
            //Resto de casos: escribir un número del 0 al 9: 	 
        } else {
            $("#cantidad").val("");
            lc_cantidad = lc_cantidad + valor;
            document.getElementById("cantidad").value = lc_cantidad;
        }
    }
    fn_focusLector();
}

/////////Funcion que permite verificarr si existe una forma de pago aplicada antes de regresar a retomar la orden///////////////
function validaExisteFormaPagoSalir() {
    send = { "validaExisteFormaPagoSalir": 1 };
    send.dop_cuenta = $("#txtNumCuenta").val();
    send.factAevaluar = $("#txtNumFactura").val();
    let tarjeta=$("#Tarjeta").val();
    if(tarjeta=='1'){
        alertify.alert("No permitido, ya existe un pago con tarjeta en proceso.");
        return false;    
    }
    $.getJSON("config_facturacion.php", send, function (datos) {
        //        if (datos.impreso == 1) {
        //            alertify.alert("No permitido. La orden ya fue impresa.");
        //            return false;
        //        } else 
        if (datos.pagado == 1) {
            alertify.alert("Ya existe una forma de pago aplicada a esta factura.");
            return false;
        } else if (datos.descuento == 1) {
            alertify.alert("Existe un descuento aplicado a esta factura.");
            return false;
        } else if (datos.pagado == 0 && datos.descuento == 0) {
            mesa_id = $("#txtNumMesa").val();
            odp_id = $("#txtOrdenPedidoId").val();
            send = { "actualiza_estados_OrdenYfactura": 1 };
            send.mesaF = mesa_id;
            send.odpId = odp_id;
            send.dop_cuenta = $("#txtNumCuenta").val();
            $.getJSON("config_facturacion.php", send, function (datos) {
                if (datos["direccion"] === "pantalla_separarcuenta") {
                    window.location.replace(datos["url"]);
                } else if (datos["direccion"] === "pantalla_ordenpedido") {
                    window.location.replace(datos["url"]);
                }
            });
        }
    });
}

function fn_validaEnvioSubSWT(subagrupacion, envio_id) {
    var send;
    lc_subagrupacion = subagrupacion;
    lc_tipoEnvio = envio_id;
    if (subagrupacion == "PINPAD") {
        send = { "verificaConfiguracionSWT": 1 };
        send.SWTaccionconfiguracion = "subagrupacion";
        send.tipoConfiguracionSWT = subagrupacion;
        $.getJSON("config_facturacion.php", send, function (datos) {
            if (datos.existe == 1) {
                $("#modalsubSWT").dialog("close");
                $("#modalSWT").dialog("close");
                $(".jb-shortscroll-wrapper").show();
                send = { "insertarRequerimientoAutorizacion": 1 };
                send.cfac = $("#txtNumFactura").val();
                send.formaPagoID = $("#btnFormaPagoId").val();
                send.valorTransaccion = $("#pagado").val();
                if ($("#cantidad").val() == "") {
                    send.prop_valor = 0;
                } else {
                    send.prop_valor = parseFloat($("#cantidad").val());
                }
                $.getJSON("config_facturacion.php", send, function (datos) {
                    lc_control = 0;
                    cargando(0);
                    fn_timeout();
                    fn_esperaRespuesta();
                }); //lectura PINPAD
            } else if (datos.existe == 2) {
                $("#usr_claveAdmin").val("");
                lc_banderaOkAdmin = "pinpad";
                alertify.set({ labels: { ok: "SI", cancel: "NO" } });
                alertify.confirm("Su estaci&oacute;n actualmente no dispone esta opci&oacute;n. Desea activar el cobro de tarjetas por medio de " + subagrupacion + "?", function (e) {
                    if (e) {
                        $("#credencialesAdmin").dialog({
                            modal: true,
                            draggable: false,
                            width: 500,
                            heigth: 500,
                            resizable: false,
                            opacity: 0,
                            show: "none",
                            hide: "none",
                            duration: 500
                        });
                    }

                }); //lectura PINPAD si esque activa la opcion
            } else {
                alertify.error("Configuracion incorrecta.");
                return false;
            }

        });
    } else if (subagrupacion == "BANDA MAGNETICA") {
        $("#modalsubSWT").dialog("close");
        $("#modalSWT").dialog("close");
        $(".jb-shortscroll-wrapper").show();
        send = { "verificaConfiguracionSWT": 1 };
        send.SWTaccionconfiguracion = "subagrupacion";
        send.tipoConfiguracionSWT = subagrupacion;
        $.getJSON("config_facturacion.php", send, function (datos) {
            if (datos.existe == 1) {
                alertify.log("DESLICE LA TARJETA.");
                $("#txt_trama").focus();
                $("#txt_trama").keyup(function (event) {
                    if (event.keyCode == "13") {
                        fn_muestraTecladoCVV();
                    }
                });
                //LECTURA POR BANDA	
            } else if (datos.existe == 2) {
                alertify.set({ labels: { ok: "SI", cancel: "NO" } });
                alertify.confirm("Su estaci&oacute;n actualmente no dispone esta opci&oacute;n. Desea activar el cobro de tarjetas por medio de " + subagrupacion + "?", function (e) {
                    if (e) {
                        $("#usr_claveAdmin").val("");
                        lc_banderaOkAdmin = "banda";
                        $("#credencialesAdmin").show();
                        $("#credencialesAdmin").dialog({
                            modal: true,
                            draggable: false,
                            width: 500,
                            heigth: 500,
                            resizable: false,
                            opacity: 0,
                            show: "none",
                            hide: "none",
                            duration: 500
                        });
                    } else {
                        $("#pagado").val("");
                    }

                });
                //lectura BANDA si esque activa la opcion					
            } else {
                alertify.error("Configuracion incorrecta.");
                return false;
            }
        });
    } else {
        alertify.error("Configuracion incorrecta. No tiene ninguna subagrupación");
        return false;
    }
}


function fn_armaTramaPinpadunired(opcion, tipoTran) {
    valordelaTransaccionPinPadP = parseFloat($("#pagoTotal").val());
    send = { "insertarRequerimientoPinpadProduccion": 1 };
    send.cfacPinpadP = $("#txtNumFactura").val();
    send.formaPagoIDPinpadP = $("#btnFormaPagoId").val();
    if (opcion == "compra") {
        if ($("#cantidad").val() == "") {
            send.prop_valorPinpadP = 0;
        } else {
            send.prop_valorPinpadP = parseFloat($("#cantidad").val());
        }
        if (banderaa == 1) {
            send.valorTransaccionPinPadP = $("#pagado").val();
        } else {
            if (valordelaTransaccionPinPadP != 0) {
                send.valorTransaccionPinPadP = $("#pagado").val();
            } else {
                send.valorTransaccionPinPadP = $("#pagoGranTotal").val();
            }
        }
    } else {
        send.prop_valorPinpadP = 0;
        send.valorTransaccionPinPadP = 0;
        send.valorTransaccionPinPadP = 0;
    }
    send.tipoEnvioPinPadP = 5; //lc_tipoEnvio;
    send.tipoTransaccionPinpadP = tipoTran;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        success: function (datos) {
            lc_control = 0;
            cargando(0);
            fn_timeoutPinpadUnired();
            if (opcion == "compra") {
                TEMPORIZADORUNIRED = setInterval(function () {
                    fn_esperaRespuestaPProduccion();
                }, 1000);
            } else {
                TEMPORIZADORUNIRED = setInterval(function () {
                    fn_esperaRespuestaCancelacionUnired();
                }, 1000);
            }
        }
    });
}

function Insertar_Pago() {
    alert("pago");
}

function fn_validaEnvioSWT(event, descripcion, integracion, autoriza, secuenciaConfigurada, secuencia) {
    event.stopPropagation();
    $("#modalsubSWT").dialog("close");
    $("#modalSWT").dialog("close");
    $(".jb-shortscroll-wrapper").show();
    lc_tipoEnvio = integracion;
    secuencias = secuencia.split("->");
    var funcion = "";
    $.ajaxSetup({ async: false });

    if (secuencias[0] == "Armar_Trama") {
        secuencias = secuencias.splice(1, secuencias.length - 1);
        Armar_Trama_Dinamica(
            "ENVIO",
            integracion,
            secuencias,
            "",
            "",
            $("#txtNumFactura").val(),
            $("#btnFormaPagoId").val(),
            "FACTURACION"
        );
    } else if (secuencias[0] == "Esperar_Respuesta") {
        secuencias = secuencias.splice(0, 1);
        Esperar_Respuesta();
    } else if (secuencias[0] == "Insertar_Pago") {
        secuencias = secuencias.splice(0, 1);
        Insertar_Pago();
    } else if (secuencias[0] == "Ingresar_Bin") {
        $("#modalsubSWT").dialog("close");
        $("#modalSWT").dialog("close");
        $(".jb-shortscroll-wrapper").show();

        Ingresar_Bin();
    } else if (secuencias[0] == "Lectura_Tarjeta") {
        secuencias = secuencias.splice(1, secuencias.length - 1);
        //Armar_Trama_Dinamica('ENVIO', datos[0]['idIntegracion'],secuencias,'','',$("#txtNumFactura").val(),$("#btnFormaPagoId").val(),'FACTURACION');      
      
        fn_validaConfiguracionBinTarjetaLectura(
            "ENVIO",
            integracion,
            secuencias,
            $("#txtNumFactura").val(),
            $("#btnFormaPagoId").val(),
            "FACTURACION"
        );
    }
    //funcion();
    //          }
    $.ajaxSetup({ async: true });
    return false;
    //event.stopPropagation();
    lc_tipoEnvio = integracion;
    if (integracion == 1) {
        //INICIO DE PINPAD
        $("#modalsubSWT").dialog("close");
        $("#modalSWT").dialog("close");
        $(".jb-shortscroll-wrapper").show();
        if (autoriza == 1) {
            //autorizado a aplicar pinpad
            fn_armaTramaPinPadMultired("compra", "01");
        } //no autorizado a aplicar pinpad
        else {
            alertify.set({ labels: { ok: "SI", cancel: "NO" } });
            alertify.confirm("Su estaci&oacute;n actualmente no dispone esta opci&oacute;n. Desea activar el cobro de tarjetas por medio de " + descripcion + "?", function (e) {
                if (e) {
                    $("#usr_claveAdmin").val("");
                    lc_banderaOkAdmin = "pinpad";
                    $("#credencialesAdmin").show();
                    $("#credencialesAdmin").dialog({
                        modal: true,
                        draggable: false,
                        width: 500,
                        heigth: 500,
                        resizable: false,
                        opacity: 0,
                        show: "none",
                        hide: "none",
                        duration: 500
                    });
                } else {
                    $("#pagado").val("");
                }
            }
            );
        }
    } //FIN DE PIN PAD
    //Inicio de banda
    else if (integracion == 2) {
        $("#modalsubSWT").dialog("close");
        $("#modalSWT").dialog("close");
        $(".jb-shortscroll-wrapper").show();
        //1 esta autorizado a aplicar el tipo de envio
        if (autoriza == 1) {
   
            fn_validaConfiguracionBinTarjetaLectura();
            //si no lo esta solicita credenciales de admin
        } else {
            alertify.set({ labels: { ok: "SI", cancel: "NO" } });
            alertify.confirm("Su estaci&oacute;n actualmente no dispone esta opci&oacute;n. Desea activar el cobro de tarjetas por medio de " + descripcion + "?", function (e) {
                if (e) {
                    $("#usr_claveAdmin").val("");
                    lc_banderaOkAdmin = "banda";
                    $("#credencialesAdmin").show();
                    $("#credencialesAdmin").dialog({
                        modal: true,
                        draggable: false,
                        width: 500,
                        heigth: 500,
                        resizable: false,
                        opacity: 0,
                        show: "none",
                        hide: "none",
                        duration: 500
                    });
                } else {
                    $("#pagado").val("");
                }
            });
        }
    } //fin de banda
    else if (integracion == 3) {
        //inicio de datafast-otros
        //$("#modalsubSWT").dialog("close");
        //$("#modalSWT").dialog("close");
        //$(".jb-shortscroll-wrapper").show();
        if (autoriza == 1) {
            //fn_insertaFormaPagoTarjetaDatafast();
            fn_modalBinDatafast();
        } // cuando la estacion no se encuentra autorizada a aplicar datafast
        else {
            alertify.set({ labels: { ok: "SI", cancel: "NO" } });
            alertify.confirm("Su estaci&oacute;n actualmente no dispone esta opci&oacute;n. Desea activar el cobro de tarjetas por medio de " + descripcion + "?", function (e) {
                if (e) {
                    lc_banderaOkAdmin = "otro";
                    $("#credencialesAdmin").show();
                    $("#credencialesAdmin").dialog({
                        modal: true,
                        draggable: false,
                        width: 500,
                        heigth: 500,
                        resizable: false,
                        opacity: 0,
                        show: "none",
                        hide: "none",
                        duration: 500
                    });
                }
            }
            );
        }
    } //fin de datafast-otros //inicio de pinpad Unired
    else {
        $("#modalsubSWT").dialog("close");
        $("#modalSWT").dialog("close");
        $(".jb-shortscroll-wrapper").show();
        if (autoriza == 1) {
            //autorizado a aplicar pinpad
            fn_armaTramaPinpadunired("compra", "01");
        } //no autorizado a aplicar pinpad
        else {
            alertify.alert("Estacion no autorizada para aplicar Pinpad Unired");
            return false;
        }
    }
}

function Ingresar_Bin() {

    $("#modal_binDatafast").dialog({
        modal: true,
        resizable: false,
        closeOnEscape: false,
        position: "center",
        draggable: false,
        width: 500,
        heigth: 500,
        opacity: 0,
        show: "none",
        hide: "none",
        duration: 500,
        buttons: {
            "Aceptar": function () {
                fn_validarBinTarjetaDatafast();
            },
            "Cancelar": function () {
                $(this).dialog("close");
                $(".jb-shortscroll-wrapper").show();
                $("#pagado").val("");
                $("#txt_bin").val("");
            }
        }
    });
}


function fn_insertaPagoTarjeta() {

    var html = "";
    banderaa = $("#hid_bandera_cvv").val();
    var send = {"validaColeccionEstacionTipoEnvio": 1};
    var lc_tipoEnvio = 0;
    var valordelaTransaccionPinPad = 0;
    var valordelaTransaccionPinPadP = 0;
    //$("#Tarjeta").val(1);
    $.getJSON("config_facturacion.php", send, function (datos) {
        if (datos.str > 0) {
            if (datos.str > 1) {
                $("#tblSWT").empty();
                html = "";
                for (i = 0; i < datos.str; i++) {
                    html += "<td><button class='ui-state-default ui-corner-all' onclick='fn_validaEnvioSWT(event,\"" + datos[i]['Descripcion'] + "\",\"" + datos[i]['idIntegracion'] + "\"," + datos[i]['autorizado'] + ",\"" + datos[i]['secuenciaConfigurada'] + "\",\"" + datos[i]['secuencia'] + "\");'>" + datos[i]['Descripcion'] + "</button></td>";

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

                secuencias = datos[0]['secuencia'].split("->");
                $.ajaxSetup({async: false});
                if (secuencias[0] == 'Armar_Trama') {
                    secuencias = secuencias.splice(1, secuencias.length - 1);
                    Armar_Trama_Dinamica('ENVIO', datos[0]['idIntegracion'], secuencias, '', '', $("#txtNumFactura").val(), $("#btnFormaPagoId").val(), 'FACTURACION');
                } else if (secuencias[0] == 'Esperar_Respuesta') {
                    secuencias = secuencias.splice(0, 1);
                    Esperar_Respuesta();
                } else if (secuencias[0] == 'Insertar_Pago') {
                    secuencias = secuencias.splice(0, 1);
                    Insertar_Pago();
                } else if (secuencias[0] == 'Ingresar_Bin') {
                    $("#modalsubSWT").dialog("close");
                    $("#modalSWT").dialog("close");
                    $(".jb-shortscroll-wrapper").show();
                    Ingresar_Bin();

                } else if (secuencias[0] == 'Lectura_Tarjeta') {
                    secuencias = secuencias.splice(1, secuencias.length - 1);
                    lc_tipoEnvio = datos[0]['idIntegracion'];
                    //Armar_Trama_Dinamica('ENVIO', datos[0]['idIntegracion'],secuencias,'','',$("#txtNumFactura").val(),$("#btnFormaPagoId").val(),'FACTURACION');      
                    fn_validaConfiguracionBinTarjetaLectura('ENVIO', datos[0]['idIntegracion'], secuencias, $("#txtNumFactura").val(), $("#btnFormaPagoId").val(), 'FACTURACION');
                }
                $.ajaxSetup({async: true});

            }
        } else {
            $("#pagado").val("");
            alertify.alert("No existe la configuraci&oacute;n para pagos con tarjetas para &eacute;sta estaci&oacute;n");
        }
    });
}

function fn_validaConfiguracionBinTarjetaLectura(tipoTransaccion, idDispositivo, secuencia, codigoFactura, idFormaPagoFatura, modulo) {
    $("#txt_trama").val("");
    alertify.log("DESLICE LA TARJETA.");
    $("#txt_trama").focus();
    $("#txt_trama").unbind().keypress(function (event) {
        if (event.which == 13) {
            var caracTarjeta = $("#txt_trama").val();
            var porcionTarjeta = caracTarjeta.substring(0, 50);
            send = { "verificaBinExistenteSwt": 1 };
            send.caracteresTarjeta = porcionTarjeta;
            $.getJSON("config_facturacion.php", send, function (datos) {
                if (datos.str > 0) {
                    if (datos.confirma == 1) {
                        if (secuencia[0] == "Ingresar_Cvv") {
                            secuencia = secuencia.splice(1, secuencia.length - 1);
                            fn_muestraTecladoCVV(tipoTransaccion, idDispositivo, secuencia, caracTarjeta, codigoFactura, idFormaPagoFatura, modulo);
                        }

                    } else if (datos.confirma == 2) {
                        $("#txt_trama").val("");
                        alertify.alert(datos.mensaje);
                        $("#pagado").val("");
                        return false;
                    } else {
                        $("#txt_trama").val("");
                        alertify.alert(datos.mensaje);
                        $("#pagado").val("");
                        return false;
                    }
                } else {
                    $("#txt_trama").val("");
                    alertify.error("Error!.");
                    $("$pagado").val("");
                }
            });
        }
    });
}

function fn_salirSistema() {
    send = { "validaDetalleEnFactura": 1 };
    send.codigodefactura = $("#txtNumFactura").val();
    $.getJSON("config_facturacion.php", send, function (datos) {
        if (datos.str > 0) {
            if (datos.existe == 1) {
                if ($("#txtTipoServicio").val() == 1) {
                    alertify.error(
                        "Existen detalle(s) en la Factura. No puede salir del sistema."
                    );
                } else {
                    alertify.confirm("Existen detalles en la factura. Est&aacute; seguro de salir del sistema?", function (e) {
                        if (e) {
                            lc_banderaOkAdmin = "salirSistema";
                            $(".jb-shortscroll-wrapper").hide();
                            $("#credencialesAdmin").show();
                            $("#credencialesAdmin").dialog({
                                modal: true,
                                width: 500,
                                heigth: 500,
                                resizable: false,
                                opacity: 0,
                                show: "none",
                                hide: "none",
                                duration: 500
                            });
                        }
                    });
                }
            }
        } else {
            window.location.replace("../index.php");
        }
    });
}

/*
 ////////////////////////////////////////////////////////////////////////////////////
 las siguientes funciones son para eliminar las formas de pago que ya esten aplicadas
 /////////////////////////////////////////////////////////////////////////////////////
 */
function fn_cancelarPago(event) {
    var send;
    event.stopPropagation();
    titulo = $("#hid_descTipoFp").val();
    var btnNombreFormaPago = $("#hid_descFp").val();
    $("#hid_bandera_cvv").val(2);
    var btnNombreFormaPago = $("#hid_descFp").val();
    var titulo_alerta = ((btnNombreFormaPago == "CONSUMO RECARGA") ? "CONSUMO RECARGA" : titulo);
    //NO SON TARJETAS
    if (
        titulo == "CONSUMO RECARGA" ||
        titulo == "EFECTIVO" ||
        titulo == "RETENCIONES" ||
        titulo == "CREDITO EMPRESA" ||
        titulo == "CHEQUES" ||
        titulo == "CREDITO EMPLEADO" ||
        titulo == "CUPON EFECTIVO"
    ) {
        send = { consulta_cancelacionTarjeta: 1 };
        send.can_codFact = $("#txtNumFactura").val();
        send.can_idPago = $("#btnFormaPagoId").val();
        send.banderaBuscaCancelacion = "OTRO";
        $.getJSON("config_facturacion.php", send, function (datos) {
            if (datos.existe == 1) {
                alertify.confirm(
                    "Esta seguro de cancelar la forma de pago " + titulo_alerta + "?",
                    function (e) {
                        if (e) {
                            if (btnNombreFormaPago == "CONSUMO RECARGA") {
                                cancelarPagoRecargaEfectivo();
                            } else if (
                                titulo == "EFECTIVO" ||
                                titulo == "RETENCIONES" ||
                                titulo == "CHEQUES"
                            ) {
                                send = { anula_formaPagoEfectivo: 1 };
                                send.anu_codFact = $("#txtNumFactura").val();
                                send.anu_idPago = $("#btnFormaPagoId").val();
                                $.getJSON("config_facturacion.php", send, function (datos) {
                                    fn_obtieneTotalApagar();
                                    $("#pagado").val("");
                                    fn_resumenFormaPago();
                                    $("#td_cambio").hide();
                                    $("#td_falta").show();
                                });

                                var tituloBotonCancelar = $("#btnCancelarPago").attr("title");
                                if (tituloBotonCancelar === "CUPON PREPAGADO") {
                                    //clienteCanjearCuponesMultimarca.php
                                    // if(""===cuponMultimarcaCanjeado){
                                    //     alert("Codigo de cupon multimarca vacio");
                                    //     return false;
                                    // }
                                    var codigoObj = {
                                        metodo: "anularCanje",
                                        codigoCupon: cuponMultimarcaCanjeado
                                    };
                                    // Verificar validez del cupon en gerente
                                    var $peticionValidacion = $.post("clienteCanjearCuponesMultimarca.php",
                                        codigoObj,
                                        function (datos) {
                                            // console.log("Se anuló el canje");
                                        },
                                        "json"
                                    );

                                }
                            } //fin forma de pago efectivo
                            else if (titulo == "CREDITO EMPRESA" || titulo == "CREDITO EMPLEADO") {
                                send = { "anula_formaPagoCredito": 1 };
                                send.anu_codFactCredito = $("#txtNumFactura").val();
                                send.anu_idPagoCredito = $("#btnFormaPagoId").val();
                                $.getJSON("config_facturacion.php", send, function (datos) {
                                    fn_obtieneTotalApagar();
                                    $("#pagado").val("");
                                    fn_resumenFormaPago();
                                    $("#td_cambio").hide();
                                    $("#td_falta").show();
                                });
                            }
                            else if (titulo == 'CUPON EFECTIVO'){
                                fn_anularPagoPayvalida();
                            }
                        } //FIN DEL ALERTIFY
                    });
            } // fin consulta si existen formas de pago a cancelar.
            else {
                alertify.error("No existe la forma de pago " + titulo + " aplicada a &eacute;sta factura.");
            } //// fin consulta si no existen formas de pago a cancelar.
        });
    } else if (titulo === "PAYPHONE") {

        alertify.error("Estimado usuario, para anular el pago PAYPHONE debe generar una Nota de Credito.");
    }  else if (titulo === "DE UNA") {
        console.log("ENTRO DE UNA");    
        //deunacancelar
        let cfac_idDeUna = $("#txtNumFactura").val().trim();
        fn_anularPagoDeUna(cfac_idDeUna);
        //alertify.error("Estimado usuario, para anular el pago DEUNA debe generar una Nota de Credito.");
    }   
    else //tarjetas
    { //formas de pago tarjetas
        send = { "consulta_cancelacionTarjeta": 1 };
        send.can_codFact = $("#txtNumFactura").val();
        send.can_idPago = $("#btnFormaPagoId").val();
        send.banderaBuscaCancelacion = "TARJETA";
        $.getJSON("config_facturacion.php", send, function (datos) {
            if (datos.existe == 1) {
                send = { "consultaSwtTransaccionalCancelacion": 1 };
                send.cancelacion_codFact = $("#txtNumFactura").val();
                $.getJSON("config_facturacion.php", send, function (datos) {
                    if (datos.str > 0) {
                        $("#tblSWTCancelacion").empty();
                        $(".jb-shortscroll-wrapper").hide();
                        for (i = 0; i < datos.str; i++) {
                            html = "";
                            html +=
                                "<td><button class='ui-state-default ui-corner-all' onclick='fn_anulaPagoTarjeta(\"" +
                                datos[i]["fpf_id"] +
                                '",' +
                                datos[i]["fpf_swt"] +
                                ',"' +
                                datos[i]["fmp_id"] +
                                '", "' +
                                datos[i]["secuenciaConfigurada"] +
                                '", "' +
                                datos[i]["secuencia"] +
                                "\");'>" +
                                datos[i]["fmp_descripcion"] +
                                "</button></td>";

                            $("#tblSWTCancelacion").append(html);
                        }
                        $("#modalSWTCancelacion").dialog({
                            modal: true,
                            resizable: false,
                            closeOnEscape: false,
                            position: "center",
                            draggable: false,
                            width: 500,
                            heigth: 500,
                            opacity: 0,
                            show: "none",
                            hide: "none",
                            duration: 500,
                            buttons: {
                                Cancelar: function () {
                                    $(this).dialog("close");
                                }
                            }
                        });
                    }
                }); // fin consultaSwtTransaccional	
            } else if (titulo === "PAYPHONE") {
                alertify.error("Estimado usuario, para anular el pago debe generar una Nota de Credito.");

            } else {
                alertify.error("No existe la forma de pagos con Tarjeta aplicada a &eacute;sta factura.");
            }
        });

        //fin de formas de pago con tarjeta					
    }
}

function fn_anulaPagoTarjeta(fpf_id, swt, fmp_id, secuenciaConfigurada, secuencias) {
    lc_tipoEnvio = swt;
    $("#btnFormaPagoId").val(fpf_id);

    let tipoEnvio = localStorage.getItem('servicio_tarjeta_aplica');
    if (tipoEnvio == 0) {
        //1.9.9
        if (swt == 1) { //pinpad
            lc_control = 0;
            cargando(0);
            fn_timeout();
            fn_armaTramaPinPadMultired("anula", "03");
        } else if (swt == 2) { //banda
            $("#txt_trama").val("");
            $("#modalSWTCancelacion").dialog("close");
            $(".jb-shortscroll-wrapper").show();
            alertify.log("DESLICE LA TARJETA.");
            $("#txt_trama").focus();
            $("#txt_trama").keyup(function (event) {
                if (event.keyCode == "13") {
                    fn_muestraTecladoCVV();
                }
            });
        } else if (swt == 3) { //otro tipo
            $("#modalSWTCancelacion").dialog("close");
            var send = { anula_formaPagoEfectivo: 1 };
            send.anu_codFact = $("#txtNumFactura").val();
            send.anu_idPago = fmp_id;
            $.getJSON("config_facturacion.php", send, function (datos) {
                fn_obtieneTotalApagar();
                fn_resumenFormaPago();
                $("#pagado").val("");
            });
        } else {
            $("#modalSWTCancelacion").dialog("close");
            fn_armaTramaPinpadunired("anula", "03");
        }
        //1.9.10.6
        $("#modalSWTCancelacion").dialog("close");
        secuencias.length = 0; //vacio el array que contiene las secuencias
        secuencias = secuencias.split("->"); //se ingresan nuevaamente datos al array de las secuencias

        if (secuencias[0] == "Armar Trama") {
            secuencias = secuencias.splice(1, secuencias.length - 1);
            Armar_Trama_Dinamica(
                "ANULACION",
                swt,
                secuencias,
                "",
                "",
                $("#txtNumFactura").val(),
                fpf_id,
                "FACTURACION"
            );
        } else if (secuencias[0] == "Lectura_Tarjeta") {
            secuencias = secuencias.splice(1, secuencias.length - 1);
       
            fn_validaConfiguracionBinTarjetaLectura(
                "ANULACION",
                swt,
                secuencias,
                $("#txtNumFactura").val(),
                fpf_id,
                "FACTURACION"
            );
        } else if (secuencias[0] == "Anular_Pago") {
            secuencias = secuencias.splice(1, secuencias.length - 1);
            $("#modalSWTCancelacion").dialog("close");
            send = { "anula_formaPagoEfectivo": 1 };
            send.anu_codFact = $("#txtNumFactura").val();
            send.anu_idPago = fmp_id;
            $.getJSON("config_facturacion.php", send, function (datos) {
                fn_obtieneTotalApagar();
                fn_resumenFormaPago();
                $("#pagado").val("");
            });
        }
    } else {
        cargando(0);

        $("#txtClienteCI").focus();
    
        let send = {
            'tipo': 'ANULACION',
            'dispositivo': swt,
            'factura': $("#txtNumFactura").val(),
            'valor': 0,
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
                    fn_cancelarFormaPagoConsumo("APROBADA",swt, datos.data.mensajeRespuesta, datos.data.rsautId, "FACTURACION", $("#btnFormaPagoId").val());
                } else {
                    fn_cancelarFormaPagoConsumo("NO APROBADO",swt, datos.data.mensajeRespuesta, datos.data.rsautId, "FACTURACION", $("#btnFormaPagoId").val());
                }
    
                hiddenModalPagos(datos.status);
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
    }
}

function fn_cancelarFormaPagoConsumo(respuestaAutorizacion, idDispositivo, respuesta, idRespuesta, modulo, fmp_id) {
    $("#modalSWTCancelacion").dialog("close");

    lc_control = 1;

    if (respuestaAutorizacion == "APROBADA") {
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
                $("#pagado").val("");
            });
        }
    } else {
        if (modulo == 'FACTURACION') {
            $("#countdown").countdown360().stop();
        }
        let apiImpresion = getConfiguracionesApiImpresion();
        if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
            fn_cargando(1);
            send.codigo_app = idRespuesta;
            result = new apiServicioImpresion('VoucherNoCancelar', send);        
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
            let send = { "grabaVoucherNoCancelar": 1 };
            send.respuesta = idRespuesta;
            $.getJSON("config_facturacion.php", send, function(datos) { });
            alertify.alert(respuesta);
            alertify.success('Imprimiendo Váucher...');
        }

        return false;
    }
}

function fn_obtieneTotalApagar() {
    send = { "obtieneTotalApagar": 1 };
    send.ob_codFact = $("#txtNumFactura").val();
    send.ob_idPago = $("#btnFormaPagoId").val();
    $.getJSON("config_facturacion.php", send, function (datos) {
        if (datos.str > 0) {
            $("#pagoTotal").val(parseFloat(datos.total).toFixed(2));
        }
    });
}

function fn_esperaRespuestaCancelacionUnired() {
    send = { "esperaRespuestaRequerimientoAutorizacion": 1 };
    send.cfac = $("#txtNumFactura").val();
    $.ajax({
        async: true,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.existe === 1) {
                fn_funcionMuestraRespuestaCancelacionUnired(datos.rsaut_respuesta, datos.cres_codigo, datos.fpf_id, datos.rsaut_id, datos.errorTrama);
            }
        }
    });
}

function fn_funcionMuestraRespuestaCancelacionUnired(respuesta, codigoRespuesta, formadePago, idRespuesta, tramaError) {
    var send;
    clearInterval(TEMPORIZADORUNIRED);
    $("#countdown").countdown360().stop();
    lc_control = 1;
    cargando(1);
    //alert(codigoRespuesta);
    if (tramaError == 1) {
        if (codigoRespuesta == "00") {
            send = { "cancelaTarjetaForma": 1 };
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
            send = { "grabacanalmovimientoVoucher": 1 };
            send.respuesta = idRespuesta;
            $.getJSON("config_facturacion.php", send, function (datos) { });
            alertify.alert(respuesta);
            return false;
        }
    } else {
        alertify.alert(tramaError);
        return false;
    }
}

function fn_regresarCliente() {
    $("#dominio1").css({
        display: "none",
        position: "absolute"
    });
    $("#dominio2").css({
        display: "none",
        position: "absolute"
    });
    $("#txtClienteCI").val("");
    $("#numPad").empty();
    fn_numerico(txtClienteCI);
    fn_ocultar_alfanumerico();
    $(".jb-shortscroll-wrapper").show();
    $("#listaFactura").shortscroll();
    $("#datosFactura").dialog("close");
    $("#datosFactura").dialog("destroy");
    $("#rdo_ruc").removeClass("btnRucCiInactivo");
    $("#rdo_ruc").addClass("btnRucCiActivo");
    $("#rdo_pasaporte").removeClass("btnRucCiActivo");
    $("#rdo_pasaporte").addClass("btnRucCiInactivo");
    $("#datosFactura").hide();
    $("#formasPago").show();
    $("#pagoTotal").val("0.00");
    $("#pagado").val("0.00");
    fn_resumenFormaPago();
    fn_limpiarInfo();
    $("#btnConsumidorFinal").show();
    $("#btnClienteGuardar").hide();
    $("#btnClienteGuardarActualiza").hide();
}


function fn_validaTeclado(id) {
        localStorage.setItem("invalido",0);
       
    $("#dominio1").hide();
    $("#dominio2").hide();

    if ($("#hid_bandera_teclado").val() == 1) {
        fn_numerico(id);
    } else if ($("#hid_bandera_teclado").val() == 2) {
        fn_alfaNumericoo(id);
    } else {
        alertify.error("Ninguna configuracion para el teclado");
        return false;
    }
}

function fn_validaTecladoCedula() {
    $("#hid_bandera_teclado").val(1);
    $("#rdo_ruc").removeClass("btnRucCiInactivo");
    $("#rdo_ruc").addClass("btnRucCiActivo");
    $("#rdo_pasaporte").removeClass("btnRucCiActivo");
    $("#rdo_pasaporte").addClass("btnRucCiInactivo");
    fn_limpiarInfo();
    fn_bloquearIngreso();
    $("#txtClienteCI").focus();
    fn_numerico(txtClienteCI);
    fn_ocultar_alfanumerico();
}

// VISUALIZAR FACTURA
function fn_cargarFactura(cfac_id) {
    var html = "";
    send = { "visorCabeceraFactura": 1 };
    send.cfac_id = cfac_id;
    $.getJSON("config_facturacion.php", send, function (datos) {
        if (datos.str > 0) {
            var tipo_facturacion = datos[0]["tf_id"];
            html =
                "<div class='facturacion'><br/><table width='220px' align='center'><tr><th align='center' colspan='4'>" +
                datos[0]["emp_razon_social"] +
                "</th></tr><tr><td align='center' colspan='4'>MATRIZ: " +
                datos[0]["emp_direccion"] +
                "</td></tr><tr><td style='padding-bottom:10px;' align='center' colspan='4'>RUC: " +
                datos[0]["emp_ruc"] +
                "</td></tr><tr>";
            if (tipo_facturacion == 2) {
                html = html + "<td align='center' colspan='4'>DETALLE DE FACTURA</td>";
            } else {
                html = html + "<td align='center' colspan='4'>DETALLE DE FACTURA ELECTRONICA</td>";
            }
            html =
                html +
                "</tr><tr><td style='padding-bottom:10px;' align='center' colspan='4'>DOCUMENTO SIN VALOR TRIBUTARIO</td></tr><tr><td align='left' colspan='4'># DOCUMENTO: " +
                datos[0]["documento"] +
                "</td></tr><tr><td align='left' colspan='4'>SUCURSAL: " +
                datos[0]["rst_direccion"] +
                "</td></tr><tr><td align='left' colspan='4'>SERV: " +
                datos[0]["usr_usuario"] +
                "</td></tr><tr><td align='left' colspan='4'>FECHA EMISION: " +
                datos[0]["cfac_fechacreacion"] +
                "</td></tr><tr><td align='left' colspan='4'>CLIENTE: " +
                datos[0]["cli_nombres"] +
                " " +
                datos[0]["cli_apellidos"] +
                "</td></tr><tr><td align='left' colspan='4'>RUC/CI: " +
                datos[0]["cli_documento"] +
                "</td></tr><tr><td align='left' colspan='4'>FONO: " +
                datos[0]["cli_telefono"] +
                "</td></tr><tr><td align='left' colspan='4'>DIREC.: " +
                datos[0]["cli_direccion"] +
                "</td></tr><tr><td colspan='4'>====</td></tr><tr><td align='center'><b>CANT.</b></td><td align='center'><b>DESCRIPCION</b></td><td align='center'><b>P. UNIT</b></td><td align='center'><b>VALOR</b></td></tr>";
            send = { visorDetalleFactura: 1 };
            send.cfac_id = cfac_id;
            $.getJSON("config_facturacion.php", send, function (datos) {
                if (datos.str > 0) {
                    for (i = 0; i < datos.str; i++) {
                        html =
                            html +
                            "<tr><td align='center'>" +
                            datos[i]["dtfac_cantidad"] +
                            "</td><td>" +
                            datos[i]["plu_descripcion"] +
                            "</td><td align='center'>" +
                            datos[i]["dtfac_precio_unitario"] +
                            "</td><td align='center'>" +
                            datos[i]["dtfac_total"] +
                            "</td></tr>";
                    }
                    send = { "totalDetalleFactura": 1 };
                    send.cfac_id = cfac_id;
                    $.getJSON("config_facturacion.php", send, function (datos) {
                        if (datos.str > 0) {
                            html =
                                html +
                                "<tr><td align='center' colspan='4'>====</td></tr><tr><td align='right' colspan='2'>SUBTOTAL.....$</td><td align='right' colspan='2'>" +
                                datos[0]["cfac_subtotal"] +
                                "</td></tr><tr><td align='right' colspan='4'>======</td></tr><tr><td align='right' colspan='2'>BASE 12%.....$</td><td align='right' colspan='2'>" +
                                datos[0]["cfac_base_iva"] +
                                "</td></tr><tr><td align='right' colspan='2'>BASE 0%.....$</td><td align='right' colspan='2'>" +
                                datos[0]["cfac_base_cero"] +
                                "</td></tr><tr><td align='right' colspan='2'>I.V.A.12%.....$</td><td align='right' colspan='2'>" +
                                datos[0]["cfac_iva"] +
                                "</td></tr><tr><td align='right' colspan='4'>======</td></tr><tr><td align='right' colspan='2'><b>TOTAL.....$:</b></td><td align='right' colspan='2'><b>" +
                                datos[0]["cfac_total"] +
                                "</b></td></tr>";
                            send = { formasPagoDetalleFactura: 1 };
                            send.cfac_id = cfac_id;
                            $.getJSON("config_facturacion.php", send, function (datos) {
                                if (datos.str > 0) {
                                    for (i = 0; i < datos.str; i++) {
                                        html =
                                            html +
                                            "<tr><td align='right' colspan='2'>" +
                                            datos[i]["fmp_descripcion"] +
                                            "</td><td align='right' colspan='2'>$" +
                                            datos[i]["cfac_total"] +
                                            "</td></tr>";
                                    }
                                    html = html + "</table></div>";
                                    $("#cabecerafactura").html(html);
                                }
                            });
                        }
                    });
                }
            });
        }
        $("#detalleFactura").shortscroll();
    });
}

function fn_visualizarFactura() {
    $("#listaFactura").empty();
    fn_actualizarFactura();
    //$('#detalleFactura').shortscroll();
    var Cod_Movimiento = $("#txtNumFactura").val(); //$('#listadoPedido').find("li.focus").attr("id"); // delet.
    $("#cabecerafactura").html("");
    $("#visorFacturas").css("display", "block");
    $("#detalleFactura").css("display", "block");
    if (Cod_Movimiento.substring(4, 5) == "F") {
        fn_cargarFactura(Cod_Movimiento);

    }
}

function fn_cerrarVisorFacturas() {
    $("#visorFacturas").css("display", "none");
    $("#detalleFactura").css("display", "none");
    fn_crearFacturaXml();
    fn_validaItemPagado();
}

function fn_limpiarCalculadora() {
    $(".desabilitado").removeAttr("Disabled");
    $("#pagado").val("");
    coma = 0;
}

function fn_desplegarMenu() {
    $("#rdn_pdd_brr_ccns").css("display", "block");
    $("#cnt_mn_dsplgbl_pcns_drch").css("display", "block");
}

function fn_validaAdmin() {
    if ($("#usr_claveAdmin").val() == "") {
        $("#usr_claveAdmin").focus();
        alertify.alert("Ingrese una clave.");
        return false;
    }
    
    send = { "validarUsuarioAdministrador": 1 };
    send.usr_Admin = $("#usr_claveAdmin").val();
    send.facturaAuditoria = $("#txtNumFactura").val();
    $.getJSON("config_facturacion.php", send, function (datos) {
        $("#usr_claveAdmin").val("");
        if (datos.admini == 1) {
            if (lc_banderaOkAdmin == "salirSistema") {
                $("#credencialesAdmin").dialog("close");
                window.location.replace("../index.php"); //BOTON OK para salir del sistema
            } else if (lc_banderaOkAdmin == "otro") {
                $("#credencialesAdmin").dialog("close");
                send = { "activaOpcionCobroTarjeta": 1 };
                send.opcionSwt = lc_tipoEnvio;
                send.opcion = "A";
                $.getJSON("config_facturacion.php", send, function (datos) {
                    $("#modalsubSWT").dialog("close");
                    $("#modalSWT").dialog("close");
                    $(".jb-shortscroll-wrapper").show();
                    fn_modalBinDatafast();
                });
            } else if (lc_banderaOkAdmin == "aplicaPago") {
                lc_banderaAplicapago = 1;
                $("#credencialesAdmin").dialog("close");
                $(".jb-shortscroll-wrapper").show();
                //$(".desabilitado").removeAttr("Disabled");
                //$("#visualizarFactura").hide();	
                $("#hid_bandera_cvv").val(1);
                fn_aplicaPagoSegunTipo();
            } //fin bandera aplica pago
            else if (lc_banderaOkAdmin == "banda") {
                send = { "activaOpcionCobroTarjeta": 1 };
                send.opcionSwt = lc_tipoEnvio;
                send.opcion = "A";
                $.getJSON("config_facturacion.php", send, function (datos) {
                    $("#credencialesAdmin").dialog("close");
                  
                    fn_validaConfiguracionBinTarjetaLectura();
                });
            } else if (lc_banderaOkAdmin == "pinpad") {
                $("#credencialesAdmin").dialog("close");
                send = { "activaOpcionCobroTarjeta": 1 };
                send.opcionSwt = lc_tipoEnvio;
                send.opcion = "A";
                $.getJSON("config_facturacion.php", send, function (datos) {
                    valordelaTransaccionPinPad = parseFloat($("#pagoTotal").val());
                    send = { "insertarRequerimientoAutorizacion": 1 };
                    send.cfac = $("#txtNumFactura").val();
                    send.formaPagoID = $("#btnFormaPagoId").val();
                    send.valorTransaccion = parseFloat($("#pagoTotal").val());
                    if ($("#cantidad").val() == "") {
                        send.prop_valor = 0;
                    } else {
                        send.prop_valor = parseFloat($("#cantidad").val());
                    }
                    if (banderaa == 1) {
                        send.valorTransaccionPinPad = $("#pagado").val();
                    } else {
                        if (valordelaTransaccionPinPad != 0) {
                            send.valorTransaccionPinPad = $("#pagado").val();
                        } else {
                            send.valorTransaccionPinPad = $("#pagoGranTotal").val();
                        }
                    }
                    send.tipoEnvioPinPad = lc_tipoEnvio;
                    send.tipoTransaccionPinpad = "01";
                    $.getJSON("config_facturacion.php", send,
                        function (datos) {
                            lc_control = 0;
                            cargando(0);
                            fn_timeout();
                            fn_esperaRespuesta();
                        }
                    );
                });
            }
        } else {
            alertify.alert("Clave incorrecta.");
            $("#usr_claveAdmin").val("");
            return false;
        }
    });

}

function fn_modalBinDatafast() {
    $("#modal_binDatafast").dialog({
        modal: true,
        resizable: false,
        closeOnEscape: false,
        position: "center",
        draggable: false,
        width: 500,
        heigth: 500,
        opacity: 0,
        show: "none",
        hide: "none",
        duration: 500,
        buttons: {
            Aceptar: function () {
                fn_validarBinTarjetaDatafast();
            },
            Cancelar: function () {
                $(this).dialog("close");
                $(".jb-shortscroll-wrapper").show();
                $("#pagado").val("");
                $("#txt_bin").val("");
            }
        }
    });
}

function fn_validarBinTarjetaDatafast() {
    if ($("#txt_bin").val().length == 6) {
        send = { "validaFormaBinDatafast": 1 };
        send.binTarjetaDatafast = $("#txt_bin").val();
        $.getJSON("config_facturacion.php", send, function (datos) {
            $("#modal_binDatafast").dialog("close");
            $(".jb-shortscroll-wrapper").show();
            $("#txt_bin").val("");
            if (datos.confirma == 1) {
                fn_insertaFormaPagoTarjetaDatafast(datos.idFormaPago);
            } else {
                $("#pagado").val("");
                alertify.error(datos.mensaje);
                return false;
            }

        });
    } else {
        alertify.error("Bin Incorrecto. Ingrese Nuevamente.");
        $("#txt_bin").val("");
        return false;
    }
}

function fn_insertaFormaPagoTarjetaDatafast(idFormaPago) {
    var aPagar = parseFloat($("#pagoTotal").val());
    if ($("#pagado").val() == "" || $("#pagado").val() == 0) {
        pagado = $("#pagoTotal").val();
        total = $("#pagoTotal").val();
        $("#pagado").val(total);
    }
    var pagado = parseFloat($("#pagado").val());
    var valor = aPagar - pagado;
    $("#hid_cambio").val(valor);
    var btnNombre = $("#btnAplicarPago").attr("title");
    $("#pagoTotal").val(valor.toFixed(2));

    send = { "ingresaFormaPagoTarjeta": 1 };
    send.codFactTarjeta = $("#txtNumFactura").val();
    send.fmpIdTarjeta = idFormaPago; //$("#btnFormaPagoId").val();
    send.fmpNumSegtar = 0;
    if ($("#pagoTotal").val() >= $("#pagado").val() || $("#pagoTotal").val() == $("#pagado").val()) {
        send.frmPagoTotalTarjeta = $("#pagado").val();
        send.fctTotalTarjeta = $("#pagado").val();
    } else {
        send.frmPagoTotalTarjeta = $("#pagado").val();
        send.fctTotalTarjeta = $("#pagado").val();
    }
    send.SwtTipo = 3; //lc_tipoEnvio;
    if ($("#cantidad").val() == "") {
        send.propina_valor = 0;
    } else {
        send.propina_valor = parseFloat($("#cantidad").val());
    }
    $.getJSON("config_facturacion.php", send, function (datos) {
        lc_control = 0;
        var btnNombre = $("#btnAplicarPago").attr("title");
        $("#pagado").val("");
        if (valor <= 0 && btnNombre != "EFECTIVO") {
            fn_buscaPagoCredito(); // fn_envioFactura();
        }
        var html = "";
        if (datos.str > 0) {
            $("#btnCancelarPago").attr("disabled", false);
            for (i = 0; i < datos.str; i++) {
                html += "<tr>";
                html +=
                    "<td width='130px' align='right'>" +
                    datos[i]["fmp_descripcion"] +
                    "</td><td width='40px' align='center'>$</td>";
                html +=
                    "<td width='100px' align='center'>" +
                    parseFloat(datos[i]["fpf_total_pagar"]).toFixed(2) +
                    "</td>";
                html += "</tr>";
            }
            $("#formasPago2").html(html);
        } else {
            $("#btnCancelarPago").attr("disabled", true);
            alertify.alert("Error al registrar la factura");
        }
    });
}

function fn_cerrarValidaAdmin() {
    $(".jb-shortscroll-wrapper").show();
    $("#credencialesAdmin").dialog("close");
    $("#usr_claveAdmin").val("");
    $("#pagado").val("");
    $("#modalSWT").dialog("close");
    $("#modalsubSWT").dialog("close");
}

function fn_sumarBillete() {
    $(".desabilitado").removeAttr("Disabled");
    sumar = $("#can_sumar").val();
    sumar = sumar + sumar;
}

function fn_cargaDivBilletes() {
    if ($("#hidVitality").val() == 1) {
        $("#izquierdo").hide();
    } else {
        simboloMon = $("#simMoneda").val();
        var send = { cargaBilletes: 1 };
        var es_menu_agregador = localStorage.getItem("es_menu_agregador");

        send.es_menu_agregador = es_menu_agregador;
        $.getJSON("config_facturacion.php", send, function(datos) {
        if (datos.str > 0) {
            $("#lista_billetes").empty();
                for (var i = 0; i < datos.str; i++) {
                    html =
                        "<button class='btnDinero desabilitado' id='btn" +
                        i +
                        "' title='Billete 1' onclick='fn_billete(" +
                        datos[i]["btd_Valor"] +
                        ',"' +
                        datos[i]["descFp"] +
                        '","' +
                        datos[i]["idFp"] +
                        '","' +
                        datos[i]["tfpId"] +
                        '","' +
                        datos[i]["descTfp"] +
                        '",' +
                        datos[i]["autoriza"] +
                        ")'><b>" +
                        simboloMon +
                        " " +
                        "" +
                        datos[i]["btd_Valor"] +
                        "</b></button>";
                    $("#lista_billetes").append(html);
                }
            }
        });
        $("#izquierdo").show();
    }
}

function fn_cargaTeclasEmail() {
    send = { "cargaTeclasEmail": 1 };
    $.getJSON("config_facturacion.php", send, function (datos) {
        if (datos.str > 0) {
            $("#dominio1").empty();
            $("#dominio2").empty();
            for (var i = 0; i < datos.str; i++) {
                html =
                    "<button class='btnVirtualDominio' style='font-size: 13px;' onclick='fn_agregarCaracterCorreo(txtCorreo,\"" +
                    datos[i]["descripcionEmail"] +
                    "\")'>" +
                    datos[i]["descripcionEmail"] +
                    "</button><br/>";
                if (i < 5) {
                    $("#dominio1").append(html);
                } else {
                    $("#dominio2").append(html);
                }
            }
        }
    });
}

function fn_abreModalCliente() {
    cerrarModalAgegadores();

    $(".jb-shortscroll-wrapper").hide();
    $("#usr_claveAdmin").val("");
    $("#credencialesAdmin").show();
    $("#credencialesAdmin").dialog({
        modal: true,
        draggable: false,
        width: 500,
        heigth: 500,
        resizable: false,
        opacity: 0,
        show: "none",
        hide: "none",
        duration: 500
    });
}

function fn_inicioTomaPedido() {
    var pantalla = "tomaPedido";
    send = { "obtenerMesa": 1 };
    send.rst_id = $("#idRest").val();
    $.getJSON("config_facturacion.php", send, function (datos) {
        if (datos.str > 0) {
            var mesa = datos["mesa_asignada"];
            window.location.replace("../ordenpedido/" + pantalla + ".php?numMesa=" + mesa);
        } else {
            window.location.replace("../ordenpedido/" + pantalla + ".php");
        }
    });
}

function fn_inicio() {
    var pantalla = "tomaPedido";
    send = { "obtenerMesa": 1 };
    send.rst_id = $("#idRest").val();
    $.getJSON("config_facturacion.php", send, function (datos) {
        pantalla = "tomaPedido";

        if (datos.str > 0) {
            var mesa = datos["mesa_asignada"];
            window.location.replace("../ordenpedido/" + pantalla + ".php?numMesa=" + mesa);
        } else {
            window.location.replace("../ordenpedido/" + pantalla + ".php");
        }
    });
}

//FUNCION OBTENER NUMERO DE MESA
function fn_obtenerMesa() {
    let pantalla = "tomaPedido";
    let send = { "obtenerMesa": 1 };
    send.rst_id = $("#idRest").val();
    send.odp_id = $("#txtOrdenPedidoId").val();
    $.getJSON("config_facturacion.php", send, function (datos) {
        pantalla = (datos["fidelizacion_Activa"] === 1) ? "tomaPedido" : "tomaPedido";
		let parametros = "";
        if (datos["fidelizacion_Activa"] === 1) {
			parametros = "numFactura=" + $("#txtNumFactura").val() + "&rst_id=" + $("#txtRestaurante").val() + "&cdn_id=" + $("#txtCadenaId").val() + "&op_id=" + $("#txtOrdenPedidoId").val() + "&tipo_s=" + $("#txtTipoServicio").val();
        }
        if (datos.str > 0) {
            let mesa = datos["mesa_asignada"];
            if (datos["esDivisionCuenta"] === 1) {
                let odp_id = $("#txtOrdenPedidoId").val();
                let mesa_id = $("#txtNumMesa").val();
                let imp = $("#hide_cdn_tipoimpuesto").val();
                let est_ip = $("#txt_est_ip").val();
                let rst_id = $("#txtRestaurante").val();
				let url = "../ordenpedido/separarCuentas.php?odp_id=" + odp_id + "&mesa_id=" + mesa_id + "&cdn_tipoimpuesto=" + imp +
					" &est_ip=" + est_ip + "&cat_id=" + datos["rst_cat"] + "&rst_id=" + rst_id;
                window.location.replace(url);
            } else {
                window.location.replace("../ordenpedido/" + pantalla + ".php?numMesa=" + mesa + "&" + parametros);
            }
        } else {
            window.location.replace("../ordenpedido/" + pantalla + ".php" + parametros);
        }
    });
}

function cambiarVentana() {

}

/*Funcion para ingresar caracteres del Bin para pagos con tarjeta de datafast*/
function fn_agregarBinTarjeta(valor) {
    if ($("#txt_bin").val().length < 6) {
        lc_caracter = document.getElementById("txt_bin").value;
        $("#txt_bin").val("");
        lc_caracter = lc_caracter + valor;
        document.getElementById("txt_bin").value = lc_caracter;
    }
}

/*Funcion para borrar caracteres del Bin para pagos con tarjeta de datafast*/
function fn_eliminarBin() {
    $("#txt_bin").val("");
}

/*Funcion para eliminar digito a digito el bin de la tarjeta*/
function fn_eliminarDigitoBin() {
    var lc_digito = $("#txt_bin").val();
    lc_digito = lc_digito.substring(0, lc_digito.length - 1);
    $("#txt_bin").val(lc_digito);
}


///////////////////////////////////////////////FUNCION PARA IMAGEN DE CARGANDO////////////////////////////////////////////////////////////
function fn_cargando(std) {
    if (std > 0) {
        $("#mdl_rdn_pdd_crgnd").show();
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
    }
}


//////////////LISTA LOS DESCUENTOS PARA INGRESAR////////////////////////////////////////////
function fn_listaDescuentos() {
    $("#descuentosContenedor").show();
    fn_cargando(1);
    $("#descuentosContenedor").dialog({
        modal: true,
        position: "center",
        closeOnEscape: false,
        width: 500,
        height: 465,
        draggable: false,
        resizable: false,
        open: function () {
            $(".ui-dialog-titlebar").show();
        }
    });
    $(".ui-dialog-titlebar").empty();
    $(".ui-dialog-titlebar").css({
        background: "#0E98B6",
        color: "#FFFFFF",
        "text-align": "center"
    });
    $(".ui-dialog-titlebar").append("Seleccione descuento que desea aplicar");
    var send = { consultaDescuentos: 1 };
    send.cfac_id = $("#txtNumFactura").val();
    $.ajax({
        async: false,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {
                $(".descuentosLabel ul").empty();
                for (var i = 0; i < datos.str; i++) {
                    if (datos[i]["tipoDescuento"] == "% Porcentaje") {
                        html =
                            "<li id='" +
                            datos[i]["iddescuentos"] +
                            "' style='background:#E8B80C; font-weight:bold;' onclick='fn_modificarLista(\"" +
                            datos[i]["iddescuentos"] +
                            "\")' >" +
                            datos[i]["apld_descripcion"] +
                            "</li>";
                        $("#listadescuento").append(html);
                    } else {
                        html =
                            "<li id='" +
                            datos[i]["iddescuentos"] +
                            "' style='background:#4FB6D8; font-weight:bold;' onclick='fn_modificarLista(\"" +
                            datos[i]["iddescuentos"] +
                            "\")' >" +
                            datos[i]["apld_descripcion"] +
                            "</li>";
                        $("#listadescuento").append(html);
                    }
                }
            } else {
                $("#listadescuento").html("");
                alertify.alert("No existen descuentos registrados");
            }
        }
    });
    fn_cargando(0);
}


///////////////////////////////////////////CIERRA LOS MODALES LISTA_DESCUENTOS, CREDENCIALES_ADMINISTRADOR//////////////////////////////
function fn_cerrarDialogoDescuentosContenedor() {
    $("#usr_clave1").val("");
    $("#descuentosContenedor").dialog("close");
    $("#descuentosDiscrecionalesContenedor").dialog("close");
    $("#credencialesContenedor").dialog("close");
}

///////////////////////////////////////MODIFICA EL ESTADO DE LA LISTA AL SELECCIONAR EL DESCUENTO////////////////////////////////
function fn_modificarLista(codigo) {
    $("#listadescuento li.focus").css({ "font-size": "16px" });
    $("#listadescuento")
        .find(".focus")
        .removeClass("focus");
    $("#" + codigo).css({ "font-size": "22px" });
    $("#" + codigo).addClass("focus");
}


///////////////////////////////////////////////AGREGA DESCUENTO DE MANERA MANUAL////////////////////////////////////////////////
function fn_agregarDescuento() {
    if (!$("li.focus").length) {
        alertify.error("Seleccione un descuento.");
    } else {
        $("#descuentosContenedor").dialog("close");
        //var valoresDescuentos = "";
        send = { "agregarDescuento": 1 };
        send.cfac_id = $("#txtNumFactura").val();
        var desc_id = $("#listadescuento")
            .find("li.focus")
            .attr("id");
        send.desc_id = desc_id;
        $.ajax({
            async: false,
            url: "config_facturacion.php",
            data: send,
            dataType: "json",
            success: function (datos) {
                if (datos.str > 0) {
                    if (datos[0]["desc_estado"] > 0) {
                        fn_cargando(1);

                        //ACTUALIZO LOS VALORES CON EL DESCUENTO
                        $("#listaFactura").empty();
                        for (var i = 0; i < datos.str; i++) {
                            if (datos[i]["desc_descuento"] > 0) {
                                var porcentajeDesc = datos[i]["porcentaje"];

                                if (porcentajeDesc != 0) {
                                    html = "<li id='item1' style='background:#7FCF83;'><div class='listaproductosCant'>";
                                } else {
                                    html = "<li id='item1' style='background:#4FB6D8;'><div class='listaproductosCant'>";
                                }

                                if (datos[i]["totalizado"] != 0) {
                                    html += "" + parseFloat(datos[i]["desc_cantidad"]) + "";
                                } else {
                                    html += "  &nbsp;&nbsp;";
                                }

                                html += "</div>";
                                html +=
                                    "<div class='listaproductosDesc'>" +
                                    datos[i]["desc_descripcion"] +
                                    "</div>";
                                html += "<div class='listaproductosVal'>";

                                if (datos[i]["totalizado"] != 0) {
                                    html +=
                                        "" +
                                        moneda +
                                        " " +
                                        parseFloat(datos[i]["totalizado"]).toFixed(2) +
                                        "";
                                } else {
                                    html += "";
                                }

                                html += "</div></li><br/>";

                                if (porcentajeDesc != 0) {
                                    html += "<li id='item1' style='background:#7FCF83; font-size:13px; height:18px;'><div class='listaproductosCant'>";
                                } else {
                                    html += "<li id='item1' style='background:#4FB6D8;; font-size:13px; height:18px;'><div class='listaproductosCant'>";
                                }

                                html += "&ensp;&ensp;";
                                html += "</div>";

                                if (porcentajeDesc != 0) {
                                    html += "<div class='listaproductosDesc' style='text-align: right;'>Descuento: " + porcentajeDesc + "%</div>";
                                } else {
                                    html += "<div class='listaproductosDesc' style='text-align: right;'>Descuento:</div>";
                                }

                                html += "<div class='listaproductosVal'>";
                                html +=
                                    "" +
                                    moneda +
                                    " " +
                                    parseFloat(datos[i]["desc_descuento"]).toFixed(2) +
                                    "";
                                html += "</div></li><br/>";
                            } else {
                                html = "<li id='item1' style='background:white;'><div class='listaproductosCant'>";
                                if (datos[i]["totalizado"] != 0) {
                                    html += "" + parseFloat(datos[i]["desc_cantidad"]) + "";
                                } else {
                                    html += "  &nbsp;&nbsp;";
                                }
                                html += "</div>";
                                html +=
                                    "<div class='listaproductosDesc'>" +
                                    datos[i]["desc_descripcion"] +
                                    "</div>";
                                html += "<div class='listaproductosVal'>";
                                if (datos[i]["totalizado"] != 0) {
                                    html +=
                                        "" +
                                        moneda +
                                        " " +
                                        parseFloat(datos[i]["totalizado"]).toFixed(2) +
                                        "";
                                } else {
                                    html += "";
                                }
                                html += "</div></li><br/>";
                            }
                            $("#listaFactura").append(html);
                        }
                        lc_cantidadPagada = parseFloat(datos[0]["desc_valor"]).toFixed(2);
                        fn_listaTotales();
                        fn_cargando(0);
                    } else {
                        alertify.alert(datos[0]["desc_mensaje"]);
                    }
                }
            }
        });
    }
}


///////////////////////////////////////////////ELIMINA LOS DESCUENTOS AGREGADOS ////////////////////////////////////////////////
function fn_eliminarDescuento() {
    var idOrdenPedido = $("#txtOrdenPedidoId").val();
    var idFactura = $("#txtNumFactura").val();
    var idMesa = $("#txtNumMesa").val();
    var idDopCuenta = $("#txtNumCuenta").val();
    send = { "eliminarDescuento": 1 };
    send.cfac_id = idFactura;
    send.idCabeceraOrdenPedido = idOrdenPedido;
    $.ajax({
        async: false,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {
                if (datos[0]["elim_estado"] > 0) {
                    //var odp_id = document.getElementById("hide_odp_id").value;
                    //var mesa_id = document.getElementById("hide_mesa_id").value;
                    $("#formCobrar").html(
                        '<form action="../facturacion/factura.php" name="cobro" method="post" style="display:none;"><input type="text" name="odp_id" value="' +
                        idOrdenPedido +
                        '" /><input type="text" name="dop_cuenta" value="' +
                        idDopCuenta +
                        '" /><input type="text" name="mesa_id" value="' +
                        idMesa +
                        '" /></form>'
                    );
                    document.forms["cobro"].submit();
                    //lc_cantidadPagada = (parseFloat(datos[0]['elim_valor']).toFixed(2));
                    //fn_listaTotales();
                    //detallesDescuentosDiscrecionales();
                } else {
                    alertify.alert(datos[0]["elim_mensaje"]);
                }
            }

        }
    });
}

///////////////////////////////VALIDA LAS CREDENCIALES DE ADMINISTRADOR PARA APLICAR DESCUENTO MANUAL/////////////////////////////////
function fn_validar_usuario(num) {
    var opcion = num;
    var usr_clave = $("#usr_clave1").val();
    if (usr_clave != "") {
        send = { "validarUsuarioDescuentos": 1 };
        send.usr_clave = usr_clave;
        $.ajax({
            async: false,
            url: "config_facturacion.php",
            data: send,
            dataType: "json",
            success: function (datos) {
                if (datos.str > 0) {
                    $("#anulacionesContenedor").dialog("close");
                    $("#usr_clave1").val("");
                    $("#credencialesContenedor").dialog("close");
                    //opciones para el cuadro de credenciales
                    if (opcion == 1) {
                        fn_listaDescuentos();
                    }
                    if (opcion == 2) {
                        fn_eliminarDescuento();
                    }
                    if (opcion == 3) {
                        //guardarDescuentosDiscrecionales();
                        fn_listaDescuentosDiscrecionales();
                    }

                } else {
                    alertify.alert("No tienes permiso de administrador", function (e) {
                        if (e) {
                            alertify.set({ buttonFocus: "none" });
                            $("#usr_clave1").focus();
                        }
                    });
                    $("#usr_clave1").val("");
                }
            }
        });
    } else {
        alertify.alert("Por favor ingrese una clave de administrador.", function (e) {
            if (e) {
                alertify.set({ buttonFocus: "none" });
                $("#usr_clave1").focus();
            }
        });
        $("#usr_clave1").val("");
    }
}

///////////////////////////// Modal credenciales de administrador /////////////////////////////////////
function fn_dialogCredenciales1(num) {
    var opcion = num;
    $("#credencialesContenedor").show();
    $("#credencialesContenedor").dialog({
        modal: true,
        position: "center",
        closeOnEscape: false,
        width: 500,
        height: 440,
        draggable: false,
        resizable: false,
        open: function () {
            $(".ui-dialog-titlebar").hide();
            fn_numericoCredenciales("#usr_clave1", opcion);
        }
    });
}

///////////////////////////////////////HABILITA LOS BOTONES DESCUENTOS-ELIMINAR DESCUENTOS////////////////////////////////////////
function fn_cargarAccesosSistema() {
    var tipoS = $("#txtTipoServicio").val();
    send = { "cargarAccesosPerfil": 1 };
    send.pnt_id = "facturacion.php";
    $.ajax({
        async: false,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    switch (datos[i]["acc_descripcion"]) {
                        case "Descuentos":
                            $("#btn_descuentos").attr("disabled", false);
                            $("#btn_descuentos").removeClass("boton_Opcion_Bloqueado");
                            $("#btn_descuentos").addClass("boton_Opcion");
                            break;
                        case "Eliminar Descuentos":
                            $("#btn_eliminar_descuentos").attr("disabled", false);
                            $("#btn_eliminar_descuentos").removeClass(
                                "boton_Opcion_Bloqueado"
                            );
                            $("#btn_eliminar_descuentos").addClass("boton_Opcion");
                            break;
                        case "Descuento Discrecional":
                            $("#btnDescuentoDiscrecional").attr("disabled", false);
                            $("#btnDescuentoDiscrecional").removeClass(
                                "boton_Opcion_Bloqueado"
                            );
                            $("#btnDescuentoDiscrecional").addClass("boton_Opcion");
                            break;
                        case "Imprimir Pre-Cuenta":
                            if (
                                $("#btn_imprimir").attr("servicio") === tipoS ||
                                $("#btn_imprimir").attr("servicio") === undefined
                            ) {
                                $("#btn_imprimir").attr("disabled", false);
                                $("#btn_imprimir").removeClass("boton_Accion_Bloqueado");
                                $("#btn_imprimir").addClass("boton_Accion");
                            }
                            break;
                        case "Salir":
                            $("#regresar").attr("disabled", false);
                            $("#regresar").removeClass("boton_Opcion_Bloqueado");
                            $("#regresar").addClass("boton_Opcion");
                            break;
                    }
                }
            }
        }
    });
}

/////////////////////////MODAL PARA DESCUENTOS DISCRECIONALES///////////////
function fn_modalDescuentoDiscrecionales() {
    $("#descuentosDiscrecionalesContenedor").show();
    $("#descuentosDiscrecionalesContenedor").dialog({
        modal: true,
        position: "center",
        closeOnEscape: false,
        width: 650,
        height: 465,
        draggable: false,
        resizable: false,
        open: function () {
            $(".ui-dialog-titlebar").show();
        }
    });
    $(".ui-dialog-titlebar").empty();
    $(".ui-dialog-titlebar").css({
        background: "#0E98B6",
        color: "#FFFFFF",
        "text-align": "center"
    });
    $(".ui-dialog-titlebar").append("Aplique los descuentos");
}

/////////////////////////////LISTA TODOS LOS PRODUCTOS QUE SE PUEDE APLICAR DSCT DISCRECIONAL//////////////////
function fn_listaDescuentosDiscrecionales() {
    fn_modalDescuentoDiscrecionales();
    var idFactura = $("#txtNumFactura").val();
    send = { "descuentosDiscrecionales": 1 };
    send.accion = 1;
    send.cfac_id = idFactura;
    $.ajax({
        async: false,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {
                html = "";
                $("#listaDiscrecionales").empty();
                for (i = 0; i < datos.str; i++) {
                    //html = "<tr id='listaDescuentosDiscrecionales'><td id='descripcionProductos' width='440px'>" + datos[i]['descripcion'] + "</td><td id='valorDescuentoDiscrecional' align='center'><select><option>10%</option><option>15%</option></select></td></tr>"
                    html = "<div class='productoDiscrecional'>";
                    html +=
                        "<input type='hidden' id='" +
                        datos[i]["plu_id"] +
                        "' class='idDiscrecional'/>";
                    html +=
                        "<div class='descripcionProducto' style='width: 55%; float: left; height:50px; padding-top: 18px;'>" +
                        datos[i]["descripcion"] +
                        "</div>";
                    html += "<div class='valorDescuentoDiscrecional' style='width: 40%!important; float: left;'><span>DSCT %: </span>";
                    html += listaPorcentajesDiscrecionales();
                    html += "</div>";
                    html += "</div>";
                    $("#listaDiscrecionales").append(html);
                }
            } else {
                $("#listadescuento").html("");
                alertify.alert("No existen descuentos registrados");
                $("#descuentosDiscrecionalesContenedor").dialog("close");
            }
        }
    });
}

//GUARDANDO DESCUENTOS DISCRECIONALES
function guardarDescuentosDiscrecionales() {
    $("#descuentosDiscrecionalesContenedor").dialog("close");
    var cadenaProductosDiscrecionales = crearStringproductosDiscrecionalesSeleccionados;
    var idFactura = $("#txtNumFactura").val();
    send = { "guardarDescuentosDiscrecionales": 1 };
    send.cfac_id = idFactura;
    send.cadenaProductosDiscrecionales = cadenaProductosDiscrecionales;
    $.ajax({
        async: false,
        type: "POST",
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {
                if (datos[0]["discrecional_estado"] > 0) {
                    lc_cantidadPagada = parseFloat(
                        datos[0]["discrecional_valor"]
                    ).toFixed(2);
                    fn_listaTotales();
                    detallesDescuentosDiscrecionales();
                } else {
                    alertify.alert(datos[0]["discrecional_mensaje"]);
                }

            }

        }
    });
}

//AGREGANDO CARACTERES PARA PODER ENCIAR CADENA A LA BDD
function crearStringproductosDiscrecionalesSeleccionados() {
    var arrayProductos = productosDiscrecionalesSeleccionados();
    var arrayStrings = [];
    arrayProductos.forEach(function (element) {
        arrayStrings.push(element.idDiscrecioinal + "_" + element.descrip_pro + "_" + element.valorDiscrecreacinal);
    });
    return arrayStrings.join("_") + "_";
}

//OBTENIENDO LOS VALORES DE DESCUENTO POR PRODUCTO         
function productosDiscrecionalesSeleccionados() {
    var productosDiscrecionales = [];
    //var restaurantesFechas = [];
    $(".productoDiscrecional").each(function (index) {
        $this = $(this);
        var idDiscrecional = $this.find(".idDiscrecional").attr("id");
        var descripcionProducto = $this.find(".descripcionProducto").text();
        var valorDescuentoDiscrecional = $this.find("#valorDiscrecional").val();
        var listaDescuentos = {
            idDiscrecioinal: idDiscrecional,
            descrip_pro: descripcionProducto,
            valorDiscrecreacinal: valorDescuentoDiscrecional
        };
        productosDiscrecionales.push(listaDescuentos);
    });
    return productosDiscrecionales;
}

//LISTA PORCENTAJES DE DESCUENTO DISCRECIONAL
function listaPorcentajesDiscrecionales() {
    var optionHtml = "";
    var idFactura = $("#txtNumFactura").val();
    send = { "listaPorcentajesDiscrecionales": 1 };
    send.accion = 2;
    send.cfac_id = idFactura;
    $.ajax({
        async: false,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {
                optionHtml = "<select id='valorDiscrecional' class='cntSelectMovimiento select' style='width: auto!important;'>";
                optionHtml += "<option value='0' selected >0</option>";
                for (var j = 0; j < datos.str; j++) {
                    optionHtml +=
                        "<option value='" +
                        datos[j]["valorDiscrecional"] +
                        "'>" +
                        datos[j]["valorDiscrecional"] * 100 +
                        "</option>";
                }
                optionHtml += "</select>";
            } else {
                optionHtml = "<select id='valorDiscrecional' class='cntSelectMovimiento select' style='width: auto!important;'>";
                optionHtml += "<option value='0'>0</option>";
                optionHtml += "</select>";
            }
        }
    });
    return optionHtml;
}

//LISTA LOS PRODUCTOS CON LOS DETALLES DEL DESCUENTO DISCRECIONAL
function detallesDescuentosDiscrecionales() {
    var idFactura = $("#txtNumFactura").val();
    send = { "detallesDescuentosDiscrecionales": 1 };
    send.cfac_id = idFactura;
    $.ajax({
        async: false,
        url: "config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {

                $("#listaFactura").empty();
                for (var i = 0; i < datos.str; i++) {
                    if (datos[i]["porcentajeDiscrecional"] > 0) {
                        html =
                            "<li id='item1' style='background:#E8D81C;'><div class='listaproductosCant'>";

                        if (datos[i]["totalizado"] != 0) {
                            html += "" + datos[i]["dtfac_cantidad"] + "";
                        } else {
                            html += "  &nbsp;&nbsp;";
                        }

                        html += "</div>";
                        html +=
                            "<div class='listaproductosDesc'>" +
                            datos[i]["plu_descripcion"] +
                            "</div>";
                        totalItem =
                            datos[i]["dtfac_precio_unitario"] * datos[i]["dtfac_cantidad"];
                        html += "<div class='listaproductosVal'>";

                        if (datos[i]["totalizado"] != 0) {
                            html +=
                                "" +
                                moneda +
                                " " +
                                parseFloat(datos[i]["totalizado"]).toFixed(2) +
                                "";
                        } else {
                            html += "";
                        }

                        html += "</div></li><br/>";
                        html += "<li id='item1' style='background:#E8D81C; font-size:13px; height:18px;'><div class='listaproductosCant'>";
                        html += "&ensp;&ensp;";
                        html += "</div>";
                        html +=
                            "<div class='listaproductosDesc' style='text-align: right;'>Descuento: " +
                            datos[i]["porcentajeDiscrecional"] +
                            "%</div>";
                        html += "<div class='listaproductosVal'>";
                        html +=
                            "" +
                            moneda +
                            " " +
                            parseFloat(datos[i]["descuento"]).toFixed(2) +
                            "";
                        html += "</div></li><br/>";
                    }

                    if (
                        datos[i]["desc_valorFijo"] > 0 ||
                        datos[i]["desc_porcentaje"] > 0
                    ) {
                        html = "<li id='item1' style='background:#7FCF83;'><div class='listaproductosCant'>";

                        if (datos[i]["totalizado"] != 0) {
                            html += "" + datos[i]["dtfac_cantidad"] + "";
                        } else {
                            html += "  &nbsp;&nbsp;";
                        }

                        html += "</div>";
                        html +=
                            "<div class='listaproductosDesc'>" +
                            datos[i]["plu_descripcion"] +
                            "</div>";
                        totalItem =
                            datos[i]["dtfac_precio_unitario"] * datos[i]["dtfac_cantidad"];
                        html += "<div class='listaproductosVal'>";

                        if (datos[i]["totalizado"] != 0) {
                            html +=
                                "" +
                                moneda +
                                " " +
                                parseFloat(datos[i]["totalizado"]).toFixed(2) +
                                "";
                        } else {
                            html += "";
                        }

                        html += "</div></li><br/>";
                        html += "<li id='item1' style='background:#7FCF83; font-size:13px; height:18px;'><div class='listaproductosCant'>";
                        html += "&ensp;&ensp;";
                        html += "</div>";

                        if (datos[i]["desc_porcentaje"] != 0) {
                            html +=
                                "<div class='listaproductosDesc' style='text-align: right;'>Descuento: " +
                                datos[i]["desc_porcentaje"] +
                                "%</div>";
                        } else {
                            html += "<div class='listaproductosDesc' style='text-align: right;'>Descuento: </div>";
                        }

                        html += "<div class='listaproductosVal'>";
                        html +=
                            "" +
                            moneda +
                            " " +
                            parseFloat(datos[i]["descuento"]).toFixed(2) +
                            "";
                        html += "</div></li><br/>";
                    }

                    if (datos[i]["descuento"] == 0) {
                        html =
                            "<li id='item1' style='background:white;'><div class='listaproductosCant'>";
                        if (datos[i]["totalizado"] != 0) {
                            html += "" + datos[i]["dtfac_cantidad"] + "";
                        } else {
                            html += "  &nbsp;&nbsp;";
                        }
                        html += "</div>";
                        html +=
                            "<div class='listaproductosDesc'>" +
                            datos[i]["plu_descripcion"] +
                            "</div>";
                        totalItem =
                            datos[i]["dtfac_precio_unitario"] * datos[i]["dtfac_cantidad"];
                        html += "<div class='listaproductosVal'>";
                        if (datos[i]["totalizado"] != 0) {
                            html +=
                                "" +
                                moneda +
                                " " +
                                parseFloat(datos[i]["totalizado"]).toFixed(2) +
                                "";
                        } else {
                            html += "";
                        }
                        html += "</div></li><br/>";
                    }

                    //                    cdn_tipoImpuesto = datos[i]['cdn_tipoimpuesto'];
                    //                    lc_cantidadPagada = (parseFloat(datos[i]['dtfac_total']));
                    //                    $("#btnBaseFactura").val(lc_cantidadPagada);
                    $("#listaFactura").append(html);
                }

            }
        }
    });
}

document.onkeydown = checkKeycode;

function checkKeycode(e) {
    var keycode;
    if (window.event) {
        keycode = window.event.keyCode;
    } else if (e) {
        keycode = e.which;
    }
    // Mozilla firefox
    if ($.browser.mozilla) {
        if (keycode === 116 || (e.ctrlKey && keycode === 82)) {
            if (e.preventDefault) {
                e.preventDefault();
                e.stopPropagation();
            }
        }

        // IE
    } else if ($.browser.msie) {
        if (keycode === 116 || (window.event.ctrlKey && keycode === 82)) {
            window.event.returnValue = false;
            window.event.keyCode = 0;
            window.status = "Refresh is disabled";
        }
    }
}

window.oncontextmenu = function () {
    return false;
};

//imprime pre cuenta
//imprime precuenta en el boton imprimi
function fn_imprimirPreCuenta(dop_cuenta) {
    var send;
    var odp_id = $("#txtOrdenPedidoId").val();
    var est_ipd = $("#txt_est_ip").val();
    var usr_id = $("#usr_perfil").val();
    var rst_id = $("#txtRestaurante").val();
    var dop_cuenta = dop_cuenta;

    send = { "impresionPrecuenta": 1 };
    send.odp_id = odp_id;
    send.est_ipd = est_ipd;
    send.usr_id = usr_id;
    send.rst_id = rst_id;
    send.dop_cuenta = dop_cuenta;
    send.opcionImpresion = 0;

    var apiImpresion = getConfiguracionesApiImpresion();

    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
        fn_cargando(1);

        var result = new apiServicioImpresion('impresion_precuenta', send.odp_id, 0, send);
        var imprime = result["imprime"];
        var mensaje = result["mensaje"];

        console.log('imprime: ', imprime);

        if (!imprime) {
            alertify.success("Imprimiendo precuenta #" + dop_cuenta);
            fn_cargando(0);

        } else {
        
            alertify.success('Error al imprimir...');
            fn_cargando(0);

        }

        fn_imprimirOrden(1);

    } else{

        $.getJSON("../ordenpedido/config_ordenPedido.php", send, function (datos) {
            if (datos.str > 0) {
                if (datos[0]["Confirmar"] < 1) {
                    alertify.alert("No existen detalle del pedido");
                }
            }
    
            alertify.success("Imprimiendo precuenta #" + dop_cuenta);
            fn_imprimirOrden(1);
        });

    }

}


function hayPuntosEnPantalla() {
    var respuesta = "no";
    if ($("#listaFactura_canje_puntos").html() === null) {
        respuesta = "no";
    } else {
        respuesta = "si";
    }
    return respuesta;
}

//Consumo de recarga por cliente
var consumirRecargaEfectivo = function () {
    console.log('Entra en consumirRecargaEfectivo');
    procesoFidelizacion = "Recargas";
    var html = "";
    var totalFactura = parseFloat($("#pagoGranTotal").val());
    var faltaPorPagar = parseFloat($("#pagoTotal").val());
    var valor = parseFloat($("#pagado").val());
    //Si el valor a pagar es mayor al faltante por pagar
    if (faltaPorPagar == 0) {
        fn_envioFactura();
        return false;
    } else if (valor > faltaPorPagar) {
        valor = faltaPorPagar;
        $("#pagado").val(valor);
    }
    var send = {};
    send.metodo = "consumoRecargarEfectivoCliente";
    send.idFactura = $("#txtNumFactura").val();
    send.valor = valor;
    send.totalFactura = totalFactura;
    $.ajax({
        async: false,
        type: "POST",
        url: "../recargas/consultasRecargas.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.estado == 1) {                
                $("#tablaFormasPago tr td").each(function () {
                    if (
                        $(this)
                            .find("button")
                            .attr("title") === "CONSUMO RECARGA"
                    ) {
                        $(this).find("button").css("display", "none");
                    }
                });

                $("#btnCancelarPago").attr("disabled", false);
                for (var i = 0; i < datos.str; i++) {
                    html +=
                        "<tr><td width='130px' align='right'>" +
                        datos[i]["descripcion"] +
                        "</td><td width='40px' align='center'>$</td><td width='100px' align='center'>" +
                        parseFloat(datos[i]["total"]).toFixed(2) +
                        "</td></tr>";
                }
                faltaPorPagar = parseFloat(faltaPorPagar - valor).toFixed(2);
                $("#pagado").val("");
                $("#pagoTotal").val(faltaPorPagar);
                $("#hid_cambio").val(0);
                $("#formasPago2").html(html);

                $("#hid_descTipoFp").val("EFECTIVO");
                $("#btnAplicarPago").html("<b>Aplicar <br>EFECTIVO</b>");
                $("#btnAplicarPago").html("<b>Aplicar <br>EFECTIVO</b>");
                $("#btnAplicarPago").attr("title", "EFECTIVO");
                $("#btnCancelarPago").html("<b>Cancelar <br>EFECTIVO</b>");
                $("#btnCancelarPago").attr("title", "EFECTIVO");
                $("#hid_descFp").val("EFECTIVO");
                $("#btnFormaPagoId").val(lc_idefectivo);
                fn_cargando(0);
                if (!parseFloat(faltaPorPagar) > 0) {
                    fn_envioFactura();
                }
            } else {
                fn_cargando(0);
                if (datos.estado === 201) {
                    //Abrir modal para lectura de código
                    $("#inputCodigoSeguridad").val("");
                    $("#cntSeguridadCliente").show();
                    $("#inputCodigoSeguridad").focus();
                } else if (datos.estado === 203) {
                    //Abrir modal para lectura de código y mostrar mensaje
                    alertify.confirm(datos.mensaje, function (e) {
                        if (e) {
                            $("#inputCodigoSeguridad").val("");
                            $("#cntSeguridadCliente").show();
                            $("#inputCodigoSeguridad").focus();
                        }
                    });
                } else {
                    alertify.alert(datos.mensaje);
                }
            }
        },
        error: function () {
            $("#btnCancelarPago").attr("disabled", true);
            fn_cargando(0);
            alertify.alert("Servicio no disponible...");
        }
    });
};

//Cancelar pago con recarga
var cancelarPagoRecargaEfectivo = function () {
    fn_cargando(1);
    //Codigo de la Factura
    var idFactura = $("#txtNumFactura").val();
    var send = {};
    send.metodo = "reversoConsumoRecargarCliente";
    send.idFactura = $("#txtNumFactura").val();
    $.ajax({
        async: false,
        type: "POST",
        url: "../recargas/consultasRecargas.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            procesoFidelizacion = "";
            //Obtiene el total a pagar de la factura
            fn_obtieneTotalApagar();
            total = $("#pagoTotal").val();
            $("#pagado").val("");
            //Resumen de formas de pago aplicadas a la factura
            fn_resumenFormaPago();
            //Otros
            $("#td_cambio").hide();
            $("#td_falta").show();
            alertify.alert("Reverso de consumo exitoso.");
            fn_cargando(0);
        },
        error: function () {
            fn_cargando(0);
        }
    });
};

/* DLL: Plug Them - Encuesta */

/* Aplica Plug Them */
function aplicaPlugThem() {
    var send;
    var aplicaPlugThem = { "aplicaPlugThem": 1 };
    var resultadoObject = new Array();

    send = aplicaPlugThem;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_plugThem.php",
        data: send,
        timeout:4000,

        success: function (datos) {
            if (datos.str > 0) {
                resultadoObject["aplicaCadena"] = datos[0]["aplicaCadena"];
                resultadoObject["aplicaRestaurante"] = datos[0]["aplicaRestaurante"];
            }
        }
    });

    return resultadoObject;
}

/* Login Plug Them */
function validaLoginPlugThem() {
    var resultadoObject = new aplicaPlugThem();

    if (Object.values(resultadoObject).length > 0) {
        var aplicaCadena = resultadoObject["aplicaCadena"];
        var aplicaRestaurante = resultadoObject["aplicaRestaurante"];
        //alert('aplicaCadena: ' + aplicaCadena + ', aplicaRestaurante: ' + aplicaRestaurante);
        if (aplicaCadena === 1 && aplicaRestaurante === 1) {
            tokenLogin();
        } else {
            fn_actualizarFactura(); 

            // fidelizacion: si es si el plan de datos  no imprimir aun (el campo fdznDocumento contendra una cedula cuando se trate de fdzn activa)
            fn_validaItemPagado();
        }
    } else {
        fn_actualizarFactura();

        // fidelizacion: si es si el plan de datos  no imprimir aun (el campo fdznDocumento contendra una cedula cuando se trate de fdzn activa)
        fn_validaItemPagado();
    }
}

function tokenLogin() {
    var send;
    var tokenLogin = { "tokenLogin": 1 };

    send = tokenLogin;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_plugThem.php",
        data: send,
        success: function (datos) {
            if (datos[0]["token_type"] !== "0" && datos[0]["access_token"] !== "0") {
                valorConfiguracionPlugThem(datos[0]["token_type"], datos[0]["access_token"]);
            } else {
                fn_actualizarFactura();

                fn_validaItemPagado();
            }
        }
    });
}

/* Fin Login Plug Them */

/* Aplicar encuesta según la configuración */
function valorConfiguracionPlugThem(token_type, access_token) {
    var send;
    var valorConfiguracionPlugThem = { "valorConfiguracionPlugThem": 1 };

    send = valorConfiguracionPlugThem;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_plugThem.php",
        data: send,
        success: function (datos) {
            var valorFactura = $("#pagoGranTotal").val();
            var valorPlugThem = datos[0]["valor"];
            var aplica = datos[0]["aplica"];
            var contadorFacturas = datos[0]["contador"];

            // Valor total de factura
            if (aplica === 1) {
                if (parseFloat(valorFactura) > parseFloat(valorPlugThem)) {
                    plugThemPost(token_type, access_token, "");
                } else {
                    //plugThemGet();
                    plugThemPost(token_type, access_token, "1");
                }
            }
            // Número de facturas
            else if (aplica === 2) {
                if (parseInt(contadorFacturas) !== 0 && parseInt(valorPlugThem) !== 0) {
                    if (parseInt(contadorFacturas) === parseInt(valorPlugThem)) {
                        plugThemPost(token_type, access_token, "");
                    } else {
                        //plugThemGet(); 
                        plugThemPost(token_type, access_token, "1");
                    }
                } else {
                    fn_actualizarFactura();

                    fn_validaItemPagado();
                }
            } else {
                fn_actualizarFactura();

                fn_validaItemPagado();
            }
        }
    });
}

/* Se obtiene los datos del cajero/a y administrador/a para aplicar la encuesta */
function datosPlugThemPost() {
    var send;
    var datosPlugThemPost = { "datosPlugThemPost": 1 };
    var transaccion = $("#txtNumFactura").val();
    var objectResultado = new Array();

    send = datosPlugThemPost;
    send.transaccion = transaccion;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_plugThem.php",
        data: send,
        timeout:7000,
        success: function (datos) {
            if (datos.str > 0) {
                objectResultado["BrandId"] = datos[0]["BrandId"];
                objectResultado["idCajero"] = datos[0]["idCajero"];
                objectResultado["EmpId"] = datos[0]["EmpId"];
                objectResultado["EmpName"] = datos[0]["EmpName"];
                objectResultado["SiteId"] = datos[0]["SiteId"];
                objectResultado["SiteName"] = datos[0]["SiteName"];
                objectResultado["ShiftManagerId"] = datos[0]["ShiftManagerId"];
                objectResultado["ShiftManagerName"] = datos[0]["ShiftManagerName"];
            }
        }
    });

    return objectResultado;
}

/* 
 * Plug Them POS : 
 * Al finalizar la transacción, se envia un SMS al celular del cliente
 * con el link de la página de encuesta. 
 */
function plugThemPost(token_type, acces_token, habilitarQR) {
    var send;
    var resultadoObject = new datosPlugThemPost();

    if (Object.values(resultadoObject).length > 0) {

        var BrandId = resultadoObject["BrandId"];
        var idCajero = resultadoObject["idCajero"];
        var EmpId = resultadoObject["EmpId"];
        var EmpName = resultadoObject["EmpName"];
        var SiteId = resultadoObject["SiteId"];
        var SiteName = resultadoObject["SiteName"];
        var ShiftManagerId = resultadoObject["ShiftManagerId"];
        var ShiftManagerName = resultadoObject["ShiftManagerName"];
        var Categories = "";
        var CustomerDoc = $("#txtClienteCI").val();
        var CustomerName = $("#txtClienteNombre").val();
        var CustomerEmail = $("#txtCorreo").val();
        var CustomerMobile = $("#txtClienteFono").val();
        var EffortValue = "1";
        var EffortReason = "";
        var EffortComment = "";
        var InvRange = "";
        var qr_enable = habilitarQR;
        var transaccion = $("#txtNumFactura").val();

        var token_type = token_type;
        var acces_token = acces_token;

        send = {};
        send.metodo = "plugThemPost";
        send.BrandId = BrandId;
        send.idCajero = idCajero;
        send.EmpId = EmpId;
        send.EmpName = EmpName;
        send.SiteId = SiteId;
        send.SiteName = SiteName;
        send.ShiftManagerId = ShiftManagerId;
        send.ShiftManagerName = ShiftManagerName;
        send.Categories = Categories;
        send.CustomerDoc = CustomerDoc;
        send.CustomerName = CustomerName;
        send.CustomerEmail = CustomerEmail;
        send.CustomerMobile = CustomerMobile;
        send.EffortValue = EffortValue;
        send.EffortReason = EffortReason;
        send.EffortComment = EffortComment;
        send.InvRange = InvRange;
        send.qr_enable = qr_enable;
        send.transaccion = transaccion;
        send.token_type = token_type;
        send.acces_token = acces_token;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../facturacion/clienteWS_plugThemCliente.php",
            data: send,
            timeout:5000,

            success: function (datos) {
                fn_actualizarFactura();

                fn_validaItemPagado();
            }
        });
    } else {
        fn_actualizarFactura();

        fn_validaItemPagado();
    }
}

/* 
 * Plug Them GET : QR : 
 * Al finalizar la transacción, se genera un código QR y que es visible en la factura
 * el mimmo que al ser leido se dirigirá a la pagina de encuesta. 
 */
function plugThemGet() {
    var send;
    var resultadoObject = new datosPlugThemPost();

    if (Object.values(resultadoObject).length > 0) {

        var BrandId = resultadoObject["BrandId"];
        var idCajero = resultadoObject["idCajero"];
        var EmpId = resultadoObject["EmpId"];
        var EmpName = resultadoObject["EmpName"];
        var SiteId = resultadoObject["SiteId"];
        var SiteName = resultadoObject["SiteName"];
        var ShiftManagerId = resultadoObject["ShiftManagerId"];
        var ShiftManagerName = resultadoObject["ShiftManagerName"];
        var Categories = "";
        var CustomerDoc = $("#txtClienteCI").val();
        var CustomerName = $("#txtClienteNombre").val();
        var CustomerEmail = $("#txtCorreo").val();
        var CustomerMobile = $("#txtClienteFono").val();
        var EffortValue = "1";
        var EffortReason = "";
        var EffortComment = "";
        var InvRange = "";
        var transaccion = $("#txtNumFactura").val();

        send = {};
        send.metodo = "plugThemGet";
        send.BrandId = BrandId;
        send.EmpId = EmpId;
        send.EmpName = EmpName;
        send.SiteId = SiteId;
        send.SiteName = SiteName;
        send.ShiftManagerId = ShiftManagerId;
        send.ShiftManagerName = ShiftManagerName;
        send.Categories = Categories;
        send.CustomerDoc = CustomerDoc;
        send.CustomerName = CustomerName;
        send.CustomerEmail = CustomerEmail;
        send.CustomerMobile = CustomerMobile;
        send.EffortValue = EffortValue;
        send.EffortReason = EffortReason;
        send.EffortComment = EffortComment;
        send.InvRange = InvRange;
        send.transaccion = transaccion;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../facturacion/clienteWS_plugThemCliente.php",
            data: send,
            success: function (datos) {
                fn_actualizarFactura();

                fn_validaItemPagado();
            }
        });
    } else {
        fn_actualizarFactura();

        fn_validaItemPagado();
    }
}

function fn_cerrarModalCodigoFacturacion(id) {
    $("#txtcodigoFacturacion").val("");
    $("#numPad").hide();
    $("#tecladoCodigoFacturacion").dialog("close");
    $("#keyboard").hide();
    $("#pagado").val("");

    localStorage.removeItem('id_agregador');
}

function fn_guardarCodigoFactura(e) {
    valorCampoCodigo = $("#txtcodigoFacturacion").val();
    if (valorCampoCodigo == "") {
        alertify.error("Por favor Ingresar el Codigo ");
    } else {
        var es_menu_agregador = localStorage.getItem("es_menu_agregador");        

        if (es_menu_agregador == 1) {
            agregadorOrdenPedido();    
        }
        
        $("#txtcodigoFacturacion").val("");
        //banderaUber = 1;
        BANDERA_AGREGADOR = 1;
        $("#numPad").show();
        $("#tecladoCodigoFacturacion").dialog("close");
        $("#keyboard").hide();
        fn_cargaTeclasEmail();
        fn_numerico(txtClienteCI);
        //  fn_clienteExternoSinCupon();
        // fn_clienteConsumirFinal();
        $("#datosFactura").dialog({
            title: "INFORMACION FISCAL S.R.I.",
            modal: true,
            position: "center",
            closeOnEscape: false,
            width: 1000,
            height: 810,
            show: "blind",
            resizable: "false"
        });
    }
}

function fn_clienteConsumirFinal() {
    
    var docConsumidor = "9999999999999";
    var send = { "ConsumidorFinalDatos": 1 };
    send.docConsumidor = docConsumidor;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/config_clientes.php",
        data: send,
        success: function (datos) {
            var datosR = datos.lc_regs;
            if (datosR.str > 0) {
                fn_selecionaClienteAx(datosR.descripcion, datosR.IDCliente, datosR.documento, datosR.telefono, null, datosR.email, datosR.tipoCliente);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR + textStatus + errorThrown);
        }
    });
}

// Kiosko y pickup
function validarVentaKiosko() {
    cargarDatosClienteKiosko();
    setTimeout(function () {
        cerrar();
    }, 100);
    setTimeout(function () {
        continuar();
    }, 100);
}

// Kiosko y pickup
// Cargar por defecto la información del cliente que se ingresó en kiosko/app al momento de realizar el pedido
function cargarDatosClienteKiosko() {
    fn_limpiarCamposCliente();
    var send = { "cargaDatosClienteKiosko": 1 };
    send.cfac_id = $("#txtNumFactura").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/config_clientes.php",
        data: send,
        success: function (datos) {
            if (datos !== null && datos.documento !== null && (datos.documento.length === 10 || datos.documento.length === 13)) {
                // Desactivar momentáneamente evento onChange para evitar que se dispare función fn_clienteBuscar() y se sobreescriba la información
                //onChangeTxtClienteCI(false);
                setTimeout(function () {
                    fn_cargaDatosCliente(1, "", datos.documento, datos.nombres, datos.direccion, datos.telefono, datos.email, "", "", datos.tipoDocumento);
                    //onChangeTxtClienteCI(true);
                }, 20);
            }
        }
    });
}

function onChangeTxtClienteCI(activar) {
    if (activar) {
        $("#txtClienteCI").change(fn_clienteBuscar);
    } else {
        $("#txtClienteCI").unbind("change");
    }
}

function fn_PagoCreditoConfigurados(descripcion, id, documentoAx, telefonoAx, direccionAx = null, correoAx, tipoIdentificacionAx) {
    $("#mdl_rdn_pdd_crgnd").show();
    lc_opcionCreditoEmpresa = "EXTERNO";
    $("#pagado").val($("#pagoTotal").val());
    fn_selecionaClienteAx(descripcion, id, documentoAx, telefonoAx, null, correoAx, tipoIdentificacionAx);
}

function agregadorOrdenPedido() {

    var id_agregador = localStorage.getItem('id_agregador');
    var id_cabecera_pedido = $("#txtOrdenPedidoId").val();
    var send = { agregadorOrdenPedido: 1 };    

    send.id_cabecera_pedido = id_cabecera_pedido;
    send.id_agregador = id_agregador;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/config_facturacion.php",
        data: send,
        success: function(datos) {           
        }
    });
    
}

function fidelizacionNoActiva() {
	return $("#fdznDocumento").val() === undefined || $("#fdznDocumento").val() === "0" || $("#fdznDocumento").val() === "";
}

function fn_ValidarFacturaTarjeta() {    
    let send = { "ValidarFacturaTarjeta": 1, "odp_id": $("#txtOrdenPedidoId").val() };
    $.ajax({
        async: false,
        type: "POST",
        url: "../facturacion/config_facturacion.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.estado >= 0) {
                $("#Tarjeta").val(datos.estado);
                if(datos.estado==1){
                    fn_formasPago();
                }
            }   
        }
    });
}


function fn_auditoriaRuc() {
    let send = { "auditoria": 1 };
    send.factura = sessionStorage.getItem('factura');
    send.documento = $('#txtClienteCI').val();
    send.clave =$('#txt_passPasaporte').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "config_auditoria.php",
        data: send,
        dataType: "json",
        success: function (datos) {
        }
    });
  
}

function condicionFacturacionOrdenPedido( odp_id )
{ 
    $.ajax(
        {
            async: false, 
            type: "POST", 
            dataType: "json", 
            contentType: "application/x-www-form-urlencoded", 
            url: "config_facturacion.php", 
            data: { IDCabeceraOrdenPedidoCFOP: odp_id },
            success: 
                function ( msg ) 
                {
                    if ( msg.str != 0 )
                    {
                        console.log( msg.condicionFOP );
                        /*
                        console.log( msg.condicionFOP.error );
                        console.log( msg.condicionFOP.errorDescripcion );
                        console.log( msg.condicionFOP.condicion );
                        console.log( msg.condicionFOP.condicionDescripcion );
                        console.log( msg.condicionFOP.promesaPendiente );
                        console.log( msg.condicionFOP.IDFormapagoPromPend );
                        console.log( msg.condicionFOP.montoPagadoPromPend );
                        */
                        monto_total_propina = msg.condicionFOP.monto_total_propina
                        localStorage.setItem('odp_campana_nuevo_id', odp_id);

                        var condicion           = msg.condicionFOP.condicion;
                        var errorDescripcion    = msg.condicionFOP.errorDescripcion;
                       
                        if ( condicion != 0 )
                        {
                            if ( condicion == 4 || condicion == 10 )
                            {
                                $( "#IDFormapagoPromesaPendiente" ).val( msg.condicionFOP.IDFormapagoPromPend );
                                $( "#montoPagadoPromesaPendiente" ).val( msg.condicionFOP.montoPagadoPromPend );
                             
                                console.log( "El estado concreto de la última promesa es: pendiente. Promesa pendiente \"" + msg.condicionFOP.promesaPendiente + "\"; Forma de pago pretendida \"" + msg.condicionFOP.IDFormapagoPromPend + "\"." );
                            }

                            if ( errorDescripcion != '' )
                            {
                                alertify.alert( errorDescripcion );
                            }
                        }
                        else
                        {
                            alertify.alert( "El identificador de la Orden de Pedido es inválido." );
                        }

                        $( "#condicionFacOrdenPedido" ).val( condicion );
                    }
                    else
                    {
                        console.log( "Ha ocurrido algo inesperado al intentar estudiar la condición en la que se encuentra la facturación de la orden de pedido \"" + odp_id + "\", por lo que la operación CFOP no ha tenido éxito. Error." );

                        //alertify.alert( "Ha ocurrido algo inesperado al intentar estudiar la condición en la que se encuentra la facturación de la orden de pedido actual." );

                        $( "#condicionFacOrdenPedido" ).val( "-1" );
                    }
                },
            error:
                function ( jqXHR, textStatus, errorThrown )
                {
                    console.log( jqXHR ); console.log( textStatus ); console.log( errorThrown );
                    
                    console.log( "Ha ocurrido algo inesperado al intentar estudiar la condición en la que se encuentra la facturación de la orden de pedido \"" + odp_id + "\", por lo que la operación CFOP no ha tenido éxito. Error." );

                    //alertify.alert( "Ha ocurrido algo inesperado al intentar estudiar la condición en la que se encuentra la facturación de la orden de pedido actual." );

                    $( "#condicionFacOrdenPedido" ).val( "-1" );
                }
        }
    );
}

function persistirFPTarjetas()
{
    alertify.set(
        {
            labels: 
            {
                ok: "RETOMAR"               
            }
        }
    );

    var mensaje = "<p>Ha sido interrumpida una transacción con tarjeta, presione el botón <strong style=\"font-size: 20px; color: red;\">RETOMAR</strong> para recuperar el proceso de la transacción.</p>"

    alertify.alert( mensaje, 
        function()
        { 
            //Recuperar monto pagado.
            var montoPagadoPromPend = $( "#montoPagadoPromesaPendiente" ).val();
            $( "#pagado" ).val( "" );
            $( "#pagado" ).val( montoPagadoPromPend );

            //Clic botón: forma pago Tarjetas.
            var IDFormapagoPromPend = $( "#IDFormapagoPromesaPendiente" ).val();
            document.getElementById( IDFormapagoPromPend ).click();

            //Clic botón: aplicar forma pago.
            document.getElementById( "btnAplicarPago" ).click();   
        }
    );

    alertify.set(
        {
            labels: 
            {
                ok: "OK"               
            }
        }
    );

}

async function fn_aceptaBeneficioCliente() {
    var complete = 0;
    var dop_id = localStorage.getItem("dop_beneficio"+cuenta);
    var uid = localStorage.getItem("uid"+cuenta);
    if (dop_id != null){
        var send = {"aceptaBeneficioClienteApi": 1};
        send.dop_id = dop_id;
        send.uid = uid;
        $.ajax({
            async: false,
            type: "POST",
            url: "config_facturacion.php",
            data: send,
            dataType: "json",
            success: function (datos) {
                complete = 1;
                localStorage.removeItem("dop_beneficio"+cuenta);
                localStorage.removeItem("uid"+cuenta);
                localStorage.removeItem("mesa");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alertify.error(
                    "Se ha producido un error. Por favor inténtelo nuevamente."
                );
            },
        });
    }else{
        alertify.error(
            "Por favor, recargue de nuevo la pagina."
        );
    }
    return complete;
}

function fn_anularPagoDeUna(cfac_id) {
    console.log("entro a anular de una");
    let fmp_id = $("#btnFormaPagoId").val();
    let cdn_id = $("#txtCadenaId").val();
    //**aqui revisar el usr_id */
    let usr_id = $("#txtUserId").val();
    let rst_id = $("#hide_rst_id").val();
    let est_id = $("#IDEstacionDeUna").val();
    let fpf_id = 0;
    var send = { anularPagoDeUna: 1, cfac_id, fpf_id, fmp_id, cdn_id, usr_id, rst_id, est_id };
    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        url: "../DeUna/solicitudAnulacionDeUna.php",
        data: send,
        beforeSend: function () {
            fn_cargando(0);
            fn_cargando(1);
        },
        success: function (datos) {
            console.log("fn_anularPagoDeUna");
            console.log(datos);
            if (datos.status != undefined) {
                if (datos.status == 200) {
                    fn_anularPagoDeUnaFormaDePago()
                    alertify.success("Pago reversado satisfactoriamente")
                } else {
                    if (datos.error != undefined) {
                        alertify.error("No se pudo reversar el pago: " + datos.error)
                    } else {
                        alertify.error("No se pudo reversar el pago: " + datos.error)
                    }
                }
            } else {
                if (datos.error != undefined) {
                    alertify.error("No se pudo reversar el pago: " + datos.error)
                }
            }
            fn_cargando(0);  
        },
        error: function (e) {
            //cargandoDeUnaModal(0);
            fn_cargando(0);
            console.log("error");
            console.log(e);
        }
    });
}

function fn_anularPagoDeUnaFormaDePago() {
    send = { anula_formaPagoEfectivo: 1 };
    send.anu_codFact = $("#txtNumFactura").val();
    send.anu_idPago = $("#btnFormaPagoId").val();
    $.getJSON("config_facturacion.php", send, function (datos) {
        fn_obtieneTotalApagar();
        $("#pagado").val("");
        fn_resumenFormaPago();
        $("#td_cambio").hide();
        $("#td_falta").show();
    });
}

function getApiQualtrics() {

    send = { "api_qualtrics": 1 };
    send.fact = $("#txtNumFactura").val();
    $.getJSON("config_facturacion.php", send, function (datos) {
        console.log(datos);
    });

}
    function fn_verificarCampanaSolidaria(){

 
        if (monto_total_propina > 0){

            const odp_campana_nuevo_id = localStorage.getItem('odp_campana_nuevo_id');
            const odp_campana_viejo_id = localStorage.getItem('odp_campana_viejo_id');

                if (odp_campana_nuevo_id !== null && odp_campana_nuevo_id !== odp_campana_viejo_id) {
                    
                    $("#campana_monto_texto").html('$' +monto_total_propina.toFixed(2))
                    $("#campana_modal").show()
            }

        } 


    }


    function fn_generarCampanaSolidariaFactura() {

        $("#campana_modal").hide();
        alertify.success("Enviando a la impresora comprobante...");
        const odp_campana_nuevo_id = localStorage.getItem('odp_campana_nuevo_id');
        const configuracion =  JSON.parse(localStorage.getItem('campana_solidaria'));
        const valorUnitario = parseFloat(configuracion.valor);

        if (isNaN(valorUnitario) || valorUnitario <= 0) {
            alertify.alert("El valor unitario de la campaña solidaria debe ser superior a cero (0).");
            $("#campana_modal").hide()
            return;
        }

        let cantidad = parseFloat(monto_total_propina) / parseFloat(valorUnitario);

        let send = {'metodo' : 'registrarCampanaSolidaria'};
        send.valorTotal = monto_total_propina;
        send.valorUnitario = valorUnitario;
        send.cantidad = cantidad;
        send.secuencia = configuracion.secuencia;
        send.ruta = localStorage.getItem('api_impresion_ruta');

        $.ajax({
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../ordenpedido/config_campanaSolidaria.php",
            data: send,
            success: function (datos) {
                if ('estado' in datos && datos.estado === 1) {
                    const datosAdicionales = {
                        'codigo': datos.codigo,
                        'ruta': localStorage.getItem('api_impresion_ruta')
                    }

                    var result = new apiServicioImpresion('impresionCampanaSolidaria', null, null, datosAdicionales);
                    var imprime = result["imprime"];
                    var mensaje = result["mensaje"];

                        if (!imprime) {
                            alertify.success("Imprimiendo comprobante de compaña solidaria");
                        } else {
                            alertify.error('Error al imprimir...'+ mensaje);
                        }

                        $("#campana_modal").hide()

                    localStorage.setItem('odp_campana_viejo_id', odp_campana_nuevo_id);
                    fn_abreCajon();

                }else {
                    $("#campana_modal").hide()
                    alertify.alert("Error generando la campaña solidaria.");
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                alertify.alert("Error generando la campaña solidaria." + errorThrown);
                $("#campana_modal").hide()
            }
        });

    }

    function fn_cancelar_campana() {
        $("#campana_modal").hide()


}

function fn_popUpNumeroLocalizador() {
    return new Promise((resolve) => {
        $("#numeroLocalizador").show();
        $("#numeroLocalizador").dialog({
            modal: true,
            width: 450,
            height: 450,
            resize: false,
            opacity: 0,
            show: "explode",
            hide: "explode",
            duration: 5000,
            position: "center",
            open: function(event, ui) {
                $(".ui-dialog-titlebar").hide();
            },
            close: function() {
                resolve();
            }
        });

        $("#numero_localizador").focus();
    });
}


function fn_cerrarNumeroLocalizador() {
    $(".jb-shortscroll-wrapper").show();
    $("#numeroLocalizador").dialog("close");
    $("#numero_localizador").val("");
    noCerrarModalNumeroLocalizador = 1;
}

function fn_validaNumeroLocalizador() {
   
    if ($("#numero_localizador").val() == "") {
        $("#numero_localizador").focus();
        alertify.alert("Ingrese un numero de localizador.");
        return false;
    }

        numeroLocalizador = $("#numero_localizador").val();
        guardarConfiguracionLocalizador();

    if (numeroLocalizador){
        $("#numeroLocalizador").dialog("close");
        $("#numero_localizador").val("");
        noCerrarModalNumeroLocalizador = 0;

    }

}

function condicionConfiguracionLocalizador(idCabeceraOrdenPedido)
{
    $.ajax(
        {
            async: false, 
            type: "GET", 
            dataType: "json", 
            contentType: "application/x-www-form-urlencoded", 
            url: "config_facturacion.php", 
            data: { condicionConfiguracionLocalizador: 1, idCabeceraOrdenPedido:idCabeceraOrdenPedido },
            success: function ( data ) {
                showCondicionConfiguracionLocalizador = data.condicionConfiguracionLocalizador;
                numeroLocalizador = data.localizador
            },
            error:
                function ( jqXHR, textStatus, errorThrown )
                {
                    console.log( jqXHR ); console.log( textStatus ); console.log( errorThrown );
                }
        }
    );
}

function guardarConfiguracionLocalizador()
{
    $.ajax(
        {
            async: false, 
            type: "GET", 
            dataType: "json", 
            contentType: "application/x-www-form-urlencoded", 
            url: "config_facturacion.php", 
            data: { guardarConfiguracionLocalizador: 1, 'numeroLocalizador':numeroLocalizador, IDCabeceraOrdenPedido:$("#txtOrdenPedidoId").val() },
            success: function ( data ) {
                console.log(data)
            },
            error:
                function ( jqXHR, textStatus, errorThrown )
                {
                    console.log( jqXHR ); console.log( textStatus ); console.log( errorThrown );
                }
        }
    );
    }
