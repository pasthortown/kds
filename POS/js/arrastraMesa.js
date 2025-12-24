//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para arratre de mesas//////////////
//////////////////////////////////////////////////////////////
///////FECHA CREACION: 16-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 

/////////////REVISION DEL NAVEGADOR UTILIZADO/////////////////
function carga()
{
	posicion=0;
	// IE
	if(navigator.userAgent.indexOf("MSIE")>=0) navegador=0;
	// Otros
	else navegador=1;
}

/////////////EVITAR QUE SE EJECUTEN EVENTOS ADICIONALES////////
function evitaEventos(event)
{
	if(navegador==0)
	{
	window.event.cancelBubble=true;
	window.event.returnValue=false;
	}
	if(navegador==1) event.preventDefault();
}


/////////////INICIO DEL ARRASTRE DEL OBJETO/////////////////////
function comienzoMovimiento(event, id)
{
	elMovimiento=document.getElementById(id);

	if(navegador==0)			//Obtiene la posicion inicial del objeto
	{
		cursorComienzoX=window.event.clientX+document.documentElement.scrollLeft+document.body.scrollLeft;
		cursorComienzoY=window.event.clientY+document.documentElement.scrollTop+document.body.scrollTop;
		
		document.attachEvent("onmousemove", enMovimiento);
		document.attachEvent("onmouseup", finMovimiento);
	}
	if(navegador==1)
	{
		cursorComienzoX=event.clientX+window.scrollX;
		cursorComienzoY=event.clientY+window.scrollY;
		document.addEventListener("mousemove", enMovimiento, true);
		document.addEventListener("mouseup", finMovimiento, true);
	}

	elComienzoX=parseInt(elMovimiento.style.left);
	elComienzoY=parseInt(elMovimiento.style.top);
	// Actualizo el posicion del elemento
	elMovimiento.style.zIndex=++posicion;
	document.getElementById("txtMesaId").value = id;
	evitaEventos(event);

}

/////////////PERMITE EL ARRASTRE DEL OBJETO/////////////////////////
function enMovimiento(event)
{
	var xActual, yActual;
	if(navegador==0)
	{
		xActual=window.event.clientX+document.documentElement.scrollLeft+document.body.scrollLeft;
		yActual=window.event.clientY+document.documentElement.scrollTop+document.body.scrollTop;
	}
	if(navegador==1)
	{
		xActual=event.clientX+window.scrollX;
		yActual=event.clientY+window.scrollY;
	}

	elMovimiento.style.left=(elComienzoX+xActual-cursorComienzoX)+"px";
	elMovimiento.style.top=(elComienzoY+yActual-cursorComienzoY)+"px";

	evitaEventos(event);
}

/////////////FINALIZA EL ARRASTRE DEL OBJETO/////////////////////////
function finMovimiento(event,id)
	{
	if(navegador==0)
	{
		document.detachEvent("onmousemove", enMovimiento);
		document.detachEvent("onmouseup", finMovimiento);
	}
	if(navegador==1)
	{
		document.removeEventListener("mousemove", enMovimiento, true);
		document.removeEventListener("mouseup", finMovimiento, true);
	}
	document.getElementById("txt1").value = elMovimiento.style.left;		//Asigna los valores a los input para visualizar las coordenadas del objeto en X
	document.getElementById("txt2").value = elMovimiento.style.top;			//Asigna los valores a los input para visualizar las coordenadas del objeto en Y
	fn_guardarMesa();													
}

window.onload=carga;
