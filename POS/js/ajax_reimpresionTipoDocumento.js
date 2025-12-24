/* global alertify, alertity */

$(document).ready(function () {
});

function impresionFactura(cfac_id, estacion, datos, impresora) {
    let apiImpresion = getConfiguracionesApiImpresion();
    
    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
        fn_cargando(1);
        let result = new apiServicioImpresion('factura', cfac_id, estacion, 0, datos, impresora);
        let imprime = result["imprime"];
        let mensaje = result["mensaje"];

        console.debug(imprime);
        console.debug(mensaje);

        if (!imprime) {
            alertify.success('Imprimiendo factura...');
        } else {
            alertify.success('Error al imprimir...');

        }

        fn_cargando(0);
    }
}

function impresionNotaCredito(ncred_id, estacion, datos, impresora) {
    let apiImpresion = getConfiguracionesApiImpresion();
    
    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
        fn_cargando(1);
        let result = new apiServicioImpresion('nota_credito', ncred_id, estacion, 0, datos, impresora);
        let imprime = result["imprime"];
        let mensaje = result["mensaje"];

        console.debug(imprime);
        console.debug(mensaje);

        if (!imprime) {
            alertify.success('Imprimiendo nota credito...');
        } else {
            alertify.success('Error al imprimir...');

        }

        fn_cargando(0);
    }
}

function impresionOrdenPedido(cfac_id, estacion, datos, impresora) {
    let apiImpresion = getConfiguracionesApiImpresion();
    
    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
        fn_cargando(1);
        let result = new apiServicioImpresion('orden_pedido', cfac_id, estacion, 0, datos, impresora);
        let imprime = result["imprime"];
        let mensaje = result["mensaje"];

        console.debug(imprime);
        console.debug(mensaje);

        if (!imprime) {
            alertify.success('Imprimiendo orden pedido...');
        } else {
            alertify.success('Error al imprimir...');

        }

        fn_cargando(0);
    }
}