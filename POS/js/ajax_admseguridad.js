///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuraci�n de descuentos ///////////////////
///////TABLAS INVOLUCRADAS: Descuentos ////////////////////////////////////////
///////FECHA CREACION: 07-06-2015 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
var pcn_pstn = 1;

$(document).ready(function() {
	fn_btn('cancelar',0);
	fn_btn('eliminar',0);
	fn_btn('agregar', 1);
	fn_cargarPerfiles(0, 52);
	fn_cargarUsuarios(52);
	fn_seleccionPestana(1);
	//$('#mdl_prfl').modal('show');
});

function fn_agregarUsuario(){
	alert("Nuevo usuario");
}

function fn_cargarUsuarios(std_id){
	var html = '<thead><tr class="active"><th class="text-center">Descripci&oacute;n</th><th class="text-center">Usuario</th><th class="text-center">Iniciales</th><th class="text-center">Perfil</th><th class="text-center">Local Asignado</th><th class="text-center">Activo</th></tr></thead>';
	send = {"cargarUsuarios": 1};
	send.accion = 'cargarUsuarios';
	send.resultado = 2;
	send.codigo = 0;
	send.cdn_id = $('#sess_cdn_id').val();
	send.std_id = std_id;
	$.getJSON("../adminSeguridad/config_seguridad.php", send, function(datos){
		if(datos.str > 0){
			for(i=0;i<datos.str;i++){
				html = html + '<tr id="usr_'+datos[i]['usr_id']+'" onclick="fn_seleccionarUsuario('+datos[i]['usr_id']+')"><td>'+datos[i]['usr_descripcion']+'</td><td class="text-center">'+datos[i]['usr_usuario']+'</td><td class="text-center">'+datos[i]['usr_iniciales']+'</td><td class="text-center">'+datos[i]['prf_descripcion']+'</td><td class="text-center">'+datos[i]['rst_descripcion']+'</td><td class="text-center">';
				if(datos[i]['std_id'] == 13){
					html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
				}else{
					html = html + '<input type="checkbox" name="" value="1" disabled/>';
				}
				html = html + '</td></tr>';
			}
			$('#tbl_lst_srs').html(html);
			$('#tbl_lst_srs').dataTable({
				'destroy': true
			});
			$("#tbl_lst_srs_length").hide();
			$("#tbl_lst_srs_paginate").addClass('col-xs-10');
			$("#tbl_lst_srs_info").addClass('col-xs-10');
			$("#tbl_lst_srs_length").addClass('col-xs-6');
		}else{
			html = '<tr><th colspan="6">No Existen Registros.</th></tr>';
			$('#tbl_lst_prfls').html(html);
		}
	});
}

function fn_seleccionarUsuario(usr_id){
	$("#tbl_lst_srs tr").removeClass("success");
	$("#usr_"+usr_id).addClass("success");
}

function fn_agregar(){
	if(pcn_pstn>0){
		fn_agregarPerfil();
	}else{
		fn_agregarUsuario();
	}
}

function fn_agregarPerfil(){
	$("#tqt_mdl_prfl").html("Nuevo Perfil");
	$("#prfl_std_id").prop("checked", true);
	$("#prf_descripcion").val("");
	$("#prf_nvl_seg").val(100);
	$("#hdn_prf_id").val(0);
	$("#btn_pcn_prfl").html('<button type="button" class="btn btn-primary" onclick="fn_actualizarPerfil(1)">Guardar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>');
	$('#mdl_prfl').modal('show');
}

function fn_actualizarPerfil(accion){
	var html = '<thead><tr class="active"><th class="text-center">Descripci&oacute;n</th><th class="text-center">Nivel</th><th class="text-center">Activo</th></tr></thead>';
	var usr_id = $("#sess_usr_id").val();
	var std_id = 0;
	if($('#prfl_std_id').is(':checked')){
		std_id = 52;
	}else{
		std_id = 25;
	}
	var prf_id = $("#hdn_prf_id").val();
	var prf_descripcion = $("#prf_descripcion").val();
	var prf_nivel = $("#prf_nvl_seg").val();
	if(fn_validarCampoVacio(prf_descripcion)){
		if(fn_validarCampoVacio(prf_nivel)){
			send = {"actualizarPerfil": 1};
			send.accion = accion;
			send.prf_id = prf_id;
			send.prf_descripcion = prf_descripcion;
			send.prf_nivel = prf_nivel;
			send.std_id = std_id;
			send.usr_id = usr_id;
			$.getJSON("../adminSeguridad/config_seguridad.php", send, function(datos){
				if(datos.str > 0){
					for(i=0;i<datos.str;i++){
						html = html + '<tr id="prfl_'+datos[i]['prf_id']+'" onclick="fn_seleccionarPerfil('+datos[i]['prf_id']+')" ondblclick="fn_cargarPerfil('+datos[i]['prf_id']+', \''+datos[i]['prf_descripcion']+'\','+datos[i]['std_id']+','+datos[i]['prf_nivel']+')"><td>'+datos[i]['prf_descripcion']+'</td><td class="text-center">'+datos[i]['prf_nivel']+'</td><td class="text-center">';
						if(datos[i]['std_id'] == 52){
							html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
						}else{
							html = html + '<input type="checkbox" name="" value="1" disabled/>';
						}
						html = html + '</td></tr>';
					}
					$('#tbl_lst_prfls').html(html);
					$('#tbl_lst_prfls').dataTable({
						'destroy': true
					});
					$("#tbl_lst_prfls_length").hide();
					$("#tbl_lst_prfls_paginate").addClass('col-xs-10');
					$("#tbl_lst_prfls_info").addClass('col-xs-10');
					$("#tbl_lst_prfls_length").addClass('col-xs-6');
					$('#mdl_prfl').modal('hide');
					$("#opciones_estado label").removeClass("active");
					$("#opciones_1").addClass("active");
					fn_alerta("<b>Confirmado!</b> Registro actualizado correctamente.", "success");
				}else{
					html = '<tr><th colspan="3">No Existen Registros.</th></tr>';
					$('#tbl_lst_prfls').html(html);
				}
			});
		}else{
			fn_alerta("<b>Alerta!</b> Nivel de Seguridad Campo Obligatorio.", "danger");
		}
	}else{
		fn_alerta("<b>Alerta!</b> Descripci&oacute;n de Perfil Campo Obligatorio.", "danger");
	}
}

function fn_cargarPerfil(prf_id, prf_descripcion, std_id, prf_nivel){
	$("#btn_pcn_prfl").html('<button type="button" class="btn btn-primary" onclick="fn_actualizarPerfil(0)">Guardar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>');
	$("#tqt_mdl_prfl").html(prf_descripcion);
	$("#hdn_prf_id").val(prf_id);
	if(std_id == 52){
		$("#prfl_std_id").prop("checked", true);
	}else{
		$("#prfl_std_id").prop("checked", false);
	}
	$("#prf_descripcion").val(prf_descripcion);
	$("#prf_nvl_seg").val(prf_nivel);
	$('#mdl_prfl').modal('show');
}

function fn_cargarPerfiles(resultado, std_id){
	var html = '<thead><tr class="active"><th class="text-center">Descripci&oacute;n</th><th class="text-center">Nivel</th><th class="text-center">Activo</th></tr></thead>';
	send = {"cargarPerfiles": 1};
	send.accion = 'cargarPerfiles';
	send.resultado = resultado;
	send.codigo = 0;
	send.cdn_id = 0;
	send.std_id = std_id;
	$.getJSON("../adminSeguridad/config_seguridad.php", send, function(datos){
		if(datos.str > 0){
			for(i=0;i<datos.str;i++){
				html = html + '<tr id="prfl_'+datos[i]['prf_id']+'" onclick="fn_seleccionarPerfil('+datos[i]['prf_id']+')" ondblclick="fn_cargarPerfil('+datos[i]['prf_id']+', \''+datos[i]['prf_descripcion']+'\','+datos[i]['std_id']+','+datos[i]['prf_nivel']+')"><td>'+datos[i]['prf_descripcion']+'</td><td class="text-center">'+datos[i]['prf_nivel']+'</td><td class="text-center">';
				if(datos[i]['std_id'] == 52){
					html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
				}else{
					html = html + '<input type="checkbox" name="" value="1" disabled/>';
				}
				html = html + '</td></tr>';
			}
			$('#tbl_lst_prfls').html(html);
			$('#tbl_lst_prfls').dataTable({
				'destroy': true
			});
			$("#tbl_lst_prfls_length").hide();
			$("#tbl_lst_prfls_paginate").addClass('col-xs-10');
			$("#tbl_lst_prfls_info").addClass('col-xs-10');
			$("#tbl_lst_prfls_length").addClass('col-xs-6');
		}else{
			html = '<tr><th colspan="3">No Existen Registros.</th></tr>';
			$('#tbl_lst_prfls').html(html);
		}
	});
}

function fn_seleccionarPerfil(prfl_id){
	$("#tbl_lst_prfls tr").removeClass("success");
	$("#prfl_"+prfl_id).addClass("success");
}

function fn_seleccionPestana(opcion){
	if(opcion>0){
		$('#tqt_mdl').html("Lista de Perfiles");
		$('#opciones_estado').html('<label id="opciones_1" class="btn btn-default btn-sm active"><input type="radio" value="Todos" autocomplete="off" name="ptns_std_prfl" onchange="fn_cargarPerfiles(0, 0)" checked="checked">Todos</label><label class="btn btn-default btn-sm"><input type="radio" value="Activos" autocomplete="off"  name="ptns_std_prfl" onchange="fn_cargarPerfiles(1, 52)">Activos</label><label class="btn btn-default btn-sm"><input type="radio" value="Inactivos" autocomplete="off"  name="ptns_std_prfl" onchange="fn_cargarPerfiles(1, 25)">Inactivos</label>');
	}else{
		$('#tqt_mdl').html("Lista de Usuarios");
	}
	pcn_pstn = opcion;
}

function fn_validarCampoVacio(valor){
	if(valor.length > 0){
		return true;
	}else{
		return false;
	}
}

function fn_alerta(mensaje, tipo){
	setTimeout(function() {
        $.bootstrapGrowl(mensaje, {
            type: tipo,
            align: 'right',
            width: '500',
            allow_dismiss: false
        });
    }, 100);
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

/*
$(document).ready(function(){
	fn_btn('cancelar',0);
	fn_btn('eliminar',0);
	fn_btn('agregar',0);
	fn_cargarInformacionUsuario();
});

function fn_cambioContrasena(){
	$('#mdl_clv').modal('show');
}

function fn_cargarInformacionUsuario(){
	send={"consultarInformacionUsuario": 1};
	$.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminUsuario/config_usuario.php", data: send, success:
		function(datos){
			if(datos.str > 0){
				$('#nmbr_usr').html(datos[0]['usr_descripcion']);
				$('#ncls_usr').html(datos[0]['usr_iniciales']);
				$('#fch_ltm_ngrs_usr').html(datos[0]['ultimo_ingreso']);
				$('#prfl_usr').html(datos[0]['prf_descripcion']);
				$('#mxpnt_usr').html(datos[0]['usr_usuario']);
			}
		}
	});
}

function fn_actualizarContrasena(){
	var actual = $('#ctsn_ctl').val();
	var nueva = $('#ctsn_nv').val();
	var confirmacion = $('#cnfrmcn_ctsn_nv').val();
	if(fn_validarCampoVacio(actual) && fn_validarCampoVacio(nueva) && fn_validarCampoVacio(confirmacion)){
		if(nueva == confirmacion){
			send={"actualizarClave": 1};
			send.accion = 1;
			send.actual = actual;
			send.nueva = nueva;
			send.confirmar = confirmacion;
			$.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminUsuario/config_usuario.php", data: send, success:
				function(datos){
					if(datos.str > 0){
						if(datos[0]['Confirmar'] > 0){
							$('#mdl_clv').modal('hide');
							fn_alerta("<b>Correcto!</b> Informacion actualizada.", "success");
						}else{
							fn_alerta("<b>Alerta!</b> La Contrase&ntilde;as no se Actualizo.", "danger");
						}
					}
				}
			});
		}else{
			fn_alerta("<b>Alerta!</b> Las contrase&ntilde;as no coinciden.", "danger");
		}
	}else{
		fn_alerta("<b>Alerta!</b> Todos los campos son obligatorios.", "danger");
	}
}

function fn_validarCampoVacio(valor){
	if(valor.length > 0){
		return true;
	}else{
		return false;
	}
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

function fn_alerta(mensaje, tipo){
	setTimeout(function() {
        $.bootstrapGrowl(mensaje, {
            type: tipo,
            align: 'right',
            width: '500',
            allow_dismiss: false
        });
    }, 100);
}
*/