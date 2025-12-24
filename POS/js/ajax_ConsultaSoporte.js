/*
FECHA CREACION   : 16/07/2016 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion de promociones
*/

/* global alertify */

$(document).ready(function () {
    fn_btn("agregar", 1);
    $("#divSeleccionarConsulta").hide();
    $("#divEstadoRadioButton").hide();
    $("#Tbl_ConsultaSoporte").hide();
    $("#SeleccionarCajero").prop("disabled", true);
    $("#SeleccionarAdmin").prop("disabled", true);
    $("#SeleccionarEstacion").prop("disabled", true);
    $("#divSeleccionarCajero").hide();
    $("#divSeleccionarEstacion").hide();
    fn_cargarRestaurantes();
});

/* SELECCIONA DE RESTAURANTE */
function fn_cargarRestaurantes() {
    var send;
    var Accion = "C";
    var html = "";
    send = { "cargaRestaurantes": 1 };
    send.accion = Accion;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminSoporte/config_ConsultaSoporte.php",
        data: send,
        success: function (datos) {
            if (datos.str === 0) {
                $("#divSeleccionarConsulta").hide();
                $("#divEstadoRadioButton").hide();
                $("#Tbl_ConsultaSoporte").hide();
                alertify.error("No existen datos para esta Cadena");
            } else if (datos.str > 0) {
                $("#selectRestaurante").html("");
                $('#selectRestaurante').html("<option selected value='0'>--------------   Seleccione Restaurante   --------------</option>");
                for (var i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['IDRestaurante'] + "'>" + datos[i]['Descripcion'] + "</option>";
                    $("#selectRestaurante").append(html);
                }
                $("#selectRestaurante").chosen();
                $("#selectRestaurante").change(function () {
                    var IDRestaurante = $("#selectRestaurante").val();
                    $("#hdn_IDRestaurante").val(IDRestaurante);
                    $("#selectConsulta").val("0");
                    $("#divSeleccionarConsulta").show();
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

/* CARGA Y VISUALIZA LA TABLA CON EL DETALLE SEGUN EL TIPO DE CONSULTA:
 * item: se refiere al tipo de consulta, 1=PERIODO, 2=DESASIGNADO, 3=FIN DE DIA */
function fn_seleccionarConsulta(item) {
    var SeleccionConsulta = item;
    if (SeleccionConsulta == 1) {
        $("#Tbl_ConsultaSoporte").show();
        $("#Tbl_ConsultaSoporteDesasignado").hide();
        $("#Tbl_ConsultaSoporteFindeDia").hide();
        fn_cargaDetallePeriodo(SeleccionConsulta);
    } else if (SeleccionConsulta == 2) {
        $("#Tbl_ConsultaSoporteDesasignado").show();
        $("#Tbl_ConsultaSoporte").hide();
        $("#Tbl_ConsultaSoporteFindeDia").hide();
        fn_cargaDetalleDesmontado(SeleccionConsulta)
    } else if (SeleccionConsulta == 3) {
        $("#Tbl_ConsultaSoporteFindeDia").show();
        $("#Tbl_ConsultaSoporte").hide();
        $("#Tbl_ConsultaSoporteDesasignado").hide();
        fn_cargaDetalleFindeDia(SeleccionConsulta)
    }
}

/* FUNCION QUE OBTIENE LOS BOTONES PARA LA ADMINISTRACIÓN */
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

/* CARGA EL DETALLE CONSULTA PERIODO */
function fn_cargaDetallePeriodo(item) {
    var send;
    var Accion = "D";
    var html = '<thead><tr class="active"><th class="text-center">Fecha Apertura</th><th class="text-center">Fecha Cierre</th><th class="text-center">Cerrado Por</th><th class="text-center">Estado</th></tr></thead>';
    send = { "cargarDetallePeriodo": 1 };
    send.accion = Accion;
    send.IDRestaurante = $("#hdn_IDRestaurante").val();
    send.SelecionConsulta = item;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminSoporte/config_ConsultaSoporte.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = (datos[i]["Estado"]);
                    html += '<tr id="' + i + "IDConsultaSoportePeriodo" + '" onclick="fn_seleccionarPeriodo(' + i + ', \'' + datos[i]["IDPeriodo"] + '\', 1, \'' + datos[i]["FechaApertura"] + '\', 0, 0, 0, 0, 0)" class="text-center"><td>' + datos[i]["FechaApertura"] + '</td><td>' + datos[i]["FechaCierre"] + '</td><td>' + datos[i]["CerradoPor"] + '</td>';
                    if (estado === "Abierto") {
                        html += '<td>Abierto</td>';
                    } else {
                        html += '<td>Cerrado</td>';
                    }
                    html += '</tr>';
                }
                $("#TblDetalle_ConsultaSoporte").html(html);
                $("#TblDetalle_ConsultaSoporte").dataTable({ "destroy": true });
                if (estado === "Abierto") {
                    $("#TblDetalle_ConsultaSoporte_length").html('<button type="button" class="btn btn-primary disabled">Abrir Periodo</button>');
                } else {
                    $("#TblDetalle_ConsultaSoporte_length").html('<button type="button" class="btn btn-primary" onclick="fn_validaSeleccion(1);">Abrir Periodo</button>');
                }
                $("#TblDetalle_ConsultaSoporte_paginate").addClass("col-xs-10");
                $("#TblDetalle_ConsultaSoporte_info").addClass("col-xs-10");
                $("#TblDetalle_ConsultaSoporte_length").addClass("col-xs-6");
            } else {
                html = html + '<tr><th colspan="4" class="text-center">No existe registros.</th></tr>';
                $("#TblDetalle_ConsultaSoporte").html(html);
                $("#TblDetalle_ConsultaSoporte_length").html('<button type="button" class="btn btn-primary">Abrir Periodo</button>');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

/* SELECCION DE UN REGISTRO DE LA TABLA SEGUN EL TIPO DE CONSULTA: 
 * parametroSeleccion: 1=PERIODO, 2=DESASIGNADO, 3=FIN DE DIA */
function fn_seleccionarPeriodo(fila, IDPeriodo, parametroSeleccion, aperturaPeriodo, IDCanalMovimientoDesasignado, cajero, IDCanalMovimientoPeriodo, fechaPeiodo, ipEstacion, idUsuario, idControlEstacion) {
    if (parametroSeleccion === 1) {
        $("#TblDetalle_ConsultaSoporte tr").removeClass("success");
        $("#" + fila + 'IDConsultaSoportePeriodo' + "").addClass("success");
        $("#hdn_IDPeriodo").val(IDPeriodo);
        $("#hdn_fechaApertura").val(aperturaPeriodo);
    } else if (parametroSeleccion === 2) {
        $("#TblDetalle_ConsultaSoporteDesasignado tr").removeClass("success");
        $("#" + fila + 'IDCanalMovimiento' + "").addClass("success");
        $("#hdn_IDCanalMovimientoDesmontado").val(IDCanalMovimientoDesasignado);
        $("#hdn_cajeroDesmontado").val(cajero);
        $("#hdn_IPEstacion").val(ipEstacion);
        $("#hdn_IDUsuario").val(idUsuario);
        $("#hdn_IDControlEstacion").val(idControlEstacion);
    } else if (parametroSeleccion === 3) {
        $("#TblDetalle_ConsultaSoporteFindeDia tr").removeClass("success");
        $("#" + fila + 'IDCanalMovimientoA' + "").addClass("success");
        $("#hdn_IDCanalMovimientoFindeDia").val(IDCanalMovimientoPeriodo);
        $("#hdn_fechaPeriodoFindeDia").val(fechaPeiodo);
        $("#hdn_IDPeriodo").val(IDPeriodo);
        $("#hdn_IPEstacion").val(ipEstacion);
        $("#hdn_IDUsuario").val(idUsuario);
    }
}

/* VALIDA LA SELECCION DE UN REGISTRO PARA LA APERTURA DE PERIODO O REIMPRESION DE REPORTES: 
   parametro: se refiere al tipo de consulta seleccionado; 1=PERIODO, 2=DESASIGNADO, 3=FIN DE DIA */
function fn_validaSeleccion(parametro) {
    if (parametro === 1) {
        var IDPeriodoSeleccionado = $("#TblDetalle_ConsultaSoporte").find("tr.success").attr("id");
        var fechaApertura = $("#hdn_fechaApertura").val();
        if (IDPeriodoSeleccionado) {
            $("#modalConfirmacion").modal("show");
            $("#lbl_tutulo").text("Abrir Periodo");
            $("#lbl_Mensajeconfirmacion").text("Desea volver abrir el periodo de la fecha:");
            $("#lbl_RegistroConfirmacion").text(fechaApertura);
            $("#btn_accion").html('<button type="button" class="btn btn-primary" onclick="fn_abrirPeriodo(1)"><span class="glyphicon glyphicon-floppy-saved"></span> Ok</button>\n\
                                   <button type="button" class="btn btn-default" data-dismiss="modal" onclick="fn_quitarSeleccion(1);"><span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>');
        } else {
            alertify.error("Debe seleccionar un registro.");
        }
    } else if (parametro === 2) {
        var IDDesasignadoSeleccionado = $("#TblDetalle_ConsultaSoporteDesasignado").find("tr.success").attr("id");
        var cajero = $("#hdn_cajeroDesmontado").val();
        if (IDDesasignadoSeleccionado) {
            $("#modalConfirmacion").modal("show");
            $("#lbl_tutulo").text("Imprimir Desmonte Cajero");
            $("#lbl_Mensajeconfirmacion").text("Desea imprimir el reporte desmonte cajero de:");
            $("#lbl_RegistroConfirmacion").text(cajero);
            $("#btn_accion").html('<button type="button" class="btn btn-primary" onclick="fn_imprimirReporte(2, 0, 2);"><span class="glyphicon glyphicon-floppy-saved"></span> Ok</button>\n\
                                   <button type="button" class="btn btn-default" data-dismiss="modal" onclick="fn_quitarSeleccion(2);"><span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>');
        } else {
            alertify.error("Debe seleccionar un registro para la reimpresion del reporte.");
        }
    } else if (parametro === 3) {
        var IDFindeDiaSeleccionado = $("#TblDetalle_ConsultaSoporteFindeDia").find("tr.success").attr("id");
        var fechaPeriodo = $("#hdn_fechaPeriodoFindeDia").val();
        if (IDFindeDiaSeleccionado) {
            $("#modalConfirmacion").modal("show");
            $("#lbl_tutulo").text("Imprimir Fin de Dia");
            $("#lbl_Mensajeconfirmacion").text("Desea imprimir el reporte fin de dia del periodo");
            $("#lbl_RegistroConfirmacion").text(fechaPeriodo);
            $("#btn_accion").html('<button type="button" class="btn btn-primary" onclick="fn_imprimirReporte(3, 0, 3);"><span class="glyphicon glyphicon-floppy-saved"></span> Ok</button>\n\
                                   <button type="button" class="btn btn-default" data-dismiss="modal" onclick="fn_quitarSeleccion(3);"><span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>');
        } else {
            alertify.error("Debe seleccionar un registro para la reimpresion del reporte.");
        }
    }
}

/* FUNCION QUE QUITA LA SELECCION DE UN REGISTRO: 
 * parametro: se refiere al tipo de consulta, 1=PERIODO, 2=DESASIGNADO, 3=FIN DE DIA */
function fn_quitarSeleccion(parametro) {
    if (parametro === 1) {
        $("#TblDetalle_ConsultaSoporte tr").removeClass("success");
    } else if (parametro === 2) {
        $("#TblDetalle_ConsultaSoporteDesasignado tr").removeClass("success");
    } else if (parametro === 3) {
        $("#TblDetalle_ConsultaSoporteFindeDia tr").removeClass("success");
    }
}

/* CARGA EL DETALLE REPORTE DESMONTADO CAJERO 
 * Esta consulta se ejecuta cuando la opcion seleccionada es; 2 = DESMONTADO */
function fn_cargaDetalleDesmontado(item) {
    var send;
    var Accion = "D";
    var html = '<thead><tr class="active"><th class="text-center">Fecha Impresi&oacute;n</th><th class="text-center">Cajero</th><th class="text-center">Estaci&oacute;n</th><th class="text-center">Estado</th></tr></thead>';
    send = { "cargarDetalleDesmontado": 1 };
    send.accion = Accion;
    send.IDRestaurante = $("#hdn_IDRestaurante").val();
    send.SelecionConsulta = item;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminSoporte/config_ConsultaSoporte.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estadoDesmontado = (datos[i]["Estado"]);
                    html += '<tr id="' + i + "IDCanalMovimiento" + '" onclick="fn_seleccionarPeriodo(' + i + ', 0, 2, 0, \'' + datos[i]["IDCanalMovimiento"] + '\', \'' + datos[i]["Cajero"] + '\', 0, 0, \'' + datos[i]["IPEstacion"] + '\', \'' + datos[i]["IDUsuario"] + '\', \'' + datos[i]["IDControlEstacion"] + '\')" class="text-center"><td>' + datos[i]["Fecha"] + '</td><td>' + datos[i]["Cajero"] + '</td><td>' + datos[i]["Estacion"] + '</td>';
                    if (estadoDesmontado === "Ejecutado") {
                        html += '<td>Ejecutado</td>';
                    } else {
                        html += '<td>Pendiente</td>';
                    }
                    html += '</tr>';
                }
                $("#TblDetalle_ConsultaSoporteDesasignado").html(html);
                $("#TblDetalle_ConsultaSoporteDesasignado").dataTable({ "destroy": true });
                $("#TblDetalle_ConsultaSoporteDesasignado_length").html('<button type="button" class="btn btn-primary" onclick="fn_validaSeleccion(2);"><span class="glyphicon glyphicon-print"></span> Imprimir Reporte</button>\n\
                                                                         <button type="button" class="btn btn-success" onclick="fn_nuevoRegistro(2);"><span class="glyphicon glyphicon-plus"></span> Crear Reporte</button>');
                $("#TblDetalle_ConsultaSoporteDesasignado_paginate").addClass("col-xs-10");
                $("#TblDetalle_ConsultaSoporteDesasignado_info").addClass("col-xs-10");
                $("#TblDetalle_ConsultaSoporteDesasignado_length").addClass("col-xs-6");
            } else {
                html = html + '<tr><th colspan="4" class="text-center">No existen registros.</th></tr>';
                $("#TblDetalle_ConsultaSoporteDesasignado").html(html);
                $("#TblDetalle_ConsultaSoporteDesasignado_length").html('<button type="button" class="btn btn-primary disabled">Imprimir Reporte</button> <button type="button" class="btn btn-success disabled" >Ingresar Reporte</button>');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

/* CARGA EL DETALLE FIN DE DIA 
 * Esta consulta se ejecuta cuando la opcion seleccionada es; 3 = FIN DE DIA */
function fn_cargaDetalleFindeDia(item) {
    var send;
    var Accion = "D";
    var html = '<thead><tr class="active"><th class="text-center">Fecha Impresi&oacute;n</th><th class="text-center">Per&iacute;odo</th><th class="text-center">Administrador</th><th class="text-center">Estado</th></tr></thead>';
    send = { "cargarDetalleFindeDia": 1 };
    send.accion = Accion;
    send.IDRestaurante = $("#hdn_IDRestaurante").val();
    send.SelecionConsulta = item;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminSoporte/config_ConsultaSoporte.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estadoFindeDia = (datos[i]["Estado"]);
                    html += '<tr id="' + i + "IDCanalMovimientoA" + '" onclick="fn_seleccionarPeriodo(' + i + ', \'' + datos[i]["IDPeriodo"] + '\', 3, 0, 0, 0, \'' + datos[i]["IDCanalMovimiento"] + '\', \'' + datos[i]["FechaPeriodo"] + '\', \'' + datos[i]["IPEstacion"] + '\', \'' + datos[i]["IDUsuario"] + '\', 0)" class="text-center"><td>' + datos[i]["FechaImpresion"] + '</td><td>' + datos[i]["FechaPeriodo"] + '</td><td>' + datos[i]["Administrador"] + '</td>';
                    if (estadoFindeDia === "Ejecutado") {
                        html += '<td>Ejecutado</td>';
                    } else {
                        html += '<td>Pendiente</td>';
                    }
                    html += '</tr>';
                }
                $("#TblDetalle_ConsultaSoporteFindeDia").html(html);
                $("#TblDetalle_ConsultaSoporteFindeDia").dataTable({ "destroy": true });
                $("#TblDetalle_ConsultaSoporteFindeDia_length").html('<button type="button" class="btn btn-primary" onclick="fn_validaSeleccion(3);"><span class="glyphicon glyphicon-print"></span> Imprimir Reporte</button> \n\
                                                                      <button type="button" class="btn btn-success" onclick="fn_nuevoRegistro(3);"><span class="glyphicon glyphicon-plus"></span> Crear Reporte</button>');
                $("#TblDetalle_ConsultaSoporteFindeDia_paginate").addClass("col-xs-10");
                $("#TblDetalle_ConsultaSoporteFindeDia_info").addClass("col-xs-10");
                $("#TblDetalle_ConsultaSoporteFindeDia_length").addClass("col-xs-6");
            } else {
                html = html + '<tr><th colspan="4" class="text-center">No existen registros.</th></tr>';
                $("#TblDetalle_ConsultaSoporteFindeDia").html(html);
                $("#TblDetalle_ConsultaSoporteFindeDia_length").html('<button type="button" class="btn btn-primary disabled">Imprimir Reporte</button> <button type="button" class="btn btn-success disabled" >Ingresar Reporte</button>');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

/* FUNCION QUE PERMITE LA REIMPRESION DE LOS REPORTES DESASIGNADO O FIN DE DIA 
 * parametro: se refiere al tipo de reporte, 2 = DESAGINDADO, 3 = FIN DE DIA
 * tipo = se refiere al tipo de accion que se ejecuta en la base, 1 = I Insert , 0 = U Update */
function fn_imprimirReporte(parametro, tipo, accion) {
    var send;
    var Accion = "R";
    var tipoAccion;
    var IDCanalMovimiento;
    var IDRestaurante = $("#hdn_IDRestaurante").val();
    if (parametro === 1) {
        IDCanalMovimiento = $("#hdn_IDPeriodo").val();
    } else if (parametro === 2) {
        IDCanalMovimiento = $("#hdn_IDCanalMovimientoDesmontado").val();
    } else if (parametro === 3) {
        IDCanalMovimiento = $("#hdn_IDCanalMovimientoFindeDia").val();
    }
    if (tipo === 0) { tipoAccion = "U"; }
    send = { "imprimirReporte": 1 };
    send.accion = Accion;
    send.tipo = tipoAccion;
    send.IDCanalMovimiento = IDCanalMovimiento;
    send.IDRestaurante = IDRestaurante;

    var sendInfoEstacion;
    sendInfoEstacion = { "infoAplicaApiImpresion": 1 };
    sendInfoEstacion.estacion = $("#hdn_IPEstacion").val();

    $.ajax({
        async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../../test_impresion/config_infoestacion.php", data: sendInfoEstacion, success: function (datos) {

            if (datos.aplicaTienda == 1) {
                IDPeriodo = $("#hdn_IDPeriodo").val();

                datos.ipEstacion = $("#hdn_IPEstacion").val();
                datos.idUsuario = $("#hdn_IDUsuario").val();
                datos.idControlEstacion = $("#hdn_IDControlEstacion").val();
                datos.periodo = IDPeriodo;
                datos.estacion = datos.IDEstacion;
                var reimpresion = accion == 2 ? 'reimpresionDesmontadoCajero' : 'reimpresionFinDelDia';
                var result = new apiServicioImpresionMantenimiento(reimpresion, null, null, datos);
                var imprime = result["imprime"];
                var mensaje = result["mensaje"];

                if (!imprime) {
                    if (accion == 2) {
                        alertify.success('Imprimiendo Desmontado de cajero...');
                        $("#modalConfirmacion").modal("hide");
                        fn_quitarSeleccion(parametro);
                    }
                    else if (accion == 3) {
                        alertify.success('Imprimiendo Fín del día...');
                        $("#modalConfirmacion").modal("hide");
                        fn_quitarSeleccion(parametro);
                    }

                } else {
                    alertify.alert(mensaje);

                    if (accion == 2) {
                        alertify.success('Error al imprimir Desmontado de cajero...');
                    }
                    else if (accion == 3) {
                        alertify.success('Error al imprimir Fín del día...');
                    }
                }
            }
            else {
                $.ajax({
                    async: false,
                    type: "POST",
                    dataType: "json",
                    contentType: "application/x-www-form-urlencoded",
                    url: "../adminSoporte/config_ConsultaSoporte.php",
                    data: send,
                    success: function (datos) {
                        alertify.success("imprimiendo reporte....");
                        $("#modalConfirmacion").modal("hide");
                        fn_quitarSeleccion(parametro);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert(jqXHR);
                        alert(textStatus);
                        alert(errorThrown);
                        alertify.error("Error al momento de guardar.");
                    }
                });
            }
        }
    });
}

/* FUNCION QUE PERMITE LA APERTURA DEL PERIODO CERRADO DE LA FECHA ACTUAL  
 * parametro: se refiere al tipo de reporte, 2 = DESAGINDADO, 3 = FIN DE DIA*/
function fn_abrirPeriodo(parametro) {
    var send;
    var Accion = "P";
    var IDRestaurante = $("#hdn_IDRestaurante").val();
    var IDPeriodo = $("#hdn_IDPeriodo").val();
    send = { "abrirPeriodo": 1 };
    send.accion = Accion;
    send.IDRestaurante = IDRestaurante;
    send.IDPeriodo = IDPeriodo;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminSoporte/config_ConsultaSoporte.php",
        data: send,
        success: function (datos) {
            alertify.success("Periodo abierto.");
            $("#modalConfirmacion").modal("hide");
            fn_quitarSeleccion(parametro);
            fn_cargaDetallePeriodo(parametro);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
            alertify.error("Error al momento de guardar.");
        }
    });
}

/* MUESTRA MODAL PARA LA IMPRESION DE REPORTES DESMONTADO O FIN DE DIA 
 * parametro: se refiere al tipo de reporte, 2 = DESAGINDADO, 3 = FIN DE DIA */
function fn_nuevoRegistro(parametro) {
    if (parametro === 2) {
        $("#modalNuevoRegistro").modal("show");
        $("#tituloModal").text("Crear Reporte Desmontado Cajero/a");
        fn_cargarSeleccionPeriodo(parametro);
        $("#divSeleccionarCajero").show();
        $("#divSeleccionarEstacion").hide();
        $("#SeleccionarCajero").val(" ");
        $("#SeleccionarCajero").prop("disabled", true);
        $("#SeleccionarAdmin").val(" ");
        $("#SeleccionarAdmin").prop("disabled", true);
        $("#btn_acciones").html('<button type="button" class="btn btn-primary" onclick="fn_crearReporte(2, 1);"><span class="glyphicon glyphicon-floppy-saved"></span> Guardar</button>\n\
                                 <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>');
    } else if (parametro === 3) {
        $("#modalNuevoRegistro").modal("show");
        $("#tituloModal").text("Crear Reporte Fin de Dia");
        fn_cargarSeleccionPeriodo(parametro);
        $("#divSeleccionarCajero").hide();
        $("#divSeleccionarEstacion").show();
        $("#SeleccionarEstacion").val(" ");
        $("#SeleccionarEstacion").prop("disabled", true);
        $("#SeleccionarAdmin").val(" ");
        $("#SeleccionarAdmin").prop("disabled", true);
        $("#btn_acciones").html('<button type="button" class="btn btn-primary" onclick="fn_crearReporte(3, 1);"><span class="glyphicon glyphicon-floppy-saved"></span> Guardar</button>\n\
                                 <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>');
    }
}

/* CARGA PERIODO ACTUAL PARA LA SELECCION EN MODAL CREAR REPORTE 
 * Esta consulta se utiliza siempre y  cuando la consuta seleccionada sea: 1 = DESMONTADO ó 3 = FIN DE DIA */
function fn_cargarSeleccionPeriodo(parametro) {
    var send;
    var Accion = "P";
    var html = "";
    var IDRestaurante = $("#hdn_IDRestaurante").val();
    send = { "cargarSeleccionPeriodo": 1 };
    send.accion = Accion;
    send.IDRestaurante = IDRestaurante;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminSoporte/config_ConsultaSoporte.php",
        data: send,
        success: function (datos) {
            if (datos.str === 0) {
                $("#modalNuevoRegistro").modal("hide");
                alertify.error("No existen un periodo creado.");
            } else if (datos.str > 0) {
                $("#SeleccionarPeriodo").html("");
                $("#SeleccionarPeriodo").html("<option selected value='0'>-------- Seleccione Periodo --------</option>");
                for (var i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['IDPeriodo'] + "'>" + datos[i]['Periodo'] + "</option>";
                    $("#SeleccionarPeriodo").append(html);
                }
                $("#SeleccionarPeriodo").change(function () {
                    var IDPeriodo = $("#SeleccionarPeriodo").val();
                    if (IDPeriodo !== "0") {
                        $("#hdn_IDPeriodo").val(IDPeriodo);
                        if (parametro === 2) {
                            fn_cargarSeleccionCajero(IDPeriodo);
                            $("#SeleccionarCajero").prop("disabled", false);
                        } else {
                            fn_cargarSeleccionEstacion(IDPeriodo);
                            $("#SeleccionarEstacion").prop("disabled", false);
                        }
                    } else {
                        if (parametro === 2) {
                            $("#SeleccionarCajero").val("0");
                            $("#SeleccionarCajero").prop("disabled", true);
                        } else {
                            $("#SeleccionarEstacion").val("0");
                            $("#SeleccionarEstacion").prop("disabled", true);
                        }
                        $("#SeleccionarAdmin").val("0");
                        $("#SeleccionarAdmin").prop("disabled", true);
                    }
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

/* CARGA CONTROL ESTACION Y USUARIO SIMPRE Y CUANDO ESTE DESASIGNADO;
 * Esta consulta se utiliza siempre y  cuando la consuta seleccionada sea: 2 = DESMONTADO */
function fn_cargarSeleccionCajero(IDPeriodo) {
    var send;
    var Accion = "U";
    var html = "";
    var IDRestaurante = $("#hdn_IDRestaurante").val();
    send = { "cargarSeleccionCajero": 1 };
    send.accion = Accion;
    send.IDRestaurante = IDRestaurante;
    send.IDPeriodo = IDPeriodo;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminSoporte/config_ConsultaSoporte.php",
        data: send,
        success: function (datos) {
            if (datos.str === 0) {
                alertify.error("No existen cajeros desmontados.");
            } else if (datos.str > 0) {
                $("#SeleccionarCajero").html("");
                $("#SeleccionarCajero").html("<option selected value='0'>-------- Seleccione Cajero/a --------</option>");
                for (var i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['IDControlEstacion'] + "' name='" + datos[i]['IDUsersPos'] + "'>" + datos[i]['Datos'] + "</option>";
                    $("#SeleccionarCajero").append(html);
                }
                $("#SeleccionarCajero").change(function () {
                    var IDControlEstacion = $("#SeleccionarCajero").val();
                    if (IDControlEstacion !== "0") {
                        var IDUserPosCajero = $("#SeleccionarCajero option:selected").attr("name");
                        fn_cargarSeleccionAdmin();
                        $("#hdn_IDControlEstacion").val(IDControlEstacion);
                        $("#hdn_IDUserPosCajero").val(IDUserPosCajero);
                        $("#SeleccionarCajero").prop("disabled", false);
                        $("#SeleccionarAdmin").prop("disabled", false);
                    } else {
                        $("#SeleccionarAdmin").val("0");
                        $("#SeleccionarAdmin").prop("disabled", true);
                    }
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

/* CARGA NOMBRE DE ESTACION EN LA MODAL PARA SU SELECCION;
 * Esta consulta se utiliza siempre y  cuando la consuta seleccionada sea: 3 = FIN DE DIA */
function fn_cargarSeleccionEstacion(IDPeriodo) {
    var send;
    var Accion = "E";
    var html = "";
    var IDRestaurante = $("#hdn_IDRestaurante").val();
    send = { "cargarSeleccionEstacion": 1 };
    send.accion = Accion;
    send.IDRestaurante = IDRestaurante;
    send.IDPeriodo = IDPeriodo;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminSoporte/config_ConsultaSoporte.php",
        data: send,
        success: function (datos) {
            if (datos.str === 0) {
                alertify.error("No existe estacion.");
            } else if (datos.str > 0) {
                $("#SeleccionarEstacion").html("");
                $("#SeleccionarEstacion").html("<option selected value='0'>-------- Seleccione Administrador --------</option>");
                for (var i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['IDEstacion'] + "'>" + datos[i]['Impresora'] + "</option>";
                    $("#SeleccionarEstacion").append(html);
                }
                $("#SeleccionarEstacion").change(function () {
                    var IDEstacion = $("#SeleccionarEstacion").val();
                    if (IDEstacion !== "0") {
                        fn_cargarSeleccionAdmin();
                        $("#hdn_IDEstacion").val(IDEstacion);
                        $("#SeleccionarEstacion").prop("disabled", false);
                        $("#SeleccionarAdmin").prop("disabled", false);
                    } else {
                        $("#SeleccionarAdmin").val("0");
                        $("#SeleccionarAdmin").prop("disabled", true);
                    }
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

/* CARGA USUARIO ADMINISTRADOR EN LA MODAL:
 * Esta consulta se utiliza siempre y  cuando la consuta seleccionada sea: 2 = DESMONTADO ó 3 = FIN DE DIA */
function fn_cargarSeleccionAdmin() {
    var send;
    var Accion = "A";
    var html = "";
    send = { "cargarSeleccionAdmin": 1 };
    send.accion = Accion;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminSoporte/config_ConsultaSoporte.php",
        data: send,
        success: function (datos) {
            if (datos.str === 0) {
                alertify.alert("No existe usuario administrador.");
            } else if (datos.str > 0) {
                $("#SeleccionarAdmin").html("");
                $("#SeleccionarAdmin").html("<option selected value='0'>-------- Seleccione Administrador --------</option>");
                for (var i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['IDUsersPos'] + "'>" + datos[i]['Administrador'] + "</option>";
                    $("#SeleccionarAdmin").append(html);
                }
                $("#SeleccionarAdmin").change(function () {
                    var IDUserPosAdmin = $("#SeleccionarAdmin").val();
                    $("#hdn_IDUserPosAdmin").val(IDUserPosAdmin);
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

/* CREAR UN RESGISTR PARA LA IMPRESION DE LOS REPORTES DESASIGNADO O FIN DE DIA 
 * parametro: se refiere al tipo de reporte, 2 = DESAGINDADO, 3 = FIN DE DIA 
 * tipo = se refiere al tipo de accion que se ejecuta en la base, 1 = I Insert , 0 = U Update */
function fn_crearReporte(parametro, tipo) {
    var send;
    var Accion = "R";
    var tipoAccion;
    var IDControlEstacion;
    var reporte;
    var IDUsuarioCajero = $("#hdn_IDUserPosCajero").val();
    var IDUsuarioAdmin = $("#hdn_IDUserPosAdmin").val();
    var IDEstacion;
    var IDPeriodo;
    if (parametro === 2) {
        reporte = "D";
        IDControlEstacion = $("#hdn_IDControlEstacion").val();
        IDEstacion = "0";
        if ($("#SeleccionarCajero option:selected").val() === "0") {
            alertify.error("<b>Alerta</b> Seleccione un cajero.");
            return false;
        }
    } else if (parametro === 3) {
        reporte = "F";
        IDControlEstacion = $("#hdn_IDPeriodo").val();
        IDPeriodo = $("#hdn_IDPeriodo").val();
        IDEstacion = $("#hdn_IDEstacion").val();
        if ($("#SeleccionarEstacion option:selected").val() === "0") {
            alertify.error("<b>Alerta</b> Seleccione un cajero.");
            return false;
        }
    }
    if (tipo === 1) { tipoAccion = "I"; }
    if ($("#SeleccionarPeriodo option:selected").val() === "0") {
        alertify.error("<b>Alerta</b> Seleccione un per&iacute;odo.");
        return false;
    } else if ($("#SeleccionarAdmin option:selected").val() === "0") {
        alertify.error("<b>Alerta</b> Seleccione un administrador.");
        return false;
    }

    send = { "crearReporte": 1 };
    send.accion = Accion;
    send.tipo = tipoAccion;
    send.reporte = reporte;
    send.IDControlEstacion = IDControlEstacion;
    send.IDUsuarioCajero = IDUsuarioCajero;
    send.IDUsuarioAdmin = IDUsuarioAdmin;
    send.IDEstacion = IDEstacion;

    var sendInfoEstacion;
    sendInfoEstacion = { "infoAplicaApiImpresionCrearReporte": 1 };
    sendInfoEstacion.IDParametro = parametro == 2 ? IDControlEstacion : IDEstacion;
    sendInfoEstacion.Accion = parametro == 2 ? 1 : 2;
    $.ajax({
        async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminSoporte/config_ConsultaSoporte.php", data: sendInfoEstacion, success: function (datos) {

            if (datos.aplicaTienda == 1) {
                datos.idUsuarioCajero = IDUsuarioCajero;
                datos.idUsuarioAdmin = IDUsuarioAdmin;
                datos.IDControlEstacion = IDControlEstacion;
                datos.IPEstacion = datos.estacion;
                datos.IDPeriodo = IDPeriodo;
                var crearReporte = parametro == 2 ? 'creacionReporteDesmontadoCajero' : 'creacionReporteFinDelDia';
                console.log(crearReporte)
                var result = new apiServicioImpresionMantenimiento(crearReporte, null, null, datos);
                var imprime = result["imprime"];
                var mensaje = result["mensaje"];

                if (!imprime) {
                    alertify.success("imprimiendo reporte....");
                    $("#modalNuevoRegistro").modal("hide");
                    if (parametro === 2) {
                        fn_cargaDetalleDesmontado(parametro);
                    } else {
                        fn_cargaDetalleFindeDia(parametro);
                    }
                    fn_quitarSeleccion(parametro);
                } else {
                    alertify.alert(mensaje);

                    if (accion == 2) {
                        alertify.success('Error al imprimir Desmontado de cajero...');
                    }
                    else if (accion == 3) {
                        alertify.success('Error al imprimir Fín del día...');
                    }
                }
            }
            else {
                $.ajax({
                    async: false,
                    type: "POST",
                    dataType: "json",
                    contentType: "application/x-www-form-urlencoded",
                    url: "../adminSoporte/config_ConsultaSoporte.php",
                    data: send,
                    success: function (datos) {
                        alertify.success("imprimiendo reporte....");
                        $("#modalNuevoRegistro").modal("hide");
                        if (parametro === 2) {
                            fn_cargaDetalleDesmontado(parametro);
                        } else {
                            fn_cargaDetalleFindeDia(parametro);
                        }
                        fn_quitarSeleccion(parametro);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert(jqXHR);
                        alert(textStatus);
                        alert(errorThrown);
                        alertify.error("Error al momento de guardar.");
                    }
                });
            }
        }
    });
}



// ////////////////////////////////
/* CARGA LAS FACTURAS
 * Esta consulta se ejecuta cuando la opcion seleccionada es; 4 = FACTURAS */
function fn_cargaFacturas() {
    var send;
    var Accion = "1";
    var html = '<thead><tr class="active"><th class="text-center">Código</th><th class="text-center">Número</th><th class="text-center">Valor</th><th class="text-center">Cliente</th><th class="text-center">Fecha</th><th class="text-center">Estación</th><th class="text-center">Medio</th><th class="text-center">Reimpreso?</th></tr></thead>';
    send = { "cargarFacturas": 1 };
    send.accion = Accion;

    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminSoporte/config_ConsultaSoporte.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    //var estadoFindeDia = (datos[i]["Estado"]);
                    html += '<tr id="' + i + "idcanalmovimiento" + '" onclick="fn_seleccionarDocumentos(1, ' + i + ', \'' + datos[i]["idcanalmovimiento"] + '\', \'' + datos[i]["factura"] + '\', \'' + datos[i]["imprimeMedio"] + '\', \'' + datos[i]["reimpresion"] + '\')" class="text-center"><td>' + datos[i]["factura"] + '</td><td>' + datos[i]["numeroFactura"] + '</td><td>' + datos[i]["total"] + '</td><td>' + datos[i]["cliente"] + '</td><td>' + datos[i]["fecha"] + '</td><td>' + datos[i]["estacion"] + '</td><td>' + datos[i]["medio"] + '</td><td>' + datos[i]["reimpresion"] + '</td>';

                    html += '</tr>';
                }
                $("#TblDetalle_ConsultaReimpresionFacturas").html(html);
                $("#TblDetalle_ConsultaReimpresionFacturas").dataTable({ "destroy": true });
                $("#TblDetalle_ConsultaReimpresionFacturas_length").html('<button type="button" class="btn btn-primary" onclick="fn_validaReimpresion(1);"><span class="glyphicon glyphicon-print"></span> Reimprimir </button>');
                $("#TblDetalle_ConsultaReimpresionFacturas_paginate").addClass("col-xs-10");
                $("#TblDetalle_ConsultaReimpresionFacturas_info").addClass("col-xs-10");
                $("#TblDetalle_ConsultaReimpresionFacturas_length").addClass("col-xs-6");
            } else {
                html = html + '<tr><th colspan="4" class="text-center">No existen registros.</th></tr>';
                $("#TblDetalle_ConsultaReimpresionFacturas").html(html);
                $("#TblDetalle_ConsultaReimpresionFacturas_length").html('<button type="button" class="btn btn-primary disabled">Imprimir Reporte</button> <button type="button" class="btn btn-success disabled" >Ingresar Reporte</button>');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}



/* CARGA LAS NOTAS DE CRÉDITO
 * Esta consulta se ejecuta cuando la opcion seleccionada es; 5 = NOTAS DE CRÉDITO */
function fn_cargaNotasCredito() {
    var send;
    var Accion = "2";
    var html = '<thead><tr class="active"><th class="text-center">Factura</th><th class="text-center">Código</th><th class="text-center">Número</th><th class="text-center">Valor</th><th class="text-center">Cliente</th><th class="text-center">Fecha</th><th class="text-center">Estación</th><th class="text-center">Medio</th><th class="text-center">Reimpreso?</th></tr></thead>';
    send = { "cargarNotasCredito": 1 };
    send.accion = Accion;

    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminSoporte/config_ConsultaSoporte.php",
        data: send,
        success: function (datos) {
            console.log(datos);
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += '<tr id="' + i + "idcanalmovimiento" + '" onclick="fn_seleccionarDocumentos(2, ' + i + ', \'' + datos[i]["idcanalmovimiento"] + '\', \'' + datos[i]["factura"] + '\', \'' + datos[i]["imprimeMedio"] + '\', \'' + datos[i]["reimpresion"] + '\')" class="text-center"><td>' + datos[i]["cfac_id"] + '</td><td>' + datos[i]["factura"] + '</td><td>' + datos[i]["numeroFactura"] + '</td><td>' + datos[i]["total"] + '</td><td>' + datos[i]["cliente"] + '</td><td>' + datos[i]["fecha"] + '</td><td>' + datos[i]["estacion"] + '</td><td>' + datos[i]["medio"] + '</td><td>' + datos[i]["reimpresion"] + '</td>';
                    html += '</tr>';
                }
                $("#TblDetalle_ConsultaReimpresionNotasCredito").html(html);
                $("#TblDetalle_ConsultaReimpresionNotasCredito").dataTable({ "destroy": true });
                $("#TblDetalle_ConsultaReimpresionNotasCredito_length").html('<button type="button" class="btn btn-primary" onclick="fn_validaReimpresion(2);"><span class="glyphicon glyphicon-print"></span> Reimprimir</button>');
                $("#TblDetalle_ConsultaReimpresionNotasCredito_paginate").addClass("col-xs-10");
                $("#TblDetalle_ConsultaReimpresionNotasCredito_info").addClass("col-xs-10");
                $("#TblDetalle_ConsultaReimpresionNotasCredito_length").addClass("col-xs-6");
            } else {
                html = html + '<tr><th colspan="4" class="text-center">No existen registros.</th></tr>';
                $("#TblDetalle_ConsultaReimpresionNotasCredito").html(html);
                $("#TblDetalle_ConsultaReimpresionNotasCredito_length").html('<button type="button" class="btn btn-primary disabled">Imprimir Reporte</button> <button type="button" class="btn btn-success disabled" >Ingresar Reporte</button>');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}



/* CARGA LAS ORDENES DE PEDIDO
 * Esta consulta se ejecuta cuando la opcion seleccionada es; 6 = Ordenes de pedido */

function fn_cargaOrdenesPedido() {
    console.log('holiii');
    let send;
    let accion = 3;

    let html = '<thead><tr class="active"><th class="text-center">Código</th><th class="text-center">Valor</th><th class="text-center">Cliente</th><th class="text-center">Fecha</th><th class="text-center">Medio</th><th class="text-center">Reimpreso?</th><th class="text-center">Numero orden?</th></tr></thead>';
    send = { "cargarOrdenespedido": 1, accion };
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminSoporte/config_ConsultaSoporte.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {

                for (var i = 0; i < datos.str; i++) {
                    html += '<tr id="' + i + "idcanalmovimiento" + '" onclick="fn_seleccionarDocumentos(3, ' + i + ', \'' + datos[i]["idcanalmovimiento"] + '\', \'' + datos[i]["numerofactura"] + '\', \'' + datos[i]["imprimeMedio"] + '\', \'' + datos[i]["reimpresion"] + '\')" class="text-center"><td>' + datos[i]["numerofactura"] + '</td><td>' + datos[i]["total"] + '</td><td>' + datos[i]["cliente"] + '</td><td>' + datos[i]["fecha"] + '</td><td>' + datos[i]["medio"] + '</td><td>' + datos[i]["reimpresion"] + '</td><td>' + datos[i]["numeroOrden"] + '</td>';
                    html += '</tr>';
                }
                $("#TblDetalle_ConsultaReimpresionOrdenPedido").html(html);
                $("#TblDetalle_ConsultaReimpresionOrdenPedido").dataTable({ "destroy": true });
                $("#TblDetalle_ConsultaReimpresionOrdenPedido_length").html('<button type="button" class="btn btn-primary" onclick="fn_validaReimpresion(3);"><span class="glyphicon glyphicon-print"></span> Reimprimir</button>');
                $("#TblDetalle_ConsultaReimpresionOrdenPedido_paginate").addClass("col-xs-10");
                $("#TblDetalle_ConsultaReimpresionOrdenPedido_info").addClass("col-xs-10");
                $("#TblDetalle_ConsultaReimpresionOrdenPedido_length").addClass("col-xs-6");
            } else {
                html = html + '<tr><th colspan="4" class="text-center">No existen registros.</th></tr>';
                $("#TblDetalle_ConsultaReimpresionOrdenPedido").html(html);
                $("#TblDetalle_ConsultaReimpresionOrdenPedido_length").html('<button type="button" class="btn btn-primary disabled">Imprimir Reporte</button> <button type="button" class="btn btn-success disabled" >Ingresar Reporte</button>');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // alert(jqXHR);
            // alert(textStatus);
            // alert(errorThrown);
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        }
    });

}



function fn_seleccionarDocumentos(accion, id, idcanalmovimiento, transaccion, medio, reimpresion) {

    $("#hdn_IDCanalmovimiento").val(idcanalmovimiento);
    $("#hdn_IDTransaccion").val(transaccion);
    $("#hdn_imprimeMedio").val(medio);
    $("#hdn_reimpresion").val(reimpresion);

    // facturas
    if (accion == 1) {

        $("#TblDetalle_ConsultaReimpresionFacturas tr").removeClass("success");
        $("#" + id + 'idcanalmovimiento' + "").addClass("success");



    } else if (accion == 2) {

        $("#TblDetalle_ConsultaReimpresionNotasCredito tr").removeClass("success");
        $("#" + id + 'idcanalmovimiento' + "").addClass("success");

    } else {

        $("#TblDetalle_ConsultaReimpresionOrdenPedido tr").removeClass("success");
        $("#" + id + 'idcanalmovimiento' + "").addClass("success");

    }


}




function fn_validaReimpresion(accion) {

    var IDtablaFacturas = $("#TblDetalle_ConsultaReimpresionFacturas").find("tr.success").attr("id");
    var IDtablaNC = $("#TblDetalle_ConsultaReimpresionNotasCredito").find("tr.success").attr("id");
    var IDtablaOP = $("#TblDetalle_ConsultaReimpresionOrdenPedido").find("tr.success").attr("id");
    //var idtransaccion = $("#hdn_IDTransaccion").val();
    let canalmov = $("#hdn_IDCanalmovimiento").val();
    let transaccion = $("#hdn_IDTransaccion").val();
    //K061F000972166
    //K061N000972166
    let imprimeMedio = $("#hdn_imprimeMedio").val();
    //SI - NO
    let reimpresion = $("#hdn_reimpresion").val(); //SI NO


    if (IDtablaFacturas) {



        // reimpresion factura
        if (accion == 1) {
            if (imprimeMedio == 'NO') {
                alertify.error("Esta transacción NO puede ser re-impresa");
            } else {
                if (reimpresion == 'NO') {
                    //poner sp de actualizar
                    fn_reImprimir(1, canalmov, transaccion);
                    fn_cargaFacturas();
                } else {
                    alertify.error("Transacción ya ha sido RE-IMPRESA");
                }

            }

        }
    } else if (IDtablaNC) {
        console.log('NOTAS CREDITO');
        if (accion == 2) {

            if (imprimeMedio == 'NO') {
                alertify.error("Esta transacción NO puede ser re-impresa");
            } else {
                if (reimpresion == 'NO') {
                    //poner sp de actualizar
                    fn_reImprimir(2, canalmov, transaccion);
                    fn_cargaNotasCredito();
                } else {
                    alertify.error("Transacción ya ha sido RE-IMPRESA");
                }

            }
        }
    } else if (IDtablaOP) {
        console.log('ORDENES PEDIDO');
        if (accion == 3) {

            if (imprimeMedio == 'NO') {
                alertify.error("Esta transacción NO puede ser re-impresa");
            } else {
                if (reimpresion == 'NO') {
                    //poner sp de actualizar
                    fn_reImprimir(3, canalmov, transaccion);
                    fn_cargaOrdenesPedido();
                } else {
                    alertify.error("Transacción ya ha sido RE-IMPRESA");

                }

            }
        }
    } else {
        alertify.error("Debe seleccionar un registro para la reimpresion del reporte.");
    }



}

function fn_reImprimir(accion, idcanalmovimiento, transaccion) {

    let send = { 'reimprimirDocumentos': 1 };
    send.accion = accion;
    send.idcanalmovimiento = idcanalmovimiento;
    send.transaccion = transaccion;

    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminSoporte/config_ConsultaSoporte.php",
        data: send,
        success: function (datos) {
            alertify.success("El documento ha sido REIMPRESO");

            /*if (accion == 1) {
                fn_cargaFacturas();
            }*/

            //fn_quitarSeleccionDocs(parametro);


        }
        //fn_quitarSeleccion(parametro);
    });



}

/* FUNCION QUE QUITA LA SELECCION DE UN REGISTRO: 
 * parametro: se refiere al tipo de consulta, 1=PERIODO, 2=DESASIGNADO, 3=FIN DE DIA */
function fn_quitarSeleccionDocs(parametro) {
    if (parametro === 1) {
        $("#TblDetalle_ConsultaReimpresionFacturas tr").removeClass("success");
        fn_cargaFacturas();
    } else if (parametro === 2) {
        $("#TblDetalle_ConsultaReimpresionNotasCredito tr").removeClass("success");
    } else if (parametro === 3) {
        $("#TblDetalle_ConsultaReimpresionOrdenPedido tr").removeClass("success");
    }
}