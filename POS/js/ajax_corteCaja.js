/* global alertify */

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: JOSE FERNANDEZ//////////////////////
////////DESCRIPCION		: AJAX DE PANTALLA DE FIN DE DIA///
////////TABLAS			: ARQUEO_CAJA,BILLETE_ESTACION,///////
//////////////////////////CONTROL_ESTACION,ESTACION///////////
//////////////////////////BILLETE_DENOMINACION////////////////
////////FECHA CREACION	: 20/12/2013///////////////////////////
//////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR:        XAVIER AUCANSHALA P. //////////////////////////////////
///////FECHA MODIFICACION:    15/10/2019 ////////////////////////////////////////////
///////DESCRIPCION:    * Validacion si cadena y estacion aplica pickUP //////////////
//////////////////     * Desmontar estacion PickUp //////////////////////////////////
//////////////////     * Impresión de reporte de desasignación de cajero pickup /////
/////////////////////////////////////////////////////////////////////////////////////

var accionButton = 0;
var arrayBilletes2 = new Array();
var suma2 = 0;
var coma = 0; //estado coma decimal 0=no, 1=si;
k = 0; //columnas
pagina = -1;

var data = {};

var estado_factura = 0;

var params_ajax = {
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../mantenimiento/adminDomicilio/config_domicilio.php",
    data: null
};
var valor_sir = 0;
var valor_maxpoint = 0;

$(document).ready(function () {
    fn_verificaConfiguracionDomicilio();
    $('#listacuentasabiertas').shortscroll();
    $('#listamesasabiertas').shortscroll();
    $('#listadenominaciones').shortscroll();
    $("#content").hide();
    $("#content2").hide();
    fn_cargaMesas();
    fn_cuentaAbierta();
    fn_consultaEstacion();
    fn_consultaMotorizados();
    fn_consultaPedidosApp();
    pagina = -1;
    $("#ok").attr("disabled", false);
    $("#Contenedorcuentas").hide();
    $("#Contenedorfacturas").hide();

    //Modal Menu Desplegable
    $('#id_menu_desplegable').css('display', 'none');
    $('#id_modal_opciones_drc').css('display', 'none');

    $('#boton_sidr').click(function () {
        $('#id_menu_desplegable').css('display', 'block');
        $('#id_modal_opciones_drc').css('display', 'block');
    });

    $('#id_menu_desplegable').click(function () {
        $('#id_menu_desplegable').css('display', 'none');
        $('#id_modal_opciones_drc').css('display', 'none');
    });

    cargar_url_api_motorizados_gerente();

    if (sessionStorage.getItem("continuar_fin_dia") == 1) {
        sessionStorage.removeItem("continuar_fin_dia");

        alertify.alert('<h4>Hemos limpiado las cuentas abiertas y por facturar. Por favor, presione nuevamente el botón <strong>"Finalizar el Día"</strong> para continuar con el proceso.</h4>');
    } else if (sessionStorage.getItem("limpiar_abiertas_facturar") == 1) {
        sessionStorage.removeItem("limpiar_abiertas_facturar");

        alertify.alert('<h4>Hemos limpiado las cuentas abiertas y por facturar.</h4>');
    }

    //llama a una función o ejecuta un fragmento de código de forma reiterada **CP
    myVar1 = setInterval("fn_cargaMesas()", 3000);
    myVar2 = setInterval("fn_cuentaAbierta()", 3000);
});

//Verificacion Configuracion DOMICILIO
function fn_verificaConfiguracionDomicilio() {
    send = {};
    send.metodo = "cargarConfiguracionDomicilio";
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
        success: function (response) {
            $("#hid_existeCajeroDomicilio").val(response.existeCajeroDomicilio);
            $("#hid_aplicaDomicilio").val(response.aplicaDomicilio);
        },
        error: function (a, b, c) {
            cargando(false);
            alert('Error al cargar la configuracion de Domicilio');
        }
    });
}


//CUENTAS ABIERTAS
function fn_cargaMesas() {
    var puede_retomar_ordenes = fn_validaRetomaOrdenCualquierEstacion();
    var html = "";

    var send = {
        "consultaMesa": 1
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#detalle_plu").empty();
                for (i = 0; i < datos.str; i++) {
                    espacio = '&nbsp;';
                    guion2 = '-';
                    total = ' Total:';
cuenta = ' Cuenta:';
                    servicio = $("#hide_tipo_servicio").val();

                    if (servicio == 1) {
                        if (datos[i]['total'] == 0) {
                            html = "<br /><input style='height:60px;width:350px;' class='btn btn-success notiene' type='button' value='" + datos[i]['nombre_estacion'] + "" + espacio + "" + guion2 + "" + total + " $" + datos[i]['total'].toFixed(2) + "'/><br />";
                        } else {
                            html = "<br /><input style='height:60px;width:350px;' onclick=fn_modalMesas(\"" + datos[i]['odp_id'] + "\",'" + datos[i]['mesa_descripcion'] + "',\"" + datos[i]['est_id'] + "\",\"" + datos[i]['nombre_estacion'] + "\",\"" + datos[i]['usr_usuario'] + "\",\"" + datos[i]['cuenta'] + "\",\"" + puede_retomar_ordenes + "\") class='btn btn-primary tiene' type='button' value='" + datos[i]['nombre_estacion'] + ' - Mesa: ' + datos[i]['mesa_descripcion'] + ' - Cuenta: ' + datos[i]['cuenta'] + "" + espacio + "" + guion2 + "" + total + " $" + datos[i]['total'].toFixed(2) + "'/><br />";
                        }
                    } else if (servicio == 2) {
                        if (datos[i]['total'] == 0) {
                            html = "<br /><input style='height:60px;width:350px;' class='btn btn-success notiene' type='button' value='" + datos[i]['nombre_estacion'] + ' - ' + datos[i]['mesa_descripcion'] + "" + espacio + "" + guion2 + "" + total + " $" + datos[i]['total'].toFixed(2) + "'/><br />";
                        } else {
                            html = "<br /><input style='height:60px;width:350px;' onclick=fn_modalMesas(\"" + datos[i]['odp_id'] + "\",'" + datos[i]['mesa_descripcion'] + "',\"" + datos[i]['est_id'] + "\",\"" + datos[i]['nombre_estacion'] + "\",\"" + datos[i]['usr_usuario'] + "\",\"" + datos[i]['cuenta'] + "\",\"" + puede_retomar_ordenes + "\") class='btn btn-primary tiene' type='button' value='" + datos[i]['nombre_estacion'] + ' - Mesa: ' + datos[i]['mesa_descripcion'] + ' - Cuenta: ' + datos[i]['cuenta'] + "" + espacio + "" + guion2 + "" + total + " $" + datos[i]['total'].toFixed(2) + "'/><br />";
                        }
                    }
                    $("#detalle_plu").append(html);
                }
            } else {
                $("#detalle_plu").empty();
                html = "<br /><input class='btn btn-primary' style='height:60px;width:250px;' type='button' value='Ninguna Cuenta Abierta'><br />";
                $("#detalle_plu").append(html);
                $("#hid_controlMesa").val(1);
            }
            $('#detalle_plu').shortscroll();
        }
    });
}

function fn_validaRetomaOrdenCualquierEstacion(){
    var puede_retomar_ordenes = 0;
    var send = {
        "validaRetomaOrdenCualquierEstacion": 1
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                puede_retomar_ordenes = datos[0].retoma_ordenes;
            }
        }
    });
    return puede_retomar_ordenes;
}

//CUENTAS ABIERTAS
function fn_modalMesas(codigo_orden, codigo_mesa, estacionId, nombre_estacion, usuario, cuenta, cualquier_estacion = 0) {
    $("#hid_codigoorden").val(codigo_orden);
$("#hid_cuentaOdp").val(cuenta);

    if ($("#est_id").val() == estacionId || cualquier_estacion == '1') {
        var html = "<tr class='active'><th style='text-align:center'>Cantidad</th><th style='text-align:center'>Descripci&oacute;n</th><th style='text-align:center;'>Precio</th></tr>";
        var send = {
            "consultadetalleMesa": 1
        };
        send.codigoOrden = codigo_orden;
send.cuenta = cuenta;
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_corteCaja.php",
            data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    $("#detalleMesa").empty();
                    for (i = 0; i < datos.str; i++) {
                        if (datos[i]['dop_cantidad'] != 0 && datos[i]['subtotal'] != 0) {
                            html += "<tr><td><input class='form-control' style='width:50px; text-align:center' type='text' readonly='readonly' value=" + datos[i]['dop_cantidad'] + "></td>";
                            html += "<td><input class='form-control' style='width:200px; text-align:left' type='text' readonly='readonly' value='" + datos[i]['plu_descripcion'] + "'></td>";
                            html += "<td><input class='form-control' style='width:70px; text-align:center' type='text' readonly='readonly' value='$" + datos[i]['subtotal'] + "'></td></tr>";
                        }
                        $("#detalleMesa").html(html);
                    }

                    send = {
                        "consultatotalesMesa": 1
                    };
                    send.codigoOrden = codigo_orden;
send.cuenta = cuenta;
                    $.ajax({
                        async: false,
                        type: "GET",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "config_corteCaja.php",
                        data: send,
                        success: function (datos) {
                            if (datos.str > 0) {
                                $("#txt_txt_totalNeto").val('$' + datos.precioNeto.toFixed(2));
                                $("#txt_iva").val('$' + datos.IVA.toFixed(2));
                                $("#txt_totalDetalle").val('$' + datos.total.toFixed(2));
                                $("#txt_codigoMesa").val(codigo_orden);
                                $("#txt_mesa_descripcion").val(codigo_mesa);
                            }
                        }
                    });
                }
            }
        });

        $("#content").dialog({
            title: "Cuetas Abiertas - " + nombre_estacion,
            width: 850,
            autoOpen: false,
            resizable: false,
            show: {},
            hide: {},
            modal: true,
            position: "center",
            closeOnEscape: false
        });

        $("#content").dialog("open");
        $("#btn_okDetalle").click(function () {
            $("#content").dialog("close");
        });
    } else {
        
        var html = "";
        var send = {
            "consultaEstadoOrdenPedido": 1
        };
        send.codigoOrden = codigo_orden;
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_corteCaja.php",
            data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    for (i = 0; i < datos.str; i++) {
                        //odp_total=0 ; pantalla mesas
                        //odp_total=1 ; pantalla orden de pedido
                        var bandera_op = datos[i]['odp_total'];
$("#hid_bandera_op").val(bandera_op);
                        $("#hid_nombreEstacion").val(nombre_estacion);
                        $("#hid_nombreUsuario").val(usuario);
                        $("#hid_nombreMesa").val(codigo_mesa);
                        $("#hid_codigoorden").val(codigo_orden);

                        if (bandera_op == 1) {
                            alertify.alert("<b>Atenci&oacute;n..!!</b> La mesa a la que intenta acceder esta siendo atendida en la estaci&oacute;n <b>" + nombre_estacion + " - MESA " + codigo_mesa + "</b> con el usuario <b>" + usuario + "</b>");
                        } else { //Retomar orden

                            alertify.confirm("¿Desea retomar la cuenta abierta en la estaci&oacute;n <b>" + nombre_estacion + " - MESA " + codigo_mesa + "</b> ?", function (e) {

                                if (e) {
                                    var html = "<tr class='active'><th style='text-align:center'>Cantidad</th><th style='text-align:center'>Descripci&oacute;n</th><th style='text-align:center;'>Precio</th></tr>";
                                    var send = {
                                        "consultadetalleMesa": 1
                                    };
                                    send.codigoOrden = codigo_orden;
send.cuenta = cuenta;
                                    $.ajax({
                                        async: false,
                                        type: "GET",
                                        dataType: "json",
                                        contentType: "application/x-www-form-urlencoded",
                                        url: "config_corteCaja.php",
                                        data: send,
                                        success: function (datos) {
                                            if (datos.str > 0) {
                                                $("#detalleMesa").empty();
                                                for (i = 0; i < datos.str; i++) {
                                                    if (datos[i]['dop_cantidad'] != 0 && datos[i]['subtotal'] != 0) {
                                                        html += "<tr><td><input class='form-control' style='width:50px; text-align:center' type='text' readonly='readonly' value=" + datos[i]['dop_cantidad'] + "></td>";
                                                        html += "<td><input class='form-control' style='width:200px; text-align:left' type='text' readonly='readonly' value='" + datos[i]['plu_descripcion'] + "'></td>";
                                                        html += "<td><input class='form-control' style='width:70px; text-align:center' type='text' readonly='readonly' value='$" + datos[i]['subtotal'] + "'></td></tr>";
                                                    }
                                                    $("#detalleMesa").html(html);
                                                }

                                                send = {
                                                    "consultatotalesMesa": 1
                                                };
                                                send.codigoOrden = codigo_orden;
send.cuenta = cuenta;
                                                $.ajax({
                                                    async: false,
                                                    type: "GET",
                                                    dataType: "json",
                                                    contentType: "application/x-www-form-urlencoded",
                                                    url: "config_corteCaja.php",
                                                    data: send,
                                                    success: function (datos) {
                                                        if (datos.str > 0) {
                                                            $("#txt_txt_totalNeto").val('$' + datos.precioNeto.toFixed(2));
                                                            $("#txt_iva").val('$' + datos.IVA.toFixed(2));
                                                            $("#txt_totalDetalle").val('$' + datos.total.toFixed(2));
                                                            $("#txt_codigoMesa").val(codigo_orden);
                                                            $("#txt_mesa_descripcion").val(codigo_mesa);
                                                        }
                                                    }
                                                });
                                            }
                                        }
                                    });

                                    $("#content").dialog({
                                        title: "Cuetas Abiertas - " + nombre_estacion,
                                        width: 850,
                                        autoOpen: false,
                                        resizable: false,
                                        show: {},
                                        hide: {},
                                        modal: true,
                                        position: "center",
                                        closeOnEscape: false
                                    });

                                    $("#content").dialog("open");
                                    $("#btn_okDetalle").click(function () {
                                        $("#content").dialog("close");
                                    });
                                }
                            });
                        }
                    }
                }
            }
        });
    }
}

//LIMPIA LAS CUENTAS ABIERTAS EN CERO
function fn_LimpiarcuentaAbierta(bandera = true) {
    let send = {
        "validacuenta": 1
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (let i = 0; i < datos.str; i++) {
                    if (datos[i]['valida'] == 0) {
                        alertify.set({
                            labels: {
                                ok: "Ok",
                                cancel: "Cancelar"
                            }
                        });
                        alertify.confirm("¿Desea limpiar las cuentas abiertas o por facturar?", function (e) {
                            if (e) {
                                send = {
                                    "limpiarcuentacero": 1
                                };
                                $.ajax({
                                    async: false,
                                    type: "GET",
                                    dataType: "json",
                                    contentType: "application/x-www-form-urlencoded",
                                    url: "config_corteCaja.php",
                                    data: send,
                                    success: function (datos) {
if (datos['limpiarcuentas'] == 1) {
                                            sessionStorage.setItem("limpiar_abiertas_facturar", "1");
                                        window.location.replace("corteCaja.php");
} else {
                                            alertify.error('Algo inesperado ha ocurrido y no hemos conseguido limpiar las cuentas abiertas o por facturar.');
                                        }
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                        alertify.error('Algo inesperado ha ocurrido y no hemos conseguido limpiar las cuentas abiertas o por facturar.');
                                    }
                                });
                            }
                        });
                    }}
                    } else {
                        alertify.error('Algo inesperado ha ocurrido y no hemos conseguido limpiar las cuentas abiertas o por facturar.');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error('Algo inesperado ha ocurrido y no hemos conseguido limpiar las cuentas abiertas o por facturar.');
        }
    });
}
/*
function fn_validaFindeDia() {
    let send = {
        "validacuenta": 1
    };

    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (let i = 0; i < datos.str; i++) {
                    if (datos[i]['valida'] == 0) {
                        alertify.set({
                            labels: {
                                ok: "Ok",
                                cancel: "Cancelar"
                            }
                        });

                        alertify.confirm("¿Desea cerrar las cuentas abiertas o por facturar?", function (e) {
                            if (e) {
                                send = {
                                    "limpiarcuentacero": 1
                                };

                                $.ajax({
                                    async: false,
                                    type: "GET",
                                    dataType: "json",
                                    contentType: "application/x-www-form-urlencoded",
                                    url: "config_corteCaja.php",
                                    data: send,
                                    success: function (datos) {
                                        if (datos['limpiarcuentas'] == 1) {
                                            sessionStorage.setItem("continuar_fin_dia", "1");

                                            window.location.replace("corteCaja.php");
                                        } else {
                                            ejecFinDeDia();
                    }
                },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                        ejecFinDeDia();
        }
    });
} else {
                                ejecFinDeDia();
                            }
                        });
                    } else {
                        ejecFinDeDia();
                    }
                }
            } else {
                ejecFinDeDia();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            ejecFinDeDia();
        }
    });
}

function ejecFinDeDia() {
    let send = {
        "validaFindeDia": 1
    };

    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    var fechAperturaPeriodo = (datos[i]['fechaAperturaPeriodo']);
                    CambiarEstadoFondosEstacionesPorConfirmar(fechAperturaPeriodo);
                }
            }
        }
    });
}
*/

//CUENTAS POR FACTURAR
function fn_cuentaAbierta() {
    var html = "";

    var send = {
        "consultaCuenta": 1
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#cuenta").empty();
                for (i = 0; i < datos.str; i++) {
                    id_cabfactura = datos[i]['cfac_id'];
                    html = "<br /><input style='height:60px;width:455px;' onclick=fn_modalCuentas('\"" + datos[i]['cfac_id'] + "\"',\"" + datos[i]['est_id'] + "\",\"" + datos[i]['cualquiera_estacion'] + "\",\"" + datos[i]['nombre_estacion'] + "\",\"" + datos[i]['usr_usuario'] + "\",\"" + datos[i]['mesa_descripcion'] + "\",\"" + datos[i]['IDCabeceraOrdenPedido'] + "\",\"" + datos[i]['dop_cuenta'] + "\") class='btn btn-primary' type='button' value='" + datos[i]['nombre_estacion'] + " - Mesa: " + datos[i]['mesa_descripcion'] + " - Cuenta: " + datos[i]['dop_cuenta'] + " - " + datos[i]['cfac_id'] + " - Total: $" + datos[i]['cfac_total'].toFixed(2) + "'/><br />";
                    $("#cuenta").append(html);
                }
            } else {
                $("#cuenta").empty();
                html = "<br /><input class='btn btn-primary' style='height:60px;width:250px;' type='button' value='Ninguna Cuenta por Facturar'><br />";

                $("#cuenta").append(html);
                $("#hid_controlCuenta").val(1);
            }
            $('#cuenta').shortscroll();
        }
    });
}

//CONSULTA EMPLEADOS ASIGNADOS
function fn_consultaEstacion() {
    var html = "";

    var send = {
        "consultaEstacion": 1
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#empleado").empty();
                for (i = 0; i < datos.str; i++) {
                    desmontado = datos[i]['desmontado'];
                    descripUsuario = datos[i]['usr_usuario'];
                    descripUsuario = String(descripUsuario);
                    msjdesmontado = 'Oficina';
                    if (desmontado == 1) {
                        html = "<br /><input style='height:60px;width:200px;' onclick='fn_consultaFormaPago(\"" + datos[i]['est_id'] + "\",\"" + datos[i]['ctrc_id'] + "\",\"" + datos[i]['usr_id'] + "\",\"" + descripUsuario + "\")' class='btn btn-default' type='button' value=" + datos[i]['usr_usuario'] + ">";
                        html += "<input type='hidden' value=" + datos[i]['est_id'] + "/><br />";
                        $("#empleado").append(html);
                    } else {
                        html = "<br /><input style='height:60px;width:200px;' onclick='fn_consultaFormaPago(\"" + datos[i]['est_id'] + "\",\"" + datos[i]['ctrc_id'] + "\",\"" + datos[i]['usr_id'] + "\",\"" + descripUsuario + "\")' class='btn btn-primary' type='button' value=" + datos[i]['usr_usuario'] + ">";
                        html += "<input type='hidden' value=" + datos[i]['usr_id'] + "/><br />";
                        $("#empleado").append(html);
                    }
                }
            } else {
                $("#empleado").empty();
                html = "<br /><input class='btn btn-primary' style='height:60px;width:250px;' type='button' value='Ning&uacute;n Empleado Asignado'><br />";
                $("#empleado").append(html);
                $("#hid_controlEstacion").val(1);
            }
            $('#empleado').shortscroll();
        }
    });
}
//CONSULTA MOTORIZADOS
function fn_consultaMotorizados() {
    var html = "";

    var send = {
        "consultaMotorizados": 1
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#motorizados").empty();
                for (i = 0; i < datos.str; i++) {
                    html = "<br /><input style='height:60px;width:200px;' onclick='fn_consultaDesasignacionMotorizado(\"" + datos[i]['idMotorizado'] + "\",\"" + datos[i]['estado'] + "\",\"" + datos[i]['motorizado'] + "\")' class='btn btn-primary' type='button' value=" + datos[i]['motorizado'] + ">";
                    html += "<input type='hidden' value=" + datos[i]['idMotorizado'] + "/><br />";
                    $("#motorizados").append(html);
                    $("#hid_controlMotorizado").val(0);
                }
            } else {
                $("#motorizados").empty();
                html = "<br /><input class='btn btn-primary' style='height:60px;width:250px;' type='button' value='Ning&uacute;n Motorizado Asignado'><br />";
                $("#motorizados").append(html);
                $("#hid_controlMotorizado").val(1);
            }
            $('#motorizados').shortscroll();
        }
    });
}
//CONSULTA MOTORIZADOS
function fn_consultaPedidosApp() {
    var html = "";

    var send = {
        "consultaPedidosApp": 1
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            console.log(datos);
            if (datos.str > 0) {
                $("#pedidosApp").empty();
                for (i = 0; i < datos.str; i++) {
                    html = "<br /><input style='height:60px;width:295px;' class='btn btn-primary' type='button' value='#" + datos[i]['codigo'] + " - " + datos[i]['estado'] + " - $" + datos[i]['total'].toFixed(2) + "'/><br />";
                    $("#pedidosApp").append(html);
                }
                $("#hid_controlPendientesApp").val(0);
            } else {
                $("#pedidosApp").empty();
                html = "<br /><input class='btn btn-primary' style='height:60px;width:250px;' type='button' value='Ning&uacute;n Pedido Pendiente'><br />";
                $("#pedidosApp").append(html);
                $("#hid_controlPendientesApp").val(1);
            }
            $('#pedidosApp').shortscroll();
        }
    });
}

function fn_consultaFormaPago(codigo_estacion, codigo_ctrEstacion, id_usuario, usuario_descripcion) {
    // Desmontar estacion PickUp, validado si aplica pickup en el cadena y estacion

    send = {
        validaDesasignarCajero: 1
    };
    send.id_usuario = id_usuario;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {

            if (datos.str > 0) {
                if (datos.resp === 0) {

                    nombreUsr = $("#hid_usuarioDescripcion").val();
                    alertify.alert("No se puede desasignar el cajero ya que la hora de cierre de caja es a partir de las: " + "<b>" + datos.salida + "h.</b> ");

                } else {

                    var send = {
                        "PickupConfiguracionEstacion": 1
                    };
                    console.log("datos");
                    console.log(codigo_estacion);
                    console.log(codigo_ctrEstacion);
                    console.log(id_usuario);
                    console.log(usuario_descripcion);
                    // usuario_descripcion = 'V009PICKUP1'
                    $.getJSON("config_corteCaja.php", send, function (datos) {
                        console.log("first")
                        console.log(datos);
                        aplicaCadena = datos[0].ActivoCadena_Pickup;
                        aplicaEstacion = datos[0].ActivoEstacion_Pickup; //Estacion Aplica Pickup Activo = 1
                        nom_usuario = datos[0].Nombre_Pickup;

                        if (aplicaCadena == 1 && aplicaEstacion == 1 && usuario_descripcion == nom_usuario) {
                            //alertify.success('El cajero PickUp esta activo')  
                            alertify.confirm("<h4>¿Desea desmontar el cajero " + nom_usuario + " ?</h4><br>", function (e) {
                                if (e) {
                                    fn_cargando(0);
                                    var send = {
                                        "PickupConfiguracionEstacion": 1
                                    };
                                    $.getJSON("config_corteCaja.php", send, function (datos) {
                                        var send = {
                                            "desmontarEstacionPickup": 1
                                        };
                                        $.ajax({
                                            async: false,
                                            type: "GET",
                                            dataType: "json",
                                            contentType: "application/x-www-form-urlencoded",
                                            url: "config_corteCaja.php",
                                            data: send,
                                            success: function (datos) {
                                                var send;
                                                send = {
                                                    "traeUsuarioAdmin": 1
                                                };
                                                send.accion = 5;
                                                $.ajax({
                                                    async: false,
                                                    type: "GET",
                                                    dataType: "json",
                                                    contentType: "application/x-www-form-urlencoded",
                                                    url: "config_corteCaja.php",
                                                    data: send,
                                                    success: function (datos) { //"config_corteCaja.php"
                                                        if (datos.str > 0) {
                                                            usr_id_admin = datos.usr_id;
                                                            send = {
                                                                "imprimeDesmontadoCajeroPickUp": 1
                                                            };
                                                            send.usr_id = id_usuario;
                                                            send.ctrc_id = codigo_ctrEstacion;

                                                            let apiImpresion = getConfiguracionesApiImpresion();
                                                            if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                                                                imprimirPickup('pickupDesmontarCajero', send);
                                                            } else {
                                                                $.ajax({
                                                                    async: false,
                                                                    type: "GET",
                                                                    dataType: "json",
                                                                    contentType: "application/x-www-form-urlencoded",
                                                                    url: "config_corteCaja.php",
                                                                    data: send,
                                                                    success: function (datos) {}
                                                                });
                                                            }
                                                        }
                                                    }
                                                });

                                                fn_generar_interface(1, 'Pickup');
                                                alertify.success('El cajero PickUp ha sido desasignado correctamente...');
                                                setTimeout(function () {
                                                    window.location.replace("corteCaja.php");
                                                }, 3000);
                                            }
                                        });
                                    });
                                    //  alert("Hola");
                                } else {
                                    alertify.error('No se ha desmontado la caja ' + nom_usuario)
                                }
                            });
                        } else {
                            alertify.error('Es necesario desasignar la caja ' + usuario_descripcion)
                            controlMesa = $("#hid_controlMesa").val();
                            controlCuenta = $("#hid_controlCuenta").val();
                            $("#hid_controlEstacion").val(codigo_ctrEstacion);
                            if ((controlMesa && controlCuenta) == 1) {
                                $("#hid_usuarioDescripcion").val(usuario_descripcion);
                                $("#hid_usuario").val(id_usuario);
                                $("#hid_estacion").val(codigo_estacion);
                                codigo_estacion = codigo_estacion;
                                var send = {
                                    "consultaformaPago": 1
                                };
                                send.codigo = codigo_ctrEstacion;
                                send.codigoUsuario = id_usuario;
                                $.ajax({
                                    async: false,
                                    type: "GET",
                                    dataType: "json",
                                    contentType: "application/x-www-form-urlencoded",
                                    url: "config_corteCaja.php",
                                    data: send,
                                    success: function (datos) {
                                        if (datos.str > 0) {
                                            send = {
                                                "validaEstaciondesmontado": 1
                                            };
                                            send.codigo = codigo_ctrEstacion;
                                            $.getJSON("config_corteCaja.php", send, function (datos) {
                                                if (datos[0]['desmontado'] == 0) {
                                                    send = {
                                                        "ValidaFondoRetirado": 1
                                                    };
                                                    send.usr_claveAdmin = id_usuario; //utilizamos esta variable para enviar el id de usuario
                                                    send.tarjeta = '0';
                                                    $.getJSON("config_corteCaja.php", send, function (datos) {
                                                        if (datos.valida == 1) {
                                                            alertify.alert("El fondo asignado aun no ha sido retirado.");
                                                        } else {
                                                            banderaa = 'Fin';
                                                            if ($("#est_id").val() == codigo_estacion) {
                                                                window.location.replace("desmontado_cajero.php?bandera=" + banderaa);
                                                            } else {
                                                                alertify.alert("No puede retirar el Empleado " + usuario_descripcion + " desde esta estaci&oacute;n.");
                                                            }
                                                        }
                                                    });
                                                } else {
                                                    alertify.alert("Debe proceder ha realizar el <b>Corte de Caja</b> desde la oficina.");
                                                }
                                            });
                                        } else {
                                            send = {
                                                "ValidaFondoRetirado": 1
                                            };
                                            send.usr_claveAdmin = id_usuario; //utilizamos esta variable para enviar el id de usuario
                                            send.tarjeta = '0';
                                            $.getJSON("config_corteCaja.php", send, function (datos) {
                                                if (datos.valida == 1) {
                                                    alertify.alert("El fondo asignado aun no ha sido retirado.");
                                                } else {
                                                    alertify.set({
                                                        labels: {
                                                            ok: "Ok",
                                                            cancel: "Cancelar"
                                                        }
                                                    });
                                                    alertify.confirm("No existen transacciones del empleado asignado, desea retirarlo.", function (e) {
                                                        if (e) {
                                                            usuarioCajero = $("#hid_usuarioDescripcion").val();
                                                            send = {
                                                                "auditoriaCajero": 1
                                                            };
                                                            send.usuarioCajero = $("#hid_usuarioDescripcion").val();
                                                            $.ajax({
                                                                async: false,
                                                                type: "GET",
                                                                dataType: "json",
                                                                contentType: "application/x-www-form-urlencoded",
                                                                url: "config_corteCaja.php",
                                                                data: send,
                                                                success: function (datos) {
                                                                    fn_consultaEstacion();
                                                                    fn_consultaMotorizados();
                                                                }
                                                            });
                                                            window.location.reload();
                                                        } else {}
                                                    });
                                                }
                                            });
                                        }
                                    }
                                });
                            } else {
                                alertify.alert("Existen Cuentas Abiertas &oacute; Cuentas por Facturar.");
                            }
                            $('#listaformaspagos1').shortscroll();
                        }
                    });

                }
            }

        }

    });
    // FIn Desmontar estacion PickUp   
}

function imprimirPickup(tipo, send) {
    fn_cargando(1);
    let result = new apiServicioImpresion(tipo, null, null, send);
    let imprime = result["imprime"];
    let mensaje = result["mensaje"];

    console.log('imprime: ', imprime);
    console.log('imprime: ', mensaje);

    if (!imprime) {
        alertify.success('Imprimiendo pickup...');

    } else {
        alertify.success('Error al imprimir pickup...');
    }
    fn_cargando(0);
}

function fn_cargando(estado) {
    if (estado) {
        $('#cargando').css('display', 'block');
        $('#cargandoimg').css('display', 'block');
    } else {
        $('#cargando').css('display', 'none');
        $('#cargandoimg').css('display', 'none');
    }
}

function fn_modalCuentas(id_detalleFactura, estacion, cualquier_estacion = 0, nombre_estacion, usuario, mesa_descripcion, codigo_orden, cuenta) {
    $("#hid_codigofactura").val(id_detalleFactura);
    $("#hid_idOdp").val(codigo_orden);
    $("#hid_cuentaOdp").val(cuenta);
    if ($("#est_id").val() == estacion || cualquier_estacion == 1) {
        var html = "<tr class='active'><th style='text-align:center'>Cantidad</th><th style='text-align:center'>Descripci&oacute;n</th><th style='text-align:center;'>Precio</th></tr>";
        var send = {
            "consultadetalleCuenta": 1
        };
        send.accion = 1;
        send.idfacDetalle = id_detalleFactura;
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_corteCaja.php",
            data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    $("#detalleCuentass").empty();
                    for (i = 0; i < datos.str; i++) {
                        html += "<tr><td><input class='form-control' style='width:50px; text-align:center' type='text' readonly='readonly' value=" + datos[i]['dtfac_cantidad'] + "></td>";
                        html += "<td><input class='form-control' style='width:180px; text-align:left' type='text' readonly='readonly' value='" + datos[i]['plu_descripcion'] + "'></td>";
                        html += "<td><input class='form-control' style='width:70px; text-align:center' type='text' readonly='readonly' value='$" + datos[i]['subtotal'] + "'></td></tr>"; //

                        $("#detalleCuentass").html(html);
                    }

                    send = {
                        "consultatotalesCuenta": 1
                    };
                    send.accion = 2;
                    send.idfacDetalle = id_detalleFactura;
                    $.ajax({
                        async: false,
                        type: "GET",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "config_corteCaja.php",
                        data: send,
                        success: function (datos) {
                            if (datos.str > 0) {
                                $("#txt_totalNetoCuenta").val('$' + datos.precioNeto.toFixed(2));
                                $("#txt_ivaCuenta").val('$' + datos.IVA.toFixed(2));
                                $("#txt_totalDetalleCuenta").val('$' + datos.total.toFixed(2));
                                $("#txt_codigoFactura").val(id_detalleFactura);
                                $("#txt_factura").val(id_detalleFactura);
                            }
                        }
                    });
                }
            }
        });
        $("#content2").dialog({
            width: 850,
            autoOpen: false,
            resizable: false,
            show: {},
            hide: {},
            modal: true,
            position: "center",
            closeOnEscape: false
        });
        $("#content2").dialog("open");
        $("#btn_okDetalleCuenta").click(function () {
            $("#content2").dialog("close");
        });
    } else {
        
        var html = "";
        var send = {
            "consultaEstadoOrdenPedido": 1
        };
        send.codigoOrden = codigo_orden;
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_corteCaja.php",
            data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    for (i = 0; i < datos.str; i++) {
                        //odp_total=0 ; pantalla mesas
                        //odp_total=1 ; pantalla orden de pedido
                        var bandera_op = datos[i]['odp_total'];
                        
                        if (bandera_op == 1) {
                            alertify.alert("<b>Atenci&oacute;n..!!</b> La cuenta a la que intenta acceder esta siendo atendida en la estaci&oacute;n <b>" + nombre_estacion + " - MESA " + mesa_descripcion + "</b> con el usuario <b>" + usuario + "</b>");
                        } else { //Retomar cuenta por facturar
                            alertify.confirm("¿Desea retomar la cuenta por facturar en la estaci&oacute;n <b>" + nombre_estacion + " - MESA " + mesa_descripcion + "</b> ?", function (e) {

                                if (e) {
                                    var html = "<tr class='active'><th style='text-align:center'>Cantidad</th><th style='text-align:center'>Descripci&oacute;n</th><th style='text-align:center;'>Precio</th></tr>";
                                    var send = {
                                        "consultadetalleCuenta": 1
                                    };
                                    send.accion = 1;
                                    send.idfacDetalle = id_detalleFactura;
                                    $.ajax({
                                        async: false,
                                        type: "GET",
                                        dataType: "json",
                                        contentType: "application/x-www-form-urlencoded",
                                        url: "config_corteCaja.php",
                                        data: send,
                                        success: function (datos) {
                                            if (datos.str > 0) {
                                                $("#detalleCuentass").empty();
                                                for (i = 0; i < datos.str; i++) {
                                                    html += "<tr><td><input class='form-control' style='width:50px; text-align:center' type='text' readonly='readonly' value=" + datos[i]['dtfac_cantidad'] + "></td>";
                                                    html += "<td><input class='form-control' style='width:180px; text-align:left' type='text' readonly='readonly' value='" + datos[i]['plu_descripcion'] + "'></td>";
                                                    html += "<td><input class='form-control' style='width:70px; text-align:center' type='text' readonly='readonly' value='$" + datos[i]['subtotal'] + "'></td></tr>"; //

                                                    $("#detalleCuentass").html(html);
                                                }

                                                send = {
                                                    "consultatotalesCuenta": 1
                                                };
                                                send.accion = 2;
                                                send.idfacDetalle = id_detalleFactura;
                                                $.ajax({
                                                    async: false,
                                                    type: "GET",
                                                    dataType: "json",
                                                    contentType: "application/x-www-form-urlencoded",
                                                    url: "config_corteCaja.php",
                                                    data: send,
                                                    success: function (datos) {
                                                        if (datos.str > 0) {
                                                            $("#txt_totalNetoCuenta").val('$' + datos.precioNeto.toFixed(2));
                                                            $("#txt_ivaCuenta").val('$' + datos.IVA.toFixed(2));
                                                            $("#txt_totalDetalleCuenta").val('$' + datos.total.toFixed(2));
                                                            $("#txt_codigoFactura").val(id_detalleFactura);
                                                            $("#txt_factura").val(id_detalleFactura);
                                                        }
                                                    }
                                                });
                                            }
                                        }
                                    });
                                    $("#content2").dialog({
                                        title: 'Cuentas por Facturar - ' + nombre_estacion,
                                        width: 850,
                                        autoOpen: false,
                                        resizable: false,
                                        show: {},
                                        hide: {},
                                        modal: true,
                                        position: "center",
                                        closeOnEscape: false
                                    });
                                    $("#content2").dialog("open");
                                    $("#btn_okDetalleCuenta").click(function () {
                                        $("#content2").dialog("close");
                                    });
                                }
                            });
                        }
                    }
                }
            }
        });
    }
}

/*----------------------------------------------------------------------------------------------------
 Funci�n retoma una cuenta abierta
 -----------------------------------------------------------------------------------------------------*/
function fn_retomarCuentaAbierta() {
    var send;
    if (Cod_FacturaRetomar.indexOf('F') > -1) {
        var odp_id = 0;
        var dop_cuenta = 1;
        var mesa_id = 0;
        send = {
            "retomarCuentaAbierta": 1
        };
        send.accion = 3;
        send.cfac_id = Cod_FacturaRetomar;
        $.getJSON("config_anularOrden.php", send, function (datos) {
            if (datos.str > 0) {
                odp_id = datos[0]['odp_id'];
                mesa_id = datos[0]['mesa_id'];
                dop_cuenta = datos[0]['dop_cuenta'];
                $('#contenedorRetomarOrden').html('<form action="../facturacion/factura.php" name="cobro" method="post" style="display:none;"><input type="text" name="odp_id" value="' + odp_id + '" /><input type="text" name="dop_cuenta" value="' + dop_cuenta + '" /><input type="text" name="mesa_id" value="' + mesa_id + '" /></form>');
                document.forms['cobro'].submit();
            }
        });
    } else {
        send = {
            "consultarMesaOrden": 1
        };
        send.odp_id = Cod_FacturaRetomar;
        $.getJSON("config_anularOrden.php", send, function (datos) {
            if (datos.str > 0) {
                window.location.replace("../ordenpedido/tomaPedido.php?numMesa=" + datos[0]['mesa_id']);
            }
        });
    }
}

function fn_retomarCuenta() {
    var ls_cuenta = "";
    var odp_id = 0;
    var dop_cuenta = 0;
    var mesa_id = 0;

    ls_cuenta = $("#txt_codigoFactura").val();
    //alertify.alert("Retomar cuenta: "+ls_cuenta);
    var send = {
        "retomarCuentaAbierta": 1
    };
    send.accion = 3;
    send.cfac_id = ls_cuenta;
    $.getJSON("config_corteCaja.php", send, function (datos) {
        if (datos.str > 0) {
            odp_id = datos[0]['odp_id'];
            mesa_id = datos[0]['mesa_id'];
            dop_cuenta = datos[0]['dop_cuenta'];
var es_agregador = datos[0]['es_agregador'];
            //alert('CorteCaja: '+es_agregador)
            localStorage.setItem("ls_recupera_orden", 1);
            localStorage.setItem("ls_recupera_orden_id", odp_id);
            localStorage.setItem("ls_recupera_orden_mesa", mesa_id);
            localStorage.setItem("ls_recupera_orden_cuenta", dop_cuenta);
localStorage.setItem("es_menu_agregador", es_agregador);
            fn_actualizaEstacionOrdenPedidoFastFood(odp_id);

            $('#contenedorRetomarOrden').html('<form action="../facturacion/factura.php" name="cobro" method="post" style="display:none;"><input type="text" name="odp_id" value="' + odp_id + '" /><input type="text" name="dop_cuenta" value="' + dop_cuenta + '" /><input type="text" name="mesa_id" value="' + mesa_id + '" /></form>');
            document.forms['cobro'].submit();
        }
    });
}

function fn_retomarMesa() {
    var li_mesa = 0;
var cuenta = 0;
    li_mesa = $("#txt_codigoMesa").val();
cuenta = $("#hid_cuentaOdp").val();
fn_actualizaEstacionOrdenPedidoFastFood(li_mesa);
    var send = {
        "consultarMesaOrden": 1
    };
    send.odp_id = li_mesa;
    $.getJSON("config_corteCaja.php", send, function (datos) {
        if (datos.str > 0) {
            window.location.replace("../ordenpedido/tomaPedido.php?numMesa=" + datos[0]['mesa_id'] + "&numSplit=" + cuenta);
        }
    });

   
}

function fn_validaFindeDia() {
    if(accionButton == 1){
        alert('Espere un momento, la solicitud se esta procesando')
        return;
    }
    accionButton = 1;
    let send = {"validaFindeDia": 1};
    fn_LimpiarcuentaAbierta(false);
    modalBarraProgreso(1);
    barraProgreso(90, '<span class="spinnerBarraProgreso"></span> Estamos validando algunas cosas . . .');
    $.ajax({
            async: true,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_corteCaja.php",
            data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    for (i = 0; i < datos.str; i++) {
                        var fechAperturaPeriodo = (datos[i]['fechaAperturaPeriodo']);
                        CambiarEstadoFondosEstacionesPorConfirmar(fechAperturaPeriodo);
                    }
                } else {
                    accionButton = 0;
                    modalBarraProgreso(0);
                    alertify.error("Error...");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                modalBarraProgreso(0);
                accionButton = 0;
                alertify.error("Error...");
            }
        });
}

function enviarSalesSummaryQPM() {
    let send = {
        transaccion: 'SalesSummary',
        parametros: {
            rst_id: $("#hide_rst_id").val(),
            cdn_id: $("#cdn_id").val(),
            EventType: 'Add',
            periodo: $("#IDPeriodo").val(),
            accion: '1',
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
        },
        error: function (e) {
            console.log(e);
        }
    });
}

var validarMotorizadosAsignados = function () {
    var send = {
        "validarMotorizadoAsignados": 1
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            //console.log("Datos: ", datos);
            if (datos.estado > 0) {
                $("#hid_controlMotorizado").val(datos.estado);
            } else {
                $("#hid_controlMotorizado").val(datos.estado);
            }
        }
    });
}
var validarPendientesApp = function () {
    var send = {
        "validarPendientesApp": 1
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            //console.log("Datos: ", datos);
            if (datos.estado > 0) {
                $("#hid_controlPendientesApp").val(datos.estado);
            } else {
                $("#hid_controlPendientesApp").val(datos.estado);
            }
        }
    });
}

var validarPendienteKiUP = function () {
    let control;
    var send = {
        "consultaEstacion": 1
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                control = 0;
            } else {
                control = 1;
            }
        }
    });
    return control;
}

function totalVentaPorControlEstacion(fechaAperturaPeriodo) {
    // var totalDevuelto = 0
    // //console.log(idControlEstacion);
    // var send = {"devuelveTotalVentaPorEstacion": 1};

    // send.fechaAperturaPeriodo=fechaAperturaPeriodo;
    // $.ajax({
    //     async: false, 
    //     type: "GET", 
    //     dataType: "json", 
    //     contentType: "application/x-www-form-urlencoded", 
    //     url: "config_corteCaja.php", 
    //     data: send,
    //     success: function (datos) {
    //         if(datos[0]["total"] !== null){
    //             totalDevuelto=(datos[0]["total"]);   
    //         }else{
    //             totalDevuelto=(0);
    //         }

    //     },error: function(XMLHttpRequest, textStatus, errorThrown) { 
    //         alert("Status: " + textStatus); alert("Error: " + errorThrown); 
    //     }  
    // });
    return (totalDevuelto);
}

async function cuadraCaja(fechaAperturaPeriodo) {

    var cuadrado;
    var acumulaTotales = 0;
    var restaurante = 0;
    var localizacion = '';
    console.log(fechaAperturaPeriodo);
    var send = {
        "devuelveControlEstacion": 1,
        "fechaAperturaPeriodo": fechaAperturaPeriodo
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            // console.log("esto devuelve restaurante")
            // console.log(datos);
            localizacion = datos[0]["rst_localizacion"];
            restaurante = datos[0]["rst_id"];

            //acumulaTotales=totalVentaPorControlEstacion(fechaAperturaPeriodo);

            //console.log(idControlEstacion);
            var send = {
                "devuelveTotalVentaPorEstacion": 1
            };

            send.fechaAperturaPeriodo = fechaAperturaPeriodo;
            $.ajax({
                async: false,
                type: "GET",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_corteCaja.php",
                data: send,
                success: function (datos) {
                    if (datos[0]["total"] !== null) {
                        acumulaTotales = (datos[0]["total"]);
                    } else {
                        acumulaTotales = (0);
                    }

                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    // alert("Status: " + textStatus); alert("Error: " + errorThrown); 
                    console.log("error")
                }
            });


        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            // alert("Error: " + 'Revisar Stored'); 
            console.log("error con store");
        }
    });

    var arrayDeFecha = fechaAperturaPeriodo.split('/');
    var anio = arrayDeFecha[2]
    var mes = arrayDeFecha[1]
    var dia = arrayDeFecha[0]
    var periodo = anio + "-" + mes + "-" + dia;

    //consumir api 
    //console.log("********************")
    // // data de prueba 
    // var data={
    //     "restaurante": 186,
    //     "periodo": "2022-01-26",
    //     "valor_venta_total": 0
    // }


    var data = {
        "restaurante": restaurante,
        "periodo": periodo,
        "valor_venta_total": acumulaTotales,
        "localizacion": localizacion
    }
    valor_maxpoint = acumulaTotales;

    console.log("data Enviada");
    console.log(data);
    obtenerUrlControlMxpSir();
    var url_sir = localStorage.getItem('url_sir');
    // var politica_activa=localStorage.getItem('politica_activa');
    var politica_activa
    //console.log(localStorage.getItem('url_sir'));

    var send = {
        "politicaControlCajaActiva": 1,
        "rst_id": restaurante
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            console.log("esto devuelve");
            console.log(datos);
            politica_activa = datos[0]["variableB"];
            console.log(politica_activa);


        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            //alert("Status: " + textStatus); alert("Error: " + 'Revisar Stored'); 
        }
    });



    if (politica_activa == 1) {
        var send;
        send = {};
        send.metodo = "controlSIR";
        send.url_sir = url_sir;
        send.data = data;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "clienteWS_controlSIR.php",
            data: send,
            success: function (datos) {
                cuadrado = datos["success"];
                valor_sir = datos["valor_en_sir"];
            }
        });

        if (cuadrado == true) {
            return (1);
        } else {
            return (0);
        }
    } else if (politica_activa.trim() != "0") {
        alertify.alert("<h4><b>Atenci&oacute;n..!!</b> " + politica_activa.trim() + "</h4>");
        return (1);
    } else if (politica_activa.trim() != "0") {
        alertify.alert("<h4><b>Atenci&oacute;n..!!</b> " + politica_activa.trim() + "</h4>");
        return (1);
    } else {
        return (1);
    }
}


async function fn_finDia(fechaAperturaPeriodo) {

    var cajaCuadrada = await cuadraCaja(fechaAperturaPeriodo);
    console.log("caja: " + cajaCuadrada);
    validarCuentasAbiertas()
    validarMotorizadosAsignados();
    validarPendientesApp();
    let control = validarPendienteKiUP();
    var fecha_actual = new Date();
    var hora = fecha_actual.getHours();
    var minuto = fecha_actual.getMinutes();
    var segundo = fecha_actual.getSeconds();
    var meridiano = "";
    if (hora > 12) {
        meridiano = " pm";
    } else {
        meridiano = " am";
    }
    if (hora < 10) {
        hora = "0" + hora;
    }
    if (minuto < 10) {
        minuto = "0" + minuto;
    }
    if (segundo < 10) {
        segundo = "0" + segundo;
    }
    var horita = hora + ":" + minuto + ":" + segundo + meridiano;

    controlMesa = $("#hid_controlMesa").val();
    controlCuenta = $("#hid_controlCuenta").val();
    controlEstacion = $("#hid_controlEstacion").val();
    controlMotorizado = $("#hid_controlMotorizado").val();
    controlPendientesApp = $("#hid_controlPendientesApp").val();

    if (controlMesa == 1 && controlCuenta == 1 && controlEstacion == 1 && controlMotorizado == 1 && controlPendientesApp == 1 && control == 1 && cajaCuadrada == 1) {
        var valido = valida24Horas(fechaAperturaPeriodo);
        if (valido !== null) {
            if (valido) {
                barraProgreso(180, '<span class="spinnerBarraProgreso"></span> Estamos por finalizar el día . . .');
                alertify.set({
                    labels: {
                        ok: "Ok",
                        cancel: "Cancelar"
                    }
                });

                alertify.confirm("<h4>Desea finalizar el d&iacute;a de la fecha: <b>" + fechaAperturaPeriodo + "</b><br> siendo las <b>" + horita + "</h4>", function (e) {
                    if (e) {

                        //Ejecutar la interface de ventas
                        fn_generar_interface(2, 'findia');

                        // enviar Sales Summary a QPM
                        enviarSalesSummaryQPM();

                        // FINALIZA EL DIA
                        fn_aplicaFindeDia();

                        //clean data on dragonTail
                        dragonTailCleanData();
                    } else {
                        modalBarraProgreso(0);
                        accionButton = 0;
                    }
                });
            } else {
                modalBarraProgreso(0);
                alertify.alert("<h4>Restaurantes 24 horas no pueden cerrar el periodo antes de que termine el d&iacute;a (00h00).</h4>");
                accionButton = 0;
            }
        } else {
            modalBarraProgreso(0);
            alertify.alert("<h4>Ha ocurrido un error! Por favor intentar de nuevo.</h4>");
            accionButton = 0;
        }
    } else {
        modalBarraProgreso(0);
        accionButton = 0;
        if ( controlMotorizado != 1 ) {
            alertify.alert("<h4><b>Atenci&oacute;n..!!</b> Existen motorizados asignados al periodo.</h4>");
        } else if (controlPendientesApp != 1) {
            alertify.alert("<h4><b>Atenci&oacute;n..!!</b> Existen pedidos pendientes por cerrar.</h4>");
        } else if ((controlMesa && controlCuenta) != 1) {
            alertify.alert("<h4><b>Atenci&oacute;n..!!</b> Existen cuentas pendientes por cerrar.</h4>");
        } else if (controlEstacion != 1) {
            alertify.alert("<h4><b>Atenci&oacute;n..!!</b> Existen empleados asignados, proceda a desasignarlos.</h4>");
        } else if (control != 1) {
            alertify.alert("<h4><b>Atenci&oacute;n..!!</b> Existen cajas asignadas, proceda a desasignarlos.</h4>");
        } else if (cajaCuadrada != 1) {
            alertify.alert("<h4><b>Atenci&oacute;n..!!</b> Existe descuadre de cajas.Por favor revisar valores totales. de SIR - MXP. <br><b>Valor Sir: " + valor_sir + " <br> Valor Maxpoint: " + valor_maxpoint + "</b></h4>");

        }
    }
}

async function dragonTailCleanData(){
    let session = JSON.parse(await getSessionConfig());
    send = { restaurantId:session['idRestaurante'],}
    $.ajax({
        data: send,
        url: "../resources/module/domicilio/dragon-tail/cleanDataController.php",
        type: 'POST',
        success: function(data){
            alertify.success(data);
        },
        error: function(jqXHR, exception) {
            alertify.error("error "+jqXHR.responseText);
        }
    });
}
function getSessionConfig() {
    return $.ajax({
        url: "../resources/module/domicilio/dragon-tail/sessionControler.php",
        type: 'GET',
    });
}

function valida24Horas(fechaAperturaPeriodo) {
    var valido = null;
    var send = {
        "valida24Horas": 1
    };
    send.fechaAperturaPeriodo = fechaAperturaPeriodo;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos !== null) {
                if (datos[0]["valido"]) {
                    valido = true;
                } else {
                    valido = false;
                }
            }
        }
    });
    return valido;
}

function obtenerUrlControlMxpSir() {

    var send = {
        "obtenerUrlSir": 1
    };

    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {

            localStorage.setItem("url_sir", datos.url);
            localStorage.setItem("politica_activa", datos.activa);
        }


    });
    //return respuesta;
}


function CambiarEstadoFondosEstacionesPorConfirmar(fechaAperturaPeriodo) {
    var send = {
        "cambiarEstadoEstaciones": 1
    };
    send.fechaAperturaPeriodo = fechaAperturaPeriodo;
    $.ajax({
        async: true,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            fn_finDia(fechaAperturaPeriodo);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            modalBarraProgreso(0);
            accionButton = 0;
            alertify.error("Error...");
        }
    });


}

function fn_aplicaFindeDia() {
    barraProgreso(300, '<span class="spinnerBarraProgreso"></span> Estamos concluyendo . . .');
    var finDia = {"finDia": 1};
    var send = finDia;
    $.ajax({
        async: true,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            barraProgreso(360, 'Hemos finalizado el día.');
            fn_ejecutarCambioIva();
            alertify.alert("<h4>Ha finalizado el día correctamente.</h4>", function () {
                modalBarraProgreso(0);
                accionButton = 0;
                window.location.replace("../index.php");
            });

            fn_imprime_findeldia();
            fn_apagarEstacion();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            modalBarraProgreso(0);
            accionButton = 0;
            alertify.error("Error...");
        }
    });
}

//IMPRESION REPORTE FIN DEL DIA
function fn_imprime_findeldia() {
    var traeUsuarioAdmin = {
        "traeUsuarioAdmin": 1
    };

    IDPeriodo = $('#IDPeriodo').val();
    IDEstacion = $('#est_id').val();

    var send = traeUsuarioAdmin;
    send.accion = 5;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                var InsertcanalmovimientoFindelDia = {
                    "InsertcanalmovimientoFindelDia": 1
                };
                usr_id_admin = datos.usr_id;

                send = InsertcanalmovimientoFindelDia;
                send.periodo = IDPeriodo;
                send.estacion = IDEstacion;
                send.usr_id_admin = usr_id_admin;

                var apiImpresion = getConfiguracionesApiImpresion();
                // console.log(apiImpresion);        
                if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                    fn_cargando(1);
                    var result = new apiServicioImpresion('findeldia', null, null, send);
                    var imprime = result["imprime"];
                    var mensaje = result["mensaje"];

                    console.log('imprime: ', imprime);
                    console.log('imprime: ', mensaje);

                    if (!imprime) {
                        alertify.success('Imprimiendo Fín del día...');

                    } else {
                        alertify.alert(mensaje);

                        alertify.success('Error al imprimir Fín del día...');
                        fn_cargando(0);
                    }

                } else {

                    $.ajax({
                        async: false,
                        type: "GET",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "config_corteCaja.php",
                        data: send,
                        success: function (datos) {

                        }
                    });
                }
            }
        }
    });
}

//INSERTA EN CANAL MOVIMIETNO - APAGAR CAJA
function fn_apagarEstacion() {
    var grabaCanalCierraSistema = {
        "grabaCanalCierraSistema": 1
    };

    var send = grabaCanalCierraSistema;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {

        }
    });
}

//CUENTAS ABIERTAS
function fn_validarUsuarioAdministrador(retomar) {
    var puede_retomar_ordenes = fn_validaRetomaOrdenCualquierEstacion();
    servicio = $("#hide_tipo_servicio").val();
    estado_factura = 1;
    if (servicio == 1) {
        $("#Contenedorcuentas").show();
        $("#Contenedorcuentas").dialog({
            modal: true,
            width: 500,
            heigth: 500,
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
        $("#content").dialog("close");
    } else {

        var codigo_orden = $("#hid_codigoorden").val();
        var send = {
            "consultaEstadoOrdenPedido": 1
        };
        send.codigoOrden = codigo_orden;
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_corteCaja.php",
            data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    for (i = 0; i < datos.str; i++) {
                        //odp_total=0 ; pantalla mesas
                        //odp_total=1 ; pantalla orden de pedido
                        var bandera_op = datos[i]['odp_total'];
                        var nombre_estacion = $("#hid_nombreEstacion").val();
                        var usuario = $("#hid_nombreUsuario").val();
                        var codigo_mesa = $("#hid_nombreMesa").val();                        

                        if (bandera_op == 1 && puede_retomar_ordenes != '1') {
                            alertify.alert("<b>Atenci&oacute;n..!!</b> La mesa a la que intenta acceder esta siendo atendida en la estaci&oacute;n <b>" + nombre_estacion + " - MESA " + codigo_mesa + "</b> con el usuario <b>" + usuario + "</b>");
                        } else { //Retomar orden
//retomar = 1 --> retoma la cuenta abierta
        //retomar = 0 --> anula la cuenta abierta
        if (retomar == 1) {
        fn_retomarMesa();
} else {
            alertify.confirm("Est&aacute; seguro de anular la orden ?", function (e) {
                if (e) {
                    condicionFacturacionOrdenPedido( codigo_orden );
                                        //fn_anularMesa();
                }
            });
        }
}
                    }
                }
            }
        });

    }
}

function fn_validarCredencialesUsuario() {
    $("#numPad").hide();
    var usr_clave = $("#usr_clave").val();
    var cod_orden = $("#hid_codigoorden").val();

    if (usr_clave.indexOf('%') >= 0) {
        var old_usr_clave = usr_clave.split('?;')[0];
        var new_usr_clave = old_usr_clave.replace(new RegExp("%", 'g'), "");
        var usr_tarjeta = new_usr_clave;
        usr_clave = 'noclave';
    } else {
        var usr_tarjeta = 0;
    }

    if (usr_clave != "") {
        var send = {
            "validarCreencialesUsuario": 1
        };
        send.usr_clave = usr_clave;
        send.usr_tarjeta = usr_tarjeta;
        send.movimiento = cod_orden;
        send.factura = '';
        $.getJSON("config_corteCaja.php", send, function (datos) {
            if (datos.str > 0) {
                $("#Contenedorcuentas").dialog("close");
                $("#usr_clave").val("");
                fn_retomarMesa();
            } else {
                fn_numerico("#usr_clave");
                alertify.confirm("No tiene permisos para retomar esta Cuenta Abierta.", function (e) {
                    if (e) {
                        alertify.set({
                            buttonFocus: "none"
                        });
                        $("#usr_clave").focus();
                    }
                });
                $("#usr_clave").val("");
            }
        });
    } else {
        fn_numerico("#usr_clave");
        alertify.confirm("Ingrese la clave para retomar Cuenta Abierta", function (e) {
            if (e) {
                alertify.set({
                    buttonFocus: "none"
                });
                $("#usr_clave").focus();
            }
        });
        $("#usr_clave").val("");
    }
}

function fn_cerrarDialogoAnulacion() {
    $('#usr_clave').val('');
    $("#numPad").hide();
    $("#Contenedorcuentas").dialog("close");
    $("#content").dialog("open");
    $(".ui-dialog-titlebar").show();
}

//CUENTAS POR FACTURAR
function fn_validarUsuarioAdministradorfacturas(retomar) {
    servicio = $("#hide_tipo_servicio").val();
estado_factura = 2;
    if (servicio == 1) {
        $("#Contenedorfacturas").show();
        $("#Contenedorfacturas").dialog({
            modal: true,
            width: 500,
            heigth: 500,
            resize: false,
            opacity: 0,
            show: "none",
            hide: "none",
            duration: 500,
            open: function (event, ui) {
                $(".ui-dialog-titlebar").hide();
                $('#usr_claves').attr('onchange', 'fn_validarCredencialesUsuariofactura()');
                fn_numericos("#usr_claves");
            }
        });
        $("#content2").dialog("close");
    } else {
//retomar = 1 --> retoma la cuenta abierta
        //retomar = 0 --> anula la cuenta abierta
        if (retomar == 1) {
        fn_retomarCuenta();
} else {
            alertify.confirm("Est&aacute; seguro de anular la cuenta por facturar ?", function (e) {
                if (e) {
                    var odp_id = $("#hid_idOdp").val();                     
                    condicionFacturacionOrdenPedido( odp_id )
                    //fn_anularCuentaPorFacturar();
                                    }
            });
        }
    }
}

function fn_validarCredencialesUsuariofactura() {
    $("#numPad").hide();
    var usr_clave = $("#usr_claves").val();
    var factura = $("#hid_codigofactura").val();

    if (usr_clave.indexOf('%') >= 0) {
        var old_usr_clave = usr_clave.split('?;')[0];
        var new_usr_clave = old_usr_clave.replace(new RegExp("%", 'g'), "");
        var usr_tarjeta = new_usr_clave;
        usr_clave = 'noclave';
    } else {
        var usr_tarjeta = 0;
    }

    if (usr_clave != "") {
        var send = {
            "validarCreencialesUsuariofactura": 1
        };
        send.usr_clave = usr_clave;
        send.usr_tarjeta = usr_tarjeta;
        send.movimiento = 0;
        send.factura = factura;
        $.getJSON("config_corteCaja.php", send, function (datos) {
            if (datos.str > 0) {
                $("#Contenedorfacturas").dialog("close");
                $("#usr_claves").val("");
                fn_retomarCuenta();
            } else {
                fn_numericos("#usr_claves");
                alertify.confirm("No tiene permisos para retomar esta Cuenta por Facturar.", function (e) {
                    if (e) {
                        alertify.set({
                            buttonFocus: "none"
                        });
                        $("#usr_claves").focus();
                    }
                });
                $("#usr_claves").val("");
            }
        });
    } else {
        fn_numericos("#usr_claves");
        alertify.confirm("Ingrese la clave para retomar Cuenta por Facturar", function (e) {
            if (e) {
                alertify.set({
                    buttonFocus: "none"
                });
                $("#usr_claves").focus();
            }
        });
        $("#usr_claves").val("");
    }
}

function fn_cerrarDialogofacturar() {
    $('#usr_claves').val('');
    $("#numPad").hide();
    $("#Contenedorfacturas").dialog("close");
    $("#content2").dialog("open");
    $(".ui-dialog-titlebar").show();
}

function fn_cerrarValidaAdmin() {
    $("#credencialesAdmin").dialog('close');
    $("#credencialesAdminteclado").hide();
    $("#usr_claveAdmin").val('');
}

function fn_funcionesGerente() {
    var send = {
        "ValidaRegreso": 1
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    if (datos[i]['Confirmar'] == 1) {
                        window.location.replace("../funciones/funciones_gerente.php");
                    } else {
                        alertify.alert("No existe empleado asignado a esta estaci&oacute;n, proceda asignar uno.");
                        window.location.replace("../index.php");
                    }
                }
            }
        }
    });
}

function fn_salirSistema() {
    var send = {
        "ValidaRegreso": 1
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    if (datos[i]['Confirmar'] == 1) {
                        window.location.replace("../index.php");
                    } else {
                        alertify.alert("No existe empleado asignado a esta estaci&oacute;n, proceda asignar uno.");
                        window.location.replace("../index.php");
                    }
                }
            }
        }
    });
}

function fn_TomaPedido() {
    var send = {
        "ValidaRegreso": 1
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    if (datos[i]['Confirmar'] == 1) {
                        window.location.replace("../ordenpedido/tomaPedido.php");
                    } else {
                        alertify.alert("No existe empleado asignado a esta estaci&oacute;n, proceda asignar uno.");
                        window.location.replace("../index.php");
                    }
                }
            }
        }
    });
}

function fn_refresh() {
    window.location.reload();
}

function validarCuentasAbiertas() {
    var send = {
        "validarCuentasAbiertas": 1
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                var permite_cuantas_abiertas = datos['permite_cuantas_abiertas'];
                if (permite_cuantas_abiertas == 1) {
                    $("#hid_controlMesa").val(1);
                    $("#hid_controlCuenta").val(1);
                }
            }
        }
    });
}

var cargar_url_api_motorizados_gerente = function () {

    var aplicaDomicilio = $("#hid_aplicaDomicilio").val();
    var existeCajeroDomicilio = $("#hid_existeCajeroDomicilio").val();

    if (aplicaDomicilio && existeCajeroDomicilio && aplicaDomicilio == 1 && existeCajeroDomicilio == 1) {

        send = {};
        send.metodo = "cargarUrlApiMotorizadosGerente";
        params_ajax.data = send;
        $.ajax({
            ...params_ajax,
            success: function (response) {
                $("#url_api_motorizados_gerente").val(response.url);
            },
            error: function (a, b, c) {
                cargando(false);
                alert('Error al obtener la dirección de Notificación a Gerente...');
            }
        });

    }

}

var cargando = function (estado) {
    if (estado) {
        $("#mdl_rdn_pdd_crgnd").css("display", "block");
        $("#mdl_pcn_rdn_pdd_crgnd").css("display", "block");
    } else {
        $("#mdl_rdn_pdd_crgnd").css("display", "none");
        $("#mdl_pcn_rdn_pdd_crgnd").css("display", "none");
    }
};

var finalizarTurnoMotorizado = function () {
    console.log("Data: ", data);
    if (data.estado === 'Asignado') {
        alertify.confirm("Estas seguro de finalizar el turno de " + data.motorizado + "?", function (e) {
            if (e) {
                cargando(true);
                send = {};
                send.metodo = "finalizarTurnoMotorizado";
                send.idMotorizado = data.idMotorizado;
                send.idPeriodo = data.idPeriodo;
                params_ajax.data = send;
                $.ajax({
                    ...params_ajax,
                    success: function (response) {
                        console.log("Respuesta: ", response);
                        if (response.estado) {
                            alertify.success(response.mensaje);
                            fn_consultaMotorizados();
                            //$('#modal').modal('hide');

                            //Notificacion a Gerente de Resumen
                            //Motorizados Internos
                            notificacionMotorizadoGerente(data.idPeriodo, data.idMotorizado);

                            //Ingreso en canal de impresion
                            imprimirFinTurnoMotorizado(data.idPeriodo, data.idMotorizado);

                            data.idMotorizado = null;
                            data.motorizado = null;
                            cargando(false);


                        } else {
                            alertify.error(response.mensaje);
                            cargando(false);
                        }
                    },
                    error: function () {
                        cargando(false);
                        alert('Error al cargar motorizados...');
                    }
                });
            }
        });
    } else {
        alertify.error("El motorizado " + data.motorizado + " tiene ordenes ASIGNADAS o EN CAMINO.");
    }
}


var imprimirFinTurnoMotorizado = function (idPeriodo, idMotorizado) {
    send = {};
    send.metodo = "imprimirFinTurnoMotorizado";
    send.idPeriodo = idPeriodo;
    send.idMotorizado = idMotorizado;
    params_ajax.data = send;


    var apiImpresion = getConfiguracionesApiImpresion();

    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
        fn_cargando(1);
        var result = new apiServicioImpresion('desmontarMotorizado', null, null, send);
        var imprime = result["imprime"];
        var mensaje = result["mensaje"];

        if (!imprime) {
            alertify.success('Imprimiendo desmontado de Motorizado...');
            fn_cargando(0);


        } else {
            alertify.alert(mensaje);

            alertify.success('Error al imprimir desmontado de Motorizado...');
            fn_cargando(0);

        }

    } else {

        $.ajax({
            ...params_ajax,
            success: function (response) {
                console.log("Respuesta IMPRESION MOTORIZADO: ", response);
                if (response) {

                } else {
                    alertify.error(response.mensaje);
                    cargando(false);
                }
            },
            error: function (a, b, c) {
                cargando(false);
                console.log('Error al imprimir el turno del motorizado');
                console.log(a);
                console.log(b);
                console.log(c);

            }
        });
    }

}


var notificacionMotorizadoGerente = function (idPeriodo, idMotorizado) {
    send = {};
    send.metodo = "notificacionMotorizadoGerente";
    send.idPeriodo = idPeriodo;
    send.idMotorizado = idMotorizado;
    send.url = $("#url_api_motorizados_gerente").val();
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
        success: function (response) {
            console.log("Respuesta GERENTE: ", response);
            if (response) {

            } else {
                alertify.error(response.mensaje);
                cargando(false);
            }
        },
        error: function () {
            cargando(false);

            alert('Error al cargar motorizados...');
        }
    });

}


var fn_consultaDesasignacionMotorizado = function (idMotorizado, estado, motorizado) {
    // Iniciar datos
    data.idMotorizado = idMotorizado;
    data.estado = estado;
    data.motorizado = motorizado;
    data.idPeriodo = $('#IDPeriodo').val();
    finalizarTurnoMotorizado();

}
const fn_pedidoMotorizado = (cod_cabeceraApp, e, codigo_app) => {

    cambiarPedidoMotorolo.idPedido = cod_cabeceraApp;
    cambiarPedidoMotorolo.codigo_app = codigo_app;
    $(e).addClass("optionSelected");
    $(e).siblings().removeClass("optionSelected");

};
const fn_dataConsultaMotorizados = (idMotorizado, estado, motorizado) => {
    let htmlComandas = "",
        htmlMotorizados = "",
        htmlMotorizadosAgregarComadas = "";
    console.log(typeof motorizado)
    var send = {
        "comandasMotorizados": 1,
        "data": IDMotorizados
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#comandas table tbody").empty();
                $("#motorizados table tbody").empty();
                $("#agregarComandas table tbody").empty();
                for (i = 0; i < datos.str; i++) {
                    htmlComandas = `<tr onclick="fn_consultaPedidosMotorizado('${datos[i]["IDMotorolo"]}',this,'${datos[i]["empresaMotorolo"]}', '${datos[i]["nombres"]}', '${datos[i]["apellidos"]}')">
                    <td>${datos[i]["empresaMotorolo"]} - ${datos[i]["nombres"]} ${datos[i]["apellidos"]} </td>
                    <td>${datos[i]["numeroComandas"]}</td>
                    </tr>`;
                    htmlMotorizados = `<tr onclick="fn_cambiarPedidoMotorizado('${datos[i]["IDMotorolo"]}',this,'${datos[i]["empresaMotorolo"]}', '${datos[i]["nombres"]}', '${datos[i]["apellidos"]}','${idMotorizado}','${estado}','${motorizado}')">
                    <td>${datos[i]["empresaMotorolo"]} - ${datos[i]["nombres"]} ${datos[i]["apellidos"]} </td>
                    </tr>`;
                    if (datos[i]["tipoMotorolo"] !== "AGREGADOR") {
                        htmlMotorizadosAgregarComadas = `<tr>
                        <td>${datos[i]["empresaMotorolo"]} - ${datos[i]["nombres"]} ${datos[i]["apellidos"]} </td>
                        <td>${datos[i]["numeroComandas"]}</td>
                        <td><input IDMotorolo='${datos[i]["IDMotorolo"]}' class="form-control extrasMotorolos" type="number" min="0"></td>
                        </tr>`;
                        $("#agregarComandas table tbody").append(htmlMotorizadosAgregarComadas);
                    }
                    $("#comandas table tbody").append(htmlComandas);
                    $("#motorizados table tbody").append(htmlMotorizados);
                }
            }
        }
    });

};

function fn_consultaPedidosMotorizado(idMotorizado, e, empresa, nombreMotorizado, apellidosMotorizado) {
    $(e).addClass("optionSelected");
    let html = "";
    cambiarPedidoMotorolo = {};
    cambiarPedidoMotorolo.idMotorizado = idMotorizado;
    cambiarPedidoMotorolo.empresa = empresa;
    cambiarPedidoMotorolo.nombreCompletoMotorizado = nombreMotorizado + ' - ' + apellidosMotorizado;
    var send = {
        "pedidosMotorizado": 1,
        "data": idMotorizado
    };
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {

            if (datos.str > 0) {
                $("#pedidos table tbody").empty();
                for (i = 0; i < datos.str; i++) {
                    html = `<tr onclick="fn_pedidoMotorizado('${datos[i]["cod_cabeceraApp"]}',this,'${datos[i]["codigo_app"]}')">
                    <td>${datos[i]["codigo_app"]}</td>
                    <td>${datos[i]["medio"]}</td>
                    <td>${datos[i]["total_Factura"]}</td>
                    </tr>`;
                    $("#pedidos table tbody").append(html);
                }
                //$('#pedidos').css( "height", "220px" ).shortscroll();
            }
            console.log(cambiarPedidoMotorolo);
        }
    });
    $(e).siblings().removeClass("optionSelected");
}

function fn_consultarComandasMotorizados(idMotorizado, estado, motorizado) {

    if (revisionComandas === false) {
        modal.style.display = "block";
        $(".jb-shortscroll-wrapper").css("display", "none");
        fn_dataConsultaMotorizados(idMotorizado, estado, motorizado);

    } else {
        fn_consultaDesasignacionMotorizado(idMotorizado, estado, motorizado);
    }
}

function fn_cambiarPedidoMotorizado(idMotorizado, e, empresa, nombreMotorizado, apellidosMotorizado, idMotorolo, estado, motorizado) {

    $(e).addClass("optionSelected");
    $(e).siblings().removeClass("optionSelected");

    if (cambiarPedidoMotorolo.hasOwnProperty("idPedido") && cambiarPedidoMotorolo.hasOwnProperty("idMotorizado")) {
        cambiarPedidoMotorolo.idMotorizadoPedido = idMotorizado;
        cambiarPedidoMotorolo.empresa2 = empresa;
        cambiarPedidoMotorolo.nombreCompletoMotorizado2 = nombreMotorizado + ' - ' + apellidosMotorizado;
        let send = {
            "actualizarComandasMotorizados": 1,
            "data": cambiarPedidoMotorolo
        };
        alertify.confirm(`Estas seguro de transferir el pedido con el codigo ${cambiarPedidoMotorolo.codigo_app} de ${cambiarPedidoMotorolo.empresa} - ${cambiarPedidoMotorolo.nombreCompletoMotorizado} a ${cambiarPedidoMotorolo.empresa2} - ${cambiarPedidoMotorolo.nombreCompletoMotorizado2}`, function (e) {
            if (e) {
                $.ajax({
                    async: false,
                    type: "POST",
                    dataType: "json",
                    contentType: "application/x-www-form-urlencoded",
                    url: "config_corteCaja.php",
                    data: send,
                    success: function (datos) {
                        if (datos) {
                            cambiarPedidoMotorolo = {};
                            $(".optionSelected").each(function () {
                                $(this).removeClass("optionSelected");
                            });
                            revisionComandas = true;
                            alertify.success("Pedido transferido con exito!!")
                            fn_dataConsultaMotorizados(idMotorolo, estado, motorizado);
                            $("#pedidos table tbody").empty();
                        }
                    },
                    error: function (e) {
                        console.log(e);
                    }
                });
            }
        });


    } else {
        alertify.error("Debe seleccionar el motorizado y pedido a transferir")
        $(e).removeClass("optionSelected");
    }

}

const fn_guardarExtrasMotorizados = () => {
    let data = [];
    $(".extrasMotorolos").each(function () {
        data.push([$(this).attr("IDMotorolo"), $(this).val()])
    });
    let send = {
        "añadirComandasMotorizados": 1,
        "data": data
    };
    alertify.confirm(`Est&aacute; seguro de añadir comandas extras a los motorizados?`, function (e) {
        if (e) {
            $.ajax({
                async: false,
                type: "POST",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_corteCaja.php",
                data: send,
                success: function (datos) {
                    if (datos) {
                        console.log(datos);
                        alertify.success("comandas añadidas con exito!!")
                    }
                },
                error: function (e) {
                    console.log(e);
                }
            });
        }
    });
}

//función que anula la orden, elimina la cabecera y detalle orden pedido
function fn_anularMesa() {
    var id_odp = $("#hid_codigoorden").val(); 
var cuenta = $("#hid_cuentaOdp").val();
    var send = {
        "anularMesaOdp": 1
    };
    send.codigoOrden = id_odp;
send.cuenta = cuenta;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {                    
                    var respuesta = datos[i]['resp'];
                    var mensaje = datos[i]['mensaje'];
                    if (respuesta == '1') {
                        alertify.success(mensaje);
                        $("#content").dialog("close");
                        fn_cargaMesas();
                        fn_cuentaAbierta();                        
                       // window.location.reload();
                    }
                    
                }
            }
        },
        error: function (e) {
            console.log(e);
        }
    });
}

//función que anula la cuenta por facturar, actualiza a cerrada la factura y orden de pedido
function fn_anularCuentaPorFacturar() {
    var id_odp = $("#hid_idOdp").val();
    var id_factura = $("#hid_codigofactura").val();
var cuenta = $("#hid_cuentaOdp").val();
    var send = {
        "anularCuentaPorFacturar": 1
    };
    send.codigoOrden = id_odp;
    send.codigoFactura = id_factura;
send.cuenta = cuenta;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_corteCaja.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {                    
                    var respuesta = datos[i]['resp'];
                    var mensaje = datos[i]['mensaje'];
                    if (respuesta == '1') {
                        alertify.success(mensaje);
                        $("#content2").dialog("close");
                        fn_cargaMesas();
                        fn_cuentaAbierta();                        
                       // window.location.reload();
                    }
                    
                }
            }
        },
        error: function (e) {
            console.log(e);
        }
    });
}

function fn_actualizaEstacionOrdenPedidoFastFood(codigoOrdenPedido) {
    var send = {
        "actualizaEstacionOdp": 1
    };
    send.accion = '4';
    send.codigoOrden = codigoOrdenPedido;
    $.getJSON("config_corteCaja.php", send, function (datos) {
    });
}

//retomar cuenta abierta cuando es separacion de cuentas
function condicionFacturacionOrdenPedido( odp_id )
{
    $.ajax(
        {
            async: false, 
            type: "POST", 
            dataType: "json", 
            contentType: "application/x-www-form-urlencoded", 
            url: "config_corteCaja.php", 
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

                        var condicion = msg.condicionFOP.condicion;
                       
                        if ( condicion != 0 )
                        {
                            if ( condicion != 1 && condicion != 2 && condicion != 6 && condicion != 9 )
                            {
                                procesoFacturacion();
                            } else{
                                if (estado_factura == 1){ //anula orden de pedido
                                    fn_anularMesa();
                                }else{ //anula factura
                                    fn_anularCuentaPorFacturar();
                                }                                
                            }
                        }
                        else
                        {
                            alertify.error( "El identificador de la Orden de Pedido es inválido." );
                        }
                    }
                    else
                    {
                        console.log( "Ha ocurrido algo inesperado al intentar estudiar la condición en la que se encuentra la facturación de la orden de pedido \"" + odp_id + "\", por lo que la operación CFOP no ha tenido éxito. Error." );

                        //alertify.error( "Ha ocurrido algo inesperado al intentar estudiar la condición en la que se encuentra la facturación de la orden de pedido actual." );
                    }
                },
            error:
                function ( jqXHR, textStatus, errorThrown )
                {
                    console.log( jqXHR ); console.log( textStatus ); console.log( errorThrown );
                    
                    console.log( "Ha ocurrido algo inesperado al intentar estudiar la condición en la que se encuentra la facturación de la orden de pedido \"" + odp_id + "\", por lo que la operación CFOP no ha tenido éxito. Error." );

                    //alertify.error( "Ha ocurrido algo inesperado al intentar estudiar la condición en la que se encuentra la facturación de la orden de pedido actual." );
                }
        }
    );
}

function procesoFacturacion()
{
    var mensaje = "<p>Ya se ha iniciado un proceso de facturación para la orden de pedido, presione el botón <strong style=\"font-size: 20px; color: red;\">RETOMAR</strong> para dar continuidad.</p>"

    alertify.alert( mensaje, 
        function()
        { 
            document.getElementById("btn_opciones").click();
            document.getElementById("cobrar").click();
            //alertify.success('');
        } 
    );
}

async function fn_ejecutarCambioIva(){
    let send = {"ejecutarCambioIva": 1};
    $.ajax({
        data: send,
        url: "config_corteCaja.php",
        type: 'POST',
        success: function(data){
           console.log(data)
        },
        error: function(jqXHR, exception) {
            alertify.error("error "+jqXHR.responseText);
        }
    });
}