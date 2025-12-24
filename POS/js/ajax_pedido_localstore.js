$(document).ready(function() 
{
});

function localStoragePedido(cadena, restaurante) {
    try {
        listaMedioEstacion()
        listaMedio(cadena)
        timeOut()
    } catch (error) {
        alert(error);
    }
    
    return 
}

function listaMedioEstacion() {
    try {
        let send = { "accion": 1 };
        send.filtrarPedidosMedios = 1;

        $.ajax({
            async: false,
            type: "POST",
            dataType: 'json',
            contentType: "application/x-www-form-urlencoded",
            url: "ordenpedido/config_ordenPedido.php",
            data: send,
            success: function(datos) {
                if (datos.str > 0) {
                    localStorage.removeItem("pedidos_medios_filtra");
                    localStorage.setItem("pedidos_medios_filtra", JSON.stringify(datos));
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert(jqXHR);
                alert(textStatus);
                alert(errorThrown);
            }
        });
    } catch (error) {
        alert(error);
    }
}

function listaMedio(cadena) {
    try {
        send = { metodo: "cargarListaMedios" };
        send.idCadena = cadena;

        $.ajax({
            async: false,
            type: "POST",
            dataType: 'json',
            contentType: "application/x-www-form-urlencoded",
            url: "ordenpedido/config_app.php",
            data: send,
            success: function(datos) {
                if (datos.registros == 0) {
                    alert('No se pudo obtener la lista medios');
                    return
                }

                let medios = filtraMedio(datos);

                localStorage.removeItem("pedidos_medios");
                localStorage.setItem("pedidos_medios", JSON.stringify(medios));

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert(jqXHR);
                alert(textStatus);
                alert(errorThrown);
            }
        });
    } catch (error) {
        alert(error);
    }
}

function timeOut() {

    try {
        let send = { "accion": 1 };
        send.consultarTimeOut = 1;

        $.ajax({
            async: false,
            type: "POST",
            dataType: 'json',
            contentType: "application/x-www-form-urlencoded",
            url: "ordenpedido/config_ordenPedido.php",
            data: send,
            success: function(datos) {
                if (datos.str > 0) {
                    localStorage.removeItem("time_out");
                    localStorage.setItem("time_out", JSON.stringify(datos.timeout));
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert(jqXHR);
                alert(textStatus);
                alert(errorThrown);
            }
        });
    } catch (error) {
        alert(error);
    }
}

function filtraMedio(medios) {

    let strMedios       = localStorage.getItem('pedidos_medios_filtra');
    strMedios           = JSON.parse(strMedios)
    let mediosFiltro    = strMedios[0].medios

    let mediosVisualizar = mediosFiltro.split(",");
    let cantidadTotalRegistros = medios.registros;
    let mediosFiltrados = [];
    let contadorRegistros = 0;
    
    if(mediosFiltro == "") {
        for(let indexMedios = 0; indexMedios < cantidadTotalRegistros; indexMedios++) {
            let medioVisualizar = medios[indexMedios];
            mediosFiltrados.push(medioVisualizar);
            contadorRegistros++;
        }
    }

    mediosVisualizar.forEach( medio =>{
        for(let indexMedios = 0; indexMedios < cantidadTotalRegistros; indexMedios++) {
            let medioVisualizar = medios[indexMedios];
            if( medio.toUpperCase().trim() == medioVisualizar?.codigo.toUpperCase().trim() ){
                mediosFiltrados.push(medioVisualizar);
                contadorRegistros++;
            }
        }
    });

    if(mediosFiltrados.length > 0){
        mediosFiltrados['registros'] = contadorRegistros;
        medios = mediosFiltrados;
    }

    return medios
}

function removeLocalStoragePedido() {
    localStorage.removeItem("time_out");
    localStorage.removeItem("pedidos_medios_filtra");
    localStorage.removeItem("pedidos_medios");
}