var coma = 0;
var decimal = 0;
$(document).ready(function(){    
    $("#keyboardCliente").hide();
    $("#numPadCliente").hide();
    $("#txtPadCliente").hide();
    $("#dominio1").hide();
    $("#dominio2").hide();
    //$("#tecladoAdmin").hide();
});

/////////////////////////////CERRAR INACTIVO/////////////////////////////////
function toggleDiv(divID){
    $("#" + divID).fadeToggle(200, function() {
        var openDiv = $(this).is(':visible') ? divID : null;
    });
}

/* TECLADO ALFANUMERICO COMPLETO */
function fn_tecladoAlfanumerico(e){
    if (!$(e.target).closest("#keyboardCliente").length){}
    
    if(($(e).attr("id")) == 'txtCorreo'){
        $("#dominio1").css({
            display: "block",
            position: "absolute"
        });
        $("#dominio2").css({
            display: "block",
            position: "absolute"
        });
    }
    else{
        $("#dominio1").css({
            display: "none",
            position: "absolute"
        });
        $("#dominio2").css({
            display: "none",
            position: "absolute"
        });
    }

    $("#keyboardCliente").empty();
    var posicion = $(e).position();
    var leftPos = 155;
    var topPos = 250;
    
    leftPos:posicion.left;
    topPos:posicion.top;
		
    var num0 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",0)'>0</button>";
    var num1 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",1)'>1</button>";
    var num2 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",2)'>2</button>";
    var num3 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",3)'>3</button>";
    var num4 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",4)'>4</button>";
    var num5 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",5)'>5</button>";
    var num6 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",6)'>6</button>";
    var num7 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",7)'>7</button>";
    var num8 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",8)'>8</button>";
    var num9 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",9)'>9</button>";

    var cadQ = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"Q\")'>Q</button>";
    var cadW = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"W\")'>W</button>";
    var cadE = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"E\")'>E</button>";
    var cadR = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"R\")'>R</button>";
    var cadT = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"T\")'>T</button>";
    var cadY = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"Y\")'>Y</button>";
    var cadU = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"U\")'>U</button>";
    var cadI = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"I\")'>I</button>";
    var cadO = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"O\")'>O</button>";
    var cadP = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"P\")'>P</button>";

    var cadA = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"A\")'>A</button>";
    var cadS = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"S\")'>S</button>";
    var cadD = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"D\")'>D</button>";
    var cadF = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"F\")'>F</button>";
    var cadG = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"G\")'>G</button>";
    var cadH = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"H\")'>H</button>";
    var cadJ = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"J\")'>J</button>";
    var cadK = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"K\")'>K</button>";
    var cadL = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"L\")'>L</button>";

    var cadZ = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"Z\")'>Z</button>";
    var cadX = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"X\")'>X</button>";
    var cadC = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"C\")'>C</button>";
    var cadV = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"V\")'>V</button>";
    var cadB = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"B\")'>B</button>";
    var cadN = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"N\")'>N</button>";
    var cadM = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"M\")'>M</button>";

    var arroba = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"@\")'>@</button>";
    var guion = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"-\")'>-</button>";
    var barraBaja = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"_\")'>_</button>";

    var numeral = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"#\")'>#</button>";
    var espacio = "";
    espacio = "<button class='btnEspaciadoraCDC' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\" \")'>Espacio</button>";
    var cadEnie = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"&Ntilde;\")'>&Ntilde;</button>";
    var coma = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\",\")'>,</button>";
    var punto = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\".\")'>.</button>";

    var borrarCaracter="<button class='btnVirtualBorrar' onclick='fn_eliminarNumero("+$(e).attr("id")+")'>&larr;</button>";
    var borrarTodo="<button class='btnVirtualBorrar' onclick='fn_eliminarTodo("+$(e).attr("id")+")'>&lArr;</button>";
    var btnOk ="<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_ocultarTecladoAlfanumerico("+$("#keyboardCliente").attr("id")+", "+$(e).attr("id")+");'>OK</button>";
    var btnBorrarTodo ="<button class='btnVirtualBorrarTodo' id='btn_ok_borrar_todo'  onclick='fn_eliminarTodo("+$(e).attr("id")+")'>Borrar</button>";

    if(($(e).attr("id")) == 'txtCorreo'){
        $("#dominio1").css({
            display: "block",
            position: "absolute"
        });
        $("#dominio2").css({
            display: "block",
            position: "absolute"
        });
        cadEnie = "<button class='btnVirtual' >&Ntilde;</button>";
    }
    else{
        $("#dominio1").css({
            display: "none",
            position: "absolute"
        });
        $("#dominio2").css({
            display: "none",
            position: "absolute"
        });
    }

    $("#keyboardCliente").css({
        display: "block",
        position: "absolute"
    });		

    $("#keyboardCliente").append(num1+num2+num3+num4+num5+num6+num7+num8+num9+num0+borrarCaracter+"<br/>"+cadQ+cadW+cadE+cadR+cadT+cadY+cadU+cadI+cadO+cadP+borrarTodo+"<br/>"+arroba+cadA+cadS+cadD+cadF+cadG+cadH+cadJ+cadK+cadL+guion+"<br/>"+numeral+barraBaja+cadZ+cadX+cadC+cadV+cadB+cadN+cadM+cadEnie+punto+"<br/>"+btnBorrarTodo+espacio+btnOk);
}

function fn_alfanumericoCliente(e){	
    $("#dominio1").css({
        display: "none",
        position: "absolute"
    });
    
    $("#dominio2").css({
        display: "none",
        position: "absolute"
    });
	
    if (!$(e.target).closest("#keyboardCliente").length){
    }

    $("#keyboardCliente").empty();
    var posicion = $(e).position();
    var leftPos = 155;
    var topPos = 250;
    
    leftPos:posicion.left;
    topPos:posicion.top;
		
    var num0 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",0)'>0</button>";
    var num1 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",1)'>1</button>";
    var num2 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",2)'>2</button>";
    var num3 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",3)'>3</button>";
    var num4 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",4)'>4</button>";
    var num5 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",5)'>5</button>";
    var num6 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",6)'>6</button>";
    var num7 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",7)'>7</button>";
    var num8 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",8)'>8</button>";
    var num9 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",9)'>9</button>";

    var cadQ = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"Q\")'>Q</button>";
    var cadW = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"W\")'>W</button>";
    var cadE = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"E\")'>E</button>";
    var cadR = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"R\")'>R</button>";
    var cadT = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"T\")'>T</button>";
    var cadY = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"Y\")'>Y</button>";
    var cadU = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"U\")'>U</button>";
    var cadI = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"I\")'>I</button>";
    var cadO = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"O\")'>O</button>";
    var cadP = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"P\")'>P</button>";

    var cadA = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"A\")'>A</button>";
    var cadS = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"S\")'>S</button>";
    var cadD = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"D\")'>D</button>";
    var cadF = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"F\")'>F</button>";
    var cadG = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"G\")'>G</button>";
    var cadH = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"H\")'>H</button>";
    var cadJ = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"J\")'>J</button>";
    var cadK = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"K\")'>K</button>";
    var cadL = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"L\")'>L</button>";

    var cadZ = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"Z\")'>Z</button>";
    var cadX = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"X\")'>X</button>";
    var cadC = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"C\")'>C</button>";
    var cadV = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"V\")'>V</button>";
    var cadB = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"B\")'>B</button>";
    var cadN = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"N\")'>N</button>";
    var cadM = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"M\")'>M</button>";

    var arroba = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"@\")'>@</button>";
    var guion = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"-\")'>-</button>";
    var barraBaja = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"_\")'>_</button>";

    var numeral = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"#\")'>#</button>";
    var espacio = "";
    espacio = "<button class='btnEspaciadoraCDC' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\" \")'>Espacio</button>";
    var cadEnie = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"&Ntilde;\")'>&Ntilde;</button>";
    var coma = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\",\")'>,</button>";
    var punto = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\".\")'>.</button>";

    borrarCaracter="<button class='btnVirtualBorrar' onclick='fn_eliminarNumero("+$(e).attr("id")+")'>&larr;</button>";
    borrarTodo="<button class='btnVirtualBorrar' onclick='fn_eliminarTodo("+$(e).attr("id")+")'>&lArr;</button>";
    btnOk ="<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_ocultarTecladoAlfanumerico("+$("#keyboardCliente").attr("id")+", "+$(e).attr("id")+");'>OK</button>";
    btnBorrarTodo ="<button class='btnVirtualBorrarTodo' id='btn_ok_borrar_todo'  onclick='fn_eliminarTodo("+$(e).attr("id")+")'>Borrar</button>";

    $("#keyboardCliente").css({
        display: "block",
        position: "absolute"
    });		

    $("#keyboardCliente").append(num1+num2+num3+num4+num5+num6+num7+num8+num9+num0+borrarCaracter+"<br/>"+cadQ+cadW+cadE+cadR+cadT+cadY+cadU+cadI+cadO+cadP+borrarTodo+"<br/>"+arroba+cadA+cadS+cadD+cadF+cadG+cadH+cadJ+cadK+cadL+guion+"<br/>"+numeral+barraBaja+cadZ+cadX+cadC+cadV+cadB+cadN+cadM+cadEnie+punto+"<br/>"+btnBorrarTodo+espacio+btnOk);
}

function fn_alfanumericoTelefono(e){
    $("#dominio1").css({
        display: "none",
        position: "absolute"
    });
    
    $("#dominio2").css({
        display: "none",
        position: "absolute"
    });

    if (!$(e.target).closest("#keyboardCliente").length){}

    $("#keyboardCliente").empty();
    var posicion = $(e).position();
    var leftPos = 155;
    var topPos = 250;
    
    leftPos:posicion.left;
    topPos:posicion.top;

    var num0 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",0)'>0</button>";
    var num1 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",1)'>1</button>";
    var num2 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",2)'>2</button>";
    var num3 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",3)'>3</button>";
    var num4 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",4)'>4</button>";
    var num5 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",5)'>5</button>";
    var num6 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",6)'>6</button>";
    var num7 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",7)'>7</button>";
    var num8 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",8)'>8</button>";
    var num9 = "<button class='btnVirtual' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",9)'>9</button>";
	
    var cadQ = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"Q\")'>Q</button>";
    var cadW = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"W\")'>W</button>";
    var cadE = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"E\")'>E</button>";
    var cadR = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"R\")'>R</button>";
    var cadT = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"T\")'>T</button>";
    var cadY = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"Y\")'>Y</button>";
    var cadU = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"U\")'>U</button>";
    var cadI = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"I\")'>I</button>";
    var cadO = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"O\")'>O</button>";
    var cadP = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"P\")'>P</button>";

    var cadA = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"A\")'>A</button>";
    var cadS = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"S\")'>S</button>";
    var cadD = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"D\")'>D</button>";
    var cadF = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"F\")'>F</button>";
    var cadG = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"G\")'>G</button>";
    var cadH = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"H\")'>H</button>";
    var cadJ = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled'onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"J\")'>J</button>";
    var cadK = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"K\")'>K</button>";
    var cadL = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"L\")'>L</button>";

    var cadZ = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"Z\")'>Z</button>";
    var cadX = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"X\")'>X</button>";
    var cadC = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"C\")'>C</button>";
    var cadV = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"V\")'>V</button>";
    var cadB = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"B\")'>B</button>";
    var cadN = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"N\")'>N</button>";
    var cadM = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"M\")'>M</button>";		

    var arroba = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"@\")'>@</button>";
    var guion = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"-\")'>-</button>";
    var barraBaja = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"_\")'>_</button>";

    var numeral = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"#\")'>#</button>";
    var espacio = "";
    espacio = "<button disabled='disabled' class='btnEspaciadoraCDC, btnEspaciadora_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\" \")'>Espacio</button>";
    var cadEnie = "<button  disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\"&Ntilde;\")'>&Ntilde;</button>";
    var coma = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\",\")'>,</button>";
    var punto = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterCDC("+$(e).attr("id")+",\".\")'>.</button>";

    var borrarCaracter="<button class='btnVirtualBorrar' onclick='fn_eliminarNumero("+$(e).attr("id")+")'>&larr;</button>";
    var borrarTodo="<button class='btnVirtualBorrar' onclick='fn_eliminarTodo("+$(e).attr("id")+")'>&lArr;</button>";
    var btnOk ="<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_ocultarTecladoAlfanumerico("+$("#keyboardCliente").attr("id")+", "+$(e).attr("id")+");'>OK</button>";
    var btnBorrarTodo ="<button class='btnVirtualBorrarTodo' id='btn_ok_borrar_todo'  onclick='fn_eliminarTodo("+$(e).attr("id")+")'>Borrar</button>";

    $("#keyboardCliente").css({
        display: "block",
        position: "absolute"
    });		

    $("#keyboardCliente").append(num1+num2+num3+num4+num5+num6+num7+num8+num9+num0+borrarCaracter+"<br/>"+cadQ+cadW+cadE+cadR+cadT+cadY+cadU+cadI+cadO+cadP+borrarTodo+"<br/>"+arroba+cadA+cadS+cadD+cadF+cadG+cadH+cadJ+cadK+cadL+guion+"<br/>"+numeral+barraBaja+cadZ+cadX+cadC+cadV+cadB+cadN+cadM+cadEnie+punto+"<br/>"+btnBorrarTodo+espacio+btnOk);
}

/* TECLADO PARA DIGITAR EL DOCUMENTO DE IDENTIFICACIÓN */
function fn_tecladoIdentificacion(e){
    if (!$(e.target).closest("#numPadClienteAdmin").length){}

    $("#numPadCliente").empty();
    var posicion = $(e).position();
    var leftPos = 310;
    var topPos = 110;
    
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

    var borrarCaracter="<button class='btnVirtualBorrar' onclick='fn_eliminarNumero("+$(e).attr("id")+");'>&larr;</button>";
    var borrarTodo="<button class='btnVirtualOKpq' onclick='fn_eliminarTodo("+$(e).attr("id")+");'>&lArr;</button>";
    var btnOk ="<button class='btnVirtualOKpq' onclick='fn_ocultarTecladoAlfanumerico("+$("#numPadCliente").attr("id")+", "+$(e).attr("id")+");'>OK</button>";

    $("#numPadCliente").css({
        display:"block",
        position: "absolute",
        top:topPos,
        left:leftPos
    });

    $("#numPadCliente").append(num7+num8+num9+"<br/>"+num4+num5+num6+"<br/>"+num1+num2+num3+"<br/>"+num0+btnOk+"<br/>"+borrarCaracter+borrarTodo);    
}

function fn_tecladoCredenciales(e){   
    if (!$(e.target).closest("#tecladoAdmin").length){/*toggleDiv('tecladoAdmin');*/}

    $("#tecladoAdmin").empty();
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
    var btnOk ="<button class='btnVirtualOKpq' onclick='fn_btnOk("+$("#tecladoAdmin").attr("id")+", "+$(e).attr("id")+");'>OK</button>";
    var btnCancelar ="<button class='btnVirtualCancelar' onclick='fn_cerrarDialogoAdmin("+$(e).attr("id")+");'>Cancelar</button>";

    $("#tecladoAdmin").css({
        display:"block",
        position: "absolute"
    });

    $("#tecladoAdmin").append(num7+num8+num9+borrarCaracter+"<br/>"+num4+num5+num6+borrarTodo+"<br/>"+num1+num2+num3+btnOk+"<br/>"+num0+btnCancelar+"<br/>");	
}

function fn_agregarCaracterCorreo(objeto,valor){
    var lc_cantidad = $(objeto).val();	

    if(isNaN(valor)){
        var posicionArroba=lc_cantidad.indexOf('@');
        if(posicionArroba!=-1)
        {                
            var subtr = lc_cantidad.substring(0, posicionArroba);
            $(objeto).val(subtr+valor);
        } else {
            $(objeto).val(lc_cantidad+valor);
        }
    }
}

/////////////////////////////AGREGAR NUMERO/////////////////////////////////
function fn_agregarCaracterCDC(obj, valor){    
    //if (document.getElementById("txtClienteNombre").disabled != true){
        var lc_cantidad = $(obj).val();
    
        if($(obj).attr('id')!="txtCorreo") {
            if(lc_cantidad =='' && valor==".") {		
                //si escribimos una coma al principio del n�mero
                $(obj).val('0.');
                coma = 1;	
            } else { 
                //continuar escribiendo un n�mero
                if (valor=="." && coma==0){ 			
                    //si escribimos una coma decimal p�r primera vez            
                    lc_cantidad = lc_cantidad+valor;
                    $(obj).val(lc_cantidad);			
                    coma=1; //cambiar el estado de la coma  
                }
                //si intentamos escribir una segunda coma decimal no realiza ninguna acci�n.
                else if (valor=="." && coma==1) {} 
                //Resto de casos: escribir un n�mero del 0 al 9: 	 
                else{			
                    lc_cantidad = lc_cantidad+valor;
                    var id = $(obj).attr('id');

                    if(id=='txtClienteFono'){
                        if((lc_cantidad.length<11)){
                            $(obj).val(lc_cantidad);								
                        }
                    } else if(id=='pagado') {					
                        if(coma==1){
                            var validador = lc_cantidad.indexOf('.');                        
                            if(validador!=-1){
                                var splitt = lc_cantidad.split(".");	
                                var dec = splitt[1];
                                if(dec.length<3){
                                    $(obj).val(lc_cantidad);		
                                }						
                            } else {
                                coma = 0;
                                $(obj).val(lc_cantidad);
                            }
                        } else {						
                            if(lc_cantidad == 0){
                                $(obj).val('0.');
                                coma=1;	
                            } else {
                                $(obj).val(lc_cantidad);												
                            }
                        }
                    } else {
                        
                        /*if($(obj).attr('id') == "txtClienteCI"){
                            if (lc_cantidad.length => 15){
                                $(obj).val(lc_cantidad); 
                            }
                        } else {
                            $(obj).val(lc_cantidad); 
                        }*/
                        
                        $(obj).val(lc_cantidad); 
                        	
                    }
                }
            }	
        } else {       
            lc_cantidad = lc_cantidad+valor;
            $(obj).val(lc_cantidad);
        }
    //}  
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

    if (lc_cantidad==""){
       lc_cantidad="";
       coma = 0;
    }
    
    if (lc_cantidad=="."){
        coma = 0;
    }	
}

/////////////////////////////ELIMINAR TODO LA CADENA///////////////////////////
function fn_eliminarTodo(e){
    $(e).val(''); 
    if($(e).attr('id') == 'txt_cliAx'){		
        $("#detalleAx").empty();
    }
}

function fn_ocultarTecladoAlfanumerico(e, p){
    $(e).hide();
    $(e).empty();
    $(p).focus();
    $(p).trigger("change");
}

function fn_ocultar(){
    $("#txtPadCliente").hide();	
}

function fn_ocultar_alfanumerico(){	
    $("#keyboardCliente").hide();
}

function fn_ver_alfanumerico(){	
    $("#keyboardCliente").show();
}

function fn_ver_numerico(){	
    $("#numPadCliente").show();
}



