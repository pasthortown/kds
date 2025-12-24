/*********************************************************
 *          DESARROLLADO POR: Alex Merino                *
 *          DESCRIPCION: JS de empresa                   *
 *          FECHA CREACION: 14/04/2018                   *
 *********************************************************/

var Accion = 0;
var lc_paginas = -1; //para el paginador nunca se va a encerar a menos quer se actualice la pantalla
var acc_resultado = 0;
var acc_std = 0;
var reg_max = 0;
var reg_pres = 10;
var indice = "N/A";
var arrayDatos = new Array();
var txtTitulo = "";


var lc_IDColeccionEmpresa = '';
var lc_IDColeccionDeDatosEmpresa = '';

/*MODIFICAR COLECCION*/
var lc_nombreEColeccion = '';
var lc_IDColeccionEmpresa_edit = '';
var lc_IDColeccionDeDatosEmpresa_edit = '';

$(document).ready(function () {
    $("#txtcontabilidad").bootstrapSwitch('state', true);
    fn_esconderDiv();
    fn_btn("agregar", 1);
    //fn_cargarPais();
    fn_getPais();

    //Coleccion de datos para los input Date     

    $('#txtfechRes').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    }); //fechRes

    $('#fechRes').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

});

/**
 * Funcion que permite obtener la informacion del pais automaticamente desde la variable de session de id cadena
 * @returns {undefined}
 */
function fn_getPais() {
    //Añadimos la imagen de carga en el contenedor
    $('#load').html('<div style="text-align: center"><img src="../../imagenes/ajax-loader.gif"/></div>');
    send = {"infPais": 1};
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminEmpresa/configadminEmpresa.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                //Seteamos el id del pais
                $("#idPais").val(datos.pais_id);
                //Seteamos el nombre del pais
                $("#namePais").val(datos.pais_descripcion);
                //Llenamos la informacion de la tabla
                fn_cargarEmpresas(datos.pais_id);

            }
        }
    });

}

/**
 * Funcion que permite mostrar las empresas una vez que se haya seleccionado el pais
 * @returns {undefined}
 */
function fn_cargarPais() {
    send = {"infPais": 1};
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminEmpresa/configadminEmpresa.php", data: send,
        success: function (datos) {
            if (datos.str === 0) {
                alertify.alert("No existen datos para este Pais");
            } else if (datos.str > 0) {
                //$("#divrestaurante").show();  
                $("#selpais").html("");
                $('#selpais').html("<option selected value='0'>--------------  Seleccione pais  --------------</option>");
                for (var i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['pais_id'] + "'>" + datos[i]['pais_id'] + "-" + datos[i]['pais_descripcion'] + "</option>";
                    $("#selpais").append(html);
                }
                $("#selpais").chosen();
                //Opcion cuando se cambia algun elemento del combobox
                $("#selpais").change(function () {
                    //Seleccionamos el valor restaurante
                    pa_id = $("#selpais").val();
                    //Seleccionamos el texto del valor de un restaurante
                    txt_res = $("#selpais option:selected").html();
                    //Realizamos el split del texto seleccionado para capturar el nombre
                    txt_value_res = txt_res.split("-");
                    //Llama a la funcion que mostrara la tabla  
                    fn_cargarEmpresas(pa_id);
                    //Seteamos el nombre del combo seleccionado en el titulo del modal
                    $("#titulomodalNuevo").text(txt_res);
                    //Seteamos el id del restaurante seleccionado
                    $("#idPais").val(pa_id);
                    //Seteamos el nombre del restaurante
                    $("#namePais").val(txt_value_res[1]);
                });
            }
        }
    });
}

/**
 * Funcion que permite obtener el listado de empresas disponibles
 * @param {type} pa_id
 * @returns {undefined}
 */
function fn_cargarEmpresas(pa_id) {
    send = {"lstEmpresas": 1};
    send.pa_id = pa_id;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminEmpresa/configadminEmpresa.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#tabla_empresa").show();
                html = "<thead><tr class='active'>";
                if (pa_id > 1) {
                    html += "<th align='center' style='width:180px; text-align:center;'>Nit</th>";
                } else {
                    html += "<th align='center' style='width:180px; text-align:center;'>Ruc</th>";
                }
                html += "<th align='center' style='width:270px; text-align:center;'>Nombre</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Tipo de contribuyente</th>";
                html += "<th align='center' style='width:80px; text-align:center;'>Resolución</th>";
                html += "<th align='center' style='width:80px; text-align:center;'>Fecha Resolución</th>";
                html += "<th align='center' style='width:80px; text-align:center;'>Activado</th>";
                html += "</tr></thead>";
                for (var i = 0; i < datos.str; i++) {
                    html += "<tr class='tabla_detalleMov' id='" + i + "' style='cursor:pointer'";
                    html += "onclick=fn_seleccionclick(" + i + ") ondblclick=fn_seleccionModificar('" + datos[i]['emp_id'] + "','" + datos[i]['emp_nombre'].split(" ").join("&nbsp;") + "')>";
                    html += "<td align='center'  style='width:160px;'>" + datos[i]['emp_ruc'] + "&nbsp;</td>";
                    html += "<td align='center'  style='width:270px;'>" + datos[i]['emp_nombre'] + "&nbsp;</td>";
                    html += "<td align='center'  style='width:170px;'>" + datos[i]['emp_tipo_contribuyente'] + "&nbsp;</td>";
                    html += "<td align='center'  style='width:170px;'>" + datos[i]['emp_resolucion'] + "&nbsp;</td>";
                    //html += "<td align='center'  style='width:170px;'>" + datos[i]['emp_resolucion'] + "&nbsp;</td>";
                    html += "<td align='center'  style='width:170px;'>" + datos[i]['emp_fecha_resolucion'] + "&nbsp;</td>";
                    if (datos[i]['estado'].trim() === 'Activo') {
                        indice = datos[i]['IDAutorizacionRestaurante'];
                        html += "<td align='center'  style='width:80px' ><input type='checkbox' checked='checked' disabled /></td></tr>";
                    } else
                    if (datos[i]['estado'].trim() === 'Inactivo') {
                        html += "<td align='center'  style='width:80px' ><input type='checkbox' disabled /></td></tr>";
                    }

                    $("#detalle_empresa").html(html);
                }
                $('#detalle_empresa').dataTable(
                    {//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
                        'destroy': true
                    }
                );
                $("#detalle_empresa_length").hide();
                $("#detalle_empresa_paginate").addClass('col-xs-10');
                $("#detalle_empresa_info").addClass('col-xs-10');
                $("#detalle_empresa_length").addClass('col-xs-6');
            } else {
                $("#tabla_empresa").show();
                html = "<thead><tr class='active'>";
                if (pa_id > 1) {
                    html += "<th align='center' style='width:180px; text-align:center;'>Nit</th>";
                } else {
                    html += "<th align='center' style='width:180px; text-align:center;'>Ruc</th>";
                }
                html += "<th align='center' style='width:170px; text-align:center;'>Nombre</th>";
                html += "<th align='center' style='width:170px; text-align:center;'>Tipo de contribuyente</th>";
                html += "<th align='center' style='width:80px; text-align:center;'>Resolución</th>";
                html += "<th align='center' style='width:80px; text-align:center;'>Fecha Resolución</th>";
                html += "<th align='center' style='width:80px; text-align:center;'>Activado</th></tr></thead>";
                html += "<tr><td colspan='8'> No existen datos para visualizar.</td></tr>";
                $("#detalle_empresa").html(html);
                //alertify.alert("No existen estaciones configuradas para este restaurante");
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
    $("#detalle_empresa tr").removeClass("success");
    $("#" + fila + "").addClass("success");

}


/**
 * Funcion para modificar los elementos de la tabla al hacer doble click mendiante el id de empresa
 * @param {type} idEmpresa
 * @param {type} descripcionEmpresa
 * @returns {undefined}
 */
function fn_seleccionModificar(idEmpresa, descripcionEmpresa) {
    Accion = 2;
    $('#myTabs a:first').tab('show');
    $('#titulomodalModificar').text(descripcionEmpresa);
    $('#ModalModificar').modal('show');
    $("#IDEmpresa").val(idEmpresa);
    fn_cargarInfoEmpresa(idEmpresa);
    fn_cargarEmpresaColeccionDatos(idEmpresa);
}

function fn_cargarEmpresaColeccionDatos(idEmpresa) {
    var html = '<tr class="bg-primary"><th class="text-center col-md-5" style="width: 25%;">Descripci&oacute;n</th><th class="text-center col-md-5" >Dato</th><th class="text-center col-md-7">Especifica Valor</th><th>Obligatorio</th><th class="text-center col-md-5">Tipo de Dato</th><th class="text-center">Valor</th><th>Activo</th></tr>';
    send = {"CargarColeccionEmpresa": 1};
    send.id_Empresa = idEmpresa;
    $.getJSON("../adminEmpresa/configadminEmpresa.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var valorPolitica = evaluarValorPolitica(datos[i]);
                html += '<tr id="ecol' + i + '" onclick="fn_seleccionarEColeccion(' + i + ',\'' + datos[i]['ID_ColeccionEmpresa'] + '\', \'' + datos[i]['descripcion_coleccion'] + '\', \'' + datos[i]['ID_ColeccionDeDatosEmpresa'] + '\')"  class="text-center"><td>' + datos[i]['descripcion_coleccion'] + '</td><td>' + datos[i]['descripcion_dato'] + '</td>';
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
            $("#empresa_coleccion").html(html);
        } else {
            html = html + '<tr><th colspan="14" class="text-center">No existen registros.</th></tr>';
            $("#empresa_coleccion").html(html);
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

function fn_seleccionarEColeccion(filaA, IDColeccion, nombreColeccion, IDColecciondeDatos) {
    $("#empresa_coleccion tr").removeClass("success");
    $("#ecol" + filaA + "").addClass("success");
    lc_nombreEColeccion = nombreColeccion;
    lc_IDColeccionEmpresa_edit = IDColeccion;
    lc_IDColeccionDeDatosEmpresa_edit = IDColecciondeDatos;
}


/**
 * Funcion que carga la data en el modal de auto impresores para despues ser modificados
 * @param {type} idEmpresa
 * @returns {undefined}
 */
function fn_cargarInfoEmpresa(idEmpresa) {
    send = {"infEmpresa": 1};
    send.id_Empresa = idEmpresa;
    $.getJSON("../adminEmpresa/configadminEmpresa.php", send, function (datos) {
        if (datos.str > 0)
        {
            //Se valida si el dato estado de la BDD esta activo o inactivo
            if (datos.estado.trim() === 'Activo') {
                $('#opcion_Modificar').prop('checked', true);
            } else {
                $('#opcion_Modificar').prop('checked', false);
            }
            //Se valida si es diferente a pais Ecuador
            if (datos.pais_id > 1) {
                $("#divContabilidad").hide();
                $("#textTitulo").text("Nit:");
                $("#textTitulo").text("Nit:");
                $("#tipEmision").html("");
                $("#tipEmision").html(" <select id='seltipEmision' class='form-control' size='1' ></select>");
                $('#seltipEmision').append("<option selected value='NULL'> No aplica </option>");
                $("#tipAmbiente").html("");
                $("#tipAmbiente").html(" <select id='seltipAmbiente' class='form-control' size='1' ></select>");
                $('#seltipAmbiente').append("<option selected value='NULL'> No aplica  </option>");
            } else {
                //Mostrar div contabilidad
                $("#divContabilidad").show();
                //Llena los select tipo ambiente y tipo emisison
                $("#textTitulo").text("Ruc:");
                fn_getTipoAmbiente(datos.IDTipoAmbiente);
                fn_getTipoEmision(datos.tem_id);
            }
            //Se llena los campos del modal con la data obtenida en la consulta
            $("#idEmpresa").val(datos.emp_id);
            $("#txtruc").val(datos.emp_ruc);
            $("#txtnombre").val(datos.emp_nombre);
            $("#txtciudad").val(datos.emp_ciudad);
            $("#txtdireccion").val(datos.emp_direccion);
            $("#txtrazonSocial").val(datos.emp_razon_social);
            $("#txttelefono").val(datos.emp_fono);
            $("#txttipoContibuyente").val(datos.emp_tipo_contribuyente);
            $("#txtresolucion").val(datos.emp_resolucion);
            $("#txtfechRes").val(datos.fecha_resolucion);
            if (datos.emp_obligado_contabilidad.toUpperCase() === 'SI') {
                $("#txtcontabilidad").bootstrapSwitch('state', true);
            } else {
                $("#txtcontabilidad").bootstrapSwitch('state', false);
            }
        } else {
            alertify.error('Error en la carga de informacion de empresa');
        }
    });
}
/**
 * Funcion que permite obtener el tipo de ambiente
 * @param {type} id
 * @returns {undefined}
 */
function fn_getTipoAmbiente(id) {
    send = {"infTipoAmbiente": 1};
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminEmpresa/configadminEmpresa.php", data: send,
        success: function (datos) {
            if (datos.str === 0) {
                alertify.alert("No existen datos para tipo ambiente");
            } else if (datos.str > 0) {
                $("#tipAmbiente").html("");
                $("#tipAmbiente").html(" <select id='seltipAmbiente' class='form-control' size='" + (datos.str - 1) + "' ></select>");
                for (var i = 0; i < datos.str - 1; i++) {
                    if (id === datos[i]['IDTipoAmbiente']) {
                        html = "<option value='" + datos[i]['IDTipoAmbiente'] + "' selected>" + datos[i]['tam_descripcion'] + "</option>";
                    } else
                    if (datos[i]['tam_descripcion'].trim() !== 'NA') {
                        html = "<option value='" + datos[i]['IDTipoAmbiente'] + "'>" + datos[i]['tam_descripcion'] + "</option>";
                    }
                    $("#seltipAmbiente").append(html);
                }
            }
        }
    });

}

/*
 * Funcion que permite obtener el tipo de mision
 * @param {type} id
 * @returns {undefined}
 */
function fn_getTipoEmision(id) {
    send = {"infTipoEmision": 1};
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminEmpresa/configadminEmpresa.php", data: send,
        success: function (datos) {
            if (datos.str === 0) {
                alertify.alert("No existen datos para tipo emision");
            } else if (datos.str > 0) {
                $("#tipEmision").html("");
                $("#tipEmision").html(" <select id='seltipEmision' class='form-control' size='" + (datos.str - 1) + "' ></select>");
                for (var i = 0; i < datos.str - 1; i++) {
                    if (id === datos[i]['tem_id']) {
                        html = "<option value='" + datos[i]['tem_id'] + "' selected >" + datos[i]['tem_descripcion'] + "</option>";
                    } else
                    if (datos[i]['tem_descripcion'].trim() !== 'NA') {
                        html = "<option value='" + datos[i]['tem_id'] + "'>" + datos[i]['tem_descripcion'] + "</option>";
                    }
                    $("#seltipEmision").append(html);
                }
            }
        }
    });
}


/*
 * Funcion que permite validar los datos modificados o nuevos
 * @param {type} Accion
 * @returns {undefined}
 */

function fn_guardarCambios(Accion) {
    if (Accion === 1) { //Nuevo
        var opcion = $('#option_new').is(':checked');
        var idPais = $('#idPais').val();
        var idEmpresa = $('#empresaId').val();
        var ruc = $('#ruc').val();
        var nombre = $('#nombre').val();
        var ciudad = $('#ciudad').val();
        var direccion = $('#direccion').val();
        var razonSocial = $('#razonSocial').val();
        var telefono = $('#telefono').val();
        var tipoContrib = $('#tipoContibuyente').val();
        var resolucion = $('#resolucion').val();
        var fechRes = $('#fechRes').val();
        var bandera;
        if (opcion === true) {
            bandera = 1;
        } else {
            bandera = 2;
        }

        if (idEmpresa.length === 0) {
            alertify.error("Ingrese el id de la empresa", function () {
                $('#empresaId').focus();
            });
        } else
        if (ruc.length === 0) {
            alertify.error("Ingrese el ruc de la empresa", function () {
                $('#ruc').focus();
            });
        } else
        if (nombre.length === 0) {
            alertify.error("Ingrese el nombre de la empresa", function () {
                $('#nombre').focus();
            });
        } else
        if (ciudad.length === 0) {
            alertify.error("Ingrese la ciudad", function () {
                $('#ciudad').focus();
            });
        } else
        if (direccion.length === 0) {
            alertify.error("Ingrese la dirección", function () {
                $('#direccion').focus();
            });
        } else
        if (razonSocial.length === 0) {
            alertify.error("Ingrese la razón social", function () {
                $('#razonSocial').focus();
            });
        } else
        if (tipoContrib.length === 0) {
            alertify.error("Ingrese el tipo de contribuyente", function () {
                $('#tipoContibuyente').focus();
            });
        } else
        if (resolucion.length === 0) {
            alertify.error("Ingrese la resolución", function () {
                $('#resolucion').focus();
            });
        } else
        if (fechRes.length === 0) {
            alertify.error("Ingrese la fecha de resolución", function () {
                $('#fechRes').focus();
            });
        } else
        if (telefono.length === 0) {
            alertify.error("Ingrese un teléfono", function () {
                $('#telefono').focus();
            });
        } else {
            /* En el primer proceso guardamos el nuevo registro*/
            fn_guardarNuevaEmpresa(idEmpresa, nombre, ciudad, direccion, razonSocial, telefono, ruc, tipoContrib, resolucion, fechRes, bandera);
            /* Cerramos la ventana modal*/
            $('#ModalNuevo').modal('hide');
            /* En el segundo proceso actualiza la tabla para que se reflejen los cambios*/
            fn_cargarEmpresas(idPais);
        }

    } else if (Accion === 2) { //Modificar

        var opcion = $('#opcion_Modificar').is(':checked');
        var idPais = $('#idPais').val();
        var idEmpresa = $('#idEmpresa').val();
        var ruc = $('#txtruc').val();
        var nombre = $('#txtnombre').val();
        var ciudad = $('#txtciudad').val();
        var direccion = $('#txtdireccion').val();
        var razonSocial = $('#txtrazonSocial').val();
        var telefono = $('#txttelefono').val();
        var tipoContrib = $('#txttipoContibuyente').val();
        var resolucion = $('#txtresolucion').val();
        var fechRes = $('#txtfechRes').val();
        var tipEmision = $('#seltipEmision').val();
        var tipAmbiente = $('#seltipAmbiente').val();
        var contabilidad = $("#txtcontabilidad").bootstrapSwitch('state');
        if (contabilidad === true) {
            contabilidad = 'SI';
        } else {
            contabilidad = 'NO';
        }
        var bandera;
        if (opcion === true) {
            bandera = 1;
        } else {
            bandera = 2;
        }
        if (ruc.length === 0) {
            alertify.error("Ingrese el ruc de la empresa", function () {
                $('#txtruc').focus();
            });
        } else
        if (nombre.length === 0) {
            alertify.error("Ingrese el nombre de la empresa", function () {
                $('#txtnombre').focus();
            });
        } else
        if (ciudad.length === 0) {
            alertify.error("Ingrese la ciudad", function () {
                $('#txtciudad').focus();
            });
        } else
        if (direccion.length === 0) {
            alertify.error("Ingrese la dirección", function () {
                $('#txtdireccion').focus();
            });
        } else
        if (razonSocial.length === 0) {
            alertify.error("Ingrese la razón social", function () {
                $('#txtrazonSocial').focus();
            });
        } else
        if (tipoContrib.length === 0) {
            alertify.error("Ingrese el tipo de contribuyente", function () {
                $('#txttipoContibuyente').focus();
            });
        } else
        if (resolucion.length === 0) {
            alertify.error("Ingrese la resolución", function () {
                $('#txtresolucion').focus();
            });
        } else
        if (fechRes.length === 0) {
            alertify.error("Ingrese la fecha de resolución", function () {
                $('#txtfechRes').focus();
            });
        } else
        {
            /* En el primer proceso modifica la tabla*/
            fn_modificarEmpresa(idEmpresa, nombre, ciudad, direccion, razonSocial, telefono, ruc, tipoContrib, resolucion, fechRes, bandera, tipEmision, tipAmbiente, contabilidad);
            /* Cerramos la ventana modal*/
            $('#ModalModificar').modal('hide');
            /* En el segundo proceso actualiza la tabla para que se reflejen los cambios*/
            fn_cargarEmpresas(idPais);
        }
    }

}

/*
 * Funcion para guardar los nuevos datos de empresa
 * @param {type} numAuth
 * @param {type} fechaValidez
 * @param {type} txtinicioSec
 * @param {type} txtfinSec
 * @param {type} bandera
 * @returns {undefined}
 */
function fn_guardarNuevaEmpresa(idRest, numAuth, fechaValidez, txtinicioSec, txtfinSec, bandera) {
    send = {"guardarNuevaEmpresa": 1};
    send.accion = 2;//Significa que se guardara en el SP
    send.opcion = bandera;
    send.id_rest = idRest;
    send.num_autorizacion = numAuth;
    send.fecha_validez = fechaValidez;
    send.ini_secuencia = txtinicioSec;
    send.fin_secuencia = txtfinSec;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminAutoImpresora/configadminAutoImpresora.php", data: send,
        success: function (datos) {
            //Mensaje para la interfaz
            alertify.success("Los datos han sido guardados satisfactoriamente.");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error("No se ha podido guardar los elementos." + textStatus);
        }
    });
}

/**
 * Funcion para guardar los cambios en la tabla empresa
 * @param {type} idEmpresa
 * @param {type} nombre
 * @param {type} ciudad
 * @param {type} direccion
 * @param {type} razonSocial
 * @param {type} telefono
 * @param {type} ruc
 * @param {type} tipoContrib
 * @param {type} resolucion
 * @param {type} fechRes
 * @param {type} bandera
 * @param {type} tipEmision
 * @param {type} tipAmbiente
 * @param {type} contabilidad
 * @returns {undefined}
 */
function fn_modificarEmpresa(idEmpresa, nombre, ciudad, direccion, razonSocial, telefono, ruc, tipoContrib, resolucion, fechRes, bandera, tipEmision, tipAmbiente, contabilidad) {
    send = {"guardarDatosModificadosEmpresa": 1};
    send.idEmpresa = idEmpresa;
    send.nombre = nombre;
    send.ciudad = ciudad;
    send.direccion = direccion;
    send.razonSocial = razonSocial;
    send.telefono = telefono;
    send.ruc = ruc;
    send.tipoContribuyente = tipoContrib;
    send.resolucion = resolucion;
    send.fechRes = fechRes;
    send.bandera = bandera;
    send.tipEmision = tipEmision;
    send.tipAmbiente = tipAmbiente;
    send.contabilidad = contabilidad;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminEmpresa/configadminEmpresa.php", data: send,
        success: function (datos) {
            alertify.success("Los datos han sido modificados satisfactoriamente.");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error("No se ha podido actualizar los elementos." + textStatus);
//            alert( jqXHR);
//            alert(textStatus);
//            alert(errorThrown);
        }
    });
}

/*
 * Funcion principal para Pre visualizacion de pantallas
 * @param {type} accion
 * @returns {Boolean}
 */
function fn_accionar(accion) {
    if (accion === 'CallWS') {
        alert('Se llamara al  ws');
    } else if (accion === 'Grabar') {
    } else if (accion === 'Cancelar') {
    } else if (accion === 'Modificar') {
        lc_control = 1;
        lc_estacion = $("#cod_estacion").val();
        //fn_cargarestacionModificada(lc_estacion);
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


function fn_nuevaEmpresaColeccion() {
    $("#tipos_de_datoE").hide();
    $('#mdl_nuevaEColeccion').modal('show');
    $('#ModalModificar').modal('hide');
    fn_DetalleEColeccion();
    fn_limpiarcamposEmpresa();
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

function fn_DetalleEColeccion() {
    var empresa = $("#IDEmpresa").val();
    var html = '<tr class="bg-primary"><th class="text-center">Descripci&oacute;n</th></tr>';
    send = {"detalleColeccionEmpresa": 1};
    send.accion = 3;
    send.empresa = empresa;
    $.getJSON("../adminEmpresa/configadminEmpresa.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += '<tr id="' + i + 'detalleE_id' + '" onclick="fn_seleccionarDetalleEColeccion(' + i + ',\'' + datos[i]['ID_ColeccionEmpresa'] + '\',\'' + datos[i]['Descripcion'] + '\')"  class="text-left"><td>' + datos[i]['Descripcion'] + '</td>';
                html += '</tr>';
            }
            $("#coleccion_descripcionE").html(html);
        } else {
            html = html + '<tr><th colspan="6" class="text-center">No existen registros.</th></tr>';
            $("#coleccion_descripcionE").html(html);
        }
    });
}

function fn_seleccionarDetalleEColeccion(filaB, IDDetalleColeccion, descripcion) {
    $("#coleccion_descripcion tr").removeClass("success");
    $("#" + filaB + 'detalleE_id' + "").addClass("success");
    fn_DatosColeccionE(IDDetalleColeccion);
    $('#nombreColeccionE').text(descripcion);
}

function fn_DatosColeccionE(IDDetalleColeccion) {
    var empresa = $("#IDEmpresa").val();
    var html = '<tr class="bg-primary"><th class="text-center">Datos</th></tr>';
    send = {"datosColeccionEmpresa": 1};
    send.accion = 4;
    send.empresa = empresa;
    send.IDColeccionEmpresa = IDDetalleColeccion;
    $.getJSON("../adminEmpresa/configadminEmpresa.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += '<tr id="' + i + 'datosEmpresa_id' + '" onclick="fn_seleccionarDatosEColeccion(' + i + ',\'' + datos[i]['ID_ColeccionDeDatosEmpresa'] + '\',' + datos[i]['especificarValor'] + ',' + datos[i]['obligatorio'] + ',\'' + datos[i]['tipodedato'] + '\')" class="text-left"><td>' + datos[i]['datos'] + '</td>';
                html += '</tr>';
            }
            $("#coleccion_datosE").html(html);
        } else {
            html = html + '<tr><th colspan="6" class="text-center">No existen registros.</th></tr>';
            $("#coleccion_datosE").html(html);
        }
    });

    lc_IDColeccionEmpresa = IDDetalleColeccion;
}

function fn_editColeccionEmpresa() {
    var IDColeccion = $('#empresa_coleccion').find("tr.success").attr("id");
    if (IDColeccion) {
        var empresa = $("#IDEmpresa").val();
        $('#mdl_editEColeccion').modal('show');
        $('#ModalModificar').modal('hide');
        $('#edit_nombreColeccionE').text(lc_nombreEColeccion);

        $('#tipo_fecha_editE').daterangepicker({
            minDate: moment(),
            singleDatePicker: true,
            format: 'DD/MM/YYYY HH:mm',
            drops: 'up'
        }, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });

        $('#FechaInicial_editE').daterangepicker({
            minDate: moment(),
            singleDatePicker: true,
            format: 'DD/MM/YYYY HH:mm',
            drops: 'up'
        }, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });

        $('#FechaFinal_editE').daterangepicker({
            singleDatePicker: true,
            format: 'DD/MM/YYYY HH:mm',
            drops: 'up'
        }, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });

        $("#tipo_bit_editE").bootstrapSwitch('state', true);

        send = {"ListarColeccionxEmpresa": 1};
        send.accion = 2;
        send.empresa = empresa;
        send.lc_IDColeccionEmpresa_edit = lc_IDColeccionEmpresa_edit;
        send.lc_IDColeccionDeDatosEmpresa_edit = lc_IDColeccionDeDatosEmpresa_edit;
        $.getJSON("../adminEmpresa/configadminEmpresa.php", send, function (datos) {
            if (datos.str > 0) {
                if (datos[0].estado === 1) {
                    $("#check_estadoE").prop('checked', true);
                } else {
                    $("#check_estadoE").prop('checked', false);
                }

                if (datos[0].especificarValor === 1) {
                    $("#edit_check_especificaE").prop('checked', true);
                } else {
                    $("#edit_check_especificaE").prop('checked', false);
                }

                if (datos[0].obligatorio === 1) {
                    $("#edit_check_obligatorioE").prop('checked', true);
                } else {
                    $("#edit_check_obligatorioE").prop('checked', false);
                }

                $('#edit_lbl_tipoDatoE').text(datos[0]['tipodedato']);
                $('#tipo_varchar_editE').val(datos[0]['caracter']);
                $("#tipo_entero_editE").val(datos[0]['entero']);
                $('#tipo_fecha_editE').val(datos[0]['fecha']);
                $('#tipo_numerico_editE').val(datos[0]['numerico']);
                $('#FechaInicial_editE').val(datos[0]['fechaInicio']);
                $('#FechaFinal_editE').val(datos[0]['fechaFin']);
                $('#rango_minimo_editE').val(datos[0]['minimo']);
                $('#rango_maximo_editE').val(datos[0]['maximo']);

                if (datos[0]['seleccion'] === 1) {
                    $("#tipo_bit_editE").bootstrapSwitch('state', true);
                } else {
                    $("#tipo_bit_editE").bootstrapSwitch('state', false);
                }
            }
        });
    } else {
        alertify.error("Debe Seleccionar un Registro.");
    }
}

function fn_verModalE() {
    $('#mdl_editEColeccion').modal('hide');
    $('#mdl_nuevaEColeccion').modal('hide');
    $('#ModalModificar').modal('show');

}

function fn_modificarEmpresaColeccion() {

    var tipo_enteroE = 0;
    var tipo_numericoE = 0;
    var rango_minimoE = 0;
    var rango_maximoE = 0;

    if ($("#tipo_entero_editE").val() === '') {
        tipo_enteroE = 'null';
    } else {
        tipo_enteroE = $("#tipo_entero_editE").val();
    }

    seleccionE = $("#tipo_bit_editE").bootstrapSwitch('state');
    if (seleccionE === true) {
        var tipo_bit_editE = 1;
    } else {
        var tipo_bit_editE = 0;
    }

    if ($("#tipo_numerico_editE").val() === '') {
        tipo_numericoE = 'null';
    } else {
        tipo_numericoE = $("#tipo_numerico_editE").val();
    }

    if ($("#rango_minimo_editE").val() === '') {
        rango_minimoE = 'null';
    } else {
        rango_minimoE = $("#rango_minimo_editE").val();
    }

    if ($("#rango_maximo_editE").val() === '') {
        rango_maximoE = 'null';
    } else {
        rango_maximoE = $("#rango_maximo_editE").val();
    }

    send = {"modificarEmpresaColeccion": 1};
    send.accion = 2;
    send.lc_IDColeccionDeDatosEmpresa_edit = lc_IDColeccionDeDatosEmpresa_edit;
    send.lc_IDColeccionEmpresa_edit = lc_IDColeccionEmpresa_edit;
    send.varchar = $("#tipo_varchar_editE").val();
    send.entero = tipo_enteroE;
    send.fecha = $("#tipo_fecha_editE").val();
    send.seleccion = tipo_bit_editE;
    send.numerico = tipo_numericoE;
    send.fecha_inicio = $("#FechaInicial_editE").val();
    send.fecha_fin = $("#FechaFinal_editE").val();
    send.minimo = rango_minimoE;
    send.maximo = rango_maximoE;
    send.IDUsuario = $("#idUser").val();
    if ($("#check_estadoE").is(':checked')) {
        send.estado = 1;
    } else {
        send.estado = 0;
    }
    $.getJSON("../adminEmpresa/configadminEmpresa.php", send, function (datos) {
        if (datos.str > 0) {
            alertify.success("Datos actualizados correctamente.");
            var idEmpresa = $("#IDEmpresa").val();
            fn_cargarEmpresaColeccionDatos(idEmpresa);
            $('#mdl_editEColeccion').modal('hide');
            $('#ModalModificar').modal('show');

        } else {
            var error = datos.str;
            error = error.substr(54, 40);
            alertify.error(error);
        }
    });
}

function fn_insertarEColeccion() {
    var empresa = $("#IDEmpresa").val();
    var tipo_entero = 0;
    var tipo_numerico = 0;
    var rango_minimo = 0;
    var rango_maximo = 0;

    if ($("#tipo_enteroE").val() === '') {
        tipo_entero = 'null';
    } else {
        tipo_entero = $("#tipo_enteroE").val();
    }

    estado = $("#tipo_bitE").bootstrapSwitch('state');
    if (estado === true) {
        var tipo_bit = 1;
    } else {
        var tipo_bit = 0;
    }

    if ($("#tipo_numericoE").val() === '') {
        tipo_numerico = 'null';
    } else {
        tipo_numerico = $("#tipo_numericoE").val();
    }

    if ($("#rango_minimoE").val() === '') {
        rango_minimo = 'null';
    } else {
        rango_minimo = $("#rango_minimoE").val();
    }

    if ($("#rango_maximoE").val() === '') {
        rango_maximo = 'null';
    } else {
        rango_maximo = $("#rango_maximoE").val();
    }

    send = {"insertarEmpresaColeccion": 1};
    send.accion = 1;
    send.IDColecciondeDatosEmpresa = lc_IDColeccionDeDatosEmpresa;
    send.IDColeccionEmpresa = lc_IDColeccionEmpresa;
    send.varchar = $("#tipo_varcharE").val();
    send.entero = tipo_entero;
    send.fecha = $("#tipo_fechaE").val();
    send.seleccion = tipo_bit;
    send.numerico = tipo_numerico;
    send.fecha_inicio = $("#FechaInicialE").val();
    send.fecha_fin = $("#FechaFinalE").val();
    send.minimo = rango_minimo;
    send.maximo = rango_maximo;
    send.IDUsuario = $("#idUser").val();
    $.getJSON("../adminEmpresa/configadminEmpresa.php", send, function (datos) {
        if (datos.str > 0) {
            alertify.success("Datos guardados correctamente.");
            fn_cargarEmpresaColeccionDatos(empresa);
            $('#mdl_nuevaEColeccion').modal('hide');
            $('#ModalModificar').modal('show');
        } else {
            var error = datos.str;
            error = error.substr(54, 40);
            alertify.error(error);
        }
    });
}

function fn_seleccionarDatosEColeccion(filaC, IDDatosColeccion, especifica, obligatorio, tipo) {

    $("#coleccion_datosE tr").removeClass("success");
    $("#" + filaC + 'datosEmpresa_id' + "").addClass("success");
    $("#tipos_de_datoE").show();

    if (especifica === 1) {
        $("#check_especificaE").prop("checked", true);
    } else {
        $("#check_especificaE").prop("checked", false);
    }

    if (obligatorio === 1) {
        $("#check_obligatorioE").prop("checked", true);
    } else {
        $("#check_obligatorioE").prop("checked", false);
    }

    $("#lbl_tipoDatoE").text(tipo);

    $('#tipo_fechaE').daterangepicker({
        minDate: moment(),
        singleDatePicker: true,
        format: 'DD/MM/YYYY HH:mm',
        drops: 'up'
    }, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $('#FechaInicialE').daterangepicker({
        minDate: moment(),
        singleDatePicker: true,
        format: 'DD/MM/YYYY HH:mm',
        drops: 'up'
    }, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $('#FechaFinalE').daterangepicker({
        singleDatePicker: true,
        format: 'DD/MM/YYYY HH:mm',
        drops: 'up'
    }, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $("#tipo_bitE").bootstrapSwitch('state', true);

    lc_IDColeccionDeDatosEmpresa = IDDatosColeccion;
}
