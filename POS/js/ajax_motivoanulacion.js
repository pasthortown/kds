///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuraci�n formas de pago //////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 20-02-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

var Cod_Motivo_Anulacion = 0;
var Accion = 0;
var pnt_Id = 0;

$(document).ready(function(){
	fn_esconderDivArea();
	fn_cargarIdPantalla('Motivos Anulacion');
	fn_cargarMotivosAnulacion();
	fn_botonModificar(0);
	fn_botonCancelar(0);
	fn_botonGuardar(0);
	fn_botonAgregar(1);
	$('#mtv_descripcion').val("");
});

function fn_cargarMotivosAnulacion(){
	send = {"cargarMotivosAnulacion": 1};
	$("#motivos").html("<tr><th>#</th><th>Motivo</th><th>Estado</th></tr>");
	$.getJSON("config_motivoanulacion.php", send, function(datos) {
		if(datos.str>0){
			for(i=0; i<datos.str; i++) {
				if(datos[i]['std_id'] == 7){
					$("#motivos").append("<tr id='"+i+"' onclick='fn_seleccion("+i+","+datos[i]['mtv_id']+")'><td style='text-align: center;'>"+datos[i]['mtv_orden']+"</td><td>"+datos[i]['mtv_descripcion']+"</td><td style='text-align: center;'>Activo</td></tr>");
				}else{
					$("#motivos").append("<tr id='"+i+"' onclick='fn_seleccion("+i+","+datos[i]['mtv_id']+")'><td style='text-align: center;'>"+datos[i]['mtv_orden']+"</td><td>"+datos[i]['mtv_descripcion']+"</td><td style='text-align: center;'>Inactivo</td></tr>");
				}
			}
		}else{
			$("#motivos").append("<tr><td colspan='6'>No existen datos.</td></tr>");
		}
	});
}

function fn_cargarMotivoAnulacion(codigo){
	send = {"cargarMotivoAnulacion": 1};
	send.mtv_id = codigo;
	$.getJSON("config_motivoanulacion.php", send, function(datos) {
		if(datos.str>0){
			$('#mtv_descripcion').val(datos[0]['mtv_descripcion']);
			$('#std_id').val(datos[0]['std_id']);
		}else{
			$("#motivos").append("<tr><td colspan='6'>No existen datos.</td></tr>");
		}
	});
}

function fn_agregar(){
	Accion = 1;
	$('#mtv_descripcion').val("");
	fn_botonGuardar(1);
	fn_botonModificar(0);
	fn_botonAgregar(0);
	fn_mostrarDivArea();
	fn_botonCancelar(1);
}

function fn_guardar(){
	if(Accion > 0 && Accion < 2){
		var descripcion = $('#mtv_descripcion').val();
		var estado = $('#std_id option:selected').val();
		if(descripcion.length > 0){
			fn_agregarMotivoAnulacion(descripcion, estado);
		}else{
			alertify.alert("Ingrese una descripcion.");
		}
	}else if(Accion > 1 && Accion < 3){
		var codigo = Cod_Motivo_Anulacion;
		var descripcion = $('#mtv_descripcion').val();
		var estado = $('#std_id option:selected').val();
		if(descripcion.length > 0){
			fn_modificarMotivoAnulacion(codigo, descripcion, estado);
		}else{
			alertify.alert("Ingrese una descripcion.");
		}
	}
}

function fn_agregarMotivoAnulacion(descripcion, estado){
	send = {"agregarMotivoAnulacion": 1};
	send.mtv_descripcion = descripcion;
	send.std_id = estado;
	$("#motivos").html("<tr><th>#</th><th>Motivo</th><th>Estado</th></tr>");
	$.getJSON("config_motivoanulacion.php", send, function(datos) {
		if(datos.str>0){
			for(i=0; i<datos.str; i++) {
				if(datos[i]['std_id'] == 7){
					$("#motivos").append("<tr id='"+i+"' onclick='fn_seleccion("+i+","+datos[i]['mtv_id']+")'><td style='text-align: center;'>"+datos[i]['mtv_orden']+"</td><td>"+datos[i]['mtv_descripcion']+"</td><td style='text-align: center;'>Activo</td></tr>");
				}else{
					$("#motivos").append("<tr id='"+i+"' onclick='fn_seleccion("+i+","+datos[i]['mtv_id']+")'><td style='text-align: center;'>"+datos[i]['mtv_orden']+"</td><td>"+datos[i]['mtv_descripcion']+"</td><td style='text-align: center;'>Inactivo</td></tr>");
				}
			}
		}else{
			$("#motivos").append("<tr><td colspan='6'>No existen datos.</td></tr>");
		}
	});
	Cod_Motivo_Anulacion = 0;
	Accion = 0;
	fn_esconderDivArea();
	fn_botonModificar(0);
	fn_botonCancelar(0);
	fn_botonGuardar(0);
	fn_botonAgregar(1);
	$("#motivos tr").removeClass("seleccionado");
}

function fn_modificarMotivoAnulacion(codigo, descripcion, estado){
	send = {"modificarMotivoAnulacion": 1};
	send.mtv_id = codigo;
	send.mtv_descripcion = descripcion;
	send.std_id = estado;
	$("#motivos").html("<tr><th>#</th><th>Motivo</th><th>Estado</th></tr>");
	$.getJSON("config_motivoanulacion.php", send, function(datos) {
		if(datos.str>0){
			for(i=0; i<datos.str; i++) {
				if(datos[i]['std_id'] == 7){
					$("#motivos").append("<tr id='"+i+"' onclick='fn_seleccion("+i+","+datos[i]['mtv_id']+")'><td style='text-align: center;'>"+datos[i]['mtv_orden']+"</td><td>"+datos[i]['mtv_descripcion']+"</td><td style='text-align: center;'>Activo</td></tr>");
				}else{
					$("#motivos").append("<tr id='"+i+"' onclick='fn_seleccion("+i+","+datos[i]['mtv_id']+")'><td style='text-align: center;'>"+datos[i]['mtv_orden']+"</td><td>"+datos[i]['mtv_descripcion']+"</td><td style='text-align: center;'>Inactivo</td></tr>");
				}
			}
		}else{
			$("#motivos").append("<tr><td colspan='6'>No existen datos.</td></tr>");
		}
	});
	Cod_Motivo_Anulacion = 0;
	Accion = 0;
	fn_esconderDivArea();
	fn_botonModificar(0);
	fn_botonCancelar(0);
	fn_botonGuardar(0);
	fn_botonAgregar(1);
	$("#motivos tr").removeClass("seleccionado");
}

function fn_seleccion(fila, codigo){
	if(Accion == 0){
		Cod_Motivo_Anulacion = codigo;
		$("#motivos tr").removeClass("seleccionado");
		$("#"+fila+"").addClass("seleccionado");
		fn_botonModificar(1);
		fn_botonAgregar(0);
		fn_botonGuardar(0);
		fn_botonCancelar(1);
	}
}

function fn_cargarIdPantalla(pnt_nombre){
	send = {"cargaIdPantalla":1};
	send.pnt_nombre = pnt_nombre;
	$.getJSON("config_motivoanulacion.php",send,function(datos) {
		if(datos.str>0){
			pnt_Id = datos[0]['pnt_id'];
			fn_cargarPermisosPantalla();
		}
	});
}

function fn_cargarPermisosPantalla(){
	$("#btn_Modificar").hide();
	$("#btn_Agregar").hide();
	send={"cargarMenuPantalla":1};
	send.usr_id = $("#idUser").val();
	send.pnt_id = pnt_Id;
	$.getJSON("config_motivoanulacion.php",send,function(datos) {
		if(datos.str>0){
			for(i=0; i<datos.str; i++) {
				if(datos[i]['acc_descripcion'] == 'Modificar'){
					$("#btn_Modificar").show();
				}else if(datos[i]['acc_descripcion'] == 'Insertar'){
					$("#btn_Agregar").show();
				}else if(datos[i]['acc_descripcion'] == 'Todo'){
					$("#btn_Modificar").show();
					$("#btn_Agregar").show();
					break;
				}
			}
		}
	});
}

function fn_cancelar(){
	fn_esconderDivArea();
	Accion = 0;
	Cod_Forma_Pago = 0;
	$("#motivos tr").removeClass("seleccionado");
	fn_botonAgregar(1);
	fn_botonModificar(0);
	fn_botonCancelar(0);
	fn_botonGuardar(0);
}

function fn_modificar(){
	Accion = 2;
	fn_cargarMotivoAnulacion(Cod_Motivo_Anulacion);
	fn_botonGuardar(1);
	fn_botonModificar(0);
	fn_botonAgregar(0);
	fn_botonCancelar(1);
	fn_mostrarDivArea();
}

function fn_botonGuardar(estado){
	if(estado > 0){
		document.getElementById("btn_Guardar").style.backgroundImage="url('../../imagenes/botones/btnGuardar.png')";
		$("#btn_Guardar").removeAttr("disabled");
	}else{
		document.getElementById("btn_Guardar").style.backgroundImage="url('../../imagenes/botones/btnGuardarHide.png')";
		$("#btn_Guardar").prop('disabled', true);
	}
}

function fn_botonModificar(estado){
	if(estado > 0){
		document.getElementById("btn_Modificar").style.backgroundImage="url('../../imagenes/botones/btnModificar.png')";
		$("#btn_Modificar").removeAttr("disabled");
	}else{
		document.getElementById("btn_Modificar").style.backgroundImage="url('../../imagenes/botones/btnModificarHide.png')";
		$("#btn_Modificar").prop('disabled', true);
	}
}

function fn_botonAgregar(estado){
	if(estado > 0){
		document.getElementById("btn_Agregar").style.backgroundImage="url('../../imagenes/botones/btnAgregar.png')";
		$("#btn_Agregar").removeAttr("disabled");
	}else{
		document.getElementById("btn_Agregar").style.backgroundImage="url('../../imagenes/botones/btnAgregarHide.png')";
		$("#btn_Agregar").prop('disabled', true);
	}
}

function fn_botonCancelar(estado){
	if(estado > 0){
		document.getElementById("btn_Cancelar").style.backgroundImage="url('../../imagenes/botones/btnCancelar.png')";
		$("#btn_Cancelar").removeAttr("disabled");
	}else{
		document.getElementById("btn_Cancelar").style.backgroundImage="url('../../imagenes/botones/btnCancelarHide.png')";
		$("#btn_Cancelar").prop('disabled', true);
	}
}

function fn_esconderDivArea(){
	$("#area_trabajo").hide();
}

function fn_mostrarDivArea(){
	$("#area_trabajo").show();
}

function aMays(e, elemento){
	tecla = (document.all) ? e.keyCode : e.which; 
	elemento.value = elemento.value.toUpperCase();
}

/*


function fn_cargarFormasPago(){
	var cdn_id = $("#cadena").val();
	if(cdn_id != 0){
		fn_botonAgregar(1);
		send = {"cargarFormasPago": 1};
		send.cdn_id = cdn_id;
		$("#formas_pago").html("<tr><th>#</th><th>Descripci�n</th><th>Codigo</th><th>Tipo</th><th>Adquiriente</th><th>Estado</th></tr>");
		$.getJSON("config_formaspago.php", send, function(datos) {
			if(datos.str>0){
				for(i=0; i<datos.str; i++) {
					if(datos[i]['std_id']==16){
						$("#formas_pago").append("<tr id='"+i+"' onclick='fn_seleccion("+i+","+datos[i]['fmp_id']+")'><td style='text-align: center;'>"+datos[i]['fmp_orden']+"</td><td>"+datos[i]['fmp_descripcion']+"</td><td style='text-align: center;'>"+datos[i]['fpf_codigo']+"</td><td style='text-align: center;'>"+datos[i]['tfp_descripcion']+"</td><td style='text-align: center;'>"+datos[i]['rda_descripcion']+"</td><td style='text-align: center;'>Activo</td></tr>");
					}else{
						$("#formas_pago").append("<tr id='"+i+"' onclick='fn_seleccion("+i+","+datos[i]['fmp_id']+")'><td style='text-align: center;'>"+datos[i]['fmp_orden']+"</td><td>"+datos[i]['fmp_descripcion']+"</td><td style='text-align: center;'>"+datos[i]['fpf_codigo']+"</td><td style='text-align: center;'>"+datos[i]['tfp_descripcion']+"</td><td style='text-align: center;'>"+datos[i]['rda_descripcion']+"</td><td style='text-align: center;'>Inactivo</td></tr>");
					}
				}
			}else{
				$("#formas_pago").append("<tr><td colspan='6'>No existen datos para esta cadena.</td></tr>");
			}
		});
	}else{
		fn_botonAgregar(0);
		$("#formas_pago").html("");
		alertify.alert("Elija una cadena v�lida.");
	}
}

function fn_cargarTipoFormaPago(){
	var cadena = "";
	send = {"cargarTipoFormaPago": 1};
	$.getJSON("config_formaspago.php", send, function(datos) {
		if(datos.str>0){
			for(i=0; i<datos.str; i++) {
				cadena = cadena + "<option value='"+datos[i]['tfp_id']+"'>"+datos[i]['tfp_descripcion']+"</option>";
			}
		}
		$("#tfp_tipo").html(cadena);
	});
}

function fn_cargarTipoAdquiriente(){
	var cadena = "";
	send = {"cargarTipoAdquiriente": 1};
	cadena = "<option value='0'>Ninguno</option>";
	$.getJSON("config_formaspago.php", send, function(datos) {
		if(datos.str>0){
			for(i=0; i<datos.str; i++) {
				cadena = cadena + "<option value='"+datos[i]['rda_id']+"'>"+datos[i]['rda_descripcion']+"</option>";
			}
		}
		$("#rda_id").html(cadena);
	});
}

function fn_cargarFormaPago(codigo){
	send = {"cargarFormaPago": 1};
	send.fmp_id = codigo;
	$.getJSON("config_formaspago.php", send, function(datos) {
		if(datos.str>0){
			$('#fmp_descripcion').val(datos[0]['fmp_descripcion']);
			$('#fpf_codigo').val(datos[0]['fpf_codigo']);
			$('#tfp_tipo').val(datos[0]['tfp_id']);
			$('#fmp_std').val(datos[0]['std_id']);
			$('#rda_id').val(datos[0]['rda_id']);
		}
	});
}

function fn_modificar(){
	$("#cadena").attr('disabled', true);
	Accion = 2;
	fn_cargarFormaPago(Cod_Forma_Pago);
	fn_botonGuardar(1);
	fn_botonModificar(0);
	fn_botonAgregar(0);
	fn_botonCancelar(1);
	fn_mostrarDivArea();
}

function fn_agregar(){
	$("#cadena").attr('disabled', true);
	Accion = 1;
	$('#fmp_descripcion').val("");
	$('#fpf_codigo').val("");
	fn_botonGuardar(1);
	fn_botonModificar(0);
	fn_botonAgregar(0);
	fn_mostrarDivArea();
	fn_botonCancelar(1);
}

function fn_guardar(){
	if(Accion > 0 && Accion < 2){
		var nombre = $('#fmp_descripcion').val();
		var codigo = $('#fpf_codigo').val();
		var tipo = $('#tfp_tipo option:selected').val();
		var estado = $('#fmp_std option:selected').val();
		var adquiriente = $('#rda_id option:selected').val();
		var cadena = $("#cadena option:selected").val();
		if(nombre.length > 0){
			if(codigo.length > 0){
				fn_agregarFormaPago(nombre, codigo, tipo, adquiriente, cadena, estado);
			}else{
				alertify.alert("Ingrese un Valor para la Forma de Pago.");
			}
		}else{
			alertify.alert("Ingrese una Descripci�n para la Forma de Pago.");
		}
	}else if(Accion > 1 && Accion < 3){
		var fmp_id = Cod_Forma_Pago;
		var nombre = $('#fmp_descripcion').val();
		var codigo = $('#fpf_codigo').val();
		var tipo = $('#tfp_tipo option:selected').val();
		var estado = $('#fmp_std option:selected').val();
		var adquiriente = $('#rda_id option:selected').val();
		var cadena = $("#cadena option:selected").val();
		if(nombre.length > 0){
			if(codigo.length > 0){
				fn_modificarFormaPago(fmp_id, nombre, codigo, tipo, estado, adquiriente, cadena);
			}else{
				alertify.alert("Ingrese un Valor para la Forma de Pago.");
			}
		}else{
			alertify.alert("Ingrese una Descripci�n para la Forma de Pago.");
		}
	}
}

function fn_modificarFormaPago(id, nombre, codigo, tipo, estado, adquiriente, cadena){
	send = {"modificarFormasPago": 1};
	send.fmp_id = id;
	send.fmp_descripcion = nombre;
	send.fpf_codigo = codigo;
	send.std_id = estado;
	send.tfp_id = tipo;
	send.rda_id = adquiriente;
	send.cdn_id = cadena;
	$("#formas_pago").html("<tr><th>#</th><th>Descripci�n</th><th>Codigo</th><th>Tipo</th><th>Adquiriente</th><th>Estado</th></tr>");
	$.ajax({async:false, type:"POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url:"config_formaspago.php", data:send, success:
		function(datos){
//	$.getJSON("config_formaspago.php", send, function(datos) {
			if(datos.str>0){
				for(i=0; i<datos.str; i++) {
					if(datos[i]['std_id']==16){
						$("#formas_pago").append("<tr id='"+i+"' onclick='fn_seleccion("+i+","+datos[i]['fmp_id']+")'><td style='text-align: center;'>"+datos[i]['fmp_orden']+"</td><td>"+datos[i]['fmp_descripcion']+"</td><td style='text-align: center;'>"+datos[i]['fpf_codigo']+"</td><td style='text-align: center;'>"+datos[i]['tfp_descripcion']+"</td><td style='text-align: center;'>"+datos[i]['rda_descripcion']+"</td><td style='text-align: center;'>Activo</td></tr>");
					}else{
						$("#formas_pago").append("<tr id='"+i+"' onclick='fn_seleccion("+i+","+datos[i]['fmp_id']+")'><td style='text-align: center;'>"+datos[i]['fmp_orden']+"</td><td>"+datos[i]['fmp_descripcion']+"</td><td style='text-align: center;'>"+datos[i]['fpf_codigo']+"</td><td style='text-align: center;'>"+datos[i]['tfp_descripcion']+"</td><td style='text-align: center;'>"+datos[i]['rda_descripcion']+"</td><td style='text-align: center;'>Inactivo</td></tr>");
					}
				}
			}else{
				$("#formas_pago").append("<tr><td colspan='6'>No existen datos para esta cadena.</td></tr>");
			}
		}
	});
	Cod_Forma_Pago = 0;
	Accion = 0;
	fn_esconderDivArea();
	fn_botonModificar(0);
	fn_botonCancelar(0);
	fn_botonGuardar(0);
	fn_botonAgregar(1);
	$("#cadena").attr('disabled', false);
	$("#formas_pago tr").removeClass("seleccionado");
}

function fn_agregarFormaPago(nombre, codigo, tipo, adquiriente, cadena, estado){
	send = {"agregarFormasPago": 1};
	send.fmp_descripcion = nombre;
	send.fpf_codigo = codigo;
	send.std_id = estado;
	send.tfp_id = tipo;
	send.rda_id = adquiriente;
	send.cdn_id = cadena;
	$("#formas_pago").html("<tr><th>#</th><th>Descripci�n</th><th>Codigo</th><th>Tipo</th><th>Adquiriente</th><th>Estado</th></tr>");
	$.getJSON("config_formaspago.php", send, function(datos) {
		if(datos.str>0){
			for(i=0; i<datos.str; i++) {
				if(datos[i]['std_id']==16){
					$("#formas_pago").append("<tr id='"+i+"' onclick='fn_seleccion("+i+","+datos[i]['fmp_id']+")'><td style='text-align: center;'>"+datos[i]['fmp_orden']+"</td><td>"+datos[i]['fmp_descripcion']+"</td><td style='text-align: center;'>"+datos[i]['fpf_codigo']+"</td><td style='text-align: center;'>"+datos[i]['tfp_descripcion']+"</td><td style='text-align: center;'>"+datos[i]['rda_descripcion']+"</td><td style='text-align: center;'>Activo</td></tr>");
				}else{
					$("#formas_pago").append("<tr id='"+i+"' onclick='fn_seleccion("+i+","+datos[i]['fmp_id']+")'><td style='text-align: center;'>"+datos[i]['fmp_orden']+"</td><td>"+datos[i]['fmp_descripcion']+"</td><td style='text-align: center;'>"+datos[i]['fpf_codigo']+"</td><td style='text-align: center;'>"+datos[i]['tfp_descripcion']+"</td><td style='text-align: center;'>"+datos[i]['rda_descripcion']+"</td><td style='text-align: center;'>Inactivo</td></tr>");
				}
			}
		}else{
			$("#formas_pago").append("<tr><td colspan='6'>No existen datos para esta cadena.</td></tr>");
		}
	});
	Cod_Forma_Pago = 0;
	Accion = 0;
	fn_esconderDivArea();
	fn_botonModificar(0);
	fn_botonCancelar(0);
	fn_botonGuardar(0);
	fn_botonAgregar(1);
	$("#cadena").attr('disabled', false);
	$("#formas_pago tr").removeClass("seleccionado");
}

function fn_cancelar(){
	$("#cadena").attr('disabled', false);
	fn_esconderDivArea();
	Accion = 0;
	Cod_Forma_Pago = 0;
	$("#formas_pago tr").removeClass("seleccionado");
	fn_botonAgregar(1);
	fn_botonModificar(0);
	fn_botonCancelar(0);
	fn_botonGuardar(0);
}

function fn_cargarIdPantalla(pnt_nombre){
	send = {"cargaIdPantalla":1};
	send.pnt_nombre = pnt_nombre;
	$.getJSON("config_formaspago.php",send,function(datos) {
		if(datos.str>0){
			pnt_Id = datos[0]['pnt_id'];
			fn_cargarPermisosPantalla();
		}
	});
}

function fn_cargarPermisosPantalla(){
	$("#btn_Modificar").hide();
	$("#btn_Agregar").hide();
	send={"cargarMenuPantalla":1};
	send.usr_id = $("#idUser").val();
	send.pnt_id = pnt_Id;
	$.getJSON("config_formaspago.php",send,function(datos) {
		if(datos.str>0){
			for(i=0; i<datos.str; i++) {
				if(datos[i]['acc_descripcion']=='Modificar'){
					$("#btn_Modificar").show();
				}else if(datos[i]['acc_descripcion']=='Insertar'){
					$("#btn_Agregar").show();
				}
			}
		}
	});
}

function fn_seleccion(fila, codigo){
	if(Accion == 0){
		Cod_Forma_Pago = codigo;
		$("#formas_pago tr").removeClass("seleccionado");
		$("#"+fila+"").addClass("seleccionado");
		fn_botonModificar(1);
		fn_botonAgregar(0);
		fn_botonGuardar(0);
		fn_botonCancelar(1);
		fn_esconderDivArea();
	}
}

function fn_ceroRegistros(){
	document.getElementById("btn_Agregar").style.backgroundImage="url('../../imagenes/botones/btnAgregarHide.png')";
	document.getElementById("btn_Modificar").style.backgroundImage="url('../../imagenes/botones/btnModificarHide.png')";
	document.getElementById("btn_Guardar").style.backgroundImage="url('../../imagenes/botones/btnGuardarHide.png')";
	document.getElementById("btn_Cancelar").style.backgroundImage="url('../../imagenes/botones/btnCancelarHide.png')";
	
	$("#btn_Agregar").prop('disabled', true);
	$("#btn_Modificar").prop('disabled', true);
	$("#btn_Guardar").prop('disabled', true);
	$("#btn_Cancelar").prop('disabled', true);
}

function fn_botonGuardar(estado){
	if(estado > 0){
		document.getElementById("btn_Guardar").style.backgroundImage="url('../../imagenes/botones/btnGuardar.png')";
		$("#btn_Guardar").removeAttr("disabled");
	}else{
		document.getElementById("btn_Guardar").style.backgroundImage="url('../../imagenes/botones/btnGuardarHide.png')";
		$("#btn_Guardar").prop('disabled', true);
	}
}

function fn_botonModificar(estado){
	if(estado > 0){
		document.getElementById("btn_Modificar").style.backgroundImage="url('../../imagenes/botones/btnModificar.png')";
		$("#btn_Modificar").removeAttr("disabled");
	}else{
		document.getElementById("btn_Modificar").style.backgroundImage="url('../../imagenes/botones/btnModificarHide.png')";
		$("#btn_Modificar").prop('disabled', true);
	}
}

function fn_botonAgregar(estado){
	if(estado > 0){
		document.getElementById("btn_Agregar").style.backgroundImage="url('../../imagenes/botones/btnAgregar.png')";
		$("#btn_Agregar").removeAttr("disabled");
	}else{
		document.getElementById("btn_Agregar").style.backgroundImage="url('../../imagenes/botones/btnAgregarHide.png')";
		$("#btn_Agregar").prop('disabled', true);
	}
}

function fn_botonCancelar(estado){
	if(estado > 0){
		document.getElementById("btn_Cancelar").style.backgroundImage="url('../../imagenes/botones/btnCancelar.png')";
		$("#btn_Cancelar").removeAttr("disabled");
	}else{
		document.getElementById("btn_Cancelar").style.backgroundImage="url('../../imagenes/botones/btnCancelarHide.png')";
		$("#btn_Cancelar").prop('disabled', true);
	}
}

function fn_esconderDivArea(){
	$("#area_trabajo").hide();
}

function fn_mostrarDivArea(){
	$("#area_trabajo").show();
}

function fn_cargarCadenas(){
	send={"cargaCadena":1};
	$.getJSON("config_formaspago.php",send,function(datos) {
		if(datos.str>0){
			$("#cadena").html("");
			$('#cadena').html("<option selected value='0'>----Seleccione Cadena----</option>");
			for(i=0; i<datos.str; i++) {
				$("#cadena").append("<option value="+datos[i]['cdn_id']+">" +datos[i]['cdn_descripcion']+"</option>");
			}
			$("#cadena").val(0);
		}
	});
}

function aMays(e, elemento){
	tecla=(document.all) ? e.keyCode : e.which; 
	elemento.value = elemento.value.toUpperCase();
}
function fn_cargarPermisosPantalla(){
	$("#btn_Modificar").hide();
	$("#btn_Agregar").hide();
	send={"cargarMenuPantalla":1};
	send.usr_id = $("#idUser").val();
	send.pnt_id = pnt_Id;
	$.getJSON("config_formaspago.php",send,function(datos) {
		if(datos.str > 0){
			for(i = 0; i < datos.str; i++) {
				if(datos[i]['acc_descripcion']=='Modificar'){
					$("#btn_Modificar").show();
				}else if(datos[i]['acc_descripcion']=='Insertar'){
					$("#btn_Agregar").show();
				}else if(datos[i]['acc_descripcion']=='Todo'){
					$("#btn_Modificar").show();
					$("#btn_Agregar").show();
					break;
				}
			}
		}
	});
}
*/