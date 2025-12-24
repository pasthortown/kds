$(document).ready(function () {

});

function setConfiguracionesApiImpresion(servicioApiImpresion) {
    var jsonServicioApiImpresion = JSON.parse(servicioApiImpresion)

    localStorage.setItem("api_impresion_tienda_aplica", jsonServicioApiImpresion.aplica_tienda);
    localStorage.setItem("api_impresion_estacion_aplica", jsonServicioApiImpresion.aplica_estacion);
    localStorage.setItem("api_impresion_ruta", jsonServicioApiImpresion.ruta);
    localStorage.setItem("api_impresion_url", jsonServicioApiImpresion.url);            
    localStorage.setItem("api_impresion_reintentos", jsonServicioApiImpresion.reintentos);
    localStorage.setItem("api_impresion_timeout", jsonServicioApiImpresion.timeout);  
    // Apertura Cajon
    localStorage.setItem("api_impresion_ruta_apertura_cajon", jsonServicioApiImpresion.ruta_servicio_apertura_cajon);
    localStorage.setItem("api_impresion_asignacion_retiro_fondo", jsonServicioApiImpresion.asignacion_retiro_fondo);
    localStorage.setItem("api_impresion_apertura_cajon_caja_chica", jsonServicioApiImpresion.apertura_cajon_caja_chica);

    localStorage.setItem("api_impresion_impresora_apertura_cajon", jsonServicioApiImpresion.impresora_apertura_cajon);


}

function removeConfiguracionesApiImpresion() {
    localStorage.removeItem("api_impresion_tienda_aplica");
    localStorage.removeItem("api_impresion_estacion_aplica");
    localStorage.removeItem("api_impresion_ruta");
    localStorage.removeItem("api_impresion_url");            
    localStorage.removeItem("api_impresion_reintentos");
    localStorage.removeItem("api_impresion_timeout");
    // Apertura cajon
    localStorage.removeItem("api_impresion_ruta_apertura_cajon"); 
    localStorage.removeItem("api_impresion_asignacion_retiro_fondo"); 
    localStorage.removeItem("api_impresion_apertura_cajon_caja_chica");            
    localStorage.removeItem("api_impresion_impresora_apertura_cajon");
}

function getConfiguracionesApiImpresion() {
    var api_impresion_tienda_aplica = localStorage.getItem('api_impresion_tienda_aplica');
    var api_impresion_estacion_aplica = localStorage.getItem('api_impresion_estacion_aplica');
    var api_impresion_ruta = localStorage.getItem('api_impresion_ruta');
    var api_impresion_url = localStorage.getItem('api_impresion_url');
    var api_impresion_reintentos = localStorage.getItem('api_impresion_reintentos');
    var api_impresion_timeout = localStorage.getItem('api_impresion_timeout');
    // Apertura cajon
    var api_impresion_ruta_apertura_cajon = localStorage.getItem('api_impresion_ruta_apertura_cajon');
    var api_impresion_asignacion_retiro_fondo = localStorage.getItem('api_impresion_asignacion_retiro_fondo');
    var api_impresion_apertura_cajon_caja_chica = localStorage.getItem('api_impresion_apertura_cajon_caja_chica');
    var api_impresion_impresora_apertura_cajon = localStorage.getItem('api_impresion_impresora_apertura_cajon');

    return {
        api_impresion_tienda_aplica,
        api_impresion_estacion_aplica,
        api_impresion_ruta,
        api_impresion_url,
        api_impresion_reintentos,
        api_impresion_timeout,
        api_impresion_ruta_apertura_cajon,
        api_impresion_asignacion_retiro_fondo,
        api_impresion_apertura_cajon_caja_chica,
        api_impresion_impresora_apertura_cajon
    }
}

function apiServicioImpresion(tipo, transaccion = null, idCabeceraOrdenPedido = null, datosAdicionales = null) {
    var result = new Array();
    var send = {};

    send.metodo = "apiServicioImpresion";
    send.tipo = tipo;
    send.transaccion = transaccion;
    send.idCabeceraOrdenPedido = idCabeceraOrdenPedido;
    send.datosAdicionales = datosAdicionales;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../impresion/cliente_ws_servicioImpresion.php",
        data: send,
        success: function (datos) {
            
            if (datos === null ) {
                result["imprime"] = 'Error';
                result["mensaje"] = 'No hay conexion con el api de impresion';   
            } else {   
                result["imprime"] = datos.error;
                result["mensaje"] = datos.mensaje;    
            }
                
        }
    });

    fn_cargando(0);

    return result;
}

function apiServicioImpresionMantenimiento(tipo, transaccion = null, idCabeceraOrdenPedido = null, datosAdicionales = null) {
    var result = new Array();
    var send = {};

    send.metodo = "apiServicioImpresion";
    send.tipo = tipo;
    send.transaccion = transaccion;
    send.idCabeceraOrdenPedido = idCabeceraOrdenPedido;
    send.datosAdicionales = datosAdicionales;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../../impresion/cliente_ws_servicioImpresion.php",
        data: send,
        success: function (datos) {            
            if (datos === null ) {
                result["imprime"] = 'Error';
                result["mensaje"] = 'No hay conexion con el api de impresion';   
            } else {   
                result["imprime"] = datos.error;
                result["mensaje"] = datos.mensaje;    
            }           
        }
    });

    return result;
}

function apiServicioImpresionTest(tipo, transaccion = null, idCabeceraOrdenPedido = null, datosAdicionales = null) {
    var result = new Array();
    var send = {};

    send.metodo = "apiServicioImpresion";
    send.tipo = tipo;
    send.transaccion = transaccion;
    send.idCabeceraOrdenPedido = idCabeceraOrdenPedido;
    send.datosAdicionales = datosAdicionales;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "impresion/cliente_ws_servicioImpresion.php",
        data: send,
        success: function (datos) {            
            if (datos === null ) {
                result["imprime"] = 'Error';
                result["mensaje"] = 'No hay conexion con el api de impresion';   
            } else {   
                result["imprime"] = datos.error;
                result["mensaje"] = datos.mensaje;    
            }                   
        }
    });

        return result;
}

    const storedData = localStorage.getItem("requests");
    if (storedData) {
        const requests = JSON.parse(storedData);
        requests.forEach((request, index) => {
            if(request.dataProccess == 'Impresion'){
                apiServicioImpresion(request.tipoDocumento, request.transaccion);
            }
            requests.splice(index, 1);
            localStorage.setItem("requests", JSON.stringify(requests));
        });
    } 