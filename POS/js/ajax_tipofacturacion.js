/* global alertify */

///////////////////////////////////////////////////////////////////////////////
//FECHA CREACION: 22/02/2016
//DESARROLLADOR: Daniel Llerena
//DESCRIPCION: Mantenimiento Tipo Facturacion
//FECHA ULTIMA MODIFICACION: 
//USUARIO QUE MODIFICO: 
//DECRIPCION: 
///////////////////////////////////////////////////////////////////////////////

//VARIABLES
var lc_banderaChecks = -1; 
var lc_estado = "";

$(document).ready(function()
{
    fn_btn("agregar",1);		
    fn_CargaTipoFacturacionXestado("Activo");
		
});

//Funcion para los botones de mantenimiento
function fn_btn(boton,estado){
    
    if(estado){
            $("#btn_"+boton).css("background"," url('../../imagenes/admin_resources/"+boton+".png') 14px 4px no-repeat");
            $("#btn_"+boton).removeAttr("disabled");
            $("#btn_"+boton).addClass("botonhabilitado");
            $("#btn_"+boton).removeClass("botonbloqueado");
    }
    else{
            $("#btn_"+boton).css("background"," url('../../imagenes/admin_resources/"+boton+"_bloqueado.png') 14px 4px no-repeat");
            $("#btn_"+boton).prop("disabled", true);
            $("#btn_"+boton).addClass("botonbloqueado");
            $("#btn_"+boton).removeClass("botonhabilitado");
        }
}

//Coloca los campos seleccionados con la funci�n en mayusculas
function aMays(e, elemento){
	var tecla=(document.all) ? e.keyCode : e.which; 
	elemento.value = elemento.value.toUpperCase();
}

//CARGA TIPO DE FACTURACION X ESTADO ACTIVOS � INACTIVOS
function fn_CargaTipoFacturacionXestado(opcion){
    
    var send;
    var CargaTipoFacturacionXestado = {"CargaTipoFacturacionXestado":1};
    var Accion = "C";	
    var html = "<thead><tr class='active'><th class='text-center col-md-4'>Tipo Facturaci&oacute;n</th><th class='text-center col-md-5'>URL de Impresi&oacute;n</th><th class='text-center col-md-2'>Activo</th></tr></thead>";	
    
    send = CargaTipoFacturacionXestado;
    send.Accion = Accion;
    send.Opcion = opcion;
    $.getJSON("../admimTipoFacturacion/config_tipofacturacion.php",send,function(datos) {											
        if(datos.str > 0){			
            for(var i=0; i<datos.str; i++){
                if(datos[i]["estado"] === "Activo"){
                    html = html + "<tr id='"+i+'tipofacturacion_id'+"' onclick='fn_seleccionar("+i+",\""+datos[i]["IDTipoFacturacion"]+"\")' ondblclick='fn_seleccionModificar("+i+",\""+datos[i]["IDTipoFacturacion"]+"\")'><td>"+datos[i]["tf_descripcion"]+"</td><td>"+datos[i]["url"]+"</td><td class='text-center'><input id='checked_estado' type='checkbox' checked='checked' disabled/></td></tr>";	
                }
                else{
                        html = html + "<tr id='"+i+'tipofacturacion_id'+"' onclick='fn_seleccionar("+i+",\""+datos[i]["IDTipoFacturacion"]+"\")' ondblclick='fn_seleccionModificar("+i+",\""+datos[i]["IDTipoFacturacion"]+"\")'><td>"+datos[i]["tf_descripcion"]+"</td><td>"+datos[i]["url"]+"</td><td class='text-center'><input id='checked_estado' type='checkbox' disabled/></td></tr>";
                    }

                $("#tabladetalletipofacturacion").html(html);
                $('#tabladetalletipofacturacion').dataTable({destroy: true});

                $("#tabladetalletipofacturacion_length").hide();
                $("#tabladetalletipofacturacion_paginate").addClass("col-xs-10");
                $("#tabladetalletipofacturacion_info").addClass("col-xs-10");
                $("#tabladetalletipofacturacion_length").addClass("col-xs-6");
            }
        }
        else{
                html += "<td colspan='3'>No existen datos.</td></tr>";
                $("#tabladetalletipofacturacion").html(html);
            }
    });		
}

//CARGA TIPO DE FACTURACION ACTIVOS E INACTIVOS
function fn_CargaTipoFacturacion(){	
    
    var send;
    var CargaTipoFacturacion = {"CargaTipoFacturacion":1};
    var Accion = "T";	
    var html = "<thead><tr class='active'><th class='text-center col-md-4'>Tipo Facturaci&oacute;n</th><th class='text-center col-md-5'>URL de Impresi&oacute;n</th><th class='text-center col-md-2'>Activo</th></tr></thead>";	
    send = CargaTipoFacturacion;
    send.Accion = Accion;
    $.getJSON("../admimTipoFacturacion/config_tipofacturacion.php",send,function(datos) {
        if(datos.str>0){
            for(var i=0; i<datos.str; i++){
                if(datos[i]["estado"] === "Activo"){
                    html = html + "<tr id='"+i+'tipofacturacion_id'+"' onclick='fn_seleccionar("+i+",\""+datos[i]["IDTipoFacturacion"]+"\")' ondblclick='fn_seleccionModificar("+i+",\""+datos[i]["IDTipoFacturacion"]+"\")'><td>"+datos[i]["tf_descripcion"]+"</td><td>"+datos[i]["url"]+"</td><td class='text-center'><input id='checked_estado' type='checkbox' checked='checked' disabled/></td></tr>";						
                }
                else{
                        html = html + "<tr id='"+i+'tipofacturacion_id'+"' onclick='fn_seleccionar("+i+",\""+datos[i]["IDTipoFacturacion"]+"\")' ondblclick='fn_seleccionModificar("+i+",\""+datos[i]["IDTipoFacturacion"]+"\")'><td>"+datos[i]["tf_descripcion"]+"</td><td>"+datos[i]["url"]+"</td><td class='text-center'><input id='checked_estado' type='checkbox' disabled/></td></tr>";
                    }

                $("#tabladetalletipofacturacion").html(html);
                $('#tabladetalletipofacturacion').dataTable({destroy: true});

                $("#tabladetalletipofacturacion_length").hide();
                $("#tabladetalletipofacturacion_paginate").addClass("col-xs-10");
                $("#tabladetalletipofacturacion_info").addClass("col-xs-10");
                $("#tabladetalletipofacturacion_length").addClass("col-xs-6");
            }
        }
        else{
                html += "<td colspan='3'>No existen datos.</td></tr>";
                $("#tabladetalletipofacturacion").html(html);
            }
    });		
}

function fn_agregar(){	
    
    $("#ruta_archivoimpresion").val("");	
    $("#nombreMenunuevo").val("");	
    $("#modalnuevo").modal("show");	
}

function fn_guardaNuevoTipoFacturacion(){
    
    var send;
    var AccionNuevo = {"AccionNuevo":1};
    
    var Accion = "I";
    var tipo_facturacion = $("#tipofacturacion_descripcion").val();
    var ruta = $("#ruta_archivoimpresion").val();

    if(tipo_facturacion === ""){
        alertify.error("Ingrese una descripci&oacute;n del tipo de facturaci&oacute;n.", function(){
            $("#modalnuevo").on("shown.bs.modal", function(){
                    $("#tipofacturacion_descripcion").focus();
            });
        });
            return false;
    }

    if(ruta === ""){        
        alertify.error("Es obligatorio ingresar la URL de impresi&oacute;n.", function(){
            $("#modalnuevo").on("shown.bs.modal", function(){
                    $("#ruta_archivoimpresion").focus();
            });
        });
            return false;
    }		

    send = AccionNuevo;	
    send.Accion = Accion;	
    send.descripcion_tf = $("#tipofacturacion_descripcion").val();
    send.ruta_impresion = $("#ruta_archivoimpresion").val();
    $.getJSON("../admimTipoFacturacion/config_tipofacturacion.php", send, function(datos){
        $("#opciones_estado label").removeClass("active");
        $("#opt_Activos").addClass("active");
        $("#modalnuevo").modal("hide");
        alertify.success("<h5>Datos Guardados Correctamente</h5>");				
        $("#tipofacturacion_descripcion").val("");
        $("#ruta_archivoimpresion").val("");
        fn_CargaTipoFacturacionXestado("Activo");
    });	
}

function fn_seleccionar(fila, menu_id){
    
    $("#tabladetalletipofacturacion tr").removeClass("success");
    $("#"+fila+'tipofacturacion_id'+"").addClass("success");	 
}

function fn_seleccionModificar(fila, IDTipoFacturacion){ 
    
    $('#modalmodificar').modal("show");	
    fn_cargarTipoFacturacionModificar(IDTipoFacturacion);
}

function fn_cargarTipoFacturacionModificar(IDTipoFacturacion){    
    
    var send;
    var CargaModificarTipoFacturacion = {"CargaModificarTipoFacturacion":1};	
    
    $("#IDTipoFacturacion").val(IDTipoFacturacion);
    var Accion = "M";	
    send = CargaModificarTipoFacturacion;	
    send.Accion = Accion;		
    send.IDTipoFacturacion = IDTipoFacturacion;
    $.getJSON("../admimTipoFacturacion/config_tipofacturacion.php",send,function(datos) { 
        if(datos.str > 0){ 			
            if(datos[0].estado === "Activo"){
                $("#check_activo").prop("checked",true);			 
            }
            else{
                    $("#check_activo").prop("checked",false);			
                }					  

            $("#tipofacturacion_descripcionModificar").val(datos[0].tf_descripcion);
            $("#myModalLabel").text(datos[0].tf_descripcion);
            $("#ruta_archivoimpresionModificar").val(datos[0].url);
        }
    });
}

//MODIFICA EL TIPO DE FACTURACION
function fn_modificarTipoFacturacion(){
    
    var send;
    var AccionModificar = {"AccionModificar":1};	
    
    var Accion = "U";
    var tipo_facturacion = $("#tipofacturacion_descripcionModificar").val();
    var ruta = $("#ruta_archivoimpresionModificar").val();
    var IDTipoFacturacion = $("#IDTipoFacturacion").val();
    
    if(tipo_facturacion === ""){
        alertify.error("Ingrese una descripci&oacute;n del tipo de facturaci&oacute;n.", function(){
            $("#modalnuevo").on("shown.bs.modal", function(){
                    $("#tipofacturacion_descripcionModificar").focus();
            });
        });
            return false;
    }

    if(ruta === ""){
        alertify.error("Es obligatorio ingresar la URL de impresi&oacute;n.", function(){
            $("#modalnuevo").on("shown.bs.modal", function(){
                    $("#ruta_archivoimpresionModificar").focus();
            });
        });
            return false;
    }		

    send = AccionModificar;	
    send.Accion = Accion;
    send.IDTipoFacturacion = IDTipoFacturacion;
    send.descripcion_tf = $("#tipofacturacion_descripcionModificar").val();
    send.ruta_impresion = $("#ruta_archivoimpresionModificar").val();
    if($("#check_activo").is(":checked")) {
            send.estado = 1;
    }
    else{
            send.estado = 0;	
        } 
    $.getJSON("../admimTipoFacturacion/config_tipofacturacion.php", send, function(datos){
        $("#opciones_estado label").removeClass("active");
        $("#opt_Activos").addClass("active");
        $("#modalmodificar").modal("hide");
        alertify.success("<h5>Datos Modificados Correctamente</h5>");				
        $("#tipofacturacion_descripcionModificar").val("");
        $("#ruta_archivoimpresionModificar").val("");
        fn_CargaTipoFacturacionXestado("Activo");
    });	
}
