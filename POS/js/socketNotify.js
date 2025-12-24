
$(document).ready(function() {
   
    var send = {};
    send.metodo = "configuracionAlertaMedios";
    send.idCadena = $("#txtCadena").val();
    send.idRestaurante = $("#txtRest").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        accept: "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../ordenpedido/config_app.php",
        data: send,
        success: function(datos) {
            if (datos.registros > 0) {
               
               activarSocketNotificacion(datos[0]);
            } else {
                alert("No se ha configurado las politicas para la notificación");
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert("No existen las Politicas para Notificación");
        }
    });


    
});
function activarSocketNotificacion(datos){


    let url_server=datos.url;
    const myArr = url_server.split(":5000");
    let ip_address=myArr[0];

        // let socket_port = '4000';
        // let url = ip_address + ':' + socket_port;
        console.log("Dirección:"+datos.url);

        const socket = io(url_server);
      

    if (datos.activo == 1 && datos.url != "") {

        socket.on('Pedidos',function(data){
		
            if(data.length>0)
            {
                

                if( Array.isArray(data) && data.length ) {
                    console.log(data);
                    notificar(data);
                    habilitarContenedorPedidos();
                }
                    
            }});
       
        socket.on("disconnect", function(data) {
            //$socketActivo.val("0");
            console.log("Desconectado del Socket");
        });
        socket.on("connect_error", function() {
           // $socketActivo.val("0");
            console.log("Falló la conexión al Socket");
        });

        socket.on("notify", function( data ) {
            console.log("notify");
            console.log(data);
           
            // agregarLista(data);
            if( Array.isArray(data) && data.length ) {
                notificar(data);
                // habilitarContenedorPedidosPendientes();
            }
           
        });
        

        //agregarEventoListaOrdenes();
    }
}


function notificar(data) {
    var code = 0;
    data.forEach(element => {
        if(code!=element.codigo){
            console.log('Sonando... '+element.codigo+' '+element.medio);
                var sound = new Audio();
                sound.src = "../sonidos/Agregadores.mpeg";
                sound.play()
                code=element.codigo;
            alertify.success('Nuevo pedido de '+element.medio+' recibido.');
        };
    });
}


function agregarLista(data){
   
if($('#cboEstadoPedido').val()==='PRINCIPAL' && $.trim($('#listado_pedido_app').html())!=='' ){
    var code = 0;
    var HTML = '';
    var count = 0;
    data.forEach(element => {
        if(code!=element.codigo){
            // verifico que tiempo indicar
    var tiempo = 0;
    var tfecha;
    var semaforo;
    // console.log(now);
    var icon ;

    if (element["tiempo"] != null) {
      tiempo = element["tiempo"];
      tfecha = tiempo;
    }
    icon = '<i class="material-icons">two_wheeler</i>';
    
    semaforo = dibujarSemaforo( tiempo );

    html =
      '<li class="datos_app" style="background-color:'+element["color_fila"]+';color:'+element["color_texto"]+'" fecha="' +
      tfecha +
      '" id="' +
      element["codigo_app"] +
      '" codigo_fac="' +
      element["codigo"] +
      '" documento="' +
      element["documento"] +
      '" formapago="' +
      element["forma_pago"] +
      '" medio="' +
      element["medio"] +
      '" observacion="' +
      element["observacion"] +
      '" adicional="' +
      element["datos_envio"] +
      '" direccion="' +
      element["direccion_despacho"] +
      '" total="' +
      element["total"] +
      '" fecha="' +
      element["fecha"] +
      '" telefono="' +
      element["telefono"] +
      '" cliente="' +
      element["cliente"] +
      '"  idMotorizado="' +
      element["idMotorizado"] +
      '"  motorizado="' +
      element["motorizado"] +
      '" motorizado_telefono="' +
      element["motorizado_telefono"] +
      '" estado="' +
      element["estado"] +
      '" cambio_estado="' +
      element["cambio_estado"] +
      '" codigo_externo="' +
      element["codigo_externo"] +
      '" codigo_factura="' +
      element["codigo_factura"] +
      '" onclick="seleccionarPedido(this)"><div class="lista_medios">' +
      element["medio"] +
      '</div><div class="codigo_app"><b>' +
      element["codigo"] +
      '</b></div><div class="cliente_app">' +
      element["cliente"] +
      '</div><div class="lista_motorizado">' +
      element["motorizado"] +
      '</div><div class="lista_estado"> ' +
      icon +
      '</div><div class="lista_semaforo">' +
      semaforo +
      '</div><div class="lista_tiempo">' +
      formatoHorasMinutos( tiempo ) +
      "</div></li>";
   
        count++;
    };
        HTML +=html;
    });
    HTML = HTML + $("#listado_pedido_app").html();
    var n =  Number($("#btn_filtro_app_principal").find("span").attr("badge"));
    $("#btn_filtro_app_principal").find("span").attr("badge",n+count);
    $("#listado_pedido_app").html(html);
}

}