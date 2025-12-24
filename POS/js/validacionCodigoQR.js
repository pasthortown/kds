/* global alertify */

// Autor: Juan Esteban Canelos
// Fecha: 03/09/2018
// Validación del formato del código QR 

//cc[0] = cedula
//cc[1] = token
function separarCedulaCodigo(codigo) {
    var cc = ["", ""];
    if (codigo.indexOf("-") > 0) {
        cc[0] = codigo.substring(0, codigo.indexOf("-"));
        cc[1] = codigo.substring(codigo.indexOf("-") + 1, codigo.length);
        return cc;
    } else {
        return;
    }
}

function validarCodigoSeguridad(codigo) {
    var valido = false;
    if (codigo !== "" && formatoCodigoSeguridad(codigo)) {
        valido = true;
    } else {
        fn_cargando(0);
        alertify.error("Error en la configuración del lector. Por favor comunicarse con la mesa de servicio.");
    }
    return valido;
}

var parseJwt = function( token ) {
    var base64Url = token.split('.')[1];
    var base64 = base64Url.replace('-', '+').replace('_', '/');
    return JSON.parse( window.atob( base64 ) );
}

//Validar JWT, ejemplo:
//eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOiJsdEpDa25SMkNyWnk2ZFpLZThZbUZGWkdvaXQxIiwiaWF0IjoxNTM1NzM3Nzg4LCJleHAiOjE1MzU4MjQxODh9.n-Z_fvlVET31cfkDqUP0HLcN3pAN6PoOVFQnxdcYHwI
function formatoCodigoSeguridad(codigo) {
    var regex = /^[\w\-]+\.[\w\-]+\.[\w\-]+$/;
    return regex.test(codigo);
}