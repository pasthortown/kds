/* global alertify, alertity */

$(document).ready(function () {
});

function cargando(lc_estado) {
    if (lc_estado == 0) {
        $("#loading").dialog({
            maxHeight: 270,
            width: 300,
            title: 'Procesando...',
            resizable: false,
            position: "center",
            draggable: false,
            closeOnEscape: false,
            modal: true,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide();
            }
        });
    } else if (lc_estado == 1) {
        $("#loading").dialog("destroy");
    }
}

function fn_cargando(estado) {
    if (estado) {
        $("#cargando").css("display", "block");
        $("#cargandoimg").css("display", "block");
    } else {
        $("#cargando").css("display", "none");
        $("#cargandoimg").css("display", "none");
    }
}