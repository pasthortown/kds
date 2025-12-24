/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function pay_numerico(e, valid, url) {
    //if (!$(e.target).closest("#numPad").length){toggleDiv('numPad');}

    $("#numPad").empty();
    $("#keyboard").empty();

    $("#dominio1").empty();
    $("#dominio2").empty();

    $("#keyboard").css({
        display: "none"
    });

    var posicion = $(e).position();

    var leftPos = 590;
    var topPos = 300;
    leftPos:posicion.left;
    topPos:posicion.top;

    var num0 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"0\")'>0</button>";
    var num1 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"1\")'>1</button>";
    var num2 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"2\")'>2</button>";
    var num3 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"3\")'>3</button>";
    var num4 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"4\")'>4</button>";
    var num5 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"5\")'>5</button>";
    var num6 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"6\")'>6</button>";
    var num7 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"7\")'>7</button>";
    var num8 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"8\")'>8</button>";
    var num9 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"9\")'>9</button>";

    var borrarCaracter = "<button class='btnVirtualOKpq' onclick='fn_eliminarNumero(" + $(e).attr("id") + ");'>&larr;</button>";
    var borrarTodo = "<button class='btnVirtualOKpq' onclick='fn_eliminarTodo(" + $(e).attr("id") + ");'>&lArr;</button>";
    var btnOk = "<button class='btnVirtualOKpq' onclick='fn_btnOk_(" + $("#numPad").attr("id") + ", " + $(e).attr("id") + ", "+ valid + ", " +  url + ");'>OK</button>";
//    var btnCancelar ="<button class='btnVirtualCancelar' onclick='fn_cerrarDialogoAnulacion();'>Cancelar</button>";
    var btnCancelar = "<button class='btnVirtualCancelar' onclick='cancelarTeclado();'>Cancelar</button>";

    $("#numPad").css({
        display: "block",
        position: "absolute",
        top: "90px",
        left: "600px"
    });

    $("#numPad").append(num7 + num8 + num9 + borrarCaracter + "<br/>" + num4 + num5 + num6 + borrarTodo + "<br/>" + num1 + num2 + num3 + btnOk + "<br/>" + num0 + btnCancelar + "<br/>");
}

function pay_numericoCliente(selector, valid = null, url = null) {
    var $input = $(selector);
    var $container = $input.closest('.modal__container, .modal-dialog').find('.teclado_clientes');
    var $numPad = $container.find(".numPad");


    // Limpiar el contenido previo
    $numPad.empty();
    $container.find(".numPad").empty();
    $container.find(".keyboard").empty();
    $container.find(".dominio5").empty();
    $container.find(".dominio6").empty();
    $container.find(".dominio5").hide();
    $container.find(".dominio6").hide();

    $container.find(".keyboard").css({
        display: "none"
    });

    $("#keyboard").css({
        display: "none"
    });


    var posicion = $input.position();

    // Opciones de teclado
    var num0 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $input.attr("id") + ",\"0\")'>0</button>";
    var num1 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $input.attr("id") + ",\"1\")'>1</button>";
    var num2 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $input.attr("id") + ",\"2\")'>2</button>";
    var num3 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $input.attr("id") + ",\"3\")'>3</button>";
    var num4 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $input.attr("id") + ",\"4\")'>4</button>";
    var num5 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $input.attr("id") + ",\"5\")'>5</button>";
    var num6 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $input.attr("id") + ",\"6\")'>6</button>";
    var num7 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $input.attr("id") + ",\"7\")'>7</button>";
    var num8 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $input.attr("id") + ",\"8\")'>8</button>";
    var num9 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $input.attr("id") + ",\"9\")'>9</button>";

    var borrarCaracter = "<button class='btnVirtualOKpq' onclick='fn_eliminarNumero(" + $input.attr("id") + ");'>&larr;</button>";
    var borrarTodo = "<button class='btnVirtualOKpq' onclick='fn_eliminarTodo(" + $input.attr("id") + ");'>&lArr;</button>";
    var btnOk = "<button class='btnVirtualOKpq' onclick='fn_btnOk_(" + $container.find(".numPad").attr("id") + ", " + $input.attr("id") + ", "+ valid + ", " +  url + ");'>OK</button>";
    var btnCancelar = "<button class='btnVirtualCancelar' onclick='cancelarTeclados();'>Cancelar</button>";

    // Mostrar teclado virtual
    $numPad.css({
        display: "block", // Asegúrate de que esté visible
        margin: "0px 85px",
        padding: "10px 0px"
    });


    $container.find(".numPad").append(num7 + num8 + num9 + borrarCaracter + "<br/>" + num4 + num5 + num6 + borrarTodo + "<br/>" + num1 + num2 + num3 + btnOk + "<br/>" + num0 + btnCancelar + "<br/>");
}


function pay_numericoClienteBeneficio(e, valid, url) {

    $("#numPad2").empty();

    var posicion = $(e).position();

    var leftPos = 590;
    var topPos = 300;
    leftPos:posicion.left;
    topPos:posicion.top;

    var num0 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"0\")'>0</button>";
    var num1 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"1\")'>1</button>";
    var num2 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"2\")'>2</button>";
    var num3 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"3\")'>3</button>";
    var num4 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"4\")'>4</button>";
    var num5 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"5\")'>5</button>";
    var num6 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"6\")'>6</button>";
    var num7 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"7\")'>7</button>";
    var num8 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"8\")'>8</button>";
    var num9 = "<button class='btnVirtual' onclick='fn_agregarCaracterNum(" + $(e).attr("id") + ",\"9\")'>9</button>";

    var borrarCaracter = "<button class='btnVirtualOKpq' onclick='fn_eliminarNumero(" + $(e).attr("id") + ");'>&larr;</button>";
    var borrarTodo = "<button class='btnVirtualOKpq' onclick='fn_eliminarTodo(" + $(e).attr("id") + ");'>&lArr;</button>";
    var btnOk = "<button class='btnVirtualOKpq' onclick='fn_btnOk_(" + $("#numPad2").attr("id") + ", " + $(e).attr("id") + ", "+ valid + ", " +  url + ");'>OK</button>";
    var btnCancelar = "<button class='btnVirtualCancelar' onclick='cancelarTeclado();'>Cancelar</button>";

    $("#numPad2").css({
        display: "block"
    });

    $("#numPad2").append(num7 + num8 + num9 + borrarCaracter + "<br/>" + num4 + num5 + num6 + borrarTodo + "<br/>" + num1 + num2 + num3 + btnOk + "<br/>" + num0 + btnCancelar + "<br/>");
}

function pay_alfaNumericoCliente(e) {

    //    if (!$(e.target).closest("#keyboard").length) {
    //        toggleDiv('keyboard');
    //    }
    $("#numPad2").empty();
    $("#keyboard").empty();
    $("#dominio5").empty();
    $("#dominio6").empty();
    $("#dominio5").hide();
    $("#dominio6").hide();
    $("#numPad2").css({
        display: "none"
    });

    $(".numPad").css({
        display: "none"
    });
    
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
    var btnOk = "<button id='btn_ok_teclado' class='btnVirtualOK' id='btn_ok_pad' onclick='cancelarTeclado()'>OK</button>";
    var btnCancelar = "<button id='btn_cancelar_teclado' class='btnVirtualCancelar' onclick='cancelarTeclado()'>Cancelar</button>";

    $("#keyboard").css({
        display: "block",
        position: "absolute",
        left: "260px",
        top: "455px"
    });

    $("#keyboard").append(num1 + num2 + num3 + num4 + num5 + num6 + num7 + num8 + num9 + num0 + borrarCaracter + "<br/>" + cadQ + cadW + cadE + cadR + cadT + cadY + cadU + cadI + cadO + cadP + borrarTodo + "<br/>" + arroba + cadA + cadS + cadD + cadF + cadG + cadH + cadJ + cadK + cadL + guion + "<br/>" + numeral + barraBaja + cadZ + cadX + cadC + cadV + cadB + cadN + cadM + coma + punto + "<br/>" + btnCancelar + espacio + btnOk);
}

function pay_alfaNumerico(e) {

//    if (!$(e.target).closest("#keyboard").length) {
//        toggleDiv('keyboard');
//    }
    $("#numPad").empty();
    $("#keyboard").empty();
    $("#dominio1").empty();
    $("#dominio2").empty();     
    
    $("#keyboard1").css({
        display: "none",
        top: "0",
        left: "0"
    });
    
    
    $("#numPad").css({
        display: "none",
        top: "0",
        left: "0"
    });

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
    var btnOk = "<button id='btn_ok_teclado' class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnOk_(" + $("#numPad").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";
    var btnCancelar = "<button id='btn_cancelar_teclado' class='btnVirtualCancelar' onclick='cancelarTeclado()'>Cancelar</button>";

    $("#keyboard").css({
        display: "block",
        position: "absolute",
        left: "260px",
        top: "455px"
    });

    $("#keyboard").append(num1 + num2 + num3 + num4 + num5 + num6 + num7 + num8 + num9 + num0 + borrarCaracter + "<br/>" + cadQ + cadW + cadE + cadR + cadT + cadY + cadU + cadI + cadO + cadP + borrarTodo + "<br/>" + arroba + cadA + cadS + cadD + cadF + cadG + cadH + cadJ + cadK + cadL + guion + "<br/>" + numeral + barraBaja + cadZ + cadX + cadC + cadV + cadB + cadN + cadM + coma + punto + "<br/>" + btnCancelar + espacio + btnOk);
}

function cancelarTeclado() {
    $("#numPad").empty();
    $("#keyboard").empty();
    $("#numPad").hide();
    $("#keyboard").hide();

    $("#numPad1").empty();
    $("#keyboard1").empty();
    $("#numPad1").hide();
    $("#keyboard1").hide();

    $("#numPad2").empty();
    $("#numPad2").hide();
}

function fn_btnOk_Close(e, p, valid) {
    if(valid){
        console.log(valid);
    }
    $(e).hide();
    $(e).empty();
}

function fn_btnOk_(e, p, valid) {
    if(valid){
        console.log(valid);
    }
    // $(e).hide();
    // $(e).empty();
    // $(p).trigger("change");
    ejecutarAcciones(p.id, e, p, valid);
}

function enfocar(input) {
    var txt = $("#" + input);
    txt.focus();
    txt.click();
}

function ejecutarAcciones(opcion, e, p, valid) {
    switch (opcion) {
        case  'inpCodigoQrAppedirNew':
            //presionamos el boton enter al dar clic en ok
            fn_okQrAppedir();
            $(e).hide();
            $(e).empty();
            $(p).trigger("change");
            break;
        case  'pay_txtCedulaCliente':
            fn_buscarCliente("#pay_txtCedulaCliente", valid);
            $(e).hide();
            $(e).empty();
            $(p).trigger("change");
            break;

        case  'input_cedulaBeneficio':
            fn_obtenerBeneficioCliente();
            $(p).trigger("change");
            break;

        case  'pay_txtTelefono':
            enfocar("pay_txtNombres");
            $(e).hide();
            $(e).empty();
            $(p).trigger("change");
            break;

        case 'pay_txtNombres':

            enfocar("pay_txtCorreo");

            break;

        case 'pay_txtCorreo':
            $("#dominio1").empty();
            $("#dominio2").empty();
            let regexValidation = true;
            if(valid != 0){
                if(localStorage.getItem('intValidaOP') === undefined || localStorage.getItem('intValidaOP') === null){
                    console.log('no existe la config, se procede a cargarla')
                    localStorage.setItem('intValida', valid);
                    localStorage.setItem('intValidaFC', valid);
                    localStorage.setItem('intValidaOP', valid);
                }
               regexValidation =  fn_validaEmailRegisteredAPI($("#pay_txtCorreo").val());
        
            }
            if(!regexValidation){
                enfocar("pay_txtCorreo");
            }else{
                if ($('#datosDomicilio').is(':visible') == true){
                  enfocar("pay_txtDireccion");  
                }else{
                    cancelarTeclado();
                    $("#dominio5").hide();
                    $("#dominio6").hide();
                }
            }
           
            break;


        case 'pay_txtDireccion':
            enfocar("pay_numeroCallePrincipal");
            break;

        case 'pay_numeroCallePrincipal':
            enfocar("pay_calleSecundaria");
            break;

        case 'pay_calleSecundaria':
            enfocar("pay_referenciaTipoInmueble");
            break;

        case 'pay_referenciaTipoInmueble':
            enfocar("pay_referencia");
            break;

        case 'pay_referencia':
//            $(e).hide();
            cancelarTeclado();
            break;

        case  'inpCodigoPromocion':
            verificacionCodigoOtp();
            $(e).hide();
            $(e).empty();
            $(p).trigger("change");
            break;

        default:

            break;
    }
}


function fn_validaEmailRegisteredAPI(email) {
    console.log('aqui viernes api nueva funcion')
    var arr_e = [];
    var datos_existentes = localStorage.getItem('email_valid');
    datos_existentes = datos_existentes == null ? [] : JSON.parse(datos_existentes);
    arr_e = localStorage.getItem('email_valid');
    expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(!email){
        alertify.error("Debe especificar un correo electronico");
        return false;
    }
    if ( !expr.test(email) ){
        alertify.error("El correo especificado no cumple con el formato");
        return false;
    }
    send = {"cargarUrlApiValidaPlugthem": 1, "CustomerEmail": email.toString()};
    $.ajax({type: "POST", dataType: 'json', url: "../ordenpedido/config_ordenPedido.php", data: send}).fail((jqXHR, textStatus, errorThrown) => {
        console.log('error');
    }).done((data, textStatus, jqXHR)=>{
        validateBackgroundEmailPlugThem(data,email,  datos_existentes,arr_e);
    });
    return true;
}
function validateBackgroundEmailPlugThem(data, email, datos_existentes, previusData){
    if(data && data.status){
        if(data.status === "invalid"){
            if(!previusData.includes(email)){
                datos_existentes.push(email);
                console.log('no se ha validado el correo ingresado, reinicio contador')
                //localStorage.removeItem('email_valid');
                localStorage.setItem("intValidaOP", localStorage.getItem("intValidaOP") - 1);
                alertify.alert("El email ingresado no es v\u00e1lido. Por favor vuelva a verificar." + "Le restan " + localStorage.getItem("intValidaOP").toString() + " " + "intentos. Al culminarlos el correo será tomado como válido.");
            }else{
                console.log('resto del actual')
                    localStorage.setItem("intValidaOP", localStorage.getItem("intValidaOP") - 1);
                    if(localStorage.getItem("intValidaOP") > 0) {
                        alertify.alert("El email ingresado no es v\u00e1lido. Por favor vuelva a verificar." + "Le restan " + localStorage.getItem("intValidaOP").toString() + " " + "intentos. Al culminarlos el correo será tomado como válido.");
                    } else if (localStorage.getItem("intValidaOP") == 0)  {
                        alertify.alert("El email ingresado no es v\u00e1lido. Ya agoto sus intentos de verificación, el correo será tomado como válido.");    
                    }
            }
            
            localStorage.setItem('email_valid', JSON.stringify(datos_existentes));
        }
    }
}
 
function fn_alfaNumericoCorreo(e, valid) {

    $("#numPad").empty();
    $("#keyboard").empty();
    $("#dominio1").empty();
    $("#dominio2").empty();    
    
    $("#keyboard").hide();
    $("#keyboard").empty();
    $("#numPadCliente").hide();
    $("#pay_TeladocedulaCliente").hide();
    cargarTeclasCorreo();

    if (!$(e.target).closest("#keyboard").length) {}

    if (($(e).attr("id")) === 'pay_txtCorreo') {
        $("#dominio1").css({
            display: "block",
            position: "absolute",
            top: "460px",
            left: "40px"
        });
        $("#dominio2").css({
            display: "block",
            position: "absolute",
            top: "460px",
            left: "885px"
        });
    } else {
        $("#dominio1").css({
            display: "none",
            position: "absolute"
        });
        $("#dominio2").css({
            display: "none",
            position: "absolute"
        });
    }

    $("#keyboard").empty();
    var posicion = $(e).position();
    var leftPos = 155;
    var topPos = 250;

    leftPos: posicion.left;
    topPos: posicion.top;

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
    var espacio = "";
    espacio = "<button class='btnEspaciadora' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\" \")'>Espacio</button>";
    var cadEnie = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"&Ntilde;\")'>&Ntilde;</button>";
    var coma = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\",\")'>,</button>";
    var punto = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\".\")'>.</button>";

    var borrarCaracter = "<button class='btnVirtualBorrar' onclick='fn_eliminarNumero(" + $(e).attr("id") + ")'>&larr;</button>";
    var borrarTodo = "<button class='btnVirtualBorrar' onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>&lArr;</button>";
//    var btnOk = "<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnCerrarTodo(" + $("#keyboard").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";
    var btnOk = "<button id='btn_ok_teclado' class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnOk_(" + $("#numPad").attr("id") + ", " + $(e).attr("id") + ", " + valid + ");'>OK</button>";
    var btnBorrarTodo = "<button class='btnVirtualBorrarTodo' id='btn_ok_borrar_todo'  onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>Borrar</button>";

    $("#keyboard").css({
        display: "block",
        position: "absolute",
        top: "460px",
        left: "170px"
    });
    $("#keyboard").append(num1 + num2 + num3 + num4 + num5 + num6 + num7 + num8 + num9 + num0 + borrarCaracter + "<br/>" + cadQ + cadW + cadE + cadR + cadT + cadY + cadU + cadI + cadO + cadP + borrarTodo + "<br/>" + arroba + cadA + cadS + cadD + cadF + cadG + cadH + cadJ + cadK + cadL + guion + "<br/>" + numeral + barraBaja + cadZ + cadX + cadC + cadV + cadB + cadN + cadM + cadEnie + punto + "<br/>" + btnBorrarTodo + espacio + btnOk);
}

function fn_alfaNumericoCorreoCliente(e, valid) {

    $("#numPad1").empty();
    $("#keyboard1").empty();
    $("#dominio5").empty();
    $("#dominio6").empty();    
    
    $("#numPad1").hide();
    $("#keyboard1").hide();
    $("#keyboard1").empty();
    $("#numPadCliente").hide();
    $("#pay_TeladocedulaCliente").hide();
    cargarTeclasCorreoCliente();

    if (!$(e.target).closest("#keyboard1").length) {}

    if (($(e).attr("id")) === 'pay_txtCorreo') {
        $("#dominio5").css({
            display: "block"
        });
        $("#dominio6").css({
            display: "block"
        });
    } else {
        $("#dominio5").css({
            display: "none",
            position: "absolute"
        });
        $("#dominio6").css({
            display: "none",
            position: "absolute"
        });
    }

    $("#keyboard1").empty();
    var posicion = $(e).position();
    var leftPos = 155;
    var topPos = 250;

    leftPos: posicion.left;
    topPos: posicion.top;

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
    var cadEnie = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"&Ntilde;\")'>&Ntilde;</button>";
    var coma = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\",\")'>,</button>";
    var punto = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\".\")'>.</button>";

    var borrarCaracter = "<button class='btnVirtualBorrar' onclick='fn_eliminarNumero(" + $(e).attr("id") + ")'>&larr;</button>";
    var borrarTodo = "<button class='btnVirtualBorrar' onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>&lArr;</button>";
    //var btnOk = "<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnCerrarTodo(" + $("#keyboard").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";
    var btnOk = "<button id='btn_ok_teclado' class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnOk_(" + $("#numPad").attr("id") + ", " + $(e).attr("id") + ", " + valid + ");'>OK</button>";
    var btnBorrarTodo = "<button class='btnVirtualBorrarTodo' id='btn_ok_borrar_todo'  onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>Borrar</button>";

    $("#keyboard1").css({display: "block"});
    $("#keyboard1").append(num1 + num2 + num3 + num4 + num5 + num6 + num7 + num8 + num9 + num0 + borrarCaracter + "<br/>" + cadQ + cadW + cadE + cadR + cadT + cadY + cadU + cadI + cadO + cadP + borrarTodo + "<br/>" + arroba + cadA + cadS + cadD + cadF + cadG + cadH + cadJ + cadK + cadL + guion + "<br/>" + numeral + barraBaja + cadZ + cadX + cadC + cadV + cadB + cadN + cadM + cadEnie + punto + "<br/>" + btnBorrarTodo + espacio + btnOk);
}


function fn_agregarCaracterCorreo(objeto, valor) {
    var lc_cantidad = $(objeto).val();

    if (isNaN(valor)) {
        var posicionArroba = lc_cantidad.indexOf('@');
        if (posicionArroba != -1) {
            var subtr = lc_cantidad.substring(0, posicionArroba);
            $(objeto).val(subtr + valor);
        } else {
            $(objeto).val(lc_cantidad + valor);
        }
    }
}


function cargarTeclasCorreo() {

    send = {"cargaTeclasEmail": 1};
    send.cdn_id = $("#hide_cdn_id").val();
    $.ajax({
        async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../seguridades/config_usuario.php", data: send,
        success: function (datos) {

            if (datos.str > 0) {

                $("#dominio3").empty();
                $("#dominio4").empty();
                for (i = 0; i < datos.str; i++) {
                    html = "<button class='btnVirtualDominio' style='font-size: 13px;' onclick='fn_agregarCaracterCorreo(pay_txtCorreo,\"" + datos[i]['descripcionEmail'] + "\")'>" + datos[i]['descripcionEmail'] + "</button><br/>";
                    if (i < 5) {
                        $("#dominio1").append(html);
                    }
                    if (i > 4) {
                        $("#dominio2").append(html);
                    }
                }
            }
        }
    });
}

function cargarTeclasCorreoCliente() {

    send = {"cargaTeclasEmail": 1};
    send.cdn_id = $("#hide_cdn_id").val();
    $.ajax({
        async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../seguridades/config_usuario.php", data: send,
        success: function (datos) {

            if (datos.str > 0) {

                $("#dominio5").empty();
                $("#dominio6").empty();
                for (i = 0; i < datos.str; i++) {
                    html = "<button class='btnVirtualDominio' style='font-size: 13px;' onclick='fn_agregarCaracterCorreo(pay_txtCorreo,\"" + datos[i]['descripcionEmail'] + "\")'>" + datos[i]['descripcionEmail'] + "</button><br/>";
                    if (i < 5) {
                        $("#dominio5").append(html);
                    }
                    if (i > 4) {
                        $("#dominio6").append(html);
                    }
                }
            }
        }
    });
}