/* global alertify */

///////////////////////////////////////////////////////////
///////DESARROLLADO POR: Juan Méndez ////////////////////
///////DESCRIPCION:Configuración Menú Toma de Pedidos ///////////////////
///////TABLAS INVOLUCRADAS: //////// //
///////FECHA ULTIMA MODIFICACION: 26/05/2015//////////////////////////////
///////USUARIO QUE MODIFICO:  Jose Fernandez//////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Nuevo estilo en pantalla y///////////////
//////////////////////////////mejora de la funcionalidad//////////////////
//////////////////////////////Buscador todos los campos///////////////////
//////////////////////////////Cambio de etiquetas y estados con check/////
///////////////////////////////////////////////////////////  

lc_codigo = -1; //codigo de la factura
lc_grabar = -1; //para saber si voy a grabar o actualizar los datos de la solicitud
lc_estado = -1; //almacena el estado de la solicitud seleccionada
lc_usuario = -1;
lc_clave = -1;
lc_puesto = -1;
lc_contador = 0;
lc_bandera = -1;
var grupos = 10;
var Accion = 0;
var total_registros = 0;
lc_banderaEstado = -1;
lc_banderaChecks = -1;
lc_estado = '';

$(document).ready(function () {
    $("#par_numplu").hide();
    $("#img_buscar").hide();
    lc_bandera = 1;
    lc_banderaEstado = -1;
    $("#img_remove").hide();
    fn_btn('agregar', 1);
    $("#menu_producto").hide();
    $("#mdl_menuProducto").hide();
    $("#mdl_menuProductoNuevo").hide();
    $("#check_todos").prop("checked", true);

    fn_esconderDiv();
    fn_controlCampos();
    fn_cargarCaracteristicaPorEstado(0, 11, 'activo');
    //marcar(':checkbox');

    $("#impresionPlu").focusout(function () {
        fn_obtenerDatos();
    });

    $("#colorTexto").focusout(function () {
        fn_obtenerDatos();
    });

    $("#colorFondo").focusout(function () {
        fn_obtenerDatos();
    });

    $("#par_numplu").keyup(function (event) {
        if (event.keyCode == '13') {
            fn_cargarCaracteristica();
        }
    });

    $("#par_numplu").keypress(function () {
        Accion = 0;
        fn_cargarCaracteristica(0, grupos + 1);
    });

    $("#par_numplu").keypress(function () {
        if ($("#par_numplu").val() == '') {
            $("#img_buscar").show();
            $("#img_remove").hide();
        } else {
            $("#img_remove").show();
            $("#img_buscar").hide();
        }

        fn_cargarCaracteristica(0, grupos + 1);
    });

    $('#FechaInicial').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $('#FechaFinal').daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $('#FechaInicialNuevo').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $('#FechaFinalNuevo').daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });
});

function fn_limpiaBuscador() {
    $("#par_numplu").val('');
    $("#img_remove").hide();
    $("#img_buscar").show();
}

function fn_pluImpresion() {
    $("#impresionPluNuevo").val($("#selPlus").text());
}

function fn_cargaPlus() {
    $("#selPlus").empty();
    send = {"cargarPlus": 1};
    $.getJSON("../adminMenuBotones/config_menuProducto.php", send, function (datos) {
        html = '';
        html = html + "<option selected value='0'>---Seleccione Plu---</option>";
        //$('#selPlus').html("<option selected value='0'>---Seleccione Plu---</option>");
        for (i = 0; i < datos.str; i++) {
            html = html + "<option  value='" + datos[i]['plu_id'] + "' name='" + datos[i]['plu_descripcion'] + "'>" + datos[i]['plu_num_plu'] + " - " + datos[i]['plu_descripcion'] + " - " + datos[i]['cla_Nombre'] + "</option>";
            //$("#selPlus").append(html);		
        }
        $("#selPlus").html(html);
        $("#selPlus").chosen(
                {no_results_text: "No existen registros para ", search_contains: true}
        );
        $("#selPlus_chosen").css('width', '430');
        $('#selPlus').trigger("chosen:updated");


        $("#selPlus").change(function () {
            //name=$("#selPlus option:selected").text();
            name = $(this).find('option:selected').attr("name");
            $("#impresionPluNuevo").val(name);
            $("#impresionfacturanuevo").val(name);
        });
    });
}

function fn_esconderDiv() {
    $("#descripcion").hide();
    $("#diseno").hide();
    $("#administracion").hide();
}

function fn_mostrarDiv() {
    $("#descripcion").show();
    $("#diseno").show();
    $("#administracion").show();
}

/*function fn_sumarfecha(){
 
 //SUMA 5 AñOS A LA FECHA INICIAL AL CREAR UN NUEVO BOTON 
 $('#FechaInicialNuevo').change(function(){
 var fecha = this.value;
 fecha = fecha.split("/");
 var siguiente = (parseInt(fecha[2])+5);
 var nuevaFecha = fecha[0]+'/'+fecha[1]+'/'+siguiente;
 //$('#FechaFinalNuevo').val(nuevaFecha);
 });
 }*/

function fn_agregar() {
    fn_cargaPlus();
    fn_cargarTiendasAplicar();
    fn_limpiaCampos();
    $("#mdl_menuProductoNuevo").modal('show');
    $('#FechaInicialNuevo').val(moment().format('DD/MM/YYYY'));
    $("#colorTextooNuevo").spectrum("set", "#FFFFFF");
    $("#colorFondooNuevo").spectrum("set", "#000000");

    $("#colorTextooNuevo").spectrum({
        showPalette: true,
        hideAfterPaletteSelect: true,
        showButtons: false,
        color: "#f7f5f4",
        palette: [
            ["#000", "#444", "#666", "#999", "#ccc", "#eee", "#f3f3f3", "#fff"],
            ["#f00", "#f90", "#ff0", "#0f0", "#0ff", "#00f", "#90f", "#f0f"],
            ["#f4cccc", "#fce5cd", "#fff2cc", "#d9ead3", "#d0e0e3", "#cfe2f3", "#d9d2e9", "#ead1dc"],
            ["#ea9999", "#f9cb9c", "#ffe599", "#b6d7a8", "#a2c4c9", "#9fc5e8", "#b4a7d6", "#d5a6bd"],
            ["#e06666", "#f6b26b", "#ffd966", "#93c47d", "#76a5af", "#6fa8dc", "#8e7cc3", "#c27ba0"],
            ["#c00", "#e69138", "#f1c232", "#6aa84f", "#45818e", "#3d85c6", "#674ea7", "#a64d79"],
            ["#900", "#b45f06", "#bf9000", "#38761d", "#134f5c", "#0b5394", "#351c75", "#741b47"],
            ["#600", "#783f04", "#7f6000", "#274e13", "#0c343d", "#073763", "#20124d", "#4c1130"]
        ]
    });

    $("#colorFondooNuevo").spectrum({
        showPalette: true,
        hideAfterPaletteSelect: true,
        showButtons: false,
        color: "#000000",
        palette: [
            ["#000", "#444", "#666", "#999", "#ccc", "#eee", "#f3f3f3", "#fff"],
            ["#f00", "#f90", "#ff0", "#0f0", "#0ff", "#00f", "#90f", "#f0f"],
            ["#f4cccc", "#fce5cd", "#fff2cc", "#d9ead3", "#d0e0e3", "#cfe2f3", "#d9d2e9", "#ead1dc"],
            ["#ea9999", "#f9cb9c", "#ffe599", "#b6d7a8", "#a2c4c9", "#9fc5e8", "#b4a7d6", "#d5a6bd"],
            ["#e06666", "#f6b26b", "#ffd966", "#93c47d", "#76a5af", "#6fa8dc", "#8e7cc3", "#c27ba0"],
            ["#c00", "#e69138", "#f1c232", "#6aa84f", "#45818e", "#3d85c6", "#674ea7", "#a64d79"],
            ["#900", "#b45f06", "#bf9000", "#38761d", "#134f5c", "#0b5394", "#351c75", "#741b47"],
            ["#600", "#783f04", "#7f6000", "#274e13", "#0c343d", "#073763", "#20124d", "#4c1130"]
        ]
    });
}

/*---------------------------------------------
 Carga los productos de una Cadena selecvcionada
 -----------------------------------------------*/
function fn_cargarCaracteristica(inicio, fin) {
    lc_banderaChecks = 1;
    Accion = 0;
	var html = "<thead><tr class='active'><th class='text-center' width='20%'>Nombre Producto</th><th class='text-center' width='20%'>Nombre Bot&oacute;n</th><th class='text-center' width='20%'>Impresi&oacute;n Factura</th><th class='text-center'>Clasificaci&oacute;n</th><th class='text-center'>Plus</th><th class='text-center'>Master Plu</th><th class='text-center'>Activo</th><th class='text-center'>Como se Visualiza</th></tr></thead>";
    send = {"cargarCaracteristica": 1};
    send.Accion = Accion;
    send.inicio = inicio;
    send.fin = fin;
    send.filtro = $("#par_numplu").val();
    $.getJSON("../adminMenuBotones/config_menuProducto.php", send, function (datos) {
        if (datos.str > 0) {
            $("#menu_producto").show();
            var total_registros = datos[0]['Total'];
            if (total_registros > 0) {
                for (i = 0; i < datos.str; i++) {
					html = html + "<tr id='"+i+"' onclick='fn_seleccionar("+i+");' ondblclick='fn_seleccionarPlus("+i+", \""+datos[i]['magp_id']+"\", "+datos[i]['plu_id']+")'><td style='text-align: left; height:60px;'>"+datos[i]['plu_descripcion']+"</td><td style='text-align: left; height:60px;'>"+datos[i]['magp_desc_impresion']+"</td><td style='text-align: left; height:60px;'>"+datos[i]['magp_impresion']+"</td><td style='text-align: center; height:60px;'>"+datos[i]['cla_Nombre']+"</td><td style='text-align: center; height:60px;'>"+datos[i]['plu_num_plu']+"</td><td style='text-align: center; height:60px;'>"+datos[i]['plu_reportnumber']+"</td><td style='text-align: center; height:60px; font-size:16px;'>";					
                    if (datos[i]['estado'] == 'ACTIVO') {
                        html += "<input type='checkbox' checked='checked' disabled='disabled'/></td>";
                    } else {
                        html += "<input type='checkbox' disabled='disabled'/></td>";
                    }
                    html += "<td><button style='background-color:" + datos[i]['magp_color'] + "; color:" + datos[i]['magp_colortexto'] + "; height:60px; width:120px;'>" + datos[i]['magp_desc_impresion'] + "</button></td></tr>";
                }
                $('#detalle_preguntas').html(html);

                $('#detalle_preguntas').dataTable({destroy: true});

                $("#detalle_preguntas_length").hide();
            }
        } else {
            alertify.error('No se encontraron Plus.');
            $('#detalle_preguntas').html(html);
        }
    });
}

function fn_cargarCaracteristicaPorEstado(inicio, fin, opcion) {
    lc_estado = opcion;
    lc_banderaChecks = 2;
	var html = "<thead><tr class='active'><th class='text-center' width='20%'>Nombre Producto</th><th class='text-center' width='20%'>Nombre Bot&oacute;n</th><th class='text-center' width='20%'>Impresi&oacute;n Factura</th><th class='text-center'>Clasificaci&oacute;n</th><th class='text-center'>Plus</th><th class='text-center'>Master Plu</th><th class='text-center'>Activo</th><th class='text-center'>Como se Visualiza</th></tr></thead>";
    send = {"cargarCaracteristicaPorEstado": 1};
    send.inicio = inicio;
    send.fin = fin;
    send.filtro = $("#par_numplu").val();
    send.opcion = opcion;
    $.getJSON("../adminMenuBotones/config_menuProducto.php", send, function (datos) {
        if (datos.str > 0) {
            $("#menu_producto").show();

            var total_registros = datos[0]['Total'];
            if (total_registros > 0) {
                if (lc_banderaEstado == 1) {
                    fn_paginador(total_registros);
                    fn_paginacion_color(0);
                }

                for (i = 0; i < datos.str; i++) {
					html = html + "<tr id='"+i+"' onclick='fn_seleccionar("+i+");'  ondblclick='fn_seleccionarPlus("+i+",\""+datos[i]['magp_id']+"\", "+datos[i]['plu_id']+")'><td style='text-align: left; height:60px;'>"+datos[i]['plu_descripcion']+"</td><td style='text-align: left; height:60px;'>"+datos[i]['magp_desc_impresion']+"</td><td style='text-align: left; height:60px;'>"+datos[i]['magp_impresion']+"</td><td style='text-align: center; height:60px;'>"+datos[i]['cla_Nombre']+"</td><td style='text-align: center; height:60px;'>"+datos[i]['plu_num_plu']+"</td><td style='text-align: center; height:60px;'>"+datos[i]['plu_reportnumber']+"</td><td style='text-align: center; height:60px;'>";					
                    if (datos[i]['estado'] == 'ACTIVO') {
                        html += "<input type='checkbox' checked='checked' disabled='disabled'/></td>";
                    } else {
                        html += "<input type='checkbox' disabled='disabled'/></td>";
                    }
                    html += "<td><button style='background-color:" + datos[i]['magp_color'] + "; color:" + datos[i]['magp_colortexto'] + "; height:60px; width:120px;'>" + datos[i]['magp_desc_impresion'] + "</button></td></tr>";

                }
                $('#detalle_preguntas').html(html);

                $('#detalle_preguntas').dataTable({destroy: true});

                $("#detalle_preguntas_length").hide();
            }
        } else {
            alertify.error('No se encontraron Plus.');
            $('#detalle_preguntas').html(html);
        }
    });
}

function fn_seleccionar(fila) {
    $("#detalle_preguntas tr").removeClass("success");
    $("#" + fila + "").addClass("success");
}

function fn_abreModalInfoMenuProducto(magId, descripcion_producto) {
    $("#hid_magId").val(magId);
    $("#myModalLabel").text(descripcion_producto);
    $('#mdl_menuProducto').modal('show');
}

function fn_seleccionarPlus(fila, magp_id, plu) {
    aplica = 3;
    magp_id = magp_id;
    send = {"cargarDatosPluMenu": 1};
    send.magi_Id = magp_id;
    $.getJSON("../adminMenuBotones/config_menuProducto.php", send, function (datos) {
        if (datos.str > 0) {
            if (datos.estado == 'ACTIVO') {
                $("#check_activo").prop('checked', true);
            } else {
                $("#check_activo").prop('checked', false);
            }
            $("#impresionPluModificar").val(datos.magp_desc_impresion);
            $("#impresionfactura").val(datos.magp_impresion);
            $("#nombrePlu").val(datos.plu_descripcion);
            $("#colorTextoo").spectrum("set", datos.magp_colortexto);
            $("#colorFondoo").spectrum("set", datos.magp_color);
            $("#FechaInicial").val(datos.magp_fecha_inicio);
            $("#FechaFinal").val(datos.magp_fecha_vencimiento);
            fn_abreModalInfoMenuProducto(datos.magp_id, datos.plu_descripcion);
        } else {
            alertify.error('No se encontraron Plus.');
        }
    });

    $("#colorTextoo").spectrum({
        showPalette: true,
        hideAfterPaletteSelect: true,
        showButtons: false,
        palette: [
            ["#000", "#444", "#666", "#999", "#ccc", "#eee", "#f3f3f3", "#fff"],
            ["#f00", "#f90", "#ff0", "#0f0", "#0ff", "#00f", "#90f", "#f0f"],
            ["#f4cccc", "#fce5cd", "#fff2cc", "#d9ead3", "#d0e0e3", "#cfe2f3", "#d9d2e9", "#ead1dc"],
            ["#ea9999", "#f9cb9c", "#ffe599", "#b6d7a8", "#a2c4c9", "#9fc5e8", "#b4a7d6", "#d5a6bd"],
            ["#e06666", "#f6b26b", "#ffd966", "#93c47d", "#76a5af", "#6fa8dc", "#8e7cc3", "#c27ba0"],
            ["#c00", "#e69138", "#f1c232", "#6aa84f", "#45818e", "#3d85c6", "#674ea7", "#a64d79"],
            ["#900", "#b45f06", "#bf9000", "#38761d", "#134f5c", "#0b5394", "#351c75", "#741b47"],
            ["#600", "#783f04", "#7f6000", "#274e13", "#0c343d", "#073763", "#20124d", "#4c1130"]
        ]
    });

    $("#colorFondoo").spectrum({
        showPalette: true,
        hideAfterPaletteSelect: true,
        showButtons: false,
        palette: [
            ["#000", "#444", "#666", "#999", "#ccc", "#eee", "#f3f3f3", "#fff"],
            ["#f00", "#f90", "#ff0", "#0f0", "#0ff", "#00f", "#90f", "#f0f"],
            ["#f4cccc", "#fce5cd", "#fff2cc", "#d9ead3", "#d0e0e3", "#cfe2f3", "#d9d2e9", "#ead1dc"],
            ["#ea9999", "#f9cb9c", "#ffe599", "#b6d7a8", "#a2c4c9", "#9fc5e8", "#b4a7d6", "#d5a6bd"],
            ["#e06666", "#f6b26b", "#ffd966", "#93c47d", "#76a5af", "#6fa8dc", "#8e7cc3", "#c27ba0"],
            ["#c00", "#e69138", "#f1c232", "#6aa84f", "#45818e", "#3d85c6", "#674ea7", "#a64d79"],
            ["#900", "#b45f06", "#bf9000", "#38761d", "#134f5c", "#0b5394", "#351c75", "#741b47"],
            ["#600", "#783f04", "#7f6000", "#274e13", "#0c343d", "#073763", "#20124d", "#4c1130"]
        ]
    });

    var html = '';
    send = {"traerRestaurantes": 1};
    send.aplica = aplica;
    send.magp_id = magp_id;
    send.restaurante = $("#rest_id").val();
    $.getJSON("../adminMenuBotones/config_menuProducto.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                if (datos[i]['agregado'] == 1) {
                    html = html + "<a id='b" + i + "' class='list-group-item list-group-item-success' onclick='fn_seleccionRestaurantemod(" + i + "," + datos[i]['rst_id'] + ")'><input name='chck_rst[]' id='input" + i + "'  value='" + datos[i]['rst_id'] + "' type='checkbox'>&nbsp; " + datos[i]['rst_descripcion'] + "</a>";
                } else {
                    html = html + "<a id='b" + i + "' class='list-group-item' onclick='fn_seleccionRestaurantemod(" + i + "," + datos[i]['rst_id'] + ")'><input name='chck_rst[]' id='input" + i + "' value='" + datos[i]['rst_id'] + "' type='checkbox' checked='checked'>&nbsp; " + datos[i]['rst_descripcion'] + "</a>";
                }
            }
            //$("#rst_agregadonuevo").html(html);
            $("#rst_agregado").html(html);
        }
    });
}

function fn_guardarInfoMenuProducto() {
    Accion = 'AB';
    if ($("#impresionPluModificar").val() == '') {
        alertify.error("Debe ingresar un nombre para el bot&oacute;n.")
        $('#impresionPluModificar').focus();
        return false;
    }
    if ($("#impresionfactura").val() == '') {
        alertify.error("Debe ingresar una descripci&oacute; de impresi&oacute;n en la factura.")
        $('#impresionfactura').focus();
        return false;
    }
    if ($("#FechaInicial").val() == '') {
        alertify.error("Debe ingresar la fecha inicial del producto.")
        $('#FechaInicial').focus();
        return false;
    }
    if ($("#FechaFinal").val() == '') {
        alertify.error("Debe ingresar la fecha final del producto.")
        $('#FechaFinal').focus();
        return false;
    }
    magId = $("#hid_magId").val();
    send = {"guardaInfoPluMenu": 1};
    send.accion = Accion;
    send.plu_id = 0;
    send.color = $("#colorTextoo").spectrum('get').toHexString();//$("#colorTextoo").val();
    send.fondo = $("#colorFondoo").spectrum('get').toHexString();//$("#colorFondoo").val()
    send.nomImpresion = $("#impresionPluModificar").val();
    send.fechainicio = $("#FechaInicial").val();
    send.fechafin = $("#FechaFinal").val();
    send.usr_id = $("#idUser").val();
    send.mag = magId;
    if ($("#check_activo").is(':checked')) {
        send.estado = 'activo';
    } else {
        send.estado = 'inactivo';
    }
    send.impresionfactuta = $("#impresionfactura").val();
    $.getJSON("../adminMenuBotones/config_menuProducto.php", send, function (datos) {

        if (datos[0]['Respuesta'] == 1) {
            $('#mdl_menuProducto').modal('hide');
            alertify.error('No se puede Desactivar el botón, porque se encuentra asignado a una posicion en el menú');
        } else {
            $('#mdl_menuProducto').modal('hide');
            alertify.success("Datos actualizados correctamente");
            $("#colorTextoo").spectrum("destroy");
            $("#colorFondoo").spectrum("destroy");
            $("#selPlus").chosen("destroy");
            if (lc_banderaChecks == 1) {
                fn_cargarCaracteristica(0, grupos + 1);
            } else if (lc_banderaChecks == 2) {
                fn_cargarCaracteristicaPorEstado(0, grupos + 1, lc_estado);
            }

        }
    });
    fn_guardaFormaPagoAplicaTienda(magId);
}

function fn_guardarInfoMenuProductoNuevo() {
    Accion = 'IB';
    if ($("#selPlus").val() == 0) {
        alertify.error("Debe seleccionar un producto.")
        $('#selPlus').focus();
        return false;
    }
    if ($("#impresionPluNuevo").val() == '') {
        alertify.error("Debe ingresar un nombre para el bot&oacute;n.")
        $('#impresionPluNuevo').focus();
        return false;
    }
    if ($("#impresionfacturanuevo").val() == '') {
        alertify.error("Debe ingresar una descripci&oacute; de impresi&oacute;n en la factura.")
        $('#impresionfacturanuevo').focus();
        return false;
    }
    if ($("#FechaInicialNuevo").val() == '') {
        alertify.error("Debe ingresar la fecha inicial del producto.")
        $('#FechaInicialNuevo').focus();
        return false;
    }
    if ($("#FechaFinalNuevo").val() == '') {
        alertify.error("Debe ingresar la fecha final del producto.")
        $('#FechaFinalNuevo').focus();
        return false;
    }

    send = {"guardaInfoPluMenuNuevo": 1};
    send.accion = Accion;
    send.plu_id = $("#selPlus").val();
    send.colorNuevo = $("#colorTextooNuevo").spectrum('get').toHexString();//$("#colorTextooNuevo").val(); 
    send.fondoNuevo = $("#colorFondooNuevo").spectrum('get').toHexString();//$("#colorFondooNuevo").val();
    send.nomImpresionNuevo = $("#impresionPluNuevo").val();
    send.fechainicioNuevo = $("#FechaInicialNuevo").val();
    send.fechafinNuevo = $("#FechaFinalNuevo").val();
    send.usr_id = $("#idUser").val();
    send.magp_id = 0;
    send.estado = 'x';
    send.impresionfactuta = $("#impresionfacturanuevo").val();
    $.getJSON("../adminMenuBotones/config_menuProducto.php", send, function (datos) {
        for (i = 0; i < datos.str; i++) {
            $("#IDMenuAgrupacionProducto").val(datos[i]['IDMenuAgrupacionProducto']);
        }

        $("#mdl_menuProductoNuevo").modal("hide");
        $("#selPlus").val(0);
        alertify.success("Datos guardados correctamente");
        $("#colorTextooNuevo").spectrum("destroy");
        $("#colorFondooNuevo").spectrum("destroy");
        $("#opciones_estado label").removeClass("active");
        $("#check_activos").addClass("active");
        //$('#selPlus').trigger("chosen:updated"); //encerar select con chosen			
        fn_cargarCaracteristicaPorEstado(0, 11, 'activo');
        fn_guardaBotonAplicaTiendaNuevo();
    });
    //fn_guardaBotonAplicaTiendaNuevo();
}

/*-------------------------------
 Funcion para cambiar propiedades
 ---------------------------------*/
function fn_cambiarPropiedad(nombreImpresion, colorTexto, colorFondo) {
    $("#fondoEjemplo").css("background-color", colorFondo);
    $("#fondoEjemplo").css("color", colorTexto);
    $("#colorFondo").css("background-color", colorFondo);
    $("#colorTexto").css("background-color", colorTexto);
    $("#fondoEjemplo").val(nombreImpresion);
}

/*---------------------------------------
 funcion para obtener valores de la pagina
 -----------------------------------------*/
function fn_obtenerDatos() {
    nomImpresion = $("#impresionPlu").val();
    colorTexto = $("#colorTexto").val();
    colorFondo = $("#colorFondo").val();
    fn_cambiarPropiedad(nomImpresion, colorTexto, colorFondo);
}

function fn_camposLimpios()
{
    $("#descripcionPlu").val('');
    $("#impresionPlu").val('');
    $("#colorTexto").val('');
    $("#colorFondo").val('');
    $("#codigoPlu").val('');
    $("#botonId").val('');
}
/*--------------------------
 Coloca los campos seleccionados con la función
 en mayusculas
 -----------------------------*/
function aMays(e, elemento) {
    tecla = (document.all) ? e.keyCode : e.which;
    elemento.value = elemento.value.toUpperCase();
}

function fn_controlCampos() {
    $("#selecPlu").hide();
    $("#descripcionPlu").show();
    $(".ui-helper-hidden-accessible").hide();
    $("#descripcionPlu").prop('disabled', true);
    $("#impresionPlu").prop('disabled', true);
    $("#colorTexto").prop('disabled', true);
    $("#colorFondo").prop('disabled', true);
}

function fn_limpiaCampos() {
    $("#impresionPluNuevo").val('');
    $("#impresionfacturanuevo").val('');
    $("#colorTextooNuevo").val('');
    $("#colorFondooNuevo").val('');
}

function fn_btn(boton, estado) {
    if (estado) {
        $("#btn_" + boton).css("background", " url('../../imagenes/admin_resources/" + boton + ".png') 7px 3px no-repeat");
        $("#btn_" + boton).removeAttr("disabled");
        $("#btn_" + boton).addClass("botonhabilitado");
        $("#btn_" + boton).removeClass("botonbloqueado");
    } else {
        $("#btn_" + boton).css("background", " url('../../imagenes/admin_resources/" + boton + "_bloqueado.png') 7px 3px no-repeat");
        $("#btn_" + boton).prop('disabled', true);
        $("#btn_" + boton).addClass("botonbloqueado");
        $("#btn_" + boton).removeClass("botonhabilitado");
    }
}

function fn_limpiarpapeleta() {
    $('#selPlus').val(0);
    $("#colorTextooNuevo").spectrum("destroy");
    $("#colorFondooNuevo").spectrum("destroy");
    $("#colorTextoo").spectrum("destroy");
    $("#colorFondoo").spectrum("destroy");
    $('#selPlus').trigger("chosen:updated");
}

function fn_cargarTiendasAplicar() {
    var html = '';
    send = {"traerRestaurantes": 1};
    send.aplica = 1;
    send.magp_id = 0;
    send.restaurante = $("#rest_id").val();
    $.getJSON("../adminMenuBotones/config_menuProducto.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                if (datos[i]['agregado'] == 1) {
                    html = html + "<a id='a" + i + "' class='list-group-item list-group-item-success' onclick='fn_seleccionRestaurante(" + i + "," + datos[i]['rst_id'] + ")'><input name='chck_rst_id[]' id='input" + i + "'  value='" + datos[i]['rst_id'] + "' type='checkbox'>&nbsp; " + datos[i]['rst_descripcion'] + "</a>";
                } else {
                    html = html + "<a id='a" + i + "' class='list-group-item' onclick='fn_seleccionRestaurante(" + i + "," + datos[i]['rst_id'] + ")'><input name='chck_rst_id[]' id='input" + i + "' value='" + datos[i]['rst_id'] + "' type='checkbox' checked='checked'>&nbsp; " + datos[i]['rst_descripcion'] + "</a>";
                }
            }
            $("#rst_agregadonuevo").html(html);
            //$("#rst_agregado").html(html);
        }
    });
}

/*===========================================================*/
/*FUNCIONES PARA PARA MARCAR Y DESMARCAR TODAS LAS TIENDAS  */
/*==========================================================*/
marcar = function (elemento) {
    elemento = $('input[name="chck_rst_id[]"]');
    elemento.prop("checked", true);
}

desmarcar = function (elemento) {
    elemento = $('input[name="chck_rst_id[]"]');
    elemento.prop("checked", false);
}

marcarmod = function (elemento) {
    elemento = $('input[name="chck_rst[]"]');
    elemento.prop("checked", true);
}

desmarcarmod = function (elemento) {
    elemento = $('input[name="chck_rst[]"]');
    elemento.prop("checked", false);
}

/*======================================================*/
/*FUNCION PARA MARCAR LAS TIENDAS CON LA CLASE SUCCESS  */
/*======================================================*/
function fn_seleccionRestaurante(fila, codigo) {
    Cod_Restaurante = codigo;

    if ($("#a" + fila + "").hasClass("list-group-item-success")) {
        $("#a" + fila + "").removeClass("list-group-item-success");
        $("#input" + fila + "").prop("checked", true);
    } else {
        $("#a" + fila + "").addClass("list-group-item-success");
        $("#input" + fila + "").prop("checked", false);
    }
    $("#txt_idrestaurante").val(Cod_Restaurante);
}

function fn_seleccionRestaurantemod(fila, rest) {
    Restaurante_id = rest;

    if ($("#b" + fila + "").hasClass("list-group-item-success")) {
        $("#b" + fila + "").removeClass("list-group-item-success");
        $("#input" + fila + "").prop("checked", true);
    } else {
        $("#b" + fila + "").addClass("list-group-item-success");
        $("#input" + fila + "").prop("checked", false);
    }
    $("#txt_idrestaurantemod").val(Restaurante_id);
}

/*=========================================================================================*/
/*FUNCION PARA GUARDAR LAS TIENDAS SELECCIONADAS EN LA TABLA RESTAURANTE ATRIBUTOS NUEVO   */
/*=========================================================================================*/
function fn_guardaBotonAplicaTiendaNuevo() {
    var id_restaurante = new Array();

    $('input[name="chck_rst_id[]"]:checked').each(function () {
        id_restaurante.push($(this).val());
    });

    send = {"aplica_restaurante_nuevo": 1};
    send.id_restaurante = id_restaurante;
    send.restaurante = $("#rest_id").val();
    send.id_nuevo = $("#IDMenuAgrupacionProducto").val();
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminMenuBotones/config_menuProducto.php", data: send,
        success: function (datos) {
        }
    });
}

/*===================================================================================*/
/*FUNCION PARA GUARDAR LAS TIENDAS SELECCIONADAS EN LA TABLA RESTAURANTE ATRIBUTOS MODIFICAR   */
/*===================================================================================*/
function fn_guardaFormaPagoAplicaTienda(magp_id) {
    var id_restaurante = new Array();
    $('input[name="chck_rst[]"]:checked').each(function () {
        id_restaurante.push($(this).val());
    });

    send = {"aplica_restaurante": 1};
    send.id_restaurante = id_restaurante;
    send.magp_id = magp_id;
    send.restaurante = $("#rest_id").val();
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminMenuBotones/config_menuProducto.php", data: send,
        success: function (datos) {
}
});
}