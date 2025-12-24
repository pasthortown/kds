///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuraci�n de perfiles /////////////////////
///////FECHA CREACION: 28-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

var pcn_pstn = 1;
lc_estado=-1;
lc_banderaChecks=-1; 
lc_cajeroGlobal='';

$(document).ready(function() {
	fn_btn('duplicar', 1);
	fn_btn('restablecer', 1); 
	fn_btn('agregar', 1);
	fn_cargarPerfiles(1, 'Activo');
        $("#sel_seleccione").bootstrapSwitch('state', false);
});

function fn_restablecer(){
	var prf_id = $('#tbl_lst_prfls').find("tr.success").attr("id");
	if(prf_id){
		prf_id = prf_id.substring(5, prf_id.length);
		send = {"restablecerClavePerfil": 1};
		send.accion = 3;
		send.prf_id = prf_id;
		send.usr_log = $("#sess_usr_id").val();
		$.getJSON("../adminPerfil/config_perfil.php", send, function(datos){
			if(datos.Confirmar > 0){
				alertify.success("<h5>Claves Restablecidas</h5>");
			}else{
				alertify.error("No se actulizaron los registros");
				
			}
		});
	}else{
		alertify.error("Debe Seleccionar un Perfil");
	}
}

function fn_seleccionTipoAdministracion(){
	if(!$('#pcn_tp_adm').is(':checked')){
		$('#pcn_tp_ope').prop('checked', false);
		$('#pcn_tp_adm').prop('checked', true);
	}
}

function fn_seleccionTipoOperativo(){
	if(!$('#pcn_tp_ope').is(':checked')){
		$('#pcn_tp_adm').prop('checked', false);
		$('#pcn_tp_ope').prop('checked', true);
	}
}

function fn_verAccesoPantallas(){
	var std_id = 0;
	if($('#pcn_tp_adm').is(':checked')){
		std_id = 'Funciones de Gerente';
	}else{
		std_id = 'Admin';
	}
	var nivel = $('#prf_nvl_seg').val();
	var prf_id = $("#hdn_prf_id").val();
	var html = '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';
	send = {"verPantallasPerfil": 1};
	send.resultado = 0;
	send.prf_id = prf_id;
	send.acc_nivel = nivel;
	send.pnt_id = 0;
	send.std_id = std_id;
	$.getJSON("../adminPerfil/config_perfil.php", send, function(datos){
		if(datos.str > 0){
			for(i=0;i<datos.str;i++){
			html = html + "<div class='panel panel-default'><div class='panel-heading' onclick='fn_verAccesos(\""+datos[i]['pnt_id']+"\")' role='tab' id='headingOne' style='padding: 2px 0 2px 10px; margin: 0;'><h6><a class='collapsed' role='button' data-toggle='collapse' data-parent='#accordion' href='#accs_dspl_"+datos[i]['pnt_id']+"' aria-expanded='false' aria-controls='accs_dspl_"+datos[i]['pnt_id']+"'>"+datos[i]['pnt_nombre_mostrar']+"</a></h6></div><div id='accs_dspl_"+datos[i]['pnt_id']+"' class='panel-collapse collapse' role='tabpanel' aria-labelledby='headingOne'><div class='panel-body'></div></div></div>";
			}
			html = html + "</div>";
			$('#cnt_pntlls_ccs').html(html);
		}else{
			$('#cnt_pntlls_ccs').html('<div class="well well-sm">No existen registros para el nivel de seguridad.</div>');
		}
	});
}

function fn_verAccesos(pnt_id){
	var nivel = $('#prf_nvl_seg').val();
	var prf_id = $("#hdn_prf_id").val();
	var html = '<ul class="list-group">';
	send = {"verAccesosPerfil": 1};
	send.resultado = 1;
	send.prf_id = prf_id;
	send.acc_nivel = nivel;
	send.pnt_id = pnt_id;
	send.std_id = 0;
	$.getJSON("../adminPerfil/config_perfil.php", send, function(datos){
		if(datos.str > 0){
			for(i=0;i<datos.str;i++){
					html = html + '<li class="list-group-item">'+datos[i]['acc_descripcion']+' ('+datos[i]['acc_nivel']+')</li>';
			}
			html = html + '</ul>';
			$('#accs_dspl_'+pnt_id).html(html);
		}else{
			$('#accs_dspl_'+pnt_id).html('<ul class="list-group"><li class="list-group-item">No existe acceso para el nivel de seguridad.</li></ul>');
		}
	});
}

function fn_duplicarPerfil()
{
   
	$("#opciones_estado label").removeClass("active");
	$("#opt_Activos").addClass("active");
	$("#ptns_std_prfl").prop("checked", false);
	$("#pcn_prf_ctv").prop("checked", true);
	var html = '<thead><tr class="active"><th class="text-center">Descripci&oacute;n</th><th class="text-center">Nivel</th><th class="text-center">Acceso</th><th class="text-center">Es Cajero</th><th class="text-center">Activo</th></tr></thead>';
	var usr_id = $("#sess_usr_id").val();
	var std_id = 0;
	var prf_id = $("#hdn_prf_id").val();
	if(prf_id){
		send = {"actualizarPerfil": 1};
		send.accion = 2;
		send.prf_id = prf_id;
		send.prf_descripcion = 0;
		send.prf_nivel = 0;
		send.prf_acceso = 0;
		send.std_id = 0;
		send.usr_id = usr_id;
                send.vEsCajero =lc_cajeroGlobal;
		$.getJSON("../adminPerfil/config_perfil.php", send, function(datos){
			if(datos.str > 0){
				for(i=0;i<datos.str;i++){
					html = html + '<tr id="prfl_'+datos[i]['prf_id']+'" onclick="fn_seleccionarPerfil(\''+datos[i]['prf_id']+'\', \''+datos[i]['esCajero']+'\')" ondblclick="fn_cargarPerfil(\''+datos[i]['prf_id']+'\', \''+datos[i]['prf_descripcion']+'\', \''+datos[i]['std_id']+'\','+datos[i]['prf_nivel']+', \''+datos[i]['prf_acceso']+'\', \''+datos[i]['esCajero']+'\')"><td>'+datos[i]['prf_descripcion']+'</td><td class="text-center">'+datos[i]['prf_nivel']+'</td><td class="text-center">'+datos[i]['prf_acceso']+'</td><td class="text-center">'+datos[i]['esCajero']+'</td><td class="text-center">';
					if(datos[i]['std_id'] == 'Activo'){
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
				alertify.success("<h5>Registro actualizado correctamente</h5>");
			}else{
				html = '<tr><th colspan="3">No Existen Registros.</th></tr>';
				$('#tbl_lst_prfls').html(html);
			}
		});
	}else{
		alertify.error("Debe Seleccionar un perfil");
	}
}

function fn_agregar(){
	$('#cnt_pntlls_ccs').html('<ul class="list-group"><li class="list-group-item">No existe pantallas asignadas al nivel de seguridad.</li></ul>');
	fn_agregarPerfil();
	$("#select_acceso").val(0);	
}

function fn_agregarPerfil(){
	$("#tqt_mdl_prfl").html("Nuevo Perfil");
	$("#prfl_std_id").prop("checked", true);
	$("#prf_descripcion").val("");
	$("#prf_nvl_seg").val(0);
	$("#hdn_prf_id").val(0);
	$("#select_acceso").val(0);	
	$("#btn_pcn_prfl").html('<button type="button" class="btn btn-primary" onclick="fn_actualizarPerfil(1)">Aceptar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');
	$('#mdl_prfl').modal('show');
}

function fn_actualizarPerfil(accion)
{           
        //alert(accion);
	$("#opciones_estado label").removeClass("active");
	$("#opt_Activos").addClass("active");
	$("#ptns_std_prfl").prop("checked", false);
	$("#pcn_prf_ctv").prop("checked", true);
	
	if($("#prf_descripcion").val()==''){
		alertify.error("Debe ingresar una Descripci&oacute;n para el perfil")
		$('#prf_descripcion').focus();
		return false;		
	}
	if($("#select_acceso").val()==0){
		alertify.error("Debe seleccionar el Nivel de Acceso")
		return false;		
	}
	if($("#select_acceso").val()== null){
		alertify.error("El campo Nivel de Acceso no puede estar vacio");
		return false;
	}
	
	var html = '<thead><tr class="active"><th class="text-center">Descripci&oacute;n</th><th class="text-center">Nivel</th><th class="text-center">Acceso</th><th class="text-center">Es Cajero</th><th class="text-center">Activo</th></tr></thead>';
	var usr_id = $("#sess_usr_id").val();
	var std_id = 0;
	if($('#prfl_std_id').is(':checked')){
		std_id = 'Activo';
	}else{
		std_id = 'Inactivo';
	}
	var prf_id = $("#hdn_prf_id").val();
	var prf_descripcion = $("#prf_descripcion").val();
	var prf_nivel = $("#prf_nvl_seg").val();
	var prf_acceso = $("#select_acceso option:selected").val();
	if(fn_validarCampoVacio(prf_nivel)){
		send = {"actualizarPerfil": 1};
		send.accion = accion;
		send.prf_id = prf_id;
		send.prf_descripcion = prf_descripcion;
		send.prf_nivel = prf_nivel;
		send.prf_acceso = prf_acceso;
		send.std_id = std_id;
		send.usr_id = usr_id;
                //if(accion==1)
                //{
                    if(($("#sel_seleccione").bootstrapSwitch('state'))==true)
                    {
                       send.vEsCajero=1
                    }
                    else
                    {
                        send.vEsCajero=0;
                    }
                //}
		$.getJSON("../adminPerfil/config_perfil.php", send, function(datos){
			if(datos.str > 0){
				for(i=0;i<datos.str;i++){
					html = html + '<tr id="prfl_'+datos[i]['prf_id']+'" onclick="fn_seleccionarPerfil(\''+datos[i]['prf_id']+'\', \''+datos[i]['esCajero']+'\')" ondblclick="fn_cargarPerfil(\''+datos[i]['prf_id']+'\', \''+datos[i]['prf_descripcion']+'\', \''+datos[i]['std_id']+'\', '+datos[i]['prf_nivel']+', \''+datos[i]['prf_acceso']+'\', \''+datos[i]['esCajero']+'\')"><td>'+datos[i]['prf_descripcion']+'</td><td class="text-center">'+datos[i]['prf_nivel']+'</td><td class="text-center">'+datos[i]['prf_acceso']+'</td><td class="text-center">'+datos[i]['esCajero']+'</td><td class="text-center">';
					if(datos[i]['std_id'] == 'Activo'){
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
				alertify.success("<h5>Registro actualizado correctamente</h5>");
			}else{
				html = '<tr><th colspan="3">No Existen Registros.</th></tr>';
				$('#tbl_lst_prfls').html(html);
			}
		});
	}else{
		alertify.error("Nivel de Seguridad Campo Obligatorio");
	}
}

function fn_cargarPerfil(prf_id, prf_descripcion, std_id, prf_nivel, prf_acceso,esCajero)
{
       
	$("#pcn_tp_dmnstrcn label").removeClass("active");
	//$("#tp_18").addClass("active");
	$('#pcn_tp_adm').prop('checked', true);
	$("#btn_pcn_prfl").html('<button type="button" class="btn btn-primary" onclick="fn_actualizarPerfil(0)">Aceptar</button><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');
	$("#tqt_mdl_prfl").html(prf_descripcion);
	$("#hdn_prf_id").val(prf_id);
	if(std_id == 'Activo'){
		$("#prfl_std_id").prop("checked", true);
	}else{
		$("#prfl_std_id").prop("checked", false);
	}
	
	$("#prf_descripcion").val(prf_descripcion);
        if(esCajero=='SI'){   $("#sel_seleccione").bootstrapSwitch('state',true) }		
                    else{$("#sel_seleccione").bootstrapSwitch('state',false)}            
	$("#prf_nvl_seg").val(prf_nivel);
	$("#select_acceso").val(prf_acceso);	
	
	fn_verAccesoPantallas();	
	$('#mdl_prfl').modal('show');
}

function fn_cargarPerfiles(resultado, std_id)
    {
	lc_banderaChecks=1;
	lc_estado=std_id;
	var html = '<thead><tr class="active"><th class="text-center">Descripci&oacute;n</th><th class="text-center">Nivel</th><th class="text-center">Acceso</th><th class="text-center">Es Cajero</th><th class="text-center">Activo</th></tr></thead>';
	send = {"administracionSeguridad": 1};
	send.accion = 'cargarPerfiles';
	send.resultado = resultado;
	send.codigo = 0;
	send.cdn_id = 0;
	send.std_id = std_id;
	$.getJSON("../adminPerfil/config_perfil.php", send, function(datos){
		if(datos.str > 0){
			for(i=0;i<datos.str;i++){
				html = html + '<tr id="prfl_'+datos[i]['prf_id']+'" onclick="fn_seleccionarPerfil(\''+datos[i]['prf_id']+'\', \''+datos[i]['esCajero']+'\')" ondblclick="fn_cargarPerfil(\''+datos[i]['prf_id']+'\', \''+datos[i]['prf_descripcion']+'\', \''+datos[i]['std_id']+'\', '+datos[i]['prf_nivel']+', \''+datos[i]['prf_acceso']+'\', \''+datos[i]['esCajero']+'\')"><td>'+datos[i]['prf_descripcion']+'</td><td class="text-center">'+datos[i]['prf_nivel']+'</td><td class="text-center">'+datos[i]['prf_acceso']+'</td><td class="text-center">'+datos[i]['esCajero']+'</td><td class="text-center">';
				if(datos[i]['std_id'] == 'Activo'){
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
			html += '<tr><th colspan="3">No Existen Registros.</th></tr>';
			$('#tbl_lst_prfls').html(html);
		}
	});
}

function fn_seleccionarPerfil(prfl_id,cajero)
{
	$("#tbl_lst_prfls tr").removeClass("success");
	$("#prfl_"+prfl_id).addClass("success");
	$("#hdn_prf_id").val(prfl_id);
        lc_cajeroGlobal=cajero;
}

function fn_seleccionPestana(opcion){
	if(opcion>0){
		$('#tqt_mdl').html("Lista de Perfiles");
		$('#opciones_estado').html('');
	}else{
		$('#tqt_mdl').html("Lista de Usuarios");
		$('#opciones_estado').html('<label id="opciones_1" class="btn btn-default btn-sm active"><input type="radio" value="Todos" autocomplete="off" name="ptns_std_prfl" onchange="fn_cargarUsuarios(2, 0)" checked="checked">Todos</label><label class="btn btn-default btn-sm"><input type="radio" value="Activos" autocomplete="off"  name="ptns_std_prfl" onchange="fn_cargarUsuarios(3, 13)">Activos</label><label class="btn btn-default btn-sm"><input type="radio" value="Inactivos" autocomplete="off"  name="ptns_std_prfl" onchange="fn_cargarUsuarios(3, 25)">Inactivos</label>');
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
			offset: {from: 'top', amount: 750},
            allow_dismiss: false
        });
    }, 100);
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
	var valor = $('#prf_nvl_seg').val();
	if(valor<0){
		valor = $('#prf_nvl_seg').val(0);
	}else if(valor>100){
		valor = $('#prf_nvl_seg').val(100);
	}
	fn_verAccesoPantallas();
}

function justNumbers(e){
	var keynum = window.event ? window.event.keyCode : e.which;
	if ((keynum == 8) || (keynum == 46))
	return true;
	 
	return /\d/.test(String.fromCharCode(keynum));
}