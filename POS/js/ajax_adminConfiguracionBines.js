/*********************************************************
 *          DESARROLLADO POR: Alex Merino                *
 *          DESCRIPCION: JS de bines                     *
 *          FECHA CREACION: 16/04/2018                   *
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
var banderaMinimo; //variable para verificar si el minimo es correcto
var banderaMaximo; //variable para verificar si el maximo es correcto
var banderaMinimoM; //variable para verificar si el minimo a modificar es correcto
var banderaMaximoM; //variable para verificar si el maximo a modificar es correcto
var longitudBines = 0;
var arraydetalleBines = [];
var table;
$(document).ready(function () {
    $("#txtcontabilidad").bootstrapSwitch('state', true);
    fn_esconderDiv();
    fn_btn("agregar", 1);
    fn_cargarBines();
    fn_configuracion();
});

/* Formatting function for row details - modify as you need */
function format(data, i) {
    html = "";
    var arreglo = [];
    var columnas = [];
    var arreglo = data.split(";");
    html += "<table id=\"subTable-" + i + "\" style=\"width:100%\">";
    html += "<thead><th style='color: #333;' class='titulo'>Definición de politica</th><th style='color: #333;' class='titulo'>Bin inicial</th><th style='color: #333;' class='titulo'>Bin final</th><th style='color: #333;' class='titulo'>Activo</th></thead>";
    for (var i = 0; i < arreglo.length; i++) {
        if (arreglo[i] !== "") {
            columnas = arreglo[i].split(",");
            html += "<tr class='tabla_detalleMov' id='" + i + "' style='cursor:pointer'";
            html += "onclick=fn_seleccionclick(" + i + ") ondblclick=fn_seleccionModificar('" + escape(columnas[0]) + "','" + columnas[1] + "','" + columnas[2] + "')>";
            html += "<td align='center'  style='width:30%;'>" + columnas[4] + "</td>";
            html += "<td align='center'  style='width:25%;'>" + columnas[5] + "</td>";
            html += "<td align='center'  style='width:25%;'>" + columnas[6] + "</td>";
            if (columnas[7] === 'Activo') {
                html += "<td align='center'  style='width:15%' ><input type='checkbox' checked='checked' disabled /></td></tr>";
            } else
            if (columnas[7] === 'Inactivo') {
                html += "<td align='center'  style='width:15%' ><input type='checkbox' disabled /></td></tr>";
            }

        }
    }
    html += "</table>";
    return html;
}

/**
 * Funcion que obtiene el valor de configuracion de los bines
 * @returns {undefined}
 */
function fn_configuracion() {
    send = {"infValorConfiguracion": 1};
    $.ajax({async: false, type: "GET", dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminConfiguracionBines/configadminConfiguracionBines.php", data: send,
        success: function (datos) {
            if (datos.valor > 0) {
                longitudBines = datos.valor;
            }
            //Eliminamos la imagen de carga  
            $("#load").empty();
        }
    });
}

/**
 * Funcion para inicializar las estados para select minimo y maximo
 * @returns {undefined}
 */
function fn_close() {
    banderaMinimo = true;
    banderaMaximo = true;
    banderaMinimoM = true;
    banderaMaximoM = true;
}

function fn_data(data, indice) {
    var tr = $("#details-control-" + indice).closest('tr');
    var row = table.row(tr);
    $("#details-control-image-" + indice).toggleClass("fa fa-angle-double-right fa fa-angle-double-down");
    // Para cerrar el accordion abierto  
    if (row.child.isShown()) {
        $("#details-control-" + indice).toggleClass('btn_blue btn');
        row.child.hide();
        tr.removeClass('shown');
    } else { //Parar abrir un accordion del treetable        
        $("#details-control-" + indice).toggleClass('btn btn_blue');
        row.child(format(data, indice)).show();
        tr.addClass('shown');
        $('#subTable-' + indice).dataTable({
            'lengthChange': false,
            'paging': true,
            'select': false,
            'ordering': true,
            'searching': true,
            'destroy': true
        });
    }

}

/**
 * Funcion que permite obtener el listado de empresas disponibles 
 * @returns {undefined}
 */
function fn_cargarBines() {
    $('#load').html('<div style="text-align: center"><img src="../../imagenes/ajax-loader.gif"/></div>');
    send = {"lstBines": 1};
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminConfiguracionBines/configadminConfiguracionBines.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#tabla_bines").show();
                html = "<thead><tr>";
                html += "<th style=\"width:5%\"></th>";  
                html += "<th style=\"width:95%;font-size:18px;\">Forma de pago</th>";  
                html += "</tr></thead>";  
                html += "<tbody>";  
                for (var i = 0; i < datos.str; i++) {
                    html += "<tr><td><center><button id='details-control-" + i + "' class=\"btn\" onclick=\"fn_data('" + datos[i]['valor'] + "'," + i + ")\" \"><i id=\"details-control-image-" + i + "\" class=\"fa fa-angle-double-right\"></i></button></center></td><td colspan='5'>" + datos[i]['fmp_descripcion'] + "</td></tr>";
                }
                html += "</tbody>";
                $("#bin").html(html);
                table = $('#bin').DataTable({
                    'lengthChange': false,
                    'paging': true,
                    'select': false,
                    'ordering': true,
                    'searching': true,
                    'destroy': true
                });
            } else {
                $("#tabla_bines").show();
                html = "<thead><tr>";  
                html += "<th style=\"width:5%\"></th>";
                html += "<th style=\"width:95%;font-size:18px;\">Forma de pago</th>";               
                html += "<tr><td colspan='6'> No existen datos para visualizar.</td></tr>";
                $("#bin").html(html);
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
    $("#detalle_bines tr").removeClass("success");
    $("#" + fila + "").addClass("success");
}


/**
 * Funcion para modificar los elementos de la tabla al hacer doble click mendiante el id coleccioncadena
 * @param {type} nameCadena 
 * @param {type} idColeccionCadena
 * @param {type} idColecciondedatosCadena 
 * @returns {undefined}
 */
function fn_seleccionModificar(nameCadena, idColeccionCadena, idColecciondedatosCadena) {
    Accion = 2;
    $('#myTabs a:first').tab('show');
    $('#titulomodalModificar').text(unescape(nameCadena));
    $('#ModalModificar').modal('show');
    $("#divminimoM").html("");
    $("#divmaximoM").html("");
    // Se crea los input 
    $("#divminimoM").html("<input type='text' onkeydown=\"return validarNumeros(event)\" maxlength='" + longitudBines + "' style=\"text-align:left\" onblur=\"fn_validarBinInicialModificar()\" class=\"form-control\" id=\"txtminimo\" />");
    $("#divmaximoM").html("<input type='text' onkeydown=\"return validarNumeros(event)\" maxlength='" + longitudBines + "' style=\"text-align:left\" onblur=\"fn_validarBinFinalModificar()\" class=\"form-control\" id=\"txtmaximo\" />");
    //Color para los input
    $("#txtminimo").css({'border-color': '#d5d8dc'});
    $("#txtmaximo").css({'border-color': '#d5d8dc'});
    fn_cargarInfoBines(idColeccionCadena, idColecciondedatosCadena);
}


/**
 * Funcion que carga la data en el modal de bines para despues ser modificados
 * @param {type} idColeccionCadena  
 * @param {type} idColecciondedatosCadena     
 * @returns {undefined}
 */
function fn_cargarInfoBines(idColeccionCadena, idColecciondedatosCadena) {
    send = {"infBines": 1};
    send.idColeccionCadena = idColeccionCadena;
    send.idColecciondedatosCadena = idColecciondedatosCadena;
    $.getJSON("../adminConfiguracionBines/configadminConfiguracionBines.php", send, function (datos) {
        if (datos.str > 0)
        {
            //Se valida si el dato estado de la BDD esta activo o inactivo       
            if (datos.estado === 'Activo') {
                $('#opcion_Modificar').prop('checked', true);
            } else {
                $('#opcion_Modificar').prop('checked', false);
            }
            //Se llena los campos del modal con la data obtenida en la consulta
            $("#txtidcadena").val(datos.ID_ColeccionCadena);
            $("#txtidcadenaColeccion").val(datos.id_colecciondedatoscadena);
            $("#txtcadena").val(datos.cdn_descripcion);
            $("#txtminimo").val(datos.min);
            $("#txtmaximo").val(datos.max);
            //Muestra las politicas
            fn_getTipoDefinicionesPoliticas(idColeccionCadena, idColecciondedatosCadena, datos.definicionPolitica);
            //Muestra las formas de pago
            fn_getTipoFormapago(datos.IDFormapago);
        }
    });
}
/**
 * Funcion que permite obtener la polica de las definiciones
 * @param {type} id  
 * @param {type} definicion  
 * @param {type} idColecciondedatosCadena  
 * @returns {undefined}   
 */
function fn_getTipoDefinicionesPoliticas(id, idColecciondedatosCadena, definicion) {
    send = {"lstDefinicionesModificar": 1};
    send.idColeccionCadena = id;
    $.ajax({async: false, type: "GET", dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminConfiguracionBines/configadminConfiguracionBines.php", data: send,
        success: function (datos) {
            if (datos.str === 0) {
                $("#tipDefiniciones").html("<select id='selDefinicionesModificar' class='form-control'><option value='" + idColecciondedatosCadena + "'>" + definicion + "</option></select>");
            } else if (datos.str > 0) {
                $("#tipDefiniciones").html("");
                $("#tipDefiniciones").html(" <select id='selDefinicionesModificar' class='form-control'><option value='" + idColecciondedatosCadena + "' selected>" + definicion + "</option></select>");
                for (var i = 0; i < datos.str; i++) {
                    if (datos[i]['ID_ColeccionDeDatosCadena'] !== idColecciondedatosCadena) {
                        html = "<option value='" + datos[i]['ID_ColeccionDeDatosCadena'] + "'>" + datos[i]['Descripcion'] + "</option>";
                        $("#selDefinicionesModificar").append(html);
                    }
                }

            }
        }
    });
}

/**
 * Funcion que permite obtener el tipo de forma de pago
 * @param {type} id  
 * @returns {undefined}   
 */
function fn_getTipoFormapago(id) {
    send = {"infFormaPago": 1};
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminConfiguracionBines/configadminConfiguracionBines.php", data: send,
        success: function (datos) {
            if (datos.str === 0) {
                alertify.alert("No existen datos para forma de pago");
            } else if (datos.str > 0) {
                $("#tipFormaPago").html("");
                $("#tipFormaPago").html(" <select id='selformaPago' class='form-control'></select>");
                for (var i = 0; i < datos.str; i++) {
                    if (id === datos[i]['IDFormapago']) {
                        html = "<option value='" + datos[i]['IDFormapago'] + "' selected>" + datos[i]['fmp_descripcion'] + "</option>";
                    } else
                    {
                        html = "<option value='" + datos[i]['IDFormapago'] + "'>" + datos[i]['fmp_descripcion'] + "</option>";
                    }
                    $("#selformaPago").append(html);
                }
                //$("#selformaPago").chosen({width: "100%"});
            }
        }
    });
}

/**
 * VALIDACIONES DE BINES INICIAL Y FINAL
 * 
 */
function fn_validarBinInicial() {
    banderaMinimo = true;
    send = {"validateMinimo": 1};
    send.minimo = $("#minimo").val();
    if ($("#minimo").val().length > 0) {
        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminConfiguracionBines/configadminConfiguracionBines.php", data: send,
            success: function (datos) {
                if (datos.valor > 0) {
                    banderaMinimo = false;
                    $("#minimo").focus();
                    $("#minimo").css({'border-color': 'red'});
                    alertify.error("El bin inicial ya esta registrado");
                } else {
                    $("#minimo").css({'border-color': '#d5d8dc'});
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alertify.error("No se ha podido actualizar los elementos." + textStatus);
            }
        });
    }

}

function fn_validarBinFinal() {
    banderaMaximo = true;
    send = {"validateMaximo": 1};
    send.maximo = $("#maximo").val();
    if ($("#maximo").val().length > 0) {
        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminConfiguracionBines/configadminConfiguracionBines.php", data: send,
            success: function (datos) {
                if (datos.valor > 0) {
                    banderaMaximo = false;
                    $("#maximo").focus();
                    $("#maximo").css({'border-color': 'red'});
                    alertify.error("El bin final ya esta registrado");
                } else {
                    $("#maximo").css({'border-color': '#d5d8dc'});
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alertify.error("No se ha podido actualizar los elementos." + textStatus);
            }
        });
    }
}

/**
 * VALIDACIONES DE BINES INICIAL Y FINAL PARA MODIFICAR
 * 
 */
function fn_validarBinInicialModificar() {
    banderaMinimoM = true;
    send = {"validateMinimo": 1};
    send.minimo = $("#txtminimo").val();
    if ($("#txtminimo").val().length > 0) {
        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminConfiguracionBines/configadminConfiguracionBines.php", data: send,
            success: function (datos) {
                if (datos.valor > 0) {
                    banderaMinimoM = false;
                    $("#txtminimo").focus();
                    $("#txtminimo").css({'border-color': 'red'});
                    alertify.error("El bin inicial ya esta registrado");
                } else {
                    $("#txtminimo").css({'border-color': '#d5d8dc'});
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alertify.error("No se ha podido actualizar los elementos." + textStatus);
            }
        });
    }

}

function fn_validarBinFinalModificar() {
    banderaMaximoM = true;
    send = {"validateMaximo": 1};
    send.maximo = $("#txtmaximo").val();
    if ($("#txtmaximo").val().length > 0) {
        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminConfiguracionBines/configadminConfiguracionBines.php", data: send,
            success: function (datos) {
                if (datos.valor > 0) {
                    banderaMaximoM = false;
                    $("#txtmaximo").focus();
                    $("#txtmaximo").css({'border-color': 'red'});
                    alertify.error("El bin final ya esta registrado");
                } else {
                    $("#txtmaximo").css({'border-color': '#d5d8dc'});
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alertify.error("No se ha podido actualizar los elementos." + textStatus);
            }
        });
    }
}

/*
 * Funcion que permite obtener las politicas  
 * @param {type} id     
 * @returns {undefined}
 */
function getPoliticas() {
    send = {"lsPoliticas": 1};
    $.ajax({async: false, type: "GET", dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminConfiguracionBines/configadminConfiguracionBines.php", data: send,
        success: function (datos) {
            if (datos.str === 0) {
                alertify.alert("No existen datos para politicas");
            } else if (datos.str > 0) {
                $("#politicas").html("");
                $("#politicas").html("<input type='text' id='txtidpolitica' value='" + datos.ID_ColeccionCadena + "' />");
                //Llamamos a las definiciones  
                getDefiniciones(datos.ID_ColeccionCadena);
            }
        }
    });
}

/*
 * Funcion que permite obtener las definiciones de las politicas 
 * @param {type} id     
 * @returns {undefined}
 */
function getDefiniciones(id) {
    send = {"lstDefiniciones": 1};
    send.idColeccionCadena = id;
    $.ajax({async: false, type: "GET", dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminConfiguracionBines/configadminConfiguracionBines.php", data: send,
        success: function (datos) {
            if (datos.str === 0) {
                $("#definiciones").html("<select id='selDefiniciones' class='form-control'><option value='0'>No hay definiciones</option></select>");
            } else if (datos.str > 0) {
                $("#definiciones").html("");
                $("#definiciones").html(" <select id='selDefiniciones' class='form-control'><option value='0' selected>---Seleccione una definición---</option></select>");
                for (var i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['ID_ColeccionDeDatosCadena'] + "'>" + datos[i]['Descripcion'] + "</option>";
                    $("#selDefiniciones").append(html);
                }
            }
            //$("#selDefiniciones").chosen({width: "100%"});
        }
    });
}

function getFormaPago() {
    send = {"infFormaPago": 1};
    $.ajax({async: false, type: "GET", dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminConfiguracionBines/configadminConfiguracionBines.php", data: send,
        success: function (datos) {
            if (datos.str === 0) {
                $("#formaPago").html("");
                $("#formaPago").html("<select id='selFormaPago' width='100%' class='form-control'><option value'0'> No hay una forma de pago</option></select>");
            } else if (datos.str > 0) {
                $("#formaPago").html("");
                $("#formaPago").html("<select id='selFormaPago' class='form-control'><option value'0' selected>---- Seleccione una forma de pago----</option></select>");
                for (var i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['IDFormapago'] + "'>" + datos[i]['fmp_descripcion'] + "</option>";
                    $("#selFormaPago").append(html);
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
        var politicas = $('#txtidpolitica').val();
        var definicion = $('#selDefiniciones').val();
        var minimo = $('#minimo').val();
        var maximo = $('#maximo').val();
        var formapago = $('#selFormaPago').val();
        var bandera;
        if (opcion === true) {
            bandera = 1;
        } else {
            bandera = 0;
        }

        if (minimo.length === 0) {
            alertify.error("Ingrese un bin inicial", function () {
                $('#minimo').focus();
            });
        } else
        if (maximo.length === 0) {
            alertify.error("Ingrese un bin final", function () {
                $('#maximo').focus();
            });
        } else
        if (minimo.length < longitudBines || maximo.length < longitudBines) {
            alertify.error("La longitud de los bines ingresados deben ser " + longitudBines, function () {
                $('#minimo').focus();
            });
        } else
        if (politicas === null) {
            alertify.error("Debe seleccionar una política", function () {
                $('#selPoliticas').focus();
            });
        } else
        if (definicion === null || definicion === '0') {
            alertify.error("Debe seleccionar una definición o ya ha sido seleccionada", function () {
                $('#selDefiniciones').focus();
            });
        } else
        if (formapago === null) {
            alertify.error("Debe seleccionar una forma de pago", function () {
                $('#selFormaPago').focus();
            });
        } else
        if (banderaMinimo === false || banderaMaximo === false) {
            alertify.error("Tienes validaciones pendientes en el formulario", function () {
            });
        } else {
            /* En el primer proceso guardamos el nuevo registro*/
            fn_guardarNuevoBines(politicas, definicion, minimo, maximo, formapago, bandera);
            /* Cerramos la ventana modal*/
            $('#ModalNuevo').modal('hide');
            /* En el segundo proceso actualiza la tabla para que se reflejen los cambios*/
            fn_cargarBines();
        }

    } else
    if (Accion === 2) { //Modificar 
        var opcion = $('#opcion_Modificar').is(':checked');
        var idCadena = $('#txtidcadena').val();
        var idColeccionCadena = $('#txtidcadenaColeccion').val();
        var definicionModificar = $('#selDefinicionesModificar').val();
        var minimo = $('#txtminimo').val();
        var maximo = $('#txtmaximo').val();
        var formpago = $('#selformaPago').val();
        /*var contabilidad = $("#txtcontabilidad").bootstrapSwitch('state');
         if (contabilidad === true) {
         contabilidad = 'SI';
         } else {
         contabilidad = 'NO';
         }*/
        var bandera;
        if (opcion === true) {
            bandera = 1;
        } else {
            bandera = 0;
        }

        err = false;
        if (minimo.length === 0) {
            alertify.error("Ingrese el bin inicial", function () {
                $('#txtminimo').focus();
            });
            err = true;
        } else
        if (maximo.length === 0) {
            alertify.error("Ingrese el bin final", function () {
                $('#txtmaximo').focus();
            });
            err = true;
        } else if (((parseInt(minimo.length) < parseInt(longitudBines) || parseInt(minimo.length) > parseInt((longitudBines))) &&
                ((parseInt(maximo.length) < parseInt(longitudBines)) || parseInt(maximo.length) > parseInt(longitudBines)))) {
            alertify.error("La longitud de los bines ingresados deben ser " + longitudBines, function () {
                $('#txtminimo').focus();
            });
            err = true;
        } else
        if (banderaMinimoM === false || banderaMaximoM === false) {
            alertify.error("Tienes validaciones pendientes en el formulario", function () {
            });
            err = true;
        }
        if (definicionModificar === null || definicionModificar === '0') {
            alertify.error("Debe seleccionar una definición", function () {
                $('#selDefiniciones').focus();
            });
            err = true;
        }

        if (!err) {
            //Validacion para modificar los bines 
            if (idColeccionCadena.trim() === definicionModificar.trim()) {
                fn_modificarBit(idCadena, idColeccionCadena, '', minimo, maximo, formpago, bandera);
            } else {
                fn_modificarBit(idCadena, idColeccionCadena, definicionModificar, minimo, maximo, formpago, bandera);
            }

            /* Cerramos la ventana modal*/
            $('#ModalModificar').modal('hide');
            /* En el segundo proceso actualiza la tabla para que se reflejen los cambios*/
            fn_cargarBines();
        }


    }
}

/*
 * Funcion para guardar los nuevos datos de empresa
 * @param {type} politicas 
 * @param {type} definicion 
 * @param {type} minimo
 * @param {type} maximo
 * @param {type} formapago
 * @param {type} bandera
 * @returns {undefined}
 */
function fn_guardarNuevoBines(politicas, definicion, minimo, maximo, formapago, bandera) {
    send = {"guardarNuevoBin": 1};
    send.politicas = politicas;
    send.definicion = definicion;
    send.minimo = minimo;
    send.maximo = maximo;
    send.formapago = formapago;
    send.bandera = bandera;
    $.ajax({async: false, type: "GET", dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminConfiguracionBines/configadminConfiguracionBines.php", data: send,
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
 * Funcion para guardar los cambios en la tabla cadenacolecciondedatos 
 * @param {type} idCadena
 * @param {type} idColeccionCadena
 * @param {type} idColeccionCadenaModificar
 * @param {type} minimo
 * @param {type} maximo
 * @param {type} formpago
 * @param {type} bandera  
 * @returns {undefined}
 */
function fn_modificarBit(idCadena, idColeccionCadena, idColeccionCadenaModificar, minimo, maximo, formpago, bandera) {
    send = {"guardarDatosModificadosBines": 1};
    send.idCadena = idCadena;
    send.idColeccionCadena = idColeccionCadena;
    send.idColeccionCadenaModificar = idColeccionCadenaModificar;
    send.minimo = minimo;
    send.maximo = maximo;
    send.formpago = formpago;
    send.bandera = bandera;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminConfiguracionBines/configadminConfiguracionBines.php", data: send,
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

