///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuraci�n de descuentos ///////////////////
///////TABLAS INVOLUCRADAS: Descuentos ////////////////////////////////////////
///////FECHA CREACION: 23-04-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 22/05/2015///////////////////////////////////
///////USUARIO QUE MODIFICO: JOSE FERNANDEZ////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: LECTURA DE CADENA POR VARIABLE DE SESION//////
/////////////////////////////////BUSCADOR ON KEY PRESS Y ENTER/////////////////
////////////////////////////////Aplicacion Bootstrap///////////////////////////
///////FECHA ULTIMA MODIFICACION: 27/06/2015///////////////////////////////////
///////USUARIO QUE MODIFICO: Daniel Llerena////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Quitar filtros del menu, agregar radio radio /
///////button por clasificacion, agregar paginador y buscador, en la modal ////
///////agregar cantidad de producto, tipo plato, canal impresion tiempo ///////
///////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 04/01/2016 //////////////////////////////////
///////USUARIO QUE MODIFICO: Christian Pinto //////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Creacion de lista dinamica de Preguntas //////
/////// Sugeridas para el orden y buscador en Lista ///////////////////////////
///////////////////////////////////////////////////////////////////////////////

var grupos = 10;
var Accion = 0;
var Cod_Plu = 0;
var reportNumber=0;
var cantidadProducto=0;
var canalImpresion='';
var tipoPlato='';
var tiempo=0;
lc_paginas=-1;
lc_bandera=-1;
lc_banderaEstado=-1;
lc_banderaChecks=-1; //VARIABLE PARA LOS RADIO BUTTON
lc_estados='';        //VARIABLE DE LA CLASIFICACION DE PLUS 
lcbanderareceta=-1;
arrayImpresion = [];
orden_preguntas = 0;

//FUNCION SOLO NUMEROS
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

$(document).ready(function(){	
	
$("#mdl_rdn_pdd_crgnd").hide();	

$("#mdl_rdn_pdd_crgnd").show();	
	$("ol.sortable").sortable();
		
	$("#par_numplu").hide();
	$("#img_buscar").hide();
	$("#img_remove").hide();	
	$("#todos").prop("checked",true);
	lc_bandera=1;
	lc_banderaEstado=-1;
	fn_activarCombos(0);
	fn_cargarClasificacionPlu();
	fn_cargarUsuarioReceta();
	fn_cargarCategoriasPlu();	
	fn_cargarUbicacion();	
	fn_cargarPlus(0, grupos+1);	
	fn_cargarImpresora();
	fn_cargarImpuestos();
	fn_BuscarDescripcionPlu();	
	fn_AreaTrabajo(0);
	fn_ListaPreguntas(0);
	fn_btn('buscar', 0);
	fn_btn('modificar', 0);
	fn_btn('agregar', 0);
	fn_btn('guardar', 0);
	fn_btn('cancelar', 0);
	fn_btn('eliminar', 0);
	fn_btn('imprimir', 0);
	fn_btn('procesar', 1);
	
	$("#par_numplu").keypress(function() { 					
			if($("#par_numplu").val()==''){
					$("#img_buscar").show();	
					$("#img_remove").hide();
			}else{
					$("#img_remove").show();			
					$("#img_buscar").hide();
				 }
					fn_cargarPlus(0, grupos+1);	  			
	});
	
	$('#par_numplu').keyup(function(){
		if($('#par_numplu').val()!=''){
			fn_btn('cancelar', 1);
			$('#par_desc').val();
			Accion = 1;
			fn_btn('buscar', 1);
		}else{
			fn_btn('cancelar', 0);
			Accion = 0;
			fn_cargarPlus(0, grupos+1);
		}
	});
	
	$('#par_desc').keyup(function(){
		if($('#par_desc').val()!=''){
			fn_btn('cancelar', 1);
			$('#par_numplu').val();
			Accion = 2;
			fn_btn('buscar', 1);
		}else{
			fn_btn('cancelar', 0);
			Accion = 0;
			fn_cargarPlus(0, grupos+1);
		}
	});
	
	$("#par_numplu").keyup(function(event)
		{
			if(event.keyCode == '13')
			{				
				fn_buscar();
			}
	});
	
	$("#par_desc").keyup(function(event)
		{
			if(event.keyCode == '13')
			{				
				fn_buscar();
			}
	});
	
	$( "#par_numplu" ).keypress(function() {
		fn_buscar();
	});
	
	$( "#par_desc" ).keypress(function() {
		fn_buscar();
	});
	
	//GUARDA LA CONFIGIRACION DE ANULACION EN UN PRODUCTO EN LA BD
	$('#config_anuu').on('switchChange.bootstrapSwitch',function(){
		estado=$("#config_anuu").bootstrapSwitch('state');														 
		if(estado==true) 
		{
			var anulacion=1;
		}
		else
		{
			var anulacion=0;
		}		
			var plu_id = Cod_Plu;
			fn_modificarConfiguracionPlu('E', plu_id, anulacion);
	});
	
	//GUARDA CONFIGURACION DE GRAMOS DE UN PRODUCTO EN LA BD
	$('#config_gramoss').on('switchChange.bootstrapSwitch',function(){	
		estado=$("#config_gramoss").bootstrapSwitch('state');
		if(estado==true)
		{
			var gramos=1;
		}
		else
		{
			var gramos=0;
		}		
			var plu_id = Cod_Plu;
			fn_modificarConfiguracionPlu('F', plu_id, gramos);
	});	
	
	//GUARDA LA CONFIGURACION DEL QSR EN LA BD
	$('#config_qsrr').on('switchChange.bootstrapSwitch',function(){	
		estado=$("#config_qsrr").bootstrapSwitch('state');		
		if(estado==true) 
		{
			var qsr=1;
		}
		else
		{
			var qsr=0;
		}		
			var plu_id = Cod_Plu;
			fn_modificarConfiguracionPlu('G', plu_id, qsr);
	});
	
	//CONFIGURACION DEL STOK 
	$('#config_stock').on('switchChange.bootstrapSwitch',function(){	
		estado=$("#config_stock").bootstrapSwitch('state');		
		if(estado==true) 
		{
			var stock=1;
			$("#txt_cantidadproducto").prop('disabled', false);
			$("#txt_cantidadproducto").focus();
		}
		else
		{
			var stock=0;
			var plu_id = Cod_Plu;
			var cantidadproducto = 'null';
			$("#txt_cantidadproducto").prop('disabled', true);
			$("#txt_cantidadproducto").val('');
			fn_modificarConfiguracionPlu('J', plu_id, cantidadproducto);
		}		
	});
	
	//GUARDA LA CANTIDAD DE PRODUCTO EN LA BD
	$('#txt_cantidadproducto').change(function(){
		var cantidadproducto = $('#txt_cantidadproducto').val();
		if(cantidadproducto == '')
		{
			alertify.alert("Ingrese su Stock de Productos", function(){
			$('#txt_cantidadproducto').focus();});
			return false;
		}
			var plu_id = Cod_Plu;
			fn_modificarConfiguracionPlu('J', plu_id, cantidadproducto);
	});
	
	//GUARDA EL CODIGO DE BARRAS EN LA BD
	$('#cod_barras').change(function(){
		var codbarras = $('#cod_barras').val();
		var plu_id = Cod_Plu;
		fn_modificarConfiguracionPlu('H', plu_id, codbarras);
	});


    $('#estadoProducto').change(function () {
        if ($("#estadoProducto").is(':checked')) {
            estado = "Activo";
        } else {
            estado = "Inactivo";
        }
        var plu_id = $("#plu_id").val();
        send.usuario = $('#idUser').val();
        send.estado = estado;
        fn_modificarConfiguracionPlu('Z', plu_id, estado);
    });


    //GUARDA EL MASTER PLU ASOCIADO A OTRO PLU EN LA BD
	$('#cod_reportnumber').change(function(){
		var reportnumber = $('#cod_reportnumber').val();
		if(reportnumber=='')
		{			
			alertify.error("Ingrese un n&uacute;mero de ReportNumber");
			return false;
		}
				
		var plu_id = Cod_Plu;
		fn_modificarConfiguracionPlu('I', plu_id, reportnumber);
	});
	
	//GUARDA EL TIEMPO DE PREPARACION DE UN PRODUCTO EN LA BD
	$('#txt_timepreparacion').change(function(){
		var tiempopreparacion = $('#txt_timepreparacion').val();
		if(tiempopreparacion=='')
		{
			alertify.error("Ingrese el tiempo de preparaci�n del producto");
			return false;
		}
		var plu_id = Cod_Plu;
		fn_modificarConfiguracionPlu('K', plu_id, tiempopreparacion);
	});
	
	//GUARDA EL TIPO DE PLATO DE UN PRODUCTO(ENTRADA, PLATO FUERTE, ETC)
	$('#select_tipoplato').change(function(){
		var tipoplato = $('#select_tipoplato').val();
		if(tipoplato=='')
		{
			alertify.error("Seleccione un tipo de plato");
			return false;
		}
		var plu_id = Cod_Plu;		
		var cadena =  $('#cadenas').val();		
		fn_modificarConfiguracionPlu('L', plu_id, tipoplato);
		fn_guardaTipoPlato('I', plu_id, cadena, tipoplato);
		
	});
	
	//GUARDA EL NIVEL DE SEGURIDAD EN LA BD
	$('#selec_seguridad').change(function(){
		var nivelseguridad = $('#selec_seguridad').val();
		if(nivelseguridad=='')
		{
			alertify.error("Seleccione el Nivel de Seguridad");
			return false;
		}
		var plu_id = Cod_Plu;		
		fn_guardaNivelSeguridad('I', plu_id, nivelseguridad);
		
	});	

	//GUARDA EL TIPO DE PRODUCTO
	$('#selec_tipoproducto').change(function(){
		var tipoproducto = $('#selec_tipoproducto').val();
		if(tipoproducto=='')
		{
			alertify.error("Seleccione 	el tipo de producto");
			return false;
		}
		var plu_id = Cod_Plu;	
		fn_guardaTipoProducto('I', plu_id, tipoproducto);
		
	});
	
	$('#contenedor_configuraciones').on('shown.bs.modal', function () {   
		   $("#buscador").focus();				
	});	
	 	
});

function fn_limpiaBuscador()
{
	$("#par_numplu").val('');
	$("#img_remove").hide();
	$("#img_buscar").show();
}

//FUNCION PARA GUARDA LAS MODIFICACIONES DE PLU
function fn_modificarConfiguracionPlu(accion, plu_id, parametro){
	send = {"configuracionPlu": 1};
	send.Resultado = accion;
	send.plu_id = plu_id;
	send.parametro = parametro;
	send.usuario = $('#idUser').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		
	});	
}

//FUNCION PARA GUARDA TIPO DE PLATO DE PLU
function fn_guardaTipoPlato(opcion, plu_id, cdn_id, cdat_id){
	//opcion = 'I';	
	send = {"guardarPlatos": 1};
	send.opcion = opcion;
	send.plu_id = plu_id;
	send.cdn_id = $('#cadenas').val();
	send.cdat_id = $('#select_tipoplato').val();
	send.usuario = $('#idUser').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		
	});	
}

//FUNCION PARA GUARDAR NIVEL DE SEGURIDAD
function fn_guardaNivelSeguridad(opcion, plu_id, prf_id){
		
	send = {"guardarSeguridades": 1};
	send.opcion = opcion;
	send.plu_id = plu_id;	
	send.prf_id = $('#selec_seguridad').val();
	send.usuario = $('#idUser').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		
	});	
}

//FUNCION QUE SE CARGAR AL PRECIONAR EL BOTON ACEPTAR DE LA MODAL
function fn_limpiarCampos()
{	
	$("#config_anuu").prop("checked,false");
	$("#config_gramoss").prop("checked,false");
	$("#config_qsrr").prop("checked,false");
	$("#config_stock").prop("checked,false");
	$("#cod_barras").val('');	
}

function fn_verificarstock()
{
	
	var estadostock = $("#config_stock").bootstrapSwitch('state');
	var campo = $('#txt_cantidadproducto').val();
		if(estadostock==true && campo=='') 
		{			
			alertify.error("Ingrese su Stock de Productos");
            $('#contenedor_configuraciones').modal('show');
			$('#txt_cantidadproducto').focus();
			return false;
        } else {
			$.ajax({async: true});
			/*if($('#selec_canal_imp').val()==null)
			{
				alertify.error("Debe seleccionar por lo menos un canal de impresi&oacute;n");
				return false;
			}else{*/
            fn_guardaVariosCanalesImpresion();
			/*}*/
			
			if($('#selec_impuestos').val()==null)
			{
				alertify.error("Debe seleccionar por lo menos un Impuesto");
				return false;
			}else{
				fn_guardaImpuestos();		
			}

			$.ajax({async: false});
		}

}



/*funcion que guarda los canales de impresion*/
function fn_guardaVariosCanalesImpresion()
{	

	send = {"guardaVariosCanalesImpresion": 1};
	send.impIds = $('#selec_canal_imp').val();
	send.pluID=$("#plu_id").val();
	send.usuario = $('#idUser').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
        $('#contenedor_configuraciones').modal('hide');
                alertify.success("Datos guardados correctamente.");
	});
}

/*funcion que guarda los canales de impresion*/
function fn_guardaImpuestos()
{		
	send = {"guardaImpuestos": 1};
	send.impIds = $('#selec_impuestos').val();
	send.pluID=$("#plu_id").val();
	send.usuario = $('#idUser').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
        $('#contenedor_configuraciones').modal('hide');
        alertify.success("Datos guardados correctamente.");
	});
}

function fn_cargarMenus(){
	var html = "<option value='0'>-- Seleccione un Men&uacute; --</option>";
	send = {"cargarMenus": 1};
	send.cdn_id = $('#cadenas').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str>0){
			for(i=0;i<datos.str;i++){
				html = html + "<option value='"+datos[i]['menu_id']+"'>"+datos[i]['menu_Nombre']+"</option>";
			}
			$('#menus').html(html);
			$("#menus").attr('disabled', false);
			$("#categorias").attr('disabled', true);
			fn_activarCombos(0);					
			fn_AreaTrabajo(0);
			fn_ListaPreguntas(0);
		}else{
			fn_activarCombos(0);			
			fn_AreaTrabajo(0);
			fn_ListaPreguntas(0);
			$("#categorias").attr('disabled', true);
			$('#menus').html(html);
			alertify.alert('No tiene configurado ning&uacute;n men&uacute;.');
		}
	});
}

//FUNCION PARA CARGAR LAS IMPRESORAS SEGUN EL CADENA
function fn_cargarImpresora()
{   
	Accion = 0;	
	send = {"cargarImpresora": 1};
	send.cdn_id = $('#cadenas').val();
	send.Accion=Accion;
	send.magp_id = 0;
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str>0) {
			
			for(i=0; i<datos.str; i++)
                        {
                            //alert(datos[i]['idIntegracion']);
				$("#selec_canal_imp").append("<option value="+datos[i]['cimp_id']+">"+datos[i]['cimp_descripcion']+"</option>");					
				}				
			}
			
			$('.chosen-select').chosen();			
			$("#selec_canal_imp_chosen").css('width','480');			
	});	
}


//FUNCION PARA CARGAR LAS IMPUESTOS
function fn_cargarImpuestos(){
	
	Accion = 6;	
	send = {"cargarImpuestos": 1};
	send.cdn_id = $('#cadenas').val();
	send.Accion=Accion;
	send.magp_id = 0;
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str>0) {
			for(i=0; i<datos.str; i++){
				$("#selec_impuestos").append("<option value="+datos[i]['idIntegracion']+">"+datos[i]['nombreImpuesto']+"</option>");					
				
			}				
		}

		$('.chosen-select').chosen();			
		$("#selec_impuestos_chosen").css('width','480');			
	});	
}

//FUNCION PARA CARGAR EL COMBOBOX Y REALIZAR LA BUSQUEDA DE PLUS
function fn_BuscarDescripcionPlu()
{
	var html = "<option value='0'>-- Buscar Plu --</option>";
	Accion = 1;		
	send = {"buscadescripcionplu": 1};
	send.Accion = Accion;
	send.magp_id = 0;
	$.getJSON("../adminplus/config_plus.php", send, function(datos){		
			
			for(i=0; i<datos.str; i++) 
			{				
				html = html + "<option value='"+datos[i]['plu_num_plu']+"' name='"+datos[i]['plu_descripcion']+"'>"+datos[i]['plu_num_plu']+ "  -  " +datos[i]['plu_descripcion']+ "  -  " +datos[i]['cla_Nombre']+"</option>";				
							
			}	
				$('#cod_reportnumber').html(html);
                              $("#mdl_rdn_pdd_crgnd").hide();

				$("#cod_reportnumber").chosen();
				$("#cod_reportnumber_chosen").css('width','480');				
	});
}

//FUNCION PARA CARGAR LOS TIPOS DE PLATOS
function fn_cargarTipoPlato(plu_id){
	opcion = 'C';	
	lc_estado=0;
	send = {"cargarPlatos": 1};
	send.plu_id = plu_id;
	send.cdn_id = $('#cadenas').val();
	send.cdat_id = 0;
	send.opcion=opcion;
	send.usuario = $('#idUser').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str>0) {			
			
			$("#select_tipoplato").html("<option selected  value='0'>-----Seleccionar-----</option>");
			for(i=0; i<datos.str; i++){	
					
					if(datos[i]['ESTADO']==1){
						lc_estado = datos[i]['ID_ColeccionDeDatosPlus'];
					}
					
					$("#select_tipoplato").append("<option value="+datos[i]['ID_ColeccionDeDatosPlus']+">"+datos[i]['Descripcion']+"</option>");
				}				
			}
			$("#select_tipoplato").val(lc_estado);
	});	
}

//FUNCION PARA CARGAR LOS NIVELES DE SEGURIDAD
function fn_cargarNivelSeguridad(plu_id){
	opcion = 'C';	
	lc_estadoseg = 0;
	send = {"cargarSeguridades": 1};
	send.plu_id = plu_id;	
	send.prf_id = 0;
	send.opcion=opcion;
	send.usuario = $('#idUser').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str>0) {
						
			$("#selec_seguridad").html("<option selected  value='0'>-----Seleccionar-----</option>");
			for(i=0; i<datos.str; i++){	
					
					if(datos[i]['ESTADO']==1){
						lc_estadoseg = datos[i]['ID_ColeccionDeDatosPlus'];
					}
					
					$("#selec_seguridad").append("<option value="+datos[i]['ID_ColeccionDeDatosPlus']+">"+datos[i]['Descripcion']+"</option>");
				}				
			}
				$("#selec_seguridad").val(lc_estadoseg);
	});	
}

//FUNCION QUE CARGA LA CLASIFICIACION DE LOS PLUS EN UN RADIO BUTTON (SALON,DOMICILIO,DRIVE,LLEVAR,ETC.)
function fn_cargarClasificacionPlu()
{   
	Accion = 2;
	var html = '';
	html = html +"<label class='btn btn-default btn active' onClick='fn_cargarPlus(0,11);'><input id='todos' type='radio' name='check_todos' value='0'><h6>Todos</h6></label>";
	send={"cargarClasificacion":1};	
	send.cdn_id = $('#cadenas').val();
	send.Accion = Accion;
	send.magp_id = 0;
    $.ajax ({
		async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"../adminplus/config_plus.php",data:send,success:function(datos){
			if(datos.str>0) {	
			   for(i=0; i<datos.str; i++) 
			   {					
					html = html +"<label class='btn btn-default' onClick='fn_cargarPlusClasificacion(0,11,\""+datos[i]['cla_id']+"\");'><h6><input id='optionsClas' type='radio' name='options_checks' value='"+datos[i]['cla_id']+"'>"+datos[i]['cla_Nombre']+"</h6></label>";
				}				
					$("#selec_categoria").html(html);
                                     
			}
     	}
     });  
}

//FUNCION QUE CARGA LOCALES POR USUARIO
function fn_cargarUsuarioReceta()
{  		
	send={"cargarUsuarioRecetas":1};	
	send.cdn_id = $('#cadenas').val();
	send.usu_id = $('#idUser').val();	
    $.ajax ({ async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"../adminplus/config_plus.php",data:send,success:function(datos){																																											  
			if(datos.str>0) {				 
			   
			   $("#selec_localusuario").append("<option selected  value='0'>-----Seleccionar-----</option>");
			   for(i=0; i<datos.str; i++) 
			   {					
					$("#selec_localusuario").append("<option name="+datos[i]['rst_id']+" value="+datos[i]['rst_id']+">"+datos[i]['rst_cod_tienda']+" - "+datos[i]['rst_descripcion']+"</option>");
			   }			   
			}
     	}
     });  
}

//FUNCION QUE CARGA LAS CATEGORIAS
function fn_cargarCategoriasPlu()
{   	
	Accion = 3;		
	send={"cargarCategoria_Plus":1};	
	send.cdn_id = $('#cadenas').val();
	send.Accion = Accion;	
	send.magp_id = 0;
    $.ajax ({ async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"../adminplus/config_plus.php",data:send,success:function(datos){																																											  
			if(datos.str>0) {				 
			   			   
			   for(i=0; i<datos.str; i++) 
			   {					
					$("#selec_categorias2").append("<option name="+datos[i]['Cod_Categoria']+" value="+datos[i]['Cod_Categoria']+">"+datos[i]['Descripcion']+"</option>");
			   }		       
			  					
				}
			}
		 });  
}

//FUNCION QUE CARGA LAS UBICACIONES
//aqui estoy
function fn_cargarUbicacion()
{
	$.ajaxSetup({async: false});
	var opcioncategoria = $('#selec_categorias2').val();
	Accion = 4;
	var html = '';	
	send={"cargarUbicacion":1};	
	send.cdn_id = $('#cadenas').val();
	send.Accion = Accion;
	send.magp_id = 0;
    $.ajax ({ async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"../adminplus/config_plus.php",data:send,success:function(datos){																																											  
			if(datos.str>0) {	
			
			   for(i=0; i<datos.str; i++) 
			   {				   	
				   	if(i==0){
						html = html + "<label class='btn btn-default active' onChange='fn_setSelectRestaurante(); fn_cargarRecetasdetalle();' id='ubicacion_checks"+datos[i]['Cod_Ubicacion_Cadenas']+"'><h10><input type='radio' name='ubicacion' checked='checked' value='"+datos[i]['Cod_Ubicacion_Cadenas']+"'>"+datos[i]['Descripcion']+"</h10></label>";						
							}
					else{	
					html = html +"<label class='btn btn-default' onChange='fn_setSelectRestaurante(); fn_cargarRecetasdetalle();' id='ubicacion_checks"+datos[i]['Cod_Ubicacion_Cadenas']+"' ><h10><input type='radio'  name='ubicacion' value='"+datos[i]['Cod_Ubicacion_Cadenas']+"'>"+datos[i]['Descripcion']+"</h10></label>";						
						}		
				}		
					$("#selec_ubicacion").html(html);										
				}
			}
		 });
	$.ajaxSetup({async: true});
}

function fn_setSelectRestaurante(){
	$("#selec_localusuario").val(0);
}

//FUNCION PARA CARGAR EL LISTADO DE TODOS LOS PLUS
function fn_cargarRecetasdetalle()
{
  

	if(name=$("#selec_localusuario option:selected").val()==0){		
		opcion = 'A';		
	}else{
		opcion = 'B';		
	}
	
	var rest = $("#selec_localusuario option:selected").val();
	var Num_Plu =$('#hid_num_plu').val();
	var opcioncategoria = $('#selec_categorias2').val();
    var opcionubicacion = $(":input[name='ubicacion']:checked").val();
	var htmlcab = "<tr class='bg-primary'><th style='text-align:center'># Plu</th><th style='text-align:center'>Nombre del Plu</th><th style='text-align:center'>Departamento</th><th style='text-align:center'>Tipo</th><th style='text-align:center'>PVP</th><th style='text-align:center'>Precio Neto</th><th style='text-align:center'>Contribuci&oacute;n Neta</th><th style='text-align:center'>Contribuci&oacute;n Costo</th><th style='text-align:center'>Impuesto</th></tr>";
	var html = "<tr class='bg-primary'><th style='text-align:center;'># Item</th><th style='text-align:center'>Nombre Item</th><th style='text-align:center'>Unidad Receta</th><th style='text-align:center'>Cantidad</th><th style='text-align:center'>Costo Unidad</th><th style='text-align:center'>Costo Total</th><th style='text-align:center'>Costo %</th></tr>";
	send = {"cargarRecetas": 1};
	send.opcion = opcion;
	send.Num_Plu = Num_Plu;
	send.cdn_id = $('#cadenas').val();	
	send.categoria = opcioncategoria;
	send.ubicacion = opcionubicacion;
	send.rest = rest;
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str > 0){	
							 
				for(i=0;i<datos.str;i++)
				{
					//CARGA LOS DATOS DE CABECERA
					if(i==0){
						htmlcab = htmlcab + "<tr '("+datos[i]['Cod_Plu']+")'><td class='text-center'>"+datos[i]['Num_Plu']+"</td><td style='text-align: left;'>"+datos[i]['nombre_plu']+"</td><td class='text-center'>"+datos[i]['departamento']+"</td><td class='text-center'>"+datos[i]['clase']+"</td><td class='text-center'>"+datos[i]['pvp']+"</td><td class='text-center'>"+datos[i]['neto']+"</td><td class='text-center'>"+datos[i]['contribucion']+"</td><td class='text-center'>"+datos[i]['contribucion_costo']+"</td><td class='text-center'>"+datos[i]['impuesto']+"</td></tr>";
								$('#cabecera_subrecetas').html(htmlcab);
							}						
						
					var lc_categoria = datos[i]['cod_categoria'];					
					var lc_ubicacion = datos[i]['Cod_Ubicacion_Cadenas'];					
					var lc_costofinalpor = datos[i]['costo_final'];
					var lc_sumacostoporcentaje = datos[i]['Total_costo_porcentaje'];
						
					//CARGA LOS DATOS DEL DETALLE	
					html = html + "<tr><td>"+datos[i]['Cod_Art']+"</td><td>"+datos[i]['Nombre']+"</td><td class='text-center'>"+datos[i]['Unidad_Receta']+"</td><td class='text-center'>"+datos[i]['Cantidad']+"</td><td class='text-center'>"+datos[i]['CostoReceta']+"</td><td class='text-center'>"+datos[i]['costo_total']+"</td><td class='text-center'>"+datos[i]['costo_porcentaje']+"</td></tr>";
					 
				}
				//AGREGAR LOS TOTALES DEL COSTO
				html = html + "<tr><th colspan='5' class='text-right'><h4><b>Total:</b></h4></th><th class='text-center'><h4><b>"+lc_costofinalpor+"</b></h4></th><th class='text-center'><h4><b>"+lc_sumacostoporcentaje+"</b></h4></th></tr>";
					
				$('#recetas_subrecetas').html(html);
				 
				$('#selec_categorias2').val(lc_categoria);				
				
//				$(":input[name=ubicacion]:checked").val(lc_ubicacion);								
				$("#selec_ubicacion label").removeClass("active");					
				$("#ubicacion_checks"+lc_ubicacion).addClass("active");				
				 
		}else{	
				alertify.error("No existen Registros.");			   
			    $("#recetas_subrecetas").html(html);
                      
			 }
                       
                         

	});	
 
}

//FUNCION PARA CARGAR EL LISTADO DE TODOS LOS PLUS
function fn_cargarPlus(inicio, fin)
{	
    $("#mdl_rdn_pdd_crgnd").show();

	lc_banderaChecks = 1;
	Accion = 0;	
	var html = "<thead><tr class='active'><th style='text-align:center' width='10%'># Plu</th><th style='text-align:center' width='30%'>Nombre del Plu</th><th style='text-align:center;' width='30%'>Descripci&oacute;n Impresi&oacute;n</th><th style='text-align:center' width='20%'>Clasificaci&oacute;n</th><th style='text-align:center' width='10%'>Master Plu</th></tr></thead>";
	send = {"cargarPlus": 1};
	send.Accion = Accion;
	send.inicio = inicio;
	send.fin = fin;
	send.num_plu = 0;
	send.cdn_id = $('#cadenas').val();	
	send.filtro = $("#par_numplu").val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str > 0){
			
			$("#producto_plu").show();		
			for(i=0;i<datos.str;i++)
			{				
				html = html + "<tr id='"+i+"' onclick='fn_seleccionar("+i+");' ondblclick='fn_seleccionarPlus("+i+", "+datos[i]['plu_id']+",\""+datos[i]['magp_id']+"\","+datos[i]['Num_Plu']+")'><td class='text-center'>"+datos[i]['Num_Plu']+"</td><td style='text-align: left;'>"+datos[i]['plu_descripcion']+"</td><td style='text-align: left;'>"+datos[i]['det_Impresion']+"</td><td class='text-center'>"+datos[i]['cla_Nombre']+"</td><td class='text-center'>"+datos[i]['plu_reportnumber']+"</td></tr>";
			}
				$('#plus').html(html);
                                $("#mdl_rdn_pdd_crgnd").hide();

				$('#plus').dataTable(
				{ 
					'destroy': true   
				});
				
				$("#plus_length").hide();
			
		}else{	
				alertify.error("No existen Registros.");			   
			    $("#plus").html(html);
			 }
	});	
}
//
//FUNCION PARA CARGAR EL LISTADO DE LOS PLUS POR SU CLASIFICACION
function fn_cargarPlusClasificacion(inicio, fin, opcion)
{	
    
    $("#mdl_rdn_pdd_crgnd").show();

	lc_estados = opcion;
	lc_banderaChecks = 2;
	Accion = 0;	
	var html = "<thead><tr class='active'><th style='text-align:center' width='10%'># Plu</th><th style='text-align:center' width='30%'>Nombre del Plu</th><th style='text-align:center;' width='30%'>Descripci&oacute;n Impresi&oacute;n</th><th style='text-align:center' width='20%'>Clasificaci&oacute;n</th><th style='text-align:center' width='10%'>Master Plu</th></tr></thead>";
	send = {"cargarPlusXClasificacion": 1};
	send.Accion = Accion;
	send.inicio = inicio;
	send.fin = fin;
	send.cdn_id = $('#cadenas').val();	
	send.filtro = $("#par_numplu").val();
	send.opcion = opcion;
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str > 0){
			
			$("#producto_plu").show();		
			for(i=0;i<datos.str;i++)
			{
				html = html + "<tr id='"+i+"' onclick='fn_seleccionar("+i+");' ondblclick='fn_seleccionarPlus("+i+", "+datos[i]['plu_id']+",\""+datos[i]['magp_id']+"\","+datos[i]['Num_Plu']+")'><td class='text-center'>"+datos[i]['Num_Plu']+"</td><td style='text-align: left;'>"+datos[i]['plu_descripcion']+"</td><td style='text-align: left;'>"+datos[i]['det_Impresion']+"</td><td class='text-center'>"+datos[i]['cla_Nombre']+"</td><td class='text-center'>"+datos[i]['plu_reportnumber']+"</td></tr>";
			}
				$('#plus').html(html);
                                $("#mdl_rdn_pdd_crgnd").hide();

				$('#plus').dataTable(
				{ 
					'destroy': true   
				});
			   $("#plus_length").hide();		 
			
		}else{	
				alertify.error("No existen Registros.");			    
			    $("#plus").html(html);
			 }
	});	
}

function fn_buscarPlusNumPlu(inicio, fin){
	var html = "<tr class='active'><th class='text-center'>Num Plu</th><th class='text-center'>Descripci&oacute;n</th></tr>";
	send = {"cargarPlus": 1};
	send.Accion = Accion;
	send.inicio = inicio;
	send.fin = fin;
	send.mag_id = $('#categorias').val();
	send.num_plu = $('#par_numplu').val();
	send.plu_descripcion = "0";
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str > 0){
			var total_registros = datos[0]['Total'];
			if(total_registros > 0){
				fn_paginador(total_registros);
				for(i=0;i<datos.str;i++){
					html = html + "<tr id='"+i+"' onclick='fn_seleccionar("+i+");' ondblclick='fn_seleccionarPlus("+i+", "+datos[i]['plu_id']+","+datos[i]['plu_descripcion']+")'><td>"+datos[i]['Num_Plu']+"</td><td style='text-align: left;'>"+datos[i]['plu_descripcion']+"</td></tr>";
				}
				$('#plus').html(html);
			}
		}else{
			alertify.alert('No se encontraron Plus.');
		}
	});	
}

//FUNCION PARA CARGAR LAS PREGUNTAS EN LA MODAL
function fn_cargarPreguntasPlus(plu_id)
{
	//alert(plu_id);
	var html = "<thead><tr><td>Preguntas Agregadas</td></tr></thead>";
	var tabla_seleccionado = "<thead><tr class='bg-primary'><td align='center'><h5>Preguntas Asociadas al Producto</h5></td><td align='center' style='width:30px'><h5>Quitar</h5></td></tr></thead>";
	send = {"cargarPreguntasPlus": 1};
	send.plu_id = plu_id;
	send.usuario = $('#idUser').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){															
		if(datos.str > 0){			
			ul_seleccionado = '';
			for(i=0;i<datos.str;i++){
				html = html + "<tr><td style='text-align: left;'>" + datos[i]['pre_sug_descripcion']+"</td></tr>";			
			
				ul_seleccionado = ul_seleccionado + "<li class='ui-state-default' id='"+datos[i]['psug_id']+"'  style='height: 37px;' onmousedown='fn_actualizaOrdenPreguntaNueva("+plu_id+", \""+datos[i]['psug_id']+"\");'>" + datos[i]['pre_sug_descripcion']+"<input  type='button' align='right' name='opcione' class='opcionAgregado' onclick='fn_quitarPreguntaPlu("+plu_id+", \""+datos[i]['psug_id']+"\")' style='height: 33px; width: 33px; float:right'/></li>";
				//alert(datos[i]['pre_sug_descripcion']);
			}
			$('#lista_agregadas').html(html);
			$('#ul_sortable').html(ul_seleccionado);
			$('#preguntas_agregadas').html(tabla_seleccionado);
			$("input[name='opcione']").css("background","#666666 url('../../imagenes/admin_resources/btn_eliminar.png') 1px 1px no-repeat");			
			
		}else{
			tabla_seleccionado = tabla_seleccionado + "<tr><td colspan='2' style='text-align: center;'>No existen preguntas asignadas.</td></tr>";
			$('#preguntas_agregadas').html(tabla_seleccionado);
			html = html + "<tr><td colspan='2' style='text-align: center;'>No existen preguntas asignadas.</td></tr>";
			$('#lista_agregadas').html(html);
			$('#ul_sortable').empty();
		}
	});	
}


//FUNCION PARA QUITAR LAS PREGUNTAS AGREGADAS A UN PLU
function fn_quitarPreguntaPlu(plu_id, psug_id){
	var html = "<thead><tr class='bg-primary'><td align='center'><h5>Preguntas Asociadas al Producto</h5></td><td align='center' style='width:30px'><h5>Quitar</h5></td></tr></thead>";
	$('#buscador').val("");
	var cdn_id = $('#cadenas').val();
	send = {"quitarPreguntasPlus": 1};
	send.plu_id = plu_id;
	send.psug_id = psug_id;
	send.cdn_id = $('#cadenas').val();
	send.usuario = $('#idUser').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str > 0){
			$('#ul_sortable').empty();
			html_ul_seleccionado = '';
			for(i=0;i<datos.str;i++){
				//html = html + "<tr><td>" + datos[i]['pre_sug_descripcion']+"</td><td style='text-align: center;'><input type='button' name='opcione' class='opcionAgregado' onclick='fn_quitarPreguntaPlu("+plu_id+", "+datos[i]['psug_id']+")' style='height: 33px; width: 33px;'/></td></tr>";
				
				html_ul_seleccionado = html_ul_seleccionado + "<li class='ui-state-default' style='height: 37px;'>" + datos[i]['pre_sug_descripcion']+"<input type='button' name='opcione' class='opcionAgregado' onclick='fn_quitarPreguntaPlu("+plu_id+", \""+datos[i]['psug_id']+"\")' style='height: 33px; width: 33px; float:right'/></li>";
				
			}
			//$('#preguntas_agregadas').html(html);
			$('#ul_sortable').html(html_ul_seleccionado);
			
			$("input[name='opcione']").css("background","#666666 url('../../imagenes/admin_resources/btn_eliminar.png') 1px 1px no-repeat");
			fn_cargarPreguntasNoAgregadasPlus(cdn_id, plu_id);			
			//$('#ul_sortable').empty();
			
		}else{
			html = html + "<tr><td colspan='2' style='text-align: center;'>No existen preguntas asignadas.</td></tr>";
			$('#preguntas_agregadas').html(html);
			$('#ul_sortable').empty();
			fn_cargarPreguntasNoAgregadasPlus(cdn_id, plu_id);			
			orden_preguntas = 0;
		}
	});	
}

//FUNCION PARA CARGAR LAS PREGUNTAS SUGERIDAS EN LOS PLUS
function fn_cargarPreguntasNoAgregadasPlus(cdn_id, plu_id){
	var html = "<thead><tr class='bg-primary'><td align='center'><h5>Preguntas Sugeridas</h5></td><td align='center' style='width:30px'><h5>Agregar</h5></td></tr></thead>";
	send = {"cargarPreguntasNoAgregadasPlus": 1};
	send.cdn_id = $('#cadenas').val();
	send.plu_id = plu_id;
	send.usuario = $('#idUser').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str > 0){
			for(i=0;i<datos.str;i++){
				html = html + "<tr><td>"+datos[i]['pre_sug_descripcion']+"</td><td style='text-align: center;'><input type='button' onclick='fn_agregarPreguntaPlu("+plu_id+", \""+datos[i]['psug_id']+"\")' name='opciona' class='opcionAgregado' style='height: 33px; width: 33px;'/></td></tr>";
				
				//ul_seleccionado = ul_seleccionado + "<li class='ui-state-default'>" + datos[i]['pre_sug_descripcion']+"<input type='button' name='opcione' class='opcionAgregado' onclick='fn_quitarPreguntaPlu("+plu_id+", "+datos[i]['psug_id']+")' style='height: 33px; width: 33px;'/></li>";
			}
			$('#preguntas').html(html);
			//$('#ul_sortable').html(ul_seleccionado);
			$("input[name='opciona']").css("background","#666666 url('../../imagenes/admin_resources/btn_aceptar.png') 1px 1px no-repeat");
			//$("#preguntas").css('height','480');
			
		}else{
			html = html + "<tr><td colspan='2'>No existen preguntas.</td></tr>";
			$('#preguntas').html(html);	
			
		}
	});	
}

//FUNCION PARA AGREGAR PREGUNTAS SUGERIDAS A UN PLU
function fn_agregarPreguntaPlu(plu_id, psug_id){
	var html = "<thead><tr class='bg-primary'><td align='center'><h5>Preguntas Sugeridas</h5></td><td align='center' style='width:30px'><h5>Agregar</h5></td></tr></thead>";
	$('#buscador').val("");	
	
	orden_preguntas = orden_preguntas + 1;	
		
	send = {"agregarPreguntasPlus": 1};
	send.plu_id = plu_id;
	send.psug_id = psug_id;
	send.cdn_id = $('#cadenas').val();
	send.usuario = $('#idUser').val();
	send.orden_preguntas = orden_preguntas;
	
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str > 0){
			ul_seleccionado = '';
			for(i=0;i<datos.str;i++){
				html = html + "<tr><td style='text-align: left;'>" + datos[i]['pre_sug_descripcion']+"</td><td style='text-align: center;'><input type='button' name='opciona' class='opcionAgregado' onclick='fn_agregarPreguntaPlu("+plu_id+", \""+datos[i]['psug_id']+"\")' style='height: 33px; width: 33px;'/></td></tr>";
				
				ul_seleccionado = ul_seleccionado + "<li class='ui-state-default' style='height: 37px; id=\''psug_"+datos[i]['psug_id']+"'\' onclick='fn_actualizaOrdenPreguntaNueva("+plu_id+", \""+psug_id+"\");'>" + datos[i]['pre_sug_descripcion']+"<input align='right'  type='button' name='opcione' class='opcionAgregado' onclick='fn_quitarPreguntaPlu("+plu_id+", \""+datos[i]['psug_id']+"\")' style='height: 33px; width: 33px; float:right;'/></li>";
				
			}
			$('#preguntas').html(html);
			$('#ul_sortable').html(ul_seleccionado);
			$("input[name='opciona']").css("background","#666666 url('../../imagenes/admin_resources/btn_aceptar.png') 1px 1px no-repeat");
			
			fn_cargarPreguntasPlus(plu_id);
			
			
		}else{
				html = html + "<tr><td colspan='2' style='text-align: center;'>No existen preguntas.</td></tr>";
				$('#preguntas').html(html);
				fn_cargarPreguntasPlus(plu_id);					
			 }
	});

}

function fn_buscarPlusDescripcion(inicio, fin){
	var html = "<tr><td>Num Plu</td><td>Descripci&oacute;n</td></tr>";
	send = {"cargarPlus": 1};
	send.Accion = Accion;
	send.inicio = inicio;
	send.fin = fin;
	send.mag_id = $('#categorias').val();
	send.num_plu = 0;
	send.plu_descripcion = $('#par_desc').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str > 0){
			var total_registros = datos[0]['Total'];
			if(total_registros > 0){
				fn_paginador(total_registros);
				for(i=0;i<datos.str;i++){
					html = html + "<tr id='"+i+"' onclick='fn_seleccionar("+i+")' ondblclick='fn_seleccionarPlus("+i+", "+datos[i]['plu_id']+","+datos[i]['plu_descripcion']+")'><td>"+datos[i]['Num_Plu']+"</td><td style='text-align: left;'>"+datos[i]['plu_descripcion']+"</td></tr>";
				}
				$('#plus').html(html);
			}
		}else{
				alertify.alert('No se encontraron Plus.');
			 }
	});	
}

//FUNCION QUE CARGA LA VIZUALIZACION DEL BOTON EN LA MODAL
function fn_cargarBoton(magp_id)
{	
	Accion = 5;
	var html = ' ';	
	send = {"cargarBotonPlu": 1};
	send.Accion = Accion;
	send.cdn_id = 0;	
	send.magp_id = magp_id;	
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str>0) 
		{			
			for(i=0;i<datos.str;i++)
			{
				html = html + "<tr><td><button style='background-color:"+datos[i]['magp_color']+"; color:"+datos[i]['magp_colortexto']+"; height:60px; width:120px;'>"+datos[i]['magp_desc_impresion']+"</button></td></tr>";
			}
			$('#muestra_boton').html(html);			 
		}else
		{			
			html = html + "<tr><td><button style='background-color:null; color:null; height:60px; width:120px;'>Bot&oacute;n No Configurado</button></td></tr>";
			$('#muestra_boton').html(html);
		}
	});	
}

function fn_paginador(total){
	Cod_Plu = 0;
	var pag = "";
	var paginas = Math.ceil(total/grupos);
	var contador = 0;
	lc_bandera=lc_bandera+1;
	for(i=0;i<paginas;i++){
	pag = pag + "<li  id='pag_"+(i+1)+"' value='"+(i+1)+"' onclick='fn_cargarPlus("+contador+","+(grupos*(i+1)+1)+"), fn_paginacion_color("+(i+1)+")'><a>"+(i+1)+"</a></li> ";
		contador = contador + grupos;
	}
	$('#paginas').html(pag);
}

function fn_paginacion_color(indice)
{
	$("#paginas li").removeClass("active");
	$("#pag_"+indice).addClass("active");
}

//SE CARGAN LAS CONFIGURACIONES DEL PLU EN LA MODAL OBTENIDAS DE LA BD
function fn_cargarConfiguracionPlu(plu_id)
{	
	send = {"cargarCanalImpresion": 1};
	send.plu_id = plu_id;
	send.plu_descripcion = $('#par_desc').val();
	send.usuario = $('#idUser').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){															
		if(datos.str > 0)
		{
			for(i=0;i<datos.str;i++)
			{
				arrayImpresion.push(datos[i]['canal_impresion']);
			}						
			$('#selec_canal_imp').val(arrayImpresion).trigger("chosen:updated.chosen");			
		}
		else
			{				
				$("#selec_canal_imp").val(0);								
				$('#selec_canal_imp').trigger("chosen:updated");
			}
	});
	
	/*CARGAR IMPUESTOS APLICADO PARA ESTE PRODUCTO*/
	send = {"cargarImpuestosProducto": 1};
	send.plu_id = plu_id;
	send.plu_descripcion = $('#par_desc').val();
	send.usuario = $('#idUser').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){															
		if(datos.str > 0)
		{
			for(i=0;i<datos.str;i++)
			{
				arrayImpresion.push(datos[i]['canal_impresion']);
			}
			$('#selec_impuestos').val(arrayImpresion).trigger("chosen:updated.chosen");			
		}
		else
			{				
				$("#selec_impuestos").val(0);								
				$('#selec_impuestos').trigger("chosen:updated");
			}
	});
	

	arrayImpresion.length=0;
	send = {"cargarConfiguracionPlus": 1};
	send.plu_id = plu_id;
	send.plu_descripcion = $('#par_desc').val();
	send.usuario = $('#idUser').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){															
		if(datos.str > 0)
		{
			$("#plu_id").val(plu_id);
			$("#contenedor_configuraciones").modal("show");
			$("#titulo1").text(datos[0]['plu_descripcion']);
			
			reportNumber=$('#cod_reportnumber').val();
			cantidadProducto = $('#txt_cantidadproducto').val();			
			canalImpresion = $('#selec_canal_imp').val();
			tipoPlato = $('#txt_tipoplato').val();
			tipoPlato = $('#select_tipoplato').val();
			tiempo=$('#txt_timepreparacion').val();


            if(datos[0]['anulacion']==1)
			{				
				$("#config_anuu").bootstrapSwitch('state', true);
			}
			else
			{
				$("#config_anuu").bootstrapSwitch('state', false);
			}			
			
			if(datos[0]['gramo']==1)
			{					
				$("#config_gramoss").bootstrapSwitch('state', true);
			}
			else
			{					
				$("#config_gramoss").bootstrapSwitch('state', false);
			}
			
			if(datos[0]['plu_reportnumber']==0 || datos[0]['plu_reportnumber']==null)
			{
				$("#cod_reportnumber").val(0);								
				$('#cod_reportnumber').trigger("chosen:updated");
				//$('#cod_reportnumbercanal').val('');
			}
			else
			{				
				$("#cod_reportnumber").val(datos[0]["plu_reportnumber"]).trigger("chosen:updated.chosen");	
				//$('#cod_reportnumbercanal').val(datos[0]['cla_Nombre']);
            }
            if (datos[0]['estado'] === "Activo") {
                $("#estadoProducto").prop("checked", true);
            } else {
                $("#estadoProducto").prop("checked", false);
            }
							
			$('#txt_cantidadproducto').val(datos[0]['cantidad']);			
			$('#txt_tipoplato').val(datos[0]['tipo_plato']);
			$('#select_tipoplato').val(datos[0]['tipo_plato']);
			$('#txt_timepreparacion').val(datos[0]['tiempo_preparacion']);
			$('#cod_barras').val(datos[0]['codigo_barras']);			
			
			if(datos[0]['qsr']==1)
			{					
				$("#config_qsrr").bootstrapSwitch('state', true);
			}
			else
			{					
				$("#config_qsrr").bootstrapSwitch('state', false);
			}
			
			if($("#txt_cantidadproducto").val()!= 0)
			{					
				$("#config_stock").bootstrapSwitch('state', true);
				$("#txt_cantidadproducto").prop('disabled', false);
				$("#txt_cantidadproducto").focus();
			}
			else
			{					
				$("#config_stock").bootstrapSwitch('state', false);
				$("#txt_cantidadproducto").prop('disabled', true);
            }

		}
	});	
}

function fn_modificar(){
	fn_ListaPreguntas(0);
	fn_AreaTrabajo(1);
	fn_activarCombos(1);
	fn_btn('modificar', 0);
}

function fn_buscar(){
	if(Accion == 1){
		fn_buscarPlusNumPlu(0, grupos+1);
		$('#par_desc').val("");
	}else if(Accion == 2){
		fn_buscarPlusDescripcion(0, grupos+1);
		$('#par_numplu').val("");
	}
	fn_ListaPreguntas(0);
	fn_AreaTrabajo(0);
	fn_activarCombos(0);
}

function fn_cancelar(){
	if(Accion == 0){
		$('#par_numplu').val("");
		$('#par_desc').val("");
		fn_cargarPlus(0, grupos+1);
	}else if(Accion == 1 || Accion ==2){
		$('#par_numplu').val("");
		$('#par_desc').val("");
		Accion = 0;
		fn_cargarPlus(0, grupos+1);
	}else if(Accion == 3){
		$('#par_numplu').val("");
		$('#par_desc').val("");
		$("#plus tr").removeClass("seleccionado");
		Accion = 0;
		fn_cargarPlus(0, grupos+1);
	}
	fn_ListaPreguntas(0);
	fn_AreaTrabajo(0);
	fn_btn('cancelar', 0);
	fn_btn('modificar', 0);
	fn_btn('buscar', 0);
}

function fn_seleccionar(fila)
{
	$("#plus tr").removeClass("success");
	$("#"+fila+"").addClass("success");
}

function fn_seleccionarPlus(fila, plu_id, magp_id, Num_Plu)
{
	fn_ListaPreguntas(0);
	fn_AreaTrabajo(1);
	fn_activarCombos(1);
	fn_btn('modificar', 0);		
	fn_btn('buscar', 0);
	fn_activarCombos(0);
	Accion = 3;
	Cod_Plu = plu_id;
	$("#plus tr").removeClass("success");
	$("#"+fila+"").addClass("success");
	fn_btn('modificar', 1);
	fn_btn('cancelar', 1);
	fn_cargarConfiguracionPlu(plu_id);
	fn_cargarPreguntasPlus(plu_id);
	fn_cargarPreguntasNoAgregadasPlus(10, plu_id);
	fn_cargarBoton(magp_id);
	fn_cargarTipoPlato(plu_id);
	fn_cargarTipoProducto(plu_id);
	fn_cargarNivelSeguridad(plu_id);
	$('#hid_num_plu').val(Num_Plu);
	$("#selec_localusuario").val(0);	
	$("#config_stock").bootstrapSwitch();
	fn_cargarRecetasdetalle();	
}

function fn_AreaContenedorPlus(estado){
	if(estado){
		$("#contenedor_plus").show();
	}else{
		$("#contenedor_plus").hide();
	}
}

function fn_ListaPreguntas(estado){
	if(estado){
		$("#listapreguntas").show();
	}else{
		$("#listapreguntas").hide();
	}
}

function fn_AreaTrabajo(estado){
	if(estado){
		$("#areaTrabajo").show();
	}else{
		$("#areaTrabajo").hide();
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

function fn_activarCombos(estado){
	if(estado == 0){
		$('#config_anu').attr('disabled', true);
		$('#config_gramos').attr('disabled', true);			
	}else{
		$('#config_anu').attr('disabled', false);
		$('#config_gramos').attr('disabled', false);		
	}
}

//FUNCION PARA SINCRONIZAR PRODUCTOS AL SER CREADOS EN EL SG
function fn_sincronizarProductos(){
		
	fn_cargando(1);	
	send = {"sincronizarproductos": 1};		
	send.cadena = $('#cadenas').val();	
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
	
	fn_cargando(0);	
	fn_cargarPlus(0, grupos+1);
	alertify.success("La Sincronizaci&oacute;n ha terminado exitosamente");
		
	});	
}

//BUSACADOR DE PREGUNTAS SUGERIDAS
$(function(){
	
	var theTable = $("#preguntas");	
	$("#buscador").keyup(function() {
	$.uiTableFilter(theTable, this.value);
	});
	
	//VISUALIZAR LA CABECERA DE LA TABLA
	var grid = $(this);
    var rowCount = grid.find("tbody:first > tr:visible").length;
    if (rowCount > 0)
    {
        grid.find("thead").show();		
    }
    else
    {
        grid.find("thead").hide();		
    }
});

//BUSCADOR EN LISTA DE PREGUNTAS ASOCIADAS AL PRODUCTO//CP
$(function(){
   $.expr[":"].contains = $.expr.createPseudo(function(arg) {
		return function( elem ) {
			return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
		};
	});
   
    $(document).ready(function(){   
        $('#buscador').keyup(function(){
                     buscar = $(this).val();
                    					
					$('.sortable li').hide(0,function(){ //Oculta todos los resultados
 
						if($('.sortable:animated').length===0) //Previene repeticiones de la animacion
						{
								$('.sortable li:contains('+buscar+')').show() //Muestra solo los objetos que coinciden con el resultado
						}
					}); 
            });
    });
});

//BUSACADOR DE PREGUNTAS ASOCIADAS AL PRODUCTO
$(function(){	
	
	var theTable = $("#preguntas_agregadas");	
	$("#buscador").keyup(function() {
	$.uiTableFilter(theTable, this.value);
	});
	
	//VISUALIZAR LA CABECERA DE LA TABLA
	var grids = $(this);
    var rowCount = grids.find("tbody:first > tr:visible").length;
    if (rowCount > 0)
    {
        grids.find("thead").show();		
    }
    else
    {
        grids.find("thead").hide();	
    }
});

//FUNCION PARA CARGAR LOS TIPOS DE PRODUCTO
function fn_cargarTipoProducto(plu_id){
	opcion = 'C';	
	lc_estados = 0;
	send = {"cargarTipoproducto": 1};
	send.opcion=opcion;	
	send.plu_id = plu_id;	
	send.usuario = $('#idUser').val();
	send.tipoproducto = '';
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		if(datos.str>0) {			
			
			$("#selec_tipoproducto").html("<option selected  value='0'>-----Seleccionar-----</option>");
			for(i=0; i<datos.str; i++){	
					
					if(datos[i]['ESTADO']==1){
						lc_estados = datos[i]['ID_ColeccionDeDatosPlus'];
					}
					
					$("#selec_tipoproducto").append("<option value="+datos[i]['ID_ColeccionDeDatosPlus']+">"+datos[i]['Descripcion']+"</option>");
				}				
			}
			$("#selec_tipoproducto").val(lc_estados);
	});	
}

//FUNCION PARA GUARDA TIPO DE PLATO DE PLU
function fn_guardaTipoProducto(opcion, plu_id, cfpa_id)
{	
	send = {"guardarTipoproducto": 1};
	send.opcion = opcion;
	send.plu_id = plu_id;	
	send.usuario = $('#idUser').val();
	send.tipoproducto = $('#selec_tipoproducto').val();
	$.getJSON("../adminplus/config_plus.php", send, function(datos){
		
	});	
}

//FUNCION PARA ACTUALIZAR EL ORDEN AL MOVER LAS PREGUNTAS SUGERIDAS //CP
function fn_actualizaOrdenPreguntaNueva(plu_id,psug_id)
{	
	cdn_id = $('#cadenas').val();
	usuario = $('#idUser').val();
	
	//POSICION ELEMENTOS SORTABLE	
	$("#ul_sortable").sortable({ 
		placeholder: "ui-state-highlight", 
		update: function(){ //FUNCION DESPUES DE ACTUALIZAR LA LISTA CON SORTABLE //CP

            nuevos = [];
		nuevos = $(this).sortable("toArray");
				
		  var ordenElementos = nuevos+'&actualizaOrdenPregunta=1'+'&plu_id='+plu_id+''+'&psug_id='+psug_id+''+'&cdn_id='+cdn_id+''+'&usuario='+usuario+'&arreglo='+nuevos; 
		  //con serialize obtenemos el orden de los elementos, tenemos que concatenale el id con un nombre = psug_ ==> que sera el array=psug[] //CP		 
		
			$.getJSON("../adminplus/config_plus.php",ordenElementos, function(datos){
				
			});  
		} 
	 }); 
}
