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

    // Crear mapa de dop_id -> índice en itemsArray para búsqueda rápida
    var dopIdToIndex = {};
    itemsArray.forEach(function(item, idx) {
        dopIdToIndex[item.dop_id] = idx;
    });

    // Array de productos finales y mapa de dop_id -> índice en products
    var products = [];
    var dopIdToProductIndex = {};
    var lastMainProductDopId = null;

    // Procesar items secuencialmente
    itemsArray.forEach(function(item) {
        var isSelfAncestor = item.ancestro === item.dop_id;
        var hasPrice = parseFloat(item.dop_total) > 0;
        var isComment = item.tipo === 0;

        if (isSelfAncestor && hasPrice && !isComment) {
            // PRODUCTO PRINCIPAL: ancestro === dop_id Y tiene precio Y no es comentario
            var newProduct = {
                productId: String(item.plu_id),
                name: item.magp_desc_impresion,
                amount: item.dop_cantidad || 1,
                content: [],
                modifier: null,
                comments: null,
                _dop_id: item.dop_id,
                _subitems: [],
                _comments: []
            };
            dopIdToProductIndex[item.dop_id] = products.length;
            products.push(newProduct);
            lastMainProductDopId = item.dop_id;

        } else if (!isSelfAncestor) {
            // SUBITEM/COMENTARIO CON ANCESTRO EXPLÍCITO
            var parentDopId = item.ancestro;
            var parentProductIndex = dopIdToProductIndex[parentDopId];

            if (parentProductIndex !== undefined) {
                if (isComment) {
                    products[parentProductIndex]._comments.push(item.magp_desc_impresion);
                } else {
                    products[parentProductIndex]._subitems.push({
                        name: item.magp_desc_impresion,
                        quantity: item.dop_cantidad || 1
                    });
                }
            }

        } else if (isSelfAncestor && !hasPrice) {
            // SUBITEM HUÉRFANO: ancestro === dop_id pero sin precio
            // Asociar al último producto principal
            if (lastMainProductDopId !== null) {
                var lastProductIndex = dopIdToProductIndex[lastMainProductDopId];
                if (lastProductIndex !== undefined) {
                    if (isComment) {
                        products[lastProductIndex]._comments.push(item.magp_desc_impresion);
                    } else {
                        products[lastProductIndex]._subitems.push({
                            name: item.magp_desc_impresion,
                            quantity: item.dop_cantidad || 1
                        });
                    }
                }
            }
        }
    });

    // Convertir _subitems y _comments a formato final
    products.forEach(function(product) {
        // Procesar comentarios
        if (product._comments && product._comments.length > 0) {
            product.comments = product._comments.join(", ");
        }

        // Procesar subitems como modifier
        if (product._subitems && product._subitems.length > 0) {
            var modifierItems = [];
            product._subitems.forEach(function(sub) {
                if (sub.quantity > 1) {
                    modifierItems.push(sub.quantity + " " + sub.name);
                } else {
                    modifierItems.push(sub.name);
                }
            });
            product.modifier = modifierItems.join(", ");
        }

        // Limpiar propiedades temporales
        delete product._dop_id;
        delete product._subitems;
        delete product._comments;
    });

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

// ============================================
// COMUNICACIÓN CON EL BACKEND KDS
// ============================================

// Variables globales para almacenar el token y credenciales
var kds_auth_token = null;
var kds_token_timestamp = null;
var kds_url = null;
var kds_email = null;
var kds_password = null;
var KDS_TOKEN_EXPIRY_MS = 14 * 60 * 1000; // 14 minutos (el token expira en 15)

/**
 * Función principal que envía datos al KDS
 * Obtiene políticas UNA SOLA VEZ y las almacena en variables globales
 */
function fn_send_to_kds(tomando_pedido, pedido_finalizado) {
    // Obtener políticas UNA SOLA VEZ
    var politicas = get_politicas_kds();
    if (!politicas) return;

    // Almacenar en variables globales para uso posterior
    kds_url = politicas.url;
    kds_email = politicas.email;
    kds_password = politicas.password;

    var activo = politicas.activo;
    var impresion_a_tiempo_real = politicas.impresion_a_tiempo_real;

    if (activo != 1) return;
    if (!kds_url) {
        console.error("[KDS] URL no configurada en políticas");
        return;
    }

    var toSendKDS = null;

    if (tomando_pedido == true && impresion_a_tiempo_real == true) {
        toSendKDS = fn_get_orden_pedido("TOMANDO PEDIDO");
        if (toSendKDS) {
            fn_communication_with_kds(toSendKDS);
        }
    }
    if (pedido_finalizado) {
        toSendKDS = fn_get_orden_pedido("PEDIDO TOMADO");
        if (toSendKDS) {
            fn_communication_with_kds(toSendKDS);
        }
    }
}

/**
 * Función principal de comunicación con el backend KDS
 * Usa las variables globales kds_url, kds_email, kds_password
 * @param {Object} orderData - Datos de la orden en formato ApiComanda
 */
function fn_communication_with_kds(orderData) {
    // Verificar si tenemos un token válido
    if (!fn_is_token_valid()) {
        // Necesitamos autenticarnos primero
        fn_authenticate_kds(function(success) {
            if (success) {
                fn_send_order_to_kds(orderData);
            } else {
                console.error("[KDS] Error de autenticación, no se pudo enviar la orden");
            }
        });
    } else {
        // Token válido, enviar directamente
        fn_send_order_to_kds(orderData);
    }
}

/**
 * Verifica si el token actual es válido
 * @returns {boolean}
 */
function fn_is_token_valid() {
    if (!kds_auth_token || !kds_token_timestamp) {
        return false;
    }
    var elapsed = Date.now() - kds_token_timestamp;
    return elapsed < KDS_TOKEN_EXPIRY_MS;
}

/**
 * Autentica con el backend KDS y obtiene el token
 * Usa las variables globales kds_url, kds_email, kds_password
 * @param {Function} callback - Función a llamar con el resultado (true/false)
 */
function fn_authenticate_kds(callback) {
    var loginUrl = kds_url + "/auth/login";

    console.log("[KDS] Autenticando con el backend...");

    $.ajax({
        url: loginUrl,
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
            email: kds_email,
            password: kds_password
        }),
        success: function(response) {
            if (response && response.accessToken) {
                kds_auth_token = response.accessToken;
                kds_token_timestamp = Date.now();
                console.log("[KDS] Autenticación exitosa");
                if (callback) callback(true);
            } else {
                console.error("[KDS] Respuesta de login sin token", response);
                if (callback) callback(false);
            }
        },
        error: function(xhr, status, error) {
            console.error("[KDS] Error en autenticación:", error);
            console.error("[KDS] Status:", status);
            console.error("[KDS] Response:", xhr.responseText);

            // Limpiar token en caso de error
            kds_auth_token = null;
            kds_token_timestamp = null;

            if (callback) callback(false);
        }
    });
}

/**
 * Envía la orden al endpoint de tickets del KDS
 * Usa la variable global kds_url
 * @param {Object} orderData - Datos de la orden en formato ApiComanda
 */
function fn_send_order_to_kds(orderData) {
    var ticketUrl = kds_url + "/tickets/receive";

    console.log("[KDS] Enviando orden al backend...", orderData);

    $.ajax({
        url: ticketUrl,
        type: "POST",
        contentType: "application/json",
        headers: {
            "Authorization": "Bearer " + kds_auth_token
        },
        data: JSON.stringify(orderData),
        success: function(response) {
            if (response && response.success) {
                console.log("[KDS] Orden enviada exitosamente. ID:", response.orderId);
            } else {
                console.warn("[KDS] Respuesta inesperada:", response);
            }
        },
        error: function(xhr, status, error) {
            console.error("[KDS] Error enviando orden:", error);

            // Si el error es 401 (no autorizado), intentar re-autenticar
            if (xhr.status === 401) {
                console.log("[KDS] Token expirado, re-autenticando...");
                kds_auth_token = null;
                kds_token_timestamp = null;

                // Re-autenticar usando variables globales (ya almacenadas)
                fn_authenticate_kds(function(success) {
                    if (success) {
                        // Re-intentar envío
                        fn_send_order_to_kds(orderData);
                    }
                });
            } else {
                console.error("[KDS] Status:", xhr.status);
                console.error("[KDS] Response:", xhr.responseText);
            }
        }
    });
}

/**
 * Fuerza la renovación del token
 * Útil para llamar antes de operaciones críticas
 * Requiere que fn_send_to_kds haya sido llamado antes para tener las credenciales
 * @param {Function} callback - Función a llamar cuando termine
 */
function fn_refresh_kds_token(callback) {
    if (!kds_url || !kds_email || !kds_password) {
        console.error("[KDS] No hay credenciales almacenadas para refresh");
        if (callback) callback(false);
        return;
    }

    // Invalidar token actual
    kds_auth_token = null;
    kds_token_timestamp = null;

    // Re-autenticar usando variables globales
    fn_authenticate_kds(callback);
}