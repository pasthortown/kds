var cuenta = '_1';
if ((localStorage.getItem("cuenta") != '') && (localStorage.getItem("cuenta") != null)){
    cuenta = '_'+localStorage.getItem("cuenta");
}
var cuenta_separada = localStorage.getItem('tipo');
if ((cuenta_separada != '') && (cuenta_separada != null)){
    cuenta_separada = 1;
}else{
    cuenta_separada = 0;
}
$(document).ready(function () {
    $("#pay_txtCedulaCliente").attr("disabled", false);
    fn_cargando(0);
    limpiarBeneficios(cuenta_separada);
    $("#pay_txtCedulaCliente").focus();
    activarBotones(false);
    fn_PayobtieneInformacionFormulario(0);
    if ((localStorage.getItem("ls_hayCliente") == 1) && (localStorage.getItem("ls_documento"+cuenta) != '') && localStorage.getItem("ls_documento"+cuenta) != null){
        cancelarTeclados();
        $("#pay_txtCedulaCliente").val(localStorage.getItem("ls_documento"+cuenta));
        $("#validacion").val(localStorage.getItem("ls_typeDocument"+cuenta));
        $("#closeModal").removeClass("hidden");
        fn_buscarCliente("#pay_txtCedulaCliente", 0);
        //$("#pay_txtCedulaCliente").attr("disabled", true);
    }else{
        modalDatosCliente(FORMULARIO_CLIENTES);
        $("#btn_opciones").show();
        $("#closeModal").addClass("hidden");
        $("#pay_txtCedulaCliente").attr("disabled", false);
    }
});

function activarFocusLectorBarra(activar) {
    if (activar) {
        $(document).keydown(teclado);
    } else {
        $(document).off("keydown");
    }
}

function modalDatosCliente(estado) {
    if (estado == 1) {
        $("#ModalRegistroDatosDomicilio").show();
        pay_numericoCliente('#pay_txtCedulaCliente', 0);
    } else {
        $("#ModalRegistroDatosDomicilio").hide();
    }
}

function btn1Text(opcion) {
    if(opcion == 'Siguiente'){
        $("#btn_opciones").hide();  
    }else{
        $("#btn_opciones").show();  
    }
    $("#btn_opciones").text(opcion);
}

function btn2Text(opcion) {
    $("#btn_opcionesGuardar").text(opcion);
}

function activarBotones(estado) {
//    $("#pay_txtCedulaCliente").prop('disabled', true);
    $("#pay_txtTelefono").prop('disabled', !estado);
    $("#pay_txtNombres").prop('disabled', !estado);
    $("#pay_txtCorreo").prop('disabled', !estado);
    $("#pay_txtDireccion").prop('disabled', !estado);
    $("#pay_numeroCallePrincipal").prop('disabled', !estado);
    $("#pay_calleSecundaria").prop('disabled', !estado);
    $("#selectTiposInmuebles").prop('disabled', !estado);
    $("#pay_referenciaTipoInmueble").prop('disabled', !estado);
    $("#pay_referencia").prop('disabled', !estado);
}

function activarBotones(estado) {
//    $("#pay_txtCedulaCliente").prop('disabled', true);
    $("#pay_txtTelefono").prop('disabled', !estado);
    $("#pay_txtNombres").prop('disabled', !estado);
    $("#pay_txtCorreo").prop('disabled', !estado);
    $("#pay_txtDireccion").prop('disabled', !estado);
    $("#pay_numeroCallePrincipal").prop('disabled', !estado);
    $("#pay_calleSecundaria").prop('disabled', !estado);
    $("#selectTiposInmuebles").prop('disabled', !estado);
    $("#pay_referenciaTipoInmueble").prop('disabled', !estado);
    $("#pay_referencia").prop('disabled', !estado);
}

function limpiarCampos() {
    //$("#pay_txtCedulaCliente").attr('disabled', true);
    $("#pay_txtTelefono").val("");
    $("#pay_txtNombres").val("");
    $("#pay_txtCorreo").val("");
    $("#pay_txtDireccion").val("");
    $("#pay_numeroCallePrincipal").val("");
    $("#pay_calleSecundaria").val("");
    $("#selectTiposInmuebles").val("");
    $("#pay_referenciaTipoInmueble").val("");
    $("#pay_referencia").val("");
}

function cancelarPayPhone() {
    modalDatosCliente(0);
    localStorage.setItem("ls_PayPhoneFormularioEnOrdenPedido", 0);
    fn_actualizarOrdenPedidoApp('C');
    localStorage.setItem("ls_hayCliente", 0);
    activarFocusLectorBarra(true);// Lector de barras
    cancelarTeclados();
    limpiarBeneficios();
}

function limpiarBeneficios(cuenta_separada = 0){
    if (cuenta_separada === 0){
        $('#nfrmcn_srs_sstm_hora').html("");
        localStorage.setItem("ls_telefono"+cuenta, "");
        localStorage.setItem("ls_nombres"+cuenta, "");
        localStorage.setItem("ls_correo"+cuenta, "");
        localStorage.setItem("ls_documento"+cuenta, "");
        var id_beneficio = localStorage.getItem("dop_beneficio"+cuenta);
        if ((id_beneficio != '') && (id_beneficio != null)){
            $('#'+id_beneficio).addClass('focus');
            fn_eliminarElemento('1');
        }
    }
}

function ejecutarOpciones() {
    console.log('ejeuctar acciones');
    localStorage.setItem("intValidaOP", localStorage.getItem("intValida"));

    var opcion = $("#btn_opciones").text();

    switch (opcion) {
        case 'Cancelar':
        
            if(localStorage.getItem("ls_hayCliente") === "1") {
                Swal.fire({
                    title: '¿Está Usted Seguro?',
                    text: "Si cancela tendrá que ingresar nuevamente los datos para facturar.",
                    icon: 'warning',
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Si, Cancelar!',
                    cancelButtonText: 'Mantener datos!'
                  }).then((result) => {
                    if (result.value) {
                        modalDatosCliente(0);
                        localStorage.setItem("ls_PayPhoneFormularioEnOrdenPedido", 0);
                        fn_actualizarOrdenPedidoApp('C');
                        localStorage.setItem("ls_hayCliente", 0);
                        activarFocusLectorBarra(true);// Lector de barras
                        cancelarTeclados();
                    } else {
                        almacenarClienteLocalStorage();
                        cancelarTeclados();
                        modalDatosCliente(0);
                        activarFocusLectorBarra(true);// Lector de barras
                        return ;
                    }
                  })
            }else {
                modalDatosCliente(0);
                localStorage.setItem("ls_PayPhoneFormularioEnOrdenPedido", 0);
                fn_actualizarOrdenPedidoApp('C');
                localStorage.setItem("ls_hayCliente", 0);
                activarFocusLectorBarra(true);// Lector de barras
                cancelarTeclados();
            }
            
            break;
        case 'Siguiente':

            almacenarClienteLocalStorage();
            modalDatosCliente(0);
            fn_actualizarOrdenPedidoApp('A');
            localStorage.setItem("ls_hayCliente", 1);
            activarFocusLectorBarra(true);// Lector de barras

            cancelarTeclados();


            break;
        case 'Actualizar':
            console.log('');
            break;
        default:
            console.log('Lo lamentamos, por el momento no disponemos de  ');
    }
}


async  function ejecutarOpcionesGuardar() {

    if (!validarCampos('pay_txtCedulaCliente'))
        return;

    if (!validarCampos('pay_txtTelefono'))
        return;

    if (!validarCampos('pay_txtNombres'))
        return;

    if (!validarCampos('pay_txtCorreo'))
        return;

    var isChecked = $("input[name=es_domicilio]").prop("checked");
    if (isChecked == true){
        if (!validarCampos('pay_txtDireccion'))
            return;
    }

    var opcion = $("#btn_opcionesGuardar").text();

    switch (opcion) {
        case 'Guardar':
            cancelarTeclados();

            var resultado = await guardarCliente("I");

            if (resultado.estado === 200) {
                localStorage.setItem("ls_hayCliente", 1);
                almacenarClienteLocalStorage();
                btn1Text("Siguiente");

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
                        modalDatosCliente(0);
                        fn_actualizarOrdenPedidoApp('A');
                        activarFocusLectorBarra(true);// Lector de barras
                    }
                })
            } else {
                localStorage.setItem("ls_hayCliente", 0);

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
                        console.log(result);
                    }
                })
            }

            break;

        case 'Actualizar':
            cancelarTeclados();

            var resultado = await guardarCliente("U");

            if (resultado.estado === 200) {
                localStorage.setItem("ls_hayCliente", 1);
                almacenarClienteLocalStorage();
                btn1Text("Siguiente");


                Swal.fire({
                    title: 'Cliente actualizado',
                    text: "",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.value) {
                        modalDatosCliente(0);
                        fn_actualizarOrdenPedidoApp('A');
                        activarFocusLectorBarra(true);// Lector de barras
                    }
                })
            } else {
                localStorage.setItem("ls_hayCliente", 0);

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
                        console.log(result);
                    }
                })
            }


            break;


        case 'Aceptar':
            almacenarClienteLocalStorage();
            cancelarTeclados();
            fn_actualizarOrdenPedidoApp('A');
            modalDatosCliente(0);
            activarFocusLectorBarra(true);// Lector de barras
            break;

        default:
            console.log('Lo lamentamos  ');
    }
}

function fn_PayobtieneInformacionFormulario(facturacion) {
    var send = {"obtieneInformacionFormulario": 1};
    send.odp_id = $("#hide_odp_id").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_ordenPedido.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                if (datos[0].datos === 1) { // Hay datos
                    if (localStorage.getItem("ls_PayPhoneFormularioEnOrdenPedido") === "1") {
                        btn2Text("Aceptar");
                        modalDatosCliente(1);
                        var tipo = localStorage.getItem('tipo');
                        if ((tipo === null) || (tipo === '')){
                            llenarCamposCliente(datos[0], facturacion, cuenta);
                            localStorage.setItem("ls_documento"+cuenta, datos[0]["documento"]);
                        }
                        localStorage.setItem("ls_hayCliente", 1);
                        activarFocusLectorBarra(false);
                        console.log(datos);
                    }
                } else {
                    if (localStorage.getItem("ls_PayPhoneFormularioEnOrdenPedido") === "1") {
                        localStorage.setItem("ls_hayCliente", 0);
                        modalDatosCliente(1);
                        activarFocusLectorBarra(false);
                    }
                }

            } else {

            }

        }
        , error: function (e) {

        }
    });
}

function fn_actualizarOrdenPedidoApp( accion ) {
    return new Promise(function (resolve, reject) {
        var dom_direccion = '';
        var dire_numero = $("#pay_txtDireccion").val().split(" ")[1];
        var dom_numero = $("#pay_numeroCallePrincipal").val();

        if (dire_numero == dom_numero){
            dom_direccion = $("#pay_txtDireccion").val();
        }else{
            dom_direccion = $("#pay_txtDireccion").val() + " " + $("#pay_numeroCallePrincipal").val();
        }

        var send = {"actualizarOrdenPedidoApp": 1};
        send.odp_id = $("#hide_odp_id").val();
        send.cliente = $("#pay_txtNombres").val();
        send.telefono = $("#pay_txtTelefono").val();
        send.direccion = dom_direccion;
        send.direccion2 = $("#pay_calleSecundaria").val();
        send.cedulaCliente = $("#pay_txtCedulaCliente").val();
        send.accion = accion;
        send.observaciones = $("#pay_referencia").val();
        send.medio = $("#pay_medio").val(); 
        send.latitud = $("#coords-lat").val();
        send.longitud = $("#coords-long").val();
        send.tipoInmueble = $("#selectTiposInmuebles").val(); 
        send.numeroInmueble = $("#pay_referenciaTipoInmueble").val(); 
        send.numeroCallePrincipal = $("#pay_numeroCallePrincipal").val();
        send.acepta_beneficio = $('#acepta_beneficio').val();
        send.email = $("#pay_txtCorreo").val();
        if ( accion == 'A' ){
            var docuIdent = $("#pay_txtCedulaCliente").val();
            var typeDocument = ( docuIdent.length === 10 ) ? "CEDULA" : "RUC";
            send.argvTDI = (localStorage.getItem("ls_typeDocument"+cuenta) !== undefined && localStorage.getItem("ls_typeDocument"+cuenta) === "pasaporte")
                           ? "PASAPORTE" : typeDocument;
        }

        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_ordenPedido.php",
            data: send,
            success: function (datos) {
                var html = '';
                if ($("#pay_txtCedulaCliente").val() !== ''){
                    html += '<tr><td>CLIENTE:  '+$("#pay_txtNombres").val()+'</td></tr>';
                    html += '<tr><td>TELEFONO: '+$("#pay_txtTelefono").val()+'</td></tr>';
                    /*html += '<tr><td><p>DIRECCIÓN:  '+dom_direccion+' '+$("#pay_calleSecundaria").val()+'</p></td></tr>';
                    html += '<tr><td></td></tr>';
                    html += '<tr><td></td></tr>';
                    html += '<tr><td><p>DATOS ADICIONALES: <</p></td></tr>';
                    html += '<tr><td><p>OBSERVACIONES PEDIDO: '+$("#pay_referencia").val()+'</p></td></tr>';
                    html += '<tr><td>ZIP-CODE: </td></tr>';*/
                }
                $('#nfrmcn_srs_sstm_hora').html(html);
                resolve({
                    "estado": 200,
                    "mensaje": "Cliente actualizado."
                });
            }
            , error: function (e) {

            }

        });
    });
}


function guardarCliente(option) {
    return new Promise(function (resolve, reject) {

        var ci = $("#pay_txtCedulaCliente").val();
        var send = {"guardarCliente": 1};
        send.accion = option;
        send.tipoConsulta = "W";
        send.clienteTipoDoc = (ci.length === 10) ? "CEDULA" : "RUC";
        send.clienteDocumento = ci;
        send.clienteDescripcion = $("#pay_txtNombres").val();
        send.clienteDireccion = $("#pay_txtDireccion").val();
        send.clienteFono = $("#pay_txtTelefono").val();
        send.clienteCorreo = $("#pay_txtCorreo").val();
        send.usuario = $("#hide_usr_id").val();
        send.estadoWS = 0;
        send.tipoCliente = "NORMA";        

        var cadenaJSON = {
            "TiposInmuebles": $("#selectTiposInmuebles").val(),
            "numeroCallePrincipal": $("#pay_numeroCallePrincipal").val(),
            "calleSecundaria": $("#pay_calleSecundaria").val(),
            "referenciaTipoInmueble": $("#pay_referenciaTipoInmueble").val(),
            "referencia": $("#pay_referencia").val(),
            "latitud" : $("#coords-lat").val(),
            "longitud" : $("#coords-long").val()
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
                if (datos === 1) {
                    resolve({
                        "estado": 200,
                        "mensaje": "Cliente registrado."
                    });
                } else {
                    resolve({
                        "estado": 204,
                        "mensaje": "Error de conexion."
                    });
                }

            }
            , error: function (e) {

                resolve({
                    "estado": 500,
                    "mensaje": "Error de conexion."
                });
            }

        });

    })

}


function almacenarClienteLocalStorage() {
    localStorage.setItem("ls_documento"+cuenta, $("#pay_txtCedulaCliente").val());
    localStorage.setItem("ls_nombres"+cuenta, $("#pay_txtNombres").val());
    localStorage.setItem("ls_direccion"+cuenta, $("#pay_txtDireccion").val());
    localStorage.setItem("ls_telefono"+cuenta, $("#pay_txtTelefono").val());
    localStorage.setItem("ls_correo"+cuenta, $("#pay_txtCorreo").val());
    localStorage.setItem("ls_latitud"+cuenta, $("#coords-lat").val());
    localStorage.setItem("ls_longitud"+cuenta, $("#coords-long").val());
}

async function fn_buscarCliente(id, valid) {
    var ci = $(id).val();
    var validacion = $('#validacion').val();
    localStorage.setItem("ls_typeDocument"+cuenta, $('#validacion').val());
    localStorage.setItem("ls_documento"+cuenta, ci);

    if (ci !== ''){
        if (validacion == 'cedula'){
            if (ci.length === 13) {
                if (!validarRUC(ci)) {
                    alertify.error("El RUC no es válido o no es persona Natural.");
                    limpiarCampos();
                    return;
                }
            } else if (ci.length === 10){
                if (!validarCedula(ci)) {
                    alertify.error("Número de documento no válido.");
                    limpiarCampos();
                    return;
                }    
            }else{
                if (ci.length < 10 || ci.length > 10) {
                    alertify.error("Ingrese una cédula válida.");
                    limpiarCampos();
                    return;
                }       
            }
        }

        activarBotones(true);
        // busco en la base local.
        var unCliente = await BuscarClienteEnLocal(ci);
        if (unCliente.estado === 200) {
            $("#btn_opCancelar").show();
            if(valid != 0){
                if(localStorage.getItem('intValidaOP') === undefined || localStorage.getItem('intValidaOP') === null){
                    console.log('no existe la config, se procede a cargarla')
                    localStorage.setItem('intValida', valid);
                    localStorage.setItem('intValidaFC', valid);
                    localStorage.setItem('intValidaOP', valid);
                }
                fn_validaEmailRegisteredAPI(unCliente.email);
            }
            //$("#btn_opciones").hide();
            cancelarTeclados();
            btn1Text("Siguiente");
            btn2Text("Aceptar");
            llenarCamposCliente(unCliente);
            $("#div_adminPasaporte").css("display", "none");
            return;
        }

        // Busco en Master Data.
        var unClienteMasterData = await fn_buscarEInsertarClienteMasterData(ci);
        if (unClienteMasterData.estado === 200) {
            if(valid != 0){
                if(localStorage.getItem('intValidaOP') === undefined || localStorage.getItem('intValidaOP') === null){
                    console.log('no existe la config, se procede a cargarla')
                    localStorage.setItem('intValida', valid);
                    localStorage.setItem('intValidaFC', valid);
                    localStorage.setItem('intValidaOP', valid);
                }
                fn_validaEmailRegisteredAPI(unClienteMasterData.email);
            }
            llenarCamposCliente(unClienteMasterData);
            btn1Text("Siguiente");
            btn2Text("Aceptar");
        } else {
            alertify.error("Cliente no encontrado.");
            btn2Text("Guardar");
            btn1Text("Cancelar");
            limpiarCampos();
            $("#pay_txtCedulaCliente").attr("disabled", false);
            $("#div_adminPasaporte").css("display", "none");
        }
    }
}

async function fn_validarCodigoPromocion(otp, odp_id,categoria_rst, menuId){
    var ci = $(otp).val();

    var result = await fn_validarOTPTirillaPromocion(ci)
    console.log(result)
    if (result.estado === 200) {
        cancelarTeclados();
        fn_modalLeerPromocion(false);
        return fn_obtenerProducto(result.data.productId, odp_id, categoria_rst,menuId);
    } else {
        alertify.error(result.mensaje);
    }

}

function fn_validaEmailRegisteredAPI(email) {
    var intentos = localStorage.getItem("intValidaOP");
    if(localStorage.getItem("intValidaOP") > 0){
        var intentosOP = intentos - 1;
        localStorage.setItem("intValidaOP", intentosOP);
        send = {"cargarUrlApiValidaPlugthem": 1};
        $.ajax({
             async: false, type: "POST", dataType: 'json', url: "../ordenpedido/config_ordenPedido.php", data: send,
             success: function (datos) {
               var urlService = datos;
                    $.ajax({
                    async: true,
                    type: "POST",
                    dataType: "json",
                    //contentType: "application/json",
                    url: urlService,
                    data: { CustomerEmail: email.toString()},
                    success: function( datos ) {
                        console.log('RESPONSE VALIDACIÓN EMAIL REGISTERED APIAAAAAAA');
                        localStorage.setItem("intValidaOP", localStorage.getItem("intValidaOP") - 1);
                        if(datos['status'] == 'invalid'){
                            alertify.alert("El email ingresado no es v\u00e1lido. Por favor vuelva a verificar." + "Le restan " + intentosOP.toString() + " " + "intentos. Al culminarlos el correo será tomado como válido.");
                            if(localStorage.getItem("intValidaOP") > 0) {
                                alertify.alert("El email ingresado no es v\u00e1lido. Por favor vuelva a verificar." + "Le restan " + localStorage.getItem("intValidaOP").toString() + " " + "intentos. Al culminarlos el correo será tomado como válido.");
                                } else {
                                alertify.alert("El email ingresado no es v\u00e1lido. Ya agoto sus intentos de verificación, el correo será tomado como válido.");    
                                }
                        } 
                
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log('error validando emailaaaaaaaaa')
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                    });
            },
            error: function (jqXHR, textStatus, errorThrown) {
              console.log('error respuesta consulta sp de url de politica')
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
        });
    }
}

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

function llenarCamposCliente(ClienteJSON, facturacion = 0) {
    $("#txtDocumentoClientePaypone").val(ClienteJSON.documento);
    if (facturacion != 0){
        $("#pay_txtCedulaCliente").val(ClienteJSON.documento);
    }
    $("#pay_txtNombres").val(ClienteJSON.descripcion);
    $("#pay_txtDireccion").val(ClienteJSON.direccion);
    $("#pay_txtTelefono").val(ClienteJSON.telefono);
    $("#pay_txtCorreo").val(ClienteJSON.email);

    $("#closeModal").removeClass('hidden');

    try {
        var data = JSON.parse(ClienteJSON.jsonDatosAdicionales.replace(/\\/g, ""));
        $("#selectTiposInmuebles").val(data["TiposInmuebles"]);

        var dom_direccion = '';
        var dire_numero = $("#pay_txtDireccion").val().split(" ")[1];
        var dom_numero = $("#pay_numeroCallePrincipal").val();

        if (dire_numero == dom_numero){
            dom_direccion = $("#pay_txtDireccion").val();
        }else{
            dom_direccion = $("#pay_txtDireccion").val() + " " + $("#pay_numeroCallePrincipal").val();
        }

        $("#pay_numeroCallePrincipal").val(data["numeroCallePrincipal"]);
        $("#pay_txtDireccion").val(data["callePrincipal"]);
        $("#pay_calleSecundaria").val(data["calleSecundaria"]);
        $("#pay_referenciaTipoInmueble").val(data["referenciaTipoInmueble"]);
        $("#pay_referencia").val(data["referencia"]);
        $("#coords-lat").val(data["latitud"]);
        $("#coords-long").val(data["longitud"]);
    } catch (error) {
        //console.log(error);
    }
}


function habilitarBotones() {


}

function fn_cargando(std) {
    if (std > 0) {
        $("#mdl_rdn_pdd_crgnd").show();
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
    }
}


function validarRUC(documento) {
    var estado = false;
    var send = {"validarRUC": 1};
    send.documento = documento;
    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "../seguridades/config_usuario.php",
        data: send,
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

function validarCedula(documento) {
    var estado = false;
    var send = { "validarCedula": 1 };
    send.documento = documento;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../seguridades/config_usuario.php",
        data: send,
        success: function(datos) {
            if (datos["estado"] === "1") {
                estado = true;
            }
        }
    });
    return estado;
}

function BuscarClienteEnLocal(IDDocumento) {
    return new Promise(function (resolve, reject) {
        fn_cargando(1);
        existeClienteLocal = 0;
        var send = {"ConusltarExistente": 1};
        send.accion = 'B';
        send.numeroDocumento = IDDocumento;
        send.cdn_id = $('#hide_cdn_id').val();
        $.ajax({
            type: "POST",
            dataType: 'json',
            contentType: "application/x-www-form-urlencoded",
            url: "../seguridades/config_usuario.php",
            data: send,
            before: function (send) {
                console.log("The 'before' function was called with datos:", send);
            },
            success: function (datos) {
                var info_cliente = '';
                info_cliente = fn_ObtenerCliente(datos[0], IDDocumento.length);
                if (datos.str > 0) {
                    if (info_cliente !== ''){
                        datos[0]["estado"] = 200;
                        resolve(info_cliente);
                    }else{
                        alertify.error(
                            "Se ha producido un error. Por favor inténtelo nuevamente."
                        );
                    }
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
        send.revocado = 1;
        send.payphone =1;
        $.ajax({
            async: false, 
            type: "POST",
            dataType: "json",
            "Accept": "application/json, text/javascript2",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../facturacion/clienteWSClientes.php",
            data: send, 
            success: function (datos) {
                var cliente;

                if (datos["cliente"].IDCliente !== undefined  && datos["cliente"].IDCliente !== "" && datos["mensaje"] == 'baselocal' && datos["estado"] == 3) {
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

function fn_validarOTPTirillaPromocion(otp) {
    return new Promise(function (resolve) {
        fn_cargando(1);
        existeEnMasterData = 0;
        var send;
        send = {};
        send.metodo = "validarOTP";
        send.codigo_otp = otp;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            "Accept": "application/json, text/javascript2",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../ordenpedido/config_app.php",
            data: send,
            success: function (result) {
                const datos = JSON.parse(result);
                if (datos && datos['status'] == 200) {
                    resolve({
                        "estado": 200,
                        "mensaje": datos.message ,
                        "data": datos.response ,
                    });
                } else {
                    resolve({
                        "estado": datos.status,
                        "mensaje": datos.message
                    });
                }

                fn_cargando(0);

            }, error: function () {
                fn_cargando(0);
                resolve({
                    "estado": 500,
                    "mensaje": "Error de conexion."
                });
            }
        });
    })
}

function fn_obtenerProducto(productId, odp_id,categoria_rst, menuId) {
    return new Promise(function (resolve) {
        fn_cargando(1);
        existeEnMasterData = 0;
        var send;
        send = {};
        send.metodo = "obtenerProducto";
        send.productId = productId;
        send.odp_id = odp_id;
        send.menuId = menuId;
        send.cat_id = categoria_rst;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            "Accept": "application/json, text/javascript2",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../ordenpedido/config_app.php",
            data: send,
            success: function (result) {
                resolve({
                    "data": result ,
                });

                fn_cargando(0);

            }, error: function () {
                fn_cargando(0);
                resolve({
                    "estado": 500,
                    "mensaje": "Error de conexion."
                });
            }
        });
    })
}


function fn_registrarClienteMasterData() {
    fn_cargando(1);
    if ($("#pay_btnBuscar").text() === "Siguiente")
        return


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

function validarCampos(idInput) {
    if ($("#" + idInput).val() === "") {
        var txt = $("#" + idInput);
        alertify.error("Complete el campo " + txt.attr("desc"));
        txt.focus();
        txt.click();
        return false;
    } else {
        return true;
    }
}

function pedirDatosDocumento(){
    if (localStorage.getItem("dop_beneficio") !== null){
        $('#btn_beneficio').text('Agregar Beneficio');
        $('#btn_beneficio').removeClass('disabled');
        $('#btn_beneficio').removeAttr('disabled');
    }
    limpiarBeneficios();
    limpiarCampos()
    $(".segundo").css('display', 'none');
    $("#botones_cabecera").css('display', 'block');
    $("#botones_domicilio").css('display', 'none');
    $("#botones_segundos").css('display', 'none');
    $("#botones_primeros").css('display', 'flex');
    $("#closeModal").addClass('hidden');
    modalDatosCliente(1);
    $("#pay_txtCedulaCliente").attr("disabled", false);
    if(localStorage.getItem("ls_typeDocument") !== undefined && localStorage.getItem("ls_typeDocument") === "pasaporte"){
        $("#pasaporte_validar").addClass("active");
        $("#cedula_validar").removeClass("active");
        $('#validacion').val('pasaporte');
    }else{
        $("#pasaporte_validar").removeClass("active");
        $("#cedula_validar").addClass("active");
        $('#validacion').val('cedula');
    }
}

function fn_solicitaCredencialesAdministrador(beneficio = '') {    
    var send;
    send = {
        "solicitaCredencialesAdministrador": 1
    };
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/config_clientes.php",
        data: send,
        success: function(datos) {
            if (datos.solicita === 1) {
                fn_abremodalAdministradorPasporte();
            } else if (datos.solicita === 0) {
                fn_btnOk(numPadCliente);
                $("#rdo_ruc").removeClass('btnRucCiActivo');
                $("#rdo_ruc").addClass('btnRucCiInactivo');
                $("#rdo_pasaporte").removeClass('btnRucCiInactivo');
                $("#rdo_pasaporte").addClass('btnRucCiActivo');
                $("#hid_bandera_teclado").val(2);
                fn_alfaNumericoo(txtClienteCI);
                fn_bloquearIngreso();
                $("#txtClienteCI").focus();
                $("#nombres_obligatorios").show();
                $("#direccion_obligatorios").show();
            } else if (datos.solicita === 2 && localStorage.getItem("invalido") == 1) {                
                $('#btnClienteConfirmarDatos').hide();
                $('#btnClienteGuardar').hide();
                fn_abremodalAdministradorCedula();         
                $('#btnConsumidorFinal').show()
            } 
            else {
                fn_abremodalAdministradorPasporte(beneficio);
            }
        }
    });
}

///////////////////////////////////////////LIMPIAR DATOS////////////////////////////////
function fn_bloquearIngreso() {
    $("#pay_txtCedulaCliente").val("");
}

function fn_abremodalAdministradorPasporte(beneficio = '') {
    cancelarTeclados();
    if ((beneficio != '') && (beneficio == 1)){
        $("#input_cedulaBeneficio").val('');
    }else{
        $("#pay_txtCedulaCliente").val('');
    }
    $("#div_adminPasaporte").show();
    $("#div_adminPasaporte").dialog({
        modal: true,
        width: 550,
        heigth: 550,
        resize: true,
        opacity: 0,
        //show: "explode",
        //hide: "explode",
        duration: 5000,
        position: "center",
        closeOnEscape: false,
        open: function(event, ui) {
            $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
        }
    });
    if ((beneficio != '') && (beneficio == 1)){
        $('#fn_okPasaporte').attr('onclick', 'fn_okPasaporte(1);');
    }
    fn_ocultar_alfanumerico();
}

function fn_abremodalAdministrador() {
    cancelarTeclados();
    $("#pay_txtCedulaCliente").val('');
    $("#div_adminPasaporte").show();
    $("#div_adminPasaporte").dialog({
        modal: true,
        width: 550,
        heigth: 550,
        resize: true,
        opacity: 0,
        //show: "explode",
        //hide: "explode",
        duration: 5000,
        position: "center",
        closeOnEscape: false,
        open: function(event, ui) {
            $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
        }
    });

    fn_ocultar_alfanumerico();
}

function fn_ocultar_alfanumerico(){ 
    $("#keyboard").hide();
}

function fn_btnOk(e, p) {
    var txt = $("#" + e.id);
    txt.focus();
    txt.click();
    fn_validarCredencialesAdministrador();
}

function fn_canPasaporte() {
    localStorage.setItem("invalido",0);
    $('#validacion').val('cedula');
    $("#hid_bandera_teclado").val(1);
    $("#div_adminPasaporte").dialog("destroy");
    $("#rdo_ruc").removeClass("btnRucCiInactivo");
    $("#rdo_ruc").addClass("btnRucCiActivo");
    $("#rdo_pasaporte").removeClass("btnRucCiActivo");
    $("#rdo_pasaporte").addClass("btnRucCiInactivo");
    $("#pay_txtCedulaCliente").focus();
    $("#pay_txtCedulaCliente").val('');
    $("#pasaporte_validar").removeClass("active")
    $("#cedula_validar").addClass("active")
    $("#pay_txtCedulaCliente").attr("disabled", false);
    $("#div_adminPasaporte").css("display", "none");
}

function fn_okPasaporte(beneficio = '') {
    if ($("#txt_passPasaporte").val() == "") {
        alertify.alert("Ingrese una clave.");
        return false;
    }
    var usr_claveSinCupon = $("#txt_passPasaporte").val()
    var send;
    send = {
        "validarUsuarioCreditoSinCupon": 1
    };
    send.usr_claveSinCupon = usr_claveSinCupon;
    $.getJSON(
        "../facturacion/config_facturacion.php",
        send,
    function(datos) {
        if (datos.admini == 1) {
            if ((beneficio != '') && (beneficio == 1)){
                $("#btn_cl_ben_1").attr("checked", false);
                $("#btn_cl_ben_2").attr("checked", true);
                $("#div_adminPasaporte").dialog("destroy");
                $("#div_adminPasaporte").css("display", "none");
                $("#hid_bandera_teclado").val(2);
                $("#input_cedulaBeneficio").focus();
                $("#input_cedulaBeneficio").val('');
                localStorage.setItem("administrador", usr_claveSinCupon);
                pay_numericoClienteBeneficio('#input_cedulaBeneficio', 0);
            }else{
                $("#hid_bandera_teclado").val(2);
                $("#div_adminPasaporte").dialog("destroy");
                fn_bloquearIngreso();
                $("#pay_txtCedulaCliente").focus();
                if(localStorage.getItem("invalido")==1)
                fn_clienteBuscar(false, 0)
                $("#pay_txtCedulaCliente").attr("disabled", false);
                $("#div_adminPasaporte").css("display", "none");
                pay_numericoCliente('#pay_txtCedulaCliente', 0);
            }
        } else {
            alertify.confirm("Clave no autorizada. Ingrese clave de Administrador.", function(e) {
                if (e) {
                    alertify.set({
                        buttonFocus: "none"
                    });
                    $("#txt_passPasaporte").focus();
                }
            });
            $("#txt_passPasaporte").val("");
        }
    });
}
