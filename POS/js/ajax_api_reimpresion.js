$(document).ready(function () {

});

function setConfiguracionesApiImpresion(servicioApiImpresion) {
    let jsonServicioApiImpresion = JSON.parse(servicioApiImpresion)

    localStorage.setItem("api_impresion_tienda_aplica", jsonServicioApiImpresion.aplica_tienda);
    localStorage.setItem("api_impresion_estacion_aplica", jsonServicioApiImpresion.aplica_estacion);
    localStorage.setItem("api_impresion_ruta", jsonServicioApiImpresion.ruta);
    localStorage.setItem("api_impresion_url", jsonServicioApiImpresion.url);            
    localStorage.setItem("api_impresion_reintentos", jsonServicioApiImpresion.reintentos);
    localStorage.setItem("api_impresion_timeout", jsonServicioApiImpresion.timeout);  
}

function removeConfiguracionesApiImpresion() {
    localStorage.removeItem("api_impresion_tienda_aplica");
    localStorage.removeItem("api_impresion_estacion_aplica");
    localStorage.removeItem("api_impresion_ruta");
    localStorage.removeItem("api_impresion_url");            
    localStorage.removeItem("api_impresion_reintentos");
    localStorage.removeItem("api_impresion_timeout");
}

function getConfiguracionesApiImpresion() {
    let api_impresion_tienda_aplica = localStorage.getItem('api_impresion_tienda_aplica');
    let api_impresion_estacion_aplica = localStorage.getItem('api_impresion_estacion_aplica');
    let api_impresion_ruta = localStorage.getItem('api_impresion_ruta');
    let api_impresion_url = localStorage.getItem('api_impresion_url');
    let api_impresion_reintentos = localStorage.getItem('api_impresion_reintentos');
    let api_impresion_timeout = localStorage.getItem('api_impresion_timeout');

    return {
        api_impresion_tienda_aplica,
        api_impresion_estacion_aplica,
        api_impresion_ruta,
        api_impresion_url,
        api_impresion_reintentos,
        api_impresion_timeout
    }
}

function apiServicioImpresion(tipo, transaccion = null, estacion = null, idCabeceraOrdenPedido = null, datosAdicionales = null, impresora = null) {
    let result = new Array();
    let send = {};

    send.metodo = "apiServicioImpresion";
    send.tipo = tipo;
    send.transaccion = transaccion;
    send.estacion = estacion;
    send.idCabeceraOrdenPedido = idCabeceraOrdenPedido;
    send.datosAdicionales = datosAdicionales;
    send.impresora = impresora;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../reimpresion/cliente_ws_servicioReimpresion.php",
        data: send,
        success: function (datos) {            
            result["imprime"] = datos.error;
            result["mensaje"] = datos.mensaje;                     
        }
    });

    fn_cargando(0);

    return result;
}