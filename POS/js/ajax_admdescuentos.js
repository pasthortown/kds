/* global moment */

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Francisco Sierra ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de descuentos ///////////////////
///////TABLAS INVOLUCRADAS: Descuentos ////////////////////////////////////////
///////FECHA CREACION: 04-05-2017 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////LOG DE CAMBIOS CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

var accion = 0;
//var acc_resultado = 0;
var acc_std = 0;
var reg_max = 0;
var reg_pres = 10;
var slcn_dsct_id = 0;
var slcn_apld_id = 0;
var slcn_plus_lmcn = "";
var plus_agregados_descuento = [];

$(document).ready(function () {
    fn_cargando(0);
    fn_cargarDescuentos();
    $("#FechaInicial").daterangepicker({
        singleDatePicker: true,
        format: "DD/MM/YYYY",
        drops: "up"
    },
    function (start, end, label) {
        var valorFecha = moment(start).format("DD/MM/YYYY");
        $.each($(".inicioperiodo"), function (key, item) {
            $(item).data("daterangepicker").setStartDate(valorFecha);
            $(item).data("daterangepicker").setEndDate(valorFecha);
        });
    });

    $("#FechaFinal").daterangepicker({
        singleDatePicker: true,
        format: "DD/MM/YYYY",
        drops: "up"
    },
    function (start, end, label) {
        var valorFecha = moment(start).format("DD/MM/YYYY");
        $.each($(".finperiodo"), function (key, item) {
            $(item).data("daterangepicker").setStartDate(valorFecha);
            $(item).data("daterangepicker").setEndDate(valorFecha);
        });
    });

    $("#buscar_Restaurantes").on("keyup", function (e) {
        filtrarLocales();
    });
    $("#icono_buscar_restaurantes").on("click", filtrarLocales);

    $("#select-plus-cadena").chosen({
        width: "100%",
        search_contains: true
    });
    $("#btn-agregar-plu-descuento").on("click", function () {
        var valorPLU = $("#select-plus-cadena").val();
        var $elementoPLU = $($("#select-plus-cadena option[value='" + valorPLU + "']")[0]);
        if (valorPLU == 0) {
            fn_alerta("<b>Alerta!</b> Por favor escoge un valor.", "danger");
            return false;
        }
        if (plus_agregados_descuento.hasOwnProperty(valorPLU)) {
            if (plus_agregados_descuento[valorPLU].agregado == 1) {
                fn_alerta("<b>Alerta!</b> El PLU ya se encuentra asignado.", "danger");
                return false;
            }
        }

        var $esDescuentoCupones=$("#dsct_cupones").is(":checked");
        if($esDescuentoCupones){
            console.log("Es Cupones");
            var valoresNuevoElemento = {
                agregado: 1,
                plu_id: $elementoPLU.data("plu_id"),
                plu_descripcion: $elementoPLU.data("plu_descripcion"),
                plu_num_plu: $elementoPLU.data("plu_num_plu")
            };
            var nuevoElemento = crearElementoListaPlusDescuento(valoresNuevoElemento);
            plus_agregados_descuento[valoresNuevoElemento.plu_id] = valoresNuevoElemento;
            $("#listado-plus-agregardos-descuento").append(nuevoElemento);
        }else{
            var send = {validarCondicionesPLU: 1};
            send.cdn_id = $("#sess_cdn_id").val();
            send.plu_id = valorPLU;
            $.post("../adminDescuentos/config_descuentos.php", send, function (datos) {
                if("ERROR"==datos){
                    fn_alerta("<b>Alerta!</b> No se puede agregar el PLU al descuento.", "danger");
                    return false;
                }
                if(!("1"==datos)){
                    fn_alerta("<b>Alerta!</b> "+datos, "danger");
                    return false;
                }

                var valoresNuevoElemento = {
                    agregado: 1,
                    plu_id: $elementoPLU.data("plu_id"),
                    plu_descripcion: $elementoPLU.data("plu_descripcion"),
                    plu_num_plu: $elementoPLU.data("plu_num_plu")
                };
                var nuevoElemento = crearElementoListaPlusDescuento(valoresNuevoElemento);
                plus_agregados_descuento[valoresNuevoElemento.plu_id] = valoresNuevoElemento;
                $("#listado-plus-agregardos-descuento").append(nuevoElemento);
                return true;
            },"json");

        }

    });
    $("#slct_tp_dsct").on("change", function () {
        $("#dsct_valor").val(0);
    });

    $("#dsct_valor").on("input", function (evt) {
        var textoSeleccionado = $("#slct_tp_dsct option:selected").text();
        var value = evt.target.value;
        var tam = value.length;
        var valorOriginal = value.substr(0, (tam - 1));
        var validacion = /^\d+(\.\d{0,3})?$/.test(value);
        if (!validacion) {
            $(this).val(valorOriginal);
            fn_alerta("<b>Alerta!</b> Por favor ingresa un valor decimal.", "danger");
            return false;
        }
        return true;
    });
    $(".bt-aplica-descuento").on("click", function (evt) {
        var nuevoValorRadio = $(this).find("input[type='radio']").val();
        seleccionarRadioGrupo("grp_apld", nuevoValorRadio);
        seleccionarRestricciones(nuevoValorRadio);
    });

    $("#dsct_aplica_cantidad").on("change", function (evt) {
        var activado = evt.target.checked;
        fn_cambiarEtiquetasInputCantidades(activado);
        if (activado) {
            fn_asignaValorCheckbox("dscto_aplica_minimo_maximo", 1);
        }
    });

    $("#modal").on("hide.bs.modal", function (evento) {
        $("#buscar_Restaurantes").val("");
        fn_cambiarEtiquetasInputCantidades(false);
    });

    $("#btnAgregarTodosRestaurantes").on("click", function () {
        fn_cargando_adm(1);
        $("#contenedor_lst_rest_no_activos").hide({
            complete: function () {
                $.when($("#lst_rst_id button").trigger("click")).done(function () {
                    fn_cargando_adm(0);
                    $("#contenedor_lst_rest_no_activos").show();
                });
            }
        });

    });

    $("#btnQuitarTodosRestaurantes").on("click", function () {
        fn_cargando_adm(1);
        $("#contenedor_lst_rest_activos").hide({
            complete: function () {
                $.when($("#lst_rst_dscto button").trigger("click")).done(function () {
                    $("#contenedor_lst_rest_activos").show();
                    fn_cargando_adm(0);
                });
            }
        });
    });

    $("#dsct_cupones").on("click",function(evt){
        var $check = $(evt.target);
        if($check.is(":checked")){
            ocultarBotonAplicaDiscrecional();
        }else{
            mostrarBotonAplicaDiscrecional();
        }

    });
});

function seleccionarRestricciones(valor) {
    /* La única opción aqui es quemar el tipo de descuento (1 o 2) 
     * para segun ese tipo seleccionar o quitar la seleccion 
     * de los checkbox de las restricciones */
    if (1 == valor) {
        //Descuento a la Factura
        fn_asignaValorCheckbox('dscto_automatico', 0);
        fn_asignaValorCheckbox('dscto_seguridad', 1);
        fn_asignaValorCheckbox('dsct_aplica_cantidad', 0);
        fn_bloquearCheckbox('dsct_aplica_cantidad', 1);
        //fn_bloquearCheckbox('dscto_seguridad', 1)
    } else {
        fn_bloquearCheckbox('dsct_aplica_cantidad', 0);
    }
    if (2 == valor) {
        //Descuento a productos
        fn_asignaValorCheckbox('dscto_automatico', 1);
        fn_asignaValorCheckbox('dscto_seguridad', 1);
        //fn_bloquearCheckbox('dscto_seguridad', 0)

    }
}

function seleccionarRadioGrupo(nombre, valor) {
    var $elem = $("input[name=" + nombre + "][value=" + valor + "]");
    $elem.prop('checked', 'checked');
    $(".bt-aplica-descuento").removeClass("btn-info");
    $(".bt-aplica-descuento").removeClass("active");
    var $botonCercano = $elem.closest(".bt-aplica-descuento");
    $botonCercano.addClass("btn-info");
    $botonCercano.addClass("active");
    return true;
}

function fn_agregarDescuento() {
    //slcn_plus_lmcn = '';
    seleccionarRadioGrupo("grp_apld", 1);
    seleccionarRestricciones(1);
    $('#dsct_dscrp').val("");
    $('#FechaFinal').val(moment().format('DD/MM/YYYY'));
    $('#FechaInicial').val(moment().format('DD/MM/YYYY'));
    //$('#slct_prf').val(0);
    $('#slct_tp_dsct').val(0);
    $('#dsct_mnt_max').val("");
    $('#dsct_valor').val("");
    $('#dsct_mnt_min').val("");
    $('#dsct_cnt_min').val("");
    $("#dsct_std_id").prop("checked", "checked");
    $("#lst_rst_dscto").empty();
    $("#listado-plus-agregardos-descuento").empty();
    var cdn_id = $("#sess_cdn_id").val();
    accion = 1;
    slcn_apld_id = 1;
    fn_procesoPlusDescuento(slcn_dsct_id, cdn_id);
    $('#modal').modal('show');
    fn_cargarLocalesNoAsignadosDescuento(0, cdn_id);
    $('#titulomodal').html("Agregar Descuento");
    $("#pestanas li").removeClass("active");
    $('#inicio').addClass('active');
    $("#pst_cnt div").removeClass("active");
    $('#profile').addClass('active');
    $('#pnl_pcn_btn').html('<button type="button" onclick="fn_guardarDescuento(' + cdn_id + ')" class="btn btn-primary">Guardar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');
}

function fn_guardarDescuento(cdn_id) {
    var nuevoDescuento = {};
    nuevoDescuento.insertarDescuento = 1;

    nuevoDescuento.aplica_minimos = fn_retornaValorCheckbox("dscto_aplica_minimo_maximo");
    nuevoDescuento.aplica_cantidad = fn_retornaValorCheckbox("dsct_aplica_cantidad");
    nuevoDescuento.automatico = fn_retornaValorCheckbox("dscto_automatico");
    nuevoDescuento.seguridad = fn_retornaValorCheckbox("dscto_seguridad");
    nuevoDescuento.estado = fn_retornaValorCheckbox("dsct_std_id");
    nuevoDescuento.dsct_cupones = fn_retornaValorCheckbox("dsct_cupones");

    nuevoDescuento.dsct_descripcion = $('#dsct_dscrp').val();
//    nuevoDescuento.fecha_final = $('#FechaFinal').val();
//    nuevoDescuento.fecha_inicial = $('#FechaInicial').val();
    nuevoDescuento.apld_id = $("input[name='grp_apld']:checked").val();
    nuevoDescuento.std_id = $("#dsct_std_id").is(':checked');
    nuevoDescuento.tpd_id = $('#slct_tp_dsct').val();
    nuevoDescuento.dsct_min = $('#dsct_mnt_min').val();

    var valorInputDsctMax = $('#dsct_mnt_max').val();
    if (1 === nuevoDescuento.aplica_cantidad && (!valorInputDsctMax || valorInputDsctMax.length === 0)) {
        nuevoDescuento.dsct_max = 999;
    } else {
        nuevoDescuento.dsct_max = valorInputDsctMax;
    }

    var textoSeleccionado = $("#slct_tp_dsct option:selected").text();
    var valorInputDescuento = $('#dsct_valor').val();

    nuevoDescuento.textoSeleccionado = textoSeleccionado;
    nuevoDescuento.dsct_valor = fn_recuperarValorDescuento(valorInputDescuento, textoSeleccionado);

    nuevoDescuento.cdn_id = $("#sess_cdn_id").val();
    //nuevoDescuento.parametros = '';


    //var strRestaurantes=arrayRestaurantes.join("_")+"_";
    var strRestaurantes = crearStringRestaurantesSeleccionados();
    nuevoDescuento.restaurantes = strRestaurantes;

    var arrayProductos = buscarProductosSeleccionados();
    var strProductos = arrayProductos.join("_") + "_";
    nuevoDescuento.productos = strProductos;

    var resultadoValidacion = fn_validarDatosDescuento(nuevoDescuento);
    if (!resultadoValidacion)
        return false;
    fn_cargando_adm(1);
    $.ajax({async: false, type: "POST", dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        //url: "../adminDescuentos/config_descuentos.php?modificarDescuento=1",
        url: "../adminDescuentos/config_descuentos.php",
        data: nuevoDescuento,
        success: function (datos) {
            if (datos.Confirmar > 0) {
                $('#modal').modal('hide');
                fn_cargarDescuentos();
                fn_alerta("<b>Listo!</b> Descuento guardado correctamente.", "success");
                return true;
            } else {
                fn_alerta("<b>Alerta!</b> No se pudo guardar el descuento.", "danger");
                return true;
            }
        },
        complete: function () {
            fn_cargando_adm(0);
        }
    });
}

function crearStringRestaurantesSeleccionados() {
    var arrayRestaurantes = buscarRestaurantesSeleccionados();
    var arrayStrings = [];
    arrayRestaurantes.forEach(function (element) {
        arrayStrings.push(element.rst_id + "_" + element.inicio + "_" + element.fin);
    });
    return arrayStrings.join("_") + "_";
}

function buscarRestaurantesSeleccionados() {
    var restaurantesFechas = [];
    $("#lst_rst_dscto .restaurante_descto").each(function (index) {
        $this = $(this);
        var fechaInicio = $this.find(".inicioperiodo").val();
        var fechaFin = $this.find(".finperiodo").val();
        var restauranteActual = {
            rst_id: $this.data("rst-id"),
            inicio: fechaInicio,
            fin: fechaFin
        };
        restaurantesFechas.push(restauranteActual);
    });
    return restaurantesFechas;
}

function buscarProductosSeleccionados() {
    //Productos Seleccionados
    var productos = [];
    $("#listado-plus-agregardos-descuento li").each(function () {
        productos.push($(this).data("plu_id"));
    });
    return productos;
}

function fn_retornaValorCheckbox(id) {
    if (null === document.getElementById(id))
        return 0;
    if ($("#" + id).is(":checked"))
        return 1;
    return 0;
}

function fn_validarDatosDescuento(nuevoDescuento) {
    if (!fn_validarCampoVacio(nuevoDescuento.dsct_descripcion)) {
        fn_alerta("<b>Alerta!</b> Descripcion campo obligatorio.", "danger");
        return false;
    }
    if (nuevoDescuento.tpd_id == 0) {
        fn_alerta("<b>Alerta!</b> Seleccione el Tipo de Descuento.", "danger");
        return false;
    }

    if (nuevoDescuento.aplica_minimos == 1) {
        if (!$.isNumeric(nuevoDescuento.dsct_min)) {
            fn_alerta("<b>Alerta!</b> Cantidad Minimo campo obligatorio.", "danger");
            return false;
        }
        if (!$.isNumeric(nuevoDescuento.dsct_max)) {
            fn_alerta("<b>Alerta!</b> Cantidad Maximo campo obligatorio", "danger");
            return false;
        }
        if (nuevoDescuento.dsct_min > nuevoDescuento.dsct_max) {
            fn_alerta("<b>Alerta!</b> La cantidad mínima no puede ser mayor a la cantidad máxima", "danger");
            return false;
        }
    }

    var esDescuentoPorcentaje = (nuevoDescuento.textoSeleccionado.indexOf("Porcentaje") !== -1);
    if (esDescuentoPorcentaje && (nuevoDescuento.dsct_valor <= 0 || nuevoDescuento.dsct_valor > 1)) {
        fn_alerta("<b>Alerta!</b> Por favor ingresa un valor entre 1 y 100.", "danger");
        return false;
    }
//
//    if (!($.isNumeric(nuevoDescuento.dsct_max)&&nuevoDescuento.dsct_max>0)) {
//        fn_alerta("<b>Alerta!</b> Cantidad Minima campo obligatorio.", "danger");
//        return false;
//    }
    if (!fn_validarCampoVacio(nuevoDescuento.apld_id)) {
        fn_alerta("<b>Alerta!</b> Debe Escoger la forma de aplicación del descuento.", "danger");
        return false;
    } else {
        var hayProductosSeleccionados = (nuevoDescuento.productos.length > 1);
        //alert(nuevoDescuento.productos.length);
        if (nuevoDescuento.apld_id == 2 && (!hayProductosSeleccionados)) {
            fn_alerta("<b>Alerta!</b> Debe seleccionar productos a los que se aplique el descuento.", "danger");
            return false;
        }
    }
    if (!($.isNumeric(nuevoDescuento.dsct_valor))) {
        fn_alerta("<b>Alerta!</b> Valor del Descuento campo obligatorio.", "danger");
        return false;
    }
    if (!fn_validarCampoVacio(nuevoDescuento.restaurantes)) {
        fn_alerta("<b>Alerta!</b> Seleccionar Locales donde aplicar Descuento.", "danger");
        return false;
    }
    return true;
}

function fn_buscarDescuentos() {
    $.ajaxSetup({async: false});
    //fn_cargando(1);
    fn_buscarListaDescuentos(1, 0, 0);
    $("#pag_0").addClass("active");
    //fn_cargando(0);
    $.ajaxSetup({async: true});
}

function fn_cargarDescuentos() {

    $.ajaxSetup({async: false});

    //$("#buscar").val("");
    var ls_opcion = $(":input[name=options]:checked").val();
    $.ajaxSetup({async: false});
    //acc_resultado = 1;
    acc_std = 0;

    fn_consultarListaDescuentos(0);


    $("#pag_0").addClass("active");

    $.ajaxSetup({async: true});
}

function fn_consultarListaDescuentos(pagina) {
    var cdn_id = $("#sess_cdn_id").val();
    var html = '<thead><tr class="active"><th>Descripci&oacute;n Descuento</th><th class="text-center" >Fecha Creaci&oacute;n</th><th class="text-center" >Fecha Modificaci&oacute;n</th><th class="text-center" >Valor</th><th >Tipo de Descuento</th><th >Aplica a:</th><th>Usr Crea:</th><th >Usr Modifica:</th><th class="text-center" >Estado</th></tr></thead>';
    send = {"consultarListaDescuentos": 1};
    send.resultado = 0;
    send.dsct_id = 0;
    send.cdn_id = cdn_id;
    send.parametro = '';
    send.pagina = pagina;
    send.registros = reg_pres;
    send.std_id = 0;
    fn_cargando_adm(1);
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function (datos) {
        if (datos.str > 0) {
            reg_max = datos[0]['num_reg'];
            html = html + '<tbody>';
            for (i = 0; i < datos.str; i++) {
                var valorMostrar = fn_setearValorDescuento(datos[i]['dsct_valor'], datos[i]['tpd_descripcion']);
                var textoMostrar = fn_formatearTextoValorDescuento(valorMostrar, datos[i]['tpd_descripcion']);

                html = html + '<tr id=' + datos[i]['dsct_id'] + ' class="trDescuento" ><td>' + datos[i]['dsct_descripcion'] + '</td><td class="text-center">' + datos[i]['fechainicio'] + '</td><td class="text-center">' + datos[i]['fechafin'] + '</td><td class="text-center">' + textoMostrar + '</td><td>' + datos[i]['tpd_descripcion'] + '</td><td>' + datos[i]['apld_descripcion'] + '</td><td>' + datos[i]['usuario_crea'] + '</td><td>' + datos[i]['usuario_modifica'] + '</td><td class="text-center">';
                if (datos[i]['std_id'] == 1) {
                    html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
                } else {
                    html = html + '<input type="checkbox" name="" value="1" disabled/>';
                }
                html = html + '</td></tr>';
            }
            html = html + '</tbody>';

            $('#listaDescuentos').html(html);
            $(".trDescuento td").on("click", function () {
                $(".trDescuento").removeClass("success");
                $(this).closest("tr").addClass("success");
            });
            $(".trDescuento").on("dblclick", function () {
                fn_modificarDescuento(this);
            });
        } else {
            reg_max = 0;
            html = html + '<tr><th colspan="7">No existen registros.</th></tr>';
            $('#listaDescuentos').html(html);
            // $('#paginador').html("");
        }

        $("#listaDescuentos").dataTable({'destroy': true});
        $("#listaDescuentos_length").hide();
        fn_cargando_adm(0);
    });
}

function fn_buscarListaDescuentos(resultado, pagina, std_id) {
    var cdn_id = $("#sess_cdn_id").val();
    var html = '<tr class="active"><th style="background-color:#08C">Descripci&oacute;n Descuento</th><th class="text-center" style="background-color:#08C">Fecha Creaci&oacute;n</th><th class="text-center" style="background-color:#08C">Fecha Modificaci&oacuten</th><th class="text-center" style="background-color:#08C">Valor</th><th style="background-color:#08C">Tipo de Descuento</th><th style="background-color:#08C">Aplica a:</th><th class="text-center" style="background-color:#08C">Estado</th></tr>';
    send = {"consultarListaDescuentos": 1};
    send.resultado = resultado;
    send.dsct_id = 0;
    send.cdn_id = cdn_id;
    send.parametro = '';
    send.pagina = pagina;
    send.registros = reg_pres;
    send.std_id = std_id;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function (datos) {
        if (datos.str > 0) {
            reg_max = datos[0]['num_reg'];
            for (i = 0; i < datos.str; i++) {
                var valorMostrar = fn_setearValorDescuento(datos[i]['dsct_valor'], datos[i]['tpd_descripcion']);
                var textoMostrar = fn_formatearTextoValorDescuento(valorMostrar, datos[i]['tpd_descripcion']);
                html = html + '<tr id=' + datos[i]['dsct_id'] + ' class="trDescuento"><td>' + datos[i]['dsct_descripcion'] + '</td><td class="text-center">' + datos[i]['fechainicio'] + '</td><td class="text-center">' + datos[i]['fechafin'] + '</td><td class="text-center">' + textoMostrar + '</td><td>' + datos[i]['tpd_descripcion'] + '</td><td>' + datos[i]['apld_descripcion'] + '</td><td class="text-center">';
                if (datos[i]['std_id'] == 1) {
                    html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
                } else {
                    html = html + '<input type="checkbox" name="" value="1" disabled/>';
                }
                html = html + '</td></tr>';
            }
            $('#listaDescuentos').html(html);
            $(".trDescuento td").on("click", function () {
                $(".trDescuento").removeClass("success");
                $(this).closest("tr").addClass("success");
            });
            $(".trDescuento").on("dblclick", function () {
                fn_modificarDescuento(this);
            });
            fn_cargando(0);
        } else {
            reg_max = 0;
            html = html + '<tr><th colspan="7">Busqueda no exitosa</th></tr>';
            $('#listaDescuentos').html(html);
            // $('#paginador').html("");
            fn_cargando(0);
        }
    });
}

function fn_seleccionarDescuento(fila) {
    $("#listaDescuentos tr").removeClass("success");
    $("#" + fila + "").addClass("success");
}

function fn_cargarLocalesNoAsignadosDescuento(dsct_id, cdn_id) {
    var send;
    send = {"cargarRestaurantesNoAsignadosDescuentos": 1};
    send.resultado = 5;
    send.dsct_id = dsct_id;
    send.cdn_id = cdn_id;
    send.parametro = '';
    send.pagina = 0;
    send.registros = 0;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var item = crearItemListaRestaurantes(datos[i]);
                $("#lst_rst_id").append(item);
            }
        }
    });
}

function fn_cargarLocalesAsignadosDescuento(dsct_id, cdn_id) {
    var send;
    send = {"cargarRestaurantesAsignadosDescuentos": 1};
    send.resultado = 10;
    send.dsct_id = dsct_id;
    send.cdn_id = cdn_id;
    send.parametro = '';
    send.pagina = 0;
    send.registros = 0;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                agregarRestaurante(datos[i]);
            }
        }
    });
}

function crearItemListaRestaurantes(datosRestaurante) {
    var $itemActual = $("<li class='list-group-item' />");
    $itemActual.data("rst_id", datosRestaurante['rst_id']);
    $itemActual.data("rst_descripcion", datosRestaurante['rst_descripcion']);

    var $botonAgregar = $("<button class='btn btn-xs btn-success'>Agregar</button>");
    $botonAgregar.on("click", function () {
        $itemActual.empty().remove();
        agregarRestaurante(datosRestaurante);
    });
    $itemActual.append($botonAgregar);
    $itemActual.append(" " + datosRestaurante['rst_descripcion']);
    return $itemActual;
}

function agregarRestaurante(datosRestaurante) {
    var $restauranteDescuento = $("<div class='restaurante_descto' style='padding:3px;border:solid 1px #dedede; margin-bottom:3px' />");
    $restauranteDescuento.data("rst-id", datosRestaurante['rst_id']);
    $restauranteDescuento.data("rst-descripcion", datosRestaurante['rst_descripcion']);

    var htmlRestauranteDescuento = $("<div>" + datosRestaurante['rst_descripcion'] + "</div>");
    var botonEliminarRestaurante = $("<button type='button' style='float:right' class='btn btn-danger btn-xs' ><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>");
    botonEliminarRestaurante.on("click", function () {
        $(this).parents(".restaurante_descto").hide(500, function () {
            var item = crearItemListaRestaurantes(datosRestaurante);
            $("#lst_rst_id").append(item);
            $(this).remove();
        });
    });
    htmlRestauranteDescuento.append(botonEliminarRestaurante);
    $restauranteDescuento.append(htmlRestauranteDescuento);
    var $calendarios = $("<div class=row />");
    $calendarios.append(crearInputCalendario("inicio", datosRestaurante));
    $calendarios.append(crearInputCalendario("fin", datosRestaurante));
    $restauranteDescuento.append($calendarios);
    $("#lst_rst_dscto").append($restauranteDescuento);
    return true;
}

function crearInputCalendario(tipo, datosRestaurante) {
    var nombre;
    var placeholder;
    var clase;
    var valor;

    var fechaInicio = datosRestaurante.fechainicio || $("#FechaInicial").val();
    var fechaFin = datosRestaurante.fechafin || $("#FechaFinal").val();
    if ("inicio" === tipo) {
        nombre = "FechaInicia";
        placeholder = "Fecha Inicia";
        clase = "inicioperiodo";
        valor = fechaInicio;
    } else {
        nombre = "FechaFinaliza";
        placeholder = "Fecha Finaliza";
        clase = "finperiodo";
        valor = fechaFin;
    }
    var $inputBusqueda = $("<div class='form-group col-xs-6' style='margin-bottom:8px'></div>")
            .append("<div><div class='form-group' style='margin-bottom:8px'><label for='" + nombre + "' class='control-label'>" + nombre + "</label><div class='input-prepend input-group'><span class='input-group-addon'><i class='glyphicon glyphicon-calendar fa fa-calendar'></i></span><input type='text' value='' class='form-control input-fecha-dinamico " + clase + "' name='" + nombre + "'  placeholder='" + placeholder + "' /></div></div></div>");

    $inputBusqueda.find(".input-fecha-dinamico").val(valor);
    $inputBusqueda.find(".input-fecha-dinamico").daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'});

    return $inputBusqueda;
}

function fn_modificarDescuento(elemento) {
    var $descuentoModificar = $(elemento);
    var dsct_id = $descuentoModificar.attr("id");
    $("#lst_rst_id").empty();
    var cdn_id = $("#sess_cdn_id").val();
    fn_resetearModalDescuento();
    send = {"cargarDetalleDescuento": 1};
    send.resultado = 9;
    send.dsct_id = dsct_id;
    send.cdn_id = cdn_id;
    fn_cargando_adm(1);
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDescuentos/config_descuentos.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#titulo_pest3").html("Aplicar en Productos");
                fn_procesoPlusDescuento(dsct_id, cdn_id);
                fn_asignaValoresModalDescuento(datos[0]);
            }
            fn_cargando_adm(0);
        }
    });
    fn_cargarLocalesNoAsignadosDescuento(dsct_id, cdn_id);
    fn_cargarLocalesAsignadosDescuento(dsct_id, cdn_id);
    $('#modal').modal('show');
    $("#pestanas li").removeClass("active");
    $('#inicio').addClass('active');
    $("#pst_cnt div").removeClass("active");
    $('#profile').addClass('active');
    var $footerModal = $('#pnl_pcn_btn');
    $footerModal.html('<button type="button" class="btn btn-primary">Guardar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');
    $footerModal.find(".btn-primary").on("click", function () {
        fn_guardarCambiosDescuento(dsct_id, cdn_id);
    });
}

function fn_resetearModalDescuento() {
    slcn_plus_lmcn = '';
    $('#dsct_mnt_max').attr('disabled', false);
    $('#titulomodal').html("Modificar Descuento: ");
    $('#dsct_dscrp').val("");
    $('#FechaFinal').val("");
    $('#FechaInicial').val("");
    slcn_apld_id = 0;
    //$('#slct_prf').val("");
    $('#slct_tp_dsct').val("");
    $('#dsct_mnt_max').val("");
    $('#dsct_valor').val("");
    $('#dsct_mnt_min').val("");
    $('#dsct_cnt_min').val("");
    $("#listado-plus-agregardos-descuento").empty();
    $("#lst_rst_dscto").empty();
    mostrarBotonAplicaDiscrecional();
}

function fn_asignaValoresModalDescuento(datosDescuento) {
    seleccionarRadioGrupo("grp_apld", datosDescuento['apld_id']);
    seleccionarRestricciones(datosDescuento['apld_id']);
    $('#titulomodal').append(datosDescuento['dsct_descripcion']);
    $('#dsct_dscrp').val(datosDescuento['dsct_descripcion']);

    fn_asignaValorCheckbox("dsct_std_id", datosDescuento['estado']);
    fn_asignaValorCheckbox("dsct_aplica_cantidad", datosDescuento['dsct_aplica_cantidad']);
    fn_cambiarEtiquetasInputCantidades(datosDescuento['dsct_aplica_cantidad']);
    fn_asignaValorCheckbox("dscto_aplica_minimo_maximo", datosDescuento['dsct_aplica_min_max']);
    fn_asignaValorCheckbox("dscto_automatico", datosDescuento['dsct_automatico']);
    fn_asignaValorCheckbox("dscto_seguridad", datosDescuento['dsct_seguridad']);
    fn_asignaValorCheckbox("dsct_cupones", datosDescuento['dsct_cupones']);


    $('#slct_tp_dsct').val(datosDescuento['IDTipoDescuento']);

    var textoSeleccionado = $("#slct_tp_dsct option:selected").text();
    var esDescuentoPorcentaje = (textoSeleccionado.indexOf("Porcentaje") !== -1);
    if (esDescuentoPorcentaje) {
        $('#dsct_valor').val(parseFloat(datosDescuento['dsct_valor'] * 100));
    } else {
        $('#dsct_valor').val(parseFloat(datosDescuento['dsct_valor']));
    }

    $('#dsct_mnt_min').val(datosDescuento['dsct_minimo']);
    $('#dsct_mnt_max').val(datosDescuento['dsct_maximo']);
    $('#FechaInicial').val(datosDescuento['fechainicio']);
    $('#FechaFinal').val(datosDescuento['fechafin']);
    $('#FechaFinal').trigger('chosen:updated');
    $('#FechaInicial').trigger('chosen:updated');

    //Seguridad
    $('#slct_prf').val(datosDescuento['prf_id']);

    if(1==datosDescuento["dsct_cupones"]){
        $(".bt-aplica-discrecional").hide();
    }
}

function fn_asignaValorCheckbox(idElemento, valor) {
    var estado = (valor == 1) ? true : false;
    $("#" + idElemento).prop("checked", estado);
}

function fn_bloquearCheckbox(idElemento, valor) {
    var estado = (valor == 1) ? true : false;
    $("#" + idElemento).prop("disabled", estado);
}

function fn_guardarCambiosDescuento(dsct_id, cdn_id) {

    var descuentoActualizar = {};
    descuentoActualizar.modificarDescuento = 1;

    descuentoActualizar.aplica_minimos = fn_retornaValorCheckbox("dscto_aplica_minimo_maximo");
    descuentoActualizar.aplica_cantidad = fn_retornaValorCheckbox("dsct_aplica_cantidad");
    descuentoActualizar.automatico = fn_retornaValorCheckbox("dscto_automatico");
    descuentoActualizar.dsct_cupones = fn_retornaValorCheckbox("dsct_cupones");
    descuentoActualizar.seguridad = fn_retornaValorCheckbox("dscto_seguridad");
    descuentoActualizar.estado = fn_retornaValorCheckbox("dsct_std_id");

    descuentoActualizar.dsct_id = dsct_id;
    descuentoActualizar.dsct_descripcion = $('#dsct_dscrp').val();
//    descuentoActualizar.fecha_final = $('#FechaFinal').val();
//    descuentoActualizar.fecha_inicial = $('#FechaInicial').val();
    descuentoActualizar.apld_id = $("input[name='grp_apld']:checked").val();
    descuentoActualizar.std_id = $("#dsct_std_id").is(':checked');
    //descuentoActualizar.prf_id = $('#slct_prf').val();
    descuentoActualizar.tpd_id = $('#slct_tp_dsct').val();

    var valorminimoFormulario = ($("#dsct_mnt_min").val().length === 0) ? 0 : $("#dsct_mnt_min").val();
    var valormaximoFormulario = ($("#dsct_mnt_max").val().length === 0) ? 0 : $("#dsct_mnt_max").val();

    descuentoActualizar.dsct_min = parseFloat(valorminimoFormulario);
    var valorInputDsctMax = parseFloat(valormaximoFormulario);
    if (1 === descuentoActualizar.aplica_cantidad && (!valorInputDsctMax || valorInputDsctMax.length === 0)) {
        descuentoActualizar.dsct_max = 999;
    } else {
        descuentoActualizar.dsct_max = valorInputDsctMax;
    }

    var textoSeleccionado = $("#slct_tp_dsct option:selected").text();
    descuentoActualizar.textoSeleccionado = textoSeleccionado;

    var valorIniputDescuento = $('#dsct_valor').val();
    descuentoActualizar.dsct_valor = fn_recuperarValorDescuento(valorIniputDescuento, textoSeleccionado);

    descuentoActualizar.cdn_id = cdn_id;

    var strRestaurantes = crearStringRestaurantesSeleccionados();
    descuentoActualizar.restaurantes = strRestaurantes;

    var arrayProductos = buscarProductosSeleccionados();
    var strProductos = arrayProductos.join("_") + "_";
    descuentoActualizar.productos = strProductos;

    var resultadoValidacion = fn_validarDatosDescuento(descuentoActualizar);
    if (!resultadoValidacion)
        return false;
    fn_cargando_adm(1);
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminDescuentos/config_descuentos.php", data: descuentoActualizar,
        success: function (datos) {
            if (datos.Confirmar > 0) {
                $('#modal').modal('hide');
                fn_cargarDescuentos();
                fn_alerta("<b>Listo!</b> Descuento guardado correctamente.", "success");
                return true;
            } else {
                fn_alerta("<b>Alerta!</b> No se guardó el descuento.", "danger");
                return false;
            }
        },
        complete: function() {
            fn_cargando_adm(0);
        }
    });

}

function fn_procesoPlusDescuento(dsct_id, cdn_id) {
    fn_cargarPlusDescuento(4, dsct_id, cdn_id, 1);
}

function fn_cargarPlusDescuento(resultado, dsct_id, cdn_id, cla_id) {
    plus_agregados_descuento = [];
    var html = '';
    send = {"cargarPlusDescuento": 1};
    send.resultado = resultado;
    send.dsct_id = dsct_id;
    send.cdn_id = cdn_id;
    send.parametro = '';
    send.pagina = 0;
    send.registros = 0;
    send.cla_id = cla_id;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function (datos) {
        $.each(datos, function (key, element) {
            if ("object" === $.type(element)) {
                plus_agregados_descuento[element.plu_id] = element;
                var nuevoElemento = crearElementoListaPlusDescuento(element);
                $("#listado-plus-agregardos-descuento").append(nuevoElemento);
            }
        });
    });

    return true;
}

function crearElementoListaPlusDescuento(elemento) {
    var $elementoLista = $("<li class='elemento-lista-plus-descuento list-group-item'></li>");
    $elementoLista.data("agregado", elemento.agregado);
    var textoDescripcion = elemento.plu_num_plu + " | " + elemento.plu_descripcion;
    $elementoLista.data("plu_descripcion", textoDescripcion);
    $elementoLista.data("plu_id", elemento.plu_id);
    $elementoLista.data("plu_num_plu", elemento.plu_num_plu);
    $elementoLista.html(textoDescripcion);
    var botonEliminarPlu = $("<button type='button' style='float:right' class='btn btn-danger btn-xs' ><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>");
    botonEliminarPlu.on("click", function () {
        $(this).closest(".elemento-lista-plus-descuento").empty().remove();
//        $(this).closest("li").css({"text-decoration": "line-through"}).data("agregado", 0);
//        $(this).remove();
        delete plus_agregados_descuento[elemento.plu_id];
    });
    $elementoLista.append(botonEliminarPlu);

    return $elementoLista;
}

function fn_validarCampoVacio(valor) {
    if ("undefined" === typeof valor) {
        return false;
    }
    return (valor.length > 0);
}

function fn_alerta(mensaje, tipo) {
    setTimeout(function () {
        $.bootstrapGrowl(mensaje, {
            type: tipo,
            align: 'right',
            width: '500',
            allow_dismiss: false
        });
    }, 100);
}

function filtrarLocales() {
    // Declare variables
    var input, filter, ul, li, a, i;
    input = document.getElementById('buscar_Restaurantes');
    filter = input.value.toUpperCase();
    ul = document.getElementById("lst_rst_id");
    li = ul.getElementsByTagName('li');

    // Loop through all list items, and hide those who don't match the search query
    for (i = 0; i < li.length; i++) {
        //a = li[i].getElementsByTagName("a")[0];
        string = (li[i].innerText || li[i].textContent);
        if (li[i].innerHTML.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

function fn_setearValorDescuento(valor, tipoDescuento) {
    var esDescuentoPorcentaje = (tipoDescuento.indexOf("Porcentaje") !== -1);
    if (esDescuentoPorcentaje) {
        return parseFloat(valor * 100).toFixed(0);
    } else {
        return parseFloat(valor);
    }
    return 0;
}

function fn_recuperarValorDescuento(valor, tipoDescuento) {
    var esDescuentoPorcentaje = (tipoDescuento.indexOf("Porcentaje") !== -1);
    if (esDescuentoPorcentaje) {
        //return parseFloat(valor/100).toFixed(2);
        return parseFloat(valor / 100).toFixed(2);
    } else {
        return parseFloat(valor);
    }
    return 0;
}

function fn_formatearTextoValorDescuento(valor, tipoDescuento) {
    var esDescuentoPorcentaje = (tipoDescuento.indexOf("Porcentaje") !== -1);
    if (esDescuentoPorcentaje) {
        return valor + "%";
    } else {
        return "$" + valor;
    }
    return false;
}

function fn_cambiarEtiquetasInputCantidades(esCantidad) {
    if (esCantidad) {
        $("#labelInputMinimo").html("Cantidad");
        $("#labelInputMaximo").html("Cantidad");
    } else {
        $("#labelInputMinimo").html("Monto");
        $("#labelInputMaximo").html("Monto");
    }
}

function ocultarBotonAplicaDiscrecional(){
    $(".bt-aplica-discrecional").hide();
    $("#pcn_apld_id").find(".bt-aplica-descuento").first().trigger("click");
}

function mostrarBotonAplicaDiscrecional(){
    $(".bt-aplica-discrecional").show();
}

/*-------------------------------------------------------
 Funcion para mostrar pantalla de espera (Cargando)
 -------------------------------------------------------*/
function fn_cargando(estado) {
    if (estado) {
        $("#cargando").css("display", "block");
        $("#cargandoimg").css("display", "block");
    } else {
        $("#cargando").css("display", "none");
        $("#cargandoimg").css("display", "none");
    }
}

function fn_cargando_adm(estado) {
    if (estado) {
        $("#mdl_rdn_pdd_crgnd").show();
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
    }
}