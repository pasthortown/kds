/* global alertify */

var send = {};

$(document).ready(function () {
    cargarTiposProductos();
    cargarImpuestos();
    cargarClasificaciones();
    cargarProductosPorCadena();
    cargarCategoriasPorCadena();
    cargarCanalesImpresionPorCadena();
    cargarColeccionDepartamentos();
    cargarListaModificadores();
    $("#inTipoProducto").change(function () {
        if (this.value > 0) {
            $("#inModificador").val(0);
            $("#inModificador").attr("disabled", "disabled");
        } else {
            $("#inModificador").val(0);
            $("#inModificador").removeAttr("disabled");
        }
    });
});

var cargarListaModificadores = function () {
    var html = "<option value='0'>-- Seleccionar Modificador --</option>";
    var send = {};
    send.metodo = "cargarListaModificadores";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminModificadores/config_modificadores.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html = html + "<option value='" + datos[i]["idModificador"] + "'>" + datos[i]["Modificador"] + "</option>";
                }
            }
            $("#inModificador").html(html);
            $("#mdl_rdn_pdd_crgnd").hide();
        }
    });
};

var cargarProductosPorCadena = function () {
    $("#mdl_rdn_pdd_crgnd").show();
    var html = "<thead><tr class='active'><th style='text-align:center' width='10%'>NumPlu</th><th style='text-align:center' width='30%'>Descripción</th><th style='text-align:center' width='10%'>Master Plu</th><th style='text-align:center' width='10%'>Estado</th></tr></thead>";
    var send = {};
    send.metodo = "cargarPlusPorCadena";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo")
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    html += "<tr id='tblPrdctSelected" + datos[i]["idProducto"] + "' onclick='seleccionarFilaTablaProductos(" + datos[i]["idProducto"] + ")' ondblclick='modificarFilaTablaProductos(" + datos[i]["idProducto"] + ")'><td class='text-center'>" + datos[i]["numPlu"] + "</td><td style='text-align: left;'>" + datos[i]["descripcion"] + " (" + datos[i]["clasificacion"] + ")</td><td class='text-center'>" + datos[i]["masterPlu"] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $("#plus").html(html);
            $("#plus").dataTable({"destroy": true});
            $("#plus_length").hide();
            $("#mdl_rdn_pdd_crgnd").hide();
        }
    });
};

var cargarProductosPorClasificacion = function (idClasificacion) {
    $("#mdl_rdn_pdd_crgnd").show();
    var html = "<thead><tr class='active'><th style='text-align:center' width='10%'>NumPlu</th><th style='text-align:center' width='30%'>Descripción</th><th style='text-align:center' width='10%'>Master Plu</th><th style='text-align:center' width='10%'>Estado</th></tr></thead>";
    var send = {};
    send.metodo = "cargarPlusPorClasificacion";
    send.idClasificacion = idClasificacion;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var estado = '<input type="checkbox" disabled="disabled">';
                    if (datos[i]['estado'] === "Activo")
                        estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                    html = html + "<tr id='tblPrdctSelected" + datos[i]["idProducto"] + "' onclick='seleccionarFilaTablaProductos(" + datos[i]["idProducto"] + ")' ondblclick='modificarFilaTablaProductos(" + datos[i]["idProducto"] + ")'><td class='text-center'>" + datos[i]["numPlu"] + "</td><td style='text-align: left;'>" + datos[i]["descripcion"] + "</td><td class='text-center'>" + datos[i]["masterPlu"] + "</td><td class='text-center'>" + estado + "</td></tr>";
                }
            }
            $("#plus").html(html);
            $("#plus").dataTable({"destroy": true});
            $("#plus_length").hide();
            $("#mdl_rdn_pdd_crgnd").hide();
        }
    });
};

var seleccionarFilaTablaProductos = function (idProducto) {
    $("#plus tr").removeClass("success");
    $("#tblPrdctSelected" + idProducto).addClass("success");
};

var agregarNuevoProducto = function () {
    $("#titulo1").html("Nuevo Producto");
    $("#mdl_rdn_pdd_crgnd").show();
    $("#mdlProducto").modal("show");
    $("#pstConfigPoliticas").hide();
    setPropiedades();
    cargarPreguntasSugeridasPorCadena();
    $("#btnGuardarCambios").attr("onclick", "validarParametrosProducto(0, 0)");
    $("#mdl_rdn_pdd_crgnd").hide();
    $('#listaCategoriasPrecios input[name="pvp"]').removeAttr("disabled");
    $('#listaCategoriasPrecios input[name="neto"]').css("display", "none");
    $('#listaCategoriasPrecios input[name="iva"]').css("display", "none");
    $("#cntEtiquetaNeto").css("display", "none");
    $("#cntEtiquetaIva").css("display", "none");
};

var modificarFilaTablaProductos = function (idProducto) {
    $("#mdl_rdn_pdd_crgnd").show();
    $("#mdlProducto").modal("show");
    $("#pstConfigPoliticas").show();
    setPropiedades();
    cargarConfiguracionProducto(idProducto);
    $('#listaCategoriasPrecios input[name="neto"]').css("display", "block");
    $('#listaCategoriasPrecios input[name="iva"]').css("display", "block");
    $("#cntEtiquetaNeto").css("display", "block");
    $("#cntEtiquetaIva").css("display", "block");
    $("#btnGuardarCambios").attr("onclick", "validarParametrosProducto(1, " + idProducto + ")");
    $("#btnPluAgregarColeccionPlus").attr("onclick", "nuevaColeccionPlus(" + idProducto + ")");
    cargarPlusColeccionDeDatos(idProducto);
    $("#mdl_rdn_pdd_crgnd").hide();
    $("#listaCategoriasPrecios input").attr("disabled", "disabled");
};

var validarParametrosProducto = function (accion, idProducto) {
    var preciosPorCategoria = "";
    var impuestos = new Array();
    $("#mdl_rdn_pdd_crgnd").show();
    var descripcion = $("#inDescripcionProducto").val();
    var preparacion = $("#inPreparacionProducto").val();
    var idTipoProducto = $("#inTipoProducto").val();
    var idClasificacion = $("#inClasificacionProducto").val();
    var idIntegracionClasificacion = $("#inClasificacionProducto :selected").attr("idIntegracion");
    var codigoBarras = $("#inCodigoBarrasProducto").val();
    var idModificador = $("#inModificador").val();
    var departamento = $("#inDepartamento").val();
    var idIntegracionDepartamento = $("#inDepartamento :selected").attr("idIntegracion");
    var estado = "Inactivo";
    if ($("#inEstadoProducto").is(":checked")) {
        estado = "Activo";
    }
    var anulacion = 0;
    if ($("#inAnulacionProducto").is(":checked")) {
        anulacion = 1;
    }
    var gramo = 0;
    if ($("#inGramosProducto").is(":checked")) {
        gramo = 1;
    }
    var qsr = 0;
    if ($("#inQsrProducto").is(":checked")) {
        qsr = 1;
    }
    var cantidad = 0;
    if ($("#inCantidadProducto").is(":checked")) {
        cantidad = 1;
    }
    if (preparacion.length === 0) {
        preparacion = 0;
    }
    $("#inImpuestosProducto :selected").each(function () {
        impuestos.push(this.value);
    });
    var contenido = $("#inContenidoProducto").val();

    var cargarPvpCategoriaPorProducto = function () {
        var continuar = true;
        $("#listaCategoriasPrecios .row").each(function () {
            var pvp = $("#inCat" + $(this).find("input").attr("idCategoria")).val();
            if (!isNaN(pvp)) {
                if (parseFloat(pvp) >= 0) {
                    preciosPorCategoria += $(this).find("input").attr("idCategoria") + "_" + pvp + "_";
                } else {
                    return false;
                }
            } else {
                return false;
            }
        });
        return continuar;
    };

    if (cargarPvpCategoriaPorProducto()) {
        if (impuestos.length > 0) {
            if (descripcion.length > 4) {
                // if (departamento != "0") {
                    if (contenido.length <= 500) {
                        guardarProducto(accion, idProducto, descripcion, preparacion, idTipoProducto, idClasificacion, idIntegracionClasificacion, codigoBarras, anulacion, gramo, qsr, cantidad, preciosPorCategoria, contenido, departamento, idIntegracionDepartamento, idModificador, estado);
                    } else {
                        $("#mdl_rdn_pdd_crgnd").hide();
                        alertify.error("El contenido del producto no puede tener más de 500 caracteres.");
                    }
                // } else {
                //   $('#mdl_rdn_pdd_crgnd').hide();
                // alertify.error("Debe seleccionar un departamento.");
                //}
            } else {
                $("#mdl_rdn_pdd_crgnd").hide();
                alertify.error("El nombre del producto debe tener por lo menos 4 caracteres.");
            }
        } else {
            $("#mdl_rdn_pdd_crgnd").hide();
            alertify.error("Debe seleccionar por lo menos un impuesto.");
        }
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
        alertify.error("Debe completar los precios por categorias.");
    }
};

var guardarProducto = function (accion, idProducto, descripcion, preparacion, idTipoProducto, idClasificacion, idIntegracionClasificacion, codigoBarras, anulacion, gramo, qsr, cantidad, preciosPorCategoria, contenido, departamento, idIntegracionDepartamento, idModificador, estado) {
    var preguntas = cargarPreguntasSugeridas();
    var canales = "";
    var impuesto1 = 0;
    var impuesto2 = 0;
    var impuesto3 = 0;
    var impuesto4 = 0;
    var impuesto5 = 0;
    var masterPlu = 0;
    if ($("#inMasterPluProducto").attr("idMasterPlu").length > 0) {
        masterPlu = $("#inMasterPluProducto").attr("idMasterPlu");
    }
    $("#inImpuestosProducto :selected").each(function () {
        switch (this.value) {
            case "1":
                impuesto1 = 1;
                break;
            case "2":
                impuesto2 = 1;
                break;
            case "3":
                impuesto3 = 1;
                break;
            case "4":
                impuesto4 = 1;
                break;
            case "5":
                impuesto5 = 1;
                break;
        }
    });
    $("#inCanalImpresionProducto :selected").each(function () {
        canales += this.value + "_";
    });
    $("#filtroClasificacion label").removeClass("active");
    $("#lblTodos").addClass("active");
    var html = "";
    var send = {};
    send.metodo = "guardarProducto";
    send.accion = accion;
    send.idProducto = idProducto;
    send.descripcion = descripcion;
    send.preparacion = preparacion;
    send.idTipoProducto = idTipoProducto;
    send.idClasificacion = idClasificacion;
    send.idIntegracionClasificacion = idIntegracionClasificacion;
    send.codigoBarras = codigoBarras;
    send.anulacion = anulacion;
    send.gramo = gramo;
    send.qsr = qsr;
    send.cantidad = cantidad;
    send.impuesto1 = impuesto1;
    send.impuesto2 = impuesto2;
    send.impuesto3 = impuesto3;
    send.impuesto4 = impuesto4;
    send.impuesto5 = impuesto5;
    send.masterPlu = masterPlu;
    send.preciosPorCategoria = preciosPorCategoria;
    send.canales = canales;
    send.preguntas = preguntas;
    send.contenido = contenido;
    send.departamento = departamento;
    send.idIntegracionDepartamento = idIntegracionDepartamento;
    send.idModificador = idModificador;
    send.estado = estado;
    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminProductos/clienteWSProductos.php",
        data: send,
        success: function (datos) {
            //  if (datos.hasOwnProperty("str")) {
                if (datos.str > 0) {
                    for (var i = 0; i < datos.str; i++) {
                        var estado = '<input type="checkbox" disabled="disabled">';
                        if (datos[i]['estado'] === "Activo")
                            estado = '<input type="checkbox" checked="checked" disabled="disabled">';
                        html = html + "<tr id='tblPrdctSelected" + datos[i]['idProducto'] + "' onclick='seleccionarFilaTablaProductos(" + datos[i]['idProducto'] + ")' ondblclick='modificarFilaTablaProductos(" + datos[i]['idProducto'] + ")'><td class='text-center'>" + datos[i]['numPlu'] + "</td><td style='text-align: left;'>" + datos[i]['descripcion'] + "</td><td class='text-center'>" + datos[i]['masterPlu'] + "</td><td class='text-center'>" + estado + "</td></tr>";
                    }
                    $('#plus').html(html);
                    $('#plus').dataTable({'destroy': true});
                    $("#plus_length").hide();

                }
            $("#mdlProducto").modal("hide");
            $("#mdl_rdn_pdd_crgnd").hide();
            cargarProductosPorCadena();

            ///
            alertify.success("Producto actualizado correctamente.");
            //    } else {
            //      alertify.error("Lo sentimos, ha ocurrido un error.");
            // }

        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#mdlProducto").modal("hide");
            $("#mdl_rdn_pdd_crgnd").hide();
            alertify.error("Servicio No Disponible.");
        }
    });
};

var cargarConfiguracionProducto = function (idProducto) {
    var impuestos = new Array();
    var send = {};
    send.metodo = "cargarConfiguracionProducto";
    send.idProducto = idProducto;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                cargarPrecioPorCategoriasPorProducto(idProducto);
                cargarCanalesImpresionPorProducto(idProducto);
                cargarPreguntasSugeridasPorProducto(idProducto);
                $("#titulo1").html(datos.numPlu + " - " + datos.descripcion);
                $("#inDescripcionProducto").val(datos.descripcion);
                $("#inPreparacionProducto").val(datos.preparacion);
                $("#inTipoProducto").val(datos.idTipoProducto);
                if (datos.idTipoProducto > 0) {
                    $("#inModificador").val(0);
                    $("#inModificador").attr("disabled", "disabled");
                } else {
                    $("#inModificador").val(0);
                    $("#inModificador").removeAttr("disabled");
                }
                $("#inClasificacionProducto").val(datos.idClasificacion);
                $("#inCodigoBarrasProducto").val(datos.codigoBarras);
                $("#inMasterPluProducto").val(datos.masterPlu + " - " + datos.masterDescripcion);
                $("#inDepartamento").val(datos.idDepartamento);
                $("#inModificador").val(datos.idModificador);
                if (datos.estado === "Activo") {
                    $("#inEstadoProducto").prop("checked", true);
                } else {
                    $("#inEstadoProducto").prop("checked", false);
                }
                if (datos.anulacion > 0) {
                    $("#inAnulacionProducto").bootstrapSwitch("state", true);
                }
                if (datos.gramo > 0) {
                    $("#inGramosProducto").bootstrapSwitch("state", true);
                }
                if (datos.qsr > 0) {
                    $("#inQsrProducto").bootstrapSwitch("state", true);
                }
                if (datos.cantidad > 0) {
                    $("#inCantidadProducto").bootstrapSwitch("state", true);
                }
                if (datos.impueto1 === 1) {
                    impuestos.push("1");
                }
                if (datos.impueto2 === 1) {
                    impuestos.push("2");
                }
                if (datos.impueto3 === 1) {
                    impuestos.push("3");
                }
                if (datos.impueto4 === 1) {
                    impuestos.push("4");
                }
                if (datos.impueto5 === 1) {
                    impuestos.push("5");
                }
                $("#inContenidoProducto").val(datos.contenido);
                $("#inImpuestosProducto").val(impuestos).trigger("chosen:updated.chosen");
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#mdlProducto").modal("hide");
            alertify.alert("Error: Error mientras se conectaba con el servidor.");
        }
    });
};

var setPropiedades = function () {
    $("#cntPstModal li").removeClass("active");
    $("#cntParametrosConfiguracion div").removeClass("active fade in");
    $("#pstConfigBoton").addClass("active");
    $("#pstConfigProducto").addClass("active fade in");
    $("#inDescripcionProducto").val("");
    $("#inEstadoProducto").prop("checked", true);
    $("#inAnulacionProducto").bootstrapSwitch("state", false);
    $("#inGramosProducto").bootstrapSwitch("state", false);
    $("#inQsrProducto").bootstrapSwitch("state", false);
    $("#inCantidadProducto").bootstrapSwitch("state", false);
    $("#inImpuestosProducto").val(0);
    $("#inImpuestosProducto").trigger("chosen:updated");
    $("#inCanalImpresionProducto").val(0);
    $("#inCanalImpresionProducto").trigger("chosen:updated");
    $("#inMasterPlus").html(0);
    $("#inDepartamento").val(0);
    if ($("#inTipoProducto").val() > 0) {
        $("#inModificador").val(0);
        $("#inModificador").attr("disabled", "disabled");
    } else {
        $("#inModificador").val(0);
        $("#inModificador").removeAttr("disabled");
    }
    $("#inMasterPluProducto").val("");
    $("#inParametroMasterPluProducto").val("");
    $("#listaPreguntasRelacionadas").html("");
    $("#listaPreguntasSugeridas").html("");
    $("#inContenidoProducto").val("");
    $("#tblColeccionPlus").html("");
    setearPvpCategoriaPorProducto();
};

var cargarTiposProductos = function () {
    var send = {};
    var html = "";
    send.metodo = "cargarTiposProductos";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html = html + '<option value="' + datos[i]['idTipoProducto'] + '">' + datos[i]['tipoProducto'] + '</option>';
                }
            }
            $("#inTipoProducto").html(html);
        }
    });
};

var cargarClasificaciones = function () {
    var send = {};
    var html = "<label id='lblTodos' class='btn btn-default btn active' onclick='cargarProductosPorCadena()'><input type='radio' name='check_todos' value='0'><h6>Todos</h6></label>";
    var combo = "";
    send.metodo = "cargarCanalesClasificacion";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html = html + "<label class='btn btn-default' onclick='cargarProductosPorClasificacion(\"" + datos[i]['idClasificacion'] + "\")'><h6><input id='optionsClas' type='radio' name='options_checks' value='" + datos[i]['idClasificacion'] + "'>" + datos[i]['clasificacion'] + "</h6></label>";
                    combo = combo + "<option value='" + datos[i]['idClasificacion'] + "' idIntegracion='" + datos[i]['idIntegracionClasificacion'] + "'>" + datos[i]['clasificacion'] + "</option>";
                }
                $("#filtroClasificacion").html(html);
                $("#inClasificacionProducto").html(combo);
            }
        }
    });
};

var cargarImpuestos = function () {
    var send = {};
    send.metodo = "cargarImpuestos";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    $("#inImpuestosProducto").append("<option value=" + datos[i]["ordenImpuesto"] + ">" + datos[i]["impuesto"] + "</option>");
                }
                $("#inImpuestosProducto").chosen();
                $("#inImpuestosProducto_chosen").css("width", "570");
            }
        }
    });
};

var cargarCategoriasPorCadena = function () {
    var html = "";
    var send = {};
    send.metodo = "cargarCategoriasPorCadena";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += '<div class="row"><div class="col-xs-2"></div><div class="col-xs-2 text-right"><h6><b>' + datos[i]["categoria"] + '</b></h6></div><div class="col-xs-2"><div class="form-group"><label class="sr-only" for="inCat' + datos[i]["idCategoria"] + '">PVP</label><input type="text" name="pvp" id="inCat' + datos[i]["idCategoria"] + '" idCategoria="' + datos[i]["idCategoria"] + '" integracion="' + datos[i]["idIntegracion"] + '" onkeyup="keyUpPrecioCategoria(\'' + datos[i]["idCategoria"] + '\')" onchange="changePrecioCategoria(\'' + datos[i]["idCategoria"] + '\')" class="form-control" placeholder="Precio Base"/></div></div><div class="col-xs-2"><div class="form-group"><input type="text" name="neto" id="inCatNeto' + datos[i]["idCategoria"] + '" class="form-control" placeholder="0"/></div></div><div class="col-xs-2"><div class="form-group"><input type="text" name="iva" id="inCatIva' + datos[i]["idCategoria"] + '" class="form-control" placeholder="0"/></div></div></div>';
                }
                $("#listaCategoriasPrecios").html(html);
            }
        }
    });
};

var keyUpPrecioCategoria = function (idCategoria) {
    var valor = $("#inCat" + idCategoria).val();
    if (isNaN(valor)) {
        if (parseFloat(valor)) {
            $("#inCat" + idCategoria).val(0);
        }
    }
};

var changePrecioCategoria = function (idCategoria) {
    $("#inCat" + idCategoria).val(parseFloat($("#inCat" + idCategoria).val()));
};

var setearPvpCategoriaPorProducto = function () {
    $("#listaCategoriasPrecios .row").each(function () {
        $("#inCat" + $(this).find("input").attr("idCategoria")).val(0);
        $("#inCatNeto" + $(this).find("input").attr("idCategoria")).val(0);
        $("#inCatIva" + $(this).find("input").attr("idCategoria")).val(0);
    });
};

var cargarPrecioPorCategoriasPorProducto = function (idProducto) {
    var send = {};
    send.metodo = "cargarPrecioPorCategoriasPorPlu";
    send.idProducto = idProducto;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    $("#inCat" + datos[i]["idCategoria"]).val(datos[i]["precioBase"]);
                    $("#inCatNeto" + datos[i]["idCategoria"]).val(datos[i]["neto"]);
                    $("#inCatIva" + datos[i]["idCategoria"]).val(datos[i]["iva"]);
                }
            }
        }
    });
};

var cargarCanalesImpresionPorCadena = function () {
    var send = {};
    send.metodo = "cargarCanalesImpresionPorCadena";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    $("#inCanalImpresionProducto").append("<option value=\"" + datos[i]['idCanal'] + "\">" + datos[i]['canal'] + "</option>");
                }
                $("#inCanalImpresionProducto").chosen();
                $("#inCanalImpresionProducto_chosen").css("width", "570");
            }
        }
    });
};

var cargarCanalesImpresionPorProducto = function (idProducto) {
    var canales = new Array();
    var send = {};
    send.metodo = "cargarCanalesImpresionPorProducto";
    send.idProducto = idProducto;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    canales.push(datos[i]["idCanal"]);
                }
                $("#inCanalImpresionProducto").val(canales).trigger("chosen:updated.chosen");
            }
        }
    });
};

var cargarMasterPlus = function () {
    $("#inMasterPlus").html("<option value=\"0\">-- Seleccionar MasterPlu --</option>");
    var send = {};
    if ($("#inParametroMasterPluProducto").val().length > 0) {
        send.metodo = "cargarMasterPlus";
        send.parametro = $("#inParametroMasterPluProducto").val();
        $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    for (var i = 0; i < datos.str; i++) {
                        $("#inMasterPlus").append("<option value=\"" + datos[i]["numPlu"] + "\">" + datos[i]["numPlu"] + "-" + datos[i]["producto"] + "</option>");
                    }
                }
            }
        });
    }
};

var cargarPreguntasSugeridasPorCadena = function () {
    var send = {};
    send.metodo = "cargarPreguntasSueridasPorCadena";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    $("#listaPreguntasSugeridas").append("<tr id=\"idPSugerida" + datos[i]["idPregunta"] + "\"><td>" + datos[i]["pregunta"] + "<button type=\"button\" class=\"btn btn-success btn-sm aling-btn-right\" onclick=\"agregarPregunta(this)\"><span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span></button></td></tr>");
                }
            }
        }
    });
};

var cargarPreguntasSugeridasPorProducto = function (idProducto) {
    var j = 0;
    var send = {};
    send.metodo = "cargarPreguntasSueridasPorProducto";
    send.idProducto = idProducto;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    if (datos[i]["orden"] >= 0) {
                        $("#listaPreguntasRelacionadas").append("<tr id=\"idPSugerida" + datos[i]["idPregunta"] + "\" orden=\"" + (j + 1) + "\" ondragover=\"allowDrop(event)\" ondragstart=\"drag(event)\" ondrop=\"drop(event)\" draggable=\"true\"><td>" + datos[i]["pregunta"] + "<button type=\"button\" class=\"btn btn-danger btn-sm aling-btn-right\" onclick=\"quitarPregunta(this)\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button></td></tr>");
                        j++;
                    } else {
                        $("#listaPreguntasSugeridas").append("<tr id=\"idPSugerida" + datos[i]["idPregunta"] + "\"><td>" + datos[i]["pregunta"] + "<button type=\"button\" class=\"btn btn-success btn-sm aling-btn-right\" onclick=\"agregarPregunta(this)\"><span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span></button></td></tr>");
                    }
                }
            }
        }
    });
};

var changeMasterPlu = function () {
    $("#inMasterPluProducto").val($("#inMasterPlus").val() + " - " + $("#inMasterPlus :selected").html());
    $("#inMasterPluProducto").attr("idMasterPlu", $("#inMasterPlus").val());
};

var allowDrop = function (ev) {
    ev.preventDefault();
};

var drag = function (ev) {
    ev.dataTransfer.setData("text", ev.target.id);
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
    $("#" + inicia).removeClass("success");
};

var agregarPregunta = function (parametro) {
    $(parametro).removeClass("btn-success");
    $(parametro).addClass("btn-danger");
    $(parametro).attr("onclick", "quitarPregunta(this)");
    $(parametro).find("span").removeClass("glyphicon-ok");
    $(parametro).find("span").addClass("glyphicon-remove");
    $("#listaPreguntasRelacionadas").append("<tr id=\"" + (parametro.parentNode).parentNode.id + "\" orden=\"" + ($("#listaPreguntasRelacionadas >tbody >tr").length + 1) + "\" ondragover=\"allowDrop(event)\" ondragstart=\"drag(event)\" ondrop=\"drop(event)\" draggable=\"true\">" + $("#" + (parametro.parentNode).parentNode.id).html() + "</tr>");
    $("#" + (parametro.parentNode).parentNode.id).remove();
};

var quitarPregunta = function (parametro) {
    $(parametro).removeClass("btn-danger");
    $(parametro).addClass("btn-success");
    $(parametro).attr("onclick", "agregarPregunta(this)");
    $(parametro).find("span").removeClass("glyphicon-remove");
    $(parametro).find("span").addClass("glyphicon-ok");
    $("#" + (parametro.parentNode).parentNode.id).removeAttr("ondragover");
    $("#" + (parametro.parentNode).parentNode.id).removeAttr("ondragstart");
    $("#" + (parametro.parentNode).parentNode.id).removeAttr("ondrop");
    $("#" + (parametro.parentNode).parentNode.id).removeAttr("draggable");
    var id = (parametro.parentNode).parentNode.id;
    var html = $("#" + (parametro.parentNode).parentNode.id).html();
    $("#listaPreguntasSugeridas").append("<tr id=\"" + id + "\">" + html + "</tr>");
    $("#listaPreguntasRelacionadas").find("tr#" + id).remove();
    var i = 0;
    $("#listaPreguntasRelacionadas tr").each(function () {
        $("#" + this.id).attr("orden", (i + 1));
        i++;
    });
};

var cargarPreguntasSugeridas = function () {
    var preguntas = "";
    $("#listaPreguntasRelacionadas tr").each(function () {
        preguntas += (this.id).substr(11, (this.id).lenght) + "_" + $("#" + this.id).attr("orden") + "_";
    });
    return preguntas;
};

var cargarColeccionDepartamentos = function () {
    var html = "<option value='0' idIntegracion='0'>-- Seleccionar Departamento --</option>";
    var send = {};
    send.metodo = "cargarColeccionDepartamentos";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            var registros = datos.str;
            for (var i = 0; i < registros; i++) {
                html += "<option value='" + datos[i]["idDepartamento"] + "' idIntegracion='" + datos[i]["idIntegracionDepartamento"] + "'>" + datos[i]["departamento"] + "</option>";
            }
            $("#inDepartamento").html(html);
        },
        error: function () {
            alertify.error("No existen departamentos configurados para esta cadena.");
        }
    });
};
/*
var actualizarColeccionDepartamentos = function () {
    var html = "<option value='0' idIntegracion='0'>-- Seleccionar Departamento --</option>";
    $("#mdl_rdn_pdd_crgnd").show();
    var send = {};
    send.metodo = "actualizarColeccionDepartamentos";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/clienteWSDepartamentos.php", data: send,
        success: function (datos) {
            $("#mdl_rdn_pdd_crgnd").hide();
            if (datos.hasOwnProperty("mensaje") && datos.hasOwnProperty("estado")) {
                if (datos.estado > 0) {
                    var departamentos = JSON.parse(datos.departamentos);
                    var registros = departamentos["str"];
                    for (var i = 0; i < registros; i++) {
                        html += "<option value='" + departamentos[i]["idDepartamento"] + "' idIntegracion='" + departamentos[i]["idIntegracionDepartamento"] + "'>" + departamentos[i]["departamento"] + "</option>";
                    }
                    $("#inDepartamento").html(html);
                    alertify.success(datos.mensaje);
                } else {
                    alertify.error(datos.mensaje);
                }
            } else {
                alertify.error("Lo sentimos, ha ocurrido un error.");
            }
        },
        error: function () {
            $("#mdl_rdn_pdd_crgnd").hide();
            alertify.error("Servicio no disponible.");
        }
    });
};*/

var cargarPlusColeccionDeDatos = function (idProducto) {
    var html = "";
    var send = {};
    send.idProducto = idProducto;
    send.metodo = "cargarPlusColeccionDeDatos";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var valorPolitica = evaluarValorPolitica(datos[i]);
                    var datoPolitica = datos[i];

                    //html += "<tr id='tbrColeccionPlus" + datos[i]['idColeccionPlus'] + "_" + datos[i]['idColeccionDeDatosPlus'] + "_" + datos[i]['idPlu'] + "' onclick='seleccionarColeccion(\"" + datos[i]['idColeccionPlus'] + "\", \"" + datos[i]['idColeccionDeDatosPlus'] + "\", " + datos[i]['idPlu'] + ")'><td style='width: 150px;'>" + datos[i]['descripcionColeccion'] + "</td><td style='width: 150px;'>" + datos[i]['descripcionDato'] + "</td>";
                    html += "<tr id='tbrColeccionPlus" + datos[i]['idColeccionPlus'] + "_" + datos[i]['idColeccionDeDatosPlus'] + "_" + datos[i]['idPlu'] + "' onclick='seleccionarColeccion(\"" + datoPolitica.idColeccionPlus + "\", \"" + datoPolitica.idColeccionDeDatosPlus + "\", " + datoPolitica.idPlu + ", \"" + datoPolitica.descripcionColeccion + "\", " + datoPolitica.especificarValor + ", \"" + datoPolitica.obligatorio + "\", " + datoPolitica.activo + ", \"" + datoPolitica.tipoDeDato + "\", \"" + datoPolitica.caracter + "\", \"" + datoPolitica.entero + "\", \"" + datoPolitica.fecha + "\", \"" + datoPolitica.seleccion + "\", \"" + datoPolitica.numerico + "\", \"" + datoPolitica.fechaIni + "\", \"" + datoPolitica.fechaFin + "\", \"" + datoPolitica.min + "\", \"" + datoPolitica.max + "\")'><td style='width: 150px;'>" + datos[i]['descripcionColeccion'] + "</td><td style='width: 150px;'>" + datos[i]['descripcionDato'] + "</td>";

                    if (datos[i]['especificarValor'] === 1) {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } else {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" disabled/></td>';
                    }

                    if (datos[i]['obligatorio'] === 1) {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } else {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" disabled/></td>';
                    }

                    html += '<td style="width: 70px;">' + datos[i]['tipoDeDato'] + '</td><td style="width: 300px;">' + valorPolitica + '</td>';

                    if (datos[i]['activo'] === 1) {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" checked="checked" disabled/></td></tr>';
                    } else {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" disabled/></td></tr>';
                    }
                }
            } else {
                html += '<tr><th colspan="14" class="text-center">No existen registros.</th></tr>';
            }
            $("#tblColeccionPlus").html(html);
        },
        error: function () {
            alertify.error("Error.");
        }
    });
};

var seleccionarColeccion = function (idColeccionPlus, idColeccionDeDatosPlus, idPlu, descripcion, especificaValor, obligatorio, estado, tipoDeDato, varchar, entero, fecha, seleccion, numerico, fechaIni, fechaFin, min, max) {
    if (seleccion == "SI") {
        seleccion = 1;
    } else {
        seleccion = 0;
    }

    if ($("#tblColeccionPlus").find(".success").attr("id") !== "tbrColeccionPlus" + idColeccionPlus + "_" + idColeccionDeDatosPlus + "_" + idPlu) {
        $("#tblColeccionPlus tr").removeClass("success");
        $("#tbrColeccionPlus" + idColeccionPlus + "_" + idColeccionDeDatosPlus + "_" + idPlu).addClass("success");
        $("#btnPluEditarColeccionPlus").attr("onclick", "editarColeccionPlus('" + idColeccionPlus + "', '" + idColeccionDeDatosPlus + "', " + idPlu + ", '" + descripcion + "', " + especificaValor + ", " + obligatorio + ", " + estado + ", '" + tipoDeDato + "', '" + varchar + "', '" + entero + "', '" + fecha + "', " + seleccion + ", '" + numerico + "', '" + fechaIni + "', '" + fechaFin + "', '" + min + "', '" + max + "')");
    }
};

var editarColeccionPlus = function (idColeccionPlus, idColeccionDeDatosPlus, idPlu, descripcion, especificaValor, obligatorio, estado, tipoDeDato, varchar, entero, fecha, seleccion, numerico, fechaIni, fechaFin, min, max) {
    var seleccionColeccion = $("#tblColeccionPlus").find("tr.success").attr("id");

    if (seleccionColeccion != undefined) {
        if (idColeccionPlus !== null) {
            $("#mdlProducto").modal("hide");
            var descripcion = descripcion;
            var especificaValor = especificaValor;
            var obligatorio = obligatorio;
            var tipoDeDato = tipoDeDato;
            var varchar = varchar;
            var entero = entero;
            var fecha = fecha;
            var seleccion = seleccion;
            var numerico = numerico;
            var fechaIni = fechaIni;
            var fechaFin = fechaFin;
            var min = min;
            var max = max;
            var estado = estado;

            $("#mdlPluEditarColeccionTitulo").html(descripcion);
            $("#mdlPluEditarColeccionTitulo").attr("data-idColeccionPlus", idColeccionPlus);
            $("#mdlPluEditarColeccionTitulo").attr("data-idColeccionDeDatosPlus", idColeccionDeDatosPlus);
            $("#mdlPluEditarColeccionTitulo").attr("data-idPlu", idPlu);
            $("#inpPluEditarEstado").prop("checked", true);

            if (estado === 0) {
                $("#inpPluEditarEstado").prop("checked", false);
            }

            $("#inpPluEditarEspecificaValor").prop("checked", true);

            if (especificaValor === 0) {
                $("#inpPluEditarEspecificaValor").prop("checked", false);
                $("#especificaValor").val(0);
            } else {
                $("#especificaValor").val(1);
            }

            $("#inpPluEditarObligatorio").prop("checked", true);

            if (obligatorio === 0) {
                $("#inpPluEditarObligatorio").prop("checked", false);
                $("#obligatorio").val(0);
            } else {
                $("#obligatorio").val(1);
            }

            $("#lblPluEditarTipoDato").html(tipoDeDato);
            $("#inpPluEditarCaracter").val(varchar);
            $("#inpPluEditarEntero").val(entero);
            $("#inpPluEditarFecha").daterangepicker({
                singleDatePicker: true,
                minDate: moment(),
                format: "DD/MM/YYYY HH:mm",
                drops: "up"
            });

            $("#inpPluEditarFecha").val(fecha);
            $("#inpPluEditarSeleccion").bootstrapSwitch("state", false);

            if (seleccion === 1) {
                $("#inpPluEditarSeleccion").bootstrapSwitch("state", true);
            }

            $("#inpPluEditarNumerico").val(numerico);
            $("#inpPluEditarFechaIni").daterangepicker({
                singleDatePicker: true,
                minDate: moment(),
                format: "DD/MM/YYYY HH:mm",
                drops: "up"
            });

            $("#inpPluEditarFechaIni").val(fechaIni);
            $("#inpPluEditarFechaFin").daterangepicker({
                singleDatePicker: true,
                minDate: moment(),
                format: "DD/MM/YYYY HH:mm",
                drops: "up"
            });

            $("#inpPluEditarFechaFin").val(fechaFin);
            $("#inpPluEditarMin").val(min);
            $("#inpPluEditarMax").val(max);
            $("#mdlPluEditarColeccion").modal("show");
        } else {
            alertify.error("Seleccionar un registro.");
        }
    } else {
        alertify.error("<b>Alerta!</b> Debe Seleccionar un Registro.");
    }
};

var cancelar = function () {
    $("#mdlProducto").modal("show");
};

var validarEditarColeccionPlus = function () {
    $("#mdl_rdn_pdd_crgnd").show();
    var tipoDato = $("#lblPluEditarTipoDato").html();
    var varchar = "null";
    var entero = "null";
    var fecha = "null";
    var seleccion = "null";
    var numerico = "null";
    var fechaIni = "null";
    var fechaFin = "null";
    var min = "null";
    var max = "null";
    var validar = true;
    var validarFecha = true;
    var especificaValor = $("#especificaValor").val();
    var obligatorio = $("#obligatorio").val();

    //Tipos de dato en SP PRODUCTOS_plus_configuracion_colecciones opcion
    if (especificaValor === 1 || obligatorio === 1) {
        if (tipoDato === "Entero") {
            entero = $("#inpPluEditarEntero").val();
            if (entero === "") {
                validar = false;
            }
        } else if (tipoDato === "Fecha") {
            fecha = "'" + $("#inpPluEditarFecha").val() + "'";
            if (fecha === "''") {
                validar = false;
            }
        } else if (tipoDato === "Fecha Inicio-Fin") {
            fechaIni = $("#inpPluEditarFechaIni").val();
            fechaFin = $("#inpPluEditarFechaFin").val();
            if (fechaIni === "" || fechaFin === "") {
                validar = false;
            } else if (!validarFechaHora(fechaIni) || !validarFechaHora(fechaFin)) {
                validar = false;
                validarFecha = false;
            } else {
                fechaIni = "'" + fechaIni + "'";
                fechaFin = "'" + fechaFin + "'";
            }
        } else if (tipoDato === "Caracter") {
            varchar = "'" + $("#inpPluEditarCaracter").val() + "'";
            if (varchar === "''") {
                validar = false;
            }
        } else if (tipoDato === "Numerico") {
            numerico = $("#inpPluEditarNumerico").val();
            if (numerico === "") {
                validar = false;
            }
        } else if (tipoDato === "Seleccion") {
            seleccion = $("#inpPluEditarSeleccion").bootstrapSwitch('state');

            if (seleccion) {
                seleccion = 1;
            } else {
                seleccion = 0;
            }
        } else if (tipoDato === "Minimo-Maximo") {
            min = $("#inpPluEditarMin").val();
            max = $("#inpPluEditarMax").val();
            if (min === "" || max === "") {
                validar = false;
            }
        }
    } else {
        validar = true;

        entero = $("#inpPluEditarEntero").val();
        fecha = "'" + $("#inpPluEditarFecha").val() + "'";
        seleccion = $("#inpPluEditarSeleccion").bootstrapSwitch('state');
        fechaIni = "'" + $("#inpPluEditarFechaIni").val() + "'";
        fechaFin = "'" + $("#inpPluEditarFechaFin").val() + "'";
        varchar = $("#inpPluEditarCaracter").val();
        numerico = $("#inpPluEditarNumerico").val();
        seleccion = $("#inpPluEditarSeleccion").bootstrapSwitch('state');
        min = $("#inpPluEditarMin").val();
        max = $("#inpPluEditarMax").val();

        if (entero === "") {
            entero = "NULL";
        }
        if (fecha === "") {
            fecha = "";
        }
        if (seleccion) {
            seleccion = 1;
        } else {
            seleccion = 0;
        }
        if (fechaIni === "" || fechaFin === "") {
            fechaIni = "";
            fechaFin = "";
        }
        if (varchar === "") {
            varchar = "";
        }
        if (numerico === "") {
            numerico = "NULL";
        }
        if (min === "" || max === "") {
            min = "NULL";
            max = "NULL";
        }
    }

    if (validar) {
        guardarEditarColeccionPlus(varchar, entero, fecha, seleccion, numerico, fechaIni, fechaFin, min, max);
    } else {
        if (validarFecha) {
            alertify.error("Llenar el campo o los campos del tipo de dato " + tipoDato);
        } else {
            alertify.error("La fecha ingresada es inválida");
        }
    }
    $("#mdl_rdn_pdd_crgnd").hide();
};

function validarFechaHora(fechaHora) {
    var formato = /^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4} ([0-9]|[01][0-9]|2[0-3]):([0-5][0-9])$/;
    return fechaHora.match(formato);
}

var guardarEditarColeccionPlus = function (varchar, entero, fecha, seleccion, numerico, fechaIni, fechaFin, min, max) {
    var idColeccionPlus = $("#mdlPluEditarColeccionTitulo").attr("data-idColeccionPlus");
    var idColeccionDeDatosPlus = $("#mdlPluEditarColeccionTitulo").attr("data-idColeccionDeDatosPlus");
    var idPlu = $("#mdlPluEditarColeccionTitulo").attr("data-idPlu");
    var estado = 0;
    if ($("#inpPluEditarEstado").is(":checked")) {
        estado = 1;
    }

    var html = "";
    var send = {};
    send.idColeccionPlus = idColeccionPlus;
    send.idColeccionDeDatosPlus = idColeccionDeDatosPlus;
    send.idPlu = idPlu;
    send.varchar = varchar;
    send.entero = entero;
    send.fecha = fecha;
    send.seleccion = seleccion;
    send.numerico = numerico;
    send.fechaIni = fechaIni;
    send.fechaFin = fechaFin;
    send.min = min;
    send.max = max;
    send.estado = estado;
    send.metodo = "editarColeccionPlu";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var valorPolitica = evaluarValorPolitica(datos[i]);
                    var datoPolitica = datos[i];

                    html += "<tr id='tbrColeccionPlus" + datos[i]['idColeccionPlus'] + "_" + datos[i]['idColeccionDeDatosPlus'] + "_" + datos[i]['idPlu'] + "' onclick='seleccionarColeccion(\"" + datoPolitica.idColeccionPlus + "\", \"" + datoPolitica.idColeccionDeDatosPlus + "\", " + datoPolitica.idPlu + ", \"" + datoPolitica.descripcionColeccion + "\", " + datoPolitica.especificarValor + ", \"" + datoPolitica.obligatorio + "\", " + datoPolitica.activo + ", \"" + datoPolitica.tipoDeDato + "\", \"" + datoPolitica.caracter + "\", \"" + datoPolitica.entero + "\", \"" + datoPolitica.fecha + "\", \"" + datoPolitica.seleccion + "\", \"" + datoPolitica.numerico + "\", \"" + datoPolitica.fechaIni + "\", \"" + datoPolitica.fechaFin + "\", \"" + datoPolitica.min + "\", \"" + datoPolitica.max + "\")'><td style='width: 150px;'>" + datos[i]['descripcionColeccion'] + "</td><td style='width: 150px;'>" + datos[i]['descripcionDato'] + "</td>";

                    if (datos[i]['especificarValor'] === 1) {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } else {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" disabled/></td>';
                    }

                    if (datos[i]['obligatorio'] === 1) {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } else {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" disabled/></td>';
                    }

                    html += '<td style="width: 70px;">' + datos[i]['tipoDeDato'] + '</td><td style="width: 300px;">' + valorPolitica + '</td>';

                    if (datos[i]['activo'] === 1) {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" checked="checked" disabled/></td></tr>';
                    } else {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" disabled/></td></tr>';
                    }
                }
            } else {
                html += '<tr><th colspan="14" class="text-center">No existen registros.</th></tr>';
            }
            $("#tblColeccionPlus").html(html);
            $("#mdlProducto").modal("show");
            $("#mdlPluEditarColeccion").modal("hide");
            alertify.success("Colección modificada exitosamente.");
        },
        error: function () {
            alertify.error("Error");
        }
    });
};

function evaluarValorPolitica(fila) {
    var tipoDato = fila.tipoDeDato.trim();

    switch (tipoDato) {
        case "Entero":
            return fila.entero;
            break;
        case "Caracter":
            return fila.caracter;
            break;
        case "Seleccion":
            return fila.seleccion;
            break;
        case "Numerico":
            return fila.numerico;
            break;
        case "Fecha":
            return fila.fecha;
            break;
        case "Fecha Inicio-Fin":
            return fila.fechaIni + " - " + fila.fechaFin;
            break;
        case "Minimo-Maximo":
            return fila.min + " - " + fila.max;
            break;
        default:
            return fila.caracter;
            break;
    }
}

var nuevaColeccionPlus = function (idPlu) {
    $("#mdlProducto").modal("hide");
    limpiarDivPluColeccionTiposDato();
    $("#tblPluColeccionDatos").html("");
    cargarPluColeccionDescripcion(idPlu);

    $("#inpPluNuevoFecha").daterangepicker({
        singleDatePicker: true,
//        timePicker: true,
//        timePicker24Hour: true,
//        timePickerIncrement: 1,
        minDate: moment(),
        format: "DD/MM/YYYY HH:mm",
        drops: "up"
    });
    $("#inpPluNuevoFechaIni").daterangepicker({
        singleDatePicker: true,
//        timePicker: true,
//        timePicker24Hour: true,
//        timePickerIncrement: 1,
        minDate: moment(),
        format: "DD/MM/YYYY HH:mm",
        drops: "up"
    });
    $("#inpPluNuevoFechaFin").daterangepicker({
        singleDatePicker: true,
//        timePicker: true,
//        timePicker24Hour: true,
//        timePickerIncrement: 1,
        minDate: moment(),
        format: "DD/MM/YYYY HH:mm",
        drops: "up"
    });

    $("#mdlPluNuevaColeccion").modal("show");
};

var limpiarDivPluColeccionTiposDato = function () {
    $("#divPluColeccionTiposDato").hide();
    $("#inpPluNuevoCaracter").val("");
    $("#inpPluNuevoEntero").val("");
    $("#inpPluNuevoEspecificaValor").prop("checked", false);
    $("#inpPluNuevoFecha").val("");
    $("#inpPluNuevoFechaFin").val("");
    $("#inpPluNuevoFechaIni").val("");
    $("#inpPluNuevoMax").val("");
    $("#inpPluNuevoMin").val("");
    $("#inpPluNuevoNumerico").val("");
    $("#inpPluNuevoObligatorio").prop("checked", false);
    $("#inpPluNuevoSeleccion").bootstrapSwitch("state");
};

var cargarPluColeccionDescripcion = function (idPlu) {
    var html = "<tr class='bg-primary'><th class='text-center'>Descripción</th></tr>";
    var send = {};
    send.metodo = "cargarPluColeccionDescripcion";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += "<tr id='tbrColeccionDescripcion" + datos[i]['idColeccionPlus'] + "' onclick='seleccionarColeccionDescripcion(\"" + datos[i]['idColeccionPlus'] + "\", \"" + datos[i]['descripcion'] + "\", " + idPlu + ")' class='text-left'><td>" + datos[i]['descripcion'] + "</td></tr>";
                }
            } else {
                html += "<tr><th class='text-center'>No existen registros</th></tr>";
            }
            $("#tblPluColeccionDescripcion").html(html);
        },
        error: function () {
            alertify.error("Error");
        }
    });
};

var seleccionarColeccionDescripcion = function (idColeccionPlus, descripcion, idPlu) {
    if ($("#tblPluColeccionDescripcion").find(".success").attr("id") !== "tbrColeccionDescripcion" + idColeccionPlus) {
        $("#tblPluColeccionDescripcion tr").removeClass("success");
        $("#tbrColeccionDescripcion" + idColeccionPlus).addClass("success");
        cargarPluColeccionDatos(idColeccionPlus, idPlu);
        $('#mdlPluNuevaColeccionTitulo').text(descripcion);
        $("#divPluColeccionTiposDato").hide();
    }
};

var cargarPluColeccionDatos = function (idColeccionPlus, idPlu) {
    var html = "<tr class='bg-primary'><th class='text-center'>Datos</th></tr>";
    var send = {};
    send.metodo = "cargarPluColeccionDatos";
    send.idColeccionPlus = idColeccionPlus;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += "<tr id='tbrColeccionDato" + datos[i]['idColeccionDatosPlus'] + "' onclick='seleccionarColeccionDato(\"" + datos[i]['idColeccionDatosPlus'] + "\", \"" + idColeccionPlus + "\", " + idPlu + ", " + datos[i]['especificarValor'] + ", " + datos[i]['obligatorio'] + ", \"" + datos[i]['tipoDeDato'] + "\")'><td>" + datos[i]['descripcion'] + "</td></tr>";
                }
            } else {
                html += "<tr><th class='text-center'>No existen registros</th></tr>";
            }
            $("#tblPluColeccionDatos").html(html);
        },
        error: function () {
            alertify.error("Error");
        }
    });
};

var seleccionarColeccionDato = function (idColeccionDatosPlus, idColeccionPlus, idPlu, especificaValor, obligatorio, tipoDato) {
    if ($("#tblPluColeccionDatos").find(".success").attr("id") !== "tbrColeccionDato" + idColeccionDatosPlus) {
        $("#tblPluColeccionDatos tr").removeClass("success");
        $("#tbrColeccionDato" + idColeccionDatosPlus).addClass("success");
        limpiarDivPluColeccionTiposDato();
        cargarColeccionTiposDato(idColeccionDatosPlus, idColeccionPlus, idPlu, especificaValor, obligatorio, tipoDato);
        $("#divPluColeccionTiposDato").show();
    }
};

var cargarColeccionTiposDato = function (idColeccionDatosPlus, idColeccionPlus, idPlu, especificaValor, obligatorio, tipoDato) {
    if (especificaValor === 1) {
        $("#inpPluNuevoEspecificaValor").prop("checked", true);
        $("#especificaValor").val(1);
    } else {
        $("#especificaValor").val(0);
    }

    if (obligatorio === 1) {
        $("#inpPluNuevoObligatorio").prop("checked", true);
        $("#obligatorio").val(1);
    } else {
        $("#obligatorio").val(0);
    }

    $("#lblPluNuevoTipoDato").text(tipoDato);
    $("#btnPluNuevaColeccionPlus").attr("onclick", "validarNuevaColeccionPlus('" + idColeccionDatosPlus + "', '" + idColeccionPlus + "', " + idPlu + ")");
};

var validarNuevaColeccionPlus = function (idColeccionDatosPlus, idColeccionPlus, idPlu) {
    var tipoDato = $("#lblPluNuevoTipoDato").text();
    var varchar = "null";
    var entero = "null";
    var fecha = "null";
    var seleccion = "null";
    var numerico = "null";
    var fechaIni = "null";
    var fechaFin = "null";
    var min = "null";
    var max = "null";
    var validar = true;
    var validarFecha = true;

    if ($("#tblPluColeccionDescripcion").find(".success").attr("id")) {
        if ($("#tblPluColeccionDatos").find(".success").attr("id")) {
            if (idColeccionDatosPlus !== null) {
                if (idColeccionPlus !== null) {
                    if (idPlu !== null) {
                        //Tipos de dato en SP PRODUCTOS_plus_configuracion_colecciones opcion 0
                        var espicificaValor = $("#especificaValor").val();
                        var obligatorio = $("#obligatorio").val();

                        if (espicificaValor === 1 || obligatorio === 1) {
                            if (tipoDato === "Entero") {
                                entero = $("#inpPluNuevoEntero").val();
                                if (entero === "") {
                                    validar = false;
                                }
                            } else if (tipoDato === "Fecha") {
                                fecha = "'" + $("#inpPluNuevoFecha").val() + "'";
                                if (fecha === "") {
                                    validar = false;
                                }
                            } else if (tipoDato === "Fecha Inicio-Fin") {
                                fechaIni = $("#inpPluNuevoFechaIni").val();
                                fechaFin = $("#inpPluNuevoFechaFin").val();
                                if (fechaIni === "" || fechaFin === "") {
                                    validar = false;
                                } else if (!validarFechaHora(fechaIni) || !validarFechaHora(fechaFin)) {
                                    validar = false;
                                    validarFecha = false;
                                } else {
                                    fechaIni = "'" + fechaIni + "'";
                                    fechaFin = "'" + fechaFin + "'";
                                }
                            } else if (tipoDato === "Caracter") {
                                varchar = $("#inpPluNuevoCaracter").val();
                                if (varchar === "") {
                                    validar = false;
                                }
                            } else if (tipoDato === "Numerico") {
                                numerico = $("#inpPluNuevoNumerico").val();
                                if (numerico === "") {
                                    validar = false;
                                }
                            } else if (tipoDato === "Seleccion") {
                                seleccion = $("#inpPluNuevoSeleccion").bootstrapSwitch('state');
                            } else if (tipoDato === "Minimo-Maximo") {
                                min = $("#inpPluNuevoMin").val();
                                max = $("#inpPluNuevoMax").val();
                                if (min === "" || max === "") {
                                    validar = false;
                                }
                            }
                        } else {
                            validar = true;

                            entero = $("#inpPluNuevoEntero").val();
                            fecha = "'" + $("#inpPluNuevoFecha").val() + "'";
                            fechaIni = "'" + $("#inpPluNuevoFechaIni").val() + "'";
                            fechaFin = "'" + $("#inpPluNuevoFechaFin").val() + "'";
                            varchar = $("#inpPluNuevoCaracter").val();
                            numerico = $("#inpPluNuevoNumerico").val();
                            seleccion = $("#inpPluNuevoSeleccion").bootstrapSwitch('state');
                            min = $("#inpPluNuevoMin").val();
                            max = $("#inpPluNuevoMax").val();

                            if (entero === "") {
                                entero = "NULL";
                            }
                            if (fecha === "") {
                                fecha = "";
                            }
                            if (seleccion) {
                                seleccion = 1;
                            } else {
                                seleccion = 0;
                            }
                            if (fechaIni === "" || fechaFin === "") {
                                fechaIni = "";
                                fechaFin = "";
                            }
                            if (varchar === "") {
                                varchar = "";
                            }
                            if (numerico === "") {
                                numerico = "NULL";
                            }
                            if (min === "" || max === "") {
                                min = "NULL";
                                max = "NULL";
                            }
                        }

                        if (validar) {
                            guardarNuevaColeccionPlus(idColeccionDatosPlus, idColeccionPlus, idPlu, varchar, entero, fecha, seleccion, numerico, fechaIni, fechaFin, min, max);
                        } else {
                            if (validarFecha) {
                                alertify.error("Llenar el campo o los campos del tipo de dato " + tipoDato);
                            } else {
                                alertify.error("La fecha ingresada es inválida");
                            }
                        }
                    } else {
                        alertify.error("Error");
                    }
                } else {
                    alertify.error("Error");
                }
            } else {
                alertify.error("Error");
            }
        } else {
            alertify.error("No se ha seleccionado el dato");
        }
    } else {
        alertify.error("No se ha seleccionado la descripción");
    }
};

var guardarNuevaColeccionPlus = function (idColeccionDatosPlus, idColeccionPlus, idPlu, varchar, entero, fecha, seleccion, numerico, fechaIni, fechaFin, min, max) {
    var html = "";
    var send = {};
    send.metodo = "guardarNuevaColeccionPlus";
    send.idColeccionDatosPlus = idColeccionDatosPlus;
    send.idColeccionPlus = idColeccionPlus;
    send.idPlu = idPlu;
    send.varchar = varchar;
    send.entero = entero;
    send.fecha = fecha;
    send.seleccion = seleccion;
    send.numerico = numerico;
    send.fechaIni = fechaIni;
    send.fechaFin = fechaFin;
    send.min = min;
    send.max = max;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminProductos/config_productos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var valorPolitica = evaluarValorPolitica(datos[i]);
                    var datoPolitica = datos[i];

                    html += "<tr id='tbrColeccionPlus" + datos[i]['idColeccionPlus'] + "_" + datos[i]['idColeccionDeDatosPlus'] + "_" + datos[i]['idPlu'] + "' onclick='seleccionarColeccion(\"" + datoPolitica.idColeccionPlus + "\", \"" + datoPolitica.idColeccionDeDatosPlus + "\", " + datoPolitica.idPlu + ", \"" + datoPolitica.descripcionColeccion + "\", " + datoPolitica.especificarValor + ", \"" + datoPolitica.obligatorio + "\", " + datoPolitica.activo + ", \"" + datoPolitica.tipoDeDato + "\", \"" + datoPolitica.caracter + "\", \"" + datoPolitica.entero + "\", \"" + datoPolitica.fecha + "\", \"" + datoPolitica.seleccion + "\", \"" + datoPolitica.numerico + "\", \"" + datoPolitica.fechaIni + "\", \"" + datoPolitica.fechaFin + "\", \"" + datoPolitica.min + "\", \"" + datoPolitica.max + "\")'><td style='width: 150px;'>" + datos[i]['descripcionColeccion'] + "</td><td style='width: 150px;'>" + datos[i]['descripcionDato'] + "</td>";

                    if (datos[i]['especificarValor'] === 1) {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } else {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" disabled/></td>';
                    }

                    if (datos[i]['obligatorio'] === 1) {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } else {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" disabled/></td>';
                    }

                    html += '<td style="width: 70px;">' + datos[i]['tipoDeDato'] + '</td><td style="width: 300px;">' + valorPolitica + '</td>';

                    if (datos[i]['activo'] === 1) {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" checked="checked" disabled/></td></tr>';
                    } else {
                        html += '<td style="width: 70px;"><input type="checkbox" value="1" disabled/></td></tr>';
                    }
                }
            } else {
                html += '<tr><th colspan="14" class="text-center">No existen registros.</th></tr>';
            }
            $("#tblColeccionPlus").html(html);
            $("#mdlProducto").modal("show");
            $("#mdlPluNuevaColeccion").modal("hide");
            alertify.success("Colección ingresada exitosamente.");
        },
        error: function () {
            alertify.error("Error");
        }
    });
};