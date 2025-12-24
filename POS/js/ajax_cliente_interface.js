/*
 DESARROLLADOR: Darwin Mora
 DESCRIPCION: Consume WS para interface
 FECHA CREACION: 27-01-2016  
 FECHA ULTIMA MODIFICACION: 29/06/2016
 USUARIO QUE MODIFICO: Darwin Mora
 DECRIPCION ULTIMO CAMBIO: Controlar intentos en Interface 
 HISTORIAL DE CAMBIOS:
    10/01/2017 Francisco Sierra
        Se incorpora el proceso de transferencia de venta 
 */

$(document).ready(function () {
    fn_cargando(0);
});    

function fn_generar_interface(accion, bandera) {    
    var send;
    var IDPeriodo = $('#IDPeriodo').val();

    send = {"validaVentaInterface": 1};
    send.accion = accion;
    send.id_periodo = IDPeriodo; 
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function (datos) {
            
            var existe_diferencia = datos.existe_diferencia;
            var mensaje = datos.mensaje;
            var interface_json = datos.interface_json;
            var interface_eventos = datos.interface_eventos;
            var json=datos.log_descuadre;

            if (existe_diferencia == 'SI') {
                alertify.alert('<h4><b>Atenci\u00F3n!! </b> <br><br>' + mensaje + '<br><br></h4>');
                enviarDatosInterface(IDPeriodo, interface_json, interface_eventos, bandera, accion);
            } else {   
                enviarDatosInterface(IDPeriodo, interface_json, interface_eventos, bandera, accion);
            }
        }
    });             
}

function fn_cargando(std) {
    if (std > 0) {
        $("#mdl_rdn_pdd_crgnd").show();
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
    }
}

function enviarDatosInterface(IDPeriodo, interface_json, interface_eventos, bandera, accion) {
    fn_cargando(1);

    alertify.success('Generando interface de venta...');

    if (IDPeriodo == '') {
        alertify.error('ID del Periodo Incorrecto!!');
        
        return false();
    }

    var send;
    
    send = {"realizaTransferencia": 1};
    $.ajax({
        async: true,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function (datos) {
            send = { "generarInterface": 1 };
            send.IDPeriodo = IDPeriodo;
            send.tipo_interface = interface_json;
            send.interface_eventos = interface_eventos;

            if (datos.Valida==="ORIGEN") {                
                // Se genera la interface desde ORIGEN 
                // (TOTAL DE VENTA - VENTA HELADERÍA)
                send.TipoTransferencia = "ORIGEN";
            } else if(datos.Valida==="DESTINO") {
                // Lógica de interface desde destino
                // (TOTAL DE VENTA + VENTA HELADERÍA ENTRANTE)
                send.TipoTransferencia = "DESTINO";
            } else {
                // Lógica de interface normal
                // (TOTAL DE VENTA)
                send.TipoTransferencia = "NORMAL";
            }
           
            $.ajax({
                async: true,
                type: "GET",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "../serviciosweb/interface/config_cliente_servicio.php",
                data: send,
                success: function (datos) {
                    fn_cargando(0);

                    //const obj = JSON.parse(datos[0][0]);
                    const resp = datos.Respuesta;
                    const msj = datos.Mensaje;
                    var mensaje;
                    
                    if (resp == 1 || resp == 0) {
                        mensaje = msj;                       
                    } else {
                        console.log(msj);
                        mensaje = "Existe problemas al generar interface de venta, por favor comuniquese con soporte!";
                    }

                    alertify.alert(mensaje);
                    $("#alertify-ok").click(function () {
                        if (accion == 1) {
                            if (bandera == 'Pickup') {
                                alertify.success('El cajero PickUp ha sido desasignado correctamente!');
                            } else {
                                alertify.success('El cajero ha sido desasignado correctamente!');
                            }

                            if (bandera == "Inicio") {
                                window.location.replace("../index.php");
                            } else {
                                window.location.replace("corteCaja.php");
                            }   
                        }
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    fn_cargando(0);

                    mensaje = "Existe problemas al generar interface de venta, por favor comuniquese con soporte!";

                    alertify.alert(mensaje);
                    $("#alertify-ok").click(function () {
                        if (accion == 1) {
                            if (bandera == "Inicio") {
                                window.location.replace("../index.php");
                            } else {
                                window.location.replace("corteCaja.php");
                            }   
                        }
                    });
                }
            });            
        }
    });     
}


