var factura = '';
var idTransaccion = 0;
var odp_id = '';
var mesa_id = '';
var dop_cuenta = 1;
var simbolo = '';

$(document).ready(function(){
	//Modal Confirmacion PayPhone
	$('#mdl_cntdr_payphone').css('display','none');
	$('#mdl_rsmn_payphone').css('display','none');
	
	factura = $('#factura').val();
	odp_id = $('#odp_id').val();
	mesa_id = $('#mesa_id').val();
	dop_cuenta = $('#dop_cuenta').val();
	simbolo = $('#simbolo').val();
	fn_cargando(0);
	// fn_cargarRegiones();
	// fn_cargarDatosFactura();
	// fn_alfaNumerico('#parametro');
	// fn_cargando(0);
	// $('#btn_cancelar_teclado').attr('onclick','fn_regresar()');
	// $('#btn_ok_teclado').attr('onclick','fn_CrearTransaccion()');
	// $('#parametro').val('');
 
});

function fn_cargarRegiones(){
	var html = '';
	send={"cargarRegiones": 1};
	$.ajax({async: false, type: "POST", url: "config_payphone.php", data: send, dataType: "json",
		success: function(datos){
			if(datos.str>0){
				for(i=0;i<datos.str;i++){
					html += '<option value="'+datos[i]['RegionPrefixNumber']+'">'+datos[i]['Name']+'</option>';
				}
				$('#rgnPayphone').html(html);
				fn_cargando(0);
			}
		},
		error: function (xhr, ajaxOptions, thrownError) {
        	var message = xhr.responseText;
        	message = JSON.parse(message);
        	fn_cargando(0);
			alertify.alert(message['Message'], function(){fn_regresar()});
		}
	});
}

function fn_CrearTransaccion(){
	fn_cargando(1);
	var parametro = $('#parametro').val();
	var typeCharge = $("input[name=tipoNickName]:checked").val();
	if(parametro.length>8){
		send={"crearTransaccion": 1};
		send.typeCharge = typeCharge;
		send.regionCode = $('#rgnPayphone').val();
		send.parametro = parametro;
		send.factura = factura;
		$.ajax({async: false, type: "POST", url: "config_payphone.php", data: send, dataType: "json",
			success: function(datos){
				fn_cargando(0);
				var date = datos.Date;
				date = date.substring(0, 10)+' '+date.substring(11,16);
				$('#outCliente').html(datos.Name+' '+datos.LastName);
				$('#outFecha').html(date);
				idTransaccion = datos.TransactionId;
				$('#outTotal').html(simbolo+''+datos.Total);
				$('#btn_confirmar').attr({onclick: 'fn_confirmarTransaccion();'});
				$('#btn_cancelar').attr({onclick: 'fn_regresar();'});
				$('#mdl_cntdr_payphone').css('display','block');
				$('#mdl_rsmn_payphone').css('display','block');
			},
			error: function (xhr, ajaxOptions, thrownError) {
				var message = xhr.responseText;
				message = JSON.parse(message);
				fn_cargando(0);
				alertify.alert(message['Message']);
			}
		});
	}else{
		var descripcion = "";
		if(typeCharge==0){
			descripcion = 'Celular';
		}else{
			descripcion = 'Identificaci&oacute;n';
		}
		fn_cargando(0);
		alertify.alert('Por favor, Ingrese un n&uacute;mero de '+descripcion+" v&aacute;lido.");
	}
}

function fn_confirmarTransaccion(){
	fn_cargando(1);
	send={"consultarEstadoTransaccion": 1};
	send.TransactionId = idTransaccion;
	$.ajax({async: false, type: "POST", url: "config_payphone.php", data: send, dataType: "json",
		success: function(datos){
			fn_cargando(0);
			if(datos.Status==1){
				$('#btn_confirmar').attr({onclick: 'fn_verificarTransaccion();'});
				$('#btn_cancelar').attr({onclick: 'fn_cancelarTransaccion();'});
				alertify.alert('Desea Probar nuevamente, '+ datos.Message+'?');
			}else if(datos.Status==2){
				alertify.alert("Transaccion Cancelada", function(){fn_regresar()});
			}else if(datos.Status==3){
				alertify.alert("Transaccion Aceptada.", function(){fn_regresar()});
			}
		},
		error: function (xhr, ajaxOptions, thrownError) {
        	var message = xhr.responseText;
        	message = JSON.parse(message);
        	fn_cargando(0);
        	alertify.alert(message['Message']);
		}
	});
}

function fn_verificarTransaccion(){
	fn_cargando(1);
	send={"verificarTransaccion": 1};
	send.TransactionId = idTransaccion;
	$.ajax({async: false, type: "POST", url: "config_payphone.php", data: send, dataType: "json",
		success: function(datos){
			fn_cargando(0);
			if(datos.Status==1){
				alertify.alert('Desea Probar nuevamente, '+ datos.Message+'?');
				$('#btn_confirmar').attr({onclick: 'fn_confirmarTransaccion('+id+');'});
				$('#btn_cancelar').attr({onclick: 'fn_cancelarTransaccion();'});
			}else if(datos.Status==2){
				alertify.alert("Transaccion Cancelada", function(){fn_regresar()});
			}else if(datos.Status==3){
				alertify.alert("Transaccion Aceptada", function(){fn_regresar()});
			}
		},
		error: function (xhr, ajaxOptions, thrownError) {
        	var message = xhr.responseText;
        	message = JSON.parse(message);
        	fn_cargando(0);
        	alertify.alert(message['Message'], function(){fn_regresar()});
		}
	});
}

function fn_cancelarTransaccion(){
	fn_cargando(1);
	send={"cancelarTransaccion": 1};
	send.TransactionId = idTransaccion;
	$.ajax({async: false, type: "POST", url: "config_payphone.php", data: send, dataType: "json",
		success: function(datos){
			fn_cargando(0);
			alertify.alert("Transaccion Cancelada.");
			fn_regresar();
		},
		error: function (xhr, ajaxOptions, thrownError) {
        	var message = xhr.responseText;
        	message = JSON.parse(message);
        	fn_cargando(0);
			alertify.alert(message['Message']);
		}
	});
}

function fn_regresar(){
	$('#cntFormulario').html('<form action="../facturacion/factura.php" name="formulario" method="post" style="display:none;"><input type="text" name="odp_id" value="'+odp_id+'" /><input type="text" name="dop_cuenta" value="'+dop_cuenta+'" /><input type="text" name="mesa_id" value="'+mesa_id+'" /></form>');
	document.forms['formulario'].submit();
}

function fn_cargarDatosFactura(){
	send={"cargarDatosFactura": 1};
	send.factura = factura;
	$.ajax({async: false, type: "POST", url: "config_payphone.php", data: send, dataType: "json",
		success: function(datos){
			$('#sbttl_trnsccn').html(simbolo+''+datos.SubTotal);
			$('#ttl_trnsccn').html(simbolo+''+datos.Total);
		}
	});
}

function fn_cargando(std){if(std>0){$("#mdl_rdn_pdd_crgnd").show();}else{$("#mdl_rdn_pdd_crgnd").hide();}}