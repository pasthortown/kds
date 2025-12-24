function get_politicas_kds() {
    let toReturn = null;

    $.ajax({
        async: false,
        type: "POST",
        url: "./../kds/config_kds.php",
        data: { get_politicas_kds: 1 },
        dataType: "json",
        success: function (datos) {
            toReturn = datos;
        }
    });

    return toReturn;
}

function fn_get_orden_pedido() {
    var send;
    if ($("#hide_numSplit").val() === "undefined") {
        send = {
            cargar_ordenPedidoPendiente: 1,
            numSplit: 0
        };
    } else {
        send = {
            cargar_ordenPedidoPendiente: 1,
            numSplit: $("#hide_numSplit").val()
        };
    }
    send.odp_id = document.getElementById("hide_odp_id").value;
    send.cat_id = rst_categoria;
    data_to_kds = null;
    $.ajax({
        async: false,
        url: "config_ordenPedido.php",
        data: send,
        dataType: "json",
        success: function (datos) {
            if (datos.str > 0) {
                data_to_kds = fn_prepare_data_to_kds_from_order(datos);
            }
        }
    });
    return data_to_kds;
}

function fn_prepare_data_to_kds_from_order(datos) {
    return datos;
}

function fn_send_to_kds(tomando_pedido, pedido_finalizado) {
    let politicas = get_politicas_kds();
    if (!politicas) return;
    let url_api_kds = politicas.url;
    let activo = politicas.activo;
    let impresion_a_tiempo_real = politicas.impresion_a_tiempo_real;
    let toSendKDS = null;
    if (activo != 1) return;
    if (tomando_pedido == true && impresion_a_tiempo_real == true) {
        toSendKDS = fn_get_orden_pedido();
    }
    if (pedido_finalizado) {
        toSendKDS = fn_get_orden_pedido();
    }
    console.log(toSendKDS);
}