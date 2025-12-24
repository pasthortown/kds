/* global alertify */

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jose Fernandez///////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuraci�n preguntas sugeridas//////////////
///////TABLAS INVOLUCRADAS: Descuentos ////////////////////////////////////////
///////FECHA CREACION: 22-04-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 01/06/2015///////////////////////////////////
///////USUARIO QUE MODIFICO: Jose Fernandez////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Aplicacion nuevo estilo, configuraciones en //
////////////////////////////////pantalla modal, cambio de etiquetas///////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

var lc_control = -1;
var grupos = 10;
var lc_banderaChecks = -1; //VARIABLE PARA LOS RADIO BUTTON
var lc_estado = ''; //VARIABLE DE LA CLASIFICACION DE PLUS 

function justNumbers(e) {
    var keynum = window.event ? window.event.keyCode : e.which;
    if ((keynum == 8) || (keynum == 46)) {
        return true;
    } else {
        return /\d/.test(String.fromCharCode(keynum));
    }
}

$(document).ready(function() {
    $("#par_numplu").hide();
    $("#img_buscar").hide();
    $("#img_remove").hide();
    $("#img_remove").hide();
    fn_btn("modificar", 0);
    fn_btn("guardar", 0);
    fn_btn("cancelar", 0);
    fn_btn("eliminar", 0);
    fn_btn("agregar", 1);
    fn_muestraDetallePreguntasPorEstado(0, grupos + 1, "activo");
    $("#inicio_preguntas").hide();
    $("#inicio_Respuestas").hide();
    $("#mdl_productos").hide();
    $("#mdl_productosModificados").hide();
    $("#div_modificar").hide();

    $("#par_numplu").keyup(function(event) {
        if (event.keyCode == '13') {
            fn_muestraDetallePreguntasPorEstado(0, grupos + 1, "activo");
        }
    });

    $("#par_numplu").keypress(function() {
        if ($("#par_numplu").val() == '') {
            $("#img_buscar").show();
            $("#img_remove").hide();
        } else {
            $("#img_remove").show();
            $("#img_buscar").hide();
        }
        fn_muestraDetallePreguntasPorEstado(0, grupos + 1, "activo");
    });

    $("#inpPsgDescripcionPregunta").keyup(function() {
        $("#inpPsgDescripcionPreguntaPOS").val($("#inpPsgDescripcionPregunta").val());
    });

    $("#inpPsgDescripcionPregunta").keyup(function() {
        $("#inpPsgDescripcionPreguntaPOS").val($("#inpPsgDescripcionPregunta").val());
    });

    cargarGruposPreguntasSugeridas();

});

function fn_cerrarModificar() {
    fn_muestraDetallePreguntasPorEstado(0, grupos + 1, lc_estado);
    fn_btn("agregar", 1);
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

function fn_agregar() {
    fn_btn("cancelar", 1);
    fn_btn("agregar", 1);
    fn_btn("eliminar", 0);
    fn_btn("modificar", 0);

    $("#inicio_preguntas").show();
    lc_control = 1;
    $("#divcadenaNuevo").hide();
    fn_irAmostrarRespuestaNuevo();
    fn_limpiaInfo();
}

function fn_muestraDetallePreguntas() {
    var send;
    var cargarDetallePreguntasInicio = { "cargarDetallePreguntasInicio": 1 };
    lc_banderaChecks = 1;
    $('#mdl_rdn_pdd_crgnd').show();
    var html = "<thead><tr class='text-center active'><th class='text-center' width='30%'>Descripción</th><th class='text-center' width='30%'>Descripción en POS</th><th class='text-center' width='10%'>Mínimo Respuestas</th><th class='text-center' width='10%'>Máximo Respuestas</th><th class='text-center' width='20%'>Activo</th></tr></thead>";
    send = cargarDetallePreguntasInicio;
    send.cadenaDetalle = $("#cdn_id").val();
    send.aBuscar = $("#par_numplu").val();
    $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
        if (datos.str > 0) {
            fn_btn('agregar', 1);
            $("#inicio_preguntas").show();
            $("#detalle_preguntas").empty();
            for (var i = 0; i < datos.str; i++) {
                html += "<tr id='" + i + "' style='cursor:pointer;'";
                html += " onclick='fn_seleccion(" + i + ",\"" + datos[i]["psug_id"] + "\")' ondblclick='modificar(\"" + datos[i]["psug_id"] + "\", \"" + datos[i]["idGrupo"] + "\")'>";
                html += "<td style='text-align:left'>" + datos[i]["pre_sug_descripcion"] + "&nbsp;</td>";
                html += "<td style='text-align:left'>" + datos[i]["psug_descripcion_pos"] + "&nbsp;</td>";
                html += "<td align='center'  style='width:80px;'>" + datos[i]["psug_resp_minima"] + "&nbsp;</td>";
                html += "<td align='center'  style='width:80px;'>" + datos[i]["psug_resp_maxima"] + "&nbsp;</td>";
                html += "<td>" + datos[i]["grupo"] + "</td>";
                if (datos[i]["Estado"] == "Activo") {
                    html += "<td align='center'><input type='checkbox' checked='checked' disabled='disabled'/></td></tr>";
                } else {
                    html += "<td align='center'><input type='checkbox' disabled='disabled'/></td></tr>";
                }
                $("#detalle_preguntas").html(html);
                $('#detalle_preguntas').dataTable({ "destroy": true });
                $("#detalle_preguntas_length").hide();
            }
        } else {
            alertify.error("No existen registros");
            $("#detalle_preguntas").html(html);
        }
        $('#mdl_rdn_pdd_crgnd').hide();
    });
}

/* Aqui estoy */
function fn_muestraDetallePreguntasPorEstado(inicio, fin, estado) {
    var send;
    var cargarDetallePreguntasInicioPorEstado = { "cargarDetallePreguntasInicioPorEstado": 1 };
    lc_estado = estado;
    lc_banderaChecks = 2;
    $('#mdl_rdn_pdd_crgnd').show();
    var html = "<thead><tr class='text-center active'><th width='30%' class='text-center'>Descripción</th><th width='30%' class='text-center'>Descripción en POS</th><th class='text-center'>Mínimo Respuestas</th><th class='text-center'>Máximo Respuestas</th><th class='text-center'>Grupo</th><th class='text-center'>Activo</th></tr></thead>";
    send = cargarDetallePreguntasInicioPorEstado;
    send.cadenaPorEstado = $("#cdn_id").val();
    send.aBuscarEstado = $("#par_numplu").val();
    send.opcionEstado = estado;
    send.inicio = inicio;
    send.fin = fin;
    $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
        if (datos.str > 0) {
            fn_btn('agregar', 1);
            $("#inicio_preguntas").show();
            $("#detalle_preguntas").empty();
            for (var i = 0; i < datos.str; i++) {
                html += "<tr id='" + i + "' style='cursor:pointer;'";
                html += " onclick='fn_seleccion(" + i + ",\"" + datos[i]["psug_id"] + "\")' ondblclick='modificar(\"" + datos[i]["psug_id"] + "\", \"" + datos[i]["idGrupo"] + "\")'>";
                html += "<td style='text-align:left'>" + datos[i]["pre_sug_descripcion"] + "&nbsp;</td>";
                html += "<td style='text-align:left'>" + datos[i]["psug_descripcion_pos"] + "&nbsp;</td>";
                html += "<td align='center'>" + datos[i]["psug_resp_minima"] + "&nbsp;</td>";
                html += "<td align='center'>" + datos[i]["psug_resp_maxima"] + "&nbsp;</td>";
                html += "<td>" + datos[i]["grupo"] + "</td>";
                if (datos[i]["Estado"] == "Activo") {
                    html += "<td align='center'><input type='checkbox' checked='checked' disabled='disabled'/></td></tr>";
                } else {
                    html += "<td align='center'><input type='checkbox' disabled='disabled'/></td></tr>";
                }
                // $("#inGrupoPregunta").val(datos[i]["idGrupo"]);
                $("#detalle_preguntas").html(html);
                $('#detalle_preguntas').dataTable({ "destroy": true });
                $("#detalle_preguntas_length").hide();
            }
        } else {
            alertify.error("No existen registros");
            $("#detalle_preguntas").html(html);
        }
        $("#mdl_rdn_pdd_crgnd").hide();
    });
}

var modificar = function(idPregunta, idGrupoPregunta) {
    $("#mdl_rdn_pdd_crgnd").show();
    $("#hid_psug_id").val(idPregunta);

    fn_cargarPreguntasAgregadasPlus(idPregunta);

    var send;
    var cargaPreguntaModificada = { "cargaPreguntaModificada": 1 };
    send = cargaPreguntaModificada;
    send.psuModificado = idPregunta;
    $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
        if (datos.str > 0) {
            fn_cargaRespuestasPreguntaModificada();
            $("#mdlPreguntasSugeridasTitulo").text(datos.pre_sug_descripcion);
            $("#inpPsgDescripcionPregunta").val((datos.pre_sug_descripcion).trim());
            $("#inpPsgDescripcionPreguntaPOS").val(datos.psug_descripcion_pos);
            $("#inpPsgCantidadMinimaRespuestas").val(datos.psug_resp_minima);
            $("#inpPsgCantidadMaximaRespuestas").val(datos.psug_resp_maxima);
            $("#inpPsgEstado").prop('checked', false);
            if (datos.Estado == 'Activo') {
                $("#inpPsgEstado").prop('checked', true);
            }
            $('#mdlPreguntasSugeridas').modal("show");
            $('#tabPreguntasSugeridas').show();
            $('#tabRespuestas').show();
            $('#tabPlusAsociados').show();
            $("#tabs li").removeClass("active");
            $("#tabsPsg div").removeClass("active fade in");
            $("#tabPreguntasSugeridas").addClass("active");
            $("#tabPsgPreguntasSugeridas").addClass("active fade in");
            $("#inGrupoPregunta").val(idGrupoPregunta);
            $("#btnPsgGuardarCambios").attr("onclick", "mergePreguntaSugerida(0, '" + idPregunta + "');");
        }
        $("#mdl_rdn_pdd_crgnd").hide();
    });
};

var agregar = function() {

    $("#inpPsgEstado").prop("checked", "checked");

    $("#mdl_rdn_pdd_crgnd").show();
    $('#mdlPreguntasSugeridas').modal("show");

    $("#mdlPreguntasSugeridasTitulo").text("Nueva Pregunta Sugerida");

    $("#tblPsgPlusAsociados").html("<tr class='bg-primary'><td align='center'><h5># Plu</h5></td><td align='center'><h5>Plu Asociado a Pregunta</h5></td><td align='center' style='width:50px'></td></tr>");

    $("#tblPsgRespuestas").html("<thead><tr class='bg-primary'><th align='center'>NumPlu</th><th align='center'>Producto</th><th align='center'>Respuesta</th><th width='10%' align='center'>Opcion</th></tr></thead>");

    $("#inpPsgDescripcionPregunta").val("");
    $("#inpPsgCantidadMaximaRespuestas").val(0);
    $("#inpPsgCantidadMinimaRespuestas").val(0);
    $("#inpPsgDescripcionPreguntaPOS").val("");

    $("#hid_psug_id").val("");
    $("#btnPsgGuardarCambios").attr("onclick", "mergePreguntaSugerida(1, '');");
    $("#mdl_rdn_pdd_crgnd").hide();
};

function fn_seleccion(fila, psug_id) {
    $("#detalle_preguntas tr").removeClass("success");
    $("#" + fila + "").addClass("success");
    $("#hid_psug_id").val(psug_id);
}

function fn_irAmostrarRespuestaNuevo() {
    var send;
    var validaConfiguracionRespuestasMaximas = { "validaConfiguracionRespuestasMaximas": 1 };
    send = validaConfiguracionRespuestasMaximas;
    send.cdnvalidaConfiguracionRespuestasMaximas = $("#cdn_id").val();
    $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
        if (datos.numero_maximo_respuestas > 0) {
            $("#tabPreguntasSugeridas").show();
            $("#tabRespuestas").hide();
            $("#tabPlusAsociados").hide();
            $("#tabs li").removeClass("active");
            $("#tabsPsg div").removeClass("active fade in");
            $("#tabPreguntasSugeridas").addClass("active");
            $("#tabPsgPreguntasSugeridas").addClass("active fade in");
            $("#mdlPreguntasSugeridasTitulo").html("Nueva Pregunta Sugerida");
            $("#inpPsgCantidadMaximaRespuestas").val(datos.numero_maximo_respuestas);
            $("#btnPsgGuardarCambios").attr("onclick", "fn_aplicarRespuesta()");
            $("#mdlPreguntasSugeridas").modal("show");
        } else {
            alertify.error("No existe la configuración de número de respuestas máxima para esta cadena");
            return false;
        }
    });
}

//NUEVO: CARGA LOS PLUS PARA AGREGAR EN LAS RESPUESTAS 
function fn_cargaPlusRespuesta(opcion) {
    var send;
    var cargaPlus = { "cargaPlus": 1 };
    var tipo = opcion;
    var html = "";
    fn_cargando(1);

    send = cargaPlus;
    send.cdn = $("#cdn_id").val();
    send.tipo_producto = tipo;
    $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
        $('#selPlusNuevo').empty();
        if (datos.str > 0) {
            fn_cargando(0);
            $("#mdlPreguntasSugeridas").modal("hide");
            $("#selPlusNuevo").append("<option selected value='0'>----Seleccione Producto----</option>");
            for (var i = 0; i < datos.str; i++) {
                html = "<option value='" + datos[i]["plu_id"] + "' name='" + datos[i]["plu_descripcion"] + "'>" + datos[i]["plu_num_plu"] + " - " + datos[i]["plu_descripcion"] + "</option>";
                $("#selPlusNuevo").append(html);
            }
            $("#selPlusNuevo").trigger("chosen:updated");
            $("#selPlusNuevo").chosen();
            $("#selPlusNuevo_chosen").css("width", "480");

            $("#selPlusNuevo").change(function() {
                $("#plu").val($("#selPlusNuevo").val());
            });

            $("#selPlusNuevo").change(function() {
                name = $(this).find('option:selected').attr("name");
                $("#txt_nombrerespuestanueva").val(name);
            });

            $("#mdl_productosModificados").modal("show");
        } else {
            fn_cargando(0);
            alertify.error("No existen plus para esta cadena");
            $("#selPlusNuevo").trigger("chosen:updated");
        }
    });
}

//NUEVO: VALIDA NUMERO DE RESPUESTAS OBTENIDAS POR COLECCION CADENA
function fn_validaNumeroRespuestas() {
    var send;
    var validaNumeroDerespuestas = { "validaNumeroDerespuestas": 1 };
    var tipo = "Todo";
    send = validaNumeroDerespuestas;
    send.cdnNumeroRespuestas = $("#cdn_id").val();
    send.psugNumero = $("#pre_id").val();
    $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
        if (datos.str > 0) {
            if (datos.respuestasasociadas < datos.numero_maximo_respuestas) {
                fn_cargaPlusRespuesta(tipo);
                $("#txt_nombrerespuestanueva").val("");
            } else {
                alertify.error("<b>Alerta</b> Superó el límite de respuestas");
                return false;
            }
        }
    });
}

//NUEVO: APLICA RESPUESTAS EN LA PREGUNTA SUGERIDA
function fn_aplicarRespuesta() {
    var send;
    var insertaCabeceraPregunta = { "insertaCabeceraPregunta": 1 };
    var Accion = "IP";
    if ($("#inpPsgDescripcionPregunta").val() !== "") {
        if ($("#inpPsgDescripcionPreguntaPOS").val() !== "") {
            if ($("#inpPsgCantidadMinimaRespuestas").val() !== "") {
                if ($("#inpPsgCantidadMaximaRespuestas").val() !== "") {
                    if ($("#inpPsgCantidadMinimaRespuestas").val() <= $("#inpPsgCantidadMaximaRespuestas").val()) {
                        if (lc_control == 1) {
                            $("#mdl_rdn_pdd_crgnd").show();
                            $("#mdlPreguntasSugeridas").hide();
                            send = insertaCabeceraPregunta;
                            send.accion = Accion;
                            send.cdnCabecera = $("#cdn_id").val();
                            send.descCabecera = $("#inpPsgDescripcionPregunta").val();
                            send.descPos = $("#inpPsgDescripcionPreguntaPOS").val();
                            send.maxCabecera = $("#inpPsgCantidadMaximaRespuestas").val();
                            send.minCabecera = $("#inpPsgCantidadMinimaRespuestas").val();
                            send.respID = 0;
                            send.preElimina = 0;
                            send.plu = 0;
                            send.estado = "x";
                            send.respuesta = "x";
                            $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
                                if (datos.str > 0) {
                                    $("#pre_id").val(datos.psug_id);
                                    fn_muestraDetallePreguntasPorEstado(0, grupos + 1, "activo");
                                    $("#opciones_estado label").removeClass("active");
                                    $("#check_activos").addClass("active");

                                    fn_validaNumeroRespuestas();
                                    $("#btnPsgGuardarCambios").attr("onclick", "aceptar()");
                                    $("#tabPreguntasSugeridas").show();
                                    $("#tabRespuestas").show();
                                    $("#tabPlusAsociados").show();
                                }
                                $("#mdl_rdn_pdd_crgnd").hide();
                            });
                        } else {
                            fn_validaNumeroRespuestas();
                        }
                    } else {
                        alertify.error("El número mínimo de respuestas no puede ser mayor al número máximo de respuestas");
                    }
                } else {
                    alertify.error("Ingresar cantidad máxima de respuestas");
                }
            } else {
                alertify.error("Ingresar cantidad mínima de respuestas");
            }
        } else {
            alertify.error("Ingresar descripción de la pregunta en POS");
        }
    } else {
        alertify.error("Ingresar descripción de la pregunta");
    }
}

function fn_cierraNuevos() {
    $("#mdl_productosModificados").modal("hide");
    $("#mdlPreguntasSugeridas").modal("hide");
}

var eliminarRespuesta = function(idProducto) {
    $("#idrespuesta_" + idProducto).remove();
    var i = 1;
    $('#tblPsgRespuestas >tbody tr').each(function() {
        $("#idrespuesta_" + this.id).attr("orden", (i));
        i++;
    });
};

//Aqui estoy
function fn_grabaRespuestaModificada() {
    var idPregunta = $("#hid_psug_id").val();
    var idProducto = $("#plu").val();
    var descripcionCanal = $("#txt_nombrerespuesta").val();
    var numPlu = $("#selPlus option:selected").attr("numplu");
    var descripcion = $("#selPlus option:selected").attr("descripcion");
    var orden = $('#tblPsgRespuestas >tbody >tr').length + 1;
    $("#tblPsgRespuestas").append("<tr id='idrespuesta_" + idProducto + "' orden=\"" + orden + "\" ondragover=\"allowDrop(event)\" ondragstart=\"drag(event)\" ondrop=\"drop(event)\" draggable=\"true\">><td style='text-align: center;'>" + numPlu + "</td><td>" + descripcionCanal + "</td><td><a data-type='text' href='#' id='res_dscrpcn" + idProducto + "'>" + descripcion + "</a></td><td style='text-align: center;'><input type='button' name='eliminar' class='opcionAgregado' onclick='eliminarRespuesta(" + idProducto + ");' style=\"background: #666666 url(\'../../imagenes/admin_resources/btn_eliminar.png\') 1px 1px no-repeat; height: 33px; width: 33px;\"/></td>");

    $('#res_dscrpcn' + idProducto).editable({});

    $("#mdl_productos").modal("hide");
    $("#mdlPreguntasSugeridas").modal("show");
}

function fn_eliminarRespuesta(resp_id) {
    var send;
    var eliminaRespuesta = { "eliminaRespuesta": 1 };
    $("#mdl_rdn_pdd_crgnd").show();
    var Accion = "ER";
    send = eliminaRespuesta;
    send.accion = Accion;
    send.cdnCabecera = $("#cdn_id").val();
    send.descCabecera = "x";
    send.maxCabecera = 0;
    send.minCabecera = 0;
    send.descPos = "x";
    send.respID = resp_id;
    send.preElimina = 0;
    send.plu = 0;
    send.estado = "x";
    send.respuesta = "x";
    $.getJSON("../adminpregunta ssugeridas/config_preguntassugeridas.php", send, function(datos) {
        lc_control = 3;
        fn_cargaDetalleDeRespuestas();
        $("#mdl_rdn_pdd_crgnd").hide();
    });
}

function fn_eliminarRespuestaModificada(resp_id) {
    var send;
    var eliminaRespuesta = { "eliminaRespuesta": 1 };
    $('#mdl_rdn_pdd_crgnd').show();
    var Accion = "ER";
    send = eliminaRespuesta;
    send.accion = Accion;
    send.cdnCabecera = $("#cdn_id").val();
    send.descCabecera = "x";
    send.maxCabecera = 0;
    send.minCabecera = 0;
    send.descPos = "x";
    send.respID = resp_id;
    send.preElimina = 0;
    send.plu = 0;
    send.estado = "x";
    send.respuesta = "x";
    $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
        lc_control = 3;
        fn_cargaRespuestasPreguntaModificada();
        $("#mdl_rdn_pdd_crgnd").hide();
    });
}

//NUEVO: GUARDA LAS RESPUESTAS SELECCIONADAS PARA LA PREGUNTA SUGERIDA
function fn_grabaRespuesta() {
    var send;
    var grabaRespuesta = { "grabaRespuesta": 1 };
    var Accion = "IR";
    if ($("#selPlusNuevo").val() !== "0") {
        $('#mdl_rdn_pdd_crgnd').show();
        var html = "<tr class='bg-primary'><td width='50%' align='center'># Plu</td><td width='30%' align='center'>Respuesta</td><td width='10%' align='center'>Eliminar</td></tr>";
        send = grabaRespuesta;
        send.accion = Accion;
        send.cdnCabecera = $("#cdn_id").val();
        send.descCabecera = 'x';
        send.maxCabecera = 0;
        send.minCabecera = 0;
        send.descPos = 'x';
        send.usuario = $("#idUser").val();
        send.respID = 0;
        send.preID = $("#pre_id").val();
        send.pluID = $("#plu").val();
        send.estado = 'x';
        send.respuesta = $("#txt_nombrerespuestanueva").val();
        $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
            lc_control = 3;
            $("#mdl_productosModificados").modal("hide");
            $("#selPlusNuevo").val(0);
            $("#mdlPreguntasSugeridas").modal("show");
            $("#selPlusNuevo").trigger("chosen:updated");
            $("#tblPsgRespuestas").html(html);

            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html = "<tr>";
                    html += "<td class='text-left'>" + datos[i]["plu_num_plu"] + "&nbsp;</td>";
                    html += "<td><a data-type='text' href='#' id='res_dscrpcn" + datos[i]["res_id"] + "'>" + datos[i]["res_descripcion"] + "&nbsp;</a></td>";
                    html += "<td style='text-align: center;'><input type='button' name='eliminar' class='opcionAgregado' onclick='eliminarRespuesta(" + datos[i]["plu_id"] + ");' style='height: 33px; width: 33px;'/></td>";

                    $("#tblPsgRespuestas").append(html);
                    $("input[name='eliminar']").css("background", "#666666 url('../../imagenes/admin_resources/btn_eliminar.png') 1px 1px no-repeat");

                    fn_cargarPreguntasAgregadasPlus(datos[i]["res_id"]);

                    $('#res_dscrpcn' + datos[i]["res_id"]).editable({
                        type: 'text',
                        url: '../adminpreguntassugeridas/config_preguntassugeridas.php',
                        pk: 'modificarRespuesta',
                        title: 'Describa su Respuesta',
                        tpl: "<input type='text' style='width: 300px' maxlength='50'>",
                        ajaxOptions: {
                            type: 'GET',
                            dataType: 'json'
                        },
                        success: function(response, newValue) {
                            if (!response.Confirmar > 0) {
                                return true;
                            } else {
                                return response.msg;
                            }
                        }
                    });
                }
            }
            $("#mdl_rdn_pdd_crgnd").hide();
            $("#opciones_respuestanuevas label").removeClass("active");
            $("#check_todosnuevo").addClass("active");
        });
    } else {
        alertify.error("Seleccione una respuesta");
    }
}

//NUEVO: CARGA LAS RESPUESTAS UNA VEZ GUARDADAS
function fn_cargaDetalleDeRespuestas() {
    var send;
    var cargaLosDetalles = { "cargaLosDetalles": 1 };
    var html = "<thead><tr class='bg-primary'><th align='center'>NumPlu</th><th align='center'>Producto</th><th align='center'>Respuesta</th><th width='10%' align='center'>Opcion</th></tr></thead>";
    send = cargaLosDetalles;
    send.preIDdetalle = $("#pre_id").val();
    $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
        $("#tblPsgRespuestas").html(html);
        lc_control = 3;
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                html = "<tr id='idrespuesta_" + datos[i]["plu_id"] + "'>";
                html += "<td class='text-left'>" + datos[i]["plu_num_plu"] + "&nbsp;</td>";
                html += "<td><a data-type='text' href='#' id='res_dscrpcn" + datos[i]["plu_id"] + "'>" + datos[i]["res_descripcion"] + "&nbsp;</a></td>";
                html += "<td style='text-align: center;'><input type='button' name='eliminar' class='opcionAgregado' onclick='eliminarRespuesta(" + datos[i]["plu_id"] + ");' style='height: 33px; width: 33px;'/></td></tr>";

                $("#tblPsgRespuestas").append(html);
                $("input[name='eliminar']").css("background", "#666666 url('../../imagenes/admin_resources/btn_eliminar.png') 1px 1px no-repeat");

                $('#res_dscrpcn' + datos[i]["plu_id"]).editable({
                    type: 'text',
                    url: '../adminpreguntassugeridas/config_preguntassugeridas.php',
                    pk: 'modificarRespuesta',
                    title: 'Describa su Respuesta',
                    tpl: "<input type='text' style='width: 300px' maxlength='50'>",
                    ajaxOptions: {
                        type: 'GET',
                        dataType: 'json'
                    },
                    success: function(response, newValue) {
                        if (!response.Confirmar > 0) {
                            return true;
                        } else {
                            return response.msg;
                        }
                    }
                });
            }
        } else {
            $("#tblPsgRespuestas").append("");
        }
    });
}

//MODIFICAR: CARGA LAS RESPUESTAS UNA VEZ GUARDADAS
function fn_cargaRespuestasPreguntaModificada() {
    var send;
    var idPregunta = $("#hid_psug_id").val();
    var cargaLosDetalles = { "cargaLosDetalles": 1 };
    var html = "<thead><tr class='bg-primary'><th align='center'>NumPlu</th><th align='center'>Producto</th><th align='center'>Respuesta</th><th width='10%' align='center'>Opcion</th></tr></thead>";
    send = cargaLosDetalles;
    send.preIDdetalle = idPregunta;
    $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
        $("#tblPsgRespuestas").html(html);
        lc_control = 3;
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                html = "<tr id='idrespuesta_" + datos[i]["plu_id"] + "' orden=\"" + (i + 1) + "\" ondragover=\"allowDrop(event)\" ondragstart=\"drag(event)\" ondrop=\"drop(event)\" draggable=\"true\">";
                html += "<td class='text-center'>" + datos[i]["numPlu"] + "</td>";
                html += "<td>" + datos[i]["plu_num_plu"] + "</td>";
                html += "<td><a data-type='text' href='#' id='res_dscrpcn" + datos[i]["plu_id"] + "'>" + datos[i]["res_descripcion"] + "</a></td>";
                html += "<td style='text-align: center;'><input type='button' name='eliminar' class='opcionAgregado' onclick='eliminarRespuesta(" + datos[i]["plu_id"] + ");' style='height: 33px; width: 33px;'/></td></tr>";

                $("#tblPsgRespuestas").append(html);
                $("input[name='eliminar']").css("background", "#666666 url('../../imagenes/admin_resources/btn_eliminar.png') 1px 1px no-repeat");

                $('#res_dscrpcn' + datos[i]["plu_id"]).editable({});
            }
        } else {
            $("#tblPsgRespuestas").append("");
        }
    });
}

//MODIFICAR: ACTUALIZA LA PREGUNTA SUGERIDA
function mergePreguntaSugerida(accion, idPregunta) {
    $("#mdl_rdn_pdd_crgnd").show();
    var html = "";
    var pregunta = $("#hid_psug_id").val();
    var cadena = "";
    $('#tblPsgRespuestas >tbody >tr').each(function() {
        var idProducto = this.id;
        var orden = $("#" + this.id).attr("orden");
        idProducto = idProducto.substring(12, idProducto.length);
        var respuesta = $(this).find("td").eq(2).find("a").html();
        cadena += idProducto + "_" + respuesta + "_" + orden + "_";
    });
    if ($("#inpPsgDescripcionPregunta").val() == "") {
        $("#mdl_rdn_pdd_crgnd").hide();
        alertify.error("Ingresar descripción de la pregunta");
        return false;
    }
    if ($("#inpPsgDescripcionPreguntaPOS").val() == "") {
        $("#mdl_rdn_pdd_crgnd").hide();
        alertify.error("Ingresar descripción de la pregunta en POS");
        return false;
    }
    if ($("#inpPsgCantidadMinimaRespuestas").val() == "") {
        $("#mdl_rdn_pdd_crgnd").hide();
        alertify.error("Ingresar cantidad mínima de respuestas");
        return false;
    }
    if ($("#inpPsgCantidadMinimaRespuestas").val() > $("#inpPsgCantidadMaximaRespuestas").val()) {
        $("#mdl_rdn_pdd_crgnd").hide();
        alertify.error("La cantidad mínima de respuestas no puede ser mayor que la cantidad máxima de respuestas");
        return false;
    }
    var idGrupoPregunta = $("#inGrupoPregunta").val();
    if (idGrupoPregunta == "-1") {
        $("#mdl_rdn_pdd_crgnd").hide();
        alertify.error("Grupo de pregunta campo obligatorio");
        return false;
    }
    var send = { "mergePreguntaSugerida": 1 };
    send.accion = accion;
    send.descripcion = $("#inpPsgDescripcionPregunta").val();
    send.maximo = $("#inpPsgCantidadMaximaRespuestas").val();
    send.minimo = $("#inpPsgCantidadMinimaRespuestas").val();
    send.descripcionPos = $("#inpPsgDescripcionPreguntaPOS").val();
    send.nivel = 0;
    send.idPregunta = idPregunta;
    send.plu = 0;
    send.idGrupo = idGrupoPregunta;
    if ($("#inpPsgEstado").is(":checked")) {
        send.estado = "Activo";
    } else {
        send.estado = "Inactivo";
    }
    send.respuestas = cadena;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminpreguntassugeridas/config_preguntassugeridas.php",
        data: send,
        success: function(datos) {

            if (datos.str > 0) {
                alertify.success("Pregunta actualizada correctamente.");
            }

            for (var i = 0; i < datos.str; i++) {
                html += "<tr id='" + i + "' style='cursor:pointer;'";
                html += " onclick='fn_seleccion(" + i + ",\"" + datos[i]["psug_id"] + "\")' ondblclick='modificar(\"" + datos[i]["psug_id"] + "\", \"" + datos[i]["idGrupo"] + "\")'>";
                html += "<td style='text-align:left'>" + datos[i]["pre_sug_descripcion"] + "&nbsp;</td>";
                html += "<td style='text-align:left'>" + datos[i]["psug_descripcion_pos"] + "&nbsp;</td>";
                html += "<td align='center'  style='width:80px;'>" + datos[i]["psug_resp_minima"] + "&nbsp;</td>";
                html += "<td align='center'  style='width:80px;'>" + datos[i]["psug_resp_maxima"] + "&nbsp;</td>";
                html += "<td>" + datos[i]["grupo"] + "</td>";
                if (datos[i]["Estado"] == "Activo") {
                    html += "<td align='center'><input type='checkbox' checked='checked' disabled='disabled'/></td></tr>";
                } else {
                    html += "<td align='center'><input type='checkbox' disabled='disabled'/></td></tr>";
                }
            }

            $("#opciones_estado label").removeClass("active");
            $("#check_activos").addClass("active");

            $("#detalle_preguntas").html(html);
            $('#detalle_preguntas').dataTable({ "destroy": true });
            $("#detalle_preguntas_length").hide();
            $('#mdlPreguntasSugeridas').modal("hide");
            $("#mdl_rdn_pdd_crgnd").hide();
        }
    });

}

//MODIFICAR: CARGA LOS PLUS PARA AGREGAR A LAS RESPUESTAS
function fn_cargaPlusRespuestaModificar(opcion) {
    $("#mdl_rdn_pdd_crgnd").show();
    var send;
    var cargaPlus = { "cargaPlus": 1 };
    var tipo = opcion;
    var html = "";
    send = cargaPlus;
    send.cdn = $("#cdn_id").val();
    send.tipo_producto = tipo;
    $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
        $("#selPlus").empty();
        if (datos.str > 0) {
            $("#selPlus").val(0);
            $("#txt_nombrerespuesta").val("");

            $("#selPlus").append("<option selected value='0'>----Seleccione Producto----</option>");
            for (var i = 0; i < datos.str; i++) {
                html = "<option value='" + datos[i]["plu_id"] + "' numplu='" + datos[i]["plu_num_plu"] + "' descripcion='" + datos[i]["descripcion"] + "' name='" + datos[i]["plu_descripcion"] + "'>" + datos[i]["plu_num_plu"] + " - " + datos[i]["plu_descripcion"] + "</option>";
                $("#selPlus").append(html);
            }
            $("#mdl_productos").modal("show");
            $("#selPlus").trigger("chosen:updated");
            $("#selPlus").chosen({
                no_results_text: "No existen registros para ",
                search_contains: true

            });
            $("#selPlus_chosen").css("width", "480");

            $("#selPlus").change(function() {
                var lc_plu = $("#selPlus").val();
                $("#plu").val(lc_plu);
            });

            $("#selPlus").change(function() {
                var name = $(this).find("option:selected").attr("name");
                $("#txt_nombrerespuesta").val(name);
            });
        } else {
            alertify.error("No se encontró ningun plus '" + opcion + "' para esta cadena");
            $("#selPlus").trigger("chosen:updated");
        }
        $("#mdlPreguntasSugeridas").modal("hide");
        $("#mdl_rdn_pdd_crgnd").hide();
    });
}

//MODIFICAR: APLICA RESPUESTAS A LA PREGUNTA SUGERIDA
function fn_aplicarRespuestaModificada() {
    var tipo = "Todo";
    fn_cargaPlusRespuestaModificar(tipo);
}

function fn_limpiaInfo() {
    $("#inpPsgDescripcionPregunta").val("");
    $("#inpPsgDescripcionPreguntaPOS").val("");
    $("#inpPsgCantidadMinimaRespuestas").val(0);
    $("#inpPsgEstado").prop("checked", true);
    $("#tblPsgRespuestas").empty();
}

function validaNumeroDerespuestas() {
    var send;
    var validaNumeroDerespuestas = { "validaNumeroDerespuestas": 1 };
    send = validaNumeroDerespuestas;
    send.cdnNumeroRespuestas = $("#cdn_id").val();
    $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
        if (datos.str > 0) {
            return true;
        } else {
            alertify.error("No existe la configuración para esta cadena");
            return false;
        }
    });
}

function fn_modalesModificados() {
    $("#mdl_productos").modal("hide");
    $("#mdlPreguntasSugeridas").modal("show");
}

function fn_cerrar() {
    window.location.reload();
}

function fn_eliminar() {
    var send;
    var eliminaPregunta = { "eliminaPregunta": 1 };
    alertify.confirm("Esta seguro de eliminar esta pregunta?", function(e) {
        if (e) {
            $("#mdl_rdn_pdd_crgnd").show();
            send = eliminaPregunta;
            send.preElimina = $("#hid_psug_id").val();
            $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
                fn_muestraDetallePreguntas();
                alertify.success("Pregunta eliminada correctamente");
                $("#mdl_rdn_pdd_crgnd").hide();
            });
        }
    });
}

//FUNCION PARA CARGAR LOS PLUS ASOCIADOS A UNA PREGUNTA SUGERIDA
function fn_cargarPreguntasAgregadasPlus(psug_id) {
    var send;
    var cargaPreguntaaaproducto = { "cargaPreguntaaaproducto": 1 };
    var html = "<tr class='bg-primary'><td align='center'><h5># Plu</h5></td><td align='center'><h5>Plu Asociado a Pregunta</h5></td><td align='center' style='width:50px'><h5><input type='button' name='opcioneliminar' class='opcionAgregado' onclick='fn_quitarPreguntaPlu()' style='height: 33px; width: 33px;'/></h5></td></tr>";
    send = cargaPreguntaaaproducto;
    send.psug_id = psug_id;
    send.usuario = $('#idUser').val();
    $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                html += "<tr><td style='text-align: center;'>" + datos[i]["plu_num_plu"] + "</td><td>" + datos[i]["plu_descripcion"] + "</td><td style='text-align: center;'><input type='button' name='opcioneliminar' class='opcionAgregado' onclick='fn_eliminarPreguntaPlu(" + datos[i]["plu_id"] + ")' style='height: 33px; width: 33px;'/></td></tr>";
            }
        } else {
            html += "<tr><td colspan='3' style='text-align: center;'>No existen plus asociados a pregunta.</td></tr>";
        }
        $('#tblPsgPlusAsociados').html(html);
        $("input[name='opcioneliminar']").css("background", "#666666 url('../../imagenes/admin_resources/btn_eliminar.png') 1px 1px no-repeat");
    });
}

//FUNCION PARA QUITAR TODOS LOS PLUS DE UNA PREGUNTA SUGERIDA
function fn_quitarPreguntaPlu() {
    var send;
    var eliminaPreguntadeproducto = { "eliminaPreguntadeproducto": 1 };
    alertify.set({ labels: { ok: "Si", cancel: "No" } });
    alertify.confirm("Desea eliminar todos los productos de esta pregunta?", function(e) {
        if (e) {
            $("#mdl_rdn_pdd_crgnd").show();
            var psug_id = $("#hid_psug_id").val();
            send = eliminaPreguntadeproducto;
            send.psug_id = psug_id;
            send.usuario = $("#idUser").val();
            $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
                fn_cargarPreguntasAgregadasPlus(psug_id);
                alertify.success("Los plus asociados a la pregunta han sido eliminados");
                $("#mdl_rdn_pdd_crgnd").hide();
            });
        }
    });
}

//FUNCION PARA ELIMINAR EN PLU O PRODUCTO DE LA PREGUNTA
function fn_eliminarPreguntaPlu(plu_id) {
    var send;
    var eliminaproductodepregunta = { "eliminaproductodepregunta": 1 };
    alertify.set({ labels: { ok: "Si", cancel: "No" } });
    alertify.confirm("Desea eliminar el producto de esta pregunta?", function(e) {
        if (e) {
            $("#mdl_rdn_pdd_crgnd").show();
            psug_id = $("#hid_psug_id").val();
            send = eliminaproductodepregunta;
            send.plu_id = plu_id;
            send.psug_id = psug_id;
            send.usuario = $('#idUser').val();
            $.getJSON("../adminpreguntassugeridas/config_preguntassugeridas.php", send, function(datos) {
                fn_cargarPreguntasAgregadasPlus(psug_id);
                alertify.success("El plus asociado a la pregunta ha sido eliminado");
                $("#mdl_rdn_pdd_crgnd").hide();
            });
        }
    });
}

function aceptar() {
    $("#mdlPreguntasSugeridas").modal("hide");
}

var allowDrop = function(ev) {
    ev.preventDefault();
};

var drag = function(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
    $("#" + ev.target.id).addClass("success");
};

var drop = function(ev) {
    ev.preventDefault();
    var inicia = ev.dataTransfer.getData("text");
    var oinicia = $("#" + inicia).attr("orden");
    var termina = ev.target.parentNode.id;
    $("#" + inicia).attr("orden", $("#" + termina).attr("orden"));
    $("#" + termina).attr("orden", oinicia);
    if ($("#" + inicia).index() > $("#" + termina).index()) {
        $("#" + inicia).insertBefore($("#" + termina));
    } else {
        $("#" + inicia).insertAfter($("#" + termina));
    }
    $("#" + inicia).removeClass("success");
};

function cargarGruposPreguntasSugeridas() {
    $("#mdl_rdn_pdd_crgnd").show();
    var send = { "cargarGrupoPreguntasSugeridas": 1 };
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminpreguntassugeridas/config_preguntassugeridas.php",
        data: send,
        success: function(datos) {
            var html = "<option value='-1'>-- Seleccione una opción --</option>";
            $("#inGrupoPregunta").html(html);
            for (var i = 0; i < datos.str; i++) {
                $("#inGrupoPregunta").append("<option value='" + datos[i]["idGrupo"] + "'>" + datos[i]["grupo"] + "</option>");
            }
        }
    });

}