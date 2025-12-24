////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: ADMINISTRACION DE RED ADQUIRIENTE, LISTADO, AGREGAR Y MODIFICAR     ////////////
////////////////TABLAS: Red_Adquiriente ////////////////////////////////////////////////////////////////
////////FECHA CREACION: 13/07/2015

var pnt_Id = 0;
var accion = 0;
$(document).ready(function(){
						   
	fn_btn('agregar',1);
	fn_cargarRedadquirienteInactivos(16);
		
});

function fn_OpcionSeleccionada(ls_opcion)
{		
		if (ls_opcion == 'Todos') 
		{
			fn_cargarRedadquiriente();
		}else if(ls_opcion == 'Activos'){
			accion = 16;
			fn_cargarRedadquirienteInactivos(accion);
		}else if(ls_opcion == 'Inactivos'){
			accion = 17;
			fn_cargarRedadquirienteInactivos(accion);
		}
}

function fn_OpcionSeleccionadaModInsert()
{
		
		var ls_opcion = '';
		ls_opcion = $(":input[name=estados]:checked").val();
		
		if (ls_opcion == 'Todos') 
		{
			fn_cargarRedadquiriente();
		}else if(ls_opcion == 'Activos'){
			accion = 16;
			fn_cargarRedadquirienteInactivos(accion);
			
		}else if(ls_opcion == 'Inactivos'){
			accion = 17;
			fn_cargarRedadquirienteInactivos(accion);
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
/*FUNCION PARA CARGAR AL INICIO TABLA DE RED ADQUIRIENTE    */
/*==========================================================*/
function fn_cargarRedadquiriente(){
	
 	accion = 1;
 	var html = "<thead><tr class='active'><th style='text-align:center; width:20%'>C&oacute;digo Red Adquiriente</th><th style='text-align:center; width:60%'>Descripci&oacute;n</th><th style='text-align:center'>Activo</th></thead>";
 	send = {"administracionRedadquiriente": 1};
 	send.accion = accion;
 	$.getJSON("../adminRedAdquiriente/config_redadquiriente.php", send, function(datos){
  	if(datos.str > 0)
  	{
		$('#redadquiriente').show();
    	for(i=0;i<datos.str;i++)
    		{
     			html = html + "<tr id='"+i+"' onclick='fn_seleccion("+i+",\""+datos[i]['rda_id']+"\");' ondblclick='fn_selecciondoble("+i+",\""+datos[i]['rda_id']+"\")'><td style='text-align:center; width:20%'>"+datos[i]['rda_red_adquiriente']+"</td><td style='text-align:center; width:60%'>"+datos[i]['rda_descripcion']+"</td>";     
	 			if(datos[i]['std_id']==16){
				html+="<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";				
				}
				if(datos[i]['std_id']==17){
				html+="<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";	
				}
    }       
    $('#tabla_redadquiriente').html(html);
    $('#tabla_redadquiriente').dataTable(
									  	{	//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
											'destroy': true   
										}
									 );
	$("#tabla_redadquiriente_length").hide();
	$("#tabla_redadquiriente_paginate").addClass('col-xs-10');
	$("#tabla_redadquiriente_info").addClass('col-xs-12');
	$("#tabla_redadquiriente_length").addClass('col-xs-6');
	
	//col-sm-9
	
  }else{
			alertify.error("No existen datos.");
			//$("#botonesTodos").hide();
			//$('#impresora').hide();
			//html+="<tr>";
			//html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
			$("#tabla_redadquiriente").html(html);
			}
	}); 
}


/*==================================================================================================*/
/*FUNCION PARA CARGAR TABLA DE RED ADQUIRIENTE ACTIVOS O INACTIVOS DE ACUERDO AL ESTADO 16 Y 17     */
/*==================================================================================================*/
function fn_cargarRedadquirienteInactivos(accion){
	
 	var html = "<thead><tr class='active'><th style='text-align:center; width:20%'>C&oacute;digo Red Adquiriente</th><th style='text-align:center; width:60%'>Descripci&oacute;n</th><th style='text-align:center'>Activo</th></tr></thead>";
 	send = {"administracionRedadquiriente": 1};
 	send.accion = accion;
 	$.getJSON("../adminRedAdquiriente/config_redadquiriente.php", send, function(datos){
  	if(datos.str > 0)
  	{
		$('#redadquiriente').show();
    	for(i=0;i<datos.str;i++)
    		{
     			html = html + "<tr id='"+i+"' onclick='fn_seleccion("+i+",\""+datos[i]['rda_id']+"\");' ondblclick='fn_selecciondoble("+i+",\""+datos[i]['rda_id']+"\")'><td style='text-align:center; width:20%'>"+datos[i]['rda_red_adquiriente']+"</td><td style='text-align:center; width:60%'>"+datos[i]['rda_descripcion']+"</td>";     
	 			if(datos[i]['std_id']==16){
				html+="<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";				
				}
				if(datos[i]['std_id']==17){
				html+="<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";	
				}
    }       
    $('#tabla_redadquiriente').html(html);
    $('#tabla_redadquiriente').dataTable(
									  	{	//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
											'destroy': true   
										}
									 );
	$("#tabla_redadquiriente_length").hide();
	$("#tabla_redadquiriente_paginate").addClass('col-xs-10');
	$("#tabla_redadquiriente_info").addClass('col-xs-12');
	$("#tabla_redadquiriente_length").addClass('col-xs-6');
	
	//col-sm-9
	
  }else{
			alertify.error("No existen datos.");
			//$("#botonesTodos").hide();
			//$('#impresora').hide();
			//html+="<tr>";
			//html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
			$("#tabla_redadquiriente").html(html);
			}
	});
}

function fn_seleccion(fila, codigo){

		Cod_Redadquiriente = codigo;
		$("#tabla_redadquiriente tr").removeClass("success");
		$("#"+fila+"").addClass("success");
		$("#txt_idredadquiriente").val(Cod_Redadquiriente);
}

function fn_selecciondoble(fila, codigo){

		lc_control = 3;
		Cod_Redadquiriente = codigo;
		$("#txt_idredadquiriente").val(Cod_Redadquiriente);
		$("#tabla_redadquiriente tr").removeClass("success");
		$("#"+fila+"").addClass("success");
		fn_cargarRedadquirienteModifica(codigo)
		$('#ModalMod').modal('show');
}

/*====================================================================*/
/*FUNCION PARA ACCIONAR SI ES NUEVO O MODIFICAR Y LLAMAR A LA MODAL   */
/*====================================================================*/
function fn_accionar(accion)
{
	
	if(accion=='Nuevo')
	{	
		$('#txt_descripcion').val('');
		$('#txt_codreadquiriente').val('');
				
		lc_control = 2;
						
		$('#ModalNuevo').modal('show');
		////////////Colocar foco en cualquier campo de la modal////////////////// CP
        $('#ModalNuevo').on('shown.bs.modal', function () { 
			$("#txt_codreadquiriente").focus();					
		});
	}
	else if(accion=='Grabar'){
			if(lc_control==2){
					
					if($("#txt_codreadquiriente").val()==''){
						alertify.error("Ingrese Codigo Red Adquiriente.");
						return false;
						}
					if($("#txt_descripcion").val()==''){
						alertify.error("Ingrese Descripci&oacute;n.");
						return false;
						}
						fn_guardarRedadquiriente();
						//fn_OpcionSeleccionadaModInsert();
					
						
				}
			else if(lc_control==3){
					if($("#txt_codredadquirienteMod").val()==''){
						alertify.error("Ingrese Codigo Red Adquiriente.");
						return false;
						}
					if($("#txt_descripcionMod").val()==''){
						alertify.error("Ingrese Descripci&oacute;n.");
						return false;
						}
					fn_guardarRedadquirienteModifica();
					//fn_OpcionSeleccionadaModInsert();
									
						
					}
		
		}
}

/*=====================================================================*/
/*FUNCION PARA GUARDAR UN NUEVO REGISTRO DE RED ADQUIRIENTE            */
/*=====================================================================*/
function fn_guardarRedadquiriente(){
	
		accion = 2;
		send = {"nuevoRedadquiriente": 1};
		send.accion = accion;
		send.codredaquiriente = $("#txt_codreadquiriente").val();
		send.descripcion = $("#txt_descripcion").val();
		
		if($("#option").is(':checked'))
			{send.estado=16;}
			else
			{send.estado=17;}
		$.getJSON("../adminRedAdquiriente/config_redadquiriente.php", send, function(datos) {
				alertify.success("Red Adquiriente agregada correctamente.");	
				fn_OpcionSeleccionadaModInsert();
		});
		$('#ModalNuevo').modal('hide');
		$("#botonesTodos").show();
		
}

/*==================================================================*/
/*FUNCION PARA TRAER DATOS CUANDO MODIFICAMOS UN REGISTRO           */
/*==================================================================*/
function fn_cargarRedadquirienteModifica(codigo){
	//$('#titulomodalMod').html("Modificar Configuracion de Impresora: ");
	accion = 3;
	send = {"cargarRedadquirienteMod": 1};
	send.rda_id = codigo;
	send.accion = accion;
	$.getJSON("../adminRedAdquiriente/config_redadquiriente.php", send, function(datos) {
		if(datos.str>0){
			$('#titulomodalMod').html(" "+datos[0]['rda_descripcion']+" ");
			$('#txt_descripcionMod').val(datos[0]['rda_descripcion']);
			$('#txt_codredadquirienteMod').val(datos[0]['rda_red_adquiriente']);
		
			$("#optionmod").empty();
			if(datos[0]['std_id']==16){
			$("#optionmod").prop("checked", true);  // para poner la marca
			}
			if(datos[0]['std_id']==17){
			$("#optionmod").prop("checked", false);
			}

		}
	});
}
/*=============================================================================================*/
/*FUNCION PARA GUARDAR DATOS CUANDO VAMOS A MODIFICAR UN REGISTRO                              */
/*=============================================================================================*/
function fn_guardarRedadquirienteModifica(){

		accion = 4;
		//alert($("#txt_idcanalesimpresion").val());
		send = {"guardaRedaquirienteMod": 1};
		send.accion = accion;
		send.codredaquiriente = $("#txt_codredadquirienteMod").val();
		send.descripcion = $("#txt_descripcionMod").val();
		send.rda_id = $("#txt_idredadquiriente").val();
		
		if($("#optionmod").is(':checked'))
			{send.estado=16;}
			else
			{send.estado=17;}
		$.getJSON("../adminRedAdquiriente/config_redadquiriente.php", send, function(datos) {
			alertify.success("Red Adquiriente actualizada correctamente.");
			fn_OpcionSeleccionadaModInsert();
		});
		$('#ModalMod').modal('hide');
		
}