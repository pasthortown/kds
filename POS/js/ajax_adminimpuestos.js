/////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO////////////////////////////////////////
///////////DESCRIPCION: AJAX IMPUESTOS, VISUALIZAR, CREAR Y MODIFICAR IMPUESTOS//
////////////////TABLAS: impuestos ///////////////////////////////////////////////
////////FECHA CREACION: 10/03/2016///////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

var pnt_Id = 0;
var accion = 0;
//var bandera = 0; //bandera para saber si es nuevo � modificar
$(document).ready(function(){
						   
	fn_btn('agregar',1);
	fn_btn('cancelar',1);	
	fn_cargarImpuestos('Activo');
});

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
/*FUNCION PARA CARGAR TABLA IMPUESTOS                       */
/*==========================================================*/
function fn_cargarImpuestos(estado){
	
 	accion = 1;
 	var html = "<thead><tr class='active'><th style='text-align:center'>Pa&iacute;s</th><th style='text-align:center'>Descripci&oacute;n</th><th style='text-align:center'>Porcentaje</th><th style='text-align:center'>FE C&oacute;digo</th><th style='text-align:center'>FE C&oacute;digo Porcentaje</th><th style='text-align:center'>Activo</th></thead>";
 	send = {"cargarImpuestos": 1};
 	send.accion = accion;
	send.estado = estado;
 	$.getJSON("../adminImpuestos/config_impuestos.php", send, function(datos){
  	if(datos.str > 0)
  	{ 
    	for(i=0;i<datos.str;i++)
    		{
     			html = html + "<tr id='"+i+"' onclick='fn_seleccion("+i+",\""+datos[i]['IDImpuestos']+"\");' ondblclick='fn_selecciondoble("+i+",\""+datos[i]['IDImpuestos']+"\")'><td>"+datos[i]['pais_descripcion']+"</td>";     
				html+="<td style='text-align: center;'>"+datos[i]['imp_descripcion']+"</td>";				
				html+="<td style='text-align: center;'>"+datos[i]['imp_porcentaje']+" %</td>";	
				html+="<td style='text-align: center;'>"+datos[i]['fe_codigo']+"</td>";
				html+="<td style='text-align: center;'>"+datos[i]['fe_codigoPorcentaje']+"</td>";
				if(datos[i]['estado']=='Activo'){
				html+="<td style='text-align: center;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";	
				}
				if(datos[i]['estado']=='Inactivo'){
				html+="<td style='text-align: center;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";	
				}
				
    }       
    $('#tabla_impuestos').html(html);
    $('#tabla_impuestos').dataTable(
									  	{	
											'destroy': true
										}
									 );
	$("#tabla_impuestos_length").hide();
	$("#tabla_impuestos_paginate").addClass('col-xs-10');
	$("#tabla_impuestos_info").addClass('col-xs-10');
	$("#tabla_impuestos_length").addClass('col-xs-6');
	
	//col-sm-9
	
  }else{
			alertify.error("No existen datos para esta cadena.");
			//html+="<tr>";
			//html+="<td colspan='6'>No existen datos para esta cadena.</td></tr>";
			$("#tabla_impuestos").html(html);
			}
	}); 
}

function fn_seleccion(fila, codigo){

		IDImpuestos = codigo;
		$("#tabla_impuestos tr").removeClass("success");
		$("#"+fila+"").addClass("success");
		$("#txt_IDImpuestos").val(IDImpuestos);
}
function fn_selecciondoble(fila, codigo){

		lc_control = 3;
		IDImpuestos = codigo;
		$("#txt_IDImpuestos").val(IDImpuestos);
		$("#tabla_impuestos tr").removeClass("success");
		$("#"+fila+"").addClass("success");
		fn_cargarImpuestosModifica(codigo)
		$('#ModalMod').modal('show');
}

/*====================================================================*/
/*FUNCION PARA ACCIONAR SI ES NUEVO O MODIFICAR Y LLAMAR A LA MODAL   */
/*====================================================================*/
function fn_accionar(accion)
{
	
	if(accion=='Nuevo')
	{	
		
		send = {"numeroMaximoImpuestos": 1};
		send.accion = 3;
		$.getJSON("../adminImpuestos/config_impuestos.php", send, function(datos) {
			if(datos.str > 0)
  			{ 
    			for(i=0;i<datos.str;i++)
    			{
					if(datos[i]['numImpuestos'] == 1){
						alertify.error("No puede ingresar mas de 5 impuestos.");
						return false;
					}
					else{
						//bandera = 1; //nuevo registro
						//fn_cargarPais(bandera,0);
						$("#txt_descripcion").val('');
						$("#txt_porcentaje").val('');
						$("#txt_feCodigo").val('');
						$("#txt_feCodigoPorcentaje").val('');
						lc_control = 2;
						
						$('#ModalNuevo').modal('show');
						////////////Colocar foco en cualquier campo de la modal////////////////// CP
						$('#ModalNuevo').on('shown.bs.modal', function () { 
							$("#txt_descripcion").focus();					
						});
					}
				}
			}
		});
				
	}
	else if(accion=='Grabar')
        {			
			if(lc_control==2)
                        {
				
				if($("#txt_descripcion").val()==''){
					alertify.error("Ingrese descripci&oacute;n del Impuesto.");
					return false;
				}
				if($("#txt_porcentaje").val()==''){
					alertify.error("Ingrese porcentaje del Impuesto.");
					return false;
				}
				if($("#txt_feCodigo").val()==''){
					alertify.error("Ingrese C&oacute;digo Facturaci&oacute;n Electr&oacute;nica.");
					return false;
				}
				if($("#txt_feCodigoPorcentaje").val()==''){
					alertify.error("Ingrese C&oacute;digo de porcentaje Facturaci&oacute;n Electr&oacute;nica.");
					return false;
				}
                                if($("#txt_ordenImpuesto").val()=="")
                                {
					alertify.error("Ingrese el orden del impuesto.");
					return false;
				}
				
				fn_guardarImpuesto();	
			}
			else if(lc_control==3){
				 	if($("#txt_descripcionMod").val()==''){
					alertify.error("Ingrese descripci&oacute;n del Impuesto.");
					return false;
					}
					if($("#txt_porcentajeMod").val()=='')
                                        {
					alertify.error("Ingrese porcentaje del Impuesto.");
					return false;
					}
                                        if($("#txt_ordenImpM").val()=="")
                                        {
					alertify.error("Ingrese orden del Impuesto.");
					return false;
					}
					fn_guardarImpuestosModifica();
			}
		
		}
}

/*==================================================================*/
/*FUNCION PARA GUARDAR UN NUEVO REGISTRO EN IMPUESTOS               */
/*==================================================================*/
function fn_guardarImpuesto(){
		accion = 'I';
		send = {"guardarImpuesto": 1};
		send.accion = accion;
		send.descripcion = $("#txt_descripcion").val();
		send.porcentaje = $("#txt_porcentaje").val();
		send.feCodigo = $("#txt_feCodigo").val();
		send.feCodigoPorcentaje = $("#txt_feCodigoPorcentaje").val();               
		if($("#option").is(':checked'))
		{send.estado='Activo';}
		else
		{send.estado='Inactivo';}
                send.ordenImpN=$("#txt_ordenImpuesto").val();    
		$.getJSON("../adminImpuestos/config_impuestos.php", send, function(datos) 
                {
                                $("#txt_ordenImpuesto").val("");
				alertify.success("Impuesto agregado correctamente.");	
				
				var ls_opcion = '';
				ls_opcion = $(":input[name=estados]:checked").val();
				if (ls_opcion == 'Activos')
				{
					fn_cargarImpuestos('Activo');
				}
				else
				if(ls_opcion == 'Inactivos')
				{
					fn_cargarImpuestos('Inactivo');
				}
				else
				if(ls_opcion == 'Todos')
				{
					fn_cargarImpuestos('Todos');
				}
		});
		$('#ModalNuevo').modal('hide');
		
}

/*==================================================================*/
/*FUNCION PARA TRAER DATOS CUANDO MODIFICAMOS UN REGISTRO           */
/*==================================================================*/
function fn_cargarImpuestosModifica(codigo){
	accion = 2;
	send = {"cargarImpuestosMod": 1};
	send.IDImpuestos = codigo;
	send.accion = accion;
	$.getJSON("../adminImpuestos/config_impuestos.php", send, function(datos) {
		if(datos.str>0){
			$('#titulomodalMod').html(" "+datos[0]['imp_descripcion']+" ");
			$('#txt_descripcionMod').val(datos[0]['imp_descripcion']);	
			$('#txt_porcentajeMod').val(datos[0]['imp_porcentaje']);	
			$('#txt_feCodigoMod').val(datos[0]['fe_codigo']);
			$('#txt_feCodigoPorcentajeMod').val(datos[0]['fe_codigoPorcentaje']);
			if(datos[0]['estado']=='Activo'){
			$("#optionmod").prop("checked", true);  // para poner la marca
			}
			if(datos[0]['estado']=='Inactivo'){
			$("#optionmod").prop("checked", false);
			}	
		}
	});
}

/*=============================================================================================*/
/*FUNCION QUE LLAMA A LA FUNCION DE TRAER DATOS PARA MODIFICAR Y ACCION=2 CUANDO ES MODIFICAR  */
/*=============================================================================================*/
function fn_guardarImpuestosModifica(){
	accion = 'U';
	send = {"guardarImpuestosMod": 1};
	send.accion = accion;
	send.IDImpuestos = $("#txt_IDImpuestos").val();
	send.descripcion = $("#txt_descripcionMod").val();
	send.porcentaje = $("#txt_porcentajeMod").val();
	send.feCodigo = $("#txt_feCodigoMod").val();
	send.feCodigoPorcentaje = $("#txt_feCodigoPorcentajeMod").val();
	if($("#optionmod").is(':checked'))
	{send.estado='Activo';}
	else
	{send.estado='Inactivo';}
	send.ordenImpM=$("#txt_ordenImpM").val();
	$.getJSON("../adminImpuestos/config_impuestos.php", send, function(datos) 
        {
                        $("#txt_ordenImpM").val("");
			alertify.success("Impuesto actualizado correctamente.");	
			
			var ls_opcion = '';
			ls_opcion = $(":input[name=estados]:checked").val();
			if (ls_opcion == 'Activos')
			{
				fn_cargarImpuestos('Activo');
			}
			else
			if(ls_opcion == 'Inactivos')
			{
				fn_cargarImpuestos('Inactivo');
			}
			else
			if(ls_opcion == 'Todos')
			{
				fn_cargarImpuestos('Todos');
			}
	});
	$('#ModalMod').modal('hide');	
}

function NumCheck(e, field) {
  key = e.keyCode ? e.keyCode : e.which
  // backspace

  if (key == 8) return true
  // 0-9

  if (key > 47 && key < 58) {
    if (field.value == "") return true
    regexp = /.[0-9]{2}$/
    return !(regexp.test(field.value))
  }
  // .

  if (key == 46) {
    if (field.value == "") return false
    regexp = /^[0-9]+$/
    return regexp.test(field.value)
  }
  // other key

  return false
 
}

function fn_cargarPais(bandera,pais){
  
  send={"cargarPais":1};
  send.accion = 4;
  $.getJSON("../adminImpuestos/config_impuestos.php",send,function(datos) {
	  $('#selPais').empty();
	  $('#selPaisMod').empty();
	  
	  if (bandera == 1) //nuevo registro
	  {
		  $('#selPais').append("<option selected value='0'>----------Seleccione Pa&iacute;s----------</option>");
		  for(i=0; i<datos.str; i++) 
		  {
			  html="<option value='"+datos[i]['pais_id']+"'>"+datos[i]['pais_descripcion']+"</option>";
			  $("#selPais").append(html);				
			  //$("#selcadena").append("<option selected value="+datos[i]['cdn_id']+">"+datos[i]['cdn_descripcion']+"</option>");
		  }
		  $("#selPais").change(function(){
			  lc_idpais=$("#selPais").val();
			  $("#idPais").val(lc_idpais);						
		  });
	  }
	  else if(bandera == 2){ //modifica registro
		 
		  for(i=0; i<datos.str; i++) 
		  {
			  html="<option value='"+datos[i]['pais_id']+"'>"+datos[i]['pais_descripcion']+"</option>";
			  $("#selPaisMod").append(html);				
			  //$("#selcadena").append("<option selected value="+datos[i]['cdn_id']+">"+datos[i]['cdn_descripcion']+"</option>");
		  }
		  $("#selPaisMod").val(pais);
		  $("#selPaisMod").change(function(){
			  lc_idpais=$("#selPaisMod").val();
			  $("#idPais").val(lc_idpais);						
		  });
	  }
  });
}

