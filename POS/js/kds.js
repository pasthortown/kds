
function fn_APIkds(tipo, codigo = '', cuenta = -1) {

    let orden = codigo !== '' ? codigo : $("#txtOrdenPedidoId").val();

    let send = {    
        "metodo":tipo,
        "IDRestaurante":$("#txtRestaurante").val(),
        "IDOrdenPedido":orden,
        "cuenta":cuenta
    };

    $.ajax({
        async: false, 
        type: "POST",
        dataType: "json", 
        contentType: "application/json",
        url: "../serviciosweb/kds/cliente_ws_servicioKds.php",
        data: JSON.stringify(send),
        success: function (datos) {
                console.log('KDS NETCORE');
        },
        error: function(xhr, status, error) {
            console.error("Error: " + status + " " + error);
        }
    });
}