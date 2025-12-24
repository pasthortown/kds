///////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Worman Andrade////////////////////////////
///////DESCRIPCION: Clase para Reservas de mesas///////////////////
///////FECHA CREACION: 27-01-2015//////////////////////////////////
///////MODIFICACION: Juan Estévez//////////////////////////////////
///////FECHA MODIFICACIÓN: 21-12-2016 /////////////////////////////
///////DESCRIPCIÓN: Cargar imagen de área /////////////////////////
/////////////////// Cargar imagen de mesas de acuerdo al status////
/////////////////// Transparencia de mesas/////////////////////////
///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////

var Accion = 0;
var Cod_Mesa = 1;
var lc_paginas = -1; //para el paginador nunca se va a encerar a menos quer se actualice la pantalla
var acc_resultado = 0;
var acc_std = 0;
var reg_max = 0;
var reg_pres = 10;

$(document).ready(function () {
    $('#lblCuadradas').addClass('Active');
    
  
    fn_esconderDiv();
   
    $("#txt_codmesa_hidden").val(2);
    fn_btn("agregar", 1);
    
    $("#txtRestaurante").attr("disabled", false);
    fn_cargarRestaurante();
     
    fn_cargarCantidadAgregar();
 
    fn_cargarCantidadModificar();

    $("#txtRestaurante").change(function () {
        if ($("#txtRestaurante").val() != '0') {
            $("#selec_piso").html("");
            $("#selec_area").html("");
            fn_esconderDiv();
            fn_cargarPiso();
            $("#txtPiso").attr("disabled", false);
        } else {
            $("#selec_piso").html("");
            $("#selec_area").html("");
            fn_esconderDiv();
        }
    });

    $("#txtArea").change(function () {
        fn_imagenArea();
        fn_cargarMesas();
    });

    $("#txtPiso").attr("disabled", true);
    $("#txtArea").attr("disabled", true);
    $("#btn_guardar").hide;
    $("#img_remove").hide();

    $('#Modal_agregarmesa').on('shown.bs.modal', function () {
        $("#selec_tipo_nuevo").focus();
    });
});

function fn_OpcionSeleccionada(std) {
    var ls_opcion = std;
    if (ls_opcion == 'Todos') {
        estado = 'Todos';
        codPiso = $("#txt_codpiso_hidden").val();
        codAreaPiso = $("#txt_codarea_hidden").val();
        fn_cargarMesas(codAreaPiso, codPiso, estado);
    } else if (ls_opcion == 'Activo') {
        estado = 'Activo';
        codPiso = $("#txt_codpiso_hidden").val();
        codAreaPiso = $("#txt_codarea_hidden").val();
        fn_cargarMesas(codAreaPiso, codPiso, estado);
    } else if (ls_opcion == 'Inactivo') {
        estado = 'Inactivo';
        codPiso = $("#txt_codpiso_hidden").val();
        codAreaPiso = $("#txt_codarea_hidden").val();
        fn_cargarMesas(codAreaPiso, codPiso, estado);
        
    } else if (ls_opcion === 'Cuadradas') {
        $('#lblCuadradas').addClass('Active');   
        $('#lblRedondas').removeClass('Active');
        $('#lblRectangulares').removeClass('Active');

    } else if (ls_opcion === 'Redondas') {
        $('#lblCuadradas').removeClass('Active');
        $('#lblRedondas').addClass('Active');
        $('#lblRectangulares').removeClass('Active');
 
    } else if (ls_opcion === 'Rectangulares') {
        $('#lblCuadradas').removeClass('Active'); 
        $('#lblRedondas').removeClass('Active'); 
        $('#lblRectangulares').addClass('Active');
 
    }
    
    
}

function fn_OpcionSeleccionadaModInsert()
{
    var ls_opcion = '';
    ls_opcion = $(":input[name=estados]:checked").val();

    if (ls_opcion == 'Todos')
    {
        estado = 'Todos';
        codPiso = $("#txt_codpiso_hidden").val();
        codAreaPiso = $("#txt_codarea_hidden").val();
        fn_cargarMesas(codAreaPiso, codPiso, estado)
    } else if (ls_opcion == 'Activo') {
        estado = 'Activo';
        codPiso = $("#txt_codpiso_hidden").val();
        codAreaPiso = $("#txt_codarea_hidden").val();
        fn_cargarMesas(codAreaPiso, codPiso, estado)

    } else if (ls_opcion == 'Inactivo') {
        estado = 'Inactivo';
        codPiso = $("#txt_codpiso_hidden").val();
        codAreaPiso = $("#txt_codarea_hidden").val();
        fn_cargarMesas(codAreaPiso, codPiso, estado)
    }
}

//////////////////////////////CARGAR RESTAURANTE///////////////////////////////////////////////
function fn_cargarRestaurante() {
    //var codCadena = $("#txtCadena").val();
    send = {"cargarRestaurante": 1};
    //send.codigo=codCadena;
    $(".mesa").empty();
    $("#txtRestaurante").empty();
    $("#txtPiso").empty();
    $("#txtArea").empty();
    $("#txt1").empty();
    $("#txt2").empty();
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                $("#txtRestaurante").append("<option selected  value='0'>-----Seleccionar-----</option>");
                for (i = 0; i < datos.str; i++) {
                    $("#txtRestaurante").append("<option value=" + datos[i]['rst_id'] + ">" + datos[i]['rst_cod_tienda'] + " - " + datos[i]['rst_descripcion'] + "</option>");
                }
                $("#txtRestaurante").chosen();
            }
        }});
}

/////////////////////////////CARGAR PISO//////////////////////////////////////////////////
function fn_cargarPiso() {
    var html = '';
    var codRestaurante = $("#txtRestaurante").val();
    send = {"cargarPiso": 1};
    send.codigo = codRestaurante;
    $(".mesa").empty();
    $("#txtPiso").empty();
    $("#txtArea").empty();
    $("#txt1").empty();
    $("#txt2").empty();
    $("#txtPiso").empty();
    $("#txtArea").empty();
    $("#mesas_total").empty();
    $("#selec_piso").show();
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    html = html + "<label class='btn btn-default btn-sm' onclick='fn_cargarArea(\"" + datos[i]['pis_id'] + "\");'> <input id='" + datos[i]['pis_id'] + "' type='radio' name='optionsPiso' value='" + datos[i]['pis_id'] + "'>" + datos[i]['pis_numero'] + "</label>";
                }
                $("#selec_piso").html(html);
            }
        }});
}

/////////////////////////////CARGAR AREA////////////////////////////////////////////////////////////
function fn_cargarArea(codigo) {
    $("#selec_area").html("");
    fn_esconderDiv();
    var html = '';
    $("#txt_codpiso_hidden").val(codigo);
    $(".mesa").empty();
    $("#txtArea").val('');
    $("#txt1").val('');
    $("#txt2").val('');
    estado = 'Activo';
    $("#selec_area").show();
    send = {"cargarArea": 1};
    send.codigo = codigo;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    html = html + "<label class='btn btn-default btn-sm' onclick='fn_cargarMesas(\"" + datos[i]['arp_id'] + "\", \"" + codigo + "\",\"" + estado + "\");'> <input type='radio' name='optionsArea' id='" + datos[i]['arp_id'] + "' value=" + datos[i]['arp_id'] + ">" + datos[i]['arp_descripcion'] + "</label>";
                }
                $("#selec_area").html(html);
            }
        }});
}

/*-------------------------------------------------------
 FUNCION PARA SELECCIONAR UN CLICK
 -------------------------------------------------------*/

function fn_seleccionar(fila) {
    $("#mesas tr").removeClass("success");
    $("#" + fila + "").addClass("success");
}

/*-------------------------------------------------------
 FUNCION PARA SELECCIONAR DOBLE CLICK
 -------------------------------------------------------*/
function fn_seleccionModificar(fila, mesa_id , mesa_tipo_id) {
  
    Accion == 2;
    $('#Modal_modificarrmesa').modal('show');
    //fn_cargarMesas(mesa_id);
    //fn_cargarMesasImg(mesa_id);
    Cod_Mesa = mesa_id;
    fn_cargarMesaModificar(Cod_Mesa);
    document.getElementById("selec_tipo").value= mesa_tipo_id;
    $("#selec_tipo").trigger("chosen:updated");
}

/////////////////////////////CARGA LAS MESAS DE ACUERDO A LA BASE DE DATOS//////////////		
function fn_cargarMesas(codAreaPiso, codPiso, estado) {

    $('#opciones_estados label').removeClass('active');
    $('#std_activo').addClass('active');
    $('input[name="estados"]').prop('checked', false);
    $('#std_activo input').prop('checked', true);

    var html = '';
    var plano = "";
    fn_mostrarDiv();

    $("#txt_codpiso_hidden").val(codPiso);
    $("#txt_codarea_hidden").val(codAreaPiso);

    var codCadena = $("#txtCadena").val();
    var codRestaurante = $("#txtRestaurante").val();
    var codPiso = codPiso;
    var codAreaPiso = codAreaPiso;

    fn_cargarPanel(codRestaurante, codPiso, codAreaPiso);

    var lc_opcion_filtro = estado;
    var lc_buscar = 0;
    var j = 0;
    var k = 0;
    var lc_posX = 0;
    var lc_posY = 0;

    send = {"cargarMesa": 1};
    send.restaurante = codRestaurante;
    send.piso = codPiso;
    send.area = codAreaPiso;
    send.filtro = lc_opcion_filtro;
    send.buscar = lc_buscar;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                html = "<thead><tr class='active'>";
                html += "<th class='text-center'>Mesa</th>";
                html += "<th class='text-center'>Tipo Mesa</th>";
                html += "<th class='text-center'>Activo</th>";
                html += "</tr></thead>";
                $('#mesas').empty();
                for (i = 0; i < datos.str; i++) {
 
                    //Plano
                    if (datos[i]['std_id'] != 'Inactivo') {
                        if (datos[i]['mesa_coordenaday'] != null) {
                            lc_posY = datos[i]['mesa_coordenaday'];
                            lc_posX = datos[i]['mesa_coordenadax'];
                        } else {
                            if (i % 5 == 0) {
                                j += 90;
                                lc_posX += 55;
                                k = 20;
                            } else {
                                lc_posX = k;
                                lc_posY = j;
                                k += 55;
                            }
                        }
                        var dimension = datos[i]['mesa_dimension'].split("|");
                        plano += "<div class='ui-widget-content context-menu-sub box menu-1' align='center'  tmes_id=" + datos[i]['tmes_id'] + "  id="  + datos[i]['mesa_id'] + " style='width:" + dimension[0] + "px;height:" + dimension[1] + "px; background: rgba(255, 255, 255, 0.01); border:none; top:" + lc_posY + "%; left:" + lc_posX + "%; position:absolute;' onmousemove='fn_draggable(\"" + datos[i]['mesa_id'] + "\");' ><label style='position:absolute;top:50%;left:50%;transform: translate(-50%, -50%);'>" + datos[i]['mesa_descripcion'] + "</label><img id='planomesa' height='100%' width='100%' src='" + datos[i]['tmes_ruta_imagen'] + datos[i]['std_id'] + ".png' border=0 title= " + datos[i]['mesa_descripcion'] + "></div>";
                    
                    }

                    //Tabla
                    if (datos[i]['std_id'] != 'Inactivo') {
                        html += "<tr id='" + i + "' onclick='fn_seleccionar(" + i + ")' ondblclick='fn_seleccionModificar(" + i + ", \"" + datos[i]['mesa_id'] + "\" , \"" + datos[i]['tmes_id'] + "\" )'>";
                        html += "<td>" + datos[i]['mesa_descripcion'] + "</td>";
                        html += "<td>" + datos[i]['tmes_descripcion'] + "</td>";
                        html += "<td class='text-center'><input type='checkbox' checked='checked' disabled/></td></tr>";
                    } else {
                        html += "<tr id='" + i + "' onclick='fn_seleccionar(" + i + ")' ondblclick='fn_seleccionModificar(" + i + ", \"" + datos[i]['mesa_id'] + "\", \"" + datos[i]['tmes_id'] + "\" )'>";
                        html += "<td>" + datos[i]['mesa_descripcion'] + "</td>";
                        html += "<td>" + datos[i]['tmes_descripcion'] + "</td>";
                        html += "<td class='text-center'><input type='checkbox'  disabled/></td></tr>";
                    }
                    $("#mesas_total").html("<h5>Total de mesas <span class='label label-default'>" + datos.str + "</span></h5>");
                    $('#mesas').html(html);
                    $('#mesas').dataTable({
                        'destroy': true
                    });
                    $("#mesas_length").hide();
                    $("#mesas_paginate").addClass('col-xs-10');
                    $("#mesas_info").addClass('col-xs-10');
                    $("#mesas_length").addClass('col-xs-6');
                }

                send = {"arp_id": 1};
                send.arp_id = codAreaPiso;
                $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminRestaurante/config_leerimagenarea.php", data: send,
                    success: function (datos1) {
                        $("#plano").css("background-image", "url('data:image/png;base64," + datos1.imagen + "')");
                        $("#plano").css("background-repeat", "no-repeat");
                        $("#plano").css("background-size", "100% 100%");
                    },
                    error: function (e) {
                        alert('Error lectura de imagen ' + e);
                    }});

                $("#plano").html(plano);
            } else {
                $("#mesas").html("<tr class='active'><th class='text-center'>Mesa</th><th class='text-center'>Tipo Mesa</th><th class='text-center'>Estado</th></tr");
                $("#mesas").append("<tr><td colspan='3' class='text-center'>No existen registros.</td></tr>");
                $("#plano").html(plano);
            }
        }});
    
    // carga tipo de mesas al combo.
    fn_cargarTipoMesas();
     $("#selec_tipo").chosen({width: "100%" , no_results_text: "No hay resultados para " , search_contains: true});
//     $("#selec_tipo").focus();
}

/////////////////////////////CARGA LAS MESAS DE ACUERDO A LA BASE DE DATOS//////////////		
function fn_cargarMesasInactivos(codAreaPiso, codPiso, estado) {
    var html = '';
    fn_mostrarDiv();
    var codCadena = $("#txtCadena").val();
    var codRestaurante = $("#txtRestaurante").val();
    $("#txt_codpiso_hidden").val(codPiso);
    $("#txt_codarea_hidden").val(codAreaPiso);

    var lc_opcion_filtro = estado;
    var lc_buscar = 0/*$("#buscar").val()*/;
    var j = 0;
    var k = 0;
    var lc_posX = 0;
    var lc_posY = 0;

    send = {"cargarMesa": 1};
    send.restaurante = codRestaurante;
    send.piso = codPiso;
    send.area = codAreaPiso;
    send.filtro = lc_opcion_filtro;
    send.buscar = lc_buscar;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                html = "<thead><tr class='active'>";
                html += "<th class='text-center'>Mesa</th>";
                html += "<th class='text-center'>Tipo Mesa</th>";
                html += "<th class='text-center'>Activo</th>";
                html += "</tr></thead>";
                $('#mesas').empty();
                for (i = 0; i < datos.str; i++) {
                    if (datos[i]['std_id'] != 'Inactivo') {
                        html += "<tr id='" + i + "' onclick='fn_seleccionar(" + i + ")' ondblclick='fn_seleccionModificar(" + i + ", \"" + datos[i]['mesa_id'] + "\")'>";
                        html += "<td>" + datos[i]['mesa_descripcion'] + "</td>";
                        html += "<td>" + datos[i]['tmes_descripcion'] + "</td>";
                        html += "<td class='text-center'><input type='checkbox' checked='checked' disabled/></td></tr>";
                    } else {
                        html += "<tr id='" + i + "' onclick='fn_seleccionar(" + i + ")' ondblclick='fn_seleccionModificar(" + i + ", \"" + datos[i]['mesa_id'] + "\")'>";
                        html += "<td>" + datos[i]['mesa_descripcion'] + "</td>";
                        html += "<td>" + datos[i]['tmes_descripcion'] + "</td>";
                        html += "<td class='text-center'><input type='checkbox'  disabled/></td></tr>";
                    }
                    $("#mesas_total").html("<h5>Total de mesas <span class='label label-default'>" + datos.str + "</span></h5>");
                    $('#mesas').html(html);
                    $('#mesas').dataTable(
                            {
                                'destroy': true
                            }
                    );
                    $("#mesas_length").hide();
                    $("#mesas_paginate").addClass('col-xs-10');
                    $("#mesas_info").addClass('col-xs-10');
                    $("#mesas_length").addClass('col-xs-6');
                }
            } else {
                $("#mesas").html("<tr class='active'><th class='text-center'>Mesa</th><th class='text-center'>Tipo Mesa</th><th class='text-center'>Estado</th></tr");
                $("#mesas").append("<tr><td colspan='3' class='text-center'>No existen registros.</td></tr>");
            }
        }});
}

/////////////////////////////CARGA LAS MESAS DE ACUERDO A LA BASE DE DATOS//////////////		
function fn_cargarMesasImg(codAreaPiso, codPiso) {
    var html = '';
    fn_mostrarDiv();
    var codCadena = $("#txtCadena").val();
    var codRestaurante = $("#txtRestaurante").val();
    var lc_opcion_filtro = 'Activo';
    var lc_buscar = 0;
    var j = 0;
    var k = 0;
    var lc_posX = 0;
    var lc_posY = 0;
    send = {"cargarMesa": 1};
    send.restaurante = codRestaurante;
    send.piso = codPiso;
    send.area = codAreaPiso;
    send.filtro = lc_opcion_filtro;
    send.buscar = lc_buscar;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    if (datos[i]['std_id'] != 'Inactivo') {
                        if (datos[i]['mesa_coordenaday'] != null) {
                            lc_posY = datos[i]['mesa_coordenaday'];
                            lc_posX = datos[i]['mesa_coordenadax'];
                        } else {
                            if (i % 5 == 0) {
                                j += 90;
                                lc_posX += 55;
                                k = 20;
                            } else {
                                lc_posX = k;
                                lc_posY = j;
                                k += 55;
                            }
                        }
                        html = html + "<div class='ui-widget-content' align='center' id=" + datos[i]['mesa_id'] + " style='top:" + lc_posY + "%; left:" + lc_posX + "%; position:absolute;' onmousemove='fn_draggable(\"" + datos[i]['mesa_id'] + "\");' ><img id='planomesa' height='100%' width='100%' src='../../imagenes/mesa/mesa.png' border=0 title= " + datos[i]['mesa_descripcion'] + "><br/><label>" + datos[i]['mesa_descripcion'] + "</label></div>";
                    }
                    $("#plano").html(html);
                }
            }
        }});
}

function fn_draggable(id_mesa) {
    $("#plano").sortable();
    $("#plano").disableSelection();
    $("#" + id_mesa + "").draggable({
        containment: '#plano',
        cursor: 'move',
        drag: function (event, ui) {
  
            $("#posx").val(  (ui.position.left /8.55)  );
            $("#posy").val( ui.position.top  /6.41);
        },
        stop: function () {
            fn_guardarMesa(id_mesa);
        }
    });
    $("#" + id_mesa).resizable({
        stop: function () {
            fn_guardarMesaDimension(id_mesa);
        }
    });
}

/////////////////////////////CARGAR LA IMAGEN////////////////////////////////////////////
function fn_imagenArea() {
    $(".mesa").empty();
    var codRestaurante = $("#txtRestaurante").val();
    var codCadena = $("#txtCadena").val();
    var codPiso = $("#txtPiso").val();
    var codArea = $("#txtArea").val();
    var cadena = codCadena + "_" + codRestaurante + "_" + codPiso + "_" + codArea;
    var objeto = document.getElementById('imagen');
    objeto.style.backgroundImage = 'url(../../imagenes/planos/' + cadena + '.jpg)';
    objeto.style.backgroundRepeat = 'no-repeat';
    objeto.style.width = (screen.width - (screen.width * 0.3)) + 'px';
    objeto.style.height = (screen.height - (screen.height * 0.1)) + 'px';
}

////////////////////////////////////GUARDAR MESA//////////////////////////////////////////

function fn_guardarMesa(mesaId) {
    var valoresX = $("#posx").val();
    var valoresY = $("#posy").val();
    send = {"guardarMesa": 1};
    send.mesaId = mesaId;
    send.valoresX = valoresX;
    send.valoresY = valoresY;
    send.opc = "1";
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send, success: function (datos) {
            if (!datos) {
                alert("Error al guardar datos de la mesa seleccionada");
            }
        }});
}

function fn_guardarMesaDimension(mesaId) {
    var canvas = document.getElementById(mesaId);
    var valoresX = canvas.scrollWidth;
    var valoresY = canvas.scrollHeight;
    send = {"guardarMesa": 1};
    send.mesaId = mesaId;
    send.valoresX = valoresX;
    send.valoresY = valoresY;
    send.opc = "2";
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send, success: function (datos) {
            if (!datos) {
                alert("Error al guardar datos de la mesa seleccionada");
            }
        }});

}

////////////////////////////////////BORRAR CAMPOS//////////////////////////////////////////

function fn_borrar() {
    $(".mesa").empty();
    $("#txtCadena").val(0);
    $("#txtRestaurante").empty();
    $("#txtPiso").empty();
    $("#txtArea").empty();
    $("#txt1").val('');
    $("#txt2").val('');
    fn_cargarRestaurante();
    fn_esconderDiv();
    $("#selec_piso").hide();
    $("#selec_area").hide();
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

/*-------------------------------------------------------
 FUNCION PARA CARGAR LA MODAL AGREGAR MESA
 -------------------------------------------------------*/
function fn_agregar() {
    Accion = 1;
    if ($("#txtRestaurante").val() == 0) {
        alertify.error("Debe seleccionar un Restaurante");
    } else if ($("#txtPiso").val() == 0) {
        alertify.error("Debe seleccionar un Piso del Restaurante");
    } else if ($("#txtArea").val() == 0) {
        alertify.error("Debe seleccionar un Area del Restaurante");
    } else {
        $("#nombreMesaNew").val("");
        $("#selec_tipo_nuevo").val(0);
        $('#Modal_agregarmesa').modal('show');
    }

}

/*-------------------------------------------------------
 FUNCION PARA CARGAR AL COMBO BOX LA CANTIDAD DE PERSONAS AL AGREGAR UNA MESA
 -------------------------------------------------------*/
function fn_cargarCantidadAgregar() {
 
    send = {"cargarCantidad": 1};
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                $("#selec_tipo_nuevo").append("<option selected  value='0'>-----Seleccionar-----</option>");
                for (i = 0; i < datos.str; i++) {
 
                    $("#selec_tipo_nuevo").append("<option id=" + datos[i]['tmes_id'] + " value=" + datos[i]['tmes_id'] + ">" + datos[i]['tmes_descripcion'] + "</option>");
                    $("#" + datos[i]['tmes_id']).attr("data-img-src", datos[i]['tmes_ruta_imagen'] + "Disponible.png");
                }
                $("#txtcantidadpersonas").val(0);
                $(".my-select").chosen({width: "100%"});
                
            }
        }});
}

 
 
 
 
   
 function fn_cargarTipoMesas() {
 
    send = {"cargarTipoMesa": 1};
    send.restaurante =  $('#txtRestaurante option:selected').val(); 
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                $("#select_tipo_mesa").append("<option selected  value='0'>-----Seleccionar-----</option>");
                for (i = 0; i < datos.str; i++) {
                    $("#select_tipo_mesa").append("<option id=" +"tm_"+ datos[i]['ID_ColeccionDeDatosRestaurante'] + " value=" + datos[i]['ID_ColeccionDeDatosRestaurante'] + ">" + datos[i]['Descripcion'] + "</option>");
                }
                
            }
        }});
 
}


/*-------------------------------------------------------
 FUNCION PARA CARGAR AL COMBO BOX LA CANTIDAD DE PERSONAS AL MODIFICAR UNA MESA
 -------------------------------------------------------*/
function fn_cargarCantidadModificar() {
    send = {"cargarCantidad": 1};
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                $("#selec_tipo").append("<option selected  value='0'>-----Seleccionar-----</option>");
                for (i = 0; i < datos.str; i++) {
                    //alert(datos[i]['tmes_ruta_imagen']);
                    $("#selec_tipo").append("<option value=" + datos[i]['tmes_id'] + ">" + datos[i]['tmes_descripcion'] + "</option>");
                }
            }
        }
    });
}

/*-------------------------------------------------------
 FUNCION PARA GUARDAR DATOS AL AGREGAR MESA
 -------------------------------------------------------*/
function fn_guardarMesaNueva(nombre, area, tmesa, estado, cadena, usuario, rest) {

    var plano = "";
    $('#opciones_estados label').removeClass('active');
    $('#std_activo').addClass('active');
    $('input[name="estados"]').prop('checked', false);
    $('#std_activo input').prop('checked', true);
    var pis_id = $("#txt_codpiso_hidden").val();

    send = {"accionMenu": 1};
    send.accion = 1;
    send.id = 0;
    send.mesa_descripcion = nombre;
    send.arp_id = area;
    send.tmes_id = tmesa;
    send.cdn_id = cadena;
    send.user = usuario;
    send.rst = rest;
    send.pis_id = pis_id;
    if ($("#check_activonuevo").is(':checked')) {
        send.std_id = 'Activo';
    } else {
        send.std_id = 'Inactivo';
    }
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send, success:
                function (datos) {
                    if (datos.str > 0) {
                        html = "<thead><tr class='active'>";
                        html += "<th class='text-center'>Mesa</th>";
                        html += "<th class='text-center'>Tipo Mesa</th>";
                        html += "<th class='text-center'>Activo</th>";
                        html += "</tr></thead>";
                        $('#mesas').empty();
                        for (i = 0; i < datos.str; i++) {
                            //Plano
                            if (datos[i]['std_id'] != 'Inactivo') {
                                if (datos[i]['mesa_coordenaday'] != null) {
                                    lc_posY = datos[i]['mesa_coordenaday'];
                                    lc_posX = datos[i]['mesa_coordenadax'];
                                } else {
                                    if (i % 5 == 0) {
                                        j += 90;
                                        lc_posX += 55;
                                        k = 20;
                                    } else {
                                        lc_posX = k;
                                        lc_posY = j;
                                        k += 55;
                                    }
                                }
                                plano += "<div class='ui-widget-content context-menu-sub box menu-1' align='center' id=" + datos[i]['mesa_id'] + " style=' background: rgba(255, 255, 255, 0.01); border:none; top:" + lc_posY + "%; left:" + lc_posX + "%; position:absolute;' onmousemove='fn_draggable(\"" + datos[i]['mesa_id'] + "\");' ><label style='position:absolute;top:50%;left:50%;transform: translate(-50%, -50%);'>" + datos[i]['mesa_descripcion'] + "</label><img id='planomesa' height='100%' width='100%' src='../../imagenes/mesa/" + datos[i]['std_id'] + ".png' border=0 title= " + datos[i]['mesa_descripcion'] + "></div>";
                            }
                            //$("#"+datos[i]['mesa_id']).resizable();
                            //Tabla
                            if (datos[i]['std_id'] != 'Inactivo') {
                                html += "<tr id='" + i + "' onclick='fn_seleccionar(" + i + ")' ondblclick='fn_seleccionModificar(" + i + ", \"" + datos[i]['mesa_id'] + "\")'>";
                                html += "<td>" + datos[i]['mesa_descripcion'] + "</td>";
                                html += "<td>" + datos[i]['tmes_descripcion'] + "</td>";
                                html += "<td class='text-center'><input type='checkbox' checked='checked' disabled/></td></tr>";
                            } else {
                                html += "<tr id='" + i + "' onclick='fn_seleccionar(" + i + ")' ondblclick='fn_seleccionModificar(" + i + ", \"" + datos[i]['mesa_id'] + "\")'>";
                                html += "<td>" + datos[i]['mesa_descripcion'] + "</td>";
                                html += "<td>" + datos[i]['tmes_descripcion'] + "</td>";
                                html += "<td class='text-center'><input type='checkbox'  disabled/></td></tr>";
                            }

                            $("#mesas_total").html("<h5>Total de mesas <span class='label label-default'>" + datos.str + "</span></h5>");
                            $('#mesas').html(html);
                            $('#mesas').dataTable({
                                'destroy': true
                            });
                            $("#mesas_length").hide();
                            $("#mesas_paginate").addClass('col-xs-10');
                            $("#mesas_info").addClass('col-xs-10');
                            $("#mesas_length").addClass('col-xs-6');
                        }
                        $("#plano").html(plano);
                    } else {
                        $("#mesas").html("<tr class='active'><th class='text-center'>Mesa</th><th class='text-center'>Tipo Mesa</th><th class='text-center'>Estado</th></tr");
                        $("#mesas").append("<tr><td colspan='3' class='text-center'>No existen registros.</td></tr>");
                        $("#plano").html(plano);
                    }
                    fn_cargarPanel(rest, pis_id, area);
                }
    });
}

/*-------------------------------------------------------
 FUNCION PARA VALIDAR DATOS AL AGREGAR MESA
 -------------------------------------------------------*/
function fn_guardarNuevo() {
    if (Accion == 1) {
        var nombre = $('#nombreMesaNew').val();
        var area = $(":input[name=optionsArea]:checked").val();
        var tmesa = $('#selec_tipo_nuevo option:selected').val();
       
        
        var estado = $('#check_activonuevo option:checked').val();
        var cadena = $('#cadenas').val();
        var usuario = $('#idUser').val();
        var rest = $("#txtRestaurante").val();
        if (nombre.length == 0) {
            alertify.error("Ingrese una Descripcion", function () {
                $('#nombreMesaNew').focus();
            });
        } else if ($("#selec_tipo_nuevo").val() == '0') {
            alertify.error("Seleccione un tipo de mesa", function () {
                $('#selec_tipo_nuevo').focus();
            });
        } else {
            fn_guardarMesaNueva(nombre, area, tmesa, estado, cadena, usuario, rest);
            $('#Modal_agregarmesa').modal('hide');
             fn_cargarMesas($("#txt_codarea_hidden").val(), $("#txt_codpiso_hidden").val(), "Activo");
          
            
            
            $('#selec_tipo_nuevo').val($('#selec_tipo_nuevo > option:first').val());
             
             
              $('#selec_tipo_nuevo').trigger('chosen:updated');
        }
    }
     
}

/*-------------------------------------------------------
 FUNCION PARA VALIDAR DATOS AL MODIFICAR MESA
 -------------------------------------------------------*/
function fn_guardarModificar(Accion) {
  
    var Cod_Mesa = $("#codigomesa").val();
 
    if (Accion === 2) {
        var nombre = $('#nombreMesa').val();
        var area = $(":input[name=optionsArea]:checked").val();
        var tmesa = $('#selec_tipo option:selected').val();
        
        var estado = $('#check_activo option:checked').val();
        var cadena = $('#cadenas').val();
        var usuario = $('#idUser').val();
        var rest = $("#txtRestaurante").val();
        if (nombre.length == 0) {
            alertify.error("Ingrese una Descripcion", function () {
                $('#nombreMesa').focus();
            });
        } else if ($("#selec_tipo").val() == 0) {
            alertify.error("Seleccione un tipo de mesa", function () {
                $('#selec_tipo').focus();
            });
        } else {
 
            
             fn_modificarMesa(Cod_Mesa, nombre, area, tmesa, estado, cadena, usuario, rest);
            $('#Modal_modificarrmesa').modal('hide');
            
   
            fn_cargarMesas($("#txt_codarea_hidden").val(), $("#txt_codpiso_hidden").val(), "Activo");
            
          
            
        }
    }
}

/*-------------------------------------------------------
 FUNCION PARA CARGAR LOS DATOS AL MODIFICAR MESA
 -------------------------------------------------------*/
function fn_cargarMesaModificar(codigo) {
    Accion = 2;
    send = {"cargaMesaModificar": 1};
    send.mesa_id = codigo;
    $.getJSON("../adminmesas/config_mesa.php", send, function (datos) {
        if (datos.str > 0) {
            if (datos.std_id != 'Inactivo') {
                $("#check_activo").prop('checked', true);
            } else {
                $("#check_activo").prop('checked', false);
            }
            $('#nombreMesa').val(datos.mesa_descripcion);
            $('#selec_tipo').val(datos.tmes_id);
        }
    });
}

/*-------------------------------------------------------
 FUNCION PARA GUARDAR LOS DATOS AL MODIFICAR UNA MESA
 -------------------------------------------------------*/
function fn_modificarMesa(codigo, nombre, area, tmesa, estado, cadena, usuario, rest) {
    var plano = "";
    var html = "";
    $('#opciones_estados label').removeClass('active');
    $('#std_activo').addClass('active');
    $('input[name="estados"]').prop('checked', false);
    $('#std_activo input').prop('checked', true);
    var pis_id = $("#txt_codpiso_hidden").val();

    send = {"accionMenu": 1};
    send.accion = 2;
    send.id = codigo;
    send.mesa_descripcion = nombre;
    send.arp_id = area;
    send.tmes_id = tmesa;
    send.cdn_id = cadena;
    send.user = usuario;
    send.rst = rest;
    send.pis_id = pis_id;
    if ($("#check_activo").is(':checked')) {
        send.std_id = 'Activo';
    } else {
        send.std_id = 'Inactivo';////
    }
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send, success:
                function (datos) {
                    if (datos.str > 0) {
                        html = "<thead><tr class='active'>";
                        html += "<th class='text-center'>Mesa</th>";
                        html += "<th class='text-center'>Tipo Mesa</th>";
                        html += "<th class='text-center'>Activo</th>";
                        html += "</tr></thead>";
                        $('#mesas').empty();
                        for (i = 0; i < datos.str; i++) {
                            //Plano
                            if (datos[i]['std_id'] != 'Inactivo') {
                                if (datos[i]['mesa_coordenaday'] != null) {
                                    lc_posY = datos[i]['mesa_coordenaday'];
                                    lc_posX = datos[i]['mesa_coordenadax'];
                                } else {
                                    if (i % 5 == 0) {
                                        j += 90;
                                        lc_posX += 55;
                                        k = 20;
                                    } else {
                                        lc_posX = k;
                                        lc_posY = j;
                                        k += 55;
                                    }
                                }
                                plano += "<div class='ui-widget-content' align='center' id=" + datos[i]['mesa_id'] + " style='background: rgba(255, 255, 255, 0.01); border:none; top:" + lc_posY + "%; left:" + lc_posX + "%; position:absolute;' onmousemove='fn_draggable(\"" + datos[i]['mesa_id'] + "\");' ><label style='position:absolute;top:50%;left:50%;transform: translate(-50%, -50%);'>" + datos[i]['mesa_descripcion'] + "</label><img id='planomesa' height='100%' width='100%' src='../../imagenes/mesa/" + datos[i]['std_id'] + ".png' border=0 title= " + datos[i]['mesa_descripcion'] + "></div>";
                            }
                            //Tabla
                            if (datos[i]['std_id'] != 'Inactivo') {
                                html += "<tr id='" + i + "' onclick='fn_seleccionar(" + i + ")' ondblclick='fn_seleccionModificar(" + i + ", \"" + datos[i]['mesa_id'] + "\")'>";
                                html += "<td>" + datos[i]['mesa_descripcion'] + "</td>";
                                html += "<td>" + datos[i]['tmes_descripcion'] + "</td>";
                                html += "<td class='text-center'><input type='checkbox' checked='checked' disabled/></td></tr>";
                            } else {
                                html += "<tr id='" + i + "' onclick='fn_seleccionar(" + i + ")' ondblclick='fn_seleccionModificar(" + i + ", \"" + datos[i]['mesa_id'] + "\")'>";
                                html += "<td>" + datos[i]['mesa_descripcion'] + "</td>";
                                html += "<td>" + datos[i]['tmes_descripcion'] + "</td>";
                                html += "<td class='text-center'><input type='checkbox'  disabled/></td></tr>";
                            }
                            $("#mesas_total").html("<h5>Total de mesas <span class='label label-default'>" + datos.str + "</span></h5>");
                            $('#mesas').html(html);
                            $('#mesas').dataTable({
                                'destroy': true
                            });
                            $("#mesas_length").hide();
                            $("#mesas_paginate").addClass('col-xs-10');
                            $("#mesas_info").addClass('col-xs-10');
                            $("#mesas_length").addClass('col-xs-6');
                        }
                        $("#plano").html(plano);

                    } else {
                        $("#mesas").html("<tr class='active'><th class='text-center'>Mesa</th><th class='text-center'>Tipo Mesa</th><th class='text-center'>Estado</th></tr");
                        $("#mesas").append("<tr><td colspan='3' class='text-center'>No existen registros.</td></tr>");
                    }
                    fn_cargarPanel(rest, pis_id, area);
                }
    });
}

/*---------------------------------------------
 Esconder los divs
 -----------------------------------------------*/
function fn_esconderDiv() {
    $("#tabcontenedor").hide();
}

/*---------------------------------------------
 Carga los divs
 -----------------------------------------------*/
function fn_mostrarDiv() {
    $("#tabcontenedor").show();
    $("#selec_piso").show();
    $("#selec_area").show();
}

/*---------------------------------------------
 FUNCION PARA LIMPIAR EL BUSCADOR
 -----------------------------------------------*/
function fn_limpiaBuscador() {
    $("#buscar").val('');
    $("#img_remove").hide();
    $("#img_buscar").show();
}

/*--------------------------------------------
 FUNCION PARA CARGAR PANEL DE MESAS
 -----------------------------------------------*/
function fn_cargarPanel(rest, piso, area) {
    $("#mesas_parciales").empty();
    send = {"cargarPanelMesa": 1};
    send.rest = rest;
    send.piso = piso;
    send.area = area;
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    $("#mesas_parciales").append("<h6> <img id='planomesa' width='30px' height='30px' src='" + datos[i]['tmes_ruta_imagen'] + datos[i]['std_descripcion'] + ".png' border=0 title= " + datos[i]['std_descripcion'] + "> Mesas " + datos[i]['std_descripcion'] + " <span class='label label-default'>" + datos[i]['cantidad'] + "</span></h6>");
                }
            } else {
                $("#mesas_parciales").html("<h5>No existen mesas asignadas</h5>");
            }
        }});
}
