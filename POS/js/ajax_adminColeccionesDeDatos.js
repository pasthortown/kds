///////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO//////////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE COLECCIONES DE DATOS, CREAR MODIFICAR /////////
////////////////TABLAS: Colecciones Varias ////////////////////////////////////////
////////FECHA CREACION: 16/03/2016/////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////
lc_control = -1; //bandera para guardar un nuevo registro o para guardar un registro modificado
seleccionFilaA = -1; //bandera para saber si esta seleccionado un registro TABLA A
seleccionFilaB = -1; //bandera para saber si esta seleccionado un registro TABLA B
bandera = -1; // bandera para controlar si es nuevo � vamos a modificar un registro
var _filaSeleccionada = function (fila) { //funcion  para agregar la clase succes a la fila luego de modificar
    $("#" + fila + "").addClass("success");
}
$(document).ready(function () {
    fn_btn('cancelar', 1);
    fn_btn('agregar', 1);
    fn_cargarTablasCabeceracolecciones();
    $("#txt_bandera").val('');
    $("#txt_ID_Coleccion").val('');
    $("#div_Coleccion").hide();
    $("#div_ColeccionDeDatos").hide();
});

/*========================================*/
/*FUNCION PARA CARGAR BOTONES SUPERIORES  */
/*========================================*/
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

/*==================================================================================*/
/*FUNCION PARA CARGAR NOMBRE DE LAS TABLAS CABECERA DE COLECCIONES                  */
/*==================================================================================*/
function fn_cargarTablasCabeceracolecciones() {


    send = {"cargarTablasCabeceracolecciones": 1};
    send.accion = 1;
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminestacion/config_adminestacion.php", data: send,
        success: function (datos) {
            if (datos.str > 0)
            {
                for (i = 0; i < datos.str; i++)
                {
                    nombre_tabla = datos[i]['nombre_tabla'];
                    //cargamos pesta�as tablas coleccion de datos////////////////////////////////////////////////////////////////////////					
                    html = "<li role='presentation' id='tab_" + datos[i]['nombre_tabla'] + "'>";
                    html += "<a href='#panel_" + datos[i]['nombre_tabla'] + "' aria-controls='panel_" + datos[i]['nombre_tabla'] + "' role='tab' data-toggle='tab' onclick='fn_cargarTabsColecciones(\"" + nombre_tabla + "\")'><h5>" + datos[i]['nombre_coleccion'] + "</h5></a>";
                    html += "</li>";
                    $('#ul_pestanas').append(html);
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                    //cargamos contenido de las pesta�as/////////////////////////////////////////////////////////////////////////////////
                    $("#div_tabla_" + datos[i]['nombre_tabla'] + "").empty();
                    //creamos tabs para las tablas de colecciones	
                    contenido = "<div role='tabpanel' class='tab-pane' id='panel_" + datos[i]['nombre_tabla'] + "'><br>";
                    contenido += "<ul id='tabs_" + datos[i]['nombre_tabla'] + "' class='nav nav-tabs'></ul></div>";
                    $('#div_tabs').append(contenido);
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
                }
            } else {
                alertify.error("No existen datos para esta cadena.");
                $("#div_colecciones").html(html);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

/*==================================================================================*/
/*FUNCION PARA CARGAR TABS CON NOMBRE DE LAS TABLAS DE COLECCIONES                  */
/*==================================================================================*/
function fn_cargarTabsColecciones(nombreTablaColeccion) {

    seleccionFilaA = -1;
    send = {"cargarNombreTablasColecciones": 1};
    send.accion = 3;
    send.nombretabla = nombreTablaColeccion;
    $.getJSON("../adminColeccionesDeDatos/config_coleccionesDeDatos.php", send, function (datos) {
        if (datos.str > 0)
        {
            $('#tabs_' + nombreTablaColeccion + '').empty();
            $("#div_Coleccion").hide();
            $("#div_ColeccionDeDatos").hide();
            for (i = 0; i < datos.str; i++)
            {
                //cargamos pesta�as tablas coleccion de datos//
                if (datos[i]['num_tabla'] == 3) {
                    html = "<li role='presentation' id='tab_" + datos[i]['nombre_tabla'] + "'>";
                    html += "<a id = 'panel_" + datos[i]['num_tabla'] + "_" + datos[i]['nombre_tabla'] + "' href='#panel_" + datos[i]['num_tabla'] + "' aria-controls='panel_' role='tab' data-toggle='tab' onclick='fn_cargarTablaColeccion();'><h5>" + datos[i]['nombre_tabla'] + "</h5></a>";
                    html += "</li>";
                    $('#tabs_' + nombreTablaColeccion + '').append(html);
                }
                //guardamos nombre de las tablas en cajas de textos//
                if (datos[i]['num_tabla'] == 1) {
                    $("#txt_nombreTablaA").val(datos[i]['nombre_tabla']);
                }
                if (datos[i]['num_tabla'] == 2) {
                    $("#txt_nombreTablaB").val(datos[i]['nombre_tabla']);
                }
                if (datos[i]['num_tabla'] == 3) {
                    $("#txt_nombreTablaC").val(datos[i]['nombre_tabla']);
                }
            }
            fn_cargarTablaColeccion();
        }
    });
}

/*
 function fn_traedatosTablaColecciones(nombre_tablaColeccionDeDatos,nombre_tablaColeccion,num_tabla){
 
 $("#txt_nombreTablaA").val(nombre_tablaColeccion);
 $("#txt_nombreTabla").val(nombre_tablaColeccionDeDatos);
 $("#txt_bandera").val(num_tabla); //==>bandera para abrir modal de acuerdo a la tabla de colecciones cuando es nuevo//
 $('#contenido').empty();
 
 contenido = "<div id='div_tabla_"+nombre_tablaColeccionDeDatos+"'>";
 contenido += "<table class='table table-bordered table-hover' id='tabla_"+nombre_tablaColeccionDeDatos+"' border='1' cellpadding='1' cellspacing='0'>";
 contenido += "</table>"
 contenido += "</div>";
 $('#contenido').append(contenido);
 
 if(num_tabla == '1'){
 $("#txt_ID_Coleccion").val('0'); //seteamos a '0' para mostrar todas las coleccionesDeDatos
 fn_cargarTablaColeccion(nombre_tablaColeccion,nombre_tablaColeccionDeDatos);
 }
 
 if(num_tabla == '2'){
 fn_cargarTablaColeccionDeDatos(nombre_tablaColeccion,nombre_tablaColeccionDeDatos)
 }
 
 if(num_tabla == '3'){
 fn_cargarTablaColecciones(nombre_tablaColeccion,nombre_tablaColeccionDeDatos);
 }
 
 }
 */

/*==================================================================================*/
/*FUNCION PARA CARGAR LA INFORMACION DE LAS TABLAS CABECERAS DE COLECCION           */
/*==================================================================================*/
function fn_cargarTablaColeccion() {

    nombre_TablaA = $("#txt_nombreTablaA").val();
    nombre_TablaB = $("#txt_nombreTablaB").val();
    $("#titulo_panel_coleccion").html("" + nombre_TablaA + "");

    send = {"cargarTablaCabecera": 1};
    send.accion = 2;
    send.nombretabla = nombre_TablaA;
    $.getJSON("../adminColeccionesDeDatos/config_coleccionesDeDatos.php", send, function (datos) {
        if (datos.str > 0)
        {
            $("#div_Coleccion").show();
            $("#tabla_Coleccion").empty();
            tabla = "<thead><tr class='active'>";
            tabla += "<th style='width:200px; text-align:center ;'>Colecci&oacute;n</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Configuraci&oacute;n</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Reporte</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Cubo</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Repetir Configuraci&oacute;n</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Activo</th>";
            tabla += "</tr></thead>";

            for (i = 0; i < datos.str; i++)
            {

                tabla += "<tr class='tabla_detalleMov' id='A" + i + "' style='cursor:pointer;'";
                tabla += "onclick='fn_seleccionclickA(\"A" + i + "\",\"" + datos[i]['ID_Coleccion'] + "\",\"" + datos[i]['Descripcion'] + "\")' ondblclick='fn_seleccion(\"" + datos[i]['ID_Coleccion'] + "\",0," + i + ",'" + datos[i]['cdn_id'] + "',\"" + nombre_TablaA + "\",\"" + nombre_TablaB + "\")'>";
                tabla += "<td align='center'  style='wdth:160px;'>" + datos[i]['Descripcion'] + "&nbsp;</td>";
                if (datos[i]['configuracion'] == 1) {
                    tabla += "<td align='center'  style='width:169px;'><input disabled='disabled' type='checkbox' checked='checked' id='option_configuracion_" + nombre_tabla + "'></td>";
                }
                if (datos[i]['configuracion'] == 0) {
                    tabla += "<td align='center'  style='width:169px;'><input type='checkbox' disabled='disabled'id='option_configuracion_" + nombre_tabla + "'></td>";
                }
                if (datos[i]['reporte'] == 1) {
                    tabla += "<td align='center'  style='width:169px;'><input disabled='disabled' type='checkbox' checked='checked' id='option_reporte_" + nombre_tabla + "'></td>";
                }
                if (datos[i]['reporte'] == 0) {
                    tabla += "<td align='center'  style='width:169px;'><input type='checkbox' disabled='disabled'id='option_reporte_" + nombre_tabla + "'></td>";
                }
                if (datos[i]['cubo'] == 1) {
                    tabla += "<td align='center'  style='width:169px;'><input disabled='disabled' type='checkbox' checked='checked' id='option_cubo_" + nombre_tabla + "'></td>";
                }
                if (datos[i]['cubo'] == 0) {
                    tabla += "<td align='center'  style='width:169px;'><input type='checkbox' disabled='disabled'id='option_cubo_" + nombre_tabla + "'></td>";
                }
                if (datos[i]['repetirConfiguracion'] == 1) {
                    tabla += "<td align='center'  style='width:169px;'><input disabled='disabled' type='checkbox' checked='checked' id='option_repetirConfiguracion_" + nombre_tabla + "'></td>";
                }
                if (datos[i]['repetirConfiguracion'] == 0) {
                    tabla += "<td align='center'  style='width:169px;'><input type='checkbox' disabled='disabled'id='option_repetirConfiguracion_" + nombre_tabla + "'></td>";
                }
                if (datos[i]['isActive'] == 1) {
                    tabla += "<td align='center'  style='width:169px;'><input disabled='disabled' type='checkbox' checked='checked' id='option_isActive_" + nombre_tabla + "'></td></tr>";
                }
                if (datos[i]['isActive'] == 0) {
                    tabla += "<td align='center'  style='width:169px;'><input type='checkbox' disabled='disabled'id='option_isActive_" + nombre_tabla + "'></td></tr>";
                }
                $("#tabla_Coleccion").html(tabla);
            }
            $("#tabla_Coleccion").dataTable(
                    {//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
                        'destroy': true
                    }
            );

            $("#tabla_Coleccion_length").empty();
            //$("#tabla_Coleccion_length").append("");
            $("#tabla_Coleccion_paginate").addClass('col-xs-10');
            $("#tabla_Coleccion_info").addClass('col-xs-10');
            $("#tabla_Coleccion_length").addClass('col-xs-6');
        }
    });

}

/*=============================================================================================*/
/*FUNCION PARA SELECCIONAR LAS FILAS DE LA TABLA_A                                               */
/*=============================================================================================*/
function fn_seleccionclickA(fila, ID_Coleccion, descripcion) {
    seleccionFilaA = 1; //bandera para saber si esta seleccionado un registro
    nombre_TablaA = $("#txt_nombreTablaA").val();
    nombre_TablaB = $("#txt_nombreTablaB").val();
    $("#txt_filaA").val(fila);
    $("#tabla_Coleccion tr").removeClass("success");
    $("#" + fila + "").addClass("success");
    $("#txt_ID_Coleccion").val(ID_Coleccion);
    $("#txt_descripcionA").val(descripcion);
    fn_cargarTablaColeccionDeDatos(nombre_TablaA, nombre_TablaB)
}

/*=============================================================================================*/
/*FUNCION PARA SELECCIONAR DOBLE CLICK Y MODIFICAR LOS REGISTROS                               */
/*=============================================================================================*/
function fn_seleccion(ID_Coleccion, ID_ColeccionDeDatos, fila, cadena, idIntegracion)
{
    lc_control = 1;
    bandera = $("#txt_bandera").val();
    nombre_tablaColeccion = $("#txt_nombreTablaA").val();
    nombre_tablaColeccionDeDatos = $("#txt_nombreTabla").val();
    $("#txt_ID_Coleccion").val(ID_Coleccion);
    $("#txt_ID_ColeccionDeDatos").val(ID_ColeccionDeDatos);

    if (bandera == 1) {
        fn_traeColeccionesModificar(ID_Coleccion);
        $('#ModalNuevo').modal('show');
    }
    if (bandera == 2) {
        fn_traeColeccionDeDatosModificar(ID_Coleccion, ID_ColeccionDeDatos);
        $('#ModalNuevoB').modal('show');
    }
    if (bandera == 3) {

    }
}

/*=============================================================================================*/
/*FUNCION PARA SELECCIONAR LAS FILAS DE LA TABLA_B                                               */
/*=============================================================================================*/
function fn_seleccionclickB(filaB, ID_Coleccion, ID_ColeccionDeDatos) {

    seleccionFilaB = 1;
    nombre_TablaA = $("#txt_nombreTablaA").val();
    nombre_TablaB = $("#txt_nombreTablaB").val();
    $("#tabla_ColeccionDeDatos tr").removeClass("success");
    $("#" + filaB + "").addClass("success");
    $("#txt_ID_Coleccion").val(ID_Coleccion);
    $("#txt_ID_ColeccionDeDatos").val(ID_ColeccionDeDatos);
    $("#txt_filaB").val(filaB);
}

/*==================================================================================*/
/*FUNCION PARA CARGAR LA INFORMACION DE LA TABLA ColeccionDeDatos                   */
/*==================================================================================*/
function fn_cargarTablaColeccionDeDatos(nombre_TablaA, nombre_TablaB) {

    ID_Coleccion = $("#txt_ID_Coleccion").val();
    descripcionA = $("#txt_descripcionA").val();
    $("#titulo_panel_coleccionDeDatos").html("" + nombre_TablaB + "");
    send = {"cargarTablaColeccionDeDatos": 1};
    send.accion = 4;
    send.nombretabla = nombre_TablaB;
    send.ID_Coleccion = ID_Coleccion;
    $.getJSON("../adminColeccionesDeDatos/config_coleccionesDeDatos.php", send, function (datos) {
        if (datos.str > 0)
        {
            $("#div_ColeccionDeDatos").show();
            $("#div_tabla_ColeccionDeDatos").show();
            $("#tabla_ColeccionDeDatos").empty();
            tabla = "<thead><tr class='active'>";
            //tabla += "<th style='width:200px; text-align:center ;'>Colecci&oacute;n</th>";
            tabla += "<th style='width:200px; text-align:center ;'>Descripci&oacute;n</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Especificar Valor</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Obligatorio</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Tipo de Dato</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Activo</th>";
            tabla += "</tr></thead>";

            for (i = 0; i < datos.str; i++)
            {

                tabla += "<tr class='tabla_detalleMov' id='B" + i + "' style='cursor:pointer;'";
                tabla += "onclick='fn_seleccionclickB(\"B" + i + "\",\"" + datos[i]['ID_Coleccion'] + "\",\"" + datos[i]['ID_ColeccionDeDatos'] + "\")' ondblclick=fn_seleccion(\"" + datos[i]['ID_Coleccion'] + "\",\"" + datos[i]['ID_ColeccionDeDatos'] + "\"," + i + ",'" + datos[i]['cdn_id'] + "',\"" + datos[i]['idIntegracion'] + "\");>";
                //tabla += "<td align='center'  style='wdth:160px;'>"+datos[i]['DescripcionColeccion']+"&nbsp;</td>";
                tabla += "<td align='center'  style='wdth:160px;'>" + datos[i]['Descripcion'] + "&nbsp;</td>";

                if (datos[i]['especificarValor'] == 1) {
                    tabla += "<td align='center'  style='width:50px;'><input disabled='disabled' type='checkbox' checked='checked' id='option_configuracion_" + nombre_tabla + "'></td>";
                }
                if (datos[i]['especificarValor'] == 0) {
                    tabla += "<td align='center'  style='width:50px;'><input type='checkbox' disabled='disabled'id='option_configuracion_" + nombre_tabla + "'></td>";
                }
                if (datos[i]['obligatorio'] == 1) {
                    tabla += "<td align='center'  style='width:50px;'><input disabled='disabled' type='checkbox' checked='checked' id='option_reporte_" + nombre_tabla + "'></td>";
                }
                if (datos[i]['obligatorio'] == 0) {
                    tabla += "<td align='center'  style='width:50px;'><input type='checkbox' disabled='disabled'id='option_reporte_" + nombre_tabla + "'></td>";
                }
                tabla += "<td align='center'  style='wdth:100px;'>" + datos[i]['tipodedato'] + "&nbsp;</td>";

                if (datos[i]['isActive'] == 1) {
                    tabla += "<td align='center'  style='width:50px;'><input disabled='disabled' type='checkbox' checked='checked' id='option_isActive_" + nombre_tabla + "'></td></tr>";
                }
                if (datos[i]['isActive'] == 0) {
                    tabla += "<td align='center'  style='width:50px;'><input type='checkbox' disabled='disabled'id='option_isActive_" + nombre_tabla + "'></td></tr>";
                }

                $("#tabla_ColeccionDeDatos").html(tabla);
            }



            $("#tabla_ColeccionDeDatos").dataTable(
                    {//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
                        'destroy': true
                    }
            );

            $("#tabla_ColeccionDeDatos_length").hide();
            $("#tabla_ColeccionDeDatos_paginate").addClass('col-xs-10');
            $("#tabla_ColeccionDeDatos_info").addClass('col-xs-12');
            $("#tabla_ColeccionDeDatos_length").addClass('col-xs-5');
        } else {
            $("#div_ColeccionDeDatos").show();
            $("#div_tabla_ColeccionDeDatos").hide();
            alertify.error("No existen datos para esta Colecci&oacute;n.");
        }
    });
}

/*=============================================================================================*/
/*FUNCION PARA CARGAR INFORMACION DE LA TABLA DE COLECCIONES(NombreColeccion)ColeccionDeDatos  */
/*=============================================================================================*/
function fn_cargarTablaColecciones(nombre_tablaColeccion, nombre_tablaColeccionDeDatos) {
    //alert(nombre_tablaColeccion);
    //alert(nombre_tablaColeccionDeDatos);
    send = {"cargarTablaColecciones": 1};
    send.accion = 5;
    send.nombretabla = nombre_tablaColeccion;
    send.nombre_tablaColeccionDeDatos = nombre_tablaColeccionDeDatos;
    $.getJSON("../adminColeccionesDeDatos/config_coleccionesDeDatos.php", send, function (datos) {
        if (datos.str > 0)
        {
            tabla = "<thead><tr class='active'>";
            tabla += "<th style='width:200px; text-align:center ;'>Colecci&oacute;n</th>";
            tabla += "<th style='width:200px; text-align:center ;'>Colecci&oacute;nDeDatos</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Varchar</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Int</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>DateTime</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Bit</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Decimal</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>FechaIni</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>FechaFin</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Min</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Max</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Id Integraci&oacute;n</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Int Descripci&oacute;n</th>";
            tabla += "<th align='center' style='width:100px; text-align:center;'>Activo</th>";
            tabla += "</tr></thead>";

            for (i = 0; i < datos.str; i++)
            {

                tabla += "<tr class='tabla_detalleMov' id='C" + i + "' style='cursor:pointer;'";
                tabla += "onclick=fn_seleccionclickC(\"C" + i + "\"); ondblclick=fn_seleccion('" + datos[i]['ID_Coleccion'] + "'," + i + ",'" + datos[i]['cdn_id'] + "'," + datos[i]['idIntegracion'] + ");>";
                tabla += "<td align='center'  style='wdth:160px;'>" + datos[i]['DescripcionColeccion'] + "&nbsp;</td>";
                tabla += "<td align='center'  style='wdth:160px;'>" + datos[i]['DescripcionColeccionDeDatos'] + "&nbsp;</td>";
                tabla += "<td align='center'  style='wdth:160px;'>" + datos[i]['variableV'] + "&nbsp;</td>";
                tabla += "<td align='center'  style='wdth:160px;'>" + datos[i]['variableI'] + "&nbsp;</td>";
                tabla += "<td align='center'  style='wdth:160px;'>" + datos[i]['variableD'] + "&nbsp;</td>";
                if (datos[i]['variableB'] == 1) {
                    tabla += "<td align='center'  style='width:169px;'><input disabled='disabled' type='checkbox' checked='checked' id='option_configuracion_" + nombre_tabla + "'></td>";
                }
                if (datos[i]['variableB'] == 0) {
                    tabla += "<td align='center'  style='width:169px;'><input type='checkbox' disabled='disabled'id='option_configuracion_" + nombre_tabla + "'></td>";
                }
                tabla += "<td align='center'  style='wdth:160px;'>" + datos[i]['variableN'] + "&nbsp;</td>";
                tabla += "<td align='center'  style='wdth:160px;'>" + datos[i]['fechaIni'] + "&nbsp;</td>";
                tabla += "<td align='center'  style='wdth:160px;'>" + datos[i]['fechaFin'] + "&nbsp;</td>";
                tabla += "<td align='center'  style='wdth:160px;'>" + datos[i]['min'] + "&nbsp;</td>";
                tabla += "<td align='center'  style='wdth:160px;'>" + datos[i]['max'] + "&nbsp;</td>";
                tabla += "<td align='center'  style='wdth:160px;'>" + datos[i]['idIntegracion'] + "&nbsp;</td>";
                tabla += "<td align='center'  style='wdth:160px;'>" + datos[i]['intDescripcion'] + "&nbsp;</td>";
                if (datos[i]['isActive'] == 1) {
                    tabla += "<td align='center'  style='width:169px;'><input disabled='disabled' type='checkbox' checked='checked' id='option_reporte_" + nombre_tabla + "'></td>";
                }
                if (datos[i]['isActive'] == 0) {
                    tabla += "<td align='center'  style='width:169px;'><input type='checkbox' disabled='disabled'id='option_reporte_" + nombre_tabla + "'></td>";
                }

                $("#tabla_" + nombre_tablaColeccionDeDatos + "").html(tabla);
            }
            $("#tabla_" + nombre_tablaColeccionDeDatos + "").dataTable(
                    {//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
                        'destroy': true
                    }
            );

            $("#tabla_" + nombre_tablaColeccionDeDatos + "_length").hide();
            $("#tabla_" + nombre_tablaColeccionDeDatos + "_paginate").addClass('col-xs-10');
            $("#tabla_" + nombre_tablaColeccionDeDatos + "_info").addClass('col-xs-10');
            $("#tabla_" + nombre_tablaColeccionDeDatos + "_length").addClass('col-xs-6');
        }
    });

}

function fn_accionar(accion, control)
{
    if (accion == 'Nuevo')
    {
        bandera = 1; // bandera para controlar si es nuevo � vamos a modificar un registro
        if (control == 1) { //cuando es nuevo en TABLA A
            $("#txt_descripcion").val('');
            $("#txt_idintegracion").val('');
            $("#txt_iddescripcion").val('');
            $("#txt_estatus1").val('');
            $("#txt_estatus2").val('');
            $("#option_configuracion").removeAttr('checked');
            $("#option_reporte").removeAttr('checked');
            $("#option_rconfiguracion").removeAttr('checked');
            $("#option_cubo").removeAttr('checked');
            //--------MODAL NUEVA CABECERA COLECCIONES--------// 
            name_table = $("#txt_nombreTablaA").val();
            banderaNuevaColeccion = $("#txt_bandera").val();
            name_colection = name_table.substring(9);

            $("#titulomodalNuevo").html("Nueva Colecci&oacute;n " + name_colection + ":");
            ////////////Colocar foco en cualquier campo de la modal////////////////// CP
            $('#ModalNuevo').on('shown.bs.modal', function () {
                $("#txt_descripcion").focus();
            });
            $('#ModalNuevo').modal('show');
        } else if (control == 2) { //cuando es nuevo en TABLA B
            fn_traeColeccionesCabecera();
            fn_traeTipoDeDato();
            $('#selColeccionB').attr('disabled', 'disabled');
            $("#selColeccionB").val('0');
            $("#txt_descripcionB").val('');
            $("#selTipoDato").val('0');
            $("#txt_idintegracionB").val('');
            $("#txt_iddescripcionB").val('');
            $("#txt_estatus1B").val('');
            $("#txt_estatus2B").val('');
            $("#option_obligatorioB").removeAttr('checked');
            $("#option_especificarValorB").removeAttr('checked');
            name_table = $("#txt_nombreTablaA").val();
            banderaNuevaColeccion = $("#txt_bandera").val();
            name_colection = name_table.substring(9);
            $("#titulomodalNuevoB").html("Nueva Colecci&oacute;n " + name_colection + ":");
            ////////////Colocar foco en cualquier campo de la modal////////////////// CP
            $('#ModalNuevoB').on('shown.bs.modal', function () {
                $("#txt_descripcion").focus();
            });
            $('#ModalNuevoB').modal('show');
        }
    }

    if (accion == 'Grabar') {

        if (control == 1) { //grabamos en TABLA A (nuevo y modificar)

            if (bandera == 1) { //nuevo
                ID_Coleccion = 0;
            } else { //modificar
                ID_Coleccion = $("#txt_ID_Coleccion").val();
                filaA = $("#txt_filaA").val();
            }
            descripcion = $("#txt_descripcion").val();
            if (descripcion == '') {
                alertify.error("Ingrese Descripci&oacute;n.");
                return false;
            }
            fn_guardarCabeceraColeccion(ID_Coleccion, filaA);
            $('#ModalNuevo').modal('hide');
        } else if (control == 2) { //grabamos en TABLA B (nuevo y modificar)

            coleccionMod = $("#selColeccionB").val();
            descripcionMod = $("#txt_descripcionB").val();
            tipodedatoMod = $("#selTipoDato").val();
            if (bandera == 1) { //nuevo
                ID_Coleccion = $("#txt_ID_Coleccion").val();
                ID_ColeccionDeDatos = 0;
            } else { //modificar
                ID_Coleccion = $("#txt_ID_Coleccion").val();
                ID_ColeccionDeDatos = $("#txt_ID_ColeccionDeDatos").val();
                filaB = $("#txt_filaB").val();
            }
            if (coleccionMod == '0') {
                alertify.error("Seleccione Colecci&oacute;n.");
                return false;
            }
            if (descripcionMod == '') {
                alertify.error("Ingrese Descripci&oacute;n.");
                return false;
            }
            if (tipodedatoMod == '0') {
                alertify.error("Seleccione Tipo de Dato.");
                return false;
            }
            fn_guardarColeccionDeDatos(ID_Coleccion, ID_ColeccionDeDatos, filaB);
            $('#ModalNuevoB').modal('hide');
        }
    }

    if (accion == 'Modificar') {
        bandera = 0;
        if (control == 1) { //TABLA A
            if (seleccionFilaA == 1) { //si esta seleccionado un registro

                ID_Coleccion = $("#txt_ID_Coleccion").val();
                fn_traeColeccionesModificar(ID_Coleccion);
                $('#ModalNuevo').modal('show');
            } else {
                alertify.error("Seleccione un registro.");
                return false;
            }
        } else if (control == 2) { //TABLA B
            if (seleccionFilaB == 1) { //si esta seleccionado un registro
                bandera = 0;
                ID_Coleccion = $("#txt_ID_Coleccion").val();
                ID_ColeccionDeDatos = $("#txt_ID_ColeccionDeDatos").val();
                fn_traeColeccionDeDatosModificar(ID_Coleccion, ID_ColeccionDeDatos);
                $('#ModalNuevoB').modal('show');
            } else {
                alertify.error("Seleccione un registro.");
                return false;
            }
        } else if (control == 3) { //TABLA 3
            alert('TABLA 3')
        }
    }
}

function fn_guardarCabeceraColeccion(ID_Coleccion, filaA) {

    idintegracion = $("#txt_idintegracion").val();
    iddescripcion = $("#txt_iddescripcion").val();
    estatus1 = $("#txt_estatus1").val();
    estatus2 = $("#txt_estatus2").val();

    send = {"guardarCabeceraColeccion": 1};
    send.accion = 'I';
    send.resultado = '1'
    send.name_table = $("#txt_nombreTablaA").val();
    send.descripcion = $("#txt_descripcion").val();
    send.idintegracion = idintegracion;
    send.iddescripcion = iddescripcion;
    send.ID_Coleccion = ID_Coleccion;
    if (estatus1 == '')
    {
        send.estatus1 = 0;
    } else
    {
        send.estatus1 = estatus1;
    }
    if (estatus2 == '')
    {
        send.estatus2 = 0;
    } else
    {
        send.estatus2 = estatus2;
    }
    if ($("#option_configuracion").is(':checked'))
    {
        send.configuracion = 'Activo';
    } else
    {
        send.configuracion = 'Inactivo';
    }
    if ($("#option_reporte").is(':checked'))
    {
        send.reporte = 'Activo';
    } else
    {
        send.reporte = 'Inactivo';
    }
    if ($("#option_rconfiguracion").is(':checked'))
    {
        send.rconfiguracion = 'Activo';
    } else
    {
        send.rconfiguracion = 'Inactivo';
    }
    if ($("#option_cubo").is(':checked'))
    {
        send.cubo = 'Activo';
    } else
    {
        send.cubo = 'Inactivo';
    }
    if ($("#option_estado").is(':checked'))
    {
        send.estado = 'Activo';
    } else
    {
        send.estado = 'Inactivo';
    }

    $.getJSON("../adminColeccionesDeDatos/config_coleccionesDeDatos.php", send, function (datos) {
        if (bandera == 1) {
            alertify.success("Registro Ingresado correctamente.");
            nombre_tablaColeccion = $("#txt_nombreTablaA").val();
            fn_cargarTablaColeccion(nombre_tablaColeccion);
            seleccionFilaA = -1;
        }
        if (bandera == 0) {
            alertify.success("Registro Actualizado correctamente.");
            nombre_tablaColeccion = $("#txt_nombreTablaA").val();
            fn_cargarTablaColeccion(nombre_tablaColeccion);
            setTimeout(function () {
                _filaSeleccionada(filaA)
            }, 500); //espera 0.5 segundos a que se ejecute la funcion para poner la clase succes a la fila 
        }
    });
}

function fn_guardarColeccionDeDatos(ID_Coleccion, ID_ColeccionDeDatos, filaB) {

    idcoleccion = $("#selColeccionB").val();
    descripcion = $("#txt_descripcionB").val();
    tipodedato = $("#selTipoDato").val();
    idintegracion = $("#txt_idintegracionB").val();
    iddescripcion = $("#txt_iddescripcionB").val();
    estatus1 = $("#txt_estatus1B").val();
    estatus2 = $("#txt_estatus2B").val();
    name_table = $("#txt_nombreTablaB").val();

    send = {"guardarColeccionDeDatos": 1};
    send.accion = 'I';
    send.resultado = '2'
    send.name_table = name_table;
    send.descripcion = descripcion;
    send.idintegracion = idintegracion;
    send.iddescripcion = iddescripcion;
    send.idcoleccion = idcoleccion;
    send.tipodedato = tipodedato;
    send.ID_ColeccionDeDatos = ID_ColeccionDeDatos;
    if (estatus1 == '')
    {
        send.estatus1 = 0;
    } else
    {
        send.estatus1 = estatus1;
    }
    if (estatus2 == '')
    {
        send.estatus2 = 0;
    } else
    {
        send.estatus2 = estatus2;
    }
    if ($("#option_obligatorioB").is(':checked'))
    {
        send.obligatorioB = 'Activo';
    } else
    {
        send.obligatorioB = 'Inactivo';
    }
    if ($("#option_especificarValorB").is(':checked'))
    {
        send.especificarValorB = 'Activo';
    } else
    {
        send.especificarValorB = 'Inactivo';
    }
    if ($("#option_estadoB").is(':checked'))
    {
        send.estado = 'Activo';
    } else
    {
        send.estado = 'Inactivo';
    }

    $.getJSON("../adminColeccionesDeDatos/config_coleccionesDeDatos.php", send, function (datos) {
        if (bandera == 1) {
            alertify.success("Registro Ingresado correctamente.");
            nombre_tablaColeccion = $("#txt_nombreTablaA").val();
            nombre_tablaColeccionDeDatos = $("#txt_nombreTablaB").val();
            fn_cargarTablaColeccionDeDatos(nombre_tablaColeccion, nombre_tablaColeccionDeDatos);
            seleccionFilaB = -1;
        }
        if (bandera == 0) {
            alertify.success("Registro Actualizado correctamente.");
            nombre_tablaColeccion = $("#txt_nombreTablaA").val();
            nombre_tablaColeccionDeDatos = $("#txt_nombreTablaB").val();
            fn_cargarTablaColeccionDeDatos(nombre_tablaColeccion, nombre_tablaColeccionDeDatos);
            setTimeout(function () {
                _filaSeleccionada(filaB)
            }, 500); //espera 0.5 segundos a que se ejecute la funcion para poner la clase succes a la fila 
        }
    });
}

/*=============================================================================================*/
/*FUNCION PARA TRAER LOS TIPOS DE DATOS QUE EXISTEN EN LA TABLA COLECCIONES                    */
/*=============================================================================================*/
function fn_traeTipoDeDato() {

    nombre_TablaB = $("#txt_nombreTablaB").val();
    send = {"traeTipoDeDato": 1};
    send.accion = 6;
    send.nombre_tablaColeccionDeDatos = nombre_TablaB;
    $.getJSON("../adminColeccionesDeDatos/config_coleccionesDeDatos.php", send, function (datos) {
        if (datos.str > 0)
        {
            $("#selTipoDato").html("");
            $('#selTipoDato').html("<option selected value='0'>----Seleccione Tipo de Dato----</option>");
            for (i = 0; i < datos.str; i++)
            {
                html = "<option value='" + datos[i]['ID_DATA_TYPE'] + "'>" + datos[i]['DATA_TYPE'] + "</option>";
                $("#selTipoDato").append(html);
            }
        }
    });
}


/*=============================================================================================*/
/*FUNCION PARA LAS COLECCIONES QUE EXISTEN EN LA TABLA DE CABECERA                             */
/*=============================================================================================*/
function fn_traeColeccionesCabecera() {

    coleccion = $("#txt_nombreTablaA").val();
    send = {"traeColeccionesCabecera": 1};
    send.accion = 7;
    send.nombretabla = coleccion;
    $.getJSON("../adminColeccionesDeDatos/config_coleccionesDeDatos.php", send, function (datos) {
        if (datos.str > 0)
        {
            $("#selColeccionB").html("");
            $('#selColeccionB').html("<option selected value='0'>----Seleccione Colecci&oacute;n----</option>");
            for (i = 0; i < datos.str; i++)
            {
                html = "<option value='" + datos[i]['ID_Coleccion'] + "'>" + datos[i]['Descripcion'] + "</option>";
                $("#selColeccionB").append(html);
            }
            ID_Coleccion = $("#txt_ID_Coleccion").val();
            $("#selColeccionB").val(ID_Coleccion);
        }
    });
}

/*=============================================================================================*/
/*FUNCION PARA TRAER DATOS DEL REGISTRO DE COLECCIONES PARA MODIFICAR                          */
/*=============================================================================================*/
function fn_traeColeccionesModificar(ID_Coleccion) {

    nombre_tablaColeccion = $("#txt_nombreTablaA").val();
    nombre_tablaColeccionDeDatos = $("#txt_nombreTabla").val();
    send = {"traeColeccionesModificar": 1};
    send.accion = 8;
    send.nombretabla = nombre_tablaColeccion;
    send.ID_Coleccion = ID_Coleccion;

    $.getJSON("../adminColeccionesDeDatos/config_coleccionesDeDatos.php", send, function (datos) {
        if (datos.str > 0)
        {
            for (i = 0; i < datos.str; i++)
            {
                $("#titulomodalNuevo").html(datos[i]['Descripcion']);
                $("#txt_descripcion").val(datos[i]['Descripcion']);
                $("#txt_idintegracion").val(datos[i]['idIntegracion']);
                $("#txt_iddescripcion").val(datos[i]['intDescripcion']);
                $("#txt_estatus1").val(datos[i]['estatus1']);
                $("#txt_estatus2").val(datos[i]['estatus2']);
                if (datos[i]['configuracion'] == 1) {
                    $("#option_configuracion").prop("checked", true);  // para poner la marca
                }
                if (datos[i]['configuracion'] == 0) {
                    $("#option_configuracion").prop("checked", false);
                }
                if (datos[i]['reporte'] == 1) {
                    $("#option_reporte").prop("checked", true);  // para poner la marca
                }
                if (datos[i]['reporte'] == 0) {
                    $("#option_reporte").prop("checked", false);
                }
                if (datos[i]['repetirConfiguracion'] == 1) {
                    $("#option_rconfiguracion").prop("checked", true);  // para poner la marca
                }
                if (datos[i]['repetirConfiguracion'] == 0) {
                    $("#option_rconfiguracion").prop("checked", false);
                }
                if (datos[i]['cubo'] == 1) {
                    $("#option_cubo").prop("checked", true);  // para poner la marca
                }
                if (datos[i]['cubo'] == 0) {
                    $("#option_cubo").prop("checked", false);
                }
                if (datos[i]['isActive'] == 1) {
                    $("#option_estado").prop("checked", true);  // para poner la marca
                }
                if (datos[i]['isActive'] == 0) {
                    $("#option_estado").prop("checked", false);
                }
            }
        }
    });
}

/*=============================================================================================*/
/*FUNCION PARA TRAER DATOS DEL REGISTRO DE COLECCION DE DATOS PARA MODIFICAR                 */
/*=============================================================================================*/
function fn_traeColeccionDeDatosModificar(ID_Coleccion, ID_ColeccionDeDatos) {

    nombre_tablaColeccion = $("#txt_nombreTablaA").val();
    nombre_tablaColeccionDeDatos = $("#txt_nombreTablaB").val();
    send = {"traeColeccionDeDatosModificar": 1};
    send.accion = 9;
    send.nombretabla = nombre_tablaColeccion;
    send.nombre_tablaColeccionDeDatos = nombre_tablaColeccionDeDatos;
    send.ID_Coleccion = ID_Coleccion;
    send.ID_ColeccionDeDatos = ID_ColeccionDeDatos;

    $.getJSON("../adminColeccionesDeDatos/config_coleccionesDeDatos.php", send, function (datos) {
        if (datos.str > 0)
        {
            for (i = 0; i < datos.str; i++)
            {
                $("#titulomodalNuevoB").html(datos[i]['Descripcion']);
                coleccion = $("#txt_nombreTablaA").val();
                ID_Coleccion = datos[i]['ID_Coleccion'];
                //Coleccion//
                send = {"traeColeccionesCabecera": 1};
                send.accion = 7;
                send.nombretabla = coleccion;
                $.getJSON("../adminColeccionesDeDatos/config_coleccionesDeDatos.php", send, function (datos) {
                    if (datos.str > 0)
                    {
                        $("#selColeccionB").html("");
                        $('#selColeccionB').html("<option selected value='0'>----Seleccione Colecci&oacute;n----</option>");
                        for (i = 0; i < datos.str; i++)
                        {
                            html = "<option value='" + datos[i]['ID_Coleccion'] + "'>" + datos[i]['Descripcion'] + "</option>";
                            $("#selColeccionB").append(html);
                        }
                        $("#selColeccionB").val(ID_Coleccion);
                    }
                });
                $('#selColeccionB').attr('disabled', 'disabled');
                //Fin Coleccion//				
                $("#txt_descripcionB").val(datos[i]['Descripcion']);

                //Tipo de Dato//
                tipodedato = datos[i]['tipodedato'];
                send = {"traeTipoDeDato": 1};
                send.accion = 6;
                send.nombre_tablaColeccionDeDatos = nombre_tablaColeccionDeDatos;
                $.getJSON("../adminColeccionesDeDatos/config_coleccionesDeDatos.php", send, function (datos) {
                    if (datos.str > 0)
                    {
                        $("#selTipoDato").html("");
                        for (i = 0; i < datos.str; i++)
                        {
                            html = "<option value='" + datos[i]['ID_DATA_TYPE'] + "'>" + datos[i]['DATA_TYPE'] + "</option>";
                            $("#selTipoDato").append(html);
                        }
                        $("#selTipoDato").val(tipodedato);
                    }
                });
                //Fin Tipo de Dato//
                $("#txt_idintegracionB").val(datos[i]['idIntegracion']);
                $("#txt_iddescripcionB").val(datos[i]['intDescripcion']);
                $("#txt_estatus1B").val(datos[i]['estatus1']);
                $("#txt_estatus2B").val(datos[i]['estatus2']);
                if (datos[i]['obligatorio'] == 1) {
                    $("#option_obligatorioB").prop("checked", true);  // para poner la marca
                }
                if (datos[i]['obligatorio'] == 0) {
                    $("#option_obligatorioB").prop("checked", false);
                }
                if (datos[i]['especificarValor'] == 1) {
                    $("#option_especificarValorB").prop("checked", true);  // para poner la marca
                }
                if (datos[i]['especificarValor'] == 0) {
                    $("#option_especificarValorB").prop("checked", false);
                }
                if (datos[i]['isActive'] == 1) {
                    $("#option_estadoB").prop("checked", true);  // para poner la marca
                }
                if (datos[i]['isActive'] == 0) {
                    $("#option_estadoB").prop("checked", false);
                }
            }
        }
    });
}
