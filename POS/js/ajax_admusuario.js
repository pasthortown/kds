///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de cambio de clave ///////////////////////////////
///////TABLAS INVOLUCRADAS: Usiarios del Sistemas /////////////////////////////
///////FECHA CREACION: 07-06-2015 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

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
	var usr_id = $('#sess_usr_id').val();
	send={"consultarInformacionUsuario": 1};
	send.usr_id = usr_id;
	$.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminUsuario/config_usuario.php", data: send, success:
		function(datos){
			if(datos.str > 0){
				$('#nmbr_usr').html(datos[0]['usr_descripcion']);
				$('#cdl_usr').html(datos[0]['usr_cedula']);
				$('#ncls_usr').html(datos[0]['usr_iniciales']);
				$('#fch_ltm_ngrs_usr').html(datos[0]['ultimo_ingreso']);
				$('#prfl_usr').html(datos[0]['prf_descripcion']);
				$('#mxpnt_usr').html(datos[0]['usr_usuario']);
			}
		}
	});
}

function fn_actualizarContrasena(){
	var usr_id = $('#sess_usr_id').val();
	var actual = $('#ctsn_ctl').val();
	var nueva = $('#ctsn_nv').val();
	var confirmacion = $('#cnfrmcn_ctsn_nv').val();
	if(fn_validarCampoVacio(actual) && fn_validarCampoVacio(nueva) && fn_validarCampoVacio(confirmacion)){
		if(nueva == confirmacion){
			send={"actualizarClave": 1};
			send.accion = 1;
			send.usr_id = usr_id;
			send.actual = actual;
			send.nueva = nueva;
			send.confirmar = confirmacion;
			$.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminUsuario/config_usuario.php", data: send, success:
				function(datos){
					if(datos.str > 0){
						if(datos[0]['Confirmar'] > 0){
							$('#mdl_clv').modal('hide');
							alertify.success("<b>Correcto!</b> Contrasena actualizada.");
						}else{
							alertify.error("<b>Alerta!</b> La Contrase&ntilde;as no se Actualizo.");
						}
					}
				}
			});
		}else{
			alertify.error("<b>Alerta!</b> Las contrase&ntilde;as no coinciden.");
		}
	}else{
		alertify.error("<b>Alerta!</b> Todos los campos son obligatorios.");
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
		$("#btn_"+boton).css("background","url('../../imagenes/admin_resources/"+boton+".png') 14px 4px no-repeat");
		$("#btn_"+boton).removeAttr("disabled");
		$("#btn_"+boton).addClass("botonhabilitado");
		$("#btn_"+boton).removeClass("botonbloqueado");
	}else{
		$("#btn_"+boton).css("background","url('../../imagenes/admin_resources/"+boton+"_bloqueado.png') 14px 4px no-repeat");
		$("#btn_"+boton).prop('disabled', true);
		$("#btn_"+boton).addClass("botonbloqueado");
		$("#btn_"+boton).removeClass("botonhabilitado");
	}
}