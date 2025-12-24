/* global alertify */
var FidelizacionActiva = 0;
var codigoSeguridad = "";
var recarga = "";
var vitality = "";
var econtroDatos = -1;
var fb_document = -1;
var fb_name = -1;
var fb_status = -1;
var fb_points = -1;
var fb_email = -1;
var fb_phone = -1;
var fb_money = 0;
var DatosDevueltos = 0;
var existeEnMasterData = 0;
var existeClienteLocal = 0;

$(document).ready(function () {
    fn_cargando(0);
    alertify.set({
        labels: {
            ok: "SI", cancel: "NO"
        }
    });
    $("#rdn_pdd_brr_ccns").on("click", function () {
        $("#rdn_pdd_brr_ccns").hide();
    });

    //lECTURA VITALITY
    $("#txtCodigoVitality").on("change", function () {
        var codigoV = $("#txtCodigoVitality").val();
        $("#txtCodigoVitality").val(codigoV);
        cargarTokenSeguridadVitality(codigoV);
    });

    //Lector de barras
    $("#inLectorCodigos").on("change", function () {
        fn_cargando(1);
        var codigo = $("#inLectorCodigos").val();
        var cc = separarCedulaCodigo(codigo);
        if (cc) {
            console.log("Flujo JVZ");
            var cedula = cc[0];
            codigoSeguridad = cc[1];
            if (validarCodigoSeguridad(codigoSeguridad)) {
                $("#txtNumeroCedulaBusqueda").val(cedula);
                buscarExistente("txtNumeroCedulaBusqueda", "jvz");
            } else {
                fn_cargando(0);
                codigoSeguridad = "";
                $("#inLectorCodigos").val("");
            }
        } else {
            var payload = parseJwt(codigo);
            console.log("Flujo Appedir");
            console.log("Payload:", payload);
            if (payload.document && payload.app) {
                $("#txtNumeroCedulaBusqueda").val(payload.document);
                buscarExistente("txtNumeroCedulaBusqueda", payload.app);
            } else {
                fn_cargando(0);
                codigoSeguridad = "";
                $("#inLectorCodigos").val("");
            }
        }
    });
    $("#txtNumeroDocumento").focus(function () {
        vaciarCampos();
        deshabilitarCamposCliente(true);
    });
});

var darFocoEntradaQR = function () {
    $("#inLectorCodigos").focus();
};

function fn_cargando(std) {
    if (std > 0) {
        $("#mdl_rdn_pdd_crgnd").show();
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
    }
}

function fn_FidelizacionActiva() {
    var send = {"FidelizacionActiva": 1};
    send.est_ip = $("#hid_ip").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../seguridades/config_usuario.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#fidelizacionActiva").val((datos[0]["respuesta"]));
            } else {
                $("#fidelizacionActiva").val(0);
            }
        }
    });
}

function flujo_seguimiento(show, hide, limpiar) {
    if (recarga !== "") {
        $("#tqtIngresoCR").html('LEA CODIGO DE SEGURIDAD<p style="font-size: 0.6em;" class="mensajePequeno">Para acumular puntos es necesario lectura de código de seguridad.</p>');
    }
    if (limpiar) {
        $("#txtNumeroCedulaBusqueda").val("");
        $("#txtNumeroDocumento").val("");
        $("#txtNombresApellidos").val("");
        $("#txtTelefono").val("");
        $("#txtDireccion").val("");
        $("#txtCorreo").val("");
        $("#btnCRUD").val("Registrar");
    }
    if (show == "pre1") {
        recarga = "";
    } else if (show == "registroCliente") {
        deshabilitarCamposCliente(true);
    }
    $("#" + hide).hide(200);
    $("#" + show).show(200);
    ocultarTeclado();
    //Foco en Entrada de Codigo
    darFocoEntradaQR();
    $("#txtNumeroCedulaBusqueda").focus();
}


function ocultarTeclado() {
    fn_cerrarModalCuponesSistemaGerente();
    $("#keyboardCliente").hide();
    $("#keyboardCliente").empty();
    $("#dominio1").empty();
    $("#dominio2").empty();
    $("#numPadCliente").empty();
}

function cargarTeclasCorreo() {
    var send = {"cargaTeclasEmail": 1};
    send.cdn_id = $("#hide_cdn_id").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../seguridades/config_usuario.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#dominio1").empty();
                $("#dominio2").empty();
                for (i = 0; i < datos.str; i++) {
                    html = "<button class='btnVirtualDominio' style='font-size: 13px;' onclick='fn_agregarCaracterCorreo(txtCorreo,\"" + datos[i]["descripcionEmail"] + "\")'>" + datos[i]["descripcionEmail"] + "</button><br/>";
                    if (i < 5) {
                        $("#dominio1").append(html);
                    } else if (i > 4) {
                        $("#dominio2").append(html);
                    }
                }
            }
        }
    });
}

function fn_alfaNumerico_EscribirNombre(e) {
    ocultarTeclado();
    $("#dominio1").css({
        display: "none", position: "absolute"
    });
    $("#dominio2").css({
        display: "none", position: "absolute"
    });
    if (!$(e.target).closest("#keyboardCliente").length) {
    }
    $("#keyboardCliente").empty();
    var posicion = $(e).position();
    var leftPos = 155;
    var topPos = 250;
    leftPos: posicion.left;
    topPos: posicion.top;
    var num0 = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",0)'>0</button>";
    var num1 = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",1)'>1</button>";
    var num2 = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",2)'>2</button>";
    var num3 = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",3)'>3</button>";
    var num4 = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",4)'>4</button>";
    var num5 = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",5)'>5</button>";
    var num6 = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",6)'>6</button>";
    var num7 = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",7)'>7</button>";
    var num8 = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",8)'>8</button>";
    var num9 = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",9)'>9</button>";
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
    var arroba = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"@\")'>@</button>";
    var guion = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"-\")'>-</button>";
    var barraBaja = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"_\")'>_</button>";
    var numeral = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"#\")'>#</button>";
    var espacio = "";
    espacio = "<button class='btnEspaciadora' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\" \")'>Espacio</button>";
    var cadEnie = "<button class='btnVirtual' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"&Ntilde;\")'>&Ntilde;</button>";
    var coma = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\",\")'>,</button>";
    var punto = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\".\")'>.</button>";
    borrarCaracter = "<button class='btnVirtualBorrar' onclick='fn_eliminarNumero(" + $(e).attr("id") + ")'>&larr;</button>";
    borrarTodo = "<button class='btnVirtualBorrar' onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>&lArr;</button>";
    btnOk = "<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnOk1(" + $("#keyboardCliente").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";
    btnBorrarTodo = "<button class='btnVirtualBorrarTodo' id='btn_ok_borrar_todo'  onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>Borrar</button>";
    $("#keyboardCliente").css({
        display: "block", position: "absolute"
    });
    $("#keyboardCliente").append(num1 + num2 + num3 + num4 + num5 + num6 + num7 + num8 + num9 + num0 + borrarCaracter + "<br/>" + cadQ + cadW + cadE + cadR + cadT + cadY + cadU + cadI + cadO + cadP + borrarTodo + "<br/>" + arroba + cadA + cadS + cadD + cadF + cadG + cadH + cadJ + cadK + cadL + guion + "<br/>" + numeral + barraBaja + cadZ + cadX + cadC + cadV + cadB + cadN + cadM + cadEnie + punto + "<br/>" + btnBorrarTodo + espacio + btnOk);
}

function fn_btnOk1(e, p) {
    taxTeclado("#" + $(p).attr("id"));
    $(e).hide();
    $(e).empty();
    $(p).focus();
    $(p).trigger("change");
    $("#dominio1").hide();
    $("#dominio2").hide();
}

function fn_alfaNumericoCorreo(e) {
    $("#keyboard").hide();
    $("#keyboard").empty();
    $("#numPadCliente").hide();
    cargarTeclasCorreo();
    if (!$(e.target).closest("#keyboardCliente").length) {
    }
    if (($(e).attr("id")) == "txtCorreo") {
        $("#dominio1").css({
            display: "block", position: "absolute"
        });
        $("#dominio2").css({
            display: "block", position: "absolute"
        });
    } else {
        $("#dominio1").css({
            display: "none", position: "absolute"
        });
        $("#dominio2").css({
            display: "none", position: "absolute"
        });
    }
    $("#keyboardCliente").empty();
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
    var btnOk = "<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnOk1(" + $("#keyboardCliente").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";
    var btnBorrarTodo = "<button class='btnVirtualBorrarTodo' id='btn_ok_borrar_todo'  onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>Borrar</button>";
    $("#keyboardCliente").css({
        display: "block", position: "absolute"
    });
    $("#keyboardCliente").append(num1 + num2 + num3 + num4 + num5 + num6 + num7 + num8 + num9 + num0 + borrarCaracter + "<br/>" + cadQ + cadW + cadE + cadR + cadT + cadY + cadU + cadI + cadO + cadP + borrarTodo + "<br/>" + arroba + cadA + cadS + cadD + cadF + cadG + cadH + cadJ + cadK + cadL + guion + "<br/>" + numeral + barraBaja + cadZ + cadX + cadC + cadV + cadB + cadN + cadM + cadEnie + punto + "<br/>" + btnBorrarTodo + espacio + btnOk);
}

function fn_activarCasillaFDZN(id) {
    ocultarTeclado();
    $("#dominio1").css({
        display: "none", position: "absolute"
    });
    $("#dominio2").css({
        display: "none", position: "absolute"
    });
    $("#keyboardCliente").hide();
    $("#keyboardCliente").empty();
    $("#keyboard").hide();
    $("#keyboard").empty();
    fn_alfaNumerico(id);
    $("#btn_ok_teclado").attr("onclick", "fn_buscarCliente( '" + id + "')");
    $("#btn_cancelar_teclado").attr("onclick", "fn_cerrarModalCuponesSistemaGerente()");
}

function fn_numericoFDZN(e) {
    if (recarga == "Recargas" || e === "#txtNumeroDocumento") {
        ocultarTeclado();
        if (!$(e.target).closest("#numPadClienteAdmin").length) {
        }
        $("#numPadCliente").empty();
        var posicion = $(e).position();
        var leftPos = 910;
        var topPos = 450;
        leftPos: posicion.left;
        topPos: posicion.top;
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
        var borrarCaracter = "<button class='btnVirtualBorrar' onclick='fn_eliminarNumero(" + $(e).attr("id") + ");'>&larr;</button>";
        var borrarTodo = "<button class='btnVirtualOKpq' onclick='fn_eliminarTodo(" + $(e).attr("id") + ");'>&lArr;</button>";
        var btnOk = "<button class='btnVirtualOKpq' onclick='fn_buscarCliente(\"" + e + "\")'>OK</button>";
        $("#numPadCliente").css({
            display: "block", position: "absolute"
        });
        $("#numPadCliente").append(num7 + num8 + num9 + "<br/>" + num4 + num5 + num6 + "<br/>" + num1 + num2 + num3 + "<br/>" + num0 + btnOk + "<br/>" + borrarCaracter + borrarTodo);
        if (e === "#txtNumeroDocumento" || e === "#txtTelefono") {
            $("#numPadCliente").css("left", "80%");
            $("#numPadCliente").css("top", "20%");
        } else if (e === "#inValorRecarga") {
            $("#numPadCliente").css("left", "48%");
            $("#numPadCliente").css("top", "46%");
        } else if (e === "#txtNumeroCedulaBusqueda") {
            $("#numPadCliente").css("left", "42%");
            $("#numPadCliente").css("top", "44%");
        } else {
            $("#numPadCliente").css("left", "42%");
            $("#numPadCliente").css("top", "37%");
        }
    }
    darFocoEntradaQR();
}

function fn_tecladoNumTelefono(e) {
    ocultarTeclado();
    $("#dominio1").css({
        display: "none", position: "absolute"
    });
    $("#dominio2").css({
        display: "none", position: "absolute"
    });
    if (!$(e.target).closest("#keyboardCliente").length) {
    }
    $("#keyboardCliente").empty();
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
    var cadQ = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"Q\")'>Q</button>";
    var cadW = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"W\")'>W</button>";
    var cadE = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"E\")'>E</button>";
    var cadR = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"R\")'>R</button>";
    var cadT = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"T\")'>T</button>";
    var cadY = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"Y\")'>Y</button>";
    var cadU = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"U\")'>U</button>";
    var cadI = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"I\")'>I</button>";
    var cadO = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"O\")'>O</button>";
    var cadP = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"P\")'>P</button>";
    var cadA = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"A\")'>A</button>";
    var cadS = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"S\")'>S</button>";
    var cadD = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"D\")'>D</button>";
    var cadF = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"F\")'>F</button>";
    var cadG = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"G\")'>G</button>";
    var cadH = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"H\")'>H</button>";
    var cadJ = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled'onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"J\")'>J</button>";
    var cadK = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"K\")'>K</button>";
    var cadL = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"L\")'>L</button>";
    var cadZ = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"Z\")'>Z</button>";
    var cadX = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"X\")'>X</button>";
    var cadC = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"C\")'>C</button>";
    var cadV = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"V\")'>V</button>";
    var cadB = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"B\")'>B</button>";
    var cadN = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"N\")'>N</button>";
    var cadM = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"M\")'>M</button>";
    var arroba = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"@\")'>@</button>";
    var guion = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"-\")'>-</button>";
    var barraBaja = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"_\")'>_</button>";
    var numeral = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"#\")'>#</button>";
    var espacio = "";
    espacio = "<button disabled='disabled' class='btnEspaciadora, btnEspaciadora_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\" \")'>Espacio</button>";
    var cadEnie = "<button  disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\"&Ntilde;\")'>&Ntilde;</button>";
    var coma = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\",\")'>,</button>";
    var punto = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracter(" + $(e).attr("id") + ",\".\")'>.</button>";
    var borrarCaracter = "<button class='btnVirtualBorrar' onclick='fn_eliminarNumero(" + $(e).attr("id") + ")'>&larr;</button>";
    var borrarTodo = "<button class='btnVirtualBorrar' onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>&lArr;</button>";
    var btnOk = "<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnOk1(" + $("#keyboardCliente").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";
    var btnBorrarTodo = "<button class='btnVirtualBorrarTodo' id='btn_ok_borrar_todo'  onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>Borrar</button>";
    $("#keyboardCliente").css({
        display: "block", position: "absolute"
    });
    $("#keyboardCliente").append(num1 + num2 + num3 + num4 + num5 + num6 + num7 + num8 + num9 + num0 + borrarCaracter + "<br/>" + cadQ + cadW + cadE + cadR + cadT + cadY + cadU + cadI + cadO + cadP + borrarTodo + "<br/>" + arroba + cadA + cadS + cadD + cadF + cadG + cadH + cadJ + cadK + cadL + guion + "<br/>" + numeral + barraBaja + cadZ + cadX + cadC + cadV + cadB + cadN + cadM + cadEnie + punto + "<br/>" + btnBorrarTodo + espacio + btnOk);
}

function fn_cerrarModalCuponesSistemaGerente() {
    $("#keyboard").hide();
    $("#keyboard").empty();
}

function taxTeclado(id) {
    $(this).trigger("blur");
    var txt;
    if (id === "#txtNumeroDocumento") {
        $("#txtNombresApellidos").focus();
        $("#txtNombresApellidos").click();
    } else if (id === "#txtNombresApellidos") {
        $("#txtTelefono").focus();
        $("#txtTelefono").click();
        txt.click();
    } else if (id === "#txtTelefono") {
        $("#txtDireccion").focus();
        $("#txtDireccion").click();
    } else if (id === "#txtDireccion") {
        $("#txtCorreo").focus();
        $("#txtCorreo").click();
    }
}

function fn_buscarCliente(id) {
    if (id === "#txtNumeroDocumento" /*|| id === "#txtNumeroCedulaBusqueda"*/) {
        var ci = $(id).val();
        if (!validarDocumentoCedulaRUC(ci)) {
            deshabilitarCamposCliente(true);
            return;
        }
    }
    deshabilitarCamposCliente(false);
    fn_cargando(1);
    $("#numPadCliente").hide();
    // Verificar si el boton OK del teclado esta dentro del txtDcouemento para consultar si existe en la tienda.
    if (id === "#txtNumeroDocumento") {
        // busco en la base local.
        ConusltarExistente("txtNumeroDocumento");
        // Si no hay conexion a la red no permitir que continue.
        if (existeClienteLocal === 404) {
            alertify.error("No hay conexión a la red.");
            fn_cargando(0);
            return;
        }
        // si no existe buscar en MD
        if (existeClienteLocal === 0) {
            fn_buscarInsertarClienteMasterData("txtNumeroDocumento");
            if (existeEnMasterData === 1) {
                // Obtengo la autorizacion para poder actualizarlo en MD
                fn_buscarCLienteAutorizacionMasterData("txtNumeroDocumento");
                ConusltarExistente("txtNumeroDocumento");
            }
        } else {
            //  ConusltarExistente("txtNumeroDocumento");
            // Obtengo la autorizacion para poder actualizarlo en MD
            fn_buscarCLienteAutorizacionMasterData("txtNumeroDocumento");
        }
    }
    $("#keyboard").empty();
    fn_cargando(0);
    if (existeClienteLocal === 1 || existeEnMasterData === 1 || existeEnMasterData === 2) {
        taxTeclado(id);
    }
}

function mostrarPanel() {
    $("#rdn_pdd_brr_ccns").show();
}

function fn_salirSistema() {
    window.location.replace("../index.php");
}

// Verifica el estado del registro del cliente (PREREGISTRADO, REGISTRADO)
function VerificarEstadoPR_or_R(id) {
    if (fb_status === "PREREGISTERED") {
        $("#inLectorCodigos").val("");
        alertify.confirm("El cliente se encuentra PREREGISTRADO, por favor termine su proceso de inscripción en la web o app para disfrutar de sus beneficios.");
        $("#alertify-cancel").hide();
        $("#alertify-ok").click(function () {
            ir_tomaPedido();
        });
    } else {
        if (econtroDatos === 1) {
            ir_tomaPedido();
        } else {
            if (econtroDatos === 404) {
                $("#inLectorCodigos").val("");
                alertify.error("El cliente no consta en el plan amigos.");
                vaciarCampos();
                // flujo_seguimiento("registroCliente", "ingresoCedula");
                return;
            } else if (econtroDatos === 405) {
                $("#inLectorCodigos").val("");
                alertify.error("Ocurre un problema con el servicio web, intente nuevamente en un momento.");
                vaciarCampos();
                // flujo_seguimiento("registroCliente", "ingresoCedula");
                return;
            }
            $("#inLectorCodigos").val("");
            alertify.confirm("No hay comunicación con el servicio web de puntos. ¿Desea realizar la toma del pedido de la forma normal?");
            $("#alertify-cancel").html("No");
            $("#alertify-ok").html("Si");
            $("#alertify-ok").click(function () {
                ir_tomaPedido();
            });
        }
    }
}

function validarDocumentoCedulaRUC(documento) {
    var estado = false;
    var longitud = documento.length;
    if (longitud === 13) {
        if (validarRUC(documento)) {
            estado = true;
        } else {
            alertify.error("El RUC no es válido o no es persona natural");
        }
    } else if (validarCedula(documento)) {
        estado = true;
    } else {
        alertify.error("Número de documento no válido");
    }
    return estado;
}

function validarCedula(documento) {
    var estado = false;
    var send = {"validarCedula": 1};
    send.documento = documento;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../seguridades/config_usuario.php",
        data: send,
        success: function (datos) {
            if (datos["estado"] === "1") {
                estado = true;
            }
        }
    });
    return estado;
}

function validarRUC(documento) {
    var estado = false;
    var send = {"validarRUC": 1};
    send.documento = documento;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../seguridades/config_usuario.php",
        data: send,
        success: function (datos) {
            if (datos["estado"] === "1") {
                estado = true;
            }
        }
    });
    return estado;
}

function buscarExistente(IDDocumento,app) {
    let ci = $("#" + IDDocumento).val();
    const words = ci.split('-');
    let qr=ci;
    ci=words[0];
    if (ci.length === 10 || ci.length === 13) {
        fn_cargando(1);
        //1. Consultar en Firebase
        fn_consultaFB(ci, "",qr);
        fn_cargando(0);
    } else {
        alertify.error("Ingrese una cédula válida");
        $("#inLectorCodigos").val("");
        fn_cargando(0);
    }
}

function vaciarCampos() {
    $("#hid_IDCliente").val("");
    $("#txtNombresApellidos").val("");
    $("#txtTelefono").val("");
    $("#txtDireccion").val("");
    $("#txtCorreo").val("");
}

function ConusltarExistente(IDDocumento) {
    existeClienteLocal = 0;
    var ci = $("#" + IDDocumento).val();
    var send = {"ConusltarExistente": 1};
    send.accion = "B";
    send.numeroDocumento = ci;
    send.cdn_id = $("#hide_cdn_id").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../seguridades/config_usuario.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                $("#inLectorCodigos").val("");
                existeClienteLocal = 1;
                $("#hid_IDCliente").val(datos[0]["IDCliente"]);
                $("#txtNombresApellidos").val(datos[0]["descripcion"]);
                $("#txtTelefono").val(datos[0]["telefono"]);
                $("#txtDireccion").val(datos[0]["direccion"]);
                $("#txtCorreo").val(datos[0]["email"]);
                // document.getElementById("btnCRUD").value = "Actualizar";
            }
        },
        error: function (e) {
            existeClienteLocal = 404; // Error de conexion a internet.
        }
    });
}

function fn_CRUD_Cliente() {
    var docCliente = $("#txtNumeroDocumento").val();
    var descripcionCliente = $("#txtNombresApellidos").val().trim();
    var telf = $("#txtTelefono").val();
    if (validarDocumentoCedulaRUC(docCliente)) {
        if (descripcionCliente.length > 0) {
            if (telf.length >= 7 && telf.length <= 10) {
                if (docCliente.length === 10) {
                    $("#txtTipoDocumento").val("CEDULA");
                } else {
                    $("#txtTipoDocumento").val("RUC");
                }
                fn_cargando(1);
                var send = {"CRUD_Cliente": 1};
                send.accion = 1; //Actualizar Cliente
                send.nombresApellidos = descripcionCliente;
                send.numeroDocumento = docCliente;
                send.telefono = telf;
                send.tipoDocumento = $("#txtTipoDocumento").val();
                send.direccion = $("#txtDireccion").val();
                send.mail = $("#txtCorreo").val();
                send.usuario = $("#hide_usr_id").val();
                // Este WS funciona igual tanto para el insert  y el update.
                // fn_envioDatosClienteWS(send.tipoDocumento, send.numeroDocumento, send.nombresApellidos, send.direccion, send.telefono, send.mail);
                // diferente Insert y update.
                //Registro Plan Amigos
                $.ajax({
                    async: false,
                    type: "POST",
                    dataType: "json",
                    contentType: "application/x-www-form-urlencoded",
                    url: "../seguridades/config_usuario.php",
                    data: send,
                    success: function (datos) {
                        if (datos.str > 0) {
                            $("#hid_IDCliente").val(datos[0]["IDCliente"]);
                            fn_preRegistroFB(send.tipoDocumento, send.numeroDocumento, send.nombresApellidos, send.direccion, send.telefono, send.mail, send.accion);
                            // VerificarEstadoPR_or_R(id);
                            //  alertify.success((send.accion == 1) ? "Cliente Registrado" : "Cliente Actualizado");
                            // ir_tomaPedido();
                            // VerificarEstadoPR_or_R("#txtNumeroDocumento");
                        }
                    }
                });
            } else {
                alertify.error("Número de teléfono no válido, debe tener entre 8 y 10 dígitos");
            }
        } else {
            alertify.error("Nombre y apellido son obligatorios");
        }
    }
    fn_cargando(0);
}

var updateDatos = function () {
    if (!datosClienteModificados) {
        datosClienteModificados = !datosClienteModificados;
    }
};

function fn_preRegistroFB(tipoDocumento, documento, descripcion, direccion, telefono, correo, accion) {
    var send = {};
    var autorizacion = $("#clienteAutorizacion").val();
    send.metodo = "preRegistroFireBase";
    send.autorizacion = autorizacion;
    send.tipoDocumento = tipoDocumento;
    send.documento = documento;
    send.descripcion = descripcion;
    send.direccion = direccion;
    send.telefono = telefono;
    send.correo = correo;
    send.accion = accion;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/clienteWSClientes.php",
        data: send,
        success: function (datos) {
            fn_cargando(0);
            var mensaje = "";
            if (datos.hasOwnProperty("errors")) {
                if (datos.errors.document == "Campo Documento ya está en uso") {
                    mensaje = "El cliente ya está PREREGISTRADO, desea tomar pedido?";
                } else if (datos.errors.document == "Registrado") {
                    mensaje = "El cliente ya está REGISTRADO, desea tomar pedido?";
                }
            } else {
                mensaje = "Cliente PREREGISTRADO correctamente, desea tomar pedido?";
            }
            alertify.confirm(mensaje);
            $("#alertify-ok").click(function () {
                fn_cargando(1);
                fn_consultaFB(documento, "jvz");
            });
            $("#alertify-cancel").click(function () {
                //Ir al inicio
                location.reload();
            });
        },
        error: function (e) {
            fn_cargando(0);
            alertify.error("Ocurrió un problema, intente luego nuevamente.");
            irOrdenPedido();
        }
    });
}

function fn_consultaFB(documento, app='',qr='') {
    econtroDatos = -1;
    fb_document = -1;
    fb_name = -1;
    fb_status = -1;
    fb_points = -1;
    fb_email = -1;
    fb_phone = -1;
    let ci = documento;
    let send = {};
    send.metodo = "consultaEstadoFireBase";
    send.documento = ci;
    send.app = app;
    send.tipoDocumento= (ci.length > 10) ? "RUC" : "CI";
    send.codigoSeguridad = (qr.indexOf("-") > 0) ? qr.substring(qr.indexOf("-") + 1,qr.length) : '' ;
    send.app = (app=='')? 'jvz': app;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/clienteWSClientes.php",
        data: send,
        success: function (datos) {
            console.info(datos);
            if (datos["mensaje"] === undefined || datos["mensaje"] === '') { // el mensaje solo se obtiene cuando ocurre un error.
                econtroDatos = 1;
                fb_document = datos["document"];
                fb_name = datos["name"];
                //fb_status = datos["reasonDisabled"]; // ******* V2 FIDELIZACION *******
                fb_status = datos["status"];
                fb_points = datos["points"]; // ******* V1 FIDELIZACION *******
                //fb_points = datos["balance"]["points"]; // ******* V2 FIDELIZACION *******
                fb_email = datos["email"];
                fb_phone = datos["phone"];
                fb_money = datos["balance"]; // ******* V1 FIDELIZACION *******
                //fb_money = datos["balance"]["balance"]; // ******* V2 FIDELIZACION *******
                if (recarga == "Recargas") {
                    if (obtenerLimitesRecarga()) {
                        $("#cntRecargas").show();
                        $("#ingresoCedula").hide();
                        $("#outCliente").html("Cliente: " + fb_name);
                        $("#outClientePuntos").html("Puntos: " + parseFloat(fb_points).toFixed(2));  //parseFloat(fb_points).toFixed(2)
                        $("#outClienteSaldo").html("Recargas: $" + parseFloat(fb_money).toFixed(2));
                        ocultarTeclado();
                    } else {
                        alertify.error("Error al obtener parámetros de recargas.");
                    }
                } else {
                    irOrdenPedido();
                }
            } else {
                if(datos["error"]){
                    // Se encontro un error
                    alertify.alert(datos["mensaje"]);
                }
                if (datos["estadoPeticion"] == 404) {
                    //TODO: caso de cédula no válida
                    if (datos["mensaje"] == "Usuario no encontrado") {
                        //Registrar usuario
                        alertify.confirm("El cliente no se encuentra registrado. ¿Desea registrarlo?");
                        $("#alertify-cancel").click(function () {
                            //Ir a la orden de pedido
                            irOrdenPedido();
                        });
                        $("#alertify-ok").click(function () {
                            // flujo_seguimiento("registroCliente", "ingresoCedula", true);
                            deshabilitarCamposCliente(true);
                            $("#txtNumeroDocumento").val(documento);
                            $("#txtNombresApellidos").val(datos.cliente.descripcion);
                            $("#txtTelefono").val(datos.cliente.telefonoDomiclio);
                            $("#txtDireccion").val(datos.cliente.direccionDomicilio);
                            $("#txtCorreo").val(datos.cliente.correo);
                            $("#btnCRUD").val("Registrar");
                            //Ocultar formulario anterior
                            $("#ingresoCedula").hide(200);
                            // Formulario de Registro
                            $("#registroCliente").show(200);
                            fn_numericoFDZN('#txtNumeroDocumento');
                        });
                    }
                }
                // econtroDatos = 404;
            }
        },
        error: function (xhr, textStatus, error) {
            console.log(xhr.responseText);
            if (textStatus === "parsererror") {
                econtroDatos = 405;
            } else {
                econtroDatos = 404;
            }
        }
    });
}

function fn_buscarInsertarClienteMasterData(documento) {
    existeEnMasterData = 0;
    var send = {};
    var tipoDocumento = "CEDULA";
    send.metodo = "buscarCliente";
    send.documento = $("#" + documento).val();
    send.tipoDocumento = tipoDocumento;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../facturacion/clienteWSClientes.php",
        data: send,
        success: function (datos) {
            existeEnMasterData = datos["estado"];
            $("#clienteAutorizacion").val(datos["cliente"]["autorizacion"]);
        },
        error: function () { //  para cuando no se pueda autorizar.
            existeEnMasterData = 0;
            fn_cargando(0);
            //  fn_nuevoCliente(documento);
            $("#estadoWS").val(0);
        }
    });
}

function fn_buscarCLienteAutorizacionMasterData(documento) {
    var ci = $("#" + documento).val();
    var send = {};
    var tipoDocumento = "CEDULA";
    send.metodo = "obtenerDatosCliente";
    send.documento = ci;
    send.tipoDocumento = tipoDocumento;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../facturacion/clienteWSClientes.php",
        data: send,
        success: function (datos) {
            existeEnMasterData = datos["estado"];
            $("#clienteAutorizacion").val(datos["cliente"]["autorizacion"]);
        },
        error: function () { // Cuando el WS no esta en linea
        }
    });
}

function fn_envioDatosClienteWS(tipoDocumento, documento, descripcion, direccion, telefono, correo) {
    var send = {};
    var autorizacion = $("#clienteAutorizacion").val();
    send.metodo = "enviarCliente";
    send.autorizacion = autorizacion;
    send.tipoDocumento = tipoDocumento;
    send.documento = documento;
    send.descripcion = descripcion;
    send.direccion = direccion;
    send.telefono = telefono;
    send.correo = correo;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/clienteWSClientes.php",
        data: send,
        success: function (datos) {
        }
    });
}

function ir_tomaPedido() {
    var IDMesa = $("#hide_mesa_id").val();
    if (recarga == "Recargas") {
        if (obtenerLimitesRecarga()) {
            $("#cntRecargas").show();
            $("#ingresoCedula").hide();
            fn_numericoFDZN("#inValorRecarga");
        } else {
            alertify.error("Error al obtener parámetros de recargas.");
        }
    } else {
        window.location.replace("tomaPedido.php?numMesa=" + IDMesa);
    }
}

function ir_noEnrrolar() {
    var send = {"noEnrrolar": 1};
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../seguridades/config_usuario.php",
        data: send,
        success: function (datos) {
            ir_tomaPedido();
        }
    });
}

function redireccionar(mesa) {
    window.location.replace("../ordenpedido/tomaPedido.php?numMesa=" + mesa);
}


function ejecutarFuncion(event) {
    if (event.keyCode === 13) {
        buscarExistente('txtNumeroCedulaBusqueda');
    }
}

var opcionSetRecarga = function (r) {
    recarga = r;
    vitality = "";
    $("#registroCliente").hide();
    $("#pre2").hide();
    $("#ingresoVitality").hide();
    flujo_seguimiento("ingresoCedula", "pre1", true);
    $("#tqtIngresoCR").html(r + '<p style="font-size: 0.6em;" class="mensajePequeno">Código de seguridad.</p>');
    darFocoEntradaQR();
    $("#txtNumeroCedulaBusqueda").focus();
};

var opcionSetVitality = function (opcion) {
    vitality = opcion;
    recarga = "";
    $("#cntRecargas").hide();
    $("#registroCliente").hide();
    $("#pre2").hide();
    $("#ingresoCedula").hide();
    flujo_seguimiento("ingresoVitality", "pre1", true);
    $("#tqtIngresoVT").html('Vitality<p style="font-size: 0.6em;" class="mensajePequeno">Lea código de seguridad.</p>');
    $("#txtCodigoVitality").focus();
};


var cancelarRecarga = function () {
    $("#tqtIngresoCR").html('LEA CODIGO DE SEGURIDAD<p style="font-size: 0.6em;" class="mensajePequeno">Para acumular puntos es necesario lectura de código de seguridad.</p>');
    recarga = "";
    $("#inValorRecarga").val("");
    $("#numPadCliente").hide();
    $("#numPadCliente").empty();
    $("#pre1").show();
    $("#cntRecargas").hide();
};

var recargarEfectivoCliente = function () {
    //Validaciones
    //outCliente
    //outClientePuntos
    //outClienteSaldo
    var valor = $("#inValorRecarga").val();
    var min = Number($("#spnMinimo").html());
    var max = Number($("#spnMaximo").html());
    if (valor !== "") {
        valor = Number(valor);
        if (valor >= min) {
            if (valor <= max) {
                alertify.confirm("Se acreditará el valor de $" + valor + " a " + fb_name + ". ¿Está seguro?");
                $("#alertify-ok").click(function () {
                    fn_cargando(1);
                    var send = {};
                    send.metodo = "recargarEfectivoCliente";
                    send.valor = valor;
                    // send.invoiceCode = $("#txtNumFactura").val(), //probar
                    $.ajax({
                        async: false,
                        type: "POST",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "../recargas/consultasRecargas.php",
                        data: send,
                        success: function (datos) {

                            if (datos.estado == 1) {

                                var apiImpresion = getConfiguracionesApiImpresion();
                                if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                            
                                    var result = new apiServicioImpresion('impresionFidelizacionRecarga', null, null, datos.idTransaccion);
                                    var imprime = result["imprime"];
                                    var mensaje = result["mensaje"];
                            
                                    console.log('imprime: ', imprime);
                            
                                    if (!imprime) {
                                        alertify.success('Imprimiendo recarga de fidelizacion...');
                                    } else {
                                        alertify.success('Error al imprimir...');
                                    }
                            
                                    }


                                fn_cargando(0);
                                $("#inValorRecarga").val("");
                                alertify.confirm("Recarga exitosa. ¿Tomar el pedido al cliente?");
                                $("#alertify-cancel").click(function () {
                                    $("#numPadCliente").hide();
                                    $("#numPadCliente").empty();
                                    recarga = "";
                                    //IMPORTANTE! Borrar variables sesion del cliente
                                    location.reload();
                                });
                                $("#alertify-ok").click(function () {
                                    $("#numPadCliente").hide();
                                    $("#numPadCliente").empty();
                                    recarga = "";
                                    ir_tomaPedido();
                                });
                            } else if (datos.estado == 2 || datos.estado == 3) {
                                fn_cargando(0);
                                $("#inValorRecarga").val("");
                                alertify.error(datos.mensaje);
                            } else {
                                $("#numPadCliente").hide();
                                $("#numPadCliente").empty();
                                fn_cargando(0);
                                alertify.error(datos.mensaje);
                            }
                        },
                        error: function () {
                            alertify.error("Error, por favor comuníquese con sistemas.");
                            fn_cargando(0);
                        }
                    });
                });
            } else {
                alertify.error("El valor a recargar no puede ser mayor a $" + max + ".");
            }
        } else {
            alertify.error("El valor a recargar no puede ser menor a $" + min + ".");
        }
    } else {
        alertify.error("Ingresar el valor a recargar.");
    }
};

function irOrdenPedido() {
    recarga = "";
    ir_tomaPedido();
}

function obtenerLimitesRecarga() {
    var valido = false;
    var send = {"obtenerLimitesRecarga": 1};
    send.idCadena = $("#hide_cdn_id").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../seguridades/config_usuario.php",
        data: send,
        success: function (datos) {
            if (datos !== null && datos.str > 0) {
                $("#spnMinimo").html(datos.min);
                $("#spnMaximo").html(datos.max);
                valido = true;
            }
        }
    });
    return valido;
}

function deshabilitarCamposCliente(deshabilitarCampos) {
    $("#txtNombresApellidos").prop("disabled", deshabilitarCampos);
    $("#txtTelefono").prop("disabled", deshabilitarCampos);
    $("#txtDireccion").prop("disabled", deshabilitarCampos);
    $("#txtCorreo").prop("disabled", deshabilitarCampos);
}

function cargarTokenSeguridadVitality(codigoQRVitality) {
    fn_cargando(1);
    var send = {};
    send.metodo = "cargarTokenSeguridadVitality";
    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "config_VitalityWS.php",
        data: send,
        success: function (datos) {
            if (datos.access_token != '') {
                var TokenSeguridadVitality = datos.token_type + ' ' + datos.access_token;
                validarCodigoQRVitality(codigoQRVitality, TokenSeguridadVitality);
                //ventaVitality(codigoQRVitality, TokenSeguridadVitality);

            } else {
                fn_cargando(0);
                $("#txtCodigoVitality").focus();
                alertify.error('Error:' + datos.mensajes).delay(10);
                $("#txtCodigoVitality").val('');

            }
        }
    });
};

function validarCodigoQRVitality(codigoQRVitality, TokenSeguridadVitality) {
    var send = {};
    send.metodo = "ValidarVoucherVitality";
    send.codigoQRVitality = codigoQRVitality;
    send.TokenSeguridadVitality = TokenSeguridadVitality;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "config_VitalityWS.php",
        data: send,
        success: function (datos) {
            var info = datos.data;
            if (datos.errores == "") {
                var telf = filtrarNumeros(info.phoneNumber);
                if (parseFloat(info.currentBalance) > 0.00) {
                    var cliente = obtenerIdClienteExterno(0, info.legalName, "", "RUC", info.documentNumber, telf, info.address, "", "");
                    if (cliente != null) {
                        $("#txtCodigoVitality").val("");
                        $("#txtCodigoVitality").focus();
                        fn_cargando(0);
                        ir_tomaPedido();
                    } else {
                        fn_cargando(0);
                        $("#txtCodigoVitality").val("");
                        alertify.error("Error con Datos de Cupón").delay(10);
                        $("#txtCodigoVitality").focus();
                    }
                } else {
                    fn_cargando(0);
                    $("#txtCodigoVitality").val('');
                    alertify.error('Error: Cupón Canjeado o Expirado').delay(10);
                    $("#txtCodigoVitality").focus();
                }
            } else {
                fn_cargando(0);
                $("#txtCodigoVitality").val("");
                alertify.error("Existe problemas con el Cupón. Por favor intente más tarde.").delay(10);
                $("#txtCodigoVitality").focus();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR, textStatus, errorThrown);
            fn_cargando(0);
            $("#txtCodigoVitality").val("");
            alertify.error("Servicio no disponible, por favor intentalo más tarde.").delay(10);
            $("#txtCodigoVitality").focus();
        }
    });
}

function obtenerIdClienteExterno(idCiudad, nombre, apellido, tipoDocumento, documento, telefono, direccion, email, tipoCliente) {
    var idCliente = null;
    var send = {"metodo": "obtenerIdClienteExterno"};
    send.idCiudad = idCiudad;
    send.nombre = nombre;
    send.apellido = apellido;
    send.tipoDocumento = tipoDocumento;
    send.documento = documento;
    send.telefono = telefono;
    send.direccion = direccion;
    send.email = email;
    send.tipoCliente = tipoCliente;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_VitalityWS.php",
        data: send,
        success: function (datos) {
            if (datos !== null) {
                idCliente = datos.idCliente;
            }
        }
    });

    return idCliente;
}

//Devolver solo números
function filtrarNumeros(numero) {
    return numero.replace(/\D/g, "");
}