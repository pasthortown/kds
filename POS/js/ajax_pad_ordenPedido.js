//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para teclados Virtuales////////////
//////////////////////////////////////////////////////////////
///////FECHA CREACION: 25-Febrero-2014////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 
$(document).ready(function(){
	$("#keyboard").hide();
	$("#numPad").hide();
	$("#txtPad").hide();
	
});

/////////////////////////////CERRAR VENTANA///////////////////////////////////
/*$(document).click(function(e) {
    if (!$(e.target).closest('#'+openDiv).length) {
        toggleDiv(openDiv);
    }
});*/

/////////////////////////////CERRAR INACTIVO/////////////////////////////////
function toggleDiv(divID) {
    $("#" + divID).fadeToggle(200, function() {
        openDiv = $(this).is(':visible') ? divID : null;
    });
}

/////////////////////////////CREAR TECLADO ALFANUMERICO/////////////////////////////////
function fn_alfaNumerico(e){
	
		if (!$(e.target).closest("#keyboard").length)
			{toggleDiv('keyboard'); }
		
		$("#keyboard").empty();
		var posicion = $(e).position();
		
		var leftPos=155;
		var topPos=300;
		leftPos:posicion.left;
		topPos:posicion.top;
		
		num0 = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'0'),fn_listarPro()>0</button>";
		num1 = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'1'),fn_listarPro()>1</button>";
		num2 = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'2'),fn_listarPro()>2</button>";
		num3 = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'3'),fn_listarPro()>3</button>";
		num4 = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'4'),fn_listarPro()>4</button>";
		num5 = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'5'),fn_listarPro()>5</button>";
		num6 = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'6'),fn_listarPro()>6</button>";
		num7 = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'7'),fn_listarPro()>7</button>";
		num8 = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'8'),fn_listarPro()>8</button>";
		num9 = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'9'),fn_listarPro()>9</button>";
	
		cadQ = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'Q'),fn_listarPro()>Q</button>";
		cadW = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'W'),fn_listarPro()>W</button>";
		cadE = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'E'),fn_listarPro()>E</button>";
		cadR = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'R'),fn_listarPro()>R</button>";
		cadT = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'T'),fn_listarPro()>T</button>";
		cadY = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'Y'),fn_listarPro()>Y</button>";
		cadU = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'U'),fn_listarPro()>U</button>";
		cadI = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'I'),fn_listarPro()>I</button>";
		cadO = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'O'),fn_listarPro()>O</button>";
		cadP = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'P'),fn_listarPro()>P</button>";
		
		cadA = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'A'),fn_listarPro()>A</button>";
		cadS = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'S'),fn_listarPro()>S</button>";
		cadD = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'D'),fn_listarPro()>D</button>";
		cadF = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'F'),fn_listarPro()>F</button>";
		cadG = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'G'),fn_listarPro()>G</button>";
		cadH = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'H'),fn_listarPro()>H</button>";
		cadJ = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'J'),fn_listarPro()>J</button>";
		cadK = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'K'),fn_listarPro()>K</button>";
		cadL = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'L'),fn_listarPro()>L</button>";

		cadZ = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'Z'),fn_listarPro()>Z</button>";
		cadX = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'X'),fn_listarPro()>X</button>";
		cadC = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'C'),fn_listarPro()>C</button>";
		cadV = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'V'),fn_listarPro()>V</button>";
		cadB = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'B'),fn_listarPro()>B</button>";
		cadN = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'N'),fn_listarPro()>N</button>";
		cadM = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'M'),fn_listarPro()>M</button>";
		
		arroba = "<button class='btnVirtualBorrar' onclick=fn_agregarCaracter("+$(e).attr("id")+",'@'),fn_listarPro()>@</button>";
		guion = "<button class='btnVirtualBorrar' onclick=fn_agregarCaracter("+$(e).attr("id")+",'-'),fn_listarPro()>-</button>";
		barraBaja = "<button class='btnVirtualBorrar' onclick=fn_agregarCaracter("+$(e).attr("id")+",'_'),fn_listarPro()>_</button>";

		numeral = "<button class='btnVirtualBorrar' onclick=fn_agregarCaracter("+$(e).attr("id")+",'#'),fn_listarPro()>#</button>";
		espacio = "<button class='btnEspaciadora' onclick=fn_agregarCaracter("+$(e).attr("id")+",'---'),fn_listarPro()>Espacio</button>";
		coma = "<button class='btnVirtualBorrar' onclick=fn_agregarCaracter("+$(e).attr("id")+",','),fn_listarPro()>,</button>";
		punto = "<button class='btnVirtualBorrar' onclick=fn_agregarCaracter("+$(e).attr("id")+",'.'),fn_listarPro()>.</button>";
			
		borrarCaracter="<button class='btnVirtualBorrar' onclick=fn_eliminarNumero("+$(e).attr("id")+"),fn_listarPro()>&larr;</button>";
		borrarTodo="<button class='btnVirtualBorrar' onclick=fn_eliminarTodo("+$(e).attr("id")+")()>&lArr;</button>";
		btnOk ="<button class='btnVirtualOK' onclick=fn_btnOk("+$("#keyboard").attr("id")+")>OK</button>";
		
		$("#keyboard").css({
			display:"block",
			position: "absolute",
			top:topPos,
			left:leftPos,
		})
		
		$("#keyboard").append(num1+num2+num3+num4+num5+num6+num7+num8+num9+num0+borrarCaracter+"<br/>"+cadQ+cadW+cadE+cadR+cadT+cadY+cadU+cadI+cadO+cadP+borrarTodo+"<br/>"+arroba+cadA+cadS+cadD+cadF+cadG+cadH+cadJ+cadK+cadL+guion+"<br/>"+numeral+barraBaja+cadZ+cadX+cadC+cadV+cadB+cadN+cadM+coma+punto+"<br/>"+espacio+btnOk);
	
}

/////////////////////////////CREAR TECLADO NUMERICO/////////////////////////////////
function fn_numerico(e){
		
		if (!$(e.target).closest("#numPad").length)
			{toggleDiv('numPad'); }
		
		$("#numPad").empty();
		var posicion = $(e).position();
		
		var leftPos=410;
		var topPos=500;
		leftPos:posicion.left;
		topPos:posicion.top;
	
		num0 = "<button class='btnVirtual' onclick=fn_agregarCaracterNum("+$(e).attr("id")+",'0')>0</button>";
		num1 = "<button class='btnVirtual' onclick=fn_agregarCaracterNum("+$(e).attr("id")+",'1')>1</button>";
		num2 = "<button class='btnVirtual' onclick=fn_agregarCaracterNum("+$(e).attr("id")+",'2')>2</button>";
		num3 = "<button class='btnVirtual' onclick=fn_agregarCaracterNum("+$(e).attr("id")+",'3')>3</button>";
		num4 = "<button class='btnVirtual' onclick=fn_agregarCaracterNum("+$(e).attr("id")+",'4')>4</button>";
		num5 = "<button class='btnVirtual' onclick=fn_agregarCaracterNum("+$(e).attr("id")+",'5')>5</button>";
		num6 = "<button class='btnVirtual' onclick=fn_agregarCaracterNum("+$(e).attr("id")+",'6')>6</button>";
		num7 = "<button class='btnVirtual' onclick=fn_agregarCaracterNum("+$(e).attr("id")+",'7')>7</button>";
		num8 = "<button class='btnVirtual' onclick=fn_agregarCaracterNum("+$(e).attr("id")+",'8')>8</button>";
		num9 = "<button class='btnVirtual' onclick=fn_agregarCaracterNum("+$(e).attr("id")+",'9')>9</button>";
		
		borrarCaracter="<button class='btnVirtualBorrar' onclick=fn_eliminarNumero("+$(e).attr("id")+")>&larr;</button>";
		borrarTodo="<button class='btnVirtualOKpq' onclick=fn_eliminarTodo("+$(e).attr("id")+")>&lArr;</button>";
		btnOk ="<button class='btnVirtualOKpq' onclick=fn_btnOk("+$("#numPad").attr("id")+")>OK</button>";
		
		$("#numPad").css({
			display:"block",
			position: "absolute",
			top:topPos,
			left:leftPos,
		})
		
		$("#numPad").append(num7+num8+num9+"<br/>"+num4+num5+num6+"<br/>"+num1+num2+num3+"<br/>"+num0+btnOk+"<br/>"+borrarCaracter+borrarTodo);	
	
}

/////////////////////////////CREAR TECLADO ALFABETICO/////////////////////////////////
function fn_letras(e){
	
		if (!$(e.target).closest("#txtPad").length)
			{toggleDiv('txtPad'); }
	
		$("#txtPad").empty();
		var posicion = $(e).position();
		
		var leftPos=15;
		if(posicion.left>900)
			{leftPos=posicion.left-700;}
		else
			{leftPos:posicion.left}
	
		cadQ = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'Q')>Q</button>";
		cadW = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'W')>W</button>";
		cadE = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'E')>E</button>";
		cadR = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'R')>R</button>";
		cadT = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'T')>T</button>";
		cadY = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'Y')>Y</button>";
		cadU = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'U')>U</button>";
		cadI = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'I')>I</button>";
		cadO = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'O')>O</button>";
		cadP = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'P')>P</button>";
		
		cadA = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'A')>A</button>";
		cadS = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'S')>S</button>";
		cadD = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'D')>D</button>";
		cadF = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'F')>F</button>";
		cadG = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'G')>G</button>";
		cadH = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'H')>H</button>";
		cadJ = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'J')>J</button>";
		cadK = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'K')>K</button>";
		cadL = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'L')>L</button>";

		cadZ = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'Z')>Z</button>";
		cadX = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'X')>X</button>";
		cadC = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'C')>C</button>";
		cadV = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'V')>V</button>";
		cadB = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'B')>B</button>";
		cadN = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'N')>N</button>";
		cadM = "<button class='btnVirtual' onclick=fn_agregarCaracter("+$(e).attr("id")+",'M')>M</button>";
		
		borrarCaracter="<button class='btnVirtualBorrar' onclick=fn_eliminarNumero("+$(e).attr("id")+")>&larr;</button>";
		borrarTodo="<button class='btnVirtualBorrar' onclick=fn_eliminarTodo("+$(e).attr("id")+")>&lArr;</button>";
		btnOk ="<button class='btnVirtualOKpq' onclick=fn_btnOk("+$("#txtPad").attr("id")+")>OK</button>";
		espacio = "<button class='btnEspaciadoraGr' onclick=fn_agregarCaracter("+$(e).attr("id")+",'&nbsp;')>Espacio</button>";
		
		$("#txtPad").css({
			display:"block",
			position: "absolute",
			top:posicion.top + 30,
			left:leftPos,
		})
		
		$("#txtPad").append(cadQ+cadW+cadE+cadR+cadT+cadY+cadU+cadI+cadO+cadP+"<br/>"+cadA+cadS+cadD+cadF+cadG+cadH+cadJ+cadK+cadL+borrarCaracter+"<br/>"+cadZ+cadX+cadC+cadV+cadB+cadN+cadM+borrarTodo+btnOk+"<br/>"+espacio);
	
}

/////////////////////////////AGREGAR NUMERO/////////////////////////////////
function fn_agregarCaracter(obj, valor){
	var lc_cantidad=$(obj).val();
	lc_cantidad += valor;
	$(obj).val(lc_cantidad);
	fn_buscarTouch(lc_cantidad);
}

/////////////////////////////AGREGAR NUMERO/////////////////////////////////
function fn_agregarCaracterNum(obj, valor){
	var lc_cantidad=$(obj).val();
	lc_cantidad += valor;
	$(obj).val(lc_cantidad);
}

/////////////////////////////ELIMINAR CARACTER/////////////////////////////////
function fn_eliminarNumero(e){
	var lc_cantidad = $(e).val();
	lc_cantidad = lc_cantidad.substring(0, lc_cantidad.length-1);
	$(e).val(lc_cantidad); 
}

/////////////////////////////ELIMINAR TODO LA CADENA///////////////////////////
function fn_eliminarTodo(e){
	$(e).val(''); 
}

/////////////////////////////ACEPTAR///////////////////////////////////////////
function fn_btnOk(e){
 $(e).hide();
 $(e).empty();
}

/////////////////////////////OCULTAR DIVS///////////////////////////////////////////
function fn_ocultar(){
	$("#txtPad").hide();
}

