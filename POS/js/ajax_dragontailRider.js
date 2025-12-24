async function sendRiderToDragontail(motoroloId, accion) {
    let session = JSON.parse(await getSessionConfig());
    let send = { restaurantId: session['idRestaurante'], motoroloId, accion: accion }
    $.ajax({
        type: "POST",
        url: "../../resources/module/domicilio/dragon-tail/riderController.php",
        data: send,
        dataType: "json",
        success: function(data){
            alertify.success(data);
        },
        error: function(jqXHR, exception) {
            alertify.error("error al crear/asignar motorizado en Dragontail" + jqXHR.responseText);
        }
    });
}

function getSessionConfig() {
    return $.ajax({
        url: "../../resources/module/domicilio/dragon-tail/sessionControler.php",
        type: 'GET',
    });
}
