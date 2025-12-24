
///////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Aldo Navarrete///////////////////////
////////DESCRIPCION: Clase para mostrar los pedidos y   ///////
////////             detalle de PICKUP desde el servicio///////
////////             centralizado                       ///////
//////////////////////////////// //////////////////////////////
///////FECHA CREACION: 29-AGOSTO-2020//////////////////////////
//////////////////////////////////////////////////////////////

var HabilitarRPM = false;

function SetHabilitadoRPM(dato) {
    HabilitarRPM = dato;
}

function GetHabilitadoRPM() {
    return HabilitarRPM;
}

var TiempoRPM = false;

function SetTiempoRPM(dato) {
    TiempoRPM = dato;
}

function GetTiempoRPM() {
    return TiempoRPM;
}

var tiempoEsperaCentral;

 
var api = {
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../reportes/pedidos/apiPedido.php",
    data: null
};

var dataEnMemoria = [];

$(document).ready(function () {

    console.log('PICKUP CENTRAL !!!!');
    var send = {};
    send.metodo = "aplicaPickup";
    api.data = send;

    $.ajax({
        ...api,
        success: function(datos) {

            console.log('Aplica Pickup Cental !!!!!');
            console.log(datos)

            if(datos && datos == 1){

                cargarUrlServidorCentral();

                $("#pnl_detalle_central").shortscroll();
                $('#pnl_detalle_central').hide();
                $("#pnl_principal_central").shortscroll();
            
            
                $('#inp_identificacion_central').focus(function(){
                    cerrarTeclado();
                    fn_alfaNumerico("#inp_identificacion_central");
                    $("#keyboard").show();
                    $("#btn_cancelar_teclado").attr("onclick","cerrarTeclado()");
                    $("#btn_ok_teclado").attr("onclick","cerrarTeclado()");
            
                });
            
                $('#inp_codigo_app_central').focus(function(){
                    cerrarTeclado();
                    fn_alfaNumerico("#inp_codigo_app_central");
                    $("#keyboard").show();
                    $("#btn_cancelar_teclado").attr("onclick","cerrarTeclado()");
                    $("#btn_ok_teclado").attr("onclick","cerrarTeclado()");
                });
            
                buscarCentral();
                fn_ResfrescarCentral();    

            }

        
        },error: function (e1, e2, e3) {
            console.log('=================================');
            console.log('ERROR en Obtener politica de Aplica Pickup');
            console.log('=================================');
            console.log(e1);
            console.log(e2);
            console.log(e3);
        }



    });
});


function fn_ResfrescarCentral() {
    var send = {"ResfrescarPanelMesas": 1};
    send.rst_id = $("#txtRest").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "config_UserMesas.php",
        data: send,
        success: function(datos) {

            if (datos.str > 0 && datos[0] && datos[0]['tiempo']) {
                SetHabilitadoRPM(datos[0]['habilitado']);
                SetTiempoRPM(datos[0]['tiempo'] * 3);
                if (GetHabilitadoRPM() === 'Si') {
                    if(tiempoEsperaCentral){
                        clearInterval(tiempoEsperaCentral);
                    }
                    tiempoEsperaCentral = setInterval(buscarCentral, GetTiempoRPM());
                }
            
            } else {
                SetHabilitadoRPM(false);
                SetTiempoRPM(0);
            }
        }
    });
}


var cargarUrlServidorCentral = function(){
    send = {};
    send.metodo = 'urlServidorCentral';
    api.data = send;

    $.ajax({
        ...api,
        success: function (response) {

            $('#urlServidorCentral').val(response);
            console.log('Respuesta Servidor Central');
            console.log(response);


        },
        error: function (e1, e2, e3) {
            console.log('=================================');
            console.log('ERROR en Busqueda URL Servidor Centralizado');
            console.log('=================================');
            console.log(e1);
            console.log(e2);
            console.log(e3);
        }
    })

}

var buscarCentral = function () {
    console.log('Buscando Central');
    cerrarTeclado();

    var html = '';
    // let ingresado = $('#chk_ingresado').prop('checked');
    // let preparando = $('#chk_preparando').prop('checked');
    // let listo = $('#chk_listo').prop('checked');
    // let entregado = $('#chk_entregado').prop('checked');
    let identificacionCentral = $('#inp_identificacion_central').val();
    let codigoAppCentral = $('#inp_codigo_app_central').val();


    $("#tbl_pedidos_central").empty();

    html += '<thead> ';
    html += '<tr> ';
    html += '<th align="center" class="tdCodigoApp">';
    html += 'Código App';
    html += '</th>';
    html += '<th align="center">';
    html += 'Cliente';
    html += '</th>';
    html += '<th align="center">';
    html += 'Telf.';
    html += '</th>';
    html += '<th align="center">';
    html += 'Hora pedido';
    html += '</th>';
    html += '<th align="center">';
    html += 'Hora pickup';
    html += '</th>';
    html += '<th align="center">';
    html += 'Tipo Pago';
    html += '</th>';
    html += '<th align="center">';
    html += 'Estado';
    html += '</th>';
    html += '</tr>';
    html += '</thead> ';

    $('#tbl_pedidos_central').append(html);
    html = '';

    // if (!ingresado && !preparando && !listo && !entregado) {
    //     return;
    // }

    send = {};
    send.metodo = 'buscarCentral';
    // send.ingresado = ingresado;
    // send.preparando = preparando;
    // send.listo = listo;
    // send.entregado = entregado;
    send.identificacion = identificacionCentral;
    send.codigoApp = codigoAppCentral;
    send.url = $('#urlServidorCentral').val();
    api.data = send;

    $.ajax({
        ...api,
        success: function (response) {

            console.log('Respuesta Centralizado');
            console.log(response);

            if (response) {
                if (response.data && response.data.length > 0) {

                    let { data } = response;
                    dataEnMemoria = [];
                    dataEnMemoria = data;

                    const totalErrors = data.filter(itemError => itemError.state == 'rechazado-restaurante');

                    console.log('TOTAL ERRORES');
                    console.log(totalErrors);

                    if(totalErrors){
                        $('#lbl_cantidad_error').text(totalErrors.length);
                    }else{
                        $('#lbl_cantidad_error').text(0);
                    }


                    for (var i = 0; i < data.length; i++) {
                        if (data[i].state === 'rechazado-restaurante') {
                        html += '<tr class="fila-pedidos-pickup" id="' + data[i]._id + '" codigo_app_pickup="'+ data[i].codigoApp +'" estado_pedido_pickup="'+data[i].stateMessage+'"  ondblclick="seleccionPedidoCentral(\'' + data[i]._id + '\')" > ';
                        html += '<td>';
                        html += data[i].codigo_app;
                        html += '</td>';
                        html += '<td>';
                        html += data[i].request.cli_nombres;
                        html += '</td>';
                        html += '<td>';
                        html += data[i].request.cli_telefono;
                        html += '</td>';
                        html += '<td align="center">';
                        html += data[i].created_at.split('T')[1].slice(0, 5);
                        html += '</td>';
                        html += '<td align="center">';
                        html += data[i].request.fecha_hora_pickup.split(' ')[1].slice(0, 5);
                        html += '</td>';
                        html += '<td align="center">';
                        html += data[i].request.tipo_pago.toUpperCase();
                        html += '</td>';
                        html += '<td align="center" class="rp_celda_estado ';
                        if (data[i].state === 'rechazado-restaurante') {
                            html += 'rp_color_rechazado" >';
                        } else {
                            html += 'rp_color_listo" >';
                        }

                        html += data[i].stateMessage;
                        html += '</td>';
                        html += '</tr>';

                        $('#tbl_pedidos_central').append(html);
                        html = '';
                    }
                    }


                } else {
                    $('#lbl_cantidad_error').text(0);
                    if (response.response && response.response == 'ERROR') {
                        alertify.error(response.mensaje);
                    }
                }
            }

        },
        error: function (e1, e2, e3) {
            console.log('=================================');
            console.log('ERROR en Busqueda Centralizado');
            console.log('=================================');
            console.log(e1);
            console.log(e2);
            console.log(e3);
        }
    })

}

var seleccionPedidoCentral = function (_id) {


    if (_id) {
        $('#pnl_principal_central').hide();
        $('#pnl_detalle_central').show();

        const pedidoSeleccionado = dataEnMemoria.find(element => element._id == _id);
        cargaDatosPrincipalesCentral(pedidoSeleccionado);
        cargaDetalleImpresionCentral(pedidoSeleccionado.codigo_app);

    }
}


var cargaDatosPrincipalesCentral = function (response) {

    var htmlFp = '';
    var htmlAutorizaciones = '';
    console.log('RESPONSE');
    console.log(response);
    //Estado Cabecera
    $('#lbl_detalle_cabecera_estado_central').text(response.stateMessage);
    $('#lbl_detalle_cabecera_estado_mensaje_central').text(response.stateTechnicalMessage);
    if (response.state === 'rechazado-restaurante') {
        $('#lbl_detalle_cabecera_estado_central').removeClass('rp_color_letra_ingresado');
        $('#lbl_detalle_cabecera_estado_central').removeClass('rp_color_letra_preparando');
        $('#lbl_detalle_cabecera_estado_central').removeClass('rp_color_letra_listo');
        $('#lbl_detalle_cabecera_estado_central').removeClass('rp_color_letra_entregado');
        $('#lbl_detalle_cabecera_estado_central').removeClass('rp_color_letra_ingresado');
        $('#lbl_detalle_cabecera_estado_central').addClass('rp_color_letra_rechazado');

        $('#lbl_detalle_cabecera_estado_mensaje_central').removeClass('rp_color_letra_ingresado');
        $('#lbl_detalle_cabecera_estado_mensaje_central').removeClass('rp_color_letra_preparando');
        $('#lbl_detalle_cabecera_estado_mensaje_central').removeClass('rp_color_letra_listo');
        $('#lbl_detalle_cabecera_estado_mensaje_central').removeClass('rp_color_letra_entregado');
        $('#lbl_detalle_cabecera_estado_mensaje_central').removeClass('rp_color_letra_ingresado');
        $('#lbl_detalle_cabecera_estado_mensaje_central').addClass('rp_color_letra_rechazado');

    } else if (response.state === 'recibido-restaurante') {
        $('#lbl_detalle_cabecera_estado_central').removeClass('rp_color_letra_ingresado');
        $('#lbl_detalle_cabecera_estado_central').removeClass('rp_color_letra_preparando');
        $('#lbl_detalle_cabecera_estado_central').removeClass('rp_color_letra_listo');
        $('#lbl_detalle_cabecera_estado_central').removeClass('rp_color_letra_entregado');
        $('#lbl_detalle_cabecera_estado_central').removeClass('rp_color_letra_ingresado');
        $('#lbl_detalle_cabecera_estado_central').removeClass('rp_color_letra_rechazado');
        $('#lbl_detalle_cabecera_estado_central').addClass('rp_color_letra_listo');


        $('#lbl_detalle_cabecera_estado_mensaje_central').removeClass('rp_color_letra_ingresado');
        $('#lbl_detalle_cabecera_estado_mensaje_central').removeClass('rp_color_letra_preparando');
        $('#lbl_detalle_cabecera_estado_mensaje_central').removeClass('rp_color_letra_listo');
        $('#lbl_detalle_cabecera_estado_mensaje_central').removeClass('rp_color_letra_entregado');
        $('#lbl_detalle_cabecera_estado_mensaje_central').removeClass('rp_color_letra_ingresado');
        $('#lbl_detalle_cabecera_estado_mensaje_central').removeClass('rp_color_letra_rechazado');
        $('#lbl_detalle_cabecera_estado_mensaje_central').addClass('rp_color_letra_listo');

    }


    // //Datos de Pedido
    $("#detalle_codigo_app_central").text(response.codigo_app);
    $("#detalle_restaurante_central").text(response.restaurante.rst_cod_tienda);
    $("#detalle_tipo_servicio_central").text(response.request.servicio_pickup);
    $("#detalle_tipo_pickup_central").text(response.request.tipo_pickup);

    // //Datos de Cliente
    $("#detalle_cliente_nombre_central").text((response.request.cli_nombres == null) ? '' : response.request.cli_nombres);
    $("#detalle_cliente_direccion_central").text((response.request.cli_direccion) == null ? '' : response.request.cli_direccion);
    $("#detalle_cliente_email_central").text((response.request.cli_email == null) ? '' : response.request.cli_email);
    $("#detalle_cliente_telefono_central").text((response.request.cli_telefono == null) ? '' : response.request.cli_telefono);

    //Formas de Pago
    $("#tbl_detalle_formas_pago_central").empty();
    $("#tbl_detalle_autorizaciones_pago_central").empty();


    // //Datos de Pago
    if (response.request.formaspago) {
        if (response.request.formaspago.length > 0) {

            for (var i = 0; i < response.request.formaspago.length; i++) {
                htmlFp += '<tr> ';
                htmlFp += '<td>';
                htmlFp += '<b>Valor:</b>';
                htmlFp += '</td>';
                htmlFp += '<td>';
                htmlFp += response.request.formaspago[i].fpf_total_pagar;
                htmlFp += '</td>';
                htmlFp += '<td>';
                htmlFp += '<b>Bin:</b>';
                htmlFp += '</td>';
                htmlFp += '<td>';
                htmlFp += (response.request.formaspago[i].bin == null) ? '' : response.request.formaspago[i].bin;
                htmlFp += '</td>';
                htmlFp += '</tr>';

                $('#tbl_detalle_formas_pago_central').append(htmlFp);
                htmlFp = '';
            }

        } else {
            htmlFp += '<tr> ';
            htmlFp += '<td style="background-color: #f2dede;">';
            htmlFp += '<b>NO EXISTEN REGISTROS</b>';
            htmlFp += '</td>';
            htmlFp += '</tr>';
            $('#tbl_detalle_formas_pago_central').append(htmlFp);
            htmlFp = '';
        }
    } else {
        htmlFp += '<tr> ';
        htmlFp += '<td style="background-color: #f2dede;">';
        htmlFp += '<b>NO EXISTEN REGISTROS</b>';
        htmlFp += '</td>';
        htmlFp += '</tr>';
        $('#tbl_detalle_formas_pago_central').append(htmlFp);
        htmlFp = '';
    }


    //Datos de Autorizaciones de Pago
    if (response.request.autorizaciones) {
        if (response.request.autorizaciones.length > 0) {

            for (var i = 0; i < response.request.autorizaciones.length; i++) {
                htmlAutorizaciones += '<tr> ';
                htmlAutorizaciones += '<td>';
                htmlAutorizaciones += '<b>Código:</b>';
                htmlAutorizaciones += '</td>';
                htmlAutorizaciones += '<td>';
                htmlAutorizaciones += response.request.autorizaciones[i].Autorizacion;
                htmlAutorizaciones += '</td>';
                htmlAutorizaciones += '</tr> ';

                htmlAutorizaciones += '<tr> ';
                htmlAutorizaciones += '<td>';
                htmlAutorizaciones += '<b>Mensaje respuesta:</b>';
                htmlAutorizaciones += '</td>';
                htmlAutorizaciones += '<td>';
                htmlAutorizaciones += response.request.autorizaciones[i].MensajeRespuestaAut;
                htmlAutorizaciones += '</td>';
                htmlAutorizaciones += '</tr> ';

                htmlAutorizaciones += '<tr> ';
                htmlAutorizaciones += '<td>';
                htmlAutorizaciones += '<b>Tarjetahabiente:</b>';
                htmlAutorizaciones += '</td>';
                htmlAutorizaciones += '<td>';
                htmlAutorizaciones += response.request.autorizaciones[i].TarjetaHabiente;
                htmlAutorizaciones += '</td>';
                htmlAutorizaciones += '</tr> ';

                htmlAutorizaciones += '<tr> ';
                htmlAutorizaciones += '<td>';
                htmlAutorizaciones += '<b>Pasarela pago:</b>';
                htmlAutorizaciones += '</td>';
                htmlAutorizaciones += '<td>';
                htmlAutorizaciones += response.request.autorizaciones[i].PasarelaPago;
                htmlAutorizaciones += '</td>';
                htmlAutorizaciones += '</tr> ';

                htmlAutorizaciones += '<tr> ';
                htmlAutorizaciones += '<td>';
                htmlAutorizaciones += '<b>Tipo tarjeta:</b>';
                htmlAutorizaciones += '</td>';
                htmlAutorizaciones += '<td>';
                htmlAutorizaciones += response.request.autorizaciones[i].TipoTarjeta;
                htmlAutorizaciones += '</td>';
                htmlAutorizaciones += '</tr> ';

                htmlAutorizaciones += '<tr> ';
                htmlAutorizaciones += '<td>';
                htmlAutorizaciones += '<b>Fecha - Hora:</b>';
                htmlAutorizaciones += '</td>';
                htmlAutorizaciones += '<td>';
                htmlAutorizaciones += response.request.autorizaciones[i].FechaTransaccion + ' ' + response.request.autorizaciones[i].HoraTransaccion;
                htmlAutorizaciones += '</td>';
                htmlAutorizaciones += '</tr> ';


                $('#tbl_detalle_autorizaciones_pago_central').append(htmlAutorizaciones);
                htmlAutorizaciones = '';
            }
        } else {

            htmlAutorizaciones += '<tr> ';
            htmlAutorizaciones += '<td style="background-color: #f2dede;">';
            htmlAutorizaciones += '<b>NO EXISTEN REGISTROS</b>';
            htmlAutorizaciones += '</td>';
            htmlAutorizaciones += '</tr>';
            $('#tbl_detalle_autorizaciones_pago_central').append(htmlAutorizaciones);
            htmlAutorizaciones = '';
        }
    } else {
        htmlAutorizaciones += '<tr> ';
        htmlAutorizaciones += '<td style="background-color: #f2dede;">';
        htmlAutorizaciones += '<b>NO EXISTEN REGISTROS</b>';
        htmlAutorizaciones += '</td>';
        htmlAutorizaciones += '</tr>';
        $('#tbl_detalle_autorizaciones_pago_central').append(htmlAutorizaciones);
        htmlAutorizaciones = '';
    }

    //Datos encabezado panel Orden Pedido
    $("#detalle_encabezado_orden_pedido_id_central").text((response._id == null) ? '' : response._id);

    //Carga de Informacion Factura Central
    cargaInformacionFacturaCentral(response.codigo_app, response.state, response.request.fecha_hora_pickup);

    //Carga de Informacion de Productos Pedido
    cargaInformacionProductosPedidoCentral(response);




}


var cargaInformacionFacturaCentral = function (codigoApp, estado, fechaPickup) {

    send = {};
    send.metodo = 'cargarInformacionFacturaCentral';
    send.codigoApp = codigoApp;
    send.estado = estado;
    api.data = send;

    $.ajax({
        ...api,
        success: function (response) {

            //Datos de Factura
            let fechaFacturaConFormato = '';
            if (response.facturaFechaInsercion && response.facturaFechaInsercion.date) {
                fechaFacturaConFormato = response.facturaFechaInsercion.date.substring(0, 15);
            }


            if((response.facturaId == null)){
                $("#df_id_central").hide();
                $("#df_fecha_insercion_central").hide();
                $("#df_valor_total_central").hide();
                $("#df_valor_neto_central").hide();
                $("#df_valor_iva_central").hide();
                $("#df_valor_descuento_central").hide();
                $("#df_rechazado_central").show();
            }else{

                $("#df_id_central").show();
                $("#df_fecha_insercion_central").show();
                $("#df_valor_total_central").show();
                $("#df_valor_neto_central").show();
                $("#df_valor_iva_central").show();
                $("#df_valor_descuento_central").show();
                $("#df_rechazado_central").hide();

                $("#detalle_factura_id_central").text((response.facturaId == null) ? 'NO EXISTE FACTURA ASOCIADA' : response.facturaId);
                $("#detalle_factura_fecha_central").text((response.facturaId == null) ? 'NO EXISTE FACTURA ASOCIADA' : fechaFacturaConFormato);
                $("#detalle_factura_valor_total_central").text((response.facturaId == null) ? 'NO EXISTE FACTURA ASOCIADA' : response.facturaTotal);
                $("#detalle_factura_valor_neto_central").text((response.facturaId == null) ? 'NO EXISTE FACTURA ASOCIADA' : response.facturaSubtotal);
                $("#detalle_factura_valor_iva_central").text((response.facturaId == null) ? 'NO EXISTE FACTURA ASOCIADA' : response.facturaIva);
                $("#detalle_factura_valor_descuento_central").text((response.facturaId == null) ? 'NO EXISTE FACTURA ASOCIADA' : response.facturaDescuento);
    
            }


            cargaDetalleFacturaCentral(response.facturaId);


            //Datos encabezado panel Factura
            $("#detalle_encabezado_factura_id_central").text((response.facturaId == null) ? 'NO SE GENERÓ ID DE FACTURA' : response.facturaId);
            $("#detalle_encabezado_hora_pickup_central").text((fechaPickup == null) ? '' : fechaPickup);


        },
        error: function (e1, e2, e3) {
            console.log(e1);
            console.log(e2);
            console.log(e3);
        }
    });


}

var cargaDetalleFacturaCentral = function(idFactura){
    $("#tbl_detalle_factura_pickup_central").empty();
    var htmlDetalleFacturaPickup    = '';



    send = {};
    send.metodo     = 'detalleFacturaPickup';
    send.idFactura  = idFactura;
    api.data = send;

    $.ajax({
        ...api,
        success: function (response) {
            if(response){

                htmlDetalleFacturaPickup +=  '<thead>';
                htmlDetalleFacturaPickup +=     '<tr>';
                htmlDetalleFacturaPickup +=         '<th><b>PLU:</b></th>';
                htmlDetalleFacturaPickup +=         '<th><b>MODIF:</b></th>'
                htmlDetalleFacturaPickup +=         '<th><b>NOMBRE:</b></th>';
                htmlDetalleFacturaPickup +=         '<th><b>P. UNI:</b></th>';
                htmlDetalleFacturaPickup +=         '<th><b>CANT.:</b></th>';
                htmlDetalleFacturaPickup +=         '<th><b>TOTAL:</b></th>';
                htmlDetalleFacturaPickup +=     '</tr>';
                htmlDetalleFacturaPickup +=  '</thead>';
                $('#tbl_detalle_factura_pickup_central').append(htmlDetalleFacturaPickup);
                htmlDetalleFacturaPickup = '';
            

                if(response.registros > 0){
                    for (var i = 0; i < response.registros; i++) {      
                        
                        if(response[i].es_producto == 1){
                            htmlDetalleFacturaPickup += '<tr> ';
                            htmlDetalleFacturaPickup +=     '<td> ';
                            htmlDetalleFacturaPickup +=         response[i].plu_id;
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +=     '<td> ';
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +=     '<td> ';
                            htmlDetalleFacturaPickup +=         response[i].plu_descripcion;
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +=     '<td> ';
                            htmlDetalleFacturaPickup +=         response[i].dop_precio.toFixed(2);
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +=     '<td> ';
                            htmlDetalleFacturaPickup +=         response[i].dop_cantidad;
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +=     '<td> ';
                            htmlDetalleFacturaPickup +=         response[i].dop_total;
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +='</tr> ';
                        }else{
                            htmlDetalleFacturaPickup += '<tr> ';
                            htmlDetalleFacturaPickup +=     '<td> ';
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +=     '<td> ';
                            htmlDetalleFacturaPickup +=         response[i].plu_id;
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +=     '<td> ';
                            htmlDetalleFacturaPickup +=         response[i].plu_descripcion;
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +=     '<td> ';
                            htmlDetalleFacturaPickup +=         response[i].dop_precio.toFixed(2);
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +=     '<td> ';
                            htmlDetalleFacturaPickup +=         response[i].dop_cantidad;
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +=     '<td> ';
                            htmlDetalleFacturaPickup +=         response[i].dop_total;
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +='</tr> ';
                        }
        
                        $('#tbl_detalle_factura_pickup_central').append(htmlDetalleFacturaPickup);
                        htmlDetalleFacturaPickup = '';
                    }
    
                }else{
                    htmlDetalleFacturaPickup += '<tr> ';
                    htmlDetalleFacturaPickup += '<td colspan="6" style="background-color: #f86c6b; color:#ffffff;">';
                    htmlDetalleFacturaPickup += '<b>NO SE ENCONTRO DETALLE DE FACTURA</b>';
                    htmlDetalleFacturaPickup += '</td>';
                    htmlDetalleFacturaPickup += '</tr>';
                    $('#tbl_detalle_factura_pickup_central').append(htmlDetalleFacturaPickup);
                    htmlDetalleFacturaPickup = '';
                }


            }else{
                htmlDetalleFacturaPickup += '<tr> ';
                htmlDetalleFacturaPickup += '<td colspan="6" style="background-color: #f86c6b; color:#ffffff;">';
                htmlDetalleFacturaPickup += '<b>NO SE ENCONTRO DETALLE DE FACTURA</b>';
                htmlDetalleFacturaPickup += '</td>';
                htmlDetalleFacturaPickup += '</tr>';
                $('#tbl_detalle_factura_pickup_central').append(htmlDetalleFacturaPickup);
                htmlDetalleFacturaPickup = '';
            }
        },
        error: function (e1, e2, e3) {
            htmlDetalleFacturaPickup += '<tr> ';
            htmlDetalleFacturaPickup += '<td colspan="6" style="background-color: #f86c6b; color:#ffffff;">';
            htmlDetalleFacturaPickup += '<b>NO SE ENCONTRO DETALLE DE FACTURA</b>';
            htmlDetalleFacturaPickup += '</td>';
            htmlDetalleFacturaPickup += '</tr>';
            $('#tbl_detalle_factura_pickup_central').append(htmlDetalleFacturaPickup);
            htmlDetalleFacturaPickup = '';

            console.log(e1);
            console.log(e2);
            console.log(e3);
        }
    });


}


var cargaInformacionProductosPedidoCentral = function (response) {


    if (response) {

        if (response.request && response.request.detalles) {

            var htmlDetalleProductos = '';


            //Poblar Tabla impresion Factura
            $("#tbl_detalle_pedido_producto_central").empty();


            htmlDetalleProductos += '<thead>';
            htmlDetalleProductos += '<tr>';
            htmlDetalleProductos += '<th>PLU</th>'
            htmlDetalleProductos += '<th>MODIF</th>'
            htmlDetalleProductos += '<th>NOMBRE</th>'
            htmlDetalleProductos += '<th>CANTIDAD</th>'
            htmlDetalleProductos += '<th>COMENTARIO</th>'
            htmlDetalleProductos += '</tr>';
            htmlDetalleProductos += '</thead>';
            $('#tbl_detalle_pedido_producto_central').append(htmlDetalleProductos);
            htmlDetalleProductos = '';


            let { detalles } = response.request;

            let detallesConsultar = {
                detalles
            }

            send = {};
            send.metodo = 'informacionProductosPedidoJson';
            send.jsonDetalles = detallesConsultar;
            api.data = send;
            $.ajax({
                ...api,
                success: function (response) {


                    console.log('DETALLES');
                    console.log(detallesConsultar);
                    if (detalles && detalles.length > 0) {

                        for (var i = 0; i < detalles.length; i++) {
                            htmlDetalleProductos += '<tr> ';
                            htmlDetalleProductos += '<td> ';
                            htmlDetalleProductos += detalles[i].plu_id;
                            htmlDetalleProductos += '</td> ';
                            htmlDetalleProductos += '<td> ';
                            htmlDetalleProductos += '</td> ';
                            let nombreProducto = response.find(x => x.producto_id == detalles[i].plu_id).producto_nombre;

                            if (nombreProducto) {
                                htmlDetalleProductos += '<td> ';
                                htmlDetalleProductos += nombreProducto;
                                htmlDetalleProductos += '</td> ';
                            } else {
                                htmlDetalleProductos += '<td class="rp_color_letra_rechazado"> ';
                                htmlDetalleProductos += 'NO EXISTE EN BASE LOCAL';
                                htmlDetalleProductos += '</td> ';
                            }

                            htmlDetalleProductos += '<td align="center"> ';
                            htmlDetalleProductos += detalles[i].dop_cantidad;
                            htmlDetalleProductos += '</td> ';
                            htmlDetalleProductos += '<td> ';
                            htmlDetalleProductos += (detalles[i].comentario == null) ? '' : detalles[i].comentario;
                            htmlDetalleProductos += '</td> ';
                            htmlDetalleProductos += '</tr> ';
                            $('#tbl_detalle_pedido_producto_central').append(htmlDetalleProductos);
                            htmlDetalleProductos = '';

                            if (detalles[i].modificadores && detalles[i].modificadores.length > 0) {
                                for (var j = 0; j < detalles[i].modificadores.length; j++) {
                                    htmlDetalleProductos += '<tr> ';
                                    htmlDetalleProductos += '<td> ';
                                    htmlDetalleProductos += '</td> ';
                                    htmlDetalleProductos += '<td> ';
                                    htmlDetalleProductos += detalles[i].modificadores[j].plu_id;
                                    htmlDetalleProductos += '</td> ';

                                    let nombreProductoModificador = response.find(x => x.producto_id == detalles[i].modificadores[j].plu_id).producto_nombre;

                                    if (nombreProductoModificador) {
                                        htmlDetalleProductos += '<td> ';
                                        htmlDetalleProductos += nombreProductoModificador;
                                        htmlDetalleProductos += '</td> ';
                                    } else {
                                        htmlDetalleProductos += '<td class="rp_color_letra_rechazado"> ';
                                        htmlDetalleProductos += 'NO EXISTE EN BASE LOCAL';
                                        htmlDetalleProductos += '</td> ';
                                    }

                                    htmlDetalleProductos += '<td align="center" > ';
                                    htmlDetalleProductos += detalles[i].modificadores[j].dop_cantidad;
                                    htmlDetalleProductos += '</td> ';
                                    htmlDetalleProductos += '<td> ';
                                    htmlDetalleProductos += '</td> ';
                                    htmlDetalleProductos += '</tr> ';
                                    $('#tbl_detalle_pedido_producto_central').append(htmlDetalleProductos);
                                    htmlDetalleProductos = '';

                                }
                            }

                        }

                    } else {
                        htmlDetalleProductos += '<tr> ';
                        htmlDetalleProductos += '<td colspan="5" style="background-color: #f2dede;">';
                        htmlDetalleProductos += '<b>NO EXISTEN REGISTROS</b>';
                        htmlDetalleProductos += '</td>';
                        htmlDetalleProductos += '</tr>';
                        $('#tbl_detalle_pedido_producto_central').append(htmlDetalleProductos);
                        htmlDetalleProductos = '';

                    }


                },
                error: function (e1, e2, e3) {
                    console.log(e1);
                    console.log(e2);
                    console.log(e3);
                }
            });



        }
    }

}


var cargaDetalleImpresionCentral = function(codigoApp){
    send = {};
    send.metodo = 'detalleImpresionesCentral';
    send.codigoApp = codigoApp;
    api.data = send;

    $.ajax({
        ...api,
        success: function (response) {

            var htmlImpresionFactura    = '';
            var htmlImpresionPedido     = '';


            if(response){

                var listaImpresionFactura = [];
                var listaImpresionPedido = [];


                if(response.registros && response.registros > 0){
                    
                    for (var i = 0; i < response.registros; i++) {
                        if(response[i].canal === 'Factura'){
                            listaImpresionFactura.push(response[i]);
                        }else if(response[i].canal === 'Pedido'){
                            listaImpresionPedido.push(response[i]);
                        }
                    }

                }

                //Poblar Tabla impresion Factura
                $("#tbl_detalle_impresion_factura_central").empty();
 

                htmlImpresionFactura +=  '<thead>';
                htmlImpresionFactura +=     '<tr>';
                htmlImpresionFactura +=         '<td><b>Fecha</b></td>';
                htmlImpresionFactura +=         '<td><b>Estación</b></td>'
                htmlImpresionFactura +=         '<td><b>Estado</b></td>';
                htmlImpresionFactura +=         '<td><b>Impresora</b></td>';
                htmlImpresionFactura +=         '<td class="text-center"><b>Acciones</b></td>'
                htmlImpresionFactura +=     '</tr>';
                htmlImpresionFactura +=  '</thead>';
                $('#tbl_detalle_impresion_factura_central').append(htmlImpresionFactura);
                htmlImpresionFactura = '';


                //Carga de Impresiones de Factura

                if(listaImpresionFactura.length > 0){
                    for (var i = 0; i < listaImpresionFactura.length; i++) {        
        
                        htmlImpresionFactura += '<tr> ';
                        htmlImpresionFactura +=     '<td> ';
                        htmlImpresionFactura +=         listaImpresionFactura[i].fecha.date.substring(0,16);
                        htmlImpresionFactura +=     '</td> ';
                        htmlImpresionFactura +=     '<td> ';
                        htmlImpresionFactura +=         listaImpresionFactura[i].estacion;
                        htmlImpresionFactura +=     '</td> ';
                        htmlImpresionFactura +=     '<td> ';
                        htmlImpresionFactura +=         listaImpresionFactura[i].estado;
                        htmlImpresionFactura +=     '</td> ';
                        htmlImpresionFactura +=     '<td> ';
                        htmlImpresionFactura +=         listaImpresionFactura[i].impresora;
                        htmlImpresionFactura +=     '</td> ';
                        htmlImpresionFactura +=     '<td class="text-center"> ';
                        htmlImpresionFactura +=         '<button class="btn btn-xs btn-info rp_boton_detalle" style= "height: 25px !important; width: 35px !important;" title="Ver Factura" onclick="verDocumento(\''+listaImpresionFactura[i].url+'\')"> ';
                        htmlImpresionFactura +=             '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="15px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">';
                        htmlImpresionFactura +=                 '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>';
                        htmlImpresionFactura +=                 '<circle cx="12" cy="12" r="3"></circle>';
                        htmlImpresionFactura +=             '</svg>';
                        htmlImpresionFactura +=          '</button>';
                        htmlImpresionFactura +=          '<button class="btn btn-xs rp_boton_detalle" style= "height: 25px !important; width: 35px !important; background-color: black; color: white;" title="Reimprimir"  onclick="reimprimir(\''+listaImpresionFactura[i].id+'\')">';
                        htmlImpresionFactura +=             '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="15px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer">';
                        htmlImpresionFactura +=                 '<polyline points="6 9 6 2 18 2 18 9"></polyline>';
                        htmlImpresionFactura +=                 '<path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>';
                        htmlImpresionFactura +=                 '<rect x="6" y="14" width="12" height="8"></rect>';
                        htmlImpresionFactura +=             '</svg>';
                        htmlImpresionFactura +=         '</button>';
                        htmlImpresionFactura +=     '</td> ';
                        htmlImpresionFactura +='</tr> ';

                        $('#tbl_detalle_impresion_factura_central').append(htmlImpresionFactura);
                        htmlImpresionFactura = '';
                    }
    
                }else{
                    htmlImpresionFactura += '<tr> ';
                    htmlImpresionFactura += '<td colspan="5" style="background-color: #f86c6b; color:#ffffff;">';
                    htmlImpresionFactura += '<b>NO SE ENCONTRO IMPRESIONES</b>';
                    htmlImpresionFactura += '</td>';
                    htmlImpresionFactura += '</tr>';
                    $('#tbl_detalle_impresion_factura_central').append(htmlImpresionFactura);
                    htmlImpresionFactura = '';
                }

        
    
                //Poblar Tabla impresion Orden Pedido
                $("#tbl_detalle_impresion_orden_pedido_central").empty();

                htmlImpresionPedido +=  '<thead>';
                htmlImpresionPedido +=     '<tr>';
                htmlImpresionPedido +=         '<td><b>Fecha</b></td>';
                htmlImpresionPedido +=         '<td><b>Estación</b></td>'
                htmlImpresionPedido +=         '<td><b>Estado</b></td>';
                htmlImpresionPedido +=         '<td><b>Impresora</b></td>';
                htmlImpresionPedido +=         '<td class="text-center"><b>Acciones</b></td>'
                htmlImpresionPedido +=     '</tr>';
                htmlImpresionPedido +=  '</thead>';
                $('#tbl_detalle_impresion_orden_pedido_central').append(htmlImpresionPedido);
                htmlImpresionPedido = '';
                
                //Carga de Impresiones de Pedido
                if(listaImpresionPedido.length > 0 ){
                    for (var i = 0; i < listaImpresionPedido.length; i++) {
                        htmlImpresionPedido += '<tr> ';
                        htmlImpresionPedido +=     '<td> ';
                        htmlImpresionPedido +=         listaImpresionPedido[i].fecha.date.substring(0,16);
                        htmlImpresionPedido +=     '</td> ';
                        htmlImpresionPedido +=     '<td> ';
                        htmlImpresionPedido +=         listaImpresionPedido[i].estacion;
                        htmlImpresionPedido +=     '</td> ';
                        htmlImpresionPedido +=     '<td> ';
                        htmlImpresionPedido +=         listaImpresionPedido[i].estado;
                        htmlImpresionPedido +=     '</td> ';
                        htmlImpresionPedido +=     '<td> ';
                        htmlImpresionPedido +=         listaImpresionPedido[i].impresora;
                        htmlImpresionPedido +=     '</td> ';
                        htmlImpresionPedido +=     '<td class="text-center"> ';
                        htmlImpresionPedido +=         '<button class="btn btn-xs btn-info rp_boton_detalle" style= "height: 25px !important; width: 35px !important;" title="Ver Factura" onclick="verDocumento(\''+listaImpresionPedido[i].url+'\')"> ';
                        htmlImpresionPedido +=             '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="15px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">';
                        htmlImpresionPedido +=                 '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>';
                        htmlImpresionPedido +=                 '<circle cx="12" cy="12" r="3"></circle>';
                        htmlImpresionPedido +=             '</svg>';
                        htmlImpresionPedido +=          '</button>';
                        htmlImpresionPedido +=          '<button class="btn btn-xs rp_boton_detalle" style= "height: 25px !important; width: 35px !important; background-color: black; color: white;" title="Reimprimir"  onclick="reimprimir('+listaImpresionPedido[i].id+')">';
                        htmlImpresionPedido +=             '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="15px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer">';
                        htmlImpresionPedido +=                 '<polyline points="6 9 6 2 18 2 18 9"></polyline>';
                        htmlImpresionPedido +=                 '<path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>';
                        htmlImpresionPedido +=                 '<rect x="6" y="14" width="12" height="8"></rect>';
                        htmlImpresionPedido +=             '</svg>';
                        htmlImpresionPedido +=         '</button>';
                        htmlImpresionPedido +=     '</td> ';
                        htmlImpresionPedido +='</tr> ';
                        $('#tbl_detalle_impresion_orden_pedido_central').append(htmlImpresionPedido);
                        htmlImpresionPedido = '';  
                    }    
                }else{
                    htmlImpresionPedido += '<tr> ';
                    htmlImpresionPedido += '<td colspan="5" style="background-color: #f86c6b; color:#ffffff;">';
                    htmlImpresionPedido += '<b>NO SE ENCONTRÓ IMPRESIONES</b>';
                    htmlImpresionPedido += '</td>';
                    htmlImpresionPedido += '</tr>';
                    $('#tbl_detalle_impresion_orden_pedido_central').append(htmlImpresionPedido);
                    htmlImpresionPedido = '';
                }



            }

        },
        error: function (e1, e2, e3) {

            var htmlImpresionFactura    = '';
            var htmlImpresionPedido     = '';


            $("#tbl_detalle_impresion_orden_pedido_central").empty();


            htmlImpresionPedido += '<tr> ';
            htmlImpresionPedido += '<td colspan="5" style="background-color: #f86c6b; color:#ffffff;">';
            htmlImpresionPedido += '<b>NO SE ENCONTRÓ IMPRESIONES</b>';
            htmlImpresionPedido += '</td>';
            htmlImpresionPedido += '</tr>';
            $('#tbl_detalle_impresion_orden_pedido_central').append(htmlImpresionPedido);
            htmlImpresionPedido = '';
            htmlImpresionPedido +=  '<thead>';
            htmlImpresionPedido +=     '<tr>';
            htmlImpresionPedido +=         '<td><b>Fecha</b></td>';
            htmlImpresionPedido +=         '<td><b>Estación</b></td>'
            htmlImpresionPedido +=         '<td><b>Estado</b></td>';
            htmlImpresionPedido +=         '<td><b>Impresora</b></td>';
            htmlImpresionPedido +=         '<td class="text-center"><b>Acciones</b></td>'
            htmlImpresionPedido +=     '</tr>';
            htmlImpresionPedido +=  '</thead>';
            $('#tbl_detalle_impresion_orden_pedido_central').append(htmlImpresionPedido);
            htmlImpresionPedido = '';



            $("#tbl_detalle_impresion_factura_central").empty();
 

            htmlImpresionFactura +=  '<thead>';
            htmlImpresionFactura +=     '<tr>';
            htmlImpresionFactura +=         '<td><b>Fecha</b></td>';
            htmlImpresionFactura +=         '<td><b>Estación</b></td>'
            htmlImpresionFactura +=         '<td><b>Estado</b></td>';
            htmlImpresionFactura +=         '<td><b>Impresora</b></td>';
            htmlImpresionFactura +=         '<td class="text-center"><b>Acciones</b></td>'
            htmlImpresionFactura +=     '</tr>';
            htmlImpresionFactura +=  '</thead>';
            $('#tbl_detalle_impresion_factura_central').append(htmlImpresionFactura);
            htmlImpresionFactura = '';
            htmlImpresionFactura += '<tr> ';
            htmlImpresionFactura += '<td colspan="5" style="background-color: #f86c6b; color:#ffffff;">';
            htmlImpresionFactura += '<b>NO SE ENCONTRÓ IMPRESIONES</b>';
            htmlImpresionFactura += '</td>';
            htmlImpresionFactura += '</tr>';
            $('#tbl_detalle_impresion_factura_central').append(htmlImpresionFactura);
            htmlImpresionFactura = '';

            console.log(e1);
            console.log(e2);
            console.log(e3);
        }
    });

}


var cerrarTeclado = function(){
    $("#keyboard").hide();
}

var regresarAPanelPrincipalCentral = function () {
    $('#pnl_detalle_central').hide();
    $('#pnl_principal_central').show();
}
