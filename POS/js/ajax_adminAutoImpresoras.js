///////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Alex Merino////////////////////////////
///////DESCRIPCION: Clase para auto impresoras///////////////////
///////FECHA CREACION: 14-03-2018//////////////////////////////////
///////////////////////////////////////////////////////////////////

var Accion = 0;
var lc_paginas = -1; //para el paginador nunca se va a encerar a menos quer se actualice la pantalla
var acc_resultado = 0;
var acc_std = 0;
var reg_max = 0;
var reg_pres = 10;
var indice = "N/A";
var arrayDatos = new Array();
var descripciones = [];
var ID_CAMPOS_FORMULARIO = '';
var ID_CAMPOS_FECHAS = "";
var ID_CAMPOS_SELECT = "";
var ACUM_VALOR = "";

$(document).ready(function () {
    fn_esconderDiv();
    fn_btn("agregar", 1);
    $("#selrest").attr("disabled", false);
    //Funcion que permite obtener la informacion de los restaurantes
    fn_cargarRestaurante();
    //Acciones al momento de obtener la informacion
    $("#selrest").change(function () {
        if ($("#selrest").val() != '0') {
            fn_esconderDiv();
        } else {
            fn_esconderDiv();
        }
    });

    //Funcion que permite cargar los campos de configuración
    fn_loadTable();
});


/**
 * Funcion que permite crear los campos del modal dinamicamente
 * @returns {undefined}
 */
function fn_loadTable() {
    send = {"loadModalNuevo": 1};
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminAutoImpresora/configadminAutoImpresora.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#panelInformacion").html();
                html = "";
                titulo = "";
                for (var i = 0; i < datos.str; i++) {
                    html += "<div class=\"row\">";
                    html += "<div class=\"col-xs-1\"></div>";
                    html += "<div class=\"col-xs-3\"><h5>" + datos[i]['Descripcion'] + ":</h5></div>";
                    html += "<div class=\"col-xs-7\">";
                    html += "<div class=\"form - group\" class=\"col - xs - 1\">";
                    if (datos[i]['tipodedato'] === 'I') {
                        html += "<input style=\"text-align:left\" class=\"form-control autoimpresores\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "' onpaste=\"return false\" onkeydown=\"return validarNumeros(event)\" />";
                    } else
                    if (datos[i]['tipodedato'] === 'D') {
                        html += "<div class=\"input-prepend input-group\"><span class=\"add-on input-group-addon\"><i class=\"glyphicon glyphicon-calendar fa fa-calendar\"></i></span><input type=\"text\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "' style=\"text-align:left\" class=\"form-control autoimpresores\"  readonly /></div>"
                        ID_CAMPOS_FECHAS += datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + ",";
                    } else
                    if (datos[i]['tipodedato'] === 'V') {
                        html += "<input style=\"text-align:left\" class=\"form-control autoimpresores\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "' onpaste=\"return false\" style=\"text-transform:uppercase\" onkeydown=\"return soloLetras(event)\" />";
                    } else
                    if (datos[i]['tipodedato'] === 'T') {
                        html += "<div class=\"input-prepend input-group\"><span class=\"add-on input-group-addon\"><i class=\"glyphicon glyphicon-calendar fa fa-calendar\"></i></span><input type=\"text\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "' style=\"text-align:left\" class=\"form-control autoimpresores\"  readonly /></div>"
                        ID_CAMPOS_FECHAS += datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + ",";
                    } else
                    if (datos[i]['tipodedato'] === 'N') {
                        html += "<input style=\"text-align:left\" class=\"form-control autoimpresores\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "' onpaste=\"return false\" onkeydown=\"return validarNumeros(event)\" />";
                    } else
                    if (datos[i]['tipodedato'] === 'B') {
                        campoidentificador = ((datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato']).replace("_", "-").replace(" ", ""));
                        html += "<input type=\"checkbox\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "' class=\"form-control autoimpresores " + campoidentificador + "\" data-off-text=\"No\" data-on-text=\"Si\"/>";
                        ID_CAMPOS_SELECT += campoidentificador + ",";
                    } else
                    if (datos[i]['tipodedato'] === 'M') {
                        html += "<input style=\"text-align:left\" class=\"form-control autoimpresores\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "' onpaste=\"return false\"  />";
                    } else
                    if (datos[i]['tipodedato'] === 'E') {
                        html += "<input style=\"text-align:left\" class=\"form-control autoimpresores\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "' onpaste=\"return false\" onkeydown=\"return validarNumeros(event)\" />";
                    }
                    html += "</div>";
                    html += "</div>";
                    html += "<div class=\"col-xs-1\"></div>";
                    html += "</div></br>";
                    titulo = datos[i]['titulo'];
                    descripciones[i] = datos[i]['Descripcion'];
                }
                $("#titulo").html("<span class=\"glyphicon glyphicon-print\" style=\"font-size: 20px;\"></span>&nbsp; " + titulo);
                $("#panelInformacion").html(html);
                //Se agregan los campos para la pantalla modal de mofificar
                htmlM = "";
                for (var i = 0; i < datos.str; i++) {
                    htmlM += "<div class=\"row\">";
                    htmlM += "<div class=\"col-xs-1\"></div>";
                    htmlM += "<div class=\"col-xs-3\"><h5>" + datos[i]['Descripcion'] + ":</h5></div>";
                    htmlM += "<div class=\"col-xs-7\">";
                    htmlM += "<div class=\"form - group\" class=\"col - xs - 1\">";
                    if (datos[i]['tipodedato'] === 'I') {
                        htmlM += "<input style=\"text-align:left\" class=\"form-control autoimpresoresM\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "-M' onpaste=\"return false\" onkeydown=\"return validarNumeros(event)\" />";
                    } else
                    if (datos[i]['tipodedato'] === 'D') {
                        htmlM += "<div class=\"input-prepend input-group\"><span class=\"add-on input-group-addon\"><i class=\"glyphicon glyphicon-calendar fa fa-calendar\"></i></span><input type=\"text\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "-M' style=\"text-align:left\" class=\"form-control autoimpresoresM\"  readonly /></div>"
                        ID_CAMPOS_FECHAS += datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "-M,";
                    } else
                    if (datos[i]['tipodedato'] === 'V') {
                        htmlM += "<input style=\"text-align:left\" class=\"form-control autoimpresoresM\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "-M' onpaste=\"return false\" style=\"text-transform:uppercase\" onkeydown=\"return soloLetras(event)\" />";
                    } else
                    if (datos[i]['tipodedato'] === 'T') {
                        htmlM += "<div class=\"input-prepend input-group\"><span class=\"add-on input-group-addon\"><i class=\"glyphicon glyphicon-calendar fa fa-calendar\"></i></span><input type=\"text\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "-M' style=\"text-align:left\" class=\"form-control autoimpresoresM\"  readonly /></div>"
                        ID_CAMPOS_FECHAS += datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "-M,";
                    } else
                    if (datos[i]['tipodedato'] === 'N') {
                        htmlM += "<input style=\"text-align:left\" class=\"form-control autoimpresoresM\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "-M' onpaste=\"return false\" onkeydown=\"return validarNumeros(event)\" />";
                    } else
                    if (datos[i]['tipodedato'] === 'B') {
                        campoidentificadorM = ((datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + '-M').replace("_", "-").replace(" ", ""));
                        htmlM += "<input type=\"checkbox\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "-M' class=\"form-control autoimpresoresM " + campoidentificadorM + "\" data-off-text=\"No\" data-on-text=\"Si\"/>";
                        ID_CAMPOS_SELECT += campoidentificadorM + ",";
                    } else
                    if (datos[i]['tipodedato'] === 'M') {
                        htmlM += "<input style=\"text-align:left\" class=\"form-control autoimpresoresM\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "-M' onpaste=\"return false\"  />";
                    } else
                    if (datos[i]['tipodedato'] === 'E') {
                        htmlM += "<input style=\"text-align:left\" class=\"form-control autoimpresoresM\" id='" + datos[i]['descripcionCompleta'] + '-' + datos[i]['tipodedato'] + "-M' onpaste=\"return false\" onkeydown=\"return validarNumeros(event)\" />";
                    }
                    htmlM += "</div>";
                    htmlM += "</div>";
                    htmlM += "<div class=\"col-xs-1\"></div>";
                    htmlM += "</div></br>";
                    titulo = datos[i]['titulo'];
                    descripciones[i] = datos[i]['Descripcion'];
                }
                htmlM += "<div class=\"row\" style=\"display:none\">";
                htmlM += "<div class=\"col-xs-1\"></div>";
                htmlM += "<div class=\"col-xs-3\"><h5>Id coleccion restaurante:</h5></div>";
                htmlM += "<div class=\"col-xs-7\">";
                htmlM += "<div class=\"form - group\" class=\"col - xs - 1\">";
                htmlM += "<input style=\"text-align:left\" class=\"form-control\" id='idcoleccionrestaurante-M'   />";
                htmlM += "</div>";
                htmlM += "</div>";
                htmlM += "<div class=\"col-xs-1\"></div>";
                htmlM += "</div></br>";

                htmlM += "<div class=\"row\" style=\"display:none\">";
                htmlM += "<div class=\"col-xs-1\"></div>";
                htmlM += "<div class=\"col-xs-3\"><h5>Id coleccion de datos restaurante:</h5></div>";
                htmlM += "<div class=\"col-xs-7\">";
                htmlM += "<div class=\"form - group\" class=\"col - xs - 1\">";
                htmlM += "<input style=\"text-align:left\" class=\"form-control \" id='idcolecciondedatosrestaurante-M'  style=\"display:none\"  />";
                htmlM += "</div>";
                htmlM += "</div>";
                htmlM += "<div class=\"col-xs-1\"></div>";
                htmlM += "</div></br>";

                htmlM += "<div class=\"row\" style=\"display:none\">";
                htmlM += "<div class=\"col-xs-1\"></div>";
                htmlM += "<div class=\"col-xs-3\"><h5>Secuencial Autoimpresor:</h5></div>";
                htmlM += "<div class=\"col-xs-7\">";
                htmlM += "<div class=\"form - group\" class=\"col - xs - 1\">";
                htmlM += "<input style=\"text-align:left\" class=\"form-control \" id='idcoleccionsecuencial'  style=\"display:none\"  />";
                htmlM += "</div>";
                htmlM += "</div>";
                htmlM += "<div class=\"col-xs-1\"></div>";
                htmlM += "</div></br>";

                $("#panel_modificar").html(htmlM);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error("Pilas error." + errorThrown);
        }
    });

    //Crea los componentes Date
    fn_rowDate();

    //Crea los componentes Select
    fn_rowSelect();

}

/**
 * Funcion para crear el componente chosee en los modales
 * @returns {undefined}
 */
function fn_rowSelect() {
    var cls_select = ID_CAMPOS_SELECT.split(",");
    STR = "";
    cls_select.forEach(function (element) {
        if (element !== '') {
            STR = "." + element;
            $(STR).bootstrapSwitch('state', true);
        }
    });
}


/**
 * Funcion para crear el componente date en los modales
 * @returns {undefined}
 */
function fn_rowDate() {
    var id_fechas = ID_CAMPOS_FECHAS.split(",");
    id_fechas.forEach(function (element) {
        $('#' + element).daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });
    });
}

/**
 * Funcion que permite mostrar las empresas
 * @returns {undefined}
 */
function fn_cargarEmpresa() {
    /* Carga la informacion de las empresas existentes*/
    send = {"cargarEmpresaAutoImpresora": 1};
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminAutoImpresora/configadminAutoImpresora.php", data: send,
        success: function (datos) {
            if (datos.str == 0) {
                alertify.alert("No existen datos para esta Cadena");
            } else if (datos.str > 0) {
                $("#sel_empresa").html("");
                $("#sel_empresa").html("<option selected value='0'>--------------   Seleccione Empresa  --------------</option>");
                for (var i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['emp_id'] + "'>" + datos[i]['emp_id'] + " | " + datos[i]['emp_nombre'] + "</option>";
                    $("#sel_empresa").append(html);
                }
            }
        }
    });
}

/**
 * Funcion que permite mostrar los restaurantes
 * @returns {undefined}
 */
function fn_cargarRestaurante() {
    send = {"infRestaurantesCadena": 1};
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminAutoImpresora/configadminAutoImpresora.php", data: send,
        success: function (datos) {
            if (datos.str === 0) {
                alertify.alert("No existen datos para esta Cadena");
            } else if (datos.str > 0) {
                //$("#divrestaurante").show();  
                $("#selrest").html("");
                $('#selrest').html("<option selected value='0'>--------------  Seleccione restaurante  --------------</option>");
                for (var i = 0; i < datos.str; i++) {
                    html = "<option value='" + datos[i]['rst_id'] + "' data-fastFood='" + datos[i]['rst_cod_tienda'] + "'>" + datos[i]['rst_cod_tienda'] + "-" + datos[i]['rst_descripcion'] + "</option>";
                    $("#selrest").append(html);
                }
                $("#selrest").chosen();
                //Opcion cuando se cambia algun elemento del combobox
                $("#selrest").change(function () {
                    //Seleccionamos el valor restaurante
                    lc_rest = $("#selrest").val();
                    //Seleccionamos el texto del valor de un restaurante
                    txt_res = $("#selrest option:selected").html();
                    //Realizamos el split del texto seleccionado para capturar el nombre
                    txt_value_res = txt_res.split("-");
                    //Llama a la funcion que mostrara la tabla  
                    fn_cargarDetalleInactivos(lc_rest, 10);
                    //Seteamos el nombre del combo seleccionado en el titulo del modal
                    $("#titulomodalNuevo").text(txt_res);
                    //Seteamos el id del restaurante seleccionado                    
                    $("#idRestaurante").val(lc_rest);
                    //Seteamos el nombre del restaurante  
                    $("#nameRestaurante").val(txt_value_res[1]);
                    //Llama a la funcion que cargara la data de las columnas de valor
                    fn_cargarDataCamposAutoimpresor();
                });
            }
        }
    });
}

/**
 * Funcion que permite obtener los datos y ubicarlos 
 * @returns {undefined}
 */
function fn_cargarDataCamposAutoimpresor() {
    ACUM_VALOR = "";
    send = {"infAutoimpresorDatos": 1};
    send.rst_id = $('#idRestaurante').val();
    $.getJSON("../adminAutoImpresora/configadminAutoImpresora.php", send, function (datos) {
        if (datos.str > 0)
        {
            str = "";
            acum = "1";
            intDescripcion = "0";
            for (var i = 0; i < datos.str; i++) {
                if (parseInt(datos[i]['intDescripcion']) === (parseInt(acum))) {
                    if (datos[i]['Descripcion'].indexOf('arse') !== -1) {
                        str += "0-";
                    } else {
                        str += datos[i]['valor'] + "-";
                    }
                } else {
                    ACUM_VALOR += str + "" + acum + ",";
                    str = "";
                    if (datos[i]['Descripcion'].indexOf('arse') !== -1) {
                        str += "0-";
                    } else {
                        str += datos[i]['valor'] + "-";
                    }
                }
                acum = datos[i]['intDescripcion'];
            }
            ACUM_VALOR += str + "" + acum + ",";
        }
    });
}

/**
 * Funcion que permite obtener el detalle de los restaurantes activos e inactivos para los auto impresores
 * @param {type} lc_rest
 * @param {type} accion
 * @returns {undefined}
 */
function fn_cargarDetalleInactivos(lc_rest, accion) {
    send = {"infRestaurante": 1};
    send.lc_res = lc_rest;
    send.estado = accion;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminAutoImpresora/configadminAutoImpresora.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#tabla_autoimpresores").show();
                html = "<thead><tr class='active'>";
                for (var i = 0; i < descripciones.length; i++) {
                    html += "<th align='center' style='width:180px; text-align:center;'>" + descripciones[i] + "</th>";
                }
                html += "<th align='center' style='width:80px; text-align:center;' >Activo</th></tr>";
                html += "</tr></thead>";
                $("#detalle_autoimpresores").empty();

                for (var i = 0; i < datos.str; i++) {
                    html += "<tr class='tabla_detalleMov' id='" + i + "' style='cursor:pointer'";
                    html += "onclick=fn_seleccionclick(" + i + ") ondblclick=fn_seleccionModificar('" + datos[i]['ID_ColeccionDeDatosRestaurante'] + "','" + datos[i]['ID_ColeccionRestaurante'] + "','" + datos[i]['intDescripcion'] + "') >";
                    if (datos[i]['dato']) {
                        $("#detalle_autoimpresores").html(fn_setBodyTable(datos[i]['dato']));
                    }
                    if (datos[i]['isActive'] === 1) {
                        html += "<td align='center'  style='width:80px' ><input type='checkbox' checked='checked' disabled /></td></tr>";
                    } else
                    if (datos[i]['isActive'] === 0) {
                        html += "<td align='center'  style='width:80px' ><input type='checkbox' disabled /></td></tr>";
                    }
                }

                $("#detalle_autoimpresores").html(html);

                $('#detalle_autoimpresores').dataTable(
                        {//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
                            'destroy': true
                        }
                );
                $("#detalle_autoimpresores_length").hide();
                $("#detalle_autoimpresores_paginate").addClass('col-xs-10');
                $("#detalle_autoimpresores_info").addClass('col-xs-10');
                $("#detalle_autoimpresores_length").addClass('col-xs-6');
            } else {
                $("#tabla_autoimpresores").show();
                html = "<thead><tr class='active'>";
                for (var i = 0; i < descripciones.length; i++) {
                    html += "<th align='center' style='width:180px; text-align:center;'>" + descripciones[i] + "</th>";
                }
                html += "<th align='center' style='width:80px; text-align:center;' >Activo</th></tr>";
                html += "<tr><td colspan='7'> No existen datos para visualizar.</td></tr>";
                html += "</tr></thead>";
                $("#detalle_autoimpresores").html(html);
                //alertify.alert("No existen estaciones configuradas para este restaurante");
                alertify.error("No existen datos");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error("Error al capturar la data." + errorThrown);
        }
    });
}

/**
 * Funcion que permite crear las columnas de la tabla
 * @param {type} columnas
 * @returns {html|String}
 */
function fn_setBodyTable(columnas) {
    ID_VALOR_COLUMNAS = columnas.split(",");
    for (var i = 0; i < ID_VALOR_COLUMNAS.length; i++) {
        if (ID_VALOR_COLUMNAS[i] !== '') {
            html += "<td align='center'  style='width:160px;'>" + ID_VALOR_COLUMNAS[i] + "&nbsp;</td>";
        }
    }
    return html;
}

/**
 * Funcion que permite pintar una fila de la tabla al ser seleccionada
 * @param {type} fila
 * @returns {undefined}
 */
function fn_seleccionclick(fila) {
    $("#detalle_autoimpresores tr").removeClass("success");
    $("#" + fila + "").addClass("success");
}


/**
 * Funcion para modificar los elementos de la tabla al hacer doble click mendiante el numero de autorizacion
 * @param {type} idAuthorizacion
 * @param {type} descripcion  
 * @returns {undefined}
 */
function fn_seleccionModificar(idColecciondedatosrestaurante, idColeccionrestaurante, intDescripcion) {
    Accion = 2;
    $('#titulomodalModificar').text($("#selrest option:selected").html());
    $('#ModalModificar').modal('show');
    $("#idcolecciondedatosrestaurante-M").val(idColecciondedatosrestaurante);
    $("#idcoleccionrestaurante-M").val(idColeccionrestaurante); //idcoleccionsecuencial
    fn_cargarAutoImpresoraModificar(intDescripcion);

}


/**
 * Funcion que carga la data en el modal de auto impresores para despues ser modificados
 * @param {type} intDescripcion  
 * @returns {undefined}
 */
function fn_cargarAutoImpresoraModificar(intDescripcion) {
    Accion = 2;
    send = {"infAutoImpresor": 1};
    send.cod_RST = intDescripcion;
    send.rst_id = $('#idRestaurante').val();
    $.getJSON("../adminAutoImpresora/configadminAutoImpresora.php", send, function (datos) {
        if (datos.str > 0)
        {
            //Se valida si el dato estado de la BDD esta activo o inactivo
            if (datos.isActive === 1) {
                $("#checkModificar").html("<b>Est&aacute; Activo?:<input type=\"checkbox\" id=\"opcion_Modificar\" checked=\"checked\" /> </b>");
            } else {
                $("#checkModificar").html("<b>Est&aacute; Activo?:<input type=\"checkbox\" id=\"opcion_Modificar\" /> </b>");
            }
            //alert(datos.dato);
            //Se inicializa el vector
            VALORES = [];
            //Se elimina la ultima coma de los datos  
            VALORES = (datos.dato.slice(0, -1)).split(",");
            contador = 0;
            secuencial = '';
            $(".autoimpresoresM").each(function ()
            {
                $(this).val(VALORES[contador]);
                if (($(this).attr('id').slice(0, -2)).slice(($(this).attr('id').slice(0, -2)).length - 1, ($(this).attr('id').slice(0, -2)).length) === 'B') {
                    if ($(this).val() === '1') {
                        $('.' + ($(this).attr('id').replace('_', '-').replace(' ', ''))).bootstrapSwitch('state', true);
                    } else {
                        $('.' + ($(this).attr('id').replace('_', '-').replace(' ', ''))).bootstrapSwitch('state', false);
                    }
                }
                contador = contador + 1;
            });
            //identificador del secuencial
            $("#idcoleccionsecuencial").val(datos.intDescripcion);
        }
    });
}

/*
 * Funcion que permite validar los datos modficados o nuevos 
 * @param {type} Accion
 * @returns {undefined}
 */
function fn_guardarCambios(Accion) {

    if (Accion === 1) { //Nuevo   
        if (validaCamposLlenos())
        {
            arrayValoresAutoimpresores = new Array();
            inicioSecuencial = 0;
            finSecuencial = 0;
            identificador = 0;
            $(".autoimpresores").each(function ()
            {
                if (($(this).attr('id').slice(0, -4)).indexOf('Ini') !== -1) {
                    inicioSecuencial = $(this).val();
                }
                if (($(this).attr('id').slice(0, -4)).indexOf('Fin') !== -1) {
                    finSecuencial = $(this).val();
                }
                //Capturamos el id del activador secuencial
                if (($(this).attr('id').slice(0, -4)).indexOf('arse') !== -1 &&
                        (($(this).attr('id').substr(($(this).attr('id').length) - 1, $(this).attr('id').length)) === 'B')) {

                    if ($('.' + ($(this).attr('id').replace('_', '-').replace(' ', ''))).bootstrapSwitch('state')) {
                        identificador = "1";
                    } else {
                        identificador = "0";
                    }
                }
                //Se obtiene el valor de todos los campos select
                if (($(this).attr('id').substr(($(this).attr('id').length) - 1, $(this).attr('id').length)) === 'B') {
                    value = '';
                    if ($('.' + ($(this).attr('id').replace('_', '-').replace(' ', ''))).bootstrapSwitch('state')) {
                        value = '1';
                    } else {
                        value = 0;
                    }
                    arrayValoresAutoimpresores.push(value + '-' + $(this).attr('id'));
                } else {
                    arrayValoresAutoimpresores.push($(this).val() + '-' + $(this).attr('id'));
                }

            });
        } else
        {
            alertify.error("Faltan algunos campos por ingresar : " + ID_CAMPOS_FORMULARIO);
            return false;
        }
        var opcion = $('#option_new').is(':checked');
        var idRestaurante = $('#idRestaurante').val();
        var bandera;
        if (opcion === true) {
            bandera = 1;
        } else {
            bandera = 0;
        }
        //Validacion de variables           
        if (parseInt(inicioSecuencial) >= parseInt(finSecuencial)) {
            alertify.error("El secuencial inicial debe ser menor al secuencial final");
        } else {
            /* En el primer proceso guardamos el nuevo registro*/
            fn_guardarAutoImpresor(idRestaurante, arrayValoresAutoimpresores, bandera, identificador);
            /* Cerramos la ventana modal*/
            $('#ModalNuevo').modal('hide');
            /* En el segundo proceso actualiza la tabla para que se reflejen los cambios*/
            fn_cargarDetalleInactivos(idRestaurante, 10);
        }

    } else
    if (Accion === 2) { //Modificar 

        if (validaCamposModificados())
        {
            arrayValoresAutoimpresores = new Array();
            inicioSecuencial = 0;
            finSecuencial = 0;
            numeroFormulario = '';
            $(".autoimpresoresM").each(function ()
            {
                //Se obtiene el secuencial inicial
                if (($(this).attr('id').slice(0, -6)).indexOf('Ini') !== -1) {
                    inicioSecuencial = $(this).val();
                }
                //Se obtiene el secuencial final       
                if (($(this).attr('id').slice(0, -6)).indexOf('Fin') !== -1) {
                    finSecuencial = $(this).val();
                }

                //Se busca por el filtro de Número de formulario 
                if (($(this).attr('id').slice(0, -6)).indexOf('mero') !== -1) {
                    numeroFormulario = $(this).val();
                }

                //Se obtiene el valor de todos los campos select
                if (($(this).attr('id').slice(0, -2)).slice(($(this).attr('id').slice(0, -2)).length - 1, ($(this).attr('id').slice(0, -2)).length) === 'B') {
                    value = '';
                    if ($('.' + ($(this).attr('id').replace('_', '-').replace(' ', ''))).bootstrapSwitch('state')) {
                        value = '1';
                    } else {
                        value = 0;
                    }
                    arrayValoresAutoimpresores.push(value + '-' + ($(this).attr('id')).slice(0, $(this).attr('id').length - 2));
                } else {
                    arrayValoresAutoimpresores.push($(this).val() + '-' + ($(this).attr('id')).slice(0, $(this).attr('id').length - 2));
                }
            });

        } else
        {
            alertify.error("Faltan algunos campos por ingresar : " + ID_CAMPOS_FORMULARIO);
            return false;
        }

        var opcion = $('#opcion_Modificar').is(':checked');
        var idRestaurante = $('#idRestaurante').val();
        var idColecciondedatosRestaurante = $('#idcolecciondedatosrestaurante-M').val();
        var idColeccionRestaurante = $('#idcoleccionrestaurante-M').val();
        var identificador = $("#idcoleccionsecuencial").val();
        var bandera;
        if (opcion === true) {
            bandera = 1;
        } else {
            bandera = 0;
        }
        if (parseInt(inicioSecuencial) >= parseInt(finSecuencial)) {
            alertify.error("El secuencial inicial debe ser menor al secuencial final");
        } else {
            alertify.confirm("¿ Está seguro de realizar modificación al secuencial ?");
            $("#alertify-ok").click(function () {
                /* En el primer proceso modifica la tabla*/
                fn_modificarAutoImpresor(idColecciondedatosRestaurante, idColeccionRestaurante, idRestaurante, arrayValoresAutoimpresores, bandera, numeroFormulario, identificador);
                /* Cerramos la ventana modal*/
                $('#ModalModificar').modal('hide');
                /* En el segundo proceso actualiza la tabla para que se reflejen los cambios*/
                fn_cargarDetalleInactivos(idRestaurante, 10);
                return true;
            });
            $("#alertify-cancel").click(function () {
                return false;
            });


        }

    }

}

/**
 * Funcion que valida si los campos de ingreso están vacios
 * @returns {Boolean}
 */
function validaCamposLlenos() {
    ID_CAMPOS_FORMULARIO = '';
    var camposRellenados = true;
    $(".autoimpresores").each(function () {
        var $this = $(this);
        if ($this.val().length <= 0 && ($(this).attr('id').substr(($(this).attr('id').length) - 1, $(this).attr('id').length)) !== 'B') {
            camposRellenados = false;
            ID_CAMPOS_FORMULARIO = $(this).attr('id').split("_")[0];
            return false;
        }
    });
    if (camposRellenados == false) {
        return false;
    } else {
        return true;
    }
}

/**
 * Funcion que valida si los campos modificados están vacios
 * @returns {Boolean}
 */
function validaCamposModificados()
{
    ID_CAMPOS_FORMULARIO = '';
    var camposRellenados = true;
    $(".autoimpresoresM").each(function () {
        var $this = $(this);
        if ($this.val().length <= 0 &&
                ($(this).attr('id').slice(0, -2)).slice(($(this).attr('id').slice(0, -2)).length - 1, ($(this).attr('id').slice(0, -2)).length) !== 'B') {
            camposRellenados = false;
            ID_CAMPOS_FORMULARIO = $(this).attr('id').split("_")[0];
            return false;
        }
    });
    if (camposRellenados == false) {
        return false;
    } else {
        return true;
    }
}

/*
 * Funcion para guardar los nuevos datos de un auto imrpesor
 * @param {type} numAuth 
 * @param {type} fechaValidez 
 * @param {type} txtinicioSec
 * @param {type} txtfinSec
 * @param {type} bandera
 * @returns {undefined}
 */
function fn_guardarAutoImpresor(idRest, arrayValoresAutoimpresores, bandera, identificador) {
    send = {"guardarDatosAutoImpresoras": 1};
    send.accion = 2;//Significa que se guardara en el SP  
    send.activo = bandera;
    send.id_rest = idRest;
    send.identificador = identificador;
    send.valores = arrayValoresAutoimpresores.toString() + ",";
    if (identificador === "1") {
        send.campos_adicionales = ACUM_VALOR;
    } else {
        send.campos_adicionales = "";
    }
    //alert(arrayValoresAutoimpresores);
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminAutoImpresora/configadminAutoImpresora.php", data: send,
        success: function (datos) {
            //Mensaje para la interfaz
            alertify.success("Los datos han sido guardados satisfactoriamente.");
            //Funcion para cargar nuevamente el valor a actualizar
            fn_cargarDataCamposAutoimpresor();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error("No se ha podido guardar los elementos." + textStatus);
        }
    });
}

/*
 * Funcion para guardar los cambios en la tabla autoimpresores
 * @param {type} idAutorizacion
 * @param {type} fechaInicial
 * @param {type} fechaFinal
 * @param {type} inicioSecuencial
 * @param {type} finSecuencial
 * @param {type} opcion
 * @returns {undefined}
 */
function fn_modificarAutoImpresor(idColecciondedatosRestaurante, idColeccionRestaurante, idRestaurante, arrayValoresAutoimpresores, bandera, numeroFormulario, identificador) {
    send = {"guardarDatosModificarAutoImpresoras": 1};
    send.activo = bandera;
    send.id_rest = idRestaurante;
    send.valores = arrayValoresAutoimpresores.toString() + ",";
    send.idcoleccionrestaurante = idColeccionRestaurante;
    send.idColecciondedatosRestaurante = idColecciondedatosRestaurante;
    send.numFormulario = numeroFormulario;
    send.identificadorSecuencial = identificador;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminAutoImpresora/configadminAutoImpresora.php", data: send,
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
        //Se limpian los datos que han sido guardados    
        $('#option_new').prop('checked', true);
        //Vacia los campos del modal nuevo
        $(".autoimpresores").each(function ()
        {
            $(this).val("");

        });
        /*Valida que este seleccionado un restaurante*/
        if ($("#selrest").val() == 0) {
            alertify.error("Debe seleccionar un Restaurante");
            $("#selrest").focus();
            return false;
        }
        //Funcion que cargara la informacion del combo para Empresa
        //fn_cargarEmpresa();
        //Se cargara la informacion para combo de tipo identificador
        //$("#sel_tipDocumento").html("");
        $('#botonesguardarcancelar').show();
        $('#botonessalir').hide();
        ////////////Colocar foco en cualquier campo de la modal////////////////// CP
        $('#ModalNuevo').on('shown.bs.modal', function () {
            $("#txt_rstNuevo").focus();
        });

        $('#ModalNuevo').modal('show');

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

