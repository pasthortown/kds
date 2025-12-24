
/* global alertify */

$(document).ready(function()
{	
    
    fn_btn("agregar", 1);
    fn_cargarCategoria(2, "Activo", 0);

    $("#agregarCategoria").on("shown.bs.modal", function(){
            $("#nombreCategoria").focus();
    });
	  
});

/*---------------------------------------------
Carga los productos de una Cadena seleccionada
-----------------------------------------------*/
function fn_cargarCategoria(resultado, std_id, mag_id)
{    
    $("#mdl_rdn_pdd_crgnd").show();
    var cargarCategoria = {"cargarCategoria": 1};
    var html = "<thead><tr class='active'><th width='50%'>Nombre</th><th width='30%'>Como se Visualiza</th><th width='20%'>Activo</th></tr></thead>";
    
    send = cargarCategoria;
    send.resultado = resultado;
    send.cdn_id = $("#sess_cdn_id").val();
    send.mag_id = mag_id;
    send.std_id = std_id;
    $.getJSON("../adminmenucategoria/config_menuCategoria.php",send,function(datos) {
        
        if(datos.str>0){
            
            
            for(var i=0; i<datos.str; i++){
                html = html + "<tr id='ctgr_mn_grp_id_"+datos[i]['mag_id']+"' onclick='fn_seleccionCategoria(\""+datos[i]['mag_id']+"\")' ondblclick='fn_modificarCategoria(\""+datos[i]['mag_id']+"\", \""+datos[i]['mag_descripcion']+"\", \""+datos[i]['mag_color']+"\", \""+datos[i]['mag_colortexto']+"\", \""+datos[i]['std_id']+"\")'><td>"+datos[i]['mag_descripcion']+"</td><td class='text-center'><button style='background-color:"+datos[i]['mag_color']+"; color:"+datos[i]['mag_colortexto']+"; height:60px; width:120px;'>"+datos[i]['mag_descripcion']+"</button></td>";
                if (datos[i]["std_id"] === "Activo"){
                        html+="<td class='text-center'><input type='checkbox' checked='checked' disabled/></td>";
                }
                else{
                        html+="<td class='text-center'><input type='checkbox' disabled/></td>";
                    }
                     
                        $("#lista_categorias").html(html); 
                        $("#lista_categorias").dataTable({destroy: true});
                        $("#lista_categorias_length").hide();
                        $("#lista_categorias_paginate").addClass("col-xs-10");
                        $("#lista_categorias_info").addClass("col-xs-10");
                        $("#lista_categorias_length").addClass("col-xs-6");
            }
          $("#mdl_rdn_pdd_crgnd").hide();
        }
        else{
                html = html + '<tr><th colspan="3">No existen registros.</th></tr>';
                $("#lista_categorias").html(html);
                $("#mdl_rdn_pdd_crgnd").hide();
            }
    });
            $("#opciones_estado label").removeClass("active");
            $("#opciones_1").addClass("active");
            fn_destruirPaletaModificar();
            
              //$("#mdl_rdn_pdd_crgnd").hide();
}

function fn_agregar()
{	
    $("#check_activo").prop("checked",true);
    $("#check_activo").prop("disabled",true);
    $("#mod_magid").val(0);
    $("#nombreCategoria").val("");
    $("#fondoEjemplo").val("");
    $("#fondoEjemplo").css({"background": "#000", "color": "#FFF"});

    $("#colorTexto").spectrum({showPalette: true, hideAfterPaletteSelect: true, showButtons: false, color: "#f7f5f4", palette: [["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]]});

    $("#colorFondo").spectrum({showPalette: true, hideAfterPaletteSelect:true, showButtons: false, color: "#000000", palette: [["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]]});

    $("#pcn_mdl_mn_ctgrs_grgr").html('<button type="button" class="btn btn-primary" onclick="fn_administrarCategoriaAgregar(1);">Aceptar</button><button type="button" class="btn btn-default" onclick="fn_destruirPaletaAgregar();" data-dismiss="modal">Cancelar</button>');

    $("#agregarCategoria").modal("show");
}

function fn_actualizarColorTexto()
{
    var colorTexto = $("#mod_colorTexto").spectrum("get").toHexString();
    $("#mod_fondoEjemplo").css({"color": colorTexto});
}

function fn_actualizarColor()
{
    var color = $("#mod_colorFondo").spectrum("get").toHexString();
    $("#mod_fondoEjemplo").css({"background": color});
}

function fn_modificarCategoria(mag_id, mag_descripcion, mag_color, mag_colortexto, std_id)
{	
    if(std_id === "Activo")
    {
        $("#check_activonuevo").prop("checked",true);
    }
    else{
            $("#check_activonuevo").prop("checked",false);
        }
        
    $("#mod_magid").val(mag_id);
    $("#mod_nombreCategoria").val(mag_descripcion);
    $("#myModalLabel").text(mag_descripcion);
    $("#mod_fondoEjemplo").val(mag_descripcion);
    $("#mod_fondoEjemplo").css({"background": mag_color, "color": mag_colortexto});
    if(mag_colortexto === ""){mag_colortexto = "#000000";}
    if(mag_color === ""){mag_color = "#FFFFFF";}
    $("#mod_colorTexto").spectrum({ showPalette: true, hideAfterPaletteSelect: true, showButtons: false, color: mag_colortexto, palette: [["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]]});
    $("#mod_colorFondo").spectrum({showPalette: true, hideAfterPaletteSelect: true, showButtons: false, color: mag_color, palette: [["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],	["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],	["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]]});
    $("#pcn_mdl_mn_ctgrs").html('<button type="button" class="btn btn-primary" onclick="fn_administrarCategoria(0);">Aceptar</button><button type="button" class="btn btn-default" onclick="fn_destruirPaletaModificar();" data-dismiss="modal">Cancelar</button>');
    $("#modificarCategoria").modal("show");
}

function aMays(e, elemento) 
{
    tecla=(document.all) ? e.keyCode : e.which; 
    elemento.value = elemento.value.toUpperCase();
}

function fn_administrarCategoria(accion)
{
    var administrarCategoria = {"administrarCategoria":1};
    var html = "<thead><tr class='active'><th width='50%'>Nombre</th><th width='30%'>Como se Visualiza</th><th width='20%'>Activo</th></tr></thead>";
    var mag_id = $("#mod_magid").val();
    var std_id = $("#check_activonuevo").is(":checked");
    var mag_descripcion = $("#mod_nombreCategoria").val();
    var mag_colortexto = $("#mod_colorTexto").spectrum("get").toHexString();
    var mag_color = $("#mod_colorFondo").spectrum("get").toHexString();
    var cdn_id = $("#sess_cdn_id").val();
    var menu_id = 0;
    var usr_id = $("#usuario").val();

    if($.trim(mag_descripcion)==="" || $.trim(mag_colortexto)==="" || $.trim(mag_color)==="")
    {
        alertify.error("Campos requeridos se encuentran vacios");
        return false;
    }

    if(std_id === true)
    {
        std_id = "Activo";
    }
    else{
            std_id = "Inactivo";
        }
    
    send = administrarCategoria;	
    send.accion = accion;
    send.mag_id = mag_id;
    send.mag_descripcion = mag_descripcion;
    send.mag_colortexto = mag_colortexto;
    send.mag_color = mag_color;
    send.menu_id = menu_id;
    send.cdn_id = cdn_id;
    send.usr_id = usr_id;
    send.std_id = std_id;
    $.getJSON("../adminmenucategoria/config_menuCategoria.php", send, function(datos) {
        if(datos.str>0){
            for(var i=0; i<datos.str; i++) {
                html = html + "<tr id='ctgr_mn_grp_id_"+datos[i]['mag_id']+"' onclick='fn_seleccionCategoria(\""+datos[i]['mag_id']+"\")' ondblclick='fn_modificarCategoria(\""+datos[i]['mag_id']+"\", \""+datos[i]['mag_descripcion']+"\", \""+datos[i]['mag_color']+"\", \""+datos[i]['mag_colortexto']+"\", \""+datos[i]['std_id']+"\")'><td>"+datos[i]['mag_descripcion']+"</td><td class='text-center'><button style='background-color:"+datos[i]['mag_color']+"; color:"+datos[i]['mag_colortexto']+"; height:60px; width:120px;'>"+datos[i]['mag_descripcion']+"</button></td>";
                if (datos[i]['std_id'] === 'Activo'){
                        html+="<td class='text-center'><input type='checkbox' checked='checked' disabled/></td>";
                }
                else{
                        html+="<td><input type='checkbox' disabled/></td>";
                    }
                        $("#lista_categorias").html(html);
                        $("#lista_categorias").dataTable({destroy: true});
                        $("#lista_categorias_length").hide();
                        $("#lista_categorias_paginate").addClass("col-xs-10");
                        $("#lista_categorias_info").addClass("col-xs-10");
                        $("#lista_categorias_length").addClass("col-xs-6");					
                        $("#modificarCategoria").modal("hide");								
            }
        }
    });		
			
    if(std_id === "Activo")
    {
        $("#opciones_estado label").removeClass("active");
        $("#opciones_1").addClass("active");
    }
    else if(std_id === "Inactivo"){

                    $("#opciones_estado label").removeClass("active");
                    $("#opciones_2").addClass("active");
            }
            else{
                    $("#opciones_estado label").removeClass("active");
                    $("#opciones_3").addClass("active");
                }
    fn_destruirPaletaModificar();
}

function fn_administrarCategoriaAgregar(accion)
{
    var administrarCategoria = {"administrarCategoria":1};
    var html = "<thead><tr class='active'><th width='50%'>Nombre</th><th width='30%'>Como se Visualiza</th><th width='20%'>Activo</th></tr></thead>";
    var mag_id = $("#mod_magid").val();
    var std_id = $("#check_activo").is(":checked");
    var mag_descripcion = $("#nombreCategoria").val();
    var mag_colortexto = $("#colorTexto").spectrum("get").toHexString();
    var mag_color = $("#colorFondo").spectrum("get").toHexString();
    var cdn_id = $("#sess_cdn_id").val();
    var menu_id = 0;
    var usr_id = $("#usuario").val();

    if($.trim(mag_descripcion)==="" || $.trim(mag_colortexto)==="" || $.trim(mag_color)===""){
            alertify.error("Campos requeridos se encuentran vacios");
            return false;
    }

    if (std_id === true){
            std_id = "Activo";
    }else{
            std_id = "Inactivo";
    }
    
    send = administrarCategoria;
    send.accion = accion;
    send.mag_id = mag_id;
    send.mag_descripcion = mag_descripcion;
    send.mag_colortexto = mag_colortexto;
    send.mag_color = mag_color;
    send.menu_id = menu_id;
    send.cdn_id = cdn_id;
    send.usr_id = usr_id;
    send.std_id = std_id;
    $.getJSON("../adminmenucategoria/config_menuCategoria.php", send, function(datos) {
        if(datos.str>0){
            for(var i=0; i<datos.str; i++) {
                html = html + "<tr id='ctgr_mn_grp_id_"+datos[i]['mag_id']+"' onclick='fn_seleccionCategoria(\""+datos[i]['mag_id']+"\")' ondblclick='fn_modificarCategoria(\""+datos[i]['mag_id']+"\", \""+datos[i]['mag_descripcion']+"\", \""+datos[i]['mag_color']+"\", \""+datos[i]['mag_colortexto']+"\", \""+datos[i]['std_id']+"\")'><td>"+datos[i]['mag_descripcion']+"</td><td class='text-center'><button style='background-color:"+datos[i]['mag_color']+"; color:"+datos[i]['mag_colortexto']+"; height:60px; width:120px;'>"+datos[i]['mag_descripcion']+"</button></td>";
                if (datos[i]["std_id"] === "Activo"){
                        html+="<td class='text-center'><input type='checkbox' checked='checked' disabled/></td>";
                }else{
                        html+="<td><input type='checkbox' disabled/></td>";
                }
                $("#lista_categorias").html(html);
                $("#lista_categorias").dataTable({destroy: true});
                $("#lista_categorias_length").hide();
                $("#lista_categorias_paginate").addClass("col-xs-10");
                $("#lista_categorias_info").addClass("col-xs-10");
                $("#lista_categorias_length").addClass("col-xs-6");					
                $("#agregarCategoria").modal("hide");			
            }
        }
    });
        $("#opciones_estado label").removeClass("active");
        $("#opciones_1").addClass("active");
        fn_destruirPaletaAgregar();
}

function fn_actualizarTextoAgregar()
{
    $("#fondoEjemplo").val($("#nombreCategoria").val());
}

function fn_actualizarColorTextoAgregar()
{
    var colorTexto = $("#colorTexto").spectrum("get").toHexString();
    $("#fondoEjemplo").css({"color": colorTexto});
}

function fn_actualizarColorAgregar()
{
    var color = $("#colorFondo").spectrum("get").toHexString();
    $("#fondoEjemplo").css({"background": color});
}

function fn_actualizarTexto()
{
    $("#mod_fondoEjemplo").val($("#mod_nombreCategoria").val());
}

function fn_btn(boton,estado)
{
    if(estado)
    {
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

function fn_seleccionCategoria(mag_id)
{
    $("#lista_categorias tr").removeClass("success");
    $("#ctgr_mn_grp_id_"+mag_id+"").addClass("success");
    $("#magid").val(mag_id);
}

function fn_destruirPaletaAgregar()
{
    $("#colorTexto").spectrum("destroy");
    $("#colorFondo").spectrum("destroy");
}

function fn_destruirPaletaModificar()
{
    $("#mod_colorTexto").spectrum("destroy");
    $("#mod_colorFondo").spectrum("destroy");
}