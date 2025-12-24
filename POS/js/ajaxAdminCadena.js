/*variables globales*/
/* global alertify */

var lc_idColeccionCadena = '';
var lc_nombreColeccion = '';
var lc_coleccionDatosCadena = '';
var lc_especifica = -1;
var lc_obligatorio = -1;
var lc_tipoDato = '';
var lc_idCadenaColeccion = '';
var lc_nombreColeccionDatos = '';
var lc_fila_seleccionada = 0;
/****************************/

$(document).ready(function () {
    $("#sel_seleccione").bootstrapSwitch('state', false);
    $("#sel_seleccioneM").bootstrapSwitch('state', false);
    fn_cargarCadena();
    fn_cargaColeccionTransferenciaVentas('Activos');
    $("#mdl_rdn_pdd_crgnd").hide();
});

function fn_cargarCadena() {
   
    var send;
    var cargaCadena = {"cargaCadena": 1};
    var html = '<thead><tr class="bg-primary"><th>Descripci&oacute;n</th><th class="text-center">Dato</th><th>Especifica Valor</th><th>Obligatorio</th><th class="text-center">Tipo de Dato</th><th class="text-center">Valor</th><th>Activo</th></tr></thead>';
    send = cargaCadena;
    send.accion = 1;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminCadena/configAdminCadena.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var valorPolitica = evaluarValorPolitica(datos[i]);
                    html += '<tr onclick="fn_seleccionColeccion(' + i + ',\'' + datos[i]["descripcion_coleccion"] + '\',\'' + datos[i]["ID_ColeccionCadena"] + '\',\'' + datos[i]["ID_ColeccionDeDatosCadena"] + '\',\'' + datos[i]["descripcion_dato"] + '\')" id="' + i + '_pc" class="text-center"><td>' + datos[i]["descripcion_coleccion"] + '</td><td>' + datos[i]["descripcion_dato"] + '</td>';
                    if (datos[i]["especificarValor"] === 1) {
                        html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } else {
                        html += '<td><input type="checkbox" value="1" disabled/></td>';
                    }
                    if (datos[i]["obligatorio"] === 1) {
                        html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } else {
                        html += '<td><input type="checkbox" value="1" disabled/></td>';
                    }
                    html += '<td>' + datos[i]["tipodedato"] + '</td><td>' + valorPolitica + '</td>';

                    if (datos[i]["isActive"] === 1) {
                        html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } else {
                        html += '<td><input type="checkbox" value="1" disabled/></td>';
                    }
                    html += '</tr>';
                }
                $("#listaCadenas").html(html);
                $("#listaCadenas").dataTable({"destroy": true});
                $("#listaCadenas_length").hide();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }});
}

function evaluarValorPolitica(fila) {
    var tipoDato = fila.tipodedato.trim().toLowerCase();
    switch (tipoDato) {
        case 'entero':
            return fila.entero;
            break;
        case 'caracter':
            return fila.caracter;
            break;
        case 'seleccion':
            return fila.seleccion;
            break;
        case 'numerico':
            return fila.numerico;
            break;
        case 'fecha':
            return fila.fecha
            break;
        case 'fecha inicio-iin':
            return fila.fechaIni + ' - ' + fila.fechaFin;
            break;
        case 'minimo-maximo':
            return fila.min + ' - ' + fila.max;
            break;
        default:
            return fila.caracter;
            break;
        //code block
    }
}
function fn_accionar(accion) {
    if (accion == 'Nuevo') {
        fn_cargaColeccionDatosTabla();
    } else if (accion == 'Modificar') {
        if (lc_idColeccionCadena == '') {
            alertify.error('Seleccione una coleccion..');
            return false;
        }
        fn_modificarColeccion();
    }
}

function fn_modificarColeccion() {

    var send;
    var editarColeccionDeDatos = {"editarColeccionDeDatos": 1};

    send = editarColeccionDeDatos;
    send.accion = 4;
    send.idColeccioncadenaM = lc_idColeccionCadena;
    send.idDatosColeccionM = lc_coleccionDatosCadena;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminCadena/configAdminCadena.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                if (datos.especificarValor == 1) {
                    $("#check_especificaM").prop("checked", true);
                } else {
                    $("#check_especificaM").prop("checked", false);
                }

                if (datos.obligatorio == 1) {
                    $("#check_obligatorioM").prop("checked", true);
                } else {
                    $("#check_obligatorioM").prop("checked", false);
                }

                $("#lbl_tipoDatoM").text(datos.tipodedato);
                $('#txt_fechaSImpleM').daterangepicker({minDate: moment(), singleDatePicker: true, format: "DD/MM/YYYY", drops: "up"}, function (start, end, label) {});
                $('#txt_fechaInicioM').daterangepicker({minDate: moment(), singleDatePicker: true, format: "DD/MM/YYYY", drops: "up"}, function (start, end, label) {});
                $('#txt_fechaFinM').daterangepicker({minDate: moment(), singleDatePicker: true, format: "DD/MM/YYYY", drops: "up"}, function (start, end, label) {});
                $("#txt_caracterM").val(datos.caracter);
                $("#txt_enteroM").val(datos.entero);
                $("#txt_fechaSImpleM").val(datos.fecha);

                if (datos.seleccion == 1) {
                    $("#sel_seleccioneM").bootstrapSwitch("state", true)
                } else {
                    $("#sel_seleccioneM").bootstrapSwitch("state", false)
                }

                $("#txt_numericoM").val(datos.numerico);
                $("#txt_fechaInicioM").val(datos.fechaInicio);
                $("#txt_fechaFinM").val(datos.fechaFin);
                $("#txt_minimoM").val(datos.minimo);
                $("#txt_maximoM").val(datos.maximo);

                if (datos.activo == 1) {
                    $("#check_activo").prop("checked", true);
                } else {
                    $("#check_activo").prop("checked", false);
                }

                $("#lblNombreColeccionModificar").text(lc_nombreColeccionDatos);
                $("#mdl_editaColeccion").modal("show");
            } else {
                alertify.error("No existen datos.");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.alert(jqXHR);
            alertify.alert(textStatus);
            alertify.alert(errorThrown);
        }});
}

function fn_cargaColeccionDatosTabla() {

    var send;
    var cargaColeccionDeDatos = {"cargaColeccionDeDatos": 1};

    var html = '<tr class="bg-primary"><th>Descripci&oacute;n</th></tr>';
    send = cargaColeccionDeDatos;
    send.accion = 2;
    send.idColeccioncadena = "0";
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminCadena/configAdminCadena.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html += '<tr id="' + i + '_c" onclick="fn_seleccionColeccionCadena(' + i + ',\'' + datos[i]["ID_ColeccionCadena"] + '\')">';
                    html += '<td>' + datos[i]["Descripcion"] + '</td>';
                    html += '</tr>';
                }
                $("#listaColecciones").html(html);
                $("#lblNombreColeccion").text(lc_nombreColeccion);
                $("#div_caracteristicas").hide();
                $("#mdl_nuevaColeccion").modal("show");
            } else {
                alertify.error("No existen colecciones que agregar.");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }});
}

function fn_seleccionColeccion(indice, nombreColeccion, idColeccion, idDatos, nombreDatos) {
     
    $("#listaCadenas tr").removeClass("success");
    $("#" + indice + "_pc").addClass("success");
    lc_idColeccionCadena = idColeccion;
    lc_nombreColeccion = nombreColeccion;
    lc_coleccionDatosCadena = idDatos;
    lc_nombreColeccionDatos = nombreDatos;
    
}

function fn_seleccionColeccionDeDatosCadena(indice2, idColeccion, idColeccionDatos, especifica, obligatorio, tipoDato) {
    $("#lista_datos tr").removeClass("success");
    $("#" + indice2 + "_cd").addClass("success");
    lc_coleccionDatosCadena = idColeccionDatos;
    lc_especifica = especifica;
    lc_obligatorio = obligatorio;
    lc_tipoDato = tipoDato;
    fn_cargaCaracteristicas();
}

function fn_seleccionColeccionCadena(indice3, idColeccionCadena) {
    $("#listaColecciones tr").removeClass("success");
    $("#" + indice3 + "_c").addClass("success");
    lc_idColeccionCadena = idColeccionCadena;
    fn_cargaColeccionDatosCadena();
    $("#div_caracteristicas").hide();
}

function fn_cargaColeccionDatosCadena() {

    var send;
    var cargaColeccionDatosC = {"cargaColeccionDatosC": 1};

    var html = '<tr class="bg-primary"><th>Descripci&oacute;n</th><th>Datos</th></tr>';
    send = cargaColeccionDatosC;
    send.accion = 3;
    send.idColeccionCadenaa = lc_idColeccionCadena;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminCadena/configAdminCadena.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                $("#_detalle_restaurante_coleccion").show();
                for (var i = 0; i < datos.str; i++) {
                    html += '<tr id="' + i + '_cd" onclick="fn_seleccionColeccionDeDatosCadena(' + i + ',\'' + datos[i]["ID_ColeccionCadena"] + '\',\'' + datos[i]["ID_ColeccionDeDatosCadena"] + '\',' + datos[i]["especificarValor"] + ',' + datos[i]["obligatorio"] + ',\'' + datos[i]["tipodedato"] + '\')">';
                    html += '<td>' + datos[i]["Descripcion"] + '</td><td>' + datos[i]["dato"] + '</td>';
                    html += '</tr>';
                }
                $("#lista_datos").html(html);
            } else {
                html += '<tr><td colspan=2>No existen registros.</td></tr>';
                $("#lista_datos").html(html);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }});
}

function fn_cargaCaracteristicas() {

    if (lc_especifica == 1) {
        $("#check_especifica").prop("checked", true)
    } else {
        $("#check_especifica").prop("checked", false)
    }

    if (lc_obligatorio == 1) {
        $("#check_obligatorio").prop("checked", true)
    } else {
        $("#check_obligatorio").prop("checked", false)
    }

    $("#lbl_tipoDato").text(lc_tipoDato);
    $('#txt_fechaSImple').daterangepicker({minDate: moment(), singleDatePicker: true, format: "DD/MM/YYYY", drops: "up"}, function (start, end, label) {});
    $('#txt_fechaInicio').daterangepicker({minDate: moment(), singleDatePicker: true, format: "DD/MM/YYYY", drops: "up"}, function (start, end, label) {});
    $('#txt_fechaFin').daterangepicker({minDate: moment(), singleDatePicker: true, format: "DD/MM/YYYY", drops: "up"}, function (start, end, label) {});
    $("#div_caracteristicas").show();
}

function fn_guardarColeccion() {

    var send;
    var grabaCadenaColeccionDatos = {"grabaCadenaColeccionDatos": 1};

    var estado = $("#sel_seleccione").bootstrapSwitch("state");
    var seleccion = 0;
    if (estado) {
        seleccion = 1;
    }
    send = grabaCadenaColeccionDatos;
    send.accion = "I";
    send.idColeccionDatoscadena = lc_coleccionDatosCadena;
    send.caracter = $("#txt_caracter").val();
    send.entero = $("#txt_entero").val();
    send.fecha = $("#txt_fechaSImple").val();
    send.numerico = $("#txt_numerico").val();
    send.fechaInicio = $("#txt_fechaInicio").val();
    send.fechaFin = $("#txt_fechaFin").val();
    send.minimo = $("#txt_minimo").val();
    send.maximo = $("#txt_maximo").val();
    send.idColeccionCadena = lc_idColeccionCadena;
    send.seleccione = seleccion;
    send.isactive = 1;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminCadena/configAdminCadena.php", data: send, success: function (datos) {
            if (datos.str == 1) {
                $("#mdl_nuevaColeccion").modal("hide");
                fn_cargarCadena();
                $(".limpiar").val("");
                $("#_detalle_restaurante_coleccion").hide();
                alertify.success("Datos guardados correctamente.");
            } else {
                var error = datos.str;
                error = error.substr(54, 40);
                alertify.error(error);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }});
}

function fn_actualizaColeccion() {

    var send;
    var actualizaCadenaColeccionDatos = {"actualizaCadenaColeccionDatos": 1};

    var estado = $("#sel_seleccioneM").bootstrapSwitch("state");
    var seleccion = 0;
    if (estado) {
        seleccion = 1;
    }
    var activo = 0;
    if ($("#check_activo").is(":checked")) {
        activo = 1;
    }

    if ($("#txt_enteroM").val() === '') {
        fn_guardarStorageIntentosNull();
    } 

    send = actualizaCadenaColeccionDatos;
    send.accion = "A";
    send.idColeccionDatoscadenaM = lc_coleccionDatosCadena;
    send.caracterM = $("#txt_caracterM").val();
    send.enteroM = $("#txt_enteroM").val();
    send.fechaM = $("#txt_fechaSImpleM").val();
    send.numericoM = $("#txt_numericoM").val();
    send.fechaInicioM = $("#txt_fechaInicioM").val();
    send.fechaFinM = $("#txt_fechaFinM").val();
    send.minimoM = $("#txt_minimoM").val();
    send.maximoM = $("#txt_maximoM").val();
    send.idColeccionCadenaM = lc_idColeccionCadena;
    send.seleccioneM = seleccion;
    send.isactive = activo;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminCadena/configAdminCadena.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                $("#mdl_editaColeccion").modal("hide");
                fn_cargarCadena();
                $(".limpiar").val("");
                alertify.success("Datos actualizados correctamente.");
            } else {
                var error = datos.str;
                error = error.substr(54, 40);
                alertify.error(error);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }});
}

function sincronizarCadenas() {

    var send;
    send = {};
    send.metodo = "sincronizarCadenas";
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminCadena/consumoWSCadenas.php", data: send, success: function (datos) {
            alertify.success("Sincronización realizada exitosamente");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error("Sincronización no pudo realizarse");
        }});
}

function fn_OpcionSeleccionada(ls_opcion) {
   $("#mdl_rdn_pdd_crgnd").show();
 
    var estadoColeccion;
    if (ls_opcion === 'Todos') {
        estadoColeccion = ls_opcion;
        fn_cargaColeccionTransferenciaVentas(estadoColeccion);
    } else if (ls_opcion === 'Activos') {
        estadoColeccion = ls_opcion;
        fn_cargaColeccionTransferenciaVentas(estadoColeccion);
    } else if (ls_opcion === 'Inactivos') {
        estadoColeccion = ls_opcion;
        fn_cargaColeccionTransferenciaVentas(estadoColeccion);
    }
    $("#mdl_rdn_pdd_crgnd").hide();

}

//FUNCION QUE CARGA LA COLECCIÓN DE TRANSFERENCIA DE VENTAS CADENA
function fn_cargaColeccionTransferenciaVentas(estadoColeccion) {
  
    var send;
    var cargaColeccionDeDatosT = {"cargaColeccionDeDatosT": 1};
    var html = '<thead><tr class="bg-primary"><th style="width:;">DESCRIPCI&Oacute;N</th><th>ORIGEN</th><th>DESTINO</th><th>ACTIVO</th></tr></thead>';
    send = cargaColeccionDeDatosT;
    send.accion = 'T';
    send.estado = estadoColeccion;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminCadena/configAdminCadena.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                
                
                for (var i = 0; i < datos.str; i++) {
                    html += '<tr class="cursor:pointer;" id=' + i + ' onclick="fn_seleccionclick(' + i + ',\'' + datos[i]["ID_ColeccionCadena"] + '\', \'' + datos[i]["ID_ColeccionDeDatosCadena"] + '\')">';
                    html += '<td>' + datos[i]["descripcion_coleccion"] + '</td>';
                    html += '<td>' + datos[i]["origen"] + '</td>';
                    html += '<td>' + datos[i]["destino"] + '</td>';
                    if (datos[i]['isActive'] === '1') {
                        html += "<td align='center'><input disabled='disabled' type='checkbox' checked='checked'></td>";
                    } else if (datos[i]['isActive'] === '0') {
                        html += "<td align='center'><input type='checkbox' disabled='disabled'></td>";
                    }
                    html += '</tr>';
                  
                    var descripcion_coleccion = datos[i]["descripcion_coleccion"];
                }
                
                 
                $("#tabla_transferenciaVentas").html(html);
                
               
                $("#tabla_transferenciaVentas").dataTable({"destroy": true});
                
                if (estadoColeccion === 'Activos') {
                  //  $("#tabla_transferenciaVentas_length").html('<button class="btn btn-primary" onclick="fn_inactivarTransferenciaVentas(0)" id="btnInactivar" disabled="true">Inactivar &nbsp;<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>');
                // $("#tabla_transferenciaVentas_length").html('<button class="btn btn-primary" onclick="fn_modalTransferenciaVentas( \'' + descripcion_coleccion + '\')">Agregar &nbsp;<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>&nbsp;&nbsp;&nbsp;<button class="btn btn-primary" onclick="fn_inactivarTransferenciaVentas(0)" id="btnInactivar" disabled="true">Inactivar &nbsp;<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>');

                
                } else if (estadoColeccion === 'Inactivos') {
                 //   $("#tabla_transferenciaVentas_length").html('<button class="btn btn-primary" onclick="fn_inactivarTransferenciaVentas(1)" id="btnActiv" disabled="true">Activar &nbsp;<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>');
                } else if (estadoColeccion === 'Todos') {
                   // $("#tabla_transferenciaVentas_length").html('<button class="btn btn-primary" onclick="fn_modalTransferenciaVentas( \'' + descripcion_coleccion + '\')">Agregar &nbsp;<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>');
                }

   $("#btnAgregarTransferenciaCadena").click( function (){
      
      
                
      
      
                  fn_modalTransferenciaVentas(descripcion_coleccion);
   }) ;


            } else {
                html += '<tr class="cursor:pointer;">';
                html += '<td>No existen registros..</td>';
                html += '<td></td>';
                html += '<td></td>';
                html += '<td></td>';
                html += '</tr>';

                $("#tabla_transferenciaVentas").html(html);
                $("#tabla_transferenciaVentas").dataTable({"destroy": true});
                if (estadoColeccion === 'Activos') {
                  //  $("#tabla_transferenciaVentas_length").html('<button class="btn btn-primary" onclick="fn_modalTransferenciaVentas( \'' + descripcion_coleccion + '\')">Agregar &nbsp;<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>');
                } else if (estadoColeccion === 'Inactivos') {
                    $("#tabla_transferenciaVentas_length").html('');
                } else if (estadoColeccion === 'Todos') {
                   // $("#tabla_transferenciaVentas_length").html('<button class="btn btn-primary" onclick="fn_modalTransferenciaVentas( \'' + descripcion_coleccion + '\')">Agregar &nbsp;<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>');
                }
                
                
                
                 $("#btnAgregarTransferenciaCadena").click( function (){
                  fn_modalTransferenciaVentas( descripcion_coleccion);
                 }) ;
                
                
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error("Consulta no pudo realizarse, verificar si la coleccion TRANSFERENCIA DE VENTA CADENA existe");
        }});
}

//FUNCIÓN QUE MUESTRA LA MODAL PARA REALIZAR LA TRANSFERENCIA DE VENTAS DE CADENA A CADENA
function fn_modalTransferenciaVentas(descripcion_coleccion) {
//    alert($("#sess_cdn_descripcion").val());
//    alert($('#sess_cdn_id').val());



    if (descripcion_coleccion  === undefined) {
     descripcion_coleccion ='Nueva configuración';
    }

    $("[name='option']").bootstrapSwitch();
    $("#txt_cadena_origen").val($("#sess_cdn_descripcion").val());
    fn_cargarCadenasDestino();
    /*
     $("#sel_cadena_origen").change(function () {
     var cdn_id = $("#sel_cadena_origen").val();
     fn_cargarCadenasDestino();
     $("#sel_cadena_destino").find("option[value='" + cdn_id + "']").remove();
     });
     */
    $("#tituloModalNuevaColeccion").html('<span class="glyphicon glyphicon-sort" aria-hidden="true"></span>&nbsp;' + descripcion_coleccion);
    $("#modalTransferenciaVentas").modal("show");
}

//FUNCIÓN QUE CARGA LAS CADENAS ORIGEN
function fn_cargarCadenasOrigen() {
    var send;
    var cargarCadenas = {"cargarCadenas": 1};
    send = cargarCadenas;
    send.accion = 'C';
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminCadena/configAdminCadena.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                $("#sel_cadena_origen").html("");
                $('#sel_cadena_origen').html("<option selected value='0'>------SELECCIONE CADENA------</option>");
                for (i = 0; i < datos.str; i++)
                {
                    html = "<option value='" + datos[i]['cdn_id'] + "'>" + datos[i]['cdn_descripcion'] + "</option>";
                    $("#sel_cadena_origen").append(html);
                }
                $("#sel_cadena_origen").change(function () {
                    var cdn_id = $("#sel_cadena_origen").val();
                    $("#txt_hidden_cdn_id").val(cdn_id);
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error("Consulta no pudo realizarse");
        }});
}

//FUNCIÓN QUE CARGA LAS CADENAS DESTINO
function fn_cargarCadenasDestino() {
    var send;
    var cargarCadenas = {"cargarCadenas": 1};
    send = cargarCadenas;
    send.accion = 'C';
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminCadena/configAdminCadena.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                $("#sel_cadena_destino").html("");
                $('#sel_cadena_destino').html("<option selected value='0'>------SELECCIONE CADENA------</option>");
                for (i = 0; i < datos.str; i++)
                {
                    html = "<option value='" + datos[i]['cdn_id'] + "'>" + datos[i]['cdn_descripcion'] + "</option>";
                    $("#sel_cadena_destino").append(html);
                }
                $("#sel_cadena_destino").change(function () {
                    var cdn_id = $("#sel_cadena_destino").val();
                    $("#txt_hidden_cdn_id").val(cdn_id);
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error("Consulta no pudo realizarse");
        }});
}

//FUNCIÓN QUE GUARDA EL REGISTRO DE TRANSFERENCIA DE VENTAS 
function fn_guardarTransferenciaVentas() {

    if ($("#sel_cadena_origen").val() === '0')
    {
        alertify.error("ALERTA!! Seleccione cadena origen de ventas.");
        return false;
    }
    if ($("#sel_cadena_destino").val() === '0')
    {
        alertify.error("ALERTA!! Seleccione cadena destino de ventas.");
        return false;
    }
//
    var send;
    var guardarTransferenciaVentas = {"guardarTransferenciaVentas": 1};
    send = guardarTransferenciaVentas;
    send.accion = 'I';   
    
    send.cdn_id_origen = $("#sess_cdn_id").val();
    send.cdn_id_destino = $("#sel_cadena_destino").val();
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminCadena/configAdminCadena.php", data: send, success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++)
                {
                    if (datos[i]['respuesta'] === '1') {

                        alertify.success(datos[i]['mensaje']);
                       // fn_cargaColeccionTransferenciaVentas();
                        $("#modalTransferenciaVentas").modal("hide");
                    } else if (datos[i]['respuesta'] === '0') {
                        alertify.error(datos[i]['mensaje']);
                        return false;
                    }
                }
            }
            //Recargar la tabla.
            fn_OpcionSeleccionada('Activos');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error("Registro no se pudo guardar");
        }});

}

function fn_seleccionclick(fila, ID_ColeccionCadena, ID_ColeccionDeDatosCadena) {

    lc_fila_seleccionada = 1;
    $("#txt_hidden_ID_ColeccionCadena").val(ID_ColeccionCadena);
    $("#txt_hidden_ID_ColeccionDeDatosCadena").val(ID_ColeccionDeDatosCadena);
    $("#tabla_transferenciaVentas tr").removeClass("success");
    $("#" + fila + "").addClass("success");
    //Habilitar botones al seleccionar una fila de la tabla.
    try {
        document.getElementById('btnInactivar').disabled = false;
    } catch (err) {
        document.getElementById('btnActiv').disabled = false;
    }


}

function fn_inactivarTransferenciaVentas(estado_coleccion) {

    if (lc_fila_seleccionada === 0) {
        alertify.error('Seleccione un registro');
        return false;
    }
    var send;
    var inactivarTransferencia = {"inactivarTransferenciaVentas": 1};

    send = inactivarTransferencia;
    send.accion = 'U';
    send.ID_ColeccionCadena = $("#txt_hidden_ID_ColeccionCadena").val();
    send.ID_ID_ColeccionDeDatosCadena = $("#txt_hidden_ID_ColeccionDeDatosCadena").val();
    send.estado = estado_coleccion;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../adminCadena/configAdminCadena.php", data: send, success: function (datos) {

            if (datos.str > 0) {


                for (i = 0; i < datos.str; i++)
                {
                    if (datos[i]['respuesta'] === '1') {

                        alertify.success(datos[i]['mensaje']);
                        if (estado_coleccion === 0) {
                            fn_cargaColeccionTransferenciaVentas('Activos');
                        } else if (estado_coleccion === 1) {
                            fn_cargaColeccionTransferenciaVentas('Inactivos');
                        }
                    }
                }

                // CargarTabla Segun opción seleccionada  por el usuario.
                if (estado_coleccion === 0) {
                    fn_OpcionSeleccionada('Activos');
                } else if
                        (estado_coleccion === 1) {
                    fn_OpcionSeleccionada('Inactivos');
                }


            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error("Registro no se pudo actualizar");
        }});
}

function fn_guardarStorageIntentos(txt_enteroM) {
    var x = $("#txt_enteroM").val();
    localStorage.removeItem("intValida");
    localStorage.setItem("intValida", x);
    localStorage.setItem("intValidaU", x);
    localStorage.setItem("intValidaOP", x);
    localStorage.setItem("intValidaFC", x);
    localStorage.setItem("intValidaCC", x);
    //////LOCAL STORAGE BASE PARA GUARDAR EMAILS Y VALIDAR
    var array_emails = ["email@email.com"];
    localStorage.setItem('email_valid', JSON.stringify(array_emails));
    console.log('ya guardo en storae')
}

function fn_guardarStorageIntentosNull() {
    localStorage.removeItem("intValida");
    localStorage.setItem("intValida", 1);
    localStorage.setItem("intValidaU", 1);
    localStorage.setItem("intValidaOP", 1);
    localStorage.setItem("intValidaFC", 1);
    localStorage.setItem("intValidaCC", 1);
    //////LOCAL STORAGE BASE PARA GUARDAR EMAILS Y VALIDAR
    var array_emails = ["email@email.com"];
    localStorage.setItem('email_valid', JSON.stringify(array_emails));
    console.log('ya guardo en storae')
}
