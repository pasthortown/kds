/* global alertify */

////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: CREACION DE CLIENTES  //////////////////////////////////////////////////////////
////////////////TABLAS: Cliente ////////////////////////////////////////////////////////////////////////
////////FECHA CREACION: 23/08/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

$(document).ready(function () {
    fn_btn("agregar", 1);
    fn_clientes();
});

/*=================================================================*/
/*FUNCION PARA CARGAR AL INICIO TABLA CLIENTES POR FORMAS DE PAGO  */
/*=================================================================*/
function fn_clientes() {
    var send = {"cargarClientes": 1};
    send.accion = 1;
    $.getJSON("../adminclientes/config_adminclientes.php", send, function (datos) {
        $("#tablaclientes").show();
        var html = "<thead><tr class='active'>";
        html += "<th style='text-align:center'>Cliente Nombre</th>";
        html += "<th style='text-align:center'>Cédula/Ruc</th>";
        html += "<th style='text-align:center'>Teléfono</th>";
        html += "<th style='text-align:center'>Dirección</th>";
        html += "<th style='text-align:center'>Ciudad</th>";
        html += "<th style='text-align:center'>Email</th>";
        html += "</tr></thead>";
        $("#tablaclientes").empty();
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += "<tr id='" + i + "' onclick='fn_seleccion(" + i + ",\"" + datos[i]["cli_id"] + "\");' ondblclick='fn_selecciondoble(" + i + ",\"" + datos[i]["cli_id"] + "\")'>";
                html += "<td style='text-align: center; width:500px'>" + datos[i]["cli_nombres"] + ' ' + datos[i]['cli_apellidos'] + "</td>";
                html += "<td style='text-align: center; width:200px'>" + datos[i]["cli_documento"] + "</td>";
                html += "<td style='text-align: center; width:200px'>" + datos[i]["cli_telefono"] + "</td>";
                html += "<td style='text-align: center; width:800px'>" + datos[i]["cli_direccion"] + "</td>";
                html += "<td style='text-align: center; width:200px'>" + datos[i]["ciu_nombre"] + "</td>";
                html += "<td style='text-align: center; width:100px'>" + datos[i]["cli_email"] + "</td></tr>";

                $("#tablaclientes").html(html);
                $("#tablaclientes").dataTable({"destroy": true});
                $("#tablaclientes_length").hide();
                $("#tablaclientes_paginate").addClass("col-xs-10");
                $("#tablaclientes_info").addClass("col-xs-10");
                $("#tablaclientes_length").addClass("col-xs-6");
            }
        } else {
            html += "<tr>";
            html += "<td colspan='6'>No existen datos para esta cadena.</td></tr>";
            $("#tablaclientes").html(html);
        }
    });
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
        $("#btn_" + boton).prop("disabled", true);
        $("#btn_" + boton).addClass("botonbloqueado");
        $("#btn_" + boton).removeClass("botonhabilitado");
    }
}

function fn_cargarTipoDocumento() {
    var html = "";
    send = {"traerTipoDocumento": 1};
    send.accion = 2;

    $.getJSON("../adminclientes/config_adminclientes.php", send, function (datos) {
        if (datos.str > 0) {
            $("#sel_tipodocumento").html("");
            $("#sel_tipodocumento").html("<option selected value='0'>-----Seleccione-----</option>");
            for (i = 0; i < datos.str; i++) {
                html = html + "<option value='" + datos[i]["tpdoc_id"] + "'>" + datos[i]["tpdoc_descripcion"] + "</option>";
            }
        }
        $("#sel_tipodocumento").append(html);
    });
}

function fn_cargarCiudad() {
    var html = "";
    send = {"traerCiudad": 1};
    send.accion = 3;

    $.getJSON("../adminclientes/config_adminclientes.php", send, function (datos) {
        if (datos.str > 0) {
            $("#sel_ciudad").html("");
            $("#sel_ciudad").html("<option selected value='0'>------------------------Seleccione------------------------</option>");
            for (i = 0; i < datos.str; i++) {
                html = html + "<option value='" + datos[i]["ciu_id"] + "'>" + datos[i]["ciu_nombre"] + "</option>";
            }
        }
        $("#sel_ciudad").append(html);
    });
}

function fn_guardarClienteFormasPago() {
    if ($("#cli_nombres").val() == "") {
        alertify.error("Ingrese nombres cliente");
        return false;
    }
    if ($("#cli_apellidos").val() == "") {
        alertify.error("Ingrese apellidos cliente");
        return false;
    }
    if ($("#sel_tipodocumento").val() == 0) {
        alertify.error("Seleccione documento cliente");
        return false;
    }
    if ($("#cli_documento").val() == "") {
        alertify.error("Ingrese documento");
        return false;
    }
    if ($("#cli_telefono").val() == "") {
        alertify.error("Ingrese teléfono cliente");
        return false;
    }
    if ($("#sel_ciudad").val() == 0) {
        alertify.error("Seleccione ciudad cliente");
        return false;
    }
    if ($("#cli_direccion").val() == "") {
        alertify.error("Ingrese dirección cliente.");
        return false;
    }
    if ($("#cli_email").val() == "") {
        alertify.error("Ingrese email cliente");
        return false;
    }

    if ($("#sel_tipodocumento").val() != 3) {
        if (fn_validarDocumento()) {
            usr_email = $("#cli_email").val();
            if (usr_email != "") {
                var regex = /[\w-\.]{2,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;

                if (!(regex.test(usr_email.trim()))) {
                    alertify.error("La dirección de correo no es válida");
                    return false;
                }
            }

            send = {"guardarClienteFormasPago": 1};
            send.accion = 1;
            send.cli_nombres = $("#cli_nombres").val();
            send.cli_apellidos = $("#cli_apellidos").val();
            send.sel_tipodocumento = $("#sel_tipodocumento").val();
            send.cli_documento = $("#cli_documento").val();
            send.cli_telefono = $("#cli_telefono").val();
            send.cli_direccion = $("#cli_direccion").val();
            send.sel_ciudad = $("#sel_ciudad").val();
            send.cli_email = $("#cli_email").val();
            send.sel_formapago = $("#sel_formapago").val();

            $.getJSON("../adminclientes/config_adminclientes.php", send, function (datos) {
                if (datos.str > 0) {
                    alertify.success("Se agregó correctamente el cliente.");
                    fn_clientes();
                    $("#modalClientes").modal("hide");
                }
            });
        } else {
            alertify.error("Cédula incorrecta");
            return false;
        }
    } else {
        usr_email = $("#cli_email").val();
        if (usr_email != "") {
            var regex = /[\w-\.]{2,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;

            if (!(regex.test(usr_email.trim()))) {
                alertify.error("La dirección de correo no es válida");
                return false;
            }
        }

        send = {"guardarClienteFormasPago": 1};
        send.accion = 1;
        send.cli_nombres = $("#cli_nombres").val();
        send.cli_apellidos = $("#cli_apellidos").val();
        send.sel_tipodocumento = $("#sel_tipodocumento").val();
        send.cli_documento = $("#cli_documento").val();
        send.cli_telefono = $("#cli_telefono").val();
        send.cli_direccion = $("#cli_direccion").val();
        send.sel_ciudad = $("#sel_ciudad").val();
        send.cli_email = $("#cli_email").val();
        send.sel_formapago = $("#sel_formapago").val();

        $.getJSON("../adminclientes/config_adminclientes.php", send, function (datos) {
            if (datos.str > 0) {
                alertify.success("Se agregó correctamente el cliente");
                fn_clientes();
                $("#modalClientes").modal("hide");
            }
        });
    }
}

function fn_agregarClienteFormasPago() {
    $("#titulomodalclientes").empty();
    $("#titulomodalclientes").append("Nuevo Cliente: ");
    $("#cli_nombres").val("");
    $("#cli_apellidos").val("");
    $("#sel_tipodocumento").val(0);
    $("#cli_documento").val("");
    $("#cli_telefono").val("");
    $("#cli_direccion").val("");
    $("#sel_ciudad").val(0);
    $("#cli_email").val("");
    $("#sel_formapago").val(0);

    fn_cargarTipoDocumento();
    fn_cargarCiudad();

    $("#modalClientes").modal("show");
    ////////////Colocar foco en cualquier campo de la modal////////////////// CP
    $("#modalClientes").on("shown.bs.modal", function () {
        $("#cli_nombres").focus();
    });

    $("#pnl_pcn_btn").html("");
    $("#pnl_pcn_btn").html('<button type="button" onclick="fn_guardarClienteFormasPago()" class="btn btn-primary">Aceptar</button><button type="button" class="btn btn-default" data-dismiss="modal" onclick="">Cancelar</button>');
}

function aMays(e, elemento) {
    tecla = (document.all) ? e.keyCode : e.which;
    elemento.value = elemento.value.toUpperCase();
}

function fn_selecciondoble(fila, codigo) {
    Cod_Cliente = codigo;
    $("#txt_cliente").val(Cod_Cliente);
    $("#tablaclientes tr").removeClass("success");
    $("#" + fila + "").addClass("success");
    fn_modificar(codigo);
}

function fn_seleccion(fila, codigo) {
    Cod_Cliente = codigo;
    $("#tablaclientes tr").removeClass("success");
    $("#" + fila + "").addClass("success");
    $("#txt_cliente").val(Cod_Cliente);
}

/*=============================================================================================*/
/*FUNCION QUE LLAMA A LA FUNCION DE TRAER DATOS PARA MODIFICAR Y ACCION=2 CUANDO ES MODIFICAR  */
/*=============================================================================================*/
function fn_modificar(codigo) {
    $("#modalClientes").modal("show");
    fn_traerCliente();
    $("#pnl_pcn_btn").html("");
    $("#pnl_pcn_btn").html('<button type="button" onclick="fn_guardarClienteModifica();" class="btn btn-primary">Aceptar</button><button type="button" class="btn btn-default" data-dismiss="modal" onclick="">Cancelar</button>');
}

function fn_traerCliente() {
    send = {"traerCliente": 1};
    send.accion = 5;
    send.cli_id = $("#txt_cliente").val();

    $.getJSON("../adminclientes/config_adminclientes.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                $("#titulomodalclientes").empty();
                $("#titulomodalclientes").append(datos[i]["cli_nombres"] + " " + datos[i]["cli_apellidos"]);
                $("#cli_nombres").val(datos[i]["cli_nombres"]);
                $("#cli_apellidos").val(datos[i]["cli_apellidos"]);
                $("#cli_documento").val(datos[i]["cli_documento"]);
                $("#cli_telefono").val(datos[i]["cli_telefono"]);
                $("#cli_direccion").val(datos[i]["cli_direccion"]);
                $("#cli_email").val(datos[i]["cli_email"]);
                tipo_doc = datos[i]["tpdoc_id"];

                var tipo = "";
                send = {"traerTipoDocumento": 1};
                send.accion = 2;

                $.getJSON("../adminclientes/config_adminclientes.php", send, function (datos) {
                    if (datos.str > 0) {
                        $("#sel_tipodocumento").html("<option selected value='0'>-----Seleccione-----</option>");
                        $("#sel_tipodocumento").empty();
                        for (i = 0; i < datos.str; i++) {
                            tipo = tipo + "<option value='" + datos[i]["tpdoc_id"] + "'>" + datos[i]["tpdoc_descripcion"] + "</option>";
                        }
                    }
                    $("#sel_tipodocumento").append(tipo);
                    $("#sel_tipodocumento").val(tipo_doc);
                });

                ciudad = datos[i]["ciu_id"];

                var html = "";
                send = {"traerCiudad": 1};
                send.accion = 3;

                $.getJSON("../adminclientes/config_adminclientes.php", send, function (datos) {
                    if (datos.str > 0) {
                        $("#sel_ciudad").html("<option selected value='0'>------------------------Seleccione------------------------</option>");
                        $("#sel_ciudad").empty();
                        for (i = 0; i < datos.str; i++) {
                            html = html + "<option value='" + datos[i]["ciu_id"] + "'>" + datos[i]["ciu_nombre"] + "</option>";
                        }
                    }
                    $("#sel_ciudad").append(html);
                    $("#sel_ciudad").val(ciudad);
                });
            }
        }
    });
}

function fn_guardarClienteModifica() {
    if ($("#cli_nombres").val() == "") {
        alertify.error("Ingrese nombres cliente");
        return false;
    }
    if ($("#cli_apellidos").val() == "") {
        alertify.error("Ingrese apellidos cliente");
        return false;
    }
    if ($("#sel_tipodocumento").val() == 0) {
        alertify.error("Seleccione documento cliente");
        return false;
    }
    if ($("#cli_documento").val() == "") {
        alertify.error("Ingrese documento");
        return false;
    }
    if ($("#cli_telefono").val() == "") {
        alertify.error("Ingrese teléfono cliente");
        return false;
    }
    if ($("#sel_ciudad").val() == 0) {
        alertify.error("Seleccione ciudad cliente");
        return false;
    }
    if ($("#cli_direccion").val() == "") {
        alertify.error("Ingrese dirección cliente");
        return false;
    }
    if ($("#cli_email").val() == "") {
        alertify.error("Ingrese email cliente");
        return false;
    }

    if ($("#sel_tipodocumento").val() != 3) {
        if (fn_validarDocumento()) {
            usr_email = $("#cli_email").val();
            if (usr_email != "") {
                var regex = /[\w-\.]{2,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;

                if (!(regex.test(usr_email.trim()))) {
                    alertify.error("La dirección de correo no es válida");
                    return false;
                }
            }

            send = {"guardarClienteModifica": 1};
            send.accion = 2;
            send.cli_nombres = $("#cli_nombres").val();
            send.cli_apellidos = $("#cli_apellidos").val();
            send.sel_tipodocumento = $("#sel_tipodocumento").val();
            send.cli_documento = $("#cli_documento").val();
            send.cli_telefono = $("#cli_telefono").val();
            send.cli_direccion = $("#cli_direccion").val();
            send.sel_ciudad = $("#sel_ciudad").val();
            send.cli_email = $("#cli_email").val();
            send.cli_id = $("#txt_cliente").val();
            $.getJSON("../adminclientes/config_adminclientes.php", send, function (datos) {
                if (datos.str > 0) {
                    alertify.success("Se actualizó correctamente el cliente.");
                    fn_clientes();
                    $("#modalClientes").modal("hide");
                }
            });
        } else {
            alertify.error("Cédula incorrecta.");
            return false;
        }
    } else {
        usr_email = $("#cli_email").val();
        if (usr_email != "") {
            var regex = /[\w-\.]{2,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;

            if (!(regex.test(usr_email.trim()))) {
                alertify.error("La dirección de correo no es válida");
                return false;
            }
        }

        send = {"guardarClienteModifica": 1};
        send.accion = 2;
        send.cli_nombres = $("#cli_nombres").val();
        send.cli_apellidos = $("#cli_apellidos").val();
        send.sel_tipodocumento = $("#sel_tipodocumento").val();
        send.cli_documento = $("#cli_documento").val();
        send.cli_telefono = $("#cli_telefono").val();
        send.cli_direccion = $("#cli_direccion").val();
        send.sel_ciudad = $("#sel_ciudad").val();
        send.cli_email = $("#cli_email").val();
        send.cli_id = $("#txt_cliente").val();
        $.getJSON("../adminclientes/config_adminclientes.php", send, function (datos) {
            if (datos.str > 0) {
                alertify.success("Se actualizó correctamente el cliente");
                fn_clientes();
                $("#modalClientes").modal("hide");
            }
        });
    }
}

function fn_validarDocumento() {
    var numero = $("#cli_documento").val();
    var longitud = numero.length;
    var valido = false;

    if (/^\d+$/.test(numero)) {
        if (longitud === 10 || longitud === 13) {
            var digitos = numero.split("").map(Number);
            var codProvincia = digitos[0] * 10 + digitos[1];

            //1-24: provincias
            //30: ecuatorianos registrados en el exterior
            if ((codProvincia > 0 && codProvincia <= 24) || (codProvincia === 30)) {
                var RUC = "";
                var digitoVerificador;

                //El único mecanismo de validación formalmente aceptado para personas naturales es el dígito verificador
                //Por ende, para toda persona natural se aplica algoritmo verificador de módulo 10
                //De igual manera a los RUCs cuyo tercer dígito está entre 0 y 5
                if (longitud === 10 || digitos[2] >= 0 && digitos[2] <= 5) {
                    if (longitud === 13) {
                        for (var i = 0; i < 3; i++) {
                            RUC = String(digitos.pop()) + RUC;
                        }
                    }
                    if (RUC === "" || RUC === "001") {
                        digitoVerificador = digitos.pop();
                        valido = (modulo10(digitos) === digitoVerificador);
                    }

                    //Para RUCs cuyo tercer dígito es 6 se aplica algoritmo verificador de módulo 11
                    //El dígito verificador es el noveno
                } else if (digitos[2] === 6) {
                    for (var i = 0; i < 4; i++) {
                        RUC = String(digitos.pop()) + RUC;
                    }
                    if (RUC === "0001") {
                        digitoVerificador = digitos.pop();
                        valido = (modulo11(digitos) === digitoVerificador);
                    }

                    //Para RUCs cuyo tercer dígito es 9 se aplica algoritmo verificador de módulo 11
                } else if (digitos[2] === 9) {
                    for (var i = 0; i < 3; i++) {
                        RUC = String(digitos.pop()) + RUC;
                    }
                    if (RUC === "001") {
                        digitoVerificador = digitos.pop();
                        valido = (modulo11(digitos) === digitoVerificador);
                    }
                }
            }
        }
    }

    return valido;
}

function validar(e) { // 1
    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla == 8) {
        return true; // 3
    }
    patron = /[A-Za-z\s]/; // 4
    te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
}

//El botón desencadena la accion
function fn_validarCorreo() {
    var regex = /[\w-\.]{2,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;

    if (regex.test($("#cli_email").val().trim())) {
        alert("Correo validado");
    } else {
        alert("La dirección de correo no es válida");
        return false;
    }
}

function fn_numerosDocumento(e) {
    if ($("#sel_tipodocumento").val() != 3) {
        tecla = (document.all) ? e.keyCode : e.which;
        if (tecla == 8 || tecla == 0) {
            return true;
        }
        patron = /\d/;
        te = String.fromCharCode(tecla);
        return patron.test(te);
    }
}

function fn_limpiaDocumento() {
    $("#cli_documento").val("");
}