///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de accesos //////////////////////
///////FECHA CREACION: 27-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

$(document).ready(function (){
	fn_btn('agregar', 1);
	fn_verAccesoPantallas();
	$("#acc_nvl_seg").prop('disabled', true);
});

function fn_acceso(accion){
	var html = '<thead><tr class="active"><th class="text-center">Acceso</th><th class="text-center">Descripci&oacute;n</th><th class="text-center">Nivel</th></tr></thead>';
	var usr_id = $("#sess_usr_id").val();
    var cdn_id = $("#sess_cdn_id").val();
	var acc_id = $("#hdn_ccs_id").val();
	var acc_nombre = $('#acc_nombre').val();
	var acc_descripcion = $('#acc_descripcion').val();
	var acc_nivel = $('#acc_nvl_seg').val();
	if(fn_validarCampoVacio(acc_nombre)){
		if(fn_validarCampoVacio(acc_descripcion)){
			send = {"administrarAcceso": 1};
			send.accion = accion;
			send.acc_id = acc_id;
			send.acc_descipcion = acc_descripcion;
			send.acc_nombre = acc_nombre;
			send.acc_nivel = acc_nivel;
			send.usr_id = usr_id;
			send.cdn_id = cdn_id;
			$.getJSON("../adminAcceso/config_acceso.php", send, function(datos){
				if(datos.str > 0){
					for(i=0;i<datos.str;i++){
						html = html + '<tr id="ccs_'+datos[i]['acc_id']+'" onclick="fn_seleccionarAcceso(\''+datos[i]['acc_id']+'\')" ondblclick="fn_cargarAcceso(\''+datos[i]['acc_id']+'\', \''+datos[i]['acc_Nombre']+'\', \''+datos[i]['acc_descripcion']+'\', '+datos[i]['acc_Nivel']+')"><td>'+datos[i]['acc_descripcion']+'</td><td>'+datos[i]['acc_Nombre']+'</td><td>'+datos[i]['acc_Nivel']+'</td></tr>';
					}
					$('#tbl_lst_ccs').html(html);
					$('#tbl_lst_ccs').dataTable({
						'destroy': true
					});
					$("#tbl_lst_ccs_length").hide();
					$("#tbl_lst_ccs_paginate").addClass('col-xs-10');
					$("#tbl_lst_ccs_info").addClass('col-xs-10');
					$("#tbl_lst_ccs_length").addClass('col-xs-6');
					$('#mdl_acc').modal('hide');
					alertify.success("<b>Confirmado!</b> Registro actualizado correctamente.");
				}
			});
		}else{
			alertify.error("<b>Alerta!</b> Descripci&oacute;n campo obligatorio");
		}
	}else{
		alertify.error("<b>Alerta!</b> Nombre campo obligatorio");
	}
}

function fn_verAccesoPantallas(){
	var html = '<thead><tr class="active"><th class="text-center">Acceso</th><th class="text-center">Descripci&oacute;n</th><th class="text-center">Nivel</th></tr></thead>';
	send = {"cargarAccesos": 1};
	send.resultado = 0;
	$.getJSON("../adminAcceso/config_acceso.php", send, function(datos){
		if(datos.str > 0){
			for(i=0;i<datos.str;i++){
				html = html + '<tr id="ccs_'+datos[i]['acc_id']+'" onclick="fn_seleccionarAcceso(\''+datos[i]['acc_id']+'\')" ondblclick="fn_cargarAcceso(\''+datos[i]['acc_id']+'\', \''+datos[i]['acc_Nombre']+'\', \''+datos[i]['acc_descripcion']+'\', '+datos[i]['acc_Nivel']+')"><td>'+datos[i]['acc_descripcion']+'</td><td>'+datos[i]['acc_Nombre']+'</td><td>'+datos[i]['acc_Nivel']+'</td></tr>';
			}
			$('#tbl_lst_ccs').html(html);
			$('#tbl_lst_ccs').dataTable({
				'destroy': true
			});
			$("#tbl_lst_ccs_length").hide();
			$("#tbl_lst_ccs_paginate").addClass('col-xs-10');
			$("#tbl_lst_ccs_info").addClass('col-xs-10');
			$("#tbl_lst_ccs_length").addClass('col-xs-6');
		}
	});
}

function fn_agregar(){
	$("#hdn_ccs_id").val(0);
	$('#tqt_mdl_acc').html("Agregar Nuevo Acceso");
	$('#acc_nombre').val("");
	$('#acc_descripcion').val("");
	$('#acc_nvl_seg').val(0);
	$("#btn_pcn_acc").html('<button type="button" class="btn btn-primary" onclick="fn_acceso(1)">Guardar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');
	$('#mdl_acc').modal('show');
}

function fn_cargarAcceso(acc_id, acc_descripcion, acc_nombre, acc_nivel){
	$('#tqt_mdl_acc').html(acc_nombre);
	$('#acc_nombre').val(acc_nombre);
	$('#acc_descripcion').val(acc_descripcion);
	$('#acc_nvl_seg').val(acc_nivel);
	$("#btn_pcn_acc").html('<button type="button" class="btn btn-primary" onclick="fn_acceso(0)">Guardar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');
	$('#mdl_acc').modal('show');
}

function fn_seleccionarAcceso(acc_id){
	$("#tbl_lst_ccs tr").removeClass("success");
	$("#ccs_"+acc_id).addClass("success");
	$("#hdn_ccs_id").val(acc_id);
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

function fn_validarMaximo(){
	var valor = $('#acc_nvl_seg').val();
	if(valor<0){
		valor = $('#acc_nvl_seg').val(0);
	}else if(valor>100){
		valor = $('#acc_nvl_seg').val(100);
	}
}

function justNumbers(e){
	var keynum = window.event ? window.event.keyCode : e.which;
	if ((keynum == 8) || (keynum == 46))
	return true;
	 
	return /\d/.test(String.fromCharCode(keynum));
}