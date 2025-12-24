/* 
 * Daniel Llerema
 * 30/07/2018
 * Colección de datos usuarios
 */

/* global alertify */
var GLOBAL_IDCOLECCIONUSUARIO = "";
var GLOBAL_DECRIPCIONCOLECCION = "";
var GLOBAL_IDCOLECCIONDEDATOSUSUARIO = "";
var GLOBAL_PARAMETRODESCRIPCION = "";
var GLOBAL_ESPECIFICARVALOR = 0;
var GLOBAL_OBLIGATORIO = 0;
var GLOBAL_TIPODEDATO = "";
var GLOBAL_CARACTER = "";
var GLOBAL_ENTERO = "";
var GLOBAL_FECHA = "";
var GLOBAL_SELECCION = 0;
var GLOBAL_NUMERICO = "";
var GLOBAL_FECHAINI = "";
var GLOBAL_FECHAFIN = "";
var GLOBAL_MIN = "";
var GLOBAL_MAX = "";
var GLOBAL_ISACTIVE = 0;


$(document).ready(function () {
//    $("#modalTipoDatos_AgregarColeccionUsuario").modal("hide");
//    $("#modalTipoDatos_AgregarColeccionUsuario").hide();
});


function agregarColeccionUsuario() {
    $("#modalAgregarColeccionUsuario").modal("show");
    $("#mdl_usr").modal("hide");     
    $("#coleccion_datos").hide();
    
    detalleColeccionUsuarios();    
}

function limpiarCampos() {
    $("#tipo_varchar").val("");
    $("#tipo_entero").val("");
    $("#tipo_fecha").val("");
    $("#tipo_numerico").val("");
    $("#FechaInicial").val("");
    $("#FechaFinal").val("");
    $("#rango_minimo").val("");
    $("#rango_maximo").val("");
}

function cerrarModalAgregar() {
    $("#mdl_usr").modal("show");
    $("#modalAgregarColeccionUsuario").modal("hide");  
}

function detalleColeccionUsuarios() {
    var html = "";
    var send;
    send = {"detalleColeccionUsuarios": 1};    
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminUsuario/config_coleccionUsuarios.php", data: send, success: function (datos){
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                html += '<tr id="'+i+'idColeccionUsuarios'+'" onclick="seleccionarDetalleColeccion('+i+',\''+datos[i]['ID_ColeccionUsuarios']+'\',\''+datos[i]['Descripcion']+'\')"  class="text-left"><td>' + datos[i]['Descripcion'] + '</td>';
                html += '</tr>';
            }
            $("#coleccion_descripcion").html(html);
        } else {
            html = html + '<tr><th colspan="6" class="text-center">No existen registros.</th></tr>';
            $("#coleccion_descripcion").html(html);
        }
    }});
}

function seleccionarDetalleColeccion(filaTabla, idColeccionUsuarios, descripcion) {
    $("#coleccion_descripcion tr").removeClass("success");
    $("#"+filaTabla+'idColeccionUsuarios'+"").addClass("success");    
    $("#nombreColeccion").text(descripcion);
    $("#idColeccionUsuarios").val(idColeccionUsuarios);    
    $("#coleccion_datos").show();
   
    detalleColeccionDeDatosUsuario(idColeccionUsuarios);
}

function detalleColeccionDeDatosUsuario(idColeccionUsuarios) {
    var html = "";
    var send;
    send = {"detalleColeccionDeDatosUsuarios": 1}; 
    send.idColeccionUsuarios = idColeccionUsuarios;
    send.idUsuario = $("#hdn_usr_id").val();
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminUsuario/config_coleccionUsuarios.php", data: send, success: function (datos){
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                html += '<tr id="'+i+'idColeccionDeDatosUsuarios'+'" onclick="seleccionarDetalleColeccionDeDatos('+i+',\''+datos[i]['ID_ColeccionDeDatosUsuarios']+'\',\''+datos[i]['Descripcion']+'\',\''+datos[i]['especificarValor']+'\',\''+datos[i]['obligatorio']+'\',\''+datos[i]['tipodedato']+'\')"  class="text-left"><td>' + datos[i]['Descripcion'] + '</td>';
                html += '</tr>';
            }
            $("#coleccion_datos").html(html);
        } else {
            html = html + '<tr><th colspan="6" class="text-center">No existen registros.</th></tr>';
            $("#coleccion_datos").html(html);
        }
    }});
}

function seleccionarDetalleColeccionDeDatos(filaTabla, idColeccionDeDatosUsuarios, descripcion, especificarValor, obligatorio, tipodedato){
    $("#coleccion_datos tr").removeClass("info");
    $("#"+filaTabla+'idColeccionDeDatosUsuarios'+"").addClass("info");    
    $("#nombreParametro").text(descripcion);
    $("#idColeccionDeDatosUsuarios").val(idColeccionDeDatosUsuarios);
    
    $("#modalTipoDatos_AgregarColeccionUsuario").modal("show");
    $("#modalAgregarColeccionUsuario").modal("hide");
    $("#mdl_usr").modal("hide");
    
    cargarParametrosColeccionDeDatos(especificarValor, obligatorio, tipodedato);
}

function cerraModalTipoDatos(opcion) {
    
    if (opcion == 1) {
        $("#modalAgregarColeccionUsuario").modal("show");
        $("#modalTipoDatos_AgregarColeccionUsuario").modal("hide"); 
    } else {
        $("#mdl_usr").modal("show");
        $("#modalTipoDatos_AgregarColeccionUsuario").modal("hide");
    }
    
}

function cargarParametrosColeccionDeDatos(especificarValor, obligatorio, tipodedato) {
    
    limpiarCampos();
    
    $("#check_estado").prop('disabled', true);
    
    if (especificarValor == 1) {
        $("#check_especifica").prop("checked", true);
    } else {   
        $("#check_especifica").prop("checked", false);
    }

    if (obligatorio == 1) {
        $("#check_obligatorio").prop("checked", true);
    } else {
        $("#check_obligatorio").prop("checked", false);
    }

    $("#lbl_tipoDato").text(tipodedato);
    
    $("#tipo_bit").bootstrapSwitch("state", true);
    
    $('#tipo_fecha').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY HH:mm', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });
    
    $('#FechaInicial').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY HH:mm', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });

    $('#FechaFinal').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY HH:mm', drops: 'up'}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });
    
    $("#btn_accion").html('<button type="button" class="btn btn-default" data-dismiss="modal" onclick="cerraModalTipoDatos(1);">Cancelar</button><button type="button" class="btn btn-primary" onclick="guardarUsauriosColeccionDeDatos(1);">Guardar</button>');
}

function guardarUsauriosColeccionDeDatos(accion) {
    
    var send;    
    var idColeccionDeDatosUsuarios = $("#idColeccionDeDatosUsuarios").val(); 
    var idColeccionUsuarios = $("#idColeccionUsuarios").val(); 
    var idUsuario = $("#hdn_usr_id").val();
    var tipo_varchar = $("#tipo_varchar").val();
    var tipo_entero = $("#tipo_entero").val();
    var fecha = $("#tipo_fecha").val(); 
    var tipo_bit = $("#tipo_bit").bootstrapSwitch("state");
    var tipo_numerico = $("#tipo_numerico").val();
    var fecha_inicio = $("#FechaInicial").val();
    var fecha_fin = $("#FechaFinal").val();
    var rango_minimo = $("#rango_minimo").val();
    var rango_maximo = $("#rango_maximo").val();
    var isActive = 0;
    

    if (tipo_varchar == "") {
        tipo_varchar = "NULL";
    } 
    
    if (tipo_entero == "") {
        tipo_entero = "NULL";
    } 
    
    if (fecha == "") {
        fecha = "NULL";
    } 

    if (tipo_bit == true) {
        tipo_bit = 1;
    } else {
        tipo_bit = 0;
    }

    if (tipo_numerico == "") {
        tipo_numerico = "NULL";
    } 
    
    if (fecha_inicio == "") {
        fecha_inicio = "NULL";
    } 
    
    if (fecha_fin == "") {
        fecha_fin = "NULL";
    } 

    if (rango_minimo == "") {
        rango_minimo = "NULL";
    } 

    if (rango_maximo == "") {
        rango_maximo = "NULL";
    }
    
    if (accion == 1) {
        isActive = 1;
    } else {
        var isChecked = $("#check_estado").prop("checked");
    
        if (isChecked == true) {
            isActive = 1;
        } else {
            isActive = 0;
        }
    }
    
    send = {"guardarInformacionColeccion": 1}; 
    send.accion = accion;
    send.idColeccionDeDatosUsuarios = idColeccionDeDatosUsuarios; 
    send.idColeccionUsuarios = idColeccionUsuarios; 
    send.idUsuario = idUsuario;
    send.tipo_varchar = tipo_varchar;
    send.tipo_entero = tipo_entero;
    send.fecha = fecha; 
    send.tipo_bit = tipo_bit;
    send.tipo_numerico = tipo_numerico;
    send.fecha_inicio = fecha_inicio;
    send.fecha_fin = fecha_fin;
    send.rango_minimo = rango_minimo;
    send.rango_maximo = rango_maximo;
    send.isActive = isActive;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminUsuario/config_coleccionUsuarios.php", data: send, success: function (datos){
        if (datos.str > 0) { 
            $("#modalTipoDatos_AgregarColeccionUsuario").modal("hide");  
            
            if (accion == 1) {                
                $("#modalAgregarColeccionUsuario").modal("hide");                
            } 
            
            $("#mdl_usr").modal("show");  
            
            alertify.success("Datos guardados correctamente.");            
            
            detalleUsuarioColeccionDeDatos(idUsuario);
        } else {
            var error = datos.str;
            error = error.substr(54, 125);                
            alertify.error(error);
            //alertify.error("<b>Alerta!</b> Ha ocurrido un problema, por favor comuniquese con el administrador.");
        }
    }});    
}

function detalleUsuarioColeccionDeDatos(idUsuario) {
    
    var html = "";
    var send;
    
    send = {"detalleUsuarioColeccionDeDatos": 1};    
    send.idUsuario = idUsuario;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminUsuario/config_coleccionUsuarios.php", data: send, success: function (datos){
        if (datos.str > 0) {
                
            for (var i = 0; i < datos.str; i++) {
              
                var valorPolitica = evaluarTipoDatoPolitica(datos[i]);
                
                html += '<tr id="'+i+'" onclick="seleccionUsuarioColeccionDeDatos('+i+', \''+datos[i]['ID_ColeccionUsuarios']+'\', \''+datos[i]['coleccionDescripcion']+'\', \''+datos[i]['ID_ColeccionDeDatosUsuarios']+'\', \''+datos[i]['parametroDescripcion']+'\', \''+datos[i]['especificarValor']+'\', \''+datos[i]['obligatorio']+'\', \''+datos[i]['tipodedato']+'\', \''+datos[i]['caracter']+'\', \''+datos[i]['entero']+'\', \''+datos[i]['fecha']+'\', \''+datos[i]['seleccion']+'\', \''+datos[i]['numerico']+'\', \''+datos[i]['fechaIni']+'\', \''+datos[i]['fechaFin']+'\', \''+datos[i]['min']+'\', \''+datos[i]['max']+'\', \''+datos[i]['isActive']+'\')"  class="text-center"><td style="width: 150px;">' + datos[i]['coleccionDescripcion'] + '</td><td style="width: 150px;">' + datos[i]['parametroDescripcion'] + '</td>';
                
                if (datos[i]['especificarValor'] == 1) {
                    html += '<td style="width: 70px;"><input type="checkbox" value="1" checked="checked" disabled/></td>';
                } else {
                    html += '<td style="width: 70px;"><input type="checkbox" value="1" disabled/></td>';
                }

                if (datos[i]['obligatorio'] == 1) {
                    html += '<td style="width: 70px;"><input type="checkbox" value="1" checked="checked" disabled/></td>';
                } else {
                    html += '<td style="width: 70px;"><input type="checkbox" value="1" disabled/></td>';
                }

                html += '<td style="width: 70px;">' + datos[i]['tipodedato'] + '</td><td style="width: 300px;">' + valorPolitica + '</td>';

                if (datos[i]['isActive'] == 1) {
                    html += '<td style="width: 70px;"><input type="checkbox" value="1" checked="checked" disabled/></td>';
                } else {
                    html += '<td style="width: 70px;"><input type="checkbox" value="1" disabled/></td>';
                }
                html += '</tr>';
            }
            $("#tablaUsuariosColeccion").html(html);

        } else {
            html = html + '<tr><th colspan="14" class="text-center">No existen registros.</th></tr>';
            $("#tablaUsuariosColeccion").html(html);
        }
    }});
}

function evaluarTipoDatoPolitica(fila) {
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
            return fila.fecha;
            break;
        case 'fecha ini-fin':
            return fila.fechaIni + ' - ' + fila.fechaFin;
            break;
        case 'min-max':
            return fila.min + ' - ' + fila.max;
            break;
        default:
            return fila.caracter;
            break;
        //code block
    }
}

function seleccionUsuarioColeccionDeDatos(idTabla, idColeccionUsuario, decripcionColeccion, idColeccionDeDatosUsuario, parametroDescripcion, especificarValor, obligatorio, tipodedato, caracter, entero, fecha, seleccion, numerico, fechaIni, fechaFin, min, max, isActive) {
    $("#tablaUsuariosColeccion tr").removeClass("success");
    $("#"+idTabla+"").addClass("success");
    
    $("#idColeccionDeDatosUsuarios").val(idColeccionDeDatosUsuario);
    $("#idColeccionUsuarios").val(idColeccionUsuario);
    
    GLOBAL_IDCOLECCIONUSUARIO = idColeccionUsuario; 
    GLOBAL_DECRIPCIONCOLECCION = decripcionColeccion;
    GLOBAL_IDCOLECCIONDEDATOSUSUARIO = idColeccionDeDatosUsuario;
    GLOBAL_PARAMETRODESCRIPCION = parametroDescripcion;
    GLOBAL_ESPECIFICARVALOR = especificarValor;
    GLOBAL_OBLIGATORIO = obligatorio;
    GLOBAL_TIPODEDATO = tipodedato;
    GLOBAL_CARACTER = caracter; 
    GLOBAL_ENTERO = entero;
    GLOBAL_FECHA = fecha;
    GLOBAL_SELECCION = seleccion;
    GLOBAL_NUMERICO = numerico;
    GLOBAL_FECHAINI = fechaIni;
    GLOBAL_FECHAFIN = fechaFin;
    GLOBAL_MIN = min;
    GLOBAL_MAX = max;
    GLOBAL_ISACTIVE = isActive;
}

function editarColeccionUsuario() {
    var seleccionColeccion = $('#tablaUsuariosColeccion').find("tr.success").attr("id");
    if (seleccionColeccion) {
        $("#mdl_usr").modal("hide");
        $("#modalTipoDatos_AgregarColeccionUsuario").modal("show");
        $("#nombreParametro").text(GLOBAL_DECRIPCIONCOLECCION);
        
        if (GLOBAL_ISACTIVE == 1) {
            $("#check_estado").prop('disabled', false);
            $("#check_estado").prop("checked", true);
        } else {
            $("#check_estado").prop('disabled', false);
            $("#check_estado").prop("checked", false);
        }
        
        if (GLOBAL_ESPECIFICARVALOR == 1) {
            $("#check_especifica").prop("checked", true);
        } else {            
            $("#check_especifica").prop("checked", false);
        }

        if (GLOBAL_OBLIGATORIO == 1) {
            $("#check_obligatorio").prop("checked", true);
        } else {
            $("#check_obligatorio").prop("checked", false);
        }
        
        if (GLOBAL_SELECCION == "SI") {
            $("#tipo_bit").bootstrapSwitch("state", true);
        } else {
            $("#tipo_bit").bootstrapSwitch("state", false);
        }
                
        $("#lbl_tipoDato").text(GLOBAL_TIPODEDATO);
        
        if (GLOBAL_CARACTER == "" || GLOBAL_CARACTER == "NULL") {
            $("#tipo_varchar").val("");
        } else {
            $("#tipo_varchar").val(GLOBAL_CARACTER);
        }
        
        if (GLOBAL_ENTERO == "" || GLOBAL_ENTERO == "NULL") {
            $("#tipo_entero").val("");
        } else {
            $("#tipo_entero").val(GLOBAL_ENTERO);
        }
        
        if (GLOBAL_FECHA == "" || GLOBAL_FECHA == "NULL") {
            $("#tipo_fecha").val("");
        } else {
            $("#tipo_fecha").val(GLOBAL_FECHA);
        }
        
        if (GLOBAL_NUMERICO == "" || GLOBAL_NUMERICO == "NULL") {
            $("#tipo_numerico").val("");
        } else {
            $("#tipo_numerico").val(GLOBAL_NUMERICO);
        }
        
        if (GLOBAL_FECHAINI == "" || GLOBAL_FECHAINI == "NULL") {
            $("#FechaInicial").val("");
        } else {
            $("#FechaInicial").val(GLOBAL_FECHAINI);
        }
        
        if (GLOBAL_FECHAFIN == "" || GLOBAL_FECHAFIN == "NULL") {
            $("#FechaFinal").val("");
        } else {
            $("#FechaFinal").val(GLOBAL_FECHAFIN);
        }
       
        if (GLOBAL_MIN == "" || GLOBAL_MIN == "NULL") {
            $("#rango_minimo").val("");
        } else {
            $("#rango_minimo").val(GLOBAL_MIN);
        }
        
        if (GLOBAL_MAX == "" || GLOBAL_MAX == "NULL") {
            $("#rango_maximo").val("");          
        } else {
            $("#rango_maximo").val(GLOBAL_MAX);          
        }
        
        $('#tipo_fecha').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY HH:mm', drops: 'up'}, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });

        $('#FechaInicial').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY HH:mm', drops: 'up'}, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });

        $('#FechaFinal').daterangepicker({minDate: moment(), singleDatePicker: true, format: 'DD/MM/YYYY HH:mm', drops: 'up'}, function (start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });
        
        $("#btn_accion").html('<button type="button" class="btn btn-default" data-dismiss="modal" onclick="cerraModalTipoDatos(2);">Cancelar</button><button type="button" class="btn btn-primary" onclick="guardarUsauriosColeccionDeDatos(2);">Guardar</button>');
        
    } else {
        alertify.error("<b>Alerta!</b> Debe Seleccionar un Registro.");
    }
}

