////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: CONFIGURACION IMPRESORA, CREAR MODIFICAR CONFIGURACION DE IMPRESORA ////////////
////////////////TABLAS: Impresora, Tipo_impresora, Canal_Impresora_Estacion, Restaurante ///////////////
////////FECHA CREACION: 22/06/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

var pnt_Id = 0;
var accion = 0;

$(document).ready(function(){
						   
	fn_btn('agregar',1);
	fn_btn('cancelar',1);
	fn_cargarRestaurante();
	$("#botonesTodos").hide();
	
});

function fn_OpcionSeleccionada(ls_opcion)
{
		if (ls_opcion == 'Todos') 
		{
			fn_cargarImpresoras();
		}else if(ls_opcion == 'Activos'){
			accion = 60;
			//alert(resultado)
			rest=$("#txt_idrestaurante").val()
			fn_cargarImpresorasInactivos(rest,accion);
			
		}else if(ls_opcion == 'Inactivos'){
			accion = 61;
			//alert(resultado)
			rest=$("#txt_idrestaurante").val()
			fn_cargarImpresorasInactivos(rest,accion);
		}
}

function fn_OpcionSeleccionadaModInsert()
{
		
		var ls_opcion = '';
		ls_opcion = $(":input[name=estados]:checked").val();
		
		if (ls_opcion == 'Todos') 
		{
			fn_cargarImpresoras();
		}else if(ls_opcion == 'Activos'){
			resultado = 60;
			rest=$("#txt_idrestaurante").val()
			fn_cargarImpresorasInactivos(rest,resultado);
			
		}else if(ls_opcion == 'Inactivos'){
			resultado = 61;
			rest=$("#txt_idrestaurante").val()
			fn_cargarImpresorasInactivos(rest,resultado);
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
/*FUNCION PARA CARGAR AL INICIO LOS RESTAURANTES X CADENA   */
/*==========================================================*/
function fn_cargarRestaurante()
{
	send={"cargarrestaurante":1};
	$.ajax({async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"../adminimpresora/config_adminimpresora.php",data:send,
		success:function(datos)
		{
			if(datos.str==0){
				//$("#divrestaurante").hide();
				alertify.error("No existen datos para esta Cadena");}
			else if(datos.str>0)
			{
				//$("#divrestaurante").show();
				$("#selrest").html("");
				$('#selrest').html("<option selected value='0'>--------------Seleccione Restaurante--------------</option>");
				for(i=0; i<datos.str; i++) 
				{
					html="<option value='"+datos[i]['rst_id']+"'>"+datos[i]['Descripcion']+"</option>";
    				$("#selrest").append(html);										
				}
				
				$("#selrest").chosen();
				$("#selrest").change(function(){
									
				lc_rest=$("#selrest").val();
				$("#txt_idrestaurante").val(lc_rest);
				$("#botonesTodos").show();
				rest=$("#txt_idrestaurante").val();
				//fn_cargarImpresoras(rest);
				fn_cargarImpresorasInactivos(rest,60);
				
											});
			}
		}
	});	
}


/*==========================================================*/
/*FUNCION PARA CARGAR AL INICIO TABLA DE IMPRESORAS         */
/*==========================================================*/
function fn_cargarImpresoras(rest){
	
 	accion = 1;
 	var html = "<thead><tr class='active'><th style='text-align:center'>Nombre Impresora</th><th style='text-align:center'>Descripcion Impresora</th><th style='text-align:center;'>Tipo Impresora</th><th style='text-align:center'>Estaci&oacute;n Conectada</th><th style='text-align:center'>Activo</th></thead>";
 	send = {"administracionImpresora": 1};
 	send.accion = accion;
	send.restaurante = $("#txt_idrestaurante").val();
 	$.getJSON("../adminimpresora/config_adminimpresora.php", send, function(datos){
  	if(datos.str > 0)
  	{
		$('#impresora').show();
    	for(i=0;i<datos.str;i++)
    		{
     			html = html + "<tr id='"+i+"' onclick='fn_seleccion("+i+",\""+datos[i]['imp_id']+"\");' ondblclick='fn_selecciondoble("+i+",\""+datos[i]['imp_id']+"\")'><td>"+datos[i]['imp_nombre']+"</td><td>"+datos[i]['imp_descripcion']+"</td><td width='28%'>"+datos[i]['timp_descripcion']+"</td><td>"+datos[i]['est_nombre']+"</td>";     
	 			if(datos[i]['std_id']==60){
				html+="<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";				
				}
				if(datos[i]['std_id']==61){
				html+="<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";	
				}
    }       
    $('#tabla_impresora').html(html);
    $('#tabla_impresora').dataTable(
									  	{	//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPAÑOL
											'destroy': true   
										}
									 );
	$("#tabla_impresora_length").hide();
	$("#tabla_impresora_paginate").addClass('col-xs-10');
	$("#tabla_impresora_info").addClass('col-xs-12');
	$("#tabla_impresora_length").addClass('col-xs-6');
	
	//col-sm-9
	
  }else{
			alertify.error("No existen datos para esta cadena.");
			$("#botonesTodos").hide();
			$('#impresora').hide();
			//html+="<tr>";
			//html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
			$("#tabla_impresora").html(html);
			}
	}); 
}

/*==================================================================================================*/
/*FUNCION PARA CARGAR TABLA CANALES DE IMPRESION ACTIVOS O INACTIVOS DE ACUERDO A LA ACCION 60 Y 61 */
/*==================================================================================================*/
function fn_cargarImpresorasInactivos(rest,accion){
	
 	var html = "<thead><tr class='active'><th style='text-align:center'>Nombre Impresora</th><th style='text-align:center'>Descripcion Impresora</th><th style='text-align:center;'>Tipo Impresora</th><th style='text-align:center'>Estaci&oacute;n Conectada</th><th style='text-align:center'>Activo</th></thead>";
 	send = {"administracionImpresora": 1};
 	send.accion = accion;
	send.restaurante = rest;
 	$.getJSON("../adminimpresora/config_adminimpresora.php", send, function(datos){
  	if(datos.str > 0)
  	{
		$('#impresora').show();
    	for(i=0;i<datos.str;i++)
    		{
     			html = html + "<tr id='"+i+"' onclick='fn_seleccion("+i+",\""+datos[i]['imp_id']+"\");' ondblclick='fn_selecciondoble("+i+",\""+datos[i]['imp_id']+"\")'><td>"+datos[i]['imp_nombre']+"</td><td>"+datos[i]['imp_descripcion']+"</td><td width='28%'>"+datos[i]['timp_descripcion']+"</td><td>"+datos[i]['est_nombre']+"</td>";     
	 			if(datos[i]['std_id']==60){
				html+="<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";				
				}
				if(datos[i]['std_id']==61){
				html+="<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";	
				}
    }       
    $('#tabla_impresora').html(html);
    $('#tabla_impresora').dataTable(
									  	{	//PROPIEDADES DE LA DATABLE CAMBIAMOS EL IDIOMA A ESPAÑOL
											'destroy': true   
										}
									 );
	$("#tabla_impresora_length").hide();
	$("#tabla_impresora_paginate").addClass('col-xs-10');
	$("#tabla_impresora_info").addClass('col-xs-12');
	$("#tabla_impresora_length").addClass('col-xs-6');
	
	//col-sm-9
	
  }else{
			alertify.error("No existen datos para esta cadena.");
			//$("#botonesTodos").hide();
			//$('#impresora').hide();
			//html+="<tr>";
			//html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
			$("#tabla_impresora").html(html);
			}
	});  
}

function fn_seleccion(fila, codigo){

		Cod_Impresora = codigo;
		$("#tabla_impresora tr").removeClass("success");
		$("#"+fila+"").addClass("success");
		$("#txt_idimpresora").val(Cod_Impresora);
}

function fn_selecciondoble(fila, codigo){

		lc_control = 3;
		Cod_Impresora = codigo;
		$("#txt_idimpresora").val(Cod_Impresora);
		$("#tabla_impresora tr").removeClass("success");
		$("#"+fila+"").addClass("success");
		fn_cargarImpresoraModifica(codigo)
		$('#ModalMod').modal('show');
		fn_traerNombreRestaurante();
}

/*====================================================================*/
/*FUNCION PARA ACCIONAR SI ES NUEVO O MODIFICAR Y LLAMAR A LA MODAL   */
/*====================================================================*/
function fn_accionar(accion)
{
	
	if(accion=='Nuevo')
	{	
		$('#txt_descripcion').val('');
		$('#txt_nombre').val('');
		//$('#txt_puertoimpresora').val('');
		$('#txt_tipoimpresora').val('');
		
		lc_control = 2;
		if($("#selrest").val()==0){
			alertify.error("Selleccione un restaurante.");
			$("#selrest").focus();
			return false;
			} 
		fn_cargarTipoImpresora();
		fn_traerEstaciones();
		fn_traerNombreRestaurante();
		
		$('#ModalNuevo').modal('show');
		////////////Colocar foco en cualquier campo de la modal////////////////// CP
        $('#ModalNuevo').on('shown.bs.modal', function () { 
			$("#txt_nombre").focus();					
		});
	}
	else if(accion=='Grabar'){
			if(lc_control==2){
										
					if($("#txt_descripcion").val()==''){
						alertify.error("Ingrese Nombre de Configuracion de Impresora.");
						return false;
						}
					if($("#txt_descripcion").val()==''){
						alertify.error("Ingrese Descripcion de Configuracion de Impresora.");
						return false;
						}
					if($("#sel_tipoimpresora").val()=='0'){
						alertify.error("Seleccione Tipo de Impresora.");
						return false;
						}
					if($("#txt_puertoimpresora").val()==''){
						alertify.error("Ingrese Puerto de Impresora.");
						return false;
						}
					if($("#sel_estacion").val()=='0'){
						alertify.error("Seleccione Estacion.");
						return false;
						}
						fn_guardarConfiguracionImpresora();
						///fn_OpcionSeleccionadaModInsert();
					
						
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
					if($("#sel_estacionMod").val()=='0'){
						alertify.error("Seleccione Estacion.");
						return false;
						}
					fn_guardarImpresoraModifica();
					//fn_OpcionSeleccionadaModInsert();
									
						
					}
		
		}
}

/*======================================*/
/*FUNCION PARA CARGAR TIPO DE IMPRESORA */
/*======================================*/
function fn_cargarTipoImpresora(){
	
	accion = 5;
	send={"cargartipoimpresora":1};
 	send.accion = accion;
 	$.getJSON("../adminimpresora/config_adminimpresora.php", send, function(datos){
  		if(datos.str > 0)
  		{ 		
				$("#sel_tipoimpresora").html("");
				$('#sel_tipoimpresora').html("<option selected value='0'>--SELECCIONE TIPO IMPRESORA--</option>");
				
				for(i=0; i<datos.str; i++) 
				{
					html="<option value='"+datos[i]['timp_id_todos']+"'>"+datos[i]['timp_descripcion_todos']+"</option>";
    				$("#sel_tipoimpresora").append(html);										
				}
				$("#sel_tipoimpresora").change(function(){
				lc_tipoimpresora=$("#tipoimpresora").val();
				$("#txt_idtipoimpresora").val(lc_tipoimpresora);
											});
			}
	});		
}

/*=====================================================================*/
/*FUNCION PARA GUARDAR UN NUEVO REGISTRO DE CONFIGURACION DE IMPRESORA */
/*=====================================================================*/
function fn_guardarConfiguracionImpresora(){
	
		accion = 2;
		send = {"nuevaConfiguracionImpresora": 1};
		send.accion = accion;
		send.restaurante = $("#txt_idrestaurante").val();
		send.nombre = $("#txt_nombre").val();
		send.descripcion = $("#txt_descripcion").val();
		send.tipoimpresora=$("#sel_tipoimpresora").val();
		send.puertoimpresora=$("#txt_puertoimpresora").val();
		send.estacion=$("#sel_estacion").val();
		
		if($("#option").is(':checked'))
			{send.estado=60;}
			else
			{send.estado=61;}
		$.getJSON("../adminimpresora/config_adminimpresora.php", send, function(datos) {
				alertify.success("Configuracion de Impresora agregada correctamente.");	
				fn_OpcionSeleccionadaModInsert();
		});
		$('#ModalNuevo').modal('hide');
		$("#botonesTodos").show();
		
}

/*==================================================================*/
/*FUNCION PARA TRAER DATOS CUANDO MODIFICAMOS UN REGISTRO           */
/*==================================================================*/
function fn_cargarImpresoraModifica(codigo){
	//$('#titulomodalMod').html("Modificar Configuracion de Impresora: ");
	accion = 3;
	send = {"cargarImpresoraMod": 1};
	send.imp_id = codigo;
	send.accion = accion;
	$.getJSON("../adminimpresora/config_adminimpresora.php", send, function(datos) {
		if(datos.str>0){
			$('#titulomodalMod').html(" "+datos[0]['imp_nombre']+" ");
			$('#txt_descripcionMod').val(datos[0]['imp_descripcion']);
			$('#txt_nombreMod').val(datos[0]['imp_nombre']);
			
			//$('#txt_puertoimpresoraMod').val(datos[0]['imp_puerto']);
			
			$("#optionmod").empty();
			if(datos[0]['std_id']==60){
			$("#optionmod").prop("checked", true);  // para poner la marca
			}
			if(datos[0]['std_id']==61){
			$("#optionmod").prop("checked", false);
			}
			
			tipoimpresora=(datos[0]['timp_id']);
			//alert(tipoimpresora);
			estacion=(datos[0]['est_id']);			 
			accion = 5;
			send={"cargartipoimpresora":1};
			send.accion = accion;
			$.getJSON("../adminimpresora/config_adminimpresora.php", send, function(datos){
				if(datos.str > 0)
				{ 		
				$("#sel_tipoimpresoraMod").empty();
				//$('#sel_tipoimpresoraMod').html("<option selected value='0'>--SELECCIONE TIPO IMPRESORA--</option>");
				
				for(i=0; i<datos.str; i++) 
				{
					html="<option value='"+datos[i]['timp_id_todos']+"'>"+datos[i]['timp_descripcion_todos']+"</option>";
    				$("#sel_tipoimpresoraMod").append(html);										
				}
				
				$("#sel_tipoimpresoraMod").val(tipoimpresora);
				$("#sel_tipoimpresoraMod").change(function(){
				lc_tipoimpresora=$("#sel_tipoimpresoraMod").val();
				$("#txt_idtipoimpresora").val(lc_tipoimpresora);
											});
				}
			});	
			
			accion = 6;
			send = {'traerEstaciones':1}
			send.accion = accion;
			send.idrestauante = $("#selrest").val();
			$.getJSON("../adminimpresora/config_adminimpresora.php", send, function(datos) {
				if(datos.str > 0)
					{ 		
						$("#sel_estacionMod").empty();
						//$('#sel_estacion').html("<option selected value='0'>&nbsp;&nbsp;&nbsp;&nbsp;--SELECCIONE ESTACION--</option>");
						for(i=0; i<datos.str; i++) 
						{
							html="<option value='"+datos[i]['est_id']+"'>"+datos[i]['est_nombre']+"</option>";
							$("#sel_estacionMod").append(html);										
						}
						
						$("#sel_estacionMod").val(estacion);
						/*$("#sel_estacion").val(tipoimpresora);
						$("#sel_estacion").change(function(){
						lc_tipoimpresora=$("#sel_tipoimpresoraMod").val();
						$("#txt_idtipoimpresora").val(lc_tipoimpresora);
													});*/
					}			
	});
	
			 
		}
	});
}

/*=============================================================================================*/
/*FUNCION QUE LLAMA A LA FUNCION DE TRAER DATOS PARA MODIFICAR                                 */
/*=============================================================================================*/
function fn_guardarImpresoraModifica(){

		accion = 4;
		//alert($("#txt_idcanalesimpresion").val());
		send = {"guardaImpresoraMod": 1};
		send.accion = accion;
		send.restaurante = $("#txt_idrestaurante").val();
		send.imp_id = $("#txt_idimpresora").val();
		send.timp_id = $("#sel_tipoimpresoraMod").val();
		send.imp_nombre = $('#txt_nombreMod').val();
		send.imp_descripcion = $('#txt_descripcionMod').val();
		send.imp_puerto = $('#txt_puertoimpresoraMod').val();
		send.estacion = $("#sel_estacionMod").val();
		
		if($("#optionmod").is(':checked'))
			{send.estado=60;}
			else
			{send.estado=61;}
		$.getJSON("../adminimpresora/config_adminimpresora.php", send, function(datos) {
				alertify.success("Impresora actualizada correctamente.");	
				fn_OpcionSeleccionadaModInsert();
		});
		$('#ModalMod').modal('hide');
		
}

function fn_traerNombreRestaurante(){
	
	send = {'nombrerestaurante':1}
	send.idrestauante = $("#selrest").val();
	$.getJSON("../adminimpresora/config_adminimpresora.php", send, function(datos) {
		if(datos.str > 0)
			{ 		
			for(i=0; i<datos.str; i++) 
				{	
					$("#txt_restMod").val(datos[i]['descripcion']);
					$("#txt_rest").val(datos[i]['descripcion']);
					
				}
			}			
	});
}

function fn_traerEstaciones(){
	
	accion = 6;
	send = {'traerEstaciones':1}
	send.accion = accion;
	send.idrestauante = $("#selrest").val();
	$.getJSON("../adminimpresora/config_adminimpresora.php", send, function(datos) {
		if(datos.str > 0)
			{ 		
				$("#sel_estacion").empty();
				$('#sel_estacion').html("<option selected value='0'>&nbsp;&nbsp;&nbsp;&nbsp;--SELECCIONE ESTACION--</option>");
				for(i=0; i<datos.str; i++) 
				{
					html="<option value='"+datos[i]['est_id']+"'>"+datos[i]['est_nombre']+"</option>";
    				$("#sel_estacion").append(html);										
				}
				
				/*$("#sel_estacion").val(tipoimpresora);
				$("#sel_estacion").change(function(){
				lc_tipoimpresora=$("#sel_tipoimpresoraMod").val();
				$("#txt_idtipoimpresora").val(lc_tipoimpresora);
											});*/
			}			
	});
}