var send = {};
var AMBIENTE="0";// VARIABLE GLOBAL PARA IDENTIFICAR EL TIPO DE AMBIENTE EN EL QUE NOS ENCONTRAMOS(TIENDA, AZURE, ETC)

$(document).ready(function () {
    cargarCategorias();
    cargarVariablesSesion();
    cargarTiposDato();
    eventoSwitchObligatorio();
    $('#slcTipoDato').change(function () {
        eventoSelectTipoDato();
    });
    fn_cargaColeccionRutaCarpeta();
   
});

var cargarCategorias = function () {
    $('#mdl_rdn_pdd_crgnd').show();
    var html = "<thead><tr class='active'><th width='90%'>Descripción</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "cargarCategorias";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReportes/configReportes.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo") {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrCategoria" + datos[i]['idCategoria'] + "' onclick='seleccionarFilaTablaCategorias(\"" + datos[i]['idCategoria'] + "\")' ondblclick='modificarFilaTablaCategorias(\"" + datos[i]['idCategoria'] + "\", \"" + datos[i]['descripcion'] + "\", \"" + datos[i]['estado'] + "\", \"" + datos[i]['ruta'] + "\")'><td>" + datos[i]['descripcion'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $('#tblCategorias').html(html);
            $('#mdl_rdn_pdd_crgnd').hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            alert("Error: " + thrownError);
        }
    });
};

 function fn_cargaColeccionRutaCarpeta() 
 {
    $('#sel_ruta').empty();    
    send = {};
    send.metodo = "cargaComboRutaCarpeta";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReportes/configReportes.php", data: send,
        success: function (datos) 
        {  
           html='';
           for (i = 0; i < datos.str; i++) 
            {                
                html += '<option value="' + datos[i]['descripcion'] + '">' + datos[i]['descripcion'] + '</option>';
            } 
            $('#sel_ruta').append(html);
        }        
    });
}

var seleccionarFilaTablaCategorias = function (idCategoria) { 
    if ($("#tblCategorias").find(".success").attr("id") !== "tbrCategoria" + idCategoria) {
        $("#tblCategorias tr").removeClass("success");
        $("#tbrCategoria" + idCategoria).addClass("success");
        cargarReportes(idCategoria);
        $("#lblRptCategoria").html("&nbsp;&nbsp;&nbsp;&nbsp;" + $("#tbrCategoria" + idCategoria + " td").html());
        $('#btnAgregarNuevoReporte').attr("onclick", "agregarNuevoReporte('" + idCategoria + "')");
        $("#btnAgregarNuevoParametro").attr("onclick", "agregarNuevoParametro(null)");
        $('#tblParametros').html("");
    }
};

var modificarFilaTablaCategorias = function (idCategoria, descripcion, estado,ruta) {
    $('#mdl_rdn_pdd_crgnd').show();
    $('#mdlCategoriaTitulo').html(descripcion);
    $('#inpCtgDescripcion').val(descripcion);
    $('#sel_ruta').val(ruta);
    if (estado === "Activo")
        $("#inpCtgEstado").prop("checked", true);
    else
        $("#inpCtgEstado").prop("checked", false);
    $('#btnCtgGuardarCambios').attr("onclick", "validarParametrosCategoria(0, '" + idCategoria + "')");
    eventoKeyPressCategoria(0, idCategoria);
    $('#mdlNuevaCategoria').modal("show");
    $('#mdl_rdn_pdd_crgnd').hide();
};

var cargarCategoria = function (idCategoria) {
    send = {};
    send.metodo = "cargarCategoria";
    send.idCategoria = idCategoria;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReportes/configReportes.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $('#mdlCategoriaTitulo').html(datos.descripcion);
                $('#inpCtgDescripcion').val(datos.descripcion);
                if (datos.estado === "Activo")
                    $("#inpCtgEstado").prop("checked", true);
                else
                    $("#inpCtgEstado").prop("checked", false);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            alert("Error: " + thrownError);
        }
    });
};

var validarParametrosCategoria = function (accion, idCategoria) {
    $('#mdl_rdn_pdd_crgnd').show();
    var descripcion = $('#inpCtgDescripcion').val();
    var estado = "Inactivo";
    if ($("#inpCtgEstado").is(':checked'))
        estado = "Activo";
    if (descripcion.length >= 4) {
        guardarCategoria(accion, idCategoria, descripcion, estado);
    } else {
        alertify.error("El nombre de la categoría debe tener por lo menos 4 caracteres");
    }
    $('#mdl_rdn_pdd_crgnd').hide();
};

var guardarCategoria = function (accion, idCategoria, descripcion, estado) {
    var html = "<thead><tr class='active'><th width='90%'>Descripción</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "guardarCategoria";
    send.accion = accion;
    send.idCategoria = idCategoria;
    send.descripcion = descripcion;
    send.estado = estado;
    send.opcionRuta=$('#sel_ruta').val();
    //$('#sel_ruta').empty();
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReportes/configReportes.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo") {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrCategoria" + datos[i]['idCategoria'] + "' onclick='seleccionarFilaTablaCategorias(\"" + datos[i]['idCategoria'] + "\")' ondblclick='modificarFilaTablaCategorias(\"" + datos[i]['idCategoria'] + "\", \"" + datos[i]['descripcion'] + "\", \"" + datos[i]['estado'] + "\", \"" + datos[i]['ruta'] + "\",)'><td>" + datos[i]['descripcion'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $('#tblCategorias').html(html);
            $('#tblReportes').html("");
            $('#btnAgregarNuevoReporte').attr("onclick", "agregarNuevoReporte(null)");
            $('#tblParametros').html("");
            $('#btnAgregarNuevoParametro').attr("onclick", "agregarNuevoParametro(null)");
            $("#mdlNuevaCategoria").modal("hide");
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            $("#mdlNuevaCategoria").modal("hide");
            alert("Error: " + thrownError);
        }
    });
};

var agregarNuevaCategoria = function () {
    $('#mdl_rdn_pdd_crgnd').show();
    $('#mdlCategoriaTitulo').html("Nueva Categoría");
    $('#inpCtgDescripcion').val("");
    $('#inpCtgEstado').prop("checked", true);
    $('#btnCtgGuardarCambios').attr("onclick", "validarParametrosCategoria(1, '0')");
    eventoKeyPressCategoria(1, '0');
    $('#mdlNuevaCategoria').modal("show");
    $('#mdl_rdn_pdd_crgnd').hide();
};

var cargarReportes = function (idCategoria) {
    $('#mdl_rdn_pdd_crgnd').show();
    var html = "<thead><tr class='active'><th width='90%'>Nombre</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "cargarReportes";
    send.idCategoria = idCategoria;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReportes/configReportes.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo") {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrReporte" + datos[i]['idReporte'] + "' onclick='seleccionarFilaTablaReportes(\"" + datos[i]['idReporte'] + "\")' ondblclick='modificarFilaTablaReportes(\"" + datos[i]['idReporte'] + "\", \"" + datos[i]['label'] + "\", \"" + datos[i]['descripcion'] + "\", \"" + datos[i]['url'] + "\", \"" + datos[i]['estado'] + "\", \"" + idCategoria + "\")'><td>" + datos[i]['label'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $('#tblReportes').html(html);
            $('#mdl_rdn_pdd_crgnd').hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            alert("Error: " + thrownError);
        }
    });
};

var seleccionarFilaTablaReportes = function (idReporte) {
    if ($("#tblReportes").find(".info").attr("id") !== "tbrReporte" + idReporte) {
        $("#tblReportes tr").removeClass("info");
        $("#tbrReporte" + idReporte).addClass("info");
        cargarParametros(idReporte);
        $("#lblPrmReporte").html("&nbsp;&nbsp;&nbsp;&nbsp;" + $("#tbrReporte" + idReporte + " td").html());
        $('#btnAgregarNuevoParametro').attr("onclick", "agregarNuevoParametro('" + idReporte + "')");
    }
};

var modificarFilaTablaReportes = function (idReporte, label, descripcion, url, estado, idCategoria) {
    $('#mdl_rdn_pdd_crgnd').show();
    $('#mdlReporteTitulo').html(label);
    $('#inpRptLabel').val(label);
    $('#inpRptDescripcion').val(descripcion);
    var posicion = url.lastIndexOf("/");
    if (posicion > 0) {
        $('#inpRptCarpeta').val(url.substring(1, posicion));
    } else {
        $('#inpRptCarpeta').val("");
    }
    $('#inpRptArchivo').val(url.substring(url.lastIndexOf("/") + 1));
    if (estado === "Activo")
        $("#inpRptEstado").prop("checked", true);
    else
        $("#inpRptEstado").prop("checked", false);
    $("#btnRptGuardarCambios").attr("onclick", "validarParametrosReporte(0, '" + idReporte + "', '" + idCategoria + "')");
    eventoKeyPressReporte(0, idReporte, idCategoria);
    $('#mdlNuevoReporte').modal("show");
    $('#mdl_rdn_pdd_crgnd').hide();
};

var validarParametrosReporte = function (accion, idReporte, idCategoria) {
    $('#mdl_rdn_pdd_crgnd').show();
    var label = $('#inpRptLabel').val();
    var descripcion = $('#inpRptDescripcion').val();
    var archivo = "/" + $('#inpRptArchivo').val();
    var carpeta = "";
    if ($('#inpRptCarpeta').val() !== "") {
        carpeta = "/" + $('#inpRptCarpeta').val();
    }
    var url = carpeta + archivo;
    var estado = "Inactivo";
    if ($('#inpRptEstado').is(':checked')) {
        estado = "Activo";
    }
    if (label.length >= 4) {
        if (descripcion.length >= 4) {
            if (archivo.length > 1) {
                guardarReporte(accion, idReporte, label, descripcion, url, estado, idCategoria);
            } else {
                alertify.error("Especificar la ubicación del archivo del reporte");
            }
        } else {
            alertify.error("La descripción del reporte debe tener por lo menos 4 caracteres");
        }
    } else {
        alertify.error("El nombre del reporte debe tener por lo menos 4 caracteres");
    }
    $('#mdl_rdn_pdd_crgnd').hide();
};

var guardarReporte = function (accion, idReporte, label, descripcion, url, estado, idCategoria) {
    var html = "<thead><tr class='active'><th width='90%'>Nombre</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "guardarReporte";
    send.accion = accion;
    send.idReporte = idReporte;
    send.label = label;
    send.descripcion = descripcion;
    send.url = url;
    send.estado = estado;
    send.idCategoria = idCategoria;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReportes/configReportes.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo") {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrReporte" + datos[i]['idReporte'] + "' onclick='seleccionarFilaTablaReportes(\"" + datos[i]['idReporte'] + "\")' ondblclick='modificarFilaTablaReportes(\"" + datos[i]['idReporte'] + "\", \"" + datos[i]['label'] + "\", \"" + datos[i]['descripcion'] + "\", \"" + datos[i]['url'] + "\", \"" + datos[i]['estado'] + "\", \"" + idCategoria + "\")'><td>" + datos[i]['label'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $('#tblReportes').html(html);
            $('#tblParametros').html("");
            $('#btnAgregarNuevoParametro').attr("onclick", "agregarNuevoParametro(null)");
            $("#mdlNuevoReporte").modal("hide");
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            $("#mdlNuevoReporte").modal("hide");
            alert("Error: " + thrownError);
        }
    });
};

var agregarNuevoReporte = function (idCategoria) {
    if (idCategoria !== null) {
        $('#mdl_rdn_pdd_crgnd').show();
        $('#mdlReporteTitulo').html("Nuevo Reporte");
        $('#inpRptLabel').val("");
        $('#inpRptDescripcion').val("");
        $('#inpRptCarpeta').val("");
        $('#inpRptArchivo').val("");
        $("#inpRptEstado").prop("checked", true);
        $("#btnRptGuardarCambios").attr("onclick", "validarParametrosReporte(1, '0', '" + idCategoria + "')");
        eventoKeyPressReporte(1, '0', idCategoria);
        $('#mdlNuevoReporte').modal("show");
        $('#mdl_rdn_pdd_crgnd').hide();
    } else {
        alertify.error("Seleccione la categoría del reporte.");
    }
};

var cargarParametros = function (idReporte) {
    $('#mdl_rdn_pdd_crgnd').show();
    var html = "<thead><tr class='active'><th style='text-align:center' width='10%'>Orden</th><th width='30%'>Descripción</th><th width='30%'>Parámetro</th><th style='text-align:center' width='20%'>Tipo de Dato</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "cargarParametros";
    send.idReporte = idReporte;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReportes/configReportes.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo") {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrParametro" + datos[i]['idParametro'] + "' orden=\"" + (i + 1) + "\" ondragover=\"allowDrop(event)\" ondragstart=\"drag(event)\" ondrop=\"drop(event)\" draggable=\"true\" onclick='seleccionarFilaTablaParametros(\"" + datos[i]['idParametro'] + "\")' ondblclick='modificarFilaTablaParametros(\"" + datos[i]['idParametro'] + "\", \"" + datos[i]['estado'] + "\", \"" + datos[i]['etiqueta'] + "\", \"" + datos[i]['variable'] + "\", \"" + datos[i]['tipoDato'] + "\", " + datos[i]['obligatorio'] + ", \"" + datos[i]['tablaIntegracion'] + "\", \"" + datos[i]['columnaIntegracion'] + "\", \"" + datos[i]['query'] + "\", \"" + idReporte + "\")'><td class='text-center'>" + (i + 1) + "</td><td>" + datos[i]['etiqueta'] + "</td><td>" + datos[i]['variable'] + "</td><td class='text-center'>" + datos[i]['tipoDato'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $('#tblParametros').html(html);
            $('#mdl_rdn_pdd_crgnd').hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            alert("Error: " + thrownError);
        }
    });
};

var seleccionarFilaTablaParametros = function (idParametro) {
    if ($("#tblParametros").find(".success").attr("id") !== "tbrParametro" + idParametro) {
        $("#tblParametros tr").removeClass("success");
        $("#tbrParametro" + idParametro).addClass("success");
        $('#btnEliminarParametro').attr("onclick", "validarEliminarParametro('" + idParametro + "')");
    }
};

var modificarFilaTablaParametros = function (idParametro, estado, etiqueta, variable, tipoDato, obligatorio, tablaIntegracion, columnaIntegracion, query, idReporte) {
    $('#mdl_rdn_pdd_crgnd').show();
    $('#mdlParametroTitulo').html(etiqueta);
    $('#inpPrmEtiqueta').val(etiqueta);
    $('#inpPrmVariable').val(variable);
    $('#slcTipoDato').val(tipoDato);
    eventoSelectTipoDato();
    if (obligatorio === 1) {
        $("#inpPrmObligatorio").prop("checked", true);
        $('#slcColumnaIntegracion').attr('disabled', 'disabled');
        $('#slcColumnaIntegracion').val(0);
    } else if (obligatorio === 0) {
        $("#inpPrmObligatorio").prop("checked", false);
        $('#slcColumnaIntegracion').removeAttr('disabled');
    }
    $('#slcTablaIntegracion').val(tablaIntegracion);
    $('#slcColumnaIntegracion').val(columnaIntegracion);
    $('#txaPrmQuery').val(query);
    if (estado === "Activo")
        $("#inpPrmEstado").prop("checked", true);
    else
        $("#inpPrmEstado").prop("checked", false);
    $('#btnPrmGuardarCambios').attr("onclick", "validarParametrosParametro(0, '" + idParametro + "', '" + idReporte + "')");
    eventoKeyPressParametro(0, idParametro, idReporte);
    $('#mdlNuevoParametro').modal("show");
    $('#mdl_rdn_pdd_crgnd').hide();
};

var cargarVariablesSesion = function () {
    var html = "";
    send = {};
    send.metodo = "cargarVariablesSesion";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReportes/configReportes.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += "<option value='" + datos[i]['sesion'] + "'>" + datos[i]['descripcion'] + "</option>";
                }
            }
            $('#slcColumnaIntegracion').html(html);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert("Error: " + thrownError);
        }
    });
};

var cargarTiposDato = function () {
    var html = "";
    send = {};
    send.metodo = "cargarTiposDato";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReportes/configReportes.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += "<option value='" + datos[i]['idTipoDato'] + "'>" + datos[i]['descripcion'] + "</option>";
                }
            }
            $('#slcTipoDato').html(html);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert("Error: " + thrownError);
        }
    });
};

var validarParametrosParametro = function (accion, idParametro, idReporte) {
    $('#mdl_rdn_pdd_crgnd').show();
    var etiqueta = $('#inpPrmEtiqueta').val();
    var variable = $('#inpPrmVariable').val();
    var tipoDato = $('#slcTipoDato').val();
    var obligatorio = 0;
    if ($('#inpPrmObligatorio').is(':checked')) {
        obligatorio = 1;
    }
    var tablaIntegracion = $('#slcTablaIntegracion').val();
    var columnaIntegracion = $('#slcColumnaIntegracion').val();
    var query = $('#txaPrmQuery').val();
    var estado = "Inactivo";
    var orden = 0;
    if (accion > 0) {
        orden = $("#tblParametros tr").length;
    } else {
        orden = $("#tbrParametro" + idParametro).attr("orden");
    }
    if ($('#inpPrmEstado').is(':checked')) {
        estado = "Activo";
    }
    if (etiqueta.length >= 4) {
        if (variable.length >= 4) {
            if (tipoDato !== null) {
                if (!$('#inpPrmObligatorio').is(':checked') && columnaIntegracion === null && tipoDato !== "B" && tipoDato !== "E") {
                    alertify.error("Si el parámetro no es obligatorio, se debe elegir la columna de integración.");
                } else {
                    if (tipoDato === "B" && query.length === 0) {
                        alertify.error("El query no puede estar vacío");
                    } else {
                        guardarParametro(accion, idParametro, etiqueta, variable, tipoDato, obligatorio, tablaIntegracion, columnaIntegracion, query, orden, estado, idReporte);
                    }
                }
            } else {
                alertify.error("Escoger un tipo de dato");
            }
        } else {
            alertify.error("El parámetro debe tener por lo menos 4 caracteres");
        }
    } else {
        alertify.error("La descripción del parámetro debe tener por lo menos 4 caracteres");
    }
    $('#mdl_rdn_pdd_crgnd').hide();
};

var guardarParametro = function (accion, idParametro, etiqueta, variable, tipoDato, obligatorio, tablaIntegracion, columnaIntegracion, query, orden, estado, idReporte) {
    var html = "<thead><tr class='active'><th style='text-align:center' width='10%'>Orden</th><th width='30%'>Descripción</th><th width='30%'>Parámetro</th><th style='text-align:center' width='20%'>Tipo de Dato</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "guardarParametro";
    send.accion = accion;
    send.idParametro = idParametro;
    send.etiqueta = etiqueta;
    send.variable = variable;
    send.tipoDato = tipoDato;
    send.obligatorio = obligatorio;
    send.tablaIntegracion = tablaIntegracion;
    send.columnaIntegracion = columnaIntegracion;
    send.query = query;
    send.orden = (orden - 1);
    send.estado = estado;
    send.idReporte = idReporte;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReportes/configReportes.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo") {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrParametro" + datos[i]['idParametro'] + "' orden=\"" + (i + 1) + "\" ondragover=\"allowDrop(event)\" ondragstart=\"drag(event)\" ondrop=\"drop(event)\" draggable=\"true\" onclick='seleccionarFilaTablaParametros(\"" + datos[i]['idParametro'] + "\")' ondblclick='modificarFilaTablaParametros(\"" + datos[i]['idParametro'] + "\", \"" + datos[i]['estado'] + "\", \"" + datos[i]['etiqueta'] + "\", \"" + datos[i]['variable'] + "\", \"" + datos[i]['tipoDato'] + "\", " + datos[i]['obligatorio'] + ", \"" + datos[i]['tablaIntegracion'] + "\", \"" + datos[i]['columnaIntegracion'] + "\", \"" + datos[i]['query'] + "\", \"" + idReporte + "\")'><td class='text-center'>" + (i + 1) + "</td><td>" + datos[i]['etiqueta'] + "</td><td>" + datos[i]['variable'] + "</td><td class='text-center'>" + datos[i]['tipoDato'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $('#tblParametros').html(html);
            $("#mdlNuevoParametro").modal("hide");
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            $("#mdlNuevoParametro").modal("hide");
            alert("Error: " + thrownError);
        }
    });
};

var agregarNuevoParametro = function (idReporte) {
    if (idReporte !== null) {
        $('#mdl_rdn_pdd_crgnd').show();
        $('#mdlParametroTitulo').html("Nuevo Parámetro");
        $('#inpPrmEtiqueta').val("");
        $('#inpPrmVariable').val("");
        $('#slcTipoDato').val(0);
        eventoSelectTipoDato();
        $("#inpPrmObligatorio").prop("checked", true);
        $('#slcColumnaIntegracion').attr('disabled', 'disabled');
        $('#slcColumnaIntegracion').val(0);
        $('#slcTablaIntegracion').val(0);
        $("#inpPrmEstado").prop("checked", true);
        $("#btnPrmGuardarCambios").attr("onclick", "validarParametrosParametro(1, '0', '" + idReporte + "')");
        eventoKeyPressParametro(1, '0', idReporte);
        $('#mdlNuevoParametro').modal("show");
        $('#mdl_rdn_pdd_crgnd').hide();
    } else {
        alertify.error("Seleccione el reporte del parámetro.");
    }
};

var allowDrop = function (ev) {
    ev.preventDefault();
};

var drag = function (ev) {
    ev.dataTransfer.setData("text", ev.target.id);
    $("#tblParametros tr").removeClass("success");
    $("#" + ev.target.id).addClass("success");
};

var drop = function (ev) {
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
    setOrden();
};

var setOrden = function () {
    var html = "";
    var j = 0;
    $('#tblParametros tr').each(function () {
        if ($("#" + this.id).index() >= 0) {
            html += this.id.substr(12) + "_" + (j) + "_";
            $("#" + this.id).find('td:eq(0)').html(j + 1);
            $("#" + this.id).attr("orden", j);
            j++;
        }
    });
    actualizarOrdenParametro(html);
};

var actualizarOrdenParametro = function (html) {
    send = {};
    send.metodo = "actualizarOrdenParametro";
    send.html = html;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReportes/configReportes.php", data: send,
        success: function (datos) {
            if (datos.Confirmar < 1) {
                alertify.error("Error actualizando el orden de los parámetros.");
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert("Error: " + thrownError);
        }
    });
};

var eventoSwitchObligatorio = function () {
    $('#inpPrmObligatorio').change(function () {
        if ($('#inpPrmObligatorio').is(':checked')) {
            $('#slcColumnaIntegracion').attr('disabled', 'disabled');
            $('#slcColumnaIntegracion').val(0);
        } else {
            $('#slcColumnaIntegracion').removeAttr('disabled');
        }
    });
};

var eventoSelectTipoDato = function () {
    if ($('#slcTipoDato').val() === "B") {
        $('#divTablaIntegracion').hide();
        $('#divColumnaIntegracion').hide();
        $('#divQuery').show();
    } else if ($('#slcTipoDato').val() === "E") {
        $('#divTablaIntegracion').show();
        $('#divColumnaIntegracion').hide();
        $('#divQuery').hide();
    } else {
        $('#divTablaIntegracion').hide();
        $('#divColumnaIntegracion').show();
        $('#divQuery').hide();
    }
};

var eventoKeyPressCategoria = function (accion, idCategoria) {
    $('#mdlNuevaCategoria').unbind("keypress");
    $('#mdlNuevaCategoria').keypress(function (e) {
        if (e.which === 13) {
            e.preventDefault();
            validarParametrosCategoria(accion, idCategoria);
        }
    });
};

var eventoKeyPressReporte = function (accion, idReporte, idCategoria) {
    $('#mdlNuevoReporte').unbind("keypress");
    $('#mdlNuevoReporte').keypress(function (e) {
        if (e.which === 13) {
            e.preventDefault();
            validarParametrosReporte(accion, idReporte, idCategoria);
        }
    });
};

var eventoKeyPressParametro = function (accion, idParametro, idReporte) {
    $('#mdlNuevoParametro').unbind("keypress");
    $('#mdlNuevoParametro').keypress(function (e) {
        if (e.which === 13) {
            e.preventDefault();
            validarParametrosParametro(accion, idParametro, idReporte);
        }
    });
};

var validarEliminarCategoria = function () {
    $('#mdl_rdn_pdd_crgnd').show();
    var idCategoria = $("#tblCategorias").find(".success").attr("id");
    if (idCategoria) {
        idCategoria = idCategoria.substring(12);
        var categoria = $("#tbrCategoria" + idCategoria + " td").html();
        alertify.confirm('¿Está seguro que desea eliminar la categoría "' + categoria + '"? Todos los reportes y sus respectivos parámetros dentro de esta categoría también serán eliminados.', function (e) {
            if (e) {
                eliminarCategoria(idCategoria);
            }
        });
    } else {
        alertify.error("Seleccione una categoría.");
    }
    $('#mdl_rdn_pdd_crgnd').hide();
};

var eliminarCategoria = function (idCategoria) {
    var html = "<thead><tr class='active'><th width='90%'>Descripción</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "eliminarCategoria";
    send.idCategoria = idCategoria;
    send.accion = 2;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReportes/configReportes.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo") {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrCategoria" + datos[i]['idCategoria'] + "' onclick='seleccionarFilaTablaCategorias(\"" + datos[i]['idCategoria'] + "\")' ondblclick='modificarFilaTablaCategorias(\"" + datos[i]['idCategoria'] + "\", \"" + datos[i]['descripcion'] + "\", \"" + datos[i]['estado'] + "\", \"" + datos[i]['ruta'] + "\")'><td>" + datos[i]['descripcion'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $('#tblCategorias').html(html);
            $('#tblReportes').html("");
            $('#btnAgregarNuevoReporte').attr("onclick", "agregarNuevoReporte(null)");
            $('#tblParametros').html("");
            $('#btnAgregarNuevoParametro').attr("onclick", "agregarNuevoParametro(null)");
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            alert("Error: " + thrownError);
        }
    });
};

var validarEliminarReporte = function () {
    $('#mdl_rdn_pdd_crgnd').show();
    var idCategoria = $("#tblCategorias").find(".success").attr("id");
    var idReporte = $("#tblReportes").find(".info").attr("id");
    if (idReporte) {
        idCategoria = idCategoria.substring(12);
        idReporte = idReporte.substring(10);
        var reporte = $("#tbrReporte" + idReporte + " td").html();
        alertify.confirm('¿Está seguro que desea eliminar el reporte "' + reporte + '"? Todos los parámetros de este reporte también serán eliminados.', function (e) {
            if (e) {
                eliminarReporte(idCategoria, idReporte);
            }
        });
    } else {
        alertify.error("Seleccione un reporte.");
    }
    $('#mdl_rdn_pdd_crgnd').hide();
};

var eliminarReporte = function (idCategoria, idReporte) {
    var html = "<thead><tr class='active'><th width='90%'>Nombre</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "eliminarReporte";
    send.idCategoria = idCategoria;
    send.idReporte = idReporte;
    send.accion = 2;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReportes/configReportes.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo") {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrReporte" + datos[i]['idReporte'] + "' onclick='seleccionarFilaTablaReportes(\"" + datos[i]['idReporte'] + "\")' ondblclick='modificarFilaTablaReportes(\"" + datos[i]['idReporte'] + "\", \"" + datos[i]['label'] + "\", \"" + datos[i]['descripcion'] + "\", \"" + datos[i]['url'] + "\", \"" + datos[i]['estado'] + "\", \"" + idCategoria + "\")'><td>" + datos[i]['label'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $('#tblReportes').html(html);
            $('#tblParametros').html("");
            $('#btnAgregarNuevoParametro').attr("onclick", "agregarNuevoParametro(null)");
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            alert("Error: " + thrownError);
        }
    });
};

var validarEliminarParametro = function () {
    $('#mdl_rdn_pdd_crgnd').show();
    var idReporte = $("#tblReportes").find(".info").attr("id");
    var idParametro = $("#tblParametros").find(".success").attr("id");
    if (idParametro) {
        idReporte = idReporte.substring(10);
        idParametro = idParametro.substring(12);
        alertify.confirm('¿Está seguro que desea eliminar este parámetro?', function (e) {
            if (e) {
                eliminarParametro(idParametro, idReporte);
            }
        });
    } else {
        alertify.error("Seleccione un parámetro.");
    }
    $('#mdl_rdn_pdd_crgnd').hide();
};

var eliminarParametro = function (idParametro, idReporte) {
    var html = "<thead><tr class='active'><th style='text-align:center' width='10%'>Orden</th><th width='30%'>Descripción</th><th width='30%'>Parámetro</th><th style='text-align:center' width='20%'>Tipo de Dato</th><th style='text-align:center' width='10%'>Activo</th></tr></thead>";
    send = {};
    send.metodo = "eliminarParametro";
    send.idParametro = idParametro;
    send.idReporte = idReporte;
    send.accion = 2;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReportes/configReportes.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo") {
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    }
                    html += "<tr id='tbrParametro" + datos[i]['idParametro'] + "' orden=\"" + (i + 1) + "\" ondragover=\"allowDrop(event)\" ondragstart=\"drag(event)\" ondrop=\"drop(event)\" draggable=\"true\" onclick='seleccionarFilaTablaParametros(\"" + datos[i]['idParametro'] + "\")' ondblclick='modificarFilaTablaParametros(\"" + datos[i]['idParametro'] + "\", \"" + datos[i]['estado'] + "\", \"" + datos[i]['etiqueta'] + "\", \"" + datos[i]['variable'] + "\", \"" + datos[i]['tipoDato'] + "\", " + datos[i]['obligatorio'] + ", \"" + datos[i]['tablaIntegracion'] + "\", \"" + datos[i]['columnaIntegracion'] + "\", \"" + datos[i]['query'] + "\", \"" + idReporte + "\")'><td class='text-center'>" + (i + 1) + "</td><td>" + datos[i]['etiqueta'] + "</td><td>" + datos[i]['variable'] + "</td><td class='text-center'>" + datos[i]['tipoDato'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $('#tblParametros').html(html);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('#mdl_rdn_pdd_crgnd').hide();
            alert("Error: " + thrownError);
        }
    });
};