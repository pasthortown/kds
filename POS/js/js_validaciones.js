function fn_aceptaNum(evt){ 
tecla = (document.all) ? evt.keyCode : evt.which; 
    if (tecla==8 || tecla==0) 
	{
		return true;
	}
    patron =/[\d.]/; 
    te = String.fromCharCode(tecla); 
    return patron.test(te);
}

function fn_eliminaEspacios(lc_cadena)
{
	// Funcion equivalente a trim en PHP
	var x=0, y=lc_cadena.length-1;
	while(lc_cadena.charAt(x)==" ") x++;	
	while(lc_cadena.charAt(y)==" ") y--;	
	return lc_cadena.substr(x, y-x+1);
}

function fn_aceptaNum_Rec(evt){ 
var nav4 = window.Event ? true : false;
// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57 
var key = nav4 ? evt.which : evt.keyCode; 
return (key <= 13 || (key >= 45 && key <= 57 && key!=47));
}


function fn_numeros(e) {
    tecla = (document.all) ? e.keyCode : e.which; 
    if (tecla==8 || tecla==0) 
	{
		return true;
	}
    patron =/\d/; 
    te = String.fromCharCode(tecla); 
    return patron.test(te); 
}
function fn_ip(e)
{
	tecla = (document.all) ? e.keyCode : e.which; 
    if (tecla==8 || tecla==0) 
	{
		return true;
	}
    patron =/[\d.]/; 
    te = String.fromCharCode(tecla); 
    return patron.test(te);
}

function fn_letras(e)
{
	tecla = (document.all) ? e.keyCode : e.which; 
    if (tecla==8 || tecla==0) 
	{
		//alert('Ingrese solo letras');
		return true;
		
	}
    patron = /[a-zA-Z\s\u00e1\u00e9\u00ed\u00f3\u00fa\u00c1\u00c9\u00cd\u00d3\u00da\u00f1\u00d1]/;
    te = String.fromCharCode(tecla); 
    return patron.test(te);
}