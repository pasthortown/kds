/* global alertify, moment */

// JavaScript Document
////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
////////DESCRIPCION: ADMINISTRACION DE USUARIOS POR TIENDA, CREACION DE PERFILES CAJEROS ///////////////
////////TABLAS: Users_Pos, Perfil_Pos //////////////////////////////////////////////////////////////////
////////FECHA CREACION: 27/07/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
////////MODIFICADO POR: Juan Esteban Canelos ///////////////////////////////////////////////////////////
////////FECHA MODIFICACION: 06/04/2018 /////////////////////////////////////////////////////////////////
////////DESCRIPCION: campo tarjeta //////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

var accion = 0;
var slccn_usr_rst = '';
var IDRESTAUTANTE = '';

$(document).ready(function () {
//    fn_btn('agregar',1);
//    fn_btn('restablecer',1);
    fn_btn('restablecer', 1, 'botonMnSpr l-basic-lock-open');
    fn_btn('agregar', 1, 'botonMnSpr l-basic-elaboration-document-plus');
    fn_cargarUsuariosTiendaInactivos(52);
    fn_cargarLocales(0);
    fn_cargarPerfiles();

    $('#usr_inicio').daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $('#usr_fin').daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $("#usr_sw_tipodocumento").bootstrapSwitch('state', true);
    $('#usr_sw_tipodocumento').on('switchChange.bootstrapSwitch', function () {
        var estado = $("#usr_sw_tipodocumento").bootstrapSwitch('state');

        if (estado === true) {
            $('#usr_cedula').val('');
        } else {
            $('#usr_cedula').val('');
        }
    });
    
    $("#usr_tarjeta").click(function() {
        $(this).focus();
        $(this).select();
    });
});

function fn_OpcionSeleccionada(ls_opcion) {
    if (ls_opcion == 'Todos') {
        fn_cargarUsuariosTienda();
    } else if (ls_opcion == 'Activos') {
        accion = 52;
        fn_cargarUsuariosTiendaInactivos(accion);
    } else if (ls_opcion == 'Inactivos') {
        accion = 25;
        fn_cargarUsuariosTiendaInactivos(accion);
    }
}

function fn_OpcionSeleccionadaModInsert() {
    var resultado;
    var ls_opcion = '';
    ls_opcion = $(":input[name=estados]:checked").val();

    if (ls_opcion == 'Todos') {
        fn_cargarUsuariosTienda();
    } else if (ls_opcion == 'Activos') {
        resultado = 52;
        fn_cargarUsuariosTiendaInactivos(resultado);
    } else if (ls_opcion == 'Inactivos') {
        resultado = 25;
        fn_cargarUsuariosTiendaInactivos(resultado);
    }
}

function fn_alerta(mensaje, tipo) {
    setTimeout(function () {
        $.bootstrapGrowl(mensaje, {
            type: tipo,
            align: 'right',
            width: '500',
            allow_dismiss: false
        });
    }, 100);
}
/*===================================================*/
/*FUNCION PARA TRAER LOS BOTONES DE ADMINISTRACION   */
/*===================================================*/
function fn_btn(boton, estado, estilo) {
    if (estado) {
        //$("#btn_"+boton).css("background"," url('../../imagenes/admin_resources/"+boton+".png') 14px 4px no-repeat");
        $("#btn_" + boton).removeAttr("disabled");
        $("#btn_" + boton).addClass("botonhabilitado " + estilo);
        $("#btn_" + boton).removeClass("botonbloqueado");
    } else {
        $("#btn_" + boton).css("background", " url('../../imagenes/admin_resources/" + boton + "_bloqueado.png') 14px 4px no-repeat");
        $("#btn_" + boton).prop('disabled', true);
        $("#btn_" + boton).addClass("botonbloqueado");
        $("#btn_" + boton).removeClass("botonhabilitado");
    }
}

/*==========================================================*/
/*FUNCION PARA CARGAR AL INICIO TABLA DE USUARIOS           */
/*==========================================================*/
function fn_cargarUsuariosTienda() {
    var administracionUsuariosTienda = {"administracionUsuariosTienda": 1};
    var html = '<thead><tr class="active"><th class="text-center">Descripci&oacute;n</th><th class="text-center">Nombre MaxPoint</th><th class="text-center">Usuario</th><th class="text-center">Iniciales</th><th class="text-center">Perfil</th><th class="text-center">Restaurante</th><th class="text-center">Fecha Inicio</th><th class="text-center">Fecha Fin</th><th class="text-center">Activo</th></tr></thead>';
    var send;
    send = administracionUsuariosTienda;
    send.accion = 1;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminUsuariosTienda/config_adminusuariostienda.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                $('#usuariostienda').show();
                for (var i = 0; i < datos.str; i++) {
                    html = html + "<tr id='usr_" + datos[i]['usr_id'] + "' onclick='fn_seleccion(\"" + datos[i]['usr_id'] + "\",\"" + datos[i]['usr_id'] + "\");' ondblclick='fn_modificar(" + i + ",\"" + datos[i]['usr_id'] + "\")'><td>" + datos[i]['usr_descripcion'] + "</td><td>" + datos[i]['usr_nombre_en_pos'] + "</td><td>" + datos[i]['usr_usuario'] + "</td><td>" + datos[i]['usr_iniciales'] + "</td><td>" + datos[i]['prf_descripcion'] + "</td><td class='text-center'> " + datos[i]['RestauranteAsignado'] + "</td><td>" + datos[i]['usr_fecha_ingreso'] + "</td><td>" + datos[i]['usr_fecha_salida'] + "</td>";
                    if (datos[i]['std_id'] === 52 || datos[i]['std_id'] === 68) {
                        html += "<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";
                    }
                    if (datos[i]['std_id'] === 25) {
                        html += "<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";
                    }
                }
                $('#tabla_usuariostienda').html(html);
                $('#tabla_usuariostienda').dataTable({'destroy': true});
                $("#tabla_usuariostienda_length").hide();
                $("#tabla_usuariostienda_paginate").addClass('col-xs-10');
                $("#tabla_usuariostienda_info").addClass('col-xs-12');
                $("#tabla_usuariostienda_length").addClass('col-xs-6');
            } else {
                alertify.error("No existen datos para esta cadena.");
                $("#botonesTodos").hide();
                $('#usuariostienda').hide();
                $("#tabla_usuariostienda").html(html);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

/*========================================================*/
/*FUNCION PARA CARGAR TABLA INICIO DE ACTIVOS E INACTIVOS */
/*========================================================*/
function fn_cargarUsuariosTiendaInactivos(accion) {
    var administracionUsuariosTienda = {"administracionUsuariosTienda": 1};
    var html = '<thead><tr class="active"><th class="text-center">Descripci&oacute;n</th><th class="text-center">Nombre MaxPoint</th><th class="text-center">Usuario</th><th class="text-center">Iniciales</th><th class="text-center">Perfil</th><th class="text-center">Restaurante</th><th class="text-center">Fecha Inicio</th><th class="text-center">Fecha Fin</th><th class="text-center">Activo</th></tr></thead>';
    var send;
    send = administracionUsuariosTienda;
    send.accion = accion;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminUsuariosTienda/config_adminusuariostienda.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                $('#usuariostienda').show();
                for (var i = 0; i < datos.str; i++) {
                    html = html + "<tr id='usr_" + datos[i]['usr_id'] + "' onclick='fn_seleccion(\"" + datos[i]['usr_id'] + "\",\"" + datos[i]['usr_id'] + "\");' ondblclick='fn_modificar(" + i + ",\"" + datos[i]['usr_id'] + "\")'><td>" + datos[i]['usr_descripcion'] + "</td><td>" + datos[i]['usr_nombre_en_pos'] + "</td><td>" + datos[i]['usr_usuario'] + "</td><td>" + datos[i]['usr_iniciales'] + "</td><td>" + datos[i]['prf_descripcion'] + "</td><td>" + datos[i]['RestauranteAsignado'] + "</td><td>" + datos[i]['usr_fecha_ingreso'] + "</td><td>" + datos[i]['usr_fecha_salida'] + "</td>";
                    if (datos[i]['std_id'] === 52 || datos[i]['std_id'] === 68) {
                        html += "<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";
                    }
                    if (datos[i]['std_id'] === 25) {
                        html += "<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";
                    }
                }
                $('#tabla_usuariostienda').html(html);
                $('#tabla_usuariostienda').dataTable({'destroy': true});
                $("#tabla_usuariostienda_length").hide();
                $("#tabla_usuariostienda_paginate").addClass('col-xs-10');
                $("#tabla_usuariostienda_info").addClass('col-xs-12');
                $("#tabla_usuariostienda_length").addClass('col-xs-6');
            } else {
                alertify.error("No existen datos para esta cadena.");
                $('#usuariostienda').hide();
                html += "<tr>";
                html += "<td colspan='6'>No existen datos.</td></tr>";
                $("#tabla_usuariostienda").html(html);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_setearListaRestaurantes() {
    $(":input[name=chck_usr_rst]:checked").each(function () {
        $("#ckbx_rst_id_" + $(this).val()).prop('checked', false);
    });
}

function fn_verificarUsuario() {
    var descripcion = $('#usr_descripcion').val();
    var send;
    if (descripcion.length > 5) {
        descripcion = descripcion.toLowerCase();
        var iniciales = '';
        var usuario = '';
        var indice = 1;
        var continuar = true;
        iniciales = descripcion.substring(0, 1) + descripcion.substring(descripcion.indexOf(' ') + 1, descripcion.indexOf(' ') + 2);
        iniciales = iniciales.toUpperCase();
        $.ajaxSetup({async: false});
        while (continuar) {
            usuario = descripcion.substring(0, indice) + descripcion.substring(descripcion.indexOf(' ') + 1, descripcion.length);
            if (usuario.indexOf(' ') > 0) {
                usuario = usuario.substring(0, usuario.indexOf(' '));
            }
            accion = 4;
            send = {"verificarUsuarioSistema": 1};
            send.usuario = usuario;
            send.accion = accion;
            $.getJSON("../adminUsuariosTienda/config_adminusuariostienda.php", send, function (datos) {
                if (datos.str > 0) {
                    if (datos[0]['Continuar'] > 0) {
                        $('#usr_iniciales').val(iniciales);
                        $('#usr_sistema').val(usuario);
                        $('#usr_descripcion').val(descripcion.toUpperCase());
                        continuar = false;
                    } else {
                        if (indice > 5) {
                            alertify.error("Usuario no disponible, cambie el nombre del usuario.");
                            continuar = false;
                        }
                    }
                }
            });
            indice++;
        }
        $.ajaxSetup({async: true});
    }
}

function fn_agregar() {
    //$('#advertenciaUsuario').hide();
    slccn_usr_rst = '';
    fn_setearListaRestaurantes();
    Grabar = 'Grabar';
    $("#pcns_rst_lclzcn label").removeClass("active");
    $('#pcn_rst_lclzcn1').addClass('active');
    fn_cargarLocales(0);
    $('#tqt_mdl_usr').html("Nuevo Cajero/a");
    $("#pestanas li").removeClass("active");
    $('#tag_inicio').addClass('active');
    $("#pst_cnt div").removeClass("active");
    $('#inicio').addClass('active');
    $('#btn_pcn_dmn').html("<button type='button' onclick='fn_validaDocumentoRepetido(" + Grabar + ");' class='btn btn-primary'>Aceptar</button><button type='button' class='btn btn-default' data-dismiss='modal'>Cancelar</button>");
    $('#hdn_usr_id').val("0");
    $('#usr_std_id').prop('checked', true);
    $('#usr_descripcion').val('');
    $('#usr_clave').val('');
    $('#usr_direccion').val('');
    $('#usr_cedula').val('');
    $('#usr_iniciales').val('');
    $('#usr_email').val('');
    $('#usr_telefono').val('');
    $('#usr_sistema').val('');
    $('#usr_prf_id').val(0);
    $('#usr_dscrp_mxpnt').val('');
    $('#usr_inicio').val(moment().format('DD/MM/YYYY'));
    $('#usr_fin').val('01/01/6000');
    $('#usr_tarjeta').val('');
    $('#mdl_usr').modal('show');
    $("#usr_inicio").prop('disabled', true);
    $("#usr_sistema").prop('disabled', false);
    $("#usr_descripcion").prop('disabled', false);
    $("#usr_cedula").prop('disabled', false);
    $("#usr_clave").prop('disabled', false);
}

// BLOQUEA LA TECLA BACKSPACE
function ValidarNumero(e, campo) {
    var key = e.keyCode ? e.keyCode : e.which;
    if (key === 32) {
        return false;
    }
}

function fn_cargarLocalesUsuario(usr_id) {
    var send;
    send = {"cargarLocalesUsuario": 1};
    send.resultado = 4;
    send.usr_id = usr_id;
    send.cdn_id = $('#sess_cdn_id').val();
    send.std_id = 0;
    $.getJSON("../adminUsuariosTienda/config_adminusuariostienda.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                slccn_usr_rst = slccn_usr_rst + datos[i]['rst_id'] + '_';
                $("#ckbx_rst_id_" + datos[i]['rst_id']).prop('checked', 'checked');
            }
        }
    });
}

function fn_cargarLocales(std_id) {
    var html = '';
    var send;
    send = {"cargarLocales": 1};
    send.accion = 2;
    send.std_id = std_id;
    $.getJSON("../adminUsuariosTienda/config_adminusuariostienda.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                html = html + '<a class="list-group-item"><input id="ckbx_rst_id_' + datos[i]['rst_id'] + '" checked="checked" name="chck_rst_id[]" onclick="fn_seleccionarLocal(' + datos[i]['rst_id'] + ');" value="' + datos[i]['rst_id'] + '" type="radio">&nbsp; ' + datos[i]['rst_descripcion'] + '</a>';
            }
            $("#lst_rst_usr").html(html);
            fn_marcarLocalesSeleccionados();
        } else {
            html = html + '<a class="list-group-item">"No existen Tiendas"</a>';
            $("#lst_rst_usr").html(html);
        }
    });
}

function fn_seleccionarLocal(rst_id) {
    IDRESTAUTANTE = rst_id;
}

/*===================================================================================*/
/*FUNCION PARA GUARDAR LAS TIENDAS SELECCIONADAS EN LA TABLA RESTAURANTE ATRIBUTOS   */
/*===================================================================================*/
function fn_guardaUserRestaurante() {
    var rst_id = IDRESTAUTANTE;
    var send;
    send = {"guardaUserRestaurante": 1};
    send.accion = 1;
    send.rst_id = rst_id;
    send.usr_id = 0;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminUsuariosTienda/config_adminusuariostienda.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                }
            }
        }
    });
}

function fn_guardaUserRestauranteMod() {
    var rst_id = IDRESTAUTANTE;
    var send;
    var usr_id = $("#txt_idusuario").val();
    send = {"guardaUserRestaurante": 1};
    send.accion = 2;
    send.rst_id = rst_id;
    send.usr_id = usr_id;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminUsuariosTienda/config_adminusuariostienda.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                }
            }
        }
    });
}

function fn_marcarLocalesSeleccionados() {
    var argumento = slccn_usr_rst.split('_');
    for (var i = 0; i < argumento.length; i++) {
        $("#ckbx_rst_id_" + argumento[i]).prop('checked', 'checked');
    }
}

function fn_seleccion(fila, codigo) {
    $("#tabla_usuariostienda tr").removeClass("success");
    $("#usr_" + fila + "").addClass("success");
    $("#txt_idusuario").val(codigo);
}

/*====================================================================*/
/*FUNCION PARA ACCIONAR SI ES NUEVO O MODIFICAR Y LLAMAR A LA MODAL   */
/*====================================================================*/
function fn_accionar(accion) {
    if (accion == 'Nuevo') {
        $('#advertenciaUsuario').hide();
        fn_agregar();
        fn_cargarPerfiles();
        $('#usr_descripcion').val('');
        $('#txt_nombre').val('');
        $('#txt_tipoimpresora').val('');
        lc_control = 2;
        $('#modal_user').modal('show');
        $('#modal_user').on('shown.bs.modal', function () {
            $("#usr_descripcion").focus();
        });
    } else if (accion == 'Grabar') {
        if (lc_control == 2) {
            grabarUsuario();
        } else if (lc_control == 3) {
            fn_guardarUsuarioMod();
        }
    }
}

function fn_modificar(fila, usr_id) {
    $('#advertenciaUsuario').hide();
    fn_setearListaRestaurantes();
    slccn_usr_rst = '';
    $("#txt_idusuario").val(usr_id);
    $('#modal_user').modal('show');
    $('#usr_cedula').val('');
    $('#usr_tarjeta').val('');
    Grabar = 'Grabar';
    $("#pcns_rst_lclzcn label").removeClass("active");
    $('#pcn_rst_lclzcn1').addClass('active');
    $("#usr_clave").prop('disabled', true);
    fn_cargarLocales(0);
    fn_traerDatosUsuario(usr_id);
    lc_control = 3;
    $('#btn_pcn_dmn').html("<button type='button' onclick='fn_accionar(" + Grabar + ");' class='btn btn-primary'>Aceptar</button><button type='button' class='btn btn-default' data-dismiss='modal'>Cancelar</button>");
}

function fn_restablecer() {
    var usuario = '';
    usuario = $("#txt_idusuario").val();
    if (usuario == '') {
        alertify.error("Debe seleccionar un usuario.");
    } else {
        fn_cargando(1);
        var usr_id = '';
        usr_id = usuario;
        //var usr_clave = $("#usr_clave_cambio").val();
        var send;
        send = {"restablecerClaveUsuario": 1};
        send.accion = 2;
        send.usr_id = usr_id;
        //send.usr_clave = usr_clave;
        $.getJSON("../adminUsuariosTienda/config_adminusuariostienda.php", send, function (datos) {
            if (datos.str > 0) {
                if (datos.existe == 0) {
                    alertify.success("Clave Restablecida.");
                    //$('#modal_cambioclave').modal('hide');
                    fn_cargando(0);
                } else {
                    //alertify.error("La Clave de Usuario ya existe ingrese otra!!");
                    alertify.error("La Clave de no pudo ser modificada");
                    fn_cargando(0);
                    return false;
                }
            } else {
                alert("Error");
                fn_cargando(0);
            }
        });
    }
}

function fn_modalCambioClave() {
    var usr_id = '';
    usr_id = $("#txt_idusuario").val();
    $("#usr_clave_cambio").val('');
    if (usr_id) {
        $('#modal_cambioclave').modal('show');
        $('#modal_cambioclave').on('shown.bs.modal', function () {
            $("#usr_clave_cambio").focus();
        });
    } else {
        alertify.error("Debe seleccionar un cajero/a.");
    }
}

function fn_traerDatosUsuario(usr_id) {
    $("#usr_cedula").attr('disabled', 'disabled');
    $("#usr_iniciales").attr('disabled', 'disabled');
    $("#usr_sistema").attr('disabled', 'disabled');
    var send;
    send = {"traerDatosUsuario": 1};
    send.accion = 6;
    send.usr_id = usr_id;
    $.getJSON("../adminUsuariosTienda/config_adminusuariostienda.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                var documento = (datos[i]['usr_cedula']);
                $('#tqt_mdl_usr').html(datos[i]['usr_descripcion']);
                $('#usr_descripcion').val(datos[i]['usr_descripcion']);
                $("#usr_cedula").val(datos[i]['usr_cedula']);
                $('#usr_direccion').val(datos[i]['usr_direccion']);
                $('#usr_iniciales').val(datos[i]['usr_iniciales']);
                $('#usr_email').val(datos[i]['usr_email']);
                $('#usr_telefono').val(datos[i]['usr_telefono']);
                $('#usr_sistema').val(datos[i]['usr_usuario']);
                $('#usr_dscrp_mxpnt').val(datos[i]['usr_nombre_en_pos']);
                $('#usr_inicio').val(datos[i]['usr_fecha_ingreso']);
                $('#usr_fin').val(datos[i]['usr_fecha_salida']);
                $('#usr_tarjeta').val((datos[i]['usr_tarjeta'] === '-') ? '' : datos[i]['usr_tarjeta']);
                $('#usr_prf_id').val(datos[i]['prf_id']);
                $("#usr_std_id").empty();
                if (datos[i]['std_id'] == 52) {
                    $("#usr_std_id").prop("checked", true);
                }
                if (datos[i]['std_id'] == 25) {
                    $("#usr_std_id").prop("checked", false);
                }
                if (fn_validarDocumento(documento)) {
                    $("#usr_sw_tipodocumento").bootstrapSwitch('state', true);
                } else {
                    $("#usr_sw_tipodocumento").bootstrapSwitch('state', false);
                }
            }
        }
    });
    fn_checkRestauranteUser(usr_id);
}

function fn_checkRestauranteUser(usr_id) {
    var send;
    send = {"traerRestauranteUser": 1};
    send.accion = 7;
    send.usr_id = usr_id;
    $.getJSON("../adminUsuariosTienda/config_adminusuariostienda.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                slccn_usr_rst = slccn_usr_rst + datos[i]['rst_id'] + '_';
                $("#ckbx_rst_id_" + datos[i]['rst_id']).prop('checked', 'checked');
            }
        }
    });
}

function fn_cargarPerfiles() {
    var html = '<option value="0">-- Seleccionar Perfil --</option>';
    var send;
    send = {"cargarPerfiles": 1};
    send.accion = 3;
    send.std_id = 0;
    $.getJSON("../adminUsuariosTienda/config_adminusuariostienda.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                html = html + '<option value="' + datos[i]['prf_id'] + '">' + datos[i]['prf_descripcion'] + '</option>';
            }
            $('#usr_prf_id').html(html);
        }
    });
}

function fn_guardarUsuarioMod() {
    //$('#advertenciaUsuario').hide();
    fn_cargando(1);
    var usr_id = $('#hdn_usr_id').val();
    var usr_descripcion = $('#usr_descripcion').val();
    var usr_direccion = $('#usr_direccion').val();
    var usr_iniciales = $('#usr_iniciales').val();
    var usr_email = $('#usr_email').val();
    var usr_telefono = $('#usr_telefono').val();
    var usr_usuario = $('#usr_sistema').val();
    var prf_id = $('#usr_prf_id').val();
    var usr_nombre_en_pos = $('#usr_dscrp_mxpnt').val();
    var usr_fecha_ingreso = $('#usr_inicio').val();
    var usr_fecha_salida = $('#usr_fin').val();
    var usr_tarjeta = $('#usr_tarjeta').val();
    var usr_cedula = $('#usr_cedula').val();
    //var usr_clave = $('#usr_clave').val();
    var usr_clave = $('#usr_cedula').val();
    var std_id = 25;
    if ($('#usr_std_id').is(':checked')) {
        std_id = 52;
    }

    if (usr_descripcion === '') {
        $('#advertenciaUsuario').show();
        alertify.error("<b>Alerta!</b> Nombre y Apellido es un campo obligatorio.");
        fn_cargando(0);
        return false;
    } else if (usr_cedula === '') {
        $('#advertenciaUsuario').show();
        alertify.error("<b>Alerta!</b> C&eacute;dula campo obligatorio.");
        fn_cargando(0);
        return false;
    } else if (prf_id == "0") {
        $('#advertenciaUsuario').show();
        alertify.error("<b>Alerta!</b> Perfil campo obligatorio.");
        fn_cargando(0);
        return false;
    } else if (usr_nombre_en_pos === '') {
        $('#advertenciaUsuario').show();
        alertify.error("<b>Alerta!</b> Nombre MaxPoint campo obligatorio.");
        fn_cargando(0);
        return false;
    } else if (usr_fecha_salida === '') {
        $('#advertenciaUsuario').show();
        alertify.error("<b>Alerta!</b> Fecha Fin campo obligatorio.");
        fn_cargando(0);
        return false;
    }

    usr_id = $("#txt_idusuario").val();
    fn_guardaUsuarioCajeroModificado(usr_id, prf_id, std_id, usr_nombre_en_pos, usr_usuario, usr_iniciales, usr_descripcion, usr_tarjeta, usr_fecha_ingreso, usr_fecha_salida, usr_telefono, usr_email, usr_direccion, usr_cedula, usr_clave);
}

function fn_guardaUsuarioCajeroModificado(usr_id, prf_id, std_id, usr_nombre_en_pos, usr_usuario, usr_iniciales, usr_descripcion, usr_tarjeta, usr_fecha_ingreso, usr_fecha_salida, usr_telefono, usr_email, usr_direccion, usr_cedula, usr_clave) {
    var send;
    send = {"guardarUsuarioMod": 1};
    send.accion = 'm';
    send.usr_id = usr_id;
    send.prf_id = prf_id;
    send.std_id = std_id;
    send.usr_nombre_en_pos = usr_nombre_en_pos;
    send.usr_usuario = usr_usuario;
    send.usr_iniciales = usr_iniciales;
    send.usr_descripcion = usr_descripcion;
    send.usr_tarjeta = usr_tarjeta;
    send.usr_fecha_ingreso = usr_fecha_ingreso;
    send.usr_fecha_salida = usr_fecha_salida;
    send.usr_telefono = usr_telefono;
    send.usr_email = usr_email;
    send.usr_direccion = usr_direccion;
    send.usr_rst = 0;
    send.usr_cedula = usr_cedula;
//    if(usr_clave == ''){usr_clave = 0;}
//    send.usr_clave = usr_clave;
    send.usr_clave = usr_cedula;
    $.getJSON("../adminUsuariosTienda/config_adminusuariostienda.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                if (datos[i]['existe'] == 0 || datos[i]['existe'] == 3) {
                    alertify.success("Usuario actualizado correctamente.");
                    fn_guardaUserRestauranteMod();
                    $('#modal_user').modal('hide');
                    fn_OpcionSeleccionadaModInsert();
                    fn_setearListaRestaurantes();
                    fn_cargando(0);
                } else {
                    alertify.error("<b>Alerta!</b> La Clave de Usuario ya existe, ingrese otra!!");
                    fn_cargando(0);
                    return false;
                }
            }
        }
    });
}

function validaPais() {
    var send;
    send = {"validarPais": 1};
    send.accion = 5;
    $.getJSON("../adminUsuariosTienda/config_adminusuariostienda.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                if (datos[i]['Pais'] == 'ECUADOR') {
                }
            }
        }
    });
}

function grabarUsuario() {
    $('#advertenciaUsuario').hide();
    fn_cargando(1);
    var slccn_usr_rst = '';
    var usr_descripcion = $('#usr_descripcion').val();
    var usr_direccion = $('#usr_direccion').val();
    var usr_iniciales = $('#usr_iniciales').val();
    var usr_email = $('#usr_email').val();
    var usr_telefono = $('#usr_telefono').val();
    var usr_usuario = $('#usr_sistema').val();
    var prf_id = $('#usr_prf_id').val();
    var usr_nombre_en_pos = $('#usr_dscrp_mxpnt').val();
    var usr_fecha_ingreso = $('#usr_inicio').val();
    var usr_fecha_salida = $('#usr_fin').val();
    var usr_tarjeta = $('#usr_tarjeta').val();
    var usr_cedula = $('#usr_cedula').val();
    //var usr_clave = $('#usr_clave').val();
    var usr_clave = $('#usr_cedula').val();
    var std_id = 25;
    if ($('#usr_std_id').is(':checked')) {
        std_id = 52;
    }

    if (prf_id == 0) {
        $('#advertenciaUsuario').show();
        alertify.error("<b>Alerta!</b> Perfil campo obligatorio.");
        fn_cargando(0);
        return false;
    } else if (usr_nombre_en_pos === '') {
        $('#advertenciaUsuario').show();
        alertify.error("<b>Alerta!</b> Nombre MaxPoint campo obligatorio.");
        fn_cargando(0);
        return false;
    }
//    else if(usr_clave === ''){
//        alertify.error("Clave campo obligatorio.");
//        fn_cargando(0);
//        return false;
//    } 
    else if (usr_fecha_ingreso === '') {
        $('#advertenciaUsuario').show();
        alertify.error("<b>Alerta!</b> Fecha Inicio campo obligatorio.");
        fn_cargando(0);
        return false;
    } else if (usr_fecha_salida === '') {
        $('#advertenciaUsuario').show();
        alertify.error("<b>Alerta!</b> Fecha Fin campo obligatorio.");
        fn_cargando(0);
        return false;
    } else if (usr_email != '') {
        var regex = /[\w-\.]{2,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
        if (regex.test(usr_email.trim())) {
        } else {
            alertify.error('<b>Alerta!</b> La direccion de correo no es valida');
            fn_cargando(0);
            return false;
        }
    }
    var rst_id = new Array();
    $('input[name="chck_rst_id[]"]:checked').each(function () {
        rst_id.push($(this).val());
    });

    fn_grabaUsuarioCajero(prf_id, std_id, usr_nombre_en_pos, usr_usuario, usr_iniciales, usr_descripcion, usr_tarjeta, usr_fecha_ingreso, usr_fecha_salida, usr_telefono, usr_email, usr_direccion, slccn_usr_rst, usr_cedula, usr_clave, rst_id);
}

function fn_grabaUsuarioCajero(prf_id, std_id, usr_nombre_en_pos, usr_usuario, usr_iniciales, usr_descripcion, usr_tarjeta, usr_fecha_ingreso, usr_fecha_salida, usr_telefono, usr_email, usr_direccion, slccn_usr_rst, usr_cedula, usr_clave, rst_id) {
    var send;
    send = {"guardarUsuario": 1};
    send.accion = 'i';
    send.prf_id = prf_id;
    send.std_id = std_id;
    send.usr_nombre_en_pos = usr_nombre_en_pos;
    send.usr_usuario = usr_usuario;
    send.usr_iniciales = usr_iniciales;
    send.usr_descripcion = usr_descripcion;
    send.usr_tarjeta = usr_tarjeta;
    send.usr_fecha_ingreso = usr_fecha_ingreso;
    send.usr_fecha_salida = usr_fecha_salida;
    send.usr_telefono = usr_telefono;
    send.usr_email = usr_email;
    send.usr_direccion = usr_direccion;
    send.usr_rst = slccn_usr_rst;
    send.usr_cedula = usr_cedula;
    //send.usr_clave = usr_clave;
    //se envia la misma cedula ya que esa es la clave
    send.usr_clave = usr_cedula;
    send.rst_id = rst_id;
    $.getJSON("../adminUsuariosTienda/config_adminusuariostienda.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
//                if(datos[i]['existe'] === 1){
//                    alertify.error("<b>Alerta!</b> La Clave de Usuario ya existe ingrese otra!!");
//                    $('#usr_clave').val('');
//                    fn_cargando(0);
//                    return false;
//                } else {
                alertify.success("Datos guardados correctamente.");
                fn_guardaUserRestaurante();
                $('#modal_user').modal('hide');
                $('#usr_clave').val('');
                fn_OpcionSeleccionadaModInsert();
                fn_setearListaRestaurantes();
                fn_cargando(0);
                //}
            }
        }
    });
}

function validar(e) {
    var tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla == 8)
        return true; // 3
    var patron = /[A-Za-z\s]/; // 4
    var te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
}

//El boton desencadena la accion
function fn_validarCorreo() {
    //Utilizamos una expresion regular
    var regex = /[\w-\.]{2,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
    //Se utiliza la funcion test() nativa de JavaScript
    if (regex.test($('#email').val().trim())) {
        alert('Correo validado');
    } else {
        alert('La direccion de correo no es valida');
        return false;
    }
}

/* Verifica que el nÚmero de documento ingresado no exista en la BD */
function  fn_validaDocumentoRepetido(opcion) {
    var Accion = 'VD';
    var documento = $('#usr_cedula').val();
    var usuario = $('#usr_sistema').val();
    var send;
    send = {"ValidaDocumento": 1};
    send.accion = Accion;
    send.documento = documento;
    send.usuario = usuario;
    $.getJSON("../adminUsuario/config_usuarios.php", send, function (datos) {
        if (datos.str > 0) {
            if (datos[0]['continuar'] === 1) {
                //alertify.error("<b>Alerta!</b> El Documento de identificación ingresado ya existe.");
                alertify.alert("<b>Alerta!</b> El Documento de identificación ingresado ya existe.<br>" +
                        '<b>El usuario es:</b> ' + datos[0]['usuario'] +
                        '<b> Su perfil es:</b> ' + datos[0]['perfil'] +
                        '<b> Está en la tienda:</b> ' + datos[0]['tienda']);
//                $('#modal_user').on('shown.bs.modal', function () {
//                    $('#usr_cedula').focus();
//                });
                return false;
            } else {
                fn_validaUsuario(opcion, documento);
            }
        }
    });
}

/* Valida que el nombre de usuario no se repita */
function  fn_validaUsuario(opcion, documento) {
    var Accion = 'VU';
    var usuario = $('#usr_sistema').val();
    var send;
    send = {"ValidaUsuario": 1};
    send.accion = Accion;
    send.documento = documento;
    send.usuario = usuario;
    $.getJSON("../adminUsuario/config_usuarios.php", send, function (datos) {
        if (datos.str > 0) {
            if (datos[0]['continuar'] === 1) {
                alertify.error("<b>Alerta!</b> El nombre de usuario ya existe, proceda a modificarlo.");
                return false;
            } else {
                fn_tipoDocumento(opcion);
            }
        }
    });
}

/* Verificamos si el tipo de documento es cédula o pasaporte */
function fn_tipoDocumento(opcion) {
    var estadosw = $('#usr_sw_tipodocumento').bootstrapSwitch('state');
    var documento = $("#usr_cedula").val();
    var descripcion = $("#usr_descripcion").val();

    if (estadosw === true) {
        if (fn_validarCampoVacio(descripcion)) {
            if (fn_validarCampoVacio(documento)) {
                if (fn_validarDocumento(documento)) {
                    fn_accionar(opcion);
                } else {
                    $('#advertenciaUsuario').show();
                    alertify.error("<b>Alerta!</b> Cédula incorrecta.");
                }
            } else {
                $('#advertenciaUsuario').show();
                alertify.error("<b>Alerta!</b> Cédula es obligatorio.");
            }
        } else {
            $('#advertenciaUsuario').show();
            alertify.error("<b>Alerta!</b> Nombre y Apellido son obligatorios.");
        }
    } else {
        fn_accionar(opcion);
    }
}

function fn_validarCampoVacio(valor) {
    if (valor.length > 0) {
        return true;
    } else {
        return false;
    }
}