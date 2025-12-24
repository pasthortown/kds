/*
FECHA CREACION   : 15/05/2017 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Reporte notas de crédito por clientes
*/

/* global alertify */

$(document).ready(function(){

    $('#mdl_rdn_pdd_crgnd').hide();
    
    $("#fechaDesde").daterangepicker({singleDatePicker: true, format: "DD/MM/YYYY", drops: "down"}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });
    $("#fechaHasta").daterangepicker({singleDatePicker: true, format: "DD/MM/YYYY", drops: "down"}, function (start, end, label) {
        console.log(start.toISOString(), end.toISOString(), label);
    });
    
    fn_cargaClientes();
    
});

function fn_generarReporte(){
    var fechaDesde = $("#fechaDesde").val();
    var fechaHasta = $("#fechaHasta").val();
    var identificacion = $("#selCliente option:selected").val();
    var aplicaNotasCreditoWS = $("#aplicaNotasCreditoWS").val();
    
    if(fechaDesde === ""){
        alertify.error("<b>Atención:</b> La fecha Desde es obligatoria.");
        $('#fechaDesde').focus();
        return false;        
    }    
    else if(fechaHasta === ""){
        alertify.error("<b>Atención:</b> La fecha Hasta es obligatoria.");
        $('#fechaHasta').focus();
        return false;        
    }    
    else if(identificacion == 0){
        alertify.error("<b>Atención:</b> Debe seleccionar un Cliente.");
        $('#selCliente').focus();
        return false;        
    }    

    if (aplicaNotasCreditoWS == 1) {
        $('#mdl_rdn_pdd_crgnd').show();
        
        send = {};
        send.metodo = "notas_credito";
        send.accion = "N";
        send.fechaDesde = fechaDesde;
        send.fechaHasta = fechaHasta;
        send.identificacion = identificacion;

        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../reporteNotaCreditoCliente/cliente_ws_notascredito.php",
            data: send,
            success: function (datos) {

                $('#mdl_rdn_pdd_crgnd').hide();

                var estado = datos['estado'];
                var mensaje = datos['mensaje']; 

                if (estado == 'OK') {
                    alertify.success('<b>Información enviada:</b> ' + mensaje);   
                } else if (estado == '001') {
                    alertify.success(mensaje);
                } else if (estado == '002') {
                    alertify.error('<b>Atención:</b> ' + mensaje);
                } else if (estado == '003') {
                    alertify.error('<b>Atención:</b> ' + mensaje);
                } else {
                    alertify.error('<b>Atención:</b> Ha ocurrido un error al consultar la información.!!!');
                }
            }
        });   
    }
}

function enviar() {
    var formulario = document.getElementById("frmReporte");
    var fechaDesde = $("#fechaDesde").val();
    var fechaHasta = $("#fechaHasta").val();
    var identificacion = $("#selCliente option:selected").val();

    if (fechaDesde != "" && fechaHasta != "" && identificacion != 0){
        formulario.submit();
        return true;
    } else {
        return false;
    }
}

function fn_cargaClientes() {
    var send;
    var html;
    var accion = "C";
    var identificacion;
    var cliente;
    send = {"CargaClientes":1};
    send.accion = accion;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
    url: "../reporteNotaCreditoCliente/config_notaCreditoCliente.php", data: send, success: function (datos) {
        if(datos.str === 0){
            alertify.error("No existe ning&uacute;n cliente.");
        }
        else if(datos.str > 0){
            $("#selCliente").html("");
            $('#selCliente').html("<option selected value='0'>-------------- Seleccioner Cliente --------------</option>");
            
            for(var i=0; i<datos.str; i++) {
                html="<option value='"+datos[i]['Documento']+"' name='"+datos[i]['Cliente']+"'>"+datos[i]['Documento']+" - "+datos[i]['Cliente']+"</option>";
                $("#selCliente").append(html);										
            }

            $("#selCliente").chosen();
            $("#selCliente").change(function(){                
                identificacion = $("#selCliente").val();
                cliente = $("#selCliente option:selected").attr("name");
                $("#identificacionCliente").val(identificacion);
                $("#descripcionCliente").val(cliente);
            });
        }
    }});
}