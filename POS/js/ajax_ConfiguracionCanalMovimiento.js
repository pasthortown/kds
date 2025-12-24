/*
FECHA CREACION   : 16/07/2016 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion de promociones
*/

/* global alertify */

$(document).ready(function(){						   
    fn_btn("agregar",1);
    fn_cargaDetalle("Activo");    
});

/* FUNCION QUE OBTIENE LOS BOTONES PARA LA ADMINISTRACIÓN */
function fn_btn(boton,estado){
    if(estado){
        $("#btn_"+boton).css("background"," url('../../imagenes/admin_resources/"+boton+".png') 14px 4px no-repeat");
        $("#btn_"+boton).removeAttr("disabled");
        $("#btn_"+boton).addClass("botonhabilitado");
        $("#btn_"+boton).removeClass("botonbloqueado");
    } else {
            $("#btn_"+boton).css("background"," url('../../imagenes/admin_resources/"+boton+"_bloqueado.png') 14px 4px no-repeat");
            $("#btn_"+boton).prop("disabled", true);
            $("#btn_"+boton).addClass("botonbloqueado");
            $("#btn_"+boton).removeClass("botonhabilitado");
        }
}

/* FUNCION QUE CARGA EL DETALLE DE LA TABLA CONFIGURACION_CANAL_MOVIMIENTO */
function fn_cargaDetalle(opcionEstado){    
    var send;
    var Accion = "C";    
    var html = '<thead><tr class="active"><th class="text-center">Descripci&oacute;n</th><th class="text-center">C&oacute;digo</th><th class="text-center">Valor</th><th class="text-center">Activo</th></tr></thead>';
    
    send = {"cargarDetalle": 1};
    send.accion = Accion;    
    send.estado = opcionEstado;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminConfiguracionCanalMovimiento/config_ConfiguracionCanalMovimiento.php", data: send, success: function (datos){        
        if (datos.str > 0){
            for (var i = 0; i < datos.str; i++){                 
                var estado = (datos[i]["Estado"]);                
                html += '<tr id="'+i+"IDConfiguracionCanalMovimiento"+'" onclick="fn_seleccionar('+i+')" ondblclick="fn_seleccionModificar('+i+',\''+datos[i]["IDConfiguracionCanalMovimiento"]+'\',\''+datos[i]["Descripcion"]+'\',\''+datos[i]["Codigo"]+'\',\''+datos[i]["Valor"]+'\',\''+datos[i]["Estado"]+'\')" class="text-center"><td>'+datos[i]["Descripcion"]+'</td><td>'+datos[i]["Codigo"]+'</td><td>'+datos[i]["Valor"]+'</td>';
                                
                if (estado === "Inactivo"){
                    html += '<td><input type="checkbox" value="1" disabled/></td>';
                } else {
                        html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    } 
                        html += '</tr>';
            }
                $("#TblDetalle_ConfigCanalMovimiento").html(html);
                $("#TblDetalle_ConfigCanalMovimiento").dataTable({"destroy": true});
                $("#TblDetalle_ConfigCanalMovimiento_length").hide();
                $("#TblDetalle_ConfigCanalMovimiento_paginate").addClass("col-xs-10");
                $("#TblDetalle_ConfigCanalMovimiento_info").addClass("col-xs-10");
                $("#TblDetalle_ConfigCanalMovimiento_length").addClass("col-xs-6");
        } else {
                html = html + '<tr><th colspan="4" class="text-center">No existen registros.</th></tr>';
                $("#TblDetalle_ConfigCanalMovimiento").html(html);
            }
    },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

/* Función para seleccionar un registro de la tabla */
function fn_seleccionar(fila){
    $("#TblDetalle_ConfigCanalMovimiento tr").removeClass("success");
    $("#"+fila+'IDConfiguracionCanalMovimiento'+"").addClass("success");
}

/* Funcion en la cual se obtiene los datos de un registro con doble click para la modificacion */
function fn_seleccionModificar(fila, IDConfigCanalMovimiento, Descripcion, Codigo, Valor, Estado){    
    if(Estado === "Inactivo"){
        $("#check_isactive").prop("checked", false);
    } else {
            $("#check_isactive").prop("checked", true);
        }
    
    $("#hdn_IDConfiguracionCanalMovimiento").val(IDConfigCanalMovimiento);
    $("#ModalConfiguracionCanalMovimiento").modal("show");
    $("#tituloModal").text(Descripcion);
    $("#check_isactive").prop("disabled", false);
    $("#txt_descripcion").val(Descripcion);
    $("#txt_codigo").val(Codigo);
    $("#txt_valor").val(Valor);     
    $("#btn_accion").html('<button type="button" class="btn btn-primary" onclick="fn_guardar(2);"><span class="glyphicon glyphicon-floppy-saved"></span> Guardar</button>\n\
                           <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>');
}

/* Función para Insert ó Update */
function fn_guardar(accionGuardar){
    // accionGuardar = 1 es Insert, accionGuardar = 2 es Update,
    fn_cargando(1); 
    var send; 
    var Accion;
    var descripcion = $("#txt_descripcion").val();
    var codigo = $("#txt_codigo").val();    
    var valor = $("#txt_valor").val();
    var estado = $("#check_isactive").is(":checked");
    var IDConfiguracionCanalMovimiento;
    
    if(accionGuardar === 1){
        Accion = "I";
        estado = 1;
        IDConfiguracionCanalMovimiento = "0";
    } else {
        Accion = "U";
        if(estado === true){
                estado = 1;
            } else {
                    estado = 0;
                }                
                    IDConfiguracionCanalMovimiento = $("#hdn_IDConfiguracionCanalMovimiento").val();
    } 
    
    if(descripcion === ""){
        alertify.error("<b>Alerta</b> La descripci&oacute;n es obligatoria.");
        fn_cargando(0);
        return false;        
    }    
    else if(codigo === ""){
        alertify.error("<b>Alerta</b> El c&oacute;digo es obligatorio.");
        fn_cargando(0);
        return false;        
    }    
    else if(valor === ""){
        alertify.error("<b>Alerta</b> El valor es obligatorio.");
        fn_cargando(0);
        return false;        
    }  
        
    send = {"guardarRegistro": 1};
    send.accion = Accion;      
    send.descripcion = descripcion;
    send.codigo = codigo;
    send.valor = valor;
    send.estado = estado;
    send.IDConfiguracionCanalMovimiento = IDConfiguracionCanalMovimiento;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../adminConfiguracionCanalMovimiento/config_ConfiguracionCanalMovimiento.php", data: send, success: function (datos) {
        
        alertify.success("Datos guardados correctamente.");      
        $("#ModalConfiguracionCanalMovimiento").modal("hide");         
        fn_labelEstados(estado);
        fn_cargando(0);         
    },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
            alertify.error("Error al momento de guardar.");
            fn_cargando(0);
        }
    });
}

/* Funcion que carga el detalle despues de un Insert ó Update */
function fn_labelEstados(estado){
    if(estado === 1){
        $("#opciones_estados label").removeClass("active");
        $("#opcion_1").addClass("active");
        fn_cargaDetalle("Activo");
    } 
    else if(estado === 0){
        $("#opciones_estados label").removeClass("active");
        $("#opcion_2").addClass("active");
        fn_cargaDetalle('Inactivo');
    }
    else{
            $("#opciones_estados label").removeClass("active");
            $("#opcion_3").addClass("active");
            fn_cargaDetalle('Todos');
        }
}

/* Nuevo Registro */
function fn_nuevoRegistro(){              
    $("#ModalConfiguracionCanalMovimiento").modal("show");
    $("#tituloModal").text("Nueva Configuración");
    $("#check_isactive").prop("checked", true);
    $("#check_isactive").prop("disabled", true);  
    $("#txt_descripcion").val("");
    $("#txt_codigo").val("");
    $("#txt_valor").val("");     
    $("#btn_accion").html('<button type="button" class="btn btn-primary" onclick="fn_guardar(1);"><span class="glyphicon glyphicon-floppy-saved"></span> Guardar</button>\n\
                           <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-ban-circle"></span> Cancelar</button>');
}