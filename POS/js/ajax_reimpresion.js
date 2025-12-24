/* global alertify, alertity */
var tipoDocumento = null
var tipoDocumentoDefault = 'FACTURA'

//Impedir el uso del botón "Atrás" del navegador
if (window.history && history.pushState) {
    history.pushState(null, null, null);
    addEventListener('popstate', function() {
        history.pushState(null, null, null);
        alertify.alert("<p style='font-size: 17px; margin-bottom: 15px; line-height: 2;'>Si deseas salir, por favor utilizar los menu de navegación de MaxPoint&nbsp;&nbsp;</p>");
    });
}
 
$(document).ready(function () {
    fn_cargarTipoDocumentos();
    fn_cargarImpresora();

    $('#listaPedido').shortscroll();
    $('#menu_desplegable').css('display', 'none');
    $('#modalImpresora').hide();
    $('#visorFacturas').css('display', 'none');

    $('#menu_desplegable').click(function () {
        $('#menu_desplegable').css('display', 'none');
        $('#cnt_mn_dsplgbl_pcns_drch').css('display', 'none');
    });
});

function mostarMenu() {
    $('#menu_desplegable').css('display', 'block');
    $('#cnt_mn_dsplgbl_pcns_drch').css('display', 'block');
}

function fn_cargarTipoDocumentos(tipo = null) {
    let send = {"tipoDocumentos": 1, "tipo": tipo};
    let html = '<input type="button" id="boton_menu" value="Menu" class="boton_Accion" onclick="mostarMenu()" style="float: right; margin: 5px 20px 0 0;"/>';
        html += '<input type="button" id="boton_imprimir" value="Imprimir" class="boton_Accion" onclick="fn_ModalImpresora()" style="float: right; margin: 5px 20px 0 0;"/>';
        html += '<input type="button" id="boton_ver" value="Ver" class="boton_Accion" onclick="fn_ModalVisualizarFactura()" style="float: right; margin: 5px 20px 0 0;"/>';

    $.getJSON("config_reimpresion.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++)
            {
                let validarClass = 'boton_Opcion';
                if((!tipoDocumento) && (datos[i]['tipo']==tipoDocumentoDefault)) {
                    tipoDocumento = tipoDocumentoDefault;
                    fn_cargarListarTransacciones(tipoDocumentoDefault);
                    validarClass = 'boton_Accion_EE1';
                }

                html += "<input class='"+ validarClass +"' type='button' id='" + datos[i]['tipo'] + "' value='" + datos[i]['valor'] + "' onclick='fn_seleccionarTipoDocumento(" + datos[i]['tipo'] + ")' style='float: left; margin: 5px 10px 0 0;'/>";
            }
            $("#tipoDocumentos").html(html);
        }
    });
}

function fn_cargarImpresora() {
    let send = {
        "cargarImpresora": 1,
        "estacion": $('#hide_est_id').val(),
        "restaurante": $('#hide_rst_id').val(),
    };
    let html = '';

    $.getJSON("config_reimpresion.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                html += "<option value='" + datos[i]['id'] + "'>" + datos[i]['descripcion'] + "</option>";
            }
            $("#impresora").html(html);
        }
    });
}

function fn_cargarListarTransacciones(tipo = null) {
    
    cargando(0);
    $("#listadoPedido").html('');
    $("#hid_transaccion").val('');
    let html = '';
    let send = {
        "listarTransacciones"   : 1, 
        'tipo'                  : tipo,
        'restaurante'           : $('#hide_rst_id').val(),
        'estacion'              : $('#hide_est_id').val(),
    };

    $.getJSON("config_reimpresion.php", send, function (datos) {
        if (datos.str === 0) {
            cargando(1);
            html += "<li><div style='float:left;width: 350px;font-size:14px'>No tienes transacciones en estado error por listar...</div></li>";
            $("#listadoPedido").append(html);
            alertify.error('No tienes transacciones en '+ tipoDocumento + '...');
            return
        }

        for (var i = 0; i < datos.str; i++) {
            html += "<li id=" + datos[i]['transaccion'] + " onclick='fn_modificarLista(\"" + datos[i]['transaccion'] + "\", \"" + datos[i]['factura'] + "\")'> \
                        <div class='listaFactura'><b>" + datos[i]['transaccion'] + "</b></div><div class='listaMesa'>" + datos[i]['mesa'] + "</div> \
                        <div class='listaSubtotal'>" + datos[i]['subtotal'] + "</div><div class='listaTotal'>" + datos[i]['total'] + "</div> \
                        <div class='listaCaja'>" + datos[i]['estacion_nombre'] + "</div><div class='listaCajero'>" + datos[i]['usuario'] + "</div> \
                        <div class='listaComentario'><p>" + datos[i]['observacion'] + "</p></div> \
                    </li> \
                    <input type='hidden' id='est_fac_" + datos[i]['transaccion'] + "' value='" + datos[i]['estacion'] + "'/>";
        }
        $("#listadoPedido").append(html);

        cargando(1);
    });
}

function fn_seleccionarTipoDocumento(tipo = null) {
    var elementosInput = document.querySelectorAll('input.boton_Accion_EE1');

    elementosInput.forEach(function(input) {
        $("#"+input.id).removeClass("boton_Accion_EE1").addClass("boton_Opcion");
    });

    $("#"+tipo.id).addClass('boton_Accion_EE1');

    fn_cargarListarTransacciones(tipo.id)
    tipoDocumento = tipo.id
}

function fn_modificarLista(transaccion, factura) {

    if(tipoDocumento==='ORDEN_PEDIDO') {
        transaccion = factura;
    }
    
    $("#hid_transaccion").val(transaccion);

    $("#hid_factura").val(factura);
    $('#listadoPedido li').removeClass('focus');
    $("#" + transaccion).addClass("focus");

    $('#listadoPedido li').live('click', function () {
        $(this).addClass('focus');
    });
    $('#listadoPedido li.focus').click(function () {
        $(this).removeClass('focus');
    });
}

function fn_ModalImpresora() {
    if(!$('#hid_transaccion').val()) {
        alertify.error('Selecciona la transacción a imprimir...');
        return
    }

    $("#modalImpresora").show();
    $("#modalImpresora").dialog({
        modal: true,
        width: 510,
        height: 300,
        position: {
            my: 'top',
            at: 'top+80'
        },
        resize: false,
        opacity: 0,
        show: "none",
        hide: "none",
        open: function (event, ui) {
            $("#impresora").val(0);
            $(".ui-dialog-titlebar").hide();
        }
    });
}

function fn_botonOk () {
    if(tipoDocumento==='FACTURA') {
        let impresora = $('#impresora').val();
        let estacion = $('#hide_est_id').val();
        let factura = $('#hid_transaccion').val();
        
        impresionFactura(factura, estacion, null, impresora);
    }

    if(tipoDocumento==='ORDEN_PEDIDO') {
        let impresora = $('#impresora').val();
        let factura = $('#hid_factura').val();
        let estacion = $('#hide_est_id').val();
        
        impresionOrdenPedido(factura, estacion, null, impresora);
    }
    
    if(tipoDocumento==='NOTA_CREDITO') {
        let impresora = $('#impresora').val();
        let estacion = $('#hide_est_id').val();
        let factura = $('#hid_factura').val();
        
        impresionNotaCredito(factura, estacion, null, impresora);
    }

    this.fn_botonCancelar();
}

function fn_botonCancelar() {
    $('#modalImpresora').dialog('close');
    $("#modalImpresora").hide();
    $("#hid_transaccion").val('');

    fn_cargarListarTransacciones(tipoDocumento)
}

function fn_funcionesGerente() {
    window.location.replace("../funciones/funciones_gerente.php");
}

function fn_obtenerMesa() {
    send = {"obtenerMesa": 1};
    $.getJSON("config_reimpresion.php", send, function (datos) {
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

function fn_cargarFactura(cfac_id) {
    var html = "";
    send = { "visorCabeceraFactura": 1 };
    send.cfac_id = cfac_id;
    $.getJSON("config_reimpresion.php", send, function (datos) {
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
                "</td></tr><tr><td colspan='4'>==============================================</td></tr><tr><td align='center'><b>CANT.</b></td><td align='center'><b>DESCRIPCION</b></td><td align='center'><b>P. UNIT</b></td><td align='center'><b>VALOR</b></td></tr>";
            send = { visorDetalleFactura: 1 };
            send.cfac_id = cfac_id;
            $.getJSON("config_reimpresion.php", send, function (datos) {
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
                    $.getJSON("config_reimpresion.php", send, function (datos) {
                        if (datos.str > 0) {
                            html =
                                html +
                                "<tr><td align='center' colspan='4'>==============================================</td></tr><tr><td align='right' colspan='2'>SUBTOTAL.....$</td><td align='right' colspan='2'>" +
                                datos[0]["cfac_subtotal"] +
                                "</td></tr><tr><td align='right' colspan='4'>=============</td></tr><tr><td align='right' colspan='2'>BASE 12%.....$</td><td align='right' colspan='2'>" +
                                datos[0]["cfac_base_iva"] +
                                "</td></tr><tr><td align='right' colspan='2'>BASE 0%.....$</td><td align='right' colspan='2'>" +
                                datos[0]["cfac_base_cero"] +
                                "</td></tr><tr><td align='right' colspan='2'>I.V.A.12%.....$</td><td align='right' colspan='2'>" +
                                datos[0]["cfac_iva"] +
                                "</td></tr><tr><td align='right' colspan='4'>=============</td></tr><tr><td align='right' colspan='2'><b>TOTAL.....$:</b></td><td align='right' colspan='2'><b>" +
                                datos[0]["cfac_total"] +
                                "</b></td></tr>";
                            send = { formasPagoDetalleFactura: 1 };
                            send.cfac_id = cfac_id;
                            $.getJSON("config_reimpresion.php", send, function (datos) {
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

        $("#visorFacturas").css("display", "block");
        $("#cabecerafactura").css("display", "block");
        $("#detalleFactura").css("display", "block");
    });
}

function fn_cargarOrdenPedido(cfac_id) {
    let html = "";
    send = { "visorCabeceraOrdenPedido": 1 };
    send.cfac_id = cfac_id;
    $.getJSON("config_reimpresion.php", send, function (datos) {
        if (datos.str > 0) {
            let html = ""
            html += "<table>" +
                    "<tr><td colspan='4'>==============================================</td></tr>"+
                    "<tr><td align='center'><b>CANT.</b></td><td align='center'><b>DESCRIPCION</b></td></tr>"
            for (i = 0; i < datos.productos.length; i++) {
                html =
                html +
                "<tr><td align='center'>" +
                datos.productos[i].cantidad +
                "</td><td>" +
                datos.productos[i].producto +
                "</td><td align='center'></td><td align='center'></td></tr>";
            }

            html = html + "</table>";
            $("#cabecerafactura").html(html);
        }

        $("#visorFacturas").css("display", "block");
        $("#cabecerafactura").css("display", "block");
        $("#detalleFactura").css("display", "block");
    });
}

function fn_ModalVisualizarFactura() {
    if(!$('#hid_transaccion').val()) {
        alertify.error('Selecciona la transacción a imprimir...');
        return
    }

    if(tipoDocumento==='ORDEN_PEDIDO') {
        fn_cargarOrdenPedido($('#hid_factura').val());
    }else{
        fn_cargarFactura($('#hid_factura').val());
    }

    $("#cabecerafactura").html("");
}

function fn_cerrarVisorFacturas() {
    $("#visorFacturas").css("display", "none");
    $("#detalleFactura").css("display", "none");
}


function fn_salirSistema() {
    window.location.href = "../index.php";
}