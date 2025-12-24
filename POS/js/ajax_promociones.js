/* global alertify */

var $botonAplicarPromocion = $("#btnagregarPromocion");
var $modalPromociones = $("#modalQRPromociones");
var BUFFER = "";
$(document).ready(function () {
    fn_modalPromocionesQR();
});

$botonAplicarPromocion.click(function () {
    fn_modalPromocionesQR();
    $modalPromociones.dialog("open");
});

function fn_modalPromocionesQR() {
    $modalPromociones.dialog({
        modal: true,
        autoOpen: false,
        position: {
            my: "top",
            at: "top+200"
        },
        width: 400,
        heigth: 400,
        resize: false,
        opacity: 0,
        open: function () {
            eventoCerrarModalQR(true);
            //window.addEventListener('keydown', onQRReadHandler, false);
            $(this).on('keydown', 'input', bufferLecturaQR);
            darFocoFijo($("#valorQRPromociones"), true);
        },
        close: function () {
            cerrarModalQR();
        }
    });
}

// Evento para permitir cerrar la modal de código QR al presionar afuera de la modal
// Esto debido a que en algunas tiendas no se estaba cargando la barra superior donde está el botón (X)
function eventoCerrarModalQR(activar) {
    if (activar) {
        $('.ui-widget-overlay').click(function () {
            cerrarModalQR();
        });
    } else {
        $('.ui-widget-overlay').off("click");
    }
}

// Evento para fijar el foco en el input, de manera que no pueda perderlo
function darFocoFijo($input, activar) {
    if (activar) {
        $input.focus();
        $input.blur(function () {
            setTimeout(function () {
                $input.focus();
            }, 20);
        });
    } else {
        $input.off("blur");
    }
}

function cerrarModalQR() {
    $modalPromociones.dialog('destroy').remove();
    $modalPromociones.off('keydown', 'input', bufferLecturaQR);
    $modalPromociones.find('input').val('');
    eventoCerrarModalQR(false);
    darFocoFijo($("#valorQRPromociones"), false);
}

// Falta aplicar función para validar
function validarJWT(codigo) {
    var valido = false;
    if (codigo !== "" && formatoJWT(codigo)) {
        valido = true;
    } else {
        fn_cargando(0);
        alertify.error("Error en la configuración del lector. Por favor comunicarse con la mesa de servicio para validar el idioma de la caja.");
    }
    return valido;
}

// Tres partes codificadas en Base64URL (caracteres a-z, A-Z, 0-9, _ y -) concatenadas con puntos
function formatoJWT(codigo) {
    var regex = /^[\w\-]+\.[\w\-]+\.[\w\-]+$/;
    return regex.test(codigo);
}

// Evento para permitir cerrar la modal de código QR al presionar afuera de la modal
// Esto debido a que en algunas tiendas no se estaba cargando la barra superior donde está el botón (X)
function eventoCerrarModalQR(activar) {
    if (activar) {
        $(".ui-widget-overlay").click(function () {
            cerrarModalQR();
        });
    } else {
        $(".ui-widget-overlay").off("click");
    }
}

// Evento para fijar el foco en el input, de manera que no pueda perderlo
function darFocoFijo($input, activar) {
    if (activar) {
        $input.focus();
        $input.blur(function () {
            setTimeout(function () {
                $input.focus();
            }, 20);
        });
    } else {
        $input.off("blur");
    }
}

function cerrarModalQR() {
    $modalPromociones.dialog("destroy").remove();
    $modalPromociones.off("keydown", "input", bufferLecturaQR);
    $modalPromociones.find("input").val("");
    eventoCerrarModalQR(false);
    darFocoFijo($("#valorQRPromociones"), false);
}

// Falta aplicar función para validar
function validarJWT(codigo) {
    var valido = false;
    if (codigo !== "" && formatoJWT(codigo)) {
        valido = true;
    } else {
        fn_cargando(0);
        alertify.error("Error en la configuración del lector. Por favor comunicarse con la mesa de servicio para validar el idioma de la caja.");
    }
    return valido;
}

// Tres partes codificadas en Base64URL (caracteres a-z, A-Z, 0-9, _ y -) concatenadas con puntos
function formatoJWT(codigo) {
    var regex = /^[\w\-]+\.[\w\-]+\.[\w\-]+$/;
    return regex.test(codigo);
}

function bufferLecturaQR(e) {
    var esCaracterEspecial = /^(13|37|38|39|40|16)$/.test("" + e.keyCode);
    if (!esCaracterEspecial) {
        //if(e.keyCode!==16) BUFFER=BUFFER+e.key;
        BUFFER = BUFFER + e.key;
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
    if (13 == e.keyCode) {
        $("#valorQRPromociones").val(BUFFER);
        var valorInput = BUFFER;
        if ("http" == valorInput.substring(0, 4)) {
            valorInput = valorInput.substr(7);
        }
        var cfac_id = $("#txtNumFactura").val();
        var odp_id = $("#hide_odp_id").val();
        var dop_cuenta = $("#hide_numeroCuenta").val();
        $modalPromociones.dialog("close");
        fn_cargando(1);
        $.ajax({
            url: "../promociones/config_promociones.php",
            type: "POST",
            data: {
                canjearPromocionTrade: 1,
                cfac_id: cfac_id,
                odp_id: odp_id,
                cod_promo: valorInput,
                cantidadCanjes: 1,
                dop_cuenta: dop_cuenta
            },
            dataType: "json",
            success: function (datos) {
                console.log("datos canje", datos);
                if (datos.estadoPromocion == "OK") {
                    insertarBeneficiosOrdenPedido(odp_id, datos.Id_Promociones, datos.mensajePromocion, datos.beneficioPromocion);
                    return true;
                }

                if (datos.mensajePromocion == null) {
                    alertify.alert("Error al realizar el canje.");
                    return true;
                }

                alertify.alert(datos.mensajePromocion);
                return true;
            },
            complete: function () {
                fn_cargando(0);
                BUFFER = "";
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log("error promocion on qr buffer")
                console.log(xhr.responseText);
                fn_cargando(0);
                alertify.alert("No se pudo canjear la promoción");
            }
        });
    }
}

function onQRReadHandler(event) {
    event.stopPropagation();
    var codigoTecla = event.keyCode;
    if (13 == codigoTecla) {
        var valorInput = $(this).val();
        if ('http' == valorInput.substring(0, 4)) {
            valorInput = valorInput.substr(7);
        }
        var cfac_id = $("#txtNumFactura").val();
        var odp_id = $("#hide_odp_id").val();
        var dop_cuenta = $("#hide_numeroCuenta").val();
        $modalPromociones.dialog("close");
        fn_cargando(1);
        $.ajax({
            url: "../promociones/config_promociones.php",
            type: "POST",
            data: {
                canjearPromocionTrade: 1,
                cfac_id: cfac_id,
                odp_id: odp_id,
                cod_promo: valorInput,
                cantidadCanjes: 1,
                dop_cuenta: dop_cuenta
            },
            dataType: "json",
            success: function (datos) {
                console.log("datos canje", datos);
                if (datos.estadoPromocion == "OK") {
                    insertarBeneficiosOrdenPedido(odp_id, datos.Id_Promociones, datos.mensajePromocion, datos.beneficioPromocion);
                    return true;
                }

                if (datos.mensajePromocion == null) {
                    alertify.alert("Error al realizar el canje.");
                    return true;
                }

                alertify.alert(datos.mensajePromocion);
                return true;
            },
            complete: function () {
                fn_cargando(0);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log("error promocion handler")
                console.log(xhr.responseText);
                fn_cargando(0);
                alertify.alert("No se pudo canjear la promoción");
            }
        });
    }
    return true;
}

function insertarBeneficiosOrdenPedido(idCabeceraOrdenPedido, Id_Promociones, mensaje, beneficioPromocion) {
    var send;
    var idUsuario = $("#hide_usr_id").val();
    var dop_cuenta = $("#hide_numeroCuenta").val() ? $("#hide_numeroCuenta").val() : 1;
    send = {"insertarBeneficiosOrdenPedido": 1};

    send.idCabeceraOrdenPedido = idCabeceraOrdenPedido;
    send.Id_Promociones = Id_Promociones;
    send.idUsuario = idUsuario;
    send.dop_cuenta = dop_cuenta;
    send.beneficioPromocion = beneficioPromocion;

    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_ordenPedido.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                alertify.success(mensaje);
                fn_listaPendiente();
            } else {
                alertify.alert("No se pudo canjear la promoción.");
                fn_listaPendiente();
            }
        }
    });
}
