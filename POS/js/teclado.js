///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade //////////////////////
////////DESCRIPCION: Clase para teclados Virtuales ////////////
///////FECHA CREACION: 25-02-2014 /////////////////////////////
///////////////////////////////////////////////////////////////

$(document).ready(function(){
    
    $("#keyboard").hide();
    $("#numPad").hide();
    $("#keyboardCanje").hide();
    $("#numPadCredenciales").hide();
    $("#txtPad").hide();
        
});




function toggleDiv(divID){
    $("#" + divID).fadeToggle(200, function() {
        var openDiv = $(this).is(':visible') ? divID : null;
    });
}

var detectarOS =function() {
    const platform = navigator.platform.toLowerCase(),
        iosPlatforms = ['iphone', 'ipad', 'ipod', 'ipod touch'];

    if (platform.includes('mac')) return 'MacOS';
    if (iosPlatforms.includes(platform)) return 'iOS';
    if (platform.includes('win')) return 'Windows';
    if (/android/.test(navigator.userAgent.toLowerCase())) return 'Android';
    if (/linux/.test(platform)) return 'Linux';

    return 'unknown';
}


function fn_alfaNumerico(e){
        if (!$(e.target).closest("#keyboard").length){toggleDiv('keyboard');}

        $("#keyboard").empty();
        var posicion = $(e).position();
    
        var leftPos = 155;
        var topPos = 300;
        leftPos:posicion.left;
        topPos:posicion.top;
    
        var num0 = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",0)'>0</button>";
        var num1 = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",1)'>1</button>";
        var num2 = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",2)'>2</button>";
        var num3 = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",3)'>3</button>";
        var num4 = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",4)'>4</button>";
        var num5 = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",5)'>5</button>";
        var num6 = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",6)'>6</button>";
        var num7 = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",7)'>7</button>";
        var num8 = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",8)'>8</button>";
        var num9 = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",9)'>9</button>";
    
        var cadQ = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"Q\")'>Q</button>";
        var cadW = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"W\")'>W</button>";
        var cadE = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"E\")'>E</button>";
        var cadR = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"R\")'>R</button>";
        var cadT = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"T\")'>T</button>";
        var cadY = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"Y\")'>Y</button>";
        var cadU = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"U\")'>U</button>";
        var cadI = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"I\")'>I</button>";
        var cadO = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"O\")'>O</button>";
        var cadP = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"P\")'>P</button>";
    
        var cadA = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"A\")'>A</button>";
        var cadS = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"S\")'>S</button>";
        var cadD = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"D\")'>D</button>";
        var cadF = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"F\")'>F</button>";
        var cadG = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"G\")'>G</button>";
        var cadH = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"H\")'>H</button>";
        var cadJ = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"J\")'>J</button>";
        var cadK = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"K\")'>K</button>";
        var cadL = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"L\")'>L</button>";
    
        var cadZ = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"Z\")'>Z</button>";
        var cadX = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"X\")'>X</button>";
        var cadC = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"C\")'>C</button>";
        var cadV = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"V\")'>V</button>";
        var cadB = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"B\")'>B</button>";
        var cadN = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"N\")'>N</button>";
        var cadM = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"M\")'>M</button>";
    
        var arroba = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"@\")'>@</button>";
        var guion = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"-\")'>-</button>";
        var barraBaja = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"_\")'>_</button>";
    
        var numeral = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"#\")'>#</button>";
        var espacio = "<button class='btnEspaciadora' onclick='fn_agregarCaracter("+$(e).attr("id")+",\" \")'>Espacio</button>";
        var coma = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter("+$(e).attr("id")+",\",\")'>,</button>";
        var punto = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter("+$(e).attr("id")+",\".\")'>.</button>";
    
        var borrarCaracter="<button class='btnVirtualBorrar' onclick='fn_eliminarNumero("+$(e).attr("id")+")'>&larr;</button>";
        var borrarTodo="<button class='btnVirtualBorrar' onclick='fn_eliminarTodo("+$(e).attr("id")+")'>&lArr;</button>";
        var btnOk ="<button id='btn_ok_teclado' class='btnVirtualOK' id='btn_ok_pad' onclick=''>OK</button>";
        var btnCancelar ="<button id='btn_cancelar_teclado' class='btnVirtualCancelar' onclick=''>Cancelar</button>";
    
        $("#keyboard").css({
            display: "block",
            position: "absolute"
        });
    
        $("#keyboard").append(num1 + num2 + num3 + num4 + num5 + num6 + num7 + num8 + num9 + num0 + borrarCaracter + "<br/>" + cadQ + cadW + cadE + cadR + cadT + cadY + cadU + cadI + cadO + cadP + borrarTodo + "<br/>" + arroba + cadA + cadS + cadD + cadF + cadG + cadH + cadJ + cadK + cadL + guion + "<br/>" + numeral + barraBaja + cadZ + cadX + cadC + cadV + cadB + cadN + cadM + coma + punto + "<br/>" + btnCancelar + espacio + btnOk);
    
}


function fn_alfaNumericoCanje(e) {
    if (!$(e.target).closest("#keyboard").length) {
        toggleDiv('keyboard');
    }

    $("#keyboard").empty();
    var posicion = $(e).position();
    var leftPos = 155;
    var topPos = 300;
    leftPos:posicion.left;
    topPos:posicion.top;

    var num0 = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",0)'>0</button>";
    var num1 = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",1)'>1</button>";
    var num2 = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",2)'>2</button>";
    var num3 = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",3)'>3</button>";
    var num4 = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",4)'>4</button>";
    var num5 = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",5)'>5</button>";
    var num6 = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",6)'>6</button>";
    var num7 = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",7)'>7</button>";
    var num8 = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",8)'>8</button>";
    var num9 = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",9)'>9</button>";

    var cadQ = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"Q\")'>Q</button>";
    var cadW = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"W\")'>W</button>";
    var cadE = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"E\")'>E</button>";
    var cadR = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"R\")'>R</button>";
    var cadT = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"T\")'>T</button>";
    var cadY = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"Y\")'>Y</button>";
    var cadU = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"U\")'>U</button>";
    var cadI = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"I\")'>I</button>";
    var cadO = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"O\")'>O</button>";
    var cadP = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"P\")'>P</button>";

    var cadA = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"A\")'>A</button>";
    var cadS = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"S\")'>S</button>";
    var cadD = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"D\")'>D</button>";
    var cadF = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"F\")'>F</button>";
    var cadG = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"G\")'>G</button>";
    var cadH = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"H\")'>H</button>";
    var cadJ = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"J\")'>J</button>";
    var cadK = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"K\")'>K</button>";
    var cadL = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"L\")'>L</button>";

    var cadZ = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"Z\")'>Z</button>";
    var cadX = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"X\")'>X</button>";
    var cadC = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"C\")'>C</button>";
    var cadV = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"V\")'>V</button>";
    var cadB = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"B\")'>B</button>";
    var cadN = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"N\")'>N</button>";
    var cadM = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"M\")'>M</button>";

    var arroba = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"@\")'>@</button>";
    var guion = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"-\")'>-</button>";
    var barraBaja = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"_\")'>_</button>";

    var numeral = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"#\")'>#</button>";
    var espacio = "<button class='btnEspaciadora' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\" \")'>Espacio</button>";
    var coma = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\",\")'>,</button>";
    var punto = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\".\")'>.</button>";

    var borrarCaracter = "<button class='btnVirtualBorrar' onclick='fn_eliminarNumero(" + $(e).attr("id") + ")'>&larr;</button>";
    var borrarTodo = "<button class='btnVirtualBorrar' onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>&lArr;</button>";

    var btnOk = "<button class='btnVirtualOKpq' onclick='fn_btnOk(" + $("#keyboard").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";
//    var btnCancelar ="<button class='btnVirtualCancelar' onclick='fn_cerrarDialogoAnulacion();'>Cancelar</button>";
    var btnCancelar = "<button class='btnVirtualCancelar' onclick='fn_cerrarDialogoAnulacion(" + $(e).attr("id") + ");'>Cancelar</button>";

    $("#keyboard").css({
        display: "block",
        position: "absolute"
    });

    $("#keyboard").append(num1+num2+num3+num4+num5+num6+num7+num8+num9+num0+borrarCaracter+"<br/>"+cadQ+cadW+cadE+cadR+cadT+cadY+cadU+cadI+cadO+cadP+borrarTodo+"<br/>"+arroba+cadA+cadS+cadD+cadF+cadG+cadH+cadJ+cadK+cadL+guion+"<br/>"+numeral+barraBaja+cadZ+cadX+cadC+cadV+cadB+cadN+cadM+coma+punto+"<br/>"+btnCancelar+espacio+btnOk);
}

/////////////////////////////CREAR TECLADO NUMERICO/////////////////////////////////
function fn_numerico(e){
    //if (!$(e.target).closest("#numPad").length){toggleDiv('numPad');}

    $("#numPad").empty();
    var posicion = $(e).position();

    var leftPos=590;
    var topPos=300;
    leftPos:posicion.left;
    topPos:posicion.top;

    var num0 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"0\")'>0</button>";
    var num1 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"1\")'>1</button>";
    var num2 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"2\")'>2</button>";
    var num3 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"3\")'>3</button>";
    var num4 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"4\")'>4</button>";
    var num5 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"5\")'>5</button>";
    var num6 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"6\")'>6</button>";
    var num7 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"7\")'>7</button>";
    var num8 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"8\")'>8</button>";
    var num9 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"9\")'>9</button>";

    var borrarCaracter="<button class='btnVirtualOKpq' onclick='fn_eliminarNumero("+$(e).attr("id")+");'>&larr;</button>";
    var borrarTodo="<button class='btnVirtualOKpq' onclick='fn_eliminarTodo("+$(e).attr("id")+");'>&lArr;</button>";
    var btnOk ="<button class='btnVirtualOKpq' onclick='fn_btnOk("+$("#numPad").attr("id")+", "+$(e).attr("id")+");'>OK</button>";
//    var btnCancelar ="<button class='btnVirtualCancelar' onclick='fn_cerrarDialogoAnulacion();'>Cancelar</button>";
    var btnCancelar ="<button class='btnVirtualCancelar' onclick='fn_cerrarDialogoAnulacion("+$(e).attr("id")+");'>Cancelar</button>";

    $("#numPad").css({
        display:"block",
        position: "absolute"
    });

    $("#numPad").append(num7+num8+num9+borrarCaracter+"<br/>"+num4+num5+num6+borrarTodo+"<br/>"+num1+num2+num3+btnOk+"<br/>"+num0+btnCancelar+"<br/>");	
}



/////////////////////////////CREAR TECLADO NUMERICO PARA MODAL CREDENCIALES DESCUENTOS/////////////////////////////////
function fn_numericoCredenciales(e, dato){
    $("#numPadCredenciales").empty();
    var posicion = $(e).position();
    var leftPos=590;
    var topPos=300;
    leftPos:posicion.left;
    topPos:posicion.top;

    var num0 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"0\")'>0</button>";
    var num1 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"1\")'>1</button>";
    var num2 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"2\")'>2</button>";
    var num3 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"3\")'>3</button>";
    var num4 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"4\")'>4</button>";
    var num5 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"5\")'>5</button>";
    var num6 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"6\")'>6</button>";
    var num7 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"7\")'>7</button>";
    var num8 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"8\")'>8</button>";
    var num9 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum("+$(e).attr("id")+",\"9\")'>9</button>";

    var borrarCaracter="<button class='btnVirtualOKpq' onclick='fn_eliminarNumero("+$(e).attr("id")+");'>&larr;</button>";
    var borrarTodo="<button class='btnVirtualOKpq' onclick='fn_eliminarTodo("+$(e).attr("id")+");'>&lArr;</button>";
    var btnOk ="<button class='btnVirtualOKpq' onclick='fn_validar_usuario("+dato+")'>OK</button>";
//    var btnCancelar ="<button class='btnVirtualCancelar' onclick='fn_cerrarDialogoAnulacion();'>Cancelar</button>";
    var btnCancelar ="<button class='btnVirtualCancelar' onclick='fn_cerrarDialogoDescuentosContenedor()'>Cancelar</button>";

    $("#numPadCredenciales").css({
        display:"block",
        position: "absolute"
    });

    $("#numPadCredenciales").append(num7+num8+num9+borrarCaracter+"<br/>"+num4+num5+num6+borrarTodo+"<br/>"+num1+num2+num3+btnOk+"<br/>"+num0+btnCancelar+"<br/>");	
}





/////////////////////////////CREAR TECLADO ALFABETICO/////////////////////////////////
function fn_letras(e){
    if(!$(e.target).closest("#txtPad").length){toggleDiv('txtPad');}

    $("#txtPad").empty();
    var posicion = $(e).position();

    var leftPos=15;
    if(posicion.left>900){
            leftPos=posicion.left-700;
    }else{
            leftPos:posicion.left;
    }

    var cadQ = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"Q\")'>Q</button>";
    var cadW = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"W\")'>W</button>";
    var cadE = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"E\")'>E</button>";
    var cadR = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"R\")'>R</button>";
    var cadT = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"T\")'>T</button>";
    var cadY = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"Y\")'>Y</button>";
    var cadU = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"U\")'>U</button>";
    var cadI = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"I\")'>I</button>";
    var cadO = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"O\")'>O</button>";
    var cadP = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"P\")'>P</button>";

    var cadA = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"A\")'>A</button>";
    var cadS = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"S\")'>S</button>";
    var cadD = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"D\")'>D</button>";
    var cadF = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"F\")'>F</button>";
    var cadG = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"G\")'>G</button>";
    var cadH = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"H\")'>H</button>";
    var cadJ = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"J\")'>J</button>";
    var cadK = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"K\")'>K</button>";
    var cadL = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"L\")'>L</button>";

    var cadZ = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"Z\")'>Z</button>";
    var cadX = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"X\")'>X</button>";
    var cadC = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"C\")'>C</button>";
    var cadV = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"V\")'>V</button>";
    var cadB = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"B\")'>B</button>";
    var cadN = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"N\")'>N</button>";
    var cadM = "<button class='btnVirtual' onclick='fn_agregarCaracter("+$(e).attr("id")+",\"M\")'>M</button>";

    var borrarCaracter="<button class='btnVirtualBorrar' onclick='fn_eliminarNumero("+$(e).attr("id")+");'>&larr;</button>";
    var borrarTodo="<button class='btnVirtualBorrar' onclick='fn_eliminarTodo("+$(e).attr("id")+");'>&lArr;</button>";
    var btnOk ="<button class='btnVirtualOKpq' onclick='fn_btnOk("+$("#txtPad").attr("id")+", "+$(e).attr("id")+");'>OK</button>";
    var espacio = "<button class='btnEspaciadoraGr' onclick='fn_agregarCaracter("+$(e).attr("id")+",' ');'>Espacio</button>";

    $("#txtPad").css({
        display:"block",
        position: "absolute"
    });

    $("#txtPad").append(cadQ+cadW+cadE+cadR+cadT+cadY+cadU+cadI+cadO+cadP+"<br/>"+cadA+cadS+cadD+cadF+cadG+cadH+cadJ+cadK+cadL+borrarCaracter+"<br/>"+cadZ+cadX+cadC+cadV+cadB+cadN+cadM+borrarTodo+btnOk+"<br/>"+espacio);
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
    $(e).hide();
    $(e).empty();
    $(p).trigger("change");
}

/////////////////////////////OCULTAR DIVS///////////////////////////////////////////
function fn_ocultar(){
    $("#txtPad").hide();
}