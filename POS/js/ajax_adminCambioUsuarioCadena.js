/*
FECHA CREACION   : 07/05/2018 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Pantalla que realiza el cambio de cadena a usuarios MP 
*/

/* global alertify */

var SELECCIONAR_RESTAURANTE = "";

$(document).ready(function(){
    
    obtenerPerfiles();
    $("#cargando").hide();
    
});

function obtenerPerfiles(){
    var send;
    var obtenerPerfiles = {"obtenerPerfiles": 1};
    var html = "";
    
    send = obtenerPerfiles;   
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminCambioUsuarioCadena/config_cambioUsuarioCadena.php", data: send, success: function (datos){
        if (datos.str > 0) {
            $("#selectPerfil").html("");
            $('#selectPerfil').html("<option selected value='0'>-------------- Seleccionar Perfil --------------</option>");
            
            for(var i=0; i<datos.str; i++) {
                html = "<option value='"+datos[i]['IDPerfil']+"'>"+datos[i]['perfil']+"</option>";
                $("#selectPerfil").append(html);										
            }

            $("#selectPerfil").chosen();
            $("#selectPerfil").change(function(){
                var IDPerfil = $("#selectPerfil").val();
                
                if (IDPerfil != 0) {
                    $("#tablaUsuarios").show();
                    obtenerUsuarios(IDPerfil);                        
                } else {
                    $("#tablaUsuarios").hide();
                } 
            });
        }
    }});    
}

function obtenerUsuarios(IDPerfil){
    var send;    
    var obtenerUsuarios = {"obtenerUsuarios": 1};
    var html = '<thead><tr class="active"><th class="text-center">Identificaci&oacute;n</th><th class="text-center">Descripci&oacute;n</th><th class="text-center">Usuario</th><th class="text-center">Tel&eacute;fono</th><th class="text-center">Correo</th><th class="text-center">Direcci&oacute;n</th><th class="text-center">Perfil</th></tr></thead>';
    
    send = obtenerUsuarios; 
    send.IDPerfil = IDPerfil;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminCambioUsuarioCadena/config_cambioUsuarioCadena.php", data: send, success: function (datos){        
        if(datos.str > 0){            
            html += '<tbody>';
            
            for(var i=0; i<datos.str; i++){
                html += '<tr class="text-center" id="'+i+'" onclick="seleccionarRegistro('+i+');" ondblclick="verDatosUsuario(\''+datos[i]["IDUsersPos"]+'\', \''+datos[i]["identificacion"]+'\', \''+datos[i]["descripcion"]+'\', \''+datos[i]["usuario"]+'\', \''+datos[i]["telefono"]+'\', \''+datos[i]["email"]+'\', \''+datos[i]["direccion"]+'\', \''+datos[i]["nombrePos"]+'\', \''+datos[i]["perfil"]+'\');"><td>'+datos[i]["identificacion"]+'</td><td>'+datos[i]["descripcion"]+'</td><td>'+datos[i]["usuario"]+'</td><td>'+datos[i]["telefono"]+'</td><td>'+datos[i]["email"]+'</td><td>'+datos[i]["direccion"]+'</td><td>'+datos[i]["perfil"]+'</td>';
                html += '</tr>';
            }
            
            html += '</tbody>';
            $("#tablaDetalleUsuarios").html(html);
            $("#tablaDetalleUsuarios").dataTable({ 
                destroy: true,
                autoWidth: false,
                columnDefs: [
                    { width: "10%", targets: 0 }
                    , { width: "20%", targets: 1 }
                    , { width: "10%", targets: 2 }
                    , { width: "10%", targets: 3 }
                    , { width: "10%", targets: 4 }
                    , { width: "20%", targets: 5 }                    
                    , { width: "20%", targets: 6 }
                ]
            });
            $("#tablaDetalleUsuarios_length").hide();
            $("#tablaDetalleUsuarios_paginate").addClass("col-xs-10");
            $("#tablaDetalleUsuarios_info").addClass("col-xs-10");
            $("#tablaDetalleUsuarios_length").addClass("col-xs-6");
        } else {
                html = html + '<tbody><tr><th colspan="8" class="text-center">No existen registros.</th></tr></tbody>';
                $("#tablaDetalleUsuarios").html(html);
            }
    }});
}

function seleccionarRegistro(fila){
    $("#tablaDetalleUsuarios tr").removeClass("success");
    $("#"+fila+"").addClass("success");    
}

/* Tab : Datos Usuario */
function verDatosUsuario(idUsuario, identificacion, descripcion, usuario, telefono, email, direccion, nombrePos, perfil){
    
    $("#modal").modal("show");
    $("#pestanas li").removeClass("active");
    $('#tabDetalle').addClass("active");
    $("#tabContenedor div").removeClass("active");
    $('#tab_detalle').addClass("active"); 
    
    $("#lbl_descripcion").text(descripcion);
    $("#hdn_IDUsuario").val(idUsuario);
    $("#txt_descripcion").val(descripcion);
    $("#txt_identificacion").val(identificacion);
    $("#txt_usuario").val(usuario);
    $("#txt_telefono").val(telefono);
    $("#txt_email").val(email);
    $("#txt_direccion").val(direccion);
    $("#txt_nombrePos").val(nombrePos);
    $("#txt_perfil").val(perfil);
    
    obtenerRestaurantesAsinados(idUsuario);
    obtenerCadena();
    
    $("#selectCadena").val(0);
    $("#contenedorRegion").hide();
    $("#contenedorTiendas").hide();
}

/* Tab : Restaurantes Asignados */
function obtenerRestaurantesAsinados(idUsuario){
    var send;
    var obtenerRestaurantesAsinados = {"obtenerRestaurantesAsinados": 1};
    $("#listaRestaurantesAsignados").html('<thead><tr class="bg-primary"><th class="text-center" style="width: 10%">N°</th><th class="text-center" style="width: 70%">Restaurante</th><th class="text-center" style="width: 20%">Eliminar</th></tr></thead>');
    
    send = obtenerRestaurantesAsinados; 
    send.idUsuario = idUsuario;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminCambioUsuarioCadena/config_cambioUsuarioCadena.php", data: send, success: function (datos){
        if(datos.str > 0){
            for(var i=0; i<datos.str; i++){
                $('#listaRestaurantesAsignados').append("<tr><td class='text-center'>"+[i]+"</td><td>" + datos[i]["tienda"] + "</td><td class='text-center'><button type=\"button\" class=\"btn btn-danger btn-sm aling-btn-center\" data-toggle=\"confirmation\" data-placement=\"left\" onclick=\"eliminarUsuarioRestaurante("+datos[i]["idRestaurante"]+")\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button></td></tr>");
            }
            $("#btnEliminarTodos").show();
        } else {
            $("#listaRestaurantesAsignados").html('<tbody><tr><th colspan="2" class="text-center">Ning&uacute;n restaurante asignado.</th></tr></tbody>');
            $("#btnEliminarTodos").hide();
        }
    }});
}

function eliminarUsuarioRestaurante(idRestaurante){
    var send;
    var eliminarUsuarioRestaurante = {"eliminarUsuarioRestaurante": 1};
    var idUsuario = $("#hdn_IDUsuario").val();
    
    send = eliminarUsuarioRestaurante; 
    send.idRestaurante = idRestaurante; 
    send.idUsuario = idUsuario;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminCambioUsuarioCadena/config_cambioUsuarioCadena.php", data: send, success: function (datos){
        alertify.success("El restaurante ha sido retirado de la lista.");
        obtenerRestaurantesAsinados(idUsuario);
    }});
}

function eliminarTodosUsuarioRestaurantes(){
    var send;
    var eliminarTodosUsuarioRestaurantes = {"eliminarTodosUsuarioRestaurantes": 1};
    var idUsuario = $("#hdn_IDUsuario").val();
    
    $("#cargando").show();
    send = eliminarTodosUsuarioRestaurantes; 
    send.idUsuario = idUsuario;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminCambioUsuarioCadena/config_cambioUsuarioCadena.php", data: send, success: function (datos){
        alertify.success("Todos los restaurantes ha sido retirados de la lista.");
        obtenerRestaurantesAsinados(idUsuario);
        $("#cargando").hide();
    }});
}

/* Tab : Agregar Restaurante Cambiar de Cadena */
function obtenerCadena(){
    var send;
    var obtenerCadena = {"obtenerCadena": 1};
    var html = "";
    
    send = obtenerCadena;   
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminCambioUsuarioCadena/config_cambioUsuarioCadena.php", data: send, success: function (datos){
        if (datos.str > 0) {
            $("#selectCadena").html("");
            $('#selectCadena').html("<option selected value='0'>-------------- Seleccione una Cadena --------------</option>");
            
            for(var i=0; i<datos.str; i++) {
                html = "<option value='"+datos[i]['IDCadena']+"'>"+datos[i]['cadena']+"</option>";
                $("#selectCadena").append(html);										
            }
            
            $("#selectCadena").trigger("chosen:updated");
            $("#selectCadena").chosen();
            $("#selectCadena_chosen").css('width', '500');
            $("#selectCadena").change(function(){            
                var idCadena = $("#selectCadena").val();                    
                $("#hdn_IDCadena").val(idCadena); 
                
                if (idCadena == 0) {
                    $("#contenedorRegion").hide();
                    $("#contenedorTiendas").hide();
                } else {
                    $("#contenedorRegion").show();
                    obtenerRegion();
                }                 
            });
        }
    }});
}

function obtenerRegion(){
    var send;
    var obtenerRegion = {"obtenerRegion": 1};
    var html = "";
    
    send = obtenerRegion;   
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminCambioUsuarioCadena/config_cambioUsuarioCadena.php", data: send, success: function (datos){
        if(datos.str>0) {
            for(var i=0; i<datos.str; i++){
                if (i == 0) {
                    html = html + "<label class='btn btn-default active' id='region_checks"+datos[i]['idRegion']+"' onChange='obtenerRestaurantesXRegion(0);'><h10><input type='radio' name='region' checked='checked' value='"+datos[i]['idRegion']+"'>"+datos[i]['region']+"</h10></label>";						
                    $("#contenedorTiendas").show();
                    obtenerRestaurantesXRegion(0);
                } else {
                    html = html + "<label class='btn btn-default' id='region_checks"+datos[i]['idRegion']+"' onChange='obtenerRestaurantesXRegion(9);'><h10><input type='radio' name='region' value='"+datos[i]['idRegion']+"'>"+datos[i]['region']+"</h10></label>";
                }
                $("#selectRegion").html(html);                
            }
        }
    }});
}

function obtenerRestaurantesXRegion(opcionRegion){    
    desmarcarRestaurantesSeleccionados();
    var send;
    var obtenerRestaurantesXRegion = {"obtenerRestaurantesXRegion": 1};
    var html = ""; 
    var idCadena = $("#hdn_IDCadena").val();
    var idRegion;
    var idUsuario = $("#hdn_IDUsuario").val();
    
    if (opcionRegion == 0){
        idRegion = 0;
    } else {
        idRegion = $(":input[name='region']:checked").val(); 
    } 

    send = obtenerRestaurantesXRegion;
    send.idUsuario = idUsuario;
    send.idCadena = idCadena;
    send.idRegion = idRegion;    
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminCambioUsuarioCadena/config_cambioUsuarioCadena.php", data: send, success: function (datos){
        if(datos.str > 0){
            for(var i=0; i<datos.str; i++){
                html = html + '<a class="list-group-item"><input id="cbx_idRestaurante_'+datos[i]['idRestaurante']+'" name="cbx_restaurante" value="'+datos[i]['idRestaurante']+'" type="checkbox" onclick="seleccionarRestaurante('+datos[i]['idRestaurante']+')">&nbsp; '+datos[i]['restaurante']+'</a>';
            } 
            $("#listaRestaurantesCadena").html(html);
        } else {
            html = html + '<a class="list-group-item">"No existen registros."</a>';
            $("#listaRestaurantesCadena").html(html);
        }
    }});
}

function seleccionarTodosRestaurantes(){
    SELECCIONAR_RESTAURANTE = "";
    var marcarTodos = $("#cbx_todosRestaurantes").is(":checked");    
    
    if (marcarTodos) { 
        $(":input[name=cbx_restaurante]").each(function(){
            var marcarTiendas = (SELECCIONAR_RESTAURANTE.indexOf($(this).val()+"_"));
            
            if (marcarTiendas < 0) {
                SELECCIONAR_RESTAURANTE = SELECCIONAR_RESTAURANTE + $(this).val() + "_";                
            }
            
            $("#cbx_idRestaurante_"+$(this).val()).prop("checked", "checked");
        });        
    } else {
        $(":input[name=cbx_restaurante]").each(function(){
            var desmarcarTiendas = (SELECCIONAR_RESTAURANTE.indexOf($(this).val()+"_"));  
            
            if (desmarcarTiendas >= 0) {
                SELECCIONAR_RESTAURANTE = SELECCIONAR_RESTAURANTE.replace($(this).val()+"_", "");         
            }
            
            $("#cbx_idRestaurante_"+$(this).val()).prop("checked", false);
        }); 
    }
}

function seleccionarRestaurante(idRestaurante){    
    var marcarRestaurante = $("#cbx_idRestaurante_" + idRestaurante).is(":checked");
    
    if (marcarRestaurante) {
        SELECCIONAR_RESTAURANTE = SELECCIONAR_RESTAURANTE + idRestaurante + "_";        
    } else {
        SELECCIONAR_RESTAURANTE = SELECCIONAR_RESTAURANTE.replace(idRestaurante + "_", "");       
    }
}

function desmarcarRestaurantesSeleccionados(){    
    SELECCIONAR_RESTAURANTE = "";
    $("#cbx_todosRestaurantes").prop("checked", false);
    
    $(":input[name=cbx_restaurante]:checked").each(function(){        
        $("#cbx_idRestaurante_"+$(this).val()).prop("checked", false);
    });    
}

function posicionTab(opcion){
    
    if (opcion == 1 || opcion == 2) {
        $("#btn_accion").html('<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');
    } else { 
       $("#btn_accion").html('<button type="button" class="btn btn-primary" onclick="agregarUsuarioRestaurante();">Aceptar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');      
    }
}

function agregarUsuarioRestaurante(){
    var send;
    var agregarUsuarioRestaurante = {"agregarUsuarioRestaurante": 1};
    var selectCadena = $("#selectCadena option:selected").val();
    
    if (selectCadena == 0) {
        alertify.error("<b>Alerta:</b> Debe seleccionar una cadena.");      
        return false;  
    } else if(SELECCIONAR_RESTAURANTE == ""){
        alertify.error("<b>Alerta:</b> Debe seleccionar por lo menos un restaurante.");      
        return false;        
    }  
    
    $("#cargando").show();
    send = agregarUsuarioRestaurante;    
    send.idRestaurante = SELECCIONAR_RESTAURANTE; 
    send.idUsuario = $("#hdn_IDUsuario").val();
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminCambioUsuarioCadena/config_cambioUsuarioCadena.php", data: send, success: function (datos){
        alertify.success("Datos guardados correctamente.");      
        $("#modal").modal("hide"); 
        desmarcarRestaurantesSeleccionados();
        $("#cargando").hide();
    }});
}
