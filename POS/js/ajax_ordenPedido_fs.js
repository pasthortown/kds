var preguntas_sug = new Array();
var fechaservidor = '';
var h = '';
var m = '';
var Accion = 0;
var lc_cantidad = 1;
var x = "0";
var xi = 1;
var coma = 0;
var ni = 0;
var op = "no";
var lc_control = -1;
var plu_lector = 0;
var plu_mag_id = 0;
var plu_gramo = 0;
var rst_categoria = 0;
var rst_tiempopedido = 0;
var VALIDADOR = 0;
var TOTALCUENTA = 0;
var palabraDefault = "";
$(document).ready(function () {
    var solicitarInformacion = 0;
    fn_cargando(1);
    document.onkeydown = teclado;
    $('.jb-shortscroll-wrapper').show();
    $("#cantidadOK").val(1);
    $("#hid_bandera_gramo").val('');
    $('#listaPedidoTomaPedido').shortscroll();
    $('#listaPedidoTomaPedido1').shortscroll();
    $('#cnt_rdn_pdd_pls').shortscroll();
    $('#cnt_rdn_pdd_ctgrs').shortscroll();
    $("#formCobrar").hide();
    $("#contenedorComentario").hide();
    $("#modalCuponSistemaGerente").hide();
    $("#anulacionesContenedor").hide();
    $("#tecladoCodigos").hide();
    $("#buscarPlu").show();
    $("#cantidad").val('');
    $("#etiqueta_cantidad").val('x1');
    $("#txt_busca").val('');
    $(".hide_impuesto").hide();
    $("#aumentarContador").hide();

    var rst_id = $('#hide_rst_id').val();
    // if($("#hide_mesa_id").val()!=0)
    //{
    //ConfiguracionesEstablecimiento
    send = {"configuracionOrdenPedido": 1};
    send.mesa_id = $("#hide_mesa_id").val();
    send.num_Pers = $("#hide_num_Pers").val();
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            palabraDefault = datos[0]['palabraDefault'];
            solicitarInformacion = datos[0]['solicitarInicio'];
            fechaservidor = new Date(datos[0]['fecha']);
            $("#nfrmcn_srs_sstm_hora").html(datos[0]['observacion']);
            $("#nfrmcn_srs_sstm_periodo").html(datos[0]['fecha_periodo']);
            var existe = datos[0]['existe'];
            var tipo_impuesto = datos[0]['tipo_impuesto'];
            var tipo_cantidad = datos[0]['tipo_cantidad'];
            var odp_id = datos[0]['odp_id'];
            rst_categoria = datos[0]['cat_id'];
            rst_tiempopedido = datos[0]['rst_tiempopedido'];
            $("#hide_cdn_tipoimpuesto").val(tipo_impuesto);
            $("#hide_odp_id").val(odp_id);
            if (tipo_impuesto == 'Diferenciado') {
                $(".hide_impuesto").show();
            }
            if (tipo_cantidad == 0 || tipo_cantidad == null) {
                $("#btn_punto").prop('disabled', true);
                $("#btn_punto").hide();
            } else {
                $("#btn_punto").prop('disabled', false);
                $("#btn_punto").show();
            }
            if (existe == 1) {
                if ($("#txt_bloqueado").val() != 0) {
                    $("#btn_eliminarElemnto").attr("Disabled", false);
                    $("#btn_eliminarElemnto").removeClass("boton_Accion_Bloqueado");
                    $("#btn_eliminarElemnto").removeClass("boton_accion_disabled");
                    $("#btn_eliminarElemnto").removeClass("eliminar_bloqueado");
                    $("#btn_eliminarElemnto").addClass("boton_Accion");
                    $("#btn_eliminarElemnto").addClass("eliminar_activo");
                    $("#btn_cuponesSG").removeClass("boton_Opcion");
                    $("#btn_cuponesSG").addClass("boton_Opcion_Bloqueado");
                    $("#btn_cuponesSG").attr("Disabled", true);
                } else {
                    fn_cargarAccesosSistema();
                }
                $("#btn_salonLlevar").show();
                $("#btnDesmesa").text(datos[0]["nombreMesa"]);
                fn_listaPendiente();
            } else {
                $("#btn_salonLlevar").hide();
                alertify.alert('No existen productos configurados para esta estaci&oacute;n');
            }
            if (solicitarInformacion > 0) {
                if ($('li.focus').length < 1) {
                    $('#plu_comentar').html("Comentar Orden");
                    $("#contenedorComentario").dialog({
                        modal: true,
                        autoOpen: false,
                        position: {
                            my: 'top',
                            at: 'top+120'
                        },
                        show: {
                            effect: "none",
                            duration: 500
                        },
                        hide: {
                            effect: "none",
                            duration: 500
                        },
                        width: 500,
                        height: 340,
                        open: function (event, ui) {
                            $(".ui-dialog-titlebar").hide();
                            $("#comentario").val(palabraDefault);
                            fn_alfaNumerico('#comentario');
                            $('#btn_cancelar_teclado').attr('onclick', 'fn_cerrarModalComentar()');
                            $('#btn_ok_teclado').attr('onclick', 'fn_agregarComentarioOrden()');
                        }
                    });
                    $("#contenedorComentario").dialog("open");
                }
            }
        }
    });

    cargarMenuEstacionDinamico();

    fn_popupCantidad();

    $("#txt_lectorBarras").val('');
    fn_focusLector();
    $('#txt_lectorBarras').change(function () {
        fn_lectorBarras();
    });

    //$('#cuadro_buscador').css('display', 'none');

    //Modal Preguntas Sugeridas
    $('#mdl_prgnts_sgrds').css('display', 'none');
    $('#mdl_pcns_prgnts_sgrds').css('display', 'none');
    $('#body_prgnts_sgrds').shortscroll();

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

    $("#btn_cantidad_cero").prop('disabled', false);

    fn_cargando(0);

});

function fn_imprimirCuponCanjeado(cupon, mensaje, estado) {
    var odp_id = $("#hide_odp_id").val();
    var est_ipd = $("#hide_est_ip").val();
    var usr_id = $("#hide_usr_id").val();
    var rst_id = $("#hide_rst_id").val();
    send = {"impresionDetalleCuponOrdenPedido": 1};
    send.cupon = cupon;
    send.mensaje = mensaje;
    send.estado = estado;
    $.ajax({type: "GET", async: false, url: "config_ordenPedido.php", data: send, dataType: "json", success: function (datos) {}});
}

function fn_procesoCanjearAutomatico() {
    $('#pcn_tp_cnj_cpn_1').attr('checked', false);
    $('#pcn_tp_cnj_cpn_0').attr('checked', true);
    $("#keyboard").hide();
    $("#keyboard").empty();
    $('#aut_frm_cnj_cpn').css('display', 'block');
    $('#man_frm_cnj_cpn').css('display', 'none');
    $('#input_cuponSistemaGerenteAut').focus();
}

function fn_validarCuponManual() {
    //Validar campo 1
    var valor = $('#input_cuponSistemaGerenteMan1').val();
    if (valor.length > 0 && valor.length < 8) {
        $('#input_cuponSistemaGerenteMan1').removeClass('alertaInput');
        //Validar campo 2
        valor = $('#input_cuponSistemaGerenteMan2').val();
        if (valor.length > 0 && valor.length < 8) {
            $('#input_cuponSistemaGerenteMan2').removeClass('alertaInput');
            //Validar campo 3
            valor = $('#input_cuponSistemaGerenteMan3').val();
            if (valor.length > 0 && valor.length < 8) {
                $('#input_cuponSistemaGerenteMan3').removeClass('alertaInput');
                //Validar campo 4
                valor = $('#input_cuponSistemaGerenteMan4').val();
                if (valor.length > 0 && valor.length < 8) {
                    $('#input_cuponSistemaGerenteMan4').removeClass('alertaInput');
                    fn_canjearCuponManual();
                } else {
                    fn_agregarAlertaInput('#input_cuponSistemaGerenteMan4');
                    fn_activarCasilla('#input_cuponSistemaGerenteMan4');
                    if (valor.length == 0) {
                        alertify.alert("Todos los campos son obligatorios.");
                    } else {
                        alertify.alert("Excede en n�mero de caracteres permitidos.");
                    }
                }

            } else {
                fn_agregarAlertaInput('#input_cuponSistemaGerenteMan3');
                fn_activarCasilla('#input_cuponSistemaGerenteMan3');
                if (valor.length == 0) {
                    alertify.alert("Todos los campos son obligatorios.");
                } else {
                    alertify.alert("Excede en n�mero de caracteres permitidos.");
                }
            }

        } else {
            fn_agregarAlertaInput('#input_cuponSistemaGerenteMan2');
            fn_activarCasilla('#input_cuponSistemaGerenteMan2');
            if (valor.length == 0) {
                alertify.alert("Todos los campos son obligatorios.");
            } else {
                alertify.alert("Excede en n�mero de caracteres permitidos.");
            }
        }

    } else {
        fn_agregarAlertaInput('#input_cuponSistemaGerenteMan1');
        fn_activarCasilla('#input_cuponSistemaGerenteMan1');
        if (valor.length == 0) {
            alertify.alert("Todos los campos son obligatorios.");
        } else {
            alertify.alert("Excede en n�mero de caracteres permitidos.");
        }
    }
}

function fn_agregarAlertaInput(id) {
    $(id).addClass('alertaInput');
    $(id).val("");
    $(id).focus();
}

function fn_activarCasilla(id) {
    $("#keyboard").hide();
    $("#keyboard").empty();
    fn_alfaNumerico(id);
    $('#btn_ok_teclado').attr('onclick', 'fn_validarCuponManual()');
    $('#btn_cancelar_teclado').attr('onclick', 'fn_cerrarModalCuponesSistemaGerente()');
}

function fn_canjearCuponManual() {
    alert();
    fn_cargando(1);
    var cdn_id = $('#hide_cdn_id').val();
    var tipo = 0;
    var incremental = $('#input_cuponSistemaGerenteMan1').val();
    var solicitud = $('#input_cuponSistemaGerenteMan2').val();
    var cupon = $('#input_cuponSistemaGerenteMan3').val();
    var rst_id = $('#hide_rst_id').val();
    var usr_id = 1;
    var text_for = $('#input_cuponSistemaGerenteMan4').val();
    send = {};
    send.metodo = "canjearCuponesManual";
    send.cdn_id = cdn_id;
    send.tipo = tipo;
    send.incremental = incremental;
    send.solicitud = solicitud;
    send.cupon = cupon;
    send.rst_id = rst_id;
    send.usr_id = usr_id;
    send.text_for = text_for;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "cliente_canjearcupones.php", data: send,
        success: function (datos) {
            if (datos.retorno > 0) {
                fn_cargando(0);
                alertify.alert("" + datos.respuesta);
                fn_cerrarModalCuponesSistemaGerente();
                $("#input_cuponSistemaGerenteAut").prop("disabled", false);
            } else {
                if (datos.retorno > -1) {
                    fn_cargando(0);
                    alertify.confirm(datos.respuesta + " &iquest;Desea probar otra vez?", function (e) {
                        if (e) {
                            fn_procesoManual();
                        } else {
                            $("#input_cuponSistemaGerenteAut").prop("disabled", false);
                            fn_cerrarModalCuponesSistemaGerente();
                        }
                    });
                } else {
                    fn_cargando(0);
                    alertify.confirm("Error! &iquest;Desea probar otra vez?", function (e) {
                        if (e) {
                            fn_procesoManual();
                        } else {
                            $("#input_cuponSistemaGerenteAut").prop("disabled", false);
                            fn_cerrarModalCuponesSistemaGerente();
                        }
                    });
                }
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#input_cuponSistemaGerenteAut").prop("disabled", false);
            $('#input_cuponSistemaGerenteAut').focus();
            fn_cargando(0);
            alertify.alert("Servicio no Disponible");
        }
    });
}

function fn_procesoManual() {
    $('#input_cuponSistemaGerenteMan1').val("");
    $('#input_cuponSistemaGerenteMan1').removeClass('alertaInput');
    $('#input_cuponSistemaGerenteMan2').val("");
    $('#input_cuponSistemaGerenteMan2').removeClass('alertaInput');
    $('#input_cuponSistemaGerenteMan3').val("");
    $('#input_cuponSistemaGerenteMan3').removeClass('alertaInput');
    $('#input_cuponSistemaGerenteMan4').val("");
    $('#input_cuponSistemaGerenteMan4').removeClass('alertaInput');
    fn_activarCasilla('#input_cuponSistemaGerenteMan1');
    $('#input_cuponSistemaGerenteMan1').focus();
}

function fn_procesoCanjearManual() {
    if (!$('#pcn_tp_cnj_cpn_1').is(":checked")) {
        $('#pcn_tp_cnj_cpn_0').attr('checked', false);
        $('#pcn_tp_cnj_cpn_1').attr('checked', true);
        $('#man_frm_cnj_cpn').css('display', 'block');
        $('#aut_frm_cnj_cpn').css('display', 'none');
        fn_procesoManual();
    }
}

function fn_canjearCuponAutomatico() {
    fn_cargando(1);
    $("#input_cuponSistemaGerenteAut").prop("disabled", true);
    var cdgcpn = $('#input_cuponSistemaGerenteAut').val();
    send = {};
    send.metodo = "canjearCuponesAutomatico";
    send.cupon = cdgcpn;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "cliente_canjearcupones.php", data: send,
        success: function (datos) {
            if (datos.retorno > 0) {
                fn_cargando(0);
                alertify.alert("" + datos.respuesta);
                fn_cerrarModalCuponesSistemaGerente();
                $("#input_cuponSistemaGerenteAut").prop("disabled", false);
            } else {
                if (datos.retorno > -1) {
                    fn_cargando(0);
                    alertify.confirm(datos.mensaje + " &iquest;Desea probar otra vez?", function (e) {
                        if (e) {
                            $("#input_cuponSistemaGerenteAut").prop("disabled", false);
                            $('#input_cuponSistemaGerenteAut').val("");
                            $('#input_cuponSistemaGerenteAut').focus();
                        } else {
                            $("#input_cuponSistemaGerenteAut").prop("disabled", false);
                            fn_cerrarModalCuponesSistemaGerente();
                        }
                    });
                } else {
                    fn_cargando(0);
                    alertify.confirm("Error! &iquest;Desea probar otra vez?", function (e) {
                        if (e) {
                            $("#input_cuponSistemaGerenteAut").prop("disabled", false);
                            $('#input_cuponSistemaGerenteAut').val("");
                            $('#input_cuponSistemaGerenteAut').focus();
                        } else {
                            $("#input_cuponSistemaGerenteAut").prop("disabled", false);
                            fn_cerrarModalCuponesSistemaGerente();
                        }
                    });
                }
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#input_cuponSistemaGerenteAut").prop("disabled", false);
            $('#input_cuponSistemaGerenteAut').val("");
            $('#input_cuponSistemaGerenteAut').focus();
            fn_cargando(0);
            alertify.alert("Servicio no Disponible");
        }
    });
}

    function fn_modalCupones() {
        fn_procesoCanjearAutomatico();
        $("#modalCuponSistemaGerente").show();
        $("#modalCuponSistemaGerente").dialog({
            modal: true,
            position: {
                my: 'top',
                at: 'top+220'
            },
            width: 500,
            heigth: 500,
            resize: false,
            opacity: 0,
            show: "none",
            hide: "none",
            duration: 500,
            open: function (event, ui) {
                $('.ui-dialog-titlebar').hide();
                $('#input_cuponSistemaGerenteAut').val('');
            }
        });
    }

function fn_validarCuponSistemaGerente() {
    var cdgcpn = $('#input_cuponSistemaGerente').val();
}

function fn_cerrarModalCuponesSistemaGerente() {
    $("#keyboard").hide();
    $("#keyboard").empty();
    $("#modalCuponSistemaGerente").hide();
    $("#modalCuponSistemaGerente").dialog("close");
}

function cargarMenuEstacionDinamico() {
    send = {"cargarMenuEstacionDinamico": 1};
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                if (i < datos.str - 1) {
                    $("#cntdr_mn_dnmc_stcn").append('<input type="button" id="btn_mn_' + datos[i]['menu_id'] + '_' + datos[i]['cla_id'] + '" class="btn_mn_dnmc_stcn" value="' + datos[i]['Descripcion'] + '" onclick="fn_cargarCategoriaMenu(this, \'' + datos[i + 1]['menu_id'] + '\', \'' + datos[i + 1]['cla_id'] + '\');" style="display: none;"/>');
                } else {
                    $("#cntdr_mn_dnmc_stcn").append('<input type="button" id="btn_mn_' + datos[i]['menu_id'] + '_' + datos[i]['cla_id'] + '" class="btn_mn_dnmc_stcn" value="' + datos[i]['Descripcion'] + '" onclick="fn_cargarCategoriaMenu(this, \'' + datos[0]['menu_id'] + '\', \'' + datos[0]['cla_id'] + '\');" style="display: none;"/>');
                }
                if (i == 0) {
                    $('#hid_cla_id').val(datos[i]['cla_id']);
                    $('#hide_menu_id').val(datos[i]['menu_id']);
                    $("#btn_mn_" + datos[i]['menu_id'] + "_" + datos[i]['cla_id']).css('display', 'block');
                    fn_cargarMenuCategoriaEstacion(datos[i]['menu_id'], datos[i]['cla_id']);
                }
            }
        }
    });
}

function fn_cargarCategoriaMenu(anterior, siguiente, cla_id) {
    $(anterior).css('display', 'none');
    $("#btn_mn_" + siguiente + "_" + cla_id).css('display', 'block');
    $('#hide_menu_id').val(siguiente);
    $('#hid_cla_id').val(cla_id);
    fn_cargarMenuCategoriaEstacion(siguiente, cla_id);
}

function fn_validarCredencialesAdministrador() {
    var usr_clave = $("#usr_clave").val();
    var cfac_id = $('#listadoPedido').find("li.focus").attr("id");
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
        send = {"validarUsuario": 1};
        send.usr_clave = usr_clave;
        send.usr_tarjeta = usr_tarjeta;
        $.getJSON("config_ordenPedido.php", send, function (datos) {
            if (datos.str > 0) {
                $("#anulacionesContenedor").dialog("close");
                $("#usr_clave").val("");
                window.location.href = "../index.php";
            } else {
                fn_numerico("#usr_clave");
                alertify.confirm("La clave ingresada no tiene permisos de Administrador.", function (e) {
                    if (e) {
                        alertify.set({buttonFocus: "none"});
                        $("#usr_clave").focus();
                    }
                });
                $("#usr_clave").val("");
            }
        });
    } else {
        fn_numerico("#usr_clave");
        alertify.confirm("Ingrese clave de Administrador", function (e) {
            if (e) {
                alertify.set({buttonFocus: "none"});
                $("#usr_clave").focus();
            }
        });
        $("#usr_clave").val("");
    }
}

function fn_validarUsuarioAdministrador() {
    $("#anulacionesContenedor").show();
    $("#anulacionesContenedor").dialog({
        modal: true,
        width: 500,
        heigth: 500,
        resize: false,
        opacity: 0,
        show: "none",
        hide: "none",
        duration: 500,
        open: function (event, ui) {
            $('.ui-dialog-titlebar').hide();
            fn_numerico('#usr_clave');
            $('#usr_clave').val('');
            $('#usr_clave').attr('onchange', 'fn_validarCredencialesAdministrador()');
        }
    });
}

function fn_salirSistema() {
    send = {"cargarConfiguracionRestaurante": 1};
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            if (datos[0]['tpsrv_descripcion'] == 'FAST FOOD') {
                if ($('#listadoPedido li').length > 0) {
                    alertify.error('Estimado usuario, usted no puede salir del sistema.');
                } else {
                    window.location.replace("../index.php");
                }
            } else {
                if ($('#listadoPedido li').length < 1) {
                    fn_validarUsuarioAdministrador();
                } else {
                    alertify.error("Tiene detalle en la orden, no puede salir del sistema.");
                }
            }
        }
    });
}

function fn_resumenVentas() {
    window.location.replace("../resumen/resumenVentas.php");
}

function fn_obtenerMesa() {
    send = {"obtenerMesa": 1};
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            var mesa = datos[0]['mesa_asignada'];
            $("body").html("<form action='../ordenpedido/tomaPedido.php' name='asignarMesa' method='get' style='display:none;'><input type='text' name='numMesa' value='" + mesa + "' /></form>");
            document.forms['asignarMesa'].submit();
        } else {
            alertify.alert('No existen mesas disponibles para asignar');
        }
    });
}

function fn_focusLector() {
    $("#txt_lectorBarras").focus();
}

function fn_lectorBarras() {
    $("#btn_cambio").val("gramos");
    var codigo_barras = $("#txt_lectorBarras").val();
    var num_plu = codigo_barras;
    num_plu = num_plu.substring(2, 6);
    send = {"lectorBarras": 1};
    send.rst_id = $('#hide_rst_id').val();
    send.cat_id = $('#hide_est_id').val();
    send.cat_id = rst_categoria;
    send.num_plu = num_plu;
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            var plu_id = datos[0]['plu_id'];
            var magp_id = datos[0]['magp_id'];
            var plu_gramos = datos[0]['plu_gramo'];
            plu_lector = plu_id;
            plu_mag_id = magp_id;
            plu_gramo = plu_gramos;
            //Pantalla modal Cantidad
            if (Accion != 2) {
                Accion = 1;
                $("#aumentarContador").dialog("open");
                $("#btn_punto").hide();
                $("#cantidad").val("");
                $("#etiqueta_cantidad").val('x1');
                $("#txt_lectorBarras").val("");
                fn_focusLector();
            } else {
                $("#cantidadOK").val($("#cantidad").val());
                fn_verificarGramosPluParametro(magp_id, plu_id, plu_gramos);
                $("#txt_lectorBarras").val('');
            }
        } else {
            alertify.alert("No existe el producto escaneado");
            $("#txt_lectorBarras").val('');
            fn_focusLector();
        }
    });
}

function fn_ipEstacion() {
    send = {"ipEstacion": 1};
    send.est_id = $("#hide_est_id").val();
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            $("#hide_est_ip").val(datos[0]['est_ip']);
        } else {
            alertify.alert('No existe una IP asociada a la estaci&oacute;n');
        }
    });
    fn_focusLector();
}

/*------------------------------------------
 funcion de inicio para restaurante que si tienen opcion para llevar
 ---------------------------------------------*/
function fn_cargarMenuCategoriaEstacion(menu_id, cla_id)
{
    var html = "";
    $("#barraCategoria").html(html);
    $("#barraProducto").html(html);
    $("agregarCantidad").hide();
    send = {"cargarMenuCategoria": 1};
    send.menu_id = menu_id;
    send.cla_id = cla_id;
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            $("#barraCategoria").empty();
            fn_activarProductos(datos[0]['mag_id']);
            for (i = 0; i < datos.str; i++) {
                var posicionTotal = "" + datos[i]['mag_orden'] + "";
                var separar = posicionTotal.split(",");
                left = separar[0];
                arriba = separar[1];
                html += "<button id='btn_c" + datos[i]['mag_id'] + "' onclick='fn_activarProductos(\"" + datos[i]['mag_id'] + "\")' style='background-color:" + datos[i]['mag_color'] + "; position:absolute; color:" + datos[i]['mag_colortexto'] + "; left:" + left + "px; top:" + arriba + "px;'>" + datos[i]['mag_descripcion'] + "</button>";
                $("#codigoCategoria").val(datos[i]['mag_id']);
            }
            $("#barraCategoria").html(html);
        } else {
            alertify.alert('No existen productos configurados para esta estaci&oacute;n');
        }
    });
    fn_focusLector();
}

function concat_split(content, mesa, numSplit) {
    var head = '<div  class="content_split" >' +
            '<div class="header_split" onclick="fn_recuperar_cuenta_split(' + numSplit + ')">' +
            "<label>Mesa " + mesa + "  Split " + numSplit + "</label>\n" +
            "</div>\n" +
            "<div id=\"rdn_pdd_brr_nfmcn\" style=\" height: 90%;\" class=\"rdn_pdd_brr_nfmcn\">\n" +
            "<div id=\"rdn_pdd_sprdr\" class=\"rdn_pdd_sprdr\"></div>\n" +
            "<div>\n" +
            "<div style=\"width:100%\" class=\"listado\" id=\"listado" + numSplit + "\">\n" +
            "<ul class=\"\listadoPedido\" id=\"listadoPedido" + numSplit + "\">";


    var footer =
            "</ul>  </div>\n" +
            "</div>\n" +
            "</div>\n" +
            "</div>      ";
    return (head + content + footer);
}



//recuperar cuentas separadas
function  fn_recuperar_cuenta_split(numSplit) {
    $.urlParam = function(name){
	var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
	return results[1] || 0;
    };
    
    mesa_id=$.urlParam("numMesa");
    //alert(mesa_id);
    window.location.href = "tomaPedido.php?numMesa=" + mesa_id+"&numSplit="+numSplit;
    
}


function existe(lista, dato) {
    for (var i = 0; i < lista.length; i++) {
        if (lista[i] === dato) {
            return true;
        }
    }
    return false;
}
function fn_listaPendiente() {
    var html = "";
    var htmlTotal = "";
    alert($("#hide_numSplit").val());
    if($("#hide_numSplit").val()  === "undefined"){
         send = {"cargar_ordenPedidoPendiente": 1,
         "numSplit":1};
    }else{
        send = {"cargar_ordenPedidoPendiente": 1,
        "numSplit":$("#hide_numSplit").val()};
    }
    
    
    
    send.odp_id = document.getElementById("hide_odp_id").value;
    send.cat_id = rst_categoria;
    TOTALCUENTA = 0;
    VALIDADOR = 0;
    $.ajax({async: false, url: "config_ordenPedido.php", data: send, dataType: "json", success: function (datos) {
            if (datos.str > 0) {


                $("#listado ul").empty();
                var subTotalFinal = 0;
                var basedoceFinal = 0;
                var baseceroFinal = 0;
                var IvaFinal = 0;
                var TotalFinal = 0;

                //alert(JSON.stringify(datos));
                var splits = [];
                for (j = 0; j < datos.str; j++) {
                    if (!existe(splits, datos[j]['dop_cuenta'])) {
                        splits.push(datos[j]['dop_cuenta']);
                    }
                }

                for (var x = 0; x < splits.length; x++) { // .


                    html = ""; // .
                    for (i = 0; i < datos.str; i++) {

                        if (datos[i]['dop_cuenta'] === splits[x]) { // .



                            if (datos[i]['tipo'] > 0) {
                                if (parseFloat(datos[i]['dop_precio_unitario']) > 0) {
                                    html += "<li id='" + datos[i]['dop_id'] + "' onclick='fn_modificarLista(\"" + datos[i]['dop_id'] + "\")' codigovalidador=" + datos[i]['codigoAnularValidacion'] + " gramos=" + datos[i]['plu_gramo'] + " anular=" + datos[i]['plu_anulacion'] + " ancestro='" + datos[i]['ancestro'] + "' tipo='" + datos[i]['tipo'] + "'><div class='listaproductosDescTomaPedido'>" + datos[i]['magp_desc_impresion'] + "</div><div class='listaproductosValTomaPedido'>$" + (datos[i]['dop_precio_unitario'] * datos[i]['dop_cantidad']).toFixed(2) + "</div><div class='listaproductosCantTomaPedido'>" + datos[i]['dop_cantidad'] + "</div></li>";
                                    TOTALCUENTA += (datos[i]['dop_precio_unitario'] * datos[i]['dop_cantidad']);
                                    if (parseFloat(datos[i]['validador']) > 0)
                                    {
                                        VALIDADOR += parseFloat(datos[i]['validador']);//).toFixed(2); 
                                    }
                                } else {
                                    html += "<li id='" + datos[i]['dop_id'] + "' onclick='fn_modificarLista(\"" + datos[i]['dop_id'] + "\")' gramos=" + datos[i]['plu_gramo'] + " anular=" + datos[i]['plu_anulacion'] + " ancestro='" + datos[i]['ancestro'] + "' tipo='" + datos[i]['tipo'] + "'><div class='listaproductosDescTomaPedido'>" + datos[i]['magp_desc_impresion'] + "</div></li>";
                                }
                            } else {
                                html += "<li id='" + datos[i]['plu_id'] + "' onclick='fn_modificarLista(\"" + datos[i]['plu_id'] + "\")' ancestro='" + datos[i]['plu_id'] + "' tipo='" + datos[i]['tipo'] + "'><div class='listaproductosDescTomaPedido'>" + datos[i]['magp_desc_impresion'] + "</div></li>";
                            }
                            var subTotal = (datos[i]['dop_precio_unitario'] * datos[i]['dop_cantidad']);
                            subTotalFinal = subTotalFinal + subTotal;
                            if (datos[i]['plu_impuesto'] == 0) {
                                var basecero = (datos[i]['dop_precio_unitario'] * datos[i]['dop_cantidad']);
                                baseceroFinal = baseceroFinal + basecero;
                            } else if (datos[i]['plu_impuesto'] == 1) {
                                var basedoce = (datos[i]['dop_precio_unitario'] * datos[i]['dop_cantidad']);
                                basedoceFinal = basedoceFinal + basedoce;
                            }
                            var Iva = (datos[i]['dop_cantidad'] * datos[i]['dop_iva']);
                            IvaFinal = IvaFinal + Iva;
                            var Total = (datos[i]['dop_cantidad'] * datos[i]['dop_total']);
                            TotalFinal = TotalFinal + Total;

                        }
                    } // .

                    html = concat_split(html, 1, splits[x]); // .
                    htmlTotal += html;


                } // .
                $("#container_splits").html(htmlTotal);


                //  $("#container_splits").html(html);


                var ancestro = $('li#' + datos[datos.str - 1]['dop_id']).attr("ancestro");
                if (ancestro.length > 0) {
                    $('[ancestro="' + ancestro + '"]').addClass('focus');
                } else {
                    $('li#' + datos[datos.str - 1]['dop_id']).addClass("focus");
                }
                $(".subTotal").html(subTotalFinal.toFixed(2));
                $(".baseCero").html(baseceroFinal.toFixed(2));
                $(".baseDoce").html(basedoceFinal.toFixed(2));
                $(".Iva").html(IvaFinal.toFixed(2));
                $(".Total").html(TotalFinal.toFixed(2));
                document.getElementById("cantidad").value = "";
                lc_cantidad = 1;
            } else {
                var subTotalFinal = 0;
                var basedoceFinal = 0;
                var baseceroFinal = 0;
                var IvaFinal = 0;
                var TotalFinal = 0;
                $(".subTotal").html(subTotalFinal.toFixed(2));
                $(".baseCero").html(baseceroFinal.toFixed(2));
                $(".baseDoce").html(basedoceFinal.toFixed(2));
                $(".Iva").html(IvaFinal.toFixed(2));
                $(".Total").html(TotalFinal.toFixed(2));
                $("#listadoPedido").html("");
            }
        }
    });
    fn_focusLector();
}


function fn_activarProductos(mag_id) {
    var html = "";
    $("#barraProducto").show();
    $("#agregarCantidad").show();
    $("#barraProducto").empty();
    $("#codigoCategoria").val(mag_id);
    send = {"cargarProducto": 1};
    send.menu_id = $('#hide_menu_id').val();
    send.mag_id = mag_id;
    send.cla_id = $('#hid_cla_id').val();
    send.cat_id = rst_categoria;
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var posicionTotal = "" + datos[i]['magp_orden'] + "";
                var separar = posicionTotal.split(",");
                left = separar[0];
                arriba = separar[1];
                if (datos[i]['std_fecha'] < 1) {
                    html += "<button id='btn_p" + datos[i]['magp_id'] + "' onclick='fn_verificarGramosPlu(\"" + datos[i]['magp_id'] + "\", " + datos[i]['plu_id'] + "," + datos[i]['validador'] + ")' style=';position:absolute;background-color:" + datos[i]['magp_color'] + "; color:" + datos[i]['magp_colortexto'] + "; left:" + left + "px; top:" + arriba + "px;' class='producto_inactivo' pls_grms='" + datos[i]['plu_gramo'] + "' disabled>" + datos[i]['magp_desc_impresion'] + "</button>";
                } else {
                    html += "<button id='btn_p" + datos[i]['magp_id'] + "' onclick='fn_verificarGramosPlu(\"" + datos[i]['magp_id'] + "\", " + datos[i]['plu_id'] + "," + datos[i]['validador'] + ")' style='position:absolute;background-color:" + datos[i]['magp_color'] + "; color:" + datos[i]['magp_colortexto'] + "; left:" + left + "px; top:" + arriba + "px;' class='producto_activo' pls_grms='" + datos[i]['plu_gramo'] + "'>" + datos[i]['magp_desc_impresion'] + "</button>";
                }
            }
            $("#barraProducto").html(html);
        }
    });
    fn_focusLector();
}

function fn_okBuscar() {
    $("#keyboard").hide();
    var cantidad = $("#cantidadOK").val();
    var texto = $("#txt_busca").val();
    var plu_id = $("#hide_pluId").val();
    if (plu_id != "") {
        if (cantidad != "") {
            if (texto != "") {
                var plu_id = $("#hide_pluId").val();
                var magp_id = $("#hide_magp_id").val();
                var plu_gram = $("#hide_magp_id").val();
                lc_cantidad = cantidad;
                fn_verificarPreguntaSugerida(magp_id, plu_id, /*plu_gram,*/0);
                $("#cuadro_buscador").dialog("close");
                document.getElementById("hide_pluId").value = "";
                $("#txt_busca").val("");
                $("#cantidadOK").val(0);
                $("#etiqueta_cantidad").val('x1');
            } else {
                alertify.alert("Ingrese La Busqueda para Agregar Plu");
            }
        } else {
            if (texto != "") {
                var plu_id = $("#hide_pluId").val();
                var magp_id = $("#hide_magp_id").val();
                var plu_gram = $("#hide_magp_id").val();
                lc_cantidad = 1;
                fn_verificarPreguntaSugerida(magp_id, plu_id/*, plu_gram*/, 0);
                $("#cuadro_buscador").dialog("close");
                document.getElementById("hide_pluId").value = "";
                $("#txt_busca").val("");
                $("#cantidadOK").val(0);
                $("#etiqueta_cantidad").val('x1');
            } else {
                alertify.alert("Ingrese La Busqueda para Agregar Plu");
            }
        }
    } else {
        alertify.alert("Ingrese una Busqueda correcta");
    }
    fn_focusLector();
}

function fn_verificarPreguntaSugerida(magp_id, plu_id, codigoValidador) {
    preguntas_sug = new Array();
    var cantidad = $("#cantidadOK").val();
    var html_mod = "";
    var total_preguntas = 0;
    send = {"verificarPreguntasSugerida": 1};
    send.cat_id = rst_categoria;
    send.plu_id = plu_id;
    send.menu_id = $("#hide_menu_id").val();
    $.ajax({async: false, url: "config_ordenPedido.php", data: send, dataType: "json", success: function (datos) {
            if (datos.str > 0)
            {
                for (i = 0; i < datos.str; i++) {
                    var tipo_opcion = 0;
                    var psug_resp_minima = datos[i]['psug_resp_minima'];
                    var psug_resp_maxima = datos[i]['psug_resp_maxima'];
                    var psug_id = datos[i]['psug_id'];
                    var contenedor_id = i;
                    var new_contenedor_id = contenedor_id + 1;
                    if (psug_resp_minima == 0) {
                        tipo_opcion = 1;
                    }
                    preguntas_sug[i] = new Array(i, psug_resp_minima, psug_resp_maxima, tipo_opcion);
                    if (datos[i]['psug_resp_minima'] == 0) {
                        html_mod += "<div id='preguntasContenedor_" + i + "'  style='clear:both;'><div class='preguntasTituloOpcional'><label>" + datos[i]['pre_sug_descripcion'] + "</label><button class='btn_lmpr_slccn_prgnts_sugrds' onclick='fn_limpiarSeleccionPreguntaSugerida(" + i + ")'></button></div>";
                    } else {
                        html_mod += "<div id='preguntasContenedor_" + i + "'  style='clear:both;'><div class='preguntasTitulo'><label>[Obligatorio] " + datos[i]['pre_sug_descripcion'] + " (Min: " + datos[i]['psug_resp_minima'] + " - Max: " + datos[i]['psug_resp_maxima'] + ")</label><button class='btn_lmpr_slccn_prgnts_sugrds' onclick='fn_limpiarSeleccionPreguntaSugerida(" + i + ")'></button></div>";
                    }
                    send = {"verificarRespuestaPreguntasSugeridas": 1};
                    send.psug_id = psug_id;
                    send.cat_id = rst_categoria;
                    $.ajax({async: false, url: "config_ordenPedido.php", data: send, dataType: "json", success: function (datos) {
                            for (j = 0; j < datos.str; j++) {
                                var res_descripcion = datos[j]['res_descripcion'];
                                res_descripcion = res_descripcion.replace(new RegExp(" ", 'g'), "_");
                                html_mod += "<div class='divContenedorRespuestasAPreguntasSugeridas'><button class='btn_descripcionPreguntas_sugeridas' type='button' id='pl_prgt_sug_pcn" + datos[j]['plu_respuesta'] + "_" + i + "' name='grp_prgnts" + i + "' value='" + datos[j]['plu_respuesta'] + "' onclick='fn_sumarCantidadRespuestaPreguntaSugerida(" + datos[j]['plu_respuesta'] + ", " + i + ")'>" + datos[j]['res_descripcion'] + "</button><button value='" + datos[j]['plu_respuesta'] + "' class='slccn_cntdad_prgnts_sgrds' style='border-radius: 0px 0px 0px 0px; border: 0px solid #000000; font-size=25px; width=30px;' id='pl_prgt_sug_cnt" + datos[j]['plu_respuesta'] + "_" + i + "'>0</button></div>";
                            }
                        }});
                    html_mod += "</div>";
                    total_preguntas++;
                }
                var plu_descripcion = $('#btn_p' + magp_id).html();
                plu_descripcion = plu_descripcion.toUpperCase();
                $('#cbcr_prgnts_sgrds_cntdor').html("<h4>1/" + cantidad + "</h4>");
                $('#cbcr_prgnts_sgrds').html("<h4>" + plu_descripcion + "</h4>");
                $('#cntndr_body_prgnts_sgrds').html(html_mod);
                $('#cntndr_mdl_prgnts_sgrds_btns').html('<div class="cnt_swtch_prgnts"><h5>&iquest;Agrupar Orden?</h5></div><div class="cnt_swtch_prgnts"><div class="switch"><input id="cmn-toggle-1" class="cmn-toggle cmn-toggle-round" type="checkbox"><label for="cmn-toggle-1"></label></div></div><input type="button" id="btn_prgnts_sgrds_cnfrmar" onclick="fn_guardarRespuestasPreguntasSugeridas(' + total_preguntas + ', ' + plu_id + ')" class="boton_Opcion_PS_Bloqueado" value="Confirmar"/>&nbsp;&nbsp;<input type="button" onclick="fn_limpiarPreguntasSugeridasTodo(' + total_preguntas + ')" class="boton_Opcion_PS" value="Limpiar Todo"/>&nbsp;&nbsp;<input type="button" onclick="fn_cancelarPreguntasSugeridas()" class="boton_Opcion_PS" value="Cancelar"/>');
                $('#mdl_prgnts_sgrds').css('display', 'block');
                $('#mdl_pcns_prgnts_sgrds').css('display', 'block');
                $('#btn_prgnts_sgrds_cnfrmar').attr("disabled", true);
                if (datos.str == 1)
                {

                    if (datos[0]['psug_resp_minima'] == 0)
                    {
                        $('#btn_prgnts_sgrds_cnfrmar').attr("disabled", false);
                        $('#btn_prgnts_sgrds_cnfrmar').removeClass('boton_Opcion_PS_Bloqueado');
                        $('#btn_prgnts_sgrds_cnfrmar').addClass("boton_Opcion_PS");
                    } else
                    {
                        $('#btn_prgnts_sgrds_cnfrmar').attr("disabled", true);
                        $('#btn_prgnts_sgrds_cnfrmar').removeClass('boton_Opcion_PS');
                        $('#btn_prgnts_sgrds_cnfrmar').addClass("boton_Opcion_PS_Bloqueado");
                    }
                }

            } else {
                fn_verificarElemnto(magp_id, plu_id, codigoValidador);
            }
        }});
}

function fn_sumarCantidadRespuestaPreguntaSugerida(id, i)
{
    var contador = 0;
    $("#pl_prgt_sug_cnt" + id + "_" + i).addClass("seleccionado" + i);
    $(".seleccionado" + i).each(function () {
        cnt = $(this).text();
        contador += parseInt(cnt);
    });
    /*$("input[name=grp_prgnts" + i + "]:checked").each(function () {
     cnt = $("#pl_prgt_sug_cnt" + $(this).val() + "_" + i).html();
     contador += parseInt(cnt);
     });*/
    if (preguntas_sug[i][1] == 0 || contador < preguntas_sug[i][2])
    {
        var qyt = $("#pl_prgt_sug_cnt" + id + "_" + i).html();
        qyt = parseInt(qyt);
        qyt++;
        $("#pl_prgt_sug_cnt" + id + "_" + i).html(qyt);
        var marcado = $("#pl_prgt_sug_pcn" + id + "_" + i).hasClass("seleccionado");
        if (!marcado) {
            $("#pl_prgt_sug_pcn" + id + "_" + i).addClass('seleccionado');
        }
    } else {
        $("#preguntasContenedor_" + preguntas_sug[i][0]).find(".preguntasTitulo").css({"background": "#BD6120"});
    }
    fn_verificarRespuestasPreguntassugeridas();
}

function fn_limpiarSeleccionPreguntaSugerida(i) {
    $("#preguntasContenedor_" + preguntas_sug[i][0]).find(".preguntasTitulo").css({"background": "#0E98B6"});
    //$("input[name=grp_prgnts" + i + "]").attr('checked', false);
    $(".seleccionado" + i).html(0);
    $(".seleccionado" + i).removeClass("seleccionado" + i);
    //$("#preguntasContenedor_" + i + " label.slccn_cntdad_prgnts_sgrds").html(0);

    fn_verificarRespuestasPreguntassugeridas();
}

function fn_cancelarPreguntasSugeridas()
{
    $("#cantidad").val(1);
    $("#cantidadOK").val(1);
    $("#etiqueta_cantidad").val('x1');
    $('#mdl_prgnts_sgrds').css('display', 'none');
    $('#mdl_pcns_prgnts_sgrds').css('display', 'none');
}

function fn_limpiarPreguntasSugeridasTodo(total_preguntas) {
    $(".slccn_cntdad_prgnts_sgrds").html(0);
    for (i = 0; i < total_preguntas; i++) {
        $(".seleccionado" + i).removeClass("seleccionado" + i);
        $("#preguntasContenedor_" + preguntas_sug[i][0]).find(".preguntasTitulo").css({"background": "#0E98B6"});
    }
    fn_verificarRespuestasPreguntassugeridas();
}

function fn_verificarRespuestasPreguntassugeridas() {
    var continuar = true;
    var total_preguntas = preguntas_sug.length;
    /*
     $(".seleccionado"+i).each(function () {    
     cnt = $(this).text();    
     //pl_prgt_sug_cnt39832_0   
     contador += parseInt(cnt);   
     });   
     */
    for (i = 0; i < total_preguntas; i++) {
        preguntas_sug[i][4] = 0;
        $(".seleccionado" + i).each(function () {
            cnt = $(this).text();
            preguntas_sug[i][4] = preguntas_sug[i][4] + parseInt(cnt);
        });
    }
    for (i = 0; i < total_preguntas; i++) {
        if (preguntas_sug[i][1] != 0) {
            if ((preguntas_sug[i][4]) < preguntas_sug[i][1] || (preguntas_sug[i][4]) > preguntas_sug[i][2]) {
                continuar = false;
            }
        }
    }
    if (continuar) {
        $('#btn_prgnts_sgrds_cnfrmar').attr("disabled", false);
        $('#btn_prgnts_sgrds_cnfrmar').removeClass('boton_Opcion_PS_Bloqueado');
        $('#btn_prgnts_sgrds_cnfrmar').addClass("boton_Opcion_PS");
    } else {
        $('#btn_prgnts_sgrds_cnfrmar').attr("disabled", true);
        $('#btn_prgnts_sgrds_cnfrmar').removeClass('boton_Opcion_PS');
        $('#btn_prgnts_sgrds_cnfrmar').addClass("boton_Opcion_PS_Bloqueado");
    }
}

function fn_guardarRespuestasPreguntasSugeridas(total_preguntas, plu_id) {
    var agrupacion = false;
    var prg_opcional = '';
    var estado_cantidad = 0;
    var cantidad = $("#cantidadOK").val();
    var continuar = true;
    var odp_id = document.getElementById("hide_odp_id").value;
    var cdn_plus = "";
    var cnt = 0;
    estado_cantidad = parseInt($('#cbcr_prgnts_sgrds_cntdor h4').html());
    if ($("#cmn-toggle-1").is(':checked')) {
        agrupacion = true;
    }
    if (agrupacion) {
        cdn_plus = plu_id + "_" + (cantidad - estado_cantidad + 1) + "_1_";
    } else {
        cdn_plus = plu_id + "_1_1_";
    }
    for (i = 0; i < total_preguntas; i++) {
        preguntas_sug[i][4] = 0;
        /*
         $(".seleccionado"+i).each(function () {
         cnt = $(this).text(); 
         */
        $(".seleccionado" + i).each(function () {
            cnt = $(this).text();
            if (preguntas_sug[i][3] == 0) {
                if (agrupacion) {
                    cdn_plus += $(this).val() + "_" + cnt * (cantidad - estado_cantidad + 1) + "_" + preguntas_sug[i][3] + "_";
                } else {
                    cdn_plus += $(this).val() + "_" + cnt + "_" + preguntas_sug[i][3] + "_";
                }
            } else {
                if (agrupacion) {
                    prg_opcional += $(this).val() + "_" + cnt * (cantidad - estado_cantidad + 1) + "_" + preguntas_sug[i][3] + "_";
                } else {
                    prg_opcional += $(this).val() + "_" + cnt + "_" + preguntas_sug[i][3] + "_";
                }
            }
            preguntas_sug[i][4] = preguntas_sug[i][4] + parseInt(cnt);
        });
    }
    cdn_plus += prg_opcional;
    for (i = 0; i < total_preguntas; i++) {
        if (preguntas_sug[i][1] != 0) {
            if ((preguntas_sug[i][4]) < preguntas_sug[i][1] || (preguntas_sug[i][4]) > preguntas_sug[i][2]) {
                $("#preguntasContenedor_" + preguntas_sug[i][0]).find(".preguntasTitulo").css({"background": "#BD6120"});
                continuar = false;
            } else {
                $("#preguntasContenedor_" + preguntas_sug[i][0]).find(".preguntasTitulo").css({"background": "#0E98B6"});
            }
        }
    }
    if (continuar) {
        send = {"agregarPreguntaSugerida": 1};
        send.cat_id = rst_categoria;
        send.odp_id = odp_id;
        send.plus = cdn_plus;
        send.cantidad = 1;
        $.ajax({async: false, url: "config_ordenPedido.php", data: send, dataType: "json", success: function (datos) {
                if (datos.str > 0) {
                    $("#listado ul").empty();
                    for (i = 0; i < datos.str; i++) {
                        if (datos[i]['tipo'] > 0) {
                            if (parseFloat(datos[i]['dop_precio_unitario']) > 0) {
                                html = "<li id='" + datos[i]['dop_id'] + "' onclick='fn_modificarLista(\"" + datos[i]['dop_id'] + "\")' gramos=" + datos[i]['plu_gramo'] + " anular=" + datos[i]['plu_anulacion'] + " ancestro='" + datos[i]['ancestro'] + "' tipo='" + datos[i]['tipo'] + "'><div class='listaproductosDescTomaPedido'>" + datos[i]['magp_desc_impresion'] + "</div><div class='listaproductosValTomaPedido'>$" + (datos[i]['dop_precio_unitario'] * datos[i]['dop_cantidad']).toFixed(2) + "</div><div class='listaproductosCantTomaPedido'>" + datos[i]['dop_cantidad'] + "</div></li>";
                                TOTALCUENTA += (datos[i]['dop_precio_unitario'] * datos[i]['dop_cantidad']);//).toFixed(2);                         
                                if (parseFloat(datos[i]['validador']) > 0)
                                {
                                    VALIDADOR += parseFloat(datos[i]['validador']);//).toFixed(2); 
                                }
                                $("#listadoPedido").append(html);
                            } else {
                                html = "<li id='" + datos[i]['dop_id'] + "' onclick='fn_modificarLista(\"" + datos[i]['dop_id'] + "\")' gramos=" + datos[i]['plu_gramo'] + " anular=" + datos[i]['plu_anulacion'] + " ancestro='" + datos[i]['ancestro'] + "' tipo='" + datos[i]['tipo'] + "'><div class='listaproductosDescTomaPedido'>" + datos[i]['magp_desc_impresion'] + "</div></li>";
                                $("#listadoPedido").append(html);
                            }
                        } else {
                            html = "<li id='" + datos[i]['plu_id'] + "' onclick='fn_modificarLista(\"" + datos[i]['plu_id'] + "\")' ancestro='" + datos[i]['plu_id'] + "' tipo='" + datos[i]['tipo'] + "'><div class='listaproductosDescTomaPedido'>" + datos[i]['magp_desc_impresion'] + "</div></li>";
                            $("#listadoPedido").append(html);
                        }
                        if (i == datos.str - 1) {
                            var ancestro = 0;
                            ancestro = datos[i]['ancestro'];
                            $('#listadoPedido li').removeClass('focus');
                            $('#' + datos[i]['dop_id']).addClass("focus");
                            if (ancestro.length > 0) {
                                $('[ancestro="' + ancestro + '"]').addClass('focus');
                            }
                        }
                    }
                    $("#cantidad").val("");
                    $("#etiqueta_cantidad").val('x1');
                    lc_cantidad = 1;
                } else {
                    $("#listadoPedido").html("");
                }
            }
        });
        if (estado_cantidad == cantidad || agrupacion) {
            $("#cantidadOK").val(1);
            $("#etiqueta_cantidad").val('x1');
            $('#mdl_prgnts_sgrds').css('display', 'none');
            $('#mdl_pcns_prgnts_sgrds').css('display', 'none');
            $('#cntndr_body_prgnts_sgrds').html("");
            fn_focusLector();
        } else {
            estado_cantidad++;
            $('#cbcr_prgnts_sgrds_cntdor h4').html(estado_cantidad + "/" + cantidad);
            for (i = 0; i < total_preguntas; i++) {
                $("input[name=grp_prgnts" + i + "]").attr('checked', false);
            }
            $(".slccn_cntdad_prgnts_sgrds").html(0);
            $("#mdl_prcns_prgnts_sgrds").find(".preguntasTitulo").css({"background": "#0E98B6"});
            $('#btn_prgnts_sgrds_cnfrmar').attr("disabled", true);
            $('#btn_prgnts_sgrds_cnfrmar').removeClass('boton_Opcion_PS');
            $('#btn_prgnts_sgrds_cnfrmar').addClass("boton_Opcion_PS_Bloqueado");
        }
    }
}

function fn_cerrarDialogo(new_contenedor_id) {
    if ($("#preguntasContenedor_" + new_contenedor_id + "").length > 0) {
        var less_new_contenedor_id = new_contenedor_id - 1;
        $("#preguntasContenedor_" + less_new_contenedor_id + "").hide();
        $("#preguntasContenedor_" + new_contenedor_id + "").removeClass("preguntasOculto");
        $("#preguntasContenedor_" + new_contenedor_id + "").show(500);
        $('#pluAgregar').val('');
        $('#magpAgregar').val('');
    } else {
        $('#preguntasContenedor').dialog('close');
        $('#preguntasContenedor').empty();
        $('#pluAgregar').val('');
        $('#magpAgregar').val('');
    }
    fn_focusLector();
}

function fn_modificarLista(dop_id) {
    var ancestro = "";
    var tipo_plu = 0;
    ancestro = $('#' + dop_id).attr("ancestro");
    tipo_plu = $('#' + dop_id).attr("tipo");
    $('.listadoPedido li').removeClass('focus');
    $('.' + dop_id).addClass("focus");
    if (ancestro.length > 0) {
        $('[ancestro="' + ancestro + '"]').addClass('focus');
    }
    fn_focusLector();
    if (tipo_plu > 0) {
        if ($("#" + ancestro).val() == 0 && $("#hid_bandera_gramo").val() == 1) {
            alertify.alert("No puede aplicar cantidad en gramos para eliminar este producto.")
            $("#hid_bandera_gramo").val(0);
            fn_focusLector();
            return false;
        }
    }
}

function fn_verificarGramosPluParametro(magp_id, plu_id, plu_gramos) {
    var cantidad = $("#cantidadOK").val();
    if (plu_gramos == 0 && cantidad.indexOf('.') > 0) {
        alertify.alert("No se puede aplicar cantidad en gramos para este producto.");
        return false;
    } else {
        fn_verificarPreguntaSugerida(magp_id, plu_id, 0);
    }
}
function fn_verificarGramosPluBusqueda(magp_id, plu_id, validaMinimo) {
    fn_cerrarModalBuscador();
    fn_verificarGramosPlu(magp_id, plu_id, validaMinimo);
}
function fn_verificarGramosPlu(magp_id, plu_id, validaMinimo) {
    //alert(TOTALCUENTA+' '+validaMinimo);    
    //VALIDADOR=validaMinimo;// variable para validar consuno de WS
    var cantidad = $("#cantidadOK").val();
    var plu_gramos = $('#btn_p' + magp_id).attr('pls_grms');
    if (plu_gramos == 0 && cantidad.indexOf('.') > 0) {
        alertify.alert("No se puede aplicar cantidad en gramos para este producto.");
        return false;
    } else
    {
        if (validaMinimo > 0)
        {
            fn_ejecutaValidaciones(magp_id, plu_id);
            /*
             if(parseFloat(TOTALCUENTA)<parseFloat(validaMinimo)) 
             {
             alertify.alert("Para aplicar este producto el TOTAL de la cuenta debe ser superior a: $ "+parseFloat(validaMinimo).toFixed(2));
             return false;
             }
             else
             {
             fn_abreTeclado(magp_id, plu_id,validaMinimo);
             }
             */

        } else
        {
            fn_verificarPreguntaSugerida(magp_id, plu_id, 0);
        }
    }
}

function fn_ejecutaValidaciones(magp_id, plu_id) {
    var send;
    send = {};
    send.buscarValidacionesPLU = 1;
    send.plu_id = plu_id;
    $.ajax({
        url: "../ordenpedido/config_ordenPedido.php",
        type: "POST",
        dataType: "json",
        accept: "application/json",
        data: send,
        async: false,
        success: function (datos) {
            //console.log(datos);
            var validaciones = datos.validaciones;
            var posicionHeladeria = $.inArray('HELADERIA', validaciones);
            var validaMinimo = datos.valorMinimo;
            var existeCajeroActivo = true;
            //No hay validaciones por relizar, continuar
            if (datos.str == 0) {
                fn_verificarPreguntaSugerida(magp_id, plu_id, 0);
                return true;
            }
            //Si tiene Validacion de Heladeria, priorizar
            if (posicionHeladeria >= 0) {
                existeCajeroActivo = fn_validaCajeroHeladeria(magp_id, plu_id);
                if (false == existeCajeroActivo) {
                    return false;
                } else
                if ((true == existeCajeroActivo) && validaciones.length === 1) {
                    fn_verificarPreguntaSugerida(magp_id, plu_id, 0);
                }
                validaciones.splice(posicionHeladeria, 1);
            }

            //Validación de Go Trade
            if ($.inArray('GO TRADE', validaciones) >= 0) {
                if (!existeCajeroActivo)
                    return false;
                if (parseFloat(TOTALCUENTA) < parseFloat(validaMinimo))
                {
                    alertify.alert("Para aplicar este producto el TOTAL de la cuenta debe ser superior a: $ " + parseFloat(validaMinimo).toFixed(2));
                    return false;
                } else
                {
                    fn_abreTeclado(magp_id, plu_id, validaMinimo);
                }
            }
        }
    });
}

function fn_validaCajeroHeladeria(maggp_id, plu_id)
{
    fn_cargando(1);
    var resp = false;
    var send;
    send = {};
    send.metodo = "validaCajeroHeladeria";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../ordenpedido/configWS.php", data: send, success: function (datos)
        {
            fn_cargando(0);
            if (datos.estado == 1)
            {
                resp = true;
                return true;
            } else if (datos.estado == 0)
            {
                alertify.alert("No existe un cajero activo en la Heladería. No puede digitar este producto.");
                resp = false;
                return false;
            } else
            {
                alertify.alert("Existen problemas tratando de consultar Web Service de Heladería.");
                resp = false;
                return false;
            }
            /* if(datos.status==0)
             {
             VALIDADOR=validaMinimo;
             fn_verificarPreguntaSugerida(mg, pl);
             }
             else
             {
             alertify.alert(datos.mensaje);
             return false;
             }  */
        }, error: function () { // Cuando el WS no esta en linea 
            fn_cargando(0);
            alertify.alert("Existen problemas tratando de consultar Web Service de Heladería.");
            return false;
        }
    });
    return resp;
}

function fn_abreTeclado(magpid, pluid, validaMinimo)
{
    $("#tecladoCodigos").show();
    $("#tecladoCodigos").dialog({
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
            fn_alfaNumericoCanje("#txt_codigos");
            $('#txt_codigos').val('');
            $('#txt_codigos').attr('onchange', 'fn_validaCodigo(event,"' + magpid + '","' + pluid + '","' + validaMinimo + '")');
        }
    });
}

function fn_validaCodigo(event, mg, pl, validaMinimo)
{
    fn_cargando(1);
    event.stopPropagation();
    codigosCanje = $("#txt_codigos").val();
    //return false;
    send = {};
    send.metodo = "validaCodigo";
    send.codigo = trim(codigosCanje);
    send.bandera = "compra";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../ordenpedido/configWS.php", data: send, success: function (datos)
        {
            fn_cargando(0);
            $("#tecladoCodigos").dialog("close");
            $("#keyboard").hide();
            if (datos.status == 0)
            {
                VALIDADOR = validaMinimo;
                codigoAgrabar = $("#txt_codigos").val();
                fn_verificarPreguntaSugerida(mg, pl, codigoAgrabar);
                $("#txt_codigos").val("");
            } else
            {
                fn_cargando(0);
                alertify.alert('Error : ' + datos.mensaje);
                $("#txt_codigos").val("");
                return false;
            }
        }
    });
}


function fn_verificarElemnto(magp_id, plu_id, codigoValidado)
{
    //alert(codigoValidado);
    var cantidad = $("#cantidadOK").val();
    if ($("#btn_salonLlevar").val() == 'Salon') {
        lc_control = 1;
    } else {
        lc_control = 2;
    }
    $('#btn_p' + magp_id + '').prop("disabled", true);
    $('#magpAgregar').val('');
    $('#magpAgregar').val(magp_id);
    $("#listado ul").empty();
    var odp_id = $("#hide_odp_id").val();
    var dop_id = $("#hide_dop_id").val();
    var totalito = 0;
    TOTALCUENTA = 0;
    VALIDADOR = 0;
    send = {"agregarPlusOrdenPedido": 1};
    send.cat_id = rst_categoria;
    send.odp_id = odp_id;
    send.plu_id = plu_id;
    send.cantidad = lc_cantidad;
    send.menuId = $("#hide_menu_id").val();
    send.codigoValidadoo = codigoValidado;
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            $("#listado ul").empty();
            for (i = 0; i < datos.str; i++) {
                if (datos[i]['tipo'] > 0) {
                    if (parseFloat(datos[i]['dop_precio_unitario']) > 0) {
                        html = "<li id='" + datos[i]['dop_id'] + "' onclick='fn_modificarLista(\"" + datos[i]['dop_id'] + "\")' codigoValidador=" + datos[i]['codigoAnularValidacion'] + " gramos=" + datos[i]['plu_gramo'] + " anular=" + datos[i]['plu_anulacion'] + " ancestro='" + datos[i]['ancestro'] + "' tipo='" + datos[i]['tipo'] + "'><div class='listaproductosDescTomaPedido'>" + datos[i]['magp_desc_impresion'] + "</div><div class='listaproductosValTomaPedido'>$" + (datos[i]['dop_precio_unitario'] * datos[i]['dop_cantidad']).toFixed(2) + "</div><div class='listaproductosCantTomaPedido'>" + datos[i]['dop_cantidad'] + "</div></li>";
                        TOTALCUENTA += (datos[i]['dop_precio_unitario'] * datos[i]['dop_cantidad']);//).toFixed(2);                         
                        if (parseFloat(datos[i]['validador']) > 0)
                        {
                            VALIDADOR += parseFloat(datos[i]['validador']);//).toFixed(2); 
                        }
                        $("#listadoPedido").append(html);
                    } else {
                        html = "<li id='" + datos[i]['dop_id'] + "' onclick='fn_modificarLista(\"" + datos[i]['dop_id'] + "\")' gramos=" + datos[i]['plu_gramo'] + " anular=" + datos[i]['plu_anulacion'] + " ancestro='" + datos[i]['ancestro'] + "' tipo='" + datos[i]['tipo'] + "'><div class='listaproductosDescTomaPedido'>" + datos[i]['magp_desc_impresion'] + "</div></li>";
                        $("#listadoPedido").append(html);
                    }
                } else {
                    html = "<li id='" + datos[i]['plu_id'] + "' onclick='fn_modificarLista(\"" + datos[i]['plu_id'] + "\")' ancestro='" + datos[i]['plu_id'] + "' tipo='" + datos[i]['tipo'] + "'><div class='listaproductosDescTomaPedido'>" + datos[i]['magp_desc_impresion'] + "</div></li>";
                    $("#listadoPedido").append(html);
                }
                if (i == datos.str - 1) {
                    var ancestro = 0;
                    ancestro = datos[i]['ancestro'];
                    $('#listadoPedido li').removeClass('focus');
                    $('#' + datos[i]['dop_id']).addClass("focus");
                    if (ancestro.length > 0) {
                        $('[ancestro="' + ancestro + '"]').addClass('focus');
                    }
                }
            }
            $("#cantidad").val(0);
            $("#cantidadOK").val(1);
            $("#etiqueta_cantidad").val('x1');
            lc_cantidad = 1;
            fn_kds();
        }
    });
    $('#btn_p' + magp_id + '').prop("disabled", false);
    fn_focusLector();
    Accion = 0;
    lc_cantidad = 1;
}

function fn_kds() {
    send = {"kds": 1};
    $.getJSON("config_ordenPedido.php", send, function (datos) {});
}

function fn_validarAnulacion() {
    var tipo_plu = 0;
    var dop_id = 0;
    var ancestro = 0;
    if (!($('li.focus').length)) {
        alertify.alert('No ha seleccionado un producto para eliminar');
    } else {
        ancestro = $('#listadoPedido').find("li.focus").attr("ancestro");
        if ($('li.focus').length < 2) {
            dop_id = $('#listadoPedido').find("li.focus").attr("id");
        } else {
            dop_id = $('#listadoPedido').find("li.focus").attr("ancestro");
        }
        tipo_plu = $('#listadoPedido').find("li.focus").attr("tipo");
        $('#btn_eliminarElemnto').prop("disabled", true);
        if (tipo_plu > 0) {
            if ($('#listadoPedido').find("li.focus").attr("anular") > 0) {
                $("#anulacionesContenedor").show();
                $("#anulacionesContenedor").dialog({
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
                        fn_numerico("#usr_clave");
                        $('#usr_clave').val('');
                        $('#usr_clave').attr('onchange', 'fn_validarUsuario()');
                    }
                });
            } else {

                fn_validarTiempo(dop_id);
            }
        } else {
            send = {"eliminarTextoPlu": 1};
            send.dop_id = dop_id;
            $.getJSON("config_ordenPedido.php", send, function (datos) {
                if (datos.Confirmar > 0) {
                    ancestro = $('li#' + dop_id).prev().attr("ancestro");
                    if (ancestro.length > 0) {
                        $('[ancestro="' + ancestro + '"]').addClass('focus');
                    } else {
                        $('li#' + dop_id).prev().addClass("focus");
                    }
                    $('li#' + dop_id).remove();
                    $('#btn_eliminarElemnto').prop("disabled", false);
                }
            });
        }
    }
    fn_focusLector();
}

function fn_validarUsuario() {
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
    if (usr_clave != "") {
        send = {"validarUsuario": 1};
        send.usr_clave = usr_clave;
        send.usr_tarjeta = usr_tarjeta;
        $.getJSON("config_ordenPedido.php", send, function (datos) {
            if (datos.str > 0) {
                $("#anulacionesContenedor").dialog("close");
                $("#usr_clave").val("");
                fn_eliminarElemento();
            } else {
                fn_numerico("#usr_clave");
                alertify.confirm("La clave ingresada no tiene permisos para anulaciones", function (e) {
                    if (e) {
                        alertify.set({buttonFocus: "none"});
                        $("#usr_clave").focus();
                    }
                });
                $("#usr_clave").val("");
            }
        });
    } else {
        alertify.confirm("Ingrese la clave para anular el producto", function (e) {
            if (e) {
                alertify.set({buttonFocus: "none"});
                $("#usr_clave").focus();
                fn_numerico("#usr_clave");
            }
        });
        $("#usr_clave").val("");
    }
    fn_focusLector();
}

function fn_validarTiempo(dop_id) {
    var odp_id = document.getElementById("hide_odp_id").value;
    var rst_id = document.getElementById("hide_rst_id").value;
    send = {"validarTiempoPlu": 1};
    send.dop_id = dop_id;
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            var plu_creacionfecha = datos[0]['plu_creacionfecha'];
            if (plu_creacionfecha < rst_tiempopedido) {
                fn_eliminarElemento();
            } else {
                $("#anulacionesContenedor").show();
                $("#anulacionesContenedor").dialog({
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
                        fn_numerico("#usr_clave");
                        $('#usr_clave').val('');
                        $('#usr_clave').attr('onchange', 'fn_validarUsuario()');
                    }
                });
            }
        } else {
            alertify.alert("No se pudo validar el tiempo de pedido");
        }
    });
    fn_focusLector();
}

function fn_cerrarDialogoAnulacion(id)
{
    if ($(id).prop("id") === "txt_codigos")
    {
        $("#tecladoCodigos").dialog("close");
        $("#txt_codigos").val("");
        $("#numPad").hide();
        $("#keyboard").hide();
    } else
    {
        $('#anulacionesContenedor').dialog('close');
        $('#usr_clave').val('');
        $("#numPad").hide();
        $('#btn_eliminarElemnto').prop("disabled", false);
        $("#keyboard").hide();
        fn_focusLector();
    }
}

function fn_eliminarElemento() {
    //Variable cantidad anular
    var cantidadAnular = $("#cantidadOK").val();
    var dop_id = 0;
    var tipo_plu = 0;
    var odp_id = $("#hide_odp_id").val();
    if (!($('li.focus').length)) {
        alertify.alert('No ha seleccionado un producto para eliminar');
    } else {
        if ($('li.focus').length < 2) {
            dop_id = $('#listadoPedido').find("li.focus").attr("id");
        } else {
            dop_id = $('#listadoPedido').find("li.focus").attr("ancestro");
        }
        $('#btn_eliminarElemnto').prop("disabled", true);
        //Verifica si el producto a eliminar es el �ltimo
        //codigo = $('#listadoPedido').find("li.focus").attr("codigovalidador");
        //alert(codigo);
        //if(codigo>0)
        /*{
         fn_cargando(1);                        
         send = {};
         send.metodo = "validaCodigoAnular";
         send.codigo = codigo;
         send.bandera = "anula"; 
         $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", 
         contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", 
         url: "../ordenpedido/configWS.php", data: send, success: function (datos) 
         {     
         fn_cargando(0)
         send = {"verificarUltimoElemento": 1};
         send.dop_id = dop_id;
         send.odp_id = odp_id;
         send.cantidadAnular = lc_cantidad;
         $.ajax({async: false, url: "config_ordenPedido.php", data: send, dataType: "json", success: function (datos) {
         //Elimina el �ltimo producto de la orden
         if (datos.str == 1) {
         send = {"eliminarUltimoElemento": 1};
         send.dop_id = dop_id;
         send.odp_id = odp_id;
         send.cantidadAnular = lc_cantidad;
         $.ajax({async: false, url: "config_ordenPedido.php", data: send, dataType: "json", success: function (datos) {
         fn_listaPendiente();
         document.getElementById("cantidad").value = "";
         lc_cantidad = 1;
         $("#cantidadOK").val(1);
         $("#etiqueta_cantidad").val('x1');
         }});
         $('#btn_eliminarElemnto').prop("disabled", false);
         } else {
         send = {"eliminarunElemento": 1};
         send.dop_id = dop_id;
         send.odp_id = odp_id;
         send.cantidadAnular = lc_cantidad;
         $.ajax({async: false, url: "config_ordenPedido.php", data: send, dataType: "json", success: function (datos) {
         if (datos.str > 0) {
         if (datos[0]['Resultado'] > 0) {
         var pluCantidadAnt = $("li#" + datos[0]['dop_id'] + " .listaproductosCantTomaPedido").text();
         var pluCantidadNew = datos[0]['dop_cantidad'];
         var pluPrecio = datos[0]['dop_precio_unitario'] * datos[0]['dop_cantidad'];
         var pluPrecioNew = pluPrecio.toFixed(2);
         $('li.focus').text('');
         html = "<div class='listaproductosDescTomaPedido'>" + datos[0]['magp_desc_impresion'] + "<input type='hidden' id='hid_" + dop_id + "' value='" + tipo_plu + "'></div><div class='listaproductosValTomaPedido'>$" + pluPrecioNew + "</div><div class='listaproductosCantTomaPedido'>" + pluCantidadNew + "</div></li>";
         $('li.focus').append(html);
         $("cantidad").val("");
         lc_cantidad = 1;
         $("#cantidadOK").val(1);
         $("#etiqueta_cantidad").val('x1');
         } else {
         lc_cantidad = 1;
         alertify.alert("La cantidad anular es menor a la ingresada.");
         }
         
         }
         }
         });
         $('#btn_eliminarElemnto').prop("disabled", false);
         
         }
         }
         });
         }
         }); 
         } */
        //else
        //{
        send = {"verificarUltimoElemento": 1};
        send.dop_id = dop_id;
        send.odp_id = odp_id;
        send.cantidadAnular = lc_cantidad;
        $.ajax({async: false, url: "config_ordenPedido.php", data: send, dataType: "json", success: function (datos) {
                //Elimina el �ltimo producto de la orden
                if (datos.str == 1) {
                    send = {"eliminarUltimoElemento": 1};
                    send.dop_id = dop_id;
                    send.odp_id = odp_id;
                    send.cantidadAnular = lc_cantidad;
                    $.ajax({async: false, url: "config_ordenPedido.php", data: send, dataType: "json", success: function (datos) {
                            fn_listaPendiente();
                            document.getElementById("cantidad").value = "";
                            lc_cantidad = 1;
                            $("#cantidadOK").val(1);
                            $("#etiqueta_cantidad").val('x1');
                        }});
                    $('#btn_eliminarElemnto').prop("disabled", false);
                } else {
                    send = {"eliminarunElemento": 1};
                    send.dop_id = dop_id;
                    send.odp_id = odp_id;
                    send.cantidadAnular = lc_cantidad;
                    $.ajax({async: false, url: "config_ordenPedido.php", data: send, dataType: "json", success: function (datos) {
                            if (datos.str > 0) {
                                if (datos[0]['Resultado'] > 0) {
                                    var pluCantidadAnt = $("li#" + datos[0]['dop_id'] + " .listaproductosCantTomaPedido").text();
                                    var pluCantidadNew = datos[0]['dop_cantidad'];
                                    var pluPrecio = datos[0]['dop_precio_unitario'] * datos[0]['dop_cantidad'];
                                    var pluPrecioNew = pluPrecio.toFixed(2);
                                    $('li.focus').text('');
                                    html = "<div class='listaproductosDescTomaPedido'>" + datos[0]['magp_desc_impresion'] + "<input type='hidden' id='hid_" + dop_id + "' value='" + tipo_plu + "'></div><div class='listaproductosValTomaPedido'>$" + pluPrecioNew + "</div><div class='listaproductosCantTomaPedido'>" + pluCantidadNew + "</div></li>";
                                    $('li.focus').append(html);
                                    $("cantidad").val("");
                                    lc_cantidad = 1;
                                    $("#cantidadOK").val(1);
                                    $("#etiqueta_cantidad").val('x1');
                                } else {
                                    lc_cantidad = 1;
                                    alertify.alert("La cantidad anular es menor a la ingresada.");
                                }

                            }
                        }
                    });
                    $('#btn_eliminarElemnto').prop("disabled", false);

                }
            }
        });
        //}

    }
    fn_focusLector();
}


function fn_volver(dato) {
    window.location.href = "../ordenpedido/userMesas.php";
}
function fn_cobrar(dop_cuenta)
{
    //alert(VALIDADOR); 
    //return false;
    //alert(TOTALCUENTA+' '+VALIDADOR); 
    //return false;
    //if(VALIDADOR>0)
    //{
    //  if(parseFloat(TOTALCUENTA).toFixed(2)<parseFloat(VALIDADOR).toFixed(2))
    //{
    //  alertify.alert("Producto de promocion exige un minimo de: $ "+parseFloat(VALIDADOR).toFixed(2)+" en total de la cuenta.");               
    //return false;
    //}ss
    //}        
    if (!($('#listadoPedido li').length)) {
        alertify.alert('No ha seleccionado un producto para cobrar');
    } else {
        var odp_id = document.getElementById("hide_odp_id").value;
        var mesa_id = document.getElementById("hide_mesa_id").value;
        $('#formCobrar').html('<form action="../facturacion/factura.php" name="cobro" method="post" style="display:none;"><input type="text" name="odp_id" value="' + odp_id + '" /><input type="text" name="dop_cuenta" value="' + dop_cuenta + '" /><input type="text" name="mesa_id" value="' + mesa_id + '" /></form>');
        document.forms['cobro'].submit();
    }
    return false;
}

function fn_separar() {
    if (!($('#listadoPedido li').length)) {
        alertify.alert('No ha seleccionado un producto para separar cuentas');
    } else {
        var odp_id = document.getElementById("hide_odp_id").value;
        var mesa_id = document.getElementById("hide_mesa_id").value;
        var cdn_tipoimpuesto = document.getElementById("hide_cdn_tipoimpuesto").value;
        var est_ip = document.getElementById("hide_est_ip").value;
        var categoria_id =  document.getElementById("codigoCategoria").value;
       var rst_id =  document.getElementById("hide_rst_id").value;
        $("#formCobrar").html("<form action='separarCuentas.php' name='separar' method='get' style='display:none;'> <input type='text' name='cat_id' value='" + categoria_id + "' /> <input type='text' name='rst_id' value='" + rst_id + "' />    <input type='text' name='odp_id' value='" + odp_id + "' /><input type='text' name='mesa_id' value='" + mesa_id + "' /><input type='text' name='cdn_tipoimpuesto' value='" + cdn_tipoimpuesto + "' /><input type='text' name='est_ip' value='" + est_ip + "' /></form>");
        document.forms['separar'].submit();
    }
    return false;
}

function fn_funcionesGerente() {
    window.location.replace("../funciones/funciones_gerente.php");
}

function fn_agregarNumero(valor) {
    var cantidad = $("#cantidad").val();
    if (cantidad.length == 0 && valor == 0) {
    } else if ((cantidad.length == 0 && valor == ".")) {
        $("#cantidad").val("0.");
        coma = 1;
    } else {
        if (valor == "." && coma == 0) {
            cantidad = cantidad + "" + valor;
            $("#cantidad").val(cantidad);
            coma = 1;
        } else if (valor == "." && coma == 1) {
        } else {
            var variable = cantidad;
            var indice = 0;
            indice = variable.indexOf('.') + 1;
            if (indice > 0) {
                variable = variable.substring(indice, variable.length);
                if (variable.length <= 2) {
                    cantidad = cantidad + "" + valor;
                    $("#cantidad").val(cantidad);
                    fn_focusLector();
                }
            } else {
                cantidad = cantidad + "" + valor;
                $("#cantidad").val(cantidad);
                fn_focusLector();
            }
        }
    }
    fn_focusLector();
}

function fn_eliminarCantidad() {
    var lc_cantidad = $("#cantidad").val();
    if (lc_cantidad != '0.') {
        lc_cantidad = lc_cantidad.substring(0, document.getElementById("cantidad").value.length - 1);
        if (lc_cantidad == "") {
            lc_cantidad = "";
            coma = 0;
        }
        if (lc_cantidad == ".") {
            coma = 0;
        }
        $("#cantidad").val(lc_cantidad);
    } else {
        $("#cantidad").val("");
    }
}

function teclado(elEvento) {
    evento = elEvento || window.event;
    k = evento.keyCode; //n�mero de c�digo de la tecla.
    //teclas n�mericas del teclado alfamun�rico
    if (k > 47 && k < 58) {
        p = k - 48; //buscar n�mero a mostrar.
        p = String(p); //convertir a cadena para poder a��dir en pantalla.
        fn_agregarNumero(p); //enviar para mostrar en pantalla
    }
    //Teclas del teclado n�merico. Seguimos el mismo procedimiento que en el anterior.
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
        document.getElementById("cantidad").value = "";
    }
    fn_focusLector();
}

function fn_cancelarAgregarCantidad() {
    Accion = 0;
    lc_cantidad = 1;
    $("#cantidad").val('');
    $("#etiqueta_cantidad").val('x1');
    $("#aumentarContador").dialog("close");
    $("#keyboard").hide();
}

function fn_popupCantidad() {
    $("#cantidad").val('');
    $("#aumentarContador").dialog({modal: true, autoOpen: false,
        show: {
            effect: "none",
            duration: 500
        },
        hide: {
            effect: "none",
            duration: 500
        },
        width: "auto",
        open: function (event, ui) {
            $(".ui-dialog-titlebar").hide();
        }
    });

    $("#agregarCantidad").click(function () {
        Accion = 0;
        $("#aumentarContador").dialog("open");
        $("#cantidad").val('');
    });

    fn_focusLector();
}

function fn_okCantidad() {
    coma = 0;
    var cantidad = $("#cantidad").val();
    if (parseFloat(cantidad) < 1000 && parseFloat(cantidad) > 0) {
        lc_cantidad = cantidad;
        $("#aumentarContador").dialog("close");
        $("#cantidadOK").val(cantidad);
        if (Accion == 0) {
            Accion = 2;
            fn_focusLector();
        } else {
            fn_verificarGramosPlu(plu_mag_id, plu_lector);
            fn_focusLector();
            Accion = 0;
        }
        $("#etiqueta_cantidad").val('x' + cantidad);
        $("#btn_cambio").val("gramos");
        fn_focusLector();
    } else {
        alertify.alert('Ingrese un cantidad v&aacute;lida.');
        $("#cantidad").val("");
    }
}

function fn_cambiar() {
    var estado = $("#btn_cambio").val();
    if (estado == "gramos") {
        $("#btn_punto").show();
        $("#hid_bandera_gramo").val(1);
        $("#btn_cambio").val("numero");
    } else {
        $("#cantidad").val("");
        $("#etiqueta_cantidad").val('x1');
        $("#btn_punto").hide();
        $("#btn_cambio").val("gramos");
        $("#hid_bandera_gramo").val(0);
    }
    fn_focusLector();
}

function fn_salirMesas() {
    window.location.href = "../ordenpedido/userMesas.php";
}

function fn_confirmarComentarioPlu() {
    var comentario = $("#comentario").val();
    comentario = trim(comentario);
    if (comentario.length > 100) {
        alertify.alert('Comentario excede el n&uacute;mero de caracteres permitidos.');
        return false;
    }
    if (comentario.length > 0) {
        var ancestro = $('#listadoPedido').find("li.focus").attr("ancestro");
        var dop_id = 0;
        if (ancestro == 0) {
            dop_id = $('#listadoPedido').find("li.focus").attr("id");
        } else {
            dop_id = ancestro;
        }
        fn_agregarComentario(comentario, dop_id);
        $("#keyboard").hide();
        $("#keyboard").empty();
        $('#contenedorComentario').dialog("close");
    } else {
        alertify.alert('Agregue un comentario.');
    }
}

function fn_cerrarModalComentar() {
    $("#keyboard").hide();
    $("#keyboard").empty();
    $("#contenedorComentario").dialog("close");
}

function trim(cadena) {
    var retorno = cadena.replace(/^\s+/g, '');
    retorno = retorno.replace(/\s+$/g, '');
    return retorno;
}

function fn_agregarComentarioOrden() {
    var comentario = $("#comentario").val();
    comentario = trim(comentario);
    if (comentario.length > 100) {
        alertify.alert('Comentario excede el n&uacute;mero de caracteres permitidos.');
        return false;
    }
    if (comentario.length > 0) {

        var odp_id = $("#hide_odp_id").val();
        send = {"agregarComentarioOrdenPedido": 1};
        send.odp_id = odp_id;
        send.comentario = comentario;
        $.getJSON("config_ordenPedido.php", send, function (datos) {
            if (datos.Confirmar > 0) {
                $("#nfrmcn_srs_sstm_hora").text(comentario);
            }
        });

        $("#keyboard").hide();
        $("#keyboard").empty();
        $('#contenedorComentario').dialog("close");
    } else {
        alertify.alert('Agregue un comentario.');
    }
}

function fn_comentar() {
    if ($('li.focus').length < 1) {
        $('#plu_comentar').html("Comentar Orden");
        $('#plu_comentar').html(plu_descripcion);
        $("#contenedorComentario").dialog({
            modal: true,
            autoOpen: false,
            position: {
                my: 'top',
                at: 'top+120'
            },
            show: {
                effect: "none",
                duration: 500
            },
            hide: {
                effect: "none",
                duration: 500
            },
            width: 500,
            height: 340,
            open: function (event, ui) {
                $(".ui-dialog-titlebar").hide();
                $("#comentario").val(palabraDefault);
                fn_alfaNumerico('#comentario');
                $('#btn_cancelar_teclado').attr('onclick', 'fn_cerrarModalComentar()');
                $('#btn_ok_teclado').attr('onclick', 'fn_agregarComentarioOrden()');
            }
        });
        $("#contenedorComentario").dialog("open");
    } else {

        var ancestro = $('#listadoPedido').find("li.focus").attr("ancestro");
        var dop_id = 0;
        var tipo_plu = $('#listadoPedido').find("li.focus").attr("tipo");
        if (ancestro == 0) {
            dop_id = $('#listadoPedido').find("li.focus").attr("id");
        } else {
            dop_id = ancestro;
        }
        if (tipo_plu > 0) {
            var plu_descripcion = $('#' + dop_id + ' .listaproductosDescTomaPedido').html();
            $('#plu_comentar').html(plu_descripcion);
            $("#contenedorComentario").dialog({
                modal: true,
                autoOpen: false,
                position: {
                    my: 'top',
                    at: 'top+120'
                },
                show: {
                    effect: "none",
                    duration: 500
                },
                hide: {
                    effect: "none",
                    duration: 500
                },
                width: 500,
                height: 340,
                open: function (event, ui) {
                    $(".ui-dialog-titlebar").hide();
                    $("#comentario").val('');
                    fn_alfaNumerico('#comentario');
                    $('#btn_cancelar_teclado').attr('onclick', 'fn_cerrarModalComentar()');
                    $('#btn_ok_teclado').attr('onclick', 'fn_confirmarComentarioPlu()');
                }
            });
            $("#contenedorComentario").dialog("open");
        } else {
            alertify.alert('Para agregar un comentario debe seleccionar un producto.');
        }
    }
}

function fn_agregarComentario(comentario, dop_id) {
    var ancestro = $('#listadoPedido').find("li.focus").attr("ancestro");
    var odp_id = document.getElementById("hide_odp_id").value;
    send = {"insertarComentario": 1};
    send.odp_id = odp_id;
    send.dop_id = dop_id;
    send.comentario = comentario;
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            $("#listado ul").empty();
            for (i = 0; i < datos.str; i++) {
                if (datos[i]['tipo'] > 0) {
                    if (parseFloat(datos[i]['dop_precio_unitario']) > 0) {
                        html = "<li id='" + datos[i]['dop_id'] + "' onclick='fn_modificarLista(\"" + datos[i]['dop_id'] + "\")' gramos=" + datos[i]['plu_gramo'] + " anular=" + datos[i]['plu_anulacion'] + " ancestro='" + datos[i]['ancestro'] + "' tipo='" + datos[i]['tipo'] + "'><div class='listaproductosDescTomaPedido'>" + datos[i]['magp_desc_impresion'] + "</div><div class='listaproductosValTomaPedido'>$" + (datos[i]['dop_precio_unitario'] * datos[i]['dop_cantidad']).toFixed(2) + "</div><div class='listaproductosCantTomaPedido'>" + datos[i]['dop_cantidad'] + "</div></li>";
                        $("#listadoPedido").append(html);
                    } else {
                        html = "<li id='" + datos[i]['dop_id'] + "' onclick='fn_modificarLista(\"" + datos[i]['dop_id'] + "\")' gramos=" + datos[i]['plu_gramo'] + " anular=" + datos[i]['plu_anulacion'] + " ancestro='" + datos[i]['ancestro'] + "' tipo='" + datos[i]['tipo'] + "'><div class='listaproductosDescTomaPedido'>" + datos[i]['magp_desc_impresion'] + "</div></li>";
                        $("#listadoPedido").append(html);
                    }
                } else {
                    html = "<li id='" + datos[i]['plu_id'] + "' onclick='fn_modificarLista(\"" + datos[i]['plu_id'] + "\")' ancestro='" + datos[i]['plu_id'] + "' tipo='" + datos[i]['tipo'] + "'><div class='listaproductosDescTomaPedido'>" + datos[i]['magp_desc_impresion'] + "</div></li>";
                    $("#listadoPedido").append(html);
                }
            }
            if (ancestro.length > 0) {
                $('[ancestro="' + dop_id + '"]').addClass('focus');
            } else {
                $('#' + dop_id).addClass('focus');
            }
            document.getElementById("cantidad").value = "";
            lc_cantidad = 1;
        }
    });
}

function fn_imprimirOrdenPedido() {
    var odp_id = $("#hide_odp_id").val();
    var est_ipd = $("#hide_est_ip").val();
    var usr_id = $("#hide_usr_id").val();
    var rst_id = $("#hide_rst_id").val();
    send = {"impresionOrdenPedido": 1};
    send.odp_id = odp_id;
    send.est_ipd = est_ipd;
    send.usr_id = usr_id;
    send.rst_id = rst_id;
    send.dop_cuenta = $('#hide_numSplit').val();

    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            if (datos[0]['Confirmar'] < 1) {
                alertify.alert('No existen impresiones pendientes');
            }
        }
    });
}

function fn_imprimirPreCuenta() {
    var odp_id = $("#hide_odp_id").val();
    var est_ipd = $("#hide_est_ip").val();
    var usr_id = $("#hide_usr_id").val();
    var rst_id = $("#hide_rst_id").val();
    send = {"impresionPrecuenta": 1};
    send.odp_id = odp_id;
    send.est_ipd = est_ipd;
    send.usr_id = usr_id;
    send.rst_id = rst_id;
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            if (datos[0]['Confirmar'] < 1) {
                alertify.alert('No existen detalle del pedido');
            }
        }
    });
}

function fn_cargarAccesosSistema() {
    var usr_id = $("#hide_usr_id").val();
    send = {"cargarAccesosPerfil": 1};
    send.pnt_id = 'tomapedido.php';
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                switch (datos[i]['acc_descripcion']) {
                    case 'Llevar':
                        $('#btn_salonLlevar').attr("disabled", false);
                        break;
                    case 'Cobrar':
                        $('#cobrar').attr("disabled", false);
                        $("#cobrar").removeClass("boton_Accion_Bloqueado");
                        $("#cobrar").addClass('boton_Accion');
                        $("#cobrar").removeClass("ok_bloqueado");
                        $("#cobrar").addClass('ok_activo');
                        break;
                    case 'Comentar':
                        $('#comentar').attr("disabled", false);
                        $("#comentar").removeClass("boton_Accion_Bloqueado");
                        $("#comentar").addClass('boton_Accion');
                        $("#comentar").removeClass("comentar_bloqueado");
                        $("#comentar").addClass('comentar_activo');
                        break;
                    case 'Eliminar':
                        $('#btn_eliminarElemnto').attr("disabled", false);
                        $("#btn_eliminarElemnto").removeClass("boton_Accion_Bloqueado");
                        $("#btn_eliminarElemnto").addClass('boton_Accion');
                        $("#btn_eliminarElemnto").removeClass("eliminar_bloqueado");
                        $("#btn_eliminarElemnto").addClass('eliminar_activo');
                        break;
                    case 'Cantidad':
                        $('#agregarCantidad').attr("disabled", false);
                        $("#agregarCantidad").removeClass("boton_Accion_Bloqueado");
                        $("#agregarCantidad").addClass('boton_Accion');
                        $("#agregarCantidad").removeClass("cantidad_bloqueado");
                        $("#agregarCantidad").addClass('cantidad_activo');
                        break;
                    case 'Funciones Gerente':
                        $('#funcionesGerente').attr("disabled", false);
                        $("#funcionesGerente").removeClass("boton_Opcion_Bloqueado");
                        $("#funcionesGerente").addClass('boton_Opcion');
                        $('#btn_transacciones').attr("disabled", false);
                        $("#btn_transacciones").removeClass("boton_Opcion_Bloqueado");
                        $("#btn_transacciones").addClass('boton_Opcion');
                        break;
                    case 'Separar':
                        $('#separarCuentas').attr("disabled", false);
                        $("#separarCuentas").removeClass("boton_Opcion_Bloqueado");
                        $("#separarCuentas").addClass('boton_Opcion');
                        break;
                    case 'Imprimir Pre-Cuenta':
                        $('#precuenta').attr("disabled", false);
                        $("#precuenta").removeClass("boton_Opcion_Bloqueado");
                        $("#precuenta").addClass('boton_Opcion');
                        break;
                    case 'Imprimir Orden Pedido':
                        $('#imprimir_orden').attr("disabled", false);
                        $("#imprimir_orden").removeClass("boton_Opcion_Bloqueado");
                        $("#imprimir_orden").addClass('boton_Opcion');
                        break;
                    case 'Guardar Cuenta':
                        $('#btn_sistema').attr("disabled", false);
                        $("#btn_sistema").removeClass("boton_Accion_Bloqueado");
                        $("#btn_sistema").addClass('boton_Accion');
                        $("#btn_sistema").removeClass("guardar_bloqueado");
                        $("#btn_sistema").addClass('guardar_activo');
                        break;
                    case 'Buscar':
                        $('#buscar').attr("disabled", false);
                        $("#buscar").removeClass("boton_Opcion_Bloqueado");
                        $("#buscar").addClass('boton_Opcion');
                        break;
                    case 'Salir':
                        $('#regresar').attr("disabled", false);
                        $("#regresar").removeClass("boton_Opcion_Bloqueado");
                        $("#regresar").addClass('boton_Opcion');
                        break;
                    case 'Resumen Ventas':
                        $('#resumenVentas').attr("disabled", false);
                        $("#resumenVentas").removeClass("boton_Opcion_Bloqueado");
                        $("#resumenVentas").addClass('boton_Opcion');
                        break;
                }
            }
            validaAdminBuscador();
        }
    });
}

function validaAdminBuscador()
{
    send = {"validaAdministradorBuscar": 1};
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "config_ordenPedido.php", data: send,
        success: function (datos)
        {
            if (datos.str > 0)
            {
                if (datos.valida == "admin")
                {
                    $('#buscar').attr("disabled", true);
                    $("#buscar").removeClass('boton_Opcion');
                    $("#buscar").addClass("boton_Opcion_Bloqueado");
                } else
                {

                }
            } else
            {

            }
        }});
}

function fn_cerrarModalBuscador() {
    $("#keyboard").hide();
    $("#keyboard").empty();
    $("#cuadro_buscador").dialog("close");
}

function fn_modalBuscador() {
    $("#txt_busca").val('');
    $("#cuadro_buscador").dialog({
        modal: true,
        autoOpen: false,
        position: {my: 'top', at: 'top'},
        show: {
            effect: "none",
            duration: 500
        },
        hide: {
            effect: "none",
            duration: 500
        },
        width: 800,
        height: 500,
        open: function (event, ui) {
            $(".ui-dialog-titlebar").hide();
            fn_alfaNumerico('#txt_busca');
            $('#btn_cancelar_teclado').attr('onclick', 'fn_cerrarModalBuscador()');
            $('#btn_ok_teclado').attr('onclick', 'fn_buscarPlusDescripcion()');
        }
    });
    $("#cuadro_buscador").dialog("open");

    $("#hide_pluId").val("");
    fn_focusLector();
}

function fn_buscarPlusDescripcion() {
    var html = "";
    var descripcion = $("#txt_busca").val();
    if (descripcion.length > 0) {
        $("#codigoCategoria").val(0);
        $("#txt_busca").val();
        if ($("#btn_salonLlevar").val() == 'Salon') {
            lc_control = 1;
        } else {
            lc_control = 2;
        }
        $("#agregarCantidad").show();
        send = {"cargarProductoBuscador": 1};
        send.menu_id = $('#hide_menu_id').val();
        send.cat_id = rst_categoria;
        send.mag_id = $("#codigoCategoria").val();
        send.cla_id = $('#hid_cla_id').val();
        send.descripcion = descripcion;
        $.getJSON("config_ordenPedido.php", send, function (datos) {
            if (datos.str > 0) {
                $("#buscaProducto").empty();
                for (i = 0; i < datos.str; i++) {
                    if (datos[i]['std_fecha'] < 1) {
                        if (datos[i]['validador'] == 1)
                            html += "<button id='btn_p" + datos[i]['magp_id'] + "' onclick='fn_verificarGramosPluBusqueda(\"" + datos[i]['magp_id'] + "\", " + datos[i]['plu_id'] + "," + datos[i]['validador'] + ")' style='position; left;width: 130px; font-size: 12px; background-color:" + datos[i]['magp_color'] + "; color:" + datos[i]['magp_colortexto'] + "; left:" + left + "px; top:" + arriba + "px;' class='producto_inactivo' pls_grms='" + datos[i]['plu_gramo'] + "' disabled>" + datos[i]['magp_desc_impresion'] + "</button>&nbsp;";
                        else
                            html += "<button id='btn_p" + datos[i]['magp_id'] + "' onclick='fn_verificarGramosPlu(\"" + datos[i]['magp_id'] + "\", " + datos[i]['plu_id'] + "," + datos[i]['validador'] + ")' style='position; left;width: 130px; font-size: 12px; background-color:" + datos[i]['magp_color'] + "; color:" + datos[i]['magp_colortexto'] + "; left:" + left + "px; top:" + arriba + "px;' class='producto_inactivo' pls_grms='" + datos[i]['plu_gramo'] + "' disabled>" + datos[i]['magp_desc_impresion'] + "</button>&nbsp;";
                    } else {
                        if (datos[i]['validador'] == 1)
                            html += "<button id='btn_p" + datos[i]['magp_id'] + "' onclick='fn_verificarGramosPluBusqueda(\"" + datos[i]['magp_id'] + "\", " + datos[i]['plu_id'] + "," + datos[i]['validador'] + ")' style='position; left;width: 130px; font-size: 12px;background-color:" + datos[i]['magp_color'] + "; color:" + datos[i]['magp_colortexto'] + "; left:" + left + "px; top:" + arriba + "px;' class='producto_activo' pls_grms='" + datos[i]['plu_gramo'] + "'>" + datos[i]['magp_desc_impresion'] + "</button>&nbsp;";
                        else
                            html += "<button id='btn_p" + datos[i]['magp_id'] + "' onclick='fn_verificarGramosPlu(\"" + datos[i]['magp_id'] + "\", " + datos[i]['plu_id'] + "," + datos[i]['validador'] + ")' style='position; left;width: 130px; font-size: 12px;background-color:" + datos[i]['magp_color'] + "; color:" + datos[i]['magp_colortexto'] + "; left:" + left + "px; top:" + arriba + "px;' class='producto_activo' pls_grms='" + datos[i]['plu_gramo'] + "'>" + datos[i]['magp_desc_impresion'] + "</button>&nbsp;";
                    }
                }
                $("#buscaProducto").html(html);
            } else {
                $("#buscaProducto").empty();
                alertify.alert('No se encontraron productos con esta descripci&oacute;n');
            }
        });
        fn_focusLector();
    } else {
        alertify.alert('Descripci&oacute;n no v&aacute;lida.');
    }
}

function fn_guardarCuenta() {
    var mesa = "";
    send = {"obtenerMesa": 1};
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            var mesa = datos[0]['mesa_asignada'];
            window.location.href = "../ordenpedido/tomaPedido.php?numMesa=" + mesa;
        }
    });
}

function fn_irTransacciones() {
    window.location.href = "../anulacion/anularOrden.php";
}

function fn_activarAsincronico(estado) {
    if (estado) {
        $.ajaxSetup({
            async: true
        });
    } else {
        $.ajaxSetup({
            async: false
        });
    }
}

function fn_cargando(std) {
    if (std > 0) {
        $("#mdl_rdn_pdd_crgnd").show();
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
    }
}
function corregirHora(i) {
    if (i < 10) {
        i = "0" + i
    }
    return i;
}
function corregirMinutos(i) {
    if (i < 10) {
        i = "0" + i;
    } else if (i == 60) {
        i = "00";
        m = 0;
        aumentarHora();
    }
    return i;
}
function aumentarHora() {
    if (h < 23) {
        h++;
    } else {
        h = 0;
    }
}
function aumentarMinuto() {
    m++;
}
/*
 function crearReloj() {
 var minutos = corregirMinutos(m);
 var horas = corregirHora(h);
 $("#nfrmcn_srs_sstm_hora").html(horas + ":" + minutos);
 var t = setTimeout(function () {
 aumentarMinuto();
 crearReloj();
 }, 60000);
 }*/
function fn_actualizar() {

    for (var x in jQuery.cache) {
        delete jQuery.cache[x];
    }
    location.reload();
}