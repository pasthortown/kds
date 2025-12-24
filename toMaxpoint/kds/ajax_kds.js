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

function fn_get_orden_pedido(statusPos) {
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
                data_to_kds = fn_prepare_data_to_kds_from_order(datos, statusPos);
            }
        }
    });
    return data_to_kds;
}

/**
 * Prepara los datos de la orden del POS para enviar al backend KDS
 * Convierte el formato de MaxPoint al formato ApiComanda esperado por el backend
 * @param {Object} datos - Datos crudos de la orden del POS
 * @param {string} statusPos - Estado del pedido: "TOMANDO PEDIDO" o "PEDIDO TOMADO"
 * @returns {Object} - Objeto ApiComanda listo para enviar al backend
 */
function fn_prepare_data_to_kds_from_order(datos, statusPos) {
    // Obtener datos del contexto del POS
    var orderId = $("#hide_odp_id").val() || "";
    var customerName = $("#hide_cli_nombres").val() || "";
    var customerDocument = $("#hide_cli_documento").val() || "";
    var customerPhone = $("#hide_cli_telefono").val() || "";
    var customerAddress = $("#hide_cli_direccion").val() || "";
    var tipoServicio = $("#txtTipoServicio").val() || "";
    var mesaId = $("#hide_mesa_id").val() || "";
    var estacionId = $("#hide_est_id").val() || "";
    var numeroCuenta = $("#hide_numeroCuenta").val() || "1";

    // Convertir datos a array para procesar
    var itemsArray = [];
    var itemCount = parseInt(datos.str) || 0;

    for (var i = 0; i < itemCount; i++) {
        if (datos[i]) {
            itemsArray.push(datos[i]);
        }
    }

    // Agrupar productos con sus modificadores/subitems
    var productsMap = {};  // Mapa de productos principales por dop_id
    var modifiersMap = {}; // Mapa de modificadores/subitems agrupados por ancestro

    // Primera pasada: identificar productos principales y agrupar modificadores
    itemsArray.forEach(function(item) {
        var isMainProduct = item.ancestro === item.dop_id;
        var isModifier = item.tipo === 0;

        if (isMainProduct && !isModifier) {
            // Producto principal
            productsMap[item.dop_id] = {
                productId: String(item.plu_id),
                name: item.magp_desc_impresion,
                amount: item.dop_cantidad || 1,
                content: [],
                modifier: null,
                comments: null
            };
            // Inicializar array de modificadores para este producto
            if (!modifiersMap[item.dop_id]) {
                modifiersMap[item.dop_id] = [];
            }
        } else {
            // Es un subitem o modificador - asociar al ancestro
            if (!modifiersMap[item.ancestro]) {
                modifiersMap[item.ancestro] = [];
            }
            modifiersMap[item.ancestro].push({
                name: item.magp_desc_impresion,
                isModifier: isModifier,
                quantity: item.dop_cantidad || 0
            });
        }
    });

    // Segunda pasada: asignar modificadores a sus productos principales
    Object.keys(productsMap).forEach(function(productId) {
        var product = productsMap[productId];
        var mods = modifiersMap[productId] || [];

        var commentItems = [];
        var modifierItems = [];

        mods.forEach(function(mod) {
            if (mod.isModifier) {
                // Es un comentario especial (tipo 0) - va a comments
                commentItems.push(mod.name);
            } else {
                // Es un subitem (tipo 1) - va a modifier
                if (mod.quantity > 1) {
                    modifierItems.push(mod.quantity + " " + mod.name);
                } else {
                    modifierItems.push(mod.name);
                }
            }
        });

        if (commentItems.length > 0) {
            product.comments = commentItems.join(", ");
        }
        if (modifierItems.length > 0) {
            product.modifier = modifierItems.join(", ");
        }
    });

    // Convertir el mapa a array de productos
    var products = Object.values(productsMap);

    // Determinar el canal/tipo de servicio
    var channelName = "POS";
    var channelType = tipoServicio || "MOSTRADOR";

    if (mesaId && mesaId !== "" && mesaId !== "0") {
        channelType = "SALON";
    }

    // Construir el objeto ApiComanda
    var apiComanda = {
        id: orderId,
        orderId: orderId,
        createdAt: new Date().toISOString(),
        channel: {
            id: 1,
            name: channelName,
            type: channelType
        },
        cashRegister: {
            cashier: estacionId,
            name: "Estación " + estacionId
        },
        products: products,
        otrosDatos: {
            turno: -1,
            nroCheque: numeroCuenta,
            llamarPor: customerName,
            Fecha: new Date().toLocaleString(),
            Direccion: customerAddress
        },
        statusPos: statusPos || "TOMANDO PEDIDO"
    };

    // Agregar datos del cliente si existen
    if (customerName) {
        apiComanda.customer = {
            name: customerName
        };
    }

    return apiComanda;
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
        toSendKDS = fn_get_orden_pedido("TOMANDO PEDIDO");
        fn_communication_with_kds(toSendKDS);
    }
    if (pedido_finalizado) {
        toSendKDS = fn_get_orden_pedido("PEDIDO TOMADO");
        fn_communication_with_kds(toSendKDS);
    }
}

function fn_communication_with_kds(toSendKDS) {
    
}