////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////
///////////DESCRIPCION: CANALES DE IMPRESION, CREAR MODIFICAR CANAL DE IMPRESION////
/////////////////////// POR CADENA /////////////////////////////////////////////////
////////////////TABLAS: canal_impresion, cadena/////////////////////////////////////
////////FECHA CREACION: 18/06/2015///////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

var pnt_Id = 0;
var accion = 0;
$(document).ready(function(){
						   
	fn_btn('agregar',1);
	fn_btn('cancelar',1);
	fn_cargarCanalesImpresionActivosInactivos(60);
	
});


function fn_OpcionSeleccionada(ls_opcion)
{
	/*$('#opt_Todos, #opt_Activos, #opt_Inactivos').change(function(){
		var ls_opcion = '';
		ls_opcion = $(":input[name=estados]:checked").val();*/
		
		if (ls_opcion == 'Todos'){
			fn_cargarCanalesImpresion();
	   	}else if(ls_opcion == 'Activos'){
			accion = 60;
			fn_cargarCanalesImpresionActivosInactivos(accion);
		}else if(ls_opcion == 'Inactivos'){
			accion = 61;
			fn_cargarCanalesImpresionActivosInactivos(accion);
		}
	//});
}

function fn_OpcionSeleccionadaModInsert()
{
	
		var ls_opcion = '';
		ls_opcion = $(":input[name=estados]:checked").val();
		
		if (ls_opcion == 'Todos'){
			fn_cargarCanalesImpresion();
	   	}else if(ls_opcion == 'Activos'){
			accion = 60;
			fn_cargarCanalesImpresionActivosInactivos(accion);
		}else if(ls_opcion == 'Inactivos'){
			accion = 61;
			fn_cargarCanalesImpresionActivosInactivos(accion);
		}
}
/*===================================================*/
/*FUNCION PARA TRAER LOS BOTONES DE ADMINISTRACION   */
/*===================================================*/
function fn_btn(boton,estado){
	if(estado){
		$("#btn_"+boton).css("background"," url('../../imagenes/admin_resources/"+boton+".png') 14px 4px no-repeat");
		$("#btn_"+boton).removeAttr("disabled");
		$("#btn_"+boton).addClass("botonhabilitado");
		$("#btn_"+boton).removeClass("botonbloqueado");
	}else{
		$("#btn_"+boton).css("background"," url('../../imagenes/admin_resources/"+boton+"_bloqueado.png') 14px 4px no-repeat");
		$("#btn_"+boton).prop('disabled', true);
		$("#btn_"+boton).addClass("botonbloqueado");
		$("#btn_"+boton).removeClass("botonhabilitado");
	}
}

/*==========================================================*/
/*FUNCION PARA CARGAR AL INICIO TABLA CANALES DE IMPRESION  */
/*==========================================================*/
function fn_cargarCanalesImpresionRespaldo(){
		accion = 1;
		send = {"cargarCanalesImpresion": 1};
		send.accion = accion;
		$.getJSON("../admincanalesimpresion/config_canalesimpresion.php", send, function(datos) {
			$("#tabla_canales_impresion").show();
			html="<tr class='active'>";
			html+="<th style='text-align:center'>Descripci&oacute;n</th>";
			html+="<th style='text-align:center'>Activo</th>";
			html+="</tr>";
			$("#canales_impresion").empty();
		if(datos.str>0){
			
			for(i=0; i<datos.str; i++) {
			html+="<tr id='"+i+"' onclick='fn_seleccion("+i+",\""+datos[i]['cimp_id']+"\");' ondblclick='fn_selecciondoble("+i+",\""+datos[i]['cimp_id']+"\")'>";
			html+="<td>"+datos[i]['cimp_descripcion']+"</td>";
						
			if(datos[i]['std_id']==60){
			html+="<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";				}
			if(datos[i]['std_id']==61){
			html+="<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";	
					}		
			$("#canales_impresion").html(html);
				}
			}else{
			//alertify.error("No existen datos para esta cadena.");
			html+="<tr>";
			html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
			$("#canales_impresion").html(html);
			}
		});
}

/*==========================================================*/
/*FUNCION PARA CARGAR AL INICIO TABLA CANALES DE IMPRESION  */
/*==========================================================*/
function fn_cargarCanalesImpresion(){
	
 	accion = 1;
 	var html = "<thead><tr class='active'><th style='text-align:center'>Descripci&oacute;n</th><th style='text-align:center'>Activo</th></thead>";
 	send = {"cargarCanalesImpresion": 1};
 	send.accion = accion;
 	$.getJSON("../admincanalesimpresion/config_canalesimpresion.php", send, function(datos){
  	if(datos.str > 0)
  	{ 
    	for(i=0;i<datos.str;i++)
    		{
     			html = html + "<tr id='"+i+"' onclick='fn_seleccion("+i+",\""+datos[i]['cimp_id']+"\");' ondblclick='fn_selecciondoble("+i+",\""+datos[i]['cimp_id']+"\")'><td>"+datos[i]['cimp_descripcion']+"</td>";     
	 			if(datos[i]['std_id']==60){
				html+="<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";				
				}
				if(datos[i]['std_id']==61){
				html+="<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";	
				}
    }       
    $('#canales_impresion').html(html);
    $('#canales_impresion').dataTable(
									  	{	
											'destroy': true
										}
									 );
	$("#canales_impresion_length").hide();
	$("#canales_impresion_paginate").addClass('col-xs-10');
	$("#canales_impresion_info").addClass('col-xs-10');
	$("#canales_impresion_length").addClass('col-xs-6');
	
	//col-sm-9
	
  }else{
			alertify.error("No existen datos para esta cadena.");
			//html+="<tr>";
			//html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
			$("#canales_impresion").html(html);
			}
	}); 
}

/*==================================================================================================*/
/*FUNCION PARA CARGAR TABLA CANALES DE IMPRESION ACTIVOS O INACTIVOS DE ACUERDO A LA ACCION 60 Y 61 */
/*==================================================================================================*/
function fn_cargarCanalesImpresionActivosInactivos(accion){
	
 	//accion = 60;
 	var html = "<thead><tr class='active'><th style='text-align:center'>Descripci&oacute;n</th><th style='text-align:center'>Activo</th></thead>";
 	send = {"cargarCanalesImpresion": 1};
 	send.accion = accion;
 	$.getJSON("../admincanalesimpresion/config_canalesimpresion.php", send, function(datos){
  	if(datos.str > 0)
  	{ 
    	for(i=0;i<datos.str;i++)
    		{
     			html = html + "<tr id='"+i+"' onclick='fn_seleccion("+i+",\""+datos[i]['cimp_id']+"\");' ondblclick='fn_selecciondoble("+i+",\""+datos[i]['cimp_id']+"\")'><td>"+datos[i]['cimp_descripcion']+"</td>";     
	 			if(datos[i]['std_id']==60){
				html+="<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";				
				}
				if(datos[i]['std_id']==61){
				html+="<td style='text-align: center;'><input type='checkbox' disabled='disabled' id='optiondetalle'></td></tr>";	
				}
    }       
    $('#canales_impresion').html(html);
    $('#canales_impresion').dataTable(
									  	{
											'destroy': true
										}
									 );
	$("#canales_impresion_length").hide();
	$("#canales_impresion_paginate").addClass('col-xs-10')
	$("#canales_impresion_info").addClass('col-xs-6')
	$("#canales_impresion_filter").addClass('col-xs-6')
	
	//col-sm-9
	
  }else{
			alertify.error("No existen datos.");
			//html+="<tr>";
			//html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
			$("#canales_impresion").html(html);
			}
	}); 
}

function fn_seleccion(fila, codigo){

		Cod_Canales_Impresion = codigo;
		$("#canales_impresion tr").removeClass("success");
		$("#"+fila+"").addClass("success");
		$("#txt_idcanalesimpresion").val(Cod_Canales_Impresion);
}

function fn_selecciondoble(fila, codigo){

		lc_control = 3;
		Cod_Canales_Impresion = codigo;
		$("#txt_idcanalesimpresion").val(Cod_Canales_Impresion);
		$("#formas_pago tr").removeClass("success");
		$("#"+fila+"").addClass("success");
		fn_traerNombreCadena();
		fn_cargarCanalesImpresionModifica(codigo)
		$('#ModalMod').modal('show');
}

/*====================================================================*/
/*FUNCION PARA ACCIONAR SI ES NUEVO O MODIFICAR Y LLAMAR A LA MODAL   */
/*====================================================================*/
function fn_accionar(accion)
{
	
	if(accion=='Nuevo')
	{	
		$("#txt_descripcion").val('');
		lc_control = 2;
		fn_traerNombreCadena();
		$('#ModalNuevo').modal('show');
		////////////Colocar foco en cualquier campo de la modal////////////////// CP
        $('#ModalNuevo').on('shown.bs.modal', function () { 
			$("#txt_descripcion").focus();					
		});
	}
	else if(accion=='Grabar'){
			if(lc_control==2){
					
					if($("#txt_descripcion").val()==''){
						alertify.error("Ingrese descripcion de Canal de Impresion.");
						return false;
						}
					fn_guardarCanalesImpresion();
					//fn_OpcionSeleccionadaModInsert();
				
						
				}
			else if(lc_control==3){
					if($("#txt_descripcionmod").val()==''){
						alertify.error("Ingrese descripcion de Canal de Impresion.");
						return false;
						}
					fn_guardarCanalesImpresionModifica();
					//fn_OpcionSeleccionadaModInsert();
		
						
						
					}
		
		}
}

/*===========================================*/
/*FUNCION PARA TRAER EL NOMBRE DE LA CADENA  */
/*===========================================*/
function fn_traerNombreCadena(){
	accion = 0;
	send = {"nombrecadena": 1};
	send.accion = accion;
	$.getJSON("../admincanalesimpresion/config_canalesimpresion.php", send, function(datos){
		if(datos.str>0){
			for(i=0; i<datos.str; i++) {
				$("#txt_cadena").val(datos[i]['cdn_descripcion']);
				$("#txt_cadenaMod").val(datos[i]['cdn_descripcion']);
			}
		}
	});
}
/*==================================================================*/
/*FUNCION PARA GUARDAR UN NUEVO REGISTRO EN CANALES DE IMPRESION   */
/*==================================================================*/
function fn_guardarCanalesImpresion(){
		accion = 2;
		send = {"nuevoCanalImpresion": 1};
		send.accion = accion;
		send.descripcion = $("#txt_descripcion").val();
		if($("#option").is(':checked'))
			{send.estado=60;}
			else
			{send.estado=61;}
		$.getJSON("../admincanalesimpresion/config_canalesimpresion.php", send, function(datos) {
				alertify.success("Canal de Impresion agregado correctamente.");	
				fn_OpcionSeleccionadaModInsert();
		});
		$('#ModalNuevo').modal('hide');
		
}

/*==================================================================*/
/*FUNCION PARA TRAER DATOS CUANDO MODIFICAMOS UN REGISTRO           */
/*==================================================================*/
function fn_cargarCanalesImpresionModifica(codigo){
	//$('#titulomodalMod').html("Modificar Canal de Impresion: ");
	accion = 3;
	send = {"cargarCanalesImpresionMod": 1};
	send.cimp_id = codigo;
	send.accion = accion;
	$.getJSON("../admincanalesimpresion/config_canalesimpresion.php", send, function(datos) {
		if(datos.str>0){
			$('#titulomodalMod').html(" "+datos[0]['cimp_descripcion']+" ");
			$('#txt_descripcionmod').val(datos[0]['cimp_descripcion']);
			
			if(datos[0]['std_id']==60){
			$("#optionmod").prop("checked", true);  // para poner la marca
			}
			if(datos[0]['std_id']==61){
			$("#optionmod").prop("checked", false);
			}			
		}
	});
}

/*=============================================================================================*/
/*FUNCION QUE LLAMA A LA FUNCION DE TRAER DATOS PARA MODIFICAR Y ACCION=2 CUANDO ES MODIFICAR  */
/*=============================================================================================*/
function fn_guardarCanalesImpresionModifica(){

		accion = 4;
		//alert($("#txt_idcanalesimpresion").val());
		send = {"guardaCanalImpresionMod": 1};
		send.accion = accion;
		send.cimp_id = $("#txt_idcanalesimpresion").val();
		send.descripcion = $("#txt_descripcionmod").val();
		
		if($("#optionmod").is(':checked'))
			{send.estado=60;}
			else
			{send.estado=61;}
		$.getJSON("../admincanalesimpresion/config_canalesimpresion.php", send, function(datos) {
				alertify.success("Canal de Impresion actualizado correctamente.");	
				 fn_OpcionSeleccionadaModInsert();
		});
		$('#ModalMod').modal('hide');
		
		
}