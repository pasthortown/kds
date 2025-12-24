////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: ADMINISTRACION DE TIPOS FORMAS DE PAGO TARJETAS, LISTADO, AGREGAR Y MODIFICAR //
////////////////TABLAS: Tipo_Forma_Pago ////////////////////////////////////////////////////////////////
////////FECHA CREACION: 13/07/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

var pnt_Id = 0;
var accion = 0;
$(document).ready(function(){
						   
	fn_btn('agregar',1);
	fn_btn('cancelar',1);
	fn_cargarTipoformaspagoInactivos(16);
		
});

function fn_OpcionSeleccionada(ls_opcion)
{		
		if (ls_opcion == 'Todos') 
		{
			fn_cargarTipoformaspago();
		}else if(ls_opcion == 'Activos'){
			accion = 16;
			fn_cargarTipoformaspagoInactivos(accion);
		}else if(ls_opcion == 'Inactivos'){
			accion = 17;
			fn_cargarTipoformaspagoInactivos(accion);
		}
}

function fn_OpcionSeleccionadaModInsert()
{
		
		var ls_opcion = '';
		ls_opcion = $(":input[name=estados]:checked").val();
		
		if (ls_opcion == 'Todos') 
		{
			fn_cargarTipoformaspago();
		}else if(ls_opcion == 'Activos'){
			accion = 16;
			fn_cargarTipoformaspagoInactivos(accion);
			
		}else if(ls_opcion == 'Inactivos'){
			accion = 17;
			fn_cargarTipoformaspagoInactivos(accion);
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
/*FUNCION PARA CARGAR AL INICIO TABLA TIPO FORMAS DE PAGO   */
/*==========================================================*/
function fn_cargarTipoformaspago(){
	
 	accion = 1;
 	var html = "<thead><tr class='active'><th style='text-align:center'>Descripci&oacute;n</th><th style='text-align:center'>Activo</th></thead>";
 	send = {"administracionTipoformaspago": 1};
 	send.accion = accion;
 	$.getJSON("../adminTipoFormasPago/config_tipoformaspago.php", send, function(datos){
  	if(datos.str > 0)
  	{
		$('#tipoformaspago').show();
    	for(i=0;i<datos.str;i++)
    		{
     			html = html + "<tr id='"+i+"' onclick='fn_seleccion("+i+",\""+datos[i]['tfp_id']+"\");' ondblclick='fn_selecciondoble("+i+",\""+datos[i]['tfp_id']+"\")'><td>"+datos[i]['tfp_descripcion']+"</td>";     
	 			if(datos[i]['std_id']=='Activo'){
				html+="<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";				
				}
				if(datos[i]['std_id']=='Inactivo'){
				html+="<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";	
				}
    }       
    $('#tabla_tipoformaspago').html(html);
    $('#tabla_tipoformaspago').dataTable(
									  	{	//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
											'destroy': true   
										}
									 );
	$("#tabla_tipoformaspago_length").hide();
	$("#tabla_tipoformaspago_paginate").addClass('col-xs-10');
	$("#tabla_tipoformaspago_info").addClass('col-xs-12');
	$("#tabla_tipoformaspago_length").addClass('col-xs-6');
	
	//col-sm-9
	
  }else{
			alertify.error("No existen datos.");
			//$("#botonesTodos").hide();
			//$('#impresora').hide();
			//html+="<tr>";
			//html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
			$("#tabla_tipoformaspago").html(html);
			}
	}); 
}


/*======================================================================================================*/
/*FUNCION PARA CARGAR TABLA DE TIPO FORMAS DE PAGO ACTIVOS O INACTIVOS DE ACUERDO AL ESTADO 16 Y 17     */
/*======================================================================================================*/
function fn_cargarTipoformaspagoInactivos(accion){
	
 	var html = "<thead><tr class='active'><th style='text-align:center'>Descripci&oacute;n</th><th style='text-align:center'>Activo</th></thead>";
 	send = {"administracionTipoformaspago": 1};
 	send.accion = accion;
 	$.getJSON("../adminTipoFormasPago/config_tipoformaspago.php", send, function(datos){
  	if(datos.str > 0)
  	{
		$('#tipoformaspago').show();
    	for(i=0;i<datos.str;i++)
    		{
     			html = html + "<tr id='"+i+"' onclick='fn_seleccion("+i+",\""+datos[i]['tfp_id']+"\");' ondblclick='fn_selecciondoble("+i+",\""+datos[i]['tfp_id']+"\")'><td>"+datos[i]['tfp_descripcion']+"</td>";     
	 			if(datos[i]['std_id']=='Activo'){
				html+="<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";				
				}
				if(datos[i]['std_id']=='Inactivo'){
				html+="<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";	
				}
    }       
    $('#tabla_tipoformaspago').html(html);
    $('#tabla_tipoformaspago').dataTable(
									  	{	//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPA�OL
											'destroy': true   
										}
									 );
	$("#tabla_tipoformaspago_length").hide();
	$("#tabla_tipoformaspago_paginate").addClass('col-xs-10');
	$("#tabla_tipoformaspago_info").addClass('col-xs-12');
	$("#tabla_tipoformaspago_length").addClass('col-xs-6');
	
	//col-sm-9
	
  }else{
			alertify.error("No existen datos.");
			//$("#botonesTodos").hide();
			//$('#impresora').hide();
			//html+="<tr>";
			//html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
			$("#tabla_tipoformaspago").html(html);
			}
	});
}

function fn_seleccion(fila, codigo){

		Cod_Tipoformapago = codigo;
		$("#tabla_tipoformaspago tr").removeClass("success");
		$("#"+fila+"").addClass("success");
		$("#txt_idtipoformaspago").val(Cod_Tipoformapago);
}

function fn_selecciondoble(fila, codigo){

		lc_control = 3;
		Cod_Tipoformapago = codigo;
		$("#txt_idtipoformaspago").val(Cod_Tipoformapago);
		$("#tabla_tipoformaspago tr").removeClass("success");
		$("#"+fila+"").addClass("success");
		fn_cargarTipoformaspagoModifica(codigo)
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
						
		lc_control = 2;
						
		$('#ModalNuevo').modal('show');
		////////////Colocar foco en cualquier campo de la modal////////////////// CP
        $('#ModalNuevo').on('shown.bs.modal', function () { 
			$("#txt_descripcion").focus();					
		});
	}
	else if(accion=='Grabar'){
			if(lc_control==2){
					
					if($("#txt_descripcion").val()==''){
						alertify.error("Ingrese Descripci&oacute;n.");
						return false;
						}
						fn_guardarTipoformaspago();
						fn_OpcionSeleccionadaModInsert();
					
						
				}
			else if(lc_control==3){
					
					if($("#txt_descripcionMod").val()==''){
						alertify.error("Ingrese Descripci&oacute;n.");
						return false;
						}
					fn_guardarTipoformaspagoModifica();
					fn_OpcionSeleccionadaModInsert();
									
						
					}
		
		}
}

/*=====================================================================*/
/*FUNCION PARA GUARDAR UN NUEVO REGISTRO DE RED ADQUIRIENTE            */
/*=====================================================================*/
function fn_guardarTipoformaspago(){
	
		accion = 2;
		send = {"nuevoTipoformaspago": 1};
		send.accion = accion;
		send.descripcion = $("#txt_descripcion").val();
		
		if($("#option").is(':checked'))
			{send.estado='Activo';}
			else
			{send.estado='Inactivo';}
		$.getJSON("../adminTipoFormasPago/config_tipoformaspago.php", send, function(datos) {
			
		});
		$('#ModalNuevo').modal('hide');
		$("#botonesTodos").show();
		alertify.success("Tipo Forma de Pago agregada correctamente.");
}

/*==================================================================*/
/*FUNCION PARA TRAER DATOS CUANDO MODIFICAMOS UN REGISTRO           */
/*==================================================================*/
function fn_cargarTipoformaspagoModifica(codigo){
	//$('#titulomodalMod').html("Modificar Configuracion de Impresora: ");
	accion = 3;
	send = {"cargarTipoformaspagoMod": 1};
	send.tfp_id = codigo;
	send.accion = accion;
	$.getJSON("../adminTipoFormasPago/config_tipoformaspago.php", send, function(datos) {
		if(datos.str>0){
			$('#titulomodalMod').html(" "+datos[0]['tfp_descripcion']+" ");
			$('#txt_descripcionMod').val(datos[0]['tfp_descripcion']);
					
			$("#optionmod").empty();
			if(datos[0]['std_id']=='Activo'){
			$("#optionmod").prop("checked", true);  // para poner la marca
			}
			if(datos[0]['std_id']=='Inactivo'){
			$("#optionmod").prop("checked", false);
			}

		}
	});
}
/*=============================================================================================*/
/*FUNCION PARA GUARDAR DATOS CUANDO VAMOS A MODIFICAR UN REGISTRO                              */
/*=============================================================================================*/
function fn_guardarTipoformaspagoModifica(){

		accion = 4;
		//alert($("#txt_idcanalesimpresion").val());
		send = {"guardaTipoformaspagoMod": 1};
		send.accion = accion;
		send.descripcion = $("#txt_descripcionMod").val();
		send.tfp_id = $("#txt_idtipoformaspago").val();
		
		if($("#optionmod").is(':checked'))
			{send.estado='Activo';}
			else
			{send.estado='Inactivo';}
		$.getJSON("../adminTipoFormasPago/config_tipoformaspago.php", send, function(datos) {
			
		});
		$('#ModalMod').modal('hide');
		alertify.success("Tipo Forma de Pago actualizada correctamente.");
}