

/* global alertify */

$(document).ready(function () { 
    $("#hid_tecladoIdentificacion").val(1);
    $("#confirmarAnulacion").val(1);
    $("#datosFactura").hide();
    $("#tecladoCredenciales").hide();
    $("#btnNuevoCliente").hide;
    $("#sri_leyenda").hide();
    fn_bloquearIngreso();    
    localStorage.setItem('nuevoCliente',0);
});

/* Administrador: Visualiza el teclado para solicitar el ingreso de un pasaporte */
function fn_modalCredenciales(){    
    $("#datosFactura").find("*").prop("disabled", true);
    $("#datosFactura").css({"opacity": "0.5"});

    $("#tecladoCredenciales").show();
    $("#tecladoCredenciales").dialog({   
        modal: true,
        position: "center",
        closeOnEscape: false,
        width: 500,
        height: 440,
        draggable: false,
        dialogClass: 'tecladoCredenciales', 
        open: function () {
            $(".ui-dialog-titlebar").hide();                
            $('#claveAdmin').attr('onchange', 'fn_validarUsuarioAdministrador()');                
            fn_tecladoCredenciales("#claveAdmin");
        }
    });    
}
function fn_modalCredencialesRuc(){    
    $("#datosFactura").find("*").prop("disabled", true);
    $("#datosFactura").css({"opacity": "0.5"});

    $("#tecladoCredenciales").show();
    $("#tecladoCredenciales").dialog({   
        modal: true,
        position: "center",
        closeOnEscape: false,
        width: 500,
        height: 440,
        draggable: false,
        dialogClass: 'tecladoCredenciales', 
        open: function () {
            $(".ui-dialog-titlebar").hide();                
            $('#claveAdmin').attr('onchange', 'fn_validarUsuarioAdministradorRuc()');                
            fn_tecladoCredenciales("#claveAdmin");
        }
    });    
}

/* Funcion que se ejecuta al precionar el boton cancelar el el dialog de credenciales de administrador */
function fn_cerrarDialogoAdmin() {
    $('#claveAdmin').val('');
    $("#tecladoAdmin").hide();
    $('#tecladoCredenciales').dialog('close');    
    $("#datosFactura").find("*").prop("disabled", false);    
    $("#datosFactura").css({"opacity": ""});
    if(localStorage.getItem("nuevoCliente")==0)
    fn_bloquearIngreso();
    $(".ui-dialog-titlebar").show();
    $("#sri_leyenda").show();
}

function teclado(elEvento) {
    evento = elEvento || window.event;
    k = evento.keyCode; //número de código de la tecla.
    //teclas númericas del teclado alfamunérico
    if (k > 47 && k < 58) {
        p = k - 48; //buscar número a mostrar.
        p = String(p); //convertir a cadena para poder añádir en pantalla.
        //fn_agregarNumero(p); //enviar para mostrar en pantalla
    }
//Teclas del teclado númerico. Seguimos el mismo procedimiento que en el anterior.
    if (k > 95 && k < 106) {
        p = k - 96;
        p = String(p);
        //fn_agregarNumero(p);
    }
    if (k == 110 || k == 190) {
        fn_agregarNumero(".");
    } //teclas de coma decimal
    if (k == 8) {
        fn_eliminarNumero();
    } //Retroceso en escritura : tecla retroceso.
    if (k > 57 && k < 210) {
        document.getElementById("parBusqueda").value = "";
    }
}

/* Funcion que valida la clave de adm para realizar el cabio de datos */
function fn_validarUsuarioAdministrador() {
    $("#sri_leyenda").hide();
    var usr_clave = $("#claveAdmin").val();
    var cfac_id = $('#listadoPedido').find("li.focus").attr("id");
    var usr_tarjeta;
    
    if (usr_clave.indexOf("%") >= 0) {
        var old_usr_clave = usr_clave.split("?;")[0];
        var new_usr_clave = old_usr_clave.replace(new RegExp("%", "g"), "");
        usr_tarjeta = new_usr_clave;
        usr_clave = "noclave";
    } else {
        usr_tarjeta = 0;
    }
    
    if (usr_clave !== "") {
        var rst_id = $("#hide_rst_id").val();
        send = {"validarUsuario": 1};
        send.rst_id = rst_id;
        send.usr_clave = usr_clave;
        send.usr_tarjeta = usr_tarjeta;
        $.getJSON("config_anularOrden.php", send, function (datos) {
            if (datos.str > 0) {
                $("#hid_usuarioAdmin").val(datos.usr_id);
                $("#hid_codigoFactura").val(cfac_id);
                $("#tecladoCredenciales").dialog("close");
                lc_userAdmin = datos.usr_id;
                $("#claveAdmin").val("");                  
                
                fn_cerrarDialogoAdmin();
                fn_tecladoAlfanumerico(txtClienteCI);                       
            } else {
                fn_tecladoCredenciales("#claveAdmin");                
                var cabeceramsj = 'Atenci&oacute;n!!';
                var mensaje = 'Clave incorrecta vuelva a intentarlo.';
                fn_mensajeAlerta(cabeceramsj, mensaje, 1, "#claveAdmin"); 
            }
        });
    } else {
        fn_tecladoCredenciales("#claveAdmin");
        var cabeceramsj = 'Atenci&oacute;n!!';
        var mensaje = 'Ingrese su clave de administrador(a).';
        fn_mensajeAlerta(cabeceramsj, mensaje, 1, "#claveAdmin");         
    }
}


function fn_validarUsuarioAdministradorRuc() {
    $("#sri_leyenda").hide();
    var usr_clave = $("#claveAdmin").val();
    var cfac_id = $('#listadoPedido').find("li.focus").attr("id");
    var usr_tarjeta;
    
    if (usr_clave.indexOf("%") >= 0) {
        var old_usr_clave = usr_clave.split("?;")[0];
        var new_usr_clave = old_usr_clave.replace(new RegExp("%", "g"), "");
        usr_tarjeta = new_usr_clave;
        usr_clave = "noclave";
    } else {
        usr_tarjeta = 0;
    }
    
    if (usr_clave !== "") {
        var rst_id = $("#hide_rst_id").val();
        send = {"validarUsuario": 1};
        send.rst_id = rst_id;
        send.usr_clave = usr_clave;
        send.usr_tarjeta = usr_tarjeta;
        $.getJSON("config_anularOrden.php", send, function (datos) {
            if (datos.str > 0) {
                $("#hid_usuarioAdmin").val(datos.usr_id);
                $("#hid_codigoFactura").val(cfac_id);
                $("#tecladoCredenciales").dialog("close");
                lc_userAdmin = datos.usr_id;
                $("#claveAdmin").val("");                  
                
                fn_cerrarDialogoAdmin();
                fn_tecladoAlfanumerico(txtClienteCI);    
                   
                fn_documentoRuc($("#txtClienteCI").val(), 0);                   
            } else {
                fn_tecladoCredenciales("#claveAdmin");                
                var cabeceramsj = 'Atenci&oacute;n!!';
                var mensaje = 'Clave incorrecta vuelva a intentarlo.';
                fn_mensajeAlerta(cabeceramsj, mensaje, 1, "#claveAdmin"); 
            }
        });
    } else {
        fn_tecladoCredenciales("#claveAdmin");
        var cabeceramsj = 'Atenci&oacute;n!!';
        var mensaje = 'Ingrese su clave de administrador(a).';
        fn_mensajeAlerta(cabeceramsj, mensaje, 1, "#claveAdmin");         
    }
}

/* Abre la modal cliente para el cambio de datos */
function fn_modalDatosCliente(codigoFactura, usuarioAdmin){

    var es_nota_credito = $("#hide_opcion_nota_credito").val();

    if (es_nota_credito == 0) {
        $('#anulacionesMotivo').hide();
        $('#anulacionesMotivo').dialog('close');
        $("#keyboard").hide();
    }

    informacion_sri();
    fn_bloquearIngreso();    
    $("#btnNuevoCliente").hide();
    $("#btnClienteConfirmarDatos").hide();
    $("#btnClienteCancelarAnulacion").show();
    $("#keyboardCliente").hide();
    $("#datosFactura").show();
    $("#datosFactura").dialog({
        title: "INFORMACIÓN FISCAL S.R.I. "+" "+codigoFactura,
        modal: true,
        position: "center",
        closeOnEscape: false,
        width: 1000,
        height: 800,
        draggable: false,
        open: function(){
            $(".ui-dialog-titlebar").show();
        }
    });
    
   // $("#txtClienteCI").removeAttr("disabled");
    $("#rdo_ruc").prop('disabled', false);
    $("#rdo_pasaporte").prop('disabled', false);
    
    $("#hid_codigoFactura").val(codigoFactura);
    $("#hid_usuarioAdmin").val(usuarioAdmin);
    fn_obtenerDatosCliente(codigoFactura);
    
    $("#dominio1").hide();
    $("#dominio2").hide();
    fn_cargaTeclasCorreoElectronico();
}

/* Obtiene los datos del cliente x su codigo de factura */
function fn_obtenerDatosCliente(codigoFactura){ 
    var send; 
    var Accion = "C"; 
    send = {"obtenerCambioDatosCliente": 1};
    send.accion = Accion; 
    send.codigoFactura = codigoFactura;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "config_cambioDatosCliente.php", data: send, success: function (datos){        
        if(datos.str > 0){ 
            fn_cargarDatos(datos[0]['IDCliente'], datos[0]['TipoDocumento'], datos[0]['Documento'], datos[0]['Cliente'], datos[0]['Direccion'], datos[0]['Telefono'], datos[0]['Email'], datos[0]['tipoCliente']);
        } 
    }});
}

/* Carga los datos del cliente en los campos de la modal cliente */
function fn_cargarDatos(IDCliente, tipoDocumento, identificacion, descripcion, direccionDomicilio, telefonoDomiclio, correo, tipoCliente){
    $("#txtClienteId").val(IDCliente);
    $("#txtClienteCI").val(identificacion);
    $("#txtClienteNombre").val(descripcion);                                        
  //  $("#txtClienteDireccion").val(direccionDomicilio);
    $("#txtClienteFono").val(telefonoDomiclio);
    $("#txtCorreo").val(correo);   
    
    if (tipoCliente != 0){
        $("#btnClienteConfirmarDatos").hide();
        $("#btnClienteAnularFactura").show();
    } else {
        $("#btnClienteAnularFactura").hide();
    }
    
    // Carga el teclado segun el tipo de documento
    if (tipoDocumento == 1){ 
        $("#hid_tecladoIdentificacion").val(1); 
        fn_tecladoIdentificacion(txtClienteCI);
        fn_habilitaBotonCedulaRuc();    
    } else if (tipoDocumento == 2) {
        $("#hid_tecladoIdentificacion").val(2); 
        fn_tecladoAlfanumerico(txtClienteCI);
        fn_habilitaBotonPasaporte();
        $("#sri_leyenda").hide();
    }
}

/* Boton cancelar - cierra la modal datos de cliente */
function fn_botonCancelar(){
    $("#datosFactura").hide();
    $("#datosFactura").dialog("close");
    $("#cambiarDatosCliente").prop("disabled", false);
    $("#anularOrden").prop("disabled", false);
    $("#numPadCliente").hide();
    $("#anulacionesContenedor").hide();
    $("#anulacionesContenedor").dialog("close");    
    fn_habilitaBotonCedulaRuc();
    $("#confirmarAnulacion").val(1);     
    $('#listadoPedido li').removeClass('focus');    
}

/* Boton cancelar - cierra la modal datos de cliente */
function fn_CerrarModalClientes() {
    $("#datosFactura").hide();
    $("#datosFactura").dialog("close");
    $("#cambiarDatosCliente").prop("disabled", false);
    $("#anularOrden").prop("disabled", false);
    $("#numPadCliente").hide();
    $("#anulacionesContenedor").hide();
    $("#anulacionesContenedor").dialog("close");    
    //fn_habilitaBotonCedulaRuc(); 
    //$("#confirmarAnulacion").val(1);     
    //$('#listadoPedido li').removeClass('focus');    
}

/* Valida documento de RUC */
function fn_validarRucPolitica() {
    if($("#txtClienteCI").val().length == 13){
        return ($("#txtClienteCI").val().substring(10)==$("#ValidacionRucCodigo").val());       
    }
    return 0;
}

/* Botones C.I/RUC - PASAPORTE */
function fn_validaTecladoIdentificacion(id){
    $("#dominio1").hide();
    $("#dominio2").hide();
    $("#btnClienteConfirmarDatos").hide();
    var tecladoIdentificacion = $("#hid_tecladoIdentificacion").val();      

    if(tecladoIdentificacion == 1){ 
        fn_tecladoIdentificacion(id);
        fn_ocultarTecladoAlfanumerico("#keyboardCliente");        
    } else if(tecladoIdentificacion == 2){
        fn_tecladoAlfanumerico(id);
        fn_ocultarTecladoAlfanumerico("#numPadCliente");
        $("#sri_leyenda").hide();
    }   
}

function fn_habilitaBotonCedulaRuc(){
    $("#rdo_ruc").removeClass("btnRucCiInactivo");
    $("#rdo_ruc").addClass("btnRucCiActivo");
    $("#rdo_pasaporte").removeClass("btnRucCiActivo");
    $("#rdo_pasaporte").addClass("btnRucCiInactivo");   
}

function fn_habilitaBotonPasaporte(){
    $("#rdo_ruc").removeClass('btnRucCiActivo');
    $("#rdo_ruc").addClass('btnRucCiInactivo');
    $("#rdo_pasaporte").removeClass('btnRucCiInactivo');
    $("#rdo_pasaporte").addClass('btnRucCiActivo');
}

function fn_validaTecladoCedulaRuc(){
    $("#hid_tecladoIdentificacion").val(1);
    fn_habilitaBotonCedulaRuc();
    $("#txtClienteCI").focus();
    fn_tecladoIdentificacion("#txtClienteCI"); 
    
    $("#anulacionesContenedor").hide();
    $("#anulacionesContenedor").dialog("close");
    fn_ocultar_alfanumerico();
    fn_limpiarCamposCliente();
    $("#txtClienteCI").val("");
    $("#sri_leyenda").show();
}

function fn_validaTecladoPasaporte(){
    var confirmarAnulacion =  $("#confirmarAnulacion").val();    
    $("#hid_tecladoIdentificacion").val(2);
    fn_habilitaBotonPasaporte(); 
    
    if (confirmarAnulacion == 1){
        fn_modalCredenciales();
        fn_ocultar_alfanumerico();
    } else if (confirmarAnulacion == 2){
        fn_tecladoAlfanumerico(txtClienteCI);
    }
    
    fn_ocultarTecladoAlfanumerico(numPadCliente);
    fn_limpiarCamposCliente();
    $("#txtClienteCI").val("");
    $("#sri_leyenda").hide();
    $("#btnClienteConfirmarDatos").hide();   
}

/* Valida y busca documento de cliente */
function fn_clienteCambioDatosBuscar(valid) {  
    console.log('valid')
    console.log(valid)  
    if($("#txtClienteCI").val() == "9999999999" || $("#txtClienteCI").val() == "9999999999999"){
       alertify.alert("No se puede realizar notas de crédito o cambio de datos del cliente como consumidor final.");
       return false;
    }
    
    if($("#txtClienteCI").val().length == 13){
        if($("#HabilitarValidacionRUC").val()=='1' && fn_validarRucPolitica()==0){
            alertify.alert($("#ValidacionErrorRUC").val()+" "+$("#ValidacionRucCodigo").val());
            $("#txtClienteCI").focus();
            return false;
        }
    }
    var documento = $("#txtClienteCI").val(); 
    fn_bloquearIngreso();
 if($("#rdo_ruc").hasClass("btnRucCiActivo")){
        fn_documentoCedulaRuc(documento, valid)        //fn_documentoCedulaRuc(documento, valid);
    } else { 
        fn_documentoPasaporte(documento, valid);
    }
}
function fn_documentoRuc(documento, valid){
    fn_limpiarCamposCliente();
    
    var send;
    var tipoDocumento;
    
    tipoDocumento = "RUC";
    
        send = {};
        send.metodo = "buscarCliente";
        send.documento = documento; 
        send.tipoDocumento = tipoDocumento;
        $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", 
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", 
        url: "../facturacion/clienteWSClientes.php", data: send, success: function (datos) {
            if(datos.hasOwnProperty("cliente")) {                    
                var cliente = datos.cliente; 
                $("#clienteEstado").val(datos.estado);

                if (datos.estado === 1){ 
                    localStorage.setItem('nuevoCliente',0);

                    // El cliente no existe en el local pero si existe en la base centralizada                    
                    fn_cargando(0);
                    $("#estadoWS").val(1);
                    fn_cargaDatosCliente(1, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, 0, datos.estado, valid);
                    $("#btnNuevoCliente").hide();

                    if(cliente.descripcion == '' || cliente.correo == '' || cliente.correo == null){
                       
                        fn_obtenerDatosClienteWS();
                        fn_habilitarIngreso();
                        $("#btnClienteConfirmarDatos").hide();
                        $("#btnClienteConfirmarDatosFacturar").show();
                        $("#confirmarAnulacion").val(2);  
                        $("#rdo_ruc").prop('disabled', true);
                        $("#rdo_pasaporte").prop('disabled', true);
                        fn_focoinput_nombres();
                        $("#sri_leyenda").hide();

                    }

                    localStorage.setItem('cedulaCliente',  cliente.identificacion);
          
                } else if(datos.estado === 2) { // El cliente no existe la base centralizada ni en el local                      
                    localStorage.setItem('nuevoCliente',1);
                    fn_cargando(0);
                    var cliente = datos.cliente;                    
                    $("#clienteAutorizacion").val(cliente.autorizacion);
                    $("#estadoWS").val(1);
                    $("#btnNuevoCliente").show();
                    fn_nuevoCliente(documento);
                    $("#btnClienteAnularFactura").hide();
                    $("#btnClienteConfirmarDatos").hide();
                    localStorage.setItem('cedulaCliente',  documento);
            
                    
                } else if(datos.estado === 3) { // El cliente existe en el local
        
                    localStorage.setItem('nuevoCliente',0 );
                    fn_cargaDatosCliente(1, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, 0, datos.estado, valid);
                    $("#btnNuevoCliente").hide();

                    if(cliente.descripcion == '' || cliente.correo == '' || cliente.correo == null){
                       
                        fn_obtenerDatosClienteWS();
                        fn_habilitarIngreso();
                        $("#btnClienteConfirmarDatos").hide();
                        $("#btnClienteConfirmarDatosFacturar").show();
                        $("#confirmarAnulacion").val(2);  
                        $("#rdo_ruc").prop('disabled', true);
                        $("#rdo_pasaporte").prop('disabled', true);
                        fn_focoinput_nombres();
                        $("#sri_leyenda").hide();

                    }

                    localStorage.setItem('cedulaCliente',  cliente.identificacion);
               

                } 
            } else {
                // Cuando el WS no esta en linea o error en la consulta
                fn_cargando(0);
                localStorage.setItem('nuevoCliente',1);

                fn_nuevoCliente(documento);
                $("#estadoWS").val(0);
               

            }
        }, error: function () { // Cuando el WS no esta en linea 
                fn_cargando(0);
                localStorage.setItem('nuevoCliente',1);
                fn_nuevoCliente(documento);
                $("#estadoWS").val(0);

            }
        });
    
}


/* Valida documento de RUC */
function fn_validarRucPolitica() {
    if($("#txtClienteCI").val().length == 13){
        return ($("#txtClienteCI").val().substring(10)==$("#ValidacionRucCodigo").val());       
    }
    return 0;
}


function fn_conteoValidacionRUC(){
    let ValidacionRUCintento=parseInt($("#ValidacionRUCintento").val(),10);
    let ValidacionRUCNIntentos=parseInt($("#ValidacionRUCNIntentos").val(),10);
    let estado=false;
    if(ValidacionRUCNIntentos>ValidacionRUCintento){
        estado=true;
    }else{
        estado=false
    }
    return estado;
}

function fn_conteoDirectoRUCVariable(cadena,tercera){
for (i = 0; i < cadena.length; i++) {
    if(cadena[i]==tercera){
        return true;
    }
  }
  return false; 
}

function fn_conteoDirectoRUC(){
    let ValidacionRUCNIntentos=parseInt($("#ValidacionRUCNIntentos").val(),10);
    let ValidacionRUCdirectoN=parseInt($("#ValidacionRUCdirectoN").val(),10);
    let terceraletra=$("#txtClienteCI").val().substring(2,3);
    let estado=false;
    let cadena=$("#ValidacionRUCdirecto").val().split(";");
    if(ValidacionRUCNIntentos>=ValidacionRUCdirectoN && fn_conteoDirectoRUCVariable(cadena,terceraletra)){
        estado=true;
    }else{
        estado=false
    }
    return estado;
}

function fn_sumarconteo(){
    $("#ValidacionRUCNIntentos").val(parseInt($("#ValidacionRUCNIntentos").val(),10)+1)
}

/* Consumo Web Service por documento CI/RUC :  */
function fn_documentoCedulaRuc(documento, valid){
    fn_limpiarCamposCliente();
    var send;
    var tipoDocumento;
    fn_sumarconteo();
    if(fn_validarDocumento($("#txtClienteCI").val())) {
        fn_cargando(1);
        if($("#txtClienteCI").val().length == 10) {
            tipoDocumento = "CEDULA";
        } else if($("#txtClienteCI").val().length == 13){
            tipoDocumento = "RUC";
        } else{
            fn_documentoInvalido();
            return true;
        }

        send = {};
        send.metodo = "buscarCliente";
        send.documento = documento; 
        send.tipoDocumento = tipoDocumento;
        send.revocado = 1;
        $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", 
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", 
        url: "../facturacion/clienteWSClientes.php", data: send, success: function (datos) {
            if(datos.hasOwnProperty("cliente")) {                    
                var cliente = datos.cliente; 
                $("#clienteEstado").val(datos.estado);

                if (datos.estado === 1){ 
                    
                    // El cliente no existe en el local pero si existe en la base centralizada                    
                    fn_cargando(0);
                    $("#estadoWS").val(1);
                    fn_cargaDatosCliente(1, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, 0, datos.estado, valid);
                     
                     if(cliente.descripcion == '' || cliente.correo == '' || cliente.correo == null){
                       
                        fn_obtenerDatosClienteWS();
                        fn_habilitarIngreso();
                        $("#btnClienteConfirmarDatos").hide();
                        $("#btnClienteConfirmarDatosFacturar").show();
                        $("#confirmarAnulacion").val(2);  
                        $("#rdo_ruc").prop('disabled', true);
                        $("#rdo_pasaporte").prop('disabled', true);
                        fn_focoinput_nombres();
                        $("#sri_leyenda").hide();

                    }





                    localStorage.setItem('cedulaCliente',  cliente.identificacion);

                } else if(datos.estado === 2) { // El cliente no existe la base centralizada ni en el local                      
                    fn_cargando(0);
                    var cliente = datos.cliente;                    
                    $("#clienteAutorizacion").val(cliente.autorizacion);
                    $("#estadoWS").val(1);
                    fn_nuevoCliente(documento);
                  
                    localStorage.setItem('cedulaCliente',  documento);

                } else if(datos.estado === 3) { // El cliente existe en el local                       
                    fn_cargando(0);
                    fn_cargaDatosCliente(1, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, 0, datos.estado, valid);
                    $("#estadoWS").val(1);

                     if(cliente.descripcion == '' || cliente.correo == '' || cliente.correo == null){
                       
                        fn_obtenerDatosClienteWS();
                        fn_habilitarIngreso();
                        $("#btnClienteConfirmarDatos").hide();
                        $("#btnClienteConfirmarDatosFacturar").show();
                        $("#confirmarAnulacion").val(2);  
                        $("#rdo_ruc").prop('disabled', true);
                        $("#rdo_pasaporte").prop('disabled', true);
                        fn_focoinput_nombres();
                        $("#sri_leyenda").hide();

                    }

                    localStorage.setItem('cedulaCliente',  cliente.identificacion);

                } 
            } else {
                // Cuando el WS no esta en linea o error en la consulta
                fn_cargando(0);
                fn_nuevoCliente(documento);
                $("#estadoWS").val(0);
            }
        }, error: function () { // Cuando el WS no esta en linea 
                fn_cargando(0);
                fn_nuevoCliente(documento);
                $("#estadoWS").val(0);
            }
        });
    } else {
        fn_cargando(0); 
            /*
            alertify.alert("N\u00famero de documento no v\u00e1lido, por favor ingrese credenciales de administrador.");
            fn_modalCredencialesRuc();
            */
            let HabilitarValidacionRUC=$("#HabilitarValidacionRUC").val();
            let validarDocumentoCliente=fn_validarDocumento($("#txtClienteCI").val());
            let ValidarPoliticaRuc=fn_validarRucPolitica();

                       if($("#txtClienteCI").val().length == 13){
                           if($("#HabilitarValidacionRUC").val()=='1' && ValidarPoliticaRuc==0){
                               alertify.alert($("#ValidacionErrorRUC").val()+" "+$("#ValidacionRucCodigo").val());   
                               $("#txtClienteCI").focus();
                               return false;
                           }
                           else if(HabilitarValidacionRUC=='1' && validarDocumentoCliente==0  && ValidarPoliticaRuc==1)
                           {
                               if(fn_conteoDirectoRUC()==false){
                                   if(fn_conteoValidacionRUC()==true){ 
                                       alertify.alert("N\u00famero de documento no v\u00e1lido, por favor ingrese credenciales de administrador.");
                                       /*localStorage.setItem("invalido",1)
                                       fn_solicitaCredencialesAdministrador();
                                       $('#btnClienteConfirmarDatos').hide();
                                       $('#btnConsumidorFinal').show();
                                       */
                                       fn_modalCredencialesRuc();
                                   }else{
                                       fn_documentoInvalido();
                                       return false;
                                   }
                               }else{
                                   fn_cargando(0);
                                   $("#clienteAutorizacion").val('');
                                   $("#estadoWS").val(1);
                                   fn_nuevoCliente(documento);
                               }
                            }else if(HabilitarValidacionRUC=='0' && validarDocumentoCliente==0){
                                alertify.alert("N\u00famero de documento no v\u00e1lido, por favor ingrese credenciales de administrador.");
                                fn_modalCredencialesRuc();
                            }
                else
                fn_documentoInvalido();        
            } else{
                fn_documentoInvalido();
                return true;
            }
    }
}
/* Consumo Web Service PASAPORTE */
function fn_documentoPasaporte(documento, valid){
    fn_cargando(1);
    fn_limpiarCamposCliente();
    var send; 
    var tipoDocumento = "PASAPORTE";
    var strDocumento = documento.substring(0, 15);
    var trimDocumento = strDocumento.trim();    
    send = {};
    send.metodo = "buscarCliente";
    send.documento = trimDocumento; 
    send.tipoDocumento = tipoDocumento;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", 
    contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", 
    url: "../facturacion/clienteWSClientes.php", data: send, success: function (datos) {
        if(datos.hasOwnProperty("cliente")) {                    
            var cliente = datos.cliente;  
            $("#clienteEstado").val(datos.estado);
            if (datos.estado === 1){ // El cliente no existe en el local pero si existe en la base centralizada                   
                fn_cargando(0);
                $("#estadoWS").val(1);
                fn_cargaDatosCliente(2, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, 0, datos.estado, valid);                           
                

                 if(cliente.descripcion == '' || cliente.correo == '' || cliente.correo == null){
                       
                    fn_obtenerDatosClienteWS();
                    fn_habilitarIngreso();
                    $("#btnClienteConfirmarDatos").hide();
                    $("#btnClienteConfirmarDatosFacturar").show();
                    $("#confirmarAnulacion").val(2);  
                    $("#rdo_ruc").prop('disabled', true);
                    $("#rdo_pasaporte").prop('disabled', true);
                    fn_focoinput_nombres();
                    $("#sri_leyenda").hide();

                }

           
            } else if(datos.estado === 2) { //El cliente no existe la base centralizada ni en el local
                fn_cargando(0);
                var cliente = datos.cliente;                    
                $("#clienteAutorizacion").val(cliente.autorizacion);
                $("#estadoWS").val(1);
                fn_nuevoCliente(documento);
            } else if(datos.estado === 3) { // El cliente existe en el local                       
                fn_cargando(0);
                $("#estadoWS").val(1);
                fn_cargaDatosCliente(2, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, 0, datos.estado, valid);                
                   

                 if(cliente.descripcion == '' || cliente.correo == '' || cliente.correo == null){
                       
                    fn_obtenerDatosClienteWS();
                    fn_habilitarIngreso();
                    $("#btnClienteConfirmarDatos").hide();
                    $("#btnClienteConfirmarDatosFacturar").show();
                    $("#confirmarAnulacion").val(2);  
                    $("#rdo_ruc").prop('disabled', true);
                    $("#rdo_pasaporte").prop('disabled', true);
                    fn_focoinput_nombres();
                    $("#sri_leyenda").hide();

                }

            } 
        } else {
            fn_cargando(0);
            fn_nuevoCliente(documento);
            $("#estadoWS").val(0);
        }
    }, error: function () { // Cuando el WS no esta en linea 
            fn_cargando(0);
            fn_nuevoCliente(documento);
            $("#estadoWS").val(0);
        }
    });
}

/* Carga los datos de cliente en la modal cliente obtenidos de base central por WS */
function fn_cargaDatosCliente(tipoDocumento, IDCliente, identificacion, descripcion, direccionDomicilio, telefonoDomiclio, correo, tipoCliente, opcion, estado, valid){
    $("#txtClienteId").val(IDCliente);
    $("#txtClienteCI").val(identificacion);

    $("#txtClienteNombre").val(descripcion || $("#txtClienteNombre").val());
    $("#txtClienteFono").val((localStorage.getItem("ls_telefono") || telefonoDomiclio) || $("#txtClienteFono").val());
    $("#txtCorreo").val(correo || $("#txtCorreo").val());

    if(valid == 1){
        console.log('a valifa email00')
        fn_validaEmailRegisteredAPI(correo);
    }
     
    // opcion 0 es consulta datos clientes, opcion 1 es actualizacion datos cliente
    fn_validaBotones(opcion, tipoCliente, estado);

    // 1 = Cédula, RUC o Consumidor Final, 2 = Pasaporte
    if (tipoDocumento === 1){        
        fn_ocultarTecladoAlfanumerico(numPadCliente);
        if (document.getElementById("txtClienteNombre").disabled != true){
       //     if (document.getElementById("txtClienteDireccion").disabled != true){
                if (document.getElementById("txtClienteFono").disabled != true){
                    if (document.getElementById("txtCorreo").disabled != true){
                        fn_focoinput_nombres();
                    }
                }
       //     }
        }
        
    } else {
        if (document.getElementById("txtClienteNombre").disabled != true){
       //     if (document.getElementById("txtClienteDireccion").disabled != true){
                if (document.getElementById("txtClienteFono").disabled != true){
                    if (document.getElementById("txtCorreo").disabled != true){
                        fn_focoinput_nombres();
                    }
                }
       //     }
        }       
    }    
    
    $("#txtClienteNombre").focus();                     
    $("#nombres_obligatorios").show();
    $("#direccion_obligatorios").show();        
}

function fn_validaEmail(valid) {

    console.log("aqui viernes api nueva funcion cambio datos");
    /*var arr_e = [];
    var datos_existentes = localStorage.getItem("email_valid");
    datos_existentes =
      datos_existentes == null ? [] : JSON.parse(datos_existentes);
    arr_e = localStorage.getItem("email_valid");*/
    console.log('aqui viernes api nueva funcion')
    var arr_e = [];
    var email = $("#txtCorreo").val();
    var datos_existentes = localStorage.getItem('email_valid');
    datos_existentes = datos_existentes == null ? [] : JSON.parse(datos_existentes);
    arr_e = localStorage.getItem('email_valid');
    expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if ( !expr.test(email) ){
        console.log('el email es incorrecto')
        return;
    }
  
    if(localStorage.getItem('intValidaCC') === undefined || localStorage.getItem('intValidaCC') === null){
        console.log('no existe la config, se procede a cargarla')
        localStorage.setItem('intValida', valid);
        localStorage.setItem('intValidaFC', valid);
        localStorage.setItem('intValidaOP', valid);
        localStorage.setItem('intValidaCC', valid);
    }
  
    send = {"cargarUrlApiValidaPlugthem": 1, "CustomerEmail": email};

    if(valid != 0 && localStorage.getItem("intValidaCC") > 0 && expr.test(email)){
        $.ajax({async: false, type: "POST", dataType: 'json', url: "../ordenpedido/config_ordenPedido.php", data: send,
            success: function( datos ) {
              console.log('RESPONSE VALIDACIÓN EMAIL REGISTERED API CC');
              console.log('RESPONSE VALIDACIÓN EMAIL API CONFIRMAR DATOS');
              var objectStringArray = (new Function("return [" + datos+ "];")());
              console.log(objectStringArray[0]['status'])
              var existsE = arr_e != null ? arr_e.includes(email) : false;
              if (objectStringArray[0]['status'] == 'invalid'){
                  if(!existsE){
                      datos_existentes.push(email);
                      console.log('no se ha validado el correo ingresado, reinicio contador')
                      //localStorage.removeItem('email_valid');
                      localStorage.setItem("intValidaCC", localStorage.getItem("intValida") - 1);
                      alertify.alert("El email ingresado no es v\u00e1lido. Por favor vuelva a verificar." + "Le restan " + localStorage.getItem("intValidaCC").toString() + " " + "intentos. Al culminarlos el correo será tomado como válido.");
                  } else {
                      console.log('resto del actual')
                      localStorage.setItem("intValidaCC", localStorage.getItem("intValidaCC") - 1);
                      if(localStorage.getItem("intValidaCC") > 0) {
                          alertify.alert("El email ingresado no es v\u00e1lido. Por favor vuelva a verificar." + "Le restan " + localStorage.getItem("intValidaCC").toString() + " " + "intentos. Al culminarlos el correo será tomado como válido.");
                      } else if (localStorage.getItem("intValidaCC") == 0)  {
                          alertify.alert("El email ingresado no es v\u00e1lido. Ya agoto sus intentos de verificación, el correo será tomado como válido.");    
                      }
                  }
                  localStorage.setItem('email_valid', JSON.stringify(datos_existentes));
              } 
            
      
            },
            error: function (jqXHR, textStatus, errorThrown) {
              console.log('error validando email CC')

            }
          });

    } 
     
   }


   




function fn_validaBotones(opcion, tipoCliente, estado){    
    if (opcion === 0){
        
        var clienteTipo;
    
        // clienteTipo = 1 son RELACIONADOS O EXTERNOS; clienteTipo = 0 son NORMALES 
        if (estado == 1) {
            // Consulta en master data clientes
            if (tipoCliente == "NORMAL" || tipoCliente == "" || tipoCliente == "NULL" || tipoCliente == null) {
                clienteTipo = 0;
            } else {
                clienteTipo = 1;
            }      
        } else {
            // consulta en su base local
            if (tipoCliente == 1 || tipoCliente == 2) {
                clienteTipo = 1;
            } else {
                clienteTipo = 0; 
            }    
        }
         
          
        if (localStorage.getItem("nuevoCliente") == 1){        
            $("#btnClienteConfirmarDatos").hide();
            $("#btnClienteAnularFactura").hide();
        }else
        if (clienteTipo == 1){        
            $("#btnClienteConfirmarDatos").hide();
            $("#btnClienteAnularFactura").show();
        } else {      
            $("#btnClienteConfirmarDatos").show();
            $("#btnClienteAnularFactura").hide();
        }        
    } else {
        $("#btnClienteAnularFactura").show();
    }     
}

/* Oculta los teclados cuando el documento es incorrecto */
function fn_documentoInvalido() {
    var cabeceramsj = 'Atenci&oacute;n!!';
    var mensaje = 'N\u00famero de documento no v\u00e1lido.';
    fn_mensajeAlerta(cabeceramsj, mensaje, 0, "#claveAdmin");                       
    fn_limpiarCamposCliente();
    fn_bloquearIngreso();
    $("#txtClienteCI").focus();
    fn_tecladoIdentificacion(txtClienteCI);    
    $("#keyboardCliente").hide();
    $("#nombres_obligatorios").hide();
    $("#direccion_obligatorios").hide();
    $("#btnClienteConfirmarDatos").hide();
    $("#btnClienteAnularFactura").hide();
    $("#btnClienteCancelarAnulacion").show();
}

/* Bloquea los campos */
function fn_bloquearIngreso(){    
    $("#txtClienteNombre").attr("disabled", "-1");
    $("#txtClienteApellido").attr("disabled", "-1");
  //  $("#txtClienteDireccion").attr("disabled", "-1");
    $("#txtClienteFono").attr("disabled", "-1");    
    $("#txtCorreo").attr("disabled", "-1"); 
}

/* Limpia los campos */
function fn_limpiarCamposCliente(){
    $("#txtClienteNombre").val("");
 //   $("#txtClienteDireccion").val("");                
    $("#txtClienteFono").val("");
    $("#txtCorreo").val("");
}

function fn_focoinput_nombres(){ 
    $("#dominio1").hide();
    $("#dominio2").hide();    
    fn_alfanumericoCliente(txtClienteNombre);
    $("#txtClienteNombre").focus();
    $("#sri_leyenda").hide();    
}

function fn_focoinput_direccion(){
    $("#dominio1").hide();
    $("#dominio2").hide();
//    $("#txtClienteDireccion").focus();
//    fn_tecladoAlfanumerico(txtClienteDireccion);
    $("#sri_leyenda").hide();   
}



function fn_focoinput_telefono(){
    $("#dominio1").hide();
    $("#dominio2").hide();
    $("#txtClienteFono").focus();
    fn_alfanumericoTelefono(txtClienteFono);
    $("#sri_leyenda").hide();   
}

function fn_focoinput_email(){      
    $("#dominio1").show();
    $("#dominio2").show();
    $("#txtCorreo").focus();
    fn_tecladoAlfanumerico(txtCorreo);
    $("#sri_leyenda").hide();   
}

function fn_habilitarIngreso(){ 
    $("#txtClienteNombre").removeAttr("disabled");  
//    $("#txtClienteDireccion").removeAttr("disabled");
    $("#txtClienteFono").removeAttr("disabled");
    $("#txtCorreo").removeAttr("disabled");
}

/* Funcion cargando */
function fn_cargando(estado) {
    if (estado) {
        $("#mdl_rdn_pdd_crgnd").css("display", "block");
        $("#mdl_pcn_rdn_pdd_crgnd").css("display", "block");
    } else {
        $("#mdl_rdn_pdd_crgnd").css("display", "none");
        $("#mdl_pcn_rdn_pdd_crgnd").css("display", "none");
    }
}

/* Muesta modal para confirmar los datos ingresados */
function fn_confirmarCambioDatos(){   
    
    var validaIdentificacion = $("#hid_tecladoIdentificacion").val();
    
    // 1 = Cedula RUC : 2 = Pasaporte
    if (validaIdentificacion == 1){
        if($("#txtClienteCI").val().length == 13) {        
            fn_dialogConfirmarDatosCliente();       
        }
        else if(fn_validarDocumento($("#txtClienteCI").val())) {        
            fn_dialogConfirmarDatosCliente();       
        } else {
            fn_documentoInvalido();  
        }
    } else if (validaIdentificacion == 2) {
        fn_dialogConfirmarDatosCliente();
    }
}

function fn_validaConfirmarCambioDatos(){
    if($("#txtClienteCI").val() == ''){        
        alertify.error("Documento de identificaci&oacute;n obligatorio.");
        $("#txtClienteCI").focus();
        return false;       
    } else {
        fn_confirmarCambioDatos();
    } 
}

/* Se confirma si los datos del cliete son correctos
 * de ser "SI", se realiza el cierre de la facturación.
 * de ser "NO", se obtiene del Azure los datos del cliente por WS. */
function fn_dialogConfirmarDatosCliente(){

    var titulo = '<h4>INFORMACI&Oacute;N FISCAL S.R.I.</h4> <br><br> ¿LOS DATOS DE CLIENTE SON CORRECTOS? <br><br>';

    alertify.set({
        labels: {
            ok: "SI",
            cancel: "NO"
        }
    });
    
    alertify.confirm(titulo, function (e) {
        if (e) {
            var es_nota_credito = $("#hide_opcion_nota_credito").val();

        if($("#txtClienteCI").val() == ''){        
            alertify.alert("Documento de identificaci&oacute;n obligatorio. <br>");
            $("#txtClienteCI").focus();
            return false;       
        } else if($("#txtClienteNombre").val().trim() == ''){
            $("#keyboardCliente").hide();
            alertify.alert("Ingrese los nombres del Cliente. <br>");
            $("#txtClienteNombre").focus();
            return false; 
        } else if (fn_validarCorreoElectronico($("#txtCorreo").val())) {
            if (opcion == "U"){            
                fn_confirmarDatosAnular('U', 0); 
            } else if (opcion == "I"){
                fn_confirmarDatosAnular('U', 0);            
            }        
        } else {
            alertify.alert("Direcci\u00f3n de correo electr&oacute;nico no v\u00e1lida.. <br>");
        }

            if (es_nota_credito == 1) {
                fn_duplicarFacturaActual();
            } else {
                $("#hide_cliente").val($("#txtClienteCI").val());
                fn_CerrarModalClientes();
                fn_formasPago();
            }
        } else {
            fn_obtenerDatosClienteWS();
            fn_habilitarIngreso();
            $("#btnClienteConfirmarDatos").hide();
            $("#btnClienteConfirmarDatosFacturar").show();
            $("#confirmarAnulacion").val(2);  
            
            $("#rdo_ruc").prop('disabled', true);
            $("#rdo_pasaporte").prop('disabled', true);
       //     $("#txtClienteCI").attr("disabled", "-1");
            
            fn_focoinput_nombres();
            $("#sri_leyenda").hide();
        }
    });        
}

/* Obtenemos los datos del cliente por WS */
function fn_obtenerDatosClienteWS(){
    $("#btnClienteAnularFactura").show();
    fn_cargando(1);
    var send;
    var documento = $("#txtClienteCI").val();
    var validaTipoDocumento = $("#hid_tecladoIdentificacion").val();
    var tipoDocumento;
    var strDocumento = documento.substring(0, 15);
    var trimDocumento = strDocumento.trim(); 
    
    if (validaTipoDocumento == 1) {
        if($("#txtClienteCI").val().length == 10) {
            tipoDocumento = "CEDULA";
        } else if($("#txtClienteCI").val().length == 13){
            tipoDocumento = "RUC";
        }
    } else if (validaTipoDocumento == 2) {
        tipoDocumento = "PASAPORTE";        
    }
        
    send = {};
    send.metodo = "obtenerDatosCliente";
    send.documento = trimDocumento; 
    send.tipoDocumento = tipoDocumento;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", 
    contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", 
    url: "../facturacion/clienteWSClientes.php", data: send, success: function (datos) {
        if(datos.hasOwnProperty("cliente")) {                    
            var cliente = datos.cliente;             
            if (datos.estado === 1){ // Existe                      
                if (cliente.tipoIdentificacion === "CEDULA") {
                    fn_cargando(0);                 
                    fn_cargaDatosCliente(1, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, 1, datos.estado); 
                } else if (cliente.tipoIdentificacion === "RUC") {                    
                    fn_cargando(0);                 
                    fn_cargaDatosCliente(1, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, 1, datos.estado); 
                } else {
                    fn_cargando(0);                 
                    fn_cargaDatosCliente(2, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, 1, datos.estado);
                }            
            } else {
              fn_cargando(0);   
            }
        } else {
            // Cuando el WS no esta en linea o error en la consulta
            fn_cargando(0);
        }
    }, error: function () { // Cuando el WS no esta en linea 
            fn_cargando(0);
        }
    });
}
/* SI LOS DATOS DEL CLIENTE SON CORRECTOS */
/* INSERT: Duplica la factura seleccionada con un nuevo codigo de factura. */
function fn_duplicarFacturaActual(){
    fn_cargando(1);

    $("#countdown").attr("style", "display:block");
    $("#modalBloquearCargaCronometro").show();
    cargando(1);

    var send; 
    var Accion = "D";
    var codigoFactura = $("#hid_codigoFactura").val();
    var usuarioAdmin =  $("#hid_usuarioAdmin").val(); 
    var documentoCliente = $("#txtClienteCI").val();
    send = {"duplicarFacturaActual": 1};
    send.accion = Accion; 
    send.codigoFactura = codigoFactura;
    send.usuarioAdmin = usuarioAdmin;
    send.documentoCliente = documentoCliente;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "config_cambioDatosCliente.php", data: send, success: function (datos){        
        if(datos.str > 0){ 
            $("#hid_CodigoNuevaCredito").val(datos['NuevaFactura']);            
            fn_anulacionFormasPago(codigoFactura, usuarioAdmin, documentoCliente);
        } 
    }});
}

/* UPDATE: Anula las formas de pago de la factura seleccionada. */
function fn_anulacionFormasPago(codigoFactura, usuarioAdmin, documentoCliente){
    var send; 
    var Accion = "V";    
    send = {"anulacionFormasPago": 1};
    send.accion = Accion; 
    send.codigoFactura = codigoFactura;
    send.usuarioAdmin = usuarioAdmin;
    send.documentoCliente = documentoCliente;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "config_cambioDatosCliente.php", data: send, success: function (datos){        
        if(datos.Respuesta > 0){ 
            if ( $("#cod_fac_periodo_anterior_" + codigoFactura).val() === undefined)
                fn_motivoAnulacionCDC(codigoFactura, usuarioAdmin, documentoCliente);
            else 
                fn_motivoCDC(codigoFactura, usuarioAdmin, documentoCliente);
        } 
    }});
}

/* UPDATE: Actualiza el motivo de anulacion sobre la factura seleccionada. */
function fn_motivoAnulacionCDC(codigoFactura, usuarioAdmin, documentoCliente){
    var send; 
    var Accion = "M";    
    send = {"anulacionFormasPago": 1};
    send.accion = Accion; 
    send.codigoFactura = codigoFactura;
    send.usuarioAdmin = usuarioAdmin;
    send.documentoCliente = documentoCliente;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "config_cambioDatosCliente.php", data: send, success: function (datos){        
        if(datos.Respuesta > 0){ 
            fn_crearNotaDeCredito(codigoFactura, usuarioAdmin, documentoCliente);
        } 
    }});
}

/* UPDATE: Actualiza el motivo de cambio sobre la factura seleccionada. */
function fn_motivoCDC(codigoFactura, usuarioAdmin, documentoCliente){
    var send; 
    var Accion = "O";    
    send = {"anulacionFormasPago": 1};
    send.accion = Accion; 
    send.codigoFactura = codigoFactura;
    send.usuarioAdmin = usuarioAdmin;
    send.documentoCliente = documentoCliente;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "config_cambioDatosCliente.php", data: send, success: function (datos){        
        if(datos.Respuesta > 0){ 
            fn_crearNotaDeCredito(codigoFactura, usuarioAdmin, documentoCliente);
        } 
    }});
}

/*INSERT: Crea la nota de credito de la factura seleccionada. */
function fn_crearNotaDeCredito(codigoFactura, usuarioAdmin, documentoCliente){

    var send; 
    var Accion = "N";    
    var ipEstacionCambioDatos = $("#hid_IpEstacionCambioDatos").val();

    send = {"crearNotaDeCredito": 1};
    send.accion = Accion; 
    send.codigoFactura = codigoFactura;
    send.usuarioAdmin = usuarioAdmin;
    send.documentoCliente = documentoCliente;
    
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "config_cambioDatosCliente.php", data: send, success: function (datos){       
        if(datos.str > 0){ 
            fn_generarClaveAcceso(codigoFactura, usuarioAdmin, documentoCliente, datos.idEstacion, datos.aplicaEnEstacion, datos.servidorUrlApi, ipEstacionCambioDatos);
            fn_actializarFacturaDuplicada(usuarioAdmin, documentoCliente, datos.idEstacion, datos.aplicaEnEstacion, datos.servidorUrlApi, ipEstacionCambioDatos);            
            fn_cuentasCerradas();
                    
            $("#countdown").attr("style", "display:none");
            $("#modalBloquearCargaCronometro").hide();
            cargando(0);

            fn_botonCancelar();
            fn_cargando(0);  
            alertify.alert('<h3>Cambio de Datos Cliente generado correctamente...!!!</h3> <br><br>'); 
                      
        } 
    }});


}

/* UPDATE: Genera la clave de acceso para la nota de crédito creada.
 * INSERT: Impresión de comprobante Nota de Crédito. */
function fn_generarClaveAcceso(codigoFactura, usuarioAdmin, documentoCliente, idEstacion, aplicaEnEstacion, servidorUrlApi, ipEstacionCambioDatos){
    var send; 
    var Accion = "C";    
    send = {"anulacionFormasPago": 1};
    
    send.accion = Accion; 
    send.codigoFactura = codigoFactura;
    send.usuarioAdmin = usuarioAdmin;
    send.documentoCliente = documentoCliente;
    send.idEstacion  = idEstacion;
    send.servidorUrlApi = servidorUrlApi;
    send.ipEstacionCambioDatos = ipEstacionCambioDatos;

    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "config_CambioDatosCliente.php", data: send, success: function (datos){   
            
             send.accion = 'I'; 
            
            if(datos.Respuesta > 0){

                var apiImpresion = getConfiguracionesApiImpresion();

                if(aplicaEnEstacion == 1){

                    fn_cargando(1);

                    var result = new apiServicioImpresion('impresion_cambio_datos_cliente', codigoFactura, 0, send);
                    var imprime = result["imprime"];
                    var mensaje = result["mensaje"];

                    console.log('imprime: ', imprime);

                    if (!imprime) {
                        alertify.success('Imprimiendo...');
                        fn_cuentasCerradas();
                        fn_cargando(0);

                    } else {
                    
                        alertify.success('Error al imprimir...');
                        fn_cuentasCerradas();
                        fn_cargando(0);

                    }

                }else{

                    
                if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                    fn_cargando(1);

                    var result = new apiServicioImpresion('impresion_cambio_datos_cliente', codigoFactura, 0, send);
                    var imprime = result["imprime"];
                    var mensaje = result["mensaje"];

                    console.log('imprime: ', imprime);

                    if (!imprime) {
                        alertify.success('Imprimiendo...');
                        fn_cuentasCerradas();
                        fn_cargando(0);

                    } else {
                    
                        alertify.success('Error al imprimir...');
                        fn_cuentasCerradas();
                        fn_cargando(0);

                    }

                    }

                }


            } 
        }});





}

/* UPDATE: Genera la clave de acceso para la nueva factura.
 * INSERT: Impresión de comprobante Factura. */
function fn_actializarFacturaDuplicada(usuarioAdmin, documentoCliente, idEstacion, aplicaEnEstacion, servidorUrlApi, ipEstacionCambioDatos){
    var send; 
    var Accion = "A"; 
    var codigoFactura = $("#hid_CodigoNuevaCredito").val();

    send = {"anulacionFormasPago": 1};
    send.accion = Accion; 
    send.codigoFactura = codigoFactura;
    send.usuarioAdmin = usuarioAdmin;
    send.documentoCliente = documentoCliente;
    send.idEstacion  = idEstacion;
    send.servidorUrlApi = servidorUrlApi;
    send.ipEstacionCambioDatos = ipEstacionCambioDatos;

    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "config_cambioDatosCliente.php", data: send, success: function (datos){    
        
         send.accion = 'F'

        if(datos.Respuesta > 0){

        var apiImpresion = getConfiguracionesApiImpresion();

        if(aplicaEnEstacion == 1){

        fn_cargando(1);

        var result = new apiServicioImpresion('impresion_cambio_datos_cliente', codigoFactura, 0, send);
        var imprime = result["imprime"];
        var mensaje = result["mensaje"];

        console.log('imprime: ', imprime);

        if (!imprime) {
            alertify.success('Imprimiendo...');
            fn_cuentasCerradas();
            fn_cargando(0);

        } else {
        
            alertify.success('Error al imprimir...');
            fn_cuentasCerradas();
            fn_cargando(0);

        }



    }else{

        
        if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
            fn_cargando(1);

            var result = new apiServicioImpresion('impresion_cambio_datos_cliente', codigoFactura, 0, send);
            var imprime = result["imprime"];
            var mensaje = result["mensaje"];

            console.log('imprime: ', imprime);

            if (!imprime) {
                alertify.success('Imprimiendo...');
                fn_cuentasCerradas();
                fn_cargando(0);

            } else {
            
                alertify.success('Error al imprimir...');
                fn_cuentasCerradas();
                fn_cargando(0);

            }

        }


    }


        } 
    }});


}

/* Valida que los campos de la modal cliente sean llenados */
function fn_validaCamposCliente(opcion, valid){
    console.log('valid')
    console.log(valid)
    if($("#txtClienteCI").val() == ''){        
        alertify.alert("Documento de identificaci&oacute;n obligatorio. <br>");
        $("#txtClienteCI").focus();
        return false;       
    } else if($("#txtClienteNombre").val().trim() == ''){
        $("#keyboardCliente").hide();
        alertify.alert("Ingrese los nombres del Cliente. <br>");
        $("#txtClienteNombre").focus();
        return false; 
    } else if (fn_validarCorreoElectronico($("#txtCorreo").val())) {
        if (opcion == "U"){            
            fn_confirmarDatosAnular(opcion, valid); 
        } else if (opcion == "I"){
            fn_confirmarDatosAnular(opcion, valid);            
        }        
    } else {
        alertify.alert("Direcci\u00f3n de correo electr&oacute;nico no v\u00e1lida.. <br>");
    }
}

function fn_confirmarDatosAnular(accion, valid){
    var validaIdentificacion = $("#hid_tecladoIdentificacion").val();
    
    // 1 = Cedula RUC : 2 = Pasaporte
    if (accion == "U"){        
        if (validaIdentificacion == 1){
            if($("#txtClienteCI").val().length == 13) {        
                fn_actualizarCambioDatosCliente(); 
             

            }
            else if(fn_validarDocumento($("#txtClienteCI").val())) {        
                fn_actualizarCambioDatosCliente(); 
            } else {
                fn_documentoInvalido();  
            }
        } else if (validaIdentificacion == 2) {
            fn_actualizarCambioDatosCliente(); 
        }        
    } else if (accion == "I"){
        if (validaIdentificacion == 1){
            if($("#txtClienteCI").val().length == 13) {        
                fn_guardarCambioDatosNuevoCliente(valid);
            
            }
            else
            if(fn_validarDocumento($("#txtClienteCI").val())) {        
                fn_guardarCambioDatosNuevoCliente(valid);
            } else {
                fn_documentoInvalido();  
            }
        } else if (validaIdentificacion == 2) {
            fn_guardarCambioDatosNuevoCliente(valid);
        } 
    }    
}

/* Valida email */
function fn_validarCorreoElectronico() {
    var evaluar = $("#txtCorreo").val();    
    evaluar = evaluar.trim();   
    var filter = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

    if (evaluar.length == 0) 
            return true;
    if (filter.test(evaluar))
            return true;
    else{        
        $("#txtCorreo").focus();
        $("#keyboardCliente").hide();
        $("#dominio1").hide();
        $("#dominio2").hide();
        return false;
    }
}

/* UPDATE: Actualiza los datos del cliente */
function fn_actualizarCambioDatosCliente(){

    fn_cargando(1);
    var send;
    var Accion = "U";
    var tipoDocumento = "0"; 
    var documento = $("#txtClienteCI").val();
    var strDocumento = documento.substring(0, 15);
    var trimDocumento = strDocumento.trim();
    
    send = {"actualizarDatosCliente":1};
    send.accion = Accion;   
    send.clienteTipoDoc = tipoDocumento;
    send.clienteDocumento = trimDocumento
    send.clienteDescripcion = $("#txtClienteNombre").val();
//    send.clienteDireccion = $("#txtClienteDireccion").val();
    send.clienteFono = $("#txtClienteFono").val();
    send.clienteCorreo = $("#txtCorreo").val();
    send.usuario = $("#hid_usuarioAdmin").val(); 
    send.estadoWS = 1; 
    send.tipoCliente = "NULL";
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../facturacion/config_clientes.php", data: send, success: function (datos) {  
        $("#dominio1").hide();
        $("#dominio2").hide();
        fn_bloquearIngreso();
        fn_ocultarTecladoAlfanumerico(numPadCliente);
        
        var es_nota_credito = $("#hide_opcion_nota_credito").val();

        if (es_nota_credito == 1) {
            fn_duplicarFacturaActual();
        } else {
            $("#hide_cliente").val($("#txtClienteCI").val());
            fn_CerrarModalClientes();
            fn_formasPago();
        }

        fn_cargando(0);
    }});
}

/* Cuando el cliente no existe ni en la base local ni en la base central */
function fn_nuevoCliente(documento){
    fn_limpiarCamposCliente(); 
    fn_habilitarIngreso();    
    $("#numPadCliente").hide();
    $("#nombres_obligatorios").show();
    $("#direccion_obligatorios").show(); 
    $("#txtClienteNombre").focus();  
    fn_alfanumericoCliente(txtClienteNombre);   
    //$("#txtClienteCI").removeAttr("disabled");
    $("#txtClienteCI").val(documento);  
    $("#btnNuevoCliente").show();
    $("#btnClienteConfirmarDatos").hide(); 
    $("#btnClienteCancelarAnulacion").show();
    
    $("#rdo_ruc").prop('disabled', true);
    $("#rdo_pasaporte").prop('disabled', true);
   // $("#txtClienteCI").attr("disabled", "-1");
    $("#sri_leyenda").hide();
}

/*INSERT: Guarda el cliente en la base local y central */
function fn_guardarCambioDatosNuevoCliente(valid){
    fn_cargando(1);
    var send;
    var Accion = 'I';
    var tipoDocumento;
    var estado = $("#clienteEstado").val();
    var validaTipoDocumento = $("#hid_tecladoIdentificacion").val();
    var documento = $("#txtClienteCI").val();
    var strDocumento = documento.substring(0, 15);
    var trimDocumento = strDocumento.trim();
    fn_validaEmail(valid);
    
    if (validaTipoDocumento == 1) {
        if($("#txtClienteCI").val().length == 10) {
            tipoDocumento = "CEDULA";
        } else if($("#txtClienteCI").val().length == 13){
            tipoDocumento = "RUC";
        }
        fn_cargando(0);
    } else if (validaTipoDocumento == 2) {
        tipoDocumento = "PASAPORTE";
        fn_cargando(0);
    }
    
    send = {"nuevoCliente":1};
    send.accion = Accion;   
    send.clienteTipoDoc = tipoDocumento;
    send.clienteDocumento = trimDocumento;
    send.clienteDescripcion = $("#txtClienteNombre").val();
//    send.clienteDireccion = $("#txtClienteDireccion").val();
    send.clienteFono = $("#txtClienteFono").val();
    send.clienteCorreo = $("#txtCorreo").val();
    send.usuario = $("#hid_usuarioAdmin").val();  
    send.estadoWS = $("#estadoWS").val();
    send.tipoCliente = "NULL";
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../facturacion/config_clientes.php", data: send, success: function (datos) {
        $("#dominio1").hide();
        $("#dominio2").hide();
        fn_bloquearIngreso(); 
        fn_ocultarTecladoAlfanumerico(numPadCliente);
        
        var es_nota_credito = $("#hide_opcion_nota_credito").val();

        if (es_nota_credito == 1) {
            fn_duplicarFacturaActual();
        } else {
            $("#hide_cliente").val($("#txtClienteCI").val());
            fn_CerrarModalClientes();
            fn_formasPago();
        }

        fn_cargando(0);
    }});
    
    // Envio la informacion solo si el cliente no existe en el local ni en la base centralizada
    if (estado == 2){ 
        fn_envioCambioDatosClienteWS(tipoDocumento, $("#txtClienteCI").val(), $("#txtClienteNombre").val(), /*$("#txtClienteDireccion").val()*/ '', $("#txtClienteFono").val(), $("#txtCorreo").val());
    }
}

/* Envio datos de cliente a la base centralizada por Web Services */
function fn_envioCambioDatosClienteWS(tipoDocumento, documento, descripcion, direccion, telefono, correo){
    var send; 
    var autorizacion = $("#clienteAutorizacion").val();    
    var strDocumento = documento.substring(0, 15);
    var trimDocumento = strDocumento.trim();
    
    send = {};
    send.metodo = "enviarCliente";
    send.autorizacion = autorizacion;
    send.tipoDocumento = tipoDocumento;
    send.documento = trimDocumento;
    send.descripcion = descripcion;
    //send.direccion = direccion;
    send.telefono = telefono;
    send.correo = correo;   
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../facturacion/clienteWSClientes.php", data: send, success: function (datos) {
    }}); 
}

/* CONSULTA: Carga las teclas email */
function fn_cargaTeclasCorreoElectronico(){
    var send;
    send = {"cargaTeclasEmail": 1};
    $.getJSON("../facturacion/config_facturacion.php", send, function (datos) {
        if (datos.str > 0) {
            $("#dominio1").empty();
            $("#dominio2").empty();
            for (var i = 0; i < datos.str; i++) {
                html = "<button class='btnVirtualDominio' style='font-size: 13px;' onclick='fn_agregarCaracterCorreo(txtCorreo,\"" + datos[i]['descripcionEmail'] + "\")'>" + datos[i]['descripcionEmail'] + "</button><br/>";
                if (i < 5) {
                    $("#dominio1").append(html);
                }
                if (i > 4) {
                    $("#dominio2").append(html);
                }
            }
        }
    });
}

function informacion_sri() {
    
    var send;
    var html = '';

    send = { "informacionSRI": 1 };
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_cambioDatosCliente.php",
        data: send,
        success: function (datos) {       
            if (datos.str > 0) {
                var ver_leyenda = '0';
                var titulo = '';
                var articulo = '';                
                var nota = '';               
                var info = '';
                                
                for (var i = 0; i < datos.str; i++) {
                    if (datos[i]['campo'] == 'ver-leyenda') {
                        ver_leyenda = datos[i]['dato'];   
                    } else if (datos[i]['campo'] == 'titulo') {
                        titulo = datos[i]['dato'];   
                    } else if (datos[i]['campo'] == 'articulo') {
                        articulo = datos[i]['dato'];   
                    } else if (datos[i]['campo'] == 'nota') {
                        nota = datos[i]['dato'];   
                    } else {
                        info = JSON.parse(datos[i]['dato']);   
                    }                    
                }

                if (ver_leyenda == '1') {
                    $("#sri_leyenda").show();
                    
                    html = html + '<h4 class="sri-titulo">'+titulo+'</h4>';
                    html = html + '<h3 class="sri-articulo">'+articulo+'</h3>';
                    html = html + '<p class="sri-nota">'+nota+'</p>';
                    
                    html = html + '<div class="sri-info-content">';
                    
                    for(var i = 0; i < info.length; i++){
                        html = html +   '<p class="sri-info">'+info[i]['dato']+'</p>';
                    }

                    html = html + '</div>';                    

                    $("#sri_leyenda").html(html);
                }
            }      
        }
    });
}


function fn_validaEmailRegisteredAPI(email) {
  console.log("aqui viernes api nueva funcion cambio datos");
  /*var arr_e = [];
  var datos_existentes = localStorage.getItem("email_valid");
  datos_existentes =
    datos_existentes == null ? [] : JSON.parse(datos_existentes);
  arr_e = localStorage.getItem("email_valid");*/
  console.log('aqui viernes api nueva funcion')
  var arr_e = [];
  var datos_existentes = localStorage.getItem('email_valid');
  datos_existentes = datos_existentes == null ? [] : JSON.parse(datos_existentes);
  arr_e = localStorage.getItem('email_valid');
  expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if ( !expr.test(email) ){
      console.log('el email es incorrecto')
      return;
  }

  if(localStorage.getItem('intValidaCC') === undefined || localStorage.getItem('intValidaCC') === null){
      console.log('no existe la config, se procede a cargarla')
      localStorage.setItem('intValida', valid);
      localStorage.setItem('intValidaFC', valid);
      localStorage.setItem('intValidaOP', valid);
      localStorage.setItem('intValidaCC', valid);
  }

  send = {"cargarUrlApiValidaPlugthem": 1, "CustomerEmail": email};
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    url: "../ordenpedido/config_ordenPedido.php",
    data: send,
    success: function (datos) {
        console.log('RESPONSE VALIDACIÓN EMAIL API cambio cliente');
        console.log('response en 1')
        var objectStringArray = (new Function("return [" + datos+ "];")());
        console.log(objectStringArray[0]['status'])
        var existsE = arr_e != null ? arr_e.includes(email) : false;
        if (objectStringArray[0]['status'] == 'invalid'){
            if(!existsE){
                datos_existentes.push(email);
                console.log('no se ha validado el correo ingresado, reinicio contador')
                //localStorage.removeItem('email_valid');
                localStorage.setItem("intValidaCC", localStorage.getItem("intValida") - 1);
                alertify.alert("El email ingresado no es v\u00e1lido. Por favor vuelva a verificar." + "Le restan " + localStorage.getItem("intValidaCC").toString() + " " + "intentos. Al culminarlos el correo será tomado como válido.");
            } else {
                console.log('resto del actual')
                localStorage.setItem("intValidaCC", localStorage.getItem("intValidaCC") - 1);
                if(localStorage.getItem("intValidaCC") > 0) {
                    alertify.alert("El email ingresado no es v\u00e1lido. Por favor vuelva a verificar." + "Le restan " + localStorage.getItem("intValidaCC").toString() + " " + "intentos. Al culminarlos el correo será tomado como válido.");
                } else if (localStorage.getItem("intValidaCC") == 0)  {
                    alertify.alert("El email ingresado no es v\u00e1lido. Ya agoto sus intentos de verificación, el correo será tomado como válido.");    
                }
            }
            localStorage.setItem('email_valid', JSON.stringify(datos_existentes));
        } 
    /*  var urlService = datos;

      $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        //contentType: "application/json",
        url: urlService,
        data: { CustomerEmail: email.toString() },
        success: function (datos) {
          console.log("RESPONSE VALIDACIÓN EMAIL API");
          var existsE = arr_e != null ? arr_e.includes(email) : false;
          if (existsE) {
            console.log("ya se valido el correo, resto del arry");
            localStorage.setItem(
              "intValidaFC",
              localStorage.getItem("intValidaFC") - 1
            );
          } else {
            console.log(
              "no se ha validado el correo ingresado, reinicio contador"
            );
            //localStorage.removeItem('email_valid');
            localStorage.setItem(
              "intValidaFC",
              localStorage.getItem("intValida")
            );
          }
          if (datos["status"] == "invalid") {
            if (!existsE) {
              datos_existentes.push(email);
            }
            localStorage.setItem(
              "email_valid",
              JSON.stringify(datos_existentes)
            );
            if (localStorage.getItem("intValidaFC") > 0) {
              alertify.alert(
                "El email ingresado no es v\u00e1lido. Por favor vuelva a verificar." +
                  "Le restan " +
                  localStorage.getItem("intValidaFC").toString() +
                  " " +
                  "intentos. Al culminarlos el correo será tomado como válido."
              );
            } else {
              alertify.alert(
                "El email ingresado no es v\u00e1lido. Ya agoto sus intentos de verificación, el correo será tomado como válido."
              );
            }
            //fn_habilitarIngreso();
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log("error validando emaila");
        },
      });*/
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log("error respuesta consulta sp de url de politica");
    },
  });
}
