/* global alertify */

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: JOSE FERNANDEZ//////////////////////
////////DESCRIPCION		: AJAX DE PANTALLA APERTURA///////////
////////TABLAS			: PERIODO, ESTACION///////////////////
////////FECHA CREACION	: 20/12/2013//////////////////////////
//////////////////////////////////////////////////////////////

$(document).ready(function ()
{

    fn_traerLogoCadena();

});


function fn_grabaperiodo(opcion_apertura) {    
    var send;

    send = {"validaAccesoPerfil": 1};
    send.accion = 2;
    send.usr_usuario = $("#hid_idusuario").val();
    send.est_ip = $("#est_ip").val();
    send.rst_id = $("#hid_rest").val();
    
    send.cadena_des = $("#hid_cadena_des").val();
    send.rst_des = $("#hid_rst_des").val();
    send.bd_dest = $("#hid_bd_dest").val();
    send.transf = $("#hid_transf").val();
    fn_cargando(1);
  
    $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "confi_apertura.php", data: send, success: function (datos) {
            
            //alert(JSON.stringify(datos));
            if (datos.accesoperfil !== 0)
            {
                send = {"validaperiodoAbierto": 1};
                send.rst_id = $("#hid_rest").val();
                send.est_ip = $("#est_ip").val();
                $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded",
                    url: "confi_apertura.php", data: send, success: function (datos) {
                        if (datos.Estado !== 6)
                        {
                            send = {"grabaperiodo": 1};
                            send.accion = 'i';
                            send.rst_id = $("#hid_rest").val();
                            send.est_ip = $("#est_ip").val();
                            send.usr_usuario = $("#hid_idusuario").val();
                            send.opcion_apertura = opcion_apertura;
                            $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded",
                                url: "confi_apertura.php", data: send, success: function (datos) {
                                    dragontailLogin($("#hid_rest").val());
                                    alertify.alert("Per\xEDodo creado correctamente.");
                                    tiposervicio = $("#txt_tipoServicio").val();

                                    $("#alertify-ok").click(function () {
                                        window.location.replace("../index.php");
                                        $('#usr_clave').focus();
                                    });
                                }});
                        } else {
                            alertify.alert("<b>Atenci\xf3n..!! </b>  Ya existe un per\xEDodo abierto.");
                            window.location.replace("../index.php");
                        }
                    },complete: function () {
                    fn_cargando(0);
                }});
            } else {
               
               if( datos.tipo_problema!== null){
                    alertify.alert( datos.str);
                       fn_cargando(0);
                    //  
               }else {
                    alertify.alert("Permisos insuficientes para realizar esta operaci\xf3n.");
               }
                
                
              
                $("#alertify-ok").click(function () {
                    window.location.replace("../index.php");
                });
            }
        }, complete: function () {
                    fn_cargando(0);
                }});  
}

function dragontailLogin(restaurantId){
    send = { restaurantId:restaurantId}
    $.ajax({
        data: send,
        url: "../resources/module/domicilio/dragon-tail/loginController.php",
        type: 'POST',
        success: function(data){
            alertify.success(data);
        },
        error: function(jqXHR, exception) {
            alertify.error("error "+jqXHR.responseText);
        }
    });
}

function fn_traerLogoCadena()
{
    est_ip = $("#est_ip").val();
    var html = "";

    send = {"traerLogoCadena": 1};
    send.accion = 3;
    send.est_ip = est_ip;
    $.getJSON("../cierre/confi_apertura.php", send, function (datos)
    {
        if (datos.str > 0) {

            for (i = 0; i < datos.str; i++)
            {
                html = "<tr align='center'>";
                html += "<td><img src='../imagenes/Logos/" + datos.cdn_logotipo + "' width='200' height='100'></td></tr>";

                $("#hid_cadena").val(datos.cdn_id);
                $("#hid_rest").val(datos.rst_id);
                $("#hid_tiposervicio").val(datos.rst_tipo_servicio);
            }
            $("#tabla_Logo").append(html);
        }
    });
}
function fn_cargando(estado) {
    if (estado) {
        $('#cargando').css('display', 'block');
        $('#cargandoimg').css('display', 'block');
    } else {
        $('#cargando').css('display', 'none');
        $('#cargandoimg').css('display', 'none');
    }
}

