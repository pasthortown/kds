////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: PIERRE QUITIAQUEZ///////////////////////////////////////////////////////////////
///////////DESCRIPCION: PLUS/PRECIOS //////////////////////////////////////////////////////////
////////////////TABLAS: PLUS ////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

$(document).ready(function(){



});


function fn_consultarGerente(){
    var plu=$("#txnumpluprecios").val();
    if(plu==''){
        alertify.error("Debe ingresar un numero de plu.");
    }else{
    send = {"cargarGerente": 1};
    send.plu=plu;
     $.getJSON("../adminDescargaPluPrecio/config_admiDesPluprecio.php", send, function(datos) {
         $("#divtabla_plusMaxprecio").hide();
         $("#tituloSGPrecios").show();
         $("#tablaplusprecios").show();
        html="<thead><tr class='active'> ";
        html+="<th style='text-align:center'>C&oacute;digo</th>";
        html+="<th style='text-align:center' >N&uacute;mero</th>";
        html+="<th style='text-align:center'>Descripci&oacute;n</th>";
        html+="<th style='text-align:center'>Canal de Venta</th>";
         html+="<th style='text-align:center'>PVP</th>";
         html+="<th style='text-align:center'>Valor Neto</th>";
         html+="<th style='text-align:center'>Valor Iva</th>";
         html+="<th style='text-align:center'>Categoria</th>";
        html+="</tr></thead>";
        $("#tablaplusprecios").empty();
        if(datos.str>0){
            $("#btcargaprecios").show();
            for(i=0; i<datos.str; i++) {
               html+="<td style='text-align: center; width:500px'>"+datos[i]['plu_codigo']+"</td>";
                html+="<td style='text-align: center; width:200px'>"+datos[i]['plu_num']+"</td>";
                html+="<td style='text-align: center; width:200px'>"+datos[i]['plu_descripcion']+"</td>";
                html+="<td style='text-align: center; width:200px'>"+datos[i]['plu_canal']+"</td>";
                html+="<td style='text-align: center; width:200px'>"+datos[i]['Pvp']+"</td>";
                html+="<td style='text-align: center; width:200px'>"+datos[i]['Valor_Neto']+"</td>";
                html+="<td style='text-align: center; width:200px'>"+datos[i]['Valor_Iva']+"</td>";
                html+="<td style='text-align: center; width:500px'>"+datos[i]['Categoria']+"</td></tr>";

                $("#tablaplusprecios").html(html);
                $("#tablaplusprecios").dataTable(
                    {
                        'destroy': true
                    }
                );
                $("#tablaplusprecios_length").hide();
                $("#tablaplusprecios_paginate").addClass('col-xs-10');
                $("#tablaplusprecios_info").addClass('col-xs-10');
                $("#tablaplusprecios_length").addClass('col-xs-6');
            }
        }else{

            html+="<tr>";
            html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
            $("#tablaplusprecios").html(html);
            $("#divtabla_plusMaxprecio").hide();
            $("#btcargaprecios").hide();
        }
    });

    }
}

function fn_CargarTablaMaxpPrecios() {

    var plu = $("#txnumpluprecios").val();
    if (plu == '') {
        alertify.error("Debe ingresar un numero de plu.");
    } else {
        send = {"CargarTablaMaxPrecios": 1};
        send.plu = plu;
        $.getJSON("../adminDescargaPluPrecio/config_admiDesPluprecio.php", send, function (datos) {
            $("#divtabla_plusMaxprecio").show();
           $("#tituloMaxprecios").show();
            $("#tablapluspreciosmax").show();
            html = "<thead><tr class='active'> ";
            html += "<th style='text-align:center'>C&oacute;digo</th>";
            html += "<th style='text-align:center' >N&uacute;mero</th>";
            html += "<th style='text-align:center'>Descripci&oacute;n</th>";
            html += "<th style='text-align:center'>PVP</th>";
            html += "<th style='text-align:center'>Valor Neto</th>";
            html += "<th style='text-align:center'>Valor Iva</th>";
            html += "<th style='text-align:center'>Categoria</th>";
            html += "</tr></thead>";
            $("#tablapluspreciosmax").empty();
            if (datos.str > 0) {

                for (i = 0; i < datos.str; i++) {
                    html += "<td style='text-align: center; width:500px'>" + datos[i]['plu_codigo'] + "</td>";
                    html += "<td style='text-align: center; width:200px'>" + datos[i]['plu_num'] + "</td>";
                    html += "<td style='text-align: center; width:200px'>" + datos[i]['plu_descripcion'] + "</td>";
                    html += "<td style='text-align: center; width:200px'>" + datos[i]['Pvp'] + "</td>";
                    html += "<td style='text-align: center; width:200px'>" + datos[i]['Valor_Neto'] + "</td>";
                    html += "<td style='text-align: center; width:200px'>" + datos[i]['Valor_Iva'] + "</td>";
                    html += "<td style='text-align: center; width:500px'>" + datos[i]['Categoria'] + "</td></tr>";

                    $("#tablapluspreciosmax").html(html);
                    $("#tablapluspreciosmax").dataTable(
                        {
                            'destroy': true
                        }
                    );
                    $("#tablapluspreciosmax_length").hide();
                    $("#tablapluspreciosmax_paginate").addClass('col-xs-10');
                    $("#tablapluspreciosmax_info").addClass('col-xs-10');
                    $("#tablapluspreciosmax_length").addClass('col-xs-6');
                }
            } else {

                html += "<tr>";
                html += "<td colspan='6'>No existen datos para esta cadena.</td></tr>";
                $("#tablapluspreciosmax").html(html);
                $("#tablaplusmax").hide();
                $("#divtabla_plusMaxprecio").hide();
            }
        });
        $('#cargando').hide();

    }
}

function fn_cerrarModals() {
    $("#modal_Confirm").hide();
    $("#modal_Confirm1").hide();
}

function fn_confirmar(){
    $("#modal_Confirm").show();

}

function fn_confirmar1(){
    $("#modal_Confirm1").show();

}

function fn_consultarGerentePlus(){



    var plu=$("#txnumplu").val();
    if(plu==''){
        alertify.error("Debe ingresar un numero de plu.");

    }else{
        send = {"cargarGerentePlus": 1};
        send.plu=plu;
        $.getJSON("../adminDescargaPluPrecio/config_admiDesPluprecio.php", send, function(datos) {
            $("#divtabla_plusMaxp").hide();
            $("#tituloSG").show();
            $("#tablaplus").show();
            html="<thead><tr class='active'> ";
            html+="<th style='text-align:center'>C&oacute;digo</th>";
            html+="<th style='text-align:center' >N&uacute;mero</th>";
            html+="<th style='text-align:center'>Descripci&oacute;n</th>";
            html+="<th style='text-align:center'>Canal de Venta</th>";
            html+="</tr></thead>";
            $("#tablaplus").empty();
            if(datos.str>0){
                $("#btcarga").show();
                for(i=0; i<datos.str; i++) {
                    html+="<tr><td style='text-align: center; width:300px'>"+datos[i]['plu_codigo']+"</td>";
                    html+="<td style='text-align: center; width:200px'>"+datos[i]['plu_num']+"</td>";
                    html+="<td style='text-align: center; width:200px'>"+datos[i]['plu_descripcion']+"</td>";
                    html+="<td style='text-align: center; width:200px'>"+datos[i]['plu_canal']+"</td></tr>";
                    $("#tablaplus").html(html);
                    $("#tablaplus").dataTable(
                        {
                            'destroy': true
                        }
                    );
                    $("#tablaplus_length").hide();
                    $("#tablaplus_paginate").addClass('col-xs-10');
                    $("#tablaplus_info").addClass('col-xs-10');
                    $("#tablaplus_length").addClass('col-xs-6');
                }

            }else{

                html+="<tr>";
                html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
                $("#tablaplus").html(html);
                $("#divtabla_plusMaxp").hide();
                $("#btcarga").hide();
                $('#cargando').hide();
            }
        });

    }
}


function fn_cargarDatosMaxpoint(){
    $("#modal_Confirm").hide();
    $('#cargando').show();
    var plu=$("#txnumplu").val();
    send = {"cargarPluMaxp": 1};
    send.plu=plu;
    $.getJSON("../adminDescargaPluPrecio/config_admiDesPluprecio.php", send, function(datos) {
        if(datos.str>0) {
            alertify.success('El Plu : '+plu+' se ha cargado correctamente');
            $("#divtabla_plusMaxp").show();
        }else{
            alertify.error('Error al cargar el Plu: '+plu);
        }
    });

}

function fn_descargaPreciosPlu(){

    $("#modal_Confirm1").hide();
    $('#cargando').show();
    var plu=$("#txnumpluprecios").val();
    send = {"cargarPluPrecioMaxp": 1};
    send.plu=plu;
    $.getJSON("../adminDescargaPluPrecio/config_admiDesPluprecio.php", send, function(datos) {
        if(datos.str>0) {

            alertify.success('Los Precios del Plu: '+plu+' se han cargado correctamente');
            $("#divtabla_plusMaxprecio").show();

        }else{
            alertify.error('Error al cargar los precios del Plu :' +plu);
        }
    });
}

function fn_CargarTablaMaxp(){

    var plu=$("#txnumplu").val();
    send = {"CargarTablaMaxPlus": 1};
    send.plu=plu;
    $.getJSON("../adminDescargaPluPrecio/config_admiDesPluprecio.php", send, function(datos) {

        $("#divtabla_plusMaxprecio").hide();
        $("#tituloMax").show();
        $("#tablaplusmax").show();
        html="<thead><tr class='active'> ";
        html+="<th style='text-align:center'>C&oacute;digo</th>";
        html+="<th style='text-align:center' >N&uacute;mero</th>";
        html+="<th style='text-align:center'>Descripci&oacute;n</th>";
        html+="<th style='text-align:center'>Canal de Venta</th>";
        html+="</tr></thead>";
        $("#tablaplusmax").empty();
        if(datos.str>0){
            $("#bt_cargar").show();
            for(i=0; i<datos.str; i++) {
                html+="<td style='text-align: center; width:500px'>"+datos[i]['plu_codigo']+"</td>";
                html+="<td style='text-align: center; width:200px'>"+datos[i]['plu_num']+"</td>";
                html+="<td style='text-align: center; width:200px'>"+datos[i]['plu_descripcion']+"</td>";
                html+="<td style='text-align: center; width:800px'>"+datos[i]['plu_canal']+"</td></tr>";
                $("#tablaplusmax").html(html);
                $("#tablaplusmax").dataTable(
                    {
                        'destroy': true
                    }
                );
                $("#tablaplusmax_length").hide();
                $("#tablaplusmax_paginate").addClass('col-xs-10');
                $("#tablaplusmax_info").addClass('col-xs-10');
                $("#tablaplusmax_length").addClass('col-xs-6');
            }

        }else{
            html+="<tr>";
            html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
            $("#tablaplusmax").html(html);


        }
    });
    $('#cargando').hide();
}


function verificarPreciosCero() {
    var table = document.getElementById("tablaplusprecios");
    var x = document.getElementById("tablaplusprecios").rows.length;
    for (var i = 1, row; row = table.rows[i]; i++) {
        if (i < (x - 1)) {
            for (var j = 0, col; col = row.cells[j]; j++) {
                if (j == 5) {
                    if (row.cells[j].innerText == 0) {
                        return 1;
                    }
                }
            }
        }
    }
}
function verificarPrecio() {
    if (verificarPreciosCero()==1) {

        alertify.confirm("Existe Productos con Precios de Cero,Desea Descargalos?",
            function(e){
                if(e){
                    fn_descargaPreciosPlu();
                    fn_CargarTablaMaxpPrecios();
                } else {
                    fn_cerrarModals();
                }
            });
    } else {
        fn_descargaPreciosPlu();
        fn_CargarTablaMaxpPrecios();
    }
}



