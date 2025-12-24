///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de Configuración de Pantallas ////////////////////
///////FECHA CREACION: 25-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

var slccn_acc_pnt = '';

$(document).ready(function(){
	fn_cargarPredecesores();
	fn_cargarPantallas(2);
	fn_btn('agregar', 1);
	fn_cargarImagenes();
	fn_cargarAccesos();
});

function fn_seleccionarTodosAccesos(){
	if($("#chck_acc_tds").is(':checked')){
		$(":input[name=ckbx_acc_id]").each(function(){
			if(slccn_acc_pnt.indexOf($(this).val()+'_')<0){
				slccn_acc_pnt = slccn_acc_pnt + $(this).val() + '_';
			}
			$("#ckbx_acc_id_"+$(this).val()).prop('checked', 'checked');
		});
	}else{
		$(":input[name=ckbx_acc_id]").each(function(){
			if(slccn_acc_pnt.indexOf($(this).val()+'_')>=0){
				slccn_acc_pnt = slccn_acc_pnt.replace($(this).val()+'_', '');
			}
			$("#ckbx_acc_id_"+$(this).val()).prop('checked', false);
		});
	}
}

function fn_quitarAccesos(){
	$(":input[name=ckbx_acc_id]").each(function(){
		$("#ckbx_acc_id_"+$(this).val()).prop('checked', false);
	});
}

function fn_cargarAccesos(){
	var html = '';
	send = {"cargarAccesos": 1};
	send.resultado = 9;
	send.pnt_id = 0;
	send.nivel = 0;
	send.std_id = 0;
	$.getJSON("../adminPantalla/config_pantalla.php", send, function(datos){
		if(datos.str > 0){
			for(i=0; i<datos.str; i++){
				html = html + '<a class="list-group-item"><input id="ckbx_acc_id_'+datos[i]['acc_id']+'" name="ckbx_acc_id" onclick="fn_seleccionarAcceso(\''+datos[i]['acc_id']+'\')" value="'+datos[i]['acc_id']+'" type="checkbox">&nbsp; '+datos[i]['acc_descripcion']+'</a>';
			}
			$("#lst_acc_pnt").html(html);
		}
	});	
}

function fn_cargarAccesosPantalla(pnt_id){
	var html = '';
	send = {"cargarAccesos": 1};
	send.resultado = 10;
	send.pnt_id = pnt_id;
	send.nivel = 0;
	send.std_id = 0;
	$.getJSON("../adminPantalla/config_pantalla.php", send, function(datos){
		if(datos.str > 0){
			for(i=0; i<datos.str; i++){
				if(slccn_acc_pnt.indexOf(datos[i]['acc_id']+'_')<0){
					slccn_acc_pnt = slccn_acc_pnt + datos[i]['acc_id'] + '_';
					$("#ckbx_acc_id_"+ datos[i]['acc_id']).prop('checked', 'checked');
				}
			}
		}
	});
}

function fn_seleccionarAcceso(pnt_id){
	if($("#ckbx_acc_id_"+pnt_id).is(':checked')){
		slccn_acc_pnt = slccn_acc_pnt + pnt_id + '_';
	}else{
		slccn_acc_pnt = slccn_acc_pnt.replace(pnt_id+'_', '');
	}
}

function fn_cargarImagenes(){
	var html = '';
	$.getJSON("../adminPantalla/imagenes.php", function(datos){
		for(i=0;i<datos.length;i++){
			if(datos[i]['type']<1){
				html = html + '<option class="glyphicon '+datos[i]['name']+'" value="'+datos[i]['name']+'"> '+datos[i]['descript']+'</option>';
			}else{
				html = html + '<option class="'+datos[i]['name']+'" value="'+datos[i]['name']+'"> '+datos[i]['descript']+'</option>';
			}
		}
		$("#pnt_img_mn").html(html);
		$("#pnt_img_mn").chosen({no_results_text: "No existen registros para "});
		$("#pnt_img_mn_chosen").css('width','270');
	});
}

function fn_agregar(){
	fn_quitarAccesos();
	slccn_acc_pnt = '';
	$("#chck_acc_tds").prop('checked', false);
	$("#pestanas li").removeClass("active");
	$('#tag_inicio').addClass('active');
	$("#pst_accs div").removeClass("active");
	$('#inicio').addClass('active');
	$("#hdn_pnt_id").val(0);
	$("#pcn_tp_dmnstrcn label").removeClass("active");
	$("#tp_Admi").addClass("active");
	$("#tqt_mdl_pntll").html("Nueva Pantalla");
	$('#pntll_std_id').prop('checked', true);
	$("#pntll_nmbr_mstrr").val("");
	$("#pntll_frmlr").val("");
	$("#pntll_crpt").val("");
	$("#pnt_prdcsr").val(0);
	$("#pnt_sb_prdcsr").prop('disabled', true);
	$("#pnt_cnfg").val(0);
	$("#pnt_cnfg").prop('disabled', false);
	$("#pntll_frmlr").prop('disabled', false);
	$("#pntll_crpt").prop('disabled', false);
	$("#pnt_prdcsr").prop('disabled', true);
	$("#pntll_rdn_mn").prop('disabled', false);
	$("#pntll_rdn_mn").val("");
	$("#pcn_tp_dmnstrcn label").removeClass("active");
	$("#tp_Admin").addClass("active");
	$("#input[name=tp_dmnstrcn]").prop("checked", false);
	$("#tp_Admin input").prop("checked", true);
	fn_seleccionarMaxPointMenu();
	fn_cargarOrdenMenuModulo('Admin');
	$('#mdl_pnt').modal('show');
	$("#btn_pcn_pntll").html('<button type="button" class="btn btn-primary" onclick="fn_Pantalla(1)">Guardar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');
}

function fn_Pantalla(accion){
	var html = '<thead><tr class="active"><th class="text-center">Orden</th><th class="text-center">Nivel</th><th class="text-center">M&oacute;dulo</th><th class="text-center">Formulario</th><th class="text-center">Tipo Men&uacute;</th><th class="text-center">Ruta</th><th class="text-center">Activo</th></tr></thead>';
	var usr_id = $('#sess_usr_id').val();
	var cdn_id = $('#sess_cdn_id').val();
	var pnt_id = $("#hdn_pnt_id").val();
	var pnt_Nombre_Mostrar = $("#pntll_nmbr_mstrr").val();
	var pnt_Nombre_Formulario = $("#pntll_frmlr").val();
	var pnt_Ruta = $("#pntll_crpt").val();
	var std_id = $('#pcn_tp_dmnstrcn').find("label.active").attr("id");
	if(!std_id){
		std_id = 'Inactivo';
	}else{
		std_id = std_id.substring(3, std_id.length);
	}
	var pnt_Nivel = 0;
	var pnt_Orden_Menu = $("#pntll_rdn_mn").val();
	if(!pnt_Orden_Menu){
		pnt_Orden_Menu = 0;
	}
	var pnt_Imagen = $("#pnt_img_mn").val();
	var pnt_Descripcion = $("#pnt_cnfg").val();
	if(pnt_Descripcion == 0){
		pnt_Nivel = -1;
		pnt_Descripcion = '#';
	}else if(pnt_Descripcion == 1){
		pnt_Nivel = 0;
		pnt_Descripcion = 'MODULO';
	}else if(pnt_Descripcion == 2){
		pnt_Nivel = 1;
		pnt_Descripcion = 'SUBMODULO';
	}else if(pnt_Descripcion == 3){
		pnt_Nivel = 2;
		pnt_Descripcion = 'INICIO';
	}else if(pnt_Descripcion == 4){
		pnt_Nivel = 3;
		pnt_Descripcion='PANTALLA';
	}else if(pnt_Descripcion == 5){
		pnt_Nivel = 4;
		pnt_Descripcion='SUBPANTALLA';
	}
	if(!$("#pntll_std_id").is(':checked')){
		std_id = 'Inactivo';
	}
	if(std_id.length>0){
		if(fn_validarCampoVacio(pnt_Nombre_Mostrar)){
			$("#opciones_estado label").removeClass("active");
			$("#opciones_1").addClass("active");
			$("#input[name=ptns_std_prfl]").prop("checked", false);
			$("#opciones_1 input").prop("checked", true);
			send = {"administrarPantallas": 1};
			send.accion = accion;
			send.pnt_id = pnt_id;
			send.pnt_Nombre_Mostrar = pnt_Nombre_Mostrar;
			send.pnt_Nombre_Formulario = pnt_Nombre_Formulario;
			send.pnt_Descripcion = pnt_Descripcion;
			send.pnt_Orden_Menu = pnt_Orden_Menu;
			send.pnt_Nivel = pnt_Nivel;
			send.pnt_Ruta = pnt_Ruta;
			send.pnt_Imagen = pnt_Imagen;
			send.std_id = std_id;
			send.usr_id = usr_id;
			send.cdn_id = cdn_id;
			send.acc_id = slccn_acc_pnt;
			$.getJSON("../adminPantalla/config_pantalla.php", send, function(datos){
				if(datos.str > 0){
					for(i=0;i<datos.str;i++){
						html = html + '<tr id="pntll_'+datos[i]['pnt_id']+'" onclick="fn_seleccionarPantalla(\''+datos[i]['pnt_id']+'\');" ondblclick="fn_modificarPantalla(\''+datos[i]['pnt_id']+'\', \''+datos[i]['pnt_Nombre_Mostrar']+'\', \''+datos[i]['pnt_Nombre_Formulario']+'\', \''+datos[i]['pnt_Descripcion']+'\', '+datos[i]['pnt_Orden_Menu']+', '+datos[i]['pnt_Nivel']+', \''+datos[i]['pnt_Ruta']+'\', \''+datos[i]['pnt_Imagen']+'\', \''+datos[i]['std_id']+'\');"><td class="text-center">'+datos[i]['pnt_Orden_Menu']+'</td><td class="text-center">'+datos[i]['pnt_Nivel']+'</td><td>'+datos[i]['pnt_Nombre_Mostrar']+'</td><td>'+datos[i]['pnt_Nombre_Formulario']+'</td><td class="text-center">'+datos[i]['pnt_Descripcion']+'</td><td class="text-center">'+datos[i]['pnt_Ruta']+'</td><td class="text-center">';
						if(datos[i]['std_id'] == 'Admin' || datos[i]['std_id'] == 'Menu Superior Admin' || datos[i]['std_id'] == 'Funciones de Gerente'){
							html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
						}else{
							html = html + '<input type="checkbox" name="" value="1" disabled/>';
						}
						html = html + '</td></tr>';
					}
					$('#tbl_lst_pntlls').html(html);
					$('#tbl_lst_pntlls').dataTable({
						'destroy': true
					});
					$("#tbl_lst_pntlls_length").hide();
					$("#tbl_lst_pntlls_paginate").addClass('col-xs-10');
					$("#tbl_lst_pntlls_info").addClass('col-xs-10');
					$("#tbl_lst_pntlls_length").addClass('col-xs-6');
					$('#mdl_pnt').modal('hide');
					fn_cargarPredecesores();
				}
			});
		}else{
			alertify.error("<b>Alerta!</b> Descripci&oacute;n campo obligatorio.");
		}
	}else{
		alertify.error("<b>Alerta!</b> Seleccione el Tipo de Pantalla.");
	}
}

function fn_cargarPantallas(resultado){
	var html = '<thead><tr class="active"><th class="text-center">Orden</th><th class="text-center">Nivel</th><th class="text-center">M&oacute;dulo</th><th class="text-center">Formulario</th><th class="text-center">Tipo Men&uacute;</th><th class="text-center">Ruta</th><th class="text-center">Activo</th></tr></thead>';
	send = {"cargarPantallas": 1};
	send.resultado = resultado;
	send.pnt_id = 0;
	send.nivel = 0;
	send.std_id = 0;
	$.getJSON("../adminPantalla/config_pantalla.php", send, function(datos){
		if(datos.str > 0){
			for(i=0;i<datos.str;i++){
				html = html + '<tr id="pntll_'+datos[i]['pnt_id']+'" onclick="fn_seleccionarPantalla(\''+datos[i]['pnt_id']+'\');" ondblclick="fn_modificarPantalla(\''+datos[i]['pnt_id']+'\', \''+datos[i]['pnt_Nombre_Mostrar']+'\', \''+datos[i]['pnt_Nombre_Formulario']+'\', \''+datos[i]['pnt_Descripcion']+'\', '+datos[i]['pnt_Orden_Menu']+', '+datos[i]['pnt_Nivel']+', \''+datos[i]['pnt_Ruta']+'\', \''+datos[i]['pnt_Imagen']+'\', \''+datos[i]['std_id']+'\');"><td class="text-center">'+datos[i]['pnt_Orden_Menu']+'</td><td class="text-center">'+datos[i]['pnt_Nivel']+'</td><td>'+datos[i]['pnt_Nombre_Mostrar']+'</td><td>'+datos[i]['pnt_Nombre_Formulario']+'</td><td class="text-center">'+datos[i]['pnt_Descripcion']+'</td><td class="text-center">'+datos[i]['pnt_Ruta']+'</td><td class="text-center">';
				if(datos[i]['std_id'] == 'Admin' || datos[i]['std_id'] == 'Menu Superior Admin' || datos[i]['std_id'] == 'Funciones de Gerente'){
					html = html + '<input type="checkbox" name="" value="1" checked="checked" disabled/>';
				}else{
					html = html + '<input type="checkbox" name="" value="1" disabled/>';
				}
				html = html + '</td></tr>';
			}
			$('#tbl_lst_pntlls').html(html);
			$('#tbl_lst_pntlls').dataTable({
				'destroy': true
			});
			$("#tbl_lst_pntlls_length").hide();
			$("#tbl_lst_pntlls_paginate").addClass('col-xs-10');
			$("#tbl_lst_pntlls_info").addClass('col-xs-10');
			$("#tbl_lst_pntlls_length").addClass('col-xs-6');
		}
	});
}

function fn_seleccionarPantalla(pntll_id){
	$("#tbl_lst_pntlls tr").removeClass("success");
	$("#pntll_"+pntll_id).addClass("success");
	$("#hdn_pnt_id").val(pntll_id);
}

function fn_modificarPantalla(pnt_id, pnt_Nombre_Mostrar, pnt_Nombre_Formulario, pnt_Descripcion, pnt_Orden_Menu, pnt_Nivel, pnt_Ruta, pnt_Imagen, std_id){
	$.ajaxSetup({async: false});
	fn_quitarAccesos();
	$("#chck_acc_tds").prop('checked', false);
	slccn_acc_pnt = '';
	$("#pestanas li").removeClass("active");
	$('#tag_inicio').addClass('active');
	$("#pst_accs div").removeClass("active");
	$('#inicio').addClass('active');
	var predecesor = Math.floor(pnt_Orden_Menu);
	$("#tqt_mdl_pntll").html(pnt_Nombre_Mostrar);
	if(std_id == 'Inactivo'){
		$("#pcn_tp_dmnstrcn label").removeClass("active");
		$("#input[name=tp_dmnstrcn]").prop("checked", false);
		$('#pntll_std_id').prop('checked', false);
	}else{
		$("#pcn_tp_dmnstrcn label").removeClass("active");
		$('#tp_'+std_id).addClass('active');
		$('#pntll_std_id').prop('checked', true);
		if(std_id == 'Admin'){
			$("#pcn_tp_dmnstrcn label").removeClass("active");
			$("#tp_Admin").addClass("active");
			$("#input[name=tp_dmnstrcn]").prop("checked", false);
			$("#tp_Admin input").prop("checked", true);
			$('#pnt_cnfg option[value=0]').hide();
			$('#pnt_cnfg option[value=1]').show();
			$('#pnt_cnfg option[value=2]').show();
			$('#pnt_cnfg option[value=3]').show();
			$('#pnt_cnfg option[value=4]').show();
			$('#pnt_cnfg option[value=5]').show();
			$("#pnt_cnfg").val(1);
			$("#pnt_cnfg").prop('disabled', false);
		}else if(std_id == 'Funciones de Gerente'){
			$("#pcn_tp_dmnstrcn label").removeClass("active");
			$("#tp_Funciones").addClass("active");
			$("#input[name=tp_dmnstrcn]").prop("checked", false);
			$("#tp_Funciones input").prop("checked", true);
			$('#pnt_cnfg option[value=0]').show();
			$('#pnt_cnfg option[value=1]').hide();
			$('#pnt_cnfg option[value=2]').hide();
			$('#pnt_cnfg option[value=3]').hide();
			$('#pnt_cnfg option[value=4]').hide();
			$('#pnt_cnfg option[value=5]').hide();
			$("#pnt_cnfg").val(0);
			$("#pnt_cnfg").prop('disabled', true);
		}else if(std_id == 'Menu Superior Admin'){
			$("#pcn_tp_dmnstrcn label").removeClass("active");
			$("#tp_Menu").addClass("active");
			$("#input[name=tp_dmnstrcn]").prop("checked", false);
			$("#tp_Menu input").prop("checked", true);
			$('#pnt_cnfg option[value=0]').hide();
			$('#pnt_cnfg option[value=1]').hide();
			$('#pnt_cnfg option[value=2]').hide();
			$('#pnt_cnfg option[value=3]').hide();
			$('#pnt_cnfg option[value=4]').show();
			$('#pnt_cnfg option[value=5]').hide();
			$("#pnt_cnfg").val(4);
			$("#pnt_cnfg").prop('disabled', true);
		}
	}
	$("#pntll_nmbr_mstrr").val(pnt_Nombre_Mostrar);
	$("#pntll_frmlr").val(pnt_Nombre_Formulario);
	$("#pntll_crpt").val(pnt_Ruta);
	if(pnt_Descripcion=='#'){
		$("#pnt_prdcsr").val(0);
		$("#pnt_cnfg").val(0);
		$("#pntll_frmlr").prop('disabled', false);
		$("#pntll_crpt").prop('disabled', false);
		$("#pnt_prdcsr").prop('disabled', true);
		$("#pntll_rdn_mn").prop('disabled', true);
		$("#pnt_sb_prdcsr").prop('disabled', true);
		$("#pntll_rdn_mn").val("");
	}else if(pnt_Descripcion=='MODULO'){
		$("#pnt_img_mn").val(pnt_Imagen).trigger("chosen:updated.chosen");
		$("#pnt_prdcsr").val(0);
		$("#pnt_cnfg").val(1);
		$("#pntll_frmlr").prop('disabled', true);
		$("#pntll_crpt").prop('disabled', true);
		$("#pnt_prdcsr").prop('disabled', true);
		$("#pnt_sb_prdcsr").prop('disabled', true);
		$("#pntll_rdn_mn").prop('disabled', false);
	}else if(pnt_Descripcion=='SUBMODULO'){
		$("#pnt_prdcsr").val(predecesor);
		$("#pnt_cnfg").val(2);
		$("#pntll_frmlr").prop('disabled', true);
		$("#pntll_crpt").prop('disabled', true);
		$("#pnt_prdcsr").prop('disabled', false);
		$("#pntll_rdn_mn").prop('disabled', false);
		$("#pnt_sb_prdcsr").prop('disabled', true);
	}else if(pnt_Descripcion=='INICIO'){
		$("#pnt_img_mn").val(pnt_Imagen).trigger("chosen:updated.chosen");
		$("#pnt_prdcsr").val(0);
		$("#pnt_cnfg").val(3);
		$("#pnt_prdcsr").prop('disabled', true);
		$("#pntll_frmlr").prop('disabled', false);
		$("#pntll_crpt").prop('disabled', false);
		$("#pntll_rdn_mn").prop('disabled', false);
		$("#pnt_sb_prdcsr").prop('disabled', true);
	}else if(pnt_Descripcion=='PANTALLA'){
		$("#pnt_cnfg").val(4);
		$("#pntll_frmlr").prop('disabled', false);
		$("#pntll_crpt").prop('disabled', false);
		$("#pnt_prdcsr").prop('disabled', false);
		$("#pntll_rdn_mn").prop('disabled', false);
		$("#pnt_sb_prdcsr").prop('disabled', true);
		$("#pnt_prdcsr").val(predecesor);
	}else if(pnt_Descripcion=='SUBPANTALLA'){
		$("#pnt_cnfg").val(5);
		$("#pntll_frmlr").prop('disabled', false);
		$("#pntll_crpt").prop('disabled', false);
		$("#pnt_prdcsr").prop('disabled', false);
		$("#pntll_rdn_mn").prop('disabled', false);
		$("#pnt_sb_prdcsr").prop('disabled', false);
		$("#pnt_prdcsr").val(predecesor);
	}
	fn_cargarAccesosPantalla(pnt_id);
	$.ajaxSetup({async: true});
	$("#pntll_rdn_mn").val(pnt_Orden_Menu);
	$('#mdl_pnt').modal('show');
	$("#btn_pcn_pntll").html('<button type="button" class="btn btn-primary" onclick="fn_Pantalla(0)">Guardar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');
}

function fn_verificarConfiguracion(){
	var estado = $("#pnt_cnfg").val();
	if(estado == 0){
		$("#pnt_prdcsr").val(0);
		$("#pntll_crpt").val("");
		$("#pnt_img_mn").val(0).trigger("chosen:updated.chosen");
		$("#pntll_frmlr").prop('disabled', false);
		$("#pntll_crpt").prop('disabled', false);
		$("#pnt_prdcsr").prop('disabled', true);
		$("#pntll_rdn_mn").prop('disabled', true);
		$("#pntll_rdn_mn").val("");
		$("#pnt_sb_prdcsr").prop('disabled', true);
	//MODULO
	}else if(estado == 1){
		$("#pnt_prdcsr").val(0);
		$("#pntll_frmlr").val("");
		$("#pntll_crpt").val("");
		$("#pnt_img_mn").val(0).trigger("chosen:updated.chosen");
		$("#pntll_frmlr").prop('disabled', true);
		$("#pntll_crpt").prop('disabled', true);
		$("#pnt_prdcsr").prop('disabled', true);
		$("#pntll_rdn_mn").prop('disabled', false);
		$("#pnt_sb_prdcsr").prop('disabled', true);
		fn_cargarOrdenMenu();
	//SUBMODULO
	}else if(estado == 2){
		$("#pntll_frmlr").val("");
		$("#pntll_crpt").val("");
		$("#pnt_prdcsr").val(0);
		$("#pnt_img_mn").val(0).trigger("chosen:updated.chosen");
		$("#pntll_frmlr").prop('disabled', true);
		$("#pntll_crpt").prop('disabled', true);
		$("#pnt_prdcsr").prop('disabled', false);
		$("#pntll_rdn_mn").prop('disabled', false);
		$("#pnt_sb_prdcsr").prop('disabled', true);
	//INICIO
	}else if(estado == 3){
		$("#pnt_prdcsr").val(0);
		$("#pntll_frmlr").prop('disabled', false);
		$("#pntll_crpt").prop('disabled', false);
		$("#pnt_prdcsr").prop('disabled', true);
		$("#pntll_rdn_mn").prop('disabled', false);
		$("#pnt_sb_prdcsr").prop('disabled', true);
	//PANTALLA
	}else if(estado == 4){
		$("#pnt_prdcsr").val(0);
		$("#pnt_img_mn").val(0).trigger("chosen:updated.chosen");
		$("#pntll_frmlr").prop('disabled', false);
		$("#pntll_crpt").prop('disabled', false);
		$("#pnt_prdcsr").prop('disabled', false);
		$("#pntll_rdn_mn").prop('disabled', false);
		$("#pnt_sb_prdcsr").prop('disabled', true);
	//SUBPANTALLA
	}else if(estado == 5){
		$("#pnt_prdcsr").val(0);
		$("#pnt_img_mn").val(0).trigger("chosen:updated.chosen");
		$("#pntll_frmlr").prop('disabled', false);
		$("#pntll_crpt").prop('disabled', false);
		$("#pnt_prdcsr").prop('disabled', false);
		$("#pntll_rdn_mn").prop('disabled', false);
		$("#pnt_sb_prdcsr").prop('disabled', false);
	}
}

function fn_cargarOrdenMenuModulo(std_id){
	var pnt_prdcsr = $("#pnt_prdcsr").val();
	var pnt_cnfg = $("#pnt_cnfg").val();
	send = {"cargarOrdenMenu": 1};
	send.resultado = 8;
	send.pnt_id = 0;
	send.nivel = 0;
	send.std_id = std_id;
	$.getJSON("../adminPantalla/config_pantalla.php", send, function(datos){
		if(datos.str > 0){
			$("#pntll_rdn_mn").val(datos[0]['pnt_Orden_Menu']);
		}
	});
}

function fn_cargarOrdenMenu(){
	var pnt_prdcsr = $("#pnt_prdcsr").val();
	var pnt_cnfg = $("#pnt_cnfg").val();
	if(pnt_prdcsr>0){
		if(pnt_cnfg!=5){
			send = {"cargarOrdenMenu": 1};
			send.resultado = 4;
			send.pnt_id = 0;
			send.nivel = pnt_prdcsr;
			send.std_id = 'Admin';
			$.getJSON("../adminPantalla/config_pantalla.php", send, function(datos){
				if(datos.str > 0){
					$("#pntll_rdn_mn").val(datos[0]['pnt_Orden_Menu']);
				}
			});
		}else{
			var html = '<option value="0">-- Seleccione un Subnivel --</option>';
			send = {"cargaSubModulo": 1};
			send.resultado = 5;
			send.pnt_id = 0;
			send.nivel = pnt_prdcsr;
			send.std_id = '';
			$.getJSON("../adminPantalla/config_pantalla.php", send, function(datos){
				if(datos.str > 0){
					for(i=0;i<datos.str;i++){
						html = html + '<option value="'+datos[i]['pnt_Orden_Menu']+'">'+datos[i]['pnt_Nombre_Mostrar']+'</option>';
					}
					$("#pnt_sb_prdcsr").html(html);
				}
			});
		}
	}
}

function fn_cargarPredecesores(){
	var html = '<option value="0">-- Seleccione un Predecesor --</option>';
	var std_id = $('#pcn_tp_dmnstrcn').find("label.active").attr("id");
	std_id = std_id.substring(3, std_id.length);
	send = {"cargarPredecesores": 1};
	send.resultado = 3;
	send.pnt_id = 0;
	send.nivel = 0;
	send.std_id = 0;
	$.getJSON("../adminPantalla/config_pantalla.php", send, function(datos){
		if(datos.str > 0){
			for(i=0;i<datos.str;i++){
				html = html + '<option value="'+datos[i]['pnt_Orden_Menu']+'">'+datos[i]['pnt_Nombre_Mostrar']+'</option>';
			}
			$("#pnt_prdcsr").html(html);
		}
	});
}

function fn_calcularSubNivel(){
	var sub_nivel = $("#pnt_sb_prdcsr").val();
	var std_id = $('#pcn_tp_dmnstrcn').find("label.active").attr("id");
	std_id = std_id.substring(3, std_id.length);
	send = {"cargarOrdenMenu": 1};
	send.resultado = 6;
	send.pnt_id = 0;
	send.nivel = sub_nivel;
	send.std_id = std_id;
	$.getJSON("../adminPantalla/config_pantalla.php", send, function(datos){
		if(datos[0]['pnt_Orden_Menu']){
			$("#pntll_rdn_mn").val(datos[0]['pnt_Orden_Menu']);
		}else{
			$("#pntll_rdn_mn").val(parseFloat(sub_nivel)+0.01);
		}
	});
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

function fn_validarCampoVacio(valor){
	if(valor.length > 0){
		return true;
	}else{
		return false;
	}
}

function fn_seleccionarMaxPointSubMenu(){
	$("#pnt_sb_prdcsr").prop('disabled', true);
	$('#pnt_cnfg option[value=0]').hide();
	$('#pnt_cnfg option[value=1]').hide();
	$('#pnt_cnfg option[value=2]').hide();
	$('#pnt_cnfg option[value=3]').hide();
	$('#pnt_cnfg option[value=4]').show();
	$('#pnt_cnfg option[value=5]').hide();
	$("#pnt_cnfg").val(4);
	fn_verificarConfiguracion();
	$("#pnt_prdcsr").val(0);
	$("#pnt_prdcsr").prop('disabled', true);
}

function fn_seleccionarMaxPointMenu(){
	$("#pnt_sb_prdcsr").prop('disabled', true);
	$('#pnt_cnfg option[value=0]').hide();
	$('#pnt_cnfg option[value=1]').show();
	$('#pnt_cnfg option[value=2]').show();
	$('#pnt_cnfg option[value=3]').show();
	$('#pnt_cnfg option[value=4]').show();
	$('#pnt_cnfg option[value=5]').show();
	$("#pnt_cnfg").val(1);
	fn_verificarConfiguracion();
}

function fn_seleccionarFuncionesGerente(){
	$("#pnt_sb_prdcsr").prop('disabled', true);
	$('#pnt_cnfg option[value=0]').show();
	$('#pnt_cnfg option[value=1]').hide();
	$('#pnt_cnfg option[value=2]').hide();
	$('#pnt_cnfg option[value=3]').hide();
	$('#pnt_cnfg option[value=4]').hide();
	$('#pnt_cnfg option[value=5]').hide();
	$("#pnt_cnfg").val(0);
	fn_verificarConfiguracion();
}


