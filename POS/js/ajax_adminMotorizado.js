var motorolo = {};
var tiposDocumentoGlobal = [];
var call_data = {
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../adminMotorizado/config_motorizado.php",
    data: null
};

$(document).ready(function () {
    cargar_tipos_motorizados();
    cargar_motorizados('Activo');
    $('#in_tipo').on('change', function(){
        const $tipoEmpresa = $('#in_tipo').val();
        if($tipoEmpresa){
            if($tipoEmpresa != 'INTERNO'){
                $('#in_nomina').val('');
                $('#div_nomina').hide();
            }else{
                $('#div_nomina').show();
                $('#in_nomina').show();
            }
            cargar_empresas_por_tipo($tipoEmpresa);
        }
    } );

    $( "#in_documento" ).keyup(function() {
        $documento = $( "#in_documento" ).val();
        if($documento  && $documento.length >= 10){
            buscarMotorizado();
        }else{
            limpiezaParcialCampos();
            ocultarParcialCampos();
        }
    });

    cargar_tipo_identificacion();
    cargar_url_api_motorizados();
    cargar_ubicacion_restaurante();
    crear_token_API_MDM_CLIENTE();
    //obtener_TipoDocumento();
    //consultar_Cliente_API_MDM_CLIENTE();


});


var cargar_ubicacion_restaurante = function(){

    send = {};
    send.metodo = "cargarUbicacionRestaurante";
    call_data.data = send;
    $.ajax({
        ...call_data,
        success: function ( response ) {
            $("#id_pais").val(response.id_pais);
            $("#nombre_pais").val(response.nombre_pais);
            $("#id_ciudad").val(response.id_ciudad);
            $("#nombre_ciudad").val(response.nombre_ciudad);
            $("#in_ciudad_formulario").val(response.nombre_ciudad);
            $("#in_ciudad_formulario").prop( "disabled", true );
        },
        error: function () {
            cargando( false );
            alert('Error al cargar la ubicación del Restaurante...');
        }
    });

  }


var cargar_url_api_motorizados = function(){

    send = {};
    send.metodo = "cargarUrlApiMotorizados";
    call_data.data = send;
    $.ajax({
        ...call_data,
        success: function ( response ) {

            $("#url_api_motorizados").val(response.url);
        },
        error: function () {
            cargando( false );
            alert('Error al cargar la dirección de Motorizados Centralizado...');
        }
    });

  }

  var crear_token_API_MDM_CLIENTE = function(){

    send = {};
    send.metodo = "validarTokenApiCliente";
    call_data.data = send;
    $.ajax({
        ...call_data,
        success: function ( response ) {
            //console.log(response);
            //alert('Se creo y valido el token API MDM CLIENTE...');
        },
        error: function () {
            cargando( false );
            //console.log("Error al cargar el token API MDM CLIENTE...");
            //alert('Error al cargar el token API MDM CLIENTE...');
        }
    });

  }

  var obtener_TipoDocumento = function(){

    send = {};
    send.metodo = "obtenerTipoDocumento";
    call_data.data = send;
    $.ajax({
        ...call_data,
        success: function ( response ) {
            console.log(response);
            //alert('TIPO DE DOCUMENTO MOTOROLO');
        },
        error: function () {
            cargando( false );
            //console.log("Error al cargar el token API MDM CLIENTE...");
            //alert('Error al cargar el token API MDM CLIENTE...');
        }
    });

  }



var ocultarParcialCampos = function(){
    $('#in_nombres').val('');
    $('#in_apellidos').val('');
    $('#div_nombres').hide();

    $('#in_telefono').val('');
    $('#div_telefono').hide();

    $('#in_nomina').val('');
    $('#div_nomina').hide();

    $('#div_ciudad').hide();

    $('#div_botones').hide();

}

var mostrarParcialCampos = function(){
    $('#div_nombres').show();
    $('#div_telefono').show();
    $('#div_nomina').show();
    $('#div_botones').show();

}

var limpiezaParcialCampos = function(){
    $('#in_nombres').val('');
    $('#in_apellidos').val('');
    $('#in_telefono').val('');
    $('#in_nomina').val('');
}

var cargar_motorizados = function( estado ) {
    var html = '<thead><tr class="active"><th>Documento</th><th>Nombres</th><th class="text-center">Telefono</th><th class="text-center">Tipo</th><th class="text-center">Empresa</th><th>Activo</th></tr></thead>';
    $('#lstMotorizados').html( html );
    cargando( true );
    send = {};
    send.metodo = "cargarMotorizados";
    send.estado = estado;
    call_data.data = send;
    $.ajax({
        ...call_data,
        success: function ( response ) {
            cargando( false );
            //console.log(response);
            if( response.registros > 0 ) {
                for ( i = 0; i < response.registros; i++ ) {
                    html = '<tr id="' + response[i]['idMotorolo'] + '" onclick="seleccionar(\'' + response[i]['idMotorolo'] + '\')" ondblclick="cargarMotorolo(\'' + response[i]['idMotorolo'] + '\', \'' + response[i]['nombres'] + '\', \'' + response[i]['apellidos'] + '\', \'' + response[i]['documento'] + '\', \'' + response[i]['telefono'] + '\', \'' + response[i]['tipo'] + '\', \'' + response[i]['empresa'] + '\', \'' + response[i]['codigo'] + '\', \'' + response[i]['nomina'] + '\', \'' + response[i]['estado'] + '\')"><td>' + response[i]['documento'] + '</td><td>' + response[i]['nombres'] + ' ' + response[i]['apellidos'] + '</td><td class="text-center">' + response[i]['telefono'] + '</td><td class="text-center">' + response[i]['tipo'] + '</td><td>' + response[i]['empresa'] + '</td>';
                    if ( response[i]['estado'] == 'Activo' ) {
                        html = html + '<td class="text-center"><input type="checkbox" name="" value="1" checked="checked" disabled/></td></tr>';
                    } else {
                        html = html + '<td class="text-center"><input type="checkbox" name="" value="1" disabled/></td></tr>';
                    }
                    $('#lstMotorizados').append( html );
                }

                $('#lstMotorizados').dataTable({ 'destroy': true });
                $("#lstMotorizados_length").hide();

                $("#lstMotorizados_paginate").addClass('col-xs-10');
                $("#lstMotorizados_info").addClass('col-xs-10');
                $("#lstMotorizados_length").addClass('col-xs-6');

            } else {
                html = html + '<tr><th colspan="6">No existen registros.</th></tr>';
                $('#lstMotorizados').html(html);
                cargando( false );
            }
        },
        error: function () {
            cargando( false );
            alert('Error al cargar los motorizados...');
        }
    });

};

var buscarMotorizado = function() {

    cargando( true );

    const urlApiMotorizado = $('#url_api_motorizados').val();
    const idPais = $('#id_pais').val();
    const nombrePais = $('#nombre_pais').val().toUpperCase();

    var estado = 'Activo';
    if( !$('#in_estado').is(":checked") ) {
        estado = 'Inactivo';
    }

    var tipoIdentificacion  = $('#in_tipo_identificacion').val();
    var documento           = $('#in_documento').val();
    var idCedula            = $('#id_td_cedula').val();
    var idRuc               = $('#id_td_ruc').val();

    if ( documento.length < 10 ){
        cargando( false );
        alertify.error("Identificacion es requerida y tiene que tener mínimo 10 caracteres.");
        return;
    }

    if(nombrePais == 'ECUADOR'){
        if( (tipoIdentificacion == idCedula || tipoIdentificacion == idRuc) && !fn_validarDocumento(documento)) {
            fn_documentoInvalido();
            cargando( false );
            return
        }
    }

    motorolo.estado = estado;
    motorolo.documento = documento;

    send = {
        metodo: "buscarMotorizado",
        url:    urlApiMotorizado,
        ...motorolo
    };

    call_data.data = send;
    $.ajax({
        ...call_data,
        success: function(response) {

            if(response){
                mostrarParcialCampos();
                $('#div_ciudad').show();

                if(response.estado == 3 || response.estado == 2){
                    $tipoEmpresa = response.motorolo.tipo;
                    $('#in_tipo').val($tipoEmpresa);
                    cargar_empresas_por_tipo($tipoEmpresa);
                    $('#in_nombres').val(response.motorolo.nombres);
                    $('#in_apellidos').val(response.motorolo.apellidos);
                    $('#in_telefono').val(response.motorolo.telefono);
                    $('#in_empresa').val(response.motorolo.empresa);

                    if($tipoEmpresa === 'INTERNO'){
                        $('#in_nomina').val(response.motorolo.codigoNomina);
                        $('#div_nomina').show();
                        $('#in_nombres').prop('disabled',true);
                        $('#in_apellidos').prop('disabled',true);
                    }else{
                        $('#in_nomina').val('');
                        $('#div_nomina').hide();
                    }




                    if(response.estado == 3){
                        motorolo.idMotorolo = response.motorolo.IDMotorolo;
                    }

                }
                else{
                    $tipoEmpresa = $('#in_tipo').val();

                    $('#in_nombres').prop('disabled',false);
                    $('#in_apellidos').prop('disabled',false);
                    limpiezaParcialCampos();

                    if($tipoEmpresa === 'INTERNO'){
                        $('#in_nomina').val(response.motorolo.codigoNomina);
                        $('#div_nomina').show();
                    }else{
                        $('#in_nomina').val('');
                        $('#div_nomina').hide();
                    }

                }
            }

            console.log('Resultado');
            console.log(response);

        },
        error: function() {
            cargando(false);
            alertify.error('Error al buscar los motorizados...');
        }
    });

    cargando( false );

};

var  fn_documentoInvalido = function() {
    alertify.error('N\u00famero de identificacion no v\u00e1lido.');
}

var crear_motorizado = function() {
    var idCedula = $('#id_td_cedula').val();
    console.log('ID CEDULA');
    console.log(idCedula);
    motorolo.idMotorolo = null;
    $('#titulomodal').html("Nuevo Motorolo");
    $('#in_estado').prop("checked", true);
    $('#in_tipo').val( 'INTERNO' );
    $('#in_documento').val( '' );
    $('#in_nombres').val( '' );
    $('#in_apellidos').val( '' );
    $('#in_telefono').val( '' );
    $('#in_nomina').val( '' );
    cargar_empresas_por_tipo('INTERNO');
    $('#in_tipo_identificacion').val(idCedula);
    ocultarParcialCampos();
    $('#in_nombres').prop('disabled',false);
    $('#in_apellidos').prop('disabled',false);
    $('#modal').modal('show');
};

var cargarMotorolo = function( idMotorolo, nombres, apellidos, documento, telefono, tipo, empresa, codigo, nomina, estado ) {

    cargar_empresas_por_tipo( tipo );

    console.log(idMotorolo, nombres, apellidos, documento, telefono, tipo, empresa, codigo, nomina, estado);

    motorolo.idMotorolo = idMotorolo;

    if ( estado === 'Activo' ) {
        $('#in_estado').prop("checked", true);
    } else {
        $('#in_estado').prop("checked", false);
    }

    $('#titulomodal').html("Modificar "+ nombres + " " + apellidos );
    $('#modal').modal('show');

    $('#in_tipo').val( tipo );
    $('#in_empresa').val( empresa );
    $('#in_documento').val( documento );
    $('#in_nombres').val( nombres );
    $('#in_apellidos').val( apellidos );
    $('#in_telefono').val( telefono );
    $('#in_codigo').val( codigo );
    $('#in_nomina').val( nomina );

};

var cargando = function( estado ) {
    if ( estado ) {
        $('#mdl_rdn_pdd_crgnd').css('display', 'block');
        $('#mdl_pcn_rdn_pdd_crgnd').css('display', 'block');
    } else {
        $('#mdl_rdn_pdd_crgnd').css('display', 'none');
        $('#mdl_pcn_rdn_pdd_crgnd').css('display', 'none');
    }
};

var seleccionar = function( idMotorolo ) {
    $("#lstMotorizados tr").removeClass("success");
    $("#" + idMotorolo).addClass("success");
}

var cargar_tipos_motorizados = function( estado ) {
    var html = '';
    $('#in_tipo').html( html );
    cargando( true );
    send = {};
    send.metodo = "cargarTiposMotorizados";
    call_data.data = send;
    $.ajax({
        ...call_data,
        success: function ( response ) {
            cargando( false );
            //console.log(response);
            if( response.registros > 0 ) {
                for ( i = 0; i < response.registros; i++ ) {
                    html = '<option values="' + response[i]['tipos'] + '">' + response[i]['tipos'] + '</option>';
                    $('#in_tipo').append( html );
                }
            } else {
                alertify.error("No existen tipos de motorizados configurados.");
            }
        },
        error: function () {
            cargando( false );
            alert('Error al cargar los motorizados...');
        }
    });

};

var cargar_empresas_por_tipo = function($tipoEmpresa){

    var html = '';
    $('#in_empresa').html( html );
    cargando( true );
    send = {};
    send.metodo = "cargarEmpresas";
    send.tipoEmpresa = $tipoEmpresa;
    call_data.data = send;
    $.ajax({
        ...call_data,
        success: function ( response ) {
            cargando( false );
            //console.log(response);
            if( response.registros > 0 ) {
                for ( i = 0; i < response.registros; i++ ) {
                    html = '<option values="' + response[i]['empresa'] + '">' + response[i]['empresa'] + '</option>';
                    $('#in_empresa').append( html );
                }
            } else {
                alertify.error("No existen tipos de motorizados configurados.");
            }
        },
        error: function () {
            cargando( false );
            alert('Error al cargar las empresas de acuerdo al tipo ....');
        }
    });

};

var cargar_tipo_identificacion = function(){

    var html = '';
    $('#in_tipo_identificacion').html( html );
    cargando( true );
    send = {};
    send.metodo = "cargarTiposDocumentos";
    call_data.data = send;
    $.ajax({
        ...call_data,
        success: function ( response ) {
            cargando( false );
            if( response.registros > 0 ) {
                tiposDocumentoGlobal = [];

                for ( i = 0; i < response.registros; i++ ) {

                    let identificacion = {
                        'id':  response[i]['idTipoDocumento'],
                        'nombre': response[i]['nombre']
                    };

                    tiposDocumentoGlobal.push(identificacion);

                    //Almacenar ID de RUC en variable global
                    if(response[i]['codigo'] == '04' ||  response[i]['nombre'] == 'RUC' ){
                        $('#id_td_ruc').val(response[i]['idTipoDocumento']);
                    }

                    //Almacenar ID de CEDULA en variable global
                    if(response[i]['codigo'] == '05' ||  response[i]['nombre'] == 'CEDULA'){
                        $('#id_td_cedula').val(response[i]['idTipoDocumento']);
                    }

                    html = '<option value="' + response[i]['idTipoDocumento'] + '">' + response[i]['nombre'] + '</option>';
                    $('#in_tipo_identificacion').append( html );
                }

                console.log('Tipos de Documento arreglo');
                console.log(tiposDocumentoGlobal);
                cargando( false );
            } else {
                alertify.error("No existen tipos de identificacion configurados.");
                cargando( false );
            }
        },
        error: function () {
            cargando( false );
            alert('Error al cargar los tipos de identificacion...');
        }
    });
};

var fn_validarDocumento = function(numero){


     var suma = 0;
     var residuo = 0;
     var pri = false;
     var pub = false;
     var nat = false;
     var numeroProvincias = 22;
     var modulo = 11;

     /* Verifico que el campo no contenga letras */
     var ok=1;
     for (i=0; i<numero.length && ok==1 ; i++){
        var n = parseInt(numero.charAt(i));
        if (isNaN(n)) ok=0;
     }
     if (ok==0){
        //alert("No puede ingresar caracteres en el n�mero de c�dula!!");
        return false;
     }

     if (numero.length < 10 ){
        //alert('El n�mero ingresado no es v�lido');
        //alertify.error("El n&uacute;mero de c&eacute;dula ingresado no es v�lido.");
        return false;
     }

     /* Los primeros dos digitos corresponden al codigo de la provincia */
     provincia = numero.substr(0,2);
     if (provincia < 1 || provincia > numeroProvincias){
        //alert('El c�digo de la provincia (dos primeros d�gitos) es inv�lido');
        //alertify.error("El c�digo de la provincia (dos primeros d�gitos) es inv�lido.");
    return false;
     }

     /* Aqui almacenamos los digitos de la cedula en variables. */
     d1  = numero.substr(0,1);
     d2  = numero.substr(1,1);
     d3  = numero.substr(2,1);
     d4  = numero.substr(3,1);
     d5  = numero.substr(4,1);
     d6  = numero.substr(5,1);
     d7  = numero.substr(6,1);
     d8  = numero.substr(7,1);
     d9  = numero.substr(8,1);
     d10 = numero.substr(9,1);

     /* El tercer digito es: */
     /* 9 para sociedades privadas y extranjeros   */
     /* 6 para sociedades publicas */
     /* menor que 6 (0,1,2,3,4,5) para personas naturales */

     if (d3==7 || d3==8){
        //alert('El tercer d�gito ingresado es inv�lido');
        //alertify.error("El tercer d�gito ingresado es inv�lido.");
        return false;
     }

     /* Solo para personas naturales (modulo 10) */
     if (d3 < 6){
        nat = true;
        p1 = d1 * 2;  if (p1 >= 10) p1 -= 9;
        p2 = d2 * 1;  if (p2 >= 10) p2 -= 9;
        p3 = d3 * 2;  if (p3 >= 10) p3 -= 9;
        p4 = d4 * 1;  if (p4 >= 10) p4 -= 9;
        p5 = d5 * 2;  if (p5 >= 10) p5 -= 9;
        p6 = d6 * 1;  if (p6 >= 10) p6 -= 9;
        p7 = d7 * 2;  if (p7 >= 10) p7 -= 9;
        p8 = d8 * 1;  if (p8 >= 10) p8 -= 9;
        p9 = d9 * 2;  if (p9 >= 10) p9 -= 9;
        modulo = 10;
     }

     /* Solo para sociedades publicas (modulo 11) */
     /* Aqui el digito verficador esta en la posicion 9, en las otras 2 en la pos. 10 */
     else if(d3 == 6){
        pub = true;
        p1 = d1 * 3;
        p2 = d2 * 2;
        p3 = d3 * 7;
        p4 = d4 * 6;
        p5 = d5 * 5;
        p6 = d6 * 4;
        p7 = d7 * 3;
        p8 = d8 * 2;
        p9 = 0;
     }

     /* Solo para entidades privadas (modulo 11) */
     else if(d3 == 9) {
        pri = true;
        p1 = d1 * 4;
        p2 = d2 * 3;
        p3 = d3 * 2;
        p4 = d4 * 7;
        p5 = d5 * 6;
        p6 = d6 * 5;
        p7 = d7 * 4;
        p8 = d8 * 3;
        p9 = d9 * 2;
     }

     suma = p1 + p2 + p3 + p4 + p5 + p6 + p7 + p8 + p9;
     residuo = suma % modulo;

     /* Si residuo=0, dig.ver.=0, caso contrario 10 - residuo*/
     digitoVerificador = residuo==0 ? 0: modulo - residuo;

     /* ahora comparamos el elemento de la posicion 10 con el dig. ver.*/
     if (pub==true){
        if (digitoVerificador != d9){
           //alert('El ruc de la empresa del sector p�blico es incorrecto.');
           return false;
        }
        /* El ruc de las empresas del sector publico terminan con 0001*/
        if ( numero.substr(9,4) != '0001' ){
           //alert('El ruc de la empresa del sector p�blico debe terminar con 0001');
           return false;
        }
     }
     else if(pri == true){
        if (digitoVerificador != d10){
           //alert('El ruc de la empresa del sector privado es incorrecto.');
           return false;
        }
        if ( numero.substr(10,3) != '001' ){
           //alert('El ruc de la empresa del sector privado debe terminar con 001');
           return false;
        }
     }

     else if(nat == true){
        if (digitoVerificador != d10){
           //alert('El n�mero de c�dula de la persona natural es incorrecto.');
           //alertify.error("El n&uacute;mero de c&eacute;dula de la persona es incorrecto.");
           return false;
        }
        if (numero.length >10 && numero.substr(10,3) != '001' ){
           //alert('El ruc de la persona natural debe terminar con 001');
           return false;
        }
     }
     return true;
};

var guardarMotorizado = function() {

    cargando(true);

    const urlApiMotorizado = $('#url_api_motorizados').val();


    if(validacionGuardar()){

        var estado = 'Activo';
        if (!$('#in_estado').is(":checked")) {
            estado = 'Inactivo';
        }

        var tipo = $('#in_tipo').val();
        var empresa = $('#in_empresa').val();
        var tipoIdentificacion = $('#in_tipo_identificacion').val();
        var documento = $('#in_documento').val();
        var nombres = $('#in_nombres').val();
        var apellidos = $('#in_apellidos').val();
        var telefono = $('#in_telefono').val();
        var nomina = $('#in_nomina').val();



        motorolo.action = 'Update';
        if (!motorolo.idMotorolo) {
            motorolo.action = 'New';
        }

        motorolo.estado = estado.toUpperCase();
        motorolo.tipo = tipo.toUpperCase();
        motorolo.empresa = empresa.toUpperCase();
        motorolo.documento = documento.toUpperCase();
        motorolo.nombres = nombres.toUpperCase();
        motorolo.apellidos = apellidos.toUpperCase();
        motorolo.telefono = telefono.toUpperCase();
        motorolo.nomina = nomina.toUpperCase();
        motorolo.tipoIdentificacion = tipoIdentificacion;
        motorolo.idCiudad = $('#id_ciudad').val();
        motorolo.urlApi = urlApiMotorizado;
        motorolo.nombreTipoIdentificacion =  tiposDocumentoGlobal.find(x => x.id == tipoIdentificacion).nombre;
        motorolo.nombreCiudad = $('#nombre_ciudad').val();

        send = {
            metodo: "crearMotorizado",
            ...motorolo
        };

        call_data.data = send;
        $.ajax({
            ...call_data,
            success: function(response) {
                //console.log(response);
                if (response.estado > 0) {

                    alertify.success(response.mensaje);
                    $('#modal').modal('hide');
                    $('#flt_activo').prop("checked", true);
                    $('#flt_inactivo').prop("checked", false);
                    $('#opciones_1').addClass("active");
                    $('#opciones_2').removeClass("active");
                    if($('#Dragontail').is(":checked")){
                        sendRiderToDragontail(response.IDMotorolo,0);
                    }
                    cargar_motorizados('Activo');

                } else {
                    cargando(false);
                    alertify.error('Error al actualizar los motorizados...');
                }
            },
            error: function() {
                cargando(false);
                alertify.error('Error al actualizar los motorizados...');
            }
        });


    }else{
        cargando(false);

    }


};



var validacionGuardar = function(){

    var tipo = $('#in_tipo').val();
    var empresa = $('#in_empresa').val();
    var tipoIdentificacion = $('#in_tipo_identificacion').val();
    var documento = $('#in_documento').val();
    var nombres = $('#in_nombres').val();
    var apellidos = $('#in_apellidos').val();
    var telefono = $('#in_telefono').val();


    if(!tipo){
        alertify.error('Es necesario seleccionar un tipo de empresa');
        return false;
    }


    if(!empresa){
        alertify.error('Es necesario seleccionar una empresa');
        return false;
    }

    if(!tipoIdentificacion){
        alertify.error('Es necesario seleccionar un tipo de identificación');
        return false;
    }

    if(!documento){
        alertify.error('Es necesario ingresar un número de identificación');
        return false;
    }

    if(!nombres){
        alertify.error('Es necesario ingresar un nombre');
        return false;
    }

    if(!apellidos){
        alertify.error('Es necesario ingresar un apellido');
        return false;
    }

    if(!telefono){
        alertify.error('Es necesario ingresar un teléfono');
        return false;
    }

    return true;


}
