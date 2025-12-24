
$(document).ready(function () {
    bloquearCtrlC_CtrlV('cardNumber');
});



function bloquearCtrlC_CtrlV(id) {

    var myInput = document.getElementById(id);
    myInput.onpaste = function (e) {
        e.preventDefault();
        alertify.error("esta acción está prohibida");
    }

    myInput.oncopy = function (e) {
        e.preventDefault();
        alertify.error("esta acción está prohibida");
    }
}
function ocultarTeclado() {
    fn_cerrarModalCuponesSistemaGerente();
    $("#pay_TeladoNombres").hide();
    $("#pay_TeladoNombres").empty();
    $("#dominio3").empty();
    $("#dominio4").empty();
    $("#pay_TeladocedulaCliente").empty();
}


function fn_cerrarModalCuponesSistemaGerente() {

    $("#keyboard").hide();
    $("#keyboard").empty();
    $("#modalCuponSistemaGerente").hide();
    $("#modalCuponSistemaGerente").dialog("close");


}

function fn_agregarCaracterNum(obj, valor) {

    var lc_cantidad = $(obj).val();
    lc_cantidad += valor;
    $(obj).val(lc_cantidad);
}




function llenarCamposCliente(ClienteJSON) {

    $("#txtDocumentoClientePaypone").val(ClienteJSON.documento);

    $("#pay_txtCedulaCliente").val(ClienteJSON.documento);
    $("#pay_txtNombres").val(ClienteJSON.descripcion);
    $("#pay_txtDireccion").val(ClienteJSON.direccion);
    $("#pay_txtTelefono").val(ClienteJSON.telefono);
    $("#pay_txtCorreo").val(ClienteJSON.email);
}




function habilitarBotones() {
    $("#paybtn1").show();
    $("#pay_datosCliente").show(200);
    $("#pay_TeladocedulaCliente").hide();
    $("#paybtn1").text('Siguiente');
}

function fn_cargando(std) {
    if (std > 0) {
        $("#mdl_rdn_pdd_crgnd1").show();
    } else {
        $("#mdl_rdn_pdd_crgnd1").hide();
    }
}


function validarRUC(documento) {
    var estado = false;
    var send = {"validarRUC": 1};
    send.documento = documento;
    $.ajax({
        async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../seguridades/config_usuario.php", data: send,
        success: function (datos) {
            if (datos["estado"] === "1") {
                estado = true;
            } else {
                estado = false;
            }
        },
        error: function (e) {
            estado = false;
        }
    });
    return estado;
}




function BuscarClienteEnLocal(IDDocumento) {
    fn_cargando(1);
    return new Promise(function (resolve, reject) {
        existeClienteLocal = 0;
        var send = {"ConusltarExistente": 1};
        send.accion = 'B';
        send.numeroDocumento = IDDocumento;
        send.cdn_id = $('#txtCadenaId').val();

        $.ajax({
            async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../seguridades/config_usuario.php", data: send,
            success: function (datos) {

                if (datos.str > 0) {
                    datos[0]["estado"] = 200;
                    resolve(datos[0]);
                } else {
                    resolve({
                        "estado": 204,
                        "mensaje": "Cliente no encontrado en el local"
                    });
                }
                fn_cargando(0);
                resolve(datos);
            },
            error: function (e) {
                fn_cargando(0);
                resolve({
                    "estado": 500,
                    "mensaje": "Error de conexion."
                });
            }
        });
    })
}


function fn_buscarEInsertarClienteMasterData(documento) {
    return new Promise(function (resolve, reject) {
        fn_cargando(1);
        existeEnMasterData = 0;
        var send;
        var tipoDocumento = "CEDULA";
        send = {};
        send.metodo = "buscarCliente";
        send.documento = documento;
        send.tipoDocumento = tipoDocumento;
        send.payphone =1;

        $.ajax({
            async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../facturacion/clienteWSClientes.php", data: send, success: function (datos) {
                var cliente;
                if (datos["cliente"].IDCliente !== undefined  && datos["cliente"].IDCliente !== "") {
                    cliente = {
                        autorizacion: datos["cliente"].autorizacion,
                        documento: datos["cliente"].identificacion,
                        descripcion: datos["cliente"].descripcion,
                        direccion: datos["cliente"].direccionDomicilio,
                        telefono: datos["cliente"].telefonoDomiclio,
                        email: datos["cliente"].correo
                    }
                    cliente["estado"] = 200;

                } else {
                    resolve({
                        "estado": 204,
                        "mensaje": "Cliente no encontrado."
                    });
                }


                fn_cargando(0);
                resolve(cliente);
                // existeEnMasterData = datos["estado"];
                // $("#clienteAutorizacion").val(datos["cliente"]["autorizacion"]);

            }, error: function () { //  para cuando no se pueda autorizar.
                fn_cargando(0);
                resolve({
                    "estado": 500,
                    "mensaje": "Error de conexion."
                });
                // existeEnMasterData = 0;
                // fn_cargando(0);
                // //  fn_nuevoCliente(documento);
                // $("#estadoWS").val(0);
            }
        });


    })
}


var OpcionCLiente = "I"
function guardarCliente() {


    if ($("#paybtn1").text() === "Siguiente")
        return;

    if (!validarCampos('pay_txtCedulaCliente'))
        return;
    if (!validarCampos('pay_txtNombres'))
        return;
    if (!validarCampos('pay_txtDireccion'))
        return;
    if (!validarCampos('pay_txtTelefono'))
        return;
    if (!validarCampos('pay_txtCorreo'))
        return;



    var ci = $("#pay_txtCedulaCliente").val();

    fn_cargando(1);

    var send = {"guardarCliente": 1};
    send.accion = OpcionCLiente; // I = insetar o U =Actualizar
    send.tipoConsulta = "W";
    send.clienteTipoDoc = (ci.length === 10) ? "CEDULA" : "RUC";
    send.clienteDocumento = ci;
    send.clienteDescripcion = $("#pay_txtNombres").val();
    send.clienteDireccion = $("#pay_txtDireccion").val();
    send.clienteFono = $("#pay_txtTelefono").val();
    send.clienteCorreo = $("#pay_txtCorreo").val();
    send.usuario = $("#txtUserId").val();
    send.estadoWS = 0;
    send.tipoCliente = "NORMA";

    var cadenaJSON = {
        "TiposInmuebles": "",
        "numeroCallePrincipal": "",
        "calleSecundaria": "",
        "referenciaTipoInmueble": "",
        "referencia": ""

    };
    send.jsonDatosAdicionales = (cadenaJSON);
    send.send = {"guardarCliente": 1};


    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/config_clientes.php",
        data: send,
        success: function (datos) {
            fn_cargando(0);

            if (datos === 1) {

                Swal.fire({
                    title: 'Cliente registrado',
                    text: "",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.value) {
                        $("#txtDocumentoClientePaypone").val(ci);
                        $("#modalRegistroCliente").hide();
                        $("#modalPay").show(200);
                    }
                })

            } else {

                Swal.fire({
                    title: 'Ocurrio un Error.',
                    text: "",
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.value) {

                    }
                })

            }

        }
        , error: function (e) {

            localStorage.setItem("ls_documento", $("#pay_txtCedulaCliente").val());
            localStorage.setItem("ls_nombres", $("#pay_txtNombres").val());
            localStorage.setItem("ls_direccion", $("#pay_txtDireccion").val());
            localStorage.setItem("ls_telefono", $("#pay_txtTelefono").val());
            localStorage.setItem("ls_correo", $("#pay_txtCorreo").val());

            fn_cargando(0);

        }

    });


//    fn_cargando(0);
//    $("#modalRegistroCliente").hide();
//    $("#modalPay").show(200);
}


function fn_registrarClienteMasterData() {

    if (!validarCampos('pay_txtCedulaCliente'))
        return;
    if (!validarCampos('pay_txtNombres'))
        return;
    if (!validarCampos('pay_txtDireccion'))
        return;
    if (!validarCampos('pay_txtTelefono'))
        return;
    if (!validarCampos('pay_txtCorreo'))
        return;

    var ci = $("#pay_txtCedulaCliente").val();


    fn_cargando(1);
    var send;
    var autorizacion = "";//$("#clienteAutorizacion").val();
    send = {};
    send.metodo = "enviarCliente";
    send.autorizacion = autorizacion;
    send.tipoDocumento = (ci.length === 10) ? "CEDULA" : "RUC";
    send.documento = ci;
    send.descripcion = $("#pay_txtNombres").val();
    send.direccion = $("#pay_txtDireccion").val();
    send.telefono = $("#pay_txtTelefono").val();
    send.correo = $("#pay_txtCorreo").val();

    $.ajax({
        async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/clienteWSClientes.php", data: send, success: function (datos) {
            //alert(JSON.stringify(datos)); 
        }
        ,
        error: function (e) {

            localStorage.setItem("ls_documento", $("#pay_txtCedulaCliente").val());
            localStorage.setItem("ls_nombres", $("#pay_txtNombres").val());
            localStorage.setItem("ls_direccion", $("#pay_txtDireccion").val());
            localStorage.setItem("ls_telefono", $("#pay_txtTelefono").val());
            localStorage.setItem("ls_correo", $("#pay_txtCorreo").val());

            fn_cargando(0);
            resolve({
                "estado": 500,
                "mensaje": "Error de conexion."
            });
        }
    });
    fn_cargando(0);
    $("#modalRegistroCliente").hide();
    $("#modalPay").show(200);
}





async function fn_buscarCliente(id) {

    var ci = $(id).val();
    if (ci.length === 13) {

        if (!validarRUC(ci)) {
            alertify.error("El RUC no es válido o no es persona Natural.");
            return;
        }
    } else
    if (ci.length < 10 || ci.length > 10) {
        alertify.error("Ingrese una cédula válida.");
        return;
    }


    // busco en la base local.
    var unCliente = await BuscarClienteEnLocal(ci);
    if (unCliente.estado === 200) {
        llenarCamposCliente(unCliente);
        habilitarBotones();
        $("#paybtn1").click(pay_buscarCliente);
        $("#paybtn1").text('Siguiente');
        return
    }

    // Busco en Master Data.
    var unClienteMasterData = await fn_buscarEInsertarClienteMasterData(ci);

    if (unClienteMasterData.estado === 200) {
        llenarCamposCliente(unClienteMasterData);
        habilitarBotones();

    } else {
        var txt = $("#pay_txtNombres");
        habilitarBotones();

        $("#paybtn1").removeAttr('onclick');

        OpcionCLiente = "I";
        $("#paybtn1").click(guardarCliente);

        $("#paybtn1").text('Registrar');

        var txt = $("#pay_txtNombres");
        txt.focus();
        txt.click();
    }


    // pay_buscarCliente();
    // $("#pay_TeladocedulaCliente").hide();
}


































function fn_numericoFDZN(e) {

    ocultarTeclado();

    if (!$(e.target).closest("#pay_cedulaClienteAdmin").length) {
    }

    $("#pay_TeladocedulaCliente").empty();
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
    // $('#btn_ok_teclado').attr("onclick", "fn_buscarCliente( " + $(e).attr("id") + ")");
    $("#pay_TeladocedulaCliente").css({
        display: "block",
        position: "absolute"

    });

    $("#pay_TeladocedulaCliente").append(num7 + num8 + num9 + "<br/>" + num4 + num5 + num6 + "<br/>" + num1 + num2 + num3 + "<br/>" + num0 + btnOk + "<br/>" + borrarCaracter + borrarTodo);


    // $("#pay_TeladocedulaCliente").css("left", "780px");
    //     $("#pay_TeladocedulaCliente").css("top", "220px"); 
    if (e === "#pay_txtCedulaCliente" || e === "#pay_txtCedulaCliente") {
        $("#pay_TeladocedulaCliente").css("left", "680px");
        $("#pay_TeladocedulaCliente").css("top", "160px");
    } else {
        $("#pay_TeladocedulaCliente").css("left", "42%");
        $("#pay_TeladocedulaCliente").css("top", "37%");
    }

}



function fn_alfaNumerico_EscribirNombre(e) {

    ocultarTeclado();

    $("#dominio3").css({
        display: "none",
        position: "absolute"
    });

    $("#dominio4").css({
        display: "none",
        position: "absolute"
    });

    if (!$(e.target).closest("#pay_TeladoNombres").length) {
    }

    $("#pay_TeladoNombres").empty();
    var posicion = $(e).position();
    var leftPos = 155;
    var topPos = 250;

    leftPos: posicion.left;
    topPos: posicion.top;

    var num1 = "<button  class='btnVirtual, btnVirtual' onclick=''>1</button>";
    var num2 = "<button  class='btnVirtual, btnVirtual' onclick=''>2</button>";
    var num0 = "<button  class='btnVirtual, btnVirtual' onclick=''>0</button>";
    var num3 = "<button  class='btnVirtual, btnVirtual' onclick=''>3</button>";
    var num4 = "<button  class='btnVirtual, btnVirtual' onclick=''>4</button>";
    var num5 = "<button  class='btnVirtual, btnVirtual' onclick=''>5</button>";
    var num6 = "<button  class='btnVirtual, btnVirtual' onclick=''>6</button>";
    var num7 = "<button  class='btnVirtual, btnVirtual' onclick=''>7</button>";
    var num8 = "<button  class='btnVirtual, btnVirtual' onclick=''>8</button>";
    var num9 = "<button  class='btnVirtual, btnVirtual' onclick=''>9</button>";

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

    btnOk = "<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnOk1(" + $("#pay_txtDireccion").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";

    if (e === "#holderName") {
        btnOk = "<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnOkPayphoneCerrar();'>OK</button>";
    }

    btnBorrarTodo = "<button class='btnVirtualBorrarTodo' id='btn_ok_borrar_todo'  onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>Borrar</button>";

    $("#pay_TeladoNombres").css({
        display: "block",
        position: "absolute",
        top: "460px",
        left: "170px"
    });

    $("#pay_TeladoNombres").append(num1 + num2 + num3 + num4 + num5 + num6 + num7 + num8 + num9 + num0 + borrarCaracter + "<br/>" + cadQ + cadW + cadE + cadR + cadT + cadY + cadU + cadI + cadO + cadP + borrarTodo + "<br/>" + arroba + cadA + cadS + cadD + cadF + cadG + cadH + cadJ + cadK + cadL + guion + "<br/>" + numeral + barraBaja + cadZ + cadX + cadC + cadV + cadB + cadN + cadM + cadEnie + punto + "<br/>" + btnBorrarTodo + espacio + btnOk);
}




function fn_alfaNumerico_EscribirDireccion(e) {

    ocultarTeclado();

    $("#dominio3").css({
        display: "none",
        position: "absolute"
    });

    $("#dominio4").css({
        display: "none",
        position: "absolute"
    });

    if (!$(e.target).closest("#pay_TeladoNombres").length) {
    }

    $("#pay_TeladoNombres").empty();
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
    btnOk = "<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnOk1(" + $("#pay_txtTelefono").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";
    btnBorrarTodo = "<button class='btnVirtualBorrarTodo' id='btn_ok_borrar_todo'  onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>Borrar</button>";

    $("#pay_TeladoNombres").css({
        display: "block",
        position: "absolute",
        top: "460px",
        left: "170px"
    });

    $("#pay_TeladoNombres").append(num1 + num2 + num3 + num4 + num5 + num6 + num7 + num8 + num9 + num0 + borrarCaracter + "<br/>" + cadQ + cadW + cadE + cadR + cadT + cadY + cadU + cadI + cadO + cadP + borrarTodo + "<br/>" + arroba + cadA + cadS + cadD + cadF + cadG + cadH + cadJ + cadK + cadL + guion + "<br/>" + numeral + barraBaja + cadZ + cadX + cadC + cadV + cadB + cadN + cadM + cadEnie + punto + "<br/>" + btnBorrarTodo + espacio + btnOk);
}



function fn_agregarCaracterTarjeta(obj, valor) {

    var temporal = "";

    var lc_cantidad = $(obj).val();
    temporal = lc_cantidad + valor;


    if (obj.id === "cardNumber") {

        var lc_cantidadSinEnmascarar = $("#cardNumberSinEnmascarar").val();
        temporalSinEnmascarar = lc_cantidadSinEnmascarar + valor;
        $("#cardNumberSinEnmascarar").val(temporalSinEnmascarar);


        if (temporal.length > 4 && temporal.length <= 11) {
            lc_cantidad += "X";
        } else {
            if (temporal.length > 11) {
                lc_cantidad += valor;
            } else {
                lc_cantidad += valor;
            }

        }

    } else {
        lc_cantidad += valor;
    }



    $(obj).val(lc_cantidad);


}

function fn_tecladoNumTelefono(e) {

    $("#dominio3").css({
        display: "none",
        position: "absolute"
    });

    $("#dominio4").css({
        display: "none",
        position: "absolute"
    });

    if (!$(e.target).closest("#pay_TeladoNombres").length) {
    }

    $("#pay_TeladoNombres").empty();
    var posicion = $(e).position();
    var leftPos = 155;
    var topPos = 250;

    leftPos: posicion.left;
    topPos: posicion.top;

    var num0 = "<button class='btnVirtual' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",0)'>0</button>";
    var num1 = "<button class='btnVirtual' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",1)'>1</button>";
    var num2 = "<button class='btnVirtual' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",2)'>2</button>";
    var num3 = "<button class='btnVirtual' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",3)'>3</button>";
    var num4 = "<button class='btnVirtual' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",4)'>4</button>";
    var num5 = "<button class='btnVirtual' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",5)'>5</button>";
    var num6 = "<button class='btnVirtual' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",6)'>6</button>";
    var num7 = "<button class='btnVirtual' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",7)'>7</button>";
    var num8 = "<button class='btnVirtual' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",8)'>8</button>";
    var num9 = "<button class='btnVirtual' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",9)'>9</button>";

    var cadQ = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"Q\")'>Q</button>";
    var cadW = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"W\")'>W</button>";
    var cadE = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"E\")'>E</button>";
    var cadR = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"R\")'>R</button>";
    var cadT = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"T\")'>T</button>";
    var cadY = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"Y\")'>Y</button>";
    var cadU = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"U\")'>U</button>";
    var cadI = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"I\")'>I</button>";
    var cadO = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"O\")'>O</button>";
    var cadP = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"P\")'>P</button>";

    var cadA = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"A\")'>A</button>";
    var cadS = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"S\")'>S</button>";
    var cadD = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"D\")'>D</button>";
    var cadF = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"F\")'>F</button>";
    var cadG = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"G\")'>G</button>";
    var cadH = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"H\")'>H</button>";
    var cadJ = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled'onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"J\")'>J</button>";
    var cadK = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"K\")'>K</button>";
    var cadL = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"L\")'>L</button>";

    var cadZ = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"Z\")'>Z</button>";
    var cadX = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"X\")'>X</button>";
    var cadC = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"C\")'>C</button>";
    var cadV = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"V\")'>V</button>";
    var cadB = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"B\")'>B</button>";
    var cadN = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"N\")'>N</button>";
    var cadM = "<button disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"M\")'>M</button>";

    var arroba = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"@\")'>@</button>";
    var guion = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"-\")'>-</button>";
    var barraBaja = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"_\")'>_</button>";

    var numeral = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"#\")'>#</button>";
    var espacio = "";
    espacio = "<button disabled='disabled' class='btnEspaciadora, btnEspaciadora_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\" \")'>Espacio</button>";
    var cadEnie = "<button  disabled='disabled' class='btnVirtual, btnVirtual_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\"&Ntilde;\")'>&Ntilde;</button>";
    var coma = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\",\")'>,</button>";
    var punto = "<button disabled='disabled' class='btnVirtualBorrar, btnVirtualBorrar_disabled' onclick='fn_agregarCaracterTarjeta(" + $(e).attr("id") + ",\".\")'>.</button>";

    var borrarCaracter = "<button class='btnVirtualBorrar' onclick='fn_eliminarNumero(" + $(e).attr("id") + ")'>&larr;</button>";
    var borrarTodo = "<button class='btnVirtualBorrar' onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>&lArr;</button>";
    var btnOk = "<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnOk1(" + $("#pay_txtCorreo").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";

    if (e === "#cardNumber") {
        btnOk = "<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnOkPayphone(" + $("#expirationMonth").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";
    } else if (e === "#expirationMonth") {
        btnOk = "<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnOkPayphone(" + $("#expirationYear").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";
    } else if (e === "#expirationYear") {
        btnOk = "<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnOkPayphone(" + $("#securityCode").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";

    } else if (e === "#securityCode") {
        btnOk = "<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnOkPayphone(" + $("#holderName").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";
    }

    var btnBorrarTodo = "<button class='btnVirtualBorrarTodo' id='btn_ok_borrar_todo'  onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>Borrar</button>";


    $("#pay_TeladoNombres").css({
        display: "block",
        position: "absolute",
        top: "460px",
        left: "170px"
    });

    $("#pay_TeladoNombres").append(num1 + num2 + num3 + num4 + num5 + num6 + num7 + num8 + num9 + num0 + borrarCaracter + "<br/>" + cadQ + cadW + cadE + cadR + cadT + cadY + cadU + cadI + cadO + cadP + borrarTodo + "<br/>" + arroba + cadA + cadS + cadD + cadF + cadG + cadH + cadJ + cadK + cadL + guion + "<br/>" + numeral + barraBaja + cadZ + cadX + cadC + cadV + cadB + cadN + cadM + cadEnie + punto + "<br/>" + btnBorrarTodo + espacio + btnOk);
}



function fn_alfaNumericoCorreo(e) {

    $("#keyboard").hide();
    $("#keyboard").empty();
    $("#numPadCliente").hide();
    $("#pay_TeladocedulaCliente").hide();
    cargarTeclasCorreo();

    if (($(e).attr("id")) == 'pay_txtCorreo'){
        cargarTeclasCorreo("pay_txtCorreo"); 
     }else {
        cargarTeclasCorreo("correoLink"); 
     }


    if (!$(e.target).closest("#pay_TeladoNombres").length) {
    }


    if (($(e).attr("id")) == 'pay_txtCorreo' || ($(e).attr("id")) == 'correoLink' ) {
        $("#dominio3").css({
            display: "block",
            position: "absolute",
            top: "460px",
            left: "40px"
        });
        $("#dominio4").css({
            display: "block",
            position: "absolute",
            top: "460px",
            left: "885px"
        });
    } else {
        $("#dominio3").css({
            display: "none",
            position: "absolute"
        });
        $("#dominio4").css({
            display: "none",
            position: "absolute"
        });
    }

    $("#pay_TeladoNombres").empty();
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
    var punto = "<button class='btnVirtualBorrar' onclick='fn_agregarCaracterPunto(" + $(e).attr("id") + ",\".\")'>.</button>";

    var borrarCaracter = "<button class='btnVirtualBorrar' onclick='fn_eliminarNumero(" + $(e).attr("id") + ")'>&larr;</button>";
    var borrarTodo = "<button class='btnVirtualBorrar' onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>&lArr;</button>";
    var btnOk = "<button class='btnVirtualOK' id='btn_ok_pad' onclick='fn_btnCerrarTodo(" + $("#pay_TeladoNombres").attr("id") + ", " + $(e).attr("id") + ");'>OK</button>";
    var btnBorrarTodo = "<button class='btnVirtualBorrarTodo' id='btn_ok_borrar_todo'  onclick='fn_eliminarTodo(" + $(e).attr("id") + ")'>Borrar</button>";

    $("#pay_TeladoNombres").css({
        display: "block",
        position: "absolute",
        top: "460px",
        left: "170px"
    });
    $("#pay_TeladoNombres").append(num1 + num2 + num3 + num4 + num5 + num6 + num7 + num8 + num9 + num0 + borrarCaracter + "<br/>" + cadQ + cadW + cadE + cadR + cadT + cadY + cadU + cadI + cadO + cadP + borrarTodo + "<br/>" + arroba + cadA + cadS + cadD + cadF + cadG + cadH + cadJ + cadK + cadL + guion + "<br/>" + numeral + barraBaja + cadZ + cadX + cadC + cadV + cadB + cadN + cadM + cadEnie + punto + "<br/>" + btnBorrarTodo + espacio + btnOk);
}

function fn_agregarCaracterPunto(obj, valor){
    var lc_cantidad=$(obj).val();
    lc_cantidad += valor;
    $(obj).val(lc_cantidad);
}
function cargarTeclasCorreo(id) {

    send = {"cargaTeclasEmail": 1};
    send.cdn_id = $("#txtCadenaId").val();
    $.ajax({
        async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../seguridades/config_usuario.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {

                $("#dominio3").empty();
                $("#dominio4").empty();
                for (i = 0; i < datos.str; i++) {
                    html = "<button class='btnVirtualDominio' style='font-size: 13px;' onclick='fn_agregarCaracterCorreo( "+id+"  ,\"" + datos[i]['descripcionEmail'] + "\")'>" + datos[i]['descripcionEmail'] + "</button><br/>";
                    if (i < 5) {
                        $("#dominio3").append(html);
                    }
                    if (i > 4) {
                        $("#dominio4").append(html);
                    }
                }
            }
        }
    });
}



function fn_btnOk1(e, p) {

    var txt = $("#" + e.id);
    txt.focus();
    txt.click();
    if (e.id !== "pay_txtCorreo") {
        $("#dominio3").hide();
        $("#dominio4").hide();
    }

}
function fn_btnOkPayphone(e, p) {

    var txt = $("#" + e.id);
    txt.focus();
    txt.click();

}

function fn_btnOkPayphoneCerrar() {
    // background-color: #d8cccc; Disable
    // rgb(246, 146, 31); Enable


    $("#pay_TeladoNombres").empty();
    $("#pay_TeladoNombres").hide();
    $("#dominio3").hide();
    $("#dominio4").hide();

    if (!validarCampos('cardNumber'))
        return
    if (!validarCampos('expirationMonth'))
        return
    if (!validarCampos('expirationYear'))
        return
    if (!validarCampos('securityCode'))
        return
    if (!validarCampos('holderName'))
        return

    $("#btnPagar").removeAttr("disabled");
    $("#btnPagar").css({
        'background-color': 'rgb(246, 146, 31)'
    });

}
function fn_btnCerrarTodo(e, p) {

    $("#pay_TeladoNombres").empty();
    $("#pay_TeladoNombres").hide();
    $("#dominio3").hide();
    $("#dominio4").hide();


}


/////////////////////////////ELIMINAR CARACTER/////////////////////////////////
function fn_eliminarNumero(e) {
    if (e.id === "cardNumber") {
        var lc_cantidad1 = $("#cardNumberSinEnmascarar").val();
        lc_cantidad1 = lc_cantidad1.substring(0, lc_cantidad1.length - 1);
        $("#cardNumberSinEnmascarar").val(lc_cantidad1);
    }
    var lc_cantidad = $(e).val();
    lc_cantidad = lc_cantidad.substring(0, lc_cantidad.length - 1);
    $(e).val(lc_cantidad);
}

/////////////////////////////ELIMINAR TODO LA CADENA///////////////////////////
function fn_eliminarTodo(e) {

    if (e.id === "cardNumber") {
        $("#cardNumberSinEnmascarar").val("");
    }
    $(e).val('');
}