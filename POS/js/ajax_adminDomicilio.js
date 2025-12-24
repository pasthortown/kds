
var motorizadosActivos = [];
var motorizadosActivosCentralizados = [];
var tiposDocumentoGlobal = [];
var motorolo = {};



$(document).ready(function () {
    cargarPeriodoAbierto();
    cargar_url_api_motorizados();
    cargar_url_api_motorizados_gerente();
    cargar_tipo_identificacion();
    cargar_ubicacion_restaurante();

    $( "#lst_mtrzds_ctvs_filter" ).keyup(function() {
        let $buscador =  $( "#lst_mtrzds_ctvs_filter" ).val();

        if($buscador){
            $buscador = $buscador.trim();
            if($buscador.length >= 10){
                buscar_motorizado();
            }else if($buscador.length < 10){
                
                if(motorizadosActivos && motorizadosActivos.length > 0){

                    var html = '<thead><tr class="active"><th class="text-center">Documento</th><th class="text-center">Motorizado</th><th class="text-center">Telefono</th><th class="text-center">Tipo</th><th class="text-center">Opcion</th></tr></thead>';
                    $('#lst_mtrzds_ctvs').html( html );
                
                    for ( i = 0; i < motorizadosActivos.length; i++ ) {
                        html = '<tr id="mt_'    + motorizadosActivos[i]['idMotorizado'] + '"><td class="text-center">' 
                                                + motorizadosActivos[i]['documento'] + '</td><td>' 
                                                + motorizadosActivos[i]['motorizado'] + '</td><td class="text-center">' 
                                                + motorizadosActivos[i]['telefono'] + '</td><td class="text-center">' 
                                                + motorizadosActivos[i]['tipo'] 
                                                + '</td><td class="text-center"><button class="btn btn-primary" type="button" onclick="asignarMotorizadoPeriodo(\'' + motorizadosActivos[i]['idMotorizado'] + '\', \'' + motorizadosActivos[i]['motorizado'] + '\', false)">Asignar</button></td></td>';
                    
                        $('#lst_mtrzds_ctvs').append( html );
                    }
                
                }

            }
        }

    });

});

var data = {};

var params_ajax = {
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../adminDomicilio/config_domicilio.php",
    data: null
};

var call_data = {
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../adminMotorizado/config_motorizado.php",
    data: null
};


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
        },
        error: function () {
            cargando( false );
            alert('Error al cargar la ubicación del Restaurante...');
        }
    });

  }


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
            //console.log(response);
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


var buscar_motorizado = function(){
    let $buscador =  $( "#lst_mtrzds_ctvs_filter" ).val();

    if(motorizadosActivos){
        let motorizadoLocalCoincidencia = motorizadosActivos.filter(x => x.documento == $buscador);


        if(motorizadoLocalCoincidencia && motorizadoLocalCoincidencia.length > 0){
            var html = '<thead><tr><th class="text-center" colspan="5">BASE DE DATOS LOCAL</th></tr><tr class="active"><th class="text-center">Documento</th><th class="text-center">Motorizado</th><th class="text-center">Telefono</th><th class="text-center">Tipo</th><th class="text-center">Opcion</th></tr></thead>';
            $('#lst_mtrzds_ctvs').html( html );

            for ( i = 0; i < motorizadoLocalCoincidencia.length; i++ ) {
                html = '<tr id="mt_'    + motorizadoLocalCoincidencia[i]['idMotorizado'] + '"><td class="text-center">' 
                                        + motorizadoLocalCoincidencia[i]['documento'] + '</td><td>' 
                                        + motorizadoLocalCoincidencia[i]['motorizado'] + '</td><td class="text-center">' 
                                        + motorizadoLocalCoincidencia[i]['telefono'] + '</td><td class="text-center">' 
                                        + motorizadoLocalCoincidencia[i]['tipo'] 
                                        + '</td><td class="text-center"><button class="btn btn-primary" type="button" onclick="asignarMotorizadoPeriodo(\'' + motorizadoLocalCoincidencia[i]['idMotorizado'] + '\', \'' + motorizadoLocalCoincidencia[i]['motorizado'] + '\', false)">Asignar</button></td></td>';

                $('#lst_mtrzds_ctvs').append( html );
            }

        }else if(motorizadoLocalCoincidencia.length == 0){
            cargarMotorizadosActivosAPI();
        }

    }

}


var cargar_url_api_motorizados = function(){

    send = {};
    send.metodo = "cargarUrlApiMotorizados";
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
        success: function ( response ) {
            $("#url_api_motorizados").val(response.url);
        },
        error: function () {
            cargando( false );
            alert('Error al obtener la dirección de Motorizados centralizado...');
        }
    });

  }

  var cargar_url_api_motorizados_gerente = function(){

    send = {};
    send.metodo = "cargarUrlApiMotorizadosGerente";
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
        success: function ( response ) {
            $("#url_api_motorizados_gerente").val(response.url);
        },
        error: function (a,b,c) {
            cargando( false );
            alert('Error al obtener la dirección de Notificación a Gerente...');
        }
    });

  }

var cargarTurnosMotorizado = function() {
    var html = '<thead><tr class="active"><th class="text-center">Documento</th><th class="text-center">Motorizado</th><th class="text-center">Telefono</th><th class="text-center">Inicio Turno</th><th class="text-center">Fin Turno</th></tr></thead>';
    $('#lstPeriodoMotorizados').html( html );
    send = {};
    send.metodo = "cargarTurnosMotorizado";
    send.idPeriodo = data.idPeriodo;
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
        success: function ( response ) {
            console.log( "Motorizados: ", response );
            if ( response.registros > 0 ) {
                for ( i = 0; i < response.registros; i++ ) {
                    html = '<tr id="' + response[i]['idMotorizado'] + '" onclick="seleccionar(\'' + response[i]['idMotorizado'] + '\', \'' + response[i]['estado'] + '\')"><td class="text-center">' + response[i]['documento'] + '</td><td>' + response[i]['motorizado'] + '</td><td class="text-center">' + response[i]['telefono'] + '</td><td class="text-center">' + response[i]['fecha_inicio'] + ' ' + response[i]['hora_inicio'] + '</td><td class="text-center">' + response[i]['fecha_fin'] + ' ' + response[i]['hora_fin'] + '</td>';
                    $('#lstPeriodoMotorizados').append( html );
                }

                $('#lstPeriodoMotorizados').dataTable({ 'destroy': true });
                $("#lstPeriodoMotorizados_length").hide();

                $("#lstPeriodoMotorizados_paginate").addClass('col-xs-10');
                $("#lstPeriodoMotorizados_info").addClass('col-xs-10');
                $("#lstPeriodoMotorizados_length").addClass('col-xs-6');

                cargando( false );
            } else {
                cargando( false );
                $('#').html( '<tr><td colspan="5">No existen registros.</td></tr>' );
            }
        },
        error: function () {
            cargando( false );
            alert('Error al cargar motorizados...');
        }
    });

};

var asignarMotorizadoPeriodo = function( idMotorizado, motorizado, centralizado ) {

    alertify.confirm( "Estas seguro de asignar a " + motorizado + "?" , function ( e ) {
        if ( e ) {
            cargando( true );

            if(!centralizado){
                send = {};
                send.metodo = "asignarTurnoMotorizado";
                send.idPeriodo = data.idPeriodo;
                send.idMotorizado = idMotorizado;
                params_ajax.data = send;
                $.ajax({
                    ...params_ajax,
                    success: function ( response ) {
                        console.log( "Respuesta: ", response );
                        if ( response.estado > 0 ) {
                            sendRiderToDragontail(idMotorizado,1);

                            cargarMotorizadosActivos();
                            cargarMotorizadosAsignadosPeriodo();
    
                            alertify.alert( response.mensaje );
    
                            $('#mt_'+idMotorizado).remove();
    
                        } else {
                            alertify.error( 'Error: ' + response.mensaje );
                            cargando( false );
                        }
                    },
                    error: function () {
                        cargando( false );
                        alert('Error al asignar motorizado...');
                    }
                });
    
            }else{
                //Proceso de Creación y luego Asignacion


                motorizadoACrear = motorizadosActivosCentralizados.filter(x => x.identificacion == idMotorizado);

                if(motorizadoACrear && motorizadoACrear.length > 0){
                    crearAsignarMotorizado(motorizadoACrear[0], data.idPeriodo);
                }
            }

        }
    });

};


var crearAsignarMotorizado = function(motorizadoACrear, periodoAsociado) {

    cargando(true);

    const urlApiMotorizado = $('#url_api_motorizados').val();


        var estado = 'Activo';

        var tipoIdentificacionAsociado = tiposDocumentoGlobal.filter(x => x.nombre == motorizadoACrear.tipoIdentificacion);

        if(tipoIdentificacionAsociado && tipoIdentificacionAsociado.length > 0){
            tipoIdentificacionAsociado = tipoIdentificacionAsociado[0];
        }

        var tipo = motorizadoACrear.tipoMotorolo;
        var empresa = motorizadoACrear.empresaMotorolo;
        var tipoIdentificacion =  tipoIdentificacionAsociado.id;
        var documento = motorizadoACrear.identificacion;
        var nombres = motorizadoACrear.nombres;
        var apellidos = motorizadoACrear.apellidos;
        var telefono = motorizadoACrear.telefono;
        var nomina = motorizadoACrear.codigoNomina;
       
        motorolo.action = 'New';
        motorolo.idMotorolo = null;
        motorolo.estado = estado.toUpperCase();
        motorolo.tipo = tipo.toUpperCase();
        motorolo.empresa = empresa.toUpperCase();
        motorolo.documento = documento;
        motorolo.nombres = nombres.toUpperCase();
        motorolo.apellidos = apellidos.toUpperCase();
        motorolo.telefono = telefono.toUpperCase();
        motorolo.nomina = nomina.toUpperCase();
        motorolo.tipoIdentificacion = tipoIdentificacion;
        motorolo.idCiudad = $('#id_ciudad').val();
        motorolo.urlApi = urlApiMotorizado;
        motorolo.nombreTipoIdentificacion =  tipoIdentificacionAsociado.nombre; 
        motorolo.nombreCiudad = $('#nombre_ciudad').val(); 
        
        send = {
            metodo: "crearMotorizado",
            ...motorolo
        };
    
        call_data.data = send;
        $.ajax({
            ...call_data,
            success: function(response) {
                if (response.estado > 0) {

               send = {};
                send.metodo = "asignarTurnoMotorizado";
                send.idPeriodo = periodoAsociado;
                send.idMotorizado = response.IDMotorolo;
                params_ajax.data = send;
                $.ajax({
                    ...params_ajax,
                    success: function ( response ) {
                        if ( response.estado > 0 ) {
                            sendRiderToDragontail(send.idMotorizado,1);

                            cargarMotorizadosActivos();
                            cargarMotorizadosAsignadosPeriodo();
    
                            alertify.alert( response.mensaje );
        
                        } else {
                            alertify.error( 'Error: ' + response.mensaje );
                            cargando( false );
                        }
                    },
                    error: function () {
                        cargando( false );
                        alert('Error al asignar motorizado...');
                    }
                });


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


};



var cargarMotorizadosActivos = function() {
    
    $('#lst_mtrzds_ctvs_filter').val('');
    var html = '<thead><tr class="active"><th class="text-center">Documento</th><th class="text-center">Motorizado</th><th class="text-center">Telefono</th><th class="text-center">Tipo</th><th class="text-center">Opcion</th></tr></thead>';
    $('#lst_mtrzds_ctvs').html( html );
    motorizadosActivos = [];
    send = {};
    send.metodo = "cargarMotorizadosActivos";
    send.idPeriodo = data.idPeriodo;
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
        success: function ( response ) {
            if ( response.registros > 0 ) {
                for ( i = 0; i < response.registros; i++ ) {
                    motorizadosActivos.push(response[i]);
                    html = '<tr id="mt_' + response[i]['idMotorizado'] + '"><td class="text-center">' + response[i]['documento'] + '</td><td>' + response[i]['motorizado'] + '</td><td class="text-center">' + response[i]['telefono'] + '</td><td class="text-center">' + response[i]['tipo'] + '</td><td class="text-center"><button class="btn btn-primary" type="button" onclick="asignarMotorizadoPeriodo(\'' + response[i]['idMotorizado'] + '\', \'' + response[i]['motorizado'] + '\', false)">Asignar</button></td></td>';
                    $('#lst_mtrzds_ctvs').append( html );
                }

                $('#mdl_motorizados').modal('show');

                cargando( false );
            } else {
                $('#lst_mtrzds_ctvs').html('<tr><td colspan="5">No existen motorizados</td></tr>');
                $('#mdl_motorizados').modal('show');
                cargando( false );
            }
        },
        error: function () {
            cargando( false );
            alert('Error al cargar motorizados...');
        }
    });

};


var cargarMotorizadosActivosAPI = function(){
    let $buscador =  $( "#lst_mtrzds_ctvs_filter" ).val();

    send = {};
    send.metodo = "cargarMotorizadosActivosAPI";
    send.url    = $('#url_api_motorizados').val();
    send.parametro = $buscador;
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
        success: function ( response ) {

            motorizadosActivosCentralizados = [];

            if (  response.success == true ) {
                var html = '<thead><tr><th class="text-center" colspan="5">BASE DE DATOS CENTRALIZADA</th></tr><tr class="active"><th class="text-center">Documento</th><th class="text-center">Motorizado</th><th class="text-center">Telefono</th><th class="text-center">Tipo</th><th class="text-center">Opcion</th></tr></thead>';
                $('#lst_mtrzds_ctvs').html( html );
                for ( i = 0; i <  response.data.length; i++ ) {

                    motorizadosActivosCentralizados.push(response.data[i]);
                    html = '<tr id="mt_'    + response.data[i]['identificacion'] + '"><td class="text-center">' 
                                            + response.data[i]['identificacion'] + '</td><td>' 
                                            + response.data[i]['nombres'] + ' '
                                            + response.data[i]['apellidos'] +'</td><td class="text-center">' 
                                            + response.data[i]['telefono'] + '</td><td class="text-center">' 
                                            + response.data[i]['tipoMotorolo'] 
                                            + '</td><td class="text-center"><button class="btn btn-primary" type="button" onclick="asignarMotorizadoPeriodo(\'' + response.data[i]['identificacion'] + '\', \'' + response.data[i]['nombres'] + ' '+ response.data[i]['apellidos']+'\', true)">Asignar</button></td></td>';
                    $('#lst_mtrzds_ctvs').append( html );
                }
                cargando( false );
            } else {
                $('#lst_mtrzds_ctvs').html('<tr><td colspan="5">No existen motorizados</td></tr>');
                cargando( false );
            }
        },
        error: function () {
            cargando( false );
            alert('Error al cargar motorizados...');
        }
    });

}


var cargarPeriodoAbierto = function() {
    cargando( true );
    send = {};
    send.metodo = "cargarPeriodoAbierto";
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
        success: function ( response ) {
            console.log( "Periodo: ", response );
            if ( response.idPeriodo ) {
                $("#prd_fecha").html( response.fecha + ' '+ response.hora );
                $("#prd_usuario").html( response.usuario );
                data.idPeriodo = response.idPeriodo;
                cargarMotorizadosAsignadosPeriodo();
            } else {
                alertify.alert("El periodo aún no se encuentra abierto.");
            }
        },
        error: function () {
            cargando( false );
            alert('Error al cargar periodo abierto...');
        }
    });

};

var cargarMotorizadosAsignadosPeriodo = function() {
    var html = '<thead><tr class="active"><th class="text-center">Documento</th><th class="text-center">Motorizado</th><th class="text-center">Telefono</th><th class="text-center">Total Ordenes</th><th class="text-center">Estado</th></tr></thead>';
    $('#lstPeriodoMotorizados').html( html );
    send = {};
    send.metodo = "cargarMotorizadosAsignadosPeriodo";
    send.idPeriodo = data.idPeriodo;
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
        success: function ( response ) {
            console.log( "Motorizados: ", response );
            if ( response.registros > 0 ) {
                for ( i = 0; i < response.registros; i++ ) {
                    html = '<tr id="' + response[i]['idMotorizado'] + '" onclick="seleccionar(\'' + response[i]['idMotorizado'] + '\', \'' + response[i]['motorizado'] + '\', \'' + response[i]['estado'] + '\')" ondblclick="abrirModalTransacciones(\'' + response[i]['idMotorizado'] + '\', \'' + response[i]['motorizado'] + '\', \'' + response[i]['documento'] + '\', \'' + response[i]['telefono'] + '\', \'' + response[i]['fecha_inicio'] + ' ' + response[i]['hora_inicio'] + '\', \'' + response[i]['fecha_fin'] + ' ' + response[i]['hora_fin'] + '\', \'' + response[i]['estado'] + '\')" ><td class="text-center">' + response[i]['documento'] + '</td><td>' + response[i]['motorizado'] + '</td><td class="text-center">' + response[i]['telefono'] + '</td><td class="text-center">' + response[i]['total'] + '/' + response[i]['maximo_ordenes'] + '</td><td class="text-center">' + response[i]['estado'] + '</td></tr>';
                    $('#lstPeriodoMotorizados').append( html );
                }

                $('#lstPeriodoMotorizados').dataTable({ 'destroy': true });
                $("#lstPeriodoMotorizados_length").hide();

                $("#lstPeriodoMotorizados_paginate").addClass('col-xs-10');
                $("#lstPeriodoMotorizados_info").addClass('col-xs-10');
                $("#lstPeriodoMotorizados_length").addClass('col-xs-6');

                cargando( false );
            } else {
                $('#lstPeriodoMotorizados').append('<tr><td colspan="5">No existen motorizados asignados al periodo.</td></tr>');
                cargando( false );
            }
        },
        error: function () {
            cargando( false );
            alert('Error al cargar motorizados...');
        }
    });

};

var finalizarTurnoMotorizado = function() {
    console.log( "Data: ", data );
    if ( data.estado === 'Asignado' ) {
        alertify.confirm( "Estas seguro de finalizar el turno de " + data.motorizado + "?" , function ( e ) {
            if ( e ) {
                cargando( true );
                send = {};
                send.metodo = "finalizarTurnoMotorizado";
                send.idMotorizado = data.idMotorizado;
                send.idPeriodo = data.idPeriodo;
                params_ajax.data = send;
                $.ajax({
                    ...params_ajax,
                    success: function ( response ) {
                        console.log( "Respuesta: ", response );
                        if ( response.estado ) {
                            alertify.success( response.mensaje );
                            sendRiderToDragontail(data.idMotorizado,0);

                            cargarMotorizadosAsignadosPeriodo();
                            $('#modal').modal('hide');

                            //Notificacion a Gerente de Resumen
                            //Motorizados Internos
                            notificacionMotorizadoGerente(data.idPeriodo, data.idMotorizado);

                            //Ingreso en canal de impresion
                            imprimirFinTurnoMotorizado(data.idPeriodo, data.idMotorizado);

                            data.idMotorizado = null;
                            data.motorizado = null;
                            cargando( false );


                        } else {
                            alertify.error( response.mensaje );
                            cargando( false );
                        }
                    },
                    error: function () {
                        cargando( false );
                        alert('Error al cargar motorizados...');
                    }
                });
            }
        });
    } else {
        alertify.error("El motorizado " + data.motorizado + " tiene ordenes ASIGNADAS o EN CAMINO.");
    }
}


var imprimirFinTurnoMotorizado = function(idPeriodo, idMotorizado){
    send = {};
    send.metodo = "imprimirFinTurnoMotorizado";
    send.idPeriodo = idPeriodo;
    send.idMotorizado = idMotorizado;
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
        success: function ( response ) {
            console.log( "Respuesta IMPRESION MOTORIZADO: ", response );
            if ( response ) {

            } else {
                alertify.error( response.mensaje );
                cargando( false );
            }
        },
        error: function (a,b,c) {
            cargando( false );
            console.log('Error al imprimir el turno del motorizado');
            console.log(a);
            console.log(b);
            console.log(c);

        }
    });

}


var notificacionMotorizadoGerente = function(idPeriodo, idMotorizado){
    send = {};
    send.metodo = "notificacionMotorizadoGerente";
    send.idPeriodo = idPeriodo;
    send.idMotorizado = idMotorizado;
    send.url = $("#url_api_motorizados_gerente").val();
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
        success: function ( response ) {
            console.log( "Respuesta GERENTE: ", response );
            if ( response ) {

            } else {
                alertify.error( response.mensaje );
                cargando( false );
            }
        },
        error: function () {
            cargando( false );

            alert('Error al cargar motorizados...');
        }
    });

}


var abrirModalTransacciones = function( idMotorizado, motorizado, documento, telefono, fecha_inicio, fecha_fin, estado ) {

    // Iniciar datos
    data.idMotorizado = idMotorizado;
    data.estado = estado;
    data.motorizado = motorizado;

    $('#tqt_motorizado').html( motorizado );
    $('#tqt_documento').html( documento );
    $('#tqt_telefono').html( telefono );
    $('#tqt_estado').html( estado );
    $('#tqt_fecha_inicio').html( fecha_inicio );
    $('#tqt_fecha_fin').html( fecha_fin );
    cargarTransaccionesPorMotorizado( idMotorizado );
    $('#modal').modal('show');
};

var cargarTransaccionesPorMotorizado = function( idMotorizado ) {
    cargando( true );
    var html = '<thead><tr><th class="text-center">Pedido</th><th class="text-center">Cliente</th><th class="text-center">Telefono</th><th class="text-center">Forma Pago</th><th class="text-center">Total</th></tr></thead>';
    $('#lst_transacciones').html( html );
    send = {};
    send.metodo = "cargarTransaccionesPorMotorizado";
    send.idMotorizado = idMotorizado;
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
        success: function ( response ) {
            console.log( "Transacciones: ", response );
            if ( response.registros > 0 ) {
                for ( i = 0; i < response.registros; i++ ) {
                    html = '<tr><td class="text-center">' + response[i]['codigo'] + '</td><td>' + response[i]['cliente'] + '</td><td>' + response[i]['telefono'] + '</td><td>' + response[i]['forma_pago'] + '</td><td>$' + response[i]['total'] + '</td></tr>';
                    $('#lst_transacciones').append( html );
                }
                cargando( false );
            } else {
                html = '<tr><td colspan="5">No tiene pedidos asignados</td></tr>';
                $('#lst_transacciones').append( html );
                cargando( false );
            }
        },
        error: function () {
            cargando( false );
            alert('Error al cargar motorizados...');
        }
    });

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

var seleccionar = function( idMotorolo, motorizado, estado ) {

    $("#lstPeriodoMotorizados tr").removeClass("success");
    $("#" + idMotorolo).addClass("success");

    if ( estado == 'Asignado' ) {
        $("#lstPeriodoMotorizados_length").show();
        $("#lstPeriodoMotorizados_length").html('<button id="btnEstGuardarCambios" class="btn btn-primary" type="button" onclick="cambiarEstado(\'' + idMotorolo + '\', \'' + motorizado + '\', \'' + estado + '\')"><i class="glyphicon glyphicon-floppy-saved mr10"></i>Poner en Camino</button>');
    } else if ( estado == 'EnCamino' ) {
        $("#lstPeriodoMotorizados_length").show();
        $("#lstPeriodoMotorizados_length").html('<button id="btnEstGuardarCambios" class="btn btn-primary" type="button" onclick="cambiarEstado(\'' + idMotorolo + '\', \'' + motorizado + '\', \'' + estado + '\')"><i class="glyphicon glyphicon-check mr10"></i>Confirmar Entrega</button>');
    } else {
        // 
        console.log('estamos con esto');

        impresionDesasignacionMotorizado(idMotorolo);
        $("#lstPeriodoMotorizados_length").hide();
    }

}

var cambiarEstado = function( idMotorolo, motorizado, estado ) {

    console.log(idMotorolo, motorizado, estado);

    var mensaje = 'ENTREGADO';
    var metodo = 'cambiarEstadoPedidoAEntregado';
    if ( estado == 'Asignado' ) {
        metodo = 'cambiarEstadoPedidoAEnCamino';
        mensaje = 'EN CAMINO';
        
    }

    alertify.confirm( "¿Estás seguro de colocar los pedidos de " + motorizado + " a estado " + mensaje + "?" , function ( e ) {
        if ( e ) {
            send = {};
            send.metodo = metodo;
            send.idPeriodo = data.idPeriodo;
            send.idMotorizado = idMotorolo;
            params_ajax.data = send;
            $.ajax({
                ...params_ajax,
                success: function ( response ) {
                    console.log( "Respuesta: ", response );

                    if ( response.estado > 0 ) {
                        actualizarEventosTransaccionesAsignadas(idMotorolo);
                        cargarPeriodoAbierto();
                        $("#lstPeriodoMotorizados_length").hide();

                        alertify.alert( response.mensaje );

                    } else {
                        alertify.error( 'Error: ' + response.mensaje );
                        cargando( false );
                    }
                },
                error: function () {
                    cargando( false );
                    alert('Error al actualizar transaccion...');
                }
            });
        }
    });

}


var impresionDesasignacionMotorizado = function(idMotorolo){
    $("#motorizadoCorteCaja").val('');
    send = {};
    send.metodo = "impresionDesasignacionMotorizado";
    send.id_motorizado =idMotorolo;
    send.id_periodo = data.idPeriodo;
    call_data.data = send;
    $.ajax({
        ...call_data,
        success: function ( response ) {
            
            $("#motorizadoCorteCaja").val(response);
            $('#mdl_cerrarmotorizados').modal('show');
        },
        error: function () {
            cargando( false );
            alert('Error al cargar cierre de Motorizado...');
        }
    });

  }
