//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para Formas de Pago////////////////
//////////////////////////////////////////////////////////////
///////FECHA CREACION: 28-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 

$(document).ready(function(){
	$('#numPad').hide();
	$('#txtPad').hide();
});

/////////////////////////////AGREGAR NUMERO O CARACTER/////////////////////////////////
function fn_agregarLetra(valor){
	var lc_cantidad=$("#txtLed").val();

	lc_cantidad += valor;
	
	$("#txtLed").val(lc_cantidad);
}

/////////////////////////////AGREGAR NUMERO O CARACTER/////////////////////////////////
function fn_agregarNumero(valor){
	var lc_cantidad=$("#numLed").val();

	lc_cantidad += valor;
	
	$("#numLed").val(lc_cantidad);
}


/////////////////////////////ELIMINAR CARACTER O NUMERO/////////////////////////////////

function fn_eliminarNumero(e){
	alert(e);
	
  var lc_cantidad = document.getElementById('+e+').value.substring(0, document.getElementById('+e+').value.length-1);
   document.getElementById(e).value=lc_cantidad;
}


/////////////////////////////PRESENTA NUMPAD/////////////////////////////////

function fn_numPad(e){
	var posicion = $(e).position();
	$("#numPad").css({
		display:"block",
		position: "absolute",
		top:posicion.top + 20,
		left:posicion.left,
	})
}

/////////////////////////////OCULTA NUMPAD/////////////////////////////////

function fn_numPadHide(){
 $( "#numPad" ).hide({
		  autoOpen: false,
		  hide: {
			effect: "explode",
			duration: 1000
		  }
    });
}

/////////////////////////////PRESENTA TXTPAD/////////////////////////////////
function fn_txtPad(e){
	var posicion = $(e).position();
	$("#txtPad").css({
		display:"block",
		position: "absolute",
		top:posicion.top + 20,
		left:posicion.left,
	})
}

/////////////////////////////OCULTA TXTPAD/////////////////////////////////

function fn_txtPadHide(){
 $( "#txtPad" ).hide({
		  autoOpen: false,
		  hide: {
			effect: "explode",
			duration: 1000
		  }
    });
}


/////////////////////////////AGREGAR NUMERO/////////////////////////////////
function teclado (elEvento) { 
	 evento = elEvento || window.event;
	 k=evento.keyCode; 

	 if (k>47 && k<58) { 
		p=k-48; //buscar número a mostrar.
		p=String(p); //convertir a cadena para poder añádir en pantalla.
		fn_agregarNumero(p); //enviar para mostrar en pantalla
	}	
	if (k>95 && k<106) {
		p=k-96;
		p=String(p);
		fn_agregarNumero(p);
	}
	if (k==110 || k==190) {fn_agregarNumero(".");} //teclas de coma decimal
	if (k==8) {fn_eliminarNumero();} //Retroceso en escritura : tecla retroceso.
	if(k>57 && k<210)
	{ document.getElementById("txtLed").value=""; }
}



/////////////////////////////////////////VALIDAR CEDULA/////////////////////////
function validarDocumento(campo) {
	if(campo.search(/\D/g)>=0){
		return false; 
	}
	numero = campo;
	var suma = 0;
	var residuo = 0;
	var pri = false;
	var pub = false;
	var nat = false;
	var numeroProvincias = 22;
	var modulo = 11;

	var ok=1;			/* Verifico que el campo no contenga letras */

	/* Aqui almacenamos los digitos de la cedula en variables. */
	d1 = numero.substr(0,1);
	d2 = numero.substr(1,1);
	d3 = numero.substr(2,1);
	d4 = numero.substr(3,1);
	d5 = numero.substr(4,1);
	d6 = numero.substr(5,1);
	d7 = numero.substr(6,1);
	d8 = numero.substr(7,1);
	d9 = numero.substr(8,1);
	d10 = numero.substr(9,1); 

	/* El tercer digito es: */
	/* 9 para sociedades privadas y extranjeros */
	/* 6 para sociedades publicas */
	/* menor que 6 (0,1,2,3,4,5) para personas naturales */ 

	if (d3==7 || d3==8)
		{return false;}

	if (d10=="")
		{return false; } 

/* Solo para personas naturales (modulo 10) */
	if (d3 < 6){
		nat = true;
		p1 = d1 * 2; if (p1 >= 10) p1 -= 9;
		p2 = d2 * 1; if (p2 >= 10) p2 -= 9;
		p3 = d3 * 2; if (p3 >= 10) p3 -= 9;
		p4 = d4 * 1; if (p4 >= 10) p4 -= 9;
		p5 = d5 * 2; if (p5 >= 10) p5 -= 9;
		p6 = d6 * 1; if (p6 >= 10) p6 -= 9;
		p7 = d7 * 2; if (p7 >= 10) p7 -= 9;
		p8 = d8 * 1; if (p8 >= 10) p8 -= 9;
		p9 = d9 * 2; if (p9 >= 10) p9 -= 9;
		modulo = 10;
	} 

	/* Solo para sociedades publicas (modulo 11) */
	/* Aqui el digito verficador esta en la posicion 9, en las otras 2 en la pos. 10 */
	else if(d3 == 6){
		pub = true;
		p1 = d1 * 3;
		p2 = d2 * 2;
		p3 = d3 * 7;
		p4 = d4 * 6;
		p5 = d5 * 5;
		p6 = d6 * 4;
		p7 = d7 * 3;
		p8 = d8 * 2;
		p9 = 0;
	} 

/* Solo para entidades privadas (modulo 11) */
		else if(d3 == 9) {
			pri = true;
			p1 = d1 * 4;
			p2 = d2 * 3;
			p3 = d3 * 2;
			p4 = d4 * 7;
			p5 = d5 * 6;
			p6 = d6 * 5;
			p7 = d7 * 4;
			p8 = d8 * 3;
			p9 = d9 * 2;
		}

		suma = p1 + p2 + p3 + p4 + p5 + p6 + p7 + p8 + p9;
		residuo = suma % modulo; 

		digitoVerificador = residuo==0 ? 0: modulo - residuo;		/* Si residuo=0, dig.ver.=0, caso contrario 10 - residuo*/
		
/* ahora comparamos el elemento de la posicion 10 con el dig. ver.*/
	if (pub==true){
		if (digitoVerificador != d9)
			{return false; }
/* El ruc de las empresas del sector publico terminan con 0001*/
		if ( numero.substr(9,4) != '0001' )
			{ return false; }
	} else if(pri == true){
		if (digitoVerificador != d10)
			{ return false; }
		if ( numero.substr(10,3) != '001' )
			{ return false; }
	} else if(nat == true){
		if (digitoVerificador != d10)
			{ return false; }
		if (numero.length >10 && numero.substr(10,3) != '001')
			{return false;}
	}
	return true;
}
