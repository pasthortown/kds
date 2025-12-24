
//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Aldo Navarrete//////////////////////
////////DESCRIPCION: Clase para mostrar los pedidos y  ///////
////////             detalle de PICKUP  //////////////////////
//////////////////////////////////////////////////////////////
///////FECHA CREACION: 17-JULIO-2020//////////////////////////
//////////////////////////////////////////////////////////////
 
var pagina = 1;
var tamanio = 5;
var totalRegistros = 0;


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

var tiempoEspera;

var api = {
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../reportes/pedidos/apiPedido.php",
    data: null
};

$(document).ready(function () {

    console.log('PICKUP LOCAL !!!!');
    var send = {};
    send.metodo = "aplicaPickup";
    api.data = send;

    $.ajax({
        ...api,
        success: function(datos) {

            console.log('Aplica Pickup Local !!!!!');
            console.log(datos);

            if(datos && datos == 1){
                $('#cal_desde').datepicker({
                    format: "dd/mm/yyyy",
                    language: "es",
                    keyboardNavigation: false
                });
            
                $('#cal_hasta').datepicker({
                    format: "dd/mm/yyyy",
                    language: "es",
                    keyboardNavigation: false
                });
            
            
            
                $('#chk_ingresado').change(function () {
                    buscar();
                });
                $('#chk_preparando').change(function () {
                    buscar();
                });
                $('#chk_listo').change(function () {
                    buscar();
                });
                $('#chk_entregado').change(function () {
                    buscar();
                });
                $('#chk_transferido').change(function () {
                    buscar();
                });
                $('#tbl_pedidos').on("click", "tr.filaPrincipal", function (e) {
                    $('.selected').removeClass('seleccionRegistro');
                    $('tr').removeClass('seleccionRegistro');
                    $('filaPrincipal').removeClass('seleccionRegistro');
                    $(this).addClass("seleccionRegistro");
                });
                $( "#dlg_impresion_previa" ).dialog({
                    headerVisible: false,
                    autoOpen: false,
                    modal: true,
                    width: 350,
                    top: 0,
                    resizable: false, draggable: false,
                    show: {
                      effect: "blind",
                      duration: 500
                    },
                    hide: {
                      effect: "blind",
                      duration: 500
                    },
                    open: function (event, ui) {
                        $('.ui-widget-overlay').bind('click', function () {
                            $("#dlg_impresion_previa").dialog('close');
                        });
                    }
    
                });
            
            
                $('#chk_ingresado').attr('checked');
            
                $("#pnl_detalle").shortscroll();
            
                regresarAPanelPrincipal();
                cantidadPorEstado();
                buscar();
            
                $('#btn_anterior').hide();
            
                $('#inp_nombre_cliente').focus(function(){
                    cerrarTeclado();
                    fn_alfaNumerico("#inp_nombre_cliente");
                    $("#keyboard").show();
                    $("#btn_cancelar_teclado").attr("onclick","cerrarTeclado()");
                    $("#btn_ok_teclado").attr("onclick","cerrarTeclado()");
                
                });
            
                $('#inp_identificacion').focus(function(){
                    cerrarTeclado();
                    fn_alfaNumerico("#inp_identificacion");
                    $("#keyboard").show();
                    $("#btn_cancelar_teclado").attr("onclick","cerrarTeclado()");
                    $("#btn_ok_teclado").attr("onclick","cerrarTeclado()");
                
                });
            
                $('#inp_codigo_app').focus(function(){
                    cerrarTeclado();
                    fn_alfaNumerico("#inp_codigo_app");
                    $("#keyboard").show();
                    $("#btn_cancelar_teclado").attr("onclick","cerrarTeclado()");
                    $("#btn_ok_teclado").attr("onclick","cerrarTeclado()");
                });
            
                fn_Resfrescar();
    
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

var detectOS =function() {
    const platform = navigator.platform.toLowerCase(),
        iosPlatforms = ['iphone', 'ipad', 'ipod', 'ipod touch'];

    if (platform.includes('mac')) return 'MacOS';
    if (iosPlatforms.includes(platform)) return 'iOS';
    if (platform.includes('win')) return 'Windows';
    if (/android/.test(navigator.userAgent.toLowerCase())) return 'Android';
    if (/linux/.test(platform)) return 'Linux';

    return 'unknown';
}

function fn_Resfrescar() {
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
                SetTiempoRPM(datos[0]['tiempo']);
                if (GetHabilitadoRPM() === 'Si') {
                    if(tiempoEspera){
                        clearInterval(tiempoEspera);
                    }
                    tiempoEspera = setInterval(buscar, GetTiempoRPM());
                }
            
            } else {
                SetHabilitadoRPM(false);
                SetTiempoRPM(0);
            }
        }
    });
}

var cerrarTeclado = function(){
    $("#keyboard").hide();
}

var buscar = function () {
    console.log('Buscando');
    //cerrarTeclado();
    cantidadPorEstado();

    var html = '';
    let ingresado = $('#chk_ingresado').prop('checked');
    let preparando = $('#chk_preparando').prop('checked');
    let listo = $('#chk_listo').prop('checked');
    let entregado = $('#chk_entregado').prop('checked');
    let transferido = $('#chk_transferido').prop('checked');
    let nombreCliente = $('#inp_nombre_cliente').val();
    let identificacion = $('#inp_identificacion').val();
    let codigoApp = $('#inp_codigo_app').val();


    $("#tbl_pedidos").empty();

    html += '<thead> ';
    html += '<tr> ';
    html += '<th align="center" class="tdCodigoApp fitwidth">';
    html += 'Código App';
    html += '</th>';
    html += '<th align="center" class="fitwidth">';
    html += 'Cliente';
    html += '</th>';
    html += '<th align="center" class="fitwidth">';
    html += 'Telf.';
    html += '</th>';
    html += '<th align="center" class="fitwidth">';
    html += 'Tipo Pago';
    html += '</th>';
    html += '<th align="center" class="fitwidth">';
    html += 'Hora pedido';
    html += '</th>';
    html += '<th align="center" class="fitwidth">';
    html += 'Hora pickup';
    html += '</th>';
    html += '<th align="center" class="fitwidth">';
    html += 'Estado';
    html += '</th>';
    html += '</tr>';
    html += '</thead> ';

    $('#tbl_pedidos').append(html);
    html = '';

    if (!ingresado && !preparando && !listo && !entregado && !transferido) {
        return;
    }

    send = {};
    send.metodo = 'buscar';
    send.pagina = pagina;
    send.tamanio = tamanio;
    send.ingresado = ingresado;
    send.preparando = preparando;
    send.listo = listo;
    send.entregado = entregado;
    send.transferido = transferido;
    send.nombreCliente = nombreCliente;
    send.identificacion = identificacion;
    send.codigoApp = codigoApp;
    api.data = send;

    $.ajax({
        ...api,
        success: function (response) {

            if(response && response.totalRegistros && response.totalRegistros > 0){
            
                totalRegistros = response.totalRegistros;

                if((pagina * tamanio) >= totalRegistros){
                    $('#btn_siguiente').hide();
                }else{
                    $('#btn_siguiente').show();
                }

            }


            if (response && response.registros && response.registros > 0) {

                for (var i = 0; i < response.registros; i++) {
                    html += '<tr class="filaPrincipal" id="' + response[i].id + '" nombre_pickup="'+response[i].cliente+'"  codigo_app_pickup="'+ response[i].codigoApp +'"  estado_pedido_pickup="'+response[i].estado+'" forma_pago_pickup="'+ response[i].tipoPago +'" ondblclick="seleccionPedido(' + response[i].id + ', \'' + response[i].estado + '\' )" > ';
                    html += '<td>';
                    html += response[i].codigoApp;
                    html += '</td>';
                    html += '<td>';
                    html += response[i].cliente;
                    html += '</td>';
                    html += '<td>';
                    html += response[i].telefono;
                    html += '</td>';
                    html += '<td align="center">';
                    html += response[i].tipoPago.toUpperCase() + ' / ' + response[i].tipoTarjeta.toUpperCase();
                    html += '</td>';
                    html += '<td align="center">';
                    html += response[i].horaPedido;
                    html += '</td>';
                    html += '<td align="center">';
                    html += response[i].horaPickup;
                    html += '</td>';
                    html += '<td align="center" class="rp_celda_estado ';
                    if(response[i].estado === 'Ingresado'){
                        html +=  'rp_color_ingresado" >';
                    }else if(response[i].estado === 'Preparando'){
                        html +=  'rp_color_preparando" >';
                    }else if(response[i].estado === 'Listo'){
                        html +=  'rp_color_listo" >';
                    }else if(response[i].estado === 'Entregado'){
                        html +=  'rp_color_entregado" >';
                    }else if(response[i].estado === 'Transferido') {
                        html +=  'rp_color_transferido" >';
                    }
                    html += response[i].estado;
                    html += '</td>';
                    html += '</tr>';

                    $('#tbl_pedidos').append(html);
                    html = '';
                }

            }
        },
        error: function (e1, e2, e3) {
            console.log(e1);
            console.log(e2);
            console.log(e3);
        }
    })

}

var seleccionPedido = function (id, estadoPedido) {

    if (id && id > 0) {
        $('#pnl_principal').hide();
        $('#pnl_detalle').show();

        cargaDatosPrincipales(id, estadoPedido);
        cargaDetalleImpresion(id);

    }
}

var cargaDatosPrincipales = function(id,estadoPedido){
    send = {};
    send.metodo = 'detalle';
    send.kioskoCabeceraId = id;
    send.estadoPedido = estadoPedido;
    api.data = send;

    $.ajax({
        ...api,
        success: function (response) {

            var htmlFp             = '';
            var htmlAutorizaciones = '';

            //Estado Cabecera
            $('#lbl_detalle_cabecera_estado').text(response.estadoPedido);

            if(response.estadoPedido === 'Ingresado'){
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_ingresado'); 
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_preparando'); 
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_listo'); 
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_entregado'); 
                $('#lbl_detalle_cabecera_estado').addClass('rp_color_letra_ingresado');
            }else if(response.estadoPedido === 'Preparando'){
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_ingresado'); 
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_preparando'); 
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_listo'); 
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_entregado'); 
                $('#lbl_detalle_cabecera_estado').addClass('rp_color_letra_preparando');
            }else if(response.estadoPedido === 'Listo'){
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_ingresado'); 
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_preparando'); 
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_listo'); 
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_entregado'); 
                $('#lbl_detalle_cabecera_estado').addClass('rp_color_letra_listo');
            }else if(response.estadoPedido === 'Entregado'){
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_ingresado'); 
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_preparando'); 
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_listo'); 
                $('#lbl_detalle_cabecera_estado').removeClass('rp_color_letra_entregado'); 
                $('#lbl_detalle_cabecera_estado').addClass('rp_color_letra_entregado');
            }



            //Datos de Pedido
            $("#detalle_codigo_app").text(response.jsonPedido.codigo_app);
            $("#detalle_tipo_servicio").text(response.jsonPedido.servicio_pickup);
            $("#detalle_tipo_pickup").text(response.jsonPedido.tipo_pickup);

            //Datos de Cliente
            $("#detalle_cliente_nombre").text(response.jsonPedido.cli_nombres);
            $("#detalle_cliente_direccion").text(response.jsonPedido.cli_direccion);
            $("#detalle_cliente_email").text(response.jsonPedido.cli_email);
            $("#detalle_cliente_telefono").text(response.jsonPedido.cli_telefono);

            $("#tbl_detalle_formas_pago").empty();
            $("#tbl_detalle_autorizaciones_pago").empty();


            //Datos de Pago
            if (response.jsonPedido.formaspago) {
                if (response.jsonPedido.formaspago.length > 0) {

                    for (var i = 0; i < response.jsonPedido.formaspago.length; i++) {
                        htmlFp += '<tr> ';
                        htmlFp += '<td>';
                        htmlFp += '<b>Valor:</b>';
                        htmlFp += '</td>';
                        htmlFp += '<td>';
                        htmlFp += response.jsonPedido.formaspago[i].fpf_total_pagar;
                        htmlFp += '</td>';
                        htmlFp += '<td>';
                        htmlFp += '<b>Bin:</b>';
                        htmlFp += '</td>';
                        htmlFp += '<td>';
                        htmlFp += response.jsonPedido.formaspago[i].bin;
                        htmlFp += '</td>';
                        htmlFp += '</tr>';

                        $('#tbl_detalle_formas_pago').append(htmlFp);
                        htmlFp = '';
                    }



                } else {
                    htmlFp += '<tr> ';
                    htmlFp += '<td style="background-color: #f2dede;">';
                    htmlFp += '<b>NO EXISTEN REGISTROS</b>';
                    htmlFp += '</td>';
                    htmlFp += '</tr>';
                    $('#tbl_detalle_formas_pago').append(htmlFp);
                    htmlFp = '';
                }
            } else {
                htmlFp += '<tr> ';
                htmlFp += '<td style="background-color: #f2dede;">';
                htmlFp += '<b>NO EXISTEN REGISTROS</b>';
                htmlFp += '</td>';
                htmlFp += '</tr>';
                $('#tbl_detalle_formas_pago').append(htmlFp);
                htmlFp = '';
            }


            //Datos de Autorizaciones de Pago
            if (response.jsonPedido.autorizaciones){
                if(response.jsonPedido.autorizaciones.length > 0){

                    for (var i = 0; i < response.jsonPedido.autorizaciones.length; i++) {
                        htmlAutorizaciones += '<tr> ';
                        htmlAutorizaciones +=   '<td>';
                        htmlAutorizaciones +=       '<b>Código:</b>';
                        htmlAutorizaciones +=   '</td>';
                        htmlAutorizaciones +=   '<td>';
                        htmlAutorizaciones +=       response.jsonPedido.autorizaciones[i].Autorizacion;
                        htmlAutorizaciones +=   '</td>';
                        htmlAutorizaciones += '</tr> ';

                        htmlAutorizaciones += '<tr> ';
                        htmlAutorizaciones +=   '<td>';
                        htmlAutorizaciones +=       '<b>Mensaje respuesta:</b>';
                        htmlAutorizaciones +=   '</td>';
                        htmlAutorizaciones +=   '<td>';
                        htmlAutorizaciones +=       response.jsonPedido.autorizaciones[i].MensajeRespuestaAut;
                        htmlAutorizaciones +=   '</td>';
                        htmlAutorizaciones += '</tr> ';

                        htmlAutorizaciones += '<tr> ';
                        htmlAutorizaciones +=   '<td>';
                        htmlAutorizaciones +=       '<b>Tarjetahabiente:</b>';
                        htmlAutorizaciones +=   '</td>';
                        htmlAutorizaciones +=   '<td>';
                        htmlAutorizaciones +=       response.jsonPedido.autorizaciones[i].TarjetaHabiente;
                        htmlAutorizaciones +=   '</td>';
                        htmlAutorizaciones += '</tr> ';

                        htmlAutorizaciones += '<tr> ';
                        htmlAutorizaciones +=   '<td>';
                        htmlAutorizaciones +=       '<b>Pasarela pago:</b>';
                        htmlAutorizaciones +=   '</td>';
                        htmlAutorizaciones +=   '<td>';
                        htmlAutorizaciones +=       response.jsonPedido.autorizaciones[i].PasarelaPago;
                        htmlAutorizaciones +=   '</td>';
                        htmlAutorizaciones += '</tr> ';

                        htmlAutorizaciones += '<tr> ';
                        htmlAutorizaciones +=   '<td>';
                        htmlAutorizaciones +=       '<b>Tipo tarjeta:</b>';
                        htmlAutorizaciones +=   '</td>';
                        htmlAutorizaciones +=   '<td>';
                        htmlAutorizaciones +=       response.jsonPedido.autorizaciones[i].TipoTarjeta;
                        htmlAutorizaciones +=   '</td>';
                        htmlAutorizaciones += '</tr> ';

                        htmlAutorizaciones += '<tr> ';
                        htmlAutorizaciones +=   '<td>';
                        htmlAutorizaciones +=       '<b>Fecha - Hora:</b>';
                        htmlAutorizaciones +=   '</td>';
                        htmlAutorizaciones +=   '<td>';
                        htmlAutorizaciones +=       response.jsonPedido.autorizaciones[i].FechaTransaccion + ' '+ response.jsonPedido.autorizaciones[i].HoraTransaccion;
                        htmlAutorizaciones +=   '</td>';
                        htmlAutorizaciones += '</tr> ';


                        $('#tbl_detalle_autorizaciones_pago').append(htmlAutorizaciones);
                        htmlAutorizaciones = '';
                    }
                }else{

                    htmlAutorizaciones += '<tr> ';
                    htmlAutorizaciones += '<td style="background-color: #f2dede;">';
                    htmlAutorizaciones += '<b>NO EXISTEN REGISTROS</b>';
                    htmlAutorizaciones += '</td>';
                    htmlAutorizaciones += '</tr>';
                    $('#tbl_detalle_autorizaciones_pago').append(htmlAutorizaciones);
                    htmlAutorizaciones = '';
                }
            }else{
                htmlAutorizaciones += '<tr> ';
                htmlAutorizaciones += '<td style="background-color: #f2dede;">';
                htmlAutorizaciones += '<b>NO EXISTEN REGISTROS</b>';
                htmlAutorizaciones += '</td>';
                htmlAutorizaciones += '</tr>';
                $('#tbl_detalle_autorizaciones_pago').append(htmlAutorizaciones);
                htmlAutorizaciones = '';
            }


            //Datos de Factura
            let fechaFacturaConFormato = '';
            if(response.facturaFechaInsercion && response.facturaFechaInsercion.date){
                fechaFacturaConFormato =  response.facturaFechaInsercion.date.substring(0, 15);
            }

            $("#detalle_factura_id").text(response.facturaId);
            $("#detalle_factura_fecha").text(fechaFacturaConFormato);
            $("#detalle_factura_valor_total").text(response.facturaTotal);
            $("#detalle_factura_valor_neto").text(response.facturaSubtotal);
            $("#detalle_factura_valor_iva").text(response.facturaIva);
            $("#detalle_factura_valor_descuento").text(response.facturaDescuento);
            

            if(response.facturaId && response.facturaId != ''){
                cargaDetalleFactura(response.facturaId);
            }

            //Datos encabezado panel Factura
            $("#detalle_encabezado_factura_id").text(response.facturaId);
            $("#detalle_encabezado_hora_pickup").text(response.jsonPedido.fecha_hora_pickup);

            //Datos encabezado panel Orden Pedido
            $("#detalle_encabezado_orden_pedido_id").text(response.ordenPedidoId);
            

            //Carga de Informacion de Productos Pedido
            cargaInformacionProductosPedido(id, response.jsonPedido.detalles);


        },
        error: function (e1, e2, e3) {
            console.log(e1);
            console.log(e2);
            console.log(e3);
        }
    });

}

var cargaInformacionProductosPedido = function(id, detalles){

    send = {};
    send.metodo = 'informacionProductosPedido';
    send.kioskoCabeceraId = id;
    api.data = send;
    $.ajax({
        ...api,
        success: function (response) {

            var htmlDetalleProductos    = '';


            //Poblar Tabla impresion Factura
            $("#tbl_detalle_pedido_producto").empty();
 

            htmlDetalleProductos +=  '<thead>';
            htmlDetalleProductos +=     '<tr>';
            htmlDetalleProductos +=         '<th>PLU</th>'
            htmlDetalleProductos +=         '<th>MODIF</th>'
            htmlDetalleProductos +=         '<th>NOMBRE</th>'
            htmlDetalleProductos +=         '<th>CANTIDAD</th>'
            htmlDetalleProductos +=         '<th>COMENTARIO</th>'
            htmlDetalleProductos +=     '</tr>';
            htmlDetalleProductos +=  '</thead>';
            $('#tbl_detalle_pedido_producto').append(htmlDetalleProductos);
            htmlDetalleProductos = '';
            
            if(detalles && detalles.length > 0){

                for (var i = 0; i < detalles.length; i++) {
                    htmlDetalleProductos += '<tr> ';
                    htmlDetalleProductos +=     '<td> ';
                    htmlDetalleProductos +=         detalles[i].plu_id;
                    htmlDetalleProductos +=     '</td> ';
                    htmlDetalleProductos +=     '<td> ';
                    htmlDetalleProductos +=     '</td> ';
                    htmlDetalleProductos +=     '<td> ';
                    htmlDetalleProductos +=         response.find(x => x.producto_id == detalles[i].plu_id).producto_nombre;
                    htmlDetalleProductos +=     '</td> ';
                    htmlDetalleProductos +=     '<td> ';
                    htmlDetalleProductos +=         detalles[i].dop_cantidad;
                    htmlDetalleProductos +=     '</td> ';
                    htmlDetalleProductos +=     '<td> ';
                    htmlDetalleProductos +=         detalles[i].comentario;
                    htmlDetalleProductos +=     '</td> ';
                    htmlDetalleProductos += '</tr> ';
                    $('#tbl_detalle_pedido_producto').append(htmlDetalleProductos);
                    htmlDetalleProductos = '';   
                    
                    if(detalles[i].modificadores && detalles[i].modificadores.length > 0){
                        for (var j = 0; j < detalles[i].modificadores.length; j++) {
                            htmlDetalleProductos += '<tr> ';
                            htmlDetalleProductos +=     '<td> ';
                            htmlDetalleProductos +=     '</td> ';
                            htmlDetalleProductos +=     '<td> ';
                            htmlDetalleProductos +=         detalles[i].modificadores[j].plu_id;
                            htmlDetalleProductos +=     '</td> ';
                            htmlDetalleProductos +=     '<td> ';
                            htmlDetalleProductos +=         response.find(x => x.producto_id == detalles[i].modificadores[j].plu_id).producto_nombre;  ;
                            htmlDetalleProductos +=     '</td> ';
                            htmlDetalleProductos +=     '<td> ';
                            htmlDetalleProductos +=         detalles[i].modificadores[j].dop_cantidad;
                            htmlDetalleProductos +=     '</td> ';
                            htmlDetalleProductos +=     '<td> ';
                            htmlDetalleProductos +=     '</td> ';
                            htmlDetalleProductos += '</tr> ';
                            $('#tbl_detalle_pedido_producto').append(htmlDetalleProductos);
                            htmlDetalleProductos = '';   
        
                        }
                    }

                }

            }else{
                htmlDetalleProductos += '<tr> ';
                htmlDetalleProductos += '<td colspan="5" style="background-color: #f2dede;">';
                htmlDetalleProductos += '<b>NO EXISTEN REGISTROS</b>';
                htmlDetalleProductos += '</td>';
                htmlDetalleProductos += '</tr>';
                $('#tbl_detalle_pedido_producto').append(htmlDetalleProductos);
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

var cargaDetalleImpresion = function(id){
    send = {};
    send.metodo = 'detalleImpresiones';
    send.kioskoCabeceraId = id;
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
                $("#tbl_detalle_impresion_factura").empty();
 

                htmlImpresionFactura +=  '<thead>';
                htmlImpresionFactura +=     '<tr>';
                htmlImpresionFactura +=         '<td><b>Fecha</b></td>';
                htmlImpresionFactura +=         '<td><b>Estación</b></td>'
                htmlImpresionFactura +=         '<td><b>Estado</b></td>';
                htmlImpresionFactura +=         '<td><b>Impresora</b></td>';
                htmlImpresionFactura +=         '<td class="text-center"><b>Acciones</b></td>'
                htmlImpresionFactura +=     '</tr>';
                htmlImpresionFactura +=  '</thead>';
                $('#tbl_detalle_impresion_factura').append(htmlImpresionFactura);
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

                        $('#tbl_detalle_impresion_factura').append(htmlImpresionFactura);
                        htmlImpresionFactura = '';
                    }
    
                }else{
                    htmlImpresionFactura += '<tr> ';
                    htmlImpresionFactura += '<td colspan="5" style="background-color: #f2dede;">';
                    htmlImpresionFactura += '<b>NO EXISTEN REGISTROS</b>';
                    htmlImpresionFactura += '</td>';
                    htmlImpresionFactura += '</tr>';
                    $('#tbl_detalle_impresion_factura').append(htmlImpresionFactura);
                    htmlImpresionFactura = '';
                }

        
    
                //Poblar Tabla impresion Orden Pedido
                $("#tbl_detalle_impresion_orden_pedido").empty();

                htmlImpresionPedido +=  '<thead>';
                htmlImpresionPedido +=     '<tr>';
                htmlImpresionPedido +=         '<td><b>Fecha</b></td>';
                htmlImpresionPedido +=         '<td><b>Estación</b></td>'
                htmlImpresionPedido +=         '<td><b>Estado</b></td>';
                htmlImpresionPedido +=         '<td><b>Impresora</b></td>';
                htmlImpresionPedido +=         '<td class="text-center"><b>Acciones</b></td>'
                htmlImpresionPedido +=     '</tr>';
                htmlImpresionPedido +=  '</thead>';
                $('#tbl_detalle_impresion_orden_pedido').append(htmlImpresionPedido);
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
                        $('#tbl_detalle_impresion_orden_pedido').append(htmlImpresionPedido);
                        htmlImpresionPedido = '';  
                    }    
                }else{
                    htmlImpresionPedido += '<tr> ';
                    htmlImpresionPedido += '<td colspan="5" style="background-color: #f2dede;">';
                    htmlImpresionPedido += '<b>NO EXISTEN REGISTROS</b>';
                    htmlImpresionPedido += '</td>';
                    htmlImpresionPedido += '</tr>';
                    $('#tbl_detalle_impresion_orden_pedido').append(htmlImpresionPedido);
                    htmlImpresionPedido = '';
                }



            }

        },
        error: function (e1, e2, e3) {
            console.log(e1);
            console.log(e2);
            console.log(e3);
        }
    });

}

var verDocumento = function(url){
    var res = url.replace("172.17.0.70:880/pos", "localhost:880/pos");
    $('#dlg_impresion_previa').load("http://"+res);
    $( "#dlg_impresion_previa" ).dialog( "open" );
    $( "#dlg_impresion_previa" ).dialog( "moveToTop" );
}

var regresarAPanelPrincipal = function () {
    $('#pnl_detalle').hide();
    $('#pnl_principal').show();
}

var cantidadPorEstado = function(){
    send = {};
    send.metodo = 'cantidadPorEstado';
    api.data = send;

    $.ajax({
        ...api,
        success: function (response) {
            if(response){
                $('#lbl_cabecera_nuevos_pedidos').text(response.ingresados);
                $('#lbl_pedidos_ingresados').text(response.ingresados);
                $('#lbl_pedidos_preparando').text(response.preparando);
                $('#lbl_pedidos_listo').text(response.listos);
                $('#lbl_pedidos_entregado').text(response.entregados);
                $('#lbl_pedidos_transferido').text(response.transferidos);
            }else{
                $('#lbl_cabecera_nuevos_pedidos').text(0);
                $('#lbl_pedidos_ingresados').text(0);
                $('#lbl_pedidos_preparando').text(0);
                $('#lbl_pedidos_listo').text(0);
                $('#lbl_pedidos_entregado').text(0);
                $('#lbl_pedidos_transferido').text(0);
            }
        },
        error: function (e1, e2, e3) {

            $('#lbl_cabecera_nuevos_pedidos').text(0);
            $('#lbl_pedidos_ingresados').text(0);
            $('#lbl_pedidos_preparando').text(0);
            $('#lbl_pedidos_listo').text(0);
            $('#lbl_pedidos_entregado').text(0);
            $('#lbl_pedidos_transferido').text(0);


            console.log(e1);
            console.log(e2);
            console.log(e3);
        }
    });
}

var cargaDetalleFactura = function(idFactura){
    $("#tbl_detalle_factura_pickup").empty();
    var htmlDetalleFacturaPickup    = '';


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
    $('#tbl_detalle_factura_pickup').append(htmlDetalleFacturaPickup);
    htmlDetalleFacturaPickup = '';

    send = {};
    send.metodo     = 'detalleFacturaPickup';
    send.idFactura  = idFactura;
    api.data = send;

    $.ajax({
        ...api,
        success: function (response) {
            if(response){

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
                            htmlDetalleFacturaPickup +=         response[i].dop_precio;
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
                            htmlDetalleFacturaPickup +=         response[i].dop_precio;
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +=     '<td> ';
                            htmlDetalleFacturaPickup +=         response[i].dop_cantidad;
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +=     '<td> ';
                            htmlDetalleFacturaPickup +=         response[i].dop_total;
                            htmlDetalleFacturaPickup +=     '</td> ';
                            htmlDetalleFacturaPickup +='</tr> ';
                        }
        
                        $('#tbl_detalle_factura_pickup').append(htmlDetalleFacturaPickup);
                        htmlDetalleFacturaPickup = '';
                    }
    
                }else{
                    htmlDetalleFacturaPickup += '<tr> ';
                    htmlDetalleFacturaPickup += '<td colspan="6" style="background-color: #f2dede;">';
                    htmlDetalleFacturaPickup += '<b>NO EXISTEN REGISTROS</b>';
                    htmlDetalleFacturaPickup += '</td>';
                    htmlDetalleFacturaPickup += '</tr>';
                    $('#tbl_detalle_factura_pickup').append(htmlDetalleFacturaPickup);
                    htmlDetalleFacturaPickup = '';
                }


            }else{
                htmlDetalleFacturaPickup += '<tr> ';
                htmlDetalleFacturaPickup += '<td colspan="6" style="background-color: #f2dede;">';
                htmlDetalleFacturaPickup += '<b>NO EXISTEN REGISTROS</b>';
                htmlDetalleFacturaPickup += '</td>';
                htmlDetalleFacturaPickup += '</tr>';
                $('#tbl_detalle_factura_pickup').append(htmlDetalleFacturaPickup);
                htmlDetalleFacturaPickup = '';
            }
        },
        error: function (e1, e2, e3) {
            htmlDetalleFacturaPickup += '<tr> ';
            htmlDetalleFacturaPickup += '<td colspan="6" style="background-color: #f2dede;">';
            htmlDetalleFacturaPickup += '<b>NO EXISTEN REGISTROS</b>';
            htmlDetalleFacturaPickup += '</td>';
            htmlDetalleFacturaPickup += '</tr>';
            $('#tbl_detalle_factura_pickup').append(htmlDetalleFacturaPickup);
            htmlDetalleFacturaPickup = '';

            console.log(e1);
            console.log(e2);
            console.log(e3);
        }
    });


}

var siguiente = function(){
    this.pagina++;

    if(this.pagina > 1){
        $('#btn_anterior').show();
    }

    buscar();
}

var anterior = function(){
    
    if(this.pagina > 1){
        this.pagina--;
        buscar();
    }
    
    if(this.pagina <= 1){
        $('#btn_anterior').hide();
    }
}

var reimprimir = function(idCanalMovimiento){
    send = {};
    send.metodo = 'reimprimir';
    send.idCanalMovimiento = idCanalMovimiento;
    api.data = send;

    $.ajax({
        ...api,
        success: function (response) {
            if(response){
                if(response == 1){
                    alertify.success("Reimpresión realizada correctamente");
                }
            }
        },
        error: function (e1, e2, e3) {

            console.log(e1);
            console.log(e2);
            console.log(e3);
        }
    });
}
