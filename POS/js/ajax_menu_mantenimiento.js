///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Darwin Mora ////////////////////////////////////////////
///////DESCRIPCION: mostrar el trafico de la pagina de inicio//////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 20-02-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
	
	$.ajaxSetup({async: false});
	fn_cargando(1);
	$("#incluirPagina").load('/pos/mantenimiento/inicio/inicio.php');
	fn_muestra_trafico();
	fn_cargando(0);
	$.ajaxSetup({async: true});

var usr_id= 0;
var pnt_Id = 0;
lc_std=-1; //almacena el estado de la solicitud seleccionada

$(document).ready(function(){

	//recuperar el ID del usuario
	usr_id=$('#txt_usuario').val();
	
	//Muestra acceso rapido
	fn_muestra_trafico(pnt_Id,usr_id);
	
});

/*-------------------------------------------------------
Funcion para la pantalla para seleccionar el restaurante
-------------------------------------------------------*/
function valida_envia(){
	document.frmAccesoUser.submit();
};
lc_variable = document.getElementById("txtclavenue"); 
(typeof(window[lc_variable]) == "undefined")? false: true;

if(lc_variable){
	document.getElementById("txtclavenue").focus();
}else{
	document.getElementById("selrestaurante").focus();        
}

function retornar_index(){
	location.href = "../mantenimiento/index.php";	
}

/*-------------------------------------------------------
Funcion para capturar el trafico del ingreso por usuario
-------------------------------------------------------*/

function fn_captura_trafico(pnt_id,usr_id/*,pnt_Ruta*/){

	$.ajaxSetup({async: false});
	fn_cargando(1);
	var ruta = $('#'+pnt_id).attr('name');
	$("#incluirPagina").load('/pos/mantenimiento/'+ruta);
	fn_cargando(0);
	$.ajaxSetup({async: true});
	
	send={"capturarTrafico":1};
	send.pnt_id = pnt_id;
	send.usr_id = usr_id;
	$.getJSON("../inicio/config_menu_mantenimiento.php",send,function(datos) {
		if(datos.str>0){
			ctra_id = datos[0]['ctra_id'];
		}
		
	});

}

/*-------------------------------------------------------
Funcion para mostrar el trafico del ingreso por usuario
-------------------------------------------------------*/

function fn_muestra_trafico(pnt_id,usr_id){

	send={"muestraTrafico":1};
	send.pnt_id = pnt_id;
	send.usr_id = usr_id;
	$.getJSON("../inicio/config_menu_mantenimiento.php",send,function(datos) {
		if(datos.str>0){
			
			for(i=0; i<datos.str; i++) {
				//Salto de linea
				if(i==2 || i==4){
					$("#tabla").append("<br><br>"); 		
				}

				if(i==0||i==1){
					$("#tabla").append("<a href='"+datos[i]['pnt_Ruta']+"' onclick='fn_captura_trafico("+datos[i]['pnt_id']+","+ usr_id +")' class='btn btn-success btn-lg'>"+datos[i]['pnt_Nombre_Mostrar']+"&nbsp;<span class='badge'>"+datos[i]['ctra_numero']+"</span></a>&nbsp;"); 
				}else if( i==2 || i==3){
					$("#tabla").append("<a href='"+datos[i]['pnt_Ruta']+"' onclick='fn_captura_trafico("+datos[i]['pnt_id']+","+ usr_id +")' class='btn btn-warning'>"+datos[i]['pnt_Nombre_Mostrar']+"&nbsp;<span class='badge'>"+datos[i]['ctra_numero']+"</span></a>&nbsp;"); 
				}else /*if(i==3 || i==4 || i==5)*/	{
					$("#tabla").append("<a href='"+datos[i]['pnt_Ruta']+"' onclick='fn_captura_trafico("+datos[i]['pnt_id']+","+ usr_id +")' class='btn btn-danger btn-sm'>"+datos[i]['pnt_Nombre_Mostrar']+"&nbsp;<span class='badge'>"+datos[i]['ctra_numero']+"</span></a>&nbsp;"); 
				}
				
			}
		}else{
			$("#id_atajo").hide();
		}
		
	});
}

/*-------------------------------------------------------
Funcion para mostrar pantalla de espera (Cargando)
-------------------------------------------------------*/

function fn_cargando(estado){
	
	if(estado){
		$('#cargando').css('display', 'block');
		$('#cargandoimg').css('display', 'block');
	}else{
		$('#cargando').css('display', 'none');
		$('#cargandoimg').css('display', 'none');
	}
}
