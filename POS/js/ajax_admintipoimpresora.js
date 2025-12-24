/////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO////////////////////////////////////////////////
///////////DESCRIPCION: TIPO DE IMPRESORA, CREAR MODIFICAR TIPO DE IMPRESORA/////////////
////////////////TABLAS: tipo_impresora///////////////////////////////////////////////////
////////FECHA CREACION: 19/06/2015///////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////

var pnt_Id = 0;
var accion = 0;
$(document).ready(function(){
						   
	fn_btn('agregar',1);
	fn_cargarTipoImpresoraInactivos(60);
	
});


function fn_OpcionSeleccionada(ls_opcion)
{
		if (ls_opcion == 'Todos'){
			fn_cargarTipoImpresora();
	   	}else if(ls_opcion == 'Activos'){
			accion = 60;
			fn_cargarTipoImpresoraInactivos(accion);
		}else if(ls_opcion == 'Inactivos'){
			accion = 61;
			fn_cargarTipoImpresoraInactivos(accion);
		}
}

function fn_OpcionSeleccionadaModInsert()
{
	
		var ls_opcion = '';
		ls_opcion = $(":input[name=estados]:checked").val();
		
		if (ls_opcion == 'Todos'){
			fn_cargarTipoImpresora();
	   	}else if(ls_opcion == 'Activos'){
			accion = 60;
			fn_cargarTipoImpresoraInactivos(accion);
		}else if(ls_opcion == 'Inactivos'){
			accion = 61;
			fn_cargarTipoImpresoraInactivos(accion);
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
/*FUNCION PARA CARGAR AL INICIO TABLA TIPO DE IMPRESORA     */
/*==========================================================*/
function fn_cargarTipoImpresora(){
	
 	accion = 1;
 	var html = "<thead><tr class='active'><th style='text-align:center'>Descripci&oacute;n</th><th style='text-align:center'>Codigo Apertura Caja</th><th style='text-align:center'>Corte Papel</th><th style='text-align:center'>Impresion Normal</th><th style='text-align:center'>Activo</th></thead>";
 	send = {"administracionTipoImpresora": 1};
 	send.accion = accion;
 	$.getJSON("../adminTipoImpresora/config_admintipoimpresora.php", send, function(datos){
  	if(datos.str > 0)
  	{ 
    	for(i=0;i<datos.str;i++)
    		{
     			html = html + "<tr id='"+i+"' onclick='fn_seleccion("+i+",\""+datos[i]['timp_id']+"\");' ondblclick='fn_selecciondoble("+i+",\""+datos[i]['timp_id']+"\")'><td>"+datos[i]['timp_descripcion']+"</td><td>"+datos[i]['timp_codigo_apertura_caja']+"</td><td>"+datos[i]['timp_corte_papel']+"</td><td>"+datos[i]['timp_impresion_normal']+"</td>";     
	 			if(datos[i]['std_id']==60){
				html+="<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";				
				}
				if(datos[i]['std_id']==61){
				html+="<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";	
				}
    }       
    $('#tabla_tipo_impresora').html(html);
    $('#tabla_tipo_impresora').dataTable(
									  	{	//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
											'destroy': true   
										}
									 );
	$("#tabla_tipo_impresora_length").hide();
	$("#tabla_tipo_impresora_paginate").addClass('col-xs-10');
	$("#tabla_tipo_impresora_info").addClass('col-xs-10');
	$("#tabla_tipo_impresora_length").addClass('col-xs-6');
	
	//col-sm-9
	
  }else{
			alertify.error("No existen datos para esta cadena.");
			//html+="<tr>";
			//html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
			$("#tabla_tipo_impresora").html(html);
			}
	}); 
}

/*==================================================================================================*/
/*FUNCION PARA CARGAR TABLA CANALES DE IMPRESION ACTIVOS O INACTIVOS DE ACUERDO A LA ACCION 60 Y 61 */
/*==================================================================================================*/
function fn_cargarTipoImpresoraInactivos(accion){
	
 	//accion = 1;
 	var html = "<thead><tr class='active'><th style='text-align:center'>Descripci&oacute;n</th><th style='text-align:center'>Codigo Apertura Caja</th><th style='text-align:center'>Corte Papel</th><th style='text-align:center'>Impresion Normal</th><th style='text-align:center'>Activo</th></thead>";
 	send = {"administracionTipoImpresora": 1};
 	send.accion = accion;
 	$.getJSON("../adminTipoImpresora/config_admintipoimpresora.php", send, function(datos){
  	if(datos.str > 0)
  	{ 
    	for(i=0;i<datos.str;i++)
    		{
     			html = html + "<tr id='"+i+"' onclick='fn_seleccion("+i+",\""+datos[i]['timp_id']+"\");' ondblclick='fn_selecciondoble("+i+",\""+datos[i]['timp_id']+"\")'><td>"+datos[i]['timp_descripcion']+"</td><td>"+datos[i]['timp_codigo_apertura_caja']+"</td><td>"+datos[i]['timp_corte_papel']+"</td><td>"+datos[i]['timp_impresion_normal']+"</td>";     
	 			if(datos[i]['std_id']==60){
				html+="<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";				
				}
				if(datos[i]['std_id']==61){
				html+="<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";	
				}
    }       
    $('#tabla_tipo_impresora').html(html);
    $('#tabla_tipo_impresora').dataTable(
									  	{	//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
											'destroy': true   
										}
									 );
	$("#tabla_tipo_impresora_length").hide();
	$("#tabla_tipo_impresora_paginate").addClass('col-xs-10');
	$("#tabla_tipo_impresora_info").addClass('col-xs-10');
	$("#tabla_tipo_impresora_length").addClass('col-xs-6');
	
	//col-sm-9
	
  }else{
			alertify.error("No existen datos.");
			//html+="<tr>";
			//html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
			$("#tabla_tipo_impresora").html(html);
			}
	}); 
}

function fn_seleccion(fila, codigo){

		Cod_Tipo_Impresora = codigo;
		$("#tabla_tipo_impresora tr").removeClass("success");
		$("#"+fila+"").addClass("success");
		$("#txt_idtipoimpresora").val(Cod_Tipo_Impresora);
}

function fn_selecciondoble(fila, codigo){

		lc_control = 3;
		Cod_Tipo_Impresora = codigo;
		$("#txt_idtipoimpresora").val(Cod_Tipo_Impresora);
		$("#tabla_tipo_impresora tr").removeClass("success");
		$("#"+fila+"").addClass("success");
		fn_cargarTipoImpresoraModifica(codigo)
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
		$('#txt_codigoaperturacaja').val('');
		$('#txt_codigocortepapel').val('');
		$('#txt_codigoimpresionnormal').val('');

		lc_control = 2;
		//fn_traerNombreCadena();
		$('#ModalNuevo').modal('show');
		////////////Colocar foco en cualquier campo de la modal////////////////// CP
        $('#ModalNuevo').on('shown.bs.modal', function () { 
			$("#txt_descripcion").focus();					
		});
	}
	else if(accion=='Grabar'){
			if(lc_control==2){
										
					if($("#txt_descripcion").val()==''){
						alertify.error("Ingrese nombre Tipo de Impresora.");
						return false;
						}
					if($("#txt_codigoaperturacaja").val()==''){
						alertify.error("Ingrese codigo de Apertura de Caja.");
						return false;
						}
					if($("#txt_codigocortepapel").val()==''){
						alertify.error("Ingrese codigo Corte de Papel.");
						return false;
						}
					if($("#txt_codigoimpresionnormal").val()==''){
						alertify.error("Ingrese codigo de Impresion Normal.");
						return false;
						}
					fn_guardarTiposImpresora();
					//fn_OpcionSeleccionadaModInsert()
						
				}
			else if(lc_control==3){
					if($("#txt_descripcionMod").val()==''){
						alertify.error("Ingrese nombre Tipo de Impresora.");
						return false;
						}
					if($("#txt_codigoaperturacajaMod").val()==''){
						alertify.error("Ingrese codigo de Apertura de Caja.");
						return false;
						}
					if($("#txt_codigocortepapelMod").val()==''){
						alertify.error("Ingrese codigo Corte de Papel.");
						return false;
						}
					if($("#txt_codigoimpresionnormalMod").val()==''){
						alertify.error("Ingrese codigo de Impresion Normal.");
						return false;
						}
					fn_guardarTipoImpresoraModifica();
					//fn_OpcionSeleccionadaModInsert()
						
						
					}
		
		}
}

/*==================================================================*/
/*FUNCION PARA GUARDAR UN NUEVO REGISTRO EN TIPO DE IMPRESORA       */
/*==================================================================*/
function fn_guardarTiposImpresora(){
		accion = 2;
		send = {"nuevoTipoImpresora": 1};
		send.accion = accion;
		send.descripcion = $("#txt_descripcion").val();
		send.codigoaperturacaja=$("#txt_codigoaperturacaja").val();
		send.codigocortepapel=$("#txt_codigocortepapel").val();
		send.codigoimpresionnormal=$("#txt_codigoimpresionnormal").val();
		if($("#option").is(':checked'))
			{send.estado=60;}
			else
			{send.estado=61;}
		$.getJSON("../adminTipoImpresora/config_admintipoimpresora.php", send, function(datos) {
				alertify.success("Tipo de Impresora ingresado exitosamente.");
				fn_OpcionSeleccionadaModInsert();
		});
		$('#ModalNuevo').modal('hide');
		
}

/*==================================================================*/
/*FUNCION PARA TRAER DATOS CUANDO MODIFICAMOS UN REGISTRO           */
/*==================================================================*/
function fn_cargarTipoImpresoraModifica(codigo){
	//$('#titulomodalMod').html("Modificar Tipo de Impresora: ");
	accion = 3;
	send = {"cargarTipoImpresoraMod": 1};
	send.timp_id = codigo;
	send.accion = accion;
	$.getJSON("../adminTipoImpresora/config_admintipoimpresora.php", send, function(datos) {
		if(datos.str>0){
			$('#titulomodalMod').empty();
			$('#titulomodalMod').append(" "+datos[0]['timp_descripcion']+" ");
			$('#txt_descripcionMod').val(datos[0]['timp_descripcion']);
			$('#txt_codigoaperturacajaMod').val(datos[0]['timp_codigo_apertura_caja']);
			$('#txt_codigocortepapelMod').val(datos[0]['timp_corte_papel']);
			$('#txt_codigoimpresionnormalMod').val(datos[0]['timp_impresion_normal']);
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
function fn_guardarTipoImpresoraModifica(){

		accion = 4;
		//alert($("#txt_idcanalesimpresion").val());
		send = {"guardaTipoImpresoraMod": 1};
		send.accion = accion;
		send.timp_id = $("#txt_idtipoimpresora").val();
		send.descripcion = $('#txt_descripcionMod').val();
		send.codigoaperturacaja = $('#txt_codigoaperturacajaMod').val();
		send.codigocortepapel = $('#txt_codigocortepapelMod').val();
		send.codigoimpresionnormal = $('#txt_codigoimpresionnormalMod').val();
		
		if($("#optionmod").is(':checked'))
			{send.estado=60;}
			else
			{send.estado=61;}
		$.getJSON("../adminTipoImpresora/config_admintipoimpresora.php", send, function(datos) {
				alertify.success("Tipo de Impresora actualizado exitosamente.");	
				fn_OpcionSeleccionadaModInsert();
		});
		$('#ModalMod').modal('hide');
		
}