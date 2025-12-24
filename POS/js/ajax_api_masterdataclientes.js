function apiServicioMasterDataCliente(accion, data) {
    let validation = validacionesCampos(accion, data);

    if (validation.status == 200) {
        var response = sendApi(accion, data);
        return response;
    } else if (validation.status == 500) {
        return { status: 500, message: "ERROR", description: validation.description }
    }
}

function validacionesCampos(accion, data) {
    if (accion == "BUSCAR") {
        if(!data.cdn_id || !data.documento || !data.idUserPos || !data.rst_id){
            return { status: 500, message: "ERROR", description: "NO SE RECIBE LOS PARAMETROS NECESARIOS PARA CONSULTAR EN MDC" }
        }
        return { status: 200, message: "OK" }
    } else if (accion == "GUARDAR" || accion == "MODIFICAR") {
        if(!data.cdn_id || 
           !data.documento || 
           !data.tipoDocumento || 
           /* !data.email || */ 
           /* !data.telefono || */ 
           !data.primerNombre || 
           /* !data.direccion || */
           !data.idUserPos ||
           !data.rst_id)
        {
            return { status: 500, message: "ERROR", description: "NO SE RECIBE LOS PARAMETROS NECESARIOS PARA GUARDAR/MODIFICAR EN MDC" }
        }

        data.pais='ECU'
        data.aceptacionPoliticas=false;
        data.autenticacion=false;
        data.envioComunicacionesComerciales=false;
        data.envioComunicacionesComercialesPush=false;
        data.analisisDeDatosPerfiles=false;
        data.cesionDatosATercerosNacionales=false;
        data.cesionDatosATercerosInternacionales=false;
        data.sistemaOrigen="1";
        data.preguntaCanal=0;
        data.preguntaProducto=0;
        return { status: 200, data }   
    }
}

function sendApi(accion, data){
    url="../MasterDataClientes/ApiMasterDataClient.php";
    var send = {};
    var result = {};
    send.accion = accion;
    send.info = data;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: url,
        data: JSON.stringify(send),
        success: function (datos) {
            result = JSON.stringify(datos);
        }
    });
    return result;
}