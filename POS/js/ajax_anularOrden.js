/* global alertify, alertity */

lc_control_firma = 0; //
lc_control_enlace = 0;
var tiempoEspera = 0;
var myVar;
var lc_timer = 0;
var lc_control = 0;
var opcion = 0;
var Cod_FacturaRetomar = 0;
var Cod_Factura = 0;
var Tipo_FormaPago = 0;
var formaPagoId = 0;
var lc_userAdmin = '';
var idRespuestaVoucher = '';
var idCanalMovimiento = '';
var banderaImprimeAnulado = -1;
var TEMPORIZADOR_PINPAD;
var TEMPORIZADOR_UNIRED;
var CABECERAMSJ = "Atenci&oacute;n";
var SECUENCIAS_ANULACION = new Array;

    //Impedir el uso del botón "Atrás" del navegador
    if (window.history && history.pushState) {
        history.pushState(null, null, null);
        addEventListener('popstate', function() {
            history.pushState(null, null, null);
            alertify.alert("<p style='font-size: 17px; margin-bottom: 15px; line-height: 2;'>Si deseas salir, por favor utilizar los menu de navegación de MaxPoint&nbsp;&nbsp;</p>");
        });
    }

    document.onkeydown = function(e){
        tecla = (document.all) ? e.keyCode : e.which;
        if (tecla == 116){
         return false
        }
       } 

    window.onbeforeunload = function() {
        alertify.alert("<p style='font-size: 17px; margin-bottom: 15px; line-height: 2;'>Si deseas salir, verifique que no existan procesos activos, ya que al salir o refrescar la página, pueden habe perdidas de datos.</p>");
        return "Are you sure you want to leave?";
    }
 
$(document).ready(function () {
    tiempoEspera = $("#tiempoEspera").val();
    if ($("#txt_bloqueado").val() != 0) {
        $("#nuevaorden").attr("Disabled", true);
        $("#nuevaorden").removeClass("boton_Opcion");
        $("#nuevaorden").addClass("boton_Opcion_Bloqueado");
    }
    fn_cargarConfiguracionRestaurante();
    fn_cargarAccesosSistema();
    $('#mdl_cntdr_payphone').css('display', 'none');
    $('#mdl_rsmn_payphone').css('display', 'none');
    $('#detalleFactura').shortscroll();
    $('#listaPedido').shortscroll();
    $("#div_cvv").hide();
    $('#visorFacturas').css('display', 'none');
    $('#detalleFactura').css('display', 'none');
    $('#visorFormasPago').css('display', 'none');
    $('#detalleFormasPago').css('display', 'none');
    $("#retomarOrden").hide();
    $('#retomarOrden').prop("disabled", true);
    $("#visualizarFactura").hide();
    $('#visualizarFactura').prop("disabled", true);
    $("#imprimirTransaccion").hide();
    $("#imprimirTransaccion").prop("disabled", true);
    $("#verFormasPago").hide();
    $('#verFormasPago').prop("disabled", true);
    $("#parBusqueda").val('');
    $('#parBusqueda').keypad();
    $('#codigoTran').keypad();
    $('#nroFactura').keypad();
    $('#identificacion').keypad();
    $("#parBusqueda").prop('disabled', true);
    opcion = 0;
    $("#listado2").hide();
    $("#listadoTxTarjetas").hide();
    $(".calculosTransacciones").hide();
    $("#anulacionesContenedor").hide();
    $("#anulacionesMotivo").hide();
    $("#anulacionesPago").hide();
    $("#teclado").hide();
    $('#parBusqueda').change(function () {
        busqueda();
    });
    //Modal Menu Desplegable
    $('#menu_desplegable').css('display', 'none');
    $('#cnt_mn_dsplgbl_pcns_drch').css('display', 'none');
    $('#boton_sidr').click(function () {
        $('#menu_desplegable').css('display', 'block');
        $('#cnt_mn_dsplgbl_pcns_drch').css('display', 'block');
    });
    $('#menu_desplegable').click(function () {
        $('#menu_desplegable').css('display', 'none');
        $('#cnt_mn_dsplgbl_pcns_drch').css('display', 'none');
    });
    fn_cuentasAbiertas();
    fn_cargarMotivosAnulacion();
    $('#retomarOrden').attr('onclick', 'fn_verificarOrigenOrden()');
    fn_cargarProveedorTracking();

    const [day, month, year] = $("#hide_periodo").val().split('/');
    const periodoActual = new Date(+year, +month - 1, +day);
    const aplicaMesVigentePA = $("#hide_aplicaMesVigentePA").val() === 'SI';
    const dias = $("#hide_cantidadDiasPA").val();
    const min = new Date(new Date(periodoActual).setDate(aplicaMesVigentePA ? periodoActual.getDate() - dias : 1));
    const max = new Date(new Date(periodoActual).setDate(periodoActual.getDate() - 1));
    const myCalender = new CalendarPicker('#myCalendarWrapper', {
        // If max < min or min > max then the only available day will be today.
        init: periodoActual,
        min: min,
        max: max, // NOTE: new Date(nextYear, 10) is "Nov 01 <nextYear>"
        locale: 'es-VE', // Can be any locale or language code supported by Intl.DateTimeFormat, defaults to 'en-US'
        showShortWeekdays: false // Can be used to fit calendar onto smaller (mobile) screens, defaults to false
    });

    $("#fechaTran").on("click", function(){
        $("#contenedorFechaTransaccion").show();
    });

    myCalender.onValueChange((currentValue) => {
        $("#contenedorFechaTransaccion").hide();
        $("#fechaTran").val(currentValue.toLocaleDateString("es-VE"));
    });
});

function fn_cargarMotivosAnulacion() {
    var send;
    var html = "<option value='0'>- Seleccionar Opci&oacute;n -</option>";
    send = {"motivoAnulacion": 1};
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {

            for (var i = 0; i < datos.str; i++) {
                html += "<option value='" + datos[i]['mtv_id'] + "'>" + datos[i]['mtv_descripcion'] + "</option>";
            }
            $("#motivosAnulacion").html(html);
        }
    });
}

function fn_cargarConfiguracionRestaurante() {
    var send = {"cargarConfiguracionRestaurante": 1};
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            $("#hide_tipo_servicio").val(datos[0]['tpsrv_descripcion']);
            $("#aplica_nc_sinconsumidor").val(datos[0]['aplica_nc_sinconsumidor']);
        }
    });
}

function fn_verificarOrigenOrden() {
    var cfac_id = $('#listadoPedido').find("li.focus").attr("id");
    var est_cfac = $('#est_' + cfac_id).val();
    var est_id = $('#hide_est_id').val();
    var tipo_servicio = $("#hide_tipo_servicio").val();

    if (est_id != est_cfac) {
        var cabeceramsj = 'Atenci&oacute;n!!';
        var mensaje = 'Este movimiento ha sido creado en otra estaci&oacute;n.';
        fn_mensajeAlerta(cabeceramsj, mensaje, 0, '0');
    } else {
        if (tipo_servicio == 'FAST FOOD') {
            fn_validarUsuarioAdministrador();
        } else {
            fn_retomarCuentaAbierta();
        }
    }
}

function fn_notaCreditoFacturaOtros(cfac_id) {
    $('#visualizarFactura').show();
    $("#verFormasPago").show();
    $("#imprimirTransaccion").show();
    $("#imprimirTransaccion").prop("disabled", false);
    $("#anularOrden").show();
    $('#anularOrden').prop("disabled", false);
    Cod_FacturaRetomar = 0;
    $("#retomarOrden").hide();
    $('#retomarOrden').prop("disabled", true);
    $("#parBusqueda").val('');
    $("#parBusqueda").prop('disabled', false);
    opcion = 1;
    $("#listado2").hide();
    $("#listado").show();
    var send;
    var motivo = $('#motivoObservacion').val();
    var mtv_id = $("#motivosAnulacion").val();
    var rst_id = $("#hide_rst_id").val();
    var numFacturas = 0;
    var numAnulacion = 0;
    send = {"generarNotaCreditoOtros": 1};
    send.cfac_id = cfac_id;
    send.motivo = motivo;
    send.mtv_id = mtv_id;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            $('#anulacionesMotivo').hide();
            $('#anulacionesMotivo').dialog('close');
            $("#keyboard").hide();
            alertify.alert('ANULADA');
            var totalventas = 0;
            $("#calculoTransNum").empty();
            $("#calculoTransTotal").empty();
            $("#listado ul").empty();
            for (var i = 0; i < datos.str; i++) {
                html = "<li id=" + datos[i]['cfac_id'] + " onclick='fn_modificarLista(\"" + datos[i]['ncre_id'] + "\", \"" + datos[i]['cfac_id'] + "\")'><div class='listaFactura'>" + datos[i]['cfac_id'] + "</div><div class='listaMesa'>" + datos[i]['mesa_descripcion'] + "</div><div class='listaSubtotal'>" + datos[i]['cfac_subtotal'] + "</div><div class='listaTotal'>" + datos[i]['cfac_total'] + "</div><div class='listaCaja'>" + datos[i]['est_nombre'] + "</div><div class='listaCajero'>" + datos[i]['usuario'] + "</div><div class='listaStatus'>" + datos[i]['descripcionEstado'] + "</div></li>";
                $("#listadoPedido").append(html);
                if (datos[i]['ncre_id'] != 0) {
                    $("#" + datos[i]['cfac_id'] + "").css({'background': '#00A7F5'});
                    numAnulacion++;
                } else {
                    totalventas = totalventas + datos[i]['cfac_total'];
                    numFacturas++;
                }
            }
            numAnulacion = numAnulacion / 2;
            $("#calculoTransNum").text(numFacturas);
            $("#calculoAnulacion").text(numAnulacion);
            $("#calculoTransTotal").text(totalventas.toFixed(2));
            $(".calculosTransacciones").show();
            $('#cuentasAbiertas').prop("disabled", false);
            $('#cuentasCerradas').prop("disabled", true);
        } else {
            $("#listadoPedido").html("");
            alertify.alert('No existen cuentas cerradas');
            $('#cuentasCerradas').prop("disabled", false);
            $('#cuentasAbiertas').prop("disabled", false);
        }
    });
}

function fn_salirSistema() {
    window.location.href = "../index.php";
}

function fn_irTomaPedido() {
    window.location.href = "../ordenpedido/tomaPedido.php";
}

function fn_cargarAccesosSistema() {
    var send;
    send = {"cargarAccesosPerfil": 1};
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                switch (datos[i]['acc_descripcion']) {
                    case 'Imprimir':
                        $('#imprimirFactura').css('display', 'block');
                        $('#imprimirFactura2').css('display', 'block');
                        break;
                    case 'Todo':
                        $('#imprimirFactura').css('display', 'block');
                        $('#imprimirFactura2').css('display', 'block');
                        break;
                }
            }
        }
    });
}

function fn_visualizarFormasPago() {
    var Cod_Movimiento = $('#listadoPedido').find("li.focus").attr("id");
    if (Cod_Movimiento.substring(4, 5) == 'F') {
        $("#hide_saut_id").val("");
        var send;
        var cfac_id = $('#listadoPedido').find("li.focus").attr("id");
        send = {"cargarTipoEnvioFacturaFormaPago": 1};
        send.cfac_id = cfac_id;
        $.getJSON("config_anularOrden.php", send, function (datos) {
            if (datos.str > 0) {
                if (datos[0]['tpenv_descripcion'] != 'OTRO') {
                    if (datos[0]['descripcion'] == 'PAYPHONE') {
                        fn_cargarInformacionFactura(cfac_id);
                    } else {
                        $('#cabeceraFormasPago').html("");
                        $('#visorFormasPago').css('display', 'block');
                        $('#detalleFormasPago').css('display', 'block');
                        fn_cargarFormasPagoFactura(Cod_Movimiento);
                    }
                } else {
                    var cabeceramsj = 'Atenci&oacute;n!!';
                    var mensaje = 'No existen transacciones con tarjeta en el sistema.';
                    fn_mensajeAlerta(cabeceramsj, mensaje, 0, '0');
                }
            } else {
                var cabeceramsj = 'Atenci&oacute;n!!';
                var mensaje = 'No existen transacciones con tarjeta de esta factura.';
                fn_mensajeAlerta(cabeceramsj, mensaje, 0, '0');
            }
        });
    }
}

function fn_cargarFormasPagoFactura(cfac_id) {
    var send;
    var html = "<div class='titulo_formapago'><h4>Forma de Pago</h4></div>";
    send = {"consultar_formasPagoFactura": 1};
    send.cfac_id = cfac_id;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                html = html + "<input type='button' id='' value='" + datos[i]['fmp_descripcion'] + "' onclick='fn_verTransaccionesFormasPago(\"" + cfac_id + "\", " + datos[i]['fpf_id'] + ", \"" + datos[i]['fmp_descripcion'] + "\")' class='boton_Opcion' style='margin: 10px auto;' />";
            }
            $('#lista_formaspago').html(html);
        }
    });
}

function fn_verTransaccionesFormasPago(Cod_Movimiento, fpf_id, descripcion) {
    var send;
    var html = "<tr><th>#</th><th>HORAS</th><th>FECHA</th><th>RESPUESTA</th><th>TOTAL</th><th>AUTORIZACION</th><th>ESTADO</th></tr>";
    send = {"consultar_transaccionformaPagoFactura": 1};
    send.rsaut_movimiento = Cod_Movimiento;
    send.fpf_id = fpf_id;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            $("#hide_saut_id").val("");
            for (var i = 0; i < datos.str; i++) {
                html = html + "<tr id='" + i + "' onclick='fn_seleccionarTransaccion(" + i + ", " + datos[i]['rsaut_id'] + ")'><td>" + datos[i]['btd_rank'] + "</td><td>" + datos[i]['hora'] + "</td><td>" + datos[i]['fecha'] + "</td><td>" + datos[i]['rsaut_respuesta'] + "</td><td>" + datos[i]['total'] + "</td><td>" + datos[i]['rsaut_numero_autorizacion'] + "</td><td>" + datos[i]['std_id'] + "</td></tr>";
            }
            $('#detalles_transaccionesformapago').html(html);
        } else {
            $("#hide_saut_id").val("");
        }
    });
}

function fn_seleccionarTransaccion(fila, rsaut_id) {
    $("#hide_saut_id").val(rsaut_id);
    $("#detalles_transaccionesformapago tr").removeClass("seleccionado");
    $("#" + fila + "").addClass("seleccionado");
}

function fn_imprimirFormasPago() {
    var send;
    var codigo = $("#hide_saut_id").val();
    if (codigo.length > 0) {
        send = {"impresionVaucher": 1};
        send.rsaut_id = codigo;
        send.rst_id = $("#hide_rst_id").val();
        send.usr_id = $("#hide_usr_id").val();
        send.est_ip = $("#hide_est_ip").val();
        $.getJSON("config_anularOrden.php", send, function (datos) {
            if (datos.str > 0) {
                alertify.alert('Imprimiendo...');
            }
        });
    } else {
        var cabeceramsj = 'Atenci&oacute;n!!';
        var mensaje = 'Debe seleccionar una transaccion para imprimir.';
        fn_mensajeAlerta(cabeceramsj, mensaje, 0, '0');
    }
}

fn_reimpresion = function () {
    var impresion = $('#listadoPedido').find("li.focus").attr("impresion");
    if (!($('li.focus').length)) {
        alert("Seleccione una transaccion en error");
    } else if (impresion == 53) {
        var idFactura = $('#listadoPedido').find("li.focus").attr("id");
        send = {"impresionFacturaError": 1};
        send.idFactura = idFactura;


        var apiImpresion = getConfiguracionesApiImpresion();

        if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
            fn_cargando(1);

            var result = new apiServicioImpresion('reimpresion_factura', idFactura);
            var imprime = result["imprime"];
            var mensaje = result["mensaje"];

            console.log('imprime: ', result);

            if (!imprime) {
                alertify.success('Imprimiendo...');
                fn_cuentasCerradas();
                fn_cargando(0);

            } else {
            
                alertify.success('Error al imprimir...');
                fn_cuentasCerradas();
                fn_cargando(0);

            }
        } else{

        $.getJSON("config_anularOrden.php", send, function (datos) {
            if (datos.Confirmar > 0) {
                fn_cuentasCerradas();
                alertify.alert('Imprimiendo...');
            }
        });

            
        }

    } else {

        alertify.error('Estra factura ya fue impresa...');
    }





}

function fn_reimpresionFactura() {
    $('#visorFacturas').css('display', 'none');
    $('#detalleFactura').css('display', 'none');
    $('#imprimirFactura').css('display', 'none');
    $("#anulacionesContenedor").dialog({
        modal: true,
        width: 500,
        height: 500,
        resize: false,
        opacity: 0,
        show: "none",
        hide: "none",
        duration: 500,
        open: function (event, ui) {
            $(".ui-dialog-titlebar").hide();
            $('#usr_clave').attr('onchange', 'reimpresionDelVooucher()');
            fn_numerico("#usr_clave");
        }
    });
}

function reimpresionDelVooucher() {
    validaAdminReimpresiones();
}

function fn_visualizarFactura() {
    $('#visorFacturas').css('display', 'block');
    $('#detalleFactura').css('display', 'block');
    if (banderaImprimeAnulado == 0) {
        $('#imprimirFactura').css('display', 'block');
    } else {
        $('#imprimirFactura').css('display', 'none');
    }
    fn_visualizaVoucher();
}

function fn_imprimir() {
    var Cod_Movimiento = $('#listadoPedido').find("li.focus").attr("id");
    $('#visorFacturas').css('display', 'none');
    $('#detalleFactura').css('display', 'none');
    if (Cod_Movimiento.substring(4, 5) == 'F') {
        fn_imprimirFactura(Cod_Movimiento);
    } else {
        fn_imprimirNotaCredito(Cod_Movimiento);
    }
}

function fn_imprimirFactura(cfac_id) {
    var usr_id = $('#hide_usr_id').val();
    var est_ip = $('#hide_est_ip').val();
    send = {"impresionFactura": 1};
    send.cfac_id = cfac_id;
    send.usr_id = usr_id;
    send.est_ip = est_ip;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.Respuesta > 0) {
            alertify.alert('Imprimiendo...');
        }
    });
    fn_cerrarVisorFacturas();
}

function fn_imprimirNotaCredito(ncre_id) {
    var usr_id = $('#hide_usr_id').val();
    var est_ip = $('#hide_est_ip').val();
    send = {"impresionNotaCredito": 1};
    send.ncre_id = ncre_id;
    send.usr_id = usr_id;
    send.est_ip = est_ip;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.Respuesta > 0) {
            alertify.alert('Imprimiendo...');
        }
    });
}

function fn_cargarFactura(cfac_id) {
    send = {"impresion_factura": 1};
    send.cfac_id = cfac_id;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                $("#cabecerafactura").append(datos[i]['html']).append(datos[i]['html2']).append(datos[i]['htmlf']);
            }
        }
    });
}

function fn_visualizaVoucher() {
    send = {"impresion_voucher": 1};
    send.rqaut = idRespuestaVoucher;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        $("#cabecerafactura").empty();
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                $("#cabecerafactura").append(datos[i]['html']).append(datos[i]['htmla']).append(datos[i]['htmlb']).append(datos[i]['htmlf']);
            }
        }
    });
}

function fn_cerrarVisorFormasPago() {
    $('#visorFormasPago').css('display', 'none');
    $('#detalleFormasPago').css('display', 'none');
}

function fn_cerrarVisorFacturas() {
    $('#visorFacturas').css('display', 'none');
    $('#detalleFactura').css('display', 'none');
}

function busqueda() {
    var parametro = $('#parBusqueda').val();

    if (opcion == 0) {
        $('#parBusqueda').val("");
    } else if (opcion == 1) {
        if (parametro.length > 2 || parametro.length == 0)
            fn_busquedaCuentasCerradas(parametro);
    } else if (opcion == 2) {
        if (parametro.length > 2 || parametro.length == 0)
            fn_busquedaCuentasAbiertas(parametro);
    }
}

function teclado(elEvento) {
    evento = elEvento || window.event;
    k = evento.keyCode; //nÃºmero de cÃ³digo de la tecla.
    //teclas nÃºmericas del teclado alfamunÃ©rico
    if (k > 47 && k < 58) {
        p = k - 48; //buscar nÃºmero a mostrar.
        p = String(p); //convertir a cadena para poder aÃ±Ã¡dir en pantalla.
        //fn_agregarNumero(p); //enviar para mostrar en pantalla
    }
    //Teclas del teclado nÃºmerico. Seguimos el mismo procedimiento que en el anterior.
    if (k > 95 && k < 106) {
        p = k - 96;
        p = String(p);
        //fn_agregarNumero(p);
    }
    if (k == 110 || k == 190) {
        fn_agregarNumero(".");
    } //teclas de coma decimal
    if (k == 8) {
        fn_eliminarNumero();
    } //Retroceso en escritura : tecla retroceso.
    if (k > 57 && k < 210) {
        document.getElementById("parBusqueda").value = "";
    }
}

function fn_busquedaCuentasCerradas(parametro) {
    $("#mdl_rdn_pdd_crgnd").show();
    $('#visualizarFactura').prop("disabled", true);
    $('#verFormasPago').prop("disabled", true);
    Cod_FacturaRetomar = 0;
    $("#listado2").hide();
    $("#listado").show();
    var rst_id = document.getElementById("hide_rst_id").value;
    var numFacturas = 0;
    var numAnulacion = 0;
    send = {"busquedaCuentasCerradas": 1};
    send.rst_id = rst_id;
    send.parametro = parametro;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            var totalventas = 0;
            $("#calculoTransNum").empty();
            $("#calculoTransTotal").empty();$("#listado ul").empty();

            for (var i = 0; i < datos.str; i++) {
                html = "<li id=" + datos[i]['cfac_id'] + " planFidelizacion='" + datos[i]['PlanFidelizacionAutoConsumo'] + "' documentoConDatos='" + datos[i]['documento_con_datos'] + "' onclick='fn_modificarLista(\"" + datos[i]['ncre_id'] + "\", \"" + datos[i]['cfac_id'] + "\")'><div class='listaFactura'><b>" + datos[i]['cfac_id'] + "</b></div><div class='listaMesa'>" + datos[i]['mesa_descripcion'] + "</div><div class='listaSubtotal'>" + datos[i]['cfac_subtotal'] + "</div><div class='listaTotal'>" + datos[i]['cfac_total'] + "</div><div class='listaCaja'>" + datos[i]['est_nombre'] + "</div><div class='listaCajero'>" + datos[i]['usuario'] + "</div><div class='listaStatus'>" + datos[i]['descripcionEstado'] + "</div><div class='listaComentario'><p>" + datos[i]['cfac_observacion'] + "</p></div></li><input type='hidden' id='est_fac_" + datos[i]['cfac_id'] + "' value='" + datos[i]['est_id'] + "'/></li>";

                $("#listadoPedido").append(html);
                if (datos[i]['impresion'] == 53) {
                    $("#" + datos[i]['cfac_id'] + "").css({'background': '#ff5f3c'});
                } else if (datos[i]['ncre_id'] != 0) {
                    $("#" + datos[i]['cfac_id'] + "").css({'background': '#00A7F5'});
                    numAnulacion++;
                } else {
                    totalventas = totalventas + datos[i]['cfac_total'];
                    numFacturas++;
                }
            }

            numAnulacion = numAnulacion / 2;
            $("#calculoTransNum").text(numFacturas);
            $("#calculoAnulacion").text(numAnulacion);
            $("#calculoTransTotal").text(totalventas.toFixed(2));
            $(".calculosTransacciones").show();
            $('#cuentasAbiertas').prop("disabled", false);
            $('#cuentasCerradas').prop("disabled", true);
        } else {
            $("#listadoPedido").html("");
            var cabeceramsj = 'Atenci&oacute;n!!';
            var mensaje = 'No existen transacciones cerradas con el par&aacute;metro de b&uacute;squeda ingresado.';
            fn_mensajeAlerta(cabeceramsj, mensaje, 0, '0');
            $('#cuentasCerradas').prop("disabled", false);
            $('#cuentasAbiertas').prop("disabled", false);
            $(".calculosTransacciones").hide();
        }
        $("#mdl_rdn_pdd_crgnd").hide();
    });
}

function fn_busquedaCuentasAbiertas(parametro) {
    Cod_FacturaRetomar = 0;
    $('#retomarOrden').prop("disabled", true);
    opcion = 2;
    send = {"busquedaCuentasAbiertas": 1};
    send.parametro = parametro;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            var totalventas = 0;
            $("#calculoTransNum").empty();
            $("#calculoTransTotal").empty();
            $("#listado ul").empty();
            for (i = 0; i < datos.str; i++) {
                html = "<li id=" + datos[i]['cfac_id'] + " onclick='fn_habilitarOpcionCuentaAbierta(\"" + datos[i]['odp_id'] + "\", \"" + datos[i]['cfac_id'] + "\")'><div class='listaFactura'><b>" + datos[i]['cfac_id'] + "</b></div><div class='listaMesa'>" + datos[i]['mesa_descripcion'] + "</div><div class='listaSubtotal'>" + datos[i]['cfac_subtotal'] + "</div><div class='listaTotal'>" + datos[i]['cfac_total'] + "</div><div class='listaCaja'>" + datos[i]['est_nombre'] + "</div><div class='listaCajero'>" + datos[i]['usuario'] + "</div><div class='listaStatus'>" + datos[i]['descripcionEstado'] + "</div><div class='listaComentario'><p>" + datos[i]['odp_observacion'] + "</p></div><input type='hidden' id='est_" + datos[i]['cfac_id'] + "' value='" + datos[i]['est_id'] + "'/></li>";
                $("#listadoPedido").append(html);
                totalventas = totalventas + datos[i]['cfac_total'];
            }
            $("#calculoTransNum").append(datos.str);
            $("#calculoTransTotal").append(totalventas.toFixed(2));
            $(".calculosTransacciones").show();
            $('#cuentasCerradas').prop("disabled", false);
            $('#cuentasAbiertas').prop("disabled", true);
        } else {
            $("#listadoPedido").html("");
            var cabeceramsj = 'Atenci&oacute;n!!';
            var mensaje = 'No existen cuentas abiertas con el par&aacute;metro de b&uacute;squeda ingresado.';
            fn_mensajeAlerta(cabeceramsj, mensaje, 0, '0');
            $(".calculosTransacciones").hide();
            $('#cuentasCerradas').prop("disabled", false);
            $('#cuentasAbiertas').prop("disabled", false);
        }
    });
}

function fn_cuentasAbiertas() {
    $("#cambiarDatosCliente").hide();
    $("#visualizarFactura").css('display', 'none');
    $('#visualizarFactura').hide();
    $('#visualizarFactura').prop("disabled", true);
    $("#imprimirTransaccion").hide();
    $("#imprimirTransaccion").prop("disabled", true);
    $("#verFormasPago").hide();
    $('#verFormasPago').prop("disabled", true);
    $("#anularOrden").hide();
    $('#anularOrden').prop("disabled", true);
    Cod_FacturaRetomar = 0;
    $('#retomarOrden').show();
    $('#retomarOrden').prop("disabled", true);
    $("#parBusqueda").val('');
    $("#parBusqueda").prop('disabled', false);
    opcion = 2;
    $("#listado2").hide();
    $("#listadoTxTarjetas").hide();
    $("#listado").show();
    cargandoD(0);
    mostrarOpciones("cuentasAbiertas");
    var send = {"cuentasAbiertas": 1};
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            var totalventas = 0;
            $("#calculoTransNum").empty();
            $("#calculoTransTotal").empty();
            $("#listado ul").empty();
            for (i = 0; i < datos.str; i++) {
                html = "<li id=" + datos[i]['cfac_id'] + " onclick='fn_habilitarOpcionCuentaAbierta(\"" + datos[i]['odp_id'] + "\", \"" + datos[i]['cfac_id'] + "\")'><div class='listaFactura'><b>" + datos[i]['cfac_id'] + "</b></div><div class='listaMesa'>" + datos[i]['mesa_descripcion'] + "</div><div class='listaSubtotal'>" + datos[i]['cfac_subtotal'] + "</div><div class='listaTotal'>" + datos[i]['cfac_total'] + "</div><div class='listaCaja'>" + datos[i]['est_nombre'] + "</div><div class='listaCajero'>" + datos[i]['usuario'] + "</div><div class='listaStatus'>" + datos[i]['descripcionEstado'] + "</div><div class='listaComentario'><p>" + datos[i]['odp_observacion'] + "</p></div><input type='hidden' id='est_" + datos[i]['cfac_id'] + "' value='" + datos[i]['est_id'] + "'/></li>";
                $("#listadoPedido").append(html);
                totalventas = totalventas + datos[i]['cfac_total'];
            }
            $("#calculoTransNum").append(datos.str);
            $("#calculoTransTotal").append(totalventas.toFixed(2));
            $(".calculosTransacciones").show();
            $('#cuentasCerradas').prop("disabled", false);
            $('#cuentasAbiertas').prop("disabled", true);
        } else {
            $("#listadoPedido").html("");
            var cabeceramsj = 'Atenci&oacute;n!!';
            var mensaje = 'No existen cuentas abiertas.';
            fn_mensajeAlerta(cabeceramsj, mensaje, 0, '0');
            $(".calculosTransacciones").hide();
            $('#cuentasCerradas').prop("disabled", false);
            $('#cuentasAbiertas').prop("disabled", false);
        }
        cargandoD(1);
    });
}

function fn_validarUsuarioAdministrador() {
    if (!($('li.focus').length)) {
        alertify.alert('No ha seleccionado una transacci&oacute;n');
    } else {
        $('#anularOrden').prop("disabled", true);
        $("#anulacionesContenedor").show();
        $("#anulacionesContenedor").dialog({
            modal: true,
            width: 500,
            height: 500,
            resize: false,
            opacity: 0,
            show: "none",
            hide: "none",
            duration: 500,
            open: function (event, ui) {
                $(".ui-dialog-titlebar").hide();
                $('#usr_clave').attr('onchange', 'fn_validarCredencialesUsuario()');
                fn_numerico("#usr_clave");
            }
        });
    }
}

function fn_validarCredencialesUsuario() {
    $("#numPad").hide();
    var usr_clave = $("#usr_clave").val();
    var cfac_id = Cod_FacturaRetomar;
    var rst_id = document.getElementById("hide_rst_id").value;
    if (usr_clave.indexOf('%') >= 0) {
        var old_usr_clave = usr_clave.split('?;')[0];
        var new_usr_clave = old_usr_clave.replace(new RegExp("%", 'g'), "");
        var usr_tarjeta = new_usr_clave;
        usr_clave = 'noclave';
    } else {
        var usr_tarjeta = 0;
    }
    if (usr_clave != "") {
        send = {"validarCreencialesUsuario": 1};
        send.usr_clave = usr_clave;
        send.usr_tarjeta = usr_tarjeta;
        send.movimiento = cfac_id;
        $.getJSON("config_anularOrden.php", send, function (datos) {
            if (datos.str > 0) {
                $("#anulacionesContenedor").dialog("close");
                $("#usr_clave").val("");
                if (datos[0]['usr_id'].length > 0) {
                    fn_retomarCuentaAbierta();
                } else {
                    alertify.alert("Esta transacci&oacute;n ha sido abierta en otra estaci&oacute;n.");
                }
            } else {
                fn_numerico("#usr_clave");

                var cabeceramsj = 'Atenci&oacute;n!!';
                var mensaje = 'No tiene permisos para retomar esta transacci&oacute;n.';
                fn_mensajeAlerta(cabeceramsj, mensaje, 1, "#usr_clave");
            }
        });
    } else {
        fn_numerico("#usr_clave");

        var cabeceramsj = 'Atenci&oacute;n!!';
        var mensaje = 'Ingrese la clave para retomar la transacci&oacute;n.';
        fn_mensajeAlerta(cabeceramsj, mensaje, 1, "#usr_clave");
    }
}

function fn_habilitarOpcionCuentaAbierta(linea, factura) {
    if (factura.indexOf('F') > 0) {
        Cod_FacturaRetomar = factura;
    } else {
        Cod_FacturaRetomar = linea;
    }
    $('#listadoPedido li').removeClass('focus');
    $('#listadoPedido li').live('click', function () {
        $(this).addClass('focus');
    });
    $('#listadoPedido li.focus').click(function () {
        $(this).removeClass('focus');
    });
    $("#" + factura).addClass("focus");
    $('#retomarOrden').prop("disabled", false);
    $('#eliminarOrden').prop("disabled", false);
}

function fn_puedeRetomarEstaOrden() {

    var retomaFac;

    send = {"esMiMesaEnFacturacion": 1};
    send.est_id = $('#hide_est_id').val();
    send.odp_id = Cod_FacturaRetomar;
    $.ajax({
        async: false,
        type: 'POST',
        dataType: 'json',
        contentType: 'application/x-www-form-urlencoded',
        url: 'config_anularOrden.php',
        data: send,
        success: function (data) {
            if (data.str > 0) {

                if (data[0]['retomar'] === 'Si') {
                    retomaFac = true;
                } else {
                    retomaFac = false;
                }

            } else {
                retomaFac = false;
            }
        }
    });

    return retomaFac;
}

function fn_retomarCuentaAbierta() {

    if (Cod_FacturaRetomar.indexOf('-') < 0) {
        var odp_id = 0;
        var dop_cuenta = 0;
        var mesa_id = 0;
        send = {"retomarCuentaAbierta": 1};
        send.cfac_id = Cod_FacturaRetomar;
        $.getJSON("config_anularOrden.php", send, function (datos) {
            if (datos.str > 0) {
                odp_id = datos[0]['odp_id'];
                mesa_id = datos[0]['mesa_id'];
                dop_cuenta = datos[0]['dop_cuenta'];

                localStorage.setItem("ls_recupera_orden", 1);
                localStorage.setItem("ls_recupera_orden_id", odp_id);
                localStorage.setItem("ls_recupera_orden_mesa", mesa_id);
                localStorage.setItem("ls_recupera_orden_cuenta", dop_cuenta);

                $('#contenedorRetomarOrden').html('<form action="../facturacion/factura.php" name="cobro" method="post" style="display:none;"><input type="text" name="odp_id" value="' + odp_id + '" /><input type="text" name="dop_cuenta" value="' + dop_cuenta + '" /><input type="text" name="mesa_id" value="' + mesa_id + '" /></form>');
                document.forms['cobro'].submit();
            }
        });
    } else {
        send = {"consultarMesaOrden": 1};
        send.odp_id = Cod_FacturaRetomar;
        $.getJSON("config_anularOrden.php", send, function (datos) {
            if (datos.str > 0) {
                if ( datos[0]['url'] != 'ocupada' ) {
                    window.location.href = datos[0]['url'];
                } else {
                    alertify.alert("La transacci&oacute;n est&aacute; siendo atendida por el usuario: " + datos[0]['direccion'] + ". No se puede retomar, intente m&aacute; tarde.");
                }
            }
        });
    }
}

function fn_cuentasCerradas() {
    $("#mdl_rdn_pdd_crgnd").show();
    $("#cambiarDatosCliente").show();
    $("#visualizarFactura").css('display', 'none');
    $('#visualizarFactura').hide();
    $("#verFormasPago").show();
    $("#imprimirTransaccion").show();
    $("#anularOrden").show();
    $('#anularOrden').prop("disabled", false);
    Cod_FacturaRetomar = 0;
    $("#retomarOrden").hide();
    $('#retomarOrden').prop("disabled", true);
    $("#parBusqueda").val('');
    $("#parBusqueda").prop('disabled', false);
    opcion = 1;
    $("#listado2").hide();
    $("#listadoTxTarjetas").hide();
    $("#listado").show();
    cargando(true);
    mostrarOpciones("cuentasCerradas");
    var rst_id = document.getElementById("hide_rst_id").value;
    var numFacturas = 0;
    var numAnulacion = 0;
    var send = {"cuentasCerradas": 1};
    $.getJSON("config_anularOrden.php", send, function (datos) {
        cargando(false);
        if (datos.str > 0) {
            var totalventas = 0;
            $("#calculoTransNum").empty();
            $("#calculoTransTotal").empty();
            $("#listado ul").empty();
            for (var i = 0; i < datos.str; i++) {
                html = "<li id='" + datos[i]['cfac_id'] + "' planFidelizacion='" + datos[i]['PlanFidelizacionAutoConsumo'] + "' documentoConDatos='" + datos[i]['documento_con_datos'] + "' onclick='fn_modificarLista(\"" + ( (datos[i]['descripcionEstatus'] == 'Anulada') ? 'Anulada' : datos[i]['ncre_id']) + "\", \"" + datos[i]['cfac_id'] + "\")'><div class='listaFactura'><b>" + datos[i]['cfac_id'] + "</b></div><div class='listaMesa'>" + datos[i]['mesa_descripcion'] + "</div><div class='listaSubtotal'>" + datos[i]['cfac_subtotal'] + "</div><div class='listaTotal'>" + datos[i]['cfac_total'] + "</div><div class='listaCaja'>" + datos[i]['est_nombre'] + "</div><div class='listaCajero'>" + datos[i]['usuario'] + "</div><div class='listaStatus'>" + datos[i]['descripcionEstado'] + "</div><div class='listaComentario'><p>" + datos[i]['cfac_observacion'] + "</p></div><input type='hidden' id='est_fac_" + datos[i]['cfac_id'] + "' notacredito='" + datos[i]['ncre_id'] + "' value='" + datos[i]['est_id'] + "'/></li>";
                $("#listadoPedido").append(html);
                if (datos[i]['impresion'] == 53) {
                    $("#" + datos[i]['cfac_id'] + "").css({'background': '#ff5f3c'});
                } else if (datos[i]['ncre_id'] != 0) {
                    $("#" + datos[i]['cfac_id'] + "").css({'background': '#00A7F5'});
                    numAnulacion++;
                    if (datos[i]['ncdre_ant'] == null ) {
                        $("#" + datos[i]['cfac_id'] + "").css({'background': '#73ffa4'});
                        numAnulacion--;
                    }
                } else if (datos[i]['ncre_id'] == 0 && datos[i]['descripcionEstatus'] == 'Anulada' && datos[i]['cfac_observacion'].startsWith('Cambio Datos Cliente - Factura Extemporanea')){ 
                    $("#" + datos[i]['cfac_id'] + "").css({'background': '#73ffa4'});                 
                } else {
                    totalventas = totalventas + datos[i]['cfac_total'];
                    numFacturas++;
                }
            }
            numAnulacion = numAnulacion / 2;
            $("#calculoTransNum").text(numFacturas);
            $("#calculoAnulacion").text(numAnulacion);
            $("#calculoTransTotal").text(totalventas.toFixed(2));
            $(".calculosTransacciones").show();
            $('#cuentasAbiertas').prop("disabled", false);
            $('#cuentasCerradas').prop("disabled", true);
        } else {
            $("#listadoPedido").html("");
            alertify.alert('No existen cuentas cerradas');
            $('#cuentasCerradas').prop("disabled", false);
            $('#cuentasAbiertas').prop("disabled", false);
            $(".calculosTransacciones").hide();
        }
        $("#mdl_rdn_pdd_crgnd").hide();
    });
}

function fn_txTarjetas() {
    $("#cambiarDatosCliente").hide();
    $("#visualizarFactura").css('display', 'block');
    $('#visualizarFactura').show();
    $("#verFormasPago").hide();
    $("#imprimirTransaccion").hide();
    $("#anularOrden").hide();
    $('#anularOrden').prop("disabled", false);
    Cod_FacturaRetomar = 0;
    $("#retomarOrden").hide();
    $('#retomarOrden').prop("disabled", true);
    $("#parBusqueda").val('');
    $("#parBusqueda").prop('disabled', false);
    opcion = 1;
    $("#listado2").hide();
    $("#listado").hide();
    $("#listadoTxTarjetas").show();
    cargando(true);
    mostrarOpciones("tarjetas");
    var send = {"txTarjetas": 1};
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            var totalventas = 0;
            $("#calculoTransNum").empty();
            $("#calculoTransTotal").empty();
            $("#listadoTxTarjetas ul").empty();
            for (i = 0; i < datos.str; i++) {
                html = "<li id='" + datos[i]['cfac_id'] + "' onclick='fn_seleccionarTxTarjetas(\"" + datos[i]['IDCanalMovimiento'] + "\",\"" + datos[i]['rsaut_id'] + "\"," + datos[i]['anulado'] + ")'><div class='listaFactura'><b>" + datos[i]['tipo'] + "</b></div><div class='listaMesa'>" + datos[i]['fpf_total_pagar'] + "</div><div class='listaComentario'>" + datos[i]['fmp_descripcion'] + "</div><div class='listaComentario'>" + datos[i]['fecha'] + "</div><div class='listaComentario'>" + datos[i]['hora'] + "</div></li>";
                $("#listadoTxTarjetass").append(html);
            }
            $('#cuentasAbiertas').prop("disabled", false);
            $('#cuentasCerradas').prop("disabled", false);
        } else {
            $("#listadoTxTarjetass").html("");
            alertify.alert('No existen Transacciones con Tarjetas');
            $('#cuentasCerradas').prop("disabled", false);
            $('#cuentasAbiertas').prop("disabled", false);
        }
        cargando(false);
    });
}

function fn_modificarLista(ncre_id, cfac_id) {
    if ($("#impuesto_fac_" + cfac_id).val() !== undefined && parseInt($("#impuesto_fac_" + cfac_id).val()) > parseInt($("#hide_porcentajeImpuesto").val()) ) {
        alertify.alert(
            "<p style='font-size: 17px; margin-bottom: 15px; line-height: 2;'>Actualmente se encuentra en un Periodo con un impuesto menor al de la factura seleccionada, En estos momentos no podra realizar un Cambio de Datos de Cliente para esta factura.</p>", 
            function(){ alertify.message('OK'); }
        );
        return false;
    }
    
    $("#hid_notaCredito").val(ncre_id);
    $('#listadoPedido li').removeClass('focus');
    $("#" + cfac_id).addClass("focus");
    $("#hid_notaCredito").val(ncre_id);
    Cod_Factura = cfac_id;
    $('#visualizarFactura').prop("disabled", false);
    $('#verFormasPago').prop("disabled", false);
    $("#imprimirTransaccion").prop("disabled", false);
    if (cfac_id.substring(4, 5) == 'F') {
        $('#verFormasPago').prop("disabled", false);
    } else {
        $('#verFormasPago').prop("disabled", true);
    }
    if (ncre_id != 0) {
        $('#anularOrden').prop("disabled", true);
    } else {
        $('#anularOrden').prop("disabled", false);
    }
    $('#listadoPedido li').live('click', function () {
        $(this).addClass('focus');
    });
    $('#listadoPedido li.focus').click(function () {
        $(this).removeClass('focus');
    });
}

function fn_seleccionarTxTarjetas(idcanaM, idRespuesta, anulado) {
    idCanalMovimiento = idcanaM;
    idRespuestaVoucher = idRespuesta;
    banderaImprimeAnulado = anulado;
    $('#visualizarFactura').prop("disabled", false);

    $('#listadoTxTarjetass li').removeClass('focus');
    $('#listadoTxTarjetass li').live('click', function () {
        $(this).addClass('focus');
    });
    $('#listadoTxTarjetass li.focus').click(function () {
        $(this).removeClass('focus');
    });
}

/* Funcion que valida la selecciÃ³n obligatoria de una factura para realizar una acciÃ³n */
fn_validarAnulacion = function (opcion) {

    if (!($('li.focus').length)) {
        alertify.error('Seleccione una Factura.');
    } else {
        var cfac_id = $('#listadoPedido').find("li.focus").attr("id");

        var est_fac = $('#est_fac_' + cfac_id).val();
        var est_id = $('#hide_est_id').val();
        var notaCredito = $("#hid_notaCredito").val();
        var fidelizacion = $('#listadoPedido').find("li.focus").attr("planfidelizacion");
        if (fidelizacion === "FCP002") {
            alertify.error("No se puede anular esta transacción, porque pertenece a un canje de puntos.");
        } else if (fidelizacion === "VTL001") {
            alertify.error("No se puede anular esta transacción, porque pertenece a un canje de voucher VITALITY.");
        } else {
            if (notaCredito == 0) {
                fn_validaCajeroActivoParaAnulacion(cfac_id, opcion);
            } else {
                var cabeceramsj = 'Atenci&oacute;n!!';
                var mensaje = 'Est&aacute; factura ya ha sido anulada anteriormente.';
                fn_mensajeAlerta(cabeceramsj, mensaje, 0, '0');
            }
        }
    }
}

function fn_validaCajeroActivoParaAnulacion(factura, opcion) {
    if (opcion == 1) {
        var send = {"validaCajeroActivoParaAnulacion": 1};
        send.facturaId = factura;
        $.ajax({
            async: false,
            type: "POST",
            dataType: 'json',
            contentType: "application/x-www-form-urlencoded",
            url: "config_anularOrden.php",
            data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    if (datos.estado == 'Activo') {
                        fn_dialogCredenciales(opcion);
                    } else {
                        alertify.alert(datos.mensaje);
                        return false;
                    }
                } else {
                    alertify.error('ERROR');
                    return false;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(jqXHR);
                alert(textStatus);
                alert(errorThrown);
            }
        });
    } else {
        fn_dialogCredenciales(opcion);
    }
}

/* Modal credenciales de administrador */
function fn_dialogCredenciales(opcionBoton) {
    $('#anularOrden').prop("disabled", true);
    $("#anulacionesContenedor").show();
    $("#anulacionesContenedor").dialog({
        modal: true,
        position: "center",
        closeOnEscape: false,
        width: 500,
        height: 440,
        draggable: false,
        open: function () {
            $(".ui-dialog-titlebar").hide();
            $('#usr_clave').attr('onchange', 'fn_validarUsuario(' + opcionBoton + ');');
            fn_numerico("#usr_clave");
        }
    });
}

/* Funcion que permite visualizar una alerta */
function fn_mensajeAlerta(cabecera, mensaje, evento, objeto) {

    var msj = '<h3>' + cabecera + '</h3> <br> <h3>' + mensaje + '</h3> <br><br>';

    alertify.alert(msj, function (e) {
        if (e) {
            if (evento == 1) {
                $(objeto).val("");
                $(objeto).focus();
            }
        }
    });
}

function validaAdminReimpresiones() {
    $("#numPad").hide();
    var usr_clave = $("#usr_clave").val();

    if (usr_clave.indexOf('%') >= 0) {
        var old_usr_clave = usr_clave.split('?;')[0];
        var new_usr_clave = old_usr_clave.replace(new RegExp("%", 'g'), "");
        var usr_tarjeta = new_usr_clave;
        usr_clave = 'noclave';
    } else {
        var usr_tarjeta = 0;
    }

    var rst_id = $("#hide_rst_id").val();
    send = {"validarUsuario": 1};
    send.rst_id = rst_id;
    send.usr_clave = usr_clave;
    send.usr_tarjeta = usr_tarjeta;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            $("#anulacionesContenedor").dialog("close");
            lc_userAdmin = datos.usr_id;
            $("#usr_clave").val("");
            send = {"reimpresion_factura": 1};
            send.canal = idCanalMovimiento;
            send.usuarioAdminR = lc_userAdmin;
            $.getJSON("config_anularOrden.php", send, function (datos) {
                idCanalMovimiento = '';
                idRespuestaVoucher = '';
                alertify.success('Imprimiendo...');
                fn_cerrarVisorFacturas();
            });
        } else {
            fn_numerico("#usr_clave");
            alertify.set({buttonFocus: "none"});
            $("#usr_clave").focus();
            $("#usr_clave").val("");
            alertify.error('Clave no autorizada...');
        }
    });
}

function fn_validarUsuario(opcion) {
    $("#numPad").hide();
    var usr_clave = $("#usr_clave").val();
    var cfac_id = $('#listadoPedido').find("li.focus").attr("id");
    var usr_tarjeta;

    if (usr_clave.indexOf('%') >= 0) {
        var old_usr_clave = usr_clave.split('?;')[0];
        var new_usr_clave = old_usr_clave.replace(new RegExp("%", 'g'), "");
        usr_tarjeta = new_usr_clave;
        usr_clave = 'noclave';
    } else {
        usr_tarjeta = 0;
    }

    if (usr_clave != "") {
        var send;
        var rst_id = $("#hide_rst_id").val();
        send = {"validarUsuario": 1};
        send.rst_id = rst_id;
        send.usr_clave = usr_clave;
        send.usr_tarjeta = usr_tarjeta;
        $.getJSON("config_anularOrden.php", send, function (datos) {
            if (datos.str > 0) {
                $("#anulacionesContenedor").dialog("close");
                lc_userAdmin = datos.usr_id;
                $("#usr_clave").val("");

                if (opcion == 1) {
                    $("#hide_opcion_nota_credito").val(0);
                    fn_motivoAnulacion(cfac_id, 1, lc_userAdmin);
                } else if (opcion == 2) {
                    $("#hide_opcion_nota_credito").val(1);
                    fn_modalDatosCliente(cfac_id, lc_userAdmin);
                }
            } else {
                fn_numerico("#usr_clave");
                var cabeceramsj = 'Atenci&oacute;n!!';
                var mensaje = 'Clave incorrecta vuelva a intentarlo.';
                fn_mensajeAlerta(cabeceramsj, mensaje, 1, "#usr_clave");
            }
        });
    } else {
        fn_numerico("#usr_clave");
        var cabeceramsj = 'Atenci&oacute;n!!';
        var mensaje = 'Ingrese su clave de administrador(a)!!!!!!.';
        fn_mensajeAlerta(cabeceramsj, mensaje, 1, "#usr_clave");
    }
}

function trim(cadena) {
    var retorno = cadena.replace(/^\s+/g, '');
    retorno = retorno.replace(/\s+$/g, '');
    return retorno;
}

function fn_verificarConfiguracionTipoEnvioEstacion(cfac_id, lc_userAdmin) {
    var pais_aplica_nc = $("#aplica_nc_sinconsumidor").val();
    var documento_con_datos = $('#listadoPedido').find("li.focus").attr("documentoConDatos");
    var opcion = $("#motivosAnulacion").val();
    var motivo = $("#motivoObservacion").val();
    motivo = trim(motivo);

    if (opcion != '0') {
        if (motivo.length > 0) {
            if (pais_aplica_nc == 1) {


                if (parseInt(documento_con_datos) == 1) {
                    localStorage.setItem('cedulaCliente', null);


                    fn_formasPago();


                } else {
                    fn_modalDatosCliente(cfac_id, lc_userAdmin);
                }
            } else {
                fn_formasPago();
            }

            $("#btn_anulacancela").show();
        } else {
            var cabeceramsj = 'Atenci&oacute;n!!';
            var mensaje = 'El Comentario de anulaci&oacute;n es obligatorio.';
            fn_mensajeAlerta(cabeceramsj, mensaje, 1, "#motivoObservacion");
        }
    } else {
        var cabeceramsj = 'Atenci&oacute;n!!';
        var mensaje = 'Seleccione un motivo de anulaci&oacute;n.';
        fn_mensajeAlerta(cabeceramsj, mensaje, 1, "#motivosAnulacion");
    }
}


/* Modal credenciales de administrador */
function fn_dialogPinPad() {
    let mensaje = '<center><img width="200" height="100" src="../imagenes/facturacion/pinpad.gif"/><br/>';
    mensaje += 'Confirmar valor en el pinpad </center>';
    fn_mensajeAlerta('Advertencia ', mensaje, 0, '0');

}


function fn_formasPago() {
    console.log("fn_formasPago");
    $('#anulacionesMotivo').hide();
    $('#anulacionesMotivo').dialog('close');
    $("#keyboard").hide();

    if (!($('li.focus').length)) {
        let cabeceramsj = 'Atenci&oacute;n!!';
        let mensaje = 'No ha seleccionado una factura para anular.';
        fn_mensajeAlerta(cabeceramsj, mensaje, 0, '0');
    } else {
        //Obtener tipo de factura "App o Web"
        let cfac_id = $('#listadoPedido').find("li.focus").attr("id");
        let tipo = $('#listadoPedido').find("li.focus").find(".listaComentario").find("p").html();
        $('#anularOrden').prop("disabled", true);
        sendValidacionServicio = {"validarServicioTercero": 1};
        sendValidacionServicio.medio = (tipo) ? tipo : 'Local';
        sendValidacionServicio.servicio = 'ANULAR';
        $.ajax({
            async: false,
            type: "POST",
            dataType: 'json',
            contentType: "application/x-www-form-urlencoded",
            url: "config_anularOrden.php",
            data: sendValidacionServicio,
            success: function (datosValidacion) {
                console.log("success");
                if (datosValidacion && datosValidacion == 'APLICA') {
                    console.log('Ingreso a Aplica');
                    console.log(datosValidacion);
                    let documento = ($("#txtClienteCI").val()) ? $("#txtClienteCI").val() : 'null';
                    documento = documento.trim();
                    $("#mdl_rdn_pdd_crgnd").show();
                    let send;
                    send = {metodo: "generarNotaCreditoApp"};
                    send.idFactura = cfac_id;
                    send.idMotivoAnulacion = $('#motivosAnulacion').val();
                    send.idUsuario = lc_userAdmin;
                    send.observacion = $('#motivoObservacion').val();
                    send.cedula = documento;
                    $.ajax({
                        async: false,
                        type: "POST",
                        url: "cambioEstado_TransaccionApp.php",
                        data: send,
                        dataType: "json",
                        success: function (datos) {
                            console.log("anular" + datos);
                            console.log(datos.codigo);
                            if (datos.codigo === 200) {
                                $("#mdl_rdn_pdd_crgnd").hide();
                                alertify.alert(datos.mensaje);
                                $('#anularOrden').prop("disabled", false);
                                fn_cuentasCerradas();
                                enviarTransaccionQPM();
                                cambioEstadoTradePorFactura(cfac_id, "Anulada"); //medio a se obtiene del cfac_id en back
                                impresionAnularOrden(cfac_id, datos);
                                dragonTailOrderCancel(cfac_id);
                            } else {
                                $("#mdl_rdn_pdd_crgnd").hide();
                                $('#anularOrden').prop("disabled", false);
                                alertify.alert('Error: ' + datos.mensaje + ', no se pudo generar la nota de crédito.');
                                console.log(datos);
                            }
                            $("#txtClienteCI").val('');
                        },
                        error: function () {
                            $('#anularOrden').prop("disabled", false);
                            $("#mdl_rdn_pdd_crgnd").hide();
                            alert("Error al generar nota de crédito...")
                        }
                    });
                } else {
                    console.log('Ingreso a No Aplica');
                    console.log(datosValidacion);
                    let send;
                    send = {"formasPago": 1};
                    send.cfac_id = cfac_id;
                    $.ajax({
                        async: false,
                        url: "config_anularOrden.php",
                        data: send,
                        dataType: "json",
                        success: function (datos) {
                            if (datos.str > 0) {
                                $(".anulacionesFormasTr").empty();
                                var est_id = $("#hide_est_id").val();
                                var rst_id = $("#hide_rst_id").val();
                                var usr_id = $("#hide_usr_id").val();

                                var existeOtroTipoPago = false;

                                for (var i = 0; i < datos.str; i++) {
                                    if (datos[i]['fpf_swt'] === 7) {
                                        existeOtroTipoPago = true;
                                        break;
                                    }
                                }

                                for (var i = 0; i < datos.str; i++) {
                                    var esEfectivo = datos[i]['fpf_swt'] === -1;
                                    var idBoton = 'btn_p' + datos[i]['fpf_id'];

                                    if (esEfectivo && existeOtroTipoPago) {
                                        /* html = "<td><button class='botonPago' id='" + idBoton + "' disabled>" + datos[i]['fmp_descripcion'] + "</button></td>"; */
                                        html = "<td><button class='botonPago' id='" + idBoton + "' onclick='alertify.error(\"Primero se deben anular las formas de pago de tarjetas.\");'>" + datos[i]['fmp_descripcion'] + "</button></td>";
                                    } else {
                                        html = "<td><button class='botonPago' id='" + idBoton + "' onclick='fn_validarSWT(03, \"" + datos[i]['cfac_id'] + "\", \"" + est_id + "\", " + rst_id + ", \"" + usr_id + "\", \"" + datos[i]['fmp_id'] + "\", \"" + datos[i]['fpf_id'] + "\", " + datos[i]['fpf_swt'] + ", \"" + datos[i]['tfp_id'] + "\", \"" + datos[i]['secuenciaConfigurada'] + "\", \"" + datos[i]['secuencia'] + "\", \"" + datos[i]['fmp_descripcion'].trim() + "\", \"" + datos[i]['cf_nombre'].trim() + "\")' >" + datos[i]['fmp_descripcion'] + "</button></td>";
                                    }

                                    $(".anulacionesFormasTr").append(html);
                                }
                                $("#anulacionesPago").show();
                                $("#anulacionesPago").dialog({
                                    modal: true,
                                    width: 700,
                                    height: 500,
                                    resize: false,
                                    opacity: 0,
                                    show: "explode",
                                    hide: "explode",
                                    duration: 500,
                                    open: function (event, ui) {
                                        $(".ui-dialog-titlebar").hide();
                                    }
                                });
                            } else {
                                fn_actulizarMotivoAnulacion();
                                enviarTransaccionQPM();
                                dragonTailOrderCancel(cfac_id);
                            }
                        }
                    });

                }

            }
        });

    }
}


var mensaje = "";

async function dragonTailOrderCancel(cfac_id){
    var rst_id = $("#hide_rst_id").val();
    var orderData = JSON.parse(await getCodApp(cfac_id));
    var codApp = orderData.codigo_app;
    var medio = orderData.medio;
    console.log("🚀 ~ codApp:", codApp);
    if (!codApp) {
        return false;
    }
    send = { codApp: codApp, restauranId:rst_id, accion:2, medio: medio }
    $.ajax({
        type: "POST",
        url: "../resources/module/domicilio/dragon-tail/orders.php",
        data: send,
        dataType: "json",
        success: function(data){
            alertify.success('dragontail order No: '+data.orders[0]['orderId']+' ha sido cancelada');
        },
        error: function(jqXHR, exception) {
            alertify.error("error "+jqXHR.responseText);
        }
    });
}
function getCodApp(cfac_id) {
    //var cfac_id = $('#listadoPedido').find("li.focus").attr("id");
    send= { cfac_id: cfac_id}
    return $.ajax({
        data: send,
        url: "../resources/module/domicilio/dragon-tail/getCodAppController.php",
        type: 'POST',
    });
};

function fn_actulizarMotivoAnulacion() {

    var cfac_id = $('#listadoPedido').find("li.focus").attr("id");
    var cliente = $("#hide_cliente").val();

    send = {"actualizarMotivoAnulacion": 1};
    send.cfac_id = cfac_id;
    send.mtv_id = $('#motivosAnulacion').val();
    send.motivo = $('#motivoObservacion').val();
    $.getJSON("config_anularOrden.php", send, function (datos) {
        send = {"insertaNotaDeCredito": 1};
        send.cfac_idA = cfac_id;
        send.mtv_idA = $('#motivosAnulacion').val();
        send.motivoA = $('#motivoObservacion').val();
        send.userAdmin = lc_userAdmin;
        send.cliente = cliente;
        $.getJSON("config_anularOrden.php", send, function (datos) {
            fn_restauranteCashless(cfac_id);
            let anulacionesPagoDialogIsOpen = ($('#anulacionesPago').hasClass('ui-dialog-content'));

            if (anulacionesPagoDialogIsOpen) $('#anulacionesPago').dialog('close');
            //$('#anulacionesPago').dialog('close');
            var cabeceramsj = 'Nota de Cr&eacute;dito';
            localStorage.setItem('cedulaCliente', '');

            mensaje = '.Factura Anulada Correctamente.';

            // dar de baja a transaccion firebase.
            fn_esFacturaPlanAmigos(cfac_id);

            //Anular Canjes de cupones QR
            fn_anularCanjesCupon(cfac_id, datos.cfac_id);


            //Anular turno del turnero
            if ($("#hide_turneroActivo").val() == '1') {
                // 1-> politica activado
                var txtNumFactura = cfac_id;
                var lc_send = new Object;
                lc_send['turneroAccion'] = 'anularTurno';
                lc_send['turneroURl'] = $("#hide_turneroURl").val();
                lc_send['transaccion'] = txtNumFactura;
                lc_send['estado'] = 'Preparando';
                lc_send['orden'] = txtNumFactura.slice(-2);
                lc_send['cliente'] = '';
                lc_send['clienteDocumento'] = '';
                lc_send['tipo'] = 'LOCAL';
                $.ajax({
                    async: true,
                    type: "POST",
                    dataType: "text",
                    contentType: "application/x-www-form-urlencoded",
                    url: "../facturacion/wsTurnero.php",
                    data: lc_send,
                    success: function (datos) {
                        console.log(datos);
                    }
                });
            }

            $("#hide_ncre_id").val(datos.cfac_id);

            if ((datos.tf_descripcion) == 'PLAN MARKET') {

                send = {"claveAcceso": 1};
                send.factt = datos.cfac_id;
                send.char = 'N';
                $.getJSON("config_anularOrden.php", send, function (datosGet) {
                    
                    if (datosGet == 0){
                        send = {"claveAcceso": 1};
                        send.factt = datos.cfac_id;
                        send.char = 'N';
                        $.getJSON("config_anularOrden.php", send, function (datos) {
                            console.log('Creado por segunda vez');
                        });
                    }

                    var apiImpresion = getConfiguracionesApiImpresion();

                    if(datos.aplicaEnEstacion == 1){

                        var result = new apiServicioImpresion('nota_credito', cfac_id, 0, datos);
                        var imprime = result["imprime"];
                        var mensaje = result["mensaje"];

                        if (!imprime) {
                            alertify.success('Imprimiendo Nota de credito...');
                            fn_cuentasCerradas();
                            fn_cargando(0);
                        } else {
                            alertify.success('Error al imprimir...');
                            fn_cuentasCerradas();
                            fn_cargando(0);
                        }

                    }else{

                        if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                            fn_cargando(1);
    
                            var result = new apiServicioImpresion('nota_credito', cfac_id);
                            var imprime = result["imprime"];
                            var mensaje = result["mensaje"];
    
                            if (!imprime) {
                                alertify.success('Imprimiendo Nota de credito...');
                                fn_cuentasCerradas();
                                fn_cargando(0);
    
                            } else {
                            
                                alertify.success('Error al imprimir...');
                                fn_cuentasCerradas();
                                fn_cargando(0);
    
                            }
    
    
                        } else{
    
                            
                        send = {"grabacanalmovimientoImpresionAnulacionElectronica": 1};
                        send.idfactura = $("#hide_ncre_id").val();
                        $.getJSON("config_anularOrden.php", send, function (datos) {
                            fn_cuentasCerradas();
                        });
    
    
                        }



                        
                    }
                    

                });
            }

            if ((datos.tf_descripcion) == 'PREIMPRESA') {
                send = {"grabaCanalImpresionAnulacionPreimpresa": 1};
                send.idfacturaPre = $('#listadoPedido').find("li.focus").attr("id");
                send.charNota = 'N';
                $.getJSON("config_anularOrden.php", send, function (datos) {
                    fn_cuentasCerradas();
                });
            }

            $("#countdown").attr("style", "display:none");
            $("#modalBloquearCargaCronometro").hide();
            cargando(0);
            fn_mensajeAlerta(cabeceramsj, mensaje, 0, '0');
        });
    });
}

function fn_esFacturaPlanAmigos(codigoFactura) {
    var puntosReversados = 0;
    $("#mdl_rdn_pdd_crgnd").show();
    send = {"esFacturaPlanAmigos": 1};
    send.cfac_id = codigoFactura;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_anularOrden.php",
        data: send,
        success: (datos) => {
            console.log(datos);
            if (datos.length === 0) {
                return;
            }
            let currentAnulacion = datos[0];
            if (currentAnulacion) {
                if (currentAnulacion["puntos"] > 0) {
                    send = {};
                    send.metodo = "anularCanjePuntos";
                    send.cfac_id = codigoFactura;
                    send.app = 'jvz';
                    $.ajax({
                        async: false,
                        type: "POST",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "../facturacion/clienteWSClientes.php",
                        data: send,
                        success: function (datos) {

                            //if (datos["message"] === "Ok") { //FIDELIZACION V2
                            if (datos["code"] === 200) {
                                cambiarMensaje(datos["data"]["pointsByTransaction"]); // 
                            } else {
                                cambiarMensajeER("(Puntos no reversados)");
                                alertify.error("No se pudo reversar los puntos");
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $("#mdl_rdn_pdd_crgnd").hide();
                            alert(jqXHR);
                            alert(textStatus);
                            alert(errorThrown);
                        }
                    });
                }
            }

        },
        error: (xhr, mess, th) => {
            console.log(xhr.responseText);
        }
    });

    $("#mdl_rdn_pdd_crgnd").hide();
}

function cambiarMensaje(puntos) {
    mensaje = "Factura Anulada Correctamente, \n" + puntos + " Puntos reversados.";
}

function cambiarMensajeER(mens) {
    mensaje = "Factura Anulada Correctamente, \n" + mens;
}

//** opcional si no ingresa a IF en fn_validarSWT */
function fn_validarAnulaPickup(cfac_id, fpf_id) {
    if (fpf_id == 'PICK UP TARJETA') {
        fn_anularOrdenEfectivopickup(cfac_id, fpf_id);  //FACTURA, 
        //anularPagoPickup(cfac_id, fpf_id);//anularPagoConsumoRecarga(cfac_id, fpf_id);
    }
}


function fn_payphoneObtieneTransaccionID(codigoFactura) {
    return new Promise(function (resolve, reject) {
        var send = {"payphoneObtieneTransaccionID": 1};
        send.cfac_id = codigoFactura;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_anularOrden.php",
            data: send,
            success: function (datos) {
                resolve(datos[0].transaccionID);
            }
        });


    })
}


function obtenerTokenPayphone() {
    return new Promise(function (resolve, reject) {
        var send = {"PayPhoneObtenerClaves": 1};
        send.restaurante = $("#hide_rst_id").val();
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../facturacion/config_facturacion.php",
            data: send,
            success: function (datos) {
                resolve(datos[0]["Token"]);
            }
        });
    })
}


function payphoneReverse(transaccionId, token) {
    return new Promise(function (resolve, reject) {
        var send = {"payphoneReverse": 1};
        send.transaccionId = transaccionId;
        send.token = token;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../facturacion/config_facturacion.php",
            data: send,
            success: function (datos) {

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


async function anularTransaccionPayPhone(codigoFactura, fpf_id) {

    $("#mdl_rdn_pdd_crgnd").show();

    var token = await obtenerTokenPayphone();

    var IDtransaccion = await fn_payphoneObtieneTransaccionID(codigoFactura);

    var resultadoReverse = await payphoneReverse(IDtransaccion, token);

    $("#mdl_rdn_pdd_crgnd").hide();

    if (resultadoReverse.estado === 200) {
        fn_anularOrdenEfectivo(codigoFactura, fpf_id);
    } else {
        alertify.error(resultadoReverse.mensaje);
    }

}


var apiConfigPayvalida = {
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../facturacion/config_payvalida.php",
    data: null
};


function fn_anularPagoPayvalida(cfac_id, fmp_id, fpf_id) {

    send = {};
    send.metodo = 'anular';
    send.idFactura = cfac_id;
    send.idFormaPago = fmp_id
    apiConfigPayvalida.data = send;


    $.ajax({
        ...apiConfigPayvalida,
        success: function (datos) {
            console.log('DATOS ANULAR');
            console.log(datos);

            if (datos && datos.CODE && datos.CODE == '0000' && datos.DATA) {

                fn_anularOrdenEfectivo(cfac_id, fpf_id);
                servicioApiAperturaCajon(fmp_id);

            } else if (datos && datos.CODE && datos.CODE != '0000') {
                alertify.alert("NO se ha podido ANULAR el cobro por " + datos.DESC);
            } else {
                alertify.alert("NO se ha podido ANULAR el cobro ");
            }
        },
        error(e1, e2, e3) {
            alertify.alert("No se ha podido ANULAR el cobro ");
        }
    });

}
 

function fn_validarSWT(tipo_trans, cfac_id, est_id, rst_id, usr_id, fmp_id, fpf_id, fpf_swt, tfp_id, secuenciaConfigurada, secuencia,tipo='',cf_nombre='') {
    cargando(1);
    $("#countdown").attr("style", "display:block");
    $("#modalBloquearCargaCronometro").show();
    let send;
    tipo_trans = 'ANULACION';
    //EFECTIVO,RETENCIONES,CHEQUES    
    if (fpf_swt == -1) {
        if (tfp_id == 'CONSUMO RECARGA') {
            anularPagoConsumoRecarga(cfac_id, fpf_id);   //factura, id forma pago factura
        } else if (fpf_id == 'PICK UP TARJETA') {
            anularPagoPickup(cfac_id, fpf_id);//anularPagoConsumoRecarga(cfac_id, fpf_id);
        } else if ((tipo=='FIDELIZACION' && tfp_id == 'CREDITO EMPLEADO') ||(tipo=='CONSUMO RECARGA' && tfp_id == 'EFECTIVO')){ 
            fn_anularOrdenCreditoFIDELIZACION(cfac_id, fpf_id);
        } else if (cf_nombre=='AcumulaPTS' && tfp_id == 'EFECTIVO'){ 
            //fn_anularOrdenCreditoEfectivo(cfac_id, fpf_id);
            fn_anularOrdenEfectivo(cfac_id, fpf_id);
        } else if ((tfp_id == 'DE UNA')) {
            console.log("Anulacion de una");
            fn_anularPagoDeUna(cfac_id, fpf_id, fmp_id)
        }
        else {
            console.log("Anular Efectivo");
            fn_anularOrdenEfectivo(cfac_id, fpf_id);
            servicioApiAperturaCajon(fmp_id);
        }
    } else if ((fpf_swt == 8)) {
        anularTransaccionPayPhone(cfac_id, fpf_id);
    } else if ((fpf_swt == 10)) {
        fn_anularPagoPayvalida(cfac_id, fmp_id, fpf_id);
    } else if ((tfp_id == 'DE UNA')) {
        console.log("Anulacion de una");
        fn_anularPagoDeUna(cfac_id, fpf_id, fmp_id)
    } else {
        URL = "../facturacion/config_configuracionServicioTarjeta.php";
        try {
            let tipoEnvio = localStorage.getItem('servicio_tarjeta_aplica');

            if (tipoEnvio == 0) {
                SECUENCIAS_ANULACION = secuencia.split("->");
                if (SECUENCIAS_ANULACION[0] == 'Armar Trama') {
                    SECUENCIAS_ANULACION = SECUENCIAS_ANULACION.splice(1, SECUENCIAS_ANULACION.length - 1);
                    Armar_Trama_Dinamica('ANULACION', fpf_swt, SECUENCIAS_ANULACION, '', '', cfac_id, fpf_id, 'TRANSACCIONES');
                    fn_dialogPinPad();
                } else if (SECUENCIAS_ANULACION[0] == 'Anular_Pago') {
                    fn_anularOrdenEfectivo(cfac_id, fpf_id);
                } else if (SECUENCIAS_ANULACION[0] == 'Lectura_Tarjeta') {
                    SECUENCIAS_ANULACION = SECUENCIAS_ANULACION.splice(1, SECUENCIAS_ANULACION.length - 1);
                    $('#anulacionesPago').dialog('close');
                    alertify.success("Deslice la Tarjeta.");
                    $("#txt_trama").focus();
                    $("#txt_trama").keyup(function (event) {
                        if (event.keyCode == '13') {
                            Tipo_FormaPago = 'Credito';
                            formaPagoId = fpf_id;
                            if (SECUENCIAS_ANULACION[0] == 'Ingresar_Cvv') {
                                var caracTarjeta = $("#txt_trama").val();
                                SECUENCIAS_ANULACION = SECUENCIAS_ANULACION.splice(1, SECUENCIAS_ANULACION.length - 1);
                                fn_muestraTecladoCVV(tipo_trans, fpf_swt, SECUENCIAS_ANULACION, caracTarjeta, cfac_id, fpf_id, 'TRANSACCIONES');
                            }
                        }
                    });
                }
            } else {
                fn_dialogPinPad();

                $("#txtClienteCI").focus();
            
                let send = {
                    'tipo': 'ANULACION',
                    'dispositivo': fpf_swt,
                    'factura': cfac_id,
                    'valor': 0,
                    'valorPropina': 0,
                    'formaPagoIdentificador': fpf_id
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
                            fn_cancelarFormaPagoTransacciones("APROBADA",fpf_swt, datos.data.mensajeRespuesta, datos.data.rsautId, "TRANSACCIONES", fpf_id, cfac_id,fpf_id);
                        } else {
                            fn_cancelarFormaPagoTransacciones("NO APROBADO",fpf_swt, datos.data.mensajeRespuesta, datos.data.rsautId, "TRANSACCIONES", fpf_id, cfac_id, fpf_id);
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
        } catch (err) {
            console.log(err);
            alertify.error("No se consulto información politica servicio tarjeta");
        }
    }
}

function fn_cancelarFormaPagoTransacciones(respuestaAutorizacion, idDispositivo, respuesta, idRespuesta, modulo, fmp_id, factura, fpf_id) {
    if (modulo == 'FACTURACION') {
        URL = "config_facturacion.php";
    } else {
        URL = "../facturacion/config_facturacion.php";
    }

    //clearInterval(TEMPORIZADORUNIRED);
    lc_control = 1;
    cargando(1);

    if (respuestaAutorizacion == "APROBADA") {
        if (modulo == 'TRANSACCIONES') {
            send = { "cancelaTarjetaForma": 1 };
            send.can_respuesta = idRespuesta;
            send.cancela_codFact = factura;
            send.cancela_idPago = fpf_id;
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

                /* $("#countdown").countdown360().settings.seconds = 0; */
                /* $(countdown).countdown360().settings.onComplete = function(){   
                    if (typeof cargarCountDown === 'function') {
                        cargarCountDown(3);
                    }          
                }; */
                cleanIntervalModal();
            });
        }
    } else {
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
            $.getJSON(URL, send, function(datos) { });
            alertify.alert(respuesta);
            alertify.success('Imprimiendo Váucher...');
        }
        cleanIntervalModal();
        return false;
    }
}

function fn_anularOrdenCreditoEfectivo(cabfac_id, forpf_id){
    $('#anularOrden').prop("disabled", true);
    send = {"AnulacionFidelizacionEfectivo": 1};
    send.cfac_id = cabfac_id;
    send.mtv_id = $('#motivosAnulacion').val();
    send.cfac_observacion = $('#motivoObservacion').val();
    send.idFormaP = forpf_id;
    $.ajax({
        async: false,
        url: "config_anularOrden.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos && datos.str && datos.str > 0) {
                alertify.success('Forma de pago anulada correctamente.');
                fn_formasPago();
                $("#btn_anulacancela").hide();
            } else {
                alertify.error('Error al intentar anular la forma de pago seleccionada.');
                return false;
            }
        }
    });
}

function fn_anularOrdenCreditoFIDELIZACION(cabfac_id, forpf_id){
    $('#anularOrden').prop("disabled", true);
    send = {"AnulacionFidelizacion": 1};
    send.cfac_id = cabfac_id;
    send.mtv_id = $('#motivosAnulacion').val();
    send.cfac_observacion = $('#motivoObservacion').val();
    send.idFormaP = forpf_id;
    $.ajax({
        async: false,
        url: "config_anularOrden.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos && datos.str && datos.str > 0) {
                alertify.success('Forma de pago anulada correctamente.');
                fn_formasPago();
                $("#btn_anulacancela").hide();
            } else {
                if (datos && datos.mensaje && datos.mensaje.length > 0) {
                    alertify.error(datos.mensaje);
                    cargando(0);
                    $("#countdown").attr("style", "display:none");
                    $("#modalBloquearCargaCronometro").hide();
                }else{
                    alertify.error('Error al intentar anular la forma de pago seleccionada.');
                    cargando(0);
                    $("#countdown").attr("style", "display:none");
                    $("#modalBloquearCargaCronometro").hide();
                }
                return false;
            }
        }
    });
}

function fn_timeout() {
    myVar = setTimeout("fn_detenerProceso();", 90000);
}

function fn_detenerProceso() {
    cargando(1);
    lc_control = 1;

    var msj = '<h3>Atenci&oacute;n!!</h3> <br> <h3>Expiro el tiempo de espera, vuelva a intentarlo.</h3> <br><br>';

    alertify.alert(msj, function (e) {
        if (e) {
            lc_control = 0;
        }
    });
}

function fn_recibirSWT(cfac_id, fpf_id) {
    send = {"recibirSWT": 1};
    send.cfac_id = cfac_id;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.existe === 1) {
            if (datos.cres_codigo == '00') {
                lc_control = 1;
                cargando(1);
                clearInterval(TEMPORIZADOR_PINPAD);
                send = {"cancelaTarjetaForma": 1};
                send.can_respuesta = datos.rsaut_id;
                send.cancela_codFact = cfac_id;
                send.cancela_idPago = fpf_id;
                $.getJSON("config_anularOrden.php", send, function (datos) {
                    fn_formasPago();
                    Tipo_FormaPago = 0;
                    $("#btn_anulacancela").hide();
                });
            } else {
                lc_control = 1;
                cargando(1);
                var cabeceramsj = 'Atenci&oacute;n!!';
                var mensaje = "Anulacion no Aprobada." + datos.rsaut_respuesta;
                fn_mensajeAlerta(cabeceramsj, mensaje, 0, '0');
                //alertify.alert("Anulacion no Aprobada." + datos.rsaut_respuesta);
                $("#anulacionesPago").hide();
                //return  false;
            }
        }
    });
}

function fn_esperarSWT(cfac_id, fpf_id) {
    var send = {"recibirSWT": 1};
    send.cfac_id = cfac_id;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.existe == 2) {
            if (lc_control == 0) {
                fn_recibirSWT(cfac_id, fpf_id);
            } else {
                return false;
            }
        } else {
            if (datos.cres_codigo == '00') {
                lc_control = 1;
                cargando(1);
                fn_actualizaFormaPagoEliminada(cfac_id, fpf_id);
                fn_verificaMasFormasPagoTarjetas(cfac_id, fpf_id);
            } else {
                lc_control = 1;
                cargando(1);

                var cabeceramsj = 'Atenci&oacute;n!!';
                var mensaje = "Anulacion no Aprobada." + datos.rsaut_respuesta;
                fn_mensajeAlerta(cabeceramsj, mensaje, 0, '0');

                //alertify.alert("Anulacion no Aprobada." + datos.rsaut_respuesta);
                $("#anulacionesPago").hide();
                //return  false;
            }
        }
    });
}

function fn_actualizaFormaPagoEliminada(cabecera, forma) {
    send = {"actualizarEstadoFormaEliminada": 1};
    send.cfac_idEliminada = cabecera;
    send.formaEliminada = forma;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        return true;
    });
}

function fn_verificaMasFormasPagoTarjetas(cabFact, formPago) {
    clearTimeout(myVar);
    $('#anulacionesMotivo').hide();
    $('#anulacionesMotivo').dialog('close');
    if (!($('li.focus').length)) {
        alertify.alert('No ha seleccionado una factura para anular');
    } else {
        $('#anularOrden').prop("disabled", true);
        var cfac_id = $('#listadoPedido').find("li.focus").attr("id");
        send = {"formasPago": 1};
        send.cfac_id = cfac_id;
        $.ajax({
            async: false,
            url: "config_anularOrden.php",
            data: send,
            dataType: "json",
            success: function (datos) {
                if (datos.str > 0) {
                    var est_id = $("#hide_est_id").val();
                    var rst_id = $("#hide_rst_id").val();
                    var usr_id = $("#hide_usr_id").val();
                    for (i = 0; i < datos.str; i++) {
                        if (datos[i]['tfp_id'] == 1) {
                            fn_anularOrdenEfectivo(datos[i]['cfac_id'], datos[i]['fpf_id']);
                        } else {
                            if (datos[i]['fpf_swt'] == 1) {
                                if (datos[i]['fmp_descripcion'] == 'CONSUMO RECARGA') datos[i]['tfp_id'] = datos[i]['fmp_descripcion'];
                                html = "<button class='botonPago' id='btn_p" + datos[i]['fpf_id'] + "' onclick='fn_validarSWT(03,\"" + datos[i]['cfac_id'] + "\", \"" + est_id + "\", " + rst_id + ", \"" + usr_id + "\", \"" + datos[i]['fmp_id'] + "\", \"" + datos[i]['fpf_id'] + "\", " + datos[i]['fpf_swt'] + ", \"" + datos[i]['tfp_id'] + "\")' >" + datos[i]['fmp_descripcion'] + "</button>";
                                $(".anulacionesFormas").append(html);
                                $("#anulacionesPago").show();
                                $("#anulacionesPago").dialog({
                                    modal: true,
                                    width: 700,
                                    height: 500,
                                    resize: false,
                                    opacity: 0,
                                    show: "explode",
                                    hide: "explode",
                                    duration: 500,
                                    open: function (event, ui) {
                                        $(".ui-dialog-titlebar").hide();
                                    }
                                });
                            } else {
                            }
                        }
                    }
                }
                $('#anulacionesPago').dialog('close');
                fn_anularOrdenTarjetaSinSwitch(cabFact, formPago);
            }
        });
    }
}

function fn_motivoAnulacion(cfac_id, fpf_id, id_admin) {
    if (!($('li.focus').length)) {
        alertify.alert('No ha seleccionado una factura para eliminar');
    } else {
        fn_alfaNumerico('#motivoObservacion');
        $("#anulacionesMotivo").show();
        $("#anulacionesMotivo").dialog({
            modal: true,
            width: 510,
            height: 440,
            position: {
                my: 'top',
                at: 'top+80'
            },
            resize: false,
            opacity: 0,
            show: "none",
            hide: "none",
            open: function (event, ui) {
                $("#motivosAnulacion").val(0);
                $('#motivoObservacion').val("");
                $('#btn_ok_teclado').attr('onclick', 'fn_verificarConfiguracionTipoEnvioEstacion(\"' + cfac_id + '\", \"' + id_admin + '\")');
                $('#btn_cancelar_teclado').attr('onclick', 'fn_cerrarDialogoMotivo()');
                $(".ui-dialog-titlebar").hide();
            }
        });
        adaptarParaDispositivoMovil();
    }
}

const adaptarParaDispositivoMovil = () => {
    if (screen.width <= 1280 && screen.height <= 800) {//Tablet Hyundai Koral 10XL o mas pequeña 
        let cajaMotivo = document.getElementById('anulacionesMotivo').parentNode;
        let tecladoTactilMotivo = document.getElementById('keyboard');
        let estilosCajaMotivo = window.getComputedStyle(cajaMotivo);
        let topCaja = estilosCajaMotivo.getPropertyValue('top');
        cajaMotivo.style.left = '1rem';
        tecladoTactilMotivo.style.left = '35rem'
        tecladoTactilMotivo.style.top = topCaja;
    }
}


function fn_anularOrden(cabfac_id, forpf_id) {
    $('#anulacionesMotivo').hide();
    $('#anulacionesMotivo').dialog('close');
    if (!($('li.focus').length)) {
        alertify.alert('No ha seleccionado una factura para anular');
    } else {
        $('#anularOrden').prop("disabled", true);
        var cfac_id = cabfac_id;
        var fpf_id = forpf_id;
        var mtv_id = $('#motivosAnulacion').val();
        var cfac_observacion = $("#motivoObservacion").val();

        send = {"anularOrden": 1};
        send.cfac_id = cfac_id;
        send.mtv_id = mtv_id;
        send.cfac_observacion = cfac_observacion;
        $.ajax({
            async: false,
            url: "config_anularOrden.php",
            data: send,
            dataType: "json",
            success: function (datos) {
                if (datos.str > 0) {
                    console.log(datos);
                    send = {"generarNotaCredito": 1};
                    send.std_id = datos[0]['std_id'];
                    send.usr_id = datos[0]['usr_id'];
                    send.est_id = datos[0]['est_id'];
                    send.rst_id = datos[0]['rst_id'];
                    send.cli_id = datos[0]['cli_id'];
                    send.mtv_id = datos[0]['mtv_id'];
                    send.ncre_numero_factura = datos[0]['cfac_numero_factura'];
                    send.ncre_subtotal = datos[0]['cfac_subtotal'];
                    send.ncre_base_iva = datos[0]['cfac_base_iva'];
                    send.ncre_base_cero = datos[0]['cfac_base_cero'];
                    send.ncre_iva = datos[0]['cfac_iva'];
                    send.ncre_total = datos[0]['cfac_total'];
                    send.tcp_id = datos[0]['tcp_id'];
                    send.ncre_claveAcceso = datos[0]['cfac_claveAcceso'];
                    send.prd_id = datos[0]['prd_id'];
                    send.ctrc_id = datos[0]['ctrc_id'];
                    send.cfac_id = cfac_id;
                    send.fpf_id = fpf_id;
                    $.ajax({
                        async: false,
                        url: "config_anularOrden.php",
                        data: send,
                        dataType: "json",
                        success: function (datos) {
                            if (datos.str > 0) {
                                $(".anulacionesFormas #btn_p" + fpf_id + "").remove();
                                $("#anulacionesMotivo .anulacionesSubmit").empty();
                            } else {
                                $("#listado ul").empty();
                                fn_cerrarDialogoPago();
                            }
                        }
                    });
                }
            }
        });
        $('#anularOrden').prop("disabled", false);
        $("#keyboard").hide();
        $("#calculoTransNum").empty();
        $("#calculoTransTotal").empty();
        $(".calculosTransacciones").hide();
        $('#motivoObservacion').val('');
        $('#cuentasAbiertas').prop("disabled", false);
        $('#cuentasCerradas').prop("disabled", false);
    }
}

function fn_anularOrdenEfectivo(cabfac_id, forpf_id) {
    $('#anularOrden').prop("disabled", true);
    let cfac_id = cabfac_id;
    let fpf_id = forpf_id;
    send = {"anularOrden": 1};
    send.cfac_id = cfac_id;
    send.mtv_id = $('#motivosAnulacion').val();
    send.cfac_observacion = $('#motivoObservacion').val();
    send.idFormaP = forpf_id;
    send.IDUsersPos = $("#hide_usr_id").val();
    $.ajax({
        async: false,
        url: "config_anularOrden.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {
                alertify.success('Forma de pago anulada correctamente.');
                fn_formasPago();
                $("#btn_anulacancela").hide();
                console.log("TERMINO ANULACIOn fn_anularOrdenEfectivo")
            } else {
                alertify.error('Error al intentar anular la forma de pago seleccionada.');
                return false;
            }
        }
    });
}

function fn_facturacion() {
    fn_timeout2();
    fn_esperaXMLfirmado();
}

function fn_timeout2() {
    setTimeout("fn_detenerProcesoEsperaFirma();", 10000);
}

function fn_detenerProcesoEsperaFirma() {
    cargando(1);
    lc_control_firma = 1;

    var msj = '<h3>Atenci&oacute;n!!</h3> <br> <h3>Error en el firmado del comprobante. Tiempo de espera agotado.</h3> <br><br>';

    alertify.alert(msj, function (e) {
        if (e) {
            cargando(1);
        }
    });
}

function fn_anularOrdenTarjetaSinSwitch(cabfac_id, forpf_id) {
    $('#anularOrden').prop("disabled", true);
    var cfac_id = cabfac_id;
    var fpf_id = forpf_id;
    send = {"anularOrden": 1};
    send.cfac_id = cfac_id;
    send.mtv_id = 1;
    send.cfac_observacion = 'Anulacion Tarjeta';
    $.ajax({
        async: false,
        url: "config_anularOrden.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {
                send = {"generarNotaCredito": 1};
                send.std_id = datos[0]['std_id'];
                send.usr_id = datos[0]['usr_id'];
                send.est_id = datos[0]['est_id'];
                send.rst_id = datos[0]['rst_id'];
                send.cli_id = datos[0]['cli_id'];
                send.mtv_id = datos[0]['mtv_id'];
                send.ncre_numero_factura = datos[0]['cfac_numero_factura'];
                send.ncre_subtotal = datos[0]['cfac_subtotal'];
                send.ncre_base_iva = datos[0]['cfac_base_iva'];
                send.ncre_base_cero = datos[0]['cfac_base_cero'];
                send.ncre_iva = datos[0]['cfac_iva'];
                send.ncre_total = datos[0]['cfac_total'];
                send.tcp_id = datos[0]['tcp_id'];
                send.ncre_claveAcceso = datos[0]['cfac_claveAcceso'];
                send.prd_id = datos[0]['prd_id'];
                send.ctrc_id = datos[0]['ctrc_id'];
                send.cfac_id = cfac_id;
                send.fpf_id = fpf_id;
                $.ajax({
                    async: false,
                    url: "config_anularOrden.php",
                    data: send,
                    dataType: "json",
                    success: function (datos) {
                        if (datos.str > 0) {
                            $("#anulacionesMotivo .anulacionesSubmit").empty();
                        } else {
                            send = {"obtienencreid": 1};
                            send.cfactura_id = cfac_id;
                            $.ajax({
                                async: false,
                                url: "config_anularOrden.php",
                                data: send,
                                dataType: "json",
                                success: function (datos) {
                                    $("#hide_ncre_id").val(datos.ncre_id);
                                    tipo_facturacion = datos.tf_id;
                                    if (tipo_facturacion == 1) {
                                        fn_crearAnulacionXml(datos.ncre_id);
                                        fn_facturacion();
                                    } else {
                                        send = {"anulaFacturaSinFacturacionElectronica": 1};
                                        send.cfactura_idD = cfac_id;
                                        $.ajax({
                                            async: false,
                                            url: "config_anularOrden.php",
                                            data: send,
                                            dataType: "json",
                                            success: function (datos) {
                                                alertify.alert("ANULADA");
                                                cfac_idFactura = $("#hide_ncre_id").val();
                                                send = {"grabacanalmovimientoImpresionAnulacionElectronica": 1};
                                                send.idfactura = cfac_idFactura;
                                                $.ajax({
                                                    async: false,
                                                    type: "GET",
                                                    dataType: "json",
                                                    contentType: "application/x-www-form-urlencoded",
                                                    url: "config_anularOrden.php",
                                                    data: send,
                                                    success: function (datos) {
                                                        fn_cuentasCerradas();
                                                    }
                                                })
                                            }
                                        })
                                    }
                                }
                            });
                        }
                    }
                });
            }
        }
    });
    $('#anularOrden').prop("disabled", false);
    $("#keyboard").hide();
    $("#calculoTransNum").empty();
    $("#calculoTransTotal").empty();
    $(".calculosTransacciones").hide();
    $('#motivoObservacion').val('');
    $('#cuentasAbiertas').prop("disabled", false);
    $('#cuentasCerradas').prop("disabled", false);
}

function fn_anularOrdenTarjeta(cabfac_id, forpf_id) {
    $('#anularOrden').prop("disabled", true);
    var cfac_id = cabfac_id;
    var fpf_id = forpf_id;
    send = {"anularOrden": 1};
    send.cfac_id = cfac_id;
    send.mtv_id = 1;
    send.cfac_observacion = 'Anulacion Tarjeta';
    $.ajax({
        async: false,
        url: "config_anularOrden.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {
                send = {"generarNotaCredito": 1};
                send.std_id = datos[0]['std_id'];
                send.usr_id = datos[0]['usr_id'];
                send.est_id = datos[0]['est_id'];
                send.rst_id = datos[0]['rst_id'];
                send.cli_id = datos[0]['cli_id'];
                send.mtv_id = datos[0]['mtv_id'];
                send.ncre_numero_factura = datos[0]['cfac_numero_factura'];
                send.ncre_subtotal = datos[0]['cfac_subtotal'];
                send.ncre_base_iva = datos[0]['cfac_base_iva'];
                send.ncre_base_cero = datos[0]['cfac_base_cero'];
                send.ncre_iva = datos[0]['cfac_iva'];
                send.ncre_total = datos[0]['cfac_total'];
                send.tcp_id = datos[0]['tcp_id'];
                send.ncre_claveAcceso = datos[0]['cfac_claveAcceso'];
                send.prd_id = datos[0]['prd_id'];
                send.ctrc_id = datos[0]['ctrc_id'];
                send.cfac_id = cfac_id;
                send.fpf_id = fpf_id;
                $.ajax({
                    async: false,
                    url: "config_anularOrden.php",
                    data: send,
                    dataType: "json",
                    success: function (datos) {
                        if (datos.str > 0) {
                            $("#anulacionesMotivo .anulacionesSubmit").empty();
                        } else {
                            send = {"obtienencreid": 1};
                            send.cfactura_id = cfac_id;
                            $.ajax({
                                async: false,
                                url: "config_anularOrden.php",
                                data: send,
                                dataType: "json",
                                success: function (datos) {
                                    $("#hide_ncre_id").val(datos.ncre_id);
                                    tipo_facturacion = datos.tf_id;
                                    if (tipo_facturacion == 1) {
                                        fn_crearAnulacionXml(datos.ncre_id);
                                        fn_facturacion();
                                    } else {
                                        send = {"anulaFacturaSinFacturacionElectronica": 1};
                                        send.cfactura_idD = cfac_id;
                                        $.ajax({
                                            async: false,
                                            url: "config_anularOrden.php",
                                            data: send,
                                            dataType: "json",
                                            success: function (datos) {
                                                alertify.alert("ANULADA");
                                                cfac_idFactura = $("#hide_ncre_id").val();
                                                send = {"grabacanalmovimientoImpresionAnulacionElectronica": 1};
                                                send.idfactura = cfac_idFactura;
                                                $.ajax({
                                                    async: false,
                                                    type: "GET",
                                                    dataType: "json",
                                                    contentType: "application/x-www-form-urlencoded",
                                                    url: "config_anularOrden.php",
                                                    data: send,
                                                    success: function (datos) {
                                                        fn_cuentasCerradas();
                                                    }
                                                })
                                            }

                                        })
                                    }
                                }
                            });
                        }
                    }
                });
            }
        }
    });
    $('#anularOrden').prop("disabled", false);
    $("#keyboard").hide();
    $("#calculoTransNum").empty();
    $("#calculoTransTotal").empty();
    $(".calculosTransacciones").hide();
    $('#motivoObservacion').val('');
    $('#cuentasAbiertas').prop("disabled", false);
    $('#cuentasCerradas').prop("disabled", false);
}

function fn_cerrarDialogoAnulacion() {
    $('#usr_clave').val('');
    $('#motivoObservacion').val('');
    $("#anulacionesMotivo .anulacionesSubmit").empty();
    $("#numPad").hide();
    $("#keyboard").hide();
    $('#anularOrden').prop("disabled", false);
    $('#anulacionesContenedor').dialog('close');
}

function fn_cerrarDialogoPago() {
    $('#anulacionesPago').dialog('close');
    $('#usr_clave').val('');
    $('#motivoObservacion').val('');
    $(".anulacionesFormasTr").empty();
    $("#anulacionesMotivo .anulacionesSubmit").empty();
    $("#numPad").hide();
    $("#keyboard").hide();
    $('#anularOrden').prop("disabled", false);
}

function fn_crearAnulacionXml(codigoncredito) {
    $.post('xmlAnulacion.php', {txtNumNotaCredito: codigoncredito});
}

function fn_cerrarDialogoMotivo() {
    $('#anulacionesMotivo').dialog('close');
    $('#usr_clave').val('');
    $('#motivoObservacion').val('');
    $("#anulacionesMotivo .anulacionesSubmit").empty();
    $("#numPad").hide();
    $("#keyboard").hide();
    $('#anularOrden').prop("disabled", false);
}

function fn_funcionesGerente() {
    window.location.replace("../funciones/funciones_gerente.php");
}

function cargandoD(lc_estado) {
    if (lc_estado == 0) {
        $("#loading").dialog({
            maxHeight: 270,
            width: 300,
            title: 'Procesando...',
            resizable: false,
            position: "center",
            draggable: false,
            closeOnEscape: false,
            modal: true,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide();
            }
        });
    } else if (lc_estado == 1) {
        $("#loading").dialog("destroy");
    }
}


function fn_log() {
    $('#anularOrden').prop("disabled", true);
    $("#retomarOrden").hide();
    $('#retomarOrden').prop("disabled", true);
    $("#visualizarFactura").hide();
    $('#visualizarFactura').prop("disabled", true);
    $("#imprimirTransaccion").hide();
    $("#imprimirTransaccion").prop("disabled", true);
    $("#verFormasPago").hide();
    $('#verFormasPago').prop("disabled", true);
    $("#listado").hide();
    $("#listado2").show();
    var rst_id = document.getElementById("hide_rst_id").value
    send = {"logErrores": 1};
    send.rst_id = rst_id;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            $("#listado2 ul").empty();
            for (i = 0; i < datos.str; i++) {
                html = "<li id=" + datos[i]['cfac_id'] + " onclick='fn_modificarLista()'><div class='listaFactura2'>" + datos[i]['cfac_id'] + "</div><div class='listaEstadoDescripcion'>" + datos[i]['lg_estado'] + "</div><div class='listaMensaje'>" + datos[i]['lg_mensaje'] + "</div><div class='listaFecha'>" + datos[i]['lg_fecha'] + "</div></li>";
                $("#listadoPedido2").append(html);
            }
            $('#cuentasCerradas').prop("disabled", false);
            $('#cuentasAbiertas').prop("disabled", false);
        } else {
            alertify.alert('<h3>No existen Transacciones que mostrar.</h3>');
            $('#cuentasCerradas').prop("disabled", false);
            $('#cuentasAbiertas').prop("disabled", false);
        }
    });
}

function fn_muestraTecladoCVV(tipo_trans, fpf_swt, secuencia, caracTarjeta, cfac_id, fpf_id, modulo) {
    $("#txt_cvv").val('');
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
        }
    });
    $("#div_cvv").dialog("open");

    $("#fn_okCVV").unbind().click(function () {
        if ($("#txt_cvv").val() == "") {
            alertify.error("Ingrese CVV.");
            return false;
        }

        if (SECUENCIAS_ANULACION[0] == 'Armar_Trama') {
            $("#div_cvv").dialog("close");
            var longitud = 0;
            longitud = secuencia.length;
            SECUENCIAS_ANULACION = SECUENCIAS_ANULACION.splice(1, longitud);
            Armar_Trama_Dinamica(tipo_trans, fpf_swt, SECUENCIAS_ANULACION, caracTarjeta, $("#txt_cvv").val(), cfac_id, fpf_id, modulo);
        }
    });
}

function fn_canCVV() {
    $("#div_cvv").dialog("destroy");
}

function fn_eliminarCantidadCVV() {
    var lc_cantidad = $("#txt_cvv").val().substring(0, $("#txt_cvv").val().length - 1);
    if (lc_cantidad == "") {
        lc_cantidad = "";
        coma2 = 0;
    }
    if (lc_cantidad == ".") {
        coma2 = 0;
    }
    $("#txt_cvv").val(lc_cantidad);
}

function fn_focusLectorCVV() {
    $("#txt_cvv").focus();
}

function fn_agregarNumeroCVV(valor) {
    lc_cantidad = document.getElementById("txt_cvv").value;
    if (lc_cantidad == 0 && valor == ".") {
        document.getElementById("txt_cvv").value = "0.";
        coma2 = 1;
    } else {
        if (valor == "." && coma2 == 0) {
            lc_cantidad = lc_cantidad + valor;
            document.getElementById("txt_cvv").value = lc_cantidad;
            coma2 = 1;
        } else if (valor == "." && coma2 == 1) {
        } else {
            $("#txt_cvv").val('');
            lc_cantidad = lc_cantidad + valor;
            document.getElementById("txt_cvv").value = lc_cantidad;
        }
    }
    fn_focusLectorCVV();
}

function fn_okCVV() {
    $("#div_cvv").dialog("destroy");
    cargando(0);
    var tfp_id = Tipo_FormaPago;
    var cvv = $("#txt_cvv").val();
    if (tfp_id == 'Credito') {
        fn_armaTrama(Cod_Factura, 3, tfp_id, cvv);
    } else if (tfp_id == 3) {
        send = {"muestraTipoCuenta": 1};
        $.getJSON("config_anularOrden.php", send, function (datos) {
            if (datos.str > 0) {
                $("#div_tipoCuentaTarjeta").empty();
                html = "<table>";
                for (i = 0; i < datos.str; i++) {
                    html += "<tr><td><button style='height:60px; width:270px;' onclick='fn_armaTrama(\"" + Cod_Factura + "\", " + datos[i]['tptar_id'] + ", \"" + tfp_id + "\")' id='" + datos[i]['tptar_id'] + "'>" + datos[i]['tptar_descripcion'] + "</button></td></tr>";
                }
                html += "</table>";
                $("#div_tipoCuentaTarjeta").append(html);
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
    }
}

function fn_armaTrama(cfac_id, tptar, tfp_id, cvv) {
    send = {"armaTramaSWTbanda": 1};
    send.tipoTransaccion = '03';
    send.numMovimiento = cfac_id;
    send.formaIdPagoFact = tfp_id;
    send.tipoTarjeta = tptar;
    send.trackTarjeta = $("#txt_trama").val();
    send.cvvtarjeta = cvv;
    send.anuFormaPagoId = formaPagoId;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        $("#txt_trama").val('');
        lc_control = 0;
        cargando(0);
        fn_timeoutBanda();
        fn_esperaRespuestaBanda(cfac_id);
    });
}

function fn_esperaRespuestaBanda(cfac_id) {
    send = {"esperaRespuestaRequerimientoAutorizacion": 1};
    send.cfac = cfac_id;
    $.ajax({
        async: true,
        url: "config_anularOrden.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.existe == 2) {
                if (lc_control == 0) {
                    fn_esperarBanda(cfac_id);
                } else {
                    return false;
                }
            } else {
                fn_funcionMuestraRespuesta(datos.rsaut_respuesta, datos.cres_codigo, datos.fpf_id, datos.rsaut_id, datos.errorTrama);
            }
        }
    });
}

function fn_esperarBanda(cfac_id) {
    send = {"esperaRespuestaRequerimientoAutorizacion": 1};
    send.cfac = cfac_id;
    $.ajax({
        async: true,
        url: "config_anularOrden.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.existe == 2) {
                fn_esperaRespuestaBanda(cfac_id);
            } else {
                fn_funcionMuestraRespuesta(datos.rsaut_respuesta, datos.cres_codigo, datos.fpf_id, datos.rsaut_id, datos.errorTrama);
            }
        }
    });
}

function fn_funcionMuestraRespuestaUnired(respuesta, codigoRespuesta, formadePago, idRespuesta, tramaError) {
    var send;
    timeOutUnired = clearTimeout(timeOutUnired);
    lc_control = 1;
    cargando(1);
    clearInterval(TEMPORIZADOR_UNIRED);
    if (tramaError == 1) {
        alertify.alert(respuesta);
        if (codigoRespuesta == '00') {
            send = {"cancelaTarjetaForma": 1};
            send.can_respuesta = idRespuesta;
            send.cancela_codFact = Cod_Factura;
            send.cancela_idPago = formaPagoId;
            $.getJSON("config_anularOrden.php", send, function (datos) {
                fn_formasPago();
                Tipo_FormaPago = 0;
                $("#btn_anulacancela").hide();
            });
        } else {
            send = {"grabacanalmovimientoVoucher": 1};
            send.respuesta = idRespuesta;
            $.getJSON("config_anularOrden.php", send, function (datos) {
            });
            return false;
        }
    } else {
        alertify.alert(tramaError);
        return false;
    }
}

function fn_funcionMuestraRespuesta(respuesta, codigoRespuesta, formadePago, idRespuesta, tramaError) {
    var send;
    timeOut = clearTimeout(timeOut);
    lc_control = 1;
    cargando(1);
    if (tramaError == 1) {
        alertify.alert(respuesta);
        if (codigoRespuesta == '00') {
            send = {"cancelaTarjetaForma": 1};
            send.can_respuesta = idRespuesta;
            send.cancela_codFact = Cod_Factura;
            send.cancela_idPago = formaPagoId;
            $.getJSON("config_anularOrden.php", send, function (datos) {
                fn_formasPago();
                Tipo_FormaPago = 0;
                $("#btn_anulacancela").hide();
            });
        } else {
            send = {"grabacanalmovimientoVoucher": 1};
            send.respuesta = idRespuesta;
            $.getJSON("config_anularOrden.php", send, function (datos) {
            });
            return false;
        }
    } else {
        alertify.alert(tramaError);
        return false;
    }
}

function fn_timeoutBanda() {
    timeOut = setTimeout("fn_detenerProcesoBanda();", tiempoEspera);
}

function fn_timeoutUnired() {
    timeOutUnired = setTimeout("fn_detenerProcesoUnired();", tiempoEspera);
}

function fn_detenerProcesoUnired() {
    cargando(1);
    lc_control = 1;
    clearInterval(TEMPORIZADOR_UNIRED);

    var msj = '<h3>Atenci&oacute;n!!</h3> <br> <h3>Expiro el tiempo de espera, vuelva a intentarlo.</h3> <br><br>';

    alertify.alert(msj, function (e) {
        if (e) {
            lc_control = 0;
            Tipo_FormaPago = 0;
        }
    });
}

function fn_detenerProcesoBanda() {
    cargando(1);
    lc_control = 1;

    var msj = '<h3>Atenci&oacute;n!!</h3> <br> <h3>Expiro el tiempo de espera, vuelva a intentarlo.</h3> <br><br>';

    alertify.alert(msj, function (e) {
        if (e) {
            lc_control = 0;
            Tipo_FormaPago = 0;
        }
    });
}

function fn_esperaRespuestaUnired(cfac_id) {
    send = {"esperaRespuestaRequerimientoAutorizacion": 1};
    send.cfac = cfac_id;
    $.ajax({
        async: true,
        url: "config_anularOrden.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.existe === 1) {
                fn_funcionMuestraRespuestaUnired(datos.rsaut_respuesta, datos.cres_codigo, datos.fpf_id, datos.rsaut_id, datos.errorTrama);
            }
        }
    });
}

function fn_esperarUnired(cfac_id) {
    send = {"esperaRespuestaRequerimientoAutorizacion": 1};
    send.cfac = cfac_id;
    $.ajax({
        async: true,
        url: "config_anularOrden.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.existe == 2) {
                fn_esperaRespuestaUnired(cfac_id);
            } else {
                fn_funcionMuestraRespuesta(datos.rsaut_respuesta, datos.cres_codigo, datos.fpf_id, datos.rsaut_id, datos.errorTrama);
            }
        }
    });
}

function fn_obtenerMesa() {
    send = {"obtenerMesa": 1};
    send.odp_id = window.location.href.split('?')[1].split('=')[1];
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            if (datos.respuesta === 3) {
                $('#cntFormulario').html('<form action="../facturacion/factura.php" name="formulario" method="post" style="display:none;"><input type="text" name="odp_id" value="' + datos.IDOrdenPedido + '" /><input type="text" name="dop_cuenta" value="' + 0 + '" /><input type="text" name="mesa_id" value="' + datos.IDMesa + '" /></form>');
                document.forms['formulario'].submit();
            } else {
                window.location.href = "../ordenpedido/tomaPedido.php?numMesa=" + datos.IDMesa;
            }
        } else {
            alert("Este local no tiene mesas disponibles.");
        }
    });
}

/////////////////////////////////////////////////////////
////// METODO INTEGRACION PAYPHONE //////////////////////
/////////////////////////////////////////////////////////

//Cargar datos transaccion
function fn_cargarInformacionFactura(cfac_id) {
    alertify.confirm("Desea Anular está Factura", function (e) {
        if (e) {
            send = {"solicitarAnularTransaccion": 1};
            send.cfac_id = cfac_id;
            $.ajax({
                async: false,
                type: "POST",
                url: "config_payphone.php",
                data: send,
                dataType: "json",
                success: function (datos) {
                    if (datos.Status == 3) {
                        alertify.alert("Transaccion Anulada");
                        fn_notaCreditoFacturaOtros(cfac_id);
                    } else if (datos.Status == 2) {
                        alertify.alert("Terminó el tiempo de Espera. Intente nuevamente.");
                        fn_cargarInformacionFactura(cfac_id);
                    }
                }
            });
        }
    });
}

var anularPagoConsumoRecarga = function (cfac_id, descripcionFormaPago) {
    //alert(idFactura);
    descripcionFormaPago = "EFECTIVO";
    cargando(true);
    // cargando(1);
    //Codigo de la Factura
    var idFactura = $("#txtNumFactura").val();
    var send = {};
    send.metodo = "reversoConsumoRecargarCliente";
    send.idFactura = cfac_id;
    $.ajax({
        async: false,
        type: "POST",
        url: "../recargas/consultasRecargas.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            console.log(datos);
            if (datos.estado == 1) {
                $("#btn_anulacancela").hide();
                fn_formasPago();
            } else {
                alertify.alert("Error, " + datos.mensaje);
            }
            cargando(false);
        },
        error: function () {
            // cargando(0);
            cargando(false);
            alert("Error, no se puedo reversar el pago.");
        }
    });
};

//*********PICKUP ************   */
var anularPagopickup = function (cfac_id, descripcionFormaPago) {  // forpf_id
    //alert(idFactura);
    descripcionFormaPago = "DEBITO";
    cargando(true);
    // cargando(1);
    //Codigo de la Factura
    var idFactura = $("#txtNumFactura").val();
    var send = {};
    send.metodo = "reversoConsumoPickup";
    send.idFactura = cfac_id;
    $.ajax({
        async: false,
        type: "POST",
        url: "../recargas/consultasRecargas.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.estado == 1) {
                $("#btn_anulacancela").hide();
                fn_formasPago();
            } else {
                alertify.alert("Error, " + datos.mensaje);
            }
            cargando(false);
        },
        error: function () {
            // cargando(0);
            cargando(false);
            alert("Error, no se puedo reversar el pago.");
        }
    });
};


function fn_anularCanjesCupon(cfac_id, ncre_id) {
    console.log("NCRE: " + ncre_id);
    var send = {
        "accion": "anularCanjesFactura",
        "cfac_id": cfac_id,
        "ncre_id": ncre_id
    };
    $.ajax({
        async: false,
        type: "POST",
        url: "config_anulacion_cupones.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            console.log(datos);
            if (datos) {
                //alertify.alert(datos);
            }
        }
    });
}

function fn_recargas() {
    cargando(true);
    mostrarOpciones("reversoRecargas");
    var send = {"recargas": 1};
    $.ajax({
        async: false,
        type: "POST",
        url: "../anulacion/config_anularOrden.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            var html = "";
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = (datos[i]["estado"] === "Activo") ? "Activa" : "Reversada";
                    var activa = (estado === "Activa") ? 1 : 0;
                    html += "<li id=" + datos[i]["transaccion"] + " onclick='seleccionarRecarga(\"" + datos[i]["transaccion"] + "\")' data-activo=" + activa + "><div class='listaFactura'><b>&nbsp;" + datos[i]["transaccion"] + "</b></div><div class='listaCaja'>&nbsp;" + datos[i]["valor"] + "</div><div class='listaComentario'>&nbsp;" + datos[i]["cajero"] + "</div><div class='listaStatus'>&nbsp;" + estado + "</div><div class='listaCliente'>&nbsp;" + datos[i]["clienteDocumento"] + " - " + datos[i]["cliente"] + "</div></li>";
                }
            } else {
                fn_mensajeAlerta(CABECERAMSJ, "No existen recargas", 0, "0");
            }
            $("#listadoReversos").html(html);
            cargando(false);
        },
        error: function () {
            $("#listadoReversos").html("");
            alertify.error("Error al obtener las recargas");
            cargando(false);
        }
    });
}

function seleccionarRecarga(transaccion) {
    $("#listadoReversos li").removeClass("focus");
    $("#" + transaccion).addClass("focus");
}

function fn_validarReverso() {
    var $transaccion = $("#listadoReversos").find("li.focus");
    if ($transaccion.length > 0) {
        var transaccion = $transaccion.attr("id");
        var activo = $transaccion.data("activo");
        if (activo) {
            fn_modalReverso(transaccion);
        } else {
            alertify.error("La recarga ya se encuentra reversada");
        }
    } else {
        alertify.error("No se ha seleccionado ninguna recarga");
    }
}

function fn_modalReverso(transaccion) {
    $("#anulacionesContenedor").dialog({
        modal: true,
        position: "center",
        closeOnEscape: false,
        width: 500,
        height: 440,
        draggable: false,
        open: function () {
            $(".ui-dialog-titlebar").hide();
            $("#usr_clave").attr("onchange", "fn_validarUsuarioReverso('" + transaccion + "')");
            fn_numerico("#usr_clave");
        }
    }).show();
}

function fn_validarUsuarioReverso(transaccion) {
    var usr_tarjeta;
    var usr_clave = $("#usr_clave").val();
    $("#numPad").hide();
    if (usr_clave.indexOf("%") >= 0) {
        var old_usr_clave = usr_clave.split("?;")[0];
        var new_usr_clave = old_usr_clave.replace(new RegExp("%", "g"), "");
        usr_tarjeta = new_usr_clave;
        usr_clave = "noclave";
    } else {
        usr_tarjeta = 0;
    }
    if (usr_clave !== "") {
        var rst_id = $("#hide_rst_id").val();
        var send = {"validarUsuario": 1};
        send.rst_id = rst_id;
        send.usr_clave = usr_clave;
        send.usr_tarjeta = usr_tarjeta;
        $.getJSON("config_anularOrden.php", send, function (datos) {
            if (datos.str > 0) {
                $("#anulacionesContenedor").dialog("close");
                lc_userAdmin = datos.usr_id;
                $("#usr_clave").val("");
                fn_realizarReverso(transaccion);
            } else {
                fn_numerico("#usr_clave");
                var mensaje = "Clave incorrecta, vuelva a intentarlo.";
                fn_mensajeAlerta(CABECERAMSJ, mensaje, 1, "#usr_clave");
            }
        });
    } else {
        fn_numerico("#usr_clave");
        var mensaje = "Ingresar clave de administrador";
        fn_mensajeAlerta(CABECERAMSJ, mensaje, 1, "#usr_clave");
    }
}

function fn_realizarReverso(transaccion) {
    console.log("Realiza Reverso")
    var send = {"realizarReverso": 1};
    var postBody = {
        //          **** V2 *********
        //"type": "BALANCE REDEMPTION REVERSE",  //          **** V2 *********
        // "code": transaccion  //          **** V2 *********
        "balanceRedemptionCode": transaccion  //  **** V1 *********
    };
    send.postBody = JSON.stringify(postBody);
    $.ajax({
        async: false,
        type: "POST",
        url: "../anulacion/config_anularOrden.php",
        data: send,
        dataType: "json",
        success: function (datos) {
        },
        error: function () {
            alertify.error("Error al realizar el reverso de la recarga");
        }
    });
}

//cuentasAbiertas, cuentasCerradas, reversoRecargas, tarjetas
function mostrarOpciones(opcionS) {
    $("#parBusqueda").val("");
    Cod_FacturaRetomar = 0;
    if (opcionS === "cuentasAbiertas" || opcionS === "cuentasCerradas") {
        $("#busqueda").show();
        $("#listado").show();
    } else {
        opcion = 0;
        $("#busqueda").hide();
        $("#listado").hide();
        $(".calculosTransacciones").hide();
    }
    if (opcionS === "cuentasAbiertas") {
        opcion = 2;
        $("#retomarOrden").show();
        $("#retomarOrden").prop("disabled", false);
    } else {
        $("#retomarOrden").hide();
        $("#retomarOrden").prop("disabled", true);
    }
    if (opcionS === "cuentasCerradas") {
        opcion = 1;
        $("#cambiarDatosCliente").show();
        $("#retomarOrden").prop("disabled", false);
        $("#verFormasPago").show();
        $("#verFormasPago").prop("disabled", false);
        $("#anularOrden").show();
        $("#anularOrden").prop("disabled", false);
    } else {
        $("#cambiarDatosCliente").hide();
        $("#retomarOrden").prop("disabled", true);
        $("#verFormasPago").hide();
        $("#verFormasPago").prop("disabled", true);
        $("#anularOrden").hide();
        $("#anularOrden").prop("disabled", true);
    }
    if (opcionS === "reversoRecargas") {
        $("#divReversos").show();
        $("#reversarRecarga").show();
    } else {
        $("#divReversos").hide();
        $("#reversarRecarga").hide();
    }
    if (opcionS === "tarjetas") {
        $("#visualizarFactura").show();
        $("#visualizarFactura").prop("disabled", false);
        $("#listadoTxTarjetas").show();
    } else {
        $("#visualizarFactura").hide();
        $("#visualizarFactura").prop("disabled", true);
        $("#listadoTxTarjetas").hide();
    }

    if (opcionS === "cuentasPeriodosAnteriores") {
        $("#busquedaTransacciones").show();
        $("#opciones").css("height", "0px");
        $('#cuentasAbiertas').prop("disabled", false);
        $('#cuentasCerradas').prop("disabled", false);
    } else {
        $("#busquedaTransacciones").hide();
        $("#opciones").css("height", "402px");
    }
}

function cargando(cargando) {
    if (cargando) {
        $("#mdl_rdn_pdd_crgnd").show();
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
    }
}

function cargarCountDown(lc_estado) {
    if(lc_estado == 0)
    {
        //empiezaCronometro();
        tiempoT=$("#tiempoEspera").val();
        var contador=tiempoT/1000;
        $("#countdown").attr("style", "display:block;position: absolute; top: 40%; left: 43rem; z-index: 99999;");
        $("#modalBloquearCargaCronometro").show();

        $("#countdown").countdown360({
            radius      : 60.5,
            seconds     : contador,
            strokeWidth : 15,
            fillStyle   : '#0276FD',
            strokeStyle : '#003F87',
            fontSize    : 50,
            fontColor   : '#FFFFFF',
            label: ["segundos", "segundos"], 
            autostart: false,
            onComplete  : function () { console.log('completed'); }
        }).start();
    }
    if(lc_estado == 1){
            $("#loading").dialog("destroy");
    }
    if(lc_estado == 2){
          //empiezaCronometro();
          tiempoT2=$("#tiempoEspera").val(); 
          var contador2=tiempoT2/1000;
          $("#loading").dialog({
                maxHeight: 270,
                width: 600,
                title:'Existen transacciones pendientes, por favor espere',
                resizable: false,
                position: "center",
                draggable: false,
                closeOnEscape: false,                                    
                open: function(event, ui) { 
                $(".ui-dialog-titlebar-close").hide(); },
                modal: true
            });
            
            $("#countdown").countdown360({
            radius      : 60.5,
            seconds     : contador2,
            strokeWidth : 15,
            fillStyle   : '#0276FD',
            strokeStyle : '#003F87',
            fontSize    : 50,
            fontColor   : '#FFFFFF',
            label: ["segs", "segs"], 
            autostart: false,
            onComplete  : function () { console.log('completed'); }
          }).start();
    }

    if (lc_estado == 3 && $("#countdown").css("display") === "block") {
        $("#countdown").attr("style", "display:none");
        $("#modalBloquearCargaCronometro").hide();
    }
    
}

function validarAnulacionDeUna() {
    let cfac_id = $('#listadoPedido').find("li.focus").attr("id");
    console.log("De una");
    console.log(cfac_id);
    var flagDeunaValidacion = false;
    var cdn_id = $("#hide_cdn_id").val();
    var usr_id = $("#hide_usr_id").val();
    var rst_id = $("#hide_rst_id").val();
    var est_id = $("#hide_est_id").val();
    var send = { anularPagoDeUna: 1, cfac_id, cdn_id, usr_id, rst_id, est_id };
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        url: "../DeUna/solicitudValidacionAnulacionDeUna.php",
        data: send,
        beforeSend: function () {
            //cargandoDeUnaModal(1);
        },
        success: function (datos) {
            console.log("exito en consulta");
            console.log(datos);

            if (datos.status != undefined) {
                if (datos.status == 200) {
                    flagDeunaValidacion = true;
                } else {
                    Swal.fire({
                        title: 'Anulación de Facturas - DeUna',
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
                }
            } else {
                if (datos.error != undefined) {
                    Swal.fire({
                        title: 'Anulación de Facturas - DeUna',
                        text: "No se puede realizar la Nota de Credito, la fecha de ANULACION debe ser la MISMA que la fecha del PAGO.",
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
                } else {        
                    Swal.fire({
                        title: 'Anulación de Facturas - DeUna',
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
                }
            }
        },
        error: function (e) {
            //cargandoDeUnaModal(0);
            console.log("error");
            console.log(e);
        }
    });
    return flagDeunaValidacion;
}

function validarTransferencia() {
    localStorage.setItem('invalidoCredito',0);
    localStorage.setItem('nuevoCliente',0);

    let ValidacionAnulacionFacturaTiempoApp=$("#ValidacionAnulacionFacturaTiempoApp").val();
    let ValidacionAnulacionFacturaTiempoFast =$("#ValidacionAnulacionFacturaTiempoFast").val();
    
    if (!($('li.focus').length)) {
        alertify.error('Seleccione una Factura.');
    } else {
        if (!validarAnulacionDeUna()) {
            return false;
        }
        let cabeceramsj = 'Atenci&oacute;n!!';
        let mensaje = 'La factura no puede ser anulada, porqu&eacute; existen productos de transferencia.';

        let send;
        let cfac_id = $('#listadoPedido').find("li.focus").attr("id");
        send = {"valida_transferencia": 1,"cfac_id" : cfac_id} ;        
        $.ajax({
            async: false,
            type: "POST",
            url: "config_anularOrden.php",
            data: send,
            dataType: "json",
            success: function (datos) {
                if (datos.str > 0) {
                    if (datos.transferencia === 1) {
                        console.log('Factura con transferencia');
                        cabeceramsj = 'Atenci&oacute;n!!';
                        mensaje = 'La factura no puede ser anulada, porqu&eacute; existen productos de transferencia.';
                        fn_mensajeAlerta(cabeceramsj, mensaje, 0, '0');
                    } else {
                        if(ValidacionAnulacionFacturaTiempoFast!=0 || ValidacionAnulacionFacturaTiempoApp !=0 ){
                        send = {    "valida_tiempo"                         : 1,
                                    "cfac_id"                               : cfac_id, 
                                    "ValidacionAnulacionFacturaTiempoApp"   : ValidacionAnulacionFacturaTiempoApp, 
                                    "ValidacionAnulacionFacturaTiempoFast"  : ValidacionAnulacionFacturaTiempoFast 
                                };
                            $.ajax({
                                async: false,
                                type: "POST",
                                url: "config_anularOrden.php",
                                data: send,
                                dataType: "json",
                                success: function (dato) {
                                    if (dato.str > 0) {
                                        if (dato.tiempo === 1) {
                                            console.log('Factura dentro del tiempo');
                                            fn_validarAnulacion(1);   
                                        }else{
                                            cabeceramsj = 'Atenci&oacute;n!!';
                                            mensaje = 'La factura no puede ser anulada, porqu&eacute; el tiempo m&aacute;ximo de anulaci&oacute;n ha expirado.';
                                            fn_mensajeAlerta(cabeceramsj, mensaje, 0, '0');
                                        }         
                                    }        
                                } 
                            });                        
                        }
                        else {
                            console.log('Factura SIN transferencia');
                            fn_validarAnulacion(1);
                        }
                    }
                } else {
                    console.log('Factura SIN transferencia');
                    fn_validarAnulacion(1);
                }
            }
        });
    }
}


function fn_cargarProveedorTracking() {
    send = {metodo: "cargarPoliticaProveedorTracking"};
    send.cdn_id = $("#txtCadena").val();
    $.ajax({
        async: true,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_app.php",
        data: send,
        success: function (datos) {

            if (datos && datos[0]) {
                $("#proveedor_tracking").val(datos[0].metodo);
            }
        }
    });
}

function enviarTransaccionQPM() {
    let send = {
        transaccion: 'anularTransaccion',
        parametros: {
            idTransaccion: $('#listadoPedido').find("li.focus").attr("id"),
            rst_id: $("#hide_rst_id").val(),
            cdn_id: $("#hide_cdn_id").val(),
            accion: '1',
        }
    };
    $.ajax({
        async: true,
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

function fn_consultarTramaValidarsinTarjeta() {

    let cfac_id = $('#listadoPedido').find("li.focus").attr("id");
    send = { "ValidacionSinTarjeta": 1 };
    send.cfac = cfac_id;
    $.ajax({
        async: true,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: '../facturacion/config_pagoTarjetaDinamica.php',
        data: send,
        success: function(datos) {
            if (datos.existe === 0) {
                alertify.alert('NO SE PUEDE ANULAR LA TRANSACCION SIN TARJETA, ' + datos.mensaje);
            } else {
                fn_actulizarMotivoAnulacion()
            }
        },
        error: function(e) {
            console.log(e);
            alertify.alert('Error, no se pudo anular la factura');
        }
    });

}
function fn_restauranteCashless(cfac_id){
    var rst_id = $("#hide_rst_id").val();
    send = {"restauranteCashless": 1};
    send.rst_id = rst_id;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            fn_facturaCashless(cfac_id);                      
        }
    });
}
function fn_facturaCashless(cfac_id){
    var rst_id = $("#hide_rst_id").val();
    var send;
    send = {"facturaCashless": 1};
    send.codigoFactura = cfac_id;
    send.rst_id = rst_id;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            fn_activarCashless(cfac_id,datos.codigo_barras);                      
        }
    });
}
function fn_activarCashless(cfac_id,codigo_barras){
    var rst_id = $("#hide_rst_id").val();
    var send;
    send = {"activarCashless": 1};
    send.codigoFactura = cfac_id;
    send.codigoBarras = codigo_barras;
    send.rst_id = rst_id;
    $.getJSON("config_anularOrden.php", send, function (datos) {
        if (datos.str > 0) {
            fn_facturaCashless(cfac_id);                      
        }
    });
}


/*******************************************************************************
 *             Cuentas Cerradas de periodos anteriores
 *******************************************************************************/
function fn_cuentasPeriodosAnteriores() {
    
    opcion = 1;
    cargandoD(0);

    $('#visualizarFactura').hide();
    $("#verFormasPago").hide();
    $("#imprimirTransaccion").hide();
    $("#anularOrden").hide();
    $('#retomarOrden').hide();
    $("#listadoTxTarjetas").hide();
    
    mostrarOpciones("cuentasPeriodosAnteriores");
    $("#cambiarDatosCliente").show();

    $("#listadoPedido").html("");
    $("#listado").show();

    cargandoD(1);
}

const buscarPeriodosAnteriores = () => {

    cargandoD(0);

    $("#contenedorFechaTransaccion").hide();

    let codigoTran = ($("#codigoTran").val() === null || $("#codigoTran").val() === undefined || $("#codigoTran").val() === '') ? null :  $("#codigoTran").val();
    let nroFactura = ($("#nroFactura").val() === null || $("#nroFactura").val() === undefined || $("#nroFactura").val() === '') ? null :  $("#nroFactura").val();
    let identificacion = ($("#identificacion").val() === null || $("#identificacion").val() === undefined || $("#identificacion").val() === '') ? null :  $("#identificacion").val();
    let fechaTran = $("#fechaTran").val();


    if (fechaTran === null || fechaTran === undefined || fechaTran === '') {
        alertify.alert("Debe ingresar una fecha para poder realizar la busqueda.");
        cargandoD(1);
        return false;
    }

    let html = "";
    let numFacturas = 0;
    let numAnulacion = 0;

    send = {
        busquedaCuentasPeriodosAnteriores: 1,
        codigoTran: codigoTran,
        nroFactura: nroFactura,
        identificacion: identificacion,
        fechaTran: fechaTran
    }
    $.getJSON("config_anularOrden.php", send)
        .done(function(datos) {
            if (datos.str > 0) {
                let totalventas = 0;
                $("#calculoTransNum").empty();
                $("#calculoTransTotal").empty();
                $("#listado ul").empty();
                for (let i = 0; i < datos.str; i++) {
                    html = " <li id='" + datos[i]['cfac_id'] + "' planFidelizacion='" + datos[i]['PlanFidelizacionAutoConsumo'] + "' documentoConDatos='" + datos[i]['documento_con_datos'] + "' onclick='fn_modificarLista(\"" + datos[i]['ncre_id'] + "\", \"" + datos[i]['cfac_id'] + "\")'>" 
                         + "    <div class='listaFactura'>"
                         + "        <b>" + datos[i]['cfac_id'] + "</b>"
                         + "    </div>" 
                         + "    <div class='listaMesa'>" + datos[i]['mesa_descripcion'] + "</div><div class='listaSubtotal'>" + datos[i]['cfac_subtotal'] + "</div>" 
                         + "    <div class='listaTotal'>" + datos[i]['cfac_total'] + "</div><div class='listaCaja'>" + datos[i]['est_nombre'] + "</div>" 
                         + "    <div class='listaCajero'>" + datos[i]['usuario'] + "</div><div class='listaStatus'>" + datos[i]['descripcionEstado'] + "</div>" 
                         + "    <div class='listaComentario'><p>" + datos[i]['cfac_observacion'] + "</p></div>" 
                         + "    <input type='hidden' id='est_fac_" + datos[i]['cfac_id'] + "' notacredito='" + datos[i]['ncre_id'] + "' value='" + datos[i]['est_id'] + "'/>" 
                         + "    <input type='hidden' id='impuesto_fac_" + datos[i]['cfac_id'] + "'value='" + datos[i]['impuesto'] + "'/>" 
                         + "    <input type='hidden' id='cod_fac_periodo_anterior_" + datos[i]['cfac_id'] + "'value='" + datos[i]['impuesto'] + "'/>" 
                         + " </li>";
                    $("#listadoPedido").append(html);
                    if (datos[i]['impresion'] == 53) {
                        $("#" + datos[i]['cfac_id'] + "").css({'background': '#ff5f3c'});
                    } else if (datos[i]['ncre_id'] != 0 && datos[i]['anuladaPA'] == 0) {
                        $("#" + datos[i]['cfac_id'] + "").css({'background': '#00A7F5'});
                        numAnulacion++;
                        
                        if (datos[i]['ncdre_ant'] == null ) {
                            $("#" + datos[i]['cfac_id'] + "").css({'background': '#73ffa4'});
                            numAnulacion--;
                        }
                    } else if (datos[i]['ncre_id'] == 0 && datos[i]['descripcionEstatus'] == 'Anulada' && datos[i]['cfac_observacion'].startsWith('Cambio Datos Cliente - Factura Extemporanea')){ 
                        $("#" + datos[i]['cfac_id'] + "").css({'background': '#73ffa4'});                 
                    } else {
                        totalventas = totalventas + datos[i]['cfac_total'];
                        numFacturas++;

                        if (datos[i]['anuladaPA'] == 1){
                            $("#" + datos[i]['cfac_id'] + "").css({'background': '#73ffa4'});
                        }
                    }
                }
                numAnulacion = numAnulacion / 2;
                $("#calculoTransNum").text(numFacturas);
                $("#calculoAnulacion").text(numAnulacion);
                $("#calculoTransTotal").text(totalventas.toFixed(2));
                $(".calculosTransacciones").show();
            } else {
                $("#listadoPedido").html("");
                alertify.alert('No existen cuentas cerradas');
                $(".calculosTransacciones").hide();
            }
        }).fail( function() { 
            alertify.alert('ERROR cargando las transacciones.');
            $(".calculosTransacciones").hide();
        }).always( function() {
            cargandoD(1);
        });
}

const closeContenedorFecha = () => {
    $("#contenedorFechaTransaccion").hide();
}

function impresionAnularOrden(cfac_id, datos) {
    let apiImpresion = getConfiguracionesApiImpresion();

    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
        fn_cargando(1);
        let result = new apiServicioImpresion('nota_credito', cfac_id,0,datos);
        let imprime = result["imprime"];
        let mensaje = result["mensaje"];

        console.debug(imprime);
        console.debug(mensaje);

        if (!imprime) {
            alertify.success('Imprimiendo Nota de credito...');
        } else {
            alertify.success('Error al imprimir...');

        }

        fn_cargando(0);
    }
}

function servicioApiAperturaCajon(idFormaPago){
    console.log("servicioApiAperturaCajon");
    // Aplicar apertura cajon
    var apiImpresion = getConfiguracionesApiImpresion();
    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {

    send = { "servicioApiAperturaCajon": 1 };
    send.idFormaPago = idFormaPago;

    $.getJSON("../facturacion/config_servicioApiAperturaCajon.php", send)
    .done(function(datos) {
        // Código a ejecutar si la solicitud es exitosa
        console.log("Datos recibidos:", datos);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        // Código a ejecutar si la solicitud falla
        console.error("Error en la solicitud:", textStatus, errorThrown);
    });

    }
    cargando(1);
    cargando(0);
    $("#modalBloquearCargaCronometro").hide();
}

function fn_anularPagoDeUna(cfac_id, fpf_id, fmp_id) {
    //fpf_id FORMA DE PAGO FACTURA
    //fmp_id ID DE FORMA DE PAGO
    console.log("entro a anular de una");
    var cdn_id = $("#hide_cdn_id").val();
    var usr_id = $("#hide_usr_id").val();
    var rst_id = $("#hide_rst_id").val();
    var est_id = $("#hide_est_id").val();
    var send = { anularPagoDeUna: 1, cfac_id, fpf_id, fmp_id, cdn_id, usr_id, rst_id, est_id };
    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        url: "../DeUna/solicitudAnulacionDeUna.php",
        data: send,
        beforeSend: function () {
            cargando(1);
            $("#modalBloquearCargaCronometro").hide(0);
            $("#mdl_rdn_pdd_crgnd1").show(0);
            $("#loadingAnularImg").css("margin","6px");
        },
        success: function (datos) {
            console.log("exito en consulta");
            console.log(datos);

            if (datos.status != undefined) {
                if (datos.status == 200) {
                    fn_anularOrdenEfectivo(cfac_id, fpf_id)
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
            cargando(0);  
            $("#modalBloquearCargaCronometro").hide();
            $("#mdl_rdn_pdd_crgnd1").hide();
        },
        error: function (e) {
            //cargandoDeUnaModal(0);
            cargando(0);
            $("#modalBloquearCargaCronometro").hide();
            $("#mdl_rdn_pdd_crgnd1").hide();
            console.log("error");
            console.log(e);
        }
    });
}

function valorarCambioSobreFactura(accion) {
    if ($('li.focus').length > 0) {
        var cfac_id = $('#listadoPedido').find('li.focus').attr('id');

        if (cfac_id != '' && cfac_id != null) {
            fn_cargando(1);

            $.ajax({
                async: false,
                type: 'POST',
                dataType: 'json',
                contentType: 'application/x-www-form-urlencoded',
                url: 'config_anularOrden.php',
                data: {
                    'valorarCambioSobreFactura': 1,
                    'cfac_id': cfac_id
                },
                success: function (resultado) {
                    fn_cargando(0);

                    if (resultado.error == 0) {
                        if (resultado.cambio == 0) {
                            alertify.alert(accion == 'notaCredito' ? 'No es posible emitir una nota de cr&eacute;dito para una factura vinculada al cliente Consumidor Final.' : 'No es posible modificar una factura vinculada al cliente Consumidor Final.');

                            return false;
                        } else {
                            if (accion == 'notaCredito') {
                                validarTransferencia();
                            } else {
                                fn_validarAnulacion(2);
                            }
                        
                            return true;
                        }
                    } else {
                        alertify.error('Ha ocurrido algo inesperado y no hemos conseguido valorar la posibilidad de cambios sobre la factura.');

                        return false;
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR); console.log(textStatus); console.log(errorThrown);
                    fn_cargando(0);
                    alertify.error('Ha ocurrido algo inesperado y no hemos conseguido valorar la posibilidad de cambios sobre la factura.');

                    return false;
                }
            });
        } else {
            alertify.error('Ha ocurrido algo inesperado y no hemos conseguido obtener el ID de la factura.');
        } 
    } else {
        alertify.error('Debe seleccionar una factura.');
    } 
}