
var motorizadosActivos = [];
var motorizadosActivosCentralizados = [];
var tiposDocumentoGlobal = [];
var agregadoresArray = [];
var motorolo = {};



$(document).ready(function () {
    cargarPeriodoAbierto();
    cargarPedidosEntregados();
    agregadores();
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
    url: "../adminPedidosDomicilio/config_pedidosdomicilio.php",
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
                alertify.alert("No existe un periodo abierto.");
            }
        },
        error: function () {
            cargando( false );
            alert('Error al cargar periodo abierto...');
        }
    });

};

var cargarMotorizadosAsignadosPeriodo = function() {

    var idMotorizado = $("#listado_pedido_app").find("tr.success").attr("idMotorizado");

    var html = '<thead><tr class="active"><th class="text-center">Documento</th><th class="text-center">Motorizado</th><th class="text-center">Telefono</th><th class="text-center">Total Ordenes</th><th class="text-center">Estado</th></tr></thead>';
    $('#lstPeriodoMotorizados').html( html );
    send = {};
    send.metodo = "cargarMotorizados";
    send.idPeriodo = data.idPeriodo;
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
        success: function ( response ) {
            console.log( "Motorizados: ", response );
            if ( response.registros > 0 ) {
                for ( i = 0; i < response.registros; i++ ) {
                    if(response[i]['idMotorizado'] !== idMotorizado){
                        html = '<tr id="' + response[i]['idMotorizado'] + '" onclick="seleccionar(\'' + response[i]['idMotorizado'] + '\', \'' + response[i]['motorizado'] + '\', \'' + response[i]['estado'] + '\')"><td class="text-center">' + response[i]['documento'] + '</td><td>' + response[i]['motorizado'] + '</td><td class="text-center">' + response[i]['telefono'] + '</td><td class="text-center">' + response[i]['total'] + '/' + response[i]['maximo_ordenes'] + '</td><td class="text-center">' + response[i]['estado'] + '</td></tr>';
                        $('#lstPeriodoMotorizados').append( html );
                    }
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

    if ( estado == 'Asignado' || estado == 'EnCamino' ) {
        $("#lstPeriodoMotorizados_length").show();
        $("#lstPeriodoMotorizados_length").html('<button id="btnEstGuardarCambios" class="btn btn-primary" type="button" onclick="fn_asignarMotorizado(\'' + idMotorolo + '\', \'' + motorizado + '\', \'' + estado + '\')"><i class="glyphicon glyphicon-check mr10"></i>Asignar Motorizado</button>');
    } else {
        $("#lstPeriodoMotorizados_length").hide();
    }

}


var cargarPedidosEntregados = function () {

   

    var html = "";
    $("#listado_pedido_app").html(html);
    send = { metodo: "cargarPedidosEntregados" };
    send.idPeriodo = data.idPeriodo;
    send.estado = "ENTREGADO";
    (send.estadoBusqueda = "ENTREGADO"), (send.parametroBusqueda = "");
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
      success: function (datos) {
          console.log('hi');
        if (datos.registros > 0) {
            var html = '<thead><tr class="active"><th class="text-center">Medio</th><th class="text-center">Transacción</th><th class="text-center">Cliente</th><th class="text-center">Motorizado</th><th class="text-center">T. Espera</th></tr></thead>';
            $("#listado_pedido_app").append(html);
          cargarLista(datos);
        } else {
          $("#listado_pedido_app").append(
            '<li class="datos_app"><div>No existen pedidos.</div></li>'
          );
        }
       // obtenerCantidadEstadosPedidosApp();
      },
    });
  };

  function cargarLista(datos) {

   
    for (var i = 0; i < datos.registros; i++) {
      // verifico que tiempo indicar
      var tiempo = 0;
      var tfecha = 0;

  if( !existeAgregador(datos[i]["motorizado"].trim())){
      if (datos[i]["tiempo"] != null) {
        tiempo = datos[i]["tiempo"];
        tfecha = tiempo;
      }
  
        var html =
        '<tr class="datos_app" ' +
        'id="' +
        datos[i]["codigo_app"] +
        '" tiempo="' +
        datos[i]["tiempo"] +
        '" codigo_fac="' +
        datos[i]["codigo"] +
        '" documento="' +
        datos[i]["documento"] +
        '" formapago="' +
        datos[i]["forma_pago"] +
        '" medio="' +
        datos[i]["medio"] +
        '" observacion="' +
        datos[i]["observacion"] +
        '" adicional="' +
        datos[i]["datos_envio"] +
        '" direccion="' +
        datos[i]["direccion_despacho"] +
        '" total="' +
        datos[i]["total"] +
        '" fecha="' +
        datos[i]["fecha"] +
        '" telefono="' +
        datos[i]["telefono"] +
        '" cliente="' +
        datos[i]["cliente"] +
        '"  idMotorizado="' +
        datos[i]["idMotorizado"] +
        '"  motorizado="' +
        datos[i]["motorizado"] +
        '" motorizado_telefono="' +
        datos[i]["motorizado_telefono"] +
        '" estado="' +
        datos[i]["estado"] +
        '" cambio_estado="' +
        datos[i]["cambio_estado"] +
        '" codigo_externo="' +
        datos[i]["codigo_externo"] +
        '" codigo_factura="' +
        datos[i]["codigo_factura"] +
        '" onclick="seleccionarPedido(this)" ' +
        '" ondblclick="fn_accionMotorizado(this)" ><td class="lista_medios">' +
        datos[i]["medio"] +
        '</td><td class="codigo_app"><b>' +
        datos[i]["codigo"] +
        '</b></td><td class="cliente_app">' +
        datos[i]["cliente"] +
        '</td><td class="lista_motorizado">' +
        datos[i]["motorizado"].substr(0, 20) +
        '</td><td class="lista_tiempo">' +
        formatoHorasMinutos( tiempo ) +
        "</td></tr>";
      $("#listado_pedido_app").append(html);
    }
     // $("#listado_pedido_app").children().css("background-color",datos[i]["color_fila"]);
     // $("#listado_pedido_app").children().css("color",datos[i]["color_texto"]);
    }
  }
  
  function formatoHorasMinutos( tiempo ) {
    var horas = 0;
    var minutos = tiempo%60;
    if( tiempo%60 > 0 ) {
      minutos = tiempo%60;
        horas = Math.trunc(tiempo/60);
    } else {
      horas = Math.trunc(tiempo/60);
    }
    return completarCeros(horas) + 'h' + completarCeros(minutos);
  }
  
  function completarCeros ( numero ) {
      valor = numero;
      if ( numero < 10 ) {
          numero = '0' + numero;
      }
      return numero;
  }

  var seleccionarPedido = function ( e ) {
    $("#listado_pedido_app tr").removeClass("success");
    $(e).addClass("success");
  };

  var fn_accionMotorizado = function ( e ) {
   
    var codigo = $(e).attr("codigo_fac");
    var header = "<h3>Reasignar Pedido #"+codigo+"</h3>";
   
    $("#asignarHeader").html(header);
        cargarMotorizadosAsignadosPeriodo();
      $("#modalAsignarMotorizado").modal("toggle");
   
  };

  var fn_asignarMotorizado = function ( idM,m,e ) {
    var code = $("#listado_pedido_app").find("tr.success").attr("codigo_fac");
    var idMotorizado = idM;
    var motorizado = m;
    var codigo = $("#listado_pedido_app").find("tr.success").attr("id");
  
    alertify.confirm("¿Desea Asignar el pedido: " + code + " al motorizado: " + motorizado + "?",
      function (e) {
        if (e) {
          asignarMotorizado( idMotorizado, codigo );
    
        } else {
          
        }
      }
    );
  };

  
var asignarMotorizado = function (idMotorizado, codigo_app) {
    var html = "";
    send = { metodo: "asignarMotorizado" };
    send.codigo_app = codigo_app;
    send.idMotorizado = idMotorizado;
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
      success: function (datos) {
        if ( datos[0]["estado"] === 1 ) {
          
          alertify.success(datos[0]["mensaje"]);
          cargarPedidosEntregados();
          
        } else {
          alertify.error(datos[0]["mensaje"]);
          //$("#modalAsignarMotorizado").modal("toggle");
        }
      },
    });
  };
  var agregadores = function () {

    send = { metodo: "agregadores"};
    params_ajax.data = send;
    $.ajax({
        ...params_ajax,
      success: function (datos) {
      agregadoresArray=datos;
      },
    });
  };
  var existeAgregador =function(medio){
   for(var x=0;x<agregadoresArray.length;x++){
     if (agregadoresArray[x]['descripcion']==medio)
     return 1;
   }
   return 0
   };
