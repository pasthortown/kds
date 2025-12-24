$(document).ready(function () 
{
    // se valida que no este demontado el cajero para adicionar fondo
    var send = {"validaCajeroActivo": 1};    
    $.ajax({async: false, url: "config_adicionarFondo.php", data: send, dataType: "json",
        success: function (datos) 
        {           
            if(datos.str>0)
            {
                if(datos[0].controlCajero==='Inactivo')
                {
                    alertify.alert(datos[0].mensaje);
                    $("#alertify-ok").click(function (event) 
                     {
                         event.stopPropagation();                        
                         window.location.replace("../index.php");
                     }); 
                }
                
            }
            else
            {
                alertify.alert("ERROR AL LEER INFORMACION.");
                $("#alertify-ok").click(function (event) 
                     {
                         event.stopPropagation();                        
                         window.location.replace("../index.php");
                     }); 
            }
        }, error: function (xhr, ajaxOptions, thrownError) {
            alertify.alert("ERROR AL LEER INFORMACION");
            $("#alertify-ok").click(function (event) 
                     {
                         event.stopPropagation();                        
                         window.location.replace("../index.php");
                     }); 
        }
    });
    
});

function fn_adicionarFondo()
{    
    if($('#usr_admin_fondo').val().trim() === '')
    {
        alertify.alert("Debe Ingresar Un Valor.");
        return false;
    }
    
    alertify.set({labels: {ok: "SI",cancel: "NO"}});
    moneda  = $("#txt_moneda").val();
    valor   = $("#usr_admin_fondo").val();
    
    alertify.confirm("Est&aacute; usted seguro/a que el fondo asignado de <b><h3>" + moneda +" " +valor + "</h3></b> es correcto.?", function (e) {
        if (e) 
        {
            var send = {"adicionaFondo": 1};
            send.valorFondo = $("#usr_admin_fondo").val();
            $.ajax({async: false, url: "config_adicionarFondo.php", data: send, dataType: "json",
                success: function (datos) 
                {
                    if(datos.str>0)
                    {
    
                     // Aplicar apertura cajon
                        var apiImpresion = getConfiguracionesApiImpresion();
                        if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1 && apiImpresion.api_impresion_asignacion_retiro_fondo == 1) {
                    
                            send = { "servicioApiAperturaCajon": 1 };
                            send.idFormaPago = '';
    
                            $.getJSON("../facturacion/config_servicioApiAperturaCajon.php", send, function (datos) {
                                    console.log(datos) 
                                    window.location.replace("../index.php");
                    
                                });
    
                            }else{
    
                                window.location.replace("../index.php");
                            }
    
                        
                    }
                    else
                    {
                        alertify.error("ERROR AL INGRESAR ADICION DE FONDO DE CAJA");
                    }
                }, error: function (xhr, ajaxOptions, thrownError) {
                    alertify.alert("ERROR AL INGRESAR ADICION DE FONDO DE CAJA.");
                }
            });
        }
        else
        {
            $("#usr_admin_fondo").val("");
        }
    });
    
    
}

function fn_cancelarAdicionFondo()
{
     window.location.replace("../index.php");
}