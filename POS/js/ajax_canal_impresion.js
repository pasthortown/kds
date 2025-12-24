lc_rest=-1;
lc_control=-1;//para saber si hay q grabar o actualizar
lc_imp=-1;
var Accion = 0;

$(document).ready(function(){
						   
	var lc_cadena=0;
	$("#detalle_impresoras").hide();
	$("#imp_modificado").show();	
	$("#imp_nuevo").hide();
	$("#inicio_Modal").hide();
	$("#imp_nuevo").hide();
	//$("#detalle_imp").hide();	
	fn_cargaModulo();
	//$("#modificarUsuario").hide();	
	//$("#modal_tarjeta").hide();
	//$("#modal_tarjetaModificada").hide();
	lc_cadena=$("#cadenas").val();
	//$("#tex_busqueda").focus();
	//fn_paginado(-1);
	fn_cargaRestaurante(lc_cadena);
	
	 //$("#tex_busqueda").keyup(function(event){
		//fn_paginado(-1);
     //});
	 fn_btn('modificar',0);
	fn_btn('guardar',0);
	fn_btn('cancelar',0);
	fn_btn('eliminar',0);
	fn_btn('agregar',1);
});

function fn_menu(lc_accion)
 {	//alert(lc_accion);
	$("#respuestaMenu").load("menu_canal_impresion.php?accion="+lc_accion+"");	 
 }
 
 
 function fn_cargaRestaurante(lc_cadena)
{	
	$('#selRestaurante').empty();
	$("#sel_resnuevo").empty();
	send={"cargaRestaurante":1};
	send.cadena=lc_cadena;
	$.getJSON
	("config_canal_impresion.php",send,function(datos) 
		{
			if(datos.str>0)
			{
				$('#selRestaurante').html("<option selected  align='center' value='0'>----------------Seleccione Restaurante----------------</option>");
				for(i=0; i<datos.str; i++) 
				{
					html="<option value='"+datos[i]['rst_id']+"'>"+datos[i]['Descripcion']+"</option>";
					$("#selRestaurante").append(html);								
				};	
				
				$('#sel_resnuevo').html("<option align='center' value='0'>----Seleccione Restaurante----</option>");
				for(i=0; i<datos.str; i++) 
				{
					html="<option value='"+datos[i]['rst_id']+"'>"+datos[i]['Descripcion']+"</option>";
					$("#sel_resnuevo").append(html);								
				}
				
				
				$("#selRestaurante").change(function(){
				lc_rest=$("#selRestaurante").val();
				$("#hid_rest").val(lc_rest);
				//alert($("#selCadena").val())
				fn_cargarDetalleImpresoras(lc_rest);
				fn_btn('cancelar',1);
												});
			}	
		}		  
	);	
}

function fn_cargarDetalleImpresoras(lc_rest)
{
	var html = "<tr><td >NOMBRE IMPRESORA</td><td>M&Oacute;DULO</td></tr>";
	send={"cargarDetalleImpresoras":1};
	send.restau=lc_rest;
	fn_btn('agregar',1);
	$.getJSON
	("config_canal_impresion.php",send,function(datos)
		{
			if(datos.str>0)
			{
				$("#detalle_impresoras").show();
				$("#detalle_imp").empty();
				for(i=0;i<datos.str;i++)
				{
				html+="<tr id='"+i+"' style='cursor:pointer;'";
					//html+=" onclick='fn_seleccion("+'"'+datos[i]['imp_id']+'"'+","+i+")'>";
					html+=" onclick='fn_seleccion("+i+","+datos[i]['imp_id']+")'>";
					html+="<td align='center'  style='width:190px;'>"+datos[i]['imp_nombre']+"&nbsp;</td>";
					html+="<td align='center'  style='width:160px;'>"+datos[i]['mdl_descripcion']+"&nbsp;</td></tr>";
					$("#detalle_imp").html(html);
			    }
			}
			
			else{
			$("#detalle_impresoras").html("<table id='detalle_imp'  width='400' align='center'>");
			$("#detalle_imp").html("<tr><td >NOMBRE IMPRESORA</td><td>M&Oacute;DULO</td></tr>");
			$("#detalle_imp").append("<tr><td colspan='2'>No existen registros.</td></tr>");
		}
		}
	);												
}

function fn_seleccion(fila,imp_id)
{		
	//alert(fila);
	$("#detalle_imp tr").removeClass("seleccionado");
	$("#"+fila+"").addClass("seleccionado");
	lc_imp=imp_id;	
	$("#hid_imp").val(imp_id);	
	fn_modalito();
	fn_cargarImpresoraModificada(imp_id);
	//$('#txt_impNdescripcion').val("");
}

function fn_modalito()
{
		fn_btn('cancelar',0);
		$("#inicio_Modal").show();
		$("#inicio_Modal").dialog(
		{
		modal: true,		
		width: 600,
		heigth: 500,			
		resize: false,
		opacity: 0,
		show: "explode",
		hide:"explode",
		duration: 5000,
		position:"center",
		buttons: {	
				"Eliminar": function() {
				lc_imp=-1
				$(this).dialog( "destroy" );
				$("#inicio_Modal").hide();
				fn_accionar('Eliminar');
				fn_btn('cancelar',1);
									   },
				"Aceptar": function() {
				lc_control=1;
				$(this).dialog( "destroy" );
				$("#inicio_Modal").hide();
				fn_accionar('Grabar');	
				fn_btn('cancelar',1);
									  },
				"Cancelar": function(){
				fn_btn('cancelar',1);
				$(this).dialog( "destroy" );
				$("#inicio_Modal").hide();
									  }
									  
				}		
		});	
}

function fn_modalitoagregar()
{	
		fn_btn('cancelar',0);
		$("#imp_nuevo").show();
		$("#imp_nuevo").dialog
		({
		modal: true,		
		width: 600,
		heigth: 500,			
		resize: false,
		opacity: 0,
		show: "explode",
		hide:"explode",
		duration: 5000,
		position:"center",
		buttons: {				
				"Aceptar": function() {
				lc_control=0;
				$(this).dialog( "destroy" );
				$("#imp_nuevo").hide();
				fn_accionar('Grabar');
				fn_btn('cancelar',1);
									  },
				"Cancelar": function(){
				fn_btn('cancelar',1);
				$(this).dialog( "destroy" );
				$("#imp_nuevo").hide();
									  }
				}		
		});	
}

function fn_accionar(accion)
{
	
	if(accion=='Nuevo')
	{			
		if($("#selRestaurante").val()==0)
		{
			alertify.alert("Debe seleccionar un Restaurante")
			return false;
		}
		$("#imp_nuevo").show();
		$("#detalle_impresoras").show();		
		lc_control=0;
		fn_modalitoagregar();
		fn_cargaModulo();
		$('#txt_impNdescripcion').val("");
		//fn_menu("Nuevo");
	}//
	else if(accion=='Grabar')	{		
		//alert('llega');
		if(lc_control==1)
		{	
			if($("#txt_impMdescripcion").val()=='')
			{
				alertify.alert("Debe Ingresar Nombre de Impresora.");
				fn_modalito();
				return false;
			}
			//<input type="hidden" id="hid_rest" />
			//<input type="hidden" id="hid_imp" />
			send={"actualizaImpresora":1};			
			send.lc_imp=$("#hid_imp").val();
			//send.lc_rst=$("#hid_rest").val();
			send.lc_mdl=$("#sel_modulo").val();
			send.lc_impNombre=$("#txt_impMdescripcion").val();
			$.getJSON("config_canal_impresion.php",send,function(datos) 
			{
				lc_control=0;//modificado
				alertify.alert("Impresora Actualizada Exitosamente");
				$("#alertify-ok").click(
						function()
						{						 
							 //window.location.reload();
							fn_cargarDetalleImpresoras(lc_rest);
						}
						)					
			});
					
		}
		if(lc_control==0)
		{				
			if($("#txt_impNdescripcion").val()=='')
			{
				alertify.alert("Debe Ingresar Nombre de Impresora.");
				fn_modalitoagregar();
				return false;
			}
			if($("#sel_modulonuevo").val()==0)
			{
				alertify.alert("Debe Seleccionar un Modulo.");
				fn_modalitoagregar();
				return false;
			}
			send={"grabaImpresora":1};			
			send.lc_rst=$("#hid_rest").val();
			send.lc_mdlM=$("#sel_modulonuevo").val();
			send.lc_impNombreM=$("#txt_impNdescripcion").val();
			$.getJSON("config_canal_impresion.php",send,function(datos) 
			{
				//lc_control=0;//modificado
				alertify.alert("Impresora creada Exitosamente");
				$("#alertify-ok").click(
						function()
						{
							 //window.location.reload();
							fn_cargarDetalleImpresoras(lc_rest);;
						}
						)					
			});
		}
	
	}else if(accion=='Cancelar'){
		
		window.location.reload();    		
		fn_menu('Cancelar');
		//fn_paginado(-1);
	}
	
	
	else if(accion=='Modificar')
	{	
		if(lc_imp==-1)
		{
			alertify.alert("Seleccione la impresora que desea modificar.")
			return false;
		}
		else
		{
		lc_control=1;
		$("#detalle_impresoras").hide();
		rst=$("#hid_rest").val();
		fn_cargarImpresoraModificada(rst);			
		}
	}
	else if(accion=='Eliminar')
	{	
		if(lc_imp==-1){
		/*{
			alertify.alert("Seleccione la impresora que desea eliminar.")
			return false;
		}
		else
		{	*/	
		//$("#detalle_impresoras").hide();
		rst=$("#hid_rest").val();	
		send={"eliminaImpresora":1};			
		send.lc_Eimp=$("#hid_imp").val();
			$.getJSON("config_canal_impresion.php",send,function(datos) 
			{				
				alertify.alert("Impresora Eliminada");
				$("#alertify-ok").click(
						function()
						{						 
						 	//window.location.reload();
							fn_cargarDetalleImpresoras(lc_rest);
						}
						)					
			});
		}
	}
}


function fn_cargarImpresoraModificada(rst)
{	
	send={"cargarImpresoraModificada":1};
	send.impId=$("#hid_imp").val();
	$.getJSON
	("config_canal_impresion.php",send,function(datos)
		{
			if(datos.str>0)
			{
				$("#imp_modificado").show();
				$("#txt_impMdescripcion").val(datos.imp_nombre);
				$("#sel_modulo").val(datos.mdl_id);				
			}
			fn_menu("Modificar");
		}
	);	
}

function fn_cargaModulo()
{
	send={"cargarmodulo":1};	
	$.getJSON
	("config_canal_impresion.php",send,function(datos)
		{
			if(datos.str>0)
			{
				$("#sel_modulo").empty();
				
				$('#sel_modulo').html("<option value='0'>----Seleccione Modulo----</option>");
				for(i=0; i<datos.str; i++) 
				{
					html="<option value='"+datos[i]['mdl_id']+"'>"+datos[i]['mdl_descripcion']+"</option>";
					$("#sel_modulo").append(html);								
				}
				$('#sel_modulonuevo').html("<option value='0'>----Seleccione Modulo----</option>");
				for(i=0; i<datos.str; i++) 
				{
					html="<option value='"+datos[i]['mdl_id']+"'>"+datos[i]['mdl_descripcion']+"</option>";
					$("#sel_modulonuevo").append(html);								
				}
				
			}
		}
	);	
}

function fn_btn(boton,estado){
	if(estado){
		$("#btn_"+boton).css("background","#1fa0e4 url('../../imagenes/admin_resources/"+boton+".png') 14px 4px no-repeat");
		$("#btn_"+boton).removeAttr("disabled");
		$("#btn_"+boton).addClass("botonhabilitado");
		$("#btn_"+boton).removeClass("botonbloqueado");
	}else{
		$("#btn_"+boton).css("background","#1fa0e4 url('../../imagenes/admin_resources/"+boton+"_bloqueado.png') 14px 4px no-repeat");
		$("#btn_"+boton).prop('disabled', true);
		$("#btn_"+boton).addClass("botonbloqueado");
		$("#btn_"+boton).removeClass("botonhabilitado");
	}
}

function fn_agregar()
{
	fn_accionar("Nuevo");		
}

function fn_cancelar()
{
	window.location.reload();
}





