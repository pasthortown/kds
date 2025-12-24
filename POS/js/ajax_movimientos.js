var estadoCCL = 0;
var INGRESO_NUMERO_AUTORIZACION=0;
var ID_TXT_ESCRIBE_CALCULADORA='totalMovimiento';
var TIPO_TECLADO='FLOAT';
var MENSAJE_ALERTA='';


$(document).ready(function () {

    $("#modal_ingresoAutorizacion").hide();    
    cargando(1);

    var localizacion = "";

    $("#totalMovimiento").click(function () {
        $("#cntModalTotal").dialog("open");
    });

    var configuraciones = function () {
        calendar();
        cargarTiposMovimientos("-");
        $('#tipoEgreso').click(function () {
            $("#totalMovimiento").removeAttr("disabled");
            $('#tipoEgreso').prop('checked', true);
            $('#tipoIngreso').prop('checked', false);
            if ($('#tipoEgreso').is(':checked')) {
                cargarTiposMovimientos("-");
                //$("#inicioFecha").attr("disabled", true);
                $("#finalFecha").attr("disabled", true);
                $("#totalMovimiento").val("");
                $("#msnInformacion").html("");
                $("#btnConfirmar").attr("disabled", "disabled");
            }
        });
        $('#tipoIngreso').click(function () {
            $("#totalMovimiento").removeAttr("disabled");
            $('#tipoIngreso').prop('checked', true);
            $('#tipoEgreso').prop('checked', false);
            if ($('#tipoIngreso').is(':checked')) {
                cargarTiposMovimientos("+");
                //$("#inicioFecha").attr("disabled", true);
                $("#finalFecha").attr("disabled", true);
                $("#totalMovimiento").val("");
                $("#msnInformacion").html("");
                $("#btnConfirmar").attr("disabled", "disabled");
            }
        });
        $('#tipoMovimiento').change(function () {
            var opcion = $("#tipoMovimiento option:selected").html();
            if (opcion === "Caja Chica Local") {
                //$("#inicioFecha").attr("disabled", false);
                $("#finalFecha").attr("disabled", false);
                $("#totalMovimiento").val("");
                $("#totalMovimiento").attr("disabled", "disabled");
                $("#msnInformacion").html("");
            } else {
                //$("#inicioFecha").attr("disabled", true);
                $("#finalFecha").attr("disabled", true);
                $("#totalMovimiento").val("");
                $("#totalMovimiento").removeAttr("disabled");
                $("#msnInformacion").html("");
                $("#btnConfirmar").attr("disabled", "disabled");
                $("#btnConfirmar").removeClass("boton");
                $("#btnConfirmar").addClass("botonBloqueado");
            }
        });
        $('#btnEnviar').click(function () { 
            
            INGRESO_NUMERO_AUTORIZACION=$("#tipoMovimiento option:selected").attr('ingresonumero');            
            textoCombo=$( "#tipoMovimiento option:selected" ).text();             
            if(INGRESO_NUMERO_AUTORIZACION==1)
            {                   
                if($('#txt_numCheque').val().trim() === '')
                {
                    alertify.error('Ingrese el numero de '+textoCombo);
                    return false;
                }
               
            }
           // else
            //{
              //  if()
           // }
            
            cargando(1);
            var opcion = $("#tipoMovimiento option:selected").html();
            if (opcion === "Caja Chica Local") {
                verificarCajaChicaLocalCiudad();
            } else {
                if ($("#totalMovimiento").val().length > 0 && $("#totalMovimiento").val() > 0) 
                {
                    mensajeAlerta='';
                    if(INGRESO_NUMERO_AUTORIZACION==2)
                    {
                        mensajeAlerta=MENSAJE_ALERTA;
                    }
                    else
                    {
                        mensajeAlerta='Está seguro de registrar el total de ' + $("#simboloMoneda").val() + $('#totalMovimiento').val() + ' por el concepto de '+textoCombo+' ?';
                    }
                    alertify.confirm(mensajeAlerta, function (e) {
                        if (e) {
                            guardarMovimiento();
                        } else 
                        {
                            cargando(0);
                            $("#totalMovimiento").val("");
                            $("#txt_numCheque").val("");
                        }
                    });
                } else {
                    cargando(0);
                    alertify.alert("Debe ingresar un Valor válido.");
                }
            }
        });
        $('#btnCancelar').click(function () {
            irFuncionesGerente();
        });
        $('#btnConfirmar').click(function () {
            alertify.confirm('Está seguro de registrar este total ' + $("#simboloMoneda").val() + $('#totalMovimiento').val() + '?', function (e) {
                if (e) {
                    guardarMovimientoCCC();
                    $("#btnConfirmar").attr("disabled", "disabled");
                    $("#btnConfirmar").removeClass("boton");
                    $("#btnConfirmar").addClass("botonBloqueado");
                } else {
                    $("#totalMovimiento").val("");
                }
            });
        });
        cargando(0);
    };
    var calendar = function () {
        /*
         $("#inicioFecha").datepicker({
         dateFormat: "yy-mm-dd",
         showOtherMonths: true,
         firstDay: 1});
         */
        $("#finalFecha").datepicker({
            dateFormat: "yy-mm-dd",
            showOtherMonths: true,
            firstDay: 1,
            maxDate: new Date
        });
        //$('#inicioFecha').datepicker('setDate', 'today');
        $('#finalFecha').datepicker('setDate', 'today');
    };

    //valida el cambio de fecha y setea los valores
    $("#finalFecha").change(function () {
        $("#totalMovimiento").val("");
        $("#msnInformacion").html("");
        $("#btnConfirmar1").attr("disabled", "disabled");
        $("#btnConfirmar1").removeClass("boton");
        $("#btnConfirmar1").addClass("botonBloqueado");
    });

   
    function cargarTiposMovimientos(tipo) {
        cargando(1);
        var html = "";
        var send = {"cargarTiposMovimiento": 1};
        send.accion = 0;
        send.tipo = tipo;
        $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../movimientos/config_movimientos.php", data: send, dataType: "json",
            success: function (datos) {
                if (datos.str) {
                    localizacion = datos[0]['localizacion'];   
                    MENSAJE_ALERTA = datos[0]['mensaje'];   
                    if(datos[0]['ingresoCodigo']==1)
                    {
                        $("#div_numCheque").show();
                    }
                    else
                    {
                        $("#div_numCheque").hide();
                    }
                    for (var i = 0; i < datos.str; i++) {
                        html = html + "<option value='" + datos[i]['idMotivo'] + "' ingresoNumero='"+datos[i]['ingresoCodigo']+"' mensaje='"+datos[i]['mensaje']+"'>" + datos[i]['descripcion'] + "</option>";
                    }
                    $("#tipoMovimiento").html(html);
                    
                    $("#tipoMovimiento").change(
                                                function() 
                                                    {
                                                         opcionMuestraOculta=$("#tipoMovimiento option:selected").attr('ingresoNumero');
                                                         MENSAJE_ALERTA = $("#tipoMovimiento option:selected").attr('mensaje');                                                        
                                                         fn_muestraOculta_campoNumero(opcionMuestraOculta);
                                                        
                                                    }
                                                );
                }
                cargando(0);
            }
        });
    }
    var verificarCajaChicaLocalCiudad = function () {
        //var inicio = quitarGionFecha($("#inicioFecha").val());
        var fin = quitarGionFecha($("#finalFecha").val());
        var send = {"verificarCajaChicaLocalGerente": 1};
        send.localizacion = localizacion;
        //send.fechaInicio = inicio;
        send.fechaFin = fin;
        $.ajax({type: "POST", async: false, url: "config_movimientos.php", data: send, dataType: "json",
            success: function (datos) {
                if (datos.hasOwnProperty("estado") && datos.hasOwnProperty("mensaje")) {
                    $("#totalMovimiento").val(datos.total);
                    $("#msnInformacion").html(datos.mensaje);
                    if (datos.estado === 1) {
                        $("#btnConfirmar").removeAttr("disabled");
                        $("#btnConfirmar").removeClass("botonBloqueado");
                        $("#btnConfirmar").addClass("boton");
                    } else {
                        $("#btnConfirmar").attr("disabled", "disabled");
                        $("#btnConfirmar").removeClass("boton");
                        $("#btnConfirmar").addClass("botonBloqueado");
                        alertify.alert("Mensaje: " + datos.mensaje);
                    }
                } else {
                    $("#totalMovimiento").val("");
                    $("#msnInformacion").html("Servicio no Disponible: " + datos.mensaje);
                }
                cargando(0);
            }, error: function (xhr, ajaxOptions, thrownError) {
                cargando(0);
                alertify.alert("Error, " + xhr.mensaje);
            }
        });
    };
    var guardarMovimiento = function () {
        cargando(1);
        var signo = "";
        if ($('#tipoIngreso').is(':checked')) {
            signo = "+";
        } else {
            signo = "-";
        }
        var send = {"guardarMovimientoIngresoEgreso": 1};
        send.signo = signo;
        send.idMotivo = $("#tipoMovimiento").val();
        send.valor = $("#totalMovimiento").val();
        send.hasta = quitarGionFecha($("#finalFecha").val());
        send.numeroAutorizacion=$("#txt_numCheque").val();
        $.ajax({type: "POST", async: false, url: "config_movimientos.php", data: send, dataType: "json",
            success: function (datos) {
                if (datos.hasOwnProperty("estado") && datos.hasOwnProperty("mensaje")) { 
                     cargando(0);
                     alertify.alert(datos.mensaje);
                     $("#alertify-ok").click(function (event) {
                                                                            event.stopPropagation();
                                                                            irFuncionesGerente();
                                                                         });                    
                }
               
            }, error: function (xhr, ajaxOptions, thrownError) {
                cargando(0);
                alertify.alert("Error, " + xhr.mensaje);
            }
        });
    };
    var guardarMovimientoCCC = function () {

        cargando(1);
        //var inicio = quitarGionFecha($("#inicioFecha").val());
        var fin = quitarGionFecha($("#finalFecha").val());
        var signo = "";
        if ($('#tipoIngreso').is(':checked')) {
            signo = "+";
        } else {
            signo = "-";
        }
        var send = {"guardarMovimientoIngresoEgresoCCC": 1};
        send.signo = signo;
        send.idMotivo = $("#tipoMovimiento").val();
        send.valor = $("#totalMovimiento").val();
        send.hasta = fin;
        send.localizacion = localizacion;
        $.ajax({type: "POST", async: false, url: "config_movimientos.php", data: send, dataType: "json",
            success: function (datos) {
                if (datos.hasOwnProperty("estado") && datos.hasOwnProperty("mensaje")) {
                    if (datos.estado > 0) {
                        cargando(0);
                        alertify.confirm(datos.mensaje + ' ¿Desea ingresar otro Movimiento?', function (e) {
                            if (!e) {
                                irFuncionesGerente();
                            } else {
                                $("#totalMovimiento").val("");
                                $("#msnInformacion").html("");
                                $("#btnConfirmar").attr("disabled", "disabled");
                                $("#btnConfirmar").removeClass("boton");
                                $("#btnConfirmar").addClass("botonBloqueado");
                            }
                        });
                    } else {
                        cargando(0);
                        alertify.alert(datos.mensaje);
                    }
                } else {
                    cargando(0);
                    alertify.alert("Servicio no Disponible, intentelo nuevamente.");
                }
            }, error: function (xhr, ajaxOptions, thrownError) {
                cargando(0);
                alertify.alert("Error, " + xhr.mensaje);
            }
        });
    };
    var irFuncionesGerente = function () {
        window.location.href = "../funciones/funciones_gerente.php";
    };
    var quitarGionFecha = function (fecha) {
        while (fecha.indexOf("-") >= 0) {
            fecha = fecha.replace("-", "");
        }
        return fecha;
    };
    configuraciones();
});

var fin = "";
function agregarComa() {
    var total = $("#"+ID_TXT_ESCRIBE_CALCULADORA+"").val();
    if (total.length > 0) {
        if (total.indexOf('.') < 0) {
            total += ".";
            $("#"+ID_TXT_ESCRIBE_CALCULADORA+"").val(total);
        }
    } else {
        total = "0.";
        $("#"+ID_TXT_ESCRIBE_CALCULADORA+"").val(total);
    }
}

function agregarCero() { 
    var total = $("#"+ID_TXT_ESCRIBE_CALCULADORA+"").val();
    if (total.length === 0 && TIPO_TECLADO=='FLOAT') {
        total = "0.";
    } else if ((total.indexOf('.') > 0 && total.length < total.indexOf('.') + 3) || total.indexOf('.') < 0) {
        total += "0";
    }
    $("#"+ID_TXT_ESCRIBE_CALCULADORA+"").val(total);
}

function fn_muestraOculta_campoNumero(bandera)
{
    if(bandera==1)
    {
        $("#div_numCheque").show();        
    }
    else
    {
        $("#div_numCheque").hide();        
    }
}

function agregarCantidad(numero) { 
    var total = $("#"+ID_TXT_ESCRIBE_CALCULADORA+"").val();
    if ((total.indexOf('.') > 0 && total.length < total.indexOf('.') + 3) || total.indexOf('.') < 0) {
        total += "" + numero;
        $("#"+ID_TXT_ESCRIBE_CALCULADORA+"").val(total);
    }
}

function fn_seteaIdTxt(id,tipoTeclado)
{
    ID_TXT_ESCRIBE_CALCULADORA=id;
    TIPO_TECLADO=tipoTeclado;
    if(tipoTeclado=='INT')
    {
        $("#btn_comaCalculadora").hide();
    }
    else
    {
        $("#btn_comaCalculadora").show();
    }
}

function quitarCantidad() {
    var total = $("#"+ID_TXT_ESCRIBE_CALCULADORA+"").val();
    if (total === "0.") {
        total = "";
    } else {
        total = total.substring(0, total.length - 1);
    }
    $("#"+ID_TXT_ESCRIBE_CALCULADORA+"").val(total);
}

function cargando(std) {
    if (std > 0) {
        $("#mdl_rdn_pdd_crgnd").show();
    } else {
        $("#mdl_rdn_pdd_crgnd").hide();
    }
}
function fn_modal_cajaChicaLocal() {

    //calendar();
    //configuracion();
    cargando(1);
    var html = "";
    var send = {"cargarTiposMovimiento": 1};
    send.accion = 1;
    send.tipo = "-";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../movimientos/config_movimientos.php", data: send, dataType: "json", cache: false,
        success: function (datos) {
            if (datos.str) {
                localizacion = datos[0]['localizacion'];
                for (var i = 0; i < datos.str; i++) {
                    html = html + "<option value='" + datos[i]['idMotivo'] + "'>" + datos[i]['descripcion'] + "</option>";
                }
                $("#tipoMovimiento").html(html);
                $("#ModalCajaChicaLocal").modal('show');
            }
            cargando(0);
        }
    });

    var opcion = $("#tipoMovimiento option:selected").html();
    if (opcion === "Caja Chica Local") {
        //$("#inicioFecha").attr("disabled", false);
        $("#finalFecha").attr("disabled", false);
        $("#totalMovimiento").val("");
        $("#totalMovimiento").attr("disabled", "disabled");
        $("#msnInformacion").html("");
    }
//ReferenceError: quitarGionFecha is not defined
}
function verificarCajaChicaLocalCiudadDesc() {
    //var inicio = quitarGionFecha($("#inicioFecha").val());
    fin = $("#finalFecha").val();
    while (fin.indexOf("-") >= 0) {
        fin = fin.replace("-", "");
    }
    //var fin = "20170202";
    var send = {"verificarCajaChicaLocalGerente": 1};
    send.localizacion = localizacion;
    //send.fechaInicio = inicio;
    send.fechaFin = fin;
    $.ajax({type: "POST", async: false, url: "../movimientos/config_movimientos.php", data: send, dataType: "json", cache: false,
        success: function (datos) {
            if (datos.hasOwnProperty("estado") && datos.hasOwnProperty("mensaje")) {
                $("#totalMovimiento").val(datos.total.toFixed(2));
                $("#msnInformacion").html(datos.mensaje);
                if (datos.estado === 1) {
                    $("#btnConfirmar1").removeAttr("disabled");
                    $("#btnConfirmar1").removeClass("botonBloqueado");
                    $("#btnConfirmar1").addClass("boton");
                } else {
                    $("#btnConfirmar1").attr("disabled", "disabled");
                    $("#btnConfirmar1").removeClass("boton");
                    $("#btnConfirmar1").addClass("botonBloqueado");
                    alertify.alert("Mensaje: " + datos.mensaje);
                }
            } else {
                $("#totalMovimiento").val("");
                $("#msnInformacion").html("Servicio no Disponible: " + datos.mensaje);
            }
            cargando(0);
        }, error: function (xhr, ajaxOptions, thrownError) {
            cargando(0);
            alertify.alert("Error, " + xhr.mensaje);
        }
    });
}
;

$('#btnConfirmar1').click(function () {
    alertify.confirm('Está seguro de registrar este total $' + $('#totalMovimiento').val() + '?', function (e) {
        if (e) {
            
            guardarMovimientoCCCDesc(fin);
            $("#btnConfirmar1").attr("disabled", "disabled");
            $("#btnConfirmar1").removeClass("boton");
            $("#btnConfirmar1").addClass("botonBloqueado");
            $("#ModalCajaChicaLocal").modal("hide");
            //fn_consultaFormaPago();
            //$("#modal_formaPagoEfectivo").modal("hide");
            fn_formaPagoEfectivo(1);
        } else {
            $("#btnConfirmar1").addClass("botonBloqueado");
            $("#totalMovimiento").val("");
        }
    });
});
$('#btnEnviar1').click(function () {
    cargando(1); 
    var opcion = $("#tipoMovimiento option:selected").html();
    try {
        if (opcion === "Caja Chica Local") {
            verificarCajaChicaLocalCiudadDesc();
        }
    } catch (e) {
        alert(e);
    }

});

function guardarMovimientoCCCDesc(fin) {
    cargando(1);
    //var inicio = quitarGionFecha($("#inicioFecha").val());
    //var fin = quitarGionFecha($("#finalFecha").val());
    var fin = fin;
    var signo = "-";
    var send = {"guardarMovimientoIngresoEgresoCCC": 1};
    send.signo = signo;
    send.idMotivo = $("#tipoMovimiento").val();
    send.valor = $("#totalMovimiento").val();
    send.hasta = fin;
    send.localizacion = localizacion;
    $.ajax({type: "POST", async: false, url: "../movimientos/config_movimientos.php", data: send, dataType: "json",
        success: function (datos) {
//            $("#botonCCL").html('<button type="button" id="btn_cajachicalocal2" class="btn btn-info" onclick = "fn_reversarCCL();" style="height:70px;width:180px;">Regresar Valores <br />Caja Chica Local</button>');
//            estadoCCL = 1;
            if (datos.hasOwnProperty("estado") && datos.hasOwnProperty("mensaje")) {
                if (datos.estado > 0) {

                // Aplicar apertura cajon
                var apiImpresion = getConfiguracionesApiImpresion();
                
                if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1 && apiImpresion.api_impresion_apertura_cajon_caja_chica == 1) {

                    send = { "servicioApiAperturaCajon": 1 };
                    send.idFormaPago = '';

                    $.getJSON("../facturacion/config_servicioApiAperturaCajon.php", send, function (datos) {
                            console.log(datos) 
                        });

                    }

                    cargando(0);
                    $("#totalMovimiento").val("");
                    $("#msnInformacion").html("");
                    $("#btnConfirmar1").attr("disabled", "disabled");
                    $("#btnConfirmar1").removeClass("boton");
                    $("#btnConfirmar1").addClass("botonBloqueado");
                } else {
                    cargando(0);
                    alertify.alert(datos.mensaje);
                }
            } else {
                cargando(0);
                alertify.alert("No existe conexión en estos momentos al sistemas gerente, por favor reintente más tarde");
            }
        }, error: function (xhr, ajaxOptions, thrownError) {
            cargando(0);
            alertify.alert("Error, " + xhr.mensaje);
        }
    });
}
;

function fn_cerrarCalculadora()
{
    $("#txt_ingresoValor").text('Total');
}