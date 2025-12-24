/* global parseFloat */

var cantidadP = "";
var DOCUMENTO_CLIENTE_AX = '';
var OBSERVACIONES_CREDITOS = '';
var TIPO_IDENTITICACION_CLIENTE_EXTERNO = '';
var TELEFONO_CLIENTE_CREDITO = '';
var DIRECCION_CLIENTE_CREDITO = '';
var NOMBRE_CLIENTE_CREDITO = '';
var CORREO_ELECTRONICO_CLIENTE_CREDITO = '';
var TIPO_CLIENTE_CREDITO = '';
var accionBoton = 0;


$(document).ready(function() {
    var cli_documento = $("#txt_cli_documento").val();
    if (cli_documento) {
        fn_validaCobroClienteExternoCupon();
    }
});

function fn_listarClientesExternos() {
    //  $("#buscarClienteExt").live('change', function () {
    $('.tipoCupon').css('display', 'none');
    var textoBuscarClienteExt = $("#buscarClienteExt").val();
    if (textoBuscarClienteExt) {
        var cont = 0;
        var contains = $('.tipoCupon:contains("' + textoBuscarClienteExt + '")');
        if (contains.length > 0) {
            contains.each(function() {
                cont++;
                if (cont <= 3) {
                    $(this).css('display', 'block');
                }
            });
        } else {
            alertify.error('El cliente no se encuentra configurado.');
        }
    } else {
        $('.tipoCupon').css('display', 'none');
        alertify.error('Ingrese un cliente.');
    }
    //});
}

function fn_validaCobroClienteExternoCupon() {

    var data = $('#txt_tipov_id').val();

    if (data !== undefined) { // Pago cupon.

        // txtNumFactura   btnBaseFactura  
        var txt_tipov_id = ($("#txt_tipov_id").val());
        var txt_vae_cod = ($("#txt_vae_cod").val());
        var txt_montoCupon = ($("#txt_montoCupon").val());

        lc_opcionCreditoEmpresa = "EXTERNO";
        var txt_vae_IDCliente = ($("#txt_vae_IDCliente").val());
        var txt_cli_direccion = null;
        var txt_cli_documento = ($("#txt_cli_documento").val());
        var txt_cli_email = ($("#txt_cli_email").val());
        var txt_cli_nombres = ($("#txt_cli_nombres").val());
        var txt_cli_telefono = ($("#txt_cli_telefono").val());

        // alert(txt_cli_nombres+" " + txt_vae_IDCliente+" " + txt_cli_documento+" " + txt_cli_telefono+" " + txt_cli_direccion+" " + txt_cli_email);

        if ((parseFloat(txt_montoCupon) >= parseFloat($("#btnBaseFactura").val()))) {
            //   alert(txt_cli_nombres + "      " + txt_vae_IDCliente + "      " + txt_cli_documento + "      " + txt_cli_telefono + "      " + txt_cli_direccion + "      " + txt_cli_email);

            if (parseFloat(txt_montoCupon) >= 9999) {
                $("#pagado").val(parseFloat($("#btnBaseFactura").val()));
            } else {
                $("#pagado").val(parseFloat(txt_montoCupon));
            }
            // en caso de (que no  se desee que halla faltante en la factura por exeso de voucher.)
            $("#pagado").val(parseFloat($("#btnBaseFactura").val()));

            fn_selecionaClienteAx(txt_cli_nombres, txt_vae_IDCliente, txt_cli_documento, txt_cli_telefono, null, txt_cli_email);

        } else {

            fn_DividirSiExcede();

            if (getDividir() === 1) {
                alertify.confirm("El valor de la factura excede el límite de [$ " + txt_montoCupon + "  ]¿Desea hacer separación de cuentas?");

                $("#alertify-ok").click(function() {
                    fn_Dividir();

                });

            } else {
                alertify.alert("El valor de la factura excede el límite de [$ " + txt_montoCupon + "  ]");
                $("#alertify-ok").click(function() {
                    //                    var btnAtras = $("#btn_salirOrden");
                    //                    btnAtras.click();
                    fn_cerrarModalCuponesSistemaGerenteVoucher();
                });
            }
        }
    } else {


    }
}


var dividir;

function getDividir() {
    return dividir;
}

function setDividir(dato) {
    dividir = dato;
}

function fn_DividirSiExcede() {
    var send = { "SepararCuentasAlExecerVoucher": 1 };
    send.idRest = $("#idRest").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                fn_imprimirOrden();
                setDividir(datos[0]["estado"]);
            } else {
                setDividir(0);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}
var parametros = '';

function encapsularParametros() {
    parametros = '<input type="text" name="txt_tipov_id" value="' + $("#txt_tipov_id").val() + '" />    ';
    parametros += '<input type="password" name="txt_vae_cod" id="vae_cod" value="' + encodeURI($("#txt_vae_cod").val()) + '" />';
    parametros += '<input type="text" name="txt_vae_IDCliente" value="' + $("#txt_vae_IDCliente").val() + '" />  ';
    parametros += '<input type="text" name="txt_cli_documento" value="' + $("#txt_cli_documento").val() + '" />  ';
    parametros += '<input type="text" name="txt_cli_email" value="' + $("#txt_cli_email").val() + '" />  ';
    parametros += '<input type="text" name="txt_cli_nombres" value="' + $("#txt_cli_nombres").val() + '" />  ';
    parametros += '<input type="text" name="txt_cli_telefono" value="' + $("#txt_cli_telefono").val() + '" />  ';
    parametros += '<input type="text" name="txt_montoCupon" value="' + $("#txt_montoCupon").val() + '" />  ';
    parametros += '<input type="text" name="txt_esVoucher" value="1" />    ';

}

function fn_Dividir() {


    var params = "&tipov_id=" + $("#txt_tipov_id").val() + "&vae_cod=" + encodeURI($("#txt_vae_cod").val()) + "&vae_IDCliente=" + $("#txt_vae_IDCliente").val();
    params += "&hide_cli_documento=" + $("#txt_cli_documento").val();
    params += "&hide_cli_email=" + $("#txt_cli_email").val() + "&hide_cli_nombres=" + $("#txt_cli_nombres").val() + "&hide_cli_telefono=" + $("#txt_cli_telefono").val();
    params += "&hide_montoCupon=" + ($("#txt_montoCupon").val()) + "";
    params += "&esVoucher=1";

    encapsularParametros();

    var send = { "dividir": 1 };
    send.dop_id = $("#txtOrdenPedidoId").val();
    send.cuenta1 = $("#txtNumCuenta").val();
    send.cuenta2 = 2;
    send.limiteVoucher = ($("#txt_montoCupon").val());
    send.parametrosVoucher = parametros;
    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                window.location.replace(datos[0]['url'] + params);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_pagoCredito(opcion, formaPagoIDSeleccionada) {
    tituloModal = "";
    if ((parseFloat($("#pagado").val())) > (parseFloat($("#pagoTotal").val()))) {
        $("#pagado").val($("#pagoTotal").val());
    }

    switch (opcion) {
        case "EXTERNO":
            if (fn_verificarPoliticaCodigo(formaPagoIDSeleccionada)) {
                localStorage.setItem("id_agregador", formaPagoIDSeleccionada);
                $("#btnFormaPagoId").val(formaPagoIDSeleccionada);
                cerrarModalAgegadores();
                fn_abrirModalCodigoFacturacion();
            } else {
                if (fn_visualizaBoton()) {
                    $("#tblCupon").empty();
                    html = "";
                    html += "<td><button class='ui-state-default ui-corner-all' onclick='fn_clienteExternoSinCupon()'>" + "Sin Cupón" + "</button></td>";
                    html += "<td><button class='ui-state-default ui-corner-all' onclick='fn_abrirModalVoucher()'>" + "Con Cupón " + "</button></td>";

                    $("#tblCupon").html(html);
                    fn_modalCuponExterno();
                } else {
                    fn_clienteExternoSinCupon();
                }
            }
            break;
        case "PRODUCTO":
            tituloModal = "Ingrese numero de cedula..";
            fn_modalCliente(tituloModal);
            fn_alfaNumerico_numeros(txt_cliAx);
            break;
        default:
            tituloModal = "Digite cliente..";
            fn_modalCliente(tituloModal);
            fn_alfaNumerico_letras(txt_cliAx);
            break;
    }
}


function fn_abrirModalCodigoFacturacion() {
    $("#tecladoCodigoFacturacion").show();
    $("#tecladoCodigoFacturacion").dialog({
        modal: true,
        width: 500,
        heigth: 500,
        resize: false,
        opacity: 0,
        show: "none",
        hide: "none",
        duration: 500,
        open: function(event, ui) {
            $(".ui-dialog-titlebar").hide();
            fn_abrirTecladoCodigosFacturacion("#txtcodigoFacturacion");
            $('#txtcodigoFacturacion').val('');
        }
    });
}

function fn_verificarPoliticaCodigo(idFormaPago) {
    var send = { "verificarPoliticaCodigo": 1 };
    var estado;
    
    send.idFormaPago = idFormaPago;
    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_ordenPedido.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                if (datos[0]["Activo"] === 1) {
                    estado = true;
                } else {
                    estado = false;
                }
            } else {
                estado = false;
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR + textStatus + errorThrown);

        }
    });
    
    return estado;
}

function pantalla1() {
    $("#infoMdalf").html("Ingrese información ");
    $('#infoMdalf').css({ 'color': 'FFF', 'font-size': '5mm' });
    //$('#voucherAEs').hide(100);
    $("#select_cliente").hide();
    $('#select_tipocupo').hide(100);
    $("#select_tipocupo").css("display", "none");
    $("#voucherAE").show(100);

    //    $("#keyboard").hide();
    //    $("#keyboard").empty();
    $("#botonVolver").hide(100);
    $("#botonVolver1").hide();
    $("#botonVolver2").hide();
    $('#voucherAEs').show(100);

}

function pantalla2() {

    $('#infoMdalf').html('Busque el cliente . . .');
    $('#infoMdalf').css({ 'color': 'FFF', 'font-size': '5mm' });
    $('#buscarClienteExt').val('');
    $('#voucherAE').hide(100);
    $('#voucherAEs').hide(100);
    $('#select_tipocupo').hide(100);
    $("#select_cliente").show(100);
    $("#botonVolver").show(100);
    //    $("#keyboard").hide();
    //    $("#keyboard").empty();

    $("#botonVolver1").show();
    $("#botonVolver2").hide();
    // $("#botonVolver").hmtl("<img onclick=\"pantalla1()\" style=\"margin-top: 3%; width: 10%;\" src=\"../imagenes/volverCupon.png\"></img>");
}

function pantalla3() {
    $("#infoMdalf").html("Seleccione el cupo");
    $('#infoMdalf').css({ 'color': 'FFF', 'font-size': '5mm' });
    $('#voucherAEs').hide(100);
    $("#select_cliente").hide();
    $("#voucherAE").hide(100);
    //    $("#keyboard").hide();
    //    $("#keyboard").empty();
    $("#select_tipocupo").show(100);
    $("#botonVolver1").hide();
    $("#botonVolver2").show();
}

function seleccionarClienteVoucher(IDCliente, cli_telefono, cli_nombres, cli_email, cli_documento, cli_direccion = null) {
    // Asignar los datos que luego se enviaran por prm segun frm.
    asignarDatosClienteVoucher(IDCliente, null, cli_documento, cli_email, cli_nombres, cli_telefono);
    var ID_ColeccionCadena = IDCliente.split('|');
    fn_obtenerTiposdeCupo(ID_ColeccionCadena[0], ID_ColeccionCadena[1]);

}

function asignarDatosClienteVoucher(IDCliente, cli_direccion = null, cli_documento, cli_email, cli_nombres, cli_telefono) {
    $('#txt_vae_IDCliente').val(IDCliente);
    $('#txt_cli_direccion').val(null);
    $('#txt_cli_documento').val(cli_documento);
    $('#txt_cli_email').val(cli_email);
    $('#txt_cli_nombres').val(cli_nombres);
    $('#txt_cli_telefono').val(cli_telefono);
}

function fn_obtenerTiposdeCupo(ID_ColeccionCadena, ID_ColeccionDeDatosCadena) {

    var aux = "";
    var send;
    send = { "buscarMontoVoucher": 1 };
    send.opcion = 2;
    send.ID_ColeccionCadena = ID_ColeccionCadena;
    send.ID_ColeccionDeDatosCadena = ID_ColeccionDeDatosCadena;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        Accept: "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../mantenimiento/adminCadena/configAdminCadena.php",
        data: send,
        success: function(data) {
            if (data.str > 0) {
                pantalla3();
                if (data[0].valor !== '' && data[0].valor !== '0') {
                    arrayValor = data[0].valor.split('|');

                    $.each(arrayValor, function(index, value) {
                        aux += "<div class=\"tipoCupon text-center\"  >" +
                            "<input type=\"checkbox\" class=\"checkbox\" />" +
                            "<div  onclick=\"fn_cobrarCupon(" + index + ")\"><div class=\"letra\">" + "Valor" + "</div>" +
                            "<div id='" + index + "'>" + value + "</div></div><br\>" +
                            "<button onclick=\"fn_agregar('+','" + index + "')\">+</button>" +
                            "<input id='txt_" + index + "' type='text' value='1' disabled/>" +
                            "<button onclick=\"fn_agregar('-','" + index + "')\">-</button>" +
                            "</div>";
                    });
                }
                $("#select_tipocupo").html(aux);
                $("#select_tipocupo").show(100);

            } else {
                alertify.error("No hay cupones configurados.");
            }

        }
    });
}

function fn_procesoManualVocuher() {
    //$("#mdl_rdn_pdd_crgnd").hide();
    $("#voucherAEs").show(100);
    $('#voucherAEs').html('');
    $('#voucherAEs').html(' <center>  <textarea rows=\"4\" cols=\"50\" type=\"text\" name=\"input_cuponSistemaGerenteAutEXT\"   id=\"input_cuponSistemaGerenteAutEXT\" style=\"height: 60px; width: 454px;\"/> </textarea></center>');
    $('#input_cuponSistemaGerenteAutEXT').val("");
    $('#input_cuponSistemaGerenteAutEXT').removeClass('alertaInput');

    fn_activarCasilla('#input_cuponSistemaGerenteAutEXT');
    $('#input_cuponSistemaGerenteAutEXT').focus();
    $('#btn_ok_pad').removeAttr('onclick');

    document.getElementById('btn_ok_pad').setAttribute('onclick', 'fn_validarCuponManualVoucher()');
}

function fn_guadarDescripcionVoucher() {
    var mensaje = false;
    var send = { "guadarDescripcionVoucher": 1 };
    send.opcion = 1;
    send.IDCabeceraOrdenPedido = $("#txtOrdenPedidoId").val();
    send.observacion = $("#input_cuponSistemaGerenteAutEXT").val();

    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_ordenPedido.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                alertify.success(datos[0].mensaje);
                mensaje = true;
            }
        }
    });
    return mensaje;
}

function fn_validarCuponManualVoucher() {
    if ($("#input_cuponSistemaGerenteAutEXT").val() !== "") {
        var mensaje = fn_guadarDescripcionVoucher();
        if (mensaje) {
            $('#mdl_rdn_pdd_crgnd').show();
            fn_cargarClienteExterno();

            $('#mdl_rdn_pdd_crgnd').hide();
        } else {
            alertify.error("Existen problemas al guardar la información.");
        }
    } else {
        alertify.error("ERROR!!  Debe completar la información solicitada");
    }
}

function fn_cargarClienteExterno() {

    var html = "";
    $("#select_cliente").html('');
    var send;
    send = { "traerListaVoucherAerolineas": 1 };
    send.opcion = 2;
    send.descripcion = '';
    send.documento = '';
    send.informacionVoucher = '';
    send.valor = 0;

    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        Accept: "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../mantenimiento/adminCadena/configAdminCadena.php",
        data: send,
        success: function(data) {
            if (data) {
                pantalla2();
                for (i = 0; i < data.str; i++) {
                    if (data[i].isActive) {
                        var datosVoucher = JSON.parse(data[i].Datos);
                        html += "  <div class=\"tipoCupon text-center\" style=\"display:none\" onclick=\"seleccionarClienteVoucher( '" + data[i].ID_ColeccionCadena + "|" + data[i].ID_ColeccionDeDatosCadena + "','" + datosVoucher.telefonoDomiclio + "'  , '" + datosVoucher.descripcion + "' , '" + datosVoucher.correo + "' ,'" + datosVoucher.identificacion + "'   ,'" + datosVoucher.direccionDomicilio + "'    ) \"><div class=\"letra\">  " + datosVoucher.descripcion + "</div> </div>";
                    }
                }
                fn_alfaNumerico_letras(buscarClienteExt);
                $('#btn_ok_pad').removeAttr('onclick');
                document.getElementById('btn_ok_pad').setAttribute('onclick', 'fn_listarClientesExternos()');

            } else {
                alertify.error("Error al consultar los datos.");
            }
            $("#select_cliente").html(html);

        }
    });

}

function fn_activarCasilla(id) {
    $("#keyboard").hide();
    $("#keyboard").empty();
    fn_alfaNumerico(id);
    $('#btn_ok_pad').attr('onclick', 'fn_validarCuponManual()');
    $('#btn_cancelar_teclado').attr('onclick', 'fn_cerrarModalCuponesSistemaGerente()');
}

function fn_abrirModalVoucher() {
    $("#modalCupon").dialog("close");
    $("#mdl_rdn_pdd_crgnd").show();
    pantalla1();

    var mostrarModal = true;

    var html;
    html = "<br/><center><div style=\"height:60px; font-size: 18px;\"> <h3><label> Ingrese información correspondiente a los cupones </h3></strong></div></center>";
    $("#voucherAE").html(html);
    fn_procesoManualVocuher();
    $("#mdl_rdn_pdd_crgnd").hide();
    if (mostrarModal) {
        $('#input_cuponSistemaGerenteAutVoucher').focus();
        $("#modalCuponSistemaGerenteVoucher").show();
        $("#modalCuponSistemaGerenteVoucher").dialog({
            modal: true,
            position: {
                my: 'top',
                at: 'top+50'
            },
            width: 700,
            heigth: 500,
            resize: false,
            opacity: 0,
            show: "none",
            hide: "none",
            duration: 500,
            open: function(event, ui) {
                $('.ui-dialog-titlebar').hide();
                $('#input_cuponSistemaGerenteAutVoucher').focus();
                $('#input_cuponSistemaGerenteAutVoucher').val('');
            }
        });
    }


}

function fn_visualizaBoton() {
    var estado = false;
    var send = { "VisualizaBoton": 1 };
    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_ordenPedido.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                if (datos[0]["Activo"] === 1) {
                    estado = true;
                } else {
                    estado = false;
                }

            } else {
                estado = false;
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR + textStatus + errorThrown);

        }
    });
    return estado;
}

function fn_clienteExternoSinCupon() {

    $("#modalCupon").dialog("close");
    tituloModal = "Digite cliente..";
    fn_modalCliente(tituloModal);
    fn_alfaNumerico_letras(txt_cliAx);
}

function fn_cerrarModalCuponesSistemaGerenteVoucher() {
    $("#keyboard").hide();
    $("#keyboard").empty();
    $("#modalCuponSistemaGerenteVoucher").hide();
    $("#modalCuponSistemaGerenteVoucher").dialog("close");
    $("#input_cuponSistemaGerenteAutEXT").val("");
    $("#pagado").val("");

}

function fn_modalCuponExterno() {
    $("#modalCupon").dialog({
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
            "Cancelar": function() {
                $(this).dialog("close");
                $(".jb-shortscroll-wrapper").show();
                $("#pagado").val("");
            }
        }
    });
    $('.ui-button-text').each(function (i) {
        $(this).html($(this).parent().attr('text'));
    })
}

function fn_agregar(val, index) {
    var cont = $('#txt_' + index).val();
    var valor = arrayValor[index];
    var total;
    if (val === '+') {
        cont++;
        total = cont * valor;
        $('#' + index).text(total.toFixed(2));
        $('#txt_' + index).val(cont);
    }

    if (val === '-') {

        if (cont > 1) {
            cont--;
            total = cont * valor;
            $('#' + index).text(total.toFixed(2));
            $('#txt_' + index).val(cont);
        }
    }
}

function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

function fn_cobrarCupon(index) {

    var montoMax = $("#" + index).text();
    //if ($.isNumeric(montoMax)) {
    if (isNumeric(montoMax)) {
        $("#txtOrdenPedidoId").val();
        $("#txtNumMesa").val();
        $("#txtNumCuenta").val();
        $('#txt_montoCupon').val(montoMax);
        $('#txt_esVoucher').val(1);

        var datoTxt = encodeURI($("#input_cuponSistemaGerenteAutEXT").val());
        fn_validaCobroClienteExternoCupon();

    } else {
        alertify.error("El valor ingresado es incorrecto,por favor intente de nuevo");
    }

    return false;
}

function fn_modalCliente(tituloModal) {
    $('.jb-shortscroll-wrapper').hide();
    $("#txt_cliAx").show();
    $("#modalclienteAx").dialog({
        title: tituloModal,
        modal: true,
        draggable: false,
        width: 700,
        heigth: 500,
        resizable: "false",
        opacity: 100,
        show: "none",
        hide: "none",
        duration: 500,
        position: [200, 5],
        buttons: {
            'Cancelar': function() {
                $(this).dialog("close");
                $('.jb-shortscroll-wrapper').show();
                $('#keyboard').hide();
                $('#keyboard').empty();
                $("#txt_cliAx").val('');
                $("#detalleAx").empty();
                $("#divCadenas").empty();
                $("#divRsts").hide();
                $("#pagado").val('');
                
                coma = 0;
            }
            
        }
    });
    $('.ui-button-text').each(function (i) {
        $(this).html($(this).parent().attr('text'));
    });
    
}

function fn_buscaClienteAx() {
    var send;
    fn_cargando(1);
    if ($("#txt_cliAx").val().length <= 0) {
        alertify.error("ingrese un parametro de busqueda.");
        fn_cargando(0);
        return false;
    }
    if (lc_opcionCreditoEmpresa == "PRODUCTO") {
        /*consumo del WS de validacion de asociado*/
        send = { "validaExisteAsociado": 1 };
        send.cedAso = $("#txt_cliAx").val();
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_servicios.php",
            data: send,
            success: function(datos) {
                fn_cargando(0); //se cierra la modal del cargando
                if (!datos) {
                    alertify.error("Error al recuperar la informacion");
                } else {
                    $("#detalleAx").empty();
                    if (datos['respuesta'] == 1) {
                        var idCedAso = $("#txt_cliAx").val() + "," + datos['mensaje'];
                        html = "<tr><td><button class='btn_clientesAx' style='height:60px; font-size: 18px;' onclick='fn_selecionaClienteAx(\"" + datos['mensaje'] + "\",\"" + idCedAso + "\",\"" + idCedAso + "\",\"" + idCedAso + "\",\"" + idCedAso + "\",\"" + idCedAso + "\",\"" + idCedAso + "\");'>" + datos['mensaje'] + "</button></td></tr>";

                        $("#detalleAx").append(html);
                        //Finalizar proceso 

                    } else if (datos['respuesta'] == 0) {
                        alertify.alert(datos['mensaje']);
                        $("#txt_cliAx").val("");

                    } else {
                        alertify.alert("ERROR");
                        $("#txt_cliAx").val("");
                        return false;
                    }
                    //numeroIntentos++;
                }
            }
        });
    } else {
        var tipDocument = '';
        if (lc_opcionCreditoEmpresa == 'INVENTARIO' || lc_opcionCreditoEmpresa == 'EMPLEADO' || lc_opcionCreditoEmpresa == 'INTER') {
            tipDocument = 'RELACIONADO';
            TIPO_CLIENTE_CREDITO = tipDocument;
        } else {
            tipDocument = 'EXTERNO';
            TIPO_CLIENTE_CREDITO = tipDocument;
        }

        var send;
        //send = {"buscaClienteAx": 1};
        //send.desCliAx = $("#txt_cliAx").val();
        //send.banderaC = lc_opcionCreditoEmpresa;
        //$.getJSON("config_facturacion.php", send, function (datos)
        send = {};
        send.metodo = "buscaClienteAx";
        send.documento = $("#txt_cliAx").val();
        send.tipoDocumento = tipDocument;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            "Accept": "application/json, text/javascript2",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../facturacion/clienteWSClientes.php",
            data: send,
            success: function(datos) {
                fn_cargando(0);
                if (datos == null) {

                    alertify.alert("ERROR AL CONSULTAR LA INFORMACION.");
                    return false;
                }

                if (datos[0].estado === 1) {
                    $("#detalleAx").empty();
                    var array_numeroClientes = []; //new Array(); 
                    array_numeroClientes = datos[0].cliente;
                    var numeroRegistrosClientes = array_numeroClientes.length;
                    if (numeroRegistrosClientes > 0) {
                        for (i = 0; i < numeroRegistrosClientes; i++) {
                            //alert("inicia"+i);
                            //alert(datos[0].cliente[i].descripcion);
                            html = "<tr><td><button class='btn_clientesAx' style='height:60px; font-size: 18px;' onclick='fn_selecionaClienteAx(\"" + datos[0].cliente[i].descripcion + "\",\"" + datos[0].cliente[i].identificacion + "\",\"" + datos[0].cliente[i].identificacion + "\",\"" + datos[0].cliente[i].telefonoDomiclio + "\",\"" + datos[0].cliente[i].direccionDomicilio + "\",\"" + datos[0].cliente[i].correo + "\",\"" + datos[0].cliente[i].tipoIdentificacion + "\");'>" + datos[0].cliente[i].descripcion + " - " + datos[0].cliente[i].identificacion + "</button></td></tr>";
                            $("#detalleAx").append(html);
                            //alert("termina"+i);
                        }
                    } else {
                        $("#detalleAx").empty();
                        htm = "<p>NO EXISTEN REGISTROS.</p>";
                        $("#detalleAx").append(htm);
                    }
                    //  alert(array_numeroClientes.length);
                    // alert(datos[0].cliente[0].descripcion);  
                } else if (datos[0].estado === -1) {
                    $("#detalleAx").empty();
                    htm = "<p>NO EXISTEN REGISTROS.</p>";
                    $("#detalleAx").append(htm);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                fn_cargando(0);
                alert(jqXHR);
                alert(textStatus);
                alert(errorThrown);
            }
        }); // fin de peticion

    }
}





function fn_selecionaClienteAx(descripcion, id, documentoAx, telefonoAx, direccionAx = null, correoAx, tipoIdentificacionAx) {
    var send;
    lc_documentoAx = documentoAx;
    DOCUMENTO_CLIENTE_AX = documentoAx;
    lc_nombreAx = descripcion;
    lc_telefonoAx = telefonoAx;
    lc_direccionAx = null;
    lc_correoAx = correoAx;

    TELEFONO_CLIENTE_CREDITO = telefonoAx;
    DIRECCION_CLIENTE_CREDITO = direccionAx;
    CORREO_ELECTRONICO_CLIENTE_CREDITO = correoAx;
    TIPO_IDENTITICACION_CLIENTE_EXTERNO = tipoIdentificacionAx;
    NOMBRE_CLIENTE_CREDITO = descripcion;

    $('#hid_cliAx').val(id);
    $('#hid_nombreAx').val(descripcion);
    var pagadoCredito = parseFloat($("#pagado").val()).toFixed(2);
    var totalFactura = parseFloat($("#pagoGranTotal").val()).toFixed(2);
    if (parseFloat(pagadoCredito) >= parseFloat(totalFactura)) {
        cantidadP = 'Total';
    } else if (parseFloat(pagadoCredito) < parseFloat(totalFactura)) {
        cantidadP = 'Parcial';
    }
    if (lc_opcionCreditoEmpresa === 'INVENTARIO') {
        $("#detalleAx").empty();
        $("#modalclienteAx").dialog({ title: descripcion });
        $("#txt_cliAx").val("").hide();
        $("divContenedor").css("padding-top:", "0");
        send = { "cargaCadenasEmpresa": 1 };
        send.documentoEmpresa = documentoAx;
        $.ajax({
            async: false,
            type: "POST",
            dataType: 'json',
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function(datos) {

                if (datos.str > 0) {
                    for (i = 0; i < datos.str; i++) {
                        html = "<input style='font-size: 14px' class='cadenasInput' onclick='fn_selecionaCadena(\"" + datos[i]['cdn_descripcion'] + "\"," + datos[i]['cdn_id'] + ")' id='rd_cad_' type='button' value='" + datos[i]['cdn_descripcion'] + "' />";
                        $("#divCadenas").append(html);
                    }
                    $('#keyboard').hide();
                    $('#keyboard').empty();
                    $('#divCadenasC').shortscroll();
                    $(".jb-shortscroll-wrapper").css("display", "block");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert(jqXHR);
                alert(textStatus);
                alert(errorThrown);
            }
        });
    } else if (lc_opcionCreditoEmpresa === 'INTER') {
        $("#div_conceptos").empty();
        $("#detalleAx").empty();
        $("#modalclienteAx").dialog({ title: descripcion });
        $("#txt_cliAx").val("").hide();

        /*CONSULTA LOS BOTONES DE AYUDA*/
        send = { "cargaConceptosAyuda": 1 };
        $.ajax({
            async: false,
            type: "POST",
            dataType: 'json',
            contentType: "application/x-www-form-urlencoded",
            url: "config_facturacion.php",
            data: send,
            success: function(datos) {
                if (datos.str > 0) {
                    for (i = 0; i < datos.str; i++) {
                        html = "<div style='float: left;width: 130px;height: 75px;margin: 5px;'><button style='font-size: 14px' class='botonesConceptos'  onclick='fn_escribeConcepto(\"" + datos[i]['Descripcion'] + "\")' id='rd_cad_' type='button' value='" + datos[i]['Descripcion'] + "' >" + datos[i]['Descripcion'] + "</button></div>";
                        $("#div_conceptos").append(html);
                    }
                    $('#keyboard').hide();
                    $('#keyboard').empty();
                    $('#divCadenasC').shortscroll();
                    $(".jb-shortscroll-wrapper").css("display", "block");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert(jqXHR);
                alert(textStatus);
                alert(errorThrown);
            }
        });

        $("#divObservacion").show();
        fn_alfaNumerico(txtObservacion);
        $("#modalclienteAx").dialog({
            buttons: {
                "Aceptar": function() {
                    if ($("#txtObservacion").val().length > 4) {
                        fn_insertaFormaPagoCreditoSinCupon(cantidadP);
                    } else {
                        alertify.error("INGRESE UN COMENTARIO.");
                        fn_alfaNumerico(txtObservacion);
                        return false;
                    }
                },
                "Cancelar": function() {
                    $(this).dialog("close");
                    $('.jb-shortscroll-wrapper').show();
                    $('#keyboard').hide();
                    $('#keyboard').empty();
                    $("#txt_cliAx").val('');
                    $("#detalleAx").empty();
                    $("#divCadenas").empty();
                    $("#divRsts").hide();
                    $("#divObservacion").hide();
                    $("#pagado").val('');
                    coma = 0;
                }
            }
        });
        $('.ui-button-text').each(function (i) {
            $(this).html($(this).parent().attr('text'));
        })
    } else {
        //if (banderaUber === 1) {
        if (BANDERA_AGREGADOR === 1) {
            $('.jb-shortscroll-wrapper').show();
            fn_insertaFormaPagoCreditoSinCupon(cantidadP);
        } else {
            //DOCUMENTO_CLIENTE_AX = '';
            $("#modalclienteAx").dialog("close");
            $("#txt_cliAx").val("");
            $("#detalleAx").empty();
            $('#keyboard').hide();
            alertify.set({ labels: { ok: "SI", cancel: "NO" } });
            alertify.confirm("Est&aacute; seguro de aplicar el cr&eacute;dito para el cliente <b>" + descripcion + "</b> por el valor de <b>" + $("#pagado").val() + "</b>?", function(e) {
                if (e) {

                    var vitality = ($("#hidVitality").val());
                        if (vitality == 1) {
                            $('.jb-shortscroll-wrapper').show();
                            flujoCredito();
                        } else {
                    $('.jb-shortscroll-wrapper').show();
                    fn_insertaFormaPagoCreditoSinCupon(cantidadP);
                        }

                } else {
                    $('#pagado').val('');
                }
            });
        }
    }
}



function fn_escribeConcepto(texto) {
    if ($("#txtObservacion").val().trim().length > 0) {
        contenido = $("#txtObservacion").val();
        $("#txtObservacion").val(contenido + " " + texto);
    } else {
        $("#txtObservacion").val(texto);
    }
}

function fn_selecionaCadena(cadena, idC) {
    $("#divRsts").show();
    $("#selRsts").empty();
    var send = { "cargaRstCdn": 1 };
    send.rstI = idC;
    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['rst_id'] + "'>" + datos[i]['rst_descripcion'] + "</option>";
                    $("#selRsts").append(html);
                }
                $("#modalclienteAx").dialog({
                    buttons: {
                        "Aceptar": function() {
                            $('button.ui-button:contains("Aceptar")').attr('disabled', 'disabled');
                            fn_insertaFormaPagoCreditoSinCupon(cantidadP);
                        },
                        "Cancelar": function() {
                            $(this).dialog("close");
                            $('.jb-shortscroll-wrapper').show();
                            $('#keyboard').hide();
                            $('#keyboard').empty();
                            $("#txt_cliAx").val('');
                            $("#detalleAx").empty();
                            $("#divCadenas").empty();
                            $("#divRsts").hide();
                            $("#pagado").val('');
                            coma = 0;
                        }
                    }
                });
                $('.ui-button-text').each(function (i) {
                    $(this).html($(this).parent().attr('text'));
                })

            } else {
                html = "<option value='-1'>==NO EXISTEN REGISTROS==</option>";
                $("#selRsts").append(html);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });

}

function fn_cerrarValorCredito() {
    $("#ingresoValorCredito").show();
}

function fn_popUpAdministrador() {
    $("#adminCreditoSinCupon").show();
    $("#adminCreditoSinCupon").dialog({
        modal: true,
        width: 400,
        heigth: 500,
        resize: false,
        opacity: 0,
        show: "explode",
        hide: "explode",
        duration: 5000,
        position: "center",
        open: function(event, ui) {
            $(".ui-dialog-titlebar").hide();
        }
    });
    $("#adminCreditoSinCupon").dialog("open");
    $("#usr_claveSinCupon").focus();
}


function fn_agregarNumeroSinCupon(valor) {
    lc_cantidad = document.getElementById("usr_claveSinCupon").value;
    if (lc_cantidad == 0 && valor == ".") {

        //si escribimos una coma al principio del nï¿½mero
        document.getElementById("usr_claveSinCupon").value = "0."; //escribimos 0.
        coma2 = 1;
    } else {
        //continuar escribiendo un nï¿½mero
        if (valor == "." && coma2 == 0) {
            //si escribimos una coma decimal pï¿½r primera vez
            lc_cantidad = lc_cantidad + valor;
            document.getElementById("usr_claveSinCupon").value = lc_cantidad;
            coma2 = 1; //cambiar el estado de la coma  
        }
        //si intentamos escribir una segunda coma decimal no realiza ninguna acciï¿½n.
        else if (valor == "." && coma2 == 1) {}
        //Resto de casos: escribir un nï¿½mero del 0 al 9: 	 
        else {
            $("#usr_claveSinCupon").val('');
            lc_cantidad = lc_cantidad + valor;
            document.getElementById("usr_claveSinCupon").value = lc_cantidad;
        }
    }
    fn_focusLectorSinCupon();
}

function fn_focusLectorSinCupon() {
    $("#usr_claveSinCupon").focus();
}

function fn_eliminarCantidadSinCupon() {
    var lc_cantidad = document.getElementById("usr_claveSinCupon").value.substring(0, document.getElementById("usr_claveSinCupon").value.length - 1);
    if (lc_cantidad == "") {
        lc_cantidad = "";
        coma2 = 0;
    }
    if (lc_cantidad == ".") {
        coma2 = 0;
    }
    document.getElementById("usr_claveSinCupon").value = lc_cantidad;
    fn_focusLectorSinCupon();
}


function fn_canSinCupon() {
    $("#adminCreditoSinCupon").dialog("destroy");
}

function fn_okSinCupon() {
    if ($("#usr_claveSinCupon").val() == '') {
        alertify.alert("Ingrese una clave.");
        return false;
    }
    var send = { "validarUsuarioCreditoSinCupon": 1 };
    send.usr_claveSinCupon = $("#usr_claveSinCupon").val();
    $.getJSON("config_facturacion.php", send, function(datos) {
        if (datos.admini == 1) {
            fn_insertaFormaPagoCreditoSinCupon();
            //fn_canalComprobanteCreditoSinCupon();
        } else {
            alertify.confirm("Clave no autorizada. Ingrese clave de Administrador.", function(e) {
                if (e) {
                    alertify.set({ buttonFocus: "none" });
                    $("#usr_claveSinCupon").focus();
                }
            });
            $("#usr_claveSinCupon").val("");
        }
    });
}



function insertaVoucherWs() {


    var send = { "insertaDatosWS": 1 };

    send.txt_tipov_id = $('#txt_tipov_id').val();
    send.txt_vae_cod = $('#txt_vae_cod').val();
    send.vae_cfac_id = $('#txtNumFactura').val();
    send.vae_monto = $('#btnBaseFactura').val();
    send.vae_IDCliente = $('#txt_vae_IDCliente').val();
    send.vae_cdn_id = $('#txtCadenaId').val();
    send.vae_rst_id = $('#txtRestaurante').val();

    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "config_facturacion.php",
        data: send,
        success: function(datos) {

            if (datos["estado"] === 1) {
                setRespuestaWs("Ok");
            } else {
                setRespuestaWs("Error");
            }

        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

var RespuestaWs = "No";

function setRespuestaWs(dat) {
    RespuestaWs = dat;
}

function GetRespuestaWs() {
    return RespuestaWs;
}

function fn_insertaFormaPagoCreditoSinCupon(banderaaa) {
    $("#mdl_rdn_pdd_crgnd").show();
    $("#mdl_pcn_rdn_pdd_crgnd").show();

    if ($("#txt_tipov_id").val() !== undefined) {
        var resultado = "Ok";
        if (resultado === "Ok") {
            fn_insertaFormaPagoCreditoSinCuponExecute(banderaaa);
            fn_APIkds('apiServicioKds');
            $("#alertify-ok").click(function() {
                var btnAtras = $("#btn_salirOrden");
                btnAtras.click();
            });
        } else {
            alertify.alert("Ocurrió un error al insertar valor.");
            $('button.ui-button:contains("Aceptar")').attr('enabled', 'enabled');
            $("#alertify-ok").click(function() {
                var btnAtras = $("#btn_salirOrden");
                btnAtras.click();
            });
        }

        $("#mdl_pcn_rdn_pdd_crgnd").hide();
        $("#mdl_rdn_pdd_crgnd").hide();
    } else { // cupon Voucher
        fn_insertaFormaPagoCreditoSinCuponExecute(banderaaa);
        $("#mdl_pcn_rdn_pdd_crgnd").hide();
        $("#mdl_rdn_pdd_crgnd").hide();
    }
}

/*
 * Aquí se ejectua el stored
 */
function fn_insertaFormaPagoCreditoSinCuponExecute(banderaaa) {

    if(accionBoton == 1){
        alert('Espere un momento por favor, su solicitud se esta enviando')
        return;
    }

    accionBoton = 1;
    var pagooTotal = parseFloat($("#pagoTotal").val());
    var pagadoo = parseFloat($("#pagado").val());  
    var banderaVitality;
    if ($("#hidVitality").val() == 1) {
        banderaVitality = 1;
    } else {
        banderaVitality = 0;
    }
    var send = { "insertarFormaPagoCredito": 1 };
    send.fctCredito_id = $("#txtNumFactura").val();
    send.frmPagoCredito_id = $("#btnFormaPagoId").val();
    send.frmPagoCredito_numSeg = 0;
    send.tfpSwtransaccionalCredito = -1;
    send.banderaCredito = banderaaa;
    if (lc_opcionCreditoEmpresa == 'INVENTARIO') {
        send.observacion = $("#selRsts").val();
    } else {
        send.observacion = $("#txtObservacion").val();
    }
    if (pagooTotal > pagadoo) {
        send.frmPagoBilleteCredito = $("#pagado").val();
        send.fctTotalCredito = $("#pagado").val();
    } else if (pagooTotal == pagadoo) {
        send.frmPagoBilleteCredito = $("#pagado").val();
        send.fctTotalCredito = $("#pagado").val();
    } else {
        send.frmPagoBilleteCredito = $("#pagado").val();
        send.fctTotalCredito = $("#pagoTotal").val();
    }
    send.cliCredito = $('#hid_cliAx').val();
    send.opcionFp = lc_opcionCreditoEmpresa;
    send.documentoClienteAX = DOCUMENTO_CLIENTE_AX;
    send.telefonoClienteAx = TELEFONO_CLIENTE_CREDITO;
    send.direccionClienteAx = DIRECCION_CLIENTE_CREDITO;
    send.correoClienteAx = CORREO_ELECTRONICO_CLIENTE_CREDITO;
    send.tipoIdentificacionCLienteExt = TIPO_IDENTITICACION_CLIENTE_EXTERNO;
    send.nombreCLienteCredito = NOMBRE_CLIENTE_CREDITO;
    send.tipoCliCredito = TIPO_CLIENTE_CREDITO;
    send.banderaVitality = banderaVitality;
    send.valorCampoCodigo = valorCampoCodigo;


    // introduccion del api

    var apiImpresion = getConfiguracionesApiImpresion();

    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
        fn_cargando(1);

        var result = new apiServicioImpresion('impresion_credito_empresa', send.fctCredito_id, 0, send);

        var imprime = result["imprime"];
        var mensaje = result["mensaje"];

        if (!imprime) {
            alertify.success('Imprimiendo credito empresa...');
            fn_cargando(0);

        } else {
        
            alertify.success('Error al imprimir...');
            fn_cargando(0);

        }

        localStorage.removeItem("id_menu");
        localStorage.removeItem("id_cla");
        localStorage.removeItem("es_menu_agregador");
        localStorage.removeItem("id_menu_facturacion");
        localStorage.removeItem('id_agregador');
        localStorage.removeItem("id_cla_facturacion");
        localStorage.removeItem("es_menu_agregador_facturacion");

        $("#btnCancelarPago").attr('disabled', false);
        DOCUMENTO_CLIENTE_AX = '';
        fn_resumenFormaPago();

        var aPagar = parseFloat($("#pagoTotal").val());
        if ($("#pagado").val() == '' || $("#pagado").val() == 0) {
            pagado = $("#pagoTotal").val();
            total = $("#pagoTotal").val();
            $("#pagado").val(total);
        }
        var pagado = parseFloat($("#pagado").val());
        var valor = aPagar - pagado;
        $("#pagoTotal").val(valor.toFixed(2));
        $("#pagado").val("");
        if (lc_opcionCreditoEmpresa == 'EMPLEADO' /*|| lc_opcionCreditoEmpresa === 'PRODUCTO'*/ ) {

            /*
             var aPagar = parseFloat($("#pagoTotal").val());
             if ($("#pagado").val() == '' || $("#pagado").val() == 0) {
             pagado = $("#pagoTotal").val();
             total = $("#pagoTotal").val();
             $("#pagado").val(total);
             }
             var pagado = parseFloat($("#pagado").val());
             var valor = aPagar - pagado;*/
            if (valor <= 0) {
                accionBoton = 0;
                fn_envioFactura();
            }

            $("#pagado").val('');
            valor = parseFloat(valor);
            $("#pagoTotal").val(valor.toFixed(2));

        } else if (lc_opcionCreditoEmpresa === 'INVENTARIO' || lc_opcionCreditoEmpresa === 'EXTERNO' || lc_opcionCreditoEmpresa === 'INTER' || lc_opcionCreditoEmpresa === 'PRODUCTO') {
            $("#modalclienteAx").dialog("close");
            $('#keyboard').hide();
            $('#keyboard').empty();
            if (valor <= 0) {
                accionBoton = 0;
                fn_direccionaMesasUorden($("#txtTipoServicio").val(), $("#txtOrdenPedidoId").val());
            }
            // alert('NO ES EMPLEADO');
            return;
        } else {
            if (banderaaa == 'Total') {
                if ($("#txtTipoServicio").val() == 2) {
                    accionBoton = 0;
                    location.href = '../ordenpedido/userMesas.php';
                }
                if ($("#txtTipoServicio").val() == 1) {
                    accionBoton = 0;
                    fn_obtenerMesa();
                }
            } else {
                var btnNombre = $("#btnAplicarPago").attr("title");
                var aPagar = parseFloat($("#pagoTotal").val());
                if ($("#pagado").val() == '' || $("#pagado").val() == 0) {
                    pagado = $("#pagoTotal").val();
                    total = $("#pagoTotal").val();
                    $("#pagado").val(total);
                }
                var pagado = parseFloat($("#pagado").val());
                var valor = aPagar - pagado;
                if ((valor <= 0 && btnNombre != 'EFECTIVO')) {
                    accionBoton = 0;
                    fn_envioFactura();
                }
                $("#pagado").val('');


            }
        }


    }else{

        $.getJSON("config_facturacion.php", send, function(datos) {
            if (datos.str > 0) {
                localStorage.removeItem("id_menu");
                localStorage.removeItem("id_cla");
                localStorage.removeItem("es_menu_agregador");
                localStorage.removeItem("id_menu_facturacion");
                localStorage.removeItem('id_agregador');
                localStorage.removeItem("id_cla_facturacion");
                localStorage.removeItem("es_menu_agregador_facturacion");
    
                $("#btnCancelarPago").attr('disabled', false);
                DOCUMENTO_CLIENTE_AX = '';
                accionBoton = 0;
                fn_resumenFormaPago();
    
                var aPagar = parseFloat($("#pagoTotal").val());
                if ($("#pagado").val() == '' || $("#pagado").val() == 0) {
                    pagado = $("#pagoTotal").val();
                    total = $("#pagoTotal").val();
                    $("#pagado").val(total);
                }


                var pagado = parseFloat($("#pagado").val());
                var valor = aPagar - pagado;
                $("#pagoTotal").val(valor.toFixed(2));
                $("#pagado").val("");
                if (lc_opcionCreditoEmpresa == 'EMPLEADO' /*|| lc_opcionCreditoEmpresa === 'PRODUCTO'*/ ) {
    
                    /*
                     var aPagar = parseFloat($("#pagoTotal").val());
                     if ($("#pagado").val() == '' || $("#pagado").val() == 0) {
                     pagado = $("#pagoTotal").val();
                     total = $("#pagoTotal").val();
                     $("#pagado").val(total);
                     }
                     var pagado = parseFloat($("#pagado").val());
                     var valor = aPagar - pagado;*/
                    if (valor <= 0) {
                        accionBoton = 0;
                        fn_envioFactura();
                    }

                    $("#pagado").val('');
                    valor = parseFloat(valor);
                    $("#pagoTotal").val(valor.toFixed(2));
                } else if (lc_opcionCreditoEmpresa === 'INVENTARIO' || lc_opcionCreditoEmpresa === 'EXTERNO' || lc_opcionCreditoEmpresa === 'INTER' || lc_opcionCreditoEmpresa === 'PRODUCTO') {
                    $("#modalclienteAx").dialog("close");
                    $('#keyboard').hide();
                    $('#keyboard').empty();
                    if (valor <= 0) {
                        accionBoton = 0;
                        fn_direccionaMesasUorden($("#txtTipoServicio").val(), $("#txtOrdenPedidoId").val());
                    }
                    // alert('NO ES EMPLEADO');
                    return;
                } else {

                    if (banderaaa == 'Total') {
                        if ($("#txtTipoServicio").val() == 2) {
                            accionBoton = 0;
                            location.href = '../ordenpedido/userMesas.php';
                        }
                        if ($("#txtTipoServicio").val() == 1) {
                            accionBoton = 0;
                            fn_obtenerMesa();
                        }
                    } else {
                        var btnNombre = $("#btnAplicarPago").attr("title");
                        var aPagar = parseFloat($("#pagoTotal").val());
                        if ($("#pagado").val() == '' || $("#pagado").val() == 0) {
                            pagado = $("#pagoTotal").val();
                            total = $("#pagoTotal").val();
                            $("#pagado").val(total);
                        }
                        var pagado = parseFloat($("#pagado").val());
                        var valor = aPagar - pagado;
                        if ((valor <= 0 && btnNombre != 'EFECTIVO')) {
                            accionBoton = 0;
                            fn_envioFactura();
                        }
                        $("#pagado").val('');
                    }

                }
            }

        });



    }


}



function fn_buscaPagoCredito() {


    send = { "buscaPagoCredito": 1 };
    send.factutraBuscaCredito = $("#txtNumFactura").val();
    $.getJSON("config_facturacion.php", send, function(datos) {

        if (datos.str > 0) {
            if (datos[0]['valida'] == 1) {
                send = { "cierraFacturaConCredito": 1 };
                send.codFacturaTipoCredito = $("#txtNumFactura").val();
                send.numeroDocumentoAx = datos[0]['documento'];
                $.getJSON("config_facturacion.php", send, function(datos) {
                    if (datos.str > 0) {
                        var aPagar = parseFloat($("#pagoTotal").val());
                        if ($("#pagado").val() == "" || $("#pagado").val() == 0) {
                            pagado = $("#pagoTotal").val();
                            total = $("#pagoTotal").val();
                            $("#pagado").val(total);
                        }
                        var pagado = parseFloat($("#pagado").val());
                        var valor = aPagar - pagado;
                        valor = parseFloat(valor);
                        var valorVuelto = Math.abs(valor).toFixed(2);
                        alertify.alert("SU CAMBIO ES: " + $("#simMoneda").val() + " " + valorVuelto);
                        $("#alertify-ok").click(function(event) {
                            event.stopPropagation();
                            fn_direccionaMesasUorden($("#txtTipoServicio").val(), $("#txtOrdenPedidoId").val());
                        });

                    } else {
                        alertify.alert("Error al cerrar la transacción con pagos tipo creditos");
                        return false;
                    }
                });
            } else {
                fn_envioFactura();
            }
        } else {
            //alertify.alert("Error al validar pagos de tipo creditos.");
            //return false;
            fn_envioFactura();
        }

    });
}