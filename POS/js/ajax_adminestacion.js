//////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////
///////////DESCRIPCION: NUEVOS ESTILOS INGRESO Y ACTUALIZACION DE ESTACION CON////
/////////////////////// TABLA MODAL //////////////////////////////////////////////
////////////////TABLAS: Estacion,SWT_Tipo_Envio///////////////////////////////////
////////FECHA CREACION: 01/06/2015////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////

var lc_ip = -1; //ip
var lc_control = -1;//para saber si hay q grabar o actualizar
var accion = 0;
var arrayMenu = [];
var lc_idColeccionEstacionM = '';
var lc_nombreColeccionM = '';
var lc_idEstacionNueva = '';
var lc_idColeccionCadena = '';
var lc_nombreColeccion = '';
var lc_coleccionDatosCadena = '';
var lc_nombreColeccionDatos = '';

var lc_especifica = -1;
var lc_obligatorio = -1;
var lc_tipoDato = '';
var checked = 0;

var send = {};
var html;

$(document).ready(function () {
    $("#tabla_estacion").hide();
    $("#nuevo").hide();
    $("#modificado").hide();
    $("#botonesActivosInactivos").hide();
    fn_cargarRestaurante();
    fn_btn('cancelar', 1);
    fn_btn('agregar', 1);
    fn_cargarPagoPredeterminado();
    $("#sel_seleccione").bootstrapSwitch('state', false);
    $("#sel_seleccioneM").bootstrapSwitch('state', false);
    fn_cargaMedioAutorizador();
});


function fn_OpcionSeleccionada(ls_opcion) {
    if (ls_opcion == 'Todos') {
        lc_rest = $("#descripcionRestaurante").val();
        fn_cargarDetalle(lc_rest);
    } else if (ls_opcion == 'Activos') {
        accion = 10;
        lc_rest = $("#descripcionRestaurante").val();
        fn_cargarDetalleInactivos(lc_rest, accion);
    } else if (ls_opcion == 'Inactivos') {
        accion = 11;
        lc_rest = $("#descripcionRestaurante").val();
        fn_cargarDetalleInactivos(lc_rest, accion);
    }
}

function fn_cargarPagoPredeterminado() {
    send = {"cargaPagoPredeterminado": 1};
    send.accion = 1;
    send.idEstacionPagoPredeterminado = '0';
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#selPagoPredeterminado").empty();
                $("#selPagoPredeterminadoM").empty();
                for (var i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['idIntegracion'] + "'>" + datos[i]['fmp_descripcion'] + "</option>";
                    $("#selPagoPredeterminado").append(html);
                    $("#selPagoPredeterminadoM").append(html);
                }
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

function fn_cargaMedioAutorizador() {
    
 
    send = {"cargaMedioAutorizador": 1};
    send.accion = 1;
    send.idEstacionPagoPredeterminado = '0';
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#selTipoCobro").empty();
                $("#selTipoCobroMod").empty();
                for (var i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['idIntegracion'] + "'>" + datos[i]['Descripcion'] + "</option>";
                    $("#selTipoCobro").append(html);
                    $("#selTipoCobroMod").append(html);
                }
                $('#selTipoCobro').chosen();
                $('#selTipoCobroMod').chosen();
                $('#selTipoCobro_chosen').css('width', '500');
                $('#selTipoCobroMod_chosen').css('width', '500');
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

function fn_OpcionSeleccionadaModInsert() {
    var ls_opcion = '';
    ls_opcion = $(":input[name=estados]:checked").val();

    if (ls_opcion == 'Todos') {
        lc_rest = $("#descripcionRestaurante").val();
        fn_cargarDetalle(lc_rest);
    } else if (ls_opcion == 'Activos') {
        accion = 10;
        lc_rest = $("#descripcionRestaurante").val();
        fn_cargarDetalleInactivos(lc_rest, accion);
    } else if (ls_opcion == 'Inactivos') {
        accion = 11;
        lc_rest = $("#descripcionRestaurante").val();
        fn_cargarDetalleInactivos(lc_rest, accion);
    }
}

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

function fn_cargarRestaurante() {
    send = {"cargarrestaurante": 1};
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str == 0) {
                $("#divrestaurante").hide();
                alertify.alert("No existen datos para esta Cadena");
            } else if (datos.str > 0) {
                $("#divrestaurante").show();
                $("#selrest").html("");
                $('#selrest').html("<option selected value='0'>--------------Seleccione Restaurante--------------</option>");
                for (var i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['rst_id'] + "' data-fastFood='" + datos[i]['FastFood'] + "'>" + datos[i]['Descripcion'] + "</option>";
                    $("#selrest").append(html);
                }
                $("#selrest").chosen();

                $("#selrest").change(function () {
                    lc_rest = $("#selrest").val();
                    $("#descripcionRestaurante").val(lc_rest);
                    //fn_cargarDetalle(lc_rest);
                    fn_cargarDetalleInactivos(lc_rest, 10);
                    $("#detalle_estacion").show();
                    $("#botonesActivosInactivos").show();
                });
            }
        }
    });
}

function fn_menu(lc_accion) {
    $("#respuestaMenu").load("menu_configuracionEstacion.php?accion=" + lc_accion + "");
}

function fn_cargarRestauranteNuevo(lc_cadena) {
    send = {"cargarrestaurante": 1};
    send.codcadena = lc_cadena;//$("#selcadena").val();
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#selrestNuevo").html("");
                $('#selrestNuevo').html("<option selected value='0'>----Seleccione Restaurante----</option>");
                for (var i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['rst_id'] + "'>" + datos[i]['Descripcion'] + "</option>";
                    $("#selrestNuevo").append(html);
                }
            }
        }
    });
}

function fn_cerrarModalColeccion(id) {
    $("#ModalModificar").show();
}

function fn_cargaColeccionDatosTabla(bandera) {
    //html = '<tr class="bg-primary"><th>Descripción</th><th class="text-center">Dato</th><th>Especifica Valor</th><th>Obligatorio</th><th class="text-center">Tipo de Dato</th><th class="text-center">Varchar</th><th class="text-center">Entero</th><th class="text-center">Fecha</th><th class="text-center">Numerico</th><th class="text-center">Fecha Inicio</th><th class="text-center">Fecha Fin</th><th class="text-center">Minimo</th><th class="text-center">Maximo</th><th>Activo</th></tr>';
    send = {"cargaColeccionDatosTabla": 1};
    send.accion = 1;///significa que encio para cargar los datos de coleccion cuando es nuevo en la opcion de modificar estacion
    send.idColeccionTabla = lc_idColeccionEstacionM;
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += '<tr onclick="fn_seleccionColeccion(' + i + ',\'' + datos[i]['descripcion_coleccion'] + '\',\'' + datos[i]['ID_ColeccionEstacion'] + '\')" id="' + i + '" class="text-center"><td>' + datos[i]['descripcion_coleccion'] + '</td><td>' + datos[i]['descripcion_dato'] + '</td>';
                    if (datos[i]['especificarValor'] === 1)
                    {
                        html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } else {
                        html += '<td><input type="checkbox" value="1" disabled/></td>';
                    }
                    if (datos[i]['obligatorio'] === 1) {
                        html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } else {
                        html += '<td><input type="checkbox" value="1" disabled/></td>';
                    }
                    html += '<td>' + datos[i]['tipodedato'] + '</td><td>' + datos[i]['caracter'] + '</td><td>' + datos[i]['entero'] + '</td><td>' + datos[i]['fecha'] + '</td><td>' + datos[i]['numerico'] + '</td><td>' + datos[i]['fechaIni'] + '</td><td>' + datos[i]['fechaFin'] + '</td><td>' + datos[i]['min'] + '</td><td>' + datos[i]['max'] + '</td>';
                    if (datos[i]['isActive'] === 1) {
                        html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } else {
                        html += '<td><input type="checkbox" value="1" disabled/></td>';
                    }
                    html += '</tr>';
                }
                $("#tbl_estacion_coleccion").html(html);

            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
    $("#lblNombreColeccion").text(lc_nombreColeccionM);
    $("#ModalModificar").hide();
    $("#mdl_nuevaColeccion").modal('show');
}

function fn_accionarPoliticas(accion, bandera) {
    //("#ModalModificar").modal('hide');
    if (accion == 'Nuevo') {
        //if(lc_idColeccionCadena=='')
        //{alertify.error('Seleccione una coleccion..'); return false;}
        fn_cargaColeccionDatosTabla('M');
    } else if (accion == 'Modificar') {
        if (lc_idColeccionCadena == '') {
            alertify.error('Seleccione una coleccion..');
            return false;
        }
        fn_modificarColeccion();
    }
}

function fn_modificarColeccion() {
    send = {"editarColeccionDeDatos": 1};
    send.accion = 4;
    send.idColeccioncadenaM = lc_idColeccionCadena;
    send.idDatosColeccionM = lc_coleccionDatosCadena;
    send.lc_idacesta = lc_estacion;
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                if (datos.especificarValor == 1) {
                    $("#check_especificaM").prop('checked', true);
                } else {
                    $("#check_especificaM").prop('checked', false);
                }
                if (datos.obligatorio == 1) {
                    $("#check_obligatorioM").prop('checked', true);
                } else {
                    $("#check_obligatorioM").prop('checked', false);
                }
                $("#lbl_tipoDatoM").text(datos.tipodedato);
                $('#txt_fechaSImpleM').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {});
                $('#txt_fechaInicioM').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {});
                $('#txt_fechaFinM').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {});
                $("#txt_caracterM").val(datos.caracter);
                $("#txt_enteroM").val(datos.entero);
                $("#txt_fechaSImpleM").val(datos.fecha);
                if (datos.seleccion == 1) {
                    $("#sel_seleccioneM").bootstrapSwitch('state', true);
                } else {
                    $("#sel_seleccioneM").bootstrapSwitch('state', false);
                }
                $("#txt_numericoM").val(datos.numerico);
                $("#txt_fechaInicioM").val(datos.fechaInicio);
                $("#txt_fechaFinM").val(datos.fechaFin);
                $("#txt_minimoM").val(datos.minimo);
                $("#txt_maximoM").val(datos.maximo);
                if (datos.activo == 1) {
                    $("#check_activo").prop('checked', true);
                } else {
                    $("#check_activo").prop('checked', false);
                }
                //$("#txt_carM").val(datos.activo);

                $("#lblNombreColeccionModificar").text(lc_nombreColeccionDatos);
                $("#mdl_editaColeccion").modal('show');
            } else
            {
                alertify.error('No existen datos.');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.alert(jqXHR);
            alertify.alert(textStatus);
            alertify.alert(errorThrown);
        }
    });
}

function fn_cargaColeccionDatosTabla(bandera) {
    html = '<tr class="bg-primary"><th>Descripción</th></tr>';
    send = {"cargaColeccionDeDatos": 1};
    send.accion = 2;
    send.idColeccioncadena = '0';
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += '<tr id="' + i + '_c" onclick="fn_seleccionColeccionEstacion(' + i + ',\'' + datos[i]['ID_ColeccionEstacion'] + '\')">';
                    html += '<td>' + datos[i]['Descripcion'] + '</td>';
                    html += '</tr>';
                }
                $("#listaColecciones").html(html);

                //$("#lblNombreColeccion").text(lc_nombreColeccion);		    
                $("#div_caracteristicas").hide();
                $("#mdl_nuevaColeccion").modal('show');
            } else {
                alertify.error('No existen colecciones que agregar.');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_seleccionColeccionEstacion(indice3, idColeccionEstacion) {
    $("#listaColecciones tr").removeClass("success");
    $("#" + indice3 + "_c").addClass("success");
    lc_idColeccionCadena = idColeccionEstacion;
    fn_cargaColeccionDatosCadena();
    $("#div_caracteristicas").hide();
}

function fn_cargaColeccionDatosCadena() {
    html = '<tr class="bg-primary"><th>Descripción</th><th>Datos</th></tr>';
    send = {"cargaColeccionDatosC": 1};
    send.accion = 3;
    send.idColeccionCadenaa = lc_idColeccionCadena;
    send.idestac = lc_estacion;
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#_detalle_restaurante_coleccion").show();
                for (var i = 0; i < datos.str; i++) {
                    html += '<tr id="' + i + '_cd" onclick="fn_seleccionColeccionDeDatosCadena(' + i + ',\'' + datos[i]['ID_ColeccionEstacion'] + '\',\'' + datos[i]['ID_ColeccionDeDatosEstacion'] + '\',' + datos[i]['especificarValor'] + ',' + datos[i]['obligatorio'] + ',\'' + datos[i]['tipodedato'] + '\')">';
                    html += '<td>' + datos[i]['Descripcion'] + '</td><td>' + datos[i]['dato'] + '</td>';
                    html += '</tr>';
                }
                $("#lista_datos").html(html);
            } else {
                html += '<tr><td colspan=2>No existen registros.</td></tr>';
                $("#lista_datos").html(html);
                //alertify.error('No existen datos.');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_seleccionColeccionDeDatosCadena(indice2, idColeccion, idColeccionDatos, especifica, obligatorio, tipoDato) {
    $("#lista_datos tr").removeClass("success");
    $("#" + indice2 + "_cd").addClass("success");
    lc_coleccionDatosCadena = idColeccionDatos;
    lc_especifica = especifica;
    lc_obligatorio = obligatorio;
    lc_tipoDato = tipoDato;
    fn_cargaCaracteristicas();
}

function fn_cargaCaracteristicas() {
    if (lc_especifica == 1) {
        $("#check_especifica").prop('checked', true);
    } else {
        $("#check_especifica").prop('checked', false);
    }
    if (lc_obligatorio == 1) {
        $("#check_obligatorio").prop('checked', true);
    } else {
        $("#check_obligatorio").prop('checked', false);
    }
    $("#lbl_tipoDato").text(lc_tipoDato);

    $('#txt_fechaSImple').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {});
    $('#txt_fechaInicio').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {});
    $('#txt_fechaFin').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {});
    $("#div_caracteristicas").show();
}

function fn_guardarColeccion() {
    estado = $("#sel_seleccione").bootstrapSwitch('state');
    if (estado == true) {
        var seleccion = 1;
    } else {
        var seleccion = 0;
    }
    send = {"grabaCadenaColeccionDatos": 1};
    send.accion = 'I';
    send.idColeccionDatoscadena = lc_coleccionDatosCadena;
    send.caracter = $("#txt_caracter").val();
    send.entero = $("#txt_entero").val();
    send.fecha = $("#txt_fechaSImple").val();
    send.numerico = $("#txt_numerico").val();
    send.fechaInicio = $("#txt_fechaInicio").val();
    send.fechaFin = $("#txt_fechaFin").val();
    send.minimo = $("#txt_minimo").val();
    send.maximo = $("#txt_maximo").val();
    send.idColeccionCadena = lc_idColeccionCadena;
    send.idgrabaesta = lc_estacion;
    send.seleccione = seleccion;
    send.isactive = 1;

    alert(send.entero);

    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            $("#ModalModificar").modal('show');
            if (datos.str == 1) {
                $("#mdl_nuevaColeccion").modal('hide');
                fn_configuracionPoliticasModificar(lc_estacion);
                $('.limpiar').val('');
                $("#_detalle_restaurante_coleccion").hide();
                alertify.success('Datos guardados correctamente.');
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

function fn_actualizaColeccion() {
    estado = $("#sel_seleccioneM").bootstrapSwitch('state');
    if (estado == true) {
        var seleccion = 1;
    } else {
        var seleccion = 0;
    }
    if ($("#check_activo").is(':checked')) {
        var activo = 1;
    } else {
        var activo = 0;
    }

    send = {"actualizaCadenaColeccionDatos": 1};
    send.accion = 'A';
    send.idColeccionDatoscadenaM = lc_coleccionDatosCadena;
    send.caracterM = $("#txt_caracterM").val();
    send.enteroM = $("#txt_enteroM").val();
    send.fechaM = $("#txt_fechaSImpleM").val();
    send.numericoM = $("#txt_numericoM").val();
    send.fechaInicioM = $("#txt_fechaInicioM").val();
    send.fechaFinM = $("#txt_fechaFinM").val();
    send.minimoM = $("#txt_minimoM").val();
    send.maximoM = $("#txt_maximoM").val();
    send.idColeccionCadenaM = lc_idColeccionCadena;
    send.idactualizaesta = lc_estacion;
    send.seleccioneM = seleccion;
    send.isactive = activo;
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            $("#ModalModificar").modal('show');
            if (datos.str > 0) {
                $("#mdl_editaColeccion").modal('hide');
                fn_configuracionPoliticasModificar(lc_estacion);
                $('.limpiar').val('');
                alertify.success('Datos actualizados correctamente.');
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

function fn_accionar(accion) {
    if (accion == 'Nuevo') {
        /*MODAL DE COLECCIONES*/
        //if(lc_idColeccionEstacionM=='')
        //{alertify.error('Seleccione una coleccion..'); return false;}
        //fn_cargaColeccionDatosTabla('M');
        /* FIN MODAL DE COLECCIONES*/
        fn_cargarSelectDesasignarEstacion();
        //fn_cargarSelectTipoEnvio();

        $("#ip1").val('');
        $("#ip2").val('');
        $("#ip3").val('');
        $("#ip4").val('');
        $("#txt_tid").val('');
        $("#txt_rstNuevo").val($("#selrest option:selected").text());
        $("#txt_estacion").val('');

        $("#txt_tid").hide();
        if ($('#selrest option:selected').attr('data-fastFood') === '1') {
            $("#divNuevoSeleccionarMesa").show();
            consultaColeccionMesa("Nuevo", "");
        } else {
            $("#divNuevoSeleccionarMesa").hide();
            $("#selNuevoMesa").empty();
        }

        if ($("#selrest").val() == 0) {
            alertify.error("Debe seleccionar un Restaurante");
            $("#selrest").focus();
            return false;
        }

        $("#selTipoCobro").change(function () {
            /*if($("#selTipoCobro").val()==3)
             {
             $("#txt_tid").hide();
             $("#txt_tid").val('');
             }
             else{*/
            $("#txt_tid").show();
            $("#txt_tid").val('');
            //}
        });

        lc_control = 0;
        send = {"cargarNuevo": 1};
        send.descripcionRestaurante = $("#descripcionRestaurante").val();
        $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
            if (datos.str > 0) {
                $("#txt_rstNuevo").val(datos.Restaurante);
                $("#ip1").val(datos.primer_octeto_ip);
                $("#ip2").val(datos.segundo_octeto_ip);
                $("#ip3").val(datos.tercer_octeto_ip);
            }
        });

        send = {"cargarNumeroNombreCaja": 1};
        send.descripcionRestaurante = $("#descripcionRestaurante").val();
        $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
            if (datos.str > 0) {
                if (datos.MaximaCaja > 9) {
                    $("#txt_estacion").val(datos.MaximaCaja);
                } else {
                    $("#txt_estacion").val('0' + datos.MaximaCaja);
                }
                numeroestacionnuevo = $("#txt_estacion").val();
                $('#titulomodalNuevo').html('');
                $('#titulomodalNuevo').html('Ingreso de Nueva Estación:');
                $('#titulomodalNuevo').append(" " + numeroestacionnuevo + " ");
            }
        });

        send = {"cargarmenu": 1};
        send.estacionid='0';
        $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
            
           
            if (datos.str > 0) {
                //$('#selmenu').html('');	
                $('#selmenu').val('').trigger("chosen:updated.chosen");
                html_menu = '';
                for (var i = 0; i < datos.str; i++) {
                    html_menu = html_menu + "<option value='" + datos[i]['menu_id'] + "'>" + datos[i]['menu_nombre'] + "</option>";
                    //$("#selcadena").append("<option selected value="+datos[i]['cdn_id']+">"+datos[i]['cdn_descripcion']+"</option>");
                }
                $("#selmenu").html(html_menu);

                $('#selmenu').chosen();
                $("#selmenu_chosen").css('width', '490');
                //alert($('#selmenu').val());        
                $("#selmenu").change(function () {
                    lc_cadena = $("#selmenu").val();
                    $("#descripcionMenu").val(lc_cadena);
                });
                
            }
        });

        $("#selTipoCobro").val(0);
        $('#selTipoCobro').trigger("chosen:updated");
        /*send = {"cargarTipoCobro": 1};
         $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
         $('#selTipoCobro').empty();
         $('#selTipoCobro').append("<option selected value='0'>----Seleccione Tipo Cobro----</option>");
         for (var i = 0; i < datos.str; i++)
         {
         html = "<option value='" + datos[i]['tpenv_id'] + "'>" + datos[i]['tpenv_descripcion'] + "</option>";
         $("#selTipoCobro").append(html);
         //$("#selcadena").append("<option selected value="+datos[i]['cdn_id']+">"+datos[i]['cdn_descripcion']+"</option>");
         }
         $("#selTipoCobro").change(function () {
         //lc_cadena=$("#selmenu").val();
         lc_idtipoenvio = $("#selTipoCobro").val();
         $("#idTipoEnvio").val(lc_idtipoenvio);
         });
         
         });*/

        fn_traerCanalesImpresion();
        //$('#home').show();
        $('#fin').hide();

        /*if( $('#fin').is(":visible") ){
         //alert('Elemento visible');
         }else{
         //alert('Elemento oculto');
         $('#settings').hide();
         }*/

        //

        $('#botonesguardarcancelar').show();
        $('#botonessalir').hide();
        ////////////Colocar foco en cualquier campo de la modal////////////////// CP
        $('#ModalNuevo').on('shown.bs.modal', function () {
            $("#ip4").focus();
        });

        $('#ModalNuevo').modal('show');

        //$("#botonesguardarcancelar").show();
        //$("#botonessalir").hide();
    } else if (accion == 'Grabar') {       
        if (lc_control == 3) 
        {
            alert("!!!");
            $("#txt_puntoEmision").val('');
            ipp1 = $("#ip1").val();
            ipp2 = $("#ip2").val();
            ipp3 = $("#ip3").val();
            ipp4 = $("#ip4").val();

            if ($("#ip4").val() == '') {
                alertify.error("Debe Ingresar Dirección IP");
                return false;
            }

            if ($("#ip4").val() > 254 || $("#ip4").val() == 0) {
                alertify.error("Ingrese una Dirección IP válida");
                return false;
            }

            if ($("#txt_estacion").val() == '') {
                alertify.error("Debe Ingresar Nombre de la Estación");
                return false;
            }//ipM1

            if ($('#selmenu').val() == null) {
                alertify.error("Debe seleccionar por lo menos un Menú");
                return false;
            }
            if ($("#selTipoCobro").val() == 0) {
                alertify.error("Debe Seleccionar un Tipo de Cobro");
                return false;
            }

            if ($("#txt_tid").val() == '' && $("#selTipoCobro").val() != 3) {
                alertify.error("Ingrese TID(Tarjeta de Credito)");
                return false;
            }
            //lc_ip=$("#txt_ipModSificado").val();
            lc_nombreIp = $("#txt_nombre").val();
            if ($("#txt_estacion").val().length > 1) {
                lc_numeroIp = $("#txt_estacion").val();
            } else {
                lc_numeroIp = '0' + ($("#txt_estacion").val());
            }
            //lc_numeroIp=$("#txt_estacion").val();
            lc_nombrenumeroIp = lc_nombreIp + lc_numeroIp;
            //lc_estacion=$("#cod_estacion").val();

            send = {"grabamodificaestacionnuevo": 1};
            //send.lc_ip=lc_ip;
            send.lc_nombrenumeroIp = lc_nombrenumeroIp;
            //send.lc_estacion=lc_estacion;

            send.ip1 = $("#ip1").val();
            send.ip2 = $("#ip2").val();
            send.ip3 = $("#ip3").val();
            send.ip4 = $("#ip4").val();
            send.menu = $("#selmenu").val();
            send.tipoenvio = '1';//$("#selTipoCobro").val();
            send.tid = $("#txt_tid").val();
            send.lc_selres = $("#descripcionRestaurante").val();
            send.lc_control = lc_control;

            if ($("#option").is(':checked')) {
                send.estado = 10;
            } else {
                send.estado = 11;
            }

            $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
                if (datos.Existe == 1) {
                    alertify.error("Ya existe la IP Ingresada");
                    return false;
                }
                if (datos.Existe == 3) {
                    alertify.error("Ya existe nombre de estacion, Ingrese otro");
                    return false;
                }
                if (datos.Existe == 2) {
                    //lc_control=0;//modificado
                    //alertify.alert("Estación Actualizada Exitosamente");
                    alertify.success("Estación agregada Correctamente");
                    //$('#ModalModificar').modal('hide');
                    //$("#modificado").empty();
                    $("#tabla_estacion").show();
                    lc_restaurante = $("#descripcionRestaurante").val();
                    //fn_cargarDetalle(lc_restaurante);
                    $("#detalle_estacion").show();
                    //$("#botonessalirMod").show();   
                    //$("#botonesguardarcancelarMod").hide();

                    fn_OpcionSeleccionadaModInsert();
                }
            });
        } else if (lc_control == 1) {
            ipp1 = $("#ipM1").val();
            ipp2 = $("#ipM2").val();
            ipp3 = $("#ipM3").val();
            ipp4 = $("#ipM4").val();
            if (ipp4 == '') {
                alertify.error("Debe Ingresar Dirección IP");
                return false;
            }

            if (ipp4 > 254 || ipp4 == 0) {
                alertify.error("Ingrese una Dirección IP válida");
                return false;
            }

            if ($("#txt_estacionmod").val() == '') {
                alertify.error("Debe Ingresar Nombre de la Estación");
                return false;
            }//ipM1
            if ($("#txt_tidMod").val() == '' /*&& $("#selTipoCobroMod").val()!=3*/) {
                alertify.error("Ingrese TID(Tarjeta de Credito)");
                return false;
            }
            if ($('#selmenumodifica').val() == null) {
                alertify.error("Debe seleccionar por lo menos un Menú");
                return false;
            }

            if ($("#selDesasignarEstacionMod").val() == '-1') {
                alertify.error("Seleccione Desasigna en Estación o Punto de Venta");
                return false;
            }

           
            var puntoemision = String($("#txt_puntoEmisionMod").val());
            var p = String(puntoemision).length;
            if (p < 3) {
                alertify.error("Ingrese Punto de Emisión de 3 dígitos");
                return false;
            }

            if ($('#selrest option:selected').attr('data-fastFood') === '1' && $("#selModificarMesa").val() === '-1') {
                alertify.error("Seleccionar la mesa");
                return false;
            }
            var idMesa = $("#selModificarMesa").val();
            lc_puntoemision = $("#txt_puntoEmisionMod").val();
            //lc_ip=$("#txt_ipModSificado").val();
            lc_nombreIp = $("#txt_nombre").val();
            if ($("#txt_estacionmod").val().length > 1) {
                lc_numeroIp = $("#txt_estacionmod").val();
            } else {
                lc_numeroIp = '0' + ($("#txt_estacionmod").val());
            }
            //lc_numeroIp=$("#txt_estacion").val();
            lc_nombrenumeroIp = lc_nombreIp + lc_numeroIp;
            lc_estacion = $("#cod_estacion").val();
             
           var ordenado=ChosenOrder.getSelectionOrder( $("#selmenumodifica") );
            //alert(ChosenOrder.getSelectionOrder( $("#selmenumodifica") ));     
            send = {"grabamodificaestacion": 1};
            //send.lc_ip=lc_ip;
            send.lc_nombrenumeroIp = lc_nombrenumeroIp;
            send.lc_estacion = lc_estacion;
            
            //alert($('#selmenu chosen-select').val());
            //alert($("#selmenumodifica").val());
                //.trigger("chosen:updated.chosen")
            send.ip1 = $("#ipM1").val();
            send.ip2 = $("#ipM2").val();
            send.ip3 = $("#ipM3").val();
            send.ip4 = $("#ipM4").val();           
            send.menu = ordenado; //ChosenOrder.getSelectionOrder( $("#selmenumodifica") ); //$("#selmenumodifica").val();
            send.tipoenvio = '1';//$("#selTipoCobroMod").val();
            send.tid = $("#txt_tidMod").val();
            send.lc_selres = $("#descripcionRestaurante").val();
            send.lc_control = lc_control;
            send.lc_puntoemision = lc_puntoemision;
            send.idMesa = idMesa;
            if ($("#optionmod").is(':checked')) {
                send.estado = 10;
            } else {
                send.estado = 11;
            }

            $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
                if (datos.Existe == 1) {
                    alertify.error("Ya existe la IP Ingresada");
                    return false;
                }
                if (datos.Existe == 3) {
                    alertify.error("Ya existe nombre de estación, ingrese otro");
                    return false;
                }                
                if (datos.Existe == 2) {                   
                    fn_grabaColeccionPagoPredeterminado('M');
                    fn_grabaColeccionMedioAutorizador('M');
                    lc_estacion = $("#cod_estacion").val();
                    send = {"guardaVariosMenusMod": 1};
                    send.menu = ordenado;//$("#selmenumodifica").val();
                    send.lc_selres = $("#descripcionRestaurante").val();//lc_selres;
                    send.lc_control = 6;
                    send.lc_estacion = lc_estacion;
                    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
                    });

                    fn_insertaDesasignarEstacionMod();
                    //fn_insertaTipoEnvioMod();

                    alertify.success("Estación Actualizada Correctamente");
                    $("#tabla_estacion").show();
                    lc_restaurante = $("#descripcionRestaurante").val();
                    $("#detalle_estacion").show();

                    fn_OpcionSeleccionadaModInsert();

                    $('#ModalModificar').modal('hide');
                }
            });
        } else if (lc_control == 0) {// graba por primera vez
            //$("#txt_puntoEmision").val('');
            if ($("#ip4").val() == '') {
                alertify.error("Debe Ingresar Dirección IP");
                return false;
            }
            if ($("#ip4").val() > 254 || $("#ip4").val() == 0) {
                alertify.error("Ingrese una Dirección IP válida");
                return false;
            }
            if ($("#txt_estacion").val() == '') {
                alertify.error("Debe Ingresar Numero de la Estación");
                return false;
            }

            if ($('#selmenu').val() == null) {
                alertify.error("Debe seleccionar por lo menos un Menú");
                return false;
            }

            if ($("#selTipoCobro").val() == 0) {
                alertify.error("Debe Seleccionar un Tipo de Cobro");
                return false;
            }
            if ($("#txt_tid").val() == '' /*&& $("#selTipoCobro").val()!=3*/) {
                alertify.error("Ingrese TID(Tarjeta de Credito)");
                return false;
            }

            if ($("#selDesasignarEstacion").val() == '-1') {
                alertify.error("Seleccione Desasigna en Estación ó Punto de Venta");
                return false;
            }

            /*if ($("#selTipoEnvio").val() == '-1')
             {
             alertify.error("Seleccione Tipo Envio");
             return false;
             }*/

            var puntoemision = String($("#txt_puntoEmision").val());
            var p = String(puntoemision).length;
            if (p < 3) {
                alertify.error("Ingrese Punto de Emisión de 3 dígitos");
                return false;
            }
            if ($('#selrest option:selected').attr('data-fastFood') === '1' && $("#selNuevoMesa").val() === '-1') {
                alertify.error("Seleccionar la mesa");
                return false;
            }            
            var idMesa = $("#selNuevoMesa").val();
            lc_selcadena = $("#selcadenaNuevo").val();
            lc_selres = $("#selrestNuevo").val();
            lc_puntoemision = $("#txt_puntoEmision").val();
            //lc_ip=$("#txt_ip").val();
            ip1 = $("#ip1").val();
            ip2 = $("#ip2").val();
            ip3 = $("#ip3").val();
            ip4 = $("#ip4").val();

            lc_nombreIp = $("#txt_nombre").val();
            if ($("#txt_estacion").val().length > 1) {
                lc_numeroIp = $("#txt_estacion").val();
            } else {
                lc_numeroIp = '0' + ($("#txt_estacion").val());
            }
            //lc_numeroIp=$("#txt_estacion").val();
            lc_nombrenumeroIp = lc_nombreIp + lc_numeroIp;

            //fn_menu("Nuevo");
            send = {"grabamodificaestacion": 1};
            send.lc_selcadena = lc_selcadena;
            send.lc_selres = $("#descripcionRestaurante").val();//lc_selres;
            send.ip1 = ip1;
            send.ip2 = ip2;
            send.ip3 = ip3;
            send.ip4 = ip4;
            send.lc_nombrenumeroIp = lc_nombrenumeroIp;
            send.menu = ChosenOrder.getSelectionOrder($("#selmenu"));// $("#selmenu").val();
            send.tipoenvio = '1';
            $("#selTipoCobro").val();
            send.tid = $("#txt_tid").val();
            send.lc_puntoemision = lc_puntoemision;
            send.lc_estacion = '0'; //no existe todavia estacion
            send.idMesa = idMesa;
            if ($("#option").is(':checked')) {
                send.estado = 10;
            } else {
                send.estado = 11;
            }
            send.lc_control = lc_control;
            $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
                if (datos.Existe == 1) {
                    alertify.error("Ya existe la IP Ingresada");
                    return false;
                }
                if (datos.Existe == 3) {
                    alertify.error("Ya existe nombre de estacion, Ingrese otro");
                    return false;
                }

                if (datos.Existe == 2) {
                    
                    
                    lc_idEstacionNueva = datos.idestacioninsert;
                    fn_grabaColeccionMedioAutorizador('I');
                    fn_grabaColeccionPagoPredeterminado('I');
                    send = {"guardaVariosMenus": 1};
                    send.menu = ChosenOrder.getSelectionOrder($("#selmenu"));//$("#selmenu").val();
                    send.lc_selres = $("#descripcionRestaurante").val();//lc_selres;
                    send.lc_control = 4;
                    send.lc_estacion = datos.idestacioninsert;
                    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
                    });

                    id_estacion = datos.idestacioninsert;
                    fn_insertaDesasignarEstacion(id_estacion);
                    //fn_insertaTipoEnvio(id_estacion);
                    //lc_control=1;//nuevo
                    lc_control = 3;
                    alertify.success("Estación Agregada Correctamente");

                    $("#tabla_estacion").show();
                    lc_restaurante = $("#descripcionRestaurante").val();
                    fn_OpcionSeleccionadaModInsert();
                    $("#idestacionnueva").val(datos.idestacioninsert);
                    $('#fin').show();

                    $('#botonesguardarcancelar').hide();
                    $('#botonessalir').show();

                    $('#ModalNuevo').modal('hide');
                    $("#txt_puntoEmision").val('');
                }
            });
        }
    } else if (accion == 'Cancelar') {
        window.location.reload();
        fn_menu('Cancelar');
        //fn_paginado(-1);
    } else if (accion == 'Modificar') {
        lc_control = 1;
        lc_estacion = $("#cod_estacion").val();
        fn_cargarestacionModificada(lc_estacion);
        $('#ModalModificar').modal('show');
    }
}

/*function fn_mostrarDivsettings(){
 //$('#settings').show();
 $("#inicio").addClass("active")
 
 }
 
 function fn_ocultarDivsettings(){
 
 if( $('#fin').is(":visible") ){
 alert('Elemento visible');
 $('#settings').show();
 }//else{
 //alert('Elemento oculto');
 //$('#settings').hide();
 //}
 
 }*/
function fn_grabaColeccionPagoPredeterminado(accion) {
    //accion 1 -> inserta , 2->modifica
    send = {"IAEColeccionPagoPredeterminado": 1};
    send.accion = accion;
    if (accion == 'I') { //inserta
        send.idFormaPagoN = $('#selPagoPredeterminado').val();
        send.idEstacionNueva = lc_idEstacionNueva;
    } else { //actualiza
        send.idFormaPagoN = $('#selPagoPredeterminadoM').val();
        send.idEstacionNueva = lc_estacion;
    }
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_grabaColeccionMedioAutorizador(bandera) {
    send = {"IAEColeccionMedioAutorizador": 1};
    send.accion = bandera;
    if (bandera == 'I') { //inserta
        send.madioA = $('#selTipoCobro').val();
        send.idEstacionNueva = lc_idEstacionNueva;
    } else { //actualiza
        send.madioA = $('#selTipoCobroMod').val();
        send.idEstacionNueva = lc_estacion;
    }
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_cargarDetalle(lc_rest) {
    send = {"cargarDetalle": 1};
    send.lc_res = lc_rest;//$("#selcadena").val();
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#tabla_estacion").show();
                html = "<thead><tr class='active'>";
                html += "<th style='width:160px; text-align:center;'>Ip</th>";
                html += "<th align='center' style='width:180px; text-align:center;'>Nombre Estación</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Menú</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Activo</th>";
                html += "</tr></thead>";
                $("#detalle_estacion").empty();
                for (var i = 0; i < datos.str; i++) {
                    //var test6 = new String("999 888");	

                    html += "<tr class='tabla_detalleMov coleM' id='" + i + "' style='cursor:pointer;'";
                    //html+="onclick=fn_seleccionclick("+i+");";
                    html += "onclick=fn_seleccionclick(" + i + "); ondblclick=fn_seleccion('" + datos[i]['est_ip'] + "'," + i + ",'" + datos[i]['est_id'] + "'," + datos[i]['menu_id'] + ");>";
                    html += "<td align='center'  style='width:160px;'>" + datos[i]['est_ip'] + "&nbsp;</td>";
                    html += "<td align='center'  style='width:180px;'>" + datos[i]['est_nombre'] + "&nbsp;</td>";
                    html += "<td align='center'  style='width:170px;'>" + datos[i]['menu_Nombre'] + "&nbsp;</td>";
                    //html+="<td align='center'  style='width:169px;'>"+datos[i]['Estado']+"&nbsp;</td></tr>";
                    if (datos[i]['Estado'] == 10) {
                        html += "<td align='center'  style='width:169px;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";
                    }
                    if (datos[i]['Estado'] == 11) {
                        html += "<td align='center'  style='width:169px;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";
                    }
                    $("#detalle_estacion").html(html);
                }
                $('#detalle_estacion').dataTable(
                        {//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
                            'destroy': true
                        }
                );

                $("#detalle_estacion_length").hide();
                $("#detalle_estacion_paginate").addClass('col-xs-10');
                $("#detalle_estacion_info").addClass('col-xs-10');
                $("#detalle_estacion_length").addClass('col-xs-6');
            } else {
                //$("#tabla_estacion").show();
                html = "<thead><tr class='active'>";
                html += "<th style='width:160px; text-align:center;'>Ip</th>";
                html += "<th align='center' style='width:180px; text-align:center;'>Nombre Estación</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Menu</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Activo</th>";
                html += "</tr></thead>";
                $("#detalle_estacion").html(html);
                //$("#tabla_estacion").hide();
                //alertify.alert("No existen estaciones configuradas para este restaurante");
                alertify.error("No existen estaciones configuradas para este restaurante");
            }
        }
    });

}

function fn_cargarDetalleInactivos(lc_rest, accion) {
    send = {"cargarDetalleInactivos": 1};
    send.lc_res = lc_rest;//$("#selcadena").val();
    send.estado = accion;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#tabla_estacion").show();
                html = "<thead><tr class='active'>";
                html += "<th style='width:160px; text-align:center;'>Ip</th>";
                html += "<th align='center' style='width:180px; text-align:center;'>Nombre Estación</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Menú</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Activo</th>";
                html += "</tr></thead>";
                $("#detalle_estacion").empty();
                for (var i = 0; i < datos.str; i++) {
                    //var test6 = new String("999 888");	

                    html += "<tr class='tabla_detalleMov' id='" + i + "' style='cursor:pointer;'";
                    //html+="onclick=fn_seleccionclick("+i+");";
                    html += "onclick=fn_seleccionclick(" + i + "); ondblclick=fn_seleccion('" + datos[i]['est_ip'] + "'," + i + ",'" + datos[i]['est_id'] + "'," + datos[i]['menu_id'] + ");>";
                    html += "<td align='center'  style='width:160px;'>" + datos[i]['est_ip'] + "&nbsp;</td>";
                    html += "<td align='center'  style='width:180px;'>" + datos[i]['est_nombre'] + "&nbsp;</td>";
                    html += "<td align='center'  style='width:170px;'>" + datos[i]['menu_Nombre'] + "&nbsp;</td>";
                    //html+="<td align='center'  style='width:169px;'>"+datos[i]['Estado']+"&nbsp;</td></tr>";
                    if (datos[i]['Estado'] == 10) {
                        html += "<td align='center'  style='width:169px;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";
                    }
                    if (datos[i]['Estado'] == 11) {
                        html += "<td align='center'  style='width:169px;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";
                    }
                    $("#detalle_estacion").html(html);
                }
                $('#detalle_estacion').dataTable(
                        {//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
                            'destroy': true
                        }
                );
                $("#detalle_estacion_length").hide();
                $("#detalle_estacion_paginate").addClass('col-xs-10');
                $("#detalle_estacion_info").addClass('col-xs-10');
                $("#detalle_estacion_length").addClass('col-xs-6');
            } else {
                html = "<thead><tr class='active'>";
                html += "<th style='width:160px; text-align:center;'>Ip</th>";
                html += "<th align='center' style='width:180px; text-align:center;'>Nombre Estación</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Menu</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Activo</th>";
                html += "</tr></thead>";
                $("#detalle_estacion").html(html);
                //alertify.alert("No existen estaciones configuradas para este restaurante");
                alertify.error("No existen datos");
            }
        }
    });
}

function fn_seleccion(ip, fila, idEstacion, id_menu) {
    $("#detalle_estacion tr").removeClass("success");
    $("#" + fila + "").addClass("success");
    $("#hid_ip").val("ip");
    $("#cod_estacion").val(idEstacion);
    $("#idMenu").val(id_menu);
   
    
      $("#divModificarSeleccionarMesa").show();
        consultaColeccionMesa("Modificar", idEstacion);
//    if ($('#selrest option:selected').attr('data-fastFood') === '1') {
//        $("#divModificarSeleccionarMesa").show();
//        consultaColeccionMesa("Modificar", idEstacion);
//    } else {
//        $("#divModificarSeleccionarMesa").hide();
//        $("#selModificarMesa").empty();
//    }
    lc_ip = ip;
    lc_estacion = idEstacion;
    fn_accionar('Modificar');
    fn_traerCanalesImpresionModificar(idEstacion);
    fn_configuracionPoliticasModificar(idEstacion);
    fn_consultaColeccionPagoPredeterminado();
    //$("#selTipoCobroMod").val('');
    // $('#selTipoCobroMod').trigger("chosen:updated");
    fn_consultaColeccionMediosAutorizadores();
    lc_idColeccionEstacionM = '';
}

function fn_consultaColeccionPagoPredeterminado() {
    send = {"USPcoleccionPagoPredeterminado": 1};
    send.accion = 2;
    send.idEstacionModifica = lc_estacion;
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#selPagoPredeterminadoM").val(datos.idIntegracion);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_consultaColeccionMediosAutorizadores() {
    var arreglo = [];
    send = {"USPcoleccionMediosAutorizadores": 1};
    send.accion = 2;
    send.idEstacionModificaMedioAutorizador = lc_estacion;
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    arreglo.push(datos[i]['idIntegracion']);
                }
                $('#selTipoCobroMod').val(arreglo).trigger("chosen:updated.chosen");
            } else {
                $("#selTipoCobroMod").val(0);
                $('#selTipoCobroMod').trigger("chosen:updated");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_configuracionPoliticasModificar(estId) {
    var html = '<tr class="bg-primary"><th>Descripción</th><th class="text-center">Dato</th><th>Especifica Valor</th><th>Obligatorio</th><th class="text-center">Tipo</th><th class="text-center">Valor</th><th>Activo</th></tr>';
    send = {"cargaConfiguracionPoliticasModificar": 1};
    send.idEstacionConfigM = estId;
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var valorPolitica = evaluarValorPolitica(datos[i]);
                    html += '<tr id="' + i + '_es" onclick="fn_seleccionColeccion(' + i + ',\'' + datos[i]['descripcion_coleccion'] + '\',\'' + datos[i]['ID_ColeccionEstacion'] + '\',\'' + datos[i]['ID_ColeccionDeDatosEstacion'] + '\',\'' + datos[i]['descripcion_dato'] + '\')" id="' + i + '" class="text-center"><td>' + datos[i]['descripcion_coleccion'] + '</td><td>' + datos[i]['descripcion_dato'] + '</td>';
                    if (datos[i]['especificarValor'] === 1) {
                        html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';

                    } else {
                        html += '<td><input type="checkbox" value="1" disabled/></td>';
                    }
                    if (datos[i]['obligatorio'] === 1)
                    {
                        html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } else {
                        html += '<td><input type="checkbox" value="1" disabled/></td>';
                    }
                    html += '<td>' + datos[i]['tipodedato'] + '</td><td>' + valorPolitica + '</td>';
                    if (datos[i]['isActive'] === 1)
                    {
                        html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } else {
                        html += '<td><input type="checkbox" value="1" disabled/></td>';
                    }
                    html += '</tr>';
                }
                $("#tbl_estacion_coleccion").html(html);
            } else {
                html = html + '<tr><th colspan="14" class="text-center">No existen registros.</th></tr>';
                $("#tbl_estacion_coleccion").html(html);
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
            return fila.seleccion;
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

function fn_seleccionclick(fila) {
    $("#detalle_estacion tr").removeClass("success");
    $("#" + fila + "").addClass("success");
}

function fn_seleccionColeccion(indice, nombreColeccion, idColeccion, idDatos, nombreDatos) {
    /*indice=indice+1;
     $("#tbl_estacion_coleccion tr").removeClass("success");    
     $("#tbl_estacion_coleccion tr:eq("+indice+")").addClass("success");    
     lc_idColeccionEstacionM=idColeccion;    
     lc_nombreColeccionM=nombreColeccion;
     */
    $("#tbl_estacion_coleccion tr").removeClass("success");
    //$("#listaCadenas tr:eq("+indice+")").addClass("success");           
    $("#" + indice + "_es").addClass("success");

    lc_idColeccionCadena = idColeccion;
    lc_nombreColeccion = nombreColeccion;
    lc_coleccionDatosCadena = idDatos;
    lc_nombreColeccionDatos = nombreDatos;
}

function fn_cargarestacionModificada(lc_estacion) {
    $("#txt_rstModifica").val('');
    $("#ipM1").val('');
    $("#ipM2").val('');
    $("#ipM3").val('');
    $("#ipM4").val('');
    $("#txt_tidMod").val('');
    $("#selmenumodifica").empty();
    //$("#selTipoCobroMod").empty();

    //$('#titulomodalModificar').html("Modificar Estaci�n: ");
    res = $("#selrest").val();
    if ($("#selrest").val() == 0) {
        alertify.alert("Seleccione el Restaurante");
        return false;
    }
    if (lc_ip == -1) {
        alertify.alert('Debe seleccionar un registro');
        return false;
    }
    if (res != 0) {
        send = {"cargarestacionModifica": 1};
        send.estacion = lc_estacion;
        send.menu = $("#descripcionMenu").val();
        send.descripcionRestaurante = $("#descripcionRestaurante").val();
        $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    $("#txt_rstModifica").val(datos[0]['Restaurante']);
                    $("#ipM1").val(datos[0]['primer_octeto_ip']);
                    $("#ipM2").val(datos[0]['segundo_octeto_ip']);
                    $("#ipM3").val(datos[0]['tercer_octeto_ip']);
                    $("#ipM4").val(datos[0]['cuarto_octeto_ip']);
                    $("#txt_puntoEmisionMod").val(datos[0]['est_punto_emision']);
                    $("#idTipoEnvioMod").val(datos[0].tpenv_id);
                    $("#txt_tidMod").val(datos[0].tid);

                    if (datos.numero_estacion > 9) {
                        $("#txt_estacionmod").val(datos[0]['numero_estacion']);
                    } else {
                        $("#txt_estacionmod").val('0' + datos[0]['numero_estacion']);
                    }

                    numeroestacion = $("#txt_estacionmod").val();
                    $('#titulomodalModificar').html('');
                    $('#titulomodalModificar').html('Estación');
                    $('#titulomodalModificar').append(" " + numeroestacion + " ");
                    //$("#txt_estacionmodsuperior").val(numeroestacion);
                    $("#txt_tidMod").val(datos[0]['tid']);
                    if (datos[0]['std_id'] == 10) {
                        $("#optionmod").prop("checked", true);  // para poner la marca
                    }
                    if (datos[0]['std_id'] == 11) {
                        $("#optionmod").prop("checked", false);
                    }
                   
                    send = {"cargarmenu": 1};
                    send.estacionid=lc_estacion;
                    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
                        $('#selmenumodifica').empty();
                        //$('#selmenumodifica').val('').trigger("chosen:updated.chosen");
                        for (var i = 0; i < datos.str; i++) {
                            html = "<option value='" + datos[i]['menu_id'] + "'>" + datos[i]['menu_nombre'] + "</option>";
                            $("#selmenumodifica").append(html);
                            //$("#selcadena").append("<option selected value="+datos[i]['cdn_id']+">"+datos[i]['cdn_descripcion']+"</option>");
                            arrayMenu.length = 0;
                        }
                        $('#selmenumodifica').chosen();
                        $("#selmenumodifica_chosen").css('width', '490');
                        
                        //var elementsInOrder = [];    
                        
                        $("#selmenumodifica").change(function () {
                            lc_cadena = $("#selmenumodifica").val();                           
                            $("#descripcionMenu").val(lc_cadena);                                   
                            //elementsInOrder.push(el.textContent);
                        });
                         
                        fn_cargarMenuDinamicoEstacion();
                    });

                    fn_cargarSelectDesasignarEstacionMod();
                    //fn_cargarSelectTipoEnvioMod();

                    idtipoenvio = $("#idTipoEnvioMod").val();

                    //fn_retornaMediosAutorizadores();

                    /*
                     send = {"cargarTipoCobro": 1};
                     send.estt_id = $("#cod_estacion").val();
                     $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
                     $("#selTipoCobroMod").empty();
                     for (var i = 0; i < datos.str; i++){
                     html = "<option value='" + datos[i]['tpenv_id'] + "'>" + datos[i]['tpenv_descripcion'] + "</option>";
                     $("#selTipoCobroMod").append(html);
                     }
                     $("#selTipoCobroMod").val(idtipoenvio);
                     $("#selTipoCobroMod").change(function () {
                     lc_cadena = $("#selTipoCobroMod").val();
                     });
                     });*/
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(jqXHR);
                alert(textStatus);
                alert(errorThrown);
            }
        });
    }
}

function fn_cargarMenuDinamicoEstacion() {
    var array = [];
    send = {"cargarselmenu": 1};
    send.id_menu = $("#idMenu").val();
    send.lc_estacion = $("#cod_estacion").val();
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
        //$("#selmenumodifica").empty();
        if (datos.str > 0) {          
            for (var i = 0; i < datos.str; i++) {               
                array.push(datos[i]['menu_id']);                
            }
             
            $('#selmenumodifica').val(array).trigger("chosen:updated.chosen");
            
             //$("#selmenumodifica").append(html);
        } else {
            $("#selmenumodifica").val(0);
            $('#selmenumodifica').trigger("chosen:updated");
        }
    });
}

/*=============================================================================================*/
/*FUNCION QUE CREA LOS BOTONES GUARDAR Y CANCELER EN LA MODAL SI ES NUEVO O MODIFICAR		   */
/*=============================================================================================*/
function fn_botonesGuardarCancelarModal() {
    //$("#cadena").attr('disabled', true);
    //Accion = 2;
    //fn_cargarFormaPago(Cod_Forma_Pago);

    $('#pnl_pcn_btn').html("<button type='button' onclick='fn_accionar('Grabar');' class='btn btn-primary'>Aceptar</button><button type='button' class='btn btn-default' data-dismiss='modal'>Cancelar</button>");
}

/*=============================================================================================*/
/*FUNCION QUE CREA LOS BOTONES SALIR EN LA MODAL SI ES NUEVO O MODIFICAR		   */
/*=============================================================================================*/
function fn_botonesSalirModal() {
    //$("#cadena").attr('disabled', true);
    //Accion = 2;
    //fn_cargarFormaPago(Cod_Forma_Pago);

    $('#pnl_pcn_btn').html('<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>');
}

/*====================================================*/
/*FUNCION QUE TRAE LOS CANALES DE IMPRESION ACTIVOS	  */
/*====================================================*/
function fn_traerCanalesImpresion() {
    accion = 1;
    send = {"traerCanalesImpresion": 1};
    send.accion = accion;
    send.est_id = 0;
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
        if (datos.str > 0) {
            $("#listaCanalesImpresion").empty();
            $("#CabeceralistaCanalesImpresion").empty();
            $("#CabeceralistaCanalesImpresion").append("<b><a class='list-group-item active'>CANAL &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; IMPRESORA &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; PUERTO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; RESPALDO</a></b>");
            for (var i = 0; i < datos.str; i++) {
                html = /*html +*/ "<a id=a" + i + " onclick=''; class='list-group-item'>  <div class='row'><div class='col-xs-2'><spam id='labelCanalesImpresion" + i + "' style='width:400%;'>" + datos[i]['cimp_descripcion'] + "</spam></div><div class='col-xs-3'><select class='form-control' id='selimpresoras" + i + "' name='selimpresoras'></select></div><div class='col-xs-4'><select class='form-control' id='selpuertos" + i + "' name='selpuertos'></select></div><div class='col-xs-3'><select class='form-control' id='selimpresorasrespaldo" + i + "' name='selimpresorasrespaldo'></select></div></div></a>";
                $("#listaCanalesImpresion").append(html);

                valor = i;
                fn_validacionesSelect(datos[i]['cimp_id'], i);
                fn_traerImpresoras(valor);
                fn_traerpuertos(valor);
                fn_traerImpresorasrespaldo(valor);
                //$("#selpuertos"+valor+"").val(0);
            }
        } else {
            $("#listaCanalesImpresion").append('No existen Impresoras');
        }
    });
}

/*====================================================*/
/*FUNCION PARA CARGAR LOS PUERTOS  		      */
/*====================================================*/
function fn_traerpuertos(valor) {
    accion = 5;
    send = {"traerPuertos": 1};
    send.accion = accion;
    send.cimp_id = 0;
    send.idestacion = 0;
    send.restaurante = $("#selrest").val();
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
        $("#selpuertos" + valor + "").empty();
        $("#selpuertos" + valor + "").append("<option selected value='0'>&nbsp;&nbsp;&nbsp;&nbsp;SELECCIONE PUERTO</option>");
        for (var i = 0; i < datos.str; i++)
        {
            html = "<option value='" + datos[i]['pto_id'] + "'>" + datos[i]['pto_descripcion'] + "</option>";
            $("#selpuertos" + valor + "").append(html);
            //$("#selcadena").append("<option selected value="+datos[i]['cdn_id']+">"+datos[i]['cdn_descripcion']+"</option>");
        }
    });
}

/*====================================================*/
/*FUNCION PARA CARGAR LAS IMPRESORAS				  */
/*====================================================*/
function fn_traerImpresoras(valor) {
    accion = 2;
    send = {"traerImpresoras": 1};
    send.accion = accion;
    send.cimp_id = 0;
    send.idestacion = 0;
    send.restaurante = $("#selrest").val();
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
        if (datos.str > 0) {
            $("#selimpresoras" + valor + "").empty();
            $("#selimpresoras" + valor + "").append("<option selected value='0'>&nbsp;NO IMPRESORA</option>");
            for (var i = 0; i < datos.str; i++)
            {
                html = "<option value='" + datos[i]['imp_id'] + "'>" + datos[i]['imp_nombre'] + "</option>";
                $("#selimpresoras" + valor + "").append(html);
                //$("#selcadena").append("<option selected value="+datos[i]['cdn_id']+">"+datos[i]['cdn_descripcion']+"</option>");
            }
        } else {
            $("#selimpresoras" + valor + "").empty();
            $("#selimpresoras" + valor + "").append("<option selected value='0'>&nbsp;NO IMPRESORA</option>");
        }

    });
}

/*====================================================*/
/*FUNCION PARA CARGAR LAS IMPRESORAS DE RESPALDO      */
/*====================================================*/
function fn_traerImpresorasrespaldo(valor) {
    accion = 2;
    send = {"traerImpresoras": 1};
    send.accion = accion;
    send.cimp_id = 0;
    send.idestacion = 0;
    send.restaurante = $("#selrest").val();
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
        if (datos.str > 0) {
            $("#selimpresorasrespaldo" + valor + "").empty();
            $("#selimpresorasrespaldo" + valor + "").append("<option selected value='0'>&nbsp;NO IMPRESORA</option>");
            for (var i = 0; i < datos.str; i++) {
                html = "<option value='" + datos[i]['imp_id'] + "'>" + datos[i]['imp_nombre'] + "</option>";
                $("#selimpresorasrespaldo" + valor + "").append(html);
                //$("#selcadena").append("<option selected value="+datos[i]['cdn_id']+">"+datos[i]['cdn_descripcion']+"</option>");
            }
        } else {
            $("#selimpresorasrespaldo" + valor + "").empty();
            $("#selimpresorasrespaldo" + valor + "").append("<option selected value='0'>&nbsp;NO IMPRESORA</option>");
        }
    });
}

/*=============================================================*/
/*VALIDACIONES PARA LOS SELECT y QUE NO SE REPITA EL PUERTO    */
/*=============================================================*/
function fn_validacionesSelect(codigo, fila) {
    Cod_CanalImpresion = codigo;
    //$("a").removeClass("list-group-item-success");
    //$("#a"+fila+"").addClass("list-group-item-success");

    $("#selimpresoras" + fila + ", #selpuertos" + fila + ", #selimpresorasrespaldo" + fila + "").change(function () {
        if ($("#ip4").val() == '' || $("#ip4").val() > 254 || $("#ip4").val() == 0 || $("#txt_estacion").val() == '' || $("#selmenu").val() == 0 || $("#selTipoCobro").val() == 0) {
            alertify.error("Complete datos en estacion!!");
            $("#selimpresoras" + fila + "").val('0');
            $("#selpuertos" + fila + "").val('0');
            $("#selimpresorasrespaldo" + fila + "").val('0');
            return false;
        }

        if ($("#txt_tid").val() != '') {
            alertify.error("Ingrese TID!!");
            $("#selimpresoras" + fila + "").val('0');
            $("#selpuertos" + fila + "").val('0');
            $("#selimpresorasrespaldo" + fila + "").val('0');
            return false;
        }
    });

    $("#selpuertos" + fila + "").change(function () {
        if ($("#selimpresoras" + fila + "").val() == 0) {
            alertify.error("Seleccione Impresora!!");
            $("#selpuertos" + fila + "").val('0');
            return false;
        }
    });

    $("#selimpresoras" + fila + "").change(function () {
        if ($("#selimpresoras" + fila + "").val() == 0) {
            $("#selpuertos" + fila + "").val('0');
            $("#selimpresorasrespaldo" + fila + "").val('0');
            return false;
        } else {
            fn_guardaImpresoraPuerto(codigo, fila);
        }
    });

    $("#selpuertos" + fila + "").change(function () {
        if ($("#selpuertos" + fila + "").val() == 0) {
            $("#selimpresorasrespaldo" + fila + "").val('0');
            return false;
        } else {
            accion = 6;
            Cod_Puerto = $("#selpuertos" + fila + "").val();
            send = {"validaPuertos": 1};
            send.accion = accion;
            send.restaurante = $("#selrest").val();
            if ($("#selpuertos" + fila + "").val() != 0) {
                send.puerto = Cod_Puerto;
            } else {
                send.puerto = '';
            }
            $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
                if (datos.str == 1) {
                    if (datos[0]['pto_id'] == 1) {
                        alertify.error("El puerto ya se encuentra configurado, elija otro!!.");
                        $("#selpuertos" + fila + "").val(0);
                        return false;
                    } else {
                        fn_guardaImpresoraPuerto(codigo, fila);
                    }
                }
            });
        }

    });

    $("#selimpresorasrespaldo" + fila + "").change(function () {
        if ($("#selimpresoras" + fila + "").val() == 0) {
            alertify.error("Seleccione Impresora!!");
            $("#selimpresorasrespaldo" + fila + "").val('0');
            return false;
        }
        if ($("#selpuertos" + fila + "").val() == 0) {
            alertify.error("Seleccione Puerto!!");
            $("#selimpresorasrespaldo" + fila + "").val('0');
            return false;
        } else {
            fn_guardaImpresorarespaldo(codigo, fila);
        }

    });
}

/*=======================================================================*/
/*FUNCION PARA GUARDAR NUEVA IMPRESORA, PUERTO                           */
/*=======================================================================*/
function fn_guardaImpresoraPuerto(codigo, fila) {
    Cod_CanalImpresion = codigo;

    Cod_Impresora = $("#selimpresoras" + fila + "").val();
    Cod_Puerto = $("#selpuertos" + fila + "").val();
    Cod_ImpresoraRespaldo = $("#selimpresorasrespaldo" + fila + "").val();

    accion = 3;
    send = {"guardaConfiguracionImpresion": 1};
    send.accion = accion;
    send.restaurante = $("#selrest").val();
    send.canalimpresion = Cod_CanalImpresion;
    send.impresora = Cod_Impresora;
    //send.impresorarespaldo=Cod_ImpresoraRespaldo;

    if ($("#selpuertos" + fila + "").val() == 0) {
        send.puerto = '';
    } else {
        send.puerto = Cod_Puerto;
    }
    if ($("#selimpresorasrespaldo" + fila + "").val() == 0) {
        send.impresorarespaldo = '';
    } else {
        send.impresorarespaldo = Cod_ImpresoraRespaldo;
    }
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
        //$("#btnsalir").addClass('btn-primary');
        alertify.success("Configuracion de Impresion agregada correctamente.");
    });

    //alertify.success("Configuracion de Impresion correctamente.") 
}

/*===================================================*/
/*FUNCION PARA GUARDAR NUEVA IMPRESORA DE RESPALDO   */
/*===================================================*/
function fn_guardaImpresorarespaldo(codigo, fila) {
    Cod_CanalImpresion = codigo;

    Cod_Impresora = $("#selimpresoras" + fila + "").val();
    Cod_Puerto = $("#selpuertos" + fila + "").val();
    Cod_ImpresoraRespaldo = $("#selimpresorasrespaldo" + fila + "").val();

    accion = 3;
    send = {"guardaConfiguracionImpresion": 1};
    send.accion = accion;
    send.restaurante = $("#selrest").val();
    send.canalimpresion = Cod_CanalImpresion;
    send.impresora = Cod_Impresora;
    send.impresorarespaldo = Cod_ImpresoraRespaldo;

    if ($("#selpuertos" + fila + "").val() == 0) {
        send.puerto = '';
    } else {
        send.puerto = Cod_Puerto;
    }
    if ($("#selimpresorasrespaldo" + fila + "").val() == 0) {
        send.impresorarespaldo = '';
    } else {
        send.impresorarespaldo = Cod_ImpresoraRespaldo;
    }
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
        //$("#btnsalir").addClass('btn-primary');
        alertify.success("Impresora de respaldo agregada correctamente.");
    });
}


/*====================================================================*/
/*FUNCION QUE TRAE LOS CANALES DE IMPRESION ACTIVOS EN MODIFICAR	  */
/*====================================================================*/
function fn_traerCanalesImpresionModificar(idestacion) {
    var accion = 1;
    send = {"traerCanalesImpresion": 1};
    send.accion = accion;
    send.est_id = idestacion;
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
        if (datos.str > 0) {
            $("#listaCanalesImpresionMod").empty();
            $("#CabeceralistaCanalesImpresionMod").empty();
            $("#CabeceralistaCanalesImpresionMod").append("<b><a class='list-group-item active'>CANAL &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; IMPRESORA &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; PUERTO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; RESPALDO</a></b>");
            for (var i = 0; i < datos.str; i++) {
                html = "<a id='a" + i + "m' onclick='' class='list-group-item'><div class='row'><div class='col-xs-2'><spam id='labelCanalesImpresionmod" + i + "' style='width:100%;'>" + datos[i]['cimp_descripcion'] + "</spam></div><div class='col-xs-3'><select class='form-control' id='selimpresorasmod" + i + "' name='selimpresorasmod'></select></div><div class='col-xs-4'><select class='form-control' id='selpuertosMod" + i + "' name='selpuertosMod'></select></div><div class='col-xs-3'><select class='form-control' id='selimpresorasrespaldomod" + i + "' name='selimpresorasrespaldomod'></select></div></div></a>";
                $("#listaCanalesImpresionMod").append(html);
                valor = i;
                cod_canalimpresion = datos[i]['cimp_id'];
                fn_validacionesSelectMod(cod_canalimpresion, valor, idestacion);
                fn_traerImpresorasMod(valor, cod_canalimpresion, idestacion);
                fn_traerpuertosMod(valor, cod_canalimpresion, idestacion);
                fn_traerImpresorasrespaldoMod(valor, cod_canalimpresion, idestacion);
                $("#selpuertosMod" + valor + "").val('p0');
            }
        }
    });
}

/*====================================================*/
/*FUNCION PARA CARGAR LAS IMPRESORAS EN MODIFICAR     */
/*====================================================*/

function fn_traerImpresorasMod(valor, idcanalimpresion, idestacion) {
    accion = 2;
    send = {"traerImpresorasMod": 1};
    send.accion = accion;
    send.cimp_id = idcanalimpresion;
    send.idestacion = idestacion;
    send.restaurante = $("#selrest").val();
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
        if (datos.str > 0) {
            $("#selimpresorasmod" + valor + "").empty();
            $("#selimpresorasmod" + valor + "").append("<option selected value='0'>NO IMPRESORA</option>");
            for (var i = 0; i < datos.str; i++) {
                //est_id = bandera
                if (datos[i]['est_id'] == '1') {
                    checked = datos[i]['imp_id'];
                } else {
                    checked = 0;
                }
                html = "<option value='" + datos[i]['imp_id'] + "'>" + datos[i]['imp_nombre'] + "</option>";
                $("#selimpresorasmod" + valor + "").append(html);
            }
            $("#selimpresorasmod" + valor + "").val(checked);

            Cod_CanalImpresion = idcanalimpresion;
        } else {
            $("#selimpresorasmod" + valor + "").empty();
            $("#selimpresorasmod" + valor + "").append("<option selected value='0'>&nbsp;NO IMPRESORA</option>");
        }
    });
}
/*====================================================*/
/*FUNCION PARA CARGAR LOS PUERTOS CUANDO MODIFICAMOS  */
/*====================================================*/
function fn_traerpuertosMod(valor, idcanalimpresion, idestacion) {
    accion = 5;
    send = {"traerPuertos": 1};
    send.accion = accion;
    send.cimp_id = idcanalimpresion;
    send.idestacion = idestacion;
    send.restaurante = $("#selrest").val();

    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
        if (datos.str > 0) {
            $("#selpuertosMod" + valor + "").empty();
            $("#selpuertosMod" + valor + "").append("<option selected value='0'>&nbsp;&nbsp;&nbsp;&nbsp;SELECCIONE PUERTO</option>");
            for (var i = 0; i < datos.str; i++) {
                if (datos[i]['est_id'] == '1') {
                    checked_p = datos[i]['pto_id'];
                } else {
                    checked_p = 0;
                }
                html = "<option value='" + datos[i]['pto_id'] + "'>" + datos[i]['pto_descripcion'] + "</option>";
                $("#selpuertosMod" + valor + "").append(html);
            }
            $("#selpuertosMod" + valor + "").val(checked_p);

            Cod_CanalImpresion = idcanalimpresion;
        }
    });
}

/*=======================================================================*/
/*FUNCION PARA CARGAR LAS IMPRESORA DE RESPALDO CUANDO MODIFICAMOS       */
/*=======================================================================*/
function fn_traerImpresorasrespaldoMod(valor, idcanalimpresion, idestacion) {
    accion = 7;
    send = {"traerImpresorasMod": 1};
    send.accion = accion;
    send.cimp_id = idcanalimpresion;
    send.idestacion = idestacion;
    send.restaurante = $("#selrest").val();
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
        if (datos.str > 0) {
            $("#selimpresorasrespaldomod" + valor + "").empty();
            $("#selimpresorasrespaldomod" + valor + "").append("<option selected value='0'>&nbsp;NO IMPRESORA</option>");
            for (var i = 0; i < datos.str; i++) {
                //est_id = bandera
                if (datos[i]['est_id'] == '1') {
                    checked = datos[i]['imp_id'];
                } else {
                    checked = 0;
                }
                html = "<option value='" + datos[i]['imp_id'] + "'>" + datos[i]['imp_nombre'] + "</option>";
                $("#selimpresorasrespaldomod" + valor + "").append(html);
            }
            $("#selimpresorasrespaldomod" + valor + "").val(checked);
        } else {
            $("#selimpresorasrespaldomod" + valor + "").empty();
            $("#selimpresorasrespaldomod" + valor + "").append("<option selected value='0'>&nbsp;NO IMPRESORA</option>");
        }
    });
}

/*==========================================================================*/
/*VALIDACIONES PARA LOS SELECT y QUE NO SE REPITA EL PUERTO EN MODIFICAR    */
/*==========================================================================*/
function fn_validacionesSelectMod(codigo, fila, idestacion) {
    Cod_CanalImpresion = codigo;
    //$("a").removeClass("list-group-item-success");
    //$("#a"+fila+"").addClass("list-group-item-success");

    $("#selimpresorasmod" + fila + ", #selpuertosMod" + fila + ", #selimpresorasrespaldomod" + fila + "").change(function () {

        if ($("#ipM4").val() == '' || $("#ipM4").val() > 254 || $("#ipM4").val() == 0 || $("#txt_estacion").val() == '' || $("#selmenumodifica").val() == 0 || $("#selTipoCobroMod").val() == 0) {
            alertify.error("Complete datos en estacion!!");
            $("#selimpresorasmod" + fila + "").val('0');
            $("#selpuertosMod" + fila + "").val('0');
            $("#selimpresorasrespaldomod" + fila + "").val('0');
            return false;
        }

        if ($("#txt_tidMod").val() == '') {
            alertify.error("Complete datos en estacion!!");
            $("#selimpresorasmod" + fila + "").val('0');
            $("#selpuertosMod" + fila + "").val('0');
            $("#selimpresorasrespaldomod" + fila + "").val('0');
            return false;
        }
    });

    $("#selpuertosMod" + fila + "").change(function () {
        if ($("#selimpresorasmod" + fila + "").val() == 0) {
            alertify.error("Seleccione Impresora!!");
            $("#selpuertosMod" + fila + "").val('0');
            return false;
        }
    });

    $("#selimpresorasmod" + fila + "").change(function () {
        if ($("#selimpresorasmod" + fila + "").val() == 0) {
            $("#selpuertosMod" + fila + "").val('0');
            $("#selimpresorasrespaldomod" + fila + "").val('0');
            eliminarCanalImpresoraEstacion(codigo, idestacion);
        } else {
            fn_guardaImpresorarespaldoMod(codigo, fila, idestacion);
        }
    });

    $("#selpuertosMod" + fila + "").change(function () {
        if ($("#selpuertosMod" + fila + "").val() == 0) {
            $("#selimpresorasrespaldomod" + fila + "").val('0');
            return false;
        } else {
            accion = 8;
            Cod_Puerto = $("#selpuertosMod" + fila + "").val();
            send = {"validaPuertosMod": 1};
            send.accion = accion;
            send.restaurante = $("#selrest").val();
            send.idestacion = idestacion;
            if ($("#selpuertosMod" + fila + "").val() != 0) {
                send.puerto = Cod_Puerto;
            } else {
                send.puerto = '';
            }
            $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
                if (datos.str == 1) {
                    if (datos[0]['pto_id'] == 1) {
                        alertify.error("El puerto ya se encuentra configurado, elija otro!!.");
                        $("#selpuertosMod" + fila + "").val(0);
                        return false;
                    } else {
                        fn_guardaImpresoraPuertoMod(codigo, fila, idestacion);
                    }
                }
            });
        }
    });

    $("#selimpresorasrespaldomod" + fila + "").change(function () {
        if ($("#selimpresorasmod" + fila + "").val() == 0) {
            alertify.error("Seleccione Impresora!!");
            $("#selimpresorasrespaldomod" + fila + "").val('0');
            return false;
        }
        if ($("#selpuertosMod" + fila + "").val() == 0) {
            alertify.error("Seleccione Puerto!!");
            $("#selimpresorasrespaldomod" + fila + "").val('0');
            return false;
        } else {
            fn_guardaImpresorarespaldoMod(codigo, fila, idestacion);
        }
    });
}
/*=======================================================================*/
/*FUNCION PARA GUARDAR IMPRESORA, PUERTO CUANDO MODIFICAMOS              */
/*=======================================================================*/
function fn_guardaImpresoraPuertoMod(codigo, fila, idestacion) {
    Cod_CanalImpresion = codigo;

    Cod_Impresora = $("#selimpresorasmod" + fila + "").val();
    Cod_Puerto = $("#selpuertosMod" + fila + "").val();
    Cod_ImpresoraRespaldo = $("#selimpresorasrespaldomod" + fila + "").val();

    accion = 4;
    send = {"guardaConfiguracionImpresionMod": 1};
    send.accion = accion;
    send.restaurante = $("#selrest").val();
    send.canalimpresion = Cod_CanalImpresion;
    send.impresora = Cod_Impresora;
    send.idestacion = idestacion;
    //send.impresorarespaldo=Cod_ImpresoraRespaldo;

    if ($("#selpuertosMod" + fila + "").val() == 0) {
        send.puerto = '';
    } else {
        send.puerto = Cod_Puerto;
    }
    if ($("#selimpresorasrespaldomod" + fila + "").val() == 0) {
        send.impresorarespaldo = '';
    } else {
        send.impresorarespaldo = Cod_ImpresoraRespaldo;
    }
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
        //$("#btnsalir").addClass('btn-primary');
        alertify.success("Configuracion de impresion actualizada correctamente.");
    });
}
/*===========================================================*/
/*FUNCION PARA GUARDAR IMPRESORA DE RESPALDO EN MODIFICAR    */
/*===========================================================*/
function fn_guardaImpresorarespaldoMod(codigo, fila, idestacion) {
    Cod_CanalImpresion = codigo;
    Cod_Impresora = $("#selimpresorasmod" + fila + "").val();
    Cod_Puerto = $("#selpuertosMod" + fila + "").val();
    Cod_ImpresoraRespaldo = $("#selimpresorasrespaldomod" + fila + "").val();

    accion = 4;
    send = {"guardaConfiguracionImpresionMod": 1};
    send.accion = accion;
    send.restaurante = $("#selrest").val();
    send.canalimpresion = Cod_CanalImpresion;
    send.impresora = Cod_Impresora;
    //send.impresorarespaldo=Cod_ImpresoraRespaldo;
    send.idestacion = idestacion;
    if ($("#selpuertosMod" + fila + "").val() == 0) {
        send.puerto = '';
    } else {
        send.puerto = Cod_Puerto;
    }
    if ($("#selimpresorasrespaldoMod" + fila + "").val() == 0) {
        send.impresorarespaldo = '';
    } else {
        send.impresorarespaldo = Cod_ImpresoraRespaldo;
    }
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
        //$("#btnsalir").addClass('btn-primary');
        alertify.success("Impresora de respaldo actualizada correctamente.");
    });
}

//FUNCION PARA CARGAR SELECT DESASIGNAR EN ESTACION//
function fn_cargarSelectDesasignarEstacion() {
    $("#selDesasignarEstacion").html("");
    $('#selDesasignarEstacion').html("<option selected value='-1'>-----SELECCIONE-----</option>");
    html = "<option value='1'>Punto de Venta</option>";
    html += "<option value='0'>Administracion</option>";
    $("#selDesasignarEstacion").append(html);

    $("#selDesasignarEstacion").change(function () {
        lc_desasigna = $("#selDesasignarEstacion").val();
        $("#desasigna").val(lc_desasigna);
    });
}

// FUNCION QUE INSERTA COLECCION DE DATOS DESASIGNAR EN ESTACION O PUNTO DE VENTA
function fn_insertaDesasignarEstacion(id_estacion) {
    restaurante = $("#selrest").val();
    desasigna = $("#selDesasignarEstacion").val();
    send = {"guardaDesasignarEstacion": 1};
    send.accion = 'I';
    send.restaurante = restaurante;
    send.desasigna = desasigna;
    send.est_id = id_estacion;
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
    });
}

//FUNCION PARA CARGAR SELECT DESASIGNAR EN ESTACION (MODIFICAR)//
function fn_cargarSelectDesasignarEstacionMod() {
    restaurante = $("#selrest").val();
    est_id = $("#cod_estacion").val();

    send = {"cargaDesasignarEstacion": 1};
    send.accion = 1;
    send.restaurante = restaurante;
    send.est_id = est_id;
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
        if (datos.str > 0) {
            bandera_desasignarEstacion = datos.variableB;
            $("#selDesasignarEstacionMod").html("");
            $('#selDesasignarEstacionMod').html("<option selected value='-1'>-----SELECCIONE-----</option>");
            html_mod = "<option value='1'>Punto de Venta</option>";
            html_mod += "<option value='0'>Administracion</option>";
            $("#selDesasignarEstacionMod").append(html_mod);
            $("#selDesasignarEstacionMod").change(function () {
                lc_desasignamod = $("#selDesasignarEstacionMod").val();
                $("#desasigna").val(lc_desasignamod);
            });
            $("#selDesasignarEstacionMod").val(bandera_desasignarEstacion);
        } else {
            $("#selDesasignarEstacionMod").html("");
            $('#selDesasignarEstacionMod').html("<option selected value='-1'>-----SELECCIONE-----</option>");
            html = "<option value='1'>Punto de Venta</option>";
            html += "<option value='0'>Administración</option>";
            $("#selDesasignarEstacionMod").append(html);
            $("#selDesasignarEstacionMod").change(function () {
                lc_desasignamod = $("#selDesasignarEstacionMod").val();
                $("#desasigna").val(lc_desasignamod);
            });
        }
    });
}

// FUNCION QUE MODIFICA COLECCION DE DATOS DESASIGNAR EN ESTACION O PUNTO DE VENTA
function fn_insertaDesasignarEstacionMod() {
    restaurante = $("#selrest").val();
    desasigna = $("#selDesasignarEstacionMod").val();
    est_id = $("#cod_estacion").val();
    send = {"modificaDesasignarEstacion": 1};
    send.accion = 'U';
    send.restaurante = restaurante;
    send.desasigna = desasigna;
    send.est_id = est_id;

    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
    });
}


//FUNCION PARA CARGAR SELECT TIPO ENVIO//
/*function fn_cargarSelectTipoEnvio() {
    $("#selTipoEnvio").html("");
    $('#selTipoEnvio').html("<option selected value='-1'>-----SELECCIONE-----</option>");
    html = "<option value='1'>SI</option>";
    html += "<option value='0'>NO</option>";
    $("#selTipoEnvio").append(html);
}*/

// FUNCION QUE INSERTA COLECCION DE DATOS TIPO ENVIO//
/*function fn_insertaTipoEnvio(id_estacion) {
    restaurante = $("#selrest").val();
    tipoEnvio = $('#selTipoEnvio').val();
    send = {"guardaTipoEnvio": 1};
    send.accion = 'I';
    send.restaurante = restaurante;
    send.tipoEnvio = tipoEnvio;
    send.est_id = id_estacion;
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
    });
}*/

//FUNCION PARA CARGAR SELECT TIPO ENVIO (MODIFICAR)//
/*function fn_cargarSelectTipoEnvioMod() {
    restaurante = $("#selrest").val();
     est_id = $("#cod_estacion").val();
     
     send = {"cargaTipoEnvio": 1};
     send.accion = 1;
     send.restaurante = restaurante;
     send.est_id = est_id;
     $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
     if (datos.str > 0) {
     bandera_tipoEnvio = datos.variableI;
     $("#selTipoEnvioMod").html("");
     //$('#selTipoEnvioMod').html("<option selected value='-1'>-----SELECCIONE-----</option>");
     html = "<option value='1'>SI</option>";
     html += "<option value='0'>NO</option>";
     $("#selTipoEnvioMod").append(html);
     $("#selTipoEnvioMod").val(bandera_tipoEnvio);
     } else {
     $("#selTipoEnvioMod").html("");
     $('#selTipoEnvioMod').html("<option selected value='-1'>-----SELECCIONE-----</option>");
     html = "<option value='1'>SI</option>";
     html += "<option value='0'>NO</option>";
     $("#selTipoEnvioMod").append(html);
     }
     });
}*/

// FUNCION QUE MODIFICA COLECCION DE DATOS TIPO ENVIO
/*function fn_insertaTipoEnvioMod() {
    restaurante = $("#selrest").val();
    tipoEnvio = $("#selTipoEnvioMod").val();
    est_id = $("#cod_estacion").val();
    send = {"modificaTipoEnvio": 1};
    send.accion = 'U';
    send.restaurante = restaurante;
    send.tipoEnvio = tipoEnvio;
    send.est_id = est_id;
     
    $.getJSON("../adminestacion/config_adminestacion.php", send, function (datos) {
    });
     
}*/

function eliminarCanalImpresoraEstacion(IDCanalImpresion, IDEstacion) {
    send = {"eliminarCanalImpresoraEstacion": 1};
    send.IDCanalImpresion = IDCanalImpresion;
    send.IDEstacion = IDEstacion;
    $.ajax({async: false, type: "POST", dataType: 'json', "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            alertify.success("Impresora desasignada correctamente.");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function consultaColeccionMesa(accion, idEstacion) {
    html = "<option value='-1'>-----SELECCIONE-----</option>";
    send = {"consultaColeccionMesa": 1};
    send.idRestaurante = $('#selrest option:selected').val();
    send.idEstacion = idEstacion;
    $.ajax({async: false, type: "POST", dataType: 'json', "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            var mesaActual = '-1';
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += "<option value='" + datos[i]['idMesa'] + "'>" + datos[i]['descripcion'] + "</option>";
                    if (datos[i]['mesaActual'] === '1') {
                        mesaActual = datos[i]['idMesa'];
                    }
                }
            } else {
                alertify.error("No hay mesas disponibles para este local");
            }
            $("#sel" + accion + "Mesa").html(html);
            $("#sel" + accion + "Mesa").val(mesaActual);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}