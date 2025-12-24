/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* global alertify */

$(document).ready(function () {    
    $('#mdlPrincipalAgregadores').hide(); 
});

function cargarModalAgreagdores() {
    $('#mdlPrincipalAgregadores').css('opacity', '1');
    $('#mdlPrincipalAgregadores').show();
    $('#mdlAgregdores').show();      
    $('.contenedor').shortscroll();
    
    var send;
    var html = "";
    
    send = {"obtener_agregadores": 1};
    $.ajax({
        async: false
        , type: "POST"
        , dataType: "json"
        , contentType: "application/x-www-form-urlencoded"
        , url: "config_facturacion.php"
        , data: send
        , success: function (datos){
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    var id_agregador = (datos[i]['id_agregador']);
                    var agregador = (datos[i]['agregador']); 
                    var imagen_agregador = (datos[i]['imagen']);
                    var id_tipo_formapago = (datos[i]['IDTipoFormaPago']);
                    var tipo_formapago = (datos[i]['tipo_forma_pago']);
                    var requiere_autorizacion = (datos[i]['requiere_autorizacion']);
                    var tipo = (datos[i]['tipo']);
                    var botonClick;
                    
                    if ((requiere_autorizacion == 1)) {
                        botonClick = 'onclick="solicitarCredencialesAdministrador(\''+tipo+'\', \''+id_agregador+'\');"';                                                
                    } else {
                        botonClick = 'onclick="fn_pagoCredito(\''+tipo+'\', \''+id_agregador+'\');"';
                    }
                    
                    html = html + '<div class="bordeAgregador" style="height: 120px; width: 200px; padding-top: 9px; margin: 5px 20px 5px 10px;" align="center">';
                    html = html +   '<button class="botonAgregadores" style="padding: 5px 5px 5px 5px; background: #E6E6E6 no-repeat center url(data:image/png;base64,'+imagen_agregador+');" '+botonClick+'></button>';
                    html = html +   '<div class="nombreAgregador">'+agregador+'</div>';
                    html = html + '</div>';
                    
                    $("#agregadores").html(html);
                }
            }
        }
    });
}

function cerrarModalAgegadores() {
    $('#mdlPrincipalAgregadores').hide();
    $('#mdlAgregdores').hide(); 
}

function solicitarCredencialesAdministrador(tipo_forma_pago, id_agregador) {
    cerrarModalAgegadores();
    
    $("#clave_administrador").val("");
    $("#credenciales_administrador").show();
    
    $("#credenciales_administrador").dialog({
        modal: true,
        draggable: false,
        width: 500,
        heigth: 500,
        resizable: false,
        opacity: 0,
        show: "none",
        hide: "none",
        duration: 500
    });
    
    var html = "";
    
    html = html + '<button style="font-size:45px;" class="btnVirtualOKpq" onclick="validar_credenciales(\''+tipo_forma_pago+'\', \''+id_agregador+'\');">OK</button>';
    
    $("#boton_validacion").html(html);
}

function validar_credenciales(tipo_forma_pago, id_agregador) {
    var send;
    var credenciales = $("#clave_administrador").val();
    
    if (credenciales == "") {
        $("#clave_administrador").focus();        
        alertify.alert("Debe ingresar sus credenciales de administrador.");
        
        return false;
    }    
    
    send = {"validarUsuarioAdministrador": 1};
    send.usr_Admin = credenciales;
    send.facturaAuditoria = $("#txtNumFactura").val();
    
    $.getJSON("config_facturacion.php", send, function (datos) {
        $("#clave_administrador").val("");
        
        if (datos.admini == 1) {
            $("#credenciales_administrador").dialog("close");
            
            console.log("tipo", tipo_forma_pago);
            console.log("id ", id_agregador);
            fn_pagoCredito(tipo_forma_pago, id_agregador);            
        } else {
            alertify.alert("Su clave de administrador es incorrecta.");
            
            $("#clave_administrador").val("");
            
            return false;
        }
    });
}

function cerrarModalCredencialesAdministrador() {
    $("#credenciales_administrador").hide();
    $("#credenciales_administrador").dialog('close');    
    $("#clave_administrador").val('');
}
