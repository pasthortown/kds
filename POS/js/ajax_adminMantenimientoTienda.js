////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: PIERRE QUITIAQUEZ///////////////////////////////////////////////////////////////
///////////DESCRIPCION: PLUS/PRECIOS //////////////////////////////////////////////////////////
////////////////TABLAS: PLUS ////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

var  nombreMenu = '';

$(document).ready(function(){
    $('#mdl_rdn_pdd_crgnd').show();
    fn_cargarMenu();
    $('#mdl_rdn_pdd_crgnd').hide();
    $('#tablas_involucradas').hide();

});


function fn_cargarMenu() {
    
    Accion = 'M';
    send = {"cargarMenu": 1};
    send.accion = Accion;
    send.cdn_id = $("#sess_cdn_id").val();
    // alert($("#sess_cdn_id").val());
    send.menu_id = 0;
    send.mag_id = 0;
    $.getJSON("../adminordenpedido/config_tomaPedido.php", send, function (datos) {
        if (datos.str > 0) {
            $("#menucadena").prop("disabled", false);
            $("#menucadena").html("");
            $('#menucadena').html("<option selected value='0'>- Seleccionar Menu -</option>");
            for (i = 0; i < datos.str; i++) {
                $("#menucadena").append("<option value=" + datos[i]['menu_id'] + " idclasificacion='" + datos[i]['IDClasificacion'] + "'>" + datos[i]['menu_Nombre'] + "</option>");
            }
            $("#menucadena").val(0);
        } else {
            alertify.error("No existen men&uacute;s para esta cadena");
            $("#menucadena").empty();
            $("#menucadena").prop("disabled", "disabled");
            fn_limpiarDatos();
        }
    });

}

function fn_NombreMenu(idMenu){

    if($("#menucadena").val() != 0){
        menu_cadena= $("#menucadena").val();
        console.log($("#menucadena").val());

        send = {"fn_NombreMenu": 1};
        send.cdn_id = $("#sess_cdn_id").val();
        send.menu_id = menu_cadena;
        send.mag_id = 0;
        $.ajax({async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"../adminMantenimientoTienda/config_admiMantenimientoTienda.php",data:send,
		success:function(datos)
		{
			if(datos.str==0){
				//$("#divrestaurante").hide();
				alertify.alert("No existen datos para este Restaurante");}
			else if(datos.str>0)
			{
				nombreMenu=datos['menu_Nombre'];
                alert(datos['menu_Nombre']);
			}
		}
	});	

    }else{
        alert("Seleccione un menú válido");
    }    
}

function fn_IgualarTablaMenu(){
    $('#cargando').show();
    send={"fn_IgualarTablaMenu":1};
	$.ajax({async:true,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"../adminMantenimientoTienda/config_admiMantenimientoTienda.php",data:send,
		success:function(datos)
		{
			if(datos.str==0){
                $('#cargando').hide();
				alertify.alert("Igualación de datos Fallida");
            }
			else if(datos.str>0)
			{
                $('#cargando').hide();
                alertify.alert("Igualación de datos exitosa");	
                document.getElementById("respuesta_registros").innerHTML=`

                    <p><b>Datos Agregados </b></p> 
                    <p><b>Plus: </b> ${datos[0]["plus_agregados"]}   </p> 
                    <p><b>Menus: </b> ${datos[0]["menus_agregados"]}   </p> 
                    <p><b>Menus Agrupación: </b>${datos[0]["menu_agrupacion"]}    </p> 
                    <p><b>Menus Categorias: </b> ${datos[0]["menu_categorias"]}   </p> 
                    <p><b>Menus por agrupación de producto: </b>${datos[0]["menu_agrupacion_producto"]}    </p> 
                    <p><b>Categorias de botones: </b> ${datos[0]["categorias_botones"]}  </p> 
                    <p><b>Preguntas Sugeridas: </b>${datos[0]["preguntas_sugeridas"]}   </p> 
                    <p><b>plu preguntas: </b>${datos[0]["plu_pregunta"]}   </p> 
                    <p><b>Colección usuarios: </b>${datos[0]["coleccion_usuarios"]}   </p> 
                    <p><b>Colección datos usuarios: </b>${datos[0]["coleccion_datos_usuarios"]}   </p> 
                    <p><b>Usuarios Colección Datos: </b>${datos[0]["usuario_coleccion_datos"]}   </p> 
                    <p><b>Acceso pos: </b>${datos[0]["acceso_pos"]}   </p> 
                    <p><b>Pantalla pos: </b>${datos[0]["pantalla_pos"]}   </p> 
                    <p><b>Permiso perfil pos: </b>${datos[0]["permiso_perfil_pos"]}   </p> 
                    <p><b>Módulo:</b>${datos[0]["modulo"]}   </p> 
                    <p><b>Ciudad: </b>${datos[0]["ciudad"]}   </p> 
                    <p><b>Clasificación </b>${datos[0]["clasificacion"]}   </p> 
                
                `;			
			}
		}
	});	
    
}

function fn_igualarBotones(){
    $('#cargando').show();
    if($("#menucadena").val() != 0){   

        menu_cadena= $("#menucadena").val();
        console.log($("#menucadena").val());

        send = {"igualarBotonesMenu": 1};
        send.cdn_id = $("#sess_cdn_id").val();
        send.menu_id = menu_cadena;
        send.mag_id = 0;
        $.ajax({async:true,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"../adminMantenimientoTienda/config_admiMantenimientoTienda.php",data:send,
            success:function(datos)
            {

                if(datos.str==0){
                    $('#cargando').hide();
                    alertify.alert("No existen datos para este Restaurante");
                    $('#tablas_involucradas').show();

                } else if(datos.str>0)
                {
                    $('#cargando').hide();
                    alertify.alert("Menu "+datos[0]["menu"] +" Ejecutado de forma <b>"+datos[0]["respuesta"]+"</b>");
                    $('#tablas_involucradas').show();

                }
            }
	    });	

    }else{
        alertify.alert("Seleccione un menú");
    }
}





