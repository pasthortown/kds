/*global alertify*/
/*global bootbox*/

var preciosNuevos = new Array();
var codigosReporte = new Array();
var preciosAntiguos = new Array();
var indice = '-3';
var bandera = -1;
var idscategorias = new Array;

$(document).ready(function () {
    fn_cargaProgramaciones();
    $("#inicio_categorias").hide();
    $("#tab_principal").show();
    $("#div_detalle_categorias").hide();
    $("#div_detalle_precios").hide();
    $("#div_detalle_programaciones").hide();
    fn_muestraDetalleCategorias();
    fn_cargarCanales();
});

function fn_cargarCanales() {
    var send;
    var cargarCanales = {"cargarCanales": 1};
    send = cargarCanales;
    $.getJSON("../adminPrecios/config_adminPrecios.php", send, function (datos) {
        if (datos.str > 0) {
            //todos='Todos';
            var i;
            for (i = 0; i < datos.str; i++) {
                html = "<div class='checkbox checkbox-primary checkbox-inline'><input id='check_" + datos[i]['cla_nombre'] + "' name='canales[]' class='canales ' type='checkbox' value='" + datos[i]['cla_id'] + "' onclick=fn_Modificar3('" + datos[i]['cla_nombre'] + "') /><label for='check_" + datos[i]['cla_nombre'] + "'>" + datos[i]['cla_nombre'] + "</label></div>";
                //html+="<span>Los</span";
                $("#opcionesDeCanal").append(html);
            }
        }
    });
}

function fn_muestraDetalleCategorias() {
    fn_cargando(1);
    var html = "<thead><tr class='active'><th class='text-center' width='25%'>ABREVIATURA CATEGOR&Iacute;A</th><th class='text-center' width='50%'>DESCRIPCI&Oacute;N CATEGOR&Iacute;A</th><th class='text-center'>Seleccione</th></tr></thead>";
    send = {"cargarDetalleCategorias": 1};
    send.cadenaDetalle = $("#cdn_id").val();
    $.getJSON("../adminPrecios/config_adminPrecios.php", send, function (datos) {
        if (datos.str > 0) {
            fn_cargando(0);
            $("#tabla_detalle_categorias").empty();
            $("#div_detalle_categorias").show();

            for (i = 0; i < datos.str; i++) {
                html += "<tr id='" + i + "' style='cursor:pointer;'";
                html += " onclick='fn_seleccion(" + i + ",\"" + datos[i]['cat_id'] + "\")'>";
                html += "<td style='text-align:left'>" + datos[i]['cat_abreviatura'] + "&nbsp;</td>";
                html += "<td style='text-align:left'>" + datos[i]['cat_descripcion'] + "&nbsp;</td>";
                html += "<td align='center'><div class='checkbox checkbox-primary'><input name='orderBox[]' id='c" + i + "' type='checkbox' abr='" + datos[i]['cat_descripcion'] + "' value='" + datos[i]['cat_id'] + "' /><label for='c" + i + "'></label></div></td></tr>";
            }
            $("#tabla_detalle_categorias").html(html);
        } else {
            fn_cargando(0);
        }
    });
}

function checkTodos(id, pID) {
    $("#" + pID + " :checkbox").attr('checked', $('#' + id).is(':checked'));
}

function fn_seleccion(fila, id_categoria) {
    $("#tabla_detalle_categorias tr").removeClass("success");
    $("#" + fila + "").addClass("success");
    $("#cat_id").val(id_categoria);
}

function fn_Modificar(opcion) {
    fn_cargando(1);
    var checkboxValues = "";
    var checkboxNames = new Array();
    var categorias = new Array();
    var canalesArray = "";
    $('.canales').each(function () {
        canalesArray += $(this).val() + ",";
    });
    $('input[name="orderBox[]"]:checked').each(function () {
        checkboxValues += $(this).val() + ",";
        idscategorias.push($(this).val());
        checkboxNames.push($(this).attr('abr'));
        categorias.push($(this).val());
    });
    if (checkboxValues.length == 0) {
        //bandera=0;
        alertify.error("Debe seleccionar al menos una Categor&iacute;a. Regrese al paso (1)Categor&iacute;a(s)");
        return false;
    }
    checkboxValues = checkboxValues.substring(0, checkboxValues.length - 1);
    canalesArray = canalesArray.substring(0, canalesArray.length - 1);

    //if(opcion=='Todos')
    //{
    //alert("Categorias: "+checkboxValues);
    //alert("Canales: "+canalesArray);
    send = {"cargaPreciosCategorias": 1};
    send.cdnId = $("#cdn_id").val();
    send.idCategorias = checkboxValues;
    send.opcionCanal = opcion;
    send.idCanales = canalesArray;
    $.getJSON("../adminPrecios/config_adminPrecios.php", send, function (datos) {
        if (datos.str > 0) {
            $('.canales').each(function () {
                $(this).prop("checked", true);
            });
            checkboxNames.sort();
            fn_cargando(0);
            $("#div_detalle_precios").show();
            $("#tabla_detalle_precios").empty();

            var html = "<thead>";

            html += "<tr><td>&nbsp;</td><td><div class='input-prepend input-group'><span class='add-on input-group-addon'><i class='glyphicon glyphicon-search'></i></span><input type='text' id='buscarPrecios' class='form-control' /></div></td>";
            for (i = 0; i < checkboxNames.length; i++) {
                id = datos[i]['cat_id'];
                html += "<td align='center' style='width:10%'><button class='btn btn-primary' onclick='fn_traerprecios(" + (i + 2) + ",\"" + id + "\");' >TRAER</button></td>";
            }
            html += "</tr>";
            html += "<tr class='active'><th class='text-center active' width='10%'>#PLU</th><td class='text-center active' width='40%'>PRODUCTO</td>";
            for (i = 0; i < checkboxNames.length; i++) {
                html += "<td class='text-center active'>" + checkboxNames[i] + "&nbsp;</td>";
            }
            html += "</thead>";
            //$("#tabla_detalle_precios").html(html);							

            numero = 0;
            inicial = 0;
            for (i = 0; i < datos.str; i++) {
                //creo los inputs necesarios donde se van a concatenar los datos.
                if ($("#hid_cat" + datos[i]['cat_id']).length == 0) {
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'hid_cat' + datos[i]['cat_id'],
                        class: 'mostrados'
                    }).appendTo('body');
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'hid_acat' + datos[i]['cat_id'],
                        class: 'nomostrados'
                    }).appendTo('body');
                } else {
                    $("#hid_cat" + datos[i]['cat_id']).val('');
                    $("#hid_acat" + datos[i]['cat_id']).val('');
                }

                //////////////////////////////////////////////////////////////

                if (numero != datos[i]['plu_num_plu']) {
                    if (inicial != 0) {
                        html += "</tr>";
                    }
                    numero = datos[i]['plu_num_plu'];
                    html += "<tr id='" + i + "' style='cursor:pointer;'>";
                    html += "<td style='text-align:left; width:200px'>" + datos[i]['plu_num_plu'] + "&nbsp;</td>";
                    html += "<td style='text-align:left; width:200px'>" + datos[i]['plu_descripcion'] + "&nbsp;</td>";
                    html += "<td style='text-align:left'><input class='visible form-control' id='pvp_" + i + "' onchange='fn_cambiarPrecio(" + i + ",\"" + datos[i]['cat_id'] + "\"," + datos[i]['plu_id'] + ");' onKeyPress='return justNumbers(event);' type='text' value='" + datos[i]['pr_pvp'] + "' /><input id='hpvp_" + i + "' class='invisible' type='hidden' value='" + datos[i]['pr_pvp'] + "' />&nbsp;</td>";
                    inicial = 1;
                } else {
                    html += "<td style='text-align:left'><input class='visible form-control' id='pvp_" + i + "'  onchange='fn_cambiarPrecio(" + i + ",\"" + datos[i]['cat_id'] + "\"," + datos[i]['plu_id'] + ");' onKeyPress='return justNumbers(event);' type='text' value='" + datos[i]['pr_pvp'] + "' /><input id='hpvp_" + i + "' class='invisible' type='hidden' value='" + datos[i]['pr_pvp'] + "' /></td>";
                }
            }
            $("#tabla_detalle_precios").html(html);

            $("#buscarPrecios").focus();

            //goheadfixed('table.fixed');
            tabla = $("#tabla_detalle_precios");
            $("#buscarPrecios").keyup(function () {
                $.uiTableFilter(tabla, this.value);
            });
        } else {
            fn_cargando(0);
            html += "<tr><td style='text-align:center'>No existen datos.</td></tr>";
            $("#tabla_detalle_precios").html(html);
        }
    });
}

function fn_Modificar2() {
    fn_cargando(1);
    $("#div_detalle_precios").modal("show");
    var checkboxValues = "";
    var checkboxNames = new Array();
    var categorias = new Array();
    $('input[name="orderBox[]"]:checked').each(function () {
        checkboxValues += $(this).val() + ",";
        idscategorias.push($(this).val());
        checkboxNames.push($(this).attr('abr'));
        categorias.push($(this).val());
    });
    if (checkboxValues.length == 0) {
        alertify.error("Debe seleccionar al menos una Categor&iacute;a. Regrese al paso (1)Categor&iacute;a(s)");
        return false;
    }
    checkboxValues = checkboxValues.substring(0, checkboxValues.length - 1);

    send = {"cargaPreciosCategoriasPorMasterPlu": 1};
    send.cdnId = $("#cdn_id").val();
    send.idCategorias = checkboxValues;
    $.getJSON("../adminPrecios/config_adminPrecios.php", send, function (datos) {
        if (datos.str > 0) {
            fn_cargando(0);
            $("#div_detalle_precios").show();
            $("#tabla_detalle_precios").empty();
            checkboxNames.sort();
            var html = "<thead>";

            html += "<tr><td>&nbsp;</td><td><div class='input-prepend input-group'><span class='add-on input-group-addon'><i class='glyphicon glyphicon-search'></i></span><input type='text' id='buscarPrecios' class='form-control' /></div></td>";
            for (i = 0; i < checkboxNames.length; i++) {
                id = +datos[i]['cat_id'];
                html += "<td align='center' style='width:10%'><button class='btn btn-primary' onclick='fn_traerprecios(" + (i + 2) + ",\"" + id + "\");' >TRAER</button></td>";
            }
            html += "</tr>";
            html += "<tr class='active'><th class='text-center active' width='10%'>#PLU</th><th class='text-center active' width='40%'>PRODUCTO</th>";
            for (i = 0; i < checkboxNames.length; i++) {
                html += "<th class='text-center active'>" + checkboxNames[i] + "&nbsp;</th>";
            }
            html += "</thead>";
            $("#tabla_detalle_precios").html(html);
            numero = 0;
            inicial = 0;
            for (i = 0; i < datos.str; i++) {
                //creo los inputs necesarios donde se van a concatenar los datos.
                if ($("#hid_cat" + datos[i]['cat_id']).length == 0) {
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'hid_cat' + datos[i]['cat_id'],
                        class: 'mostrados'
                    }).appendTo('body');
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'hid_acat' + datos[i]['cat_id'],
                        class: 'nomostrados'
                    }).appendTo('body');
                } else {
                    $("#hid_cat" + datos[i]['cat_id']).val('');
                    $("#hid_acat" + datos[i]['cat_id']).val('');
                }
                //////////////////////////////////////////////////////////////

                //'dato_'+'datos[i]['cat_id']'=new Array();
                if (numero != datos[i]['plu_num_plu']) {
                    if (inicial != 0) {
                        html += "</tr>";
                    }
                    numero = datos[i]['plu_num_plu'];
                    html += "<tr id='" + i + "' style='cursor:pointer;'>";
                    html += "<td style='text-align:left'>" + datos[i]['plu_descripcion'] + "&nbsp;</td>";
                    html += "<td style='text-align:left'><input class='visible form-control' id='pvp_" + i + "' onchange='fn_cambiarPrecio(" + i + ",\"" + datos[i]['cat_id'] + "\"," + datos[i]['plu_id'] + ");' onKeyPress='return justNumbers(event);' type='text' value='" + datos[i]['pr_pvp'] + "' /><input id='hpvp_" + i + "' class='invisible' type='hidden' value='" + datos[i]['pr_pvp'] + "' />&nbsp;</td>";
                    inicial = 1;
                } else {
                    html += "<td style='text-align:left'><input class='visible form-control' id='pvp_" + i + "'  onchange='fn_cambiarPrecio(" + i + ",\"" + datos[i]['cat_id'] + "\"," + datos[i]['plu_id'] + ");' onKeyPress='return justNumbers(event);' type='text' value='" + datos[i]['pr_pvp'] + "' /><input id='hpvp_" + i + "' class='invisible' type='hidden' value='" + datos[i]['pr_pvp'] + "' /></td>";
                }
            }
            $("#tabla_detalle_precios").html(html);

            $("#buscarPrecios").focus();
            tabla = $("#tabla_detalle_precios");
            $("#buscarPrecios").keyup(function () {
                $.uiTableFilter(tabla, this.value);
            });
        } else {
            fn_cargando(0);
            html += "<tr><td style='text-align:center'>No existen datos.</td></tr>";
            $("#tabla_detalle_precios").html(html);
        }
    });
}

function fn_Modificar3(tipoCanal) {
    var canalesArray = "";
    $('input[name="canales[]"]:checked').each(function () {
        canalesArray += $(this).val() + ",";
    });
    //console.log(canalesArray);
    if ($('#option2').hasClass('btn-primary')) {
        return false;
    } else {
        $("#div_detalle_precios").modal("show");
        var checkboxValues = "";
        var checkboxNames = new Array();
        var categorias = new Array();
        $('input[name="orderBox[]"]:checked').each(function () {
            checkboxValues += $(this).val() + ",";
            idscategorias.push($(this).val());
            checkboxNames.push($(this).attr('abr'));
            categorias.push($(this).val());
        });
        if (checkboxValues.length == 0) {
            alertify.error("Debe seleccionar al menos una Categor&iacute;a. Regrese al paso (1)Categor&iacute;a(s)");
            return false;
        }
        checkboxValues = checkboxValues.substring(0, checkboxValues.length - 1);
        canalesArray = canalesArray.substring(0, canalesArray.length - 1);
        if (canalesArray.length == 0) {
            alertify.error("Debe seleccionar al menos un Canal.");
            $("#check_" + tipoCanal).prop('checked', true);
            return false;
        }
        fn_cargando(1);
        send = {"cargaPreciosCategorias": 1};
        send.cdnId = $("#cdn_id").val();
        send.idCategorias = checkboxValues;
        send.opcionCanal = $("#hid_canalBanda").val();
        send.idCanales = canalesArray;
        /*
         send.cdnId=$("#cdn_id").val();
         send.idCategorias=checkboxValues;
         send.opcionCanal=opcion;
         */
        $.getJSON("../adminPrecios/config_adminPrecios.php", send, function (datos) {
            if (datos.str > 0) {
                fn_cargando(0);
                $("#div_detalle_precios").show();
                $("#tabla_detalle_precios").empty();
                checkboxNames.sort();
                var html = "<thead>";

                html += "<tr><td>&nbsp;</td><td><div class='input-prepend input-group'><span class='add-on input-group-addon'><i class='glyphicon glyphicon-search'></i></span><input type='text' id='buscarPrecios' class='form-control' /></div></td>";
                for (i = 0; i < checkboxNames.length; i++) {
                    id = datos[i]['cat_id'];
                    html += "<td align='center' style='width:10%'><button class='btn btn-primary' onclick='fn_traerprecios(" + (i + 2) + "," + id + ");' >TRAER</button></td>";
                }
                html += "</tr>";
                html += "<tr class='active'><th class='text-center active' width='10%'>#PLU</th><th class='text-center active' width='40%'>PRODUCTO</th>";
                for (i = 0; i < checkboxNames.length; i++) {

                    html += "<th class='text-center active'>" + checkboxNames[i] + "&nbsp;</th>";
                }
                html += "</thead>";
                $("#tabla_detalle_precios").html(html);
                numero = 0;
                inicial = 0;
                for (i = 0; i < datos.str; i++) {
                    //creo los inputs necesarios donde se van a concatenar los datos.
                    if ($("#hid_cat" + datos[i]['cat_id']).length == 0) {
                        $('<input>').attr({
                            type: 'hidden',
                            id: 'hid_cat' + datos[i]['cat_id'],
                            class: 'mostrados'
                        }).appendTo('body');
                        $('<input>').attr({
                            type: 'hidden',
                            id: 'hid_acat' + datos[i]['cat_id'],
                            class: 'nomostrados'
                        }).appendTo('body');
                    } else {
                        $("#hid_cat" + datos[i]['cat_id']).val('');
                        $("#hid_acat" + datos[i]['cat_id']).val('');
                    }
                    //////////////////////////////////////////////////////////////

                    //'dato_'+'datos[i]['cat_id']'=new Array();
                    if (numero != datos[i]['plu_num_plu']) {
                        if (inicial != 0) {
                            html += "</tr>";
                        }
                        numero = datos[i]['plu_num_plu'];
                        html += "<tr id='" + i + "' style='cursor:pointer;'>";
                        html += "<td style='text-align:left'>" + datos[i]['plu_descripcion'] + "&nbsp;</td>";
                        html += "<td style='text-align:left'><input class='visible form-control' id='pvp_" + i + "' onchange='fn_cambiarPrecio(" + i + ",\"" + datos[i]['cat_id'] + "\"," + datos[i]['plu_id'] + ");' onKeyPress='return justNumbers(event);' type='text' value='" + datos[i]['pr_pvp'] + "' /><input id='hpvp_" + i + "' class='invisible' type='hidden' value='" + datos[i]['pr_pvp'] + "' />&nbsp;</td>";
                        inicial = 1;
                    } else {
                        html += "<td style='text-align:left'><input class='visible form-control' id='pvp_" + i + "'  onchange='fn_cambiarPrecio(" + i + ",\"" + datos[i]['cat_id'] + "\"," + datos[i]['plu_id'] + ");' onKeyPress='return justNumbers(event);' type='text' value='" + datos[i]['pr_pvp'] + "' /><input id='hpvp_" + i + "' class='invisible' type='hidden' value='" + datos[i]['pr_pvp'] + "' /></td>";
                    }
                }
                $("#tabla_detalle_precios").html(html);

                $("#buscarPrecios").focus();
                tabla = $("#tabla_detalle_precios");
                $("#buscarPrecios").keyup(function () {
                    $.uiTableFilter(tabla, this.value);
                });

            } else {
                fn_cargando(0);
                html += "<tr><td style='text-align:center'>No existen datos.</td></tr>";
                $("#tabla_detalle_precios").html(html);
            }
        });
    }
}


function fn_traerprecios(indice, categoria) {
    $("#hid_indice").val(indice);
    $("#hid_categoriaId").val(categoria);
    send = {"cargaCategoriasTraerPrecios": 1};
    send.cdnIdTraePrecios = $("#cdn_id").val();
    $.getJSON("../adminPrecios/config_adminPrecios.php", send, function (datos) {
        $('#selCategorias').empty();
        if (datos.str > 0) {
            $('#selCategorias').append("<option selected value='0'>---Seleccione Categoria---</option>");
            for (i = 0; i < datos.str; i++) {
                html = "<option value='" + datos[i]['cat_id'] + "'>" + datos[i]['cat_descripcion'] + "</option>";
                $("#selCategorias").append(html);
            }
            $("#mdl_traerCategorias").modal("show");
        }
    });
}

function fn_aceptaModalCategoria() {
    if ($("#selCategorias").val() == 0) {
        alertify.error("Seleccione una categor&iacute;a");
        return false;
    }
    fn_cargando(1);
    $("#mdl_traerCategorias").modal("hide");
    indicee = parseInt($("#hid_indice").val()) + 1;
    var arrayPreciosImportados = new Array();
    send = {"traerPreciosUnaCategoria": 1};
    send.catTraePrecios = $("#selCategorias").val();
    $.getJSON("../adminPrecios/config_adminPrecios.php", send, function (datos) {
        //$('#selCategorias').empty();
        if (datos.str > 0) {
            fn_cargando(0);
            j = 0;
            //h=0;
            preciosNuevos.length = 0;
            for (i = 0; i < datos.str; i++) {
                arrayPreciosImportados.push(datos[i]['pr_pvp']);
                //concatenamos los datos en input//////////////////////////////////////////////////////////////////////////////////////
                idcate = $("#hid_categoriaId").val();
                valorActual = $("#hid_cat" + idcate).val();
                $("#hid_cat" + idcate).val(valorActual + datos[i]['pr_pvp'] + "*" + datos[i]['plu_id'] + "*" + $("#hid_categoriaId").val() + "*" + ',');
                valorAntiguo = $("#hid_acat" + idcate).val();
                $("#hid_acat" + idcate).val(valorAntiguo + $("#hpvp_" + i).val() + "*" + datos[i]['plu_id'] + "*" + $("#hid_categoriaId").val() + "*" + ',');
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
                preciosNuevos.push(datos[i]['pr_pvp'] + "*" + datos[i]['plu_id'] + "*" + $("#hid_categoriaId").val());
                preciosAntiguos.push($("#hpvp_" + i).val() + "*" + datos[i]['plu_id'] + "*" + $("#hid_categoriaId").val() + "*");
                //$("#tabla_detalle_precios tbody tr:eq("+j+") td:nth-child("+(indicee)+") input.visible").val(contenido).css("background-color", "yellow" );
            }
            $.each(arrayPreciosImportados, function (index, contenido) {
                //console.log(contenido);												   
                //$("#tabla_detalle_precios tbody tr td:nth-child("+(indicee)+") input.visible").each(function () 
                //{
                //console.log(contenido+" 2");	
                //alert(indicee);
                $("#tabla_detalle_precios tbody tr:eq(" + j + ") td:nth-child(" + (indicee) + ") input.visible").val(contenido).css("background-color", "yellow");

                //});
                j++;
            });
        } else {
            fn_cargando(0);
        }
    });
}

function fn_cambiarPrecio(indice, cat_id, plu_id) {
    if (isNaN($("#pvp_" + indice).val()) || $("#pvp_" + indice).val() == "")
    {
        alertify.error("Digita un número Correcto.");
        $("#pvp_" + indice).css("background-color", "#ffb6c1")/*.addClass("noValido")*/;
    } else {
        //$("#pvp_"+indice).removeClass("noValido");    
        valorActual = $("#hid_cat" + cat_id).val();
        $("#hid_cat" + cat_id).val(valorActual + $("#pvp_" + indice).val() + "*" + plu_id + "*" + cat_id + "*" + ',');
        valorAntiguo = $("#hid_acat" + cat_id).val();
        $("#hid_acat" + cat_id).val(valorAntiguo + $("#hpvp_" + indice).val() + "*" + plu_id + "*" + cat_id + "*" + ',');
        preciosNuevos.push($("#pvp_" + indice).val() + "*" + plu_id + "*" + cat_id + "*");
        $("#pvp_" + indice).css("background-color", "yellow");
        preciosAntiguos.push($("#hpvp_" + indice).val() + "*" + plu_id + "*" + cat_id + "*");
    }
}


function fn_grabaCadenaPrecios() {

    indicadorCanal = $("#hid_canalBanda").val();
    if ($("#FechaInicial").val() == '') {
        alertify.error("Ingrese la fecha en la que se va a aplicar los precios.");
        return false;
    }
    if ($('#timepicker1').val() == '') {
        alertify.error("Ingrese la hora en la que se va a aplicar los precios.");
        return false;
    }
    canalesArray = '';
    $('input[name="canales[]"]:checked').each(function () {
        canalesArray += $(this).val() + ",";
    });
    canalesArray = canalesArray.substring(0, canalesArray.length - 1);

    if (preciosNuevos.length > 0) {
        bootbox.setLocale('es');
        bootbox.confirm("Est&aacute; seguro de que los precios ingresados son los correctos?", function (result) {
            //Example.show("Confirm result: "+result);
            if (result) {
                j = 0;
                $(".mostrados").each(function () {
                    id = $(this).attr('id');
                    //alert(id);
                    var categoId = id;
                    categoId = categoId.replace("hid_cat", " ");
                    //alert($("#"+id).val());
                    if ($("#" + id).val() != '') {
                        j++;
                        send = {"grabaCadenaPrecios": 1};
                        send.cadenaPrecios = $("#" + id).val();//preciosNuevos.toString();
                        send.cadenaPreciosAntiguos = preciosAntiguos.toString();
                        send.fechaAplicacion = $("#FechaInicial").val();
                        send.horaAplicacion = $("#timepicker1").val();
                        send.categoria = categoId.trim();
                        send.opcion = 'I';
                        send.canalIndica = indicadorCanal;
                        send.idOpciondecanal = canalesArray;
                        $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminPrecios/config_adminPrecios.php", data: send,
                            success: function (datos) {
                                codigosReporte.push(datos.prp_id);
                            }
                        });
                    }
                });//FIN each mostrados
                /*$(".nomostrados").each(function () 
                 {								
                 id=$(this).attr('id');
                 var categoId = id;
                 categoId = categoId.replace("hid_acat"," ");																
                 if($("#"+id).val()!='')
                 {									
                 send={"grabaCadenaPrecios":1};
                 send.cadenaPrecios=preciosNuevos.toString();
                 send.cadenaPreciosAntiguos=$("#"+id).val();//preciosAntiguos.toString();
                 send.fechaAplicacion=$("#FechaInicial").val();		
                 send.horaAplicacion=$("#timepicker1").val();		
                 send.categoria=categoId.trim();
                 send.opcion='U';
                 send.canalIndica=indicadorCanal;
                 send.idOpciondecanal=canalesArray;
                 $.ajax({async:false,type:"POST",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"../adminPrecios/config_adminPrecios.php",data:send,
                 success:function(datos)
                 {					
                 
                 }
                 });
                 }
                 });*/

                //console.log(codigosReporte);
                fn_validaReporte(2);

                preciosNuevos.length = 0;
                canalesArray.length = 0;
                alertify.success("Guardado Correctamente");
                for (i = 0; i < idscategorias.length; i++) {
                    $("#hid_cat" + idscategorias[i]).remove();
                    $("#hid_acat" + idscategorias[i]).remove();
                }

                idscategorias.length = 0;
                //alert($("#hid_codigosProgramacion").val());

                fn_cargaProgramaciones();
                $('input[name="orderBox[]"]:checked').each(function ()
                {
                    $(this).prop("checked", false);
                });
                $("#div_detalle_precios").modal("hide");
                $("#hid_canalBanda").val('Canal');
                //$("#option1").prop('checked',true);
                $("#option1").removeClass('btn-default');
                $("#option1").addClass('btn-primary');
                $("#option2").removeClass('btn-primary');
                $("#option2").addClass('btn-default');
                //$("#option2").prop('checked',false);
                //
                $('#rootwizard').find("a[href*='tab1']").tab('show');
            }
        });
    } else {
        $('input[name="orderBox[]"]:checked').each(function () {
            $(this).prop("checked", false);
        });
        preciosNuevos.length = 0;
        $("#div_detalle_precios").modal("hide");
    }
}

function fn_validaReporte(indicador) {
    //console.log("indicador "+indicador+" longitudCodigos: "+codigosReporte.length)
    //lenReporte=codigosReporte.length;
    //alert("indicador:"+indicador+" longitud:"+lenReporte);
    //if(indicador==lenReporte)
    //{
    //alert("A enviar "+codigosReporte.toString());
    fn_cargando(1);
    var html3 = "<tr><td>Fecha de Aplicaci&oacute;n: " + $("#FechaInicial").val() + " " + $('#timepicker1').val() + "</td>";
    $("#dateAplicacion").html(html3);

    var html = "<thead style='width:100%'><tr class='active'><th class='text-center'>NUM PLU</th><th class='text-center'>PLU DESCRIPCI&Oacute;N</th><th class='text-center'>PRECIO ACTUAL</th><th class='text-center'>PRECIO NUEVO</th><th class='text-center'>CATEGOR&Iacute;A</th></tr></thead>";
    send = {"detalleReportePrecios": 1};
    send.valoresReporte = codigosReporte.toString();
    $.getJSON("../adminPrecios/config_adminPrecios.php", send, function (datos) {
        //$.ajax({async:true,type:"POST",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"../adminPrecios/config_adminPrecios.php",data:send,success:function(datos)			
        if (datos.str > 0) {
            fn_cargando(0);
            $("#detalle_reporte").empty();
            for (i = 0; i < datos.str; i++) {
                html += "<tr id='" + i + "' style='cursor:pointer;'>";
                html += "<td style='text-align:left'>" + datos[i]['num_plu'] + "&nbsp;</td>";
                html += "<td style='text-align:left'>" + datos[i]['plu_descripcion'] + "&nbsp;</td>";
                html += "<td style='text-align:left'>" + datos[i]['precio_actual'] + "&nbsp;</td>";
                html += "<td style='text-align:left'>" + datos[i]['precio_nuevo'] + "&nbsp;</td>";
                html += "<td style='text-align:left'>" + datos[i]['categoria'] + "&nbsp;</td></tr>";
            }
            $("#detalle_reporte").html(html);
            codigosReporte.length = 0;
        } else {
            fn_cargando(0);
        }
        //codigosReporte.length=0;
    });
    $("#modal_reporte").modal("show");
}


function fn_cerrarModal() {
    $("#div_detalle_precios").modal("hide");
    $('input[name="orderBox[]"]:checked').each(function () {
        $(this).prop("checked", false);
    });
}

function fn_cargaProgramaciones() {
    fn_cargando(1);
    var html = "<thead><tr class='active'><th class='text-center' width='30%'>PROGRAMADO POR</th><th class='text-center' width='25%'>FECHA CREACI&Oacute;N</th><th class='text-center' width='25%'>CATEGOR&Iacute;A</th><th class='text-center' width='25%'>FECHA APLICACI&Oacute;N</th><th></th></tr></thead>";

    send = {"cargaProgramaciones": 1};
    $.getJSON("../adminPrecios/config_adminPrecios.php", send, function (datos) {
        $("#div_detalle_programaciones").show();
        if (datos.str > 0) {
            fn_cargando(0);
            $("#tabla_detalle_programaciones").empty();
            for (i = 0; i < datos.str; i++) {
                des = datos[i]['cat_descripcion'];
                //alert(des);
                html += "<tr id='" + i + "' ondblclick='fn_preview(\"" + datos[i]['prp_id'] + "\",des)'";
                if (datos[i]['std_id'] == 0) {
                    html += "class='danger'>";
                } else {
                    html += ">";
                }
                html += "<td class='text-center'>" + datos[i]['usr_descripcion'] + "&nbsp;</td>";
                html += "<td class='text-center'>" + datos[i]['fechaCreacion'] + "&nbsp;" + datos[i]['horaCreacion'] + "</td>";
                html += "<td class='text-center'>" + datos[i]['cat_descripcion'] + "&nbsp;</td>";
                html += "<td class='text-center'>" + datos[i]['fecha'] + "&nbsp;" + datos[i]['hora'] + "</td>";
                html += "<td align='center'>";
                //alert(datos[i]['imagenEstado']);
                if (datos[i]['imagenEstado'] == 'eliminar') {
                    html += "<input type='button' class='opcionAgregado' style='height:33px; width:33px;  background: rgb(102, 102, 102) url(../../imagenes/admin_resources/btn_eliminar.png) no-repeat scroll 1px 1px;' onclick='fn_cancelarProgramacion(\"" + datos[i]['prp_id'] + "\");'/>";
                }
                if (datos[i]['imagenEstado'] == 'aplicado si') {
                    html += "<input type='button' class='opcionAgregado' style='height:33px; width:33px;  background: rgb(102, 102, 102) url(../../imagenes/admin_resources/btn_aceptar.png) no-repeat scroll 1px 1px;'/>";
                }

                html += "</td>";
                html += "</tr>";
                /*$("#tabla_detalle_programaciones").html(html);								
                 $("#tabla_detalle_programaciones").dataTable(
                 {						  
                 'destroy': true,					  
                 'fixedHeader': true,
                 "sDom": 'T<"clear">lfrtip',
                 "oTableTools": {
                 "aButtons": [
                 "print"										
                 ]
                 }
                 } 
                 );
                 $("#tabla_detalle_programaciones_length").hide();
                 $("#tabla_detalle_programaciones_paginate").addClass('col-xs-10');
                 $("#tabla_detalle_programaciones_info").addClass('col-xs-10');*/
                //$("#tabla_detalle_programaciones_length").addClass('col-xs-6');
            }
            $("#tabla_detalle_programaciones").html(html);
            $("#tabla_detalle_programaciones").dataTable({
                'destroy': true,
                'fixedHeader': true,
                "sDom": 'T<"clear">lfrtip',
                "oTableTools": {
                    "aButtons": ["print"]
                }
            });
            $("#tabla_detalle_programaciones_length").hide();
            $("#tabla_detalle_programaciones_paginate").addClass('col-xs-10');
            $("#tabla_detalle_programaciones_info").addClass('col-xs-10');
        } else {
            fn_cargando(0);
            html = "<tr><th colspan='4' style='text-align:center'>No existen datos.</th></tr>";
            $("#tabla_detalle_programaciones").html(html);
        }
    });

}

function fn_cancelarProgramacion(pro_id) {
    bootbox.setLocale('es');
    bootbox.confirm("Est&aacute; seguro(a) de querer cancelar &eacute;sta programaci&oacute;n de precios?", function (result) {
        if (result) {
            send = {"cancelaProgramacion": 1};
            send.programacionId = pro_id;
            $.getJSON("../adminPrecios/config_adminPrecios.php", send, function (datos) {
                if (datos.actualiza == 1) {
                    fn_cargaProgramaciones();
                } else {
                    alertify.error("No se puede cancelar esta programaci&oacute;n debido a que la fecha de aplicaci&oacute;n es menor a la fecha actual.");
                    return false;
                }
            });
        }
    });
}


function fn_bandera(/*id*/) {
    fn_cargaProgramaciones();
}

function fn_wizard() {
    $('#rootwizard').bootstrapWizard({onTabClick: function (tab, navigation, index) {
            return false;
        },
        onTabShow: function (tab, navigation, index) {
            var $total = navigation.find('li').length;
            var $current = index + 1;
            var $percent = ($current / $total) * 100;
            $('#rootwizard').find('.bar').css({width: $percent + '%'});
            if ($current >= $total) {
                $('#rootwizard').find('.pager .next').hide();
                $('#rootwizard').find('.pager .finish').show();
                $('#rootwizard').find('.pager .finish').removeClass('disabled');
            } else {
                $('#rootwizard').find('.pager .next').show();
                $('#rootwizard').find('.pager .finish').hide();
            }
        },
        onNext: function (tab, navigation, index) {
            if (index == 1) {
                var checks = "";
                $('input[name="orderBox[]"]:checked').each(function () {
                    checks += $(this).val() + ",";
                });
                if (checks.length == 0) {
                    alertify.error("Debe seleccionar al menos una Categor&iacute;a.");
                    return false;
                }
                fn_Modificar('Canal');
            }
            if (index == 2) {
                //if($(".visible").hasClass("noValido")){ alertify.error("Tiene productos con precios(numeros) no validos. "); return false;}
                fn_fechaYhora();                   				
            }
        }
    });
}

function fn_fechaYhora() {
    $('#FechaInicial').daterangepicker({singleDatePicker: true, format: 'DD/MM/YYYY', minDate: moment(), drops: 'up'}, function (start, end, label) {
        //console.log(start.toISOString(), end.toISOString(), label);
    });
    $('#FechaInicial').val(moment().format('DD/MM/YYYY'));
    $('#timepicker1').timepicker();
}

function fn_validarAccion(valor, id) {
    //console.log("id"+id);
    if (valor == 'Master') {
        bootbox.setLocale('es');
        bootbox.confirm("Se perder&aacute; toda la informaci&oacute;n modificada. Desea Continuar", function (result) {
            if (result) {
                $("#hid_canalBanda").val(valor);
                //fn_Modificar2();
                fn_Modificar(valor);
                if (!$("#option2").hasClass('btn-primary')) {
                    $("#option2").addClass('btn-primary');
                    $("#option2").removeClass('btn-default');
                }
                if ($("#option1").hasClass('btn-primary')) {
                    $("#option1").removeClass('btn-primary');
                    $("#option1").addClass('btn-default');
                }
            }
        });
    } else {
        //$("#hid_canalBanda").val(valor);
        bootbox.setLocale('es');
        bootbox.confirm("Se perder&aacute; toda la informaci&oacute;n modificada. Desea Continuar", function (result) {
            if (result) {
                $("#hid_canalBanda").val(valor);
                //$("#hid_canalBanda").val('Todos');
                fn_Modificar(valor);
                if (!$("#option1").hasClass('btn-primary')) {
                    $("#option1").addClass('btn-primary');
                    $("#option1").removeClass('btn-default');
                }
                if ($("#option2").hasClass('btn-primary')) {
                    $("#option2").removeClass('btn-primary');
                    $("#option2").addClass('btn-default');
                }
            }
        });
    }
}

function justNumbers(e) {
    var keynum = window.event ? window.event.keyCode : e.which;
    if ((keynum == 8) || (keynum == 46))
        return true;

    return /\d/.test(String.fromCharCode(keynum));
}

function fn_preview(id, des) {
    $("#Labelpreview").text(des);
    var html = "<thead><tr class='active'><th class='text-center'>NUM PLU</th><th class='text-center'>PLU DESCRIPCI&Oacute;N</th><th class='text-center'>PRECIO ACTUAL</th><th class='text-center'>PRECIO NUEVO</th><th class='text-center'>CATEGOR&Iacute;A</th></tr></thead>";

    send = {"preview": 1};
    send.idProgramacion = id;
    $.getJSON("../adminPrecios/config_adminPrecios.php", send, function (datos) {
        if (datos.str > 0) {
            //html+="<tbody>";
            for (i = 0; i < datos.str; i++) {
                html += "<tr id='" + i + "' style='cursor:pointer;'>";
                html += "<td style='text-align:left'>" + datos[i]['num_plu'] + "&nbsp;</td>";
                html += "<td style='text-align:left'>" + datos[i]['plu_descripcion'] + "&nbsp;</td>";
                html += "<td style='text-align:left'>" + datos[i]['precio_actual'] + "&nbsp;</td>";
                html += "<td style='text-align:left'>" + datos[i]['precio_nuevo'] + "&nbsp;</td>";
                html += "<td style='text-align:left'>" + datos[i]['categoria'] + "&nbsp;</td></tr>";
                //$("#detalle_preview").html(html);					
            }
            $("#detalle_preview").html(html);
            //html+="</tbody>";
            $("#detalle_preview").html(html);
            $('#detalle_preview').dataTable({
                "destroy": true,
                "scrollY": "250px",
                //"scrollCollapse": true,
                "paging": false,
                "searching": false
            });
            $("#modal_previewPrecios").modal("show");
            fn_cargaCiudadesYRestaurantes(id);
        }
    });
};

function fn_cargaCiudadesYRestaurantes(idProgramacionPrecios) {
    var html = "<table id='detalle_prev' class='table table-bordered' width='100%'>";
    html += "<thead><tr class='active'><th class='text-center'>CIUDAD</th><th class='text-center'>NOMBRE RESTAURANTE</th><th class='text-center'>C&Oacute;DIGO RESTAURANTE</th></tr></thead>";
    html += "</table>";
    $("#div_prev").html(html);
    send = {"previewCiudadesYrestaurantes": 1};
    send.idProPrecios = idProgramacionPrecios;
    $.getJSON("../adminPrecios/config_adminPrecios.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                html += "<tr id='" + i + "' style='cursor:pointer;'>";
                html += "<td style='text-align:left'>" + datos[i]['ciu_nombre'] + "&nbsp;</td>";
                html += "<td style='text-align:left'>" + datos[i]['rst_descripcion'] + "&nbsp;</td>";
                html += "<td style='text-align:left'>" + datos[i]['rst_cod_tienda'] + "&nbsp;</td></tr>";
                $("#detalle_prev").html(html);
            }
            $("#detalle_prev").dataTable({"bLengthChange": false, "bPaginate": true}).rowGrouping({iGroupingColumnIndex: 0,
                sGroupingColumnSortDirection: "asc",
                iGroupingOrderByColumnIndex: 0});
        } else {
            html += "<tr><td colspan=3 style='text-align:center'>No existen datos.</td></tr>";
            $("#detalle_prev").html(html);
        }
    });
}

function fn_imprimir() {
    $(".printable").print();
}

function fn_aplicarPrecios() {
    var send = {};
    send = {"aplicarPrecios": 1};
    $.getJSON("../adminPrecios/config_adminPrecios.php", send, function (datos) {
        if (datos.str > 0) {
            alertify.success(datos[0]['mensaje']);
        }
    });
}