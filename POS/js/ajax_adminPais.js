/*********************************************************
 *          DESARROLLADO POR: Alex Merino                *
 *          DESCRIPCION: JS de pais                      *
 *          FECHA CREACION: 06/06/2018                   *
 *********************************************************/

var Accion = 0;
var lc_paginas = -1; //para el paginador nunca se va a encerar a menos quer se actualice la pantalla
var acc_resultado = 0;
var acc_std = 0;
var reg_max = 0;
var reg_pres = 10;

var lc_IDColeccionPais = '';
var lc_IDColeccionDeDatosPais = '';
var lc_mdl_id = '';

/*MODIFICAR COLECCION*/
var lc_nombrePColeccion = '';
var lc_IDColeccionPais_edit = '';
var lc_IDColeccionDeDatosPais_edit = '';

$(document).ready(function () {
    $("#txtcontabilidad").bootstrapSwitch('state', true);
    fn_esconderDiv();
    fn_btn("agregar", 1);
    fn_configuracion();
    fn_Pais();
    $('table_politicas').treetable({expandable: true});
});

/**
 * Funcion que obtiene el valor de configuracion de la cadena
 * @returns {undefined}
 */
function fn_configuracion() {
    send = {"infConfiguracionPais": 1};
    $.ajax({async: false, type: "GET", dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminPais/configadminPais.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#idPais").val(datos.pais_id);
                $("#idEmpresa").val(datos.emp_id);
                $("#namePais").val(datos.pais_descripcion);
            }
            //Eliminamos la imagen de carga  
            $("#load").empty();
        }
    });
}

/**
 * Funcion que permite cargar la data de PaisColeccionDatos
 * @param {type} idPais
 * @returns {undefined}
 */
function fn_cargarPaisColeccionDatos(idPais) {
    var html = '';
    send = {"CargarColeccionPais": 1};
    send.id_pais = idPais;
    send.id_emp = $("#idEmpresa").val();
    $.getJSON("../adminPais/configadminPais.php", send, function (datos) {
        if (datos.str > 0) {
            $("#detalle_pais_coleccion").show();
            html += "<thead><tr class='active'>";
            html += "<th align='center' style='width:170px; text-align:center;'>Descripci&oacute;n</th>";
            html += "<th align='center' style='width:170px; text-align:center;'>Dato</th>";
            html += "<th align='center' style='width:170px; text-align:center;'>Valor</th>";
            html += "<th align='center' style='width:170px; text-align:center;'>Tipo de Dato</th>";
            html += "<th align='center' style='width:170px; text-align:center;'>Especifica Valor</th>";
            html += "<th align='center' style='width:170px; text-align:center;'>Obligatorio</th>";
            html += "<th align='center' style='width:170px; text-align:center;'>Activo</th>";
            html += "</tr></thead>";
            //html += "<tbody><tr data-tt-id=\"1\"><td colspan=\"7\">Comentario</td></tr><tr data-tt-id=\"1.1\" data-tt-parent-id=\"1\"><td>asdsasss</td><td>7867</td></tr>";
            html += "<tbody>";
            for (i = 0; i < datos.str; i++) {
                var valorPolitica = evaluarValorPolitica(datos[i]);
                html += '<tr id="ecol' + i + '" onclick="fn_seleccionarPColeccion(' + i + ',\'' + datos[i]['IDColeccionPais'] + '\', \'' + datos[i]['descripcion_coleccion'] + '\', \'' + datos[i]['IDColeccionDeDatosPais'] + '\', \'' + datos[i]['mdl_id'] + '\')" ondblclick="fn_editColeccionPais()"   class="text-center"><td>' + datos[i]['descripcion_coleccion'] + '</td><td>' + datos[i]['descripcion_dato'] + '</td><td>' + valorPolitica + '</td><td>' + datos[i]['tipodedato'] + '</td>';
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

                if (datos[i]['isActive'] === 1) {
                    html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                } else {
                    html += '<td><input type="checkbox" value="1" disabled/></td>';
                }
                html += '</tr>';

            }
            html += '</tbody>';
            $("#pais_coleccion").html(html);

            $('#pais_coleccion').dataTable(
                    {//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
                        'destroy': true
                    }
            );
            $("#pais_coleccion_length").hide();
            $("#pais_coleccion_paginate").addClass('col-xs-10');
            $("#pais_coleccion_info").addClass('col-xs-10');
            $("#pais_coleccion_length").addClass('col-xs-6');

            //$("#pais_coleccion").html(html);  
        } else {
            html = "<thead><tr class='active'>";
            html += "<th align='center' style='width:170px; text-align:center;'>Descripci&oacute;n</th>";
            html += "<th align='center' style='width:170px; text-align:center;'>Dato</th>";
            html += "<th align='center' style='width:170px; text-align:center;'>Valor</th>";
            html += "<th align='center' style='width:170px; text-align:center;'>Tipo de Dato</th>";
            html += "<th align='center' style='width:170px; text-align:center;'>Especifica Valor</th>";
            html += "<th align='center' style='width:170px; text-align:center;'>Obligatorio</th>";
            html += "<th align='center' style='width:170px; text-align:center;'>Activo</th>";
            html += "<tr><td colspan='7'> No existen datos para visualizar.</td></tr>";
            $("#pais_coleccion").html(html);
        }
    }).error(function (jqXHR, textStatus, errorThrown) {
        alertify.alert(textStatus);
    });
}

/**
 * Funcion que valida la politica
 * @param {type} fila
 * @returns {String}
 */
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

/**
 * Funcion que valida la fila seleccionada
 
 * @param {type} filaA
 * @param {type} IDColeccion
 * @param {type} nombreColeccion
 * @param {type} IDColecciondeDatos
 * @param {type} mdlId 
 * @returns {undefined} */
function fn_seleccionarPColeccion(filaA, IDColeccion, nombreColeccion, IDColecciondeDatos, mdlId) {
    $("#pais_coleccion tr").removeClass("success");
    $("#ecol" + filaA + "").addClass("success");
    lc_nombrePColeccion = nombreColeccion;
    lc_IDColeccionPais_edit = IDColeccion;
    lc_IDColeccionDeDatosPais_edit = IDColecciondeDatos;
    lc_mdl_id = mdlId;
}



/**
 * Funcion que permite cargar la informacion del pais 
 * @returns {undefined}
 */
function fn_Pais() {
    $('#load').html('<div style="text-align: center"><img src="../../imagenes/ajax-loader.gif"/></div>');
    send = {"lstPais": 1};
    send.idPais = $("#idPais").val();
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminPais/configadminPais.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#tabla_pais").show();
                html = "<thead><tr class='active'>";
                html += "<th align='center' style='width:170px; text-align:center;'>Descripción</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Simbolo</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Moneda</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Base Factura</th>";
                html += "</tr></thead>";
                
                for (var i = 0; i < datos.str; i++) {

                    var descripcionPais = (datos[i]['pais_descripcion']);

                    html += '<tr class="tabla_detalleMov" id="' + i + '" onclick="fn_seleccionclick(' + i + ')" ondblclick="fn_seleccionModificar(\'' + datos[i]["pais_id"] + '\',\'' + descripcionPais + '\')">';
                    html += "<td align='center'  style='width:160px;'>" + datos[i]['pais_descripcion'] + "&nbsp;</td>";
                    html += "<td align='center'  style='width:170px;'>" + datos[i]['pais_moneda_simbolo'] + "&nbsp;</td>";
                    html += "<td align='center'  style='width:160px;'>" + datos[i]['pais_desc_modeda'] + "&nbsp;</td>";
                    html += "<td align='center'  style='width:180px;'>" + datos[i]['pais_base_factura'] + "&nbsp;</td>";
                    html += '</tr>';

                    $("#detalle_pais").html(html);
                }

                //PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
                $('#detalle_pais').dataTable({
                    'destroy': true
                });
                $("#detalle_pais_length").hide();
                $("#detalle_pais_paginate").addClass('col-xs-10');
                $("#detalle_pais_info").addClass('col-xs-10');
                $("#detalle_pais_length").addClass('col-xs-6');
            } else {
                $("#tabla_pais").show();
                html = "<thead><tr class='active'>";
                html += "<th align='center' style='width:170px; text-align:center;'>Descripción</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Simbolo</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Moneda</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Base Factura</th>";
                html += "<tr><td colspan='5'> No existen datos para visualizar.</td></tr>";
                $("#detalle_pais").html(html);

                alertify.error("No existen datos");
            }
            //Eliminamos la imagen de carga       
            $("#load").empty();
        }
    });
}

/**
 * Funcion que permite pintar una fila de la tabla al ser seleccionada
 * @param {type} fila
 * @returns {undefined}
 */
function fn_seleccionclick(fila) {
    $("#detalle_pais tr").removeClass("success");
    $("#" + fila + "").addClass("success");
}


/**
 * Funcion para modificar los elementos de la tabla al hacer doble click mendiante el id coleccioncadena
 * @param {type} idPais   
 * @param {type} nombreDescripcion   
 * @returns {undefined}
 */
function fn_seleccionModificar(idPais, nombreDescripcion) {
    Accion = 2;
    $('#myTabs a:first').tab('show');
    $('#titulomodalModificar').text("Modificar " + nombreDescripcion);
    $('#ModalModificar').modal('show');
    fn_cargarPais(idPais);
    fn_cargarPaisColeccionDatos(idPais);
}


/**
 * Funcion que carga la data en el modal de pais seleccionado
 * @param {type} idPais   
 * @returns {undefined}
 */
function fn_cargarPais(idPais) {
    send = {"infPais": 1};
    send.idPais = idPais;
    $.getJSON("../adminPais/configadminPais.php", send, function (datos) {
        if (datos.str > 0)
        {
            //Se llena los campos del modal con la data obtenida en la consulta
            $("#txtidpais").val(datos.pais_id);
            $("#txtSimbolo").val(datos.pais_moneda_simbolo);
            $("#txtMoneda").val(datos.pais_desc_modeda);
            $("#txtBaseFactura").val(datos.pais_base_factura);
        }
    });
}

/**
 * Funcion que edita los datos de la primera tabla de la colección
 
 * @returns {undefined} */
function fn_editColeccionPais() {
    var IDColeccion = $('#pais_coleccion').find("tr.success").attr("id");
    if (IDColeccion) {
        var empresa = $("#idEmpresa").val();
        $('#mdl_editPColeccion').modal('show');
        $('#ModalModificar').modal('hide');
        $('#edit_nombreColeccionP').text(lc_nombrePColeccion);

        $('#tipo_fecha_editP').daterangepicker({
            minDate: moment(),
            singleDatePicker: true,
            format: 'DD/MM/YYYY HH:mm',
            drops: 'up'
        }, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });

        $('#FechaInicial_editP').daterangepicker({
            minDate: moment(),
            singleDatePicker: true,
            format: 'DD/MM/YYYY HH:mm',
            drops: 'up'
        }, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });

        $('#FechaFinal_editP').daterangepicker({
            singleDatePicker: true,
            format: 'DD/MM/YYYY HH:mm',
            drops: 'up'
        }, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });

        $("#tipo_bit_editP").bootstrapSwitch('state', true);

        send = {"ListarColeccionxPais": 1};
        send.accion = 2;
        send.empresa = empresa;
        send.lc_IDColeccionPais_edit = lc_IDColeccionPais_edit;
        send.lc_IDColeccionDeDatosPais_edit = lc_IDColeccionDeDatosPais_edit;
        $.getJSON("../adminPais/configadminPais.php", send, function (datos) {
            if (datos.str > 0) {
                if (datos[0].estado === 1) {
                    $("#check_estadoP").prop('checked', true);
                } else {
                    $("#check_estadoP").prop('checked', false);
                }

                if (datos[0].especificarValor === 1) {
                    $("#edit_check_especificaP").prop('checked', true);
                } else {
                    $("#edit_check_especificaP").prop('checked', false);
                }

                if (datos[0].obligatorio === 1) {
                    $("#edit_check_obligatorioP").prop('checked', true);
                } else {
                    $("#edit_check_obligatorioP").prop('checked', false);
                }

                $('#edit_lbl_tipoDatoP').text(datos[0]['tipodedato']);
                $('#tipo_varchar_editP').val(datos[0]['caracter']);
                $("#tipo_entero_editP").val(datos[0]['entero']);
                $('#tipo_fecha_editP').val(datos[0]['fecha']);
                $('#tipo_numerico_editP').val(datos[0]['numerico']);
                $('#FechaInicial_editP').val(datos[0]['fechaInicio']);
                $('#FechaFinal_editP').val(datos[0]['fechaFin']);
                $('#rango_minimo_editP').val(datos[0]['minimo']);
                $('#rango_maximo_editP').val(datos[0]['maximo']);

                if (datos[0]['seleccion'] === 1) {
                    $("#tipo_bit_editP").bootstrapSwitch('state', true);
                } else {
                    $("#tipo_bit_editP").bootstrapSwitch('state', false);
                }
            }
        });
    } else {
        alertify.error("Debe Seleccionar un Registro.");
    }
}


/**
 * Funcion que permite modificar los datos de la tabla de paiscolecciondedatos
 
 * @returns {undefined} */
function fn_modificarPaisColeccion() {

    var tipo_enteroP = 0;
    var tipo_numericoP = 0;
    var rango_minimoP = 0;
    var rango_maximoP = 0;
    if ($("#tipo_entero_editP").val() === '') {
        tipo_enteroP = 'NULL';
    } else {
        tipo_enteroP = $("#tipo_entero_editP").val();
    }

    seleccionP = $("#tipo_bit_editP").bootstrapSwitch('state');
    if (seleccionP === true) {
        var tipo_bit_editP = 1;
    } else {
        var tipo_bit_editP = 0;
    }

    if ($("#tipo_numerico_editP").val() === '') {
        tipo_numericoP = 'NULL';
    } else {
        tipo_numericoP = $("#tipo_numerico_editP").val();
    }

    if ($("#rango_minimo_editP").val() === '') {
        rango_minimoP = 'NULL';
    } else {
        rango_minimoP = $("#rango_minimo_editP").val();
    }

    if ($("#rango_maximo_editP").val() === '') {
        rango_maximoP = 'NULL';
    } else {
        rango_maximoP = $("#rango_maximo_editP").val();
    }

    send = {"modificarPaisColeccion": 1};
    send.accion = 2;
    send.lc_IDColeccionDeDatosPais_edit = lc_IDColeccionDeDatosPais_edit;
    send.lc_IDColeccionPais_edit = lc_IDColeccionPais_edit;
    send.varchar = $("#tipo_varchar_editP").val();
    send.entero = tipo_enteroP;
    send.fecha = $("#tipo_fecha_editP").val();
    send.seleccion = tipo_bit_editP;
    send.numerico = tipo_numericoP;
    send.fecha_inicio = $("#FechaInicial_editP").val();
    send.fecha_fin = $("#FechaFinal_editP").val();
    send.minimo = rango_minimoP;
    send.maximo = rango_maximoP;
    send.IDUsuario = $("#idUser").val();
    if ($("#check_estadoP").is(':checked')) {
        send.estado = 1;
    } else {
        send.estado = 0;
    }
    $.getJSON("../adminPais/configadminPais.php", send, function (datos) {
        if (datos.str > 0) {
            alertify.success("Datos actualizados correctamente.");
            var idEmpresa = $("#idEmpresa").val();
            fn_cargarPaisColeccionDatos(idEmpresa);
            $('#mdl_editPColeccion').modal('hide');
            $('#ModalModificar').modal('show');

        } else {
            var error = datos.str;
            error = error.substr(54, 40);
            alertify.error(error);
        }
    });
}


/**
 * Funcion para switch de politicas de pais
 * @returns {undefined}
 */
function fn_nuevaPaisColeccion() {
    $("#tipos_de_datoP").hide();
    $('#mdl_nuevaPColeccion').modal('show');
    $('#ModalModificar').modal('hide');
    $('#txtObservacion').empty();
    $('#coleccion_datosP').empty();
    fn_DetallePColeccion();
    fn_limpiarcamposPais();
}

/**
 * Limpiar detalle de campos ingresados 
 
 * @returns {undefined} */
function fn_limpiarcamposPais() {
    $('#tipo_varcharP').val('');
    $('#tipo_enteroP').val('');
    $('#tipo_fechaP').val('');
    $('#tipo_numericoP').val('');
    $('#FechaInicialP').val('');
    $('#FechaFinalP').val('');
    $('#rango_minimoP').val('');
    $('#rango_maximoP').val('');
}

/**
 * Funcion que consulta las politicas del pais
 * @returns {undefined}
 */
function fn_DetallePColeccion() {
    var html = '<thead><tr class=\'active\'><th class="text-center">Descripci&oacute;n</th></tr></thead>';
    send = {"detalleColeccionPais": 1};
    send.accion = 3;
    send.id_emp = $("#idEmpresa").val();
    $.getJSON("../adminPais/configadminPais.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += '<tr id="' + i + 'detalleP_id' + '" onclick="fn_seleccionarDetallePColeccion(' + i + ',\'' + datos[i]['IDColeccionPais'] + '\',\'' + escape(datos[i]['Descripcion']) + '\',\'' + escape(datos[i]['Observaciones']) + '\')"  class="text-left"><td>' + datos[i]['Descripcion'] + '</td></tr>';
            }
            $("#coleccion_descripcionP").html(html);
            $('#coleccion_descripcionP').dataTable(
                    {//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
                        'destroy': true,
                        'paging': false,
                        'ordering': false,
                        'info': false
                    }
            );
        } else {
            html = html + '<tr><th colspan="6" class="text-center">No existen registros.</th></tr>';
            $("#coleccion_descripcionP").html(html);
        }
    });
}

/**
 * Funcion que detalla las politicas consultadas
 
 * @param {type} filaB
 * @param {type} IDDetalleColeccion
 * @param {type} descripcion
 * @returns {undefined} */
function fn_seleccionarDetallePColeccion(filaB, IDDetalleColeccion, descripcion, observacion) {
    $("#coleccion_descripcionP tr").removeClass("success");
    $("#" + filaB + 'detalleP_id' + "").addClass("success");
    fn_DatosColeccionP(IDDetalleColeccion);
    $('#nombreColeccionP').text(unescape(descripcion));
    $('#tipos_de_datoP').hide();
    $('#txtObservacion').html("<div class=\"alert alert-info\" role=\"alert\">Descripción: " + unescape(observacion) + "</div>");
}

/**
 * Funcion que consulta las definiciones de las politicas 
 * @param {type} IDDetalleColeccion
 * @returns {undefined} */
function fn_DatosColeccionP(IDDetalleColeccion) {
    var empresa = $("#idEmpresa").val();
    var html = '<thead><tr class=\'active\'><th class="text-center">Datos</th></tr></thead>';
    send = {"datosColeccionPais": 1};
    send.accion = 4;
    send.empresa = empresa;
    send.IDColeccionPais = IDDetalleColeccion;
    $.getJSON("../adminPais/configadminPais.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += '<tr id="' + i + 'datosPais_id' + '" onclick="fn_seleccionarDatosPColeccion(' + i + ',\'' + datos[i]['IDColeccionDeDatosPais'] + '\',' + datos[i]['especificarValor'] + ',' + datos[i]['obligatorio'] + ',\'' + datos[i]['tipodedato'] + '\')" class="text-left"><td>' + datos[i]['datos'] + '</td></tr>';
            }
            $("#coleccion_datosP").html(html);
            $("#coleccion_datosP").html(html);
            $('#coleccion_datosP').dataTable(
                    {//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
                        'destroy': true,
                        'paging': false,
                        'ordering': false,
                        'info': false
                    }
            );
        } else {
            html = html + '<tr><th colspan="6" class="text-center">No existen registros.</th></tr>';
            $("#coleccion_datosP").html(html);
            $("#tipos_de_datoP").hide();
        }
    });

    lc_IDColeccionPais = IDDetalleColeccion;
}


/**
 * Funcion que habilita las tablas de definiciones de politicas
 
 * @param {type} filaC
 * @param {type} IDDatosColeccion
 * @param {type} especifica
 * @param {type} obligatorio
 * @param {type} tipo
 * @returns {undefined} */
function fn_seleccionarDatosPColeccion(filaC, IDDatosColeccion, especifica, obligatorio, tipo) {
    $("#coleccion_datosP tr").removeClass("success");
    $("#" + filaC + 'datosPais_id' + "").addClass("success");
    $("#tipos_de_datoP").show();

    if (especifica === 1) {
        $("#check_especificaP").prop("checked", true);
    } else {
        $("#check_especificaP").prop("checked", false);
    }

    if (obligatorio === 1) {
        $("#check_obligatorioP").prop("checked", true);
    } else {
        $("#check_obligatorioP").prop("checked", false);
    }

    $("#lbl_tipoDatoP").text(tipo);

    $('#tipo_fechaP').daterangepicker({
        minDate: moment(),
        singleDatePicker: true,
        format: 'DD/MM/YYYY HH:mm',
        drops: 'up'
    }, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $('#FechaInicialP').daterangepicker({
        minDate: moment(),
        singleDatePicker: true,
        format: 'DD/MM/YYYY HH:mm',
        drops: 'up'
    }, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $('#FechaFinalP').daterangepicker({
        singleDatePicker: true,
        format: 'DD/MM/YYYY HH:mm',
        drops: 'up'
    }, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $("#tipo_bitP").bootstrapSwitch('state', true);

    lc_IDColeccionDeDatosPais = IDDatosColeccion;
}



function fn_verModalP() {
    $('#mdl_editPColeccion').modal('hide');
    $('#mdl_nuevaPColeccion').modal('hide');
    $('#ModalModificar').modal('show');

}

function fn_insertarPColeccion() {
    var empresa = $("#IDEmpresa").val();
    var tipo_entero = 0;
    var tipo_numerico = 0;
    var rango_minimo = 0;
    var rango_maximo = 0;

    if ($("#tipo_enteroP").val() === '') {
        tipo_entero = 'null';
    } else {
        tipo_entero = $("#tipo_enteroP").val();
    }

    estado = $("#tipo_bitP").bootstrapSwitch('state');
    if (estado === true) {
        var tipo_bit = 1;
    } else {
        var tipo_bit = 0;
    }

    if ($("#tipo_numericoP").val() === '') {
        tipo_numerico = 'null';
    } else {
        tipo_numerico = $("#tipo_numericoP").val();
    }

    if ($("#rango_minimoP").val() === '') {
        rango_minimo = 'null';
    } else {
        rango_minimo = $("#rango_minimoP").val();
    }

    if ($("#rango_maximoP").val() === '') {
        rango_maximo = 'null';
    } else {
        rango_maximo = $("#rango_maximoP").val();
    }

    send = {"insertarPaisColeccion": 1};
    send.accion = 1;
    send.IDColecciondeDatosPais = lc_IDColeccionDeDatosPais;
    send.IDColeccionPais = lc_IDColeccionPais;
    send.varchar = $("#tipo_varcharP").val();
    send.entero = tipo_entero;
    send.fecha = $("#tipo_fechaP").val();
    send.seleccion = tipo_bit;
    send.numerico = tipo_numerico;
    send.fecha_inicio = $("#FechaInicialP").val();
    send.fecha_fin = $("#FechaFinalP").val();
    send.minimo = rango_minimo;
    send.maximo = rango_maximo;
    send.IDUsuario = $("#idUser").val();
    $.getJSON("../adminPais/configadminPais.php", send, function (datos) {
        if (datos.str > 0) {
            alertify.success("Datos guardados correctamente.");
            fn_cargarPaisColeccionDatos(empresa);
            $('#mdl_nuevaPColeccion').modal('hide');
            $('#ModalModificar').modal('show');
        } else {
            var error = datos.str;
            error = error.substr(54, 40);
            alertify.error(error);
        }
    });
}

function fn_limpiarcamposEmpresa() {
    $('#tipo_varcharE').val('');
    $('#tipo_enteroE').val('');
    $('#tipo_fechaE').val('');
    $('#tipo_numericoE').val('');
    $('#FechaInicialE').val('');
    $('#FechaFinalE').val('');
    $('#rango_minimoE').val('');
    $('#rango_maximoE').val('');
}


/*
 * Funcion que permite validar los datos modificados o nuevos 
 * @param {type} Accion
 * @returns {undefined}
 */

function fn_guardarCambios(Accion) {
    if (Accion === 1) { //Nuevo        

    } else
    if (Accion === 2) { //Modificar         
        var idPais = $('#txtidpais').val();
        var simbolo = $('#txtSimbolo').val();
        var moneda = $('#txtMoneda').val();
        var base = $('#txtBaseFactura').val();
        if (simbolo.length === 0) {
            alertify.error("Ingrese el simbolo de la moneda", function () {
                $('#txtSimbolo').focus();
            });
            err = true;
        } else
        if (moneda.length === 0) {
            alertify.error("Ingrese la moneda del país", function () {
                $('#txtMoneda').focus();
            });
            err = true;
        } else if (base.length === 0) {
            alertify.error("Ingrese la base del país ", function () {
                $('#txtBaseFactura').focus();
            });
            err = true;
        } else {
            fn_modificarPais(idPais, simbolo, moneda, base);
            /* Cerramos la ventana modal*/
            $('#ModalModificar').modal('hide');
            /* En el segundo proceso actualiza la tabla para que se reflejen los cambios*/
            fn_Pais();
        }



    }
}


/**
 * Funcion para guardar los cambios en la tabla cadenacolecciondedatos 
 * @param {type} idPais
 * @param {type} simbolo
 * @param {type} moneda
 * @param {type} base 
 * @returns {undefined}
 */
function fn_modificarPais(idPais, simbolo, moneda, base) {
    send = {"guardarDatosPais": 1};
    send.idPais = idPais;
    send.simbolo = simbolo;
    send.moneda = moneda;
    send.base = base;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminPais/configadminPais.php", data: send,
        success: function (datos) {
            alertify.success("Los datos han sido modificados satisfactoriamente.");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error("No se ha podido actualizar los elementos." + textStatus);
        }
    });
}

/*
 * Funcion principal para Pre visualizacion de pantallas
 * @param {type} accion
 * @returns {Boolean}
 */
function fn_accionar(accion) {
    if (accion === 'Nuevo') {
        //Se valida si la longitud de bines esta configurada
        if (longitudBines === 0) {
            alertify.error("La longitud de bines no ha sido configurada.");
        }
        //Se limpian los datos que han sido guardados    
        $('#option_new').prop('checked', true);
        $('#titulomodalNuevo').text('BINES');
        $("#txtidpolitica").val("");
        $("#definiciones").html("<select id='selDefiniciones' class='form-control'></select>");
        $("#divminimo").html("");
        $("#divmaximo").html("");
        $("#FormaPago").empty("");
        $('#botonesguardarcancelar').show();
        $('#botonessalir').hide();
        // Se crea los input   
        $("#divminimo").html("<input type='text' onkeydown=\"return validarNumeros(event)\" maxlength='" + longitudBines + "' style=\"text-align:left\" onblur=\"fn_validarBinInicial()\" class=\"form-control\" id=\"minimo\" />");
        $("#divmaximo").html("<input type='text' onkeydown=\"return validarNumeros(event)\" maxlength='" + longitudBines + "' style=\"text-align:left\" onblur=\"fn_validarBinFinal()\" class=\"form-control\" id=\"maximo\" />");
        //Color para los input
        $("#minimo").css({'border-color': '#d5d8dc'});
        $("#maximo").css({'border-color': '#d5d8dc'});
        //Se consulta los datos para mostrar las politicas //
        getPoliticas();
        //Se consulta las formas de pago//
        getFormaPago();
        ////////////Colocar foco en cualquier campo de la modal////////////////// 
        $('#ModalNuevo').on('shown.bs.modal', function () {
            $("#txtidpolitica").focus();
        });

        $('#ModalNuevo').modal('show');
        $('#ModalNuevo').modal('handleUpdate');
    } else if (accion === 'Grabar') {
    } else if (accion === 'Cancelar') {
    } else if (accion === 'Modificar') {
        lc_control = 1;
        lc_estacion = $("#cod_estacion").val();
        fn_cargarestacionModificada(lc_estacion);
        $('#ModalModificar').modal('show');
    }
}


/**
 * FUNCIONES PRINCIPALES PARA CADA PANTALLA A UTILIZAR
 * @returns {undefined}
 */


/*---------------------------------------------
 Esconder los divs
 -----------------------------------------------*/
function fn_esconderDiv() {
    $("#tabcontenedor").hide();
}


/*---------------------------------------------
 FUNCION PARA LIMPIAR EL BUSCADOR
 -----------------------------------------------*/
function fn_limpiaBuscador() {
    $("#buscar").val('');
    $("#img_remove").hide();
    $("#img_buscar").show();
}

/*-------------------------------------------------------
 Funcion para los botones de mantenimiento
 -------------------------------------------------------*/
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

/////////////////////////////MUESTRA FECHA ACTUAL /////////////////////////////////
function muestraFecha() {
    var today = new Date();
    var anio = today.getYear() + 1900;
    var mes = today.getMonth() + 1;

    var fechaHoy = mes + '/' + today.getDate() + '/' + anio;

    $("#txtfechaI").val(fechaHoy);
}

/*
 * FUNCIONES PARA VALIDAR CAMPOS DE NUMERICOS Y DE TEXTO
 */

/////////////////////////////VALIDACION DE NUMEROS/////////////////////////////////	
function validarNumeros(e)  // 1
{
    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla == 8)
        return true; // backspace
    if (tecla == 109)
        return true; // menos
    if (tecla == 110)
        return true; // punto
    if (tecla == 189)
        return true; // guion
    if (e.ctrlKey && tecla == 86) {
        return true
    }
    ; //Ctrl v
    if (e.ctrlKey && tecla == 67) {
        return true
    }
    ; //Ctrl c
    if (e.ctrlKey && tecla == 88) {
        return true
    }
    ; //Ctrl x
    if (tecla >= 96 && tecla <= 105) {
        return true;
    } //numpad

    patron = /[0-9]/; // patron

    te = String.fromCharCode(tecla);
    return patron.test(te); // prueba
}

/////////////////////////////VALIDACION DE LETRAS/////////////////////////////////
function soloLetras(e)
{
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = " �����abcdefghijklmn�opqrstuvwxyz";
    especiales = [8, 37, 39, 46];

    tecla_especial = false
    for (var i in especiales)
    {
        if (key == especiales[i])
        {
            tecla_especial = true;
            break;
        }
    }

    if (letras.indexOf(tecla) == -1 && !tecla_especial)
    {
        return false;
    }
}

