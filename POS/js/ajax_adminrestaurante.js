/* global alertify */

/*NUEVA COLECCION*/
var lc_IDColeccionRestaurante = '';
var lc_IDColeccionDeDatosRestaurante = '';

/*MODIFICAR COLECCION*/
var lc_nombreColeccion = '';
var lc_IDColeccionRestaurante_edit = '';
var lc_IDColeccionDeDatosRestaurante_edit = '';

/*VARIABLE PARA VALIDAR QUE TIPO DE DOCUMENTO NO ESTE EN 2 CANALES DE IMPRESION*/
var VALIDA_TIPO_DOCUMENTO = '';
var VALIDA_TIPO_DOCUMENTO_FALSE = '';
$.fn.editable.defaults.ajaxOptions = {type: "GET"};

$(document).ready(function () {
    fn_visor(0);

    $('#modal').modal('hide');
    $('#cnt_lst_aut_rst').css('display', 'block');
    $('#cnt_frm_nv_aut_rst').css('display', 'none');
    $("#rst_cntd_grms").bootstrapSwitch('state', true);
    $("#rst_srvc").bootstrapSwitch('state', true);
    $("#rst_br_cjn").bootstrapSwitch('state', true);
    $("#rst_cnclr_pg").bootstrapSwitch('state', true);
    $("#rst_nmr_prsns").bootstrapSwitch('state', true);
    $("#rst_sw_atencion24horas").bootstrapSwitch('state', true);

    fn_btn('cancelar', 0);
    fn_btn('eliminar', 0);
    fn_btn('agregar', 1);

    fn_cargarCiudades();
    fn_cargarCategoria();
    fn_cargarTipoServicio();
    fn_cargarTipoFacturacion();
    fn_cargarMetodosCalculoImpuestos();
    fn_cargarImpuestosCadena();
    fn_consultarListaRestaurantesActivos(0);
    fn_cargando(0);

    $('#rst_cntd_grms').on('switchChange.bootstrapSwitch', function () {
        estado = $("#rst_cntd_grms").bootstrapSwitch('state');
        if (estado) {
            var anulacion = 1;
        } else {
            var anulacion = 0;
        }
    });

    $('#rst_srvc').on('switchChange.bootstrapSwitch', function () {
        estado = $("#rst_srvc").bootstrapSwitch('state');
        if (estado) {
            var anulacion = 1;
        } else {
            var anulacion = 0;
        }
    });

    $('#rst_br_cjn').on('switchChange.bootstrapSwitch', function () {
        estado = $("#rst_br_cjn").bootstrapSwitch('state');
        if (estado) {
            var anulacion = 1;
        } else {
            var anulacion = 0;
        }
    });

    $('#rst_cnclr_pg').on('switchChange.bootstrapSwitch', function () {
        estado = $("#rst_cnclr_pg").bootstrapSwitch('state');
        if (estado) {
            var anulacion = 1;
        } else {
            var anulacion = 0;
        }
    });

    $('#rst_nmr_prsns').on('switchChange.bootstrapSwitch', function () {
        estado = $("#rst_nmr_prsns").bootstrapSwitch('state');
        if (estado) {
            var anulacion = 1;
        } else {
            var anulacion = 0;
        }
    });

    $('#rst_sw_atencion24horas').on('switchChange.bootstrapSwitch', function () {
        estado = $("#rst_sw_atencion24horas").bootstrapSwitch('state');
        if (estado) {
            var atencion24horas = 1;
        } else {
            var atencion24horas = 0;
        }
    });

});

function fn_cargarAutorizacionesRestaurante(rst_id) {
    $('#ant_aut_rst_fch_fnl').daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });
    $('#aut_rst_fch_ncl').daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });
    var cdn_id = $("#sess_cdn_id").val();
    var html = '<tr class="bg-primary"><th>N&uacute;mero Autorizaci&oacute;n</th><th class="text-center">Fecha Inicio</th><th>Fecha Fin</th><th>Inicio Secuencia</th><th class="text-center">Fin Secuencia</th><th>Activo</th></tr>';
    send = {"cargarAutorizacionesRestaurantes": 1};
    send.resultado = 9;
    send.rst_id = rst_id;
    send.cdn_id = cdn_id;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += '<tr id="atr_id_' + i + '" onclick="fn_seleccionarAutorizacion(' + i + ')" ondblclick="fn_modificarAutorizacionRestaurante(' + rst_id + ', ' + cdn_id + ', ' + datos[i]['aur_id'] + ', ' + datos[i]['std_id'] + ', \'' + datos[i]['aur_fecha_inicio'] + '\', \'' + datos[i]['aur_fecha_fin'] + '\', ' + datos[i]['aur_inicio_secuencia'] + ', ' + datos[i]['aur_ultima_secuencia'] + ')" class="text-center"><td>' + datos[i]['atr_numero_autorizacion'] + '</td><td>' + datos[i]['aur_fecha_inicio'] + '</td><td>' + datos[i]['aur_fecha_fin'] + '</td><td>' + datos[i]['aur_inicio_secuencia'] + '</td><td>' + datos[i]['aur_ultima_secuencia'] + '</td>';
                if (datos[i]['std_id'] == 1) {
                    html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                } else {
                    html += '<td><input type="checkbox" value="1" disabled/></td>';
                }
                html += '</tr>';
            }
            $("#lst_autorizaciones_rst").html(html);
        } else {
            html += '<tr ondblclick="fn_crearAutorizacionRestaurante(' + rst_id + ', ' + cdn_id + ');"><td colspan="6">No existen registros. Doble click para crear nueva secuencia.</td></tr>';
            $("#lst_autorizaciones_rst").html(html);
        }
    });
}

function fn_crearAutorizacionRestaurante(rst_id, cdn_id) {
    var emp_confirmar = $('#emp_confirmacion_ok').val();
    var emp_nombre = $('#rst_mprs_dscrpcn').html();
    $('#nuv_aut_rst_fch_ncl').daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {});
    var html = '<button type="button" onclick="fn_guardarNuevaAutorizacion(1, ' + rst_id + ', ' + cdn_id + ')" class="btn btn-primary">Confirmar</button>&nbsp;&nbsp;<button type="button" onclick="fn_cancelarAutorizacionNuevoResturante();" class="btn btn-default">Cancelar</button>';
    $('#cnt_aut_nea_rst_opc').html(html);
    if (emp_confirmar < 1) {
        alertify.error("<b>Alerta!</b> No existe una nueva Autorización para " + emp_nombre);
    } else {
        $('#nuv_aut_rst_fch_ncl').prop('disabled', false);
        $('#nuv_aut_rst_fch_fnl').prop('disabled', true);
        $('#nuv_aut_rst_scnc_ncl').prop('disabled', false);
        $('#nuv_aut_rst_scnc_fnl').prop('disabled', true);
        $('#nuv_aut_rst_scnc_ncl').val("");
        $('#cnt_lst_aut_rst').css('display', 'none');
        $('#cnt_frm_nv_nea_aut_rst').css('display', 'block');
    }
}

function fn_guardarNuevaAutorizacion(accion, rst_id, cdn_id) {
    var html = '<tr class="bg-primary"><th>N&uacute;mero Autorizaci&oacute;n</th><th class="text-center">Fecha Inicio</th><th>Fecha Fin</th><th>Inicio Secuencia</th><th class="text-center">Fin Secuencia</th><th>Activo</th></tr>';
    send = {"administrarAutorizacionRestaurante": 1};
    send.accion = accion;
    send.rst_id = rst_id;
    send.cdn_id = cdn_id;
    send.aur_id = 0;
    send.sec_ini = $('#nuv_aut_rst_scnc_ncl').val();
    send.sec_fin = $('#nuv_aut_rst_scnc_ncl').val();
    send.fecha_ini = $('#nuv_aut_rst_fch_ncl').val();
    send.fecha_fin = $('#nuv_aut_rst_fch_fnl').val();
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += '<tr id="atr_id_' + i + '" onclick="fn_seleccionarAutorizacion(' + i + ')" ondblclick="fn_modificarAutorizacionRestaurante(' + rst_id + ', ' + cdn_id + ', ' + datos[i]['aur_id'] + ', ' + datos[i]['std_id'] + ', \'' + datos[i]['aur_fecha_inicio'] + '\', \'' + datos[i]['aur_fecha_fin'] + '\', ' + datos[i]['aur_inicio_secuencia'] + ', ' + datos[i]['aur_ultima_secuencia'] + ')" class="text-center"><td>' + datos[i]['atr_numero_autorizacion'] + '</td><td>' + datos[i]['aur_fecha_inicio'] + '</td><td>' + datos[i]['aur_fecha_fin'] + '</td><td>' + datos[i]['aur_inicio_secuencia'] + '</td><td>' + datos[i]['aur_ultima_secuencia'] + '</td>';
                if (datos[i]['std_id'] == 1) {
                    html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                } else {
                    html += '<td><input type="checkbox" value="1" disabled/></td>';
                }
                html += '</tr>';
            }
            $('#emp_confirmacion_ok').val(0);
            $("#lst_autorizaciones_rst").html(html);
            $('#cnt_lst_aut_rst').css('display', 'block');
            $('#cnt_frm_nv_nea_aut_rst').css('display', 'none');
        }
    });
}

function fn_modificarAutorizacionRestaurante(rst_id, cdn_id, aur_id, std_id, fecha_inicio, fecha_fin, sec_inicio, sec_fin) {
    var html = '<button type="button" onclick="fn_guardarAutorizaciones(0, ' + rst_id + ', ' + cdn_id + ', ' + aur_id + ')" class="btn btn-primary">Confirmar</button>&nbsp;&nbsp;<button type="button" onclick="fn_cancelarAutorizacionResturante()" class="btn btn-default">Cancelar</button>';
    var emp_confirmar = $('#emp_confirmacion_ok').val();
    var emp_nombre = $('#rst_mprs_dscrpcn').html();
    if (std_id < 1) {
        alertify.error("<b>Alerta!</b> Este registro no puede ser modificado.");
    } else {
        if (emp_confirmar < 1) {
            alertify.error("<b>Alerta!</b> No existe una nueva Autorización para " + emp_nombre);
        } else {
            $('#ant_aut_rst_fch_ncl').val(fecha_inicio);
            $('#ant_aut_rst_fch_ncl').prop('disabled', true);
            $('#ant_aut_rst_fch_fnl').val("");
            $('#ant_aut_rst_fch_fnl').prop('disabled', false);
            $('#ant_aut_rst_scnc_ncl').val(sec_inicio);
            $('#ant_aut_rst_scnc_ncl').prop('disabled', true);
            $('#ant_aut_rst_scnc_fnl').val("");
            $('#ant_aut_rst_scnc_fnl').prop('disabled', false);
            $('#aut_rst_fch_ncl').prop('disabled', false);
            $('#aut_rst_fch_fnl').prop('disabled', true);
            $('#aut_rst_scnc_ncl').prop('disabled', false);
            $('#aut_rst_scnc_fnl').prop('disabled', true);
            $('#aut_rst_fch_fnl').val("");
            $('#aut_rst_scnc_ncl').val("");
            $('#aut_rst_scnc_fnl').val("");
            $('#cnt_lst_aut_rst').css('display', 'none');
            $('#cnt_frm_nv_aut_rst').css('display', 'block');
            $('#cnt_aut_rst_opc').html(html);
        }
    }
}

function fn_guardarAutorizaciones(accion, rst_id, cdn_id, aur_id) {
    var html = '<tr class="bg-primary"><th>N&uacute;mero Autorizaci&oacute;n</th><th class="text-center">Fecha Inicio</th><th>Fecha Fin</th><th>Inicio Secuencia</th><th class="text-center">Fin Secuencia</th><th>Activo</th></tr>';
    send = {"administrarAutorizacionRestaurante": 1};
    send.accion = accion;
    send.rst_id = rst_id;
    send.cdn_id = cdn_id;
    send.aur_id = aur_id;
    send.sec_ini = $('#aut_rst_scnc_ncl').val();
    send.sec_fin = $('#ant_aut_rst_scnc_fnl').val();
    send.fecha_ini = $('#aut_rst_fch_ncl').val();
    send.fecha_fin = $('#ant_aut_rst_fch_fnl').val();
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += '<tr id="atr_id_' + i + '" onclick="fn_seleccionarAutorizacion(' + i + ')" ondblclick="fn_modificarAutorizacionRestaurante(' + rst_id + ', ' + cdn_id + ', ' + datos[i]['aur_id'] + ', ' + datos[i]['std_id'] + ', \'' + datos[i]['aur_fecha_inicio'] + '\', \'' + datos[i]['aur_fecha_fin'] + '\', ' + datos[i]['aur_inicio_secuencia'] + ', ' + datos[i]['aur_ultima_secuencia'] + ')" class="text-center"><td>' + datos[i]['atr_numero_autorizacion'] + '</td><td>' + datos[i]['aur_fecha_inicio'] + '</td><td>' + datos[i]['aur_fecha_fin'] + '</td><td>' + datos[i]['aur_inicio_secuencia'] + '</td><td>' + datos[i]['aur_ultima_secuencia'] + '</td>';
                if (datos[i]['std_id'] == 1) {
                    html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                } else {
                    html += '<td><input type="checkbox" value="1" disabled/></td>';
                }
                html += '</tr>';
            }
            $('#emp_confirmacion_ok').val(0);
            $("#lst_autorizaciones_rst").html(html);
            $('#cnt_lst_aut_rst').css('display', 'block');
            $('#cnt_frm_nv_aut_rst').css('display', 'none');
        }
    });
}

function fn_calcularSecuenciaInicial() {
    var secuencia_final = $('#ant_aut_rst_scnc_fnl').val();
    secuencia_final = parseInt(secuencia_final) + 1;
    $('#aut_rst_scnc_ncl').val(secuencia_final);
}

function fn_cancelarAutorizacionResturante() {
    $('#cnt_lst_aut_rst').css('display', 'block');
    $('#cnt_frm_nv_aut_rst').css('display', 'none');
}

function fn_seleccionarAutorizacion(indice) {
    $("#lst_autorizaciones_rst tr").removeClass("success");
    $("#atr_id_" + indice).addClass("success");
}

function fn_cancelarAutorizacionNuevoResturante() {
    $('#cnt_lst_aut_rst').css('display', 'block');
    $('#cnt_frm_nv_nea_aut_rst').css('display', 'none');
}

function fn_consultarInformacionRestaurante(rst_id) {
    var cdn_id = $("#sess_cdn_id").val();
    var html = '<tr class="active"><th>Descripci&oacute;n Restaurante</th><th>Direccion</th><th class="text-center">Ciudad</th><th class="text-center">Telefono</th><th>Tipo de Servicio</th><th>Pisos</th><th>Localizaci&oacute;n</th><th class="text-center">Activo</th></tr>';
    send = {"cargarInformacionRestaurante": 1};
    send.resultado = 4;
    send.rst_id = rst_id;
    send.cdn_id = cdn_id;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {

            $("#slccn_rst_id").val(rst_id);
            $('#dscrpcn_rst_ttl').html(datos[0]['rst_descripcion']);
            $('#titulomodal').html(datos[0]['rst_descripcion']);
            $('#rst_direccion').val(datos[0]['rst_direccion']);
            $('#rst_telefono').val(datos[0]['rst_fono']);
            $('#lclzcn_rst').html(datos[0]['rst_localizacion']);
            $('#ciu_rst').val(datos[0]['ciu_id']);
            $('#cat_rst').val(datos[0]['IDCategoria']);
            $('#tpsrv_rst').val(datos[0]['rst_tipo_servicio']);
            $('#rst_mid').val(datos[0]['rst_mid']);
            $('#tpfct_rst').val(datos[0]['rst_tipo_facturacion']);
            $('#rst_nmr_sr').val(datos[0]['rst_serie']);
            $('#rst_pnt_msn').val(datos[0]['rst_puntoemision']);
            $('#rst_tmp_pdd').val(datos[0]['rst_tiempopedido'] / 6000);
            $('#vlr_emp_id').val(datos[0]['emp_id']);
            $('#rst_mprs_ruc').html(datos[0]['emp_ruc']);
            $('#rst_mprs_dscrpcn').html(datos[0]['emp_nombre']);
            $('#emp_confirmacion_ok').val(datos[0]['emp_confirmar']);
            $('#tp_mtd_mpst_rst').val(datos[0]['metodoImpuesto']);
            if (datos[0]['rst_cancelar_pago'] == 1) {
                $("#rst_cnclr_pg").bootstrapSwitch('state', true);
            } else {
                $("#rst_cnclr_pg").bootstrapSwitch('state', false);
            }
            if (datos[0]['rst_num_personas'] == 1) {
                $("#rst_nmr_prsns").bootstrapSwitch('state', true);
            } else {
                $("#rst_nmr_prsns").bootstrapSwitch('state', false);
            }
            if (datos[0]['rst_tipo_cantidad'] == 1) {
                $("#rst_cntd_grms").bootstrapSwitch('state', true);
            } else {
                $("#rst_cntd_grms").bootstrapSwitch('state', false);
            }
            if (datos[0]['rst_cajon_fin_transaccion'] == 1) {
                $("#rst_br_cjn").bootstrapSwitch('state', true);
            } else {
                $("#rst_br_cjn").bootstrapSwitch('state', false);
            }
            if (datos[0]['rst_horarioatencion'] == 1) {
                $("#rst_sw_atencion24horas").bootstrapSwitch('state', true);
            } else {
                $("#rst_sw_atencion24horas").bootstrapSwitch('state', false);
            }
            if (datos[0]['std_id'] == 'Activo') {
                $("#rst_std_id").prop('checked', true);
            } else {
                $("#rst_std_id").prop('checked', false);
            }
            fn_cargando(0);
        }
    });
}

function fn_validarImpuestosLocal() {
    var metodo = $("#tp_mtd_mpst_rst option:selected").text();

    if ($("#slct_mpsts_rstrnt option:selected").length > 1 && (metodo == 'INCLUIDO' || metodo == 'EXCLUIDO')) {
        $("#slct_mpsts_rstrnt").val("");
        $('#slct_mpsts_rstrnt').trigger("chosen:updated");
        alertify.error("<b>Alerta!</b> Con el Metodo " + metodo + " solo puede seleccionar un impuesto.");
    }
}

function fn_guardarImpuestos() {
    var impuestos = "";
    var rst_id = $("#slccn_rst_id").val();
    $('#slct_mpsts_rstrnt option:selected').each(function (i, selected) {
        impuestos = impuestos + $(selected).val() + '_';
    });
    send = {"guardarImpuestosRestaurante": 1};
    send.accion = 1;
    send.rst_id = rst_id;
    send.impuestos = impuestos;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            alertify.success("Impuestos actualizados correctamente.");
        } else {
            alertify.error("<b>Alerta!</b> Ocurrio un error, verifique los impuestos.");
        }
    });
}

function fn_guardar() {
    fn_validaTipoDocumentoCanalImpresion();

    if (VALIDA_TIPO_DOCUMENTO_FALSE !== 0) {
        var html = '<thead><tr class="active"><th>Descripci&oacute;n Restaurante</th><th>Direccion</th><th class="text-center">Ciudad</th><th class="text-center">Telefono</th><th>Tipo de Servicio</th><th>Pisos</th><th>Localizaci&oacute;n</th><th class="text-center">Activo</th></tr></thead>';
        var cdn_id = $("#sess_cdn_id").val();
        var rst_id = $("#slccn_rst_id").val();
        var rst_direccion = $('#rst_direccion').val();
        var rst_fono = $('#rst_telefono').val();
        var ciu_id = $('#ciu_rst').val();
        var tpsrv_rst = $('#tpsrv_rst').val();
        var rst_mid = $('#rst_mid').val();
        var tpfct_rst = $('#tpfct_rst').val();
        var rst_nmr_sr = $('#rst_nmr_sr').val();
        var rst_pnt_msn = $('#rst_pnt_msn').val();
        var rst_tmp_pdd = $('#rst_tmp_pdd').val();
        var tp_mtd_mpst_rst = $('#tp_mtd_mpst_rst').val();
        var cat_rst = $("#cat_rst").val();
        //Transformaci�n a milisegundos
        rst_tmp_pdd = rst_tmp_pdd * 6000;
        var rst_cnclr_pg = 0;
        var rst_nmr_prsns = 0;
        var rst_srvc = 0;
        var rst_cntd_grms = 0;
        var rst_br_cjn = 0;
        var rst_horarioatencion = 0;
        var std_id = 'Inactivo';
        if ($("#rst_cnclr_pg").bootstrapSwitch('state')) {
            rst_cnclr_pg = 1;
        }
        if ($("#rst_nmr_prsns").bootstrapSwitch('state')) {
            rst_nmr_prsns = 1;
        }
        if ($("#rst_cntd_grms").bootstrapSwitch('state')) {
            rst_cntd_grms = 1;
        }
        if ($("#rst_br_cjn").bootstrapSwitch('state')) {
            rst_br_cjn = 1;
        }
        if ($("#rst_sw_atencion24horas").bootstrapSwitch('state')) {
            rst_horarioatencion = 1;
        }
        if ($("#rst_std_id").is(':checked')) {
            std_id = 'Activo';
        }
        //validaci�n categoria
        if ($("#cat_rst").val() == '00000000-0000-0000-0000-000000000000') {
            alertify.error("<b>Alerta!</b> Categoria es un campo obligatorio.");
            return false;
        }
        if (fn_validarCampoVacio(rst_direccion)) {
            if (fn_validarCampoVacio(rst_fono)) {
                if (ciu_id > 0) {
                    if (tpsrv_rst.length > 1) {
                        if (fn_validarCampoVacio(rst_mid)) {
                            if (tpfct_rst.length > 1) {
                                if (fn_validarCampoVacio(rst_nmr_sr)) {
                                    if (fn_validarCampoVacio(rst_pnt_msn)) {
                                        if (rst_tmp_pdd >= 0) {
                                            if (tp_mtd_mpst_rst != '0') {
                                                if ($("#slct_mpsts_rstrnt option:selected").length > 0) {
                                                    var metodo = $("#tp_mtd_mpst_rst option:selected").text();
                                                    if ($("#slct_mpsts_rstrnt option:selected").length > 1 && (metodo == 'INCLUIDO' || metodo == 'EXCLUIDO')) {//validaci�n impuestos
                                                        alertify.error("<b>Alerta!</b> Con el Metodo " + metodo + " solo puede seleccionar un impuesto.");
                                                        return false;
                                                    } else if ($("#slct_mpsts_rstrnt option:selected").length < 2 && (metodo == 'DIFERENCIADO INCLUIDO' || metodo == 'DIFERENCIADO EXCLUIDO')) {//validaci�n impuestos
                                                        alertify.error("<b>Alerta!</b> Con el Metodo " + metodo + " debe seleccionar mas de 2 impuestos.");
                                                        return false;
                                                    }
                                                    //if(metodo=='DIFERENCIADO INCLUIDO' || metodo=='DIFERENCIADO EXCLUIDO'){													
                                                    var Confirmar = 0;
                                                    var impuestos = "";
                                                    var rst_id = $("#slccn_rst_id").val();
                                                    $('#slct_mpsts_rstrnt option:selected').each(function (i, selected) {
                                                        impuestos = impuestos + $(selected).val() + '_';
                                                    });
                                                    send = {"validarImpuestosRestaurante": 1};
                                                    send.accion = 2;
                                                    send.rst_id = rst_id;
                                                    send.impuestos = impuestos;
                                                    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
                                                        if (datos.str > 0) {
                                                            for (i = 0; i < datos.str; i++) {
                                                                Confirmar = datos[i]['Confirmar'];
                                                                if (datos[i]['Confirmar'] == 0 && (metodo == 'DIFERENCIADO INCLUIDO' || metodo == 'DIFERENCIADO EXCLUIDO')) {
                                                                    alertify.error("<b>Alerta!</b> Tiene productos con impuestos que intenta desactivar.");
                                                                    return false;
                                                                } else {
                                                                    fn_cargando(1);
                                                                    send = {"modificarRestaurante": 1};
                                                                    send.accion = 0;
                                                                    send.resultado = 0;
                                                                    send.rst_id = rst_id;
                                                                    send.rst_direccion = rst_direccion;
                                                                    send.rst_fono = rst_fono;
                                                                    send.ciu_id = ciu_id;
                                                                    send.tpsrv_rst = tpsrv_rst;
                                                                    send.rst_mid = rst_mid;
                                                                    send.tpfct_rst = tpfct_rst;
                                                                    send.rst_nmr_sr = rst_nmr_sr;
                                                                    send.rst_pnt_msn = rst_pnt_msn;
                                                                    send.rst_tmp_pdd = rst_tmp_pdd;
                                                                    send.rst_cnclr_pg = rst_cnclr_pg;
                                                                    send.rst_nmr_prsns = rst_nmr_prsns;
                                                                    send.rst_srvc = rst_srvc;
                                                                    send.rst_cntd_grms = rst_cntd_grms;
                                                                    send.rst_br_cjn = rst_br_cjn;
                                                                    send.std_id = std_id;
                                                                    send.cdn_id = cdn_id;
                                                                    send.cat_rst = cat_rst;
                                                                    send.horarioatencion = rst_horarioatencion;
                                                                    send.idMetodoImpuesto = tp_mtd_mpst_rst;
                                                                    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
                                                                        if (datos.str > 0) {
                                                                            fn_guardarImpuestos();
                                                                            for (i = 0; i < datos.str; i++) {
                                                                                html = html + '<tr id="rst_id_' + datos[i]['rst_id'] + '" onclick="fn_seleccionarRestaurante(' + datos[i]['rst_id'] + ')" ondblclick="fn_modificar(' + datos[i]['rst_id'] + ')"><td>' + datos[i]['rst_descripcion'] + '</td><td>' + datos[i]['rst_direccion'] + '</td><td class="text-center">' + datos[i]['ciu_nombre'] + '</td><td class="text-center">' + datos[i]['rst_fono'] + '</td><td>' + datos[i]['tpsrv_descripcion'] + '</td><td>' + datos[i]['rst_numpiso'] + '</td><td>' + datos[i]['rst_localizacion'] + '</td><td class="text-center">';
                                                                                if (datos[i]['std_id'] == 'Activo') {
                                                                                    html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
                                                                                } else {
                                                                                    html = html + '<input type="checkbox" name="" value="1" disabled/>';
                                                                                }
                                                                                html = html + '</td></tr>';
                                                                            }
                                                                            $('#listaRestaurantes').html(html);
                                                                            $('#listaRestaurantes').dataTable({
                                                                                'destroy': true
                                                                            });
                                                                            $("#listaRestaurantes_length").hide();
                                                                            $("#listaRestaurantes_paginate").addClass('col-xs-10');
                                                                            $("#listaRestaurantes_info").addClass('col-xs-10');
                                                                            $("#listaRestaurantes_length").addClass('col-xs-6');
                                                                            fn_cargando(0);
                                                                            $("#opciones_estado label").removeClass("active");
                                                                            $("#opciones_1").addClass("active");
                                                                            $('#modal').modal('hide');
                                                                        } else {
                                                                            html = html + '<tr><th colspan="8">No existen registros.</th></tr>';
                                                                            $('#listaRestaurantes').html(html);
                                                                            fn_cargando(0);
                                                                        }
                                                                    });
                                                                }
                                                            }
                                                        }
                                                    });
                                                } else {
                                                    alertify.error("<b>Alerta!</b> Debe agregar por lo menos un impuesto.");
                                                }
                                            } else {
                                                alertify.error("<b>Alerta!</b> El M&eacute;todo de Calculo de impuestos es Obligatorio.");
                                            }
                                        } else {
                                            alertify.error("<b>Alerta!</b> Tiempo de Pedido campo obligatorio.");
                                        }
                                    } else {
                                        alertify.error("<b>Alerta!</b> Punto de Emision campo obligatorio.");
                                    }
                                } else {
                                    alertify.error("<b>Alerta!</b> Numero de serie campo obligatorio.");
                                }
                            } else {
                                alertify.error("<b>Alerta!</b> Elija un Tipo de Facturacion.");
                            }
                        } else {
                            alertify.error("<b>Alerta!</b> MID campo obligatorio.");
                        }
                    } else {
                        alertify.error("<b>Alerta!</b> Elija un Tipo de Servicio.");
                    }
                } else {
                    alertify.error("<b>Alerta!</b> Elija una ciudad.");
                }
            } else {
                alertify.error("<b>Alerta!</b> Telefono campo obligatorio.");
            }
        } else {
            alertify.error("<b>Alerta!</b> Direccion campo obligatorio.");
        }

        fn_guardarImpresionTipoDocumentos();
    } else {
        alertify.error("<b>Alerta!</b> No puede ingresar el mismo Tipo de Documento en varios Canales de Impresion.");
        VALIDA_TIPO_DOCUMENTO_FALSE = 1;
        return false;
    }
}


function fn_consultarListaRestaurantes(rst_id) {
    var cdn_id = $("#sess_cdn_id").val();
    var html = '<thead><tr class="active"><th>Descripci&oacute;n Restaurante</th><th>Direccion</th><th class="text-center">Ciudad</th><th class="text-center">Telefono</th><th>Tipo de Servicio</th><th>Pisos</th><th>Localizaci&oacute;n</th><th class="text-center">Activo</th></tr></thead>';
    send = {"cargarListaRestaurantes": 1};
    send.resultado = 0;
    send.rst_id = rst_id;
    send.cdn_id = cdn_id;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html = html + '<tr id="rst_id_' + datos[i]['rst_id'] + '" onclick="fn_seleccionarRestaurante(' + datos[i]['rst_id'] + ')" ondblclick="fn_modificar(' + datos[i]['rst_id'] + ')"><td>' + datos[i]['rst_descripcion'] + '</td><td>' + datos[i]['rst_direccion'] + '</td><td class="text-center">' + datos[i]['ciu_nombre'] + '</td><td class="text-center">' + datos[i]['rst_fono'] + '</td><td>' + datos[i]['tpsrv_descripcion'] + '</td><td>' + datos[i]['rst_numpiso'] + '</td><td>' + datos[i]['rst_localizacion'] + '</td><td class="text-center">';
                if (datos[i]['std_id'] == 'Activo') {
                    html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
                } else {
                    html = html + '<input type="checkbox" name="" value="1" disabled/>';
                }
                html = html + '</td></tr>';
            }
            $('#listaRestaurantes').html(html);
            $('#listaRestaurantes').dataTable({'destroy': true});
            $("#listaRestaurantes_length").hide();
            $("#listaRestaurantes_paginate").addClass('col-xs-10');
            $("#listaRestaurantes_info").addClass('col-xs-10');
            $("#listaRestaurantes_length").addClass('col-xs-6');
            fn_cargando(0);
        } else {
            html = html + '<tr><th colspan="8">No existen registros.</th></tr>';
            $('#listaRestaurantes').html(html);
            fn_cargando(0);
        }
    });
}

function fn_consultarListaRestaurantesActivos(rst_id) {
    var cdn_id = $("#sess_cdn_id").val();
    var html = '<thead><tr class="active"><th>Descripci&oacute;n Restaurante</th><th>Direccion</th><th class="text-center">Ciudad</th><th class="text-center">Telefono</th><th>Tipo de Servicio</th><th>Pisos</th><th>Localizaci&oacute;n</th><th class="text-center">Activo</th></tr></thead>';
    send = {"cargarListaRestaurantes": 1};
    send.resultado = 7;
    send.rst_id = rst_id;
    send.cdn_id = cdn_id;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html = html + '<tr id="rst_id_' + datos[i]['rst_id'] + '" onclick="fn_seleccionarRestaurante(' + datos[i]['rst_id'] + ')" ondblclick="fn_modificar(' + datos[i]['rst_id'] + ')"><td>' + datos[i]['rst_descripcion'] + '</td><td>' + datos[i]['rst_direccion'] + '</td><td class="text-center">' + datos[i]['ciu_nombre'] + '</td><td class="text-center">' + datos[i]['rst_fono'] + '</td><td>' + datos[i]['tpsrv_descripcion'] + '</td><td>' + datos[i]['rst_numpiso'] + '</td><td>' + datos[i]['rst_localizacion'] + '</td><td class="text-center">';
                if (datos[i]['std_id'] == 'Activo') {
                    html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
                } else {
                    html = html + '<input type="checkbox" name="" value="1" disabled/>';
                }
                html = html + '</td></tr>';
            }
            $('#listaRestaurantes').html(html);
            $('#listaRestaurantes').dataTable({'destroy': true});

            $("#listaRestaurantes_length").hide();
            $("#listaRestaurantes_paginate").addClass('col-xs-10');
            $("#listaRestaurantes_info").addClass('col-xs-10');
            $("#listaRestaurantes_length").addClass('col-xs-6');
            fn_cargando(0);
        } else {
            html = html + '<tr><th colspan="8">No existen registros.</th></tr>';
            $('#listaRestaurantes').html(html);
            fn_cargando(0);
        }
    });
}

function fn_consultarListaRestaurantesInactivos(rst_id) {
    var cdn_id = $("#sess_cdn_id").val();
    var html = '<thead><tr class="active"><th>Descripci&oacute;n Restaurante</th><th>Direccion</th><th class="text-center">Ciudad</th><th class="text-center">Telefono</th><th>Tipo de Servicio</th><th>Pisos</th><th>Localizaci&oacute;n</th><th class="text-center">Activo</th></tr></thead>';
    send = {"cargarListaRestaurantes": 1};
    send.resultado = 8;
    send.rst_id = rst_id;
    send.cdn_id = cdn_id;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html = html + '<tr id="rst_id_' + datos[i]['rst_id'] + '" onclick="fn_seleccionarRestaurante(' + datos[i]['rst_id'] + ')" ondblclick="fn_modificar(' + datos[i]['rst_id'] + ')"><td>' + datos[i]['rst_descripcion'] + '</td><td>' + datos[i]['rst_direccion'] + '</td><td class="text-center">' + datos[i]['ciu_nombre'] + '</td><td class="text-center">' + datos[i]['rst_fono'] + '</td><td>' + datos[i]['tpsrv_descripcion'] + '</td><td>' + datos[i]['rst_numpiso'] + '</td><td>' + datos[i]['rst_localizacion'] + '</td><td class="text-center">';
                if (datos[i]['std_id'] == 'Activo') {
                    html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
                } else {
                    html = html + '<input type="checkbox" name="" value="1" disabled/>';
                }
                html = html + '</td></tr>';
            }
            $('#listaRestaurantes').html(html);
            $('#listaRestaurantes').dataTable({'destroy': true});

            $("#listaRestaurantes_length").hide();
            $("#listaRestaurantes_paginate").addClass('col-xs-10');
            $("#listaRestaurantes_info").addClass('col-xs-10');
            $("#listaRestaurantes_length").addClass('col-xs-6');
            fn_cargando(0);
        } else {
            html = html + '<tr><th colspan="8">No existen registros.</th></tr>';
            $('#listaRestaurantes').html(html);
            fn_cargando(0);
        }
    });
}

function fn_cargarCiudades() {
    var html = '<option value="0">- Seleccionar Ciudad -</option>';
    send = {"cargarCiudades": 1};
    send.rst_id = 0;
    send.cdn_id = 0;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html = html + '<option value="' + datos[i]['ciu_id'] + '">' + datos[i]['ciu_nombre'] + '</option>';
            }
            $('#ciu_rst').html(html);
        }
    });
}

function fn_cargarCategoria() {
    var html = '<option value="00000000-0000-0000-0000-000000000000">- Seleccionar Categoria -</option>';
    send = {"cargarCategoria": 1};
    send.rst_id = 0;
    send.cdn_id = $("#sess_cdn_id").val();
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html = html + '<option value="' + datos[i]['IDCategoria'] + '">' + datos[i]['cat_descripcion'] + '</option>';
            }
            $('#cat_rst').html(html);
        }
    });
}

function fn_cargarTipoServicio() {
    var html = '<option value="0">- Seleccionar Tipo de Servicio -</option>';
    send = {"cargarTipoServicio": 1};
    send.rst_id = 0;
    send.cdn_id = 0;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html = html + '<option value="' + datos[i]['tpsrv_id'] + '">' + datos[i]['tpsrv_descripcion'] + '</option>';
            }
            $('#tpsrv_rst').html(html);
        }
    });
}

function fn_cargarTipoFacturacion() {
    var html = '<option value="0">- Seleccionar Tipo de Facturaci&oacute;n -</option>';
    send = {"cargarTipoFacturacion": 1};
    send.rst_id = 0;
    send.cdn_id = 0;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html = html + '<option value="' + datos[i]['tf_id'] + '">' + datos[i]['tf_descripcion'] + '</option>';
            }
            $('#tpfct_rst').html(html);
        }
    });
}

function fn_cargarPisos(rst_id) {
    var html = '';
    send = {"cargarPisos": 1};
    send.resultado = 5;
    send.rst_id = rst_id;
    send.cdn_id = 0;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html = html + '	<div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a style="width: 200px" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" onclick="fn_cargarAreas(\'' + datos[i]['pis_id'] + '\')" href="#collapse' + datos[i]['pis_id'] + '">Piso ' + datos[i]['pis_numero'] + '</a><b style="font-size: 12px;" class="pull-right">Est&aacute; Activo?: <input type="checkbox" id="chck_pis' + datos[i]['pis_id'] + '" onclick="fn_actualizarEstadoPiso(4, -1, ' + rst_id + ', \'' + datos[i]['pis_id'] + '\', 0, 0)"';
                if (datos[i]['std_id'] == 'Activo') {
                    html = html + ' checked></b></h4></div><div id="collapse' + datos[i]['pis_id'] + '" class="panel-collapse collapse"><div id="pnl' + datos[i]['pis_id'] + '" class="panel-body"></div></div></div>';
                } else {
                    html = html + '></b></h4></div><div id="collapse' + datos[i]['pis_id'] + '" class="panel-collapse collapse"><div id="pnl' + datos[i]['pis_id'] + '" class="panel-body"></div></div></div>';
                }
            }
            $('#accordion').html(html);
        }
    });
}

function fn_actualizarEstadoPiso(accion, resultado, rst_id, pis_id, arp_id, descripcion) {
    var std_id = 'Inactivo';
    if ($('#chck_pis' + pis_id).is(':checked')) {
        std_id = 'Activo';
    }

    send = {"administrarPisoArea": 1};
    send.accion = accion;
    send.resultado = resultado;
    send.rst_id = rst_id;
    send.pis_id = pis_id;
    send.arp_id = arp_id;
    send.std_id = std_id;
    send.descripcion = descripcion;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.Confirmar < 1) {
            alert("Error al actualizar el estado");
        }
    });
}

function fn_cargarAreas(pis_id) {
    var html = '<div class="row"><div class="col-md-1"></div><div class="col-xs-12 col-md-9"><button type="button" class="btn btn-default" aria-label="Left Align" onclick="fn_agregarArea(\'' + pis_id + '\')"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Area</button></div><div class="col-md-2"></div></div><div class="row"><div class="col-md-1"></div><div class="col-md-5"><h5><b>Area</h5></b></div><div class="col-md-1"><h5 class="text-center"><b>Activo</b></h5></div><div class="col-md-2"><h5 class="text-center"><b>Cargar Imagen</b></h5></div><div class="col-md-1"><h5 class="text-center"><b>Ver Imagen</b></h5></div><div class="col-md-2"></div></div>';
    send = {"cargarAreas": 1};
    send.resultado = 6;
    send.pis_id = pis_id;
    send.cdn_id = 0;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            $('#pnl' + pis_id).html(html);
            for (i = 0; i < datos.str; i++) {
                html = '<div class="row"><div class="col-md-1"></div><div class="col-md-5"><a href="#" style="font-size: 13px;" id="arp_dscrpcn' + datos[i]['arp_id'] + '">' + datos[i]['arp_descripcion'] + '</a></div><div class="col-md-1 text-center"><input type="checkbox" id="chck_' + datos[i]['arp_id'] + '" onclick="fn_actualizarEstadoArea(3, -1, 0,  \'' + pis_id + '\', \'' + datos[i]['arp_id'] + '\', 0)"';
                if (datos[i]['std_id'] == 'Activo') {
                    html = html + ' checked></div><div class="col-md-2 text-center"><span class="btn btn-default btn-file">Imagen<input id="file_' + datos[i]['arp_id'] + '"  multiple="false" type="file" onchange="fn_cargarImagen(\'' + datos[i]['arp_id'] + '\')"></span></div><div class="col-md-1"><a href="#myModal" class="btn btn-default" onclick="fn_obtenerImagen(\'' + datos[i]['arp_id'] + '\')" data-container="body" data-toggle="popover" data-placement="right" data-content=""><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a></div><div class="col-md-2"></div></div>';
                } else {
                    html = html + '></div><div class="col-md-2 text-center"><span class="btn btn-default btn-file">Imagen<input id="file_' + datos[i]['arp_id'] + '"  multiple="false" type="file" onchange="fn_cargarImagen(\'' + datos[i]['arp_id'] + '\')"></span></div><div class="col-md-1"><a href="#myModal" class="btn btn-default" onclick="fn_obtenerImagen(\'' + datos[i]['arp_id'] + '\')" data-container="body" data-toggle="popover" data-placement="right" data-content=""><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a></div><div class="col-md-2"></div></div>';
                }
                $('#pnl' + pis_id).append(html);
                $('#arp_dscrpcn' + datos[i]['arp_id']).editable({
                    type: 'text',
                    url: '../adminRestaurante/config_restaurante.php',
                    pk: 'modificarDescripcionArea',
                    title: 'Descripcion',
                    ajaxOptions: {
                        type: 'GET',
                        dataType: 'json'
                    },

                    validate: function(pk) {
                        if($.trim(pk) == '') {
                            return 'Campo requerido';
                        }
                    }


                    ,
                    success: function (response, newValue) {
                        if (!response.Confirmar > 0) {
                            return true;
                        } else {
                            return response.msg;
                        }
                    }
                });
            }
        } else {
            html = '<div class="row"><div class="col-md-1"></div><div class="col-xs-12 col-md-9"><button type="button" class="btn btn-default" aria-label="Left Align" onclick="fn_agregarArea(\'' + pis_id + '\')"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Area</button></div><div class="col-md-2"></div></div><br/><div class="row"><div class="col-md-1"></div><div class="col-md-11">No existen areas</div></div>';
            $('#pnl' + pis_id).html(html);
        }
    });
}

function fn_obtenerImagen(arp_id) {
    fn_cargando(1);
    send = {"obtenerImagen": 1};
    send.arp_id = arp_id;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminRestaurante/config_leerimagenarea.php", data: send,
        success: function (datos) {
            $('#visor_img').html('<img width="700px" height="500px" src="data:image/png;base64,' + datos.imagen + '"/>');
            fn_visor(1);
        },complete: function (jqXHR, textStatus) {
            fn_cargando(0);
        }, 
        error: function () {
            alert('Error lectura de imagen');
        }
    });
}

function fn_cargarImagen(arp_id) {
    /*Mostrar imagen cargado, Funciona*/
    fn_cargando(1);
    var fileInput = document.getElementById("file_" + arp_id);
    var canvas = document.getElementById("micanvas");
    var ctx = canvas.getContext("2d");
    var img = new Image();
    img.src = URL.createObjectURL(fileInput.files[0]);
    ctx.drawImage(img, 0, 0);
    img.onload = function () {
        ctx.drawImage(img, 0, 0);
    };

    var img = new Image();
    img.src = URL.createObjectURL(fileInput.files[0]);
    img.onload = function () {
        var canvas = document.createElement("canvas");
        canvas.width = this.width;
        canvas.height = this.height;
        var ctx = canvas.getContext("2d");
        ctx.drawImage(this, 0, 0);
        var dataURL = canvas.toDataURL("image/png");
        $('#txt_area_imagen').html(dataURL.replace(/^data:image\/(png|jpg);base64,/, ""));
        var data = new FormData();
        data.append("imagen", $('#txt_area_imagen').html());
        data.append("modificarImagenArea", 1);
        data.append("arp_id", arp_id);
        $.ajax({async: false, type: "POST", dataType: "json", contentType: false, url: "../adminRestaurante/config_imagenarea.php", data: data, processData: false, cache: false,
            success: function (datos) {
            },
            complete: function (jqXHR, textStatus) {
                fn_cargando(0);
            }
        });
    };
}

var Base64Binary = {
    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
    decodeArrayBuffer: function (input) {
        var bytes = (input.length / 4) * 3;
        var ab = new ArrayBuffer(bytes);
        this.decode(input, ab);
        return ab;
    },
    removePaddingChars: function (input) {
        var lkey = this._keyStr.indexOf(input.charAt(input.length - 1));
        if (lkey == 64) {
            return input.substring(0, input.length - 1);
        }
        return input;
    },
    decode: function (input, arrayBuffer) {
        //get last chars to see if are valid
        input = this.removePaddingChars(input);
        input = this.removePaddingChars(input);

        var bytes = parseInt((input.length / 4) * 3, 10);
        var uarray;
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;
        var j = 0;
        if (arrayBuffer)
            uarray = new Uint8Array(arrayBuffer);
        else
            uarray = new Uint8Array(bytes);

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        for (i = 0; i < bytes; i += 3) {
            //get the 3 octects in 4 ascii chars
            enc1 = this._keyStr.indexOf(input.charAt(j++));
            enc2 = this._keyStr.indexOf(input.charAt(j++));
            enc3 = this._keyStr.indexOf(input.charAt(j++));
            enc4 = this._keyStr.indexOf(input.charAt(j++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            uarray[i] = chr1;
            if (enc3 != 64)
                uarray[i + 1] = chr2;
            if (enc4 != 64)
                uarray[i + 2] = chr3;
        }
        return uarray;
    }
};

function fn_actualizarEstadoArea(accion, resultado, rst_id, pis_id, arp_id, descripcion) {

    var std_id = 'Inactivo';

    if ($('#chck_' + arp_id).is(':checked')) {
        std_id = 'Activo';
    }

    send = {"administrarPisoArea": 1};
    send.accion = accion;
    send.resultado = resultado;
    send.rst_id = rst_id;
    send.pis_id = pis_id;
    send.arp_id = arp_id;
    send.std_id = std_id;
    send.descripcion = descripcion;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.Confirmar < 1) {
            alert("Error al actualizar el estado");
        }
    });
}

function fn_agregarArea(pis_id) {
    var html = '<div class="row"><div class="col-md-1"></div><div class="col-xs-12 col-md-9"><button type="button" class="btn btn-default" aria-label="Left Align" onclick="fn_agregarArea(\'' + pis_id + '\')"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Area</button></div><div class="col-md-2"></div></div><div class="row"><div class="col-md-1"></div><div class="col-md-5"><h5><b>Area</h5></b></div><div class="col-md-1"><h5 class="text-center"><b>Activo</b></h5></div><div class="col-md-2"><h5 class="text-center"><b>Cargar Imagen</b></h5></div><div class="col-md-1"><h5 class="text-center"><b>Ver Imagen</b></h5></div><div class="col-md-2"></div></div>';
    var rst_id = $('#listaRestaurantes').find("tr.success").attr("id");
    rst_id = rst_id.substring(7, rst_id.length);
    send = {"agregarArea": 1};
    send.accion = 1;
    send.resultado = 1;
    send.rst_id = rst_id;
    send.pis_id = pis_id;
    send.arp_id = 0;
    send.std_id = 0;
    send.descripcion = '';
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            $('#pnl' + pis_id).html(html);
            for (i = 0; i < datos.str; i++) {
                html = '<div class="row"><div class="col-md-1"></div><div class="col-md-5"><a href="#" style="font-size: 13px;" id="arp_dscrpcn' + datos[i]['arp_id'] + '">' + datos[i]['arp_descripcion'] + '</a></div><div class="col-md-1 text-center"><input type="checkbox" id="chck_' + datos[i]['arp_id'] + '" onclick="fn_actualizarEstadoArea(3, -1, 0, \'' + pis_id + '\', \'' + datos[i]['arp_id'] + '\', 0)"';
                if (datos[i]['std_id'] == 'Activo') {
                    html = html + ' checked></div><div class="col-md-2 text-center"><span class="btn btn-default btn-file">Imagen<input id="file_' + datos[i]['arp_id'] + '"  multiple="false" type="file" onchange="fn_cargarImagen(\'' + datos[i]['arp_id'] + '\')"></span></div><div class="col-md-1"><button type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="right" data-content=""><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button></div><div class="col-md-2"></div></div>';
                } else {
                    html = html + '></div><div class="col-md-2 text-center"><span class="btn btn-default btn-file">Imagen<input id="file_' + datos[i]['arp_id'] + '"  multiple="false" type="file" onchange="fn_cargarImagen(\'' + datos[i]['arp_id'] + '\')"></span></div><div class="col-md-1"><button type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="right" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus."><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button></div><div class="col-md-2"></div></div>';
                }
                $('#pnl' + pis_id).append(html);
                $('#arp_dscrpcn' + datos[i]['arp_id']).editable({
                    type: 'text',
                    url: '../adminRestaurante/config_restaurante.php',
                    pk: 'modificarDescripcionArea',
                    title: 'Descripcion Area',
                    ajaxOptions: {
                        type: 'GET',
                        dataType: 'json'
                    },
                    validate: function(pk) {
                        if($.trim(pk) == '') {
                            return 'Campo requerido';
                        }
                    },
                    success: function (response, newValue) {
                        if (!response.Confirmar > 0) {
                            return true;
                        } else {
                            return response.msg;
                        }
                    }
                });
            }
        } else {
            html = '<div class="row"><div class="col-md-1"></div><div class="col-xs-12 col-md-9"><button type="button" class="btn btn-default" aria-label="Left Align" onclick="fn_agregarArea(\'' + pis_id + '\')"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Area</button></div><div class="col-md-2"></div></div><br/><div class="row"><div class="col-md-1"></div><div class="col-md-11">No existen areas</div></div>';
            $('#pnl' + pis_id).html(html);
        }
    });
}

function fn_agregarPiso() {
    var html = '';
    var rst_id = $('#listaRestaurantes').find("tr.success").attr("id");
    rst_id = rst_id.substring(7, rst_id.length);
    send = {"agregarPiso": 1};
    send.accion = 0;
    send.resultado = 0;
    send.rst_id = rst_id;
    send.pis_id = 0;
    send.arp_id = 0;
    send.std_id = 0;
    send.descripcion = '';
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html = html + '	<div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" onclick="fn_cargarAreas(\'' + datos[i]['pis_id'] + '\')" href="#collapse' + datos[i]['pis_id'] + '">Piso ' + datos[i]['pis_numero'] + '</a><b style="font-size: 12px;" class="pull-right">Est&aacute; Activo?: <input type="checkbox" id="chck_pis' + datos[i]['pis_id'] + '" onclick="fn_actualizarEstadoPiso(4, -1, ' + rst_id + ', \'' + datos[i]['pis_id'] + '\', 0, 0)"';
                if (datos[i]['std_id'] == 'Activo') {
                    html = html + ' checked></b></h4></div><div id="collapse' + datos[i]['pis_id'] + '" class="panel-collapse collapse"><div id="pnl' + datos[i]['pis_id'] + '" class="panel-body"></div></div></div>';
                } else {
                    html = html + '></b></h4></div><div id="collapse' + datos[i]['pis_id'] + '" class="panel-collapse collapse"><div id="pnl' + datos[i]['pis_id'] + '" class="panel-body"></div></div></div>';
                }
            }
            $('#accordion').html(html);
        }
    });
}

function fn_cargarMetodosCalculoImpuestos() {
    var html = '<option value="0">- Seleccionar M&eacute;todo -</option>';
    send = {"cargarMetodoCalculoImpuesto": 1};
    send.cdn_id = $("#sess_cdn_id").val();
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html = html + '<option value="' + datos[i]['id'] + '">' + datos[i]['descripcion'] + '</option>';
            }
            $('#tp_mtd_mpst_rst').html(html);
        }
    });
}

function fn_cargarImpuestosCadena() {
    var html = '';
    send = {"cargarImpuestosCadena": 1};
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html = html + '<option value="' + datos[i]['id'] + '">' + datos[i]['descripcion'] + '</option>';
            }
            $('#slct_mpsts_rstrnt').html(html);
            $('.chosen-select').chosen();
            $("#slct_mpsts_rstrnt_chosen").css('width', '480');
        }
    });
}

function fn_cargarImpuestosRestaurante(rst_id) {
    var impuestos = [];
    var html = '';
    send = {"cargarImpuestosRestaurante": 1};
    send.rst_id = rst_id;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                impuestos.push(datos[i]['id']);
            }
            $('#slct_mpsts_rstrnt').val(impuestos).trigger("chosen:updated.chosen");
        }
    });
}

function fn_seleccionarRestaurante(rst_id) {
    $("#listaRestaurantes tr").removeClass("success");
    $("#rst_id_" + rst_id).addClass("success");
    $("#slccn_rst_id").val(rst_id);
}

function fn_validarImpuestosIE() {
    if ($("#tp_mtd_mpst_rst option:selected").text() == 'INCLUIDO' || $("#tp_mtd_mpst_rst option:selected").text() == 'EXCLUIDO') {
        $("#slct_mpsts_rstrnt").val("");
        $('#slct_mpsts_rstrnt').trigger("chosen:updated");
    }
}


function fn_modificar(rst_id) {
    $('#modal').modal('show');
    $("#pestanas li").removeClass("active");
    $('#tag_informacion').addClass('active');
    $("#contenedor_gestion div").removeClass("active");
    $('#informacion').addClass('active');
    $('#cnt_lst_aut_rst').css('display', 'block');
    $('#cnt_frm_nv_nea_aut_rst').css('display', 'none');
    $('#cnt_frm_nv_aut_rst').css('display', 'none');
    fn_cargaTipoDocumento(rst_id);
    fn_consultarInformacionRestaurante(rst_id);
    fn_cargarPisos(rst_id);
    fn_cargarAutorizacionesRestaurante(rst_id);
    $("#slct_mpsts_rstrnt").val('');
    $('#slct_mpsts_rstrnt').trigger("chosen:updated");
    fn_cargarImpuestosRestaurante(rst_id);

    //COLECCIONES
    fn_cargaRestauranteColecciondeDatos(rst_id);
    $("#seleccion_factura").val('');
    $('#seleccion_factura').trigger("chosen:updated");
    $("#seleccion_voucher").val('');
    $('#seleccion_voucher').trigger("chosen:updated");
    $("#seleccion_linea").val('');
    $('#seleccion_linea').trigger("chosen:updated");
    fn_cargarTipoDocumentoRestaurante(rst_id);
}

function fn_validarCampoVacio(valor) {
    if (valor.length > 0) {
        return true;
    } else {
        return false;
    }
}

function fn_visor(estado) {
    if (estado) {
        $('#visor').css('display', 'block');
        $('#visor_img').css('display', 'block');
    } else {
        $('#visor').css('display', 'none');
        $('#visor_img').css('display', 'none');
    }
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

function fn_btn(boton, estado) {
    if (estado) {
        $("#btn_" + boton).css("background", "url('../../imagenes/admin_resources/" + boton + ".png') 14px 4px no-repeat");
        $("#btn_" + boton).removeAttr("disabled");
        $("#btn_" + boton).addClass("botonhabilitado");
        $("#btn_" + boton).removeClass("botonbloqueado");
    } else {
        $("#btn_" + boton).css("background", "url('../../imagenes/admin_resources/" + boton + "_bloqueado.png') 14px 4px no-repeat");
        $("#btn_" + boton).prop('disabled', true);
        $("#btn_" + boton).addClass("botonbloqueado");
        $("#btn_" + boton).removeClass("botonhabilitado");
    }
}

//COLECCION RESTAURANTE
function fn_cargaRestauranteColecciondeDatos(rst_id)
{
    $("#IDRestaurante").val(rst_id);
    var html = '<tr class="bg-primary"><th class="text-center col-md-5" style="width: 30%;">Descripci&oacute;n</th><th class="text-center col-md-5">Dato</th><th class="text-center col-md-7">Especifica Valor</th><th>Obligatorio</th><th class="text-center col-md-5">Tipo de Dato</th><th class="text-center">Valor</th><th>Activo</th></tr>';
    send = {"administrarColeccionRestaurante": 1};
    send.accion = 'C';
    send.rst_id = rst_id;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var valorPolitica = evaluarValorPolitica(datos[i]);
                html += '<tr id="' + i + '" onclick="fn_seleccionarColeccion(' + i + ',\'' + datos[i]['ID_ColeccionRestaurante'] + '\', \'' + datos[i]['descripcion_coleccion'] + '\', \'' + datos[i]['ID_ColeccionDeDatosRestaurante'] + '\')"  class="text-center"><td>' + datos[i]['descripcion_coleccion'] + '</td><td>' + datos[i]['descripcion_dato'] + '</td>';
                if (datos[i]['especificarValor'] === 1) {
                    html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                } else {
                    html += '<td><input type="checkbox" value="1" disabled/></td>';
                }

                if (datos[i]['obligatorio'] === 1) {
                    html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                } else {
                    html += '<td><input type="checkbox" value="1" disabled/></td>';
                }

                html += '<td>' + datos[i]['tipodedato'] + '</td><td>' + valorPolitica + '</td>';

                if (datos[i]['isActive'] === 1) {
                    html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                } else {
                    html += '<td><input type="checkbox" value="1" disabled/></td>';
                }
                html += '</tr>';
            }
            $("#restaurante_coleccion").html(html);

        } else {
            html = html + '<tr><th colspan="14" class="text-center">No existen registros.</th></tr>';
            $("#restaurante_coleccion").html(html);
        }
    });
}

function evaluarValorPolitica(fila) {
    var tipoDato = fila.tipodedato.trim();
    switch (tipoDato) {
        case 'Entero':
            return fila.entero;
            break;
        case 'Caracter':
            return fila.caracter;
            break;
        case 'Seleccion':
            return fila.bitt;
            break;
        case 'Numerico':
            return fila.numerico;
            break;
        case 'Fecha':
            return fila.fecha;
            break;
        case 'Fecha Inicio-Fin':
            return fila.fechaIni + ' - ' + fila.fechaFin;
            break;
        case 'Minimo-Maximo':
            return fila.min + ' - ' + fila.max;
            break;
        default:
            return fila.caracter;
            break;
        //code block
    }
}
function fn_seleccionarColeccion(filaA, IDColeccion, nombreColeccion, IDColecciondeDatos) {
    $("#restaurante_coleccion tr").removeClass("success");
    $("#" + filaA + "").addClass("success");
    lc_nombreColeccion = nombreColeccion;
    lc_IDColeccionRestaurante_edit = IDColeccion;
    lc_IDColeccionDeDatosRestaurante_edit = IDColecciondeDatos;
}

function fn_limpiarcampos() {
    $('#tipo_varchar').val('');
    $('#tipo_entero').val('');
    $('#tipo_fecha').val('');
    $('#tipo_numerico').val('');
    $('#FechaInicial').val('');
    $('#FechaFinal').val('');
    $('#rango_minimo').val('');
    $('#rango_maximo').val('');
}

function fn_nuevaColeccion() {
    $("#tipos_de_dato").hide();
    $('#mdl_nuevaColeccion').modal('show');
    $('#modal').modal('hide');
    fn_DetalleColeccion();
    fn_limpiarcampos();
}

function fn_verModal() {
    $('#mdl_nuevaColeccion').modal('hide');
    $('#modal').modal('show');

}

function fn_DetalleColeccion()
{
    var rest = $("#IDRestaurante").val();
    var html = '<tr class="bg-primary"><th class="text-center">Descripci&oacute;n</th></tr>';
    send = {"detalleColeccionRestaurante": 1};
    send.accion = 'D';
    send.rst_id = rest;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += '<tr id="' + i + 'det_id' + '" onclick="fn_seleccionarDetalleColeccion(' + i + ',\'' + datos[i]['ID_ColeccionRestaurante'] + '\',\'' + datos[i]['Descripcion'] + '\')"  class="text-left"><td>' + datos[i]['Descripcion'] + '</td>';
                html += '</tr>';
            }
            $("#coleccion_descripcion").html(html);
        } else {
            html = html + '<tr><th colspan="6" class="text-center">No existen registros.</th></tr>';
            $("#coleccion_descripcion").html(html);
        }
    });
}

function fn_seleccionarDetalleColeccion(filaB, IDDetalleColeccion, descripcion) {
    $("#coleccion_descripcion tr").removeClass("success");
    $("#" + filaB + 'det_id' + "").addClass("success");
    fn_DatosColeccion(IDDetalleColeccion);
    $('#nombreColeccion').text(descripcion);
}

function fn_DatosColeccion(IDDetalleColeccion) {
    var rest = $("#IDRestaurante").val();
    var html = '<tr class="bg-primary"><th class="text-center">Datos</th></tr>';
    send = {"datosColeccionRestaurante": 1};
    send.accion = 'E';
    send.rst_id = rest;
    send.IDColeccionRestaurante = IDDetalleColeccion;
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += '<tr id="' + i + 'dat_id' + '" onclick="fn_seleccionarDatosColeccion(' + i + ',\'' + datos[i]['ID_ColeccionDeDatosRestaurante'] + '\',' + datos[i]['especificarValor'] + ',' + datos[i]['obligatorio'] + ',\'' + datos[i]['tipodedato'] + '\')" class="text-left"><td>' + datos[i]['datos'] + '</td>';
                html += '</tr>';
            }
            $("#coleccion_datos").html(html);
        } else {
            html = html + '<tr><th colspan="6" class="text-center">No existen registros.</th></tr>';
            $("#coleccion_datos").html(html);
        }
    });

    lc_IDColeccionRestaurante = IDDetalleColeccion;
}

function fn_seleccionarDatosColeccion(filaC, IDDatosColeccion, especifica, obligatorio, tipo) {
    $("#coleccion_datos tr").removeClass("success");
    $("#" + filaC + 'dat_id' + "").addClass("success");
    $("#tipos_de_dato").show();

    if (especifica === 1) {
        $("#check_especifica").prop("checked", true);
    } else {
        $("#check_especifica").prop("checked", false);
    }

    if (obligatorio === 1) {
        $("#check_obligatorio").prop("checked", true);
    } else {
        $("#check_obligatorio").prop("checked", false);
    }

    $("#lbl_tipoDato").text(tipo);

    $('#tipo_fecha').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY HH:mm', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $('#FechaInicial').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY HH:mm', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $('#FechaFinal').daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY HH:mm', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $("#tipo_bit").bootstrapSwitch('state', true);

    lc_IDColeccionDeDatosRestaurante = IDDatosColeccion;
}

function fn_guardarRestauranteColeccion() {
    Accion = 'I';
    var rest = $("#IDRestaurante").val();
    var tipo_entero = 0;
    var tipo_numerico = 0;
    var rango_minimo = 0;
    var rango_maximo = 0;

    if ($("#tipo_entero").val() === '') {
        tipo_entero = 'null';
    } else {
        tipo_entero = $("#tipo_entero").val();
    }

    estado = $("#tipo_bit").bootstrapSwitch('state');
    if (estado === true) {
        var tipo_bit = 1;
    } else {
        var tipo_bit = 0;
    }

    if ($("#tipo_numerico").val() === '') {
        tipo_numerico = 'null';
    } else {
        tipo_numerico = $("#tipo_numerico").val();
    }

    if ($("#rango_minimo").val() === '') {
        rango_minimo = 'null';
    } else {
        rango_minimo = $("#rango_minimo").val();
    }

    if ($("#rango_maximo").val() === '') {
        rango_maximo = 'null';
    } else {
        rango_maximo = $("#rango_maximo").val();
    }

    send = {"guardarRestauranteColeccion": 1};
    send.accion = Accion;
    send.IDColecciondeDatosRestaurante = lc_IDColeccionDeDatosRestaurante;
    send.IDColeccionRestaurante = lc_IDColeccionRestaurante;
    send.IDRestaurante = rest;
    send.varchar = $("#tipo_varchar").val();
    send.entero = tipo_entero;
    send.fecha = $("#tipo_fecha").val();
    send.seleccion = tipo_bit;
    send.numerico = tipo_numerico;
    send.fecha_inicio = $("#FechaInicial").val();
    send.fecha_fin = $("#FechaFinal").val();
    send.minimo = rango_minimo;
    send.maximo = rango_maximo;
    send.IDUsuario = $("#sess_usr_id").val();
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            alertify.success("Datos guardados correctamente.");
            fn_cargaRestauranteColecciondeDatos(rest);
            $('#mdl_nuevaColeccion').modal('hide');
            $('#modal').modal('show');
        } else {
            var error = datos.str;
            error = error.substr(54, 40);
            alertify.error(error);
        }
    });
}

function fn_editColeccionRestaurante()
{
    var IDColeccion = $('#restaurante_coleccion').find("tr.success").attr("id");
    if (IDColeccion)
    {
        var Accion = 'F';
        var rest = $("#IDRestaurante").val();
        $('#mdl_editColeccion').modal('show');
        $('#modal').modal('hide');
        $('#edit_nombreColeccion').text(lc_nombreColeccion);

        $('#tipo_fecha_edit').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY HH:mm', drops: 'up'}, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });

        $('#FechaInicial_edit').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY HH:mm', drops: 'up'}, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });

        $('#FechaFinal_edit').daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY HH:mm', drops: 'up'}, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });

        $("#tipo_bit_edit").bootstrapSwitch('state', true);

        send = {"cargaRestauranteColeccion_edit": 1};
        send.accion = Accion;
        send.rst_id = rest;
        send.IDColeccionRestaurante = lc_IDColeccionRestaurante_edit;
        send.IDColecciondeDatosRestaurante = lc_IDColeccionDeDatosRestaurante_edit;
        $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
            if (datos.str > 0) {
                if (datos[0].estado === 1) {
                    $("#check_estado").prop('checked', true);
                } else {
                    $("#check_estado").prop('checked', false);
                }

                if (datos[0].especificarValor === 1) {
                    $("#edit_check_especifica").prop('checked', true);
                } else {
                    $("#edit_check_especifica").prop('checked', false);
                }

                if (datos[0].obligatorio === 1) {
                    $("#edit_check_obligatorio").prop('checked', true);
                } else {
                    $("#edit_check_obligatorio").prop('checked', false);
                }

                $('#edit_lbl_tipoDato').text(datos[0]['tipodedato']);
                $('#tipo_varchar_edit').val(datos[0]['caracter']);
                $("#tipo_entero_edit").val(datos[0]['entero']);
                $('#tipo_fecha_edit').val(datos[0]['fecha']);
                $('#tipo_numerico_edit').val(datos[0]['numerico']);
                $('#FechaInicial_edit').val(datos[0]['fechaInicio']);
                $('#FechaFinal_edit').val(datos[0]['fechaFin']);
                $('#rango_minimo_edit').val(datos[0]['minimo']);
                $('#rango_maximo_edit').val(datos[0]['maximo']);

                if (datos[0]['seleccion'] === 1) {
                    $("#tipo_bit_edit").bootstrapSwitch('state', true);
                } else {
                    $("#tipo_bit_edit").bootstrapSwitch('state', false);
                }
            }
        });
    } else {
        alertify.error("Debe Seleccionar un Registro.");
    }
}

function fn_modificaRestauranteColeccion()
{
    Accion = 'U';
    var rest = $("#IDRestaurante").val();
    var tipo_entero = 0;
    var tipo_numerico = 0;
    var rango_minimo = 0;
    var rango_maximo = 0;

    if ($("#tipo_entero_edit").val() === '') {
        tipo_entero = 'null';
        fn_guardarStorageIntentosNull();
    } else {
        tipo_entero = $("#tipo_entero_edit").val();
    }

    seleccion = $("#tipo_bit_edit").bootstrapSwitch('state');
    if (seleccion === true) {
        var tipo_bit_edit = 1;
    } else {
        var tipo_bit_edit = 0;
    }

    if ($("#tipo_numerico_edit").val() === '') {
        tipo_numerico = 'null';
    } else {
        tipo_numerico = $("#tipo_numerico_edit").val();
    }

    if ($("#rango_minimo_edit").val() === '') {
        rango_minimo = 'null';
    } else {
        rango_minimo = $("#rango_minimo_edit").val();
    }

    if ($("#rango_maximo_edit").val() === '') {
        rango_maximo = 'null';
    } else {
        rango_maximo = $("#rango_maximo_edit").val();
    }

  
    send = {"modificarRestauranteColeccion": 1};
    send.accion = Accion;
    send.IDColecciondeDatosRestaurante = lc_IDColeccionDeDatosRestaurante_edit;
    send.IDColeccionRestaurante = lc_IDColeccionRestaurante_edit;
    send.IDRestaurante = rest;
    send.varchar = $("#tipo_varchar_edit").val();
    send.entero = tipo_entero;
    send.fecha = $("#tipo_fecha_edit").val();
    send.seleccion = tipo_bit_edit;
    send.numerico = tipo_numerico;
    send.fecha_inicio = $("#FechaInicial_edit").val();
    send.fecha_fin = $("#FechaFinal_edit").val();
    send.minimo = rango_minimo;
    send.maximo = rango_maximo;
    send.IDUsuario = $("#sess_usr_id").val();
    if ($("#check_estado").is(':checked')) {
        send.estado = 1;
    } else {
        send.estado = 0;
    }
    $.getJSON("../adminRestaurante/config_restaurante.php", send, function (datos) {
        if (datos.str > 0) {
            alertify.success("Datos actualizados correctamente.");
            fn_cargaRestauranteColecciondeDatos(rest);
            $('#mdl_editColeccion').modal('hide');
            $('#modal').modal('show');

        } else {
            var error = datos.str;
            error = error.substr(54, 40);
            alertify.error(error);
        }
    });
}

function aMays(e, elemento) {
    tecla = (document.all) ? e.keyCode : e.which;
    elemento.value = elemento.value.toUpperCase();
}

//COLECCION IMPRESION TIPO DE DOCUMENTO 
function fn_cargaTipoDocumento(rst_id) {
    Accion = 'D';
    var html = '';

    send = {"cargarTipoDocumento": 1};
    send.accion = Accion;
    send.rst_id = rst_id;
    send.cadena = $("#sess_cdn_id").val();
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded",
        url: "../adminRestaurante/config_restaurante.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    html = html + '<option value="' + datos[i]['ID_ColeccionDeDatosRestaurante'] + '">' + datos[i]['Tipo_Documento'] + '</option>';
                }
                $('#seleccion_factura').html(html);
                $('.chosen-select').chosen();
                $("#seleccion_factura_chosen").css('width', '500');

                $('#seleccion_voucher').html(html);
                $('.chosen-select').chosen();
                $("#seleccion_voucher_chosen").css('width', '500');

                $('#seleccion_linea').html(html);
                $('.chosen-select').chosen();
                $("#seleccion_linea_chosen").css('width', '500');


            }
        }
    });
}

function fn_cargarTipoDocumentoRestaurante(rst_id) {
    fn_cargarTipoDocumentoF(rst_id);
    fn_cargarTipoDocumentoV(rst_id);
    fn_cargarTipoDocumentoL(rst_id);
}

function fn_cargarTipoDocumentoF(rst_id)
{
    Accion = 'F';
    var arrayImpresion = [];

    send = {"cargarTipoDocumentoFactura": 1};
    send.accion = Accion;
    send.rst_id = rst_id;
    send.cadena = $("#sess_cdn_id").val();
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded",
        url: "../adminRestaurante/config_restaurante.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    arrayImpresion.push(datos[i]['IDTipoDocumento']);
                }
                $('#seleccion_factura').val(arrayImpresion).trigger("chosen:updated.chosen");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_cargarTipoDocumentoV(rst_id) {
    Accion = 'V';
    var arrayImpresion = [];

    send = {"cargarTipoDocumentoVoucher": 1};
    send.accion = Accion;
    send.rst_id = rst_id;
    send.cadena = $("#sess_cdn_id").val();
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded",
        url: "../adminRestaurante/config_restaurante.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    arrayImpresion.push(datos[i]['IDTipoDocumento']);

                }
                $('#seleccion_voucher').val(arrayImpresion).trigger("chosen:updated.chosen");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });

}

function fn_cargarTipoDocumentoL(rst_id) {
    Accion = 'L';
    var arrayImpresion = [];

    send = {"cargarTipoDocumentoLinea": 1};
    send.accion = Accion;
    send.rst_id = rst_id;
    send.cadena = $("#sess_cdn_id").val();
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded",
        url: "../adminRestaurante/config_restaurante.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    arrayImpresion.push(datos[i]['IDTipoDocumento']);
                }
                $('#seleccion_linea').val(arrayImpresion).trigger("chosen:updated.chosen");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

//********************************************************************************//
//Función que valida que el Tipo de documento no este en otro canal de impresión  //
//********************************************************************************//
function fn_validaTipoDocumentoCanalImpresion() {
    if ($('#seleccion_factura').val() === null) {
        $("#seleccion_voucher option:selected").each(function () {
            valorV = $(this).attr('value');
            $("#seleccion_linea option:selected").each(function () {
                valorL = $(this).attr('value');
                if (valorV === valorL) {
                    VALIDA_TIPO_DOCUMENTO_FALSE = 0;
                    return false;
                } else {
                    VALIDA_TIPO_DOCUMENTO = 1;
                }
            });
        });
    } else {
        if ($('#seleccion_voucher').val() === null) {
            $("#seleccion_factura option:selected").each(function () {
                valorF = $(this).attr('value');
                $("#seleccion_linea option:selected").each(function () {
                    valorL = $(this).attr('value');
                    if (valorF === valorL) {
                        VALIDA_TIPO_DOCUMENTO_FALSE = 0;
                        return false;
                    } else {
                        VALIDA_TIPO_DOCUMENTO = 1;
                    }
                });
            });
        } else {
            if ($('#seleccion_linea').val() === null) {
                $("#seleccion_factura option:selected").each(function () {
                    valorF = $(this).attr('value');
                    $("#seleccion_voucher option:selected").each(function () {
                        valorV = $(this).attr('value');

                        if (valorF === valorV) {
                            VALIDA_TIPO_DOCUMENTO_FALSE = 0;
                            return (false);
                        } else {
                            //alert("true");
                            VALIDA_TIPO_DOCUMENTO = 1;
                        }
                    });
                });
            } else {
                if ($('#seleccion_linea').val() !== null && $('#seleccion_factura').val() !== null && $('#seleccion_voucher').val() !== null) {
                    $("#seleccion_factura option:selected").each(function () {
                        valorF = $(this).attr('value');
                        $("#seleccion_voucher option:selected").each(function () {
                            valorV = $(this).attr('value');
                            if (valorF === valorV) {
                                VALIDA_TIPO_DOCUMENTO_FALSE = 0;
                                return false;
                            } else {
                                VALIDA_TIPO_DOCUMENTO = 1;
                            }
                        });
                    });

                    $("#seleccion_factura option:selected").each(function () {
                        valorF = $(this).attr('value');
                        $("#seleccion_linea option:selected").each(function () {
                            valorL = $(this).attr('value');
                            if (valorF === valorL) {
                                VALIDA_TIPO_DOCUMENTO_FALSE = 0;
                                return false;
                            } else {
                                VALIDA_TIPO_DOCUMENTO = 1;
                            }
                        });
                    });

                    $("#seleccion_voucher option:selected").each(function () {
                        valorV = $(this).attr('value');
                        $("#seleccion_linea option:selected").each(function () {
                            valorL = $(this).attr('value');
                            if (valorV === valorL) {
                                VALIDA_TIPO_DOCUMENTO_FALSE = 0;
                                return false;
                            } else {
                                VALIDA_TIPO_DOCUMENTO = 1;
                            }
                        });
                    });
                }
            }
        }
    }
}

function fn_guardarImpresionTipoDocumentos() {
    var rst_id = $("#slccn_rst_id").val();

    //alert($('#seleccion_voucher').val());
    if ($('#seleccion_factura').val() == null) {
        tipodocumentoF = 0;
    } else {
        tipodocumentoF = $('#seleccion_factura').val();
    }
    if ($('#seleccion_voucher').val() == null) {
        tipodocumentoV = 0;
    } else {
        tipodocumentoV = $('#seleccion_voucher').val();
    }
    if ($('#seleccion_linea').val() == null) {
        tipodocumentoL = 0;
    } else {
        tipodocumentoL = $('#seleccion_linea').val();
    }
    send = {"guardarImpresionTipoDocumentos": 1};
    send.rst_id = rst_id;
    send.cadena = $("#sess_cdn_id").val();
    send.tipodocumentoF = tipodocumentoF;
    send.tipodocumentoV = tipodocumentoV;
    send.tipodocumentoL = tipodocumentoL;
    send.usuario = $('#sess_usr_id').val();

    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminRestaurante/config_restaurante.php", data: send,
        success: function (datos) {
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }});
}

function sincronizarRestaurantes () {
    send = {};
    send.metodo = "sincronizarRestaurantes";
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminRestaurante/consumoWSRestaurantes.php", data: send,
        success: function (datos) {
            alertify.success("Sincronización realizada exitosamente");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error("Sincronización no pudo realizarse");
        }
    });
}

function fn_guardarStorageIntentos(tipo_entero_edit) {
    var x = $("#tipo_entero_edit").val();
    localStorage.removeItem("intValida");
    localStorage.setItem("intValida", x);
    localStorage.setItem("intValidaU", x);
    localStorage.setItem("intValidaOP", x);
    localStorage.setItem("intValidaFC", x);
    localStorage.setItem("intValidaCC", x);
    //////LOCAL STORAGE BASE PARA GUARDAR EMAILS Y VALIDAR
    var array_emails = ["email@email.com"];
    localStorage.setItem('email_valid', JSON.stringify(array_emails));
    console.log('ya guardo en storae')
}

function fn_guardarStorageIntentosNull() {
    localStorage.removeItem("intValida");
    localStorage.setItem("intValida", 1);
    localStorage.setItem("intValidaU", 1);
    localStorage.setItem("intValidaOP", 1);
    localStorage.setItem("intValidaFC", 1);
    localStorage.setItem("intValidaCC", 1);
    //////LOCAL STORAGE BASE PARA GUARDAR EMAILS Y VALIDAR
    var array_emails = ["email@email.com"];
    localStorage.setItem('email_valid', JSON.stringify(array_emails));
    console.log('ya guardo en storae')
}
