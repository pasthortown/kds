
/* global moment, alertify, parseInt, parseFloat */

//////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Daniel Llerena///////////////////////////////////////
///////DESCRIPCION	   : Archivo de configuracion del Modulo Retiro Fondo/
////////FECHA CREACION : 10/11/2015 //////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR       : Christian Pinto ////////////////////////////////
///////DESCRIPCION          : Cierre Periodo abierto mas de un día ///////////
///////FECHA MODIFICACIÓN   : 07/07/2016 /////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////


$(document).ready(function () {

    $("#credencialesAdmin").hide();
    $("#credencialesAdminteclado").hide();
    if ($("#txt_bloqueado").val() != 0) {
        $("#nuevaorden").attr("Disabled", true);
        $("#nuevaorden").removeClass("boton_Opcion");
        $("#nuevaorden").addClass("boton_Opcion_Bloqueado");
    }
    //Modal Menu Desplegable
    $('#id_menu_desplegable').css('display', 'none');
    $('#id_modal_opciones_drc').css('display', 'none');

    $('#boton_sidr').click(function () {
        $('#id_menu_desplegable').css('display', 'block');
        $('#id_modal_opciones_drc').css('display', 'block');
    });

    $('#id_menu_desplegable').click(function () {
        $('#id_menu_desplegable').css('display', 'none');
        $('#id_modal_opciones_drc').css('display', 'none');
    });

    var fecha = moment().format('DD/MM/YYYY hh:mm:ss');
    $('#tqt_fch_ctl').html(fecha);

    fn_detalleRetiroFondo();

    if ($("#banderaCierrePeriodo").val() === 'FinDeDiaCierrePeriodo') {
        $('#nuevaorden').css('display', 'none');
    }
});

function fn_detalleRetiroFondo() {
    var send;
    var CargaDetallesFondoAsignado = { "CargaDetallesFondoAsignado": 1 };
    var simbolo = $("#moneda").val();
    var html = " ";

    send = CargaDetallesFondoAsignado;
    send.usr_claveAdmin = "x";
    send.tarjeta = "x";
    $.getJSON("config_retiroFondo.php", send, function (datos) {
        if (datos.str > 0) {
            $("#tbl_detalle_Retiro").empty();

            for (var i = 0; i < datos.str; i++) {
                $("#tqt_fch_prd").html(datos[0]["periodo"]);
                if (datos[i]["fondoretirado"] == null) {
                    html += '<tr><td class="text-center" style="height:40px">' + simbolo + ' ' + parseFloat(datos[i]["fondoasignado"]).toFixed(2) + '</td><td class="text-center">' + datos[i]["supervisorasignar"] + '</td><td class="text-center">' + datos[i]["cajeroasignado"] + '</td><td class="text-center">' + datos[i]["fechaconfirmacion"] + '</td><td class="text-center">' + simbolo + ' ' + parseFloat(datos[i]["fondoasignado"]).toFixed(2) + '</td><td> </td><td> </td><td class="text-center" style="color:#FFF; background:#F00">POR RETIRAR</td></tr>';
                } else {
                    html += '<tr><td class="text-center" style="height:40px">' + simbolo + ' ' + parseFloat(datos[i]["fondoasignado"]).toFixed(2) + '</td><td class="text-center">' + datos[i]["supervisorasignar"] + '</td><td class="text-center">' + datos[i]["cajeroasignado"] + '</td><td class="text-center">' + datos[i]["fechaconfirmacion"] + '</td><td class="text-center">' + simbolo + ' ' + parseFloat(datos[i]["fondoasignado"]).toFixed(2) + '</td><td class="text-center">' + datos[i]["supervisorretiro"] + '</td><td class="text-center">' + datos[i]["fecharetiro"] + '</td><td class="text-center" style="color:#FFF; background:#36F">RETIRADO</td></tr>';
                }

                $("#tbl_detalle_Retiro").html(html);
            }
        }
        else {
            html += "<tr><td colspan='8' style='text-align: center;'>No existe registro.</td></tr>";
            $("#tbl_detalle_Retiro").html(html);
        }
    });
}

function fn_validaAdmin() {

    var send;
    var ValidaFondoRetirado = { "ValidaFondoRetirado": 1 };

    send = ValidaFondoRetirado;
    send.usr_claveAdmin = $("#usrrid").val();
    send.tarjeta = "0";
    $.getJSON("config_retiroFondo.php", send, function (datos) {
        if (datos.valida == 1) {
            $("#credencialesAdmin").show();
            $("#credencialesAdmin").dialog({
                modal: true,
                width: 500,
                heigth: 500,
                resize: false,
                opacity: 0,
                show: "none",
                hide: "none",
                duration: 500,
                open: function (event, ui) {
                    $(".ui-dialog-titlebar").hide();
                    $("#credencialesAdminteclado").show();
                }
            });
        }
        else {
            alertify.alert("El fondo asignado ya ha sido retirado.");
            return false;
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

function fn_retirofondo() {

    var send;
    var validarUsuarioAdministrador = { "validarUsuarioAdministrador": 1 };

    var usr_clave = $("#usr_claveAdmin").val();

    if (usr_clave.indexOf('%') >= 0) {
        var old_usr_clave = usr_clave.split('?;')[0];
        var new_usr_clave = old_usr_clave.replace(new RegExp("%", 'g'), "");
        var usr_tarjeta = new_usr_clave;
        usr_clave = "noclave";
    }
    else {
        var usr_tarjeta = 0;
    }

    if ($("#usr_claveAdmin").val() == "") {
        $("#usr_claveAdmin").focus()
        alertify.alert("Ingrese una clave.");
        return false;
    }

    send = validarUsuarioAdministrador;
    send.accion = 1;
    send.usr_claveAdmin = usr_clave;
    send.usr_claveCajero = "0";
    send.est_ip = $("#dirIp").val();
    send.tarjeta = usr_tarjeta;
    $.getJSON("config_retiroFondo.php", send, function (datos) {
        var simbolomoneda = datos.moneda;
        if (datos.admini == 1) {
            var ConsultaFondoAsignado = { "ConsultaFondoAsignado": 1 };
            send = ConsultaFondoAsignado;
            send.usr_claveAdmin = usr_clave;
            send.tarjeta = usr_tarjeta;
            $.getJSON("config_retiroFondo.php", send, function (datos) {
                var cantidad = datos.fondo;
                var cajero = datos.cajero;
                $("#credencialesAdmin").dialog("close");
                $("#credencialesAdminteclado").hide();

                alertify.set({
                    labels: {
                        ok: "SI",
                        cancel: "NO"
                    }
                });

                alertify.confirm("Desea realizar un retiro de fondo asignado por un valor de: <b><h3>" + simbolomoneda + +cantidad + "</b></h3> del cajero/a: " + cajero, function (e) {
                    if (e) {
                        var RetirarFondoAsignado = { "RetirarFondoAsignado": 1 };
                        send = RetirarFondoAsignado;
                        send.usr_claveAdmin = usr_clave;
                        send.tarjeta = usr_tarjeta;

                        var apiImpresion = getConfiguracionesApiImpresion();       
                        if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                            console.log('imprime:');
                            var result = new apiServicioImpresion('retiraFondos',null,null, send);
                            var imprime = result["imprime"];
                            var mensaje = result["mensaje"];
                            
                            if (!imprime) {
                                alertify.success('Imprimiendo Retiro Fondos...');

                                if(apiImpresion.api_impresion_asignacion_retiro_fondo == 1){

                                    // Api apertura de cajon
                                    send = { "servicioApiAperturaCajon": 1 };
                                    send.idFormaPago = ''
                
                                    $.getJSON("../facturacion/config_servicioApiAperturaCajon.php", send, function (datos) { 
                            
                                        console.log(datos);                                        
                                        window.location.reload();
                                        fn_detalleRetiroFondo();
                                        var bandera = $("#txt_banderaDesasignar").val(); // bandera para volver al desmontado de cajero
                                        
                                        if (bandera === 'volverDesasignar')
                                        {
                                            window.location.replace("../corteCaja/desmontado_cajero.php");
                                        }
                                        else
                                        {
                                            window.location.reload();
                                        }
                                        
                            
                                        });

                                }else{
                                      
                                    window.location.reload();
                                    fn_detalleRetiroFondo();
                                    var bandera = $("#txt_banderaDesasignar").val(); // bandera para volver al desmontado de cajero
                                    
                                    if (bandera === 'volverDesasignar')
                                    {
                                        window.location.replace("../corteCaja/desmontado_cajero.php");
                                    }
                                    else
                                    {
                                        window.location.reload();
                                    }
                                    

                                }



                            } else {


                                
                            if(apiImpresion.api_impresion_asignacion_retiro_fondo == 1){

                                // Api apertura de cajon
                                send = { "servicioApiAperturaCajon": 1 };
                                send.idFormaPago = ''

                                $.getJSON("../facturacion/config_servicioApiAperturaCajon.php", send, function (datos) { 

                                
                                    console.log(datos);
                            
                                        });


                                alertify.alert(mensaje);

                                alertify.success('Error al imprimir Retiro Fondos...');
                                fn_cargando(0);
                                window.location.reload();
                                fn_detalleRetiroFondo();
                                var bandera = $("#txt_banderaDesasignar").val(); // bandera para volver al desmontado de cajero
                                
                                if (bandera === 'volverDesasignar')
                                {
                                    window.location.replace("../corteCaja/desmontado_cajero.php");
                                }
                                else
                                {
                                    window.location.reload();
                                }


                            }else{

                                fn_cargando(0);
                                window.location.reload();
                                fn_detalleRetiroFondo();
                                var bandera = $("#txt_banderaDesasignar").val(); // bandera para volver al desmontado de cajero
                                
                                if (bandera === 'volverDesasignar')
                                {
                                    window.location.replace("../corteCaja/desmontado_cajero.php");
                                }
                                else
                                {
                                    window.location.reload();
                                }

                                
                            }

                        }


                        }else{

                            $.getJSON("config_retiroFondo.php", send, function (datos) {
                                window.location.reload();
                                fn_detalleRetiroFondo();
                                var bandera = $("#txt_banderaDesasignar").val(); // bandera para volver al desmontado de cajero

                                if (bandera === 'volverDesasignar') {
                                    window.location.replace("../corteCaja/desmontado_cajero.php");
                                }
                                else {
                                    window.location.reload();
                                }

                            });
                            $("#usr_claveAdmin").val("");
                        }
                    }
                    else {
                        $("#usr_claveAdmin").val("");
                    }
                });
            });
        }
        else {
            alertify.alert("Usuario no autorizado para realizar retiros de fondo.");
            $("#usr_claveAdmin").val("");
            return false;
        }
    });
}

function fn_cerrarValidaAdmin() {

    $("#credencialesAdmin").dialog("close");
    $("#credencialesAdminteclado").hide();
    $("#usr_claveAdmin").val("");
}

function fn_funcionesGerente() {
    window.location.href = "../funciones/funciones_gerente.php";
}

function fn_salirSistema() {
    window.location.href = "../index.php";
}

function fn_TomaPedido() {
    fn_obtenerMesa();
}

function fn_obtenerMesa() {

    var send;
    var obtenerMesa = { "obtenerMesa": 1 };

    send = obtenerMesa;
    $.getJSON("config_retiroFondo.php", send, function (datos) {
        if (datos.str > 0) {
            if (datos.respuesta === 3) {
                $("#cntFormulario").html('<form action="../facturacion/factura.php" name="formulario" method="post" style="display:none;"><input type="text" name="odp_id" value="' + datos.IDOrdenPedido + '" /><input type="text" name="dop_cuenta" value="' + 0 + '" /><input type="text" name="mesa_id" value="' + datos.IDMesa + '" /></form>');
                document.forms["formulario"].submit();
            }
            else {
                window.location.replace("../ordenpedido/tomaPedido.php?numMesa=" + datos.IDMesa);
            }
        }
        else {
            alertify.alert("Este local no tiene mesas disponibles.");
        }
    });
}

