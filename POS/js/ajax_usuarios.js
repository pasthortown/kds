///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR JOSE FERNANDEZ////////////////////////////////////
///////////DESCRIPCION: AJAX DE CONFIGURACION DE USUARIOS//////////////////
////////FECHA CREACION: 28/08/2014/////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

var lc_control=-1;//para saber si hay q grabar o actualizar
var lc_paginas=-1;
var lc_usuario=-1;
var lc_pass=-1;
var lc_tarjeta=0;

$(document).ready(function(){
	//$("#divrestaurante").hide();
	$("#nuevoUsuario").hide();
	$("#modificarUsuario").hide();	
	$("#modal_tarjeta").hide();
	$("#modal_tarjetaModificada").hide();
	
	$("#tex_busqueda").focus();
	fn_paginado(-1);
	fn_cargarPerfil();
	fn_cargaRestaurante();
	
	 $("#tex_busqueda").keyup(function(event){
		fn_paginado(-1);
     });
});

function fn_menu(lc_accion)
 {	//alert(lc_accion);
	$("#respuestaMenu").load("menu_usuarios.php?accion="+lc_accion+"");	 
 }
 
 function fn_accionar(accion)
{
	if(accion=='Nuevo')
	{		
		lc_control=0;
		fn_menu("Nuevo");
		$("#nuevoUsuario").show();				
		$("#div_usuarios").hide();
		$("#txt_inicialesUsuario").val('');
		
		fn_cargarCodUsuario();
		fn_cargarPerfil();
		fn_cargaRestaurante();
		//fn_cargarRestaurante();
	}
	else if(accion=='Grabar')
	{
		if(lc_control==0)		
		{									
			nombre=$("#txt_nombreUsuario").val();
			iniciales=$("#txt_inicialesUsuario").val();
			usuario=$("#txt_Usuario").val();
			
			if($("#txt_nombreUsuario").val()=='' )
			{
				alertify.alert("Ingrese un nombre de usuaio");
				return false;
			}
			if($("#txt_inicialesUsuario").val()=='' )
			{
				alertify.alert("Ingrese iniciales de usuaio");
				return false;
			}
			if($("#txt_Usuario").val()=='' )
			{
				alertify.alert("Ingrese usuario");
				return false;
			}
			if($("#txt_passUserNuevo").val()=='' )
			{
				alertify.alert("Ingrese password del Usuario.");
				return false;
			}
			
			if($("#selPerfiles").val()==0)
			{
				alertify.alert("Seleccione un perfil");
				return false;
			}
			if($("#selRestaurante").val()==0)
			{				
				alertify.alert("Seleccione un restaurante");
				return false;
			}
			
			send={"validaPassDuplicado":1};
			send.passUsuarioNuevo=$("#txt_passUserNuevo").val();
			send.restauranteusuario=$("#selRestaurante").val();
			$.getJSON("config_usuarios.php",send,function(datos) 
			{					
				if(datos.existe==1)
				{
					alertify.alert("Ya existe un Usuario con el mismo password para este Restaurante.");
					return false;
				}
				else
				{					
					send={"validaTarjetaDuplicado":1};
					send.passTarjetaNuevo=$("#txt_mtarjeta").val();
					send.restauranteTarjeta=$("#selRestaurante").val();
					$.getJSON("config_usuarios.php",send,function(datos) 
					{	
						if(datos.existe==1)
						{
							alertify.alert("Ya existe un Usuario con la misma tarjeta para este Restaurante.");
							return false;
						}
						else
						{
							send={"grabaUsuario":1};
							send.nombreUsuario=nombre;
							send.inicialesUsuario=iniciales;
							send.usuarioUsuario=usuario;
							send.perfilUsuario=$("#selPerfiles").val();
							send.userRestaurante=$("#selRestaurante").val();
							send.passNuevoUser=$("#txt_passUserNuevo").val();
							send.passTarjeta=$("#txt_mtarjeta").val();
							$.getJSON("config_usuarios.php",send,function(datos) 
							{
									alertify.alert("Usuario creado correctamente");
									$("#alertify-ok").click(
										function()
										{					
										$("#nuevoUsuario").empty();
										 window.location.reload();
										}
										);
							}
							);
						}
					}
					);					
				}
			}
			);						
		}
		if(lc_control==1)
		{
			if($("#txt_nombreUsuarioModificado").val()=='' )
			{
				alertify.alert("Ingrese un nombre de usuaio");
				return false;
			}
			if($("#txt_inicialesModificado").val()=='' )
			{
				alertify.alert("Ingrese iniciales de usuaio");
				return false;
			}
			if($("#txt_UsuarioModificado").val()=='' )
			{
				alertify.alert("Ingrese usuario");
				return false;
			}
			if($("#selPerfilesModificado").val()==0)
			{
				alertify.alert("Seleccione un perfil");
				return false;
			}
			if($("#selRestauranteModificado").val()==0)
			{				
				alertify.alert("Seleccione un restaurante");
				return false;
			}
			if(lc_pass==1)
			{
				if($("#txt_PassModificado").val()=='')
				{
				alertify.alert("Ingrese el nuevo password");
				return false;
				}
			}
			
				send={"validaPassDuplicado":1};
				send.passUsuarioNuevo=$("#txt_PassModificado").val();
				send.restauranteusuario=$("#selRestauranteModificado").val();
				$.getJSON("config_usuarios.php",send,function(datos) 
				{					
					if(datos.existe==1)
					{
						alertify.alert("Ya existe un Usuario con el mismo password para este Restaurante.");
						return false;
					}
					else
					{						
						send={"validaTarjetaDuplicado":1};
						send.passTarjetaNuevo=$("#txt_mtarjetaModificada").val();
						send.restauranteTarjeta=$("#selRestauranteModificado").val();
						$.getJSON("config_usuarios.php",send,function(datos) 
						{	
							if(datos.existe==1)
							{
								alertify.alert("Ya existe un Usuario con la misma tarjeta para este Restaurante.");
								return false;
							}
							else
							{
								send={"actualizaUsuario":1};
								send.nombreUsuarioModificado=$("#txt_nombreUsuarioModificado").val();
								send.inicialesUsuarioModificado=$("#txt_inicialesModificado").val();
								send.usuarioUsuarioModificado=$("#txt_UsuarioModificado").val();
								send.perfilUsuarioModificado=$("#selPerfilesModificado").val();
								send.userRestauranteModificado=$("#selRestauranteModificado").val();						
								send.codUsuariomodificado=$("#txt_codUsuarioModificado").val();
								send.checkPass=lc_pass;
								send.checkValor=$("#txt_PassModificado").val();		
								send.passTarjetaModificada=$("#txt_mtarjetaModificada").val();
								$.getJSON("config_usuarios.php",send,function(datos) 
								{
										alertify.alert("Usuario actualizado correctamente");
										$("#txt_Usuario").val('');
										$("#alertify-ok").click(
											function()
											{						
											 window.location.reload();
											}
											);
								}
								);
							}

						});
					}
				});
										
		}
	}
	else if(accion=='Cancelar'){
		
		window.location.reload();    
		$("#nuevoUsuario").empty();
		fn_menu('Cancelar');
		//fn_paginado(-1);
	}
	else if(accion=='Modificar')
	{	
		if(lc_usuario==-1)
		{
			alertify.alert('Debe seleccionar un registro');
			return false;
		}
		lc_control=1;		
		$("#div_usuarios").hide();
		$("#tdpassModificado").hide();
		$("#txt_PassModificado").hide();		
		fn_usuarioModificado(lc_usuario);
		fn_menu('Modificar');	
	}
	else if(accion=='Eliminar')
	{	
		if(lc_usuario==-1)
		{
			alertify.alert('Debe seleccionar un registro');
			return false;
		}
		mensaje="Esta seguro(a) que desea eliminar el usuario seleccionado?";
		alertify.confirm(mensaje,function (e) {if (e) {
		alertify.success("Usuario eliminado correctamente");
		send={"eliminaUsuario":1};
		send.codUsuarioElimina=lc_usuario;				
		$.getJSON("config_usuarios.php",send,function(datos) 
			{			
				window.location.reload();
                //alertify.success("Usuario eliminado correctamente");
			});
												 
            } else { 
                        alertify.error("Operacion cancelada por el usuario.");
            }
      });
		/*
		alertify.confirm("<p>Aqu� confirmamos algo.<br><br><b>ENTER</b> y <b>ESC</b> corresponden a <b>Aceptar</b> o <b>Cancelar</b></p>", function (e) {
            if (e) {
                  alertify.success("Has pulsado '" + alertify.labels.ok + "'");
            } else { 
                        alertify.error("Has pulsado '" + alertify.labels.cancel + "'");
            }
      }); 
		*/
		
	}
}

function fn_cargarCodUsuario()
{
	send={"cargarCodUsuario":1};
	//send.codcadena=lc_cadena;//$("#selcadena").val();
	$.ajax({async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_usuarios.php",data:send,
		success:function(datos)
		{
			$("#txt_codUsuario").val(datos.codUsuario);
		}
		   });
}

function fn_cargarPerfil()
{
	$("#selPerfiles").empty();
	send={"cargarPerfil":1};	
	$.ajax({async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_usuarios.php",data:send,
		success:function(datos)
		{
			if(datos.str>0)
			{
				$('#selPerfiles').html("<option value='0'>----Seleccione Perfil----</option>");
				for(i=0; i<datos.str; i++) 
				{
					html="<option value='"+datos[i]['prf_id']+"'>"+datos[i]['prf_descripcion']+"</option>";
					$("#selPerfiles").append(html);								
				}
				$('#selPerfilesModificado').html("<option value='0'>----Seleccione Perfil----</option>");
				for(i=0; i<datos.str; i++) 
				{
					html="<option value='"+datos[i]['prf_id']+"'>"+datos[i]['prf_descripcion']+"</option>";
					$("#selPerfilesModificado").append(html);								
				}
			}
		}
		   });
}

function fn_cargaRestaurante()
{
	$('#selRestaurante').empty();
	$("#selRestauranteModificado").empty();
	send={"cargaRestaurante":1};	
	$.ajax({async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_usuarios.php",data:send,
		success:function(datos)
		{
			if(datos.str>0)
			{
				$('#selRestaurante').html("<option selected value='0'>----------------Seleccione Restaurante----------------</option>");
				for(i=0; i<datos.str; i++) 
				{
					html="<option value='"+datos[i]['rst_id']+"'>"+datos[i]['Descripcion']+"</option>";
					$("#selRestaurante").append(html);								
				};
				$('#selRestauranteModificado').html("<option selected value='0'>----------------Seleccione Restaurante----------------</option>");
				for(i=0; i<datos.str; i++) 
				{
					html="<option value='"+datos[i]['rst_id']+"'>"+datos[i]['Descripcion']+"</option>";
					$("#selRestauranteModificado").append(html);								
				};		

			}
		}
		   });
}

function fn_paginado(pagina)
{
	if(pagina==-1)
		send={"cargarUsuarios":0};	
	else if(pagina==0) {
		send={"cargarUsuarios":$("#selOpt").val()-1};
	}
	else if(pagina==1)
	{
		//alert(pagina);
		if($("#selOpt").val()==1)
			return false;
		lc_pageOpt=$("#selOpt").val()-1;
		$("#selOpt").val(lc_pageOpt);
		send={"cargarUsuarios":$("#selOpt").val()-1};
	}
	else if(pagina==2)
	{
		lc_pageOpt=$("#selOpt").val()+1;
		if(lc_paginas<lc_pageOpt)
			return false;
		$("#selOpt").val(lc_pageOpt);
		send={"cargarUsuarios":$("#selOpt").val()-1};
	}

	send.campo=$('#selbusqueda').val();
	send.valor=$('#tex_busqueda').val();
	$.ajax({async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_usuarios.php",data:send,
		success:function(datos)
		{ 
			if(datos.str>0)
			{	
			//alert(datos.str2);
				$("div#optPagina").html(datos.str2).find("#selOpt").val(send.cargarUsuarios+1);
				lc_paginas=datos.paginas;				
				
				$("#div_usuarios").show();
				$("#detalle_usuarios").empty();
				for(i=0;i<datos.str;i++)
				{
				html="<tr class='tabla_detalleMov' id='"+i+"' style='cursor:pointer;'";
					html+="onclick='fn_seleccion("+i+","+datos[i]['usr_id']+")'>";
					html+="<td align='center' style='width:280px;'>"+datos[i]['usr_descripcion']+"&nbsp;</td>";
					html+="<td align='center' style='width:149px;'>"+datos[i]['usr_usuario']+"&nbsp;</td>";
					html+="<td align='center' style='width:149px;'>"+datos[i]['prf_descripcion']+"&nbsp;</td>";
					html+="<td align='center' style='width:144px;'>"+datos[i]['std_id']+"&nbsp;</td></tr>";					
					$("#detalle_usuarios").append(html);
				}
			}
			else
			{
				$("#div_usuarios").hide();
				alertify.alert("No existen usuarios que mostrar.");
				$("#alertify-ok").click(
						function()
						{						
						window.location.reload();
						}
						);
			}
		}
		 });
}


function fn_seleccion(fila,usr_id)
{			
	$("#detalle_usuarios tr").removeClass("activo");
	$("#"+fila+"").addClass("activo");
	$("#hid_usrId").val(usr_id);
	lc_usuario=usr_id;
	
}


function fn_usuarioModificado(lc_usuario)
{
	$("#modificarUsuario").show();	
	send={"cargarUsuarioModificado":1};	
	send.codUsuarioModificado=lc_usuario;
	$.ajax({async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_usuarios.php",data:send,
		success:function(datos)
		{ 
			$("#txt_codUsuarioModificado").val(datos.usr_id);
			$("#sel_estadoModificado").attr('disabled', false);
			if(datos.std_id=='Activo')
			{
			$("#sel_estadoModificado").val(1);
			}
			else
			$("#sel_estadoModificado").val(0);
			$("#txt_nombreUsuarioModificado").val(datos.usr_descripcion);
			$("#txt_inicialesModificado").val(datos.usr_iniciales);
			$("#txt_UsuarioModificado").val(datos.usr_usuario);
			$("#selPerfilesModificado").val(datos.prf_id);
			$("#selRestauranteModificado").val(datos.rst_id);			
		}
		});
}


function checkPass(valor)
{
	if(valor=='con')
	{
		lc_pass=0;
		$("#tdpassModificado").hide();
		$("#txt_PassModificado").hide();		
	}
	if(valor=='res')
	{
		lc_pass=1;
		$("#tdpassModificado").show();
		$("#txt_PassModificado").show();		
		$("#txt_PassModificado").focus();		
	}
}


function asignaTarjeta(id)
{
	lc_tarjeta=1;
	$( "#modal_tarjeta" ).dialog
		({
			width: 400,
			autoOpen: false,
			resizable: false,			
			modal: true,
			position: "center",
			closeOnEscape: false,
		});	    
					
		$( "#modal_tarjeta" ).dialog( "open" );	
}

function asignaTarjetaModificada(id)
{
	lc_tarjeta=1;
	$( "#modal_tarjetaModificada" ).dialog
		({
			width: 400,
			autoOpen: false,
			resizable: false,			
			modal: true,
			position: "center",
			closeOnEscape: false,
		});	    
					
		$( "#modal_tarjetaModificada" ).dialog( "open" );	
}


function fn_cerrarModal(idBtn)
{
	if(idBtn=='can')
	{
		$("#tarjeta").attr("checked",false);
		$( "#modal_tarjeta" ).dialog( "destroy" );
	}
	if(idBtn=='ace')
	{
		if($("#txt_mtarjeta").val()=='')
		{
			alertify.alert("Ingrese la clave de la tarjeta.");
			return false;
		}
		else
		$( "#modal_tarjeta" ).dialog( "destroy" );
	}
}


function fn_cerrarModalModificada(idMod)
{
	if(idMod=='can')
	{
		$("#tarjetaModificada").attr("checked",false);
		$( "#modal_tarjetaModificada" ).dialog( "destroy" );
	}
	if(idMod=='ace')
	{
		if($("#txt_mtarjetaModificada").val()=='')
		{
			alertify.alert("Ingrese la clave de la tarjeta.");
			return false;
		}
		else
		$( "#modal_tarjetaModificada" ).dialog( "destroy" );
	}
}


