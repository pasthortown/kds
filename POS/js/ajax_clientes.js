/* global alertify, parseFloat, numPadCliente, txtClienteCI, txtClienteNombre, txtCorreo, txtClienteFono, txtClienteDireccion */

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para Formas de Pago////////////////
///////FECHA CREACION: 28-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION:21/04/2014///////////////////
///////USUARIO QUE MODIFICO: Jose Fernandez///////////////////
///////DECRIPCION ULTIMO CAMBIO: validadion de Consumidor///// 
//////////////////////////////////Final///////////////////////
//////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION:04/01/2016///////////////////
///////USUARIO QUE MODIFICO: Christian Pinto//////////////////
///////DECRIPCION ULTIMO CAMBIO: validador del correo para /// 
///////que acepte guiones, ingreso del tel�fono opcional, ////
///////validaci�n que no borre la cedula,mensaje que sale ////
///////atr�s de teclado///////////////////////////////////////
//////////////////////////////////////////////////////////////

var CLIENTE_REPETIDO = 0;
var clientetipodocumentokiosko = "";
var clienteexisteenmasterdatacliente = "";
var facturaImpresa = false;
$(document).ready(function() {
    fn_cargando(0);
    fn_bloquearIngreso();
    $("#btnClienteGuardar").hide();
    $("#btnClienteGuardarActualiza").hide();
    $("#btnConsumidorFinal").show();
    $("#nombres_obligatorios").hide();
    $("#direccion_obligatorios").hide();
    $("#btnClienteConfirmarDatos").hide();
    $("#btnClienteConfirmarDatosFacturar").hide();
    localStorage.setItem("invalido",0);
    
});

var cuenta = '_1';
if ((localStorage.getItem("cuenta") != '') && (localStorage.getItem("cuenta") != null)){
    cuenta = '_'+localStorage.getItem("cuenta");
}

function fn_cambioEstadosAutomatico() {
    send = { metodo: "cambioEstadosAutomatico" };
    send.cdn_id = 'NO' ;
    $.ajax({
      async: true,
      type: "POST",
      dataType: 'json',
      contentType: "application/x-www-form-urlencoded",
      url: "../ordenpedido/config_app.php",
      data: send,
      success: function( datos ) {

        console.log('CAMBIO ESTADOS');
        console.log(datos);

        if(datos && datos[0]){   

          $("#cambio_estados_automatico").val(datos[0].automatico);
          
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      }
    });
  }
  

/* Valida documento de cliente */
function fn_clienteBuscar(cambioDocumento = false, valid, revocado = 0) {
    console.log('valid cliente')
    console.log(cambioDocumento);

    var documento = '';

    // Verificar si existen datos de cliente en localstorage
    if(!cambioDocumento){
        if ( localStorage.getItem("ls_documento"+cuenta) ) {
            $("#txtClienteCI").val( localStorage.getItem("ls_documento"+cuenta) );
        }
    }

    documento = $("#txtClienteCI").val();
    typeDocument = (localStorage.getItem("ls_typeDocument"+cuenta) !== undefined) ? localStorage.getItem("ls_typeDocument"+cuenta) : ""; //cedula - pasaporte
    if(typeDocument == 'pasaporte'){
        $("#rdo_ruc").removeClass("btnRucCiActivo")
        $("#rdo_ruc").addClass("btnRucCiInactivo")
        $("#rdo_pasaporte").removeClass("btnRucCiInactivo")
        $("#rdo_pasaporte").addClass("btnRucCiActivo")
    }
    fn_bloquearIngreso();

    if(documento=="9999999999999"){
        fn_numerico(txtClienteCI);
    }

    const $fdznDocumento = $("#fdznDocumento");
    if($fdznDocumento.length > 0 && documento == $fdznDocumento.val()){
        console.log('es igual a fidelizacion mostrar teclado');
        fn_numerico(txtClienteCI);
    }
        

    fn_masterdatacliente("CONSULTAR", documento, typeDocument, null, valid, revocado);
}

function fn_masterdatacliente(accion, documento=null, nombreTipodocumento=null, datos=null, valid, revocado = 0) {
    if (accion=="CONSULTAR") {
        if ($("#rdo_ruc").hasClass("btnRucCiActivo") && localStorage.getItem("invalido") == 0) {
            var validacampos = __validaciondocumento(documento);
        } else if ($("#rdo_ruc").hasClass("btnRucCiActivo") && localStorage.getItem("invalido") == 1) {
            var validacampos = _validacionDocumentoRUC(documento);
        } else {
            var validacampos = __validaciondocumentopasaporte(documento);
            $("#hid_pasaporte").val(1);
        }

        if (!validacampos) {
            return;
        }

        var getClienteMDC = __getClienteMDC(validacampos);
        if (getClienteMDC.success == true || getClienteMDC.statusCode == 200) { //Existe el cliente en la Master Data Clientes
            alertify.success("Cliente existe en MasterDataCliente");
            var clienteMapeado = {};

            const primerNombre = getClienteMDC['data']['cliente']['primerNombre'] || '';
            const apellidos = getClienteMDC['data']['cliente']['apellidos'] || '';

            clienteMapeado.identificacion = getClienteMDC['data']['cliente']['documento'];
            clienteMapeado.descripcion = (primerNombre + ' ' + apellidos).trim();
            clienteMapeado.direccionDomicilio = getClienteMDC['data']['cliente']['direccion'];
            clienteMapeado.telefonoDomiclio = getClienteMDC['data']['cliente']['telefono'];
            clienteMapeado.correo = getClienteMDC['data']['cliente']['email'];
            clienteMapeado.tipoCliente = "";
            clienteMapeado.tipo_documento = getClienteMDC['data']['cliente']['tipoDocumento'];
            clienteMapeado.IDCliente = getClienteMDC['data']['cliente']['_id'];
            clienteMapeado.estado = 1;
            var cliente;
            cliente = {
                cliente: clienteMapeado
            };

            let userJsonData = JSON.stringify(cliente);
            sessionStorage.setItem("DatosCliente", userJsonData);
            $("#estadoWS").val(1);
            fn_cargaDatosCliente(1, cliente.cliente.IDCliente, cliente.cliente.identificacion, cliente.cliente.descripcion, cliente.cliente.direccionDomicilio, cliente.cliente.telefonoDomiclio, cliente.cliente.correo, cliente.cliente.tipoCliente, cliente.cliente.estado, cliente.tipo_documento, valid);
            clienteexisteenmasterdatacliente = 1;
            var getClienteLocal = __getClienteLocal(validacampos, valid, revocado);
        } else if(getClienteMDC.statusCode == 500) {
            alertify.error("Problemas al conectar con MasterDataCliente: " + getClienteMDC.response);
            $("#estadoWS").val(0);
            var getClienteLocal = __getClienteLocal(validacampos, valid, revocado);
        } else { //No existe el cliente en la Master Data Cliente o hubo un error con la API
            alertify.success("Cliente NO existe en MasterDataCliente");
            $("#estadoWS").val(0);
            var getClienteLocal = __getClienteLocal(validacampos, valid, revocado);
        }
    } else if (accion == "GUARDAR" || accion == "MODIFICAR") {
        var saveClienteMDC = __saveupdateClienteMDC(accion,datos);
        if (saveClienteMDC.success == true || saveClienteMDC.statusCode == 200 || saveClienteMDC.uid) {
            alertify.success("Cliente GUARDADO/MODIFICADO en MasterDataCliente");
        } else if (saveClienteMDC.statusCode == 500) {
            alertify.error("Problemas al conectar con MasterDataCliente: " + saveClienteMDC.response);
        }
        return saveClienteMDC;
    }
}

function __getClienteLocal(validacampos, valid, revocado) {
    fn_cargando(1);
    send = {};
    send.metodo = "buscarClienteV2";
    send.documento = validacampos.documento;
    send.tipoDocumento = validacampos.tipoDocumento;
    send.revocado = revocado;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../facturacion/clienteWSClientes.php",
        data: send,
        success: function (datos) {
            let userJsonData = JSON.stringify(datos);
            sessionStorage.setItem("DatosCliente", userJsonData);

            var cliente = datos.cliente;
            if (datos['estado'] == 1) { //Existe cliente en la base de datos local y carga los datos en el formulario
                fn_cargando(0);
                $("#estadoWS").val(1);
                fn_cargaDatosCliente(1, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, cliente.tipo_documento, datos.estado, valid);
            } else if(datos['estado'] == 0) { //No existe cliente en la base de datos local y presenta formulario para crear nuevo cliente
                fn_cargando(0);
                $("#estadoWS").val(1);
                fn_crearCliente(validacampos.documento);
            }
        },
        error: function () { 

        }
    });
}

function __getClienteMDC(validacampos){
    var result = apiServicioMasterDataCliente('BUSCAR', {
        'cdn_id':$("#txtCadenaId").val(),
        'documento':validacampos.documento,
        'idUserPos':$("#txtUserId").val(),
        'rst_id':$('#txtRestaurante').val()
    });
    var response = JSON.parse(result);
    return response;
}

function __saveupdateClienteMDC(accion,validacampos) {
    var result = apiServicioMasterDataCliente(accion,{
        'cdn_id':validacampos.cdn_id,
        'documento':validacampos.documento,
        'tipoDocumento':validacampos.tipoDocumento,
        'email':validacampos.email,
        'telefono':validacampos.telefono,
        'primerNombre':validacampos.primerNombre,
        'apellidos':validacampos.apellidos,
        'direccion':validacampos.direccion,
        'idUserPos':validacampos.idUserPos,
        'rst_id':validacampos.rst_id
    });
    var response = JSON.parse(result);
    return response;
}

function __validaciondocumentopasaporte(documento) {
    $("#hid_pasaporte").val(1);
    if ($("#txtClienteCI").val() != '') {
        var consumidor_final = '9999999999';
        if ($("#txtClienteCI").val() == consumidor_final
        || $("#txtClienteCI").val() == consumidor_final + '9'
        || $("#txtClienteCI").val() == consumidor_final + '99'
        || $("#txtClienteCI").val() == consumidor_final + '999') 
        {
            alertify.alert("Debe ingresar factura con datos. No puede ser consumidor final. <br><br>");

            fn_alfaNumericoo(txtClienteCI);

            $("#btnClienteConfirmarDatos").hide();
            $("#btnClienteGuardar").hide();
            $("#btnClienteGuardarActualiza").hide();
            $("#btnClienteConfirmarDatosFacturar").hide();
            $("#btnConsumidorFinal").show();
        }else{
            let tipoDocumento="PASAPORTE";
            return {documento,tipoDocumento};
        }
    } else {
        alertify.alert("Documento de identificaci&oacute;n obligatorio. <br><br>");
        fn_alfaNumericoo(txtClienteCI);
    }
}

function __validaciondocumento(documento) {
    aplicaPlugThem();
    $("#hid_conDatos").val(1);
    fn_limpiarCamposCliente();
    var send;
    var tipoDocumento;

    fn_sumarconteo();

    if (fn_validarDocumento($("#txtClienteCI").val())) {
        localStorage.setItem("invalido", 0);

        if ($("#txtClienteCI").val().length == 10) {
            tipoDocumento = "CEDULA";
        } else if ($("#txtClienteCI").val().length == 13) {
            tipoDocumento = "RUC";
        }

        return {documento, tipoDocumento};
    } else {
        fn_cargando(0);
        let HabilitarValidacionRUC = $("#HabilitarValidacionRUC").val();
        let validarDocumentoCliente = fn_validarDocumento($("#txtClienteCI").val());
        let ValidarPoliticaRuc = fn_validarRucPolitica();

        if ($("#txtClienteCI").val().length == 13) {
            if ($("#txtClienteCI").val() != '9999999999999') {
                if (HabilitarValidacionRUC == '1' && ValidarPoliticaRuc == 0) {
                    alertify.alert($("#ValidacionErrorRUC").val()+" "+$("#ValidacionRucCodigo").val());   
                    $("#txtClienteCI").focus();
                    return false;
                } else if(HabilitarValidacionRUC == '1' && validarDocumentoCliente == 0  && ValidarPoliticaRuc == 1) {
                    if (fn_conteoDirectoRUC() == false) {
                        if (fn_conteoValidacionRUC() == true) { 
                            alertify.alert("N\u00famero de documento no v\u00e1lido, por favor ingrese credenciales de administrador.");
                            localStorage.setItem("invalido", 1);
                            fn_solicitaCredencialesAdministrador();
                            $('#btnClienteConfirmarDatos').hide();
                            $('#btnConsumidorFinal').show();
                        } else {
                            fn_documentoInvalido();
                            return false;
                        }
                    } else {
                        fn_cargando(0);
                        $("#clienteAutorizacion").val('');
                        $("#estadoWS").val(1);
                        fn_crearCliente(documento);
                    }
                } else if (HabilitarValidacionRUC == '0' && validarDocumentoCliente == 0) {
                    alertify.alert("N\u00famero de documento no v\u00e1lido, por favor ingrese credenciales de administrador.");
                    localStorage.setItem("invalido",1)
                    fn_solicitaCredencialesAdministrador();
                    $('#btnClienteConfirmarDatos').hide();
                    $('#btnConsumidorFinal').show();
                }  
            } else {
                console.log("Es consumidor final, no se valida.");
                return false;
            }            
        } else {
			fn_documentoInvalido();
		}
    }

    return false;
}

function _validacionDocumentoRUC(documento) {
    aplicaPlugThem();
    $("#hid_conDatos").val(1);
    fn_limpiarCamposCliente();
    $('#btnClienteGuardar').hide();
    $('#btnClienteGuardarActualiza').hide();
    $('#btnClienteConfirmarDatos').hide();
    $('#btnClienteConfirmarDatosFacturar').hide();
    let tipoDocumento="RUC";
    $("#hid_bandera_teclado").val(1);

    return {documento, tipoDocumento};
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


function fn_conteoDirectoRUCVariable(cadena,tercera){
    for (i = 0; i < cadena.length; i++) {
        if(cadena[i]==tercera){
            return true;
        }
      }
      return false; 
    }

/* Consumo Web Service por documento CI/RUC :  */
function fn_documentoCedulaRuc(documento, valid, revocado = 0) {
    aplicaPlugThem();
    $("#hid_conDatos").val(1);
    fn_limpiarCamposCliente();
    var send;
    var tipoDocumento;
  
    fn_sumarconteo();

    if (fn_validarDocumento($("#txtClienteCI").val())) { // || ($("#cli_nombres").val().length && fn_conteoDirectoRUC()==true)
        localStorage.setItem("invalido",0)
        fn_cargando(1);
        if ($("#txtClienteCI").val().length == 10) {
            tipoDocumento = "CEDULA";
        } else if ($("#txtClienteCI").val().length == 13) {
            tipoDocumento = "RUC";
        }
        send = {};
        send.metodo = "buscarCliente";
        send.documento = documento;
        send.tipoDocumento = tipoDocumento;
        send.revocado = revocado;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            "Accept": "application/json, text/javascript2",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../facturacion/clienteWSClientes.php",
            data: send,
            success: function(datos) {
               console.log(datos); 
                if (datos.hasOwnProperty("cliente")) {
                    var cliente = datos.cliente;
                    $("#clienteEstado").val(datos.estado);
                    if (datos.estado === 1) { // El cliente no existe en el local pero si existe en Azur                     
                        fn_cargando(0);
                        $("#estadoWS").val(1);
                        fn_cargaDatosCliente(1, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, datos.estado, cliente.tipo_documento, valid);
                    } else if (datos.estado === 2) { // El cliente no existe Azur ni en el local                      
                        fn_cargando(0);
                        var cliente = datos.cliente;
                        $("#clienteAutorizacion").val(cliente.autorizacion);
                        $("#estadoWS").val(1);
                        fn_crearCliente(documento);
                    } else if (datos.estado === 3) { // El cliente existe en el local                       
                        fn_cargando(0);
                        $("#estadoWS").val(1);
                        fn_cargaDatosCliente(1, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, datos.estado, cliente.tipo_documento, valid);
                    } else if (datos.estado == 0) {
                        console.error("Servicio Master Data no Disponible...");
                        fn_cargando(0);
                        fn_crearCliente(documento);
                        $("#estadoWS").val(0);
                    } else if (datos.estado == 4) {
                        alertify.error("No ha configurado la política WS CLIENTE.");
                        fn_cargando(0);
                        fn_crearCliente(documento);
                        $("#estadoWS").val(0);
                    }
                } else {
                    // Cuando el WS no esta en linea o error en la consulta
                    fn_cargando(0);
                    fn_crearCliente(documento);
                    $("#estadoWS").val(0);
                }
            },
            error: function() { // Cuando el WS no esta en linea 
                fn_cargando(0);
                fn_crearCliente(documento);
                $("#estadoWS").val(0);
            }
        });
    } else {
        fn_cargando(0);
        let HabilitarValidacionRUC=$("#HabilitarValidacionRUC").val();
        let validarDocumentoCliente=fn_validarDocumento($("#txtClienteCI").val());
        let ValidarPoliticaRuc=fn_validarRucPolitica();

        if($("#txtClienteCI").val().length == 13){
            if ($("#txtClienteCI").val() != '9999999999999') {
                if(HabilitarValidacionRUC=='1' && ValidarPoliticaRuc==0){
                    alertify.alert($("#ValidacionErrorRUC").val()+" "+$("#ValidacionRucCodigo").val());   
                    $("#txtClienteCI").focus();
                    return false;
                }
                else if(HabilitarValidacionRUC=='1' && validarDocumentoCliente==0  && ValidarPoliticaRuc==1)
                {
                    if(fn_conteoDirectoRUC()==false){
                        if(fn_conteoValidacionRUC()==true){ 
                            alertify.alert("N\u00famero de documento no v\u00e1lido, por favor ingrese credenciales de administrador.");
                            localStorage.setItem("invalido",1)
                            fn_solicitaCredencialesAdministrador();
                            $('#btnClienteConfirmarDatos').hide();
                            $('#btnConsumidorFinal').show();
                        }else{
                            fn_documentoInvalido();
                            return false;
                        }
                    }else{
                        fn_cargando(0);
                        $("#clienteAutorizacion").val('');
                        $("#estadoWS").val(1);
                        fn_crearCliente(documento);
                    }
                }else if(HabilitarValidacionRUC=='0' && validarDocumentoCliente==0){
                    alertify.alert("N\u00famero de documento no v\u00e1lido, por favor ingrese credenciales de administrador.");
                    localStorage.setItem("invalido",1)
                    fn_solicitaCredencialesAdministrador();
                    $('#btnClienteConfirmarDatos').hide();
                    $('#btnConsumidorFinal').show();
                }   
            }            
        }
        else{
       fn_documentoInvalido();}
    }
}
function fn_documentoRuc(documento, valid) {
    aplicaPlugThem();
    $("#hid_conDatos").val(1);
    fn_limpiarCamposCliente();
    $('#btnClienteGuardar').hide();
    $('#btnClienteGuardarActualiza').hide();
    $('#btnClienteConfirmarDatos').hide();
    $('#btnClienteConfirmarDatosFacturar').hide();
    var send;
    var tipoDocumento;
        tipoDocumento = "RUC";
        send = {};
        send.metodo = "buscarCliente";
        send.documento = documento;
        send.tipoDocumento = tipoDocumento;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            "Accept": "application/json, text/javascript2",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../facturacion/clienteWSClientes.php",
            data: send,
            success: function(datos) {
               console.log(datos); 
                if (datos.hasOwnProperty("cliente")) {
                    var cliente = datos.cliente;
                    $("#clienteEstado").val(datos.estado);


                    if (datos.estado === 1) { // El cliente no existe en el local pero si existe en Azur                     
                        fn_cargando(0);
                        $("#estadoWS").val(1);
                        fn_cargaDatosCliente(1, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, datos.estado, cliente.tipo_documento, valid);
                    } else if (datos.estado === 2) { // El cliente no existe Azur ni en el local                      
                        fn_cargando(0);
                        var cliente = datos.cliente;
                        $("#clienteAutorizacion").val(cliente.autorizacion);
                        $("#estadoWS").val(1);
                        fn_crearCliente(documento);
                    } else if (datos.estado === 3) { // El cliente existe en el local                       
                        fn_cargando(0);
                        $("#estadoWS").val(1);
                        fn_cargaDatosCliente(1, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, datos.estado, cliente.tipo_documento, valid);
                    } else if (datos.estado == 0) {
                        console.error("Servicio Master Data no Disponible...");
                        fn_cargando(0);
                        fn_crearCliente(documento);
                        $("#estadoWS").val(0);
                    }


                } else {
                    // Cuando el WS no esta en linea o error en la consulta
                    fn_cargando(0);
                    fn_crearCliente(documento);
                    $("#estadoWS").val(0);
                }
            },
            error: function() { // Cuando el WS no esta en linea 
                fn_cargando(0);
                fn_crearCliente(documento);
                $("#estadoWS").val(0);
            }
        });
        $("#hid_bandera_teclado").val(1);
    
}
/* Consumo Web Service */
function fn_documentoPasaporte(documento, valid) {
    if ($("#txtClienteCI").val() != '') {
        var consumidor_final = '9999999999';        

        if ($("#txtClienteCI").val() == consumidor_final
            || $("#txtClienteCI").val() == consumidor_final + '9'
            || $("#txtClienteCI").val() == consumidor_final + '99'
            || $("#txtClienteCI").val() == consumidor_final + '999' ) {
            alertify.alert("Debe ingresar factura con datos. No puede ser consumidor final. <br><br>");
        
            fn_alfaNumericoo(txtClienteCI);
            
            $("#btnClienteConfirmarDatos").hide();
            $("#btnClienteGuardar").hide();
            $("#btnClienteGuardarActualiza").hide();
            $("#btnClienteConfirmarDatosFacturar").hide();            
            $("#btnConsumidorFinal").show();
        } else { 
            console.log('avan')
            aplicaPlugThem();
            $("#hid_conDatos").val(1);
            fn_cargando(1);
            fn_limpiarCamposCliente();
            var send;
            var tipoDocumento = "PASAPORTE";
            send = {};
            send.metodo = "buscarCliente";
            send.documento = documento;
            send.tipoDocumento = tipoDocumento;
            $.ajax({
                async: false,
                type: "POST",
                dataType: "json",
                "Accept": "application/json, text/javascript2",
                contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
                url: "../facturacion/clienteWSClientes.php",
                data: send,
                success: function(datos) {
                    console.log('datos de cliente con passport')
                    console.log(datos)
                    if (datos.hasOwnProperty("cliente")) {
                        var cliente = datos.cliente;
                        $("#clienteEstado").val(datos.estado);
                        if (datos.estado === 1) {
                            console.log('1') // El cliente no existe en el local pero si existe en Azur                      
                            fn_cargando(0);
                            $("#estadoWS").val(1);
                            fn_cargaDatosCliente(2, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, datos.estado, cliente.tipo_documento, valid);
                        } else if (datos.estado === 2) { //El cliente no existe Azur ni en el local
                            fn_cargando(0);
                            var cliente = datos.cliente;
                            $("#clienteAutorizacion").val(cliente.autorizacion);
                            $("#estadoWS").val(1);
                            fn_crearCliente(documento);
                        } else if (datos.estado === 3) { 
                            console.log('3')
                            // El cliente existe en el local                       
                            fn_cargando(0);
                            $("#estadoWS").val(1);
                            fn_cargaDatosCliente(2, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, datos.estado, cliente.tipo_documento, valid);
                        }
                    } else {
                        fn_cargando(0);
                        fn_crearCliente(documento, valid);
                        $("#estadoWS").val(0);
                    }
                },
                error: function() { // Cuando el WS no esta en linea 
                    fn_cargando(0);
                    fn_crearCliente(documento, valid);
                    $("#estadoWS").val(0);
                }
            });
        }
    } else {
        alertify.alert("Documento de identificaci&oacute;n obligatorio. <br><br>");
        fn_alfaNumericoo(txtClienteCI);
    }
}

/* Carga los datos de cliente */
function fn_cargaDatosCliente(tipoDocumento, IDCliente, identificacion, descripcion, direccionDomicilio, telefonoDomiclio, correo, tipoCliente, estado, tipoDocumentoCliente, valid) {
    $("#txtClienteId").val(IDCliente);
    $("#txtClienteCI").val(identificacion);

    $("#txtClienteNombre").val(descripcion || $("#txtClienteNombre").val());
    $("#txtClienteFono").val((localStorage.getItem("ls_telefono"+cuenta) || telefonoDomiclio) || $("#txtClienteFono").val());
    $("#txtCorreo").val(correo || $("#txtCorreo").val());

    //$("#btnClienteGuardar").hide();
    //SI LA POLITICA DE VALIDACIÓN DE EMAIL ESTA ACTIVA, VALIDA EL EMAIL
    //1 = ACTIVA
    //0= NO ACTIVA
    if(valid != 0){

        if(localStorage.getItem('intValidaFC') === undefined || localStorage.getItem('intValidaFC') === null){
            console.log('no existe la config, se procede a cargarla')
            localStorage.setItem('intValida', valid);
            localStorage.setItem('intValidaFC', valid);
            localStorage.setItem('intValidaOP', valid);
        }
    }

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

    // Clientes tipo RELACIONADOS O EXTERNOS, sus datos no pueden ser modificados 
    if (clienteTipo == 1) {
        $("#btnClienteGuardar").hide();
        $("#btnClienteConfirmarDatosFacturar").show();
        $("#btnClienteConfirmarDatos").hide();
    } else {
        $("#btnClienteConfirmarDatos").show();
        $("#btnClienteConfirmarDatosFacturar").hide();
    }

    // 1 = Cédula O RUC, 2 = Pasaporte
    if (tipoDocumento === 1) {
      //  fn_btnOk(numPadCliente); //ocultar teclado al presionar buscar cliente txtPad   
    }

    $("#txtClienteNombre").focus();
    $("#nombres_obligatorios").show();
    $("#direccion_obligatorios").show();

    // CORRECCION TEMPORAL FIDELIZACION CON FOOD CLUB /////////////////////////////////////
    if ($("#btnFormaPagoId").val() === "E00A9503-85CF-E511-80C6-000D3A3261F3") { //TARJETAS
        $("#btnConsumidorFinal").show();
    } else {
        $("#btnConsumidorFinal").hide();
    }
    ///////////////////////////////////////////////////////////////////////////////////////

    if ($("#hide_ordenKiosko").val() === '1' && identificacion === '9999999999999') {
        $("#btnConsumidorFinal").show();
        $("#btnClienteConfirmarDatos").hide();
    }

    if(($("#hide_ordenKiosko").val() === '1' && identificacion !== '') || ($("#hide_pickupActivo").val() === '1' && identificacion !== ''))
    {
        clientetipodocumentokiosko = tipoDocumentoCliente;
        fn_btnOk(numPadCliente);
        $("#btnClienteConfirmarDatos").show();
    }
}

/* Oculta los teclados cuando el documento es incorrecto */
function fn_documentoInvalido() {
    alertify.alert("N\u00famero de documento no v\u00e1lido.");
    fn_limpiarCamposCliente();
    fn_bloquearIngreso();
    fn_numerico(txtClienteCI);
    $("#txtClienteCI").focus();
    $("#btnClienteGuardar").hide();
    $("#btnClienteGuardarActualiza").hide();
    $("#keyboardCliente").hide();
    $("#nombres_obligatorios").hide();
    $("#direccion_obligatorios").hide();
    $("#btnClienteConfirmarDatos").hide();
    $("#btnConsumidorFinal").show();
}

function fn_EmailInvalido() {
    alertify.alert("El email ingresado no es v\u00e1lido. Por favor vuelva a verificar");
    $("#txtCorreo").focus();
    $("#btnClienteGuardar").hide();
    $("#btnClienteGuardarActualiza").hide();
    $("#nombres_obligatorios").hide();
    $("#direccion_obligatorios").hide();
    $("#btnClienteConfirmarDatos").show();
}

/* Muestra los campos obligatorios */
function fn_crearCliente(documento, valid) {
    fn_clienteNuevo(documento, valid);
    $("#txtClienteNombre").focus();
    $("#nombres_obligatorios").show();
    $("#direccion_obligatorios").show();
}

/* Limpia los campos */
function fn_limpiarCamposCliente() {
    $("#txtClienteNombre").val("");
 //   $("#txtClienteDireccion").val("");
    $("#txtClienteFono").val("");
    $("#txtCorreo").val("");
}

function fn_limpiarInfo() {
    $("#txtClienteNombre").val("");
    $("#txtClienteApellido").val("");
 //   $("#txtClienteDireccion").val("");
    $("#txtClienteCiudad").val("");
    $("#txtClienteFono").val("");
    $("#txtCorreo").val("");
}

/* Valida cliente repetido */
function fn_clienteRepetido() {
    if ($("#rdo_ruc").hasClass("btnRucCiActivo")) {
        if (fn_validarDocumento($("#txtClienteCI").val())) {
            fn_consultaClienteRepetido();
        } else {
            alertify.alert("N\u00famero de documento no v\u00e1lido.");
            $("#txtClienteCI").focus();
            $("#keyboardCliente").hide();
        }
    } else {
        fn_consultaClienteRepetido();
    }
}

function fn_consultaClienteRepetido() {
    var send;
    var valor = "Inicial";
    send = {
        "clienteRepetido": 1
    };
    send.CedulaRepetido = $("#txtClienteCI").val();
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_clientes.php",
        data: send,
        success: function(datos) {
            if (datos.existe == 1) {
                CLIENTE_REPETIDO = 1;
            } else {
                CLIENTE_REPETIDO = 0;
            }
        }
    });
}

//////////////////////////////////CERRAR VENTANA////////////////////////////////////////////////
function fn_cerrar() {
    $("#ventanaVisuliza").hide({
        autoOpen: false,
        hide: {
            effect: "explode",
            duration: 500
        }
    });
}

///////////////////////////////////////////REDIRECCIONAR PAGINA///////////////////////////////
function redireccionar(pagina) {
    location.href = pagina;
}

//////////////////////////////////////////BUSCAR CLIENTES/////////////////////////////////
function fn_consultarCliente() {
    $("#txtClienteCI").autocomplete({
        source: "config_clientes.php?clienteBuscar",
        minLength: 2,
        select: function(event, ui) {
            $("#txtClienteCI").focus();
            $(".ui-helper-hidden-accessible").hide();
        },
        create: function(event, ui) {}
    });
}

///////////////////////////////////////////DATOS CONSUMIDOR FINAL////////////////////////
function fn_consumidorFinal() {
    $("#hid_conDatos").val(0);
    fn_cargando(1);

    var send;
    var cambio = calcularCambio();
    var pagadoesta = $("#pagoGranTotal").val();
    send = {
        "consultabasefactura": 1
    };
    send.valorpagadoo = pagadoesta;
    $.getJSON("config_clientes.php", send, function(datos) {
        var base = datos.pais_base_factura;
        if (parseFloat(pagadoesta) >= parseFloat(base)) {
            alertify.alert("Debe ingresar factura con datos. No puede ser consumidor final. <br><br>");
            fn_cargando(0);
            return false;
        } else {
            notificarCambio(cambio);
            $("#txtClienteCI").val(datos.Documento);
            fn_limpiarCamposCliente();
            $("#txtClienteNombre").val(datos.Cliente);
            fn_bloquearIngreso();
            $("#dominio1").css({
                display: "none",
                position: "absolute"
            });
            $("#dominio2").css({
                display: "none",
                position: "absolute"
            });
            fn_btnOk(numPadCliente);
            fn_ocultar_alfanumerico();
            
            if (BANDERA_AGREGADOR === 1) {           
                //  Imprimir orden
                var send = { imprimirOrden: 1 };
                send.odpOrden = $("#txtOrdenPedidoId").val();
                send.dop_id = $("#txtNumCuenta").val();
                send.imprimeTodas = 0;
                send.guardarOrden = 1;
                send.dop_cuenta = $("#txtNumCuenta").val();

                var apiImpresion = getConfiguracionesApiImpresion();
                if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                    fn_cargando(1);
            
                    var result = new apiServicioImpresion('orden_pedido', null, send.odpOrden, send);
                    var imprime = result["imprime"];
                    var mensaje = result["mensaje"];
            
                    console.log('imprime: ', imprime);
            
                    if (!imprime) {
                        alertify.success('Imprimiendo Orden de pedido...');
                        fn_imprimirPromocionFactura(send.odpOrden, send.dop_id)
                        fn_cargando(0);
                        
            
                    } else {
                    
                        alertify.success('Error al imprimir...');
                        fn_cargando(0);
            
                    }
                }

                fn_clienteConsumirFinal();
                enviarTransaccionQPM();


               
            } else {
                fn_printFactura();
                fn_cargando(0);
            }
        }
    });
}

///////////////////////////////////////////LIMPIAR DATOS////////////////////////////////
function fn_bloquearIngreso() {
    $("#txtClienteNombre").attr("disabled", "-1");
    $("#txtClienteApellido").attr("disabled", "-1");
//    $("#txtClienteDireccion").attr("disabled", "-1");
    $("#txtClienteFono").attr("disabled", "-1");
    $("#txtCorreo").attr("disabled", "-1");
}

///////////////////////////////////////////LIMPIAR DATOS////////////////////////////////
function fn_habilitarIngreso() {
    $("#txtClienteNombre").removeAttr("disabled");
 //   $("#txtClienteDireccion").removeAttr("disabled");
    $("#txtClienteFono").removeAttr("disabled");
    $("#txtCorreo").removeAttr("disabled");
}

///////////////////////////////////////////DATOS CONSUMIDOR FINAL////////////////////////
function fn_clienteNuevo(ci) {
    if (clienteexisteenmasterdatacliente == 0) {
        fn_limpiarCamposCliente();
    }
    $("#txtClienteNombre").focus();
    $("#numPadCliente").hide();
    fn_alfaNumerico_letrass(txtClienteNombre);
    $("#txtClienteCI").removeAttr("disabled");
    $("#txtClienteCI").val(ci);
    $("#btnConsumidorFinal").hide();
    fn_habilitarIngreso();
    $("#lupaCliente").show();
    $("#btnClienteGuardar").show();
    $("#btnClienteGuardarActualiza").hide();
    $("#btnClienteConfirmarDatos").hide();
    //$("#txtClienteCI").attr("disabled", "-1"); 
    $("#rdo_ruc").attr("disabled", "-1");
    $("#rdo_pasaporte").attr("disabled", "-1");
}

function fn_habilitarCamposCliente(documento) {
    $("#txtClienteCI").val(documento);
    fn_limpiarCamposCliente();
    fn_habilitarIngreso();
    fn_alfaNumerico_letrass(txtClienteNombre);
    $("#btnConsumidorFinal").hide();
    $("#btnClienteGuardar").show();
    $("#btnClienteGuardarActualiza").hide();
}

//////////////////////////////////////////CANCELAR CAMBIOS///////////////////////////////
function fn_clienteCancelar() {
    $("#txtClienteCI").val("");
    fn_limpiarCamposCliente();
    fn_bloquearIngreso();
    location.reload();
}

/* Verifica que los campos sean llenados */
function fn_clienteGuardar(valid) {
    console.log('fn_clienteGuardar valid')
    expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    var arr_e = [];
    var datos_existentes = localStorage.getItem('email_valid');
    datos_existentes = datos_existentes == null ? [] : JSON.parse(datos_existentes);
    arr_e = localStorage.getItem('email_valid');
    var email = $("#txtCorreo").val();

    if(valid != 0 && localStorage.getItem("intValidaFC") > 0 && expr.test(email)){

        send = {"cargarUrlApiValidaPlugthem": 1, "CustomerEmail":  email};
        $.ajax({type: "POST", dataType: 'json', url: "../ordenpedido/config_ordenPedido.php", data: send,})
            .fail((jqXHR, textStatus, errorThrown) => {
            console.log('error');
        }).done((data, textStatus, jqXHR)=>{
            validateBackgroundEmailPlugThem(data, email, datos_existentes, arr_e);
        }).always(()=>{
            continuarProcesoC(valid);
        });

    } else {
        console.log('continua proceso')
        localStorage.setItem("intValidaFC", localStorage.getItem("intValida"));
        continuarProcesoC(valid);
    }

}

function validarCamposCliente() {
    if ($("#txtClienteCI").val().trim() === "") {
        alertify.error("Documento de identificaci&oacute;n obligatorio.");
        $(".alertify-logs").css("top", 300 + "px");
        $("#txtClienteCI").focus();
        console.log('Error documento identificacion')
        return false;
    }

    if ($("#txtClienteNombre").val().trim() === "") {
        $("#keyboardCliente").hide();
        alertify.error("Ingrese los nombres del Cliente.");
        $(".alertify-logs").css("top", 300 + "px");
        $("#txtClienteNombre").focus();
        fn_focoinput_nombres();
        console.log('Error nombre cliente')
        return false;
    }

    if ($("#txtClienteFono").val() !== "") {
        if (($("#txtClienteFono").val().length < 7) || ($("#txtClienteFono").val().length > 10)) {
            alertify.error("Ingrese un n&uacute;mero de tel&eacute;fono v\u00e1lido.");
            $(".alertify-logs").css("top", 300 + "px");
            $("#txtClienteFono").focus();
            fn_focoinput_telefono();
            return false;
        }
    }

    if (!fn_validarEmail($("#txtCorreo").val())) {
        alertify.error("Ingrese un correo v\u00e1lido..");
        $(".alertify-logs").css("top", 300 + "px");
        fn_focoinput_email();
        return false;
    }

    return true;
}

function continuarProcesoC(valid){

        var cambio = calcularCambio();
        if ($("#hid_pasaporte").val() == 1) {
            tipoDocumento = "PASAPORTE";
    
            var consumidor_final = '9999999999'; 
            
            if ($("#txtClienteCI").val() == consumidor_final
                || $("#txtClienteCI").val() == consumidor_final + '9'
                || $("#txtClienteCI").val() == consumidor_final + '99'
                || $("#txtClienteCI").val() == consumidor_final + '999') {
                alertify.alert("Debe ingresar factura con datos. No puede ser consumidor final. <br><br>");
            
                fn_alfaNumericoo(txtClienteCI);
                
                $("#btnClienteConfirmarDatos").hide();
                $("#btnClienteGuardar").hide();
                $("#btnClienteGuardarActualiza").hide();
                $("#btnClienteConfirmarDatosFacturar").hide();            
                $("#btnConsumidorFinal").show();
            }
            console.log('ooo')
        }
    
        if(!validarCamposCliente()){
            console.log('retorna false')
            return false;
        }

        console.log('a ingresar cliente')
        fn_ingresoCliente(cambio, valid);
}

/* Verifica que el cliente no exista */
function fn_ingresoCliente(cambio, valid) {

    // fn_clienteRepetido();
    if (CLIENTE_REPETIDO == 0) {

        if(!validarCamposCliente()){
            console.log('retorna false')
            return false;
        }

        fn_nuevoCliente(cambio, valid);
    } else {
        alertify.error("Ya existe un cliente con este documento de identificaci\u00f3n");
        $(".alertify-logs").css("top", 300 + "px");
    }
}

/* Registro de clientes en la DB del local */
function fn_nuevoCliente(cambio, valid) {
    fn_cargando(1);
    var send;
    var Accion = 'I';
    var tipoDocumento;
    var estado = $("#clienteEstado").val();
    if ($("#txtClienteCI").val().length == 10) {
        tipoDocumento = "CEDULA";
    } else if ($("#txtClienteCI").val().length == 13) {
        tipoDocumento = "RUC";
    }

    var datamdcsave = {};

    if ($("#hid_pasaporte").val() == 1) {
        tipoDocumento = "PASAPORTE";

        var consumidor_final = '9999999999';

        if ($("#txtClienteCI").val() == consumidor_final
            || $("#txtClienteCI").val() == consumidor_final + '9'
            || $("#txtClienteCI").val() == consumidor_final + '99'
            || $("#txtClienteCI").val() == consumidor_final + '999') {
            alertify.alert("Debe ingresar factura con datos. No puede ser consumidor final. <br><br>");
        
            fn_alfaNumericoo(txtClienteCI);
            
            $("#btnClienteConfirmarDatos").hide();
            $("#btnClienteGuardar").hide();
            $("#btnClienteGuardarActualiza").hide();
            $("#btnClienteConfirmarDatosFacturar").hide();            
            $("#btnConsumidorFinal").show();
        }
    }

    send = {
        "nuevoCliente": 1
    };
    send.accion = Accion;
    send.clienteTipoDoc = tipoDocumento;
    send.clienteDocumento = $("#txtClienteCI").val();
    send.clienteDescripcion = $("#txtClienteNombre").val();
   // send.clienteDireccion = $("#txtClienteDireccion").val();
    send.clienteFono = $("#txtClienteFono").val();
    send.clienteCorreo = $("#txtCorreo").val();
    send.usuario = $('#idUser').val();
    send.estadoWS = $("#estadoWS").val();
    send.tipoCliente = "NULL";
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_clientes.php",
        data: send,
        success: function(datos) {
            notificarCambio(cambio);
            $(".alertify-logs").css("top", 70 + "px");
            $("#btnConsumidorFinal").hide();
            $("#btnClienteGuardar").hide();
            $("#dominio1").hide();
            $("#dominio2").hide();
            fn_bloquearIngreso();
            fn_btnOk(numPadCliente);
            fn_ocultar_alfanumerico();

            datamdcsave.cdn_id = $("#txtCadenaId").val();
            datamdcsave.documento = send.clienteDocumento;
            datamdcsave.tipoDocumento = tipoDocumento;
            datamdcsave.email = send.clienteCorreo;
            datamdcsave.telefono = send.clienteFono;
            datamdcsave.primerNombre = send.clienteDescripcion;
            datamdcsave.direccion = '';
            datamdcsave.idUserPos = $('#idUser').val();
            datamdcsave.rst_id = $('#txtRestaurante').val();
            var response = fn_masterdatacliente("GUARDAR", null, null, datamdcsave, valid);
            if (response.codeError === "GKFC002") { //Si el cliente existe en la Master Data Clientes entonces se modifican los datos
                fn_masterdatacliente("MODIFICAR", null, null, datamdcsave);
            }
            
            //if (banderaUber === 1) {
            if (BANDERA_AGREGADOR === 1) {
                var cedulaC = $("#txtClienteCI").val();
                var nombreC = $("#txtClienteNombre").val();
     //           var direccionC = $("#txtClienteDireccion").val();
                var telefonoC = $("#txtClienteFono").val();
                var correoC = $("#txtCorreo").val();
                fn_selecionaClienteAx(nombreC, cedulaC, cedulaC, telefonoC, null, correoC, 'NULL');
            } else {
                fn_printFactura();
                fn_cargando(0);
            }
        }
    });

    // Envio la informacion solo si el cliente no existe en el local ni en Azur
    if (estado == 2) {
        fn_envioDatosClienteWS(tipoDocumento, $("#txtClienteCI").val(), $("#txtClienteNombre").val(), $("#txtClienteDireccion").val(), $("#txtClienteFono").val(), $("#txtCorreo").val());
    }
}

/* Envio datos de cliente por Web Services */
function fn_envioDatosClienteWS(tipoDocumento, documento, descripcion, direccion, telefono, correo) {
    var send;
    var autorizacion = $("#clienteAutorizacion").val();
    send = {};
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
        success: function(datos) {
            //alert(JSON.stringify(datos));
        }
    });
}

function calcularCambio() {
    var cambio = parseFloat($("#hid_cambio").val()).toFixed(2);
    cambio = parseFloat(cambio * (parseFloat(-1)));
    cambio = parseFloat(cambio).toFixed(2);
    return cambio;
}

const ajaxRequest = (url, paramsRequired, type = 'GET') => {
    return $.ajax({
        type,
        url,
        data: paramsRequired,
        success: function (response) {
            return response;
        }
    });
  }

  function validarJSON(strJSON) {
    if (/^[\],:{}\s]*$/.test(strJSON.replace(/\\["\\\/bfnrtu]/g, '@').
        replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
        replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
        return true;
    }
    return false;
  }

/* Finaliza la transacción */
function fn_clienteGuardarActualiza(valid) {
    var cambio = calcularCambio();

    if ($("#rdo_ruc").hasClass("btnRucCiActivo")) {
        if(localStorage.getItem("invalido")==0){
            if (fn_validarDocumento($("#txtClienteCI").val())) {
                fn_actualizaCliente(cambio, valid);
            } else {
                alertify.error("N&uacute;mero de documento no v&aacute;lido");
                $(".alertify-logs").css("top", 300 + "px");
            }
        }else{
            fn_actualizaCliente(cambio, valid);
        }
    } else {
        fn_actualizaCliente(cambio, valid);
    }
}

function fn_crearOrdenBringg(){

    var cambio_estados_automatico = $("#cambio_estados_automatico").val();

  //  print('crear orden bringg')
  //  print(cambio_estados_automatico);
  
    if(cambio_estados_automatico && cambio_estados_automatico == 1){
  
      var $url_bringg =  $("#url_bringg_crear").val();
      var codigo = $("#txtNumFactura").val();

      console.log('URL BRINGG');
      console.log($("#url_bringg_crear").val());
      console.log('NUMERO DE FACTURA');
      console.log($("#txtNumFactura").val());



      send = { metodo: "cambioEstadoBringg" };
      send.idFactura  = codigo;
      send.idApp      = codigo;
      send.url        = $url_bringg;

      $.ajax({
          async: false,
          type: "POST",
          dataType: 'json',
          contentType: "application/x-www-form-urlencoded",
          url: "../ordenpedido/config_app.php",
          data: send,
          success: function( datos ) {
              //RESPUESTA DESDE BRINGG
              console.log('Respuesta Bringg');
              console.log(datos);   
          },
          error: function (jqXHR, textStatus, errorThrown) {
            console.error(jqXHR);
            console.error(textStatus);
            console.error(errorThrown);
          },
      
      });
  
    }
  
}
function crearOrdenDunaAjaxClientes() {
  let cambioEstadosAutomatico = $("#cambio_estados_automatico").val();

  if (cambioEstadosAutomatico == 1 || cambioEstadosAutomatico === "SI") {
    let parametrosObtenerUrl = {
      metodo: "obtenerUrlCrearPedidoDuna",
      rst_id: $("#txtRestaurante").val(),
    };

    $.ajax({
      async: false,
      type: "POST",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded",
      url: "../ordenpedido/config_app.php",
      data: parametrosObtenerUrl,
      success: function (datosUrlIngresoDuna) {
        let url = datosUrlIngresoDuna[0]?.url.includes("https://")
          ? datosUrlIngresoDuna[0]?.url.replace("http://", "")
          : datosUrlIngresoDuna[0]?.url;

        let cfac_id = $("#txtNumFactura").val();

        let parametrosCrearOrdenDuna = {
          metodo: "crearOrdenDuna",
          idFactura: cfac_id,
          idApp: cfac_id,
          url,
        };

        if (parametrosCrearOrdenDuna.url) {
          $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../ordenpedido/config_app.php",
            data: parametrosCrearOrdenDuna,
            success: function (respuestaDuna) {
                console.log("enviado a D-UNA");
            },
          });
        }
      },
    });
  }
}

/* Finaliza la transaccion */
async function fn_actualizaCliente(cambio, valid) {
    console.log('actualiza cliente')
    console.log(cambio);
    fn_cargando(1);
    $('#btnConsumidorFinal').hide();
    $('#btnClienteGuardarActualiza').hide();
    fn_bloquearIngreso();
    fn_btnOk(numPadCliente);
    fn_ocultar_alfanumerico();
    notificarCambio(cambio);
    $('.alertify-logs').css('top', 300 + 'px');

    /**
     * Se guarda la session en una variable y se coloca en formato json
    */
    let dataSessionCliente = JSON.parse(sessionStorage.getItem("DatosCliente"));

    let dataCliente = {
        descripcion: dataSessionCliente.cliente.descripcion,
        direccion: dataSessionCliente.cliente.direccionDomicilio,
        telefono: dataSessionCliente.cliente.telefonoDomiclio,
        email: dataSessionCliente.cliente.correo
    }

    /**
     * Obtiene datos de input formulario
    */
    let cedulaC = $("#txtClienteCI").val();
    let nombreC = $("#txtClienteNombre").val();
    let direccionC = $("#txtClienteDireccion").val();
    let telefonoC = $("#txtClienteFono").val();
    let correoC = $("#txtCorreo").val();

    var beneficio = '';
    var acepta_beneficio = localStorage.getItem("dop_beneficio"+cuenta);
    if (acepta_beneficio != null){
        beneficio = await fn_aceptaBeneficioCliente();
    }else{
        beneficio = 1;
    }

    if (beneficio == 1){
        //if (banderaUber === 1) {
        if (BANDERA_AGREGADOR === 1) {
            fn_selecionaClienteAx(nombreC, cedulaC, cedulaC, telefonoC, null, correoC, 'NULL');
        } else if ($('#hide_ordenKiosko').val() === '1' || $('#hide_pickupActivo').val() === '1') {
            fn_actualizarRegistroCliente(valid);
        } else {
            /**
             * Validación para actualizar el cliente con el json que se trae de la variable de sesión
             */
            if (dataSessionCliente != "" || dataSessionCliente != null) {
                if (nombreC != dataCliente.descripcion || direccionC != dataCliente.direccion || telefonoC != dataCliente.telefono || correoC != dataCliente.email) {
                    /**
                     * Funcion para actualizar los datos de cliente
                     */
                    fn_actualizarRegistroCliente(valid);
                }
            }

            if (!facturaImpresa) {
                fn_printFactura();
                fn_cargando(0);
                facturaImpresa = true;
            }

            /**
             * Remover la session de datos cliente
            */
            sessionStorage.removeItem("DatosCliente");
        }
    }
}

/* Valida email */
function fn_validarEmail() {
    var evaluar = $("#txtCorreo").val();
    evaluar = evaluar.trim();
    var filter = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

    if (evaluar.length == 0)
        return true;
    if (filter.test(evaluar))
        return true;
    else {
        $("#txtCorreo").focus();
        $("#keyboardCliente").hide();
        $("#dominio1").hide();
        $("#dominio2").hide();
        return false;
    }
}

function fn_solicitaCredencialesAdministrador() {
    $("#btnClienteConfirmarDatos").hide();
    $("#btnConsumidorFinal").show(); 

    
    var send;
    send = {
        "solicitaCredencialesAdministrador": 1
    };
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_clientes.php",
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
            } else if (datos.solicita === 2 && localStorage.getItem("invalido") ==1) {

                
                $('#btnClienteConfirmarDatos').hide();
                $('#btnClienteGuardar').hide();
                fn_abremodalAdministradorCedula();
         
                $('#btnConsumidorFinal').show();

            } 
            else {
                fn_abremodalAdministradorPasporte(); 
            }
        }
    });
}

function fn_auditoria_ruc() {
    send = { metodo: "auditoria" };
    send.mensaje = 'NO' ;
    $.ajax({
      async: true,
      type: "POST",
      dataType: 'json',
      contentType: "application/x-www-form-urlencoded",
      url: "../facturacion/config_auditoria.php",
      data: send,
      success: function( datos ) {

        console.log('CAMBIO ESTADOS');
        console.log(datos);

        if(datos && datos[0]){   

          $("#cambio_estados_automatico").val(datos[0].automatico);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      }
    });
  }
  
function fn_abremodalAdministradorPasporte() {
    $("#rdo_ruc").removeClass('btnRucCiActivo');
    $("#rdo_ruc").addClass('btnRucCiInactivo');
    $("#rdo_pasaporte").removeClass('btnRucCiInactivo');
    $("#rdo_pasaporte").addClass('btnRucCiActivo');
    $("#txtClienteCI").val('');
    fn_limpiarCamposCliente();
    $("#txtClienteCI").focus();
    fn_numerico(txtClienteCI);
    $("#div_adminPasaporte").show();
    $("#txt_passPasaporte").val('');
    $("#rdo_pasaporte").attr("checked", true);
    $("#div_adminPasaporte").dialog({
        modal: true,
        width: 500,
        heigth: 500,
        resize: false,
        opacity: 0,
        show: "explode",
        hide: "explode",
        duration: 5000,
        position: "center",
        open: function(event, ui) {}
    });

    fn_ocultar_alfanumerico();
    fn_btnOk(numPadCliente);
}
function fn_abremodalAdministradorCedula() {
    fn_btnOk(keyboardCliente, txtClienteCI);
    $("#rdo_ruc").removeClass('btnRucCiInactivo');
    $("#rdo_ruc").addClass('btnRucCiActivo');
    $("#rdo_pasaporte").removeClass('btnRucCiActivo');
    $("#rdo_pasaporte").addClass('btnRucCiInactivo');
  //  $("#txtClienteCI").val('');
    fn_limpiarCamposCliente();
    $("#txtClienteCI").focus();
    //fn_numerico(txtClienteCI);
    $("#div_adminPasaporte").show();
    $("#txt_passPasaporte").val('');
    $("#rdo_pasaporte").attr("checked", true);
    $("#div_adminPasaporte").dialog({
        modal: true,
        width: 500,
        heigth: 500,
        resize: false,
        opacity: 0,
        show: "explode",
        hide: "explode",
        duration: 5000,
        position: "center",
        open: function(event, ui) {}
    });
    $('#btnClienteGuardar').hide();

    $('#btnClienteConfirmarDatos').hide();
    $('#btnConsumidorFinal').show();

    fn_ocultar_alfanumerico();
    fn_btnOk(numPadCliente);
}

function fn_agregarNumeroPasaporte(valor) {
    var lc_cantidad = document.getElementById("txt_passPasaporte").value;
    var coma2;
    if (lc_cantidad == 0 && valor == ".") {
        //si escribimos una coma al principio del n�mero
        document.getElementById("txt_passPasaporte").value = "0."; //escribimos 0.
        coma2 = 1;
    } else {
        //continuar escribiendo un n�mero
        if (valor == "." && coma2 == 0) {
            //si escribimos una coma decimal p�r primera vez
            lc_cantidad = lc_cantidad + valor;
            document.getElementById("txt_passPasaporte").value = lc_cantidad;
            coma2 = 1; //cambiar el estado de la coma  
        }
        //si intentamos escribir una segunda coma decimal no realiza ninguna acci�n.
        else if (valor == "." && coma2 == 1) {}
        //Resto de casos: escribir un n�mero del 0 al 9:    
        else {
            $("#txt_passPasaporte").val('');
            lc_cantidad = lc_cantidad + valor;
            document.getElementById("txt_passPasaporte").value = lc_cantidad;
        }
    }
    fn_focusLectorPasaporte();
}

function fn_focusLectorPasaporte() {
    $("#txt_passPasaporte").focus();
}

function fn_eliminarCantidadPasaporte() {
    var lc_cantidad = document.getElementById("txt_passPasaporte").value.substring(0, document.getElementById("txt_passPasaporte").value.length - 1);
    var coma2;

    if (lc_cantidad == "") {
        lc_cantidad = "";
        coma2 = 0;
    }
    if (lc_cantidad == ".") {
        coma2 = 0;
    }
    document.getElementById("txt_passPasaporte").value = lc_cantidad;
    fn_focusLectorPasaporte();
}

function fn_canPasaporte() {
    localStorage.setItem("invalido",0);
    $("#hid_bandera_teclado").val(1);
    $("#div_adminPasaporte").dialog("destroy");
    $("#rdo_ruc").removeClass("btnRucCiInactivo");
    $("#rdo_ruc").addClass("btnRucCiActivo");
    $("#rdo_pasaporte").removeClass("btnRucCiActivo");
    $("#rdo_pasaporte").addClass("btnRucCiInactivo");
    $("#txtClienteCI").focus();
    fn_numerico(txtClienteCI);
    fn_ocultar_alfanumerico();
}

function fn_okPasaporte() {
    if ($("#txt_passPasaporte").val() == "") {
        alertify.alert("Ingrese una clave.");
        return false;
    }

    var send;
    send = {
        "validarUsuarioCreditoSinCupon": 1
    };
    send.usr_claveSinCupon = $("#txt_passPasaporte").val();
    $.getJSON("config_facturacion.php", send, function(datos) {
        if (datos.admini == 1) {
            $("#hid_bandera_teclado").val(2);
            $("#div_adminPasaporte").dialog("destroy");
            fn_alfaNumericoo(txtClienteCI);
            fn_bloquearIngreso();
            $("#txtClienteCI").focus();
            $("#nombres_obligatorios").show();
            $("#direccion_obligatorios").show();
            if(localStorage.getItem("invalido")==1)
            fn_clienteBuscar(false, 0)
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

function fn_focoinput_nombres() {
    $("#txtClienteNombre").focus();
    fn_alfaNumerico_letrass(txtClienteNombre);
}
/*
function fn_focoinput_direccion() {
    $("#txtClienteDireccion").focus();
    fn_alfaNumericoo(txtClienteDireccion);
}
*/
function fn_focoinput_telefono() {
    $("#txtClienteFono").focus();
    fn_alfaNumerico_numeross(txtClienteFono);
}

function fn_focoinput_email() {
    $("#txtCorreo").focus();
    fn_alfaNumericoo(txtCorreo);
}

function fn_cargando(estado) {
    if (estado) {
        $("#mdl_rdn_pdd_crgnd").css("display", "block");
        $("#mdl_pcn_rdn_pdd_crgnd").css("display", "block");
    } else {
        $("#mdl_rdn_pdd_crgnd").css("display", "none");
        $("#mdl_pcn_rdn_pdd_crgnd").css("display", "none");
    }
}

/* Se confirma si los datos del cliente son correctos -si no es pedido de kiosko o pickup-
 * de ser "SI", se realiza el cierre de la facturación.
 * de ser "NO", se obtiene del Azure los datos del cliente por WS. */
function fn_confirmarDatos(valid) {

    var email = '';
    email = $("#txtCorreo").val();
    expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if ( !expr.test(email) ){
        console.log('el email es incorrecto')
        Toastify({
            text: "El email es invalido",
            className: "info",
            style: {
                background: "linear-gradient(to right, #00b09b, #96c93d)",
            }
        }).showToast();
    }

    //expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    var arr_e = [];
    var datos_existentes = localStorage.getItem('email_valid');
    datos_existentes = datos_existentes == null ? [] : JSON.parse(datos_existentes);
    arr_e = localStorage.getItem('email_valid');

    if(valid != 0 && localStorage.getItem("intValidaFC") > 0 && expr.test($("#txtCorreo").val())){
        send = {"cargarUrlApiValidaPlugthem": 1, "CustomerEmail": $("#txtCorreo").val()};
        $.ajax({type: "POST", dataType: 'json', url: "../ordenpedido/config_ordenPedido.php", data: send,})
            .fail((jqXHR, textStatus, errorThrown) => {
            console.log('error');
        }).done((data, textStatus, jqXHR)=>{
            validateBackgroundEmailPlugThem(data, email, datos_existentes, arr_e);
        }).always(()=>{
            continuarProceso(valid);
        });
    } else {
        localStorage.setItem("intValidaFC", localStorage.getItem("intValida"));
        continuarProceso(valid);
    }
}

function continuarProceso(valid){

    if ($('#hide_ordenKiosko').val() === '1' || $('#hide_pickupActivo').val() === '1') {
        guardaActualizaCliente(valid);
    } else {
        alertify.set({
            labels: {
                ok: 'SI',
                cancel: 'NO',
            },
        });

        //if ($('#txtClienteNombre').val().trim() === '' || $('#txtClienteDireccion').val().trim() === '') {

        if ($('#txtClienteNombre').val().trim() === '') {    
            alertify.error("Completar la información obligatoria del cliente");
            // Timeout mínimo para permitirle al mensaje desplegarse antes de empezar a cargar
            setTimeout(function() {
                habilitarDatosCliente();
            }, 55);
        } else {
            alertify.confirm('<h4>¿LOS DATOS DE CLIENTE SON CORRECTOS?</h4><br>', function (e) {
                if (e) {
                    if ($("#txtClienteNombre").val().trim() === '') {
                        $("#keyboardCliente").hide();
                        alertify.error("Ingrese los nombres del Cliente.");
                        $(".alertify-logs").css("top", 300 + "px");
                        $("#txtClienteNombre").focus();
                        fn_focoinput_nombres();
                        return false;
                    }
                    /*else if ($("#txtClienteDireccion").val() === '') {
                        $("#keyboardCliente").hide();
                        alertify.error("Ingrese la direcci&oacute;n del Cliente.");
                        $(".alertify-logs").css("top", 300 + "px");
                        $("#txtClienteDireccion").focus();
                    // fn_focoinput_direccion();
                        return false;
                    }*/ 
                    else if (fn_validarEmail($("#txtCorreo").val())) {
                        if ($("#txtClienteFono").val() !== "") {
                            if (($("#txtClienteFono").val().length < 7) || ($("#txtClienteFono").val().length > 10)) {
                                alertify.error("Ingrese un n&uacute;mero de tel&eacute;fono v\u00e1lido.");
                                $(".alertify-logs").css("top", 300 + "px");
                                $("#txtClienteFono").focus();
                                fn_focoinput_telefono();
                                return false;
                                // fn_btnOk(numPadCliente);
                                // fn_ocultar_alfanumerico();
                            }
                        }
                    } else {
                        alertify.error("Direcci\u00f3n de correo electr&oacute;nico no v\u00e1lida..");
                        $(".alertify-logs").css("top", 300 + "px");
                        $("#txtCorreo").focus();
                        fn_focoinput_email();
                        return false;
                    }
                    fn_clienteGuardarActualiza(valid);
                } else {
                    habilitarDatosCliente();
                }
            });
        }
    }

}



function cambioEstadoAutomaticoSinProveedorAjaxClientes() {
    let cambioEstadosAutomatico = $("#cambio_estados_automatico").val();
  
    if(cambioEstadosAutomatico == 1 || cambioEstadosAutomatico === 'SI' ) {
  
        let cfac_id = $("#txtNumFactura")?.val();
        let parametrosCambioEstadoSinProveedor = {
          metodo: "cambioEstadoAutomaticoSinProveedor",
          idFactura:cfac_id,
          idApp:cfac_id
        };
  
        if (cfac_id) {
            $.ajax({
                async: false,
                type: "POST",
                dataType: 'json',
                contentType: "application/x-www-form-urlencoded",
                url: "../ordenpedido/config_app.php",
                data: parametrosCambioEstadoSinProveedor,
                success: function( datos ) {
                    console.log('Respuesta cambio Estado Automatico Sin Proveedor');
                    console.log(datos);   
                },
                error: function (jqXHR, textStatus, errorThrown) {
                  console.error(jqXHR);
                  console.error(textStatus);
                  console.error(errorThrown);
                },
            });
        }
      }
}

function habilitarDatosCliente() {
    fn_obtenerDatosCliente();

    // CORRECCION TEMPORAL FIDELIZACION CON FOOD CLUB /////////////////////
    $('#btnConsumidorFinal').hide();
    ///////////////////////////////////////////////////////////////////////

    fn_habilitarIngreso();
    $('#btnClienteConfirmarDatos').hide();
    $('#btnClienteConfirmarDatosFacturar').show();
    fn_focoinput_nombres();
    $('#txtClienteCI').attr('disabled', '-1');
    $('#rdo_ruc').attr('disabled', '-1');
    $('#rdo_pasaporte').attr('disabled', '-1');
}

function fn_confirmarDatosFIDELIZACION() {
    fn_clienteGuardarActualiza();
}

function fn_validarConfirmacion() {
    if ($("#txtClienteCI").val() !== "") {
        if ($("#rdo_ruc").hasClass("btnRucCiActivo")) {
            if (fn_validarDocumento($("#txtClienteCI").val())) {
                fn_confirmarDatos();
            } else {
                alertify.alert("N\u00famero de documento no v\u00e1lido.");
                $("#txtClienteCI").focus();
                $("#keyboardCliente").hide();
            }
        } else {
            (fn_confirmarDatos)();
        }
    } else {
        alertify.error("Documento de identificaci&oacute;n obligatorio.");
    }
}

/* Obtenemos los datos del cliente por WS */
function fn_obtenerDatosCliente() {
    fn_cargando(1);
    var send;
    var documento = $("#txtClienteCI").val();
    var tipoDocumento;
    if ($("#txtClienteCI").val().length == 10) {
        tipoDocumento = "CEDULA";
    } else if ($("#txtClienteCI").val().length == 13) {
        tipoDocumento = "RUC";
    }
    if ($("#hid_pasaporte").val() == 1) {
        tipoDocumento = "PASAPORTE";
    }
    send = {};
    send.metodo = "obtenerDatosCliente";
    send.documento = documento;
    send.tipoDocumento = tipoDocumento;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        "Accept": "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../facturacion/clienteWSClientes.php",
        data: send,
        success: function(datos) {
            if (datos.hasOwnProperty("cliente")) {
                var cliente = datos.cliente;
                if (datos.estado === 1) { // Existe                      
                    if (cliente.tipoIdentificacion === "CEDULA") {
                        fn_cargando(0);
                        fn_cargaDatosCliente(1, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, datos.estado, cliente.tipo_documento);
                    } else if (cliente.tipoIdentificacion === "RUC") {
                        fn_cargando(0);
                        fn_cargaDatosCliente(1, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, datos.estado, cliente.tipo_documento);
                    } else {
                        fn_cargando(0);
                        fn_cargaDatosCliente(2, cliente.IDCliente, cliente.identificacion, cliente.descripcion, cliente.direccionDomicilio, cliente.telefonoDomiclio, cliente.correo, cliente.tipoCliente, datos.estado, cliente.tipo_documento);
                    }
                } else {
                    // Cuando el WS no esta en linea o error en la consulta
                    fn_cargando(0);
                }
            }
        },
        error: function() { // Cuando el WS no esta en linea 
            fn_cargando(0);
        }
    });
}

/* Actualiza los datos del cliente */
function fn_actualizarDatosCliente(cambio, valid) {
    fn_cargando(1);
    var send;
    var Accion = "U";
    var tipoDocumento = "0";
    var datamdcsave = {};
    send = {
        "actualizarDatosCliente": 1
    };
    send.accion = Accion;
    send.clienteTipoDoc = tipoDocumento;
    send.clienteDocumento = $("#txtClienteCI").val();
    send.clienteDescripcion = $("#txtClienteNombre").val();
    //send.clienteDireccion = $("#txtClienteDireccion").val();
    send.clienteFono = $("#txtClienteFono").val();
    send.clienteCorreo = $("#txtCorreo").val();
    send.usuario = $("#idUser").val();
    send.estadoWS = 1;
    send.tipoCliente = "NULL";
    
    var argvtipoDocumentoIdentidad;

    if ( $("#txtClienteCI").val().length == 10 ) 
    {
        argvtipoDocumentoIdentidad = "CEDULA";
    } 
    else if ( $("#txtClienteCI").val().length == 13 ) 
    {
        argvtipoDocumentoIdentidad = "RUC";
    }
    if ( $("#hid_pasaporte").val() == 1 ) 
    {
        argvtipoDocumentoIdentidad = "PASAPORTE";
    }

    send.argvTDI = argvtipoDocumentoIdentidad;

    info_cliente = fn_ObtenerCliente(send, $("#txtClienteCI").val().length, 'editar');
    fn_aceptaBeneficioCliente();

    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_clientes.php",
        data: send,
        success: function(datos) {
            notificarCambio(cambio);
            $(".alertify-logs").css("top", 300 + "px");
            $("#btnClienteConfirmarDatosFacturar").hide();
            $("#dominio1").hide();
            $("#dominio2").hide();
            fn_bloquearIngreso();
            fn_btnOk(numPadCliente);
            fn_ocultar_alfanumerico();

            datamdcsave.cdn_id = $("#txtCadenaId").val();
            datamdcsave.documento = send.clienteDocumento;
            datamdcsave.tipoDocumento = tipoDocumento;
            datamdcsave.email = send.clienteCorreo;
            datamdcsave.telefono = send.clienteFono;
            datamdcsave.primerNombre = send.clienteDescripcion;
            datamdcsave.direccion = '';
            datamdcsave.idUserPos = $('#idUser').val();
            datamdcsave.rst_id = $('#txtRestaurante').val();
            var response = fn_masterdatacliente("GUARDAR", null, null, datamdcsave, valid);
            if (response.codeError==="GKFC002") { //Si el cliente existe en la Master Data Clientes entonces se modifican los datos
                fn_masterdatacliente("MODIFICAR", null, null, datamdcsave);
            }

            var tipo = localStorage.getItem("tipo");
            if (tipo !== null){
                localStorage.removeItem("id_menu_facturacion");
                localStorage.removeItem("id_cla_facturacion");
                localStorage.removeItem("es_menu_agregador_facturacion");                
            }
            
            //if (banderaUber === 1) {
            if (BANDERA_AGREGADOR === 1) {
                var cedulaC = $("#txtClienteCI").val();
                var nombreC = $("#txtClienteNombre").val();
                //var direccionC = $("#txtClienteDireccion").val();
                var telefonoC = $("#txtClienteFono").val();
                var correoC = $("#txtCorreo").val();
                fn_selecionaClienteAx(nombreC, cedulaC, cedulaC, telefonoC, null, correoC, 'NULL');
                
            } else {
                if (!facturaImpresa) {
                    fn_printFactura();
                    fn_cargando(0);
                    facturaImpresa = true;
                }
            }

        }        
    });
}

function fn_ObtenerCliente(datos, cedula, accion = '') {
    var cliente = 0;
    var send = {"ObtenerCliente": 1};
    send.datos = datos;
    send.cedula = $('#txtClienteCI').val();
    send.usuario = $("#txtUserId").val();
    send.accion = accion;
    send.odp_id = $("#txtOrdenPedidoId").val();
    $.ajax({
        async: false,
        type: "POST",
        url: "../ordenpedido/config_ordenPedido.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            console.log(datos);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alertify.error(
                "Se ha producido un error. Por favor inténtelo nuevamente."
            );
        },
    });
    return cliente;
}

/* Finaliza la transacción al presionar el botón FACTURAR */
function fn_actualizarRegistroCliente(valid) {

    if(!validarCamposCliente()){
        console.log('retorna false')
        return false;
    }

  
    var cambio = calcularCambio();

    fn_actualizarDatosCliente(cambio, valid);
}

///********INTEGRACION PARA BIOMETRICA******////
function Identificar() {
    var send;
    var respuesta = '';
    var Respuesta;
    var Mensaje;
    var cedula = $("#txtClienteCI").val();
    CapturaHuellaCr(cedula, 6, 2);
    if (Respuesta === 0) {
        send = {
            "biometrika": 1
        };
        send.hid_bio = Mensaje; //$("#d2trama").val();//
        send.cedula_bio = cedula;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../facturacion/config_facturacion.php",
            data: send,
            success: function(datos) {
                respuesta = datos[0]['respuesta'];
            }
        });
        return respuesta;
    } else {
        alert("Error " + Mensaje);
    }
    return false;
}

function notificarCambio(cambio) {
    alertify.error("Su cambio es de $" + cambio);
}

// Crear o actualizar cliente
function guardaActualizaCliente(valid) {
    var send = {
        "guardaActualizaCliente": 1
    };
    var tipoDocumento;
    if ($("#txtClienteCI").val().length == 10) {
        tipoDocumento = "CEDULA";
    } else if ($("#txtClienteCI").val().length == 13) {
        tipoDocumento = "RUC";
    }
    send.clienteTipoDoc = clientetipodocumentokiosko;
    var datamdcsave = {};
    send.clienteDocumento = $("#txtClienteCI").val();
    send.clienteDescripcion = $("#txtClienteNombre").val();
    //send.clienteDireccion = $("#txtClienteDireccion").val();
    send.clienteTelefono = $("#txtClienteFono").val();
    send.clienteCorreo = $("#txtCorreo").val();

    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_clientes.php",
        data: send,
        success: function(datos) {
            notificarCambio(calcularCambio());
            $(".alertify-logs").css("top", 70 + "px");
            $("#btnConsumidorFinal").hide();
            $("#btnClienteConfirmarDatos").hide();
            $("#dominio1").hide();
            $("#dominio2").hide();
            fn_bloquearIngreso();
            fn_btnOk(numPadCliente);
            fn_ocultar_alfanumerico();

            datamdcsave.cdn_id = $("#txtCadenaId").val();
            datamdcsave.documento = send.clienteDocumento;
            datamdcsave.tipoDocumento = send.clienteTipoDoc;
            datamdcsave.email = send.clienteCorreo;
            datamdcsave.telefono = send.clienteTelefono;
            datamdcsave.primerNombre = send.clienteDescripcion;
            datamdcsave.direccion = '';
            datamdcsave.idUserPos = $('#idUser').val();
            datamdcsave.rst_id = $('#txtRestaurante').val();
            var response = fn_masterdatacliente("GUARDAR", null, null, datamdcsave, valid);
            if (response.codeError==="GKFC002") { //Si el cliente existe en la Master Data Clientes entonces se modifican los datos
                fn_masterdatacliente("MODIFICAR", null, null, datamdcsave);
            }

            if (banderaUber === 1) {
                var cedulaC = $("#txtClienteCI").val();
                var nombreC = $("#txtClienteNombre").val();
                //var direccionC = $("#txtClienteDireccion").val();
                var telefonoC = $("#txtClienteFono").val();
                var correoC = $("#txtCorreo").val();
                fn_selecionaClienteAx(nombreC, cedulaC, cedulaC, telefonoC, null, correoC, 'NULL');
            } else {
                fn_printFactura();
                fn_cargando(0);
            }
        }
    });
}

function validateBackgroundEmailPlugThem(data, email, datos_existentes, previusData){
    if(data && data.status){
        if(data.status === "invalid"){
            if(!previusData.includes(email)){
                datos_existentes.push(email);
                console.log('no se ha validado el correo ingresado, reinicio contador')
                //localStorage.removeItem('email_valid');
                localStorage.setItem("intValidaFC", localStorage.getItem("intValida") - 1);
                alertify.alert("El email ingresado no es valido, ");  
            }else{
                // reintentar po ralgun fallo del api (no nos devolvio la respuesta esperada)
                console.log('resto del actual')
                localStorage.setItem("intValidaFC", localStorage.getItem("intValidaFC") - 1);
                if(localStorage.getItem("intValidaFC") > 0) {
                    alertify.alert("El email ingresado no es v\u00e1lido. Por favor vuelva a verificar." + "Le restan " + localStorage.getItem("intValidaFC").toString() + " " + "intentos. Al culminarlos el correo será tomado como válido.");
                } else if (localStorage.getItem("intValidaFC") == 0)  {
                    alertify.alert("El email ingresado no es v\u00e1lido. Ya agoto sus intentos de verificación, el correo será tomado como válido.");
                }
            }
            localStorage.setItem('email_valid', JSON.stringify(datos_existentes));
        }
    }
    
}
// Verifica email de cliente, retorna si es válido o no

function fn_validaEmailAPI(valid) {
    console.log('1')

    var email = '';
    email = $("#txtCorreo").val();
    expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if ( !expr.test(email) ){
        Toastify({
            text: "El email es invalido",
            className: "info",
            style: {
                background: "linear-gradient(to right, #00b09b, #96c93d)",
            }
        }).showToast();
        //console.log('el email es incorrecto')
        return;
    }
    var arr_e = [];
    var datos_existentes = localStorage.getItem('email_valid');
    datos_existentes = datos_existentes == null ? [] : JSON.parse(datos_existentes);
    arr_e = localStorage.getItem('email_valid');
    if(valid != 0){
        if(localStorage.getItem('intValidaFC') === undefined || localStorage.getItem('intValidaFC') === null){
            console.log('no existe la config, se procede a cargarla')
            localStorage.setItem('intValida', valid);
            localStorage.setItem('intValidaFC', valid);
            localStorage.setItem('intValidaOP', valid);
        }

        send = {"cargarUrlApiValidaPlugthem": 1, "CustomerEmail": email};
        $.ajax({type: "POST", dataType: 'json', url: "../ordenpedido/config_ordenPedido.php", data: send})
            .fail((jqXHR, textStatus, errorThrown) => {
            console.log('error');
        }).done((data, textStatus, jqXHR)=>{
            validateBackgroundEmailPlugThem(data, email, datos_existentes, arr_e);
        });


    }
}

function fn_validaEmailRegisteredAPI(email) {
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

    if(localStorage.getItem('intValidaFC') === undefined || localStorage.getItem('intValidaFC') === null){
        console.log('no existe la config, se procede a cargarla')
        localStorage.setItem('intValida', valid);
        localStorage.setItem('intValidaFC', valid);
        localStorage.setItem('intValidaOP', valid);
    }
    send = {"cargarUrlApiValidaPlugthem": 1, "CustomerEmail": email};
    $.ajax({type: "POST", dataType: 'json', url: "../ordenpedido/config_ordenPedido.php", data: send})
        .fail((jqXHR, textStatus, errorThrown) => {
        console.log('error');
    }).done((data, textStatus, jqXHR)=>{
        validateBackgroundEmailPlugThem(data, email, datos_existentes, arr_e);
    });
}