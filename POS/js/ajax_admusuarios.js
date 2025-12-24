/* global alertify, moment */

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco 
///////DESCRIPCION: Pantalla de configuración de usuarios 
///////FECHA CREACION: 28-01-2016 
///////MODIFICADO POR: Daniel Llerena
///////DESCRIPCION: Validacion de cedula y que no se repita
///////FECHA MODIFICACION: 11/07/2016
///////MODIFICADO POR: Juan Esteban Canelos
///////DESCRIPCION: lectura y escritura de campo tarjeta
///////FECHA MODIFICACION: 09/04/2018
///////////////////////////////////////////////////////////////////////////////

var slccn_usr_rst = '';

$(document).ready(function () {
    fn_btn('restablecer', 1, 'botonMnSpr l-basic-lock-open');
    fn_btn('agregar', 1, 'botonMnSpr l-basic-elaboration-document-plus');

    $('#usr_inicio').daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $('#usr_fin').daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });


    fn_cargarUsuarios(1, 'Activo');
    fn_cargarPerfiles();
    fn_listarRegiones();
    fn_cargarLocales('Todos');

    $("#usr_sw_tipodocumento").bootstrapSwitch('state', true);
    $('#usr_sw_tipodocumento').on('switchChange.bootstrapSwitch', function () {
        var estado = $("#usr_sw_tipodocumento").bootstrapSwitch('state');

        $('#usr_cedula').val('');
    });

    $("#usr_tarjeta").click(function () {
        $(this).focus();
        $(this).select();
    });
});

function fn_seleccionarTodosLocales() {
    if ($("#chck_rst_tds").is(':checked')) {
        $(":input[name=chck_usr_rst]").each(function () {
            if (slccn_usr_rst.indexOf($(this).val() + '_') < 0) {
                slccn_usr_rst = slccn_usr_rst + $(this).val() + '_';
            }
            $("#ckbx_rst_id_" + $(this).val()).prop('checked', 'checked');
        });
    } else {
        $(":input[name=chck_usr_rst]").each(function () {
            if (slccn_usr_rst.indexOf($(this).val() + '_') >= 0) {
                slccn_usr_rst = slccn_usr_rst.replace($(this).val() + '_', '');
            }
            $("#ckbx_rst_id_" + $(this).val()).prop('checked', false);
        });
    }
}

function fn_setearListaRestaurantes() {
    $(":input[name=chck_usr_rst]:checked").each(function () {
        $("#ckbx_rst_id_" + $(this).val()).prop('checked', false);
    });
}

function validarCaracteres(e) {
    var tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla == 8) {
        return true; // 3
    }

    var patron = /[A-Za-z\s]/; // 4
    var te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
}


function fn_verificarUsuario() {
    var descripcion = $('#usr_descripcion').val();
    descripcion = omitirAcentos(descripcion);
    if (descripcion.length > 5) {
        descripcion = descripcion.toLowerCase();
        var iniciales = '';
        var usuario = '';
        var indice = 1;
        var continuar = true;
        var send;
        iniciales = descripcion.substring(0, 1) + descripcion.substring(descripcion.indexOf(' ') + 1, descripcion.indexOf(' ') + 2);
        iniciales = iniciales.toUpperCase();
        $.ajaxSetup({async: false});
        while (continuar) {
            usuario = descripcion.substring(0, indice) + descripcion.substring(descripcion.indexOf(' ') + 1, descripcion.length);
            if (usuario.indexOf(' ') > 0) {
                usuario = usuario.substring(0, usuario.indexOf(' '));
            }
            send = {"verificarUsuarioSistema": 1};
            send.usuario = usuario;
            $.getJSON("../adminUsuario/config_usuarios.php", send, function (datos) {
                if (datos.str > 0) {
                    if (datos[0]['Continuar'] > 0) {
                        $('#usr_iniciales').val(iniciales);
                        $('#usr_sistema').val(usuario);
                        $('#usr_descripcion').val(descripcion.toUpperCase());
                        continuar = false;
                    } else {
                        if (indice > 5) {
                            alertify.error("<b>Alerta!</b> Usuario no disponible, cambie el nombre del usuario.");
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

function omitirAcentos(text) {
    var acentos = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç´";
    var original = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc";

    for (var i = 0; i < acentos.length; i++) {
        text = text.replace(acentos.charAt(i), original.charAt(i));
    }
    return text;
}

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
                //alertify.error("<b>Alerta!</b> El Documento de identificación ingresado ya existe." + datos[0]['usuario'] + ' ' + datos[0]['perfil'] + ' ' + datos[0]['tienda']);
                alertify.alert("<b>Alerta!</b> El Documento de identificación ingresado ya existe.<br>" +
                        '<b>El usuario es:</b> ' + datos[0]['usuario'] +
                        '<b> Su perfil es:</b> ' + datos[0]['perfil'] +
                        '<b> Está en la tienda:</b> ' + datos[0]['tienda']);
                return false;
            } else {
                fn_validaUsuario(opcion, documento);
            }
        }
    });
}

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

function fn_tipoDocumento(opcion) {
    var estadosw = $('#usr_sw_tipodocumento').bootstrapSwitch('state');
    var documento = $("#usr_cedula").val();
    if (documento == '') {
        $('#advertenciaUsuario').show();
        alertify.error("<b>Alerta!</b> Cédula campo obligatorio.");
    } else {
        if (estadosw == true) {
            if (fn_validarDocumento(documento)) {
                fn_guardar(opcion);
            } else {
                $('#advertenciaUsuario').hide();
                alertify.error("<b>Alerta!</b> Cédula incorrecta.");
            }
        } else {
            fn_guardar(opcion);
        }
    }
}

/* Guarda los datos ingresados en variables  */
function fn_guardar(accion) {
    fn_cargando(1);
    $("#opciones_estado label").removeClass("active");
    $("#opciones_1").addClass("active");
    $('input[name="ptns_std_prfl"]').prop("checked", false);
    $("#opciones_1 input").prop("checked", true);
    var usr_id = $("#hdn_usr_id").val();
    var usr_cedula = $("#usr_cedula").val();
    var cdn_id = $("#sess_cdn_id").val();
    var x = $("#usr_descripcion").val();
    var usr_descripcion = x.trim();
    var usr_direccion = $("#usr_direccion").val();
    var usr_iniciales = $("#usr_iniciales").val();
    var usr_email = $("#usr_email").val();
    var usr_telefono = $("#usr_telefono").val();
    var usr_usuario = $("#usr_sistema").val();
    var prf_id = $("#usr_prf_id").val();
    var usr_nombre_en_pos = $("#usr_dscrp_mxpnt").val();
    var usr_fecha_ingreso = $("#usr_inicio").val();
    var usr_fecha_salida = $("#usr_fin").val();
    var usr_tarjeta = $("#usr_tarjeta").val();
    var std_id = "Inactivo";

    var usr_clave = $("#usr_clave").val();

    if ($("#usr_std_id").is(":checked")) {
        std_id = "Activo";
    }

    fn_validaCampos(accion, usr_id, usr_cedula, cdn_id, prf_id, std_id, usr_nombre_en_pos, usr_usuario, usr_iniciales, usr_descripcion, usr_tarjeta, usr_fecha_ingreso, usr_fecha_salida, usr_telefono, usr_email, usr_direccion, usr_clave);
}

/* Valida que los campos sean llenados */
function fn_validarUsuarioDescripcion(user) {
    var patt = new RegExp("^[a-zA-Z ]+$");
    return patt.test(user);
}
/* Valida que los campos sean llenados */
function fn_validaCampos(accion, usr_id, usr_cedula, cdn_id, prf_id, std_id, usr_nombre_en_pos, usr_usuario, usr_iniciales, usr_descripcion, usr_tarjeta, usr_fecha_ingreso, usr_fecha_salida, usr_telefono, usr_email, usr_direccion, usr_clave) {
    if (fn_validarUsuarioDescripcion(usr_descripcion)) {
        if (fn_validarCampoVacio(usr_descripcion)) {
            if (prf_id !== "0") {
                if (fn_validarCampoVacio(usr_nombre_en_pos)) {
                    if (fn_validarCampoVacio(usr_clave)) {
                        if (fn_validarCampoVacio(usr_fecha_ingreso)) {
                            if (fn_validarCampoVacio(usr_fecha_salida)) {
                                fn_guardaUsuarioAdmin(accion, usr_id, usr_cedula, cdn_id, prf_id, std_id, usr_nombre_en_pos, usr_usuario, usr_iniciales, usr_descripcion, usr_tarjeta, usr_fecha_ingreso, usr_fecha_salida, usr_telefono, usr_email, usr_direccion, usr_clave);
                            } else {
                                $('#advertenciaUsuario').show();
                                alertify.error("<b>Alerta!</b> Fecha Fin campo obligatorio.");
                                fn_cargando(0);
                            }
                        } else {
                            $('#advertenciaUsuario').show();
                            alertify.error("<b>Alerta!</b> Fecha Inicio campo obligatorio.");
                            fn_cargando(0);
                        }
                    } else {
                        $('#advertenciaUsuario').show();
                        alertify.error("<b>Alerta!</b> Clave campo obligatorio.");
                        fn_cargando(0);
                    }
                } else {
                    $('#advertenciaUsuario').show();
                    alertify.error("<b>Alerta!</b> Nombre MaxPoint campo obligatorio.");
                    fn_cargando(0);
                }
            } else {
                $('#advertenciaUsuario').show();
                alertify.error("<b>Alerta!</b> Perfil campo obligatorio.");
                fn_cargando(0);
            }
        } else {
            $('#advertenciaUsuario').show();
            alertify.error("<b>Alerta!</b> Nombre y Apellido son campos obligatorios.");
            fn_cargando(0);
        }
    } else {
        $('#advertenciaUsuario').show();
        alertify.error("<b>Alerta!</b> Nombre y Apellido no deben contener caracteres especiales o numeros.");
        fn_cargando(0);
    }
}

/* Gurda la informacion ingresada */
function fn_guardaUsuarioAdmin(accion, usr_id, usr_cedula, cdn_id, prf_id, std_id, usr_nombre_en_pos, usr_usuario, usr_iniciales, usr_descripcion, usr_tarjeta, usr_fecha_ingreso, usr_fecha_salida, usr_telefono, usr_email, usr_direccion, usr_clave) {
    //para reiniciar el valor de la variable
    if (slccn_usr_rst == 2323) {
        slccn_usr_rst = '';
    }

    if (slccn_usr_rst.length > 0) {
        var send;
        var html = '<thead><tr class="active"><th class="text-center">Descripci&oacute;n</th><th class="text-center">Cedula</th><th class="text-center">Nombre MaxPoint</th><th class="text-center">Usuario</th><th class="text-center">Iniciales</th><th class="text-center">Perfil</th><th class="text-center">Restaurante</th><th class="text-center">Fecha Inicio</th><th class="text-center">Fecha Fin</th><th class="text-center">Activo</th></tr></thead>';
        send = {"administrarUsuario": 1};
        send.accion = accion;
        send.usr_id = usr_id;
        send.usr_cedula = usr_cedula;
        send.usr_log = $('#sess_usr_id').val();
        send.cdn_id = cdn_id;
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

        send.usr_clave = usr_clave;

        $.getJSON("../adminUsuario/config_usuarios.php", send, function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html = html + '<tr id="usr_' + datos[i]["usr_id"] + '" onclick="fn_seleccionarUsuario(\'' + datos[i]["usr_id"] + '\')" ondblclick="fn_modificar(\'' + datos[i]["usr_id"] + '\', \'' + datos[i]["usr_cedula"] + '\', \'' + datos[i]["usr_descripcion"] + '\', \'' + datos[i]["usr_nombre_en_pos"] + '\', \'' + datos[i]["usr_usuario"] + '\', \'' + datos[i]["usr_iniciales"] + '\', \'' + datos[i]["prf_id"] + '\', \'' + datos[i]["usr_telefono"] + '\', \'' + datos[i]["usr_email"] + '\', \'' + datos[i]["usr_direccion"] + '\', \'' + datos[i]["usr_tarjeta"] + '\', \'' + datos[i]["std_id"] + '\', \'' + datos[i]["usr_fecha_ingreso"] + '\', \'' + datos[i]["usr_fecha_salida"] + '\')"><td>' + datos[i]["usr_descripcion"] + '</td><td>' + datos[i]["usr_cedula"] + '</td><td class="text-center">' + datos[i]["usr_nombre_en_pos"] + '</td><td class="text-center">' + datos[i]["usr_usuario"] + '</td><td class="text-center">' + datos[i]["usr_iniciales"] + '</td><td class="text-center">' + datos[i]["prf_descripcion"] + '</td><td class="text-center">' + datos[i]["RestauranteAsignado"] + '</td><td class="text-center">' + datos[i]["usr_fecha_ingreso"] + '</td><td class="text-center">' + datos[i]["usr_fecha_salida"] + '</td><td class="text-center">';
                    if (datos[i]["std_id"] === "Activo") {
                        html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
                    } else if (datos[i]["std_id"] === "Restablecer") {
                        html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
                    } else {
                        html = html + '<input type="checkbox" name="" value="1" disabled/>';
                    }
                    html = html + '</td></tr>';
                }
                $("#tbl_lst_srs").html(html);
                $("#tbl_lst_srs").dataTable({"destroy": true});
                $("#tbl_lst_srs_length").hide();
                $("#tbl_lst_srs_paginate").addClass("col-xs-10");
                $("#tbl_lst_srs_info").addClass("col-xs-10");
                $("#tbl_lst_srs_length").addClass("col-xs-6");
                $("#mdl_usr").modal('hide');
                fn_cargarPerfiles();
                alertify.success("Datos guardados correctamente.");
                fn_cargando(0);
            } else {
                $("#mdl_usr").modal("hide");
                $("#tbl_lst_srs").html("");
                $("#tbl_lst_srs").dataTable({"destroy": true});
                $("#tbl_lst_srs_length").hide();
                $("#tbl_lst_srs_paginate").addClass("col-xs-10");
                $("#tbl_lst_srs_info").addClass("col-xs-10");
                $("#tbl_lst_srs_length").addClass("col-xs-6");
                fn_cargando(0);
            }
        });

    } else {
        $('#cargando').hide();
        alertify.error('<b>Alerta!</b> Debe seleccionar como mínimo una tienda');
    }
}

function fn_seleccionarLocal(rst_id) {
    if ($("#ckbx_rst_id_" + rst_id).is(':checked')) {
        slccn_usr_rst = slccn_usr_rst + rst_id + '_';
    } else {
        slccn_usr_rst = slccn_usr_rst.replace(rst_id + '_', '');
    }
}

function fn_marcarLocalesSeleccionados() {
    var argumento = slccn_usr_rst.split('_');
    for (var i = 0; i < argumento.length; i++) {
        $("#ckbx_rst_id_" + argumento[i]).prop('checked', 'checked');
    }
}

function fn_cargarLocalesUsuario(usr_id) {
    var send;
    send = {"cargarLocalesUsuario": 1};
    send.resultado = 4;
    send.usr_id = usr_id;
    send.cdn_id = $('#sess_cdn_id').val();
    send.std_id = 'Activo';
    $.getJSON("../adminUsuario/config_usuarios.php", send, function (datos) {
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
    send.resultado = 3;
    send.usr_id = 0;
    send.cdn_id = $('#sess_cdn_id').val();
    send.std_id = std_id;
    $.getJSON("../adminUsuario/config_usuarios.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                html = html + '<a class="list-group-item"><input id="ckbx_rst_id_' + datos[i]['rst_id'] + '" name="chck_usr_rst" onclick="fn_seleccionarLocal(' + datos[i]['rst_id'] + ')" value="' + datos[i]['rst_id'] + '" type="checkbox">&nbsp; ' + datos[i]['rst_descripcion'] + '</a>';
            }
            $("#lst_rst_usr").html(html);
            fn_marcarLocalesSeleccionados();
        } else {
            $("#lst_rst_usr").html('');
        }
    });
}

function fn_cargarPerfiles() {
    var html = '<option value="0">-- Seleccionar Perfil --</option>';
    var send;
    send = {"cargarPerfiles": 1};
    send.resultado = 2;
    send.usr_id = $('#sess_usr_id').val();
    send.cdn_id = 0;
    send.std_id = 0;
    $.getJSON("../adminUsuario/config_usuarios.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                html = html + '<option value="' + datos[i]['prf_id'] + '">' + datos[i]['prf_descripcion'] + '</option>';
            }
            $('#usr_prf_id').html(html);
        }
    });
}

function fn_agregar() {
    //elimina el color del borde 
    $(".form-group").css({'border': '0'});
    //elimina el mensaje de advertencia
    $('#advertenciaUsuario').hide();
    //agrego el campo de la clave para crear nuevo usuario
    $('#campoClave').show();
    $('#usr_clave').val('');

    fn_setearListaRestaurantes();
    slccn_usr_rst = '';
    $("#pcns_rst_lclzcn label").removeClass("active");
    $('#pcn_rst_lclzcn1').addClass('active');
    fn_cargarLocales('Todos');
    $('#tqt_mdl_usr').html("Nuevo Usuario");
    $("#pestanas li").removeClass("active");
    $('#tag_inicio').addClass('active');
    $("#pst_cnt div").removeClass("active");
    $('#inicio').addClass('active');
    $('#btn_pcn_dmn').html('<button type="button" class="btn btn-primary" onclick="fn_validaDocumentoRepetido(0)">Guardar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');
    $('#hdn_usr_id').val("0");
    $('#usr_std_id').prop('checked', true);
    $('#usr_descripcion').val('');
    $('#usr_cedula').val('');
    $('#usr_direccion').val('');
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
    $("#usr_iniciales").prop('disabled', true);
    $("#usr_descripcion").prop('disabled', false);
    $("#usr_cedula").prop('disabled', false);
    $("#usr_sistema").prop('disabled', false);
}

function fn_modificar(usr_id, usr_cedula, descripcion, nombrepos, usuario, iniciales, prf_id, telefono, correo, direccion, tarjeta, std_id, inicio, fin) {
    //elimina el color del borde 
    $(".form-group").css({'border': '0'});
    //elimina el mensaje de advertencia
    $('#advertenciaUsuario').hide();
    //oculta el campos de la clave al modificar
    $('#campoClave').hide();

    fn_setearListaRestaurantes();
    slccn_usr_rst = '';
    $("#pcns_rst_lclzcn label").removeClass("active");
    $('#pcn_rst_lclzcn0').addClass('active');
    fn_cargarLocales('Todos');
    $('#tqt_mdl_usr').html(descripcion);
    $("#pestanas li").removeClass("active");
    $('#tag_inicio').addClass('active');
    $("#pst_cnt div").removeClass("active");
    $('#inicio').addClass('active');
    $('#btn_pcn_dmn').html('<button type="button" class="btn btn-primary" onclick="fn_guardar(1)">Guardar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');
    $('#hdn_usr_id').val(usr_id);
    $("#usr_iniciales").prop('disabled', true);
    $("#usr_sistema").prop('disabled', true);
    $("#usr_descripcion").prop('disabled', false);
    if (std_id == 'Inactivo') {
        $('#usr_std_id').prop('checked', false);
    } else {
        $('#usr_std_id').prop('checked', true);
    }
    if (correo == '-') {
        correo = '';
    }
    if (telefono == '-') {
        telefono = '';
    }
    if (direccion == '-') {
        direccion = '';
    }
    if (nombrepos == '-') {
        nombrepos = descripcion;
    }
    if (tarjeta == '-') {
        tarjeta = '';
    }
    if (usr_cedula == '-') {
        usr_cedula = '';
    }
    $('#usr_descripcion').val(descripcion);

    if (fn_validarDocumento(usr_cedula)) {
        $("#usr_sw_tipodocumento").bootstrapSwitch('state', true);
    } else {
        $("#usr_sw_tipodocumento").bootstrapSwitch('state', false);
    }

    $("#usr_cedula").prop('disabled', true);
    $('#usr_cedula').val(usr_cedula);
    $('#usr_direccion').val(direccion);
    $('#usr_iniciales').val(iniciales);
    $('#usr_email').val(correo);
    $('#usr_telefono').val(telefono);
    $('#usr_sistema').val(usuario);
    $('#usr_prf_id').val(prf_id);
    $('#usr_dscrp_mxpnt').val(nombrepos);
    $('#usr_inicio').val(inicio);
    $('#usr_fin').val(fin);
    $('#usr_tarjeta').val(tarjeta);
    //seteando la clave a 0.. ya que no se actualiza en la base
    $('#usr_clave').val('0');

    $('#mdl_usr').modal('show');
    fn_cargarLocalesUsuario(usr_id);

    detalleUsuarioColeccionDeDatos(usr_id);
}

//function fn_restablecer(){
//    var usr_id = '';
//    var send;
//    usr_id = $('#tbl_lst_srs').find("tr.success").attr("id");
//    if(usr_id){
//        usr_id = usr_id.substring(4, usr_id.length);
//        send = {"restablecerClaveUsuario": 1};
//        send.accion = 2;
//        send.usr_id = usr_id;
//        send.usr_log = $('#sess_usr_id').val();
//        $.getJSON("../adminUsuario/config_usuarios.php", send, function(datos){
//            if(datos.Confirmar > 0){
//                alertify.success("<b>Confirmaci&oacute;n!</b> Clave Restablecida.");
//            } else {
//                alert("Error");
//            }
//        });
//    } else {
//        alertify.error("<b>Alerta!</b> Debe seleccionar un Usuario.");
//    }
//}

function fn_cargarUsuarios(resultado, std_id) {
    var send;
    var html = '<thead><tr class="active"><th class="text-center">Descripci&oacute;n</th><th class="text-center">Cedula</th><th class="text-center">Nombre MaxPoint</th><th class="text-center">Usuario</th><th class="text-center">Iniciales</th><th class="text-center">Perfil</th><th class="text-center">Restaurante</th><th class="text-center">Fecha Inicio</th><th class="text-center">Fecha Fin</th><th class="text-center">Activo</th></tr></thead>';
    send = {"administracionSeguridad": 1};
    send.accion = 'cargarUsuarios';
    send.resultado = resultado;
    send.usr_id = $('#sess_usr_id').val();
    send.cdn_id = $('#sess_cdn_id').val();
    send.std_id = std_id;
    $.getJSON("../adminUsuario/config_usuarios.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html = html + '<tr id="usr_' + datos[i]['usr_id'] + '" onclick="fn_seleccionarUsuario(\'' + datos[i]['usr_id'] + '\')" ondblclick="fn_modificar(\'' + datos[i]['usr_id'] + '\', \'' + datos[i]['usr_cedula'] + '\', \'' + datos[i]['usr_descripcion'] + '\', \'' + datos[i]['usr_nombre_en_pos'] + '\', \'' + datos[i]['usr_usuario'] + '\', \'' + datos[i]['usr_iniciales'] + '\', \'' + datos[i]['prf_id'] + '\', \'' + datos[i]['usr_telefono'] + '\', \'' + datos[i]['usr_email'] + '\', \'' + datos[i]['usr_direccion'] + '\', \'' + datos[i]['usr_tarjeta'] + '\', \'' + datos[i]['std_id'] + '\', \'' + datos[i]['usr_fecha_ingreso'] + '\', \'' + datos[i]['usr_fecha_salida'] + '\')"><td>' + datos[i]['usr_descripcion'] + '</td><td>' + datos[i]['usr_cedula'] + '</td><td class="text-center">' + datos[i]['usr_nombre_en_pos'] + '</td><td class="text-center">' + datos[i]['usr_usuario'] + '</td><td class="text-center">' + datos[i]['usr_iniciales'] + '</td><td class="text-center">' + datos[i]['prf_descripcion'] + '</td><td class="text-center">' + datos[i]['RestauranteAsignado'] + '</td><td class="text-center">' + datos[i]['usr_fecha_ingreso'] + '</td><td class="text-center">' + datos[i]['usr_fecha_salida'] + '</td><td class="text-center">';
                if (datos[i]['std_id'] == 'Activo') {
                    html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
                } else if (datos[i]['std_id'] == 'Restablecer') {
                    html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
                } else {
                    html = html + '<input type="checkbox" name="" value="1" disabled/>';
                }
                html = html + '</td></tr>';
            }
            $('#tbl_lst_srs').html(html);
            $('#tbl_lst_srs').dataTable({'destroy': true});
            $("#tbl_lst_srs_length").hide();
            $("#tbl_lst_srs_paginate").addClass('col-xs-10');
            $("#tbl_lst_srs_info").addClass('col-xs-10');
            $("#tbl_lst_srs_length").addClass('col-xs-6');
        } else {
            $('#tbl_lst_srs').html("");
            $('#tbl_lst_srs').dataTable({'destroy': true});
            $("#tbl_lst_srs_length").hide();
            $("#tbl_lst_srs_paginate").addClass('col-xs-10');
            $("#tbl_lst_srs_info").addClass('col-xs-10');
            $("#tbl_lst_srs_length").addClass('col-xs-6');
        }
    });
}

function fn_seleccionarUsuario(usr_id) {
    $("#tbl_lst_srs tr").removeClass("success");
    $("#usr_" + usr_id).addClass("success");
}

function fn_validarCampoVacio(valor) {
    return (valor.length > 0);
}

function fn_btn(boton, estado, estilo) {
    if (estado) {
        //$("#btn_"+boton).css("background","url('../../imagenes/admin_resources/"+boton+".png') 14px 4px no-repeat");

        $("#btn_" + boton).removeAttr("disabled");
        $("#btn_" + boton).addClass("botonhabilitado " + estilo);
        $("#btn_" + boton).removeClass("botonbloqueado");
    } else {
        $("#btn_" + boton).css("background", "url('../../imagenes/admin_resources/" + boton + "_bloqueado.png') 14px 4px no-repeat");
        $("#btn_" + boton).prop('disabled', true);
        $("#btn_" + boton).addClass("botonbloqueado");
        $("#btn_" + boton).removeClass("botonhabilitado");
    }
}

/////////////////////MODAL DE CREDENCIALES PARA RESTABLECER CONTRASEñA////////////////////
function fn_modalCambioClave() {
    var usr_id = '';
    $("#advertencia").hide();
    $("#claveNueva").css({'color': '#333333'});
    $("#claveConfirma").css({'color': '#333333'});
    //usr_id = $("#txt_idusuario").val();
    $(".form-group").css({'border': '0'});
    usr_id = $('#tbl_lst_srs').find("tr.success").attr("id");
    $("#usr_clave_cambio1").val('');
    $("#usr_clave_cambio2").val('');
    if (usr_id) {
        $('#modal_cambioclave').modal('show');
        $('#modal_cambioclave').on('shown.bs.modal', function () {
            $("#usr_clave_cambio1").focus();
        });
    } else {
        alertify.error("Debe seleccionar un usuario.");
    }
}

////////////////////////////FUNCION QUE VALIDA LAS CREDENCIALES/////////////////////////////
function fn_validarCredenciales() {
    var claveNueva = $("#usr_clave_cambio1").val();
    var confirmaClave = $("#usr_clave_cambio2").val();
    if (claveNueva.length < 1 || confirmaClave.length < 1) {
        $(".form-group").css({'border': '0'});
        $("#claveNueva").css({'color': '#d93f3f'});
        $("#claveConfirma").css({'color': '#d93f3f'});
        $("#advertencia").show();
        alertify.error('Debe ingresar una clave');
    } else if (claveNueva == confirmaClave && claveNueva.length > 0 && confirmaClave.length > 0) {
        fn_restablecer(claveNueva);
    } else {
        $(".form-group").css({'border': '0.5px solid red'});
        $("#advertencia").hide();
        $("#claveNueva").css({'color': '#333333'});
        $("#claveConfirma").css({'color': '#333333'});
        alertify.error('Las claves son distintas, vuelva a intentarlo');
    }
}

//////////////////////////////FUNCION QUE MODIFICA LAS CREDENCIALES//////////////////////////////////
function fn_restablecer(claveNueva) {
    var clave = claveNueva;
    var usr_id = '';
    var send;
    usr_id = $('#tbl_lst_srs').find("tr.success").attr("id");
    usr_id = usr_id.substring(4, usr_id.length);
    send = {"restablecerClaveUsuario": 1};
    send.accion = 2;
    send.usr_id = usr_id;
    send.pass = clave;
    send.usr_log = $('#sess_usr_id').val();
    $.getJSON("../adminUsuario/config_usuarios.php", send, function (datos) {
        if (datos[0]['continuar'] == 1) {
            alertify.success("<b>Confirmaci&oacute;n!</b> Clave Modificada.");
            $('#modal_cambioclave').modal('hide');
        } else {
            $("#usr_clave_cambio1").val('');
            $("#usr_clave_cambio2").val('');
            alertify.error("<b>Error</b> Clave Ya Existe.");
        }
    });
}

function fn_listarRegiones() {
    var send;
    var html;
    html = '<label id="pcn_rst_lclzcn0" class="btn btn-default btn-sm active" onclick="fn_cargarLocales(\'Todos\');"><input type="radio" value="Todos" autocomplete="off" name="ptns_std_prfl" checked="checked">Todos</label>';
    send = {"listarRegiones": 1};
    $.getJSON("../adminUsuario/config_usuarios.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html = html + '<label id="pcn_rst_lclzcn' + (i + 1) + '" class="btn btn-default btn-sm" onclick="fn_cargarLocales(\'' + datos[i]['rgn_descripcion'] + '\');">' +
                        '<input type="radio"  autocomplete="off"  name="ptns_std_prfl">' + datos[i]['rgn_descripcion'] + '</label>';
            }
            $('#pcns_rst_lclzcn').html(html);
        }
    });
}