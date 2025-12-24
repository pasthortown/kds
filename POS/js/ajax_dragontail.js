async function createDragonTailOrder(codigo, medio){
    fn_cargando(1);
    let session = JSON.parse(await getSessionConfig());
    send = {
        codApp: codigo,
        restauranId:session['idRestaurante'],
        medio: medio,
        accion:0
    }

    $.ajax({
        type: "POST",
        url: "../resources/module/domicilio/dragon-tail/orders.php",
        data: send,
        dataType: "json",
        async: false,
        success: function(data){
            if(data != ''){
                alertify.success("order creada en dragontail con el order ID: " + data.orders[0]['orderId']);
            }
            $("#btn_reenviar_bringg").hide();
            $("#btn_asignar").hide();
            changeStatusAgregador(codigo, medio);
            cargarPedidosRecibidos();
            fn_cargando();
        },
        error: function(jqXHR, exception) {
            alertify.error(jqXHR.responseJSON.message);
            $("#btn_reenviar_bringg").show();
            changeStatus(codigo, medio, 'error');
            cargarPedidosRecibidos();
            fn_cargando();
        }
    });
}

async function hideButtonForDragonTail(codigo_externo, medio){
    let isValidMedio = await getRiderAgregador(medio); // Valida si es un agregador existente
    if (codigo_externo != "1") {
        $("#btn_reenviar_bringg").show();
    }
    $("#btn_desasignar").hide();
    $("#btn_en_camino").hide();
    $("#btn_asignar").hide();
    if (isValidMedio['riderId'] && (codigo_externo != "null")) {
        $("#btn_entregado_agregador").show();
    } else {
        $("#btn_entregado_agregador").hide();
    }
}

function getSessionConfig() { 
    return $.ajax({
        url: "../resources/module/domicilio/dragon-tail/sessionControler.php",
        type: 'GET',
    });
}

function changeStatus(codApp,medio,estado){
    send = { codApp: codApp, medio: medio, estado: estado}
    $.ajax({
        type: "POST",
        url: "../resources/module/domicilio/dragon-tail/orderStatus.php",
        data: send,
        dataType: "json",
        success: function(data){
            alertify.success(data);
        }
    });
}

function checkDragontailNewFlow() {
    send = {
        metodo: "getConfigCadena",
        accdescription: "DRAGONTAIL CONFIGS",
        cdcdescription: "FLUJO AUTOMATICO",
        data: "variableB"
    };

    return $.ajax({
        type: "POST",
        url: "../ordenpedido/config_app.php",
        data: send,
        dataType: "json",
        success: function(data) {}
    });
}

async function getDragonTailStatus() {
    let parametros = { metodo: "getDragonTailStatus" };

    return await ajaxRequest('../ordenpedido/config_app.php', parametros, 'POST');
}
