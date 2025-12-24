/*global alertify*/
/*global moment*/
var ambiente;
var numeroLoteActivo = "";
var usuario = $("#idUserReplica").val();

$(document).ready(function () {
    cargarAmbiente();

    $("#modalAnulacionLoteDistribuidor").modal({
        show: false
    });

    $("#modalAnulacionLoteDistribuidor").on("hide.bs.modal", function () {
        numeroLoteActivo = "";
        $("#tituloModalNumeroLote").html(numeroLoteActivo);
        $("#observacionAnulacionLote").val("");
    });

    $("#btnEjecutarAnulacionLote").click(function () {
        var textoObservacionAnulacion = $("#observacionAnulacionLote").val();
        var tamTextoObservacionAnulacionLote = textoObservacionAnulacion.length;
        if ("" === numeroLoteActivo) {
            alertify.alert("Error con el numero de lote.");
            return false;
        }
        if (10 > tamTextoObservacionAnulacionLote) {
            alertify.error("Ingrese al menos 10 caracteres en el campo de observación");
            return false;
        }
        var datosAnulacion = {
            observacion: textoObservacionAnulacion,
            numeroLote: numeroLoteActivo
        };
        desactivarLoteReplica(datosAnulacion);
    });
    $("#divTableDistribuidor").on("click", ".btninfoTrama", function () {
        var $this = $(this);
        var mensajeError = $this.attr("data-mensajeerror");
        alertify.alert(mensajeError);
    });

    $("#divTableDistribuidor").on("click", ".btnAnularLote", function () {
        var $this = $(this);
        var strLote = $this.attr("data-idlote");
        numeroLoteActivo = strLote;
        $("#tituloModalNumeroLote").html(strLote);
        var $modalAnulacion = $("#modalAnulacionLoteDistribuidor");
        $modalAnulacion.modal("show");

    });
    $("#divTableDistribuidor").on("click", ".btnProbarConexiones", function () {
        fn_cargando(1);
        var $this = $(this);
        var strLote = $this.attr("data-idlote");
        send = {
            "probarConectividad": 1,
            "lote": strLote
        };

        $.ajax({
            type: "POST",
            dataType: "json",
            accept: "application/json",
            url: "../adminReplicacion/config_nuevareplica.php",
            data: send,
            success: function (datos) {
                var htmlModal = crearHtmlModalConexionesLote(datos);
                $("#modalEstadoReplicacion .modal-body").html(htmlModal);
                $("#modalEstadoReplicacion").modal("show");
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alertify.error("No se pudo probar");
            },
            complete: function () {
                fn_cargando(0);
            }
        });
    });

    $("#divTableDistribuidor").on("click", ".btnReplicarTramaDistribuidor", function () {
        fn_cargando(1);
        var $this = $(this);
        $this.prop('disabled', true);
        var $tdPadre = $this.closest("td");
        var idTrama = $this.attr("data-idTrama");
        send = {
            "replicarTrama": 1,
            "idTrama": idTrama
        };
        $.ajax({
            type: "POST",
            accepts: "application/json",
            contentType: "application/x-www-form-urlencoded;",
            url: "../adminReplicacion/config_nuevareplica.php",
            data: send,
            success: function (datos) {
                var resultadoReplicaTrama = datos[0];
                if (0 == datos["estado"]) {
                    alertify.error(datos["errores"][0]);
                    return false;
                }
                  if ("ERROR" == resultadoReplicaTrama.ESTADO) {
                    alertify.error(resultadoReplicaTrama.ERRORMESSAGE);
                    return false;
                }
                $tdPadre.html("<span class='label label-success' style='padding:8px;font-size:13px'>OK</span>");
                return true;
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.responseText);
            },
            complete: function () {
                $this.prop('disabled', false);
                fn_cargando(0);
            }
        });
    });

    $("#opcionesAzureReplicacion").on("click", ".btnSeleccionarEstado", function (event) {
        var $this = $(this);
        seleccionarEstado($this.attr("data-ambiente"), $this.attr("data-estado"));
        event.preventDefault();
        event.stopPropagation();
    });

    $("#opcionesDistribuidorReplicacion").on("click", ".btnSeleccionarEstado", function (event) {
        var $this = $(this);
        seleccionarEstado($this.attr("data-ambiente"), $this.attr("data-estado"));
        event.preventDefault();
        event.stopPropagation();
    });

    $("#opcionesTiendaReplicacion").on("click", ".btnSeleccionarEstado", function (event) {
        var $this = $(this);
        seleccionarEstado($this.attr("data-ambiente"), $this.attr("data-estado"));
        event.preventDefault();
        event.stopPropagation();
    });


});

var crearContenidoModalEstadoReplica = function (datosPeticion) {
    var htmlGenerado = '';
    htmlGenerado += "<div class='row'><div class='col-sm-12'> Error al transmitir el lote:</div></div>";
    if (Array.isArray(datosPeticion.errores)) {
        datosPeticion.errores.forEach(function (element) {
            htmlGenerado += "<div class='bg-warning' style='padding:1em;' ><ul><li>Restaurante:" + element.rst_id + "</li><li>Mensaje: " + element.mensaje + "</li></ul></div>";
        });
    }
    return htmlGenerado;
};

var crearHtmlModalConexionesLote = function (datosPeticion) {
    var htmlGenerado = '';
    htmlGenerado += "<h3>Total tramas del lote: " + datosPeticion.numerotramas + "</h3>";
    htmlGenerado += "<h3>Estado de las conexiones: </h3>";

    if (Array.isArray(datosPeticion.error)) {
        htmlGenerado += "<div class='bg-warning' style='padding:0.5em 0.5em 0.5em 0.5em;' ><h3>Error:</h3>";
        datosPeticion.error.forEach(function (element) {
            htmlGenerado += "<div><ul><li>" + element.restaurante.Descripcion + "</li><li>Mensaje: " + element.mensaje + "</li></ul></div>";
        });
        htmlGenerado += "</div>";
    }
    if (Array.isArray(datosPeticion.desactivados)) {
        htmlGenerado += "<div class='bg-info' style='padding:0.5em;' ><h3>Desactivados:</h3><ul>";
        datosPeticion.desactivados.forEach(function (element) {
            htmlGenerado += "<li>" + element.Descripcion + "</li>";
        });
        htmlGenerado += "</ul></div>";
    }
    if (Array.isArray(datosPeticion.invalidos)) {
        htmlGenerado += "<div class='bg-info' style='padding:0.5em;' ><h3>Sin datos de conexión:</h3><ul>";
        datosPeticion.invalidos.forEach(function (element) {
            htmlGenerado += "<li>" + element.Descripcion + "</li>";
        });
        htmlGenerado += "</ul></div>";
    }
    if (Array.isArray(datosPeticion.ok)) {
        htmlGenerado += "<div class='bg-success' style='padding:0.5em;' ><h3>Correctos:</h3><ul>";
        datosPeticion.ok.forEach(function (element) {
            htmlGenerado += "<li>" + element.Descripcion + "</li>";
        });
        htmlGenerado += "</ul></div>";
    }
    return htmlGenerado;
};

var cargarAmbiente = function () {
    fn_cargando(1);
    send = {"cargarAmbiente": 1};
    $.ajax({
        async: false,
        type: "POST",
        dataType: "text",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminReplicacion/config_replicacion.php",
        data: send,
        success: function (datos) {
            cargarTabsAmbiente(quitarZWNBS(datos)["tipoambiente"]);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            alert(ajaxOptions);
            alert(thrownError);
        },
        complete: function () {
            fn_cargando(0);
        }
    });

};

var cargarInputsFechas = function () {
    var limiteDias = 7;

    $('#inpFechasAzure').daterangepicker({
        'startDate': moment().subtract(limiteDias - 1, 'days'),
        'endDate': moment(),
        'drops': 'down',
        'opens': 'center',
        'autoApply': true,
        'locale': {
            'firstDay': 1,
            'format': 'DD/MM/YYYY'
        },
        'dateLimit': {
            'days': limiteDias - 1
        }
    }, function (start, end) {

    });
    $('#inpFechasAzure').on('apply.daterangepicker', function (ev, picker) {
        cargarLotesReplicaAzure();
    });

    $('#inpFechasDistribuidor').daterangepicker({
        'startDate': moment().subtract(limiteDias - 1, 'days'),
        'endDate': moment(),
        'drops': 'down',
        'opens': 'center',
        'autoApply': true,
        'locale': {
            'firstDay': 1,
            'format': 'DD/MM/YYYY'
        },
        'dateLimit': {
            'days': limiteDias - 1
        }
    }, function (start, end) {

    });
    $('#inpFechasDistribuidor').on('apply.daterangepicker', function (ev, picker) {
        cargarLotesReplicaDistribuidor();
    });

    $('#inpFechasTienda').daterangepicker({
        'startDate': moment().subtract(limiteDias - 1, 'days'),
        'endDate': moment(),
        'drops': 'down',
        'opens': 'center',
        'autoApply': true,
        'locale': {
            'firstDay': 1,
            'format': 'DD/MM/YYYY'
        },
        'dateLimit': {
            'days': limiteDias - 1
        }
    }, function (start, end) {

    });
    $('#inpFechasTienda').on('apply.daterangepicker', function (ev, picker) {
        cargarLotesReplicaTienda();
    });

    $('.daterangepicker_input').each(function () {
        $(this).hide();
    });
};

var cargarTabsAmbiente = function (ambiente) {
    if (ambiente === "0") {
        alertify.error("No se pudo recuperar la configuración del tipo de ambiente");
    } else {
        cargarModulos(ambiente);
        cargarInputsFechas();
        cargarEstados(ambiente);
    }

    if (ambiente === "tienda") {
        $('#liTiendas').addClass('active');
        $('#liDistribuidor').hide();
        $('#liAzure').hide();
        $('#tabTienda').addClass('active in');
        cargarLotesReplicaTienda();
    } else if (ambiente === "azure") {
        $('#liAzure').addClass('active');
        $('#liTiendas').hide();
        $('#tabAzure').addClass('active in');
        cargarLotesReplicaAzure();
    }
};

var cargarModulos = function (ambiente) {
    var html = "";
    var send = {
        "cargarModulos": 1,
        ambiente: ambiente
    };
    $.ajax({
        async: false,
        type: "POST",
        dataType: "text",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminReplicacion/config_replicacion.php",
        data: send,
        success: function (datos) {
            var json = quitarZWNBS(datos);
            if (json.str > 0) {
                for (var i = 0; i < json.str; i++) {
                    html += "<option value='" + json[i]['idModulo'] + "'>" + json[i]['descripcion'] + "</option>";
                }
                $('#slcAzure').html(html);
                $('#slcAzure').change(function () {
                    cargarLotesReplicaAzure();
                });
                $('#slcDistribuidor').html(html);
                $('#slcDistribuidor').change(function () {
                    cargarLotesReplicaDistribuidor();
                });
                $('#slcTiendas').html(html);
                $('#slcTiendas').change(function () {
                    cargarLotesReplicaTienda();
                });
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            alert(ajaxOptions);
            alert(thrownError);
        }
    });
};

var cargarEstados = function (ambiente) {
    var send = {};
    var estadosNoValidos = []; //Estados que no aparecerán en el ambiente
    var estadosActivosPorDefecto = []; //Estados que estarán activos por defecto en el ambiente
    send = {"cargarEstados": 1};
    send.ambiente = ambiente;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "text",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminReplicacion/config_replicacion.php",
        data: send,
        success: function (datos) {
            var json = quitarZWNBS(datos);
            //Azure
            ambiente = 'Azure';
            estadosNoValidos = ['Aplicado', 'Reaplicado', 'Parcial'];
            estadosActivosPorDefecto = ['Pendiente', 'Error'];
            $('#opciones' + ambiente + 'Replicacion').html(cargarHtmlEstados(json, ambiente, estadosNoValidos, estadosActivosPorDefecto));

            //Distribuidor
            ambiente = 'Distribuidor';
            estadosNoValidos = [];
            estadosActivosPorDefecto = ['Pendiente', 'Aplicado', 'Error', 'Reaplicado', 'Parcial', 'Procesando'];
            $('#opciones' + ambiente + 'Replicacion').html(cargarHtmlEstados(json, ambiente, estadosNoValidos, estadosActivosPorDefecto));

            //Tienda
            ambiente = 'Tienda';
            estadosNoValidos = ['Transmitido'];
            estadosActivosPorDefecto = ['Pendiente', 'Error', 'Parcial'];
            $('#opciones' + ambiente + 'Replicacion').html(cargarHtmlEstados(json, ambiente, estadosNoValidos, estadosActivosPorDefecto));
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            alert(ajaxOptions);
            alert(thrownError);
        }
    });
};

var cargarHtmlEstados = function (json, ambiente, estadosNoValidos, estadosActivosPorDefecto) {
    var html = "";
    var checked;
    for (var i = 0; i < json.str; i++) {
        checked = "";
        if (jQuery.inArray(json[i]['estado'], estadosNoValidos) === -1) {

            html += "<label id='lblRep" + ambiente + "Opcion" + json[i]['idEstado'] + "' class='btn btn-default btn-sm btnSeleccionarEstado";
            if (jQuery.inArray(json[i]['estado'], estadosActivosPorDefecto) !== -1) {
                html += " active";
                checked = " checked";
            }
            // html += "' onclick='seleccionarEstado(\"" + ambiente + "\", \"" + json[i]['idEstado'] + "\")'>" + json[i]['estado'] + "<input id='inpRep" + ambiente + "Opcion" + json[i]['idEstado'] + "' type='checkbox' name='" + ambiente + "' value='" + json[i]['idEstado'] + "' data-toggle='button'" + checked + " /></label>";
            html += "' data-ambiente='" + ambiente + "' data-estado='" + json[i]['idEstado'] + "' >" + json[i]['estado'] + "<input id='inpRep" + ambiente + "Opcion" + json[i]['idEstado'] + "' type='checkbox' name='" + ambiente + "' value='" + json[i]['idEstado'] + "' data-toggle='button'" + checked + " /></label>";

        }
    }
    return html;
};

var alternarEstadoBotonesEstados = function (ambiente, nuevoEstado) {
    var $inputs = $("input[name='" + ambiente + "']");
    var $labels = $("#opciones" + ambiente + "Replicacion label");
    $inputs.prop("checked", nuevoEstado);
    if (nuevoEstado) {
        $labels.addClass("active");
    } else {
        $labels.removeClass("active");
    }
    return true;
};
//La clase 'active' es visual; para ver los estados seleccionados se revisa los checkbox
var seleccionarEstado = function (ambiente, idEstado) {

    var estadoTodos = '0';
    var $elemento = $('#lblRep' + ambiente + 'Opcion' + idEstado);
    var $inputActual = $elemento.find("input").first();
    var nuevoEstado = !($inputActual.prop("checked"));

    if (idEstado == 0) {
        alternarEstadoBotonesEstados(ambiente, nuevoEstado);
    } else {
        $inputActual.prop("checked", nuevoEstado);
        if (true == nuevoEstado)
            $elemento.addClass("active");
        else
            $elemento.removeClass("active");
    }

    if (ambiente === 'Azure') {
        cargarLotesReplicaAzure();
    } else if (ambiente === 'Distribuidor') {
        cargarLotesReplicaDistribuidor();
    } else if (ambiente === 'Tienda') {
        cargarLotesReplicaTienda();
    }
    return true;
};

var recargarAzure = function () {
    if (!$('#tabAzure').parent().hasClass('active')) {
        $('#slcAzure').val('0');
        cargarLotesReplicaAzure();
    }
};

var cargarLotesReplicaAzure = function () {

    var send = {};
    var html = '';
    var encabezadoHtml = "<table id='tblAzure' class='table table-bordered table-striped table-hover dt-responsive non-responsive dataTable no-footer dtr-inline'><thead><tr class='active'><th width='20%' style='text-align: center; font-size: 1.5em'><b>Fecha/Hora</b></th><th width='15%' style='text-align: center; font-size: 1.5em'><b>Lote</b></th><th width='30%' style='text-align: center; font-size: 1.5em'><b>Módulo</b></th><th width='20%' style='text-align: center; font-size: 1.5em'><b>Estado</b></th><th width='15%' style='text-align: center; font-size: 1.5em'><b>Acción</b></th></tr></thead><tbody>";
    var fechaDesde = $('#inpFechasAzure').data('daterangepicker').startDate.format('DD/MM/YYYY');
    var fechaHasta = $('#inpFechasAzure').data('daterangepicker').endDate.format('DD/MM/YYYY');
    var idModulo = $('#slcAzure :selected').val();
    var idEstados = [];
    $('#opcionesAzureReplicacion').find('input').each(function () {
        if (($(this).prop('checked') == true) && $(this).val() !== '0') { //Por alguna razón aunque el checkbox de 'Todos' no está marcado, al correr la función aparece como que sí está marcado (tal vez por el data-toggle="buttons"); por eso la necesidad de la segunda condición
            //console.log("Algo "+$(this).val());
            idEstados.push($(this).val());
        }
    });
    var cantidadEstados = idEstados.length;
    if (cantidadEstados > 0) {
        fn_cargando(1);
        send = {
            cargarLotesReplicaAzure: 1,
            ambiente: "azure",
            fechaDesde: fechaDesde,
            fechaHasta: fechaHasta,
            idModulo: idModulo,
            idEstados: idEstados,
            cantidadEstados: cantidadEstados
        };

        $.ajax({
            type: "POST",
            dataType: "text",
            accepts: "application/json",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../adminReplicacion/config_replicacion.php",
            data: send,
            success: function (datos) {
                var json = quitarZWNBS(datos);
                var lotesReplicaPendientes = '';
                if (json.str > 0) {
                    for (var i = 0; i < json.str; i++) {
                        if (json[i]['idLote'] === null) {
                            var numlote = (json[i]['numeroLote'] ? json[i]['numeroLote'] : "N/A");
                            lotesReplicaPendientes += "<tr data-tt-id='LRP" + json[i]['idModuloLote'] + "' data-tt-branch='true' data-cargado='false'><td style='font-size: 1.2em; vertical-align: middle'></td><td style='font-size: 1.2em; vertical-align: middle'>" + numlote + "</td><td style='font-size: 1.2em; vertical-align: middle'>" + json[i]['moduloLote'] + "</td><td style='font-size: 1.2em; vertical-align: middle'>" + json[i]['estadoLote'] + "</td><td style='font-size: 1.2em; text-align: center'><button class='btn btn-primary btn-sm' onclick='replicacionAzure(" + json[i]['idModuloLote'] + ")'>Transmitir</button></td></tr>";
                        } else {
                            html += "<tr data-tt-id='LR" + json[i]['idLote'] + "' data-tt-branch='true' data-cargado='false'><td style='font-size: 1.2em'>" + json[i]['fechaLote'] + " - " + json[i]['horaLote'] + "</td><td style='font-size: 1.2em'>" + json[i]['numeroLote'] + "</td><td style='font-size: 1.2em'>" + json[i]['moduloLote'] + "</td><td style='font-size: 1.2em'>" + json[i]['estadoLote'] + "</td><td style='font-size: 1.2em'></td></tr>";
                        }
                    }
                    html = lotesReplicaPendientes + html;
                } else {
                    html += "<tr><td colspan='5' class='text-center'>No se encontraron registros</td></tr>";
                }
                html = encabezadoHtml + html + "</tbody></table>";
                $('#divTableAzure').html(html);
                $('#tblAzure').treetable({
                    expandable: true,
                    onNodeExpand: function () {
                        cargarUpdateStoreAzure(this);
                    }
                });
            },
            complete: function () {
                fn_cargando(0);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
                alert(ajaxOptions);
                alert(thrownError);
            }
        });
    } else {
        alertify.error("No se ha seleccionado ningún estado");
        html += "<tr><td colspan='5' class='text-center'>No se encontraron registros</td></tr></tbody></table>";
        $('#divTableAzure').html(html);
        $('#tblAzure').treetable({expandable: true});
    }
};

var cargarUpdateStoreAzure = function (nodo) {
    fn_cargando(1);
    if (!$(nodo.row).data('cargado')) {
        var idLote = 0;
        var idModulo = 0;
        var loteReplicaPendiente = 'LRP';
        var loteReplica = 'LR';
        var id = $(nodo.row).data('ttId');
        if (id.substr(0, loteReplicaPendiente.length) === loteReplicaPendiente) {
            idModulo = parseInt(id.substr(loteReplicaPendiente.length));
        } else if (id.substr(0, loteReplica.length) === loteReplica) {
            idLote = parseInt(id.substr(loteReplica.length));
        }
        send = {"cargarUpdateStoreAzure": 1};
        send.ambiente = 'azure';
        send.idLote = idLote;
        send.idModulo = idModulo;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "text",
            "Accept": "application/json, text/javascript2",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../adminReplicacion/config_replicacion.php",
            data: send,
            success: function (datos) {
                var html = '';
                var json = quitarZWNBS(datos);
                if (json.str > 0) {
                    if (idModulo !== 0) {
                        html += "<tr data-tt-id='LRPS" + idModulo + "' data-tt-parent-id='" + id + "'><td style='font-weight: bold; font-size: 1.2em'>Fecha/Hora</td><td style='font-weight: bold; font-size: 1.2em'>Tabla</td><td style='font-weight: bold; font-size: 1.2em'>Trama</td><td style='font-weight: bold; font-size: 1.2em'>Restaurante</td><td style='font-weight: bold; font-size: 1.2em'></td></tr>";
                        for (var i = 0; i < json.str; i++) {
                            html += "<tr data-tt-id='US" + json[i]['idUpdateStore'] + "' data-tt-parent-id='" + id + "'><td style='vertical-align: middle'>" + json[i]['fechaUpdateStore'] + " - " + json[i]['horaUpdateStore'] + "</td><td style='vertical-align: middle'>" + json[i]['tablaUpdateStore'] + "</td><td><div style='width: 300px; overflow: auto; white-space: nowrap'>" + json[i]['tramaUpdateStore'] + "</div></td><td style='vertical-align: middle'>" + json[i]['restauranteUpdateStore'] + "</td><td style='text-align: center; vertical-align: middle'></td></tr>";
                        }
                    } else if (idLote !== 0) {
                        html += "<tr data-tt-id='LRS" + idLote + "'data-tt-parent-id='" + id + "'><td style='font-weight: bold; font-size: 1.2em'>Fecha/Hora</td><td style='font-weight: bold; font-size: 1.2em'>Tabla</td><td style='font-weight: bold; font-size: 1.2em'>Trama</td><td style='font-weight: bold; font-size: 1.2em'>Restaurante</td><td style='font-weight: bold; font-size: 1.2em'></td></tr>";
                        for (var i = 0; i < json.str; i++) {
                            html += "<tr data-tt-id='US" + json[i]['idUpdateStore'] + "' data-tt-parent-id='" + id + "'><td style='vertical-align: middle'>" + json[i]['fechaUpdateStore'] + " - " + json[i]['horaUpdateStore'] + "</td><td style='vertical-align: middle'>" + json[i]['tablaUpdateStore'] + "</td><td><div style='width: 300px; overflow: auto; white-space: nowrap'>" + json[i]['tramaUpdateStore'] + "</div></td><td style='vertical-align: middle'>" + json[i]['restauranteUpdateStore'] + "</td><td style='text-align: center; vertical-align: middle'></td></tr>";
                        }
                    }
                } else {
                    html += "<tr data-tt-id='USX" + idLote + "'data-tt-parent-id='" + id + "'><td colspan='5' class='text-center'>No se encontraron registros</td></tr>";
                }
                $('#tblAzure').treetable('loadBranch', nodo, html);
                $(nodo.row).data('cargado', true);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
                alert(ajaxOptions);
                alert(thrownError);
            }
        });
    }
    fn_cargando(0);
};

var replicacionAzure = function (idModulo) {
    fn_cargando(1);
    var send = {
        replicacionAzure: 1,
        ambiente: 'azure',
        idModulo: idModulo
    };
    //$.ajax({async: false, type: "POST", dataType: "text", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminReplicacion/config_replicacion.php", data: send,
    $.ajax({
        type: "POST",
        accepts: "application/json; charset=utf-8",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminReplicacion/config_nuevareplica.php",
        data: send,
        success: function (datos) {
            // var json = quitarZWNBS(datos);
            if (datos.estado == 1) {
                alertify.success("Transmisión realizada exitosamente");
            } else {
                alertify.alert("Error al transmitir: " + datos["errores"][0]);
            }
        },
        complete: function () {
            fn_cargando(0);
            recargarAzure();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            //alert(ajaxOptions);
            //alert(thrownError);
        }
    });
};

var recargarDistribuidor = function () {
    if (!$('#tabDistribuidor').parent().hasClass('active')) {
        $('#slcDistribuidor').val('0');
        cargarLotesReplicaDistribuidor();
    }
};

var desactivarLoteReplica = function (datos) {
    fn_cargando(1);
    $.post("../adminReplicacion/config_nuevareplica.php", {
        anularLoteDistribuidor: 1,
        lote: datos.numeroLote,
        observacion: datos.observacion
    }, 'json').success(function (datos) {
        if (datos.estado == 1) {
            alertify.success("Se anuló correctamente el lote");
        } else {
            var numeroErrores = datos.errores.length;
            var textoError = "";
            if (numeroErrores > 0) {
                datos.errores.forEach(function (item, index) {
                    textoError += item + "<br/>";
                });
                alertify.error("No se anuló: " + textoError);
            } else {
                alertify.error("No se anuló el lote");
            }
        }
    }).error(function () {
        alertify.error("No se pudo anular el lote. ");
    })
        .complete(function () {
            $("#modalAnulacionLoteDistribuidor").modal("hide");
            fn_cargando(0);
            cargarLotesReplicaDistribuidor();
        });
};

var cargarLotesReplicaDistribuidor = function () {
    var send = {};
    var html = "<table id='tblDistribuidor' class='table table-bordered table-striped table-hover dt-responsive non-responsive dataTable no-footer dtr-inline'><thead><tr class='active'><th width='20%' style='text-align: center; font-size: 1.5em'><b>Fecha/Hora</b></th><th width='15%' style='text-align: center; font-size: 1.5em'><b>Lote</b></th><th width='30%' style='text-align: center; font-size: 1.5em'><b>Módulo</b></th><th width='20%' style='text-align: center; font-size: 1.5em'><b>Estado</b></th><th width='15%' style='text-align: center; font-size: 1.5em'><b>Acción</b></th></tr></thead><tbody>";
    var fechaDesde = $('#inpFechasDistribuidor').data('daterangepicker').startDate.format('DD/MM/YYYY');
    var fechaHasta = $('#inpFechasDistribuidor').data('daterangepicker').endDate.format('DD/MM/YYYY');
    var idModulo = $('#slcDistribuidor :selected').val();
    var idEstados = [];

    $('#opcionesDistribuidorReplicacion').find('input').each(function () {
        if ($(this).is(':checked') && $(this).val() !== '0') { //Por alguna razón aunque el checkbox de 'Todos' no está marcado, al correr la función aparece como que sí está marcado (tal vez por el data-toggle="buttons"); por eso la necesidad de la segunda condición
            idEstados.push($(this).val());
        }
    });
    var cantidadEstados = idEstados.length;
    if (cantidadEstados > 0) {
        fn_cargando(1);
        send = {
            cargarLotesReplicaDistribuidor: 1,
            ambiente: "onpremise",
            fechaDesde: fechaDesde,
            fechaHasta: fechaHasta,
            idModulo: idModulo,
            idEstados: idEstados,
            cantidadEstados: cantidadEstados
        };

        $.ajax({
            async: true,
            type: "POST",
            dataType: "text",
            "Accept": "application/json",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../adminReplicacion/config_replicacion.php",
            data: send,
            success: function (datos) {
                var json = quitarZWNBS(datos);
                var estadosAplicarTransmitir = ['Pendiente'];
                var estadosTransmitir = ['Aplicado', 'Reaplicado', 'Parcial'];
                var ultimosLotesPendientes = {};
                if (json.str > 0) {
                    for (var i = 0; i < json.str; i++) {
                        if (jQuery.inArray(json[i]['estadoLote'], estadosAplicarTransmitir) !== -1 || jQuery.inArray(json[i]['estadoLote'], estadosTransmitir) !== -1) {
                            ultimosLotesPendientes[json[i]['moduloLote']] = 'LR' + json[i]['idLote'];
                        }
                        html += "<tr data-tt-id='LR" + json[i]['idLote'] + "' data-tt-branch='true' data-cargado='false'><td style='font-size: 1.2em; vertical-align: middle'>" + json[i]['fechaHoraLote'] + "</td><td style='font-size: 1.2em; vertical-align: middle'>" + json[i]['numeroLote'] + "</td><td style='font-size: 1.2em; vertical-align: middle'>" + json[i]['moduloLote'] + "</td><td style='font-size: 1.2em; vertical-align: middle'>" + json[i]['estadoLote'] + "</td><td style='font-size: 1.2em; text-align: center'></td></tr>";
                    }
                    html += "</tbody></table>";
                } else {
                    html += "<tr><td colspan='5' class='text-center'>No se encontraron registros</td></tr></tbody></table>";
                }
                $('#divTableDistribuidor').html(html);

                console.log(ultimosLotesPendientes);
                //Solo los lotes más antiguos de cada módulo pueden replicarse
                Object.keys(ultimosLotesPendientes).forEach(function (modulo) {
                    console.log(ultimosLotesPendientes);
                    var boton = '';
                    var idLoteReplicacionActual = ultimosLotesPendientes[modulo];
                    var $tr = $('#tblDistribuidor').find("[data-tt-id='" + ultimosLotesPendientes[modulo] + "']");
                    var estado = $(':nth-child(4)', $tr).html();
                    var numeroLote = $(':nth-child(2)', $tr).html();
                    if (jQuery.inArray(estado, estadosAplicarTransmitir) !== -1) {
                        boton = "<span><button class='btn btn-primary btn-sm' onclick='aplicarReplicacionDistribuidor(\"" + numeroLote + "\")'>Aplicar</button></span>";
                        boton = $(boton);
                    } else if ("Parcial" === estado) {
                        boton = "<button class='btn btn-primary btn-sm' onclick='transmitirReplicacionDistribuidor(\"" + idLoteReplicacionActual + "\")'>Transmitir</button>";
                        boton += "<button class='btn btn-danger btn-sm btnAnularLote' data-idlote='" + idLoteReplicacionActual + "'>Inactivar</button>";
                        boton += "<div class='btn btn-sm btn-info btnProbarConexiones' style='margin:3px' data-idlote='" + idLoteReplicacionActual + "'><span class='glyphicon glyphicon glyphicon-check' style='padding:0;'></span></div>";

                    }
                    else {
                        boton = "<button class='btn btn-primary btn-sm' onclick='transmitirReplicacionDistribuidor(\"" + idLoteReplicacionActual + "\")'>Transmitir</button>";
                        boton += "<div class='btn btn-sm btn-info btnProbarConexiones' style='margin:3px' data-idlote='" + idLoteReplicacionActual + "'><span class='glyphicon glyphicon glyphicon-check' style='padding:0;'></span></div>";
                        boton += "<button class='btn btn-danger btn-sm btnAnularLote' data-idlote='" + idLoteReplicacionActual + "'>Inactivar</button>";
                    }
                    $(':nth-child(5)', $tr).html(boton);
                });

                $('#tblDistribuidor').treetable({
                    expandable: true,
                    onNodeExpand: function () {
                        var loteReplica = 'LR';
                        var updateStore = 'US';
                        var loteReplicaT = 'LT';

                        var opcion = $(this.row).data('ttId').substring(0, 2);
                        if (opcion === loteReplica) {
                            cargarTUpdateStoreDistribuidor(this);
                        } else if (opcion === updateStore) {
                            cargarUpdateStoreTiendasDistribuidor(this);
                        } else if (opcion === loteReplicaT) {
                            cargarUpdateStoreDistribuidor(this);
                        }
                    }
                });
            },
            complete: function () {
                fn_cargando(0);


            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
                alert(ajaxOptions);
                alert(thrownError);
            }
        });
        verificarLotesPendientesDistribuidor(fechaDesde, fechaHasta);
    } else {
        alertify.error("No se ha seleccionado ningún estado");
        html += "<tr><td colspan='5' class='text-center'>No se encontraron registros</td></tr></tbody></table>";
        $('#divTableDistribuidor').html(html);
        $('#tblDistribuidor').treetable({expandable: true});
    }

};

var verificarLotesPendientesDistribuidor = function (fechaDesde, fechaHasta) {
    var send = {};
    send = {'verificarLotesPendientesDistribuidor': 1};
    send.ambiente = 'onpremise';
    send.fechaDesde = fechaDesde;
    send.fechaHasta = fechaHasta;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "text",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminReplicacion/config_replicacion.php",
        data: send,
        success: function (datos) {
            var json = quitarZWNBS(datos);
            if (json.str > 0) {
                alertify.error("<b>Advertencia:</b> Existen tramas pendientes fuera del rango seleccionado (en la fecha " + json[0]['fechaLote'] + ")");
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            alert(ajaxOptions);
            alert(thrownError);
        }
    });
};


var cargarTUpdateStoreDistribuidor = function (nodoT) {
    if (!$(nodoT.row).data('cargado')) {
        fn_cargando(1);
        var idLote;
        var loteReplica = 'LR';
        var id = $(nodoT.row).data('ttId');
        idLote = parseInt(id.substr(loteReplica.length));

        send = {
            cargarTUpdateStoreDistribuidor: 1,
            ambiente: 'onpremise',
            idLote: idLote
        };
        $.ajax({
            type: "POST",
            dataType: "text",
            accepts: "application/json",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../adminReplicacion/config_replicacion.php",
            data: send,
            success: function (datos) {
                var html = '';
                var json = quitarZWNBS(datos);
                var ttBranch = '';
                if (json.str > 0) {
                    for (var i = 0; i < json.str; i++) {
                        var respuestabt = RespuestaBotones(json[i]);
                        html += "<tr data-tt-branch='true' data-tt-id='LT" + json[i]['idRestaurante'] + "-" + id + "' data-tt-parent-id='" + id + "'>" +
                            "<td style='font-weight: bold; font-size: 1.13em; background-color:#DCDCDC'  >" + json[i]['restauranteUpdateStore'] + "</td>" +
                            "<td style='font-size: 1em; background-color: #E0E0E0' ></td>" +
                            "<td style=' font-weight: bold; font-size: 1.25em ; background-color:#E0E0E0'>TOTAL TRAMAS : " + json[i]['TotalTramasLocal'] + "</td>" +
                            "<td style='font-weight: bold; font-size: 1em; background-color: #E0E0E0' align='center'>ESTADO: " + respuestabt + " </td>" +
                            "<td  style='font-weight: bold; font-size: 1.25em ; background-color: #E0E0E0' align='center'></td></tr>";
                    }
                } else {
                    html += "<tr data-tt-branch='false' data-tt-id='LT" + idLote + "'data-tt-parent-id='" + id + "'><td colspan='5' class='text-center'>No se encontraron registros</td></tr>";
                }

                $('#tblDistribuidor').treetable('loadBranch', nodoT, html);

                $(nodoT.row).data('cargado', true);

            },
            complete: function () {
                fn_cargando(0);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
                alert(ajaxOptions);
                alert(thrownError);
            }
        });
    }
};


var cargarUpdateStoreDistribuidor = function (nodo) {
    if (!$(nodo.row).data('cargado')) {
        fn_cargando(1);
        var idLote;
        var loteReplica = 'LR';
        var localReplica = 'LT';
        var id = $(nodo.row).data('ttId');
        var idlotes = $(nodo.row).data('ttParentId');

        idLote = parseInt(idlotes.substr(loteReplica.length));
        idRestaurante = parseInt(id.substr(localReplica.length));
        console.log(idRestaurante);
        send = {
            cargarUpdateStoreDistribuidor: 1,
            ambiente: 'onpremise',
            idLote: idLote,
            idRestaurante: idRestaurante
        };

        $.ajax({
            type: "POST",
            dataType: "text",
            accepts: "application/json",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../adminReplicacion/config_replicacion.php",
            data: send,
            success: function (datos) {
                var html = '';
                var json = quitarZWNBS(datos);
                var ttBranch = '';
                if (json.str > 0) {
                    html += "<tr data-tt-id='LRS" + idLote + "'data-tt-parent-id='" + id + "'><td style='font-weight: bold; font-size: 1.2em'>Fecha/Hora</td><td style='font-weight: bold; font-size: 1.2em'>Tabla</td><td style='font-weight: bold; font-size: 1.2em'>Trama</td><td style='font-weight: bold; font-size: 1.2em'>Restaurante</td><td style='font-weight: bold; font-size: 1.2em'></td></tr>";
                    for (var i = 0; i < json.str; i++) {
                        ttBranch = '';
                        var botones = crearBotonesTrama(json[i]);
                        html += "<tr data-tt-id='US" + json[i]['idUpdateStore'] + "' data-tt-parent-id='" + id + "' " + ttBranch + "data-cargado='false'><td style='vertical-align: middle'>" + json[i]['fechaUpdateStore'] + " - " + json[i]['horaUpdateStore'] + "</td><td style='vertical-align: middle'>" + json[i]['tablaUpdateStore'] + "</td><td><div style='width: 300px; overflow: auto; white-space: nowrap'>" + json[i]['tramaUpdateStore'] + "</div></td><td style='vertical-align: middle'>" + json[i]['restauranteUpdateStore'] + "</td><td style='text-align: center; vertical-align: middle'>" + botones + "</td></tr>";
                    }
                } else {
                    html += "<tr data-tt-id='USX" + idLote + "'data-tt-parent-id='" + id + "'><td colspan='5' class='text-center'>No se encontraron registros</td></tr>";
                }
                $('#tblDistribuidor').treetable('loadBranch', nodo, html);
                $(nodo.row).data('cargado', true);
            },
            complete: function () {
                fn_cargando(0);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
                alert(ajaxOptions);
                alert(thrownError);
            }
        });
    }
};

var cargarUpdateStoreTiendasDistribuidor = function (nodo) {
    fn_cargando(1);
    if (!$(nodo.row).data('cargado')) {
        var idUpdateStore;
        var updateStore = 'US';
        var id = $(nodo.row).data('ttId');
        idUpdateStore = parseInt(id.substr(updateStore.length));
        send = {'cargarUpdateStoreTiendasDistribuidor': 1};
        send.ambiente = 'onpremise';
        send.idUpdateStore = idUpdateStore;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "text",
            "Accept": "application/json, text/javascript2",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../adminReplicacion/config_replicacion.php",
            data: send,
            success: function (datos) {
                var html = '';
                var json = quitarZWNBS(datos);
                if (json.str > 0) {
                    var estadoUpdateStoreTiendas = '';
                    var estadoTransmitido = "<td style='text-align: center; vertical-align: middle'>Transmitido</td>";
                    var estadoNoTransmitido = "<td style='text-align: center; vertical-align: middle; color: red'>No transmitido</td>";
                    html += "<tr data-tt-id='USS" + updateStore + "'data-tt-parent-id='" + id + "'><td style='font-weight: bold; font-size: 1.2em'>Fecha/Hora</td><td style='font-weight: bold; font-size: 1.2em'>Tabla</td><td style='font-weight: bold; font-size: 1.2em'>Trama</td><td style='font-weight: bold; font-size: 1.2em'>Restaurante</td><td style='font-weight: bold; font-size: 1.2em'>Estado</td></tr>";
                    for (var i = 0; i < json.str; i++) {
                        if (json[i]['estadoUpdateStoreTiendas'] === 0) {
                            estadoUpdateStoreTiendas = estadoNoTransmitido;
                        } else {
                            estadoUpdateStoreTiendas = estadoTransmitido;
                        }
                        html += "<tr data-tt-id='UST" + json[i]['idUpdateStoreTiendas'] + "' data-tt-parent-id='" + id + "' ><td style='vertical-align: middle'>" + json[i]['fechaUpdateStoreTiendas'] + " - " + json[i]['horaUpdateStoreTiendas'] + "</td><td style='vertical-align: middle'>" + json[i]['tablaUpdateStoreTiendas'] + "</td><td><div style='width: 300px; overflow: auto; white-space: nowrap'>" + json[i]['tramaUpdateStoreTiendas'] + "</div></td><td style='vertical-align: middle'>" + json[i]['restauranteUpdateStoreTiendas'] + "</td>" + estadoUpdateStoreTiendas + "</tr>";
                    }
                } else {
                    html += "<tr data-tt-id='USTX" + idUpdateStore + "'data-tt-parent-id='" + id + "'><td colspan='5' class='text-center'>No se encontraron registros</td></tr>";
                }
                $('#tblDistribuidor').treetable('loadBranch', nodo, html);
                $(nodo.row).data('cargado', true);
            },
            complete: function () {
                fn_cargando(0);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
                alert(ajaxOptions);
                alert(thrownError);
            }
        });
    }
};

var aplicarReplicacionDistribuidor = function (lote) {
    fn_cargando(1);
    var send = {
        aplicarReplicacionDistribuidor: 1,
        lote: lote
    };

    $.ajax({
        type: "POST",
        dataType: "json",
        accepts: "application/json; charset=utf-8",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminReplicacion/config_nuevareplica.php",
        data: send,
        success: function (datos) {
            if (datos.estado == 1) {
                if (datos.datos[0].Respuesta == 0) alertify.alert("Existe una trama con error en el lote, no se aplica.");
                else (alertify.success("Replicación aplicada exitosamente"));
            } else {
                alertify.error("No se pudo aplicar el lote");
            }
        },
        complete: function () {
            fn_cargando(0);
            recargarDistribuidor();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            alert(ajaxOptions);
            alert(thrownError);
        }
    });
};

var transmitirReplicacionDistribuidor = function (lote) {
    fn_cargando(1);
    var send = {
        transmitirReplicacionDistribuidor: 1,
        ambiente: 'onpremise',
        lote: lote
    };

    $.ajax({
        type: "POST",
        dataType: "json",
        accepts: "application/json; charset=utf-8",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminReplicacion/config_nuevareplica.php",
        data: send,
        success: function (datos) {
            if (datos.estado == 1) {
                alertify.success("Replicación transmitida exitosamente");
            } else {
                var htmlModal = crearContenidoModalEstadoReplica(datos);
                $("#modalEstadoReplicacion .modal-body").html(htmlModal);
                $("#modalEstadoReplicacion").modal("show");
            }
        },
        complete: function () {
            cargarLotesReplicaDistribuidor();
            fn_cargando(0);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
        }
    });
};

var recargarTiendas = function () {
    if (!$('#tabTienda').hasClass('active')) {
        $('#slcTiendas').val('0');
        cargarLotesReplicaTienda();
    }
};

var cargarLotesReplicaTienda = function () {
    fn_cargando(1);
    var send = {};
    var html = "<table id='tblTienda' class='table table-bordered table-striped table-hover dt-responsive non-responsive dataTable no-footer dtr-inline'><thead><tr class='active'><th width='20%' style='text-align: center; font-size: 1.5em'><b>Fecha/Hora</b></th><th width='15%' style='text-align: center; font-size: 1.5em'><b>Lote</b></th><th width='30%' style='text-align: center; font-size: 1.5em'><b>Módulo</b></th><th width='20%' style='text-align: center; font-size: 1.5em'><b>Estado</b></th><th width='15%' style='text-align: center; font-size: 1.5em'><b>Acción</b></th></tr></thead><tbody>";
    var fechaDesde = $('#inpFechasTienda').data('daterangepicker').startDate.format('DD/MM/YYYY');
    var fechaHasta = $('#inpFechasTienda').data('daterangepicker').endDate.format('DD/MM/YYYY');
    var idModulo = $('#slcTiendas :selected').val();
    var idEstados = [];
    $('#opcionesTiendaReplicacion').find('input').each(function () {
        if ($(this).is(':checked') && $(this).val() !== '0') { //Por alguna razón aunque el checkbox de 'Todos' no está marcado, al correr la función aparece como que sí está marcado (tal vez por el data-toggle="buttons"); por eso la necesidad de segunda condición
            idEstados.push($(this).val());
        }
    });
    var cantidadEstados = idEstados.length;
    if (cantidadEstados > 0) {
        send = {"cargarLotesReplicaTienda": 1};
        send.ambiente = 'tienda';
        send.fechaDesde = fechaDesde;
        send.fechaHasta = fechaHasta;
        send.idModulo = idModulo;
        send.idEstados = idEstados;
        send.cantidadEstados = cantidadEstados;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "text",
            "Accept": "application/json, text/javascript2",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../adminReplicacion/config_replicacion.php",
            data: send,
            success: function (datos) {
                var json = quitarZWNBS(datos);
                var estadoPendiente = 'Pendiente';
                var ultimosLotesPendientes = {};
                if (json.str > 0) {
                    for (var i = 0; i < json.str; i++) {
                        if (json[i]['estadoLote'] === estadoPendiente) {
                            ultimosLotesPendientes[json[i]['moduloLote']] = 'LR' + json[i]['idLote'];
                        }
                        html += "<tr data-tt-id='LR" + json[i]['idLote'] + "' data-tt-branch='true' data-cargado='false'><td style='font-size: 1.2em; vertical-align: middle'>" + json[i]['fechaHoraLote'] + "</td><td style='font-size: 1.2em; vertical-align: middle'>" + json[i]['numeroLote'] + "</td><td style='font-size: 1.2em; vertical-align: middle'>" + json[i]['moduloLote'] + "</td><td style='font-size: 1.2em; vertical-align: middle'>" + json[i]['estadoLote'] + "</td><td style='font-size: 1.2em; text-align: center'></td></tr>";
                    }
                } else {
                    html += "<tr><td colspan='5' class='text-center'>No se encontraron registros</td></tr>";
                }
                html += "</tbody></table>";
                $('#divTableTienda').html(html);

                //Solo los lotes más antiguos de cada módulo pueden replicarse
                Object.keys(ultimosLotesPendientes).forEach(function (modulo) {
                    var $tr = $('#tblTienda').find("[data-tt-id='" + ultimosLotesPendientes[modulo] + "']");
                    var numeroLote = $(':nth-child(2)', $tr).html();
                    $(':nth-child(5)', $tr).html("<button class='btn btn-primary btn-sm' onclick='replicacionTienda(\"" + numeroLote + "\")'>Aplicar</button>");
                });

                $('#tblTienda').treetable({
                    expandable: true,
                    onNodeExpand: function () {
                        cargarUpdateStoreTienda(this);
                    }
                });
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
                alert(ajaxOptions);
                alert(thrownError);
            }
        });
        verificarLotesPendientesTienda(fechaDesde, fechaHasta);
    } else {
        alertify.error("No se ha seleccionado ningún estado");
        html += "<tr><td colspan='5' class='text-center'>No se encontraron registros</td></tr></tbody></table>";
        $('#divTableTienda').html(html);
        $('#tblTienda').treetable({expandable: true});
    }
    fn_cargando(0);
};


var verificarLotesPendientesTienda = function (fechaDesde, fechaHasta) {
    var send = {};
    send = {'verificarLotesPendientesTienda': 1};
    send.ambiente = 'tienda';
    send.fechaDesde = fechaDesde;
    send.fechaHasta = fechaHasta;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "text",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminReplicacion/config_replicacion.php",
        data: send,
        success: function (datos) {
            var json = quitarZWNBS(datos);
            if (json.str > 0) {
                alertify.error("<b>Advertencia:</b> Existen tramas pendientes fuera del rango seleccionado (en la fecha " + json[0]['fechaLote'] + ")");
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            alert(ajaxOptions);
            alert(thrownError);
        }
    });
};

var cargarUpdateStoreTienda = function (nodo) {
    fn_cargando(1);
    if (!$(nodo.row).data('cargado')) {
        var idLote = 0;
        var loteReplica = 'LR';
        var id = $(nodo.row).data('ttId');
        idLote = parseInt(id.substr(loteReplica.length));
        send = {"cargarUpdateStoreTienda": 1};
        send.ambiente = 'tienda';
        send.idLote = idLote;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "text",
            "Accept": "application/json, text/javascript2",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../adminReplicacion/config_replicacion.php",
            data: send,
            success: function (datos) {
                var html = '';
                var json = quitarZWNBS(datos);
                if (json.str > 0) {
                    html += "<tr data-tt-id='LRS" + idLote + "'data-tt-parent-id='" + id + "'><td style='font-weight: bold; font-size: 1.2em'>Fecha/Hora</td><td style='font-weight: bold; font-size: 1.2em'>Tabla</td><td style='font-weight: bold; font-size: 1.2em'>Trama</td><td style='font-weight: bold; font-size: 1.2em'>Restaurante</td><td style='font-weight: bold; font-size: 1.2em'></td></tr>";
                    for (var i = 0; i < json.str; i++) {
                        html += "<tr data-tt-id='US" + json[i]['idUpdateStore'] + "' data-tt-parent-id='" + id + "'><td style='vertical-align: middle'>" + json[i]['fechaUpdateStore'] + " - " + json[i]['horaUpdateStore'] + "</td><td style='vertical-align: middle'>" + json[i]['tablaUpdateStore'] + "</td><td><div style='width: 300px; overflow: auto; white-space: nowrap'>" + json[i]['tramaUpdateStore'] + "</div></td><td style='vertical-align: middle'>" + json[i]['restauranteUpdateStore'] + "</td><td style='text-align: center; vertical-align: middle'></td></tr>";
                    }
                } else {
                    html += "<tr data-tt-id='USX" + idLote + "'data-tt-parent-id='" + id + "'><td colspan='5' class='text-center'>No se encontraron registros</td></tr>";
                }
                $('#tblTienda').treetable('loadBranch', nodo, html);
                $(nodo.row).data('cargado', true);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
                alert(ajaxOptions);
                alert(thrownError);
            }
        });
    }
    fn_cargando(0);
};

var replicacionTienda = function (lote) {
    fn_cargando(1);
    var send = {};
    send = {"replicacionTienda": 1};
    send.ambiente = 'tienda';
    send.lote = lote;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "text",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../adminReplicacion/config_replicacion.php",
        data: send,
        success: function (datos) {
            var json = quitarZWNBS(datos);
            if (json['resultado'] === 1) {
                alertify.success("Replicación aplicada exitosamente");
            } else {
                alertify.error("Error al aplicar: " + json['resultado']);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            alert(ajaxOptions);
            alert(thrownError);
        }
    });
    cargarLotesReplicaTienda();
    fn_cargando(0);
};

//Por una inescrutable razón (relacionada con la codificación de los caracteres), los datos de los llamados ajax retornan con un caracter invisible (es decir, infernal)
//llamado 'zero width no-break space' al inicio (lo cual sucede únicamente en el servidor de producción sazmaxpappec), el cual debe ser removido.
//Posteriormente, se convierte la data a JSON y se la retorna.
var quitarZWNBS = function (datos) {
    return JSON.parse(datos.replace('\uFEFF', ''));
};

/*-------------------------------------------------------
 Funcion para mostrar pantalla de espera (Cargando)
 -------------------------------------------------------*/

function fn_cargando(estado) {
    if (estado) {
        $("#mdl_rdn_pdd_crgnd").show();
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
    }
}

var crearBotonesTrama = function (trama) {
    var contenido = "<span class='label label-success' style='padding:8px;font-size:13px'>OK</span>";
    if ("ERROR" == $.trim(trama["estado"])) {
        contenido = "<div class='btn btn-sm btn-danger btnReplicarTramaDistribuidor' data-idTrama=" + trama["idUpdateStore"] + " style='margin:3px'><span class='glyphicon glyphicon-repeat' style='padding:0;'></span></div>";
        contenido += "<div class='btn btn-sm btn-info btninfoTrama' data-mensajeerror='" + trama["errormessage"] + "' style='margin:3px'><span class='glyphicon glyphicon-info-sign' style='padding:0;'></span></div>";
    }
    if (!trama["estado"]) {
        contenido = "<div class='btn btn-sm btn-warning' style='margin:3px'><span class='glyphicon glyphicon-time' style='padding:0;'></span></div>";
    }
    return contenido;
}
var RespuestaBotones = function (trama) {
    var contenido = "<span class='label label-success' style='padding:8px;font-size:13px'>OK</span>";
    if ("ERROR" == $.trim(trama["estado"])) {
        contenido = "<div class='btn btn-sm btn-danger' style='margin:3px'><span class='glyphicon glyphicon-remove' style='padding:0;'></span></div>";
    }
    if (!trama["estado"]) {
        contenido = "<div class='btn btn-sm btn-warning' style='margin:3px'><span class='glyphicon glyphicon-time' style='padding:0;'></span></div>";
    }
    return contenido;
}

