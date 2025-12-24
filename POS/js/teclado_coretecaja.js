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

/////////////////////////////CERRAR INACTIVO/////////////////////////////////
function toggleDiv(divID){
    $("#" + divID).fadeToggle(200, function() {
        var openDiv = $(this).is(':visible') ? divID : null;
    });
}

/////////////////////////////CREAR TECLADO ALFANUMERICO/////////////////////////////////
function fn_alfaNumerico(e){
    if (!$(e.target).closest("#keyboard").length){
    }

    $("#keyboard").empty();
    var posicion = $(e).position();

    var leftPos = 155;
    var topPos = 250;
    leftPos:posicion.left;
    topPos:posicion.top;

    var num0 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",0)'>0</button>";
    var num1 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",1)'>1</button>";
    var num2 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",2)'>2</button>";
    var num3 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",3)'>3</button>";
    var num4 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",4)'>4</button>";
    var num5 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",5)'>5</button>";
    var num6 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",6)'>6</button>";
    var num7 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",7)'>7</button>";
    var num8 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",8)'>8</button>";
    var num9 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",9)'>9</button>";

    var cadQ = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"Q\")'>Q</button>";
    var cadW = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"W\")'>W</button>";
    var cadE = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"E\")'>E</button>";
    var cadR = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"R\")'>R</button>";
    var cadT = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"T\")'>T</button>";
    var cadY = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"Y\")'>Y</button>";
    var cadU = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"U\")'>U</button>";
    var cadI = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"I\")'>I</button>";
    var cadO = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"O\")'>O</button>";
    var cadP = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"P\")'>P</button>";

    var cadA = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"A\")'>A</button>";
    var cadS = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"S\")'>S</button>";
    var cadD = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"D\")'>D</button>";
    var cadF = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"F\")'>F</button>";
    var cadG = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"G\")'>G</button>";
    var cadH = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"H\")'>H</button>";
    var cadJ = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"J\")'>J</button>";
    var cadK = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"K\")'>K</button>";
    var cadL = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"L\")'>L</button>";

    var cadZ = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"Z\")'>Z</button>";
    var cadX = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"X\")'>X</button>";
    var cadC = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"C\")'>C</button>";
    var cadV = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"V\")'>V</button>";
    var cadB = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"B\")'>B</button>";
    var cadN = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"N\")'>N</button>";
    var cadM = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"M\")'>M</button>";

    var arroba = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"@\")'>@</button>";
    var guion = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"-\")'>-</button>";
    var barraBaja = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"_\")'>_</button>";

    var numeral = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"#\")'>#</button>";
    var espacio = "";
    espacio = "<button align='center' class='btnEspaciadora' onclick='fn_agregarCaracter("+$(e).attr("id")+",\" \")'>Espacio</button>";
    var coma = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter("+$(e).attr("id")+",\",\")'>,</button>";
    var punto = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter("+$(e).attr("id")+",\".\")'>.</button>";

    var borrarCaracter="<button class='btnVirtualBorrar' onclick='fn_eliminarNumero("+$(e).attr("id")+")'>&larr;</button>";
    var borrarTodo="<button class='btnVirtualBorrarTodo' onclick='fn_eliminarTodo("+$(e).attr("id")+")'>Limpiar</button>";

    $("#keyboard").css({
        display: "block",
        position: "absolute"
    });

    $("#keyboard").append("<br/>"+num1+num2+num3+num4+num5+num6+num7+num8+num9+num0+borrarCaracter+"<br/>"+cadQ+cadW+cadE+cadR+cadT+cadY+cadU+cadI+cadO+cadP+borrarTodo+"<br/>"+arroba+cadA+cadS+cadD+cadF+cadG+cadH+cadJ+cadK+cadL+guion+"<br/>"+numeral+barraBaja+cadZ+cadX+cadC+cadV+cadB+cadN+cadM+coma+punto+"<br/>"+espacio);
}

/////////////////////////////CREAR TECLADO NUMERICO/////////////////////////////////
function fn_numerico(e){
		
    if (!$(e.target).closest("#numPad").length)
            {toggleDiv('numPad'); }

    $("#numPad").empty();
    var posicion = $(e).position();

    var leftPos = 310;
    var topPos = 60;
    leftPos:posicion.left;
    topPos:posicion.top;
	
    var num0 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"0\")'>0</button>";
    var num1 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"1\")'>1</button>";
    var num2 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"2\")'>2</button>";
    var num3 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"3\")'>3</button>";
    var num4 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"4\")'>4</button>";
    var num5 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"5\")'>5</button>";
    var num6 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"6\")'>6</button>";
    var num7 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"7\")'>7</button>";
    var num8 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"8\")'>8</button>";
    var num9 = "<button class='btnVirtualMotivo' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"9\")'>9</button>";

    var borrarCaracter="<button class='btnVirtualBorrar' onclick='fn_eliminarNumero("+$(e).attr("id")+");'>&larr;</button>";
    var borrarTodo="<button class='btnVirtualOKpq' onclick='fn_eliminarTodo("+$(e).attr("id")+");'>&lArr;</button>";
    var btnOk ="<button class='btnVirtualOKpq' onclick='fn_btnOk("+$("#numPad").attr("id")+", "+$(e).attr("id")+");'>OK</button>";

    $("#numPad").css({
        display:"block",
        position: "absolute",
        top:topPos,
        left:leftPos
    });

    $("#numPad").append(num7+num8+num9+"<br/>"+num4+num5+num6+"<br/>"+num1+num2+num3+"<br/>"+num0+btnOk+"<br/>"+borrarCaracter+borrarTodo);	
}

/////////////////////////////AGREGAR NUMERO/////////////////////////////////
function fn_agregarCaracter(obj, valor){
    var lc_cantidad=$(obj).val();
    lc_cantidad += valor;
    $(obj).val(lc_cantidad);
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
function fn_btnOk(e, p){
    $(e).show();
    $(p).trigger("change");
}

/////////////////////////////OCULTAR DIVS///////////////////////////////////////////
function fn_ocultar(){
    $("#txtPad").hide();	
}

function fn_ocultar_alfanumerico(){	
    $("#keyboard").hide();
}

function fn_ver_alfanumerico(){	
    $("#keyboard").show();
}
