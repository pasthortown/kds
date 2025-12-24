///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla denominacion de billetes /////////////////////////
///////TABLAS INVOLUCRADAS: Billete_Denominacion, /////////////////////////////
///////FECHA CREACION: 20-02-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 04/06/2015 //////////////////////////////////
///////USUARIO QUE MODIFICO: Daniel Llerena ///////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Se incluyo la denominación de moneda , ////////
///////campo billete o moneda y se adapta alos nuevos estilos con Modales /////
///////////////////////////////////////////////////////////////////////////////

var pnt_Id = 0;
var Cod_btd_id = 1;
var Accion = 0;
var Descripcion = '';
var Valor = 0;
var Estado = 0;
//lc_std=-1; //almacena el estado de la solicitud seleccionada

$(document).ready(function(){
						   
	fn_esconderDivArea();
	fn_cargarDenominacionesBilletesInactivos(23);
	
});


function fn_OpcionSeleccionadab(ls_opcion)
{
	if (ls_opcion == 'Todos') 
		{
			fn_cargarDenominacionesBilletes();
		}else if(ls_opcion == 'Activos'){
			accion = 23;
			//alert(resultado)
			fn_cargarDenominacionesBilletesInactivos(accion);
			
		}else if(ls_opcion == 'Inactivos'){
			accion = 24;
			//alert(resultado)
			fn_cargarDenominacionesBilletesInactivos(accion);
		}
}

function fn_OpcionSeleccionadaModInsertb()
{
		
		var ls_opcion = '';
		ls_opcion = $(":input[name=estadosb]:checked").val();
		
		if (ls_opcion == 'Todos') 
		{
			fn_cargarDenominacionesBilletes();
		}else if(ls_opcion == 'Activos'){
			accion = 23;
			//alert(resultado)
			fn_cargarDenominacionesBilletesInactivos(accion);
			
		}else if(ls_opcion == 'Inactivos'){
			accion = 24;
			//alert(resultado)
			fn_cargarDenominacionesBilletesInactivos(accion);
		}
}

function fn_cargarDenominacionesBilletes(){
	accion=3;
	Cod_btd_id = 0;
	var cadena = $('#cadenas').val();
	send={"cargarDenominacionesBilletes": 1};
	send.cadena=cadena;
	send.accion = accion;
	$.getJSON("../adminFormasPago/config_denominacion.php",send,function(datos) {
		if(datos.str>0){
			$("#tabla_denominacion_billetes").html("<thead><tr class='active'><th class='text-center'>Descripci&oacute;n</th><th class='text-center'>Valor</th><th class='text-center'>Tipo</th><th class='text-center'>Activo</th></tr></thead>");
			for(i = 0; i < datos.str; i++){
				cadena = cadena + "<tr id='"+i+"' onclick='fn_seleccionarb("+i+")' ondblclick='fn_seleccionb("+i+",\""+datos[i]['btd_id']+"\",\""+datos[i]['btd_Descripcion']+"\","+datos[i]['btd_Valor']+",\""+datos[i]['btd_Tipo']+"\",\""+datos[i]['Simbolo']+"\",\""+datos[i]['std_id']+"\")'><td align='center'>"+datos[i]['btd_Descripcion']+"</td><td align='center'>"+datos[i]['Simbolo']+datos[i]['btd_Valor']+"</td><td align='center'>"+datos[i]['btd_Tipo']+"</td>";
				
				if(datos[i]['std_id'] == 'Activo'){
					cadena = cadena + "<td align='center'><input type='checkbox' checked='checked' disabled/></td></tr>";
				}else{
					cadena = cadena + "<td align='center'><input type='checkbox'  disabled/></td></tr>";					
				     }
			 }
			$("#tabla_denominacion_billetes").append(cadena);
			$("#tabla_denominacion_billetes").dataTable(
								  {
									  'destroy': true
								  }
								  );
			$("#tabla_denominacion_billetes_length").hide();
			$("#tabla_denominacion_billetes_paginate").addClass('col-xs-10');
			$("#tabla_denominacion_billetes_info").addClass('col-xs-10');
			$("#tabla_denominacion_billetes_length").addClass('col-xs-6');
		}else{
			alertify.error("No existen datos para esta cadena.");
			}
	});
}

function fn_cargarDenominacionesBilletesInactivos(accion){
	Cod_btd_id = 0;
	//alert(accion);
	var cadena = $('#cadenas').val();
	send={"cargarDenominacionesBilletes": 1}; 
	send.cadena=cadena;
	send.accion = accion;
	$.getJSON("../adminFormasPago/config_denominacion.php",send,function(datos) { 
		
			$("#tabla_denominacion_billetes").html("<thead><tr class='active'><th class='text-center'>Descripci&oacute;n</th><th class='text-center'>Valor</th><th class='text-center'>Tipo</th><th class='text-center'>Activo</th></tr></thead>");
			if(datos.str>0){
			for(i = 0; i < datos.str; i++){
				cadena = cadena + "<tr id='"+i+"' onclick='fn_seleccionarb("+i+")' ondblclick='fn_seleccionb("+i+",\""+datos[i]['btd_id']+"\",\""+datos[i]['btd_Descripcion']+"\","+datos[i]['btd_Valor']+",\""+datos[i]['btd_Tipo']+"\",\""+datos[i]['Simbolo']+"\",\""+datos[i]['std_id']+"\")'><td align='center'>"+datos[i]['btd_Descripcion']+"</td><td align='center'>"+datos[i]['Simbolo']+datos[i]['btd_Valor']+"</td><td align='center'>"+datos[i]['btd_Tipo']+"</td>";
				if(datos[i]['std_id'] == 'Activo'){
					cadena = cadena + "<td align='center'><input type='checkbox' checked='checked' disabled/></td></tr>";
				}else{
					cadena = cadena + "<td align='center'><input type='checkbox'  disabled/></td></tr>";					
				     }
			 }
			$("#tabla_denominacion_billetes").append(cadena);
			$("#tabla_denominacion_billetes").dataTable(
								  {
									  'destroy': true
								  }
								  );
			$("#tabla_denominacion_billetes_length").hide();
			$("#tabla_denominacion_billetes_paginate").addClass('col-xs-10');
			$("#tabla_denominacion_billetes_info").addClass('col-xs-10');
			$("#tabla_denominacion_billetes_length").addClass('col-xs-6');
		}else{
			cadena = cadena + "<tr><td>No existen datos</td></tr>";
			$("#tabla_denominacion_billetes").append(cadena);
			//$("#tabla_denominacion_billetes").empty();
			//alertify.error("No existen datos.");
			}
	});
}


function fn_cargarDenominacionModificar(codigo, btd_Valor, btd_Descripcion, btd_Tipo, simbolo, std_id){
	Accion = 2;

	if(std_id=='Activo')
	{
		 $("#check_activo").prop('checked',true);
	}
	else			
	{
		 $("#check_activo").prop('checked',false);
	}
	$("#myModalLabel").empty();
	$("#myModalLabel").append(btd_Descripcion);
	$('#btd_val').val(btd_Valor);
	$('#btd_des').val(btd_Descripcion);
	tipo=btd_Tipo.trim();
	$('#btd_tipo').val(tipo);	
	$('#simbolo').text(simbolo);	
}

function fn_modificarDenominacion(codigo, nombre, valor, tipo, estado){
	var cadena = $('#cadenas').val();
	send={"modificarDenominacionesBilletes":1};
	send.accion = Accion;	
	send.btd_id = codigo;
	send.btd_Descripcion = nombre;
	send.btd_Valor = valor;
	send.btd_Tipo = tipo;
	send.std_id = estado;
	if($("#check_activo").is(':checked')) 
	{
	send.std_id='Activo';
	}
	else
	{
	send.std_id='Inactivo';	
	}
	$.ajax({async:false, type:"GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url:"../adminFormasPago/config_denominacion.php", data:send,success:
		function(datos){
			if(datos.str>0){
				Accion = 0;
				alertify.success("Datos actualizados correctamente");
				fn_OpcionSeleccionadaModInsertb();	
						   }
			   		   }
		 });
}

function fn_agregarDenominacion(nombre, valor, tipo, estado){
	var cadena = $('#cadenas').val();
	send={"agregarDenominacionesBilletes":1};	
	send.accion = Accion;	
	send.btd_Descripcion = nombre;
	send.btd_Valor = valor;
	send.btd_Tipo = tipo;
	send.std_id = estado;
	if($("#check_activonuevo").is(':checked')) 
	{
	send.std_id=23;
	}
	else
	{
	send.std_id=24;	
	}
	$.ajax({async:false, type:"GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url:"../adminFormasPago/config_denominacion.php", data:send,success:
		function(datos){
			if(datos.str>0){
				
				Accion = 0;
				alertify.success("<h5>Datos Agregados Correctamente</h5>");
				fn_OpcionSeleccionadaModInsertb();		
						   }
					   }
	     });	
}

function fn_seleccionb(fila, btd_id, btd_Descripcion, btd_Valor, btd_Tipo, simbolo, std_id){	
	$('#modalmodificarb').modal('show')
	Cod_btd_id = btd_id;
	Descripcion = btd_Descripcion;
	Valor = btd_Valor;
	Tipo = btd_Tipo;
	Estado = std_id;	
	fn_cargarDenominacionModificar(Cod_btd_id, btd_Valor,btd_Descripcion, btd_Tipo, simbolo, std_id)
	fn_verificarSeleccionDenominacion();
	//fn_botonAgregar(0);
}

function fn_seleccionarb(fila){
 $("#tabla_denominacion_billetes tr").removeClass("success");
 $("#"+fila+"").addClass("success");
}


function fn_cancelar(){
	$("#tabla_denominacion_billetes tr").removeClass("seleccionado");
	fn_esconderDivArea();
	Cod_btd_id = 0;
	Accion = 0;
	Descripcion = '';
	Valor = 0;
	Estado = 0;	
}

function fn_guardardenominacionbilletes(){
	if(Accion == 1){		  
		var nombre = $("#btd_desnuevo").val();
		var val = $("#btd_valnuevo").val();
		var tip = $("#btd_tiponuevo").val();
		var est = $("#check_activonuevo option:checked").val();		
		if(nombre.length > 0 )
			if(val.length > 0 )
			{
				fn_agregarDenominacion(nombre, val, tip, est);
				$('#modalnuevo').modal('hide')				
			}
		else{
			alertify.error("Ingrese un Valor");				
		} 
		else{
			alertify.error("Ingrese una Descripcion");		
		} 
	}else if(Accion == 2){
		var codigo = Cod_btd_id;
		var nombre = $("#btd_des").val();
		var valor = $("#btd_val").val();
		var tipo = $("#btd_tipo option:selected").val();
		var estado = $("#btd_std option:selected").val();
		if(nombre.length > 0)
			if(valor.length > 0)
			{
				fn_modificarDenominacion(codigo, nombre, valor, tipo, estado);
				$('#modalmodificarb').modal('hide')				
			}
		else{
			alertify.error("Ingrese un Valor");			
		}
		else{
			alertify.error("Ingrese una Descripcion");	
		}
	}	
	fn_esconderDivArea();
	fn_activaBotonAgregar();
}

function fn_esconderDivArea(){
	$("#area_trabajo").hide();
	$("#administracion").hide();
}

function fn_activaBotonAgregar(){
	Accion = 9;
	}
function fn_desactivaBotonAgregar(){
	Accion = 6;
	}
function fn_agregar(){
	if(Accion == 9){
		Accion = 1;
	
		$('#modalnuevo').modal('show')
		  $('modalnuevo').on('shown', function() {
			$("#btd_desnuevo").focus();
		})
		
		fn_mostrarDivArea();	
		$("#btd_desnuevo").val("");
		$("#btd_valnuevo").val("");
	}
}

function fn_mostrarDivArea(){
	$("#area_trabajo").show();
}

function fn_verificarSeleccionDenominacion(){
	if(Cod_btd_id > 0){
		//fn_botonModificar(1);
	}else{
		//fn_botonModificar(0);
	}
}

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

//VALIDACION: PARA INGRESAR SOLO NUMEROS ENTEROS O DECIMALES CON X(.)
jQuery('.numbersOnly').keyup(function () { this.value = this.value.replace(/[^0-9-x\, ]/g,''); });

//VALIDACION: PARA INGRESAR SOLO NUMEROS ENTEROS O DECIMALES CON PUNTO(.)
 function NumCheck(e, field) {
    key = e.keyCode ? e.keyCode : e.which
    if (key == 8) return true
    if (key > 47 && key < 58) {
      if (field.value == "") return true
      regexp = /.[0-9]{5}$/ 
      return !(regexp.test(field.value))
    }
    if (key == 46) {
      if (field.value == "") return false
      regexp = /^[0-9]+$/
      return regexp.test(field.value)
    }
    return false
  }  
