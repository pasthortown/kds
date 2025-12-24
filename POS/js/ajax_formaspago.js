/* global alertify */

////////////////////////////////////////////////////////////////////////////////////////////
////////MODIFICADO POR: CHRISTIAN PINTO/////////////////////////////////////////////////////
////////DECRIPCION: NUEVOS ESTILOS INGRESO Y ACTUALIZACION FORMAS DE PAGO///////////////////
///////////////////////////////// TABLA MODAL, COLECCION DE DATOS ATRIBUTO FORMA PAGO///////
////////TABLAS INVOLUCRADAS: Formapago,Cadena///////////////////////////////////////////////
////////FECHA CREACION: 09/06/2015//////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////

var Cod_Forma_Pago = 0;
var Accion = 0;
var pnt_Id = 0;
var aplica = 0;
var vecesclick = 0;

/*NUEVA COLECCION*/
var lc_IDColeccionFormasPago = '';
var lc_IDColeccionDeDatosFormasPago = '';

/*MODIFICAR COLECCION*/
var lc_nombreColeccion = '';
var lc_IDColeccionFormasPago_edit = '';
var lc_IDColeccionDeDatosFormasPago_edit = '';

$(document).ready(function () {
    fn_cargarCadenas();
    fn_btn('agregar', 1);
    fn_cargarFormasPagoActivosInactivos(16);
    fn_cargarSimboloMoneda();
    $("#tabla_formas_pago").show();
    Accion = 6;
    cargarCodigoRespuestaDLLGerente();
});

function fn_OpcionSeleccionada(ls_opcion) {
    if (ls_opcion == 'Todos') {
        fn_cargarFormasPago();
    } else if (ls_opcion == 'Activos') {
        resultado = 16;
        fn_cargarFormasPagoActivosInactivos(resultado);
    } else if (ls_opcion == 'Inactivos') {
        resultado = 17;
        fn_cargarFormasPagoActivosInactivos(resultado);
    }
}

function fn_OpcionSeleccionadaModInsert() {
    var ls_opcion = '';
    ls_opcion = $(":input[name=estados]:checked").val();
    if (ls_opcion == 'Todos') {
        fn_cargarFormasPago();
    } else if (ls_opcion == 'Activos') {
        resultado = 16;
        //alert(resultado)
        fn_cargarFormasPagoActivosInactivos(resultado);
    } else if (ls_opcion == 'Inactivos') {
        resultado = 17;
        fn_cargarFormasPagoActivosInactivos(resultado);
    }
}

/*===================================================*/
/*FUNCION PARA TRAER LOS BOTONES DE ADMINISTRACION FORMAS PAGO   */
/*===================================================*/
function fn_btn(boton, estado) {
    if (estado) {
        $("#btn_" + boton).css("background", " url('../../imagenes/admin_resources/" + boton + ".png') 14px 4px no-repeat");
        $("#btn_" + boton).removeAttr("disabled");
        $("#btn_" + boton).addClass("botonhabilitado");
        $("#btn_" + boton).removeClass("botonbloqueado");
    } else {
        $("#btn_" + boton).css("background", " url('../../imagenes/admin_resources/" + boton + "_bloqueado.png') 14px 4px no-repeat");
        $("#btn_" + boton).prop('disabled', true);
        $("#btn_" + boton).addClass("botonbloqueado");
        $("#btn_" + boton).removeClass("botonhabilitado");
    }
}

/*===================================================*/
/*FUNCION PARA CARGAR AL INICIO TABLA FORMAS DE PAGO */
/*===================================================*/
function fn_cargarFormasPago() {
    send = {"cargarFormasPago": 1};
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        $("#tabla_formas_pago").show();
        html = "<thead><tr class='active'>";
        html += "<th style='text-align:center'>Descripci&oacute;n</th>";
        html += "<th style='text-align:center'>Tipo</th>";
        html += "<th style='text-align:center'>Adquiriente</th>";
        html += "<th style='text-align:center'>Codigo DLL</th>";
        html += "<th style='text-align:center'>Activo</th>";
        html += "</tr></thead>";
        $("#formas_pago").empty();
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += "<tr id='" + i + "' onclick='fn_seleccion(" + i + "," + datos[i]['fmp_id'] + ");' ondblclick='fn_selecciondoble(" + i + "," + datos[i]['fmp_id'] + ")'>";
                html += "<td style='width:250px'>" + datos[i]['fmp_descripcion'] + "</td>";
                html += "<td style='text-align: center; width:250px'>" + datos[i]['tfp_descripcion'] + "</td>";
                html += "<td style='text-align: center;'>" + datos[i]['rda_descripcion'] + "</td>";
                html += "<td style='text-align: center;'>" + datos[i]['fpf_codigo'] + "</td>";
                if (datos[i]['std_id'] == 16) {
                    html += "<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";
                }
                if (datos[i]['std_id'] == 17) {
                    html += "<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";
                }
                $("#formas_pago").html(html);
                $("#formas_pago").dataTable({'destroy': true});
                $("#formas_pago_length").hide();
                $("#formas_pago_paginate").addClass('col-xs-10');
                $("#formas_pago_info").addClass('col-xs-10');
                $("#formas_pago_length").addClass('col-xs-6');
            }
        } else {
            //alertify.error("No existen datos para esta cadena.");
            html += "<tr>";
            html += "<td colspan='6'>No existen datos para esta cadena.</td></tr>";
            $("#formas_pago").html(html);
        }
    });
}

/*=========================================================================*/
/*FUNCION PARA CARGAR AL INICIO TABLA FORMAS DE PAGO ACTIVOS � INACTIVOS   */
/*=========================================================================*/
function fn_cargarFormasPagoActivosInactivos(resultado) {
    send = {"cargarFormasPagoActivoInactivo": 1};
    send.resultado = resultado;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        $("#tabla_formas_pago").show();
        html = "<thead><tr class='active'>";
        html += "<th style='text-align:center'>Descripci&oacute;n</th>";
        html += "<th style='text-align:center'>Tipo</th>";
        html += "<th style='text-align:center'>Adquiriente</th>";
        html += "<th style='text-align:center'>Codigo DLL</th>";
        html += "<th style='text-align:center'>Activo</th>";
        html += "</tr></thead>";
        $("#formas_pago").empty();
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += "<tr id='" + i + "' onclick='fn_seleccion(" + i + ",\"" + datos[i]['fmp_id'] + "\");' ondblclick='fn_selecciondoble(" + i + ",\"" + datos[i]['fmp_id'] + "\")'>";
                html += "<td style='width:250px'>" + datos[i]['fmp_descripcion'] + "</td>";
                html += "<td style='text-align: center; width:250px'>" + datos[i]['tfp_descripcion'] + "</td>";
                html += "<td style='text-align: center;'>" + datos[i]['rda_descripcion'] + "</td>";
                html += "<td style='text-align: center;'>" + datos[i]['fpf_codigo'] + "</td>";
                if (datos[i]['std_id'] == 16) {
                    html += "<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";
                }
                if (datos[i]['std_id'] == 17) {
                    html += "<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";
                }
                $("#formas_pago").html(html);
                $("#formas_pago").dataTable({'destroy': true});
                $("#formas_pago_length").hide();
                $("#formas_pago_paginate").addClass('col-xs-10');
                $("#formas_pago_info").addClass('col-xs-10');
                $("#formas_pago_length").addClass('col-xs-6');
            }
        } else {
            html += "<tr>";
            html += "<td colspan='6'>No existen datos.</td></tr>";
            $("#formas_pago").html(html);
        }
    });
}

function fn_AreaContenedorConfiguracionFormaPago(estado) {
    if (estado) {
        $("#contenedor_descuentos").show();
    } else {
        $("#contenedor_descuentos").hide();
    }
}

function fn_cargarTipoFormaPago() {
    var cadena = "";
    send = {"cargarTipoFormaPago": 1};
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                cadena = cadena + "<option value='" + datos[i]['tfp_id'] + "'>" + datos[i]['tfp_descripcion'] + "</option>";
            }
        }
        $("#tfp_tipo").html(cadena);
    });
}

function fn_cargarTipoAdquiriente() {
    var cadena = "";
    send = {"cargarTipoAdquiriente": 1};
    cadena = "<option value='0'>Ninguno</option>";
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                cadena = cadena + "<option value='" + datos[i]['rda_id'] + "'>" + datos[i]['rda_descripcion'] + "</option>";
            }
        }
        $("#rda_id").html(cadena);
    });
}

function fn_cargarTipoFormaPagoNuevo() {
    var cadena = "";
    send = {"cargarTipoFormaPago": 1};
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            $("#tfp_tipo").html("");
            $('#tfp_tipo').html("<option selected value='0'>----Seleccione Tipo Medios de Pago----</option>");
            for (i = 0; i < datos.str; i++) {
                cadena = cadena + "<option value='" + datos[i]['tfp_id'] + "'>" + datos[i]['tfp_descripcion'] + "</option>";
            }
        }
        $("#tfp_tipo").append(cadena);
    });
}

function fn_cargarTipoAdquirienteNuevo() {
    var cadena = "";
    send = {"cargarTipoAdquiriente": 1};
    cadena = "<option value='0'>Ninguno</option>";
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            $("#rda_id").html("");
            $('#rda_id').html("<option selected value='x'>----Seleccione Adquiriente----</option>");
            for (i = 0; i < datos.str; i++) {
                cadena = cadena + "<option value='" + datos[i]['rda_id'] + "'>" + datos[i]['rda_descripcion'] + "</option>";
            }
        }
        $("#rda_id").append(cadena);
    });
}

/*==========================================================*/
/*FUNCION PARA CARGAR LOS DATOS DEL REGISTRO A MODIFICAR    */
/*==========================================================*/
function fn_cargarFormaPago(codigo) {
    $("[name='checkbox_moneda']").bootstrapSwitch();
    $("[name='checkbox_propina']").bootstrapSwitch();
    $("[name='checkbox_aperturacajon']").bootstrapSwitch();
    $("[name='checkbox_ticket']").bootstrapSwitch();
    $("[name='checkbox_deslizar']").bootstrapSwitch();
    $("[name='checkbox_depositar']").bootstrapSwitch();
    $("[name='checkbox_autorizaventa']").bootstrapSwitch();
    send = {"cargarFormaPago": 1};
    send.fmp_id = codigo;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            $('#imagentitulomodalModificar').empty();
            $('#titulomodal').empty();
            for (i = 0; i < datos.str; i++) {
                $('#titulomodal').append(datos[i]['fmp_descripcion']);
                $('#fmp_descripcion').val(datos[i]['fmp_descripcion']);
                $('#slcDLL').val(datos[i]['fpf_codigo']);
                tipoFormaPago = datos[i]['tfp_id'];
                var cadena = "";
                send = {"cargarTipoFormaPago": 1};
                $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
                    if (datos.str > 0) {
                        for (i = 0; i < datos.str; i++) {
                            cadena = cadena + "<option value='" + datos[i]['tfp_id'] + "'>" + datos[i]['tfp_descripcion'] + "</option>";
                        }
                    }
                    $("#tfp_tipo").html(cadena);
                    $("#tfp_tipo").val(tipoFormaPago);
                });
                if (datos[i]['std_id'] == 16) {
                    $("#option").prop("checked", true);  // para poner la marca
                }
                if (datos[i]['std_id'] == 17) {
                    $("#option").prop("checked", false);
                }
                $('#rda_id').val(datos[i]['rda_id']);
                fn_obtenerImagen();
            }
        }
    });
}

/*=============================================================================================*/
/*FUNCION QUE LLAMA A LA FUNCION DE TRAER DATOS PARA MODIFICAR Y ACCION=2 CUANDO ES MODIFICAR  */
/*=============================================================================================*/
function fn_modificar(codigo) {
    Accion = 2;
    fn_cargarTipoAdquiriente();
    fn_cargarFormaPago(codigo);
    $('#pnl_pcn_btn').html('<button type="button" onclick="fn_guardar();fn_guardaFormaPagoAplicaTienda(); fn_guardaFormaPagoAplicaClientes();" class="btn btn-primary">Aceptar</button><button type="button" class="btn btn-default" data-dismiss="modal" onclick="fn_eliminarConfiguracionesNull();">Cancelar</button>');
}

function fn_agregarF() {
    if (Accion == 6) {
        $("#divImagen").hide();
        $("#divImagenNuevo").show();
        $('#titulomodal').empty();
        $('#titulomodal').append("Nueva Forma de Pago: ");
        $('#fmp_descripcion').val('');
        $('#slcDLL').val('');
        $("#fileimagenNuevo").val('');
        $('#fileimagenNuevo').attr({value: ''});
        $("#div_urlImpresionTipoFacturacion").hide();
        $("#div_urlImprimeTicket").hide();
        $("#url_imprimeTicket").val('');
        $('#imagentitulomodalModificar').empty();
        fn_cargarTipoFormaPagoNuevo();
        fn_cargarTipoAdquirienteNuevo();
        fn_cargarTipoFacturacion();
        fn_CrearConfiguracionesFormaPagoNuevo(0, 3);
        fn_cargarTiendasFormasPago(3, 0);
        fn_cargarClientesAplicaFormaPago(2, 0);
        fn_cargarPerfilesNivelSeguridad();
        $('#pnl_pcn_btn').html('<button type="button" onclick="fn_guardar(); " class="btn btn-primary">Aceptar</button><button type="button" class="btn btn-default" data-dismiss="modal" onclick="fn_eliminarConfiguracionesNull();">Cancelar</button>');
        $('#ModalModificar').modal('show');
    }
}

function fn_activaTavFormaPago() {
    Accion = 6;
    $("#btn_agregar").show();
}

function fn_activaTavDenominacionBillete() {
    Accion = 9;
    $("#btn_agregar").show();
}

function fn_guardar() {
    if (Accion === 6) {
        var nombre = $('#fmp_descripcion').val();
        var codigo = $('#slcDLL').val();
        var tipo = $('#tfp_tipo option:selected').val();
        var adquiriente = $('#rda_id option:selected').val();
        var imagen = $("#fileimagenNuevo").val();
        var tipoFacturacion = $("#sel_tipo_facturacion").val();
        var urlImprimeTicket = $("#url_imprimeTicket").val();
        if ($("#option").is(':checked')) {
            estado = 'Activo';
        } else {
            estado = 'Inactivo';
        }
        if (nombre === '') {
            alertify.error("Ingrese Descripicion.");
            return false;
        }
        if (tipo === 0) {
            alertify.error("Seleccione Tipo Medios de Pago.");
            return false;
        }
        if (adquiriente === 'x') {
            alertify.error("Seleccione Adquiriente.");
            return false;
        }
        if (codigo === "null") {
            alertify.error("Seleccione Código Respuesta DLL Gerente");
            return false;
        }
        if (tipoFacturacion === 0 || tipoFacturacion === null) {
            alertify.error("Seleccione Tipo de facturaci&oacute;n.");
            return false;
        }
        if (urlImprimeTicket === '' && $("[name=checkbox1]").bootstrapSwitch('state') === true) {
            alertify.error("Ingrese URL de Impresi&oacute;n de Ticket.");
            return false;
        }
        if (imagen === '') {
            alertify.error("Seleccione Imagen.");
            return false;
        }
        fn_agregarFormaPago(nombre, codigo, tipo, adquiriente, estado);
        $('#ModalModificar').modal('hide');
    } else if (Accion > 1 && Accion < 3) {
        var fmp_id = Cod_Forma_Pago;
        var nombre = $('#fmp_descripcion').val();
        var codigo = $('#slcDLL').val();
        var tipo = $('#tfp_tipo option:selected').val();
        if ($("#option").is(':checked')) {
            estado = 'Activo';
        } else {
            estado = 'Inactivo';
        }
        var adquiriente = $('#rda_id option:selected').val();
        if (nombre.length > 0) {
            if (codigo !== null) {
                url_imprimeTicket = $("#url_imprimeTicket").val();
                if (tipoFacturacion === 0) {
                    alertify.error("Seleccione Tipo de facturaci&oacute;n.");
                    return false;
                }
                if (url_imprimeTicket === '' && $("[name=checkbox1]").bootstrapSwitch('state') === true) {
                    alertify.error("Ingrese URL de Impresi&oacute;n de Ticket.");
                    return false;
                }
                fn_modificarFormaPago(fmp_id, nombre, codigo, tipo, estado, adquiriente);
                //fn_guardarModificaConfiguracion();
                fn_guardarTipoFacturacionModifica(fmp_id);
                fn_guardarUrlImprieTicket(fmp_id);
                alertify.success("Se actualizo correctamente la Forma de Pago.");
                $('#ModalModificar').modal('hide');
                Accion = 6;
                fn_OpcionSeleccionadaModInsert();
            } else {
                alertify.error("Seleccione Código Respuesta DLL Gerente.");
                return false;
            }
        } else {
            alertify.error("Ingrese una Descripcion para la Forma de Pago.");
            return false;
        }
    }
}

function fn_agregarFormaPago(nombre, codigo, tipo, adquiriente, estado) {
    $('#verimagen').hide();
    var fileInput = document.getElementById("fileimagenNuevo");
    var canvas = document.getElementById("micanvas");
    var ctx = canvas.getContext("2d");
    var img = new Image();
    img.src = URL.createObjectURL(fileInput.files[0]);
    ctx.drawImage(img, 0, 0);
    img.onload = function () {
        ctx.drawImage(img, 0, 0);
    };
    var img2 = new Image();
    img2.src = URL.createObjectURL(fileInput.files[0]);
    img2.onload = function () {
        var canvas = document.createElement("canvas");
        canvas.width = this.width;
        canvas.height = this.height;
        var ctx = canvas.getContext("2d");
        ctx.drawImage(this, 0, 0);
        var dataURL = canvas.toDataURL("image/png");
        $('#txt_area_imagen').html(dataURL.replace(/^data:image\/(png|jpg);base64,/, ""));
        send = {"agregarFormasPago": 1};
        send.fmp_imagen = $('#txt_area_imagen').html();
        send.fmp_descripcion = nombre;
        send.fpf_codigo = codigo;
        send.std_id = estado;
        send.tfp_id = tipo;
        send.rda_id = adquiriente;
        $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminFormasPago/config_formaspago.php", data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    fmp_id = datos.fmp_id;
                    alertify.success("Forma de Pago agregado correctamente.");
                }
            }
        });
        fn_guardaFormaPagoAplicaTiendaNuevo(fmp_id);
        fn_guardaFormaPagoAplicaClienteNuevo(fmp_id);
        fn_agregarNivelSeguridad(fmp_id);
        fn_agregarConfiguracionesFormaPago(fmp_id);
        fn_guardarTipoFacturacion(fmp_id);
        fn_guardarUrlImprieTicket(fmp_id);
        fn_OpcionSeleccionadaModInsert();
    };

}

function fn_agregarConfiguracionesFormaPago(fmp_id) {
    porcentajepropina = $("#porcentajepropina").val();
    idporcentajepropina = $("#porcentajepropina").attr("name");
    send = {"agregarConfiguracionesFormasPago": 1};
    send.idporcentajepropina = idporcentajepropina;
    send.porcentajepropina = porcentajepropina;
    send.fmp_id = fmp_id;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
        }
    });
}

/*===========================================================*/
/*FUNCION PARA TRAER LAS CONFIFURACIONES DE FORMA DE PAGO   */
/*==========================================================*/
function fn_CrearConfiguracionesFormaPagoNuevo(codigo, accion) {
    send = {"traerconfigformapagocoleccion": 1};
    send.accion = accion;
    send.idformapago = codigo;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            $("#contenedorconfiguraciones").empty();
            for (i = 0; i < datos.str; i++) {
                if ((datos[i]['orden']) !== '8') {
                    if ((datos[i]['fila']) < 2) {
                        html = "<div class='col-xs-1'></div><div class='col-xs-3'><h5>" + datos[i]['cfre_descripcion'] + "</h5></div><div class='col-xs-2'><div class='form-group'><input type='checkbox' name='checkbox" + i + "' value=" + datos[i]['cfpa_id'] + " data-off-text='No' data-on-text='Si' onchange='fn_guardarNuevaConfiguracion(" + i + "," + codigo + "); fn_ocultarPorcentajePropina();'></div></div>";
                    } else {
                        html = "<div class='col-xs-1'></div><div class='col-xs-3'><h5>" + datos[i]['cfre_descripcion'] + "</h5></div><div class='col-xs-2'><div class='form-group'><input type='checkbox' name='checkbox" + i + "' value=" + datos[i]['cfpa_id'] + " data-off-text='No' data-on-text='Si' onchange='fn_guardarNuevaConfiguracion(" + i + "," + codigo + "); fn_ocultarPorcentajePropina();'></div><div class='col-xs-1'></div></div>";
                    }
                } else {
                    html = "<div class='col-xs-1'></div><div class='col-xs-3' id='divPorcentajePropina'><h5>" + datos[i]['cfre_descripcion'] + "</h5></div><div class='col-xs-2' id='divinputPorcentajePropina'><input style='width:71%' onKeyPress='return fn_numeros(event);' class='form-control' type='text' id='porcentajepropina' name=" + datos[i]['cfpa_id'] + " value=" + datos[i]['fpat_float'] + " maxlength='3' onchange='fn_guardarNuevaConfiguracion(" + i + ",\"" + codigo + "\");'/></div><div class='col-xs-1'></div></div>";
                }
                $("#contenedorconfiguraciones").append(html);
                $("[name='checkbox" + i + "']").bootstrapSwitch();
                $("#divPorcentajePropina").hide();
                $("#divinputPorcentajePropina").hide();
            }
        }
    });
}

function fn_ocultarPorcentajePropina() {
    estado = $("[name=checkbox6]").bootstrapSwitch('state');
    estadoImprimeTicket = $("[name=checkbox1]").bootstrapSwitch('state');
    //alert(codigo)
    if (estadoImprimeTicket) {
        $("#div_urlImprimeTicket").show();
    } else {
        $("#div_urlImprimeTicket").hide();
    }
    if (estado) {
        $("#divPorcentajePropina").show();
        $("#divinputPorcentajePropina").show();
        $("#porcentajepropina").show();
    } else {
        $("#divPorcentajePropina").hide();
        $("#divinputPorcentajePropina").hide();
        $("#porcentajepropina").val(0);
        $("#porcentajepropina").hide();
    }
}

function fn_eliminarConfiguracionesNull() {
    Accion = 6;
    send = {"eliminarConfiguracionesNull": 1};
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
        }
    });
}

function fn_guardarNuevaConfiguracion(valor, codigo) {
    estado = $("[name=checkbox" + valor + "]").bootstrapSwitch('state');
    var bit = 0;
    if (estado) {
        bit = 1;
    }
    //alert(estado);
    if ($("#porcentajepropina").val() == '') {
        $("#porcentajepropina").val(0);
    }
    configuracion = $("[name=checkbox" + valor + "]").val();
    porcentajepropina = $("#porcentajepropina").val();
    idporcentajepropina = $("#porcentajepropina").attr("name");
    idcodformapago = $("#txt_formapago").val();
    send = {"guardarNuevaConfiguracion": 1};
    if (configuracion == undefined) {
        configuracion = 8;
    }
    send.idconfiguracion = configuracion;
    send.bit = bit;
    send.idporcentajepropina = idporcentajepropina;
    send.porcentajepropina = porcentajepropina;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
            }
        }
    });
}

function fn_modificarFormaPago(id, nombre, codigo, tipo, estado, adquiriente) {
    send = {"modificarFormasPago": 1};
    send.fmp_id = id;
    send.fmp_descripcion = nombre;
    send.fpf_codigo = codigo;
    send.std_id = estado;
    send.tfp_id = tipo;
    send.rda_id = adquiriente;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminFormasPago/config_formaspago.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                }
            }
        }
    });
    fn_agregarNivelSeguridadModificar();
    Cod_Forma_Pago = 0;
    $("#formas_pago tr").removeClass("success");
}

function fn_seleccion(fila, codigo) {
    Cod_Forma_Pago = codigo;
    $("#formas_pago tr").removeClass("success");
    $("#" + fila + "").addClass("success");
    $("#txt_formapago").val(Cod_Forma_Pago);
}

function fn_selecciondoble(fila, codigo) {
    aplica = 0;
    $(":file").filestyle('clear');
    $("#divImagen").show();
    $("#divImagenNuevo").hide();
    Cod_Forma_Pago = codigo;
    $("#txt_formapago").val(Cod_Forma_Pago);
    $("#formas_pago tr").removeClass("success");
    $("#" + fila + "").addClass("success");
    fn_modificar(codigo);
    fn_cargarTiendasFormasPago(aplica, Cod_Forma_Pago);
    fn_cargarClientesAplicaFormaPago(aplica, Cod_Forma_Pago);
    fn_CrearConfiguracionesFormaPago(codigo, 7);
    fn_cargarPerfilesNivelSeguridadModificar();
    fn_cargarTipoFacturacionModifica(Cod_Forma_Pago);
    fn_cargarUrlImprimeTicket(Cod_Forma_Pago);
    $('#ModalModificar').modal('show');
    fn_cargaFormasPagoColecciondeDatos(Cod_Forma_Pago);
}

/*======================================================*/
/*FUNCION PARA MARCAR LAS TIENDAS CON LA CLASE SUCCESS  */
/*======================================================*/
function fn_seleccionRestaurante(fila, codigo) {
    Cod_Restaurante = codigo;
    if ($("#a" + fila + "").hasClass("list-group-item-success")) {
        $("#a" + fila + "").removeClass("list-group-item-success");
        $("#input" + fila + "").prop("checked", false);
    } else {
        $("#a" + fila + "").addClass("list-group-item-success");
        $("#input" + fila + "").prop("checked", true);
    }
    $("#txt_idrestaurante").val(Cod_Restaurante);
}

function fn_esconderDivArea() {
    $("#area_trabajo").hide();
}

function fn_mostrarDivArea() {
    $("#area_trabajo").show();
}

function fn_cargarCadenas() {
    send = {"cargaCadena": 1};
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            $("#cadena").html("");
            $('#cadena').html("<option selected value='0'>----Seleccione Cadena----</option>");
            for (i = 0; i < datos.str; i++) {
                $("#cadena").append("<option value=" + datos[i]['cdn_id'] + ">" + datos[i]['cdn_descripcion'] + "</option>");
            }
            $("#cadena").val(0);
        }
    });
}

function aMays(e, elemento) {
    tecla = (document.all) ? e.keyCode : e.which;
    elemento.value = elemento.value.toUpperCase();
}

/*=============================================================================================*/
/*FUNCION PARA CARGAR TIENDAS AGREGADAS(CHECK) - TIENDAS NO AGREGADAS(NO CHECK)  */
/*=============================================================================================*/
function fn_cargarTiendasFormasPago(aplica, Cod_Forma_Pago) {
    var html = '';
    //alert(aplica);
    send = {"traerRestaurantes": 1};
    send.Cod_Forma_Pago = Cod_Forma_Pago;
    send.aplica = aplica;
    send.descripcionformapago = $("#fmp_descripcion").val();
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                if (datos[i]['agregado'] > 0 && datos[i]['rsat_bit'] == 1) {
                    html = html + "<a id='a" + i + "' class='list-group-item list-group-item-success' onclick='fn_seleccionRestaurante(" + i + "," + datos[i]['rst_id'] + ")'><input name='chck_rst_id[]' id='input" + i + "'  value='" + datos[i]['rst_id'] + "' type='checkbox' checked>&nbsp; " + datos[i]['rst_descripcion'] + "</a>";
                } else {
                    html = html + "<a id='a" + i + "' class='list-group-item' onclick='fn_seleccionRestaurante(" + i + "," + datos[i]['rst_id'] + ")'><input name='chck_rst_id[]' id='input" + i + "' value='" + datos[i]['rst_id'] + "' type='checkbox'>&nbsp; " + datos[i]['rst_descripcion'] + "</a>";
                }
            }
            $("#rst_agregado").html(html);
        }
    });
}

/*===================================================================================*/
/*FUNCION PARA GUARDAR LAS TIENDAS SELECCIONADAS EN LA TABLA RESTAURANTE ATRIBUTOS   */
/*===================================================================================*/
function fn_guardaFormaPagoAplicaTienda() {
    var id_restaurante = new Array();//array q contiene los codigos de los perfiles
    //recorremos todos los checkbox seleccionados con .each
    $('input[name="chck_rst_id[]"]:checked').each(function () {
        id_restaurante.push($(this).val());
    });
    aplica = 1;
    send = {"aplica_restaurante": 1};
    send.Cod_Forma_Pago = $("#txt_formapago").val();
    send.id_restaurante = id_restaurante;
    send.aplica = aplica;
    send.descripcionformapago = $("#fmp_descripcion").val();
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminFormasPago/config_formaspago.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                }
            }
        }
    });
}

/*=========================================================================================*/
/*FUNCION PARA GUARDAR LAS TIENDAS SELECCIONADAS EN LA TABLA RESTAURANTE ATRIBUTOS NUEVO   */
/*=========================================================================================*/
function fn_guardaFormaPagoAplicaTiendaNuevo(fmp_id) {
    var id_restaurante = new Array();//array q contiene los codigos de los perfiles
    //recorremos todos los checkbox seleccionados con .each
    $('input[name="chck_rst_id[]"]:checked').each(function () {
        //$(this).val() es el valor del checkbox correspondiente
        id_restaurante.push($(this).val());

    });
    send = {"aplica_restaurante_nuevo": 1};
    send.fmp_id = fmp_id;
    send.id_restaurante = id_restaurante;
    send.descripcionformapago = $("#fmp_descripcion").val();
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminFormasPago/config_formaspago.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                }
            }
        }
    });
}

/*===========================================================*/
/*FUNCIONES PARA PARA MARCAR Y DESMARCAR TODAS LAS TIENDAS  */
/*==========================================================*/
marcar = function (elemento) {
    elemento = $('input[name="chck_rst_id[]"]');
    elemento.prop("checked", true);
};

desmarcar = function (elemento) {
    elemento = $('input[name="chck_rst_id[]"]');
    elemento.prop("checked", false);
};

/*===========================================================*/
/*FUNCION PARA TRAER LAS CONFIFURACIONES DE FORMA DE PAGO   */
/*==========================================================*/
function fn_CrearConfiguracionesFormaPago(codigo, accion) {
    send = {"traerconfigformapagocoleccion": 1};
    send.accion = accion;
    send.idformapago = codigo;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            $("#contenedorconfiguraciones").empty();
            for (i = 0; i < datos.str; i++) {
                if ((datos[i]['orden']) != '8') {
                    if ((datos[i]['fpat_bit']) == 1) {
                        if (datos[i]['cfre_descripcion'] == 'Imprime Ticket' && datos[i]['fpat_bit'] == 1) {
                            $("#div_urlImprimeTicket").show();
                        }
                        if ((datos[i]['fila']) < 2) {
                            html = "<div class='col-xs-1'></div><div class='col-xs-3'><h5>" + datos[i]['cfre_descripcion'] + "</h5></div><div class='col-xs-2'><div class='form-group'><input type='checkbox' name='checkbox" + i + "' value=" + datos[i]['cfpa_id'] + " data-off-text='No' data-on-text='Si' onchange='fn_guardarModificaConfiguracion(" + i + ",\"" + codigo + "\"); fn_ocultarPorcentajePropina();' checked></div></div>";
                        } else {
                            html = "<div class='col-xs-1'></div><div class='col-xs-3'><h5>" + datos[i]['cfre_descripcion'] + "</h5></div><div class='col-xs-2'><div class='form-group'><input type='checkbox' name='checkbox" + i + "' value=" + datos[i]['cfpa_id'] + " data-off-text='No' data-on-text='Si' onchange='fn_guardarModificaConfiguracion(" + i + ",\"" + codigo + "\"); fn_ocultarPorcentajePropina();' checked></div><div class='col-xs-1'></div></div>";
                        }
                    } else {
                        if (datos[i]['cfre_descripcion'] == 'Imprime Ticket' && datos[i]['fpat_bit'] != 1) {
                            $("#div_urlImprimeTicket").hide();
                        }
                        if ((datos[i]['fila']) < 2) {
                            html = "<div class='col-xs-1'></div><div class='col-xs-3'><h5>" + datos[i]['cfre_descripcion'] + "</h5></div><div class='col-xs-2'><div class='form-group'><input type='checkbox' name='checkbox" + i + "' value=" + datos[i]['cfpa_id'] + " data-off-text='No' data-on-text='Si' onchange='fn_guardarModificaConfiguracion(" + i + ",\"" + codigo + "\"); fn_ocultarPorcentajePropina();'></div></div>";
                        } else {
                            html = "<div class='col-xs-1'></div><div class='col-xs-3'><h5>" + datos[i]['cfre_descripcion'] + "</h5></div><div class='col-xs-2'><div class='form-group'><input type='checkbox' name='checkbox" + i + "' value=" + datos[i]['cfpa_id'] + " data-off-text='No' data-on-text='Si' onchange='fn_guardarModificaConfiguracion(" + i + ",\"" + codigo + "\"); fn_ocultarPorcentajePropina();'></div><div class='col-xs-1'></div></div>";
                        }
                    }
                } else {
                    html = "<div class='col-xs-1'></div><div class='col-xs-3' id='divPorcentajePropina'><h5>" + datos[i]['cfre_descripcion'] + "</h5></div><div class='col-xs-2'><input style='width:71%' class='form-control' type='text' id='porcentajepropina' name=\"" + datos[i]['cfpa_id'] + "\" value=" + datos[i]['fpat_float'] + " maxlength='3' onKeyPress='return fn_numeros(event);' onchange='fn_guardarModificaConfiguracion(" + i + ",\"" + codigo + "\");'/></div><div class='col-xs-1'></div></div>";
                }
                $("#contenedorconfiguraciones").append(html);
                $("[name='checkbox" + i + "']").bootstrapSwitch();
                estado = $("[name=checkbox6]").bootstrapSwitch('state');
                if (estado) {
                    $("#divPorcentajePropina").show();
                    $("#porcentajepropina").show();
                } else {
                    $("#divPorcentajePropina").hide();
                    $("#porcentajepropina").hide();
                    $("#porcentajepropina").val(0);
                }

                estado_imprimeTicket = $("[name=checkbox1]").bootstrapSwitch('state');

                if (estado_imprimeTicket) {
                    $("#div_urlImprimeTicket").show();
                } else {
                    $("#div_urlImprimeTicket").hide();
                }
                if (datos[i]['cfre_descripcion'] == 'Imprime Ticket' && datos[i]['fpat_bit'] != 1) {
                    $("#div_urlImprimeTicket").hide();
                }
            }
        }
    });
}

function fn_guardarModificaConfiguracion(valor, codigo) {
    estado = $("[name=checkbox" + valor + "]").bootstrapSwitch('state');
    var bit = 0;
    if (estado) {
        bit = 1;
    }

    if ($("#txt_porcentajepropina").val() == '') {
        $("#txt_porcentajepropina").val(0);
    }
    configuracion = $("[name=checkbox" + valor + "]").val();
    porcentajepropina = $("#txt_porcentajepropina").val();
    idporcentajepropina = 0;
    idcodformapago = $("#txt_formapago").val();
    accion = 2;
    send = {"guardarModificaConfiguracion": 1};
    send.accion = accion;
    send.idformapago = idcodformapago;
    if (configuracion == undefined) {
        configuracion = 8;
    }
    send.idconfiguracion = configuracion;
    send.bit = bit;
    send.idporcentajepropina = idporcentajepropina;
    send.porcentajepropina = porcentajepropina;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            if (estado) {
                $("#div_urlImprimeTicket").show();
            }
            for (i = 0; i < datos.str; i++) {
            }
        }
    });
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

function fn_obtenerImagen() {
    codigofp = $("#txt_formapago").val();
    send = {"cargarImagen": 1};
    send.accion = 1;
    send.codigofp = codigofp;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminFormasPago/config_imagen.php", data: send,
        success: function (datos) {
            $('#imagentitulomodalModificar').empty();
            $('#imagentitulomodalModificar').append('<img src="data:image/png;base64,' + datos.imagen + '"/>');
        }, error: function () {
            alert('Error lectura de imagen');
        }
    });
}

function fn_cargarImagen() {
    $('#verimagen').hide();
    codigofp = $("#txt_formapago").val();
    var fileInput = document.getElementById("fileimagen");
    var canvas = document.getElementById("micanvas");
    var ctx = canvas.getContext("2d");
    var img = new Image();
    img.src = URL.createObjectURL(fileInput.files[0]);
    ctx.drawImage(img, 0, 0);
    img.onload = function () {
        ctx.drawImage(img, 0, 0);
    };
    var img3 = new Image();
    img3.src = URL.createObjectURL(fileInput.files[0]);
    img3.onload = function () {
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
        data.append("codigofp", codigofp);
        $.ajax({async: false, type: "POST", dataType: "json", contentType: false, url: "../adminFormasPago/config_imagenactualizar.php", data: data, processData: false, cache: false,
            success: function (datos) {
                fn_obtenerImagen();
            }
        });
    }
}

function fn_cargarImagenNuevo() {
    /*Mostrar imagen cargado, Funciona*/
    $('#verimagen').hide();
    codigofp = $("#txt_formapago").val();
    var fileInput = document.getElementById("fileimagen");
    var canvas = document.getElementById("micanvas");
    var ctx = canvas.getContext("2d");
    var img = new Image();
    img.src = URL.createObjectURL(fileInput.files[0]);
    ctx.drawImage(img, 0, 0);
    img.onload = function () {
        ctx.drawImage(img, 0, 0);
    };
    var img4 = new Image();
    img4.src = URL.createObjectURL(fileInput.files[0]);
    img4.onload = function () {
        var canvas = document.createElement("canvas");
        canvas.width = this.width;
        canvas.height = this.height;
        var ctx = canvas.getContext("2d");
        ctx.drawImage(this, 0, 0);
        var dataURL = canvas.toDataURL("image/png");
        $('#txt_area_imagen').html(dataURL.replace(/^data:image\/(png|jpg);base64,/, ""));
        data = {"guardaImagenNuevo": 1};
        data.append("imagen", $('#txt_area_imagen').html());
        data.append("modificarImagenArea", 1);
        data.append("codigofp", codigofp);
        $.ajax({async: false, type: "POST", dataType: "json", contentType: false, url: "../adminFormasPago/config_formaspago.php", data: data, processData: false, cache: false,
            success: function (datos) {
            }
        });
    };
}

function fn_cargarPerfilesNivelSeguridad() {
    var html = "";
    send = {"cargarPerfilesNivelSeguridad": 1};
    send.accion = 1;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            $("#select_perfil").html("");
            $('#select_perfil').html("<option selected value='0'>--------Seleccione Perfil--------</option>");
            for (i = 0; i < datos.str; i++) {
                html = html + "<option value='" + datos[i]['prf_id'] + "'>" + datos[i]['prf_descripcion'] + "</option>";
            }
        }
        $("#select_perfil").append(html);
    });
}

function fn_agregarNivelSeguridad(fmp_id) {
    send = {"agregarNivelSeguridad": 1};
    send.accion = 1;
    send.fmp_id = fmp_id;
    send.prf_id = $("#select_perfil").val();
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
        }
    });
}

function fn_cargarPerfilesNivelSeguridadModificar() {
    $("#select_perfil").val(0);
    fn_cargarPerfilesNivelSeguridad();
    send = {"cargarPerfilesNivelSeguridadModificar": 1};
    send.accion = 2;
    send.fmp_id = $("#txt_formapago").val();
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                $("#select_perfil").val(datos[i]['prf_id_coleccion']);
            }
        }
    });
}

function fn_agregarNivelSeguridadModificar() {
    send = {"agregarNivelSeguridadModificar": 1};
    send.accion = 2;
    send.fmp_id = $("#txt_formapago").val();
    send.prf_id = $("#select_perfil").val();
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
        }
    });
}

/*===================================================*/
/*FUNCION PARA CARGAR AL INICIO TABLA SIMBOLO MONEDA */
/*===================================================*/
function fn_cargarSimboloMoneda() {
    send = {"cargarSimboloMoneda": 1};
    send.accion = 1;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        $("#tabla_simbolo").show();
        html = "<thead><tr class='active'>";
        html += "<th style='text-align:center'>Pa&iacute;s Descripci&oacute;n</th>";
        html += "<th style='text-align:center'>Moneda</th>";
        html += "<th style='text-align:center'>Descripci&oacute;n Moneda</th>";
        html += "<th style='text-align:center'>Pa&iacute;s Base Factura</th>";
        html += "<th style='text-align:center'>Moneda S&iacute;mbolo</th>";
        html += "</tr></thead>";
        $("#tabla_simbolo").empty();
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += "<tr id='" + i + "' onclick='fn_seleccion(" + i + "," + datos[i]['pais_id'] + ");' ondblclick='fn_modificarMonedaSimbolo(" + datos[i]['pais_id'] + ")'>";
                html += "<td style='text-align: center;'>" + datos[i]['pais_descripcion'] + "</td>";
                html += "<td style='text-align: center;'>" + datos[i]['pais_moneda'] + "</td>";
                html += "<td style='text-align: center;'>" + datos[i]['pais_desc_modeda'] + "</td>";
                html += "<td style='text-align: center;'>" + datos[i]['pais_base_factura'] + "</td>";
                html += "<td style='text-align: center;'>" + datos[i]['pais_moneda_simbolo'] + "</td>";
                $("#tabla_simbolo").html(html);
                $("#tabla_simbolo").dataTable({'destroy': true});
                $("#tabla_simbolo_length").hide();
                $("#tabla_simbolo_paginate").addClass('col-xs-10');
                $("#tabla_simbolo_info").addClass('col-xs-10');
                $("#tabla_simbolo_length").addClass('col-xs-6');
            }
        } else {
            html += "<tr>";
            html += "<td colspan='6'>No existen datos para esta cadena.</td></tr>";
            $("#tabla_simbolo").html(html);
        }
    });
}

function fn_activaTavMonedaSimbolo() {
    Accion = 7;
    $("#btn_agregar").hide();
}

function fn_agregarMonedaSimbolo() {
    if (Accion == 7) {
        $('#titulomodalmoneda').empty();
        $('#titulomodalmoneda').append("Nueva Moneda: ");
        $('#pais_descripcion').val('');
        $('#pais_moneda').val('');
        $('#pais_desc_modeda').val('');
        $("#pais_base_factura").val('');
        $("#pais_moneda_simbolo").val('');
        $('#modalMoneda').modal('show');
    }
}

function fn_guardarMonedaSimbolo() {
    send = {"guardarMonedaSimbolo": 1};
    send.accion = 1;
    send.pais_descripcion = $('#pais_descripcion').val();
    send.pais_moneda = $('#pais_moneda').val();
    send.pais_desc_modeda = $('#pais_desc_modeda').val();
    send.pais_base_factura = $('#pais_base_factura').val();
    send.pais_moneda_simbolo = $('#pais_moneda_simbolo').val();
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
            }
            alertify.success("Se agrego correctamente la Moneda.");
        }
    });
}

function fn_modificarMonedaSimbolo(pais_id) {
    Accion = 8;
    if (Accion == 8) {
        $('#pais_descripcion').val('');
        $('#pais_moneda').val('');
        $('#pais_desc_modeda').val('');
        $("#pais_base_factura").val('');
        $("#pais_moneda_simbolo").val('');
        $("#txt_pais_id").val(pais_id);
        fn_traerModificaMonedaSimbolo(pais_id);
        $('#modalMoneda').modal('show');
    }
}

function fn_traerModificaMonedaSimbolo(pais_id) {
    send = {"traerModificaSimboloMoneda": 1};
    send.accion = 2;
    send.pais_id = pais_id;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                $('#titulomodalmoneda').empty();
                $('#titulomodalmoneda').append(datos[i]["pais_desc_modeda"]);
                $('#pais_descripcion').val(datos[i]["pais_descripcion"]);
                $('#pais_moneda').val(datos[i]["pais_moneda"]);
                $('#pais_desc_modeda').val(datos[i]["pais_desc_modeda"]);
                $("#pais_base_factura").val(datos[i]["pais_base_factura"]);
                $("#pais_moneda_simbolo").val(datos[i]["pais_moneda_simbolo"]);
            }
        }
    });
}

function fn_guardarModificaSimboloMoneda() {
    if ($('#pais_moneda').val() == '') {
        alertify.error("Ingrese Moneda.");
        return false;
    }
    if ($('#pais_desc_modeda').val() == '') {
        alertify.error("Ingrese Descripcion Moneda.");
        return false;
    }
    if ($('#pais_base_factura').val() == '') {
        alertify.error("Ingrese Base Factura.");
        return false;
    }
    if ($('#pais_moneda_simbolo').val() == '') {
        alertify.error("Ingrese Simbolo Moneda.");
        return false;
    }
    send = {"guardarModificaSimboloMoneda": 1};
    send.accion = 2;
    send.pais_id = $("#txt_pais_id").val();
    send.pais_descripcion = $('#pais_descripcion').val();
    send.pais_moneda = $('#pais_moneda').val();
    send.pais_desc_modeda = $('#pais_desc_modeda').val();
    send.pais_base_factura = $('#pais_base_factura').val();
    send.pais_moneda_simbolo = $('#pais_moneda_simbolo').val();
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
            }
            alertify.success("Se modifico correctamente la Moneda.");
            $('#modalMoneda').modal('hide');
            fn_cargarSimboloMoneda();
        }
    });
}

function fn_activaTavClientes() {
    Accion = 11;
    $("#btn_agregar").show();
}

function fn_agregarClienteFormasPago() {
    if (Accion == 11) {
        $('#titulomodalclientes').empty();
        $('#titulomodalclientes').append("Nuevo Cliente: ");
        $('#cli_nombres').val('');
        $('#cli_apellidos').val('');
        $('#sel_tipodocumento').val(0);
        $('#cli_documento').val('');
        $("#cli_telefono").val('');
        $("#cli_direccion").val('');
        $('#sel_ciudad').val(0);
        $('#cli_email').val('');
        $('#sel_formapago').val(0);
        fn_cargarTipoDocumento();
        fn_cargarCiudad();
        fn_cargarAplicaFormasPago();
        $('#modalClientes').modal('show');
    }
}

function fn_cargarTipoDocumento() {
    var html = "";
    send = {"traerTipoDocumento": 1};
    send.accion = 2;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            $("#sel_tipodocumento").html("");
            $('#sel_tipodocumento').html("<option selected value='0'>-----Seleccione-----</option>");
            for (i = 0; i < datos.str; i++) {
                html = html + "<option value='" + datos[i]['tpdoc_id'] + "'>" + datos[i]['tpdoc_descripcion'] + "</option>";
            }
        }
        $("#sel_tipodocumento").append(html);
    });
}

function fn_cargarCiudad() {
    var html = "";
    send = {"traerCiudad": 1};
    send.accion = 3;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            $("#sel_ciudad").html("");
            $('#sel_ciudad').html("<option selected value='0'>------------------------Seleccione------------------------</option>");
            for (i = 0; i < datos.str; i++) {
                html = html + "<option value='" + datos[i]['ciu_id'] + "'>" + datos[i]['ciu_nombre'] + "</option>";
            }
        }
        $("#sel_ciudad").append(html);
    });
}

function fn_cargarAplicaFormasPago() {
    var html = "";
    send = {"traerAplicaFormasPago": 1};
    send.accion = 4;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            $("#sel_formapago").html("");
            $('#sel_formapago').html("<option selected value='0'>----------------Seleccione----------------</option>");
            for (i = 0; i < datos.str; i++) {
                html = html + "<option value='" + datos[i]['fmp_id'] + "'>" + datos[i]['fmp_descripcion'] + "</option>";
            }
        }
        $("#sel_formapago").append(html);
    });
}

function fn_guardarClienteFormasPago() {
    if ($('#cli_nombres').val() == '') {
        alertify.error("Ingrese Nombres Cliente.");
        return false;
    }
    if ($('#cli_apellidos').val() == '') {
        alertify.error("Ingrese Apellidos Cliente.");
        return false;
    }
    if ($('#sel_tipodocumento').val() == '') {
        alertify.error("Seleccione Documento Cliente.");
        return false;
    }
    if ($('#cli_documento').val() == '') {
        alertify.error("Ingrese Documento.");
        return false;
    }
    if ($('#cli_telefono').val() == '') {
        alertify.error("Ingrese Telefono Cliente.");
        return false;
    }
    if ($('#sel_ciudad').val() == '') {
        alertify.error("Seleccione Ciudad Cliente.");
        return false;
    }
    if ($('#cli_direccion').val() == '') {
        alertify.error("Ingrese Direccion Cliente.");
        return false;
    }
    if ($('#cli_email').val() == '') {
        alertify.error("Ingrese Email Cliente.");
        return false;
    }
    send = {"guardarClienteFormasPago": 1};
    send.accion = 1;
    send.cli_nombres = $('#cli_nombres').val();
    send.cli_apellidos = $('#cli_apellidos').val();
    send.sel_tipodocumento = $('#sel_tipodocumento').val();
    send.cli_documento = $('#cli_documento').val();
    send.cli_telefono = $('#cli_telefono').val();
    send.cli_direccion = $('#cli_direccion').val();
    send.sel_ciudad = $('#sel_ciudad').val();
    send.cli_email = $('#cli_email').val();
    send.sel_formapago = $('#sel_formapago').val();
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
            }
            alertify.success("Se agrego correctamente el Cliente.");
            fn_guardarClienteAplicaFormasPago();
        }
    });
}

function fn_guardarClienteAplicaFormasPago() {
    send = {"guardarClienteAplicaFormasPago": 1};
    send.accion = 2;
    send.sel_formapago = $('#sel_formapago').val();
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
            }
        }
    });
}

/*=============================================================================================*/
/*FUNCION PARA CARGAR CLIENTES AGREGADOS(CHECK) - CLIENTES NO AGREGADOS(NO CHECK)  */
/*=============================================================================================*/
function fn_cargarClientesAplicaFormaPago(aplica, Cod_Forma_Pago) {
    var html = '';
    send = {"cargarClientesAplicaFormaPago": 1};
    send.Cod_Forma_Pago = Cod_Forma_Pago;
    send.aplica = aplica;
    send.descripcionformapago = $("#fmp_descripcion").val();
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                if (datos[i]['clienteagregado'] > 0 && datos[i]['fpat_bit'] == 1) {
                    html = html + "<a id='c" + i + "' class='list-group-item list-group-item-success' onclick='fn_seleccionCliente(" + i + ",\"" + datos[i]['cli_id'] + "\")'><input name='chck_clientes_id[]' id='inputClie" + i + "'  value='" + datos[i]['cli_id'] + "' type='checkbox' checked>&nbsp; " + datos[i]['cli_apellidos'] + ' ' + datos[i]['cli_nombres'] + "</a>";
                } else {
                    html = html + "<a id='c" + i + "' class='list-group-item' onclick='fn_seleccionCliente(" + i + ",\"" + datos[i]['cli_id'] + "\")'><input name='chck_clientes_id[]' id='inputClie" + i + "' value='" + datos[i]['cli_id'] + "' type='checkbox'>&nbsp; " + datos[i]['cli_apellidos'] + ' ' + datos[i]['cli_nombres'] + "</a>";
                }
            }
            $("#cliente_agregado").html(html);
        }
    });
}

/*===================================================================================*/
/*FUNCION PARA GUARDAR LOS CLIENTES SELECCIONADOS                                    */
/*===================================================================================*/
function fn_guardaFormaPagoAplicaClientes() {
    var id_cliente = new Array();//array q contiene los codigos de los perfiles
    //recorremos todos los checkbox seleccionados con .each
    $('input[name="chck_clientes_id[]"]:checked').each(function () {
        //$(this).val() es el valor del checkbox correspondiente
        id_cliente.push($(this).val());

    });
    aplica = 1;
    send = {"aplica_cliente": 1};
    send.Cod_Forma_Pago = $("#txt_formapago").val();
    send.id_cliente = id_cliente;
    send.aplica = aplica;
    send.descripcionformapago = $("#fmp_descripcion").val();
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminFormasPago/config_formaspago.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                }
            }
        }
    });
    $('#ModalModificar').modal('hide');
}

/*======================================================*/
/*FUNCION PARA MARCAR CLIENTES CON LA CLASE SUCCESS  */
/*======================================================*/
function fn_seleccionCliente(fila, codigo) {
    Cod_Cliente = codigo;
    if ($("#c" + fila + "").hasClass("list-group-item-success")) {
        $("#c" + fila + "").removeClass("list-group-item-success");
        $("#inputClie" + fila + "").prop("checked", false);
    } else {
        $("#c" + fila + "").addClass("list-group-item-success");
        $("#inputClie" + fila + "").prop("checked", true);
    }
    $("#txt_cliente_id").val(Cod_Cliente);
}

/*=========================================================================================*/
/*FUNCION PARA GUARDAR LOS CLIENTE SELECCIONADOS EN LA TABLA FORMAS PAGOS ATRIBUTOS NUEVO   */
/*=========================================================================================*/
function fn_guardaFormaPagoAplicaClienteNuevo(fmp_id) {
    var id_cliente = new Array();//array q contiene los codigos de los perfiles
    //recorremos todos los checkbox seleccionados con .each
    $('input[name="chck_clientes_id[]"]:checked').each(function () {
        //$(this).val() es el valor del checkbox correspondiente
        id_cliente.push($(this).val());
    });
    send = {"aplica_cliente_nuevo": 1};
    send.fmp_id = fmp_id;
    send.id_cliente = id_cliente;
    send.aplica = 4;
    send.descripcionformapago = $("#fmp_descripcion").val();
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminFormasPago/config_formaspago.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                }
            }
        }
    });	
}

/*=================================================================*/
/*FUNCION PARA CARGAR EL TIPO DE FACTURACION DE LA FORMA DE PAGO  */
/*===============================================================*/
function fn_cargarTipoFacturacion() {
    send = {"cargarTipoFacturacion": 1};
    send.accion = 1;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            html = "";
            $("#sel_tipo_facturacion").html("");
            $('#sel_tipo_facturacion').html("<option selected value='0'>----Seleccione Tipo de Facturaci&oacute;n----</option>");
            for (i = 0; i < datos.str; i++) {
                html = html + "<option value='" + datos[i]['IDTipoFacturacion'] + "'>" + datos[i]['tf_descripcion'] + "</option>";
            }
        }
        $("#sel_tipo_facturacion").append(html);
        $("#sel_tipo_facturacion").change(function () {
        });
    });
}

/*====================================================================================*/
/*FUNCION PARA GURDAR EL TIPO DE FACTURACION DE LA FORMA DE PAGO EN LAS COLECCIONES   */
/*===================================================================================*/
function fn_guardarTipoFacturacion(fmp_id) {
    IDTipoFacturacion = $("#sel_tipo_facturacion").val();
    send = {"guardarTipoFacturacionColeccion": 1};
    send.accion = 1;
    send.IDTipoFacturacion = IDTipoFacturacion;
    send.fmp_id = fmp_id;
    send.url = 'x';
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
        }
    });
}

//fn_selecciondoble
/*==============================================================================*/
/*FUNCION PARA CARGAR EL TIPO DE FACTURACION DE LA FORMA DE PAGO EN MODIFICAR  */
/*=============================================================================*/
function fn_cargarTipoFacturacionModifica(Cod_Forma_Pago) {
    fn_cargarTipoFacturacion();
    send = {"cargarTipoFacturacionModifica": 1};
    send.accion = 2;
    send.Cod_Forma_Pago = Cod_Forma_Pago;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                tipo_facturacion = (datos[i]['IDTipoFacturacion']);
                $("#sel_tipo_facturacion").val(tipo_facturacion);
            }
        }
    });
}

/*=======================================================================================================*/
/*FUNCION PARA GURDAR EL TIPO DE FACTURACION DE LA FORMA DE PAGO EN LAS COLECCIONES CUANDO MODIFICAMOS  */
/*======================================================================================================*/
function fn_guardarTipoFacturacionModifica(fmp_id) {
    var IDTipoFacturacion = $("#sel_tipo_facturacion").val();
    send = {"guardarTipoFacturacionColeccion": 1};
    send.accion = 2;
    send.IDTipoFacturacion = IDTipoFacturacion;
    send.fmp_id = fmp_id;
    send.url = 'x';
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
        }
    });
}

/*==============================================================================*/
/*FUNCION PARA CARGAR URL DE IMPRIME TICKET DE LA FORMAA DE PAGO EN MODIFICAR   */
/*=============================================================================*/
function fn_cargarUrlImprimeTicket(Cod_Forma_Pago) {
    send = {"cargarUrlImprimeTicket": 1};
    send.accion = 3;
    send.Cod_Forma_Pago = Cod_Forma_Pago;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var imprime = datos[i]['imprimevoucher'];
                if (imprime === 1) {
                    $("#div_urlImprimeTicket").show();
                    $("#url_imprimeTicket").val(datos[i]['url_imprime_ticket']);
                } else {
                    $("#div_urlImprimeTicket").hide();
                }
            }
        }
    });
}

/*=================================================*/
/*FUNCION PARA GUARDAR LA URL SI IMPRIME TICKET    */
/*=================================================*/
function fn_guardarUrlImprieTicket(fmp_id) {
    URL_imprimeTicket = $("#url_imprimeTicket").val();
    send = {"guardarUrlImprieTicket": 1};
    send.accion = 3;
    send.URL_imprimeTicket = URL_imprimeTicket;
    send.fmp_id = fmp_id;
    $.getJSON("../adminFormasPago/config_formaspago.php", send, function (datos) {
        if (datos.str > 0) {
        }
    });
}

/*=================================================*/
/*           COLECCION FORMKAS DE PAGO             */
/*=================================================*/
function fn_cargaFormasPagoColecciondeDatos(IDFormaPago) {
    $("#IDFormaPago").val(IDFormaPago);
    var html = '<tr class="bg-primary"><th class="text-center" style="width: 30%">Descripci&oacute;n</th><th class="text-center">Dato</th><th>Especifica Valor</th><th>Obligatorio</th><th class="text-center">Tipo</th><th class="text-center">Valor</th><th>Activo</th></tr>';
    send = {"administrarColeccionFormasPago": 1};
    send.accion = 'C';
    send.IDCadena = $("#cadenas").val();
    send.IDFormaPago = IDFormaPago;
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminFormasPago/config_formaspago.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    var valorPolitica = evaluarValorPolitica(datos[i]);
                    html += '<tr id="' + i + 'id' + '" onclick="fn_seleccionarColeccion(' + i + ',\'' + datos[i]['ID_ColeccionFormapago'] + '\', \'' + datos[i]['descripcion_coleccion'] + '\', \'' + datos[i]['ID_ColeccionDeDatosFormapago'] + '\')"  class="text-center"><td>' + datos[i]['descripcion_coleccion'] + '</td><td>' + datos[i]['descripcion_dato'] + '</td>';
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
                $("#formaspago_coleccion").html(html);
            } else {
                html = html + '<tr><th colspan="14" class="text-center">No existen registros.</th></tr>';
                $("#formaspago_coleccion").html(html);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function evaluarValorPolitica(fila) {
    var tipoDato = fila.tipodedato.trim().toLowerCase();
    switch (tipoDato) {
        case 'entero':
            return fila.entero;
            break;
        case 'caracter':
            return fila.caracter;
            break;
        case 'seleccion':
            return fila.bitt;
            break;
        case 'numerico':
            return fila.numerico;
            break;
        case 'fecha':
            return fila.fecha
            break;
        case 'fecha inicio-iin':
            return fila.fechaIni + ' - ' + fila.fechaFin;
            break;
        case 'minimo-maximo':
            return fila.min + ' - ' + fila.max;
            break;
        default:
            return fila.caracter;
            break;
        //code block
    }
}

function fn_seleccionarColeccion(filaA, IDColeccion, nombreColeccion, IDColecciondeDatos) {
    $("#formaspago_coleccion tr").removeClass("success");
    $("#" + filaA + 'id' + "").addClass("success");
    lc_nombreColeccion = nombreColeccion;
    lc_IDColeccionFormasPago_edit = IDColeccion;
    lc_IDColeccionDeDatosFormasPago_edit = IDColecciondeDatos;
}

function fn_nuevaColeccion() {
    $("#tipos_de_dato").hide();
    $('#mdl_nuevaColeccion').modal('show');
    $('#ModalModificar').modal('hide');
    fn_DetalleColeccion();
    fn_limpiarcampos();
}

function fn_verModal() {
    $('#mdl_nuevaColeccion').modal('hide');
    $('#ModalModificar').modal('show');
}

function fn_DetalleColeccion() {
    var html = '<tr class="bg-primary"><th class="text-center">Descripci&oacute;n</th></tr>';
    send = {"detalleColeccionFormasPago": 1};
    send.accion = 'D';
    send.IDCadena = $("#cadenas").val();
    send.IDFormaPago = '0';
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded",
        url: "../adminFormasPago/config_formaspago.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    html += '<tr id="' + i + 'det_id' + '" onclick="fn_seleccionarDetalleColeccion(' + i + ',\'' + datos[i]['ID_ColeccionFormapago'] + '\',\'' + datos[i]['Descripcion'] + '\')"  class="text-left"><td>' + datos[i]['Descripcion'] + '</td>';
                    html += '</tr>';
                }
                $("#coleccion_descripcion").html(html);
            } else {
                html = html + '<tr><th colspan="6" class="text-center">No existen registros.</th></tr>';
                $("#coleccion_descripcion").html(html);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
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
    var IDFormaPago = $("#IDFormaPago").val();
    var html = '<tr class="bg-primary"><th class="text-center">Datos</th></tr>';
    send = {"datosColeccionFormasPago": 1};
    send.accion = 'E';
    send.IDCadena = $("#cadenas").val();
    send.IDFormaPago = IDFormaPago;
    send.IDColeccionFormaPago = IDDetalleColeccion;
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded",
        url: "../adminFormasPago/config_formaspago.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    html += '<tr id="' + i + 'dat_id' + '" onclick="fn_seleccionarDatosColeccion(' + i + ',\'' + datos[i]['ID_ColeccionDeDatosFormapago'] + '\',' + datos[i]['especificarValor'] + ',' + datos[i]['obligatorio'] + ',\'' + datos[i]['tipodedato'] + '\')" class="text-left"><td>' + datos[i]['datos'] + '</td>';
                    html += '</tr>';
                }
                $("#coleccion_datos").html(html);
            } else {
                html = html + '<tr><th colspan="6" class="text-center">No existen registros.</th></tr>';
                $("#coleccion_datos").html(html);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
    lc_IDColeccionFormasPago = IDDetalleColeccion;
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
    $('#tipo_fecha').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });
    $('#FechaInicial').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });
    $('#FechaFinal').daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });
    $("#tipo_bit").bootstrapSwitch('state', true);
    lc_IDColeccionDeDatosFormasPago = IDDatosColeccion;
}

function fn_guardarFormasPagoColeccion() {
    var IDColeccion = $('#coleccion_datos').find("tr.success").attr("id");
    if (IDColeccion) {
        Accion = 'I';
        var IDFormaPago = $("#IDFormaPago").val();
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
        var tipo_bit = 0;
        if (estado) {
            tipo_bit = 1;
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
        send = {"guardarFormasPagoColeccion": 1};
        send.accion = Accion;
        send.IDColecciondeDatosFormasPago = lc_IDColeccionDeDatosFormasPago;
        send.IDColeccionFormasPago = lc_IDColeccionFormasPago;
        send.IDFormaPago = IDFormaPago;
        send.varchar = $("#tipo_varchar").val();
        send.entero = tipo_entero;
        send.fecha = $("#tipo_fecha").val();
        send.seleccion = tipo_bit;
        send.numerico = tipo_numerico;
        send.fecha_inicio = $("#FechaInicial").val();
        send.fecha_fin = $("#FechaFinal").val();
        send.minimo = rango_minimo;
        send.maximo = rango_maximo;
        send.IDUsuario = $("#idUser").val();
        $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminFormasPago/config_formaspago.php", data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    alertify.success("Datos guardados correctamente.");
                    fn_cargaFormasPagoColecciondeDatos(IDFormaPago);
                    $('#mdl_nuevaColeccion').modal('hide');
                    $('#ModalModificar').modal('show');
                } else {
                    var error = datos.str;
                    error = error.substr(54, 40);
                    alertify.error(error);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(jqXHR);
                alert(textStatus);
                alert(errorThrown);
            }
        });
    } else {
        alertify.error("Debe Seleccionar un Dato de Colecci&oacute;n.");
    }
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

function fn_editColeccionFormaPago() {
    var IDColeccion = $('#formaspago_coleccion').find("tr.success").attr("id");
    if (IDColeccion) {
        var Accion = 'F';
        var IDFormaPago = $("#IDFormaPago").val();
        $('#mdl_editColeccion').modal('show');
        $('#ModalModificar').modal('hide');
        $('#edit_nombreColeccion').text(lc_nombreColeccion);
        $('#tipo_fecha_edit').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });
        $('#FechaInicial_edit').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });
        $('#FechaFinal_edit').daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });
        $("#tipo_bit_edit").bootstrapSwitch('state', true);
        send = {"cargaFormaPagoColeccion_edit": 1};
        send.accion = Accion;
        send.IDCadena = $("#cadenas").val();
        send.IDFormaPago = IDFormaPago;
        send.IDColeccionFormaPago = lc_IDColeccionFormasPago_edit;
        send.IDColecciondeDatosFormaPago = lc_IDColeccionDeDatosFormasPago_edit;
        $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminFormasPago/config_formaspago.php", data: send,
            success: function (datos) {
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
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(jqXHR);
                alert(textStatus);
                alert(errorThrown);
            }
        });
    } else {
        alertify.error("Debe Seleccionar un Registro.");
    }
}

function fn_modificaFormaPagoColeccion() {
    Accion = 'U';
    var IDFormaPago = $("#IDFormaPago").val();
    var tipo_entero = 0;
    var tipo_numerico = 0;
    var rango_minimo = 0;
    var rango_maximo = 0;
    if ($("#tipo_entero_edit").val() === '') {
        tipo_entero = 'null';
    } else {
        tipo_entero = $("#tipo_entero_edit").val();
    }
    seleccion = $("#tipo_bit_edit").bootstrapSwitch('state');
    var tipo_bit_edit = 0;
    if (seleccion === true) {
        tipo_bit_edit = 1;
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
    send = {"modificarFormaPagoColeccion": 1};
    send.accion = Accion;
    send.IDColecciondeDatosFormasPago = lc_IDColeccionDeDatosFormasPago_edit;
    send.IDColeccionFormasPago = lc_IDColeccionFormasPago_edit;
    send.IDFormaPago = IDFormaPago;
    send.varchar = $("#tipo_varchar_edit").val();
    send.entero = tipo_entero;
    send.fecha = $("#tipo_fecha_edit").val();
    send.seleccion = tipo_bit_edit;
    send.numerico = tipo_numerico;
    send.fecha_inicio = $("#FechaInicial_edit").val();
    send.fecha_fin = $("#FechaFinal_edit").val();
    send.minimo = rango_minimo;
    send.maximo = rango_maximo;
    send.IDUsuario = $("#idUser").val();
    if ($("#check_estado").is(':checked')) {
        send.estado = 1;
    } else {
        send.estado = 0;
    }
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminFormasPago/config_formaspago.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                alertify.success("Datos actualizados correctamente.");
                fn_cargaFormasPagoColecciondeDatos(IDFormaPago);
                $('#mdl_editColeccion').modal('hide');
                $('#ModalModificar').modal('show');
            } else {
                var error = datos.str;
                error = error.substr(54, 40);
                alertify.error(error);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function cargarCodigoRespuestaDLLGerente() {
    var html = "";
    send = {};
    send.metodo = "cargarCodigoRespuestaDLLGerente";
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminFormasPago/clienteFormasPago.php",
        data: send,
        success: function (datos) {
            for (var i = 0; i < datos.length; i++) {
                html += "<option value='" + datos[i]['codFormaPago'] + "'>" + datos[i]['nombre'] + "</option>";
            }
            $('#slcDLL').html(html);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert("Error: " + thrownError);
        }
    });
}