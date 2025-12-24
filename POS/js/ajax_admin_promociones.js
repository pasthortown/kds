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
var $modalDetalleCupon = $("#modalDetalleCupon");
var $modalTipoCupon = $("#modalTipoCupon");
var $colorPickerModalTipoCupon = $("#colorTipoCupon");
var urlServidorTrade = $("#urlServidorTrade").html();
var urlServidorMasterData = $("#urlServidorMasterData").html();
var $filaTipoCuponActual = {};

$(document).ready(function() {
    fn_cargarDescuentos();

    $("#listaPromociones").on("dblclick", ".trPromocion", function() {
        var $this = $(this);
        var idPromocion = $this.closest(".trPromocion").attr("data-idPromocion");
        fn_cargando_adm(1);
        $("#incluirPagina").load('../promociones/editarPromocion.php?idPromocion=' + idPromocion);
    });

    /*
    $("#listaPromociones").on("click",".btnEditarPromocion",function(){
       var $this=$(this);
       var idPromocion=$this.closest(".trPromocion").attr("data-idPromocion");
        fn_cargando_adm(1);
        $("#incluirPagina").load('../promociones/editarPromocion.php?idPromocion='+idPromocion);
    });
    */

    $("#btnGuardarClaveJWT").on("click", function() {
        var valor = $("#inputClaveJWTTrade").val();
        $.ajax({
            url: "../promociones/config_promociones.php",
            type: "POST",
            data: {
                guardarClaveJWTTrade: 1,
                claveJWT: valor
            },
            dataType: "json",
            success: function(datos) {
                alertify.alert(datos.mensaje);
            },
            complete: function() {
                fn_cargando(0);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                fn_cargando(0);
                alertify.alert("Error al guardar");
            }
        });
    });

    $("#btnGuardarRutaEndpointCanjeTrade").on("click", function() {
        var valor = $("#rutaEndpointCanjeTrade").val();
        $.ajax({
            url: "../promociones/config_promociones.php",
            type: "POST",
            data: {
                guardarRutaEndpointCanjeTrade: 1,
                rutaEndpoint: valor
            },
            dataType: "json",
            success: function(datos) {
                alertify.alert(datos.mensaje);
            },
            complete: function() {
                fn_cargando(0);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                fn_cargando(0);
                alertify.alert("Error al guardar");
            }
        });
    });

    $("#btnGuardarUrlServidorTrade").on("click", function() {
        var valor = $("#servidorCanjesTrade").val();
        $.ajax({
            url: "../promociones/config_promociones.php",
            type: "POST",
            data: {
                guardarUrlServidorTrade: 1,
                urlServidor: valor
            },
            dataType: "json",
            success: function(datos) {
                alertify.alert(datos.mensaje);
            },
            complete: function() {
                fn_cargando(0);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                fn_cargando(0);
                alertify.alert("Error al guardar");
            }
        });
    });

    /*   Asignacion de valores a checkbox
        $("#dsct_aplica_cantidad").on("change",function(evt){
            var activado=evt.target.checked;
            fn_cambiarEtiquetasInputCantidades(activado);
            if(activado){
                fn_asignaValorCheckbox("dscto_aplica_minimo_maximo",1);
            }
        });
      */


    $('#listaPromociones tbody').on("click", ".btnDetallesPromocion", function() {
        var idCupon = $(this).closest("tr.trPromocion").attr("data-idpromocion");
        buscarDetallesPromocion(idCupon, crearHTMLModalDetalleCupon);
    });

    $(".rutaEndpoint").keyup(function() {
        var $this = $(this);
        var $divValor = $(this).closest(".form-endpoint-trade").find(".rutacompletaws");
        var textoservidor = $(this).closest(".configuracionesServidor").attr("data-servidor");
        var texto = $this.val();
        var textoCompletoRuta = "http://" + textoservidor + texto;
        $divValor.html(textoCompletoRuta);
    });

    $("#tblTiposCupon").on("dblclick", ".trTipoCupon", function(evt) {
        var $this = $(this);
        $filaTipoCuponActual = $this;

        var idParametro = $this.attr("data-idparametro");
        var strEtiqueta = $this.find(".etiquetaTipo").html();
        var strColor = $this.find(".colorTipo").attr("data-hexcolor");
        var strParametro = $this.find(".nombreTipo").html();

        //var $bodyModalTipoCupon = $modalTipoCupon.find(".modal-body");

        $("#idParametroTipoCupon").val(idParametro);
        $("#etiquetaTipoCupon").val(strEtiqueta);
        $("#nombreTipoCupon").val(strParametro);
        $colorPickerModalTipoCupon.spectrum('set', strColor);
        $modalTipoCupon.modal("show");
    });

    $colorPickerModalTipoCupon.spectrum({
        showInput: true
    });

    $("#btnGuardarTipoCupon").click(function() {
        var strIdParametro = $("#idParametroTipoCupon").val();
        var strEtiqueta = $("#etiquetaTipoCupon").val();
        var strColor = $colorPickerModalTipoCupon.spectrum('get').toHex();
        var nombreParametro = $("#nombreTipoCupon").val();

        var valores = {
            guardarTipoCupon: 1,
            nombrePolitica: 'CUPONES CATEGORIAS',
            idParametro: strIdParametro,
            etiqueta: strEtiqueta,
            nombreParametro: nombreParametro,
            color: strColor
        };
        $.ajax({
            url: "../promociones/config_promociones.php",
            type: "POST",
            data: valores,
            dataType: "json",
            success: function(datos) {
                actualizarFilaTablaTiposCupon($filaTipoCuponActual, valores);
                alertify.success("Guardado correctamente");
                $modalTipoCupon.modal("hide");
            },
            complete: function() {
                fn_cargando(0);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                fn_cargando(0);
                alertify.alert("Error al guardar");
            }
        });
    });
});

/*
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
*/

function fn_retornaValorCheckbox(id) {
    if (null === document.getElementById(id))
        return 0;
    if ($("#" + id).is(":checked"))
        return 1;
    return 0;
}

function fn_buscarDescuentos() {
    $.ajaxSetup({ async: false });
    //fn_cargando(1);
    fn_buscarlistaPromociones(1, 0, 0);
    $("#pag_0").addClass("active");
    //fn_cargando(0);
    $.ajaxSetup({ async: true });
}

function fn_cargarDescuentos() {

    $.ajaxSetup({ async: false });
    var ls_opcion = $(":input[name=options]:checked").val();
    fn_consultarlistaPromociones(0);
    $("#pag_0").addClass("active");
    $.ajaxSetup({ async: true });
}

function fn_consultarlistaPromociones(pagina) {


    var send = {
        "cargarListadoPromociones": 1,
        "pagina": pagina
    };
    var html = '';

    fn_cargando_adm(1);
    $.getJSON("../promociones/config_promociones.php", send, function(datos) {
        if (datos.estado == 0) {
            html += "<tr><td colspan='6'>Error al cargar la información</td></tr>";
            $('#listaPromociones tbody').html(html);
            return;
        }
        var totalDatos = datos.datos.length;
        if (totalDatos > 0) {
            var registros = datos.datos;
            registros.forEach(function(fila) {
                html = html + '<tr class="trPromocion"  data-idPromocion="' + fila.ID_Promociones + '">' +
                    '<td>' + fila.Nombre + '</td>' +
                    '<td>' + fila.Nombre_imprimible + '</td>' +
                    '<td>' + fila.Codigo_externo + '</td>' +
                    '<td>' + fila.Codigo_amigable + '</td>' +
                    '<td>' + fila.Activo_desde + '</td>' +
                    '<td>' + fila.Activo_Hasta + '</td>' +
                    '<td class="text-center">';
                if (fila.Activo == 1) {
                    html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
                } else {
                    html = html + '<input type="checkbox" name="" value="1" disabled/>';
                }
                html = html + '</td>';
                //html = html + '<td><button class="btnDetallesPromocion">Detalles</button><button class="btnEditarPromocion">Editar</button></td>';
                html = html + '<td style="text-align:center"><button class="btn btn-info btnDetallesPromocion"><span class="glyphicon glyphicon-info-sign"></span> </button></td>';
                html = html + '</tr>';
            });

            $('#listaPromociones tbody').html(html);
        } else {
            html = html + '<tr><th colspan="8">No existen registros.</th></tr>';
            $('#listaPromociones tbody').html(html);
        }

        $("#listaPromociones").dataTable({ 'destroy': true });
        $("#listaPromociones_length").hide();

    }).always(function() {
        fn_cargando_adm(0);
    });
}

function fn_buscarlistaPromociones(resultado, pagina, std_id) {
    var cdn_id = $("#sess_cdn_id").val();
    var html = '<tr class="active"><th style="background-color:#08C">Descripci&oacute;n Descuento</th><th class="text-center" style="background-color:#08C">Fecha Creaci&oacute;n</th><th class="text-center" style="background-color:#08C">Fecha Modificaci&oacuten</th><th class="text-center" style="background-color:#08C">Valor</th><th style="background-color:#08C">Tipo de Descuento</th><th style="background-color:#08C">Aplica a:</th><th class="text-center" style="background-color:#08C">Estado</th></tr>';
    send = { "cargarListadoPromociones": 1 };
    send.resultado = resultado;
    send.dsct_id = 0;
    send.cdn_id = cdn_id;
    send.parametro = '';
    send.pagina = pagina;
    send.registros = reg_pres;
    send.std_id = std_id;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
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
            $('#listaPromociones').html(html);
            $(".trDescuento td").on("click", function() {
                $(".trDescuento").removeClass("success");
                $(this).closest("tr").addClass("success");
            });
            $(".trDescuento").on("dblclick", function() {
                fn_modificarDescuento(this);
            });
            fn_cargando(0);
        } else {
            reg_max = 0;
            html = html + '<tr><th colspan="7">Busqueda no exitosa</th></tr>';
            $('#listaPromociones').html(html);
            // $('#paginador').html("");
            fn_cargando(0);
        }
    });
}

function fn_seleccionarDescuento(fila) {
    $("#listaPromociones tr").removeClass("success");
    $("#" + fila + "").addClass("success");
}

function fn_asignaValorCheckbox(idElemento, valor) {
    var estado = (valor == 1) ? true : false;
    $("#" + idElemento).prop("checked", estado);
}

function fn_bloquearCheckbox(idElemento, valor) {
    var estado = (valor == 1) ? true : false;
    $("#" + idElemento).prop("disabled", estado);
}

function fn_procesoPlusDescuento(dsct_id, cdn_id) {
    fn_cargarPlusDescuento(4, dsct_id, cdn_id, 1);
}

function fn_validarCampoVacio(valor) {
    if ("undefined" === typeof valor) {
        return false;
    }
    if (valor.length > 0) {
        return true;
    } else {
        return false;
    }
}

function buscarDetallesPromocion(idCupon) {
    $.ajax({
        url: "../promociones/config_promociones.php",
        type: "POST",
        data: {
            cargarDetallesCupon: 1,
            idCupon: idCupon
        },
        dataType: "json",
        success: crearHTMLModalDetalleCupon,
        complete: function() {
            fn_cargando(0);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alertify.alert("No se pudo consultar");
        }
    });
}

function crearHTMLModalDetalleCupon(datos) {

    var $bodyModalDetalleCupon = $modalDetalleCupon.find(".modal-body");
    var html = "";
    if (datos.estado == 0) {
        html += "<div class='panel panel-default'>" +
            "<div class='panel-body'>";
        html += "<div>Error al cargar los datos del cupón</div>";
        html += "</div>" + "</div>";
    } else {
        cupon = datos.datos;
        html += "<div class='panel panel-default'>" +
            "<div class='panel-body'>";

        html += "<div>" +
            "<h4>Datos Generales</h4>" +
            "<table class='table table-condensed'>" +
            "<tr><td>Nombre</td><td>" + cupon.Nombre + "</td></tr>" +
            "<tr><td>Codigo Amigable</td><td>" + cupon.Codigo_amigable + "</td></tr>" +
            "<tr><td>Válido desde</td><td>" + cupon.Activo_desde + "</td></tr>" +
            "<tr><td>Válido hasta</td><td>" + cupon.Activo_Hasta + "</td></tr>" +
            "</table>" +
            "</div>";
        html += "</div></div>";

        html += "<div class='panel panel-default'>" +
            "<div class='panel-body'>" +
            "<h4>Condiciones de canje</h4>" +
            "<table class='table'>" +
            "<tr><td>Montos de factura</td><td>" + formatearRestriccionMontoFactura(cupon.Bruto_minimo_factura, cupon.Bruto_maximo_factura) + "</td></tr>" +
            "<tr><td>Número mínimo de productos en factura</td><td>" + formatearRestriccionCantidadProductos(cupon.Cantidad_minima_productos_factura) + "</td></tr>" +
            "<tr><td>Días canjeable</td><td>" + formatearRestriccionDias(cupon.Requiere_dias, cupon.Dias_canjeable) + "</td></tr>" +
            "<tr><td>Canjeable en horario</td><td>" + formatearRestriccionHorarios(cupon.Requiere_horario, cupon.Horario_canjeable) + "</td></tr>" +
            "<tr><td>Límite de canjes por cliente</td><td>" + formatearRestriccionLimiteCanjes(cupon.Limite_canjes_cliente) + "</td></tr>" +
            "<tr><td>Límite de canjes total</td><td>" + formatearRestriccionLimiteCanjes(cupon.Limite_canjes_total) + "</td></tr>" +
            "<tr><td>Permite otras promociones</td><td>" + formatearRestriccionBooleanas(cupon.Permite_otras_promociones) + "</td></tr>" +
            "<tr><td>Permite descuento sobre descuento</td><td>" + formatearRestriccionBooleanas(cupon.Permite_descuento_sobre_descuento) + "</td></tr>";
        html += "</table></div></div>";

        html += "<div class='panel panel-default'>" +
            "<div class='panel-body'>";
        html += "<div><h4>Productos Requeridos</h4></div>";
        html += formatearPlusRequeridos(cupon.Requiere_productos, cupon.plus_requeridos);
        html += "</div></div>";

        html += "<div class='panel panel-default'>" +
            "<div class='panel-body'>";
        html += "<div><h4>Beneficios</h4></div>";
        html += formatearbeneficios(cupon.beneficios);
        html += "</div></div>";
    }
    $bodyModalDetalleCupon.html(html);
    $modalDetalleCupon.modal("show");
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

function formatearRestriccionMontoFactura(minimo, maximo) {

    if (minimo == 0.0 && maximo == 0.0) {
        return "Sin restricción";
    }
    if (minimo == 0.0 && maximo > 0.0) {
        return "Hasta $" + maximo.toString();
    }
    if (minimo > 0.0 && maximo == 0.0) {
        return "Desde $" + minimo.toString();
    }
    if (minimo > 0.0 && maximo > 0.0) {
        return "Entre $" + minimo.toString() + " y $" + maximo.toString();
    }
}

function formatearRestriccionDias(activo, stringDias) {
    if (activo == 0 || !activo) return "Sin restricción";
    var diasSemana = ["Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado", "Domingo"];
    var arrayDias = stringDias.split(",");
    var diasRetorno = [];
    arrayDias.forEach(function(element) {
        diasRetorno.push(diasSemana[element - 1]);

    });
    return diasRetorno.join();
}

function formatearRestriccionCantidadProductos(numeroProductos) {
    if (numeroProductos == 0) return "Sin restricción";
    return numeroProductos;
}

function formatearRestriccionHorarios(activo, stringHorarios) {
    if (activo == 0 || !activo) return "Sin restricción";
    var arrayHorarios = stringHorarios.split(",");

    return "De " + arrayHorarios[0] + " a " + arrayHorarios[1];
}

function formatearRestriccionLimiteCanjes(limite) {
    if (limite == 0) return "Sin restricción";
    return limite;
}

function formatearRestriccionBooleanas(valor) {
    return valor == 0 ? "No" : "Si";
}

function formatearbeneficios(beneficios) {
    var htmlBeneficios = "<table class='table table-condensed'><tbody>";
    beneficios.forEach(function(beneficio) {
        if (beneficio.Descuento == "" || beneficio.Descuento === null) {
            htmlBeneficios += "<tr><td>Producto gratis</td><td>" + beneficio.Producto + "</td></tr>";
        } else {
            htmlBeneficios += "<tr><td>Descuento</td><td>" + beneficio.Producto + "</td></tr>";
        }
    });
    htmlBeneficios += "</tbody></table>";
    return htmlBeneficios;
}


function formatearPlusRequeridos(activo, plusRequeridos) {
    if (!activo || activo == 0) {
        return "<div><b>Sin restricción</b></div>";
    }

    var htmlBeneficios = "<table class='table table-condensed'><tbody>";
    if (plusRequeridos.length == 0) {
        htmlBeneficios += "<tr><td colspan='2'><b>No se configuró plus</b></td></tr>";
    } else {
        console.log(plusRequeridos);
        htmlBeneficios += "<thead><tr><th colspan='2'>Producto</th><th colspan='2'>Cantidad</th></tr></thead><tbody>";
        plusRequeridos.forEach(function(plu) {
            htmlBeneficios += "<tr><td colspan='2'>" + plu.plu_descripcion + "</td><td colspan='2'>" + plu.Cantidad + "</td></tr>";
        });
    }
    htmlBeneficios += "</tbody></table>";
    return htmlBeneficios;
}

function actualizarFilaTablaTiposCupon($fila, valores) {

    $fila.find(".etiquetaTipo").html(valores.etiqueta);
    $fila.find(".colorTipo").attr("data-hexcolor");
    $fila.find(".colorTipo").css("background-color", "#" + valores.color);

    console.log(valores);
}