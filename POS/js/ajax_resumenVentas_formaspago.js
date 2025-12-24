$(document).ready(function () {

    fn_cargarAccesosSistema();

    $('#cntndr_dtll_rsmn_vnts').shortscroll();
    fn_cargarConfiguracionResumenVentas();

    var fecha = moment().format('DD/MM/YYYY hh:mm');
    $('#tqt_fch_ctl').html(fecha);

    //Modal Menu Desplegable
    $('#rdn_pdd_brr_ccns').css('display', 'none');
    $('#cnt_mn_dsplgbl_pcns_drch').css('display', 'none');

    $('#boton_sidr').click(function () {
        $('#rdn_pdd_brr_ccns').css('display', 'block');
        $('#cnt_mn_dsplgbl_pcns_drch').css('display', 'block');
    });

    $('#rdn_pdd_brr_ccns').click(function () {
        $('#rdn_pdd_brr_ccns').css('display', 'none');
        $('#cnt_mn_dsplgbl_pcns_drch').css('display', 'none');
    });

    fn_cargarResumenVentas();
});

function fn_cargarConfiguracionResumenVentas() {
    send = {"cargarConfiguracionResumenVentas": 1};
    $.getJSON("config_resumenventas.php", send, function (datos) {
        if (datos.str > 0) {
            $('#tqt_fch_prd').html(datos[0]['fecha']);
        }
    });
}

function fn_cargarResumenVentas() {
    var html = "";
    var tl_total = 0;
    var tl_efectivo = 0;
    var tl_tarjetas = 0;
    var tl_retencioni = 0;
    var tl_retencionf = 0;
    var tl_transacciones = 0;
    var tl_payphone = 0;
    var tl_cheque = 0;
    var tl_ticket = 0;
    var tl_cupones = 0;
    var tl_empleado = 0;
    var usr_anterior = '';
    send = {"cargarResumenVentasFacturas": 1};
    $.ajax({async: false, url: "config_resumenventas_formaspago.php", data: send, dataType: "json", success:
                function (datos) {
                    if (datos.str > 0) {
                        for (i = 0; i < datos.str; i++) {
                            tl_total += datos[i]['Total'];
                            tl_transacciones += datos[i]['Transacciones'];
                            tl_efectivo += datos[i]['Efectivo'];
                            tl_tarjetas += datos[i]['Tarjetas'];
                            tl_retencioni += datos[i]['RetencionI'];
                            tl_retencionf += datos[i]['RetencionF'];
                            tl_payphone += datos[i]['PAYPHONE'];
                            tl_cheque += datos[i]['CHEQUE'];
                            tl_empleado += datos[i]['EMPLEADO'];
                            tl_ticket += datos[i]['Ticket'];
                            tl_cupones += datos[i]['Cupones'];
                            if (datos[i]['usuario'] != usr_anterior) {
                                html += '<tr><td>' + datos[i]['usuario'] + '</td><td class="text-center">' + datos[i]['fecha_inicio'] + '</td><td class="text-center">' + datos[i]['Cupones'] + '</td><td class="text-center">' + datos[i]['Efectivo'].toFixed(2) + '</td><td class="text-center">' + datos[i]['Tarjetas'].toFixed(2) + '</td><td class="text-center">' + datos[i]['RetencionI'].toFixed(2) + '</td><td class="text-center">' + datos[i]['RetencionF'].toFixed(2) + '</td><td class="text-center">' + datos[i]['CHEQUE'] + '</td><td class="text-center">' + datos[i]['PAYPHONE'] + '</td><td class="text-center">' + datos[i]['EMPLEADO'].toFixed(2) + '</td><td class="text-center">' + datos[i]['Transacciones'] + '</td><td class="text-center">' + datos[i]['Total'].toFixed(2) + '</td><td class="text-center">' + datos[i]['Ticket'].toFixed(2) + '</td></tr>';
                            } else {
                                html += '<tr><td></td><td class="text-center">' + datos[i]['fecha_inicio'] + '</td><td class="text-center">' + datos[i]['Cupones'] + '</td><td class="text-center">' + datos[i]['Efectivo'].toFixed(2) + '</td><td class="text-center">' + datos[i]['Tarjetas'].toFixed(2) + '</td><td class="text-center">' + datos[i]['RetencionI'].toFixed(2) + '</td><td class="text-center">' + datos[i]['RetencionF'].toFixed(2) + '</td><td class="text-center">' + datos[i]['CHEQUE'] + '</td><td class="text-center">' + datos[i]['PAYPHONE'] + '</td><td class="text-center">' + datos[i]['EMPLEADO'].toFixed(2) + '</td><td class="text-center">' + datos[i]['Transacciones'] + '</td><td class="text-center">' + datos[i]['Total'].toFixed(2) + '</td><td class="text-center">' + datos[i]['Ticket'].toFixed(2) + '</td></tr>';
                            }
                            usr_anterior = datos[i]['usuario'];
                        }
                        tl_ticket = tl_total / tl_transacciones;
                        html += '<tr class="active tbl_rsmn_vnts_ttls"><th colspan="2">Totales</th><td class="text-center">' + tl_cupones + '</td><td class="text-center">' + tl_efectivo.toFixed(2) + '</td><td class="text-center">' + tl_tarjetas.toFixed(2) + '</td><td class="text-center">' + tl_retencioni.toFixed(2) + '</td><td class="text-center">' + tl_retencionf.toFixed(2) + '</td><td class="text-center">' + tl_cheque + '</td><td class="text-center">' + tl_payphone + '</td><td class="text-center">' + tl_empleado.toFixed(2) + '</td><td class="text-center">' + tl_transacciones + '</td><td class="text-center">' + tl_total.toFixed(2) + '</td><td class="text-center">' + tl_ticket.toFixed(2) + '</td></tr>';
                        $('#tbl_rsm_vnts_prd').append(html);
                    } else {
                        html += '<tr class="active tbl_rsmn_vnts_ttls"><th colspan="2">Totales</th><td class="text-center">' + tl_cupones + '</td><td class="text-center">' + tl_efectivo.toFixed(2) + '</td><td class="text-center">' + tl_tarjetas.toFixed(2) + '</td><td class="text-center">' + tl_retencioni.toFixed(2) + '</td><td class="text-center">' + tl_retencionf.toFixed(2) + '</td><td class="text-center">' + tl_cheque + '</td><td class="text-center">' + tl_payphone + '</td><td class="text-center">' + tl_empleado.toFixed(2) + '</td><td class="text-center">' + tl_transacciones + '</td><td class="text-center">' + tl_total.toFixed(2) + '</td><td class="text-center">' + tl_ticket.toFixed(2) + '</td></tr>';
                        $('#tbl_rsm_vnts_prd').append(html);
                    }
                }
    });
}

function fn_cargarAccesosSistema() {
    send = {"cargarAccesosPerfil": 1};
    send.pnt_id = 'resumenVentas.php';
    $.getJSON("config_resumenventas_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                switch (datos[i]['acc_descripcion']) {
                    case 'Salir':
                        $('#btn_salirSistema').attr("disabled", false);
                        $("#btn_salirSistema").removeClass("boton_Opcion_Bloqueado");
                        $("#btn_salirSistema").addClass('boton_Opcion');
                        break;
                    case 'Funciones Gerente':
                        $('#funcionesGerente').attr("disabled", false);
                        $("#funcionesGerente").removeClass("boton_Opcion_Bloqueado");
                        $("#funcionesGerente").addClass('boton_Opcion');
                        break;
                }
            }
        }
    });
}

function fn_obtenerMesa() {
    send = {"obtenerMesa": 1};
    $.getJSON("config_resumenventas.php", send, function (datos) {
        if (datos.str > 0) {
            var mesa = datos['mesa_asignada'];
            window.location.href = "../ordenpedido/tomaPedido.php?numMesa=" + mesa;
        } else {
            alert("Lo sentimos, vuelva a intentarlo.");
        }
    });
}


function fn_salirSistema() {
    window.location.href = "../index.php";
}

function fn_funcionesGerente() {
    window.location.href = "../funciones/funciones_gerente.php";
}

function fn_irTomaPedido() {
    window.location.href = "../ordenpedido/tomaPedido.php";
}