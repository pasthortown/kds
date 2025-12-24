$(document).ready(function () {
    $("#modalCamapanaSolidaria").hide();

    fn_validarSiAplica();
});

function fn_validarSiAplica() {
    localStorage.removeItem('campana_solidaria');
    localStorage.setItem('campana_solidaria', JSON.stringify({ 'aplica': 0, 'valor': 0, 'secuencia': 0 }));
    $('#btnCampanaSolidaria').hide();

    let send = {'metodo' : 'aplicaCampanaSolidaria'};
    
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_campanaSolidaria.php",
        data: send,
        success: function (datos) {
            let valor = parseFloat(datos.valor);
            localStorage.setItem('campana_solidaria', JSON.stringify({ 'aplica': datos.aplica, 'valor': valor.toFixed(2), 'secuencia': datos.secuencia, 'cantidadLimite':datos.cantidadLimite }));

            if(datos.aplica == 1) {
                $('#btnCampanaSolidaria').show();
            } 
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_modalCampanaSolidaria() {
    const configuracion =  JSON.parse(localStorage.getItem('campana_solidaria'));
    $("#valorUnitario").text(configuracion.valor);
    $("#valorUnitarioCampañaSolidaria").val(configuracion.valor);

    fn_calularValorCampanaSolidaria();

    $("#modalCamapanaSolidaria").show();
    $("#modalCamapanaSolidaria").dialog({
        modal: true,
        position: {
            my: "top",
            at: "top+100"
        },
        width: 500,
        heigth: 500,
        resize: false,
        opacity: 100,
        show: "none",
        hide: "none",
        duration: 500,
        open: function (event, ui) {
            $(".ui-dialog-titlebar").hide();
            $("#input_cuponSistemaGerenteAut").val("");
            $(".ui-widget-overlay").css("opacity", 0.5);
        }
    });
}

function fn_cerrarModalCampanaSolidaria() {
    $("#keyboard").hide();
    $("#keyboard").empty();
    $("#modalCamapanaSolidaria").hide();
    $("#modalCamapanaSolidaria").dialog("close");
}

function fn_agregarNumeroCampanaSolidaria(valor) {
    var cantidad = $("#cantidadCampañaSolidaria").val();
    if (cantidad.length == 0 && valor == 0) {
    } else if ((cantidad.length == 0 && valor == ".")) {
        $("#cantidadCampañaSolidaria").val("0.");
        coma = 1;
    } else {
        if (valor == "." && coma == 0) {
            cantidad = cantidad + "" + valor;
            $("#cantidadCampañaSolidaria").val(cantidad);
            coma = 1;
        } else if (valor == "." && coma == 1) {
        } else {
            var variable = cantidad;
            var indice = 0;
            indice = variable.indexOf(".") + 1;
            if (indice > 0) {
                variable = variable.substring(indice, variable.length);
                if (variable.length <= 2) {
                    cantidad = cantidad + "" + valor;
                    $("#cantidadCampañaSolidaria").val(cantidad);
                    fn_focusLector();
                }
            } else {
                cantidad = cantidad + "" + valor;
                $("#cantidadCampañaSolidaria").val(cantidad);
                fn_focusLector();
            }
        }
    }
    fn_calularValorCampanaSolidaria();
    fn_focusLector();
}

function fn_eliminarCantidadCampanaSolidaria() {
    var lc_cantidad = $("#cantidadCampañaSolidaria").val();
    if (lc_cantidad != "0.") {
        lc_cantidad = lc_cantidad.substring(0, document.getElementById("cantidadCampañaSolidaria").value.length - 1);
        if (lc_cantidad == "") {
            lc_cantidad = "";
            coma = 0;
        }
        if (lc_cantidad == ".") {
            coma = 0;
        }
        $("#cantidadCampañaSolidaria").val(lc_cantidad);
    } else {
        $("#cantidadCampañaSolidaria").val("");
    }

    fn_calularValorCampanaSolidaria();
}

function fn_cancelarAgregarCantidadCampanaSolidaria() {
    $("#cantidadCampañaSolidaria").val("");
    fn_calularValorCampanaSolidaria();
}

function fn_calularValorCampanaSolidaria() {
    const configuracion =  JSON.parse(localStorage.getItem('campana_solidaria'));

    let total = parseFloat(0);
    if($("#cantidadCampañaSolidaria").val() !== '') {
        total = parseFloat(configuracion.valor) * parseFloat($("#cantidadCampañaSolidaria").val());
    }

    $("#valorTotalCampañaSolidaria").val(total.toFixed(2));
    $("#valorTotal").text(total.toFixed(2));
}

function fn_okCantidadCampanaSolidaria() {
    const valorTotal = parseFloat($("#valorTotalCampañaSolidaria").val());
    if (isNaN(valorTotal) || valorTotal <= 0) {
        alertify.alert("El valor de la campaña solidaria debe ser superior a cero (0).");
        return;
    }

    const cantidad = parseFloat($("#cantidadCampañaSolidaria").val());
    if (isNaN(cantidad) || cantidad <= 0) {
        alertify.alert("La cantidad de la campaña solidaria debe ser superior a cero (0).");
        return;
    }

    const valorUnitario = parseFloat($("#valorUnitarioCampañaSolidaria").val());
    if (isNaN(valorUnitario) || valorUnitario <= 0) {
        alertify.alert("El valor unitario de la campaña solidaria debe ser superior a cero (0).");
        return;
    }

    alertify.set({ labels: { ok: "SI", cancel: "NO" } });
    alertify.confirm("¿Desea generar la transacción de la campaña solidaria por un valor de $"+valorTotal.toFixed(2)+"?", function (e) {
        if (e) {
            fn_generarCampanaSolidaria(valorUnitario, cantidad, valorTotal);
        }
    });
}

function fn_generarCampanaSolidaria(valorUnitario, cantidad, valorTotal) {
    const configuracion =  JSON.parse(localStorage.getItem('campana_solidaria'));

    let send = {'metodo' : 'registrarCampanaSolidaria'};
    send.valorTotal = valorTotal;
    send.valorUnitario = valorUnitario;
    send.cantidad = cantidad;
    send.secuencia = configuracion.secuencia;
    send.ruta = localStorage.getItem('api_impresion_ruta');

    if (configuracion.cantidadLimite > 0 && configuracion.cantidadLimite !== "" && configuracion.cantidadLimite != null) {

        if (cantidad > configuracion.cantidadLimite){
            alertify.alert("Has superado el número máximo de cupones permitidos en la campaña solidaria. El límite es: " + configuracion.cantidadLimite);
            return;
        }

    } 

    $("#ok_cantidad").attr("disabled", true);

    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_campanaSolidaria.php",
        data: send,
        success: function (datos) {
            if ('estado' in datos && datos.estado == 1) {
                const datosAdicionales = {
                    'codigo': datos.codigo,
                    'ruta': localStorage.getItem('api_impresion_ruta')
                }
                let apiImpresion = getConfiguracionesApiImpresion();

                let result = new apiServicioImpresion('impresionCampanaSolidaria', null, null, datosAdicionales);
                    aperturaCajon();

                if (apiImpresion.api_impresion_tienda_aplica == '1' || apiImpresion.api_impresion_estacion_aplica == '1') {

                    var imprime = result["imprime"];
                    var mensaje = result["mensaje"];

                    if (!imprime) {
                        alertify.success('Imprimiendo...');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);

                    } else {
                        
                        fn_cerrarModalCampanaSolidaria();
                        anularRetiroCampanaSolidaria(datos.codigo)
                        fn_cancelarAgregarCantidadCampanaSolidaria()
                        alertify.success('Error al imprimir...');

                    }
                }

            }else {
                anularRetiroCampanaSolidaria(datos.codigo)
                fn_cancelarAgregarCantidadCampanaSolidaria()
                fn_cerrarModalCampanaSolidaria();
                alertify.alert(datos.codigo);
            }

            fn_cerrarModalCampanaSolidaria();
            $("#ok_cantidad").attr("disabled", false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
            anularRetiroCampanaSolidaria(datos.codigo)
            fn_cancelarAgregarCantidadCampanaSolidaria()
            $("#ok_cantidad").attr("disabled", false);
        }
    });
}

function anularRetiroCampanaSolidaria(IDretiro){

    let send = {'metodo' : 'anularCampanaSolidaria'};
    send.IDretiro = IDretiro;

    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_campanaSolidaria.php",
        data: send,
        success: function (datos) {
            console.log('campaña solidaria anulada')
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('error campaña solidaria anulada')
        }
    });

}

function aperturaCajon() {
    let apiImpresion = getConfiguracionesApiImpresion();
    if (apiImpresion.api_impresion_tienda_aplica == '0' || apiImpresion.api_impresion_estacion_aplica == '0') {
        return;
    }
    
    let send = { "servicioApiAperturaCajon": 1 };
    send.idFormaPago = '';

    $.getJSON("../facturacion/config_servicioApiAperturaCajon.php", send, function (datos) { 
    });
    
}